<?php

namespace Source\App\Reembolsos;

@session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Reembolsos\Reembolso;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Vendas\Venda;
use Source\Helpers\Emails;
use Source\Helpers\Paginador;
use Source\App\Checkout1\PagarMe;

class AppReembolsos
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    /**
     * Lista de solicitações para produtores/afiliados/adm
     */
    public function index($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $userId = $_SESSION["id"];
        $tipo   = $_SESSION["tipo"]; // 1 master, 2 admin, 3 func, 4 produtor, 5 afiliado, 6 comprador

        // Filtros
        $filtros = [];
        $isAdmin = ($tipo == 1 || $tipo == 2);
        $q = $_GET;

        if ($isAdmin) {
            if (!empty($q["produtor"]))  $filtros[] = "produtor_id = " . (int)$q["produtor"];
            if (!empty($q["afiliado"]))  $filtros[] = "afiliado_id = " . (int)$q["afiliado"];
            if (!empty($q["comprador"])) $filtros[] = "comprador_id = " . (int)$q["comprador"];
            if (!empty($q["produto"]))   $filtros[] = "produto_id = " . (int)$q["produto"];
            if (isset($q["status"]) && $q["status"] !== "") $filtros[] = "status = " . (int)$q["status"];
        } else {
            if ($tipo == 4) {
                $filtros[] = "produtor_id = {$userId}";
            } elseif ($tipo == 5) {
                $filtros[] = "afiliado_id = {$userId}";
            } elseif ($tipo == 6) {
                $filtros[] = "comprador_id = {$userId}";
            } elseif ($tipo == 3) {
                $filtros[] = "(produtor_id = {$userId} OR afiliado_id = {$userId})";
            }
        }

        $where = implode(" AND ", $filtros);

        $offset = 20;
        $pagina = isset($q["page"]) && (int)$q["page"] > 0 ? (int)$q["page"] : 1;
        $paginador = new Paginador();
        $calcOffset = $paginador->paginador($pagina, $offset);

        $repo = new Reembolso();
        $total = $repo->find($where ?: null)->count();
        $paginas = $total ? ceil($total / $offset) : 1;

        $reembolsos = $repo->find($where ?: null)
            ->order("id DESC")
            ->limit($offset)
            ->offset($calcOffset)
            ->fetch(true);

        // Enriquecimento: nomes e cpf comprador
        if ($reembolsos) {
            foreach ($reembolsos as $r) {
                $r->produtor_nome  = $this->buscaNomeUsuario($r->produtor_id);
                $r->afiliado_nome  = $this->buscaNomeUsuario($r->afiliado_id);
                $r->comprador_nome = $this->buscaNomeUsuario($r->comprador_id);
                $r->comprador_cpf  = $this->buscaCpfUsuarioDados($r->comprador_id);
            }
        }

        echo $this->view->render("reembolsos/reembolsos", [
            "title" => "Reembolsos",
            "reembolsos" => $reembolsos,
            "paginas" => $paginas,
            "pagina" => $pagina,
            "paginador" => $paginador,
            "query" => $q,
            "isAdmin" => $isAdmin
        ]);
    }

    /**
     * Retorna produtos elegíveis a reembolso pelo CPF informado.
     * Query param: cpf
     * Apenas vendas pagas (status 4 ou 8).
     */
    public function produtos($data)
    {
        header("Content-Type: application/json");

        $cpf = preg_replace('/\\D/', '', @$data["cpf"] ?? @$_GET["cpf"]);
        if (!$cpf) {
            http_response_code(400);
            echo json_encode(["erro" => "CPF obrigatório"]);
            return;
        }

        $comprador = (new UsuarioDados())->find("REPLACE(REPLACE(REPLACE(REPLACE(cpf_cnpj,'.',''),'-',''),'/',''),' ','') = '{$cpf}'")->fetch();

        if (!$comprador) {
            http_response_code(404);
            echo json_encode(["erro" => "Comprador não encontrado"]);
            return;
        }

        // Status pagos: 4 (pago) ou 8 (pago manual/ajuste)
        $vendas = (new Venda())->find("comprador = {$comprador->id} AND (status_transacao = 4 OR status_transacao = 8)")->order("created_at DESC")->fetch(true);

        if (!$vendas) {
            http_response_code(404);
            echo json_encode(["erro" => "Nenhuma compra encontrada"]);
            return;
        }

        $resp = [];
        foreach ($vendas as $v) {
            $produto = $v->dataProduto();
            $valorProduto = $v->valor_produto ?: $v->valor_bruto ?: $v->valor_liquido;
            $resp[] = [
                "id"          => (int) $v->id,
                "name"        => $produto ? $produto->nome : "Produto #{$v->produto}",
                "price"       => (float) $valorProduto,
                "date"        => date("d/m/Y", strtotime($v->created_at)),
                "img"         => $produto && $produto->foto ? url("uploads/produtos/{$produto->foto}") : "https://via.placeholder.com/96x96.png?text=WP",
                "venda_id"    => (int) $v->id,
                "produto_id"  => (int) $v->produto,
                "produtor_id" => (int) $v->produtor,
                "afiliado_id" => $v->afiliado ? (int) $v->afiliado : null
            ];
        }

        echo json_encode(["produtos" => $resp]);
    }

    /**
     * Endpoint para salvar solicitação (JSON ou form-data)
     * Espera: cpf, comprador_id (opcional), produtos[{venda_id, produto_id, produtor_id, afiliado_id, valor}], motivo
     */
    public function solicitar($data)
    {
        header("Content-Type: application/json");

        $payload = file_get_contents("php://input");
        $json = json_decode($payload, true);
        $data = array_merge($data, is_array($json) ? $json : []);

        $produtos = @$data["produtos"];
        $motivo   = @$data["motivo"];
        $cpf      = @$data["cpf"];

        if (!$produtos || !is_array($produtos)) {
            http_response_code(400);
            echo json_encode(["erro" => "Nenhum produto selecionado."]);
            return;
        }

        // Resolve comprador por CPF se informado
        $compradorId = null;
        if ($cpf) {
            $cpfSan = preg_replace('/\\D/', '', $cpf);
            $dadosComprador = (new UsuarioDados())->find("REPLACE(REPLACE(REPLACE(REPLACE(cpf_cnpj,'.',''),'-',''),'/',''),' ','') = '{$cpfSan}'")->fetch();
            if ($dadosComprador) {
                $compradorId = $dadosComprador->usuario;
            }
        }
        if (@$data["comprador_id"]) {
            $compradorId = $data["comprador_id"];
        }

        $valorTotal = 0;
        $produtorId = null;
        $afiliadoId = null;
        $vendaId = null;
        $produtoId = null;

        foreach ($produtos as $p) {
            $valorTotal += (float) @$p["valor"];
            $produtorId = $produtorId ?: @$p["produtor_id"];
            $afiliadoId = $afiliadoId ?: @$p["afiliado_id"];
            $vendaId    = $vendaId ?: @$p["venda_id"];
            $produtoId  = $produtoId ?: @$p["produto_id"];
        }

        $reembolso = new Reembolso();
        $reembolso->venda_id    = $vendaId;
        $reembolso->produto_id  = $produtoId;
        $reembolso->comprador_id= $compradorId;
        $reembolso->produtor_id = $produtorId;
        $reembolso->afiliado_id = $afiliadoId;
        $reembolso->valor_total = $valorTotal;
        $reembolso->motivo      = $motivo;
        $reembolso->itens       = json_encode($produtos, JSON_UNESCAPED_UNICODE);
        $reembolso->status      = 0; // pendente

        if ($reembolso->save()) {
            $this->notificar($reembolso);
            echo json_encode(["ok" => true, "id" => $reembolso->id]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => $reembolso->fail()->getMessage()]);
        }
    }

    /**
     * Notifica produtor / afiliado / suporte
     */
    private function notificar(Reembolso $reembolso)
    {
        $email = new Emails();

        $produtor = $reembolso->produtor_id ? (new Usuario())->findById($reembolso->produtor_id) : null;
        $afiliado = $reembolso->afiliado_id ? (new Usuario())->findById($reembolso->afiliado_id) : null;
        $comprador= $reembolso->comprador_id ? (new Usuario())->findById($reembolso->comprador_id) : null;

        $msg = "<h3>Nova solicitação de reembolso</h3>";
        $msg .= "<p>ID: {$reembolso->id}</p>";
        $msg .= "<p>Valor total: R$ " . number_format($reembolso->valor_total, 2, ",", ".") . "</p>";
        $msg .= "<p>Motivo: " . nl2br(htmlspecialchars($reembolso->motivo ?? '')) . "</p>";

        if ($produtor && $produtor->email) {
            $data["nome"]     = $produtor->nome;
            $data["email"]    = $produtor->email;
            $data["assunto"]  = "Nova solicitação de reembolso";
            $data["mensagem"] = $msg;
            $email->enviaSendGrid($data);
        }

        if ($afiliado && $afiliado->email) {
            $data["nome"]     = $afiliado->nome;
            $data["email"]    = $afiliado->email;
            $data["assunto"]  = "Reembolso em venda com sua afiliação";
            $data["mensagem"] = $msg;
            $email->enviaSendGrid($data);
        }
    }

    private function buscaNomeUsuario($id)
    {
        if (!$id) return null;
        $u = (new Usuario())->findById($id);
        return $u ? $u->nome : null;
    }

    private function buscaCpfUsuarioDados($usuarioId)
    {
        if (!$usuarioId) return null;
        $d = (new UsuarioDados())->find("usuario = {$usuarioId}")->fetch();
        return $d ? $d->cpf_cnpj : null;
    }

    /**
     * Atualiza status da solicitação
     * POST: id, status (0..4)
     * Permissões:
     * - Master/Admin (1,2): qualquer solicitação
     * - Produtor/Afiliado: apenas se a solicitação for deles
     */
    public function status($data)
    {
        header("Content-Type: application/json");
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $id = @$data["id"];
        $novoStatus = @$data["status"];

        if (!$id || $novoStatus === null) {
            http_response_code(400);
            echo json_encode(["erro" => "Parâmetros obrigatórios: id e status"]);
            return;
        }

        if (!in_array((int)$novoStatus, [0,1,2,3,4])) {
            http_response_code(400);
            echo json_encode(["erro" => "Status inválido"]);
            return;
        }

        $reembolso = (new Reembolso())->findById($id);
        if (!$reembolso) {
            http_response_code(404);
            echo json_encode(["erro" => "Solicitação não encontrada"]);
            return;
        }

        $userId = $_SESSION["id"];
        $tipo   = $_SESSION["tipo"];

        $autorizado = false;
        if ($tipo == 1 || $tipo == 2) { // master/admin
            $autorizado = true;
        } elseif ($tipo == 4 && $reembolso->produtor_id == $userId) {
            $autorizado = true;
        } elseif ($tipo == 5 && $reembolso->afiliado_id == $userId) {
            $autorizado = true;
        }

        if (!$autorizado) {
            http_response_code(403);
            echo json_encode(["erro" => "Sem permissão para alterar esta solicitação"]);
            return;
        }

        $reembolso->status = (int)$novoStatus;
        if ($reembolso->save()) {
            echo json_encode(["ok" => true, "status" => $reembolso->status]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => $reembolso->fail()->getMessage()]);
        }
    }

    /**
     * Retorna HTML parcial com detalhes para o modal
     */
    public function detalhe($data)
    {
        header("Content-Type: text/html; charset=utf-8");
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $id = @$data["id"] ?? @$_GET["id"];
        if (!$id) {
            echo "ID não informado";
            return;
        }

        $reembolso = (new Reembolso())->findById($id);
        if (!$reembolso) {
            echo "Solicitação não encontrada";
            return;
        }

        // Permissão básica: master/admin sempre; outros só se envolvidos
        $userId = $_SESSION["id"];
        $tipo = $_SESSION["tipo"];
        $autorizado = ($tipo == 1 || $tipo == 2)
            || ($tipo == 4 && $reembolso->produtor_id == $userId)
            || ($tipo == 5 && $reembolso->afiliado_id == $userId)
            || ($tipo == 6 && $reembolso->comprador_id == $userId);
        if (!$autorizado) {
            echo "Sem permissão";
            return;
        }

        $itens = json_decode($reembolso->itens ?? "[]");

        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>";
        $produtorNome  = $reembolso->produtor_id ? (@$reembolso->produtor_nome ?: $this->buscaNomeUsuario($reembolso->produtor_id)) : "-";
        $afiliadoNome  = $reembolso->afiliado_id ? (@$reembolso->afiliado_nome ?: $this->buscaNomeUsuario($reembolso->afiliado_id)) : "-";
        $compradorNome = $reembolso->comprador_id ? (@$reembolso->comprador_nome ?: $this->buscaNomeUsuario($reembolso->comprador_id)) : "-";
        $compradorCpf  = $this->buscaCpfUsuarioDados($reembolso->comprador_id);

        echo "<tr><th>ID</th><td>{$reembolso->id}</td></tr>";
        echo "<tr><th>Status</th><td>{$reembolso->status}</td></tr>";
        echo "<tr><th>Valor Total</th><td>R$ " . number_format($reembolso->valor_total, 2, ',', '.') . "</td></tr>";
        echo "<tr><th>Produtor</th><td>{$produtorNome}</td></tr>";
        echo "<tr><th>Afiliado</th><td>{$afiliadoNome}</td></tr>";
        echo "<tr><th>Comprador</th><td>{$compradorNome} " . ($compradorCpf ? "<br><small>CPF: {$compradorCpf}</small>" : "") . "</td></tr>";
        echo "<tr><th>Motivo</th><td>" . nl2br(htmlspecialchars($reembolso->motivo ?? '')) . "</td></tr>";
        echo "<tr><th>Criado em</th><td>" . $reembolso->criado_em . "</td></tr>";
        echo "</table>";

        echo "<h5>Itens</h5>";
        if ($itens && is_array($itens)) {
            echo "<table class='table table-bordered'><thead><tr><th>Produto</th><th>Valor</th><th>Venda</th><th>Produtor</th><th>Afiliado</th></tr></thead><tbody>";
            foreach ($itens as $i) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars(@$i->nome ?: @$i->name) . "</td>";
                echo "<td>R$ " . number_format((float) @$i->valor ?: @$i->price, 2, ',', '.') . "</td>";
                echo "<td>" . htmlspecialchars(@$i->venda_id) . "</td>";
                echo "<td>" . htmlspecialchars(@$i->produtor_id) . "</td>";
                echo "<td>" . htmlspecialchars(@$i->afiliado_id) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Nenhum item registrado.</p>";
        }

        echo "</div>";
    }

    public function export($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $userId = $_SESSION["id"];
        $tipo   = $_SESSION["tipo"];
        $isAdmin = ($tipo == 1 || $tipo == 2);

        $filtros = [];
        $q = $_GET;
        if ($isAdmin) {
            if (!empty($q["produtor"]))  $filtros[] = "produtor_id = " . (int)$q["produtor"];
            if (!empty($q["afiliado"]))  $filtros[] = "afiliado_id = " . (int)$q["afiliado"];
            if (!empty($q["comprador"])) $filtros[] = "comprador_id = " . (int)$q["comprador"];
            if (!empty($q["produto"]))   $filtros[] = "produto_id = " . (int)$q["produto"];
            if (isset($q["status"]) && $q["status"] !== "") $filtros[] = "status = " . (int)$q["status"];
        } else {
            if ($tipo == 4) $filtros[] = "produtor_id = {$userId}";
            elseif ($tipo == 5) $filtros[] = "afiliado_id = {$userId}";
            elseif ($tipo == 6) $filtros[] = "comprador_id = {$userId}";
            elseif ($tipo == 3) $filtros[] = "(produtor_id = {$userId} OR afiliado_id = {$userId})";
        }
        $where = implode(" AND ", $filtros);

        $rows = (new Reembolso())->find($where ?: null)->order("id DESC")->fetch(true);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=reembolsos.xls");

        echo "ID\tStatus\tValor\tProdutor\tAfiliado\tComprador\tCPF Comprador\tCriado em\tMotivo\n";
        if ($rows) {
            foreach ($rows as $r) {
                $produtor = $this->buscaNomeUsuario($r->produtor_id);
                $afiliado = $this->buscaNomeUsuario($r->afiliado_id);
                $comprador = $this->buscaNomeUsuario($r->comprador_id);
                $cpf = $this->buscaCpfUsuarioDados($r->comprador_id);
                $statusTxt = ["Pendente","Aprovado","Recusado","Processando","Concluído"][$r->status] ?? $r->status;
                $motivo = str_replace(["\n","\r","\t"], " ", $r->motivo);
                echo "{$r->id}\t{$statusTxt}\t" . number_format($r->valor_total,2,',','.') . "\t{$produtor}\t{$afiliado}\t{$comprador}\t{$cpf}\t{$r->criado_em}\t{$motivo}\n";
            }
        }
        exit;
    }

    /**
     * Dispara estorno via Pagar.me (charges/{id} DELETE)
     * POST: id (reembolso)
     */
    public function estornar($data)
    {
        header("Content-Type: application/json");
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $id = @$data["id"];
        if (!$id) {
            http_response_code(400);
            echo json_encode(["erro" => "ID obrigatório"]);
            return;
        }

        $reembolso = (new Reembolso())->findById($id);
        if (!$reembolso) {
            http_response_code(404);
            echo json_encode(["erro" => "Solicitação não encontrada"]);
            return;
        }

        $userId = $_SESSION["id"];
        $tipo   = $_SESSION["tipo"];

        $autorizado = ($tipo == 1 || $tipo == 2)
            || ($tipo == 4 && $reembolso->produtor_id == $userId)
            || ($tipo == 5 && $reembolso->afiliado_id == $userId);

        if (!$autorizado) {
            http_response_code(403);
            echo json_encode(["erro" => "Sem permissão para estornar"]);
            return;
        }

        $venda = $reembolso->venda_id ? (new Venda())->findById($reembolso->venda_id) : null;
        if (!$venda || !$venda->transacao) {
            http_response_code(400);
            echo json_encode(["erro" => "Venda ou transação não encontrada para estorno"]);
            return;
        }

        $amount = (int) round($reembolso->valor_total * 100);

        $pagarme = new PagarMe();
        $resp = $pagarme->reembolso($venda->transacao, $amount, null);

        if (@$resp->status == "canceled" || @$resp->status == "failed" || @$resp->id) {
            $reembolso->status = 4; // concluído
            $reembolso->save();
            echo json_encode(["ok" => true, "status" => 4, "resp" => $resp]);
        } else {
            http_response_code(400);
            echo json_encode(["erro" => "Falha ao estornar", "resp" => $resp]);
        }
    }
}

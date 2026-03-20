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
use Source\App\Checkout1\Stripe;
use Source\App\Checkout1\EfiBank;
use Source\App\Checkout1\AppProcessos;
use CoffeeCode\DataLayer\Connect;

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
                $r->comprador_telefone = $this->buscaTelefoneUsuarioDados($r->comprador_id);
                $vendaRef = $r->venda_id ? (new Venda())->findById($r->venda_id) : null;
                $r->transacao = $vendaRef ? $vendaRef->transacao : null;
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

            // Verifica se já há reembolso em aberto (pendente/aprovado/processando) para esta venda
            $refundPendente = (bool) (new Reembolso())
                ->find("venda_id = :vid AND status IN (0,1,3)", "vid={$v->id}")
                ->fetch();

            $resp[] = [
                "id"              => (int) $v->id,
                "name"            => $produto ? $produto->nome : "Produto #{$v->produto}",
                "price"           => (float) $valorProduto,
                "date"            => date("d/m/Y", strtotime($v->created_at)),
                "img"             => $produto && $produto->foto ? url("uploads/produtos/{$produto->foto}") : "https://via.placeholder.com/96x96.png?text=WP",
                "venda_id"        => (int) $v->id,
                "produto_id"      => (int) $v->produto,
                "produtor_id"     => (int) $v->produtor,
                "afiliado_id"     => $v->afiliado ? (int) $v->afiliado : null,
                "refund_pendente" => $refundPendente,
            ];
        }

        echo json_encode(["produtos" => $resp]);
    }

    /**
     * Consulta pública de status por CPF (e opcionalmente id).
     * GET: cpf, id (opcional)
     */
    public function consultaPublica($data)
    {
        header("Content-Type: application/json");

        $cpf = preg_replace('/\\D/', '', @$data["cpf"] ?? @$_GET["cpf"]);

        if (!$cpf) {
            echo json_encode(["erro" => "CPF obrigatório"]);
            return;
        }

        $comprador = (new UsuarioDados())->find("REPLACE(REPLACE(REPLACE(REPLACE(cpf_cnpj,'.',''),'-',''),'/',''),' ','') = '{$cpf}'")->fetch();
        if (!$comprador) {
            echo json_encode(["erro" => "Nenhuma solicitação encontrada para este CPF", "reembolsos" => []]);
            return;
        }

        $userId = (int) $comprador->usuario;

        // Também considera reembolsos vinculados às vendas deste comprador
        $vendas = (new Venda())->find("comprador = {$userId}", null, "id")->fetch(true);
        $idsVendas = $vendas ? array_filter(array_map(fn($v) => (int) $v->id, $vendas)) : [];

        $filtros = [];
        $filtros[] = "comprador_id = {$userId}";
        if ($idsVendas) {
            $in = implode(",", $idsVendas);
            $filtros[] = "venda_id IN ({$in})";
        }

        $where = implode(" OR ", $filtros);

        $lista = (new Reembolso())->find($where)->order("id DESC")->fetch(true);
        if (!$lista) {
            echo json_encode(["erro" => "Nenhuma solicitação encontrada para este CPF", "reembolsos" => []]);
            return;
        }

        $statusMap = [
            0 => "Pendente",
            1 => "Aprovado",
            2 => "Reprovado",
            3 => "Processando",
            4 => "Concluído"
        ];

        $resp = [];
        foreach ($lista as $r) {
            $itens = json_decode($r->itens ?? "[]", true) ?: [];

            $motivoCliente = $r->motivo ?? null;
            $motivoRecusa = null;
            if ($motivoCliente && preg_match("~\\[RECUSA\\](.*)$~s", $motivoCliente, $m)) {
                $motivoRecusa = trim($m[1]);
                $motivoCliente = trim(preg_replace("~\\[RECUSA\\].*$~s", "", $motivoCliente));
            }
            $resp[] = [
                "id"             => (int) $r->id,
                "status"         => (int) $r->status,
                "status_label"   => $statusMap[$r->status] ?? $r->status,
                "valor_total"    => (float) $r->valor_total,
                "motivo"         => $motivoCliente,
                "motivo_recusa"  => $motivoRecusa,
                "contestacao"    => $r->contestacao ?? null,
                "contestado_em"  => $r->contestado_em ?? null,
                "criado_em"      => $r->criado_em,
                "atualizado_em"  => @$r->atualizado_em,
                "venda_id"       => $r->venda_id,
                "itens"          => array_map(function ($i) {
                    return [
                        "nome"       => @$i["nome"] ?? @$i["name"] ?? null,
                        "valor"      => isset($i["valor"]) ? (float) $i["valor"] : (isset($i["price"]) ? (float) $i["price"] : null),
                        "venda_id"   => @$i["venda_id"],
                        "produto_id" => @$i["produto_id"]
                    ];
                }, $itens)
            ];
        }

        echo json_encode([
            "reembolsos" => $resp,
            "cpf" => $cpf
        ]);
    }

    /**
     * Cliente contesta reprovação: volta status para pendente e registra contestação.
     * Espera: id, cpf, contestacao (texto)
     */
    public function contestar($data)
    {
        header("Content-Type: application/json");

        $id = @$data["id"] ?? @$data["reembolso_id"];
        $contestacao = trim(@$data["contestacao"] ?? "");
        $cpf = preg_replace('/\\D/', '', @$data["cpf"] ?? "");

        if (!$id || !$contestacao || !$cpf) {
            http_response_code(400);
            echo json_encode(["erro" => "Parâmetros obrigatórios: id, cpf e contestação"]);
            return;
        }

        $reembolso = (new Reembolso())->findById($id);
        if (!$reembolso) {
            http_response_code(404);
            echo json_encode(["erro" => "Solicitação não encontrada"]);
            return;
        }

        // Confere se CPF pertence ao comprador da solicitação
        $dadosComprador = $reembolso->comprador_id ? (new UsuarioDados())->find("usuario = {$reembolso->comprador_id}")->fetch() : null;
        $cpfComprador = $dadosComprador ? preg_replace('/\\D/', '', $dadosComprador->cpf_cnpj) : null;
        if (!$cpfComprador || $cpfComprador !== $cpf) {
            http_response_code(403);
            echo json_encode(["erro" => "CPF não autorizado para contestar esta solicitação"]);
            return;
        }

        // Só pode contestar se estiver reprovado
        if ((int)$reembolso->status !== 2) {
            http_response_code(400);
            echo json_encode(["erro" => "A contestação só é permitida para solicitações reprovadas"]);
            return;
        }

        $reembolso->contestacao = $contestacao;
        $reembolso->contestado_em = date("Y-m-d H:i:s");
        $reembolso->status = 0; // volta para pendente

        // Grava direto no banco para evitar qualquer bloqueio de DataLayer
        $pdo = Connect::getInstance();
        $stmt = $pdo->prepare("
            UPDATE reembolsos SET
                status = 0,
                contestacao = :contestacao,
                contestado_em = NOW(),
                atualizado_em = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ":contestacao" => $contestacao,
            ":id" => $reembolso->id
        ]);

        $fresh = (new Reembolso())->findById($reembolso->id);
        echo json_encode([
            "ok" => true,
            "status" => 0,
            "status_db" => $fresh ? (int)$fresh->status : null,
            "contestado_em" => $fresh->contestado_em ?? null
        ]);
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
        $vendasEmAberto = [];

        foreach ($produtos as $p) {
            $valorTotal += (float) @$p["valor"];
            $produtorId = $produtorId ?: @$p["produtor_id"];
            $afiliadoId = $afiliadoId ?: @$p["afiliado_id"];
            $vendaId    = $vendaId ?: @$p["venda_id"];
            $produtoId  = $produtoId ?: @$p["produto_id"];

            // Verifica duplicidade por venda: bloqueia se já existir pedido aberto (pendente/aprovado/processando)
            if (!empty($p["venda_id"])) {
                $idVenda = (int) $p["venda_id"];
                $jaExiste = (new Reembolso())
                    ->find("venda_id = :venda AND status IN (0,1,3)", "venda={$idVenda}")
                    ->fetch();
                if ($jaExiste) {
                    $vendasEmAberto[] = $idVenda;
                }
            }
        }

        // Bloqueia se qualquer venda selecionada já tiver reembolso em aberto
        if ($vendasEmAberto) {
            http_response_code(409);
            $lista = implode(", ", $vendasEmAberto);
            echo json_encode([
                "erro" => "Já existe uma solicitação de reembolso em análise para a venda(s): {$lista}. Acompanhe o pedido existente antes de abrir outro."
            ]);
            return;
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
        $reembolso->criado_em   = date("Y-m-d H:i:s"); // usa timezone do PHP (Config.php), não do MySQL

        if ($reembolso->save()) {
            // ---------------------------------------------------------------
            // Reembolso automático: valor <= R$70 e venda criada há até 7 dias
            // ---------------------------------------------------------------
            $autoOk   = false;
            $autoErro = false;

            if ($vendaId && $valorTotal <= 70.00) {
                $vendaRef = (new Venda())->findById($vendaId);
                if ($vendaRef && $vendaRef->created_at) {
                    $diasDesdeVenda = (time() - strtotime($vendaRef->created_at)) / 86400;
                    if ($diasDesdeVenda <= 7) {
                        $transacao      = $vendaRef->transacao;
                        $amountCentavos = (int) round($valorTotal * 100);
                        $gwRef          = @$vendaRef->gateway_usado ?: @$vendaRef->meio_pagamento;
                        $gateway        = $this->detectarGateway($transacao, $gwRef);
                        try {
                            $respGw = $this->executarEstorno($gateway, $transacao, $amountCentavos);
                            if ($this->estornoSucedeu($gateway, $respGw)) {
                                $autoOk = true;
                                $pdo = Connect::getInstance();
                                $pdo->prepare("UPDATE reembolsos SET status = 4, auto_reembolsado = 1, atualizado_em = NOW() WHERE id = :id")
                                    ->execute([":id" => $reembolso->id]);
                                $this->atualizarVendaDevolvida($vendaId);
                                $this->bloquearAcessoMembrosCurso($reembolso);
                                $this->notificarReembolsoConcluido($reembolso, true);
                            } else {
                                throw new \Exception("Gateway retornou falha no estorno automático");
                            }
                        } catch (\Throwable $e) {
                            $autoErro = true;
                            error_log("[Reembolso Auto] id={$reembolso->id} gateway={$gateway}: " . $e->getMessage());
                            Connect::getInstance()
                                ->prepare("UPDATE reembolsos SET auto_reembolso_erro = 1, atualizado_em = NOW() WHERE id = :id")
                                ->execute([":id" => $reembolso->id]);
                        }
                    }
                }
            }
            // ---------------------------------------------------------------

            $this->notificar($reembolso);
            echo json_encode(["ok" => true, "id" => $reembolso->id, "auto_reembolsado" => $autoOk, "auto_erro" => $autoErro]);
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

    private function buscaTelefoneUsuarioDados($usuarioId)
    {
        if (!$usuarioId) return null;
        $d = (new UsuarioDados())->find("usuario = {$usuarioId}")->fetch();
        return $d ? $d->telefone : null;
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
        $motivoRecusa = @$data["motivo_recusa"] ?? @$data["motivo"] ?? null;

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

        // Se for reprovar, motivo é obrigatório
        if ((int)$novoStatus === 2 && !$motivoRecusa) {
            http_response_code(400);
            echo json_encode(["erro" => "Informe o motivo da reprovação"]);
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

        // Normaliza motivo: preserva motivo original do cliente e anexa motivo da reprovação
        if ((int)$novoStatus === 2) {
            $motivoBase = preg_replace("~\\[RECUSA\\].*$~s", "", $reembolso->motivo ?? "");
            $motivoBase = trim($motivoBase);
            $motivoRecusa = trim((string) $motivoRecusa);
            $reembolso->motivo = trim(($motivoBase ? $motivoBase . "\n\n" : "") . "[RECUSA] " . $motivoRecusa);
            // zera contestação
            if (property_exists($reembolso, "contestacao")) {
                $reembolso->contestacao = null;
            }
            if (property_exists($reembolso, "contestado_em")) {
                $reembolso->contestado_em = null;
            }
        }

        $reembolso->status = (int)$novoStatus;
        // Persistência direta para evitar qualquer cache/atributo protegido
        $pdo = Connect::getInstance();
        $stmt = $pdo->prepare("
            UPDATE reembolsos SET
                status = :status,
                motivo = :motivo,
                contestacao = :contestacao,
                contestado_em = :contestado_em,
                atualizado_em = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ":status" => $reembolso->status,
            ":motivo" => $reembolso->motivo,
            ":contestacao" => $reembolso->contestacao ?? null,
            ":contestado_em" => $reembolso->contestado_em ?? null,
            ":id" => $reembolso->id
        ]);

        // Recarrega do banco para garantir que persistiu
        $fresh = (new Reembolso())->findById($id);
        echo json_encode([
            "ok" => true,
            "status" => $reembolso->status,
            "status_db" => $fresh ? (int)$fresh->status : null,
            "atualizado_em" => $fresh->atualizado_em ?? null
        ]);
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
        $compradorTel  = $this->buscaTelefoneUsuarioDados($reembolso->comprador_id);

        echo "<tr><th>ID</th><td>{$reembolso->id}</td></tr>";
        echo "<tr><th>Status</th><td>{$reembolso->status}</td></tr>";
        echo "<tr><th>Valor Total</th><td>R$ " . number_format($reembolso->valor_total, 2, ',', '.') . "</td></tr>";
        echo "<tr><th>Produtor</th><td>{$produtorNome}</td></tr>";
        echo "<tr><th>Afiliado</th><td>{$afiliadoNome}</td></tr>";
        echo "<tr><th>Comprador</th><td>{$compradorNome} " . ($compradorCpf ? "<br><small>CPF: {$compradorCpf}</small>" : "") . ($compradorTel ? "<br><small>Tel: {$compradorTel}</small>" : "") . "</td></tr>";
        echo "<tr><th>Motivo</th><td>" . nl2br(htmlspecialchars($reembolso->motivo ?? '')) . "</td></tr>";
        if ($reembolso->contestacao) {
            echo "<tr><th>Contestação</th><td>" . nl2br(htmlspecialchars($reembolso->contestacao)) . ($reembolso->contestado_em ? "<br><small>Enviada em: {$reembolso->contestado_em}</small>" : "") . "</td></tr>";
        }
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
        $transacao = @$data["transacao"] ?: ($venda ? $venda->transacao : null);

        if (!$transacao) {
            http_response_code(400);
            echo json_encode(["erro" => "Transação não encontrada para estorno"]);
            return;
        }

        $amount  = (int) round($reembolso->valor_total * 100);
        $gateway = $this->detectarGateway($transacao, $venda ? ($venda->gateway_usado ?? $venda->meio_pagamento) : null);

        try {
            $resp = $this->executarEstorno($gateway, $transacao, $amount);
        } catch (\Throwable $e) {
            error_log("[Reembolso] Estorno falhou gateway={$gateway}: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => $e->getMessage()]);
            return;
        }

        $ok = $this->estornoSucedeu($gateway, $resp);

        if ($ok) {
            $pdo = Connect::getInstance();
            $pdo->prepare("UPDATE reembolsos SET status = 4, atualizado_em = NOW() WHERE id = :id")
                ->execute([":id" => $reembolso->id]);
            $this->atualizarVendaDevolvida($reembolso->venda_id);
            $this->bloquearAcessoMembrosCurso($reembolso);
            $this->notificarReembolsoConcluido($reembolso, false);
            echo json_encode(["ok" => true, "status" => 4, "gateway" => $gateway, "resp" => $resp]);
        } else {
            http_response_code(400);
            echo json_encode(["erro" => "Falha ao estornar no gateway {$gateway}", "gateway" => $gateway, "resp" => $resp]);
        }
    }

    /**
     * Bloqueia o acesso do comprador ao curso no Site Membros (octaclube) quando o produto
     * for do tipo curso com membros = "octaclube". Idempotente: se já bloqueado, não causa erro.
     */
    private function bloquearAcessoMembrosCurso(Reembolso $reembolso): void
    {
        if (!$reembolso->venda_id || !$reembolso->comprador_id) return;
        try {
            $pdo = Connect::getInstance();

            // Busca o produto da venda e verifica se é octaclube
            $stmtV = $pdo->prepare("SELECT produto FROM vendas WHERE id = :id LIMIT 1");
            $stmtV->execute([':id' => $reembolso->venda_id]);
            $venda = $stmtV->fetch(\PDO::FETCH_OBJ);
            if (!$venda) return;

            $stmtP = $pdo->prepare("SELECT membros FROM produtos WHERE id = :id LIMIT 1");
            $stmtP->execute([':id' => $venda->produto]);
            $produto = $stmtP->fetch(\PDO::FETCH_OBJ);
            if (!$produto || $produto->membros !== 'octaclube') return;

            // Busca o curso vinculado ao produto
            $stmt = $pdo->prepare("SELECT id FROM cursos WHERE produto = :produto LIMIT 1");
            $stmt->execute([':produto' => $venda->produto]);
            $curso = $stmt->fetch(\PDO::FETCH_OBJ);
            if (!$curso) return;

            // Bloqueia todas as matrículas ativas do aluno neste curso (status 2 = bloqueado)
            $pdo->prepare(
                "UPDATE turmaaluno SET status = 2, updated_at = NOW()
                 WHERE aluno = :aluno AND curso = :curso AND status = 1"
            )->execute([':aluno' => $reembolso->comprador_id, ':curso' => $curso->id]);

            error_log("[Reembolso] Acesso ao curso {$curso->id} bloqueado para comprador {$reembolso->comprador_id} (reembolso #{$reembolso->id})");
        } catch (\Throwable $e) {
            error_log("[Reembolso] Falha ao bloquear acesso ao curso (reembolso #{$reembolso->id}): " . $e->getMessage());
        }
    }

    /**
     * Envia push notification ao produtor informando que o reembolso foi concluído.
     */
    private function notificarReembolsoConcluido(Reembolso $reembolso, bool $automatico): void
    {
        if (!$reembolso->produtor_id) return;
        try {
            $tipo     = $automatico ? 'automático' : 'manual';
            $valor    = number_format($reembolso->valor_total, 2, ',', '.');
            $titulo   = "Reembolso #{$reembolso->id} concluído";
            $mensagem = "O reembolso de R$ {$valor} foi processado ({$tipo}).";
            (new AppProcessos(null))->enviarNotificacaoUsuarioEspecifico(
                $reembolso->produtor_id, $titulo, $mensagem
            );
        } catch (\Throwable $e) {
            error_log("[Reembolso Notif] id={$reembolso->id}: " . $e->getMessage());
        }
    }

    /**
     * Marca a venda associada como DEVOLVIDA (status_transacao = 6).
     */
    private function atualizarVendaDevolvida(?int $vendaId): void
    {
        if (!$vendaId) return;
        Connect::getInstance()
            ->prepare("UPDATE vendas SET status_transacao = 6, updated_at = NOW() WHERE id = :id")
            ->execute([":id" => $vendaId]);
    }

    /**
     * Detecta o gateway pelo prefixo do ID de transação ou pelo campo gateway_usado/meio_pagamento.
     */
    private function detectarGateway(string $transacao, ?string $gatewayOuMeio): string
    {
        // Detecção pelo prefixo do ID de transação (mais confiável)
        if (str_starts_with($transacao, 'pi_'))                              return 'stripe';
        if (str_starts_with($transacao, 'or_') || str_starts_with($transacao, 'ch_')) return 'pagarme';
        if (is_numeric($transacao))                                          return 'efibank';

        // Fallback pelo campo salvo na venda
        $mp = strtolower((string)$gatewayOuMeio);
        if (str_contains($mp, 'stripe'))  return 'stripe';
        if (str_contains($mp, 'efi'))     return 'efibank';

        return 'pagarme';
    }

    /**
     * Chama o método de estorno do gateway correto.
     */
    private function executarEstorno(string $gateway, string $transacao, int $amountCentavos): mixed
    {
        switch ($gateway) {

            case 'stripe':
                $stripeData = str_starts_with($transacao, 'pi_')
                    ? ["payment_intent" => $transacao, "valor" => $amountCentavos]
                    : ["transacao"      => $transacao, "valor" => $amountCentavos];
                return (new Stripe())->estornoCartao($stripeData);

            case 'efibank':
                if (!is_numeric($transacao)) {
                    // Pix EfiBank: reembolso Pix requer endToEndId que não é o txid.
                    // Não é possível estornar automaticamente sem o E2E ID do pagamento recebido.
                    throw new \Exception("Reembolso Pix EfiBank não pode ser feito automaticamente: registre o estorno manualmente no painel EfiBank.");
                }
                $efi = new EfiBank();
                return $efi->reembolso((int)$transacao);

            case 'pagarme':
            default:
                $pagarme  = new PagarMe();
                $chargeId = $transacao;
                if (str_starts_with($transacao, 'or_')) {
                    $list = $pagarme->cobrancaPorPedido(["transacao" => $transacao]);
                    if (@$list->data && @$list->data[0]->id) {
                        $chargeId = $list->data[0]->id;
                    } else {
                        throw new \Exception("Charge PagarMe não encontrada para o pedido {$transacao}");
                    }
                }
                return $pagarme->reembolso($chargeId, $amountCentavos, null);
        }
    }

    /**
     * Verifica se a resposta do gateway indica estorno bem-sucedido.
     */
    private function estornoSucedeu(string $gateway, $resp): bool
    {
        switch ($gateway) {
            case 'stripe':  return !empty($resp->id);
            case 'efibank': return isset($resp->data->status) && $resp->data->status === 'canceled';
            default:        return @$resp->status === 'canceled' || @$resp->status === 'failed' || !empty($resp->id);
        }
    }
}

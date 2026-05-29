<?php

namespace Source\App\Atendente;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\App\Usuarios\AppUsuarios;
use Source\App\Checkout1\GatewayManager;
use Source\App\Checkout1\Validacao;
use Source\Models\Atendente\VendaPersonalizadaItem;
use Source\Models\Configuracoes\Taxa;
use Source\Models\Permissao\Section;
use Source\Models\Produtos\Plano;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Vendas\Venda;

class AppAtendente
{
    private $view;
    private Section $section;

    public function __construct($router)
    {
        $tema = $_SESSION["tema"] ?? "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/{$tema}", "php");
        $this->view->addData(["router" => $router]);
        $this->section = new Section();
    }

    // ─── NOVA VENDA (GET) ────────────────────────────────────────────────────────

    public function novaVenda($data)
    {
        (new AppSessao())->verificaSeLogado();
        $this->exigeAtendente();

        $produtorId = $this->getProdutorId();
        $tipo       = (int)$this->section->getTipo();
        $erro       = null;

        // tipo=7 sem produtor associado → bloqueia
        if ($tipo === 7 && !$produtorId) {
            echo $this->view->render("atendente/nova_venda", [
                "title"    => "Nova Venda | " . SITE,
                "produtos" => [],
                "erro"     => "Sua conta ainda não está associada a nenhum produtor. Contate o administrador.",
            ]);
            return;
        }

        $where = $produtorId
            ? "excluir = 0 AND status = 2 AND rascunho = 0 AND usuario = {$produtorId}"
            : "excluir = 0 AND status = 2 AND rascunho = 0";

        $rawProdutos = (new Produto())->find($where)->order("nome ASC")->fetch(true) ?? [];

        // Monta array com produto + planos ativos (evita queries na view)
        $produtosComPlanos = [];
        foreach ($rawProdutos as $produto) {
            $planos = (new Plano())->find(
                "produto = {$produto->id} AND ativo = 1 AND privado = 0"
            )->order("preco ASC")->fetch(true);

            if (!$planos) continue; // produto sem plano ativo: não exibe

            $produtosComPlanos[] = [
                "produto" => $produto,
                "planos"  => $planos,
            ];
        }

        echo $this->view->render("atendente/nova_venda", [
            "title"              => "Nova Venda | " . SITE,
            "produtos_com_planos" => $produtosComPlanos,
            "erro"               => $erro,
        ]);
    }

    // ─── CRIAR VENDA (POST) ───────────────────────────────────────────────────────

    public function criarVenda($data)
    {
        (new AppSessao())->verificaSeLogado();
        $this->exigeAtendente();

        header("Content-Type: application/json");

        $planoIds   = array_map('intval', (array)($_POST["planos"]   ?? []));
        $valorFinal = (float) str_replace(",", ".", $_POST["valor_final"] ?? "0");
        $nome       = trim($_POST["nome"]     ?? "");
        $email      = trim($_POST["email"]    ?? "");
        $cpf        = preg_replace('/\D/', '', $_POST["cpf"]      ?? "");
        $telefone   = preg_replace('/\D/', '', $_POST["telefone"] ?? "");

        if (empty($planoIds) || $valorFinal <= 0 || !$nome || !$email || !$cpf || !$telefone) {
            echo json_encode(["erro" => true, "msg" => "Preencha todos os campos obrigatórios."]);
            exit;
        }

        // ── Produtor associado ao atendente ──────────────────────────────────────
        $produtorId     = $this->getProdutorId();
        $tipoAtual      = (int)$this->section->getTipo();
        $restringirProdutor = ($tipoAtual === 7); // admins não restringem

        if ($restringirProdutor && !$produtorId) {
            echo json_encode(["erro" => true, "msg" => "Atendente sem produtor associado."]);
            exit;
        }

        // ── Carrega planos e valida que pertencem ao produtor do atendente ────────
        $planos   = [];
        $produtos = [];

        foreach ($planoIds as $pid) {
            $plano   = (new Plano())->findById($pid);
            $produto = $plano ? (new Produto())->findById($plano->produto) : null;

            if (!$plano || !$produto || $produto->excluir || $produto->status != 2) {
                echo json_encode(["erro" => true, "msg" => "Produto/plano inválido selecionado."]);
                exit;
            }

            if ($restringirProdutor && (int)$produto->usuario !== $produtorId) {
                echo json_encode(["erro" => true, "msg" => "Produto não pertence ao produtor associado a este atendente."]);
                exit;
            }

            $planos[]   = $plano;
            $produtos[] = $produto;
        }

        // Para admins sem produtorId fixo, usa o produtor do primeiro produto
        if (!$produtorId) {
            $produtorId = (int)$produtos[0]->usuario;
        }

        // ── Soma original para cálculo proporcional ────────────────────────────────
        $somaOriginal = 0.00;
        foreach ($planos as $i => $plano) {
            $somaOriginal += (float)$plano->preco;
        }
        if ($somaOriginal <= 0) $somaOriginal = $valorFinal;

        // ── Taxa da plataforma (cobra uma vez sobre o total) ─────────────────────
        $produtor      = (new Usuario())->findById($produtorId);
        $taxaPlataforma = (float)($produtor->taxa_redrocket ?? 5);

        $valorRedrocket  = round(($valorFinal * ($taxaPlataforma / 100)) + 1, 2);
        $valorLiquido    = $valorFinal - $valorRedrocket;
        $comissaoProdutor = $valorLiquido;

        // ── CPF/CNPJ e telefone ────────────────────────────────────────────────────
        $cpfLen = strlen($cpf);
        $tipoDoc = $cpfLen <= 11 ? 1 : 2;

        if (strlen($telefone) > 11 && substr($telefone, 0, 2) === "55") {
            $ddd = substr($telefone, 2, 2);
            $fone = substr($telefone, 4);
        } else {
            $ddd  = substr($telefone, 0, 2);
            $fone = substr($telefone, 2);
        }

        // ── Comprador: busca ou cria ───────────────────────────────────────────────
        $cadastrado = (new UsuarioDados())->verificaCpf(["cpf" => $cpf]);
        if ($cadastrado) {
            $compradorId = $cadastrado;
        } else {
            $appUsu = new AppUsuarios(null);
            $novoUsu = $appUsu->cadastroUsuarioDados([
                "nome"     => $nome,
                "email"    => $email,
                "cpf"      => $cpf,
                "tipo_doc" => $tipoDoc,
                "ddd"      => $ddd,
                "telefone" => $fone,
            ]);
            $compradorId = $novoUsu["id"] ?? null;
        }

        if (!$compradorId) {
            echo json_encode(["erro" => true, "msg" => "Erro ao cadastrar comprador."]);
            exit;
        }

        // ── Protocolo e link público ───────────────────────────────────────────────
        $protocolo  = strtoupper(bin2hex(random_bytes(5)));
        $linkPublico = bin2hex(random_bytes(16)); // 32 chars hex UUID

        $primeiroProduto = $produtos[0];
        $primeiroPlano   = $planos[0];

        // ── Split de pagamento ────────────────────────────────────────────────────
        $usRed = (new Usuario())->findById(1);
        $comissoesPagamento = [
            "redrocket" => [
                "usuario"           => $usRed->pagarme_conta ?? "",
                "comissao"          => $valorRedrocket,
                "efibank_payee_code" => $usRed->efibank_payee_code ?? "",
            ],
            "produtor" => [
                "usuario"           => $produtor->pagarme_conta ?? "",
                "comissao"          => $comissaoProdutor,
                "efibank_payee_code" => $produtor->efibank_payee_code ?? "",
            ],
        ];

        $nomeKit = implode(" + ", array_map(fn($p) => $p->nome, $produtos));

        $dataGw = [
            "nome"                => $nome,
            "email"               => $email,
            "cpf"                 => $cpf,
            "ddd"                 => $ddd,
            "telefone"            => $fone,
            "preco"               => (int) round($valorFinal * 100),
            "produto"             => $primeiroProduto->codigo,
            "nomekit"             => $nomeKit,
            "protocolo"           => $protocolo,
            "tipo_doc"            => $tipoDoc,
            "pais"                => "BR",
            "comissoes_pagamento" => $comissoesPagamento,
        ];

        // ── Chama gateway ─────────────────────────────────────────────────────────
        $gwResult = (new GatewayManager())->compraPix($dataGw);

        if ($gwResult->erro) {
            echo json_encode(["erro" => true, "msg" => $gwResult->mensagem_erro ?? "Falha ao gerar PIX."]);
            exit;
        }

        // ── Salva venda principal ─────────────────────────────────────────────────
        $venda = new Venda();
        $venda->produto           = $primeiroProduto->id;
        $venda->plano             = $primeiroPlano->id;
        $venda->comprador         = $compradorId;
        $venda->produtor          = $produtorId;
        $venda->comissao_produtor = $comissaoProdutor;
        $venda->meio_pagamento    = $gwResult->meio_pagamento;
        $venda->forma_pagamento   = 3; // PIX
        $venda->status_transacao  = 1; // aguardando
        $venda->transacao         = $gwResult->transacao_id;
        $venda->valor_bruto       = $valorFinal;
        $venda->valor_produto     = $valorFinal;
        $venda->valor_juros       = 0;
        $venda->taxa_juros        = 0;
        $venda->taxa_redrocket    = $taxaPlataforma;
        $venda->valor_redrocket   = $valorRedrocket;
        $venda->valor_desconto    = 0;
        $venda->valor_liquido     = $valorLiquido;
        $venda->parcelas          = 1;
        $venda->data_pedido       = date("Y-m-d");
        $venda->data_ultimo_evento = date("Y-m-d H:i:s");
        $venda->tipo_venda        = 1;
        $venda->link_publico      = $linkPublico;
        $venda->atendente         = $this->section->getId();
        $venda->ip                = $_SERVER["REMOTE_ADDR"] ?? "";

        if (!$venda->save()) {
            echo json_encode(["erro" => true, "msg" => "Erro ao salvar venda."]);
            exit;
        }

        // ── Salva itens proporcionais ─────────────────────────────────────────────
        $totalProporcional = 0.00;
        $numItens = count($planos);

        foreach ($planos as $i => $plano) {
            $proporcao = $somaOriginal > 0
                ? ((float)$plano->preco / $somaOriginal)
                : (1 / $numItens);

            $valorProp = ($i < $numItens - 1)
                ? round($valorFinal * $proporcao, 2)
                : round($valorFinal - $totalProporcional, 2); // último absorve centavos

            $totalProporcional += $valorProp;

            $item = new VendaPersonalizadaItem();
            $item->venda              = $venda->id;
            $item->produto            = $produtos[$i]->id;
            $item->plano              = $plano->id;
            $item->nome_produto       = $produtos[$i]->nome;
            $item->nome_plano         = $plano->nome;
            $item->valor_original     = (float)$plano->preco;
            $item->valor_proporcional = $valorProp;
            $item->save();
        }

        // ── Salva QR code na venda ────────────────────────────────────────────────
        $vendaSalva = (new Venda())->findById($venda->id);
        if ($vendaSalva && $gwResult->qr_code) {
            $vendaSalva->url_checkout = $gwResult->qr_code;
            $vendaSalva->save();
        }

        $linkPagamento = url("pagar/{$linkPublico}");

        echo json_encode([
            "erro"            => false,
            "link_pagamento"  => $linkPagamento,
            "qr_code"         => $gwResult->qr_code,
            "qr_code_image"   => $gwResult->qr_code_image,
            "venda_id"        => $venda->id,
        ]);
        exit;
    }

    // ─── MINHAS VENDAS (GET) ─────────────────────────────────────────────────────

    public function minhasVendas($data)
    {
        (new AppSessao())->verificaSeLogado();
        $this->exigeAtendente();

        $atendenteId = $this->section->getId();

        $vendas = (new Venda())->find(
            "tipo_venda = 1 AND atendente = :a",
            "a={$atendenteId}"
        )->order("id DESC")->fetch(true);

        $vendasComItens = [];
        foreach ($vendas as $v) {
            $itens = (new VendaPersonalizadaItem())->porVenda($v->id);
            $vendasComItens[] = ["venda" => $v, "itens" => $itens];
        }

        echo $this->view->render("atendente/minhas_vendas", [
            "title"          => "Minhas Vendas | " . SITE,
            "vendas_itens"   => $vendasComItens,
        ]);
    }

    // ─── CONSULTAR PIX AJAX (POST) ────────────────────────────────────────────────

    public function consultarPix($data)
    {
        header("Content-Type: application/json");

        $linkPublico = trim($_POST["link_publico"] ?? "");
        if (!$linkPublico) {
            echo json_encode(["status" => null]);
            exit;
        }

        $venda = (new Venda())->find(
            "link_publico = :lp",
            "lp={$linkPublico}"
        )->fetch();

        if (!$venda) {
            echo json_encode(["status" => null]);
            exit;
        }

        echo json_encode([
            "status"          => (int)$venda->status_transacao,
            "status_texto"    => $this->statusTexto((int)$venda->status_transacao),
        ]);
        exit;
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────────

    private function exigeAtendente(): void
    {
        $tipo = (int)$this->section->getTipo();
        if (!in_array($tipo, [1, 2, 3, 7])) {
            header("Location: " . url());
            exit;
        }
    }

    /**
     * Retorna o ID do produtor associado ao atendente logado.
     * Admins (tipo 1/2/3) não têm restrição de produtor — retorna null,
     * sinalizando que o caller deve lidar diferente.
     * Para tipo 7 busca atendente_produtor no registro do usuario.
     */
    private function getProdutorId(): ?int
    {
        $tipo = (int)$this->section->getTipo();

        // Admins têm acesso livre — sem restrição de produtor
        if (in_array($tipo, [1, 2, 3])) {
            return null; // null = sem filtro (todos os produtos)
        }

        $usuario = (new Usuario())->findById($this->section->getId());
        return $usuario && $usuario->atendente_produtor
            ? (int)$usuario->atendente_produtor
            : null;
    }

    private function statusTexto(int $status): string
    {
        switch ($status) {
            case 4:  return "Pago";
            case 5:  return "Cancelado";
            default: return "Pendente";
        }
    }
}

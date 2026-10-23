<?php

namespace Source\App\Ia;

use Source\App\Checkout1\GatewayManager;
use Source\App\Usuarios\AppUsuarios;
use Source\Models\Produtos\Plano;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Vendas\Venda;

/**
 * API REST para agentes de IA (QuickAgent / automações).
 * Autenticação: header  X-Api-Key: <API_KEY_IA>
 *
 * Rotas:
 *   GET  /ia/produtos?produtor_id=N      → lista produtos + planos do produtor
 *   POST /ia/venda                       → cria venda PIX, retorna link + QR
 *   GET  /ia/venda/{uuid}                → status pelo link_publico
 *   GET  /ia/venda/buscar?q=             → busca por CPF / telefone / e-mail
 *
 * Status: 1-3=aguardando  4=pago  5-6=cancelado  7=chargeback
 */
class AppIaApi
{
    public function __construct($router) {}

    // ── helpers ──────────────────────────────────────────────────────────────

    private function json($data, int $status = 200): void
    {
        http_response_code($status);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function autenticar(): bool
    {
        $headers = getallheaders();
        $key = "";
        foreach ($headers as $k => $v) {
            if (strtolower($k) === "x-api-key") { $key = trim($v); break; }
        }
        if (!$key && !empty($_SERVER["HTTP_X_API_KEY"])) {
            $key = trim($_SERVER["HTTP_X_API_KEY"]);
        }
        return defined("API_KEY_IA") && hash_equals(API_KEY_IA, $key);
    }

    private function input(): array
    {
        $ct = $_SERVER["CONTENT_TYPE"] ?? "";
        if (strpos($ct, "application/json") !== false) {
            return (array)(json_decode(file_get_contents("php://input"), true) ?? []);
        }
        return array_merge($_GET, $_POST);
    }

    private function statusTexto(int $s): string
    {
        if ($s === 4)                   return "pago";
        if (in_array($s, [5, 6, 7]))    return "cancelado";
        return "aguardando";
    }

    // ── GET /ia/produtos?produtor_id=N ────────────────────────────────────────

    public function produtos($data): void
    {
        if (!$this->autenticar()) { $this->json(["erro" => "Não autorizado."], 401); }

        $produtorId = (int)($_GET["produtor_id"] ?? 0);
        if (!$produtorId) { $this->json(["erro" => "produtor_id obrigatório."], 422); }

        $rawProdutos = (new Produto())->find(
            "excluir = 0 AND status = 2 AND rascunho = 0 AND usuario = {$produtorId}"
        )->order("nome ASC")->fetch(true) ?? [];

        $resultado = [];
        foreach ($rawProdutos as $produto) {
            $planos = (new Plano())->find(
                "produto = {$produto->id} AND ativo = 1 AND privado = 0"
            )->order("preco ASC")->fetch(true);

            if (!$planos) continue;

            $resultado[] = [
                "id"     => (int)$produto->id,
                "codigo" => $produto->codigo,
                "nome"   => $produto->nome,
                "planos" => array_map(fn($pl) => [
                    "id"    => (int)$pl->id,
                    "nome"  => $pl->nome,
                    "preco" => (float)$pl->preco,
                ], $planos),
            ];
        }

        $this->json(["produtos" => $resultado]);
    }

    // ── POST /ia/venda ────────────────────────────────────────────────────────
    // Body JSON: { nome, cpf, telefone, email, plano_id, valor? }

    public function criarVenda($data): void
    {
        if (!$this->autenticar()) { $this->json(["erro" => "Não autorizado."], 401); }

        $in = $this->input();

        $planoId  = (int)($in["plano_id"] ?? 0);
        $nome     = trim($in["nome"]      ?? "");
        $email    = trim($in["email"]     ?? "");
        $cpf      = preg_replace('/\D/', '', $in["cpf"]      ?? "");
        $telefone = preg_replace('/\D/', '', $in["telefone"] ?? "");

        if (!$planoId || !$nome || !$email || !$cpf || !$telefone) {
            $this->json(["erro" => "Campos obrigatórios: nome, cpf, telefone, email, plano_id."], 422);
        }

        $plano   = (new Plano())->findById($planoId);
        $produto = $plano ? (new Produto())->findById($plano->produto) : null;

        if (!$plano || !$produto || $produto->excluir || $produto->status != 2) {
            $this->json(["erro" => "Plano ou produto inválido."], 422);
        }

        $produtorId      = (int)$produto->usuario;
        $produtor        = (new Usuario())->findById($produtorId);
        $valorFinal      = isset($in["valor"]) && (float)$in["valor"] > 0
                            ? (float)$in["valor"]
                            : (float)$plano->preco;

        $taxaPlataforma  = (float)($produtor->taxa_redrocket ?? 5);
        $valorRedrocket  = round(($valorFinal * ($taxaPlataforma / 100)) + 1, 2);
        $valorLiquido    = $valorFinal - $valorRedrocket;

        // telefone split
        if (strlen($telefone) > 11 && substr($telefone, 0, 2) === "55") {
            $ddd  = substr($telefone, 2, 2);
            $fone = substr($telefone, 4);
        } else {
            $ddd  = substr($telefone, 0, 2);
            $fone = substr($telefone, 2);
        }
        $tipoDoc = strlen($cpf) <= 11 ? 1 : 2;

        // comprador: busca ou cria
        $compradorId = (new UsuarioDados())->verificaCpf(["cpf" => $cpf]);
        if (!$compradorId) {
            $novoUsu = (new AppUsuarios(null))->cadastroUsuarioDados([
                "nome"     => $nome,
                "email"    => $email,
                "cpf"      => $cpf,
                "tipo_doc" => $tipoDoc,
                "ddd"      => $ddd,
                "telefone" => $fone,
            ]);
            $compradorId = $novoUsu["id"] ?? null;
        }
        if (!$compradorId) { $this->json(["erro" => "Erro ao cadastrar comprador."], 500); }

        $protocolo   = strtoupper(bin2hex(random_bytes(5)));
        $linkPublico = bin2hex(random_bytes(16));
        $usRed       = (new Usuario())->findById(1);

        $gwResult = (new GatewayManager())->compraPix([
            "nome"      => $nome,
            "email"     => $email,
            "cpf"       => $cpf,
            "ddd"       => $ddd,
            "telefone"  => $fone,
            "preco"     => (int) round($valorFinal * 100),
            "produto"   => $produto->codigo,
            "nomekit"   => $produto->nome,
            "protocolo" => $protocolo,
            "tipo_doc"  => $tipoDoc,
            "pais"      => "BR",
            "comissoes_pagamento" => [
                "redrocket" => [
                    "usuario"            => $usRed->pagarme_conta ?? "",
                    "comissao"           => $valorRedrocket,
                    "efibank_payee_code" => $usRed->efibank_payee_code ?? "",
                ],
                "produtor" => [
                    "usuario"            => $produtor->pagarme_conta ?? "",
                    "comissao"           => $valorLiquido,
                    "efibank_payee_code" => $produtor->efibank_payee_code ?? "",
                ],
            ],
        ]);

        if ($gwResult->erro) {
            $this->json(["erro" => $gwResult->mensagem_erro ?? "Falha ao gerar PIX."], 500);
        }

        $venda = new Venda();
        $venda->produto            = $produto->id;
        $venda->plano              = $plano->id;
        $venda->comprador          = $compradorId;
        $venda->produtor           = $produtorId;
        $venda->comissao_produtor  = $valorLiquido;
        $venda->meio_pagamento     = $gwResult->meio_pagamento;
        $venda->forma_pagamento    = 3;
        $venda->status_transacao   = 1;
        $venda->transacao          = $gwResult->transacao_id;
        $venda->valor_bruto        = $valorFinal;
        $venda->valor_produto      = $valorFinal;
        $venda->valor_juros        = 0;
        $venda->taxa_juros         = 0;
        $venda->taxa_redrocket     = $taxaPlataforma;
        $venda->valor_redrocket    = $valorRedrocket;
        $venda->valor_desconto     = 0;
        $venda->valor_liquido      = $valorLiquido;
        $venda->parcelas           = 1;
        $venda->data_pedido        = date("Y-m-d");
        $venda->data_ultimo_evento = date("Y-m-d H:i:s");
        $venda->tipo_venda         = 1;
        $venda->link_publico       = $linkPublico;
        $venda->url_checkout       = $gwResult->qr_code ?? null;
        $venda->ip                 = $_SERVER["REMOTE_ADDR"] ?? "";

        if (!$venda->save()) { $this->json(["erro" => "Erro ao salvar venda."], 500); }

        $this->json([
            "venda_id"       => (int)$venda->id,
            "link_pagamento" => url("pagar/{$linkPublico}"),
            "link_publico"   => $linkPublico,
            "qr_code"        => $gwResult->qr_code ?? null,
            "qr_code_image"  => $gwResult->qr_code_image ?? null,
            "valor"          => $valorFinal,
            "produto"        => $produto->nome,
            "plano"          => $plano->nome,
        ], 201);
    }

    // ── GET /ia/venda/{uuid} ──────────────────────────────────────────────────

    public function statusVenda($data): void
    {
        if (!$this->autenticar()) { $this->json(["erro" => "Não autorizado."], 401); }

        $uuid = trim($data["uuid"] ?? "");
        if (!$uuid) { $this->json(["erro" => "uuid obrigatório."], 422); }

        $venda = (new Venda())->find("link_publico = :lp", "lp={$uuid}")->fetch();
        if (!$venda) { $this->json(["erro" => "Venda não encontrada."], 404); }

        $status = (int)$venda->status_transacao;
        $this->json([
            "venda_id"       => (int)$venda->id,
            "link_publico"   => $venda->link_publico,
            "status"         => $status,
            "status_texto"   => $this->statusTexto($status),
            "pago"           => $status === 4,
            "valor_bruto"    => (float)$venda->valor_bruto,
            "data_pedido"    => $venda->data_pedido,
            "data_pagamento" => $venda->data_pagamento ?? null,
        ]);
    }

    // ── GET /ia/venda/buscar?q= ───────────────────────────────────────────────

    public function buscarVenda($data): void
    {
        if (!$this->autenticar()) { $this->json(["erro" => "Não autorizado."], 401); }

        $termo = trim($_GET["q"] ?? "");
        if (strlen($termo) < 3) { $this->json(["erro" => "q deve ter ao menos 3 caracteres."], 422); }

        $apenasDigitos = preg_replace('/\D/', '', $termo);

        if (strlen($apenasDigitos) === 11 || strlen($apenasDigitos) === 14) {
            $where = "cpf_cnpj = '{$apenasDigitos}'";
        } elseif (filter_var($termo, FILTER_VALIDATE_EMAIL)) {
            $where = "email = '" . addslashes($termo) . "'";
        } elseif (strlen($apenasDigitos) >= 8) {
            $where = "telefone LIKE '%{$apenasDigitos}%'";
        } else {
            $t     = addslashes($termo);
            $where = "nome LIKE '%{$t}%' OR email LIKE '%{$t}%'";
        }

        $compradores = (new UsuarioDados())->find($where)->fetch(true);
        if (!$compradores) { $this->json(["vendas" => []]); }

        $ids  = implode(',', array_map(fn($c) => (int)$c->id, $compradores));
        $rows = (new Venda())->find("comprador IN ({$ids})")->order("id DESC")->limit(20)->fetch(true);
        if (!$rows) { $this->json(["vendas" => []]); }

        $vendas = [];
        foreach ($rows as $v) {
            $status   = (int)$v->status_transacao;
            $vendas[] = [
                "venda_id"       => (int)$v->id,
                "link_publico"   => $v->link_publico,
                "link_pagamento" => $v->link_publico ? url("pagar/{$v->link_publico}") : null,
                "status"         => $status,
                "status_texto"   => $this->statusTexto($status),
                "pago"           => $status === 4,
                "valor_bruto"    => (float)$v->valor_bruto,
                "data_pedido"    => $v->data_pedido,
                "data_pagamento" => $v->data_pagamento ?? null,
            ];
        }

        $this->json(["vendas" => $vendas]);
    }
}

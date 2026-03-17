<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

/**
 * Iugu – Integração com a API v1
 *
 * Documentação oficial: https://dev.iugu.com/reference
 *
 * Campos esperados em ConfiguracaoCheckout (ajuste o findById conforme o id cadastrado):
 *   chave_producao  → Account ID de produção
 *   token_producao  → API Token live (secret)
 *   chave_sandbox   → Account ID de sandbox
 *   token_sandbox   → API Token test
 *   sandbox         → 0 = produção | 1 = sandbox
 */
class Iugu
{
    private string $baseUrl  = "https://api.iugu.com/v1";
    private string $apiToken = "";
    private string $accountId = "";
    private bool   $sandbox  = false;

    // ID do registro na tabela configuracao_checkout para a Iugu
    private const CONFIG_ID = 9;

    public function __construct()
    {
        // construtor leve; credenciais carregadas em dados()
    }

    // -------------------------------------------------------------------------
    // Credenciais
    // -------------------------------------------------------------------------

    public function dados($adquirenteId = null): void
    {
        $id = $adquirenteId ?? self::CONFIG_ID;
        $adquirente = (new ConfiguracaoCheckout())->findById($id);

        if (!$adquirente) {
            throw new \Exception("[Iugu] Configuração (id={$id}) não encontrada.");
        }

        $this->sandbox   = (bool)($adquirente->sandbox ?? false);
        $this->accountId = $this->sandbox
            ? (string)($adquirente->chave_sandbox   ?? "")
            : (string)($adquirente->chave_producao  ?? "");
        $this->apiToken  = $this->sandbox
            ? (string)($adquirente->token_sandbox   ?? "")
            : (string)($adquirente->token_producao  ?? "");

        if (empty($this->apiToken)) {
            throw new \Exception("[Iugu] API Token não configurado.");
        }
    }

    // -------------------------------------------------------------------------
    // Executor HTTP
    // -------------------------------------------------------------------------

    /**
     * Executa uma requisição na API Iugu.
     *
     * Auth: HTTP Basic – api_token como usuário, senha vazia.
     * Ref: https://dev.iugu.com/reference/autenticacao
     */
    public function executa(string $endpoint, string $metodo, ?array $payload = null): object
    {
        $url  = $this->baseUrl . $endpoint;
        $auth = base64_encode($this->apiToken . ":");
        $json = $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $metodo,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Basic " . $auth,
                "Content-Type: application/json",
                "Accept: application/json",
            ],
        ]);

        $resp  = curl_exec($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errNo = curl_errno($ch);
        $err   = curl_error($ch);
        curl_close($ch);

        error_log("[Iugu] {$metodo} {$url} → HTTP {$http}");
        if ($errNo) {
            error_log("[Iugu] cURL #{$errNo}: {$err}");
            throw new \Exception("[Iugu] Erro de conexão: #{$errNo} {$err}");
        }

        $decoded = json_decode((string)$resp);
        if ($decoded === null) {
            error_log("[Iugu] Resposta inválida: {$resp}");
            throw new \Exception("[Iugu] Resposta não-JSON recebida (HTTP {$http}).");
        }

        return $decoded;
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /** Remove caracteres não numéricos */
    private function onlyDigits(?string $value): string
    {
        return $value ? preg_replace('/\D+/', '', $value) : "";
    }

    /**
     * Monta o objeto "payer" padrão usado em invoices.
     * Ref: https://dev.iugu.com/reference/criar-fatura
     */
    private function buildPayer(array $data): array
    {
        return [
            "cpf_cnpj"     => $this->onlyDigits($data["cpf"] ?? ""),
            "name"         => trim((string)($data["nome"] ?? "")),
            "phone_prefix" => (string)($data["ddd"] ?? ""),
            "phone"        => $this->onlyDigits($data["telefone"] ?? ""),
            "email"        => (string)($data["email"] ?? ""),
            "address"      => [
                "street"   => (string)($data["rua"]     ?? ""),
                "number"   => (string)($data["numero"]  ?? ""),
                "complement" => (string)($data["complemento"] ?? ""),
                "district" => (string)($data["bairro"]  ?? ""),
                "city"     => (string)($data["cidade"]  ?? ""),
                "state"    => (string)($data["estado"]  ?? ""),
                "country"  => "Brasil",
                "zip_code" => $this->onlyDigits($data["cep"] ?? ""),
            ],
        ];
    }

    /**
     * Monta o array de items da fatura.
     * price_cents: valor em centavos (int).
     */
    private function buildItems(array $data): array
    {
        return [
            [
                "description" => (string)($data["nomekit"] ?? $data["nome_produto"] ?? "Produto"),
                "quantity"    => 1,
                "price_cents" => (int)($data["preco"] ?? 0),
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Cobrança PIX
    // -------------------------------------------------------------------------

    /**
     * Gera uma cobrança via Pix (QR Code estático dinâmico).
     *
     * Fluxo:
     *   1. Cria a fatura com payable_with = "pix"
     *   2. A resposta já contém pix.qrcode e pix.qrcode_text
     *
     * Ref: https://dev.iugu.com/reference/criar-fatura
     *
     * Campos $data esperados:
     *   preco       int    valor em centavos
     *   nome        string nome do pagador
     *   email       string e-mail do pagador
     *   cpf         string CPF ou CNPJ
     *   vencimento  string data de vencimento "YYYY-MM-DD" (padrão: amanhã)
     *   postbackUrl string URL de notificação (webhook)
     */
    public function compraPix(array $data): object
    {
        $this->dados($data["adquirente"] ?? null);

        if (empty($data["preco"])) {
            throw new \Exception("[Iugu][Pix] Campo 'preco' obrigatório (centavos).");
        }
        if (empty($data["nome"]) || empty($data["email"]) || empty($data["cpf"])) {
            throw new \Exception("[Iugu][Pix] Campos obrigatórios: nome, email, cpf.");
        }

        $dueDate         = $data["vencimento"] ?? date("Y-m-d", strtotime("+1 day"));
        $notificationUrl = $data["postbackUrl"] ?? (defined("URL_BASE") ? URL_BASE . "/webhook/iugu" : "");

        $payload = [
            "account_id"          => $this->accountId,
            "email"               => (string)$data["email"],
            "due_date"            => $dueDate,
            "payable_with"        => "pix",
            "notification_url"    => $notificationUrl,
            "ignore_due_email"    => true,
            "items"               => $this->buildItems($data),
            "payer"               => $this->buildPayer($data),
            "custom_variables"    => [
                ["name" => "external_id", "value" => (string)($data["id"] ?? "")],
            ],
        ];

        return $this->executa("/invoices", "POST", $payload);
    }

    // -------------------------------------------------------------------------
    // Cobrança Cartão de Crédito
    // -------------------------------------------------------------------------

    /**
     * Tokeniza o cartão diretamente pelo backend e realiza a cobrança.
     *
     * Fluxo:
     *   1. Tokeniza os dados do cartão via POST /payment_token
     *   2. Cria a fatura (invoice) com payable_with = "credit_card"
     *   3. Realiza a cobrança (charge) associando token + invoice_id
     *
     * Ref tokenização: https://dev.iugu.com/reference/criar-token-de-cartao
     * Ref cobrança:    https://dev.iugu.com/reference/criar-cobranca
     *
     * Campos $data esperados:
     *   preco    int    valor em centavos
     *   nome     string nome do titular
     *   email    string e-mail do pagador
     *   cpf      string CPF/CNPJ
     *   numcart  string número do cartão
     *   titular  string nome impresso no cartão
     *   mes-val  string mês de validade (2 dígitos)
     *   ano-val  string ano de validade (4 dígitos)
     *   cvv      string código de segurança
     *   parcela  int    número de parcelas (default: 1)
     */
    public function compraCartao(array $data): object
    {
        $this->dados($data["adquirente"] ?? null);

        if (empty($data["preco"])) {
            throw new \Exception("[Iugu][Cartão] Campo 'preco' obrigatório (centavos).");
        }
        if (empty($data["numcart"]) || empty($data["cvv"])) {
            throw new \Exception("[Iugu][Cartão] Dados do cartão incompletos.");
        }

        $numero    = $this->onlyDigits($data["numcart"] ?? "");
        $cvv       = $this->onlyDigits($data["cvv"]     ?? "");
        $parcelas  = max(1, (int)($data["parcela"] ?? 1));
        $anoVal    = strlen((string)($data["ano-val"] ?? "")) === 4
            ? (string)$data["ano-val"]
            : "20" . substr((string)($data["ano-val"] ?? ""), -2);

        $nomeTitular = trim((string)($data["titular"] ?? $data["nome"] ?? ""));
        $partesNome  = explode(" ", $nomeTitular, 2);
        $firstName   = $partesNome[0]        ?? "";
        $lastName    = $partesNome[1] ?? $partesNome[0] ?? "";

        // --- 1. Tokenizar cartão ---
        $tokenPayload = [
            "account_id" => $this->accountId,
            "method"     => "credit_card",
            "test"       => $this->sandbox,
            "data"       => [
                "number"             => $numero,
                "verification_value" => $cvv,
                "first_name"         => $firstName,
                "last_name"          => $lastName,
                "month"              => str_pad((string)($data["mes-val"] ?? ""), 2, "0", STR_PAD_LEFT),
                "year"               => $anoVal,
            ],
        ];

        $tokenResp = $this->executa("/payment_token", "POST", $tokenPayload);

        if (empty($tokenResp->id)) {
            error_log("[Iugu][Cartão] Falha na tokenização: " . json_encode($tokenResp));
            throw new \Exception("[Iugu][Cartão] Não foi possível tokenizar o cartão.");
        }

        $cardToken = $tokenResp->id;

        // --- 2. Criar fatura ---
        $dueDate         = date("Y-m-d");
        $notificationUrl = $data["postbackUrl"] ?? (defined("URL_BASE") ? URL_BASE . "/webhook/iugu" : "");

        $invoicePayload = [
            "account_id"       => $this->accountId,
            "email"            => (string)$data["email"],
            "due_date"         => $dueDate,
            "payable_with"     => "credit_card",
            "notification_url" => $notificationUrl,
            "ignore_due_email" => true,
            "items"            => $this->buildItems($data),
            "payer"            => $this->buildPayer($data),
            "custom_variables" => [
                ["name" => "external_id", "value" => (string)($data["id"] ?? "")],
            ],
        ];

        $invoice = $this->executa("/invoices", "POST", $invoicePayload);

        if (empty($invoice->id)) {
            error_log("[Iugu][Cartão] Falha ao criar fatura: " . json_encode($invoice));
            throw new \Exception("[Iugu][Cartão] Não foi possível criar a fatura.");
        }

        // --- 3. Realizar cobrança ---
        // Ref: https://dev.iugu.com/reference/criar-cobranca
        $chargePayload = [
            "token"      => $cardToken,
            "invoice_id" => $invoice->id,
            "months"     => $parcelas,
        ];

        return $this->executa("/charge", "POST", $chargePayload);
    }

    // -------------------------------------------------------------------------
    // Cobrança Boleto Bancário
    // -------------------------------------------------------------------------

    /**
     * Gera um boleto bancário.
     *
     * Fluxo:
     *   1. Cria fatura com payable_with = "bank_slip"
     *   2. Resposta contém bank_slip.digitable_line e bank_slip.barcode
     *
     * Ref: https://dev.iugu.com/reference/criar-fatura
     *
     * Campos $data esperados:
     *   preco       int    valor em centavos
     *   nome        string nome do pagador
     *   email       string e-mail do pagador
     *   cpf         string CPF/CNPJ
     *   vencimento  string "YYYY-MM-DD" (padrão: 3 dias úteis)
     */
    public function compraBoleto(array $data): object
    {
        $this->dados($data["adquirente"] ?? null);

        if (empty($data["preco"])) {
            throw new \Exception("[Iugu][Boleto] Campo 'preco' obrigatório (centavos).");
        }
        if (empty($data["nome"]) || empty($data["email"]) || empty($data["cpf"])) {
            throw new \Exception("[Iugu][Boleto] Campos obrigatórios: nome, email, cpf.");
        }

        $dueDate         = $data["vencimento"] ?? date("Y-m-d", strtotime("+3 days"));
        $notificationUrl = $data["postbackUrl"] ?? (defined("URL_BASE") ? URL_BASE . "/webhook/iugu" : "");

        $payload = [
            "account_id"              => $this->accountId,
            "email"                   => (string)$data["email"],
            "due_date"                => $dueDate,
            "payable_with"            => "bank_slip",
            "notification_url"        => $notificationUrl,
            "ignore_due_email"        => true,
            "bank_slip_extra_days"    => (int)($data["bank_slip_extra_days"] ?? 0),
            "fines"                   => false,
            "per_day_interest"        => false,
            "items"                   => $this->buildItems($data),
            "payer"                   => $this->buildPayer($data),
            "custom_variables"        => [
                ["name" => "external_id", "value" => (string)($data["id"] ?? "")],
            ],
        ];

        return $this->executa("/invoices", "POST", $payload);
    }

    // -------------------------------------------------------------------------
    // Consultas
    // -------------------------------------------------------------------------

    /**
     * Consulta os dados de uma fatura pelo ID.
     * Ref: https://dev.iugu.com/reference/buscar-fatura
     */
    public function consultarFatura(string $invoiceId, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        return $this->executa("/invoices/{$invoiceId}", "GET");
    }

    /**
     * Lista faturas com filtros opcionais.
     * Ref: https://dev.iugu.com/reference/listar-faturas
     *
     * @param array $filtros  Ex.: ["status" => "pending", "limit" => 50, "start" => 0]
     */
    public function listarFaturas(array $filtros = [], $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        $query = !empty($filtros) ? "?" . http_build_query($filtros) : "";
        return $this->executa("/invoices{$query}", "GET");
    }

    // -------------------------------------------------------------------------
    // Reembolso / Estorno
    // -------------------------------------------------------------------------

    /**
     * Estorna/cancela uma fatura.
     * Para faturas pagas via cartão, gera estorno.
     * Para boleto/pix pendente, cancela a cobrança.
     *
     * Ref: https://dev.iugu.com/reference/reembolso-de-fatura
     *
     * @param string $invoiceId ID da fatura Iugu (ex.: "ABC123DEF456")
     */
    public function reembolso(string $invoiceId, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);

        $log = [
            "when"       => date("Y-m-d H:i:s"),
            "invoiceId"  => $invoiceId,
            "sandbox"    => $this->sandbox,
        ];

        if (defined("DIRETORIO")) {
            file_put_contents(
                DIRETORIO . "logs/iugu-reembolso.log",
                json_encode($log, JSON_UNESCAPED_UNICODE) . PHP_EOL,
                FILE_APPEND
            );
        }

        return $this->executa("/invoices/{$invoiceId}/refund", "POST");
    }

    /**
     * Cancela uma fatura (apenas faturas com status "pending" ou "draft").
     * Ref: https://dev.iugu.com/reference/cancelar-fatura
     */
    public function cancelarFatura(string $invoiceId, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        return $this->executa("/invoices/{$invoiceId}/cancel", "PUT");
    }

    // -------------------------------------------------------------------------
    // Recebedores / Marketplace (split)
    // -------------------------------------------------------------------------

    /**
     * Cria uma subconta (recebedor) no marketplace Iugu.
     * Ref: https://dev.iugu.com/reference/criar-conta-filho
     *
     * Campos $data esperados:
     *   nome           string
     *   email          string
     *   cpf            string CPF/CNPJ da empresa
     *   nome_empresa   string razão social (para PJ)
     *   nome_resp      string responsável (para PJ)
     *   telefone       string
     *   ddd            string
     */
    public function criarRecebedor(array $data, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);

        $isPj = strlen($this->onlyDigits($data["cpf"] ?? "")) === 14;

        $payload = [
            "name"              => (string)($data["nome"] ?? ""),
            "email"             => (string)($data["email"] ?? ""),
            "person_type"       => $isPj ? "Juridica" : "Fisica",
            "cpf_cnpj"          => $this->onlyDigits($data["cpf"] ?? ""),
            "company_name"      => $isPj ? (string)($data["nome_empresa"] ?? "") : null,
            "responsible_name"  => $isPj ? (string)($data["nome_resp"]    ?? "") : null,
            "phone"             => $this->onlyDigits($data["telefone"] ?? ""),
            "phone_prefix"      => (string)($data["ddd"] ?? ""),
            "resp_cpf"          => $isPj ? $this->onlyDigits($data["cpf_resp"] ?? "") : null,
        ];

        // Remove chaves nulas para não poluir o payload
        $payload = array_filter($payload, fn($v) => $v !== null && $v !== "");

        return $this->executa("/marketplace/create_account", "POST", $payload);
    }

    /**
     * Monta o array de splits para faturas no modelo marketplace Iugu.
     * Ref: https://dev.iugu.com/reference/split-de-pagamento
     *
     * @param array $comissoes  Array de ["usuario" => "ACC_ID", "comissao" => 50.00 (reais)]
     * @return array
     */
    public function buildSplit(array $comissoes): array
    {
        $splits = [];

        foreach ($comissoes as $i => $item) {
            $splits[] = [
                "cents"            => (int)round($item["comissao"] * 100),
                "recipient_account_id" => (string)$item["usuario"],
                "permit_charge_back"   => $i === 0, // somente o principal absorve chargeback
            ];
        }

        return $splits;
    }

    // -------------------------------------------------------------------------
    // Assinaturas (Recorrência)
    // -------------------------------------------------------------------------

    /**
     * Cria um plano de assinatura.
     * Ref: https://dev.iugu.com/reference/criar-plano
     *
     * Campos $data esperados:
     *   codigo         string  ex.: "plano-mensal"
     *   nome           string  ex.: "Plano Mensal"
     *   preco          int     valor em centavos
     *   intervalo      int     ex.: 1
     *   intervalo_tipo string  "months" | "weeks" | "days"
     *   metodo         string  "credit_card" | "bank_slip" | "pix" | "all"
     */
    public function criarPlano(array $data, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);

        $payload = [
            "name"             => (string)($data["nome"] ?? ""),
            "identifier"       => (string)($data["codigo"] ?? ""),
            "interval"         => (int)($data["intervalo"] ?? 1),
            "interval_type"    => (string)($data["intervalo_tipo"] ?? "months"),
            "currency"         => "BRL",
            "value_cents"      => (int)($data["preco"] ?? 0),
            "payable_with"     => (string)($data["metodo"] ?? "credit_card"),
        ];

        return $this->executa("/plans", "POST", $payload);
    }

    /**
     * Cria uma assinatura vinculada a um plano.
     * Ref: https://dev.iugu.com/reference/criar-assinatura
     *
     * Campos $data esperados:
     *   plan_identifier  string   identifier do plano
     *   customer_id      string   ID do cliente Iugu
     *   email            string   e-mail (se não tiver customer_id)
     *   credito_token    string   token do cartão (para débito automático)
     */
    public function criarAssinatura(array $data, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);

        $payload = [
            "plan_identifier"       => (string)($data["plan_identifier"] ?? ""),
            "customer_id"           => (string)($data["customer_id"] ?? ""),
            "email"                 => (string)($data["email"] ?? ""),
            "credits_based"         => false,
            "only_on_charge_success" => true,
        ];

        if (!empty($data["credito_token"])) {
            $payload["customer_payment_method_id"] = (string)$data["credito_token"];
        }

        $payload = array_filter($payload, fn($v) => $v !== null && $v !== "");

        return $this->executa("/subscriptions", "POST", $payload);
    }

    /**
     * Suspende uma assinatura.
     * Ref: https://dev.iugu.com/reference/suspender-assinatura
     */
    public function suspenderAssinatura(string $subscriptionId, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        return $this->executa("/subscriptions/{$subscriptionId}/suspend", "POST");
    }

    /**
     * Cancela uma assinatura.
     * Ref: https://dev.iugu.com/reference/cancelar-assinatura
     */
    public function cancelarAssinatura(string $subscriptionId, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        return $this->executa("/subscriptions/{$subscriptionId}", "DELETE");
    }
}

<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

/**
 * Stripe – Integração com a API v1
 *
 * Documentação oficial: https://docs.stripe.com/api
 *
 * Métodos implementados:
 *   compraCartao()         – PaymentIntent com cartão (+ split via Connect)
 *   compraPix()            – PaymentIntent via Pix (QR Code)
 *   estornoCartao()        – Reembolso via /v1/refunds
 *   criarContaConectada()  – POST /v1/accounts (Express, para split/marketplace)
 *   linkOnboarding()       – POST /v1/account_links (link de cadastro do vendedor)
 *   adicionarContaBancaria() – POST /v1/accounts/{id}/external_accounts
 *   saldo()                – GET /v1/balance (da conta conectada)
 *   saque()                – POST /v1/payouts (na conta conectada)
 *
 * Split de pagamento:
 *   Passa $data['destination'] = 'acct_xxx' e $data['application_fee_amount'] (centavos).
 *   Ref: https://docs.stripe.com/connect/destination-charges
 */
class Stripe
{
    private string $endpoint = "https://api.stripe.com/v1/";
    private string $token = "";

    // ID do registro na tabela configuracao_checkout para a Stripe
    private const CONFIG_ID = 9;

    public function __construct()
    {
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(self::CONFIG_ID);
        $configuracao         = (new ConfiguracaoCheckout())->findById(1);

        if ($configuracao->sandbox == 1) {
            $this->token = (string)($configuracaoCheckout->token_sandbox ?? "");
        } else {
            $this->token = (string)($configuracaoCheckout->token_producao ?? "");
        }
    }

    // -------------------------------------------------------------------------
    // Executores HTTP
    // -------------------------------------------------------------------------

    /**
     * Executor padrão (Bearer + form-urlencoded).
     * A Stripe usa application/x-www-form-urlencoded para todos os endpoints.
     */
    public function executa(string $url, string $metodo, ?string $postFields = null): object
    {
        return $this->executaComHeaders($url, $metodo, $postFields);
    }

    /**
     * Executor com suporte a headers extras (Idempotency-Key, Stripe-Account etc.).
     *
     * @param string      $url
     * @param string      $metodo
     * @param string|null $postFields   Corpo já serializado via http_build_query()
     * @param array       $extraHeaders Headers adicionais (ex.: Stripe-Account)
     */
    private function executaComHeaders(
        string  $url,
        string  $metodo,
        ?string $postFields = null,
        array   $extraHeaders = []
    ): object {
        $defaultHeaders = [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/x-www-form-urlencoded",
        ];
        $headers = array_values(array_unique(array_merge($defaultHeaders, $extraHeaders)));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $metodo,
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        $response = curl_exec($curl);
        error_log("[GatewayManager][PIX]{$response}'"); 
        $http     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $errNo    = curl_errno($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        // Log
        if (defined("DIRETORIO")) {
            $logFile = DIRETORIO . "logs/stripe.txt";
            if ($f = @fopen($logFile, "a+")) {
                fwrite($f, "=== " . date("d/m/Y H:i:s") . " ===\n");
                fwrite($f, "URL: {$url}\nMETHOD: {$metodo}\nHTTP: {$http}\n");
                if ($postFields) fwrite($f, "BODY: {$postFields}\n");
                fwrite($f, "RESP: {$response}\n");
                if ($err)        fwrite($f, "CURL_ERR: #{$errNo} {$err}\n");
                fwrite($f, "-------------------------------\n");
                fclose($f);
            }
        }

        $obj = json_decode((string)$response);
        if (!is_object($obj)) {
            $obj = (object)["raw" => $response];
        }
        $obj->_http = $http;

        return $obj;
    }

    // -------------------------------------------------------------------------
    // Cobrança – Cartão de Crédito
    // -------------------------------------------------------------------------

    /**
     * Cria um PaymentIntent para cartão de crédito e o confirma imediatamente.
     *
     * Aceita token do frontend (tok_… via Stripe.js) ou dados brutos do cartão.
     * Suporta parcelamento e split via Stripe Connect.
     *
     * Ref: https://docs.stripe.com/api/payment_intents/create
     *
     * Campos $data esperados:
     *   preco                  int    valor em centavos
     *   MPHiddenInputToken     string token tok_… gerado pelo Stripe.js (preferível)
     *   numcart / mes-val / ano-val / cvv  – dados brutos (fallback)
     *   titular                string nome impresso no cartão
     *   parcela                int    número de parcelas (default: 1)
     *   nome / email           string billing details
     *   cpf                    string CPF/CNPJ do pagador
     *   destination            string acct_xxx da conta conectada (split)
     *   application_fee_amount int    taxa da plataforma em centavos (split)
     *   nome_fatura            string texto no extrato (statement_descriptor)
     */
    public function compraCartao(array $data): object
    {
        $url   = $this->endpoint . "payment_intents";
        $valor = (int)($data["preco"] ?? 0);

        // --- Token do cartão (frontend via Stripe.js)
        $token = $data["MPHiddenInputToken"]
            ?? $data["stripe-token"]
            ?? $data["stripeToken"]
            ?? null;

        // --- Monta corpo base do PaymentIntent
        $body = [
            "amount"                    => $valor,
            "currency"                  => "brl",
            "confirmation_method"       => "automatic",
            "confirm"                   => "true",
            "payment_method_types[]"    => "card",
            "payment_method_data[type]" => "card",
            // Billing details (nome e e-mail do pagador)
            "payment_method_data[billing_details][name]"  => trim((string)($data["nome"]  ?? "")),
            "payment_method_data[billing_details][email]" => (string)($data["email"] ?? ""),
        ];

        // --- Dados do cartão
        if ($token) {
            $body["payment_method_data[card][token]"] = $token;
        } elseif (!empty($data["numcart"]) && !empty($data["cvv"])) {
            $numero = preg_replace("/\D+/", "", (string)$data["numcart"]);
            $mes    = str_pad(preg_replace("/\D+/", "", (string)($data["mes-val"] ?? $data["mes_val"] ?? "")), 2, "0", STR_PAD_LEFT);
            $ano    = preg_replace("/\D+/", "", (string)($data["ano-val"] ?? $data["ano_val"] ?? ""));
            if (strlen($ano) === 2) {
                $ano = "20" . $ano;
            }

            $body["payment_method_data[card][number]"]    = $numero;
            $body["payment_method_data[card][exp_month]"] = $mes;
            $body["payment_method_data[card][exp_year]"]  = $ano;
            $body["payment_method_data[card][cvc]"]       = preg_replace("/\D+/", "", (string)$data["cvv"]);
        } else {
            return (object)[
                "error" => (object)["message" => "Dados do cartão ausentes."],
            ];
        }

        // --- Parcelamento (Stripe Brasil)
        // Ref: https://docs.stripe.com/payments/installments
        $parcelas = (int)($data["parcela"] ?? 1);
        if ($parcelas > 1) {
            $body["payment_method_options[card][installments][enabled]"]              = "true";
            $body["payment_method_options[card][installments][plan][type]"]           = "fixed_count";
            $body["payment_method_options[card][installments][plan][count]"]          = (string)$parcelas;
            $body["payment_method_options[card][installments][plan][interval]"]       = "month";
        }

        // --- Texto no extrato do cartão (máx. 22 chars)
        if (!empty($data["nome_fatura"])) {
            $body["statement_descriptor"] = substr((string)$data["nome_fatura"], 0, 22);
        }

        // --- Split via Stripe Connect
        // Ref: https://docs.stripe.com/connect/destination-charges
        if (!empty($data["destination"])) {
            $body["transfer_data[destination]"] = (string)$data["destination"];
        }
        if (isset($data["application_fee_amount"])) {
            $body["application_fee_amount"] = (int)$data["application_fee_amount"];
        }

        $idempotencyKey = bin2hex(random_bytes(16));

        return $this->executaComHeaders(
            $url,
            "POST",
            http_build_query($body),
            ["Idempotency-Key: {$idempotencyKey}"]
        );
    }

    // -------------------------------------------------------------------------
    // Cobrança – Pix
    // -------------------------------------------------------------------------

    /**
     * Cria um PaymentIntent via Pix e retorna o QR Code para exibição.
     *
     * A resposta inclui next_action.pix_display_qr_code com:
     *   - data              → string Pix (copia-e-cola)
     *   - image_url_png     → URL da imagem PNG do QR code
     *   - image_url_svg     → URL da imagem SVG do QR code
     *   - expires_at        → unix timestamp de expiração
     *   - hosted_instructions_url → página Stripe para o pagador
     *
     * Ref: https://docs.stripe.com/payments/pix
     *
     * Campos $data esperados:
     *   preco               int    valor em centavos
     *   nome                string nome do pagador
     *   email               string e-mail do pagador
     *   cpf                 string CPF ou CNPJ do pagador
     *   expires_after       int    segundos até expirar (default: 3600 = 1h; max: 259200 = 3 dias)
     *   return_url          string URL de retorno após pagamento confirmado
     *   destination         string acct_xxx da conta conectada (split – opcional)
     *   application_fee_amount int taxa da plataforma (split – opcional)
     */
    public function compraPix(array $data): object
    {
        $url   = $this->endpoint . "payment_intents";
        $valor = (int)($data["preco"] ?? 0);

        if ($valor <= 0) {
            return (object)["error" => (object)["message" => "Valor (preco) inválido."]];
        }
        if (empty($data["nome"]) || empty($data["email"])) {
            return (object)["error" => (object)["message" => "nome e email são obrigatórios para Pix."]];
        }

        $returnUrl      = $data["return_url"]
            ?? (defined("URL_BASE") ? URL_BASE . "/checkout/pix-sucesso" : "https://seusite.com.br/sucesso");
        $expiresAfter   = (int)($data["expires_after"] ?? 3600); // 1 hora default
        $cpfCnpj        = preg_replace("/\D+/", "", (string)($data["cpf"] ?? ""));

        $body = [
            "amount"                    => $valor,
            "currency"                  => "brl",
            "confirm"                   => "true",
            "payment_method_types[]"    => "pix",
            "payment_method_data[type]" => "pix",
            // Billing details do pagador (obrigatório pelo BCB para Pix)
            "payment_method_data[billing_details][name]"  => trim((string)$data["nome"]),
            "payment_method_data[billing_details][email]" => (string)$data["email"],
            // Expiração do QR Code
            "payment_method_options[pix][expires_after_seconds]" => $expiresAfter,
            // URL de retorno (obrigatório ao confirmar server-side)
            "return_url" => $returnUrl,
        ];

        // CPF/CNPJ (tax ID – exigido pelo Banco Central para Pix no Brasil)
        if (!empty($cpfCnpj)) {
            $body["payment_method_data[billing_details][tax_id]"] = $cpfCnpj;
        }

        // --- Split via Stripe Connect (opcional)
        if (!empty($data["destination"])) {
            $body["transfer_data[destination]"] = (string)$data["destination"];
        }
        if (isset($data["application_fee_amount"])) {
            $body["application_fee_amount"] = (int)$data["application_fee_amount"];
        }

        $idempotencyKey = bin2hex(random_bytes(16));

        return $this->executaComHeaders(
            $url,
            "POST",
            http_build_query($body),
            ["Idempotency-Key: {$idempotencyKey}"]
        );
    }

    // -------------------------------------------------------------------------
    // Reembolso / Estorno
    // -------------------------------------------------------------------------

    /**
     * Estorna uma cobrança (parcial ou total).
     *
     * Ref: https://docs.stripe.com/api/refunds/create
     *
     * @param array $data  Requer $data["transacao"] = PaymentIntent ID (pi_xxx) ou Charge ID (ch_xxx)
     *                     Opcional: $data["valor"] em centavos para estorno parcial
     */
    public function estornoCartao(array $data): object
    {
        $url = $this->endpoint . "refunds";

        $body = [
            "charge" => (string)($data["transacao"] ?? ""),
        ];

        // Estorno parcial: informar valor em centavos
        if (!empty($data["valor"])) {
            $body["amount"] = (int)$data["valor"];
        }

        // Se vier um PaymentIntent (pi_xxx) em vez de charge (ch_xxx)
        if (!empty($data["payment_intent"])) {
            unset($body["charge"]);
            $body["payment_intent"] = (string)$data["payment_intent"];
        }

        return $this->executa($url, "POST", http_build_query($body));
    }

    // -------------------------------------------------------------------------
    // Stripe Connect – Contas Conectadas (split / marketplace)
    // -------------------------------------------------------------------------

    /**
     * Cria uma conta conectada do tipo Express (recomendado para Brasil).
     *
     * O vendedor recebe um link de onboarding (via linkOnboarding()) para
     * completar o cadastro no painel Stripe. Após verificação, o acct_xxx
     * pode receber pagamentos via transfer_data[destination].
     *
     * Ref: https://docs.stripe.com/connect/express-accounts
     * Ref: https://docs.stripe.com/api/accounts/create
     *
     * Campos $data esperados:
     *   email           string  e-mail do vendedor (obrigatório)
     *   nome            string  nome completo (individual) ou razão social (empresa)
     *   cpf             string  CPF (11 dígitos) → individual | CNPJ (14 dígitos) → company
     *   tipo            string  "express" | "standard" (default: "express")
     *   ddd / telefone  string  telefone do responsável
     *   nascimento_dia / nascimento_mes / nascimento_ano  – data de nascimento (individual)
     *   rua / numero / cidade / estado / cep  – endereço
     */
    public function criarContaConectada(array $data): object
    {
        $url = $this->endpoint . "accounts";

        $cpfCnpj      = preg_replace("/\D+/", "", (string)($data["cpf"] ?? ""));
        $businessType = strlen($cpfCnpj) === 14 ? "company" : "individual";
        $tipo         = in_array($data["tipo"] ?? "", ["standard", "custom"]) ? $data["tipo"] : "express";
        $cep          = preg_replace("/\D+/", "", (string)($data["cep"] ?? ""));

        $body = [
            "type"          => $tipo,
            "country"       => "BR",
            "email"         => (string)($data["email"] ?? ""),
            "business_type" => $businessType,
            // Capabilities necessárias para receber pagamentos no Brasil
            "capabilities[card_payments][requested]"     => "true",
            "capabilities[transfers][requested]"         => "true",
        ];

        if ($businessType === "individual") {
            $nomePartes = explode(" ", trim((string)($data["nome"] ?? "")), 2);
            $body["individual[first_name]"]         = $nomePartes[0] ?? "";
            $body["individual[last_name]"]          = $nomePartes[1] ?? $nomePartes[0] ?? "";
            $body["individual[email]"]              = (string)($data["email"] ?? "");
            $body["individual[id_number]"]          = $cpfCnpj; // CPF

            if (!empty($data["nascimento_dia"])) {
                $body["individual[dob][day]"]   = (int)$data["nascimento_dia"];
                $body["individual[dob][month]"] = (int)$data["nascimento_mes"];
                $body["individual[dob][year]"]  = (int)$data["nascimento_ano"];
            }

            if (!empty($data["rua"])) {
                $body["individual[address][line1]"]       = trim($data["rua"] . ", " . ($data["numero"] ?? ""));
                $body["individual[address][city]"]        = (string)($data["cidade"] ?? "");
                $body["individual[address][state]"]       = (string)($data["estado"] ?? "");
                $body["individual[address][postal_code]"] = $cep;
                $body["individual[address][country]"]     = "BR";
            }

            if (!empty($data["ddd"]) && !empty($data["telefone"])) {
                $fone = preg_replace("/\D+/", "", (string)$data["telefone"]);
                $body["individual[phone]"] = "+55" . $data["ddd"] . $fone;
            }
        } else {
            // Empresa
            $body["company[name]"]                  = (string)($data["nome"] ?? "");
            $body["company[tax_id]"]                = $cpfCnpj; // CNPJ

            if (!empty($data["rua"])) {
                $body["company[address][line1]"]       = trim($data["rua"] . ", " . ($data["numero"] ?? ""));
                $body["company[address][city]"]        = (string)($data["cidade"] ?? "");
                $body["company[address][state]"]       = (string)($data["estado"] ?? "");
                $body["company[address][postal_code]"] = $cep;
                $body["company[address][country]"]     = "BR";
            }

            if (!empty($data["ddd"]) && !empty($data["telefone"])) {
                $fone = preg_replace("/\D+/", "", (string)$data["telefone"]);
                $body["company[phone]"] = "+55" . $data["ddd"] . $fone;
            }
        }

        return $this->executa($url, "POST", http_build_query($body));
    }

    /**
     * Gera o link de onboarding para o vendedor completar o cadastro Express.
     * Deve ser chamado logo após criarContaConectada() e o link enviado ao vendedor.
     *
     * Ref: https://docs.stripe.com/api/account_links/create
     *
     * @param string $accountId   ID da conta conectada (acct_xxx)
     * @param string $refreshUrl  URL caso o link expire e o vendedor precise de um novo
     * @param string $returnUrl   URL após conclusão do onboarding
     */
    public function linkOnboarding(string $accountId, string $refreshUrl = "", string $returnUrl = ""): object
    {
        $url = $this->endpoint . "account_links";

        $refreshUrl = $refreshUrl ?: (defined("URL_BASE") ? URL_BASE . "/vendedor/onboarding/reenviar" : "");
        $returnUrl  = $returnUrl  ?: (defined("URL_BASE") ? URL_BASE . "/vendedor/onboarding/concluido" : "");

        $body = [
            "account"     => $accountId,
            "refresh_url" => $refreshUrl,
            "return_url"  => $returnUrl,
            "type"        => "account_onboarding",
        ];

        return $this->executa($url, "POST", http_build_query($body));
    }

    /**
     * Adiciona uma conta bancária (para saque) a uma conta conectada.
     *
     * Ref: https://docs.stripe.com/api/external_account_bank_accounts/create
     *
     * Campos $data esperados:
     *   account_holder_name  string
     *   account_holder_type  string  "individual" | "company"
     *   account_number       string  número da conta
     *   routing_number       string  código do banco (ISPB ou equivalente)
     */
    public function adicionarContaBancaria(string $accountId, array $data): object
    {
        $url = $this->endpoint . "accounts/{$accountId}/external_accounts";

        $body = [
            "external_account[object]"              => "bank_account",
            "external_account[country]"             => "BR",
            "external_account[currency]"            => "brl",
            "external_account[account_holder_name]" => (string)($data["account_holder_name"] ?? ""),
            "external_account[account_holder_type]" => (string)($data["account_holder_type"] ?? "individual"),
            "external_account[account_number]"      => (string)($data["account_number"] ?? ""),
            "external_account[routing_number]"      => (string)($data["routing_number"] ?? ""),
        ];

        return $this->executa($url, "POST", http_build_query($body));
    }

    // -------------------------------------------------------------------------
    // Saldo e Saque (conta conectada)
    // -------------------------------------------------------------------------

    /**
     * Consulta o saldo de uma conta conectada.
     * Usa o header "Stripe-Account" para operar em nome da conta.
     *
     * Ref: https://docs.stripe.com/api/balance/balance_retrieve
     *
     * @param string $accountId  acct_xxx da conta conectada
     */
    public function saldo(string $accountId): object
    {
        $url = $this->endpoint . "balance";

        return $this->executaComHeaders(
            $url,
            "GET",
            null,
            ["Stripe-Account: {$accountId}"]
        );
    }

    /**
     * Solicita um saque (payout) para a conta bancária da conta conectada.
     * O valor é debitado do saldo disponível da conta conectada.
     *
     * Ref: https://docs.stripe.com/api/payouts/create
     *
     * @param string $accountId  acct_xxx da conta conectada
     * @param int    $amount     Valor em centavos a sacar
     */
    public function saque(string $accountId, int $amount): object
    {
        $url = $this->endpoint . "payouts";

        $body = [
            "amount"   => $amount,
            "currency" => "brl",
        ];

        return $this->executaComHeaders(
            $url,
            "POST",
            http_build_query($body),
            ["Stripe-Account: {$accountId}"]
        );
    }

    // -------------------------------------------------------------------------
    // Consultas
    // -------------------------------------------------------------------------

    /**
     * Consulta um PaymentIntent pelo ID.
     * Ref: https://docs.stripe.com/api/payment_intents/retrieve
     */
    public function consultarPagamento(string $paymentIntentId): object
    {
        $url = $this->endpoint . "payment_intents/{$paymentIntentId}";
        return $this->executa($url, "GET");
    }

    /**
     * Consulta uma conta conectada pelo ID.
     * Ref: https://docs.stripe.com/api/accounts/retrieve
     */
    public function consultarConta(string $accountId): object
    {
        $url = $this->endpoint . "accounts/{$accountId}";
        return $this->executa($url, "GET");
    }
}

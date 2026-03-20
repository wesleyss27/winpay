<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

/**
 * EfiBank (Efí Pay / Gerencianet) – Integração com as APIs v1 e Pix v2
 *
 * Documentação oficial: https://dev.efipay.com.br/docs/api-pix/credenciais
 *
 * Dois contextos de API:
 *   Pix API    → https://pix.api.efipay.com.br         (requer mTLS com certificado .pem)
 *   Cobrança   → https://cobrancas.api.efipay.com.br/v1 (OAuth2 padrão)
 *
 * Credenciais (ConfiguracaoCheckout id=10):
 *   chave_producao  → {client_id}|{chave_pix}    ex.: "Client_Id_xxxx|seu@email.com"
 *   token_producao  → client_secret de produção
 *   chave_sandbox   → {client_id}|{chave_pix}    sandbox
 *   token_sandbox   → client_secret sandbox
 *   sandbox         → 0=produção | 1=sandbox
 *
 * Certificado mTLS (necessário para Pix):
 *   Produção: {DIRETORIO}/certs/efibank_prod.pem   (openssl pkcs12 -in cert.p12 -out cert.pem -nodes)
 *   Sandbox:  {DIRETORIO}/certs/efibank_sandbox.pem
 */
class EfiBank
{
    private string $clientId     = "";
    private string $clientSecret = "";
    private string $chavePix     = "";   // e-mail, CPF, CNPJ ou telefone cadastrado como chave Pix
    private string $certPath     = "";   // caminho para o .pem (mTLS)
    private bool   $sandbox      = false;

    // URLs base
    private string $pixUrl      = "";
    private string $cobrancaUrl = "";

    private const CONFIG_ID = 10;

    public function __construct()
    {
        // construtor leve — credenciais carregadas em dados()
    }

    // -------------------------------------------------------------------------
    // Credenciais
    // -------------------------------------------------------------------------

    public function dados($adquirenteId = null): void
    {
        $id         = $adquirenteId ?? self::CONFIG_ID;
        $adquirente = (new ConfiguracaoCheckout())->findById($id);

        if (!$adquirente) {
            throw new \Exception("[EfiBank] Configuração (id={$id}) não encontrada.");
        }

        $this->sandbox = (bool)($adquirente->sandbox ?? false);

        $chaveRaw      = $this->sandbox
            ? (string)($adquirente->chave_sandbox  ?? "")
            : (string)($adquirente->chave_producao ?? "");
        $this->clientSecret = $this->sandbox
            ? (string)($adquirente->token_sandbox  ?? "")
            : (string)($adquirente->token_producao ?? "");

        // chave_producao armazena "client_id|chave_pix"
        [$this->clientId, $this->chavePix] = array_pad(explode("|", $chaveRaw, 2), 2, "");

        $this->pixUrl      = $this->sandbox
            ? "https://pix-h.api.efipay.com.br"
            : "https://pix.api.efipay.com.br";

        $this->cobrancaUrl = $this->sandbox
            ? "https://sandbox.gerencianet.com.br/v1"
            : "https://cobrancas.api.efipay.com.br/v1";

        // Caminho do certificado mTLS para a Pix API
        if (defined("DIRETORIO")) {
            $this->certPath = DIRETORIO . "certs/efibank_" . ($this->sandbox ? "sandbox" : "prod") . ".pem";
        }

        if (empty($this->clientId) || empty($this->clientSecret)) {
            throw new \Exception("[EfiBank] client_id ou client_secret não configurados.");
        }
    }

    // -------------------------------------------------------------------------
    // Autenticação OAuth2
    // -------------------------------------------------------------------------

    /**
     * Obtém access_token via client_credentials.
     * Para a Pix API usa mTLS (certificado .pem obrigatório).
     * Para a Cobrança API não é necessário certificado.
     *
     * Ref: https://dev.efipay.com.br/docs/api-pix/credenciais
     */
    private function getAccessToken(bool $usarCert = false): string
    {
        $baseUrl  = $usarCert ? $this->pixUrl : $this->cobrancaUrl;
        $authUrl  = $baseUrl . "/oauth/token";
        $basic    = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $ch = curl_init();
        $opts = [
            CURLOPT_URL            => $authUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode(["grant_type" => "client_credentials"]),
            CURLOPT_HTTPHEADER     => [
                "Authorization: Basic {$basic}",
                "Content-Type: application/json",
            ],
            CURLOPT_TIMEOUT        => 20,
        ];

        // mTLS obrigatório para a Pix API
        if ($usarCert && !empty($this->certPath) && file_exists($this->certPath)) {
            $opts[CURLOPT_SSLCERT]       = $this->certPath;
            $opts[CURLOPT_SSLKEY]        = $this->certPath;
            $opts[CURLOPT_SSL_VERIFYPEER] = true;
        } elseif ($usarCert) {
            error_log("[EfiBank] Certificado mTLS não encontrado em: {$this->certPath}");
        }

        curl_setopt_array($ch, $opts);

        $resp  = curl_exec($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errNo = curl_errno($ch);
        $err   = curl_error($ch);
        curl_close($ch);

        if ($errNo) {
            throw new \Exception("[EfiBank][Auth] Erro cURL #{$errNo}: {$err}");
        }

        $decoded = json_decode((string)$resp, true);
        $token   = $decoded["access_token"] ?? null;

        if (empty($token)) {
            error_log("[EfiBank][Auth] HTTP {$http} Resp: {$resp}");
            throw new \Exception("[EfiBank] Falha ao obter access_token. HTTP {$http}");
        }

        return $token;
    }

    // -------------------------------------------------------------------------
    // Executor HTTP
    // -------------------------------------------------------------------------

    /**
     * Executa requisição autenticada.
     *
     * @param string      $url       URL completa
     * @param string      $metodo    GET | POST | PUT | PATCH | DELETE
     * @param array|null  $payload   Corpo da requisição (será codificado em JSON)
     * @param string      $token     Bearer token
     * @param bool        $usarCert  Aplica mTLS (necessário para Pix API)
     */
    private function executa(string $url, string $metodo, ?array $payload, string $token, bool $usarCert = false): object
    {
        $json = $payload !== null ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;

        $ch = curl_init();
        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $metodo,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json",
                "Accept: application/json",
            ],
        ];

        if ($usarCert && !empty($this->certPath) && file_exists($this->certPath)) {
            $opts[CURLOPT_SSLCERT]        = $this->certPath;
            $opts[CURLOPT_SSLKEY]         = $this->certPath;
            $opts[CURLOPT_SSL_VERIFYPEER] = true;
        }

        curl_setopt_array($ch, $opts);

        $resp  = curl_exec($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errNo = curl_errno($ch);
        $err   = curl_error($ch);
        curl_close($ch);

        error_log("[EfiBank] {$metodo} {$url} → HTTP {$http}");
        if ($errNo) {
            throw new \Exception("[EfiBank] Erro cURL #{$errNo}: {$err}");
        }

        $decoded = json_decode((string)$resp);
        if ($decoded === null) {
            error_log("[EfiBank] Resposta inválida: {$resp}");
            throw new \Exception("[EfiBank] Resposta não-JSON recebida (HTTP {$http}).");
        }

        return $decoded;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function onlyDigits(?string $v): string
    {
        return $v ? preg_replace('/\D+/', '', $v) : "";
    }

    /** Gera txid único para cobranças Pix (26-35 alfanuméricos) */
    private function txid(): string
    {
        return substr(preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(random_bytes(32))), 0, 35);
    }

    /**
     * Constrói array de repasses para a Charges API (boleto/cartão).
     *
     * Cada entrada em $comissoes deve conter:
     *   efibank_payee_code  string  Código payee_code da conta EfiBank do recebedor
     *   comissao            float   Valor em reais
     *
     * Retorna null se nenhuma entrada tiver payee_code válido.
     *
     * Ref: https://dev.efipay.com.br/docs/api-cobrancas/marketplace
     */
    private function buildSplitCharges(array $comissoes): ?array
    {
        $repasses = [];

        foreach ($comissoes as $comissao) {
            $payeeCode = trim((string)($comissao["efibank_payee_code"] ?? ""));
            if (empty($payeeCode)) {
                continue;
            }
            $fixed = (int)round((float)$comissao["comissao"] * 100);
            if ($fixed <= 0) {
                continue;
            }
            $repasses[] = [
                "payee_code" => $payeeCode,
                "fixed"      => $fixed,
            ];
        }

        return empty($repasses) ? null : $repasses;
    }

    /**
     * Constrói o bloco de split para a Pix API.
     *
     * Cada entrada em $comissoes deve conter:
     *   efibank_payee_code  string  Chave Pix (e-mail, CPF, telefone ou chave aleatória) do recebedor
     *   comissao            float   Valor em reais
     *
     * Retorna null se nenhuma entrada tiver chave válida.
     *
     * Ref: https://dev.efipay.com.br/docs/api-pix/split-de-pagamento-pix
     */
    private function buildSplitPix(array $comissoes): ?array
    {
        $repasses = [];

        foreach ($comissoes as $comissao) {
            $chave = trim((string)($comissao["efibank_payee_code"] ?? ""));
            if (empty($chave)) {
                continue;
            }
            $valor = number_format((float)$comissao["comissao"], 2, '.', '');
            if ((float)$valor <= 0) {
                continue;
            }
            $repasses[] = [
                "conta" => ["chave" => $chave],
                "tipo"  => "valor",
                "valor" => $valor,
            ];
        }

        if (empty($repasses)) {
            return null;
        }

        return [
            "divisaoTarifa" => "remetente",
            "repasses"      => $repasses,
        ];
    }

    // -------------------------------------------------------------------------
    // Pix
    // -------------------------------------------------------------------------

    /**
     * Cria cobrança Pix imediata (cob).
     *
     * A resposta inclui:
     *   pixCopiaECola  → string Pix copia-e-cola
     *   loc.id         → ID da location (use para buscar QR code image)
     *   status         → "ATIVA"
     *
     * Para obter a imagem do QR code: GET /v2/loc/{id}/qrcode
     * Ref: https://dev.efipay.com.br/docs/api-pix/cobrancas-imediatas
     *
     * Campos $data esperados:
     *   preco         int    valor em centavos
     *   nome          string nome do devedor
     *   cpf           string CPF (11 dígitos) ou CNPJ (14)
     *   expires_after int    expiração em segundos (default: 3600)
     *   postbackUrl   string URL do webhook (opcional)
     */
    public function compraPix(array $data): object
    {
        $this->dados($data["adquirente"] ?? null);

        if (empty($data["preco"])) {
            throw new \Exception("[EfiBank][Pix] Campo 'preco' obrigatório (centavos).");
        }
        if (empty($data["nome"]) || empty($data["cpf"])) {
            throw new \Exception("[EfiBank][Pix] Campos obrigatórios: nome, cpf.");
        }
        if (empty($this->chavePix)) {
            throw new \Exception("[EfiBank][Pix] Chave Pix não configurada (campo chave_producao: 'client_id|chave_pix').");
        }

        $token     = $this->getAccessToken(usarCert: true);
        $cpfCnpj   = $this->onlyDigits($data["cpf"] ?? "");
        $valor     = number_format((int)$data["preco"] / 100, 2, '.', '');
        $expiracao = (int)($data["expires_after"] ?? 3600);
        $txid      = $this->txid();

        $devedor = strlen($cpfCnpj) === 14
            ? ["cnpj" => $cpfCnpj, "nome" => trim((string)$data["nome"])]
            : ["cpf"  => $cpfCnpj, "nome" => trim((string)$data["nome"])];

        $valorBloco = ["original" => $valor];
        if (!empty($data["comissoes_pagamento"])) {
            $splitPix = $this->buildSplitPix((array)$data["comissoes_pagamento"]);
            if ($splitPix !== null) {
                $valorBloco["split"] = $splitPix;
            }
        }

        $payload = [
            "calendario"          => ["expiracao" => $expiracao],
            "devedor"             => $devedor,
            "valor"               => $valorBloco,
            "chave"               => $this->chavePix,
            "solicitacaoPagador"  => (string)($data["nomekit"] ?? $data["nome_produto"] ?? "Pagamento"),
        ];

        // Webhook de notificação (registro separado, mas inclui na criação quando suportado)
        $resultado = $this->executa(
            "{$this->pixUrl}/v2/cob/{$txid}",
            "PUT",
            $payload,
            $token,
            usarCert: true
        );

        // Enriquece com imagem do QR code se location estiver disponível
        if (!empty($resultado->loc->id)) {
            try {
                $qrCodeResp = $this->executa(
                    "{$this->pixUrl}/v2/loc/{$resultado->loc->id}/qrcode",
                    "GET",
                    null,
                    $token,
                    usarCert: true
                );
                $resultado->qrCodeImagem = $qrCodeResp->imagemQrcode ?? null;
            } catch (\Throwable $e) {
                error_log("[EfiBank][Pix] Falha ao buscar imagem QR: " . $e->getMessage());
            }
        }

        return $resultado;
    }

    /**
     * Registra/atualiza webhook Pix para uma chave.
     * Ref: https://dev.efipay.com.br/docs/api-pix/gerenciar-webhooks
     */
    public function registrarWebhookPix(string $webhookUrl, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        $token = $this->getAccessToken(usarCert: true);

        return $this->executa(
            "{$this->pixUrl}/v2/webhook/{$this->chavePix}",
            "PUT",
            ["webhookUrl" => $webhookUrl],
            $token,
            usarCert: true
        );
    }

    // -------------------------------------------------------------------------
    // Boleto Bancário
    // -------------------------------------------------------------------------

    /**
     * Gera boleto em dois passos:
     *   1. Cria cobrança (POST /v1/charge)
     *   2. Define pagamento como boleto (POST /v1/charge/{id}/billet)
     *
     * A resposta final contém:
     *   data.link       → URL do boleto
     *   data.barcode    → código de barras
     *
     * Ref: https://dev.efipay.com.br/docs/api-cobrancas/boleto-bancario
     *
     * Campos $data esperados:
     *   preco        int    valor em centavos
     *   nome         string nome do pagador
     *   email        string e-mail
     *   cpf          string CPF/CNPJ
     *   ddd          string DDD
     *   telefone     string número
     *   vencimento   string "YYYY-MM-DD"
     *   nomekit      string descrição do produto
     */
    public function compraBoleto(array $data): object
    {
        $this->dados($data["adquirente"] ?? null);

        $token     = $this->getAccessToken(usarCert: false);
        $cpfCnpj   = $this->onlyDigits($data["cpf"] ?? "");
        $fone      = $this->onlyDigits(($data["ddd"] ?? "") . ($data["telefone"] ?? ""));
        $vencimento = $data["vencimento"] ?? date("Y-m-d", strtotime("+3 days"));

        // --- 1. Cria cobrança
        $item = [
            "name"   => (string)($data["nomekit"] ?? "Produto"),
            "value"  => (int)$data["preco"],
            "amount" => 1,
        ];

        if (!empty($data["comissoes_pagamento"])) {
            $repasses = $this->buildSplitCharges((array)$data["comissoes_pagamento"]);
            if ($repasses !== null) {
                $item["marketplace"] = ["repasses" => $repasses];
            }
        }

        $chargePayload = ["items" => [$item]];

        $charge = $this->executa("{$this->cobrancaUrl}/charge", "POST", $chargePayload, $token);

        if (empty($charge->data->charge_id)) {
            error_log("[EfiBank][Boleto] Falha ao criar cobrança: " . json_encode($charge));
            throw new \Exception("[EfiBank][Boleto] Falha ao criar cobrança.");
        }

        $chargeId = (int)$charge->data->charge_id;

        // --- 2. Define pagamento como boleto
        $isPj = strlen($cpfCnpj) === 14;

        $billetPayload = [
            "payment" => [
                "banking_billet" => [
                    "expire_at" => $vencimento,
                    "customer"  => array_filter([
                        "name"         => trim((string)$data["nome"]),
                        "email"        => (string)($data["email"] ?? ""),
                        "phone_number" => $fone ?: null,
                        $isPj ? "juridical_person" : "cpf" => $isPj
                            ? ["corporate_name" => trim((string)$data["nome"]), "cnpj" => $cpfCnpj]
                            : $cpfCnpj,
                    ], fn($v) => $v !== null && $v !== ""),
                ],
            ],
        ];

        return $this->executa("{$this->cobrancaUrl}/charge/{$chargeId}/billet", "POST", $billetPayload, $token);
    }

    // -------------------------------------------------------------------------
    // Cartão de Crédito
    // -------------------------------------------------------------------------

    /**
     * Realiza cobrança via cartão de crédito em dois passos:
     *   1. Cria cobrança (POST /v1/charge)
     *   2. Paga com cartão (POST /v1/charge/{id}/card)
     *
     * IMPORTANTE: EfiBank requer payment_token gerado pelo SDK JavaScript
     * (script: https://js.gerencianet.com.br/v1). O token vem do front-end
     * como $data["payment_token"] ou $data["MPHiddenInputToken"].
     *
     * Ref: https://dev.efipay.com.br/docs/api-cobrancas/cartao-de-credito
     *
     * Campos $data esperados:
     *   preco           int    valor em centavos
     *   payment_token   string token gerado pelo Gerencianet.js
     *   nome            string nome do comprador
     *   email           string
     *   cpf             string CPF/CNPJ
     *   ddd             string
     *   telefone        string
     *   parcela         int    número de parcelas (default: 1)
     *   rua / numero / bairro / cidade / estado / cep  → endereço de cobrança
     */
    public function compraCartao(array $data): object
    {
        $this->dados($data["adquirente"] ?? null);

        $paymentToken = (string)($data["payment_token"]
            ?? $data["MPHiddenInputToken"]
            ?? $data["efibank_token"]
            ?? "");

        if (empty($paymentToken)) {
            throw new \Exception("[EfiBank][Cartão] payment_token obrigatório. Gere via Gerencianet.js no frontend.");
        }

        $token     = $this->getAccessToken(usarCert: false);
        $cpfCnpj   = $this->onlyDigits($data["cpf"] ?? "");
        $fone      = $this->onlyDigits(($data["ddd"] ?? "") . ($data["telefone"] ?? ""));
        $parcelas  = max(1, (int)($data["parcela"] ?? 1));
        $isPj      = strlen($cpfCnpj) === 14;
        $cep       = $this->onlyDigits($data["cep"] ?? "");

        // --- 1. Cria cobrança
        $item = [
            "name"   => (string)($data["nomekit"] ?? "Produto"),
            "value"  => (int)$data["preco"],
            "amount" => 1,
        ];

        if (!empty($data["comissoes_pagamento"])) {
            $repasses = $this->buildSplitCharges((array)$data["comissoes_pagamento"]);
            if ($repasses !== null) {
                $item["marketplace"] = ["repasses" => $repasses];
            }
        }

        $chargePayload = ["items" => [$item]];

        $charge = $this->executa("{$this->cobrancaUrl}/charge", "POST", $chargePayload, $token);

        if (empty($charge->data->charge_id)) {
            error_log("[EfiBank][Cartão] Falha ao criar cobrança: " . json_encode($charge));
            throw new \Exception("[EfiBank][Cartão] Falha ao criar cobrança.");
        }

        $chargeId = (int)$charge->data->charge_id;

        // --- 2. Paga com cartão
        $customer = array_filter([
            "name"         => trim((string)$data["nome"]),
            "email"        => (string)($data["email"] ?? ""),
            "phone_number" => $fone ?: null,
            $isPj ? "juridical_person" : "cpf" => $isPj
                ? ["corporate_name" => trim((string)$data["nome"]), "cnpj" => $cpfCnpj]
                : $cpfCnpj,
        ], fn($v) => $v !== null && $v !== "");

        $billingAddress = [
            "street"      => (string)($data["rua"]    ?? ""),
            "number"      => (string)($data["numero"] ?? ""),
            "neighborhood"=> (string)($data["bairro"] ?? ""),
            "city"        => (string)($data["cidade"] ?? ""),
            "state"       => strtoupper((string)($data["estado"] ?? "")),
            "zipcode"     => $cep,
        ];

        $cardPayload = [
            "payment" => [
                "credit_card" => [
                    "installments"    => $parcelas,
                    "payment_token"   => $paymentToken,
                    "billing_address" => $billingAddress,
                    "customer"        => $customer,
                ],
            ],
        ];

        return $this->executa("{$this->cobrancaUrl}/charge/{$chargeId}/card", "POST", $cardPayload, $token);
    }

    // -------------------------------------------------------------------------
    // Consultas e Estorno
    // -------------------------------------------------------------------------

    /**
     * Consulta uma cobrança pelo ID.
     * Ref: https://dev.efipay.com.br/docs/api-cobrancas/consultar-cobrancas
     */
    public function consultarCobranca(int $chargeId, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        $token = $this->getAccessToken(usarCert: false);
        return $this->executa("{$this->cobrancaUrl}/charge/{$chargeId}", "GET", null, $token);
    }

    /**
     * Consulta uma cobrança Pix pelo txid.
     * Ref: https://dev.efipay.com.br/docs/api-pix/cobrancas-imediatas
     */
    public function consultarPix(string $txid, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        $token = $this->getAccessToken(usarCert: true);
        return $this->executa("{$this->pixUrl}/v2/cob/{$txid}", "GET", null, $token, usarCert: true);
    }

    /**
     * Cancela/estorna uma cobrança de cartão ou boleto.
     * Ref: https://dev.efipay.com.br/docs/api-cobrancas/cancelar-cobrancas
     */
    public function reembolso(int $chargeId, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        $token = $this->getAccessToken(usarCert: false);
        return $this->executa("{$this->cobrancaUrl}/charge/{$chargeId}/cancel", "PUT", null, $token);
    }

    /**
     * Estorna uma cobrança Pix já paga.
     * Ref: https://dev.efipay.com.br/docs/api-pix/devolucoes
     *
     * @param string $e2eId      endToEndId do pagamento Pix recebido no webhook
     * @param string $devolucaoId ID único para esta devolução
     * @param float  $valor       Valor em reais a devolver (parcial ou total)
     */
    public function reembolsoPix(string $e2eId, string $devolucaoId, float $valor, $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        $token = $this->getAccessToken(usarCert: true);

        $payload = ["valor" => number_format($valor, 2, '.', '')];

        return $this->executa(
            "{$this->pixUrl}/v2/pix/{$e2eId}/devolucao/{$devolucaoId}",
            "PUT",
            $payload,
            $token,
            usarCert: true
        );
    }
}

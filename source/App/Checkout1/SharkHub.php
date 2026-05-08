<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

/**
 * SharkHub – Integração com a API de pagamentos SharkHub
 *
 * Endpoint: https://api.sharkhubsubadquirente.com/v1/
 * Autenticação: Bearer token
 *
 * Documentação de métodos suportados: PIX, CREDIT_CARD, BOLETO
 *
 * IMPORTANTE: Este gateway NÃO possui split nativo.
 * As comissões são distribuídas pela carteira interna WinPay após
 * confirmação de pagamento (webhook status=PAID → status_transacao=4).
 *
 * Credenciais (ConfiguracaoCheckout id=11):
 *   chave_producao  → API key de pagamentos (Bearer — rotas /v1/payment)
 *   token_producao  → API key de saque      (Bearer — rotas /v1/transfer)
 *   chave_sandbox   → API key de pagamentos sandbox
 *   token_sandbox   → API key de saque sandbox
 *   sandbox         → 0=produção | 1=sandbox
 */
class SharkHub
{
    private string $apiKeyPagamento = "";
    private string $apiKeySaque     = "";
    private bool   $sandbox         = false;
    private string $endpoint        = "https://api.sharkhubsubadquirente.com/v1";

    private const CONFIG_ID = 11;

    public function __construct()
    {
        // construtor leve — credenciais carregadas em dados()
    }

    // -------------------------------------------------------------------------
    // Credenciais
    // -------------------------------------------------------------------------

    public function dados(?int $adquirenteId = null): void
    {
        $id         = $adquirenteId ?? self::CONFIG_ID;
        $adquirente = (new ConfiguracaoCheckout())->findById($id);

        if (!$adquirente) {
            throw new \Exception("[SharkHub] Configuração (id={$id}) não encontrada.");
        }

        $this->sandbox = (bool)($adquirente->sandbox ?? false);

        $this->apiKeyPagamento = $this->sandbox
            ? (string)($adquirente->chave_sandbox  ?? "")
            : (string)($adquirente->chave_producao ?? "");

        $this->apiKeySaque = $this->sandbox
            ? (string)($adquirente->token_sandbox  ?? "")
            : (string)($adquirente->token_producao ?? "");

        if (empty($this->apiKeyPagamento)) {
            throw new \Exception("[SharkHub] API key de pagamento não configurada.");
        }
    }

    // -------------------------------------------------------------------------
    // Executor HTTP
    // -------------------------------------------------------------------------

    private function executa(string $url, string $metodo, ?array $payload, string $apiKey): object
    {
        $json = $payload !== null
            ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : null;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $metodo,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$apiKey}",
                "Content-Type: application/json",
                "Accept: application/json",
            ],
        ]);

        $resp  = curl_exec($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errNo = curl_errno($ch);
        $err   = curl_error($ch);
        curl_close($ch);

        error_log("[SharkHub] {$metodo} {$url} → HTTP {$http} body=" . substr((string)$resp, 0, 500));
        if ($json) error_log("[SharkHub] payload=" . substr($json, 0, 800));

        if ($errNo) {
            throw new \Exception("[SharkHub] Erro cURL #{$errNo}: {$err}");
        }

        $decoded = json_decode((string)$resp);
        if ($decoded === null) {
            error_log("[SharkHub] Resposta inválida: {$resp}");
            throw new \Exception("[SharkHub] Resposta não-JSON (HTTP {$http}).");
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

    /**
     * Monta o bloco base de um pagamento (campos comuns a todos os métodos).
     */
    private function buildBase(array $data, string $method): array
    {
        $cpf   = $this->onlyDigits($data["cpf"] ?? "");
        $fone  = $this->onlyDigits(($data["ddd"] ?? "") . ($data["telefone"] ?? ""));
        $preco = (int)($data["preco"] ?? 0);

        $notificationUrl = defined("URL_BASE")
            ? rtrim(URL_BASE, "/") . "/webhook/sharkhub"
            : "";

        return [
            "amount"          => $preco,
            "currency"        => "BRL",
            "method"          => $method,
            "description"     => (string)($data["nomekit"] ?? $data["nome_produto"] ?? "Pagamento"),
            "externalRef"     => "venda_" . ($data["venda"] ?? uniqid()),
            "notificationUrl" => $notificationUrl,
            "payer"           => [
                "name"   => trim((string)($data["nome"] ?? "")),
                "taxId"  => $cpf,
                "email"  => (string)($data["email"] ?? ""),
                "phone"  => $fone,
            ],
            "items" => [
                [
                    "quantity" => 1,
                    "name"     => (string)($data["nomekit"] ?? $data["nome_produto"] ?? "Produto"),
                    "price"    => $preco,
                    "type"     => "DIGITAL",
                ],
            ],
        ];
    }

    /**
     * Tokeniza dados do cartão via libsodium (crypto_box_seal) no backend.
     * Requer extensão sodium do PHP (padrão a partir do PHP 7.2).
     * A chave pública (apiKeyPagamento) deve ser a chave x25519 base64 fornecida pela SharkHub.
     */
    public function tokenizarCartao(array $card): string
    {
        if (!function_exists('sodium_crypto_box_seal')) {
            throw new \Exception("[SharkHub] Extensão sodium não disponível. Use tokenização via frontend (SharkHub.js).");
        }

        // Normaliza base64url → base64 padrão e adiciona padding se necessário
        $pkNorm = strtr(trim($this->apiKeyPagamento), '-_', '+/');
        $pkNorm = str_pad($pkNorm, strlen($pkNorm) + (4 - strlen($pkNorm) % 4) % 4, '=');
        $publicKeyBin = base64_decode($pkNorm, true);
        if ($publicKeyBin === false || strlen($publicKeyBin) !== SODIUM_CRYPTO_BOX_PUBLICKEYBYTES) {
            throw new \Exception("[SharkHub] A chave de API configurada não é uma chave pública x25519 válida (base64, 32 bytes). Tamanho decodificado: " . (is_string($publicKeyBin) ? strlen($publicKeyBin) : 'inválido') . " bytes.");
        }

        $payload = json_encode([
            "number"   => preg_replace('/\D/', '', $card["number"] ?? ""),
            "holder"   => trim($card["holder"]   ?? ""),
            "expMonth" => str_pad((string)($card["expMonth"] ?? ""), 2, "0", STR_PAD_LEFT),
            "expYear"  => (string)($card["expYear"] ?? ""),
            "cvv"      => preg_replace('/\D/', '', $card["cvv"] ?? ""),
        ]);

        $encrypted = sodium_crypto_box_seal($payload, $publicKeyBin);
        return base64_encode($encrypted);
    }

    // -------------------------------------------------------------------------
    // PIX
    // -------------------------------------------------------------------------

    /**
     * Cria cobrança via PIX.
     *
     * Campos $data esperados:
     *   preco         int    valor em centavos
     *   nome          string nome do pagador
     *   cpf           string CPF ou CNPJ
     *   email         string
     *   ddd           string
     *   telefone      string
     *   nomekit       string descrição do produto
     *   venda         int    ID da venda (usado como externalRef)
     */
    public function compraPix(array $data): object
    {
        $this->dados();

        if (empty($data["preco"])) {
            throw new \Exception("[SharkHub][Pix] Campo 'preco' obrigatório (centavos).");
        }

        $payload = $this->buildBase($data, "PIX");

        return $this->executa("{$this->endpoint}/payment", "POST", $payload, $this->apiKeyPagamento);
    }

    // -------------------------------------------------------------------------
    // Boleto Bancário
    // -------------------------------------------------------------------------

    /**
     * Cria cobrança via Boleto.
     *
     * Campos $data esperados: mesmos do compraPix + vencimento opcional.
     */
    public function compraBoleto(array $data): object
    {
        $this->dados();

        if (empty($data["preco"])) {
            throw new \Exception("[SharkHub][Boleto] Campo 'preco' obrigatório (centavos).");
        }

        $payload = $this->buildBase($data, "BOLETO");

        // Adiciona data de vencimento se disponível
        $vencimento = $data["vencimento"] ?? date("Y-m-d", strtotime("+3 days"));
        $payload["boleto"] = ["dueDate" => $vencimento];

        return $this->executa("{$this->endpoint}/payment", "POST", $payload, $this->apiKeyPagamento);
    }

    // -------------------------------------------------------------------------
    // Cartão de Crédito
    // -------------------------------------------------------------------------

    /**
     * Realiza cobrança via Cartão de Crédito.
     *
     * O token do cartão pode vir de duas fontes:
     *   1. $data["sharkhub_token"]  — token gerado pelo SharkHub.js no front-end (recomendado)
     *   2. $data["card"]            — array com dados brutos; tokenizado aqui via sodium se disponível
     *
     * Campos $data esperados:
     *   preco               int    valor em centavos
     *   nome                string nome do pagador
     *   cpf                 string
     *   email               string
     *   ddd / telefone      string
     *   parcela             int    número de parcelas (default 1)
     *   sharkhub_token      string token pré-gerado pelo frontend (preferencial)
     *   card                array  {number, holder, expMonth, expYear, cvv} — tokenização backend
     */
    public function compraCartao(array $data): object
    {
        $this->dados();

        if (empty($data["preco"])) {
            throw new \Exception("[SharkHub][Cartão] Campo 'preco' obrigatório (centavos).");
        }

        // Obtém token do cartão
        $cardToken = (string)($data["sharkhub_token"]
            ?? $data["MPHiddenInputToken"]
            ?? $data["sharkhub_card_token"]
            ?? "");

        // Monta array card a partir dos campos brutos do checkout se necessário
        if (empty($cardToken)) {
            $cardArray = !empty($data["card"]) ? (array)$data["card"] : [];
            if (empty($cardArray) && !empty($data["numcart"])) {
                $cardArray = [
                    "number"   => preg_replace('/\D/', '', (string)($data["numcart"] ?? "")),
                    "holder"   => (string)($data["titular"] ?? ""),
                    "expMonth" => (string)($data["mes-val"] ?? ""),
                    "expYear"  => (string)($data["ano-val"] ?? ""),
                    "cvv"      => preg_replace('/\D/', '', (string)($data["cvv"] ?? "")),
                ];
            }
            if (!empty($cardArray)) {
                $cardToken = $this->tokenizarCartao($cardArray);
            }
        }

        if (empty($cardToken)) {
            throw new \Exception("[SharkHub][Cartão] Token do cartão obrigatório. Gere via SharkHub.js no frontend ou informe 'sharkhub_token'.");
        }

        $parcelas = max(1, (int)($data["parcela"] ?? 1));

        $payload = $this->buildBase($data, "CREDIT_CARD");
        $payload["card"] = [
            "token"        => $cardToken,
            "installments" => $parcelas,
        ];

        return $this->executa("{$this->endpoint}/payment", "POST", $payload, $this->apiKeyPagamento);
    }

    // -------------------------------------------------------------------------
    // Consultas e Estorno
    // -------------------------------------------------------------------------

    /**
     * Consulta um pagamento pelo ID.
     */
    public function consultarPagamento(string $paymentId, ?int $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        return $this->executa("{$this->endpoint}/payment/{$paymentId}", "GET", null, $this->apiKeyPagamento);
    }

    /**
     * Estorna um pagamento pelo ID.
     */
    public function reembolso(string $paymentId, ?int $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        return $this->executa("{$this->endpoint}/payment/{$paymentId}/refund", "POST", null, $this->apiKeyPagamento);
    }

    // -------------------------------------------------------------------------
    // Transferência (Saque)
    // -------------------------------------------------------------------------

    /**
     * Cria uma transferência PIX (saque).
     * Utiliza a API key de saque (token_producao / token_sandbox).
     *
     * @param int    $valorCentavos  Valor em centavos
     * @param string $pixKeyType     CPF | CNPJ | EMAIL | PHONE | EVP | COPYPASTE
     * @param string $pixKey         Chave PIX do destinatário
     * @param string $externalRef    Referência externa
     * @param string $notifUrl       URL de webhook da transferência
     */
    public function criarTransferencia(
        int    $valorCentavos,
        string $pixKeyType,
        string $pixKey,
        string $externalRef  = "",
        string $notifUrl     = "",
        ?int   $adquirenteId = null
    ): object {
        $this->dados($adquirenteId);

        if (empty($this->apiKeySaque)) {
            throw new \Exception("[SharkHub] API key de saque não configurada (token_producao).");
        }

        $payload = [
            "amount"          => $valorCentavos,
            "method"          => "PIX",
            "externalRef"     => $externalRef ?: ("saque_" . uniqid()),
            "notificationUrl" => $notifUrl,
            "pix"             => [
                "pixKeyType" => strtoupper($pixKeyType),
                "pixKey"     => $pixKey,
            ],
        ];

        return $this->executa("{$this->endpoint}/transfer", "POST", $payload, $this->apiKeySaque);
    }

    /**
     * Consulta uma transferência pelo ID.
     */
    public function consultarTransferencia(string $transferId, ?int $adquirenteId = null): object
    {
        $this->dados($adquirenteId);
        return $this->executa("{$this->endpoint}/transfer/{$transferId}", "GET", null, $this->apiKeySaque);
    }
}

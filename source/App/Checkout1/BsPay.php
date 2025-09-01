<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class BsPay
{
    private $view;
    private $endpoint;       // /v2/pix/qrcode
    private $auth_url;       // endpoint para pegar o token (AJUSTE AQUI conforme doc)
    private $access_token;   // Bearer obtido via Basic
    private $client_id;      // mapeado de chave_producao
    private $client_secret;  // mapeado de token_producao
    private $public_key;
    private $nome_fatura;

    public function __construct()
    {
        error_log("Construtor BsPay chamado");
    }

    public function dados($data)
    {
        $adquirente = (new ConfiguracaoCheckout())->findById(5);
        if (@$adquirente) {
            $this->endpoint = "https://api.bspay.co/v2/pix/qrcode";
            $this->auth_url = "https://api.bspay.co/v2/oauth/token";
            $this->client_id     = "z4igor_2142362738688273";
            $this->client_secret = $adquirente->token_producao;
            //$this->public_key = $adquirente->chave;
            $this->public_key = $adquirente->chave_producao;
            //$this->access_token = $adquirente->token;

            $this->access_token = $this->getAccessToken();

        }

    }

    private function getAccessToken(): string
    {
        if (empty($this->client_id) || empty($this->client_secret)) {
            throw new \Exception("Credenciais BSPay ausentes: client_id/client_secret não informados.");
        }
        if (empty($this->auth_url)) {
            throw new \Exception("URL de autenticação (auth_url) não configurada.");
        }

        // Monta o header Basic: base64(client_id:client_secret)
        $credentials = $this->client_id . ':' . $this->client_secret;
        $basic = base64_encode($credentials);

        // Alguns servidores esperam grant_type=client_credentials (OAuth2)
        // Tente primeiro com JSON; se sua doc exigir 'application/x-www-form-urlencoded', troque o bloco.
        $body = json_encode(["grant_type" => "client_credentials"], JSON_UNESCAPED_UNICODE);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->auth_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . $basic,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_FOLLOWLOCATION => false,
        ]);

        $resp  = curl_exec($ch);
        $info  = curl_getinfo($ch);
        $errNo = curl_errno($ch);
        $err   = curl_error($ch);
        curl_close($ch);

        if ($errNo) {
            throw new \Exception("Erro cURL ao obter token BSPay: #{$errNo} {$err}");
        }

        $data = json_decode((string)$resp, true);

        // Adapte as chaves conforme a resposta da BSPay (ex.: access_token / token / jwt)
        $token = $data['access_token'] ?? $data['token'] ?? null;

        if (empty($token)) {
            // Log de apoio
            error_log("[BSPay][Auth] HTTP: {$info['http_code']} Resp: {$resp}");
            throw new \Exception("Falha ao obter access_token da BSPay. Verifique auth_url e formato do body.");
        }

        return $token;
    }


    public function executa($metodo, $json = null, $autorization = null)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true, // igual ao exemplo que funcionou
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $metodo,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->access_token, // agora com Bearer REAL
                'Content-Type: application/json',
            ],
        ]);

        $resp  = curl_exec($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errNo = curl_errno($ch);
        $err   = curl_error($ch);
        curl_close($ch);

        // Logs mínimos
        error_log("[BSPay] HTTP Code: {$http}");
        if ($errNo) error_log("[BSPay] cURL #{$errNo}: {$err}");

        echo $resp;
        return json_decode($resp);
    }




    public function compraPix($data)
    {
        // Carrega credenciais (define token e endpoint base)
        $this->dados(@$data["adquirente"]);

        // Garante o endpoint correto para QR Code (cobrança)
        $this->endpoint = "https://api.bspay.co/v2/pix/qrcode";

        // --- Validações mínimas
        if (empty($this->access_token)) {
            throw new \Exception("Token de acesso da BSPay não configurado.");
        }
        if (empty($data["preco"])) {
            throw new \Exception("Preço (preco) não informado (centavos).");
        }
        if (empty($data["nome"]) || empty($data["email"]) || empty($data["cpf"])) {
            throw new \Exception("Dados do pagador incompletos: nome, email e cpf são obrigatórios.");
        }

        // helper: mantém apenas dígitos (CPF/CNPJ/CEP)
        $onlyDigits = function (?string $v) {
            return $v ? preg_replace('/\D+/', '', $v) : null;
        };

        // A BSPay espera "amount" em REAIS (ex.: 1500 centavos -> 15.00)
        $amount = round(((int)$data["preco"]) / 100, 2);

        // Identificador único da sua transação
        $externalId = (string)($data["id"] ?? uniqid("bspay_qr_", true));

        // Texto exibido ao pagador no app (opcional)
        $payerQuestion = (string)($data["nome_produto"] ?? $data["descricao"] ?? "Pagamento via Pix");

        // Dados do pagador
        $nomeCompleto = preg_replace('/\s+/', ' ', trim((string)$data["nome"]));
        $cpf = $onlyDigits($data["cpf"] ?? "");

        // Webhook de notificação (pode sobrescrever via $data["postbackUrl"])
        $postbackUrl = (string)($data["postbackUrl"] ?? "https://app.winpay.digital/webhook/bspay");

        // Monta payload conforme Cobrança PIX (QR Code)
        $payload = [
            "amount"        => $amount,          // número em reais
            "external_id"   => $externalId,      // seu ID único
            "payerQuestion" => $payerQuestion,   // pergunta/descrição
            "payer" => [
                "name"     => $nomeCompleto,
                "document" => $cpf,              // CPF/CNPJ do pagador (só dígitos)
                "email"    => (string)$data["email"],
            ],
            "postbackUrl"   => $postbackUrl
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Reutiliza o executor (já envia Authorization, Content-Type e X-Idempotency-Key)
        return $this->executa("POST", $json);
    }
    
}
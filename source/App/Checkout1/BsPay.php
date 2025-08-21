<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class BsPay
{
    private $view;
    private $endpoint;
    private $access_token;
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
            //$this->public_key = $adquirente->chave;
            $this->public_key = $adquirente->chave_producao;
            //$this->access_token = $adquirente->token;
            $this->access_token = $adquirente->token_producao;
        }

    }
    public function executa($metodo, $json = null, $autorization = null)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $this->endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',        // igual ao exemplo que funcionou
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,      // igual ao exemplo
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => $metodo,   // 'POST'
        CURLOPT_POSTFIELDS     => $json,     // json_encode($payload)
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json',
        ],
    ]);

    $resp  = curl_exec($ch);
    $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errNo = curl_errno($ch);
    $err   = curl_error($ch);
    curl_close($ch);

    // logs úteis (remova em prod)
    error_log("[BSPay:min] HTTP Code: {$http}");
    if ($errNo) error_log("[BSPay:min] cURL #{$errNo}: {$err}");

    echo $resp;
    return json_decode($resp, true);
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
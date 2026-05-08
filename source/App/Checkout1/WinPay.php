<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

class WinPay
{
  private $view;
  private $endpoint;
  private $token;
  private $key;

  public function __construct()
  {
    $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(8);
    $configuracao = (new ConfiguracaoCheckout())->findById(1);
    if ($configuracao->sandbox == 1) {

      $this->endpoint = "https://api.pagar.me/core/v5/";
      $this->token = $configuracaoCheckout->token_sandbox;
      $this->key = $configuracaoCheckout->chave_sandbox;
    } else {
      $this->endpoint = "https://api.pagar.me/core/v5/";
      $this->token = $configuracaoCheckout->token_producao;
      $this->key = $configuracaoCheckout->chave_producao;
    }
  }

  public function executa($url, $metodo, $json = null)
{
    $authorization = base64_encode($this->token . ":Red02842324*");

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "$url",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "$metodo",
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic $authorization",
            'Content-Type: application/json',
            "X-API-KEY: {$this->key}" // <- chave da API vinda da config
        ),
    ));

    $response = curl_exec($curl);

    $arquivo = fopen(DIRETORIO . "logs/dados-pagarme.txt", 'a+');
    if ($arquivo !== false) {
        $texto = date("d/m/Y H:i:s");
        $texto .= "Dados da requisição: " . @$json . "\n";
        $texto .= "*********************************************************\n";
        fwrite($arquivo, $texto . "\n");
        fclose($arquivo);
    }

    curl_close($curl);
    return json_decode($response);
}


public function processarSplitParaWallet($data)
{
    error_log("Venda recebida: " . json_encode($data));

    // Só processa se:
    // forma_pagamento 1, 2 ou 3 E status_transacao = 4
    $forma = intval($data["forma_pagamento"] ?? 0);
    $status = intval($data["status_transacao"] ?? 0);

    $formas_validas = [1, 2, 3];
    if (!in_array($forma, $formas_validas) || $status !== 4) {
        return (object)[
            "success" => false,
            "message" => "Pagamento não confirmado ou forma de pagamento inválida",
            "forma_pagamento" => $forma,
            "status_transacao" => $status
        ];
    }

    $apiKey = "minha-chave-secreta";
    $endpointBase = "http://wallet:3030/api/wallet";

    $comissoes_pagamento = [];

    // Comissão do produtor
    if (!empty($data["produtor"]) && is_numeric($data["comissao_produtor"])) {
        $comissoes_pagamento[] = [
            "usuario" => $data["produtor"],
            "comissao" => floatval($data["comissao_produtor"])
        ];
    }

    // Comissão do afiliado
    if (!empty($data["afiliado"]) && is_numeric($data["comissao_afiliado"])) {
        $comissoes_pagamento[] = [
            "usuario" => $data["afiliado"],
            "comissao" => floatval($data["comissao_afiliado"])
        ];
    }

    // Comissão de coprodutores
    if (!empty($data["comissao_coprodutores"])) {
        $coprodutores = json_decode($data["comissao_coprodutores"], true);
        if (is_array($coprodutores)) {
            foreach ($coprodutores as $copro) {
                if (!empty($copro["usuario"]) && is_numeric($copro["comissao"])) {
                    $comissoes_pagamento[] = [
                        "usuario" => $copro["usuario"],
                        "comissao" => floatval($copro["comissao"])
                    ];
                }
            }
        }
    }

    if (empty($comissoes_pagamento)) {
        return (object)["error" => "Nenhuma comissão válida encontrada para processar"];
    }

    $resultados = [];

    foreach ($comissoes_pagamento as $comissao) {
        $userId = $comissao["usuario"];
        $valor = $comissao["comissao"];

        if (!is_numeric($valor) || $valor <= 0) {
            error_log("Valor de comissão inválido para o usuário $userId: $valor");
            $resultados[] = ["user_id" => $userId, "status" => "erro", "mensagem" => "valor inválido"];
            continue;
        }

        $payload = json_encode(["amount" => $valor]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$endpointBase/$userId/credit",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                "X-API-KEY: $apiKey",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        error_log("Creditar wallet: user_id=$userId | valor=$valor | status=$statusCode | resposta=$response");

        $resultados[] = [
            "user_id" => $userId,
            "valor" => $valor,
            "status_http" => $statusCode,
            "resposta" => json_decode($response, true)
        ];
    }

    return (object)[
        "success" => true,
        "message" => "Comissões processadas com sucesso",
        "resultados" => $resultados
    ];
}



public function criarWallet($data)
{
    error_log("Criar Wallet: " . json_encode($data));

    $apiKey = "minha-chave-secreta";
    $endpoint = "http://wallet:3030/api/wallet/create";

    $userId = trim(@$data["id"]);

    if (empty($userId)) {
        return (object)["error" => "ID do usuário (id) é obrigatório"];
    }

    $json = json_encode([
        "user_id" => $userId
    ]);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => array(
            "X-API-KEY: $apiKey",
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        $curlErr = curl_error($curl);
        curl_close($curl);
        error_log("Erro cURL criarWallet: $curlErr");
        return (object)["success" => false, "error" => $curlErr];
    }

    curl_close($curl);

    $resposta = json_decode($response, true);
    error_log("criarWallet: HTTP $httpCode | resposta=" . json_encode($resposta));

    $success = $httpCode >= 200 && $httpCode < 300;

    return (object)[
        "success" => $success,
        "http_code" => $httpCode,
        "id" => $resposta["user_id"] ?? $userId,
        "resposta" => $resposta,
        "error" => $success ? null : ($resposta["message"] ?? $resposta["error"] ?? "HTTP $httpCode"),
    ];
}




  public function saldo($user_id)
  {
      $url = "http://wallet:3030/api/wallet/$user_id";

      $curl = curl_init();

      curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
              "X-API-KEY: minha-chave-secreta",
              "Content-Type: application/json"
          ),
      ));

      $response = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      $decoded = json_decode($response);
      if ($decoded !== null) {
          $decoded->_http_code = $httpCode;
      }
      return $decoded;
  }
  
  public function saque($user_id, $amount)
{
    $url = "http://wallet:3030/api/wallet/$user_id/debit";
    $json = json_encode(["amount" => $amount]);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => array(
            "X-API-KEY: minha-chave-secreta",
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response);
}
  
}

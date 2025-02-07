<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

class Stripe
{
    private $view;
    private $endpoint;
    private $token;
    private $key;

    public function __construct()
    {
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(5);
        $configuracao = (new ConfiguracaoCheckout())->findById(1);
        if ($configuracao->sandbox == 1) {
            $this->endpoint = "https://api.stripe.com/v1/";
            // $this->token = $configuracaoCheckout->token_sandbox;
            $this->token = 'sk_test_51NdI8xGgbdiVKbZ56JyQfGYBgeNVu7QP6HDNPs1GCxG6hhqwhtoRNPdhULBrWJQKNtq8oCa4bPuSBCuJVvTMdC5T00k5wFBShQ';
            $this->key = $configuracaoCheckout->chave_sandbox;
        } else {
            $this->endpoint = "https://api.stripe.com/v1/";
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;
        }
    }

    public function executa($url, $metodo, $json = null)
    {
        $autorization = $this->token;

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
                "Authorization: Bearer $autorization",
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);

        $arquivo = fopen(DIRETORIO . "logs/dados-pagarme.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = date("d/m/Y H:i:s");
        $texto .= "Dados da requisição: " . @$json . "\n";
        $texto .= "*********************************************************\n";
        fwrite($arquivo, $texto . "\n");
        fclose($arquivo);

        // echo $json;
        // echo $autorization;
        // echo $response;
        //  exit;
        curl_close($curl);
        return json_decode($response);
    }

    public function split($data)
    {
        if (@$data["comissoes_pagamento"]) {

            $split1 = '"split": [';

            $i = 0;
            foreach ($data["comissoes_pagamento"] as $comissoes) {
                $amount = number_format($comissoes["comissao"] * 100, 0, "", "");
                // $amount = $data["preco"];

                if ($i > 0) {
                    $reminder_fee = "false";
                } else {
                    $reminder_fee = "true";
                }

                $split1 .= '
        {
          "amount": ' . $amount . ',
          "recipient_id": "' . $comissoes["usuario"] . '",
          "type": "flat",
          "options": {
            "charge_processing_fee": true,
            "charge_remainder_fee": ' . $reminder_fee . ',
            "liable": true
          }
        },';

                $i++;
            }

            $split1 .= "],";
            return $split1;
        }
    }

    public function compraCartao($data)
    {

        $dados["url"] = $this->endpoint . "charges";
        $ano_val = substr(@$data["ano-val"], -2);
        $json["currency"] = 'brl';
        $json["amount"] = $data["preco"];
        $json["source"] = $data["stripe-token"];
        $js = http_build_query($json);

        // echo $dados["json"];
        // exit;
        // return $dados;
        return $this->executa($dados["url"], "POST", $js);
    }

    public function estornoCartao($data)
    {

        $dados["url"] = $this->endpoint . "refunds";
        $json["charge"] = $data["transacao"];
        $js = http_build_query($json);

        return $this->executa($dados["url"], "POST", $js);
    }

    public function criarRecebedor($data)
    {
        if (strlen(trim(@$data["cpf"])) == 11) {
            $type = "individual";
        } else {
            $type = "company";
        }

        $dados["url"] = $this->endpoint . "/recipients";

        if (@$data["digito_agencia"]) {
            $digito_agencia = '"branch_check_digit": "' . @$data["digito_agencia"] . '",';
        }

        $dados["json"] = '{
      "name": "' . @$data["nome"] . '",
      "email": "' . @$data["email"] . '",
      "description": "Recebedor ' . @$data["nome"] . '",
      "document": "' . @$data["cpf"] . '",
      "type": "' . $type . '",
      "default_bank_account": {
        "holder_name": "' . @$data["nome"] . '",
        "holder_type": "' . $type . '",
        "holder_document": "' . @$data["cpf"] . '",
        "bank": "' . @$data["banco"] . '",
        "branch_number": "' . @$data["agencia"] . '",
        ' . @$digito_agencia . '
        "account_number": "' . @$data["conta"] . '",
        "account_check_digit": "' . @$data["digito_conta"] . '",
        "type": "savings"
      },
      "transfer_settings": {
        "transfer_enabled": false,
        "transfer_interval": "Daily",
        "transfer_day": 0
      },
      "automatic_anticipation_settings": {
        "enabled": false,
        "type": "full",
        "volume_percentage": "50",
        "delay": null
      }
    }';

        // echo $dados["json"];
        // exit;
        // return $dados;
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function saldo($recipient_id)
    {
        $url = "https://api.pagar.me/core/v5/recipients/$recipient_id/balance";

        return $this->executa($url, "GET");
    }

    public function saque($recipient_id, $amount)
    {
        $url = "https://api.pagar.me/core/v5/recipients/$recipient_id/balance";
        $json = "{
      'amount': $amount
    }";

        return $this->executa($url, "GET");
    }
}

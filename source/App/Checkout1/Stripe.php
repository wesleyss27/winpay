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
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(8);
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
        $url = $this->endpoint . "payment_intents";

        // --- normaliza valor (centavos)
        $valor = $data['preco'] ?? 0;
        if (!is_int($valor)) {
            $valor = str_replace(['.', ','], ['', '.'], (string)$valor);
            $valor = (int) round(((float)$valor) * 100);
        }

        // --- token do cartão vindo do front (tok_...)
        $token = $data['MPHiddenInputToken']
            ?? $data['stripe-token']
            ?? $data['stripeToken']
            ?? null;

        if (!$token) {
            return (object)[
                'error' => (object)[
                    'message' => 'MPHiddenInputToken ausente'
                ]
            ];
        }

        // --- corpo do PaymentIntent
        $body = [
            'amount'                         => $valor,
            'currency'                       => 'brl',
            'confirmation_method'            => 'automatic',
            'confirm'                        => 'true',
            // monta o payment_method a partir do token do cartão
            'payment_method_data[type]'      => 'card',
            'payment_method_data[card][token]' => $token,
            // 'metadata[order_id]' => $data['order_id'] ?? '',
        ];

        // --- parcelas (se usar)
        if (!empty($data['parcela']) && (int)$data['parcela'] > 1) {
            $qtd = (int)$data['parcela'];

            $body['payment_method_options[card][installments][enabled]']                 = 'true';
            $body['payment_method_options[card][installments][plan][type]']              = 'fixed_count';
            $body['payment_method_options[card][installments][plan][count]']             = (string)$qtd;
            $body['payment_method_options[card][installments][plan][interval]']          = 'month'; // <-- obrigatório

            // opcional: travar rede se necessário (ex.: 'mastercard' ou 'visa')
            if (!empty($data['network'])) {
                $body['payment_method_options[card][installments][network]']             = $data['network'];
            }
        }

        // --- Stripe Connect (opcional)
        if (!empty($data['destination'])) {
            $body['transfer_data[destination]'] = $data['destination']; // acct_xxx
        }
        if (isset($data['application_fee_amount'])) {
            $body['application_fee_amount'] = (int)$data['application_fee_amount'];
        }

        // Envia como application/x-www-form-urlencoded
    return $this->executa($url, "POST", http_build_query($body));
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

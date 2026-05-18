<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

class PagarMe
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

  public function executa($url, $metodo, $json = null, $idempotencyKey = null)
  {
    $autorization = base64_encode( $this->token . ":" );

    $headers = [
      "Authorization: Basic $autorization",
      'Content-Type: application/json',
    ];

    if ($idempotencyKey) {
      $headers[] = "Idempotency-Key: $idempotencyKey";
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "$url",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "$metodo",
      CURLOPT_POSTFIELDS => $json,
      CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    error_log("[GatewayManager][PIX]{$response}'"); 

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

  public function getOrderStatus( $order_ref )
  {
    $autorization = base64_encode( $this->token . ":Red02842324*" );

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.pagar.me/core/v5/orders/" . $order_ref,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Basic $autorization",
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);
  }

  public function split($data)
  {
    if (@$data["comissoes_pagamento"]) {

      $items = [];
      $i = 0;
      foreach ($data["comissoes_pagamento"] as $comissoes) {
        $amount = (int) round($comissoes["comissao"] * 100);

        $charge_processing_fee = ($i === 0) ? 'true' : 'false';
        $reminder_fee          = ($i === 0) ? 'true' : 'false';

        $items[] = '{
          "amount": ' . $amount . ',
          "recipient_id": "' . $comissoes["usuario"] . '",
          "type": "flat",
          "options": {
            "charge_processing_fee": ' . $charge_processing_fee . ',
            "charge_remainder_fee": ' . $reminder_fee . ',
            "liable": true
          }
        }';

        $i++;
      }

      // sem trailing comma no array nem após o campo
      return '"split": [' . implode(',', $items) . '],';
    }
  }

  public function compraCartao($data)
  {
    if (strlen(trim(@$data["cpf"])) == 11) {
      $type = "individual";
    } else {
      $type = "company";
    }

    @$data['rua'] = @$data['rua'] ? @$data['rua'] : 'Rua Elvira Marques';
    @$data['numero'] = @$data['numero'] ? @$data['numero'] : '34';
    @$data['bairro'] = @$data['bairro'] ? @$data['bairro'] : 'Bateas';
    @$data['cidade'] = @$data['cidade'] ? @$data['cidade'] : 'Brusque';
    @$data['estado'] = @$data['estado'] ? @$data['estado'] : 'SC';
    @$data['cep'] = @$data['cep'] ? @$data['cep'] : '88355378';

    $numero_cartao = preg_replace('/\D+/', '', (string) ($data["numcart"] ?? ""));
    $cvv = preg_replace('/\D+/', '', (string) ($data["cvv"] ?? ""));

    $split = $this->split($data);

    $dados["url"] = $this->endpoint . "orders";
    // PagarMe v5 exige ano com 4 dígitos
    $ano_val = strlen((string)@$data["ano-val"]) === 2
        ? '20' . $data["ano-val"]
        : (string)@$data["ano-val"];
    $dados["json"] = '
    {
      "customer": {
        "name": "' . @$data["nome"] . '",
        "type": "' . $type . '",
        "country": "br",
        "email": "' . @$data["email"] . '",
        "document_type": "' . strtolower(@$data["documento"]) . '",
        "document": "' . @$data["cpf"] . '",
        "phones": {
          "mobile_phone": {
            "country_code": "55",
            "area_code": "' . @$data["ddd"] . '",
            "number": "' . @$data["telefone"] . '"
          }
        }
      },
      "items": [
        {
          "amount": ' . @$data["preco"] . ',
          "description": "' . @$data["nomekit"] . '",
          "quantity": 1,
          "code": "' . @$data["produto"] . '"
        }
      ],
      "shipping": {
        "description": "' . @$data["nomekit"] . '",
        "amount": 0,
        "recipient_name": "' . @$data["nome"] . '",
        "recipient_phone": "' . @$data["ddd"] . @$data["telefone"] . '",
        "address": {
          "country": "br",
          "state": "' . @$data["estado"] . '",
          "city": "' . @$data["cidade"] . '",
          "line_1": "' .
      @$data["numero"] . ', ' .
      @$data["rua"] . ', ' .
      @$data["bairro"] .
      '",
          "zip_code": "' . @$data["cep"] . '"
        }
      },
      "payments": [
      {
        "payment_method": "credit_card",
        "credit_card": {
          "card": {
            "billing_address": {
              "country": "br",
              "state": "' . @$data["estado"] . '",
              "city": "' . @$data["cidade"] . '",
              "line_1": "' .
      @$data["numero"] . ', ' .
              @$data["rua"] . ', ' .
              @$data["bairro"] .
      '",
              "zip_code": "' . @$data["cep"] . '"
            },
            "number": "' . $numero_cartao . '",
            "cvv": "' . $cvv . '",
            "exp_month": "' . @$data["mes-val"] . '",
            "exp_year": "' . @$ano_val . '",
            "holder_name": "' . @$data["titular"] . '",
          },
          "recurrence": false,
          "installments": ' . @$data["parcela"] . ',
          "statement_descriptor": "' . @$data["nome_fatura"] . '",
        },
        ' . $split . '
      }
      ]
    }';

    $idempotencyKey = 'cartao-' . ($data['protocolo'] ?? uniqid('', true));

    // echo $dados["json"];
    // exit;
    // return $dados;
    error_log("dados para request cartão pagarme".json_encode($dados));
    return $this->executa($dados["url"], "POST", $dados["json"], $idempotencyKey);
  }

  public function compraBoleto($data)
  {
    $expiration_date = $data["vencimento"];

    $split = $this->split($data);
    // $ano_val = substr(@$data["ano-val"], -2);
    if (strlen(trim(@$data["cpf"])) == 11)
      $type = "individual";
    else
      $type = "company";

    $dados["url"] = $this->endpoint . "/orders";

    $dados["json"] = '{
          "items": [
              {
                "amount": ' . @$data["preco"] . ',
                "description": "' . @$data["nomekit"] . '",
                "quantity": 1,
                "code": "' . @$data["produto"] . '"
              }
          ],
          "customer": {
            "name": "' . @$data["nome"] . '",
            "email": "' . @$data["email"] . '",
            "document": "' . @$data["cpf"] . '",
            "type": "' . @$type . '",
            "phones": {
              "mobile_phone": {
                "country_code": "55",
                "area_code": "' . @$data["ddd"] . '",
                "number": "' . @$data["telefone"] . '"
              }
            }
          },
          "payments": [
            {
                "payment_method": "boleto",
                "boleto": {
                    "instructions": "Pagar até o vencimento",
                    "due_at": "' . @$data["vencimento"] . '"
                },
                ' . $split . '
            }
          ]
      }';

    $idempotencyKey = 'boleto-' . ($data['protocolo'] ?? uniqid('', true));

    // echo $dados["json"];
    // exit;
    // return $dados;
    return $this->executa($dados["url"], "POST", $dados["json"], $idempotencyKey);
  }

  public function compraPix($data)
  {
    // API DE COMPRA COM PIX USA A VERSÃO v5
    if (strlen(trim(@$data["cpf"])) == 11)
      $type = "individual";
    else
      $type = "company";
    $split = $this->split($data);

    $dados["url"] = $this->endpoint . "/orders";

    $dados["json"] = '{
      "items": [
          {
            "amount": ' . @$data["preco"] . ',
            "description": "' . @$data["nomekit"] . '",
            "quantity": 1,
            "code": "' . @$data["produto"] . '"
          }
      ],
      "customer": {
        "name": "' . @$data["nome"] . '",
        "email": "' . @$data["email"] . '",
        "document": "' . @$data["cpf"] . '",
        "type": "' . @$type . '",
        "phones": {
          "mobile_phone": {
            "country_code": "55",
            "area_code": "' . @$data["ddd"] . '",
            "number": "' . @$data["telefone"] . '"
          }
        }
      },
      "payments": [
        {
            "payment_method": "pix",
            "pix": {
                "expires_in": "3600"
            },
            ' . $split . '
        }
      ]
  }';

    $idempotencyKey = 'pix-' . ($data['protocolo'] ?? uniqid('', true));

    // echo $dados["json"];
    // exit;
    // return $dados;
    return $this->executa($dados["url"], "POST", $dados["json"], $idempotencyKey);
  }

  public function criarRecebedor($data)
  {
    $doc  = preg_replace('/\D/', '', (string)@$data["cpf"]);
    $isPF = strlen($doc) <= 11;

    if ($isPF) {
      $registerInfo = [
        "email"                   => (string)@$data["email"],
        "document"                => $doc,
        "type"                    => "individual",
        "name"                    => (string)@$data["nome"],
        "birthdate"               => (string)@$data["data_nascimento"],
        "monthly_income"          => (int)@$data["renda_mensal"],
        "professional_occupation" => (string)@$data["ocupacao"],
      ];
      if (!empty($data["cep"])) {
        $registerInfo["address"] = [
          "line_1"   => trim(@$data["numero"] . ", " . @$data["rua"] . ", " . @$data["bairro"], ", "),
          "line_2"   => (string)@$data["complemento"],
          "zip_code" => preg_replace('/\D/', '', (string)@$data["cep"]),
          "city"     => (string)@$data["cidade"],
          "state"    => strtoupper((string)@$data["estado"]),
          "country"  => "BR",
        ];
      }
    } else {
      $telDigits = preg_replace('/\D/', '', (string)@$data["telefone"]);
      // Remove o 0 inicial (DDI/DDD com zero à frente) se tiver 11+ dígitos
      if (strlen($telDigits) > 11 && $telDigits[0] === '0') {
        $telDigits = ltrim($telDigits, '0');
      }
      $areaCode = substr($telDigits, 0, 2);
      $telNum   = substr($telDigits, 2);

      $registerInfo = [
        "email"          => (string)@$data["email"],
        "document"       => $doc,
        "type"           => "corporation",
        "company_name"   => (string)(@$data["nome_fantasia"] ?: @$data["nome"]),
        "trading_name"   => (string)(@$data["razao_social"] ?: @$data["nome"]),
        "annual_revenue" => (int)@$data["receita_anual"],
        "phone_numbers"  => [
          "mobile_phone" => [
            "country_code" => "55",
            "area_code"    => $areaCode,
            "number"       => $telNum,
          ],
        ],
      ];
    }

    $payload = [
      "code"                 => "user-" . @$data["id"],
      "register_information" => $registerInfo,
      "default_bank_account" => [
        "holder_name"         => (string)@$data["nome"],
        "holder_type"         => $isPF ? "individual" : "company",
        "holder_document"     => $doc,
        "bank"                => (string)@$data["banco"],
        "branch_number"       => (string)@$data["agencia"],
        "branch_check_digit"  => (string)(@$data["digito_agencia"] ?: "0"),
        "account_number"      => (string)@$data["conta"],
        "account_check_digit" => (string)@$data["digito_conta"],
        "type"                => (@$data["tipo"] === "corrente") ? "checking" : "savings",
      ],
      "transfer_settings" => [
        "transfer_enabled"  => false,
        "transfer_interval" => "Daily",
        "transfer_day"      => 0,
      ],
      "automatic_anticipation_settings" => [
        "enabled"           => false,
        "type"              => "full",
        "volume_percentage" => 0,
        "delay"             => null,
      ],
    ];

    $url = $this->endpoint . "recipients";
    return $this->executa($url, "POST", json_encode($payload, JSON_UNESCAPED_UNICODE));
  }

  public function reembolsoCartao($data)
  {
    $cobranca = $this->cobrancaPorPedido($data);
    // var_dump($cobranca);

    if (@$cobranca->data) {
      $dados["url"] = $this->endpoint . "charges/" . $cobranca->data[0]->id;
      // echo $dados["url"];

      return $this->executa($dados["url"], "DELETE");
    }
  }

  public function cobrancaPorPedido($data)
  {
    $dados["url"] = $this->endpoint . "charges/?order_id=" . $data["transacao"];

    return $this->executa($dados["url"], "GET");
  }

  public function reembolsoBoleto($data)
  {
    $dados["url"] = $this->endpoint . "/v2/payments/" . $data["transacao"] . "/refunds";
    $dados["json"] = '{
            "bankAccount": {
                "type": "BOLETO",
                "bankNumber": "",
                "agencyNumber": "",
                "agencyCheckNumber": "",
                "accountNumber": "",
                "accountCheckNumber": "",
                "holder": {
                    "fullname": "",
                    "taxDocument": ""
                }
            }
        }';

    return $this->executa($dados["url"], "DELETE", $dados["json"]);
  }

  public function planoAssinatura($data)
  {
    $dados["url"] = $this->endpoint . "/assinaturas/v1/plans";
    $dados["json"] = '{
            "code": "plan102",
            "name": "Plano Especial",
            "description": "Descrição do Plano Especial",
            "amount": 990,
            "setup_fee": 500,
            "max_qty": 1,
            "interval": {
                "length": 1,
                "unit": "DAY"
            },
            "billing_cycles": 12,
            "trial": {
                "days": 30,
                "enabled": false,
                "hold_setup_fee": false
            },
            "payment_method": "CREDIT_CARD"
        }';

    return $this->executa($dados["url"], "POST");
  }

  public function assinante($data)
  {
    $dados["url"] = $this->endpoint . "/assinaturas/v1/customers?new_vault=true";
    $dados["json"] = '{
            "code": "cliente01",
            "email": "nome@exemplo.com.br",
            "fullname": "Nome Sobrenome",
            "cpf": "22222222222",
            "phone_area_code": "11",
            "phone_number": "934343434",
            "birthdate_day": "26",
            "birthdate_month": "04",
            "birthdate_year": "1980",
            "address": {
                "street": "Rua Nome da Rua",
                "number": "100",
                "complement": "Casa",
                "district": "Nome do Bairro",
                "city": "São Paulo",
                "state": "SP",
                "country": "BRA",
                "zipcode": "05015010"
            },
            "billing_info": {
                "credit_card": {
                    "holder_name": "Nome Completo",
                    "number": "4111111111111111",
                    "expiration_month": "06",
                    "expiration_year": "22"
                }
            }
        }';

    return $this->executa($dados["url"], "POST");
  }

  public function assinatura($data)
  {
    $dados["url"] = $this->endpoint . "/assinaturas/v1/customers?new_vault=true";
    $dados["json"] = '{
            "code": "assinatura02",
            "amount": "9990",
            "payment_method": "CREDIT_CARD",
            "plan": {
                "code": "plan102"
            },
            "customer": {
                "code": "cliente01"
            }
        }';

    return $this->executa($dados["url"], "POST");
  }

  public function saldo($recipient_id)
  {
    $url = "https://api.pagar.me/core/v5/recipients/$recipient_id/balance";

    return $this->executa($url, "GET");
  }

  public function saque($recipient_id, $amount)
  {
    $url = "https://api.pagar.me/core/v5/recipients/$recipient_id/withdrawals";
    $json = "{'amount': $amount}";
    return $this->executa($url, "POST", $json);
  }

  public function historicoSaques($recipient_id, $data_ini = null, $data_fim = null, $count = 100)
  {
    $params = ["count" => $count, "page" => 1];
    if ($data_ini) $params["created_since"] = $data_ini . "T00:00:00Z";
    if ($data_fim) $params["created_until"] = $data_fim . "T23:59:59Z";

    $url = "https://api.pagar.me/core/v5/recipients/$recipient_id/withdrawals?" . http_build_query($params);
    $resp = $this->executa($url, "GET");

    if (isset($resp->data) && is_array($resp->data)) return $resp->data;
    if (is_array($resp)) return $resp;
    return [];
  }

  /**
   * Cancela/estorna uma cobrança
   * @param string $chargeId Código da cobrança no Pagar.me
   * @param int|null $amount Valor em centavos (opcional). Se null, estorna total.
   * @param array|null $bankAccount Dados bancários (necessário para boleto).
   */
  public function reembolso($chargeId, $amount = null, $bankAccount = null)
  {
    // Log request
    $log = [
      "when" => date("Y-m-d H:i:s"),
      "chargeId" => $chargeId,
      "amount" => $amount,
      "bank_account" => $bankAccount
    ];
    file_put_contents(
      DIRETORIO . "logs/pagarme-reembolso.log",
      json_encode($log, JSON_UNESCAPED_UNICODE) . PHP_EOL,
      FILE_APPEND
    );

    $payload = [];
    if ($amount !== null) {
      $payload["amount"] = (int)$amount;
    }
    if ($bankAccount) {
      $payload["bank_account"] = $bankAccount;
    }

    $json = $payload ? json_encode($payload) : null;
    $url = $this->endpoint . "charges/" . $chargeId;

    return $this->executa($url, "DELETE", $json);
  }
}

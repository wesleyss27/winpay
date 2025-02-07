<?php

namespace Source\App\Asaas;

class AsaasCobrancas
{
  private $endpoint;
  private $api_key;

  public function __construct()
  {
    $this->endpoint = 'https://sandbox.asaas.com';
    $this->api_key = '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwNDA4Mjc6OiRhYWNoXzAyZGNjOGNlLTkwOTAtNGIzMC1iNzc4LTVlNmIzNDhhYWY1Mw==';
  }

  public function split($data)
  {
    if (@$data["comissoes_pagamento"]) {

      $split1 = "'split': [";

      foreach ($data["comissoes_pagamento"] as $comissoes) {
        $split1 .= "{'walletId': '" . $comissoes["wallet_id"] . "',";

        if (@$data["parcela"] > 1) {
          $split1 .= "'totalFixedValue': " . $comissoes["comissao"] . "},";
        } else {
          $split1 .= "'fixedValue': " . $comissoes["comissao"] . "},";
        }
      }

      $split1 .= "],";
      return $split1;
    }

    return null;
  }

  public function cobrancaBoleto($data)
  {
    $id = uniqid();

    $preco = @$data["preco"] / 100;

    $split = $this->split($data);

    $dados["endpoint"] = $this->endpoint . "/api/v3/payments";
    $dados["method"] = "POST";
    $dados["json"] = "{
            'customer': '" . @$data["customer_id"] . "',
            'billingType': 'BOLETO',
            'dueDate': '" . @$data["vencimento"] . "',
            'value': " . $preco . ",
            'description': 'Pedido $id',
            'externalReference': '$id',
            $split
            'postalService': false
          }";

    return $this->executar($dados['endpoint'], $dados['method'], $dados['json']);
  }

  public function cobrancaCartaoCredito($data)
  {
    $preco = @$data["preco"] / 100;
    $id = uniqid();

    $dados["endpoint"] = $this->endpoint . "/api/v3/payments";
    $dados["method"] = "POST";
    $dados["json"] = "{
      'customer': '" . @$data["customer_id"] . "',
      'billingType': 'CREDIT_CARD',
      'dueDate': '" . @$data["vencimento"] . "',
      'installmentCount': " . @$data["parcela"] . ",
      'totalValue': $preco,
      'description': 'Pedido $id',
      'externalReference': '$id',
      'creditCard': {
        'holderName': '" . @$data["titular"] . "',
        'number': '" . @$data["numcart"] . "',
        'expiryMonth': '" . @$data["mes-val"] . "',
        'expiryYear': '" . @$data["ano-val"] . "',
        'ccv': '" . @$data["cvv"] . "'
      },
      'creditCardHolderInfo': {
        'name': '" . @$data["nome"] . "',
        'email': '" . @$data["email"] . "',
        'cpfCnpj': '" . @$data["cpf"] . "',
        'postalCode': '" . @$data["cep"] . "',
        'addressNumber': '" . @$data["numero"] . "',
        'addressComplement': null,
        'mobilePhone': '" . @$data["ddd"] . @$data["telefone"] . "'
      }
    }";

    return $this->executar($dados['endpoint'], $dados['method'], $dados['json']);
  }

  public function cobrancaPix($data)
  {
    $id = uniqid();

    $preco = @$data["preco"] / 100;

    $dados["endpoint"] = $this->endpoint . "/api/v3/payments";
    $dados["method"] = "POST";
    $dados["json"] = "{
            'customer': '" . @$data["customer_id"] . "',
            'billingType': 'PIX',
            'dueDate': '" . @$data["vencimento"] . "',
            'value': " . $preco . ",
            'description': 'Pedido $id',
            'externalReference': '$id',
            'postalService': false
          }";

    return $this->executar($dados['endpoint'], $dados['method'], $dados['json']);
  }

  public function listarCobrancas()
  {
    $dados["endpoint"] = $this->endpoint . "/api/v3/payments";
    $dados["method"] = "GET";

    return $dados;
  }

  public function dadosCobranca($id_cobranca)
  {
    $dados["endpoint"] = $this->endpoint . "/api/v3/payments/$id_cobranca";
    $dados["method"] = "GET";

    return $this->executar($dados['endpoint'], $dados['method']);
  }

  public function codigoBoleto($id_cobranca)
  {
    $dados["endpoint"] = $this->endpoint . "/api/v3/payments/$id_cobranca/identificationField";
    $dados["method"] = "GET";

    return $this->executar($dados['endpoint'], $dados['method']);
  }

  public function qrCodePix($id_cobranca)
  {
    $dados["endpoint"] = $this->endpoint . "/api/v3/payments/$id_cobranca/pixQrCode";
    $dados["method"] = "GET";

    return $this->executar($dados['endpoint'], $dados['method']);
  }

  public function estornarCobranca($data)
  {
    if (@$data["forma_pagamento"] == 1) {
      $dados["endpoint"] = $this->endpoint . "/api/v3/installments/" . $data["nit_pix"] . "/refund";
    } elseif(@$data["forma_pagamento"] == 3) {
      $dados["endpoint"] = $this->endpoint . "/api/v3/payments/" . $data["transacao"] . "/refund";
    }

    $dados["method"] = "POST";
    $dados["json"] = "{}";

    // return $dados;
    return $this->executar($dados['endpoint'], $dados['method'], $dados['json']);
  }

  public function executar($url, $method = "GET", $json = null)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "$url",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "$method",
      CURLOPT_POSTFIELDS => $json,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        "access_token: $this->api_key",
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);
  }
}

// $asaas = new AsaasCobrancas();

// $data = array();
// $data["comissoes_pagamento"][0]["wallet_id"] = "2d21g1df44gff1";
// $data["comissoes_pagamento"][0]["comissao"] = 120.25;

// $data["comissoes_pagamento"][1]["wallet_id"] = "2d21g1d2d5d8wd";
// $data["comissoes_pagamento"][1]["comissao"] = 120.25;
// // var_dump($data);

// if(@$data["comissoes_pagamento"]){
//   $i = 0;
//   $split1 = "'split': [";
//   foreach($data["comissoes_pagamento"] as $comissoes){
//     $split = $asaas->split($comissoes, $i);
//     $i++;
//     $split1 .= "{'walletId': '" . $comissoes["wallet_id"] . "',";
//     $split1 .= "'fixedValue': ". $comissoes["comissao"] . "},";
//   }
//   $split1 .= "]";
// }
// $split = $asaas->split();

// var_dump($split1);

// $novoCliente = $asaas->novoCliente(null);
// $novoCliente = $asaas->listarClientes();
// $novoCliente = $asaas->dadosCliente("cus_000004981616");
// $novoCliente = $asaas->atualizarCliente(["id_cliente" => "cus_000004981616"]);

// $executar = $asaas->executar($novoCliente['endpoint'], $novoCliente['method'], $novoCliente['json']);
// $executar = $asaas->executar($novoCliente['endpoint'], $novoCliente['method']);
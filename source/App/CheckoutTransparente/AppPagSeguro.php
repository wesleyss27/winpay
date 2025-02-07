<?php

namespace Source\App\CheckoutTransparente;
// require '../vendor/autoload.php';

// $token = '749E4412AFE645D48C23CEF0B11C2733';
// $email = 'fhamonteiro@hotmail.com';
// $url = 'https://ws.pagseguro.uol.com.br/v2/transactions/?email=' . $email . '&token=' . $token;
// echo base64_encode($token.$key) . "<br><br>";

class AppPagSeguro
{
    public function executa($url, $metodo, $json)
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
            CURLOPT_CUSTOMREQUEST => "$metodo",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        // return json_decode($response);
    }

    function cliente($data)
    {
        $id = @$data["comprador"] ? $data["comprador"] : rand(100, 1000);
        $dados["url"] = "https://sandbox.moip.com.br/v2/customers/";
        $dados["json"] = '{  
      "ownId":"' . $id . '", 
      "fullname":"' . $data["nome"] . '",
      "email":"' . $data["email"] . '",
      "birthDate":"' . $data["nasc"] . '",
      "taxDocument":{  
         "type":"CPF",
         "number":"' . $data["cpf"] . '"
      },
      "phone":{  
         "countryCode":"55",
         "areaCode":"' . $data["ddd"] . '",
         "number":"' . $data["telefone"] . '"
      },
      "shippingAddress":{  
         "city":"' . $data["cidade"] . '",
         "district":"' . $data["bairro"] . '",
         "street":"' . $data["rua"] . '",
         "streetNumber":"' . $data["numero"] . '",
         "zipCode":"' . $data["cep"] . '",
         "state":"' . $data["estado"] . '",
         "country":"BRA"
      }
   }';

        return $dados;
    }

    function pedido($data)
    {
        $pedido = @$data["venda"] ? @$data["venda"] : rand(100000, 999999);
        $dados["url"] = "https://sandbox.moip.com.br/v2/orders/";
        $dados["json"] = '{  
        "ownId":"' . $pedido . '",
        "amount":{  
           "currency":"BRL"
        },
        "items":[  
           {  
              "product":"' . $data["nomekit"] . '",
              "category":"OTHER_CATEGORIES",
              "quantity":1,
              "detail":"' . $data["nomekit"] . '",
              "price":' . $data["preco"] . '
           }
        ],
        "customer":{  
           "id":"' . $data["customer_id"] . '"
        }
     }';

        return $dados;
    }

    function compraCartao($data)
    {
        $dados["url"] = "https://sandbox.moip.com.br/v2/orders/" . $data["order_id"] . "/payments";
        $dados["json"] = '{
      "installmentCount": ' . $data["parcelas"] . ',
      "statementDescriptor": "minhaLoja.com",
      "fundingInstrument": {
        "method": "CREDIT_CARD",
        "creditCard": {
          "number": "' . $data["numcart"] . '",
          "expirationMonth": "' . $data["mes-val"] . '",
          "expirationYear": "' . $data["ano-val"] . '",
          "cvc": "' . $data["cvv"] . '",
          "store": true,
          "holder": {
            "fullname": "' . $data["titular"] . '",
            "birthdate": "' . $data["nasc"] . '",
            "taxDocument": {
              "type": "CPF",
              "number": "' . $data["cpf"] . '"
            },
            "phone": {
              "countryCode": "55",
              "areaCode": "' . $data["ddd"] . '",
              "number": "' . $data["telefone"] . '"
            }
          }
        }
      }
   }';

        return $dados;
    }

    function compraBoleto($data)
    {
        // var_dump(@$data);
        // exit;

        $token = '749E4412AFE645D48C23CEF0B11C2733';
        $email = 'fhamonteiro@hotmail.com';
        $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions?email=' . $email . '&token=' . $token;


        $id_produto = $data["itemId1"];
        $produto = $data["itemDescription1"];
        $valor_produto = $data["itemAmount1"];
        $quantidade = $data["itemQuantity1"];
        $reference = $data["reference"]; ##Codigo de  referência da transação para vincular as transações da PagSeguro às vendas registradas na redrocket;
        $ddd = substr($data["senderPhone"], 0, 2);
        $telefone = substr($data["senderPhone"], 2, strlen($data["senderPhone"]));
        $nome = $data["senderName"];
        $email = $data["senderEmail"];
       
        $shippingAddressRequired = "true"; #Caso for true, deverá ser obrigatorio passar os parametros de endereço de entrega;

        $dados["paymentMethod"] = "boleto";
        $dados["paymentMode"] = "default";
        $dados["currency"] = "BRL";
        $dados["extraAmount"] = "0.00";
        $dados["itemId1"] = "$id_produto";
        $dados["itemDescription1"] = "$produto";
        $dados["itemAmount1"] = "$valor_produto";
        $dados["itemQuantity1"] = "$quantidade";
        $dados["reference"] = "$reference";
        $dados["senderName"] = "$nome";
        $dados["senderCPF"] = $data["senderCPF"];
        $dados["senderAreaCode"] = "$ddd";
        $dados["senderPhone"] = "$telefone";
        $dados["senderEmail"] = "comprador@sandbox.pagseguro.com.br";
        $dados["senderHash"] = "{$data["senderHash"]}";
        $dados["shippingAddressRequired"] = $shippingAddressRequired;

        if ($shippingAddressRequired == true) {
            $dados["shippingAddressStreet"] = "{$data["rua"]}";
            $dados["shippingAddressNumber"] = "{$data["numero"]}";
            $dados["shippingAddressComplement"] = "{$data["complemento"]}";
            $dados["shippingAddressDistrict"] = "{$data["bairro"]}";
            $dados["shippingAddressPostalCode"] = "{$data["cep"]}";
            $dados["shippingAddressCity"] = "{$data["cidade"]}";
            $dados["shippingAddressState"] = "{$data["estado"]}";
            $dados["shippingAddressCountry"] = "BRA";
            $dados["shippingType"] = 3;
            $dados["shippingCost"] = "{$data["frete"]}";
        }

        // var_dump($dados);
        // exit;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($dados),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        // return $response;
    }
}

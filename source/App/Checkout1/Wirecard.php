<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;

class Wirecard
{
    private $view;
    private $endpoint;
    private $token;
    private $key;

    public function __construct()
    {
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(1);
        if ($configuracaoCheckout->sandbox == 1) {
            $this->endpoint = "https://sandbox.moip.com.br";
            $this->token = $configuracaoCheckout->token_sandbox;
            $this->key = $configuracaoCheckout->chave_sandbox;
        } else {
            $this->endpoint = "https://api.moip.com.br";
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;
        }
    }

    public function executa($url, $metodo, $json = null)
    {
        
        $autorization = base64_encode($this->token . ":" . $this->key);

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
                "Authorization: Basic $autorization",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //  echo $response;
        return json_decode($response);
    }

    public function logs($data, $arq)
    {
        $arquivo = fopen(DIRETORIO . "logs/$arq.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        if ($data) {
            foreach ($data as $chave => $valor) {
                if ($chave == "numcart" || $chave == "cvv") {
                    $valor = "-";
                }

                if ($chave != "codigo_order_bump")
                    $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
            }
        } else {
            $texto .= "Erro dos dados";
        }
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
    }

    public function cliente($data)
    {
        // $id = rand(1000, 10000) * rand(10, 100);
        $id = uniqid();
        // $id = 1;

        if(@$data["endereco_obrigatorio"] == 0){
            $data["cep"] = "35010070";
            $data["rua"] = "Avenida Brasil";
            $data["numero"] = "3021";
            $data["complemento"] = "";
            $data["bairro"] = "Centro";
            $data["cidade"] = "Governador Valadares";
            $data["estado"] = "MG";
            $data["cep"] = "35010050";
        }
        
        $dados["url"] = $this->endpoint . "/v2/customers/";
        $dados["json"] = '{  
            "ownId":"' . $id . '", 
            "fullname":"' . @$data["nome"] . '",
            "email":"' . @$data["email"] . '",
            "birthDate":"' . @$data["nasc"] . '",
            "taxDocument":{  
                "type":"' . @$data["documento"] . '",
                "number":"' . @$data["cpf"] . '"
            },
            "phone":{  
                "countryCode":"55",
                "areaCode":"' . @$data["ddd"] . '",
                "number":"' . @$data["telefone"] . '"
            },
            "shippingAddress":{  
                "city":"' . @$data["cidade"] . '",
                "district":"' . @$data["bairro"] . '",
                "street":"' . @$data["rua"] . '",
                "streetNumber":"' . @$data["numero"] . '",
                "zipCode":"' . @$data["cep"] . '",
                "state":"' . @$data["estado"] . '",
                "country":"BRA"
            }
        }';

        $this->logs($dados, "wirecard-clientes");

        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function pedido($data)
    {
        // $pedido = rand(100000, 999999);
        $pedido = $id = uniqid();
        $dados["url"] = $this->endpoint . "/v2/orders/";
        $dados["json"] = '{  
            "ownId":"' . @$pedido . '",
            "amount":{  
            "currency":"BRL"
            },
            "items":[  
            {  
                "product":"' . @$data["nomekit"] . '",
                "category":"OTHER_CATEGORIES",
                "quantity":1,
                "detail":"' . @$data["nomekit"] . '",
                "price":' . @$data["preco"] . '
            }
            ],
            "customer":{  
            "id":"' . @$data["customer_id"] . '"
            }
        }';

        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraCartao($data){
        $nome_fatura = @$data["nome_fatura"] ? @$data["nome_fatura"] : "RedRocket";

        $dados["url"] = $this->endpoint . "/v2/orders/".$data["order_id"]."/payments";
        $dados["json"] = '{
            "installmentCount": '.@$data["parcela"].',
            "statementDescriptor": "' . $nome_fatura . '",
            "fundingInstrument": {
                "method": "CREDIT_CARD",
                "creditCard": {
                "number": "' . @$data["numcart"] . '",
                "expirationMonth": "' . @$data["mes-val"] . '",
                "expirationYear": "' . @$data["ano-val"] . '",
                "cvc": "' . @$data["cvv"] . '",
                "store": true,
                "holder": {
                    "fullname": "' . @$data["titular"] . '",
                    "birthdate": "' . @$data["nasc"] . '",
                    "taxDocument": {
                    "type": "' . @$data["documento"] . '",
                    "number": "' . @$data["cpf"] . '"
                    },
                    "phone": {
                    "countryCode": "55",
                    "areaCode": "' . @$data["ddd"] . '",
                    "number": "' . @$data["telefone"] . '"
                    }
                }
                }
            }
        }';

        // var_dump($dados);

        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraBoleto($data)
    {
        if (@$data["vencimento"] == "") {
            if (date("D") != "Sun" || date("D") != "Mon" || date("D") != "Tue") {
                $expiration_date = date("Y-m-d", strtotime("+5 days"));
            } else if (date("D") == "Sat") {
                $expiration_date = date("Y-m-d", strtotime("+4 days"));
            } else {
                $expiration_date = date("Y-m-d", strtotime("+3 days"));
            }
        } else {
            if (date("D", strtotime($data["vencimento"])) == "Sat") {
                $expiration_date = date("Y-m-d", strtotime("+2 days",strtotime($data["vencimento"])));
            } else if (date("D", strtotime($data["vencimento"])) == "Sun") {
                $expiration_date = date("Y-m-d", strtotime("+1 day",strtotime($data["vencimento"])));
            } else {
                $expiration_date = $data["vencimento"];
            }
        }
        
        $dados["url"] = $this->endpoint . "/v2/orders/".$data["order_id"]."/payments";
        $dados["json"] = '{
            "fundingInstrument": {
                "method": "BOLETO",
                "boleto": {
                "expirationDate": "' . @$expiration_date . '",
                "instructionsLines": {
                    "firt": "Atenção,",
                    "second": "fique atento à data de vencimento do boleto.",
                    "third": "Pague em qualquer casa lotérica."
                },
                "logoUri": "https://octapay.com.br/octapay/themes/tema2/assets/imgs/logo.png"
                }
            }
            }';

        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function reembolsoCartao($data)
    {
        $dados["url"] = $this->endpoint . "/v2/payments/" . $data["transacao"] . "/refunds";
        return $this->executa($dados["url"], "POST");
    }

    public function verPagamento($data)
    {
        $dados["url"] = $this->endpoint . "/v2/payments/".$data["transacao"];
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

        return $this->executa($dados["url"], "POST");
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
}

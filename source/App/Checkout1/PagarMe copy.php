<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class PagarMeCo
{
    private $view;
    private $endpoint;
    private $token;
    private $key;

    public function __construct()
    {
        // $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(5);
        // $configuracao = (new ConfiguracaoCheckout())->findById(1);
        // if ($configuracao->sandbox == 1) {
        //     $this->endpoint = "https://api.pagar.me/1/";
        //     $this->token = $configuracaoCheckout->token_sandbox;
        //     $this->key = $configuracaoCheckout->chave_sandbox;
        // } else {
        //     $this->endpoint = "https://api.pagar.me/1/";
        //     $this->token = $configuracaoCheckout->token_producao;
        //     $this->key = $configuracaoCheckout->chave_producao;
        // }
    }

    public function dados($data)
    {
        // var_dump($data);
        $adquirente = (new Adquirente())->findById(@$data);

        if (@$adquirente) {
            $this->endpoint = "https://api.pagar.me/1/";

            $this->token = $adquirente->token;
            $this->key = $adquirente->chave;
            // $this->email = $adquirente->email;
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
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        //  exit;
        return json_decode($response);
    }

    public function compraCartao($data)
    {
        $this->dados(@$data["adquirente"]);
        // exit;
        $dados["url"] = $this->endpoint . "transactions";
        $ano_val = substr(@$data["ano-val"], -2);
        $dados["json"] = '{
            "api_key": "' . $this->key . '",
            "async": false,
            "amount": ' . @$data["preco"] . ',
            "payment_method": "credit_card",
            "installments": ' . @$data["parcela"] . ',
            "card_number": "' . @$data["numcart"] . '",
            "card_cvv": "' . @$data["cvv"] . '",
            "card_expiration_date": "' . @$data["mes-val"] . @$ano_val . '",
            "card_holder_name": "' . @$data["titular"] . '",
            "customer": {
              "external_id": "' . uniqid() . '",
              "name": "' . @$data["nome"] . '",
              "type": "individual",
              "country": "br",
              "email": "' . @$data["email"] . '",
              "documents": [
                {
                  "type": "' . strtolower(@$data["documento"]) . '",
                  "number": "' . @$data["cpf"] . '"
                }
              ],
              "phone_numbers": ["+55' . @$data["ddd"] . @$data["telefone"] . '"],
              "birthday": "' . @$data["nasc"] . '"
            },
            "billing": {
              "name": "' . @$data["nome"] . '",
              "address": {
                "country": "br",
                "state": "' . @$data["estado"] . '",
                "city": "' . @$data["cidade"] . '",
                "neighborhood": "' . @$data["bairro"] . '",
                "street": "' . @$data["rua"] . '",
                "street_number": "' . @$data["numero"] . '",
                "zipcode": "' . @$data["cep"] . '"
              }
            },
            "shipping": {
              "name": "' . @$data["nome"] . '",
              "fee": 0,
              "expedited": true,
              "address": {
                "country": "br",
                "state": "' . @$data["estado"] . '",
                "city": "' . @$data["cidade"] . '",
                "neighborhood": "' . @$data["bairro"] . '",
                "street": "' . @$data["rua"] . '",
                "street_number": "' . @$data["numero"] . '",
                "zipcode": "' . @$data["cep"] . '"
              }
            },
            "items": [
              {
                "id": "' . uniqid() . '",
                "title": "' . @$data["nomekit"] . '",
                "unit_price": ' . @$data["preco"] . ',
                "quantity": 1,
                "tangible": true
              }
            ]
        }';

        // echo $dados["json"];
        // exit;
        // return $dados;
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraBoleto($data)
    {
        $this->dados(@$data["adquirente"]);
        $expiration_date = $data["vencimento"];
        $dados["url"] = $this->endpoint . "transactions";

        // $ano_val = substr(@$data["ano-val"], -2);
        $dados["json"] = '{
            "api_key": "' . $this->key . '",
            "async": false,
            "amount": ' . @$data["preco"] . ',
            "payment_method": "boleto",
            "installments": 1,
            "boleto_expiration_date": "' . @$expiration_date . '",
            "boleto_instructions": "Aceitar somente até a data de vencimento",
            "customer": {
              "name": "' . @$data["nome"] . '",
              "type": "individual",
              "country": "br",
              "email": "' . @$data["email"] . '",
              "document_number": "' . @$data["cpf"] . '",
              "birthday": "' . @$data["nasc"] . '"
            },
            "billing": {
              "name": "' . @$data["nome"] . '",
              "address": {
                "country": "br",
                "state": "' . @$data["estado"] . '",
                "city": "' . @$data["cidade"] . '",
                "neighborhood": "' . @$data["bairro"] . '",
                "street": "' . @$data["rua"] . '",
                "street_number": "' . @$data["numero"] . '",
                "zipcode": "' . @$data["cep"] . '"
              }
            },
            "shipping": {
              "name": "' . @$data["nome"] . '",
              "fee": 0,
              "expedited": true,
              "address": {
                "country": "br",
                "state": "' . @$data["estado"] . '",
                "city": "' . @$data["cidade"] . '",
                "neighborhood": "' . @$data["bairro"] . '",
                "street": "' . @$data["rua"] . '",
                "street_number": "' . @$data["numero"] . '",
                "zipcode": "' . @$data["cep"] . '"
              }
            },
            "items": [
              {
                "id": "' . uniqid() . '",
                "title": "' . @$data["nomekit"] . '",
                "unit_price": ' . @$data["preco"] . ',
                "quantity": 1,
                "tangible": true
              }
            ]
        }';

        // echo $dados["json"];
        // exit;
        // return $dados;
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraPix($data)
    {
        // API DE COMPRA COM PIX USA A VERSÃO v5
        $this->dados(@$data["adquirente"]);
        $expiration_date = $data["vencimento"];
        $dados["url"] = "https://api.pagar.me/core/v5/orders/";

        $dados["json"] = '{
                    "api_key": "' . $this->key . '",
                    "async": false,
                    "amount": ' . @$data["preco"] . ',
                    "payment_method": "boleto",
                    "installments": 1,
                    "boleto_expiration_date": "' . @$expiration_date . '",
                    "boleto_instructions": "Aceitar somente até a data de vencimento",
                    "customer": {
                      "name": "' . @$data["nome"] . '",
                      "type": "individual",
                      "country": "br",
                      "email": "' . @$data["email"] . '",
                      "document_number": "' . @$data["cpf"] . '",
                      "birthday": "' . @$data["nasc"] . '"
                    },
                    "billing": {
                      "name": "' . @$data["nome"] . '",
                      "address": {
                        "country": "br",
                        "state": "' . @$data["estado"] . '",
                        "city": "' . @$data["cidade"] . '",
                        "neighborhood": "' . @$data["bairro"] . '",
                        "street": "' . @$data["rua"] . '",
                        "street_number": "' . @$data["numero"] . '",
                        "zipcode": "' . @$data["cep"] . '"
                      }
                    },
                    "shipping": {
                      "name": "' . @$data["nome"] . '",
                      "fee": 0,
                      "expedited": true,
                      "address": {
                        "country": "br",
                        "state": "' . @$data["estado"] . '",
                        "city": "' . @$data["cidade"] . '",
                        "neighborhood": "' . @$data["bairro"] . '",
                        "street": "' . @$data["rua"] . '",
                        "street_number": "' . @$data["numero"] . '",
                        "zipcode": "' . @$data["cep"] . '"
                      }
                    },
                    "items": [
                      {
                        "id": "' . uniqid() . '",
                        "title": "' . @$data["nomekit"] . '",
                        "unit_price": ' . @$data["preco"] . ',
                        "quantity": 1,
                        "tangible": true
                      }
                    ]
                }';

        // echo $dados["json"];
        // exit;
        // return $dados;
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function reembolsoCartao($data)
    {
        $dados["url"] = $this->endpoint . "transactions/" . $data["transacao"] . "/refund";
        $dados["json"] = '{
            "api_key": "' . $this->key . '",
            "amount": "' . @$data["valor"] . '"
        }';

        return $this->executa($dados["url"], "POST", $dados["json"]);
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

<?php

namespace Source\App\CheckoutTransparente;

use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;
use Moip\Moip;
use Moip\Auth\BasicAuth;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class Wirecard
{
    private $view;
    private $endpoint;
    private $token;
    private $key;
    private $nome_fatura;

    public function __construct()
    {
    }

    public function dados($data)
    {
        $adquirente = (new Adquirente())->findById(@$data);
        if ($adquirente) {
            //URL SANDBOX

            if (@$adquirente->sandbox == 0)
                $this->endpoint = "https://api.moip.com.br";
            else
                $this->endpoint = "https://sandbox.moip.com.br";

            $this->token = $adquirente->token;
            $this->key = $adquirente->chave;
            $this->nome_fatura = @$adquirente->nome_fatura != "" ? @$adquirente->nome_fatura : "RedRocket";
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
        // echo $response;
        // var_dump($response);
        //  exit;
        // var_dump($json);
        return json_decode($response);
    }

    public function cliente($data)
    {
        $this->dados(@$data["adquirente"]);
        // $id = rand(1000, 10000) * rand(10, 100);
        $id = uniqid();
        // $id = 1;
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
        // var_dump($dados["json"]);
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function pedido($data)
    {
        $this->dados(@$data["adquirente"]);
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
        // var_dump($dados["json"]);
        // exit;
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraCartao($data)
    {
        $this->dados(@$data["adquirente"]);


        $dados["url"] = $this->endpoint . "/v2/orders/" . $data["order_id"] . "/payments";
        $dados["json"] = '{
            "installmentCount": ' . @$data["parcela"] . ',
            "statementDescriptor": "' . $this->nome_fatura . '",
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
        // echo $dados["url"] . "\n";
        // var_dump(@$dados["json"]);
        // exit;
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraBoleto($data)
    {

        $this->dados(@$data["adquirente"]);
        $expiration_date = $data["vencimento"];

        $dados["url"] = $this->endpoint . "/v2/orders/" . $data["order_id"] . "/payments";
        $dados["json"] = '{
            "fundingInstrument": {
                "method": "BOLETO",
                "boleto": {
                "expirationDate": "' . @$expiration_date . '",
                "instructionsLines": {
                    "firt": "Atenção,",
                    "second": "fique atento à data de vencimento do boleto.",
                    "third": "Pague em qualquer casa lotérica."
                    }
                }
            }
            }';

        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function reembolsoCartao($data)
    {
        $this->dados(@$data["adquirente"]);

        $dados["url"] = $this->endpoint . "/v2/payments/" . $data["transacao"] . "/refunds";
        return $this->executa($dados["url"], "POST");
    }

    public function verPagamento($data)
    {
        $this->dados(@$data["adquirente"]);

        $dados["url"] = $this->endpoint . "/v2/payments/" . $data["transacao"];
        return $this->executa($dados["url"], "GET");
    }

    public function reembolsoBoleto($data)
    {
        $this->dados(@$data["adquirente"]);


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
        $this->dados(@$data["adquirente"]);


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
        $this->dados(@$data["adquirente"]);


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
        $this->dados(@$data["adquirente"]);


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

    public function criarNotificacaoWebhook($data)
    {

        if (@$data["token"] && @$data["chave"]) {

            $this->token = @$data["token"];
            $this->key = @$data["chave"];

            // URL SANDBOX
            // $url = "https://sandbox.moip.com.br";

            // URL PRODUÇÃO
            $url = "https://api.moip.com.br";

            $dados["url"] = $url . "/v2/preferences/notifications#";

            $dados["json"] = '{
                "events": [
                "PAYMENT.AUTHORIZED",
                "PAYMENT.CANCELLED",
                "PAYMENT.REFUNDED",
                "PAYMENT.REVERSED"
                ], 
                "target" : "' . url("webhook") . '",
                "media" : "WEBHOOK"
            }';

            return $this->executa($dados["url"], "POST", $dados["json"]);
        }
    }

    public function consultaPedidos($data)
    {

        date_default_timezone_set("America/Recife");

        echo "
        <form method='get'>
        <h4>**************** CONSULTAS WIRECARD ****************</h4>
        Data inicial: 
        <input type='date' name='inicio'><br>
        
        Data final:
        <input type='date' name='fim'><br>
        
            <input type='submit' value='Enviar'>
            </form>
            ";
        echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";

        $status_transacaoW = array(
            "CREATED" => 1,
            "WAITING" => 1,
            "IN_ANALYSIS" => 2,
            "PRE_AUTHORIZED" => 3,
            "AUTHORIZED" => 4,
            "CANCELLED" => 5,
            "REFUNDED" => 6,
            "REVERSED" => 7,
            "SETTLED" => 8
        );

        if (@$_GET["inicio"] && @$_GET["fim"]) {
            $vendas = (new Venda())->find("meio_pagamento like '%moip%' AND transacao <> '' AND created_at BETWEEN '" . @$_GET["inicio"] . " 00:00:00' AND '" . @$_GET["fim"] . " 23:59:59'")->fetch(true);
        } else {
            $vendas = (new Venda())->find("meio_pagamento like '%moip%' AND transacao <> '' AND created_at BETWEEN '" . date("Y-m-d H:i:s", strtotime("-1 week")) . "' AND '" . date("Y-m-d H:i:s") . "'")->fetch(true);
        }

        // URL SANDBOX
        $endpoint_consulta = "https://sandbox.moip.com.br/v2/payments/";

        // URL PRODUÇÃO
        // $url = "https://sandbox.moip.com.br/v2/payments/";

        if (@$vendas) {
            foreach ($vendas as $venda) {
                $adquirente = (new Adquirente())->find("usuario = {$venda->produtor} and adquirente like '%wirecard%'")->fetch();
                $url = $endpoint_consulta . $venda->transacao;
                $this->key = @$adquirente->chave;
                $this->token = @$adquirente->token;
                $transacao = $this->executa($url, "GET");
                echo "<pre>";
                echo "********************************************************************************************************<br>";
                // echo "url: " . $url . "\n";
                echo "\n <b>KEY:</b> " . $this->key . "\n";
                echo "<b>TOKEN:</b> " . $this->token . "\n";
                echo "<b>URL:</b> $url<br>";
                echo "<b>Venda:</b> $venda->id<br>";
                echo "<b>NIT PIX:</b> $venda->nit_pix<br>";
                echo "<b>Status Transacao RedRocket:</b> $venda->status_transacao<br>";
                // echo "<b>Status Transacao no MercadoPago: </b>" . @$transacao->status . "<br>";
                echo "<b>Status Transacao na Wirecard: </b>" . @$status_transacaoW[@$transacao->status] . "<br>";
                // echo "<b>Data: </b>" . @$venda->created_at . "<br>";
                $status_transacao = $status_transacaoW[@$transacao->status];
                // exit;
                // $status_transacao = 1;
                if ((@$venda->status_transacao != @$status_transacao) && @$status_transacao != "") {
                    $venda->status_transacao = @$status_transacao;
                    if (@$transacao->updatedAt) {
                        $ultimo_evento = date("Y-m-d H:i:s", strtotime($transacao->updatedAt));
                        $venda->data_ultimo_evento = @$ultimo_evento;
                    }
                    // echo "ULTIMO EVENTO: " . @$ultimo_evento;
                    // exit;
                    if ($venda->status_transacao == 4)
                        $venda->data_finalizada = date("Y-m-d H:i:s");

                    if (@$venda->status_transacao == 5)
                        $venda->motivo = @$transacao->resource->payment->cancellationDetails->description;

                    //         echo "data finalizada: " . $venda->data_finalizada . "<br><br>";
                    echo "VENDA STATUS: " . $venda->status_transacao;
                    echo "\n STATUS TRANSACAO: " . $status_transacao;
                    if (@$venda->save()) {
                        echo "<br> atualizado com sucesso <br>";
                    } else {
                        echo "<br>" . $venda->fail()->getMessage() . "<br>";
                    }

                    $data["status_transacao"] = $status_transacao;
                    $data["venda"] = $venda->id;
                    (new AppProcessos(null))->processos($data);
                } else {
                    echo "<br><br> Não foi necessario atualizar <br>";
                }
            }
        }
    }
}

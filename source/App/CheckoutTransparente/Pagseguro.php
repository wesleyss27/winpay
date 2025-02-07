<?php

namespace Source\App\CheckoutTransparente;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class Pagseguro
{
    private $view;
    private $endpoint;
    private $token;
    private $key;
    private $nome_fatura;

    public function __construct()
    {
        // $sandbox = (new ConfiguracaoCheckout())->findById(1);
        // $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(6);

        // if ($sandbox->sandbox == 1) {
        //     $this->endpoint = "https://apisandbox.cieloecommerce.cielo.com.br/";
        //     $this->token = $configuracaoCheckout->token_sandbox;
        //     $this->key = $configuracaoCheckout->chave_sandbox;
        //     $this->email = $configuracaoCheckout->email;
        // } else {
        //     $this->endpoint = "https://api.cieloecommerce.cielo.com.br/";
        //     $this->token = $configuracaoCheckout->token_producao;
        //     $this->key = $configuracaoCheckout->chave_producao;
        //     $this->email = $configuracaoCheckout->email;
        // }
    }

    public function dados($data)
    {
        // var_dump($data);
        $adquirente = (new Adquirente())->findById(@$data);
        if (@$adquirente) {
            // var_dump(@$adquirente);
            if (@$adquirente->sandbox == 0)
                $this->endpoint = 'https://api.pagseguro.com/';
            else
                $this->endpoint = 'https://sandbox.api.pagseguro.com/';


            $this->token = @$adquirente->token;

            // $this->token = "1a029e44-b87e-439c-ab5f-242f902b43bd2e4f2f654ea1ac2b33ed237b038fd25f30bd-3d90-445b-a381-e9124c475092";

            $this->nome_fatura = @$adquirente->nome_fatura != "" ? @$adquirente->nome_fatura : "RedRocket";
            // $this->key = $adquirente->chave;
            // $this->email = $adquirente->email;
        }
    }

    public function executa($url, $metodo, $token = null, $json = null)
    {
        // echo $this->token;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => @$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $metodo,
            CURLOPT_POSTFIELDS =>  $json,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        // exit;
        return json_decode($response);
    }

    public function compraCartao($data)
    {
        $this->dados(@$data["adquirente"]);

        // $pedido = rand(100000, 999999);
        $dados["url"] = $this->endpoint . "orders";
        // echo "PEDIDO: $pedido";
        $dados["json"] = '{
            "customer": {
            "name": "' . @$data["nome"] . '",
            "email": "' . @$data["email"] . '",
            "tax_id": "' . @$data["cpf"] . '",
            "phones": [
                {
                    "country": "55",
                    "area": "' . @$data["ddd"] . '",
                    "number": "' .  @$data["telefone"] . '",
                    "type": "MOBILE"
                }
            ]
        },
        "items": [
            {
                "name": "' . @$data["nomekit"] . '",
                "quantity": 1,
                "unit_amount": ' . @$data["preco"]  . '
            }
        ],
        "qr_code": {
            "amount": {
                "value": ' . @$data["preco"] . '
            }
        },
        "shipping": {
            "address": {
                "street": "' . @$data["rua"] . '",
                "number": "' . @$data["numero"] . '",
                "locality": "' . @$data["bairro"] . '",
                "city": "' . @$data["cidade"] . '",
                "region_code": "' . @$data["estado"] . '",
                "country": "BRA",
                "postal_code": "' . @$data["cep"] . '"
            }
        },
        "charges": [
            {
                "description": "' . $this->nome_fatura . '",
                "amount": {
                    "value": ' . @$data["preco"] . ',
                    "currency": "BRL"
                },
                "payment_method": {
                    "type": "CREDIT_CARD",
                    "installments": ' . @$data["parcela"] . ',
                    "capture": true,
                    "card": {
                        "number": "' . @$data["numcart"] . '",
                        "exp_month": "' . @$data["mes-val"] . '",
                        "exp_year": "' . @$data["ano-val"] . '",
                        "security_code": "' . @$data["cvv"] . '",
                        "holder": {
                            "name": "' . @$data["titular"] . '"
                        }
                    }
                }
            }
        ],
        "notification_urls": [
            "https://pay.redrocketpay.com//webhook/pagseguro"
        ]}';
        // var_dump(@$dados["json"]);
        // exit;
        return $this->executa($dados["url"], "POST", $this->token, $dados["json"]);
    }

    public function compraBoleto($data)
    {
        $this->dados($data["adquirente"]);

        // $pedido = rand(100000, 999999);
        $expiration_date = $data["vencimento"];
        $dados["url"] = $this->endpoint . "charges";

        $dados["json"] = '{
            "amount": {
                "value": ' . @$data["preco"] . ',
                "currency": "BRL"
            },
            "payment_method": {
                "type": "BOLETO",
                "boleto": {
                    "due_date": "' . @$expiration_date . '",
                    "instruction_lines": {
                        "line_1": "Pagamento processado via PagSeguro"
                    },
                    "holder": {
                        "name": "' . @$data["nome"] . '",
                        "tax_id": "' . @$data["cpf"] . '",
                        "email": "' . @$data["email"] . '",
                        "address": {
                            "country": "Brasil",
                            "city": "' . @$data["cidade"] . '",
                            "region": "' . @$data["estado"] . '",
                            "region_code": "' . @$data["estado"] . '",
                            "postal_code": "' . @$data["cep"] . '",
                            "street": "' . @$data["rua"] . '",
                            "number": "' . @$data["numero"] . '",
                            "locality": "' . @$data["bairro"] . '"
                        }
                    }
                }
            },  
            "notification_urls": [
                "https://pay.redrocketpay.com//webhook/pagseguro"
            ]
         }';

        // var_dump(@$dados["json"]);
        // exit;
        return $this->executa($dados["url"], "POST", $this->token, $dados["json"]);
    }

    // public function reembolsoCartao($data)
    // {
    //     $dados["url"] = $this->endpoint . "/1/sales/" . $data["pay_id"] . "/void";
    //     return $this->executa($dados["url"], "PUT");
    // }

    public function compraPix($data)
    {
        $this->dados($data["adquirente"]);

        $dados["url"] = $this->endpoint . "orders";

        $dados["json"] = '{
            "customer": {
            "name": "' . @$data["nome"] . '",
            "email": "' . @$data["email"] . '",
            "tax_id": "' . @$data["cpf"] . '",
            "phones": [
                {
                    "country": "55",
                    "area": "' . @$data["ddd"] . '",
                    "number": "' .  @$data["telefone"] . '",
                    "type": "MOBILE"
                }
            ]
        },
        "items": [
            {
                "name": "' . @$data["nomekit"] . '",
                "quantity": 1,
                "unit_amount": ' . @$data["preco"] . '
            }
        ],
        "qr_codes": [
            {
            "amount": {
                "value": ' . @$data["preco"] . '
            }
        }
    ],
        "shipping": {
            "address": {
                "street": "' . @$data["rua"] . '",
                "number": "' . @$data["numero"] . '",
                "locality": "' . @$data["bairro"] . '",
                "city": "' . @$data["cidade"] . '",
                "region_code": "' . @$data["estado"] . '",
                "country": "BRA",
                "postal_code": "' . @$data["cep"] . '"
            }
        },
        "notification_urls": [
            "https://pay.redrocketpay.com//webhook/pagseguro"
        ]
    }';

        // echo $dados["json"];
        return $this->executa($dados["url"], "POST", $this->token, $dados["json"]);
    }

    // BUSCA O ID DA COBRANÇA QUE FOI GERADO NO PEDIDO - APENAS PARA CARTÃO E PIX
    // TRANSACAO EM BOLETO O ID DA COBRANÇA JÁ ESTÁ SALVO EM NIT_PIX
    public function buscarPedido($data)
    {
        $this->dados($data["adquirente"]);

        $dados["url"] = $this->endpoint . "orders/" . @$data["nit_pix"];
        // echo $dados["url"];
        // exit;
        return $this->executa($dados["url"], "GET", $this->token);
    }

    // APÓS BUSCAR ID DA COBRANÇA (CARTAO OU PIX) - FAZ O CHARGEBACK
    // CASO A VENDA TENHA SIDO NO BOLETO, O ID DA COBRANÇA JÁ ESTÁ SALVO EM NIT_PIX
    public function chargeback($data)
    {
        $this->dados($data["adquirente"]);

        $dados["url"] = $this->endpoint . "charges/" . @$data["charge_id"] . "/cancel";

        $dados["json"] = '{
          "amount": {
              "value": "' . @$data["valor_total"] . '"
          }
        }';
        // echo $dados["url"] . "\n";
        // echo $dados["json"];
        // exit;
        return $this->executa($dados["url"], "POST", $this->token, $dados["json"]);
    }

    public function consultaPedidos($data)
    {

        date_default_timezone_set("America/Recife");
        // Endpoint SANDBOX
        $endpoint = "https://sandbox.api.pagseguro.com/";
        // Endpoint Produção
        // $endpoint = "https://api.pagseguro.com/orders/";

        echo "
        <form method='get'>
        <h4>**************** CONSULTAS PAGSEGURO ****************</h4>
        Data inicial: 
            <input type='date' name='inicio'><br>

        Data final:
            <input type='date' name='fim'><br>
            
            <input type='submit' value='Enviar'>
            </form>
            ";
        echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";


        $status_transacaoP = array(
            "WAITING" => 1,
            "AUTHORIZED" => 3,
            "PAID" => 4,
            "DECLINED" => 5,
            "CANCELED" => 5
        );

        if (@$_GET["inicio"] && @$_GET["fim"]) {
            $vendas = (new Venda())->find("meio_pagamento like '%PagSeguro%' AND nit_pix <> '' AND created_at BETWEEN '" . @$_GET["inicio"] . " 00:00:00' AND '" . @$_GET["fim"] . " 23:59:59'")->fetch(true);
        } else {
            $vendas = (new Venda())->find("meio_pagamento like '%PagSeguro%' AND nit_pix <> '' AND created_at BETWEEN '" . date("Y-m-d H:i:s", strtotime("-1 week")) . "' AND '" . date("Y-m-d H:i:s") . "'")->fetch(true);
        }
        // var_dump($vendas);
        // exit;
        foreach ($vendas as $venda) {
            // var_dump($venda);
            if (@$venda->forma_pagamento == 2)
                $pedido = "charges/";
            else
                $pedido = "orders/";

            $adquirente = (new Adquirente())->find("usuario = {$venda->produtor} and adquirente like '%pagseguro%'")->fetch();
            $url = $endpoint . $pedido . @$venda->nit_pix;
            // echo "<br>";
            // echo $url;

            $transacao = $this->executa($url, "GET", @$adquirente->token);

            // echo $venda->forma_pagamento;
            // var_dump(@$transacao->status);
            // exit;
            if (@$venda->forma_pagamento == 2) {
                $status_transacao = @$status_transacaoP[@$transacao->status];
            } else {
                // QUANDO PAGAMENTO FOR PIX E NÃO EXISTIR "CHARGES" É PORQUE NÃO TEVE NENHUM PAGAMENTO DO PIX
                if (!@$transacao->charges[0]->id)
                    $status_transacao = 5;
                else
                    $status_transacao = @$status_transacaoP[@$transacao->charges[0]->status];
            }

            echo "<pre>";
            echo "********************************************************************************************************<br>";
            echo "URL: $url<br>";
            echo "Venda: $venda->id";
            echo "<b>NIT PIX:</b> $venda->nit_pix<br>";
            echo "<b>Status Transacao RedRocket:</b> $venda->status_transacao<br>";
            echo "<b>Status Transacao Pagseguro:</b> $status_transacao <br>";
            echo "<br>";
            if ((@$venda->status_transacao != @$status_transacao) && @$status_transacao != "") {
                $venda->status_transacao = $status_transacao;

                // BOLETO
                if (@$venda->forma_pagamento == 2)
                    $venda->data_ultimo_evento = $transacao->paid_at;
                // CARTÃO OU PIX - SÓ IRA TER ULTIMO EVENTO COM PIX CASO ELE TENHA SIDO PAGO - IRÁ EXISTIR "CHARGE"
                else if (@$transacao->charges[0]->id)
                    $venda->data_ultimo_evento = $transacao->charges[0]->paid_at;
                else
                    $venda->data_ultimo_evento = date("Y-m-d H:i:s");;

                if ($venda->status_transacao == 4)
                    $venda->data_finalizada = date("Y-m-d H:i:s");

                if (@$venda->status_transacao == 5 && $venda->forma_pagamento == 2)
                    $venda->motivo = $transacao->payment_response->message;
                // ABAIXO É PARA TRANSACAO COM PAGAMENTO CARTÃO OU PIX 
                else if (@$transacao->charges[0]->payment_response->message && @$status_transacao == 5)
                    $venda->motivo = $transacao->charges[0]->payment_response->message;
                else
                    $venda->motivo = "Tempo para prazo de pagamento expirado";


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

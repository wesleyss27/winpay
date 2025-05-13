<?php

namespace Source\App\Checkout1;

use DateTime;
use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class MercadoPago
{
    private $view;
    private $endpoint;
    private $access_token;
    private $public_key;
    private $nome_fatura;

    public function __construct()
    {
        error_log("Construtor MercadoPago chamado");
    }

    public function dados($data)
    {
        // var_dump($data);
        // $adquirente = (new Adquirente())->findById(@$data);
        $adquirente = (new ConfiguracaoCheckout())->findById(5);

        if (@$adquirente) {

            $this->endpoint = "https://api.mercadopago.com/v1/payments";
            //$this->public_key = $adquirente->chave;
            $this->public_key = $adquirente->chave_producao;
            //$this->access_token = $adquirente->token;
            $this->access_token = $adquirente->token_producao;
        }
    }

    public function executa($metodo, $json = null, $autorization = null)
    {
        error_log(json_encode($json, true));

        $curl = curl_init();
        
        $idempotencyKey = bin2hex(random_bytes(16));

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$this->endpoint",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "$metodo",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->access_token",
                'Content-Type: application/json',
                "X-Idempotency-Key: $idempotencyKey"
            ),
        ));
        
        $response = curl_exec($curl);
        //error_log(json_encode($response, true));
        curl_close($curl);
        echo $response;

        return json_decode($response);
    }

    public function buscarPedido($data)
    {
        $this->dados(@$data["adquirente"]);
        $this->endpoint = $this->endpoint . "/" . @$data["paymentID"];

        return $this->executa("GET");
    }

    public function compraCartao($data)
    {
        //error_log(json_encode($data, true));
        $url_webhook = url("checkout-transparente/mercadopago");
        // echo "PRECO: " . @$data["preco"] . "\n";
        // $preco = @$data["preco"] / 100;
        // echo "entrou compracartao";
        if (@$data["bandeira"] == "mastercard")
            $bandeira = "master";
        elseif (@$data["bandeira"])
            $bandeira = @$data["bandeira"];
        else
            $bandeira = "master";

        $this->dados($data["adquirente"]);

        $dados["json"] = '{
            "transaction_amount": ' . (@$data["preco"] / 100) . ',
            "token": "' . @$data["MPHiddenInputToken"] . '",
            "installments": ' . @$data["parcela"] . ',
            "payment_method_id": "' . @$bandeira  . '",
            "notification_url": "https://app.winpay.digital/webhook/mercadopago",
            "payer": {
                "first_name": "' . @$data["nome"] . '",
                "email": "' . @$data["email"] . '"
            }
        }';
        // var_dump(@$dados["json"]);
        // exit;
        return $this->executa("POST", $dados["json"]);
    }

    public function compraBoleto($data)
    {
        
        
        $url_webhook = url("checkout-transparente/mercadopago");

        $vencimento = date('Y-m-d\TH:i:s.vP', strtotime($data["vencimento"]));
        // echo "VENCIMENTO: " . $vencimento . "\n";
        $nome_completo = explode(" ", trim(@$data["nome"]));
        $first_name = array_shift($nome_completo);
        $last_name = array_pop($nome_completo);

        if (strlen(@$data["cpf"]) == 11) {
            $id_type = "CPF";
        } else if (strlen(@$data["cpf"]) == 14) {
            $id_type = "CNPJ";
        }

        $this->dados($data["adquirente"]);

        $dados["json"] = '{
            "transaction_amount": ' . (@$data["preco"] / 100) . ',
            "payment_method_id": "bolbradesco",
            "date_of_expiration": "' . @$vencimento . '",
            "notification_url": "https://app.winpay.digital/webhook/mercadopago",
            "payer": {
                "first_name": "' . @$first_name . '",
                "last_name": "' . @$last_name . '",
                "email": "' . @$data["email"] . '",
                "identification": {
                    "type": "' . @$id_type . '",
                    "number": "' . @$data["cpf"] . '"
                },
                "address": {
                    "zip_code": "' . @$data["cep"] . '",
                    "street_name": "' . @$data["rua"] . '",
                    "street_number": "' . @$data["numero"] . '",
                    "neighborhood": "' . @$data["bairro"] . '",
                    "city": "' . @$data["cidade"] . '",
                    "federal_unit": "' . @$data["estado"] . '"
                }
            },
            "additional_info": {
                "items": [
                {
                    "id": "' . @$data["id"] . '",
                    "title": "' . @$data["nome_produto"] . '",
                    "description": "' . @$data["descricao"] . '",
                    "picture_url": "' . @$data["foto"] . '",
                    "category_id": "' . @$data["categoria"] . '",
                    "quantity": 1,
                    "unit_price": ' . (@$data["preco"] / 100) . ',
                    "type": "",
                    "event_date": "",
                    "warranty": false,
                    "category_descriptor": {
                    "passenger": {},
                    "route": {}
                    }
                }
                ]
            }
        }';

        return $this->executa("POST", $dados["json"]);
    }

    public function compraPix($data)
    {
        $url_webhook = url("checkout-transparente/mercadopago");

        error_log("Construtor MercadoPago chamado em " . date('Y-m-d H:i:s') . " com valor: " . (@$data["preco"] / 100));

        $nome_completo = explode(" ", trim(@$data["nome"]));
        $first_name = array_shift($nome_completo);
        $last_name = array_pop($nome_completo);

        if (strlen(trim(@$data["cpf"])) == 11) {
            $id_type = "CPF";
        } else if (strlen(trim(@$data["cpf"])) == 14) {
            $id_type = "CNPJ";
        }

        $this->dados($data["adquirente"]);

        $dados["json"] = '{
            "transaction_amount": ' . (@$data["preco"] / 100) . ',
            "payment_method_id": "pix",
            "notification_url": "https://app.winpay.digital/webhook/mercadopago",
            "additional_info": {
                "items": [
                {
                    "id": "' . @$data["id"] . '",
                    "title": "' . @$data["nome_produto"] . '",
                    "description": "' . @$data["descricao"] . '",
                    "picture_url": "' . @$data["foto"] . '",
                    "quantity": 1,
                    "unit_price": ' . (@$data["preco"] / 100) . ',
                    "type": "electronics",
                    "event_date": "",
                    "warranty": false,
                    "category_descriptor": {
                    "passenger": {},
                    "route": {}
                    }
                }
                ]
            },
            "payer": {
                "first_name": "' . @$first_name . '",
                "last_name": "' . @$last_name . '",
                "email": "' . @$data["email"] . '",
                "identification": {
                    "type": "' . @$id_type . '",
                    "number": "' . @$data["cpf"] . '"
                },
                "address": {
                    "zip_code": "' . @$data["cep"] . '",
                    "street_name": "' . @$data["rua"] . '",
                    "street_number": "' . @$data["numero"] . '",
                    "neighborhood": "' . @$data["bairro"] . '",
                    "city": "' . @$data["cidade"] . '",
                    "federal_unit": "' . @$data["estado"] . '"
                }
            }  
        }';

        return $this->executa("POST", $dados["json"]);
    }

    public function reembolsoCartao($data)
    {

        $this->dados($data["adquirente"]);

        $this->endpoint = $this->endpoint . "/" . @$data["transacao"];

        $dados["json"] = '{
            "status": "cancelled"
        }';

        return $this->executa("PUT", $dados["json"]);
    }

    public function chargeback($data)
    {
        $this->dados($data["adquirente"]);

        $this->endpoint = $this->endpoint . "/" . @$data["transacao"] . "/refunds";
        // echo "endpoint: " . $this->endpoint;
        // exit;
        $dados["json"] = '{
            
        }';

        return $this->executa("POST", $dados["json"]);
    }

    public function consultaPedidos($data)
    {

        date_default_timezone_set("America/Recife");

        echo "
        <form method='get'>
        <h4>**************** CONSULTAS MERCADOPAGO ****************</h4>
        Data inicial: 
        <input type='date' name='inicio'><br>
        
        Data final:
        <input type='date' name='fim'><br>
        
            <input type='submit' value='Enviar'>
            </form>
            ";
        echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";

        $status_transacaoMP = array(
            "pending" => 1,
            "approved" => 4,
            "authorized" => 3,
            "in_process" => 2,
            "in_mediation" => 7,
            "rejected" => 5,
            "cancelled" => 5,
            "refunded" => 6,
            "chargedback" => 7
        );

        $msg_transacao = array(
            "cc_rejected_bad_filled_card_number" => "Numero do cartao invalido",
            "cc_rejected_bad_filled_date" => "Data de vencimento do cartao invalida",
            "cc_rejected_bad_filled_other" => "Dados do cartao invalidos",
            "cc_rejected_bad_filled_security_code" => "Codigo de segurança (CVV) invalido",
            "cc_rejected_blacklist" => "Nao foi possível o Mercado Pago processar o pagamento.",
            "cc_rejected_call_for_authorize" => "O comprador deve autorizar no banco emissor do cartao, o pagamento do valor ao Mercado Pago",
            "cc_rejected_card_disabled" => "O cartao utilizado esta inativado. Peça para o comprador ativar o cartao junto ao banco emissor",
            "cc_rejected_card_error" => "O Mercado Pago nao conseguiu processar o pagamento.",
            "cc_rejected_duplicated_payment" => "Pagamento duplicado - O Comprador ja efetuou um pagamento com esse valor. Utilize outro cartao ou outra forma de pagamento.",
            "cc_rejected_high_risk" => "Transacao de alto risco. Escolha outra forma de pagamento",
            "cc_rejected_insufficient_amount" => "Saldo insuficiente",
            "cc_rejected_invalid_installments" => "O cartao nao pode processar o pagamento no parcelamento escolhido",
            "cc_rejected_max_attempts" => "Foi atingido o limite maximo de tentativas permitidas. Escolha outro meio de pagamento",
            "cc_rejected_other_reason" => "O cartao escolhido nao consegue processar o pagamento. Escolha outro cartao ou meio de pagamento.",
            "cc_rejected_card_type_not_allowed" => "O cartao escolhido nao tem a funçao credito habilitada",
            "The customer can't be equal to the collector." => "Os dados do comprador não pode ser iguais ao do vendedor"
        );

        if (@$_GET["inicio"] && @$_GET["fim"]) {
            $vendas = (new Venda())->find("meio_pagamento like '%mercadopago%' AND nit_pix <> '' AND created_at BETWEEN '" . @$_GET["inicio"] . " 00:00:00' AND '" . @$_GET["fim"] . " 23:59:59'")->fetch(true);
        } else {
            $vendas = (new Venda())->find("meio_pagamento like '%mercadopago%' AND nit_pix <> '' AND created_at BETWEEN '" . date("Y-m-d H:i:s", strtotime("-1 week")) . "' AND '" . date("Y-m-d H:i:s") . "'")->fetch(true);
        }
        // echo "<pre>";
        // exit;
        $this->endpoint_consulta = "https://api.mercadopago.com/v1/payments/";

        if (@$vendas) {
            foreach ($vendas as $venda) {
                $adquirente = (new Adquirente())->find("usuario = {$venda->produtor} and adquirente like '%mercadopago%'")->fetch();
                $this->endpoint = $this->endpoint_consulta . @$venda->nit_pix;
                // $this->key = @$adquirente->chave;
                $this->access_token = @$adquirente->token;
                $transacao = $this->executa("GET");
                echo "<pre>";
                echo "********************************************************************************************************<br>";
                // echo "url: " . $url . "\n";
                // echo "\n <b>KEY:</b> " . $this->key . "\n";
                // echo "<b>TOKEN:</b> " . $this->access_token . "\n";
                // echo "<b>URL:</b> $url<br>";
                echo "<b>Venda:</b> $venda->id<br>";
                echo "<b>NIT PIX:</b> $venda->nit_pix<br>";
                echo "<b>Status Transacao RedRocket:</b> $venda->status_transacao<br>";
                // echo "<b>Status Transacao no MercadoPago: </b>" . @$transacao->status . "<br>";
                echo "<b>Status Transacao no MercadoPago: </b>" . @$status_transacaoMP[@$transacao->status] . "<br>";
                // echo "<b>Data: </b>" . @$venda->created_at . "<br>";
                $status_transacao = $status_transacaoMP[@$transacao->status];
                // exit;
                // $status_transacao = 1;
                if ((@$venda->status_transacao != @$status_transacao) && @$status_transacao != "") {
                    $venda->status_transacao = @$status_transacao;
                    if (@$transacao->date_last_updated) {
                        $ultimo_evento = date("Y-m-d H:i:s", strtotime($transacao->date_last_updated));
                        $venda->data_ultimo_evento = @$ultimo_evento;
                    }
                    // echo "ULTIMO EVENTO: " . @$ultimo_evento;
                    // exit;
                    if ($venda->status_transacao == 4)
                        $venda->data_finalizada = date("Y-m-d H:i:s");

                    if (@$venda->status_transacao == 5)
                        $venda->motivo = @$msg_transacao[@$transacao->status_detail];

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

<?php

namespace Source\App\CheckoutTransparente;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class Cielo
{
    private $view;
    private $endpoint;
    private $token;
    private $key;
    private $nome_fatura;
    private $boleto_cielo;

    public function __construct()
    {
    }

    public function dados($data)
    {
        // var_dump($data);
        $adquirente = (new Adquirente())->findById(@$data);

        if (@$adquirente) {
            // var_dump(@$adquirente);
            //PRODUÇÃO
            if (@$adquirente->sandbox == 0) {
                $this->endpoint = "https://api.cieloecommerce.cielo.com.br/";
                $this->endpoint_consulta = "https://apiquery.cieloecommerce.cielo.com.br/";
            } else {
                //SANDBOX
                $this->endpoint = "https://apisandbox.cieloecommerce.cielo.com.br/";
                $this->endpoint_consulta = "https://apiquerysandbox.cieloecommerce.cielo.com.br/";
            }

            $this->token = $adquirente->token;
            $this->key = $adquirente->chave;
            $this->nome_fatura = @$adquirente->nome_fatura != "" ? @$adquirente->nome_fatura : "RedRocket";
            $this->boleto_cielo = @$adquirente->boleto_cielo != "" ? @$adquirente->boleto_cielo : "Bradesco2";

            // $this->email = $adquirente->email;
        }
    }

    public function executa($url, $metodo, $json = null, $autorization = null)
    {

        if ($autorization == null)
            $autorization = base64_encode($this->key . ":" . $this->token);

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
                "MerchantKey: $this->key",
                "MerchantId: $this->token",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo "MerchantKey: $this->key";
        // echo "MerchantId: $this->token";
        // echo $response;
        // exit;
        return json_decode($response);
    }

    public function buscarPedido($paymentId)
    {
        $dados["url"] = $this->endpoint_consulta . "/1/sales/" . @$paymentId;
        return $this->executa($dados["url"], "GET");
    }

    public function compraCartao($data)
    {
        // echo "entrou compracartao";
        if (@$data["bandeira"] == "mastercard")
            $bandeira = "Master";
        elseif (@$data["bandeira"] == "")
            $bandeira = "Master";
        else
            $bandeira = @$data["bandeira"];
        // if (@$dsata["bandeira"] == "visa")
        // $bandeira = "Visa";
        // if (@$data["bandeira"] == "amex")
        // $bandeira = "Amex";
        // if (@$data["bandeira"] == "")
        // var_dump($data);
        // exit;
        $this->dados($data["adquirente"]);

        $pedido = rand(100000, 999999);
        $dados["url"] = $this->endpoint . "1/sales/";

        // echo "PEDIDO: $pedido";
        $dados["json"] = '{
            "MerchantOrderId": "' . @$pedido . '",
            "Customer": {
                "Name": "' . @$data["nome"] . '",
                "Email": "' . @$data["email"] . '",
                "Address": {
                    "Street": "' . @$data["rua"] . '",
                    "Number": "' . @$data["numero"] . '",
                    "Complement": "' . @$data["complemento"] . '",
                    "ZipCode": "' . @$data["cep"] . '",
                    "City": "' . @$data["cidade"] . '",
                    "State": "' . @$data["estado"] . '",
                    "Country": "BRA"
                },
                "DeliveryAddress": {
                    "Street": "' . @$data["rua"] . '",
                    "Number": "' . @$data["numero"] . '",
                    "Complement": "' . @$data["complemento"] . '",
                    "ZipCode": "' . @$data["cep"] . '",
                    "City": "' . @$data["cidade"] . '",
                    "State": "' . @$data["estado"] . '",
                    "Country": "BRA"
                }
            },
            "Payment": {
                "Currency": "BRL",
                "Country": "BRA",
                "Installments": ' . @$data["parcela"] . ',
                "Interest": "ByMerchant",
                "Capture": true,
                "Authenticate": false,
                "SoftDescriptor": "' . $this->nome_fatura . '",
                "CreditCard": {
                    "CardNumber": "' . @$data["numcart"] . '",
                    "Holder": "' . @$data["titular"] . '",
                    "ExpirationDate": "' . @$data["mes-val"] . '/' . @$data["ano-val"] . '",
                    "SecurityCode": "' . @$data["cvv"] . '",
                    "SaveCard": "false",
                    "Brand": "' . @$bandeira . '"
                },
                "Type": "CreditCard",
                "Amount": "' . @$data["preco"] . '"
            }
        }';
        // var_dump(@$dados["json"]);
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraBoleto($data)
    {

        $this->dados($data["adquirente"]);

        $pedido = rand(100000, 999999);
        $dados["url"] = $this->endpoint . "1/sales/";
        $expiration_date = $data["vencimento"];

        // echo "PEDIDO: $pedido";
        $dados["json"] = '{
            "MerchantOrderId": "' . @$pedido . '",
            "Customer": {
                "Name": "' . @$data["nome"] . '",
                "Email": "' . @$data["email"] . '",
                "Identity": "' . @$data["cpf"] . '",
                "Address": {
                    "Street": "' . @$data["rua"] . '",
                    "Number": "' . @$data["numero"] . '",
                    "Complement": "' . @$data["complemento"] . '",
                    "ZipCode": "' . @$data["cep"] . '",
                    "City": "' . @$data["cidade"] . '",
                    "State": "' . @$data["estado"] . '",
                    "District": "' . @$data["bairro"] . '",
                    "Country": "BRA"
                }
            },
            "Payment": {
                "Type": "Boleto",
                "Amount": "' . @$data["preco"] . '",
                "Provider": "' . $this->boleto_cielo . '",
                "ExpirationDate": "' . @$expiration_date . '"
                }
        }';
        // var_dump(@$dados["json"]);
        // exit;
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function reembolsoCartao($data)
    {
        $this->dados($data["adquirente"]);

        $dados["url"] = $this->endpoint . "/1/sales/" . $data["pay_id"] . "/void";
        return $this->executa($dados["url"], "PUT");
    }

    public function consultaPedidos($data)
    {

        //PRODUÇÃO
        $this->endpoint_consulta = "https://apiquery.cieloecommerce.cielo.com.br/1/sales/";

        //SANDBOX
        // $this->endpoint_consulta = "https://apiquerysandbox.cieloecommerce.cielo.com.br/1/sales/";
        date_default_timezone_set("America/Recife");

        echo "
        <form method='get'>
        <h4>**************** CONSULTAS CIELO ****************</h4>
        Data inicial: 
            <input type='date' name='inicio'><br>

        Data final:
            <input type='date' name='fim'><br>
            
            <input type='submit' value='Enviar'>
            </form>
            ";
        echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";

        $status_transacaoC = array(
            "0" => 1,
            "1" => 3,
            "2" => 4,
            "3" => 5,
            "10" => 6,
            "11" => 6,
            "12" => 1,
            "13" => 5,
            "20" => 1,
        );

        if (@$_GET["inicio"] && @$_GET["fim"]) {
            $vendas = (new Venda())->find("meio_pagamento like '%cielo%' AND nit_pix <> '' AND created_at BETWEEN '" . @$_GET["inicio"] . " 00:00:00' AND '" . @$_GET["fim"] . " 23:59:59'")->fetch(true);
        } else {
            $vendas = (new Venda())->find("meio_pagamento like '%cielo%' AND nit_pix <> '' AND created_at BETWEEN '" . date("Y-m-d H:i:s", strtotime("-1 week")) . "' AND '" . date("Y-m-d H:i:s") . "'")->fetch(true);
        }

        // var_dump(@$vendas);
        // exit;
        if (@$vendas) {
            foreach ($vendas as $venda) {
                $adquirente = (new Adquirente())->find("usuario = {$venda->produtor} and adquirente like '%cielo%'")->fetch();
                $url = $this->endpoint_consulta . @$venda->nit_pix;
                $this->key = @$adquirente->chave;
                $this->token = @$adquirente->token;
                $transacao = $this->executa($url, "GET", @$adquirente->token);
                echo "<pre>";
                echo "********************************************************************************************************<br>";
                echo "<b>URL:</b> $url<br>";
                echo "<b>Venda:</b> $venda->id<br>";
                echo "<b>NIT PIX:</b> $venda->nit_pix<br>";
                echo "<b>Status Transacao RedRocket:</b> $venda->status_transacao<br>";
                echo "<b>Status Transacao Na Cielo: </b>" . $status_transacaoC[$transacao->Payment->Status];
                $status_transacao = $status_transacaoC[$transacao->Payment->Status];
                // $status_transacao = 1;
                if ((@$venda->status_transacao != $status_transacao) && @$status_transacao != "") {
                    $venda->status_transacao = $status_transacao;

                    if (@$transacao->Payment->CapturedDate)
                        $venda->data_ultimo_evento = $transacao->Payment->CapturedDate;

                    if ($venda->status_transacao == 4)
                        $venda->data_finalizada = date("Y-m-d H:i:s");

                    if (@$venda->status_transacao == 5)
                        $venda->motivo = $transacao->Payment->ReturnMessage;

                    //         echo "data finalizada: " . $venda->data_finalizada . "<br><br>";
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

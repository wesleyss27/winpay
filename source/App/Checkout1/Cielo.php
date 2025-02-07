<?php

namespace Source\App\Checkout1;

use Source\App\Postback\AppPostback;
use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;

class Cielo
{
    private $view;
    private $endpoint;
    private $endpoint_consulta;
    private $token;
    private $key;

    public function __construct()
    {
        $sandbox = (new ConfiguracaoCheckout())->findById(1);
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(7);
        if ($sandbox->sandbox == 1) {
            $this->endpoint = "https://apisandbox.cieloecommerce.cielo.com.br/";
            $this->endpoint_consulta = "https://apiquerysandbox.cieloecommerce.cielo.com.br/";
            $this->token = $configuracaoCheckout->token_sandbox;
            $this->key = $configuracaoCheckout->chave_sandbox;
            $this->email = $configuracaoCheckout->email;
        } else {
            $this->endpoint = "https://api.cieloecommerce.cielo.com.br/";
            $this->endpoint_consulta = "https://apiquery.cieloecommerce.cielo.com.br/";
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;
            $this->email = $configuracaoCheckout->email;
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
        // echo $response;
        return json_decode($response);
    }

    public function buscarPedido($paymentId)
    {
        $dados["url"] = $this->endpoint_consulta . "/1/sales/" . $paymentId;
        return $this->executa($dados["url"], "GET");
    }

    public function compraCartao($data)
    {
        $pedido = rand(100000, 999999);
        $dados["url"] = $this->endpoint . "1/sales/";

        $nome_fatura = @$data["nome_fatura"] ? @$data["nome_fatura"] : "RedRocket";
        $dados["json"] = '{
            "MerchantOrderId": "' . $pedido . '",
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
                "SoftDescriptor": "' . $nome_fatura . '",
                "CreditCard": {
                    "CardNumber": "' . @$data["numcart"] . '",
                    "Holder": "' . @$data["titular"] . '",
                    "ExpirationDate": "' . @$data["mes-val"] . '/' . @$data["ano-val"] . '",
                    "SecurityCode": "' . @$data["cvv"] . '",
                    "SaveCard": "false",
                    "Brand": "Visa"
                },
                "Type": "CreditCard",
                "Amount": ' . @$data["preco"] . '
            }
        }';

        // echo $dados["json"];
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function compraCartaoSimples($data)
    {
        // echo "entrou compracartao";
        if (@$data["bandeira"] == "mastercard")
            $bandeira = "Master";
        else
            $bandeira = @$data["bandeira"];
        // if (@$data["bandeira"] == "visa")
        //     $bandeira = "Visa";
        // if (@$data["bandeira"] == "amex")
        //     $bandeira = "Amex";
        // if (@$data["bandeira"] == "")
        //     var_dump($data);
        // exit;

        $pedido = rand(1000000, 9999999);
        $parcela = @$data["parcela"] ? @$data["parcela"] : 1;
        $dados["url"] = $this->endpoint . "1/sales/";
        // echo "PEDIDO: $pedido";
        $dados["json"] = '{
            "MerchantOrderId": "' . @$pedido . '",
            "Customer": {
                "Name": "' . @$data["nome"] . '",
            },
            "Payment": {
                "Currency": "BRL",
                "Country": "BRA",
                "Type": "CreditCard",
                "Amount": ' . @$data["preco"] . ',
                "Installments": ' . @$parcela . ',
                "Interest": "ByMerchant",
                "Capture": true,
                "Authenticate": false,
                "SoftDescriptor": "RedRocket",
                "CreditCard": {
                    "CardNumber": "' . @$data["numcart"] . '",
                    "Holder": "' . @$data["titular"] . '",
                    "ExpirationDate": "' . @$data["mes-val"] . '/' . @$data["ano-val"] . '",
                    "SecurityCode": "' . @$data["cvv"] . '",
                    "Brand": "Visa"
                }
            }
        }';

        $arquivo = fopen(DIRETORIO . "logs/cielo-dados.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        $texto .= $dados["json"];
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);

        // echo $dados["json"];
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function reembolsoCartao($data)
    {
        $dados["url"] = $this->endpoint . "/1/sales/" . $data["pay_id"] . "/void";
        return $this->executa($dados["url"], "PUT");
    }

    public function pix($data)
    {
        $pedido = rand(100000, 999999) * rand(100, 9999);
        $dados["url"] = $this->endpoint . "/apipix/transacao";

        $dados["json"] = '{
            "merchant_id": "' . $this->key . '",
            "meio_pagamento": "1200",
            "pedido": {
                "numero": "PIX-' . $pedido . '",
                "valor": ' . @$data["preco"] . ',
                "descricao": "' . @$data["nome_produto"] . ' / ' . @$data["nomekit"] . '",
                "expiracao": 86400,
                "formato": 3
            },
            "comprador": {
                "nome": "' . @$data["nome"] . '",
                "documento": "' . @$data["cpf"] . '",
                "endereco": {
                    "cep": "' . @$data["cep"] . '",
                    "logradouro": "' . @$data["rua"] . '",
                    "numero": "' . @$data["numero"] . '",
                    "complemento": "' . @$data["numero"] . '",
                    "bairro": "' . @$data["bairro"] . '",
                    "cidade": "' . @$data["cidade"] . '",
                    "uf": "' . @$data["estado"] . '"
                }
            }
        }';

        // echo $dados["json"];
        return $this->executa($dados["url"], "POST", $dados["json"]);
    }

    public function consultaPedidos($data)
    {
        echo "
            <form method='get'>
                Data inicial: 
                <input type='date' name='inicio'><br>

                Data final:
                <input type='date' name='fim'><br>

                <input type='submit' value='Enviar'>
            </form>
        ";

        echo "<pre>";
        echo "<h1>Consulta pedidos Cielo</h1>";
        echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";
        echo "<br>---------------------------------------<br>";

        if (@$_GET["inicio"] && @$_GET["fim"]) {
            $dataInicial = $_GET["inicio"] . " 00:00:00";
            $dataFinal = $_GET["fim"] . " 23:59:59";
        } else {
            $dataInicial = date("Y-m-d 00:00:00", strtotime("-6 days", strtotime(date("Y-m-d H:i"))));
            $dataFinal = date("Y-m-d H:i:s");
        }

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

        $vendas = (new Venda())->find("meio_pagamento = 'Cielo' AND created_at BETWEEN '$dataInicial' AND '$dataFinal'")->fetch(true);

        if ($vendas) {
            foreach ($vendas as $venda) {
                $url = $this->endpoint_consulta . "/1/sales/" . $venda->nit_pix;
                $busca = $this->executa($url, "GET");

                echo "<b>Venda: $venda->id </b><br>";
                echo "PaymentId: {$busca->Payment->PaymentId}<br>";
                echo "Status: {$busca->Payment->Status} - venda $venda->status_transacao<br>";
                // var_dump($busca);
                if ($busca && @$status_transacaoC[@$busca->Payment->Status] != @$venda->status_transacao) {
                    echo "<br>Venda encontrada: $venda->id <br>";
                    echo "<br>Venda encontrada: {$busca->Payment->Status} <br>";
                    // echo $status_transacaoC[$busca->Payment->Status];
                    // $ven = (new Venda())->findById($venda->id);
                    $venda->status_transacao = @$status_transacaoC[@$busca->Payment->Status];

                    if ($status_transacaoC[$busca->Payment->Status] == 4)
                        $venda->data_finalizada = date("Y-m-d H:i:s");

                    if ($status_transacaoC[$busca->Payment->Status] == 5)
                        $venda->motivo = $busca->Payment->ReturnMessage;

                    if($venda->save()){
                        echo "Venda atualizada $venda->status_transacao";

                        $data["venda"] = $venda->id;
                        $data["produto"] = $venda->produto;
                        $data["produtor"] = $venda->produtor;

                        (new AppPostback(null))->enviaPostback($data);
                    } else {
                        echo $venda->fail()->getMessage();
                    }
                    echo "<br>";
                }
                echo "------------------------------------------------<br>";
                echo "<br>";
            }
        }
        
    }
}

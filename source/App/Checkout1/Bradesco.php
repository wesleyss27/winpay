<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;

class Bradesco
{
    private $view;
    private $endpoint;
    private $token;
    private $key;

    public function __construct()
    {
        $sandbox = (new ConfiguracaoCheckout())->findById(1);
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(3);
        if ($sandbox->sandbox == 1) {
            $this->endpoint = "https://homolog.meiosdepagamentobradesco.com.br/";
            $this->token = $configuracaoCheckout->token_sandbox;
            $this->key = $configuracaoCheckout->chave_sandbox;
            $this->email = $configuracaoCheckout->email;
        } else {
            $this->endpoint = "https://meiosdepagamentobradesco.com.br/";
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;
            $this->email = $configuracaoCheckout->email;
        }
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
                "Authorization: Basic $autorization",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;

        $data["resposta"] = $response;
        $this->logs($data, "bradesco-resposta");
        return json_decode($response);
    }

    public function boleto($data)
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
                $expiration_date = date("Y-m-d", strtotime("+2 days", strtotime($data["vencimento"])));
            } else if (date("D", strtotime($data["vencimento"])) == "Sun") {
                $expiration_date = date("Y-m-d", strtotime("+1 day", strtotime($data["vencimento"])));
            } else {
                $expiration_date = $data["vencimento"];
            }
        }

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

        $pedido = rand(100000, 999999);
        $dados["url"] = $this->endpoint . "/apiboleto/transacao";

        $dados["json"] = '{
            "merchant_id": "' . $this->key . '",
            "meio_pagamento": "300",
            "pedido": {
                "numero": "BRA-' . $pedido . '",
                "valor": ' . @$data["preco"] . ',
                "descricao": "' . @$data["nome_produto"] . ' / ' . @$data["nomekit"] . '"
            },
            "comprador": {
                "nome": "' . @$data["nome"] . '",
                "documento": "' . @$data["cpf"] . '",
                "endereco": {
                    "cep": "' . @$data["cep"] . '",
                    "logradouro": "' . @$data["rua"] . '",
                    "numero": "' . @$data["numero"] . '",
                    "complemento": "' . @$data["complemento"] . '",
                    "bairro": "' . @$data["bairro"] . '",
                    "cidade": "' . @$data["cidade"] . '",
                    "uf": "' . @$data["estado"] . '"
                }
            },
            "boleto": {
                "beneficiario": "Scopus",
                "carteira": "26",
                "nosso_numero": "' . $pedido . '",
                "data_emissao": "' . date("Y-m-d") . '",
                "data_vencimento": "' . $expiration_date . '",
                "valor_titulo": ' . @$data["preco"] . ',
                "instrucoes": {
                    "instrucao_linha_5": "Produto",
                    "instrucao_linha_6": "' . @$data["nome_produto"] . ' / ",
                    "instrucao_linha_7": "' . @$data["nomekit"] . '"
                }
            }
        }';

        $arquivo = fopen(DIRETORIO . "logs/bradesco-dados.txt", 'a+');

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

    public function pix($data)
    {
        // $pedido = rand(100000, 999999) * rand(100, 9999);
        $dados["url"] = $this->endpoint . "/apipix/transacao";

        $dados["json"] = '{
            "merchant_id": "' . $this->key . '",
            "meio_pagamento": "1200",
            "pedido": {
                "numero": "' . @$data["pedido"] . '",
                "valor": ' . @$data["preco"] . ',
                "descricao": "' . trim(@$data["nome_produto"]) . ' / ' . @trim(@$data["nomekit"]) . '",
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

    public function consultaPix($data)
    {
        echo "<pre>";
        echo "<h1>Consulta pedidos Bradesco</h1>";
        echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";

        $autorization = base64_encode($this->email . ":" . $this->token);
        $url = $this->endpoint . "SPSConsulta/Authentication/" . $this->key;

        $autenticacao = $this->executa($url, "GET", null, $autorization);
        // var_dump($autenticacao);

        $status = array(
            120 => 1,
            121 => 4,
            122 => 4,
            123 => 4,
            124 => 5,
            125 => 5,
            126 => 6,
            127 => 6
        );

        $vendas = (new Venda())->find("meio_pagamento = 'BradescoPix'")->fetch(true);

        if ($vendas) {
            foreach ($vendas as $venda) {
                echo "Venda: $venda->id <br>";
                echo "Transacao: $venda->transacao <br>";

                $url_consulta = $this->endpoint . "SPSConsulta/GetOrderById/$this->key?token={$autenticacao->token->token}&orderId=$venda->transacao";
                $pedidos = $this->executa($url_consulta, "GET", null, $autorization);
                echo $url_consulta . "<br>";

                echo "Código de retorno: " . $pedidos->status->codigo . "<br>";
                echo "Mensagem de retorno: " . $pedidos->status->mensagem . "<br><br>";

                if ($pedidos->status->codigo == 0) {
                    if ($status[$pedidos->pedidos[0]->status] != $venda->status_transacao) {
                        $venda->status_transacao = $status[$pedidos->pedidos[0]->status];
                        $venda->data_ultimo_evento = date("Y-m-d H:i:s");

                        if ($venda->status_transacao == 4) {
                            $venda->data_finalizada = date("Y-m-d H:i:s");
                        }

                        if ($venda->save()) {
                            echo "Venda atualizada com sucesso<br>";
                        } else {
                            echo $venda->fail()->getMesage();
                        }

                        $data["venda"] = $venda->id;
                        (new AppProcessos(null))->processos($data);
                    }
                }
                echo "********************************************************************************<br><br>";
            }
        }
    }

    public function consultaPixPedido($data)
    {
        $autorization = base64_encode($this->email . ":" . $this->token);
        $url = $this->endpoint . "SPSConsulta/Authentication/" . $this->key;

        $autenticacao = $this->executa($url, "GET", null, $autorization);

        $status = array(
            120 => 1,
            121 => 4,
            122 => 4,
            123 => 4,
            124 => 5,
            125 => 5,
            126 => 6,
            127 => 6
        );

        $venda = (new Venda())->findById($data["venda"]);

        if ($venda) {
            $url_consulta = $this->endpoint . "SPSConsulta/GetOrderById/$this->key?token={$autenticacao->token->token}&orderId=$venda->transacao";
            $pedidos = $this->executa($url_consulta, "GET", null, $autorization);

            if ($pedidos->status->codigo == 0) {
                if ($status[$pedidos->pedidos[0]->status] != $venda->status_transacao) {
                    $venda->status_transacao = $status[$pedidos->pedidos[0]->status];
                    $venda->data_ultimo_evento = date("Y-m-d H:i:s");

                    if ($venda->status_transacao == 4) {
                        $venda->data_finalizada = date("Y-m-d H:i:s");
                    }

                    $venda->save();

                    (new AppProcessos(null))->processos($data);
                }
            }
        }
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

        $autorization = base64_encode($this->email . ":" . $this->token);
        $url = $this->endpoint . "SPSConsulta/Authentication/" . $this->key;

        $autenticacao = $this->executa($url, "GET", null, $autorization);

        if (@$_GET["inicio"] && @$_GET["fim"]) {
            $dataInicial = date("Y/m/d%20H:i", strtotime($_GET["inicio"]));
            $dataFinal = date("Y/m/d%20H:i", strtotime($_GET["fim"]));
        } else {
            $dataInicial = date("Y/m/d%20H:i", strtotime("-6 days", strtotime(date("Y-m-d H:i"))));
            $dataFinal = date("Y/m/d%20H:i");
        }


        $url_consulta = $this->endpoint . "SPSConsulta/GetOrderListPayment/$this->key/boleto?token={$autenticacao->token->token}&dataInicial=$dataInicial&dataFinal=$dataFinal&offset=1&limit=1500";

        $pedidos = $this->executa($url_consulta, "GET", null, $autorization);

        $status = array(
            10 => 1,
            13 => 1,
            14 => 1,
            15 => 1,
            21 => 4,
            22 => 4,
            23 => 4
        );
        echo "<pre>";
        echo "<h1>Consulta pedidos Bradesco</h1>";
        echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";
        echo $url_consulta . "<br>";
        echo "<br>---------------------------------------<br>";

        foreach ($pedidos->pedidos as $pedido) {
            // var_dump($pedido->numero);
            // echo "<br>";
            $venda = (new Venda())->find("transacao = '$pedido->numero'")->fetch();

            if (@$venda && @$pedido->status == 21 && $venda->status_transacao != @$status[$pedido->status]) {
                $venda->status_transacao = @$status[$pedido->status];
                $venda->data_ultimo_evento = dataHoraReversa($pedido->dataPagamento);
                $venda->data_finalizada = dataHoraReversa($pedido->dataPagamento);

                echo "data finalizada: " . $venda->data_finalizada . "<br><br>";
                // var_dump($pedido);

                if ($venda->save()) {
                    echo "<br> atualizado com sucesso <br>";
                } else {
                    echo "<br>" . $venda->fail()->getMessage() . "<br>";
                }

                $data["status_transacao"] = @$status[$pedido->status];
                $data["venda"] = $venda->id;
                (new AppProcessos(null))->processos($data);

                echo "---------------------------------------<br>";
            }
        }
    }
}

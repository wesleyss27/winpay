<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;

class Sitef
{
    private $view;
    private $endpoint_pix;
    private $token_pix;
    private $key_pix;

    public function __construct()
    {
        $sandbox = (new ConfiguracaoCheckout())->findById(1);
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(2);
        if ($sandbox->sandbox == 1) {
            $this->endpoint_pix = "https://esitef-homologacao.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoCheckout->token_sandbox;
            $this->key_pix = $configuracaoCheckout->chave_sandbox;
            $this->email = $configuracaoCheckout->email;
        } else {
            $this->endpoint_pix = "https://esitef-ec.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoCheckout->token_producao;
            $this->key_pix = $configuracaoCheckout->chave_producao;
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
                "Authorization: Basic $autorization",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
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

    public function dadosPix($data)
    {
        $json = '{
            "merchant_usn":"' . $data["venda"] . '",
            "order_id":"' . $data["venda"] . '",
            "installments":"1",
            "installment_type":"4",
            "authorizer_id":"440",
            "amount":"' . $data["valor_bruto"] * 100 . '",
            "additional_data":{
               "pix_psp":"",
               "additional_data":{
                     "pix_data":[
                         {
                         "payer":{
                             "name":"' . @$data["nome"] . '",
                             "email":"' . @$data["email"] . '",
                             "city":"' . @$data["cidade"] . '",
                             "state":"' . @$data["estado"] . '",
                             "address_street_name":"' . @$data["rua"] . '",
                             "address_street_number":"' . @$data["numero"] . '",
                             "address_zip_code":"' . @$data["cep"] . '",
                             "address_street_complement":"' . @$data["complemento"] . '",
                             "address_country":"BRA"
                         },
                         "items":[
                             {
                             "description":"' . @$data["nomekit"] . '",
                             "quantity":"' . @$data["quantidade"] . '",
                             "unit_price":"' . @$data["valor_bruto"] . '",
                             "quantity_type":"un"
                             }
                         ]
                     }
                 ]
             }
         }
         }';

        $urlTransacao = $this->endpoint_pix . "transactions";
        return $this->curlPix($urlTransacao, "POST", $json);
    }

    public function efetivacaoTransacao($nit)
    {
        $urlEfetivacao = $this->endpoint_pix . "payments/{$nit}";
        return $this->curlPix($urlEfetivacao, "POST", "{}");
    }

    public function consultarPix($data)
    {
        $nit = $data["nit"];
        $url = $this->endpoint_pix . "transactions/$nit";
        $consulta = $this->curlPix($url, "GET", null);

        $venda = (new Venda())->findById($data["venda"]);

        $status = array(
            "NOV" => 1,
            "INV" => 5,
            "PPC" => 1,
            "PPN" => 1,
            "PEN" => 1,
            "CON" => 4,
            "NEG" => 5,
            "ERR" => 5,
            "BLQ" => 9,
            "EXP" => 5,
            "EST" => 7,
            "AGU" => 1,
            "ABA" => 5,
            "CAN" => 5,
            "RET" => 2,
        );

        // echo "<pre>";
        // var_dump($consulta);
        // echo "</pre>";

        if ($consulta && $venda->status_transacao != @$status[@$consulta->payment->status]) {
            $venda->status_transacao = @$status[@$consulta->payment->status];
            $venda->transacao = $consulta->payment->esitef_usn;
            $venda->data_ultimo_evento = date("Y-m-d H:i:s");
            $venda->data_finalizada = recuperaDataHora(@$consulta->payment->payment_date);

            $data["nome_produto"] = $venda->dataProduto()->nome;

            if (@$status[@$consulta->payment->status] == 4) {
                $data["status_transacao"] = 4;
                (new AppProcessos(null))->processos($data);
            }

            // $this->logs($data, "not");

            if ($venda->status_transacao == 5) {
                $venda->motivo = $consulta->payment->authorizer_message;
                $data["msg-motivo"] = $venda->motivo;
            }

            // echo "<pre>";
            // var_dump($venda);
            // echo "</pre>";

            if ($venda->save()) {
                $data["msg-venda"] = "Salvo com sucesso";
            } else {
                $data["msg-venda"] =  $venda->fail()->getMessage();
            }
        }

        if ($venda->status_transacao == 4) {
            $data["msg-status"] = "Compra Aprovada!";
        }
        return $data;
    }

    public function atualizarPix()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];

        // $busca = @$inicio ? "created_at BETWEEN '$inicio 00:00:00' AND '$fim 23:59:59' AND " : "created_at BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 00:00:00")."' AND ";
        $busca = @$inicio ? "created_at BETWEEN '$inicio 00:00:00' AND '$fim 23:59:59' AND " : "";
        $vendas = (new Venda())->find($busca . "forma_pagamento = 3")->fetch(true);

        if ($vendas) {
            foreach ($vendas as $venda) {
                echo "parte 1 - $venda->id<br>";

                if ($venda->nit_pix) {
                    $url = $this->endpoint_pix . "transactions/$venda->nit_pix";
                    $consulta = $this->curlPix($url, "GET", null);

                    echo "PARTE 2 - $venda->id<br>";
                    $status = array(
                        "NOV" => 1,
                        "INV" => 5,
                        "PPC" => 1,
                        "PPN" => 1,
                        "PEN" => 1,
                        "CON" => 4,
                        "NEG" => 5,
                        "ERR" => 5,
                        "BLQ" => 9,
                        "EXP" => 5,
                        "EST" => 7,
                        "AGU" => 1,
                        "ABA" => 5,
                        "CAN" => 5,
                        "RET" => 2,
                    );

                    // echo "<pre>";
                    // var_dump($consulta);
                    // echo "</pre>";

                    if ($venda->status_transacao != $status[$consulta->payment->status] && $consulta->payment->status) {
                        $venda->status_transacao = $status[$consulta->payment->status];
                        $venda->transacao = $consulta->payment->esitef_usn;
                        $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                        $venda->data_finalizada = recuperaDataHora(@$consulta->payment->payment_date);

                        if ($venda->status_transacao == 5) {
                            $venda->motivo = $consulta->payment->authorizer_message;
                            $data["msg-motivo"] = $venda->motivo;
                            $venda->data_finalizada = NULL;
                        }

                        // echo "<pre>";
                        // var_dump($venda);
                        // echo "</pre>";

                        if ($venda->save()) {
                            echo "Salvo com sucesso<br>";
                        } else {
                            echo $venda->fail()->getMessage() . "<br>";
                        }
                        // echo "Salvo com sucesso<br>";
                    }

                    if ($venda->status_transacao == 4) {
                        echo "Compra Aprovada!<br>";
                    }
                }
            }
        }
    }

    public function verificarServicoPix()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint_pix . "transactions/139b2e94786783afb13c5ac8496474825f9698093a7ac9349816aaa1a2e537a3",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "merchant_key: $this->key_pix",
                "merchant_id: $this->token_pix",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response)
            echo "Funcionando";
        else
            echo "Não está funcionando";
    }

    public function curlPix($url, $metodo, $json = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $metodo,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                "merchant_key: $this->key_pix",
                "merchant_id: $this->token_pix",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
}

<?php

//CLASSE QUE CONTROLA AS ROTAS DOS CLIENTES

namespace Source\App\WebHooks;

use Moip\Moip;
use Moip\Auth\BasicAuth;

use Source\Models\Vendas\Venda;
use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\App\Processos\AppProcessos;
use Source\App\Bling\AppBling;
use Source\App\CheckoutTransparente\MercadoPago;
use Source\App\Comissao\AppComissao;
use Source\App\Postback\AppPostback;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class AppWebHook
{
    private $endpoint;
    private $token;
    private $key;

    public function webhook($data)
    {
        // Pega o RAW data da requisição
        $json = file_get_contents('php://input');
        // Converte os dados recebidos
        $response = json_decode($json);

        if (@$response->event == "PAYMENT.AUTHORIZED") {
            $status = 4;
            $payID = $response->resource->payment->id;
            $finalizada = date("Y-m-d H:i:s");
            $ultimo_evento = $finalizada;
        } else if (@$response->event == "PAYMENT.CANCELLED") {
            $status = 5;
            $payID = $response->resource->payment->id;
            $motivo = @$response->resource->payment->cancellationDetails->description;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        } else if (@$response->event == "PAYMENT.REFUNDED") {
            $status = 6;
            $payID = $response->resource->refund->_links->payment->title;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        } else if (@$response->event == "PAYMENT.REVERSED") {
            $status = 7;
            $payID = $response->resource->refund->_links->payment->title;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        }

        $venda = (new Venda())->find("transacao = '$payID'")->fetch();
        if ($venda) {

            echo @$finalizada . "\n";
            $venda->status_transacao = $status;
            $venda->data_finalizada = @$finalizada != "" ? $finalizada : NULL;
            $venda->data_ultimo_evento = $ultimo_evento;

            if (@$motivo)
                $venda->motivo = $motivo;

            if ($venda->save()) {
                $msg = "Salvo";

                if ($venda->status_transacao == 6)
                    // (new AppComissao(null))->cadastrarEstorno(["venda" => $venda->id]);
                    echo "test";
            } else {
                $msg = "{$venda->fail()->getMessage()}";
            }


            $arquivo = fopen('webhook.json', 'a+');
            $arquivo2 = fopen('webhook-status.txt', 'a+');
            fwrite($arquivo, $json . "\n\n");
            fwrite($arquivo2, "$status - $payID - $msg\n");
            fclose($arquivo);
            fclose($arquivo2);

            if (@$venda->endereco) {
                $dados["nome"] = $venda->dataEndereco()->nome;
                $dados["email"] = $venda->dataEndereco()->email;
                $dados["telefone"] = $venda->dataEndereco()->telefone;
            } else {
                $dados["nome"] = $venda->dataComprador()->nome;
                $dados["email"] = $venda->dataComprador()->email;
                $dados["telefone"] = $venda->dataComprador()->telefone;
            }

            $dados["venda"] = $venda->id;
            $dados["status_transacao"] = $status;
            $dados["transacao"] = $payID;
            $dados["produtor"] = $venda->produtor;

            if (@$venda->afiliado)
                $dados["afiliado"] = $venda->afiliado;

            $dados["valor_bruto"] = $venda->valor_bruto;
            $dados["valor_produto"] = $venda->valor_produto;
            $dados["valor_desconto"] = $venda->valor_desconto;

            if (@$venda->plano) {
                $dados["nomekit"] = $venda->dataPlano()->nome;
                $dados["produto"] = $venda->dataPlano()->nome;
            } else {
                $dados["nomekit"] = $venda->dataProduto()->nome;
                $dados["produto"] = $venda->dataProduto()->nome;
            }

            (new AppProcessos())->processos($dados);

            $arquivo = fopen('processos.txt', 'a+');
            $texto = "";
            foreach ($dados as $key => $value) {
                @$texto .= "$key = $value \n";
            }
            $texto .= "*******************************************************\n\n";
            fwrite($arquivo, $texto);
            fclose($arquivo);

            echo "\n venda $venda->id, usuario $venda->produtor, produto $venda->produto <br>";
            // $bling = (new AppBling(null))->criarPedido(["venda" => $venda->id, "usuario" => $venda->produtor, "produto" => $venda->produto]);
            $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
        } else {
            echo "Não tem venda";
        }
    }

    public function postbackPagarMe($data)
    {
        header('Content-Type: application/json');
        // Pega o RAW data da requisição
        $json = file_get_contents('php://input');
        // Converte os dados recebidos
        $response = json_decode($json);
        // print_r($response);

        parse_str($response->payload, $payload);
        $payload = json_encode($payload);
        $payload = json_decode($payload);
        // var_dump($payload);

        // exit;
        if (@$payload->current_status == "paid") {
            $status = 4;
            $payID = $payload->id;
            $finalizada = date("Y-m-d H:i:s");
            $ultimo_evento = $finalizada;
        } else if (@$payload->current_status == "refused") {
            $status = 5;
            $payID = $payload->id;
            $motivo = @$payload->soft_descriptor;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        } else if (@$payload->current_status == "refunded") {
            $status = 6;
            $payID = $payload->id;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        }

        $venda = (new Venda())->find("transacao = '$payID'")->fetch();
        if ($venda) {

            echo @$finalizada . "<br>";
            $venda->status_transacao = $status;
            $venda->data_finalizada = @$finalizada != "" ? $finalizada : NULL;
            $venda->data_ultimo_evento = $ultimo_evento;

            if (@$motivo)
                $venda->motivo = $motivo;

            if ($venda->save()) {
                $msg = "Salvo";

                if ($venda->status_transacao == 6)
                    // (new AppComissao(null))->cadastrarEstorno(["venda" => $venda->id]);
                    echo "test";
            } else {
                $msg = "{$venda->fail()->getMessage()}";
            }


            $arquivo = fopen('pagarme.json', 'a+');
            $arquivo2 = fopen('pagarme-status.txt', 'a+');
            fwrite($arquivo, $json . "\n\n");
            fwrite($arquivo2, "$status - $payID - $msg\n");
            fclose($arquivo);
            fclose($arquivo2);

            if (@$venda->endereco) {
                $dados["nome"] = $venda->dataEndereco()->nome;
                $dados["email"] = $venda->dataEndereco()->email;
                $dados["telefone"] = $venda->dataEndereco()->telefone;
            } else {
                $dados["nome"] = $venda->dataComprador()->nome;
                $dados["email"] = $venda->dataComprador()->email;
                $dados["telefone"] = $venda->dataComprador()->telefone;
            }

            $dados["status_transacao"] = $status;
            $dados["transacao"] = $payID;
            $dados["produtor"] = $venda->produtor;

            if (@$venda->afiliado)
                $dados["afiliado"] = $venda->afiliado;

            $dados["valor_bruto"] = $venda->valor_bruto;
            $dados["valor_produto"] = $venda->valor_produto;
            $dados["valor_desconto"] = $venda->valor_desconto;

            if (@$venda->plano) {
                $dados["nomekit"] = $venda->dataPlano()->nome;
                $dados["produto"] = $venda->dataPlano()->nome;
            } else {
                $dados["nomekit"] = $venda->dataProduto()->nome;
                $dados["produto"] = $venda->dataProduto()->nome;
            }

            $processos = (new AppProcessos())->processos($dados);

            $arquivo = fopen('postback-pagarme.txt', 'a+');
            fwrite($arquivo, implode($dados) . "\n");
            fclose($arquivo);

            echo "venda $venda->id, usuario $venda->produtor, produto $venda->produto <br>";
            $bling = (new AppBling(null))->criarPedido(["venda" => $venda->id, "usuario" => $venda->produtor, "produto" => $venda->produto]);
            $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
        } else {
            echo "Não tem venda";
        }
    }

    public function webhookVendas($data)
    {
        $forma_pagamento = array(
            1 => "CARTÃO DE CRÉDITO",
            2 => "BOLETO"
        );

        $status_transacao = array(
            1 => "Aguardando pagamento",
            2 => "EM ANÁLISE",
            3 => "PRÉ AUTORIZADA",
            4 => "Finalizada",
            5 => "Cancelada",
            6 => "DEVOLVIDA",
            7 => "CHARGEBACK",
            8 => "CONCLUÍDA",
            9 => "BLOQUEADA",
            10 => "ANTIFRAUDE",
        );

        $vendas = (new Venda())->find("st4rt = 1 AND postback_st4rt = 0 AND status_transacao = 4")->fetch(true);
        if ($vendas) {
            echo "Tem vendas<br>";
            foreach ($vendas as $venda) {

                echo $venda->id . " - " . $venda->transacao . "<br>";

                $content = http_build_query(array(
                    'chave_unica'        => 'db890335eaf4a7cb8df604ec01fdaf17',
                    'codigoProduto'      => $venda->dataProduto()->codigo,
                    'nomeProduto'        => @$venda->dataPlano()->nome . " - " . $venda->dataProduto()->nome,
                    'codVenda'           => $venda->transacao,
                    'codPlano'           => @$venda->dataPlano()->codigo,
                    'dataInicio'         => $venda->created_at,
                    'dataFim'            => $venda->data_finalizada,
                    'formaPagamento'     => $forma_pagamento[$venda->forma_pagamento],
                    'statusVenda'        => $status_transacao[$venda->status_transacao],
                    'valorVenda'         => $venda->valor_bruto,
                    'linkBoleto'         => $venda->link_boleto,
                    'nomeComprador'      => $venda->dataComprador()->nome,
                    'email'              => $venda->dataComprador()->email,
                    'cnpj_cpf'           => $venda->dataComprador()->cpf_cnpj,
                    'telefone'           => $venda->dataComprador()->telefone,
                    'cep'                => $venda->dataComprador()->cep_entrega,
                    'endereco'           => $venda->dataComprador()->rua_entrega,
                    'numero'             => $venda->dataComprador()->numero_entrega,
                    'complemento'        => $venda->dataComprador()->complemento_entrega,
                    'bairro'             => $venda->dataComprador()->bairro_entrega,
                    'cidade'             => $venda->dataComprador()->cidade_entrega,
                    'estado'             => $venda->dataComprador()->estado_entrega,
                    'pais'               => $venda->dataComprador()->pais_entrega,
                    'nomeProdutor'       => $venda->dataProdutor()->nome,
                    'emailProdutor'      => $venda->dataProdutor()->email,
                    'afiliado'           => @$venda->dataAfiliado()->nome,
                    'fonte-pedido'       => "RedRocket"
                ));

                //var_dump($content, "<br><br>");

                $context = stream_context_create(array(
                    'http' => array(
                        'method'  => 'POST',
                        'header' => "Content-type: application/x-www-form-urlencoded",
                        'content' => $content,
                    )
                ));

                //$result = file_get_contents('http://localhost/sistema/pedidos-hubsale', null, $context);
                $result = file_get_contents('https://st4rt.com.br/painel/pedidos-hubsale', false, $context);

                var_dump($result);

                $pedigo = (new Venda())->findById($venda->id);
                $pedigo->postback_st4rt = 1;
                $pedigo->save();
            }
        } else {
            echo "Nenhuma venda corresponde à pesquisa<br>";
        }
    }

    public function webhookMercadoPago($data)
    {
        date_default_timezone_set("America/Recife");

        header('Content-Type: application/json');
        // Pega o RAW data da requisição
        $json = file_get_contents('php://input');
        // Converte os dados recebidos
        $response = json_decode($json);

        $status_transacao = array(
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
        // }
        if (@$response->data->id) {
            $payID = @$response->data->id;
            $venda = (new Venda())->find("transacao = '$payID'")->fetch();
            if ($venda) {
                $adquirente = (new Adquirente())->find("adquirente = 'mercadopago' AND usuario = " . @$venda->produtor)->fetch();
                if (@$adquirente) {
                    $data["adquirente"] = @$adquirente->id;
                    $data["paymentID"] = @$payID;
                    $transacao =  (new MercadoPago(null))->buscarPedido(@$data);

                    $status = @$status_transacao[@$transacao->status];
                    if (@$status == 4 || @$status == 3) {
                        $finalizada = date("Y-m-d H:i:s");
                        $ultimo_evento = $finalizada;
                    } else if (@$status == 5) {
                        $motivo = $transacao->status_detail;
                        $ultimo_evento = date("Y-m-d H:i:s");
                        $finalizada = NULL;
                    } else {
                        $ultimo_evento = date("Y-m-d H:i:s");
                        $finalizada = NULL;
                    }
                }

                echo @$finalizada . "<br>";
                $venda->status_transacao = $status;
                $venda->data_finalizada = @$finalizada != "" ? $finalizada : NULL;
                $venda->data_ultimo_evento = $ultimo_evento;

                if (@$motivo)
                    $venda->motivo = $motivo;

                if ($venda->save()) {
                    $msg = "Salvo";

                    if ($venda->status_transacao == 6)
                        // (new AppComissao(null))->cadastrarEstorno(["venda" => $venda->id]);
                        echo "test";
                } else {
                    $msg = "{$venda->fail()->getMessage()}";
                }

                if (@$venda->endereco) {
                    $dados["nome"] = $venda->dataEndereco()->nome;
                    $dados["email"] = $venda->dataEndereco()->email;
                    $dados["telefone"] = $venda->dataEndereco()->telefone;
                } else {
                    $dados["nome"] = $venda->dataComprador()->nome;
                    $dados["email"] = $venda->dataComprador()->email;
                    $dados["telefone"] = $venda->dataComprador()->telefone;
                }

                $dados["venda"] = $venda->id;
                $dados["status_transacao"] = $status;
                $dados["transacao"] = $payID;
                $dados["produtor"] = $venda->produtor;

                if (@$venda->afiliado)
                    $dados["afiliado"] = $venda->afiliado;

                $dados["valor_bruto"] = $venda->valor_bruto;
                $dados["valor_produto"] = $venda->valor_produto;
                $dados["valor_desconto"] = $venda->valor_desconto;

                if (@$venda->plano) {
                    $dados["nomekit"] = $venda->dataPlano()->nome;
                    $dados["produto"] = $venda->dataPlano()->nome;
                } else {
                    $dados["nomekit"] = $venda->dataProduto()->nome;
                    $dados["produto"] = $venda->dataProduto()->nome;
                }

                $processos = (new AppProcessos())->processos($dados);

                // REGISTRO DE LOG RECEBIDO PELO MERCADOPAGO
                $arquivo = fopen('webhook-mercadopago.txt', 'a+');
                $texto = date("d/m/Y H:i:s") . "\n";
                $texto .= "dados = {\n";

                if ($response) {
                    foreach ($response as $chave => $valor) {
                        if (gettype($valor) === "object") {
                            foreach ($valor as $key => $value) {
                                @$texto .= "data {\n '$key' = '$value',\n } \n";
                            }
                            // echo "$key = $value<br>";
                        } else {
                            @$texto .= "'$chave': '$valor',\n";
                        }
                    }
                }
                $texto .= "}\n";
                $texto .= "**** Venda: $venda->id, Produtor: $venda->produtor, Produto: $venda->produto ***\n";

                fwrite($arquivo, $texto . "\n");
                fclose($arquivo);
                // FIM REGISTRO LOG RECEBIDO PELO MERCADOPAGO

                echo "venda $venda->id, usuario $venda->produtor, produto $venda->produto <br>";
                $bling = (new AppBling(null))->criarPedido(["venda" => $venda->id, "usuario" => $venda->produtor, "produto" => $venda->produto]);
                $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
            } else {
                echo "Não tem venda";
            }
        }
    }

    // WEBHOOK ENVIADO PELA PAGARME - Versão Doc: v5. 
    public function webhookPagarMe($data)
    {
        date_default_timezone_set("America/Recife");

        header('Content-Type: application/json');
        // Pega o RAW data da requisição
        $json = file_get_contents('php://input');
        // Converte os dados recebidos
        $response = json_decode($json);
        // print_r($response);

        $status_transacao = @$response->data->last_transaction->status;

        // Inicializa variáveis para evitar undefined variable em eventos não mapeados
        $status        = null;
        $finalizada    = null;
        $ultimo_evento = date("Y-m-d H:i:s");
        $motivo        = null;

        if (@$status_transacao == "paid") {
            $status = 4;
            $finalizada = date("Y-m-d H:i:s");
            $ultimo_evento = $finalizada;
        } else if (@$status_transacao == "canceled" || @$status_transacao == "failed") {
            $status = 5;
            $motivo = @$response->data->charges[0]->last_transaction->gateway_response->errors[0]->message;
        } else if (@$status_transacao == "refunded") {
            $status = 6;
        } else {
            // Evento não reconhecido (processing, authorized, etc.) — apenas loga e sai
            error_log("[WebhookPagarMe] Evento ignorado: type={$response->type} status={$status_transacao}");
            echo json_encode(["status" => "ignored", "reason" => "unhandled_status"]);
            return;
        }

        $payID = @$response->data->id;

        $venda = (new Venda())->find("transacao = '$payID'")->fetch();
        if ($venda) {

            // Guard contra processamento duplicado: se o status já é o mesmo, não reprocessa
            if ((int)$venda->status_transacao === $status) {
                error_log("[WebhookPagarMe] Duplicata ignorada: venda={$venda->id} status={$status}");
                echo json_encode(["status" => "ignored", "reason" => "already_processed"]);
                return;
            }

            echo @$finalizada . "<br>";
            $venda->status_transacao = $status;
            $venda->data_finalizada = $finalizada ?: null;
            $venda->data_ultimo_evento = $ultimo_evento;

            if (@$motivo)
                $venda->motivo = $motivo;

            if ($venda->save()) {
                $msg = "Salvo";

                if ($venda->status_transacao == 6)
                    // (new AppComissao(null))->cadastrarEstorno(["venda" => $venda->id]);
                    echo "test";
            } else {
                $msg = "{$venda->fail()->getMessage()}";
            }


            // REGISTRO DE LOG PAGARME - Versão: v5
            $arquivo = fopen('pagarme.json', 'a+');
            $arquivo2 = fopen('pagarme-status.txt', 'a+');

            $texto = "dados = {\n";
            $texto .= date("d/m/Y H:i:s");
            @$texto .= "\n ID Transacao: " . $response->data->id . "\n";
            @$texto .= "Event: " . $response->type . "\n";
            @$texto .= "Meio de Pagamento: " . $response->data->last_transaction->transaction_type . "\n";
            @$texto .= "Status: " . $response->data->last_transaction->status . "\n";
            $texto .= "}";

            fwrite($arquivo, $json . "\n\n");
            fwrite($arquivo2, $texto . "\n");
            fclose($arquivo);
            fclose($arquivo2);
            // FIM REGISRO LOG PAGARME - Versão: v5

            if (@$venda->endereco) {
                $dados["nome"] = $venda->dataEndereco()->nome;
                $dados["email"] = $venda->dataEndereco()->email;
                $dados["telefone"] = $venda->dataEndereco()->telefone;
            } else {
                $dados["nome"] = $venda->dataComprador()->nome;
                $dados["email"] = $venda->dataComprador()->email;
                $dados["telefone"] = $venda->dataComprador()->telefone;
            }

            $dados["venda"] = $venda->id;
            $dados["status_transacao"] = $status;
            $dados["transacao"] = $payID;
            $dados["produtor"] = $venda->produtor;

            if (@$venda->afiliado)
                $dados["afiliado"] = $venda->afiliado;

            $dados["valor_bruto"] = $venda->valor_bruto;
            $dados["valor_produto"] = $venda->valor_produto;
            $dados["valor_desconto"] = $venda->valor_desconto;

            if (@$venda->plano) {
                $dados["nomekit"] = $venda->dataPlano()->nome;
                $dados["produto"] = $venda->dataPlano()->nome;
            } else {
                $dados["nomekit"] = $venda->dataProduto()->nome;
                $dados["produto"] = $venda->dataProduto()->nome;
            }

            $processos = (new AppProcessos())->processos($dados);

            $arquivo = fopen('postback-pagarme.txt', 'a+');
            fwrite($arquivo, implode($dados) . "\n");
            fclose($arquivo);

            echo "venda $venda->id, usuario $venda->produtor, produto $venda->produto <br>";
            $bling = (new AppBling(null))->criarPedido(["venda" => $venda->id, "usuario" => $venda->produtor, "produto" => $venda->produto]);
            $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
        } else {
            echo "Não tem venda";
        }
    }
    // WEBHOOK ENVIADO PELO PAGSEGURO
    public function webhookPagseguro($data)
    {
        date_default_timezone_set("America/Recife");

        header('Content-Type: application/json');
        // Pega o RAW data da requisição
        $json = file_get_contents('php://input');
        // Converte os dados recebidos
        $response = json_decode($json);

        // print_r($response->status);
        $pagamento = substr($response->id, 0, 4);

        // WEBHOOK É CARTÃO DE CRÉDITO OU PIX
        if ($pagamento == "ORDE")
            $status_transacao = @$response->charges[0]->status;
        else //WEBHOOK É BOLETO
            $status_transacao = @$response->status;
        // echo "pagamento: " . $pagamento;
        // exit;
        if (@$status_transacao == "PAID") {
            $status = 4;
            $finalizada = date("Y-m-d H:i:s");
            $ultimo_evento = $finalizada;
        } else if (@$status_transacao == "CANCELED") {
            $status = 5;
            $motivo = @$response->charges[0]->payment_response->message;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        } else if (@$status_transacao == "refunded") {
            $status = 6;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        }
        $payID = @$response->id;

        $venda = (new Venda())->find("nit_pix = '$payID'")->fetch();
        if ($venda) {

            echo @$finalizada . "<br>";
            $venda->status_transacao = $status;
            $venda->data_finalizada = @$finalizada != "" ? $finalizada : NULL;
            $venda->data_ultimo_evento = $ultimo_evento;

            if (@$motivo)
                $venda->motivo = $motivo;

            if ($venda->save()) {
                $msg = "Salvo";

                if ($venda->status_transacao == 6)
                    // (new AppComissao(null))->cadastrarEstorno(["venda" => $venda->id]);
                    echo "test";
            } else {
                $msg = "{$venda->fail()->getMessage()}";
            }


            // REGISTRO DE LOG PAGSEGURO
            $arquivo = fopen('pagseguro.json', 'a+');
            $arquivo2 = fopen('pagseguro-status.txt', 'a+');

            $texto = "dados = {\n";
            $texto .= date("d/m/Y H:i:s");
            @$texto .= "\n ID Transacao: " . $response->id . "\n";
            // @$texto .= "Object: " . @$response . "\n";
            @$texto .= "Meio de Pagamento: " . $response->charges[0]->payment_method->type . "\n";
            @$texto .= "Status: " . $response->charges[0]->status . "\n";
            $texto .= "}";

            fwrite($arquivo, $json . "\n\n");
            fwrite($arquivo2, $texto . "\n");
            fclose($arquivo);
            fclose($arquivo2);
            // FIM REGISRO LOG PAGSEGURO

            if (@$venda->endereco) {
                $dados["nome"] = $venda->dataEndereco()->nome;
                $dados["email"] = $venda->dataEndereco()->email;
                $dados["telefone"] = $venda->dataEndereco()->telefone;
            } else {
                $dados["nome"] = $venda->dataComprador()->nome;
                $dados["email"] = $venda->dataComprador()->email;
                $dados["telefone"] = $venda->dataComprador()->telefone;
            }

            $dados["venda"] = $venda->id;
            $dados["status_transacao"] = $status;
            $dados["transacao"] = $payID;
            $dados["produtor"] = $venda->produtor;

            if (@$venda->afiliado)
                $dados["afiliado"] = $venda->afiliado;

            $dados["valor_bruto"] = $venda->valor_bruto;
            $dados["valor_produto"] = $venda->valor_produto;
            $dados["valor_desconto"] = $venda->valor_desconto;

            if (@$venda->plano) {
                $dados["nomekit"] = $venda->dataPlano()->nome;
                $dados["produto"] = $venda->dataPlano()->nome;
            } else {
                $dados["nomekit"] = $venda->dataProduto()->nome;
                $dados["produto"] = $venda->dataProduto()->nome;
            }

            $processos = (new AppProcessos())->processos($dados);

            $arquivo = fopen('postback-pagseguro.txt', 'a+');
            fwrite($arquivo, implode($dados) . "\n");
            fclose($arquivo);

            echo "venda $venda->id, usuario $venda->produtor, produto $venda->produto <br>";
            $bling = (new AppBling(null))->criarPedido(["venda" => $venda->id, "usuario" => $venda->produtor, "produto" => $venda->produto]);
            $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
        } else {
            echo "Não tem venda";
        }
    }
}

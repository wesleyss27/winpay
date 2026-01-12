<?php

//CLASSE QUE CONTROLA AS ROTAS DOS CLIENTES

namespace Source\App\WebHooks;

use Moip\Moip;
use Moip\Auth\BasicAuth;

use Source\Models\Vendas\Venda;
use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\App\Processos\AppProcessos;
use Source\App\Bling\AppBling;
//use Source\App\CheckoutTransparente\MercadoPago;
use Source\App\Checkout1\MercadoPago;
use Source\App\Comissao\AppComissao;
use Source\App\Postback\AppPostback;
use Source\Models\CartaoCheckoutTransparente\Adquirente;
use Source\Models\Assinaturas\AssinaturaCadastro;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\App\Assinaturas\AppAssinaturas;
use Source\Models\Vendas\ManualSale;
use Source\Models\Vendas\ManualSaleItem;
use Source\Models\Assinaturas\Assinatura;

class AppWebHook
{
    private $endpoint;
    private $token;
    private $key;
    private $houveAtualizacaoVendas = false;

    public function criarNotificacaoWebhook($data)
    {
        /*$token = 'K5DRSX8C9CVGZMQGEFMMT03YVQOKJTYF';
        $key = 'LWSLY0OKFYUYJAV1LAQSSMNYOL6OVGK4KCED87FO';*/

        // AMBIENTE PRODUÇÃO
        /*$token = 'AVR8C7GI6HJCLIAUH41SB75MQGTMPKCN';
        $key = 'UWXSO3C1ZBCK8UUYB2J2YLRMNK2KFPTCEPV47CVW';*/

        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(1);
        if ($configuracaoCheckout->sandbox == 1) {
            define("ENDPOINT", Moip::ENDPOINT_SANDBOX);
            $this->token = $configuracaoCheckout->token_sandbox;
            $this->key = $configuracaoCheckout->chave_sandbox;
        } else {
            define("ENDPOINT", Moip::ENDPOINT_PRODUCTION);
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;
        }

        $moip = new Moip(new BasicAuth($this->token, $this->key), ENDPOINT);

        $url = url("webhook");
        $notification = $moip->notifications()
            ->addEvent('PAYMENT.AUTHORIZED')
            ->addEvent('PAYMENT.CANCELLED')
            ->addEvent('PAYMENT.REFUNDED')
            ->addEvent('PAYMENT.REVERSED')
            ->setTarget($url)
            ->create();
        print_r($notification);
    }

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
        $json = file_get_contents('php://input');
        $response = json_decode($json);
        // loga o payload bruto para depuração
        

        $status = null;
        $payID = null;
        $motivo = null;
        $finalizada = null;
        $ultimo_evento = date("Y-m-d H:i:s");

        // Modelo atual PagarMe: dados na raiz (ou embrulhados em message)
        $event = isset($response->message) ? $response->message : $response;

        if (isset($event->data)) {
            $payload = $event->data;
            error_log(json_encode($payload->id));
            $statusString = $payload->status ?? ($payload->last_transaction->status ?? null);

            $statusMap = [
                "paid"            => 4,
                "processing"      => 2,
                "pending"         => 1,
                "waiting_payment" => 1,
                "refused"         => 5,
                "failed"          => 5,
                "canceled"        => 5,
                "refunded"        => 6,
            ];

            if ($statusString && isset($statusMap[$statusString])) {
                $status = $statusMap[$statusString];
            }

            $payID = $payload->id ?? null; // charge id (ch_...) salvo em venda->transacao

            if ($status === 4) {
                $paidAt = $payload->paid_at ?? ($payload->last_transaction->created_at ?? null);
                $finalizada = $paidAt ? date("Y-m-d H:i:s", strtotime($paidAt)) : date("Y-m-d H:i:s");
                $ultimo_evento = $finalizada;
            } elseif (in_array($status, [5, 6])) {
                $motivo = $payload->last_transaction->gateway_response->message ?? null;
            }
        }
        // Formato legado (payload urlencoded)
        elseif (isset($response->payload)) {
            parse_str($response->payload, $payload);
            $payload = json_decode(json_encode($payload));
            error_log(json_encode($payload->id));

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
                $finalizada = NULL;
            } else if (@$payload->current_status == "refunded") {
                $status = 6;
                $payID = $payload->id;
                $ultimo_evento = date("Y-m-d H:i:s");
                $finalizada = NULL;
            }
        } else {
            error_log("postbackPagarMe: formato desconhecido");
            echo "Payload inválido";
            return;
        }

        if (!$payID || $status === null) {
            error_log("postbackPagarMe: status ou id ausentes");
            echo "Payload inválido";
            return;
        }

        $venda = (new Venda())->find("transacao = '$payID'")->fetch();
        if ($venda) {

            $statusAnterior = $venda->status_transacao;
            $dispararProcessos = $statusAnterior != $status;

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

            $dados["venda"] = (int) $venda->id;
            if ($dispararProcessos && $dados["venda"]) {
                $processos = (new AppProcessos())->processos($dados);
            } else {
                error_log("postbackPagarMe: venda sem id para transacao {$payID}");
            }

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
            1  => "Aguardando pagamento",
            2  => "EM ANÁLISE",
            3  => "PRÉ AUTORIZADA",
            4  => "Finalizada",
            5  => "Cancelada",
            6  => "DEVOLVIDA",
            7  => "CHARGEBACK",
            8  => "CONCLUÍDA",
            9  => "BLOQUEADA",
            10 => "ANTIFRAUDE"
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
                $result = file_get_contents('https://st4rt.com.br/painel/pedidos-hubsale', null, $context);

                var_dump($result);

                $pedigo = (new Venda())->findById($venda->id);
                $pedigo->postback_st4rt = 1;
                $pedigo->save();
            }
        } else {
            echo "Nenhuma venda corresponde à pesquisa<br>";
        }
    }

    public function webhookBspay($data)
    {
        date_default_timezone_set("America/Recife");

        header('Content-Type: application/json');
        // Pega o RAW data da requisiçãoo
        $json = file_get_contents('php://input');
        // Converte os dados recebidos
        $response = json_decode($json);

        error_log(json_encode(["message" => "Bspay duplicado ignorado", "venda" => $response]));

        $status_transacao = array(
            "PAID" => 4,
            "PENDING" => 1,
            "CANCELED" => 5,
            "CANCELLED" => 5,
            "REFUNDED" => 6,
            "CHARGEBACK" => 7
        );

        $motivo = null;
        $finalizada = null;

        if (isset($response->requestBody->transactionId)) {
            $payID = $response->requestBody->transactionId;
            $status = $status_transacao[$response->requestBody->status] ?? null;
            if ($status === null) {
                echo "Status desconhecido recebido do BSPay.<br>";
                return;
            }

            $eventoData = $response->requestBody->dateApproval ?? null;
            $dataEventoFormatada = $eventoData ? date("Y-m-d H:i:s", strtotime($eventoData)) : null;

            if ($status === 4) {
                $finalizada = $dataEventoFormatada ?? date("Y-m-d H:i:s");
                $motivo = null;
            } elseif (in_array($status, [5, 6, 7])) {
                $motivo = $response->requestBody->message ?? "Pagamento não concluído";
            }

            $vendas = $this->atualizarVendasManualPorTransacao($payID, $status, $motivo, $finalizada, $dataEventoFormatada ?? $eventoData);

            if (!$vendas) {
                echo "Não tem venda para a transação {$payID}.<br>";
                return;
            }

            if (!$this->houveAtualizacaoVendas()) {
                $primeiraVenda = $vendas[0];
                error_log("Bspay duplicado ignorado: venda {$primeiraVenda->id}, status={$status}");
                echo "Evento duplicado (status igual).<br>";
                return;
            }

            foreach ($vendas as $venda) {
                $dados = [];
                $contato = $venda->dataEndereco() ?: $venda->dataComprador();
                $dados["nome"] = $contato ? ($contato->nome ?? null) : null;
                $dados["email"] = $contato ? ($contato->email ?? null) : null;
                $dados["telefone"] = $contato ? ($contato->telefone ?? null) : null;

                $dados["venda"] = $venda->id;
                $dados["status_transacao"] = $venda->status_transacao;
                $dados["transacao"] = $payID;
                $dados["produtor"] = $venda->produtor;

                if (@$venda->afiliado)
                    $dados["afiliado"] = $venda->afiliado;

                $dados["valor_bruto"] = $venda->valor_bruto;
                $dados["valor_produto"] = $venda->valor_produto;
                $dados["valor_desconto"] = $venda->valor_desconto;

                if (@$venda->plano) {
                    $dados["nomekit"] = $venda->dataPlano()->nome;
                    $dados["produto"] = $venda->dataPlano()->codigo;
                } else {
                    $dados["nomekit"] = $venda->dataProduto()->nome;
                    $dados["produto"] = $venda->dataProduto()->nome;
                }

                (new AppProcessos())->processos($dados);

                $arquivo = fopen('webhook-bspay.txt', 'a+');
                $texto = date("d/m/Y H:i:s") . "\n";
                $texto .= json_encode($response, JSON_PRETTY_PRINT) . "\n";
                fwrite($arquivo, $texto);
                fclose($arquivo);

                $bling = (new AppBling(null))->criarPedido(["venda" => $venda->id, "usuario" => $venda->produtor, "produto" => $venda->produto]);
                $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);

                echo "Venda {$venda->id}, status atualizado para {$venda->status_transacao}.<br>";
            }
        } else {
            echo "Dados inválidos recebidos no webhook.<br>";
        }
    }

    public function webhookStripe($data)
    {
        date_default_timezone_set("America/Recife");
        header('Content-Type: application/json');

        // 1) RAW body e decode (sem validação de assinatura)
        $payload = @file_get_contents('php://input');
        $event   = json_decode($payload);

        if (json_last_error() !== JSON_ERROR_NONE || !$event) {
            http_response_code(400);
            echo json_encode(['error' => 'Payload inválido']);
            return;
        }

        // 2) Extrai tipo e objeto principal
        $type = $event->type ?? '';
        $obj  = $event->data->object ?? null;
        $eventoStripeData = null;
        if (isset($event->created)) {
            $eventoStripeData = date("Y-m-d H:i:s", (int)$event->created);
        }

        if (!$type || !$obj) {
            http_response_code(200);
            echo json_encode(['ignored' => true, 'reason' => 'evento sem type/obj']);
            return;
        }

        // 3) Mapeia eventos Stripe -> status_transacao do seu sistema
        //    1  Aguar. pagamento | 2 Em análise | 3 Pré Autorizada | 4 Finalizada
        //    5 Cancelada         | 6 Devolvida  | 7 Chargeback     | 10 Antifraude
        $status        = null;
        $payID         = null; // usaremos para localizar a venda
        $finalizada    = null;
        $ultimo_evento = $eventoStripeData ?? date("Y-m-d H:i:s");
        $motivo        = null;

        // Tente obter IDs úteis
        $paymentIntentId = $obj->id ?? null; // para eventos de payment_intent.*
        if (isset($obj->payment_intent)) {
            $paymentIntentId = is_string($obj->payment_intent)
                ? $obj->payment_intent
                : ($obj->payment_intent->id ?? $paymentIntentId);
        }
        $chargeId = $obj->charges->data[0]->id ?? ($obj->charge ?? null); // para charge.*

        switch ($type) {
            case 'payment_intent.succeeded':
                $status     = 4; // Finalizada
                $payID      = $paymentIntentId;
                $finalizada = $ultimo_evento;
                break;

            case 'payment_intent.payment_failed':
                $status = 5; // Cancelada
                $payID  = $paymentIntentId;
                $motivo = $obj->last_payment_error->message ?? 'Pagamento falhou';
                break;

            case 'payment_intent.canceled':
                $status = 5; // Cancelada
                $payID  = $paymentIntentId;
                $motivo = 'Payment Intent cancelado';
                break;

            case 'charge.refunded':
                $status = 6; // Devolvida
                $payID  = $chargeId;
                break;

            case 'charge.dispute.created':
                $status = 7; // Chargeback (disputa aberta)
                $payID  = $chargeId;
                $motivo = 'Disputa (chargeback) criada';
                break;

            default:
                // Não tratado; só confirma 200 para evitar retries
                http_response_code(200);
                echo json_encode(['ignored' => true, 'event' => $type]);
                return;
        }

        // 4) Localiza a venda
        //    Recomendação: salve SEMPRE payment_intent.id em venda->transacao ao criar o pagamento.
        $vendas = [];
        if (!empty($paymentIntentId)) {
            $vendas = $this->atualizarVendasManualPorTransacao($paymentIntentId, $status, $motivo, $finalizada, $eventoStripeData);
        }
        if (!$vendas && !empty($chargeId)) {
            $vendas = $this->atualizarVendasManualPorTransacao($chargeId, $status, $motivo, $finalizada, $eventoStripeData);
        }

        if ($vendas) {
            if (!$this->houveAtualizacaoVendas()) {
                $primeiraVenda = $vendas[0];
                error_log("Stripe duplicado ignorado: venda {$primeiraVenda->id}, status={$status}");
                echo json_encode(['duplicate' => true]);
                return;
            }

            foreach ($vendas as $venda) {
                $dados = [];
                if (@$venda->endereco) {
                    $dados["nome"]     = $venda->dataEndereco()->nome;
                    $dados["email"]    = $venda->dataEndereco()->email;
                    $dados["telefone"] = $venda->dataEndereco()->telefone;
                } else {
                    $dados["nome"]     = $venda->dataComprador()->nome;
                    $dados["email"]    = $venda->dataComprador()->email;
                    $dados["telefone"] = $venda->dataComprador()->telefone;
                }

                $dados["venda"]            = $venda->id;
                $dados["status_transacao"] = $venda->status_transacao;
                $dados["transacao"]        = $payID ?? $paymentIntentId ?? $chargeId;
                $dados["produtor"]         = $venda->produtor;

                if (@$venda->afiliado) { $dados["afiliado"] = $venda->afiliado; }

                $dados["valor_bruto"]    = $venda->valor_bruto;
                $dados["valor_produto"]  = $venda->valor_produto;
                $dados["valor_desconto"] = $venda->valor_desconto;

                if (@$venda->plano) {
                    $dados["nomekit"] = $venda->dataPlano()->nome;
                    $dados["produto"] = $venda->dataPlano()->nome;
                } else {
                    $dados["nomekit"] = $venda->dataProduto()->nome;
                    $dados["produto"] = $venda->dataProduto()->nome;
                }

                (new \Source\App\Processos\AppProcessos())->processos($dados);

                $arq = fopen('webhook-stripe.txt', 'a+');
                $txt  = date("d/m/Y H:i:s") . "\n";
                $txt .= "event: {$type}\n";
                $txt .= "status: {$venda->status_transacao}\n";
                $txt .= "venda: {$venda->id}\n";
                $txt .= "payID: " . ($payID ?? $paymentIntentId ?? $chargeId ?? '') . "\n";
                $txt .= "-------------------------------------------\n";
                fwrite($arq, $txt);
                fclose($arq);
            }

            http_response_code(200);
            echo json_encode(['ok' => true]);
            return;
        }

        $arq = fopen('webhook-stripe-notfound.txt', 'a+');
        $txt = date("d/m/Y H:i:s") . " - venda não encontrada. event={$type} payment_intent={$paymentIntentId} charge={$chargeId}\n";
        fwrite($arq, $txt);
        fclose($arq);

        http_response_code(200);
        echo json_encode(['ok' => true, 'info' => 'venda não encontrada']);
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
            $produto = (new Produto())->findById($venda->produto);
            if ($venda) {
                //$adquirente = (new Adquirente())->find("adquirente = 'mercadopago' AND usuario = " . @$venda->produtor)->fetch();
                //if (@$adquirente) {
                //$data["adquirente"] = @$adquirente->id;
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
                //}

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
                    $dados["produto"] = $venda->dataPlano()->codigo;
                    $dados["plano"] = @$venda->dataplano()->codigo;
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

        $status_transacao = @$response->data->status;

        $antifraude = $response->data->charges[0]->last_transaction->antifraud_response->status ?? '';
        // exit;
        if (@$status_transacao == "paid") {
            $status = 4;
            $finalizada = date("Y-m-d H:i:s");
            $ultimo_evento = $finalizada;
        } else if (@$status_transacao == "canceled") {
            $status = 5;
            $motivo = @$response->data->charges[0]->last_transaction->gateway_response->errors[0]->message;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        } else if (@$status_transacao == "failed" && "reproved" === $antifraude ) {
            $status = 10;
            $motivo =  $antifraude . " - " . ( @$response->data->charges[0]->last_transaction->antifraud_response->score ?? '' );
            $ultimo_evento = date("Y-m-d H:i:s");
            $finalizada = date("Y-m-d H:i:s");
        } else if (@$status_transacao == "refunded") {
            $status = 6;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        } else if (@$status_transacao == "pending") {
            $status = 1;
            $ultimo_evento = date("Y-m-d H:i:s");
            // $finalizada = date("Y-m-d H:i:s");
            $finalizada = NULL;
        } else if (@$status_transacao == "chargedback") {
            $status = 7;
            $ultimo_evento = date("Y-m-d H:i:s");
            $finalizada = date("Y-m-d H:i:s");
        }
        $payID = @$response->data->id;

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

    private function atualizarVendasManualPorTransacao(string $transacao, int $status, ?string $motivo, ?string $dataFinalizada, ?string $dataEvento = null): array
    {
        $this->houveAtualizacaoVendas = false;

        $vendas = (new Venda())->find("transacao = :tx", "tx={$transacao}")->fetch(true);
        if (!$vendas) {
            return [];
        }

        $manualSaleIds = [];
        $manualItemIds = [];
        if ($dataEvento) {
            $timestampEvento = strtotime($dataEvento);
            $ultimoEvento = $timestampEvento ? date("Y-m-d H:i:s", $timestampEvento) : $dataEvento;
        } else {
            $ultimoEvento = date("Y-m-d H:i:s");
        }

        foreach ($vendas as $venda) {
            $statusAtual = (int)$venda->status_transacao;
            $eventoRegistrado = (
                $statusAtual === $status &&
                $venda->data_finalizada === $dataFinalizada &&
                ($venda->data_ultimo_evento === $ultimoEvento)
            );

            if (!$eventoRegistrado) {
                $venda->status_transacao = $status;
                $venda->data_finalizada = $dataFinalizada;
                $venda->data_ultimo_evento = $ultimoEvento;
                if ($motivo) {
                    $venda->motivo = $motivo;
                } elseif ($status === 4) {
                    $venda->motivo = null;
                }
                $venda->save();
                $this->houveAtualizacaoVendas = true;
            }

            if ($venda->manual_sale_item_id) {
                $manualItemIds[] = (int)$venda->manual_sale_item_id;
                $manualItem = (new ManualSaleItem())->findById($venda->manual_sale_item_id);
                if ($manualItem) {
                    $manualSaleIds[] = (int)$manualItem->manual_sale_id;
                }
            }
        }

        $manualSaleIds = array_values(array_unique($manualSaleIds));
        if ($manualSaleIds) {
            $manualSaleModel = new ManualSale();
            foreach ($manualSaleIds as $manualSaleId) {
                $manualSale = $manualSaleModel->findById($manualSaleId);
                if ($manualSale) {
                    if ((int)$manualSale->status !== $status) {
                        $manualSale->status = $status;
                        if ($manualSale->save()) {
                            $this->houveAtualizacaoVendas = true;
                        }
                    }
                }
            }
        }

        $manualItemIds = array_values(array_unique($manualItemIds));
        if ($manualItemIds) {
            $ids = implode(',', $manualItemIds);
            $assinaturas = (new Assinatura())->find("manual_sale_item_id IN ({$ids})")->fetch(true);
            if ($assinaturas) {
                foreach ($assinaturas as $assinatura) {
                    $statusAnteriorAss = (int)$assinatura->status;
                    if ($status === 4) {
                        $assinatura->status = 1;
                        $assinatura->tentativas = 0;
                    } elseif (in_array($status, [5, 6, 7])) {
                        $assinatura->status = 3;
                    } else {
                        $assinatura->status = $status;
                    }
                    $assinatura->data_ultimo_evento = $ultimoEvento;
                    if ($assinatura->save()) {
                        if ($statusAnteriorAss !== (int)$assinatura->status) {
                            $this->houveAtualizacaoVendas = true;
                        }
                    }
                }
            }
        }

        return $vendas;
    }

    private function houveAtualizacaoVendas(): bool
    {
        return (bool)$this->houveAtualizacaoVendas;
    }
}

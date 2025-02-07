<?php

namespace Source\App\CheckoutTransparente;

use League\Plates\Engine;

use Source\Models\Vendas\Venda;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Estorno\Estorno;
use Source\Models\Enderecos\Endereco;
use Source\Models\Comissao\Comissao;

use Source\Helpers\Emails;

use Source\App\Postback\AppPostback;
use Source\App\Checkout1\AppProcessos;
use Source\App\CheckoutTransparente\MercadoPago;
use Source\App\CheckoutTransparente\Wirecard;
use Source\App\Comissao\AppComissao2;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

class AppEstorno
{
    private $view;
    protected $validacao;
    private $endpoint;
    private $token;
    private $key;

    private $endpoint_pix;
    private $token_pix;
    private $key_pix;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../pagamentos", "php");
        $this->view->addData(["router" => $router]);
    }

    public function logs($data, $arq)
    {
        $texto = "data: " . date("d/m/Y H:i:s") . "\n";
        $texto .= "venda: " . $data["venda"] . "\n";
        $texto .= serialize($data["obj"]) .
            "\n*******************************************\n";
        $fp = fopen(DIRETORIO . "logs/$arq", 'a+');
        fwrite($fp, $texto);
        fclose($fp);
    }

    public function estornar($data)
    {
        // var_dump(@$data);
        // exit;
        session_start();

        // var_dump($_SESSION);
        // exit;

        $id_venda = $data["id"];
        $tipoVenda = $data["forma_pagamento"];
        $transacao = $data["transacao"];
        $data["venda"] = $data["id"];
        $status = $data["status"];
        $adquirente = @$data["adquirente"];

        $venda = (new Venda())->findById($id_venda);
        $adquirente = (new Adquirente())->findById(@$adquirente);

        if (@$adquirente) {

            if ($venda->meio_pagamento == "PagarMe" && @$adquirente->adquirente == "pagarme") {
                // $data["valor"] = number_format($venda->valor_bruto, 2, ".", "");
                $pagarme = (new PagarMe())->reembolso($data);

                if (@$pagarme->last_transaction->status == "refunded") {

                    // if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    // $venda->status_transacao = 7;
                    // else
                    $venda->status_transacao = 6;

                    $venda->comissionada = 2;
                    $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                    $venda->motivo = @$data["motivo"];

                    if ($venda->save()) {
                        echo "Sucesso";
                    } else {
                        echo "Erro ao atualizar";
                    }

                    if ($status != 9)
                        $this->cadastrarEstorno($venda->id);
                    elseif ($status == 9)
                        $this->chargebackComissoes($data);
                } else {
                    echo "Não foi possível realizar o estorno";
                    $data["venda"] = $venda->id;
                    $data["obj"] = $pagarme;
                    $this->logs($data, "pagarme-erro-estorno.txt");
                }

                exit;
            }

            if ($venda->meio_pagamento == "Cielo" && @$adquirente->adquirente == "cielo") {
                $data["valor"] = number_format($venda->valor_bruto, 2, ".", "");
                $data["pay_id"] = $venda->nit_pix;
                $cielo = (new Cielo())->reembolsoCartao($data);

                // var_dump($cielo);
                // echo $cielo->Status;
                if (@$cielo->Status == 10 || @$cielo->Status == 11) {

                    $venda->status_transacao = 6;
                    $venda->comissionada = 2;
                    $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                    $venda->motivo = @$data["motivo"];

                    if ($venda->save()) {
                        echo "Sucesso";
                    } else {
                        echo "Erro ao atualizar";
                    }

                    if ($status != 9)
                        $this->cadastrarEstorno($venda->id);
                    elseif ($status == 9)
                        $this->chargebackComissoes($data);
                } else {
                    echo "Não foi possível realizar o estorno";
                    $data["venda"] = $venda->id;
                    $data["obj"] = $cielo;
                    $this->logs($data, "cielo-erro-estorno.txt");
                }

                exit;
            }

            if ($venda->meio_pagamento == "MercadoPago" && @$adquirente->adquirente == "mercadopago") {


                if ($venda->status_transacao == 1 || $venda->status_transacao == 2)
                    $mercadopago = (new MercadoPago())->reembolsoCartao($data);
                elseif ($venda->status_transacao == 3 || $venda->status_transacao == 4) {
                    $mercadopago = (new MercadoPago())->chargeback($data);
                }

                if (@$mercadopago->status == "cancelled" || @$mercadopago->status == "approved") {

                    // if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    // $venda->status_transacao = 7;
                    // else
                    $venda->status_transacao = 6;

                    $venda->comissionada = 2;
                    $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                    $venda->motivo = @$data["motivo"];

                    if ($venda->save()) {
                        echo "Sucesso";
                    } else {
                        echo "Erro ao atualizar. \n Observação: Para estornar o pagamento via PIX ou Boleto é necessario que você possua saldo em sua conta do Mercado Pago! ";
                    }

                    if ($status != 9)
                        $this->cadastrarEstorno($venda->id);
                    elseif ($status == 9)
                        $this->chargebackComissoes($data);
                } else {
                    echo "Não foi possível realizar o estorno";
                    $data["venda"] = $venda->id;
                    $data["obj"] = $mercadopago;
                    $this->logs($data, "mercadopago-erro-estorno.txt");
                }

                exit;
            }

            if ($venda->meio_pagamento == "Moip" && @$adquirente->adquirente == "wirecard") {
                $wirecard = new Wirecard();

                if ($tipoVenda == 1 && ($status == 4 || $status == 9)) {
                    $pagamento = $wirecard->verPagamento($data);
                    $refund = $wirecard->reembolsoCartao($data);

                    // var_dump($refund);
                    if (@$refund->status == "COMPLETED" || $pagamento->status == "REFUNDED" || $pagamento->status == "REVERSED") {
                        // if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                        // $venda->status_transacao = 7;
                        // else
                        $venda->status_transacao = 6;

                        $venda->comissionada = 2;
                        $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                        $venda->observacao = @$data["motivo"];
                        $venda->save();

                        echo "Reembolso efetuado";

                        if ($status != 9)
                            $this->cadastrarEstorno($venda->id);
                        elseif ($status == 9)
                            $this->chargebackComissoes($data);
                    } else {
                        echo "Não foi possível realizar o estorno";

                        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                            echo $pagamento->status;

                        $data["venda"] = $venda->id;
                        $data["obj"] = $refund;
                        $this->logs($data, "wirecard-erro-estorno.txt");
                    }
                } else if ($tipoVenda == 1 && $status == 2) {
                    echo "Não é possivel cancelar a venda que ainda está em análise com a Wirecard";
                }
            }

            if ($venda->meio_pagamento == "PagSeguro" && @$adquirente->adquirente == "pagseguro") {
                // $pagseguro = new Pagseguro();
                // echo "tipo venda:" . $tipoVenda;
                // exit;
                $data["nit_pix"] = @$venda->nit_pix;
                if ($venda->status_transacao == 4 && ($tipoVenda == 1 || $tipoVenda == 3)) {
                    $pagseguro = (new Pagseguro())->buscarPedido($data);
                    if (@$pagseguro->charges[0]->id) {
                        $data["charge_id"] = @$pagseguro->charges[0]->id;
                        // $data["valor_total"] = @$pagseguro->charges[0]->amount->value;
                        $data["valor_total"] = 360;
                        $pagseguro = (new Pagseguro())->chargeback($data);
                    }
                } elseif ($tipoVenda == 2 && $venda->status_transacao == 4) {
                    $data["charge_id"] = @$venda->nit_pix;
                    $data["valor_total"] = @$venda->valor_bruto;
                    $pagseguro = (new Pagseguro())->chargeback($data);
                }
                // var_dump($refund);
                if (@$pagseguro->status == "CANCELED") {
                    // if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    // $venda->status_transacao = 7;
                    // else
                    $venda->status_transacao = 6;

                    $venda->comissionada = 2;
                    $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                    $venda->observacao = @$data["motivo"];
                    $venda->save();

                    echo "Reembolso efetuado";

                    if ($status != 9)
                        $this->cadastrarEstorno($venda->id);
                    elseif ($status == 9)
                        $this->chargebackComissoes($data);
                } else {
                    echo "Não foi possível realizar o estorno";

                    // if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    //     echo $pagseguro->status;

                    $data["venda"] = $venda->id;
                    $data["obj"] = $pagseguro;
                    $this->logs($data, "pagseguro-erro-estorno.txt");
                }
            }
        }
    }

    public function solicitarEstorno($data)
    {
        session_start();

        $data["assunto"]   = "Solicitação de chargeback";

        $data["mensagem"] = "
            <h2>Solicitação de chargeback</h2>
            <p>O produtor " . @$_SESSION["nome"] . " solicitou um chargeback</p><br>
            <br><p>ID venda: " . $data["id"] . "</p>
            <br><p>Transação: " . $data["transacao"] . "</p>
            <br><p>Motivo: " . $data["motivo"] . "</p>
        ";
        $data["email"] = "suporte.oficial@redrocketpay.com";

        if (AMBIENTE == "produção")
            $enviaEmail = (new Emails())->enviaSendGrid($data);

        echo $enviaEmail;
        echo "Solicitação enviada";
    }

    public function alertaChargeback($data)
    {
        $venda = (new Venda())->findById($data["venda"]);

        if ($venda) {
            $emailProdutor = $venda->dataProdutor()->email;
            $emailSuporte = $venda->dataProduto()->email_chargeback ? $venda->dataProduto()->email_chargeback : $venda->dataProduto()->email_suporte;

            $data["assunto"]   = "Pré-Contestação de transação - Envie os comprovantes da venda ($venda->transacao)";

            $data["mensagem"] = "
                <h3>Pré-Contestação de transação - Envie os comprovantes da venda ($venda->transacao)</h3><br>

                Olá, tudo bem?<br>
                Um dos seus clientes não reconheceu o valor na fatura do cartão de crédito e, por isso, abriu uma 
                solicitação de contestação da transação $venda->transacao, junto ao emissor do cartão. Por esse motivo, 
                recebemos da instituição financeira o pedido de comprovação dessa transação. 
                Seguem abaixo os dados da transação: <br><br>

                <b>Pedido:</b> " . $venda->transacao . "<br>
                <b>Data do pedido:</b> " . formataDataHora($venda->created_at) . "<br>
                <b>Data finalizado:</b> " . formataDataHora($venda->data_finalizada) . "<br>
                <b>Valor do produto:</b> " . number_format($venda->valor_produto + $venda->valor_frete, 2, ",", ".") . "<br><br>

                <b>O que você precisa fazer:</b><br><br>
                <b>1</b> - Separar documentação necessária<br>
                    Ver lista de documentos obrigatórios <a href='" . url("documentos/documentacao-chargeback.pdf") . "'>" . url("documentos/documentacao-chargeback.pdf") . "</a><br><br>

                <b>2</b> - Responder este e-mail com os documentos em anexo;<br>
                    Confira se todos os dados estão corretos e tire dúvidas por aqui mesmo <br><br>

                <b>3</b> - Todos os documentos devem estar em anexos e em <b>formato PDF</b><br><br>

                <b>Mais informações</b><br><br>

                <b>O que isso significa?</b><br>
                A contestação ainda está em análise pelo emissor do cartão de crédito e o valor foi bloqueado da sua 
                Conta RedRocket até que o processo finalize. Para tentar evitar que esse caso evolua para um chargeback e 
                este valor seja debitado da sua conta é preciso enviar a documentação comprobatória dentro de até 5 dias corridos.
                Caso seja aprovado a documentação o valor será desbloqueado.<br>
                
                <br><b>Como funciona o processo?</b><br><br>

                Esse processo não aparece em sua Conta (Central de resolução RedRocket), pois é tratado com as instituições financeiras. 
                A RedRocket envia seus documentos para a emissora do cartão que fica responsável por analisar a documentação e 
                acatar ou não o chargeback. Como esse processo inicial ocorre com as emissoras, não temos um acompanhamento 
                do processo!<br><br>

                <br><b>Não deixe para a última hora, quanto antes você enviar os documentos comprobatórios melhor!</b><br><br>

                Estamos à disposição para qualquer esclarecimento necessário e esperamos que você consiga reverter essa contestação.<br>

                Atenciosamente,
            ";
            $data["email"] = $emailProdutor;

            $enviaEmail = (new Emails())->enviaSendGrid($data);
            // echo $enviaEmail;

            $data["email"] = $emailSuporte;

            $enviaEmail = (new Emails())->enviaSendGrid($data);

            $this->bloquearComissoes($data);
        }
    }

    public function bloquearComissoes($data)
    {
        $this->buscarComissoes($data);

        $comissoes = (new Comissao())->find("venda = '{$data['venda']}'")->fetch(true);
        $comissoesBloqueadas = (new Comissao())->find("venda = '{$data['venda']}' AND tipo = 3")->fetch(true);
        // var_dump($comissoesBloqueadas);

        if ($comissoes && !$comissoesBloqueadas) {
            foreach ($comissoes as $comissao) {
                // echo "$comissao->id, $comissao->descricao, " . -$comissao->valor . "\n";

                $com = new Comissao();
                $com->usuario = $comissao->usuario;
                $com->venda = $comissao->venda;
                $com->produto = $comissao->produto;
                $com->descricao = "Bloqueio por alerta de chargeback";
                $com->valor = -$comissao->valor;
                $com->tempo_saque = date("Y-m-d");
                $com->disponivel = 0;
                $com->tipo = 3;
                $com->data_pedido = $comissao->data_finalizada;

                $com->save();
            }

            echo "Saldo bloqueado com sucesso";
            $this->atualizarSaldo();

            $venda = (new Venda())->findById($data["venda"]);
            $venda->status_transacao = 9;
            $venda->save();
        } else {
            echo "Tente novamente mais tarde";
        }
    }

    public function chargebackComissoes($data)
    {
        $comissoes = (new Comissao())->find("venda = '{$data['venda']}' AND descricao LIKE 'Bloqueio por alerta de chargeback'")->fetch(true);
        $comissoesBloqueadas = (new Comissao())->find("venda = '{$data['venda']}' AND tipo = 3")->fetch(true);
        // var_dump($comissoesBloqueadas);

        if ($comissoes) {
            foreach ($comissoes as $comissao) {
                // echo "$comissao->id, $comissao->descricao, " . -$comissao->valor . "\n";

                $comissao->descricao = "Chargeback venda bloqueada";
                $comissao->disponivel = 2;
                $comissao->tipo = 1;

                $comissao->save();
            }

            echo "Chargeback realizado com sucesso";
        } else {
            echo "Tente novamente mais tarde";
        }
    }

    public function desbloquearComissoes($data)
    {
        $this->buscarComissoes($data);

        $comissoes = (new Comissao())->find("venda = '{$data['venda']}' AND descricao LIKE '%Bloqueio por alerta de chargeback%'")->fetch(true);

        if ($comissoes) {
            foreach ($comissoes as $comissao) {
                // echo "$comissao->id, $comissao->descricao, " . -$comissao->valor . "\n";

                $com = new Comissao();
                $com->usuario = $comissao->usuario;
                $com->venda = $comissao->venda;
                $com->produto = $comissao->produto;
                $com->descricao = "Desbloqueio de venda";
                $com->valor = -$comissao->valor;
                $com->tempo_saque = date("Y-m-d");
                $com->disponivel = 0;
                $com->tipo = 1;
                $com->data_pedido = $comissao->data_finalizada;

                $com->save();
            }
            echo "Saldo desbloqueado com sucesso";

            $venda = (new Venda())->findById($data["venda"]);
            $venda->status_transacao = 4;
            $venda->save();
            $this->atualizarSaldo();
        } else {
            echo "Tente novamente mais tarde";
        }
    }

    public function buscarComissoes($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => url("comissao/busca/") . $data["venda"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
    }

    public function atualizarSaldo()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => url("comissao/saldo"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
    }

    public function autenticidade($data)
    {
        var_dump($_POST);

        $texto = "data: " . date("Y-m-d H:i:s") . "\n";
        if (@$_POST) {
            foreach ($_POST as $key => $value) {
                $texto .= "'$key': '$value', \n";
            }
            $estorno = new Estorno();
            $estorno->venda = $_POST["pedido"];
            $estorno->nit = $_POST["nit"];
            $estorno->status = 1;
            if ($estorno->save()) {
                $texto .= "'status-estorno': 'salvo', \n";
                // $this->cancelarVenda();
            } else {
                $texto .= "'status-estorno': '" . $estorno->fail()->getMessage() . "', \n";
            }
        }

        $arquivo = fopen('estorno-pix.txt', 'a+');
        fwrite($arquivo, $texto . "\n");
        fclose($arquivo);
    }

    public function cancelamentoPix($data)
    {
        $url = $this->endpoint_pix . "cancellations";
        $json = '{"esitef_usn": "' . $data["transacao"] . '"}';
        $cancela = $this->curlPix($url, "POST", $json);
        // var_dump($cancela);

        sleep(5);
        $this->cancelarVenda2($data);
        if (@$cancela->code != 0) {
            echo "$cancela->code - $cancela->message";
        }

        // echo "cancelando...";
    }

    public function cancelarVenda2($data)
    {
        $estorno = (new Estorno())->find("venda = " . $data["id"])->fetch();

        if ($estorno) {
            $url = $this->endpoint_pix . "cancellations/$estorno->nit";
            $json = '{}';
            $cancela = $this->curlPix($url, "PUT", $json);
            // var_dump($cancela);

            if (@$cancela->cancellation->status == "CON") {
                $venda = (new Venda())->findById($estorno->venda);
                $venda->status_transacao = 6;
                $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                $venda->motivo = @$data["motivo"];

                if ($venda->save()) {
                    echo "Venda estornada";
                } else {
                    echo $venda->fail()->getMessage();
                }
            } else {
                echo @$cancela->cancellation->authorizer_message . " - " . @$cancela->message;
            }

            $est = (new Estorno())->findById($estorno->id);
            $estorno->status = 2;
            $estorno->save();
        }
    }

    public function cancelarVenda($data)
    {
        $estornos = (new Estorno())->find("status = 1")->fetch(true);

        if ($estornos) {
            foreach ($estornos as $estorno) {
                $url = $this->endpoint_pix . "cancellations/$estorno->nit";
                $json = '{}';
                $cancela = $this->curlPix($url, "PUT", $json);
                // var_dump($cancela);

                if (@$cancela->cancellation->status == "CON") {
                    $venda = (new Venda())->findById($estorno->venda);
                    $venda->status_transacao = 6;
                    $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                    $venda->motivo = @$data["motivo"];

                    if ($venda->save()) {
                        echo "Venda estornada";
                    } else {
                        echo $venda->fail()->getMessage();
                    }
                } else {
                    echo @$cancela->cancellation->authorizer_message . " - " . @$cancela->message;
                }

                $est = (new Estorno())->findById($estorno->id);
                $estorno->status = 2;
                $estorno->save();
            }
        }
    }

    public function curlPix($url, $metodo, $json)
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
                "merchant_key: $this->token_pix",
                "merchant_id: $this->key_pix",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function cadastrarEstorno($venda)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => url("estorno/$venda"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
    }

    public function notificacao($data)
    {
        $content = http_build_query(array(
            "venda"             => @$data["venda"],
            "forma_pagamento"   => @$data["forma_pagamento"],
            "produto"           => @$data["produto"],
            "produtor"          => @$data["produtor"],
            "afiliado"          => @$data["afiliado"],
            "comissao_afiliado" => @$data["comissao_afiliado"],
            "transacao"         => @$data["transacao"],
        ));

        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded",
                'content' => $content,
            )
        ));

        $result = file_get_contents(url("notificacoes"), null, $context);
        //var_dump($result);
    }

    // CANCELAMENTO DE VENDA - MERCADO PAGO
    public function cancelarvendaMP($data)
    {
        $curl = curl_init();
        $url =  "https://api.mercadopago.com/v1/payments/" . @$data["transacao"];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT'
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
    }
}

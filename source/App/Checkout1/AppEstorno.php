<?php

namespace Source\App\Checkout1;

use League\Plates\Engine;
use Source\App\Asaas\AsaasCobrancas;
use Source\Models\Vendas\Venda;
use Source\App\Checkout1\Wirecard;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Estorno\Estorno;
use Source\Models\Enderecos\Endereco;
use Source\Models\Comissao\Comissao;
use Source\App\Checkout1\PagarMe;

use Source\Helpers\Emails;

use Source\App\Postback\AppPostback;
use Source\App\Checkout1\AppProcessos;
use Source\App\CheckoutTransparente\MercadoPago;
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
        $this->validacao = new Validacao();

        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(1);
        $configuracaoPix = (new ConfiguracaoCheckout())->findById(2);
        if ($configuracaoCheckout->sandbox == 1) {
            $this->endpoint = "https://sandbox.moip.com.br";
            $this->token = $configuracaoCheckout->token_sandbox;
            $this->key = $configuracaoCheckout->chave_sandbox;

            $this->endpoint_pix = "https://esitef-homologacao.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoPix->token_sandbox;
            $this->key_pix = $configuracaoPix->chave_sandbox;
        } else {
            $this->endpoint = "https://api.moip.com.br";
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;

            $this->endpoint_pix = "https://esitef-ec.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoPix->token_producao;
            $this->key_pix = $configuracaoPix->chave_producao;
        }
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

    public function logs2($data, $arq)
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

    public function estornar($data)
    {
        session_start();

        $id_venda = $data["id"];
        $tipoVenda = $data["forma_pagamento"];
        $transacao = $data["transacao"];
        $data["venda"] = $data["id"];
        $status = $data["status"];

        $asaasCobrancas = new AsaasCobrancas();
        $venda = (new Venda())->findById($id_venda);
        $data["nome"] = $venda->dataProdutor()->nome;
        $data["email"] = $venda->dataProdutor()->email;
        $data["nit_pix"] = $venda->nit_pix;

        if ($venda->meio_pagamento != "PagarMe" && ($tipoVenda == 1 || $tipoVenda == 3) && ($status == 4 || $status == 9)) {

            if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                $venda->status_transacao = 7;
            else
                $venda->status_transacao = 6;

            $venda->comissionada = 2;
            $venda->data_ultimo_evento = date("Y-m-d H:i:s");
            $venda->observacao = @$data["motivo"];
            $venda->save();

            echo "Reembolso efetuado";

            if ($status != 9) {
                $this->cadastrarEstorno($venda->id);
            } elseif ($status == 9) {
                $this->chargebackComissoes($data);
                $this->emailChargeback($data);
            }
        }

        if ($venda->meio_pagamento == "PagarMe") {
            $data["valor"] = number_format($venda->valor_bruto, 2, ".", "");
            $pagarme = (new PagarMe())->reembolsoCartao($data);

            if (@$pagarme->status == "canceled") {

                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    $venda->status_transacao = 7;
                else
                    $venda->status_transacao = 6;

                $venda->comissionada = 2;
                $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                $venda->motivo = @$data["motivo"];

                if ($venda->save()) {
                    echo "Sucesso";
                } else {
                    echo "Erro ao atualizar";
                }

                if ($status != 9) {
                    $this->cadastrarEstorno($venda->id);
                } elseif ($status == 9) {
                    $this->chargebackComissoes($data);
                    $this->emailChargeback($data);
                }
            } else {
                echo "Não foi possível realizar o estorno";
                $data["venda"] = $venda->id;
                $data["obj"] = $pagarme;
                $this->logs($data, "pagarme-erro-estorno.txt");
            }

            exit;
        }

        if ($tipoVenda == 2 && $status == 4) {
            // var_dump($data);

            $this->logs2($data, "estorno-boleto");

            $data["descricao"] = "Solicitação de estorno de boleto";
            $this->bloquearComissoes($data);

            $dados["assunto"] = "Solicitação de estorno de boleto";
            $dados["mensagem"] = "<h2>Solicitação de estorno de boleto</h2>" .
                "O cliente " . $venda->dataEndereco()->nome . " solicitou o estorno de uma compra no boleto. <br><br>" .
                "<b>Motivo do estorno:</b> " . @$data["motivo"] . "<br>" .
                "<b>Venda:</b> " . @$data["venda"] . "<br>" .
                "<b>Transação:</b> " . @$data["transacao"] . "<br>" .
                "<b>Meio de pagamento:</b> " . @$venda->meio_pagamento . "<br><br>" .
                "<h3>Dados do solicitante:</h3>";

            $dados["mensagem"] .= "<b>Nome:</b> {$venda->dataEndereco()->nome} <br>" .
                "<b>Email:</b> {$venda->dataEndereco()->email} <br>" .
                "<b>CPF/CNPJ:</b> {$venda->dataEndereco()->cpf_cnpj} <br>" .
                "<b>Telefone:</b> {$venda->dataEndereco()->telefone} <br>" .
                "<b>Banco:</b> " . @$data["banco"] . "<br>" .
                "<b>Agência:</b> " . @$data["agencia"] . "<br>" .
                "<b>Conta:</b> " . @$data["conta"] . "<br>" .
                "<b>Tipo de conta:</b> " . @$data["tipo"] . "<br>" .
                "<b>Chave Pix:</b> " . @$data["chave_pix"] . "<br>";
            $dados["email"] = "pedro@redrocketpay.com";

            if (AMBIENTE == "produção")
                $enviaEmail = (new Emails())->enviaSendGrid($dados);
        }
    }

    public function estornarOriginal($data)
    {
        session_start();

        $id_venda = $data["id"];
        $tipoVenda = $data["forma_pagamento"];
        $transacao = $data["transacao"];
        $data["venda"] = $data["id"];
        $status = $data["status"];

        $asaasCobrancas = new AsaasCobrancas();
        $venda = (new Venda())->findById($id_venda);
        $data["nome"] = $venda->dataProdutor()->nome;
        $data["email"] = $venda->dataProdutor()->email;
        $data["nit_pix"] = $venda->nit_pix;

        if ($venda->meio_pagamento == "Asaas" && ($tipoVenda == 1 || $tipoVenda == 3) && ($status == 4 || $status == 9)) {
            $pagamento = $asaasCobrancas->dadosCobranca($data["transacao"]);

            $errors = false;
            if ($pagamento->status == "RECEIVED" && $pagamento->status != "CONFIRMED") {
                $refund = $asaasCobrancas->estornarCobranca($data);
                $errors = $refund->errors ? true : false;
            }

            // var_dump($refund);
            if (!$errors && ($pagamento->status == "REFUNDED" || $pagamento->status == "REVERSED" || $pagamento->status == "CHARGEBACK_REQUESTED")) {
                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    $venda->status_transacao = 7;
                else
                    $venda->status_transacao = 6;

                $venda->comissionada = 2;
                $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                $venda->observacao = @$data["motivo"];
                $venda->save();

                echo "Reembolso efetuado";

                if ($status != 9) {
                    $this->cadastrarEstorno($venda->id);
                } elseif ($status == 9) {
                    $this->chargebackComissoes($data);
                    $this->emailChargeback($data);
                }
            } else {
                echo "Não foi possível realizar o estorno\n";

                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    echo $pagamento->status;

                $data["venda"] = $venda->id;
                $data["obj"] = $refund;
                $this->logs($data, "asaas-erro-estorno.txt");
            }
        }

        if ($venda->meio_pagamento == "Cielo") {

            $data["valor"] = number_format($venda->valor_bruto, 2, ".", "");
            $data["pay_id"] = $venda->nit_pix;
            $estorno = (new Cielo())->reembolsoCartao($data);
            $cielo = (new Cielo())->buscarPedido($venda->nit_pix);

            // var_dump($cielo->Status);
            // echo $cielo->Status;
            if (@$cielo->Payment->Status == 11 || @$cielo->Payment->Status == 10) {

                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    $venda->status_transacao = 7;
                else
                    $venda->status_transacao = 6;

                $venda->comissionada = 2;
                $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                $venda->motivo = @$data["motivo"];

                if ($venda->save()) {
                    echo "Sucesso";
                } else {
                    echo "Erro ao atualizar";
                }

                if ($status != 9) {
                    $this->cadastrarEstorno($venda->id);
                } elseif ($status == 9) {
                    $this->chargebackComissoes($data);
                    $this->emailChargeback($data);
                }
            } else {
                echo "Não foi possível realizar o estorno";
                $data["venda"] = $venda->id;
                $data["obj"] = $cielo;
                $this->logs($data, "cielo-erro-estorno.txt");
            }

            exit;
        }

        if ($tipoVenda == 2 && $status == 4) {
            // var_dump($data);

            $this->logs2($data, "estorno-boleto");

            $data["descricao"] = "Solicitação de estorno de boleto";
            $this->bloquearComissoes($data);

            $dados["assunto"] = "Solicitação de estorno de boleto";
            $dados["mensagem"] = "<h2>Solicitação de estorno de boleto</h2>" .
                "O cliente " . $venda->dataEndereco()->nome . " solicitou o estorno de uma compra no boleto. <br><br>" .
                "<b>Motivo do estorno:</b> " . @$data["motivo"] . "<br>" .
                "<b>Venda:</b> " . @$data["venda"] . "<br>" .
                "<b>Transação:</b> " . @$data["transacao"] . "<br>" .
                "<b>Meio de pagamento:</b> " . @$venda->meio_pagamento . "<br><br>" .
                "<h3>Dados do solicitante:</h3>";

            $dados["mensagem"] .= "<b>Nome:</b> {$venda->dataEndereco()->nome} <br>" .
                "<b>Email:</b> {$venda->dataEndereco()->email} <br>" .
                "<b>CPF/CNPJ:</b> {$venda->dataEndereco()->cpf_cnpj} <br>" .
                "<b>Telefone:</b> {$venda->dataEndereco()->telefone} <br>" .
                "<b>Banco:</b> " . @$data["banco"] . "<br>" .
                "<b>Agência:</b> " . @$data["agencia"] . "<br>" .
                "<b>Conta:</b> " . @$data["conta"] . "<br>" .
                "<b>Tipo de conta:</b> " . @$data["tipo"] . "<br>" .
                "<b>Chave Pix:</b> " . @$data["chave_pix"] . "<br>";
            $dados["email"] = "pedro@redrocketpay.com";

            if (AMBIENTE == "produção")
                $enviaEmail = (new Emails())->enviaSendGrid($dados);
        }
    }

    public function estorna($data)
    {
        session_start();

        // var_dump($_SESSION);
        // exit;
        $wirecard = new Wirecard();

        $id_venda = $data["id"];
        $tipoVenda = $data["forma_pagamento"];
        $transacao = $data["transacao"];
        $data["venda"] = $data["id"];
        $status = $data["status"];

        $venda = (new Venda())->findById($id_venda);
        $data["nome"] = $venda->dataProdutor()->nome;
        $data["email"] = $venda->dataProdutor()->email;

        if ($venda->meio_pagamento == "PagarMe") {
            $data["valor"] = number_format($venda->valor_bruto, 2, ".", "");
            $pagarme = (new PagarMe())->reembolsoCartao($data);

            if (@$pagarme->status == "paid") {

                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    $venda->status_transacao = 7;
                else
                    $venda->status_transacao = 6;

                $venda->comissionada = 2;
                $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                $venda->motivo = @$data["motivo"];

                if ($venda->save()) {
                    echo "Sucesso";
                } else {
                    echo "Erro ao atualizar";
                }

                if ($status != 9) {
                    $this->cadastrarEstorno($venda->id);
                } elseif ($status == 9) {
                    $this->chargebackComissoes($data);
                    $this->emailChargeback($data);
                }
            } else {
                echo "Não foi possível realizar o estorno";
                $data["venda"] = $venda->id;
                $data["obj"] = $pagarme;
                $this->logs($data, "pagarme-erro-estorno.txt");
            }

            exit;
        }

        if ($venda->meio_pagamento == "Cielo") {

            $data["valor"] = number_format($venda->valor_bruto, 2, ".", "");
            $data["pay_id"] = $venda->nit_pix;
            $cielo = (new Cielo())->reembolsoCartao($data);

            // var_dump($cielo);
            // echo $cielo->Status;
            if (@$cielo->Status == 11) {

                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    $venda->status_transacao = 7;
                else
                    $venda->status_transacao = 6;

                $venda->comissionada = 2;
                $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                $venda->motivo = @$data["motivo"];

                if ($venda->save()) {
                    echo "Sucesso";
                } else {
                    echo "Erro ao atualizar";
                }

                if ($status != 9) {
                    $this->cadastrarEstorno($venda->id);
                } elseif ($status == 9) {
                    $this->chargebackComissoes($data);
                    $this->emailChargeback($data);
                }
            } else {
                echo "Não foi possível realizar o estorno";
                $data["venda"] = $venda->id;
                $data["obj"] = $cielo;
                $this->logs($data, "cielo-erro-estorno.txt");
            }

            exit;
        }

        if ($venda->meio_pagamento == "Moip" && $tipoVenda == 1 && ($status == 4 || $status == 9)) {
            $pagamento = $wirecard->verPagamento($data);
            $refund = $wirecard->reembolsoCartao($data);

            // var_dump($refund);
            if (@$refund->status == "COMPLETED" || $pagamento->status == "REFUNDED" || $pagamento->status == "REVERSED") {
                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    $venda->status_transacao = 7;
                else
                    $venda->status_transacao = 6;

                $venda->comissionada = 2;
                $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                $venda->observacao = @$data["motivo"];
                $venda->save();

                echo "Reembolso efetuado";

                if ($status != 9) {
                    $this->cadastrarEstorno($venda->id);
                } elseif ($status == 9) {
                    $this->chargebackComissoes($data);
                    $this->emailChargeback($data);
                }
            } else {
                echo "Não foi possível realizar o estorno";

                if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    echo $pagamento->status;

                $data["venda"] = $venda->id;
                $data["obj"] = $refund;
                $this->logs($data, "wirecard-erro-estorno.txt");
            }
        }

        if ($tipoVenda == 2 && $status == 4) {
            // var_dump($data);

            $this->logs2($data, "estorno-boleto");

            $data["descricao"] = "Solicitação de estorno de boleto";
            $this->bloquearComissoes($data);

            $dados["assunto"] = "Solicitação de estorno de boleto";
            $dados["mensagem"] = "<h2>Solicitação de estorno de boleto</h2>" .
                "O cliente " . $venda->dataEndereco()->nome . " solicitou o estorno de uma compra no boleto. <br><br>" .
                "<b>Motivo do estorno:</b> " . @$data["motivo"] . "<br>" .
                "<b>Venda:</b> " . @$data["venda"] . "<br>" .
                "<b>Transação:</b> " . @$data["transacao"] . "<br>" .
                "<b>Meio de pagamento:</b> " . @$venda->meio_pagamento . "<br><br>" .
                "<h3>Dados do solicitante:</h3>";

            $dados["mensagem"] .= "<b>Nome:</b> {$venda->dataEndereco()->nome} <br>" .
                "<b>Email:</b> {$venda->dataEndereco()->email} <br>" .
                "<b>CPF/CNPJ:</b> {$venda->dataEndereco()->cpf_cnpj} <br>" .
                "<b>Telefone:</b> {$venda->dataEndereco()->telefone} <br>" .
                "<b>Banco:</b> " . @$data["banco"] . "<br>" .
                "<b>Agência:</b> " . @$data["agencia"] . "<br>" .
                "<b>Conta:</b> " . @$data["conta"] . "<br>" .
                "<b>Tipo de conta:</b> " . @$data["tipo"] . "<br>" .
                "<b>Chave Pix:</b> " . @$data["chave_pix"] . "<br>";
            $dados["email"] = "pedro@redrocketpay.com";

            // if (AMBIENTE == "produção")
            $enviaEmail = (new Emails())->enviaSendGrid($dados);
        }
    }

    public function estornoCompleto($data)
    {
        $venda = (new Venda())->findById($data["venda"]);

        $venda->status_transacao = 6;
        $venda->data_ultimo_evento = date("Y-m-d H:i:s");
        $venda->save();

        $comissoes = (new Comissao())->find("venda = '{$data['venda']}' AND descricao LIKE 'Solicitação de estorno de boleto'")->fetch(true);

        if ($comissoes) {
            foreach ($comissoes as $comissao) {
                $comissao->descricao = "Estorno de boleto";
                $comissao->disponivel = 2;
                $comissao->tipo = 1;

                $comissao->save();
            }

            echo "Estorno realizado com sucesso";
        } else {
            echo "Tente novamente mais tarde";
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
            $data["nome"] = $venda->dataProdutor()->nome;

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
                $com->descricao = @$data["descricao"] ? $data["descricao"] : "Bloqueio por alerta de chargeback";
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

    public function emailChargeback($data)
    {
        $data["assunto"] = "Contestação Encerrada";
        $data["mensagem"] = "Olá " . @$data["nome"] .  ", tudo bem?<br><br>" .
            "<p>Como você sabe, até hoje estávamos concluindo o processo de tentativa de reversão da sua contestação" .
            @$data["transacao"] . " para que o saldo voltasse para sua conta da RedRocket.</p>" .
            "<p>O que acontece é que o nosso time de Chargeback fez uma análise para verificar se este pedido se enquadra no 
        Venda Protegida e dentro de nossas regras contratuais de negócios cobertos por esse tipo de incidente. 
        Mas não conseguimos reverter esse valor como saldo disponível.</p><br>" .
            "<p>Sim, sabemos que não é uma notícia fácil de digerir. Acredite: nós também sofremos com isso. Afinal também temos 
        nossas taxas descontadas uma vez que quem controla a contestação é a operadora e não a Moip</p>" .
            "<p>Mas, basicamente o nosso programa de  venda protegida cobre 0,5% sobre o valor de todas as transações realizadas 
        através de cartão  nos últimos 12 meses quando a venda é classificada como fraude.</p><br>" .
            "<p>É importante lembrar que essa contestação só acontece porque a operadora do cartão oferece de benefício ao seu 
        cliente a opção de contestar qualquer compra até 180 dias depois do pagamento da última parcela daquela compra. </p><br>" .
            "<p>Por isso, nós também saímos perdendo quando um destes casos acontece. </p><br>" .
            "<p>Queremos pedir desculpas pelo transtorno e nos colocamos à disposição para tirar qualquer dúvida sobre este ou 
        qualquer outro assunto.</p><br><br>
        <p>Conte conosco!</p>";

        $enviaEmail = (new Emails())->enviaSendGrid($data);
    }

    public function chargebackComissoes($data)
    {
        $comissoes = (new Comissao())->find("venda = '{$data['venda']}' AND descricao LIKE 'Bloqueio por alerta de chargeback'")->fetch(true);
        $comissoesBloqueadas = (new Comissao())->find("venda = '{$data['venda']}' AND tipo = 3")->fetch(true);
        // var_dump($comissoesBloqueadas);

        if ($comissoes) {
            foreach ($comissoes as $comissao) {
                // echo "$comissao->id, $comissao->descricao, " . -$comissao->valor . "\n";

                $comissao->descricao = @$data["descricao"] ? $data["descricao"] : "Chargeback venda bloqueada";
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

<?php

namespace Source\App\Assinaturas;

use League\Plates\Engine;

use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Vendas\Venda;

use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Configuracoes\ConfiguracaoCheckout;

use Source\Helpers\Emails;

use Source\App\Checkout1\Wirecard;
use Source\App\Checkout1\Bradesco;
use Source\App\Checkout1\Cielo;
use Source\App\Checkout1\Validacao;
use Source\App\Usuarios\AppUsuarios;
use Source\App\Processos\AppProcessos;
use Source\App\Checkout1\MercadoPago;

use Moip\Moip;
use Source\App\Asaas\AppAsaas;
use Source\App\Asaas\AsaasClientes;

class AppPagamentoAssinaturas
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
            define("ENDPOINT", Moip::ENDPOINT_SANDBOX);
            $this->token = $configuracaoCheckout->token_sandbox;
            $this->key = $configuracaoCheckout->chave_sandbox;

            $this->endpoint_pix = "https://esitef-homologacao.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoPix->token_sandbox;
            $this->key_pix = $configuracaoPix->chave_sandbox;
        } else {
            define("ENDPOINT", Moip::ENDPOINT_PRODUCTION);
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;

            $this->endpoint_pix = "https://esitef-ec.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoPix->token_producao;
            $this->key_pix = $configuracaoPix->chave_producao;
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

    public function splitPagamento($data)
    {
        // $data["comissoes_pagamento"]["redrocket"]["wallet_id"] = "23179f47-376c-4be6-b672-4a7451a72c4a";
        // $data["comissoes_pagamento"]["redrocket"]["comissao"] = $data["valor_redrocket"];

        $walletProdutor = (new AppAsaas(null))->verificarCliente($data["produtor"]);
        $data["comissoes_pagamento"]["produtor"]["wallet_id"] = $walletProdutor->wallet_id;
        $data["comissoes_pagamento"]["produtor"]["comissao"] = (float) $data["comissao_produtor"] + ((float) @$data["tx_frete"]);

        if (@$data["comissao_afiliado"]) {
            $walletAfiliado = (new AppAsaas(null))->verificarCliente($data["afiliado"]);
            $data["comissoes_pagamento"]["afiliado"]["wallet_id"] = $walletAfiliado->wallet_id;
            $data["comissoes_pagamento"]["afiliado"]["comissao"] = $data["comissao_afiliado"];
        }

        if (@$data["coprodutores"]) {
            $cop = explode("/", $data["coprodutores"]);
            $com = explode("/", $data["comissao_coprodutores"]);
            $i = 0;

            foreach ($cop as $cop) {
                if ($cop) {
                    $walletCoProdutor = (new AppAsaas(null))->verificarCliente($cop);
                    $data["comissoes_pagamento"][$cop]["wallet_id"] = $walletCoProdutor->wallet_id;
                    $data["comissoes_pagamento"][$cop]["comissao"] = $com[$i];
                }
                $i++;
            }
        }
    }

    public function transacao2($data)
    {
        var_dump($data);
    }

    public function transacao($data)
    {

        /** 
         * LIMPAR CARACTERES DO TELEFONE
         */
        $telefone = (new Validacao())->limpaStr($data["telefone"]);
        $data["ddd"] = substr($telefone, 0, 2);
        $data["telefone"] = substr($telefone, 2, 12);

        $data["pais"] = "BR";

        $cpfCount = strlen($data["cpf"]);
        if ($cpfCount <= 11) {
            $cpf = (new Validacao())->validaCPF($data["cpf"]);
            $data["documento"] = "CPF";
            $data["tipo_doc"] = 1;

            if (!$cpf) {
                $retorno["msg"] = "CPF inválido";
                echo json_encode($retorno);
                exit;
            }
        } else {
            $data["documento"] = "CNPJ";
            $data["tipo_doc"] = 2;
        }

        $resultado_bytes = random_bytes(5);
        $data["protocolo"] = strtoupper(bin2hex($resultado_bytes));
        $this->logs($data, "assinaturas-dados-enviados");

        $dataNasc = mt_rand(strtotime("1970-01-01"), strtotime("2000-01-01"));
        $date = new \DateTime();
        $date->setTimestamp($dataNasc);
        $nascimento = $date->format('Y-m-d');

        $data["nasc"] = @$data["nasc"] ? $data["nasc"] : $nascimento;

        $verificarCliente = (new AppAsaas(null))->verificarCliente($us->usuario);

        if ($verificarCliente) {
            $data["customer_id"] = @$verificarCliente->client_id;
        } else {
            $clienteAsaas = new AsaasClientes(null);
            $cliente = $clienteAsaas->novoCliente($data);
            $data["customer_id"] = @$cliente->id;
            $data["client_id"] = @$cliente->id;
            $data["id_user"] = @$cliente->id_user;

            $asaasClientes = (new AppAsaas(null))->cadastrarUsuarioAsaas($data);
            $data["passo"] = "passo 1";
        }

        if (@$cliente->errors) {
            $dataEmail["erros"]          = "Houve um erro ao efetuar a transação. Por favor, entre em contato com o suporte. et: 1 \n";
            $dataEmail["assunto"]        = "Erro no cadastro de comprador wirecard";
            $dataEmail["mensagem"]       = "Erro no cadastro de comprador wirecard <br>";
            $dataEmail["mensagem"]      .= "Protocolo: " . $data["protocolo"] . " <br>";

            $dataEmail["mensagem"]      .= "Dados enviados: <br><br>";

            foreach ($data as $key => $value) {
                $dataEmail["mensagem"]  .= "$key: $value<br>";
            }

            $dataEmail["mensagem"]  .= "<br>";
            foreach ($cliente->errors as $erros) {
                $dataEmail["mensagem"]  .= "Descrição: $erros->description<br>";
                $_SESSION["erros"]["comprador"][] = @$erros->code;
                $this->logs($dataEmail, "assinaturas-erro-comprador");
            }

            $dataEmail["email"]          = "dev.ti@redrocketpay.com";
            $enviaEmail = (new Emails())->enviaSendGrid($dataEmail);
        }

        $user = (new Usuario())->find("usuario_dados = " . $data['comprador'])->fetch();
        $data["usuario"] = @$user->id;

        /** 
         * CADASTRAR UM ENDEREÇO
         */
        $endereco = (new AppUsuarios(null))->cadastroEndereco($data);
        $data["endereco"] = $endereco;

        $data["data_ultimo_evento"] = date("Y-m-d H:i:s");
        $data["status_transacao"] = 1;

        if ($data["pagamento"] == "credit_card") {

            $data["forma_pagamento"] = 1;
            $valProd = $data["valor_produto"] + @$data["frete"];

            $preco = number_format($this->validacao->juros($data["parcela"], $data["taxa_juros"], $valProd), 2, ".", "");
            $preco = $preco * $data["parcela"];
            $preco = (string) $preco * 100;

            if (@$data["plano"]) $plano = (new Plano())->findById($data["plano"]);
            if (@$plano->assumir_juros) {
                $juros = $preco;

                $preco = number_format($valProd, 2, ".", "");
                $preco = (string) $preco * 100;
            } else {
                $juros = $preco;
            }

            $data["preco"] = $preco;
            $data["valor_bruto"] = ($preco / 100);
            $data["valor_frete"] = @$data["frete"];
            $data["valor_liquido"] -=  @$data["valor_desconto"];

            $data["valor_redrocket"] = number_format(($valProd * ($data["taxa_redrocket"] / 100)) + 1, 2);
            $data["valor_liquido"] = $valProd - $data["valor_redrocket"];

            /** 
             * CALCULAR O VALOR DOS JUROS
             */
            $val = $juros / 100;
            if ($val <= $data["valor_produto"]) {
                $data["valor_juros"] = 0;
            } else {
                $data["valor_juros"] = ($juros / 100) - @$data["valor_frete"] - $data["valor_produto"];
            }

            if (@$plano->assumir_juros) {
                $data["valor_liquido"] -= $data["valor_juros"];
            }

            $data["vencimento"] = date("Y-m-d", strtotime("+1 day"));
            $data = $this->splitPagamento($data);
        } else if ($data["pagamento"] == "boleto") {

            $data["forma_pagamento"] = 2;
            
            $data["boleto"] = @$boleto;
            if (@$boleto)
                $val =  ($data["valor_produto"] + @$data["frete"]) - $data["valor_desconto"];
            else
                $val =  $data["valor_produto"] + @$data["frete"];

            $data["valor_liquido"] -= @$data["valor_desconto"];
            $preco = explode(".", $val);
            $data["preco"] = $val * 100;
            $data["valor_bruto"] = $data["preco"] / 100;
            $data["valor_juros"] = 0.00;
            $data["valor_frete"] = @$data["frete"];
            $data["taxa_juros"] = 0;

            

            $valProd = $data["valor_produto"] + @$data["frete"];
            $data["valor_redrocket"] = number_format(($valProd * ($data["taxa_redrocket"] / 100)) + 1, 2);
            $data["valor_liquido"] = $valProd - $data["valor_redrocket"];

            $data["parcela"] = 1;

            if (@$data["vencimento"] == "") {
                if (date("D") != "Sun" || date("D") != "Mon" || date("D") != "Tue") {
                    $data["vencimento"] = date("Y-m-d", strtotime("+5 days"));
                } else if (date("D") == "Sat") {
                    $data["vencimento"] = date("Y-m-d", strtotime("+4 days"));
                } else {
                    $data["vencimento"] = date("Y-m-d", strtotime("+3 days"));
                }
            }

            // SPLIT DE PAGAMENTOS
            if (@$data["valor_frete"]) {
                $data["tx_frete"] = (float) @$data["valor_frete"] - ((float) @$data["valor_frete"] * (float) $data["taxa_redrocket"] / 100);
            }
            //$data = $this->splitPagamento($data);
        }

        $status_transacao = array(
            "RECEIVED" => 4,
            "PENDING" => 1,
            "AWAITING_RISK_ANALYSIS" => 2,
            "RECEIVED_IN_CASH" => 4,
            "CONFIRMED" => 4,
            "" => 5,
        );

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

        $status_transacaoM = array(
            "pending" => 1,
            "approved" => 4,
            "cancelled" => 5,
            "rejected" => 5,
        );

        //$cobrancaAsaas = new AsaasCobrancas();
        $mercadoPago = new MercadoPago;

        if ($data["forma_pagamento"] == 1) {
            //$transacao = $cobrancaAsaas->cobrancaCartaoCredito($data);
            //$data["transacao"] = @$transacao->id;
            //$data["nit_pix"] = @$transacao->installment;
            //$data["status_transacao"] = @$status_transacao[@$transacao->status];
            //$data["meio_pagamento"] = "Asaas";
        } elseif ($data["forma_pagamento"] == 2) {

            $transacao = $mercadoPago->compraBoleto($data);
            $data["transacao"] = @$transacao->id;
            $data["status_transacao"] = @$status_transacaoM[@$transacao->status];
            $data["meio_pagamento"] = "MercadoPago";
            $data["link_boleto"] = @$transacao->transaction_details->external_resource_url;

            //$linha_digitavel = $cobrancaAsaas->codigoBoleto($transacao->id);
            $data["linha_digitavel"] = @$transacao->transaction_details->digitable_line;
        }

        // MOSTRAR NA TELA OS ERROS DA WIRECARD
        if (@$transacao->errors) {
            @$dataEmail["erros"]          .= "Houve um erro ao efetuar a transação. Por favor, entre em contato com o suporte. et: 4 \n";
            $dataEmail["assunto"]        = "Erro no cadastro de transação wirecard";
            $dataEmail["mensagem"]       = "Erro no cadastro de transação wirecard <br>";
            $dataEmail["mensagem"]      .= "Protocolo: " . $data["protocolo"] . " <br>";

            $dataEmail["mensagem"]      .= $transacao->errors->description . "<br>";
            $dataEmail["mensagem"]      .= "Dados enviados: <br><br>";

            foreach ($data as $key => $value) {
                // if($key == "numcart" || $key == "cvv"){
                //     $value = "-";
                // }
                $dataEmail["mensagem"]  .= "$key: $value<br>";
            }

            foreach ($transacao->errors as $erros) {
                $dataEmail["mensagem"]  .= "Descrição: $erros->description<br>";
                @$data["descricao"] .= $erros->description;
                $data["status_transacao"] = 5;
                $_SESSION["erros"]["transacao"] = @$erros->description;
                $this->logs($data, "assinaturas-erro-transacao");
            }

            $dataEmail["email"]          = "dev.ti@redrocketpay.com";
            $enviaEmail = (new Emails())->enviaSendGrid($dataEmail);
        }

        if (@$data["venda"]) {
            $atualizaVenda = $this->atualizaVenda($data);
        } else {
             
            $cadastroVenda = $this->cadastroVenda($data);
            $data["msg"] = $cadastroVenda["msg"];
            $data["venda"] = $cadastroVenda["venda"];
            $produto = (new Produto())->findById($data["produto"]);
            $venda = (new Venda())->findById($data["venda"]);
            $venda->assinatura = @$data["assinatura"];
            $venda->save();
        }

        $_SESSION["pagamento"] = $data;
        $data["processos"] = (new AppProcessos(null))->cadastroProcessos($data);
        return json_encode($data);
    }

    public function transacaoPix2($data)
    {
        var_dump($this->endpoint_pix, $this->token_pix, $this->key_pix);
    }

    public function transacaoPix($data)
    {
        if ($data["email"] == "") {
            $data["email"] = $data["email_suporte"];
        }

        /** 
         * VALIDAR O CPF
         */
        $cpf = (new Validacao())->validaCPF($data["cpf"]);
        // echo $cpf;

        if (!$cpf) {
            $retorno["msg"] = "CPF inválido";
            echo json_encode($retorno);
            exit;
        }

        /** 
         * LIMPAR CARACTERES DO TELEFONE
         */
        $telefone = (new Validacao())->limpaStr($data["telefone"]);
        $data["ddd"] = substr($telefone, 0, 2);
        $data["telefone"] = substr($telefone, 2, 12);

        $data["tipo_doc"] = 1;
        $data["pais"] = "BR";

        $resultado_bytes = random_bytes(5);
        $data["protocolo"] = strtoupper(bin2hex($resultado_bytes));
        $this->logs($data, "assinaturas-dados-enviados");

        /** 
         * VERIFICA SE CLIENTE TEM CADASTRO
         */
        $usuario = (new UsuarioDados());
        $cadastrado = $usuario->verificaCpf(["cpf" => $data["cpf"]]);

        if ($cadastrado) {
            $data["id"] = $cadastrado;

            $user = (new Usuario())->find("usuario_dados = $cadastrado")->fetch();
            $data["usuario"] = $user->id;
            $data["comprador"] = $cadastrado;
        } else {
            $comprador = (new AppUsuarios(null))->cadastroUsuarioDados($data);
            $data["comprador"] = $comprador;

            $user = (new Usuario())->find("usuario_dados = $comprador")->fetch();
            $data["usuario"] = $user->id;
        }

        /** 
         * CADASTRAR UM ENDEREÇO
         */
        $endereco = (new AppUsuarios(null))->cadastroEndereco($data);
        $data["endereco"] = $endereco;

        $data["meio_pagamento"] = "Asaas";

        if (@$data["meio_pagamento_desconto"]) {
            $meio_pagamento_desconto = explode(",", $data["meio_pagamento_desconto"]);
            foreach ($meio_pagamento_desconto as $pagamento) {
                if ($pagamento == 3 || $pagamento == 10) {
                    $pix = true;
                }
            }
        }

        if (@$pix && @$data["valor_desconto"] > 0)
            $val =  ($data["valor_produto"] + @$data["frete"]) - @$data["valor_desconto"];
        else
            $val =  ($data["valor_produto"] + @$data["frete"]);

        $preco = explode(".", $val);
        $data["preco"] = $val * 100;

        $data["valor_bruto"] = $data["preco"] / 100;
        $data["valor_juros"] = 0.00;
        $data["valor_frete"] = @$data["frete"];
        $data["taxa_juros"] = 0;

        $valProd = $data["valor_produto"] + @$data["frete"];
        $data["valor_redrocket"] = number_format(($valProd * ($data["taxa_redrocket"] / 100)) + 1, 2);
        $data["valor_liquido"] = $valProd - $data["valor_redrocket"];

        $data["valor_desconto"] = @$data["valor_desconto"];
        $data["valor_liquido"] -=  @$data["valor_desconto"];

        $data["parcela"] = 1;
        $data["data_ultimo_evento"] = date("Y-m-d H:i:s");

        $data["status_transacao"] = 1;
        $data["forma_pagamento"] = 3;

        if (@$data["venda"]) {
            $venda = $this->atualizaVenda($data);
        } else {
            $venda = $this->cadastroVenda($data);
        }

        $data["venda"] = $venda["venda"];
        $dados["venda"] = $venda["venda"];
        $_SESSION["pagamento"] = $data;
        // echo $venda;

        $dadosPix = $this->dadosPix($data);
        $urlTransacao = $this->endpoint_pix . "transactions";
        $transacaoPix = $this->curlPix($urlTransacao, "POST", $dadosPix);
        // var_dump($dadosPix, $transacaoPix);
        $urlEfetivacao = $this->endpoint_pix . "payments/{$transacaoPix->payment->nit}";
        $efetivacao = $this->curlPix($urlEfetivacao, "POST", "{}");

        $dados["qr_code"] = @$efetivacao->payment->qr_code;
        $dados["nit_pix"] = @$transacaoPix->payment->nit;

        $venda1 = @(new Venda())->findById(@$venda["venda"]);
        $venda1->qr_code = @$dados["qr_code"];
        $venda1->nit_pix = @$dados["nit_pix"];
        if ($venda1->save()) {
            $dados["msg_pix"] = "Venda atualizada";
        } else {
            $dados["msg_pix"] = $venda1->fail()->getMessage();
        }
        // $data["transacao"] = $transacao1->id;


        // $cartao = (new Emails())->cartao($data);
        // $dados["enviaEmail"] = @(new Emails())->enviaSendGrid($cartao);

        // @$data["venda"] = @$venda["venda"];
        // if($venda["msg"] == "Venda salva com sucesso"){
        //     $this->notificacao($data);
        // }

        $dados["msg"] = $venda["msg"];
        $dados["whatsapp"] = @$venda["whatsapp"];
        $dados["coprodutores"] = @$venda["coprodutores"];
        $dados["status_transacao"] = $data["status_transacao"];
        $dados["url"] = url("checkout/obrigado");
        (new AppProcessos(null))->processos($data);
        echo json_encode($dados);
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

        return $json;
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

                    if ($venda->status_transacao != $status[$consulta->payment->status]) {
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
                "merchant_key: $this->key_pix",
                "merchant_id: $this->token_pix",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function cadastroVenda(array $data)
    {
        var_dump($data);
        //$data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        

        $venda = new Venda();
        $venda->transacao               = @$data["transacao"];
        $venda->nit_pix                 = @$data["nit_pix"]; // GUARDA O NÚMERO NIT OU O PAYMENTID
        $venda->upsell                  = @$data["upsell"];
        $venda->produto                 = @$data["produto"];

        if (@$data["planos_orderBump"] && @$data["cod_preco_ordBump"] && @$data["total_ord_bump"]) {
            $venda->planos_order_bump = $data["planos_orderBump"];
            $venda->preco_order_bump = $data["cod_preco_ordBump"];
        }

        if (@$data["cookie_telev_atend"])
            $venda->atendente_televendas = $data["cookie_telev_atend"];

        if (@$data["plano"])
            $venda->plano                   = @$data["plano"];

        $venda->comprador               = @$data["comprador"];
        $venda->id_comprador            = @$data["id_comprador"];
        $venda->produtor                = @$data["produtor"];
        $venda->comissao_produtor       = @$data["comissao_produtor"];
        $venda->produtor                = @$data["produtor"];

        if (@$data["campanha"])
            $venda->campanha                = @$data["campanha"];

        $venda->afiliado                = @$data["afiliado"];
        $venda->comissao_afiliado       = @$data["comissao_afiliado"];
        $venda->meio_pagamento          = @$data["meio_pagamento"];
        $venda->forma_pagamento         = @$data["forma_pagamento"];
        $venda->status_transacao        = @$data["status_transacao"];
        $venda->url_checkout            = @$data["url_checkout"];
        $venda->codigo_checkout         = @$data["codigo_checkout"];
        $venda->link_boleto             = @$data["link_boleto"];

        if (@$data["linha_digitavel"])
            $venda->linha_digitavel         = @$data["linha_digitavel"];

        $venda->cupom                   = @$data["cupom"];

        $venda->valor_bruto             = @$data["valor_bruto"];
        $venda->valor_produto           = @$data["valor_produto"];
        $venda->valor_juros             = @$data["valor_juros"];
        $venda->taxa_juros              = @$data["taxa_juros"];
        $venda->assumir_juros           = @$data["assumir_juros"];
        $venda->valor_frete             = @$data["valor_frete"];
        $venda->taxa_redrocket            = @$data["taxa_redrocket"];
        $venda->valor_redrocket           = @$data["valor_redrocket"];
        $venda->valor_desconto          = @$data["valor_desconto"];
        $venda->valor_liquido           = @$data["valor_liquido"];
        $venda->parcelas                = @$data["parcela"];
        $venda->data_vencimento         = @$data["vencimento"];
        $venda->data_pedido             = date("Y-m-d");
        $venda->data_finalizada         = @$data["data_finalizada"];
        $venda->data_ultimo_evento      = @$data["data_ultimo_evento"];
        $venda->src                     = @$data["src"];
        $venda->utm_source              = @$data["utm_source"];
        $venda->utm_campaign            = @$data["utm_campaign"];
        $venda->utm_medium              = @$data["utm_medium"];
        $venda->utm_content             = @$data["utm_content"];
        $venda->st4rt                   = @$data["st4rt"];
        $venda->ip                      = @$_SERVER['REMOTE_ADDR'];
        $venda->cookie                  = ! empty( @$data["cookie"] ) ? @$data["cookie"] : 0;
        $venda->endereco                = @$data["endereco"];
        $venda->ecommerce               = @$data["ecommerce"];

        if (@$data["carrinho_abandonado"])
            $venda->carrinho_abandonado     = @$data["carrinho_abandonado"];

        $venda->coprodutores            = @$data["coprodutores"];
        $venda->comissao_coprodutores   = @$data["comissao_coprodutores"];
        //var_dump($venda);

        if ($venda->status_transacao == 5 && $venda->forma_pagamento == 1) {
            $venda->motivo = "SALDO INSUFICIENTE OU DADOS INCORRETOS - REDROCKET";
        } elseif (@$data["descricao"]) {
            $venda->motivo = @$data["descricao"];
        }

        if ($venda->save()) {
            $data["msg"] = "Venda salva com sucesso";
            $data["venda"] = $venda->id;

            // $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
            // $data["postback"] = $postback;
            $this->notificacao($data);
        } else {
            $data["msg"] =  $venda->fail()->getMessage();
        }

        $this->logs($data, "assinaturas-logs-venda");

        return $data;
    }

    public function atualizaVenda($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if (@$data["venda"])
            $venda = (new Venda())->findById($data["venda"]);
        else
            $venda = new Venda();

        $venda->transacao               = @$data["transacao"];

        $venda->comprador               = @$data["comprador"];
        $venda->meio_pagamento          = @$data["meio_pagamento"];
        $venda->forma_pagamento         = @$data["forma_pagamento"];
        $venda->status_transacao        = @$data["status_transacao"];
        $venda->url_checkout            = @$data["url_checkout"];
        $venda->link_boleto             = @$data["link_boleto"];

        if (@$data["linha_digitavel"])
            $venda->linha_digitavel         = @$data["linha_digitavel"];

        $venda->valor_bruto             = @$data["valor_bruto"];
        $venda->valor_produto           = @$data["valor_produto"];
        $venda->valor_juros             = @$data["valor_juros"];
        $venda->taxa_juros              = @$data["taxa_juros"];
        $venda->assumir_juros           = @$data["assumir_juros"];
        $venda->valor_frete             = @$data["valor_frete"];
        $venda->taxa_redrocket            = @$data["taxa_redrocket"];
        $venda->valor_redrocket           = @$data["valor_redrocket"];
        $venda->valor_desconto          = @$data["valor_desconto"];
        $venda->valor_liquido           = @$data["valor_liquido"];
        $venda->parcelas                = @$data["parcela"];
        $venda->data_vencimento         = @$data["vencimento"];
        $venda->data_pedido             = date("Y-m-d");
        $venda->data_finalizada         = @$data["data_finalizada"];
        $venda->data_ultimo_evento      = @$data["data_ultimo_evento"];
        $venda->ip                      = @$_SERVER['REMOTE_ADDR'];
        $venda->endereco                = @$data["endereco"];

        //var_dump($venda);

        if ($venda->status_transacao == 5 && $venda->forma_pagamento == 1 && !@$data["descricao"]) {
            $venda->motivo = "SALDO INSUFICIENTE OU DADOS INCORRETOS - REDROCKET";
        } elseif (@$data["descricao"]) {
            $venda->motivo = @$data["descricao"];
        }

        if ($venda->save()) {
            $data["msg"] = "Venda salva com sucesso";
            $data["venda"] = $venda->id;
        } else {
            $data["msg"] =  $venda->fail()->getMessage();
        }

        $this->logs($data, "assinaturas-logs-venda");

        return $data;
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
}

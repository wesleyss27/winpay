<?php

namespace Source\App\checkoutTransparente;

use League\Plates\Engine;
use Source\App\CheckoutTransparente\Cielo;
use Source\Models\CartaoCheckoutTransparente\Adquirente;

use Source\Models\Checkout\Checkout;
use Source\Models\Cookies\Cookies;
use Source\Models\Upsell\Upsell;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Vendas\Venda;
use Source\Models\Notificacoes\Notificacao;
use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;

use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Configuracoes\Taxa;
use Source\Models\Configuracoes\ConfiguracaoCheckout;

use Source\Models\Cartao\Cartao;
use Source\Models\Cartao\CartaoNumero;

use Source\Helpers\Emails;
use Source\App\CheckoutTransparente\PagarMe;

use Source\App\Checkout1\Bradesco;
use Source\App\Checkout1\Validacao;
use Source\App\Usuarios\AppUsuarios;
use Source\App\Postback\AppPostback;
use Source\App\Processos\AppProcessos;
use Source\App\Assinaturas\AppAssinaturas;
use Source\App\CheckoutTransparente\Pagseguro;
use Source\App\CheckoutTransparente\Wirecard as CheckoutTransparenteWirecard;
use Source\App\CheckoutTransparente\MercadoPago;
use Source\App\CheckoutTransparente\PagarMe as CheckoutTransparentePagarMe;
use Source\Models\Cookies\cookiesTelevendas;

session_start();

class AppTransacao
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
        $this->validacao = new Validacao();

        // $this->sessao = (new AppSessao())->verificaSeLogado();
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

    public function transacao($data)
    {
        // echo "<pre>";
        // var_dump(@$data);
        // exit;
        if (@$data["endereco_obrigatorio"] == 0) {
            $produtor = (new UsuarioDados())->find("usuario = " . @$data["produtor"])->fetch();
            $data["cep"] = @$produtor->cep;
            $data["rua"] = @$produtor->rua;
            $data["numero"] = @$produtor->numero;
            $data["complemento"] = "";
            $data["bairro"] = @$produtor->bairro;
            $data["cidade"] = @$produtor->cidade;
            $data["estado"] = @$produtor->estado;
        }

        if (@$data["pagamento"] == "credit_card")
            $adquirente = @(new Adquirente())->findById(@$data["adquirente"]);
        elseif (@$data["pagamento"] == "pix")
            $adquirente = @(new Adquirente())->findById(@$data["adquirente_pix"]);
        else
            $adquirente = @(new Adquirente())->findById(@$data["adquirente_boleto"]);

        // var_dump($adquirente->adquirente);
        // exit;
        if (@$adquirente) {
            $data["adquirente"] = @$adquirente->id;
            if ($data["email"] == "") {
                $data["email"] = $data["email_suporte"];
            }

            $data["email"] = str_replace(" ", "", $data["email"]);

            /** 
             * VALIDAR O CPF
             */
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
            /** 
             * LIMPAR CARACTERES DO TELEFONE
             */
            $telefone = (new Validacao())->limpaStr($data["telefone"]);
            $data["ddd"] = substr($telefone, 0, 2);
            $data["telefone"] = substr($telefone, 2, 12);
            
            $data["pais"] = "BR";
            $this->logs($data, "checkout-transparente/dados-enviados");

            /** 
             * VERIFICA SE CLIENTE TEM CADASTRO
             */
            $cadastrado = (new UsuarioDados())->verificaCpf(["cpf" => $data["cpf"]]);

            // if ($cadastrado) {
            //     $us = (new UsuarioDados())->find("cpf_cnpj = " . $data["cpf"])->fetch();
            //     $data["id_comprador"] = $us->usuario;
            //     $data["comprador"] = $us->id;
            //     $data["nasc"] = @$us->data_nascimento ? $us->data_nascimento : $data["nasc"];
            // } else { // SE NÃO TIVER CADASTRO
            //     $comprador = (new AppUsuarios(null))->cadastroUsuarioDados($data);
            //     $data["comprador"] = $comprador["id"];
            //     $data["id_comprador"] = $comprador["id_comprador"];
            // }

            if ($cadastrado) {
                $data["comprador"] = $cadastrado;
                $user = (new Usuario())->find("usuario_dados = $cadastrado")->fetch();
                $data["usuario"] = @$user->id;
                $usuarioDados = (new UsuarioDados())->findById($cadastrado);
                $usuarioDados->data_nascimento = $data["nasc"];
                $usuarioDados->save();
                /** 
                 * SE NÃO TIVER CADASTRO
                 */
            } else {
                $comprador = (new AppUsuarios(null))->cadastroUsuarioDados($data);

                /** 
                 * SE NÃO SALVAR OS DADOS
                 */
                if ($comprador["msg"] != "Salvo com sucesso") {
                    @$dataEmail["erros"]          .= "Houve um erro ao efetuar a transação. Por favor, entre em contato com o suporte. et: 2 \n";
                    $dataEmail["assunto"] = "Erro no cadastro de usuário em vendas";
                    $dataEmail["mensagem"]  = "Erro no cadastro de usuário em vendas <br>";
                    $dataEmail["mensagem"] .= "Protocolo: " . @$data["protocolo"] . " <br>";
                    $dataEmail["mensagem"] .= $comprador['msg'] . "<br>";
                    $dataEmail["mensagem"] .= "Dados enviados <br><br>";

                    foreach ($data as $key => $value) {
                        if (gettype($value) == "string") {
                            $dataEmail["mensagem"] .= "$key: $value<br>";
                        }
                    }

                    $dataEmail["email"] = "dev.ti@redrocketpay.com";
                    $enviaEmail = (new Emails())->enviaSendGrid($dataEmail);

                    /** 
                     * SE SALVAR O CLIENTE
                     */
                } else {
                    $data["comprador"] = $comprador["id"];
                    $user = (new Usuario())->find("usuario_dados = " . $comprador["id"])->fetch();
                    $data["usuario"] = @$user->id;
                }
            }

            /** 
             * CADASTRAR UM ENDEREÇO
             */
            $endereco = (new AppUsuarios(null))->cadastroEndereco($data);
            $data["endereco"] = $endereco;

            $data["data_ultimo_evento"] = date("Y-m-d H:i:s");
            $data["status_transacao"] = 1;

            if (@$data["meio_pagamento_desconto"]) {
                $meio_pagamento_desconto = explode(",", $data["meio_pagamento_desconto"]);
                foreach ($meio_pagamento_desconto as $pagamento) {
                    $data["pagamento1"] = $pagamento;
                    if ($pagamento == 1) {
                        $data["cartao"] = true;
                    } elseif ($pagamento == 2) {
                        $data["boleto"] = true;
                    } elseif ($pagamento == 3) {
                        $data["pix"] == true;
                    } elseif ($pagamento == 10) {
                        $data["cartao"] = true;
                        $data["boleto"] = true;
                        $data["pix"] = true;
                    }
                }
            }

            // Encontra preço do order_bump selecionado      
            if (@$data["codigo_order_bump"]) {
                $planos_orderBump = "";
                $cod_preco_ordBump = "";
                $total_ord_bump = 0;
                $cont_planos = @count($data["codigo_order_bump"]);
                $i = 1;

                foreach ($data["codigo_order_bump"] as $cod_plano => $valor_plano_orderBump) {
                    // Concatena codigo dos order_bump
                    if ($i < $cont_planos) {
                        $planos_orderBump .= "$cod_plano,";
                        $cod_preco_ordBump .= "$cod_plano|$valor_plano_orderBump,";
                    } else {
                        $planos_orderBump .= "$cod_plano";
                        $cod_preco_ordBump .= "$cod_plano|$valor_plano_orderBump";
                    }
                    $i++;

                    // Soma o preco total dos order_bump
                    $total_ord_bump += $valor_plano_orderBump;
                }
                $data["planos_orderBump"] = $planos_orderBump;
                $data["cod_preco_ordBump"] = $cod_preco_ordBump;
                $data["total_ord_bump"] = $total_ord_bump;
                // var_dump($planos_orderBump);
                // var_dump($cod_preco_OrdBump);
                // var_dump("total orderbump",$total_ord_bump);
            } else {
                $total_ord_bump = 0;
            }

            /****** TAXA REDROCKET PARA DROPSHIPPING ******/
            $taxa_usuario = (new Usuario())->findById(@$data["produtor"]);
            $taxa_padrao = (new Taxa())->findById(1);
            $data["taxa_redrocket"] = @$taxa_usuario->taxa_drop != "" ? @$taxa_usuario->taxa_drop : @$taxa_padrao->taxa_drop;
            /****** TAXA REDROCKET PARA DROPSHIPPING ******/

            //// AJUSTA O PRECO DE ACORDO COM A FORMA DE PAGAMENTO
            if ($data["pagamento"] == "credit_card") {
                $taxa_adquirente = array(
                    "1" => @$adquirente->taxa1,
                    "2" => @$adquirente->taxa2,
                    "3" => @$adquirente->taxa3,
                    "4" => @$adquirente->taxa4,
                    "5" => @$adquirente->taxa5,
                    "6" => @$adquirente->taxa6,
                    "7" => @$adquirente->taxa7,
                    "8" => @$adquirente->taxa8,
                    "9" => @$adquirente->taxa9,
                    "10" => @$adquirente->taxa10,
                    "11" => @$adquirente->taxa11,
                    "12" => @$adquirente->taxa12
                );

                $data["forma_pagamento"] = 1;
                /** 
                 * CALCULAR O VALOR TOTAL
                 */
                if (@$data["cartao"] && @$data["valor_desconto"] > 0)
                    $valProd = $data["preco"] + @$data["frete"] - @$data["valor_desconto"] + @$total_ord_bump;
                else
                    $valProd = $data["preco"] + @$data["frete"] + @$total_ord_bump;

                // $preco = number_format($this->validacao->juros($data["parcela"], $data["taxa_juros"], $valProd), 2, ".", "");

                // $preco = (string) $preco * 100;

                if (@$data["plano"]) $plano = (new Plano())->findById($data["plano"]);

                if (@$data["parcela"]) {
                    $juros = @$taxa_adquirente[@$data["parcela"]];
                } else {
                    $juros = 0;
                }

                $preco = number_format($valProd + ($valProd * $juros / 100), 2, ".", ",");
                $data["valor_juros"] = number_format($valProd * $juros / 100, 2, ".", ",");
                $preco = str_replace(",", "", $preco);

                // echo "\n PREÇO: " . $preco . "\n";
                // echo "JUROS: " . $juros . "\n";
                // echo "valProd: " . $valProd . "\n";

                $data["preco"] = $preco;
                $data["valor_bruto"] = $preco;
                // $data["valor_bruto"] = ($preco / 100);
                $data["valor_frete"] = @$data["frete"];

                $desconto = @$data["valor_desconto"] ? $data["valor_desconto"] : 0;
                $data["valor_redrocket"] = number_format((($preco - @$desconto) * ($data["taxa_redrocket"] / 100)), 2, ".", "");
                $data["valor_liquido"] = $preco - $data["valor_redrocket"] - @$data["valor_frete"];

                //echo "Valor REDROCKET: " . $data["valor_redrocket"] . "\n";
                //echo "Preco: " . $preco . "\n";
                //echo "Desconto: " . $desconto . "\n";
                //echo "Taxa REDROCKET: " . $data["taxa_redrocket"] . "\n";
                // exit;

                // echo (var_dump($data["preco"]));
                // echo (var_dump($data["valor_bruto"]));
                // echo (var_dump($data["valor_juros"]));
                // echo (var_dump($data["valor_liquido"]));
                // echo (var_dump($total_ord_bump));
                // exit;
                // $cookies = (new Cookies())->find("produto = {$data["produto"]} AND ip = '$ip'")->fetch();
                // if (@$cookies->afiliado) {
                //     if ($cookies->tipo_comissao == 0) {
                //         $comissao = $data["valor_liquido"] * ($cookies->comissao / 100);
                //     } else if ($cookies->tipo_comissao == 1) {
                //         $comissao = $cookies->comissao;
                //     }
                //     $data["comissao_afiliado"] = number_format($comissao, 2);
                // }
            } else if ($data["pagamento"] == "boleto") {

                $data["forma_pagamento"] = 2;

                if (@$data["boleto"] && @$data["valor_desconto"] > 0)
                    $val =  ($data["preco"] + @$data["frete"]) - $data["valor_desconto"] +  @$total_ord_bump;
                else
                    $val =  $data["preco"] + @$data["frete"] +  @$total_ord_bump;

                $preco = number_format($val, 2, ".", ",");
                $preco = str_replace(",", "", $preco);

                $preco_prod = $data["preco"];
                $data["preco"] = $preco;
                // $preco = explode(".", $val);
                // $data["preco"] = $val * 100;

                $data["valor_bruto"] = $data["preco"];
                $data["valor_juros"] = 0.00;
                $data["valor_frete"] = @$data["frete"];
                $data["taxa_juros"] = 0;

                // $valProd = $preco_prod + @$data["frete"];
                $desconto = @$data["valor_desconto"] ? @$data["valor_desconto"] : 0;
                $data["valor_redrocket"] = number_format(((@$preco_prod - @$desconto +  @$total_ord_bump) * ($data["taxa_redrocket"] / 100)), 2, ".", "");
                $data["valor_liquido"] = @$preco_prod - $data["valor_redrocket"] - @$data["valor_frete"] +  @$total_ord_bump;

                $data["parcela"] = 1;

                if (@$data["vencimento"] == "") {
                    if (date("D") != "Sun" || date("D") != "Mon" || date("D") != "Tue") {
                        $data["vencimento"] = date("Y-m-d", strtotime("+5 days"));
                    } else if (date("D") == "Sat") {
                        $data["vencimento"] = date("Y-m-d", strtotime("+4 days"));
                    } else {
                        $data["vencimento"] = date("Y-m-d", strtotime("+3 days"));
                    }
                } else {
                    if (date("D", strtotime($data["vencimento"])) == "Sat") {
                        $data["vencimento"] = date("Y-m-d", strtotime("+2 days", strtotime($data["vencimento"])));
                    } else if (date("D", strtotime($data["vencimento"])) == "Sun") {
                        $data["vencimento"] = date("Y-m-d", strtotime("+1 day", strtotime($data["vencimento"])));
                    }
                }
            } else if (@$data["pagamento"] == "pix") {
                $data["forma_pagamento"] = 3;

                if (@$data["pix"] && @$data["valor_desconto"] > 0)
                    $val =  (@$data["preco"] + @$data["frete"]) - @$data["valor_desconto"] +  @$total_ord_bump;
                else
                    $val =  @$data["preco"] + @$data["frete"] +  @$total_ord_bump;

                $preco = number_format($val, 2, ".", ",");
                $preco = str_replace(",", "", $preco);

                $preco_prod = $data["preco"];
                $data["preco"] = $preco;

                $data["valor_bruto"] = $data["preco"];
                $data["valor_juros"] = 0.00;
                $data["valor_frete"] = @$data["frete"];
                $data["taxa_juros"] = 0;
                // echo "Preco: " . $preco;
                // echo "Order Bump: " . $total_ord_bump;
                // echo "Taxa REDROCKET: " . $data["taxa_redrocket"];
                // exit;
                // $valProd = $preco_prod + @$data["frete"];
                $desconto = @$data["valor_desconto"] ? @$data["valor_desconto"] : 0;
                $data["valor_redrocket"] = number_format(((@$preco_prod - @$desconto +  @$total_ord_bump) * ($data["taxa_redrocket"] / 100)), 2, ".", "");
                $data["valor_liquido"] = @$preco_prod - $data["valor_redrocket"] - @$data["valor_frete"] +  @$total_ord_bump;
                // echo "valor liquido: " . $data["valor_liquido"];
                // echo "valor redrocket: " . $data["valor_redrocket"];
                // exit;
                $data["parcela"] = 1;
                $data["data_ultimo_evento"] = date("Y-m-d H:i:s");

                $data["status_transacao"] = 1;
            }

            $ip = (string) $_SERVER['REMOTE_ADDR'];

            // echo "CHEGOU ADQUIRENTE: " . @$adquirente->adquirente;
            // exit;
            if (@$adquirente->adquirente == "wirecard") {
                $this->adquirenteWirecard(@$data);
            } else if (@$adquirente->adquirente == "cielo") {
                $this->adquirenteCielo($data);
            } else if (@$adquirente->adquirente == "pagarme") {
                $this->adquirentePagarme($data);
            } else if (@$adquirente->adquirente == "pagseguro") {
                // var_dump(@$adquirente);
                $this->adquirentePagseguro($data);
            } else if (@$adquirente->adquirente == "mercadopago") {
                $this->adquirenteMercadopago($data);
            }
        } else { // SE NÃO TIVER ADQUIRENTE
            #
        }
    }

    public function logsCielo($data)
    {
        $arquivo = fopen(DIRETORIO . "logs/checkout-transparente/cielo-transacao.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        $texto .= $data["json"];
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
    }

    public function cadastrarCartao($data)
    {
        $cartao = new Cartao();

        $cartao->usuario            = $_SESSION["id"];
        $cartao->status             = 1;
        $cartao->nome               = @$data["nome"];
        $cartao->email              = @$data["email"];
        $cartao->telefone           = @$data["telefone"];
        $cartao->tipo_documento     = @$data["tipo_documento"];
        $cartao->cep                = @$data["cep"];
        $cartao->rua                = @$data["rua"];
        $cartao->numero             = @$data["numero"];
        $cartao->complemento        = @$data["complemento"];
        $cartao->bairro             = @$data["bairro"];
        $cartao->cidade             = @$data["cidade"];
        $cartao->estado             = @$data["estado"];
        $cartao->pais               = @$data["pais"];
        $cartao->numero_cartao      = base64_encode(@$data["numero_cartao"]);
        $cartao->ultimos_digitos    = substr(@$data["numero_cartao"], -4);
        $cartao->titular            = @$data["titular"];
        $cartao->mes                = base64_encode(@$data["mes"]);
        $cartao->ano                = base64_encode(@$data["ano"]);
        $cartao->cvv                = base64_encode(@$data["cvv"]);
        $cartao->bandeira           = @$data["bandeira"];

        $cielo = new Cielo();
        $data["preco"] = 30;
        $transacao = $cielo->compraCartao($data);
        $data["json"] = serialize($transacao);
        $this->logs($data, "checkout-transparente/cielo");
        // var_dump($transacao);
        if ($transacao->Payment->Status == 2) {
            $cartao->status = 2;
            $data["pay_id"] = $transacao->Payment->PaymentId;
            $estorno = $cielo->reembolsoCartao($data);
            // var_dump($estorno);
        } elseif ($transacao->Payment->Status == 3) {
            $cartao->status = 3;
        }

        if ($cartao->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $cartao->fail()->getMessage();
        }
    }

    public function excluirCartao($data)
    {
        $cartao = (new Cartao())->findById($data["id"]);

        if ($cartao->destroy()) {
            echo "Excluído com sucesso";
        } else {
            echo $cartao->fail()->getMessage();
        }
    }

    public function cadastrarAdquirente($data)
    {
        if (@$data["id_adquirente"])
            $adquirente = (new Adquirente())->findById($data["id_adquirente"]);
        else
            $adquirente = new Adquirente();

        $adquirente->usuario            = $_SESSION["id"];
        $adquirente->status             = 1;
        $adquirente->adquirente         = @$data["adquirente"];
        $adquirente->chave              = @$data["chave"];
        $adquirente->token              = @$data["token"];
        $adquirente->taxa1              = @$data["taxa1"];
        $adquirente->taxa2              = @$data["taxa2"];
        $adquirente->taxa3              = @$data["taxa3"];
        $adquirente->taxa4              = @$data["taxa4"];
        $adquirente->taxa5              = @$data["taxa5"];
        $adquirente->taxa6              = @$data["taxa6"];
        $adquirente->taxa7              = @$data["taxa7"];
        $adquirente->taxa8              = @$data["taxa8"];
        $adquirente->taxa9              = @$data["taxa9"];
        $adquirente->taxa10             = @$data["taxa10"];
        $adquirente->taxa11             = @$data["taxa11"];
        $adquirente->taxa12             = @$data["taxa12"];

        if ($adquirente->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $adquirente->fail()->getMessage();
        }
    }

    public function excluirAdquirente($data)
    {
        $adquirentes = (new Adquirente())->find("adquirente = '{$data["id"]}'")->fetch(true);

        // var_dump($adquirentes, $data);
        // exit;
        if ($adquirentes) {
            foreach ($adquirentes as $adquirente) {
                if ($adquirente->destroy()) {
                    echo "Excluído com sucesso";
                } else {
                    echo $adquirente->fail()->getMessage();
                }
            }
        }
    }

    public function adquirenteMercadopago($data)
    {
        $mercadopago = new MercadoPago();

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


        if (@$data["pagamento"] == "credit_card") {
            $transacao = $mercadopago->compraCartao(@$data);
            $data["transacao"] = @$transacao->id;
            $data["status_transacao"] = @$status_transacao[@$transacao->status];
            $data["nit_pix"] = @$transacao->id;
        } else if (@$data["pagamento"] == "boleto") {
            $transacao = $mercadopago->compraBoleto(@$data);
            $data["nit_pix"] = @$transacao->id;
            $data["link_boleto"] = @$transacao->transaction_details->external_resource_url;
            $data["linha_digitavel"] = @$transacao->barcode->content;
            $data["transacao"] = @$transacao->id;
            $data["status_transacao"] = @$status_transacao[@$transacao->status];
        } else if (@$data["pagamento"] == "pix") {
            $transacao = $mercadopago->compraPix(@$data);
            $data["qr_code"] = $transacao->point_of_interaction->transaction_data->qr_code;
            $data["transacao"] = $transacao->id;
            $data["status_transacao"] = @$status_transacao[@$transacao->status];
            $data["nit_pix"] = @$transacao->id;
        }

        // $data["resposta"] = $transacao;

        // var_dump($transacao);
        // echo "transacao: {$data["transacao"]}";
        // echo "status_transacao {$data["status_transacao"]}";
        // echo "qr_code: " . @$data["qr_code"];
        // echo $data["nit_pix"];
        // exit;

        // REGISTAR LOG MERCADOPAGO
        $arr["transacao"] = @$data["transacao"];
        $arr["object"] = @$transacao->status_detail;
        $arr["status"] = @$transacao->status;
        $this->logs($arr, "checkout-transparente/mercadopago");
        // FIM REGISTRO LOG MERCADOPAGO

        if (@$transacao->id) {
            $data["nit_pix"] = @$transacao->id;
            $data["meio_pagamento"] = "MercadoPago";

            if ($data["status_transacao"] == 4)
                $data["data_finalizada"] = date("Y-m-d H:i:s");

            if ($data["status_transacao"] == 5)
                $data["descricao"] = $msg_transacao[@$transacao->status_detail];
        } else {
            $data["status_transacao"] = 5;
            // echo "Erro ao realizar transação com o Mercado Pago. Message: " . @$msg_transacao[$transacao->message] . "<br> Status: " . @$transacao->status;
            if (@$transacao->message == "The customer can't be equal to the collector.") {
                $data["descricao"] = 'Os dados do comprador não podem ser iguais ao do vendedor.';
                // echo json_encode($data);
                // exit;
            } else {
                $data["descricao"] = "Erro ao realizar transação com o Mercado Pago. Message: " . @$msg_transacao["transacao->message"] . " Status: " . @$transacao->status;
                // echo json_encode($data);
                // exit;
            }
            // exit;
            // echo ""
        }

        if (@$data["venda"]) {
            $atualizaVenda = $this->atualizaVenda($data);
        } else {
            $cadastroVenda = $this->cadastroVenda($data);
            $data["msg"] = $cadastroVenda["msg"];
            $data["venda"] = $cadastroVenda["venda"];

            $produto = (new Produto())->findById($data["produto"]);
            if ($produto->tipo_cobranca == 2) {
                $plano = (new Plano())->findById($data["plano"]);

                $periodicidade = array(
                    "Semanal"       => [7, "days"],
                    "Mensal"        => [1, "months"],
                    "Bimestral"     => [2, "months"],
                    "Trimestral"    => [3, "months"],
                    "Semestral"     => [6, "months"],
                    "Anual"         => [1, "years"],
                    "Trienal"       => [3, "years"]
                );

                $data["recorrencia"] = $plano->periodo;
                $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];

                if ($plano->maximo_recorrencia > 0)
                    $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                $proxima = @$periodicidade[$plano->periodo][0];
                $data["proxima_cobranca"] = date('Y-m-d', strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));
                $data["valor_plano"] = $plano->preco;
                $data["assinatura"] = (new AppAssinaturas(null))->cadastroAssinaturas($data);
            }
        }
        $_SESSION["pagamento"] = $data;
        (new AppProcessos(null))->processos($data);
        echo json_encode($data);
    }

    public function adquirentePagseguro($data)
    {
        $pagseguro = new Pagseguro();

        $data["preco"] = @$data["preco"] * 100;
        $status_transacao = array(
            "WAITING" => 1,
            "AUTHORIZED" => 3,
            "PAID" => 4,
            "DECLINED" => 5,
            "CANCELED" => 5
        );

        if (@$data["pagamento"] == "credit_card") {
            $transacao = $pagseguro->compraCartao(@$data);
            $data["transacao"] = @$transacao->charges[0]->id;
            $data["status_transacao"] = @$status_transacao[@$transacao->charges[0]->status];
            $log_obj = @$transacao->charges[0]->payment_response->message;
            $log_status = @$transacao->charges[0]->status;
        } else if (@$data["pagamento"] == "boleto") {
            $transacao = $pagseguro->compraBoleto(@$data);
            $data["transacao"] = @$transacao->payment_method->boleto->id;
            // $data["nit_pix"] = @$transacao->charges[0]->id;
            $data["link_boleto"] = @$transacao->links[0]->href;
            $data["linha_digitavel"] = @$transacao->payment_method->boleto->barcode;
            $data["status_transacao"] = @$status_transacao[@$transacao->status];

            $log_obj = @$transacao->payment_response->message;
            $log_status = @$transacao->status;
        } else if (@$data["pagamento"] == "pix") {
            $transacao = $pagseguro->compraPix(@$data);
            $data["transacao"] = @$transacao->qr_codes[0]->id;
            @$data["qr_code"] = $transacao->qr_codes[0]->text;

            if ($transacao->id)
                $data["status_transacao"] = 1;
            else
                $data["status_transacao"] = 5;

            $log_obj = @$transacao->qr_codes[0]->text;
            $log_status = $data["status_transacao"];
        }

        // REGISTAR LOG PAGSEGURO
        $arr["transacao"] = @$data["transacao"];
        $arr["object"] = @$log_obj;
        $arr["status"] = @$log_status;
        $this->logs($arr, "checkout-transparente/pagseguro");
        // FIM REGISTRO LOG PAGSEGURO

        if (@$transacao->id) {
            // $data["transacao"] = @$transacao->id;
            $data["nit_pix"] = @$transacao->id;
            $data["meio_pagamento"] = "PagSeguro";

            if ($data["status_transacao"] == 4)
                $data["data_finalizada"] = date("Y-m-d H:i:s");

            if ($data["status_transacao"] == 5 && @$data["pagamento"] == "boleto")
                $data["descricao"] = @$transacao->payment_response->message;

            if ($data["status_transacao"] == 5 && @$data["pagamento"] == "credit_card")
                $data["descricao"] = @$transacao->charges[0]->payment_response->message;

            if ($data["status_transacao"] == 5 && @$data["pagamento"] == "pix")
                $data["descricao"] = "Erro ao gerar Pix";
        } else {
            // echo "Erro ao realizar transação com a adquirente";
            // echo ""
        }

        if (@$data["venda"]) {
            $atualizaVenda = $this->atualizaVenda($data);
        } else {
            $cadastroVenda = $this->cadastroVenda($data);
            $data["msg"] = $cadastroVenda["msg"];
            $data["venda"] = $cadastroVenda["venda"];

            $produto = (new Produto())->findById($data["produto"]);
            if ($produto->tipo_cobranca == 2) {
                $plano = (new Plano())->findById($data["plano"]);

                $periodicidade = array(
                    "Semanal"       => [7, "days"],
                    "Mensal"        => [1, "months"],
                    "Bimestral"     => [2, "months"],
                    "Trimestral"    => [3, "months"],
                    "Semestral"     => [6, "months"],
                    "Anual"         => [1, "years"],
                    "Trienal"       => [3, "years"]
                );

                $data["recorrencia"] = $plano->periodo;
                $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];

                if ($plano->maximo_recorrencia > 0)
                    $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                $proxima = @$periodicidade[$plano->periodo][0];
                $data["proxima_cobranca"] = date('Y-m-d', strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));
                $data["valor_plano"] = $plano->preco;
                $data["assinatura"] = (new AppAssinaturas(null))->cadastroAssinaturas($data);
            }
        }
        $_SESSION["pagamento"] = $data;
        (new AppProcessos(null))->processos($data);
        echo json_encode($data);
    }

    public function adquirentePagarme($data)
    {
        $pagarme = new Pagarme();
        $data["preco"] = @$data["preco"] * 100;

        $status_transacaoP = array(
            "pending" => 1,
            "paid" => 4,
            "canceled" => 5,
            "failed" => 5,
            "refunded" => 6
        );

        if (@$data["pagamento"] == "credit_card") {
            $transacao = $pagarme->compraCartao(@$data);
        } else if (@$data["pagamento"] == "boleto") {
            $transacao = $pagarme->compraBoleto(@$data);
            $data["link_boleto"] = @$transacao->charges[0]->last_transaction->pdf;
            $data["linha_digitavel"] = @$transacao->charges[0]->last_transaction->line;
        } else if (@$data["pagamento"] == "pix") {
            $transacao = $pagarme->compraPix(@$data);
            $data["qr_code"] = @$transacao->charges[0]->last_transaction->qr_code;
        }

        // var_dump($transacao);
        // echo "Transacao: " . @$data["transacao"] . "\n";
        // echo "Status Transacao: " . @$data["status_transacao"] . "\n";
        // echo "Nit_pix: " . @$data["nit_pix"] . "\n";
        // echo "Qr Code: " . @$data["qr_code"] . "\n";
        // echo "Link Boleto: " . @$data["link_boleto"] . "\n";
        // echo "Gateway Response: " . @$transacao->charges[0]->last_transaction->gateway_response->errors[0]->message; "\n";

        // exit;
        // REGISTAR LOG PAGARME
        $arr["transacao"] = @$transacao->charges[0]->id;
        $arr["object"] = @$transacao->charges[0]->id;
        $arr["status"] = @$transacao->charges[0]->status;
        $this->logs($arr, "checkout-transparente/pagarme");
        // FIM REGISTRO LOG PAGARME
        if (@$transacao->charges[0]->id) {
            $data["transacao"] = @$transacao->charges[0]->id;
            $data["status_transacao"] = $status_transacaoP[@$transacao->charges[0]->status];
            $data["nit_pix"] = @$transacao->charges[0]->id;
            $data["meio_pagamento"] = "PagarMe";

            if ($data["status_transacao"] == 4)
                $data["data_finalizada"] = date("Y-m-d H:i:s");

            if ($data["status_transacao"] == 5)
                $data["descricao"] = @$transacao->charges[0]->last_transaction->gateway_response->errors[0]->message;
        }

        if (@$data["venda"]) {
            $atualizaVenda = $this->atualizaVenda($data);
        } else {
            $cadastroVenda = $this->cadastroVenda($data);
            $data["msg"] = $cadastroVenda["msg"];
            $data["venda"] = $cadastroVenda["venda"];

            $produto = (new Produto())->findById($data["produto"]);
            if ($produto->tipo_cobranca == 2) {
                $plano = (new Plano())->findById($data["plano"]);

                $periodicidade = array(
                    "Semanal"       => [7, "days"],
                    "Mensal"        => [1, "months"],
                    "Bimestral"     => [2, "months"],
                    "Trimestral"    => [3, "months"],
                    "Semestral"     => [6, "months"],
                    "Anual"         => [1, "years"],
                    "Trienal"       => [3, "years"]
                );

                $data["recorrencia"] = $plano->periodo;
                $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];

                if ($plano->maximo_recorrencia > 0)
                    $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                $proxima = @$periodicidade[$plano->periodo][0];
                $data["proxima_cobranca"] = date('Y-m-d', strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));
                $data["valor_plano"] = $plano->preco;
                $data["assinatura"] = (new AppAssinaturas(null))->cadastroAssinaturas($data);
            }
        }

        $_SESSION["pagamento"] = $data;
        (new AppProcessos(null))->processos($data);
        echo json_encode($data);
    }

    public function adquirenteCielo($data)
    {
        $cielo = new Cielo();
        $data["preco"] = @$data["preco"] * 100;

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


        if (@$data["pagamento"] == "credit_card") {
            $transacao = $cielo->compraCartao(@$data);
            $data["transacao"] = @$transacao->Payment->Tid;
        } else if (@$data["pagamento"] == "boleto") {
            $transacao = $cielo->compraBoleto(@$data);
            $data["link_boleto"] = @$transacao->Payment->Url;
            $data["linha_digitavel"] = @$transacao->Payment->DigitableLine;
            $data["transacao"] = @$transacao->MerchantOrderId;
        } else {
            // $transacao = $cielo->compraPix(@$data);
            echo "Forma de pagamento não disponível no momento. Por favor, escolha outro meio de pagamento.";
            exit;
        }


        $data["nit_pix"] = @$transacao->Payment->PaymentId;
        $data["status_transacao"] = @$status_transacaoC[@$transacao->Payment->Status];
        $data["meio_pagamento"] = "Cielo";

        if ($data["status_transacao"] == 4)
            $data["data_finalizada"] = date("Y-m-d H:i:s");

        if ($data["status_transacao"] == 5)
            $data["descricao"] = $transacao->Payment->ReturnMessage;

        if (@$data["venda"]) {
            $atualizaVenda = $this->atualizaVenda($data);
        } else {
            $cadastroVenda = $this->cadastroVenda($data);
            $data["msg"] = $cadastroVenda["msg"];
            $data["venda"] = $cadastroVenda["venda"];

            $produto = (new Produto())->findById($data["produto"]);
            if ($produto->tipo_cobranca == 2) {
                $plano = (new Plano())->findById($data["plano"]);

                $periodicidade = array(
                    "Semanal"       => [7, "days"],
                    "Mensal"        => [1, "months"],
                    "Bimestral"     => [2, "months"],
                    "Trimestral"    => [3, "months"],
                    "Semestral"     => [6, "months"],
                    "Anual"         => [1, "years"],
                    "Trienal"       => [3, "years"]
                );

                $data["recorrencia"] = $plano->periodo;
                $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];

                if ($plano->maximo_recorrencia > 0)
                    $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                $proxima = @$periodicidade[$plano->periodo][0];
                $data["proxima_cobranca"] = date('Y-m-d', strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));
                $data["valor_plano"] = $plano->preco;
                $data["assinatura"] = (new AppAssinaturas(null))->cadastroAssinaturas($data);
            }
        }

        $_SESSION["pagamento"] = $data;
        (new AppProcessos(null))->processos($data);
        echo json_encode($data);
    }

    public function adquirenteWirecard($data)
    {
        $wirecard = new CheckoutTransparenteWirecard();
        $cliente = $wirecard->cliente(@$data);
        $data["preco"] = @$data["preco"] * 100;
        // var_dump($cliente);
        $data["customer_id"] = @$cliente->id;
        // echo $cliente->id;
        // exit;
        $status_transacao = array(
            "CREATED" => 1,
            "WAITING" => 1,
            "IN_ANALYSIS" => 2,
            "PRE_AUTHORIZED" => 3,
            "AUTHORIZED" => 4,
            "CANCELLED" => 5,
            "REFUNDED" => 6,
            "REVERSED" => 7,
            "SETTLED" => 8
        );

        //////// ERRO AO CADASTRAR CLIENTE NA WIRECARD //////////
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
            }

            $dataEmail["email"]          = "dev.ti@redrocketpay.com";
            $enviaEmail = (new Emails())->enviaSendGrid($dataEmail);
        }
        ///////// FIM ERRO AO CADASTRAR CLIENTE NA WIRECARD /////////
        ////// CADASTRA PEDIDO NA WIRECARD
        $pedido = $wirecard->pedido($data);

        $data["order_id"] = @$pedido->id;
        if (@$pedido->errors) {
            @$dataEmail["erros"]        .= "Houve um erro ao efetuar a transação. Por favor, entre em contato com o suporte. et: 3 \n";
            $dataEmail["assunto"]        = "Erro no cadastro de pedido wirecard";
            $dataEmail["mensagem"]       = "Erro no cadastro de pedido wirecard <br>";
            $dataEmail["mensagem"]      .= "Protocolo: " . $data["protocolo"] . " <br>";

            $dataEmail["mensagem"]      .= "Dados enviados: <br><br>";

            foreach ($data as $key => $value) {
                $dataEmail["mensagem"]  .= "$key: $value<br>";
            }

            foreach ($pedido->errors as $erros) {
                $dataEmail["mensagem"]  .= "Descrição: $erros->description<br>";
                $_SESSION["erros"]["pedido"][] = @$erros->code;
            }

            $dataEmail["email"]          = "dev.ti@redrocketpay.com";
            $enviaEmail = (new Emails())->enviaSendGrid($dataEmail);
        }
        ////////////// FIM CADASTRO PEDIDO NA WIRECARD ////////

        ///////// INICIO PAGAMENTO WIRECARD /////////////////
        if (@$data["pagamento"] == "credit_card") {
            // echo "COMPRA CARTÃOOOO";
            $transacao = $wirecard->compraCartao($data);
            if (!@$transacao->object || @$transacao->status == "refused") {
                $data["transacao"] = @$transacao->id;
                $data["status_transacao"] = @$status_transacao[@$transacao->status];
                $data["meio_pagamento"] = "Moip";
            }
        } else if ($data["pagamento"] == "boleto") {
            $transacao = $wirecard->compraBoleto($data);
            $data["transacao"] = @$transacao->id;
            $data["status_transacao"] = @$status_transacao[@$transacao->status];
            $data["meio_pagamento"] = "Moip";
            $data["link_boleto"] = @$transacao->_links->payBoleto->printHref;
            $data["linha_digitavel"] = @$transacao->fundingInstrument->boleto->lineCode;
        } else if (@$data["pagamento"] == "pix") {
            echo "Meio de pagamento não disponível no momento. Escolha outro";
            exit;
        }

        ///////////////// FIM PAGAMENTO WIRECARD //////////////////////

        // REGISTAR LOG WIRECARD
        $arr["transacao"] = @$transacao->id;
        $arr["object"] = @$transacao->object;
        $arr["status"] = @$transacao->status;
        $this->logs($arr, "checkout-transparente/wirecard");
        // FIM REGISTRO LOG WIRECARD

        if (@$data["venda"]) {
            $atualizaVenda = $this->atualizaVenda($data);
        } else {
            $cadastroVenda = $this->cadastroVenda($data);
            $data["msg"] = $cadastroVenda["msg"];
            $data["venda"] = $cadastroVenda["venda"];

            $produto = (new Produto())->findById($data["produto"]);
            if ($produto->tipo_cobranca == 2) {
                $plano = (new Plano())->findById($data["plano"]);

                $periodicidade = array(
                    "Semanal"       => [7, "days"],
                    "Mensal"        => [1, "months"],
                    "Bimestral"     => [2, "months"],
                    "Trimestral"    => [3, "months"],
                    "Semestral"     => [6, "months"],
                    "Anual"         => [1, "years"],
                    "Trienal"       => [3, "years"]
                );

                $data["recorrencia"] = $plano->periodo;
                $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];

                if ($plano->maximo_recorrencia > 0)
                    $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                $proxima = @$periodicidade[$plano->periodo][0];
                $data["proxima_cobranca"] = date('Y-m-d', strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));
                $data["valor_plano"] = $plano->preco;
                $data["assinatura"] = (new AppAssinaturas(null))->cadastroAssinaturas($data);
            }
        }

        $_SESSION["pagamento"] = $data;
        (new AppProcessos(null))->processos($data);
        echo json_encode($data);
    }


    public function cadastroVenda($data)
    {
        // echo "ENTROU CADASTRO VENDA";
        // var_dump(@$data);
        // exit;
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

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
            $venda->plano               = @$data["plano"];

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
        $venda->adquirente              = @$data["adquirente"];
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
        $venda->tipo_frete              = @$data["tipo_frete"];
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
        $venda->endereco                = @$data["endereco"];

        if (@$data["carrinho_abandonado"])
            $venda->carrinho_abandonado     = @$data["carrinho_abandonado"];

        $venda->coprodutores            = @$data["coprodutores"];
        $venda->comissao_coprodutores   = @$data["comissao_coprodutores"];
        //var_dump($venda);

        if ($venda->status_transacao == 5 && $venda->forma_pagamento == 1 && @!$data["descricao"]) {
            $venda->motivo = "SALDO INSUFICIENTE OU DADOS INCORRETOS - REDROCKET";
        } elseif (@$data["descricao"]) {
            $venda->motivo = @$data["descricao"];
        }

        if ($venda->save()) {
            $data["msg"] = "Venda salva com sucesso";
            $data["venda"] = $venda->id;

            if ($data["pagamento"] == "credit_card") {
                $cartao_numero = new CartaoNumero();
                $cartao_numero->comprador = @$data["comprador"];
                $cartao_numero->usuario = @$data["usuario"];
                $cartao_numero->venda = $venda->id;
                $cartao_numero->numero = base64_encode($data["numcart"]);
                $cartao_numero->ultimos_digitos = substr($data["numcart"], -4);
                $cartao_numero->save();

                $cartao = new Cartao();
                $cartao->comprador = @$data["comprador"];
                $cartao->usuario = @$data["usuario"];
                $cartao->venda = $venda->id;
                $cartao->cartao = $cartao_numero->id;
                $cartao->cvv = base64_encode($data["cvv"]);
                $cartao->mes = base64_encode($data["mes-val"]);
                $cartao->ano = base64_encode($data["ano-val"]);
                $cartao->ultimos_digitos = substr($data["numcart"], -4);
                $cartao->titular = $data["titular"];
                $cartao->cpf = base64_encode($data["cpf"]);
                $cartao->save();
            }

            $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
            $data["postback"] = $postback;
            $this->notificacao($data);
        } else {
            $data["msg"] =  $venda->fail()->getMessage();
        }

        $this->logs($data, "checkout-transparente/logs-venda");

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
        $venda->nit_pix                 = @$data["nit_pix"]; // GUARDA O NÚMERO NIT OU O PAYMENTID

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

        $this->logs($data, "checkout-transparente/logs-venda");

        return $data;
    }

    public function notificacao($data)
    {
        $content = http_build_query(array(
            "venda"                 => @$data["venda"],
            "forma_pagamento"       => @$data["forma_pagamento"],
            "status_transacao"      => @$data["status_transacao"],
            "produto"               => @$data["produto"],
            "produtor"              => @$data["produtor"],
            "comissao_produtor"     => @$data["comissao_produtor"],
            "coprodutores"          => @$data["coprodutores"],
            "comissao_coprodutores" => @$data["comissao_coprodutores"],
            "afiliado"              => @$data["afiliado"],
            "comissao_afiliado"     => @$data["comissao_afiliado"],
            "transacao"             => @$data["transacao"],
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

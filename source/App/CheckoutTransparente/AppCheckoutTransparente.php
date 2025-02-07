<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\checkoutTransparente;

use League\Plates\Engine;
use Source\App\Checkout1\Cielo;
use Source\App\Sessao\AppSessao;
use Source\App\CheckoutTransparente\Wirecard;
use Source\App\CheckoutTransparente\Cielo as CieloTransparente;
use Source\App\CheckoutTransparente\MercadoPago;
use Source\App\CheckoutTransparente\PagarMe;
use Source\App\CheckoutTransparente\Pagseguro;

use Source\Models\CartaoCheckoutTransparente\Cartao;
use Source\Models\CartaoCheckoutTransparente\Adquirente;
use Source\Models\Drop\Cobranca;
use Source\Models\Vendas\Venda;
use Source\Models\Usuarios\UsuarioDados;

use Source\Helpers\Paginador;
use Source\Models\Checkout\Checkout;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;
use Source\Models\Usuarios\Usuario;

session_start();

class AppCheckoutTransparente
{
    private $view;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->section = new Section();
    }

    public function checkoutTransparente($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("dropshipping");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'dropshipping'")->fetch();

        $usuario = (new Usuario())->findById($this->section->getId());
        if ($usuario->dropshipper == 1 || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {
            $cartoes = (new Cartao())->find("usuario = " . $this->section->getId())->fetch(true);
            $contarCartao = (new Cartao())->find("status = 2 AND usuario = " . $this->section->getId())->count();
            $adquirentes = (new Adquirente())->find("usuario = " . $this->section->getId())->fetch(true);
            $usuarioDados = (new UsuarioDados())->find("usuario = " . $this->section->getId())->fetch();

            echo $this->view->render("checkout-transparente/integracoes", [
                "title" => "Checkout Transparente",
                "section" => @$section,
                "permissao" => @$permissao,
                "cartoes" => $cartoes,
                "contarCartao" => $contarCartao,
                "adquirentes" => $adquirentes,
                "usuarioDados" => $usuarioDados,
            ]);
        } else {
            echo $this->view->render("checkout-transparente/integracoes", [
                "title" => "Checkout Transparente",
                "section" => @$section,
                "permissao" => @$permissao,
            ]);
        }
    }

    public function logs($data)
    {
        $arquivo = fopen(DIRETORIO . "logs/cielo-transacao.txt", 'a+');

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

        $cartao->usuario            = $this->section->getId();
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
        $cartao->numero_cartao      = base64_encode(@$data["numcart"]);
        $cartao->ultimos_digitos    = substr(@$data["numcart"], -4);
        $cartao->titular            = @$data["titular"];
        $cartao->mes                = base64_encode(@$data["mes-val"]);
        $cartao->ano                = base64_encode(@$data["ano-val"]);
        $cartao->cvv                = base64_encode(@$data["cvv"]);
        $cartao->bandeira           = @$data["bandeira"];

        $cielo = new Cielo();
        $data["preco"] = 3000;
        $data["parcela"] = 1;
        $transacao = $cielo->compraCartaoSimples($data);
        $data["json"] = serialize($transacao);
        $this->logs($data, "cadastro-cartao");
        // var_dump($transacao);

        $cartao->transacao = $transacao->Payment->Tid;
        $cartao->payment_id = $transacao->Payment->PaymentId;;

        if (@$transacao->Payment->Status == 2) {
            $cartao->status = 2;
            $data["pay_id"] = $transacao->Payment->PaymentId;
            $estorno = $cielo->reembolsoCartao($data);
            // var_dump($estorno);
        } elseif (@$transacao->Payment->Status == 3) {
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
        $cartoes = (new Cartao())->find("usuario = {$_SESSION['id']} AND status = 2")->count();
        $cartao = (new Cartao())->findById($data["id"]);

        if ($cartoes > 1 || $cartao->status != 2) {

            if ($cartao->destroy()) {
                echo "Excluído com sucesso";
            } else {
                echo $cartao->fail()->getMessage();
            }
        } else {
            echo "Não será possível excluir esse cartão enquanto não tiver outro cartão aprovado";
        }
    }

    public function cadastrarAdquirente($data)
    {
        function tirarAcentos($string)
        {
            return preg_replace(array('/[0-9\@\.\;\""]+/', "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o o u U n N"), $string);
        }

        if (@$data["id_adquirente"])
            $adquirente = (new Adquirente())->findById($data["id_adquirente"]);
        else
            $adquirente = new Adquirente();

        $adquirente->usuario            = $this->section->getId();
        $adquirente->status             = 1;
        $adquirente->adquirente         = @$data["adquirente"];
        $adquirente->chave              = trim(@$data["chave"]);
        $adquirente->token              = trim(@$data["token"]);
        $adquirente->nome_fatura        = tirarAcentos(@$data["nome_fatura"]);
        $adquirente->boleto_cielo       = @$data["boleto_cielo"];
        // $adquirente->pix_redrocket        = @$data["pix_redrocket"];
        // $adquirente->taxa1              = str_replace(",",".",@$data["taxa1"]);
        $adquirente->taxa1 = 0.00;
        $adquirente->taxa2              = str_replace(",", ".", @$data["taxa2"]);
        $adquirente->taxa3              = str_replace(",", ".", @$data["taxa3"]);
        $adquirente->taxa4              = str_replace(",", ".", @$data["taxa4"]);
        $adquirente->taxa5              = str_replace(",", ".", @$data["taxa5"]);
        $adquirente->taxa6              = str_replace(",", ".", @$data["taxa6"]);
        $adquirente->taxa7              = str_replace(",", ".", @$data["taxa7"]);
        $adquirente->taxa8              = str_replace(",", ".", @$data["taxa8"]);
        $adquirente->taxa9              = str_replace(",", ".", @$data["taxa9"]);
        $adquirente->taxa10             = str_replace(",", ".", @$data["taxa10"]);
        $adquirente->taxa11             = str_replace(",", ".", @$data["taxa11"]);
        $adquirente->taxa12             = str_replace(",", ".", @$data["taxa12"]);

        // CRIAÇÃO DO WEBHOOK WIRECARD
        if (@$adquirente->adquirente == "wirecard" && @$data["id_adquirente"] == "") {
            @$data["token"] = @$adquirente->token;
            @$data["chave"] = @$adquirente->chave;

            $notificacao = new Wirecard();
            $webhook = $notificacao->criarNotificacaoWebhook(@$data);

            // var_dump($webhook);
            // exit;
            if (@$webhook->events && !@$webhook->ERROR) {
                // $adquirente->webhook = 1;
            } else if (@$webhook->ERROR) {
                // $adquirente->webhook = 0;
                echo "Chave API ou Token inválido!";
                exit;
            }
        }
        // FIM CRIAÇÃO DE WEBHOOK WIRECARD

        // echo $adquirente->webhook;
        // exit;

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

    public function cobrancas($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("cobranca_dropshipping");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'cobranca_dropshipping'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $usuario = (new Usuario())->findById($this->section->getId());

        if ($usuario->dropshipper == 1 || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {
            if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {

                $cobrancasCount = (new Cobranca())->find()->count();
                $paginas = ceil($cobrancasCount / $offset);
                $cobrancas = (new Cobranca())->find()->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
            } else {
                $cobrancasCount = (new Cobranca())->find("produtor = " . $this->section->getId())->count();
                $paginas = ceil($cobrancasCount / $offset);
                $cobrancas = (new Cobranca())->find("produtor = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
            }

            echo $this->view->render("checkout-transparente/cobrancas", [
                "title" => "Cobranças",
                "section" => @$section,
                "permissao" => @$permissao,
                "cobrancas" => @$cobrancas,
                "paginador" => @$paginador,
                "total" => @$paginas,
                "pagina" => @$data["page"] ? $data["page"] : 1,
                "usuario" => @$usuario
            ]);
        } else {
            echo $this->view->render("checkout-transparente/cobrancas", [
                "title" => "Cobranças",
                "section" => @$section,
                "permissao" => @$permissao,
            ]);
        }
    }

    public function cobrarDrop($data)
    {
        echo "<pre>";
        $inicio = date("Y-m-d", strtotime("-7 days", strtotime(date("Y-m-d"))));
        $fim = date("Y-m-d");

        $busca1 = "(comissionada = 0 AND status_transacao = 4 AND data_finalizada BETWEEN '$inicio 00:00:00' AND '$fim 23:59:59')";
        $busca2 = "(comissionada = 2 AND status_transacao = 6 AND data_ultimo_evento BETWEEN '$inicio 00:00:00' AND '$fim 23:59:59')";
        $vendas = (new Venda())->find("adquirente != 'redrocket' AND ($busca1)")->fetch(true);

        // var_dump($vendas);
        // exit;
        if ($vendas) {
            foreach ($vendas as $venda) {
                echo "Venda: $venda->id <br>" .
                    "Produtor: $venda->produtor <br>" .
                    "Valor: $venda->valor_bruto <br>" .
                    "Taxa: {$venda->dataProdutor()->taxa_drop} <br>";

                $valor_redrocket = $venda->valor_bruto * $venda->dataProdutor()->taxa_drop / 100;
                $valor_redrocket = number_format($valor_redrocket, 2, ".", "");
                echo "Valor redrocket: $valor_redrocket <br><br>";

                @$valor_cobrado[$venda->produtor]["valor"] += $valor_redrocket;
                @$valor_cobrado[$venda->produtor]["produtor"] = $venda->produtor;

                $venda->comissionada += 1;
                $venda->valor_drop = $valor_redrocket;

                if ($venda->save()) {
                    echo "Salvo com sucesso <br>";
                } else {
                    echo $venda->fail()->getMessage() . "<br>";
                }
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

            // var_dump($valor_cobrado);
            if ($valor_cobrado) {
                foreach ($valor_cobrado as $valor_cobrado) {
                    $cartao = (new Cartao())->find("usuario = {$valor_cobrado['produtor']} AND status = 2")->fetch(true);

                    if ($cartao) {
                        foreach ($cartao as $cartao) {
                            $dados["nome"] = $cartao->nome;
                            $dados["preco"] = $valor_cobrado["valor"];
                            $dados["numcart"] = base64_decode($cartao->numero_cartao);
                            $dados["titular"] = $cartao->titular;
                            $dados["mes-val"] = base64_decode($cartao->mes);
                            $dados["ano-val"] = base64_decode($cartao->ano);
                            $dados["cvv"] = base64_decode($cartao->cvv);

                            $cielo = (new Cielo())->compraCartaoSimples($dados);
                            // var_dump($cielo);

                            $dados["transacao"] = @$cielo->Payment->Tid;
                            $dados["id_pagamento"] = @$cielo->Payment->PaymentId;
                            $dados["status_cobranca"] = @$status_transacaoC[@$cielo->Payment->Status];
                            $dados["meio_pagamento"] = "Cielo";
                            $dados["produtor"] = @$valor_cobrado['produtor'];

                            if ($cielo->Payment->Status == 3) {
                                $dados["nova_cobranca"] = 1;
                                $dados["tentativas"] = 1;
                            } else {
                                $dados["nova_cobranca"] = 0;
                                $dados["tentativas"] = 0;
                            }

                            $this->cadastrarCobranca($dados);

                            echo "***************************************<br>";
                            echo "Transacao: {$dados["transacao"]} <br>" .
                                "Status: {$dados["status_cobranca"]} <br>" .
                                "Valor: {$dados["preco"]} <br>" .
                                "Produtor: {$dados["produtor"]}<br>";
                            echo "***************************************<br><br>";

                            if ($dados["status_cobranca"] != 5) {
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            echo "Nenhuma venda encontrada";
        }
    }

    public function retentativaCobranca($data)
    {
        echo "<pre>";
        if (@$data["cobranca"]) {
            $cobrancas = (new Cobranca())->find("id = {$data["cobranca"]} AND status_cobranca = 5 OR status_cobranca = '' OR status_cobranca IS NULL")->fetch(true);
        } else {
            $cobrancas = (new Cobranca())->find("status_cobranca = 5 OR status_cobranca = '' OR status_cobranca IS NULL")->fetch(true);
        }

        $status_transacaoC = array(
            "0"  => 1,
            "1"  => 3,
            "2"  => 4,
            "3"  => 5,
            "10" => 6,
            "11" => 6,
            "12" => 1,
            "13" => 5,
            "20" => 1,
            ""   => 5
        );

        if ($cobrancas) {
            foreach ($cobrancas as $cobranca) {
                echo "Cobrança: $cobranca->id <br>";
                $cartoes = (new Cartao())->find("usuario = $cobranca->produtor AND status = 2")->fetch(true);

                if ($cartoes) {
                    foreach ($cartoes as $cartao) {
                        $dados["cobranca"] = $cobranca->id;
                        $dados["nome"] = $cartao->nome;
                        $dados["preco"] = $cobranca->valor_cobrado;
                        $dados["numcart"] = base64_decode($cartao->numero_cartao);
                        $dados["titular"] = $cartao->titular;
                        $dados["mes-val"] = base64_decode($cartao->mes);
                        $dados["ano-val"] = base64_decode($cartao->ano);
                        $dados["cvv"] = base64_decode($cartao->cvv);

                        $cielo = (new Cielo())->compraCartaoSimples($dados);
                        // var_dump($cielo);

                        $dados["transacao"] = @$cielo->Payment->Tid;
                        $dados["id_pagamento"] = @$cielo->Payment->PaymentId;
                        $dados["status_cobranca"] = @$status_transacaoC[@$cielo->Payment->Status];
                        $dados["meio_pagamento"] = "Cielo";

                        if (@$cielo->Payment->Status == 3) {
                            $dados["nova_cobranca"] = 1;
                            $dados["tentativas"] = $cobranca->tentativas + 1;
                        } else {
                            $dados["nova_cobranca"] = 0;
                            $dados["tentativas"] = $cobranca->tentativas + 1;
                        }

                        $this->atualizarCobranca($dados);

                        echo "***************************************<br>";
                        echo "Transacao: {$dados["transacao"]} <br>" .
                            "Status: {$dados["status_cobranca"]} <br>" .
                            "Valor: {$dados["preco"]} <br>" .
                            "Produtor: {$cobranca->produtor}<br>";
                        echo "***************************************<br><br>";

                        if ($dados["status_cobranca"] != 5) {
                            break;
                        }

                        if ($dados["status_cobranca"] == 5) {
                            $cartao->status = 3;
                            if ($cartao->save()) {
                                echo "Cartão cancelado <br>";
                            } else {
                                echo $cartao->fail()->getMessage();
                            }
                        }
                    }
                } else {
                    echo "Nenhum cartão aprovado encontrado";
                }
            }
        } else {
            echo "Nenhuma venda para retentar";
        }
    }

    public function cadastrarCobranca($data)
    {
        $cobranca = new Cobranca();

        $cobranca->transacao = @$data["transacao"];
        $cobranca->id_pagamento = @$data["id_pagamento"];
        $cobranca->meio_pagamento = @$data["meio_pagamento"];
        $cobranca->valor_cobrado = @$data["preco"];
        $cobranca->produtor = @$data["produtor"];
        $cobranca->status_cobranca = @$data["status_cobranca"];
        $cobranca->nova_cobranca = @$data["nova_cobranca"];
        $cobranca->data_finalizada = date("Y-m-d H:i:s");
        $cobranca->tentativas = @$data["tentativas"];

        if ($cobranca->save()) {
            echo "Cobrança Cadastrada<br>";
        } else {
            echo $cobranca->fail()->getMessage() . "<br>";
        }
    }

    public function atualizarCobranca($data)
    {
        $cobranca = (new Cobranca())->findById($data["cobranca"]);

        $cobranca->transacao = @$data["transacao"];
        $cobranca->id_pagamento = @$data["id_pagamento"];
        $cobranca->meio_pagamento = @$data["meio_pagamento"];
        $cobranca->valor_cobrado = @$data["preco"];
        $cobranca->status_cobranca = @$data["status_cobranca"];
        $cobranca->nova_cobranca = @$data["nova_cobranca"];
        $cobranca->data_finalizada = date("Y-m-d H:i:s");
        $cobranca->tentativas = @$data["tentativas"];

        if ($cobranca->save()) {
            echo "Cobrança Atualizada<br>";
        } else {
            echo $cobranca->fail()->getMessage() . "<br>";
        }
    }

    public function gerarNovoBoleto($data)
    {

        // var_dump($data);
        // exit;
        $venda = (new Venda())->findById(@$data["id"]);

        if (@$venda->adquirente != "redrocket") {
            $adquirente = (new Adquirente())->findById(@$venda->adquirente);
            $usuarioDados = (new UsuarioDados())->find("usuario = " . $venda->produtor)->fetch();

            if (@$adquirente->adquirente != "") {
                $data["adquirente"] = @$adquirente->id;

                // PREÇO EM CENTAVOS PARA TODAS AS ADQUIRENTES - MENOS MERCADOPAGO
                $data["preco"] = @$data["valor_total"] * 100;

                $checkout = (new Checkout())->find("codigo = $venda->codigo_checkout")->fetch();

                if (@$checkout->endereco_obrigatorio == 0) {
                    $data["cep"] = @$usuarioDados->cep;
                    $data["rua"] = @$usuarioDados->rua;
                    $data["numero"] = @$usuarioDados->numero;
                    $data["complemento"] = "";
                    $data["bairro"] = @$usuarioDados->bairro;
                    $data["cidade"] = @$usuarioDados->cidade;
                    $data["estado"] = @$usuarioDados->estado;
                }

                // if (@$data["vencimento"] == "") {
                //     if (date("D") != "Sun" || date("D") != "Mon" || date("D") != "Tue") {
                //         $data["vencimento"] = date("Y-m-d", strtotime("+5 days"));
                //     } else if (date("D") == "Sat") {
                //         $data["vencimento"] = date("Y-m-d", strtotime("+4 days"));
                //     } else {
                //         $data["vencimento"] = date("Y-m-d", strtotime("+3 days"));
                //     }
                // } else {
                //     if (date("D", strtotime($data["vencimento"])) == "Sat") {
                //         $data["vencimento"] = date("Y-m-d", strtotime("+2 days", strtotime($data["vencimento"])));
                //     } else if (date("D", strtotime($data["vencimento"])) == "Sun") {
                //         $data["vencimento"] = date("Y-m-d", strtotime("+1 day", strtotime($data["vencimento"])));
                //     }
                // }

                if (@$adquirente->adquirente == "wirecard") {
                    $wirecard = new Wirecard();

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

                    $cliente = $wirecard->cliente(@$data);
                    $data["customer_id"] = @$cliente->id;

                    $pedido = $wirecard->pedido($data);
                    $data["order_id"] = @$pedido->id;

                    $transacao = $wirecard->compraBoleto($data);
                    $data["transacao"] = @$transacao->id;
                    $data["status_transacao"] = @$status_transacao[@$transacao->status];
                    $data["meio_pagamento"] = "Moip";
                    $data["link_boleto"] = @$transacao->_links->payBoleto->printHref;
                    $data["linha_digitavel"] = @$transacao->fundingInstrument->boleto->lineCode;
                } else if (@$adquirente->adquirente == "cielo") {
                    $cielo = new CieloTransparente();

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

                    $transacao = $cielo->compraBoleto($data);
                    $data["nit_pix"] = @$transacao->Payment->PaymentId;
                    $data["status_transacao"] = @$status_transacaoC[@$transacao->Payment->Status];
                    $data["link_boleto"] = @$transacao->Payment->Url;
                    $data["linha_digitavel"] = @$transacao->Payment->DigitableLine;
                    $data["transacao"] = @$transacao->MerchantOrderId;
                } else if (@$adquirente->adquirente == "pagarme") {
                    $pagarme = new PagarMe();

                    $status_transacaoP = array(
                        "pending" => 1,
                        "paid" => 4,
                        "canceled" => 5,
                        "failed" => 5,
                        "refunded" => 6
                    );

                    $transacao = $pagarme->compraBoleto($data);
                    $data["transacao"] = @$transacao->charges[0]->id;
                    $data["nit_pix"] = @$transacao->charges[0]->id;
                    $data["link_boleto"] = @$transacao->charges[0]->last_transaction->pdf;
                    $data["linha_digitavel"] = @$transacao->charges[0]->last_transaction->line;
                    $data["status_transacao"] = $status_transacaoP[@$transacao->charges[0]->status];
                } else if (@$adquirente->adquirente == "pagseguro") {
                    $pagseguro = new Pagseguro();

                    $status_transacao = array(
                        "WAITING" => 1,
                        "AUTHORIZED" => 3,
                        "PAID" => 4,
                        "DECLINED" => 5,
                        "CANCELED" => 5
                    );

                    $transacao = $pagseguro->compraBoleto($data);
                    $data["transacao"] = @$transacao->payment_method->boleto->id;
                    $data["nit_pix"] = @$transacao->id;

                    $data["link_boleto"] = @$transacao->links[0]->href;
                    $data["linha_digitavel"] = @$transacao->payment_method->boleto->barcode;
                    $data["status_transacao"] = @$status_transacao[@$transacao->status];
                } else if (@$adquirente->adquirente == "mercadopago") {
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

                    $data["preco"] = $data["valor_total"];

                    $transacao = $mercadopago->compraBoleto($data);
                    $data["nit_pix"] = @$transacao->id;
                    $data["link_boleto"] = @$transacao->transaction_details->external_resource_url;
                    $data["linha_digitavel"] = @$transacao->barcode->content;
                    $data["transacao"] = @$transacao->id;
                    $data["status_transacao"] = @$status_transacao[@$transacao->status];
                }

                if ($data["transacao"] && @$data["transacao"] != "") {
                    $venda->transacao = @$data["transacao"];
                    $venda->link_boleto = @$data["link_boleto"];
                    $venda->linha_digitavel = @$data["linha_digital"];
                    $venda->data_vencimento = @$data["vencimento"];

                    // echo "Novo Vencimento : " . @$data["vencimento"] . "\n";
                    // exit;
                    if ($venda->save()) {
                        $data["msg"] = "Salvo com sucesso";
                    } else {
                        $data["msg"] = "Ocorreu um problema na sua solicitação. Tente novamente mais tarde";
                        $this->logs($data, "novo-boleto");
                    }

                    echo json_encode($data);
                } else {
                    $data["msg"] = "Ocorreu um problema na sua solicitação. Tente novamente mais tarde";
                    $this->logs($data, "novo-boleto");
                }
            }
        }
    }
}

<?php

namespace Source\App\Checkout1;

use League\Plates\Engine;

use Source\Models\Checkout\Checkout;
use Source\Models\Cookies\Cookies;
use Source\Models\Upsell\Upsell;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Vendas\Venda;
use Source\Models\Notificacoes\Notificacao;
use Source\Models\Assinaturas\AssinaturaCadastro;
use Source\Models\Frete\Frete;
use Source\Models\Hotjar\Hotjar;

use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Configuracoes\Taxa;
use Source\Models\Configuracoes\ConfiguracaoCheckout;

use Source\Models\Cartao\Cartao;
use Source\Models\Cartao\CartaoNumero;

use Source\Helpers\Emails;

use Source\App\Checkout1\Wirecard;
use Source\App\Checkout1\PagarMe;
use Source\App\Checkout1\MercadoPago;
use Source\App\Checkout1\BsPay;
use Source\App\Checkout1\Bradesco;
use Source\App\Checkout1\Cielo;
use Source\App\Checkout1\Sitef;
use Source\App\Checkout1\Validacao;

use Source\App\Usuarios\AppUsuarios;

use Source\App\Postback\AppPostback;
use Source\App\Processos\AppProcessos;
use Source\App\Assinaturas\AppAssinaturas;

use Moip\Moip;
use Moip\Auth\BasicAuth;
use Source\App\Asaas\AppAsaas;
use Source\App\Asaas\AsaasClientes;
use Source\App\Asaas\AsaasCobrancas;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Cookies\cookiesTelevendas;

use function PHPSTORM_META\type;

class AppCheckout
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

    public function home($data)
    {
        echo $this->view->render("checkout", [
            "title" => "Produto Não Encontrado | " . SITE,
            "mensagem" => "Produto Não Encontrado",
            "dados" => false
        ]);
    }

    public function pixel($data)
    {
        echo $this->view->render("pixel", [
            "title" => "Pixel",
        ]);
    }

    public function pixelTaboola($data)
    {
        echo $this->view->render("pixel-taboola", [
            "title" => "Pixel",
        ]);
    }

    public function analytics($data)
    {
        echo $this->view->render("analytics", [
            "title" => "Analytics",
        ]);
    }

    public function comissoes($data)
    {
        $valor_produto = $data["valor_produto"];
        $total_transacao = $data["total_transacao"];
        $taxa_transacao = $data["taxa_transacao"];
        $valor_frete = $data["valor_frete"];
        $valor_juros = $data["valor_juros"];
        $valor_afiliado = $data["valor_afiliado"];
        $valor_coprodutor = $data["valor_coprodutor"];
    }

    public function produto($data)
    {
        $codVenda = @$data["venda"];
      
        if ($codVenda) {
            $venda = (new Venda())->findById($codVenda);
        }
         

        $kit = $data["plano"]; // CÓDIGO DO PLANO NA URL
        $checkout = (new Checkout())->find("codigo = :plano", "plano={$kit}")->fetch();
        $taxa = (new Taxa())->findById(1);

        if (!$checkout) {
            echo "Produto Não Encontrado";
            exit;
        }

        $produto = (new Produto())->find("id = $checkout->produto")->fetch();
        if ($checkout->plano != "") {
            $plano = (new Plano())->find("id = $checkout->plano AND ativo = 1")->fetch();
            $idPlano = @$plano->id;
        } else {
            $plano = @$produto;
            $idPlano = "";
        }

        if (!$plano) {
            echo "Produto Não Encontrado";
            exit;
        }

        $produto->nome = trim($produto->nome);
        $plano->nome = trim($plano->nome);

        if (@$checkout->usuario)
            $usuario = (new Usuario())->findById($checkout->usuario);

        if (@$usuario && @$usuario->taxa_redrocket) {
            $taxa_transacao = $produto->tipo_cobranca == 1 ? $usuario->taxa_redrocket : $usuario->taxa_assinatura;
        } else {
            $taxa_transacao =  $produto->tipo_cobranca == 1 ? $taxa->taxa_redrocket : $taxa->taxa_assinatura;
        }
        // echo $taxa_transacao . " - ";
        if (@$_GET["ec"] && $usuario->assinatura_ecommerce == "taxa")
            $taxa_transacao = $taxa_transacao + $usuario->taxa_ecommerce;

        // echo $taxa_transacao;

        $ip = (string) $_SERVER['REMOTE_ADDR'];
        if (@$_COOKIE[$produto->id]) {
            parse_str($_COOKIE[$produto->id], $output);
            echo "<!-- {$output["codigo"]} -->";
            $cookies = (new Cookies())->find("navegador = '{$output["codigo"]}' AND produto = '$produto->id'")->fetch();
        }

        if (@$cookies) {
            $afiliacao = (new Afiliacao())->find("afiliado = $cookies->afiliado AND produto = $cookies->produto")->fetch();
            if ($afiliacao && $afiliacao->status != 2) {
                $cookies = null;
            }
        }

        $coproducao = (new CoProdutor())->find("produto = $checkout->produto AND aceito = 1")->fetch(true);
        $coproducaoCount = (new CoProdutor())->find("produto = $checkout->produto  AND aceito = 1")->count();
        $i = 1;
        $valorOcta = number_format((@$plano->preco * ($usuario->taxa_redrocket / 100)) + 1, 2);

        $coprodutores = "";
        $comissao_coprodutor = "";
        $somaCoprodutor = 0;

        if (@$plano->habilitar_comissao_coprodutores) {
            $cop = explode(",", $plano->comissao_coprodutores);

            if ($cop) {
                foreach ($cop as $cop1) {
                    $cop2 = explode("|", $cop1);
                    @$coprodutores .= "/$cop2[0]/";

                    if (@$cop2[1] == 0) {
                        $comissao_cop = number_format(($plano->preco - $valorOcta) * (@$cop2[2] / 100), 2);
                        @$comissao_coprodutor .= "/$comissao_cop/";
                    } else {
                        $comissao_cop = number_format($cop2[2], 2);
                        @$comissao_coprodutor .= "/$comissao_cop/";
                    }
                    @$somaCoprodutor += $comissao_cop;
                }
            }
        } elseif ($coproducao) {
            // echo $valorOcta;
            foreach ($coproducao as $coprodutor) {
                @$coprodutores .= "/$coprodutor->co_produtor/";
                if ($coprodutor->tipo_comissao == 0) {
                    $comissao_cop = number_format(($plano->preco - $valorOcta) * ($coprodutor->comissao / 100), 2);
                    @$comissao_coprodutor .= "/$comissao_cop/";
                } else {
                    $comissao_cop = number_format($coprodutor->comissao, 2);
                    @$comissao_coprodutor .= "/$comissao_cop/";
                }
                @$somaCoprodutor += $comissao_cop;
                $i++;
            }
        }

        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(1);
        $sandbox = $configuracaoCheckout->sandbox;

        $atual = date("Y-m-d H:i:s");
        $agora = date("Y-m-d H:i:s", strtotime("-5 minutes"));
        $semana = date("Y-m-d H:i:s", strtotime("-7 days"));
        $minutos = date("Y-m-d H:i:s", strtotime("-30 minutes"));
        $hoje = date("Y-m-d 00:00:00");
        $hora = date("Y-m-d H:i:s", strtotime("-60 minutes"));

        // $notificacoes = (new Notificacao())->find("produto = $produto->id")->fetch();
        // $frasesCount[1] = (new Venda())->find("produto = $produto->id AND created_at BETWEEN '$agora' AND '$atual'")->count();
        // $frasesCount[2] = (new Venda())->find("produto = $produto->id AND created_at BETWEEN '$semana' AND '$atual'")->count();
        // $frasesCount[3] = (new Venda())->find("produto = $produto->id AND created_at BETWEEN '$minutos' AND '$atual'")->count();
        // $frasesCount[4] = (new Venda())->find("produto = $produto->id AND created_at BETWEEN '$hoje' AND '$atual'")->count();
        // $frasesCount[5] = (new Venda())->find("produto = $produto->id AND created_at BETWEEN '$hora' AND '$atual'")->count();
        // $nomes[1] = (new UsuarioDados())->find()->order("id DESC")->fetch();
        // $nomes[2] = (new UsuarioDados())->find("created_at BETWEN '$agora' AND '$atual'")->order("id DESC")->fetch();

        $notificacoes = (new Notificacao())->find("produto = $produto->id")->fetch();
        if ($notificacoes) {
            $frasesCount[1] = rand($notificacoes->valor1, 100);
            $frasesCount[2] = rand($notificacoes->valor2, 100);
            $frasesCount[3] = rand($notificacoes->valor3, 100);
            $frasesCount[4] = rand($notificacoes->valor4, 100);
            $frasesCount[5] = rand($notificacoes->valor5, 100);
            $nomes[1] = (new UsuarioDados())->find()->order("id DESC")->fetch();
            $nomes[2] = (new UsuarioDados())->find("created_at BETWEN '$semana' AND '$atual'")->order("id DESC")->fetch();
        }

        $modelo = array(
            1 => "checkout3",
            2 => "checkout3",
            3 => "checkout3",
            4 => "checkout-venda",
        );
        $chc = @$_GET["tipo"] ? $_GET["tipo"] : "checkout";
        $chc = $checkout->modelo_checkout ? $modelo[$checkout->modelo_checkout] : $chc;

        $planos = new Plano();

        // Verifica se é um checkout compartilhado por um atendente do televendas
        if (@$_GET["at"]) {
            $atendente = $_GET["at"];
            $cookies_televendas = (new cookiesTelevendas())->find("produto =  $produto->codigo AND atendente = $atendente")->order("id DESC")->limit(1)->fetch();
        } else {
            $cookies_televendas = "";
        }

        $nome_adquirente = @$checkout->adquirente == "redrocket" ? "redrocket" : @$checkout->dataAdquirente()->adquirente;

        $hotjar = (new Hotjar())->find("usuario = $checkout->usuario")->fetch(true);

        if (@!$_COOKIE["analytics"]) {
            $numero_de_bytes = 4;
            $resultado_bytes = random_bytes($numero_de_bytes);
            $codigo_cookie = strtoupper(bin2hex($resultado_bytes));

            setcookie("analytics", $codigo_cookie, time() + (60 * 60 * 24 * 10000), "/");
        }

        $dados["url"]           = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $dados["url_anterior"]  = @$_SERVER['HTTP_REFERER'];
        $dados["navegador"]     = @$_COOKIE["analytics"];
        $dados["produto"]       = @$produto->id;
        $dados["plano"]         = @$plano->id;
        $dados["checkout"]      = @$checkout->id;
        $dados["afiliado"]      = @$cookies->afiliado;
        $dados["campanha"]      = @$cookies->campanha;
        $dados["produtor"]      = @$produto->usuario;

        echo $this->view->render("$chc", [
            "title" => $plano->nome . " | " . SITE,
            "mensagem" => "",
            "venda" => @$venda,
            "taxa" => $taxa,
            "taxa_transacao" => $taxa_transacao,
            "plano" => $plano,
            "planos" => $planos,
            "produto" => $produto,
            "idPlano" => $idPlano,
            "checkout" => $checkout,
            "sandbox" => $sandbox,
            "cookies" => @$cookies,
            "cookies_televendas" => @$cookies_televendas,
            "coprodutores" => @$coprodutores,
            "comissao_coprodutor" => @$comissao_coprodutor,
            "somaCoprodutor" => @$somaCoprodutor,
            "coproducao" => @$coproducao,
            "notificacoes" => @$notificacoes,
            "frasesCount" => @$frasesCount,
            "nomes" => @$nomes,
            "nome_adquirente" => @$nome_adquirente,
            "hotjar" => @$hotjar,
        ]);
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
                if (is_array($valor)) {
                    $val = json_encode($valor);
                    $texto .= $val;
                } else {

                    if ($chave == "numcart" || $chave == "cvv") {
                        $valor = "-";
                    }

                    if ($chave != "codigo_order_bump" && $chave != "comissoes_pagamento")
                        $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
                }
            }
        } else {
            $texto .= "Erro dos dados";
        }
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
    }

    public function buscarCep($data)
    {
        $frete = (new Frete())->find("checkout = " . $data["checkout"])->fetch();

        if ($frete) {
            if ($frete->tipo_frete == 1) { // FRETE FIXO
                $retorno[0]["valor_frete"] = $frete->valor_frete;
                $retorno[0]["prazo_frete"] = $frete->prazo_entrega;

                if ($frete->frete_gratis == 1) {
                    $retorno[0]["valor_frete_gratis"] = $frete->valor_frete_gratis;
                    $retorno[0]["prazo_frete_gratis"] = $frete->prazo_entrega_gratis;
                }
            } elseif ($frete->tipo_frete == 2) { // FRETE CORREIOS

            }
        }
    }

    public function splitPagamento($data)
    {

        if ( ! empty( $data['venda'] )  && empty( @$data["comissao_afiliado"] ) ) {
            $venda = (new Venda())->findById($data['venda']);

            if ( ! empty( $venda->afiliado ) && ! empty( $venda->comissao_afiliado ) ) {
                $data["afiliado"] = $venda->afiliado;
                $data["comissao_afiliado"] = $venda->comissao_afiliado;
                $data["comissao_produtor"] = $venda->comissao_produtor;
            }

        }


        $usRed = (new Usuario)->findById(1);

        $data["comissoes_pagamento"]["redrocket"]["usuario"] = "$usRed->pagarme_conta";
        $data["comissoes_pagamento"]["redrocket"]["comissao"] = $data["valor_redrocket"] + @$data["valor_juros"];

        $walletProdutor = (new Usuario)->findById($data["produtor"]);
        $data["comissoes_pagamento"]["produtor"]["usuario"] = $walletProdutor->pagarme_conta;
        $data["comissoes_pagamento"]["produtor"]["comissao"] = (float) $data["comissao_produtor"] + ((float) @$data["tx_frete"]);

        if (@$data["comissao_afiliado"]) {
            $walletAfiliado = (new Usuario)->findById($data["afiliado"]);
            $data["comissoes_pagamento"]["afiliado"]["usuario"] = $walletAfiliado->pagarme_conta;
            $data["comissoes_pagamento"]["afiliado"]["comissao"] = $data["comissao_afiliado"];
        }


        if (@$data["coprodutores"]) {
            $cop = explode("/", $data["coprodutores"]);
            $com = explode("/", $data["comissao_coprodutores"]);
            $i = 0;

            foreach ($cop as $cop) {
                if ($cop) {
                    $walletCoProdutor = (new Usuario)->findById($cop);
                    $data["comissoes_pagamento"][$cop]["usuario"] = $walletCoProdutor->pagarme_conta;
                    $data["comissoes_pagamento"][$cop]["comissao"] = $com[$i];
                }
                $i++;
            }
        }

        $this->logs($data["comissoes_pagamento"], "split-pagarme");
        return $data;
    }

    public function transacao2($data)
    {
        var_dump($data);
    }

    public function transacao($data)
    {

        
        /** 
         * SE CLIENTE NÃO PREENCHER O EMAIL
         */
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

        
        if (substr($telefone, 0, 2) == "55") {
            $data["ddd"] = substr($telefone, 2, 2);
            $data["telefone"] = substr($telefone, 4, 12);
        } else {
            $data["ddd"] = substr($telefone, 0, 2);
            $data["telefone"] = substr($telefone, 2, 12);
        }



        $data["pais"] = "BR";

        $resultado_bytes = random_bytes(5);
        $data["protocolo"] = strtoupper(bin2hex($resultado_bytes));

        if( ! empty( $_COOKIE['url'] ) ) {
            $data['cookie_url'] = $_COOKIE['url'];
        }

        $this->logs($data, "dados-enviados");

        /** 
         * VERIFICA SE CLIENTE TEM CADASTRO
         */
        $cadastrado = (new UsuarioDados())->verificaCpf(["cpf" => $data["cpf"]]);

        if ($cadastrado) {
            $us = (new UsuarioDados())->find("cpf_cnpj = " . $data["cpf"])->fetch();
            $data["nasc"] = @$us->data_nascimento ? $us->data_nascimento : $data["nasc"];

            $verificarCliente = (new AppAsaas(null))->verificarCliente($us->usuario);

            if ($verificarCliente) {
                $data["customer_id"] = @$verificarCliente->client_id;
            } else {
                $clienteAsaas = new AsaasClientes(null);
                $cliente = $clienteAsaas->novoCliente($data);
                $data["customer_id"] = @$cliente->id;
                $data["client_id"] = @$cliente->id;
                $data["id_user"] = @$us->usuario;

                $asaasClientes = (new AppAsaas(null))->cadastrarUsuarioAsaas($data);
                $data["passo"] = "passo 1";
            }
        }

            
        $data["cep"] = @$data["cep"] ? (new Validacao())->verificaCep(@$data["cep"]) : NULL;

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
                $dataEmail["mensagem"] .= "Protocolo: " . $data["protocolo"] . " <br>";
                $dataEmail["mensagem"] .= $comprador['msg'] . "<br>";
                $dataEmail["mensagem"] .= "Dados enviados <br><br>";

                foreach ($data as $key => $value) {
                    $dataEmail["mensagem"] .= "$key: $value<br>";
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

                $clienteAsaas = new AsaasClientes(null);
                $cliente = $clienteAsaas->novoCliente($data);
                $data["customer_id"] = @$cliente->id;
                $data["client_id"] = @$cliente->id;
                $data["id_user"] = @$user->id;

                $asaasClientes = (new AppAsaas(null))->cadastrarUsuarioAsaas($data);
                $data["passo"] = "passo 2";
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
                    $cartao = true;
                } elseif ($pagamento == 2) {
                    $boleto = true;
                } elseif ($pagamento == 10) {
                    $cartao = true;
                    $boleto = true;
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
        // Fim Código e preço Order Bump

        // 
        $ip = (string) $_SERVER['REMOTE_ADDR'];
        if ($data["pagamento"] == "credit_card") {

            $data["forma_pagamento"] = 1;
            /** 
             * CALCULAR O VALOR TOTAL
             */
            if (@$cartao)
                $valProd = (float) $data["preco"] + (float) @$data["frete"] - (float) @$data["valor_desconto"] + (float) @$total_ord_bump;
            else
                $valProd = (float) $data["preco"] + (float) @$data["frete"] + (float) @$total_ord_bump;

            $data["parcela"] = @$data["parcela"] ? $data["parcela"] : 1;
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
            // echo(var_dump($preco));
            // echo(var_dump($juros));
            // exit;

            $data["preco"] = $preco;
            $data["valor_bruto"] = ((float) $preco / 100);
            $data["valor_frete"] = @$data["frete"];

            $desconto = @$data["valor_desconto"] ? $data["valor_desconto"] : 0;
            $data["valor_redrocket"] = number_format((((float) $valProd - (float) @$desconto) * ((float) $data["taxa_redrocket"] / 100)) + 1, 2);
            $data["valor_liquido"] = (float) $valProd - (float) $data["valor_redrocket"] - (float) @$data["valor_frete"];

            /** 
             * CALCULAR O VALOR DOS JUROS
             */
            $val = $juros / 100;
            if ($val <= $data["valor_produto"]) {
                $data["valor_juros"] = 0;
            } else {
                $data["valor_juros"] = ((float) $juros / 100) - (float) @$data["valor_frete"] - (float) $data["valor_produto"] - (float) @$total_ord_bump;
            }

            if (@$plano->assumir_juros) {
                (float) $data["valor_liquido"] -= (float) $data["valor_juros"];
            }

            $data["vencimento"] = date("Y-m-d", strtotime("+1 day"));
            // SPLIT DE PAGAMENTOS
            if (@$data["valor_frete"]) {
                $data["tx_frete"] = (float) @$data["valor_frete"] - ((float) @$data["valor_frete"] * (float) $data["taxa_redrocket"] / 100);
            }
            $data = $this->splitPagamento($data);
            // var_dump($data);

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

            // var_dump($data);
            // exit;
            

            $data["forma_pagamento"] = 2;

            $data["boleto"] = @$boleto;
            if (@$boleto)
                $val =  ((float) $data["preco"] + (float) @$data["frete"]) - (float) $data["valor_desconto"] + (float) @$total_ord_bump;
            else
                $val =  (float) $data["preco"] + (float) @$data["frete"] + (float) @$total_ord_bump;

            $preco_prod = $data["preco"];

            $preco = explode(".", $val);
            $data["preco"] = $val * 100;

            $data["valor_bruto"] = (float) $data["preco"] / 100;
            $data["valor_juros"] = 0.00;
            $data["valor_frete"] = @$data["frete"];
            $data["taxa_juros"] = 0;

            $valProd = (float) $preco_prod + (float) @$data["frete"];
            $desconto = @$data["valor_desconto"] ? @$data["valor_desconto"] : 0;
            $data["valor_redrocket"] = number_format((((float) $valProd - (float) @$desconto + (float) @$total_ord_bump) * ($data["taxa_redrocket"] / 100)) + 1, 2);
            $data["valor_liquido"] = (float) $valProd - (float) $data["valor_redrocket"] - (float) @$data["valor_frete"] + (float) @$total_ord_bump;

            $data["parcela"] = 1;

            // SPLIT DE PAGAMENTOS
            if (@$data["valor_frete"]) {
                $data["tx_frete"] = (float) @$data["valor_frete"] - ((float) @$data["valor_frete"] * (float) $data["taxa_redrocket"] / 100);
            }
            $data = $this->splitPagamento($data);
            // var_dump($data);
            // exit;

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
                } else {
                    $data["vencimento"] = $data["vencimento"];
                }
            }
            // $cookies = (new Cookies())->find("produto = {$data["produto"]} AND ip = '$ip'")->fetch();
            // if (@$cookies->afiliado) {
            //     if ($cookies->tipo_comissao == 0) {
            //         $comissao = $data["valor_liquido"] * ($cookies->comissao / 100);
            //     } else if ($cookies->tipo_comissao == 1) {
            //         $comissao = $cookies->comissao;
            //     }
            //     $data["comissao_afiliado"] = number_format($comissao, 2);
            // }
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

        $status_transacaoP = array(
            "pending" => 1,
            "paid" => 4,
            "canceled" => 5,
            "failed" => 5,
        );

        $status_transacaoM = array(
            "pending" => 1,
            "approved" => 4,
            "cancelled" => 5,
            "rejected" => 5,
        );

        $status_stripe = array(
            "pending" => 1,
            "succeeded" => 4,
            "failed" => 5,
        );

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
            } else {
                $data["vencimento"] = $data["vencimento"];
            }
        }

        $cobrancaAsaas = new AsaasCobrancas();

        if ($data["forma_pagamento"] == 1) {
            $pagarme = new PagarMe;
            //$pagarme = new Stripe;
            $transacao = $pagarme->compraCartao($data);
            
            // $stripe = new Stripe;
            // $transacao = $stripe->compraCartao($data);

            var_dump($transacao);
            error_log("resposta pagarme cartão". json_encode($transacao));
            $data["transacao"] = @$transacao->id;
            $data["status_transacao"] = @$status_transacaoP[@$transacao->status];
            // $data["status_transacao"] = @$status_stripe[@$transacao->status];
            // $data["meio_pagamento"] = "Stripe";
            $data["meio_pagamento"] = "PagarMe";

            // if ($data["status_transacao"] == 5) {
            //     $data["descricao"] = $transacao->Payment->ReturnMessage;
            // }

            // $cielo = new Cielo();
            // $transacao = $cielo->compraCartao($data);

            // // var_dump($transacao);
            // $data["transacao"] = @$transacao->Payment->Tid;
            // $data["nit_pix"] = @$transacao->Payment->PaymentId;
            // $data["status_transacao"] = @$status_transacaoC[@$transacao->Payment->Status];
            // $data["meio_pagamento"] = "Cielo";

            // if ($data["status_transacao"] == 5) {
            //     $data["descricao"] = $transacao->Payment->ReturnMessage;
            // }
            // if ($data["status_transacao"] == 5) {
            //     $transacao = $cobrancaAsaas->cobrancaCartaoCredito($data);

            //     $data["transacao"] = @$transacao->id;
            //     $data["nit_pix"] = @$transacao->installment;

            //     if (!@$data["transacao"]) {
            //         $data["status_transacao"] = 5;
            //         $data["descricao"] = "Verifique os dados do cartão e se possui limites";
            //     } else {
            //         $data["status_transacao"] = @$status_transacao[@$transacao->status];
            //     }
            //     $data["meio_pagamento"] = "Asaas";
            // }

            if ($data["status_transacao"] == 4) {
                $data["data_finalizada"] = date("Y-m-d H:i:s");
            }

            //ANTIGO
            // if ($data["status_transacao"] == 4) {
            //     $data["data_finalizada"] = date("Y-m-d H:i:s");
            // }

            if($data["status_transacao"] == 5 || $transacao->status == "rejected"){
                $data["descricao"] = "O pagamento foi rejeitado";
                //$data["descricao"] = $transacao->charges[0]->last_transaction->acquirer_message;
            }

            // ANTIGO
            //if($data["status_transacao"] == 5 || $transacao->status == "failed"){
            //    $data["descricao"] = $transacao->charges[0]->last_transaction->acquirer_message;
            //}
        } else if ($data["forma_pagamento"] == 2) {


            // $transacao = $cobrancaAsaas->cobrancaBoleto($data);
            //$pagarme = new PagarMe;
            $pagarme = new MercadoPago;
            $transacao = $pagarme->compraBoleto($data);
            error_log(json_encode($transacao, true));
            // var_dump($transacao);
            // exit;

            $data["transacao"] = @$transacao->id;
            $data["status_transacao"] = @$status_transacaoM[@$transacao->status];
            $data["meio_pagamento"] = "MercadoPago";
            $data["link_boleto"] = @$transacao->transaction_details->external_resource_url;
            //$data["link_boleto"] = @$transacao->charges[0]->last_transaction->pdf;

            $data["linha_digitavel"] = @$transacao->transaction_details->digitable_line;
            //$data["linha_digitavel"] = @$transacao->charges[0]->last_transaction->line;
        }

        // MOSTRAR NA TELA OS ERROS DA WIRECARD
        //if (@$transacao->errors) {
            // var_dump($transacao->errors);
            // @$dataEmail["erros"]        .= "Houve um erro ao efetuar a transação. Por favor, entre em contato com o suporte. et: 4 \n";
            // $dataEmail["assunto"]        = "Erro no cadastro de transação wirecard";
            // $dataEmail["mensagem"]       = "Erro no cadastro de transação wirecard <br>";
            // $dataEmail["mensagem"]      .= "Protocolo: " . $data["protocolo"] . " <br>";

            // $dataEmail["mensagem"]      .= @$transacao->errors->description . "<br>";
            // $dataEmail["mensagem"]      .= "Dados enviados: <br><br>";

            // foreach ($data as $key => $value) {
            //     if ($key == "numcart" || $key == "cvv") {
            //         $value = "-";
            //     }
            //     $dataEmail["mensagem"]  .= "$key: $value<br>";
            // }

            // foreach ($transacao->errors as $erros) {
            //     $dataEmail["mensagem"]  .= "Descrição: $erros->description<br>";
            //     @$data["descricao"] .= $erros->description;
            //     $data["status_transacao"] = 5;
            //     $_SESSION["erros"]["transacao"] = @$erros->description;
            // }

            //$dataEmail["email"]          = "dev.ti@redrocketpay.com";
            //$enviaEmail = (new Emails())->enviaSendGrid($dataEmail);
        //}

        if (@$data["venda"]) {
            $atualizaVenda = $this->atualizaVenda($data);
        } else {
            $data['cookie'] = 1698199547;
            $cadastroVenda = $this->cadastroVenda($data);

            $data["msg"] = $cadastroVenda["msg"];
            $data["venda"] = $cadastroVenda["venda"];

            $produto = (new Produto())->findById($data["produto"]);
            $venda = (new Venda())->findById($data["venda"]);
            if ($produto->tipo_cobranca == 2 && $venda->status_transacao != 5) {
                $assinatura = new AssinaturaCadastro();
                $assinatura->venda = $venda->id;
                $assinatura->status = 1;
                if ($assinatura->save()) {
                    $data["assinatura"] = "Assinatura cadastrada";
                    
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
                    if (@$data["plano"]) $plano = (new Plano())->findById($data["plano"]);
                    $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];

                    if ($plano->maximo_recorrencia > 0)
                        $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                    $proxima = @$periodicidade[$plano->periodo][0];
                    $data["proxima_cobranca"] = date('Y-m-d', strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));
                    $data["valor_plano"] = $plano->preco;
                    $data["assinatura"] = (new AppAssinaturas(null))->cadastroAssinaturas($data);

                } else {
                    $data["assinatura"] = "Erro ao cadastrar";
                }
            }
        }
        // echo "<!-- Antes de entrar MEIO PAGAMENTO e status transacao != 4 -->";
        // var_dump($data["status_transacao"]);
        // var_dump($data["meio_pagamento"]);

        // if ($data["meio_pagamento"] == "Cielo" && $data["status_transacao"] != 4) {
        //     // echo "<!-- Meio pagamento Cielo e status transacao != 4 -->";
        //     // var_dump($data);
        //     $buscaCielo = $cielo->buscarPedido(@$data["nit_pix"]);
        //     $venda = (new Venda())->findById($data["venda"]);

        //     $venda->status_transacao = @$status_transacaoC[@$buscaCielo->Payment->Status];

        //     if ($venda->status_transacao == 4)
        //         $venda->data_finalizada = date("Y-m-d H:i:s");

        //     if ($venda->status_transacao == 5)
        //         $venda->descricao = $buscaCielo->Payment->ReturnMessage;
        //     // var_dump($venda);
        //     $venda->save();
        // }

        $_SESSION["pagamento"] = $data;
        (new AppProcessos(null))->processos($data);
        echo json_encode($data);
    }

    public function gerarNovoBoleto2($data)
    {
        var_dump($data);
    }

    public function gerarNovoBoleto($data)
    {
        //var_dump($data);
        if (@$data["email"] == "") {
            $data["email"] = "sac@octa.life";
        }

        // $wirecard = new Wirecard(null);

        $cliente = (new AsaasClientes())->novoCliente($data);
        $data["customer_id"] = @$cliente->id;

        $val =  @$data["valor_venda"];
        $data["preco"] = @$val * 100;

        $data["data_ultimo_evento"] = date("Y-m-d H:i:s");

        // $pedido = $wirecard->pedido($data);
        // $data["order_id"] = @$pedido->id;

        $status_transacao = array(
            "RECEIVED" => 4,
            "PENDING" => 1,
            "AWAITING_RISK_ANALYSIS" => 2,
            "RECEIVED_IN_CASH" => 4,
            "CONFIRMED" => 4,
            "" => 5,
        );

        $dt = date("Y-m-d", strtotime("+6 days", strtotime(date("Y-m-d"))));

        if (strtotime($dt) > strtotime(@$data["vencimento"])) {
            $maior = 1;
        } else {
            $maior = 0;
        }

        $transacao = (new AsaasCobrancas())->cobrancaBoleto($data);
        $data["transacao"] = @$transacao->id;
        $data["nit_pix"] = @$transacao->installment;
        $data["status_transacao"] = @$status_transacao[@$transacao->status];
        $data["meio_pagamento"] = "Asaas";
        $data["link_boleto"] = @$transacao->bankSlipUrl;

        $linha_digitavel = (new AsaasCobrancas())->codigoBoleto($transacao->id);
        $data["linha_digitavel"] = @$linha_digitavel->barCode;

        $data["forma_pagamento"] = 2;

        $boleto = (new Emails())->novoBoleto($data);
        $enviaEmail = (new Emails())->enviaSendGrid($boleto);

        if (@$data["vencimento"] == "") {
            if (date("D") != "Sun" || date("D") != "Mon" || date("D") != "Tue") {
                $expiration_date = date("Y-m-d", strtotime("+5 days"));
            } else if (date("D") == "Sat") {
                $expiration_date = date("Y-m-d", strtotime("+4 days"));
            } else {
                $expiration_date = date("Y-m-d", strtotime("+3 days"));
            }
        } else {
            $expiration_date = @$data["vencimento"];
        }

        $venda = (new Venda())->findById($data["id"]);
        $venda->transacao = $data["transacao"];
        $venda->link_boleto = $data["link_boleto"];
        $venda->linha_digitavel = $data["linha_digitavel"];
        $venda->data_vencimento = $expiration_date;

        if ($venda->save()) {
            $data["msg"] = "Salvo com sucesso";
        } else {
            $data["msg"] = "Ocorreu um problema na sua solicitação. Tente novamente mais tarde";
            $data["erro"] = $venda->fail()->getMessage();
            $this->logs($data, "novo-boleto");
        }

        //var_dump($venda);
        echo json_encode($data);
    }

    public function transacaoPix2($data)
    {
        var_dump($this->endpoint_pix, $this->token_pix, $this->key_pix);
    }

    public function transacaoPixAss($data)
    {
        if ($data["email"] == "") {
            $data["email"] = $data["email_suporte"];
        }

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

        if (substr($telefone, 0, 2) == "55") {
            $data["ddd"] = substr($telefone, 2, 2);
            $data["telefone"] = substr($telefone, 4, 12);
        } else {
            $data["ddd"] = substr($telefone, 0, 2);
            $data["telefone"] = substr($telefone, 2, 12);
        }

        $data["telefone"] = substr($telefone, 2, 12);

        $data["tipo_doc"] = 1;
        $data["pais"] = "BR";

        $resultado_bytes = random_bytes(5);
        $data["protocolo"] = strtoupper(bin2hex($resultado_bytes));

        if( ! empty( $_COOKIE['url'] ) ) {
            $data['cookie_url'] = $_COOKIE['url'];
        }
        $this->logs($data, "dados-enviados");

        /** 
         * VERIFICA SE CLIENTE TEM CADASTRO
         */
        $usuario = (new UsuarioDados());
        $cadastrado = $usuario->verificaCpf(["cpf" => $data["cpf"]]);

        if ($cadastrado) {
            $data["id"] = $cadastrado;

            $user = (new Usuario())->find("usuario_dados = $cadastrado")->fetch();
            $data["usuario"] = $user ? $user->id : null;;
            $data["comprador"] = $cadastrado;
        } else {
            $comprador = (new AppUsuarios(null))->cadastroUsuarioDados($data);
            $data["comprador"] = $comprador["id"];

            $user = (new Usuario())->find("usuario_dados = {$comprador['id']}")->fetch();
            $data["usuario"] = $user->id;
        }

        /** 
         * CADASTRAR UM ENDEREÇO
         */
        $endereco = (new AppUsuarios(null))->cadastroEndereco($data);
        $data["endereco"] = $endereco;

        if (@$data["meio_pagamento_desconto"]) {
            $meio_pagamento_desconto = explode(",", $data["meio_pagamento_desconto"]);
            foreach ($meio_pagamento_desconto as $pagamento) {
                if ($pagamento == 3 || $pagamento == 10) {
                    $pix = true;
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

        } else {
            $total_ord_bump = 0;
        }
        // Fim Código e preço Order Bump

        if (@$pix && @$data["valor_desconto"] > 0)
            $val =  ((float) $data["preco"] + (float) @$data["frete"]) - (float) @$data["valor_desconto"] + (float) @$total_ord_bump;
        else
            $val =  ((float) $data["preco"] + (float) @$data["frete"] + (float) @$total_ord_bump);

        $preco = explode(".", $val);

        $preco_prod = $data["preco"];

        $data["preco"] = (float) $val * 100;

        $data["valor_bruto"] = (float) $data["preco"] / 100;
        $data["valor_juros"] = 0.00;
        $data["valor_frete"] = @$data["frete"];
        $data["taxa_juros"] = 0;

        $valProd = (float) $preco_prod + (float) @$data["frete"];
        $data["valor_redrocket"] = number_format((((float) $valProd - (float) @$data["valor_desconto"] + (float) @$total_ord_bump) * ((float) $data["taxa_redrocket"] / 100)) + 1, 2);
        $data["valor_liquido"] = (float) $valProd - (float) $data["valor_redrocket"] - (float) @$data["valor_frete"] + (float) @$total_ord_bump;

        $data["valor_desconto"] = @$data["valor_desconto"];

        $data["parcela"] = 1;
        $data["data_ultimo_evento"] = date("Y-m-d H:i:s");

        $data["status_transacao"] = 1;
        $data["forma_pagamento"] = 3;

        $ip = (string) $_SERVER['REMOTE_ADDR'];

        // SPLIT DE PAGAMENTOS
        if (@$data["valor_frete"]) {
            $data["tx_frete"] = (float) @$data["valor_frete"] - ((float) @$data["valor_frete"] * (float) $data["taxa_redrocket"] / 100);
        }
        $data = $this->splitPagamento($data);
        
        if (@$data["venda"]) {
            $venda = $this->atualizaVenda($data);
        } else {
            $venda = $this->cadastroVenda($data);
        }

        $data["venda"] = @$venda["venda"];
        $dados["venda"] = @$venda["venda"];
        $dados["postback"] = @$venda["postback"];

        $produto = (new Produto())->findById($data["produto"]);
        $vendaAss = (new Venda())->findById($data["venda"]);
        if ($produto->tipo_cobranca == 2) {
            $assinatura = new AssinaturaCadastro();
            $assinatura->venda = $vendaAss->id;
            $assinatura->status = 1;
            if ($assinatura->save()) {
                $data["assinatura"] = "Assinatura cadastrada";
                
                $periodicidade = array(
                    "Semanal"       => [7, "days"],
                    "Mensal"        => [1, "months"],
                    "Bimestral"     => [2, "months"],
                    "Trimestral"    => [3, "months"],
                    "Semestral"     => [6, "months"],
                    "Anual"         => [1, "years"],
                    "Trienal"       => [3, "years"]
                );

                
                if (@$data["plano"]) $plano = (new Plano())->findById($data["plano"]);
                $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];
                $data["recorrencia"] = $plano->periodo;

                if ($plano->maximo_recorrencia > 0)
                    $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                $proxima = @$periodicidade[$plano->periodo][0];
                if (!empty($data["gerar_cobranca"])) {
                    // Gera a cobrança: calcula a próxima cobrança automaticamente com base no plano
                    $data["proxima_cobranca"] = date('Y-m-d',strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d')))
                    );
                } else {
                    // Não gera cobrança: usa a data informada manualmente no input
                    $data["proxima_cobranca"] = $data["proxima_cobranca_input"] ?? date('Y-m-d');
                }
                $data["valor_plano"] = $plano->preco;
                $data["assinatura"] = (new AppAssinaturas(null))->cadastroAssinaturas($data);

            } else {
                $data["assinatura"] = "Erro ao cadastrar";
            }
        }
        $_SESSION["pagamento"] = $data;
        
        if (!empty($data["gerar_cobranca"])) {
            $transacao = (new MercadoPago)->compraPix($data);
        
            $dados["qr_code"] = @$transacao->point_of_interaction->transaction_data->qr_code;
        
            $dados["meio_pagamento"] = "PagarMe";
        
            $venda1 = @(new Venda())->findById(@$venda["venda"]);
            $venda1->qr_code = @$dados["qr_code"];
            $venda1->transacao = @$transacao->id;
            $venda1->status_transacao = 1;
            $venda1->meio_pagamento = @$dados["meio_pagamento"];
        
            if ($venda1->save()) {
                // VERIFICA SE A VENDA SERÁ ENVIADA PARA O TELEVENDAS
                $venda_prod = (new Venda())->findById(@$data["venda"]);
                $produto = (new Produto())->findById(@$venda_prod->produto);
                if (@$produto && @$produto->habilitar_televendas == 1) {
                    $dataVenda["venda"] = $venda_prod->id;
                    $this->enviarTelevendas(@$dataVenda);
                }
                $dados["msg_pix"] = "Venda atualizada";
        
                $produto = (new Produto())->findById($data["produto"]);
                if ($produto->tipo_cobranca == 2 && $venda1->status_transacao != 5) {
                    $assinatura = new AssinaturaCadastro();
                    $assinatura->venda = $venda1->id;
                    $assinatura->status = 1;
                    if ($assinatura->save()) {
                        $dados["assinatura"] = "Assinatura cadastrada";
                    } else {
                        $dados["assinatura"] = "Erro ao cadastrar";
                    }
                }
            } else {
                $dados["msg_pix"] = $venda1->fail()->getMessage();
            }
        } else {
            $dados["msg_pix"] = "Assinatura criada sem cobrança.";
        }
        

        $dados["msg"] = @$venda["msg"];
        $dados["whatsapp"] = @$venda["whatsapp"];
        $dados["coprodutores"] = @$venda["coprodutores"];
        $dados["status_transacao"] = @$data["status_transacao"];
        $dados["url"] = url("checkout/obrigado");

        $_SESSION["pagamento"] = @$data;
        
        echo json_encode($dados);
    }

    public function transacaoPix($data)
    {



        if ($data["email"] == "") {
            $data["email"] = $data["email_suporte"];
        }

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

        if (substr($telefone, 0, 2) == "55") {
            $data["ddd"] = substr($telefone, 2, 2);
            $data["telefone"] = substr($telefone, 4, 12);
        } else {
            $data["ddd"] = substr($telefone, 0, 2);
            $data["telefone"] = substr($telefone, 2, 12);
        }

        $data["telefone"] = substr($telefone, 2, 12);

        $data["tipo_doc"] = 1;
        $data["pais"] = "BR";

        $resultado_bytes = random_bytes(5);
        $data["protocolo"] = strtoupper(bin2hex($resultado_bytes));

        if( ! empty( $_COOKIE['url'] ) ) {
            $data['cookie_url'] = $_COOKIE['url'];
        }
        $this->logs($data, "dados-enviados");

        /** 
         * VERIFICA SE CLIENTE TEM CADASTRO
         */
        $usuario = (new UsuarioDados());
        $cadastrado = $usuario->verificaCpf(["cpf" => $data["cpf"]]);

        if ($cadastrado) {
            $data["id"] = $cadastrado;

            $user = (new Usuario())->find("usuario_dados = $cadastrado")->fetch();
            $data["usuario"] = $user ? $user->id : null;;
            $data["comprador"] = $cadastrado;
        } else {
            $comprador = (new AppUsuarios(null))->cadastroUsuarioDados($data);
            $data["comprador"] = $comprador["id"];

            $user = (new Usuario())->find("usuario_dados = {$comprador['id']}")->fetch();
            $data["usuario"] = $user->id;
        }

        /** 
         * CADASTRAR UM ENDEREÇO
         */
        $endereco = (new AppUsuarios(null))->cadastroEndereco($data);
        $data["endereco"] = $endereco;

        if (@$data["meio_pagamento_desconto"]) {
            $meio_pagamento_desconto = explode(",", $data["meio_pagamento_desconto"]);
            foreach ($meio_pagamento_desconto as $pagamento) {
                if ($pagamento == 3 || $pagamento == 10) {
                    $pix = true;
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
            // var_dump($total_ord_bump);
        } else {
            $total_ord_bump = 0;
        }
        // Fim Código e preço Order Bump

        if (@$pix && @$data["valor_desconto"] > 0)
            $val =  ((float) $data["preco"] + (float) @$data["frete"]) - (float) @$data["valor_desconto"] + (float) @$total_ord_bump;
        else
            $val =  ((float) $data["preco"] + (float) @$data["frete"] + (float) @$total_ord_bump);

        $preco = explode(".", $val);

        $preco_prod = $data["preco"];

        $data["preco"] = (float) $val * 100;

        $data["valor_bruto"] = (float) $data["preco"] / 100;
        $data["valor_juros"] = 0.00;
        $data["valor_frete"] = @$data["frete"];
        $data["taxa_juros"] = 0;

        $valProd = (float) $preco_prod + (float) @$data["frete"];
        $data["valor_redrocket"] = number_format((((float) $valProd - (float) @$data["valor_desconto"] + (float) @$total_ord_bump) * ((float) $data["taxa_redrocket"] / 100)) + 1, 2);
        $data["valor_liquido"] = (float) $valProd - (float) $data["valor_redrocket"] - (float) @$data["valor_frete"] + (float) @$total_ord_bump;

        $data["valor_desconto"] = @$data["valor_desconto"];

        $data["parcela"] = 1;
        $data["data_ultimo_evento"] = date("Y-m-d H:i:s");

        $data["status_transacao"] = 1;
        $data["forma_pagamento"] = 3;

        $ip = (string) $_SERVER['REMOTE_ADDR'];

        // SPLIT DE PAGAMENTOS
        if (@$data["valor_frete"]) {
            $data["tx_frete"] = (float) @$data["valor_frete"] - ((float) @$data["valor_frete"] * (float) $data["taxa_redrocket"] / 100);
        }
        $data = $this->splitPagamento($data);
        
        if (@$data["venda"]) {
            $venda = $this->atualizaVenda($data);
        } else {
            $venda = $this->cadastroVenda($data);
        }

        $data["venda"] = @$venda["venda"];
        $dados["venda"] = @$venda["venda"];
        $dados["postback"] = @$venda["postback"];
        $_SESSION["pagamento"] = $data;
        // echo $venda;

        /**
         * TENTAR FAZER A TRANSAÇÃO PELO BRADESCO
         * VERIFICA SE TEVE ERRO
         * CASO DÊ ERRO, TENTAR FAZER PELO SITEF E CONTINUA O PROCESSO
         * CASO DÊ CERTO, CONTINUA O PROCESSO
         */

        // PIX BRADESCO
        // $pedido = rand(10000, 99999) * rand(100, 9999);
        // $data["pedido"] = $pedido;
        // $bradesco = (new Bradesco())->pix($data);
        // if ($bradesco->status->codigo == "0") {
        //     $dados["qr_code"] = @$bradesco->pix->textoQrCode;
        //     $dados["meio_pagamento"] = "BradescoPix";
        //     // FIM PIX BRADESCO
        // } else {
        //     $data["mensagem"]       = $bradesco->status->codigo . "<br>";
        //     $data["mensagem"]      .= $bradesco->status->mensagem . "<br>";
        //     $data["mensagem"]      .= @$bradesco->status->detalhes . "<br><br>";
        //     $this->logs($data, "erro-bradesco");

        // TRANSAÇÃO PIX SITEF
        // $sitef = new Sitef();
        // $dadosPix = $sitef->dadosPix($data);
        // $efetivacao = $sitef->efetivacaoTransacao($dadosPix->payment->nit);
        // $dados["qr_code"] = @$efetivacao->payment->qr_code;
        // $dados["nit_pix"] = @$dadosPix->payment->nit;
        // $dados["meio_pagamento"] = "Pix";
        // FIM PIX SITEF
        // }

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
        //     } else {
        //         $data["vencimento"] = $data["vencimento"];
        //     }
        // }

        // $cobrancaAsaas = new AsaasCobrancas();
        // $clienteAsaas = new AsaasClientes();
        // $cliente = $clienteAsaas->novoCliente($data);
        // $data["customer_id"] = $cliente->id;
        // $transacao = $cobrancaAsaas->cobrancaPix($data);

        //$transacao = (new PagarMe)->compraPix($data);
        $transacao = (new BsPay)->compraPix($data);
        error_log(json_encode($transacao));
        //var_dump($transacao);
        //exit;

        // $qrCode = $cobrancaAsaas->qrCodePix($transacao->id);
        $dados["qr_code"] = @$transacao->qrcode;
        //$dados["qr_code"] = @$transacao->charges[0]->last_transaction->qr_code;
        $dados["meio_pagamento"] = "PagarMe";

        $venda1 = @(new Venda())->findById(@$venda["venda"]);
        $venda1->qr_code = @$dados["qr_code"];

        error_log(json_encode($venda1->data(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $venda1->transacao = @$transacao->transactionId;
        //$venda1->transacao = @$transacao->id;

        $venda1->status_transacao = 1;
        $venda1->meio_pagamento = @$dados["meio_pagamento"];
        
        if ($venda1->save()) {

            // VERIFICA SE A VENDA SERÁ ENVIADA PARA O TELEVENDAS
            $venda_prod = (new Venda())->findById(@$data["venda"]);
            $produto = (new Produto())->findById(@$venda_prod->produto);
            if (@$produto && @$produto->habilitar_televendas == 1) {
                $dataVenda["venda"] = $venda_prod->id;
                $this->enviarTelevendas(@$dataVenda);
                // var_dump(@$data);
                // exit;
            }
            $dados["msg_pix"] = "Venda atualizada";

            $produto = (new Produto())->findById($data["produto"]);
            if ($produto->tipo_cobranca == 2 && $venda1->status_transacao != 5) {
                $assinatura = new AssinaturaCadastro();
                $assinatura->venda = $venda1->id;
                $assinatura->status = 1;
                if ($assinatura->save()) {
                    $dados["assinatura"] = "Assinatura cadastrada";
                } else {
                    $dados["assinatura"] = "Erro ao cadastrar";
                }
            }
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

        $dados["msg"] = @$venda["msg"];
        $dados["whatsapp"] = @$venda["whatsapp"];
        $dados["coprodutores"] = @$venda["coprodutores"];
        $dados["status_transacao"] = @$data["status_transacao"];
        $dados["url"] = url("checkout/obrigado");

        $_SESSION["pagamento"] = @$data;
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
        $venda = (new Venda())->findById($data["venda"]);

        // $consulta = $this->curlPix($url, "GET", null);
        $consulta = (new AsaasCobrancas())->dadosCobranca($venda->transacao);

        $status = array(
            "RECEIVED" => 4,
            "PENDING" => 1,
            "AWAITING_RISK_ANALYSIS" => 2,
            "RECEIVED_IN_CASH" => 4,
            "CONFIRMED" => 4,
            "OVERDUE" => 5,
            "REFUNDED" => 6,
            "REFUND_REQUESTED" => 9,
            "CHARGEBACK_REQUESTED" => 9,
            "CHARGEBACK_DISPUTE" => 9,
            "AWAITING_CHARGEBACK_REVERSAL" => 7,
            "DUNNING_REQUESTED" => 6,
            "DUNNING_RECEIVED" => 4
        );

        // echo "<pre>";
        // var_dump($consulta);

        // echo @$consulta->payment->status;
        // echo "</pre>";

        if ($consulta && $venda->status_transacao != @$status[@$consulta->status]) {
            $venda->status_transacao = @$status[@$consulta->status];
            $venda->transacao = $consulta->id;
            $venda->data_ultimo_evento = date("Y-m-d H:i:s");
            $venda->data_finalizada = recuperaDataHora(@$consulta->paymentDate);

            $data["nome_produto"] = $venda->dataProduto()->nome;

            if (@$status[@$consulta->status] == 4) {
                $data["status_transacao"] = 4;
                (new AppProcessos(null))->processos($data);
            }

            $this->logs($data, "not");

            if ($venda->status_transacao == 5) {
                $venda->motivo = @$consulta->description ? @$consulta->description : "Código expirado";
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

    public function consultarPixAjax($data)
    {
        $vendaId = filter_input(INPUT_POST, "venda", FILTER_VALIDATE_INT);

        if (!$vendaId) {
            echo json_encode([
                "success" => false,
                "message" => "Venda inválida"
            ]);
            return;
        }

        $venda = (new Venda())->findById($vendaId);

        if (!$venda) {
            echo json_encode([
                "success" => false,
                "message" => "Venda não encontrada"
            ]);
            return;
        }

        $payload = [
            "venda" => $venda->id,
            "nit" => $venda->nit_pix,
            "produtor" => $venda->produtor
        ];

        $consulta = null;

        if ($venda->forma_pagamento == 3 && $venda->meio_pagamento == "Pix") {
            $consulta = $this->consultarPix($payload);
            $venda = (new Venda())->findById($vendaId);
        }

        $status = (int) $venda->status_transacao;

        $response = [
            "success" => true,
            "status" => $status,
            "pago" => $status == 4,
            "redirect" => url("checkout/finalizar-compra/") . $venda->id
        ];

        if (!empty($consulta["msg-status"])) {
            $response["mensagem"] = $consulta["msg-status"];
        }

        if ($status == 5 || $status == 6 || $status == 7) {
            $response["cancelado"] = true;
            $response["motivo"] = $venda->motivo;
        }

        echo json_encode($response);
    }

    public function atualizarPix()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];

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
        echo "<h1>Atualizar status pix</h1>";

        // $busca = @$inicio ? "created_at BETWEEN '$inicio 00:00:00' AND '$fim 23:59:59' AND " : "created_at BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 00:00:00")."' AND ";
        $busca = @$inicio ? "created_at BETWEEN '$inicio 00:00:00' AND '$fim 23:59:59' AND " : "";
        $vendas = (new Venda())->find($busca . "forma_pagamento = 3 AND meio_pagamento = 'Pix'")->fetch(true);

        if ($vendas) {
            foreach ($vendas as $venda) {
                echo "<b>Venda:</b> $venda->id<br>";

                if ($venda->nit_pix) {
                    $url = $this->endpoint_pix . "transactions/$venda->nit_pix";
                    $consulta = $this->curlPix($url, "GET", null);

                    echo "<b>Venda encontrada</b><br>" .
                        "<b>Venda:</b> $venda->id<br>";

                    $status = array(
                        "NOV" => 1,
                        "INV" => 5,
                        "PPC" => 1,
                        "PPN" => 5,
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

                    echo "<b>Status venda $venda->id:</b> " . @$consulta->payment->status . "<br>";

                    if ($venda->status_transacao != @$status[@$consulta->payment->status] && @$consulta->payment->status) {
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

                $start_date = new \DateTime($venda->created_at);
                $since_start = $start_date->diff(new \DateTime(date("Y-m-d H:i:s")));
                $minutos_totais = ($since_start->days * 24 * 60);

                if ($venda->status_transacao == 1 && $minutos_totais > 10) {
                    $venda->status_transacao = 5;
                    $venda->motivo = "Código expirado";
                    if ($venda->save()) {
                        echo "Status atualizado com sucesso";
                    } else {
                        echo $venda->fail()->getMessage();
                    }
                }

                echo "------------------------------------------------<br>";
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

    public function finalizarCompra($data)
    {

        session_start();

        // echo "<h1>SESSION</h1>";

        $idVenda = @$data["venda"] ? @$data["venda"] : @$_SESSION["pagamento"]["venda"];
        $cookieId =   @$data["cookie_id"] ? @$data["cookie_id"] : @$_SESSION["pagamento"]["cookie_id"];

        $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $url = url("checkout/finalizar-compra/");
        // var_dump($url_atual, $url);
        if ($url_atual == $url) {
            header("Location: " . $url . $idVenda);
        }

        if (@$idVenda)
            $venda = (new Venda())->findById(@$idVenda);
        //echo "<h1>VENDA</h1>";
        //var_dump($venda);
        // VERIFICA SE A VENDA SERÁ ENVIADA PARA O TELEVENDAS
        $produto = (new Produto())->findById($venda->produto);
        if (@$produto && @$produto->habilitar_televendas == 1) {
            $this->enviarTelevendas($data);
            // exit;       
        }

        $data["venda"] = $venda->id;
        $data["nit"] = $venda->nit_pix;

        if ($venda->forma_pagamento == 3 && $venda->meio_pagamento == "Pix") {
            $data["produtor"] = $venda->produtor;
            $consultarPix = $this->consultarPix($data);
            // var_dump($consultarPix);
        }

        if ($venda->forma_pagamento == 3 && $venda->meio_pagamento == "BradescoPix") {
            $bradescoPix = (new Bradesco())->consultaPixPedido($data);
        }

        $generator = new \Picqer\Barcode\BarcodeGeneratorHTML();

        if (@$_SESSION["pagamento"]["checkout1"] || $venda->produto) {
            $checkout1 = @$_SESSION["pagamento"]["checkout1"];
            $upsell = (new Upsell())->find("ativar = 1 AND (checkout = $checkout1 OR (checkout IS NULL AND produto = $venda->produto))")->fetch();
            // var_dump($upsell);

            if ($checkout1)
                $checkout = (new Checkout())->findById($checkout1);
            $taxa = (new Taxa())->findById(1);

            if ($upsell) {
                if (@$upsell->plano)
                    $coproducao = (new CoProdutor())->find("produto = {$upsell->dataPlano()->produto} AND aceito = 1")->fetch(true);
                else
                    $coproducao = (new CoProdutor())->find("produto = {$upsell->dataProduto()->produto} AND aceito = 1")->fetch(true);

                $i = 1;
                $comUser = $venda->dataProduto()->tipo_cobranca == 1 ? $upsell->dataUsuario()->taxa_redrocket : $upsell->dataUsuario()->taxa_assinatura;
                // echo $comUser;
                $valorOcta = number_format((@$upsell->dataPlano()->preco * ($comUser / 100)) + 1, 2);
                if (@$upsell->dataPlano()->habilitar_comissao_coprodutores) {
                    $cop = explode(",", $upsell->dataPlano()->comissao_coprodutores);

                    if ($cop) {
                        foreach ($cop as $cop1) {
                            $cop2 = explode("|", $cop1);
                            @$coprodutores .= "/$cop2[0]/";

                            if ($cop2[1] == 0) {
                                $comissao_cop = number_format(($upsell->dataPlano()->preco - $valorOcta) * ($cop2[2] / 100), 2);
                                @$comissao_coprodutor .= "/$comissao_cop/";
                            } else {
                                $comissao_cop = number_format($cop2[2], 2);
                                @$comissao_coprodutor .= "/$comissao_cop/";
                            }
                            @$somaCoprodutor += $comissao_cop;
                        }
                    }
                } elseif ($coproducao) {
                    // echo $valorOcta;
                    foreach ($coproducao as $coprodutor) {
                        @$coprodutores .= "/$coprodutor->co_produtor/";
                        if ($coprodutor->tipo_comissao == 0) {
                            $comissao_cop = number_format(($venda->valor_produto - $valorOcta) * ($coprodutor->comissao / 100), 2);
                            @$comissao_coprodutor .= "/$comissao_cop/";
                        } else {
                            $comissao_cop = number_format($coprodutor->comissao, 2);
                            @$comissao_coprodutor .= "/$comissao_cop/";
                        }
                        @$somaCoprodutor += $comissao_cop;
                        $i++;
                    }
                }
            }
        }
        // echo $somaCoprodutor;

        $ip = (string) $_SERVER['REMOTE_ADDR'];

        if( $cookieId ) {
            $cookies = (new Cookies())->find("id = $cookieId")->fetch();
        }else {
            $cookies = (new Cookies())->find("produto = $venda->produto AND ip = '$ip'")->fetch();
        }

        // var_dump($cookies);
        $planos = new Plano();

        if (@!$_COOKIE["analytics"]) {
            $numero_de_bytes = 4;
            $resultado_bytes = random_bytes($numero_de_bytes);
            $codigo_cookie = strtoupper(bin2hex($resultado_bytes));

            setcookie("analytics", $codigo_cookie, time() + (60 * 60 * 24 * 10000), "/");
        }

        $dados["url"]           = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $dados["url_anterior"]  = @$_SERVER['HTTP_REFERER'];
        $dados["navegador"]     = @$_COOKIE["analytics"];
        $dados["produto"]       = @$produto->id;
        $dados["plano"]         = @$checkout->plano;
        $dados["checkout"]      = @$checkout->id;
        $dados["afiliado"]      = @$cookies->afiliado;
        $dados["campanha"]      = @$cookies->campanha;
        $dados["produtor"]      = @$produto->usuario;
        $dados["venda"]         = @$venda->id;

        echo $this->view->render("finalizar-compra", [
            "title" => "Recebemos Seu Pedido",
            "venda" => $venda,
            "consultarPix" => @$consultarPix,
            "generator" => $generator,
            "upsell" => @$upsell,
            "checkout" => @$checkout,
            "pixel_facebook" => $checkout->pixel_facebook,
            "taxa" => @$taxa,
            "comUser" => @$comUser,
            "coprodutores" => @$coprodutores,
            "comissao_coprodutor" => @$comissao_coprodutor,
            "somaCoprodutor" => @$somaCoprodutor,
            "cookies" => @$cookies,
            "planos" => @$planos,
        ]);
    }

    public function recusado($data)
    {
        //$this->pedido($dados);
        session_start();
        echo $this->view->render("desaprovado", [
            "title" => "Compra não autorizada | " . SITE
        ]);
    }

    public function redirect($data)
    {
        session_start();
        //var_dump($_SESSION);
        //$this->pedido($dados);
        echo $this->view->render("redirect", [
            "title" => "Você Acabou De Realizar Um Pedido"
        ]);
    }

    public function cadastroVenda($data)
    {
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
        $venda->cookie                  = ! empty( @$data["cookie"] ) ? @$data["cookie"] : 0;
        $venda->endereco                = @$data["endereco"];
        $venda->ecommerce               = @$data["ecommerce"];

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
                $cartao_numero->comprador = $data["comprador"];
                $cartao_numero->usuario = $data["usuario"];
                $cartao_numero->venda = $venda->id;
                $cartao_numero->numero = base64_encode($data["numcart"]);
                $cartao_numero->ultimos_digitos = substr($data["numcart"], -4);
                $cartao_numero->save();

                $cartao = new Cartao();
                $cartao->comprador = $data["comprador"];
                $cartao->usuario = $data["usuario"];
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

        $this->logs($data, "logs-venda");

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

        $this->logs($data, "logs-venda");

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

        //$result = file_get_contents(url("notificacoes"), null, $context);
        //var_dump($result);
    }

    public function error(array $data)
    {
        echo $this->view->render("error", [
            "title" => "Erro {$data['errcode']} | " . SITE,
            "error" => $data["errcode"]
        ]);
    }

    public function enviarTelevendas(array $data)
    {
        $venda = (new Venda())->find("id = {$data["venda"]}")->fetch();
        $produtor = (new UsuarioDados())->find("usuario = $venda->produtor")->fetch();

        if (@$venda && @$produtor) {
            // CASO VENDA TENHA SIDO FEITA POR LINK DE CHECKOUT DO TELEVENDAS
            if (@$venda->atendente_televendas != "") {
                $data["status_atendimento"] = 1;
                $data["atendente_televendas"] = @$venda->dataCookieTelevendas()->atendente;
            } else {
                $data["status_atendimento"] = 0;
                $data["atendente_televendas"] = "";
            }
            // $data["status_atendimento"] = 0;
            $data["transacao"] = @$venda->transacao;
            $data["upsell"] = @$venda->upsell;
            $data["produto"] = @$venda->dataProduto()->codigo;
            $data["plano"] = @$venda->dataplano()->codigo;
            // Seção para cadastro de endereço e usuario
            $data["nome"] = @$venda->dataComprador()->nome;
            $data["nasc"] = @$venda->dataComprador()->data_nascimento;
            $data["email"] = @$venda->dataComprador()->email;
            $data["cpf"] = @$venda->dataComprador()->cpf_cnpj;
            $data["telefone"] = @$venda->dataComprador()->telefone;
            $data["data_nascimento"] = @$venda->dataComprador()->data_nascimento;
            $data["cep"] = @$venda->dataComprador()->cep;
            $data["rua"] = @$venda->dataComprador()->rua;
            $data["numero"] = @$venda->dataComprador()->numero;
            $data["complemento"] = @$venda->dataComprador()->complemento;
            $data["bairro"] = @$venda->dataComprador()->bairro;
            $data["cidade"] = @$venda->dataComprador()->cidade;
            $data["estado"] = @$venda->dataComprador()->estado;
            $data["pais"] = @$venda->dataComprador()->pais;
            // Fim Seção
            $data["comprador"] = @$venda->dataComprador()->nome;
            $data["comprador_cpf_cnpj"] = @$venda->dataComprador()->cpf_cnpj;
            $data["produtor"] = @$produtor->cpf_cnpj;
            $data["campanha"] = @$venda->campanha;
            // $data["afiliado"] = $venda->afiliado;
            $data["forma_pagamento"] = @$venda->forma_pagamento;
            $data["status_transacao"] = @$venda->status_transacao;
            $data["motivo"] = @$venda->motivo;
            $data["url_checkout"] = @$venda->url_checkout;
            $data["link_boleto"] = @$venda->link_boleto;
            $data["linha_digital"] = @$venda->linha_digitavel;
            $data["cupom"] = @$venda->cupom;
            $data["valor_bruto"] = @$venda->valor_bruto;
            $data["valor_produto"] = @$venda->valor_produto;
            $data["taxa_juros"] = @$venda->taxa_juros;
            $data["valor_frete"] = @$venda->valor_frete;
            $data["valor_desconto"] = @$venda->valor_desconto;
            $data["parcelas"] = @$venda->parcelas;
            $data["valor_frete"] = @$venda->valor_frete;
            $data["valor_frete"] = @$venda->valor_frete;
            $data["nome_afiliado"] = @$venda->dataAfiliado()->nome;
            $data["postback_st4rt"] = @$venda->postback_st4rt;
            $data["assinatura"] = @$venda->assinatura;
            // $data["carrinho_abandonado"] = @$venda->carrinho_abandonado;
            $data["ip"] = @$venda->ip;
            $data["aluno"] = @$venda->aluno;
            $data["observacao"] = @$venda->observacao;
            $data["data_pedido"] = @$venda->data_pedido;
            $data["data_vencimento"] = @$venda->data_vencimento;
            $data["data_ultimo_evento"] = @$venda->data_ultimo_evento;
            if (@$venda->data_finalizada != "") {
                $data["data_finalizada"] = @$venda->data_finalizada;
            } else {
                $data["data_finalizada"] = "";
            }

            $curl = curl_init();
            $url = url_televendas("televendas/transacao");

            curl_setopt_array($curl, array(
                CURLOPT_URL => "$url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => @$data
            ));

            $response = curl_exec($curl);
            // exit;
            curl_close($curl);
            // echo $response;

            // return $response;
        }
    }
}

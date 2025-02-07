<?php

namespace Source\App\Shopify;

use Source\Models\Usuarios\Usuario;
use Source\Models\Shopify\Shopify;
use Source\App\Sessao\AppSessao;
use Source\App\Sessao\Produtos;
use Source\App\Frete\AppFrete;
use League\Plates\Engine;
use Source\App\Produtos\AppProdutos;
use Source\Models\Produtos\Plano;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;
use Source\Models\Assinaturas\Assinatura;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\URL\Url;
use Source\Models\Cupons\Cupom;
use Source\Models\Notificacoes\Notificacao;
use Source\Models\Upsell\Upsell;
use Source\Models\Televendas\Televendas;
use Source\Models\Postback\Postback;
use Source\Models\Campanhas\Campanha;
use Source\Models\Checkout\Checkout;
use Source\Models\Permissao\Section;

class AppShopify
{
    private $section;

    public function __construct($router)
    {
        @session_start();

        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        //$this->sessao = (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
    }


    public function cadastrar($data)
    {
        @session_start();

        if (@$data["carrinho_shopify"])
            $carrinho = 1;
        else
            $carrinho = 0;

        $this->sessao = (new AppSessao())->verificaSeLogado();
        // var_dump(@$data);
        // exit;
        $shopify = new Shopify();

        $shopify->usuario = $this->section->getId();
        $shopify->url = $data["url_shopify"];
        $shopify->chave_api = $data["api_shopify"];
        $shopify->senha = $data["senha_shopify"];
        $shopify->segredo = $data["segredo_shopify"];
        $shopify->carrinho = $carrinho;

        // var_dump($shopify);
        // exit;

        if ($shopify->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $shopify->fail()->getMessage();
        }
    }

    public function editar($data)
    {
        @session_start();

        $shopify = (new Shopify())->findById($data["id_shopify"]);

        if (@$data["carrinho_shopify"])
            $carrinho = 1;
        else
            $carrinho = 0;

        $shopify->usuario = $this->section->getId();
        $shopify->url = $data["url_shopify"];
        $shopify->chave_api = $data["api_shopify"];
        $shopify->senha = $data["senha_shopify"];
        $shopify->segredo = $data["segredo_shopify"];
        $shopify->carrinho = $carrinho;


        if ($shopify->save()) {
            // echo "Salvo com sucesso";
            $this->importarProdutos();
        } else {
            echo $shopify->fail()->getMessage();
        }
    }

    // Webhook para atualizar os produtos
    public function webhookAtualiza($data)
    {
        // var_dump(@$data);
        // exit;
        if (@$data["id"]) {
            $produto = (new Produto())->find("codigo = " . @$data["id"])->fetch();
            if (@$produto) {
                $shopify = (new Shopify())->find("usuario = " . @$produto->usuario)->fetch();
                if (@$shopify)
                    $this->importarProdutos(@$shopify->usuario);
            }
        }
    }

    // Webhook para excluir os produtos
    public function webhookExcluir($data)
    {
        if (@$data["id"]) {
            $produto = (new Produto())->find("codigo = " . @$data["id"])->fetch();
            if (@$produto) {
                $data["produto"] = @$produto->id;
                $this->excluirProduto($data);
            }
        }
    }

    public function importarProdutos($data = null)
    {
        // var_dump($data);
        if (@$data && @$data != "") {
            $id_usuario = @$data;
            $shopify = (new Shopify())->find("usuario = " . @$id_usuario)->fetch();
        } else {
            $shopify = (new Shopify())->find("usuario = " . @$this->section->getId())->fetch();
        }

        if (@$shopify) {
            $url = "$shopify->url" . "/admin/api/2021-10/products.json";
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'X-Shopify-Access-Token:' . $shopify->senha,
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $resposta = json_decode($response);

            foreach ($resposta->products as $resposta) {
                $id_produto = $resposta->images[0]->product_id;
                $produto = (new Produto())->find("codigo = " . @$id_produto)->fetch();
                if (@$produto) {
                    $produto->nome = $resposta->title;
                    $produto->preco = $resposta->variants[0]->price;
                    $produto->rascunho = 0;
                    $produto->descricao = $resposta->body_html;

                    if ($resposta->status == "active")
                        $produto->status = 2;
                    else
                        $produto->status = 0;

                    $produto->foto = $resposta->images[0]->src;
                    if ($produto->save()) {
                        $this->importarPlanos($produto->id);
                    } else
                        echo "Erro ao salvar produto";
                } else {
                    // var_dump($resposta);
                    // exit;
                    // var_dump($resposta->title);
                    // var_dump($resposta->body_html);
                    // var_dump($resposta->variants[0]->price);
                    // var_dump($resposta->images[0]->src);
                    // var_dump($resposta->images[0]->product_id);
                    // var_dump($resposta->status);
                    $data["nome"] = @$resposta->title;
                    $data["codigo_shopify"] = @$resposta->images[0]->product_id;
                    $data["preco"] = @$resposta->variants[0]->price;
                    $data["rascunho"] = 0;
                    $data["descricao"] = @$resposta->body_html;
                    $data["tipo_produto"] = 8;
                    $data["categoria"] = 18;
                    $data["tipo_cobrança"] = 1;

                    if ($resposta->status == "active") {
                        $data["disponivel"] = 1;
                        $data["status"] = 2;
                    } else {
                        $data["disponivel"] = 0;
                        $data["status"] = 0;
                    }

                    $data["shopify"] = 1;
                    $data["foto"] = @$resposta->images[0]->src;
                    $data["url_midias"] = @$shopify->url;

                    $prod_shopify = (new AppProdutos(null))->cadastro($data);

                    $this->importarPlanos(@$prod_shopify);
                }
            }
            // if ($resposta->code == 201) {
            //     return 1;
            // } else {
            //     return 0;
            // }
        }
    }

    public function importarPlanos($data)
    {
        $produto = $data;
        // var_dump($data);
        // exit;
        // $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $produto = (new Produto())->findById(@$produto);
        // echo $produto->id;
        $shopify = (new Shopify())->find("usuario = " . @$produto->usuario)->fetch();
        if (@$shopify) {
            $url = "$shopify->url" . "/admin/api/2021-10/products.json";
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'X-Shopify-Access-Token:' . $shopify->senha,
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $resposta = json_decode($response);
            echo "<pre>";
            // echo $produto->id;
            // var_dump($resposta);
            // exit;
            if ($resposta) {
                // PERCORRE O ARRAY DE PRODUTOS
                foreach ($resposta->products as $products) {
                    // PERCORRE AS VARIANTES (PLANOS) DE CADA PRODUTO
                    foreach ($products->variants as $variants) {
                        // GUARDA O ID DO PRODUTO NA VARIAVEL PRODUCT_ID
                        $product_id = $variants->product_id;
                        // VERIFICA SE O ID DO PRODUTO DA SHOPIFY É IGUAL AO CÓDIGO DO PRODUTO CRIADO NA redrocket
                        // VERIFICA SE O PRODUTO POSSUI MAIS DE 1 VARIANTE (OU SEJA, SE EXISTE PLANO) -- ** VARIANTS->POSITION > 1 **
                        // **CASO A VARIANTE TENHA APENAS 1 POSIÇÃO (POSITION = 1) NÃO TEM VARIANTE (OU SEJA, NÃO EXISTE PLANO PARA AQUELE PRODUTO).
                        // VERIFICA SE O STATUS DO PRODUTO É IGUAL A 2 (ATIVO) NA redrocket PARA CADASTRAR OS PLANOS DAQUELE PRODUTO

                        if ($product_id == $produto->codigo && $variants->position > 1 && $produto->status == 2) {
                            $plano = (new Plano())->find("codigo = " . @$variants->id)->fetch();
                            // echo "!--VARIANTE ID: $variants->id && PLANO CODIGO: $plano->codigo";
                            if (@$plano) {
                                echo "encontrou plano";
                                // echo $product_id;
                                // echo $produto->codigo;
                                // echo $variants->position;
                                // echo $produto->status;
                                // TITULO DA VARIANTE (PLANO) QUE VEIO DA SHOPIFY
                                $plano->nome = @$variants->title;
                                // PRECO DA VARIANTE (PLANO) QUE VEIO DA SHOPIFY
                                $plano->preco = @$variants->price;
                                // MOSTRAR QUE O PLANO QUE ESTÁ INDO PARA O CADASTRO É DE ORIGEM DA SHOPIFY
                                $dados["shopify"] = 1;

                                if ($plano->save())
                                    echo "Dados atualizados com sucesso!";
                                else
                                    echo "Erro ao atualizar PLANO: $plano->id";
                                // exit;
                            } else {
                                // var_dump($variants->title);
                                // var_dump($produto->id);
                                // var_dump($variants->price);
                                // var_dump($produto->foto);

                                // TITULO DA VARIANTE (PLANO) QUE VEIO DA SHOPIFY
                                $dados["nome"] = @$variants->title;
                                // ID DO PRODUTO CRIADO NA redrocket
                                $dados["produto"] = @$produto->id;
                                // ID DA VARIANTE (PLANO) QUE VEIO DA SHOPIFY
                                $dados["codigo"] = $variants->id;
                                // PRECO DA VARIANTE (PLANO) QUE VEIO DA SHOPIFY
                                $dados["preco"] = @$variants->price;
                                $dados["status"] = 2;
                                $dados["foto_atual"] = @$produto->foto;
                                $dados["privado"] = 0;
                                $dados["ativo"] = 1;

                                // MOSTRAR QUE O PLANO QUE ESTÁ INDO PARA O CADASTRO É DE ORIGEM DA SHOPIFY
                                $dados["shopify"] = 1;
                                $dados["usuario"] = @$produto->usuario;

                                // $plano = (new AppProdutos(null))->planoCadastro($dados);
                                $plano = $this->planoCadastro($dados);
                                // var_dump($plano);
                                // exit;
                                // echo "antes de entrar em plano";
                                if (@$plano) {
                                    echo "ENTROU PARA CRIAR CHECKOUT";
                                    $dados["plano"] = $plano;
                                    $this->cadastroCheckout($dados);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function cadastroCheckout($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        // $files = new File();
        // var_dump($data);
        // exit;
        $numero_de_bytes = 4;
        $resultado_bytes = random_bytes($numero_de_bytes);
        $codigo = strtoupper(bin2hex($resultado_bytes));

        $checkout = new Checkout();

        $checkout->codigo = $codigo;
        $checkout->descricao = @$data["nome"] . " - Shopify";
        $checkout->modelo_checkout = 3;
        $checkout->adquirente = "redrocket";
        $checkout->mostrar_preco = 1;

        $checkout->produto = @$data["produto"];
        $checkout->televendas = 0;
        $checkout->meio_pagamento = 10;

        $checkout->plano = @$data["plano"];
        $checkout->privado = 0;
        $checkout->usuario = @$data["usuario"];
        $checkout->usar_contador = 0;
        $checkout->usar_frete = 0;

        if ($checkout->save()) {
            $data["id_checkout"] = $checkout->id;
            $frete = (new AppFrete(null))->cadastroFrete($data);
            if (@$frete != 0)
                echo "Erro ao criar frete. Entre em contato com o suporte.";
            else
                echo "Checkout criado com sucesso!";
        }
    }

    public function planoCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if (@$data["preco"] < 5) {
            echo "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
            exit;
        }
        // $codigo = mt_rand(10000, 999999);

        $plano = new Plano();

        $plano->codigo = @$data["codigo"];
        $plano->nome = @$data["nome"];
        $plano->produto = @$data["produto"];
        $plano->quantidade = @$data["quantidade"];
        $plano->usuario = @$data["usuario"];
        $plano->preco = @$data["preco"];
        $plano->descricao = @$data["descricao"];
        $plano->privado = @$data["privado"] == "" ? 0 : 1;
        $plano->status = $data["status"];
        $plano->ativo = @$data["ativo"] == "" ? 0 : 1;
        $plano->shopify = @$data["shopify"];
        // $plano->televendas = @$data["televendas"];

        if ($plano->save()) {
            return @$plano->id;
        } else {
            echo $plano->fail()->getMessage();
        }
    }

    public function excluirProduto($data)
    {
        $vendas = (new Venda())->find("produto = " . $data["produto"])->fetch(true);
        $assinaturas = (new Assinatura())->find("produto = " . $data["produto"] . " AND status != 1")->fetch(true);

        $produto = (new Produto())->findById($data["produto"]);

        if ($vendas && !$assinaturas) {
            $produto->excluir = 1;
            $produto->status = 5;
            if ($produto->save()) {
                echo "Você não verá mais esse produto no seu painel de produtos";

                $afiliacao = (new Afiliacao())->find("produto = $produto->id")->fetch(true);
                if ($afiliacao) {
                    foreach ($afiliacao as $afiliacao) {
                        $afi = (new Afiliacao())->findById($afiliacao->id);
                        $afi->destroy();
                    }
                }

                $coproducao = (new CoProdutor())->find("produto = $produto->id")->fetch(true);
                if ($coproducao) {
                    foreach ($coproducao as $coproducao) {
                        $cop = (new CoProdutor())->findById($coproducao->id);
                        $cop->destroy();
                    }
                }

                $urls = (new Url())->find("produto = $produto->id")->fetch(true);
                if ($urls) {
                    foreach ($urls as $url) {
                        $ur = (new Url())->findById($url->id);
                        $ur->destroy();
                    }
                }

                $cupons = (new Cupom())->find("produto = $produto->id")->fetch(true);
                if ($cupons) {
                    foreach ($cupons as $cupom) {
                        $cup = (new Cupom())->findById($cupom->id);
                        $cup->destroy();
                    }
                }

                $upsell = (new Upsell())->find("produto = $produto->id")->fetch(true);
                if ($upsell) {
                    foreach ($upsell as $upsell) {
                        $ups = (new Upsell())->findById($upsell->id);
                        $ups->destroy();
                    }
                }

                $notificacoes = (new Notificacao())->find("produto = $produto->id")->fetch(true);
                if ($notificacoes) {
                    foreach ($notificacoes as $notificacao) {
                        $not = (new Notificacao())->findById($notificacao->id);
                        $not->destroy();
                    }
                }

                $televendas = (new Televendas())->find("produto = $produto->id")->fetch(true);
                if ($televendas) {
                    foreach ($televendas as $televenda) {
                        $cup = (new Televendas())->findById($televenda->id);
                        $cup->destroy();
                    }
                }

                $campanhas = (new Campanha())->find("produto = $produto->id")->fetch(true);
                if ($campanhas) {
                    foreach ($campanhas as $campanha) {
                        $camp = (new Campanha())->findById($campanha->id);
                        $camp->destroy();
                    }
                }

                $postbacks = (new Postback())->find("produto = $produto->id")->fetch(true);
                if ($postbacks) {
                    foreach ($postbacks as $postback) {
                        $post = (new Postback())->findById($postback->id);
                        $post->destroy();
                    }
                }
                sleep(5);
            } else {
                echo "Houve um problema ao excluir seu produto. Tente novamente mais tarde!";
            }
        } elseif ($vendas && $assinaturas) {
            echo "Esse produto possui assinaturas. Será necessário cancelar todas as assinaturas antes de prosseguir";
        } else {
            $checkouts = (new Checkout())->find("produto = $produto->id")->fetch(true);
            if ($checkouts) {
                foreach ($checkouts as $checkout) {
                    $ch = (new Checkout())->findById($checkout->id);
                    $ch->destroy();
                }
            }

            $planos = (new Plano())->find("produto = $produto->id")->fetch(true);
            if ($planos) {
                foreach ($planos as $plano) {
                    $pl = (new Plano())->findById($plano->id);
                    $pl->destroy();
                }
            }

            $afiliacao = (new Afiliacao())->find("produto = $produto->id")->fetch(true);
            if ($afiliacao) {
                foreach ($afiliacao as $afiliacao) {
                    $afi = (new Afiliacao())->findById($afiliacao->id);
                    $afi->destroy();
                }
            }

            $coproducao = (new CoProdutor())->find("produto = $produto->id")->fetch(true);
            if ($coproducao) {
                foreach ($coproducao as $coproducao) {
                    $cop = (new CoProdutor())->findById($coproducao->id);
                    $cop->destroy();
                }
            }

            $urls = (new Url())->find("produto = $produto->id")->fetch(true);
            if ($urls) {
                foreach ($urls as $url) {
                    $ur = (new Url())->findById($url->id);
                    $ur->destroy();
                }
            }

            $cupons = (new Cupom())->find("produto = $produto->id")->fetch(true);
            if ($cupons) {
                foreach ($cupons as $cupom) {
                    $cup = (new Cupom())->findById($cupom->id);
                    $cup->destroy();
                }
            }

            $upsell = (new Upsell())->find("produto = $produto->id")->fetch(true);
            if ($upsell) {
                foreach ($upsell as $upsell) {
                    $ups = (new Upsell())->findById($upsell->id);
                    $ups->destroy();
                }
            }

            $notificacoes = (new Notificacao())->find("produto = $produto->id")->fetch(true);
            if ($notificacoes) {
                foreach ($notificacoes as $notificacao) {
                    $not = (new Notificacao())->findById($notificacao->id);
                    $not->destroy();
                }
            }

            $televendas = (new Televendas())->find("produto = $produto->id")->fetch(true);
            if ($televendas) {
                foreach ($televendas as $televenda) {
                    $cup = (new Televendas())->findById($televenda->id);
                    $cup->destroy();
                }
            }

            $campanhas = (new Campanha())->find("produto = $produto->id")->fetch(true);
            if ($campanhas) {
                foreach ($campanhas as $campanha) {
                    $camp = (new Campanha())->findById($campanha->id);
                    $camp->destroy();
                }
            }

            $postbacks = (new Postback())->find("produto = $produto->id")->fetch(true);
            if ($postbacks) {
                foreach ($postbacks as $postback) {
                    $post = (new Postback())->findById($postback->id);
                    $post->destroy();
                }
            }

            sleep(5);

            if ($produto->destroy()) {
                echo "Excluido com sucesso";
            }
        }
    }
}

<?php

//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Produtos;

@session_start();

use League\Plates\Engine;
use Source\App\Asaas\AppAsaas;
use Source\App\Frete\AppFrete;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\App\Sessao\AppSessao;
use Source\Models\Checkout\Checkout;
use Source\Models\Upsell\Upsell;
use Source\Models\Notificacoes\Notificacao;
use Source\Models\Campanhas\Campanha;
use Source\Models\Pixel\Pixel;
use Source\Models\URL\Url;
use Source\Models\Cupons\Cupom;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;
use Source\Models\Televendas\Televendas;
use Source\Models\Assinaturas\Assinatura;
use Source\Models\Postback\Postback;
use Source\Models\Frete\Frete;
use Source\Models\CartaoCheckoutTransparente\Cartao;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Aws;
use Source\Helpers\Emails;
use Source\Models\CartaoCheckoutTransparente\Adquirente;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;
use Source\Models\Shopify\Shopify;

class AppProdutos
{
    private $view;
    private $pastaProduto;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->pastaProduto = PASTA . "produtos/";

        $this->sessao = (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
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
                if ($chave == "numcart" || $chave == "cvv" || $chave == "ano-val") {
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

    public function produtos($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("meus_produtos");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'meus_produtos'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $filtro = $this->filtros();
        if ($filtro) {
            $filtros = $filtro . " AND ";
        } else {
            $filtros = "";
        }

        $produto = new Produto();

        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {
            $produtosCount = (new Produto())->find($filtros . "excluir = 0 AND (rascunho = 0 OR usuario = " . $_SESSION["id"] . ")")->count();
            $paginas = ceil($produtosCount / $offset);
            $produtos = $produto->find($filtros . "excluir = 0 AND (rascunho = 0 OR usuario = " . $_SESSION["id"] . ")")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
            $produtores = (new Usuario())->find("tipo_usuario = 4 OR tipo_usuario = 1 OR tipo_usuario = 2")->order("nome ASC")->fetch(true);
        } else {
            $idProdutor = @$this->section->getId();
            $produtosCount = (new Produto())->find($filtros . "usuario = $idProdutor AND excluir = 0")->count();
            $paginas = ceil($produtosCount / $offset);
            $produtos = $produto->find($filtros . "usuario = $idProdutor AND excluir = 0")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
            // } else if ($_SESSION["tipo"] == 3 || $_SESSION["tipo"] == 2) {
            //     $produtosCount = (new Produto())->find($filtros . "rascunho = 0 AND excluir = 0")->count();
            //     $paginas = ceil($produtosCount / $offset);
            //     $produtos = $produto->find($filtros . "rascunho = 0 AND excluir = 0")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("status ASC")->fetch(true);
        }

        // echo $produtosCount;

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $this->section->getId())->fetch();
        $shopify = (new Shopify())->find("usuario = " . $this->section->getId())->fetch();

        echo $this->view->render("produtos/produtos", [
            "title" => "Produtos",
            "section" => @$section,
            "permissao" => @$permissao,
            "produtos" => @$produtos,
            "usuarioDados" => @$usuarioDados,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "produtores" => @$produtores,
            "shopify" => @$shopify,
        ]);
    }

    public function filtros()
    {
        $nome = @$_GET["nome"];
        $categoria = @$_GET["categoria"];
        $tipo = @$_GET["tipo"];
        $status = @$_GET["status"];
        $comissao = @$_GET["comissao"];
        $usuario = @$_GET["usuario"];
        $sql = array();

        if ($nome != "") {
            $sql[] = "nome LIKE '%{$nome}%'";
        }

        if ($categoria != "") {
            $sql[] = "categoria = {$categoria}";
        }

        if ($tipo != "") {
            $sql[] = "tipo_produto = {$tipo}";
        }

        if ($status != "") {
            $sql[] = "status = {$status}";
        }

        if ($comissao != "") {
            $sql[] = "tipo_atribuicao = {$comissao}";
        }

        if ($usuario != "") {
            $sql[] = "usuario = {$usuario}";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function novo($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("novo_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'novo_produto'")->fetch();

        $usuario = (new Usuario())->findById(@$this->section->getId());
        $usuarioDados = (new UsuarioDados())->find("usuario = " . $this->section->getId())->fetch();
        $asaasClientes = (new AppAsaas(null))->verificarCliente($_SESSION["id"]);

        echo $this->view->render("produtos/novo_produto", [
            "title" => "Novo Produto",
            "section" => @$section,
            "permissao" => @$permissao,
            "usuario" => @$usuario,
            "usuarioDados" => @$usuarioDados,
            "asaasClientes" => $asaasClientes,
        ]);
    }

    public function cadastro($data)
    {
        // var_dump($data);
        // exit;

        if (@$data["preco"] < 5) {
            $msg = "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
            $classCss = "bg-danger";

            header("Location: " . url("produtos/novo?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }

        $files = new File();

        $produto = (new Produto());
        $id = $produto->find()->limit(1)->order("id DESC")->fetch(true);

        if (@$data["codigo_shopify"] && @$data["shopify"]) {
            $produto->codigo = @$data["codigo_shopify"];
            $produto->shopify = @$data["shopify"];
        } else {
            if ($id) {
                foreach ($id as $id)
                    $produto->codigo = $id->codigo + 1;
            } else {
                $produto->codigo = "01010";
            }
        }

        $produto->usuario = $this->section->getId();
        $produto->rascunho = @$data['rascunho'] != "" ? $data['rascunho'] : 0;
        //$produto->st4rt = $data["st4rt"];

        if ($data["nome"] == "" || $data["nome"] == " ") {
            $msg = "Preencha todos os campos";
            $classCss = "bg-warning";
            header("Location: " . url("produtos/novo?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }
        $produto->nome = $data["nome"];
        $produto->descricao = $data["descricao"];
        $produto->tipo_produto = $data["tipo_produto"];
        $produto->membros = @$data["membros"] ? $data["membros"] : NULL;

        if ($produto->membros == "octaclube")
            $produto->url_membros = "https://club.winpay.digital";
        else
            $produto->url_membros = @$data["url_membros"] ? $data["url_membros"] : NULL;

        $produto->categoria = $data["categoria"];
        $produto->descricao = $data["descricao"];
        $produto->habilitar_ecommerce = $data["habilitar_ecommerce"];
        $produto->caracteristicas = $data["caracteristicas"];
        $produto->nome_fatura = @$data["nome_fatura"];
        $produto->tipo_cobranca = @$data["tipo_cobranca"];
        $produto->tipo_precificacao = @$data["tipo_precificacao"];
        $produto->preco = $data["preco"];
        $produto->disponivel = @$data["disponivel"];
        $produto->quantidade_max = @$data["quantidade_max"];
        $produto->email_suporte = @$data["email_suporte"];
        $produto->email_chargeback = @$data["email_chargeback"];
        $produto->whatsapp_checkout = @$data["whatsapp_checkout"];
        $produto->link_suporte_whatsapp = @$data["link_suporte_whatsapp"];
        $produto->garantia = @$data["garantia"];
        $produto->pagina_venda = @$data["pagina_venda"];
        $produto->obrigado_cartao = @$data["obrigado_cartao"];
        $produto->obrigado_boleto = @$data["obrigado_boleto"];
        $produto->obrigado_analise = @$data["obrigado_analise"];
        $produto->link_rastreio = @$data["link_rastreio"];

        if ($_SESSION["tipo"] == 5) {
            $usuario = (new Usuario())->findById($this->section->getId());
            $usuario->tipo_usuario = 4;
            $usuario->save();
            $_SESSION["tipo"] = 4;
        }

        if (!@$data["shopify"]) {
            if ($produto->rascunho == 1)
                $produto->status = 0;
            else
                $produto->status = 1;
        } else
            $produto->status = $data["status"]; ##Cadastra status do shopify

        $produto->aceite_termos = @$data["aceite_termos"];

        if (@$data["shopify"] && @$data["shopify"] == 1)
            $produto->foto = @$data["foto"];
        else {

            $produto->foto = $files->upload($_FILES["foto"], $this->pastaProduto);

            $produto->url_midias = $data["url_midias"];
            $produto->documentos = $files->upload($_FILES["documentos"], PASTA . "documentos/", 10);

            //$produto->termos = $data["termos"];
            $produto->aceite_termos = @$data["aceite_termos"];
        }

        if ($produto->save()) {
            if (@$data["shopify"] && @$data["shopify"] == 1) {
                $id_produto = $produto->id;
                return $id_produto;
                // echo "Produto importado com sucesso";
            } else {
                $data["url"] = @$data["pagina_venda"];
                $data["descricao"] = "Página de vendas";
                $data["produto"] = @$produto->id;
                $data["privada"] = @$produto->id;
                $data["pv"] = 1;
                $this->urlCadastro($data);

                if ($produto->rascunho == 1) {
                    $msg = "Produto em rascunho salvo com sucesso! Para que o produto vá para a análise, remova a opção de Rascunho!";
                    $classCss = "bg-info";
                } else {
                    $msg = "Produto Cadastrado Com Sucesso! Produto será enviado para análise";
                    $classCss = "bg-success";

                    $data["email"] = "suporte.oficial@redrocketpay.com";
                    $data["assunto"] = "Novo produto cadastrado";
                    $data["mensagem"] = "Novo produto cadastrado <br>" .
                        "Nome do produto: $produto->nome <br>" .
                        "Produtor: " . $produto->dataProdutor()->nome;
                    $enviaEmail = (new Emails())->enviaSendGrid($data);
                }

                $data["email"] = "suporte.oficial@redrocketpay.com";
                $data["assunto"] = "Novo produto cadastrado";
                $data["mensagem"] = "Novo produto cadastrado <br>" .
                    "Nome do produto: $produto->nome <br>" .
                    "Produtor: " . $produto->dataProdutor()->nome;

                if (AMBIENTE == "produção")
                    $enviaEmail = (new Emails())->enviaSendGrid($data);

                header("Location: " . url("produtos/plano-produto/$produto->id" . "?msg=" . $msg . "&classCss=" . $classCss));
            }
        } else {
            $msg = "Erro Ao Cadastrar Produto! Tente mais tarde" . "<br>";
            $msg .= $produto->fail()->getMessage();
            $classCss = "bg-danger";
            header("Location: " . url("produtos/novo" . "?msg=" . $msg . "&classCss=" . $classCss));
        }

        // $data["msg"] = $msg;
        // $data["classCss"] = $classCss;
        // var_dump($produto);
    }

    public function detalhes($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("meus_produtos");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'meus_produtos'")->fetch();

        $afiliacao = new Afiliacao();
        $checkout = new Checkout();
        $produto = (new Produto())->findById($data["codigo"]);
        $usuario = (new UsuarioDados())->find("usuario = $produto->usuario")->fetch();
        $urls = (new Url())->find("produto = " . $produto->id)->fetch(true);
        $planos = (new Plano())->find("produto = $produto->id AND ativo = 1 AND privado = 0")->fetch(true);

        echo $this->view->render("produtos/detalhe_produto", [
            "title" => @$produto->nome,
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "planos" => $planos,
            "usuario" => $usuario,
            "urls" => $urls,
            "afiliacao" => $afiliacao,
            "checkout" => $checkout,
            "msg" => ""
        ]);
    }

    public function editarStatus($data)
    {
        $id = $data["id"];
        $produto = (new Produto())->findById($id);
        $status = $produto->status;

        if ($produto->rascunho == 1) {
            $produto->status = 0;
            $produto->save();
            $msg = "Produto ainda como rascunho!" . "<br>";
            $classCss = "bg-danger";
        } else {

            $plano = (new Plano())->find("produto = $produto->id")->fetch(true);

            if ($data["status"])
                $produto->status = $data["status"];

            if ($data["observacao"])
                $produto->observacao = @$data["observacao"];

            if (@$data["st4rt"])
                $produto->st4rt = @$data["st4rt"];
            $produto->usuario_ultima_alteracao = $this->section->getId();

            if ($produto->save()) {
                $msg = "Atualizado Com Sucesso!";
                $classCss = "bg-success";

                if (@$data["status"] && $plano) {
                    foreach ($plano as $plano) {
                        $plano->status = $data["status"];
                        $plano->save();
                    }
                }

                $email = new Emails();
                $usuario = (new Usuario())->findById($produto->usuario);
                $data["nome"] = $usuario->nome;
                $data["email"] = $usuario->email;
                $data["produto"] = $produto->nome;

                $alertas = array(
                    2 => $email->produtoAprovado($data),
                    3 => $email->produtoReprovado($data),
                    4 => $email->produtoBloqueado($data),
                );

                if ($status != $data["status"]) {
                    $email->enviaSendGrid($alertas[$data["status"]]);
                }
            } else {
                $msg = "Erro Ao Atualizar!" . "<br>";
                $msg .= $produto->fail()->getMessage();
                $classCss = "bg-danger";
            }
        }
        header("Location: " . url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function editarP($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("editar_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'editar_produto'")->fetch();

        $produto = (new Produto())->findById($data["codigo"]);
        $usuarioDados = (new UsuarioDados())->find("usuario = " . $this->section->getId())->fetch();

        echo $this->view->render("produtos/editar_produto", [
            "title" => $produto->nome,
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "usuarioDados" => $usuarioDados
        ]);
    }

    public function editar($data)
    {
        if (@$data["preco"] < 5) {
            $msg = "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
            $classCss = "bg-danger";

            header("Location: " . url("produtos/editar/" . $data["id"] . "?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }

        $id = $data["id"];
        $produto = (new Produto())->findById($id);
        $files = new File();
        // $files = new Aws();

        $produto->rascunho = @$data['rascunho'] != "" ? 1 : 0;
        $produto->nome = $data["nome"];
        $produto->descricao = $data["descricao"];
        $produto->nome_fatura = @$data["nome_fatura"];
        $produto->tipo_produto = @$data["tipo_produto"];
        $produto->membros = @$data["membros"] ? $data["membros"] : NULL;

        if ($produto->membros == "octaclube")
            $produto->url_membros = "https://club.winpay.digital";
        else
            $produto->url_membros = @$data["url_membros"] ? $data["url_membros"] : NULL;

        $produto->categoria = $data["categoria"];
        $produto->descricao = $data["descricao"];
        $produto->habilitar_ecommerce = $data["habilitar_ecommerce"];
        $produto->caracteristicas = $data["caracteristicas"];
        $produto->tipo_cobranca = @$data["tipo_cobranca"];
        $produto->tipo_precificacao = @$data["tipo_precificacao"];
        $produto->preco = $data["preco"];
        $produto->disponivel = @$data["disponivel"];
        $produto->quantidade_max = @$data["quantidade_max"];
        $produto->email_suporte = $data["email_suporte"];
        $produto->email_chargeback = @$data["email_chargeback"];
        $produto->whatsapp_checkout = $data["whatsapp_checkout"];
        $produto->link_suporte_whatsapp = @$data["link_suporte_whatsapp"];
        $produto->garantia = $data["garantia"];
        // $produto->pagina_venda = @$data["pagina_venda"];
        $produto->obrigado_cartao = @$data["obrigado_cartao"];
        $produto->obrigado_boleto = @$data["obrigado_boleto"];
        $produto->obrigado_analise = @$data["obrigado_analise"];
        $produto->link_rastreio = @$data["link_rastreio"];

        if ($produto->rascunho == 1)
            $produto->status = 0;
        elseif ($produto->rascunho == 0 && ($produto->status == 0 || $produto->status == 1)) {
            $produto->status = 1;
            $data["email"] = "suporte.oficial@redrocketpay.com";
            $data["assunto"] = "Novo produto cadastrado";
            $data["mensagem"] = "Novo produto cadastrado <br>" .
                "Nome do produto: $produto->nome <br>" .
                "Produtor: " . @$produto->dataProdutor()->nome;

            if (AMBIENTE == "produção")
                $enviaEmail = (new Emails())->enviaSendGrid($data);
        } else
            $produto->status = $data["status"];

        // if(empty(@$_FILES["foto"]["name"])){
        //     $produto->foto = $data["foto_atual"];
        // } else {
        //     $produto->foto = $files->upload($_FILES["foto"], $this->pastaProduto);
        // }

        if (@$_FILES["foto"]["name"]) {
            $produto->foto = $files->upload($_FILES["foto"], $this->pastaProduto);
            // $produto->foto = $files->upload($_FILES["foto"], "produtos/");
            if (@$data["foto_atual"]) @unlink($this->pastaProduto . @$data["foto_atual"]);
        }

        if (@$_FILES["documentos"]["name"]) {
            $produto->documentos = $files->upload($_FILES["documentos"], PASTA . "documentos/", 10);
            // $produto->documentos = $files->upload($_FILES["documentos"], "documentos/", 10);
            if (@$data["doc_atual"]) @unlink(PASTA . "documentos/" . @$data["doc_atual"]);
        }
        $produto->url_midias = $data["url_midias"];

        if ($produto->save()) {
            //var_dump($produto);
            $msg = "Atualizado Com Sucesso!";
            $classCss = "bg-success";

            // echo $produto->foto;
        } else {
            $msg = "Erro Ao Atualizar!" . "<br>";
            $msg .= $produto->fail()->getMessage();
            $classCss = "bg-danger";
        }

        $data["msg"] = $msg;
        $data["classCss"] = $classCss;
        $data["codigo"] = $produto->id;

        //echo url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss);
        header("Location: " . url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function ativarProduto($data)
    {
        $produto = (new Produto())->findById($data["produto"]);

        $produto->status = 2;
        if ($produto->save()) {
            echo "Produto ativado com sucesso";
        } else {
            echo "Ocorreu um erro ao ativar o produto. Tente novamente mais tarde";
        }
    }

    public function inativarProduto($data)
    {
        $produto = (new Produto())->findById($data["produto"]);

        $produto->status = 6;
        if ($produto->save()) {
            echo "Produto inativado com sucesso";
        } else {
            echo "Ocorreu um erro ao inativar o produto. Tente novamente mais tarde";
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

    public function checkoutGuiado($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("checkout_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'checkout_produto'")->fetch();

        $checkout = (new Checkout())->find("produto = :produto", "produto=" . $data["codigo"])->fetch(true);
        $produto = (new Produto())->findById($data["codigo"]);
        $plano = (new Plano())->find("produto = '{$data["codigo"]}' && usuario = '{$_SESSION['id']}'")->fetch(true);
        $planosOrderBump = (new Plano())->find("usuario = '{$this->section->getId()}'")->fetch(true);
        $pixel = (new Pixel())->find("usuario = '{$this->section->getId()}'")->fetch(true);
        $adquirentes = (new Adquirente())->find("usuario = '{$this->section->getId()}'")->fetch(true);
        $cartoes = (new Cartao())->find("status = 2 AND usuario = " . $this->section->getId())->count();

        echo $this->view->render("produtos/novo_prod_checkout", [
            "title" => "Checkout $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "checkout" => $checkout,
            "produto" => $produto,
            "plano" => $plano,
            "planosOrderBump" => $planosOrderBump,
            "pixel" => $pixel,
            "adquirentes" => @$adquirentes,
            "cartoes" => $cartoes,
        ]);

        // header("Location: " . url("produtos/checkout/" . $checkout->produto . "?msg=" . $msg . "&classCss=" . $classCss));

    }

    public function checkout($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("checkout_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'checkout_produto'")->fetch();

        if (@$_GET["nome"]) {
            $filtros = "(descricao LIKE '%{$_GET["nome"]}%' OR codigo = '{$_GET["nome"]}') AND ";
        } else {
            $filtros = "";
        }

        $checkoutCount = (new Checkout())->find($filtros . "produto = :produto", "produto=" . $data["codigo"])->count();
        $checkout = (new Checkout())->find($filtros . "produto = :produto", "produto=" . $data["codigo"])->fetch(true);
        $produto = (new Produto())->findById($data["codigo"]);
        $plano = (new Plano())->find("produto = '{$data["codigo"]}' && usuario = '{$this->section->getId()}'")->fetch(true);
        $planosOrderBump = (new Plano())->find("usuario = '{$this->section->getId()}'")->fetch(true);
        $adquirentes = (new Adquirente())->find("usuario = '{$this->section->getId()}'")->fetch(true);
        // $planosOrderBump = (new Plano())->find("usuario = '{$_SESSION['id']}'")->fetch(true);
        $pixel = (new Pixel())->find("usuario = '{$this->section->getId()}'")->fetch(true);

        $cartoes = (new Cartao())->find("status = 2 AND usuario = " . $this->section->getId())->count();

        echo $this->view->render("produtos/checkout", [
            "title" => "Checkout $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "checkout" => $checkout,
            "produto" => $produto,
            "plano" => $plano,
            "adquirentes" => @$adquirentes,
            "cartoes" => $cartoes,
            "count1" => @$checkoutCount,
            "planosOrderBump" => $planosOrderBump,
            "pixel" => $pixel,
        ]);
    }

    // Envia os dados do checkout para o sistema de televendas
    public function checkoutTelevendas($data, $excluir)
    {

        $checkout = (new Checkout())->find("codigo = '{$data["codigo"]}' AND usuario = {$this->section->getId()} AND produto = {$data["produto"]}")->fetch();
        // $checkout->dataPlano()->codigo

        if ($excluir == 1) {
            $data["usuario"] = @$checkout->usuario;
            $data["codigo"] = @$checkout->codigo;
            $data["produto"] = @$checkout->dataProduto()->codigo;
            $data["excluir"] = 1;
        } else {
            $data["plano"] =  $checkout->dataPlano()->codigo;
            $data["usuario"] = @$checkout->usuario;
            $data["codigo"] = @$checkout->codigo;
            $data["descricao"] = @$checkout->descricao;
            $data["produto"] = @$checkout->dataProduto()->codigo;
            // $data["plano"] = @$checkout->plano;
            $data["privado"] = @$checkout->privado;
            $data["televendas"] = @$checkout->televendas;
            $data["excluir"] = 0;
        }

        // $curl = curl_init();
        // $url = url_televendas("televendas/checkout");
        // // echo $url;
        // // exit;
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "$url",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => false,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_POST => true,
        //     CURLOPT_POSTFIELDS => http_build_query($data),
        // ));

        // $response = curl_exec($curl);
        // // exit;
        // curl_close($curl);
        // echo $response;

        // return $response;
    }

    public function checkoutCadastro($data)
    {
        // var_dump(@$data);
        // exit;
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $numero_de_bytes = 4;
        $resultado_bytes = random_bytes($numero_de_bytes);
        $codigo = strtoupper(bin2hex($resultado_bytes));

        $files = new File();
        $checkout = new Checkout();

        $checkout->codigo = $codigo;
        $checkout->descricao = $data["descricao"];
        $checkout->modelo_checkout = @$data["modelo_checkout"];
        // ADQUIRENTE PARA CARTÃO DE CRÉDITO
        $checkout->adquirente = @$data["adquirente"] != "" ? @$data["adquirente"] : "redrocket";
        // ADQUIRENTE PARA CARTÃO DE BOLETO
        $checkout->adquirente_boleto = @$data["adquirente_boleto"] != "" ? @$data["adquirente_boleto"] : "redrocket";
        // ADQUIRENTE PARA CARTÃO DE PIX
        $checkout->adquirente_pix = @$data["adquirente_pix"] != "" ? @$data["adquirente_pix"] : "redrocket";

        $checkout->mostrar_preco = @$data["mostrar_preco"];
        $checkout->endereco_obrigatorio = @$data["endereco_obrigatorio"];
        $checkout->produto = $data["produto"];
        $checkout->televendas = $data["televendas"];

        if (@$data["shopify"]) {
            $checkout->meio_pagamento = 10;
        } else {
            if (array_search('10', @$data["meio_pagamento"]))
                $checkout->meio_pagamento = 10;
            else
                $checkout->meio_pagamento = @implode(",", @$data["meio_pagamento"]);
        }

        if (@$data["plano"])
            $checkout->plano = @$data["plano"];
        else
            $checkout->plano = NULL;

        $checkout->privado = @$data["privado"];
        $checkout->cupom = @$data["cupom"];
        $checkout->valor_cupom = @$data["valor_cupom"];
        $checkout->so_cartao = @$data["so_cartao"];
        $checkout->usuario = $this->section->getId();

        if (@$data["banner_atual"])
            $checkout->banner = $data["banner_atual"];
        else
            $checkout->banner = $files->upload($_FILES["banner"], PASTA . "checkout/");

        if (@$data["aviso_atual"])
            $checkout->aviso = $data["aviso_atual"];
        else
            $checkout->aviso = $files->upload(@$_FILES["aviso"], PASTA . "checkout/");

        if (@$data["banner_lateral"])
            $checkout->banner_lateral = $data["banner_lateral"];
        else
            $checkout->banner_lateral = $files->upload($_FILES["baner_lateral"], PASTA . "checkout/");


        if (@$data["vencimento_boletos"])
            $checkout->vencimento_boletos = $data["vencimento_boletos"];

        // if (@$data["usar_frete"]) {
        //     $checkout->usar_frete           = $data["usar_frete"];
        //     $checkout->valor_frete          = $data["valor_frete"];
        //     $checkout->prazo_entrega        = $data["prazo_entrega"];
        //     $checkout->frete_gratis         = $data["frete_gratis"];
        //     $checkout->prazo_entrega_gratis = $data["prazo_entrega_gratis"];
        // }

        if (@$data["usar_order_bump"] == 0 || @$data["usar_order_bump"] == 1) {
            $checkout->usar_order_bump       = $data["usar_order_bump"];
            $checkout->titulo_informativo    = $data["titulo_informativo"];
            // var_dump($data["planos_order_bump"]);
            if (@$data["planos_order_bump1"] != "" && @$data["planos_order_bump0"] != "") {
                // echo ("entrou if");
                $checkout->planos_order_bump = @$data["planos_order_bump0"] . "," .  @$data["planos_order_bump1"];
            } elseif (@$data["planos_order_bump0"] != "") {
                // echo ("entrou else");
                $checkout->planos_order_bump = @$data["planos_order_bump0"];
            } else {
                $checkout->planos_order_bump = @$data["planos_order_bump1"];
            }
        }

        $checkout->usar_contador = @$data["usar_contador"];

        if ($checkout->usar_contador == 1) {
            $checkout->texto_contador = @$data["texto_contador"];
            $checkout->acao_contador = @$data["acao_contador"];
            $checkout->aumento = @$data["aumento"];
            $checkout->cor_contador = @$data["cor_contador"];
            $checkout->cor_texto = @$data["cor_texto"];
        }

        $checkout->pixel_facebook = @$data["pixel_facebook"] ? $data["pixel_facebook"] : NULL;
        $checkout->pixel_facebook_api = @$data["pixel_facebook_api"] ? $data["pixel_facebook_api"] : NULL;
        $checkout->pixel_google_analitics = @$data["pixel_google_analitics"] ? @$data["pixel_google_analitics"] : NULL;
        $checkout->pixel_google_ads = @$data["pixel_google_ads"] ? @$data["pixel_google_ads"] : NULL;
        $checkout->pixel_google_tag_manager = @$data["pixel_google_tag_manager"] ? @$data["pixel_google_tag_manager"] : NULL;
        $checkout->pixel_taboola = @$data["pixel_taboola"] ? @$data["pixel_taboola"] : NULL;
        $checkout->pixel_tiktok = @$data["pixel_tiktok"] ? @$data["pixel_tiktok"] : NULL;
        $checkout->pixel_kwai = @$data["pixel_kwai"] ? @$data["pixel_kwai"] : NULL;

        $data["codigo"] = $checkout->codigo;

        // if (@$data["id_frete"] != "") {
        //     $checkout->id_frete = @$data["id_frete"];
        // }


        if (@$data["usar_frete"] == "") {
            $checkout->usar_frete = 0;
        }
        // exit;

        if( empty( $checkout->televendas  ) ) {
            $checkout->televendas  = null;
        }

        if ($checkout->save()) {

            if (@$data["id_frete"] != "") {
                $data["id_checkout"] = $checkout->id;
                $frete = (new AppFrete(null))->cadastroFrete($data);
            }

            // var_dump("CHECKOUT", $checkout);
            if ($data["televendas"] == 1) {
                $this->checkoutTelevendas($data, 0);
            }

            if (@$data["usar_frete"] != "" && @$data["id_frete"] == "") {
                $data["id_checkout"] = $checkout->id;
                $frete = (new AppFrete(null))->cadastroFrete($data);
                if (@$frete != 0) {
                    $msg = "Erro ao criar frete. Entre em contato com o suporte.";
                }
            }

            $msg = "Salvo com sucesso!";
            $classCss = "bg-success";
        } else {
            $msg  = "Erro ao salvar <br>";
            $msg .= $checkout->fail()->getMessage();
            $classCss = "bg-warning";
        }
        // var_dump($data);
        if (!@$data["shopify"]) {
            // Identifica se o checkout está sendo criado diretamente pelo novo cadastro de produto (Novo Produto -> Novo Plano -> Novo checkout)
            if (@$data["prod_checkout"] == 1) {
                header("Location: " . url("produtos/detalhe/" . $checkout->produto . "?msg=" . $msg . "&classCss=" . $classCss));
            } else {
                header("Location: " . url("produtos/checkout/" . $checkout->produto . "?msg=" . $msg . "&classCss=" . $classCss));
            }
        } else {
            echo "Dados salvos com sucesso!";
        }
    }

    public function checkoutEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $files = new File();
        $checkout = (new Checkout())->findById($data["id"]);
        $checkout->descricao = $data["descricao"];
        $checkout->modelo_checkout = @$data["modelo_checkout"];

        // ADQUIRENTE PARA CARTÃO DE CRÉDITO
        $checkout->adquirente = @$data["adquirente"] != "" ? @$data["adquirente"] : "redrocket";
        // ADQUIRENTE PARA CARTÃO DE BOLETO
        $checkout->adquirente_boleto = @$data["adquirente_boleto"] != "" ? @$data["adquirente_boleto"] : "redrocket";
        // ADQUIRENTE PARA CARTÃO DE PIX
        $checkout->adquirente_pix = @$data["adquirente_pix"] != "" ? @$data["adquirente_pix"] : "redrocket";

        $checkout->mostrar_preco = @$data["mostrar_preco"];
        $checkout->endereco_obrigatorio = @$data["endereco_obrigatorio"];
        $checkout->produto = $data["produto"];
        $checkout->televendas = $data["televendas"];

        if (@$data["plano"])
            $checkout->plano = @$data["plano"];
        else
            $checkout->plano = NULL;

        $checkout->privado = @$data["privado"];
        $checkout->cupom = @$data["cupom"];
        $checkout->valor_cupom = @$data["valor_cupom"];
        $checkout->so_cartao = @$data["so_cartao"];

        $checkout->usar_contador = @$data["usar_contador"];
        $checkout->texto_contador = @$data["texto_contador"];
        $checkout->acao_contador = @$data["acao_contador"];
        $checkout->aumento = @$data["aumento"];
        $checkout->cor_contador = @$data["cor_contador"];
        $checkout->cor_texto = @$data["cor_texto"];

        if (!@$data["meio_pagamento"])
            $checkout->meio_pagamento = 10;
        else if (array_search('10', @$data["meio_pagamento"]))
            $checkout->meio_pagamento = 10;
        else
            $checkout->meio_pagamento = @implode(",", @$data["meio_pagamento"]);

        if (@$data["vencimento_boletos"])
            $checkout->vencimento_boletos = $data["vencimento_boletos"];

        // if (@$data["usar_frete"] == 0 || @$data["usar_frete"] == 1) {
        //     $checkout->usar_frete           = $data["usar_frete"];
        //     $checkout->valor_frete          = $data["valor_frete"];
        //     $checkout->prazo_entrega        = $data["prazo_entrega"];
        //     $checkout->frete_gratis         = $data["frete_gratis"];
        //     $checkout->prazo_entrega_gratis = $data["prazo_entrega_gratis"];
        // }

        if ($_FILES["banner"]["name"] == "") {
            $checkout->banner = $data["banner_atual"];
            if (@$data["banner_atual"] == "") @unlink(PASTA . "checkout/" . @$data["ban_atual"]);
        } else {
            $checkout->banner = $files->upload($_FILES["banner"], PASTA . "checkout/");
            if (@$data["banner_atual"]) @unlink(PASTA . "checkout/" . @$data["banner_atual"]);
        }

        if ($_FILES["baner_lateral"]["name"] == "") {
            $checkout->banner_lateral = $data["banner_lateral"];
            if (@$data["banner_lateral"] == "") @unlink(PASTA . "checkout/" . @$data["ban_lateral"]);
        } else {
            $checkout->banner_lateral = $files->upload($_FILES["baner_lateral"], PASTA . "checkout/");
            if (@$data["banner_lateral"]) @unlink(PASTA . "checkout/" . @$data["banner_lateral"]);
        }

        if (@$_FILES["aviso"]["name"] == "") {
            $checkout->aviso = $data["aviso_atual"];
            if (@$data["aviso_atual"] == "") @unlink(PASTA . "checkout/" . @$data["avi_atual"]);
        } else {
            $checkout->aviso = $files->upload($_FILES["aviso"], PASTA . "checkout/");
            if (@$data["aviso_atual"]) @unlink(PASTA . "checkout/" . @$data["aviso_atual"]);
        }

        if (@$data["usar_order_bump"] == 0 || @$data["usar_order_bump"] == 1) {
            $checkout->usar_order_bump       = $data["usar_order_bump"];
            $checkout->titulo_informativo    = $data["titulo_informativo"];
            // var_dump($data["planos_order_bump"]);
            if (@$data["planos_order_bump1"] != "" && $data["planos_order_bump0"] != "") {
                // echo ("entrou if");
                $checkout->planos_order_bump = $data["planos_order_bump0"] . "," .  $data["planos_order_bump1"];
            } elseif (@$data["planos_order_bump0"] != "") {
                // echo ("entrou else");
                $checkout->planos_order_bump = $data["planos_order_bump0"];
            } else {
                $checkout->planos_order_bump = $data["planos_order_bump1"];
            }
        }

        $checkout->pixel_facebook = @$data["pixel_facebook"] ? $data["pixel_facebook"] : NULL;
        $checkout->pixel_facebook_api = @$data["pixel_facebook_api"] ? $data["pixel_facebook_api"] : NULL;
        $checkout->pixel_google_analitics = @$data["pixel_google_analitics"] ? @$data["pixel_google_analitics"] : NULL;
        $checkout->pixel_google_ads = @$data["pixel_google_ads"] ? @$data["pixel_google_ads"] : NULL;
        $checkout->pixel_google_tag_manager = @$data["pixel_google_tag_manager"] ? @$data["pixel_google_tag_manager"] : NULL;
        $checkout->pixel_taboola = @$data["pixel_taboola"] ? $data["pixel_taboola"] : NULL;
        $checkout->pixel_tiktok = @$data["pixel_tiktok"] ? @$data["pixel_tiktok"] : NULL;
        $checkout->pixel_kwai = @$data["pixel_kwai"] ? @$data["pixel_kwai"] : NULL;

        // if(@$data["pixel_facebook"]) $checkout->pixel_facebook = @$data["pixel_facebook"];
        // else $checkout->pixel_facebook = NULL;

        // if(@$data["pixel_google"]) $checkout->pixel_google = @$data["pixel_google"];
        // else $checkout->pixel_google = NULL;

        // if(@$data["pixel_taboola"]) $checkout->pixel_taboola = @$data["pixel_taboola"];
        // else $checkout->pixel_taboola = NULL;

        if (@$data["usar_frete"] == "")
            $data["usar_frete"] = 0;

        if ($checkout->save()) {
            $data["codigo"] = $checkout->codigo;

            if (@$checkout->id_frete == "") {
                $data["id_checkout"] = $checkout->id;
                $frete = (new AppFrete(null))->cadastroFrete($data);
                if (@$frete != 0) {
                    $msg = "Erro ao criar frete. Entre em contato com o suporte.";
                }
            }

            if (@$data["usar_frete"] != "" && $checkout->id_frete != "") {
                $data["id_frete"] = $checkout->id_frete;
                $data["id_checkout"] = $checkout->id;
                $frete = (new AppFrete(null))->editarFrete($data);
                // exit;
                if (@$frete != 0) {
                    $msg = "Erro ao editar frete. Entre em contato com o suporte.";
                    // exit;
                }
            }

            // $this->checkoutTelevendas($data, 0);
            $msg = "Atualizado com sucesso";
            // $msg .= $checkout->fail()->getMessage();
            $classCss = "bg-success";
        } else {
            $msg  = "Erro ao atualizar <br>";
            $msg .= $checkout->fail()->getMessage();
            $classCss = "bg-danger";
        }

        header("Location: " . url("produtos/checkout/" . $checkout->produto . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function checkoutExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        // var_dump($data);
        // exit;
        $checkout = (new Checkout())->findById($data["id"]);

        // Exclui checkout no sistema do televendas
        $data["codigo"] = $checkout->codigo;
        $data["usuario"] = $checkout->usuario;
        $data["produto"] = $checkout->produto;
        $this->checkoutTelevendas($data, 1);

        // Exclui frete
        if ($checkout->id_frete != "") {
            $data["id_frete"] = $checkout->id_frete;
            $frete = (new AppFrete(null))->excluirFrete($data);
        }

        if ($checkout->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo "Erro ao excluir <br>";
            echo $checkout->fail()->getMessage();
        }
    }

    public function planos($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("plano_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'plano_produto'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        if (@$_GET["nome"]) {
            $filtros = "(nome LIKE '%{$_GET["nome"]}%' OR codigo = '{$_GET["nome"]}') AND ";
        } else {
            $filtros = "";
        }

        $produto = (new Produto())->findById($data["codigo"]);
        $planosCount = (new Plano())->find($filtros . "produto = '{$data["codigo"]}' AND usuario = " . $this->section->getId())->count();
        $paginas = ceil($planosCount / $offset);
        $planos = (new Plano())->find($filtros . "produto = '{$data["codigo"]}' AND usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        $coproducao = (new CoProdutor())->find("produto = :produto", "produto=" . $data["codigo"])->fetch(true);

        echo $this->view->render("produtos/planos", [
            "title" => "Planos $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "planos" => $planos,
            "coproducao" => $coproducao,
            "count1" => @$planosCount,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    // Plano para ser criado a partir da sequencia de novo cadastro de produto
    public function planoGuiado($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("plano_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'plano_produto'")->fetch();

        $produto = (new Produto())->findById($data["codigo"]);
        $coproducao = (new CoProdutor())->find("produto = :produto", "produto=" . $data["codigo"])->fetch(true);

        echo $this->view->render("produtos/novo_prod_plano", [
            "title" => "Planos",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "coproducao" => $coproducao,
        ]);
    }

    public function planoCadastro($data)
    {
        // var_dump($data);
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        // var_dump(@$data);
        // exit;
        if (!@$data["shopify"]) {
            if (@$data["preco"] < 5) {
                echo "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
                exit;
            }
        }

        $plano = new Plano();

        if (@$data["shopify"]) {
            $plano->shopify = $data["shopify"];
            $codigo = @$data["codigo"];
        } else {
            $codigo = mt_rand(10000, 999999);
        }

        $plano->codigo = $codigo;
        $plano->nome = trim($data["nome"]);
        $plano->produto = $data["produto"];
        $plano->quantidade = @$data["quantidade"] ? @$data["quantidade"] : NULL;
        $plano->usuario = $this->section->getId();
        $plano->preco = $data["preco"];
        $plano->descricao = @$data["descricao"];
        $plano->assumir_juros = @$data["assumir_juros"];
        $plano->parcelas = @$data["parcelas"];
        $plano->periodo = @$data["periodo"];
        $plano->maximo_recorrencia = isset($data["maximo_recorrencia"]) && $data["maximo_recorrencia"] !== '' 
    ? (int) $data["maximo_recorrencia"] 
    : null;
        $plano->primeira_parcela = @$data["primeira_parcela"];
        $plano->preco_parcela = isset($data["preco_parcela"]) && is_numeric($data["preco_parcela"]) ? (float)$data["preco_parcela"] : null;
        $plano->carencia = isset($data["carencia"]) && is_numeric($data["carencia"]) ? (int)$data["carencia"] : null;
        $plano->comissao_afiliado_assinatura = is_numeric($data['comissao_afiliado_assinatura']) && $data['comissao_afiliado_assinatura'] !== ''
    ? $data['comissao_afiliado_assinatura']
    : null;
        $plano->descricao = @$data["descricao"];
        $plano->privado = isset($data["privado"]) ? (int)$data["privado"] : 0;
        $plano->status = isset($data["status"]) ? (int)$data["status"] : null;
        $plano->ativo = isset($data["ativo"]) ? (int)$data["ativo"] : 0;
        $plano->habilitar_ecommerce = isset($data["habilitar_ecommerce"]) ? (int)$data["habilitar_ecommerce"] : 0;

        // COMISSAO AFILIADO

        $plano->habilitar_comissao_afiliado = isset($data["habilitar_comissao_afiliado"]) && $data["habilitar_comissao_afiliado"] !== '' 
        ? (int) $data["habilitar_comissao_afiliado"] 
        : 0;

        $plano->tipo_comissao_afiliado = isset($data["tipo_comissao1"]) && is_numeric($data["tipo_comissao1"])
    ? (int)$data["tipo_comissao1"]  // Converte para inteiro se for numérico
    : null;
    $plano->comissao_afiliado = isset($data["comissao1"]) && is_numeric($data["comissao1"])
    ? (float)$data["comissao1"]  // Converte para float se for numérico
    : null;

        // FIM COMISSAO AFILIADO

        // COMISSAO COPRODUTOR
        if (@$data["habilitar_com_cop_dup"]) {
            $plano->habilitar_comissao_coprodutores = @$data["habilitar_com_cop_dup"];
            $plano->comissao_coprodutores = @$data["comissao_coprodutores"];
        }
        if (@$data["habilitar_comissao_coprodutores"] == 1) {
            $plano->habilitar_comissao_coprodutores = @$data["habilitar_comissao_coprodutores"];

            $comissao_coprodutores = "";
            $i = 0;
            // echo "gsg " . count($data["coprodutores"]);

            // var_dump($data["coprodutores"]);
            foreach ($data["coprodutores"] as $coprodutores) {
                $data["comissao_coprodutor"][$i] = str_replace(",", ".", $data["comissao_coprodutor"][$i]);
                if (count($data["coprodutores"]) - 1 == $i) {
                    $comissao_coprodutores .= "$coprodutores|{$data["tipo_comissao_coprodutor"][$i]}|" . number_format($data["comissao_coprodutor"][$i], 2, ".", "");
                } else {
                    $comissao_coprodutores .= "$coprodutores|{$data["tipo_comissao_coprodutor"][$i]}|" . number_format($data["comissao_coprodutor"][$i], 2, ".", "") . ",";
                }
                echo $i;
                $i++;
            }
            $plano->comissao_coprodutores = @$comissao_coprodutores;
        }
        // FIM COMISSAO COPRODUTOR

        $files = new File();
        if (@$data["foto_atual"])
            $plano->foto = $data["foto_atual"];
        else
            $plano->foto = $files->upload(@$_FILES["foto"], $this->pastaProduto);
        $plano->televendas = is_numeric($data['televendas']) && $data['televendas'] !== ''
        ? $data['televendas']
        : null;

        // var_dump($plano->comissao_coprodutores, $plano->foto);

        if ($plano->save()) {

            if (@$data["shopify"])
                return $plano->id;

            $data["codigo"] = $plano->codigo;

            // if (@$data["televendas"] == 1)
            //     $this->planoTelevendas($data, 0);

            $msg = "Salvo com sucesso";
            $classCss = "bg-success";
        } else {
            $msg = $plano->fail()->getMessage();
            $classCss = "bg-warning";
        }

        if (@$data["plano-guiado"] == 1) {
            header("Location: " . url("produtos/checkout-produto/" . $plano->produto));
            exit;
        }

        if (!@$data["shopify"])
            header("Location: " . url("produtos/planos/" . $plano->produto . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function planoCadastroGuiado($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if (@$data["preco"] < 5) {
            echo "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
            exit;
        }

        $codigo = mt_rand(10000, 999999);

        $plano = new Plano();

        $plano->codigo = $codigo;
        $plano->nome = $data["nome"];
        $plano->produto = $data["produto"];
        $plano->quantidade = @$data["quantidade"] ? @$data["quantidade"] : NULL;
        $plano->usuario = $this->section->getId();
        $plano->preco = $data["preco"];
        $plano->descricao = @$data["descricao"];
        $plano->assumir_juros = @$data["assumir_juros"];
        $plano->parcelas = @$data["parcelas"];
        $plano->periodo = @$data["periodo"];
        $plano->maximo_recorrencia = isset($data["maximo_recorrencia"]) && $data["maximo_recorrencia"] !== '' 
    ? (int) $data["maximo_recorrencia"] 
    : null;

        $plano->primeira_parcela = @$data["primeira_parcela"];
        $plano->preco_parcela = @$data["preco_parcela"];
        $plano->carencia = @$data["carencia"];
        $plano->comissao_afiliado_assinatura = is_numeric($data['comissao_afiliado_assinatura']) && $data['comissao_afiliado_assinatura'] !== ''
    ? $data['comissao_afiliado_assinatura']
    : null;
        $plano->descricao = @$data["descricao"];
        $plano->privado = isset($data["privado"]) ? (int)$data["privado"] : 0;
        $plano->status = isset($data["status"]) ? (int)$data["status"] : null;
        $plano->ativo = isset($data["ativo"]) ? (int)$data["ativo"] : 0;
        $plano->habilitar_ecommerce = isset($data["habilitar_ecommerce"]) ? (int)$data["habilitar_ecommerce"] : 0;

        // COMISSAO AFILIADO

        $plano->habilitar_comissao_afiliado = @$data["habilitar_comissao_afiliado"];
        $plano->tipo_comissao_afiliado = isset($data["tipo_comissao1"]) && is_numeric($data["tipo_comissao1"])
    ? (int)$data["tipo_comissao1"]  // Converte para inteiro se for numérico
    : null;
    $plano->comissao_afiliado = isset($data["comissao1"]) && is_numeric($data["comissao1"])
    ? (float)$data["comissao1"]  // Converte para float se for numérico
    : null;

        // FIM COMISSAO AFILIADO

        // COMISSAO COPRODUTOR
        if (@$data["habilitar_com_cop_dup"]) {
            $plano->habilitar_comissao_coprodutores = @$data["habilitar_com_cop_dup"];
            $plano->comissao_coprodutores = @$data["comissao_coprodutores"];
        }
        if (@$data["habilitar_comissao_coprodutores"] == 1) {
            $plano->habilitar_comissao_coprodutores = @$data["habilitar_comissao_coprodutores"];

            $comissao_coprodutores = "";
            $i = 0;
            // echo "gsg " . count($data["coprodutores"]);
            foreach ($data["coprodutores"] as $coprodutores) {
                $data["comissao_coprodutor"][$i] = str_replace(",", ".", $data["comissao_coprodutor"][$i]);
                if (count($data["coprodutores"]) - 1 == $i) {
                    $comissao_coprodutores .= "$coprodutores|{$data["tipo_comissao_coprodutor"][$i]}|" . number_format($data["comissao_coprodutor"][$i], 2, ".", "");
                } else {
                    $comissao_coprodutores .= "$coprodutores|{$data["tipo_comissao_coprodutor"][$i]}|" . number_format($data["comissao_coprodutor"][$i], 2, ".", "") . ",";
                }
                // echo $i;
                $i++;
            }
            $plano->comissao_coprodutores = @$comissao_coprodutores;
        }
        // FIM COMISSAO COPRODUTOR

        $plano->televendas = is_numeric($data['televendas']) && $data['televendas'] !== ''
        ? $data['televendas']
        : null;

        // var_dump($data);

        if ($plano->save()) {

            $data["codigo"] = $plano->codigo;

            if (@$data["televendas"] == 1)
                $this->planoTelevendas($data, 0);

            $msg = "Salvo com sucesso";
            $classCss = "bg-success";
        } else {
            $msg = $plano->fail()->getMessage();
            $classCss = "bg-warning";
        }

        echo $msg;
    }

    public function planoEditar($data)
    {

        // var_dump($data);
        // exit;
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if (@$data["preco"] < 5) {
            echo "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
            exit;
        }

        // var_dump($data);
        $plano = (new Plano())->findById($data["id"]);

        $plano->nome = trim($data["nome"]);
        $plano->quantidade = @$data["quantidade"] ? @$data["quantidade"] : NULL;
        $plano->preco = $data["preco"];
        $plano->descricao = @$data["descricao"];
        $plano->assumir_juros = @$data["assumir_juros"];
        $plano->parcelas = @$data["parcelas"];
        $plano->periodo = @$data["periodo"];
        $plano->maximo_recorrencia = isset($data["maximo_recorrencia"]) && $data["maximo_recorrencia"] !== '' 
    ? (int) $data["maximo_recorrencia"] 
    : null;

        $plano->primeira_parcela = @$data["primeira_parcela"];
        $plano->preco_parcela = @$data["preco_parcela"];
        $plano->carencia = @$data["carencia"];
        $plano->comissao_afiliado_assinatura = is_numeric($data['comissao_afiliado_assinatura']) && $data['comissao_afiliado_assinatura'] !== ''
    ? $data['comissao_afiliado_assinatura']
    : null;
        $plano->descricao = @$data["descricao"];
        $plano->privado = isset($data["privado"]) ? (int)$data["privado"] : 0;
        $plano->status = isset($data["status"]) ? (int)$data["status"] : null;
        $plano->ativo = isset($data["ativo"]) ? (int)$data["ativo"] : 0;
        $plano->habilitar_ecommerce = isset($data["habilitar_ecommerce"]) ? (int)$data["habilitar_ecommerce"] : 0;
        // $plano->televendas = @$data["televendas"];
        $files = new File();

        if (@$_FILES["foto"]["name"] == "") {
            $plano->foto = $data["foto_atual"];
            if (@$data["foto_atual"] == "") @unlink(PASTA . "produtos/" . @$data["ft_atual"]);
        } else {
            $plano->foto = $files->upload($_FILES["foto"], $this->pastaProduto);
            if (@$data["foto_atual"]) @unlink(PASTA . "produtos/" . @$data["foto_atual"]);
        }
        $plano->televendas = is_numeric($data['televendas']) && $data['televendas'] !== ''
    ? $data['televendas']
    : null;

        // COMISSAO AFILIADO

        $plano->habilitar_comissao_afiliado = @$data["habilitar_comissao_afiliado"];
        $plano->tipo_comissao_afiliado = isset($data["tipo_comissao1"]) && is_numeric($data["tipo_comissao1"])
    ? (int)$data["tipo_comissao1"]  // Converte para inteiro se for numérico
    : null;
    $plano->comissao_afiliado = isset($data["comissao1"]) && is_numeric($data["comissao1"])
    ? (float)$data["comissao1"]  // Converte para float se for numérico
    : null;

        // FIM COMISSAO AFILIADO

        // COMISSAO COPRODUTOR
        if (@$data["habilitar_comissao_coprodutores"] == 1) {
            $plano->habilitar_comissao_coprodutores = @$data["habilitar_comissao_coprodutores"];

            $comissao_coprodutores = "";
            $i = 0;
            if (!empty($data["coprodutores"])) {
                foreach ($data["coprodutores"] as $coprodutores) {
                    $data["comissao_coprodutor"][$i] = str_replace(",", ".", $data["comissao_coprodutor"][$i]);
                    if (count($data["coprodutores"]) - 1 == $i) {
                        $comissao_coprodutores .= "$coprodutores|{$data["tipo_comissao_coprodutor"][$i]}|" . number_format($data["comissao_coprodutor"][$i], 2, ".", "");
                    } else {
                        $comissao_coprodutores .= "$coprodutores|{$data["tipo_comissao_coprodutor"][$i]}|" . number_format($data["comissao_coprodutor"][$i], 2, ".", "") . ",";
                    }
                    $i++;
                }
            }
            $plano->comissao_coprodutores = @$comissao_coprodutores;
        } else {
            $plano->habilitar_comissao_coprodutores = @$data["habilitar_comissao_coprodutores"];
        }

        // FIM COMISSAO COPRODUTOR

        if ($plano->save()) {
            $data["codigo"] = $plano->codigo;
            // $this->planoTelevendas($data, 0);
            $msg = "Atualizado com sucesso";
            $classCss = "bg-success";
        } else {
            $msg = $plano->fail()->getMessage();
            $classCss = "bg-warning";
        }

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            echo $msg;
            return;
        }

        $pagina_atual = $data["pagina"];
        header("Location: " . url("produtos/planos/" . $plano->produto . "/p/" . $pagina_atual .  "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function planoExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $plano = (new Plano())->findById($data["id"]);
        $campanhas = (new Campanha())->find("url_destino LIKE '%$plano->codigo%'")->fetch(true);
        $checkouts = (new Checkout())->find("plano = $plano->id")->fetch(true);
        $upsells = (new Upsell())->find("plano = $plano->id")->fetch(true);
        $cupons = (new Cupom())->find("planos LIKE '%$plano->id%'")->fetch(true);
        if ($campanhas) {
            foreach ($campanhas as $campanha) {
                $camp = (new Campanha())->findById($campanha->id);
                $camp->destroy();
            }
        }
        if ($checkouts) {
            foreach ($checkouts as $checkout) {
                $check = (new Checkout())->findById($checkout->id);
                $check->destroy();
            }
        }
        if ($upsells) {
            foreach ($upsells as $upsell) {
                $ups = (new Upsell())->findById($upsell->id);
                $ups->destroy();
            }
        }
        if ($cupons) {
            foreach ($cupons as $cupom) {
                $cup = (new Cupom())->findById($cupom->id);
                $cup->destroy();
            }
        }

        $data["codigo"] = $plano->codigo;
        // $data["usuario"] = $plano->usuario;
        $this->planoTelevendas($data, 1);
        if ($plano->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo "Excluido com sucesso";
            // echo $checkout->fail()->getMessage();
            $plano->usuario = NULL;
            $plano->save();
        }
    }

    public function planoTelevendas($data, $excluir)
    {

        $plano = (new Plano())->find("codigo = '{$data["codigo"]}'  AND usuario = {$this->section->getId()}")->fetch();

        if ($excluir == 1) {
            $data["codigo"] = @$plano->codigo;
            $data["usuario"] = @$plano->usuario;
            $data["excluir"] = 1;
        } else {
            $data["produto"] = @$plano->dataProduto()->codigo;

            $data["codigo"] = @$plano->codigo;
            $data["nome"] = @$plano->nome;
            // $data["produto"] = $plano->produto;
            $data["usuario"] = @$plano->usuario;
            $data["quantidade"] = @$plano->quantidade;
            $data["preco"] = @$plano->preco;
            $data["status"] = @$plano->status;
            $data["ativo"] = @$plano->ativo;
            $data["privado"] = @$plano->privado;
            $data["descricao"] = @$plano->descricao;
            $data["excluir"] = 0;
        }

        // $curl = curl_init();
        // $url = url_televendas("televendas/planos");
        // // var_dump($url);
        // // exit;
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "$url",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => false,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_POST => true,
        //     CURLOPT_POSTFIELDS => $data
        // ));

        // $response = curl_exec($curl);
        // // exit;
        // curl_close($curl);
        // // echo $response;

        // return $response;
    }

    /** 
     * AFILIADOS PRODUTO
     */
    public function afiliados($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("afiliacao_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'afiliacao_produto'")->fetch();

        $produto = (new Produto())->findById($data["codigo"]);
        $afiliacao = (new Afiliacao())->find("produto = :produto", "produto=" . $data["codigo"])->fetch(true);
        $vendas = (new Venda());
        $usuarioDados = new UsuarioDados();

        echo $this->view->render("produtos/afiliados_produto", [
            "title" => "Afiliação $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "afiliacao" => $afiliacao,
            "vendas" => $vendas,
            "usuarioDados" => $usuarioDados,
        ]);
    }

    public function afiliadosProduto($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $produto = (new Produto())->findById($data["id"]);

        $produto->abrir_afiliacao = @$data["abrir_afiliacao"];
        $produto->afiliados_aprovacao = @$data["afiliados_aprovacao"];
        $produto->visualizacao = @$data["visualizacao"];
        $produto->libera_comprador = @$data["libera_comprador"];
        $produto->tempo_cookie = @$data["tempo_cookie"];
        $produto->tipo_comissao = @$data["tipo_comissao"];
        $produto->comissao_afiliado = @$data["comissao_afiliado"];
        $produto->tipo_atribuicao = @$data["tipo_atribuicao"];
        $produto->termos = @$data["termos"];
        $produto->descricao_afiliados = @$data["descricao_afiliados"];

        // var_dump($data);

        if (@$data["usuarios"] == "antigos") {
            $afiliacao = (new Afiliacao())->find("produto = :produto", "produto=" . $data["id"])->fetch(true);

            // var_dump($afiliacao);
            if ($afiliacao) {
                foreach ($afiliacao as $afiliacao) {
                    $afiliacao->liberar_comprador = @$data["libera_comprador"];
                    $afiliacao->tempo_cookie = @$data["tempo_cookie"];
                    $afiliacao->tipo_comissao = @$data["tipo_comissao"];
                    $afiliacao->comissao_afiliado = @$data["comissao_afiliado"];
                    $afiliacao->tipo_atribuicao = @$data["tipo_atribuicao"];

                    $afiliacao->save();
                }
            }
        }

        if ( ! $produto->save()) {
            echo "Erro ao salvar <br>";
            echo $produto->fail()->getMessage();
        }
    }

    public function afiliacaoExcluirTodos($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $afiliacao = (new Afiliacao())->find("produto = " . $data["id"])->fetch(true);
        $email = new Emails();

        if ($afiliacao) {
            foreach ($afiliacao as $afiliacao) {
                $data["nome"] = $afiliacao->dataAfiliado()->nome;
                $data["email"] = $afiliacao->dataAfiliado()->email;
                $data["produto"] = $afiliacao->dataProduto()->nome;

                $emailAfiliado = $email->afiliacaoExcluir($data);
                $email->enviaSendGrid($emailAfiliado);

                $afiliacao->destroy();
            }
        }
    }
    /** 
     * FIM AFILIADOS PRODUTO
     */

    /** 
     * COPRODUÇÃO PRODUTO
     */
    public function coproducao($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("coproducao_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'coproducao_produto'")->fetch();

        $produto = (new Produto())->findById($data["codigo"]);
        $coproducao = (new CoProdutor())->find("produto = :produto", "produto=" . $data["codigo"])->fetch(true);

        echo $this->view->render("produtos/coproducao_produto", [
            "title" => "Coprodução $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "coproducao" => $coproducao,
            "msg" => ""
        ]);
    }

    public function coproducaoCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $coproducao = new CoProdutor();

        $gerente = array(
            0 => "coprodutor",
            1 => "gerente"
        );

        $coprodutor = $coproducao->find("co_produtor = '" . $data["co_produtor"] . "' AND produto = '" . $data["produto"] . "'")->fetch(true);

        if ($data["co_produtor"] == $this->section->getId()) {
            echo "Você não pode ser coprodutor do próprio produto";
            exit;
        }
        if ($coprodutor > 0) {
            echo "Produtor já cadastrado!";
        } else {

            $coproducao->produto = $data["produto"];
            $coproducao->co_produtor = $data["co_produtor"];
            $coproducao->tipo_comissao = $data["tipo_comissao"];
            $coproducao->comissao = $data["comissao"];
            $coproducao->tipo_comissao_afiliados = @$data["tipo_comissao_afiliados"];
            $coproducao->comissao_afiliados = @$data["comissao_afiliados"];
            $coproducao->gerente = $data["gerente"];
            $coproducao->aceito = $data["aceito"];
            $coproducao->todos_afiliados = $data["todos_afiliados"];
            $coproducao->data_limite = @$data["data_limite"];
            // var_dump($coproducao);
            // exit;

            // CADASTRO DO COPRODUTOR NO PLANO
            $planos = (new Plano())->find("habilitar_comissao_coprodutores = 1 AND produto = " . $coproducao->produto)->fetch(true);
            // exit;
            if (@$planos) {
                foreach (@$planos as $plano) {
                    $planos_id = (new Plano())->findById(@$plano->id);
                    if (@$planos_id && @$planos_id->comissao_coprodutores == "") {
                        // echo "PLANO_ID";
                        @$planos_id->comissao_coprodutores = @$data["co_produtor"] . "|" . @$data["tipo_comissao"] . "|" . @$data["comissao"];
                        // var_dump(@$planos_id->comissao_coprodutores);
                    } else {
                        @$planos_id->comissao_coprodutores .= "," . @$data["co_produtor"] . "|" . @$data["tipo_comissao"] . "|" . @$data["comissao"];
                        // var_dump(@$planos_id->comissao_coprodutores);
                    }
                    $planos_id->save();
                }
            }
            // exit;
            if (@$plano && @$plano->comissao_coprodutores != "") {
                $planos_coprod = explode(",", @$plano->comissao_coprodutores);
                $i = 0;
                if (@$planos_coprod[0] && @$planos_coprod[1]) {
                    foreach (@$planos_coprod as $planos) {
                        // echo "<!-- PLANOS ---!>";
                        // var_dump($planos);
                        $id_coprod = explode("|", @$planos);
                        // echo "<!-- ID COPROD ---!>";
                        if (@$id_coprod[0] != @$coproducao->co_produtor) {
                            // var_dump(@$id_coprod);
                            $comissao_coprodutores1 = implode("|", @$id_coprod);
                            $coprod[$i] = $comissao_coprodutores1;
                            $i++;
                        }
                    }
                    $coprod = implode(",", $coprod);
                    $plano->comissao_coprodutores = @$coprod;
                } else {
                    $plano->comissao_coprodutores = "";
                }
                $plano->save();
            }
            // FIM CADASTRO DO COPRODUTOR


            if ($coproducao->save()) {
                $email = new Emails();
                $usuario = (new Usuario())->findById($coproducao->co_produtor);
                $data["nome"] = $usuario->nome;
                $data["email"] = $usuario->email;
                $data["produto"] = $coproducao->dataProduto()->nome;
                $data["gerente"] = $gerente[$coproducao->gerente];

                $coproducaoEmail = $email->coproducaoCadastro($data);
                $email->enviaSendGrid($coproducaoEmail);
            } else {
                echo "Erro ao salvar <br>";
                echo $coproducao->fail()->getMessage();
            }
        }
    }

    public function coproducaoEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $gerente = array(
            0 => "coprodutor",
            1 => "gerente"
        );

        $coproducao = (new CoProdutor())->findById($data["id"]);

        $coproducao->produto = $data["produto"];
        // $coproducao->co_produtor = $data["co_produtor"];
        $coproducao->tipo_comissao = $data["tipo_comissao"];
        $coproducao->comissao = @$data["comissao"];
        $coproducao->tipo_comissao_afiliados = @$data["tipo_comissao_afiliados"];
        $coproducao->comissao_afiliados = @$data["comissao_afiliados"];
        $coproducao->gerente = $data["gerente"];
        $coproducao->todos_afiliados = $data["todos_afiliados"];
        $coproducao->data_limite = @$data["data_limite"];



        if ($coproducao->save()) {
            $email = new Emails();
            $usuario = (new Usuario())->findById($coproducao->co_produtor);
            $data["nome"] = $usuario->nome;
            $data["email"] = $usuario->email;
            $data["produto"] = $coproducao->dataProduto()->nome;
            $data["gerente"] = $gerente[$coproducao->gerente];

            $coproducaoEmail = $email->coproducaoEditar($data);
            $email->enviaSendGrid($coproducaoEmail);
        } else {
            echo "Erro ao salvar <br>";
            echo $coproducao->fail()->getMessage();
        }
    }

    public function coproducaoExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $gerente = array(
            0 => "coprodutor",
            1 => "gerente"
        );

        $coproducao = (new CoProdutor())->findById($data["id"]);
        $plano = (new Plano())->find("produto = " . $coproducao->produto)->fetch();

        if (@$plano && @$plano->comissao_coprodutores != "") {
            $planos_coprod = explode(",", @$plano->comissao_coprodutores);
            $i = 0;
            if (@$planos_coprod[0] && @$planos_coprod[1]) {
                foreach ($planos_coprod as $planos) {
                    // echo "<!-- PLANOS ---!>";
                    // var_dump($planos);
                    $id_coprod = explode("|", @$planos);
                    // echo "<!-- ID COPROD ---!>";
                    if (@$id_coprod[0] != @$coproducao->co_produtor) {
                        // var_dump(@$id_coprod);
                        $comissao_coprodutores1 = implode("|", @$id_coprod);
                        $coprod[$i] = $comissao_coprodutores1;
                        $i++;
                    }
                }
                $coprod = implode(",", $coprod);
                $plano->comissao_coprodutores = @$coprod;
            } else {
                $plano->comissao_coprodutores = "";
            }
            $plano->save();
        }
        // exit;
        $email = new Emails();
        $usuario = (new Usuario())->findById($coproducao->co_produtor);
        $data["nome"] = $usuario->nome;
        $data["email"] = $usuario->email;
        $data["produto"] = $coproducao->dataProduto()->nome;
        $data["gerente"] = $gerente[$coproducao->gerente];

        $coproducaoEmail = $email->coproducaoExcluir($data);
        $email->enviaSendGrid($coproducaoEmail);

        if ($coproducao->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo "Erro ao excluir <br>";
            echo $coproducao->fail()->getMessage();
        }
    }
    /** 
     * FIM COPRODUÇÃO PRODUTO
     */

    /** URLS */

    public function urls($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("url_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'url_produto'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $produto = (new Produto())->findById($data["codigo"]);
        $urlCount = (new Url())->find("produto = '{$data["codigo"]}'")->count();
        $paginas = ceil($urlCount / $offset);
        $urls = (new Url())->find("produto = '{$data["codigo"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        echo $this->view->render("produtos/urls", [
            "title" => "URLs $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "urls" => $urls,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => ""
        ]);
    }

    public function urlCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $url = new Url();

        $url->descricao = $data["descricao"];
        $url->url = $data["url"];
        $url->usuario = $this->section->getId();
        $url->produto = $data["produto"];
        $url->privada = @$data["privada"] == "" ? 0 : 1;
        $url->pv = @$data["pv"] == "" ? 0 : 1;

        if ( ! $url->save()) {
            echo $url->fail()->getMessage();
        }
    }

    public function urlEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $url = (new Url())->findById($data["id"]);

        $url->descricao = $data["descricao"];
        $url->url = $data["url"];
        $url->privada = @$data["privada"] == "" ? 0 : 1;
        $url->pv = @$data["pv"] == "" ? 0 : 1;

        if ( ! $url->save() ) {
            echo $url->fail()->getMessage();
        }
    }

    public function urlExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $url = (new Url())->findById($data["id"]);
        if ($url->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $url->fail()->getMessage();
        }
    }
    /** FIM URLS */

    /**
     * SEÇÃO UPSELL
     */
    public function upsell($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("upsell_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'upsell_produto'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $produto = (new Produto())->findById($data["codigo"]);
        $checkouts = (new Checkout())->find("produto = " . $data["codigo"])->fetch(true);
        $planos = (new Plano())->find("usuario = " . $this->section->getId())->fetch(true);
        $upsellCount = (new Upsell())->find("produto = '{$data["codigo"]}'")->count();
        $paginas = ceil($upsellCount / $offset);
        $upsell = (new Upsell())->find("produto = '{$data["codigo"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        echo $this->view->render("produtos/upsell", [
            "title" => "Upsell $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "checkouts" => @$checkouts,
            "planos" => @$planos,
            "upsell" => $upsell,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function upsellCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $resultado_bytes = random_bytes(4);
        $codigo = strtoupper(bin2hex($resultado_bytes));

        $upsell = new Upsell();

        $upsell->codigo = $codigo;
        $upsell->produto = $data["produto"];
        $upsell->checkout = @$data["checkout"] ? $data["checkout"] : NULL;
        $upsell->plano = @$data["plano"];
        $upsell->ativar = @$data["ativar"];
        $upsell->boleto = $data["boleto"];
        $upsell->headline = $data["headline"];
        $upsell->subheadline = $data["subheadline"];
        $upsell->usuario = $this->section->getId();

        if ($data["headline"] == "") {
            echo "Preencha todos os campos marcados com *";
        } else {

            if ($upsell->save()) {
                echo "Upsell cadastrado com sucesso";
            } else {
                echo $upsell->fail()->getMessage();
            }
        }
    }

    public function upsellEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $upsell = (new Upsell())->findById($data["id"]);

        $upsell->checkout = @$data["checkout"] ? $data["checkout"] : NULL;
        $upsell->plano = @$data["plano"];
        $upsell->ativar = @$data["ativar"];
        $upsell->boleto = $data["boleto"];
        $upsell->headline = $data["headline"];
        $upsell->subheadline = $data["subheadline"];

        if ($upsell->save()) {
            echo "Upsell atualizado com sucesso";
        } else {
            echo $upsell->fail()->getMessage();
        }
    }

    public function upsellExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $upsell = (new Upsell())->findById($data["id"]);
        if ($upsell->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $upsell->fail()->getMessage();
        }
    }
    /**
     * FIM SEÇÃO UPSELL
     */

    public function campanhas($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("campanha_produto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'campanha_produto'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 10;

        $afiliacao = new Afiliacao();
        $checkout = new Checkout();
        $pixel = (new Pixel())->find("usuario = {$this->section->getId()}")->fetch(true);

        $produto = (new Produto())->findById($data["codigo"]);
        $campanhasCount = (new Campanha())->find("usuario = '{$this->section->getId()}'")->count();
        $paginas = ceil($campanhasCount / $offset);
        $campanhas = (new Campanha())->find("produto = '{$data["codigo"]}' AND usuario = '{$this->section->getId()}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        $pixel = (new Pixel())->find("usuario = '{$this->section->getId()}'")->fetch(true);

        $urls = (new Url())->find("produto = " . $produto->id)->fetch(true);

        echo $this->view->render("produtos/campanhas", [
            "title" => "Campanhas $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "urls" => $urls,
            "campanhas" => $campanhas,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "afiliacao" => $afiliacao,
            "checkout" => $checkout,
            "pixel" => $pixel,
            "msg" => ""
        ]);
    }

    public function campanhaCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $codigo = mt_rand(10000, 999999);

        $campanha = new Campanha();

        $campanha->codigo = $codigo;
        $campanha->nome = $data["nome"];
        $campanha->produto = $data["produto"];
        $campanha->usuario = $this->section->getId();
        $campanha->url_destino = $data["url_destino"];
        $campanha->analytics = @$data["analytics"];

        $campanha->pixel_facebook = @$data["pixel_facebook"] ? $data["pixel_facebook"] : NULL;
        $campanha->pixel_facebook_api = @$data["pixel_facebook_api"] ? $data["pixel_facebook_api"] : NULL;
        $campanha->pixel_google_analitics = @$data["pixel_google_analitics"] ? @$data["pixel_google_analitics"] : NULL;
        $campanha->pixel_google_ads = @$data["pixel_google_ads"] ? @$data["pixel_google_ads"] : NULL;
        $campanha->pixel_google_tag_manager = @$data["pixel_google_tag_manager"] ? @$data["pixel_google_tag_manager"] : NULL;
        $campanha->pixel_taboola = @$data["pixel_taboola"] ? $data["pixel_taboola"] : NULL;
        $campanha->pixel_tiktok = @$data["pixel_tiktok"] ? $data["pixel_tiktok"] : NULL;
        $campanha->pixel_kwai = @$data["pixel_kwai"] ? $data["pixel_kwai"] : NULL;

        if ( ! $campanha->save() ) {
            echo $campanha->fail()->getMessage();
        }
    }

    public function campanhaEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $campanha = (new Campanha())->findById($data["id"]);

        $campanha->nome = $data["nome"];
        $campanha->url_destino = $data["url_destino"];
        $campanha->analytics = @$data["analytics"];

        $campanha->pixel_facebook = @$data["pixel_facebook"] ? $data["pixel_facebook"] : NULL;
        $campanha->pixel_facebook_api = @$data["pixel_facebook_api"] ? $data["pixel_facebook_api"] : NULL;
        $campanha->pixel_google_analitics = @$data["pixel_google_analitics"] ? @$data["pixel_google_analitics"] : NULL;
        $campanha->pixel_google_ads = @$data["pixel_google_ads"] ? @$data["pixel_google_ads"] : NULL;
        $campanha->pixel_google_tag_manager = @$data["pixel_google_tag_manager"] ? @$data["pixel_google_tag_manager"] : NULL;
        $campanha->pixel_taboola = @$data["pixel_taboola"] ? $data["pixel_taboola"] : NULL;
        $campanha->pixel_tiktok = @$data["pixel_tiktok"] ? $data["pixel_tiktok"] : NULL;
        $campanha->pixel_kwai = @$data["pixel_kwai"] ? $data["pixel_kwai"] : NULL;

        if ( ! $campanha->save() ) {
            echo $campanha->fail()->getMessage();
        }
    }

    public function campanhaExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $campanha = (new Campanha())->findById($data["id"]);
        if ($campanha->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $campanha->fail()->getMessage();
        }
    }

    // Produtor atualiza informações do produto para o televendas
    public function televendas($data)
    {
        $produto = (new Produto())->findById($data["codigo"]);
        $televendas = (new Televendas())->find("usuario = {$this->section->getId()} AND produto = {$data["codigo"]}")->fetch();

        echo $this->view->render("produtos/televendas_produto", [
            "title" => $produto->nome,
            "produto" => $produto,
            "televendas" => $televendas,
        ]);
    }

    // Produtor acessa o Menu Televendas (cadastro produto no televendas)
    public function televendasCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $files = new File();
        // var_dump($data);
        // exit;
        $produto = (new Produto())->find("id = {$data["produto_id"]} AND usuario = {$this->section->getId()}")->fetch();

        // Verifica se o produtor está mudando apenas o status
        if (@$data["ativo"] == 1) {
            $produto->tipo_comissao_afiliado = @$data["tipo_comissao1"];
            $produto->comissao_afiliado_televendas = @$data["comissao1"];
            $produto->habilitar_televendas = @$data["ativo"];
            $produto->descricao_televendas = @$data["descricao"];
            $produto->documentos_televendas = $files->upload($_FILES["documentos"], PASTA . "documentos/", 10);
            $produto->eventos_televendas = implode(",", $data["evento"]);
        } else {
            $produto->habilitar_televendas = @$data["ativo"];
        }

        if ($produto->save()) {
            // $data["evento"] = $produto->eventos_televendas;
            // Envia produto para o televendas
            $msg = $this->enviaTelevendas($data);

            // exit;
            $msg .= "Sucesso";
            $classCss = "bg-success";
        } else {
            $msg = $produto->fail()->getMessage();
            $classCss = "bg-danger";
        }

        header("Location: " . url("produtos/televendas/" . $data["produto_id"] . "?msg=" . $msg . "&classCss=" . $classCss));
        // header("Location: " . url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    // Envia os dados do produto para o sistema de televendas
    public function enviaTelevendas($data)
    {
        $produto = (new Produto())->find("id = {$data["produto_id"]} AND usuario = {$this->section->getId()}")->fetch();

        if (@$produto) {
            $data["codigo"] = $produto->codigo;
            $data["nome"] = $produto->nome;
            // $data["usuario"] = $produto->usuario;
            $data["descricao"] = $produto->descricao;
            $data["categoria"] = $produto->categoria;
            $data["tipo_produto"] = $produto->tipo_produto;

            // DADOS PARA CADASTRO DE PRODUTOR
            $data["id_produtor"] = $produto->dataProdutorTelevendas()->id;
            $data["nome_produtor"] = $produto->dataProdutorTelevendas()->nome;
            $data["email"] = $produto->dataProdutorTelevendas()->email;
            $data["tipo_doc"] = $produto->dataProdutorTelevendas()->tipo_doc;
            $data["cpf_cnpj"] = $produto->dataProdutorTelevendas()->cpf_cnpj;
            $data["telefone"] = $produto->dataProdutorTelevendas()->telefone;
            $data["produ_data_nasc"] = $produto->dataProdutorTelevendas()->data_nascimento;
            $data["cep"] = $produto->dataProdutorTelevendas()->cep;
            $data["rua"] = $produto->dataProdutorTelevendas()->rua;
            $data["numero"] = $produto->dataProdutorTelevendas()->numero;
            $data["complemento"] = $produto->dataProdutorTelevendas()->complemento;
            $data["bairro"] = $produto->dataProdutorTelevendas()->bairro;
            $data["cidade"] = $produto->dataProdutorTelevendas()->cidade;
            $data["estado"] = $produto->dataProdutorTelevendas()->estado;
            $data["pais"] = $produto->dataProdutorTelevendas()->pais;
            // FIM DADOS PARA CADASTRO DE PRODUTOR

            $data["membros"] = $produto->membros;
            $data["url_membros"] = $produto->url_membros;
            $data["link_rastreio"] = $produto->link_rastreio;
            $data["tipo_cobranca"] = $produto->tipo_cobranca;
            $data["preco"] = $produto->preco;
            $data["email_suporte"] = $produto->email_suporte;
            $data["whatsapp_checkout"] = $produto->whatsapp_checkout;
            $data["status"] = $produto->status;
            $data["frete_fixo"] = $produto->frete_fixo;
            $data["pagina_venda"] = $produto->pagina_venda;
            $data["url_midias"] = $produto->url_midias;
            $data["foto"] = $produto->foto;
            $data["garantia"] = $produto->garantia;
            $data["disponivel"] = $produto->disponivel;
            $data["documentos"] = $produto->documentos;
            $data["descricao_atendente"] = $produto->descricao_atendente;
            $data["abrir_atendimento"] = $produto->abrir_atendimento;
            $data["atendente_aprovacao"] = $produto->atendente_aprovacao;
            $data["visualizacao"] = $produto->visualizacao;
            $data["tipo_comissao"] = $produto->tipo_comissao;
            $data["comissao_atendente"] = $produto->comissao_atendente;
            $data["tipo_atribuicao"] = $produto->tipo_atribuicao;
            $data["habilitar_televendas"] = $data["ativo"];
            $data["descricao_televendas"] = $produto->descricao_televendas;
            $data["documentos_televendas"] = $produto->documentos_televendas;
            $data["evento"] = $produto->eventos_televendas;
            $data["tipo_comissao_afiliado"] = $produto->tipo_comissao_afiliado;
            $data["comissao_afiliado_televendas"] = $produto->comissao_afiliado_televendas;
            $data["usuario_ultima_alteracao"] = $produto->usuario_ultima_alteracao;

            $curl = curl_init();
            $url = url_televendas("televendas/produtos");
            // $url_foto = url("/uploads/produtos/" .$produto->foto);
            // var_dump($data);
            // exit;
            curl_setopt_array($curl, array(
                CURLOPT_URL => "$url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data
            ));

            $response = curl_exec($curl);
            // exit;
            curl_close($curl);
            // echo $response;

            return $response;
        }
    }

    public function produtosPromovidos($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("produtos_promovidos");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'produtos_promovidos'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $filtros = $this->filtros();
        if ($filtros) {
            $filtros .= " AND ";
        }

        $produto = new Produto();

        $id = @$this->section->getId();
        $produtosCount = (new Afiliacao())->find($filtros . "afiliado = " . $id)->count();
        $paginas = ceil($produtosCount / $offset);
        $afiliacao = (new Afiliacao())->find($filtros . "afiliado = " . $id)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        //var_dump($afiliacao);

        echo $this->view->render("produtos/produtos-promovidos", [
            "title" => "Produtos Que Promovo",
            "section" => @$section,
            "permissao" => @$permissao,
            "afiliacao" => $afiliacao,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function minhasCoproducoes($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("minhas_coproducoes");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'minhas_coproducoes'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $filtros = $this->filtros();
        if ($filtros) {
            $filtros .= " AND ";
        }

        $produto = new Produto();

        $id = @$section->getId();
        $produtosCount = (new CoProdutor())->find($filtros . "co_produtor = $id")->count();
        $paginas = ceil($produtosCount / $offset);
        $coproducao = (new CoProdutor())->find($filtros . "co_produtor = $id")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        //var_dump($afiliacao);

        echo $this->view->render("produtos/minhas-coproducoes", [
            "title" => "Minhas Coproduções",
            "section" => @$section,
            "permissao" => @$permissao,
            "coproducao" => $coproducao,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function aceitarCoproducao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $coproducao = (new CoProdutor())->findById($data["id"]);
        $coproducao->aceito = $data["aceito"];

        if ($coproducao->save()) {
            echo "Salvo com sucesso!";
        } else {
            echo $coproducao->fail()->getMessage();
        }
    }
}

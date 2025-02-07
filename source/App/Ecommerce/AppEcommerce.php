<?php

//CLASSE QUE CONTROLA AS ROTAS DOS AFILIADOS

namespace Source\App\Ecommerce;

session_start();

use League\Plates\Engine;
use Source\App\RedrocketAnalytics\AppRedrocketAnalytics;
use Source\Models\Ecommerce\Ecommerce;
use Source\Models\Produtos\Produto;

use Source\App\Sessao\AppSessao;

use Source\Helpers\File;
use Source\Models\Checkout\Checkout;
use Source\Models\Configuracoes\Taxa;
use Source\Models\Pixel\Pixel;
use Source\Models\Produtos\Plano;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;

class AppEcommerce
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    public function modoCobranca($data)
    {
        $usuario = (new Usuario())->findById($data["id"]);
        $usuario->assinatura_ecommerce = $data["assinatura"];

        if (@$data["assinatura"] == "taxa")
            $usuario->taxa_ecommerce = $data["taxa"];
        else
            $usuario->valor_ecommerce = $data["taxa"];

        if ($usuario->save()) {
            echo "Salvo com sucesso";
        } else {
            echo "Erro ao salvar";
        }
    }

    public function ecommerce($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $usuario = $_SESSION["id"];

        $ecommerce = (new Ecommerce())->find("usuario = $usuario")->fetch();
        $produtos = (new Produto())->find("status = 2 AND habilitar_ecommerce = 1 AND usuario = $usuario AND excluir = 0")->fetch(true);
        $pixel = (new Pixel())->find("usuario = '{$_SESSION['id']}'")->fetch(true);
        $taxa = (new Taxa())->findById(1);
        $usuarioDados = (new UsuarioDados())->find("usuario = $usuario")->fetch();

        // var_dump($ecommerce);
        echo $this->view->render("ecommerce/ecommerce", [
            "title" => "Ecommerce",
            "ecommerce" => $ecommerce,
            "produtos" => $produtos,
            "pixel" => $pixel,
            "taxa" => $taxa,
            "usuarioDados" => $usuarioDados,
        ]);
    }

    public function cadastrarEcommerce($data)
    {
        $usuario = $_SESSION["id"];

        $ecommerce = (new Ecommerce())->find("usuario = $usuario")->fetch();

        if (!$ecommerce) {
            $ecommerce = new Ecommerce();
        }

        $destaque = "";
        if (@$data["destaques"]) {
            $cont = count($data["destaques"]);

            $i = 1;
            foreach ($data["destaques"] as $dest) {
                if ($i < $cont)
                    $destaque .= "$dest,";
                else
                    $destaque .= $dest;

                $i++;
            }
        }

        $files = new File();
        // $sizeDoc1 = $_FILES["documento1"]["size"] / 8192 / 8192;

        function tirarAcentos($string)
        {
            return preg_replace(array('/[@\.\;\""]+/', "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a a e e i i o o u u n n"), $string);
        }

        $ecommerce->habilitar = @$data["habilitar"];
        $ecommerce->usuario = $_SESSION["id"];
        $ecommerce->nome_loja = @$data["nome_loja"];
        $ecommerce->url_loja = tirarAcentos(@$data["url_loja"]);
        $ecommerce->logo = @$data["logo"];
        $ecommerce->banner = @$data["banner"];
        $ecommerce->destaques = @$destaque;
        $ecommerce->cor_titulo = @$data["cor_titulo"];
        $ecommerce->cor_destaque = @$data["cor_destaque"];
        $ecommerce->cor_texto = @$data["cor_texto"];
        $ecommerce->cor_tarja = @$data["cor_tarja"];
        $ecommerce->cor_botao = @$data["cor_botao"];
        $ecommerce->cor_texto_botao = @$data["cor_texto_botao"];
        $ecommerce->informacoes_loja = @$data["informacoes_loja"];
        $ecommerce->duvidas = @$data["duvidas"];

        $ecommerce->email_loja = @$data["email_loja"];
        $ecommerce->telefone_loja = @$data["telefone_loja"];
        $ecommerce->cep_loja = @$data["cep_loja"];
        $ecommerce->rua_loja = @$data["rua_loja"];
        $ecommerce->numero_loja = @$data["numero_loja"];
        $ecommerce->complemento_loja = @$data["complemento_loja"];
        $ecommerce->bairro_loja = @$data["bairro_loja"];
        $ecommerce->cidade_loja = @$data["cidade_loja"];
        $ecommerce->estado_loja = @$data["estado_loja"];

        $ecommerce->pixel_facebook = @$data["pixel_facebook"] ? $data["pixel_facebook"] : NULL;
        $ecommerce->pixel_facebook_api = @$data["pixel_facebook_api"] ? $data["pixel_facebook_api"] : NULL;
        $ecommerce->pixel_google_analytics = @$data["pixel_google_analytics"] ? @$data["pixel_google_analitics"] : NULL;
        $ecommerce->pixel_google_ads = @$data["pixel_google_ads"] ? @$data["pixel_google_ads"] : NULL;
        $ecommerce->pixel_google_tag_manager = @$data["pixel_google_tag_manager"] ? @$data["pixel_google_tag_manager"] : NULL;
        $ecommerce->pixel_taboola = @$data["pixel_taboola"] ? $data["pixel_taboola"] : NULL;
        $ecommerce->pixel_tiktok = @$data["pixel_tiktok"] ? $data["pixel_tiktok"] : NULL;

        if (@$_FILES["logo"]["name"])
            $ecommerce->logo = $files->upload(@$_FILES["logo"], PASTA . "ecommerce/", 2);
        elseif (@$data["logo-atual"])
            $ecommerce->logo = $data["logo-atual"];

        if (@$_FILES["banner"]["name"])
            $ecommerce->banner = $files->upload(@$_FILES["banner"], PASTA . "ecommerce/", 3);
        elseif (@$data["banner-atual"])
            $ecommerce->banner = $data["banner-atual"];

        // var_dump($data["destaques"]);
        // exit;

        if ($ecommerce->save()) {
            $msg = "Cadastrado Com Sucesso";
            $classCss = "bg-success";
        } else {
            $msg = $ecommerce->fail()->getMessage();
            $classCss = "bg-danger";
        }

        header("Location: " . url("ecommerce?msg=$msg&classCss=$classCss"));
    }

    public function buscarUrlLoja($data)
    {
        $loja = (new Ecommerce())->find("id != {$data['loja']} AND url_loja = '{$data['url']}'")->fetch();

        if ($loja) {
            echo "true";
        } else {
            echo "false";
        }
    }

    public function loja($data)
    {
        $loja = (new Ecommerce())->find("url_loja = '{$data["url_loja"]}'")->fetch();
        $produtoObj = new Produto();

        $cat = $this->categorias(null);
        $cat2 = $this->urlCategorias(null);

        if (@$data["categoria"]) {
            $produtos = (new Produto())->find("categoria = {$cat2[$data['categoria']]} AND status = 2 AND habilitar_ecommerce = 1 AND usuario = $loja->usuario AND excluir = 0")->order("rockets DESC")->fetch(true);
            $isCategoria = true;
        } else {
            $produtos = (new Produto())->find("status = 2 AND habilitar_ecommerce = 1 AND usuario = $loja->usuario AND excluir = 0")->order("rockets DESC")->fetch(true);
            $isCategoria = false;
        }

        $produtosCategoria = (new Produto())->find("status = 2 AND habilitar_ecommerce = 1 AND usuario = $loja->usuario AND excluir = 0", null, "DISTINCT(categoria) as categoria")->order("rockets DESC")->fetch(true);
        $cat = $this->categorias(null);
        $user = (new UsuarioDados())->find("usuario = $loja->usuario")->fetch();

        if ($produtosCategoria) {
            foreach ($produtosCategoria as $prod) {
                $categorias[] = array(
                    "id" => $prod->categoria,
                    "nome" => $cat[$prod->categoria][0],
                    "url" => $cat[$prod->categoria][1]
                );
            }
            // var_dump($categorias);
        }
        // echo @$data["url_loja"];
        // var_dump($loja->nome_loja);

        if ($loja) {
            if (@!$_COOKIE["analytics"]) {
                $numero_de_bytes = 4;
                $resultado_bytes = random_bytes($numero_de_bytes);
                $codigo_cookie = strtoupper(bin2hex($resultado_bytes));

                setcookie("analytics", $codigo_cookie, time() + (60 * 60 * 24 * 10000), "/");
            }

            $dados["url"]           = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
            $dados["url_anterior"]  = @$_SERVER['HTTP_REFERER'];
            $dados["navegador"]     = @$_COOKIE["analytics"];
            $dados["produtor"]      = @$loja->usuario;
            (new AppRedrocketAnalytics(null))->cadastrarAnalytics($dados);

            echo $this->view->render("ecommerce/loja", [
                "title" => $loja->nome_loja,
                "loja" => $loja,
                "produtoObj" => $produtoObj,
                "produtos" => $produtos,
                "categorias" => @$categorias,
                "isCategoria" => @$isCategoria,
                "user" => @$user,
            ]);
        }
    }

    public function buscaProduto($data)
    {
        // $this->sessao = (new AppSessao())->verificaSeLogado();

        if (@$data["pesquisa"] == "") {
            echo "false";
            exit;
        }

        if (@$data["pesquisa"]) {
            $busca = "(codigo = '{$data["pesquisa"]}' OR nome LIKE '%{$data["pesquisa"]}%') AND ";
        } else {
            $busca = "";
        }

        $produtos = (new Produto())->find($busca . "usuario = {$data["usuario"]} AND excluir = 0 ")->order("rockets DESC")->fetch(true);

        if ($produtos) {
            $resposta = "<br>
            <div class='container secao-destaques'>
                <div class='row'>
                    <div class='col-md-12'>
                        <div class='border-tarja'>
                            <span class='texto-tarja tarja'>Resultados</span>
                        </div>
                    </div>
                </div>

                <br>
                <div class='row'>";
            foreach ($produtos as $produto) :
                $resposta .= "
                    <div class='col-md-3'>
                        <div class='card card-body card-produto'>
                            <a class='texto-loja link-produto' href='" . url("produtos/detalhe/$produto->id") . "'>
                                <img class='imagem-produto' src='" . url("uploads/produtos/$produto->foto") . "' alt=''>

                                <div class='descricao-produto'>
                                    <span>$produto->nome<br></span>
                                    <span><small class='texto-loja'>por: </small> <b class='texto-1'>R$" . number_format($produto->preco, 2, ",", ".") . "</b></span><br>
                                </div>

                                <button class='btn btn-success'>Comprar agora</button>
                            </a>
                        </div>
                    </div>";

            endforeach;
            $resposta .= "
                </div>
            </div>";
        } else {
            $resposta = "<br>
                        <div class='container'>
                            <div class='card card-body'>
                                Nenhum produto encontrado
                            </div>
                        </div>
                        <br>
            ";
        }

        echo @$resposta;
    }

    public function produtoLoja($data)
    {
        $loja = (new Ecommerce())->find("url_loja = '{$data["url_loja"]}'")->fetch();
        $produtoObj = new Produto();
        $checkoutObj = new Checkout();
        $produto = (new Produto())->findById($data["produto"]);

        if ($produto) {
            $planos = (new Plano())->find("ativo = 1 AND status = 2 AND habilitar_ecommerce = 1 AND produto = $produto->id")->order("preco DESC")->fetch(true);
        }
        $produtosCategoria = (new Produto())->find("status = 2 AND habilitar_ecommerce = 1 AND usuario = $loja->usuario AND excluir = 0", null, "DISTINCT(categoria) as categoria")->order("rockets DESC")->fetch(true);
        $cat = $this->categorias(null);
        $user = (new UsuarioDados())->find("usuario = $loja->usuario")->fetch();

        if ($produtosCategoria) {
            foreach ($produtosCategoria as $prod) {
                $categorias[] = array(
                    "id" => $prod->categoria,
                    "nome" => $cat[$prod->categoria][0],
                    "url" => $cat[$prod->categoria][1]
                );
            }
        }
        // echo @$data["url_loja"];
        // var_dump($loja->nome_loja);

        if ($loja) {
            if (@!$_COOKIE["analytics"]) {
                $numero_de_bytes = 4;
                $resultado_bytes = random_bytes($numero_de_bytes);
                $codigo_cookie = strtoupper(bin2hex($resultado_bytes));

                setcookie("analytics", $codigo_cookie, time() + (60 * 60 * 24 * 10000), "/");
            }

            $dados["url"]           = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
            $dados["url_anterior"]  = @$_SERVER['HTTP_REFERER'];
            $dados["navegador"]     = @$_COOKIE["analytics"];
            $dados["produto"]       = @$data["produto"];
            $dados["produtor"]      = @$loja->usuario;
            (new AppRedrocketAnalytics(null))->cadastrarAnalytics($dados);

            echo $this->view->render("ecommerce/produto-loja", [
                "title" => $loja->nome_loja,
                "loja" => $loja,
                "produtoObj" => $produtoObj,
                "checkoutObj" => $checkoutObj,
                "produto" => $produto,
                "planos" => $planos,
                "categorias" => @$categorias,
                "user" => @$user,
            ]);
        }
    }

    public function urlCategorias($data)
    {
        $urlCategorias = array(
            "administracao-e-negocios" => 1,
            "animais-de-estimacao" => 2,
            "arquitetura-e-engenharia" => 3,
            "artes-e-musica" => 4,
            "auto-ajuda-e-desenvolvimento-pessoal" => 5,
            "automoveis" => 6,
            "blogs-e-redes-sociais" => 7,
            "casa-e-jardinagem" => 8,
            "culinaria-gastronomia-receitas" => 9,
            "design-e-templates-psd-ppt-ou-html" => 10,
            "edicao-de-eudio-video-ou-imagens" => 11,
            "educacional-cursos-tecnicos-e-profissionalizantes" => 12,
            "eletrodomesticos" => 13,
            "eletroportateis" => 14,
            "entretenimento-lazer-e-diversao" => 15,
            "esportes-e-fitness" => 16,
            "filmes-e-cinema" => 17,
            "geral" => 18,
            "historias-em-quadrinhos" => 19,
            "idiomas" => 20,
            "informatica" => 21,
            "internet-marketing" => 22,
            "investimentos-e-financas" => 23,
            "jogos-de-cartas-poker-loterias" => 24,
            "jogos-de-computador-jogos-online" => 25,
            "juridico" => 26,
            "literatura-e-poesia" => 27,
            "marketing-de-rede" => 28,
            "marketing-e-comunicacao" => 29,
            "meio-ambiente" => 30,
            "moda-e-vestuario" => 31,
            "moveis-e-decoracao" => 32,
            "musica-bandas-e-shows" => 33,
            "paquera-seducao-e-relacionamentos" => 34,
            "perfumaria" => 35,
            "pessoas-com-deficiencia" => 36,
            "plugins-widgets-e-extensoes" => 37,
            "produtividade-e-organizacao-pessoal" => 38,
            "produtos-infantis" => 39,
            "relatorios-artigos-e-pesquisas" => 40,
            "religiao-e-crencas" => 41,
            "romances-dramas-estorias-e-contos" => 42,
            "rpg-e-jogos-de-mesa" => 43,
            "saude-bem-estar-e-beleza" => 44,
            "scripts" => 45,
            "seguranca-do-trabalho" => 46,
            "sexologia-e-sexualidade" => 47,
            "snippets-(trechos-de-video)" => 48,
            "trader" => 49,
            "turismo" => 50
        );

        return $urlCategorias;
    }

    public function categorias($data)
    {
        $categorias = array(
            1 => ["Administração e Negócios", "administracao-e-negocios"],
            2 => ["Animais de Estimação", "animais-de-estimacao"],
            3 => ["Arquitetura e Engenharia", "arquitetura-e-engenharia"],
            4 => ["Artes e Música", "artes-e-musica"],
            5 => ["Auto-ajuda e Desenvolvimento Pessoal", "auto-ajuda-e-desenvolvimento-pessoal"],
            6 => ["Automóveis", "automoveis"],
            7 => ["Blogs e Redes Sociais", "blogs-e-redes-sociais"],
            8 => ["Casa e Jardinagem", "casa-e-jardinagem"],
            9 => ["Culinária, Gastronomia, Receitas", "culinaria-gastronomia-receitas"],
            10 => ["Design e Templates PSD, PPT ou HTML", "design-e-templates-psd-ppt-ou-html"],
            11 => ["Edição de Áudio, Vídeo ou Imagens", "edicao-de-eudio-video-ou-imagens"],
            12 => ["Educacional, Cursos Técnicos e Profissionalizantes", "educacional-cursos-tecnicos-e-profissionalizantes"],
            13 => ["Eletrodomésticos", "eletrodomesticos"],
            14 => ["Eletroportáteis", "eletroportateis"],
            15 => ["Entretenimento, Lazer e Diversão", "entretenimento-lazer-e-diversao"],
            16 => ["Esportes e Fitness", "esportes-e-fitness"],
            17 => ["Filmes e Cinema", "filmes-e-cinema"],
            18 => ["Geral", "geral"],
            19 => ["Histórias em Quadrinhos", "historias-em-quadrinhos"],
            20 => ["Idiomas", "idiomas"],
            21 => ["Informática", "informatica"],
            22 => ["Internet Marketing", "internet-marketing"],
            23 => ["Investimentos e Finanças", "investimentos-e-financas"],
            24 => ["Jogos de Cartas, Poker, Loterias", "jogos-de-cartas-poker-loterias"],
            25 => ["Jogos de Computador, Jogos Online", "jogos-de-computador-jogos-online"],
            26 => ["Jurídico", "juridico"],
            27 => ["Literatura e Poesia", "literatura-e-poesia"],
            28 => ["Marketing de Rede", "marketing-de-rede"],
            29 => ["Marketing e Comunicação", "marketing-e-comunicacao"],
            30 => ["Meio Ambiente", "meio-ambiente"],
            31 => ["Moda e vestuário", "moda-e-vestuario"],
            32 => ["Moveis e decoração", "moveis-e-decoracao"],
            33 => ["Música, Bandas e Shows", "musica-bandas-e-shows"],
            34 => ["Paquera, Sedução e Relacionamentos", "paquera-seducao-e-relacionamentos"],
            35 => ["Perfumaria", "perfumaria"],
            36 => ["Pessoas com deficiência", "pessoas-com-deficiencia"],
            37 => ["Plugins, Widgets e Extensões", "plugins-widgets-e-extensoes"],
            38 => ["Produtividade e Organização Pessoal", "produtividade-e-organizacao-pessoal"],
            39 => ["Produtos infantis", "produtos-infantis"],
            40 => ["Relatórios, Artigos e Pesquisas", "relatorios-artigos-e-pesquisas"],
            41 => ["Religião e Crenças", "religiao-e-crencas"],
            42 => ["Romances, Dramas, Estórias e Contos", "romances-dramas-estorias-e-contos"],
            43 => ["RPG e Jogos de Mesa", "rpg-e-jogos-de-mesa"],
            44 => ["Saúde, Bem-estar e Beleza", "saude-bem-estar-e-beleza"],
            45 => ["Scripts", "scripts"],
            46 => ["Segurança do Trabalho", "seguranca-do-trabalho"],
            47 => ["Sexologia e Sexualidade", "sexologia-e-sexualidade"],
            48 => ["Snippets (Trechos de Vídeo)", "snippets-(trechos-de-video)"],
            49 => ["Trader", "trader"],
            50 => ["Turismo", "turismo"]
        );

        return $categorias;
    }
}

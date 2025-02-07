<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\Cookies;

use League\Plates\Engine;
use Source\Models\Cookies\Cookies;
use Source\Models\Checkout\Checkout;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Usuarios\Usuario;
use Source\Models\Campanhas\Campanha;
use Source\Models\URL\Url;
use Source\Models\Sessao\Sessao;
use Source\Models\Cookies\cookiesTelevendas;
use Source\App\Sessao\AppSessao;
use Source\App\RedrocketAnalytics\AppRedrocketAnalytics;
use Source\Models\Produtos\Produto;

session_start();

class AppCookies
{
    private $view;
    private $sessao;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema2", "php");
        $this->view->addData(["router" => $router]);
    }

    public function cookies($data)
    {
        $codigo = $data["afiliacao"];

        $tipo         = @$_GET["tp"];
        $checkout     = @$_GET["ch"];
        $cp           = @$_GET["cp"];
        $pg           = @$_GET["pg"];

        $src          = @$_GET["src"];
        $utm_source   = @$_GET["utm_source"];
        $utm_campaign = @$_GET["utm_campaign"];
        $utm_medium   = @$_GET["utm_medium"];
        $utm_content  = @$_GET["utm_content"];
        $rastro       = "&src=$src&utm_source=$utm_source&utm_medium=$utm_medium&utm_campaign=$utm_campaign&utm_content=$utm_content";

        if (@$cp) {
            $campanha = (new Campanha())->find("codigo = $cp")->fetch();
            $idCamp = $campanha->id;
            $prod = $campanha->produto;
            $analytics = $campanha->analytics;

            if ($campanha->pixel) {
                $plataforma = $campanha->dataPixel()->plataforma;
                $id_pixel = $campanha->dataPixel()->id_pixel;
                $boleto = $campanha->dataPixel()->boleto;
                $executar_checkout = $campanha->dataPixel()->executar_checkout;
                $valor = $campanha->dataPixel()->valor;
            }

            $cookie_cp = "idCamp=$idCamp&campanha=$cp&produto=$prod&analytics=$analytics&plataforma={$plataforma}&id_pixel={$id_pixel}&boleto={$boleto}&executar_checkout={$executar_checkout}&valor={$valor}";

            setcookie("campanha", $cookie_cp, time() + (60 * 60 * -24), "/");
            setcookie("campanha", $cookie_cp, time() + (60 * 60 * 24), "/");

            echo "<title>Checkout</title>";

            echo "<br> URL destino: <br>";
            echo $campanha->url_destino . " - " . $checkout . "<br>";

            if ($checkout == '0') {
                $paginaCP = $campanha->dataProduto()->pagina_venda . "?cp=" . $cp;
            } else {
                $paginaCP = url("checkout/" . $checkout . "?cp=" . $cp);
            }
            echo $paginaCP;
        }

        if ($tipo == 5 || $tipo == 4) {
            $afiliacao = (new Afiliacao())->find("codigo = '$codigo'")->fetch();

            $sessao = (new Sessao())->find("ip = '" . $_SERVER['REMOTE_ADDR'] . "'")->fetch(true);
            echo "<br><br>IP: " . $_SERVER['REMOTE_ADDR'] . "<br>";
            //var_dump($_SESSION);

            $produto = $afiliacao ? @$afiliacao->dataProduto()->codigo : "";

            /*if($sessao || @$_SESSION["id"]){
                echo "Logado<br>";
                setcookie($produto, "", time() + (60 * 60 * -24 * $afiliacao->tempo_cookie), "/");
            } else {*/

            $cookie_name = $produto;
            $cookie_value = "codigo=$afiliacao->codigo&produto=$afiliacao->produto&afiliado=$afiliacao->afiliado&tipo_comissao=$afiliacao->tipo_comissao&comissao=$afiliacao->comissao_afiliado&tipo_atribuicao=$afiliacao->tipo_atribuicao";

            $afiliado = @$_COOKIE[$cookie_name];
            if ($afiliado == "") {
                setcookie($cookie_name, $cookie_value . $rastro, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");
            } else {
                parse_str($afiliado, $data);
                echo "<br><br>codigo " . $data["codigo"] . "<br>";

                if ($data["tipo_atribuicao"] == 2) {
                    setcookie($cookie_name, $cookie_value, time() + (60 * 60 * -24 * $afiliacao->tempo_cookie), "/");
                    setcookie($cookie_name, $cookie_value, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");
                }
            }
            //}

            setcookie("rastro", $rastro, time() + (60 * 60 * -24), "/");
            setcookie("rastro", $rastro, time() + (60 * 60 * 24), "/");

            echo "<title>Checkout</title>";
            echo "<br>Tipo de atribuição " . @$data["tipo_atribuicao"] . "<br>";
            //var_dump(@$_COOKIE[$cookie_name], @$_COOKIE["rastro"]);

            echo "<br> URL destino: <br>";
            echo "Checkout " . $checkout . "<br>";
        }

        if (@$pg) {
            $url = (new Url())->findById($pg);
            echo "URL: $url->url <br>";
            //header("Location: " . $url->url);
        }

        if ($checkout && $checkout != '0') {
            $pagina = url("checkout/" . $checkout);
        } else {
            //$pagina = $afiliacao->dataProduto()->pagina_venda;
            $pagina = $url->url;
        }
        echo "Pagina: $pagina <br>";

        /*if($paginaCP)
            header("Location: " . $paginaCP);
        else*/
        header("Location: " . $pagina);
    }

    public function cookies2($data)
    {
        $codigo = $data["afiliacao"];
        $produto = $data["produto"];

        $data["codigo_afiliacao"] = $codigo;

        $tipo         = @$_GET["tp"]; // TIPO
        $checkout     = @$_GET["ch"]; // CHECKOUT
        $cp           = @$_GET["cp"]; // CAMPANHA
        $pg           = @$_GET["pg"]; // PÁGINA

        $src          = @$_GET["src"];
        $utm_source   = @$_GET["utm_source"];
        $utm_campaign = @$_GET["utm_campaign"];
        $utm_medium   = @$_GET["utm_medium"];
        $utm_content  = @$_GET["utm_content"];

        // var_dump($_GET);

        if (@$pg) {
            $url = (new Url())->findById($pg);
            // echo "<br>URL: $url->url <br>";
        }

        if ($checkout && $checkout != '0') {
            $pagina = url("checkout/" . $checkout);
        } else {
            $pagina = $url->url;
        }

        $ip = (string) $_SERVER['REMOTE_ADDR'];
        $data["IP"] = $ip;

        if (@$cp) {
            $campanha = (new Campanha())->find("codigo = $cp")->fetch();
            if ($campanha->pixel_facebook) {
                $dado["pixel_facebook"]["id"] = $campanha->pixel_facebook;
                $dado["pixel_facebook"]["id_campanha"] = $campanha->id;
                $dado["pixel_facebook"]["id_pixel"] = $campanha->dataPixelFacebook()->id_pixel;
                $dado["pixel_facebook"]["boleto"] = $campanha->dataPixelFacebook()->boleto;
                $dado["pixel_facebook"]["executar_checkout"] = $campanha->dataPixelFacebook()->executar_checkout;
                $dado["pixel_facebook"]["valor"] = $campanha->dataPixelFacebook()->valor;
                $dado["pixel_facebook"]["status"] = $campanha->dataPixelFacebook()->status;
                $pixelFacebook = json_encode($dado["pixel_facebook"]);
            }

            if ($campanha->pixel_facebook_api) {
                $dado["pixel_facebook_api"]["id"] = $campanha->pixel_facebook_api;
                $dado["pixel_facebook_api"]["id_campanha"] = $campanha->id;
                $dado["pixel_facebook_api"]["id_pixel"] = $campanha->dataPixelFacebookApi()->id_pixel;
                $dado["pixel_facebook_api"]["boleto"] = $campanha->dataPixelFacebookApi()->boleto;
                $dado["pixel_facebook_api"]["executar_checkout"] = $campanha->dataPixelFacebookApi()->executar_checkout;
                $dado["pixel_facebook_api"]["valor"] = $campanha->dataPixelFacebookApi()->valor;
                $dado["pixel_facebook_api"]["status"] = $campanha->dataPixelFacebookApi()->status;
                $pixelFacebookApi = json_encode($dado["pixel_facebook_api"]);
            }

            if ($campanha->pixel_taboola) {
                $dado["pixel_taboola"]["id"] = $campanha->pixel_taboola;
                $dado["pixel_taboola"]["id_campanha"] = $campanha->id;
                $dado["pixel_taboola"]["id_pixel"] = $campanha->dataPixelTaboola()->id_pixel;
                $dado["pixel_taboola"]["boleto"] = $campanha->dataPixelTaboola()->boleto;
                $dado["pixel_taboola"]["executar_checkout"] = $campanha->dataPixelTaboola()->executar_checkout;
                $dado["pixel_taboola"]["valor"] = $campanha->dataPixelTaboola()->valor;
                $dado["pixel_taboola"]["status"] = $campanha->dataPixelTaboola()->status;
                $dado["pixel_taboola"]["evento_taboola"] = $campanha->dataPixelTaboola()->evento_taboola;
                $dado["pixel_taboola"]["purchase_taboola"] = $campanha->dataPixelTaboola()->purchase_taboola;
                $pixelTaboola = json_encode($dado["pixel_taboola"]);
            }

            if ($campanha->pixel_google_analitics) {
                $dado["pixel_google_analitics"]["id"] = $campanha->pixel_google_analitics;
                $dado["pixel_google_analitics"]["id_campanha"] = $campanha->id;
                $dado["pixel_google_analitics"]["id_pixel"] = $campanha->dataPixelGoogleAnalitics()->id_pixel;
                $dado["pixel_google_analitics"]["boleto"] = $campanha->dataPixelGoogleAnalitics()->boleto;
                $dado["pixel_google_analitics"]["executar_checkout"] = $campanha->dataPixelGoogleAnalitics()->executar_checkout;
                $dado["pixel_google_analitics"]["valor"] = $campanha->dataPixelGoogleAnalitics()->valor;
                $dado["pixel_google_analitics"]["status"] = $campanha->dataPixelGoogleAnalitics()->status;
                $pixelGoogleAnalitics = json_encode($dado["pixel_google_analitics"]);
            }

            if ($campanha->pixel_google_ads) {
                $dado["pixel_google_ads"]["id"] = $campanha->pixel_google_ads;
                $dado["pixel_google_ads"]["id_campanha"] = $campanha->id;
                $dado["pixel_google_ads"]["id_pixel"] = $campanha->dataPixelGoogleAds()->id_pixel;
                $dado["pixel_google_ads"]["boleto"] = $campanha->dataPixelGoogleAds()->boleto;
                $dado["pixel_google_ads"]["executar_checkout"] = $campanha->dataPixelGoogleAds()->executar_checkout;
                $dado["pixel_google_ads"]["valor"] = $campanha->dataPixelGoogleAds()->valor;
                $dado["pixel_google_ads"]["status"] = $campanha->dataPixelGoogleAds()->status;
                $dado["pixel_google_ads"]["tag_conversao"] = $campanha->dataPixelGoogleAds()->tag_conversao;
                $pixelGoogleAds = json_encode($dado["pixel_google_ads"]);
            }

            if ($campanha->pixel_google_tag_manager) {
                $dado["pixel_google_tag_manager"]["id"] = $campanha->pixel_google_tag_manager;
                $dado["pixel_google_tag_manager"]["id_campanha"] = $campanha->id;
                $dado["pixel_google_tag_manager"]["id_pixel"] = $campanha->dataPixelGoogleTagManager()->id_pixel;
                $dado["pixel_google_tag_manager"]["boleto"] = $campanha->dataPixelGoogleTagManager()->boleto;
                $dado["pixel_google_tag_manager"]["executar_checkout"] = $campanha->dataPixelGoogleTagManager()->executar_checkout;
                $dado["pixel_google_tag_manager"]["valor"] = $campanha->dataPixelGoogleTagManager()->valor;
                $dado["pixel_google_tag_manager"]["status"] = $campanha->dataPixelGoogleTagManager()->status;
                $pixelGoogleTagManager = json_encode($dado["pixel_google_tag_manager"]);
            }

            if ($campanha->pixel_tiktok) {
                $dado["pixel_tiktok"]["id"] = $campanha->pixel_tiktok;
                $dado["pixel_tiktok"]["id_campanha"] = $campanha->id;
                $dado["pixel_tiktok"]["id_pixel"] = $campanha->dataPixelTikTok()->id_pixel;
                $dado["pixel_tiktok"]["boleto"] = $campanha->dataPixelTikTok()->boleto;
                $dado["pixel_tiktok"]["executar_checkout"] = $campanha->dataPixelTikTok()->executar_checkout;
                $dado["pixel_tiktok"]["valor"] = $campanha->dataPixelTikTok()->valor;
                $dado["pixel_tiktok"]["status"] = $campanha->dataPixelTikTok()->status;
                $pixelTikTok = json_encode($dado["pixel_tiktok"]);
            }
        }
        // var_dump($campanha);
        if (@$codigo) {
            $afiliacao = (new Afiliacao())->find("codigo = '$codigo'")->fetch();
        }

        $cookies = (new Cookies())->find("ip = '$ip' AND produto = '$produto'")->fetch();

        // var_dump($cookies);
        $data["url"] = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        echo "<br>";

        $data["tipo_atribuicao"] = @$afiliacao->tipo_atribuicao;
        $data["tempo_cookie"] = @$afiliacao->tempo_cookie;

        if ($cookies) {
            $tempo = @$afiliacao->tempo_cookie ? @$afiliacao->tempo_cookie : 1;
            $data["tem_cookie"] = "SIM";

            if ($cookies->tipo_atribuicao == 2 || strtotime(date("Y-m-d")) > strtotime(@$cookies->tempo_cookie)) {
                $cookies->afiliacao = @$afiliacao->id;
                $cookies->afiliado = @$afiliacao->afiliado;
                $cookies->tipo_comissao = @$afiliacao->tipo_comissao;
                $cookies->comissao = @$afiliacao->comissao_afiliado;
                $cookies->tipo_atribuicao = @$afiliacao->tipo_atribuicao;
                $cookies->tempo_cookie = date("Y-m-d", strtotime("+" . @$tempo . " days", strtotime(date("Y-m-d"))));
                echo "Atualizando afiliado...<br>";

                $_SESSION["cookie"]["afiliacao"] = @$afiliacao->id;
                $_SESSION["cookie"]["afiliado"] = @$afiliacao->afiliado;
                $_SESSION["cookie"]["tipo_comissao"] = @$afiliacao->tipo_comissao;
                $_SESSION["cookie"]["comissao_afiliado"] = @$afiliacao->comissao_afiliado;

                $data["cookie_secao_afiliacao"] = $_SESSION["cookie"]["afiliacao"];
                $data["cookie_secao_afiliado"] = $_SESSION["cookie"]["afiliado"];
                $data["cookie_secao_tipo_comissao"] = $_SESSION["cookie"]["tipo_comissao"];
                $data["cookie_secao_comissao_afiliado"] = $_SESSION["cookie"]["comissao_afiliado"];

                $data["cookie_banco_afiliacao"] = $cookies->afiliacao;
                $data["cookie_banco_afiliado"] = $cookies->afiliado;
                $data["cookie_banco_tipo_comissao"] = $cookies->tipo_comissao;
                $data["cookie_banco_comissao"] = $cookies->comissao;
                $data["cookie_banco_tipo_atribuicao"] = $cookies->tipo_atribuicao;
                $data["cookie_banco_tempo_cookie"] = $cookies->tempo_cookie;

                $this->logs($data, "cookies");
            }

            $cookies->idCampanha = @$campanha ? @$campanha->id : NULL;
            $cookies->nome_campanha = @$campanha ? @$campanha->nome : NULL;
            $cookies->produto = $produto;
            $cookies->analytics = @$campanha ? @$campanha->analytics : NULL;
            $cookies->pixel_facebook = @$campanha->pixel_facebook ? $pixelFacebook : NULL;
            $cookies->pixel_facebook_api = @$campanha->pixel_facebook_api ? $pixelFacebookApi : NULL;
            $cookies->pixel_taboola = @$campanha->pixel_taboola ? $pixelTaboola : NULL;
            $cookies->pixel_google_analitics = @$campanha->pixel_google_analitics ? $pixelGoogleAnalitics : NULL;
            $cookies->pixel_google_ads = @$campanha->pixel_google_ads ? $pixelGoogleAds : NULL;
            $cookies->pixel_google_tag_manager = @$campanha->pixel_google_tag_manager ? $pixelGoogleTagManager : NULL;
            $cookies->pixel_tiktok = @$campanha->pixel_tiktok ? $pixelTikTok : NULL;

            $cookies->src = @$src;
            $cookies->utm_source = @$utm_source;
            $cookies->utm_medium = @$utm_medium;
            $cookies->utm_campaign = @$utm_campaign;
            $cookies->utm_content = @$utm_content;

            if ($cookies->save()) {
                echo "Cookie atualizado";
            } else {
                echo $cookies->fail()->getMessage();
            }
        } else {
            $cookies = new Cookies();
            $cookies->ip = @$ip;
            $cookies->afiliacao = @$afiliacao->id;
            $cookies->afiliado = @$afiliacao->afiliado;
            $cookies->tipo_comissao = @$afiliacao->tipo_comissao;
            $cookies->comissao = @$afiliacao->comissao_afiliado;
            $cookies->tipo_atribuicao = @$afiliacao->tipo_atribuicao;
            $tempo = @$afiliacao->tempo_cookie ? @$afiliacao->tempo_cookie : 1;
            $cookies->tempo_cookie = date("Y-m-d", strtotime("+" . @$tempo . " days", strtotime(date("Y-m-d"))));

            $cookies->idCampanha = @$campanha ? @$campanha->id : NULL;
            $cookies->nome_campanha = @$campanha ? @$campanha->nome : NULL;
            $cookies->produto = $produto;
            $cookies->analytics = @$campanha ? @$campanha->analytics : NULL;
            $cookies->pixel_facebook = @$campanha->pixel_facebook ? $pixelFacebook : NULL;
            $cookies->pixel_taboola = @$campanha->pixel_taboola ? $pixelTaboola : NULL;
            $cookies->pixel_google_analitics = @$campanha->pixel_google_analitics ? $pixelGoogleAnalitics : NULL;
            $cookies->pixel_google_ads = @$campanha->pixel_google_ads ? $pixelGoogleAds : NULL;
            $cookies->pixel_google_tag_manager = @$campanha->pixel_google_tag_manager ? $pixelGoogleTagManager : NULL;
            $cookies->pixel_tiktok = @$campanha->pixel_tiktok ? $pixelTikTok : NULL;

            $cookies->src = @$campanha->src;
            $cookies->utm_source = @$utm_source;
            $cookies->utm_medium = @$utm_medium;
            $cookies->utm_campaign = @$utm_campaign;
            $cookies->utm_content = @$utm_content;

            $data["tem_cookie"] = "NÃO";
            $_SESSION["cookie"]["afiliacao"] = @$afiliacao->id;
            $_SESSION["cookie"]["afiliado"] = @$afiliacao->afiliado;
            $_SESSION["cookie"]["tipo_comissao"] = @$afiliacao->tipo_comissao;
            $_SESSION["cookie"]["comissao_afiliado"] = @$afiliacao->comissao_afiliado;

            $data["cookie_secao_afiliacao"] = $_SESSION["cookie"]["afiliacao"];
            $data["cookie_secao_afiliado"] = $_SESSION["cookie"]["afiliado"];
            $data["cookie_secao_tipo_comissao"] = $_SESSION["cookie"]["tipo_comissao"];
            $data["cookie_secao_comissao_afiliado"] = $_SESSION["cookie"]["comissao_afiliado"];

            $data["cookie_banco_afiliacao"] = $cookies->afiliacao;
            $data["cookie_banco_afiliado"] = $cookies->afiliado;
            $data["cookie_banco_tipo_comissao"] = $cookies->tipo_comissao;
            $data["cookie_banco_comissao"] = $cookies->comissao;
            $data["cookie_banco_tipo_atribuicao"] = $cookies->tipo_atribuicao;
            $data["cookie_banco_tempo_cookie"] = $cookies->tempo_cookie;

            $this->logs($data, "cookies");

            if ($cookies->save()) {
                echo "Cookie cadastrado";
            } else {
                echo $cookies->fail()->getMessage();
            }
        }

        // echo "<script>";

        // var_dump($cookies);

        // setcookie($cookie_name, $cookie_value, time() + (60 * 60 * -24 * $afiliacao->tempo_cookie), "/");
        // setcookie($cookie_name, $cookie_value, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");

        // sleep(5);
        // header("Location: $pagina");
    }

    public function cookies3($data)
    {

        $codigo = $data["afiliacao"];
        $produto = $data["produto"];

        $data["codigo_afiliacao"] = $codigo;

        $tipo         = @$_GET["tp"]; // TIPO
        $checkout     = @$_GET["ch"]; // CHECKOUT
        $cp           = @$_GET["cp"]; // CAMPANHA
        $pg           = @$_GET["pg"]; // PÁGINA

        $src          = @$_GET["src"];
        $utm_source   = @$_GET["utm_source"];
        $utm_campaign = @$_GET["utm_campaign"];
        $utm_medium   = @$_GET["utm_medium"];
        $utm_content  = @$_GET["utm_content"];

        // var_dump($_GET);

        if (@$pg) {
            $url = (new Url())->findById($pg);
            // echo "<br>URL: $url->url <br>";
        }

        if ($checkout && $checkout != '0') {
            $pagina = url("checkout/" . $checkout);
        } else {
            $pagina = $url->url;
        }

        $ip = (string) $_SERVER['REMOTE_ADDR'];
        $data["IP"] = $ip;

        if (@$cp) {
            $campanha = (new Campanha())->find("codigo = $cp")->fetch();
            if ($campanha->pixel_facebook) {
                $dado["pixel_facebook"]["id"] = $campanha->pixel_facebook;
                $dado["pixel_facebook"]["id_campanha"] = $campanha->id;
                $dado["pixel_facebook"]["id_pixel"] = $campanha->dataPixelFacebook()->id_pixel;
                $dado["pixel_facebook"]["boleto"] = $campanha->dataPixelFacebook()->boleto;
                $dado["pixel_facebook"]["executar_checkout"] = $campanha->dataPixelFacebook()->executar_checkout;
                $dado["pixel_facebook"]["valor"] = $campanha->dataPixelFacebook()->valor;
                $dado["pixel_facebook"]["status"] = $campanha->dataPixelFacebook()->status;
                $pixelFacebook = json_encode($dado["pixel_facebook"]);
            }

            if ($campanha->pixel_facebook_api) {
                $dado["pixel_facebook_api"]["id"] = $campanha->pixel_facebook_api;
                $dado["pixel_facebook_api"]["id_campanha"] = $campanha->id;
                $dado["pixel_facebook_api"]["id_pixel"] = $campanha->dataPixelFacebookApi()->id_pixel;
                $dado["pixel_facebook_api"]["boleto"] = $campanha->dataPixelFacebookApi()->boleto;
                $dado["pixel_facebook_api"]["executar_checkout"] = $campanha->dataPixelFacebookApi()->executar_checkout;
                $dado["pixel_facebook_api"]["valor"] = $campanha->dataPixelFacebookApi()->valor;
                $dado["pixel_facebook_api"]["status"] = $campanha->dataPixelFacebookApi()->status;
                $pixelFacebookApi = json_encode($dado["pixel_facebook_api"]);
            }

            if ($campanha->pixel_taboola) {
                $dado["pixel_taboola"]["id"] = $campanha->pixel_taboola;
                $dado["pixel_taboola"]["id_campanha"] = $campanha->id;
                $dado["pixel_taboola"]["id_pixel"] = $campanha->dataPixelTaboola()->id_pixel;
                $dado["pixel_taboola"]["boleto"] = $campanha->dataPixelTaboola()->boleto;
                $dado["pixel_taboola"]["executar_checkout"] = $campanha->dataPixelTaboola()->executar_checkout;
                $dado["pixel_taboola"]["valor"] = $campanha->dataPixelTaboola()->valor;
                $dado["pixel_taboola"]["status"] = $campanha->dataPixelTaboola()->status;
                $dado["pixel_taboola"]["evento_taboola"] = $campanha->dataPixelTaboola()->evento_taboola;
                $dado["pixel_taboola"]["purchase_taboola"] = $campanha->dataPixelTaboola()->purchase_taboola;
                $pixelTaboola = json_encode($dado["pixel_taboola"]);
            }

            if ($campanha->pixel_google_analitics) {
                $dado["pixel_google_analitics"]["id"] = $campanha->pixel_google_analitics;
                $dado["pixel_google_analitics"]["id_campanha"] = $campanha->id;
                $dado["pixel_google_analitics"]["id_pixel"] = $campanha->dataPixelGoogleAnalitics()->id_pixel;
                $dado["pixel_google_analitics"]["boleto"] = $campanha->dataPixelGoogleAnalitics()->boleto;
                $dado["pixel_google_analitics"]["executar_checkout"] = $campanha->dataPixelGoogleAnalitics()->executar_checkout;
                $dado["pixel_google_analitics"]["valor"] = $campanha->dataPixelGoogleAnalitics()->valor;
                $dado["pixel_google_analitics"]["status"] = $campanha->dataPixelGoogleAnalitics()->status;
                $pixelGoogleAnalitics = json_encode($dado["pixel_google_analitics"]);
            }

            if ($campanha->pixel_google_ads) {
                $dado["pixel_google_ads"]["id"] = $campanha->pixel_google_ads;
                $dado["pixel_google_ads"]["id_campanha"] = $campanha->id;
                $dado["pixel_google_ads"]["id_pixel"] = $campanha->dataPixelGoogleAds()->id_pixel;
                $dado["pixel_google_ads"]["boleto"] = $campanha->dataPixelGoogleAds()->boleto;
                $dado["pixel_google_ads"]["executar_checkout"] = $campanha->dataPixelGoogleAds()->executar_checkout;
                $dado["pixel_google_ads"]["valor"] = $campanha->dataPixelGoogleAds()->valor;
                $dado["pixel_google_ads"]["status"] = $campanha->dataPixelGoogleAds()->status;
                $dado["pixel_google_ads"]["tag_conversao"] = $campanha->dataPixelGoogleAds()->tag_conversao;
                $pixelGoogleAds = json_encode($dado["pixel_google_ads"]);
            }

            if ($campanha->pixel_google_tag_manager) {
                $dado["pixel_google_tag_manager"]["id"] = $campanha->pixel_google_tag_manager;
                $dado["pixel_google_tag_manager"]["id_campanha"] = $campanha->id;
                $dado["pixel_google_tag_manager"]["id_pixel"] = $campanha->dataPixelGoogleTagManager()->id_pixel;
                $dado["pixel_google_tag_manager"]["boleto"] = $campanha->dataPixelGoogleTagManager()->boleto;
                $dado["pixel_google_tag_manager"]["executar_checkout"] = $campanha->dataPixelGoogleTagManager()->executar_checkout;
                $dado["pixel_google_tag_manager"]["valor"] = $campanha->dataPixelGoogleTagManager()->valor;
                $dado["pixel_google_tag_manager"]["status"] = $campanha->dataPixelGoogleTagManager()->status;
                $pixelGoogleTagManager = json_encode($dado["pixel_google_tag_manager"]);
            }

            if ($campanha->pixel_tiktok) {
                $dado["pixel_tiktok"]["id"] = $campanha->pixel_tiktok;
                $dado["pixel_tiktok"]["id_campanha"] = $campanha->id;
                $dado["pixel_tiktok"]["id_pixel"] = $campanha->dataPixelTikTok()->id_pixel;
                $dado["pixel_tiktok"]["boleto"] = $campanha->dataPixelTikTok()->boleto;
                $dado["pixel_tiktok"]["executar_checkout"] = $campanha->dataPixelTikTok()->executar_checkout;
                $dado["pixel_tiktok"]["valor"] = $campanha->dataPixelTikTok()->valor;
                $dado["pixel_tiktok"]["status"] = $campanha->dataPixelTikTok()->status;
                $pixelTikTok = json_encode($dado["pixel_tiktok"]);
            }
        }
        // var_dump($campanha);
        if (@$codigo) {
            $afiliacao = (new Afiliacao())->find("codigo = '$codigo'")->fetch();

            $numero_de_bytes = 4;
            $resultado_bytes = random_bytes($numero_de_bytes);
            $codigo_cookie = strtoupper(bin2hex($resultado_bytes));

            if (@$afiliacao)
                $conteudo_cookie = "afiliacao=$afiliacao->id&codigo=$codigo_cookie";
        }

        if (@$_COOKIE[$produto]) {
            parse_str($_COOKIE[$produto], $output);

            $cookies = (new Cookies())->find("navegador = '{$output["codigo"]}' AND produto = '$produto'")->fetch();
        }

        // var_dump($cookies);
        $data["url"] = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];

        $data["tipo_atribuicao"] = @$afiliacao->tipo_atribuicao;
        $data["tempo_cookie"] = @$afiliacao->tempo_cookie;

        if (@$cookies) {
            $tempo = @$afiliacao->tempo_cookie ? @$afiliacao->tempo_cookie : 1;
            $data["tem_cookie"] = "SIM";

            if ($cookies->tipo_atribuicao == 2 || strtotime(date("Y-m-d")) > strtotime(@$cookies->tempo_cookie)) {
                $cookies->afiliacao = @$afiliacao->id;
                $cookies->navegador = @$codigo_cookie;
                $cookies->afiliado = @$afiliacao->afiliado;
                $cookies->tipo_comissao = @$afiliacao->tipo_comissao;
                $cookies->comissao = @$afiliacao->comissao_afiliado;
                $cookies->tipo_atribuicao = @$afiliacao->tipo_atribuicao;
                $cookies->tempo_cookie = date("Y-m-d", strtotime("+" . @$tempo . " days", strtotime(date("Y-m-d"))));

                if (@$conteudo_cookie) {
                    setcookie($produto, @$conteudo_cookie, time() + (60 * 60 * -24 * @$afiliacao->tempo_cookie), "/");
                    setcookie($produto, @$conteudo_cookie, time() + (60 * 60 * 24 * @$afiliacao->tempo_cookie), "/");
                    setcookie('url',  $data["url"], time() + (60 * 60 * -24 * @$afiliacao->tempo_cookie), "/");
                    setcookie('url',  $data["url"], time() + (60 * 60 * 24 * @$afiliacao->tempo_cookie), "/");
                }

                $_SESSION["cookie"]["afiliacao"] = @$afiliacao->id;
                $_SESSION["cookie"]["afiliado"] = @$afiliacao->afiliado;
                $_SESSION["cookie"]["tipo_comissao"] = @$afiliacao->tipo_comissao;
                $_SESSION["cookie"]["comissao_afiliado"] = @$afiliacao->comissao_afiliado;

                $data["cookie_secao_afiliacao"] = $_SESSION["cookie"]["afiliacao"];
                $data["cookie_secao_afiliado"] = $_SESSION["cookie"]["afiliado"];
                $data["cookie_secao_tipo_comissao"] = $_SESSION["cookie"]["tipo_comissao"];
                $data["cookie_secao_comissao_afiliado"] = $_SESSION["cookie"]["comissao_afiliado"];

                $data["cookie_banco_afiliacao"] = $cookies->afiliacao;
                $data["cookie_banco_afiliado"] = $cookies->afiliado;
                $data["cookie_banco_tipo_comissao"] = $cookies->tipo_comissao;
                $data["cookie_banco_comissao"] = $cookies->comissao;
                $data["cookie_banco_tipo_atribuicao"] = $cookies->tipo_atribuicao;
                $data["cookie_banco_tempo_cookie"] = $cookies->tempo_cookie;

                $this->logs($data, "cookies");
            }

            $cookies->idCampanha = @$campanha ? @$campanha->id : NULL;
            $cookies->nome_campanha = @$campanha ? @$campanha->nome : NULL;
            $cookies->produto = $produto;
            $cookies->analytics = @$campanha ? @$campanha->analytics : NULL;
            $cookies->pixel_facebook = @$campanha->pixel_facebook ? $pixelFacebook : NULL;
            $cookies->pixel_facebook_api = @$campanha->pixel_facebook_api ? $pixelFacebookApi : NULL;
            $cookies->pixel_taboola = @$campanha->pixel_taboola ? $pixelTaboola : NULL;
            $cookies->pixel_google_analitics = @$campanha->pixel_google_analitics ? $pixelGoogleAnalitics : NULL;
            $cookies->pixel_google_ads = @$campanha->pixel_google_ads ? $pixelGoogleAds : NULL;
            $cookies->pixel_google_tag_manager = @$campanha->pixel_google_tag_manager ? $pixelGoogleTagManager : NULL;
            $cookies->pixel_tiktok = @$campanha->pixel_tiktok ? $pixelTikTok : NULL;

            $cookies->src = @$src;
            $cookies->utm_source = @$utm_source;
            $cookies->utm_medium = @$utm_medium;
            $cookies->utm_campaign = @$utm_campaign;
            $cookies->utm_content = @$utm_content;

            if ( ! $cookies->save()) {
                echo $cookies->fail()->getMessage();
            }
        } else {
            $cookies = new Cookies();
            $cookies->ip = @$ip;
            $cookies->navegador = @$codigo_cookie;
            $cookies->afiliacao = @$afiliacao->id;
            $cookies->afiliado = @$afiliacao->afiliado;
            $cookies->tipo_comissao = @$afiliacao->tipo_comissao;
            $cookies->comissao = @$afiliacao->comissao_afiliado;
            $cookies->tipo_atribuicao = @$afiliacao->tipo_atribuicao;
            $tempo = @$afiliacao->tempo_cookie ? @$afiliacao->tempo_cookie : 1;
            $cookies->tempo_cookie = date("Y-m-d", strtotime("+" . @$tempo . " days", strtotime(date("Y-m-d"))));

            $cookies->idCampanha = @$campanha ? @$campanha->id : NULL;
            $cookies->nome_campanha = @$campanha ? @$campanha->nome : NULL;
            $cookies->produto = $produto;
            $cookies->analytics = @$campanha ? @$campanha->analytics : NULL;
            $cookies->pixel_facebook = @$campanha->pixel_facebook ? $pixelFacebook : NULL;
            $cookies->pixel_facebook_api = @$campanha->pixel_facebook_api ? $pixelFacebookApi : NULL;
            $cookies->pixel_taboola = @$campanha->pixel_taboola ? $pixelTaboola : NULL;
            $cookies->pixel_google_analitics = @$campanha->pixel_google_analitics ? $pixelGoogleAnalitics : NULL;
            $cookies->pixel_google_ads = @$campanha->pixel_google_ads ? $pixelGoogleAds : NULL;
            $cookies->pixel_google_tag_manager = @$campanha->pixel_google_tag_manager ? $pixelGoogleTagManager : NULL;
            $cookies->pixel_tiktok = @$campanha->pixel_tiktok ? $pixelTikTok : NULL;

            $cookies->src = @$campanha->src;
            $cookies->utm_source = @$utm_source;
            $cookies->utm_medium = @$utm_medium;
            $cookies->utm_campaign = @$utm_campaign;
            $cookies->utm_content = @$utm_content;

            if (@$conteudo_cookie)
                setcookie($produto, $conteudo_cookie, time() + (60 * 60 * 24 * @$afiliacao->tempo_cookie), "/");
                setcookie('url',  $data["url"], time() + (60 * 60 * -24 * @$afiliacao->tempo_cookie), "/");
                setcookie('url',  $data["url"], time() + (60 * 60 * 24 * @$afiliacao->tempo_cookie), "/");

            $data["tem_cookie"] = "NÃO";

            $_SESSION["cookie"]["afiliacao"] = @$afiliacao->id;
            $_SESSION["cookie"]["afiliado"] = @$afiliacao->afiliado;
            $_SESSION["cookie"]["tipo_comissao"] = @$afiliacao->tipo_comissao;
            $_SESSION["cookie"]["comissao_afiliado"] = @$afiliacao->comissao_afiliado;

            $data["cookie_secao_afiliacao"] = $_SESSION["cookie"]["afiliacao"];
            $data["cookie_secao_afiliado"] = $_SESSION["cookie"]["afiliado"];
            $data["cookie_secao_tipo_comissao"] = $_SESSION["cookie"]["tipo_comissao"];
            $data["cookie_secao_comissao_afiliado"] = $_SESSION["cookie"]["comissao_afiliado"];

            $data["cookie_banco_afiliacao"] = $cookies->afiliacao;
            $data["cookie_banco_afiliado"] = $cookies->afiliado;
            $data["cookie_banco_tipo_comissao"] = $cookies->tipo_comissao;
            $data["cookie_banco_comissao"] = $cookies->comissao;
            $data["cookie_banco_tipo_atribuicao"] = $cookies->tipo_atribuicao;
            $data["cookie_banco_tempo_cookie"] = $cookies->tempo_cookie;

            $this->logs($data, "cookies");

            if ( ! $cookies->save()) {
                echo $cookies->fail()->getMessage();
            }
        }

        // var_dump($cookies);

        // setcookie($cookie_name, $cookie_value, time() + (60 * 60 * -24 * $afiliacao->tempo_cookie), "/");
        // setcookie($cookie_name, $cookie_value, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");

        // sleep(5);
        if (@!$_COOKIE["analytics"]) {
            $numero_de_bytes = 4;
            $resultado_bytes = random_bytes($numero_de_bytes);
            $codigo_cookie = strtoupper(bin2hex($resultado_bytes));

            setcookie("analytics", $codigo_cookie, time() + (60 * 60 * 24 * 10000), "/");
        }

        $produto = (new Produto())->findById(@$data["produto"]);
        // echo "<pre>";
        $dados["url"]           = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $dados["url_anterior"]  = @$_SERVER['HTTP_REFERER'];
        $dados["navegador"]     = @$_COOKIE["analytics"];
        $dados["proxima_url"]   = @$pagina;
        $dados["produto"]       = @$data["produto"];
        $dados["checkout"]      = @$_GET["ch"];
        $dados["afiliado"]      = @$data["afiliado"];
        $dados["campanha"]      = @$data["campanha"];
        $dados["produtor"]      = @$produto->usuario;

        $redrocketAnalytics = (new AppRedrocketAnalytics(null))->cadastrarAnalytics($dados);


        if (@$data["produto"] == 2) {
            header("Location: " . url("checkout/BC4BF0D0"));
        } else {
            header("Location: $pagina");
        }
    }

    public function analytics($data)
    {
        $url = $data["link"] . "/api/3/contact/sync";
        $dados["url"]           = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $dados["url_anterior"]  = @$_SERVER['HTTP_REFERER'];
        $dados["navegador"]     = @$_COOKIE["analytics"];
        $dados["proxima_url"]   = @$data["proxima_url"];
        $dados["produto"]       = @$data["produto"];
        $dados["plano"]         = @$data["plano"];
        $dados["checkout"]      = @$data["checkout"];
        $dados["afiliado"]      = @$data["afiliado"];
        $dados["campanha"]      = @$data["campanha"];
        $dados["tempo"]         = @$data["tempo"];

        $data_string = json_encode($dados);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dados,
            CURLOPT_HTTPHEADER => array(
                "Api-Token: {$data["token"]}",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data["resposta"] = $response;
        $data["dados"] = $dados;
        $this->logs($data, "active_campaign");

        return $response;
    }

    public function cookiesTelevendas($data)
    {
        // var_dump($data);
        // var_dump($_GET);
        // exit;
        $atendente = $data["atendente"];
        $produto = $data["produto"];
        $checkout     = @$_GET["ch"];   // CHECKOUT
        $comissao = $_GET["cm"];        // COMISSAO


        // } else {
        //     $pagina = $url->url;
        // }
        // echo "<br>Você está sendo redirecionado para <b>$pagina</b> <br>";

        $ip = (string) $_SERVER['REMOTE_ADDR'];

        $cookies = (new cookiesTelevendas())->find("ip = '$ip' AND produto = '$produto' AND atendente = $atendente AND comissao = $comissao")->fetch();
        // $usuario = (new Usuario())->findById($atendente);
        // var_dump($cookies);
        // exit;

        if (!@$cookies) {
            $cookies = new cookiesTelevendas();
            $cookies->ip = @$ip;
            $cookies->produto = $produto;
            $cookies->atendente = @$atendente;
            $cookies->comissao = @$comissao;

            if ($cookies->save()) {
                // echo "Cadastrado";
            } else {
                echo $cookies->fail()->getMessage();
            }
        }

        // if (@$cookies) {
        //     $cookies->atendente = @$atendente;
        //     $cookies->comissao = @$comissao;
        //     if ($cookies->save()) {
        //         var_dump($cookies);
        //         echo "Atualizado";
        //         exit;
        //     } else {
        //         echo $cookies->fail()->getMessage();
        //     }
        // } else {
        //     $cookies = new cookiesTelevendas();
        //     $cookies->ip = @$ip;
        //     $cookies->produto = $produto;
        //     $cookies->atendente = @$atendente;
        //     $cookies->comissao = @$comissao;

        //     if ($cookies->save()) {
        //         // echo "Cadastrado";
        //     } else {
        //         echo $cookies->fail()->getMessage();
        //     }
        // }

        if ($checkout && $checkout != '0')
            $pagina = url("checkout/" . $checkout);

        // echo json_encode($dado);
        // var_dump($cookies);
        // echo ($pagina);
        header("Location: " . $pagina . "&at=" . $atendente);
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

    public function campanha($data)
    {
        $codigo = $data["campanha"];
        $afiliacao = (new Campanha())->find("codigo = '$codigo'")->fetch();
        $produto = $afiliacao->dataProduto()->codigo;

        $checkout = @$_GET["ch"];

        $src = @$_GET["src"];
        $utm_source = @$_GET["utm_source"];
        $utm_campaign = @$_GET["utm_campaign"];
        $utm_medium = @$_GET["utm_medium"];
        $utm_content = @$_GET["utm_content"];

        $cookie_name = $produto;
        $cookie_value = "codigo=$afiliacao->codigo&produto=$afiliacao->produto&afiliado=$afiliacao->afiliado&tipo_comissao=$afiliacao->tipo_comissao&comissao=$afiliacao->comissao_afiliado&tipo_atribuicao=$afiliacao->tipo_atribuicao";
        $rastro = "&src=$src&utm_source=$utm_source&utm_medium=$utm_medium&utm_campaign=$utm_campaign&utm_content=$utm_content";

        $afiliado = @$_COOKIE[$cookie_name];
        if ($afiliado == "") {
            setcookie($cookie_name, $cookie_value . $rastro, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");
        } else {
            parse_str($afiliado, $data);
            echo "codigo " . $data["codigo"] . "<br>";

            if ($data["tipo_atribuicao"] == 2) {
                setcookie($cookie_name, $cookie_value . $rastro, time() + (60 * 60 * -24 * $afiliacao->tempo_cookie), "/");
                setcookie($cookie_name, $cookie_value . $rastro, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");
            }
        }


        if ($checkout != "")
            $codigoCheckout = (new Checkout())->find("codigo = '$checkout'")->fetch();

        if (!$checkout || !$codigoCheckout) {
            echo "<br> pagina venda " . $afiliacao->dataProduto()->pagina_venda;
            header("Location: " . $afiliacao->dataProduto()->pagina_venda);
        } else {
            echo "<br> pagina venda " . url("checkout/" . $checkout);
            header("Location: " . url("checkout/" . $checkout));
        }
    }

    public function usuarioPremium($data)
    {
        $usuario = (new Usuario())->findById($data["id"]);

        if ($usuario) {
            $expiracao = date("Y-m-d", strtotime("+30 days"));

            /** EXPIRAÇÃO DO COOKIE */

            $cookie = "id=$usuario->id&expiracao=$expiracao";
            parse_str(@$_COOKIE["userPr"], $arr);

            if (@$_COOKIE["userPr"]) {
                if (strtotime($arr["expiracao"]) == strtotime(date("Y-m-d"))) {
                    setcookie("userPr", $cookie, time() + (60 * 60 * 24 * 30), "/");
                }
            } else {
                setcookie("userPr", $cookie, time() + (60 * 60 * 24 * 30), "/");
            }

            header("Location: " . url("cadastro"));
        }
    }
}

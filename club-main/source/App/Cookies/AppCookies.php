<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\Cookies;

use League\Plates\Engine;
use Source\Models\Checkout\Checkout;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Usuarios\Usuario;
use Source\Models\Campanhas\Campanha;
use Source\Models\URL\Url;
use Source\Models\Sessao\Sessao;
use Source\App\Sessao\AppSessao;

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

        if(@$cp){
            $campanha = (new Campanha())->find("codigo = $cp")->fetch();
            $idCamp = $campanha->id;
            $prod = $campanha->produto;
            $analytics = $campanha->analytics;

            if($campanha->pixel){
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
            var_dump(@$_COOKIE["campanha"]);

            echo "<br> URL destino: <br>";
            echo $campanha->url_destino . " - " . $checkout . "<br>";

            if($checkout == '0'){
                $paginaCP = $campanha->dataProduto()->pagina_venda . "?cp=" . $cp;
            } else {
                $paginaCP = url("checkout/" . $checkout . "?cp=" . $cp);
            }
            echo $paginaCP;
        }

        if($tipo == 5 || $tipo == 4) {
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
                if($afiliado == "") {
                    setcookie($cookie_name, $cookie_value.$rastro, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");
                }
                else {
                    parse_str($afiliado, $data);
                    echo "<br><br>codigo " . $data["codigo"] . "<br>";
                    
                    if($data["tipo_atribuicao"] == 2){
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

        if(@$pg){
            $url = (new Url())->findById($pg);
            echo "URL: $url->url <br>";
            //header("Location: " . $url->url);
        }

        if($checkout && $checkout != '0'){
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
        if($afiliado == "") {
            setcookie($cookie_name, $cookie_value.$rastro, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");
        }
        else {
            parse_str($afiliado, $data);
            echo "codigo " . $data["codigo"] . "<br>";
            
            if($data["tipo_atribuicao"] == 2){
                setcookie($cookie_name, $cookie_value.$rastro, time() + (60 * 60 * -24 * $afiliacao->tempo_cookie), "/");
                setcookie($cookie_name, $cookie_value.$rastro, time() + (60 * 60 * 24 * $afiliacao->tempo_cookie), "/");
            }
        }

        echo "<title>Teste</title>";
        var_dump(@$_COOKIE[$cookie_name]);

        if($checkout != "")
            $codigoCheckout = (new Checkout())->find("codigo = '$checkout'")->fetch();

        if(!$checkout || !$codigoCheckout){
            echo "<br> pagina venda " . $afiliacao->dataProduto()->pagina_venda;
            header("Location: " . $afiliacao->dataProduto()->pagina_venda);
        }
        else{
            echo "<br> pagina venda " . url("checkout/".$checkout);
            header("Location: " . url("checkout/".$checkout));
        }
    }

    public function usuarioPremium($data)
    {
        $usuario = (new Usuario())->findById($data["id"]);

        if($usuario) {
            $expiracao = date("Y-m-d", strtotime("+30 days"));
            echo $expiracao . "<br>"; /** EXPIRAÇÃO DO COOKIE */

            $cookie = "id=$usuario->id&expiracao=$expiracao";
            parse_str(@$_COOKIE["userPr"], $arr);
            
            if(@$_COOKIE["userPr"]){
                if(strtotime($arr["expiracao"]) == strtotime(date("Y-m-d"))){
                    setcookie("userPr", $cookie, time() + (60 * 60 * 24 * 30), "/");
                }
            } else {
                setcookie("userPr", $cookie, time() + (60 * 60 * 24 * 30), "/");
            }

            var_dump($_COOKIE["userPr"]);

            header("Location: " . url("cadastro"));
        }
    }
}
<?php

use Source\Models\CartaoCheckoutTransparente\Cartao;
use Source\App\Facebook\AppFacebook;
use Source\Models\Pixel\Pixel;

// VERIFICAÇÕES SE OS DADOS SÃO VÁLIDOS

$dados = $_GET;

if ($dados == false) {
    echo $mensagem;
    exit;
}

if (@$plano == null) {
    echo $mensagem;
    exit;
}

if ($plano->preco < 5) {
    echo "Dados inválidos da requisição. Tente novamente mais tarde.";
    exit;
}

// FIM DAS VERIFICAÇÕES

if (@$checkout->cupom) {
    $cupom = 1;
    $valor_cupom = $checkout->valor_cupom;

    if ($checkout->so_cartao == 1) {
        $so_cartao = 1;
    } else {
        $so_cartao = 0;
    }
} else {
    $cupom = 0;
    $valor_cupom = 0;
    $so_cartao = 0;
}

// DEFINIÇÃO DE VARIÁVEIS
$preco          = ($produto->tipo_cobranca == 2 && $plano->primeira_parcela == "preco") ? $plano->preco_parcela : $plano->preco;
$nomeProduto    = $plano->nome;
$produtor       = $plano->usuario;

$statusProduto  = $checkout->dataProduto()->status;
$statusPlano    = $checkout->plano ? $checkout->dataPlano()->status : 2;

if (@$idPlano != "") $idPlano;
$idProduto      = $produto->id;

$banner             = $checkout->banner;
$aviso              = $checkout->aviso;
$email_suporte      = $produto->email_suporte;
$taxa_juros         = $taxa->parcelamento;
$url_checkout       = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
$codigo_checkout    = $checkout->codigo;
$obrigado_boleto    = $produto->obrigado_boleto;
$obrigado_cartao    = $produto->obrigado_cartao;
$obrigado_analise   = $produto->obrigado_analise;

// ID único por carregamento para deduplicar Pixel (browser) x CAPI na Meta
$eventIdCheckout    = bin2hex(random_bytes(16));
// Sinais de navegador usados pela Meta para casar com o clique do anúncio
$fbp                = $_COOKIE['_fbp'] ?? null;
$fbc                = $_COOKIE['_fbc'] ?? null;
// Fallback por IP: recupera fbc capturado pelo roundbr quando o cliente
// veio via WhatsApp e não possui o cookie _fbc
if (!$fbc) {
    $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR']
        ? trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0])
        : $_SERVER['REMOTE_ADDR'];
    $pixelLead = (new \Source\Models\PixelLead\PixelLead())->findByIp($clientIp);
    if ($pixelLead && $pixelLead->fbc) {
        $fbc = $pixelLead->fbc;
    }
}

$taxa_redrocket     = $taxa_transacao;
$valor_redrocket    = number_format((@$preco * (@$taxa_redrocket / 100)) + 1, 2, ".", "");
$valor_liquido      = @$preco - @$valor_redrocket;

// DATA DE NASCIMENTO
$data = mt_rand(strtotime("1970-01-01"), strtotime("2000-01-01"));
$date = new DateTime();
$date->setTimestamp($data);
$nascimento = $date->format('Y-m-d');

if ($checkout->vencimento_boletos)
    $data_vencimento = date("Y-m-d", strtotime("+$checkout->vencimento_boletos days"));
// FIM DEFINIÇÃO DE VARIÁVEIS

// Marca from_whatsapp via servidor (vale para este request também)
if (empty($_COOKIE['from_whatsapp'])) {
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/whatsapp|wa\.me/i', $ref.' '.$ua)) {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        // HttpOnly = false pois você já seta via JS também; SameSite=Lax ok pra checkout
        setcookie('from_whatsapp', '1', time() + 60*60*24*30, '/', '', $secure, false);
        $_COOKIE['from_whatsapp'] = '1';
    }
}
$fromWA = !empty($_COOKIE['from_whatsapp']);


// COOKIES BANCO DE DADOS
if (@$cookies) {

    $afiliado = $cookies->afiliado;

    if (@$cookies_televendas != "" && @$produto->comissao_afiliado_televendas != 0) {
        if (@$produto->tipo_comissao_afiliado == 0) {
            echo "<!-- 1 televendas R$ -->";
            $comissao = $valor_liquido * ($produto->comissao_afiliado_televendas / 100);
        } else {
            echo "<!-- 1 televendas % -->";
            $comissao = $produto->comissao_afiliado_televendas;
        }
    } else if (@$cookies_televendas != "" && @$produto->comissao_afiliado_televendas == 0) {
        echo "<!-- 2 televendas 0 -->";
        $comissao = 0;
    } else if ($idPlano != "" && $plano->habilitar_comissao_afiliado == 1 && $cookies_televendas == "") {
        if ($plano->tipo_comissao_afiliado == 0) {
            echo "<!-- 3 afiliados televendas R$ -->";
            $comissao = $valor_liquido * ($plano->comissao_afiliado / 100);
        } else {
            echo "<!-- 3 afiliados televendas % -->";
            $comissao = $plano->comissao_afiliado;
        }
    } else if ($cookies->tipo_comissao == 0) {
        echo "<!-- 4 R$ -->";
        $comissao = $valor_liquido * ($cookies->comissao / 100);
    } else if ($cookies->tipo_comissao == 1) {
        echo "<!-- 4 % -->";
        $comissao = $cookies->comissao;
    }

    $comissao_assinatura = ($plano->preco - $valor_redrocket) * ($plano->comissao_afiliado_assinatura / 100);
    $comissao_assinatura = number_format($comissao_assinatura, 2);
    $comissao = number_format($comissao, 2);
    //echo "<br>" . $comissao;

    $idCamp = @$cookies->idCampanha;
    $campanha = @$cookies->campanha;
    $analytics = @$cookies->analytics;

    if ($cookies->pixel_facebook) {
        $facebook = json_decode($cookies->pixel_facebook);
        // echo $facebook->id_pixel;
    }

    if ($cookies->pixel_facebook_api) {
        $facebookApi = json_decode($cookies->pixel_facebook_api);
        // echo $facebook->id_pixel;
    }

    if ($cookies->pixel_taboola) {
        $taboola = json_decode($cookies->pixel_taboola);
        // echo $taboola->id_pixel;
    }

    if ($cookies->pixel_google_analitics) {
        $analitics = json_decode($cookies->pixel_google_analitics);
        // echo $analitics->id_pixel;
    }

    if ($cookies->pixel_google_ads) {
        $ads = json_decode($cookies->pixel_google_ads);
        // echo $ads->id_pixel;
    }

    if ($cookies->pixel_google_tag_manager) {
        $tagManager = json_decode($cookies->pixel_google_tag_manager);
        // echo $tagManager->id_pixel;
    }

    if ($cookies->pixel_tiktok) {
        $tiktok = json_decode($cookies->pixel_tiktok);
    }

    if ($cookies->pixel_kwai) {
        $kwai = json_decode($cookies->pixel_kwai);
    }
}

$com = @$comissao ? $comissao : 0;
$af = @$comissao ? $comissao . " - " . $afiliado : 0;
$resto = $valor_liquido - @$somaCoprodutor - @$com;
echo "<!-- valor liquido = $valor_liquido | soma = R$" . @$somaCoprodutor . " | afiliado = " . @$af . " | resto = " . @$resto . " -->";

$src          = @$cookies->src != "" ? @$cookies->src : @$_GET["src"];
$utm_source   = @$cookies->utm_source != "" ? @$cookies->utm_source : @$_GET["utm_source"];
$utm_campaign = @$cookies->utm_campaign != "" ? @$cookies->utm_campaign : @$_GET["utm_campaign"];
$utm_medium   = @$cookies->utm_medium != "" ? @$cookies->utm_medium : @$_GET["utm_medium"];
$utm_content  = @$cookies->utm_content != "" ? @$cookies->utm_content : @$_GET["utm_content"];
// FIM COOKIES BANCO DE DADOS
?>

<!DOCTYPE html>
<html lang="pt-br">
<!-- HEAD -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">

    <!-- Título mostrado na pré-visualização -->
    <meta property="og:title" content=<?= $title; ?>>
    <link rel="icon" href="<?= url("themes/tema1/images/favicon.png"); ?>">

    <!-- Deixar os outros campos em branco para ocultar -->

    <!-- Twitter (opcional) -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content=<?= $title; ?>>
    <meta name="twitter:description" content="">

    <title><?= $title; ?></title>

    <link rel="stylesheet" href="<?= url("pagamentos/css/bootstrap.css"); ?>">
  
    <script src="<?= url("pagamentos/js/jquery.slim.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/popper.min.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/bootstrap.js"); ?>"></script>

    <link rel="stylesheet" href="<?= url("pagamentos/css/style.css?" . time()); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">

    <script>
        (function () {
        try {
            var hit = /whatsapp|wa\.me/i.test((document.referrer||"") + " " + (navigator.userAgent||""));
            if (hit) {
            var isHttps = location.protocol === 'https:';
            document.cookie = "from_whatsapp=1; Max-Age=2592000; Path=/; SameSite=Lax" + (isHttps?"; Secure":"");
            }
        } catch(e) {}
        })();
    </script>



    <?php if (@$sandbox == 0) : ?>
        <!-- ANALYTICS REDROCKET -->
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-154130073-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-154130073-1');
        </script>
        <!-- FIM ANALYTICS REDROCKET -->

        <script>
            (function(h, o, t, j, a, r) {
                h.hj = h.hj || function() {
                    (h.hj.q = h.hj.q || []).push(arguments)
                };
                h._hjSettings = {
                    hjid: 2669060,
                    hjsv: 6
                };
                a = o.getElementsByTagName('head')[0];
                r = o.createElement('script');
                r.async = 1;
                r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
                a.appendChild(r);
            })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
        </script>
    <?php endif; ?>

    <!-- HOTJAR PRODUTOR -->
    <?php if ($hotjar) : ?>
        <?php foreach ($hotjar as $hotjar) : ?>
            <?php if ($hotjar->produto == 0 || $hotjar->produto == $checkout->produto) : ?>
                <script>
                    (function(h, o, t, j, a, r) {
                        h.hj = h.hj || function() {
                            (h.hj.q = h.hj.q || []).push(arguments)
                        };
                        h._hjSettings = {
                            hjid: <?= $hotjar->tracking_id ?>,
                            hjsv: 6
                        };
                        a = o.getElementsByTagName('head')[0];
                        r = o.createElement('script');
                        r.async = 1;
                        r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
                        a.appendChild(r);
                    })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
                </script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <!-- FIM HOTJAR PRODUTOR -->
    <?php

    $_SESSION["checkout"] = $checkout;

    if (@$facebookApi) {
        echo "<!-- INICIO COOKIES AFILIADOS -->";

        $facebookPixels = explode(",", @$facebookApi->id_pixel);

        foreach ($facebookPixels as $pixel) :
                    if ($facebookApi->id)
                        $fb_pixel = (new Pixel())->findById($facebookApi->id);

                    if (@$facebookApi->executar_checkout == 1) :
                        if ($fb_pixel && $fb_pixel->tipo_integracao == "api") :
                            $dados = array();
                            $dados["pixel"] = $pixel;
                            $dados["token"] = $fb_pixel->token_acesso;
                            $dados["evento"] = "InitiateCheckout";
                            $dados["site_url"] = @$fb_pixel->dominio_url ? @$fb_pixel->dominio_url : "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                            $dados["ip"] = $_SERVER["REMOTE_ADDR"];
                            $dados["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
                            // event_id + fbp/fbc permitem deduplicar servidor x navegador na Meta
                            $dados["event_id"] = $eventIdCheckout;
                            $dados["fbp"] = $fbp;
                            $dados["fbc"] = $fbc;
                            $dados["action_source"] = "website";

                            echo "
                    <!-- PIXEL FACEBOOK CHECKOUT -->
                        <iframe src='" . url("pagamentos/pixel-base.php?pixel=$pixel&fbq_event=InitiateCheckout&event_id=$eventIdCheckout") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                    ";

                            $fb_afiliado = (new AppFacebook())->pixel($dados);

                    @$referenciaAf .= "pixel: $pixel, evento: InitiateCheckout, {$dados['site_url']}";
                    echo "
                        <!-- $pixel InitiateCheckout af. -->
                    ";
                endif;
            endif;
        endforeach;

        echo "<!-- FIM COOKIES AFILIADOS -->";

    } else if($checkout->pixel_facebook || $checkout->pixel_facebook_api ){

        echo "<!-- COOKIES PRODUTOR -->";

        $pixels = explode(",", $checkout->dataPixelFacebook()->id_pixel);
        $valor = @$checkout->dataPixelFacebook()->valor ? $checkout->dataPixelFacebook()->valor : $venda->valor_liquido;
        foreach ($pixels as $pixel2) :
            // echo "$forma_pagamento " . $checkout->dataPixelFacebook()->boleto;
          
            if (@$checkout->dataPixelFacebook()->tipo_integracao == "api") {
                $dados = array();
                $dados["pixel"] = $pixel2;
                $dados["token"] = $checkout->dataPixelFacebook()->token_acesso;
                $dados["evento"] = "InitiateCheckout";
                $dados["site_url"] = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $dados["ip"] = $_SERVER["REMOTE_ADDR"];
                $dados["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
                $dados["valor"] = $valor;
                // event_id + fbp/fbc permitem deduplicar servidor x navegador na Meta
                $dados["event_id"] = $eventIdCheckout;
                $dados["fbp"] = $fbp;
                $dados["fbc"] = $fbc;
                $dados["action_source"] = "website";

                if (strpos($_SERVER['HTTP_COOKIE'] ?? '', 'from_whatsapp=1') !== false) {
                    $dados['custom_data']['channel'] = 'whatsapp'; // opcional
                    $dados['from_whatsapp'] = 1; // para seus logs internos
                }
                
                echo "
                <!-- PIXEL FACEBOOK CHECKOUT -->
                    <iframe src='" . url("pagamentos/pixel-base.php?pixel=$pixel2&fbq_event=InitiateCheckout&event_id=$eventIdCheckout") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                ";

                $fb = (new AppFacebook())->pixel($dados);

                $referenciaPd = "pixel: $pixel2, evento: InitiateCheckout";

            }else {
                echo "
                    <!-- PIXEL FACEBOOK CHECKOUT -->
                    <iframe src='" . url("pagamentos/pixel.php?pixel=$pixel2&ev=InitiateCheckout&valor=$valor") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                ";
            }
            
        endforeach;

        echo "<!-- FIM COOKIES PRODUTOR -->";
    }




    if (@$taboola) :
        $taboolaPixels = explode(",", @$taboola->id_pixel);
        $eventos = explode(",", @$taboola->evento_taboola);
        $i = 0;
        foreach ($taboolaPixels as $pixel) :
            if (@$taboola->executar_checkout == 1) :
                echo "
                    <!-- PIXEL TABOOLA CAMPANHA -->
                    <!--iframe src='" . url("checkout/pixel-taboola?pixel=$pixel&ev=" . @$eventos[$i]) . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe-->
                    <!-- Taboola Pixel Code -->
                    <script type='text/javascript'>
                    window._tfa = window._tfa || [];
                    window._tfa.push({notify: 'event', name: 'page_view', id: $pixel});
                    !function (t, f, a, x) {
                            if (!document.getElementById(x)) {
                                t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
                            }
                    }(document.createElement('script'),
                    document.getElementsByTagName('script')[0],
                    '//cdn.taboola.com/libtrc/unip/$pixel/tfa.js',
                    'tb_tfa_script');
                    </script>
                    <!-- End of Taboola Pixel Code --> 
                ";

                if (@$eventos[$i]) :
                    echo "
                        <!-- Taboola Pixel Code -->
                        <script>
                            _tfa.push({notify: 'event', name: '$eventos[$i]', id: $pixel});
                        </script>
                        
                        <!-- End of Taboola Pixel Code -->
                    ";
                endif;
                $i++;
            endif;
        endforeach;
    endif;

    if (@$analitics) :
        $analiticsPixels = explode(",", @$analitics->id_pixel);
        foreach ($analiticsPixels as $pixel) :
            if (@$analitics->executar_checkout == 1) :
                echo "<script async src='https://www.googletagmanager.com/gtag/js?id=$pixel'></script>
                        <script>
                            window.dataLayer = window.dataLayer || [];
                            function gtag(){dataLayer.push(arguments);}
                            gtag('js', new Date());
            
                            gtag('config', '$pixel');
                        </script>";
            endif;
        endforeach;
    endif;

    if (@$ads) :
        $adsPixels = explode(",", @$ads->id_pixel);
        foreach ($adsPixels as $pixel) :
            if (@$ads->executar_checkout == 1) :
                echo "<script async src='https://www.googletagmanager.com/gtag/js?id=$pixel'></script>
                        <script>
                            window.dataLayer = window.dataLayer || [];
                            function gtag(){dataLayer.push(arguments);}
                            gtag('js', new Date());
            
                            gtag('config', '$pixel');
                        </script>";
            endif;
        endforeach;
    endif;

    if (@$tagManager) :
        $tagPixels = explode(",", @$tagManager->id_pixel);
        foreach ($tagPixels as $pixel) :
            if (@$tagManager->executar_checkout == 1) :
                echo "<!-- Google Tag Manager -->
                        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                        })(window,document,'script','dataLayer','" . $pixel . "');</script>
                        <!-- End Google Tag Manager -->";
            endif;
        endforeach;
    endif;

    if (@$tiktok) :
        $tiktokAfiliado = explode(",", $tiktok->id_pixel);
        foreach ($tiktokAfiliado as $tiktokAfiliado) :
            echo "<script>
                    !function (w, d, t) {
                    w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};
                    ttq.load('$tiktokAfiliado');
                    ttq.page();
                    }(window, document, 'ttq');
                </script>";
        endforeach;
    endif;
    ?>

    <?php if (@$kwai): ?>
        <?php $kwaiAfiliado = explode(",", @$kwai->id_pixel); ?>
        <script>
        !function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.install=t():e.install=t()}(window,(function(){return function(e){var t={};function n(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}return n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"_esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(o,r,function(t){return e[t]}.bind(null,r));return o},n.n=function(e){var t=e&&e.esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){"use strict";var o=this&&this.spreadArray||function(e,t,n){if(n||2===arguments.length)for(var o,r=0,i=t.length;r<i;r++)!o&&r in t||(o||(o=Array.prototype.slice.call(t,0,r)),o[r]=t[r]);return e.concat(o||Array.prototype.slice.call(t))};Object.defineProperty(t,"_esModule",{value:!0});var r=function(e,t,n){var o,i=e.createElement("script");i.type="text/javascript",i.async=!0,i.src=t,n&&(i.onerror=function(){r(e,n)});var a=e.getElementsByTagName("script")[0];null===(o=a.parentNode)||void 0===o||o.insertBefore(i,a)};!function(e,t,n){e.KwaiAnalyticsObject=n;var i=e[n]=e[n]||[];i.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];var a=function(e,t){e[t]=function(){for(var n=[],r=0;r<arguments.length;r++)n[r]=arguments[r];var i=o([t],n,!0);e.push(i)}};i.methods.forEach((function(e){a(i,e)})),i.instance=function(e){var t,n=(null===(t=i._i)||void 0===t?void 0:t[e])||[];return i.methods.forEach((function(e){a(n,e)})),n},i.load=function(e,o){var a="//s16-def.ap4r.com/kos/s101/nlav11187/pixel/events.js";i._i=i._i||{},i._i[e]=[],i._i[e]._u=a,i._t=i._t||{},i._t[e]=+new Date,i._o=i._o||{},i._o[e]=o||{};var c="?sdkid=".concat(e,"&lib=").concat(n);r(t,a+c,"https://s16-11187.ap4r.com/kos/s101/nlav11187/pixel/events.js"+c)}}(window,document,"kwaiq")}])}));
        </script>
        <script>
            <?php foreach ($kwaiAfiliado as $kwaiPixelId): ?>
                kwaiq.load("<?= trim($kwaiPixelId) ?>");
                kwaiq.page();
            <?php endforeach; ?>
        </script>
    <?php endif; ?>





    <?php

    if (@$checkout->pixel_taboola) :
        $pixels = explode(",", $checkout->dataPixelTaboola()->id_pixel);
        $eventoTaboola = explode(",", $checkout->dataPixelTaboola()->evento_taboola);
        $index = 0;
        foreach ($pixels as $pixel) :
            $eventos = @$eventoTaboola[$index];
            if (@$checkout->dataPixelTaboola()->executar_checkout == 1) :
                $index++;
                echo "
                    <!-- PIXEL TABOOLA CHECKOUT -->
                    <!--iframe src='" . url("checkout/pixel-taboola?pixel=$pixel&ev=" . @$eventos[$index]) . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe-->
                    <!-- Taboola Pixel Code -->
                    <script type='text/javascript'>
                    window._tfa = window._tfa || [];
                    window._tfa.push({notify: 'event', name: 'page_view', id: $pixel});
                    !function (t, f, a, x) {
                            if (!document.getElementById(x)) {
                                t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
                            }
                    }(document.createElement('script'),
                    document.getElementsByTagName('script')[0],
                    '//cdn.taboola.com/libtrc/unip/$pixel/tfa.js',
                    'tb_tfa_script');
                    </script>
                    <!-- End of Taboola Pixel Code --> 
                ";

                if (@$eventos) :
                    echo "
                        <!-- Taboola Pixel Code -->
                        <script>
                            _tfa.push({notify: 'event', name: '$eventos', id: $pixel});
                        </script>
                        
                        <!-- End of Taboola Pixel Code -->
                    ";
                endif;
            endif;
        endforeach;
    endif;
    ?>

    <?php
    if (@$checkout->pixel_google_analitics) :
        $google = explode(",", $checkout->dataPixelGoogleAnalitics()->id_pixel);
        foreach ($google as $google) :
            if (@$checkout->dataPixelGoogleAnalitics()->executar_checkout == 1) :
                echo "<script async src='https://www.googletagmanager.com/gtag/js?id=$google'></script>
                        <script>
                            window.dataLayer = window.dataLayer || [];
                            function gtag(){dataLayer.push(arguments);}
                            gtag('js', new Date());
                            gtag('config', '$google');
                        </script>";
            endif;
        endforeach;
    endif;

    if (@$checkout->pixel_google_ads) :
        $google = explode(",", $checkout->dataPixelGoogleAds()->id_pixel);
        foreach ($google as $google) :
            if (@$checkout->dataPixelGoogleAds()->executar_checkout == 1) :
                echo "<script async src='https://www.googletagmanager.com/gtag/js?id=$google'></script>
                        <script>
                            window.dataLayer = window.dataLayer || [];
                            function gtag(){dataLayer.push(arguments);}
                            gtag('js', new Date());
                            gtag('config', '$google');
                        </script>";
            endif;
        endforeach;
    endif;

    if (@$checkout->pixel_google_tag_manager) :
        $google = explode(",", $checkout->dataPixelGoogleTagManager()->id_pixel);
        foreach ($google as $google) :
            echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','$google');</script>
                    <!-- End Google Tag Manager -->
                ";
        endforeach;
    endif;

    if (@$checkout->pixel_tiktok) :
        $tiktok = explode(",", $checkout->dataPixelTikTok()->id_pixel);
        foreach ($tiktok as $tiktok) :
            echo "<script>
                    !function (w, d, t) {
                    w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};
                    ttq.load('$tiktok');
                    ttq.page();
                    }(window, document, 'ttq');
                </script>";
        endforeach;
    endif;
        
    ?>

    <?php if (@$checkout->pixel_kwai): ?>
        <script>
        !function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.install=t():e.install=t()}(window,(function(){return function(e){var t={};function n(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}return n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"_esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(o,r,function(t){return e[t]}.bind(null,r));return o},n.n=function(e){var t=e&&e.esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){"use strict";var o=this&&this.spreadArray||function(e,t,n){if(n||2===arguments.length)for(var o,r=0,i=t.length;r<i;r++)!o&&r in t||(o||(o=Array.prototype.slice.call(t,0,r)),o[r]=t[r]);return e.concat(o||Array.prototype.slice.call(t))};Object.defineProperty(t,"_esModule",{value:!0});var r=function(e,t,n){var o,i=e.createElement("script");i.type="text/javascript",i.async=!0,i.src=t,n&&(i.onerror=function(){r(e,n)});var a=e.getElementsByTagName("script")[0];null===(o=a.parentNode)||void 0===o||o.insertBefore(i,a)};!function(e,t,n){e.KwaiAnalyticsObject=n;var i=e[n]=e[n]||[];i.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];var a=function(e,t){e[t]=function(){for(var n=[],r=0;r<arguments.length;r++)n[r]=arguments[r];var i=o([t],n,!0);e.push(i)}};i.methods.forEach((function(e){a(i,e)})),i.instance=function(e){var t,n=(null===(t=i._i)||void 0===t?void 0:t[e])||[];return i.methods.forEach((function(e){a(n,e)})),n},i.load=function(e,o){var a="//s16-def.ap4r.com/kos/s101/nlav11187/pixel/events.js";i._i=i._i||{},i._i[e]=[],i._i[e]._u=a,i._t=i._t||{},i._t[e]=+new Date,i._o=i._o||{},i._o[e]=o||{};var c="?sdkid=".concat(e,"&lib=").concat(n);r(t,a+c,"https://s16-11187.ap4r.com/kos/s101/nlav11187/pixel/events.js"+c)}}(window,document,"kwaiq")}])}));
        </script>
        <script>
            <?php
            $kwaiPixels = explode(",", @$checkout->dataPixelKwai()->id_pixel);
            foreach ($kwaiPixels as $kwaiPixelId):
            ?>
            kwaiq.load('<?= trim($kwaiPixelId) ?>');
            kwaiq.page();
            <?php endforeach; ?>
        </script>
    <?php endif; ?>




<style>
  body {
    background-color: #121212;
    color: #f5f5f5;
    font-family: 'Segoe UI', sans-serif;
  }

  .cabecalho-novo {
    background-color: #1E1E1E;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.05);
  }

  .titulo-secao {
    font-size: 1.2rem;
    font-weight: bold;
    color: #00C853;
  }

  .corpo {
    background-color: #1E1E1E;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(255,255,255,0.05);
  }

  .form-control, .newform, select {
    background-color: #2A2A2A;
    border: 1px solid #444;
    color: #fff;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    width: 100%;
  }

  .form-control:focus, .newform:focus, select:focus {
    border-color: #00C853;
    outline: none;
    box-shadow: 0 0 5px rgba(0,200,83,0.5);
  }

  .btn-pagamento {
    background: linear-gradient(90deg, #00C853, #64DD17);
    color: #fff;
    border: none;
    padding: 0.75rem;
    font-weight: bold;
    border-radius: 10px;
    transition: background 0.3s ease;
  }

  .btn-pagamento:hover {
    background: linear-gradient(90deg, #00E676, #76FF03);
  }

  .nav-tabs .nav-link {
    background-color: #1E1E1E;
    color: #ccc;
    border: 1px solid #333;
    border-radius: 8px;
    margin-right: 5px;
  }

  .nav-tabs .nav-link.active {
    background-color: #00C853;
    color: #000;
    font-weight: bold;
  }

  label {
    font-weight: 500;
    font-size: 0.9rem;
    color: #ccc;
  }

  small {
    color: #aaa;
  }

  .total {
    font-size: 1.2rem;
    color: #00E676;
    font-weight: bold;
  }
  .resumo {
    background-color: #1E1E1E;
  }

  .resumo hr {
    border-top: 1px solid #333;
  }

  .container-75{
    background-color: #1E1E1E;
  }

  .imagem-produto {
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255,255,255,0.1);
  }

  #pix-page.active {
    display: flex !important;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 40px;
    background: #1E1E1E;
    min-height: 100vh;
}

#pix-page .row {
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
}

</style>

</head>
<!-- FIM DO HEAD -->

<!-- BODY -->
<body>
    <?php
    if (@$checkout->pixel_google_tag_manager) :
        $google = explode(",", $checkout->dataPixelGoogleTagManager()->id_pixel);
        foreach ($google as $google) :
    ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $google ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php endforeach;
    endif; ?>

    <?php
    if (@$tagManager) :
        $tagPixels = explode(",", @$tagManager->id_pixel);
        foreach ($tagPixels as $pixel) :
            if (@$tagManager->executar_checkout == 1) :
                echo "<!-- Google Tag Manager (noscript) -->
                        <noscript><iframe src='https://www.googletagmanager.com/ns.html?id=" . $pixel . "'
                        height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
                        <!-- End Google Tag Manager (noscript) -->";
            endif;
        endforeach;
    endif;
    ?>

    <?php
    if (@$tiktok) :
        $tiktok = explode(",", @$tiktok->id_pixel);
        foreach ($tiktok as $tiktok) :
            echo "<script>ttq.instance('$tiktok').track('InitiateCheckout')</script>";
        endforeach;
    endif;
    ?>

    <?php
    if (@$checkout->pixel_tiktok) :
        $tiktok = explode(",", @$checkout->dataPixelTikTok()->id_pixel);
        foreach ($tiktok as $tiktok) :
            echo "<script>ttq.instance('$tiktok').track('InitiateCheckout')</script>";
        endforeach;
    endif;
    ?>

    <?php if (@$kwai): ?>
        <?php
        $kwaiPixels = explode(",", @$kwai->id_pixel);
        foreach ($kwaiPixels as $kwaiPixelId):
            echo "<script>kwaiq.track('InitiateCheckout');</script>";
        endforeach;
        ?>
    <?php endif; ?>

    <?php if (@$checkout->pixel_kwai): ?>
        <?php
        $kwaiPixels = explode(",", @$checkout->dataPixelKwai()->id_pixel);
        foreach ($kwaiPixels as $kwaiPixelId):
            echo "<script>kwaiq.track('InitiateCheckout');</script>";
        endforeach;
        ?>
    <?php endif; ?>



    <!-- CONTADOR -->
    <?php
    $contador = @$_COOKIE[$checkout->codigo];
    $precoantigo = $plano->preco;
    if (@$contador) {
        $preco = number_format($contador, 2);
        $valor_redrocket = number_format(($preco * ($taxa_redrocket / 100)) + 1, 2);
        $valor_liquido = number_format($preco - $valor_redrocket, 2);
        $valor_cupom = 0;
    }
    ?>

    <style>
        .contador-topo {
            background: <?= @$checkout->cor_contador ? $checkout->cor_contador : "#f00" ?>;
            color: <?= @$checkout->cor_texto ? $checkout->cor_texto : "#fff" ?>;
        }

        .input-error {
            border-color: red;
            color: red;
        }
    </style>

    <?php if (@$checkout->usar_contador == 1) : ?>
        <div class="contador-topo">
            <div class="container">
                <div class="contador row">
                    <div class="col-md-6">
                        <h4><?= @$checkout->texto_contador ?></h4>
                    </div>
                    <div class="col-md-6">
                        <p class="timer">
                            <script type="text/javascript">
                                function startTimer(duration, display) {
                                    var timer = duration,
                                        minutes, seconds;
                                    var intervall = null;
                                    var meutempo = function() {
                                        minutes = parseInt(timer / 60, 10)
                                        seconds = parseInt(timer % 60, 10);

                                        minutes = minutes < 10 ? "0" + minutes : minutes;
                                        seconds = seconds < 10 ? "0" + seconds : seconds;

                                        display.textContent = minutes + ":" + seconds;

                                        --timer;
                                        if (timer <= -1) {
                                            timer = -1;
                                            <?php
                                            if (@$checkout->acao_contador == 1) {
                                                $aumento = @$checkout->aumento > 0 ? $checkout->aumento : 0.01;
                                                $novopreco = $precoantigo + ($precoantigo * ($aumento / 100));
                                                setcookie($checkout->codigo, $novopreco, time() + (60 * 60 * 24), "/");
                                            }
                                            ?>

                                            <?php if (!@$contador && @$checkout->acao_contador == 1) : ?>
                                                alert("Promoção Encerrada");
                                                location.reload();
                                            <?php endif; ?>

                                            //location.reload();
                                            clearInterval(intervall);
                                        }
                                    };

                                    intervall = setInterval(meutempo, 1000);
                                }

                                window.onload = function() {
                                    var tempo = <?= @$contador ? 0 : 10 ?>;
                                    var fiveMinutes = 60 * tempo,
                                        display = document.querySelector('#time');
                                    startTimer(fiveMinutes, display);
                                };
                            </script>
                            <strong><span id="time"><?= @$contador ? "00:00" : "10:00" ?></span></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- FIM CONTADOR -->

    <!-- LOAD -->
    <script>
        function load(action) {
            var load_div = $(".ajax_load");
            if (action === "open") {
                load_div.fadeIn().css("display", "flex");
            } else {
                load_div.fadeOut();
            }
        }
    </script>

    <?php if (@$sandbox == 1) : ?>
        <div class="container-fluid center bg-danger">MODO SANDBOX</div>
    <?php endif; ?>

    <?php if (@$statusProduto != 2 || $statusPlano != 2) : ?>
        <div class="container-fluid center bg-danger">Produto Não Habilitado Para Venda</div>
    <?php endif; ?>

    <div class="ajax_load">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
            <div class="ajax_load_box_title">Aguarde, carregando!</div>
        </div>
    </div>
    <!-- FIM LOAD -->

    <!-- CONTAINER PRINCIPAL -->
    <div class="container-75">
        <!-- HEADER -->
        <div class="row">
            <div class="col-md-12">
                <div class="header">
                    <?php if (@$banner != "") : ?>
                        <img class="banner-checkout" src="<?= url("uploads/checkout/" . @$banner); ?>" alt="Banner Checkout">
                    
                    <?php endif; ?>

                    <?php if (@$aviso != "") : ?>
                        <img id="img-aviso" src="<?= url("uploads/checkout/" . @$aviso); ?>" alt="SubBanner">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- FIM HEADER -->

        <!-- CORPO CENTRAL DO CHECKOUT -->
        <div class="row mt-4">
            <!-- LADO ESQUERDO - DADOS -->
            <div class="col-md-7">
                <!-- FORMULÁRIO DADOS PESSOAIS -->
                <?php
                $cartao = (new Cartao())->find("usuario = $checkout->usuario AND status = 2")->count();
                if ($cartao > 0 && $checkout->adquirente != "redrocket") {
                    $url_transacao = $router->route("checkout.transacaoTransparente");
                } else {
                    $url_transacao = $router->route("checkout.transacao");
                }
                // echo $url_transacao;
                // echo $checkout->adquirente;
                ?>
                <form action="<?= $url_transacao; ?>" method="post" name="checkout">
                    <div class="cabecalho-novo">
                        <span class="titulo-secao">DADOS PESSOAIS</span>
                    </div>
                    <!-- DADOS PESSOAIS -->
                    <div class="corpo mb-4">
                        <?php if (@$cookies->id) : ?>
                            <input type="hidden" name="cookie_id" id="cookie_id" value="<?= @$cookies->id ?>">
                        <?php endif; ?>
                        <?php if ($checkout->vencimento_boletos) : ?>
                            <input type="hidden" name="vencimento" id="vencimento" value="<?= @$data_vencimento ?>">
                        <?php endif; ?>
                        <?php if (@$cookies_televendas != "") : ?>
                            <input type="hidden" name="cookie_telev_atend" id="cookie_telev_atend" value="<?= $cookies_televendas->id; ?>">
                        <?php endif; ?>

                        <?php if (@$_GET["ec"]) : ?>
                            <input type="hidden" name="ecommerce" id="ecommerce" value="1">
                        <?php endif ?>

                        <input type="hidden" name="adquirente" id="adquirente" value="<?= @$checkout->adquirente; ?>">
                        <input type="hidden" name="adquirente_boleto" id="adquirente_boleto" value="<?= @$checkout->adquirente_boleto; ?>">
                        <input type="hidden" name="adquirente_pix" id="adquirente_pix" value="<?= @$checkout->adquirente_pix; ?>">

                        <?php $nome_fatura = $produto->nome_fatura ? $produto->nome_fatura : "RedRocket"; ?>
                        <?php $id_cookie = @$cookie->id ? @$cookie->id : null; ?>

                        <input type="hidden" name="nome_fatura" id="nome_fatura" value="<?= @$nome_fatura; ?>">
                        <input type="hidden" name="cookie" id="cookie" value="<?= @$id_cookie; ?>">
                        <input type="hidden" name="codigo_checkout" id="codigo_checkout" value="<?= @$codigo_checkout; ?>">
                        <input type="hidden" name="endereco_obrigatorio" id="endereco_obrigatorio" value="<?= @$checkout->endereco_obrigatorio; ?>">

                        <input type="hidden" name="preco" id="preco" value="<?= @$preco; ?>">
                        <input type="hidden" name="taxa_redrocket" value="<?= @$taxa_redrocket; ?>">
                        <input type="hidden" name="taxa_juros" value="<?= @$taxa_juros; ?>">
                        <input type="hidden" name="valor_redrocket" id="valor_redrocket" value="<?= @$valor_redrocket; ?>">
                        <input type="hidden" name="valor_liquido" id="valor_liquido" value="<?= @$valor_liquido; ?>">
                        <input type="hidden" name="valor_produto" value="<?= @$precoantigo; ?>">
                        <input type="hidden" name="valor_desconto" id="valor_desconto" value="0">
                        <input type="hidden" name="meio_pagamento_desconto" id="meio_pagamento_desconto">

                        <input type="hidden" name="nomekit" value="<?= $nomeProduto; ?>">
                        <input type="hidden" name="nome_produto" value="<?= @$produto->nome; ?>">
                        <input type="hidden" name="produto" value="<?= $idProduto; ?>">
                        <input type="hidden" name="descricao" value="<?= @$produto->descricao; ?>">
                        <input type="hidden" name="categoria_id" value="<?= @$produto->categoria; ?>">
                        <input type="hidden" name="foto" value="<?= url("uploads/produtos/" . @$produto->foto); ?>">

                        <input type="hidden" name="produtor" value="<?= $produtor; ?>">
                        <input type="hidden" name="comissao_produtor" value="<?= @$resto; ?>">
                        <input type="hidden" name="email_suporte" id="email_suporte" value="<?= $email_suporte; ?>">

                        <input type="hidden" name="url_checkout" value="<?= $url_checkout; ?>">
                        <input type="hidden" name="nasc" value="<?= $nascimento; ?>">

                        <input type="hidden" name="carrinho_abandonado" id="carrinho_abandonado">
                        <input type="hidden" name="coprodutores" id="coprodutores" value="<?= $coprodutores; ?>">
                        <input type="hidden" name="comissao_coprodutores" id="comissao_coprodutores" value="<?= $comissao_coprodutor; ?>">
                        <input type="hidden" name="st4rt" value="<?= @$produto->st4rt; ?>">
                        <input type="hidden" name="checkout1" value="<?= $checkout->id; ?>">
                        <input type="hidden" name="bandeira" id="brand">

                        <?php if (@$idPlano) ?>
                        <input type="hidden" name="plano" value="<?= $idPlano; ?>">

                        <input type="hidden" name="src" value="<?= @$src; ?>">
                        <input type="hidden" name="utm_source" value="<?= @$utm_source; ?>">
                        <input type="hidden" name="utm_medium" value="<?= @$utm_medium; ?>">
                        <input type="hidden" name="utm_campaign" value="<?= @$utm_campaign; ?>">
                        <input type="hidden" name="utm_content" value="<?= @$utm_content; ?>">

                        <input type="hidden" name="obrigado_boleto" value="<?= $obrigado_boleto; ?>">
                        <input type="hidden" name="obrigado_cartao" value="<?= $obrigado_cartao; ?>">
                        <input type="hidden" name="obrigado_analise" value="<?= $obrigado_analise; ?>">

                        <input type="hidden" name="tipo_frete" id="tipo_frete">

                        <?php if (@$afiliado) : ?>
                            <input type="hidden" name="afiliado" value="<?= @$afiliado; ?>">
                            <input type="hidden" name="comissao_afiliado" value="<?= @$comissao; ?>">
                            <input type="hidden" name="comissao_afiliado_assinatura" value="<?= @$comissao_assinatura; ?>">
                        <?php endif; ?>

                        <?php if (@$idCamp) : ?>
                            <input type="hidden" name="campanha" value="<?= @$idCamp; ?>">
                        <?php endif; ?>

                        <?php if (@$plano->assumir_juros) : ?>
                            <input type="hidden" name="assumir_juros" value="<?= @$plano->assumir_juros; ?>">
                        <?php endif; ?>

                        <?php if (@$venda) : ?>
                            <input type="hidden" name="venda" id="venda" value="<?= $venda->id; ?>">
                        <?php endif; ?>

                        <div>
                            <!-- CAMPOS VISÍVEIS -->
                            <label for="nome" class="labelcheckout">NOME COMPLETO *</label><br />
                            <?php $nome = @$venda ? $venda->dataEndereco()->nome : "" ?>
                           <input type="text" id="nome" name="nome" placeholder="Nome Completo" class="form-control newform required" value="<?= @$nome ?>" required>
                        </div>

                        <div>
                            <label for="email"> E-MAIL </label> <small>(Opcional)</small><br />
                            <?php $email = @$venda ? $venda->dataEndereco()->email : "" ?>
                           <input type="email" id="email" name="email" placeholder="E-mail" value="<?= @$email ?>" class="form-control newform required" required>
                            <span id="msg-email"></span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="telefone"> TELEFONE - <small>somente números *</small></label><br />
                               <input type="hidden" name="ddd" id="ddd" placeholder="DDD" class="form-control" maxlength="3" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                                <?php $telefone = @$venda ? $venda->dataEndereco()->telefone : "" ?>
                                <input type="tel" name="telefone" id="telefone" placeholder="DDD + Telefone" class="form-control newform" value="<?= @$telefone ?>" maxlength="11" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                            </div>
                            <div class="col-md-6 check-line">
                                <label for="cpf"> CPF/CNPJ *</label><br />
                                <?php $cpf_cnpj = @$venda ? $venda->dataEndereco()->cpf_cnpj : "" ?>
                                <input type="tel" id="cpf" name="cpf" value="<?= @$cpf_cnpj ?>" class="form-control newform required" placeholder="Somente números" maxlength="14" onblur="limpaNum(this.id);" onkeypress="return allowOnlyNumbers(event)" required>
                                <span id="msg-cpf"></span>
                            </div>
                        </div>
                    </div>
                    <!-- DADOS PESSOAIS -->

                    <?php if ($checkout->endereco_obrigatorio == 1) : ?>
                        <div class="cabecalho-novo">
                            <!-- CABEÇALHO  FORMULÁRIO ENTREGA -->
                            <?php if ($produto->tipo_produto == 8) : ?>
                                <span class="titulo-secao">DADOS DE ENTREGA</span>
                            <?php else : ?>
                                <span class="titulo-secao">DADOS DE COBRANÇA</span>
                            <?php endif ?>
                        </div>

                        <!-- CORPO FORMULÁRIO ENTREGA -->
                        <div class="corpo mb-4">
                            <div class="row">
                                <div class="col-6">
                                    <label for="cep"> CEP *</label><br>
                                    <?php $cep = @$venda ? $venda->dataEndereco()->cep : "" ?>
                                    <input type="tel" id="cep" name="cep" value="<?= @$cep ?>" class="form-control newform newform4 required" placeholder="Somente números" maxlength="8" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                                    <span id="msg-cep"></span>
                                </div>
                                <div class="col-6"></div>
                            </div>

                            <label for="rua"> ENDEREÇO *</label><br />
                            <?php $rua = @$venda ? $venda->dataEndereco()->rua : "" ?>
                            <input type="text" id="rua" name="rua" placeholder="Endereço" value="<?= @$rua ?>" class="form-control newform required" required>

                            <div class="row">
                                <div class="col-6 width100">
                                    <label for="numero"> NÚMERO *</label><br />
                                    <?php $numero = @$venda ? $venda->dataEndereco()->numero : "" ?>
                                    <input type="text" id="numero" name="numero" value="<?= @$numero ?>" class="form-control newform  required" placeholder="Somente números" maxlength="6" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                                </div>

                                <div class="col-6 width100">
                                    <label for="complemento"> COMPLEMENTO</label><br />
                                    <?php $complemento = @$venda ? $venda->dataEndereco()->complemento : "" ?>
                                    <input type="text" id="complemento" name="complemento" value="<?= @$complemento ?>" placeholder="Casa B" class="form-control newform ">
                                </div>
                            </div>

                            <label for="bairro"> BAIRRO *</label><br />
                            <?php $bairro = @$venda ? $venda->dataEndereco()->bairro : "" ?>
                            <input type="text" id="bairro" name="bairro" value="<?= @$bairro ?>" class="form-control newform required" required>

                            <div class="row">
                                <div class="col-6 width100">
                                    <label for="cidade"> CIDADE *</label><br />
                                    <?php $cidade = @$venda ? $venda->dataEndereco()->cidade : "" ?>
                                    <input type="text" id="cidade" name="cidade" value="<?= @$cidade ?>" class="form-control newform required" required>
                                </div>

                                <div class="col-6 width100">
                                    <label for="estado"> ESTADO *</label><br />
                                    <?php
                                    if (@$venda) :
                                        $v->insert("fragments/uf-venda", []);
                                    else :
                                        $v->insert("fragments/uf", []);
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <br>
                    <?php endif; ?>
                    <!-- FIM SEÇÃO FORMULÁRIO ENTREGA -->
                </form>
            </div>
            <!-- FIM LADO ESQUERDO -->

            <!-- LADO DIREITO - PAGAMENTO -->
            <div class="col-md-5">
                <div class="cabecalho-novo">
                    <!-- CABEÇALHO PAGAMENTOS -->
                    <span class="titulo-secao">VOCÊ ESTÁ COMPRANDO</span>
                </div>
                <!-- RESUMO DA COMPRA -->
                <div class="corpo mb-4">
                    <div class="row resumo">
                        <div class="col-md-9">
                            <?php $foto = $plano->foto ? $plano->foto : $produto->foto ?>
                            <div class="col-md-3 showmobile" style="text-align: center">
                                <img class="imagem-produto" src="<?= url("uploads/produtos/$foto") ?>" alt="" width="100%">
                            </div>
                            Você está adquirindo <?= @$plano->quantidade > 0 ? @$plano->quantidade : "" ?> <?= @$produto->nome ?> <?= @$checkout->plano ? " / " . @$plano->nome : "" ?>
                            <hr>
                            <?php if (@$produto->tipo_cobranca == 2) : ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>Pacote: </b><?= $plano->periodo ?>
                                        <hr>
                                        <?php if (@$checkout->mostrar_preco) : ?>
                                            <span><b>Valor hoje:</b> R$ <?= number_format(@$preco, 2, ",", ".") ?></span>
                                            <hr>
                                        <?php endif; ?>

                                        <?php if ($plano->maximo_recorrencia > 0) : ?>
                                            <span><b>Recorrência: </b><?= $plano->maximo_recorrencia ?>x</span>
                                            <hr>

                                            <?php if (@$checkout->mostrar_preco) : ?>
                                                <span><b>Total: </b>R$ <?= number_format($preco + $plano->preco * ($plano->maximo_recorrencia - 1), 2, ",", ".") ?></span>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (@$checkout->mostrar_preco && $produto->tipo_cobranca != 2) : ?>
                                <div class="row">
                                    <div class="col-md-12"><b>Valor do produto:</b> R$ <?= number_format(@$plano->preco, 2, ",", ".") ?></div>
                                </div>
                            <?php endif; ?>

                            <small style="font-size:10px">
                                <?php
                                if (@$_COOKIE[$produto->id]) {
                                    parse_str($_COOKIE[$produto->id], $output);
                                    echo "ref.: {$output["codigo"]} | {$output["afiliacao"]} | $cookies->afiliado<br>";
                                }
                                ?>
                            </small>
                        </div>

                        <div class="col-md-3 hidedesk" style="text-align: center">
                            <?php if (@$plano->shopify == 1) : ?>
                                <img class="imagem-produto" src="<?= @$plano->foto != "" ? @$plano->foto : @$produto->foto; ?>" alt="" width="100%">
                            <?php else : ?>
                                <img class="imagem-produto" src="<?= url("uploads/produtos/$foto") ?>" alt="" width="100%">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- FIM RESUMO DA COMPRA -->

                <!-- FORMAS PAGAMENTO -->
                <div class="cabecalho-novo">
                    <!-- CABEÇALHO FORMULÁRIO ENTREGA -->
                    <span class="titulo-secao">DADOS DE PAGAMENTO</span>
                </div>

                <div class="corpo mb-4">
                    <!-- CUPOM DE DESCONTO -->
                    <!-- <div class="row">
                        <div class="col-md-12">
                            <strong>POSSUI CUPOM DE DESCONTO?<br /></strong>
                            <input type="checkbox" id="btn-mostrar-cupom"></input> <a id="btn-mostrar-cupom"> Sim, tenho um cupom</a>
                        </div>
                    </div> -->

                    <form action="#" method="post" name="form-cupom">
                        <div class="row" id="div-cupom">
                            <div class="col-md-12">
                                <label>Informe o cupom:</label>

                                <div class="input-group mb-2 mr-sm-2">
                                    <input type="text" name="cupom" id="cupom" class="form-control newform" placeholder="Cupom">
                                    <div class="input-group-append">
                                        <div class="input-group-text bt"><i class="fas fa-search"></i></div>
                                    </div>
                                </div>

                                <style>
                                    .bt:hover {
                                        cursor: pointer
                                    }
                                </style>

                                <span id="msg-cupom" style="color: red; font-size: 12px;"></span>
                                <br>

                                <button type="button" id="btn-aplicar-cupom" class="btn btn-success">APLICAR DESCONTO</button>
                            </div>
                            <br>
                        </div>
                    </form>
                    <!-- FIM CUPOM DE DESCONTO -->

                    <!-- TABS DE NAVEGAÇÃO -->
                    <span><strong>PAGAR COM:</strong></span>
                    <p><small>Escolha uma forma de pagamento:</small></p>
                    <nav id="links">
                        <div class="nav nav-tabs tab-checkout-tabs" id="nav-tab" role="tablist">
                            <?php
                            $meio_pagamento = explode(",", $checkout->meio_pagamento);
                            $adquirente = $checkout->adquirente == "redrocket" ? "redrocket" : $checkout->dataAdquirente()->adquirente;
                            // $adquirente_pix = $checkout->adquirente == "redrocket" ? 1 : $checkout->dataAdquirente()->pix_redrocket;

                            if ($meio_pagamento[0] == 1) : //Cartão
                                $cartao = "active";
                                $show_cartao = "show active";
                            elseif ($meio_pagamento[0] == 2) : //Boleto
                                $boleto = "active";
                                $show_boleto = "show active";
                            elseif ($meio_pagamento[0] == 3) : //Pix
                                $pix_pagamento = "active";
                                $show_pix = "show active";
                            else :
                                $cartao = "active";
                                $show_cartao = "show active";
                            endif;

                            foreach ($meio_pagamento as $pagamento) :
                                if ($pagamento == 1) : ?>
                                    <a class="nav-item nav-link <?= $cartao ?>" id="nav-home-tab" data-toggle="tab" href="#cartao" role="tab" aria-controls="nav-home" aria-selected="true"><b>Cartão de Crédito</b></a>
                                <?php
                                elseif ($pagamento == 2) : ?>
                                    <a class="nav-item nav-link <?= $boleto ?>" id="nav-profile-tab" data-toggle="tab" href="#boleto" role="tab" aria-controls="nav-profile" aria-selected="false"><b>Boleto</b></a>
                                <?php
                                elseif ($pagamento == 3) : ?>
                                    <a class="nav-item nav-link <?= $pix_pagamento ?>" id="nav-pix-tab" data-toggle="tab" href="#pix-tab" role="tab" aria-controls="nav-pix" aria-selected="false"><b>Pix</b></a>
                                <?php
                                else : ?>
                                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#cartao" role="tab" aria-controls="nav-home" aria-selected="true"><b>Cartão de Crédito</b></a>
                                    <a class="nav-item nav-link" id="nav-pix-tab" data-toggle="tab" href="#pix-tab" role="tab" aria-controls="nav-pix" aria-selected="false"><b>Pix</b></a>
                                    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#boleto" role="tab" aria-controls="nav-profile" aria-selected="false"><b>Boleto</b></a>
                            <?php endif;
                            endforeach;
                            ?>
                        </div>
                    </nav>
                    <!-- FIM TABS DE NAVEGAÇÃO -->


                    <!-- CONTEÚDOS TABS -->
                    <div class="tab-content" id="nav-tabContent">

                        <!-- CONTEÚDO TAB CARTÃO -->
                        <div class="tab-pane fade show <?= @$show_cartao ?>" id="cartao" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="row" style="padding-left: 15px;">
                                <span class="brand"><img src="<?= url("pagamentos/imgs/brands/master.jpg"); ?>" alt="" width="45px" id="mastercard"></span>
                                <span class="brand"><img src="<?= url("pagamentos/imgs/brands/visa.jpg"); ?>" alt="" width="45px" id="visa"></span>
                                <span class="brand"><img src="<?= url("pagamentos/imgs/brands/elo.jpg"); ?>" alt="" width="45px" id="elo"></span>
                                <span class="brand"><img src="<?= url("pagamentos/imgs/brands/amex.jpg"); ?>" alt="" width="45px" id="amex"></span>
                                <span class="brand"><img src="<?= url("pagamentos/imgs/brands/hiper.jpg"); ?>" alt="" width="45px" id="hipercad"></span>
                                <span class="brand"><img src="<?= url("pagamentos/imgs/brands/diners.jpg"); ?>" alt="" width="45px" id="diners"></span>
                                <input type="hidden" id="url_base" value="<?= url() ?>">
                            </div>
                            <form action="" method="post" name="cartao" id="form_cartao">

                                <input id="MPHiddenInputToken" name="MPHiddenInputToken" type="hidden" />

                                <div class="check-line">
                                    <label for="numcart">NÚMERO DO CARTÂO:</label><br />
                                    <input type="tel" id="numcart" name="numcart" placeholder="Somente números" class="form-control newform cartao"
                                    maxlength="19" oninput="formatarCartao(this)" onblur="validaCartao(this.value)">
                                </div>

                                <div class="check-line">
                                    <label for="titular">NOME DO TITULAR:</label><br />
                                    <input type="text" id="titular" name="titular" class="form-control newform cartao" onblur="validaTitular(value, this.id)">
                                </div>

                                <div class="row">
                                    <div class="col-7">
                                        <label for="validade"> <i class="far fa-calendar-alt"></i> DATA DE VALIDADE:</label>
                                        <div class="col-40 pr-2">
                                            <!-- SELECT MES -->
                                            <select name="mes-val" id="mes-val" class="form-control newform">
                                                <?php
                                                $mes = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
                                                for ($i = 0; $i < 12; $i++) :
                                                ?>
                                                    <option value="<?= $mes[$i]; ?>"><?= $mes[$i]; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div><!-- FIM SELECT MES -->

                                        <div class="col-40">
                                            <!-- SELECT ANO -->
                                            <select name="ano-val" id="ano-val" class="form-control newform cartao">
                                                <?php
                                                $ano = date('Y');
                                                for ($i = 0; $i < 16; $i++) :
                                                ?>
                                                    <option value="<?= $ano; ?>"><?= $ano; ?></option>
                                                <?php
                                                    $ano++;
                                                endfor;
                                                ?>
                                            </select>
                                        </div><!-- FIM SELECT ANO -->

                                    </div>

                                    <div class="col-5">
                                        <label for="cvv"><i class="fas fa-lock"></i> CVC:</label>
                                        <input type="text" id="cvv" name="cvv" class="form-control newform cartao" maxlength="5" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)">
                                    </div>
                                </div>

                                <!-- analisar -->
                                <?php if (@$checkout->usar_frete == 1 || @$checkout->usar_order_bump == 1 || $cupom == 1 || @$checkout->dataFrete()->tipo_frete > 0) : ?>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-5"><b>Valor produto:</b></div>
                                        <div class="col-md-7">
                                            <?= "R$ " . number_format(@$preco, 2, ",", ".") ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- <div class="icone-frete" stlye="display: none;"><i class="fas fa-truck"></i></div>  -->
                                <!-- analisar -->

                                <span class="icone-frete" style="display: none;">
                                    <br>
                                    <hr>
                                    <i class="fas fa-truck"></i> Frete:<br>
                                </span>

                                <div class="calculo-frete">
                                </div>

                                <div class="frete-antigo">
                                    <?php if (@$checkout->usar_frete == 1 && @$checkout->id_frete == "") : ?>
                                        <label><i class="fas fa-truck"></i> Frete:</label><br>
                                        <label for="frete">
                                            <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked>
                                            Até <?= $checkout->prazo_entrega ?> dias úteis - R$
                                            <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>

                                    <?php if (@$checkout->frete_gratis == 1 && @$checkout->id_frete == "") : ?>
                                        <br>
                                        <label for="frete_gratis">
                                            <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                                            Até <?= $checkout->prazo_entrega_gratis ?> dias úteis - R$
                                            <?= number_format(0, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>
                                </div>


                                <?php if (@$checkout->id_frete != "" && (@$checkout->dataFrete()->tipo_frete == 2 || @$checkout->dataFrete()->tipo_frete == 3)) : ?>

                                    <div class="calculo-frete-correios">
                                        <label><i class="fas fa-truck"></i> Frete:</label><br>

                                        <?php if (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 0) : ?>
                                            <label for="frete">
                                                <input class="frete-pac" type="radio" name="frete" id="frete" onclick="tipoFrete('pac')" checked>
                                            </label>
                                            <span class="frete-pac"></span>

                                            <br>

                                            <label for="frete">
                                                <input class="frete-sedex" type="radio" name="frete" id="frete" onclick="tipoFrete('sedex')" checked>
                                            </label>
                                            <span class="frete-sedex"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 1) : ?>
                                            <label for="frete">
                                                <input class="frete-sedex" type="radio" name="frete" id="frete" onclick="tipoFrete('sedex')" checked>
                                            </label>
                                            <span class="frete-sedex"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 2) : ?>

                                            <label for="frete">
                                                <input class="frete-pac" type="radio" name="frete" id="frete" onclick="tipoFrete('pac')" checked>
                                            </label>
                                            <span class="frete-pac"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 3) : ?>

                                            <label for="frete">
                                                <input class="frete-jadlog" type="radio" name="frete" id="frete" onclick="tipoFrete('jadlog')" checked>
                                            </label>
                                            <span class="frete-jadlog"></span>

                                        <?php endif; ?>
                                    </div>

                                <?php endif; ?>

                                <!-- Verifica se existe ordem bump -->
                                <?php
                                if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) {
                                    include "pagamentos/fragmento_ord_bump1.php"; ?>
                                <?php
                                }
                                ?>
                                <br>
                                <span><b>Total à vista: </b></span> <span class="total" id="resultado_cartao">R$ <?= number_format(@$preco + @$frete, 2, ",", ".") ?></span>
                                <br>

                                <label for="parcela"> PARCELAMENTO DO CARTÃO: **</label>
                                <?php if (@$nome_adquirente == "redrocket" || @$checkout->adquirente == "redrocket") {
                                    if ($plano->assumir_juros) {
                                        include "pagamentos/parcelamento-sem-juros.php";
                                    } else {
                                        include "pagamentos/parcelamento.php";
                                    }
                                } else {
                                    include "pagamentos/parcelamento-chkTransp.php";
                                }
                                ?>
                                <br>
                                <input type="hidden" name="pagamento" value="credit_card">
                                <hr style="margin-bottom: 15px;" />

                                <?php if ($statusProduto == 2 && $statusPlano == 2) : ?>
                                    <!-- <button type="button" id="btn-cartao" class="btn form-control newform pagamento btn-pagamento"> <?= $produto->tipo_cobranca == 2 ? "ASSINAR AGORA" : "COMPRAR AGORA"  ?></button> -->

                                    <?php if (@$venda->status_transacao == 2 || @$venda->status_transacao == 4 || @$venda->status_transacao == 6 || @$venda->status_transacao == 7 || @$venda->status_transacao == 9) : ?>
                                        <button type="button" id="btn-cartao" class="btn form-control newform pagamento btn-pagamento" disabled>Pagamento já efetuado</button>
                                    <?php else : ?>
                                        <button type="button" id="<?= @$nome_adquirente == "mercadopago" ? "btn-cartaoMP" : "btn-cartao" ?>" class="btn form-control newform pagamento btn-pagamento"> <?= $produto->tipo_cobranca == 2 ? "ASSINAR AGORA" : "COMPRAR AGORA"  ?></button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </form>

                            <div id="pendenciasCart">
                                <p style="text-align:center" class="mb-0"><small><i class="fas fa-lock"></i> Ambiente criptografado e 100% seguro.</small></p>
                                <!--<img class="img-selo" src="<?= url("pagamentos/imgs/compra-segura.png") ?>" alt="COMPRA SEGURA - COMPRE COM TOTAL SEGURANÇA" width="50%" style="text-align:center"/>-->
                            </div>
                        </div><!-- FIM TAB CARTÃO -->

                        <!-- TAB BOLETO -->
                        <div class="tab-pane fade <?= @$show_boleto ?>" id="boleto" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="center">
                                <img src="<?= url("pagamentos/imgs/boleto.png"); ?>" alt="" width="70%">
                            </div>
                            <span>
                                Atenção: <br>
                                1 - Boleto (somente à vista) <br>
                                2 - Pagamentos com Boleto Bancário levam até 3 dias úteis para serem compensados e então terem os produtos liberados <br>
                                3 - Depois do pagamento, fique atento ao seu e-mail para receber os dados de acesso ao produto (verifique também a caixa de SPAM)
                            </span>

                            <?php if (@$checkout->cupom || @$checkout->usar_frete == 1 || @$checkout->usar_order_bump == 1) : ?>
                                <div class="row">
                                    <div class="col-md-5"><b>Valor produto:</b></div>
                                    <div class="col-md-7">
                                        <?= "R$ " . number_format(@$preco, 2, ",", ".") ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form action="" method="post" name="boleto" id="form_boleto">
                                <input type="hidden" name="pagamento" value="boleto">

                                <label class="icone-frete" style="display: none;"><i class="fas fa-truck"></i> Frete:</label><br>
                                <div class="calculo-frete"></div>

                                <div class="frete-antigo">

                                    <?php if (@$checkout->usar_frete == 1 && @$checkout->id_frete == "") : ?>
                                        <label><i class="fas fa-truck"></i> Frete:</label><br>
                                        <label for="frete">
                                            <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked>
                                            Até <?= $checkout->prazo_entrega ?> dias úteis - R$
                                            <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>

                                    <?php if (@$checkout->frete_gratis == 1 && @$checkout->id_frete == "") : ?>
                                        <br>
                                        <label for="frete_gratis">
                                            <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                                            Até <?= $checkout->prazo_entrega_gratis ?> dias úteis - R$
                                            <?= number_format(0, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>
                                </div>

                                <?php if (@$checkout->id_frete != "" && (@$checkout->dataFrete()->tipo_frete == 2 || @$checkout->dataFrete()->tipo_frete == 3)) : ?>

                                    <div class="calculo-frete-correios">
                                        <label><i class="fas fa-truck"></i> Frete:</label><br>

                                        <?php if (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 0) : ?>
                                            <label for="frete">
                                                <input class="frete-pac" type="radio" name="frete" id="frete" onclick="tipoFrete('pac')" checked>
                                            </label>
                                            <span class="frete-pac"></span>

                                            <br>

                                            <label for="frete">
                                                <input class="frete-sedex" type="radio" name="frete" id="frete" onclick="tipoFrete('sedex')" checked>
                                            </label>
                                            <span class="frete-sedex"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 1) : ?>
                                            <label for="frete">
                                                <input class="frete-sedex" type="radio" name="frete" id="frete" onclick="tipoFrete('sedex')" checked>
                                            </label>
                                            <span class="frete-sedex"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 2) : ?>

                                            <label for="frete">
                                                <input class="frete-pac" type="radio" name="frete" id="frete" onclick="tipoFrete('pac')" checked>
                                            </label>
                                            <span class="frete-pac"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 3) : ?>

                                            <label for="frete">
                                                <input class="frete-jadlog" type="radio" name="frete" id="frete" onclick="tipoFrete('jadlog')" checked>
                                            </label>
                                            <span class="frete-jadlog"></span>

                                        <?php endif; ?>
                                    </div>

                                <?php endif; ?>

                                <!-- Verifica se existe ordem bump -->
                                <?php
                                if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) :
                                    include "pagamentos/fragmento_ord_bump1.php";
                                endif;
                                ?>

                                <span><b>Total: </b></span> <span class="total" id="resultado_boleto">R$ <?= number_format($preco, 2, ",", ".") ?></span>
                                <br><br>

                                <?php if ($statusProduto == 2 && $statusPlano == 2) : ?>
                                    <!-- <button type="submit" id="btn-boleto" class="btn form-control nform pagamento btn-pagamento">COMPRAR AGORA</button> -->

                                    <?php if (@$venda->status_transacao == 2 || @$venda->status_transacao == 4 || @$venda->status_transacao == 6 || @$venda->status_transacao == 7 || @$venda->status_transacao == 9) : ?>
                                        <button type="button" id="btn-boleto" class="btn form-control newform pagamento btn-pagamento" disabled>Pagamento já efetuado</button>
                                    <?php else : ?>
                                        <button type="submit" id="btn-boleto" class="btn form-control newform pagamento btn-pagamento">Comprar Agora</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </form>

                            <div id="pendenciasBol" style="text-align: center;">
                                <p><small><i class="fas fa-lock"></i> Ambiente criptografado e 100% seguro.</small></p>
                            </div>
                        </div>
                        <!-- FIM TAB BOLETO -->

                        <!-- TAB PIX -->
                        <div class="tab-pane fade <?= @$show_pix ?>" id="pix-tab" role="tabpanel" aria-labelledby="nav-pix-tab">
                            <br>
                            <div class="center">
                                <img src="<?= url("pagamentos/imgs/pix.png"); ?>" alt="" width="50%">
                            </div>
                            <br>

                            <span style="font-size: 14px">
                                O Pix é a forma mais vantajosa para pagar à vista. O pagamento cai na hora e a sua compra é liberada mais rápido!
                            </span>

                            <?php if (@$checkout->cupom || @$checkout->usar_frete == 1 || @$checkout->usar_order_bump == 1) : ?>
                                <div class="row">
                                    <div class="col-md-5"><b>Valor produto:</b></div>
                                    <div class="col-md-7">
                                        <?= "R$ " . number_format(@$preco, 2, ",", ".") ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form action="" method="post" name="pix" id="form_pix">
                                <input type="hidden" name="pagamento" value="pix">

                                <label class="icone-frete" style="display: none;"><i class="fas fa-truck"></i> Frete:</label><br>
                                <div class="calculo-frete"></div>

                                <div class="frete-antigo">
                                    <?php if (@$checkout->usar_frete == 1 && @$checkout->id_frete == "") : ?>
                                        <label><i class="fas fa-truck"></i> Frete:</label><br>
                                        <label for="frete">
                                            <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked>
                                            Até <?= $checkout->prazo_entrega ?> dias úteis - R$
                                            <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>

                                    <?php if (@$checkout->frete_gratis == 1 && @$checkout->id_frete == "") : ?>
                                        <br>
                                        <label for="frete_gratis">
                                            <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                                            Até <?= $checkout->prazo_entrega_gratis ?> dias úteis - R$
                                            <?= number_format(0, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>
                                </div>

                                <?php if (@$checkout->id_frete != "" && (@$checkout->dataFrete()->tipo_frete == 2 || @$checkout->dataFrete()->tipo_frete == 3)) : ?>
                                    <div class="calculo-frete-correios">
                                        <label><i class="fas fa-truck"></i> Frete:</label><br>

                                        <?php if (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 0) : ?>
                                            <label for="frete">
                                                <input class="frete-pac" type="radio" name="frete" id="frete" onclick="tipoFrete('pac')" checked>
                                            </label>
                                            <span class="frete-pac"></span>

                                            <br>

                                            <label for="frete">
                                                <input class="frete-sedex" type="radio" name="frete" id="frete" onclick="tipoFrete('sedex')" checked>
                                            </label>
                                            <span class="frete-sedex"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 1) : ?>
                                            <label for="frete">
                                                <input class="frete-sedex" type="radio" name="frete" id="frete" onclick="tipoFrete('sedex')" checked>
                                            </label>
                                            <span class="frete-sedex"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 2 && @$checkout->dataFrete()->correios_entrega == 2) : ?>

                                            <label for="frete">
                                                <input class="frete-pac" type="radio" name="frete" id="frete" onclick="tipoFrete('pac')" checked>
                                            </label>
                                            <span class="frete-pac"></span>

                                        <?php elseif (@$checkout->dataFrete()->tipo_frete == 3) : ?>

                                            <label for="frete">
                                                <input class="frete-jadlog" type="radio" name="frete" id="frete" onclick="tipoFrete('jadlog')" checked>
                                            </label>
                                            <span class="frete-jadlog"></span>

                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Verifica se existe ordem bump -->
                                <?php
                                if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) :
                                    include "pagamentos/fragmento_ord_bump1.php";
                                endif;
                                ?>

                                <span><b>Total: </b></span> <span class="total" id="resultado_pix">R$ <?= number_format($preco, 2, ",", ".") ?></span>
                                <br><br>

                                <?php if ($statusProduto == 2 && $statusPlano == 2) : ?>
                                    <!-- <button type="submit" id="btn-pix" class="btn form-control newform pagamento btn-pagamento">Visualizar QR Code</button> -->

                                    <?php if (@$venda->status_transacao == 2 || @$venda->status_transacao == 4 || @$venda->status_transacao == 6 || @$venda->status_transacao == 7 || @$venda->status_transacao == 9) : ?>
                                        <button type="button" id="btn-pix" class="btn form-control newform pagamento btn-pagamento" disabled>Pagamento já efetuado</button>
                                    <?php else : ?>
                                        <button type="submit" id="btn-pix" class="btn form-control newform pagamento btn-pagamento">Comprar Agora</button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <p id="servico-pix">Verificando Serviço Pix...</p>

                                <div id="aviso_pix" style="font-size: 14px">
                                    Se o QR Code não aparecer quando você clicar em Visualizar QR Code, verifique seus dados e tente novamente ou escolhar outra forma de pagamento.
                                </div>

                                <div class="instrucao_pix" style="font-size: 14px">
                                    Instruções: <br>
                                    1. Abra o app do seu banco ou instituição financeira e entre no ambiente Pix <br>
                                    2. Escolha a opção Pagar com QR Code e escaneie o código abaixo <br>
                                    3. Confirme as informações e finalize a compra <br><br>

                                    O QR code expira em 3 minutos <br>
                                    Caso expire, <a href="" id="link-pix">clique aqui</a>
                                </div>

                                <!-- <div id="qrcode" style="margin:15px auto;"></div>

                                <div class="instrucao_pix row" style="font-size: 14px">
                                    <div class="col-md-6"><input type="text" id="inp-copiar" class="form-control newform form-control-sm" readonly></div>
                                    <div class="col-md-6"><button type="button" class="form-control btn btn-info btn-sm" id="copiar">COPIAR CÓDIGO</button></div>
                                </div> 
                                <br>
                                <button id="btn-pix-confirmar" class="btn form-control pagamento btn-pagamento">Finalizar Compra</button> -->
                            </form>
                        </div>
                        <!-- FIM TAB PIX -->
                    </div>
                </div>
                <!-- FIM FORMAS PAGAMENTO -->
            </div>
            <!-- FIM LADO DIREITO -->
        </div>
        <!-- FIM CORPO CENTRAL -->

        <!-- DADOS REDROCKET -->

        <!-- FIM DADOS REDROCKET -->
    </div>
    <!-- FIM CONTAINER PRINCIPAL -->

    <div id="pix-page" style="display: none; padding: 40px; background: #1E1E1E; min-height: 100vh;">
    <div class="text-center mb-4">
        <h2 style="color: #28a745; font-weight: bold;">Compra com Pix</h2>
        <p style="font-size: 15px; color: #fff;">Siga as instruções para efetuar o pagamento via Pix</p>
    </div>

    <div class="row justify-content-center align-items-start" style="max-width: 1000px; margin: auto;">
        <!-- LADO ESQUERDO - Instruções -->
        <div class="col-md-6 mb-4">
            <h3 style="font-weight: bold;">Falta pouco! Siga os passos abaixo para realizar o pagamento</h3>
            <ol style="font-size: 15px; line-height: 1.6;">
                <li><strong>Pagar com QR code:</strong> aponte a câmera do seu celular para o código QR Code</li>
                <li><strong>Finalize o pagamento</strong> após verificar as informações</li>
                <li><strong>Ou use Pix Copia e Cola:</strong> copie o código abaixo</li>
            </ol>
            <input type="text" class="form-control mb-3" id="inp-copiar" readonly style="background: #000000;">
            <button class="btn btn-warning btn-lg" onclick="copiarPix()" style="font-weight: 500;">Copiar Código Pix</button>
        </div>

        <!-- LADO DIREITO - QR Code e Ações -->
        <div class="col-md-6 text-center p-4 rounded" style="box-shadow: 0 0 15px rgba(0,0,0,0.05); background: #fff; color: black">
            <p style="font-size: 14px;">Sua oferta encerra em <span id="contador_pix" style="color: red; font-weight: bold;">05:00</span></p>
            <p class="mb-2"><strong>Aponte a câmera aqui</strong></p>
            <div id="qrcode" style="margin: 10px auto; width: 220px; height: 220px;"></div>
            <p class="mt-3;"><strong>Valor do Pix: R$ <?= number_format($preco, 2, ",", ".") ?></strong></p>
            <button id="btn-pix-confirmar" class="btn btn-success btn-lg mt-3" style="display: none;">Já fiz o Pagamento</button>
        </div>
    </div>
</div>


<!-- JS necessário -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>

<<script>
    function iniciarContador(segundos) {
        const el = document.getElementById("contador_pix");
        let restante = segundos;
        const intervalo = setInterval(() => {
            const min = String(Math.floor(restante / 60)).padStart(2, "0");
            const seg = String(restante % 60).padStart(2, "0");
            el.textContent = `${min}:${seg}`;
            restante--;
            if (restante < 0) clearInterval(intervalo);
        }, 1000);
    }

    function copiarPix() {
        const input = document.getElementById("inp-copiar");
        if (input && input.value) {
            navigator.clipboard.writeText(input.value).then(() => {
                alert("Código Pix copiado!");
            });
        } else {
            alert("Código Pix ainda não carregado.");
        }
    }
</script>

    <div class="container-75">
        <div class="row">
            <div class="col-md-12">
                <div class="rodape">
                    <div class="row">
                        <div class="col-md-4 mt-3">
                            <span style="font-size: 14px">
                                <b>Nome do Produto:</b> <?= @$produto->nome ?><br />
                                <b>Suporte:</b> <?= @$email_suporte ?><br />
                                <?php if (@$produto->whatsapp_checkout) : ?>
                                    <i class="fab fa-whatsapp" style="color: green"></i> <b>Whatsapp:</b>
                                    <?= @$produto->whatsapp_checkout; ?>
                                <?php endif ?>
                            </span>
                        </div>
                        <div class="col-md-4 mb-2" style="text-align: center;">
    
                            <img class="img-selo" style="width: 100%; height: auto;" src="<?= url("pagamentos/imgs/site-seguro.png") ?>" alt="COMPRA SEGURA - COMPRE COM TOTAL SEGURANÇA" />
    
                        </div>
                        <div class="col-md-4">
                            <div class="seguro-rodape">
                                <i class="fas fa-lock mr-2" style="color:#FFF"></i> COMPRA 100% SEGURA
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="container-75">
            <div class="center pt-4">
                <img height="70" src="<?= url("pagamentos/imgs/logo.png") ?>" alt="COMPRA SEGURA - COMPRE COM TOTAL SEGURANÇA" />
            </div>
        </div>
    </div> -->

    <div class="rodape2">
        <div class="container-75">
            <br>
            <small>
                <br />
                <span>Esta compra será processada pela WinPay © 2025 - Todos os direitos reservados.</span>
                <hr />
                <span>
                    ** pode haver incidencia de taxa de juros <br>
                    Ao continuar nesta compra, você concorda com os <a href="<?= url("termos-uso"); ?>">Termos de Uso</a> e
                    <a href="<?= url("politica-privacidade"); ?>">Políticas de Privacidade</a> <br>
                </span>
            </small>

            <small style="font-size:10px">
                <?php
                if (@$_COOKIE[$produto->id]) {
                    var_dump($_COOKIE[$produto->id]);
                    parse_str($_COOKIE[$produto->id], $output);
                    echo "ref.: {$output["codigo"]}<br>";
                }
                ?>
            </small>

            <?php if (@$referenciaAf || @$referenciaPd) : ?>
                <script>
                    function referencia() {

                        alert("<?= @$referenciaPd ?>" + "\n" + "<?= @$referenciaAf ?>");
                    }
                </script>

                <small onclick="referencia()">Ref.: pixel</small>
            <?php endif ?>
        </div>
    </div>

    <!-- NOTIFICAÇÕES -->
    <script>
        var dados = [];
        var contar = [];
        var nomes = [];
    </script>

    <?php
    $frases = explode(",", @$notificacoes->frases);

    if (@$frases) {
        $i = 0;
        foreach ($frases as $frases) {
            $posicao[$frases] = $frases;
            $i++;
        }
    }

    $i = 1;
    if ($frasesCount) {
        foreach ($frasesCount as $contar) {
            echo "<script>contar[$i] = $contar;</script>";
            $i++;
        }
    }

    $frase[1] = [(@$posicao[1] ? "true" : "false"), ((@$frasesCount[1] >= @$notificacoes->valor1) ? "true" : "false"), @$frasesCount[1] . " PESSOAS ESTÃO COMPRANDO " . strtoupper($produto->nome) . " NESSE MOMENTO."];
    $frase[2] = [(@$posicao[2] ? "true" : "false"), ((@$frasesCount[2] >= @$notificacoes->valor2) ? true : false), @$frasesCount[2] . " PESSOAS COMPRARAM " . strtoupper($produto->nome) . " ESSA SEMANA."];
    $frase[3] = [(@$posicao[3] ? "true" : "false"), ((@$frasesCount[3] >= @$notificacoes->valor3) ? true : false), @$frasesCount[3] . " PESSOAS COMPRARAM " . strtoupper($produto->nome) . " NOS ÚLTIMOS 30 MINUTOS."];
    $frase[4] = [(@$posicao[4] ? "true" : "false"), ((@$frasesCount[4] >= @$notificacoes->valor4) ? true : false), @$frasesCount[4] . " PESSOAS COMPRARAM " . strtoupper($produto->nome) . " HOJE."];
    $frase[5] = [(@$posicao[5] ? "true" : "false"), ((@$frasesCount[5] >= @$notificacoes->valor5) ? true : false), @$frasesCount[5] . " PESSOAS COMPRARAM " . strtoupper($produto->nome) . " NA ÚLTIMA HORA."];
    $frase[6] = [(@$nome[1]->nome ? "true" : "false"), "false", strtoupper(@$nomes[1]->nome) . " COMPROU " . strtoupper($produto->nome)];
    $frase[7] = [(@$nome[2]->nome ? "true" : "false"), "false", strtoupper(@$nomes[2]->nome) . " ACABOU DE COMPRAR " . strtoupper($produto->nome)];

    $i = 1;
    echo "<script>";
    foreach ($frase as $frase) {
        echo "dados[$i] = [$frase[0], '$frase[1]', '$frase[2]'];";
        $i++;
    }

    $nome1 = @$nomes[1]->nome ? 1 : 0;
    echo "nomes[1] = $nome1;";

    $nome2 = @$nomes[2]->nome ? 1 : 0;
    echo "nomes[2] = $nome2;";
    echo "</script>";
    ?>

    <div class="container-fluid corpo-toast">
        <div aria-live="polite" aria-atomic="true" class="position-relativ">
            <div class="position-fixed" id="toast">
                <div class="toast" data-autohide="false">
                    <div class="toast-body" style="background: #cdffe3; border: 1px solid #28a745">
                        <p id="frase-toast">
                            <!-- MENSAGEM TOAST -->
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FIM NOTIFICAÇÕES -->

    <!-- SCRIPTS JAVASCRIPT -->
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="<?= url("pagamentos/js/validacao.js?" . time()); ?>"></script>
    <script type="text/javascript" src="<?= url("pagamentos/js/qrcode.min.js?" . time()); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?= url("pagamentos/js/qrcode.js?" . time()); ?>"></script>
    <script>
        const ADQUIRENTE_ATUAL = '<?= strtolower((string)@$nome_adquirente); ?>';
        const PAGARME_PUBLIC_KEY = 'pk_xl3Qe47ilU9aPX6v';
        const PAGARME_SCRIPT_URL = 'https://assets.pagar.me/pagarme-js/4.13/pagarme.min.js';
        let pagarmeLoaderPromise;

        function ensurePagarmeLoaded() {
            if (typeof pagarme !== 'undefined') {
                return Promise.resolve();
            }

            if (!pagarmeLoaderPromise) {
                pagarmeLoaderPromise = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = PAGARME_SCRIPT_URL;
                    script.async = true;
                    script.onload = () => resolve();
                    script.onerror = () => {
                        pagarmeLoaderPromise = null;
                        reject(new Error('Não foi possível carregar a biblioteca PagarMe'));
                    };
                    document.head.appendChild(script);
                });
            }

            return pagarmeLoaderPromise;
        }

        if (ADQUIRENTE_ATUAL === 'pagarme') {
            ensurePagarmeLoaded().catch(error => {
                console.error('Falha ao pré-carregar PagarMe:', error);
            });
        }

        async function gerarPagarmeToken(e){
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }

            if (ADQUIRENTE_ATUAL !== 'pagarme') {
                if (typeof dadosCartao === 'function') {
                    dadosCartao();
                }
                return;
            }

            const numero = document.getElementById('numcart').value.replace(/\D/g,'');
            const nome   = document.getElementById('titular').value;
            const cvc    = document.getElementById('cvv').value;
            const mes    = document.getElementById('mes-val').value;        // "01".."12"
            let   ano    = document.getElementById('ano-val').value;        // "24" ou "2024"
            if (ano.length === 2) ano = '20' + ano;                          // normaliza para 4 dígitos

            const mesNormalizado = mes.length === 1 ? `0${mes}` : mes.padStart ? mes.padStart(2, '0') : mes;
            const expiration = `${mesNormalizado}${ano.slice(-2)}`;

            const cardData = {
                card_number: numero,
                card_holder_name: nome,
                card_expiration_date: expiration,
                card_cvv: cvc
            };

            try {
                await ensurePagarmeLoaded();

                if (typeof pagarme === 'undefined') {
                    throw new Error('Biblioteca PagarMe não carregada');
                }

                const client = await pagarme.client.connect({ encryption_key: PAGARME_PUBLIC_KEY });
                const cardHash = await client.security.encrypt(cardData);

                document.getElementById('MPHiddenInputToken').value = cardHash;

                if (cardHash) {
                    dadosCartao();
                }
            } catch (error) {
                console.error('PagarMe encryption error:', error);
                const message = error?.response?.errors?.[0]?.message || error?.message || 'Não foi possível gerar o token do cartão.';
                alert(message);
            }
        }

        //}
        /////////// FIM MERCADO PAGO /////////////

        total_order_bump = 0;
        // TOAST DE NOTIFICAÇÃO

        <?php if (@$notificacoes->ativo == 1) : ?>
            var i = 1;
            var j = 1;
            var tempo1 = 5,
                tempo2 = 10,
                tempo;

            mostrar = function(i) {
                $("#frase-toast").html(dados[i][2]);
                $('.toast').toast('show');
            }

            esconder = function() {
                $('.toast').toast('hide');
            }

            var intervalo = setInterval(() => {
                // console.log(i);

                if (tempo1 == i) {
                    // console.log(tempo1, tempo2, j);

                    if (dados[j][0] && dados[j][1])
                        mostrar(j);

                    tempo = tempo2;
                    tempo1 += 15;
                    tempo2 = tempo1 + 5;

                    j++;
                }

                if (tempo == i) {
                    esconder();

                    if (j > 7) {
                        clearInterval(intervalo);
                    }
                }
                i++;
            }, 1000);
        <?php endif; ?>
        // FIM TOAST DE NOTIFICAÇÃO

        //valida telefone
        $("#telefone").focusout(function() {
            var telefone = $("#telefone").val();
            validarTelefone(telefone);
        });

        $("#telefone").on("paste", function() {
            var telefone = $("#telefone").val();
            validarTelefone(telefone);
        });

        function validarTelefone(telefone) {
            // Remover caracteres especiais: + - ( ) , . -
            const numeroLimpo = telefone.replace(/\D/g, '');
            console.log(telefone, numeroLimpo)

            // Verificar se o número contém apenas dígitos
            if (/^\d+$/.test(numeroLimpo)) {
                // Outras verificações podem ser adicionadas aqui, se necessário
                $("#telefone").val(numeroLimpo);
                console.log('limpo')
                return true;
            } else {
                return false;
            }
        }

        // VALIDAÇÕES DE EMAIL
        $("#email").blur(function() {
            var email = $("#email").val();
            validarEmail(email);
        });

        $("#email").on("paste", function() {
            var email = $("#email").val();
            validarEmail(email);
        });

        // LIMPAR ESPAÇOS EMAIL
        $("#email").blur(() => {
            var email = $("#email").val();
            var email1 = email.replace(/\s/g, '');
            $("#email").attr("value", email1);
            // console.log(email1);
        });
        // FIM LIMPAR ESPAÇOS EMAIL

        function validarEmail(email) {
            if (email) {
                $.ajax({
                    url: "<?= $router->route("validacao.email") ?>",
                    data: {
                        email: email
                    },
                    type: "POST",
                    success: function(callback) {
                        if (callback == "Email inválido") {
                            $("#msg-email").css("color", "red").html("Email inválido! Favor corrigí-lo ou deixe em branco");
                            $("#email").focus();
                        } else {
                            $("#msg-email").html("");
                        }
                    }
                });
            } else {
                $("#msg-email").html("");
            }
        }
        // FIM VALIDAÇÕES DE EMAIL

        //valida nome cartao
        $("#titular").focusout(function() {
            var telefone = $("#titular").val();
            validarNomeCartao(telefone);
        });

        $("#titular").on("paste", function() {
            var telefone = $("#titular").val();
            validarNomeCartao(telefone);
        });

        function validarNomeCartao(texto) {
            // Remover caracteres especiais: + - ( ) , . -
            var textoSemAcentos = texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            // Remover números
            var textoSemNumeros = textoSemAcentos.replace(/\d/g, '');

            $("#titular").val(textoSemNumeros);
        }

        // BUSCA COMPRADOR
        function buscaComprador(info) {
            $.ajax({
                url: "<?= $router->route("appusuarios.buscaComprador") ?>",
                data: {
                    info: info
                },
                type: "POST",
                success: function(callback) {
                    function data(callback) {
                        try {
                            return JSON.parse(callback);
                        } catch (error) {
                            return false;
                        }
                    }

                    var data = data(callback);
                    // console.log(data);
                    if (data.msg != "Nenhum usuario encontrado") {
                        var ddd = data.telefone.substring(0, 2);
                        var telefone = data.telefone.substring(2, 12);
                        $("#nome").val(data.nome);
                        $("#email").val(data.email);
                        $("#cpf").val(data.cpf);
                        $("#cep").val(data.cep);
                        $("#telefone").val(data.telefone);
                        $("#numero").val(data.numero);

                        cep($("#cep").val());
                    }
                }
            });
        }
        // FIM BUSCA COMPRADOR

        // VALIDAR CPF
        $("#cpf").blur(() => {
            var cont = $("#cpf").val().length;

            if (cont <= 11) {
                var verificar = validarCPF($("#cpf").val(), "#cpf", "#msg-cpf");
            } else {
                var verificar = validarCNPJ($("#cpf").val(), "#cpf", "#msg-cpf");
            }
        });
        // FIM VALIDAR CPF

        // aTUALIZA DADOS DE PARCELAMENTO
        function parcelamento(frete = null) {
            <?php if (@$nome_adquirente == "redrocket") {
                if ($plano->assumir_juros) {
                    $url = url("pagamentos/parcelamento-sem-juros.php");
                } else {
                    $url = url("pagamentos/parcelamento.php");
                }
            } else {
                $url = url("pagamentos/parcelamento-chkTransp.php");
            }
            ?>
            <?php
            if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) { ?>
                atualiza_preco_orderbump(totalprod, 1);
            <?php }
            ?>

            // Se tiver cupom
            if (cupom_total == true) {
                $("#valor_desconto").attr("value", valor);
                cupom_desconto = parseFloat(valor);
            } else {
                cupom_desconto = 0;
            }

            var frete = frete ? frete : parseFloat($("input[name=frete]:checked").val());
            frete = frete ? frete : 0;
            var capital = parseFloat($("#preco").val());
            var taxa_juros = parseFloat("<?= $taxa_juros ?>");
            var valor_cupom = parseFloat($("#valor_desconto").val());
            var parcelas = "<?= $plano->parcelas ?>";
            const checkout = <?= @$checkout->id ?>;
            $("#capital").val(frete + capital + total_order_bump);

            var totalCartao = (capital + frete + total_order_bump - cupom_desconto).toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            });

            $("#resultado_cartao").html(totalCartao);

            frete = frete > 0 ? frete : 0;
            var preco = frete + capital + total_order_bump;
            // console.log("capital", capital, frete, total_order_bump);

            $.ajax({
                url: "<?= $url ?>",
                type: "POST",
                data: {
                    preco: preco,
                    taxa_juros: taxa_juros,
                    valor_cupom: valor_cupom,
                    parcelas: parcelas,
                    checkout: checkout
                },
                success: function(callback) {
                    // console.log(callback);
                    $("#parcela").html(callback);
                }
            });
        }
        // FIM ATUALIZA DADOS DE PARCELAMENTO


        $("#form_cartao .calculo-frete").change(() => {
            parcelamento();
            // atualiza_preco_orderbump(totalprod);
        });

        $("#form_boleto .calculo-frete").change(() => {
            atualizaBoleto();
        });

        $("#form_pix .calculo-frete").change(() => {
            atualizaPix();

        });

        // CASO FRETE DEFINIDO SEJA CORREIOS E JADLOG
        $("#form_cartao .calculo-frete-correios").change(() => {
            parcelamento();
        });

        $("#form_boleto .calculo-frete-correios").change(() => {
            // console.log("alterou boleto");

            atualizaBoleto();
        });

        $("#form_pix .calculo-frete-correios").change(() => {
            atualizaPix();

        });
        // FIM FRETE CORREIOS E JADLOG
        $("#form_cartao input[name=frete]").change(() => {
            parcelamento();
        });


        $("#form_pix input[name=frete").change(() => {
            atualizaPix();
        });


        $("#form_boleto input[name=frete]").change(() => {
            // console.log("alterou boleto");
            atualizaBoleto();
        });



        function atualizaBoleto() {
            // Pega o valor do cupom de desconto caso estiver ativo
            if (cupom_total == true) {
                $("#valor_desconto").attr("value", valor);
                cupom_desconto = parseFloat(valor);
                // console.log("cupom desconto", cupom_desconto);
                // console.log("desconto", valor);
                $("cupom").blur();

            } else {
                cupom_desconto = 0;
            }

            <?php
            if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) { ?>
                atualiza_preco_orderbump(totalprod, 3);
            <?php }
            ?>

            var frete = frete ? frete : parseFloat($("#form_boleto input[name=frete]:checked").val());
            frete = frete ? frete : 0;
            var capital = parseFloat($("#preco").val());
            // console.log("capital", capital, frete);

            var total = (capital + frete + total_order_bump - cupom_desconto).toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            });
            $("#resultado_boleto").html(total);
        }


        function atualizaPix() {
            // Pega o valor do cupom de desconto caso estiver ativo
            if (cupom_total == true) {
                $("#valor_desconto").attr("value", valor);
                cupom_desconto = parseFloat(valor);
                // console.log("cupom desconto", cupom_desconto);
                // console.log("desconto", valor);
                $("cupom").blur();
            } else {
                cupom_desconto = 0;
            }

            <?php
            if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) { ?>
                atualiza_preco_orderbump(totalprod, 2);
            <?php }
            ?>

            var frete = frete ? frete : parseFloat($("#form_pix input[name=frete]:checked").val());
            frete = frete ? frete : 0;
            var capital = parseFloat($("#preco").val());
            // console.log("capital", capital, frete, total);
            var total = (capital + frete + total_order_bump - cupom_desconto).toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            });
            $("#resultado_pix").html(total);
        }
        // FIM ALTERAR VALOR AO TROCAR DE CEP

        // BUSCA CEP
        $("#cep").focusout(function() {
            cep($(this).val());
        });

        function validarEndereco() {
            var cep = $("#cep").val();
            var rua = $("#rua").val();
            var bairro = $("#bairro").val();
            var cidade = $("#cidade").val();

            if (!cep || cep.length != 8) {
                $("#cep").addClass('input-error');
                // $("#cep").focus();
            } else {
                $("#cep").removeClass('input-error');
                return true;
            }

            if (!rua) {
                $("#rua").addClass('input-error');
                // $("#rua").focus();
            } else {
                $("#rua").removeClass('input-error');
                return true;
            }

            if (!bairro) {
                $("#bairro").addClass('input-error');
                // $("#bairro").focus();
            } else {
                $("#bairro").removeClass('input-error');
                return true;
            }

            if (!cidade) {
                $("#cidade").addClass('input-error');
                // $("#cidade").focus();
            } else {
                $("#cidade").removeClass('input-error');
                return true;
            }
        }


        function cep(cep) {
            $.ajax({
                url: 'https://api.pagar.me/1/zipcodes/' + cep,

                dataType: 'json',
                success: function(resposta) {
                    $("#rua").val(resposta.street);
                    $("#bairro").val(resposta.neighborhood);
                    $("#cidade").val(resposta.city);
                    $("#estado").val(resposta.state);
                    $("#msg-cep").hide();
                    frete();
                },
                error: function(err) {
                    if (err.status == 404) {
                        // console.log("CEP NÃO ENCONTRADO", err.status);
                        // alert("CEP NÃO ENCONTRADO");
                        $("#msg-cep").html("CEP inválido");
                        $("#msg-cep").css({
                            "color": "red", "font-size": "12px"
                        });
                    }
                }
            });
        }
        // FIM BUSCA CEP

        $(".calculo-frete-correios").hide();
        $(".frete-antigo").hide();

        function tipoFrete(tipo) {
            $("#tipo_frete").attr("value", tipo);
        }

        function frete() {
            var frete_antigo = <?= @$checkout->id_frete == "" ? 1 : 0 ?>;
            if (frete_antigo == 1) {
                parcelamento();
                atualizaBoleto();
                atualizaPix();
                $(".frete-antigo").show();

            }
            // FRETE REGIOES
            var estado = $("#estado").val();
            var tipo_frete = <?= @$checkout->id_frete != "" ? @$checkout->dataFrete()->tipo_frete : "null"; ?>;

            if (tipo_frete == 2 || tipo_frete == 3) {
                var cep_destino = $("#cep").val();
                var preco_produto = <?= @$preco; ?>;

                /* Informações Correios
                 Tipo Entrega (0 = Todos / 1 = Sedex / 2 = PAC) */
                var tipo_entrega = <?= @$checkout->dataFrete()->correios_entrega != "" ? @$checkout->dataFrete()->correios_entrega : 0; ?>;
                //Formato (0 = Caixa | Pacote / 1 = Envelope) 
                var formato = <?= @$checkout->dataFrete()->correios_formato != "" ? @$checkout->dataFrete()->correios_formato : 0; ?>;
                // Fim Informações Correios //

                var cep_origem = <?= @$checkout->dataFrete()->cep_origem != "" ? @$checkout->dataFrete()->cep_origem : 'null'; ?>;
                var altura_mercadoria = <?= @$checkout->dataFrete()->altura_mercadoria != "" ? @$checkout->dataFrete()->altura_mercadoria : 'null'; ?>;
                var largura_mercadoria = <?= @$checkout->dataFrete()->largura_mercadoria != "" ? @$checkout->dataFrete()->largura_mercadoria : 'null'; ?>;
                var comprimento_mercadoria = <?= @$checkout->dataFrete()->comprimento_mercadoria != "" ? @$checkout->dataFrete()->comprimento_mercadoria : 'null'; ?>;
                var peso_mercadoria = <?= @$checkout->dataFrete()->peso_mercadoria != "" ? @$checkout->dataFrete()->peso_mercadoria : 'null'; ?>;

                $.ajax({
                    url: "<?= $router->route("encomendas.frete") ?>",
                    type: "POST",
                    data: {
                        preco_produto: preco_produto,
                        tipo_frete: tipo_frete,
                        cep_destino: cep_destino,
                        tipo_entrega: tipo_entrega,
                        formato: formato,
                        cep_origem: cep_origem,
                        altura_mercadoria: altura_mercadoria,
                        largura_mercadoria: largura_mercadoria,
                        comprimento_mercadoria: comprimento_mercadoria,
                        peso_mercadoria: peso_mercadoria,
                    },
                    beforeSend: function() {
                        $(".ajax_load_box_title").html("Calculando Frete");
                        load('open');
                    },
                    success: function(resposta) {
                        // console.log(resposta);
                        resposta = JSON.parse(resposta);
                        // console.log(resposta.erro);

                        if (tipo_frete == 2 && resposta.erro == 0) {

                            if (tipo_entrega == 0) {
                                preco_pac = resposta.pac.toLocaleString("pt-BR", {
                                    style: "currency",
                                    currency: "BRL"
                                });

                                mensagem_pac = "até " + resposta.pac_prazo + " Dias - " + preco_pac + " via PAC";
                                $(".frete-pac").html(mensagem_pac);
                                $(".frete-pac").attr("value", resposta.pac);


                                preco_sedex = resposta.sedex.toLocaleString("pt-BR", {
                                    style: "currency",
                                    currency: "BRL"
                                });

                                mensagem = "até " + resposta.sedex_prazo + " Dias - " + preco_sedex + " via SEDEX";
                                $(".frete-sedex").html(mensagem);
                                $(".frete-sedex").attr("value", resposta.sedex);

                                $(".calculo-frete-correios").show();
                                tipoFrete("sedex");
                            }

                            if (tipo_entrega == 1) {
                                preco_sedex = resposta.sedex.toLocaleString("pt-BR", {
                                    style: "currency",
                                    currency: "BRL"
                                });

                                mensagem = "até " + resposta.sedex_prazo + " Dias - " + preco_sedex + " via SEDEX";
                                $(".frete-sedex").html(mensagem);
                                $(".frete-sedex").attr("value", resposta.sedex);

                                $(".calculo-frete-correios").show();
                                tipoFrete("sedex");
                            }

                            if (tipo_entrega == 2) {
                                preco_pac = resposta.pac.toLocaleString("pt-BR", {
                                    style: "currency",
                                    currency: "BRL"
                                });

                                mensagem_pac = "até " + resposta.pac_prazo + " Dias - " + preco_pac + " via PAC";
                                $(".frete-pac").html(mensagem_pac);
                                $(".frete-pac").attr("value", resposta.pac);

                                $(".calculo-frete-correios").show();
                                tipoFrete("pac");
                            }

                        } else if (tipo_frete == 3) {
                            preco_jadlog = resposta.jadlog.toLocaleString("pt-BR", {
                                style: "currency",
                                currency: "BRL"
                            });

                            mensagem = "até " + resposta.jadlog_prazo + " Dias - " + preco_jadlog + " via JadLog";
                            $(".frete-jadlog").html(mensagem);
                            $(".frete-jadlog").attr("value", resposta.jadlog);

                            $(".calculo-frete-correios").show();
                            tipoFrete("jadlog");
                        } else {

                            var frete_padrao = <?= @$checkout->dataFrete()->frete_padrao != "" ? @$checkout->dataFrete()->frete_padrao : 0 ?>;
                            $(".frete-pac").attr("value", frete_padrao);

                            var prazo_entrega = <?= @$checkout->dataFrete()->prazo_entrega != "" ? @$checkout->dataFrete()->prazo_entrega : 0 ?>;
                            frete_padrao = frete_padrao.toLocaleString("pt-BR", {
                                style: "currency",
                                currency: "BRL"
                            });

                            mensagem_prazo = "até " + prazo_entrega + " Dias - " + frete_padrao;
                            $(".frete-pac").html(mensagem_prazo);

                            $(".frete-sedex").hide();
                            $(".calculo-frete-correios").show();

                        }

                        parcelamento();
                        atualizaBoleto();
                        atualizaPix();
                        load('close');
                    }
                });
            }

            if (tipo_frete == 1 || tipo_frete == 4) {
                var prazo_entrega = <?= @$checkout->dataFrete()->prazo_entrega != "" ? @$checkout->dataFrete()->prazo_entrega : 0; ?>;
                var prazo_entrega_gratis = <?= @$checkout->dataFrete()->prazo_entrega_gratis != "" ? @$checkout->dataFrete()->prazo_entrega_gratis : 0; ?>;
                var frete_padrao = <?= @$checkout->dataFrete()->frete_padrao != "" ? @$checkout->dataFrete()->frete_padrao : 'null'; ?>;
                var frete_gratis = <?= (@$checkout->dataFrete()->frete_gratis != "" || @$checkout->dataFrete()->frete_gratis != "") ? @$checkout->dataFrete()->frete_gratis : 0; ?>;
                var reg_norte = <?= @$checkout->dataFrete()->regiao_norte != "" ? @$checkout->dataFrete()->regiao_norte : 0; ?>;
                var prazo_reg_norte = <?= @$checkout->dataFrete()->prazo_reg_norte != "" ? @$checkout->dataFrete()->prazo_reg_norte : 0; ?>;

                var reg_nordeste = <?= @$checkout->dataFrete()->regiao_nordeste != "" ? @$checkout->dataFrete()->regiao_nordeste : 0; ?>;
                var prazo_reg_nordeste = <?= @$checkout->dataFrete()->prazo_reg_nordeste != "" ? @$checkout->dataFrete()->prazo_reg_nordeste : 0; ?>;

                var reg_centro = <?= @$checkout->dataFrete()->regiao_centro != "" ? @$checkout->dataFrete()->regiao_centro : 0; ?>;
                var prazo_reg_centro = <?= @$checkout->dataFrete()->prazo_reg_centro != "" ? @$checkout->dataFrete()->prazo_reg_centro : 0; ?>;

                var reg_sudeste = <?= @$checkout->dataFrete()->regiao_sudeste != "" ? @$checkout->dataFrete()->regiao_sudeste : 0; ?>;
                var prazo_reg_sudeste = <?= @$checkout->dataFrete()->prazo_reg_sudeste != "" ? @$checkout->dataFrete()->prazo_reg_sudeste : 0; ?>;

                var reg_sul = <?= @$checkout->dataFrete()->regiao_sul != "" ? @$checkout->dataFrete()->regiao_sul : 0; ?>;
                var prazo_reg_sul = <?= @$checkout->dataFrete()->prazo_reg_sul != "" ? @$checkout->dataFrete()->prazo_reg_sul : 0; ?>;

                $.ajax({
                    url: "<?= url('pagamentos/fragments/frete-regiao.php') ?>",
                    type: "POST",
                    data: {
                        estado: estado,
                        tipo_frete: tipo_frete,
                        prazo_entrega: prazo_entrega,
                        prazo_entrega_gratis: prazo_entrega_gratis,
                        frete_padrao: frete_padrao,
                        frete_gratis: frete_gratis,
                        reg_norte: reg_norte,
                        prazo_reg_norte: prazo_reg_norte,
                        reg_nordeste: reg_nordeste,
                        prazo_reg_nordeste: prazo_reg_nordeste,
                        reg_centro: reg_centro,
                        prazo_reg_centro: prazo_reg_centro,
                        reg_sudeste: reg_sudeste,
                        prazo_reg_sudeste: prazo_reg_sudeste,
                        reg_sul: reg_sul,
                        prazo_reg_sul: prazo_reg_sul,
                    },
                    success: function(resposta) {
                        $("#form_cartao .icone-frete").css('display', 'inline');
                        $("#form_boleto .icone-frete").css('display', 'inline');
                        $("#form_pix .icone-frete").css('display', 'inline');
                        // $("#icone-frete").css('display', 'inline');
                        $(".calculo-frete").html(resposta);
                        parcelamento();
                        atualizaBoleto();
                        atualizaPix();
                        //load('open');
                        load("close");
                    }
                });
            }
        }


        $("#estado").change(() => {
            // console.log("MUDOU ESTADO AO TROCAR CEP");
            frete();
        });

        // APLICAR CUPOM
        $("#div-cupom").hide();
        $("#btn-mostrar-cupom").click((e) => {
            $("#div-cupom").toggle();
        });

        $("#msg-cupom").hide();
        $("#btn-aplicar-cupom").hide();

        var valor = 0;
        var meio_pagamento = 0;
        $("#cupom").blur(() => {
            var nome = $("#nome").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();
            if (nome == "" || cpf == "" || telefone == "") {
                alert("Antes de aplicar o cupom, você precisa preencher seus dados.");
                $("#nome").focus();
                $("#cupom").val('');
            } else {
                var produto = <?= $produto->id ?>;
                var cupom = $("#cupom").val();
                <?php if (@$checkout->plano) : ?>
                    var plano = <?= $checkout->plano ?>;
                <?php else : ?>
                    var plano = null;
                <?php endif; ?>

                $.ajax({
                    url: "<?= $router->route("produtos.cupomBuscar") ?>",
                    type: "POST",
                    data: {
                        cupom: cupom,
                        produto: produto,
                        plano: plano
                    },
                    success: function(callback) {
                        // console.log("url", this.url);
                        // console.log(callback);
                        cupom = JSON.parse(callback);
                        $("#msg-cupom").show().html(cupom.msg);
                        if (cupom.resp == true) {
                            $("#btn-aplicar-cupom").show();
                            if (cupom.tipo == 0) {
                                cupom.valor = <?= $preco ?> * (cupom.valor / 100);
                            } else {
                                cupom.valor = cupom.valor * 1;
                            }
                            // console.log(cupom.valor);
                            valor = cupom.valor.toFixed(2);
                            meio_pagamento = cupom.meio_pagamento;
                        }
                    }
                });
            }
        });

        $("#fechar").hide();
        $("#cancelar").show();

        var cupom_total = false;
        $("#btn-aplicar-cupom").click((e) => {
            var nome = $("#nome").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();

            if (nome == "" || cpf == "" || telefone == "") {
                alert("Antes de aplicar o cupom, você precisa inserir seus dados");
                $("#nome").focus();
            } else {
                total_order_bump = 0;
                cupom_total = true;
                // console.log("meio_pagamento", meio_pagamento);
                $("#valor_desconto").attr("value", valor);
                $("#meio_pagamento_desconto").attr("value", meio_pagamento);

                <?php if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) : ?>
                    atualiza_preco_orderbump(totalprod, 0);
                <?php endif; ?>

                var pagamento = meio_pagamento.split(",");
                pagamento.forEach((i, val) => {
                    // console.log("valor i",i);
                    preco = <?= (float) $preco ?>;
                    val = parseFloat(preco) - parseFloat(valor) + total_order_bump;
                    console.log(val, i);
                    if (i == 1) {
                        parcelamento();
                    } else if (i == 2) {

                        <?php if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) : ?>
                            atualiza_preco_orderbump(totalprod, 3);
                        <?php endif; ?>

                        $("#resultado_boleto").html(val.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }));
                        $("#inp_valor_boleto").attr("value", val);
                    } else if (i == 3) {

                        <?php if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) : ?>
                            atualiza_preco_orderbump(totalprod, 2);
                        <?php endif;
                        ?>

                        $("#resultado_pix").html(val.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }));
                        $("#inp_valor_pix").attr("value", val);
                    } else if (i == 10) {

                        parcelamento();
                        $("#resultado_pix").html(val.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }));
                        $("#resultado_boleto").html(val.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }));

                        <?php if (@$checkout->planos_order_bump && $checkout->usar_order_bump == 1) : ?>
                            atualiza_preco_orderbump(totalprod, 2);
                            atualiza_preco_orderbump(totalprod, 3);
                        <?php endif ?>
                        $("#inp_valor_boleto").attr("value", val);
                        $("#inp_valor_pix").attr("value", val);
                    }
                })
                // $("#status").modal("hide");
                $("#fechar").show();
                $("#cancelar").hide();
                alert("Cupom aplicado com sucesso!");
            }
        });
        // FIM APLICAR CUPOM

        // ENVIA DADOS DE TRANSAÇÃO CARTÃO
        $("#btn-cartao").click((e) => {
            if (typeof gerarPagarmeToken === "function") {
                gerarPagarmeToken(e);
            } else {
                e.preventDefault();
                console.error('Função de tokenização do cartão não disponível.');
                alert('Não foi possível iniciar o processamento do cartão. Atualize a página e tente novamente.');
            }
        });
        // FIM ENVIA DADOS DE TRANSAÇÃO CARTÃO
        var tgdeveloper = {
            /**
             * getCardFlag
             * Return card flag by number
             *
             * @param cardnumber
             */
            getCardFlag: function(cardnumber) {
                var cardnumber = cardnumber.replace(/[^0-9]+/g, '');
                console.log(cardnumber);
                var cards = {
                    visa: /^4[0-9]{12}(?:[0-9]{3})/,
                    mastercard: /^5[1-5][0-9]{14}/,
                    diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}/,
                    amex: /^3[47][0-9]{13}/,
                    discover: /^6(?:011|5[0-9]{2})[0-9]{12}/,
                    hipercard: /^(606282\d{10}(\d{3})?)|(3841\d{15})/,
                    elo: /^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})/,
                    jcb: /^(?:2131|1800|35\d{3})\d{11}/,
                    aura: /^(5078\d{2})(\d{2})(\d{11})$/
                };

                for (var flag in cards) {
                    if (cards[flag].test(cardnumber)) {
                        return flag;
                    }
                }

                return false;
            }

        }

        function dadosCartao() {
                   
            var mesVal = $("#mes-val").val();
            var anoVal = $("#ano-val").val();
          
            var data = new Date();
            var exp_cart = new Date($("#ano-val").val(), $("#mes-val").val() - 1, "01");
            var mes = (data.getMonth() + 1).toString();
            var mesF = (mes.length == 1) ? '0' + mes : mes;
            var ano = data.getFullYear();

            var nome = $("#nome").val();
            var numcart = $("#numcart").val();
            var cvv = $("#cvv").val();
            var titular = $("#titular").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();
            //var ddd = $("#ddd").val();

            

            var bandeira = tgdeveloper.getCardFlag(numcart);
            //$("#brand").val(bandeira);

            var hoje = mesF + "-" + ano;
            // console.log(exp_cart, hoje, nome);

            $("#numcart").removeClass('input-error');
            $("#ano-val").removeClass('input-error');
            $("#mes-val").removeClass('input-error');
            $("#cvv").removeClass('input-error');
            $("#titular").removeClass('input-error');
            $("#nome").removeClass('input-error');
            $("#cpf").removeClass('input-error');
            $("#telefone").removeClass('input-error');

            if (numcart == "") {
                // alert("Preencha o número do cartão");
                $("#numcart").addClass('input-error');
                // $("#numcart").focus();
            } else if (exp_cart <= new Date()) {
                $("#ano-val").addClass('input-error');
                $("#mes-val").addClass('input-error');
                // alert("Data de validade do cartão vencida")
            } else if (cvv == "") {
                // alert("Preencha o digito verificador do cartão");
                $("#cvv").addClass('input-error');
                // $("#cvv").focus();
            } else if (titular == "") {
                // alert("Preencha o nome do titular do cartão");
                $("#titular").addClass('input-error');

                // $("#titular").focus();
            } else if (nome == "") {
                // alert("Preencha o nome");
                $("#nome").addClass('input-error');

                // $("#nome").focus();
            } else if (cpf == "") {
                // alert("Preencha o cpf");
                $("#cpf").addClass('input-error');

                // $("#cpf").focus();
            } else if (telefone == "") {
                // alert("Preencha o telefone");
                $("#telefone").addClass('input-error');

                // $("#telefone").focus();
            } else {
                var form = $("form[name=checkout], form[name=form-cupom], form[name=cartao]");
                var url = "<?= @$checkout->adquirente == "redrocket" ? $router->route("checkout.transacao") : $router->route("checkout.transacaoTransparente") ?>";
                console.log(url);

                salvar(form, url, "dadosCartao");
            }
        }
        // ENVIA ENVIA DADOS DE TRANSAÇÃO BOLETO
        $("#btn-boleto, #btn-img-boleto").click((e) => {
            e.preventDefault();
            var nome = $("#nome").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();
            //var ddd = $("#ddd").val();
            if (nome == "") {
                // alert("Preencha o nome");
                $("#nome").addClass('input-error');
                // $("#nome").focus();
            } else if (cpf == "") {
                // alert("Preencha o cpf");
                $("#cpf").addClass('input-error');
                // $("#cpf").focus();
            } else if (telefone == "") {
                // alert("Preencha o telefone");
                $("#telefone").addClass('input-error');
                // $("#telefone").focus();
            } else {
                $("#nome").removeClass('input-error');
                $("#cpf").removeClass('input-error');
                $("#telefone").removeClass('input-error');
                
                var form = $("form[name=checkout], form[name=form-cupom], form[name=boleto]");
                var url = "<?= @$checkout->adquirente_boleto == "redrocket" ? $router->route("checkout.transacao") : $router->route("checkout.transacaoTransparente") ?>";

                if ($("#endereco_obrigatorio").val() == 1 && validarEndereco())
                    salvar(form, url);
                else if ($("#endereco_obrigatorio").val() == 0)
                    salvar(form, url);

                // console.log(form.serialize());
            }
        });
        // FIM ENVIA DADOS DE TRANSAÇÃO BOLETO

        // ENVIA DADOS DE TRANSAÇÃO PIX
        $("#btn-pix").click((e) => {
            e.preventDefault();
            var nome = $("#nome").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();
            //var ddd = $("#ddd").val();

            $("#nome").removeClass('input-error');
            $("#cpf").removeClass('input-error');
            $("#telefone").removeClass('input-error');

            if (nome == "") {
                // alert("Preencha o nome");
                $("#nome").addClass('input-error');
                // $("#nome").focus();
            } else if (cpf == "") {
                // alert("Preencha o cpf");
                $("#cpf").addClass('input-error');
                // $("#cpf").focus();
            } else if (telefone == "") {
                // alert("Preencha o telefone");
                $("#telefone").addClass('input-error');
                // $("#telefone").focus();
            } else {
                var form = $("form[name=checkout], form[name=form-cupom], form[name=pix]");
                var url = "<?= @$checkout->adquirente_pix == "redrocket" ? $router->route("checkout.transacaoPix") : $router->route("checkout.transacaoTransparente") ?>";


                $.ajax({
                    url: url,
                    // url: "< ?= $router->route("checkout.transacaoPix") ?>",
                    data: form.serialize(),
                    type: "POST",
                    //dataType: "json",
                    beforeSend: function() {
                        // console.log("atualizando");
                        load('open');
                    },
                    error: function(err) {
                        console.log(err, "O servidor não conseguiu processar o pedido");
                        load('close');
                    },
                    success: function(callback) {
                        // console.log(callback);

                        function data(callback) {
                            try {
                                let validJSON = callback.trim();
                                let results = [];
                                let start = 0;
                                let nestedCount = 0;
                                for (let i = 0; i < validJSON.length; i++) {
                                // Conta a quantidade de aberturas e fechamentos de chaves `{}` para identificar o fim de um objeto
                                if (validJSON[i] === '{') {
                                    nestedCount++;
                                } else if (validJSON[i] === '}') {
                                    nestedCount--;
                                }

                                // Quando encontrar o fim do objeto (nestedCount === 0), tenta fazer o parsing
                                if (nestedCount === 0) {
                                    const jsonString = validJSON.slice(start, i + 1); // Extrai o objeto JSON
                                    try {
                                        results.push(JSON.parse(jsonString)); // Faz o parse no JSON extraído
                                    } catch (error) {
                                        console.error("Erro ao parsear JSON:", error, jsonString);
                                        return false;
                                    }
                                    start = i + 1; // Move o índice para o próximo objeto
                                }
                            }

                            // Retorna todos os objetos JSON encontrados, caso existam múltiplos
                            return results.length > 0 ? results : JSON.parse(validJSON);
                            } catch (error) {
                                console.log(error);
                                return false;
                            }
                        }

                        var data = data(callback);
                        //console.log('numero 1', data[1]);
                        //console.log('numero 0', data[0].qrcode);
                        console.log('numero 1', data);
                        //if (data[1].msg == "Venda salva com sucesso" && data[1].status_transacao) {
                        if (data[0].msg == "Venda salva com sucesso" && data[0].status_transacao) {
                            $("#cpf").removeClass('input-error');

                            // ESCONDE A PÁGINA ATUAL E MOSTRA A TELA PIX
                            document.querySelector(".container-75")?.style.setProperty("display", "none");
                            document.body.querySelector(".row.mt-4").style.display = "none";
                            document.getElementById("pix-page").style.display = "block";
                            window.scrollTo({ top: 0, behavior: "smooth" });
                            iniciarContador(300);

                            // GERA E EXIBE O QR CODE
                            $('#qrcode').empty().qrcode({
                                width: 220,
                                height: 220,
                                text: data[0].qr_code
                            });

                            // EXIBE O CÓDIGO PIX PARA COPIA E BOTÃO
                            $("#inp-copiar").val(data[0].qr_code);
                            $("#btn-pix-confirmar").show();
                            startPixStatusWatcher(data[0].venda);

                            // REDIRECIONA AO FINALIZAR COMPRA
                            $("#btn-pix-confirmar").off("click").on("click", function (e) {
                                e.preventDefault();
                                stopPixStatusWatcher();
                                location.href = "<?= url("checkout/finalizar-compra/") ?>" + data[0].venda;
                            });
                        }else if (data[0].msg == "CPF inválido") {
                            $("#cpf").addClass('input-error');
                            // alert("CPF inválido. Verifique se digitou os números do CPF corretamente e tente novamente");
                            load("close");
                        }

                        load('close');
                    }
                });
            }
        });
        // FIM ENVIA DADOS DE TRANSAÇÃO PIX

        $("#copiar").click(() => {
            $("#inp-copiar").select();
            var copiar = document.execCommand('copy');
        });

        // AJAX TRANSAÇÃO
        function salvar(form, url = null, text = null) {
            console.log(text);
            $.ajax({
                url: url,
                data: form.serialize(),
                type: "POST",
                beforeSend: function() {
                    // console.log("atualizando");
                    console.log(url);
                    $(".ajax_load_box_title").html("Atualizando");
                    load('open');
                },
                error: function(err) {
                    //console.log(err, "O servidor não conseguiu processar o pedido");
                    load('close');
                },
                success: function(callback) {
                    function data(callback) {
                        try {
                            return JSON.parse(callback);
                        } catch (error) {
                            return false;
                        }
                    }

                    var data = data(callback);                    

                    if (data.msg == "Venda salva com sucesso" && data.status_transacao) {
                        $("#cpf").removeClass('input-error');

                        $('#btn-cartao').css('pointer-events', 'none').css('opacity', '0.3');
                        $('#btn-boleto').css('pointer-events', 'none').css('opacity', '0.3');
                        $('#btn-pix').css('pointer-events', 'none').css('opacity', '0.3');
                        load('close');

                        if (data.venda) {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>" + data.venda;
                        } else {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>";
                        }
                    } else if (data.msg == "CPF inválido") {
                        $("#cpf").addClass('input-error');
                        // alert("CPF inválido. Verifique se digitou os números do CPF corretamente e tente novamente");
                        load("close");
                    } else if (data.status_transacao) {
                        $('#btn-cartao').css('pointer-events', 'none').css('opacity', '0.3');
                        $('#btn-boleto').css('pointer-events', 'none').css('opacity', '0.3');
                        $('#btn-pix').css('pointer-events', 'none').css('opacity', '0.3');
                        load('close');

                        if (data.venda) {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>" + data.venda;
                        } else {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>";
                        }
                    } else if (data.msg == 'Os dados do comprador não podem ser iguais ao do vendedor.') {
                        alert(data.msg);
                        load("close");
                    } else {
                        $('#btn-cartao').css('pointer-events', 'none').css('opacity', '0.3');
                        $('#btn-boleto').css('pointer-events', 'none').css('opacity', '0.3');
                        $('#btn-pix').css('pointer-events', 'none').css('opacity', '0.3');
                        load('close');
                        
                        location.href = "<?= url("checkout/finalizar-compra/") ?>";
                    }              
                }
            });
        }
        // FIM AJAX TRANSAÇÃO

        // GERA QRCODE
        function makeCode(texto) {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                width: 200,
                height: 200
            });
            qrcode.makeCode(texto);
        }
        // FIM GERA QRCODE

        var pixStatusInterval = null;

        function startPixStatusWatcher(vendaId) {
            stopPixStatusWatcher();

            if (!vendaId) {
                return;
            }

            pixStatusInterval = setInterval(function() {
                $.ajax({
                    url: "<?= $router->route("checkout.consultarPix") ?>",
                    type: "POST",
                    data: { venda: vendaId },
                    success: function(response) {
                        var result;
                        try {
                            result = typeof response === "object" ? response : JSON.parse(response);
                        } catch (e) {
                            return;
                        }

                        if (!result || result.success === false) {
                            return;
                        }

                        if (result.pago && result.redirect) {
                            stopPixStatusWatcher();
                            location.href = result.redirect;
                        } else if (result.cancelado) {
                            stopPixStatusWatcher();
                            if (result.motivo) {
                                $("#aviso_pix").html("Pagamento Pix cancelado: " + result.motivo).show();
                            }
                        }
                    }
                });
            }, 7000);
        }

        function stopPixStatusWatcher() {
            if (pixStatusInterval) {
                clearInterval(pixStatusInterval);
                pixStatusInterval = null;
            }
        }

        // OPÇÕES TAB PIX
        $(".instrucao_pix").hide();
        $("#btn-pix-confirmar").hide();
        $("#btn-pix").show();
        $("#servico-pix").hide();

        $("#nav-pix-tab").click(() => {
            if ("<?= @$adquirente ?>" == "redrocket") {
                $.ajax({
                    url: "<?= $router->route("checkout.servicoPix") ?>",
                    type: "GET",
                    success: function(callback) {
                        // console.log(callback);
                        if (callback == "Funcionando") {
                            $("#btn-pix").show();
                            $("#servico-pix").hide();
                        } else {
                            $("#aviso_pix").html("Serviço de pagamento do Pix está fora do ar. Tente novamente mais tarde ou escolha outra forma de pagamento");
                        }
                    }
                });
            } else {
                $("#btn-pix").show();
                $("#servico-pix").hide();
            }
        });
        // FIM OPÇÕES TAB PIX

        // CADASTRAR CARRINHO ABANDONADO
        $("#links, #numcart").click(() => {
            // console.log("clicou");

            var form = $("form[name=checkout]");
            cadastroCarrinho(form);
        });

        $("#cpf, #cep").blur((e) => {
            // console.log("CARRINHO ABANDONADO");

            var form = $("form[name=checkout]");
            cadastroCarrinho(form);
        });

        function cadastroCarrinho(form) {
            // console.log("DADOS DO CARRINHO ENVIADOS \n", form.serialize());
            // console.log("ROTA DO CARRINHO ABANDONADO: <?= $router->route("abandono.cadastro") ?>");
            $.ajax({
                url: "<?= $router->route("abandono.cadastro") ?>",
                data: form.serialize(),
                type: "POST",
                beforeSend: function() {
                    // console.log("atualizando");
                },
                error: function(err) {
                    // console.log(err, "O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    // console.log(callback);

                    function data(callback) {
                        try {
                            return JSON.parse(callback);
                        } catch (error) {
                            return false;
                        }
                    }

                    var data = data(callback);
                    // console.log(data);

                    if (data.id) {
                        $("#carrinho_abandonado").val(data.id);
                    }
                }
            });
        }
        // FIM CADASTRO CARRINHO ABANDONADO

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (e.target.id !== "nav-pix-tab") {
                stopPixStatusWatcher();
            }
        });

        $(window).on("beforeunload", stopPixStatusWatcher);
    </script>
    <!-- FIM SCRIPTS JAVASCRIPT -->

</body>
<!-- BODY -->

</html>

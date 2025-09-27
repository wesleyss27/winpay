<?php

use Source\App\Facebook\AppFacebook;
use Source\Models\Pixel\Pixel;

//session_start(); 
$v->layout("_theme");

function logsNaTela($dados)
{
    $texto = "{";
    if ($dados) {
        foreach ($dados as $key => $value) {
            $texto .= "'$key': '$value',";
        }
    }
    $texto .= "}";

    return $texto;
}

parse_str(@$_COOKIE["campanha"], $arr);
$valor = (@$arr["valor"] ? $arr["valor"] : (@$_SESSION["pagamento"]["preco"] / 100));
$pixel = explode(",", @$arr["id_pixel"]);
$analytics = explode(",", @$arr["analytics"]);
$forma_pagamento = $venda->forma_pagamento;
$up = @$upsell ? true : false;
$waCookie = (strpos($_SERVER['HTTP_COOKIE'] ?? '', 'from_whatsapp=1') !== false);
$waQS = $waCookie ? '&wa=1' : '';

if (@$cookies) {
    // var_dump($cookies);

    $idCamp = @$cookies->idCampanha;
    $campanha = @$cookies->campanha;
    $analytics = @$cookies->analytics;

    if ($cookies->pixel_facebook) {
        $facebook = json_decode($cookies->pixel_facebook);
        // echo $facebook->id_pixel;
    } else {
        $facebook = json_decode($cookies->pixel_facebook_api);
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
}


// if (@$consultarPix["msg-status"] == "Compra Aprovada!") {
//     echo "<script>alert('Compra Aprovada!')</script>";
// }

$status = array(
    1 => "AGUARDANDO PAGAMENTO",
    2 => "EM ANÁLISE",
    3 => "PRÉ AUTORIZADO",
    4 => "PAGO",
    5 => "CANCELADO",
    6 => "DEVOLVIDO",
    7 => "CHARGEBACK",
    8 => "CONCLUÍDO",
    9 => "BLOQUEADO"
);

$taxa_adq = array();


$v->start("facebook");

?>
<script>
(function () {
  try {
    var isHttps = location.protocol === 'https:';
    var hit = /whatsapp|wa\.me/i.test((document.referrer||"") + " " + (navigator.userAgent||""));
    if (hit) {
      document.cookie = "from_whatsapp=1; Max-Age=2592000; Path=/; SameSite=Lax" + (isHttps?"; Secure":"");
    }
    // Fallback: se já houver o cookie, acrescenta wa=1 nos iframes renderizados pelo PHP
    var hasWA = document.cookie.indexOf("from_whatsapp=1") !== -1;
    if (hasWA) {
      document.querySelectorAll('iframe[src*="pagamentos/pixel-"]').forEach(function(el){
        if (el.src.indexOf('wa=1') === -1) {
          el.src += (el.src.indexOf('?')>-1 ? '&' : '?') + 'wa=1';
        }
      });
    }
  } catch(e) {}
})();
</script>

<!-- < ?php -->
<!-- // TAXAS CASO TRANSAÇÕES SEJAM FEITAS POR OUTRAS ADQUIRENTES (MP, PAGARME, PAGSEGURO, PAGSEGURO ETC..) -->
<!-- // $taxa_adq[1] = @$checkout->dataAdquirente()->taxa1 > 0.00 ? @$checkout->dataAdquirente()->taxa1 : 0.00; -->
<!-- // $taxa_adq[2] = $checkout->dataAdquirente()->taxa2 > 0.00 ? $checkout->dataAdquirente()->taxa2 : 0.00; -->
<!-- // $taxa_adq[3] = @$checkout->dataAdquirente()->taxa3 > 0.00 ? @$checkout->dataAdquirente()->taxa3 : 0.00; -->
<!-- // $taxa_adq[4] = @$checkout->dataAdquirente()->taxa4 > 0.00 ? @$checkout->dataAdquirente()->taxa4 : 0.00; -->
<!-- // $taxa_adq[5] = @$checkout->dataAdquirente()->taxa5 > 0.00 ? @$checkout->dataAdquirente()->taxa5 : 0.00; -->
<!-- // $taxa_adq[6] = @$checkout->dataAdquirente()->taxa6 > 0.00 ? @$checkout->dataAdquirente()->taxa6 : 0.00; -->
<!-- // $taxa_adq[7] = @$checkout->dataAdquirente()->taxa7 > 0.00 ? @$checkout->dataAdquirente()->taxa7 : 0.00; -->
<!-- // $taxa_adq[8] = @$checkout->dataAdquirente()->taxa8 > 0.00 ? @$checkout->dataAdquirente()->taxa8 : 0.00; -->
<!-- // $taxa_adq[9] = @$checkout->dataAdquirente()->taxa9 > 0.00 ? @$checkout->dataAdquirente()->taxa9 : 0.00; -->
<!-- // $taxa_adq[10] = @$checkout->dataAdquirente()->taxa10 > 0.00 ? @$checkout->dataAdquirente()->taxa10 : 0.00; -->
<!-- // $taxa_adq[11] = @$checkout->dataAdquirente()->taxa11 > 0.00 ? @$checkout->dataAdquirente()->taxa11 : 0.00; -->
<!-- // $taxa_adq[12] = @$checkout->dataAdquirente()->taxa12 > 0.00 ? @$checkout->dataAdquirente()->taxa12 : 0.00; -->

<!-- ?> -->

<?php if (@$sandbox == 0) : ?>
    <!-- ANALYTICS OCTAPAY -->
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
    <!-- FIM ANALYTICS OCTAPAY -->

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

<?php

if (@$taboola) :
    $taboolaPixels = explode(",", @$taboola->id_pixel);
    $eventos = explode(",", @$taboola->purchase_taboola);
    $i = 0;
    foreach ($taboolaPixels as $pixel) :
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
    endforeach;
endif;
if (@$analitics) :
    $analiticsPixels = explode(",", @$analitics->id_pixel);
    foreach ($analiticsPixels as $pixel) :
        echo "
                <!-- PIXEL ANALITICS CAMPANHA -->
                <script async src='https://www.googletagmanager.com/gtag/js?id=$pixel'></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());

                    gtag('config', '$pixel');
                </script>
            ";
    endforeach;
endif;
if (@$ads) :
    $adsPixels = explode(",", @$ads->id_pixel);
    $conversao = explode(",", @$ads->tag_conversao);
    $i = 0;

    foreach ($adsPixels as $pixel) :
        echo "
                <!-- PIXEL ADS CAMPANHA - Global site tag (gtag.js) - Google Analytics -->
                <script async src='https://www.googletagmanager.com/gtag/js?id=$pixel'></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
    
                    gtag('config', '$pixel');
                </script>
            ";

        if ($conversao) :
            echo "<!-- Event snippet for Compra conversion page -->
                    <script>
                    gtag('event', 'conversion', {
                        'send_to': '" . @$coversao[$i] . "',
                        'transaction_id': '$venda->transacao'
                    });
                    </script>
                ";
        endif;
    endforeach;
endif;

if (@$tagManager) :
    $tagPixels = explode(",", @$tagManager->id_pixel);
    foreach ($tagPixels as $pixel) :
        if (@$tagManager->executar_checkout == 1) :
            echo "
                    <!-- PIXEL TAG MANAGER CAMPANHA -->
                    <!-- Google Tag Manager -->
                    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','" . $pixel . "');</script>
                    <!-- End Google Tag Manager -->
                ";
        endif;
    endforeach;
endif;

if (@$tiktok) :
    echo "<!-- COOKIE AFILIADO TIKTOK-->";
    $tiktokAfiliado = explode(",", $tiktok->id_pixel);
    $tiktok_boletoAfiliado = explode(",", @$tiktok->boleto);
    foreach ($tiktok_boletoAfiliado as $tiktok_boletoAfiliado) :
        if (@$tiktok_boletoAfiliado == 1 || $forma_pagamento != 2) :
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
    endforeach;
endif;
?>

<?php

if (@$facebook) {

    echo "<!-- INICIO COOKIES AFILIADOS -->";
    $facebookPixels = explode(",", @$facebook->id_pixel);
    $valor = @$facebook->valor ? $facebook->valor : $venda->valor_liquido;
    foreach ($facebookPixels as $pixel) :
        if ($facebook->id)
            $fb_pixel = (new Pixel())->findById($facebook->id);

        if ((@$forma_pagamento != 2 || @$facebook->boleto == 1) && @$venda->status_transacao != 5) :
            if (@$fb_pixel->tipo_integracao == "api") :
                $dados = array();
                $dados["pixel"] = $pixel;
                $dados["token"] = $fb_pixel->token_acesso;
                $dados["evento"] = "Purchase";
                $dados["site_url"] = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $dados["ip"] = $_SERVER["REMOTE_ADDR"];
                $dados["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
                $dados["valor"] = $valor;

                echo "
                <!-- PIXEL FACEBOOK CHECKOUT -->
                    <iframe src='" . url("pagamentos/pixel-base.php?pixel=$pixel&valor=$valor&fbq_event=Purchase") . "$waQS'
          frameborder='0' height='0' width='0' style='top:-20px; position:absolute'></iframe>
                ";

                $fb = (new AppFacebook())->pixel($dados);

                echo "<script>fbq('track', 'Purchase',  {currency: 'brl', value: " . $valor . "} );</script>";

                $referenciaAf = "pixel: $pixel, evento: Purchase";
            else :
                echo "
                    <!-- PIXEL FACEBOOK CAMPANHA -->
                    <iframe src='" . url("pagamentos/pixel-purchase.php?pixel=$pixel&valor=$valor") . "$waQS'
          frameborder='0' height='0' width='0' style='top:-20px; position:absolute'></iframe>
                ";
            endif;
        endif;
    endforeach;

    echo "<!-- FIM COOKIES AFILIADOS -->";

} elseif(@$checkout->pixel_facebook || @$checkout->pixel_facebook_api){

    echo "<!-- COOKIES PRODUTOR -->";

    $pixels = explode(",", $checkout->dataPixelFacebook()->id_pixel);
    $valor = @$checkout->dataPixelFacebook()->valor ? $checkout->dataPixelFacebook()->valor : $venda->valor_liquido;

    foreach ($pixels as $pixel2) :
        // echo "$forma_pagamento " . $checkout->dataPixelFacebook()->boleto;
        if ( ( ( @$forma_pagamento == 2 && @$checkout->dataPixelFacebook()->boleto == 1 ) && @$venda->status_transacao != 5 ) 
		||  @$forma_pagamento != 2 && @$venda->status_transacao != 5  ) {
		
            if (@$checkout->dataPixelFacebook()->tipo_integracao == "api") {
                $dados = array();
                $dados["pixel"] = $pixel2;
                $dados["token"] = $checkout->dataPixelFacebook()->token_acesso;
                $dados["evento"] = "Purchase";
                $dados["site_url"] = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $dados["ip"] = $_SERVER["REMOTE_ADDR"];
                $dados["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
                $dados["valor"] = $valor;

                echo "
                <!-- PIXEL FACEBOOK CHECKOUT -->
                    <iframe src='" . url("pagamentos/pixel-base.php?pixel=$pixel2&valor=$valor&fbq_event=Purchase") . "$waQS'
          frameborder='0' height='0' width='0' style='top:-20px; position:absolute'></iframe>
                ";

                $fb = (new AppFacebook())->pixel($dados);

                echo "<script>fbq('track', 'Purchase',  {currency: 'brl', value: " . $valor . "}  );</script>";


                $referenciaPd = "pixel: $pixel2, evento: Purchase";
            }else {
                echo "
                    <!-- PIXEL FACEBOOK CHECKOUT -->
                    <iframe src='" . url("pagamentos/pixel-purchase.php?pixel=$pixel2&valor=$valor") . "$waQS'
          frameborder='0' height='0' width='0' style='top:-20px; position:absolute'></iframe>
                ";
            }
        }
    endforeach;

    echo "<!-- FIM COOKIES PRODUTOR -->";
}

if (@$checkout->pixel_taboola) :
    $pixels = explode(",", $checkout->dataPixelTaboola()->id_pixel);
    $eventos = explode(",", @$checkout->dataPixelTaboola()->purchase_taboola);
    $i = 0;
    foreach ($pixels as $pixel1) :
        if (@$forma_pagamento != 2 || @$checkout->dataPixelTaboola()->boleto == 1) :
            echo "<!-- PIXEL TABOOLA CHECKOUT -->
            <!--iframe src='" . url("checkout/pixel-taboola?pixel=$pixel1&ev=" . @$eventos[$i]) . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe-->

            <!-- Taboola Pixel Code -->
            <script type='text/javascript'>
            window._tfa = window._tfa || [];
            window._tfa.push({notify: 'event', name: 'page_view', id: $pixel1});
            !function (t, f, a, x) {
                    if (!document.getElementById(x)) {
                        t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
                    }
            }(document.createElement('script'),
            document.getElementsByTagName('script')[0],
            '//cdn.taboola.com/libtrc/unip/$pixel1/tfa.js',
            'tb_tfa_script');
            </script>
            <!-- End of Taboola Pixel Code -->
        ";

            if (@$eventos[$i]) :
                echo "
                <!-- Taboola Pixel Code -->
                <script>
                    _tfa.push({notify: 'event', name: '$eventos[$i]', id: $pixel1});
                </script>
                <!-- End of Taboola Pixel Code -->
            ";
            endif;
            $i++;
        endif;
    endforeach;
endif;

if (@$checkout->pixel_google_analitics) :
    $google = explode(",", $checkout->dataPixelGoogleAnalitics()->id_pixel);
    $i = 0;

    foreach ($google as $google) :
        if (@$checkout->dataPixelGoogleAnalitics()->boleto == 1 || $forma_pagamento != 2) :
            echo "
                    <!-- PIXEL ANALITICS CHECKOUT -->
                    <!-- Global site tag (gtag.js) - Google Analytics -->
                    <script async src='https://www.googletagmanager.com/gtag/js?id=<?= $google ?>'></script>
                    <script>
                        window.dataLayer = window.dataLayer || [];
                        function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());

                        gtag('config', '<?= $google ?>');
                    </script>
                ";
            $i++;
        endif;
    endforeach;
endif;

if (@$checkout->pixel_google_ads) :
    $google = explode(",", $checkout->dataPixelGoogleAds()->id_pixel);
    $conversao = explode(",", $checkout->dataPixelGoogleAds()->tag_conversao);
    $i = 0;

    foreach ($google as $google) :
        if (@$checkout->dataPixelGoogleAds()->boleto == 1 || $forma_pagamento != 2) :
            echo "
                    <!-- PIXEL ADS CHECKOUT -->
                    <!-- Global site tag (gtag.js) - Google Analytics -->
                    <script async src='https://www.googletagmanager.com/gtag/js?id=<?= $google ?>'></script>
                    <script>
                        window.dataLayer = window.dataLayer || [];
                        function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());

                        gtag('config', '<?= $google ?>');
                    </script>
                    <!-- Event snippet for Compra conversion page -->
                    <script>
                    gtag('event', 'conversion', {
                        'send_to': '" . @$coversao[$i] . "',
                        'transaction_id': ''
                    });
                    </script>
                ";
            $i++;
        endif;
    endforeach;
endif;
?>

<?php
if (@$checkout->pixel_google_tag_manager) :
    foreach ($pixels as $pixel) :
        if (@$executar_checkout == 1) :
            echo "
                <!-- PIXEL TAG MANAGER CHECKOUT -->
                <!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','<?= $google ?>');</script>
                <!-- End Google Tag Manager -->";
        endif;
    endforeach;
endif;

if (@$checkout->pixel_tiktok) :
    echo "<!-- COOKIE PRODUTOR TIKTOK-->";
    $tiktok = explode(",", @$checkout->dataPixelTikTok()->id_pixel);
    foreach ($tiktok as $tiktok) :
        if (@$checkout->dataPixelTikTok()->boleto == 1 || $forma_pagamento != 2) :
            echo "<script>
                !function (w, d, t) {
                w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};
                ttq.load('$tiktok');
                ttq.page();
                }(window, document, 'ttq');
            </script>";
        endif;
    endforeach;
endif;
?>
<?php $v->end(); ?>

<?php
if (@$checkout->pixel_google_tag_manager) :
    $google = explode(",", $checkout->dataPixelGoogleTagManager()->id_pixel);
    foreach ($google as $google) :
        echo "
                <!-- Google Tag Manager (noscript) -->
                <noscript><iframe src='https://www.googletagmanager.com/ns.html?id=<?= $google ?>'
                height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
                <!-- End Google Tag Manager (noscript) -->
            ";
    endforeach;
endif;
?>

<!-- PIXEL TIKTOK AFILIADO -->
<?php
if (@$tiktok) :
    echo "<!-- COOKIE AFILIADO TIKTOK EVENTO-->";
    $tiktok_id = explode(",", @$tiktok->id_pixel);
    $tiktok_boleto = explode(",", @$tiktok->boleto);
    $valor_tiktok = @$tiktok->valor ? @$tiktok->valor : @$venda->valor_liquido;
    foreach ($tiktok_boleto as $tiktok_boleto) :
        if (@$tiktok_boleto == 1 || $forma_pagamento != 2) :
            foreach ($tiktok_id as $tiktok) :
                if (@$checkout->dataPixelTikTok()->boleto == 1 || $forma_pagamento != 2) :
                    echo "<script>ttq.instance('$tiktok').track('CompletePayment',{
                        prince: $valor_tiktok,})</script>";
                endif;
            endforeach;
        endif;
    endforeach;
endif;
?>
<!-- FIM PIXEL TIKTOK AFILIADO -->

<!-- PIXEL TIKTOK PRODUTOR -->
<?php
if (@$checkout->pixel_tiktok) :
    echo "<!-- COOKIE PRODUTOR TIKTOK EVENTO-->";
    $tiktok = explode(",", @$checkout->dataPixelTikTok()->id_pixel);
    $valor_tiktok = @$checkout->dataPixelTikTok()->valor != "" ? @$checkout->dataPixelTikTok()->valor : @$venda->valor_liquido;
    foreach ($tiktok as $tiktok) :
        if (@$checkout->dataPixelTikTok()->boleto == 1 || $forma_pagamento != 2) :
            echo "<script>ttq.instance('$tiktok').track('CompletePayment', {
                prince: $valor_tiktok,})</script>";
        endif;
    endforeach;
endif;
?>
<!-- FIM PIXEL TIKTOK PRODUTOR  -->

<?php $v->start("topo"); ?>
<div class="topo row">
    <div class="col-md-3">
        <img src="<?= url("themes/imgs/logos/logo4.png"); ?>" alt="" width="40%">
    </div>

    <div class="col-md-6">
        <!-- <h4>Olá <b><?= $venda->dataEndereco()->nome ?></b>, seu pedido está <?= @$status[@$venda->status_transacao] ?>!</h4> -->
        <!-- Verifique sua caixa de email para ver os detalhes do seu pedido ou clique em entrar <br> -->
    </div>

    <div class="col-md-3 far fa-check-circle" style="font-weight: 700">
        <p style="font-family: 'Montserrat'; font-weight: 700" class="texto-teste"><?= @$status[@$venda->status_transacao] ?></p>
        <!-- <i class="far fa-check-circle" color= "#fff"></i> -->
        <!-- <a href="<?= url() ?>" target="_blank" class="btn btn-warning">Entrar</a><br> -->
    </div>
</div>
<?php $v->end(); ?>

<div class="parabens-compra">
    <p>
        <?php
        $nome = explode(" ", $venda->dataEndereco()->nome, 2);
        echo $nome[0];
        ?>,
        seu pedido está <?= @$status[@$venda->status_transacao] ?>!
    </p>
</div>

<div class="container">
    <?php if (@$venda) : ?>
        <div class="row resum">
            <div class="col-md-6">
                <div class="card card-body">
                    <?php if ($venda->forma_pagamento != 3 && $venda->transacao == "") : ?>
                        <p class="text-warning" style="text-align: center; font-size: 60px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </p>
                        <p class="text-danger">
                            <b>Infelizmente houve um problema com a sua compra,
                                <a href="<?= url("checkout/$checkout->codigo/$venda->id") ?>">CLIQUE AQUI</a>
                                e tente novamente
                            </b>
                        </p>

                        <div class="alert alert-danger">
                            <?php
                            include "erros.php";
                            // var_dump(@$_SESSION["erros"]);
                            $erros = @$_SESSION["erros"];
                            if (@$erros["comprador"]) {
                                foreach ($erros["comprador"] as $comp) {
                                    echo @$erros_transacao["$comp"] . "<br>";
                                }
                            }

                            if (@$erros["pedido"]) {
                                foreach ($erros["pedido"] as $ped) {
                                    echo @$erros_transacao["$ped"] . "<br>";
                                }
                            }

                            if (@$erros["transacao"]) {
                                echo @$erros["transacao"] . "<br>";
                            }
                            ?>
                        </div>
                    <?php else : ?>
                        <p>
                            <b>Resumo da compra:</b>
                            <br>Endereço completo:
                            <?=
                            $venda->dataEndereco()->rua . ", " .
                                $venda->dataEndereco()->numero . " - " .
                                $venda->dataEndereco()->bairro;
                            ?>
                            <br>Cidade: <?= $venda->dataEndereco()->cidade ?>
                            <br>CEP: <?= $venda->dataEndereco()->cep ?>
                        </p>

                        <p>
                            Dúvidas: <strong><?= $venda->dataProdutor->email ?> </strong> <br>
                            Whatsapp: <strong><?= $venda->dataProduto()->whatsapp_checkout ?> </strong>
                        </p>
                        <?php if (@$forma_pagamento == 1 && $venda->status_transacao == 2) : ?>
                            <p>Compras no cartão podem levar até 48 horas para serem aprovadas.</p>
                        <?php elseif (@$forma_pagamento == 2) : ?>
                            <p>Boletos levam até 3 dias úteis para serem compensados.</p>
                            <p><a href="<?= $venda->link_boleto; ?>" class="btn btn-success" target="_blank">IMPRIMIR BOLETO</a></p>
                        <?php elseif (@$forma_pagamento == 3 && $venda->status_transacao == 1) : ?>
                            <p>Se você pagou com Pix aguarde alguns minutos e recarregue a página.</p>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            </div>

            <div class="col-md-6">
                <p class="txt-right-desktop"> Código Compra: <?= $venda->transacao ?> </p>
                <?php
                if (@$venda->planos_order_bump && @$venda->preco_order_bump) : ?>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-2 col-2 col-im">
                                <?php $foto = $venda->plano && @$venda->dataPlano()->foto ? $venda->dataPlano()->foto : $venda->dataProduto()->foto ?>
                                <img class="img-produto-order-bump" src="<?= url("uploads/produtos/" . $foto); ?>" alt="">

                            </div>

                            <div class="col-md-7 col-8 txt-center-mobile">
                                <p style="font-weight:700;">
                                    <?= $venda->dataProduto()->nome ?>
                                    <?= $venda->plano ? " \ " . $venda->dataPlano()->nome : "" ?>
                                </p>
                            </div>

                            <div class="col-1 desktop" style="padding: 5px">
                                <i style="font-size: 30px; font-weight:700; color: green" class="far fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="card card-body">
                        <div class="row">
                            <?php $planos_order_bump = explode(",", $venda->planos_order_bump);
                            foreach ($planos_order_bump as $cod_plano) :
                                // echo " codigo " . $cod_plano;
                                $plano = $planos->findById($cod_plano);
                            ?>
                                <br>

                                <div class="col-md-2 col-2 col-im">
                                    <?php $foto = $plano->foto ? $plano->foto : $plano->dataProduto()->foto ?>
                                    <img class="img-produto-order-bump2" src="<?= url("uploads/produtos/" . $foto) ?>" alt="">
                                </div>

                                <div class="col-md-10 col-10 txt-center-mobile2">
                                    <p>
                                        <?= $plano->nome ?>
                                    </p>
                                </div>
                                <br>
                                <br>

                            <?php endforeach; ?>
                        </div>
                    </div>
            </div>

        <?php
                else : ?>
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4 col-img">
                        <?php $foto = $venda->plano && @$venda->dataPlano()->foto ? $venda->dataPlano()->foto : $venda->dataProduto()->foto ?>
                        <img class="img-produto" src="<?= url("uploads/produtos/" . $foto); ?>" alt="">
                    </div>

                    <div class="col-md-7 txt-center-mobile">
                        <p style="font-size: 20px; font-weight:700 ">
                            <?= $venda->dataProduto()->nome ?>
                            <?= $venda->plano ? " \ " . $venda->dataPlano()->nome : "" ?>
                        </p>
                    </div>

                    <div class="col-1 desktop" style="padding: 5px">
                        <i style="font-size: 30px; font-weight:700; color: green" class="far fa-check-circle"></i>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
</div>

<!-- UPSELL -->
<script>
    console.log(" <?= "upsell**| " . @$upsell->id . " pg " . $forma_pagamento; ?>");
    var upsell = 0;
</script>
<?php if ($forma_pagamento != 3) : ?>
    <?php if ($upsell) : ?>
        <?php if ($venda->status_transacao != 5 && ($forma_pagamento == 1 || ($forma_pagamento == 2 && $upsell->boleto == 1))) : ?>
            <script>
                upsell = 1;
            </script>
            <div class="upsell">
                <div class="row desconto-especial">
                    <div class="col-md-12">
                        <?= $upsell->headline ?>
                    </div>
                </div>

                <div class="row bloco-upsell">
                    <div class="col-md-5 txt-center-mobile">
                        <?php $foto = @$upsell->dataPlano()->foto ? $upsell->dataPlano()->foto : $upsell->dataProduto()->foto ?>
                        <img class="img-upsell" src="<?= url("uploads/produtos/" . $foto); ?>" width="100%">
                    </div>

                    <div class="col-md-7" style="text-align:center">

                        <?= $upsell->subheadline ?><br>
                        <strong>
                            <!-- ?= $venda->dataProduto()->nome ? -->
                            <?= $upsell->plano ? "" . $upsell->dataPlano()->nome : "" ?>
                        </strong>
                        por apenas


                        <?php
                        function juros1($n, $i, $capital)
                        {
                            if ($n == 1) {
                                return $capital;
                            } elseif ($n == 2) {
                                $valor = $capital + ($capital * 0.0519);
                                $valor /= $n;
                                return $valor;
                            } else {
                                $t = 1 + $i;
                                $exp = pow($t, (-$n));
                                $juros = ($i / (1 - $exp)) * $capital;
                                return $juros;
                            }
                        }

                        function jurosAdquirente($precoProd, $taxa_adq)
                        {
                            // for ($x = 1; $x < 13; $x++) {
                            // $parcela = $x;
                            $valor = @$precoProd + (@$precoProd * $taxa_adq / 100);

                            return $valor / 12;
                            // $total = number_format($valor / $parcela, 2, ",", ".");
                            // }
                        }

                        if ($upsell->plano) :
                            $produto = $upsell->dataPlano()->nome;
                            $preco = $upsell->dataPlano()->preco;
                            $plano1 = $upsell->plano;
                        else :
                            $produto = $upsell->dataProduto()->nome;
                            $preco = $upsell->dataProduto()->preco;
                        endif;

                        if ($venda->forma_pagamento == 1) :
                            echo " 12x de:";
                            if ($checkout->adquirente == "redrocket") :
                                $preco_parcelado = juros1(12, 0.0299, $preco);
                            else :
                                $taxa_adq[12] = @$checkout->dataAdquirente()->taxa12 > 0.00 ? @$checkout->dataAdquirente()->taxa12 : 0.00;
                                $preco_parcelado = jurosAdquirente($preco, $taxa_adq[12]);
                            endif;
                        else :
                            $preco_parcelado = $preco;
                        endif;
                        ?>

                        <div class="row txt-preco">
                            <div class="col-md-12">
                                R$ <?= number_format($preco_parcelado, 2, ",", "."); ?>
                            </div>
                        </div>

                        <!-- DADOS VENDA UPSELL -->
                        <?php
                        $pagamento = $_SESSION["pagamento"];

                        $url_checkout   = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
                        $valor_liquido = $preco - @$valor_cupom;
                        $taxa_juros = $taxa->parcelamento;
                        $taxa_redrocket = $venda->dataProdutor()->taxa_redrocket;
                        $valor_redrocket = ($valor_liquido * @$comUser) / 100;
                        $valor_liquido = $valor_liquido - (($valor_liquido * @$comUser) / 100);
                        $st4rt = $upsell->dataProduto()->st4rt;
                        ?>

                        <form action="">
                            <input type="hidden" name="preco" id="valor_produto" value="<?= $preco ?>">
                            <input type="hidden" name="adquirente" id="adquirente" value="<?= $checkout->adquirente ?>">
                            <input type="hidden" name="valor_produto" id="valor_produto" value="<?= $preco ?>">
                            <input type="hidden" name="valor_liquido" id="valor_liquido" value="<?= $valor_liquido ?>">
                            <input type="hidden" name="produto" id="produto" value="<?= $pagamento["produto"] ?>">
                            <input type="hidden" name="plano" id="plano" value="<?= @$plano1 ?>">
                            <input type="hidden" name="taxa_redrocket" id="taxa_redrocket" value="<?= $taxa_redrocket ?>">
                            <input type="hidden" name="valor_redrocket" id="valor_redrocket" value="<?= $valor_redrocket ?>">
                            <input type="hidden" name="taxa_juros" id="taxa_juros" value="<?= $taxa_juros ?>">
                            <input type="hidden" name="valor_desconto" id="valor_desconto" value="<?= @$valor_cupom ?>">
                            <input type="hidden" name="url_checkout" value="<?= $url_checkout; ?>">
                            <input type="hidden" name="upsell" value="<?= $venda->id; ?>">
                            <input type="hidden" name="st4rt" value="<?= @$st4rt; ?>">

                            <?php if (@$coprodutores) : ?>
                                <input type="hidden" name="coprodutores" id="coprodutores" value="<?= @$coprodutores ?>">
                                <input type="hidden" name="comissao_coprodutores" id="comissao_coprodutores" value="<?= $comissao_coprodutor; ?>">
                            <?php endif; ?>

                            <?php
                            if (@$cookies->afiliado) :
                                if ($cookies->tipo_comissao == 0) {
                                    $comissao = $valor_liquido * ($cookies->comissao / 100);
                                } else if ($cookies->tipo_comissao == 1) {
                                    $comissao = $cookies->comissao;
                                }
                                $comissao = number_format($comissao, 2);
                            ?>
                                <input type="hidden" name="afiliado" value="<?= @$pagamento["afiliado"] ?>">
                                <input type="hidden" name="comissao_afiliado" value="<?= @$comissao ?>">
                            <?php endif; ?>

                            <?php
                            $resto = $valor_liquido - @$somaCoprodutor - @$comissao;
                            echo "<!-- valor liquido = $valor_liquido | soma = R$" . @$somaCoprodutor . " | afiliado = " . @$comissao . " | resto = " . @$resto . " -->";
                            ?>
                            <input type="hidden" name="produtor" id="produtor" value="<?= $pagamento["produtor"] ?>">
                            <input type="hidden" name="comissao_produtor" value="<?= @$resto; ?>">

                            <?php if (@$pagamento["campanha"]) : ?>
                                <input type="hidden" name="campanha" value="<?= @$pagamento["campanha"] ?>">
                            <?php endif; ?>

                            <input type="hidden" name="nome" id="nome" value="<?= $venda->dataEndereco()->nome ?>">
                            <input type="hidden" name="email" id="email" value="<?= $venda->dataEndereco()->email ?>">
                            <input type="hidden" name="nasc" id="nasc" value="<?= $pagamento["nasc"] ?>">
                            <input type="hidden" name="cpf" id="cpf" value="<?= $venda->dataEndereco()->cpf_cnpj ?>">
                            <input type="hidden" name="telefone" id="telefone" value="<?= $venda->dataEndereco()->telefone ?>">
                            <input type="hidden" name="cidade" id="cidade" value="<?= $venda->dataEndereco()->cidade ?>">
                            <input type="hidden" name="bairro" id="bairro" value="<?= $venda->dataEndereco()->bairro ?>">
                            <input type="hidden" name="rua" id="rua" value="<?= $venda->dataEndereco()->rua ?>">
                            <input type="hidden" name="numero" id="numero" value="<?= $venda->dataEndereco()->numero ?>">
                            <input type="hidden" name="complemento" id="complemento" value="<?= $venda->dataEndereco()->complemento ?>">
                            <input type="hidden" name="cep" id="cep" value="<?= $venda->dataEndereco()->cep ?>">
                            <input type="hidden" name="estado" id="estado" value="<?= $venda->dataEndereco()->estado ?>">
                            <input type="hidden" name="nomekit" id="nomekit" value="<?= $produto ?>">

                            <input type="hidden" name="pagamento" id="pagamento" value="<?= $pagamento["pagamento"] ?>">

                            <?php if ($pagamento["pagamento"] == "credit_card") : ?>
                                <input type="hidden" name="numcart" id="numcart" value="<?= @$pagamento["numcart"] ?>">
                                <input type="hidden" name="mes-val" id="mes-val" value="<?= @$pagamento["mes-val"] ?>">
                                <input type="hidden" name="ano-val" id="ano-val" value="<?= @$pagamento["ano-val"] ?>">
                                <input type="hidden" name="cvv" id="cvv" value="<?= @$pagamento["cvv"] ?>">
                                <input type="hidden" name="titular" id="titular" value="<?= @$pagamento["titular"] ?>">

                                Escolha o parcelamento:
                                <div style="width: 50%; margin: 0 auto">
                                    <?php
                                    // echo @$upsell->dataPlano()->id;
                                    if (@$checkout->adquirente == "redrocket") {
                                        if (@$upsell->dataPlano()->assumir_juros) {
                                            include "parcelamento-sem-juros.php";
                                        } else {
                                            include "parcelamento.php";
                                        }
                                    } else {
                                        include "parcelamento-chkTransp.php";
                                    }
                                    ?>
                                </div>
                            <?php elseif ($pagamento["pagamento"] == "boleto") : ?>
                                <input type="hidden" name="vencimento" id="vencimento" value="<?= @$pagamento["vencimento"] ?>">
                            <?php else : ?>
                                <input type="hidden" name="pagamento" id="pagamento" value="boleto">
                            <?php endif; ?>
                        </form>
                        <!-- FIM DADOS VENDA UPSELL -->

                        <div class="row">
                            <div class="col-md-12" style="text-align:center; margin-top:20px">
                                <button class='btn-oferta' id="comprar" onclick="salvar()">
                                    Sim, Quero esta oferta! <i class="fas fa-check-double" style="color: #fff"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row" style=" margin-top:30px">
                            <div class="col-md-12" style="text-align:center">
                                <img src="<?= url("pagamentos/imgs/compra-segura.png") ?>" width="200px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL UPSELL -->

            <div class="modal fade" id="modalUpsell">
                <div class="modal-dialog">
                    <div class="modal-content borda-bloco-upsell">
                        <div class="modal-header novo-upsell">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="modal-title"><?= $upsell->headline ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="row sub-headline-upsell">
                            <?= $upsell->subheadline ?>
                        </div>

                        <div class="row imagem-upsell">
                            <img class="img-produto" src="<?= url("uploads/produtos/" . $foto); ?>" width="100%">
                        </div>

                        <div class="modal-body">
                            <div class="row" style="text-align:center">
                                <div class="col-md-12">
                                    <strong>
                                        <!-- ?= $venda->dataProduto()->nome ? -->
                                        <?= $upsell->plano ? "" . $upsell->dataPlano()->nome : "" ?>
                                    </strong>
                                    por apenas

                                    <?php
                                    if ($venda->forma_pagamento == 1) :
                                        echo "R$ " . number_format($preco, 2, ",", ".");

                                        echo "  ou 12x de:";
                                        if ($checkout->adquirente == "redrocket") :
                                            $preco_parcelado = juros1(12, 0.0299, $preco);
                                        else :
                                            $taxa_adq[12] = @$checkout->dataAdquirente()->taxa12 > 0.00 ? @$checkout->dataAdquirente()->taxa12 : 0.00;
                                            $preco_parcelado = jurosAdquirente($preco, $taxa_adq[12]);
                                        endif;
                                    else :
                                        $preco_parcelado = $preco;
                                    endif;
                                    ?>


                                    <!-- < ?php if ($pagamento["pagamento"] == "credit_card") : ?>
                                        <br>
                                        < !-- Escolha o parcelamento: -->
                                    <!-- <form> -->
                                    <!-- <div style="width: 50%; margin: 0 auto"> -->
                                    <!-- < ?php -->
                                    <!-- // echo @$upsell->dataPlano()->id; -->
                                    <!-- // if (@$upsell->dataPlano()->assumir_juros) { -->
                                    <!-- //     include "parcelamento-sem-juros.php"; -->
                                    <!-- // } else { -->
                                    <!-- //     include "parcelamento.php"; -->
                                    <!-- // } -->
                                    <!-- ?> -->
                                    <!-- </div> -->
                                    <!-- </form> -->
                                    <!-- < ?php endif; ?> -->

                                    <div class="row txt-preco">
                                        <div class="col-md-12">
                                            R$ <?= number_format($preco_parcelado, 2, ",", "."); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12" style="text-align:center; margin-top:20px">
                                            <button class='btn-oferta' id="comprar" onclick="salvar()">
                                                Sim, Quero esta oferta! <i class="fas fa-check" style="color: #fff"></i>
                                            </button>
                                            <br><br>
                                            <button type="button" class="btn-nao-obrigado" data-dismiss="modal">Não, obrigado.</button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FIM MODAL UPSELL -->
        <?php endif ?>
    <?php endif ?>
<?php endif ?>
<!-- FIM UPSELL -->
</div>
<?php endif ?>

<div style="float: right;">
    <?php if (@$referenciaAf || @$referenciaPd) : ?>
        <script>
            function referencia() {

                alert("<?= @$referenciaPd ?>" + "\n" + "<?= @$referenciaAf ?>");
            }
        </script>

        <small onclick="referencia()">Ref.: pixel</small>
    <?php endif ?>
</div>

<?php
$obrigado = array(
    1 => $venda->status_transacao == 4 ? $venda->dataProduto()->obrigado_cartao : $venda->dataProduto()->obrigado_analise,
    2 => $venda->dataProduto()->obrigado_boleto,
    3 => $venda->dataProduto()->obrigado_analise,
);
?>

<!-- <a class="btn btn-outline-primary" data-toggle="modal" data-target="#modalBoleto" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </a> -->
<?php
$mostrarObrigado = ($venda->status_transacao != 5 && $venda->status_transacao != 6 && $venda->status_transacao != 7);
$srcObrigado = @$obrigado[$venda->forma_pagamento];
?>

<?php if ($mostrarObrigado && !empty($srcObrigado)) : ?>
    <div style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: white;
        z-index: 9999;
        overflow: hidden;
    ">
        <iframe src="<?= $srcObrigado; ?>" frameborder="0" width="100%" height="100%" style="border: none;"></iframe>
    </div>
<?php endif; ?>

<!-- LOAD -->
<div class="ajax_load">
    <div class="ajax_load_box">
        <div class="ajax_load_box_circle"></div>
        <div class="ajax_load_box_title">Aguarde, carregando!</div>
    </div>
</div>
<!-- FIM LOAD -->


<?php if ($venda->forma_pagamento == 2) : ?>
    <!-- <?= $venda->forma_pagamento . $venda->id ?> -->
    <!-- excluir plano -->
    <div class="modal fade" id="modalBoleto">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Dados do boleto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            Pedido <br> <b><?= $venda->id ?></b>
                        </div>

                        <div class="col-md-3">
                            Código da transação <br> <b><?= $venda->transacao ?></b>
                        </div>

                        <div class="col-md-3">
                            Data de vencimento <br> <b><?= formataData($venda->data_vencimento) ?></b>
                        </div>

                        <div class="col-md-3">
                            <?php if (@$venda->linha_digitavel) : ?>
                                <button type="button" class="form-control btn btn-info" id="copiar">COPIAR CÓDIGO</button>
                                <a href="<?= $venda->link_boleto ?>" target="_blank" class="form-control btn btn-outline-primary" id="copiar">IMPRIMIR BOLETO</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (@$venda->linha_digitavel) : ?>
                                <div style="overflow: auto;"><?= $generator->getBarcode($venda->linha_digitavel, $generator::TYPE_CODE_128, 1, 68); ?></div>
                                <br><input type="text" id="inp-copiar" class="form-control" value="<?= $venda->linha_digitavel ?>" readonly>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    <img src="<?= url("pagamentos/imgs/compra-segura.png") ?>" alt="COMPRA SEGURA - COMPRE COM TOTAL SEGURANÇA" width="200px">
                </div>
            </div>
        </div>

    </div>
    <!-- end excluir plano -->
<?php endif; ?>

<!-- <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script> -->
<script type="text/javascript" src="<?= url("pagamentos/js/qrcode.min.js?" . time()); ?>"></script>
<script>
    if (<?= $forma_pagamento ?> != 2 && upsell == 1)
        $('#modalUpsell').modal('show');

    if (<?= $forma_pagamento ?> == 2) {
        $('#modalBoleto').modal('show');
    }

    $("#copiar, #inp-copiar").click(() => {
        $("#inp-copiar").select();
        var copiar = document.execCommand('copy');
    });

    $("#inp-copiar2").click(() => {
        $("#inp-copiar2").select();
        var copiar = document.execCommand('copy');
    });

    $("#comprar").click((e) => {
        e.preventDefault();
    });

    $("input[name=frete]").change(() => {
        var frete = parseFloat($("input[name=frete]:checked").val());
        var capital = parseFloat("<?= @$preco ?>");
        var taxa_juros = parseFloat("<?= @$taxa_juros ?>");
        var valor_cupom = parseFloat("<?= @$valor_cupom ?>");
        $("#capital").val(frete + capital);
        var preco = frete + capital;

        $.ajax({
            url: "<?= url("pagamentos/parcelamento.php") ?>",
            type: "POST",
            data: {
                preco: preco,
                taxa_juros: taxa_juros,
                valor_cupom: valor_cupom
            },
            success: function(callback) {
                console.log(callback);
                $("#parcela").html(callback);
            }
        });
    });

    function salvar() {
        var form = $("form");

        $.ajax({
            <?php if (@$checkout->adquirente != "redrocket") {
                $url = $router->route("checkout.transacaoTransparente");
            } else {
                $url = $router->route("checkout.transacao");
            }
            ?>
            url: "<?= $url ?>",
            data: form.serialize(),
            type: "POST",
            beforeSend: function() {
                console.log("atualizando");
                load('open', 20000);
            },
            error: function(err) {
                console.log(err, "O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log(callback);

                function data(callback) {
                    try {
                        return JSON.parse(callback);
                    } catch (error) {
                        return false;
                    }
                }

                var data = data(callback);

                console.log(data);

                if (data.msg == "Venda salva com sucesso" && data.status_transacao) {
                    alert("Pedido realizado com sucesso");
                    if (data.venda) {
                        location.href = "<?= url("checkout/finalizar-compra/") ?>" + data.venda;
                    } else {
                        location.href = "<?= url("checkout/finalizar-compra/") ?>";
                    }
                } else if (data.erros) {
                    alert(data.erros);
                } else {
                    alert("Faça login para ver os detalhes dos seus pedidos");
                    location.href = "<?= url("checkout/finalizar-compra/") ?>";
                }

            }
        });
    }

    function load(action, tempo = 1000) {
        var load_div = $(".ajax_load");
        if (action === "open") {
            load_div.fadeIn().css("display", "flex");
            setTimeout(() => {
                load_div.fadeOut();
            }, tempo);
        } else {
            load_div.fadeOut();
        }
    }
</script>

<?php $v->start("scripts") ?>
<script>
    var backRedirect = "<?= url("checkout/redirect?redirect=$venda->url_checkout") ?>";

    function createBackRedirect() {

        var e = {
            octa: "redrocket"
        };

        document.cookie = "backRedirect=true",
            history.replaceState(e, document.title, window.location.pathname + "#/back-redirect"),
            history.pushState(e, document.title, window.location.pathname)

    }

    createBackRedirect(),
        window.onpopstate = function(e) {

            "#/back-redirect" === window.location.hash && setTimeout(function() {
                window.location.href = backRedirect
            }, 0)
        };
</script>
<?php $v->end() ?>
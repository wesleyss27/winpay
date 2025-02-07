<?php

use Source\App\Facebook\AppFacebook;
use Source\Models\Pixel\Pixel;

include __DIR__ . "/../produtos/tipo.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <link rel="icon" href="<?= url("uploads/ecommerce/$loja->logo"); ?>">

    <link rel="stylesheet" href="<?= url("pagamentos/css/bootstrap.css"); ?>">
    <!-- <script src="<?= url("pagamentos/js/jquery.slim.js"); ?>"></script> -->
    <script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js") ?>"></script>
    <script src="<?= url("themes/tema1/plugins/jquery-ui/jquery-ui.min.js") ?>"></script>
    <script src="<?= url("pagamentos/js/popper.min.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/bootstrap.js"); ?>"></script>

    <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">

    <!-- COOKIES PRODUTOR -->
    <?php
    if (@$loja->pixel_facebook) :
        $pixels = explode(",", $loja->dataPixelFacebook()->id_pixel);
        foreach ($pixels as $pixel) :
            if (@$loja->dataPixelFacebook()->executar_checkout == 1) :
                echo "
                    <iframe src='" . url("checkout/pixel?pixel=$pixel") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                ";
            endif;
        endforeach;
    endif;

    if (@$loja->pixel_taboola) :
        $pixels = explode(",", $loja->dataPixelTaboola()->id_pixel);
        $eventoTaboola = explode(",", $loja->dataPixelTaboola()->evento_taboola);
        $index = 0;
        foreach ($pixels as $pixel) :
            $eventos = @$eventoTaboola[$index];
            if (@$loja->dataPixelTaboola()->executar_checkout == 1) :
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
    if (@$loja->pixel_google_analitics) :
        $google = explode(",", $loja->dataPixelGoogleAnalitics()->id_pixel);
        foreach ($google as $google) :
            if (@$loja->dataPixelGoogleAnalitics()->executar_checkout == 1) :
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

    if (@$loja->pixel_google_ads) :
        $google = explode(",", $loja->dataPixelGoogleAds()->id_pixel);
        foreach ($google as $google) :
            if (@$loja->dataPixelGoogleAds()->executar_checkout == 1) :
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

    if (@$loja->pixel_google_tag_manager) :
        $google = explode(",", $loja->dataPixelGoogleTagManager()->id_pixel);
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

    if (@$loja->pixel_tiktok) :
        $tiktok = explode(",", $loja->dataPixelTikTok()->id_pixel);
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
    <!-- FIM COOKIES PRODUTOR -->
</head>

<body>
    <!-- PIXEL PRODUTORES -->
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
    if (@$checkout->pixel_tiktok) :
        $tiktok = explode(",", @$checkout->dataPixelTikTok()->id_pixel);
        foreach ($tiktok as $tiktok) :
            echo "<script>ttq.instance('$tiktok').track('PageView')</script>";
        endforeach;
    endif;
    ?>
    <?php include "estilo-loja.php" ?>

    <div class="container-fluid">
        <div class="row menu-header">
            <div class="col-md-1 col-2">
                <a href="<?= url("loja/$loja->url_loja"); ?>">
                    <img class="logo-loja" src="<?= url("uploads/ecommerce/$loja->logo"); ?>" alt="">
                </a>
            </div>

            <div class="col-md-4 col-8">
                <a href="<?= url("loja/$loja->url_loja"); ?>">
                    <h1 class="texto-tarja titulo-loja"><?= $loja->nome_loja ?></h1>
                </a>
            </div>

            <div class="col-1 texto-tarja menu-mobile"><i class="fas fa-bars"></i></div>

            <div class="col-md-3 secao-pesquisa">
                <form action="#">
                    <div class="input-group pesquisa">
                        <input type="hidden" name="usuario" value="<?= $loja->usuario ?>">
                        <input type="text" name="pesquisa" id="pesquisa" class="inp-pesquisa" placeholder="Buscar produto">

                        <button class="btn-busca"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="col-md-4 menu-list texto-tarja">
            <a class="text-white" href="<?= url("loja/$loja->url_loja") ?>"><span class="item">Início</span></a>
                <?php if (@$categorias) : ?>

                    <span class="item-active">
                        <span class="item">Categorias</span>
                        <span class="submenu">
                            <?php foreach (@$categorias as $cat) : ?>
                                <a class="text-secondary" href="<?= url("loja/$loja->url_loja/" . @$cat['url']) ?>"><span class="submenu-item"><?= @$cat['nome'] ?></span></a>
                            <?php endforeach; ?>
                        </span>
                    </span>
                <?php endif; ?>
                <a class="text-white" href="<?= url("loja/$loja->url_loja#sobre") ?>"><span class="item">Sobre</span></a>
                <a class="text-white" href="#contato"><span class="item">Contato</span></a>
            </div>
        </div>
    </div>

    <?= $v->section("content"); ?>

    <div class="secao-footer bg-tarja" id="contato">
        <footer class="container texto-tarja">
            <div class="row">
                <div class="col-md-4">
                    <a href="<?= url("loja/$loja->url_loja"); ?>">
                        <img class="logo-loja-footer" src="<?= url("uploads/ecommerce/$loja->logo"); ?>" alt="" width="40px" height="40px">

                        <b class="texto-tarja"><?= $loja->nome_loja ?></b><br><br>
                    </a>

                    Email: <b><?= $loja->email_loja ? $loja->email_loja : $loja->dataUsuario()->email ?></b><br>
                    Contato: <b><?= $loja->telefone_loja ? $loja->telefone_loja : $user->telefone ?></b><br><br>
                </div>

                <div class="col-md-4 texto-tarja">
                    <?php if ($loja->cep_loja) : ?>
                        <b>Endereço:</b><br>
                        <?= $loja->rua_loja ?>, <?= $loja->numero_loja ?> - <?= $loja->complemento_loja ?><br>
                        <?= $loja->bairro_loja ?> - <?= $loja->cidade_loja ?> / <?= $loja->estado_loja ?>
                        <br><br>
                    <?php endif ?>

                    <a class="texto-tarja" href="<?= url("termos-uso") ?>">Termos de uso</a><br>
                    <a class="texto-tarja" href="<?= url("politica-privacidade") ?>">Política de privacidade</a>
                </div>

                <div class="col-md-3">
                    <b>Formas de pagamento</b>
                    <div class="card">
                        <img src="<?= url("pagamentos/imgs/pagamentos.png") ?>" alt="" width="100%">
                    </div>

                    <br><b>Site Seguro</b> <br>
                    <img src="<?= url("themes/tema1/images/seguro.png") ?>" alt="" width="120px">
                </div>
            </div>
        </footer>
    </div>

    <?= $v->section("scripts"); ?>

</body>

</html>
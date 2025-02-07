<?php

session_start();
$_SESSION["enviado"] = 0; // PARA SABER SE O EMAIL FOI ENVIADO

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
$nomeProduto        = $plano->nome;
$produtor           = $plano->usuario;

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

$taxa_octapay       = $taxa_transacao;
$valor_octapay      = number_format((@$preco * (@$taxa_octapay / 100)) + 1, 2, ".", "");
$valor_liquido      = @$preco - @$valor_octapay;

// DATA DE NASCIMENTO
$data = mt_rand(strtotime("1970-01-01"), strtotime("2000-01-01"));
$date = new DateTime();
$date->setTimestamp($data);
$nascimento = $date->format('Y-m-d');

if ($checkout->vencimento_boletos)
    $data_vencimento = date("Y-m-d", strtotime("+$checkout->vencimento_boletos days"));
// FIM DEFINIÇÃO DE VARIÁVEIS

// COOKIES BANCO DE DADOS
if (@$cookies) {
    // var_dump($cookies);

    $afiliado = $cookies->afiliado;

    if (@$cookies_televendas != "" && @$produto->comissao_afiliado_televendas != 0) {
        if (@$produto->tipo_comissao_afiliado == 0)
            $comissao = $valor_liquido * ($produto->comissao_afiliado_televendas / 100);
        else
            $comissao = $produto->comissao_afiliado_televendas;
    } else if (@$cookies_televendas != "" && @$produto->comissao_afiliado_televendas == 0) {
        $comissao = 0;
    } else if ($idPlano != "" && $plano->habilitar_comissao_afiliado == 1 && $cookies_televendas == "") {
        if ($plano->tipo_comissao_afiliado == 0)
            $comissao = $valor_liquido * ($plano->comissao_afiliado / 100);
        else
            $comissao = $plano->comissao_afiliado;
    } else if ($cookies->tipo_comissao == 0) {
        $comissao = $valor_liquido * ($cookies->comissao / 100);
    } else if ($cookies->tipo_comissao == 1) {
        $comissao = $cookies->comissao;
    }

    $comissao_assinatura = ($plano->preco - $valor_octapay) * ($plano->comissao_afiliado_assinatura / 100);
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
}

$com = @$comissao ? $comissao : 0;
$resto = $valor_liquido - @$somaCoprodutor - @$com;
echo "<!-- valor liquido = $valor_liquido | soma = R$" . @$somaCoprodutor . " | afiliado = " . @$com . " | resto = " . @$resto . " -->";

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
    <title><?= $title; ?></title>

    <link rel="icon" href="<?= url("pagamentos/imgs/icone-branco.png"); ?>">

    <link rel="stylesheet" href="<?= url("pagamentos/css/bootstrap.css"); ?>">
    <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/fontawesome.min.css "-->

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

    <script src="<?= url("pagamentos/js/jquery.slim.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/popper.min.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/bootstrap.js"); ?>"></script>

    <link rel="stylesheet" href="<?= url("pagamentos/css/style.css?" . time()); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">

    <!-- COOKIES AFILIADOS -->
    <?php
    $_SESSION["checkout"] = $checkout;

    if (@$facebook) :
        $facebookPixels = explode(",", @$facebook->id_pixel);
        foreach ($facebookPixels as $pixel) :
            if (@$facebook->executar_checkout == 1) :
                $eventos = ["InitiateCheckout"];
                echo "
                            <iframe src='" . url("checkout/pixel?pixel=$pixel&ev=InitiateCheckout") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
            else :
                echo "
                            <iframe src='" . url("checkout/pixel?pixel=$pixel") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
            endif;
        endforeach;
    endif;
    if (@$taboola) :
        $taboolaPixels = explode(",", @$taboola->id_pixel);
        $eventos = explode(",", @$taboola->evento_taboola);
        $i = 0;
        foreach ($taboolaPixels as $pixel) :
            if (@$taboola->executar_checkout == 1) :
                echo "
                            <iframe src='" . url("checkout/pixel-taboola?pixel=$pixel&ev=" . @$eventos[$i]) . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
                $i++;
            else :
                echo "
                            <iframe src='" . url("checkout/pixel-taboola?pixel=$pixel") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
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
    ?>
    <!-- FIM COOKIES AFILIADOS -->

    <!-- COOKIES PRODUTOR -->
    <?php
    if (@$checkout->pixel_facebook) :
        $pixels = explode(",", $checkout->dataPixelFacebook()->id_pixel);
        foreach ($pixels as $pixel) :
            if (@$checkout->dataPixelFacebook()->executar_checkout == 1) :
                $eventos = ["InitiateCheckout"];
                echo "
                            <iframe src='" . url("checkout/pixel?pixel=$pixel&ev=InitiateCheckout") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
            else :
                echo "
                            <iframe src='" . url("checkout/pixel?pixel=$pixel") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
            endif;
        endforeach;
    endif;

    if (@$checkout->pixel_taboola) :
        $pixels = explode(",", $checkout->dataPixelTaboola()->id_pixel);
        $eventoTaboola = explode(",", $checkout->dataPixelTaboola()->evento_taboola);
        $index = 0;
        foreach ($pixels as $pixel) :
            $eventos = @$eventoTaboola[$index];
            if (@$checkout->dataPixelTaboola()->executar_checkout == 1) :
                $index++;
                echo "
                            <iframe src='" . url("checkout/pixel-taboola?pixel=$pixel&ev=$eventos") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
            else :
                echo "
                            <iframe src='" . url("checkout/pixel-taboola?pixel=$pixel") . "' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                        ";
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
    ?>
    <!-- FIM COOKIES PRODUTOR -->

    <!-- GOOGLE ANALITICS OCTAPAY -->
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
    <!-- GOOGLE ANALITICS OCTAPAY -->
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

    <?= $v->section("content"); ?>
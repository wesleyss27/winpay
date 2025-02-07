<?php

$dados = $_GET;

session_start();
$_SESSION["enviado"] = 0; // PARA SABER SE O EMAIL FOI ENVIADO

if ($dados == false) {
    echo $mensagem;
    exit;
}

if (@$plano == null) {
    echo $mensagem;
    exit;
}

//echo "Desconto " . @$checkout->valor_cupom . " - " . $checkout->cupom . " ";

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

$preco          = $plano->preco;
$nomeProduto    = $plano->nome;
$produtor       = $plano->usuario;
if (@$idPlano != "") $idPlano;
$idProduto      = $produto->id;
$banner         = $checkout->banner;
$aviso          = $checkout->aviso;
$email_suporte  = $produto->email_suporte;
$taxa_juros     = $taxa->parcelamento;
$url_checkout   = $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
$codigo_checkout = $checkout->codigo;

$obrigado_boleto = $produto->obrigado_boleto;
$obrigado_cartao = $produto->obrigado_cartao;
$obrigado_analise = $produto->obrigado_analise;

$data = mt_rand(strtotime("1970-01-01"), strtotime("2000-01-01"));
$date = new DateTime();
$date->setTimestamp($data);
$nascimento = $date->format('Y-m-d');

$taxa_octapay = $taxa_transacao;
$valor_octapay = number_format((@$preco * (@$taxa_octapay / 100)) + 1, 2);
$valor_liquido = @$preco - @$valor_octapay;

if ($checkout->vencimento_boletos)
    $data_vencimento = date("Y-m-d", strtotime("+$checkout->vencimento_boletos days"));

//var_dump(@$_COOKIE);
parse_str(@$_COOKIE[$produto->codigo], $arr);
if (@$arr["produto"] == $idProduto) {
    $afiliado = $arr["afiliado"];

    if ($arr["tipo_comissao"] == 0) {
        $comissao = $valor_liquido * ($arr["comissao"] / 100);
    } else if ($arr["tipo_comissao"] == 1) {
        $comissao = $arr["comissao"];
    }
    $comissao = number_format($comissao, 2);
    //echo "<br>" . $comissao;
}

//var_dump($_COOKIE);
//echo @$_COOKIE["campanha"];
parse_str(@$_COOKIE["campanha"], $camp);
if (@$camp["produto"] == $idProduto) { // && @$camp["campanha"] == @$_GET["cp"]
    $idCamp = $camp["idCamp"];
    $campanha = $camp["campanha"];
    $id_pixel = $camp["id_pixel"];
    $plataforma = $camp["plataforma"];
    $executar_checkout = $camp["executar_checkout"];
    $boleto = $camp["boleto"];
    $analytics = $camp["analytics"];

    //echo "<br>".$id_pixel;
}
//var_dump(@$camp);
parse_str(@$_COOKIE["rastro"], $rastro);

$src          = @$rastro["src"] != "" ? @$rastro["src"] : @$_GET["src"];
$utm_source   = @$rastro["utm_source"] != "" ? @$rastro["utm_source"] : @$_GET["utm_source"];
$utm_campaign = @$rastro["utm_campaign"] != "" ? @$rastro["utm_campaign"] : @$_GET["utm_campaign"];
$utm_medium   = @$rastro["utm_medium"] != "" ? @$rastro["utm_medium"] : @$_GET["utm_medium"];
$utm_content  = @$rastro["utm_content"] != "" ? @$rastro["utm_content"] : @$_GET["utm_content"];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>

    <link rel="icon" href="<?= url("pagamentos/imgs/icone-branco.png"); ?>">

    <link rel="stylesheet" href="<?= url("pagamentos/css/bootstrap.css"); ?>">
    <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/fontawesome.min.css "-->
    <script src="<?= url("pagamentos/js/jquery.slim.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/popper.min.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/bootstrap.js"); ?>"></script>

    <link rel="stylesheet" href="<?= url("pagamentos/css/style.css?" . time()); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">

    <?php
    $_SESSION["checkout"] = $checkout;
    if (@$plataforma == "Facebook") :
        $pixels = explode(",", $id_pixel);
        foreach ($pixels as $pixel) :
            if (@$executar_checkout == 1) :
    ?>
                <iframe src="<?= url("checkout/pixel?pixel=$pixel&ev=InitiateCheckout") ?>" frameborder="0" height="0" width="0"></iframe>

            <?php else : ?>
                <iframe src="<?= url("checkout/pixel?pixel=$pixel") ?>" frameborder="0" height="0" width="0"></iframe>
    <?php endif;
        endforeach;
    endif; ?>

    <?php
    if (@$checkout->pixel_facebook) :
        $pixels = explode(",", $checkout->dataPixelFacebook()->id_pixel);
        foreach ($pixels as $pixel) :
            if (@$checkout->dataPixelFacebook()->executar_checkout == 1) :
    ?>
                <iframe src="<?= url("checkout/pixel?pixel=$pixel&ev=InitiateCheckout") ?>" frameborder="0" height="0" width="0"></iframe>

            <?php else : ?>
                <iframe src="<?= url("checkout/pixel?pixel=$pixel") ?>" frameborder="0" height="0" width="0"></iframe>
    <?php endif;
        endforeach;
    endif; ?>

    <?php
    if (@$checkout->pixel_taboola) :
        $pixels = explode(",", $checkout->dataPixelTaboola()->id_pixel);
        foreach ($pixels as $pixel) :
            if (@$checkout->dataPixelTaboola()->executar_checkout == 1) :
    ?>
                <iframe src="<?= url("checkout/pixel-taboola?pixel=$pixel&ev=start_checkout") ?>" frameborder="0" height="0" width="0"></iframe>

            <?php else : ?>
                <iframe src="<?= url("checkout/pixel-taboola?pixel=$pixel") ?>" frameborder="0" height="0" width="0"></iframe>
    <?php endif;
        endforeach;
    endif; ?>

    <?php
    if (@$analytics) :
        $analytics = explode(",", $analytics);
        foreach ($analytics as $analytic) :
    ?>
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $analytic ?>"></script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());

                gtag('config', '<?= $analytic ?>');
            </script>
    <?php endforeach;
    endif; ?>

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




</head>

<body>


    <script>
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

    <!-- CONTADOR -->
    <?php
    //parse_str(@$_COOKIE["checkout"], $contador);
    //var_dump(@$_COOKIE[$checkout->codigo]);
    $contador = @$_COOKIE[$checkout->codigo];
    $precoantigo = $plano->preco;
    if (@$contador) {
        $preco = number_format($contador, 2);
        $valor_octapay = number_format(($preco * ($taxa_octapay / 100)) + 1, 2);
        $valor_liquido = number_format($preco - $valor_octapay, 2);
        $valor_cupom = 0;
    }
    ?>

    <?php if ($checkout->usar_contador == 1) : ?>
        <div class="contador-topo">
            <div class="container">
                <div class="contador row">
                    <div class="col-md-6">
                        <h4><?= $checkout->texto_contador ?></h4>
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
                                        //console.log(timer);
                                        if (timer <= -1) {
                                            timer = -1;
                                            <?php
                                            if ($checkout->acao_contador == 1) {
                                                $aumento = @$checkout->aumento > 0 ? $checkout->aumento : 0.01;
                                                $novopreco = $precoantigo + ($precoantigo * ($aumento / 100));
                                                setcookie($checkout->codigo, $novopreco, time() + (60 * 60 * 24), "/");
                                            }
                                            ?>

                                            <?php if (!$contador && $checkout->acao_contador == 1) : ?>
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
                                    console.log("TEMPO DO CONTADOR:", tempo);
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

    <?php if ($sandbox == 1) : ?>
        <div class="container-fluid center bg-danger">MODO SANDBOX</div>
    <?php endif; ?>

    <div class="ajax_load">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
            <div class="ajax_load_box_title">Aguarde, carregando!</div>
        </div>
    </div>

    <div class="container">
        <!-- CONTAINER -->
        <div class="row">
            <!-- HEADER -->
            <div class="col-md-12">
                <div class="header">
                    <?php if ($banner != "") : ?>
                        <img src="<?= url("uploads/checkout/" . $banner); ?>" alt="" width="100%">
                    <?php else : ?>
                        <img src="<?= url("pagamentos/imgs/banner-checkout2.jpg"); ?>" alt="" width="100%">
                    <?php endif; ?>

                    <?php if ($aviso != "") : ?>
                        <img src="<?= url("uploads/checkout/" . $aviso); ?>" alt="" width="100%">
                    <?php endif; ?>


                </div>
            </div>
        </div><!-- FIM HEADER -->

        <main>
            <!-- FORMULÁRIO -->
            <form action="<?= $router->route("checkout.transacao"); ?>" method="post" name="checkout">
                <div class="row">
                    <div class="col-md-7">
                        <!-- SEÇÃO FORMULÁRIO ENTREGA -->

                        <div class="cabecalho">
                            <!-- CABEÇALHO FORMULÁRIO ENTREGA -->
                            <h3>1 - Entrega</h3>
                        </div><!-- FIM CABEÇALHO FORMULÁRIO ENTREGA -->


                        <div class="corpo">
                            <!-- CORPO FORMULÁRIO ENTREGA -->
                            <?php if ($checkout->vencimento_boletos) : ?>
                                <input type="hidden" name="vencimento" id="vencimento" value="<?= $data_vencimento ?>">
                            <?php endif; ?>

                            <input type="hidden" name="codigo_checkout" id="codigo_checkout" value="<?= $codigo_checkout; ?>">

                            <input type="hidden" name="preco" id="preco" value="<?= $preco; ?>">
                            <input type="hidden" name="taxa_octapay" value="<?= $taxa_octapay; ?>">
                            <input type="hidden" name="valor_octapay" id="valor_octapay" value="<?= $valor_octapay; ?>">
                            <input type="hidden" name="valor_liquido" id="valor_liquido" value="<?= $valor_liquido; ?>">
                            <input type="hidden" name="nomekit" value="<?= $nomeProduto; ?>">
                            <input type="hidden" name="valor_produto" value="<?= $precoantigo; ?>">
                            <input type="hidden" name="valor_desconto" id="valor_desconto" value="<?= $valor_cupom; ?>">
                            <input type="hidden" name="so_cartao" value="<?= $so_cartao; ?>">
                            <input type="hidden" name="produtor" value="<?= $produtor; ?>">
                            <input type="hidden" name="carrinho_abandonado" id="carrinho_abandonado">
                            <input type="hidden" name="coprodutores" id="coprodutores" value="<?= $coprodutores; ?>">
                            <input type="hidden" name="email_suporte" id="email_suporte" value="<?= $email_suporte; ?>">
                            <input type="hidden" name="venda" id="venda" value="<?= $venda->id; ?>">
                            <input type="hidden" name="checkout1" value="<?= $checkout->id; ?>">

                            <?php if (@$idPlano) ?>
                            <input type="hidden" name="plano" value="<?= $idPlano; ?>">

                            <input type="hidden" name="produto" value="<?= $idProduto; ?>">
                            <input type="hidden" name="taxa_juros" value="<?= $taxa_juros; ?>">
                            <input type="hidden" name="url_checkout" value="<?= $url_checkout; ?>">
                            <input type="hidden" name="nasc" value="<?= $nascimento; ?>">
                            <input type="hidden" name="src" value="<?= @$src; ?>">
                            <input type="hidden" name="utm_source" value="<?= @$utm_source; ?>">
                            <input type="hidden" name="utm_medium" value="<?= @$utm_medium; ?>">
                            <input type="hidden" name="utm_campaign" value="<?= @$utm_campaign; ?>">
                            <input type="hidden" name="utm_content" value="<?= @$utm_content; ?>">
                            <input type="hidden" name="st4rt" value="<?= @$produto->st4rt; ?>">

                            <input type="hidden" name="obrigado_boleto" value="<?= $obrigado_boleto; ?>">
                            <input type="hidden" name="obrigado_cartao" value="<?= $obrigado_cartao; ?>">
                            <input type="hidden" name="obrigado_analise" value="<?= $obrigado_analise; ?>">

                            <?php if (@$afiliado) : ?>
                                <input type="hidden" name="afiliado" value="<?= @$afiliado; ?>">
                                <input type="hidden" name="comissao_afiliado" value="<?= @$comissao; ?>">
                            <?php endif; ?>

                            <?php if (@$idCamp) : ?>
                                <input type="hidden" name="campanha" value="<?= @$idCamp; ?>">
                            <?php endif; ?>

                            <?php if (@$plano->assumir_juros) : ?>
                                <input type="hidden" name="assumir_juros" value="<?= @$plano->assumir_juros; ?>">
                            <?php endif; ?>

                            <label for="nome"><i class="fa fa-user"></i> Nome Completo *</label>
                            <?php $nome = @$venda ? $venda->dataEndereco()->nome : "" ?>
                            <input type="text" id="nome" name="nome" placeholder="Nome Completo" class="form-control newform required" value="<?= @$nome ?>" required>

                            <label for="email"><i class="fa fa-envelope"></i> Email</label>
                            <?php $email = @$venda ? $venda->dataEndereco()->email : "" ?>
                            <input type="email" id="email" name="email" placeholder="E-mail" value="<?= @$email ?>" class="form-control newform required" required>

                            <!--input type="tel" id="telefone" name="telefone" placeholder="DDD+TELEFONE" class="form-control required" maxlength="11" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required-->
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="telefone"><i class="fa fa-phone"></i> Telefone - <small>somente números *</small></label>
                                    <input type="hidden" name="ddd" id="ddd" placeholder="DDD" class="form-control" maxlength="3" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                                    <?php $telefone = @$venda ? $venda->dataEndereco()->telefone : "" ?>
                                    <input type="tel" name="telefone" id="telefone" placeholder="DDD + Telefone" class="form-control newform" value="<?= @$telefone ?>" maxlength="11" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                                </div>
                                <div class="col-md-6 check-line">
                                    <label for="cpf"><i class="fa fa-address-card-o"></i> CPF *</label>
                                    <?php $cpf_cnpj = @$venda ? $venda->dataEndereco()->cpf_cnpj : "" ?>
                                    <input type="tel" id="cpf" name="cpf" value="<?= @$cpf_cnpj ?>" class="form-control newform required" placeholder="Somente números" maxlength="14" onblur="limpaNum(this.id);" onkeypress="return allowOnlyNumbers(event)" required>
                                    <!--i class="fa fa-check check-lente" id="cpf-ok"></i>
                                <i class="fa fa-times check-lente" id="cpf-error"></i-->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label for="cep">CEP *</label>
                                    <?php $cep = @$venda ? $venda->dataEndereco()->cep : "" ?>
                                    <input type="tel" id="cep" name="cep" value="<?= @$cep ?>" class="form-control newform newform4 required" placeholder="Somente números" maxlength="8" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                                    <span id="msg-cep"></span>
                                </div>
                                <div class="col-6"></div>
                            </div>

                            <label for="rua">Rua *</label>
                            <?php $rua = @$venda ? $venda->dataEndereco()->rua : "" ?>
                            <input type="text" id="rua" name="rua" placeholder="Endereço" value="<?= @$rua ?>" class="form-control newform required" required>

                            <div class="row">
                                <div class="col-6">
                                    <label for="numero">Número *</label>
                                    <?php $numero = @$venda ? $venda->dataEndereco()->numero : "" ?>
                                    <input type="text" id="numero" name="numero" value="<?= @$numero ?>" class="form-control newform  required" placeholder="Somente números" maxlength="6" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)" required>
                                </div>

                                <div class="col-6">
                                    <label for="complemento">Complemento</label>
                                    <?php $complemento = @$venda ? $venda->dataEndereco()->complemento : "" ?>
                                    <input type="text" id="complemento" name="complemento" value="<?= @$complemento ?>" placeholder="Casa B" class="form-control newform ">
                                </div>
                            </div>

                            <label for="bairro"><i class="fa fa-institution"></i> Bairro *</label>
                            <?php $bairro = @$venda ? $venda->dataEndereco()->bairro : "" ?>
                            <input type="text" id="bairro" name="bairro" value="<?= @$bairro ?>" class="form-control newform required" required>

                            <div class="row">
                                <div class="col-6">
                                <?php $cidade = @$venda ? $venda->dataEndereco()->cidade : "" ?>
                                <input type="text" id="cidade" name="cidade" value="<?= @$cidade ?>" class="form-control newform required" required>
                                </div>

                                <div class="col-6">
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
                        </div><!-- FIM CORPO FORMULÁRIO ENTREGA -->

                        <br>
                        <div class="corpo row">
                            <div class="col-md-6"><img src="<?= url("pagamentos/imgs/compra-segura.png") ?>" alt="COMPRA SEGURA - COMPRE COM TOTAL SEGURANÇA" width="200px"></div>
                            <div class="col-md-6">
                                <small>Pagamento processado por:</small><br>
                                <img src="<?= url("pagamentos/imgs/logo.png") ?>" alt="COMPRA SEGURA - COMPRE COM TOTAL SEGURANÇA" width="100px">
                            </div>

                        </div>

                        <br>
                        <div class="corpo row">
                            <div class="col-md-12">
                                <div class="row">
                                    <p>
                                        * Taxa de 2,99% a.m. <br>
                                        ** Os descontos ofertados não são cumulativos. Prevalecerá o maior de desconto ofertado. <br>
                                        Ao continuar nesta compra, você concorda com os Termos de Uso e Políticas de Privacidade. <br>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div><!-- FIM SEÇÃO FORMULÁRIO ENTREGA -->

            </form>

            <div class="col-md-5">
                <!-- SEÇÃO PAGAMENTOS -->
                <div class="cabecalho">
                    <!-- CABEÇALHO PAGAMENTOS -->
                    <h3>2 - Pagamento</h3>
                </div><!-- FIM CABEÇALHO PAGAMENTOS -->

                <div class="corpo">
                    <!-- CORPO PAGAMENTOS -->

                    <div>
                        <b>Resumo da Compra</b><br>
                        <b>Produto:</b> <?= $nomeProduto ?><br>
                        <?php if ($plano->quantidade != "") echo "<b>Quantidade:</b> $plano->quantidade <br>";
                        else ""; ?>
                        <b>Vendedor:</b> <?= $email_suporte ?><br>

                        <?php if ($produto->whatsapp_checkout) : ?>
                            <b>Whatsapp:</b>
                            <i class="fab fa-whatsapp" style="color: green"></i>
                            <?= $produto->whatsapp_checkout; ?><br>
                        <?php endif ?>

                        <?php if ($cupom == 1) : ?>
                            <b>Desconto:</b> -<span id="desconto"><?= "R$ " . number_format($valor_cupom, 2, ",", ".") ?></span><br>

                            <?php if ($so_cartao == 1) : ?>
                                <b>Valor no cartão:</b> <span class="valor_avista"><?= "R$ " . number_format($preco - $valor_cupom, 2, ",", ".") ?></span><br>
                                <b>Valor no boleto:</b> <span class="valor_avista"><?= "R$ " . number_format($preco, 2, ",", ".") ?></span><br>
                            <?php else : ?>
                                <b>Valor à vista:</b> <span class="valor_avista"><?= "R$ " . number_format($preco - $valor_cupom, 2, ",", ".") ?></span><br>
                            <?php endif ?>

                        <?php else : ?>
                            <b>Valor à vista:</b> <?= "R$ " . number_format($preco, 2, ",", ".") ?><br>
                        <?php endif ?>

                        <br>
                    </div>

                    <nav id="links">
                        <!-- TABS DE NAVEGAÇÃO -->
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#cartao" role="tab" aria-controls="nav-home" aria-selected="true">Cartão de Crédito</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#boleto" role="tab" aria-controls="nav-profile" aria-selected="false">Boleto</a>
                            <a class="nav-item nav-link" id="nav-pix-tab" data-toggle="tab" href="#pix-tab" role="tab" aria-controls="nav-pix" aria-selected="false">Pix</a>
                        </div>
                    </nav><!-- FIM TABS DE NAVEGAÇÃO -->

                    <!-- CONTEÚDOS TABS -->
                    <div class="tab-content" id="nav-tabContent">

                        <!-- CONTEÚDO TAB CARTÃO -->
                        <div class="tab-pane fade show active" id="cartao" role="tabpanel" aria-labelledby="nav-home-tab">
                            <span class="brand"><img src="<?= url("pagamentos/imgs/brands/mastercard.png"); ?>" alt="" width="45px" id="mastercard"></span>
                            <span class="brand"><img src="<?= url("pagamentos/imgs/brands/visa.png"); ?>" alt="" width="45px" id="visa"></span>
                            <span class="brand"><img src="<?= url("pagamentos/imgs/brands/elo.png"); ?>" alt="" width="45px" id="elo"></span>
                            <span class="brand"><img src="<?= url("pagamentos/imgs/brands/amex.png"); ?>" alt="" width="45px" id="amex"></span>
                            <span class="brand"><img src="<?= url("pagamentos/imgs/brands/hipercard.png"); ?>" alt="" width="45px" id="hipercad"></span>
                            <span class="brand"><img src="<?= url("pagamentos/imgs/brands/diners.png"); ?>" alt="" width="45px" id="diners"></span>
                            <input type="hidden" id="url_base" value="<?= url() ?>">

                            <form action="" method="post" name="cartao">
                                <div class="check-line">
                                    <label for="numcart">Número do cartão</label>
                                    <input type="tel" id="numcart" name="numcart" placeholder="Somente números" class="form-control cartao" maxlength="16" onblur="limpaNum(this.id); return validaCartao(value)" onkeypress="return allowOnlyNumbers(event)">
                                    <!--i class="fa fa-check check-lente" id="numcart-ok"></i>
                                    <i class="fa fa-times check-lente" id="numcart-error"></i-->
                                </div>

                                <div class="check-line">
                                    <label for="titular">Nome impresso no cartão</label>
                                    <input type="text" id="titular" name="titular" class="form-control cartao" onblur="validaTitular(value, this.id)">
                                    <!--i class="fa fa-check check-lente" id="titular-ok"></i>
                                    <i class="fa fa-times check-lente" id="titular-error"></i-->
                                </div>

                                <!--div class="check-line">
                                    <label for="cpf"><i class="fa fa-address-card-o"></i> CPF do titular do cartão</label>
                                    <input type="tel" id="cpf-titular" name="cpf-titular" class="form-control" placeholder="Somente números" maxlength="11" onblur="limpaNum(this.id); return validarCPF(value)" onkeypress="return allowOnlyNumbers(event)">
                                    <i class="fa fa-check check-lente" id="cpf-ok1"></i>
                                    <i class="fa fa-times check-lente" id="cpf-error1"></i>
                                </div>

                                Data de Nascimento <br>
                                <input type="date" name="nascimento" class="form-control">

                                <label for="telefone"><i class="fa fa-phone"></i> Telefone - <small>fixo ou celular</small></label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="ddd-titular" id="ddd-titular" placeholder="DDD" class="form-control" maxlength="3" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="telefone-titular" id="ddd-titular" placeholder="Telefone" class="form-control" maxlength="11" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)">
                                    </div>
                                </div-->

                                <div class="row">
                                    <div class="col-7">
                                        <label for="validade">Validade <small>mês/ano</small></label>
                                    </div>

                                    <div class="col-5">
                                        <label for="cvv">Código de segurança</label>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-7">
                                        <!-- SELECT VALIDADE -->
                                        <div class="col-40">
                                            <!-- SELECT MES -->
                                            <select name="mes-val" id="mes-val" class="form-control">
                                                <?php
                                                $mes = array(
                                                    "01", "02", "03",
                                                    "04", "05", "06",
                                                    "07", "08", "09",
                                                    "10", "11", "12"
                                                );
                                                for ($i = 0; $i < 12; $i++) :
                                                ?>
                                                    <option value="<?= $mes[$i]; ?>"><?= $mes[$i]; ?></option>
                                                <?php
                                                endfor;
                                                ?>
                                            </select>
                                        </div><!-- FIM SELECT MES -->

                                        <div class="col-60">
                                            <!-- SELECT ANO -->
                                            <select name="ano-val" id="ano-val" class="form-control cartao">
                                                <?php
                                                $ano = date('Y');
                                                for ($i = 0; $i < 16; $i++) :
                                                ?>
                                                    <option value="<?= $ano; ?>"><?php echo $ano;
                                                                                    $ano++; ?></option>
                                                <?php
                                                endfor;
                                                ?>
                                            </select>
                                        </div><!-- FIM SELECT ANO -->
                                    </div><!-- FIM SELECT VALIDADE -->

                                    <div class="col-5">
                                        <input type="text" id="cvv" name="cvv" class="form-control cartao" maxlength="5" onblur="limpaNum(this.id)" onkeypress="return allowOnlyNumbers(event)">
                                    </div>
                                </div>

                                <?php if (@$checkout->usar_frete == 1) : ?>
                                    <br>
                                    <label for="frete">
                                        <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked>
                                        Até <?= $checkout->prazo_entrega ?> dias úteis - R$
                                        <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
                                    </label>

                                    <?php if (@$checkout->frete_gratis == 1) : ?>
                                        <br>
                                        <label for="frete_gratis">
                                            <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                                            Até <?= $checkout->prazo_entrega_gratis ?> dias úteis - R$
                                            <?= number_format(0, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <br>

                                <label for="parcela">Parcelamento</label>

                                <?php
                                if ($plano->assumir_juros) {
                                    include "parcelamento-sem-juros.php";
                                } else {
                                    include "parcelamento.php";
                                }
                                ?>
                                <br>

                                <input type="hidden" name="pagamento" value="credit_card">

                                <?php if ($venda->status_transacao == 2 || $venda->status_transacao == 4 || $venda->status_transacao == 6 || $venda->status_transacao == 7 || $venda->status_transacao == 9) : ?>
                                    <button type="button" id="btn-cartao" class="btn btn-success form-control pagamento" disabled>Pagamento já efetuado</button>
                                <?php else : ?>
                                    <button type="submit" id="btn-cartao" class="btn btn-success form-control pagamento">Comprar Agora</button>
                                <?php endif; ?>
                            </form>

                            <div id="pendenciasCart">
                                <p>Preencha os dados corretamente</p>
                            </div>

                        </div><!-- FIM TAB CARTÃO -->

                        <!-- TAB BOLETO -->
                        <div class="tab-pane fade" id="boleto" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="center">
                                <img src="<?= url("pagamentos/imgs/boleto.png"); ?>" alt="" width="70%">
                            </div>
                            <p>
                                Atenção: <br>
                                1 - Boleto (somente à vista) <br>
                                2 - Pagamentos com Boleto Bancário levam até 3 dias úteis para serem compensados e então terem os produtos liberados <br>
                                3 - Depois do pagamento, fique atento ao seu e-mail para receber os dados de acesso ao produto (verifique também a caixa de SPAM) <br>
                            </p>

                            <form action="" method="post" name="boleto">
                                <input type="hidden" name="pagamento" value="boleto">

                                <?php if (@$checkout->usar_frete == 1) : ?>
                                    <label for="frete">
                                        <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked>
                                        Até <?= $checkout->prazo_entrega ?> dias úteis - R$
                                        <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
                                    </label>

                                    <?php if (@$checkout->frete_gratis == 1) : ?>
                                        <br>
                                        <label for="frete_gratis">
                                            <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                                            Até <?= $checkout->prazo_entrega_gratis ?> dias úteis - R$
                                            <?= number_format(0, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <br><br>

                                <?php if ($venda->status_transacao == 2 || $venda->status_transacao == 4) : ?>
                                    <button type="button" id="btn-boleto" class="btn btn-success form-control pagamento" disabled>Pagamento já efetuado</button>
                                <?php else : ?>
                                    <button type="submit" id="btn-boleto" class="btn btn-success form-control pagamento">Comprar Agora</button>
                                <?php endif; ?>
                            </form>

                            <div id="pendenciasBol">
                                <p>Preencha os dados corretamente</p>
                            </div>
                        </div>
                        <!-- FIM TAB BOLETO -->

                        <!-- TAB PIX -->
                        <div class="tab-pane fade" id="pix-tab" role="tabpanel" aria-labelledby="nav-pix-tab">
                            <br>
                            <div class="center">
                                <img src="<?= url("pagamentos/imgs/pix.png"); ?>" alt="" width="70%">
                            </div>
                            <br>

                            <p style="font-size: 14px">O Pix é a forma mais vantajosa para pagar à vista. O pagamento cai na hora e a sua compra é liberada mais rápido!</p>

                            <form action="" method="post" name="pix">
                                <input type="hidden" name="pagamento" value="boleto">

                                <?php if (@$checkout->usar_frete == 1) : ?>
                                    <label for="frete">
                                        <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked>
                                        Até <?= $checkout->prazo_entrega ?> dias úteis - R$
                                        <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
                                    </label>

                                    <?php if (@$checkout->frete_gratis == 1) : ?>
                                        <br>
                                        <label for="frete_gratis">
                                            <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                                            Até <?= $checkout->prazo_entrega_gratis ?> dias úteis - R$
                                            <?= number_format(0, 2, ",", ".") ?>
                                        </label>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($venda->status_transacao == 2 || $venda->status_transacao == 4) : ?>
                                    <button type="submit" id="btn-pix" class="btn btn-success form-control pagamento" disabled>Pagamento já efetuado</button>
                                <?php else : ?>
                                    <button type="submit" id="btn-pix" class="btn btn-success form-control pagamento">Visualizar QR Code</button>
                                <?php endif; ?>


                                <p id="servico-pix">Verificando Serviço Pix...</p>
                                <div id="aviso_pix" style="font-size: 14px">
                                    Se o QR Code não aparecer quando você clicar em Visualizar QR Code, verifique seus dados e tente novamente ou escolhar outra forma de pagamento.
                                </div>
                                <div id="instrucao_pix" style="font-size: 14px">
                                    Instruções: <br>
                                    1. Abra o app do seu banco ou instituição financeira e entre no ambiente Pix <br>
                                    2. Escolha a opção Pagar com QR Code e escaneie o código abaixo <br>
                                    3. Confirme as informações e finalize a compra <br>
                                </div>
                                <div id="qrcode" style="margin:15px auto;"></div>
                                <button id="btn-pix-confirmar" class="btn btn-success form-control pagamento">Finalizar Compra</button>
                            </form>
                        </div>
                        <!-- FIM TAB PIX -->

                        <div class="center">
                            <!-- IMG CADEADO SEGURANÇA -->
                            <img src="<?= url("pagamentos/imgs/cadeado.jpg"); ?>" alt="" width="70%">
                        </div><!-- FIM IMG CADEADO SEGURANÇA -->

                    </div><!-- FIM CONTEÚDOS TABS -->

                </div><!-- FIM CORPO PAGAMENTOS -->

            </div><!-- FIM SEÇÃO PAGAMENTOS -->

    </div>
    </main><!-- FIM FORMULÁRIO -->

    <div class="center">
        <br>
        <small>
            <a href="<?= url("termos-uso"); ?>">Termos de Uso</a> |
            <a href="<?= url("politica-privacidade"); ?>">Políticas de Privacidade</a>
        </small>
    </div>

    </div><!-- FIM CONTAINER -->


    <!-- SCRIPTS JAVASCRIPT -->
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="<?= url("pagamentos/js/validacao.js?" . time()); ?>"></script>
    <script type="text/javascript" src="<?= url("pagamentos/js/qrcode.js?" . time()); ?>"></script>
    <script>
        /*$("#cpf").focusout(function(e){
        e.preventDefault();
        buscaComprador($(this).val());
    });*/

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


        function buscaComprador(info) {

            $.ajax({
                url: "<?= $router->route("appusuarios.buscaComprador") ?>",
                data: {
                    info: info
                },
                type: "POST",
                //dataType: "json",
                beforeSend: function() {
                    console.log("atualizando");
                    //load('open', 5000);
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
                        //load("close");
                    } else {
                        //load("close");
                    }
                }
            });
        }

        var valor = 0;
        $("#cpf").blur(() => {
            if (valor == 0) {
                validarCPF($("#cpf").val());
                valor = 1;
            }
        });

        // ALTERAR VALOR AO TROCAR DE CEP
        function parcelamento() {
            <?php
            if ($plano->assumir_juros) {
                $url = url("pagamentos/parcelamento-sem-juros.php");
            } else {
                $url = url("pagamentos/parcelamento.php");
            }
            ?>
            var frete = parseFloat($("input[name=frete]:checked").val());
            var capital = parseFloat($("#preco").val());
            var taxa_juros = parseFloat("<?= $taxa_juros ?>");
            var valor_cupom = parseFloat($("#valor_desconto").val());
            $("#capital").val(frete + capital);

            frete = frete > 0 ? frete : 0;
            var preco = frete + capital;
            console.log("capital", capital, frete);

            $.ajax({
                url: "<?= $url ?>",
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
        }

        $("input[name=frete]").change(() => {
            parcelamento();
            // frete = parseFloat($("input[name=frete]:checked").val());
            // preco = parseFloat($("#inp_valor_boleto").val());
            // console.log("preco",preco, frete);
        });

        $("#cep").focusout(function() {
            cep($(this).val());
        });

        function cep(cep) {
            $.ajax({
                url: 'https://api.pagar.me/1/zipcodes/' + cep,

                dataType: 'json',
                success: function(resposta) {
                    //load('open');
                    $("#rua").val(resposta.street);
                    $("#bairro").val(resposta.neighborhood);
                    $("#cidade").val(resposta.city);
                    $("#estado").val(resposta.state);
                    $("#msg-cep").hide();
                    //load("close");
                },
                error: function(err) {
                    if (err.status == 404) {
                        console.log("CEP NÃO ENCONTRADO", err.status);
                        alert("CEP NÃO ENCONTRADO");
                        $("#msg-cep").html("CEP inválido");
                        $("#msg-cep").css({
                            "color": "red"
                        });
                    }
                }
            });
        }

        $("#btn-cartao").click((e) => {
            e.preventDefault();

            var data = new Date();
            var exp_cart = new Date($("#ano-val").val(), $("#mes-val").val() - 1, "01");
            var mes = (data.getMonth() + 1).toString();
            var mesF = (mes.length == 1) ? '0' + mes : mes;
            var ano = data.getFullYear();

            var nome = $("#nome").val();
            var cvv = $("#cvv").val();
            var titular = $("#titular").val();
            var numcart = $("#numcart").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();
            //var ddd = $("#ddd").val();

            var hoje = mesF + "-" + ano;
            console.log(exp_cart, hoje);

            if (numcart == "") {
                alert("Preencha o número do cartão");
                $("#numcart").focus();
            } else if (exp_cart <= new Date()) {
                alert("Data de validade do cartão vencida")
            } else if (cvv == "") {
                alert("Preencha o digito verificador do cartão");
                $("#cvv").focus();
            } else if (titular == "") {
                alert("Preencha o nome do titular do cartão");
                $("#titular").focus();
            } else if (nome == "") {
                alert("Preencha o nome");
                $("#nome").focus();
            } else if (cpf == "") {
                alert("Preencha o cpf");
                $("#cpf").focus();
            } else if (telefone == "") {
                alert("Preencha o telefone");
                $("#telefone").focus();
            } else {
                var form = $("form[name=checkout], form[name=cartao]");
                salvar(form);
            }
        });

        $("#btn-boleto").click((e) => {
            e.preventDefault();
            var nome = $("#nome").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();
            //var ddd = $("#ddd").val();
            if (nome == "") {
                alert("Preencha o nome");
                $("#nome").focus();
            } else if (cpf == "") {
                alert("Preencha o cpf");
                $("#cpf").focus();
            } else if (telefone == "") {
                alert("Preencha o telefone");
                $("#telefone").focus();
            } else {
                var form = $("form[name=checkout], form[name=boleto]");
                salvar(form);
            }
        });

        $("#email").focusout(function(e) {
            e.preventDefault();
            //buscaComprador($(this).val());
        });

        function makeCode(texto) {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                width: 200,
                height: 200
            });
            qrcode.makeCode(texto);
        }

        $("#instrucao_pix").hide();
        $("#btn-pix-confirmar").hide();
        $("#btn-pix").hide();

        $("#nav-pix-tab").click(() => {
            $.ajax({
                url: "<?= $router->route("checkout.servicoPix") ?>",
                type: "GET",
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Funcionando") {
                        $("#btn-pix").show();
                        $("#servico-pix").hide();
                    } else {
                        $("#aviso_pix").html("Serviço de pagamento do Pix está fora do ar. Tente novamente mais tarde ou escolha outra forma de pagamento");
                    }
                }
            });
        });

        $("#btn-pix").click((e) => {
            e.preventDefault();
            var nome = $("#nome").val();
            var cpf = $("#cpf").val();
            var telefone = $("#telefone").val();
            //var ddd = $("#ddd").val();
            if (nome == "") {
                alert("Preencha o nome");
                $("#nome").focus();
            } else if (cpf == "") {
                alert("Preencha o cpf");
                $("#cpf").focus();
            } else if (telefone == "") {
                alert("Preencha o telefone");
                $("#telefone").focus();
            } else {
                var form = $("form[name=checkout], form[name=pix]");
                $.ajax({
                    url: "<?= $router->route("checkout.transacaoPix") ?>",
                    data: form.serialize(),
                    type: "POST",
                    //dataType: "json",
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
                            // location.href = data.url;
                            // makeCode(data.qr_code);

                            // Clear Previous QR Code
                            $('#qrcode').empty();

                            // Set Size to Match User Input
                            $('#qrcode').css({
                                'width': 200,
                                'height': 200
                            })

                            // Generate and Output QR Code
                            $('#qrcode').qrcode({
                                width: 200,
                                height: 200,
                                text: data.qr_code
                            });

                            $("#btn-pix").hide();
                            $("#aviso_pix").hide();
                            $("#instrucao_pix").show();
                            $("#btn-pix-confirmar").show();
                            $("#btn-pix-confirmar").click((e) => {
                                e.preventDefault();
                                location.href = "<?= url("checkout/finalizar-compra/") ?>" + data.venda;
                            });
                        } else if (data.msg == "CPF inválido") {
                            alert("CPF inválido. Verifique se digitou os números do CPF corretamente e tente novamente");
                            load("close");
                        } else if (data.status_transacao) {
                            location.href = data.url;
                        } else {
                            location.href = "<?= url("checkout/processando") ?>"
                        }
                    }
                });
            }
        });

        function salvar(form) {

            console.log(form.serialize());
            console.log("<?= $router->route("checkout.transacao") ?>");

            $.ajax({
                url: "<?= $router->route("checkout.transacao") ?>",
                data: form.serialize(),
                type: "POST",
                //dataType: "json",
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
                        // location.href = data.url;
                        if (data.venda) {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>" + data.venda;
                        } else {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>";
                        }
                    } else if (data.msg == "CPF inválido") {
                        alert("CPF inválido. Verifique se digitou os números do CPF corretamente e tente novamente");
                        load("close");
                    } else if (data.status_transacao) {
                        // location.href = data.url;
                        if (data.venda) {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>" + data.venda;
                        } else {
                            location.href = "<?= url("checkout/finalizar-compra/") ?>";
                        }
                    } else {
                        /*alert("Houve um problema na solicitação. " + 
                        "\nEntre em contato pelo email suporte@octapay.com.br para verificar a situação da sua compra.");*/

                        // location.href = "<?= url("checkout/processando") ?>"
                        location.href = "<?= url("checkout/finalizar-compra/") ?>";
                    }

                }
            });
        }

        $("#links, #numcart").click((e) => {
            //e.preventDefault();
            console.log("clicou");

            var form = $("form[name=checkout]");
            cadastroCarrinho(form);

        });

        $("#cpf, #cep, #telefone").blur((e) => {
            //e.preventDefault();
            console.log("CARRINHO ABANDONADO");

            var form = $("form[name=checkout]");
            cadastroCarrinho(form);

        });

        function cadastroCarrinho(form) {

            console.log("DADOS DO CARRINHO ENVIADOS \n", form.serialize());
            console.log("ROTA DO CARRINHO ABANDONADO: <?= $router->route("abandono.cadastro") ?>");
            $.ajax({
                url: "<?= $router->route("abandono.cadastro") ?>",
                data: form.serialize(),
                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function(err) {
                    console.log(err, "O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    var data = JSON.parse(callback);
                    console.log(data);
                    if (data.id) {
                        $("#carrinho_abandonado").val(data.id);
                    }
                }
            });
        }
        //console.log("<?= url("notificacoes") ?>")
    </script>
    <!-- FIM SCRIPTS JAVASCRIPT -->

</body>

</html>
<?php 
    //session_start(); 
    $v->layout("_theme");  
    parse_str(@$_COOKIE["campanha"], $arr);
    $valor = (@$arr["valor"] ? $arr["valor"] : (@$_SESSION["pagamento"]["preco"] / 100));
    $pixel = explode(",", @$arr["id_pixel"]);
    $analytics = explode(",", @$arr["analytics"]);

    if(@$cookies){
        // var_dump($cookies);

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

    $checkout = @$_SESSION["checkout"];

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

    $v->start("facebook");

    if(@$facebook):
        $facebookPixels = explode(",", @$facebook->id_pixel);
        $valor = @$facebook->valor ? $facebook->valor : $venda->valor_liquido;
        foreach($facebookPixels as $pixel):
            echo "
                <!-- PIXEL FACEBOOK CAMPANHA -->
                <iframe src='".url("pagamentos/pixel-purchase.php?pixel=$pixel&valor=$valor")."' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
            ";
        endforeach;
    endif;
    if(@$taboola):
        $taboolaPixels = explode(",", @$taboola->id_pixel);
        $eventos = explode(",", @$taboola->purchase_taboola);
        $i = 0;
        foreach($taboolaPixels as $pixel):
            echo "
                <!-- PIXEL TABOOLA CAMPANHA -->
                <iframe src='".url("checkout/pixel-taboola?pixel=$pixel&ev=".@$eventos[$i])."' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
            ";
            $i++;
        endforeach;
    endif;
    if(@$analitics):
        $analiticsPixels = explode(",", @$analitics->id_pixel);
        foreach($analiticsPixels as $pixel):
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
    if(@$ads):
        $adsPixels = explode(",", @$ads->id_pixel);
        $conversao = explode(",", @$ads->tag_conversao);
        $i = 0;

        foreach($adsPixels as $pixel):
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
                
            if($conversao):
                echo "<!-- Event snippet for Compra conversion page -->
                    <script>
                    gtag('event', 'conversion', {
                        'send_to': '". @$coversao[$i] ."',
                        'transaction_id': '$venda->transacao'
                    });
                    </script>
                ";
            endif;
        endforeach;
    endif;
    
    if(@$tagManager):
        $tagPixels = explode(",", @$tagManager->id_pixel);
        foreach($tagPixels as $pixel):
            if(@$tagManager->executar_checkout == 1):
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
?>

<?php
    if(@$checkout->pixel_facebook): 
        $pixels = explode(",", $checkout->dataPixelFacebook()->id_pixel);
        $valor = @$checkout->dataPixelFacebook()->valor ? $checkout->dataPixelFacebook()->valor : $venda->valor_liquido;
        foreach($pixels as $pixel2):
            if(@$venda->forma_pagamento =! 2 || @$checkout->dataPixelFacebook()->boleto == 1):

                echo "
                    <!-- PIXEL FACEBOOK CHECKOUT -->
                    <iframe src='".url("pagamentos/pixel-purchase.php?pixel=$pixel2&valor=$valor")."' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                ";
            endif;
        endforeach;
    endif; 

    if(@$checkout->pixel_taboola): 
        $pixels = explode(",", $checkout->dataPixelTaboola()->id_pixel);
        $eventos = explode(",", @$checkout->dataPixelTaboola()->purchase_taboola);
        $i = 0;
        foreach($pixels as $pixel1):
            if(@$venda->forma_pagamento != 2 || @$checkout->dataPixelTaboola()->boleto == 1):
                echo "
                    <!-- PIXEL TABOOLA CHECKOUT -->
                    <iframe src='<?= url('checkout/pixel-taboola?pixel=$pixel1&ev='.@$eventos[$i]) ?>' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                ";
                $i++; 
            else:
                echo "
                    <!-- PIXEL TABOOLA CHECKOUT -->
                    <iframe src='<?= url('checkout/pixel-taboola?pixel=$pixel1') ?>' frameborder='0' height='0' width='0' style='top: -20px; position: absolute'></iframe>
                ";
            endif;
        endforeach; 
    endif; 

    if(@$checkout->pixel_google_analitics): 
        $google = explode(",", $checkout->dataPixelGoogleAnalitics()->id_pixel);
        $i = 0;
        
        foreach($google as $google):
            if(@$checkout->dataPixelGoogleAnalitics()->boleto == 1 || $venda->forma_pagamento != 2):
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

    if(@$checkout->pixel_google_ads): 
        $google = explode(",", $checkout->dataPixelGoogleAds()->id_pixel);
        $conversao = explode(",", $checkout->dataPixelGoogleAds()->tag_conversao);
        $i = 0;
        
        foreach($google as $google):
            if(@$checkout->dataPixelGoogleAds()->boleto == 1 || $venda->forma_pagamento != 2):
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
                        'send_to': '".@$coversao[$i]."',
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
    if(@$checkout->pixel_google_tag_manager):
        foreach($pixels as $pixel):
            if(@$executar_checkout == 1):
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
    ?>
<?php $v->end(); ?>

<?php
    if(@$checkout->pixel_google_tag_manager): 
        $google = explode(",", $checkout->dataPixelGoogleTagManager()->id_pixel);
        foreach($google as $google):
            echo "
                <!-- Google Tag Manager (noscript) -->
                <noscript><iframe src='https://www.googletagmanager.com/ns.html?id=<?= $google ?>'
                height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
                <!-- End Google Tag Manager (noscript) -->
            ";
        endforeach;
    endif;
?>

<?php $v->start("topo"); ?>
    <div class="topo row">
        <div class="col-md-3">
            <img src="<?= url("pagamentos/imgs/logo.png");?>" alt="" width="50%">
        </div>
        
        <div class="col-md-6">
            <h4>Olá <b><?= $venda->dataEndereco()->nome ?></b>, seu pedido está <?=  @$status[@$venda->status_transacao] ?>!</h4>
            Verifique sua caixa de email para ver os detalhes do seu pedido ou clique em entrar <br>
        </div>

        <div class="col-md-3">
            <a href="<?= url() ?>" target="_blank" class="btn btn-warning">Entrar</a><br>
        </div>
    </div>
<?php $v->end(); ?>



<div class="obrigado corpo container-fluid">
    <?php if(@$venda): ?>
    <div class="row">
        <div class="col-md-3" style="text-align: center">
            <span class="desktop">
                <img class="img-produto" src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto); ?>" alt="">
            </span>
            
            <?php if($venda->forma_pagamento != 3 && $venda->transacao != ""): ?>
            <?php if(@$upsell): ?>
            
                <?php if($venda->forma_pagamento == 2 && @$upsell->boleto == 1): ?>
                    <div style="padding: 1em 0; border-radius: 10px; background: green; color: #fff; font-family: 'Montserrat'; margin: 15px 0">
                    <h4><?= $upsell->headline ?></h4>
                    <p style="font-size: 18px"><?= $upsell->subheadline ?></p>
                    <button class="btn btn-atencao fa-blink" data-toggle="modal" data-target="#modalUpsell" style="font-family:inter" id="cadastro">Aproveitar Oferta</i> </button>
                    </div>
                <?php endif; ?>

                <?php if($venda->forma_pagamento == 1): ?>
                    <div style="padding: 1em 0; border-radius: 10px; background: green; color: #fff; font-family: 'Montserrat'; margin: 15px 0">
                    <h4><?= $upsell->headline ?></h4>
                    <p style="font-size: 18px"><?= $upsell->subheadline ?></p>
                    <button class="btn btn-atencao fa-blink" data-toggle="modal" data-target="#modalUpsell" style="font-family:inter" id="cadastro">Aproveitar Oferta</i> </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>

            <span class="mobile">
                <img class="img-produto" src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto); ?>" alt="">
            </span>
            
        </div>

        <div class="col-md-4">
            <p>Nós somos a OctaPay, empresa que gerencia os pagamentos do/a <strong><?=  $venda->dataProduto()->nome ?></strong>.</p>

            <?php if($venda->forma_pagamento != 3 && $venda->transacao == ""): ?>
                <p class="text-warning" style="text-align: center; font-size: 60px"><i class="fas fa-exclamation-triangle"></i></p><p class="text-danger"><b>Infelizmente houve um problema com a sua compra, <a href="<?= url("checkout/$checkout->codigo/$venda->id") ?>">CLIQUE AQUI</a> e tente novamente</b></p>
                <!-- <script>
                    alert("Infelizmente houve um problema com a sua compra");
                </script> -->

                <div class="alert alert-danger">
                    <?php 
                        include "erros.php";
                        // var_dump(@$_SESSION["erros"]);
                        $erros = @$_SESSION["erros"];
                        if(@$erros["comprador"]){
                            foreach ($erros["comprador"] as $comp) {
                                echo @$erros_transacao["$comp"] . "<br>";
                            }
                        }

                        if(@$erros["pedido"]){
                            foreach ($erros["pedido"] as $ped) {
                                echo @$erros_transacao["$ped"] . "<br>";
                            }
                        }

                        if(@$erros["transacao"]){
                            echo @$erros["transacao"] . "<br>";
                        }
                    ?>
                </div>
            <?php else: ?>

                <p>Seu pedido de número <strong><?=  $venda->id ?></strong>
                está <b><?=  @$status[@$venda->status_transacao] ?></b>.</p>

                <?php 
                    if (@$consultarPix["msg-motivo"]) {
                        echo "<p>Motivo cancelamento: <b>" . @$consultarPix["msg-motivo"] . "</b></p>";
                    } 
                    if (@$venda->motivo) {
                        echo "<p>Motivo cancelamento: <b>" . $venda->motivo . "</b></p>";
                    } 
                ?>

                <?php if(@$venda->forma_pagamento == 1 && $venda->status_transacao == 2): ?>
                    <p>Compras no cartão podem levar até 48 horas para serem aprovadas.</p>
                <?php elseif(@$venda->forma_pagamento == 2): ?>
                    <p>Boletos levam até 3 dias úteis para serem compensados.</p>
                    <p><a href="<?= $venda->link_boleto; ?>" class="btn btn-success" target="_blank">IMPRIMIR BOLETO</a></p>
                    <p>Linha digitável <br>
                    <input type="text" id="inp-copiar2" class="form-control" value="<?= $venda->linha_digitavel ?>" readonly></p>
                <?php elseif(@$venda->forma_pagamento == 3 && $venda->status_transacao == 1): ?>
                    <p>Se você pagou com Pix aguarde alguns minutos e recarregue a página.</p>
                <?php endif ?>
            <?php endif ?>
        </div>

        <div class="col-md-5">
            <p>Para saber os detalhes do seu pedido, clique no botão abaixo e digite seu email e senha. 
            <br> Se for seu primeiro acesso, sua senha é 123456</p>
            <p><a href="<?= url("vendas?minhas") ?>" class='btn btn-success'>Visualizar Detalhes Do Pedido</a></p>
            
            <p>Se você ainda tem dúvidas sobre o produto, fique à vontade para entrar em contato 
            no e-mail <?= $venda->dataProduto()->email_suporte ?>.</p>
            <p><a href="mailto: <?= $venda->dataProduto()->email_suporte ?>" class='btn btn-info'>Entrar em contato</a></p>
        </div>
    </div>
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

<?php if($venda->status_transacao != 5 && $venda->status_transacao != 6 && $venda->status_transacao != 7): ?>
<div class="obrigado">
    <!-- <?= $venda->forma_pagamento . $venda->id ?> -->
    <iframe class="scroll" src="<?= @$obrigado[$venda->forma_pagamento]; ?>" frameborder="0" width="100%"></iframe>
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


<?php if($venda->forma_pagamento == 2): ?>
    <!-- <?= $venda->forma_pagamento. $venda->id ?> -->
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
                        <?php if(@$venda->linha_digitavel): ?>
                        <button type="button" class="form-control btn btn-info" id="copiar">COPIAR CÓDIGO</button>
                        <a href="<?= $venda->link_boleto ?>" target="_blank" class="form-control btn btn-outline-primary" id="copiar">IMPRIMIR BOLETO</a>
                        <?php endif; ?>
                    </div>
                </div>

                <br>
                <div class="row">
                    <div class="col-md-12">
                        <?php if(@$venda->linha_digitavel): ?>
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

    <!-- MODAL UPSELL -->
    <?php if($upsell): ?>
    <div class="modal fade" id="modalUpsell">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= $upsell->headline; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="<?= url("uploads/produtos/" . $upsell->dataProduto()->foto); ?>" width="100%">
                    </div>

                    <div class="col-md-8">
                        Leve também <br>
                        <?php 
                            if(@$checkout->cupom){
                                $cupom = 1;
                                $valor_cupom = $checkout->valor_cupom;
                        
                                if($checkout->so_cartao == 1){
                                    $so_cartao = 1;
                                } else {
                                    $so_cartao = 0;
                                }
                            } else {
                                $cupom = 0;
                                $valor_cupom = 0;
                                $so_cartao = 0;
                            }

                            if($upsell->plano):
                                $produto = $upsell->dataPlano()->nome;
                                $preco = $upsell->dataPlano()->preco;
                                $plano = $upsell->plano;
                            else:
                                $produto = $upsell->dataProduto()->nome;
                                $preco = $upsell->dataProduto()->preco;
                            endif;

                            if (@$pagamento["valor_frete"] && @$pagamento["valor_frete"] == 0) {
                                $frete = "+ R$ " . number_format($checkout->valor_frete, 2, ",", ".") . " pelo frete";
                            }
                        ?>
                        
                        <span style="font-size: 24px;" class="text-success"><?= $produto; ?></span><br> por <br> 
                        R$ <span style="font-size: 28px;" class="text-success"><?= number_format($preco - $valor_cupom, 2, ",", "."); ?></span> <?= @$frete; ?><br>
                        <img src="<?= url("pagamentos/imgs/compra-segura.png") ?>" alt="COMPRA SEGURA - COMPRE COM TOTAL SEGURANÇA" width="150px">

                        <?php
                            $pagamento = $_SESSION["pagamento"];

                            $url_checkout   = $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
                            $valor_liquido = $preco - @$valor_cupom;
                            $taxa_juros = $taxa->parcelamento;
                            $taxa_octapay = $venda->dataProdutor()->taxa_octapay;
                            $valor_octapay = ($valor_liquido * $taxa->taxa_octapay) / 100;
                            $valor_liquido = $valor_liquido - (($valor_liquido * $taxa->taxa_octapay) / 100);
                            $st4rt = $upsell->dataProduto()->st4rt;
                        ?>

                        <form action="">
                            <input type="hidden" name="valor_produto" id="valor_produto" value="<?= $preco ?>">
                            <input type="hidden" name="valor_liquido" id="valor_liquido" value="<?= $valor_liquido ?>">
                            <input type="hidden" name="produto" id="produto" value="<?= $pagamento["produto"] ?>">
                            <input type="hidden" name="plano" id="plano" value="<?= @$plano ?>">
                            <input type="hidden" name="produtor" id="produtor" value="<?= $pagamento["produtor"] ?>">
                            <input type="hidden" name="taxa_octapay" id="taxa_octapay" value="<?= $taxa_octapay ?>">
                            <input type="hidden" name="valor_octapay" id="valor_octapay" value="<?= $valor_octapay ?>">
                            <input type="hidden" name="taxa_juros" id="taxa_juros" value="<?= $taxa_juros ?>">
                            <input type="hidden" name="valor_desconto" id="valor_desconto" value="<?= $valor_cupom ?>">
                            <input type="hidden" name="url_checkout" value="<?= $url_checkout; ?>">
                            <input type="hidden" name="upsell" value="<?= $venda->id; ?>">
                            <input type="hidden" name="st4rt" value="<?= @$st4rt; ?>">

                            <?php if(@$coprodutores): ?>
                            <input type="hidden" name="coprodutores" id="coprodutores" value="<?= @$coprodutores ?>">
                            <?php endif; ?>

                            <?php if(@$pagamento["afiliado"]): ?>
                            <input type="hidden" name="afiliado" value="<?= @$pagamento["afiliado"] ?>">
                            <input type="hidden" name="comissao_afiliado" value="<?= @$pagamento["comissao_afiliado"] ?>">
                            <?php endif; ?>

                            <?php if(@$pagamento["campanha"]): ?>
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

                            <?php if ($pagamento["pagamento"] == "credit_card"): ?>
                                <input type="hidden" name="numcart" id="numcart" value="<?= @$pagamento["numcart"] ?>">
                                <input type="hidden" name="mes-val" id="mes-val" value="<?= @$pagamento["mes-val"] ?>">
                                <input type="hidden" name="ano-val" id="ano-val" value="<?= @$pagamento["ano-val"] ?>">
                                <input type="hidden" name="cvv" id="cvv" value="<?= @$pagamento["cvv"] ?>">
                                <input type="hidden" name="titular" id="titular" value="<?= @$pagamento["titular"] ?>">
                                <?php include "parcelamento.php" ?>
                            <?php elseif ($pagamento["pagamento"] == "boleto"): ?>
                                <input type="hidden" name="vencimento" id="vencimento" value="<?= @$pagamento["vencimento"] ?>">
                            <?php else: ?>
                                <input type="hidden" name="pagamento" id="pagamento" value="boleto">
                            <?php endif; ?>
                            
                            <?php if (@$pagamento["valor_frete"] && @$pagamento["valor_frete"] == 0): ?>
                                <hr>
                                Escolha a opção de frete:
                                <br>
                                <label for="frete">
                                    <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked> 
                                    Até <?= $checkout->prazo_entrega ?> dias úteis - R$ 
                                    <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
                                </label>

                                <?php if(@$checkout->frete_gratis == 1): ?>
                                <br>
                                <label for="frete_gratis">
                                    <input type="radio" name="frete" id="frete_gratis" value="0" checked> 
                                    Até <?= $checkout->prazo_entrega_gratis ?> dias úteis - R$ 
                                    <?= number_format(0, 2, ",", ".") ?>
                                </label>
                                <?php endif; ?>
                                <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" id="comprar" onclick="salvar()">Comprar Agora</button>
            </div>
        </div>
    </div>
    
    </div>
    <?php endif; ?>
    <!-- END MODAL UPSELL -->

    <div class="ajax_load">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
            <div class="ajax_load_box_title">Aguarde, carregando!</div>
        </div>
    </div>

<!-- <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script> -->
<script type="text/javascript" src="<?= url("pagamentos/js/qrcode.min.js?" . time());?>"></script>
<script>
    if(<?= $venda->forma_pagamento ?> == 2){
        $('#modalBoleto').modal('show');
    }

    $("#copiar, #inp-copiar").click(()=> {
        $("#inp-copiar").select();
        var copiar = document.execCommand('copy');
    });

    $("#inp-copiar2").click(()=> {
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
            data: {preco: preco, taxa_juros: taxa_juros, valor_cupom: valor_cupom},
            success: function(callback){
                console.log(callback);
                $("#parcela").html(callback);
            }
        });
    });

    function salvar(){
        var form = $("form");

        $.ajax({
            url: "<?=$router->route("checkout.transacao")?>",
            data: form.serialize(),
            type: "POST",
            beforeSend: function () {
                console.log("atualizando");
                load('open', 20000);
            },
            error: function(err) {
                console.log(err, "O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log(callback);
                function data(callback){
                    try {
                        return JSON.parse(callback);
                    } catch (error) {
                        return false;
                    }
                }

                var data = data(callback);

                console.log(data);
                
                if(data.msg == "Venda salva com sucesso" && data.status_transacao){
                    alert("Pedido realizado com sucesso");
                    if(data.venda){
                        location.href = "<?= url("checkout/finalizar-compra/") ?>" + data.venda;
                    } else {
                        location.href = "<?= url("checkout/finalizar-compra/") ?>";
                    }
                } else if(data.erros){
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
        if(action === "open") {
            load_div.fadeIn().css("display", "flex");
            setTimeout(() => {
                load_div.fadeOut();
            }, tempo);
        }
        else {
            load_div.fadeOut();
        }
    }
</script>

<?php $v->start("scripts") ?>
<script>
    var backRedirect = "<?= url("checkout/redirect?redirect=$venda->url_checkout") ?>";
    function createBackRedirect(){
        var e = { octa: "octapay" };

        document.cookie = "backRedirect=true",
        history.replaceState(e, document.title, window.location.pathname + "#/back-redirect"),
        history.pushState(e, document.title, window.location.pathname)
    }

    createBackRedirect(),
    window.onpopstate = function(e){
        "#/back-redirect" === window.location.hash && setTimeout(function(){
            window.location.href = backRedirect
        }, 0)
    };
</script>
<?php $v->end() ?>

<?php 
    //session_start(); 
    $v->layout("_theme");  
    parse_str(@$_COOKIE["campanha"], $arr);
    $valor = (@$arr["valor"] ? $arr["valor"] : (@$_SESSION["pagamento"]["preco"] / 100));
    $pixel = explode(",", @$arr["id_pixel"]);
    $analytics = explode(",", @$arr["analytics"]);
?>

<?php $v->start("facebook"); ?>
<?php 
    if(@$arr["id_pixel"]):
    if(@$pixel):
    foreach($pixel as $pixel): 
?>
    <!-- Facebook Pixel Code -->

    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?= $pixel ?>');
    fbq('track', 'PageView');
    fbq('track', 'Purchase', {currency: "BRL", value: <?= $valor ?>});
    </script>
    <noscript>
    <img height="1" width="1" style="display:none" 
        src="https://www.facebook.com/tr?id=<?= $arr["id_pixel"] ?>&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
    <?php endforeach; endif; endif; ?>

    <?php 
        if(@$analytics):
        foreach($analytics as $analytic): 
    ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $analytic ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '<?= $analytic ?>');
        </script>

    <?php endforeach; endif; ?>
<?php $v->end(); ?>

<?php $v->start("topo"); ?>
    <div class="topo row">
        <div class="col-md-3">
            <img src="<?= url("themes/tema2/assets/imgs/logo.png"); ?>" alt="" width="50%">
        </div>
        
        <div class="col-md-6">
            <h3>Pedido aprovado com sucesso!</h3>
            Verifique sua caixa de email para ver os detalhes do seu pedido ou clique em entrar <br>
        </div>

        <div class="col-md-3">
            <a href="<?= url() ?>" target="_blank" class="btn btn-success">Entrar</a><br>
        </div>
    </div>
<?php $v->end(); ?>

<div class="obrigado">
    <iframe src="<?= $_SESSION["pagamento"]["obrigado_cartao"]; ?>" frameborder="0" width="100%"></iframe>
</div>

<?php $v->start("scripts") ?>
<script>
    var backRedirect = "<?= url("checkout/redirect") ?>";
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
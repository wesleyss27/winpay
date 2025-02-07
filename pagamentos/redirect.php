<?php 
    //session_start(); 
    $v->layout("_theme");
?>



<?php $v->start("topo"); ?>
    <div class="topo row">
        <div class="col-md-3">
            <img src="<?= url("themes/imgs/logos/logo4.png");?>" alt="" width="50%">
        </div>
        
        <div class="col-md-6">
            <h3>Você Acabou De Realizar Um Pedido</h3>
            Verifique sua caixa de email para ver os detalhes do seu pedido ou clique em entrar <br>
        </div>

        <div class="col-md-3">
            <a href="<?= url("login") ?>" target="_blank" class="btn btn-warning">Entrar</a><br>
        </div>
    </div>
<?php $v->end(); ?>

<div class="center">
    <h3>Se desejar realizar uma nova compra desse produto, clique no link abaixo:</h3>

    <a href="<?= HTTP . $_GET["redirect"] ?>"><?= $_GET["redirect"] ?></a>
</div>


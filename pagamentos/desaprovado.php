<?php $v->layout("_theme"); ?>

<?php $v->start("topo"); ?>
    <div class="topo row">
        <div class="col-md-3">
            <img src="<?= url("themes/tema2/assets/imgs/logo.png");?>" alt="" width="50%">
        </div>
        
        <div class="col-md-6">
            <h1>Pagamento Não Autorizado</h1>
        </div>

        <div class="col-md-3">
            <a href="<?= url() ?>" target="_blank" class="btn btn-success">Entrar</a><br>
        </div>
    </div>
<?php $v->end(); ?>

<div class="center">
<?= var_dump($_SESSION) ?>
    
    <h2>Seu pagamento não foi autorizado!</h2>

    <p>Verifique se você digitou os dados corretamente ou se possui saldo suficiente.</p>
    <p>Você precisa ter o <b>saldo total da venda</b> e não somente o valor da parcela para realizar a compra.</p>

    <a href="javascript:go.history(-1)" class="btn btn-success">Voltar</a>
</div>
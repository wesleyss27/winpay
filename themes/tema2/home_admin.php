<?php

//session_start();
//$acesso = access($_SESSION["permissao"]);

/*if ($acesso < 4) {
    header("Location: ".URL_BASE."/home");
}*/

?>

<?php $v->layout("_theme"); ?>

<!-- 
- DENTRO DESSA DIV VAI O CONTEÚDO PRINCIPAL DESSA VIEW
- CADA VIEW TEM SEU PRÓPRIO CONTEÚDO E SUAS PRÓPRIAS CLASSES CSS 
-->
<div class="conteudo">
<div class="row">
    <div class="col-md-4 card bg-primary alert">
    <?php if($clientes):
            ?>
        <article class="users_user">
            <h4>Cliente(s) Cadastrados</h4>
            <h1><?= $clientes; ?></h1>
            <a class="btn btn-success" href="<?= url("clientes"); ?>">Cadastrar</a>
        </article>

    <?php
    else:
    ?>
    <h4>Cliente(s) Cadastrados</h4>
    <h1>0</h1>
    <a class="btn btn-success" href="<?= url("clientes"); ?>">Cadastrar</a>
    <?php
    endif;
    ?>
    </div>

    <div class="col-md-4 card bg-primary alert">
    <h4>Produto(s) Cadastrados</h4>
    <?php if($produtos):
            ?>
        <article class="users_user">
            <h1><?= $produtos; ?> </h1>
            <a class="btn btn-success" href="<?= url("produtos"); ?>">Cadastrar</a>
        </article>

    <?php
    else:
    ?>
    <h1>0</h1>
    <a class="btn btn-success" href="<?= url("produtos"); ?>">Cadastrar</a>
    <?php
    endif;
    ?>
    </div>
    <div class="col-md-4 card bg-primary alert">
        <h4>Compras do Dia</h4>
        <?php if($pedidos > 0):
            ?>
        <article class="users_user">
            <h1><?= $pedidos; ?></h1>
            <a class="btn btn-success" href="<?= url("pedidos"); ?>">Acessar Pedidos</a>
        </article>
        <?php else: ?>
            <h1>0</h1>
            <a class="btn btn-success" href="<?= url("pedidos"); ?>">Acessar Pedidos</a>
        <?php endif; ?>
    </div>
    </div>
</div>

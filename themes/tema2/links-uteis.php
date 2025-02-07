<?php
    if($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2){
        header("Location: " . URL_BASE);
    }
?>

<?php $v->layout("_theme"); ?>

<div class="principal bg-white">
    <h4>Buscar pedidos St4rt</h4>
    <p><a href="<?= url("webhook/vendas") ?>" target="_blank"><?= url("webhook/vendas") ?></a></p>

    <h4>Cria notificação webhook Wirecard</h4>
    <p><a href="<?= url("webhook/notificacao") ?>" target="_blank"><?= url("webhook/notificacao") ?></a> - não usar sem necessidade</p>

    <h4>Cadastrar comissões</h4>
    <p><a href="<?= url("comissao/cadastro") ?>" target="_blank"><?= url("comissao/cadastro") ?></a></p>

    <h4>Cadastrar saldo</h4>
    <p><a href="<?= url("comissao/saldo") ?>" target="_blank"><?= url("comissao/saldo") ?></a></p>

    <h4>Rockets</h4>
    <p><a href="<?= url("rockets") ?>" target="_blank"><?= url("rockets") ?></a></p>
</div>
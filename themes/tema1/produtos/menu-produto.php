<!--a href="<?= url("produtos/detalhe/") . $produto->id; ?>">Detalhes</a-->
<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("editar_produto")) : ?>
    <a href="<?= url("produtos/editar/") . $produto->id; ?>">Editar</a>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("checkout_produto")) : ?>
    <a href="<?= url("produtos/checkout/") . $produto->id; ?>">Checkout</a>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("plano_produto")) : ?>
    <a href="<?= url("produtos/planos/") . $produto->id; ?>">Planos</a>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("afiliacao_produto")) : ?>
    <a href="<?= url("produtos/afiliados/") . $produto->id; ?>">Afiliação</a>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("coproducao_produto")) : ?>
    <a href="<?= url("produtos/coproducao/") . $produto->id; ?>">Coprodução</a>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("url_produto")) : ?>
    <a href="<?= url("produtos/urls/") . $produto->id; ?>">URL's</a>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("upsell_produto")) : ?>
    <a href="<?= url("produtos/upsell/") . $produto->id; ?>">Upsell <!--< <span class="badge bg-vermelho-gradient">Novo</span> --> </a>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("cupom_desconto")) : ?>
    <a href="<?= url("produtos/cupons/") . $produto->id; ?>">Cupom de Desconto <!--< <span class="badge bg-vermelho-gradient">Novo</span> --> </a><br>
<?php endif ?>

<?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("notificacao_checkout")) : ?>
    <a href="<?= url("produtos/notificacoes/") . $produto->id; ?>">Notificações Checkout <!--<span class="badge bg-vermelho-gradient">Novo</span> --> </a>
<?php endif ?>
<!-- <a href="<?= url("produtos/televendas/") . $produto->id; ?>">Televendas <span class="badge bg-azul-dark">Em Breve</span></a>  -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>

    <!--[if IE]><link rel="shortcut icon" href="<?= url("pagamentos/imgs/logo.png"); ?>"><![endif]-->
    <link rel="icon" href="<?= url("themes/imgs/logos/logo-icon.png") ?>">

    <script src="<?= url("pagamentos/js/jquery.slim.js"); ?>"></script>
    <script src="<?= url("pagamentos/js/jquery.js"); ?>"></script>
    <link rel="stylesheet" href="<?= url("pagamentos/css/bootstrap.css");?>">
    <script src="<?= url("pagamentos/js/bootstrap.js"); ?>"></script>
    <link rel="stylesheet" href="<?= url("pagamentos/css/style.css?".time());?>">
    <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/cores.css?".time()) ?>">
    <link href="<?= url("themes/tema1/dist/css/font-montserrat.css") ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">

    <?= $v->section("facebook"); ?>

    <?= $v->section("scripts"); ?>

</head>
<body>
    <?php 
    if($v->section("topo")):
        echo $v->section("topo");
    else:
    ?>

    <div class="topo row">
        <div class="col-md-3">
            <img src="<?= url("pagamentos/imgs/logo.png");?>" alt="" width="50%">
        </div>
        
        <!--?php if(@$_GET["boleto"]): ?-->
        <div class="col-md-6">
            <h3>Seu boleto foi gerado com sucesso!</h3>
            Imprima e pague em qualquer banco até a data do vencimento.
        </div>

        <div class="col-md-3">
            <!--?php $boleto = @$_GET["boleto"]; ?-->
            <p><a href="<?= $boleto; ?>" class="btn btn-success" target="_blank">IMPRIMIR BOLETO</a></p>
            <a href="<?= url() ?>" target="_blank" class="btn btn-success btn-sm">Entrar</a><br>
        </div>
        <!--?php endif; ?-->
    </div>

    <?php endif; ?>

        <?= $v->section("content"); ?>

</body>
</html>
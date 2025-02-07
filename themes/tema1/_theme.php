<?php

use Source\App\Sessao\AppSessao;

if (@$_GET["action"] == "sair") {
    $sessao = (new AppSessao())->deletaSessao(["id" => $_SESSION["sessao_id"]]);
    session_destroy();
    header("Location: " . URL_BASE . "/login");
} elseif (@$_GET["action"] == "sair_sub") {
    $_SESSION["subusuario"] = false;
    header("Location: " . URL_BASE);
}
?>

<?= $v->insert("inc/header"); ?>
<title><?= $title ?></title>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?= $v->insert("inc/navbar_topo"); ?>

        <?= $v->insert("inc/sidebar_menu"); ?>

        <?= $v->section("content"); ?>

        <?= $v->insert("inc/footer"); ?>

        <?= $v->section("scripts"); ?>
</body>

</html>
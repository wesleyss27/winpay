<?php
    use Source\App\Sessao\AppSessao;
    if(isset($_GET["action"]) == "sair") {
        $sessao = (new AppSessao())->deletaSessao(["id" => $_SESSION["sessao_id"]]);
        session_destroy();
        header("Location: ".URL_BASE."/login");
    }
?>

<?= $v->insert("inc/header");?>
<title><?= $title ?></title>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<?= $v->insert("inc/navbar_topo");?>

<?= $v->insert("inc/sidebar_menu");?>

<?= $v->section("content"); ?>

<a id="back-to-top-global" href="#" class="back-to-top" role="button" aria-label="Voltar ao topo" title="Voltar ao topo">
  <i class="fas fa-chevron-up"></i>
</a>

<?= $v->insert("inc/footer") ;?>

<?= $v->section("scripts"); ?> 
    </body>
</html>
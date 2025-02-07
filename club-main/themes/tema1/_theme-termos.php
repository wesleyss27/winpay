<?php
    use Source\App\Sessao\AppSessao;
    if(isset($_GET["action"]) == "sair") {
        $sessao = (new AppSessao())->deletaSessao(["id" => $_SESSION["sessao_id"]]);
        session_destroy();
        header("Location: ".URL_BASE."/login");
    }
?>

<?= $v->insert("inc/header");?>
<link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
<title><?= $title ?></title>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<?= $v->insert("inc/navbar_topo_termos");?>

<?= $v->insert("inc/sidebar_menu_termos");?>

<?= $v->section("content"); ?> 

<?= $v->insert("inc/footer") ;?>

<?= $v->section("scripts"); ?> 
    </body>
</html>
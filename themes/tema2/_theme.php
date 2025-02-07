<?php
    use Source\App\Sessao\AppSessao;
    if(isset($_GET["action"]) == "sair") {
        $sessao = (new AppSessao())->deletaSessao(["id" => $_SESSION["sessao_id"]]);
        session_destroy();
        header("Location: ".URL_BASE."/login");
    }

    //$acesso = access($_SESSION["permissao"]);
    //var_dump($_SESSION["permissao"]);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>

    <!--[if IE]><link rel="shortcut icon" href="<?= url("themes/tema2/assets/imgs/logo.png"); ?>"><![endif]-->
    <link rel="icon" href="<?= url("themes/tema2/assets/imgs/logo.png"); ?>">

    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/bootstrap3.css"); ?>"/>
    <script src="<?= url("themes/tema2/assets/js/jquery.js"); ?>"></script>
    <script src="<?= url("themes/tema2/assets/js/bootstrap.js"); ?>"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/style.css?".time()); ?>"/>

    <script src="<?= url("themes/tema2/assets/js/theme.js"); ?>"></script>

    <script>
        $(document).ready(function() {
            $("#submenu, #submenu2").hide();

            $("#mostrar").click( (e) => {
                e.preventDefault();
                $("#submenu").toggle("slow, 1000");
            })
            $("#mostrar2").click( (e) => {
                e.preventDefault();
                $("#submenu2").toggle("slow, 1000");
            })
        })
    </script>

</head>
<body>
    <div class="topo"><!-- TOPO -->
    <div class="row">
        <div class="col-md-3 logo">
            <a href="<?= URL_BASE; ?>"> <img src="<?= URL_BASE . "/themes/tema2/assets/imgs/logo.png"; ?>" width="40%" alt="OctaPay"> </a>
        </div>
        <div class="col-md-7"></div>
        <div class="col-md-2 link">
            <!--a href="#">Suporte</a-->
             | 
             <a href="?action=sair">Sair</a>
        </div>
    </div>
    </div><!-- FIM TOPO -->

    <br>

    <div class="container-fluid conteudo">
        <div class="row">
            <div class="col-md-2"><!-- MENU LATERAL -->
            <div class="main_sidebar"><!-- MENU LATERAL-->

                <?php 
                /*if($v->section("side-bar")):
                    echo $v->section("side-bar");
                else:*/
                ?>
                <div class="head">
                    <span class="openNav">&#9776;</span>
                    Olá <a href="<?= url("usuarios/perfil/".$_SESSION["id"]) ?>" title="Perfil"><?= $_SESSION["nome_abreviado"] ?></a>
                </div>

                <div class="menu">
                <ul>
                    <?php 
                        $menu = array(
                            1 => "
                            <li><a href='". url() . "'>Home</a></li>
                            <li><a href='". url('marketplace') . "'>Marketplace</a></li>
                            <li><a href='#' id='mostrar'>Configurações</a></li>
                            <ul id='submenu' class='submenu'>
                                <li><a href='". url('usuarios') . "'>Funcionarios</a></li>
                                <li><a href='". url('produtor') . "'>Produtores</a></li>
                                <li><a href='". url('afiliados') . "'>Afiliados</a></li>
                                <li><a href='". url('taxas') . "'>Taxas</a></li>
                            </ul>
                            <li><a href='". url('produtos') . "'>Produtos</a></li>
                            <li><a href='". url('produtos/promovidos') . "'>Produtos Que Promovo</a></li>
                            <li><a href='". url('vendas') . "'>Vendas</a></li>

                            <li><a href='#' id='mostrar2'>Comissões e Saques</a></li>
                            <ul id='submenu2' class='submenu'>
                                <li><a href='". url('comissao') . "'>Saldos e Extrados</a></li>
                                <li><a href='". url('comissao/octapay') . "'>Comissão OctaPay</a></li>
                                <li><a href='". url('banco') . "'>Contas Bancárias</a></li>
                                <li><a href='". url('saques') . "'>Saques</a></li>
                            </ul>

                            <li><a href='". url('relatorios') . "'>Relatorios</a></li>
                            <li><a href='". url('pixel') . "'>Pixel</a></li>
                            <li><a href='".url('links-uteis')."'>Links Úteis</a></li>",
                            2 => "
                            <li><a href='". url() . "'>Home</a></li>
                            <li><a href='". url('marketplace') . "'>Marketplace</a></li>
                            <li><a href='". url('produtos') . "'>Produtos</a></li>
                            <li><a href='". url('produtos/promovidos') . "'>Produtos Que Promovo</a></li>
                            <li><a href='". url('vendas') . "'>Vendas</a></li>
                            
                            <li><a href='#' id='mostrar2'>Comissões e Saques</a></li>
                            <ul id='submenu2' class='submenu'>
                                <li><a href='". url('comissao') . "'>Saldos e Extrados</a></li>
                                <li><a href='". url('banco') . "'>Contas Bancárias</a></li>
                                <li><a href='". url('saques') . "'>Saques</a></li>
                            </ul>

                            <li><a href='". url('relatorios') . "'>Relatorios</a></li>
                            <li><a href='". url('pixel') . "'>Pixel</a></li>",
                            3 => "
                            <li><a href='". url() . "'>Home</a></li>
                            <li><a href='". url('marketplace') . "'>Marketplace</a></li>
                            <li><a href='". url('produtos') . "'>Produtos</a></li>
                            <li><a href='". url('vendas') . "'>Vendas</a></li>
                            
                            <li><a href='#' id='mostrar2'>Comissões e Saques</a></li>
                            <ul id='submenu2' class='submenu'>
                                <li><a href='". url('comissao') . "'>Saldos e Extrados</a></li>
                                <li><a href='". url('banco') . "'>Contas Bancárias</a></li>
                                <li><a href='". url('saques') . "'>Saques</a></li>
                            </ul>

                            <li><a href='". url('relatorios') . "'>Relatorios</a></li>
                            <li><a href='". url('pixel') . "'>Pixel</a></li>
                            <li><a href='".url('links-uteis')."'>Links Úteis</a></li>",
                            4 => "
                            <li><a href='". url('marketplace') . "'>Marketplace</a></li>
                            <li><a href='". url('marketplace') . "'>Minhas Compras</a></li>"
                        );
                        if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) 
                            echo $menu[1]; 
                        elseif($_SESSION["tipo"] == 4 || $_SESSION["tipo"] == 5)
                            echo $menu[2];
                        elseif($_SESSION["tipo"] == 6)
                            echo $menu[4];
                        else
                            echo $menu[3];
                        
                    ?>


                </ul>
                </div>

                <?php //endif; ?>
                </div><!-- FIM MENU -->
            </div><!-- FIM MENU LATERAL -->

            <div class="col-md-10 conteudo-principal">
                <div class="row linha">
                    <div class="col-md-7 titulo-site">
                        <span class="titulo"><?= $title; ?></span>
                    </div>

                    <div class="col-md-5">
                        <?= $v->section("promover"); ?>
                    </div>

                    <div class="col-md-5">
                        <?= $v->section("saldo"); ?>
                    </div>

                    <div class="col-md-2"></div>
                    <div class="col-md-3">
                        <?= $v->section("saque"); ?>
                    </div>
                </div>

                <div class="row content"><!-- AQUI ENTRA O CONTEÚDO DE CADA PÁGINA -->
                    <!-- ESSE SCRIPT CHAMA O CONTEUDO PRINCIPAL DE CADA VIEW -->
                    <?= $v->section("content"); ?> 
                </div><!-- FIM CONTEÚDO PRINCIPAL -->

            </div>
        </div>
    </div>
</body>
</html>
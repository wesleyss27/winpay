<?php
$v->layout("_theme");
if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}

$json = array(
    0 => "Não",
    1 => "Sim"
);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6" style="margin-bottom:10px">
                    <h1 style="font-family:inter"><?= $title ?></h1>
                </div>
                <div class="col-sm-6" style="font-family:inter">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Integrações</li>
                    </ol>
                </div>
            </div>

            <div class="card card-body">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <a href="<?= url("integracoes") ?>" class="btn btn-primary form-control text-white">Disponíveis</a>
                    </div>

                    <div class="col-md-3">
                        <a href="<?= url("integracoes/ativas") ?>" class="btn btn-primary form-control text-white ">Ativas</a>
                    </div>

                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-success">Sugerir Integração</button>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <div class="col-sm-6" style="font-family:inter">
        <li class="breadcrumb-item active">
            <h4>Plataformas Ativas</h4>
    </div>
    <style>
        .zoom:hover img {
            -moz-transform: scale(1.2);
            -webkit-transform: scale(1.2);
            transform: scale(1.2);
        }

        .box-imgs {
            padding: 8px;
            background: #fff;
            border-radius: 7px;
            box-shadow: 2px 2px 5px #ccc;
        }
    </style>
    <!-- small box -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <?php if (@$bling) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarbling" style="font-family:inter" id="btn-bling"><img src="<?= url("themes/imgs/bling.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$hotzapp) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarhotzapp" style="font-family:inter" id="btn-hot"><img src="<?= url("themes/imgs/hotzaap.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$hotjar) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrar-hotjar" style="font-family:inter" id="btn-hotjar"><img src="<?= url("themes/imgs/hotjar.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$isei) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarisei" style="font-family:inter" id="btn-isei"><img src="<?= url("themes/imgs/isei.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

            </div>
            <br>

            <div class="row">
                <?php if (@$notas) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#integracaonotazz" style="font-family:inter" id="btn-notazz"><img src="<?= url("themes/imgs/notazz.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$notificacoes) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarni" style="font-family:inter" id="btn-ni"><img src="<?= url("themes/imgs/Not-inteligentes.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$leadlovers) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarleadlovers" style="font-family:inter" id="btn-leadlovers"><img src="<?= url("themes/imgs/leadlovers.png") ?>" width='100%' style="border-radius: 20px"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$hotbillet) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarhotbillet" style="font-family:inter" id="btn-hotbillet"><img src="<?= url("themes/imgs/Hotbillet.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$chat) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarchatguru" style="font-family:inter" id="btn-chatguru"><img src="<?= url("themes/imgs/chatguru.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$shopify) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#editarshopify" style="font-family:inter" id="btn-chatguru"><img src="<?= url("themes/imgs/shopify.png") ?>" width='100%' style="border-radius: 20px; margin-top: -40%;"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$mex) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarmex" style="font-family:inter" id="btn-mex"><img src="<?= url("themes/imgs/mex.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$activecampaign) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostraractivecampaign" style="font-family:inter" id="btn-activecampaign"><img src="<?= url("themes/imgs/activecampaign.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (@$rdstation) : ?>
                    <div class="col-sm-3 card-body text-center zoom">
                        <div class="m-potlet m-portlet--rounded">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-widget1 text-center">
                                    <a class="btn" data-toggle="modal" data-target="#mostrarrdstation" style="font-family:inter" id="btn-rdstation"><img src="<?= url("themes/imgs/rdstation.png") ?>" width='100%' class="box-imgs"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <br>
        <!-- end single prod -->

    </section>
    <!-- /.content -->
</div><!-- /.container-fluid -->

<!-- <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
</a> -->

<!-- BLING -->

<!-- MODAL BLING -->
<div id="mostrarbling" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Integração Com Bling</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->


            <!-- Modal body -->
            <div class="modal-body" style="overflow-x: auto">

                <?php if (@$bling) : $i = 1; ?>
                    <script>
                        var dadosbling = [];
                    </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Proudto</th>
                                <th scope="col">API KEY</th>
                                <th scope="col">Editar/Excluir</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($bling as $bling) : ?>
                                <tr>
                                    <td><?= $bling->id ?></td>
                                    <td><?= $bling->produto ? $bling->dataProduto()->nome : "Todos" ?></td>
                                    <td><?= $bling->api_key ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-sm" data-target="#editarbling" onclick="editarbling(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <?php endif ?>

                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluirbling(<?= $bling->id; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dadosbling[<?= $i ?>] = {
                                        id: "<?= $bling->id ?>",
                                        produto: "<?= $bling->produto ?>",
                                        api_key: "<?= $bling->api_key ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php

                else : ?>
                    Nenhuma integração com Bling encontrada
                <?php endif; ?>
            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarbling">Fechar</button>
                <button type="button" class="btn btn-primary" id="salvarbling">Salvar</button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim MODAL BLING -->

<!-- MODAL EDITAR BLING -->
<div id="editarbling" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Editar Integração Com Bling</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="formbling">
                <!-- Modal body -->
                <div class="modal-body">

                    <input type="hidden" name="id" id="idbling">
                    <b>API KEY</b>
                    <input type="text" name="api_key" id="api_key" class="form-control">

                    <br>

                    <b>Produto</b>
                    <select name="produtobling" id="produtobling" class="form-control">
                        <option value="">Todos</option>
                        <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                                <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarbling">Fechar</button>
                    <button type="button" class="btn btn-primary" id="atualizarbling">Editar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL EDITAR BLING -->
<!-- FIM BLING -->


<!-- MODAL HOTZAPP -->
<div id="mostrarhotzapp" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração Hotzapp</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <!-- Modal body -->
            <div class="modal-body" style="overflow-x: auto">

                <?php if (@$hotzapp) : $i = 1; ?>
                    <script>
                        var dadoshotzapp = [];
                    </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Proudto</th>
                                <th scope="col">URL</th>
                                <th scope="col">JSON</th>
                                <th scope="col">Eventos</th>
                                <th scope="col">Editar/Excluir</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($hotzapp as $hotzapp) : ?>
                                <tr>
                                    <td><?= $hotzapp->id ?></td>
                                    <td><?= $hotzapp->produto ? $hotzapp->dataProduto()->nome : "Todos" ?></td>
                                    <td><?= $hotzapp->url ?></td>
                                    <td><?= $json[$hotzapp->json] ?></td>
                                    <td><?= $hotzapp->dataEventos() ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-sm" data-target="#editarhotzapp" onclick="editarhotzapp(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <?php endif ?>

                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluirpostback(<?= $hotzapp->id; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dadoshotzapp[<?= $i ?>] = {
                                        id: "<?= $hotzapp->id ?>",
                                        produto: "<?= $hotzapp->produto ?>",
                                        url: "<?= $hotzapp->url ?>",
                                        eventos: "<?= $hotzapp->eventos ?>",
                                        status: "<?= $hotzapp->status ?>",
                                        json: "<?= $hotzapp->json ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php

                else : ?>
                    Nenhum postback cadastrado
                <?php endif; ?>
            </div>
            <!-- Fim Modal body -->



            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL HOTZAPP -->

<!-- MODAL HOTJAR -->
<div id="mostrar-hotjar" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração Hotjar</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body" style="overflow-x: auto">

                    <?php if (@$hotjar) : $i = 1; ?>
                        <script>
                            var dadosHotjar = [];
                        </script>
                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">ID</th>
                                    <th scope="col">Proudto</th>
                                    <th scope="col">Tracking id</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($hotjar as $hotjar) : ?>
                                    <tr>
                                        <td><?= $hotjar->id ?></td>
                                        <td><?= $hotjar->produto != 0 ? $hotjar->dataProduto()->nome : "Todos" ?></td>
                                        <td><?= $hotjar->tracking_id ?></td>
                                        <td>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#editar-hotjar" onclick="editarHotjar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluirHotjar(<?= $hotjar->id; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </td>
                                    </tr>

                                    <script>
                                        dadosHotjar[<?= $i ?>] = {
                                            id: "<?= $hotjar->id ?>",
                                            produto: "<?= $hotjar->produto ?>",
                                            tracking_id: "<?= $hotjar->tracking_id ?>",
                                        }
                                    </script>
                                <?php $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    <?php

                    else : ?>
                        Nenhuma integração encotrada
                    <?php endif; ?>
                </div>
                <!-- Fim Modal body -->


                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL HOTJAR -->

<!-- MODAL EDITAR HOTJAR -->
<div id="editar-hotjar" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="card-header">
                <h4 class="card-title" id="titulo">Editar Hotjar</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="form-hotjar">
                <!-- Modal body -->
                <div class="modal-body" style="background: #f4f6f9">
                    <input type="hidden" name="descricao" id="descricao">

                    <div class="card card-body">

                        <input type="hidden" name="id" id="id-hotjar">
                        <div class="row">
                            <div class="col-md-6">
                                <b>Produto</b>
                                <select name="produto_hotjar" id="produto_hotjar" class="form-control">
                                    <option value="0">Todos</option>
                                    <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                                            <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <b>Tracking ID:</b>
                                <input type="text" name="tracking_id" id="tracking_id" class="form-control">
                                <!-- <small> * Gere a URL na sua conta do<p id="titulo"p>e cole aqui.</small> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvar-hotjar">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL EDITAR HOTJAR -->

<!-- MODAL ISEI -->
<div id="mostrarisei" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração Isei</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body" style="overflow-x: auto">

                    <?php if (@$isei) : $i = 1; ?>
                        <script>
                            var dados = [];
                        </script>
                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">ID</th>
                                    <th scope="col">Proudto</th>
                                    <th scope="col">URL</th>
                                    <th scope="col">JSON</th>
                                    <th scope="col">Eventos</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($isei as $isei) : ?>
                                    <tr>
                                        <td><?= $isei->id ?></td>
                                        <td><?= $isei->produto ? $isei->dataProduto()->nome : "Todos" ?></td>
                                        <td><?= $isei->url ?></td>
                                        <td><?= $json[$isei->json] ?></td>
                                        <td><?= $isei->dataEventos() ?></td>
                                        <td>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#editarhotzapp" onclick="editarisei(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluirpostback(<?= $isei->id; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </td>
                                    </tr>

                                    <script>
                                        dados[<?= $i ?>] = {
                                            id: "<?= $isei->id ?>",
                                            produto: "<?= $isei->produto ?>",
                                            url: "<?= $isei->url ?>",
                                            eventos: "<?= $isei->eventos ?>",
                                            status: "<?= $isei->status ?>",
                                            json: "<?= $isei->json ?>",
                                        }
                                    </script>
                                <?php $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    <?php

                    else : ?>
                        Nenhum postback cadastrado
                    <?php endif; ?>
                </div>
                <!-- Fim Modal body -->


                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL ISEI -->

<!-- MODAL NOTAZZ -->
<div id="integracaonotazz" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Integração com Notazz</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body" style="overflow-x: auto">

                    <?php if (@$notas) : $i = 1; ?>

                        <script>
                            var dadosnotazz = [];
                        </script>

                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">ID</th>
                                    <th scope="col">API</th>
                                    <th scope="col">Dias após a venda</th>
                                    <th scope="col">Produto</th>
                                    <th scope="col">Data Cadastro</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr style="font-family:inter">
                                    <?php foreach ($notas as $nota) : ?>
                                        <th scope="row"><?= $nota->id; ?></th>
                                        <th><?= mb_strimwidth(@$nota->api_notazz, 0, 30, "..."); ?></th>
                                        <td><?= $nota->data_nota == 0 ? "Manualmente" : $nota->data_nota . " dia(s)"; ?></td>
                                        <td><?= $nota->produto ? $nota->dataProduto()->nome : "-" ?></td>
                                        <td><?= formataDataHora($nota->created_at); ?></td>
                                        <td>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#editarintegracaoNotazz" onclick="editarnotazz(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluirnotazz(<?= $nota->id; ?>)"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </td>
                                </tr>

                                <script>
                                    dadosnotazz[<?= $i ?>] = {
                                        id: "<?= $nota->id ?>",
                                        api_notazz: "<?= $nota->api_notazz ?>",
                                        data_nota: "<?= $nota->data_nota ?>",
                                        produto: "<?= $nota->produto ?>",
                                        codigo_cnae: "<?= $nota->codigo_cnae ?>",
                                        codigo_lc116: "<?= $nota->codigo_lc116 ?>",
                                        enviar_email_cliente: "<?= $nota->enviar_email_cliente ?? 1 ?>",
                                        cidade_padrao: "<?= $nota->cidade_padrao ?>",
                                        uf_padrao: "<?= $nota->uf_padrao ?>",
                                        cep_padrao: "<?= $nota->cep_padrao ?>",
                                    }
                                </script>

                            <?php $i++;
                                    endforeach; ?>

                            </tbody>
                        </table>

                    <?php

                    else : ?>
                        Nenhuma integração cadastrada
                    <?php endif; ?>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarnotazz">Cancelar</button>
                    <button class="btn btn-primary" id="salvarnotazz">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL NOTAZZ -->

<!-- MODAL EDITAR NOTAZZ -->
<div id="editarintegracaoNotazz" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Editar Integração com Notazz</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="formnotazz">
                <!-- Modal body -->
                <div class="modal-body">

                    <input type="hidden" id="idnotazz" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <b>API Notazz</b> (<small>obrigatório</small>)
                            <input type="text" name="api_notazz" id="api_notazz" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <b>Quando gerar a nota?</b> (<small>obrigatorio</small>)
                            <select name="data_nota" id="data_nota" class="form-control">
                                <option value="0">Manualmente</option>
                                <!--option value="1">No momento da venda</option>
                            <option value="10">10 dias após a venda</option-->
                            </select>
                        </div>
                    </div>

                    <br>


                    <div class="row">
                        <div class="col-md-6">
                            <b>Produto</b> (<small>opcional</small>)
                            <select name="produto" id="produtonotazz" class="form-control">
                                <option value="">Selecionar</option>
                                <?php foreach ($produtos as $produto) : ?>
                                    <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <b>Código CNAE do produto</b> (<small>opcional</small>)
                            <input type="text" name="codigo_cnae" id="codigo_cnae" class="form-control">
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-6">
                            <b>Código LC116</b> (<small>opcional</small>)
                            <input type="text" name="codigo_lc116" id="codigo_lc116" class="form-control">
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <b>Enviar nota fiscal para o cliente por e-mail?</b>
                            <select name="enviar_email_cliente" id="enviar_email_cliente_editar" class="form-control">
                                <option value="1">Sim — enviar por e-mail ao cliente</option>
                                <option value="0">Não — apenas emitir a nota</option>
                            </select>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <b>Endereço padrão (para clientes sem endereço)</b> (<small>recomendado para produtos digitais</small>)
                            <small class="text-muted d-block mb-1">Use o endereço da sua empresa. Será aplicado automaticamente quando o cliente não tiver cidade/UF cadastrado.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <b>Cidade</b>
                            <input type="text" name="cidade_padrao" id="cidade_padrao_editar" class="form-control" placeholder="Ex: São Paulo">
                        </div>
                        <div class="col-md-2">
                            <b>UF</b>
                            <input type="text" name="uf_padrao" id="uf_padrao_editar" class="form-control" placeholder="SP" maxlength="2" style="text-transform:uppercase">
                        </div>
                        <div class="col-md-5">
                            <b>CEP</b>
                            <input type="text" name="cep_padrao" id="cep_padrao_editar" class="form-control" placeholder="00000000">
                        </div>
                    </div>

                    <small>
                        Campos opcionais: <br><br>

                        Escolha se desejar gerar uma nota para um produto específico; <br>
                        Digite o código de atividade econômica do produto selecionado; <br>
                        Digite o código da área de serviço na prefeitura. <br><br>

                        obs.: caso não seja digitado o código CNAE e LC116, será configurado de acordo com o cadastrado no Notazz <br>
                        obs.: você pode pegar o código CNAE e LC116 nas configurações do seu produto no Notazz <br>
                    </small>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarnotazz">Cancelar</button>
                    <button class="btn btn-primary" id="atualizarnotazz">Editar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM EDITAR NOTAZZ -->

<!--Notificações Inteligentes-->

<!-- MODAL NOTIFICAÇÕES INTELIGENTES -->
<div id="mostrarni" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Integração Notificações Inteligentes</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body" style="overflow-x: auto">

                    <?php if (@$notificacoes) : $i = 1; ?>
                        <script>
                            var dadosni = [];
                        </script>
                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">ID</th>
                                    <th scope="col">Token</th>
                                    <th scope="col">Integração ID</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($notificacoes as $notificacao) : ?>
                                    <tr>
                                        <th><?= $notificacao->id ?></th>
                                        <td><?= $notificacao->token ?></td>
                                        <td><?= $notificacao->integracao_id ?></td>
                                        <td>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#editarni" onclick="editarni(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluirni(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </td>
                                    </tr>

                                    <script>
                                        dadosni[<?= $i ?>] = {
                                            id: "<?= $notificacao->id ?>",
                                            token: "<?= $notificacao->token ?>",
                                            integracao_id: "<?= $notificacao->integracao_id ?>",
                                        }
                                    </script>
                                <?php $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    <?php

                    else : ?>
                        Nenhuma integração encontrada
                    <?php endif; ?>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarni">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvarni">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL NOTIFICAÇÕES INTELIGENTES -->

<!-- MODAL EDITAR NOTIFICAÇÕES INTELIGENTES -->
<div id="editarni" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Editar Notificações Inteligentes</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="formni">
                <!-- Modal body -->
                <div class="modal-body">

                    <input type="hidden" name="id" id="idni">
                    <div class="row">
                        <div class="col-md-6">
                            <b>Token:</b>
                            <input type="text" name="token" id="token" class="form-control">
                            <small>* Gere o token de conexão na sua conta do Notificações Inteligentes e cole aqui.</small>
                        </div>

                        <div class="col-md-6">
                            <b>Integração ID:</b>
                            <input type="text" name="integracao_id" id="integracao_id" class="form-control">
                        </div>
                    </div>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarni">Fechar</button>
                    <button type="button" class="btn btn-primary" id="atualizarni">Editar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL EDITAR NOTIFICAÇÕES INTELIGENTES -->

<!-- MODAL LEADLOVERS -->
<div id="mostrarleadlovers" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração com Leadlovers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->
            <!-- Modal body -->
            <div class="modal-body" style="overflow-x: auto">

                <?php if (@$leadlovers) : $i = 1; ?>
                    <script>
                        var dadosleadlovers = [];
                    </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Descricao</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Ação</th>
                                <th scope="col">Evento</th>
                                <th scope="col"></th>
                                <th scope="col">Editar/Excluir</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach (@$lead as $lead) : ?>
                                <tr>
                                    <td><?= $lead->id ?></td>
                                    <td><?= $lead->descricao ?></td>
                                    <td><?= $lead->dataProduto()->nome ?></td>
                                    <td><?= $lead->acao == 0 ? "Retirar" : "Inserir" ?></td>
                                    <td><?= $lead->dataEventos() ?></td>
                                    <?php $lead_maquina = explode("/", $lead->maquina);
                                    $lead_funil = explode("/", $lead->funil);
                                    $lead_nivel = explode("/", $lead->nivel);
                                    ?>
                                    <td><b>Maquina:</b> <?= @$lead->maquina_nome; ?> => <?= @$lead->funil_nome; ?> => <?= @$lead->nivel_nome; ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-sm" data-target="#editarLeadlovers" onclick="editarLeadlovers(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <?php endif ?>

                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluirLead(<?= $lead->id ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dadosleadlovers[<?= $i ?>] = {
                                        id: "<?= $lead->id ?>",
                                        usuario: "<?= $lead->usuario ?>",
                                        descricao: "<?= $lead->descricao ?>",
                                        token: "<?= $lead->token ?>",
                                        produto: "<?= $lead->produto ?>",
                                        tipo_integracao: "<?= $lead->tipo_integracao ?>",
                                        acao: "<?= $lead->acao ?>",
                                        evento: "<?= $lead->eventos ?>",
                                        maquina: "<?= $lead->maquina ?>",
                                        funil: "<?= $lead->funil ?>",
                                        nivel: "<?= $lead->nivel ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php

                else : ?>
                    Nenhuma conta cadastrada
                <?php endif; ?>
            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                <!-- <button type="button" class="btn btn-primary" id="salvar">Salvar</button> -->
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->
    </div>
    <!-- Fim Modal dialog -->
</div>
<!-- FIM MODAL LEADLOVERS -->

<!-- MODAL EDITAR LEADLOVERS -->
<div id="editarLeadlovers" class="modal fade" role="dialog">
    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Cadastrar Conta Leadlovers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->
            <form id="formLead">

                <!-- Modal body -->
                <input type="hidden" name="url" value="http://llapi.leadlovers.com/webapi/">

                <input type="hidden" id="id_leadlovers" name="leadlovers" value="0">

                <div class="modal-body" style="background: #f4f6f9">
                    <div class="card card-body">

                        <span><b>Descrição</b></span>
                        <input type="text" name="descricao" id="desc_leadlovers" class="form-control">
                        <span><b>Token</b></span>
                        <input type="text" name="chave_unica" id="token_leadlovers" class="form-control">

                    </div>

                    <!-- FIM ESCONDER LEAD -->
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <b>Produto</b>
                                <select name="produto_lead" id="produto" class="form-control">
                                    <option value="">Selecionar</option>
                                    <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                                            <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <span><b>Tipo de integração</b></span>
                                <select name="tipo_integracao" id="tipo_integracao" class="form-control">
                                    <option value="">-- Selecionar --</option>
                                    <option value="plataforma">Integração pela RedRocket</option>
                                    <option value="produto">Pelas configurações do produto no LeadLovers</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="modal-header">
                                <b>Eventos</b>
                            </div>
                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-4">
                                        <input class="chkLead" type="checkbox" name="evento[]" value="1" id="evento1"> Aguardando Pagamento <br>
                                        <input class="chkLead" type="checkbox" name="evento[]" value="2" id="evento2"> Em Análise <br>
                                        <input class="chkLead" type="checkbox" name="evento[]" value="3" id="evento3"> Pré-autorizada <br>
                                    </div>

                                    <div class="col-md-3">
                                        <input class="chkLead" type="checkbox" name="evento[]" value="4" id="evento4"> Paga <br>
                                        <input class="chkLead" type="checkbox" name="evento[]" value="5" id="evento5"> Cancelada <br>
                                        <input class="chkLead" type="checkbox" name="evento[]" value="6" id="evento6"> Devolvida <br>
                                    </div>

                                    <div class="col-md-5">
                                        <input class="chkLead" type="checkbox" name="evento[]" value="7" id="evento7"> Chargeback <br>
                                        <input class="chkLead" type="checkbox" name="evento[]" value="8" id="evento8"> Concluída <br>
                                        <input class="chkLead" type="checkbox" name="evento[]" value="9" id="evento9"> Bloqueada <br>
                                        <input class="chkLead" type="checkbox" name="evento[]" value="10" id="evento10"> Carrinho Abandonado (produtor) <br>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INTEGRAÇÃO PLATAFORMA -->
                    <div id="integracao_plataforma" style="display: none;">
                        <hr>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <span><b>O que fazer</b></span>
                                    <select name="acao_lead" id="acao_lead" class="form-control">
                                        <option value="0"> Retirar lead do funil</option>
                                        <option value="1"> Inserir lead no funil</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="card card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <b>Selecione a Máquina</b>
                                    <select name="maquina_lead" id="maquina_lead" class="form-control">
                                        <option value="">-- Selecione uma maquina --</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <b>Selecione o Funil</b>
                                    <select name="funil_lead" id="funil_lead" class="form-control" readonly="true" disabled="true">
                                        <option value="">-- Selecione uma máquina para liberar funil --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <b>Selecione o Nivel</b>
                                    <select name="nivel_lead" id="nivel_lead" class="form-control" readonly="true" disabled="true">
                                        <option value="">-- Selecione um funil para liberar nivel --</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- FIM INTEGRAÇÃO PLATAFORMA -->

                    <br>
                    <button type="button" class="btn btn-primary" onclick="salvaLeadlovers()" style="display: block; float: right;" id="postbackLeadlovers">Salvar</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarLeadlovers">Fechar</button>
                    <br>
                    <br>
                    <!-- FIM ESCONDER LEAD -->

                </div>
            </form>
        </div>
    </div>
    <!-- Fim Modal content-->
</div>
<!-- FIM EDITAR LEADLOVERS -->

<!-- MODAL HOTBILLET -->
<div id="mostrarhotbillet" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Hotbillet</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body" style="overflow-x: auto">

                    <?php if (@$hotbillet) : $i = 1; ?>
                        <script>
                            var dados = [];
                        </script>
                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">ID</th>
                                    <th scope="col">Proudto</th>
                                    <th scope="col">URL</th>
                                    <th scope="col">JSON</th>
                                    <th scope="col">Eventos</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($hotbillet as $hotbillet) : ?>
                                    <tr>
                                        <td><?= $hotbillet->id ?></td>
                                        <td><?= $hotbillet->produto ? $hotbillet->dataProduto()->nome : "Todos" ?></td>
                                        <td><?= $hotbillet->url ?></td>
                                        <td><?= $json[$hotbillet->json] ?></td>
                                        <td><?= $hotbillet->dataEventos() ?></td>
                                        <td>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#editarhotzapp" onclick="editarhotbillet(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluirpostback(<?= $hotbillet->id; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </td>
                                    </tr>

                                    <script>
                                        dados[<?= $i ?>] = {
                                            id: "<?= $hotbillet->id ?>",
                                            produto: "<?= $hotbillet->produto ?>",
                                            url: "<?= $hotbillet->url ?>",
                                            eventos: "<?= $hotbillet->eventos ?>",
                                            status: "<?= $hotbillet->status ?>",
                                            json: "<?= $hotbillet->json ?>",
                                        }
                                    </script>
                                <?php $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    <?php

                    else : ?>
                        Nenhum postback cadastrado
                    <?php endif; ?>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL HOTBILLET -->

<!-- Modal MEX10-->
<div id="mostrarmex" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração com Mex</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <!-- Modal body -->
            <div class="modal-body" style="overflow-x: auto">

                <?php if (@$mex) : $i = 1; ?>
                    <script>
                        var dadosmex = [];
                    </script>
                    <table class="table table-striped" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Token</th>
                                <th scope="col">Editar/Excluir</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($mex as $mex) : ?>
                                <tr>
                                    <td><?= $mex->id ?></td>
                                    <td><?= $mex->dataUsuario()->nome ?></td>
                                    <td><?= $mex->token ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-sm" data-target="#editarMex10" onclick="editarMex10(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <?php endif ?>

                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluirmex(<?= $mex->id ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dadosmex[<?= $i ?>] = {
                                        id: "<?= $mex->id ?>",
                                        usuario: "<?= $mex->usuario ?>",
                                        token: "<?= $mex->token ?>",
                                        msg_cartao_pago: "<?= $mex->msg_cartao_pago ?>",
                                        msg_cartao_analise: "<?= $mex->msg_cartao_analise ?>",
                                        msg_cartao_cancelado: "<?= $mex->msg_cartao_cancelado ?>",
                                        msg_boleto_pago: "<?= $mex->msg_boleto_pago ?>",
                                        msg_boleto_gerado: "<?= $mex->msg_boleto_gerado ?>",
                                        msg_boleto_cancelado: "<?= $mex->msg_boleto_cancelado ?>",
                                        msg_pix_gerado: "<?= $mex->msg_pix_gerado ?>",
                                        msg_pix_pago: "<?= $mex->msg_pix_pago ?>",
                                        msg_pix_cancelado: "<?= $mex->msg_pix_cancelado ?>",
                                        msg_carrinho: "<?= $mex->msg_carrinho ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    Nenhuma integração cadastrada
                <?php endif; ?>
            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim MEX10 -->

<!-- EDITAR MEX -->
<div id="editarMex10" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="card-title">Editar Mex10</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="formMex10">
                <!-- Modal body -->
                <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="id" id="idMex">

                    <div class="card card-body">
                        <b>TOKEN</b>
                        <input type="text" name="mex_token" id="mex_token" class="form-control">
                        <b>* Para gerar um token, vá até a guia Developers > Gerar Token, no Mex10.<br></b>
                    </div>

                    <hr>

                    <div class="card card-body">
                        <b>* Deixe em branco caso não queira enviar determinada mensagem </b><br>

                        <b>Mensagem para enviar caso compra no CARTÃO esteja em ANÁLISE:</b>
                        <br>
                        <input type="text" name="mex_cartao_analise" id="mex_cartao_analise" maxlength="300" class="form-control">
                        <small>
                            Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> está em análise.
                            <br><br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no CARTÃO esteja APROVADA:</b><br>

                        <input type="text" name="mex_cartao_aprovado" id="mex_cartao_aprovado" maxlength="300" class="form-control">
                        <small>Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> foi aprovada.
                            <br><br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no CARTÃO esteja CANCELADA:</b>
                        <br>
                        <input type="text" name="mex_cartao_cancelado" id="mex_cartao_cancelado" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, o pagamento do <b>{produto}</b> foi cancelado.
                            <br>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no BOLETO tenha sido GERADO:</b>
                        <br>
                        <input type="text" name="mex_boleto_gerado" id="mex_boleto_gerado" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, foi gerado um boleto do <b>{produto}</b>.
                            <br>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.</small>
                        <br>
                        <small><b>Observação: </b>Irá ser enviado junto link e a linha digitavel do boleto.</small>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no BOLETO tenha sido PAGO:</b>
                        <br>
                        <input type="text" name="mex_boleto_pago" id="mex_boleto_pago" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, recebemos o pagamento do <b>{produto}</b> via boleto.
                            <br>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no BOLETO tenha sido CANCELADO:</b>
                        <br>
                        <input type="text" name="mex_boleto_cancelado" id="mex_boleto_cancelado" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, o boleto de pagamento do <b>{produto}</b> está cancelado.
                            <br>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no PIX tenha sido GERADA:</b>
                        <br>
                        <input type="text" name="mex_pix_gerado" id="mex_pix_gerado" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, foi gerado um PIX do <b>{produto}</b>.
                            <br>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no PIX tenha sido PAGO:</b>
                        <br>
                        <input type="text" name="mex_pix_pago" id="mex_pix_pago" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi pago.
                            <br>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>

                    <div class="card card-body">
                        <b>Mensagem para enviar caso compra no PIX tenha sido CANCELADA:</b>
                        <br>
                        <input type="text" name="mex_pix_cancelado" id="mex_pix_cancelado" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi cancelado.
                            <br>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                    <hr>
                    <div class="card card-body">
                        <b>Mensagem para enviar caso tenha carrinho abandonado:</b>
                        <br>
                        <input type="text" name="mex_abandono_carrinho" id="mex_abandono_carrinho" maxlength="300" class="form-control">
                        <br>
                        <small>Exemplo: Olá <b>{nome}</b>, vi que você demonstrou interesse no <b>{produto}</b> e gostaria de saber se ficou com alguma dúvida.</small>
                        <br>
                        <small>
                            <br>
                            <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                            <br>
                            <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                        </small>
                    </div>
                </div>

                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarChatGuru">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvarMex10">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>
        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!--  FIM EDITAR MEX10 -->

<!-- MODAL ACTIVE CAMPAIGN -->
<div id="mostraractivecampaign" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração com Active Campaign</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <!-- Modal body -->
            <div class="modal-body" style="overflow-x: auto">

                <?php if (@$activecampaign) : $i = 1; ?>
                    <script>
                        var dadosactivecampaign = [];
                    </script>
                    <table class="table table-striped" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Token</th>
                                <th scope="col">Editar/Excluir</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($activecampaign as $activecampaign) : ?>
                                <tr>
                                    <td><?= $activecampaign->id ?></td>
                                    <td><?= $activecampaign->dataUsuario()->nome ?></td>
                                    <td><?= $activecampaign->token ?></td>
                                    <td><?= $activecampaign->link ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-sm" data-target="#editarActiveCampaign" onclick="editarActiveCampaign(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <?php endif ?>

                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluirActiveCampaign(<?= $activecampaign->id ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dadosactivecampaign[<?= $i ?>] = {
                                        id: "<?= $activecampaign->id ?>",
                                        usuario: "<?= $activecampaign->usuario ?>",
                                        token: "<?= $activecampaign->token ?>",
                                        link: "<?= $activecampaign->link ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    Nenhuma integração cadastrada
                <?php endif; ?>
            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM ACTIVE CAMPAIGN -->

<!-- EDITAR ACTIVECAMPAING -->
<div id="editarActiveCampaign" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="card-title">Editar ActiveCampaign</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="formActiveCampaign">
                <!-- Modal body -->
                <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="id" id="idActiveCampaign">

                    <div class="card card-body">
                        <b>TOKEN</b>
                        <input type="text" name="activecampaign_token" id="activecampaign_token" class="form-control">
                        <b>* Para gerar um token, vá até a guia Configuração > Desenvolvedor > Chave , na activecampaign.<br></b>
                        <br>

                        <b>LINK</b>
                        <input type="text" name="activecampaign_link" id="activecampaign_link" class="form-control">
                    </div>

                    <hr>
                </div>

                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvarActiveCampaign">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>
        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM EDITAR ACTIVECAMPAIGN -->

<!-- MODAL CHATGURU-->
<div id="mostrarchatguru" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração com ChatGuru</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="formchatguru">
                <!-- Modal body -->
                <div class="modal-body" style="overflow-x: auto">

                    <?php if (@$chat) : $i = 1; ?>
                        <script>
                            var dadoschatguru = [];
                        </script>
                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">ID</th>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Endpoit</th>
                                    <th scope="col">Key</th>
                                    <th scope="col">ID da Conta</th>
                                    <th scope="col">ID do Aparelho</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($chat as $chat) : ?>
                                    <tr>
                                        <td><?= $chat->id ?></td>
                                        <td><?= $chat->dataUsuario()->nome ?></td>
                                        <td><?= $chat->endpoint ?></td>
                                        <td><?= $chat->key_api ?></td>
                                        <td><?= $chat->account_id ?></td>
                                        <td><?= $chat->phone_id ?></td>
                                        <td>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#editarchatguru" onclick="editarchatguru(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluirchat(<?= $chat->id ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </td>
                                    </tr>

                                    <script>
                                        dadoschatguru[<?= $i ?>] = {
                                            id: "<?= $chat->id ?>",
                                            usuario: "<?= $chat->usuario ?>",
                                            endpoint: "<?= $chat->endpoint ?>",
                                            chat_number: "<?= $chat->chat_number ?>",
                                            key_api: "<?= $chat->key_api ?>",
                                            account_id: "<?= $chat->account_id ?>",
                                            phone_id: "<?= $chat->phone_id ?>",
                                            msg_cartao_pago: "<?= $chat->msg_cartao_pago ?>",
                                            msg_cartao_analise: "<?= $chat->msg_cartao_analise ?>",
                                            msg_cartao_cancelado: "<?= $chat->msg_cartao_cancelado ?>",
                                            msg_boleto_pago: "<?= $chat->msg_boleto_pago ?>",
                                            msg_boleto_gerado: "<?= $chat->msg_boleto_gerado ?>",
                                            msg_boleto_cancelado: "<?= $chat->msg_boleto_cancelado ?>",
                                            msg_pix_gerado: "<?= $chat->msg_pix_gerado ?>",
                                            msg_pix_pago: "<?= $chat->msg_pix_pago ?>",
                                            msg_pix_cancelado: "<?= $chat->msg_pix_cancelado ?>",
                                            msg_carrinho: "<?= $chat->msg_carrinho ?>",
                                        }
                                    </script>
                                <?php $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    <?php

                    else : ?>
                        Nenhum postback cadastrado
                    <?php endif; ?>
                </div>
                <!-- Fim Modal body -->



                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM CHATGURU -->

<!-- EDITAR CHATGURU -->
<div id="editarchatguru" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="card-title">Chat Guru</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="idchat" id="idchat">

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <b>Endpoint:</b> <small> (obrigatório)</small>
                                <input type="text" name="endpoint" id="endpoint" class="form-control">
                                <!-- <small></small> -->
                                <br><br>
                            </div>

                            <div class="col-md-6">
                                <b>Key:</b><small> (obrigatório)</small>
                                <input type="text" name="key" id="key" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <b>ID da Conta:</b><small> (obrigatório)</small>
                                <input type="text" name="id_conta" id="id_conta" class="form-control">
                                <!-- <small></small> -->
                                <br><br>
                            </div>

                            <div class="col-md-6">
                                <b>ID do Aparelho:</b><small> (obrigatório)</small>
                                <input type="text" name="id_aparelho" id="id_aparelho" class="form-control">
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no CARTÃO esteja em ANÁLISE:</b>
                                <br>
                                <textarea name="cartao_analise" id="cartao_analise" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> está em análise.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no CARTÃO esteja APROVADA:</b>
                                <br>
                                <textarea name="cartao_aprovado" id="cartao_aprovado" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> foi aprovada.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no CARTÃO esteja CANCELADA:</b>
                                <br>
                                <textarea name="cartao_cancelado" id="cartao_cancelado" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, o pagamento do <b>{produto}</b> foi cancelado.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no BOLETO tenha sido GERADO:</b>
                                <br>
                                <textarea name="boleto_gerado" id="boleto_gerado" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, foi gerado um boleto do <b>{produto}</b>.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.</small>
                                <br>
                                <small><b>Observação: </b>Irá ser enviado junto link e a linha digitavel do boleto.</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no BOLETO tenha sido PAGO:</b>
                                <br>
                                <textarea name="boleto_pago" id="boleto_pago" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, recebemos o pagamento do <b>{produto}</b> via boleto.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no BOLETO tenha sido CANCELADO:</b>
                                <br>
                                <textarea name="boleto_cancelado" id="boleto_cancelado" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, o boleto de pagamento do <b>{produto}</b> está cancelado.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no PIX tenha sido GERADA:</b>
                                <br>
                                <textarea name="pix_gerado" id="pix_gerado" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, foi gerado um PIX do <b>{produto}</b>.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no PIX tenha sido PAGO:</b>
                                <br>
                                <textarea name="pix_pago" id="pix_pago" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi pago.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso compra no PIX tenha sido CANCELADA:</b>
                                <br>
                                <textarea name="pix_cancelado" id="pix_cancelado" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi cancelado.
                                    <br>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Mensagem para enviar caso tenha carrinho abandonado:</b>
                                <br>
                                <textarea name="abandono_carrinho" id="abandono_carrinho" maxlength="300" rows="3" cols="95" class="form-control"></textarea>
                                <br>
                                <small>Exemplo: Olá <b>{nome}</b>, vi que você demonstrou interesse no <b>{produto}</b> e gostaria de saber se ficou com alguma dúvida.</small>
                                <br>
                                <small>
                                    <br>
                                    <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                                    <br>
                                    <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                                </small>
                                <br>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card card-body">

                        <span> <b>*Todos esses dados fica disponível na página "Celulares".<br>
                                *Para utilizar a integração é necessario solicitar a ativação na sua conta do ChatGuru.</b></span>
                    </div>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarChatGuru">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvarChatGuru">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>
        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!--  FIM EDITAR CHATGURU -->

<!-- MODAL RDSTATION -->
<div id="mostrarrdstation" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Integração com RD Station</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <!-- Modal body -->
            <div class="modal-body" style="overflow-x: auto">

                <?php if (@$rdstation) : $i = 1; ?>
                    <script>
                        var dadosrdstation = [];
                    </script>
                    <table class="table table-striped" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Client Id</th>
                                <th scope="col">Code</th>
                                <th scope="col">Editar/Excluir</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($rdstation as $rdstation) : ?>
                                <tr>
                                    <td><?= $rdstation->id ?></td>
                                    <td><?= $rdstation->dataUsuario()->nome ?></td>
                                    <td><?= $rdstation->client_id ?></td>
                                    <td><?= $rdstation->code ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-sm" data-target="#editarRdStation" onclick="editarRdStation(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <?php endif ?>
                                        
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluirRdStation(<?= $rdstation->id ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dadosrdstation[<?= $i ?>] = {
                                        id: "<?= $rdstation->id ?>",
                                        usuario: "<?= $rdstation->usuario ?>",
                                        client_id: "<?= $rdstation->client_id ?>",
                                        client_secret: "<?= $rdstation->client_secret ?>",
                                        code: "<?= $rdstation->code ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    Nenhuma integração cadastrada
                <?php endif; ?>
            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL RDSTATION -->

<!-- EDITAR RD -->
<div id="editarRdStation" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="card-title">Editar RdStation</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="formRdStation">
                <!-- Modal body -->
                <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="id" id="idRdStation">

                    <div class="card card-body">
                        <label>Client_id:</label>
                        <input type="text" name="rdstation_id" id="rdstation_id" class="form-control"><br>

                        <label>Client_secret:</label>
                        <input type="text" name="rdstation_secret" id="rdstation_secret" class="form-control"><br>

                        <label>Code:</label>
                        <input type="text" name="rdstation_code" id="rdstation_code" class="form-control"><br>

                        <span><br>* Para obter os dados, siga os passos no link <b><a href="https://developers.rdstation.com/pt-BR/authentication" target="_blank">https://developers.rdstation.com/pt-BR/authentication</a><br></b></span>
                        <span>Video: <b><a href="https://youtu.be/Xj49d7V3Gkw?t=412" target="_blank">https://youtu.be/Xj49d7V3Gkw?t=412</a></b><br><br></span>

                        Substitua os seus dados nessa url para obter o CODE: <b>https://api.rd.services/auth/dialog?client_id={client_id}&redirect_uri={redirect_uri}</b>
                    </div>

                    <hr>
                </div>

                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvarRdStation">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>
        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!--  FIM EDITAR RD -->

<!--Postbacks-->

<div id="editarhotzapp" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title" id="titulo">Editar Integração Hotzapp</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="postback">
                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" name="id" id="idpostback">

                    <input type="text" name="chave_unica" id="chave_unica" class="form-control" value="<?= $usuario->chave_unica ?>" readonly>

                    <div class="row">
                        <div class="col-md-6">
                            <b>Produto</b>
                            <select name="produto" id="produto" class="form-control">
                                <option value="">Selecionar</option>
                                <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                                        <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <b>URL</b>
                            <input type="text" name="url" id="url" class="form-control">
                            <!-- <small> * Gere a URL na sua conta do<p id="titulo"p>e cole aqui.</small> -->
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-4">
                            <b>Enviar no formato JSON?</b>
                            <select name="json" id="json" class="form-control">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                            <small>Na dúvida, deixe como SIM</small>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <b>Eventos</b>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="checkbox" name="evento[0]" value="1" id="evento1" checked> Aguardando Pagamento <br>
                                    <input type="checkbox" name="evento[1]" value="2" id="evento2" checked> Em Análise <br>
                                    <input type="checkbox" name="evento[2]" value="3" id="evento3" checked> Pré-autorizada <br>
                                </div>

                                <div class="col-md-3">
                                    <input type="checkbox" name="evento[3]" value="4" id="evento4" checked> Paga <br>
                                    <input type="checkbox" name="evento[4]" value="5" id="evento5" checked> Cancelada <br>
                                    <input type="checkbox" name="evento[5]" value="6" id="evento6" checked> Devolvida <br>
                                </div>

                                <div class="col-md-5">
                                    <input type="checkbox" name="evento[6]" value="7" id="evento7" checked> Chargeback <br>
                                    <input type="checkbox" name="evento[7]" value="8" id="evento8" checked> Concluída <br>
                                    <input type="checkbox" name="evento[8]" value="9" id="evento9" checked> Bloqueada <br>
                                    <input type="checkbox" name="evento[9]" value="10" id="evento10" checked> Carrinho Abandonado (produtor) <br>
                                </div>

                            </div>
                        </div>

                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-primary form-control" id="testar">Testar</button>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="resultado" cols="30" rows="10" class="form-control" readonly></textarea>
                        </div>
                    </div>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="editarpostback">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->

<!-- Shopify -->
<div id="editarshopify" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Integração com Shopify</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form id="formShopify" action="#">
                <input type="hidden" name="id_shopify" id="id_shopify" value="<?= @$shopify->id; ?>" class="form-control">

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="col-md-5">
                        <input type="checkbox" name="carrinho_shopify" <?= @$shopify->carrinho == 1 ? "checked" : "" ?> id="carrinho_shopify"> Pular carrinho de compras? <br>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <b>URL da loja</b>
                            <input type="text" name="url_shopify" id="url_shopify" value="<?= @$shopify->url; ?>" class="form-control">
                            <small>Exemplo: https://loja-teste.myshopify.com</small>
                        </div>

                        <div class="col-md-6">
                            <b>Chave de API</b>
                            <input type="text" name="api_shopify" id="api_shopify" value="<?= @$shopify->chave_api; ?>" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <b>Senha</b>
                            <input type="text" name="senha_shopify" id="senha_shopify" value="<?= @$shopify->senha; ?>" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <b>Segredo Compartilhado</b>
                            <input type="text" name="segredo_shopify" id="segredo_shopify" value="<?= @$shopify->segredo; ?>" class="form-control">
                        </div>
                    </div>
                    <br>
                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarnotazz">Cancelar</button>
                    <!-- <button class="btn btn-primary" id="salvarshopify">Salvar</button> -->
                    <button class="btn btn-primary" id="imp_produtos">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>
        </div>
        <!-- Fim Modal content-->
    </div>
    <!-- Fim Modal dialog -->
</div>
<!-- Shopify -->

<!--Funções-->
<?php $v->start("scripts") ?>
<script>
    // ------------INICIO BLING---------------//
    $("#atualizarbling").click((e) => {
        e.preventDefault();
        salvarbling("<?= $router->route("bling.editar") ?>");
    });

    function salvarbling(url) {

        var form = $("form[name=formbling]");
        console.log("formulario", form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    function editarbling(i) {
        var id = dadosbling[i]["id"];
        var produto = dadosbling[i]["produto"];
        var api_key = dadosbling[i]["api_key"];

        $(".modal-title").html("Integração Com Bling");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#resultado").css("display", "none");
        $("#idbling").attr({
            "value": id
        });

        if (produto)
            $("#produto option[value=" + produto + "]").prop("selected", true);

        $("#api_key").attr("value", api_key);
    }

    function cancelarbling() {
        $(".modal-title").html("Editar Integração Com Bling");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#idbling").attr({
            "value": ""
        });
        $("#produto option").prop("selected", false);
        $("#api_key").attr("value", "");
    }

    function excluirbling(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse plano?")) {
            $.ajax({
                url: "<?= $router->route("bling.excluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluído com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
    // ------------FIM BLING---------------//


    // ------------INICIO POSTBACKS---------------//
    $("#editarpostback").click((e) => {
        e.preventDefault();
        salvarpostback("<?= $router->route("postback.editar") ?>");
    });

    function salvarpostback(url) {

        var form = $("form[name=postback]");
        console.log(form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    $("#testar").click((e) => {
        e.preventDefault();
        console.log("clique");
        testar();
        console.log("clique");
    });

    function testar() {

        var url = $("#url").val();
        var json = $("#json").val();
        var chave_unica = $("#chave_unica").val();

        $.ajax({
            url: "<?= $router->route("postback.testar") ?>",
            type: "POST",
            data: {
                url: url,
                json: json,
                chave_unica: chave_unica
            },
            success: (callback) => {
                console.log(callback);
                $("#resultado").css("display", "block");
                $("#resultado").prop("value", callback);
            },
            error: (err) => {
                console.log(err);
                $("#resultado").css("display", "block");
                $("#resultado").prop("value", err);
                alert("Houve um erro ao realizar o teste. Verifique se a url está correta!");
            }
        });

    }
    // ----------------------- FIM POSTBACKS ---------------------- //

    // ----------------------- SEÇÃO HOTJAR ----------------------- //
    $("#salvar-hotjar").click((e) => {
        e.preventDefault();
        salvarHotjar("<?= $router->route("hotjar.editar") ?>");
    });

    function salvarHotjar(url) {

        var form = $("form[name=form-hotjar]");
        console.log("formulario", form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    function editarHotjar(i) {
        var id = dadosHotjar[i]["id"];
        var produto = dadosHotjar[i]["produto"];
        var tracking_id = dadosHotjar[i]["tracking_id"];

        $("#id-hotjar").attr("value", id);
        $("#produto option[value=" + produto + "]").prop("selected", true);
        $("#tracking_id").attr("value", tracking_id);
    }

    function cancelarHotjar() {
        $(".modal-title").html("Editar Integração Com Bling");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#idbling").attr({
            "value": ""
        });
        $("#produto option").prop("selected", false);
        $("#api_key").attr("value", "");
    }

    function excluirbling(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse plano?")) {
            $.ajax({
                url: "<?= $router->route("bling.excluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluído com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
    // ----------------------- FIM SEÇÃO HOTJAR ------------------- //

    // ----------------------- SEÇÃO LEADLOVERS ------------------- //
    function tipoIntegracao() {
        if ($("#tipo_integracao").val() == "plataforma") {
            $("#integracao_plataforma").show();
        } else {
            $("#integracao_plataforma").hide();
        }
    }
    tipoIntegracao();
    $("#tipo_integracao").change(() => {
        tipoIntegracao();
    });

    function editarLeadlovers(i) {
        var id = dadosleadlovers[i]["id"];
        var usuario = dadosleadlovers[i]["usuario"];
        var descricao = dadosleadlovers[i]["descricao"];
        var token = dadosleadlovers[i]["token"];
        var produto = dadosleadlovers[i]["produto"];
        var tipo_integracao = dadosleadlovers[i]["tipo_integracao"];
        var acao = dadosleadlovers[i]["acao"];
        var evento = dadosleadlovers[i]["evento"];
        var maquina = dadosleadlovers[i]["maquina"];
        var funil = dadosleadlovers[i]["funil"];
        var nivel = dadosleadlovers[i]["nivel"];
        // console.log(id);
        $(".modal-title").html("Editar Integração Com Leadlovers");
        $("#desc_leadlovers").val(descricao);
        $("#id_leadlovers").val(id);

        $("#desc_leadlovers").val(descricao);
        $("#token_leadlovers").val(token);
        $("#produto option[value=" + produto + "]").prop("selected", true);
        $("#tipo_integracao option[value=" + tipo_integracao + "]").prop("selected", true);
        $("#acao_lead option[value=" + acao + "]").prop("selected", true);

        var evento = evento.split(",");
        evento.forEach((indice, valor) => {
            $("#evento" + indice).prop("checked", true);
            // console.log("indice", indice);
        });

        carregaMaquinas(token, i);
    }

    function carregaMaquinas(token, i = 1) {

        var maquina = dadosleadlovers[i]["maquina"];
        var url = "<?= $router->route("maquina.lead") ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                token: token,
            },
            success: (callback) => {
                // console.log(callback);
                resposta = JSON.parse(callback);
                console.log(resposta[0].MachineCode);

                if (resposta[0].MachineCode != 0) {
                    var option = "<option>Selecione uma máquina </option>";
                    var option = "";
                    $.each(resposta, function(i, obj) {
                        // console.log("I:", i);
                        // console.log("obj:", obj);

                        if (maquina == obj.MachineCode)
                            option += "<option name=" + obj.MachineName + " value=" + obj.MachineCode + " selected>" + obj.MachineName + "</option>";
                        else
                            option += "<option name=" + obj.MachineName + " value=" + obj.MachineCode + ">" + obj.MachineName + "</option>";
                    })
                    $('#maquina_lead').html(option);
                    var cod_maquina = $("#maquina_lead").val();
                    console.log("MAQUINA", cod_maquina);
                    carregaFunil(token, cod_maquina, i);
                } else {
                    alert("Erro ao carregar Máquinas");
                }
            }
        })
    }

    function carregaFunil(token, maquina, i = 1) {
        var funil = dadosleadlovers[i]["funil"];
        console.log("FUNIL", funil, dadosleadlovers);
        var url = "<?= $router->route("funil.lead") ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                token: token,
                maquina: maquina,
            },
            success: (callback) => {
                console.log(callback);

                resposta = JSON.parse(callback);
                // console.log(resposta[0].SequenceCode);
                $('#funil_lead').attr('readonly', false);
                $('#funil_lead').attr('disabled', false);

                if (resposta[0].SequenceCode != 0) {
                    var option = "<option>-- Selecione um funil </option>";
                    var option = "";

                    $.each(resposta, function(i, obj) {
                        // console.log("I:", i);
                        // console.log("obj:", obj);

                        if (funil == obj.SequenceCode)
                            option += "<option value=" + obj.SequenceCode + " selected>" + obj.SequenceName + "</option>";
                        else
                            option += "<option value=" + obj.SequenceCode + ">" + obj.SequenceName + "</option>";
                    })
                    $('#funil_lead').html(option);
                    var cod_funil = $("#funil_lead").val();

                    carregaNivel(token, maquina, cod_funil, i);
                } else {
                    alert("Erro ao carregar Máquinas");
                }
            }
        })
    }

    function carregaNivel(token, maquina, funil, i = 1) {
        var nivel = dadosleadlovers[i]["nivel"];
        var url = "<?= $router->route("nivel.lead") ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                token: token,
                maquina: maquina,
                funil: funil,
            },
            success: (callback) => {
                console.log(callback);
                $('#nivel_lead').attr('readonly', false);
                $('#nivel_lead').attr('disabled', false);

                resposta = JSON.parse(callback);
                // console.log(resposta[0].SequenceCode);

                if (resposta[0].Sequence != 0) {
                    var option = "<option>-- Selecione um Nivel --</option>";
                    var option = "";

                    $.each(resposta, function(i, obj) {
                        // console.log("I:", i);
                        // console.log("obj:", obj);

                        if (nivel == obj.Sequence)
                            option += "<option value=" + obj.Sequence + " selected>" + obj.Subject + "</option>";
                        else
                            option += "<option value=" + obj.Sequence + ">" + obj.Subject + "</option>";
                    })
                    $('#nivel_lead').html(option);
                } else {
                    alert("Erro ao carregar Nivel");
                }
            }
        })
    }

    function salvaLeadlovers() {

        console.log("ID:", id);
        if (produto == "") {
            alert("Selecione um produto");
            $("#produto").focus();
            return;
        }
        var id = $("#id_leadlovers").val();
        var token = $("#token_leadlovers").val();
        var descricao = $("#desc_leadlovers").val();
        var tipo_integracao = $("#tipo_integracao").val();
        var produto = $("#formLead [name=produto_lead] :selected").val();
        var acao = $("#acao_lead").val();

        var eventos = new Array();
        $("input[name='evento[]']:checked").each(function() {
            // valores inteiros usa-se parseInt
            eventos.push(parseInt($(this).val()));
            // string
            // eventos.push($(this).val());
        });

        var cod_maquina = $("#maquina_lead").val();
        var maquina = $('#maquina_lead :selected').text();

        var cod_funil = $("#funil_lead").val();
        var funil = $("#funil_lead :selected").text();

        var cod_nivel = $("#nivel_lead").val();
        var nivel = $("#nivel_lead :selected").text();
        // alert(cod_maquina, maquina);

        var url = "<?= $router->route("editar.lead") ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                id: id,
                token: token,
                descricao: descricao,
                produto: produto,
                tipo_integracao: tipo_integracao,
                acao: acao,
                eventos: eventos,
                cod_maquina: cod_maquina,
                maquina: maquina,
                cod_funil: cod_funil,
                funil: funil,
                cod_nivel: cod_nivel,
                nivel: nivel,
            },
            success: (callback) => {
                console.log(callback);
                if (callback == "Salvo com sucesso") {
                    alert("Salvo com sucesso!");
                    // location.reload();
                } else {
                    alert("Erro." + callback);
                }
            }
        });
    }

    $("#funil_lead").change(function() {
        atualizaFunil();
    });


    $("#maquina_lead").change(function() {
        atualizaMaquina();
    });

    function atualizaFunil() {
        var token = $("#token_leadlovers").val();
        var cod_maquina = $("#maquina_lead").val();
        var cod_funil = $("#funil_lead").val();

        if (cod_funil > 0) {
            carregaNivel(token, cod_maquina, cod_funil);
        }
    }

    function atualizaMaquina() {
        var cod_maquina = $("#maquina_lead").val();
        // alert("maquina " + cod_maquina);

        if (cod_maquina > 0) {
            var token = $("#token_leadlovers").val();
            carregaFunil(token, cod_maquina);
        }
    }

    function excluirLead(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa integração?")) {
            $.ajax({
                url: "<?= $router->route("excluir.lead") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }

    // ----------------- FIM SEÇÃO LEADLOVERS ------------------- //

    function editarhotzapp(i) {
        var id = dadoshotzapp[i]["id"];
        var produto = dadoshotzapp[i]["produto"];
        var url = dadoshotzapp[i]["url"];
        var eventos = dadoshotzapp[i]["eventos"];
        var json = dadoshotzapp[i]["json"];

        console.log(id, produto, url, eventos);

        $(".modal-title").html("Editar Postback");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#resultado").css("display", "none");
        $("#idpostback").attr({
            "value": id
        });
        $("#produto option[value=" + produto + "]").prop("selected", true);
        $("#json option[value=" + json + "]").prop("selected", true);
        $("#url").attr("value", url);

        var evento = eventos.split(",");

        for (i = 1; i < 11; i++) {
            $("#evento" + i).prop("checked", false);
        }

        evento.forEach((indice, valor) => {
            $("#evento" + indice).prop("checked", true);
        });

    }

    function editarisei(i) {
        var id = dadosisei[i]["id"];
        var produto = dadosisei[i]["produto"];
        var url = dadosisei[i]["url"];
        var eventos = dadosisei[i]["eventos"];
        var json = dadosisei[i]["json"];

        console.log(id, produto, url, eventos);

        $(".modal-title").html("Editar Postback");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#resultado").css("display", "none");
        $("#idpostback").attr({
            "value": id
        });
        $("#produto option[value=" + produto + "]").prop("selected", true);
        $("#json option[value=" + json + "]").prop("selected", true);
        $("#url").attr("value", url);

        var evento = eventos.split(",");

        for (i = 1; i < 11; i++) {
            $("#evento" + i).prop("checked", false);
        }

        evento.forEach((indice, valor) => {
            $("#evento" + indice).prop("checked", true);
        });
    }

    function editarhotbillet(i) {
        var id = dadoshotbillet[i]["id"];
        var produto = dadoshotbillet[i]["produto"];
        var url = dadoshotbillet[i]["url"];
        var eventos = dadoshotbillet[i]["eventos"];
        var json = dadoshotbillet[i]["json"];

        console.log(id, produto, url, eventos);

        $(".modal-title").html("Editar Postback");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#resultado").css("display", "none");
        $("#idpostback").attr({
            "value": id
        });
        $("#produto option[value=" + produto + "]").prop("selected", true);
        $("#json option[value=" + json + "]").prop("selected", true);
        $("#url").attr("value", url);

        var evento = eventos.split(",");

        for (i = 1; i < 11; i++) {
            $("#evento" + i).prop("checked", false);
        }

        evento.forEach((indice, valor) => {
            $("#evento" + indice).prop("checked", true);
        });
    }

    function cancelar() {
        $(".modal-title").html("Editar Postback");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#id").attr({
            "value": ""
        });
        $("#produto option").prop("selected", false);
        $("#json option").prop("selected", false);
        $("#url").attr("value", "");

        for (i = 1; i < 11; i++) {
            $("#evento" + i).prop("checked", true);
        }
    }

    function excluirpostback(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa integração?")) {
            $.ajax({
                url: "<?= $router->route("postback.excluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
    // ------------FIM POSTBACKS---------------//

    // ------------INICIO NOTAZZ---------------//
    $("#atualizarnotazz").click((e) => {
        e.preventDefault();
        salvarnotazz("<?= $router->route("notazz.editar") ?>");
    });

    function salvarnotazz(url) {

        var form = $("form[name=formnotazz]");
        console.log("formulario", form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    function editarnotazz(i) {
        var id = dadosnotazz[i]["id"];
        var api_notazz = dadosnotazz[i]["api_notazz"];
        var data_nota = dadosnotazz[i]["data_nota"];
        var produto = dadosnotazz[i]["produto"];
        var codigo_cnae = dadosnotazz[i]["codigo_cnae"];
        var codigo_lc116 = dadosnotazz[i]["codigo_lc116"];
        var enviar_email_cliente = dadosnotazz[i]["enviar_email_cliente"];
        var cidade_padrao = dadosnotazz[i]["cidade_padrao"];
        var uf_padrao = dadosnotazz[i]["uf_padrao"];
        var cep_padrao = dadosnotazz[i]["cep_padrao"];

        $(".modal-title").html("Editar API");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#idnotazz").attr({ "value": id });
        $("#api_notazz").attr("value", api_notazz);
        $("#data_nota option[value=" + data_nota + "]").prop("selected", true);
        $("#produto option[value=" + produto + "]").prop("selected", true);
        $("#codigo_cnae").attr("value", codigo_cnae);
        $("#codigo_lc116").attr("value", codigo_lc116);
        $("#enviar_email_cliente_editar option[value=" + enviar_email_cliente + "]").prop("selected", true);
        $("#cidade_padrao_editar").val(cidade_padrao);
        $("#uf_padrao_editar").val(uf_padrao);
        $("#cep_padrao_editar").val(cep_padrao);
    }

    function cancelarnotazz() {
        $(".modal-title").html("Integração Notazz");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#idnotazz").attr({ "value": "" });
        $("#api_notazz").attr("value", "");
        $("#data nota option").prop("selected", false);
        $("#produto option").prop("selected", false);
        $("#codigo_cnae").attr("value", "");
        $("#codigo_lc116").attr("value", "");
        $("#cidade_padrao_editar").val("");
        $("#uf_padrao_editar").val("");
        $("#cep_padrao_editar").val("");
    }

    function excluirnotazz(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa api?")) {
            $.ajax({
                url: "<?= $router->route("notazz.excluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
    // ------------FIM NOTAZZ---------------//


    // ------------INICIO NOTIFICAÇÕES INTELIGENTES---------------//
    $("#atualizarni").click((e) => {
        e.preventDefault();
        salvarni("<?= $router->route("whatsapp.editarNI") ?>");
    });

    function salvarni(url) {

        var form = $("form[name=formni]");
        console.log("formulario", form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    function editarni(i) {
        var id = dadosni[i]["id"];
        var token = dadosni[i]["token"];
        var integracao_id = dadosni[i]["integracao_id"];

        console.log(id);

        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#idni").attr({
            "value": id
        });
        $("#token").attr("value", token);
        $("#integracao_id").attr("value", integracao_id);
    }

    function cancelarni() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#idni").attr({
            "value": ""
        });
        $("#token").attr("value", "");
        $("#integracao_id").attr("value", "");
    }

    function excluirni(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa integração?")) {
            $.ajax({
                url: "<?= $router->route("whatsapp.excluirNI") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }

    // ------------FIM NOTIFICAÇÕES INTELIGENTES---------------//


    // ------------INICIO CHATGURU---------------//

    function editarchatguru(i) {
        var id = dadoschatguru[i]["id"];
        var usuario = dadoschatguru[i]["usuario"];
        var endpoint = dadoschatguru[i]["endpoint"];
        var key_api = dadoschatguru[i]["key_api"];
        var account_id = dadoschatguru[i]["account_id"];
        var phone_id = dadoschatguru[i]["phone_id"];
        var msg_cartao_pago = dadoschatguru[i]["msg_cartao_pago"];
        var msg_cartao_analise = dadoschatguru[i]["msg_cartao_analise"];
        var msg_cartao_cancelado = dadoschatguru[i]["msg_cartao_cancelado"];
        var msg_boleto_pago = dadoschatguru[i]["msg_boleto_pago"];
        var msg_boleto_gerado = dadoschatguru[i]["msg_boleto_gerado"];
        var msg_boleto_cancelado = dadoschatguru[i]["msg_boleto_cancelado"];
        var msg_pix_gerado = dadoschatguru[i]["msg_pix_gerado"];
        var msg_pix_pago = dadoschatguru[i]["msg_pix_pago"];
        var msg_pix_cancelado = dadoschatguru[i]["msg_pix_cancelado"];
        var msg_carrinho = dadoschatguru[i]["msg_carrinho"];

        console.log(id);
        $("#editarchatguru").css("display", "inline");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#idchat").attr("value", id);

        $("#endpoint").attr("value", endpoint);
        $("#key").attr("value", key_api);
        $("#id_conta").attr("value", account_id);
        $("#id_aparelho").attr("value", phone_id);

        $("#cartao_aprovado").val(msg_cartao_pago);
        $("#cartao_analise").val(msg_cartao_analise);
        $("#cartao_cancelado").val(msg_cartao_cancelado);
        $("#boleto_pago").val(msg_boleto_pago);
        $("#boleto_gerado").val(msg_boleto_gerado);
        $("#boleto_cancelado").val(msg_boleto_cancelado);
        $("#pix_gerado").val(msg_pix_gerado);
        $("#pix_pago").val(msg_pix_pago);
        $("#pix_cancelado").val(msg_pix_cancelado);
        $("#abandono_carrinho").val(msg_carrinho);


    }

    $("#salvarChatGuru").click((e) => {
        e.preventDefault();
        salvarChatGuru("<?= $router->route("editar.chatguru") ?>");
    });

    function salvarChatGuru(url) {

        var form = $("form");
        console.log(url);

        console.log(form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }
    // FIM FUNÇÕES CHATGURU

    function editarActiveCampaign(i) {
        var id = dadosactivecampaign[i]["id"];
        var usuario = dadosactivecampaign[i]["usuario"];
        var token = dadosactivecampaign[i]["token"];
        var link = dadosactivecampaign[i]["link"];


        $("#idActiveCampaign").attr("value", id);
        $("#activecampaign_token").attr("value", token);
        $("#activecampaign_link").attr("value", link);

    }

    function excluirActiveCampaign(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa integração?")) {
            $.ajax({
                url: "<?= $router->route("activecampaign.excluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }

    $("#salvarActiveCampaign").click((e) => {
        e.preventDefault();
        salvarActiveCampaign("<?= $router->route("activecampaign.editar") ?>");
    });

    function salvarActiveCampaign(url) {

        var form = $("form[name=formActiveCampaign]");
        console.log(url);

        console.log(form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    function editarRdStation(i) {
        var id = dadosrdstation[i]["id"];
        var usuario = dadosrdstation[i]["usuario"];
        var client_id = dadosrdstation[i]["client_id"];
        var client_secret = dadosrdstation[i]["client_secret"];
        var code = dadosrdstation[i]["code"];


        $("#idRdStation").attr("value", id);
        $("#rdstation_id").attr("value", client_id);
        $("#rdstation_secret").attr("value", client_secret);
        $("#rdstation_code").attr("value", code);

    }

    function excluirRdStation(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa integração?")) {
            $.ajax({
                url: "<?= $router->route("rdstation.excluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }

    $("#salvarRdStation").click((e) => {
        e.preventDefault();
        salvarRdStation("<?= $router->route("rdstation.editar") ?>");
    });

    function salvarRdStation(url) {

        var form = $("form[name=formRdStation]");
        console.log(url);

        console.log(form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    // INICIO FUNÇÕES MEX10
    function editarMex10(i) {
        var id = dadosmex[i]["id"];
        var usuario = dadosmex[i]["usuario"];
        var token = dadosmex[i]["token"];
        var msg_cartao_pago = dadosmex[i]["msg_cartao_pago"];
        var msg_cartao_analise = dadosmex[i]["msg_cartao_analise"];
        var msg_cartao_cancelado = dadosmex[i]["msg_cartao_cancelado"];
        var msg_boleto_pago = dadosmex[i]["msg_boleto_pago"];
        var msg_boleto_gerado = dadosmex[i]["msg_boleto_gerado"];
        var msg_boleto_cancelado = dadosmex[i]["msg_boleto_cancelado"];
        var msg_pix_gerado = dadosmex[i]["msg_pix_gerado"];
        var msg_pix_pago = dadosmex[i]["msg_pix_pago"];
        var msg_pix_cancelado = dadosmex[i]["msg_pix_cancelado"];
        var msg_carrinho = dadosmex[i]["msg_carrinho"];

        console.log(id, msg_boleto_cancelado);

        $("#idMex").attr("value", id);
        $("#mex_token").attr("value", token);

        $("#mex_cartao_aprovado").prop("value", msg_cartao_pago);
        $("#mex_cartao_analise").prop("value", msg_cartao_analise);
        $("#mex_cartao_cancelado").prop("value", msg_cartao_cancelado);
        $("#mex_boleto_pago").prop("value", msg_boleto_pago);
        $("#mex_boleto_gerado").prop("value", msg_boleto_gerado);
        $("#mex_boleto_cancelado").prop("value", msg_boleto_cancelado);
        $("#mex_pix_gerado").prop("value", msg_pix_gerado);
        $("#mex_pix_pago").prop("value", msg_pix_pago);
        $("#mex_pix_cancelado").prop("value", msg_pix_cancelado);
        $("#mex_abandono_carrinho").prop("value", msg_carrinho);


    }

    $("#salvarMex10").click((e) => {
        e.preventDefault();
        salvarChatGuru("<?= $router->route("mex.editar") ?>");
    });

    function salvarMex10(url) {

        var form = $("form[name=formMex10]");
        console.log(url);

        console.log(form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    function excluirmex(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa integração?")) {
            $.ajax({
                url: "<?= $router->route("mex.excluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
    // ------------FIM CHATGURU---------------//


    // ------------SHOPIFY---------------//
    $("#imp_produtos").click((e) => {
        e.preventDefault();
        var form = $("#formShopify");

        $.ajax({
            url: "<?= $router->route("shopify.editar") ?>",
            type: "POST",
            data: form.serialize(),
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log(callback);
                if (callback == "Produto importado com sucesso!") {
                    console.log("Produtos salvos com sucesso");
                    // console.log("Produto importado com sucesso!");
                    alert("Integração Salva Com Sucesso! Seus produtos já foram importados");
                    location.reload();
                } else {
                    // console.log("Erro ao salvar produtos");
                    alert("Dados atualizados com sucesso!");
                    location.reload();
                }
            }
        });
    });
    // ------------FIM SHOPIFY---------------//

    // INICIO FUNÇÕES CHATGURU
    //     function editarchatguru(i) {
    //     console.log(id);
    //     $("#url_shopify").attr("value", < ?= $shopify->url?>);
    //     $("#api_shopify").attr("value", < ?= $shopify->chave_api?>);
    //     $("#senha_shopify").attr("value", < ?= $shopify->senha?>);
    //     $("#segredo_shopify").attr("value", < ?= $shopify->segredo?>);
    //     $("#carrinho_shopify").attr("value", < ?= $shopify->carrinho?>);
    // }

    // FIM FUNÇÕES CHATGURU
</script>
<?php $v->end() ?>
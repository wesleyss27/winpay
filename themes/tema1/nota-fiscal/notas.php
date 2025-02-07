<?php

$v->layout("_theme");

$status = array(
    1 => "Pendente",
    2 => "Autorizada",
    3 => "Cancelada",
    4 => "Rejeitada",
    5 => "Denegada",
);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Notas</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                Para ver suas notas fiscais é preciso integrar com o <a href="<?= url("notas/integracao") ?>">Notazz</a>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">
                <!--div class="alert bg-warning">Em Fase de Testes</div-->

                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-4">
                            Pedido:
                            <input type="text" name="pedido" id="pedido" class="form-control">
                        </div>

                        <div class="col-md-1">
                            <br>
                            <button class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>
                <br>

                <?php if (@$notas) : $i = 1; ?>

                    <script>
                        var dados = [];
                    </script>

                    <table class="table table-striped projects" style="font-size:13px" border>
                        <?php foreach ($notas as $nota) : ?>
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">ID</th>
                                    <th scope="col">ID da nota</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Venda</th>
                                    <th scope="col">Data Cadastro</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr style="font-family:inter">

                                    <th scope="row"><?= $nota->id; ?></th>
                                    <th><?= mb_strimwidth(@$nota->id_nota, 0, 50, "..."); ?></th>
                                    <td><?= $status[$nota->status] ?></td>
                                    <td><?= $nota->venda . " - " . ($nota->venda ? $nota->dataVenda()->transacao : "-") ?></td>
                                    <td><?= formataDataHora($nota->created_at); ?></td>
                                </tr>

                                <tr>
                                    <td colspan="5">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <b>Chave: </b> <?= $nota->chave ?><br>
                                                <b>Codigo de Verificação: </b> <?= $nota->codigo_verificacao ?><br>
                                            </div>

                                            <div class="col-md-6">
                                                <b>Emissão: </b> <?= $nota->emissao ?><br>
                                                <b>Motivo Status: </b> <?= $nota->motivo_status ?><br>
                                            </div>
                                        </div>

                                        <b>Link PDF: </b> <?= $nota->link_pdf ?><br>
                                        <b>Link XML: </b> <?= $nota->link_xml ?><br>

                                    </td>
                                </tr>



                            </tbody>
                        <?php $i++;
                        endforeach; ?>
                    </table>

                <?php
                    //EXIBE A NAVEGAÇÃO DO PAGINADOR
                    $link = URL_BASE . "/notas/";

                    //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                    $paginador->paginas($total, $pagina, $link);

                else : ?>
                    Nenhuma nota fiscal encontrada
                <?php endif; ?>
            </div>
            <!-- FIM CARD BODY -->

        </div>
    </section>
</div>
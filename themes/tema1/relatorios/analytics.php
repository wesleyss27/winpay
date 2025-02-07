<?php $v->layout("_theme"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">
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
        <?php if ($analytics) : ?>
            <div class="card card-body">
                <h4>Urls mais acessadas</h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Url acessada</th>
                            <th>Acessos</th>
                            <th>Produto</th>
                            <th>Plano</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($analytics as $analytics) : ?>
                            <tr>
                                <td><?= $analytics->url ?></td>
                                <td><?= $analytics->cont ?></td>
                                <td><?= @$analytics->dataProduto()->nome ?></td>
                                <td><?= @$analytics->dataPlano()->nome ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>

        <?php
        if ($analyticsA) : ?>
            <div class="card card-body">
                <h4>Origem dos acessos</h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Url de origem</th>
                            <th>Quantidade</th>
                            <th>Produto</th>
                            <th>Plano</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($analyticsA as $analyticsA) : ?>
                            <tr>
                                <td><?= $analyticsA->url_anterior ?></td>
                                <td><?= $analyticsA->url_anterior == "" ? $an : $analyticsA->cont ?></td>
                                <td><?= $analyticsA->url_anterior != "" ? @$analyticsA->dataProduto()->nome : "-" ?></td>
                                <td><?= @$analyticsA->dataPlano()->nome ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
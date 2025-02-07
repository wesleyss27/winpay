<?php
$v->layout("_theme");
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row titulo-pagina">
                <div class="col-md-12">
                    <h1 style="font-family:inter"><?= $title ?></h1>
                </div>
            </div>
            <hr>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <?php if ($cursos) : ?>
                    <?php foreach ($cursos as $curso) : ?>
                        <!-- single prod -->
                        <div class="col-md-3">
                            <a href="<?= url("turmas/alunos/" . $curso->id) ?>">
                                <div class="card card-body">
                                    <div style="text-align: center;">
                                        <?php if ($curso->imagem != "") : ?>
                                            <img class="img-curso" src="<?= url("uploads/cursos/" . $curso->imagem); ?>" alt="<?= mb_strimwidth($curso->nome, 0, 20, "..."); ?>"><br>
                                        <?php else : ?>
                                            <img class="img-curso" src="<?= url("uploads/cursos/default.png"); ?>" alt="<?= mb_strimwidth($curso->nome, 0, 20, "..."); ?>"><br>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-foote" style="text-align: center;">
                                        <hr>
                                        <h5 style="font-family:inter; text-transform:uppercase; color:#333">
                                            <strong><?= mb_strimwidth($curso->nome, 0, 20, "..."); ?></strong>
                                        </h5>


                                        <?php $alunosCount = $turmaAluno->find("usuario = $curso->usuario AND curso = $curso->id")->count(); ?>
                                        <i class="fa-regular fa-heart" style="color: #EC0043"></i> <?= $alunosCount ?> alunos <br>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <h4>Nenhum cursos encontrado!</h4>
                <?php endif; ?>

                <!-- end single prod -->
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>
<!-- /.content-wrapper -->
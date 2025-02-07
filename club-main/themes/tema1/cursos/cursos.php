<?php $v->layout("_theme"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content" style="margin-top: 20px;">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <h1 style="font-family:inter"><?= $title ?></h1>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <?php if ($cursos) : ?>
        <div class="card card-body">
          <div class="row">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">Nome</th>
                  <th scope="col">Alunos</th>
                  <th scope="col">Aulas</th>
                </tr>
              </thead>

              <tbody>

                <?php foreach ($cursos as $curso) : ?>
                  <?php $alunosCount = $turmaAluno->find("usuario = $curso->usuario AND curso = $curso->id")->count(); ?>
                  <?php $aulasCount = $aula->find("curso = $curso->id")->count(); ?>
                  <tr>
                    <td>
                      <a href="<?= url("cursos/detalhe/" . $curso->id) ?>">

                        <div class="row">
                          <div class="col-2">
                            <?php if ($curso->imagem != "") : ?>
                              <img class="img-curs" src="<?= url("uploads/cursos/" . $curso->imagem); ?>" alt="<?= mb_strimwidth($curso->nome, 0, 20, "..."); ?>" width="50px"><br>
                            <?php else : ?>
                              <img class="img-curs" src="<?= url("uploads/cursos/default.png"); ?>" alt="<?= mb_strimwidth($curso->nome, 0, 20, "..."); ?>" width="50px"><br>
                            <?php endif; ?>
                          </div>

                          <div class="col">
                            <?= mb_strimwidth($curso->nome, 0, 20, "..."); ?>
                          </div>
                        </div>
                      </a>
                    </td>

                    <td>
                      <i class="fa-regular fa-heart" style="color: #EC0043"></i> <?= $alunosCount ?> alunos
                    </td>

                    <td>
                      <i class="fas fa-video"></i> <?= $aulasCount ?> aulas
                    </td>
                  </tr>
                <?php endforeach; ?>

              </tbody>
            </table>
          </div>



        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE . "/cursos/p/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);

      else :
        ?>
          <h4>Nenhum cursos encontrado!</h4>
        <?php endif; ?>
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
<?php
$v->layout("_theme");
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <section class="faixa">
      <div class="wrapper">
        <ul class="miniaturas row d-ib">
          <div class="card-group">

            <?php if ($turmaaluno) : ?>
              <?php foreach ($turmaaluno as $cursos) : ?>

                <div class="card-produtos" style="width: 20rem;">
                  <?php if ($cursos->dataCurso()->imagem != "") : ?>
                    <img class="card-img-produtos1" src="<?= url("uploads/cursos/" . $cursos->dataCurso()->imagem); ?>" alt="Card image cap">
                  <?php else : ?>
                    <img src="<?= url("uploads/cursos/default.png"); ?>" alt="Card image cap" class="card-img-produtos1"><br>
                  <?php endif; ?>

                  <div class="card-body">
                    <h2 class="card-title">Curso - <?= mb_strimwidth($cursos->dataCurso()->nome, 0, 20, "..."); ?></h2>

                    <!-- <div class="progress" style="height: 15px;">
                        <div class="progress-bar" role="progressbar" style="width: 44%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">44%</div>
                      </div> -->

                    <p class="card-text"><?= mb_strimwidth($cursos->descricao, 0, 100, "..."); ?></p>
                    <p class="card-category">
                      <!-- <label>
                          <br>Data da compra: <b>21/11/2020</b></br>
                          <br>Pagamento: <b>21/11/2020</b></br>
                        </label> -->
                      <a href="<?= url("minhas-compras/viewcursos/" . $cursos->dataCurso()->id) ?>" class="btn btn-lg btn-bordered btn-block btn-outline-primary">Acesse</a>
                  </div>
                </div>
              <?php endforeach; ?>


            <?php
              $link = URL_BASE . "/produtos/p/";

              //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
              $paginador->paginas($total, $pagina, $link);

            else :
            ?>
              <h4>Nenhum cursos encontrado!</h4>
            <?php
            endif;
            ?>
        </ul>
      </div>
    </section>


  </section>

  <!-- Main content -->

  <!-- /.content -->

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>
<!-- /.content-wrapper -->
<?php

$v->layout("_theme");
$i = 1;

$status = array(
  1 => "EM ANÁLISE",
  2 => "APROVADO",
  3 => "REPROVADO",
  4 => "BLOQUEADO"
);
$ativo = array(
  1 => "SIM",
  0 => "NÃO"
);
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h3 style="font-family:inter"><?= $title; ?></h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>

            <li class="breadcrumb-item active"><?= $title; ?></li>
          </ol>
        </div>

      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <span id="total"> Total: <?= $count; ?></span>

    <!-- Default box -->
    <div class="card">

      <div class="card-body p-0">
        <?php if ($notificacoes) : ?>
          <script>
            var dados = [];
          </script>

          <div style="overflow: auto;">

            <table class="table table-striped projects" style="font-size:13px">

              <tbody>
                <?php foreach ($notificacoes as $notificacao) : ?>
                  <tr style="font-family:inter">
                    <td>
                      <img src="<?= url("themes/imgs/logos/logo_octa_pay_$ (24).png") ?>" alt="" width="70px">
                      <span style="color: #023770"><b><?= $notificacao->titulo ? $notificacao->titulo : substr($notificacao->texto, 0, 20) . "..." ?></b></span><br>
                      <?= $notificacao->texto; ?> <br>
                      <?= formataDataHora($notificacao->created_at) ?>
                    </td>
                    <td>
                      <a class="btn btn-primary btn-sm" href="<?= $notificacao->link; ?>">
                        <i class="fas fa-folder"></i> Ver
                      </a>
                    </td>
                  </tr>

                <?php $i++;
                endforeach; ?>
              </tbody>
            </table>

          <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $link = URL_BASE . "/notificacoes/p/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
          $paginador->paginas($total, $pagina, $link);

        else :
          ?>
            <h4>Nenhum pixel encontrado!</h4>
          <?php
        endif;
          ?>

          </div>

      </div>


  </section>

</div>

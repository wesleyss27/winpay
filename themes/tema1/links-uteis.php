<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1) {
  header("Location: " . URL_BASE);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><?= $title; ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item active">Links úteis</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">05 link(s)</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>

        </div>
      </div>
      <div class="card-body p-0">
        <table class="table table-striped projects">
          <thead>
            <tr>
              <th style="width: 1%"> # </th>
              <th style="width: 20%"> FAQ </th>
              <th style="width: 30%"> Link </th>
              <th style="width: 20%"> </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td> # </td>
              <td> <a> Buscar pedidos St4rt </a> </td>
              <td> <small> <?= url("webhook/vendas") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("webhook/vendas") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> <a> Cria notificação webhook Wirecard </a> </td>
              <td> <small> <?= url("webhook/notificacao") ?> </small> </td>
              <td class="project-actions text-right">
                <button class="btn btn-primary btn-sm" href="<?= url("webhook/notificacao") ?>" target="_blank" disabled>
                  <i class="fas fa-external-link-alt"></i> Ver
                </button>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> <a> Cadastrar comissões </a> </td>
              <td> <small> <?= url("comissao/cadastro") ?> </small> </td>
              <td class="project-actions text-right">
                <button class="btn btn-primary btn-sm" href="<?= url("comissao/cadastro") ?>" target="_blank" disabled>
                  <i class="fas fa-external-link-alt"></i> Ver
                </button>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> <a> Buscar comissões </a> </td>
              <td> <small> <?= url("comissao/busca") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("comissao/busca") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> <a> Cadastrar saldo </a> </td>
              <td> <small> <?= url("comissao/saldo") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("comissao/saldo") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> Rockets </td>
              <td> <small> <?= url("rockets") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("rockets") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> Atualizar vendas </td>
              <td> <small> <?= url("asaas/vendas") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("asaas/vendas") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> Assinaturas </td>
              <td> <small> <?= url("assinaturas/verificar-assinaturas") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("assinaturas/verificar-assinaturas") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <!-- <tr>
              <td> # </td>
              <td> <a> Boleto Bradesco </a> </td>
              <td> <small> <?= url("checkout/boleto-pedidos") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("checkout/boleto-pedidos") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> <a> Pix Bradesco </a> </td>
              <td> <small> <?= url("checkout/pix-pedidos") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("checkout/pix-pedidos") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr>

            <tr>
              <td> # </td>
              <td> <a> Atualizar Pix eSitef </a> </td>
              <td> <small> <?= url("checkout/atualizar-pix") ?> </small> </td>
              <td class="project-actions text-right">
                <a class="btn btn-primary btn-sm" href="<?= url("checkout/atualizar-pix") ?>" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Ver
                </a>
              </td>
            </tr> -->

          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
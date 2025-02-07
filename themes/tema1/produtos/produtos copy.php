<?php
$v->layout("_theme");

$status = array(
  0 => "Rascunho",
  1 => "Em Análise",
  2 => "Aprovado",
  3 => "Reprovado",
  4 => "Bloqueado",
  5 => "Excluido",
  6 => "Inativado",
);

$bg = array(
  0 => "bg-info",
  1 => "bg-info",
  2 => "bg-success",
  3 => "bg-danger",
  4 => "bg-danger",
  5 => "bg-danger",
  6 => "bg-warning",
);

include "tipo.php";

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6" style="margin-bottom:10px">
          <h1 style="font-family:inter">Produtos</h1>
        </div>
        <div class="col-sm-6" style="font-family:inter">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="painel.php">Início</a></li>
            <li class="breadcrumb-item active">Produtos</li>
          </ol>
        </div>
      </div>

      <form action="<?= $router->route("produtos.home"); ?>" style="font-family:inter; font-size:12px">
        <div class="callout callout-primary">
          <div class="row">
            <div class="col-12 col-md-3">
              <input type="text" name="nome" class="form-control" placeholder="Nome do produto" value="<?= @$_GET["venda"]; ?>">
            </div>

            <div class="col-12 col-md-3">
              <select name="categoria" id="categoria" class="form-control">
                <option value="" selected>Categoria...</option>
                <?php $i = 1;
                foreach ($categoria as $categorias) : ?>
                  <option value="<?= $i ?>"><?= $categorias ?></option>
                <?php $i++;
                endforeach ?>
              </select>
            </div>

            <div class="col-12 col-md-3">
              <select name="status" id="status" class="form-control">
                <option value="" selected>Status...</option>
                <option value="0">Rascunho</option>
                <option value="1">Em análise</option>
                <option value="2">Aprovado</option>
                <option value="3">Reprovado</option>
                <option value="4">Bloqueado</option>
              </select>
            </div>

            <div class="col-md-2">
              <select name="usuario" id="usuario" class="form-control">
                <option value="" selected>Produtor...</option>
                <?php if (@$produtores) : ?>
                  <?php foreach (@$produtores as $produtor) : ?>
                    <option value="<?= $produtor->id ?>"><?= $produtor->nome ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="callout callout-primary">
          <div class="row">
            <div class="col-md-3">
              <select name="tipo" id="tipo" class="form-control">
                <option value="" selected>Tipo...</option>
                <?php $i = 1;
                foreach ($tipoProduto as $tipo) : ?>
                  <option value="<?= $i ?>"><?= $tipo ?></option>
                <?php $i++;
                endforeach ?>
              </select>
            </div>

            <div class="col-md-1">
              <button type="submit" name="" class="form-control btn-primary"> <i class="fa fa-search"></i></button>
            </div>

          </div>
        </div>
      </form>

    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <div class="row">

        <?php if ($produtos) : ?>
          <?php foreach ($produtos as $produto) : ?>
            <!-- single prod -->
            <a href="<?= url("produtos/detalhe/" . $produto->id) ?>">
              <div class="col-md-3">
                <div class="ribbon-wrapper ribbon-lg">
                  <div class="ribbon <?= $bg[$produto->status] ?> text-lg" style="text-transform:none">
                    <?php echo $status[$produto->status];
                    if ($produto->rascunho == 1) echo " - Rascunho"; ?>
                  </div>
                </div>

                <div class="card card-widget widget-user-2 shadow-sm" style="border-radius:0">

                  <div class="widget-user-header bg-white">
                    <div align="center">
                      <?php if ($produto->foto != "") : ?>
                        <img src="<?= url("uploads/produtos/" . $produto->foto); ?>" alt="<?= $produto->id; ?>" width="200px" height="200px"><br>
                      <?php else : ?>
                        <img src="<?= url("uploads/produtos/default.jpg"); ?>" alt="<?= $produto->id; ?>" width="200px" height="200px"><br>
                      <?php endif; ?>
                    </div>


                  </div>
                  <div class="card-footer p-0">
                    <ul class="nav flex-column" style="font-family:inter">
                      <br />
                      <h5 align="center" style="font-family:inter; text-transform:uppercase; color:#333">
                        <strong><?= mb_strimwidth($produto->nome, 0, 20, "..."); ?></strong>
                      </h5>


                      <li class="nav-item">
                        <a href="<?= url("produtos/detalhe/" . $produto->id) ?>" class="nav-link">
                          Categoria: <span class="float-right badge bg-info" style="margin-top:3px"><?= mb_strimwidth($categoria[$produto->categoria], 0, 20, "..."); ?></span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>

              </div>
            </a>
          <?php endforeach; ?>
        <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $link = URL_BASE . "/produtos/p/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
          $paginador->paginas($total, $pagina, $link);

        else :
        ?>
          <h4>Nenhum produto encontrado!</h4>
        <?php
        endif;
        ?>
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
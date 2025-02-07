<?php
include __DIR__ . "/tipo.php";

$status = array(
  0 => "Rascunho",
  1 => "Em Análise",
  2 => "Aprovado",
  3 => "Reprovado",
  4 => "Bloqueado"
);

$bg = array(
  0 => "bg-info",
  1 => "bg-info",
  2 => "bg-success",
  3 => "bg-danger",
  4 => "bg-danger"
);

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<?php $v->layout("_theme"); ?>

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
            <li class="breadcrumb-item"><a href="painel.php">Início</a></li>
            <li class="breadcrumb-item active">Produtos</li>
          </ol>
        </div>
      </div>

      <div class="callout callout-primary">



        <form action="" method="post" style="font-family:inter; font-size:12px">

          <div class="row">
            <div class="col-12 col-md-3">

              <input type="text" name="nome" class="form-control" placeholder="Nome do produto" value="<?= @$_GET["venda"]; ?>">
            </div>

            <div class="col-12 col-md-3">

              <select name="categoria" id="categoria" class="form-control">
                <option disabled selected>Categoria...</option>
                <?php $i = 1;
                foreach ($categoria as $categorias) : ?>
                  <option value="<?= $i ?>"><?= $categorias ?></option>
                <?php $i++;
                endforeach ?>
              </select>
            </div>

            <div class="col-12 col-md-3">

              <select name="status" id="status" class="form-control">
                <option disabled selected>Status...</option>
                <option value="0">Rascunho</option>
                <option value="1">Em análise</option>
                <option value="2">Aprovado</option>
                <option value="3">Reprovado</option>
                <option value="4">Bloqueado</option>
              </select>
            </div>

            <div class="col-12 col-md-1">

              <button type="submit" name="" class="form-control btn-primary"> <i class="fa fa-search"></i></button>
            </div>

          </div>
        </form>

      </div>

    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <div class="row">
        <?php if ($afiliacao) : ?>
          <?php
          foreach ($afiliacao as $afiliacao) :
            if ($afiliacao->dataProduto()->excluir == 0 && $afiliacao->dataProduto()->status == 2) :
          ?>
              <!-- single prod -->
              <a href="<?= url("produtos/detalhe/" . $afiliacao->dataProduto()->id) ?>">
                <div class="col-md-3">
                  <div class="ribbon-wrapper ribbon-lg">
                    <div class="ribbon <?= $bg[$afiliacao->status] ?> text-lg" style="text-transform:none">
                      <?php echo $status[$afiliacao->status]; ?>
                    </div>
                  </div>

                  <div class="card card-widget widget-user-2 shadow-sm" style="border-radius:0">

                    <div class="widget-user-header bg-white">
                      <div align="center">
                        <?php if ($afiliacao->dataProduto()->foto != "") : ?>
                          <img src="<?= url("uploads/produtos/" . $afiliacao->dataProduto()->foto); ?>" alt="<?= $afiliacao->dataProduto()->id; ?>" width="200px" height="200px"><br>
                        <?php else : ?>
                          <img src="<?= url("uploads/produtos/default.jpg"); ?>" alt="<?= $afiliacao->dataProduto()->id; ?>" width="200px" height="200px"><br>
                        <?php endif; ?>
                      </div>

                    </div>
                    <div class="card-footer p-0">
                      <ul class="nav flex-column" style="font-family:inter">
                        <br />
                        <h5 align="center" style="font-family:inter; text-transform:uppercase; color:#333">
                          <strong><?= mb_strimwidth($afiliacao->dataProduto()->nome, 0, 20, "..."); ?></strong>
                        </h5>

                        <li class="nav-item"><a href="" class="nav-link"></a></li>
                        <!--li class="nav-item">
                      <a href="<?= url("produtos/detalhe/" . $afiliacao->dataProduto()->id) ?>" class="nav-link">
                        
                        Ganhe até <span class="float-right badge bg-warning" style="margin-top:3px">
                            <?php
                            if ($afiliacao->tipo_comissao == 0) {
                              $comissao = $afiliacao->dataProduto()->preco * ($afiliacao->comissao_afiliado / 100);
                              echo "R$" . number_format($comissao, 2, ",", ".");
                            } else if ($afiliacao->tipo_comissao == 1) {
                              echo "R$" . number_format($afiliacao->comissao_afiliado, 2, ",", ".");
                            }
                            ?>
                            </span>
                      </a>
                    </li-->
                      </ul>
                    </div>
                  </div>

                </div>
              </a>
          <?php endif;
          endforeach; ?>
        <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $link = URL_BASE . "/produtos/promovidos/p/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
          $paginador->paginas($total, $pagina, $link);

        else :
        ?>
          <h4>Não existem produtos cadastrados!</h4>
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
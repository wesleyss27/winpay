<?php
$v->layout("_theme");
include "produtos/tipo.php";
if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
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
            <li class="breadcrumb-item active">Marketplace</li>
          </ol>
        </div>
      </div>

      <form action="#" style="font-family:inter; font-size:12px">

        <div class="callout callout-primary">
          <div class="row">
            <div class="col-12 col-md-3">
              <input type="text" name="" class="form-control " placeholder="Nome do Produto" maxlength="100">
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
              <select name="comissao" id="comissao" class="form-control">
                <option disabled selected>Comissionamento...</option>
                <option value="1">Primeiro clique</option>
                <option value="2">Último clique</option>
              </select>
            </div>


            <div class="col-12 col-md-3">
              <select name="formato" id="formato" class="form-control">
                <option value="">Tipo do produto...</option>
                <option value="8">Físico</option>
                <option value="1">Digital</option>
              </select>
            </div>
          </div>
        </div>

        <div class="callout callout-primary">
          <div class="row">
            <div class="col-12 col-md-3">
              <a href="?recente=true" class="btn btn-primary form-control text-white">Mais Recentes</a>
            </div>

            <div class="col-md-3">
              <a href="?decolaram" class="btn btn-primary form-control text-white ">Mais Decolaram</a>
            </div>

            <div class="col-12 col-md-1">
              <button type="submit" class="btn btn-success">Buscar</button>
            </div>
          </div>
        </div>
      </form>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <?php if ($produtos) : $j = 1; ?>
        <div class="row">
          <?php foreach ($produtos as $produto) : ?>
            <!-- single prod -->
            <div class="card-produto" style="cursor: pointer" onclick="redirecionar('<?= url('produtos/detalhe/' . $produto->id) ?>')">

              <div class="imagem-produto-mkt center">
                <a href="<?= url("produtos/detalhe/" . $produto->id) ?>">
                  <?php if ($produto->foto != "") : ?>
                    <img src="<?= url("uploads/produtos/" . $produto->foto); ?>" alt="<?= $produto->id; ?>"><br>
                  <?php else : ?>
                    <img src="<?= url("uploads/produtos/default.jpg"); ?>" alt="<?= $produto->id; ?>"><br>
                  <?php endif; ?>
                </a>
              </div>

              <div class="card-produto-corpo">
                <a href="<?= url("produtos/detalhe/" . $produto->id) ?>" class="nav-link">
                  <h5 align="center" style="font-family:inter; text-transform:uppercase; color:#333">
                    <strong><?= mb_strimwidth($produto->nome, 0, 20, "..."); ?></strong>
                  </h5>
                </a>
              </div>

              <ul class="nav flex-column">
                <li class="nav-item">
                  <a href="<?= url("produtos/detalhe/" . $produto->id) ?>" class="nav-link">
                    Categoria: <span class="float-right badge bg-azul-1" style="margin-top:3px"><?= mb_strimwidth($categoria[$produto->categoria], 0, 20, "..."); ?></span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= url("produtos/detalhe/" . $produto->id) ?>" class="nav-link">
                    Tipo: <span class="float-right badge bg-azul-1" style="margin-top:3px"><?= mb_strimwidth($tipoProduto[$produto->tipo_produto], 0, 20, "..."); ?></span>
                  </a>
                </li>

                <li class="nav-item nav-link">
                  Ganhe até:
                  <div class="float-right badge bg-warning" style="font-size:16px">
                    <?php
                    if ($produto->tipo_comissao == 0) {
                      $comissao = $produto->preco * ($produto->comissao_afiliado / 100);
                      echo "R$" . number_format($comissao, 2, ",", ".");
                    } else if ($produto->tipo_comissao == 1) {
                      echo "R$" . number_format($produto->comissao_afiliado, 2, ",", ".");
                    }
                    ?>
                  </div>
                </li>

                <li class="nav-item">
                  <a href="<?= url("produtos/detalhe/" . $produto->id) ?>" class="nav-link" style="color:green">
                    Avaliações:
                    <div class="float-right" style="font-size:16px">
                      <?php if (round($produto->rockets) == 0) : ?>
                        <img src="<?= url("themes/tema1/images/foguete2.png"); ?>" width="22px" alt="" title="Rockets <?= $produto->rockets; ?>">
                        <?php
                      else :
                        $round = round($produto->rockets);
                        for ($i = 1; $i <= $round; $i++) :
                        ?>
                          <img src="<?= url("themes/tema1/images/foguete1.png"); ?>" width="22px" alt="" title="Rockets <?= $produto->rockets; ?>">
                      <?php
                        endfor;
                      endif;
                      ?>
                    </div>
                  </a>
                </li>
              </ul>

              <div style="text-align: center">
                <button onclick='location.href="<?= url("produtos/detalhe/" . $produto->id) ?>"' type="submit" class="form-control bg-azul-gradient" style="border-radius: 0 0 10px 10px; font-family:inter"> <i class="fa fa-check"></i> Promover</button>
              </div>

            </div>

            <?php if ($j % 4 == 0) : ?>
        </div>

        <div class="row">
        <?php endif; ?>
        <!-- end single prod -->
      <?php
            $j++;
          endforeach;
      ?>
        </div>

      <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE . "/produtos/p/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);

      else :
      ?>
        <h4>Nenhum produto encontrado!</h4>
      <?php endif; ?>
    </div>
  </section>
  <!-- /.content -->

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>
<!-- /.content-wrapper -->

<?php $v->start("scripts"); ?>
<script>
  function redirecionar(url) {
    location.href = url;
  }
</script>
<?php $v->end() ?>
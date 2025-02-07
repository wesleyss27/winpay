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
              <a href="?recente=true" class="btn btn-info form-control text-white">Mais Recentes</a>
            </div>

            <div class="col-md-3">
              <a href="?decolaram" class="btn btn-info form-control text-white ">Mais Decolaram</a>
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
            <div class="col-md-3">
              <div class="card" style="cursor: pointer" onclick="redirecionar('<?= url('produtos/detalhe/' . $produto->id) ?>')">

                <div class="imagem-produto-mkt center">
                  <a href="<?= url("produtos/detalhe/" . $produto->id) ?>">
                    <?php if ($produto->foto != "") : ?>
                      <img src="<?= url("uploads/produtos/" . $produto->foto); ?>" alt="<?= $produto->id; ?>"><br>
                    <?php else : ?>
                      <img src="<?= url("uploads/produtos/default.jpg"); ?>" alt="<?= $produto->id; ?>"><br>
                    <?php endif; ?>
                  </a>
                </div>

                <div class="card-body">
                  <div class="card-produto-corpo">
                    <a href="<?= url("produtos/detalhe/" . $produto->id) ?>">
                      <h5 style="font-family:inter; text-transform:uppercase; color:#333">
                        <strong><?= mb_strimwidth($produto->nome, 0, 20, "..."); ?></strong>
                      </h5>

                      <small style="color: #666">Por <?= $produto->dataProdutor()->nome ?></small>
                    </a>
                  </div>

                  <br>
                  <div class="card-produto-corpo">
                    <small>Ganhe até: </small><br>
                    <span style="font-size:20px; font-weight: 600">
                      <?php
                      if ($produto->tipo_comissao == 0) {
                        $comissao = $produto->preco * ($produto->comissao_afiliado / 100);
                        echo "R$" . number_format($comissao, 2, ",", ".");
                      } else if ($produto->tipo_comissao == 1) {
                        echo "R$" . number_format($produto->comissao_afiliado, 2, ",", ".");
                      }
                      ?>
                    </span>
                  </div>

                  <!-- <li class="nav-item">
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
                  </li> -->

                  <?php
                  $rockets = round($produto->rockets);
                  $posicao = $rockets > 0 ? $rockets * 22 : 18;
                  $posicao = $posicao > 100 ? 100 : $posicao;

                  // echo "($posicao)";
                  ?>

                  <br>

                  <div style="background-image: linear-gradient(to right, #ff0, #f00);height: 15px; border-radius: 10px; position: relative;">
                    <div style="background: #2E1046; border-radius: 50%; padding: 10px; position: absolute; left: 3%; top: 50%; transform: translate(-50%, -50%);">
                      <i class="fas fa-bolt text-white" style="width: 20px;"></i>
                    </div>

                    <?php if ($posicao >= 100) : ?>
                      <div style="background: #fff; box-shadow: 0px 0px 3px #666; border-radius: 50%; padding: 5px; position: absolute; left: <?= $posicao ?>%; top: 50%; transform: translate(-50%, -50%);">
                        <img src="<?= url("themes/tema1/images/sol.png"); ?>" alt="" width="22px">
                      </div>
                    <?php else : ?>
                      <div style="height: 35px; background: #844BF0; border-radius: 50%; padding: 5px; position: absolute; left: <?= $posicao ?>%; top: 50%; transform: translate(-50%, -50%);">
                        <img src="<?= url("themes/tema1/images/foguete1.png"); ?>" alt="" width="20px">
                      </div>
                    <?php endif ?>
                  </div>


                  <!-- <div style="text-align: center">
                    <button onclick='location.href="<?= url("produtos/detalhe/" . $produto->id) ?>"' type="submit" class="form-control bg-azul-gradient" style="border-radius: 0 0 10px 10px; font-family:inter"> <i class="fa fa-check"></i> Promover</button>
                  </div> -->
                </div>
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
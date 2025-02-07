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

include __DIR__ . "/tipo.php";

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

            <?php if (@$shopify) : ?>
              <div class="col-md-4">
                <button type="button" id="sinc_shopify" name="" class="form-control btn-primary" style="display:block; margin-left: 105%;">Sincronizar Produtos Shopify <i class="fas fa-sync-alt"></i></button>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </form>

    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">

      <?php if ($produtos) : $i = 1; ?>
        <div class="row">
          <?php foreach ($produtos as $produto) : ?>
            <!-- single prod -->
            <div class="card-produto" style="cursor: pointer" onclick="redirecionar('<?= url('produtos/detalhe/' . $produto->id) ?>')">

              <div class="imagem-produto-mkt center">
                <a href="<?= url("produtos/detalhe/" . $produto->id) ?>">
                  <?php if ($produto->foto != "" && $produto->shopify == 0) : ?>
                    <img src="<?= url("uploads/produtos/" . $produto->foto); ?>" alt="<?= $produto->id; ?>"><br>
                  <?php elseif ($produto->foto != "" && $produto->shopify == 1) : ?>
                    <img src="<?= $produto->foto; ?>" alt="<?= $produto->id; ?>"><br>
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
                    Categoria: <span class="float-right badge bg-info" style="margin-top:3px"><?= mb_strimwidth($categoria[$produto->categoria], 0, 20, "..."); ?></span>
                  </a>
                </li>
              </ul>

              <div class="<?= $bg[$produto->status] ?>" style="border-radius: 0 0 10px 10px; text-align: center">
                <?php echo $status[$produto->status]; ?>
              </div>

            </div>

            <?php if ($i % 4 == 0) : ?>
        </div>
        <div class="row">
        <?php endif; ?>
        <!-- end single prod -->
      <?php $i++;
          endforeach; ?>
        </div>
    </div>
  <?php
        echo "<br>";
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE . "/produtos/p/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);
        echo "<br>";
      else :
  ?>
    <h4>Nenhum produto encontrado!</h4>
  <?php endif; ?>
</div>
</section>
</div>
<!-- END Content Wrapper. Contains page content -->

<?php $v->start("scripts"); ?>
<script>
  function redirecionar(url) {
    location.href = url;
  }


  $("#sinc_shopify").click((e) => {
    e.preventDefault();

    var url = "<?= $router->route("shopify.produtos"); ?>";
    console.log(url);

    $.ajax({
      url: url,
      // data: form.serialize(),
      type: "GET",
      //datatype: "json",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        // console.log("callback ", callback);
        alert("Produtos sincronizados com sucesso!");
        location.reload();
      }
    });
  });
</script>
<?php $v->end() ?>
<?php
$v->layout("_theme");

if ($section->getTipo() == 4) {
  $produto = $produtos->find("excluir = 0 AND status != 6 AND status != 0 AND usuario = " . $section->getId())->fetch(true);
  // $produtor = $afiliacao->find("excluir = 0 AND status != 6 AND status != 0 AND afiliado = " . $section->getId())->fetch(true);
} elseif ($section->getTipo() == 5) {
  // $produto = $afiliacao->find("excluir = 0 AND status != 6 AND status != 0 AND afiliado = " . $section->getId())->fetch(true);
} else {
  $produto = $produtos->find("excluir = 0 AND status != 6 AND status != 0")->fetch(true);
}
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- HEADER PAGE -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h3 style="font-family:inter"><?= $title ?></h3>
        </div>

        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item active">Notas</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- FIM HEADER PAGE -->

  <!-- Main content -->
  <section class="content">
    <div class="card">
      <div class="card-body">
        <form action="#" method="post">
          <div class="row">
            <div class="col-md-3">
              Produto:
              <select name="produto" id="produto" class="form-control" required>
                <option value="">Selecione o Produto...</option>
                <?php if ($section->getTipo() == 5) : ?>
                  <?php foreach ($produto as $produto) : ?>
                    <option value="<?= $produto->dataProduto()->id ?>" <?php if (@$_POST["produto"] == $produto->id) echo "selected";
                                                                        echo "" ?>><?= $produto->dataProduto()->nome ?></option>
                  <?php endforeach; ?>
                <?php else : ?>
                  <?php foreach ($produto as $produto) : ?>
                    <option value="<?= $produto->id ?>" <?php if (@$_POST["produto"] == $produto->id) echo "selected";
                                                        echo "" ?>><?= $produto->nome ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
                <?php if (@$produtor) : foreach ($produtor as $produtor) : ?>
                    <option value="<?= $produtor->dataProduto()->id ?>" <?php if (@$_POST["produto"] == $produtor->dataProduto()->id) echo "selected";
                                                                        echo "" ?>><?= $produtor->dataProduto()->nome ?></option>
                <?php endforeach;
                endif; ?>
              </select>
            </div>
            <div class="col-md-3">
              Data Início:
              <input type="date" class="form-control" name="inicio" value="<?= @$_POST["inicio"] ?>">
            </div>
            <div class="col-md-3">
              Data Fim:
              <input type="date" class="form-control" name="fim" value="<?= @$_POST["fim"] ?>">
            </div>
            <div class="col-md-3">
              <br>
              <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
          </div>

        </form>
      </div>
    </div>

    <!-- Main content total vendas-->

    <section class="content">

      <!-- Default box -->
      <?php if (@$_POST["produto"]) : ?>
        <div class="row" style="margin: 0 0 20px 0">
          <div class="col-md-3 center">
            <div class="row">
              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total de vendas: <br>
                <h4><?= $count; ?></h4>
              </div>

              <div class="col-md-1"></div>
            </div>
          </div>

          <div class="col-md-3 center">
            <div class="row">

              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total faturado*: <br>
                <h4><?= @$somaVendas->soma ? "R$ " . number_format($somaVendas->soma, 2, ",", ".") : "-"; ?></h4>
              </div>

              <div class="col-md-1"></div>
            </div>
          </div>

          <div class="col-md-2 center">
            <div class="row">

              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total cartão: <span class="badge bg-warning">
                  <?php
                  if ($dados["cartoes"] > 0)
                    $p = ($dados["cartoes"] / ($dados["cartoes"] + $dados["boletos"] + $dados["pix"]) * 100);
                  else
                    $p = "0";
                  echo number_format($p, 2, ",", ".");
                  ?>%</span><br>
                <b><?php
                    if ($dados["cartoes"] > 0)
                      $p = ($dados["cartoes"]);
                    else
                      $p = "0";
                    echo number_format($p);
                    ?></b>
              </div>

              <div class="col-md-1"></div>
            </div>
          </div>

          <div class="col-md-2 center">
            <div class="row">

              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total boletos: <span class="badge bg-warning">
                  <?php
                  if ($dados["boletos"] > 0)
                    $p = ($dados["boletos"] / ($dados["cartoes"] + $dados["boletos"] + $dados["pix"]) * 100);
                  else
                    $p = "0";
                  echo number_format($p, 2, ",", ".");
                  ?>%</span><br>
                <b><?php
                    if ($dados["boletos"] > 0)
                      $p = ($dados["boletos"]);
                    else
                      $p = "0";
                    echo number_format($p);
                    ?></b>
              </div>

              <div class="col-md-1"></div>
            </div>
          </div>

          <div class="col-md-2 center">
            <div class="row">

              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total PIX: <span class="badge bg-warning">
                  <?php
                  if ($dados["pix"] > 0)
                    $p = ($dados["pix"] / ($dados["cartoes"] + $dados["boletos"] + $dados["pix"]) * 100);
                  else
                    $p = "0";
                  echo number_format($p, 2, ",", ".");
                  ?>%</span><br>
                <b><?php
                    if ($dados["pix"] > 0)
                      $p = ($dados["pix"]);
                    else
                      $p = "0";
                    echo number_format($p);
                    ?></b>
              </div>
            </div>
          </div>
        </div>

        <!-- Main content total vendas pagas-->

        <section class="content">

          <!-- Default box -->

          <div class="row" style="margin: 0 0 20px 0">
            <div class="col-md-3 center">
              <div class="row">
                <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                  Total de vendas pagas: <br>
                  <h4><?= $dados["cartoesPagos"] + $dados["boletosPagos"] + $dados["pixPagos"]; ?></h4>
                </div>

                <div class="col-md-1"></div>
              </div>
            </div>

            <div class="col-md-3 center">
              <div class="row">

                <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                  Total faturado pago*: <br>
                  <h4><?= @$dados["valorVendasPagas"] ? "R$ " . number_format($dados["valorVendasPagas"], 2, ",", ".") : "-"; ?></h4>
                </div>

                <div class="col-md-1"></div>
              </div>
            </div>

            <div class="col-md-2 center">
              <div class="row">

                <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                  Total cartão pago: <span class="badge bg-warning">
                    <?php
                    if ($dados["cartoesPagos"] > 0)
                      $p = ($dados["cartoesPagos"] / ($dados["cartoesPagos"] + $dados["boletosPagos"] + $dados["pixPagos"]) * 100);
                    else
                      $p = "0";
                    echo number_format($p, 2, ",", ".");
                    ?>%</span><br>
                  <b><?php
                      if ($dados["cartoesPagos"] > 0)
                        $p = ($dados["cartoesPagos"]);
                      else
                        $p = "0";
                      echo number_format($p);
                      ?></b>
                </div>

                <div class="col-md-1"></div>
              </div>
            </div>

            <div class="col-md-2 center">
              <div class="row">

                <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                  Total boleto pago: <span class="badge bg-warning">
                    <?php
                    if ($dados["boletosPagos"] > 0)
                      $p = ($dados["boletosPagos"] / ($dados["cartoesPagos"] + $dados["boletosPagos"] + $dados["pixPagos"]) * 100);
                    else
                      $p = "0";
                    echo number_format($p, 2, ",", ".");
                    ?>%</span><br>
                  <b><?php
                      if ($dados["boletosPagos"] > 0)
                        $p = ($dados["boletosPagos"]);
                      else
                        $p = "0";
                      echo number_format($p);
                      ?></b>
                </div>

                <div class="col-md-1"></div>
              </div>
            </div>

            <div class="col-md-2 center">
              <div class="row">

                <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                  Total PIX pago: <span class="badge bg-warning">
                    <?php
                    if ($dados["pixPagos"] > 0)
                      $p = ($dados["pixPagos"] / ($dados["cartoesPagos"] + $dados["boletosPagos"] + $dados["pixPagos"]) * 100);
                    else
                      $p = "0";
                    echo number_format($p, 2, ",", ".");
                    ?>%</span><br>
                  <b><?php
                      if ($dados["pixPagos"] > 0)
                        $p = ($dados["pixPagos"]);
                      else
                        $p = "0";
                      echo number_format($p);
                      ?></b>
                </div>
              </div>
            </div>
          </div>

          <!-- Default box -->
          <div class="card p-0">
            <div class="card-body">
              <?php if ($afiliacao) : ?>

                <script>
                  var dados = [];
                </script>
                <div style="overflow-x: auto">

                  <table class="table table-striped projects" style="font-size:13px">
                    <thead>
                      <tr>
                        <th scope="col">Afiliado</th>
                        <th scope="col" style="text-align:center">Vendas</th>
                        <th scope="col" style="text-align:center">Cartão</th>
                        <th scope="col" style="text-align:center">Boleto</th>
                        <th scope="col" style="text-align:center">Pix</th>
                      </tr>

                      <tr>
                        <th scope="col"></th>
                        <th scope="col" style="text-align:center">Pagas | Reembolsadas | Total</th>
                        <th scope="col" style="text-align:center">Pagos | Reembolsadas | Total</th>
                        <th scope="col" style="text-align:center">Pagos | Reembolsadas | Total</th>
                        <th scope="col" style="text-align:center">Pagos | Reembolsadas | Total</th>
                      </tr>
                    </thead>

                    <tbody>

                      <?php
                      $i = 1;

                      function formataComissao($tipo, $valor)
                      {
                        if ($tipo == 0) {
                          return number_format($valor, 2, ",", ".") . "%";
                        } else {
                          return "R$ " . number_format($valor, 2, ",", ".");
                        }
                      }

                      if (@$_POST["inicio"] && $_POST["fim"])
                        $filtros = "data_finalizada BETWEEN '" . $_POST["inicio"] . " 00:00:00' AND '" . $_POST["fim"] . " 23:59:59' AND ";

                      foreach ($afiliacao as $afiliacao) :

                        $vendapaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND status_transacao = 4")->count();
                        $vendareembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND (status_transacao = 6 or status_transacao = 7)")->count();
                        $vendatotal = $vendapaga + $vendareembolsada;

                        $valorpaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND status_transacao = 4", null, "SUM(valor_liquido) as valor")->fetch();
                        $valorreembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND (status_transacao = 6 or status_transacao = 7)", null, "SUM(valor_liquido) as valor")->fetch();
                        $valortotal = $valorpaga->valor + $valorreembolsada->valor;

                        $vendacartaopaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)")->count();
                        $vendacartaoreembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND forma_pagamento = 1 AND (status_transacao = 6 || status_transacao = 7)")->count();
                        $vendacartaototal = $vendacartaopaga + $vendacartaoreembolsada;

                        $valorcartaopaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND status_transacao = 4 AND forma_pagamento = 1", null, "SUM(valor_liquido) as valor")->fetch();
                        $valorcartaoreembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND (status_transacao = 6 or status_transacao = 7) AND forma_pagamento = 1", null, "SUM(valor_liquido) as valor")->fetch();
                        $valorcartaototal = $valorcartaopaga->valor + $valorcartaoreembolsada->valor;

                        $vendaboletopaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)")->count();
                        $vendaboletoreembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND forma_pagamento = 2 AND (status_transacao = 6 || status_transacao = 7)")->count();
                        $vendaboletototal = $vendaboletopaga + $vendaboletoreembolsada;

                        $valorboletopaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND status_transacao = 4 AND forma_pagamento = 2", null, "SUM(valor_liquido) as valor")->fetch();
                        $valorboletoreembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND (status_transacao = 6 or status_transacao = 7) AND forma_pagamento = 2", null, "SUM(valor_liquido) as valor")->fetch();
                        $valorboletototal = $valorboletopaga->valor + $valorboletoreembolsada->valor;

                        $vendapixpaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)")->count();
                        $vendapixreembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND forma_pagamento = 3 AND (status_transacao = 6 || status_transacao = 7)")->count();
                        $vendapixtotal = $vendapixpaga + $vendapixreembolsada;

                        $valorpixpaga = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND status_transacao = 4 AND forma_pagamento = 3", null, "SUM(valor_liquido) as valor")->fetch();
                        $valorpixreembolsada = $vendas->find(@$filtros . "afiliado = $afiliacao->afiliado AND produto = $afiliacao->produto AND (status_transacao = 6 or status_transacao = 7) AND forma_pagamento = 3", null, "SUM(valor_liquido) as valor")->fetch();
                        $valorpixtotal = $valorpixpaga->valor + $valorpixreembolsada->valor;

                        $dados = $usuarioDados->find("usuario = $afiliacao->afiliado")->fetch();
                      ?>

                        <tr class="cli" id="afiliacao<?= $afiliacao->afiliado; ?>">
                          <td><?= $afiliacao->dataAfiliado()->nome . " <br> " . $afiliacao->dataAfiliado()->email . " <br> " . @$dados->telefone; ?></td>
                          <td style="text-align:center">
                            <?= $vendapaga . " | " . $vendareembolsada . " | " . @$vendatotal; ?> <br>
                            <?= "R$ " . number_format($valorpaga->valor, 2, ",", ".") . " | " . "R$ " . number_format($valorreembolsada->valor, 2, ",", ".") . " | " . "R$ " . number_format(@$valortotal, 2, ",", "."); ?>
                          </td>
                          <td style="text-align:center">
                            <?= $vendacartaopaga . " | " . $vendacartaoreembolsada . " | " . @$vendacartaototal; ?> <br>
                            <?= "R$ " . number_format($valorcartaopaga->valor, 2, ",", ".") . " | " . "R$ " . number_format($valorcartaoreembolsada->valor, 2, ",", ".") . " | " . "R$ " . number_format(@$valorcartaototal, 2, ",", "."); ?>
                          </td>
                          <td style="text-align:center">
                            <?= $vendaboletopaga . " | " . $vendaboletoreembolsada . " | " . @$vendaboletototal; ?> <br>
                            <?= "R$ " . number_format($valorboletopaga->valor, 2, ",", ".") . " | " . "R$ " . number_format($valorboletoreembolsada->valor, 2, ",", ".") . " | " . "R$ " . number_format(@$valorboletototal, 2, ",", "."); ?>
                          </td>
                          <td style="text-align:center">
                            <?= $vendapixpaga . " | " . $vendapixreembolsada . " | " . @$vendapixtotal; ?> <br>
                            <?= "R$ " . number_format($valorpixpaga->valor, 2, ",", ".") . " | " . "R$ " . number_format($valorpixreembolsada->valor, 2, ",", ".") . " | " . "R$ " . number_format(@$valorpixtotal, 2, ",", "."); ?>
                          </td>

                        </tr>

                      <?php $i++;
                      endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else : ?>
                <h4>Você ainda não tem afiliados para esse produto</h4>
            <?php endif;
            endif; ?>
            </div>
          </div>
        </section>
</div>

<?= $v->start("scripts"); ?>
<script>
  function buscar() {
    $.ajax({
      url: "<?= $router->route("relatorios.busca") ?>",
      data: {
        produto: 4,
        inicio: "2020-07-01",
        fim: "2020-12-16"
      },
      type: "POST",
      success: function(callback) {
        console.log(callback);
      }
    });
  }
</script>

<?= $v->end(); ?>
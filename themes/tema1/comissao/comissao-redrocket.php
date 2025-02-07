<?php
$v->layout("_theme");

if (@$saldo == "")
  $saldo = 0;

if ($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2) {
  header("Location: " . URL_BASE);
}
?>

<div class="content-wrapper">
  <br />

  <section class="content">

    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->

      <div class="row">
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Hoje</h5>
            <h3><?= @$hoje ? "R$ " . number_format($hoje, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Ontem</h5>
            <h3><?= @$ontem ? "R$ " . number_format($ontem, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Esse mês</h5>
            <h3><?= @$mes ? "R$ " . number_format($mes, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->

        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Sacado no Mês</h5>
            <h3><?= @$saqueMes ? "R$ " . number_format(-$saqueMes->valor, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->

      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Disponível</h5>
            <h3><?= @$saldo ? "R$ " . number_format($saldo + (@$saldoPagarme->available_amount / 100), 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Pendente</h5>
            <h3><?= @$pendente ? "R$ " . number_format($pendente, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Total</h5>
            <h3><?= @$saldo ? "R$ " . number_format($saldo + $pendente, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->

        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="card card-body">
            <h5>Saldo Bloqueado</h5>
            <h3><?= @$bloqueado ? "R$ " . number_format(-$bloqueado, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->

      <div class="card">
        <div class="card-header">
          <a class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter">Solicitar Saque <i class="fa fa-chevron-right"></i> </a>
        </div>
      </div>

      <!-- Main row -->
      <!-- Default box -->
      <div class="card">

        <div class="card-body p-0">

          <?php if (@$comissao) : ?>
            <div style="overflow-x: auto">
              <table class="table table-striped projects" style="font-family:inter; font-size:13px">
                <thead>
                  <tr>
                    <?php if ($_SESSION["tipo"] == 1) : ?>
                      <th scope="col">#</th>
                    <?php endif; ?>

                    <th scope="col">Valor</th>
                    <th scope="col">Descrição</th>

                    <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                      <th scope="col">Comissionado</th>
                    <?php endif; ?>

                    <th scope="col">Produto</th>
                    <th scope="col">Venda</th>
                    <th scope="col">Data</th>
                    <th scope="col">Disponível Em:</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($comissao as $comissao) :
                    /** CALCULA COMISSÃO MENOS A TAXA DE TRANSAÇÃO */
                    //$total = $comissao->valor - ($comissao->valor * $comissao->taxa / 100);
                  ?>
                    <tr class="plano" id="plano<?= $comissao->id; ?>">
                      <?php if ($_SESSION["tipo"] == 1) : ?>
                        <th scope="row"><?= $comissao->id; ?></th>
                      <?php endif; ?>
                      <td>R$ <?= number_format($comissao->valor, 2, ",", "."); ?></td>
                      <td><?= $comissao->descricao; ?></td>

                      <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                        <td><?= @$comissao->dataUsuario()->nome . "<br>" . @$comissao->dataUsuario()->email; ?></td>
                      <?php endif; ?>

                      <td><?= (@$comissao->produto ? $comissao->dataProduto()->nome : " - ") ?></td>
                      <td><?= (@$comissao->venda ? $comissao->dataVenda()->transacao : "<a href='" . url("saques") . "'>saque</a>") ?></td>
                      <td><?= formataData(@$comissao->data_pedido); ?></td>
                      <td><?= formataData(@$comissao->tempo_saque); ?></td>
                    </tr>

                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          <?php
            //EXIBE A NAVEGAÇÃO DO PAGINADOR
            $link = URL_BASE . "/comissao/redrocket/";

            //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
            $paginador->paginas($paginas, $pagina, $link);

          else :
          ?>
            <h4>Você ainda não possui comissão!</h4>
          <?php
          endif;
          ?>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

      <!-- cadastar -->
      <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-info-circle"></i> Solicitar Saque</h3>
              <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form action="#" method="post" style="font-family:inter">
              <div class="modal-body" style="background: #f4f6f9">
                <input type="hidden" name="tipo" value="2">
                <input type="hidden" name="disponivel" value="0">
                <input type="hidden" name="saldo" value="<?= @$saldo ?>">

                <div class="card card-body">
                  <label>Valor disponível:</label> <?= @$saldo ? "R$ " . number_format($saldo, 2, ",", ".") : "R$ 0,00"; ?><br>
                </div>

                <div class="card card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <label>Valor:</label>
                      <input type="text" name="valor" id="valor" placeholder="Valor Sacado" class="form-control"><br>
                    </div>

                    <div class="col-md-6">
                      <label>Descrição:</label>
                      <input type="text" name="descricao" id="descricao" placeholder="Descrição" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"> Fechar</button>
                <button class="btn btn-success" id="salvar">Solicitar</button>
              </div>
              <!-- /.row -->
            </form>

            <!-- </div> -->

            <!-- </div> -->

          </div>
          <!-- end cadastrar -->

          <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?= $v->start("scripts") ?>
<script>
  $("#valor").blur(function(e) {
    var valor = $("#valor").val();
    $("#valor").val(valor.replace(",", "."));
  });

  $("#salvar").click(function(e) {
    e.preventDefault();
    if ($("#valor").val() != "") {
      salvarEditar(1);
    }
  });

  function salvarEditar(salvar) {
    if (salvar == 1) {
      url = "<?= $router->route("appcomissao.saqueRedrocket"); ?>";
    } else {
      url = "<?= $router->route("appcomissao.saque") ?>";
    }

    var form = $("form");
    $.ajax({

      url: url,
      data: form.serialize(),
      type: "POST",
      //datatype: "json",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        alert(callback);
        location.reload();
      }

    });
  }
</script>
<?= $v->end() ?>
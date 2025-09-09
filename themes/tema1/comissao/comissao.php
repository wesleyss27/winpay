<?php
$v->layout("_theme");

//if(strtotime(date("H:i")) >= '17:00') echo date("Y-m-d", strtotime("+3 days")); else echo date("Y-m-d", strtotime("+2 days"));

if (@$saldo == "")
  $saldo = 0;

$saqueMin = @$taxas->saque_minimo;
$saqueMax = 1900;
$limiteSaque = $saqueMax - (-@$saqueMes->valor);

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper">
  <br />

  <section class="content">

    <?= $section->getTipoSecaoLayout() ?>

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
            <h3><?= @$saldo ? "R$ " . number_format($saldo, 2, ",", ".") : "R$ 0,00"; ?></h3>
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
            <h3><?= @$bloqueado ? "R$ " . number_format($bloqueado, 2, ",", ".") : "R$ 0,00"; ?></h3>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->

      <div class="callout callout-primary">
        <form action="#">
          <div class="row">
            <div class="col-12 col-md-3">
              <h6 style="font-family:inter">Data do pedido:</h6>
              <input type="date" name="inicio" id="inicio" class="form-control" style="cursor:pointer;" value="<?= @$_GET["inicio"]; ?>">
            </div>

            <div class="col-12 col-md-3">
              <h6 style="font-family:inter; color:#fff">|</h6>
              <input type="date" name="fim" id="fim" class="form-control" style="cursor:pointer;" value="<?= @$_GET["fim"]; ?>">
            </div>

            <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
              <div class="col-12 col-md-3">
                <h6 style="font-family:inter">Usuário:</h6>
                <input type="text" name="usuario" id="usuario" class="form-control" placeholder="(nome ou email ou login)" value="<?= @$_GET["usuario"]; ?>">
              </div>
            <?php endif; ?>

            <div class="col-12 col-md-3">
              <h6 style="font-family:inter; color:#fff">|</h6>
              <button type="submit" name="" class="form-control form-control btn-primary"> <i class="fa fa-check"></i> Buscar</button>
            </div>
          </div>
        </form>
      </div>
      <!-- Main row -->

      <div class="card card-body" style="background: #17A2B833;">
        Saques disponíveis a partir de R$ 200,00 líquido (o valor disponível menos os débitos) <br>
        *Necessário ter documento cadastrado e conta verificada
      </div>
      <!-- Default box -->
      <div class="card">

        <div class="card-header">
          <?php if (isset($saldoPagarme)) : ?>
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-pagarme">
              Solicitar saque
            </button> &nbsp;
          <?php endif ?>

          <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
            <a href="?meus=true" class="btn btn-outline-primary">Minhas Comissões</a> &nbsp;
          <?php endif; ?>

        </div>

        <div class="card-bod">

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
                    <!--th scope="col">Venda</th-->
                    <th scope="col">Data</th>
                    <th scope="col">Disponível Em Até:</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($comissao as $comissao) :
                    /** CALCULA COMISSÃO MENOS A TAXA DE TRANSAÇÃO */
                    //$total = $comissao->valor - ($comissao->valor * $comissao->taxa / 100);
                  ?>
                    <tr class="plano" id="plano<?= $comissao->id; ?>">
                      <?php if ($_SESSION["tipo"] == 1) : ?>
                        <td><b><?= $comissao->id; ?></b></td>
                      <?php endif; ?>
                      <td>
                        R$ <?= number_format($comissao->valor, 2, ",", "."); ?>
                        <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?= (stripos($comissao->descricao, "tx Taxa") !== false) ? "Taxa operadora - $comissao->descricao" : $comissao->descricao; ?><br>
                        <?= (@$comissao->venda ?
                          "<a href='" . url("vendas?venda=" . $comissao->dataVenda()->transacao) . "' target='_blank'>" . $comissao->dataVenda()->transacao . "</a>" :
                          "<a href='" . url("saques") . "' target='_blank'>saque</a>") ?>
                      </td>

                      <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                        <td><?= @$comissao->dataUsuario()->nome . "<br>" . @$comissao->dataUsuario()->email; ?></td>
                      <?php endif; ?>

                      <td><?= (@$comissao->produto ? $comissao->dataProduto()->nome : " - ") ?></td>
                      <!--td><?= (@$comissao->venda ? $comissao->dataVenda()->transacao : "<a href='" . url("saques") . "'>saque</a>") ?></td-->
                      <td><?= $comissao->data_pedido ? formataData(@$comissao->data_pedido) : "-"; ?></td>
                      <td><?= formataData(@$comissao->tempo_saque); ?></td>
                    </tr>

                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          <?php
            //EXIBE A NAVEGAÇÃO DO PAGINADOR
            $link = URL_BASE . "/comissao/";
            $uri = explode("?", $_SERVER["REQUEST_URI"], 2);

            //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
            $paginador->paginas($paginas, $pagina, $link, @$uri[1]);
            echo "<br>";
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
            <div class="modal-body" style="background: #f4f6f9">

              <form action="#" method="post" style="font-family:inter">
                <input type="hidden" name="usuario" value="<?= $section->getId() ?>">
                <input type="hidden" name="taxa">
                <input type="hidden" name="tipo" value="2">
                <input type="hidden" name="disponivel" value="0">

                <input type="hidden" name="tipo_doc" value="<?= $usuario->tipo_doc ?>">
                <input type="hidden" name="saqueMin" value="<?= $saqueMin ?>">
                <input type="hidden" name="saqueMax" value="<?= $saqueMax ?>">
                <input type="hidden" name="saqueMes" value="<?= @$saqueMes->valor ? $saqueMes->valor : 0; ?>">

                <div class="card card-body" style="font-size: 13px">
                  <b>Leia Atentamente:</b><br>
                  <p>Valor mínimo para saque é de <b> R$ 200,00 </b>.</p>
                  <p>Pessoas físicas têm limite mensal de saque de <b> R$ 1900,00 </b>. Para saque
                    superior a esse valor, você precisa ter um CNPJ. </p>
                  A partir do segundo saque no dia será cobrado uma taxa de R$ 3,80 por saque. <br><br>
                  Para o primeiro saque é necessário ter a conta verificada
                </div>

                <div class="card card-body">
                  <b>Valor disponível:</b> <?= @$saldo ? "R$ " . number_format($saldo, 2, ",", ".") : "R$ 0,00"; ?><br><br>
                </div>

                <div class="card card-body">
                  <input type="hidden" name="saldo" value="<?= @$saldo ?>">

                  <div class="row">
                    <div class="col-md-6">
                      <b>Valor de saque:</b>
                      <input type="text" name="valor" id="valor" placeholder="ex.: 204634.26" class="form-control">
                    </div>

                    <div class="col-md-6">

                      <b>Conta bancária:</b>
                      <select name="conta" id="conta" class="form-control">
                        <option value="">Conta..</option>
                        <?php foreach ($bancos as $banco) : ?>
                          <option value="<?= $banco->id ?>"><?= "$banco->banco - $banco->agencia $banco->conta - $banco->titular" ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>

              <?php if ($primeiroSaque == 0) : ?>
                <?php if ($user->verificado == 1) : ?>
                  <button class="btn btn-success" id="salvar">Solicitar</button>
                <?php endif; ?>
              <?php else : ?>
                <button class="btn btn-success" id="salvar">Solicitar</button>
              <?php endif; ?>
            </div>
            <!-- /.row -->
            </form>
          </div>

        </div>

      </div>
      <!-- end cadastrar -->

      <!-- pagarme -->
      <div class="modal fade" id="modal-pagarme">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-info-circle"></i> Solicitar Saque</h3>
              <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>
            <div class="modal-body" style="background: #f4f6f9">

              <form action="#" method="post" style="font-family:inter">
                <input type="hidden" name="usuario" value="<?= $section->getId() ?>">
                <input type="hidden" name="taxa">
                <input type="hidden" name="tipo" value="2">
                <input type="hidden" name="disponivel" value="0">

                <input type="hidden" name="tipo_doc" value="<?= $usuario->tipo_doc ?>">
                <input type="hidden" name="saqueMin" value="<?= $saqueMin ?>">
                <input type="hidden" name="saqueMax" value="<?= $saqueMax ?>">
                <input type="hidden" name="saqueMes" value="<?= @$saqueMes->valor ? $saqueMes->valor : 0; ?>">

                <div class="card card-body" style="font-size: 13px">

                  <?php
                    $avaliable_amount = $saldoPagarme->balance ? $saldoPagarme->balance : 0;
                    $withdraw_value =  $avaliable_amount > $taxas->taxa_saque_pagarme ? $avaliable_amount - $taxas->taxa_saque_pagarme : '0';
                  ?>

                  <p><b>Leia Atentamente:</b></p>

                  <p>Pessoas físicas têm limite mensal de saque de R$ 1900,00 . Para saque superior a esse valor, você precisa ter um CNPJ.
                  O saque será cobrado uma taxa de <b>R$ 3,67</b> por saque realizado. Para o primeiro saque é necessário ter a conta verificada</p>

                  <p><b>Saque minimo:</b> <?php echo $taxas->vlr_min_saque ?></p>
                  <p><b>Taxa por saque:</b> <?php echo $taxas->taxa_saque_pagarme ?> a cada saque realizado.</p>
                  <p><b>Saque máximo:</b> R$ <?= number_format($withdraw_value, 2, ',', '.') ?></p>
                </div>

                <div class="card card-body">

                  <div class="row">
                    <div class="col-md-4">
                      <label>Seu saldo:</label>
                      <input type="text" name="valor_total_saque" id='valor_total_saque' class="form-control input-lg" value="<?= @$saldoPagarme->balance ? "R$ " . number_format($saldoPagarme->balance, 2, ',', '.') : "R$ 0,00"; ?>" disabled>
                      <span>sem taxa incluída<span>
                    </div>

                    <div class="col-md-3">
                        <label>Valor do saque:</label>
                       
                        <input type="text" name="valor_a_sacar" id='valor_a_sacar' class="form-control input-lg" value="<?php echo $withdraw_value ?>" min="<?php echo $taxas->vlr_min_saque ?>" max="<?php echo $withdraw_value ?>" step="100" placeholder="1.000,00">
                    </div>
                  </div>

                </div>

              </form>   
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              <button class="btn btn-success" id="solicitar">Solicitar</button>
            </div>
            <!-- /.row -->
           
          </div>

        </div>

      </div>
      <!-- end pagarme -->

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->



<?= $v->start("scripts") ?>
<script>
  $("#valor").blur(function(e) {
    var valor = $("#valor").val();
    // $("#valor").val(valor.replace(",", "."));

    var tipo_doc = <?= $usuario->tipo_doc ?>;
    var saqueMin = <?= @$saqueMin; ?>;
    var saqueMax = <?= @$saqueMax; ?>;
    var saqueMes = <?= @$saqueMes->valor ? $saqueMes->valor : 0; ?>;

    if ($("#valor").val() > <?= @$saldo; ?>) {
      alert("Valor maior que o disponível!");
      $("#valor").val("<?= @$saldo; ?>");
    } else if ($("#valor").val() < saqueMin) {
      alert("Valor menor que o saque mínimo");
      $("#valor").val(saqueMin);
    }

    var limiteSaque = saqueMax - (-saqueMes);
    console.log("soma", limiteSaque);
    if (tipo_doc == 1 && (limiteSaque < saqueMin || limiteSaque > saqueMax)) {
      alert("Você atingiu o limite mensal de saque");
      $("#salvar").attr("disabled", true);
    }
  });

  $("#salvar").click(function(e) {
    e.preventDefault();
    $("#salvar").hide();
    if ($("#valor").val() != "") {
      salvarEditar();
    }
  });

  function salvarEditar() {
    url = "<?= $router->route("appcomissao.sacar"); ?>";

    console.log("url ", url);
    var form = $("form");
    $.ajax({

      url: url,
      data: form.serialize(),
      type: "POST",
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

  $("#solicitar").click(function(e) {
    e.preventDefault();
    $("#solicitar").hide();

    url = "<?= $router->route("appcomissao.saquePagarme"); ?>";
    var form = $("form");
  
    $.ajax({

      url: url,
      data: form.serialize(),
      type: "POST",
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
  });
</script>
<?= $v->end() ?>
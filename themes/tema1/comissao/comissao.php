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

<style>
/* ===== Tema dark WinPay só para #modal-lg e #modal-pagarme (CSS puro, sem nesting) ===== */

/* Dialog + animação */
#modal-lg .modal-dialog,
#modal-pagarme .modal-dialog{
  width:100% !important;
  max-width:900px !important;
  margin:20px !important;
  animation: slideUp .3s ease-out !important;
}

/* Content + bordas/shine */
#modal-lg .modal-content,
#modal-pagarme .modal-content{
  background: linear-gradient(145deg,#1a1a1a,#111111) !important;
  border:1px solid rgba(255,221,0,.3) !important;
  border-radius:20px !important;
  box-shadow:
    0 25px 50px rgba(0,0,0,.9),
    0 0 0 1px rgba(255,221,0,.1),
    inset 0 1px 0 rgba(255,255,255,.1) !important;
  overflow:hidden !important;
  position:relative !important;
}
#modal-lg .modal-content::before,
#modal-pagarme .modal-content::before{
  content:'';
  position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(90deg,transparent,#FFDD00,transparent);
  animation: shimmer 2s infinite;
}

/* Header (serve para .card-header e .modal-header dentro dos modais) */
#modal-lg .card-header, #modal-lg .modal-header,
#modal-pagarme .card-header, #modal-pagarme .modal-header{
  background: linear-gradient(135deg,#2a2a2a,#1a1a1a) !important;
  padding:24px 32px !important;
  border-bottom:1px solid rgba(255,221,0,.2) !important;
  display:flex; align-items:center; gap:10px;
}
#modal-lg .card-header h3, #modal-lg .modal-header h3,
#modal-lg .card-header h4, #modal-lg .modal-header h4,
#modal-pagarme .card-header h3, #modal-pagarme .modal-header h3,
#modal-pagarme .card-header h4, #modal-pagarme .modal-header h4{
  color:#FFDD00 !important; font-size:20px !important; font-weight:600 !important; margin:0 !important;
  font-family:'Inter','Montserrat',sans-serif !important;
}
#modal-lg .card-header .close, #modal-lg .modal-header .close,
#modal-pagarme .card-header .close, #modal-pagarme .modal-header .close{
  background:none !important; border:none !important; color:#FFDD00 !important; font-size:24px !important;
  cursor:pointer !important; margin-left:auto; width:36px !important; height:36px !important;
  display:flex !important; align-items:center !important; justify-content:center !important; border-radius:50% !important;
  transition:all .3s ease !important;
}
#modal-lg .card-header .close:hover, #modal-lg .modal-header .close:hover,
#modal-pagarme .card-header .close:hover, #modal-pagarme .modal-header .close:hover{
  background:rgba(255,221,0,.1) !important; transform:scale(1.1);
}

/* Body (vence o inline antigo) */
#modal-lg .modal-body,
#modal-pagarme .modal-body{
  background:#111111 !important; color:#f1f1f1 !important;
  padding:32px !important; max-height:70vh !important; overflow-y:auto !important;
  font-family:'Inter','Montserrat',sans-serif;
}
#modal-lg .modal-body::-webkit-scrollbar,
#modal-pagarme .modal-body::-webkit-scrollbar{ width:8px; }
#modal-lg .modal-body::-webkit-scrollbar-track,
#modal-pagarme .modal-body::-webkit-scrollbar-track{ background:#1a1a1a; border-radius:4px; }
#modal-lg .modal-body::-webkit-scrollbar-thumb,
#modal-pagarme .modal-body::-webkit-scrollbar-thumb{ background:linear-gradient(135deg,#FFDD00,#D4B800); border-radius:4px; }
#modal-lg .modal-body::-webkit-scrollbar-thumb:hover,
#modal-pagarme .modal-body::-webkit-scrollbar-thumb:hover{ background:linear-gradient(135deg,#FFE83D,#FFDD00); }

/* Footer */
#modal-lg .modal-footer,
#modal-pagarme .modal-footer{
  background:linear-gradient(135deg,#2a2a2a,#1a1a1a) !important;
  padding:20px 32px !important; border-top:1px solid rgba(255,221,0,.2) !important;
  display:flex !important; justify-content:space-between !important; gap:12px !important;
}

/* Cards internos */
#modal-lg .card.card-body,
#modal-pagarme .card.card-body{
  background:linear-gradient(145deg,#2a2a2a,#1a1a1a) !important; color:#f1f1f1 !important;
  border:1px solid rgba(255,221,0,.1) !important; border-radius:15px !important;
  padding:24px !important; margin-bottom:20px !important;
  box-shadow:0 8px 32px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.05) !important;
  position:relative !important; overflow:hidden !important;
}
#modal-lg .card.card-body::before,
#modal-pagarme .card.card-body::before{
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,rgba(255,221,0,.3),transparent);
}
#modal-lg .titulo, #modal-pagarme .titulo{
  color:#FFDD00 !important; font-size:18px !important; font-weight:600 !important; margin-bottom:20px !important;
  border-bottom:2px solid rgba(255,221,0,.2) !important; padding-bottom:8px !important;
}
#modal-lg .row.borda1, #modal-pagarme .row.borda1{
  padding:12px 0 !important; border-bottom:1px solid rgba(255,221,0,.1) !important; transition:background .3s ease !important;
}
#modal-lg .row.borda1:hover, #modal-pagarme .row.borda1:hover{
  background:rgba(255,221,0,.05) !important; border-radius:8px !important; margin:0 -12px !important; padding:12px !important;
}
#modal-lg .row.borda1:last-child, #modal-pagarme .row.borda1:last-child{ border-bottom:none !important; }

/* Tabelas (se usar dentro do modal) */
#modal-lg table, #modal-pagarme table{
  width:100%; border-collapse:separate; border-spacing:0; background:#1a1a1a; color:#f1f1f1;
  border:1px solid rgba(255,221,0,.1); border-radius:10px; overflow:hidden;
  box-shadow:0 4px 16px rgba(0,0,0,.5);
}
#modal-lg thead, #modal-pagarme thead{ background:linear-gradient(135deg,#2a2a2a,#1a1a1a); }
#modal-lg thead th, #modal-pagarme thead th{
  padding:14px 16px; text-align:left; color:#FFDD00; font-weight:600; border-bottom:1px solid rgba(255,221,0,.1); font-size:14px;
}
#modal-lg tbody td, #modal-pagarme tbody td{
  padding:12px 16px; border-bottom:1px solid rgba(255,221,0,.05); font-size:13px;
}
#modal-lg tbody tr:hover, #modal-pagarme tbody tr:hover{ background:rgba(255,221,0,.05); }

/* Responsivo */
@media (max-width:768px){
  #modal-lg .modal-dialog, #modal-pagarme .modal-dialog{
    margin:10px !important; max-width:calc(100vw - 20px) !important;
  }
  #modal-lg .modal-body, #modal-pagarme .modal-body{
    padding:20px !important; max-height:60vh !important;
  }
  #modal-lg .card-header, #modal-lg .modal-header,
  #modal-lg .modal-footer,
  #modal-pagarme .card-header, #modal-pagarme .modal-header,
  #modal-pagarme .modal-footer{
    padding:16px 20px !important;
  }
}

/* animações */
@keyframes slideUp{ from{opacity:0; transform:translateY(30px) scale(.95);} to{opacity:1; transform:translateY(0) scale(1);} }
@keyframes shimmer{ 0%{transform:translateX(-100%);} 100%{transform:translateX(100%);} }
/* Centralização robusta para #modal-lg e #modal-pagarme */
#modal-lg .modal-dialog,
#modal-pagarme .modal-dialog{
  margin: 0 auto !important;            /* centraliza na horizontal */
  min-height: calc(100vh - 1rem) !important;
  display: flex !important;              /* ativa alinhamento vertical */
  align-items: center !important;        /* centraliza na vertical */
}

#modal-lg .modal-content,
#modal-pagarme .modal-content{
  width: 100%;
}

</style>

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
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg">
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
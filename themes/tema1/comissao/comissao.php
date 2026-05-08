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
/* ===== Tema dark WinPay para #modal-lg, #modal-pagarme e #modal-saque (CSS puro, sem nesting) ===== */

/* Dialog + animação */
#modal-lg .modal-dialog,
#modal-pagarme .modal-dialog,
#modal-saque .modal-dialog{
  width:100% !important;
  max-width:900px !important;
  margin:20px !important;
  animation: slideUp .3s ease-out !important;
}

/* Content + bordas/shine */
#modal-lg .modal-content,
#modal-pagarme .modal-content,
#modal-saque .modal-content{
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
#modal-pagarme .modal-content::before,
#modal-saque .modal-content::before{
  content:'';
  position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(90deg,transparent,#FFDD00,transparent);
  animation: shimmer 2s infinite;
}

/* Header */
#modal-lg .card-header, #modal-lg .modal-header,
#modal-pagarme .card-header, #modal-pagarme .modal-header,
#modal-saque .card-header, #modal-saque .modal-header{
  background: linear-gradient(135deg,#2a2a2a,#1a1a1a) !important;
  padding:24px 32px !important;
  border-bottom:1px solid rgba(255,221,0,.2) !important;
  display:flex; align-items:center; gap:10px;
}
#modal-lg .card-header h3, #modal-lg .modal-header h3,
#modal-lg .card-header h4, #modal-lg .modal-header h4,
#modal-pagarme .card-header h3, #modal-pagarme .modal-header h3,
#modal-pagarme .card-header h4, #modal-pagarme .modal-header h4,
#modal-saque .card-header h3, #modal-saque .modal-header h3{
  color:#FFDD00 !important; font-size:20px !important; font-weight:600 !important; margin:0 !important;
  font-family:'Inter','Montserrat',sans-serif !important;
}
#modal-lg .card-header .close, #modal-lg .modal-header .close,
#modal-pagarme .card-header .close, #modal-pagarme .modal-header .close,
#modal-saque .card-header .close, #modal-saque .modal-header .close{
  background:none !important; border:none !important; color:#FFDD00 !important; font-size:24px !important;
  cursor:pointer !important; margin-left:auto; width:36px !important; height:36px !important;
  display:flex !important; align-items:center !important; justify-content:center !important; border-radius:50% !important;
  transition:all .3s ease !important;
}
#modal-lg .card-header .close:hover, #modal-lg .modal-header .close:hover,
#modal-pagarme .card-header .close:hover, #modal-pagarme .modal-header .close:hover,
#modal-saque .card-header .close:hover, #modal-saque .modal-header .close:hover{
  background:rgba(255,221,0,.1) !important; transform:scale(1.1);
}

/* Body */
#modal-lg .modal-body,
#modal-pagarme .modal-body,
#modal-saque .modal-body{
  background:#111111 !important; color:#f1f1f1 !important;
  padding:32px !important; max-height:70vh !important; overflow-y:auto !important;
  font-family:'Inter','Montserrat',sans-serif;
}
#modal-lg .modal-body::-webkit-scrollbar,
#modal-pagarme .modal-body::-webkit-scrollbar,
#modal-saque .modal-body::-webkit-scrollbar{ width:8px; }
#modal-lg .modal-body::-webkit-scrollbar-track,
#modal-pagarme .modal-body::-webkit-scrollbar-track,
#modal-saque .modal-body::-webkit-scrollbar-track{ background:#1a1a1a; border-radius:4px; }
#modal-lg .modal-body::-webkit-scrollbar-thumb,
#modal-pagarme .modal-body::-webkit-scrollbar-thumb,
#modal-saque .modal-body::-webkit-scrollbar-thumb{ background:linear-gradient(135deg,#FFDD00,#D4B800); border-radius:4px; }
#modal-lg .modal-body::-webkit-scrollbar-thumb:hover,
#modal-pagarme .modal-body::-webkit-scrollbar-thumb:hover,
#modal-saque .modal-body::-webkit-scrollbar-thumb:hover{ background:linear-gradient(135deg,#FFE83D,#FFDD00); }

/* Footer */
#modal-lg .modal-footer,
#modal-pagarme .modal-footer,
#modal-saque .modal-footer{
  background:linear-gradient(135deg,#2a2a2a,#1a1a1a) !important;
  padding:20px 32px !important; border-top:1px solid rgba(255,221,0,.2) !important;
  justify-content:space-between !important; gap:12px !important;
}

/* Cards internos */
#modal-lg .card.card-body,
#modal-pagarme .card.card-body,
#modal-saque .card.card-body{
  background:linear-gradient(145deg,#2a2a2a,#1a1a1a) !important; color:#f1f1f1 !important;
  border:1px solid rgba(255,221,0,.1) !important; border-radius:15px !important;
  padding:24px !important; margin-bottom:20px !important;
  box-shadow:0 8px 32px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.05) !important;
  position:relative !important; overflow:hidden !important;
}
#modal-lg .card.card-body::before,
#modal-pagarme .card.card-body::before,
#modal-saque .card.card-body::before{
  content:''; position:absolute; top:0; left:0; right:0; height:1px;
  background:linear-gradient(90deg,transparent,rgba(255,221,0,.3),transparent);
}
#modal-lg .titulo, #modal-pagarme .titulo, #modal-saque .titulo{
  color:#FFDD00 !important; font-size:18px !important; font-weight:600 !important; margin-bottom:20px !important;
  border-bottom:2px solid rgba(255,221,0,.2) !important; padding-bottom:8px !important;
}

/* Responsivo */
@media (max-width:768px){
  #modal-lg .modal-dialog, #modal-pagarme .modal-dialog, #modal-saque .modal-dialog{
    margin:10px !important; max-width:calc(100vw - 20px) !important;
  }
  #modal-lg .modal-body, #modal-pagarme .modal-body, #modal-saque .modal-body{
    padding:20px !important; max-height:60vh !important;
  }
  #modal-lg .card-header, #modal-lg .modal-header, #modal-lg .modal-footer,
  #modal-pagarme .card-header, #modal-pagarme .modal-header, #modal-pagarme .modal-footer,
  #modal-saque .card-header, #modal-saque .modal-header, #modal-saque .modal-footer{
    padding:16px 20px !important;
  }
}

/* animações */
@keyframes slideUp{ from{opacity:0; transform:translateY(30px) scale(.95);} to{opacity:1; transform:translateY(0) scale(1);} }
@keyframes shimmer{ 0%{transform:translateX(-100%);} 100%{transform:translateX(100%);} }

/* Modal unificado de saque */
#modal-saque .modal-dialog{
  margin: 0 auto !important;
  min-height: calc(100vh - 1rem) !important;
  display: flex !important;
  align-items: center !important;
}
#modal-saque .modal-content{ width: 100%; }

/* Cards de seleção de carteira */
.saque-carteira-card {
  border-radius: 12px !important;
  transition: border .2s, box-shadow .2s, transform .15s !important;
}
.saque-carteira-card:not(.opacity-50):hover {
  transform: translateY(-2px);
}
.opacity-50 { opacity: .55; }

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
          <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-saque">
            Solicitar saque
          </button> &nbsp;

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


      <!-- ================================================================
           MODAL UNIFICADO: Solicitar Saque — escolha a carteira
           ================================================================ -->
      <?php
        $saldoWinPayDisp  = floatval(@$saldo ?? 0);
        $saldoPagarmeDisp = floatval(($saldoPagarme->available_amount ?? 0) / 100);
        $podePagarme      = ($saldoPagarme->available_amount ?? 0) >= 10000;
      ?>
      <div class="modal fade" id="modal-saque">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="card-header">
              <h3 class="card-title" id="modal-saque-titulo">
                <i class="fa fa-info-circle"></i> Solicitar Saque
              </h3>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

              <!-- STEP 1: Escolher carteira -->
              <div id="saque-step1">
                <p style="font-size:15px;margin-bottom:20px">Escolha de qual carteira deseja solicitar o saque:</p>
                <div class="row">

                  <!-- Card WinPay -->
                  <div class="col-md-6 mb-3">
                    <div id="card-winpay" style="
                      background:#1e1e1e;border-radius:14px;padding:22px 20px;cursor:pointer;
                      border:1px solid #2a2a2a;transition:border .2s,transform .15s;
                    ">
                      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                        <span style="font-size:22px">💳</span>
                        <strong style="color:#FFDD00;font-size:16px">Carteira WinPay</strong>
                      </div>
                      <div style="font-size:13px;color:#888;margin-bottom:6px">Disponível para saque</div>
                      <div style="font-size:24px;font-weight:700;color:#FFDD00;padding-bottom:12px;border-bottom:2px solid #FFDD00;margin-bottom:12px">
                        R$ <?= number_format($saldoWinPayDisp, 2, ',', '.') ?>
                      </div>
                      <div style="font-size:12px;color:#aaa">
                        Saque mínimo: R$ <?= number_format(floatval(@$saqueMin ?? 200), 2, ',', '.') ?>
                      </div>
                    </div>
                  </div>

                  <!-- Card PagarMe -->
                  <div class="col-md-6 mb-3">
                    <div id="card-pagarme" style="
                      background:#1e1e1e;border-radius:14px;padding:22px 20px;
                      border:1px solid #2a2a2a;transition:border .2s,transform .15s;
                      cursor:<?= $podePagarme ? 'pointer' : 'default' ?>;
                      opacity:<?= $podePagarme ? '1' : '.6' ?>;
                    ">
                      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                        <span style="font-size:22px">🏦</span>
                        <strong style="color:#FFDD00;font-size:16px">Carteira PagarMe</strong>
                      </div>
                      <div style="font-size:13px;color:#888;margin-bottom:6px">Disponível para saque</div>
                      <div style="font-size:24px;font-weight:700;color:#FFDD00;padding-bottom:12px;border-bottom:2px solid #FFDD00;margin-bottom:12px">
                        R$ <?= number_format($saldoPagarmeDisp, 2, ',', '.') ?>
                      </div>
                      <?php if ($podePagarme): ?>
                        <div style="font-size:12px;color:#aaa">Saque mínimo: R$ 100,00</div>
                      <?php else: ?>
                        <div style="font-size:12px;color:#e07070">Saldo insuficiente para saque</div>
                      <?php endif; ?>
                    </div>
                  </div>

                </div>
              </div><!-- /step1 -->

              <!-- STEP 2a: Formulário WinPay -->
              <div id="saque-step2-winpay" class="d-none">
                <form id="form-winpay" action="#" method="post" style="font-family:inter">
                  <input type="hidden" name="usuario"    value="<?= $section->getId() ?>">
                  <input type="hidden" name="taxa">
                  <input type="hidden" name="tipo"       value="2">
                  <input type="hidden" name="disponivel" value="0">
                  <input type="hidden" name="tipo_doc"   value="<?= $usuario->tipo_doc ?>">
                  <input type="hidden" name="saqueMin"   value="<?= $saqueMin ?>">
                  <input type="hidden" name="saqueMax"   value="<?= $saqueMax ?>">
                  <input type="hidden" name="saqueMes"   value="<?= @$saqueMes->valor ? $saqueMes->valor : 0 ?>">
                  <input type="hidden" name="saldo"      value="<?= $saldoWinPayDisp ?>">

                  <div class="card card-body" style="font-size:13px">
                    <b>Carteira WinPay — Leia Atentamente:</b>
                    <p class="mt-2 mb-1">Valor mínimo para saque: <b>R$ <?= number_format(floatval(@$saqueMin ?? 200), 2, ',', '.') ?></b></p>
                    <p class="mb-1">Pessoas físicas têm limite mensal de <b>R$ 1.900,00</b>. Para valores maiores é necessário CNPJ.</p>
                    <p class="mb-0">A partir do 2º saque no mesmo dia será cobrada taxa de <b>R$ 3,80</b>. Para o 1º saque a conta deve estar verificada.</p>
                  </div>

                  <div class="card card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <span style="font-size:14px">Saldo disponível na Carteira WinPay:</span>
                      <strong style="color:#28a745;font-size:18px">
                        R$ <?= number_format($saldoWinPayDisp, 2, ',', '.') ?>
                      </strong>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label style="font-weight:600">Valor de saque:</label>
                        <input type="text" name="valor" id="wp-valor"
                               class="form-control"
                               placeholder="ex.: 250,00"
                               max="<?= $saldoWinPayDisp ?>">
                        <small class="text-muted">
                          Mín: R$ <?= number_format(floatval(@$saqueMin ?? 200), 2, ',', '.') ?>
                          — Máx: R$ <?= number_format($saldoWinPayDisp, 2, ',', '.') ?>
                        </small>
                      </div>

                      <div class="col-md-6 mb-3">
                        <label style="font-weight:600">Conta bancária:</label>
                        <select name="conta" id="wp-conta" class="form-control">
                          <option value="">Selecione a conta...</option>
                          <?php foreach ($bancos as $banco) : ?>
                            <option value="<?= $banco->id ?>">
                              <?= "$banco->banco - Ag. $banco->agencia / Cc. $banco->conta — $banco->titular" ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </form>
              </div><!-- /step2-winpay -->

              <!-- STEP 2b: Formulário PagarMe -->
              <div id="saque-step2-pagarme" class="d-none">
                <form id="form-pagarme" action="#" method="post" style="font-family:inter">

                  <div class="card card-body" style="font-size:13px">
                    <b>Carteira Pagar.me — Leia Atentamente:</b>
                    <p class="mt-2 mb-1">Valor mínimo para saque: <b>R$ 100,00</b></p>
                    <p class="mb-0">O valor máximo é o saldo disponível na sua conta Pagar.me.</p>
                  </div>

                  <div class="card card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <span style="font-size:14px">Disponível para saque (Pagar.me):</span>
                      <strong style="color:#17a2b8;font-size:18px">
                        R$ <?= number_format($saldoPagarmeDisp, 2, ',', '.') ?>
                      </strong>
                    </div>

                    <div class="col-md-6 mb-3 px-0">
                      <label style="font-weight:600">Valor de saque:</label>
                      <input type="text" name="valor_a_sacar" id="pm-valor"
                             class="form-control"
                             placeholder="ex.: 500,00">
                      <small class="text-muted">
                        Mín: R$ 100,00 — Máx: R$ <?= number_format($saldoPagarmeDisp, 2, ',', '.') ?>
                      </small>
                    </div>
                  </div>
                </form>
              </div><!-- /step2-pagarme -->

            </div><!-- /modal-body -->

            <div class="modal-footer" style="justify-content:space-between">
              <!-- Botões step 1 -->
              <div id="footer-step1">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              </div>
              <!-- Botões step 2 WinPay -->
              <div id="footer-step2-winpay" class="d-none w-100" style="justify-content:space-between">
                <button type="button" class="btn btn-secondary" id="btn-voltar-winpay">← Voltar</button>
                <?php if ($primeiroSaque == 0 && $user->verificado != 1): ?>
                  <button class="btn btn-success" disabled title="Verifique sua conta primeiro">Solicitar</button>
                <?php else: ?>
                  <button class="btn btn-success" id="salvar">Solicitar</button>
                <?php endif; ?>
              </div>
              <!-- Botões step 2 PagarMe -->
              <div id="footer-step2-pagarme" class="d-none w-100" style="justify-content:space-between">
                <button type="button" class="btn btn-secondary" id="btn-voltar-pagarme">← Voltar</button>
                <button class="btn btn-success" id="salvar-pagarme">Solicitar</button>
              </div>
            </div>

          </div><!-- /modal-content -->
        </div><!-- /modal-dialog -->
      </div>
      <!-- ================================================================
           FIM DO MODAL UNIFICADO
           ================================================================ -->

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->



<?= $v->start("scripts") ?>
<script>
(function () {
  var SALDO_WINPAY  = <?= floatval(@$saldo ?? 0) ?>;
  var SALDO_PAGARME = <?= $saldoPagarmeDisp ?>;
  var SAQUE_MIN     = <?= floatval(@$saqueMin ?? 200) ?>;
  var SAQUE_MAX     = <?= floatval(@$saqueMax ?? 1900) ?>;
  var SAQUE_MES     = <?= floatval(@$saqueMes->valor ?? 0) ?>;
  var TIPO_DOC      = <?= intval($usuario->tipo_doc ?? 0) ?>;
  var urlSacar      = "<?= $router->route("appcomissao.sacar") ?>";
  var urlSacarPm    = "<?= $router->route("appcomissao.saquePagarme") ?>";

  function showStep(step) {
    $('#saque-step1, #saque-step2-winpay, #saque-step2-pagarme').addClass('d-none');
    $('#footer-step1, #footer-step2-winpay, #footer-step2-pagarme').addClass('d-none').css('display', '');
    if (step === 1) {
      $('#saque-step1').removeClass('d-none');
      $('#footer-step1').removeClass('d-none');
      $('#modal-saque-titulo').html('<i class="fa fa-info-circle"></i> Solicitar Saque');
    } else if (step === 'winpay') {
      $('#saque-step2-winpay').removeClass('d-none');
      $('#footer-step2-winpay').removeClass('d-none').css('display', 'flex');
      $('#modal-saque-titulo').html('<i class="fa fa-info-circle"></i> Solicitar Saque — Carteira WinPay');
    } else if (step === 'pagarme') {
      $('#saque-step2-pagarme').removeClass('d-none');
      $('#footer-step2-pagarme').removeClass('d-none').css('display', 'flex');
      $('#modal-saque-titulo').html('<i class="fa fa-info-circle"></i> Solicitar Saque — Pagar.me');
    }
  }

  $('#modal-saque').on('show.bs.modal', function () {
    showStep(1);
    $('#salvar').prop('disabled', false).text('Solicitar');
    $('#salvar-pagarme').prop('disabled', false).text('Solicitar');
  });

  $('#card-winpay').on('click', function () { showStep('winpay'); });

  $('#card-pagarme').on('click', function () {
    if (SALDO_PAGARME < 100) return;
    showStep('pagarme');
  });

  $('#btn-voltar-winpay, #btn-voltar-pagarme').on('click', function () { showStep(1); });

  // Validação WinPay
  $('#wp-valor').on('blur', function () {
    var val = parseFloat($(this).val().replace('.', '').replace(',', '.')) || 0;
    if (val > SALDO_WINPAY) {
      alert('Valor maior que o disponível (R$ ' + SALDO_WINPAY.toFixed(2).replace('.', ',') + ')!');
      $(this).val(SALDO_WINPAY.toFixed(2).replace('.', ',')); return;
    }
    if (val < SAQUE_MIN) {
      alert('Valor menor que o saque mínimo (R$ ' + SAQUE_MIN.toFixed(2).replace('.', ',') + ')!');
      $(this).val(SAQUE_MIN.toFixed(2).replace('.', ',')); return;
    }
    var limiteMes = SAQUE_MAX - (-SAQUE_MES);
    if (TIPO_DOC == 1 && limiteMes < SAQUE_MIN) {
      alert('Você atingiu o limite mensal de saque (R$ 1.900,00 para PF)!');
      $('#salvar').prop('disabled', true);
    }
  });

  $('#salvar').on('click', function (e) {
    e.preventDefault();
    var valor = $('#wp-valor').val().trim();
    var conta = $('#wp-conta').val();
    if (!valor) { alert('Informe o valor do saque!'); return; }
    if (!conta)  { alert('Selecione a conta bancária!'); return; }
    $(this).prop('disabled', true).text('Aguarde...');
    $.ajax({
      url: urlSacar, data: $('#form-winpay').serialize(), type: 'POST',
      success: function (resp) {
        alert(resp);
        if (resp.indexOf('sucesso') !== -1 || resp.indexOf('Salvo') !== -1) {
          location.reload();
        } else {
          $('#salvar').prop('disabled', false).text('Solicitar');
        }
      },
      error: function () {
        alert('Erro ao processar a solicitação.');
        $('#salvar').prop('disabled', false).text('Solicitar');
      }
    });
  });

  // Validação e submit PagarMe
  $('#pm-valor').on('blur', function () {
    var val = parseFloat($(this).val().replace('.', '').replace(',', '.')) || 0;
    if (val > SALDO_PAGARME) {
      alert('Valor maior que o disponível na Pagar.me (R$ ' + SALDO_PAGARME.toFixed(2).replace('.', ',') + ')!');
      $(this).val(SALDO_PAGARME.toFixed(2).replace('.', ',')); return;
    }
    if (val < 100) {
      alert('Valor mínimo para saque PagarMe é R$ 100,00!');
      $(this).val('100,00'); return;
    }
  });

  $('#salvar-pagarme').on('click', function (e) {
    e.preventDefault();
    var val = parseFloat($('#pm-valor').val().replace('.', '').replace(',', '.')) || 0;
    if (!val)           { alert('Informe o valor do saque!'); return; }
    if (val < 100)      { alert('Valor mínimo é R$ 100,00!'); return; }
    if (val > SALDO_PAGARME) { alert('Valor superior ao disponível!'); return; }
    $(this).prop('disabled', true).text('Aguarde...');
    // converte para centavos (sem ponto/vírgula)
    var valorCentavos = Math.round(val * 100);
    $.ajax({
      url: urlSacarPm,
      data: { valor_a_sacar: valorCentavos },
      type: 'POST',
      success: function (resp) {
        alert(resp);
        if (resp.indexOf('sucesso') !== -1) {
          location.reload();
        } else {
          $('#salvar-pagarme').prop('disabled', false).text('Solicitar');
        }
      },
      error: function () {
        alert('Erro ao processar a solicitação.');
        $('#salvar-pagarme').prop('disabled', false).text('Solicitar');
      }
    });
  });
}());
</script>
<?= $v->end() ?>
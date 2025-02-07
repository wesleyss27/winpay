<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper">
  <br>

  <!-- SEÇÃO PRINCIPAL -->
  <section class="content">
    <div class="container-fluid">

      <?php
      if (!@$usuarioPagarme->pagarme_conta) {
      ?>
        <script>
          if (confirm("Você precisa cadastrar ou atualizar seus dados bancários para poder transacionar")) {
            location.href = "<?= url("banco") ?>"
          }
        </script>
      <?php } ?>

      <?= $section->getTipoSecaoLayout() ?>

      <?php if (($section->getTipoSecao() == "subusuario" && @$permissao->ver == 1) || $section->getTipoSecao() == "usuario") : ?>

        <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
          <small><i style="color: green; font-size: 30px">•</i> <?= $usuariosOnline ?> usuários online agora <br></small>
        <?php endif ?>

        <small>*Dados referentes a sua comissão</small>

        <div class="row">

          <!-- HOJE -->
          <div class="col-lg-3 col-12">
            <!-- small-box -->
            <div class="small-box" style="background: #2B0582; color: #fff">
              <div class="inner">
                <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$hoje + $hojeDrop, 2, ",", "."); ?></h3>

                <p style="font-family:arial; text-shadow: 1px 1px 2px #333; font-size:23px">Hoje</p>
              </div>
              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
            </div>
            <!-- ./small-box -->
          </div>
          <!-- FIM HOJE -->

          <!-- ONTEM -->
          <div class="col-lg-3 col-12">
            <!-- small box -->
            <div class="small-box" style="background: #012F6A; color: #fff">
              <div class="inner">
                <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$ontem + $ontemDrop, 2, ",", "."); ?></h3>

                <p style="font-family:arial; text-shadow: 1px 1px 2px #333; font-size:23px">Ontem</p>
              </div>
              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
            </div>
            <!-- small box -->
          </div>
          <!-- FIM ONTEM -->

          <!-- MÊS -->
          <div class="col-lg-3 col-12">
            <!-- small box -->
            <div class="small-box" style="background: #01606A">
              <div class="inner">
                <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format($mes + $mesDrop, 2, ",", "."); ?></h3>

                <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:23px">Esse mês</p>
              </div>
              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>

            </div>
            <!-- small box -->
          </div>
          <!-- FIM MÊS -->

          <!-- SACADO MÊS -->
          <div class="col-lg-3 col-12">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">

                <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= @$saqueMes ? "R$ " . number_format(-$saqueMes, 2, ",", ".") : "R$ 0,00"; ?></h3>

                <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:23px">Sacado no Mês</p>
              </div>
              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>

            </div>
          </div>
          <!-- FIM SACADO MÊS -->

        </div>
        <!-- /.row -->

        <!-- .row -->
        <div class="row">

          <!-- SALDO DISPONÍVEL -->
          <div class="col-lg-3 col-12">
            <!-- small box -->
            <div class="small-box" style="background: #2B0582;">
              <div class="inner">

                <h3 style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$saldo->valor + (@$saldoPagarme->available_amount / 100), 2, ",", "."); ?></h3>

                <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:23px">Saldo Em Conta</p>
              </div>
              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
              <a href="<?= url("comissao") ?>" class="small-box-footer" style="font-family:inter">Saque <i class="fas fa-arrow-circle-right"></i></a>

            </div>
            <!-- small box -->
          </div>
          <!-- FIM SALDO DISPONÍVEL -->

          <!-- PENDENTE -->
          <div class="col-lg-3 col-12">
            <!-- small box -->
            <div class="small-box" style="background: #012F6A">
              <div class="inner">
                <h3 style="font-family:arial; color: #fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format($pendente, 2, ",", "."); ?></h3>

                <p style="font-family:arial; color: #fff; text-shadow: 1px 1px 2px #333; font-size:23px">A Receber</p>
              </div>
              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
              <a href="<?= url("antecipacao") ?>" class="small-box-footer" style="font-family:inter; color: #fff">Antecipar <i class="fas fa-arrow-circle-right"></i></a>

            </div>
          </div>
          <!-- FIM PENDENTE -->

          <!-- TOTAL -->
          <div class="col-lg-3 col-12">
            <!-- small box -->
            <div class="small-box" style="background: #01606A">
              <div class="inner">

                <h3 style="font-family:inter; color: #fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$saldo->valor + @$pendente, 2, ",", "."); ?></h3>
                <p style="font-family:arial; color: #fff; text-shadow: 1px 1px 2px #333; font-size:23px; font-weight:lighter">Saldo Total</p>

              </div>

              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
              <a href="<?= url("comissao") ?>" class="small-box-footer" style="font-family:inter">Saldos <i class="fas fa-arrow-circle-right"></i></a>

            </div>
          </div>
          <!-- FIM TOTAL -->

          <!-- SALDO BLOQUEADO -->
          <div class="col-lg-3 col-12">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">

                <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= @$bloqueado ? "R$ " . number_format(-$bloqueado, 2, ",", ".") : "R$ 0,00"; ?></h3>

                <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:23px">Saldo Bloqueado</p>
              </div>
              <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
              <a href="<?= url("vendas?status=9") ?>" class="small-box-footer" style="font-family:inter">Vendas Bloqueadas <i class="fas fa-arrow-circle-right"></i></a>

            </div>
          </div>
          <!-- FIM SALDO BLOQUEADO -->

        </div>
        <!-- GRÁFICO E BANNER ST4RT -->
        <div class="row">

          <!-- GRÁFICO -->
          <div class="col-lg-12 col-12">
            <div class="card">

              <div class="card-header">
                <h3 style="font-family:inter" class="card-title">Suas comissões - mês atual e anterior</h3>

                <div class="card-tools">
                  Atual: <i class="fa fa-chart-bar" style="color:green"></i> | Anterior: <i class="fa fa-chart-bar" style="color:blue"></i>
                </div>
              </div>
              <!-- /.card-header -->

              <div class="card-body">
                <small style="text-align: center">Clique em uma opção para ocultar</small>
                <div class="chart">
                  <canvas id="areaChart" style="height: 180px; width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->

            </div>
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h5>Suas Vendas</h5>
              </div>

              <div class="card-body">

                <!-- RELATÓRIO SIMPLES USUÁRIO -->
                <div class="row">
                  <div class="col-md-4 bg-azul-dark" style="text-align: center; padding: 5px; border-radius: 7px; border: 2px solid #fff">
                    Vendas do mês <br>
                    <?= $vendasAtual + $vendasCoprodutor ?>
                  </div>

                  <div class="col-md-4 bg-azul-dark" style="text-align: center; padding: 5px; border-radius: 7px; border: 2px solid #fff">
                    Total faturado de vendas <br>
                    <?php
                    if ($_SESSION["tipo"] != 5) {
                      $soma = $vendasAtualSoma->soma;
                    } else {
                      $soma = $mes;
                    }
                    ?>
                    <?= "R$ " . number_format($soma, 2, ",", "."); ?>
                  </div>

                  <div class="col-md-4 bg-azul-dark" style="text-align: center; padding: 5px; border-radius: 7px; border: 2px solid #fff">
                    <?php if ($_SESSION["tipo"] != 5) : ?>
                      Ticket médio <br>
                    <?php else : ?>
                      Comissão média <br>
                    <?php endif ?>

                    <?php if ($vendasAtual) $somaVendas = $soma / $vendasAtual; ?>
                    <?php if ($vendasCoprodutor) $somaValores = $mes / $vendasCoprodutor; ?>
                    <?= "R$ " . number_format(@$somaVendas + @$somaValores, 2, ",", ".") ?>

                  </div>
                </div>
                <!-- FIM RELATÓRIO SIMPLES USUÁRIO -->
                <br>

                <!-- RELATÓRIO REDROCKET -->
                <?php if ($_SESSION["tipo"] == 1) : ?>
                  <h5>Geral REDROCKET <small>(vendas pagas)</small></h5>

                  <div class="row">
                    <div class="col-md-4 bg-azul-dark" style="text-align: center; padding: 5px; border-radius: 7px; border: 2px solid #fff">
                      Vendas do mês <br>
                      <?= $vendasTotal ?>
                    </div>

                    <div class="col-md-4 bg-azul-dark" style="text-align: center; padding: 5px; border-radius: 7px; border: 2px solid #fff">
                      Total vendas <br>
                      <?php $soma = $vendasTotalSoma->soma; ?>
                      <?= "R$ " . number_format($soma, 2, ",", "."); ?>
                    </div>

                    <div class="col-md-4 bg-azul-dark" style="text-align: center; padding: 5px; border-radius: 7px; border: 2px solid #fff">

                      Ticket médio <br>

                      <?php if ($vendasTotal) : ?>
                        <?= "R$ " . number_format($soma / $vendasTotal, 2, ",", ".") ?>
                      <?php else : ?>
                        R$ 0,00
                      <?php endif ?>

                    </div>
                  </div>
                <?php endif ?>
                <!-- FIM RELATÓRIO REDROCKET -->

              </div>
              <!-- /.card-body -->

            </div>
            <!-- /.card -->

          </div>
          <!-- FIM GRÁFICO -->

          <!-- BANNERS VENDA -->
          <!-- <div class="col-lg-4 col-12">
            <div class="small-box bg-default" style="background:#f8f8f8;">
              <div class="inner">
                <a href="https://play.google.com/store/apps/details?id=com.octapay.octapay" target="_blank">
                  <img style="width:100%" src="< ?= url("themes/imgs/imagens-app/banner.jpeg") ?>" alt="Faça o download do app OctaPay e gerencie agora o seu negócio">
                </a>
              </div>
            </div>

          </div> -->

        </div>
        <!-- FIM GRÁFICO E BANNER -->

      <?php endif ?>

    </div><!-- container-fluid -->

  </section><!-- FIM SEÇÃO PRINCIPAL -->

</div>
<!-- content wraper -->

<!-- POPUP NOTIFICAÇÃO -->
<div class="modal fade" id="modalUpsell">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-info-circle"></i> Novidades</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-md-3 center">
            <a href="https://play.google.com/store/apps/details?id=com.octapay.octapay" target="_blank">
              <img class="img-banner-home" src="<?= url("themes/imgs/imagens-app/005.jpeg") ?>" alt="Faça o download do app OctaPay e gerencie agora o seu negócio">
            </a>
          </div>

          <div class="col-md-3 desktop">
            <a href="https://play.google.com/store/apps/details?id=com.octapay.octapay" target="_blank">
              <img class="img-banner-home" src="<?= url("themes/imgs/imagens-app/006.png") ?>" alt="Faça o download do app OctaPay e gerencie agora o seu negócio">
            </a>
          </div>

          <div class="col-md-6 msg-banner-home">
            <h4 class="txt-verde-octa3">Estamos também na Playstore</h4>
            <p style="font-family: Inter; font-size: 17px; color: #555">
              Chegou o app da OctaPay! Acompanhe e receba notificações de suas vendas também pelo celular. Baixe o app e aproveite a novidade!
            </p>
            <a href="https://play.google.com/store/apps/details?id=com.octapay.octapay" target="_blank">
              <img src="<?= url("themes/imgs/imagens-app/android-download.png") ?>" alt="Faça o download do app OctaPay e gerencie agora o seu negócio" width="45%">
            </a>
          </div>
        </div>


      </div>

      <div class="modal-footer justify-content-between">
        <button class="btn btn-danger" data-dismiss="modal">Fechar</button>
        <!-- <a href="<?= url("produtos") ?>" class="btn btn-success" data-dismiss="modal">Acessar Produtos</a> -->
      </div>
    </div>
  </div>
</div>
<!-- FIM POPUP NOTIFICAÇÃO -->

<div class="row">
  <div class="col-md-6">
    <!--?php 
        if ($comissaoAtual) {
          foreach ($comissaoAtual as $comissaoAtual) {
            echo date("d", strtotime($comissaoAtual->dia)) . " - R$ ";
            echo $comissaoAtual->valor . "<br>";
          }
        }
    ?-->
  </div>

  <div class="col-md-6">
    <?php
    /*if ($comissaoAnterior) {
      foreach ($comissaoAnterior as $comissaoAnterior) {
        echo date("d", strtotime($comissaoAnterior->dia)) . " - R$ ";
        echo $comissaoAnterior->valor . "<br>";
      }
    }*/
    ?>

    <!-- ATRIBUI OS VALORES DAS COMISSÕES AO DIA DO MÊS -->
    <?php
    $valorDia = array();
    $diaComissao = array();

    $valorDiaAnterior = array();
    $diaComissaoAnterior = array();
    $dias = array();

    /** CONJUNTO DOS DIAS */
    for ($j = 1; $j <= 31; $j++) {
      $dias[$j] = $j;
      //echo "Dia $j <br>";
    }

    if ($comissaoAtual) {

      foreach ($comissaoAtual as $comissaoAtual) {
        // var_dump($comissaoAtual->dia, $comissaoAtual->valor, "<br>");
        $dia = date("d", strtotime($comissaoAtual->dia));

        /** CONJUNTO DA COMISSÃO DO MES ATUAL */
        $valorDia[$dia] = $comissaoAtual->valor;

        /** CONJUNTO DOS DIAS DO MES ATUAL */
        $diaComissao[$dia] = $dia;
        //echo "Dia comissao $dia {$valorDia[$dia]} <br>";
      }

      foreach ($dias as $dia1) {
        //echo "Dia $dias  - dia comissao {$diaComissao[$dias]} <br>";

        if ($dia1 < 10)
          $dia1 = "0" . $dia1;

        /** VERIFICA SE O DIA EXISTE NO BANCO DE DADOS E ATRIBUI A COMISSÃO PARA O DIA CORRESPONDENTE */
        if (@$diaComissao[$dia1]) {
          //echo "dia {$diaComissao[$dia1]} - $valorDia[$dia1] - existe <br>";
          $comAtual[$dia1] = $valorDia[$dia1];
        } else {
          //echo "dia $dia1 não existe <br>";
          $comAtual[$dia1] = 0;
        }
      }
    } else {
      foreach ($dias as $dia1) {
        $comAtual[$dia1] = 0;
      }
    }

    if ($comissaoAnterior) {
      foreach ($comissaoAnterior as $comissaoAnterior) {
        $dia = date("d", strtotime($comissaoAnterior->dia));

        /** CONJUNTO DA COMISSÃO DO MES ANTERIOR */
        $valorDiaAnterior[$dia] = $comissaoAnterior->valor;

        /** CONJUNTO DOS DIAS DO MES ANTERIOR */
        $diaComissaoAnterior[$dia] = $dia;
        //echo "Dia comissao $dia {$valorDia[$dia]} <br>";
      }

      foreach ($dias as $dia2) {
        //echo "Dia $dias  - dia comissao {$diaComissao[$dias]} <br>";

        if ($dia1 < 10)
          $dia1 = "0" . $dia1;

        /** VERIFICA SE O DIA EXISTE NO BANCO DE DADOS E ATRIBUI A COMISSÃO PARA O DIA CORRESPONDENTE */
        if (@$diaComissaoAnterior[$dia2]) {
          //echo "dia {$diaComissaoAnterior[$dia2]} - $valorDiaAnterior[$dia2] - existe <br>";
          $comAnterior[$dia2] = $valorDiaAnterior[$dia2];
        } else {
          //echo "dia $dia2 não existe <br>";
          $comAnterior[$dia2] = 0;
        }
      }
    } else {
      foreach ($dias as $dia2) {
        $comAnterior[$dia2] = 0;
      }
    }
    ?>
  </div>
</div>

<?= $v->start("scripts"); ?>
<script>
  // if (<?= $_SESSION["tipo"] ?> != 5 && <?= $_SESSION["tipo"] ?> != 6)
  //   $('#modalUpsell').modal('show');

  $(function() {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // GRÁFICO DE VENDAS
    var canvasVendas = $('#areaChart').get(0).getContext('2d');

    <?php
    $dados = array();
    for ($i = 1; $i <= 31; $i++) {
      $dados[$i] = $i;
    }
    ?>

    var dadosVendas = {
      labels: [<?php foreach ($dados as $dado) echo $dado . "," ?>],
      datasets: [{
          label: 'Atual',
          backgroundColor: 'rgba(163,249,160,0.5)',
          borderColor: 'rgba(60,179,113, 0.4)',
          pointRadius: true,
          pointBorderWidth: '3',
          pointBorderColor: '#3bab6a',
          pointColor: '#3bab6a',
          pointStyle: 'circle',
          pointStrokeColor: 'rgba(60,179,113, 0.4)',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(60,179,113, 0.4)',
          data: [<?php foreach ($comAtual as $atual) echo $atual . "," ?>]
        },
        {
          label: 'Anterior',
          backgroundColor: 'rgba(160, 197, 228,0.5)',
          borderColor: 'rgba(76, 144, 203,0.7)',
          pointRadius: true,
          pointBorderWidth: '3',
          pointBorderColor: 'rgb(80, 120, 255)',
          pointColor: 'rgb(80, 120, 255)',
          pointStyle: 'circle',
          pointStrokeColor: '#c1c7d1',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(0,0,139,0.7)',
          data: [<?php foreach ($comAnterior as $anterior) echo $anterior . "," ?>]
        }
      ]
    }

    var opcoesVendas = {
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        display: true
      },
      scales: {
        xAxes: [{
          gridLines: {
            display: false,
          }
        }],
        yAxes: [{
          gridLines: {
            display: false,
          }
        }]
      }
    }

    var graficoVendas = new Chart(canvasVendas, {
      type: 'line',
      data: dadosVendas,
      options: opcoesVendas
    })
    // FIM GRÁFICO DE VENDAS


  })
</script>

<?= $v->end(); ?>
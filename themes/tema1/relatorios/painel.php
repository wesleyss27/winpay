<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1) {
  header("Location: " . URL_BASE);
}
?>

<div class="content-wrapper">
  <br>

  <section class="content">

    <div class="card">
      <div class="card-body">
        <form action="#" method="post">
          <div class="row">
            <div class="col-md-3">
              Produto:
              <select name="produto" id="produto" class="form-control">
                <option value="">Selecione o Produto...</option>
                <?php
                if (@$produtos) :
                  $produtos1 = $produtos->find()->fetch(true);
                ?>
                  <?php foreach ($produtos1 as $produto) : ?>
                    <option value="<?= $produto->id ?>" <?php if (@$_POST["produto"] == $produto->id) echo "selected";
                                                        echo "" ?>><?= $produto->nome ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-md-3">
              Produtor:
              <select name="produtor" id="produtor" class="form-control">
                <option value="">Selecione o Produto...</option>
                <?php
                if (@$produtores) :
                ?>
                  <?php foreach ($produtores as $produtor) : ?>
                    <option value="<?= $produtor->id ?>" <?php if (@$_POST["produtor"] == $produtor->id) echo "selected";
                                                          echo "" ?>><?= $produtor->nome ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-md-2">
              Data Início:
              <input type="date" class="form-control" name="inicio" value="<?= @$_POST["inicio"] ?>">
            </div>
            <div class="col-md-2">
              Data Fim:
              <input type="date" class="form-control" name="fim" value="<?= @$_POST["fim"] ?>">
            </div>
            <div class="col-md-1">
              <br>
              <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
          </div>

        </form>

        <?php
        function diferenca($data1, $data2)
        {
          $diferenca = strtotime($data2) - strtotime($data1);
          $dias = floor($diferenca / (60 * 60 * 24));
          return $dias;
        }

        if ($_POST)
          $diasCount = diferenca(@$_POST["inicio"], @$_POST["fim"]);
        else
          $diasCount = diferenca(date("Y-m-01"), date("Y-m-d"));

        $dias = array();
        $inicio = @$_POST["inicio"] ? @$_POST["inicio"] : date("Y-m-01");
        for ($i = 0; $i <= $diasCount; $i++) {
          $dias[$i] = date("Y-m-d", strtotime("+$i days", strtotime($inicio)));
          //echo $dias[$i] . " | ";
        }

        $valorDia = array();
        $diaComissao = array();

        // var_dump($dados);
        if ($vendasDia) {

          foreach ($vendasDia as $venda) {
            $dia = $venda->dia;
            //echo "$dia ";

            /** CONJUNTO DA COMISSÃO DO MES ATUAL */
            $valorDia[$dia] = $venda->valor;

            /** CONJUNTO DOS DIAS DO MES ATUAL */
            $diaComissao[$dia] = $dia;
            // echo "Dia comissao $dia {$valorDia[$dia]} <br>";
          }

          foreach ($dias as $dia1) {
            // echo "Dia $dia1  - dia comissao <br>";

            /** VERIFICA SE O DIA EXISTE NO BANCO DE DADOS E ATRIBUI A COMISSÃO PARA O DIA CORRESPONDENTE */
            if (@$diaComissao[$dia1]) {
              // echo "dia {$diaComissao[$dia1]} - $valorDia[$dia1] - existe <br>";
              $comAtual[$dia1] = $valorDia[$dia1];
              $diaCom[$dia1] = array(@$diaComissao[$dia1], $valorDia[$dia1]);
            } else {
              // echo "dia $dia1 não existe <br>";
              $comAtual[$dia1] = 0;
              $diaCom[$dia1] = array(@$dia1, 0);
            }
          }
        } else {
          foreach ($dias as $dia1) {
            $comAtual[$dia1] = 0;
          }
        }
        ?>
      </div>
    </div>

    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->

      <h3>Resumo Transações Pagas No Período Informado</h3>
      <!-- .row -->
      <div class="row">
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-primary" style="background:url(<?= url('themes/tema1/images/logo.png') ?>); background-size:cover; background-repeat:no-repeat; background-position:left">
            <div class="inner">
              <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px"><b><?= @$mes ? "R$ " . number_format($mes, 2, ",", ".") : "R$ 0,00"; ?></b></h3>
              <p style="font-family:arial; text-shadow: 1px 1px 2px #333; font-size:20px">Total Bruto do Mês</p>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-warning">
            <div class="inner">
              <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= @$wirecard ? "R$ " . number_format($wirecard, 2, ",", ".") : "R$ 0,00"; ?></h3>
              <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:20px">Taxas Adquirentes*</p>
            </div>
          </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <?php $octa = ($juros - $wirecard) + $redrocket; ?>
              <h3 style="font-family:inter; color: #fff; text-shadow: 1px 1px 2px #333; font-size:25px"><b><?= "R$ " . number_format($octa, 2, ",", "."); ?></b></b></h3>
              <p style="font-family:arial; color: #fff; text-shadow: 1px 1px 2px #333; font-size:20px">Saldo RedRocket</p>
            </div>
          </div>
        </div>
        <!-- ./col -->

        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-info">
            <div class="inner">
              <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format($mes - $wirecard - $octa, 2, ",", "."); ?></h3>
              <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:20px">Total Clientes</p>
            </div>
          </div>
        </div>
        <!-- ./col -->

      </div>
      <!-- ./row -->

      <!-- .row -->
      <div class="row">
        <!-- JUROS COBRADO -->
        <div class="col-lg-3">
          <div class="small-box">
            <div class="inner">
              Juros cobrado <br>
              <?= "R$ " . $juros ?>
            </div>
          </div>
        </div>
        <!-- FIM FUROS COBRADO -->

        <!-- TAXA WIRECARD -->
        <div class="col-lg-3">
          <div class="small-box">
            <div class="inner">
              Taxas Adquirentes <br>
              <?= "R$ " . $wirecard ?>
            </div>
          </div>
        </div>
        <!-- FIM TAXA WIRECARD -->

        <!-- TAXA RedRocket -->
        <div class="col-lg-3">
          <div class="small-box">
            <div class="inner">
              Taxa RedRocket Cobrada <br>
              <?= "R$ " . $redrocket ?>
            </div>
          </div>
        </div>
        <!-- FIM TAXA RedRocket -->

        <!-- TAXA RedRocket -->
        <div class="col-lg-3">
          <div class="small-box">
            <div class="inner">
              Taxa Antecipação Geral <br>
              <?= "R$ " . @number_format($antecipacao->valor, 2, ",", ".") ?>
            </div>
          </div>
        </div>
        <!-- FIM TAXA RedRocket -->
      </div>
      <!-- ./row -->

      <small>* Valor esperado</small><br>
      <script>
        console.log("<?= "Juros cobrado: $juros - Taxa Wirecard: $wirecard - Taxa RedRocket cobrada: $redrocket" ?>")
      </script>
      <!-- /.row -->

      <h3>Resumo Geral RedRocket</h3>
      <div class="row">
        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$saldo, 2, ",", "."); ?></h3>
              <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:20px">Saldo Disponível Geral</p>
            </div>
          </div>
        </div>
        <!-- ./col -->

        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-warning">
            <div class="inner">
              <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format($pendente, 2, ",", "."); ?></h3>
              <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:20px">Saldo a receber</p>
            </div>
          </div>
        </div>
        <!-- ./col -->

        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-danger">
            <div class="inner">
              <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$saqueOcta, 2, ",", "."); ?></h3>
              <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:20px">Sacado RedRocket</p>
            </div>
          </div>
        </div>
        <!-- ./col -->

        <!-- ./col -->
        <div class="col-lg-3 col-12">
          <!-- small box -->
          <div class="small-box bg-danger">
            <div class="inner">
              <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format((-1) * @$saqueMes, 2, ",", "."); ?></h3>
              <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:20px">Sacado Clientes</p>
            </div>
          </div>
        </div>
        <!-- ./col -->

      </div>
      <!-- Main row -->


      <div class="card">
        <!--?php if(@$_POST["produto"]): ?-->
        <div class="card-header">
          <h4>
            Dias das transações
            <small><a href="<?= url("relatorios/exportar?tabela=vendas") ?>" class="badge badge-success" target="_blank">xls</a></small>
          </h4>
        </div>

        <div class="card-body">
          <div class="chart">
            <canvas id="areaChart" style="height: 260px; width: 100%;"></canvas>
          </div>
        </div>
      </div>

      <!-- TOTAL DE VENDAS -->
      <div class="card">
        <div class="card-header">
          <h4>Dashboard
            <Small style="float:right">
              Análitico das transações
              <a href="<?= url("relatorios/exportar?tabela=analitico") ?>" class="badge badge-success" target="_blank">xls</a>
            </small>
          </h4>
        </div>

        <div class="card-body" style="background:#f4f6f9">
          <div class="card-body">
            <div class="row">

              <div class="col-md-3">
                <div class="card card-body bg-lightblue">
                  <h6>Total de Trasações</h6>
                  <h3>R$ <?= number_format($dados["valorVendas"], 2, ",", ".") ?> <i class="fas fa-money-bill-alt" style="font-size:30px; color:#ffffff"></i></h3>
                  <?php
                  $analitico[0] = array("Total de Trasa&ccedil;&otilde;es", $dados["totalVendas"], $dados["valorVendas"], 100)
                  ?>

                  <p class="mt20"><span class="label label-danger m-r-10"><?= $dados["totalVendas"] ?> - </span>Transações</p>
                </div>
              </div>

              <div class="col-xl-3 col-md-6">
                <div class="card card-body bg-green">

                  <h6>
                    Transações Pagas •
                    <b>
                      <?php
                      if ($dados["totalVendas"] > 0)
                        $p = ($dados["cartoesPagos"] + $dados["boletosPagos"] + $dados["pixPagos"]) / $dados["totalVendas"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%
                    </b>
                  </h6>

                  <h3>
                    R$ <?= number_format(@$dados["valorCartaoPago"] + @$dados["valorBoletoPago"] + @$dados["valorPixPago"], 2, ",", ".") ?>
                    <i class="fas fa-dollar-sign" style="font-size:30px; color:#ffffff"></i>
                  </h3>
                  <p class="mt20">

                    <?php
                    $analitico[20] = array("Total de Trasa&ccedil;&otilde;es Pagas", $dados["totalVendas"], $dados["valorVendas"], 100)
                    ?>
                    <span class="label label-danger m-r-10"><?= (@$dados["cartoesPagos"] + @$dados["boletosPagos"] + @$dados["pixPagos"]) ?> / <?= $dados["totalVendas"] ?> - </span>Transações
                  </p>

                </div>
              </div>

              <div class="col-xl-3 col-md-6">
                <div class="card card-body badge-warning">
                  <h6>
                    Chargeback •
                    <b>
                      <?php
                      if ($dados["cartoes"] > 0)
                        $p = $dados["chargeback"] / $dados["cartoes"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%
                    </b>
                    <?php
                    $analitico[1] = array("Chargeback", $dados["chargeback"], $dados["valorChargeback"], $p)
                    ?>
                  </h6>
                  <h3 class="m-b-0">
                    R$ <?= number_format($dados["valorChargeback"], 2, ",", ".") ?>
                    <i class="fas fa-search-dollar" style="font-size:30px; color:#000000"></i>
                  </h3>

                  <p class="mt20"><span class="label label-danger m-r-10"><?= $dados["chargeback"] ?> - </span>Chargeback(s)</p>

                </div>
              </div>

              <div class="col-xl-3 col-md-6">
                <div class="card card-body bg-danger">
                  <h6>
                    Reembolsos •
                    <b>
                      <?php
                      if ($dados["totalVendas"] > 0)
                        $p = $dados["reembolsos"] / $dados["totalVendas"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%
                    </b>
                  </h6>
                  <h3 class="m-b-0">
                    R$ <?= number_format($dados["valorReembolso"], 2, ",", ".") ?> <i class="fas fa-hand-holding-usd" style="font-size:30px; color:#fffff"></i>
                  </h3>

                  <?php
                  $analitico[1] = array("Reembolsos", $dados["reembolsos"], $dados["valorReembolso"], $p)
                  ?>
                  <p class="mt20"><span class="label label-danger m-r-10"><?= $dados["reembolsos"] ?> - </span>Reembolso(s)</p>


                </div>
              </div>
            </div>
          </div>

          <!-- LINHA 2 -->

          <!-- Conversão cartão -->
          <div class="row">
            <div class="col-md-4">
              <div class="card card-body">
                <div class="row align-items-center justify-content-center">
                  <i class="fas fa-credit-card" style="font-size:30px; color:#009788"></i>

                  <div class="col text-right">
                    <h3>R$ <?= number_format($dados["valorCartaoPago"], 2, ",", ".") ?></h3>
                    <h5 class="text-c-blue mb-0">
                      <?php
                      $analitico[2] = array("Vendas Cart&atilde;o", $dados["cartoes"], $dados["valorCartao"], $p)
                      ?><br>
                      <?php
                      if ($dados["totalVendas"] > 0)
                        $p = $dados["cartoes"] / $dados["totalVendas"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>% - <span class="text-muted">Total Cartão</span></h5>
                  </div>
                </div>

                <hr>
                <div class="row align-items-center justify-content-center card-active">
                  <div class="col-6">
                    <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
                      <?php
                      if ($dados["cartoes"] > 0)
                        $p = $dados["cartoesPagos"] / $dados["cartoes"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Cancelados: </span>
                      <?php
                      if ($dados["cartoes"] > 0)
                        $p = $dados["cartoesCancelados"] / $dados["cartoes"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Conversão boleto -->
            <div class="col-md-4">
              <div class="card card-body">
                <div class="row align-items-center justify-content-center">
                  <i class="fas fa-barcode" style="font-size:35px; color:#3F51B5"></i>

                  <div class="col text-right">
                    <h3>R$ <?= number_format($dados["valorBoletoPago"], 2, ",", ".") ?></h3>
                    <h5 class="text-c-blue mb-0">
                      <?php
                      $analitico[6] = array("Boletos", $dados["boletos"], $dados["valorBoleto"], $p)
                      ?><br>
                      <?php
                      if ($dados["totalVendas"] > 0)
                        $p = $dados["boletos"] / $dados["totalVendas"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>% - <span class="text-muted">Total Boletos</span></h5>
                  </div>
                </div>

                <hr>
                <div class="row align-items-center justify-content-center card-active">
                  <div class="col-6">
                    <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
                      <?php
                      if ($dados["boletos"] > 0)
                        $p = $dados["boletosPagos"] / $dados["boletos"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Vencidos: </span>
                      <?php
                      if ($dados["boletos"] > 0)
                        $p = $dados["boletosCancelados"] / $dados["boletos"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Conversão Pix -->
            <div class="col-md-4">
              <div class="card card-body">
                <div class="row align-items-center justify-content-center">
                  <img src="<?= url("themes/tema1/images/pix-text-info.png"); ?>" style="width:30px"></i>

                  <div class="col text-right">
                    <h3>R$ <?= number_format($dados["valorPixPago"], 2, ",", ".") ?></h3>
                    <h5 class="text-c-blue mb-0">
                      <?php
                      $analitico[2] = array("Vendas Cart&atilde;o", $dados["pix"], $dados["valorPix"], $p)
                      ?><br>
                      <?php
                      if ($dados["totalVendas"] > 0)
                        $p = $dados["pix"] / $dados["totalVendas"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>% - <span class="text-muted">Total Pix</span></h5>
                  </div>
                </div>

                <hr>
                <div class="row align-items-center justify-content-center card-active">
                  <div class="col-6">
                    <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
                      <?php
                      if ($dados["pix"] > 0)
                        $p = $dados["pixPagos"] / $dados["pix"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Cancelados: </span>
                      <?php
                      if ($dados["pix"] > 0)
                        $p = $dados["pixCancelados"] / $dados["pix"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- FIM LINHA 2 -->

          <!-- LINHA 3 -->

          <div class="row">
            <!-- Conversão Upsell -->
            <div class="col-md-4">
              <div class="card card-body">
                <div class="row align-items-center justify-content-center">
                  <i class="fas fa-shopping-cart" style="font-size:35px; color:#8BC24A"></i>

                  <div class="col text-right">
                    <h3>R$ <?php
                            $upsellValores = $dados["valorUpsell"];
                            echo number_format($upsellValores, 2, ",", ".") ?></h3>
                    <h5 class="text-c-blue mb-0">
                      <?php
                      if ($dados["totalVendas"] > 0)
                        if ($dados["valorUpsell"] == 0)
                          $p = "0";
                        else
                          $p = $dados["upsell"] / ($dados["totalVendas"]) * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>% - <span class="text-muted">Upsell</span> - <?php echo $upsell = $dados["upsell"]; ?></h5>
                  </div>
                </div>

                <hr>
                <div class="row align-items-center justify-content-center card-active">
                  <div class="col-6">
                    <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
                      <?php
                      if ($dados["upsell"] > 0)
                        $p = $dados["upsellPago"] / $dados["upsell"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Cancelados: </span>
                      <?php
                      if ($dados["upsell"] > 0)
                        $p = $dados["upsellCancelados"] / $dados["upsell"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Conversão OrderBump -->
            <div class="col-md-4">
              <div class="card card-body">
                <div class="row align-items-center justify-content-center">
                  <i class="fas fa-funnel-dollar" style="font-size:35px; color:#8BC24A"></i>

                  <div class="col text-right">
                    <h3>R$ <?php
                            $upsellValores = $dados["valorOrderBump"];
                            echo number_format($upsellValores, 2, ",", ".") ?></h3>
                    <h5 class="text-c-blue mb-0">
                      <?php
                      if ($dados["totalVendas"] > 0)
                        if ($dados["valorOrderBump"] == 0)
                          $p = "0";
                        else
                          $p = $dados["orderBump"] / ($dados["totalVendas"]) * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>% - <span class="text-muted">Order Bump</span> - <?php echo $upsell = $dados["orderBump"]; ?></h5>
                  </div>
                </div>

                <hr>
                <div class="row align-items-center justify-content-center card-active">
                  <div class="col-6">
                    <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
                      <?php
                      if ($dados["orderBump"] > 0)
                        $p = $dados["orderBumpPago"] / $dados["orderBump"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Cancelados: </span>
                      <?php
                      if ($dados["orderBump"] > 0)
                        $p = $dados["orderBumpCancelados"] / $dados["orderBump"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Televendas -->
            <div class="col-md-4">
              <div class="card card-body">
                <div class="row align-items-center justify-content-center">
                  <i class="fas fa-headset" style="font-size:35px; color:#8BC24A"></i>

                  <div class="col text-right">
                    <h3>R$ <?php
                            $upsellValores = $dados["valorTelevendas"];
                            echo number_format($upsellValores, 2, ",", ".") ?></h3>
                    <h5 class="text-c-blue mb-0">
                      <?php
                      if ($dados["totalVendas"] > 0)
                        if ($dados["valorTelevendas"] == 0)
                          $p = "0";
                        else
                          $p = $dados["televendas"] / ($dados["totalVendas"]) * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>% - <span class="text-muted">Televendas</span> - <?php echo $upsell = $dados["televendas"]; ?></h5>
                  </div>
                </div>

                <hr>
                <div class="row align-items-center justify-content-center card-active">
                  <div class="col-6">
                    <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
                      <?php
                      if ($dados["televendas"] > 0)
                        $p = $dados["televendasPago"] / $dados["televendas"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-6">
                    <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Cancelados: </span>
                      <?php
                      if ($dados["televendas"] > 0)
                        $p = $dados["televendasCancelados"] / $dados["televendas"] * 100;
                      else
                        $p = "0";
                      echo number_format($p, 2, ",", ".");
                      ?>%</h6>
                    <div class="progress">
                      <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- LINHA 4 -->
          <div class="row">

            <!-- Conversão Clientes -->

            <div class="col-xl-4 col-lg-5 center">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                  <h5 class="m-0 font-weight-bold text-primary">Top 20 Clientes</h5>
                </div>
                <!-- Card Body -->
                <div class="card-body p-0">
                  <div class="table-responsive" style="overflow:scroll; height: 280px">
                    <?php if (@$cliente) : ?>

                      <script>
                        var dados = [];
                      </script>

                      <table class="table no-wrap p-table">
                        <thead class="bg-info">
                          <tr class="border-0">
                            <th class="border-0 center">Cliente</th>
                            <th class="border-0 center">Vendas</th>
                            <th class="border-0 center">Faturamento</th>
                          </tr>
                        </thead>

                        <tbody>

                          <?php
                          $i = 1;

                          $maior = 0;

                          foreach ($cliente as $cliente) :
                            if ($i > 20)
                              break;

                            if (@$_POST["inicio"] && $_POST["fim"])
                              $filtros = "data_finalizada BETWEEN '" . $_POST["inicio"] . " 00:00:00' AND '" . $_POST["fim"] . " 23:59:59' AND ";

                          ?>

                            <?php
                            // if ($vendapaga > $maior) {
                            //   $maior = $vendapaga;

                            $nomeCliente = $cliente->dataProdutor()->nome;
                            $fotoCliente = $cliente->dataProdutor()->foto;
                            // }

                            ?>
                            <tr class="cli" id="afiliacao<?= $cliente->cliente; ?>">
                              <td style="text-align:center"><?= $cliente->dataProdutor()->nome ?></td>
                              <td style="text-align:center"><?= $cliente->total; ?></td>
                              <td style="text-align:center"><?= "R$ " . number_format($cliente->soma, 2, ",", "."); ?></td>
                            </tr>

                          <?php $i++;
                          endforeach; ?>
                        </tbody>
                      </table>

                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Conversão afiliados -->

            <div class="col-xl-4 col-lg-5 center">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                  <h5 class="m-0 font-weight-bold text-primary">Top 10 Afiliados</h5>
                </div>
                <!-- Card Body -->
                <div class="card-body p-0">
                  <div class="table-responsive" style="overflow:scroll; height: 280px">
                    <?php if (@$afiliacao) : ?>

                      <script>
                        var dados = [];
                      </script>

                      <table class="table no-wrap p-table">
                        <thead class="bg-info">
                          <tr class="border-0">
                            <th class="border-0 center">Afiliado</th>
                            <th class="border-0 center">Vendas</th>
                            <th class="border-0 center">Faturamento</th>
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
                          $maior = 0;

                          foreach ($afiliacao as $afiliacao) :
                            if ($i > 10)
                              break;

                            if (@$_POST["inicio"] && $_POST["fim"])
                              $filtros = "data_finalizada BETWEEN '" . $_POST["inicio"] . " 00:00:00' AND '" . $_POST["fim"] . " 23:59:59' AND ";

                          ?>

                            <?php
                            // if ($vendapaga > $maior) {
                            //   $maior = $vendapaga;

                            $nomeAfiliado = $afiliacao->dataAfiliado()->nome;
                            $fotoAfiliado = $afiliacao->dataAfiliado()->foto;
                            // }

                            ?>
                            <tr class="cli" id="afiliacao<?= $afiliacao->afiliado; ?>">
                              <td style="text-align:center"><?= $afiliacao->dataAfiliado()->nome ?></td>
                              <td style="text-align:center"><?= $afiliacao->total; ?></td>
                              <td style="text-align:center"><?= "R$ " . number_format($afiliacao->soma, 2, ",", "."); ?></td>
                            </tr>

                          <?php $i++;
                          endforeach; ?>
                        </tbody>
                      </table>

                    <?php else : ?>
                      <p>Você ainda não tem afiliados para esse produto</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Conversão Meios de pagamentos -->
            <div class="col-xl-4 col-lg-5 center">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                  <h5 class="m-0 font-weight-bold text-primary">Meios de Pagamento</h5>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                  <div class="">
                    <canvas id="chartMeiosPagamento" style="height: 240px; width: 100%;"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- FIM LINHA 4 -->

          <!-- LINHA 5 -->

          <div class="row">

            <?php

            $teste = 2;

            $diasemana = array("Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sabado");

            $data = date('Y-m-d');

            // $diasemana_numero = date('w', strtotime($data));

            ?>

            <div class="col-xl-4 col-lg-5 center">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                  <h5 class="m-0 font-weight-bold text-primary">Vendas x Dias da Semana</h5>
                  <?php
                  $i;
                  if ($vendasPeriodo) {
                    // var_dump($vendasPeriodo);
                    $estados = array();
                    foreach ($vendasPeriodo as $vendaP) {


                      if (isset($estados[$vendaP->dataComprador()->estado]))
                        @$estados[$vendaP->dataComprador()->estado] += 1;
                      else
                        $estados[$vendaP->dataComprador()->estado] = 1;

                      $diasemana_numero = date('w', strtotime($vendaP->data_finalizada));
                      if ($diasemana[$diasemana_numero] == "Domingo") {
                        @$domingoValor += (float) $vendaP->valor_redrocket;
                        @$domingoCount++;
                      }

                      if ($diasemana[$diasemana_numero] == "Segunda-feira") {
                        @$segundaValor += (float) $vendaP->valor_redrocket;
                        @$segundaCount++;
                      }

                      if ($diasemana[$diasemana_numero] == "Terça-feira") {
                        @$tercaValor += (float) $vendaP->valor_redrocket;
                        @$tercaCount++;
                      }

                      if ($diasemana[$diasemana_numero] == "Quarta-feira") {
                        @$quartaValor += (float) $vendaP->valor_redrocket;
                        @$quartaCount++;
                      }

                      if ($diasemana[$diasemana_numero] == "Quinta-feira") {
                        @$quintaValor += (float) $vendaP->valor_redrocket;
                        @$quintaCount++;
                      }

                      if ($diasemana[$diasemana_numero] == "Sexta-feira") {
                        @$sextaValor += (float) $vendaP->valor_redrocket;
                        @$sextaCount++;
                      }

                      if ($diasemana[$diasemana_numero] == "Sabado") {
                        @$sabadoValor += (float) $vendaP->valor_redrocket;
                        @$sabadoCount++;
                      }
                      @$soma += $vendaP->valor_redrocket;
                      $i++;
                    }
                    // var_dump($estados);
                  }

                  ?>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">

                    <table class="table no-wrap p-table">
                      <thead class="bg-info">
                        <tr class="border-0">
                          <th class="border-0 center">Dias</th>
                          <th class="border-0 center">Vendas</th>
                          <th class="border-0 center">Faturamento</th>
                        </tr>
                      </thead>

                      <tbody>

                        <tr class="cli" id="afiliacao<?= $afiliacao->afiliado; ?>">
                        <tr>

                          <td style="text-align:center">Domingo</td>
                          <td style="text-align:center"><?= @$domingoCount; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format(@$domingoValor, 2, ",", "."); ?></td>
                        </tr>
                        <tr>
                          <td style="text-align:center">Segunda</td>
                          <td style="text-align:center"><?= @$segundaCount; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format(@$segundaValor, 2, ",", "."); ?></td>
                        </tr>
                        <tr>
                          <td style="text-align:center">Terça</td>
                          <td style="text-align:center"><?= @$tercaCount; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format(@$tercaValor, 2, ",", "."); ?></td>
                        </tr>
                        <tr>
                          <td style="text-align:center">Quarta</td>
                          <td style="text-align:center"><?= @$quartaCount; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format(@$quartaValor, 2, ",", "."); ?></td>
                        </tr>
                        <tr>
                          <td style="text-align:center">Quinta</td>
                          <td style="text-align:center"><?= @$quintaCount; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format(@$quintaValor, 2, ",", "."); ?></td>
                        </tr>
                        <tr>
                          <td style="text-align:center">Sexta</td>
                          <td style="text-align:center"><?= @$sextaCount; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format(@$sextaValor, 2, ",", "."); ?></td>
                        </tr>
                        <tr>
                          <td style="text-align:center">Sábado</td>
                          <td style="text-align:center"><?= @$sabadoCount; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format(@$sabadoValor, 2, ",", "."); ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- Saques e comissões clientes e redrocket -->
              <div class="card shadow mb-4 center">
                <div class="card-header py-3">
                  <h5 class="m-0 font-weight-bold text-primary">Saques e Comissões</h5>
                  <hr>
                  <div class="row justify-content-center card-active">
                    <div class="col-6">
                      <h5 class="text-center m-b-10" style="color:red"><span class="text-muted m-r-5">Total Saques Clientes: <br></span>
                        R$ <?= number_format((-1) * @$saqueMes, 2, ",", ".") ?></h5>
                    </div>
                  </div>
                  <hr>
                  <div class="row justify-content-center card-active">
                    <div class="col-6">
                      <h5 class="text-center m-b-10" style="color:green"><span class="text-muted m-r-5"">Total Comissões RedRocket: <br></span>
                      <?php $octa = ($juros - $wirecard) + $redrocket; ?>
                      R$ <?= number_format($octa, 2, ",", ".") ?></h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            <div class=" col-xl-4 col-lg-5 center">
                          <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3">
                              <h5 class="m-0 font-weight-bold text-primary">Vendas x Horario</h5>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                              <div class="">
                                <canvas id="chartHorario" style="height: 240px; width: 100%;"></canvas>
                              </div>
                            </div>
                          </div>

                          <!-- Conversão por Estado -->
                          <div class="card shadow mb-4">
                            <div class="card-header py-3">
                              <h5 class="m-0 font-weight-bold text-primary">Vendas x Estados</h5>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                              <div class="">
                                <canvas id="chartEstados" style="height: 240px; width: 100%;"></canvas>
                              </div>
                            </div>
                          </div>
                    </div>

                    <div class="col-xl-4 col-lg-5 center">
                      <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3">
                          <h5 class="m-0 font-weight-bold text-primary">Planos Mais Vendido</h5>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                          <div class="table-responsive">
                            <?php if ($planoMaior) :
                              $i = 1;
                              foreach ($planoMaior as $planoMaior) :
                                if ($i <= 5) :
                                  if ($planoMaior->plano) :
                            ?>

                                    <b><?= @$planos->findById($planoMaior->plano)->nome . " - $planoMaior->total vendas" ?></b><br>
                            <?php
                                  endif;
                                endif;
                                $i++;
                              endforeach;
                            endif; ?>
                          </div>
                        </div>
                      </div>

                      <!-- Card Header - Dropdown -->
                      <div class="card shadow mb-4 center">
                        <div class="card-header py-3">
                          <h5 class="m-0 font-weight-bold text-primary">Tiket Médio Vendas</h5>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                          <div class="table-responsive">
                            <?php $totalPagos = $dados["cartoesPagos"] + $dados["boletosPagos"] + @$dados["pixPagos"]; ?>
                            <?php if ($totalPagos > 0) : ?>
                              <h3 class="mb-1"><?= "R$ " . number_format((($dados["valorCartaoPago"] + $dados["valorBoletoPago"] + @$dados["valorPixPago"]) / ($totalPagos)), 2, ",", ".") ?></h3>
                            <?php endif ?>
                          </div>
                        </div>
                      </div>


                      <!-- Vendas Diretas x Afiliados -->
                      <div class="card shadow mb-4 center">
                        <div class="card-header py-3">
                          <h5 class="m-0 font-weight-bold text-primary">Vendas - Diretas x Afiliados</h5>
                          <hr>
                          <div class="row align-items-center justify-content-center card-active">
                            <div class="col-6">
                              <h6 class="text-center m-b-10"><span class="text-muted m-r-5">Diretas: </span>
                                R$ <?= number_format($dados["valorVendasDiretas"], 2, ",", ".") ?></h6>
                            </div>
                            <div class="col-6">
                              <h6 class="text-center  m-b-10"><span class="text-muted m-r-5">Afiliados: </span>
                                R$ <?= number_format($dados["valorVendasAfiliados"], 2, ",", ".") ?>
                                <?php
                                $analitico[13] = array("totalVendas", $dados["vendasDiretas"], $dados["vendasAfiliados"], $p)
                                ?></h6>
                            </div>
                          </div>
                          <hr>
                          <div class="row align-items-center justify-content-center card-active">
                            <div class="col-6">
                              <h6 class="text-center m-b-10"><span class="text-muted m-r-5">Diretas: </span>
                                <?php
                                if (($dados["cartoesPagos"] + $dados["boletosPagos"] + @$dados["pixPagos"]) > 0)
                                  $p = $dados["vendasDiretas"] / ($dados["cartoesPagos"] + $dados["boletosPagos"] + @$dados["pixPagos"]) * 100;
                                else
                                  $p = "0";
                                echo number_format($p, 2, ",", ".");
                                ?>%</h6>
                              <div class="progress">
                                <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                            <div class="col-6">
                              <h6 class="text-center  m-b-10"><span class="text-muted m-r-5">Afiliados: </span>
                                <?php
                                if (($dados["cartoesPagos"] + $dados["boletosPagos"] + @$dados["pixPagos"]) > 0)
                                  $p = $dados["vendasAfiliados"] / ($dados["cartoesPagos"] + $dados["boletosPagos"] + @$dados["pixPagos"]) * 100;
                                else
                                  $p = "0";
                                echo number_format($p, 2, ",", ".");
                                ?>%</h6>
                              <div class="progress">
                                <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- FIM LINHA 5 -->
            </div>
          </div>
        </div>

        <?php

        if (@$_POST["produto"]) {
          $tabela  = "<table border='1' class='table table-striped'>";

          $tabela .= "<thead>";
          $tabela .= "<tr>";
          $tabela .= "<th>Produto</th>";
          $tabela .= "<th>In&iacute;cio</th>";
          $tabela .= "<th>Fim</th>";
          $tabela .= "</tr>";
          $tabela .= "</thead>";

          $produto = $produtos->findById($_POST["produto"]);

          $tabela .= "<tr>";
          $tabela .= "<td>$produto->nome</td>";
          $tabela .= "<td>" . formataData($_POST["inicio"]) . "</td>";
          $tabela .= "<td>" . formataData($_POST["fim"]) . "</td>";
          $tabela .= "</tr>";

          $tabela .= "</table>";

          $tabela2  = "<table border='1' class='table table-striped'>";

          $tabela2 .= "<thead>";
          $tabela2 .= "<tr>";
          $tabela2 .= "<th>Dia</th>";
          $tabela2 .= "<th>Valor</th>";
          $tabela2 .= "</tr>";
          $tabela2 .= "</thead>";

          if (@$diaCom) {
            foreach ($diaCom as $dado) {
              $tabela2 .= "<tr>";
              $tabela2 .= "<td>" . formataData($dado[0]) . "</td>";
              $tabela2 .= "<td>R$ " . number_format($dado[1], 2, ",", ".") . "</td>";
              $tabela2 .= "</tr>";
            }
          }

          $tabela2 .= "</table>";

          ####
          $tabela3  = "<table border='1' class='table table-striped'>";

          $tabela3 .= "<thead>";
          $tabela3 .= "<tr>";
          $tabela3 .= "<th>Descri&ccedil;&atilde;o</th>";
          $tabela3 .= "<th>Quantidade</th>";
          $tabela3 .= "<th>Valor</th>";
          $tabela3 .= "<th>%</th>";
          $tabela3 .= "</tr>";
          $tabela3 .= "</thead>";

          foreach ($analitico as $analitico) {
            $tabela3 .= "<tr>";
            $tabela3 .= "<td>" . $analitico[0] . "</td>";
            $tabela3 .= "<td>" . $analitico[1] . "</td>";
            $tabela3 .= "<td>R$ " . number_format($analitico[2], 2, ",", ".") . "</td>";
            $tabela3 .= "<td>" . number_format($analitico[3], 2, ",", ".") . "</td>";
            $tabela3 .= "</tr>";
          }

          $tabela3 .= "</table>";

          //echo $tabela . $tabela2 . $tabela3;
          $_SESSION["tabelaProduto"] = $tabela;
          $_SESSION["tabelaDias"] = $tabela2;
          $_SESSION["tabelaAnalitico"] = $tabela3;

          //echo $tabela3;
        }
        ?>
      </div>

      <!-- FIM TOTAL DE VENDAS -->
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

  $(function() {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    // GRÁFICO DE VENDAS
    var canvasVendas = $('#areaChart').get(0).getContext('2d');

    var dadosVendas = {
      labels: [<?php foreach ($dias as $dado) echo "'" . formataData($dado) . "'," ?>],
      datasets: [{
        label: 'Atual',
        backgroundColor: 'rgba(163,249,160,0.5)',
        borderColor: 'rgba(60,179,113, 0.4)',
        pointRadius: true,
        pointBorderWidth: '3',
        pointBorderColor: '#3b8bba',
        pointColor: '#3b8bba',
        pointStyle: 'circle',
        pointStrokeColor: 'rgba(60,179,113, 0.4)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,179,113, 0.4)',
        data: [<?php foreach ($comAtual as $atual) echo $atual . "," ?>]
      }, ]
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
            display: true,
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

    // GRÁFICO ESTADOS
    var canvasEstados = $('#chartEstados').get(0).getContext('2d');

    var dadosEstados = {
      labels: [<?php if ($estados) {
                  foreach ($estados as $uf => $qtd) echo "'$uf',";
                } ?>],
      datasets: [{
        label: 'Atual',
        backgroundColor: '#8BC24A',
        borderColor: 'rgba(60,179,113, 0.4)',
        pointRadius: true,
        pointBorderWidth: '3',
        pointBorderColor: '#3b8bba',
        pointColor: '#3b8bba',
        pointStyle: 'circle',
        pointStrokeColor: 'rgba(60,179,113, 0.4)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,179,113, 0.4)',
        data: [<?php if ($estados) {
                  foreach ($estados as $uf1 => $qtd1) echo "$qtd1 ,";
                } ?>]
      }, ]
    }

    var opcoesEstados = {
      maintainAspectRatio: false,
      responsive: true,
    }

    var graficoEstados = new Chart(canvasEstados, {
      type: 'bar',
      data: dadosEstados,
      options: opcoesEstados
    });
    // FIM GRÁFICO ESTADOS

    // GRÁFICO MEIOS DE PAGAMENTO
    var canvasMeiosPagamentos = $('#chartMeiosPagamento').get(0).getContext('2d');

    var dadosMeiosPagamentos = {
      labels: [
        'Cartão',
        'Boleto',
        'Pix'
      ],
      datasets: [{
        data: [<?= @$dados["cartoesPagos"] ?>, <?= @$dados["boletosPagos"] ?>, <?= @$dados["pixPagos"] ?>],
        backgroundColor: ['#0066CC', '#00CC66', '#0FF9CE'],
      }]
    }

    var opcoesMeiosPagamentos = {
      maintainAspectRatio: false,
      responsive: true,
    }

    var graficoMeiosPagamentos = new Chart(canvasMeiosPagamentos, {
      type: 'doughnut',
      data: dadosMeiosPagamentos,
      options: opcoesMeiosPagamentos
    })
    // FIM GRÁFICO MEIOS DE PAGAMENTO

    // GRÁFICO HORÁRIOS
    <?php if ($vendas) :
      $i = 0;

      // echo $busca;
      for ($i; $i < 24; $i++) {
        $j = $i < 10 ? "0" . $i : $i;
        $hora[$i] = $j;

        $vendaH = $vendas->find(@$busca . "(status_transacao = 4 AND created_at LIKE '%" . $j . ":%:%')")->count();
        $cont[$i] = $vendaH;
        @$total += $vendaH;
      }
    // echo $total;
    endif ?>

    var canvasHorario = $('#chartHorario').get(0).getContext('2d');

    var dadosHorario = {
      labels: [<?php foreach ($hora as $dado) echo "'$dado'," ?>],
      datasets: [{
        label: 'Atual',
        backgroundColor: '#BFEFFF',
        borderColor: 'rgb(53,152,219)',
        pointRadius: true,
        pointBorderWidth: '3',
        pointBorderColor: '#3598DB',
        pointColor: '#3598DB',
        pointStyle: 'circle',
        pointStrokeColor: 'rgba(60,179,113, 0.4)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,179,113, 0.4)',
        data: [<?php foreach ($cont as $atual) echo $atual . "," ?>]
      }, ]
    }

    var opcoesHorario = {
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
            display: true,
          }
        }]
      }
    }

    var graficoHorario = new Chart(canvasHorario, {
      type: 'line',
      data: dadosHorario,
      options: opcoesHorario
    });
    // FIM GRÁFICO HORÁRIOS
  })
</script>

<?= $v->end(); ?>
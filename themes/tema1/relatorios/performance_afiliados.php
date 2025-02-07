

<?php
$v->layout("_theme");

if (@$section->getTipo() == 4) {
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

    <?= $section->getTipoSecaoLayout(); ?>

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

        //var_dump($dados);
        if ($vendasD) {

          foreach ($vendasD as $venda) {
            $dia = $venda->dia;
            //echo "$dia ";

            /** CONJUNTO DA COMISSÃO DO MES ATUAL */
            $valorDia[$dia] = $venda->valor;

            /** CONJUNTO DOS DIAS DO MES ATUAL */
            $diaComissao[$dia] = $dia;
            //echo "Dia comissao $dia {$valorDia[$dia]} <br>";
          }

          foreach ($dias as $dia1) {
            //echo "Dia $dias  - dia comissao {$diaComissao[$dias]} <br>";

            /** VERIFICA SE O DIA EXISTE NO BANCO DE DADOS E ATRIBUI A COMISSÃO PARA O DIA CORRESPONDENTE */
            if (@$diaComissao[$dia1]) {
              //echo "dia {$diaComissao[$dia1]} - $valorDia[$dia1] - existe <br>";
              $comAtual[$dia1] = $valorDia[$dia1];
              $diaCom[$dia1] = array(@$diaComissao[$dia1], $valorDia[$dia1]);
            } else {
              //echo "dia $dia1 não existe <br>";
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
        <smal>* Valores referentes a vendas pagas</smal>
      </div>
    </div>

    <!-- Dashbords - linha 1 -->

    <div class="row">
      <?php if (@$_POST["produto"]) : ?>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
          <div class="card border-3 border-top border-top-primary">
            <div class="card-body">
              <h5 class="m-0 font-weight-bold text-primary">Total Vendas</h5>
              <div class="metric-value d-inline-block">
                <h3><?= ($dados["cartoesPagos"] + $dados["boletosPagos"] + $dados["pixPagos"]) ?></h3>
              </div>
              <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                <!-- <span class="icon-circle-small icon-box-xs text-success bg-success-light"><i class="fa fa-fw fa-arrow-up"></i></span><span>5.86%</span> -->
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
          <div class="card border-3 border-top border-top-primary">
            <div class="card-body">
              <h5 class="m-0 font-weight-bold text-primary">Total Faturado</h5>
              <div class="metric-value d-inline-block">
                <h3><?= "R$ " . number_format(($dados["valorCartaoPago"] + $dados["valorBoletoPago"] + $dados["valorPixPago"]), 2, ",", ".") ?></h3>
              </div>
              <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                <!-- <span class="icon-circle-small icon-box-xs text-success bg-success-light"><i class="fa fa-fw fa-arrow-up"></i></span><span>5.86%</span> -->
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
          <div class="card border-3 border-top border-top-primary">
            <div class="card-body">
              <h5 class="m-0 font-weight-bold text-primary">Comissão Paga</h5>
              <div class="metric-value d-inline-block">
                <h3><?= "R$ " . number_format($somaVendas->soma, 2, ",", ".") ?></h3>
              </div>
              <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                <!-- <span class="icon-circle-small icon-box-xs text-success bg-success-light"><i class="fa fa-fw fa-arrow-up"></i></span><span>5.86%</span> -->
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-body">
            <h5 class="font-weight-bold text-primary">Destaque / Mês</h5>

            <div class="row">
              <div class="col-md-10">
                <h3 id="nomeAfiliado"></h3>
              </div>

              <div class="col-md-2" class="img-afiliado">
                <img id="fotoAfiliado" width="100%">
              </div>
            </div>

            <style>
              .img-afiliado img {
                width: 100%;
              }
            </style>

          </div>
        </div>
    </div>

    <!-- Dashbords - Fim linha 1 -->

    <!-- Dashbords - linha 2 -->

    <div class="row">
      <div class="col-xl-12 col-lg-5">
        <div class="card card-body shadow">
          <h4 class="m-0 font-weight-bold text-primary">
            Vendas
            <small><a href="<?= url("relatorios/exportar?tabela=vendas") ?>" class="badge badge-success" style="float=right" target="_blank">xls</a></small>
          </h4>

          <div class="">
            <canvas id="areaChart" style="height: 260px; width: 100%;"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashbords - Fim linha 2 -->

    <!-- Dashbords - linha 3 -->

    <div class="container-fluid">

      <!-- Page Heading -->
      <p class="mb-4">
      <div class="row">

        <div class="col-xl-4 col-lg-5">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3">
              <h5 class="m-0 font-weight-bold text-primary">Top 10 Afiliados</h5>
            </div>
            <!-- Card Body -->
            <div class="card-body p-0">
              <div class="table-responsive">
                <?php if (@$afiliacao) : ?>

                  <script>
                    var dados = [];
                  </script>

                  <table class="table no-wrap p-table">
                    <thead class="bg-light">
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

                        $afiliadosDados = $usuarioDados->find("usuario = $afiliacao->afiliado")->fetch();
                      ?>

                        <?php
                        if ($vendapaga > $maior) {
                          $maior = $vendapaga;

                          $nomeAfiliado = $afiliacao->dataAfiliado()->nome;
                          $fotoAfiliado = $afiliacao->dataAfiliado()->foto;
                        }

                        ?>
                        <tr class="cli" id="afiliacao<?= $afiliacao->afiliado; ?>">
                          <td style="text-align:center"><?= $afiliacao->dataAfiliado()->nome ?></td>
                          <td style="text-align:center"><?= $vendapaga; ?></td>
                          <td style="text-align:center"><?= "R$ " . number_format($valorpaga->valor, 2, ",", "."); ?></td>
                        </tr>

                      <?php $i++;
                      endforeach; ?>
                    </tbody>
                  </table>
              </div>
            <?php else : ?>
              <h4>Você ainda não tem afiliados para esse produto</h4>
            <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-xl-4 col-lg-5">
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


        <div class="col-xl-4 col-lg-5">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
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
      </div>

    </div>

    <!-- Dashbords - Fim linha 3 -->

    <!-- Dashbords - linha 4 -->

    <div class="container-fluid">

      <!-- Page Heading -->
      <p class="mb-4">
      <div class="row">

        <?php

        $teste = 2;

        $diasemana = array("Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sabado");

        $data = date('Y-m-d');

        $diasemana_numero = date('w', strtotime($data));

        ?>

        <div class="col-xl-4 col-lg-5">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3">
              <h5 class="m-0 font-weight-bold text-primary">Vendas x Dias da Semana</h5>
              <?php
              $i;
              if ($vendasPeriodo) {
                $estados = array();
                foreach ($vendasPeriodo as $vendaP) {


                  if (isset($estados[$vendaP->dataComprador()->estado]))
                    @$estados[$vendaP->dataComprador()->estado] += 1;
                  else
                    $estados[$vendaP->dataComprador()->estado] = 1;

                  $diasemana_numero = date('w', strtotime($vendaP->data_finalizada));
                  if ($diasemana[$diasemana_numero] == "Domingo") {
                    @$domingoValor += (float) $vendaP->valor_liquido;
                    @$domingoCount++;
                  }

                  if ($diasemana[$diasemana_numero] == "Segunda-feira") {
                    @$segundaValor += (float) $vendaP->valor_liquido;
                    @$segundaCount++;
                  }

                  if ($diasemana[$diasemana_numero] == "Terça-feira") {
                    @$tercaValor += (float) $vendaP->valor_liquido;
                    @$tercaCount++;
                  }

                  if ($diasemana[$diasemana_numero] == "Quarta-feira") {
                    @$quartaValor += (float) $vendaP->valor_liquido;
                    @$quartaCount++;
                  }

                  if ($diasemana[$diasemana_numero] == "Quinta-feira") {
                    @$quintaValor += (float) $vendaP->valor_liquido;
                    @$quintaCount++;
                  }

                  if ($diasemana[$diasemana_numero] == "Sexta-feira") {
                    @$sextaValor += (float) $vendaP->valor_liquido;
                    @$sextaCount++;
                  }

                  if ($diasemana[$diasemana_numero] == "Sabado") {
                    @$sabadoValor += (float) $vendaP->valor_liquido;
                    @$sabadoCount++;
                  }
                  $i++;
                }
                // var_dump($estados);
              }

              ?>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <?php if (@$afiliacao) : ?>

                  <script>
                    var dados = [];
                  </script>


                  <table class="table no-wrap p-table">
                    <thead class="bg-light">
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
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4 col-lg-5">
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
        </div>

        <div class="col-xl-4 col-lg-5">
          <div class="card shadow mb-4 center">
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
                    if ($i <= 3) :
                ?>

                      <b><?= @$planoMaior->plano ? $planos->findById($planoMaior->plano)->nome . " - $planoMaior->total vendas" : 0 ?></b><br>
                <?php
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
                  <h3 class="mb-1"><?= "R$ " . number_format(($dados["valorCartaoPago"] + $dados["valorBoletoPago"] + @$dados["valorPixPago"]) / $totalPagos, 2, ",", ".") ?></h3>
                <?php endif ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Dashbords - Fim linha 4 -->

        <!-- Dashbords - linha 5 -->

        <div class="row">

          <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
              <!-- Card Header - Dropdown -->
              <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Total Cartões</h5>
                <small>Total de vendas cartão geradas pelo afiliado</small>
              </div>
              <!-- Card Body -->
              <div class="card-body">
                <div class="">
                  <canvas id="chartCartoes" style="height: 240px; width: 100%;"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
              <!-- Card Header - Dropdown -->
              <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Total Boletos</h5>
                <small>Total de vendas boletos geradas pelo afiliado</small>
              </div>
              <!-- Card Body -->
              <div class="card-body">
                <div class="chartPie">
                  <canvas id="chartBoletos" style="height: 240px; width: 100%;"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
              <!-- Card Header - Dropdown -->
              <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Total Pix</h5>
                <small>Total de vendas pix geradas pelo afiliado</small>
              </div>
              <!-- Card Body -->
              <div class="card-body">
                <div class="chartPie">
                  <canvas id="chartPix" style="height: 240px; width: 100%;"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Dashbords - Fim linha 5 -->

          <!--?php endif ?-->
        </div>
      </div>

    <?php endif ?>

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

      if (@$analitico) {
        foreach ($analitico as $analitico) {
          $tabela3 .= "<tr>";
          $tabela3 .= "<td>" . $analitico[0] . "</td>";
          $tabela3 .= "<td>" . $analitico[1] . "</td>";
          $tabela3 .= "<td>R$ " . number_format($analitico[2], 2, ",", ".") . "</td>";
          $tabela3 .= "<td>" . number_format($analitico[3], 2, ",", ".") . "</td>";
          $tabela3 .= "</tr>";
        }
      }

      $tabela3 .= "</table>";

      //echo $tabela . $tabela2 . $tabela3;
      $_SESSION["tabelaProduto"] = $tabela;
      $_SESSION["tabelaDias"] = $tabela2;
      $_SESSION["tabelaAnalitico"] = $tabela3;

      //echo $tabela3;
    }

    // var_dump($dias);
    ?>
    </div>

    <!-- FIM TOTAL DE VENDAS -->
  </section>
</div>

<?= $v->start("scripts"); ?>
<script>
  $("#nomeAfiliado").html("<?= @$nomeAfiliado ?>");
  var foto = "<?= @$fotoAfiliado ?>";
  console.log(foto);
  if (foto)
    foto = foto;
  else
    foto = "user.png";
  var url = "<?= url("uploads/perfil/"); ?>" + foto;
  $("#fotoAfiliado").prop({
    "src": url
  });

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
      labels: [<?php foreach ($estados as $uf => $qtd) echo "'$uf'," ?>],
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
        data: [<?php foreach ($estados as $uf1 => $qtd1) echo "$qtd1 ," ?>]
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

        $vendaH = $vendas->find(@$busca . "(status_transacao = 4 AND afiliado IS NOT NULL AND created_at LIKE '%" . $j . ":%:%')")->count();
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

    // GRÁFICO CARTÕES
    var canvasCartoes = $('#chartCartoes').get(0).getContext('2d');

    var dadosCartoes = {
      labels: [
        'Pagos',
        'Recusados',
        'Chargeback',
        'Reembolsados'
      ],
      datasets: [{
        data: [<?= $dados["cartoesPagos"] ?>, <?= $dados["cartoesCancelados"] ?>, <?= $dados["chargeback"] ?>, <?= $dados["cartoesReembolsos"] ?>],
        backgroundColor: ['#069106', '#32AA8E', '#FF0000', '#f56954', '#980000'],
      }]
    }

    var opcoesCartoes = {
      maintainAspectRatio: false,
      responsive: true,
    }

    var graficoCartoes = new Chart(canvasCartoes, {
      type: 'pie',
      data: dadosCartoes,
      options: opcoesCartoes
    });
    // FIM GRÁFICO CARTÕES

    // GRÁFICO BOLETOS
    var canvasBoletos = $('#chartBoletos').get(0).getContext('2d');

    var dadosBoletos = {
      labels: [
        'Pagos',
        'Não Pagos',
        'Reembolsados',
        'Aguardando Pagamento'
      ],
      datasets: [{
        data: [<?= $dados["boletosPagos"] ?>, <?= $dados["boletosCancelados"] ?>, <?= $dados["boletosReembolsos"] ?>, <?= $dados["boletosOutros"] ?>],
        backgroundColor: ['#00a65a', '#f56954', '#980000', '#FFC222'],
      }]
    }

    var opcoesBoletos = {
      maintainAspectRatio: false,
      responsive: true,
    }

    var graficoBoletos = new Chart(canvasBoletos, {
      type: 'pie',
      data: dadosBoletos,
      options: opcoesBoletos
    });
    // FIM GRÁFICO BOLETOS

    // GRÁFICO PIX
    var canvasPix = $('#chartPix').get(0).getContext('2d');

    var dadosPix = {
      labels: [
        'Pagos',
        'Recusados',
        'Reembolsados',
        'Aguardando Pagamento'
      ],
      datasets: [{
        data: [<?= $dados["pixPagos"] ?>, <?= $dados["pixCancelados"] ?>, <?= $dados["cartoesReembolsos"] ?>, <?= $dados["pixOutros"] ?>],
        backgroundColor: ['#069106', '#FF0000', '#980000', '#FFC222'],
      }]
    }

    var opcoesPix = {
      maintainAspectRatio: false,
      responsive: true,
    }

    var graficoPix = new Chart(canvasPix, {
      type: 'pie',
      data: dadosPix,
      options: opcoesPix
    });
    // FIM GRÁFICO PIX

  })
</script>

<?= $v->end(); ?>
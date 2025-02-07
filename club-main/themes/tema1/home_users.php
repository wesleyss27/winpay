<?php 
  $v->layout("_theme");
  if($_SESSION["tipo"] == 6){
    header("Location: " . url("vendas"));
  } 
?>

  <div class="content-wrapper">
<br/>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      
	   <div class="row">
          <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-primary" style="background:url(<?= url('themes/tema1/images/octa.png') ?>); background-size:cover; background-repeat:no-repeat; background-position:left">
              <div class="inner">
                <?php if(@$hoje): ?>
                  <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$hoje, 2, ",", "."); ?></h3>
                <?php else: ?>
                  <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px">R$ 0,00</h3>
                <?php endif; ?>

                <p style="font-family:arial; text-shadow: 1px 1px 2px #333; font-size:23px">Hoje</p>
              </div>
   <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>


            
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <?php if(@$ontem): ?>
                  <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$ontem, 2, ",", "."); ?></h3>
                <?php else: ?>
                  <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px">R$ 0,00</h3>
                <?php endif; ?>

                  <p style="font-family:arial; text-shadow: 1px 1px 2px #333; font-size:23px">Ontem</p>
              </div>
              <div class="icon">
               <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
            
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <?php if($mes): ?>
                  <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format($mes, 2, ",", "."); ?></h3>
                <?php else: ?>
                  <h3 style="font-family:inter; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px">R$ 0,00</h3>
                <?php endif; ?>

                 <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:23px">Esse mês</p>
              </div>
              <div class="icon">
                 <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
             
            </div>
          </div>
          <!-- ./col -->

          <!-- ./col -->
        </div>
		
		
        <!-- /.row -->
		
		
			   <div class="row">
          <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                
                <?php if(@$saldo->valor > 0): ?>
                  <h3 style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$saldo->valor, 2, ",", "."); ?></h3>
                <?php else: ?>
                  <h3 style="font-family:inter; text-shadow: 1px 1px 2px #333; font-size:25px; color:#fff;">R$ 0,00</h3>
                <?php endif; ?>
                  <p style="font-family:arial; color:#fff; text-shadow: 1px 1px 2px #333; font-size:23px">Disponível</p>
              </div>
                 <div class="icon">
                <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
             <a href="<?= url("comissao") ?>" class="small-box-footer" style="font-family:inter">Saque <i class="fas fa-arrow-circle-right"></i></a>
			
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-default" style="background:background: #F2994A;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #F2C94C, #F2994A);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #F2C94C, #F2994A); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
              <div class="inner">
                  <?php if($pendente): ?>
                    <h3 style="font-family:arial; color: #fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format($pendente, 2, ",", "."); ?></h3>
                  <?php else: ?>
                    <h3 style="font-family:inter; color: #fff; text-shadow: 1px 1px 2px #333; font-size:25px;">R$ 0,00</h3>
                  <?php endif; ?>
                  <p style="font-family:arial; color: #fff; text-shadow: 1px 1px 2px #333; font-size:23px">Pendente</p>
              </div>
              <div class="icon">
                 <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
              <a href="<?= url("antecipacao") ?>" class="small-box-footer" style="font-family:inter">Antecipar <i class="fas fa-arrow-circle-right"></i></a>
            
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-default" style="background: #28A745;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #28A745, #28A745);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #28A745, #28A745); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
              <div class="inner">

                <h3 style="font-family:inter; color: #fff; text-shadow: 1px 1px 2px #333; font-size:25px"><?= "R$ " . number_format(@$saldo->valor + @$pendente, 2, ",", "."); ?></h3>
                <p style="font-family:arial; color: #fff; text-shadow: 1px 1px 2px #333; font-size:23px; font-weight:lighter">Total</p>

              </div>
			  
			    <div class="icon">
                 <i class="fa fa-dollar-sign" style="color:#fff; text-shadow: 1px 1px 2px #333;"></i>
              </div>
              <a href="<?= url("comissao") ?>" class="small-box-footer" style="font-family:inter">Saldos <i class="fas fa-arrow-circle-right"></i></a>
             
             
            </div>
          </div>
          <!-- ./col -->

          <!-- ./col -->
        </div>
        <!-- /.row -->

   
   <div class="row">
   
     <div class="col-md-6" >
      <div class="card" style="background:url('<?= url('themes/tema1/images/diferencial.jpg') ?>');  background-size:cover; background-repeat:no-repeat; height:150px; background-position:left">
        
        <!-- /.card-header -->
        <div class="card-body" style="background:none; margin-top:-15px">
          <blockquote style="background:none">
            <p style="font-size:27px; color:#fff; font-weight:lighter; font-family:lobster">Indicação Premiada!</p>
            <small style="font-family:inter; color:#fff; font-size:17px">Divulgue a <b>redrocket</b> e Ganhe Comissão.</small>
          </blockquote>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </div>
    <!-- ./col -->
   
         
    <div class="col-md-6">
            <div class="card card-default" style="height:150px; ">
              <div class="card-header">
                <h3 class="card-title" style="font-family:inter">
                  <i class="fas fa-bullhorn"></i>
                Seu link de Indicação Premiada:
                </h3>
              </div>
		 
		  <!-- /.card-header -->
              <div class="card-body" >
                
          
                  <p style="font-family:inter"><a href="<?= url("up/".$_SESSION["id"]) ?>" target="_blank"><?= url("up/".$_SESSION["id"]) ?></a><br/>
Usuários cadastrados com seu link: <b>0</b></p>
             
               
              </div>
			  
              <!-- /.card-body -->
			  </div>
			  </div>

 <div class="col-lg-8 col-12" >

    <div class="card-header">
      <h3 style="font-family:inter" class="card-title">Suas comissões - mês atual e anterior</h3>

      <div class="card-tools">
        Atual: <i class="fa fa-chart-bar" style="color:green"></i> | Anterior: <i class="fa fa-chart-bar" style="color:blue"></i>
      </div>
    </div>
           

    <div class="card-body">
      <small style="text-align: center">Clique em uma opção para ocultar</small>
      <div class="chart">
        <canvas id="areaChart" style="height: 180px; width: 100%;"></canvas>
      </div>

      <br>

      <h5>Suas Vendas</h5>
      <div class="row">
        <div class="col-md-4" style="text-align: center; padding: 5px; background: #eee; border: 1px solid #bbb">
          Vendas do mês <br>
          <?= $vendasAtual ?>
        </div>

        <div class="col-md-4" style="text-align: center; padding: 5px; background: #eee; border: 1px solid #bbb">
          Total faturado de vendas <br>
          <?php 
            if($_SESSION["tipo"] != 5){
              $soma = $vendasAtualSoma->soma;
            } else {
              $soma = $mes;
            }
          ?>
          <?= "R$ " . number_format($soma, 2, ",", "."); ?>
        </div>

        <div class="col-md-4" style="text-align: center; padding: 5px; background: #eee; border: 1px solid #bbb">
        <?php if($_SESSION["tipo"] != 5): ?>
          Ticket médio <br>
        <?php else: ?>
          Comissão média <br>
        <?php endif ?>

          <?php if($vendasAtual): ?>
          <?= "R$ " . number_format($soma / $vendasAtual, 2, ",", ".") ?>
          <?php else: ?>
            R$ 0,00
          <?php endif ?>

        </div>
      </div>
      <br>

      <?php if($_SESSION["tipo"] == 1): ?>
      <h5>Geral redrocket <small>(vendas pagas)</small></h5>

      <div class="row">
        <div class="col-md-4" style="text-align: center; padding: 5px; background: #eee; border: 1px solid #bbb">
          Vendas do mês <br>
          <?= $vendasTotal ?>
        </div>

        <div class="col-md-4" style="text-align: center; padding: 5px; background: #eee; border: 1px solid #bbb">
          Total vendas <br>
          <?php $soma = $vendasTotalSoma->soma; ?>
          <?= "R$ " . number_format($soma, 2, ",", "."); ?>
        </div>

        <div class="col-md-4" style="text-align: center; padding: 5px; background: #eee; border: 1px solid #bbb">

          Ticket médio <br>

          <?php if($vendasTotal): ?>
          <?= "R$ " . number_format($soma / $vendasTotal, 2, ",", ".") ?>
          <?php else: ?>
            R$ 0,00
          <?php endif ?>

        </div>
      </div>
      <?php endif ?>

    </div>
    <!-- /.card-body -->
      
      <!-- /.card -->

    

 </div>
		  
		<!-- BANNERS VENDA -->
    <div class="col-lg-4 col-12" >
      <!-- small box -->
      <div class="small-box bg-default" style="background:#f8f8f8;">
        <div class="inner">
          <a href="https://st4rt.com.br?src=redrocket" target="_blanc">
            <img src="<?= url("uploads/banners-home/pub-st4rt.gif?" . time()) ?>" alt="" width="100%">
          </a>
        </div>
      </div>
			
			 <!--div class="small-box bg-default" style="background:#f8f8f8; height:250px">
          <div class="inner">
            <h6 style="font-family:inter; font-size:12px; color:#3399cc"><i class="fa fa-info-circle" style="color:#3399cc"></i> PUBLICIDADE</h6> 
          </div>
        </div-->
			
    </div>
    <!-- ./col -->

    <!-- ./col -->
  </div>
        <!-- /.row -->
       
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- footer -->

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
        for($j = 1; $j <= 31; $j++){
          $dias[$j] = $j;
          //echo "Dia $j <br>";
        }

        if ($comissaoAtual) {

          foreach ($comissaoAtual as $comissaoAtual) {
            $dia = date("d", strtotime($comissaoAtual->dia));

            /** CONJUNTO DA COMISSÃO DO MES ATUAL */
            $valorDia[$dia] = $comissaoAtual->valor;

            /** CONJUNTO DOS DIAS DO MES ATUAL */
            $diaComissao[$dia] = $dia;
            //echo "Dia comissao $dia {$valorDia[$dia]} <br>";
          }

          foreach ($dias as $dia1) {
            //echo "Dia $dias  - dia comissao {$diaComissao[$dias]} <br>";

            if($dia1 < 10)
              $dia1 = "0" . $dia1;

            /** VERIFICA SE O DIA EXISTE NO BANCO DE DADOS E ATRIBUI A COMISSÃO PARA O DIA CORRESPONDENTE */
            if(@$diaComissao[$dia1]){
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

            if($dia1 < 10)
              $dia1 = "0" . $dia1;

            /** VERIFICA SE O DIA EXISTE NO BANCO DE DADOS E ATRIBUI A COMISSÃO PARA O DIA CORRESPONDENTE */
            if(@$diaComissaoAnterior[$dia2]){
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
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

    <?php
      $dados = array();
      for($i = 1; $i <= 31; $i++){
        $dados[$i] = $i;
      }
      ?>

    var areaChartData = {
      labels  : [<?php foreach($dados as $dado) echo $dado . "," ?> ],
      datasets: [
        {
          label               : 'Atual',
          backgroundColor     : 'rgba(163,249,160,0.5)',
          borderColor         : 'rgba(60,179,113, 0.4)',
          pointRadius         : true,
          pointBorderWidth    : '3',
          pointBorderColor    : '#3bab6a',
          pointColor          : '#3bab6a',
          pointStyle          : 'circle',
          pointStrokeColor    : 'rgba(60,179,113, 0.4)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,179,113, 0.4)',
          data                : [<?php foreach($comAtual as $atual) echo $atual . "," ?>]
        },
        {
          label               : 'Anterior',
          backgroundColor     : 'rgba(160, 197, 228,0.5)',
          borderColor         : 'rgba(76, 144, 203,0.7)',
          pointRadius         : true,
          pointBorderWidth    : '3',
          pointBorderColor    : 'rgb(80, 120, 255)',
          pointColor          : 'rgb(80, 120, 255)',
          pointStyle          : 'circle',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(0,0,139,0.7)',
          data                : [<?php foreach($comAnterior as $anterior) echo $anterior . "," ?>]
        },
      ]
    }

    var areaChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: true
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    var areaChart       = new Chart(areaChartCanvas, {
      type: 'line',
      data: areaChartData,
      options: areaChartOptions
    })

    //-------------
    //- LINE CHART -
    //--------------
    var lineChartCanvas = $('#lineChart').get(0).getContext('2d')
    var lineChartOptions = $.extend(true, {}, areaChartOptions)
    var lineChartData = $.extend(true, {}, areaChartData)
    lineChartData.datasets[0].fill = false;
    lineChartData.datasets[1].fill = false;
    lineChartOptions.datasetFill = false

    var lineChart = new Chart(lineChartCanvas, {
      type: 'line',
      data: lineChartData,
      options: lineChartOptions
    })

    //-------------
    //- DONUT CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData        = {
      labels: [
          'Chrome',
          'IE',
          'FireFox',
          'Safari',
          'Opera',
          'Navigator',
      ],
      datasets: [
        {
          data: [700,500,400,600,300,100],
          backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
        }
      ]
    }
    var donutOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    var donutChart = new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
    })

    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieData        = donutData;
    var pieOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    var pieChart = new Chart(pieChartCanvas, {
      type: 'pie',
      data: pieData,
      options: pieOptions
    })

    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartData = $.extend(true, {}, areaChartData)
    var temp0 = areaChartData.datasets[0]
    var temp1 = areaChartData.datasets[1]
    barChartData.datasets[0] = temp1
    barChartData.datasets[1] = temp0

    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : true
    }

    var barChart = new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })

    //---------------------
    //- STACKED BAR CHART -
    //---------------------
    var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
    var stackedBarChartData = $.extend(true, {}, barChartData)

    var stackedBarChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      scales: {
        xAxes: [{
          stacked: true,
        }],
        yAxes: [{
          stacked: true
        }]
      }
    }

    var stackedBarChart = new Chart(stackedBarChartCanvas, {
      type: 'bar',
      data: stackedBarChartData,
      options: stackedBarChartOptions
    })
  })
</script>

<?= $v->end(); ?>
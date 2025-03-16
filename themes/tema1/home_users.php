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
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
          swal({
            title: "Atenção!",
            text: "Você precisa cadastrar ou atualizar seus dados bancários para poder transacionar.",
            icon: "warning",
            buttons: {
              cancel: {
                text: "Cancelar",
                visible: true,
                className: "btn-cancel"
              },
              confirm: {
                text: "Ok",
                className: "btn-confirm"
              }
            },
            dangerMode: true,
          })
          .then((confirmado) => {
            if (confirmado) {
              window.location.href = "<?= url('banco') ?>";
            }
          });
        </script>

        <style>
          /* Personalização da caixa de diálogo */
          .swal-modal {
            background: rgb(155, 135, 0); !important; /* Fundo amarelo para chamar atenção */
          }

          /* Torna todo o texto branco */
          .swal-title, .swal-text {
            color: black !important;
          }

          /* Personalização dos botões */
          .swal-button {
            background-color: black; !important; /* Botão branco */
            color: white !important; /* Texto escuro */
            border-radius: 5px;
            font-weight: bold;
          }

          /* Estilo do botão ao passar o mouse */
          .swal-button:hover {
            background-color: #ddd !important;
            color: rgb(30, 36, 51) !important;
          }

          /* Estilo específico para o botão de Cancelar */
          .btn-cancel {
            background-color: transparent !important;
            color: black !important;
            border: 1px solid black !important;
          }

          .btn-cancel:hover {
            background-color: black !important;
            color: white !important;
          }
        </style>


      <?php } ?>

      <?= $section->getTipoSecaoLayout() ?>

      <?php if (($section->getTipoSecao() == "subusuario" && @$permissao->ver == 1) || $section->getTipoSecao() == "usuario") : ?>

        <!-- <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
          <small><i style="color: green; font-size: 30px">•</i> <?= $usuariosOnline ?> usuários online agora <br></small>
        <?php endif ?>

        <small>*Dados referentes a sua comissão</small> -->

        <div class="row">
          <div class="col-md-8"></div>
          <div class="col-md-4 text-right">
            <div class="btn-group">
              <div class="dropdown">
                <button class="btn btn-winpay dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Personalizado
                </button>

                <div class="dropdown-menu dropdown-winpay" aria-labelledby="dropdownMenuButton">
                  <div class="p-3">
                    <div class="row">
                      <div class="col-6">
                        <label for="inicio">Início</label>
                        <input type="date" id="inicio" class="form-control winpay-input">
                      </div>
                      <div class="col-6">
                        <label for="fim">Fim</label>
                        <input type="date" id="fim" class="form-control winpay-input">
                      </div>
                    </div>
                    <div class="text-center mt-3">
                      <button class="btn btn-winpay-sm" onclick="busca('personalizado')">Filtrar</button>
                    </div>
                  </div>
                </div>
              </div>

              <button class="btn btn-winpay" onclick="busca('hoje')">Hoje</button>
              <button class="btn btn-winpay" onclick="busca('ontem')">Ontem</button>
              <button class="btn btn-winpay" onclick="busca('semana')">7D</button>
              <button class="btn btn-winpay" onclick="busca('mes')">30D</button>
            </div>
          </div>
        </div>

        <!-- Estilos personalizados -->
        <style>
          /* Botões modernos */
          .btn-winpay {
            background: black;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: all 0.3s ease-in-out;
          }

          .btn-winpay:hover {
            background: black;
            color: white;
            border-color: rgb(217, 192, 0);
          }

          .btn-winpay-sm {
            background: rgb(217, 192, 0);
            color: #fff;
            border: none;
            padding: 6px 16px;
            font-size: 0.85rem;
            border-radius: 5px;
          }

          .btn-winpay-sm:hover {
            background: rgb(155, 135, 0);
          }

          /* Dropdown com largura aumentada */
          .dropdown-winpay {
            width: 350px !important; /* Aumentei a largura */
            background: rgb(40, 46, 61);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
          }

          /* Input de data */
          .winpay-input {
            background: rgb(25, 30, 45);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            width: 100%;
          }

          .winpay-input:focus {
            border-color: rgb(217, 192, 0);
            outline: none;
          }
        </style>


        <br>

        <div id="dados_render">
          <!-- < ?php $v->insert("home_users_dados") ?> -->
        </div>

        <!-- GRÁFICO -->
        <div class="row">

          <!-- GRÁFICO -->
          <div class="col-lg-12 col-12">
            <div class="winpay-card p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="card-title">
                  <i class="fas fa-chart-area me-1"></i> Suas comissões - mês atual e anterior
                </h3>
              </div>

              <!-- Pequena legenda opcional -->
              <small class="text-center d-block mb-2">Clique em uma opção para ocultar</small>

              <!-- Área do gráfico -->
              <div class="chart-container">
                <canvas id="areaChart"></canvas>
              </div>
            </div>

            <!-- Estilos personalizados -->
            <style>
              .winpay-card {
                background: rgb(30, 36, 51); /* Fundo escuro */
                border-radius: 10px; /* Cantos arredondados */
                color: #ffffff; /* Texto branco */
                border: 1px solid rgba(255, 255, 255, 0.1); /* Borda sutil */
                transition: all 0.2s ease-in-out;
                padding: 20px;
              }

              .winpay-card:hover {
                border-color: rgb(217, 192, 0);
                transform: scale(1.02);
              }

              /* Título */
              .card-title {
                font-size: 1rem;
                font-weight: bold;
                color: #A0AEC0;
              }

              /* Ícone */
              .fa-chart-area {
                color: rgb(217, 192, 0);
              }

              /* Gráfico */
              .chart-container {
                width: 100%;
                height: 200px;
                position: relative;
              }
            </style>
            <!-- /.card -->
              <br>
            <div class="winpay-card p-4">
              <h5 class="mb-3"><i class="fas fa-chart-line"></i> Suas Vendas</h5>

              <!-- RELATÓRIO SIMPLES USUÁRIO -->
              <div class="row text-center">
                <div class="col-md-4 stat-box">
                  <h6>Vendas do mês</h6>
                  <h4><?= $vendasAtual + $vendasCoprodutor ?></h4>
                </div>

                <div class="col-md-4 stat-box">
                  <h6>Total faturado de vendas</h6>
                  <?php
                  if ($_SESSION["tipo"] != 5) {
                    $soma = $vendasAtualSoma->soma;
                  } else {
                    $soma = $mes;
                  }
                  ?>
                  <h4><?= "R$ " . number_format($soma, 2, ",", "."); ?></h4>
                </div>

                <div class="col-md-4 stat-box">
                  <h6><?= ($_SESSION["tipo"] != 5) ? "Ticket médio" : "Comissão média" ?></h6>
                  <?php if ($vendasAtual) $somaVendas = $soma / $vendasAtual; ?>
                  <?php if ($vendasCoprodutor) $somaValores = $mes / $vendasCoprodutor; ?>
                  <h4><?= "R$ " . number_format(@$somaVendas + @$somaValores, 2, ",", ".") ?></h4>
                </div>
              </div>

              <!-- RELATÓRIO REDROCKET -->
              <?php if ($_SESSION["tipo"] == 1) : ?>
                <h5 class="mt-4"><i class="fas fa-rocket"></i> Geral WINPAY <small>(vendas pagas)</small></h5>

                <div class="row text-center justify-content-center" style="gap: 15px;">
                  <div class="col-md-4 stat-box">
                    <h6>Vendas do mês</h6>
                    <h4><?= $vendasTotal ?></h4>
                  </div>

                  <div class="col-md-4 stat-box">
                    <h6>Total vendas</h6>
                    <?php $soma = $vendasTotalSoma->soma; ?>
                    <h4><?= "R$ " . number_format($soma, 2, ",", "."); ?></h4>
                  </div>

                  <div class="col-md-4 stat-box">
                    <h6>Ticket médio</h6>
                    <?php if ($vendasTotal) : ?>
                      <h4><?= "R$ " . number_format($soma / $vendasTotal, 2, ",", ".") ?></h4>
                    <?php else : ?>
                      <h4>R$ 0,00</h4>
                    <?php endif ?>
                  </div>
                </div>
              <?php endif ?>
            </div>

            <!-- Estilos Modernizados -->
            <style>
              .winpay-card {
                background: rgb(0, 0, 0); /* Fundo escuro */
                border-radius: 10px; /* Cantos arredondados */
                color: #ffffff; /* Texto branco */
                border: 1px solid rgba(255, 255, 255, 0.1); /* Borda sutil */
                transition: all 0.2s ease-in-out;
                padding: 20px;
              }

              .winpay-card:hover {
                border-color: rgb(217, 192, 0);
                transform: scale(1.02);
              }

              /* Estilização das estatísticas */
              .stat-box {
                background: rgba(255, 255, 255, 0.05);
                padding: 15px;
                border-radius: 10px;
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
                margin-bottom: 10px; /* Garante espaçamento entre os cards */
              }

              .stat-box:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 10px rgb(155, 135, 0);
              }

              /* Ajusta a distribuição dos elementos na linha */
              .row.text-center {
                display: flex;
                flex-wrap: wrap;
                justify-content: center; /* Centraliza os elementos */
                gap: 15px; /* Adiciona espaçamento entre os cards */
              }


              /* Títulos das estatísticas */
              h6 {
                font-size: 0.9rem;
                font-weight: bold;
                color: #A0AEC0;
              }

              /* Números destacados */
              h4 {
                font-size: 1.6rem;
                font-weight: bold;
                margin-top: 5px;
                color: rgb(217, 192, 0);
              }
            </style>

            <!-- /.card -->

          </div>
          <!-- FIM GRÁFICO -->

        </div>
        <!-- FIM GRÁFICO -->

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
  function load(action, tempo = 1000) {
    var load_div = $(".ajax_load");
    if (action === "open") {
      load_div.fadeIn().css("display", "flex");
      setTimeout(() => {
        load_div.fadeOut();
      }, tempo);
    } else {
      load_div.fadeOut();
    }
  }

  function busca(dt) {
    var inicio, fim;



    if (dt == 'personalizado' && ($("#inicio").val() && $("#fim").val())) {
      inicio = $("#inicio").val();
      fim = $("#fim").val();
    } else {
      inicio,
      fim = null;
    }
    $.ajax({
      url: "<?= url('dados') ?>",
      type: 'GET',
      data: {
        busca: dt,
        inicio: inicio,
        fim: fim
      },
      beforeSend: () => {
        load('open')
      },
      success: function(res) {
        $("#dados_render").html(res);
      }
    });
  }

  busca('hoje');

  // if (<?= $_SESSION["tipo"] ?> != 5 && <?= $_SESSION["tipo"] ?> != 6)
  //   $('#modalUpsell').modal('show');

  $(function() {
    /* ChartJS - Gráfico de Vendas Modernizado */

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
                label: 'Mês Atual',
                backgroundColor: 'rgba(155, 135, 245, 0.2)', // Roxo claro
                borderColor: 'rgb(217, 192, 0)', // Roxo vibrante
                pointRadius: 5,
                pointBorderWidth: 2,
                pointBackgroundColor: 'rgb(217, 192, 0)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: "#fff",
                pointHoverBorderColor: 'rgb(217, 192, 0)',
                data: [<?php foreach ($comAtual as $atual) echo $atual . "," ?>],
                fill: true,
                tension: 0.3, // Suavização das curvas
            },
            {
                label: 'Mês Anterior',
                borderDash: [5, 5], // Linha tracejada
                backgroundColor: 'rgba(255, 99, 132, 0.2)', // Vermelho claro
                borderColor: 'rgb(255, 99, 132)', // Vermelho moderno
                pointRadius: 5,
                pointBorderWidth: 2,
                pointBackgroundColor: 'rgb(255, 99, 132)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: "#fff",
                pointHoverBorderColor: 'rgb(255, 99, 132)',
                data: [<?php foreach ($comAnterior as $anterior) echo $anterior . "," ?>],
                fill: true,
                tension: 0.3, // Suavização das curvas
            }
        ]
    }

    var opcoesVendas = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    color: '#A0AEC0', // Cor das legendas no tema escuro
                    font: {
                        size: 13,
                        weight: 'bold',
                    }
                }
            }
        },
        interaction: {
            mode: 'nearest',
            intersect: false,
        },
        hover: {
            mode: 'nearest',
            intersect: false
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#A0AEC0', // Cor do texto eixo X
                }
            },
            y: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)', // Linhas sutis no eixo Y
                    drawBorder: false
                },
                ticks: {
                    color: '#A0AEC0' // Cor do texto eixo Y
                }
            }
        }
    }

    var graficoVendas = new Chart(canvasVendas, {
        type: 'line',
        data: dadosVendas,
        options: opcoesVendas
    });
});

</script>

<?= $v->end(); ?>
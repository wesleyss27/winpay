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
        <style>
          /* Filtros Ultra Modernos - Glassmorphism */
          .btn-group {
            display: flex !important;
            gap: 6px !important;
            align-items: center !important;
          }

          .btn-winpay {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02)) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 8px 16px !important;
            border-radius: 20px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            font-family: 'Inter', sans-serif !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            backdrop-filter: blur(20px) !important;
            position: relative !important;
            overflow: hidden !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
            text-align: center !important;
            justify-content: center !important;
            align-items: center !important;
          }

          .btn-winpay::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: -100% !important;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(90deg, transparent, rgba(255, 221, 0, 0.2), transparent) !important;
            transition: left 0.5s ease !important;
          }

          .btn-winpay:hover {
            background: linear-gradient(145deg, rgba(255, 221, 0, 0.1), rgba(255, 221, 0, 0.05)) !important;
            border-color: rgba(255, 221, 0, 0.3) !important;
            color: #FFDD00 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 32px rgba(255, 221, 0, 0.2) !important;
          }

          .btn-winpay:hover::before {
            left: 100% !important;
          }

          .btn-winpay:active {
            transform: translateY(0) !important;
            transition: transform 0.1s ease !important;
          }

          .dropdown-toggle::after {
            content: '⌄' !important;
            border: none !important;
            margin-left: 6px !important;
            font-size: 10px !important;
            opacity: 0.6 !important;
            transition: transform 0.3s ease, opacity 0.3s ease !important;
          }

          .dropdown-toggle:hover::after {
            opacity: 1 !important;
          }

          .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg) !important;
          }

          .dropdown-menu {
            background: linear-gradient(145deg, rgba(20, 20, 20, 0.95), rgba(10, 10, 10, 0.95)) !important;
            border: 1px solid rgba(255, 221, 0, 0.2) !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.05) inset !important;
            padding: 20px !important;
            margin-top: 8px !important;
            min-width: 280px !important;
            backdrop-filter: blur(40px) !important;
          }

          .dropdown-winpay .row {
            display: flex !important;
            gap: 12px !important;
            margin-bottom: 16px !important;
          }

          .dropdown-winpay .col-6 {
            flex: 1 !important;
          }

          .dropdown-winpay label {
            color: rgba(255, 221, 0, 0.8) !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            margin-bottom: 6px !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            font-family: 'Inter', sans-serif !important;
          }

          .winpay-input {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01)) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 12px !important;
            padding: 10px 14px !important;
            color: #ffffff !important;
            font-size: 13px !important;
            font-family: 'Inter', sans-serif !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            backdrop-filter: blur(10px) !important;
          }

          .winpay-input:focus {
            border-color: rgba(255, 221, 0, 0.5) !important;
            box-shadow: 0 0 0 3px rgba(255, 221, 0, 0.1), 0 4px 20px rgba(255, 221, 0, 0.1) !important;
            outline: none !important;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02)) !important;
          }

          .btn-winpay-sm {
            background: linear-gradient(145deg, #FFDD00, #E6C700) !important;
            border: none !important;
            color: #000000 !important;
            padding: 10px 20px !important;
            border-radius: 12px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            font-family: 'Inter', sans-serif !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            position: relative !important;
            overflow: hidden !important;
            box-shadow: 0 4px 20px rgba(255, 221, 0, 0.3) !important;
            text-align: center !important;
            justify-content: center !important;
            align-items: center !important;
          }

          .btn-winpay-sm::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: -100% !important;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent) !important;
            transition: left 0.4s ease !important;
          }

          .btn-winpay-sm:hover {
            background: linear-gradient(145deg, #FFF200, #FFDD00) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 30px rgba(255, 221, 0, 0.4) !important;
          }

          .btn-winpay-sm:hover::before {
            left: 100% !important;
          }

          .btn-winpay-sm:active {
            transform: translateY(0) !important;
          }
        </style>

        <div class="row">
        <div id="notificacaoWrapper" style="display: none; padding: 10px; text-align: center;">
          <button id="permitirNotificacao" class="btn btn-warning">
            <span class="material-icons" style="color: black; vertical-align: middle;">notifications</span>
            <span style="margin-left: 6px;">Ativar Notificações</span>
          </button>
        </div>
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

              <!-- Pequena legenda opcional -->
              <small class="text-center d-block mb-2">Clique em uma opção para ocultar</small>

              <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
              <!-- Área do gráfico -->
              <div class="chart-container">
                <!-- <canvas id="areaChart"></canvas> -->
                <div id="grafico-vendas" style="width: 100%; height: 45vh;"></div>
              </div>
            </div>

           
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
              
                /* Modern WinPay Cards Styles */
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

                /* Estilização dos cards no estilo WinPay modernizado */
                .winpay-card {
                  background: linear-gradient(135deg, rgb(5, 5, 5) 0%, rgb(0, 0, 0) 100%);
                  border-radius: 16px;
                  color: #ffffff;
                  border: 1px solid rgba(255, 255, 255, 0.08);
                  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                  position: relative;
                  overflow: hidden;
                  backdrop-filter: blur(10px);
                  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                }

                .winpay-card:hover {
                  border-color: rgba(217, 192, 0, 0.3);
                  transform: translateY(-4px);
                  box-shadow: 0 8px 30px rgba(217, 192, 0, 0.15);
                  background: linear-gradient(135deg, rgb(8, 8, 8) 0%, rgb(3, 3, 3) 100%);
                }

                /* Container superior com ícone e botão */
                .winpay-card .d-flex.justify-content-between.align-items-center {
                  margin-bottom: 1rem;
                }

                /* Ícones pequenos no canto superior esquerdo */
                .card-icon {
                  font-size: 1.5rem;
                  color: rgb(217, 192, 0);
                  background: rgba(217, 192, 0, 0.1);
                  width: 48px;
                  height: 48px;
                  border-radius: 12px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  transition: all 0.3s ease;
                }

                .winpay-card:hover .card-icon {
                  background: rgba(217, 192, 0, 0.15);
                  transform: scale(1.05);
                  color: #f4d03f;
                }

                /* Títulos dos cards */
                .winpay-card h5 {
                  font-size: 0.875rem;
                  font-weight: 600;
                  color: #9CA3AF;
                  margin-bottom: 0.5rem;
                  text-transform: uppercase;
                  letter-spacing: 0.5px;
                  line-height: 1.2;
                }

                /* Valor do card */
                .winpay-card h3 {
                  font-size: 2rem;
                  font-weight: 700;
                  margin-bottom: 0.75rem;
                  color: #ffffff;
                  line-height: 1.1;
                  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                }

                /* Pequena legenda abaixo */
                .winpay-card small,
                .winpay-card .text-muted {
                  color: #6B7280 !important;
                  font-size: 0.8rem;
                  font-weight: 500;
                  line-height: 1.3;
                  opacity: 0.9;
                }

                /* Ocultação dos valores com efeito moderno */
                .hidden-value {
                  filter: blur(6px);
                  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                  position: relative;
                }

                .hidden-value::after {
                  content: '●●●●●●●●';
                  position: absolute;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
                  color: rgba(217, 192, 0, 0.6);
                  font-size: 1.2rem;
                  letter-spacing: 4px;
                  filter: none;
                }

                /* Estilização do botão de visibilidade modernizado */
                .toggle-visibility {
                  background: rgba(255, 255, 255, 0.05);
                  border: 1px solid rgba(255, 255, 255, 0.1);
                  color: #9CA3AF;
                  font-size: 0.9rem;
                  cursor: pointer;
                  border-radius: 8px;
                  width: 36px;
                  height: 36px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  transition: all 0.3s ease;
                  backdrop-filter: blur(5px);
                }

                .toggle-visibility:hover {
                  background: rgba(217, 192, 0, 0.1);
                  border-color: rgba(217, 192, 0, 0.3);
                  color: rgb(217, 192, 0);
                  transform: scale(1.05);
                }

                .toggle-visibility:active {
                  transform: scale(0.95);
                }

                /* Estilização das estatísticas modernizada */
                .stat-box {
                  background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
                  padding: 1.5rem;
                  border-radius: 12px;
                  border: 1px solid rgba(255, 255, 255, 0.05);
                  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                  margin-bottom: 1rem;
                  position: relative;
                  overflow: hidden;
                  backdrop-filter: blur(5px);
                }

                .stat-box::before {
                  content: '';
                  position: absolute;
                  top: 0;
                  left: 0;
                  right: 0;
                  height: 1px;
                  background: linear-gradient(90deg, transparent, rgba(217, 192, 0, 0.3), transparent);
                }

                .stat-box:hover {
                  transform: translateY(-2px) scale(1.02);
                  box-shadow: 0 8px 25px rgba(217, 192, 0, 0.15);
                  border-color: rgba(217, 192, 0, 0.2);
                  background: linear-gradient(135deg, rgba(217, 192, 0, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
                }

                /* Títulos das estatísticas */
                .stat-box h6 {
                  font-size: 0.8rem;
                  font-weight: 600;
                  color: #9CA3AF;
                  margin-bottom: 0.75rem;
                  text-transform: uppercase;
                  letter-spacing: 0.5px;
                  line-height: 1.2;
                }

                /* Números destacados das estatísticas */
                .stat-box h4 {
                  font-size: 1.75rem;
                  font-weight: 700;
                  margin-top: 0.5rem;
                  color: rgb(217, 192, 0);
                  line-height: 1.1;
                  text-shadow: 0 2px 4px rgba(217, 192, 0, 0.3);
                  transition: all 0.3s ease;
                }

                .stat-box:hover h4 {
                  color: #f4d03f;
                  transform: scale(1.05);
                }

                /* Ajuste da distribuição dos elementos na linha */
                .winpay-card .row.text-center {
                  display: flex;
                  flex-wrap: wrap;
                  justify-content: center;
                  gap: 1rem;
                  margin: 0 -0.5rem;
                }

                .winpay-card .row.text-center .col-md-4 {
                  flex: 1;
                  min-width: 200px;
                  max-width: 300px;
                }

                /* Títulos das seções com ícones */
                .winpay-card h5 i {
                  color: rgb(217, 192, 0);
                  margin-right: 0.5rem;
                  font-size: 1rem;
                }

                .winpay-card h5 small {
                  color: #6B7280;
                  font-weight: 400;
                  font-size: 0.75rem;
                  margin-left: 0.5rem;
                }

                /* Animação suave para mostrar/ocultar valores */
                .winpay-card h3:not(.hidden-value) {
                  animation: fadeIn 0.3s ease-in-out;
                }

                @keyframes fadeIn {
                  from {
                    opacity: 0;
                    transform: translateY(5px);
                  }
                  to {
                    opacity: 1;
                    transform: translateY(0);
                  }
                }

                /* Responsividade aprimorada */
                @media (max-width: 768px) {
                  .winpay-card {
                    border-radius: 12px;
                    margin-bottom: 1rem;
                  }
                  
                  .winpay-card h3 {
                    font-size: 1.75rem;
                  }
                  
                  .card-icon {
                    width: 42px;
                    height: 42px;
                    font-size: 1.3rem;
                  }
                  
                  .toggle-visibility {
                    width: 32px;
                    height: 32px;
                    font-size: 0.8rem;
                  }
                  
                  .stat-box {
                    padding: 1.25rem;
                    margin-bottom: 0.75rem;
                  }
                  
                  .stat-box h4 {
                    font-size: 1.5rem;
                  }
                  
                  .winpay-card .row.text-center {
                    gap: 0.75rem;
                  }
                  
                  .winpay-card .row.text-center .col-md-4 {
                    min-width: 150px;
                  }
                }

                @media (max-width: 480px) {
                  .winpay-card {
                    padding: 1rem !important;
                  }
                  
                  .winpay-card h3 {
                    font-size: 1.5rem;
                  }
                  
                  .winpay-card h5 {
                    font-size: 0.8rem;
                  }
                  
                  .card-icon {
                    width: 38px;
                    height: 38px;
                    font-size: 1.2rem;
                  }
                  
                  .stat-box {
                    padding: 1rem;
                  }
                  
                  .stat-box h4 {
                    font-size: 1.3rem;
                  }
                  
                  .stat-box h6 {
                    font-size: 0.75rem;
                  }
                }

                /* Estados de foco para acessibilidade */
                .toggle-visibility:focus {
                  outline: 2px solid rgba(217, 192, 0, 0.5);
                  outline-offset: 2px;
                }

                .stat-box:focus-within {
                  border-color: rgba(217, 192, 0, 0.3);
                  box-shadow: 0 0 0 2px rgba(217, 192, 0, 0.1);
                }

                /* Efeito de carregamento opcional */
                .winpay-card.loading {
                  animation: pulse 1.5s infinite;
                }

                .stat-box.loading {
                  animation: pulse 1.5s infinite;
                }

                @keyframes pulse {
                  0%, 100% {
                    opacity: 1;
                  }
                  50% {
                    opacity: 0.8;
                  }
                }

                /* Melhorias no gradiente de hover */
                .winpay-card::after {
                  content: '';
                  position: absolute;
                  top: 0;
                  left: 0;
                  right: 0;
                  bottom: 0;
                  background: linear-gradient(135deg, rgba(217, 192, 0, 0.02) 0%, rgba(244, 208, 63, 0.02) 100%);
                  opacity: 0;
                  transition: opacity 0.3s ease;
                  pointer-events: none;
                  border-radius: 16px;
                }

                .winpay-card:hover::after {
                  opacity: 1;
                }

                /* Efeitos de brilho sutil nos stat-boxes */
                .stat-box::after {
                  content: '';
                  position: absolute;
                  top: 0;
                  left: 0;
                  right: 0;
                  bottom: 0;
                  background: linear-gradient(135deg, rgba(217, 192, 0, 0.05) 0%, transparent 50%, rgba(217, 192, 0, 0.05) 100%);
                  opacity: 0;
                  transition: opacity 0.3s ease;
                  pointer-events: none;
                  border-radius: 12px;
                }

                .stat-box:hover::after {
                  opacity: 1;
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

    <?php
      $vendasAtual = array_fill(1, 31, 0);
      $vendasAnterior = array_fill(1, 31, 0);

      // Mês Atual
      if (!empty($vendasAtualSomaProdAfi) && is_iterable($vendasAtualSomaProdAfi)) {
          foreach ($vendasAtualSomaProdAfi as $item) {
              $dia = (int)$item->dia;
              $vendasAtual[$dia] = round((float)$item->soma, 2);
          }
      }

      // Mês Anterior
      if (!empty($vendasAnteriorSomaProdAfi) && is_iterable($vendasAnteriorSomaProdAfi)) {
          foreach ($vendasAnteriorSomaProdAfi as $item) {
              $dia = (int)$item->dia;
              $vendasAnterior[$dia] = round((float)$item->soma, 2);
          }
      }

      $labels = range(1, 31);
    ?>

  </div>
</div>

<?= $v->start("scripts"); ?>
<script>
  function isIphone() {
    return /iPhone/.test(navigator.userAgent) && !window.MSStream;
  }

  if (isIphone()) {
    document.getElementById('notificacaoWrapper').style.display = 'block';
  }
</script>
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

  /*$(function() {
    /* ChartJS - Gráfico de Vendas Modernizado 

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
});*/


$(function () {
  const chartDom = document.getElementById('grafico-vendas');
  const myChart = echarts.init(chartDom);

  const option = {
    backgroundColor: 'transparent',
    color: ['#F4D03F', '#7B8CFF'],
    textStyle: {
      fontFamily: 'Inter',
      color: '#F3F4F6'
    },
    tooltip: {
      trigger: 'axis',
      backgroundColor: 'rgba(7, 7, 8, 0.92)',
      borderColor: 'rgba(244, 208, 63, 0.3)',
      borderWidth: 1,
      padding: 12,
      textStyle: {
        color: '#F9FAFB',
        fontFamily: 'Inter',
        fontWeight: 500
      },
      axisPointer: {
        type: 'line',
        lineStyle: {
          color: 'rgba(244, 208, 63, 0.45)',
          width: 1.2
        }
      }
    },
    legend: {
      data: ['Mês Atual', 'Mês Anterior'],
      top: 0,
      itemWidth: 12,
      itemHeight: 8,
      icon: 'roundRect',
      textStyle: {
        color: '#E5E7EB',
        fontFamily: 'Inter',
        fontWeight: 500,
        fontSize: 13
      }
    },
    grid: {
      left: '3%',
      right: '3%',
      top: '18%',
      bottom: '10%',
      containLabel: true
    },
    xAxis: {
      type: 'category',
      data: <?= json_encode(range(1, 31)) ?>,
      boundaryGap: false,
      axisLine: {
        lineStyle: { color: '#1F2937' }
      },
      axisTick: { show: false },
      axisLabel: {
        color: '#9CA3AF',
        fontFamily: 'Inter',
        fontSize: 12
      },
      splitLine: {
        show: false
      }
    },
    yAxis: {
      type: 'value',
      axisLine: { show: false },
      axisTick: { show: false },
      axisLabel: {
        color: '#9CA3AF',
        fontFamily: 'Inter',
        fontSize: 12
      },
      splitLine: {
        lineStyle: {
          color: 'rgba(148, 163, 184, 0.08)'
        }
      }
    },
    series: [
      {
        name: 'Mês Atual',
        type: 'line',
        smooth: true,
        showSymbol: false,
        symbol: 'circle',
        symbolSize: 8,
        lineStyle: {
          color: '#F4D03F',
          width: 3,
          shadowColor: 'rgba(244, 208, 63, 0.25)',
          shadowBlur: 12
        },
        areaStyle: {
          opacity: 1,
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: 'rgba(244, 208, 63, 0.35)' },
            { offset: 1, color: 'rgba(244, 208, 63, 0.02)' }
          ])
        },
        emphasis: {
          focus: 'series',
          scale: true
        },
        data: <?= json_encode(array_values($vendasAtual)) ?>
      },
      {
        name: 'Mês Anterior',
        type: 'line',
        smooth: true,
        showSymbol: false,
        symbol: 'circle',
        symbolSize: 8,
        lineStyle: {
          color: '#7B8CFF',
          width: 3,
          shadowColor: 'rgba(123, 140, 255, 0.25)',
          shadowBlur: 12
        },
        areaStyle: {
          opacity: 1,
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: 'rgba(123, 140, 255, 0.28)' },
            { offset: 1, color: 'rgba(123, 140, 255, 0.02)' }
          ])
        },
        emphasis: {
          focus: 'series',
          scale: true
        },
        data: <?= json_encode(array_values($vendasAnterior)) ?>
      }
    ]
  };

  myChart.setOption(option);
  window.addEventListener('resize', () => myChart.resize());
});

    const userId = <?= $_SESSION["id"] ?? 'null' ?>;
    let swRegistration = null;

    // Detecta se é iPhone
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    if ('serviceWorker' in navigator && 'PushManager' in window) {
      navigator.serviceWorker.register('/sw.js')
        .then(function (registration) {
          console.log('✅ Service Worker registrado:', registration);
          swRegistration = registration;

          // Se **não** for iPhone, já pede permissão automática
          if (!isIOS) {
            Notification.requestPermission().then(function (permission) {
              if (permission === 'granted') {
                registration.pushManager.subscribe({
                  userVisibleOnly: true,
                  applicationServerKey: 'BPVzsIRnzg3Vu6vEQ2RsYEZ_5TF-ow515Sk3kKMOhRcr5F-jL9IRcpIC0Nat_3nv2o8dvJzBIUYytAjP8LzJpeM'
                }).then(function (subscription) {
                  fetch('/salvar-token', {
                    method: 'POST',
                    body: JSON.stringify({
                      userId: userId,
                      subscription: subscription
                    }),
                    headers: {
                      'Content-Type': 'application/json'
                    }
                  });
                });
              }
            });
          } else {
            // Se for iPhone, exibe o botão
            document.getElementById('notificacaoWrapper').style.display = 'block';
          }
        })
        .catch(console.error);
    }

    // Evento para o botão (somente iPhone)
    document.getElementById('permitirNotificacao')?.addEventListener('click', async () => {
      if (!swRegistration) {
        console.warn('⚠️ Service Worker ainda não registrado');
        return;
      }

      const permission = await Notification.requestPermission();
      if (permission === 'granted') {
        const subscription = await swRegistration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: 'BPVzsIRnzg3Vu6vEQ2RsYEZ_5TF-ow515Sk3kKMOhRcr5F-jL9IRcpIC0Nat_3nv2o8dvJzBIUYytAjP8LzJpeM'
        });

        await fetch('/salvar-token', {
          method: 'POST',
          body: JSON.stringify({
            userId: userId,
            subscription: subscription
          }),
          headers: {
            'Content-Type': 'application/json'
          }
        });

        alert('🔔 Notificações ativadas com sucesso!');
        document.getElementById('notificacaoWrapper').style.display = 'none';
      } else {
        alert('❌ Permissão negada.');
      }
    });
    


</script>

<?= $v->end(); ?>

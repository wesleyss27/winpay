<div class="ajax_load">
  <div class="ajax_load_box">
    <div class="ajax_load_box_circle"></div>
    <div class="ajax_load_box_title">Aguarde, carregando!</div>
  </div>
</div>
<!--
<div class="row">
  <div class="col-md-4">
    <div class="">
      <a href="<?= url("comissao") ?>" class="text-dark">
        <span>Disponível para saque</span>
        <h5 class="text-info">R$ <?= formataNumero( ! empty( $saldoPagarme->available_amount ) ? $saldoPagarme->available_amount / 100 : 0 ) ?></h5>
      </a>
    </div>
  </div>
</div>
<hr>
-->

<div class="row">
  <!-- Card Total em conta -->
  <div class="col-md-6 mb-5">
    <div class="winpay-card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <i class="fas fa-piggy-bank card-icon"></i> 
        <button class="toggle-visibility btn btn-sm" data-target="#totalValue">
          <i class="fas fa-eye-slash"></i>
        </button>
      </div>
      <h5 class="mb-1">Total em conta</h5>
      <h3 id="totalValue" class="hidden-value">R$ <?= formataNumero($valorTotal) ?></h3>
      <small class="d-block">Soma do valor disponível + recebimentos</small>
    </div>
  </div>

  <!-- Card Disponível para saque -->
  <div class="col-md-6 mb-5">
    <div class="winpay-card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <i class="fas fa-coins card-icon"></i> 
        <button class="toggle-visibility btn btn-sm" data-target="#disponivelValue">
          <i class="fas fa-eye-slash"></i>
        </button>
      </div>
      <h5 class="mb-1">Disponível para saque</h5>
      <h3 id="disponivelValue" class="hidden-value">
        R$ <?= formataNumero( ! empty( $saldoPagarme->available_amount ) ? $saldoPagarme->available_amount / 100 : 0 ) ?>
      </h3>
      <small class="d-block">Valor disponível para saque</small>
    </div>
  </div>

  <!-- Card Total cancelados 
  <div class="col-md-4 mb-4"> 
    <div class="winpay-card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <i class="fas fa-file-invoice-dollar card-icon"></i> 
        <button class="toggle-visibility btn btn-sm" data-target="#canceladoValue">
          <i class="fas fa-eye-slash"></i>
        </button>
      </div>
      <h5 class="mb-1">Total cancelados</h5>
      <h3 id="canceladoValue" class="hidden-value">R$ <?= formataNumero($valorCancelado) ?></h3>
      <small class="d-block">Valor total cancelado</small>
    </div>
  </div>
  -->
</div>



<!-- Estilos personalizados -->
<style>
  /* Estilização dos cards no estilo WinPay */
  .winpay-card {
    background: rgb(0, 0, 0); /* Fundo escuro para manter o estilo */
    border-radius: 10px; /* Cantos levemente arredondados */
    color: #ffffff; /* Fonte branca */
    border: 1px solid rgba(255, 255, 255, 0.1); /* Borda sutil */
    transition: all 0.2s ease-in-out;
  }

  .winpay-card:hover {
    border-color: rgb(217, 192, 0);
    transform: scale(1.02);
  }

  /* Ícones pequenos no canto superior esquerdo */
  .card-icon {
    font-size: 1.3rem;
    color: #A0AEC0;
  }

  /* Títulos dos cards */
  h5 {
    font-size: 1rem;
    font-weight: bold;
    color: #A0AEC0;
  }

  /* Valor do card */
  h3 {
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 5px;
  }

  /* Pequena legenda abaixo */
  .text-muted {
    color: #A0AEC0 !important;
    font-size: 0.85rem;
  }

  /* Ocultação dos valores */
  .hidden-value {
    filter: blur(5px);
    transition: filter 0.3s ease;
  }

  /* Estilização do botão de visibilidade */
  .toggle-visibility {
    background: none;
    border: none;
    color: #A0AEC0;
    font-size: 1rem;
    cursor: pointer;
  }

  .toggle-visibility:hover {
    color: #ffffff;
  }
</style>

<!-- Script para alternar a visibilidade dos valores -->
<script>
  document.querySelectorAll('.toggle-visibility').forEach(function(button) {
    button.addEventListener('click', function() {
      const targetSelector = this.getAttribute('data-target');
      const valueElement = document.querySelector(targetSelector);
      if (valueElement.classList.contains('hidden-value')) {
        valueElement.classList.remove('hidden-value');
        this.innerHTML = '<i class="fas fa-eye"></i>';
      } else {
        valueElement.classList.add('hidden-value');
        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
      }
    });
  });
</script>

<!-- LINHA 2 -->
<div class="row">
  <!-- Card Vendas Cartão -->
  <div class="col-md-4 mb-4">
    <div class="winpay-card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <i class="fas fa-credit-card card-icon"></i>
      </div>
      <h5 class="mb-1">Vendas Cartão</h5>
      <h3 class="modern-value">R$ <?= number_format(@$dados["valorCartaoPago"], 2, ",", ".") ?></h3>
      <h6 class="modern-percent">
        <?= number_format(@$p, 2, ",", ".") ?>% - <span class="text-muted">Total Cartão</span>
      </h6>
      <hr class="border-secondary my-2">
      <div class="d-flex justify-content-between text-center">
        <div class="progress-container">
          <h6 class="modern-label"><span class="text-muted">Aprovados:</span> <?= number_format($p, 2, ",", ".") ?>%</h6>
          <div class="progress modern-progress">
            <div class="progress-bar" role="progressbar" style="width:100%; background-color:rgb(217, 192, 0);"></div>
          </div>
        </div>
        <div class="progress-container">
          <h6 class="modern-label"><span class="text-muted">Cancelados:</span> <?= number_format($p, 2, ",", ".") ?>%</h6>
          <div class="progress modern-progress">
            <div class="progress-bar" role="progressbar" style="width:100%; background-color:rgb(155, 135, 0);"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Conversão Boleto -->
  <div class="col-md-4 mb-4">
    <div class="winpay-card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <i class="fas fa-receipt card-icon"></i>
      </div>
      <h5 class="mb-1">Boletos</h5>
      <h3 class="modern-value">R$ <?= number_format(@$dados["valorBoletoPago"], 2, ",", ".") ?></h3>
      <h6 class="modern-percent">
        <?= number_format(@$p, 2, ",", ".") ?>% - <span class="text-muted">Total Boletos</span>
      </h6>
      <hr class="border-secondary my-2">
      <div class="d-flex justify-content-between text-center">
        <div class="progress-container">
          <h6 class="modern-label"><span class="text-muted">Aprovados:</span> <?= number_format($p, 2, ",", ".") ?>%</h6>
          <div class="progress modern-progress">
            <div class="progress-bar" role="progressbar" style="width:100%; background-color:rgb(217, 192, 0);"></div>
          </div>
        </div>
        <div class="progress-container">
          <h6 class="modern-label"><span class="text-muted">Vencidos:</span> <?= number_format($p, 2, ",", ".") ?>%</h6>
          <div class="progress modern-progress">
            <div class="progress-bar" role="progressbar" style="width:100%; background-color:rgb(155, 135, 0);"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Conversão Pix -->
  <div class="col-md-4 mb-4">
    <div class="winpay-card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <i class="fas fa-bolt card-icon"></i>
      </div>
      <h5 class="mb-1">Pix</h5>
      <h3 class="modern-value">R$ <?= number_format(@$dados["valorPixPago"], 2, ",", ".") ?></h3>
      <h6 class="modern-percent">
        <?= number_format(@$p, 2, ",", ".") ?>% - <span class="text-muted">Total Pix</span>
      </h6>
      <hr class="border-secondary my-2">
      <div class="d-flex justify-content-between text-center">
        <div class="progress-container">
          <h6 class="modern-label"><span class="text-muted">Aprovados:</span> <?= number_format($p, 2, ",", ".") ?>%</h6>
          <div class="progress modern-progress">
            <div class="progress-bar" role="progressbar" style="width:100%; background-color:rgb(217, 192, 0);"></div>
          </div>
        </div>
        <div class="progress-container">
          <h6 class="modern-label"><span class="text-muted">Cancelados:</span> <?= number_format($p, 2, ",", ".") ?>%</h6>
          <div class="progress modern-progress">
            <div class="progress-bar" role="progressbar" style="width:100%; background-color:rgb(155, 135, 0);"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Estilos personalizados -->
<style>
  /* Estilização dos cards no estilo WinPay */
  .winpay-card {
    background: rgb(30, 36, 51); /* Fundo escuro */
    border-radius: 10px; /* Cantos levemente arredondados */
    color: #ffffff; /* Fonte branca */
    border: 1px solid rgba(255, 255, 255, 0.1); /* Borda sutil */
    transition: all 0.2s ease-in-out;
  }

  .winpay-card:hover {
    border-color: rgb(217, 192, 0);
    transform: scale(1.02);
  }

  /* Ícones */
  .card-icon {
    font-size: 1.5rem;
    color: #A0AEC0;
  }

  /* Títulos */
  h5 {
    font-size: 1rem;
    font-weight: bold;
    color: #A0AEC0;
  }

  /* Valor */
  h3 {
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 5px;
  }

  /* Legenda */
  .text-muted {
    color: #A0AEC0 !important;
    font-size: 0.85rem;
  }

  /* Labels */
  .modern-label {
    font-size: 0.85rem;
    font-weight: bold;
    color: #A0AEC0;
  }

  /* Barra de progresso */
  .modern-progress {
    height: 8px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    width: 100%;
  }

  /* Ajuste dos contêineres das barras para ficarem lado a lado */
  .progress-container {
    width: 48%;
  }
</style>

<!-- LINHA 3 -->
<div class="row">
  <!-- Card: Tentativas de compras 
  <div class="col-md-4 mb-4">
    <div class="winpay-card p-3 d-flex flex-column justify-content-between">
      <div>
        <div class="d-flex justify-content-between align-items-center">
          <i class="fas fa-cart-arrow-down card-icon"></i>
        </div>
        <h5 class="mb-2">Tentativas de compras</h5>
        <h3 class="modern-value">R$ <?= formataNumero($valorPendente) ?></h3>
      </div>
      <hr class="border-secondary my-2">
      <div class="row">
        <div class="col-6">
          <span class="modern-info">Total: <?= $pendenteVendas ?></span>
        </div>
        <div class="col-6 text-right">
          <span class="modern-info">Ticket: R$ <?= formataNumero($ticketPendente) ?></span>
        </div>
      </div>
    </div>
  </div>

  -->

  <!-- Card: Estornos e Chargebacks 
  <div class="col-md-4 mb-4">
    <div class="winpay-card p-3 d-flex flex-column justify-content-between">
      <div>
        <div class="d-flex justify-content-between align-items-center">
          <i class="fas fa-exchange-alt card-icon"></i>
        </div>
        <h5 class="mb-2">Estornos e Chargebacks</h5>
      </div>
      <hr class="border-secondary my-2">
      <div class="row">
        <div class="col-6">
          <span class="modern-info">Estornos: <?= $estornoVendas ?></span>
        </div>
        <div class="col-6 text-right">
          <span class="modern-info">R$ <?= formataNumero($valorEstorno) ?></span>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-6">
          <span class="modern-info">Chargebacks: <?= $chargebackVendas ?></span>
        </div>
        <div class="col-6 text-right">
          <span class="modern-info">R$ <?= formataNumero($valorChargeback) ?></span>
        </div>
      </div>
    </div>
  </div>

  -->

  <!-- Card: Abandono de checkout
  <div class="col-md-4 mb-4">
    <div class="winpay-card p-3 d-flex flex-column justify-content-between" style="min-height: 150px;">
      <div>
        <div class="d-flex justify-content-between align-items-center">
          <i class="fas fa-user-slash card-icon"></i>
        </div>
        <h5 class="mb-2">Abandono de checkout</h5>
      </div>
      <hr class="border-secondary my-2">
      <div class="row">
        <div class="col-6">
          <span class="modern-info">Total: <?= $abandonadoVendas ?></span>
        </div>
        <div class="col-6 text-right">
          <!-- Espaço reservado para outros dados se necessário -->
        </div>
      </div>
    </div>
  </div>
  -->
</div>


<!-- Estilos personalizados -->
<style>
  .winpay-card {
    background: rgb(30, 36, 51); /* Fundo escuro */
    border-radius: 10px; /* Cantos levemente arredondados */
    color: #ffffff; /* Fonte branca */
    border: 1px solid rgba(255, 255, 255, 0.1); /* Borda sutil */
    transition: all 0.2s ease-in-out;
    min-height: 180px; /* Garante que todos os cards tenham a mesma altura */
    display: flex;
    flex-direction: column;
  }

  .winpay-card:hover {
    border-color: rgb(217, 192, 0);
    transform: scale(1.02);
  }

  /* Ícones */
  .card-icon {
    font-size: 1.3rem;
    color: #A0AEC0;
  }

  /* Títulos */
  h5 {
    font-size: 1rem;
    font-weight: bold;
    color: #A0AEC0;
  }

  /* Valor */
  h3 {
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 5px;
  }

  /* Legenda */
  .text-muted {
    color: #A0AEC0 !important;
    font-size: 0.85rem;
  }

  /* Label */
  .modern-info {
    font-size: 0.85rem;
    font-weight: bold;
    color: #A0AEC0;
  }

  /* Garante que os cards fiquem com a mesma altura */
  .d-flex.flex-column {
    height: 100%;
  }
</style>


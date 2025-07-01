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
      <h3 id="totalValue" class="hidden-value">R$ <?= formataNumero($valorPago) ?></h3>
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
}

/* Estados de foco para acessibilidade */
.toggle-visibility:focus {
  outline: 2px solid rgba(217, 192, 0, 0.5);
  outline-offset: 2px;
}

/* Efeito de carregamento opcional */
.winpay-card.loading {
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
           Espaço reservado para outros dados se necessário 
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


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
        <h5 class="text-info">R$ <?= formataNumero(!empty($saldoPagarme->available_amount) ? $saldoPagarme->available_amount / 100 : 0) ?></h5>
      </a>
    </div>
  </div>
</div>
<hr>
-->

<?php
  $totalVendas = (float) ($dados["totalVendas"] ?? 0);

  $cartoesTotal = (float) ($dados["cartoes"] ?? 0);
  $cartoesPagos = (float) ($dados["cartoesPagos"] ?? 0);
  $cartoesCancelados = (float) ($dados["cartoesCancelados"] ?? 0);

  $boletosTotal = (float) ($dados["boletos"] ?? 0);
  $boletosPagos = (float) ($dados["boletosPagos"] ?? 0);
  $boletosVencidos = (float) ($dados["boletosCancelados"] ?? 0);

  $pixTotal = (float) ($dados["pix"] ?? 0);
  $pixPagos = (float) ($dados["pixPagos"] ?? 0);
  $pixCancelados = (float) ($dados["pixCancelados"] ?? 0);

  $percent = static function (float $part, float $whole): float {
    return $whole > 0 ? ($part / $whole) * 100 : 0.0;
  };

  $shareCartao = $percent($cartoesTotal, $totalVendas);
  $shareBoleto = $percent($boletosTotal, $totalVendas);
  $sharePix = $percent($pixTotal, $totalVendas);

  $cartaoAprovadoPct = $percent($cartoesPagos, $cartoesTotal);
  $cartaoCanceladoPct = $percent($cartoesCancelados, $cartoesTotal);

  $boletoAprovadoPct = $percent($boletosPagos, $boletosTotal);
  $boletoVencidoPct = $percent($boletosVencidos, $boletosTotal);

  $pixAprovadoPct = $percent($pixPagos, $pixTotal);
  $pixCanceladoPct = $percent($pixCancelados, $pixTotal);
?>

<style>
  .winpay-dashboard .winpay-dashboard-row {
    margin-bottom: 2.5rem;
  }

  .winpay-dashboard .winpay-card {
    position: relative;
    background: #070708;
    border-radius: 18px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    padding: 1.6rem;
    color: #ffffff;
    transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    box-shadow: 0 20px 34px rgba(0, 0, 0, 0.45);
    overflow: hidden;
    min-height: 100%;
  }

  .winpay-dashboard .winpay-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: radial-gradient(circle at top right, rgba(217, 192, 0, 0.08), transparent 55%);
    opacity: 0.6;
    pointer-events: none;
  }

  .winpay-dashboard .winpay-card:hover {
    transform: translateY(-6px);
    border-color: rgba(217, 192, 0, 0.28);
    box-shadow: 0 26px 42px rgba(0, 0, 0, 0.52);
  }

  .winpay-dashboard .winpay-card-header,
  .winpay-dashboard .winpay-card-content,
  .winpay-dashboard .winpay-card-footer {
    position: relative;
    z-index: 2;
  }

  .winpay-dashboard .winpay-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
  }

  .winpay-dashboard .winpay-card .icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: rgba(217, 192, 0, 0.12);
    color: #f7d158;
    font-size: 1.4rem;
    box-shadow: 0 14px 26px rgba(217, 192, 0, 0.2);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .winpay-dashboard .winpay-card:hover .icon-wrapper {
    transform: scale(1.08);
    box-shadow: 0 18px 32px rgba(217, 192, 0, 0.28);
  }

  .winpay-dashboard .toggle-visibility {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.06);
    color: rgba(255, 255, 255, 0.72);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
  }

  .winpay-dashboard .toggle-visibility:hover,
  .winpay-dashboard .toggle-visibility[aria-pressed="true"] {
    background: rgba(217, 192, 0, 0.18);
    color: rgb(217, 192, 0);
    border-color: rgba(217, 192, 0, 0.4);
  }

  .winpay-dashboard .toggle-visibility:focus {
    outline: 2px solid rgba(217, 192, 0, 0.45);
    outline-offset: 3px;
  }

  .winpay-dashboard .winpay-card-title {
    display: inline-block;
    font-size: 0.9rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.62);
    margin-bottom: 0.6rem;
  }

  .winpay-dashboard .winpay-card-value {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 0.85rem;
    text-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
    position: relative;
  }

  .winpay-dashboard .winpay-card-helper,
  .winpay-dashboard .winpay-card-meta {
    display: block;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
  }

  .winpay-dashboard .winpay-card-meta {
    margin-bottom: 1.1rem;
  }

  .winpay-dashboard .winpay-card-divider {
    border-color: rgba(255, 255, 255, 0.06) !important;
    margin: 1.4rem 0;
  }

  .winpay-dashboard .winpay-card-footer {
    display: flex;
    gap: 1.1rem;
    flex-wrap: wrap;
  }

  .winpay-dashboard .winpay-metric {
    position: relative;
    flex: 1 1 160px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    background: rgba(8, 8, 9, 0.92);
    border: 1px solid rgba(255, 255, 255, 0.04);
    box-shadow: 0 16px 28px rgba(0, 0, 0, 0.35);
    overflow: hidden;
  }

  .winpay-dashboard .winpay-metric::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: radial-gradient(circle farthest-side at 85% 20%, rgba(217, 192, 0, 0.12), transparent);
    opacity: 0.6;
    pointer-events: none;
  }

  .winpay-dashboard .winpay-metric-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
  }

  .winpay-dashboard .winpay-metric-body {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
  }

  .winpay-dashboard .winpay-metric-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(255, 255, 255, 0.58);
    font-weight: 600;
  }

  .winpay-dashboard .winpay-metric-value {
    font-size: 1.05rem;
    font-weight: 600;
    color: #ffe57a;
    opacity: 0.96;
  }

  .winpay-dashboard .winpay-metric[data-tone="calm"] .winpay-metric-value {
    color: rgba(255, 229, 122, 0.75);
  }

  .winpay-dashboard .winpay-metric[data-tone="calm"] .winpay-metric-label {
    color: rgba(255, 255, 255, 0.45);
  }

  .winpay-dashboard .hidden-value {
    filter: blur(8px);
    opacity: 0.25;
  }

  .winpay-dashboard .hidden-value::after {
    content: attr(data-mask);
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    color: rgba(217, 192, 0, 0.65);
    letter-spacing: 0.08em;
    filter: none;
    opacity: 1;
    pointer-events: none;
  }

  .winpay-dashboard .hidden-value[data-mask=""]::after {
    content: "••••••••";
  }

  .winpay-dashboard .winpay-card .text-muted {
    color: rgba(255, 255, 255, 0.55) !important;
  }

  @media (max-width: 991.98px) {
    .winpay-dashboard .winpay-card {
      padding: 1.5rem;
    }

    .winpay-dashboard .winpay-card-value {
      font-size: 2rem;
    }
  }

  @media (max-width: 575.98px) {
    .winpay-dashboard .winpay-card {
      padding: 1.35rem;
      border-radius: 14px;
    }

    .winpay-dashboard .winpay-card-header {
      margin-bottom: 1.25rem;
    }

    .winpay-dashboard .winpay-card .icon-wrapper {
      width: 46px;
      height: 46px;
      border-radius: 14px;
    }

    .winpay-dashboard .toggle-visibility {
      width: 38px;
      height: 38px;
      border-radius: 12px;
    }

    .winpay-dashboard .winpay-card-footer {
      gap: 0.85rem;
    }

    .winpay-dashboard .winpay-metric {
      flex: 1 1 100%;
      padding: 0.75rem 0.85rem;
    }
  }
</style>

<div class="winpay-dashboard">
<div class="row winpay-dashboard-row">
  <div class="col-md-6 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-piggy-bank"></i>
        </span>
        <button class="toggle-visibility" data-target="#totalValue">
          <i class="fas fa-eye-slash"></i>
        </button>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Total em conta</span>
        <h3 id="totalValue" class="winpay-card-value hidden-value" data-mask="R$ ••••••••">
          R$ <?= formataNumero($valorPago) ?>
        </h3>
        <span class="winpay-card-helper">Soma do valor disponível + recebimentos</span>
      </div>
    </div>
  </div>

  <div class="col-md-6 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-coins"></i>
        </span>
        <button class="toggle-visibility" data-target="#disponivelValue">
          <i class="fas fa-eye-slash"></i>
        </button>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Disponível para saque</span>
        <h3 id="disponivelValue" class="winpay-card-value hidden-value" data-mask="R$ ••••••••">
          R$ <?= number_format($saldo->valor, 2, ',', '.') ?>
        </h3>
        <span class="winpay-card-helper">Valor disponível para saque</span>
      </div>
    </div>
  </div>
</div>

<div class="row winpay-dashboard-row">
  <div class="col-md-4 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-credit-card"></i>
        </span>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Vendas Cartão</span>
        <h3 class="winpay-card-value">
          R$ <?= number_format(@$dados["valorCartaoPago"], 2, ",", ".") ?>
        </h3>
        <span class="winpay-card-meta">
          <?= number_format($shareCartao, 2, ",", ".") ?>% - <span class="text-muted">Total Cartão</span>
        </span>
      </div>
      <hr class="winpay-card-divider">
      <div class="winpay-card-footer">
        <div class="winpay-metric" data-tone="bright">
          <div class="winpay-metric-content">
            <div class="winpay-metric-body">
              <span class="winpay-metric-label">Aprovados</span>
              <span class="winpay-metric-value"><?= number_format($cartaoAprovadoPct, 2, ",", ".") ?>%</span>
            </div>
          </div>
        </div>
        <div class="winpay-metric" data-tone="calm">
          <div class="winpay-metric-content">
            <div class="winpay-metric-body">
              <span class="winpay-metric-label">Cancelados</span>
              <span class="winpay-metric-value"><?= number_format($cartaoCanceladoPct, 2, ",", ".") ?>%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-receipt"></i>
        </span>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Boletos</span>
        <h3 class="winpay-card-value">
          R$ <?= number_format(@$dados["valorBoletoPago"], 2, ",", ".") ?>
        </h3>
        <span class="winpay-card-meta">
          <?= number_format($shareBoleto, 2, ",", ".") ?>% - <span class="text-muted">Total Boletos</span>
        </span>
      </div>
      <hr class="winpay-card-divider">
      <div class="winpay-card-footer">
        <div class="winpay-metric" data-tone="bright">
          <div class="winpay-metric-content">
            <div class="winpay-metric-body">
              <span class="winpay-metric-label">Aprovados</span>
              <span class="winpay-metric-value"><?= number_format($boletoAprovadoPct, 2, ",", ".") ?>%</span>
            </div>
          </div>
        </div>
        <div class="winpay-metric" data-tone="calm">
          <div class="winpay-metric-content">
            <div class="winpay-metric-body">
              <span class="winpay-metric-label">Vencidos</span>
              <span class="winpay-metric-value"><?= number_format($boletoVencidoPct, 2, ",", ".") ?>%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-bolt"></i>
        </span>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Pix</span>
        <h3 class="winpay-card-value">
          R$ <?= number_format(@$dados["valorPixPago"], 2, ",", ".") ?>
        </h3>
        <span class="winpay-card-meta">
          <?= number_format($sharePix, 2, ",", ".") ?>% - <span class="text-muted">Total Pix</span>
        </span>
      </div>
      <hr class="winpay-card-divider">
      <div class="winpay-card-footer">
        <div class="winpay-metric" data-tone="bright">
          <div class="winpay-metric-content">
            <div class="winpay-metric-body">
              <span class="winpay-metric-label">Aprovados</span>
              <span class="winpay-metric-value"><?= number_format($pixAprovadoPct, 2, ",", ".") ?>%</span>
            </div>
          </div>
        </div>
        <div class="winpay-metric" data-tone="calm">
          <div class="winpay-metric-content">
            <div class="winpay-metric-body">
              <span class="winpay-metric-label">Cancelados</span>
              <span class="winpay-metric-value"><?= number_format($pixCanceladoPct, 2, ",", ".") ?>%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- LINHA 3
<div class="row winpay-dashboard-row">
  <div class="col-md-4 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-cart-arrow-down"></i>
        </span>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Tentativas de compras</span>
        <h3 class="winpay-card-value">R$ <?= formataNumero($valorPendente) ?></h3>
        <span class="winpay-card-meta">Total: <?= $pendenteVendas ?> | Ticket: R$ <?= formataNumero($ticketPendente) ?></span>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-exchange-alt"></i>
        </span>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Estornos e Chargebacks</span>
        <span class="winpay-card-meta">Estornos: <?= $estornoVendas ?> | R$ <?= formataNumero($valorEstorno) ?></span>
        <span class="winpay-card-meta">Chargebacks: <?= $chargebackVendas ?> | R$ <?= formataNumero($valorChargeback) ?></span>
      </div>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="winpay-card h-100">
      <div class="winpay-card-header">
        <span class="icon-wrapper">
          <i class="fas fa-user-slash"></i>
        </span>
      </div>
      <div class="winpay-card-content">
        <span class="winpay-card-title">Abandono de checkout</span>
        <span class="winpay-card-meta">Total: <?= $abandonadoVendas ?></span>
      </div>
    </div>
  </div>
</div>
-->

<script>
  document.querySelectorAll('.winpay-dashboard .toggle-visibility').forEach(function(button) {
    var targetSelector = button.getAttribute('data-target');
    var valueElement = document.querySelector(targetSelector);

    if (!valueElement) {
      return;
    }

    var hideIcon = '<i class="fas fa-eye-slash"></i>';
    var showIcon = '<i class="fas fa-eye"></i>';

    var setState = function(hidden) {
      if (hidden) {
        valueElement.classList.add('hidden-value');
        button.innerHTML = hideIcon;
        button.setAttribute('aria-label', 'Mostrar valor');
        button.setAttribute('aria-pressed', 'false');
      } else {
        valueElement.classList.remove('hidden-value');
        button.innerHTML = showIcon;
        button.setAttribute('aria-label', 'Ocultar valor');
        button.setAttribute('aria-pressed', 'true');
      }
    };

    button.addEventListener('click', function() {
      var isHidden = valueElement.classList.contains('hidden-value');
      setState(!isHidden);
    });

    button.setAttribute('type', 'button');
    if (targetSelector) {
      var controlId = targetSelector.charAt(0) === '#' ? targetSelector.substring(1) : targetSelector;
      button.setAttribute('aria-controls', controlId);
    }

    setState(valueElement.classList.contains('hidden-value'));
  });
</script>
</div>

<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1) {
    header("Location: " . URL_BASE);
    exit;
}

$gws = [
    'pagarme'  => 'PagarMe',
    'stripe'   => 'Stripe',
    'efibank'  => 'EfiBank (Efí Pay)',
    'sharkhub' => 'SharkHub',
];

function gwSelect(string $name, string $current, array $gws, string $style = ''): string {
    $html = "<select name=\"{$name}\" class=\"form-control\" style=\"max-width:260px;font-family:inter;{$style}\">";
    foreach ($gws as $val => $label) {
        $sel = $current === $val ? 'selected' : '';
        $html .= "<option value=\"{$val}\" {$sel}>{$label}</option>";
    }
    $html .= "</select>";
    return $html;
}

$modo          = $config->modo             ?? 'dedicado';
$gwDedicado    = $config->gateway_dedicado  ?? 'pagarme';
$gwPrincipal   = $config->gateway_principal ?? 'pagarme';
$gwPix         = $config->gateway_pix       ?? 'pagarme';
$gwCartao      = $config->gateway_cartao    ?? 'pagarme';
$gwBoleto      = $config->gateway_boleto    ?? 'pagarme';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4 style="font-family:inter">Configuração de Gateway</h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item">Gateway</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">

    <?php if (!empty($_GET["msg"])) : ?>
      <div class="alert <?= $_GET["classCss"] ?? "bg-info" ?> alert-dismissible mx-3">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= htmlspecialchars($_GET["msg"]) ?>
      </div>
    <?php endif; ?>

    <div class="card mx-3">
      <div class="card-header">
        <h5 style="font-family:inter;margin:0">Modo de operação</h5>
      </div>
      <div class="card-body">

        <form action="<?= url("gateway/salvar") ?>" method="POST">

          <!-- SELEÇÃO DE MODO -->
          <div class="form-group mb-4">
            <label style="font-family:inter;font-weight:600">Selecione o modo</label>

            <div class="row mt-2">

              <!-- DEDICADO -->
              <div class="col-md-4 mb-3">
                <label class="card border p-3 w-100 cursor-pointer modo-card <?= $modo === 'dedicado' ? 'border-primary' : '' ?>"
                  for="modoDedicado" style="cursor:pointer">
                  <div class="d-flex align-items-center mb-2">
                    <input class="mr-2" type="radio" name="modo" id="modoDedicado" value="dedicado"
                      <?= $modo === 'dedicado' ? 'checked' : '' ?>>
                    <strong style="font-family:inter">Dedicado</strong>
                  </div>
                  <p class="text-muted mb-0" style="font-family:inter;font-size:12px">
                    Um único gateway processa todos os tipos de cobrança.
                  </p>
                </label>
              </div>

              <!-- BALANCEADO -->
              <div class="col-md-4 mb-3">
                <label class="card border p-3 w-100 cursor-pointer modo-card <?= $modo === 'balanceado' ? 'border-primary' : '' ?>"
                  for="modoBalanceado" style="cursor:pointer">
                  <div class="d-flex align-items-center mb-2">
                    <input class="mr-2" type="radio" name="modo" id="modoBalanceado" value="balanceado"
                      <?= $modo === 'balanceado' ? 'checked' : '' ?>>
                    <strong style="font-family:inter">Balanceado</strong>
                  </div>
                  <p class="text-muted mb-0" style="font-family:inter;font-size:12px">
                    Tenta o gateway principal; se falhar, usa o secundário automaticamente.
                  </p>
                </label>
              </div>

              <!-- PERSONALIZADO -->
              <div class="col-md-4 mb-3">
                <label class="card border p-3 w-100 cursor-pointer modo-card <?= $modo === 'personalizado' ? 'border-primary' : '' ?>"
                  for="modoPersonalizado" style="cursor:pointer">
                  <div class="d-flex align-items-center mb-2">
                    <input class="mr-2" type="radio" name="modo" id="modoPersonalizado" value="personalizado"
                      <?= $modo === 'personalizado' ? 'checked' : '' ?>>
                    <strong style="font-family:inter">Personalizado</strong>
                  </div>
                  <p class="text-muted mb-0" style="font-family:inter;font-size:12px">
                    Escolha um gateway diferente para cada tipo de cobrança.
                  </p>
                </label>
              </div>

            </div>
          </div>

          <hr>

          <!-- SEÇÃO: DEDICADO -->
          <div id="secaoDedicado" class="gateway-secao">
            <div class="form-group">
              <label style="font-family:inter;font-weight:600">Gateway ativo</label>
              <?= gwSelect('gateway_dedicado', $gwDedicado, $gws) ?>
            </div>
          </div>

          <!-- SEÇÃO: BALANCEADO -->
          <div id="secaoBalanceado" class="gateway-secao" style="display:none">
            <div class="form-group">
              <label style="font-family:inter;font-weight:600">Gateway principal</label>
              <p class="text-muted" style="font-family:inter;font-size:13px">
                O secundário será acionado automaticamente caso o principal falhe.
              </p>
              <select name="gateway_principal" class="form-control" style="max-width:300px;font-family:inter">
                <option value="pagarme"  <?= $gwPrincipal === 'pagarme'  ? 'selected' : '' ?>>PagarMe</option>
                <option value="stripe"   <?= $gwPrincipal === 'stripe'   ? 'selected' : '' ?>>Stripe</option>
                <option value="efibank"  <?= $gwPrincipal === 'efibank'  ? 'selected' : '' ?>>EfiBank (Efí Pay)</option>
                <option value="sharkhub" <?= $gwPrincipal === 'sharkhub' ? 'selected' : '' ?>>SharkHub</option>
              </select>
            </div>
          </div>

          <!-- SEÇÃO: PERSONALIZADO -->
          <div id="secaoPersonalizado" class="gateway-secao" style="display:none">
            <p class="text-muted" style="font-family:inter;font-size:13px">
              Defina qual gateway será usado em cada tipo de cobrança. Sem fallback automático.
            </p>
            <table class="table" style="max-width:480px;font-family:inter">
              <thead>
                <tr>
                  <th style="font-weight:600">Tipo de cobrança</th>
                  <th style="font-weight:600">Gateway</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="vertical-align:middle">
                    <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;margin-right:6px">qr_code_2</span>
                    Pix
                  </td>
                  <td><?= gwSelect('gateway_pix', $gwPix, $gws) ?></td>
                </tr>
                <tr>
                  <td style="vertical-align:middle">
                    <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;margin-right:6px">credit_card</span>
                    Cartão de crédito
                  </td>
                  <td><?= gwSelect('gateway_cartao', $gwCartao, $gws) ?></td>
                </tr>
                <tr>
                  <td style="vertical-align:middle">
                    <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;margin-right:6px">receipt_long</span>
                    Boleto
                  </td>
                  <td><?= gwSelect('gateway_boleto', $gwBoleto, $gws) ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary" style="font-family:inter">Salvar configuração</button>
            <a href="<?= url("gateway/migracao-stripe") ?>" class="btn btn-outline-secondary ml-2" style="font-family:inter">
              Migração Stripe →
            </a>
          </div>

        </form>
      </div>
    </div>

  </section>
</div>

<script>
(function () {
  var radios   = document.querySelectorAll('input[name="modo"]');
  var secoes   = {
    dedicado:     document.getElementById('secaoDedicado'),
    balanceado:   document.getElementById('secaoBalanceado'),
    personalizado:document.getElementById('secaoPersonalizado'),
  };
  var cards = document.querySelectorAll('.modo-card');

  function atualizar() {
    var val = document.querySelector('input[name="modo"]:checked').value;

    Object.keys(secoes).forEach(function (k) {
      secoes[k].style.display = k === val ? 'block' : 'none';
    });

    cards.forEach(function (c) { c.classList.remove('border-primary'); });
    var radio = document.querySelector('input[name="modo"][value="' + val + '"]');
    if (radio) radio.closest('.modo-card').classList.add('border-primary');
  }

  radios.forEach(function (r) { r.addEventListener('change', atualizar); });
  atualizar();
})();
</script>

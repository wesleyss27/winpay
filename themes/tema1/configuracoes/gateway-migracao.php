<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1) {
    header("Location: " . URL_BASE);
    exit;
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4 style="font-family:inter">Migração Stripe — Contas Conectadas</h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= url("gateway") ?>">Gateway</a></li>
            <li class="breadcrumb-item">Migração Stripe</li>
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
        <h5 style="font-family:inter; margin:0">
          Usuários com PagarMe sem conta Stripe
          <span class="badge badge-warning ml-2"><?= count($bancos) ?></span>
        </h5>
      </div>
      <div class="card-body p-0">

        <?php if (empty($bancos)) : ?>
          <div class="p-4 text-center text-muted" style="font-family:inter">
            Todos os usuários já possuem conta Stripe cadastrada.
          </div>
        <?php else : ?>
          <div style="overflow-x:auto">
            <table class="table table-striped" style="font-size:13px; font-family:inter">
              <thead>
                <tr>
                  <th>ID Banco</th>
                  <th>Titular</th>
                  <th>PagarMe Conta</th>
                  <th>Stripe Account</th>
                  <th>Carteira WinPay</th>
                  <th>Ação</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bancos as $banco) : ?>
                  <tr id="linha-<?= $banco->id ?>">
                    <td><?= $banco->id ?></td>
                    <td><?= htmlspecialchars($banco->titular ?? '') ?></td>
                    <td><code><?= htmlspecialchars($banco->pagarme_conta ?? '') ?></code></td>
                    <td id="stripe-id-<?= $banco->id ?>">
                      <?php if (!empty($banco->stripe_account_id)) : ?>
                        <code><?= htmlspecialchars($banco->stripe_account_id) ?></code>
                      <?php else : ?>
                        <span class="text-muted">—</span>
                      <?php endif; ?>
                    </td>
                    <td id="wallet-status-<?= $banco->id ?>">
                      <?php if (!empty($banco->tem_wallet)) : ?>
                        <span class="badge badge-success">✓ Ativa</span>
                      <?php else : ?>
                        <span class="badge badge-secondary">Sem carteira</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (empty($banco->stripe_account_id)) : ?>
                        <button class="btn btn-sm btn-primary"
                          onclick="criarConta(<?= $banco->id ?>)"
                          id="btn-criar-<?= $banco->id ?>">
                          Criar conta Stripe
                        </button>
                      <?php else : ?>
                        <button class="btn btn-sm btn-outline-secondary"
                          onclick="reenviarLink(<?= $banco->id ?>)"
                          id="btn-link-<?= $banco->id ?>">
                          Reenviar onboarding
                        </button>
                      <?php endif; ?>
                      <?php if (empty($banco->tem_wallet)) : ?>
                        <button class="btn btn-sm btn-warning ml-1"
                          onclick="criarWinPay(<?= $banco->id ?>)"
                          id="btn-winpay-<?= $banco->id ?>">
                          Criar carteira WinPay
                        </button>
                      <?php endif; ?>
                      <span id="resultado-<?= $banco->id ?>" class="ml-2" style="font-size:12px"></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

      </div>
    </div>

  </section>
</div>

<script>
function criarConta(bancoId) {
  var btn = document.getElementById('btn-criar-' + bancoId);
  var res = document.getElementById('resultado-' + bancoId);
  btn.disabled = true;
  btn.textContent = 'Criando...';

  fetch('<?= url("gateway/criar-conta-stripe") ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'banco_id=' + bancoId
  })
  .then(r => r.json())
  .then(function(data) {
    if (data.erro) {
      res.innerHTML = '<span class="text-danger">' + data.msg + '</span>';
      btn.disabled = false;
      btn.textContent = 'Criar conta Stripe';
    } else {
      document.getElementById('stripe-id-' + bancoId).innerHTML = '<code>' + data.account_id + '</code>';
      btn.outerHTML = '<button class="btn btn-sm btn-outline-secondary" onclick="reenviarLink(' + bancoId + ')" id="btn-link-' + bancoId + '">Reenviar onboarding</button>';
      if (data.onboarding_url) {
        res.innerHTML = '<a href="' + data.onboarding_url + '" target="_blank" class="text-success">Abrir link</a>';
      } else {
        res.innerHTML = '<span class="text-success">Criado</span>';
      }
    }
  })
  .catch(function(e) {
    res.innerHTML = '<span class="text-danger">Erro de conexão</span>';
    btn.disabled = false;
    btn.textContent = 'Criar conta Stripe';
  });
}

function criarWinPay(bancoId) {
  var btn = document.getElementById('btn-winpay-' + bancoId);
  var res = document.getElementById('resultado-' + bancoId);
  btn.disabled = true;
  btn.textContent = 'Criando...';

  fetch('<?= url("gateway/criar-conta-winpay") ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'banco_id=' + bancoId
  })
  .then(r => r.json())
  .then(function(data) {
    if (data.erro) {
      res.innerHTML = '<span class="text-danger">' + data.msg + '</span>';
      btn.disabled = false;
      btn.textContent = 'Criar carteira WinPay';
    } else {
      document.getElementById('wallet-status-' + bancoId).innerHTML = '<span class="badge badge-success">✓ Ativa</span>';
      btn.remove();
      res.innerHTML = '<span class="text-success">Carteira criada!</span>';
    }
  })
  .catch(function() {
    res.innerHTML = '<span class="text-danger">Erro de conexão</span>';
    btn.disabled = false;
    btn.textContent = 'Criar carteira WinPay';
  });
}

function reenviarLink(bancoId) {
  var btn = document.getElementById('btn-link-' + bancoId);
  var res = document.getElementById('resultado-' + bancoId);
  btn.disabled = true;
  btn.textContent = 'Gerando...';

  fetch('<?= url("gateway/reenviar-onboarding") ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'banco_id=' + bancoId
  })
  .then(r => r.json())
  .then(function(data) {
    btn.disabled = false;
    btn.textContent = 'Reenviar onboarding';
    if (data.erro) {
      res.innerHTML = '<span class="text-danger">' + data.msg + '</span>';
    } else {
      res.innerHTML = '<a href="' + data.onboarding_url + '" target="_blank" class="text-success">Novo link</a>';
    }
  })
  .catch(function() {
    btn.disabled = false;
    btn.textContent = 'Reenviar onboarding';
    res.innerHTML = '<span class="text-danger">Erro de conexão</span>';
  });
}
</script>

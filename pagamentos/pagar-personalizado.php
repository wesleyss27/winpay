<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? "Pagamento PIX | WinPay" ?></title>
  <link rel="icon" href="<?= url("themes/imgs/logos/logo-icon.png") ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: #0d0d0d;
      color: #f0f0f0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    /* ── Logo ── */
    .logo {
      font-size: 1.6rem;
      font-weight: 800;
      letter-spacing: 3px;
      text-transform: uppercase;
      margin-bottom: 2rem;
      text-align: center;
    }
    .logo span { color: #00C853; }

    /* ── Card principal ── */
    .card {
      background: #1E1E1E;
      border-radius: 20px;
      width: 100%;
      max-width: 460px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(0,0,0,.55);
    }

    /* ── Header do card ── */
    .card-header {
      background: linear-gradient(135deg, #121212, #1a1a1a);
      border-bottom: 1px solid rgba(0,200,83,.2);
      padding: 1.5rem 1.75rem;
      text-align: center;
    }
    .card-header h1 {
      font-size: 1.05rem;
      font-weight: 600;
      color: #e0e0e0;
      margin-bottom: .25rem;
    }
    .card-header .total-label {
      font-size: .75rem;
      color: #777;
      text-transform: uppercase;
      letter-spacing: .08rem;
    }
    .card-header .total-value {
      font-size: 2rem;
      font-weight: 800;
      color: #00C853;
      margin-top: .35rem;
    }

    /* ── Itens ── */
    .itens-section {
      padding: 1.25rem 1.75rem 0;
    }
    .itens-label {
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: .08rem;
      color: #555;
      margin-bottom: .75rem;
    }
    .item-row {
      font-size: .88rem;
      padding: .45rem 0;
      border-bottom: 1px solid rgba(255,255,255,.05);
    }
    .item-row:last-child { border-bottom: none; }
    .item-row::before { content: "•"; color: #00C853; margin-right: .5rem; }
    .item-nome { display: inline; color: #ccc; }
    .item-plano { color: #555; font-size: .78rem; }

    /* ── Divisor ── */
    .divider {
      height: 1px;
      background: rgba(255,255,255,.06);
      margin: 1.25rem 1.75rem;
    }

    /* ── Seção QR ── */
    .qr-section {
      padding: 0 1.75rem 1.25rem;
      text-align: center;
    }
    .qr-title {
      font-size: .8rem;
      color: #888;
      margin-bottom: 1rem;
    }
    .qr-box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      border-radius: 16px;
      padding: 12px;
      margin-bottom: 1.25rem;
    }
    .qr-box img { display: block; width: 190px; height: 190px; }
    .qr-placeholder {
      width: 190px;
      height: 190px;
      background: #2A2A2A;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #444;
      font-size: .8rem;
    }

    /* ── Copia e cola ── */
    .pix-label {
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: .08rem;
      color: #555;
      text-align: left;
      margin-bottom: .5rem;
    }
    .pix-input-wrap {
      display: flex;
      background: #2A2A2A;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 1rem;
    }
    .pix-code {
      flex: 1;
      background: transparent;
      border: none;
      color: #ccc;
      font-size: .75rem;
      font-family: monospace;
      padding: .8rem 1rem;
      resize: none;
      height: 72px;
      outline: none;
    }

    /* ── Botão copiar ── */
    .btn-copiar {
      width: 100%;
      padding: .95rem;
      background: linear-gradient(90deg, #00C853, #64DD17);
      color: #000;
      font-weight: 700;
      font-size: .95rem;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: opacity .2s, transform .1s;
      letter-spacing: .04rem;
    }
    .btn-copiar:hover { opacity: .92; }
    .btn-copiar:active { transform: scale(.98); }
    .btn-copiar.copiado {
      background: linear-gradient(90deg, #00897B, #26A69A);
    }

    /* ── Status banner ── */
    .status-banner {
      margin: 1.25rem 1.75rem;
      padding: 1rem 1.25rem;
      border-radius: 12px;
      font-size: .88rem;
      display: flex;
      align-items: center;
      gap: .6rem;
      display: none;
    }
    .status-banner.pendente {
      background: rgba(0,200,83,.08);
      border: 1px solid rgba(0,200,83,.25);
      color: #69f0ae;
      display: flex;
    }
    .status-banner.pago {
      background: rgba(0,200,83,.15);
      border: 1px solid rgba(0,200,83,.5);
      color: #00E676;
      display: flex;
      font-weight: 700;
    }
    .status-banner.cancelado {
      background: rgba(239,68,68,.1);
      border: 1px solid rgba(239,68,68,.3);
      color: #f87171;
      display: flex;
    }
    .status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: currentColor;
      flex-shrink: 0;
      animation: pulse 1.5s infinite;
    }
    .status-banner.pago .status-dot,
    .status-banner.cancelado .status-dot { animation: none; }
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: .3; }
    }

    /* ── Footer ── */
    .card-footer {
      padding: 1rem 1.75rem 1.5rem;
      text-align: center;
      font-size: .72rem;
      color: #444;
      border-top: 1px solid rgba(255,255,255,.04);
    }
    .card-footer svg { vertical-align: middle; margin-right: 4px; }

    /* ── Pago state ── */
    .pago-state { padding: 2rem 1.75rem; text-align: center; }
    .pago-icon { font-size: 3.5rem; margin-bottom: .75rem; }
    .pago-title { font-size: 1.3rem; font-weight: 700; color: #00E676; margin-bottom: .5rem; }
    .pago-sub { font-size: .88rem; color: #777; }

    /* ── Cancelado state ── */
    .cancelado-state { padding: 2rem 1.75rem; text-align: center; }
    .cancelado-icon { font-size: 3rem; margin-bottom: .75rem; }
    .cancelado-title { font-size: 1.1rem; font-weight: 700; color: #f87171; margin-bottom: .5rem; }
    .cancelado-sub { font-size: .85rem; color: #666; }

    /* ── Segurança ── */
    .seguranca {
      margin-top: 1.5rem;
      font-size: .72rem;
      color: #444;
      display: flex;
      align-items: center;
      gap: .4rem;
      justify-content: center;
    }
  </style>
</head>
<body>

  <div class="logo">WIN<span>PAY</span></div>

  <div class="card">

    <?php if ((int)$venda->status_transacao === 4) : ?>
      <!-- PAGO -->
      <div class="pago-state">
        <div class="pago-icon">✅</div>
        <div class="pago-title">Pagamento confirmado!</div>
        <div class="pago-sub">Seu pagamento foi recebido com sucesso.<br>Obrigado!</div>
      </div>

    <?php elseif ((int)$venda->status_transacao === 5) : ?>
      <!-- CANCELADO -->
      <div class="cancelado-state">
        <div class="cancelado-icon">❌</div>
        <div class="cancelado-title">Pagamento cancelado</div>
        <div class="cancelado-sub">Este link expirou ou foi cancelado.<br>Entre em contato com o vendedor.</div>
      </div>

    <?php else : ?>
      <!-- PENDENTE -->
      <div class="card-header">
        <?php if (!empty($nome_cliente)) : ?>
          <div style="font-size:.75rem; color:#555; text-transform:uppercase; letter-spacing:.08rem; margin-bottom:.5rem;">Olá,</div>
          <div style="font-size:1rem; font-weight:700; color:#e0e0e0; margin-bottom:1rem;"><?= htmlspecialchars($nome_cliente) ?></div>
        <?php endif ?>
        <h1>Pagamento via PIX</h1>
        <div class="total-label">Valor total</div>
        <div class="total-value">R$ <?= number_format($venda->valor_bruto, 2, ",", ".") ?></div>
      </div>

      <?php if (!empty($itens)) : ?>
        <div class="itens-section">
          <div class="itens-label">Itens incluídos</div>
          <?php foreach ($itens as $item) : ?>
            <div class="item-row">
              <div class="item-nome">
                <?= htmlspecialchars($item->nome_produto) ?>
                <?php if ($item->nome_plano) : ?>
                  <span class="item-plano"> — <?= htmlspecialchars($item->nome_plano) ?></span>
                <?php endif ?>
              </div>
            </div>
          <?php endforeach ?>
        </div>
        <div class="divider"></div>
      <?php endif ?>

      <div class="qr-section">
        <div class="qr-title">Escaneie o QR Code com o app do seu banco</div>

        <div class="qr-box">
          <?php if (!empty($qr_code)) : ?>
            <div id="qrcode"></div>
          <?php else : ?>
            <div class="qr-placeholder">QR indisponível</div>
          <?php endif ?>
        </div>

        <?php if (!empty($qr_code)) : ?>
          <div class="pix-label">Ou copie o código PIX</div>
          <div class="pix-input-wrap">
            <textarea class="pix-code" id="pixCode" readonly><?= htmlspecialchars($qr_code) ?></textarea>
          </div>
          <button class="btn-copiar" id="btnCopiar" onclick="copiarPix()">
            Copiar código PIX
          </button>
        <?php endif ?>
      </div>

      <div class="status-banner pendente" id="statusBanner">
        <div class="status-dot"></div>
        Aguardando pagamento… a página atualiza automaticamente
      </div>

      <div class="card-footer">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Pagamento seguro processado pela WinPay
      </div>

    <?php endif ?>

  </div>

  <div class="seguranca">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    Ambiente seguro · Dados criptografados
  </div>

  <?php if ((int)$venda->status_transacao === 1) : ?>
  <script src="<?= url("pagamentos/js/qrcode.min.js") ?>"></script>
  <script>
    // Gera QR Code via lib local
    (function () {
      var pixText = <?= json_encode($qr_code) ?>;
      if (pixText && document.getElementById("qrcode")) {
        new QRCode(document.getElementById("qrcode"), {
          text: pixText,
          width: 190,
          height: 190,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.M
        });
      }
    })();

    // Polling de status
    (function () {
      var uuid = "<?= htmlspecialchars($link_publico) ?>";
      var url  = "<?= url("atendente/consultar-pix") ?>";

      function verificar() {
        var fd = new FormData();
        fd.append("link_publico", uuid);
        fetch(url, { method: "POST", body: fd })
          .then(function(r) { return r.json(); })
          .then(function(res) { if (res.status === 4) location.reload(); })
          .catch(function() {});
      }

      setInterval(verificar, 8000);
    })();

    function copiarPix() {
      var code = document.getElementById("pixCode").value;
      var btn  = document.getElementById("btnCopiar");
      navigator.clipboard.writeText(code).then(function() {
        btn.textContent = "✅  Copiado!";
        btn.classList.add("copiado");
        setTimeout(function() {
          btn.textContent = "Copiar código PIX";
          btn.classList.remove("copiado");
        }, 3000);
      });
    }
  </script>
  <?php endif ?>

</body>
</html>

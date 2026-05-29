<?php $v->layout("_theme"); ?>

<?= $v->start("scripts"); ?>
<style>
  body { background:#0d0d0d; }
  .pagar-wrap {
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:2rem 1rem;
    font-family:'Inter','Montserrat',sans-serif;
  }
  .pagar-card {
    background:#111;
    border:1px solid rgba(217,192,0,.25);
    border-radius:20px;
    max-width:460px;
    width:100%;
    padding:2rem;
    color:#f5f5f5;
    box-shadow:0 8px 40px rgba(0,0,0,.5);
  }
  .pagar-logo { text-align:center; margin-bottom:1.5rem; font-size:1.4rem; font-weight:800; letter-spacing:2px; }
  .pagar-logo .win  { color:#fff; }
  .pagar-logo .pay  { color:rgb(217,192,0); }

  .pagar-title { font-size:1.1rem; font-weight:700; text-align:center; margin-bottom:1.5rem; }

  .pagar-itens { margin-bottom:1.5rem; }
  .pagar-item  { display:flex; justify-content:space-between; font-size:.88rem; padding:.45rem 0; border-bottom:1px solid rgba(255,255,255,.06); }
  .pagar-item:last-child { border-bottom:none; }
  .pagar-item-nome  { color:rgba(255,255,255,.75); }
  .pagar-item-valor { color:#f4d03f; font-weight:600; }

  .pagar-total { display:flex; justify-content:space-between; font-size:1.1rem; font-weight:700; padding:.75rem 0; border-top:1px solid rgba(217,192,0,.3); margin-bottom:1.75rem; }
  .pagar-total-valor { color:#34d399; font-size:1.3rem; }

  /* QR */
  .qr-section { text-align:center; margin-bottom:1.5rem; }
  .qr-section img { max-width:200px; border-radius:12px; background:#fff; padding:8px; }
  .qr-placeholder { width:200px; height:200px; background:#1a1a1a; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; color:rgba(255,255,255,.3); font-size:.8rem; margin:auto; }

  /* Copia e cola */
  .copiacola-label { font-size:.72rem; text-transform:uppercase; letter-spacing:.08rem; color:rgba(255,255,255,.45); margin-bottom:.5rem; }
  .copiacola-wrap { position:relative; }
  .copiacola-input { width:100%; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#f5f5f5; border-radius:10px; padding:.65rem .9rem; font-size:.75rem; word-break:break-all; resize:none; height:80px; font-family:monospace; }
  .copiacola-btn { width:100%; margin-top:.75rem; padding:.85rem; background:linear-gradient(135deg,rgb(217,192,0),#f4d03f); color:#111; font-weight:700; border:none; border-radius:10px; cursor:pointer; font-size:.95rem; transition:opacity .2s; }
  .copiacola-btn:hover { opacity:.9; }

  /* Status */
  .status-banner {
    text-align:center;
    padding:1.25rem;
    border-radius:12px;
    margin-top:1.5rem;
    font-weight:700;
    font-size:1rem;
    display:none;
  }
  .status-banner.pago     { background:rgba(5,150,105,.2); color:#34d399; border:1px solid rgba(5,150,105,.4); display:block; }
  .status-banner.pendente { background:rgba(217,192,0,.1); color:#f4d03f; border:1px solid rgba(217,192,0,.3); display:block; font-weight:400; font-size:.85rem; }
  .status-banner.cancelado{ background:rgba(239,68,68,.1); color:#f87171; border:1px solid rgba(239,68,68,.3); display:block; }

  .pagar-inst { font-size:.8rem; color:rgba(255,255,255,.4); text-align:center; margin-top:1rem; line-height:1.6; }
</style>
<?= $v->end(); ?>

<div class="pagar-wrap">
  <div class="pagar-card">

    <div class="pagar-logo"><span class="win">WIN</span><span class="pay">PAY</span></div>

    <?php if ((int)$venda->status_transacao === 4) : ?>
      <!-- Já pago -->
      <div class="status-banner pago">✅ Pagamento confirmado!</div>
      <p style="text-align:center; margin-top:1rem; color:rgba(255,255,255,.6); font-size:.9rem;">Obrigado. Seu pagamento foi recebido com sucesso.</p>

    <?php elseif ((int)$venda->status_transacao === 5) : ?>
      <!-- Cancelado -->
      <div class="status-banner cancelado">❌ Pagamento cancelado ou expirado.</div>
      <p style="text-align:center; margin-top:1rem; color:rgba(255,255,255,.5); font-size:.85rem;">Entre em contato com o vendedor para um novo link.</p>

    <?php else : ?>
      <!-- Pendente: exibe QR -->
      <div class="pagar-title">Escaneie o QR Code PIX para pagar</div>

      <!-- Itens -->
      <?php if (!empty($itens)) : ?>
        <div class="pagar-itens">
          <?php foreach ($itens as $item) : ?>
            <div class="pagar-item">
              <span class="pagar-item-nome">
                <?= htmlspecialchars($item->nome_produto) ?>
                <?php if ($item->nome_plano) : ?>
                  <small style="color:rgba(255,255,255,.4);">(<?= htmlspecialchars($item->nome_plano) ?>)</small>
                <?php endif ?>
              </span>
              <span class="pagar-item-valor">R$ <?= number_format($item->valor_proporcional, 2, ",", ".") ?></span>
            </div>
          <?php endforeach ?>
        </div>
      <?php endif ?>

      <div class="pagar-total">
        <span>Total</span>
        <span class="pagar-total-valor">R$ <?= number_format($venda->valor_bruto, 2, ",", ".") ?></span>
      </div>

      <!-- QR Code -->
      <div class="qr-section">
        <?php if (!empty($qr_code)) : ?>
          <?php
          // Gera QR code via Google Charts API (URL pública segura)
          $qrUrl = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($qr_code) . "&choe=UTF-8";
          ?>
          <img src="<?= $qrUrl ?>" alt="QR Code PIX" id="qrImg">
        <?php else : ?>
          <div class="qr-placeholder">QR indisponível</div>
        <?php endif ?>
      </div>

      <!-- Copia e cola -->
      <?php if (!empty($qr_code)) : ?>
        <div class="copiacola-label">Ou copie o código PIX</div>
        <div class="copiacola-wrap">
          <textarea class="copiacola-input" id="pixCode" readonly><?= htmlspecialchars($qr_code) ?></textarea>
        </div>
        <button class="copiacola-btn" onclick="copiarPix()" id="btnCopiar">
          Copiar código PIX
        </button>
      <?php endif ?>

      <!-- Status polling -->
      <div class="status-banner pendente" id="statusPendente">
        ⏳ Aguardando pagamento... (a página atualiza automaticamente)
      </div>

      <p class="pagar-inst">O PIX é processado em segundos.<br>Mantenha esta página aberta até a confirmação.</p>

    <?php endif ?>

  </div>
</div>

<?= $v->start("scripts"); ?>
<script>
  <?php if ((int)$venda->status_transacao === 1) : ?>
  (function() {
    const uuid = "<?= htmlspecialchars($link_publico) ?>";
    const url  = "<?= url("atendente/consultar-pix") ?>";

    function verificar() {
      const fd = new FormData();
      fd.append("link_publico", uuid);
      fetch(url, { method:"POST", body:fd })
        .then(r => r.json())
        .then(res => {
          if (res.status === 4) {
            location.reload();
          }
        })
        .catch(() => {});
    }

    setInterval(verificar, 8000); // a cada 8 s
  })();
  <?php endif ?>

  function copiarPix() {
    const code = document.getElementById("pixCode").value;
    navigator.clipboard.writeText(code).then(() => {
      const btn = document.getElementById("btnCopiar");
      btn.textContent = "✅ Copiado!";
      setTimeout(() => { btn.textContent = "Copiar código PIX"; }, 3000);
    });
  }
</script>
<?= $v->end(); ?>

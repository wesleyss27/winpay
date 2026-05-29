<?php $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<style>
.atend-page { background:#000; color:#f5f5f5; min-height:100vh; font-family:'Inter',sans-serif; padding-bottom:4rem; }
.atend-header { padding:2.5rem 1.5rem 1rem; display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem; }
.atend-title { font-size:2rem; font-weight:700; margin:0; }
.atend-subtitle { color:rgba(255,255,255,.6); margin:.4rem 0 0; }
.btn-nova-venda { background:linear-gradient(135deg,rgb(217,192,0),#f4d03f); color:#111; font-weight:700; border:none; border-radius:10px; padding:.65rem 1.4rem; text-decoration:none; font-size:.9rem; }

.mv-card { background:rgba(17,17,20,.85); border:1px solid rgba(255,255,255,.06); border-radius:18px; overflow:hidden; }
.mv-table { width:100%; border-collapse:collapse; font-size:.9rem; }
.mv-table thead { background:rgba(0,0,0,.85); }
.mv-table th { padding:.9rem 1rem; text-transform:uppercase; font-size:.72rem; letter-spacing:.08rem; color:rgba(255,255,255,.55); text-align:left; }
.mv-table tbody tr { border-top:1px solid rgba(255,255,255,.05); transition:background .2s; }
.mv-table tbody tr:hover { background:rgba(255,255,255,.03); }
.mv-table td { padding:.85rem 1rem; vertical-align:top; }

.badge-pago    { background:rgba(5,150,105,.2); color:#34d399; border:1px solid rgba(5,150,105,.4); padding:.2rem .6rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:uppercase; white-space:nowrap; }
.badge-pendente{ background:rgba(217,192,0,.15); color:#f4d03f; border:1px solid rgba(217,192,0,.35); padding:.2rem .6rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:uppercase; white-space:nowrap; }
.badge-cancel  { background:rgba(239,68,68,.15); color:#f87171; border:1px solid rgba(239,68,68,.35); padding:.2rem .6rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:uppercase; white-space:nowrap; }

.itens-list { font-size:.78rem; color:rgba(255,255,255,.55); margin:.3rem 0 0; }
.itens-list li { list-style:none; }

.link-copy { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#f4d03f; border-radius:6px; padding:.25rem .6rem; font-size:.72rem; cursor:pointer; white-space:nowrap; }
.link-copy:hover { background:rgba(217,192,0,.1); }
.vazio-msg { text-align:center; padding:3rem; color:rgba(255,255,255,.35); }
</style>
<?= $v->end(); ?>

<div class="atend-page content-wrapper">
  <div class="container-fluid">
    <div class="atend-header">
      <div>
        <h1 class="atend-title">Minhas Vendas</h1>
        <p class="atend-subtitle">Vendas personalizadas geradas por você — status em tempo real.</p>
      </div>
      <a href="<?= url("atendente/nova-venda") ?>" class="btn-nova-venda">+ Nova Venda</a>
    </div>

    <div class="mv-card">
      <?php if (empty($vendas_itens)) : ?>
        <div class="vazio-msg">
          <span class="material-symbols-rounded" style="font-size:2.5rem; color:rgba(255,255,255,.2);">shopping_bag</span>
          <p style="margin:.75rem 0 0;">Você ainda não criou nenhuma venda personalizada.</p>
          <a href="<?= url("atendente/nova-venda") ?>" class="btn-nova-venda" style="display:inline-block;margin-top:1rem;">Criar primeira venda</a>
        </div>
      <?php else : ?>
        <div style="overflow-x:auto;">
          <table class="mv-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Produtos</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Link PIX</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vendas_itens as $row) : ?>
                <?php
                $venda = $row["venda"];
                $itens = $row["itens"];
                $status = (int)$venda->status_transacao;
                if ($status === 4) {
                    $badge = '<span class="badge-pago">Pago</span>';
                } elseif ($status === 5) {
                    $badge = '<span class="badge-cancel">Cancelado</span>';
                } else {
                    $badge = '<span class="badge-pendente">Pendente</span>';
                }
                $link = url("pagar/{$venda->link_publico}");
                ?>
                <?php $comprador = $venda->dataComprador(); ?>
                <tr id="row-<?= $venda->id ?>">
                  <td style="font-weight:600;">#<?= $venda->id ?></td>
                  <td style="font-size:.8rem; color:rgba(255,255,255,.6);">
                    <?= date("d/m/Y H:i", strtotime($venda->created_at)) ?>
                  </td>
                  <td>
                    <?php if ($comprador) : ?>
                      <div style="font-weight:600; font-size:.88rem;"><?= htmlspecialchars($comprador->nome) ?></div>
                      <?php if ($comprador->email) : ?>
                        <div style="font-size:.75rem; color:rgba(255,255,255,.5);"><?= htmlspecialchars($comprador->email) ?></div>
                      <?php endif ?>
                      <?php if ($comprador->telefone) : ?>
                        <div style="font-size:.75rem; color:rgba(255,255,255,.4);">
                          <a href="https://api.whatsapp.com/send?phone=55<?= preg_replace('/\D/','',$comprador->telefone) ?>" target="_blank" style="color:#25d366;">
                            <?= htmlspecialchars($comprador->telefone) ?>
                          </a>
                        </div>
                      <?php endif ?>
                    <?php else : ?>
                      <span style="color:rgba(255,255,255,.3); font-size:.8rem;">—</span>
                    <?php endif ?>
                  </td>
                  <td>
                    <ul class="itens-list">
                      <?php foreach ($itens as $item) : ?>
                        <li>• <?= htmlspecialchars($item->nome_produto) ?>
                          <?php if ($item->nome_plano) : ?>
                            <small>(<?= htmlspecialchars($item->nome_plano) ?>)</small>
                          <?php endif ?>
                          — <span style="color:#f4d03f;">R$ <?= number_format($item->valor_proporcional, 2, ",", ".") ?></span>
                        </li>
                      <?php endforeach ?>
                    </ul>
                  </td>
                  <td style="font-weight:700; color:#059669; white-space:nowrap;">
                    R$ <?= number_format($venda->valor_bruto, 2, ",", ".") ?>
                  </td>
                  <td class="status-cell-<?= $venda->id ?>"><?= $badge ?></td>
                  <td>
                    <?php if ($venda->link_publico) : ?>
                      <button class="link-copy" onclick="copiarLink('<?= $link ?>', this)">Copiar link</button>
                      <a href="<?= $link ?>" target="_blank" style="color:#f4d03f; font-size:.75rem; display:block; margin-top:.3rem;">Abrir →</a>
                    <?php endif ?>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      <?php endif ?>
    </div>
  </div>
</div>

<?= $v->start("scripts"); ?>
<script>
// Polling automático para atualizar status das vendas pendentes
(function() {
  const pendentes = [];
  document.querySelectorAll(".badge-pendente").forEach(el => {
    const row = el.closest("tr");
    if (row) {
      const id = row.id.replace("row-", "");
      // Pega o link_publico do botão copiar
      const btn = row.querySelector(".link-copy");
      if (btn) {
        const link = btn.getAttribute("onclick").match(/'([a-f0-9]+)'/);
        // extrai uuid do href
        const aTag = row.querySelector("a[href*='pagar/']");
        if (aTag) {
          const parts = aTag.href.split("/pagar/");
          if (parts[1]) pendentes.push({ id, uuid: parts[1] });
        }
      }
    }
  });

  if (!pendentes.length) return;

  function verificar() {
    pendentes.forEach(p => {
      const fd = new FormData();
      fd.append("link_publico", p.uuid);
      fetch("<?= url("atendente/consultar-pix") ?>", { method:"POST", body:fd })
        .then(r => r.json())
        .then(res => {
          if (res.status === 4) {
            const cell = document.querySelector(".status-cell-" + p.id);
            if (cell) cell.innerHTML = '<span class="badge-pago">Pago</span>';
          } else if (res.status === 5) {
            const cell = document.querySelector(".status-cell-" + p.id);
            if (cell) cell.innerHTML = '<span class="badge-cancel">Cancelado</span>';
          }
        })
        .catch(() => {});
    });
  }

  setInterval(verificar, 15000); // a cada 15 s
})();

function copiarLink(link, btn) {
  navigator.clipboard.writeText(link).then(() => {
    const orig = btn.textContent;
    btn.textContent = "Copiado!";
    setTimeout(() => { btn.textContent = orig; }, 2000);
  });
}
</script>
<?= $v->end(); ?>

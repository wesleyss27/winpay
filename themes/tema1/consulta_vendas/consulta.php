<?php
// Maps definidos no topo para uso tanto na tabela quanto nos modais
$statusVenda = [
  1  => ['txt' => 'Aguardando',    'cls' => 'st-aguardando'],
  2  => ['txt' => 'Em análise',    'cls' => 'st-analise'],
  3  => ['txt' => 'Pré-autorizada','cls' => 'st-pre'],
  4  => ['txt' => 'Paga',          'cls' => 'st-paga'],
  5  => ['txt' => 'Cancelada',     'cls' => 'st-cancelada'],
  6  => ['txt' => 'Devolvida',     'cls' => 'st-devolvida'],
  7  => ['txt' => 'Chargeback',    'cls' => 'st-chargeback'],
  8  => ['txt' => 'Concluída',     'cls' => 'st-concluida'],
  9  => ['txt' => 'Bloqueada',     'cls' => 'st-bloqueada'],
  10 => ['txt' => 'Antifraude',    'cls' => 'st-antifraude'],
];
$pgtoMap = [
  1 => ['txt' => 'Cartão', 'icon' => 'fas fa-credit-card', 'cls' => 'cartao'],
  2 => ['txt' => 'Boleto', 'icon' => 'fas fa-barcode',     'cls' => 'boleto'],
  3 => ['txt' => 'Pix',    'icon' => 'fas fa-qrcode',      'cls' => 'pix'],
];
$rebStatusMap = [
  0 => ['txt' => 'Pendente',    'cls' => 'pendente',    'icon' => 'fas fa-clock',          'bannerIcon' => '⏳', 'bannerCls' => 'pendente'],
  1 => ['txt' => 'Aprovado',    'cls' => 'aprovado',    'icon' => 'fas fa-thumbs-up',       'bannerIcon' => '👍', 'bannerCls' => 'aprovado'],
  2 => ['txt' => 'Recusado',    'cls' => 'recusado',    'icon' => 'fas fa-times-circle',    'bannerIcon' => '❌', 'bannerCls' => 'recusado'],
  3 => ['txt' => 'Processando', 'cls' => 'processando', 'icon' => 'fas fa-spinner fa-spin', 'bannerIcon' => '⚙️', 'bannerCls' => 'processando'],
  4 => ['txt' => 'Concluído',   'cls' => 'concluido',   'icon' => 'fas fa-check-circle',    'bannerIcon' => '✅', 'bannerCls' => 'concluido'],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Consultar Venda – WinPay</title>
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/adminlte.min.css") ?>">
  <link rel="icon" href="<?= url("themes/imgs/logos/logo-icon.png") ?>">
  <style>
    :root {
      --gold: #f5a623;
      --gold-dark: #d4881a;
      --gold-light: #fff8ee;
      --bg: #f0f2f5;
      --text: #1a1a2e;
      --text-muted: #8a8fa8;
      --border: #e8eaf0;
      --radius: 16px;
      --shadow: 0 4px 24px rgba(0,0,0,.07);
    }
    * { box-sizing: border-box; }
    body {
      background: var(--bg);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: var(--text);
      min-height: 100vh;
    }

    /* ── Hero ── */
    .hero {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
      padding: 56px 16px 80px;
      text-align: center;
      position: relative;
    }
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse 60% 80% at 50% 120%, rgba(245,166,35,.18) 0%, transparent 70%);
      pointer-events: none;
    }
    .hero-logo { height: 52px; margin-bottom: 12px; filter: drop-shadow(0 2px 8px rgba(0,0,0,.4)); }
    .hero-subtitle { color: rgba(255,255,255,.6); font-size: 13px; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 32px; }

    /* ── Search Box ── */
    .search-wrap { max-width: 640px; margin: 0 auto; }
    .search-box {
      display: flex; background: #fff;
      border-radius: 999px;
      box-shadow: 0 8px 32px rgba(0,0,0,.25);
      overflow: hidden;
      border: 2px solid transparent;
      transition: border-color .2s;
    }
    .search-box:focus-within { border-color: var(--gold); }
    .search-box input {
      flex: 1; border: none; outline: none;
      padding: 14px 22px; font-size: 15px;
      background: transparent; color: var(--text);
    }
    .search-box input::placeholder { color: #aaa; }
    .search-box button {
      background: var(--gold); border: none;
      padding: 0 28px; font-size: 15px; font-weight: 600;
      color: #fff; cursor: pointer; transition: background .2s;
      white-space: nowrap; display: flex; align-items: center; gap: 8px;
    }
    .search-box button:hover { background: var(--gold-dark); }
    .search-hint { color: rgba(255,255,255,.45); font-size: 12px; margin-top: 12px; }

    /* ── Content ──
       position+z-index necessários para o card sobrepor o hero.
       Os modais ficam fora deste container, então o backdrop
       do Bootstrap não é afetado.                                */
    .content-area {
      max-width: 1400px;
      margin: -32px auto 48px;
      padding: 0 20px;
      position: relative;
      z-index: 2;
    }

    /* ── Alert ── */
    .alert-custom {
      background: #fff; border-radius: var(--radius);
      box-shadow: var(--shadow); padding: 20px 24px;
      display: flex; align-items: center; gap: 12px; font-size: 14px;
    }
    .alert-custom .icon { font-size: 20px; }
    .alert-custom.warn .icon { color: var(--gold); }
    .alert-custom.info .icon { color: #4a9eff; }

    /* ── Result Header ── */
    .result-header {
      background: #fff;
      border-radius: var(--radius) var(--radius) 0 0;
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .result-header h2 { font-size: 15px; font-weight: 700; margin: 0; }
    .count-badge {
      background: var(--gold-light); color: var(--gold-dark);
      font-weight: 700; font-size: 13px; padding: 3px 12px; border-radius: 999px;
    }

    /* ── Table ── */
    .result-wrap {
      background: #fff;
      border-radius: 0 0 var(--radius) var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }
    .result-table { width: 100%; border-collapse: collapse; }
    .result-table thead th {
      background: #fafbfc; padding: 9px 10px;
      font-size: 10px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .6px;
      color: var(--text-muted); border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }
    .result-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
    .result-table tbody tr:last-child { border-bottom: none; }
    .result-table tbody tr:hover { background: #fafbff; }
    .result-table td { padding: 10px 10px; font-size: 13px; vertical-align: middle; }

    .tx-code {
      font-family: monospace; font-size: 10px; color: var(--text-muted);
      max-width: 80px; overflow: hidden; text-overflow: ellipsis;
      white-space: nowrap; display: block;
    }
    .buyer-name    { font-weight: 700; font-size: 13px; display: block; }
    .buyer-doc     { font-size: 11px; color: var(--text-muted); }
    .contact-email { font-size: 12px; display: block; word-break: break-all; }
    .contact-phone { font-size: 11px; color: var(--text-muted); }
    .product-name  { font-size: 12px; font-weight: 500; }
    .producer-name { font-size: 12px; color: #444; }

    .pgto-chip {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px;
    }
    .pgto-chip.cartao { background: #eef2ff; color: #4338ca; }
    .pgto-chip.boleto { background: #fff7ed; color: #c2410c; }
    .pgto-chip.pix    { background: #f0fdf4; color: #15803d; }
    .parcelas-tag {
      font-size: 10px; background: #f1f5f9; color: #64748b;
      padding: 1px 6px; border-radius: 4px; margin-left: 4px;
    }

    .valor-txt { font-weight: 700; font-size: 14px; }
    .valor-sym { font-size: 11px; color: var(--text-muted); font-weight: 400; margin-right: 2px; }

    .st-pill {
      display: inline-block; padding: 4px 12px; border-radius: 999px;
      font-size: 11px; font-weight: 700; white-space: nowrap;
    }
    .st-aguardando { background:#fff8e1; color:#b45309; }
    .st-analise    { background:#e0f2fe; color:#0369a1; }
    .st-pre        { background:#f1f5f9; color:#475569; }
    .st-paga       { background:#dcfce7; color:#15803d; }
    .st-cancelada  { background:#fee2e2; color:#b91c1c; }
    .st-devolvida  { background:#fef3c7; color:#92400e; }
    .st-chargeback { background:#fce7f3; color:#9d174d; }
    .st-concluida  { background:#d1fae5; color:#065f46; }
    .st-bloqueada  { background:#f3f4f6; color:#374151; }
    .st-antifraude { background:#ede9fe; color:#5b21b6; }

    /* ── Célula de Reembolso ── */
    .reb-cell {
      display: flex; flex-direction: column; gap: 5px; min-width: 120px;
    }
    .reb-btn {
      display: inline-flex; align-items: center; gap: 7px;
      font-size: 11px; font-weight: 700;
      padding: 5px 11px; border-radius: 7px;
      border: 1.5px solid currentColor;
      cursor: pointer; white-space: nowrap; width: 100%;
      transition: transform .1s, box-shadow .1s;
      box-shadow: 0 1px 4px rgba(0,0,0,.07);
      background: none;
      position: relative;
    }
    .reb-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0,0,0,.13);
    }
    .reb-btn:active { transform: translateY(0); }

    /* dot indicador de status */
    .reb-dot {
      width: 8px; height: 8px; border-radius: 50%;
      flex-shrink: 0; display: inline-block;
    }
    .reb-btn .reb-label { flex: 1; text-align: left; }
    .reb-btn .reb-arrow {
      font-size: 10px; opacity: .5; margin-left: auto;
    }

    .reb-btn.pendente    { background:#fffbeb; color:#92400e; border-color:#fbbf24; }
    .reb-btn.pendente    .reb-dot { background: #f59e0b; }
    .reb-btn.aprovado    { background:#eff6ff; color:#1d4ed8; border-color:#93c5fd; }
    .reb-btn.aprovado    .reb-dot { background: #60a5fa; }
    .reb-btn.recusado    { background:#fff1f2; color:#be123c; border-color:#fca5a5; }
    .reb-btn.recusado    .reb-dot { background: #f87171; }
    .reb-btn.processando { background:#f5f3ff; color:#6d28d9; border-color:#c4b5fd; }
    .reb-btn.processando .reb-dot { background: #a78bfa; }
    .reb-btn.concluido   { background:#f0fdf4; color:#15803d; border-color:#86efac; }
    .reb-btn.concluido   .reb-dot { background: #4ade80; }

    .date-main { font-size: 12px; font-weight: 600; display: block; white-space: nowrap; }
    .date-time { font-size: 11px; color: var(--text-muted); white-space: nowrap; }

    .result-footer {
      padding: 12px 24px; font-size: 12px; color: var(--text-muted);
      border-top: 1px solid var(--border); display: flex; align-items: center; gap: 6px;
    }

    /* ── Modal Reembolso ── */
    .reb-modal .modal-content { border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.18); }
    .reb-modal .modal-header  { border-bottom: 1px solid var(--border); padding: 20px 24px; background: #fff; }
    .reb-modal .modal-title   { font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 8px; }
    .reb-modal .modal-body    { padding: 24px; background: #fafbfc; }
    .reb-modal .modal-footer  { border-top: 1px solid var(--border); background: #fff; }

    .reb-status-banner {
      border-radius: 12px; padding: 16px 20px;
      display: flex; align-items: center; gap: 14px; margin-bottom: 20px;
      border: 1px solid rgba(0,0,0,.06);
    }
    .reb-status-banner .reb-icon { font-size: 28px; line-height: 1; }
    .reb-status-banner .reb-label { font-size: 15px; font-weight: 700; }
    .reb-status-banner .reb-since { font-size: 12px; opacity: .75; margin-top: 2px; }
    .reb-status-banner.pendente    { background:#fffbeb; color:#92400e; }
    .reb-status-banner.aprovado    { background:#eff6ff; color:#1d4ed8; }
    .reb-status-banner.recusado    { background:#fff1f2; color:#be123c; }
    .reb-status-banner.processando { background:#f5f3ff; color:#6d28d9; }
    .reb-status-banner.concluido   { background:#f0fdf4; color:#15803d; }

    .reb-meta { display: flex; gap: 28px; flex-wrap: wrap; margin-bottom: 20px; }
    .reb-meta-item .m-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--text-muted); }
    .reb-meta-item .m-value { font-size: 15px; font-weight: 700; color: var(--text); margin-top: 2px; }

    .reb-section { margin-bottom: 14px; }
    .reb-section-title {
      font-size: 10px; font-weight: 700; text-transform: uppercase;
      letter-spacing: .6px; color: var(--text-muted); margin-bottom: 6px;
      display: flex; align-items: center; gap: 5px;
    }
    .reb-section-body {
      background: #fff; border-radius: 10px;
      padding: 12px 16px; font-size: 13px; line-height: 1.65;
      color: var(--text); white-space: pre-wrap;
      border: 1px solid var(--border);
    }
    .reb-section-body.recusa  { background:#fff1f2; border-color:#fca5a5; border-left: 3px solid #f87171; }
    .reb-section-body.contest { background:#fffbeb; border-color:#fde68a; border-left: 3px solid var(--gold); }

    @media (max-width: 640px) {
      .hero { padding: 36px 16px 64px; }
      .result-table td, .result-table th { padding: 8px 8px; }
      .tx-code { max-width: 80px; }
    }
  </style>
</head>
<body>

<!-- Hero -->
<div class="hero">
  <img
    src="<?= url("themes/imgs/logos/logo4.png") ?>"
    alt="WinPay"
    class="hero-logo"
    onerror="this.style.display='none'"
  >
  <p class="hero-subtitle">Consulta rápida de vendas</p>
  <div class="search-wrap">
    <form method="get" action="<?= url("consultar-venda") ?>" autocomplete="off">
      <div class="search-box">
        <input
          type="text"
          name="q"
          placeholder="CPF, e-mail, nome ou telefone do comprador…"
          value="<?= $termo ?>"
          autofocus
        >
        <button type="submit">
          <i class="fas fa-search"></i> Buscar
        </button>
      </div>
      <p class="search-hint">
        CPF (só números) &nbsp;·&nbsp; e-mail completo &nbsp;·&nbsp; parte do nome &nbsp;·&nbsp; telefone (só números)
      </p>
    </form>
  </div>
</div>

<!-- Resultados -->
<div class="content-area">

  <?php if ($buscou): ?>

    <?php if ($erro): ?>
      <div class="alert-custom warn">
        <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
        <span><?= htmlspecialchars($erro) ?></span>
      </div>

    <?php elseif (empty($vendas)): ?>
      <div class="alert-custom info">
        <span class="icon"><i class="fas fa-search"></i></span>
        <span>Nenhuma venda encontrada para <strong>"<?= $termo ?>"</strong>. Tente outro termo.</span>
      </div>

    <?php else: ?>

      <?php $total = count($vendas); ?>

      <div class="result-header">
        <h2><i class="fas fa-receipt" style="color:var(--gold);margin-right:8px;"></i>Resultados para <em>"<?= $termo ?>"</em></h2>
        <span class="count-badge"><?= $total ?> venda<?= $total !== 1 ? 's' : '' ?></span>
      </div>

      <div class="result-wrap">
        <div style="overflow-x:auto;">
          <table class="result-table">
            <thead>
              <tr>
                <th>Transação</th>
                <th>Comprador</th>
                <th>Contato</th>
                <th>Produto</th>
                <th>Pagamento</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Reembolso</th>
                <th>Data</th>
                <th>Produtor</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vendas as $venda): ?>
                <?php
                  $c = $venda->endereco
                      ? @$venda->dataEndereco()
                      : (@$compradores[$venda->comprador] ?? @$venda->dataComprador());
                  $produto  = @$venda->dataProduto();
                  $produtor = @$produtores[$venda->produtor] ?? null;
                  $rebList  = @$reembolsos[$venda->id] ?? [];
                  $st      = @$statusVenda[$venda->status_transacao] ?? ['txt' => '–', 'cls' => 'st-pre'];
                  $pg      = @$pgtoMap[$venda->forma_pagamento]      ?? ['txt' => '–', 'icon' => 'fas fa-money-bill', 'cls' => ''];
                  $dataObj = $venda->created_at ? date_create($venda->created_at) : null;
                ?>
                <tr>
                  <td>
                    <span class="tx-code" title="<?= htmlspecialchars($venda->transacao ?? $venda->id) ?>">
                      <?= htmlspecialchars($venda->transacao ?? $venda->id) ?>
                    </span>
                  </td>
                  <td>
                    <span class="buyer-name"><?= htmlspecialchars(@$c->nome ?? '–') ?></span>
                    <span class="buyer-doc"><?= htmlspecialchars(@$c->cpf_cnpj ?? '') ?></span>
                  </td>
                  <td>
                    <span class="contact-email"><?= htmlspecialchars(@$c->email ?? '–') ?></span>
                    <span class="contact-phone"><?= htmlspecialchars(@$c->telefone ?? '') ?></span>
                  </td>
                  <td>
                    <?php if ((int)$venda->tipo_venda === 1) : ?>
                      <?php $cvItens = (new Source\Models\Atendente\VendaPersonalizadaItem())->porVenda($venda->id); ?>
                      <?php if ($cvItens) : ?>
                        <?php foreach ($cvItens as $cvI) : ?>
                          <span class="product-name" style="display:block;font-size:12px;">• <?= htmlspecialchars($cvI->nome_produto) ?></span>
                        <?php endforeach ?>
                      <?php else : ?>
                        <span class="product-name"><?= htmlspecialchars(@$produto->nome ?? '–') ?></span>
                      <?php endif ?>
                    <?php else : ?>
                      <span class="product-name"><?= htmlspecialchars(@$produto->nome ?? '–') ?></span>
                    <?php endif ?>
                  </td>
                  <td>
                    <span class="pgto-chip <?= $pg['cls'] ?>">
                      <i class="<?= $pg['icon'] ?>"></i> <?= $pg['txt'] ?>
                    </span>
                    <?php if ($venda->parcelas > 1): ?>
                      <span class="parcelas-tag"><?= $venda->parcelas ?>x</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="valor-txt">
                      <span class="valor-sym">R$</span><?= number_format($venda->valor_bruto ?? 0, 2, ',', '.') ?>
                    </span>
                  </td>
                  <td><span class="st-pill <?= $st['cls'] ?>"><?= $st['txt'] ?></span></td>
                  <td>
                    <?php if (empty($rebList)): ?>
                      <span style="color:#d1d5db;font-size:13px;">—</span>
                    <?php else: ?>
                      <div class="reb-cell">
                        <?php foreach ($rebList as $reb): ?>
                          <?php $rs = @$rebStatusMap[$reb->status] ?? $rebStatusMap[0]; ?>
                          <button
                            class="reb-btn <?= $rs['cls'] ?>"
                            onclick="$('#reb-modal-<?= $reb->id ?>').modal('show')"
                            title="Clique para ver detalhes"
                          >
                            <span class="reb-dot"></span>
                            <span class="reb-label"><?= $rs['txt'] ?></span>
                            <i class="fas fa-chevron-right reb-arrow"></i>
                          </button>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td style="white-space:nowrap;">
                    <?php if ($dataObj): ?>
                      <span class="date-main"><?= date_format($dataObj, 'd/m/Y') ?></span>
                      <span class="date-time"><?= date_format($dataObj, 'H:i') ?></span>
                    <?php else: ?>
                      <span class="date-time">–</span>
                    <?php endif; ?>
                  </td>
                  <td><span class="producer-name"><?= htmlspecialchars(@$produtor->nome ?? (@$produtor->nome_abreviado ?? '–')) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php if ($total === 50): ?>
          <div class="result-footer">
            <i class="fas fa-info-circle"></i>
            Exibindo os 50 resultados mais recentes. Refine a busca para ver resultados específicos.
          </div>
        <?php endif; ?>
      </div>

    <?php endif; ?>
  <?php endif; ?>

</div><!-- /content-area -->

<!-- ══════════════════════════════════════════════════
     Modais de Reembolso
     Fora de qualquer container posicionado para que o
     backdrop do Bootstrap funcione corretamente
═══════════════════════════════════════════════════ -->
<?php if ($buscou && !$erro && !empty($reembolsos)): ?>
  <?php foreach ($reembolsos as $vendaId => $rebList): ?>
    <?php foreach ($rebList as $reb): ?>
      <?php
        $rs           = @$rebStatusMap[$reb->status] ?? $rebStatusMap[0];
        $criadoEm     = $reb->criado_em    ? date('d/m/Y \à\s H:i', strtotime($reb->criado_em))    : '–';
        $atualizadoEm = $reb->atualizado_em? date('d/m/Y H:i',     strtotime($reb->atualizado_em)) : '–';
        $contestadoEm = $reb->contestado_em? date('d/m/Y H:i',     strtotime($reb->contestado_em)) : null;
      ?>
      <div class="modal fade reb-modal" id="reb-modal-<?= $reb->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title">
                <i class="fas fa-undo-alt" style="color:var(--gold);"></i>
                Detalhes do Reembolso <small class="text-muted" style="font-weight:400;">#<?= $reb->id ?></small>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body">

              <!-- Banner de status -->
              <div class="reb-status-banner <?= $rs['bannerCls'] ?>">
                <span class="reb-icon"><?= $rs['bannerIcon'] ?></span>
                <div>
                  <div class="reb-label"><?= $rs['txt'] ?></div>
                  <div class="reb-since">Última atualização: <?= $atualizadoEm ?></div>
                </div>
              </div>

              <!-- Metadados -->
              <div class="reb-meta">
                <div class="reb-meta-item">
                  <div class="m-label">Valor solicitado</div>
                  <div class="m-value">R$ <?= number_format($reb->valor_total ?? 0, 2, ',', '.') ?></div>
                </div>
                <div class="reb-meta-item">
                  <div class="m-label">Solicitado em</div>
                  <div class="m-value" style="font-size:13px;"><?= $criadoEm ?></div>
                </div>
                <?php if ($reb->venda_id): ?>
                <div class="reb-meta-item">
                  <div class="m-label">Venda</div>
                  <div class="m-value" style="font-size:13px;">#<?= $reb->venda_id ?></div>
                </div>
                <?php endif; ?>
              </div>

              <?php if ($reb->motivo): ?>
              <div class="reb-section">
                <div class="reb-section-title"><i class="fas fa-align-left"></i> Motivo (interno)</div>
                <div class="reb-section-body"><?= nl2br(htmlspecialchars($reb->motivo)) ?></div>
              </div>
              <?php endif; ?>

              <?php if ($reb->motivo_cliente): ?>
              <div class="reb-section">
                <div class="reb-section-title"><i class="fas fa-user"></i> Motivo informado pelo comprador</div>
                <div class="reb-section-body"><?= nl2br(htmlspecialchars($reb->motivo_cliente)) ?></div>
              </div>
              <?php endif; ?>

              <?php if ($reb->motivo_recusa): ?>
              <div class="reb-section">
                <div class="reb-section-title" style="color:#be123c;"><i class="fas fa-ban"></i> Motivo da recusa</div>
                <div class="reb-section-body recusa"><?= nl2br(htmlspecialchars($reb->motivo_recusa)) ?></div>
              </div>
              <?php endif; ?>

              <?php if ($reb->contestacao): ?>
              <div class="reb-section">
                <div class="reb-section-title" style="color:#92400e;">
                  <i class="fas fa-comment-alt"></i>
                  Contestação<?= $contestadoEm ? " · $contestadoEm" : '' ?>
                </div>
                <div class="reb-section-body contest"><?= nl2br(htmlspecialchars($reb->contestacao)) ?></div>
              </div>
              <?php endif; ?>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
            </div>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>
<?php endif; ?>

<script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js") ?>"></script>
<script src="<?= url("themes/tema1/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>

</body>
</html>

<?php $v->layout("_theme"); ?>

<?= $v->start("links") ?>
<style>
  /* ── base ─────────────────────────────────────────────────────────── */
  .lote-page { background:#000; color:#f5f5f5; font-family:'Inter',sans-serif; min-height:100vh; padding-bottom:6rem; }

  /* ── header ──────────────────────────────────────────────────────── */
  .lote-header { padding:2.5rem 1.5rem 0; }
  .lote-title  { margin:0; font-size:2rem; font-weight:700; color:#fff; }
  .lote-sub    { margin:.35rem 0 0; color:rgba(255,255,255,.6); font-size:.95rem; }

  /* ── stat cards ──────────────────────────────────────────────────── */
  .lote-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:1rem; margin:2rem 0 1.5rem; }
  .lote-stat  { background:linear-gradient(135deg,rgba(17,17,20,.95),rgba(6,6,8,.9)); border:1px solid rgba(255,255,255,.06); border-radius:16px; padding:1.25rem; display:flex; flex-direction:column; gap:.5rem; transition:transform .25s,box-shadow .25s; cursor:default; }
  .lote-stat:hover { transform:translateY(-3px); box-shadow:0 14px 28px rgba(0,0,0,.4); }
  .lote-stat-icon { width:38px; height:38px; border-radius:10px; display:grid; place-items:center; font-size:1.1rem; flex-shrink:0; }
  .icon-green  { background:rgba(34,197,94,.15); color:#4ade80; }
  .icon-yellow { background:rgba(217,192,0,.15);  color:#d9c000; }
  .icon-blue   { background:rgba(99,102,241,.15);  color:#818cf8; }
  .lote-stat-label { font-size:.72rem; text-transform:uppercase; letter-spacing:.07rem; color:rgba(255,255,255,.5); }
  .lote-stat-value { font-size:1.9rem; font-weight:700; color:#fff; line-height:1; }
  .lote-stat.active-tab .lote-stat-value { color:#d9c000; }

  /* ── period filter ───────────────────────────────────────────────── */
  .lote-filter { background:rgba(17,17,20,.78); border:1px solid rgba(217,192,0,.18); border-radius:16px; padding:1.25rem 1.5rem; display:flex; flex-wrap:wrap; align-items:flex-end; gap:1rem; margin-bottom:1.75rem; }
  .lote-filter label { display:block; font-size:.72rem; text-transform:uppercase; letter-spacing:.07rem; color:rgba(255,255,255,.5); margin-bottom:.35rem; }
  .lote-filter input[type=date] { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#f5f5f5; border-radius:10px; padding:.6rem .85rem; font-size:.9rem; outline:none; }
  .lote-filter input[type=date]:focus { border-color:rgba(217,192,0,.5); }
  .btn-filter { background:linear-gradient(135deg,#d9c000,#f4d03f); color:#111; border:none; border-radius:10px; padding:.65rem 1.4rem; font-weight:700; font-size:.85rem; text-transform:uppercase; letter-spacing:.07rem; cursor:pointer; transition:filter .2s; }
  .btn-filter:hover { filter:brightness(1.1); }

  /* ── tabs ────────────────────────────────────────────────────────── */
  .lote-tabs     { display:flex; gap:.5rem; margin-bottom:0; flex-wrap:wrap; }
  .lote-tab-btn  { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); color:rgba(255,255,255,.6); border-radius:12px 12px 0 0; padding:.7rem 1.4rem; font-size:.85rem; font-weight:600; cursor:pointer; transition:all .2s; border-bottom:none; display:flex; align-items:center; gap:.5rem; }
  .lote-tab-btn:hover { background:rgba(255,255,255,.08); color:#fff; }
  .lote-tab-btn.active { background:rgba(12,12,15,.95); border-color:rgba(217,192,0,.35); color:#f4d03f; }
  .lote-badge { padding:.2rem .55rem; border-radius:999px; font-size:.7rem; font-weight:700; }
  .badge-green  { background:rgba(34,197,94,.2);  color:#4ade80; }
  .badge-yellow { background:rgba(217,192,0,.2);  color:#d9c000; }
  .badge-blue   { background:rgba(99,102,241,.2);  color:#818cf8; }

  /* ── tab panels ──────────────────────────────────────────────────── */
  .lote-panel { display:none; background:rgba(12,12,15,.95); border:1px solid rgba(217,192,0,.15); border-radius:0 12px 12px 12px; padding:1.5rem; }
  .lote-panel.active { display:block; }

  /* ── action bar ──────────────────────────────────────────────────── */
  .lote-action-bar { display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; margin-bottom:1.25rem; }
  .btn-sm-ghost { background:transparent; border:1px solid rgba(255,255,255,.15); color:rgba(255,255,255,.7); border-radius:8px; padding:.4rem .9rem; font-size:.8rem; cursor:pointer; transition:all .2s; }
  .btn-sm-ghost:hover { background:rgba(255,255,255,.06); color:#fff; border-color:rgba(255,255,255,.3); }
  .lote-counter { font-size:.82rem; color:rgba(255,255,255,.5); margin-left:.25rem; }
  .lote-counter.limit { color:#f4d03f; font-weight:600; }

  /* ── table ───────────────────────────────────────────────────────── */
  .lote-table-wrap { overflow-x:auto; border-radius:12px; }
  .lote-table-wrap::-webkit-scrollbar { height:4px; }
  .lote-table-wrap::-webkit-scrollbar-thumb { background:rgba(255,255,255,.1); border-radius:999px; }
  .lote-table { width:100%; border-collapse:collapse; font-size:.875rem; }
  .lote-table thead { background:rgba(0,0,0,.6); }
  .lote-table th { padding:.8rem 1rem; text-transform:uppercase; letter-spacing:.07rem; font-size:.7rem; color:rgba(255,255,255,.5); font-weight:600; text-align:left; white-space:nowrap; }
  .lote-table td { padding:.85rem 1rem; border-bottom:1px solid rgba(255,255,255,.04); vertical-align:middle; }
  .lote-table tbody tr { transition:background .15s; }
  .lote-table tbody tr:hover { background:rgba(255,255,255,.03); }
  .lote-table tbody tr.row-selected { background:rgba(217,192,0,.06); }
  .lote-table tbody tr:last-child td { border-bottom:none; }

  /* custom checkbox */
  .lote-chk { width:18px; height:18px; accent-color:#d9c000; cursor:pointer; }

  /* badges */
  .tag { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .65rem; border-radius:999px; font-size:.72rem; font-weight:600; white-space:nowrap; }
  .tag-green  { background:rgba(34,197,94,.15);  color:#4ade80; }
  .tag-yellow { background:rgba(217,192,0,.15);  color:#d9c000; }
  .tag-red    { background:rgba(239,68,68,.15);   color:#f87171; }
  .tag-blue   { background:rgba(99,102,241,.15);  color:#818cf8; }
  .tag-gray   { background:rgba(255,255,255,.08); color:rgba(255,255,255,.6); }

  /* info banner */
  .lote-info { display:flex; align-items:flex-start; gap:.75rem; background:rgba(217,192,0,.07); border:1px solid rgba(217,192,0,.2); border-radius:10px; padding:.9rem 1.1rem; margin-bottom:1.25rem; font-size:.83rem; color:rgba(255,255,255,.7); }
  .lote-info i { color:#d9c000; margin-top:.1rem; flex-shrink:0; }

  /* empty state */
  .lote-empty { text-align:center; padding:3rem 1rem; color:rgba(255,255,255,.35); }
  .lote-empty i { font-size:2.5rem; margin-bottom:.75rem; display:block; }

  /* ── pagination ──────────────────────────────────────────────────── */
  .lote-pag { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; padding:.85rem 0 .1rem; font-size:.82rem; color:rgba(255,255,255,.5); min-height:2.5rem; }
  .lote-pag-btns { display:flex; gap:.35rem; align-items:center; flex-wrap:wrap; }
  .btn-pag { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.65); border-radius:8px; padding:.32rem .7rem; font-size:.78rem; cursor:pointer; transition:all .18s; }
  .btn-pag:hover:not([disabled]) { background:rgba(255,255,255,.11); color:#fff; }
  .btn-pag[disabled] { opacity:.3; cursor:default; }
  .btn-pag.pag-active { background:rgba(217,192,0,.15); border-color:rgba(217,192,0,.45); color:#f4d03f; font-weight:700; }

  /* ── floating emit bar ───────────────────────────────────────────── */
  .lote-float-bar { position:fixed; bottom:0; left:0; right:0; z-index:1050; background:rgba(12,12,15,.97); border-top:1px solid rgba(217,192,0,.25); padding:1rem 1.5rem; display:none; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; backdrop-filter:blur(12px); }
  .lote-float-bar.visible { display:flex; }
  .lote-float-info { font-size:.9rem; color:rgba(255,255,255,.8); }
  .lote-float-info strong { color:#f4d03f; }
  .btn-emit { background:linear-gradient(135deg,#d9c000,#f4d03f); color:#111; border:none; border-radius:12px; padding:.8rem 2rem; font-weight:700; font-size:.9rem; cursor:pointer; transition:filter .2s; display:flex; align-items:center; gap:.5rem; }
  .btn-emit:hover { filter:brightness(1.1); }
  .btn-emit:disabled { opacity:.5; cursor:not-allowed; filter:none; }

  /* ── mobile cards ────────────────────────────────────────────────── */
  @media (max-width:640px) {
    .lote-title { font-size:1.5rem; }
    .lote-table thead { display:none; }
    .lote-table, .lote-table tbody, .lote-table tr, .lote-table td { display:block; width:100%; }
    .lote-table tr { background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.06); border-radius:12px; margin-bottom:.75rem; padding:.75rem; }
    .lote-table td { padding:.4rem .5rem; border:none; display:flex; justify-content:space-between; align-items:center; font-size:.83rem; }
    .lote-table td::before { content:attr(data-label); font-size:.7rem; text-transform:uppercase; letter-spacing:.06rem; color:rgba(255,255,255,.45); font-weight:600; flex-shrink:0; margin-right:.75rem; }
    .lote-table td:first-child { justify-content:flex-start; gap:.75rem; }
    .lote-table td:first-child::before { display:none; }
    .lote-float-bar { padding:.85rem 1rem; }
  }
</style>
<?= $v->end() ?>

<div class="content-wrapper lote-page">
  <div class="container-fluid px-3 px-md-4">

    <!-- HEADER -->
    <div class="lote-header">
      <div class="d-flex flex-wrap justify-content-between align-items-end" style="gap:1rem">
        <div>
          <h2 class="lote-title">Emissão em Lote</h2>
          <p class="lote-sub">Emita notas fiscais para múltiplas vendas de uma vez</p>
        </div>
        <ol class="breadcrumb" style="background:transparent;padding:0;margin:0">
          <li class="breadcrumb-item"><a href="./" style="color:rgba(255,255,255,.5)">Início</a></li>
          <li class="breadcrumb-item"><a href="<?= url('notas') ?>" style="color:rgba(255,255,255,.5)">Notas</a></li>
          <li class="breadcrumb-item active" style="color:rgba(255,255,255,.7)">Lote</li>
        </ol>
      </div>
    </div>

    <div class="px-0 px-md-2 mt-3">

      <!-- STAT CARDS -->
      <div class="lote-stats">
        <div class="lote-stat" onclick="abrirTab('elegiveis')" style="cursor:pointer" id="stat-el">
          <div class="lote-stat-icon icon-green"><i class="fas fa-check"></i></div>
          <div class="lote-stat-label">Elegíveis</div>
          <div class="lote-stat-value"><?= count($elegiveis) ?></div>
        </div>
        <div class="lote-stat" onclick="abrirTab('nao-elegiveis')" style="cursor:pointer" id="stat-ne">
          <div class="lote-stat-icon icon-yellow"><i class="fas fa-exclamation-triangle"></i></div>
          <div class="lote-stat-label">Não Elegíveis</div>
          <div class="lote-stat-value"><?= count($nao_elegiveis) ?></div>
        </div>
        <div class="lote-stat" onclick="abrirTab('emitidas')" style="cursor:pointer" id="stat-em">
          <div class="lote-stat-icon icon-blue"><i class="fas fa-file-invoice"></i></div>
          <div class="lote-stat-label">Já Emitidas</div>
          <div class="lote-stat-value"><?= count($emitidas) ?></div>
        </div>
      </div>

      <!-- FILTER -->
      <form method="GET" class="lote-filter">
        <div>
          <label>De</label>
          <input type="date" name="inicio" value="<?= $inicio ?>">
        </div>
        <div>
          <label>Até</label>
          <input type="date" name="fim" value="<?= $fim ?>">
        </div>
        <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filtrar</button>
      </form>

      <!-- TABS NAV -->
      <div class="lote-tabs">
        <button class="lote-tab-btn active" onclick="abrirTab('elegiveis')" id="tab-btn-elegiveis">
          <i class="fas fa-check-circle"></i> Elegíveis
          <span class="lote-badge badge-green"><?= count($elegiveis) ?></span>
        </button>
        <button class="lote-tab-btn" onclick="abrirTab('nao-elegiveis')" id="tab-btn-nao-elegiveis">
          <i class="fas fa-exclamation-circle"></i> Não Elegíveis
          <span class="lote-badge badge-yellow"><?= count($nao_elegiveis) ?></span>
        </button>
        <button class="lote-tab-btn" onclick="abrirTab('emitidas')" id="tab-btn-emitidas">
          <i class="fas fa-file-invoice"></i> Já Emitidas
          <span class="lote-badge badge-blue"><?= count($emitidas) ?></span>
        </button>
      </div>

      <!-- ── TAB: ELEGÍVEIS ─────────────────────────────────────────── -->
      <div class="lote-panel active" id="panel-elegiveis">
        <?php if ($elegiveis): ?>
          <div class="lote-action-bar">
            <button class="btn-sm-ghost" onclick="selecionarTodos('el')"><i class="fas fa-check-square"></i> Selecionar todos</button>
            <button class="btn-sm-ghost" onclick="limparSelecao('el')"><i class="fas fa-square"></i> Limpar</button>
            <span class="lote-counter" id="counter-el">0 selecionados (máx. 50)</span>
          </div>
          <div class="lote-table-wrap">
            <table class="lote-table">
              <thead>
                <tr>
                  <th></th>
                  <th>Venda</th>
                  <th>Cliente</th>
                  <th>CPF</th>
                  <th>Produto</th>
                  <th>Valor</th>
                  <th>Data</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($elegiveis as $venda): ?>
                <tr id="row-el-<?= $venda->id ?>">
                  <td><input type="checkbox" class="lote-chk chk-el" value="<?= $venda->id ?>" onchange="onCheck('el')"></td>
                  <td data-label="Venda">#<?= $venda->id ?></td>
                  <td data-label="Cliente"><?= htmlspecialchars($venda->dataComprador()->nome) ?></td>
                  <td data-label="CPF"><?= $venda->dataComprador()->cpf_cnpj ?></td>
                  <td data-label="Produto"><?= htmlspecialchars($venda->dataProduto()->nome) ?></td>
                  <td data-label="Valor"><strong style="color:#d9c000">R$ <?= number_format($venda->valor_bruto, 2, ',', '.') ?></strong></td>
                  <td data-label="Data"><?= formataData($venda->created_at) ?></td>
                  <td data-label="Status" id="st-el-<?= $venda->id ?>"><span class="tag tag-green"><i class="fas fa-check"></i> Elegível</span></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div id="pag-el" class="lote-pag"></div>
        <?php else: ?>
          <div class="lote-empty"><i class="fas fa-check-circle"></i>Nenhuma venda elegível no período selecionado.</div>
        <?php endif; ?>
      </div>

      <!-- ── TAB: NÃO ELEGÍVEIS ─────────────────────────────────────── -->
      <div class="lote-panel" id="panel-nao-elegiveis">
        <div class="lote-info">
          <i class="fas fa-info-circle"></i>
          Dados incompletos detectados — a emissão ainda é possível. Campos de endereço ausentes serão substituídos por "Não Informado" automaticamente.
        </div>
        <?php if ($nao_elegiveis): ?>
          <div class="lote-action-bar">
            <button class="btn-sm-ghost" onclick="selecionarTodos('ne')"><i class="fas fa-check-square"></i> Selecionar todos</button>
            <button class="btn-sm-ghost" onclick="limparSelecao('ne')"><i class="fas fa-square"></i> Limpar</button>
            <span class="lote-counter" id="counter-ne">0 selecionados (máx. 50)</span>
          </div>
          <div class="lote-table-wrap">
            <table class="lote-table">
              <thead>
                <tr>
                  <th></th>
                  <th>Venda</th>
                  <th>Cliente</th>
                  <th>Produto</th>
                  <th>Valor</th>
                  <th>Data</th>
                  <th>Pendências</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($nao_elegiveis as $item): $venda = $item['venda']; ?>
                <tr id="row-ne-<?= $venda->id ?>">
                  <td><input type="checkbox" class="lote-chk chk-ne" value="<?= $venda->id ?>" onchange="onCheck('ne')"></td>
                  <td data-label="Venda">#<?= $venda->id ?></td>
                  <td data-label="Cliente"><?= htmlspecialchars($venda->dataComprador()->nome) ?></td>
                  <td data-label="Produto"><?= htmlspecialchars($venda->dataProduto()->nome) ?></td>
                  <td data-label="Valor"><strong style="color:#d9c000">R$ <?= number_format($venda->valor_bruto, 2, ',', '.') ?></strong></td>
                  <td data-label="Data"><?= formataData($venda->created_at) ?></td>
                  <td data-label="Pendências"><span class="tag tag-yellow"><i class="fas fa-exclamation-triangle"></i> <?= implode(', ', $item['problemas']) ?></span></td>
                  <td data-label="Status" id="st-ne-<?= $venda->id ?>"><span class="tag tag-gray">Aguardando</span></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div id="pag-ne" class="lote-pag"></div>
        <?php else: ?>
          <div class="lote-empty"><i class="fas fa-exclamation-circle"></i>Nenhuma venda com pendências no período selecionado.</div>
        <?php endif; ?>
      </div>

      <!-- ── TAB: JÁ EMITIDAS ───────────────────────────────────────── -->
      <div class="lote-panel" id="panel-emitidas">
        <?php if ($emitidas): ?>
          <div class="lote-action-bar">
            <button class="btn-sm-ghost" onclick="selecionarTodos('em')"><i class="fas fa-check-square"></i> Selecionar todos</button>
            <button class="btn-sm-ghost" onclick="limparSelecao('em')"><i class="fas fa-square"></i> Limpar</button>
            <span class="lote-counter" id="counter-em">0 selecionados (máx. 50)</span>
          </div>
          <div class="lote-table-wrap">
            <table class="lote-table">
              <thead>
                <tr>
                  <th></th>
                  <th>Venda</th>
                  <th>Cliente</th>
                  <th>Produto</th>
                  <th>Valor</th>
                  <th>Emitida em</th>
                  <th>Dados</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($emitidas as $item): $venda = $item['venda']; $nf = $item['nota']; ?>
                <tr id="row-em-<?= $venda->id ?>">
                  <td><input type="checkbox" class="lote-chk chk-em" value="<?= $venda->id ?>" onchange="onCheck('em')"></td>
                  <td data-label="Venda">#<?= $venda->id ?></td>
                  <td data-label="Cliente"><?= htmlspecialchars($venda->dataComprador()->nome) ?></td>
                  <td data-label="Produto"><?= htmlspecialchars($venda->dataProduto()->nome) ?></td>
                  <td data-label="Valor"><strong style="color:#d9c000">R$ <?= number_format($venda->valor_bruto, 2, ',', '.') ?></strong></td>
                  <td data-label="Emitida em" style="font-size:.8rem;color:rgba(255,255,255,.6)"><?= formataDataHora($nf->created_at) ?></td>
                  <td data-label="Dados">
                    <?php if ($item['elegivel']): ?>
                      <span class="tag tag-green"><i class="fas fa-check"></i> Completos</span>
                    <?php else: ?>
                      <span class="tag tag-yellow" title="<?= implode(', ', $item['problemas']) ?>"><i class="fas fa-exclamation-triangle"></i> Incompletos</span>
                    <?php endif; ?>
                  </td>
                  <td data-label="Status" id="st-em-<?= $venda->id ?>"><span class="tag tag-blue"><i class="fas fa-file-invoice"></i> Emitida</span></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div id="pag-em" class="lote-pag"></div>
        <?php else: ?>
          <div class="lote-empty"><i class="fas fa-file-invoice"></i>Nenhuma nota emitida no período selecionado.</div>
        <?php endif; ?>
      </div>

    </div><!-- /inner -->
  </div><!-- /container -->
</div>

<!-- FLOATING ACTION BAR -->
<div class="lote-float-bar" id="float-bar">
  <div class="lote-float-info">
    <strong id="float-count">0</strong> nota(s) selecionada(s)
    <span id="float-tab-label" style="color:rgba(255,255,255,.5);font-size:.82rem;margin-left:.5rem"></span>
  </div>
  <button class="btn-emit" id="btn-emit" onclick="emitirLote()">
    <i class="fas fa-paper-plane"></i> Emitir Notas
  </button>
</div>

<!-- MODAL RESUMO -->
<div id="modal-resumo" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem">
  <div style="background:#111;border:1px solid rgba(217,192,0,.25);border-radius:20px;width:100%;max-width:600px;max-height:85vh;display:flex;flex-direction:column;overflow:hidden">
    <!-- header -->
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <h3 style="margin:0;font-size:1.1rem;font-weight:700;color:#fff">Resumo da Emissão</h3>
      <button onclick="fecharModal()" style="background:none;border:none;color:rgba(255,255,255,.5);font-size:1.4rem;cursor:pointer;line-height:1;padding:0">&times;</button>
    </div>
    <!-- summary chips -->
    <div id="modal-chips" style="display:flex;gap:.75rem;padding:1rem 1.5rem;flex-shrink:0;flex-wrap:wrap"></div>
    <!-- table -->
    <div style="overflow-y:auto;flex:1;padding:0 1.5rem 1.5rem">
      <table style="width:100%;border-collapse:collapse;font-size:.85rem">
        <thead>
          <tr style="position:sticky;top:0;background:#111">
            <th style="text-align:left;padding:.6rem .5rem;color:rgba(255,255,255,.4);font-size:.7rem;text-transform:uppercase;letter-spacing:.06rem;border-bottom:1px solid rgba(255,255,255,.08)">Venda</th>
            <th style="text-align:left;padding:.6rem .5rem;color:rgba(255,255,255,.4);font-size:.7rem;text-transform:uppercase;letter-spacing:.06rem;border-bottom:1px solid rgba(255,255,255,.08)">Status</th>
            <th style="text-align:left;padding:.6rem .5rem;color:rgba(255,255,255,.4);font-size:.7rem;text-transform:uppercase;letter-spacing:.06rem;border-bottom:1px solid rgba(255,255,255,.08)">Motivo / ID Nota</th>
          </tr>
        </thead>
        <tbody id="modal-tbody"></tbody>
      </table>
    </div>
    <!-- footer -->
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.08);text-align:right;flex-shrink:0">
      <button onclick="fecharModal()" style="background:linear-gradient(135deg,#d9c000,#f4d03f);color:#111;border:none;border-radius:10px;padding:.65rem 1.75rem;font-weight:700;font-size:.9rem;cursor:pointer">Fechar</button>
    </div>
  </div>
</div>

<?php $v->start("scripts") ?>
<script>
  const LIMITE     = 50;
  const URL_EMITIR = "<?= $router->route('notazz.lote.emitir') ?>";
  let   tabAtiva   = 'el';

  // ── tabs ─────────────────────────────────────────────────────────────
  function abrirTab(nome) {
    const map = { 'elegiveis':'el', 'nao-elegiveis':'ne', 'emitidas':'em' };
    const slug = map[nome] || nome;
    tabAtiva = slug;

    document.querySelectorAll('.lote-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.lote-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + nome).classList.add('active');
    document.getElementById('tab-btn-' + nome).classList.add('active');
    atualizarFloatBar();
  }

  // ── selection ─────────────────────────────────────────────────────────
  function getSelecionados(prefix) {
    return [...document.querySelectorAll(`.chk-${prefix}:checked`)].map(el => el.value);
  }

  function onCheck(prefix) {
    const all = [...document.querySelectorAll(`.chk-${prefix}:checked`)];
    if (all.length > LIMITE) { all[LIMITE].checked = false; }
    atualizarContador(prefix);
    atualizarFloatBar();

    // highlight row
    document.querySelectorAll(`.chk-${prefix}`).forEach(chk => {
      chk.closest('tr').classList.toggle('row-selected', chk.checked);
    });
  }

  function atualizarContador(prefix) {
    const n   = getSelecionados(prefix).length;
    const el  = document.getElementById('counter-' + prefix);
    if (!el) return;
    el.textContent = `${n} selecionado${n !== 1 ? 's' : ''} (máx. ${LIMITE})`;
    el.className   = 'lote-counter' + (n >= LIMITE ? ' limit' : '');
  }

  function selecionarTodos(prefix) {
    // Only selects visible rows (current page)
    const todos   = [...document.querySelectorAll(`.chk-${prefix}`)];
    const visiveis = todos.filter(c => c.closest('tr').style.display !== 'none');
    todos.forEach(c => { c.checked = false; c.closest('tr').classList.remove('row-selected'); });
    visiveis.slice(0, LIMITE).forEach(c => { c.checked = true; c.closest('tr').classList.add('row-selected'); });
    atualizarContador(prefix);
    atualizarFloatBar();
  }

  function limparSelecao(prefix) {
    document.querySelectorAll(`.chk-${prefix}`).forEach(c => {
      c.checked = false;
      c.closest('tr').classList.remove('row-selected');
    });
    atualizarContador(prefix);
    atualizarFloatBar();
  }

  // ── pagination ────────────────────────────────────────────────────────
  const PER_PAGE  = 50;
  const pageState = { el: 1, ne: 1, em: 1 };
  const panelMap  = { el: 'elegiveis', ne: 'nao-elegiveis', em: 'emitidas' };

  function getRows(prefix) {
    return [...document.querySelectorAll(`#panel-${panelMap[prefix]} .lote-table tbody tr`)];
  }

  function paginar(prefix, page) {
    const rows       = getRows(prefix);
    const total      = rows.length;
    const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
    page = Math.max(1, Math.min(page, totalPages));
    pageState[prefix] = page;

    const start = (page - 1) * PER_PAGE;
    rows.forEach(function(row, i) {
      row.style.display = (i >= start && i < start + PER_PAGE) ? '' : 'none';
    });

    const pag = document.getElementById('pag-' + prefix);
    if (!pag) return;
    if (total === 0) { pag.innerHTML = ''; return; }
    if (totalPages === 1) { pag.innerHTML = `<span>${total} registro${total !== 1 ? 's' : ''}</span>`; return; }

    const prev = `<button class="btn-pag" onclick="paginar('${prefix}',${page-1})"${page===1?' disabled':''}>&#8249; Anterior</button>`;
    const next = `<button class="btn-pag" onclick="paginar('${prefix}',${page+1})"${page===totalPages?' disabled':''}>Próxima &#8250;</button>`;

    let btns = '';
    for (var p = 1; p <= totalPages; p++) {
      if (p === 1 || p === totalPages || (p >= page - 2 && p <= page + 2)) {
        btns += `<button class="btn-pag${p === page ? ' pag-active' : ''}" onclick="paginar('${prefix}',${p})">${p}</button>`;
      } else if (p === page - 3 || p === page + 3) {
        btns += `<span style="color:rgba(255,255,255,.3);padding:0 .2rem">…</span>`;
      }
    }

    const info = `${start + 1}–${Math.min(start + PER_PAGE, total)} de ${total}`;
    pag.innerHTML = `<span>${info}</span><div class="lote-pag-btns">${prev}${btns}${next}</div>`;
  }

  // init
  paginar('el', 1); paginar('ne', 1); paginar('em', 1);

  // ── float bar ─────────────────────────────────────────────────────────
  function atualizarFloatBar() {
    const n     = getSelecionados(tabAtiva).length;
    const bar   = document.getElementById('float-bar');
    const labels = { el:'Elegíveis', ne:'Não Elegíveis', em:'Já Emitidas' };
    document.getElementById('float-count').textContent     = n;
    document.getElementById('float-tab-label').textContent = '— ' + (labels[tabAtiva] || '');
    bar.classList.toggle('visible', n > 0);
  }

  // ── emit ──────────────────────────────────────────────────────────────
  function emitirLote() {
    const ids = getSelecionados(tabAtiva);
    if (!ids.length) { alert('Selecione ao menos uma venda.'); return; }
    if (!confirm(`Emitir ${ids.length} nota(s) fiscal(is)?`)) return;

    const btn = document.getElementById('btn-emit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Emitindo...';

    const prefix = tabAtiva;

    $.ajax({
      url: URL_EMITIR, method: 'POST', data: { ids: ids }, dataType: 'json',
      success: function(res) {
        let ok = 0, erro = 0;
        res.forEach(function(r) {
          const cel = document.getElementById('st-' + prefix + '-' + r.venda_id);
          if (r.statusProcessamento === 'sucesso' || r.codigoProcessamento === '000') {
            if (cel) cel.innerHTML = '<span class="tag tag-green"><i class="fas fa-check"></i> Emitida</span>';
            ok++;
          } else {
            if (cel) cel.innerHTML = '<span class="tag tag-red"><i class="fas fa-times"></i> Erro</span>';
            erro++;
          }
        });
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Emitir Notas';
        abrirModalResumo(res, ok, erro);
      },
      error: function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Emitir Notas';
        alert('Erro de comunicação com o servidor.');
      }
    });
  }

  // ── modal resumo ──────────────────────────────────────────────────────────
  function abrirModalResumo(resultados, ok, erro) {
    // chips de resumo
    var chips = '';
    chips += '<div style="display:flex;align-items:center;gap:.5rem;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);border-radius:10px;padding:.5rem 1rem;font-size:.9rem;font-weight:700;color:#4ade80">'
           + '<i class="fas fa-check-circle"></i> ' + ok + ' emitida' + (ok !== 1 ? 's' : '') + ' com sucesso</div>';
    if (erro > 0) {
      chips += '<div style="display:flex;align-items:center;gap:.5rem;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:.5rem 1rem;font-size:.9rem;font-weight:700;color:#f87171">'
             + '<i class="fas fa-times-circle"></i> ' + erro + ' com erro</div>';
    }
    document.getElementById('modal-chips').innerHTML = chips;

    // linhas da tabela
    var rows = '';
    resultados.forEach(function(r) {
      var sucesso = r.statusProcessamento === 'sucesso' || r.codigoProcessamento === '000';
      var statusHtml = sucesso
        ? '<span class="tag tag-green"><i class="fas fa-check"></i> Emitida</span>'
        : '<span class="tag tag-red"><i class="fas fa-times"></i> Erro</span>';
      var detalhe = sucesso
        ? (r.id ? '<span style="color:rgba(255,255,255,.45);font-size:.78rem;word-break:break-all">' + r.id + '</span>' : '-')
        : '<span style="color:#f87171;font-size:.82rem">' + (r.motivo || 'Erro desconhecido') + '</span>';
      rows += '<tr style="border-bottom:1px solid rgba(255,255,255,.05)">'
            + '<td style="padding:.65rem .5rem;color:rgba(255,255,255,.8);white-space:nowrap">#' + r.venda_id + '</td>'
            + '<td style="padding:.65rem .5rem">' + statusHtml + '</td>'
            + '<td style="padding:.65rem .5rem">' + detalhe + '</td>'
            + '</tr>';
    });
    document.getElementById('modal-tbody').innerHTML = rows;

    var modal = document.getElementById('modal-resumo');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function fecharModal() {
    document.getElementById('modal-resumo').style.display = 'none';
    document.body.style.overflow = '';
  }

  document.getElementById('modal-resumo').addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
  });

  // clique nos stat cards abre a tab correta
  const tabMap = { 'stat-el':'elegiveis', 'stat-ne':'nao-elegiveis', 'stat-em':'emitidas' };
  document.querySelectorAll('.lote-stat').forEach(function(el) {
    el.addEventListener('click', function() { abrirTab(tabMap[el.id] || 'elegiveis'); });
  });
</script>
<?php $v->end() ?>

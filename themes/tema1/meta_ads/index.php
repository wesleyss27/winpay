<?php
$v->layout("_theme");
if ($_SESSION["tipo"] == 6) { header("Location: " . url("")); exit; }

$erro    = htmlspecialchars($_GET["erro"]    ?? "");
$sucesso = htmlspecialchars($_GET["sucesso"] ?? "");
$nContas = intval($_GET["contas"] ?? 0);
?>

<style>
/* ── Base ─────────────────────────────────────────── */
.ma-section { margin-bottom: 28px; }
.ma-section-title {
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: #FFDD00; margin-bottom: 14px;
    padding-bottom: 8px; border-bottom: 1px solid rgba(255,221,0,.15);
    display: flex; align-items: center; gap: 8px;
}
.ma-section-title .material-symbols-rounded { font-size: 18px; }

/* ── Connection card ──────────────────────────────── */
.ma-connect-card {
    background: #1a1a1a; border: 1px solid rgba(255,221,0,.2);
    border-radius: 14px; padding: 20px 24px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 14px;
}
.ma-connect-left { display: flex; align-items: center; gap: 14px; }
.ma-connect-icon {
    width: 46px; height: 46px; border-radius: 12px;
    background: linear-gradient(135deg,#1877f2,#0a5dc4);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ma-connect-icon .material-symbols-rounded { font-size: 24px; color: #fff; }
.ma-connect-name  { font-size: 15px; font-weight: 700; color: #fff; }
.ma-connect-sub   { font-size: 12px; color: #666; margin-top: 2px; }

/* ── Status pill ──────────────────────────────────── */
.ma-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
}
.ma-pill-ok   { background: rgba(34,197,94,.12); color: #4ade80; border: 1px solid rgba(34,197,94,.25); }
.ma-pill-off  { background: rgba(148,163,184,.08); color: #94a3b8; border: 1px solid rgba(148,163,184,.18); }
.ma-pill-warn { background: rgba(251,191,36,.1); color: #fbbf24; border: 1px solid rgba(251,191,36,.25); }
.ma-pill .material-symbols-rounded { font-size: 14px; }

/* ── Buttons ──────────────────────────────────────── */
.ma-btn {
    display: inline-flex; align-items: center; gap: 6px;
    border: none; border-radius: 8px; padding: 8px 16px;
    font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
    transition: filter .2s;
}
.ma-btn:hover { filter: brightness(1.1); }
.ma-btn-primary { background: linear-gradient(135deg,#1877f2,#0a5dc4); color: #fff; }
.ma-btn-yellow  { background: #FFDD00; color: #111; }
.ma-btn-dark    { background: #2a2a2a; color: #ccc; border: 1px solid #333; }
.ma-btn-danger  { background: rgba(239,68,68,.12); color: #f87171; border: 1px solid rgba(239,68,68,.2); }
.ma-btn-sync    { background: rgba(24,119,242,.1); color: #60a5fa; border: 1px solid rgba(24,119,242,.2); }

/* ── Alert ────────────────────────────────────────── */
.ma-alert {
    padding: 11px 16px; border-radius: 10px; font-size: 13px;
    display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
}
.ma-alert-err { background: rgba(239,68,68,.08); color: #fca5a5; border: 1px solid rgba(239,68,68,.18); }
.ma-alert-ok  { background: rgba(34,197,94,.08); color: #86efac; border: 1px solid rgba(34,197,94,.18); }

/* ── Filters ──────────────────────────────────────── */
.ma-filters {
    background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px;
    padding: 16px 20px; display: flex; align-items: flex-end; flex-wrap: wrap; gap: 12px;
}
.ma-filters label { display: block; font-size: 11px; font-weight: 700; color: #666;
    text-transform: uppercase; letter-spacing: .6px; margin-bottom: 4px; }
.ma-filters input[type=date] {
    background: #0f0f0f; border: 1px solid #333; color: #f0f0f0;
    border-radius: 8px; padding: 7px 10px; font-size: 13px; outline: none;
}
.ma-filters input[type=date]:focus { border-color: #FFDD00; }

/* ── KPI Cards ────────────────────────────────────── */
.fin-card {
    background: linear-gradient(145deg,#1e1e1e,#161616);
    border: 1px solid rgba(255,221,0,.15); border-radius: 14px;
    padding: 20px 22px; color: #fff; position: relative; overflow: hidden;
}
.fin-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background: var(--card-color,#FFDD00);
}
.fin-card .card-icon {
    width:44px; height:44px; border-radius:10px; background:rgba(255,255,255,.06);
    display:flex; align-items:center; justify-content:center;
    margin-bottom:12px; color:var(--card-color,#FFDD00); font-size:24px;
}
.fin-card .card-label { font-size:11px; color:#666; text-transform:uppercase; letter-spacing:.7px; margin-bottom:4px; }
.fin-card .card-value { font-size:24px; font-weight:700; color:#fff; }
.fin-card .card-sub   { font-size:12px; color:#555; margin-top:4px; }

/* ── Chart ────────────────────────────────────────── */
.ma-chart-wrap {
    background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 14px; padding: 20px;
}

/* ── Tables ───────────────────────────────────────── */
.ma-table-wrap {
    background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 14px;
    overflow: hidden;
}
.ma-table-head {
    padding: 16px 20px; display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 10px;
    border-bottom: 1px solid #2a2a2a;
}
.ma-table { width: 100%; border-collapse: collapse; }
.ma-table thead th {
    padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700;
    color: #555; text-transform: uppercase; letter-spacing: .5px;
    background: #111; border-bottom: 1px solid #222;
}
.ma-table thead th.r { text-align: right; }
.ma-table tbody td { padding: 12px 14px; font-size: 13px; color: #ccc; border-bottom: 1px solid #222; vertical-align: middle; }
.ma-table tbody tr:last-child td { border-bottom: none; }
.ma-table tbody tr:hover td { background: rgba(255,221,0,.03); }
.ma-table td.r { text-align: right; }

/* ── Badge ────────────────────────────────────────── */
.ma-badge {
    display: inline-block; max-width: 180px; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap;
    padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; vertical-align: middle;
}
.ma-badge-blue   { background: rgba(24,119,242,.15); color: #60a5fa; border: 1px solid rgba(24,119,242,.25); }
.ma-badge-purple { background: rgba(124,58,237,.15); color: #c084fc; border: 1px solid rgba(124,58,237,.25); }
.ma-badge-gray   { background: rgba(255,255,255,.07); color: #9ca3af; border: 1px solid rgba(255,255,255,.1); }
.ma-badge-orange { background: rgba(234,88,12,.15); color: #fb923c; border: 1px solid rgba(234,88,12,.25); }
.ma-badge-green  { background: rgba(34,197,94,.12); color: #4ade80; border: 1px solid rgba(34,197,94,.2); }

/* ── Match tipo ───────────────────────────────────── */
.ma-match {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 10px; font-size: 10px; font-weight: 700;
    white-space: nowrap; vertical-align: middle;
}
.ma-match-url  { background: rgba(34,197,94,.1); color: #4ade80; border: 1px solid rgba(34,197,94,.2); }
.ma-match-ip   { background: rgba(251,191,36,.1); color: #fbbf24; border: 1px solid rgba(251,191,36,.2); }
.ma-match-none { background: rgba(255,255,255,.05); color: #555; border: 1px solid #2a2a2a; }

/* ── Progress ─────────────────────────────────────── */
.ma-bar-wrap { background: #2a2a2a; border-radius: 4px; height: 5px; min-width: 70px; }
.ma-bar      { height: 5px; border-radius: 4px; background: #FFDD00; transition: width .5s; }

/* ── ROAS ─────────────────────────────────────────── */
.roas-hi  { color: #4ade80; font-weight: 700; }
.roas-mid { color: #fbbf24; font-weight: 700; }
.roas-lo  { color: #f87171; font-weight: 700; }

/* ── Search ───────────────────────────────────────── */
.ma-search {
    background: #111; border: 1px solid #333; color: #ccc;
    border-radius: 8px; padding: 6px 12px; font-size: 13px; outline: none; width: 200px;
}
.ma-search:focus { border-color: #FFDD00; }
.ma-search::placeholder { color: #555; }

/* ── Pagination ───────────────────────────────────── */
.ma-pager {
    display: flex; align-items: center; gap: 8px; padding: 12px 20px;
    border-top: 1px solid #222; font-size: 12px; color: #666;
}
.ma-pager button {
    background: #2a2a2a; border: 1px solid #333; color: #ccc;
    border-radius: 6px; padding: 4px 12px; font-size: 12px; cursor: pointer;
}
.ma-pager button:hover:not(:disabled) { background: #333; }
.ma-pager button:disabled { opacity: .35; cursor: default; }
.ma-pager-info { flex: 1; text-align: center; color: #555; }

/* ── Empty / loading ──────────────────────────────── */
.ma-empty   { text-align: center; padding: 40px 24px; color: #555; }
.ma-empty .material-symbols-rounded { font-size: 40px; display: block; margin-bottom: 8px; color: #333; }
.ma-loading { text-align: center; padding: 36px; color: #555; font-size: 13px; }
.ma-spinner { display:inline-block; width:16px; height:16px; border:2px solid #2a2a2a;
    border-top-color:#FFDD00; border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Contas grid ──────────────────────────────────── */
.ma-contas-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(210px,1fr)); gap: 10px; padding: 16px 20px; }
.ma-conta-row {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    background: #111; border: 1px solid #2a2a2a; border-radius: 10px;
}
.ma-conta-name  { font-size: 13px; font-weight: 600; color: #e0e0e0; }
.ma-conta-sub   { font-size: 11px; color: #555; }

/* ── Tip ──────────────────────────────────────────── */
.ma-tip {
    background: rgba(255,221,0,.05); border: 1px solid rgba(255,221,0,.15);
    border-radius: 12px; padding: 16px 20px;
}
.ma-tip-title { font-size: 12px; font-weight: 700; color: #FFDD00; margin-bottom: 6px;
    display: flex; align-items: center; gap: 6px; }
.ma-tip code  { background: rgba(255,255,255,.06); padding: 5px 10px; border-radius: 6px;
    font-size: 11px; display: inline-block; margin-top: 6px; color: #ccc; }
.ma-tip small { font-size: 11px; color: #666; display: block; margin-top: 8px; }
</style>

<div class="content-wrapper">
<br>
<section class="content">
<div class="container-fluid">

  <!-- ── ALERTS ───────────────────────────────────── -->
  <?php
  $errMsgs = [
    'sem_config'    => 'App Meta não configurado. Configure abaixo.',
    'acesso_negado' => 'Acesso negado pelo Meta.',
    'state_invalido'=> 'Sessão expirada. Tente novamente.',
    'token_invalido'=> 'Erro ao obter token de acesso.',
    'user_invalido' => 'Erro ao identificar usuário Meta.',
  ];
  if ($erro): ?>
    <div class="ma-alert ma-alert-err">
      <span class="material-symbols-rounded" style="font-size:18px">error</span>
      <?= $errMsgs[$erro] ?? "Erro: $erro" ?>
    </div>
  <?php endif; ?>
  <?php if ($sucesso === 'conectado'): ?>
    <div class="ma-alert ma-alert-ok">
      <span class="material-symbols-rounded" style="font-size:18px">check_circle</span>
      Meta conectado com sucesso! <?= $nContas ? "{$nContas} conta(s) de anúncio importada(s)." : "" ?>
      Clique em "Sincronizar" para buscar dados de gasto.
    </div>
  <?php endif; ?>
  <?php if ($sucesso === 'desconectado'): ?>
    <div class="ma-alert ma-alert-err">
      <span class="material-symbols-rounded" style="font-size:18px">link_off</span>
      Conta Meta desconectada.
    </div>
  <?php endif; ?>

  <!-- ── HEADER ───────────────────────────────────── -->
  <div class="d-flex align-items-start justify-content-between mb-4" style="flex-wrap:wrap;gap:12px">
    <div>
      <h4 style="color:#FFDD00;font-weight:700;margin:0">
        <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:6px">ads_click</span>
        Meta Ads — Atribuição
      </h4>
      <span style="color:#555;font-size:13px">Rastreie de qual campanha, anúncio e criativo veio cada venda</span>
    </div>
    <?php if ($conexao): ?>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <span class="ma-pill ma-pill-ok">
          <span class="material-symbols-rounded">check_circle</span>
          <?= htmlspecialchars($conexao->meta_user_name ?? 'Conectado') ?>
        </span>
        <button class="ma-btn ma-btn-sync" id="btnSincronizar" onclick="sincronizar()">
          <span class="material-symbols-rounded" style="font-size:16px">sync</span> Sincronizar gastos
        </button>
        <a href="<?= url('meta-ads/desconectar') ?>" class="ma-btn ma-btn-danger"
           onclick="return confirm('Desconectar conta Meta?')">
          <span class="material-symbols-rounded" style="font-size:16px">link_off</span> Desconectar
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── CONEXÃO META ──────────────────────────────── -->
  <div class="ma-section">
    <div class="ma-section-title">
      <span class="material-symbols-rounded">link</span> Conexão Meta Ads
    </div>

    <?php if ($conexao): ?>
      <!-- Conectado -->
      <div class="ma-connect-card">
        <div class="ma-connect-left">
          <div class="ma-connect-icon">
            <span class="material-symbols-rounded">person</span>
          </div>
          <div>
            <div class="ma-connect-name"><?= htmlspecialchars($conexao->meta_user_name ?? 'Conta Meta') ?></div>
            <div class="ma-connect-sub">ID: <?= htmlspecialchars($conexao->meta_user_id ?? '—') ?>
              <?php if ($conexao->token_expires_at): ?>
                · Token expira em <?= date('d/m/Y', strtotime($conexao->token_expires_at)) ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <span class="ma-pill ma-pill-ok">
          <span class="material-symbols-rounded">check_circle</span> Ativo
        </span>
      </div>

      <!-- Contas de anúncio -->
      <?php if ($contas): ?>
        <div class="ma-table-wrap" style="margin-top:14px">
          <div class="ma-table-head">
            <span style="font-size:13px;font-weight:600;color:#ccc">
              <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;color:#FFDD00">account_balance</span>
              Contas de Anúncio
              <span style="font-size:11px;color:#555;font-weight:400"> — ative as que deseja rastrear</span>
            </span>
          </div>
          <div class="ma-contas-grid">
            <?php foreach ($contas as $c): ?>
              <div class="ma-conta-row">
                <div class="custom-control custom-switch mb-0">
                  <input type="checkbox" class="custom-control-input" id="c<?= $c->id ?>"
                         <?= $c->ativa ? 'checked' : '' ?>
                         onchange="toggleConta('<?= htmlspecialchars($c->account_id) ?>',this.checked?1:0)">
                  <label class="custom-control-label" for="c<?= $c->id ?>"></label>
                </div>
                <div>
                  <div class="ma-conta-name"><?= htmlspecialchars($c->account_name ?? $c->account_id) ?></div>
                  <div class="ma-conta-sub"><?= htmlspecialchars($c->account_id) ?> · <?= $c->currency ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    <?php elseif ($config && $config->app_id): ?>
      <!-- App configurado mas não conectado -->
      <div class="ma-connect-card">
        <div class="ma-connect-left">
          <div class="ma-connect-icon">
            <span class="material-symbols-rounded">ads_click</span>
          </div>
          <div>
            <div class="ma-connect-name">Meta Ads não conectado</div>
            <div class="ma-connect-sub">Conecte sua conta para importar dados de gasto e calcular ROAS</div>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
          <span class="ma-pill ma-pill-off">
            <span class="material-symbols-rounded">link_off</span> Desconectado
          </span>
          <a href="<?= url('meta-ads/conectar') ?>" class="ma-btn ma-btn-primary">
            <span class="material-symbols-rounded" style="font-size:16px">login</span>
            Conectar com Meta
          </a>
        </div>
      </div>

    <?php else: ?>
      <!-- App não configurado -->
      <div class="ma-connect-card">
        <div class="ma-connect-left">
          <div class="ma-connect-icon" style="background:linear-gradient(135deg,#444,#222)">
            <span class="material-symbols-rounded">warning</span>
          </div>
          <div>
            <div class="ma-connect-name">App Meta não configurado</div>
            <div class="ma-connect-sub">
              <?php if ($isAdmin): ?>
                Configure o App ID e App Secret para habilitar a conexão OAuth.
              <?php else: ?>
                Solicite ao administrador que configure o App Meta.
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
          <span class="ma-pill ma-pill-warn">
            <span class="material-symbols-rounded">warning</span> Não configurado
          </span>
          <?php if ($isAdmin): ?>
            <button class="ma-btn ma-btn-yellow" data-toggle="modal" data-target="#modalConfig">
              <span class="material-symbols-rounded" style="font-size:16px">settings</span>
              Configurar App
            </button>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Nota: atribuição por UTM funciona mesmo sem conexão Meta -->
    <p style="font-size:12px;color:#555;margin-top:10px">
      <span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;color:#FFDD00">info</span>
      A atribuição de vendas por campanha funciona via UTM mesmo sem a conexão Meta. A conexão é necessária apenas para ver dados de <strong style="color:#ccc">gasto e ROAS</strong>.
    </p>
  </div>

  <!-- ── FILTROS ───────────────────────────────────── -->
  <div class="ma-section">
    <div class="ma-section-title">
      <span class="material-symbols-rounded">filter_alt</span> Período
    </div>
    <div class="ma-filters">
      <div>
        <label>Início</label>
        <input type="date" id="filtroInicio" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
      </div>
      <div>
        <label>Fim</label>
        <input type="date" id="filtroFim" value="<?= date('Y-m-d') ?>">
      </div>
      <div>
        <button class="ma-btn ma-btn-yellow" onclick="carregarDados()">
          <span class="material-symbols-rounded" style="font-size:16px">search</span> Buscar
        </button>
      </div>
      <div style="display:flex;gap:6px">
        <button class="ma-btn ma-btn-dark" onclick="setPeriodo(7)">7d</button>
        <button class="ma-btn ma-btn-dark" onclick="setPeriodo(30)">30d</button>
        <button class="ma-btn ma-btn-dark" onclick="setPeriodo(90)">90d</button>
      </div>
    </div>
  </div>

  <!-- ── KPI CARDS ─────────────────────────────────── -->
  <div class="ma-section">
    <div class="row">
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="fin-card" style="--card-color:#FFDD00">
          <div class="card-icon"><span class="material-symbols-rounded">trending_up</span></div>
          <div class="card-label">Receita Meta</div>
          <div class="card-value" id="cReceita">—</div>
          <div class="card-sub">vendas pagas no período</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="fin-card" style="--card-color:#60a5fa">
          <div class="card-icon"><span class="material-symbols-rounded">shopping_bag</span></div>
          <div class="card-label">Total de Vendas</div>
          <div class="card-value" id="cVendas">—</div>
          <div class="card-sub">ticket médio: <span id="cTicket">—</span></div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="fin-card" style="--card-color:#fb923c">
          <div class="card-icon"><span class="material-symbols-rounded">payments</span></div>
          <div class="card-label">Gasto Meta</div>
          <div class="card-value" id="cGasto">—</div>
          <div class="card-sub" id="cGastoSub">sincronize para ver</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="fin-card" style="--card-color:#4ade80">
          <div class="card-icon"><span class="material-symbols-rounded">query_stats</span></div>
          <div class="card-label">ROAS</div>
          <div class="card-value" id="cRoas">—</div>
          <div class="card-sub">receita ÷ gasto</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── GRÁFICO ───────────────────────────────────── -->
  <div class="ma-section">
    <div class="ma-section-title">
      <span class="material-symbols-rounded">bar_chart</span> Receita por Dia
    </div>
    <div class="ma-chart-wrap">
      <div id="chartLoading" class="ma-loading"><div class="ma-spinner"></div></div>
      <canvas id="chartReceita" style="display:none;height:200px;max-height:200px"></canvas>
    </div>
  </div>

  <!-- ── TABELA CAMPANHAS ──────────────────────────── -->
  <div class="ma-section">
    <div class="ma-section-title">
      <span class="material-symbols-rounded">campaign</span>
      Performance por Campanha / Anúncio
      <span id="badgeCamp" style="font-size:11px;font-weight:400;color:#555;text-transform:none;letter-spacing:0"></span>
    </div>
    <div class="ma-table-wrap">
      <div id="campLoading" class="ma-loading"><div class="ma-spinner"></div></div>
      <div id="campEmpty" class="ma-empty" style="display:none">
        <span class="material-symbols-rounded">ads_click</span>
        <p style="margin:0;font-size:13px">Nenhuma venda de Meta Ads no período.</p>
        <p style="margin:4px 0 0;font-size:12px">Configure seus anúncios com <code style="background:#1a1a1a;padding:2px 6px;border-radius:4px">utm_source=facebook</code></p>
      </div>
      <div id="campWrap" style="display:none;overflow-x:auto">
        <table class="ma-table">
          <thead>
            <tr>
              <th>Campanha</th>
              <th>Anúncio (criativo)</th>
              <th>Fonte</th>
              <th class="r">Vendas</th>
              <th class="r">Receita</th>
              <th style="min-width:90px">Share</th>
              <th class="r">Ticket</th>
              <th class="r">Gasto</th>
              <th class="r">ROAS</th>
            </tr>
          </thead>
          <tbody id="campBody"></tbody>
        </table>
      </div>
      <div class="ma-pager" id="campPager" style="display:none">
        <button id="campPrev" onclick="pagCamp(-1)">&#8592; Ant.</button>
        <span class="ma-pager-info" id="campPagerInfo"></span>
        <button id="campNext" onclick="pagCamp(1)">Próx. &#8594;</button>
      </div>
    </div>
  </div>

  <!-- ── TABELA VENDAS ─────────────────────────────── -->
  <div class="ma-section">
    <div class="ma-section-title">
      <span class="material-symbols-rounded">receipt_long</span>
      Vendas com Atribuição Meta
      <span id="badgeVend" style="font-size:11px;font-weight:400;color:#555;text-transform:none;letter-spacing:0"></span>
    </div>
    <div class="ma-table-wrap">
      <div class="ma-table-head">
        <span style="font-size:13px;color:#ccc;font-weight:600">Lista de vendas</span>
        <input type="text" class="ma-search" id="searchVend"
               placeholder="Buscar comprador, campanha..."
               oninput="filtrarVendas()">
      </div>
      <div id="vendLoading" class="ma-loading"><div class="ma-spinner"></div></div>
      <div id="vendEmpty" class="ma-empty" style="display:none">
        <span class="material-symbols-rounded">receipt_long</span>
        <p style="margin:0;font-size:13px">Nenhuma venda encontrada no período.</p>
      </div>
      <div id="vendWrap" style="display:none;overflow-x:auto">
        <table class="ma-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Data</th>
              <th>Comprador</th>
              <th>Produto</th>
              <th class="r">Valor</th>
              <th>Campanha</th>
              <th>Anúncio</th>
              <th>Fonte</th>
              <th title="URL = UTM direto | IP = resolvido via pixel_lead pelo IP">Atrib.</th>
            </tr>
          </thead>
          <tbody id="vendBody"></tbody>
        </table>
      </div>
      <div class="ma-pager" id="vendPager" style="display:none">
        <button id="vendPrev" onclick="pagVend(-1)">&#8592; Ant.</button>
        <span class="ma-pager-info" id="vendPagerInfo"></span>
        <button id="vendNext" onclick="pagVend(1)">Próx. &#8594;</button>
      </div>
    </div>
  </div>

  <!-- ── TIP ──────────────────────────────────────── -->
  <div class="ma-tip">
    <div class="ma-tip-title">
      <span class="material-symbols-rounded" style="font-size:16px">lightbulb</span>
      Como configurar a atribuição nos anúncios Meta
    </div>
    <p style="font-size:12px;color:#888;margin:0">Adicione esses parâmetros na URL de destino do checkout:</p>
    <code>?utm_source=facebook&amp;utm_medium=paid&amp;utm_campaign={{campaign.name}}&amp;utm_content={{ad.name}}</code>
    <small>Os valores entre <strong>{{ }}</strong> são preenchidos automaticamente pelo Meta com o nome real de cada campanha e anúncio.</small>
  </div>

</div><!-- /container-fluid -->
</section>
</div><!-- /content-wrapper -->

<!-- ── MODAL CONFIG (admin) ──────────────────────── -->
<?php if ($isAdmin): ?>
<div class="modal fade" id="modalConfig" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background:#1a1a1a;border:1px solid #2a2a2a;border-radius:14px;overflow:hidden">
      <div class="modal-header" style="background:#111;border-bottom:1px solid #2a2a2a">
        <h5 class="modal-title" style="color:#FFDD00;font-size:15px;font-weight:700">
          <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:6px;font-size:18px">settings</span>
          Configurar App Meta
        </h5>
        <button type="button" class="close" data-dismiss="modal" style="color:#666">&times;</button>
      </div>
      <div class="modal-body" style="color:#ccc">
        <p style="font-size:12px;color:#666">
          Crie um App em <a href="https://developers.facebook.com/apps/" target="_blank" style="color:#FFDD00">developers.facebook.com</a>
          (tipo Business), ative o produto <strong style="color:#ccc">Marketing API</strong> e adicione a permissão <code style="background:#111;padding:2px 6px;border-radius:4px">ads_read</code>.
        </p>
        <div class="form-group">
          <label style="font-size:11px;font-weight:700;color:#888;text-transform:uppercase">App ID</label>
          <input type="text" id="cfgAppId" class="form-control form-control-sm"
                 style="background:#111;border-color:#333;color:#fff"
                 value="<?= htmlspecialchars($config->app_id ?? '') ?>" placeholder="1234567890">
        </div>
        <div class="form-group">
          <label style="font-size:11px;font-weight:700;color:#888;text-transform:uppercase">App Secret</label>
          <input type="password" id="cfgAppSecret" class="form-control form-control-sm"
                 style="background:#111;border-color:#333;color:#fff"
                 value="<?= htmlspecialchars($config->app_secret ?? '') ?>">
        </div>
        <div class="form-group mb-0">
          <label style="font-size:11px;font-weight:700;color:#888;text-transform:uppercase">Versão API</label>
          <input type="text" id="cfgVersao" class="form-control form-control-sm"
                 style="background:#111;border-color:#333;color:#fff;max-width:100px"
                 value="<?= htmlspecialchars($config->versao_api ?? 'v22.0') ?>">
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid #2a2a2a">
        <button class="ma-btn ma-btn-dark" data-dismiss="modal">Cancelar</button>
        <button class="ma-btn ma-btn-yellow" onclick="salvarConfig()">Salvar configuração</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
var _chart = null;

/* ── Helpers ─────────────────────────────────────── */
function brl(v) {
  return 'R$ ' + parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2});
}
function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function setPeriodo(dias) {
  var hoje = new Date(), ini = new Date();
  ini.setDate(hoje.getDate() - dias + 1);
  document.getElementById('filtroFim').value    = hoje.toISOString().slice(0,10);
  document.getElementById('filtroInicio').value = ini.toISOString().slice(0,10);
  carregarDados();
}

/* ── Paginação campanhas ──────────────────────────── */
var _campAll  = [];
var _campPage = 1;
var _campSize = 15;

function renderCampPage() {
  var lista = _campAll;
  var totalPag = Math.max(1, Math.ceil(lista.length / _campSize));
  if (_campPage > totalPag) _campPage = totalPag;

  var start  = (_campPage - 1) * _campSize;
  var slice  = lista.slice(start, start + _campSize);
  var totalR = lista.reduce(function(s,r){ return s + parseFloat(r.receita||0); }, 0);
  var maxRec = lista.reduce(function(m,r){ return Math.max(m, parseFloat(r.receita||0)); }, 0);

  var tbody = document.getElementById('campBody');
  tbody.innerHTML = '';
  slice.forEach(function(row) {
    var pct   = maxRec > 0 ? (parseFloat(row.receita)/maxRec*100).toFixed(1) : 0;
    var share = totalR > 0 ? (parseFloat(row.receita)/totalR*100).toFixed(0) : 0;
    var roas  = parseFloat(row.gasto_meta) > 0 ? (parseFloat(row.receita)/parseFloat(row.gasto_meta)).toFixed(2) : null;
    var roasCls = roas ? (roas>=3?'roas-hi':roas>=1.5?'roas-mid':'roas-lo') : '';
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td><span class="ma-badge ma-badge-blue" title="'+esc(row.campanha)+'">'+esc(row.campanha)+'</span></td>'+
      '<td><span class="ma-badge ma-badge-purple" title="'+esc(row.anuncio)+'">'+esc(row.anuncio)+'</span></td>'+
      '<td><span class="ma-badge ma-badge-gray">'+esc(row.fonte)+'</span></td>'+
      '<td class="r" style="font-weight:700;color:#fff">'+row.total_vendas+'</td>'+
      '<td class="r" style="font-weight:700;color:#FFDD00">'+brl(row.receita)+'</td>'+
      '<td><div style="display:flex;align-items:center;gap:6px">'+
        '<div class="ma-bar-wrap" style="flex:1"><div class="ma-bar" style="width:'+pct+'%"></div></div>'+
        '<span style="font-size:11px;color:#555;width:28px">'+share+'%</span></div></td>'+
      '<td class="r" style="color:#666">'+brl(row.ticket_medio)+'</td>'+
      '<td class="r">'+(parseFloat(row.gasto_meta)>0?brl(row.gasto_meta):'<span style="color:#333">—</span>')+'</td>'+
      '<td class="r '+roasCls+'">'+(roas?roas+'x':'<span style="color:#333">—</span>')+'</td>';
    tbody.appendChild(tr);
  });

  document.getElementById('campPagerInfo').textContent = 'Página '+_campPage+' de '+totalPag+' ('+lista.length+' linhas)';
  document.getElementById('campPrev').disabled = _campPage <= 1;
  document.getElementById('campNext').disabled = _campPage >= totalPag;
  document.getElementById('campPager').style.display = totalPag > 1 ? '' : 'none';
}

function pagCamp(dir) { _campPage += dir; renderCampPage(); }

/* ── Paginação vendas ─────────────────────────────── */
var _vendAll      = [];
var _vendFiltered = [];
var _vendPage     = 1;
var _vendSize     = 25;

function renderVendPage() {
  var lista    = _vendFiltered;
  var totalPag = Math.max(1, Math.ceil(lista.length / _vendSize));
  if (_vendPage > totalPag) _vendPage = totalPag;

  var start = (_vendPage - 1) * _vendSize;
  var slice = lista.slice(start, start + _vendSize);

  var tbody = document.getElementById('vendBody');
  tbody.innerHTML = '';
  slice.forEach(function(v) {
    var src = (v.utm_source||'').toLowerCase();
    var srcBadge  = src.includes('instagram') ? 'ma-badge-orange' : 'ma-badge-blue';
    var matchCls  = v.match_tipo==='url' ? 'ma-match-url' : v.match_tipo==='ip' ? 'ma-match-ip' : 'ma-match-none';
    var matchLbl  = v.match_tipo==='url' ? '🔗 URL' : v.match_tipo==='ip' ? '📡 IP' : '—';
    var matchTitle= v.match_tipo==='url' ? 'UTM direto na URL da venda' : v.match_tipo==='ip' ? 'Resolvido via pixel_lead pelo IP' : 'Sem atribuição';
    var tr = document.createElement('tr');
    tr.setAttribute('data-s', [v.comprador_nome,v.produto_nome,v.utm_campaign,v.utm_content,v.utm_source].join(' ').toLowerCase());
    tr.innerHTML =
      '<td style="color:#444;font-size:11px">#'+v.id+'</td>'+
      '<td style="white-space:nowrap;font-size:12px;color:#888">'+esc(v.data)+'</td>'+
      '<td><div style="font-size:13px;font-weight:600;color:#e0e0e0">'+esc(v.comprador_nome)+'</div>'+
           '<div style="font-size:11px;color:#555">'+esc(v.comprador_email)+'</div></td>'+
      '<td style="font-size:13px;color:#aaa">'+esc(v.produto_nome)+'</td>'+
      '<td class="r" style="font-weight:800;font-size:14px;color:#FFDD00">'+brl(v.valor_bruto)+'</td>'+
      '<td><span class="ma-badge ma-badge-blue" title="'+esc(v.utm_campaign||'—')+'">'+esc(v.utm_campaign||'—')+'</span></td>'+
      '<td><span class="ma-badge ma-badge-purple" title="'+esc(v.utm_content||'—')+'">'+esc(v.utm_content||'—')+'</span></td>'+
      '<td><span class="ma-badge '+srcBadge+'">'+esc(v.utm_source||'—')+'</span></td>'+
      '<td><span class="ma-match '+matchCls+'" title="'+matchTitle+'">'+matchLbl+'</span></td>';
    tbody.appendChild(tr);
  });

  document.getElementById('vendPagerInfo').textContent = 'Página '+_vendPage+' de '+totalPag+' ('+lista.length+' vendas)';
  document.getElementById('vendPrev').disabled = _vendPage <= 1;
  document.getElementById('vendNext').disabled = _vendPage >= totalPag;
  document.getElementById('vendPager').style.display = lista.length > _vendSize ? '' : 'none';
}

function pagVend(dir) { _vendPage += dir; renderVendPage(); }

function filtrarVendas() {
  var q = document.getElementById('searchVend').value.toLowerCase();
  _vendFiltered = q
    ? _vendAll.filter(function(v){ return (v._s||'').includes(q); })
    : _vendAll.slice();
  _vendPage = 1;
  if (!_vendFiltered.length) {
    document.getElementById('vendWrap').style.display='none';
    document.getElementById('vendPager').style.display='none';
    document.getElementById('vendEmpty').style.display='';
  } else {
    document.getElementById('vendEmpty').style.display='none';
    document.getElementById('vendWrap').style.display='';
    renderVendPage();
  }
}

/* ── Carregar dados ──────────────────────────────── */
function carregarDados() {
  var since = document.getElementById('filtroInicio').value;
  var until = document.getElementById('filtroFim').value;
  if (!since || !until) return;

  ['campLoading','vendLoading'].forEach(function(id){ document.getElementById(id).style.display=''; });
  ['campEmpty','campWrap','campPager','vendEmpty','vendWrap','vendPager'].forEach(function(id){
    document.getElementById(id).style.display='none';
  });
  document.getElementById('chartLoading').innerHTML = '<div class="ma-spinner"></div>';
  document.getElementById('chartLoading').style.display='';
  document.getElementById('chartReceita').style.display='none';

  fetch('<?= url("meta-ads/dados") ?>?since='+since+'&until='+until)
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (!res.success) return;
      renderCards(res.resumo);
      renderChart(res.por_dia);
      renderCampanhas(res.campanhas);
      renderVendasLista(res.vendas);
    })
    .catch(function(e){ console.error(e); });
}

/* ── Cards ───────────────────────────────────────── */
function renderCards(r) {
  document.getElementById('cReceita').textContent = brl(r.receita);
  document.getElementById('cVendas').textContent  = r.vendas;
  document.getElementById('cTicket').textContent  = brl(r.ticket);

  if (r.gasto > 0) {
    document.getElementById('cGasto').textContent    = brl(r.gasto);
    document.getElementById('cGastoSub').textContent = 'gasto no período';
  } else {
    document.getElementById('cGasto').textContent    = '—';
    document.getElementById('cGastoSub').textContent = 'sincronize para ver';
  }

  var roasEl = document.getElementById('cRoas');
  if (r.roas) {
    roasEl.textContent = r.roas + 'x';
    roasEl.className   = 'card-value ' + (r.roas>=3?'roas-hi':r.roas>=1.5?'roas-mid':'roas-lo');
  } else {
    roasEl.textContent = '—';
    roasEl.className   = 'card-value';
  }
}

/* ── Gráfico ─────────────────────────────────────── */
function renderChart(porDia) {
  document.getElementById('chartLoading').style.display = 'none';
  var canvas = document.getElementById('chartReceita');

  if (!porDia || !porDia.length) {
    document.getElementById('chartLoading').innerHTML = '<span style="color:#333;font-size:13px">Sem dados no período</span>';
    document.getElementById('chartLoading').style.display = '';
    return;
  }

  canvas.style.display = '';
  var labels   = porDia.map(function(d){ return d.dia.slice(5).replace('-','/'); });
  var receitas = porDia.map(function(d){ return parseFloat(d.receita); });

  if (_chart) _chart.destroy();
  _chart = new Chart(canvas, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Receita (R$)',
        data: receitas,
        borderColor: '#FFDD00',
        backgroundColor: 'rgba(255,221,0,0.07)',
        borderWidth: 2,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#FFDD00',
        pointRadius: 3,
        pointHoverRadius: 6,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a1a1a',
          borderColor: '#333',
          borderWidth: 1,
          titleColor: '#888',
          bodyColor: '#FFDD00',
          callbacks: { label: function(ctx){ return ' R$ '+ctx.raw.toLocaleString('pt-BR',{minimumFractionDigits:2}); } }
        }
      },
      scales: {
        x: { grid:{ color:'#1a1a1a' }, ticks:{ color:'#555', font:{ size:11 }, maxRotation:45 } },
        y: { grid:{ color:'#222' }, ticks:{ color:'#555', font:{ size:11 },
             callback: function(v){ return 'R$'+v.toLocaleString('pt-BR'); } } }
      }
    }
  });
}

/* ── Campanhas ───────────────────────────────────── */
function renderCampanhas(lista) {
  document.getElementById('campLoading').style.display = 'none';
  document.getElementById('badgeCamp').textContent = lista.length ? '— '+lista.length+' campanhas' : '';

  if (!lista.length) {
    document.getElementById('campEmpty').style.display = '';
    return;
  }

  _campAll  = lista;
  _campPage = 1;
  document.getElementById('campWrap').style.display = '';
  renderCampPage();
}

/* ── Vendas ──────────────────────────────────────── */
function renderVendasLista(lista) {
  document.getElementById('vendLoading').style.display = 'none';
  document.getElementById('badgeVend').textContent = lista.length ? '— '+lista.length+' vendas' : '';

  if (!lista.length) {
    document.getElementById('vendEmpty').style.display = '';
    return;
  }

  // pré-indexa campo de busca
  lista.forEach(function(v){
    v._s = [v.comprador_nome,v.produto_nome,v.utm_campaign,v.utm_content,v.utm_source].join(' ').toLowerCase();
  });
  _vendAll      = lista;
  _vendFiltered = lista.slice();
  _vendPage     = 1;
  document.getElementById('vendWrap').style.display = '';
  renderVendPage();
}

/* ── Toggle conta ────────────────────────────────── */
function toggleConta(accountId, ativa) {
  $.post('<?= url("meta-ads/conta") ?>', {account_id: accountId, ativa: ativa});
}

/* ── Sincronizar ─────────────────────────────────── */
function sincronizar() {
  var since = document.getElementById('filtroInicio').value || '<?= date('Y-m-d',strtotime('-30 days')) ?>';
  var until = document.getElementById('filtroFim').value   || '<?= date('Y-m-d') ?>';
  var btn   = document.getElementById('btnSincronizar');
  if (!btn) return;
  btn.disabled = true;
  btn.innerHTML = '<div class="ma-spinner"></div> Sincronizando...';

  fetch('<?= url("meta-ads/sincronizar") ?>?since='+since+'&until='+until)
    .then(function(r){ return r.json(); })
    .then(function(res){
      btn.disabled = false;
      btn.innerHTML = '<span class="material-symbols-rounded" style="font-size:16px">sync</span> Sincronizar gastos';
      if (res.success) { carregarDados(); }
      else { alert('Erro: '+(res.msg||'Tente novamente')); }
    })
    .catch(function(){
      btn.disabled = false;
      btn.innerHTML = '<span class="material-symbols-rounded" style="font-size:16px">sync</span> Sincronizar gastos';
    });
}

<?php if ($isAdmin): ?>
function salvarConfig() {
  var appId     = document.getElementById('cfgAppId').value.trim();
  var appSecret = document.getElementById('cfgAppSecret').value.trim();
  var versao    = document.getElementById('cfgVersao').value.trim();
  if (!appId || !appSecret) { alert('Preencha App ID e App Secret'); return; }
  $.post('<?= url("meta-ads/configurar") ?>', {app_id:appId,app_secret:appSecret,versao_api:versao}, function(r){
    try {
      var res = JSON.parse(r);
      if (res.success) { alert(res.msg); location.reload(); }
      else { alert('Erro: '+res.msg); }
    } catch(e) { alert('Erro inesperado'); }
  });
}
<?php endif; ?>

document.addEventListener('DOMContentLoaded', function(){
  carregarDados();
  <?php if (!empty($_GET['modal']) && $_GET['modal']==='config' && $isAdmin): ?>
  $('#modalConfig').modal('show');
  <?php endif; ?>
});
</script>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? WINTURB_NAME, ENT_QUOTES) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      /* ── Superfícies ── */
      --bg:      #f5f6fb;
      --bg2:     #ffffff;
      --bg3:     #eef0f8;
      --border:  #e8eaf3;
      --shadow:  0 1px 2px rgba(20,24,60,.04), 0 8px 24px -10px rgba(20,24,60,.09);
      --shadow-md: 0 2px 6px rgba(20,24,60,.06), 0 16px 36px -12px rgba(20,24,60,.16);

      /* ── Accent índigo ── */
      --accent:  #5b4df0;
      --accent2: #4a3de0;
      --accent-soft: rgba(91,77,240,.09);
      --accent-border: rgba(91,77,240,.24);
      --grad: linear-gradient(135deg, #5b4df0 0%, #6f5cf6 55%, #7c6bff 100%);

      /* ── Semânticas ── */
      --ok:      #12a765;
      --ok-soft: rgba(18,167,101,.11);
      --info:    #3b7cf6;
      --info-soft: rgba(59,124,246,.11);
      --warn:    #d98b0b;
      --warn-soft: rgba(217,139,11,.12);
      --danger:  #e5484d;
      --danger-soft: rgba(229,72,77,.09);

      /* ── Texto ── */
      --text:    #15173b;
      --muted:   #737797;

      /* ── Sidebar ── */
      --nav-bg:  #0e0c27;
      --nav-w:   232px;

      --radius:  16px;
      --radius-sm: 10px;
    }

    html, body { min-height: 100%; }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      font-size: .875rem;
      line-height: 1.45;
      display: flex;
      -webkit-font-smoothing: antialiased;
    }
    a { color: inherit; }
    :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

    /* ── Sidebar ──────────────────────────────────────────────── */
    .wt-sidebar {
      width: var(--nav-w);
      flex-shrink: 0;
      background:
        radial-gradient(120% 40% at 0% 0%, rgba(91,77,240,.22) 0%, transparent 60%),
        linear-gradient(180deg, #0e0c27 0%, #120f33 100%);
      display: flex;
      flex-direction: column;
      padding: 1.6rem 0 1rem;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 50;
    }

    .wt-logo {
      display: flex; align-items: baseline;
      padding: 0 1.5rem 2rem;
      font-size: 1.55rem;
      font-weight: 800;
      letter-spacing: -.035em;
      color: #fff;
      text-decoration: none;
    }
    .wt-logo em {
      font-style: normal;
      background: linear-gradient(90deg, #8f84ff, #5aa7ff);
      -webkit-background-clip: text; background-clip: text;
      color: transparent;
    }
    .wt-logo sup { font-size: .42rem; font-weight: 600; color: rgba(255,255,255,.4); margin-left: 1px; }

    .wt-nav {
      flex: 1; display: flex; flex-direction: column;
      gap: .2rem; padding: 0 .85rem;
    }
    .wt-nav a {
      display: flex; align-items: center; gap: .8rem;
      padding: .72rem .95rem;
      border-radius: 12px;
      color: rgba(255,255,255,.58);
      text-decoration: none;
      font-size: .875rem; font-weight: 600;
      transition: background .15s, color .15s;
    }
    .wt-nav a:hover { background: rgba(255,255,255,.06); color: #fff; }
    .wt-nav a.active {
      background: var(--grad);
      color: #fff;
      box-shadow: 0 8px 20px -8px rgba(91,77,240,.8), inset 0 1px 0 rgba(255,255,255,.18);
    }
    .wt-nav i { width: 18px; text-align: center; font-size: .95rem; }

    .wt-sidebar-footer {
      margin: 0 .85rem;
      padding-top: .9rem;
      border-top: 1px solid rgba(255,255,255,.08);
      display: flex; flex-direction: column; gap: .35rem;
    }
    .wt-me {
      display: flex; align-items: center; gap: .7rem;
      padding: .5rem .6rem;
      min-width: 0;
    }
    .wt-avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background: linear-gradient(135deg, #3b3a78, #5b4df0);
      color: #fff; font-size: .72rem; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      box-shadow: inset 0 0 0 1px rgba(255,255,255,.16);
    }
    .wt-me-name {
      font-size: .8rem; font-weight: 600; color: #fff;
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .wt-sidebar-footer a {
      display: flex; align-items: center; gap: .75rem;
      padding: .6rem .95rem;
      border-radius: 12px;
      color: rgba(255,255,255,.5);
      text-decoration: none;
      font-size: .84rem; font-weight: 600;
      transition: color .15s, background .15s;
    }
    .wt-sidebar-footer a i { width: 18px; text-align: center; }
    .wt-sidebar-footer a:hover { color: #ffb4b8; background: rgba(255,120,130,.09); }

    /* ── Main ─────────────────────────────────────────────────── */
    .wt-main {
      margin-left: var(--nav-w);
      flex: 1; min-width: 0; display: flex; flex-direction: column;
      min-height: 100vh;
    }

    .wt-topbar {
      display: flex; align-items: center; justify-content: space-between;
      gap: 1rem;
      padding: 1.6rem 2.2rem 0;
    }
    .wt-head { min-width: 0; }
    .wt-head h1 {
      font-size: 1.75rem; font-weight: 800;
      letter-spacing: -.03em; line-height: 1.15;
    }
    .wt-head p { color: var(--muted); margin-top: .3rem; font-size: .875rem; }
    .wt-actions { display: flex; align-items: center; gap: .65rem; flex-wrap: wrap; justify-content: flex-end; }

    .wt-content { padding: 1.6rem 2.2rem 2.4rem; flex: 1; animation: wt-in .35s ease-out both; }
    @keyframes wt-in { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) {
      .wt-content { animation: none; }
      * { transition-duration: .01ms !important; }
    }

    /* ── Cards ────────────────────────────────────────────────── */
    .wt-card {
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.4rem;
      box-shadow: var(--shadow);
    }
    .wt-card + .wt-card { margin-top: 1.1rem; }
    .wt-card-title {
      font-size: .95rem;
      font-weight: 700;
      letter-spacing: -.01em;
      color: var(--text);
      margin-bottom: 1rem;
    }

    /* ── Botões ───────────────────────────────────────────────── */
    .btn-wt {
      display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
      padding: .62rem 1.15rem;
      border-radius: var(--radius-sm);
      font-size: .84rem; font-weight: 700;
      background: var(--grad); color: #fff;
      border: none; cursor: pointer; text-decoration: none;
      font-family: inherit;
      box-shadow: 0 6px 16px -6px rgba(91,77,240,.7), inset 0 1px 0 rgba(255,255,255,.18);
      transition: filter .15s, transform .1s, box-shadow .18s;
    }
    .btn-wt:hover { filter: brightness(1.07); box-shadow: 0 10px 22px -8px rgba(91,77,240,.75), inset 0 1px 0 rgba(255,255,255,.18); }
    .btn-wt:active { transform: scale(.97); }

    .btn-wt-ghost {
      background: #fff;
      border: 1px solid var(--border);
      color: var(--text);
      box-shadow: 0 1px 2px rgba(20,24,60,.05);
    }
    .btn-wt-ghost:hover { background: var(--bg); filter: none; box-shadow: 0 1px 2px rgba(20,24,60,.05); }
    .btn-wt-danger {
      background: var(--danger-soft);
      border: 1px solid rgba(229,72,77,.2);
      color: var(--danger);
      box-shadow: none;
    }
    .btn-wt-danger:hover { background: rgba(229,72,77,.15); filter: none; box-shadow: none; }
    .btn-wt-sm { padding: .4rem .85rem; font-size: .79rem; border-radius: 8px; }

    /* ── Inputs ───────────────────────────────────────────────── */
    .wt-input, .wt-select, .wt-textarea {
      width: 100%;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--text);
      padding: .65rem .9rem;
      font-size: .875rem;
      font-family: inherit;
      transition: border-color .15s, box-shadow .15s;
    }
    .wt-input:focus, .wt-select:focus, .wt-textarea:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(91,77,240,.14);
    }
    .wt-textarea { resize: vertical; min-height: 80px; }
    .wt-label {
      display: block;
      font-size: .78rem; font-weight: 700;
      color: var(--text);
      margin-bottom: .4rem;
    }
    .wt-field { margin-bottom: 1rem; }

    /* ── Busca (topbar) ───────────────────────────────────────── */
    .wt-search {
      display: flex; align-items: center; gap: .55rem;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: .58rem .9rem;
      width: 260px;
      box-shadow: 0 1px 2px rgba(20,24,60,.04);
      transition: border-color .15s, box-shadow .15s;
    }
    .wt-search:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(91,77,240,.12); }
    .wt-search i { color: var(--muted); font-size: .85rem; }
    .wt-search input {
      background: none; border: none; outline: none;
      color: var(--text); font-family: inherit; font-size: .84rem; width: 100%;
    }
    .wt-search input::placeholder { color: var(--muted); }

    /* ── Grid ─────────────────────────────────────────────────── */
    .wt-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
    .wt-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.1rem; }
    @media (max-width: 900px) { .wt-grid-2, .wt-grid-3 { grid-template-columns: 1fr; } }

    /* ── Table ────────────────────────────────────────────────── */
    .wt-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .wt-table th {
      padding: .7rem .9rem; text-align: left;
      border-bottom: 1px solid var(--border);
      color: var(--muted); font-weight: 700;
      font-size: .74rem;
      background: transparent;
    }
    .wt-table td {
      padding: .85rem .9rem; text-align: left;
      border-bottom: 1px solid var(--border);
      color: var(--text);
    }
    .wt-table tbody tr { transition: background .12s; }
    .wt-table tbody tr:hover td { background: var(--bg); }
    .wt-table tr:last-child td { border-bottom: none; }

    /* ── Stat card ────────────────────────────────────────────── */
    .wt-stat { display: flex; flex-direction: column; gap: .4rem; }
    .wt-stat-value {
      font-size: 2rem; font-weight: 800; line-height: 1;
      letter-spacing: -.03em;
      color: var(--text);
    }
    .wt-stat-label { font-size: .78rem; font-weight: 600; color: var(--muted); }

    /* ── Preset buttons ───────────────────────────────────────── */
    .wt-preset-btn {
      padding: .32rem .8rem;
      font-size: .76rem; font-weight: 600; font-family: inherit;
      border-radius: 99px;
      border: 1px solid var(--border);
      background: #fff;
      color: var(--muted);
      cursor: pointer;
      transition: background .15s, border-color .15s, color .15s;
    }
    .wt-preset-btn:hover { background: var(--accent-soft); border-color: var(--accent-border); color: var(--accent); }

    /* ── Divider ──────────────────────────────────────────────── */
    .wt-divider { border: none; border-top: 1px solid var(--border); margin: 1.2rem 0; }

    /* ── Badge / status ───────────────────────────────────────── */
    .wt-badge {
      display: inline-flex; align-items: center; gap: .35rem;
      padding: .22rem .65rem; border-radius: 99px;
      font-size: .72rem; font-weight: 700;
      background: var(--accent-soft);
      color: var(--accent);
    }
    .wt-badge.ok   { background: var(--ok-soft);   color: var(--ok); }
    .wt-badge.info { background: var(--info-soft); color: var(--info); }
    .wt-badge.warn { background: var(--warn-soft); color: var(--warn); }
    .wt-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* ── Toast ────────────────────────────────────────────────── */
    #wt-toast {
      position: fixed; bottom: 1.5rem; right: 1.5rem;
      z-index: 9999; display: flex; flex-direction: column; gap: .5rem;
    }
    .wt-toast-item {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: .75rem 1.1rem;
      font-size: .84rem; font-weight: 500;
      display: flex; align-items: center; gap: .6rem;
      min-width: 240px;
      box-shadow: var(--shadow-md);
      color: var(--text);
      transform: translateX(120%); opacity: 0;
      transition: transform .3s cubic-bezier(.22,1,.36,1), opacity .3s;
    }
    .wt-toast-item.show { transform: translateX(0); opacity: 1; }
    .wt-toast-item.ok  i { color: var(--ok); }
    .wt-toast-item.err i { color: var(--danger); }

    /* ── Hamburger ────────────────────────────────────────────── */
    .wt-hamburger {
      display: none;
      background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm);
      cursor: pointer; width: 40px; height: 40px;
      color: var(--text); font-size: 1rem;
      align-items: center; justify-content: center;
      flex-shrink: 0;
    }


    /* ── KPI / painéis / miniaturas (compartilhados) ─────────── */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.1rem; margin-bottom: 1.1rem; }
    @media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 540px)  { .kpi-grid { grid-template-columns: 1fr; } }

    .kpi-card {
      background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
      padding: 1.15rem 1.25rem 1rem; box-shadow: var(--shadow);
      display: grid; grid-template-columns: auto 1fr; gap: .2rem 1rem; align-items: center;
    }
    .kpi-icon {
      width: 48px; height: 48px; border-radius: 14px; grid-row: span 2;
      display: flex; align-items: center; justify-content: center; font-size: 1.05rem;
    }
    .kpi-icon.purple { background: var(--accent-soft); color: var(--accent); }
    .kpi-icon.blue   { background: var(--info-soft);   color: var(--info); }
    .kpi-icon.green  { background: var(--ok-soft);     color: var(--ok); }
    .kpi-icon.rose   { background: var(--danger-soft); color: var(--danger); }
    .kpi-lbl { font-size: .8rem; font-weight: 600; color: var(--muted); align-self: end; }
    .kpi-val { font-size: 1.9rem; font-weight: 800; letter-spacing: -.035em; line-height: 1.05; align-self: start; }
    .kpi-foot {
      grid-column: 1 / -1; display: flex; align-items: flex-end; justify-content: space-between;
      gap: .6rem; margin-top: .55rem;
    }
    .kpi-cmp { font-size: .72rem; color: var(--muted); line-height: 1.3; }
    .kpi-delta {
      display: inline-flex; align-items: center; gap: .3rem;
      font-size: .82rem; font-weight: 700; margin-right: .4rem;
    }
    .kpi-delta.up   { color: var(--ok); }
    .kpi-delta.down { color: var(--danger); }
    .kpi-delta i { font-size: .72rem; }
    .kpi-cmp .kpi-delta { display: flex; margin: 0 0 .15rem; }

    /* ── Painéis ─────────────────────────────────────────────── */

    .panel {
      background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
      box-shadow: var(--shadow); padding: 1.25rem 1.35rem; min-width: 0;
    }
    .panel-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
    .panel-title { display: flex; align-items: center; gap: .7rem; }
    .panel-title > i { color: var(--accent); font-size: 1.1rem; }
    .panel-title h2 { font-size: 1rem; font-weight: 700; letter-spacing: -.01em; line-height: 1.2; }
    .panel-title p { font-size: .76rem; color: var(--muted); margin-top: .15rem; }
    .panel-link { font-size: .8rem; font-weight: 700; color: var(--accent); text-decoration: none; white-space: nowrap; }
    .panel-link:hover { text-decoration: underline; }

    .metric-select {
      appearance: none; -webkit-appearance: none;
      background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none' stroke='%23737797' stroke-width='1.6'%3E%3Cpath d='m1 1 4 4 4-4'/%3E%3C/svg%3E") no-repeat right .8rem center;
      border: 1px solid var(--border); border-radius: 9px;
      font-family: inherit; font-size: .8rem; font-weight: 600; color: var(--text);
      padding: .45rem 2rem .45rem .8rem; cursor: pointer;
    }

    .thumb {
      position: relative; width: 64px; aspect-ratio: 16/10; border-radius: 8px; overflow: hidden;
      background: linear-gradient(135deg, #2a2660, #14122f); display: flex; align-items: center; justify-content: center;
      color: rgba(255,255,255,.35); font-size: .8rem;
    }
    .thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .bar { height: 5px; background: var(--bg3); border-radius: 99px; overflow: hidden; margin-top: .3rem; }
    .bar > i { display: block; height: 100%; border-radius: 99px; background: var(--grad); }
    .bar.ok > i { background: linear-gradient(90deg, #12a765, #34d399); }

    /* ── Sparkline ────────────────────────────────────────────── */
    .spark { display: block; overflow: visible; }

    /* ── Overlay ──────────────────────────────────────────────── */
    #wt-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(10,8,30,.5);
      z-index: 49;
      backdrop-filter: blur(2px);
    }
    #wt-overlay.show { display: block; }

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width: 768px) {
      .wt-hamburger { display: inline-flex; }
      .wt-sidebar {
        transform: translateX(-100%);
        transition: transform .25s cubic-bezier(.22,1,.36,1);
      }
      .wt-sidebar.open { transform: translateX(0); box-shadow: 4px 0 32px rgba(0,0,0,.4); }
      .wt-main { margin-left: 0; }
      .wt-content { padding: 1.2rem 1rem 2rem; }
      .wt-topbar { padding: 1.1rem 1rem 0; flex-wrap: wrap; }
      .wt-head h1 { font-size: 1.4rem; }
      .wt-actions { width: 100%; justify-content: flex-start; }
      .wt-search { width: 100%; }
    }
  </style>
  <?= $v->section("styles") ?>
</head>
<body>

<nav class="wt-sidebar" aria-label="Menu principal">
  <a class="wt-logo" href="<?= url("dashboard") ?>">Win<em>Turb</em><sup>®</sup></a>
  <div class="wt-nav">
    <a href="<?= url("dashboard") ?>"><i class="fas fa-table-cells-large"></i> Dashboard</a>
    <a href="<?= url("videos") ?>"><i class="fas fa-circle-play"></i> Vídeos</a>
  </div>
  <div class="wt-sidebar-footer">
    <div class="wt-me">
      <span class="wt-avatar"><?= htmlspecialchars(wt_iniciais($usuario ?? ""), ENT_QUOTES) ?></span>
      <span class="wt-me-name"><?= htmlspecialchars($usuario ?? "", ENT_QUOTES) ?></span>
    </div>
    <a href="<?= url("logout") ?>"><i class="fas fa-arrow-right-from-bracket"></i> Sair</a>
  </div>
</nav>

<div id="wt-overlay"></div>

<div class="wt-main">
  <header class="wt-topbar">
    <div style="display:flex;align-items:center;gap:.8rem;min-width:0;">
      <button class="wt-hamburger" id="wt-hamburger" aria-label="Abrir menu">
        <i class="fas fa-bars"></i>
      </button>
      <div class="wt-head">
        <?= $v->section("page_head", '<h1>' . htmlspecialchars($title ?? WINTURB_NAME, ENT_QUOTES) . '</h1>') ?>
      </div>
    </div>
    <div class="wt-actions"><?= $v->section("actions") ?></div>
  </header>
  <main class="wt-content">
    <?= $v->section("content") ?>
  </main>
</div>

<div id="wt-toast" role="status" aria-live="polite"></div>

<script>
(function() {
  var btn     = document.getElementById("wt-hamburger");
  var sidebar = document.querySelector(".wt-sidebar");
  var overlay = document.getElementById("wt-overlay");
  if (!btn) return;
  function open()  { sidebar.classList.add("open");    overlay.classList.add("show");    }
  function close() { sidebar.classList.remove("open"); overlay.classList.remove("show"); }
  btn.addEventListener("click", function() { sidebar.classList.contains("open") ? close() : open(); });
  overlay.addEventListener("click", close);
  document.querySelectorAll(".wt-nav a, .wt-sidebar-footer a").forEach(function(a) { a.addEventListener("click", close); });
})();

(function() {
  var p = location.pathname;
  document.querySelectorAll('.wt-nav a').forEach(function(a) {
    var href = a.getAttribute('href');
    if (!href) return;
    var ap = new URL(href, location.origin).pathname;
    if (p === ap || (ap !== '/' && p.startsWith(ap))) a.classList.add('active');
  });
})();

function wtToast(msg, type) {
  var t = document.createElement("div");
  t.className = "wt-toast-item " + (type === "ok" ? "ok" : "err");
  t.innerHTML = '<i class="fas ' + (type === "ok" ? "fa-check-circle" : "fa-circle-xmark") + '"></i> ' + msg;
  document.getElementById("wt-toast").appendChild(t);
  requestAnimationFrame(function() { requestAnimationFrame(function() { t.classList.add("show"); }); });
  setTimeout(function() { t.classList.remove("show"); setTimeout(function() { t.remove(); }, 350); }, 3200);
}
</script>

<?= $v->section("scripts") ?>

</body>
</html>

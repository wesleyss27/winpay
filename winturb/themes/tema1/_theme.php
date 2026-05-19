<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? WINTURB_NAME, ENT_QUOTES) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      /* ── Paleta clara (estilo VTurb) ── */
      --bg:      #f2f3f8;
      --bg2:     #ffffff;
      --bg3:     #eaecf5;
      --border:  rgba(0,0,0,.08);
      --shadow:  0 1px 3px rgba(0,0,0,.06), 0 4px 14px rgba(0,0,0,.05);
      --shadow-md: 0 2px 8px rgba(0,0,0,.08), 0 8px 24px rgba(0,0,0,.06);

      /* ── Accent violeta (VTurb) ── */
      --accent:  #6c5ce7;
      --accent2: #5a4bd6;
      --accent-soft: rgba(108,92,231,.1);
      --accent-border: rgba(108,92,231,.25);

      /* ── Texto ── */
      --text:    #1e1c3a;
      --muted:   rgba(30,28,58,.42);

      /* ── Sidebar ── */
      --nav-bg:  #13112b;
      --nav-w:   220px;

      --radius:  12px;
    }

    html, body { height: 100%; }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Outfit', sans-serif;
      font-size: .9rem;
      display: flex;
    }

    /* ── Sidebar ──────────────────────────────────────────────── */
    .wt-sidebar {
      width: var(--nav-w);
      flex-shrink: 0;
      background: var(--nav-bg);
      display: flex;
      flex-direction: column;
      padding: 1.5rem 0 1rem;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 50;
    }

    .wt-logo {
      padding: 0 1.3rem 1.8rem;
      font-family: 'Syne', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      letter-spacing: -.02em;
      color: #fff;
    }
    .wt-logo em { font-style: normal; color: var(--accent); }
    .wt-logo-dot {
      display: inline-block;
      width: 5px; height: 5px;
      background: var(--accent);
      border-radius: 50%;
      margin-left: 2px;
      vertical-align: middle;
      position: relative; top: -2px;
      animation: blink-dot 2.8s ease-in-out infinite;
    }
    @keyframes blink-dot {
      0%,100% { opacity: 1; transform: scale(1); }
      50%      { opacity: .4; transform: scale(.55); }
    }

    .wt-nav-label {
      font-size: .6rem;
      font-weight: 700;
      letter-spacing: .16em;
      text-transform: uppercase;
      color: rgba(255,255,255,.2);
      padding: 0 1.3rem .4rem;
    }

    .wt-nav {
      flex: 1; display: flex; flex-direction: column;
      gap: .05rem; padding: 0 .7rem;
    }

    .wt-nav a {
      display: flex; align-items: center; gap: .68rem;
      padding: .58rem .85rem;
      border-radius: 9px;
      color: rgba(255,255,255,.5);
      text-decoration: none;
      font-size: .84rem; font-weight: 500;
      transition: background .15s, color .15s;
    }
    .wt-nav a:hover {
      background: rgba(255,255,255,.07);
      color: rgba(255,255,255,.88);
    }
    .wt-nav a.active {
      background: rgba(108,92,231,.22);
      color: #c4b8ff;
    }
    .wt-nav a.active::before {
      content: '';
      position: absolute; left: 0; top: 20%; bottom: 20%;
      width: 3px;
      background: var(--accent);
      border-radius: 0 3px 3px 0;
    }
    .wt-nav a { position: relative; }
    .wt-nav i { width: 17px; text-align: center; font-size: .85rem; }

    .wt-sidebar-footer {
      padding: .9rem .7rem 0;
      border-top: 1px solid rgba(255,255,255,.07);
    }
    .wt-sidebar-footer a {
      display: flex; align-items: center; gap: .65rem;
      padding: .55rem .85rem;
      border-radius: 9px;
      color: rgba(255,255,255,.38);
      text-decoration: none;
      font-size: .82rem;
      transition: color .15s, background .15s;
    }
    .wt-sidebar-footer a:hover {
      color: #fda4af;
      background: rgba(253,164,175,.08);
    }

    /* ── Main ─────────────────────────────────────────────────── */
    .wt-main {
      margin-left: var(--nav-w);
      flex: 1; display: flex; flex-direction: column;
      min-height: 100vh;
    }

    .wt-topbar {
      display: flex; align-items: center; justify-content: space-between;
      padding: .78rem 2rem;
      background: #fff;
      border-bottom: 1px solid var(--border);
      position: sticky; top: 0; z-index: 40;
      box-shadow: 0 1px 0 var(--border);
    }
    .wt-topbar-title {
      font-size: .88rem; font-weight: 600; color: var(--text);
    }
    .wt-topbar-user {
      display: flex; align-items: center; gap: .5rem;
      font-size: .78rem; color: var(--muted);
      background: var(--bg3);
      border: 1px solid var(--border);
      padding: .28rem .82rem;
      border-radius: 99px;
    }
    .wt-topbar-user i { color: var(--accent); font-size: .8rem; }

    .wt-content { padding: 1.8rem 2rem; flex: 1; }

    @keyframes wt-in {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .wt-content > * { animation: wt-in .32s cubic-bezier(.22,1,.36,1) both; }
    .wt-content > *:nth-child(2) { animation-delay: .05s; }
    .wt-content > *:nth-child(3) { animation-delay: .10s; }
    .wt-content > *:nth-child(4) { animation-delay: .15s; }
    .wt-content > *:nth-child(5) { animation-delay: .20s; }

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
      font-size: .7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .11em;
      color: var(--muted);
      margin-bottom: 1rem;
    }

    /* ── Botões ───────────────────────────────────────────────── */
    .btn-wt {
      display: inline-flex; align-items: center; gap: .48rem;
      padding: .52rem 1.05rem;
      border-radius: 9px;
      font-size: .84rem; font-weight: 600;
      background: var(--accent); color: #fff;
      border: none; cursor: pointer; text-decoration: none;
      font-family: 'Outfit', sans-serif;
      transition: background .15s, transform .1s, box-shadow .18s;
    }
    .btn-wt:hover {
      background: var(--accent2);
      box-shadow: 0 4px 16px rgba(108,92,231,.3);
    }
    .btn-wt:active { transform: scale(.97); }

    .btn-wt-ghost {
      background: #fff;
      border: 1px solid var(--border);
      color: var(--text);
      box-shadow: var(--shadow);
    }
    .btn-wt-ghost:hover {
      background: var(--bg3);
      box-shadow: var(--shadow);
    }
    .btn-wt-danger {
      background: #fff4f4;
      border: 1px solid rgba(239,68,68,.18);
      color: #dc2626;
    }
    .btn-wt-danger:hover {
      background: #fee2e2;
      box-shadow: none;
    }
    .btn-wt-sm { padding: .28rem .72rem; font-size: .78rem; border-radius: 7px; }

    /* ── Inputs ───────────────────────────────────────────────── */
    .wt-input, .wt-select, .wt-textarea {
      width: 100%;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 9px;
      color: var(--text);
      padding: .58rem .85rem;
      font-size: .87rem;
      font-family: 'Outfit', sans-serif;
      transition: border-color .15s, box-shadow .15s;
      box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .wt-input:focus, .wt-select:focus, .wt-textarea:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(108,92,231,.12);
    }
    .wt-textarea { resize: vertical; min-height: 80px; }
    .wt-label {
      display: block;
      font-size: .7rem; font-weight: 700;
      letter-spacing: .09em; text-transform: uppercase;
      color: var(--muted);
      margin-bottom: .38rem;
    }
    .wt-field { margin-bottom: 1rem; }

    /* ── Grid ─────────────────────────────────────────────────── */
    .wt-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
    .wt-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.1rem; }
    @media (max-width: 900px) { .wt-grid-2, .wt-grid-3 { grid-template-columns: 1fr; } }

    /* ── Table ────────────────────────────────────────────────── */
    .wt-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .wt-table th {
      padding: .65rem .9rem; text-align: left;
      border-bottom: 1px solid var(--border);
      color: var(--muted); font-weight: 700;
      text-transform: uppercase; font-size: .67rem; letter-spacing: .1em;
      background: var(--bg);
    }
    .wt-table td {
      padding: .8rem .9rem; text-align: left;
      border-bottom: 1px solid var(--border);
      color: var(--text);
    }
    .wt-table tbody tr { transition: background .12s; }
    .wt-table tbody tr:hover td { background: var(--accent-soft); }
    .wt-table tr:last-child td { border-bottom: none; }

    /* ── Stat card ────────────────────────────────────────────── */
    .wt-stat { display: flex; flex-direction: column; gap: .4rem; }
    .wt-stat-value {
      font-family: 'Syne', sans-serif;
      font-size: 2.1rem; font-weight: 700; line-height: 1;
      color: var(--accent);
    }
    .wt-stat-label {
      font-size: .7rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .1em;
      color: var(--muted);
    }

    /* ── Preset buttons ───────────────────────────────────────── */
    .wt-preset-btn {
      padding: .28rem .72rem;
      font-size: .75rem; font-family: 'Outfit', sans-serif;
      border-radius: 99px;
      border: 1px solid var(--border);
      background: #fff;
      color: var(--muted);
      cursor: pointer;
      box-shadow: 0 1px 2px rgba(0,0,0,.05);
      transition: background .15s, border-color .15s, color .15s, box-shadow .15s;
    }
    .wt-preset-btn:hover {
      background: var(--accent-soft);
      border-color: var(--accent-border);
      color: var(--accent);
    }

    /* ── Divider ──────────────────────────────────────────────── */
    .wt-divider { border: none; border-top: 1px solid var(--border); margin: 1.2rem 0; }

    /* ── Badge ────────────────────────────────────────────────── */
    .wt-badge {
      display: inline-flex; align-items: center; gap: .3rem;
      padding: .18rem .55rem; border-radius: 99px;
      font-size: .68rem; font-weight: 700;
      background: var(--accent-soft);
      border: 1px solid var(--accent-border);
      color: var(--accent);
    }

    /* ── Toast ────────────────────────────────────────────────── */
    #wt-toast {
      position: fixed; bottom: 1.5rem; right: 1.5rem;
      z-index: 9999; display: flex; flex-direction: column; gap: .4rem;
    }
    .wt-toast-item {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 11px;
      padding: .65rem 1.1rem;
      font-size: .83rem;
      display: flex; align-items: center; gap: .55rem;
      min-width: 220px;
      box-shadow: var(--shadow-md);
      color: var(--text);
      transform: translateX(120%); opacity: 0;
      transition: transform .3s cubic-bezier(.22,1,.36,1), opacity .3s;
    }
    .wt-toast-item.show { transform: translateX(0); opacity: 1; }
    .wt-toast-item.ok  { border-left: 3px solid #10b981; }
    .wt-toast-item.ok i { color: #10b981; }
    .wt-toast-item.err { border-left: 3px solid #ef4444; }
    .wt-toast-item.err i { color: #ef4444; }

    /* ── Hamburger ────────────────────────────────────────────── */
    .wt-hamburger {
      display: none;
      background: none; border: none;
      cursor: pointer; padding: .3rem;
      color: var(--text); font-size: 1.1rem;
      margin-right: .4rem;
    }

    /* ── Overlay ──────────────────────────────────────────────── */
    #wt-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,.45);
      z-index: 49;
      backdrop-filter: blur(2px);
    }
    #wt-overlay.show { display: block; }

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width: 768px) {
      .wt-hamburger { display: inline-flex; align-items: center; }
      .wt-sidebar {
        transform: translateX(-100%);
        transition: transform .25s cubic-bezier(.22,1,.36,1);
      }
      .wt-sidebar.open {
        transform: translateX(0);
        box-shadow: 4px 0 32px rgba(0,0,0,.4);
      }
      .wt-main { margin-left: 0; }
      .wt-content { padding: 1.2rem; }
      .wt-topbar { padding: .78rem 1.2rem; }
    }
  </style>
  <?= $v->section("styles") ?>
</head>
<body>

<nav class="wt-sidebar">
  <div class="wt-logo">Win<em>Turb</em><span class="wt-logo-dot"></span></div>
  <div class="wt-nav-label">Menu</div>
  <div class="wt-nav">
    <a href="<?= url("dashboard") ?>"><i class="fas fa-chart-pie"></i> Dashboard</a>
    <a href="<?= url("videos") ?>"><i class="fas fa-film"></i> Meus Vídeos</a>
  </div>
  <div class="wt-sidebar-footer">
    <a href="<?= url("logout") ?>"><i class="fas fa-arrow-right-from-bracket"></i> Sair</a>
  </div>
</nav>

<div id="wt-overlay"></div>

<div class="wt-main">
  <header class="wt-topbar">
    <button class="wt-hamburger" id="wt-hamburger" aria-label="Menu">
      <i class="fas fa-bars"></i>
    </button>
    <span class="wt-topbar-title"><?= htmlspecialchars($title ?? WINTURB_NAME, ENT_QUOTES) ?></span>
    <span class="wt-topbar-user">
      <i class="fas fa-user-circle"></i>
      <?= htmlspecialchars($usuario ?? "", ENT_QUOTES) ?>
    </span>
  </header>
  <main class="wt-content">
    <?= $v->section("content") ?>
  </main>
</div>

<div id="wt-toast"></div>

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
  t.innerHTML = '<i class="fas ' + (type === "ok" ? "fa-check-circle" : "fa-times-circle") + '"></i> ' + msg;
  document.getElementById("wt-toast").appendChild(t);
  requestAnimationFrame(function() { requestAnimationFrame(function() { t.classList.add("show"); }); });
  setTimeout(function() { t.classList.remove("show"); setTimeout(function() { t.remove(); }, 350); }, 3200);
}
</script>

<?= $v->section("scripts") ?>

</body>
</html>

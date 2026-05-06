<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'Área de Membros' ?></title>
  <link rel="icon" href="<?= url("themes/imgs/logo-icon.png") ?>">
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">
  <link href="<?= url("themes/tema1/dist/css/font-inter.css") ?>" rel="stylesheet">
  <?= $v->section("links") ?>
</head>
<?php
/* ── Tema injetado via variáveis CSS ── */
$vmAccent = !empty($curso->tema_cor_primaria) ? $curso->tema_cor_primaria : '#f4d03f';
$vmBg     = !empty($curso->tema_cor_fundo)    ? $curso->tema_cor_fundo    : '#0d0f14';
$vmText   = !empty($curso->tema_cor_texto)     ? $curso->tema_cor_texto    : '#f0f0f0';

/* rgb helpers */
$toRgb = function($hex) {
    $hex = ltrim($hex,'#');
    if (strlen($hex)===3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    [$r,$g,$b] = array_map('hexdec', str_split($hex,2));
    return "$r,$g,$b";
};
$rgbAccent = $toRgb($vmAccent);
$rgbBg     = $toRgb($vmBg);

$nomeExibir = !empty($curso->tema_titulo_exibicao) ? $curso->tema_titulo_exibicao : ($curso->nome ?? '');
$exibirTit  = ($curso->tema_exibir_titulo ?? 1) == 1;

$logoUrl = !empty($curso->tema_logo)  ? url("uploads/cursos/" . $curso->tema_logo)
         : (!empty($curso->imagem)    ? url("uploads/cursos/" . $curso->imagem) : '');

$nomeAluno  = $_SESSION['nome_abreviado'] ?? $_SESSION['nome'] ?? 'Aluno';
$fotoAluno  = !empty($_SESSION['foto']) ? url_octa("uploads/perfil/" . $_SESSION['foto']) : '';

/* Progresso geral */
$totalAulas = 0; $aulasConcluidas = 0;
if (!empty($modulosData)) {
    foreach ($modulosData as $item) {
        $totalAulas      += (int)$item['total'];
        $aulasConcluidas += (int)$item['concluidas'];
    }
}
$pctProgresso = $totalAulas > 0 ? round(($aulasConcluidas / $totalAulas) * 100) : 0;

/* Acesso restante (baseado em created_at + 365 dias como fallback) */
$pctAcesso = 100;
if (!empty($turma) && !empty($turma->created_at)) {
    $criado    = strtotime($turma->created_at);
    $duracao   = 365 * 86400; // 1 ano padrão
    $decorrido = time() - $criado;
    $pctAcesso = max(0, min(100, 100 - round(($decorrido / $duracao) * 100)));
}
?>
<body style="
  --vm-accent: <?= $vmAccent ?>;
  --vm-accent-rgb: <?= $rgbAccent ?>;
  --vm-bg: <?= $vmBg ?>;
  --vm-bg-rgb: <?= $rgbBg ?>;
  --vm-text: <?= $vmText ?>;
  margin:0; padding:0;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  background: var(--vm-bg);
  color: var(--vm-text);
  overflow-x: hidden;
">
<style>
*, *::before, *::after { box-sizing: border-box; }
a { text-decoration: none; color: inherit; }
button { border: none; background: none; cursor: pointer; font-family: inherit; }

/* ── WRAPPER PRINCIPAL ── */
.vm-wrapper {
  display: flex;
  min-height: 100vh;
}

/* ── SIDEBAR ── */
.vm-sidebar {
  width: 240px;
  min-height: 100vh;
  flex-shrink: 0;
  background: color-mix(in srgb, var(--vm-bg) 92%, #000 8%);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0; left: 0; bottom: 0;
  z-index: 100;
  overflow-y: auto;
  scrollbar-width: none;
}
.vm-sidebar::-webkit-scrollbar { display: none; }

/* ── LOGO ── */
.vm-sb-brand {
  padding: 1.8rem 1.4rem 1.4rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .75rem;
}
.vm-sb-logo-ring {
  position: relative;
  width: 78px; height: 78px;
  flex-shrink: 0;
}
.vm-sb-logo-ring::before {
  content: '';
  position: absolute; inset: -4px;
  border-radius: 50%;
  background: conic-gradient(
    var(--vm-accent) 0%,
    rgba(var(--vm-accent-rgb), .15) 60%,
    var(--vm-accent) 100%
  );
  animation: sb-spin 6s linear infinite;
}
.vm-sb-logo-ring::after {
  content: '';
  position: absolute; inset: -2px;
  border-radius: 50%;
  background: color-mix(in srgb, var(--vm-bg) 92%, #000 8%);
}
@keyframes sb-spin { to { transform: rotate(360deg); } }
.vm-sb-logo {
  position: relative; z-index: 1;
  width: 78px; height: 78px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
}
.vm-sb-logo-ph {
  position: relative; z-index: 1;
  width: 78px; height: 78px;
  border-radius: 50%;
  background: rgba(var(--vm-accent-rgb), .12);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.8rem; color: var(--vm-accent);
}
.vm-sb-course-name {
  font-size: .8rem;
  font-weight: 800;
  letter-spacing: .05em;
  text-align: center;
  color: var(--vm-text);
  line-height: 1.3;
  max-width: 180px;
}

/* ── USUÁRIO + PROGRESSO ── */
.vm-sb-user {
  margin: 0 .9rem .4rem;
  padding: 1rem 1rem .9rem;
  background: rgba(255,255,255,.04);
  border-radius: 14px;
  border: 1px solid rgba(255,255,255,.06);
}
.vm-sb-user-row {
  display: flex; align-items: center; gap: .65rem;
  margin-bottom: .9rem;
}
.vm-sb-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  object-fit: cover; flex-shrink: 0;
  border: 2px solid rgba(var(--vm-accent-rgb), .4);
}
.vm-sb-avatar-ph {
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(var(--vm-accent-rgb), .18);
  border: 2px solid rgba(var(--vm-accent-rgb), .35);
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem; font-weight: 800;
  color: var(--vm-accent); flex-shrink: 0;
}
.vm-sb-user-name {
  font-size: .8rem; font-weight: 700;
  color: var(--vm-text);
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

/* Progresso como stat numérico */
.vm-sb-stat-row {
  display: flex; gap: .5rem;
}
.vm-sb-stat {
  flex: 1;
  background: rgba(255,255,255,.04);
  border-radius: 9px;
  padding: .45rem .5rem;
  border: 1px solid rgba(255,255,255,.05);
}
.vm-sb-stat-val {
  font-size: .88rem; font-weight: 900;
  color: var(--vm-accent);
  line-height: 1;
}
.vm-sb-stat-lbl {
  font-size: .55rem; font-weight: 600;
  letter-spacing: .06em; text-transform: uppercase;
  color: rgba(255,255,255,.35);
  margin-top: .2rem;
}
/* thin bar abaixo */
.vm-sb-thin-bar {
  margin-top: .8rem;
  height: 3px; border-radius: 999px;
  background: rgba(255,255,255,.08); overflow: hidden;
}
.vm-sb-thin-fill {
  height: 100%; border-radius: 999px;
  background: linear-gradient(90deg, rgba(var(--vm-accent-rgb),.5), var(--vm-accent));
  transition: width .6s ease;
}

/* ── NAV (pills) ── */
.vm-sb-nav {
  flex: 1;
  padding: .5rem .9rem;
  display: flex; flex-direction: column; gap: .2rem;
}
.vm-sb-nav-item {
  display: flex; align-items: center; gap: .7rem;
  padding: .7rem .9rem;
  border-radius: 10px;
  font-size: .8rem; font-weight: 500;
  color: rgba(255,255,255,.45);
  text-decoration: none;
  transition: background .18s ease, color .18s ease;
}
.vm-sb-nav-item:hover {
  background: rgba(255,255,255,.06);
  color: rgba(255,255,255,.85);
}
.vm-sb-nav-item.active {
  background: rgba(var(--vm-accent-rgb), .15);
  color: var(--vm-accent);
  font-weight: 700;
}
.vm-sb-nav-item i {
  width: 18px; text-align: center;
  font-size: .85rem; flex-shrink: 0;
}
.vm-sb-nav-item.active i { color: var(--vm-accent); }

/* divisor */
.vm-sb-divider {
  height: 1px;
  margin: .4rem .9rem;
  background: rgba(255,255,255,.06);
}

/* ── FOOTER ── */
.vm-sb-footer {
  padding: .8rem 1.4rem 1.4rem;
  display: flex; flex-direction: column; gap: .3rem;
}
.vm-sb-footer a {
  font-size: .62rem; color: rgba(255,255,255,.22);
  transition: color .2s;
}
.vm-sb-footer a:hover { color: rgba(255,255,255,.55); }

/* ── ÁREA PRINCIPAL ── */
.vm-main-area {
  flex: 1;
  margin-left: 240px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* ── TOP NAVBAR ── */
.vm-topbar {
  position: sticky; top: 0; z-index: 90;
  height: 54px;
  background: rgba(var(--vm-bg-rgb), .96);
  border-bottom: 1px solid rgba(255,255,255,.06);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 2rem;
  backdrop-filter: blur(12px);
}
.vm-topbar-brand {
  font-size: .9rem;
  font-weight: 800;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--vm-text);
  display: flex;
  align-items: center;
  gap: .5rem;
}
.vm-topbar-brand::before {
  content: '';
  display: inline-block;
  width: 12px; height: 12px;
  border-radius: 50%;
  background: var(--vm-accent);
  flex-shrink: 0;
}
.vm-topbar-right {
  display: flex;
  align-items: center;
  gap: .75rem;
}
.vm-topbar-user {
  display: flex; align-items: center; gap: .5rem;
  font-size: .78rem; font-weight: 600;
  color: rgba(255,255,255,.8);
  cursor: pointer;
}
.vm-topbar-avatar {
  width: 30px; height: 30px; border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(var(--vm-accent-rgb),.4);
}
.vm-topbar-avatar-ph {
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(var(--vm-accent-rgb),.2);
  display: flex; align-items: center; justify-content: center;
  font-size: .7rem; font-weight: 700;
  color: var(--vm-accent);
  border: 2px solid rgba(var(--vm-accent-rgb),.3);
  flex-shrink: 0;
}
.vm-topbar-icon {
  width: 32px; height: 32px; border-radius: 50%;
  background: rgba(255,255,255,.06);
  display: flex; align-items: center; justify-content: center;
  color: rgba(255,255,255,.5);
  font-size: .8rem;
  transition: background .2s, color .2s;
}
.vm-topbar-icon:hover { background: rgba(var(--vm-accent-rgb),.15); color: var(--vm-accent); }

/* ── CONTENT ── */
.vm-content {
  flex: 1;
  padding: 0 clamp(1.2rem, 3vw, 2.5rem) 4rem;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
  position: relative; z-index: 1;
}

/* ── MOBILE ── */
.vm-mobile-menu-btn {
  display: none;
  width: 36px; height: 36px; border-radius: 8px;
  background: rgba(255,255,255,.07);
  align-items: center; justify-content: center;
  color: var(--vm-text); font-size: 1rem;
}
@media (max-width: 768px) {
  .vm-sidebar { transform: translateX(-100%); transition: transform .3s ease; }
  .vm-sidebar.open { transform: translateX(0); }
  .vm-main-area { margin-left: 0; }
  .vm-mobile-menu-btn { display: flex; }
  .vm-sidebar-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.5); z-index: 99;
  }
  .vm-sidebar-overlay.show { display: block; }
}

@media (max-width: 768px) {
  .vm-banner-wrap {
    height: auto !important;
    min-height: unset !important;
  }
  .vm-banner-img {
    position: relative !important;
    inset: unset !important;
    width: 100% !important;
    height: auto !important;
    object-fit: unset !important;
    display: block !important;
  }
}
</style>

<div class="vm-wrapper">

  <!-- ── SIDEBAR ── -->
  <aside class="vm-sidebar" id="vmSidebar">

    <!-- Logo + nome do curso -->
    <div class="vm-sb-brand">
      <div class="vm-sb-logo-ring">
        <?php if ($logoUrl): ?>
          <img src="<?= $logoUrl ?>" alt="" class="vm-sb-logo">
        <?php else: ?>
          <div class="vm-sb-logo-ph"><i class="fas fa-graduation-cap"></i></div>
        <?php endif; ?>
      </div>
      <?php if ($exibirTit): ?>
        <div class="vm-sb-course-name"><?= htmlspecialchars($nomeExibir) ?></div>
      <?php endif; ?>
    </div>

    <!-- Usuário + stats de progresso -->
    <div class="vm-sb-user">
      <div class="vm-sb-user-row">
        <?php if ($fotoAluno): ?>
          <img src="<?= $fotoAluno ?>" alt="" class="vm-sb-avatar">
        <?php else: ?>
          <div class="vm-sb-avatar-ph"><?= mb_strtoupper(mb_substr($nomeAluno, 0, 1)) ?></div>
        <?php endif; ?>
        <div class="vm-sb-user-name"><?= htmlspecialchars($nomeAluno) ?></div>
      </div>

      <div class="vm-sb-stat-row">
        <div class="vm-sb-stat">
          <div class="vm-sb-stat-val"><?= $pctProgresso ?>%</div>
          <div class="vm-sb-stat-lbl">Progresso</div>
        </div>
        <div class="vm-sb-stat">
          <div class="vm-sb-stat-val"><?= $aulasConcluidas ?></div>
          <div class="vm-sb-stat-lbl">Concluídas</div>
        </div>
      </div>

      <div class="vm-sb-thin-bar">
        <div class="vm-sb-thin-fill" style="width:<?= $pctProgresso ?>%"></div>
      </div>
    </div>

    <!-- Navegação em pills -->
    <?php $vmPage = $vmActivePage ?? 'modulos'; ?>
    <nav class="vm-sb-nav">
      <a href="<?= url("minhas-compras/viewmodulos/" . $curso->id) ?>" class="vm-sb-nav-item <?= $vmPage === 'modulos' ? 'active' : '' ?>">
        <i class="fas fa-house"></i> Início
      </a>
      <a href="<?= url("usuarios/perfil/" . $_SESSION['id']) ?>" class="vm-sb-nav-item">
        <i class="fas fa-circle-user"></i> Minha Conta
      </a>
    </nav>

    <!-- Footer -->
    <div class="vm-sb-footer">
      <a href="#">Suporte para Alunos</a>
      <a href="<?= url("termos-uso") ?>">Termos de Uso</a>
      <a href="<?= url("politica-privacidade") ?>">Políticas de Privacidade</a>
    </div>

  </aside>

  <div class="vm-sidebar-overlay" id="vmOverlay"></div>

  <!-- ── ÁREA PRINCIPAL ── -->
  <div class="vm-main-area">

    <!-- TOP NAVBAR -->
    <header class="vm-topbar">
      <div style="display:flex;align-items:center;gap:.8rem;">
        <button class="vm-mobile-menu-btn" id="vmMenuBtn">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <div class="vm-topbar-right">
        <div class="vm-topbar-user">
          <span><?= htmlspecialchars($nomeAluno) ?></span>
          <i class="fas fa-chevron-down" style="font-size:.6rem;opacity:.5;"></i>
          <?php if ($fotoAluno): ?>
            <img src="<?= $fotoAluno ?>" alt="" class="vm-topbar-avatar">
          <?php else: ?>
            <div class="vm-topbar-avatar-ph"><?= mb_substr($nomeAluno, 0, 1) ?></div>
          <?php endif; ?>
        </div>
        <a href="<?= url("usuarios/perfil/" . $_SESSION['id']) ?>" class="vm-topbar-icon" title="Configurações">
          <i class="fas fa-cog"></i>
        </a>
      </div>
    </header>

    <!-- BANNER DO CURSO (se configurado) -->
    <?php if (!empty($curso->banner)): ?>
    <div class="vm-banner-wrap">
      <img src="<?= url("uploads/cursos/" . $curso->banner) ?>" alt="" class="vm-banner-img">
      <div class="vm-banner-overlay"></div>
      <?php if (!empty($curso->banner_titulo) || !empty($curso->boas_vindas)): ?>
      <div class="vm-banner-text">
        <?php if (!empty($curso->banner_titulo)): ?>
          <h2 class="vm-banner-title"><?= htmlspecialchars($curso->banner_titulo) ?></h2>
        <?php endif; ?>
        <?php if (!empty($curso->boas_vindas)): ?>
          <p class="vm-banner-sub"><?= htmlspecialchars($curso->boas_vindas) ?></p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <style>
    .vm-banner-wrap {
      position: relative; width: 100%;
      height: clamp(240px, 38vw, 480px);
      overflow: hidden;
      flex-shrink: 0;
    }
    .vm-banner-img {
      width: 100%; height: 100%;
      object-fit: cover;
      object-position: <?= htmlspecialchars($curso->banner_posicao ?? 'center center', ENT_QUOTES) ?>;
      display: block;
    }
    .vm-banner-overlay { display: none; }
    .vm-banner-text {
      position: absolute; bottom: 1.5rem; left: 2.5rem; right: 2.5rem;
      z-index: 2;
    }
    .vm-banner-title {
      font-size: clamp(1rem, 2.2vw, 1.6rem);
      font-weight: 900; color: var(--vm-text);
      margin: 0 0 .25rem;
      text-shadow: 0 2px 12px rgba(0,0,0,.5);
    }
    .vm-banner-sub {
      font-size: clamp(.72rem, 1vw, .88rem);
      color: rgba(255,255,255,.65);
      margin: 0;
      max-width: 600px;
    }
    </style>
    <?php endif; ?>

    <!-- CONTENT -->
    <main class="vm-content">
      <?= $v->section("content") ?>
    </main>

  </div>
</div>

<!-- jQuery -->
<script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js") ?>"></script>
<script>
(function(){
  var btn     = document.getElementById('vmMenuBtn');
  var sidebar = document.getElementById('vmSidebar');
  var overlay = document.getElementById('vmOverlay');
  if (btn) {
    btn.addEventListener('click', function(){
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', function(){
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }
})();
</script>
<?= $v->section("scripts") ?>
</body>
</html>

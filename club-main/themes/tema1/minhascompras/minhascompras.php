<?php
$v->layout("_theme");

$listaCursos         = $turmaaluno         ?? [];
$progressoCursos     = $progressoCursos    ?? [];
$continuarAssistindo = $continuarAssistindo ?? [];
$heroBanners         = $heroBanners        ?? [];

$imgCurso = function ($curso) {
    if (!$curso) return url("uploads/cursos/default.png");
    $src = $curso->imagem ?? null;
    return $src ? url("uploads/cursos/" . $src) : url("uploads/cursos/default.png");
};

// Popup: dados da aula mais recente
$popupItem = $continuarAssistindo[0] ?? null;
?>

<style>
/* ─── VARIÁVEIS ─── */
:root {
  --mc-accent:     #e2e8f0;
  --mc-accent-rgb: 226, 232, 240;
  --mc-radius:     20px;
  --mc-card-w:     clamp(200px, 22vw, 280px);
}

/* remove o radial-gradient amarelo global desta página */
.mc-page.content-wrapper::before { display: none !important; }

.mc-page .content {
  padding: 0 0 clamp(2rem, 4vw, 3rem);
}

.mc-page .container-fluid {
  display: flex;
  flex-direction: column;
  gap: clamp(2rem, 3vw, 2.8rem);
  padding: 0;
}

/* ─── WELCOME DASHBOARD ─── */
.mc-welcome {
  width: 100%;
  padding: 1.4rem clamp(1rem, 3vw, 2.4rem);
  border-bottom: 1px solid rgba(255,255,255,.06);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
}
.mc-welcome-greeting { display: flex; flex-direction: column; gap: .2rem; }
.mc-welcome-sub {
  font-size: .62rem;
  font-weight: 600;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: rgba(226,232,240,.4);
}
.mc-welcome-name {
  font-size: clamp(1rem, 1.6vw, 1.3rem);
  font-weight: 800;
  color: #f9fafb;
  line-height: 1.15;
}
.mc-welcome-stats {
  display: flex;
  align-items: center;
  gap: clamp(1rem, 2.5vw, 2.4rem);
  flex-shrink: 0;
}
.mc-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .18rem;
}
.mc-stat-value {
  font-size: clamp(1.2rem, 2vw, 1.6rem);
  font-weight: 800;
  color: #f9fafb;
  line-height: 1;
}
.mc-stat-label {
  font-size: .58rem;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: rgba(226,232,240,.4);
  white-space: nowrap;
}
.mc-stat-divider {
  width: 1px;
  height: 28px;
  background: rgba(255,255,255,.1);
}
@media (max-width: 600px) {
  .mc-welcome {
    flex-direction: column;
    align-items: flex-start;
    padding: 1.1rem 1rem 1.2rem;
    gap: .9rem;
  }
}

/* ─── ROW LABEL ─── */
.mc-section {
  padding: 0 clamp(1rem, 3vw, 2.4rem);
}

.mc-row-label {
  display: flex;
  align-items: baseline;
  gap: 0.75rem;
  margin-bottom: 1.1rem;
}

.mc-row-label h2 {
  margin: 0;
  font-size: clamp(1rem, 2vw, 1.25rem);
  font-weight: 700;
  letter-spacing: 0.04em;
  color: var(--wh-text);
}

.mc-row-label span {
  font-size: 0.72rem;
  color: var(--wh-text-soft);
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

/* ─── SLIDER HORIZONTAL ─── */
.mc-slider {
  display: flex;
  gap: clamp(0.9rem, 1.8vw, 1.4rem);
  overflow-x: auto;
  padding-bottom: 1rem;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
  scrollbar-color: rgba(var(--mc-accent-rgb), 0.22) transparent;
}

.mc-slider::-webkit-scrollbar { height: 4px; }
.mc-slider::-webkit-scrollbar-thumb { background: rgba(var(--mc-accent-rgb), 0.28); border-radius: 99px; }

/* ─── CARD CONTINUAR ASSISTINDO ─── */
.mc-card-watch {
  position: relative;
  flex: 0 0 var(--mc-card-w);
  width: var(--mc-card-w);
  border-radius: var(--mc-radius);
  overflow: hidden;
  background: rgba(8,10,20,0.85);
  border: 1px solid rgba(var(--mc-accent-rgb), 0.1);
  text-decoration: none;
  color: inherit;
  scroll-snap-align: start;
  transition: transform 0.28s ease, box-shadow 0.28s ease;
  display: flex;
  flex-direction: column;
}

.mc-card-watch:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 20px 46px rgba(0,0,0,0.55);
  border-color: rgba(var(--mc-accent-rgb), 0.35);
}

.mc-card-watch__thumb {
  position: relative;
  aspect-ratio: 16/9;
  overflow: hidden;
  background: #050508;
  flex-shrink: 0;
}

.mc-card-watch__thumb img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.mc-card-watch:hover .mc-card-watch__thumb img { transform: scale(1.06); }

.mc-card-watch__progress {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
  background: rgba(255,255,255,0.15);
}

.mc-card-watch__progress-bar {
  height: 100%;
  background: var(--mc-accent);
  border-radius: 99px;
}

.mc-card-watch__play {
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  background: rgba(0,0,0,0.38);
  opacity: 0; transition: opacity 0.25s;
}

.mc-card-watch:hover .mc-card-watch__play { opacity: 1; }

.mc-card-watch__play-btn {
  width: 44px; height: 44px;
  border-radius: 50%;
  background: rgba(var(--mc-accent-rgb), 0.92);
  display: flex; align-items: center; justify-content: center;
  color: #050508; font-size: 1rem;
}

.mc-card-watch__info {
  padding: 0.75rem 0.9rem 0.9rem;
  display: flex; flex-direction: column; gap: 0.25rem;
  flex: 1;
}

.mc-card-watch__course {
  font-size: 0.62rem;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: rgba(var(--mc-accent-rgb), 0.8);
  font-weight: 600;
}

.mc-card-watch__title {
  font-size: 0.86rem;
  font-weight: 600;
  color: var(--wh-text);
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.mc-card-watch__pct {
  font-size: 0.7rem;
  color: var(--wh-text-soft);
  margin-top: 0.1rem;
}

/* ─── SLIDER DE CURSOS (PORTRAIT) ─── */
.mc-courses-slider {
  display: flex;
  gap: clamp(.8rem, 1.6vw, 1.2rem);
  overflow-x: auto;
  padding-bottom: .6rem;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,.15) transparent;
}
.mc-courses-slider::-webkit-scrollbar { height: 3px; }
.mc-courses-slider::-webkit-scrollbar-thumb { background:rgba(255,255,255,.18); border-radius:99px; }

/* card portrait — mesmo tamanho dos módulos (9/16, 4 por linha) */
.mc-card-course {
  scroll-snap-align: start;
  flex: 0 0 clamp(160px, 20vw, 260px);
  position: relative;
  border-radius: 18px;
  overflow: hidden;
  background: #07080f;
  text-decoration: none;
  color: inherit;
  transition: transform .3s ease, box-shadow .3s ease;
  box-shadow: 0 20px 48px rgba(0,0,0,.6);
  outline: 2px solid transparent;
}

.mc-card-course:hover {
  transform: translateY(-8px) scale(1.025);
  box-shadow: 0 24px 60px rgba(0,0,0,.7),
              0 0 0 2px rgba(255,255,255,.18);
}

/* thumb */
.mc-card-course__cover {
  aspect-ratio: 9/16;
  overflow: hidden;
  background: #050508;
  position: relative;
}

.mc-card-course__cover img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform .45s ease;
}

.mc-card-course:hover .mc-card-course__cover img { transform: scale(1.08); }

/* gradiente dramático no fundo */
.mc-card-course__cover::after {
  content: "";
  position: absolute; inset: 0;
  background: linear-gradient(
    transparent 30%,
    rgba(5,5,8,.6)  60%,
    rgba(5,5,8,.97) 100%
  );
  pointer-events: none;
}

/* sem accent amarelo no hover */
.mc-card-course::after { display: none; }

.mc-card-course__play {
  position: absolute; inset: 0; z-index: 1;
  background: rgba(0,0,0,0);
  transition: background .22s ease;
}
.mc-card-course:hover .mc-card-course__play { background: rgba(0,0,0,.2); }
.mc-card-course__play-icon { display: none; }

/* label overlay */
.mc-card-course__label {
  position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;
  padding: .75rem 1rem 1rem;
  display: flex; flex-direction: column; gap: .3rem;
}
.mc-card-course__name {
  font-size: clamp(.9rem, 1.4vw, 1.1rem); font-weight: 800; color: #f9fafb;
  line-height: 1.25;
  display: -webkit-box; -webkit-line-clamp: 2;
  -webkit-box-orient: vertical; overflow: hidden;
  text-shadow: 0 2px 10px rgba(0,0,0,.8);
}

/* progress bar na base */
.mc-card-course__pbar {
  position: absolute; bottom: 0; left: 0; right: 0; height: 4px; z-index: 4;
  background: rgba(255,255,255,.1);
}
.mc-card-course__pbar-fill {
  height: 100%; border-radius: 99px;
  transition: width .4s ease;
}

/* botão acessar curso */
.mc-card-course__btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .45rem;
  margin: .85rem .9rem .9rem;
  padding: .58rem 1rem;
  border-radius: 10px;
  text-align: center;
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #f9fafb;
  background: linear-gradient(160deg, #1a1a1f 0%, #0d0d10 100%);
  border: 1px solid rgba(255,255,255,.14);
  box-shadow: 0 2px 0 rgba(0,0,0,.6), 0 4px 12px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.07);
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
}
.mc-card-course__btn::before {
  content: "▶";
  font-size: .6rem;
  opacity: .7;
}
.mc-card-course:hover .mc-card-course__btn {
  transform: translateY(-1px);
  box-shadow: 0 4px 0 rgba(0,0,0,.55), 0 8px 20px rgba(0,0,0,.4), inset 0 1px 0 rgba(255,255,255,.1);
  background: linear-gradient(160deg, #22222a 0%, #141418 100%);
}
.mc-card-course__btn:active,
.mc-card-course:active .mc-card-course__btn {
  transform: translateY(1px);
  box-shadow: 0 1px 0 rgba(0,0,0,.7), 0 2px 6px rgba(0,0,0,.3), inset 0 2px 4px rgba(0,0,0,.4);
}

/* badge concluído */
.mc-card-done-badge {
  position: absolute; top: .65rem; right: .65rem; z-index: 3;
  width: 26px; height: 26px; border-radius: 50%;
  background: rgba(74,222,128,.9); color: #050507;
  display: flex; align-items: center; justify-content: center;
  font-size: .7rem; box-shadow: 0 4px 12px rgba(0,0,0,.5);
}


/* ─── SEÇÃO MEUS CURSOS ─── */
.mc-continue-wrap {
  padding: 0;
}
.mc-continue-inner {
  padding: 1.4rem 0 1.6rem;
  border-top: 1px solid rgba(255,255,255,.06);
}
.mc-continue-head {
  display: flex; align-items: center;
  margin-bottom: 1rem;
  padding: 0 clamp(1rem, 3vw, 1.6rem);
}
.mc-continue-title {
  font-size: .78rem; font-weight: 800;
  letter-spacing: .12em; text-transform: uppercase;
  color: var(--mc-accent); margin: 0;
}

/* ─── MOBILE ─── */
@media (max-width: 768px) {
  .mc-continue-wrap { padding: 0; }
  .mc-continue-inner { padding: 1rem 0 1.1rem; }
  .mc-continue-head { padding: 0 .9rem; }
  .mc-courses-slider { padding: 0 .9rem .6rem; }
  .mc-card-course { width: 58vw; border-radius: 10px; }
}

@media (max-width: 420px) {
  .mc-card-course { width: 72vw; }
}
</style>

<!-- ─── CONTEÚDO ─── -->
<div class="content-wrapper win-page mc-page">

  <!-- ─── WELCOME DASHBOARD ─── -->
  <?php
    $totalCursos   = count($listaCursos);
    $emAndamento   = 0;
    $concluidos    = 0;
    foreach ($listaCursos as $entrada) {
      $pctCurso = $progressoCursos[$entrada->dataCurso()->id ?? 0] ?? 0;
      if ($pctCurso >= 100) $concluidos++;
      elseif ($pctCurso > 0) $emAndamento++;
    }
    $primeiroNome  = explode(' ', trim($_SESSION['nome_abreviado'] ?? $_SESSION['nome'] ?? 'Aluno'))[0];
    $hora          = (int)date('H');
    $saudacao      = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');
  ?>
  <div class="mc-welcome">
    <div class="mc-welcome-greeting">
      <span class="mc-welcome-sub"><?= $saudacao ?></span>
      <span class="mc-welcome-name"><?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="mc-welcome-stats">
      <div class="mc-stat">
        <span class="mc-stat-value"><?= $totalCursos ?></span>
        <span class="mc-stat-label">Matriculado<?= $totalCursos !== 1 ? 's' : '' ?></span>
      </div>
      <div class="mc-stat-divider"></div>
      <div class="mc-stat">
        <span class="mc-stat-value"><?= $emAndamento ?></span>
        <span class="mc-stat-label">Em andamento</span>
      </div>
      <div class="mc-stat-divider"></div>
      <div class="mc-stat">
        <span class="mc-stat-value"><?= $concluidos ?></span>
        <span class="mc-stat-label">Concluído<?= $concluidos !== 1 ? 's' : '' ?></span>
      </div>
    </div>
  </div>

  <!-- ─── MEUS CURSOS (card colado ao banner) ─── -->
  <?php if ($listaCursos) : ?>
  <div class="mc-continue-wrap">
    <div class="mc-continue-inner">
      <div class="mc-continue-head">
        <h2 class="mc-continue-title"><i class="fas fa-graduation-cap" style="margin-right:.4rem;"></i>Meus Produtos <span style="font-size:.65rem;opacity:.5;font-weight:500;letter-spacing:.1em;"><?= count($listaCursos) ?> <?= count($listaCursos) === 1 ? 'curso' : 'cursos' ?></span></h2>
      </div>
      <div class="mc-courses-slider">
        <?php foreach ($listaCursos as $entrada) :
          $curso = $entrada->dataCurso();
          if (!$curso) continue;
          $cover      = $curso->imagem ? url("uploads/cursos/" . $curso->imagem) : url("uploads/cursos/default.png");
          $pct        = $progressoCursos[$curso->id] ?? 0;
          $done       = $pct >= 100;
          $corDestaque = !empty($curso->tema_cor_primaria) ? htmlspecialchars($curso->tema_cor_primaria, ENT_QUOTES, 'UTF-8') : '#e2e8f0';
        ?>
          <a href="<?= url("minhas-compras/viewmodulos/" . $curso->id) ?>" class="mc-card-course">
            <div class="mc-card-course__cover">
              <img src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($curso->nome, ENT_QUOTES, 'UTF-8') ?>">
              <div class="mc-card-course__play"></div>
              <div class="mc-card-course__label">
                <?php if ($curso->mostrar_titulo_aulas ?? 1) : ?>
                  <span class="mc-card-course__name"><?= htmlspecialchars($curso->nome, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
              </div>
              <div class="mc-card-course__pbar">
                <div class="mc-card-course__pbar-fill" style="width:<?= $pct ?>%; background:<?= $corDestaque ?>"></div>
              </div>
              <?php if ($done) : ?>
                <div class="mc-card-done-badge"><i class="fas fa-check"></i></div>
              <?php endif; ?>
            </div>
            <div class="mc-card-course__btn">Acessar agora</div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <section class="content" style="padding-top: 0;">
    <div class="container-fluid"></div>
  </section>
</div>


<?= $v->start("scripts") ?>
<?= $v->end() ?>

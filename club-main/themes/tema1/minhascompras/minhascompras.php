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

/* ─── HERO SLIDESHOW ─── */
.mc-hero {
  position: relative;
  width: 100%;
  height: clamp(240px, 38vw, 480px);
  overflow: hidden;
  background: #050508;
  margin-left: 0;
  margin-right: 0;
}

.mc-hero-slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 0.9s ease;
  pointer-events: none;
}

.mc-hero-slide.is-active {
  opacity: 1;
  pointer-events: auto;
}

.mc-hero-slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  display: block;
}

.mc-hero-slide::after { display: none; }

.mc-hero-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 2;
  padding: clamp(1.2rem, 3vw, 2.2rem) clamp(1rem, 3vw, 2.4rem);
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.mc-hero-label {
  font-size: 0.65rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: rgba(var(--mc-accent-rgb), 0.8);
  font-weight: 600;
}

.mc-hero-title {
  font-size: clamp(1.1rem, 2.8vw, 1.8rem);
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
  max-width: 560px;
}

.mc-hero-msg {
  font-size: clamp(0.78rem, 1.3vw, 0.95rem);
  color: rgba(255,255,255,0.75);
  max-width: 480px;
  line-height: 1.55;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.mc-hero-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.4rem;
  overflow: hidden;
  max-height: 0;
  opacity: 0;
  transition: max-height .35s ease, opacity .35s ease;
}
.mc-hero-slide.is-active:hover .mc-hero-actions {
  max-height: 60px;
  opacity: 1;
}

.mc-hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.4rem;
  border-radius: 999px;
  background: var(--mc-accent);
  color: #050508;
  font-weight: 700;
  font-size: 0.82rem;
  text-decoration: none;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  transition: opacity 0.2s;
}

.mc-hero-btn:hover { opacity: 0.88; color: #050508; }

/* dots */
.mc-hero-dots {
  position: absolute;
  bottom: 0.75rem;
  right: clamp(1rem, 3vw, 2.4rem);
  z-index: 3;
  display: flex;
  gap: 0.4rem;
}

.mc-hero-dot {
  width: 6px;
  height: 6px;
  border-radius: 99px;
  background: rgba(255,255,255,0.35);
  transition: background 0.3s, width 0.3s;
  cursor: pointer;
}

.mc-hero-dot.is-active {
  background: var(--mc-accent);
  width: 20px;
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
  height: 100%; background: rgba(255,255,255,.55); border-radius: 99px;
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

/* ─── POPUP CONTINUAR ─── */
#mc-popup {
  position: fixed;
  bottom: 1.6rem;
  right: 1.6rem;
  z-index: 1200;
  width: clamp(260px, 28vw, 340px);
  border-radius: 18px;
  overflow: hidden;
  background: rgba(11,14,24,0.97);
  border: 1px solid rgba(var(--mc-accent-rgb), 0.28);
  box-shadow: 0 24px 60px rgba(0,0,0,0.65);
  display: none;
  flex-direction: column;
  animation: mc-slide-in 0.45s cubic-bezier(0.34,1.56,0.64,1) both;
}

@keyframes mc-slide-in {
  from { transform: translateX(110%) scale(0.92); opacity: 0; }
  to   { transform: translateX(0) scale(1); opacity: 1; }
}

.mc-popup__header {
  position: relative;
  aspect-ratio: 16/9;
  overflow: hidden;
  background: #050508;
}

.mc-popup__header img {
  width: 100%; height: 100%;
  object-fit: cover;
}

.mc-popup__header::after {
  content: "";
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(5,5,8,0.72) 0%, transparent 55%);
}

.mc-popup__progress {
  position: absolute; bottom: 0; left: 0; right: 0;
  height: 3px; background: rgba(255,255,255,0.15);
}

.mc-popup__progress-bar {
  height: 100%; background: var(--mc-accent); border-radius: 99px;
}

.mc-popup__close {
  position: absolute; top: 0.5rem; right: 0.6rem; z-index: 2;
  background: rgba(0,0,0,0.55); border: none;
  color: rgba(255,255,255,0.7);
  width: 26px; height: 26px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.82rem; cursor: pointer;
  transition: background 0.2s;
}

.mc-popup__close:hover { background: rgba(0,0,0,0.8); color: #fff; }

.mc-popup__body {
  padding: 0.9rem 1.1rem 1.1rem;
  display: flex; flex-direction: column; gap: 0.5rem;
}

.mc-popup__label {
  font-size: 0.6rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(var(--mc-accent-rgb), 0.75);
  font-weight: 600;
}

.mc-popup__course {
  font-size: 0.7rem;
  color: var(--wh-text-soft);
}

.mc-popup__aula {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--wh-text);
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.mc-popup__btn {
  display: inline-flex; align-items: center; gap: 0.5rem;
  padding: 0.55rem 1.2rem;
  border-radius: 999px;
  background: var(--mc-accent);
  color: #050508; font-weight: 700;
  font-size: 0.78rem; text-decoration: none;
  align-self: flex-start;
  margin-top: 0.2rem;
  transition: opacity 0.2s;
}

.mc-popup__btn:hover { opacity: 0.85; color: #050508; }

/* ─── CONTINUAR ASSISTINDO (card colado ao hero) ─── */
.mc-continue-wrap {
  margin-top: -3rem;
  position: relative;
  z-index: 2;
  padding: 0 clamp(1rem, 3vw, 2.4rem);
}
.mc-continue-inner {
  background: #050508;
  border: 1px solid rgba(var(--mc-accent-rgb), .1);
  border-radius: 18px;
  padding: 1.2rem 1.4rem 1.4rem;
}
.mc-continue-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1rem;
}
.mc-continue-title {
  font-size: .78rem; font-weight: 800;
  letter-spacing: .12em; text-transform: uppercase;
  color: var(--mc-accent); margin: 0;
}

/* ─── MOBILE ─── */
@media (max-width: 768px) {
  /* Hero: proporção natural da imagem no mobile */
  .mc-hero {
    height: 25vw !important; /* proporção 4:1 (banner 1920×480) */
    min-height: 140px;
    max-height: 220px;
    background: #050508;
    overflow: hidden;
  }
  .mc-hero-slide img {
    object-fit: contain;
    object-position: center center;
    background: #050508;
  }
  .mc-hero-content {
    position: relative;
    padding: .75rem 1rem;
    background: rgba(5,5,8,.7);
  }
  .mc-hero-title { font-size: clamp(.9rem, 4.5vw, 1.2rem); }
  .mc-hero-msg { display: none; }
  .mc-hero-dots { display: none; }

  .mc-continue-wrap {
    padding: 0 .75rem;
    margin-top: 0;
  }
  .mc-continue-inner {
    padding: 1rem 1rem 1.1rem;
    border-radius: 14px;
  }
  .mc-card-course {
    flex: 0 0 44vw;
    border-radius: 14px;
  }
}

@media (max-width: 420px) {
  .mc-hero { height: 52vw; }
  .mc-card-course { flex: 0 0 58vw; }
  #mc-popup {
    bottom: 0; right: 0; left: 0;
    width: 100%; border-radius: 18px 18px 0 0;
    animation: mc-slide-up 0.4s cubic-bezier(0.34,1.3,0.64,1) both;
  }
  @keyframes mc-slide-up {
    from { transform: translateY(100%); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
  }
}
</style>

<!-- ─── CONTEÚDO ─── -->
<div class="content-wrapper win-page mc-page">

  <!-- ─── HERO SLIDESHOW (dentro do content-wrapper para respeitar a sidebar) ─── -->
  <?php if ($heroBanners) : ?>
  <div class="mc-hero" id="mc-hero">
    <?php foreach ($heroBanners as $i => $banner) :
      $src    = url("uploads/cursos/" . $banner['banner']);
      $ctaUrl = url("minhas-compras/viewmodulos/" . $banner['curso_id']);
    ?>
      <div class="mc-hero-slide <?= $i === 0 ? 'is-active' : ''; ?>" data-index="<?= $i; ?>">
        <img src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>" alt="">
        <div class="mc-hero-content">
          <?php if ($banner['mostrar_titulo'] ?? 1) : ?>
            <h1 class="mc-hero-title"><?= htmlspecialchars($banner['nome'], ENT_QUOTES, 'UTF-8'); ?></h1>
          <?php endif; ?>
          <?php if (!empty($banner['boas_vindas'])) : ?>
            <span class="mc-hero-label">Bem-vindo</span>
            <p class="mc-hero-msg"><?= htmlspecialchars($banner['boas_vindas'], ENT_QUOTES, 'UTF-8'); ?></p>
          <?php endif; ?>
          <div class="mc-hero-actions">
            <a href="<?= $ctaUrl; ?>" class="mc-hero-btn">
              <i class="fas fa-play"></i> Acessar curso
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (count($heroBanners) > 1) : ?>
      <div class="mc-hero-dots" id="mc-dots">
        <?php foreach ($heroBanners as $i => $b) : ?>
          <div class="mc-hero-dot <?= $i === 0 ? 'is-active' : ''; ?>" data-slide="<?= $i; ?>"></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- ─── MEUS CURSOS (card colado ao banner) ─── -->
  <?php if ($listaCursos) : ?>
  <div class="mc-continue-wrap">
    <div class="mc-continue-inner">
      <div class="mc-continue-head">
        <h2 class="mc-continue-title"><i class="fas fa-graduation-cap" style="margin-right:.4rem;"></i>Meus cursos <span style="font-size:.65rem;opacity:.5;font-weight:500;letter-spacing:.1em;"><?= count($listaCursos) ?> <?= count($listaCursos) === 1 ? 'curso' : 'cursos' ?></span></h2>
      </div>
      <div class="mc-courses-slider">
        <?php foreach ($listaCursos as $entrada) :
          $curso = $entrada->dataCurso();
          if (!$curso) continue;
          $cover = $curso->imagem ? url("uploads/cursos/" . $curso->imagem) : url("uploads/cursos/default.png");
          $pct   = $progressoCursos[$curso->id] ?? 0;
          $done  = $pct >= 100;
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
                <div class="mc-card-course__pbar-fill" style="width:<?= $pct ?>%"></div>
              </div>
              <?php if ($done) : ?>
                <div class="mc-card-done-badge"><i class="fas fa-check"></i></div>
              <?php endif; ?>
            </div>
            <div class="mc-card-course__btn">Acessar curso</div>
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

<!-- ─── POPUP CONTINUAR ASSISTINDO ─── -->
<?php if ($popupItem) :
  $popupThumb = $popupItem->aula_imagem
    ? url("uploads/cursos/" . $popupItem->aula_imagem)
    : ($popupItem->curso_banner
        ? url("uploads/cursos/" . $popupItem->curso_banner)
        : ($popupItem->curso_imagem ? url("uploads/cursos/" . $popupItem->curso_imagem) : url("uploads/cursos/default.png")));
  $popupPct   = min(100, (float)($popupItem->percent_watched ?? 0));
  $popupUrl   = url("minhas-compras/viewaulas/" . $popupItem->aula_id);
?>
<div id="mc-popup">
  <div class="mc-popup__header">
    <img src="<?= htmlspecialchars($popupThumb, ENT_QUOTES, 'UTF-8'); ?>" alt="">
    <button class="mc-popup__close" id="mc-popup-close" title="Fechar">
      <i class="fas fa-times"></i>
    </button>
    <div class="mc-popup__progress">
      <div class="mc-popup__progress-bar" style="width:<?= $popupPct; ?>%"></div>
    </div>
  </div>
  <div class="mc-popup__body">
    <span class="mc-popup__label">Continue de onde parou</span>
    <span class="mc-popup__course"><?= htmlspecialchars($popupItem->curso_nome, ENT_QUOTES, 'UTF-8'); ?></span>
    <span class="mc-popup__aula"><?= htmlspecialchars($popupItem->aula_nome, ENT_QUOTES, 'UTF-8'); ?></span>
    <a href="<?= $popupUrl; ?>" class="mc-popup__btn">
      <i class="fas fa-play"></i> Continuar
    </a>
  </div>
</div>
<?php endif; ?>

<?= $v->start("scripts") ?>
<script>
(function () {
  // ─── Popup ───
  const popup = document.getElementById('mc-popup');
  const closeBtn = document.getElementById('mc-popup-close');

  if (popup) {
    const key = 'mc_popup_' + <?= json_encode($popupItem->aula_id ?? 0) ?>;
    if (!sessionStorage.getItem(key)) {
      setTimeout(() => { popup.style.display = 'flex'; }, 2000);
    }
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        popup.style.display = 'none';
        sessionStorage.setItem(key, '1');
      });
    }
    // Click no CTA também marca como visto
    popup.querySelector('.mc-popup__btn')?.addEventListener('click', () => {
      sessionStorage.setItem(key, '1');
    });
  }

  // ─── Hero slideshow ───
  const slides = document.querySelectorAll('.mc-hero-slide');
  const dots   = document.querySelectorAll('.mc-hero-dot');
  if (slides.length > 1) {
    let current = 0;
    let timer;

    function goTo(idx) {
      slides[current].classList.remove('is-active');
      dots[current]?.classList.remove('is-active');
      current = (idx + slides.length) % slides.length;
      slides[current].classList.add('is-active');
      dots[current]?.classList.add('is-active');
    }

    function startTimer() { timer = setInterval(() => goTo(current + 1), 5000); }
    function stopTimer()  { clearInterval(timer); }

    startTimer();

    dots.forEach(dot => {
      dot.addEventListener('click', () => {
        stopTimer();
        goTo(parseInt(dot.dataset.slide));
        startTimer();
      });
    });
  }
})();
</script>
<?= $v->end() ?>

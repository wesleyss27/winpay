<?php
$v->layout("_theme_modulos");

$emProgresso = $emProgresso ?? [];
$modulosData = $modulosData ?? [];

$imgAula = function ($a) use ($curso) {
    if (!empty($a->aula_imagem)) return url("uploads/cursos/" . $a->aula_imagem);
    return !empty($curso->imagem) ? url("uploads/cursos/" . $curso->imagem) : url("uploads/cursos/default.png");
};

$imgMod = function ($mod) use ($curso) {
    if (!empty($mod->imagem)) return url("uploads/cursos/" . $mod->imagem);
    return !empty($curso->imagem) ? url("uploads/cursos/" . $curso->imagem) : url("uploads/cursos/default.png");
};

$mostrarTituloMod = ($curso->mostrar_titulo_aulas ?? 1) == 1;
?>
<style>
/* ── SECTION HEADER ── */
.vm-section-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1rem;
}
.vm-section-title {
  font-size: .82rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--vm-text);
  margin: 0;
  opacity: .9;
}
.vm-slider-arrows { display: flex; gap: .35rem; }
.vm-arrow {
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.1);
  color: rgba(255,255,255,.6);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; font-size: .75rem;
  transition: background .2s, border-color .2s, color .2s;
}
.vm-arrow:hover {
  background: rgba(var(--vm-accent-rgb), .15);
  border-color: rgba(var(--vm-accent-rgb), .4);
  color: var(--vm-accent);
}

/* ── SLIDER ── */
.vm-slider-outer { position: relative; }
.vm-slider {
  display: flex; gap: 1rem;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  padding-bottom: 4px;
}
.vm-slider::-webkit-scrollbar { display: none; }

/* ── CARD EM PROGRESSO (landscape) ── */
.vm-prog-card {
  flex-shrink: 0;
  width: clamp(190px, 22vw, 270px);
  border-radius: 10px;
  overflow: hidden;
  scroll-snap-align: start;
  display: flex; flex-direction: column;
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.08);
  text-decoration: none; color: var(--vm-text);
  transition: transform .2s, border-color .25s;
}
.vm-prog-card:hover {
  transform: translateY(-4px);
  border-color: rgba(var(--vm-accent-rgb), .35);
  color: var(--vm-text);
}
.vm-prog-img {
  width: 100%; aspect-ratio: 16/9;
  object-fit: cover; display: block;
}
.vm-prog-body {
  padding: .6rem .75rem .7rem;
  flex: 1; display: flex; flex-direction: column; gap: .2rem;
}
.vm-prog-tag {
  font-size: .6rem; font-weight: 800;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--vm-accent); margin-bottom: .1rem;
}
.vm-prog-name {
  font-size: .75rem; font-weight: 600; line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.vm-prog-bar-bg {
  height: 3px; border-radius: 999px;
  background: rgba(255,255,255,.1); overflow: hidden;
  margin-top: .4rem;
}
.vm-prog-bar-fill {
  height: 100%; border-radius: 999px;
  background: var(--vm-accent);
}
.vm-prog-pct {
  font-size: .6rem; color: rgba(255,255,255,.35); margin-top: .15rem;
}

/* ── SLIDER MÓDULOS ── */
#vm-slider-mod { gap: 1.2rem; }

/* ── CARD MÓDULO — portrait 9:16, 4 por linha ── */
.vm-mod-card {
  flex-shrink: 0;
  width: calc((100% - 3 * 1.2rem) / 4);   /* 4 visíveis */
  aspect-ratio: 9/16;
  border-radius: 18px;
  overflow: hidden;
  scroll-snap-align: start;
  display: block;
  text-decoration: none;
  color: var(--vm-text);
  position: relative;
  transition: transform .3s ease, box-shadow .3s ease;
  /* borda accent visível no hover */
  outline: 2px solid transparent;
  outline-offset: 0px;
}
.vm-mod-card:hover {
  transform: translateY(-8px) scale(1.025);
  box-shadow:
    0 24px 60px rgba(0,0,0,.7),
    0 0 0 2px rgba(var(--vm-accent-rgb), .7);
  color: var(--vm-text);
}

@media (max-width: 900px)  { #vm-slider-mod { gap: 1rem; }
  .vm-mod-card { width: calc((100% - 2rem) / 3); } }
@media (max-width: 560px)  {
  .vm-mod-card { width: calc((100% - 1rem) / 2); } }

/* Imagem de fundo */
.vm-mod-img {
  width: 100%; height: 100%;
  object-fit: contain;
  object-position: center top;
  position: absolute; inset: 0;
  transition: transform .45s ease;
  background: #0a0a0f;
}
.vm-mod-card:hover .vm-mod-img { transform: scale(1.08); }

/* Gradiente dramático — clarinho no topo, bem escuro embaixo */
.vm-mod-gradient {
  position: absolute; inset: 0; z-index: 1;
  background: linear-gradient(
    to bottom,
    rgba(0,0,0,.0)   0%,
    rgba(0,0,0,.05)  30%,
    rgba(0,0,0,.6)   60%,
    rgba(0,0,0,.93)  85%,
    rgba(0,0,0,1)    100%
  );
}

/* Shimmer accent no hover (brilho colorido na borda inferior) */
.vm-mod-card::after {
  content: '';
  position: absolute; bottom: 0; left: 0; right: 0;
  height: 4px; z-index: 5;
  background: var(--vm-accent);
  opacity: 0;
  transition: opacity .3s ease;
}
.vm-mod-card:hover::after { opacity: 1; }

/* Banner "CONTEÚDO FECHADO" no topo */
.vm-mod-locked-banner {
  position: absolute; top: 0; left: 0; right: 0;
  padding: .9rem 1rem;
  background: rgba(0,0,0,.7);
  backdrop-filter: blur(8px);
  display: flex; flex-direction: column; gap: .2rem;
  z-index: 3;
}
.vm-mod-locked-label {
  font-size: .75rem; font-weight: 900;
  letter-spacing: .12em; text-transform: uppercase;
  color: #fff;
}
.vm-mod-locked-sublabel {
  font-size: .62rem; font-weight: 700;
  color: var(--vm-accent);
  letter-spacing: .06em; text-transform: uppercase;
}

/* Cadeado centralizado */
.vm-mod-lock-icon {
  position: absolute; top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  font-size: 3.2rem;
  color: rgba(255,255,255,.25);
  z-index: 2;
}

/* Área de texto na base */
.vm-mod-name {
  position: absolute;
  bottom: 1.1rem; left: 1rem; right: 1rem;
  font-size: clamp(.88rem, 1.3vw, 1.05rem);
  font-weight: 800;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  z-index: 3;
  text-shadow: 0 2px 10px rgba(0,0,0,.8);
}

/* Barra de progresso na base */
.vm-mod-progress {
  position: absolute; bottom: 0; left: 0; right: 0;
  height: 5px; background: rgba(255,255,255,.1); z-index: 4;
}
.vm-mod-progress-fill {
  height: 100%; background: var(--vm-accent);
  transition: width .4s ease;
}

/* ── DOTS NAVEGAÇÃO ── */
.vm-dots {
  display: flex; justify-content: center; gap: .4rem;
  margin-top: 1rem;
}
.vm-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: rgba(255,255,255,.2);
  cursor: pointer;
  transition: background .2s, transform .2s;
}
.vm-dot.active { background: var(--vm-accent); transform: scale(1.3); }

/* ── EMPTY ── */
.vm-empty {
  text-align: center; padding: 3rem 1rem;
  color: rgba(255,255,255,.3); font-size: .85rem;
}
.vm-empty i { font-size: 2rem; margin-bottom: .5rem; display: block; }
</style>

<!-- ────────────── EM PROGRESSO ────────────── -->
<?php if (!empty($emProgresso)): ?>
<style>
.vm-continue-card {
  background: var(--vm-bg);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 18px;
  padding: 1.2rem 1.4rem 1.4rem;
  margin-top: -3rem;
  position: relative;
  z-index: 2;
}
.vm-continue-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.1rem;
}
.vm-continue-title {
  font-size: .8rem; font-weight: 800;
  letter-spacing: .12em; text-transform: uppercase;
  color: var(--vm-accent); margin: 0;
}
</style>
<div class="vm-continue-card">
  <div class="vm-continue-head">
    <h2 class="vm-continue-title"><i class="fas fa-circle-play" style="margin-right:.4rem;"></i>Continuar assistindo</h2>
    <div class="vm-slider-arrows">
      <button class="vm-arrow" data-slider="prog" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
      <button class="vm-arrow" data-slider="prog" data-dir="1"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>
  <div class="vm-slider-outer">
    <div class="vm-slider" id="vm-slider-prog">
      <?php foreach ($emProgresso as $ep): ?>
      <a href="<?= url("minhas-compras/viewaulas/" . $ep->aula_id) ?>" class="vm-prog-card">
        <img src="<?= $imgAula($ep) ?>" alt="" class="vm-prog-img" loading="lazy">
        <div class="vm-prog-body">
          <div class="vm-prog-tag"><?= htmlspecialchars($ep->modulo_nome) ?></div>
          <div class="vm-prog-name"><?= htmlspecialchars($ep->aula_nome) ?></div>
          <div class="vm-prog-bar-bg">
            <div class="vm-prog-bar-fill" style="width:<?= (int)$ep->percent_watched ?>%"></div>
          </div>
          <div class="vm-prog-pct"><?= (int)$ep->percent_watched ?>% assistido</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ────────────── MÓDULOS ────────────── -->
<div>
  <?php
  $totalMods = count($modulosData);
  $liberados  = array_filter($modulosData, fn($m) => $m['liberado']);
  $bloqueados = array_filter($modulosData, fn($m) => !$m['liberado']);
  $tituloSec  = !empty($bloqueados) ? 'Conteúdo Fechado' : 'Módulos do Curso';
  ?>
  <div class="vm-section-head">
    <h2 class="vm-section-title"><?= $tituloSec ?></h2>
    <div class="vm-slider-arrows">
      <button class="vm-arrow" data-slider="mod" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
      <button class="vm-arrow" data-slider="mod" data-dir="1"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>

  <?php if (empty($modulosData)): ?>
    <div class="vm-empty">
      <i class="fas fa-layer-group"></i>
      Nenhum módulo disponível ainda.
    </div>
  <?php else: ?>
    <div class="vm-slider-outer">
      <div class="vm-slider" id="vm-slider-mod">
        <?php $i = 0; foreach ($modulosData as $item):
          $mod       = $item['mod'];
          $liberado  = $item['liberado'];
          $progresso = $item['progresso'];
          $total     = $item['total'];
          $done      = $item['concluidas'];
          $i++;
          $numStr = str_pad($i, 2, '0', STR_PAD_LEFT);
          $href = ($liberado && $item['primeiraAula'])
            ? url("minhas-compras/viewaulas/" . $item['primeiraAula']->id)
            : url("minhas-compras/viewcursos/" . $curso->id);
        ?>
        <a href="<?= $href ?>" class="vm-mod-card">
          <img src="<?= $imgMod($mod) ?>" alt="" class="vm-mod-img" loading="lazy">
          <div class="vm-mod-gradient"></div>

          <?php if (!$liberado): ?>
            <div class="vm-mod-locked-banner">
              <div class="vm-mod-locked-label">Conteúdo Fechado</div>
              <div class="vm-mod-locked-sublabel">Liberado após 7 dias de compra</div>
            </div>
            <div class="vm-mod-lock-icon"><i class="fas fa-lock"></i></div>
          <?php endif; ?>

          <?php if ($mostrarTituloMod): ?>
          <div class="vm-mod-name"><?= htmlspecialchars($mod->nome) ?></div>
          <?php endif; ?>

          <?php if ($liberado && $total > 0): ?>
            <div class="vm-mod-progress">
              <div class="vm-mod-progress-fill" style="width:<?= $progresso ?>%"></div>
            </div>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Dots -->
    <?php if ($totalMods > 1): ?>
    <div class="vm-dots" id="vm-dots-mod">
      <?php for ($d = 0; $d < min($totalMods, 6); $d++): ?>
        <div class="vm-dot <?= $d === 0 ? 'active' : '' ?>" data-index="<?= $d ?>"></div>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  <?php endif; ?>
</div>

<?= $v->start("scripts") ?>
<script>
(function(){
  // Arrow scroll
  document.querySelectorAll('.vm-arrow').forEach(function(btn){
    btn.addEventListener('click', function(){
      var sliderId = 'vm-slider-' + btn.dataset.slider;
      var slider   = document.getElementById(sliderId);
      if (!slider) return;
      // Para módulos, rola uma "página" completa (largura do container)
      // Para progresso, rola 2 cards
      var step = btn.dataset.slider === 'mod'
        ? slider.clientWidth + 20          /* rola toda a "página" de 4 cards */
        : ((slider.querySelector('.vm-prog-card') || {offsetWidth: 200}).offsetWidth + 16) * 2;
      slider.scrollBy({ left: parseInt(btn.dataset.dir) * step, behavior: 'smooth' });
    });
  });

  // Dots sync para módulos
  var modSlider = document.getElementById('vm-slider-mod');
  var dots      = document.querySelectorAll('#vm-dots-mod .vm-dot');
  if (modSlider && dots.length) {
    modSlider.addEventListener('scroll', function(){
      var card    = modSlider.querySelector('.vm-mod-card');
      if (!card) return;
      var cardW   = card.offsetWidth + 16;
      var idx     = Math.round(modSlider.scrollLeft / cardW);
      var dotIdx  = Math.min(idx, dots.length - 1);
      dots.forEach(function(d, i){ d.classList.toggle('active', i === dotIdx); });
    });
    dots.forEach(function(dot){
      dot.addEventListener('click', function(){
        var card    = modSlider.querySelector('.vm-mod-card');
        if (!card) return;
        var cardW   = card.offsetWidth + 16;
        modSlider.scrollTo({ left: parseInt(dot.dataset.index) * cardW * 2, behavior: 'smooth' });
      });
    });
  }
})();
</script>
<?= $v->end() ?>

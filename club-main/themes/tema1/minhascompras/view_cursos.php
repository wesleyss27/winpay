<?php
$v->layout("_theme_modulos");

/* ── aliases para o layout ── */
$curso        = $cursos;
$vmActivePage = 'cursos';

/* ── dados básicos ── */
$imgCover = !empty($cursos->imagem)
    ? url("uploads/cursos/" . $cursos->imagem)
    : url("uploads/cursos/default.png");

$boasVindas   = !empty($cursos->boas_vindas) ? $cursos->boas_vindas : null;
$bannerTitulo = !empty($cursos->banner_titulo) ? $cursos->banner_titulo : null;
$mostrarTitulo = ($cursos->mostrar_titulo_aulas ?? 1) == 1;

/* ── progresso geral ── */
$totalAulas = (int) $aulaObj->find("curso = {$cursos->id}")->count();
$registrosConcluidos = $aulaCl->find("aluno = {$_SESSION["id"]} AND curso = {$cursos->id}")->fetch(true);
$idsConcluidos = [];
if ($registrosConcluidos) {
    foreach ($registrosConcluidos as $r) $idsConcluidos[] = (int) $r->aula;
}
$contadorConcluidas = count($idsConcluidos);
$progress = $totalAulas > 0 ? round(($contadorConcluidas / $totalAulas) * 100) : 0;

/* ── liberação ── */
$dias = 0;
if (!empty($turma)) {
    $dias = floor((time() - strtotime($turma->created_at)) / 86400);
}

/* ── módulos disponíveis ── */
$modulosDisponiveis = [];
if ($modulos) {
    foreach ($modulos as $mod) {
        $liberado = (
            $cursos->liberacao == 1 ||
            ($cursos->liberacao == 3 && $dias >= 7) ||
            empty($turma)
        );
        if ($liberado) $modulosDisponiveis[] = $mod;
    }
}

/* ── próxima aula ── */
$proximaAula = null;
foreach ($modulosDisponiveis as $mod) {
    foreach ($aulas as $a) {
        if ($a->modulo != $mod->id) continue;
        $ok = ($a->dias_liberacao === null || $a->dias_liberacao === '') || empty($turma) || $dias >= (int)$a->dias_liberacao;
        if ($ok && !in_array($a->id, $idsConcluidos, true)) { $proximaAula = $a; break 2; }
    }
}
if (!$proximaAula && $aulas) $proximaAula = $aulas[0];

$certificadoDisponivel = ($totalAulas > 0 && $contadorConcluidas === $totalAulas);

/* ── aulas em andamento (video_progresso) ── */
$emAndamento = [];
try {
    $pdo = \CoffeeCode\DataLayer\Connect::getInstance();
    $stmt = $pdo->prepare("
        SELECT vp.aula_id, vp.percent_watched, vp.last_watched_at,
               a.nome AS aula_nome, a.imagem AS aula_imagem, a.modulo AS aula_modulo
        FROM video_progresso vp
        JOIN aulas a ON a.id = vp.aula_id
        WHERE vp.usuario = :u AND a.curso = :c AND vp.percent_watched < 95
        ORDER BY vp.last_watched_at DESC
        LIMIT 8
    ");
    $stmt->execute([':u' => (int)$_SESSION['id'], ':c' => (int)$cursos->id]);
    $emAndamento = $stmt->fetchAll(\PDO::FETCH_OBJ);
} catch (\Exception $e) { $emAndamento = []; }

/* ── progresso por módulo ── */
$progressoPorModulo = [];
foreach ($modulosDisponiveis as $mod) {
    $t = $c = 0;
    foreach ($aulas as $a) {
        if ($a->modulo != $mod->id) continue;
        $t++;
        if (in_array($a->id, $idsConcluidos, true)) $c++;
    }
    $progressoPorModulo[$mod->id] = ['total' => $t, 'concluidas' => $c];
}

/* ── modulosData para o sidebar do _theme_modulos ── */
$modulosData = [];
foreach ($modulosDisponiveis as $mod) {
    $info = $progressoPorModulo[$mod->id] ?? ['total' => 0, 'concluidas' => 0];
    $modulosData[] = [
        'mod'        => $mod,
        'liberado'   => true,
        'total'      => $info['total'],
        'concluidas' => $info['concluidas'],
        'progresso'  => $info['total'] > 0 ? round(($info['concluidas'] / $info['total']) * 100) : 0,
        'primeiraAula' => null,
    ];
}

/* ── nome do módulo por id ── */
$nomeModulo = [];
if ($modulos) foreach ($modulos as $m) $nomeModulo[$m->id] = $m->nome;
?>

<style>
/* ── SECTION HEADER ── */
.vm-section-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
.vm-section-title { font-size:.82rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--vm-text); margin:0; opacity:.9; }
.vm-slider-arrows { display:flex; gap:.35rem; }
.vm-arrow {
  width:30px; height:30px; border-radius:50%;
  background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.1);
  color:rgba(255,255,255,.6); display:flex; align-items:center; justify-content:center;
  cursor:pointer; font-size:.75rem; transition:background .2s, border-color .2s, color .2s;
}
.vm-arrow:hover { background:rgba(var(--vm-accent-rgb),.15); border-color:rgba(var(--vm-accent-rgb),.4); color:var(--vm-accent); }

/* ── SLIDER ── */
.vm-slider-outer { position:relative; }
.vm-slider { display:flex; gap:1rem; overflow-x:auto; scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch; scrollbar-width:none; padding-bottom:4px; }
.vm-slider::-webkit-scrollbar { display:none; }

/* ── CARD EM PROGRESSO (landscape) ── */
.vm-prog-card { flex-shrink:0; width:clamp(190px,22vw,270px); border-radius:10px; overflow:hidden; scroll-snap-align:start; display:flex; flex-direction:column; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); text-decoration:none; color:var(--vm-text); transition:transform .2s, border-color .25s; }
.vm-prog-card:hover { transform:translateY(-4px); border-color:rgba(var(--vm-accent-rgb),.35); color:var(--vm-text); }
.vm-prog-img { width:100%; aspect-ratio:16/9; object-fit:cover; display:block; }
.vm-prog-body { padding:.6rem .75rem .7rem; flex:1; display:flex; flex-direction:column; gap:.2rem; }
.vm-prog-tag { font-size:.6rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase; color:var(--vm-accent); margin-bottom:.1rem; }
.vm-prog-name { font-size:.75rem; font-weight:600; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.vm-prog-bar-bg { height:3px; border-radius:999px; background:rgba(255,255,255,.1); overflow:hidden; margin-top:.4rem; }
.vm-prog-bar-fill { height:100%; border-radius:999px; background:var(--vm-accent); }
.vm-prog-pct { font-size:.6rem; color:rgba(255,255,255,.35); margin-top:.15rem; }

/* ── CARD AULA — portrait 9:16, mesmo estilo vm-mod-card ── */
.vc-aula-card { flex-shrink:0; width:calc((100% - 3 * 1.2rem) / 4); border-radius:18px; overflow:hidden; scroll-snap-align:start; display:block; text-decoration:none; color:var(--vm-text); position:relative; transition:transform .3s ease, box-shadow .3s ease; background:#07080f; }
.vc-aula-cover { overflow:hidden; position:relative; }
.vc-aula-card:hover { transform:translateY(-8px) scale(1.025); box-shadow:0 24px 60px rgba(0,0,0,.7), 0 0 0 2px rgba(var(--vm-accent-rgb),.7); color:var(--vm-text); }
@media (max-width:900px) { .vc-aula-card { width:calc((100% - 2rem) / 3); } }
@media (max-width:560px) { .vc-aula-card { width:calc((100% - 1rem) / 2); } }
.vc-aula-img { display:block; width:100%; height:auto; position:relative; transition:transform .45s ease; background:#0a0a0f; }
.vc-aula-card:hover .vc-aula-img { transform:scale(1.08); }
.vc-aula-gradient { position:absolute; inset:0; z-index:1; background:linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,.05) 30%, rgba(0,0,0,.6) 60%, rgba(0,0,0,.93) 85%, rgba(0,0,0,1) 100%); }
.vc-aula-card::after { content:''; position:absolute; bottom:0; left:0; right:0; height:4px; z-index:5; background:var(--vm-accent); opacity:0; transition:opacity .3s ease; }
.vc-aula-card:hover::after { opacity:1; }
.vc-aula-name { position:absolute; bottom:1.1rem; left:1rem; right:1rem; font-size:clamp(.85rem,1.2vw,1rem); font-weight:800; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; z-index:3; text-shadow:0 2px 10px rgba(0,0,0,.8); }
.vc-aula-num { position:absolute; top:.6rem; left:.6rem; z-index:4; font-size:.54rem; letter-spacing:.14em; text-transform:uppercase; font-weight:700; color:#050507; background:var(--vm-accent); padding:.12rem .45rem; border-radius:999px; }
.vc-aula-done { position:absolute; top:.5rem; right:.5rem; z-index:4; width:22px; height:22px; border-radius:50%; background:rgba(74,222,128,.9); color:#050507; display:flex; align-items:center; justify-content:center; font-size:.65rem; }
.vc-aula-progress { position:absolute; bottom:0; left:0; right:0; height:5px; background:rgba(255,255,255,.1); z-index:4; }
.vc-aula-progress-fill { height:100%; background:var(--vm-accent); transition:width .4s ease; }
.vc-aula-locked { position:absolute; top:0; left:0; right:0; padding:.9rem 1rem; background:rgba(0,0,0,.7); backdrop-filter:blur(8px); display:flex; flex-direction:column; gap:.2rem; z-index:3; }
.vc-aula-locked-label { font-size:.75rem; font-weight:900; letter-spacing:.12em; text-transform:uppercase; color:#fff; }
.vc-aula-locked-sub { font-size:.62rem; font-weight:700; color:var(--vm-accent); letter-spacing:.06em; text-transform:uppercase; }
.vc-aula-lock-icon { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:3rem; color:rgba(255,255,255,.2); z-index:2; }
.vc-aula-btn { display:flex; align-items:center; justify-content:center; gap:.45rem; margin:.85rem .9rem .9rem; padding:.58rem 1rem; border-radius:10px; font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#f9fafb; background:linear-gradient(160deg,#1a1a1f 0%,#0d0d10 100%); border:1px solid rgba(255,255,255,.14); box-shadow:0 2px 0 rgba(0,0,0,.6),0 4px 12px rgba(0,0,0,.35),inset 0 1px 0 rgba(255,255,255,.07); cursor:pointer; transition:transform .15s ease, box-shadow .15s ease, background .15s ease; }
.vc-aula-btn::before { content:"▶"; font-size:.6rem; opacity:.7; }
.vc-aula-card:hover .vc-aula-btn { transform:translateY(-1px); box-shadow:0 4px 0 rgba(0,0,0,.55),0 8px 20px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.1); background:linear-gradient(160deg,#22222a 0%,#141418 100%); }

/* ── CHIP MÓDULO ── */
.vc-mod-chip { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .6rem; border-radius:999px; font-size:.62rem; letter-spacing:.1em; text-transform:uppercase; background:rgba(var(--vm-accent-rgb),.1); border:1px solid rgba(var(--vm-accent-rgb),.2); color:var(--vm-accent); flex-shrink:0; }
.vc-mod-chip.done { background:rgba(74,222,128,.08); border-color:rgba(74,222,128,.2); color:#4ade80; }

/* ── PROGRESSO DO MÓDULO ── */
.vc-mod-bar { height:2px; background:rgba(255,255,255,.07); border-radius:99px; overflow:hidden; margin-bottom:.7rem; }
.vc-mod-bar-fill { height:100%; background:rgba(var(--vm-accent-rgb),.5); border-radius:99px; }

/* ── DOTS ── */
.vm-dots { display:flex; justify-content:center; gap:.4rem; margin-top:1rem; }
.vm-dot { width:7px; height:7px; border-radius:50%; background:rgba(255,255,255,.2); cursor:pointer; transition:background .2s, transform .2s; }
.vm-dot.active { background:var(--vm-accent); transform:scale(1.3); }

/* ── CERTIFICADO ── */
.vc-cert { display:flex; flex-wrap:wrap; align-items:center; gap:1rem; padding:1.2rem 1.4rem; background:rgba(var(--vm-accent-rgb),.08); border:1px solid rgba(var(--vm-accent-rgb),.25); border-radius:18px; }
.vc-cert-icon { width:48px; height:48px; border-radius:14px; flex-shrink:0; background:rgba(var(--vm-accent-rgb),.12); border:1px solid rgba(var(--vm-accent-rgb),.22); display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:var(--vm-accent); }
.vc-cert-body { flex:1; min-width:160px; }
.vc-cert-body h3 { margin:0 0 .2rem; font-size:.95rem; font-weight:700; color:var(--vm-accent); }
.vc-cert-body p { margin:0; font-size:.8rem; color:rgba(255,255,255,.5); }
.vc-cert-btn { display:inline-flex; align-items:center; gap:.45rem; padding:.55rem 1.2rem; border-radius:999px; font-size:.75rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; background:var(--vm-accent); color:#050507; text-decoration:none; transition:filter .2s ease, transform .2s ease; white-space:nowrap; }
.vc-cert-btn:hover { filter:brightness(1.1); transform:translateY(-1px); color:#050507; }

/* ── EMPTY ── */
.vm-empty { text-align:center; padding:3rem 1rem; color:rgba(255,255,255,.3); font-size:.85rem; }
.vm-empty i { font-size:2rem; margin-bottom:.5rem; display:block; }
</style>

<!-- Em andamento -->
<?php if ($emAndamento) : ?>
<div>
  <div class="vm-section-head">
    <h2 class="vm-section-title"><i class="fas fa-circle-play" style="color:var(--vm-accent);margin-right:.4rem;"></i>Continuar assistindo</h2>
    <div class="vm-slider-arrows">
      <button class="vm-arrow" data-slider="prog" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
      <button class="vm-arrow" data-slider="prog" data-dir="1"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>
  <div class="vm-slider-outer">
    <div class="vm-slider" id="vc-slider-prog">
      <?php foreach ($emAndamento as $ea) :
        $eaCover   = !empty($ea->aula_imagem) ? url("uploads/cursos/" . $ea->aula_imagem) : $imgCover;
        $eaPct     = round((float)$ea->percent_watched);
        $eaModNome = $nomeModulo[$ea->aula_modulo] ?? '';
      ?>
        <a href="<?= url("minhas-compras/viewaulas/" . $ea->aula_id) ?>" class="vm-prog-card">
          <img src="<?= $eaCover ?>" alt="" class="vm-prog-img" loading="lazy">
          <div class="vm-prog-body">
            <?php if ($eaModNome && $mostrarTitulo) : ?>
              <div class="vm-prog-tag"><?= htmlspecialchars($eaModNome, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($mostrarTitulo) : ?>
              <div class="vm-prog-name"><?= htmlspecialchars($ea->aula_nome, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <div class="vm-prog-bar-bg">
              <div class="vm-prog-bar-fill" style="width:<?= $eaPct ?>%"></div>
            </div>
            <div class="vm-prog-pct"><?= $eaPct ?>% assistido</div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Módulos com aulas -->
<?php if ($modulosDisponiveis) :
  $aulaNumGlobal = [];
  foreach ($modulosDisponiveis as $mod) $aulaNumGlobal[$mod->id] = 0;
  $modIdx = 0;
?>
  <?php foreach ($modulosDisponiveis as $mod) :
    $info    = $progressoPorModulo[$mod->id] ?? ['total' => 0, 'concluidas' => 0];
    $modPct  = $info['total'] > 0 ? round(($info['concluidas'] / $info['total']) * 100) : 0;
    $modDone = $info['total'] > 0 && $info['concluidas'] === $info['total'];
    $aulasDoMod = [];
    foreach ($aulas as $a) { if ($a->modulo == $mod->id) $aulasDoMod[] = $a; }
    if (!$aulasDoMod) continue;
    $modIdx++;
    $sliderId = 'vc-slider-mod-' . $modIdx;
    $totalAulasSlide = count($aulasDoMod);
  ?>
  <div>
    <div class="vm-section-head">
      <h2 class="vm-section-title"><?= htmlspecialchars($mod->nome, ENT_QUOTES, 'UTF-8') ?></h2>
      <div style="display:flex;align-items:center;gap:.6rem;">
        <span class="vc-mod-chip <?= $modDone ? 'done' : '' ?>">
          <?= $modDone ? '<i class="fas fa-check"></i> Concluído' : $info['concluidas'] . ' / ' . $info['total'] ?>
        </span>
        <div class="vm-slider-arrows">
          <button class="vm-arrow" data-slider="<?= $sliderId ?>" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
          <button class="vm-arrow" data-slider="<?= $sliderId ?>" data-dir="1"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
    </div>

    <?php if ($modPct > 0 && !$modDone) : ?>
      <div class="vc-mod-bar"><div class="vc-mod-bar-fill" style="width:<?= $modPct ?>%"></div></div>
    <?php endif; ?>

    <div class="vm-slider-outer">
      <div class="vm-slider" id="<?= $sliderId ?>">
        <?php foreach ($aulasDoMod as $aula) :
          $aulaNumGlobal[$mod->id]++;
          $num = $aulaNumGlobal[$mod->id];
          $aulaLiberada = ($aula->dias_liberacao === null || $aula->dias_liberacao === '') || empty($turma) || $dias >= (int)$aula->dias_liberacao;
          $diasRest  = $aulaLiberada ? 0 : max(0, (int)$aula->dias_liberacao - $dias);
          $completou = $aulaLiberada && in_array($aula->id, $idsConcluidos, true);
          $cover     = !empty($aula->imagem) ? url("uploads/cursos/" . $aula->imagem) : $imgCover;
        ?>
          <?php if ($aulaLiberada) : ?>
            <a href="<?= url("minhas-compras/viewaulas/" . $aula->id) ?>" class="vc-aula-card">
              <div class="vc-aula-cover">
                <img src="<?= $cover ?>" alt="" class="vc-aula-img" loading="lazy">
                <div class="vc-aula-gradient"></div>
                <?php if ($completou) : ?>
                  <div class="vc-aula-done"><i class="fas fa-check"></i></div>
                <?php endif; ?>
                <?php if ($mostrarTitulo) : ?>
                  <div class="vc-aula-num"><?= str_pad($num, 2, '0', STR_PAD_LEFT) ?></div>
                  <div class="vc-aula-name"><?= htmlspecialchars($aula->nome, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($completou) : ?>
                  <div class="vc-aula-progress"><div class="vc-aula-progress-fill" style="width:100%"></div></div>
                <?php endif; ?>
              </div>
              <div class="vc-aula-btn">Assistir agora</div>
            </a>
          <?php else : ?>
            <div class="vc-aula-card">
              <div class="vc-aula-cover">
                <img src="<?= $cover ?>" alt="" class="vc-aula-img" loading="lazy" style="filter:brightness(.2) saturate(.2)">
                <div class="vc-aula-locked">
                  <div class="vc-aula-locked-label">Conteúdo Fechado</div>
                  <?php if ($diasRest > 0) : ?>
                    <div class="vc-aula-locked-sub">Em <?= $diasRest ?> dia<?= $diasRest != 1 ? 's' : '' ?></div>
                  <?php endif; ?>
                </div>
                <div class="vc-aula-lock-icon"><i class="fas fa-lock"></i></div>
                <?php if ($mostrarTitulo) : ?>
                  <div class="vc-aula-num" style="opacity:.3"><?= str_pad($num, 2, '0', STR_PAD_LEFT) ?></div>
                  <div class="vc-aula-name" style="opacity:.3"><?= htmlspecialchars($aula->nome, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if ($totalAulasSlide > 1) : ?>
    <div class="vm-dots" id="vm-dots-<?= $sliderId ?>">
      <?php for ($d = 0; $d < min($totalAulasSlide, 6); $d++) : ?>
        <div class="vm-dot <?= $d === 0 ? 'active' : '' ?>" data-index="<?= $d ?>"></div>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

<?php else : ?>
  <div class="vm-empty">
    <i class="fas fa-layer-group"></i>
    Os módulos deste curso ainda não estão liberados.
  </div>
<?php endif; ?>

<!-- Certificado -->
<?php if ($certificadoDisponivel) : ?>
  <div class="vc-cert">
    <div class="vc-cert-icon"><i class="fas fa-graduation-cap"></i></div>
    <div class="vc-cert-body">
      <h3>Parabéns! Curso concluído</h3>
      <p>Você finalizou todas as aulas. Seu certificado está disponível.</p>
    </div>
    <a href="<?= url("certificados/ver/" . $cursos->id) ?>" class="vc-cert-btn" style="flex-shrink:0;">
      <i class="fas fa-certificate"></i> Ver certificado
    </a>
  </div>
<?php endif; ?>

<?= $v->start("scripts") ?>
<script>
(function(){
  // Arrow scroll
  document.querySelectorAll('.vm-arrow').forEach(function(btn){
    btn.addEventListener('click', function(){
      var slider = document.getElementById(btn.dataset.slider);
      if (!slider) return;
      var card = slider.querySelector('.vc-aula-card, .vm-prog-card');
      var step = card ? (card.offsetWidth + 16) * (btn.dataset.slider === 'vc-slider-prog' ? 2 : 4) : slider.clientWidth;
      slider.scrollBy({ left: parseInt(btn.dataset.dir) * step, behavior:'smooth' });
    });
  });

  // Dots sync
  document.querySelectorAll('[id^="vm-dots-"]').forEach(function(dotsEl){
    var sliderId = dotsEl.id.replace('vm-dots-', '');
    var slider   = document.getElementById(sliderId);
    var dots     = dotsEl.querySelectorAll('.vm-dot');
    if (!slider || !dots.length) return;
    slider.addEventListener('scroll', function(){
      var card  = slider.querySelector('.vc-aula-card');
      if (!card) return;
      var cardW = card.offsetWidth + parseInt(getComputedStyle(slider).gap || 16);
      var idx   = Math.min(Math.round(slider.scrollLeft / cardW), dots.length - 1);
      dots.forEach(function(d, i){ d.classList.toggle('active', i === idx); });
    });
    dots.forEach(function(dot){
      dot.addEventListener('click', function(){
        var card  = slider.querySelector('.vc-aula-card');
        if (!card) return;
        var cardW = card.offsetWidth + parseInt(getComputedStyle(slider).gap || 16);
        slider.scrollTo({ left: parseInt(dot.dataset.index) * cardW * 4, behavior:'smooth' });
      });
    });
  });
})();
</script>
<?= $v->end() ?>

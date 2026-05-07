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
/* ════════════════════════════════
   VIEW CURSOS — usa --vm-accent do _theme_modulos
   ════════════════════════════════ */
:root { --vr: 18px; }

/* ── Breadcrumb ── */
.vc2-breadcrumb {
    display:flex; align-items:center; gap:.5rem;
    font-size:.7rem; letter-spacing:.14em; text-transform:uppercase;
    color:rgba(255,255,255,.35);
    margin-bottom: .5rem;
}
.vc2-breadcrumb a {
    color:rgba(255,255,255,.35); text-decoration:none;
    transition:color .2s;
}
.vc2-breadcrumb a:hover { color: var(--vm-accent); }

/* ── Hero split ── */
.vc2-hero {
    display: grid;
    grid-template-columns: clamp(180px,28%,320px) 1fr;
    min-height: clamp(200px,28vw,360px);
    border-radius: 22px;
    overflow: hidden;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.07);
    box-shadow: 0 20px 50px rgba(0,0,0,.5);
}
.vc2-hero-img {
    position: relative; overflow: hidden;
}
.vc2-hero-img img {
    width:100%; height:100%; object-fit:cover; object-position:center top;
    filter: grayscale(15%) brightness(.75);
    transition: transform .8s ease;
}
.vc2-hero:hover .vc2-hero-img img { transform: scale(1.04); }
.vc2-hero-img::after {
    content:""; position:absolute; inset:0;
    background: linear-gradient(to right, rgba(0,0,0,0) 50%, rgba(var(--vm-bg-rgb),1) 100%);
}
.vc2-hero-inner {
    display: flex; flex-direction: column; justify-content: center;
    padding: clamp(1.4rem,4vw,2.6rem);
    gap: 1rem;
    background: rgba(255,255,255,.03);
}
.vc2-hero-eyebrow {
    font-size: .6rem; letter-spacing: .24em; text-transform: uppercase;
    font-weight: 700; color: var(--vm-accent); opacity: .8;
}
.vc2-hero-course {
    font-size: clamp(1.2rem,3vw,2rem); font-weight: 900;
    letter-spacing: .02em; color: var(--vm-text); margin: 0; line-height: 1.15;
}
.vc2-hero-text {
    font-size: clamp(.78rem,1.2vw,.9rem);
    color: rgba(255,255,255,.55); line-height: 1.7;
    border-left: 2px solid rgba(var(--vm-accent-rgb),.35);
    padding-left: 1rem;
}
.vc2-progress-strip {
    display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
    padding: .75rem 1rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(var(--vm-accent-rgb),.1);
    border-radius: 12px;
}
.vc2-progress-strip-bar { flex:1; min-width:80px; height:4px; background:rgba(255,255,255,.09); border-radius:99px; overflow:hidden; }
.vc2-progress-strip-fill { height:100%; background: var(--vm-accent); border-radius:99px; }
.vc2-progress-label { font-size:.68rem; letter-spacing:.1em; text-transform:uppercase; color:rgba(255,255,255,.4); white-space:nowrap; }
.vc2-cta-btn {
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.55rem 1.2rem; border-radius:999px; font-size:.75rem;
    font-weight:700; letter-spacing:.1em; text-transform:uppercase;
    background: var(--vm-accent); color:#050507; text-decoration:none;
    transition: filter .2s ease, transform .2s ease;
    white-space: nowrap;
}
.vc2-cta-btn:hover { filter:brightness(1.1); transform:translateY(-1px); color:#050507; }
@media (max-width: 600px) {
    .vc2-hero { grid-template-columns: 1fr; }
    .vc2-hero-img { height: 160px; }
    .vc2-hero-img::after { background: linear-gradient(to bottom, rgba(0,0,0,0) 40%, rgba(var(--vm-bg-rgb),1) 100%); }
}

/* ── Cards "Em andamento" ── */
.vc2-watch-card {
    scroll-snap-align: start;
    flex: 0 0 clamp(200px,26vw,280px);
    display: flex; flex-direction: column; gap: .5rem;
    text-decoration: none; color: inherit;
}
.vc2-watch-thumb {
    position: relative; aspect-ratio: 16/9;
    border-radius: 14px; overflow: hidden;
    background: rgba(255,255,255,.05);
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
    transition: transform .28s ease, box-shadow .28s ease;
}
.vc2-watch-card:hover .vc2-watch-thumb {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 18px 40px rgba(0,0,0,.55);
}
.vc2-watch-thumb img { width:100%; height:100%; object-fit:cover; transition: transform .38s ease; }
.vc2-watch-card:hover .vc2-watch-thumb img { transform: scale(1.07); }
.vc2-watch-thumb-overlay {
    position:absolute; inset:0;
    background: rgba(0,0,0,0); transition: background .22s ease;
}
.vc2-watch-card:hover .vc2-watch-thumb-overlay { background: rgba(0,0,0,.22); }
.vc2-watch-pbar {
    position: absolute; bottom:0; left:0; right:0; height:3px;
    background: rgba(255,255,255,.1);
}
.vc2-watch-pbar-fill { height:100%; background: var(--vm-accent); border-radius:99px; }
.vc2-watch-meta {
    font-size: .6rem; letter-spacing: .14em; text-transform: uppercase;
    color: rgba(var(--vm-accent-rgb),.7); font-weight: 600;
}
.vc2-watch-title {
    font-size: .8rem; font-weight: 600;
    color: rgba(255,255,255,.8); line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
}
.vc2-watch-pct { font-size: .65rem; color: rgba(255,255,255,.3); }

/* ── Section header ── */
.vc2-section-hd {
    display:flex; flex-wrap:wrap; align-items:center;
    justify-content:space-between; gap:.5rem;
    margin-bottom:.85rem;
}
.vc2-section-hd h2 { margin:0; font-size:clamp(.9rem,1.8vw,1.1rem); font-weight:700; color:var(--vm-text); }
.vc2-mod-chip {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.2rem .6rem; border-radius:999px; font-size:.62rem;
    letter-spacing:.1em; text-transform:uppercase;
    background:rgba(var(--vm-accent-rgb),.1); border:1px solid rgba(var(--vm-accent-rgb),.2);
    color:var(--vm-accent); flex-shrink:0;
}
.vc2-mod-chip.done { background:rgba(74,222,128,.08); border-color:rgba(74,222,128,.2); color:#4ade80; }

/* ── Slider ── */
.vc2-slider {
    display:flex; gap:.85rem; overflow-x:auto; padding-bottom:.4rem;
    scroll-snap-type: x mandatory; -webkit-overflow-scrolling:touch;
}
.vc2-slider::-webkit-scrollbar { height:3px; }
.vc2-slider::-webkit-scrollbar-track { background:rgba(255,255,255,.04); border-radius:99px; }
.vc2-slider::-webkit-scrollbar-thumb { background:rgba(var(--vm-accent-rgb),.25); border-radius:99px; }

/* ── Portrait card (aulas) ── */
.vc2-card {
    scroll-snap-align: start;
    flex: 0 0 clamp(130px,16vw,200px);
    text-decoration:none; color:inherit;
    display:block;
}
.vc2-card-thumb {
    position:relative; border-radius:var(--vr);
    overflow:hidden; aspect-ratio: 2/3;
    background:rgba(255,255,255,.05);
    box-shadow: 0 12px 28px rgba(0,0,0,.45);
    transition: transform .28s ease, box-shadow .28s ease;
}
.vc2-card:hover .vc2-card-thumb {
    transform: translateY(-5px) scale(1.03);
    box-shadow: 0 20px 44px rgba(0,0,0,.6), 0 0 0 1.5px rgba(var(--vm-accent-rgb),.5);
}
.vc2-card-thumb img { width:100%; height:100%; object-fit:cover; transition: transform .38s ease; }
.vc2-card:hover .vc2-card-thumb img { transform:scale(1.07); }
.vc2-card-thumb::after {
    content:""; position:absolute; inset:0;
    background: linear-gradient(transparent 40%, rgba(0,0,0,.95) 100%);
    pointer-events:none;
}
.vc2-play {
    position:absolute; inset:0; z-index:1;
    background:rgba(0,0,0,0); transition:background .22s ease;
}
.vc2-card:hover .vc2-play { background:rgba(0,0,0,.2); }
.vc2-done-badge {
    position:absolute; top:.4rem; right:.4rem; z-index:3;
    width:20px; height:20px; border-radius:50%;
    background:rgba(74,222,128,.9); color:#050507;
    display:flex; align-items:center; justify-content:center;
    font-size:.6rem; box-shadow:0 4px 10px rgba(0,0,0,.4);
}
.vc2-card-label {
    position:absolute; bottom:0; left:0; right:0; z-index:2;
    padding: .5rem .55rem .6rem;
    display:flex; flex-direction:column; gap:.2rem;
}
.vc2-card-num {
    display:inline-block; align-self:flex-start;
    font-size:.54rem; letter-spacing:.14em; text-transform:uppercase;
    font-weight:700; color:#050507;
    background: var(--vm-accent);
    padding:.12rem .45rem; border-radius:999px;
}
.vc2-card-title {
    font-size:.75rem; font-weight:700; color:#f9fafb; line-height:1.25;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    text-shadow:0 1px 6px rgba(0,0,0,.7);
}
.vc2-locked-overlay {
    position:absolute; inset:0; z-index:2;
    background:rgba(0,0,0,.72); backdrop-filter:blur(4px);
    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.5rem;
    padding:.75rem;
}
.vc2-locked-overlay i { font-size:1.4rem; color:rgba(255,255,255,.3); }
.vc2-locked-label {
    font-size:.56rem; letter-spacing:.1em; text-transform:uppercase; font-weight:700;
    color:rgba(255,255,255,.3); text-align:center; line-height:1.4;
}
.vc2-mod-bar { height:2px; background:rgba(255,255,255,.07); border-radius:99px; overflow:hidden; margin-bottom:.6rem; }
.vc2-mod-bar-fill { height:100%; background:rgba(var(--vm-accent-rgb),.5); border-radius:99px; }

/* ── Certificado ── */
.vc2-cert {
    display:flex; flex-wrap:wrap; align-items:center; gap:1rem;
    padding:1.2rem 1.4rem;
    background:rgba(var(--vm-accent-rgb),.08);
    border:1px solid rgba(var(--vm-accent-rgb),.25); border-radius:18px;
}
.vc2-cert-icon {
    width:48px; height:48px; border-radius:14px; flex-shrink:0;
    background:rgba(var(--vm-accent-rgb),.12); border:1px solid rgba(var(--vm-accent-rgb),.22);
    display:flex; align-items:center; justify-content:center;
    font-size:1.3rem; color:var(--vm-accent);
}
.vc2-cert-body { flex:1; min-width:160px; }
.vc2-cert-body h3 { margin:0 0 .2rem; font-size:.95rem; font-weight:700; color:var(--vm-accent); }
.vc2-cert-body p { margin:0; font-size:.8rem; color:rgba(255,255,255,.5); }

@media (max-width:575.98px) {
    .vc2-card { flex: 0 0 44vw; }
}
</style>

<!-- Breadcrumb -->
<nav class="vc2-breadcrumb">
  <a href="<?= url("minhas-compras") ?>"><i class="fas fa-graduation-cap"></i> Meus Produtos</a>
  <i class="fas fa-chevron-right" style="font-size:.55rem;opacity:.3;"></i>
  <a href="<?= url("minhas-compras/viewmodulos/" . $cursos->id) ?>"><?= htmlspecialchars($cursos->nome, ENT_QUOTES, 'UTF-8') ?></a>
  <i class="fas fa-chevron-right" style="font-size:.55rem;opacity:.3;"></i>
  <span>Módulos</span>
</nav>

<!-- Hero -->
<div class="vc2-hero">
  <div class="vc2-hero-img">
    <img src="<?= $imgCover ?>" alt="">
  </div>
  <div class="vc2-hero-inner">
    <?php if ($mostrarTitulo) : ?>
      <span class="vc2-hero-eyebrow"><i class="fas fa-graduation-cap"></i>&nbsp; <?= htmlspecialchars($cursos->nome, ENT_QUOTES, 'UTF-8') ?></span>
    <?php endif; ?>
    <?php if ($bannerTitulo) : ?>
      <h1 class="vc2-hero-course"><?= htmlspecialchars($bannerTitulo, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php elseif ($mostrarTitulo) : ?>
      <h1 class="vc2-hero-course"><?= htmlspecialchars($cursos->nome, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php endif; ?>
    <?php if ($boasVindas) : ?>
      <p class="vc2-hero-text"><?= nl2br(htmlspecialchars($boasVindas, ENT_QUOTES, 'UTF-8')) ?></p>
    <?php endif; ?>
    <div class="vc2-progress-strip">
      <div class="vc2-progress-strip-bar">
        <div class="vc2-progress-strip-fill" style="width:<?= $progress ?>%"></div>
      </div>
      <span class="vc2-progress-label"><?= $progress ?>% concluído</span>
      <?php if ($proximaAula) : ?>
        <a href="<?= url("minhas-compras/viewaulas/" . $proximaAula->id) ?>" class="vc2-cta-btn">
          <i class="fas fa-play"></i> Continuar
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Em andamento -->
<?php if ($emAndamento) : ?>
  <div>
    <div class="vc2-section-hd">
      <h2><i class="fas fa-history" style="color:var(--vm-accent);margin-right:.45rem;font-size:.9em;"></i>Continuar assistindo</h2>
      <span style="font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.3);"><?= count($emAndamento) ?> aula<?= count($emAndamento) != 1 ? 's' : '' ?></span>
    </div>
    <div class="vc2-slider">
      <?php foreach ($emAndamento as $ea) :
        $eaCover = !empty($ea->aula_imagem) ? url("uploads/cursos/" . $ea->aula_imagem) : $imgCover;
        $eaPct   = round((float)$ea->percent_watched);
        $eaModNome = $nomeModulo[$ea->aula_modulo] ?? '';
      ?>
        <a href="<?= url("minhas-compras/viewaulas/" . $ea->aula_id) ?>" class="vc2-watch-card">
          <div class="vc2-watch-thumb">
            <img src="<?= $eaCover ?>" alt="">
            <div class="vc2-watch-thumb-overlay"></div>
            <div class="vc2-watch-pbar">
              <div class="vc2-watch-pbar-fill" style="width:<?= $eaPct ?>%"></div>
            </div>
          </div>
          <?php if ($mostrarTitulo) : ?>
            <?php if ($eaModNome) : ?>
              <span class="vc2-watch-meta"><?= htmlspecialchars($eaModNome, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <span class="vc2-watch-title"><?= htmlspecialchars($ea->aula_nome, ENT_QUOTES, 'UTF-8') ?></span>
            <?php if ($eaPct > 0) : ?>
              <span class="vc2-watch-pct"><?= $eaPct ?>% assistido</span>
            <?php endif; ?>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<!-- Módulos com aulas -->
<?php if ($modulosDisponiveis) :
  $aulaNumGlobal = [];
  foreach ($modulosDisponiveis as $mod) $aulaNumGlobal[$mod->id] = 0;
?>
  <?php foreach ($modulosDisponiveis as $mod) :
    $info    = $progressoPorModulo[$mod->id] ?? ['total' => 0, 'concluidas' => 0];
    $modPct  = $info['total'] > 0 ? round(($info['concluidas'] / $info['total']) * 100) : 0;
    $modDone = $info['total'] > 0 && $info['concluidas'] === $info['total'];

    $aulasDoMod = [];
    foreach ($aulas as $a) {
        if ($a->modulo == $mod->id) $aulasDoMod[] = $a;
    }
    if (!$aulasDoMod) continue;
  ?>
    <div>
      <div class="vc2-section-hd">
        <h2><?= htmlspecialchars($mod->nome, ENT_QUOTES, 'UTF-8') ?></h2>
        <span class="vc2-mod-chip <?= $modDone ? 'done' : '' ?>">
          <?= $modDone
              ? '<i class="fas fa-check"></i> Concluído'
              : $info['concluidas'] . ' / ' . $info['total'] ?>
        </span>
      </div>

      <?php if ($info['total'] > 0 && !$modDone && $modPct > 0) : ?>
        <div class="vc2-mod-bar">
          <div class="vc2-mod-bar-fill" style="width:<?= $modPct ?>%"></div>
        </div>
      <?php endif; ?>

      <div class="vc2-slider">
        <?php foreach ($aulasDoMod as $aula) :
          $aulaNumGlobal[$mod->id]++;
          $num = $aulaNumGlobal[$mod->id];

          $aulaLiberada = ($aula->dias_liberacao === null || $aula->dias_liberacao === '')
              || empty($turma)
              || $dias >= (int)$aula->dias_liberacao;
          $diasRest = $aulaLiberada ? 0 : max(0, (int)$aula->dias_liberacao - $dias);
          $completou = $aulaLiberada && in_array($aula->id, $idsConcluidos, true);

          $cover = !empty($aula->imagem)
              ? url("uploads/cursos/" . $aula->imagem)
              : $imgCover;
        ?>
          <?php if ($aulaLiberada) : ?>
            <a href="<?= url("minhas-compras/viewaulas/" . $aula->id) ?>"
               class="vc2-card <?= $completou ? 'is-done' : '' ?>"
               title="<?= htmlspecialchars($aula->nome, ENT_QUOTES, 'UTF-8') ?>">
              <div class="vc2-card-thumb">
                <img src="<?= $cover ?>" alt="">
                <div class="vc2-play"></div>
                <?php if ($completou) : ?>
                  <div class="vc2-done-badge"><i class="fas fa-check"></i></div>
                <?php endif; ?>
                <?php if ($mostrarTitulo) : ?>
                  <div class="vc2-card-label">
                    <span class="vc2-card-num"><?= str_pad($num, 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="vc2-card-title"><?= htmlspecialchars($aula->nome, ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                <?php endif; ?>
              </div>
            </a>
          <?php else : ?>
            <div class="vc2-card is-locked" title="Disponível em <?= $diasRest ?> dia<?= $diasRest != 1 ? 's' : '' ?>">
              <div class="vc2-card-thumb">
                <img src="<?= $cover ?>" alt="" style="filter:brightness(.2) saturate(.2)">
                <div class="vc2-locked-overlay">
                  <i class="fas fa-lock"></i>
                  <span class="vc2-locked-label">CONTEÚDO<br>FECHADO<?= $diasRest > 0 ? '<br><br>Em ' . $diasRest . 'd' : '' ?></span>
                </div>
                <?php if ($mostrarTitulo) : ?>
                  <div class="vc2-card-label" style="opacity:.3">
                    <span class="vc2-card-num"><?= str_pad($num, 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="vc2-card-title"><?= htmlspecialchars($aula->nome, ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

<?php else : ?>
  <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.3);font-size:.85rem;">
    <i class="fas fa-layer-group" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
    Os módulos deste curso ainda não estão liberados.
  </div>
<?php endif; ?>

<!-- Certificado -->
<?php if ($certificadoDisponivel) : ?>
  <div class="vc2-cert">
    <div class="vc2-cert-icon"><i class="fas fa-graduation-cap"></i></div>
    <div class="vc2-cert-body">
      <h3>Parabéns! Curso concluído</h3>
      <p>Você finalizou todas as aulas. Seu certificado está disponível.</p>
    </div>
    <a href="<?= url("certificados/ver/" . $cursos->id) ?>" class="vc2-cta-btn" style="flex-shrink:0;">
      <i class="fas fa-certificate"></i> Ver certificado
    </a>
  </div>
<?php endif; ?>

<?php
$v->layout("_theme");

$playlist = $all_aulas ?? [];
if (!$playlist) {
    $playlist = [];
}
if (!is_array($playlist)) {
    $playlist = [$playlist];
}

$currentIndex = 0;
$prevLesson = null;
$nextLesson = null;

foreach ($playlist as $index => $lessonItem) {
    if ((int) $lessonItem->id === (int) $id) {
        $currentIndex = $index;
        $prevLesson = $playlist[$index - 1] ?? null;
        $nextLesson = $playlist[$index + 1] ?? null;
        break;
    }
}

$prevUrl = $prevLesson ? url("minhas-compras/viewaulas/" . $prevLesson->id) : null;
$nextUrl = $nextLesson ? url("minhas-compras/viewaulas/" . $nextLesson->id) : null;

$totalLessons = count($playlist);
$currentPosition = $totalLessons > 0 ? $currentIndex + 1 : 1;

$courseCover = !empty($curso->imagem)
    ? url("uploads/cursos/" . $curso->imagem)
    : url("uploads/cursos/default.png");

$attachments = [];
if (!empty($aula->arquivo_anexo1)) {
    $attachments[] = [
        "label" => "Anexo 1",
        "file" => $aula->arquivo_anexo1,
    ];
}
if (!empty($aula->arquivo_anexo2)) {
    $attachments[] = [
        "label" => "Anexo 2",
        "file" => $aula->arquivo_anexo2,
    ];
}
if (!empty($aula->arquivo_anexo3)) {
    $attachments[] = [
        "label" => "Anexo 3",
        "file" => $aula->arquivo_anexo3,
    ];
}

$heroDescription = $aula->descricao
    ? mb_strimwidth(strip_tags($aula->descricao), 0, 220, "…")
    : "Acompanhe o conteúdo desta aula e avance no seu progresso.";

$courseName = htmlspecialchars($curso->nome ?? '', ENT_QUOTES, 'UTF-8');
$moduleName = htmlspecialchars($modulo->nome ?? '', ENT_QUOTES, 'UTF-8');
$lessonName = htmlspecialchars($aula->nome ?? '', ENT_QUOTES, 'UTF-8');
$currentUserName = htmlspecialchars($usuario->nome ?? '', ENT_QUOTES, 'UTF-8');
$courseCoverAttr = htmlspecialchars($courseCover, ENT_QUOTES, 'UTF-8');
$prevUrlAttr = $prevUrl ? htmlspecialchars($prevUrl, ENT_QUOTES, 'UTF-8') : null;
$nextUrlAttr = $nextUrl ? htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8') : null;
?>

<style>
.lesson-hero small {
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.68rem;
  color: rgba(244, 208, 63, 0.72);
  display: inline-block;
  margin-bottom: 0.35rem;
}

.lesson-hero-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.65rem;
  margin-top: 1.2rem;
}

.lesson-hero-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.4rem 0.9rem;
  border-radius: 999px;
  background: rgba(244, 208, 63, 0.18);
  border: 1px solid rgba(244, 208, 63, 0.28);
  font-size: 0.72rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--wh-text);
}

.lesson-layout {
  display: grid;
  gap: clamp(1.4rem, 2vw, 2rem);
}

@media (min-width: 992px) {
  .lesson-layout {
    grid-template-columns: minmax(0, 1fr) clamp(280px, 28vw, 360px);
    align-items: start;
  }
}

.lesson-main-stack {
  display: flex;
  flex-direction: column;
  gap: clamp(1.2rem, 2vw, 1.8rem);
}

.lesson-player-card {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.lesson-video {
  position: relative;
  overflow: hidden;
  border-radius: var(--wh-radius-md);
  border: 1px solid rgba(244, 208, 63, 0.18);
  background: radial-gradient(circle at 10% 10%, rgba(244, 208, 63, 0.18), transparent 65%);
  box-shadow: var(--wh-shadow-soft);
}

.lesson-video iframe,
.lesson-video video {
  width: 100%;
  min-height: clamp(240px, 42vw, 460px);
  display: block;
  border: none;
}

.lesson-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem 1.2rem;
  color: var(--wh-text-muted);
}

.lesson-meta-item {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.86rem;
}

.lesson-description {
  color: var(--wh-text-muted);
  line-height: 1.7;
}

.lesson-extra {
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
}

.lesson-attachments {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.lesson-attachments a {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.75rem 0.95rem;
  border-radius: var(--wh-radius-sm);
  border: 1px dashed rgba(244, 208, 63, 0.28);
  color: var(--wh-text);
  background: rgba(8, 12, 20, 0.82);
  text-decoration: none;
  transition: border-color 0.2s ease, background 0.2s ease;
}

.lesson-attachments a:hover {
  border-color: rgba(244, 208, 63, 0.42);
  background: rgba(8, 12, 20, 0.95);
}

.lesson-feedback {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.lesson-stars {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.lesson-stars i {
  font-size: 1.4rem;
  color: #bbb;
  transition: color 0.2s ease, transform 0.2s ease;
  cursor: pointer;
}

.lesson-stars i:hover {
  transform: translateY(-2px);
}

.lesson-comments-form {
  display: flex;
  gap: 0.9rem;
}

.lesson-comments-form textarea {
  resize: vertical;
  min-height: 90px;
}

.lesson-comments-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.lesson-comment {
  display: flex;
  gap: 0.9rem;
  padding: 1.1rem 1.2rem;
  background: rgba(8, 12, 20, 0.68);
  border-radius: var(--wh-radius-sm);
  border: 1px solid rgba(244, 208, 63, 0.16);
}

.lesson-comment .avatar img {
  width: 46px;
  height: 46px;
  border-radius: 50%;
  border: 2px solid rgba(244, 208, 63, 0.32);
  object-fit: cover;
}

.lesson-comment-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
}

.lesson-comment-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.65rem;
  align-items: center;
  justify-content: space-between;
}

.lesson-comment-meta h5 {
  margin: 0;
  font-size: 1rem;
  color: var(--wh-text);
  font-weight: 600;
}

.lesson-comment-time {
  font-size: 0.78rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--wh-text-soft);
}

.lesson-comment-text {
  color: var(--wh-text-muted);
  line-height: 1.6;
}

.btn-responder {
  align-self: flex-start;
  padding: 0.45rem 0.9rem;
  border-radius: 999px;
  font-size: 0.75rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  border: 1px solid rgba(244, 208, 63, 0.28);
  background: transparent;
  color: rgba(244, 208, 63, 0.85);
  transition: all 0.2s ease;
}

.btn-responder:hover {
  background: rgba(244, 208, 63, 0.12);
}

.lesson-reply-form {
  margin-top: 0.9rem;
  padding: 1rem;
  border-radius: var(--wh-radius-sm);
  background: rgba(8, 12, 20, 0.6);
  border: 1px solid rgba(244, 208, 63, 0.12);
}

.lesson-reply-form textarea,
.lesson-reply-form input[type="text"] {
  width: 100%;
  border-radius: var(--wh-radius-sm);
  border: 1px solid rgba(244, 208, 63, 0.24);
  background: rgba(15, 17, 24, 0.75);
  color: var(--wh-text);
  padding: 0.65rem 0.8rem;
  margin-bottom: 0.75rem;
}

.lesson-replies {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 0.8rem;
  border-left: 2px solid rgba(244, 208, 63, 0.22);
  padding-left: 1rem;
}

.lesson-reply {
  background: rgba(8, 12, 20, 0.55);
}

.lesson-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.lesson-playlist-card {
  position: sticky;
  top: clamp(1.2rem, 2vw, 2.2rem);
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.lesson-playlist-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-height: 70vh;
  overflow-y: auto;
  padding-right: 0.3rem;
}

.lesson-playlist-item {
  display: grid;
  grid-template-columns: auto 72px 1fr;
  align-items: center;
  gap: 0.8rem;
  padding: 0.75rem 0.85rem;
  border-radius: var(--wh-radius-sm);
  border: 1px solid rgba(244, 208, 63, 0.12);
  background: rgba(8, 12, 20, 0.62);
  text-decoration: none;
  color: var(--wh-text);
  transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
}

.lesson-playlist-item:hover {
  transform: translateX(6px);
  border-color: rgba(244, 208, 63, 0.36);
}

.lesson-playlist-item.is-active {
  border-color: rgba(244, 208, 63, 0.55);
  background: rgba(244, 208, 63, 0.18);
}

.lesson-playlist-index {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(244, 208, 63, 0.85);
}

.lesson-playlist-thumb {
  width: 72px;
  height: 46px;
  border-radius: var(--wh-radius-sm);
  overflow: hidden;
  background: rgba(244, 208, 63, 0.12);
}

.lesson-playlist-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.lesson-playlist-info h4 {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--wh-text);
}

.lesson-playlist-info span {
  display: block;
  font-size: 0.75rem;
  color: var(--wh-text-soft);
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-top: 0.3rem;
}

.lesson-feedback-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  align-items: center;
}

#btn-concluido-desk,
#btn-concluido-mobile {
  min-width: 160px;
}

@media (max-width: 991.98px) {
  .lesson-playlist-card {
    position: static;
  }

  .lesson-playlist-list {
    max-height: none;
  }
}
</style>

<div class="content-wrapper win-page">
  <section class="content">
    <div class="container-fluid">

      <section class="win-hero lesson-hero" style="background: linear-gradient(135deg, rgba(11, 15, 28, 0.9), rgba(5, 5, 8, 0.92)), url('<?= $courseCoverAttr; ?>') center/cover no-repeat;">
        <div class="win-page-title">
          <small><?= $courseName; ?> · <?= $moduleName; ?></small>
          <h2><?= $lessonName; ?></h2>
        </div>
        <p><?= $heroDescription; ?></p>

        <div class="lesson-hero-meta">
          <span class="lesson-hero-pill"><i class="far fa-circle"></i> Aula <?= str_pad($currentPosition, 2, "0", STR_PAD_LEFT); ?> de <?= $totalLessons; ?></span>
          <span class="lesson-hero-pill"><i class="far fa-folder-open"></i> <?= $moduleName; ?></span>
        </div>

        <div class="hero-actions" style="margin-top: 1.4rem;">
          <?php if ($prevUrlAttr) : ?>
            <a href="<?= $prevUrlAttr; ?>" class="btn-win-ghost">
              <i class="far fa-arrow-alt-circle-left"></i>
              Aula anterior
            </a>
          <?php endif; ?>

          <?php if ($nextUrlAttr) : ?>
            <a href="<?= $nextUrlAttr; ?>" class="btn-win">
              Próxima aula
              <i class="far fa-arrow-alt-circle-right"></i>
            </a>
          <?php endif; ?>
        </div>
      </section>

      <div class="lesson-layout">
        <div class="lesson-main-stack">

          <div class="win-simple-card lesson-player-card">
            <div class="lesson-video">
              <?php if (!empty($aula->video_id)) : ?>
                <!-- ── PLAYER HOSPEDADO ── -->
                <?php
                $streamUrl = $router->route("videos.stream", ["id" => $aula->video_id]);
                $progressoUrl = $router->route("videos.getProgresso", ["aula_id" => $aula->id]);
                $salvarUrl    = $router->route("videos.progresso");
                ?>
                <div id="win-player-wrap" style="position:relative;background:#000;border-radius:var(--wh-radius-md);overflow:hidden;">
                  <video id="win-video"
                    src="<?= htmlspecialchars($streamUrl, ENT_QUOTES, 'UTF-8') ?>"
                    preload="metadata"
                    playsinline
                    style="width:100%;display:block;min-height:clamp(240px,42vw,460px);">
                  </video>

                  <!-- overlay de loading -->
                  <div id="win-loader" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.55);pointer-events:none;">
                    <i class="fas fa-circle-notch fa-spin" style="font-size:2rem;color:var(--wh-accent);"></i>
                  </div>

                  <!-- overlay de oferta -->
                  <div id="win-oferta-overlay" style="display:none;position:absolute;inset:0;z-index:10;align-items:center;justify-content:center;background:rgba(0,0,0,0.72);padding:1.2rem;">
                    <div style="background:rgba(11,15,28,0.97);border:1px solid rgba(244,208,63,0.35);border-radius:var(--wh-radius-md);padding:1.4rem 1.6rem;max-width:380px;width:100%;position:relative;display:flex;flex-direction:column;gap:0.8rem;box-shadow:0 8px 32px rgba(0,0,0,0.55);">
                      <button id="win-oferta-fechar" type="button" style="position:absolute;top:0.6rem;right:0.8rem;background:none;border:none;color:rgba(255,255,255,0.55);font-size:1.1rem;cursor:pointer;line-height:1;" title="Fechar">
                        <i class="fas fa-times"></i>
                      </button>
                      <p style="margin:0;font-size:0.68rem;letter-spacing:0.14em;text-transform:uppercase;color:rgba(244,208,63,0.75);">Oferta especial</p>
                      <img id="win-oferta-img" src="" alt="" style="display:none;width:100%;max-height:140px;object-fit:cover;border-radius:var(--wh-radius-sm);">
                      <strong id="win-oferta-nome" style="font-size:1rem;line-height:1.3;color:#fff;"></strong>
                      <p id="win-oferta-desc" style="display:none;margin:0;font-size:0.83rem;color:rgba(255,255,255,0.65);line-height:1.5;"></p>
                      <span id="win-oferta-valor" style="display:none;font-size:1.25rem;font-weight:700;color:var(--wh-accent);"></span>
                      <a id="win-oferta-cta" href="#" target="_blank" rel="noopener"
                         style="display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.7rem 1.2rem;background:var(--wh-accent);color:#000;font-weight:700;font-size:0.88rem;border-radius:var(--wh-radius-sm);text-decoration:none;letter-spacing:0.06em;text-transform:uppercase;margin-top:0.2rem;">
                        Comprar agora <i class="fas fa-arrow-right"></i>
                      </a>
                    </div>
                  </div>

                  <!-- controles -->
                  <div id="win-controls" style="position:absolute;bottom:0;left:0;right:0;padding:0.5rem 0.9rem 0.7rem;background:linear-gradient(transparent,rgba(0,0,0,0.82));display:flex;flex-direction:column;gap:0.45rem;opacity:0;transition:opacity 0.25s;">

                    <!-- barra de progresso -->
                    <div id="win-seek-wrap" style="position:relative;height:5px;background:rgba(255,255,255,0.18);border-radius:99px;cursor:pointer;">
                      <div id="win-buffered-bar" style="position:absolute;left:0;top:0;height:100%;border-radius:99px;background:rgba(255,255,255,0.22);width:0%;pointer-events:none;"></div>
                      <div id="win-progress-bar" style="position:absolute;left:0;top:0;height:100%;border-radius:99px;background:var(--wh-accent);width:0%;pointer-events:none;"></div>
                      <div id="win-seek-thumb" style="position:absolute;top:50%;width:13px;height:13px;border-radius:50%;background:var(--wh-accent);transform:translate(-50%,-50%);left:0%;pointer-events:none;box-shadow:0 0 0 3px rgba(244,208,63,0.25);"></div>
                    </div>

                    <!-- linha de botões -->
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                      <button id="win-play-btn" type="button" style="background:none;border:none;color:#fff;font-size:1.2rem;cursor:pointer;padding:0.2rem 0.3rem;line-height:1;">
                        <i class="fas fa-play" id="win-play-icon"></i>
                      </button>

                      <button type="button" id="win-skip-back" style="background:none;border:none;color:#fff;font-size:0.9rem;cursor:pointer;padding:0.2rem;line-height:1;" title="Voltar 10s">
                        <i class="fas fa-rotate-left"></i><small style="font-size:0.65rem;vertical-align:middle;">10</small>
                      </button>

                      <div style="font-size:0.78rem;color:rgba(255,255,255,0.8);margin-left:0.1rem;white-space:nowrap;">
                        <span id="win-cur-time">0:00</span> / <span id="win-duration">0:00</span>
                      </div>

                      <div style="margin-left:auto;display:flex;align-items:center;gap:0.5rem;">
                        <!-- velocidade -->
                        <select id="win-speed" style="background:rgba(0,0,0,0.5);border:none;color:#fff;font-size:0.75rem;border-radius:4px;padding:0.1rem 0.25rem;cursor:pointer;">
                          <option value="0.5">0.5×</option>
                          <option value="0.75">0.75×</option>
                          <option value="1" selected>1×</option>
                          <option value="1.25">1.25×</option>
                          <option value="1.5">1.5×</option>
                          <option value="2">2×</option>
                        </select>

                        <!-- volume -->
                        <button id="win-mute-btn" type="button" style="background:none;border:none;color:#fff;font-size:1rem;cursor:pointer;padding:0.2rem;" title="Mudo">
                          <i class="fas fa-volume-high" id="win-vol-icon"></i>
                        </button>
                        <input id="win-vol" type="range" min="0" max="1" step="0.05" value="1" style="width:60px;accent-color:var(--wh-accent);">

                        <!-- fullscreen -->
                        <button id="win-fs-btn" type="button" style="background:none;border:none;color:#fff;font-size:1rem;cursor:pointer;padding:0.2rem;" title="Tela cheia">
                          <i class="fas fa-expand"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- barra de progresso assistido (fora do player) -->
                <div style="margin-top:0.5rem;display:flex;align-items:center;gap:0.7rem;">
                  <div style="flex:1;height:4px;background:rgba(255,255,255,0.08);border-radius:99px;overflow:hidden;">
                    <div id="win-watched-pct-bar" style="height:100%;background:var(--wh-accent);width:0%;transition:width 0.5s;"></div>
                  </div>
                  <span id="win-watched-pct-txt" style="font-size:0.72rem;color:var(--wh-text-soft);white-space:nowrap;">0% assistido</span>
                </div>

                <script>
                (function () {
                  const VIDEO_ID      = <?= (int)$aula->video_id ?>;
                  const AULA_ID       = <?= (int)$aula->id ?>;
                  const STREAM_URL    = <?= json_encode($streamUrl) ?>;
                  const ROUTE_GET     = <?= json_encode($progressoUrl) ?>;
                  const ROUTE_POST    = <?= json_encode($salvarUrl) ?>;
                  const OFERTAS_PLAYER = <?= json_encode($ofertasPlayer ?? []) ?>;

                  const video    = document.getElementById('win-video');
                  const wrap     = document.getElementById('win-player-wrap');
                  const loader   = document.getElementById('win-loader');
                  const controls = document.getElementById('win-controls');
                  const playBtn  = document.getElementById('win-play-btn');
                  const playIcon = document.getElementById('win-play-icon');
                  const seekWrap = document.getElementById('win-seek-wrap');
                  const progBar  = document.getElementById('win-progress-bar');
                  const bufBar   = document.getElementById('win-buffered-bar');
                  const thumb    = document.getElementById('win-seek-thumb');
                  const curTxt   = document.getElementById('win-cur-time');
                  const durTxt   = document.getElementById('win-duration');
                  const volSlider = document.getElementById('win-vol');
                  const volIcon  = document.getElementById('win-vol-icon');
                  const muteBtn  = document.getElementById('win-mute-btn');
                  const fsBtn    = document.getElementById('win-fs-btn');
                  const speedSel = document.getElementById('win-speed');
                  const pctBar   = document.getElementById('win-watched-pct-bar');
                  const pctTxt   = document.getElementById('win-watched-pct-txt');

                  // controle anti-skip: máximo segundo já assistido
                  let maxWatched   = 0;
                  let savedTime    = 0;     // tempo salvo no servidor (carregado ao iniciar)
                  let metaReady    = false; // loadedmetadata já disparou?
                  let watchStart   = null;  // timestamp quando começou a assistir
                  let totalWatched = 0;     // segundos acumulados nesta sessão
                  let hideTimer    = null;
                  let isSeeking    = false;

                  function fmtTime(s) {
                    s = Math.floor(s || 0);
                    const m = Math.floor(s / 60);
                    return m + ':' + String(s % 60).padStart(2, '0');
                  }

                  // ─── Aplica ponto salvo (só quando metadata E fetch estiverem prontos) ───
                  function applySavedTime() {
                    if (metaReady && savedTime > 2) {
                      video.currentTime = savedTime;
                      maxWatched = savedTime;
                    }
                  }

                  // ─── Carrega progresso salvo ───
                  fetch(ROUTE_GET)
                    .then(r => r.json())
                    .then(data => {
                      savedTime = data.current_time || 0;
                      updatePctUI(data.percent || 0);
                      applySavedTime();
                    })
                    .catch(() => {});

                  // ─── Eventos do vídeo ───
                  video.addEventListener('loadedmetadata', () => {
                    loader.style.display = 'none';
                    durTxt.textContent = fmtTime(video.duration);
                    metaReady = true;
                    applySavedTime();
                  });

                  video.addEventListener('waiting', () => { loader.style.display = 'flex'; });
                  video.addEventListener('canplay',  () => { loader.style.display = 'none'; });

                  video.addEventListener('play', () => {
                    playIcon.className = 'fas fa-pause';
                    watchStart = Date.now();
                  });

                  video.addEventListener('pause', () => {
                    playIcon.className = 'fas fa-play';
                    accumulateWatch();
                    saveProgress();
                  });

                  video.addEventListener('ended', () => {
                    playIcon.className = 'fas fa-play';
                    accumulateWatch();
                    saveProgress(true);
                  });

                  // ─── Overlay de oferta ───
                  const ofertaOverlay = document.getElementById('win-oferta-overlay');
                  const ofertaFechar  = document.getElementById('win-oferta-fechar');
                  const shownOfertas  = new Set(); // evita repetir na sessão

                  ofertaFechar.addEventListener('click', () => {
                    ofertaOverlay.style.display = 'none';
                    video.play();
                  });

                  function mostrarOferta(o) {
                    const img   = document.getElementById('win-oferta-img');
                    const nome  = document.getElementById('win-oferta-nome');
                    const desc  = document.getElementById('win-oferta-desc');
                    const valor = document.getElementById('win-oferta-valor');
                    const cta   = document.getElementById('win-oferta-cta');

                    nome.textContent = o.nome;

                    if (o.imagem) {
                      img.src = <?= json_encode(url('uploads/cursos/')) ?> + o.imagem;
                      img.style.display = 'block';
                    } else {
                      img.style.display = 'none';
                    }

                    if (o.descricao) {
                      desc.textContent = o.descricao;
                      desc.style.display = 'block';
                    } else {
                      desc.style.display = 'none';
                    }

                    if (o.valor) {
                      valor.textContent = 'R$ ' + parseFloat(o.valor).toFixed(2).replace('.', ',');
                      valor.style.display = 'inline';
                    } else {
                      valor.style.display = 'none';
                    }

                    cta.href = o.checkout_url;
                    ofertaOverlay.style.display = 'flex';
                    video.pause();
                  }

                  video.addEventListener('timeupdate', () => {
                    const cur = video.currentTime;
                    const dur = video.duration || 1;
                    const pct = cur / dur;

                    // atualiza barra de progresso
                    progBar.style.width  = (pct * 100) + '%';
                    thumb.style.left     = (pct * 100) + '%';
                    curTxt.textContent   = fmtTime(cur);

                    // buffered
                    if (video.buffered.length > 0) {
                      bufBar.style.width = (video.buffered.end(video.buffered.length - 1) / dur * 100) + '%';
                    }

                    // anti-skip: não deixa avançar além de maxWatched + 30s
                    if (!isSeeking && cur > maxWatched + 30) {
                      video.currentTime = maxWatched;
                    } else if (cur > maxWatched) {
                      maxWatched = cur;
                    }

                    // dispara overlay de oferta no segundo configurado
                    if (OFERTAS_PLAYER.length) {
                      OFERTAS_PLAYER.forEach((o, idx) => {
                        if (!shownOfertas.has(idx) && cur >= o.exibir_em && cur < o.exibir_em + 2) {
                          shownOfertas.add(idx);
                          mostrarOferta(o);
                        }
                      });
                    }
                  });

                  // ─── Auto-save a cada 10s enquanto estiver tocando ───
                  setInterval(() => {
                    if (!video.paused && !video.ended) saveProgress();
                  }, 10000);

                  // ─── Play / Pause ───
                  playBtn.addEventListener('click', togglePlay);
                  video.addEventListener('click',  togglePlay);

                  function togglePlay() {
                    if (video.paused) video.play(); else video.pause();
                  }

                  // ─── Skip back 10s ───
                  document.getElementById('win-skip-back').addEventListener('click', () => {
                    video.currentTime = Math.max(0, video.currentTime - 10);
                  });

                  // ─── Seek na barra ───
                  seekWrap.addEventListener('mousedown', startSeek);
                  seekWrap.addEventListener('touchstart', startSeek, { passive: true });

                  function startSeek(e) {
                    isSeeking = true;
                    seek(e);
                    document.addEventListener('mousemove', seek);
                    document.addEventListener('touchmove', seek, { passive: true });
                    document.addEventListener('mouseup',   endSeek);
                    document.addEventListener('touchend',  endSeek);
                  }

                  function seek(e) {
                    const rect  = seekWrap.getBoundingClientRect();
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const ratio   = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
                    const target  = ratio * (video.duration || 0);

                    // anti-skip
                    const clamped = Math.min(target, maxWatched + 30);
                    progBar.style.width = (clamped / (video.duration || 1) * 100) + '%';
                    thumb.style.left    = (clamped / (video.duration || 1) * 100) + '%';
                    curTxt.textContent  = fmtTime(clamped);
                  }

                  function endSeek(e) {
                    document.removeEventListener('mousemove', seek);
                    document.removeEventListener('touchmove', seek);
                    document.removeEventListener('mouseup',   endSeek);
                    document.removeEventListener('touchend',  endSeek);

                    const rect    = seekWrap.getBoundingClientRect();
                    const clientX = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
                    const ratio   = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
                    const target  = ratio * (video.duration || 0);
                    video.currentTime = Math.min(target, maxWatched + 30);
                    isSeeking = false;
                  }

                  // ─── Volume ───
                  volSlider.addEventListener('input', () => {
                    video.volume = volSlider.value;
                    video.muted  = video.volume === 0;
                    updateVolIcon();
                  });

                  muteBtn.addEventListener('click', () => {
                    video.muted = !video.muted;
                    updateVolIcon();
                  });

                  function updateVolIcon() {
                    if (video.muted || video.volume === 0) {
                      volIcon.className = 'fas fa-volume-xmark';
                    } else if (video.volume < 0.5) {
                      volIcon.className = 'fas fa-volume-low';
                    } else {
                      volIcon.className = 'fas fa-volume-high';
                    }
                  }

                  // ─── Velocidade ───
                  speedSel.addEventListener('change', () => {
                    video.playbackRate = parseFloat(speedSel.value);
                  });

                  // ─── Fullscreen ───
                  fsBtn.addEventListener('click', () => {
                    if (!document.fullscreenElement) {
                      wrap.requestFullscreen().catch(() => {});
                    } else {
                      document.exitFullscreen().catch(() => {});
                    }
                  });

                  document.addEventListener('fullscreenchange', () => {
                    fsBtn.querySelector('i').className = document.fullscreenElement
                      ? 'fas fa-compress' : 'fas fa-expand';
                  });

                  // ─── Mostrar/esconder controles ───
                  wrap.addEventListener('mousemove', showControls);
                  wrap.addEventListener('touchstart', showControls, { passive: true });

                  function showControls() {
                    controls.style.opacity = '1';
                    clearTimeout(hideTimer);
                    hideTimer = setTimeout(() => {
                      if (!video.paused) controls.style.opacity = '0';
                    }, 2800);
                  }

                  // ─── Acumula tempo assistido ───
                  function accumulateWatch() {
                    if (watchStart !== null) {
                      totalWatched += Math.round((Date.now() - watchStart) / 1000);
                      watchStart = null;
                    }
                  }

                  // ─── Salva progresso no servidor ───
                  function saveProgress(forceComplete) {
                    accumulateWatch();
                    const body = new URLSearchParams({
                      aula_id:      AULA_ID,
                      video_id:     VIDEO_ID,
                      current_time: video.currentTime,
                      duration:     video.duration || 0,
                      watch_seconds: totalWatched,
                    });
                    totalWatched = 0; // zera para não somar duplo

                    fetch(ROUTE_POST, { method: 'POST', body })
                      .then(r => r.json())
                      .then(data => updatePctUI(data.percent))
                      .catch(() => {});
                  }

                  function updatePctUI(pct) {
                    pctBar.style.width  = Math.min(100, pct) + '%';
                    pctTxt.textContent  = Math.round(pct) + '% assistido';
                  }

                  // salva ao sair da página via sendBeacon (confiável no unload)
                  window.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'hidden') saveProgress();
                  });
                  window.addEventListener('beforeunload', () => {
                    accumulateWatch();
                    const body = new URLSearchParams({
                      aula_id: AULA_ID, video_id: VIDEO_ID,
                      current_time: video.currentTime, duration: video.duration || 0,
                      watch_seconds: totalWatched,
                    });
                    navigator.sendBeacon(ROUTE_POST, body);
                  });
                })();
                </script>
              <?php else : ?>
                <!-- ── EMBED EXISTENTE (inalterado) ── -->
                <?php
                $url = $aula->link_video;

                if (strpos($url, 'youtube') !== false || strpos($url, 'youtu.be') !== false) {
                    $youtube_url = 'https://www.youtube.com/embed/';
                    preg_match('%(?:youtube(?:-nocookie)?\\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\\.be/)([^"&?/ ]{11})%i', $url, $matches);
                    $videoId = $matches[1] ?? null;
                    $new_url = $videoId
                        ? '<iframe src="' . $youtube_url . $videoId . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>'
                        : '<iframe src="' . $url . '" frameborder="0" allowfullscreen loading="lazy"></iframe>';
                } elseif (strpos($url, 'vimeo') !== false) {
                    $vimeo_url = 'https://player.vimeo.com/video/';
                    $videoId = substr(strrchr($url, '/'), 1);
                    $new_url = '<iframe src="' . $vimeo_url . $videoId . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen loading="lazy"></iframe>';
                } elseif (!empty($aula->script_player)) {
                    $new_url = base64_decode($aula->script_player);
                } else {
                    $new_url = '<iframe src="' . $aula->link_video . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen loading="lazy"></iframe>';
                }

                echo $new_url;
                ?>
              <?php endif; ?>
            </div>

            <div class="lesson-meta">
              <div class="lesson-meta-item">
                <i class="far fa-play-circle"></i>
                <span><?= $courseName; ?></span>
              </div>
              <div class="lesson-meta-item">
                <i class="far fa-clone"></i>
                <span><?= $moduleName; ?></span>
              </div>
              <div class="lesson-meta-item">
                <i class="far fa-dot-circle"></i>
                <span>Aula <?= str_pad($currentPosition, 2, "0", STR_PAD_LEFT); ?></span>
              </div>
            </div>

            <div class="lesson-description">
              <?php
              $lessonDescription = $aula->descricao ?? '';
              $encodedDescription = htmlspecialchars($lessonDescription, ENT_QUOTES, 'UTF-8');
              $linkedDescription = preg_replace_callback(
                  '~((?:https?://|www\.)[^\s<]+)~i',
                  function ($matches) {
                      $display = $matches[0];
                      $href = stripos($display, 'http') === 0 ? $display : 'https://' . $display;
                      $safeHref = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
                      return '<a href="' . $safeHref . '" target="_blank" rel="noopener noreferrer">' . $display . '</a>';
                  },
                  $encodedDescription
              );
              echo nl2br($linkedDescription);
              ?>
            </div>
          </div>

          <?php if ($attachments) : ?>
            <div class="win-simple-card lesson-extra">
              <div>
                <h3 class="mb-2" style="letter-spacing: 0.1em; text-transform: uppercase; font-size: 0.82rem; color: rgba(244, 208, 63, 0.85);">Material extra</h3>
                <p class="mb-0" style="color: var(--wh-text-muted);">Complete a aula com os materiais complementares abaixo.</p>
              </div>
              <div class="lesson-attachments">
                <?php foreach ($attachments as $attachment) :
                    $attachmentUrl = htmlspecialchars(url('uploads/cursos/' . $attachment['file']), ENT_QUOTES, 'UTF-8');
                    $attachmentLabel = htmlspecialchars($attachment['label'], ENT_QUOTES, 'UTF-8');
                    $attachmentName = htmlspecialchars(basename($attachment['file']), ENT_QUOTES, 'UTF-8');
                    ?>
                  <a href="<?= $attachmentUrl; ?>" download>
                    <i class="far fa-arrow-alt-circle-down"></i>
                    <span><?= $attachmentLabel; ?> · <small><?= $attachmentName; ?></small></span>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="win-simple-card lesson-feedback">
            <div>
              <h3 style="margin: 0; font-size: 1.15rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--wh-text);">Avaliação</h3>
              <p style="margin: 0.4rem 0 0; color: var(--wh-text-soft);">Como você avalia esta aula? Sua nota ajuda a melhorar a experiência.</p>
            </div>

            <div class="lesson-feedback-actions">
              <div class="lesson-stars d-none d-lg-flex">
                <i class="fa-solid fa-star estrela" id="star1" onmouseover="marcada(1, 'star')" onmouseout="normal(1, 'star')" onclick="avaliar(1)"></i>
                <i class="fa-solid fa-star estrela" id="star2" onmouseover="marcada(2, 'star')" onmouseout="normal(2, 'star')" onclick="avaliar(2)"></i>
                <i class="fa-solid fa-star estrela" id="star3" onmouseover="marcada(3, 'star')" onmouseout="normal(3, 'star')" onclick="avaliar(3)"></i>
                <i class="fa-solid fa-star estrela" id="star4" onmouseover="marcada(4, 'star')" onmouseout="normal(4, 'star')" onclick="avaliar(4)"></i>
                <i class="fa-solid fa-star estrela" id="star5" onmouseover="marcada(5, 'star')" onmouseout="normal(5, 'star')" onclick="avaliar(5)"></i>
              </div>

              <div class="lesson-stars d-lg-none">
                <i class="fa-solid fa-star estrela" id="starm1" onmouseover="marcada(1, 'starm')" onmouseout="normal(1, 'starm')" onclick="avaliar(1)"></i>
                <i class="fa-solid fa-star estrela" id="starm2" onmouseover="marcada(2, 'starm')" onmouseout="normal(2, 'starm')" onclick="avaliar(2)"></i>
                <i class="fa-solid fa-star estrela" id="starm3" onmouseover="marcada(3, 'starm')" onmouseout="normal(3, 'starm')" onclick="avaliar(3)"></i>
                <i class="fa-solid fa-star estrela" id="starm4" onmouseover="marcada(4, 'starm')" onmouseout="normal(4, 'starm')" onclick="avaliar(4)"></i>
                <i class="fa-solid fa-star estrela" id="starm5" onmouseover="marcada(5, 'starm')" onmouseout="normal(5, 'starm')" onclick="avaliar(5)"></i>
              </div>

              <?php if (!$aulaConcluida) : ?>
                <button type="button" id="btn-concluido-desk" class="btn btn-win">
                  <i class="far fa-check-circle"></i>
                  Marcar como concluída
                </button>
              <?php else : ?>
                <span class="lesson-hero-pill" style="background: rgba(244, 208, 63, 0.22); border-color: rgba(244, 208, 63, 0.42);"><i class="far fa-check-circle"></i> Aula concluída</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="win-simple-card">
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 0.8rem;">
              <div>
                <h3 style="margin: 0; font-size: 1.15rem; letter-spacing: 0.08em; text-transform: uppercase;">Comentários</h3>
                <p style="margin: 0.35rem 0 0; color: var(--wh-text-soft);">Compartilhe suas dúvidas e interaja com outros alunos.</p>
              </div>
            </div>

            <div class="lesson-comments" style="margin-top: 1.4rem;">
              <form action="<?= $router->route("aulas.comentar") ?>" name="comentar" method="post" class="lesson-comments-form">
                <input type="hidden" name="produtor" id="produtor" value="<?= $curso->usuario ?>">
                <input type="hidden" name="modulo" id="modulo" value="<?= $aula->modulo ?>">
                <input type="hidden" name="curso" id="curso" value="<?= $curso->id ?>">
                <input type="hidden" name="aula" id="aula" value="<?= $aula->id ?>">
                <input type="hidden" name="avaliacao" id="avaliacao">
                <input type="hidden" name="avaliacao_id" id="avaliacao_id" value="<?= $avaliacao->id ?? '' ?>">
                <input type="hidden" name="nota" id="nota" value="<?= $avaliacao->avaliacao ?? 0 ?>">

                <div class="avatar">
                  <?php if (!empty($usuario->foto)) :
                      $usuarioAvatar = htmlspecialchars(url_octa("uploads/perfil/{$usuario->foto}"), ENT_QUOTES, 'UTF-8');
                      ?>
                    <img src="<?= $usuarioAvatar; ?>" alt="<?= $currentUserName; ?>">
                  <?php else : ?>
                    <img src="https://static-00.iconduck.com/assets.00/avatar-icon-2048x2048-ilrgk6vk.png" alt="Avatar padrão">
                  <?php endif; ?>
                </div>
                <div style="flex: 1;">
                  <textarea name="comentario" id="comentario" class="form-control" placeholder="Escreva aqui seu comentário"></textarea>
                  <div class="d-flex justify-content-end" style="margin-top: 0.75rem;">
                    <button type="button" class="btn btn-win" id="btn_comentar">
                      <i class="far fa-paper-plane"></i>
                      Enviar comentário
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <?php if ($comentarios) : ?>
              <div class="lesson-comments-list" style="margin-top: 1.6rem;">
                <?php foreach ($comentarios as $comentario) :
                    $comentarioAluno = $comentario->dataAluno();
                    $comentarioNome = $comentarioAluno ? htmlspecialchars($comentarioAluno->nome, ENT_QUOTES, 'UTF-8') : 'Aluno';
                    $comentarioFoto = $comentarioAluno->foto ?? null;
                    $comentarioAvatar = $comentarioFoto ? htmlspecialchars(url_octa("uploads/perfil/{$comentarioFoto}"), ENT_QUOTES, 'UTF-8') : null;
                    $respostas = $resposta->find(
                        "resposta IS NOT NULL AND aula = {$aula->id} AND resposta = {$comentario->id}"
                    )->fetch(true);
                    ?>
                  <div class="lesson-comment">
                    <div class="avatar">
                      <?php if ($comentarioAvatar) : ?>
                        <img src="<?= $comentarioAvatar; ?>" alt="<?= $comentarioNome; ?>">
                      <?php else : ?>
                        <img src="https://static-00.iconduck.com/assets.00/avatar-icon-2048x2048-ilrgk6vk.png" alt="Avatar padrão">
                      <?php endif; ?>
                    </div>
                    <div class="lesson-comment-body">
                      <div class="lesson-comment-meta">
                        <h5><?= $comentarioNome; ?></h5>
                        <span class="lesson-comment-time"><?= formataDataHora($comentario->created_at) ?></span>
                      </div>
                      <div class="lesson-comment-text">
                        <?= $comentario->comentario ?>
                      </div>
                      <button class="btn-responder" id="responder<?= $comentario->id ?>" onclick="responder(<?= $comentario->id ?>)" type="button">Responder</button>

                      <div id="div_responder<?= $comentario->id ?>" class="lesson-reply-form" style="display: none;">
                        <form action="<?= $router->route("aulas.comentar") ?>" method="post" name="responder<?= $comentario->id ?>">
                          <input type="hidden" name="produtor" value="<?= $curso->usuario ?>">
                          <input type="hidden" name="modulo" value="<?= $aula->modulo ?>">
                          <input type="hidden" name="curso" value="<?= $curso->id ?>">
                          <input type="hidden" name="aula" value="<?= $aula->id ?>">
                          <input type="hidden" name="resposta" value="<?= $comentario->id ?>">
                          <textarea id="comentario_resposta<?= $comentario->id ?>" name="comentario" rows="2" placeholder="Responder a <?= $comentarioNome; ?>"></textarea>
                          <div class="d-flex justify-content-end" style="gap: 0.6rem;">
                            <button type="button" class="btn btn-win-ghost" onclick="cancelar(<?= $comentario->id ?>)">Cancelar</button>
                            <button type="button" class="btn btn-win" onclick="btnResponder(<?= $comentario->id ?>)">Comentar</button>
                          </div>
                        </form>
                      </div>

                      <?php if ($respostas) : ?>
                        <div class="lesson-replies">
                          <?php foreach ($respostas as $resp) :
                              $respAluno = $resp->dataAluno();
                              $respNome = $respAluno ? htmlspecialchars($respAluno->nome, ENT_QUOTES, 'UTF-8') : 'Aluno';
                              $respFoto = $respAluno->foto ?? null;
                              $respAvatar = $respFoto ? htmlspecialchars(url_octa("uploads/perfil/{$respFoto}"), ENT_QUOTES, 'UTF-8') : null;
                              ?>
                            <div class="lesson-comment lesson-reply">
                              <div class="avatar">
                                <?php if ($respAvatar) : ?>
                                  <img src="<?= $respAvatar; ?>" alt="<?= $respNome; ?>">
                                <?php else : ?>
                                  <img src="https://static-00.iconduck.com/assets.00/avatar-icon-2048x2048-ilrgk6vk.png" alt="Avatar padrão">
                                <?php endif; ?>
                              </div>
                              <div class="lesson-comment-body">
                                <div class="lesson-comment-meta">
                                  <h5><?= $respNome; ?></h5>
                                  <span class="lesson-comment-time"><?= formataDataHora($resp->created_at) ?></span>
                                </div>
                                <div class="lesson-comment-text">
                                  <?= $resp->comentario ?>
                                </div>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else : ?>
              <p style="margin-top: 1.2rem; color: var(--wh-text-soft);">Ainda não há comentários para esta aula. Seja o primeiro a compartilhar algo!</p>
            <?php endif; ?>
          </div>
        </div>

        <aside class="lesson-sidebar">

          <?php if (!empty($ofertaExibir)) :
            $ofertaImg = $ofertaExibir->imagem ? url("uploads/cursos/" . $ofertaExibir->imagem) : null;
          ?>
          <div class="win-simple-card" style="padding: 1.2rem; display: flex; flex-direction: column; gap: 0.8rem;">
            <p style="margin:0; font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: var(--wh-text-soft);">Oferta especial</p>

            <?php if ($ofertaImg) : ?>
              <img src="<?= htmlspecialchars($ofertaImg, ENT_QUOTES, 'UTF-8'); ?>" alt=""
                   style="width:100%; border-radius: var(--wh-radius-sm); object-fit: cover; max-height: 140px;">
            <?php endif; ?>

            <strong style="font-size: 1rem; line-height: 1.3;">
              <?= htmlspecialchars($ofertaExibir->nome, ENT_QUOTES, 'UTF-8'); ?>
            </strong>

            <?php if ($ofertaExibir->descricao) : ?>
              <p style="margin:0; font-size: 0.85rem; color: var(--wh-text-soft); line-height: 1.5;">
                <?= nl2br(htmlspecialchars($ofertaExibir->descricao, ENT_QUOTES, 'UTF-8')); ?>
              </p>
            <?php endif; ?>

            <?php if ($ofertaExibir->valor) : ?>
              <span style="font-size: 1.3rem; font-weight: 700; color: var(--wh-primary);">
                R$ <?= number_format((float)$ofertaExibir->valor, 2, ',', '.'); ?>
              </span>
            <?php endif; ?>

            <a href="<?= htmlspecialchars($ofertaExibir->checkout_url, ENT_QUOTES, 'UTF-8'); ?>"
               target="_blank" rel="noopener"
               class="btn-win" style="text-align: center;">
              Comprar agora
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
          <?php endif; ?>

          <div class="win-simple-card lesson-playlist-card">
            <div>
              <h3 style="margin: 0; font-size: 1.05rem; letter-spacing: 0.08em; text-transform: uppercase;">Playlist do módulo</h3>
              <p style="margin: 0.3rem 0 0; color: var(--wh-text-soft);">Avance ou retome as aulas quando desejar.</p>
            </div>

            <div class="lesson-playlist-list">
              <?php if ($playlist) : ?>
                <?php foreach ($playlist as $index => $lesson) :
                  $lessonImage = !empty($lesson->imagem) ? url("uploads/cursos/" . $lesson->imagem) : $courseCover;
                  $lessonImage = htmlspecialchars($lessonImage, ENT_QUOTES, 'UTF-8');
                  $lessonNameItem = htmlspecialchars($lesson->nome ?? '', ENT_QUOTES, 'UTF-8');
                  $lessonUrl = htmlspecialchars(url("minhas-compras/viewaulas/" . $lesson->id), ENT_QUOTES, 'UTF-8');
                  $isActive = (int) $lesson->id === (int) $id;
                  ?>
                  <a class="lesson-playlist-item <?= $isActive ? 'is-active' : '' ?>" href="<?= $lessonUrl; ?>">
                    <span class="lesson-playlist-index">A<?= str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                    <div class="lesson-playlist-thumb">
                      <img src="<?= $lessonImage; ?>" alt="<?= $lessonNameItem; ?>">
                    </div>
                    <div class="lesson-playlist-info">
                      <h4><?= $lessonNameItem; ?></h4>
                      <?php if ($isActive) : ?>
                        <span>Assistindo agora</span>
                      <?php else : ?>
                        <span>Abrir aula</span>
                      <?php endif; ?>
                    </div>
                  </a>
                <?php endforeach; ?>
              <?php else : ?>
                <p style="color: var(--wh-text-soft);">Nenhuma aula cadastrada neste módulo ainda.</p>
              <?php endif; ?>
            </div>

            <div class="d-flex flex-column flex-sm-row" style="gap: 0.6rem;">
              <?php if ($prevUrlAttr) : ?>
                <a href="<?= $prevUrlAttr; ?>" class="btn btn-win-ghost" style="flex: 1; text-align: center;">
                  <i class="far fa-arrow-alt-circle-left"></i>
                  Aula anterior
                </a>
              <?php endif; ?>
              <?php if ($nextUrlAttr) : ?>
                <a href="<?= $nextUrlAttr; ?>" class="btn btn-win" style="flex: 1; text-align: center;">
                  Próxima aula
                  <i class="far fa-arrow-alt-circle-right"></i>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>
</div>

<?= $v->start("scripts") ?>
<script>
  function normal(num, id = 'star') {
    if (!num) {
      return;
    }

    for (let i = 5; i > num; i--) {
      $('#' + id + i).css('color', '#bbb');
    }
  }

  function marcada(num, id = 'star') {
    if (!num) {
      return;
    }

    for (let i = 1; i <= num; i++) {
      $('#' + id + i).css('color', '#ff0');
    }
  }

  const avaliacaoAtual = parseInt($('#nota').val() || '0', 10);
  marcada(avaliacaoAtual);
  marcada(avaliacaoAtual, 'starm');

  function avaliar(num, id = 'star') {
    for (let i = 1; i <= 5; i++) {
      $('#' + id + i).css('color', i <= num ? '#ff0' : '#bbb');
    }

    $('#avaliacao').val(num);
    $('#nota').val(num);
    const form = $('form[name=comentar]');

    $.ajax({
      url: "<?= $router->route("aulas.avaliar") ?>",
      type: 'POST',
      data: form.serialize(),
      success: function(callback) {
        alert(callback);
        location.reload();
      }
    });
  }

  $('#btn-concluido-desk, #btn-concluido-mobile').on('click', function(e) {
    e.preventDefault();
    const form = $('form[name=comentar]');
    concluido(form);
  });

  function concluido(form) {
    $.ajax({
      url: "<?= $router->route("aulas.concluir") ?>",
      type: 'POST',
      data: form.serialize(),
      success: function(callback) {
        alert(callback);
        location.reload();
      }
    });
  }

  $('#btn_comentar').on('click', function(e) {
    e.preventDefault();
    const form = $('form[name=comentar]');
    const comentario = $('#comentario').val().trim();
    if (comentario) {
      salvar(form);
    } else {
      alert('Digite um comentário antes de enviar.');
    }
  });

  function salvar(form) {
    $.ajax({
      url: "<?= $router->route("aulas.comentar") ?>",
      type: 'POST',
      data: form.serialize(),
      success: function(callback) {
        alert(callback);
        location.reload();
      }
    });
  }

  function responder(id) {
    $('#responder' + id).hide();
    $('#div_responder' + id).slideDown();
  }

  function cancelar(id) {
    $('#div_responder' + id).slideUp(function() {
      $('#responder' + id).show();
    });
  }

  function btnResponder(id) {
    const form = $('form[name=responder' + id + ']');
    const comentario = $('#comentario_resposta' + id).val().trim();
    if (comentario) {
      salvar(form);
    } else {
      alert('Digite um comentário antes de enviar.');
    }
  }
</script>
<?= $v->end() ?>

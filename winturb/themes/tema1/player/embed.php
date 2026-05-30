<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($video->titulo ?? "Player", ENT_QUOTES) ?></title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --accent: <?= preg_match('/^#[0-9a-fA-F]{6}$/', $video->cor_acento ?? '') ? htmlspecialchars($video->cor_acento, ENT_QUOTES) : '#f97316' ?>;
      --bg:     <?= preg_match('/^#[0-9a-fA-F]{6}$/', $video->cor_fundo  ?? '') ? htmlspecialchars($video->cor_fundo,  ENT_QUOTES) : '#000000' ?>;
    }

    html, body {
      width: 100%; height: 100%;
      background: var(--bg);
      overflow: hidden;
      font-family: system-ui, -apple-system, sans-serif;
    }

    /* ── Wrap ── */
    #wt-wrap {
      position: relative;
      width: 100%; height: 100%;
      background: var(--bg);
    }

    /* ── Video ── */
    #wt-video {
      width: 100%; height: 100%;
      display: block;
      object-fit: contain;
      opacity: 0;
      transition: opacity .35s;
    }
    #wt-video.ready { opacity: 1; }

    /* ── Poster overlay ── */
    #wt-poster {
      position: absolute; inset: 0; z-index: 5;
      background: #0a0a0a center/cover no-repeat;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
      transition: opacity .35s;
    }
    #wt-poster.hidden { opacity: 0; pointer-events: none; }

    /* Vignette sobre a capa */
    #wt-poster::after {
      content: '';
      position: absolute; inset: 0;
      background: radial-gradient(ellipse at center, transparent 30%, rgba(0,0,0,.55) 100%);
      pointer-events: none;
    }

    /* ── Play button ── */
    #wt-play-btn {
      position: relative;
      width: 72px; height: 72px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      z-index: 1;
    }
    /* Glow ring pulsante */
    #wt-play-btn::before {
      content: '';
      position: absolute; inset: -10px;
      border-radius: 50%;
      background: radial-gradient(circle, color-mix(in srgb, var(--accent) 35%, transparent), transparent 70%);
      animation: ring-pulse 2.2s ease-in-out infinite;
      opacity: 0;
    }
    @keyframes ring-pulse {
      0%, 100% { transform: scale(.85); opacity: 0; }
      50%       { transform: scale(1.15); opacity: 1; }
    }
    /* Círculo branco semitransparente */
    #wt-play-btn-inner {
      width: 72px; height: 72px;
      border-radius: 50%;
      background: rgba(255,255,255,.14);
      border: 1.5px solid rgba(255,255,255,.28);
      backdrop-filter: blur(8px);
      display: flex; align-items: center; justify-content: center;
      transition: transform .18s cubic-bezier(.22,1,.36,1),
                  background .18s, border-color .18s;
      box-shadow: 0 8px 32px rgba(0,0,0,.5);
    }
    #wt-poster:hover #wt-play-btn-inner {
      transform: scale(1.1);
      background: var(--accent);
      border-color: var(--accent);
    }
    #wt-play-btn svg { width: 26px; height: 26px; fill: #fff; margin-left: 4px; }

    /* ── Loader ── */
    #wt-loader {
      position: absolute; inset: 0; z-index: 4;
      display: none; align-items: center; justify-content: center;
      pointer-events: none;
    }
    #wt-loader.show { display: flex; }
    .wt-spin {
      width: 32px; height: 32px;
      border: 2px solid rgba(255,255,255,.15);
      border-top-color: var(--accent);
      border-radius: 50%;
      animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Oferta desktop ── */
    #wt-oferta-overlay {
      display: none;
      position: absolute; inset: 0; z-index: 10;
      align-items: center; justify-content: center;
      background: rgba(0,0,0,.68);
      backdrop-filter: blur(3px);
      padding: 1.2rem;
    }
    .wt-oferta-card {
      background: #0f1117;
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 16px;
      padding: 1.5rem 1.7rem;
      max-width: 360px; width: 100%;
      position: relative;
      display: flex; flex-direction: column; gap: .9rem;
      box-shadow: 0 24px 64px rgba(0,0,0,.7);
      animation: card-pop .22s cubic-bezier(.22,1,.36,1);
    }
    @keyframes card-pop {
      from { transform: scale(.93) translateY(8px); opacity: 0; }
      to   { transform: scale(1) translateY(0); opacity: 1; }
    }
    .wt-oferta-tag {
      font-size: .6rem; font-weight: 700; letter-spacing: .16em;
      text-transform: uppercase; color: var(--accent);
      display: flex; align-items: center; gap: .35rem;
    }
    .wt-oferta-tag::before {
      content: '';
      display: inline-block; width: 6px; height: 6px;
      background: var(--accent); border-radius: 50%;
      animation: blink 1.4s ease-in-out infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
    #wt-fechar-oferta {
      position: absolute; top: .8rem; right: .9rem;
      background: rgba(255,255,255,.07); border: none;
      color: rgba(255,255,255,.5);
      width: 26px; height: 26px; border-radius: 50%;
      font-size: .8rem; cursor: pointer; line-height: 1;
      display: flex; align-items: center; justify-content: center;
      transition: background .12s, color .12s;
    }
    #wt-fechar-oferta:hover { background: rgba(255,255,255,.14); color: #fff; }
    #wt-oferta-img   { display: none; width: 100%; max-height: 120px; object-fit: cover; border-radius: 10px; }
    #wt-oferta-nome  { font-size: .95rem; font-weight: 600; color: #fff; line-height: 1.35; }
    #wt-oferta-desc  { display: none; font-size: .8rem; color: rgba(255,255,255,.55); line-height: 1.55; }
    #wt-oferta-valor { display: none; font-size: 1.15rem; font-weight: 700; color: var(--accent); }
    #wt-oferta-cta {
      display: flex; align-items: center; justify-content: center; gap: .45rem;
      padding: .68rem 1.2rem;
      background: var(--accent); color: #fff;
      font-weight: 700; font-size: .82rem;
      border-radius: 10px; text-decoration: none;
      letter-spacing: .06em; text-transform: uppercase;
      transition: filter .15s;
    }
    #wt-oferta-cta:hover { filter: brightness(1.12); }

    /* ── Banner mobile ── */
    #wt-oferta-banner {
      display: none;
      position: absolute; bottom: 58px; left: 0; right: 0; z-index: 10;
      padding: 0 .6rem;
    }
    .wt-banner-inner {
      background: rgba(10,10,14,.92);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 10px; padding: .5rem .75rem;
      display: flex; align-items: center; gap: .65rem;
      backdrop-filter: blur(8px);
    }
    #wt-banner-nome  { flex: 1; font-size: .8rem; color: #fff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #wt-banner-cta   {
      flex-shrink: 0; padding: .33rem .75rem;
      background: var(--accent); color: #fff;
      font-size: .72rem; font-weight: 700; border-radius: 6px;
      text-decoration: none; white-space: nowrap;
    }
    #wt-banner-fechar {
      flex-shrink: 0; background: none; border: none;
      color: rgba(255,255,255,.45); font-size: .9rem; cursor: pointer; padding: .1rem;
    }

    /* ── Controles ── */
    #wt-controls {
      position: absolute; bottom: 0; left: 0; right: 0;
      padding: 2.5rem .9rem .75rem;
      background: linear-gradient(to top, rgba(0,0,0,.88) 0%, transparent 100%);
      display: flex; flex-direction: column; gap: .5rem;
      opacity: 0; transition: opacity .2s;
      z-index: 3;
    }

    /* Seekbar */
    #wt-seek-wrap {
      position: relative;
      height: 4px;
      background: rgba(255,255,255,.18);
      border-radius: 99px;
      cursor: pointer; touch-action: none;
      padding: 7px 0; margin: -7px 0; box-sizing: content-box;
      transition: height .15s;
    }
    #wt-seek-wrap:hover { height: 5px; }
    #wt-buf-bar {
      position: absolute; left: 0; top: 0; height: 100%;
      background: rgba(255,255,255,.2); border-radius: 99px;
      width: 0%; pointer-events: none;
    }
    #wt-prog-bar {
      position: absolute; left: 0; top: 0; height: 100%;
      background: var(--accent); border-radius: 99px;
      width: 0%; pointer-events: none;
      box-shadow: 0 0 6px color-mix(in srgb, var(--accent) 60%, transparent);
    }
    #wt-thumb {
      position: absolute; top: 50%;
      width: 12px; height: 12px; border-radius: 50%;
      background: #fff;
      transform: translate(-50%, -50%);
      left: 0%;
      pointer-events: none;
      box-shadow: 0 0 0 2px rgba(0,0,0,.5), 0 0 8px rgba(0,0,0,.4);
      opacity: 0; transition: opacity .15s;
    }
    #wt-seek-wrap:hover #wt-thumb { opacity: 1; }

    /* Controles inferiores */
    #wt-controls-row {
      display: flex; align-items: center; gap: .5rem;
    }
    .wt-btn {
      background: none; border: none;
      color: rgba(255,255,255,.85); cursor: pointer;
      padding: .25rem; line-height: 1;
      display: flex; align-items: center; justify-content: center;
      border-radius: 5px;
      transition: color .12s, background .12s;
    }
    .wt-btn:hover { color: #fff; background: rgba(255,255,255,.1); }
    .wt-btn svg { width: 18px; height: 18px; fill: currentColor; }
    .wt-btn-sm svg { width: 15px; height: 15px; }

    #wt-time {
      font-size: .73rem; color: rgba(255,255,255,.72);
      white-space: nowrap; margin-left: .1rem;
      font-variant-numeric: tabular-nums;
      letter-spacing: .02em;
    }
    #wt-right { margin-left: auto; display: flex; align-items: center; gap: .35rem; }

    #wt-speed {
      background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.12);
      color: rgba(255,255,255,.8); font-size: .7rem; border-radius: 5px;
      padding: .15rem .35rem; cursor: pointer; outline: none;
    }
    #wt-speed:hover { background: rgba(255,255,255,.16); }

    #wt-vol {
      width: 56px; height: 3px;
      accent-color: var(--accent);
      cursor: pointer;
    }

    /* ── Barra fake (decorativa, não clicável) ── */
    #wt-fake-seek-wrap {
      display: none;
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 10px;
      background: rgba(255,255,255,.18);
      border-radius: 99px;
      pointer-events: none;
      z-index: 6;
      margin: 0 .9rem 0.75rem;
    }
    #wt-fake-prog-bar {
      position: absolute; left: 0; top: 0; height: 100%;
      background: var(--accent); border-radius: 99px;
      width: 0%;
      box-shadow: 0 0 6px color-mix(in srgb, var(--accent) 60%, transparent);
      transition: width .5s linear;
    }

    /* Barra de progresso assistido */
    #wt-watched-wrap {
      position: absolute; bottom: 0; left: 0; right: 0;
      height: 2px; background: rgba(255,255,255,.05);
      pointer-events: none; z-index: 2;
    }
    #wt-watched-fill {
      height: 100%;
      background: color-mix(in srgb, var(--accent) 45%, transparent);
      width: 0%; transition: width .6s;
    }

    @media (max-width: 480px) {
      #wt-vol, #wt-mute { display: none !important; }
      .wt-btn svg { width: 16px; height: 16px; }
    }

    /* ── Unmute overlay (autoplay mudo) ── */
    #wt-unmute-pill {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      z-index: 7;
      display: flex; flex-direction: column; align-items: center; gap: .65rem;
      background: rgba(0,0,0,.78);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1.5px solid rgba(255,255,255,.18);
      border-radius: 20px;
      padding: 1.5rem 2.2rem;
      color: #fff;
      cursor: pointer;
      white-space: nowrap;
      animation: unmute-in .4s cubic-bezier(.22,1,.36,1) both;
      transition: background .15s, transform .15s;
      box-shadow: 0 12px 40px rgba(0,0,0,.55);
    }
    #wt-unmute-pill:hover {
      background: rgba(15,10,40,.9);
      transform: translate(-50%, -50%) scale(1.04);
    }
    .unmute-icon-wrap {
      width: 52px; height: 52px; border-radius: 50%;
      background: rgba(255,255,255,.12);
      border: 1.5px solid rgba(255,255,255,.2);
      display: flex; align-items: center; justify-content: center;
    }
    #wt-unmute-pill svg { width: 24px; height: 24px; fill: #fff; }
    #wt-unmute-pill .unmute-label {
      font-size: .84rem; font-weight: 700; letter-spacing: .03em;
    }
    #wt-unmute-pill .unmute-sub {
      font-size: .7rem; color: rgba(255,255,255,.5); margin-top: -.3rem;
    }
    @keyframes unmute-in {
      from { opacity: 0; transform: translate(-50%, -50%) scale(.88); }
      to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
  </style>
<?php if ($hlsUrl): ?>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1/dist/hls.min.js"></script>
<?php endif; ?>
</head>
<body>

<div id="wt-wrap">

  <video id="wt-video"
    <?php if (!$hlsUrl): ?>src="<?= htmlspecialchars($streamUrl, ENT_QUOTES) ?>"<?php endif; ?>
    <?php if ($video->capa) : ?>poster="<?= htmlspecialchars(url("uploads/capas/{$video->capa}"), ENT_QUOTES) ?>"<?php endif; ?>
    preload="none"
    playsinline>
  </video>

  <!-- Poster + play button -->
  <div id="wt-poster"
    <?php if ($video->capa) : ?>style="background-image:url('<?= htmlspecialchars(url("uploads/capas/{$video->capa}"), ENT_QUOTES) ?>')"<?php endif; ?>>
    <div id="wt-play-btn">
      <div id="wt-play-btn-inner">
        <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
      </div>
    </div>
  </div>

  <!-- Loader -->
  <div id="wt-loader"><div class="wt-spin"></div></div>

  <!-- Unmute overlay (autoplay mudo) -->
  <div id="wt-unmute-pill" style="display:none">
    <div class="unmute-icon-wrap">
      <svg viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
    </div>
    <span class="unmute-label">Clique para ouvir</span>
    <span class="unmute-sub">Vídeo com som desativado</span>
  </div>

  <!-- Oferta desktop -->
  <div id="wt-oferta-overlay">
    <div class="wt-oferta-card">
      <button id="wt-fechar-oferta" title="Fechar">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
      </button>
      <p class="wt-oferta-tag">Oferta especial</p>
      <img id="wt-oferta-img" src="" alt="">
      <strong id="wt-oferta-nome"></strong>
      <p id="wt-oferta-desc"></p>
      <span id="wt-oferta-valor"></span>
      <a id="wt-oferta-cta" href="#" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M19 19H5V5h7V3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7h-2v7zM14 3v2h3.59L7.76 14.83l1.41 1.41L19 5.41V9h2V3h-7z"/></svg>
        Ver oferta
      </a>
    </div>
  </div>

  <!-- Banner mobile -->
  <div id="wt-oferta-banner">
    <div class="wt-banner-inner">
      <span id="wt-banner-nome"></span>
      <a id="wt-banner-cta" href="#" target="_blank" rel="noopener">Ver oferta</a>
      <button id="wt-banner-fechar">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
      </button>
    </div>
  </div>

  <div id="wt-fake-seek-wrap">
    <div id="wt-fake-prog-bar"></div>
  </div>

  <!-- Controles -->
  <div id="wt-controls">
    <?php if (!$video->hide_seekbar) : ?>
    <div id="wt-seek-wrap">
      <div id="wt-buf-bar"></div>
      <div id="wt-prog-bar"></div>
      <div id="wt-thumb"></div>
    </div>
    <?php endif; ?>

    <div id="wt-controls-row">
      <!-- Play/Pause -->
      <button class="wt-btn" id="wt-play" title="Play/Pause">
        <svg id="icon-pause" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
        <svg id="icon-play"  viewBox="0 0 24 24" style="display:none"><path d="M8 5v14l11-7z"/></svg>
      </button>

      <!-- Voltar 10s -->
      <button class="wt-btn wt-btn-sm" id="wt-skip-back" title="Voltar 10s">
        <svg viewBox="0 0 24 24"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8zm-1.1 11H10v-3.26L9 13.14V12.1l1.8-.65h.1V16zm4.28-1.2c0 .41-.04.72-.12.93-.08.21-.21.38-.39.5-.18.12-.41.18-.7.18-.28 0-.51-.06-.69-.18a1.1 1.1 0 01-.4-.5c-.09-.22-.13-.53-.13-.93v-.6c0-.41.04-.72.12-.93.08-.21.21-.37.39-.49.18-.12.41-.18.7-.18.28 0 .51.06.69.18.18.12.31.28.4.49.09.21.13.52.13.93v.6zm-.74-.69c0-.27-.02-.46-.06-.57a.27.27 0 00-.11-.15.38.38 0 00-.19-.04.37.37 0 00-.19.05.25.25 0 00-.11.15c-.04.11-.06.3-.06.57v.8c0 .27.02.46.06.57.04.11.09.19.16.19.06 0 .12-.06.16-.19.04-.11.06-.3.06-.57v-.81z"/></svg>
      </button>

      <span id="wt-time">0:00 / 0:00</span>

      <div id="wt-right">
        <?php if ($video->allow_speed) : ?>
        <select id="wt-speed" title="Velocidade">
          <option value="0.5">0.5×</option>
          <option value="0.75">0.75×</option>
          <option value="1" selected>1×</option>
          <option value="1.25">1.25×</option>
          <option value="1.5">1.5×</option>
          <option value="2">2×</option>
        </select>
        <?php endif; ?>

        <!-- Mute -->
        <button class="wt-btn wt-btn-sm" id="wt-mute" title="Mudo">
          <svg id="icon-vol-on" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
          <svg id="icon-vol-off" viewBox="0 0 24 24" style="display:none"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
        </button>

        <input id="wt-vol" type="range" min="0" max="1" step="0.05" value="1" title="Volume">

        <!-- Fullscreen -->
        <button class="wt-btn wt-btn-sm" id="wt-fs" title="Tela cheia">
          <svg id="icon-fs-on"  viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
          <svg id="icon-fs-off" viewBox="0 0 24 24" style="display:none"><path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/></svg>
        </button>
      </div>
    </div>
  </div>

  <div id="wt-watched-wrap"><div id="wt-watched-fill"></div></div>
</div>

<script>
(function () {
  const ANTI_SKIP      = <?= $video->anti_skip    ? 'true' : 'false' ?>;
  const HIDE_SEEKBAR   = <?= $video->hide_seekbar ? 'true' : 'false' ?>;
  const FAKE_SEEKBAR   = <?= ($video->fake_seekbar ?? 0) ? 'true' : 'false' ?>;
  const AUTOPLAY       = <?= $video->autoplay     ? 'true' : 'false' ?>;
  const FORCE_PLAY     = <?= $video->force_play   ? 'true' : 'false' ?>;
  const TOKEN          = <?= json_encode($video->embed_token) ?>;
  const STREAM_URL     = <?= json_encode($streamUrl) ?>;
  const HLS_URL        = <?= json_encode($hlsUrl) ?>;
  const PROGRESSO_GET  = <?= json_encode($progressoUrl) ?>;
  const PROGRESSO_POST = <?= json_encode($salvarUrl) ?>;
  const EVENTO_URL     = <?= json_encode($eventoUrl) ?>;
  const HEARTBEAT_URL  = <?= json_encode(url("player/heartbeat")) ?>;
  const OFERTAS        = <?= json_encode(array_map(function($a) {
    return [
      "id"          => (int)$a->id,
      "nome"        => $a->nome,
      "descricao"   => $a->descricao,
      "imagem"      => $a->imagem,
      "valor"       => $a->valor,
      "url_destino" => $a->url_destino,
      "exibir_em"   => (int)$a->exibir_em,
      "modo"        => $a->modo ?: "pausa",
    ];
  }, $anuncios), JSON_UNESCAPED_UNICODE) ?>;

  const video       = document.getElementById('wt-video');
  const wrap        = document.getElementById('wt-wrap');
  const poster      = document.getElementById('wt-poster');
  const loader      = document.getElementById('wt-loader');
  const controls    = document.getElementById('wt-controls');
  const playBtn     = document.getElementById('wt-play');
  const iconPause   = document.getElementById('icon-pause');
  const iconPlay    = document.getElementById('icon-play');
  const seekWrap    = document.getElementById('wt-seek-wrap');
  const progBar     = document.getElementById('wt-prog-bar');
  const bufBar      = document.getElementById('wt-buf-bar');
  const seekThumb   = document.getElementById('wt-thumb');
  const timeTxt     = document.getElementById('wt-time');
  const volSlider   = document.getElementById('wt-vol');
  const muteBtn     = document.getElementById('wt-mute');
  const unmutePill  = document.getElementById('wt-unmute-pill');
  const iconVolOn   = document.getElementById('icon-vol-on');
  const iconVolOff  = document.getElementById('icon-vol-off');
  const fsBtn       = document.getElementById('wt-fs');
  const iconFsOn    = document.getElementById('icon-fs-on');
  const iconFsOff   = document.getElementById('icon-fs-off');
  const speedSel    = document.getElementById('wt-speed');
  const watchedFill = document.getElementById('wt-watched-fill');
  const fakeWrap    = document.getElementById('wt-fake-seek-wrap');
  const fakeProg    = document.getElementById('wt-fake-prog-bar');
  if (FAKE_SEEKBAR && fakeWrap) fakeWrap.style.display = 'block';

  // ── HLS init ─────────────────────────────────────────────────────────────
  let hlsInstance = null;

  (function initVideo() {
    if (HLS_URL && typeof Hls !== 'undefined' && Hls.isSupported()) {
      hlsInstance = new Hls({
        autoStartLoad:  false,
        enableWorker:   true,
        backBufferLength: 30,
        maxBufferLength:  40,
      });
      hlsInstance.loadSource(HLS_URL);
      hlsInstance.attachMedia(video);
    } else if (HLS_URL && video.canPlayType('application/vnd.apple.mpegurl')) {
      // Safari suporta HLS nativamente
      video.src = HLS_URL;
    }
    // Sem HLS: src já definido no HTML (MP4 byte-range)
  })();

  let maxWatched   = 0;
  let savedTime    = 0;
  let metaReady    = false;
  let watchStart   = null;
  let totalWatched = 0;
  let hideTimer    = null;
  let isSeeking    = false;
  let didPlay      = false;
  let didComplete  = false;
  let started      = false;
  let autoplaying  = false;

  // ── Viewer key ────────────────────────────────────────────────────────────
  // Gerado uma vez por carregamento do player; passado em todos os eventos.
  // Não depende de cookie nem de localStorage — funciona em iframe cross-origin.
  var VIEWER_KEY = (function() {
    // Tenta reusar do sessionStorage (mesma aba, mesmo domínio do iframe)
    try {
      var stored = sessionStorage.getItem('wt_vk_' + TOKEN);
      if (stored) return stored;
    } catch(e) {}
    // Gera novo UUID simples
    var key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'.replace(/x/g, function() {
      return (Math.random() * 16 | 0).toString(16);
    });
    try { sessionStorage.setItem('wt_vk_' + TOKEN, key); } catch(e) {}
    return key;
  })();

  // ── Tracking ──────────────────────────────────────────────────────────────
  function track(tipo, adId) {
    var body = new URLSearchParams({ token: TOKEN, tipo: tipo, viewer_key: VIEWER_KEY });
    if (adId) body.append('ad_id', adId);
    fetch(EVENTO_URL, { method: 'POST', body: body });
  }

  // trackOnce: dedup client-side via sessionStorage (funciona em iframe)
  // O backend tem dedup próprio por IP como segunda camada.
  var _sentEvents = {};
  function trackOnce(tipo) {
    if (_sentEvents[tipo]) return false;
    _sentEvents[tipo] = true;
    track(tipo);
    return true;
  }

  // View disparada uma vez por carregamento do player
  (function() { trackOnce('view'); })();

  // ── Pré-carregamento inteligente ──────────────────────────────────────────
  function schedulePreload() {
    const fn = () => {
      if (!started) {
        if (hlsInstance) hlsInstance.startLoad();
        else video.preload = 'auto';
      }
    };
    if ('requestIdleCallback' in window) requestIdleCallback(fn, { timeout: 3000 });
    else setTimeout(fn, 500);
  }
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) { schedulePreload(); io.disconnect(); }
    }, { threshold: 0.25 });
    io.observe(video);
  } else {
    schedulePreload();
  }

  // ── Progresso salvo ───────────────────────────────────────────────────────
  fetch(PROGRESSO_GET)
    .then(r => r.json())
    .then(d => { savedTime = d.current_time || 0; updateWatched(d.percent || 0); applySaved(); })
    .catch(() => {});

  function applySaved() {
    if (metaReady && savedTime > 2) { video.currentTime = savedTime; maxWatched = savedTime; }
  }

  // ── Autoplay mudo ─────────────────────────────────────────────────────────
  if (AUTOPLAY) {
    autoplaying   = true;
    started       = true;
    video.muted   = true;
    if (hlsInstance) hlsInstance.startLoad();
    else video.preload = 'auto';
    poster.classList.add('hidden');
    video.classList.add('ready');
    unmutePill.style.display = 'flex';
    video.play().catch(() => {});
  }

  function unmute() {
    autoplaying              = false;
    video.muted              = false;
    video.volume             = 1;
    if (volSlider) volSlider.value = '1';
    syncVolIcon();
    unmutePill.style.display = 'none';
    video.currentTime        = 0;
    if (FORCE_PLAY) lockControls();
  }

  function lockControls() {
    controls.style.display      = 'none';
    watchedFill.parentElement.style.display = 'none';
    wrap.style.cursor           = 'default';
  }

  unmutePill.addEventListener('click', unmute);

  // ── Clique no poster ──────────────────────────────────────────────────────
  poster.addEventListener('click', iniciar);
  function iniciar() {
    if (started) return;
    started = true;
    if (hlsInstance) hlsInstance.startLoad();
    else video.preload = 'auto';
    poster.classList.add('hidden');
    video.classList.add('ready');
    showCtrls();
    video.play().catch(() => {});
  }

  // ── Eventos do vídeo ──────────────────────────────────────────────────────
  video.addEventListener('loadedmetadata', () => { metaReady = true; applySaved(); updateTime(); });
  video.addEventListener('waiting', () => { if (started) loader.classList.add('show'); });
  video.addEventListener('canplay',  () => { loader.classList.remove('show'); });

  video.addEventListener('play', () => {
    iconPause.style.display = ''; iconPlay.style.display = 'none';
    watchStart = Date.now();
    // Play: 1 por browser a cada 1h (localStorage + flag in-session)
    if (!didPlay) { didPlay = true; trackOnce('play', 3600000); }
  });

  video.addEventListener('pause', () => {
    iconPause.style.display = 'none'; iconPlay.style.display = '';
    acc(); salvarProgresso();
  });

  video.addEventListener('ended', () => {
    iconPause.style.display = 'none'; iconPlay.style.display = '';
    acc(); salvarProgresso();
  });

  // ── Timeupdate ────────────────────────────────────────────────────────────
  video.addEventListener('timeupdate', () => {
    const cur = video.currentTime;
    const dur = video.duration || 1;
    const pct = cur / dur;

    if (FAKE_SEEKBAR && fakeProg) {
      // Barra fake: anda 3x mais rápido que o vídeo real, cap em 100%
      // Isso cria a ilusão de que o vídeo é curto (barra "completa" com 33% do vídeo rodado)
      fakeProg.style.width = Math.min(100, pct * 3 * 100) + '%';
    }
    if (!HIDE_SEEKBAR && progBar) {
      progBar.style.width  = (pct * 100) + '%';
      seekThumb.style.left = (pct * 100) + '%';
      if (video.buffered.length > 0)
        bufBar.style.width = (video.buffered.end(video.buffered.length - 1) / dur * 100) + '%';
    }
    updateTime();

    if (!didComplete && dur > 0 && pct >= 0.9) { didComplete = true; track('complete'); }

    if (ANTI_SKIP && !isSeeking && cur > maxWatched + 30) {
      video.currentTime = maxWatched;
    } else if (cur > maxWatched) {
      maxWatched = cur;
    }

    if (!autoplaying) {
      OFERTAS.forEach((o, idx) => {
        if (!shownOfertas.has(idx) && cur >= o.exibir_em && cur < o.exibir_em + 2) {
          shownOfertas.add(idx); mostrarOferta(o);
        }
      });
    }
  });

  function updateTime() {
    timeTxt.textContent = fmt(video.currentTime) + ' / ' + fmt(video.duration || 0);
  }
  function fmt(s) {
    s = Math.floor(s || 0);
    return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
  }

  // Auto-save a cada 10s
  setInterval(() => { if (!video.paused && !video.ended) salvarProgresso(); }, 10000);

  // ── Play/Pause ────────────────────────────────────────────────────────────
  playBtn.addEventListener('click', () => { if (!FORCE_PLAY) toggle(); });
  video.addEventListener('click', () => {
    if (!started) return;
    if (autoplaying) { unmute(); return; }
    if (FORCE_PLAY) return;
    toggle();
  });
  function toggle() { video.paused ? video.play() : video.pause(); }

  // Skip back 10s
  document.getElementById('wt-skip-back').addEventListener('click', () => {
    if (FORCE_PLAY) return;
    video.currentTime = Math.max(0, video.currentTime - 10);
  });

  // ── Seek ──────────────────────────────────────────────────────────────────
  if (!HIDE_SEEKBAR && seekWrap) {
    seekWrap.addEventListener('mousedown',  startSeek);
    seekWrap.addEventListener('touchstart', startSeek, { passive: true });

    function startSeek(e) {
      isSeeking = true; doSeekUI(e);
      document.addEventListener('mousemove', doSeekUI);
      document.addEventListener('touchmove', doSeekUI, { passive: true });
      document.addEventListener('mouseup',   endSeek);
      document.addEventListener('touchend',  endSeek);
    }
    function doSeekUI(e) {
      const rect = seekWrap.getBoundingClientRect();
      const cx   = e.touches ? e.touches[0].clientX : e.clientX;
      const r    = Math.max(0, Math.min(1, (cx - rect.left) / rect.width));
      const dur  = video.duration || 0;
      const t    = ANTI_SKIP ? Math.min(r * dur, maxWatched + 30) : r * dur;
      if (progBar) progBar.style.width = (t / (dur || 1) * 100) + '%';
      if (seekThumb) seekThumb.style.left = (t / (dur || 1) * 100) + '%';
      timeTxt.textContent = fmt(t) + ' / ' + fmt(dur);
    }
    function endSeek(e) {
      document.removeEventListener('mousemove', doSeekUI);
      document.removeEventListener('touchmove', doSeekUI);
      document.removeEventListener('mouseup',   endSeek);
      document.removeEventListener('touchend',  endSeek);
      const rect = seekWrap.getBoundingClientRect();
      const cx   = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
      const r    = Math.max(0, Math.min(1, (cx - rect.left) / rect.width));
      const dur  = video.duration || 0;
      video.currentTime = ANTI_SKIP ? Math.min(r * dur, maxWatched + 30) : r * dur;
      isSeeking = false;
    }
  }

  // ── Volume ────────────────────────────────────────────────────────────────
  if (volSlider) {
    volSlider.addEventListener('input', () => {
      video.volume = volSlider.value;
      video.muted  = video.volume === 0;
      syncVolIcon();
    });
  }
  muteBtn.addEventListener('click', () => {
    if (autoplaying) { unmute(); return; }
    if (FORCE_PLAY) return;
    video.muted = !video.muted;
    syncVolIcon();
  });
  function syncVolIcon() {
    const muted = video.muted || video.volume === 0;
    iconVolOn.style.display  = muted ? 'none' : '';
    iconVolOff.style.display = muted ? '' : 'none';
  }

  // ── Velocidade ────────────────────────────────────────────────────────────
  if (speedSel) {
    speedSel.addEventListener('change', () => { video.playbackRate = parseFloat(speedSel.value); });
  }

  // ── Fullscreen ────────────────────────────────────────────────────────────
  fsBtn.addEventListener('click', () => {
    if (!document.fullscreenElement) wrap.requestFullscreen().catch(() => {});
    else document.exitFullscreen().catch(() => {});
  });
  document.addEventListener('fullscreenchange', () => {
    const fs = !!document.fullscreenElement;
    iconFsOn.style.display  = fs ? 'none' : '';
    iconFsOff.style.display = fs ? '' : 'none';
  });

  // ── Controles visíveis ────────────────────────────────────────────────────
  wrap.addEventListener('mousemove',  showCtrls);
  wrap.addEventListener('touchstart', showCtrls, { passive: true });
  function showCtrls() {
    if (!started || FORCE_PLAY) return;
    controls.style.opacity = '1';
    clearTimeout(hideTimer);
    hideTimer = setTimeout(() => { if (!video.paused) controls.style.opacity = '0'; }, 2800);
  }

  // ── Tempo assistido ───────────────────────────────────────────────────────
  function acc() {
    if (watchStart !== null) { totalWatched += Math.round((Date.now() - watchStart) / 1000); watchStart = null; }
  }

  function salvarProgresso() {
    acc();
    const body = new URLSearchParams({
      token:         TOKEN,
      current_time:  video.currentTime,
      duration:      video.duration || 0,
      watch_seconds: totalWatched,
    });
    totalWatched = 0;
    fetch(PROGRESSO_POST, { method: 'POST', body })
      .then(r => r.json())
      .then(d => updateWatched(d.percent || 0))
      .catch(() => {});
  }
  function updateWatched(pct) { watchedFill.style.width = Math.min(100, pct) + '%'; }

  window.addEventListener('visibilitychange', () => { if (document.visibilityState === 'hidden') salvarProgresso(); });
  window.addEventListener('beforeunload', () => {
    acc();
    navigator.sendBeacon(PROGRESSO_POST, new URLSearchParams({
      token: TOKEN, current_time: video.currentTime,
      duration: video.duration || 0, watch_seconds: totalWatched,
    }));
  });

  // ── Ofertas ───────────────────────────────────────────────────────────────
  const overlay      = document.getElementById('wt-oferta-overlay');
  const banner       = document.getElementById('wt-oferta-banner');
  const shownOfertas = new Set();

  document.getElementById('wt-fechar-oferta').addEventListener('click', () => {
    overlay.style.display = 'none'; video.play();
  });
  document.getElementById('wt-banner-fechar').addEventListener('click', () => {
    banner.style.display = 'none';
  });

  function mostrarOferta(o) {
    track('ad_impression', o.id);
    if ((o.modo || 'pausa') === 'banner') {
      // Banner simples — vídeo continua rodando
      document.getElementById('wt-banner-nome').textContent = o.nome;
      const cta = document.getElementById('wt-banner-cta');
      cta.href = o.url_destino;
      cta.addEventListener('click', () => track('ad_click', o.id), { once: true });
      banner.style.display = 'block';
    } else {
      // Pausa + overlay centralizado
      const img   = document.getElementById('wt-oferta-img');
      const nome  = document.getElementById('wt-oferta-nome');
      const desc  = document.getElementById('wt-oferta-desc');
      const valor = document.getElementById('wt-oferta-valor');
      const cta   = document.getElementById('wt-oferta-cta');
      nome.textContent = o.nome;
      if (o.imagem) { img.src = <?= json_encode(url("uploads/anuncios/")) ?> + o.imagem; img.style.display = 'block'; }
      else img.style.display = 'none';
      if (o.descricao) { desc.textContent = o.descricao; desc.style.display = 'block'; }
      else desc.style.display = 'none';
      if (o.valor) { valor.textContent = 'R$ ' + parseFloat(o.valor).toFixed(2).replace('.', ','); valor.style.display = 'inline'; }
      else valor.style.display = 'none';
      cta.href = o.url_destino;
      cta.addEventListener('click', () => track('ad_click', o.id), { once: true });
      overlay.style.display = 'flex';
      video.pause();
    }
  }
  // ── Heartbeat de retenção (a cada 5s durante reprodução) ──────────────────
  setInterval(() => {
    if (!video.paused && !video.ended && started && !autoplaying) {
      navigator.sendBeacon(HEARTBEAT_URL, new URLSearchParams({
        token:   TOKEN,
        segundo: Math.floor(video.currentTime),
        duracao: Math.floor(video.duration || 0),
      }));
    }
  }, 5000);
})();
</script>
</body>
</html>

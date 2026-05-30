<?php $v->layout("_theme") ?>

<?php $v->start("styles") ?>
<style>
/* ── KPI cards ───────────────────────────────────────────── */
.kpi-row {
  display: grid;
  gap: .9rem;
  margin-bottom: 1.2rem;
}
.kpi-row-7 { grid-template-columns: repeat(7, 1fr); }
.kpi-row-2 { grid-template-columns: repeat(2, 1fr); }
@media (max-width: 1100px) {
  .kpi-row-7 { grid-template-columns: repeat(4, 1fr); }
}
@media (max-width: 750px) {
  .kpi-row-7 { grid-template-columns: repeat(2, 1fr); }
  .kpi-row-2 { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
  .kpi-row-7, .kpi-row-2 { grid-template-columns: 1fr; }
}

.kpi-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.1rem 1.3rem;
  display: flex; flex-direction: column; gap: .3rem;
  position: relative; overflow: hidden;
  box-shadow: var(--shadow);
  transition: box-shadow .2s, transform .15s;
}
.kpi-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
.kpi-icon {
  font-size: .8rem; color: var(--accent);
  background: var(--accent-soft);
  border: 1px solid var(--accent-border);
  width: 28px; height: 28px;
  border-radius: 7px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: .15rem;
}
.kpi-val {
  font-family: 'Syne', sans-serif;
  font-size: 2rem; font-weight: 700; line-height: 1;
  color: var(--text);
}
.kpi-lbl {
  font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .1em;
  color: var(--muted);
}
.kpi-sub { font-size: .75rem; color: var(--muted); margin-top: .1rem; }

/* ── Pitch inline editor ─────────────────────────────────── */
.pitch-link {
  font-size: .73rem; color: var(--accent); cursor: pointer;
  text-decoration: underline dotted; margin-top: .15rem; display: inline-block;
}
#pitch-form { display: none; margin-top: .4rem; gap: .35rem; align-items: center; flex-wrap: wrap; }
#pitch-form.show { display: flex; }
#pitch-input {
  font-size: .8rem; padding: .25rem .5rem;
  border: 1px solid var(--border); border-radius: 6px;
  width: 72px; color: var(--text); background: #fff;
  font-family: 'JetBrains Mono', monospace;
}
#pitch-save {
  font-size: .75rem; padding: .25rem .65rem;
  background: var(--accent); color: #fff; border: none;
  border-radius: 6px; cursor: pointer; font-weight: 600;
}
#pitch-save:hover { filter: brightness(1.1); }
#pitch-cancel {
  font-size: .75rem; color: var(--muted); cursor: pointer;
  background: none; border: none; text-decoration: underline;
}

/* ── Tabs ────────────────────────────────────────────────── */
.wt-tabs {
  display: flex; gap: 0;
  border-bottom: 1px solid var(--border);
  margin-bottom: 1.4rem;
  background: #fff;
  border-radius: var(--radius) var(--radius) 0 0;
  padding: 0 .5rem;
  box-shadow: var(--shadow);
}
.wt-tab {
  padding: .65rem 1.1rem;
  font-size: .82rem; font-weight: 600;
  color: var(--muted);
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
  transition: color .15s, border-color .15s;
  user-select: none;
  display: flex; align-items: center; gap: .45rem;
}
.wt-tab:hover { color: var(--text); }
.wt-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ── Chart shared ────────────────────────────────────────── */
.chart-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 1.4rem;
  margin-bottom: 1.2rem;
}
.chart-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.2rem;
  flex-wrap: wrap; gap: .5rem;
}
.chart-title { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
.chart-legend {
  display: flex; align-items: center; gap: .4rem;
  font-size: .75rem; color: var(--muted);
}
.chart-legend-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); }
.chart-legend-dot.green { background: #22c55e; }

.chart-outer {
  display: flex;
  gap: 0;
}
.chart-y-axis {
  display: flex; flex-direction: column;
  justify-content: space-between;
  padding-bottom: 22px;
  width: 38px; flex-shrink: 0;
  align-items: flex-end;
}
.chart-y-tick {
  font-size: .62rem; color: var(--muted);
  line-height: 1;
}
.chart-svg-wrap {
  flex: 1; position: relative;
}
.chart-svg-wrap svg {
  width: 100%; height: 180px; display: block; overflow: visible;
}
.chart-x-axis {
  display: flex; justify-content: space-between;
  margin-top: .4rem; padding: 0 2px;
}
.chart-x-tick {
  font-size: .62rem; color: var(--muted);
}

/* Hover tooltip */
.chart-tooltip {
  position: absolute;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: .4rem .65rem;
  font-size: .75rem;
  color: var(--text);
  pointer-events: none;
  opacity: 0;
  transform: translateY(-4px);
  transition: opacity .15s, transform .15s;
  white-space: nowrap;
  z-index: 10;
  box-shadow: var(--shadow-md);
}
.chart-tooltip.show { opacity: 1; transform: translateY(0); }
.chart-tooltip strong { color: var(--accent); }
.chart-tooltip.green strong { color: #22c55e; }

/* ── Conv pills ──────────────────────────────────────────── */
.conv-pill {
  display: inline-flex; align-items: center; gap: .3rem;
  padding: .18rem .6rem; border-radius: 99px;
  font-size: .71rem; font-weight: 700;
}
.conv-pill.hot  { background: rgba(52,211,153,.1);  border: 1px solid rgba(52,211,153,.25);  color: #34d399; }
.conv-pill.warm { background: rgba(251,191,36,.1);  border: 1px solid rgba(251,191,36,.25);  color: #fbbf24; }
.conv-pill.cold { background: rgba(148,163,184,.06); border: 1px solid rgba(148,163,184,.15); color: var(--muted); }

/* ── Empty state ─────────────────────────────────────────── */
.empty-ads {
  text-align: center; padding: 3rem 2rem; color: var(--muted);
}
.empty-ads i { font-size: 2rem; opacity: .15; display: block; margin-bottom: .8rem; }

/* ── Live dot pulse ──────────────────────────────────── */
@keyframes wt-live-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50%       { transform: scale(1.7); opacity: 0; }
}
.wt-live-dot {
  display: inline-block;
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #ef4444;
  animation: wt-live-pulse 1.4s ease-in-out infinite;
  flex-shrink: 0;
}

/* ── Ao Vivo panel ───────────────────────────────────── */
.aovivo-hero {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: 1.4rem;
}
.aovivo-count {
  font-family: 'Syne', sans-serif;
  font-size: 3.2rem; font-weight: 700; line-height: 1;
  color: var(--text);
}
.aovivo-label {
  font-size: .78rem; font-weight: 600; color: var(--muted);
  text-transform: uppercase; letter-spacing: .08em;
}
.aovivo-updated {
  font-size: .7rem; color: var(--muted); margin-top: .25rem;
}
.aovivo-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
@media (max-width: 640px) { .aovivo-grid { grid-template-columns: 1fr; } }
.aovivo-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 1.1rem 1.3rem;
}
.aovivo-card-title {
  font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .1em;
  color: var(--muted); margin-bottom: .9rem;
}
.aovivo-bar-row {
  display: flex; align-items: center; gap: .6rem;
  margin-bottom: .55rem;
}
.aovivo-bar-label { font-size: .78rem; color: var(--text); min-width: 60px; }
.aovivo-bar-track {
  flex: 1; height: 6px; background: var(--bg);
  border-radius: 99px; overflow: hidden;
}
.aovivo-bar-fill {
  height: 100%; border-radius: 99px; background: var(--accent);
  transition: width .4s ease;
}
.aovivo-bar-val { font-size: .75rem; color: var(--muted); min-width: 24px; text-align: right; }
.aovivo-location-row {
  display: flex; align-items: center; gap: .5rem;
  margin-bottom: .5rem;
}
.aovivo-loc-label { font-size: .78rem; color: var(--text); flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.aovivo-loc-val { font-size: .75rem; color: var(--muted); min-width: 24px; text-align: right; }
.aovivo-empty {
  text-align: center; padding: 2.5rem 1rem; color: var(--muted);
}
.aovivo-empty i { font-size: 2rem; opacity: .15; display: block; margin-bottom: .6rem; }

/* ── KPI selector popover ────────────────────────────── */
.kpi-selector-wrap {
  position: relative; display: inline-block;
}
.kpi-popover {
  position: absolute; top: calc(100% + 8px); right: 0;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  padding: .9rem 1rem;
  min-width: 220px;
  z-index: 100;
  display: none;
}
.kpi-popover.open { display: block; }
.kpi-popover-title {
  font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .1em;
  color: var(--muted); margin-bottom: .75rem;
}
.kpi-popover-item {
  display: flex; align-items: center; gap: .55rem;
  padding: .28rem 0; cursor: pointer;
}
.kpi-popover-item input[type=checkbox] {
  accent-color: var(--accent); width: 14px; height: 14px; cursor: pointer;
}
.kpi-popover-item label {
  font-size: .82rem; color: var(--text); cursor: pointer; user-select: none;
}
.kpi-popover-close {
  margin-top: .8rem; width: 100%;
  font-size: .78rem; padding: .35rem 0;
  background: var(--bg); border: 1px solid var(--border);
  border-radius: 7px; cursor: pointer; color: var(--muted);
  font-weight: 600;
}
.kpi-popover-close:hover { color: var(--text); }
</style>
<?php $v->end() ?>

<?php
$views          = (int)($totais->views         ?? 0);
$plays          = (int)($totais->plays         ?? 0);
$completes      = (int)($totais->completes     ?? 0);
$sessoes        = (int)($totais->sessoes_unicas ?? 0);
$avgPct         = (float)($progStats->avg_pct  ?? 0);
$avgMin         = (float)($progStats->avg_min  ?? 0);
$viewsUnicas    = (int)($totaisUnicos->views_unicas ?? 0);
$playsUnicos    = (int)($totaisUnicos->plays_unicos ?? 0);
$cliquesBotao   = (int)($totaisUnicos->cliques_botao ?? 0);
$playRate       = $viewsUnicas > 0 ? round($playsUnicos / $viewsUnicas * 100, 1) : 0;
$compRate       = $plays > 0       ? round($completes / $plays * 100, 1) : 0;
$engajamento    = $avgPct;
?>

<!-- ── Header ── -->
<div style="display:flex;gap:.6rem;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;">
  <a href="<?= url("videos/editar/{$video->id}") ?>" class="btn-wt btn-wt-ghost btn-wt-sm">
    <i class="fas fa-arrow-left"></i> Voltar
  </a>
  <div style="flex:1;min-width:0;">
    <h2 style="font-size:.95rem;font-weight:600;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
      <?= htmlspecialchars($video->titulo, ENT_QUOTES) ?>
    </h2>
    <a href="<?= htmlspecialchars($embedUrl, ENT_QUOTES) ?>" target="_blank" rel="noopener"
       style="font-size:.7rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">
      <?= htmlspecialchars($embedUrl, ENT_QUOTES) ?>
    </a>
  </div>
  <span style="font-size:.72rem;color:var(--muted);background:var(--bg);border:1px solid var(--border);padding:.28rem .75rem;border-radius:99px;">
    <i class="fas fa-calendar-days" style="margin-right:.35rem;color:var(--accent);font-size:.7rem;"></i>Últimos 30 dias
  </span>
  <button onclick="zerarMetricas()" class="btn-wt btn-wt-ghost btn-wt-sm" style="border-color:rgba(239,68,68,.4);color:#f87171;" title="Apaga todos os eventos e dados de retenção deste vídeo">
    <i class="fas fa-trash-alt"></i> Zerar métricas
  </button>
</div>

<!-- ── KPI section header with selector ── -->
<div style="display:flex;align-items:center;justify-content:flex-end;margin-bottom:.6rem;gap:.5rem;">
  <div class="kpi-selector-wrap" id="kpi-selector-wrap">
    <button class="btn-wt btn-wt-ghost btn-wt-sm" id="kpi-selector-btn" type="button"
            style="display:flex;align-items:center;gap:.4rem;">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 1 1 4.93 19.07"/><path d="M1 12s3-7 11-7 11 7 11 7-3 7-11 7"/></svg>
      Visualização de Métricas
    </button>
    <div class="kpi-popover" id="kpi-popover">
      <div class="kpi-popover-title">Exibir KPIs</div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-visualizacoes" data-kpi-target="visualizacoes" checked><label for="kpi-cb-visualizacoes">Visualizações</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-views-unicas" data-kpi-target="views-unicas" checked><label for="kpi-cb-views-unicas">Visualizações Únicas</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-plays" data-kpi-target="plays" checked><label for="kpi-cb-plays">Plays</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-plays-unicos" data-kpi-target="plays-unicos" checked><label for="kpi-cb-plays-unicos">Plays Únicos</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-play-rate" data-kpi-target="play-rate" checked><label for="kpi-cb-play-rate">Play Rate</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-retencao-pitch" data-kpi-target="retencao-pitch" checked><label for="kpi-cb-retencao-pitch">Retenção ao Pitch</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-engajamento" data-kpi-target="engajamento" checked><label for="kpi-cb-engajamento">Engajamento</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-cliques-botao" data-kpi-target="cliques-botao" checked><label for="kpi-cb-cliques-botao">Cliques no Botão</label></div>
      <div class="kpi-popover-item"><input type="checkbox" id="kpi-cb-audiencia-pitch" data-kpi-target="audiencia-pitch" checked><label for="kpi-cb-audiencia-pitch">Audiência do Pitch</label></div>
      <button class="kpi-popover-close" id="kpi-popover-close">Fechar</button>
    </div>
  </div>
</div>

<!-- ── KPI Row 1 (7 cards) ── -->
<div class="kpi-row kpi-row-7">
  <div class="kpi-card" data-kpi="visualizacoes">
    <div class="kpi-icon"><i class="fas fa-eye"></i></div>
    <div class="kpi-val"><?= number_format($views) ?></div>
    <div class="kpi-lbl">Visualizações</div>
    <div class="kpi-sub"><?= $sessoes ?> sessões</div>
  </div>
  <div class="kpi-card" data-kpi="views-unicas">
    <div class="kpi-icon"><i class="fas fa-eye"></i></div>
    <div class="kpi-val"><?= number_format($viewsUnicas) ?></div>
    <div class="kpi-lbl">Views Únicas</div>
    <div class="kpi-sub">por visitante</div>
  </div>
  <div class="kpi-card" data-kpi="plays">
    <div class="kpi-icon"><i class="fas fa-play"></i></div>
    <div class="kpi-val"><?= number_format($plays) ?></div>
    <div class="kpi-lbl">Plays</div>
    <div class="kpi-sub">total</div>
  </div>
  <div class="kpi-card" data-kpi="plays-unicos">
    <div class="kpi-icon"><i class="fas fa-play-circle"></i></div>
    <div class="kpi-val"><?= number_format($playsUnicos) ?></div>
    <div class="kpi-lbl">Plays Únicos</div>
    <div class="kpi-sub">sessões distintas</div>
  </div>
  <div class="kpi-card" data-kpi="play-rate">
    <div class="kpi-icon"><i class="fas fa-percent"></i></div>
    <div class="kpi-val"><?= $playRate ?>%</div>
    <div class="kpi-lbl">Play Rate</div>
    <div class="kpi-sub">views → plays</div>
  </div>
  <div class="kpi-card" data-kpi="retencao-pitch">
    <div class="kpi-icon"><i class="fas fa-bullseye"></i></div>
    <?php if ($pitchTime > 0): ?>
      <div class="kpi-val"><?= $retencaoPitch ?>%</div>
      <div class="kpi-lbl">Ret. ao Pitch</div>
      <div class="kpi-sub"><?= $audienciaPitch ?> de <?= $playsUnicos ?> chegaram</div>
    <?php else: ?>
      <div class="kpi-val" style="font-size:1.2rem;color:var(--muted);">—</div>
      <div class="kpi-lbl">Ret. ao Pitch</div>
    <?php endif; ?>
    <span class="pitch-link" id="pitch-toggle">
      <?= $pitchTime > 0 ? '✏️ ' . gmdate($pitchTime >= 3600 ? 'G:i:s' : 'i:s', $pitchTime) : 'Definir tempo do Pitch' ?>
    </span>
    <div id="pitch-form">
      <input type="text" id="pitch-input" placeholder="mm:ss" maxlength="7"
             value="<?= $pitchTime > 0 ? gmdate($pitchTime >= 3600 ? 'G:i:s' : 'i:s', $pitchTime) : '' ?>">
      <button id="pitch-save">Salvar</button>
      <button id="pitch-cancel" type="button">Cancelar</button>
    </div>
  </div>
  <div class="kpi-card" data-kpi="engajamento">
    <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
    <div class="kpi-val"><?= $engajamento ?>%</div>
    <div class="kpi-lbl">Engajamento</div>
    <div class="kpi-sub"><?= $avgMin ?> min médio</div>
  </div>
</div>

<!-- ── KPI Row 2 ── -->
<div class="kpi-row kpi-row-2">
  <div class="kpi-card" data-kpi="cliques-botao">
    <div class="kpi-icon"><i class="fas fa-hand-pointer"></i></div>
    <div class="kpi-val"><?= number_format($cliquesBotao) ?></div>
    <div class="kpi-lbl">Cliques no Botão</div>
    <div class="kpi-sub">total de ad_click</div>
  </div>
  <div class="kpi-card" data-kpi="audiencia-pitch">
    <div class="kpi-icon"><i class="fas fa-users"></i></div>
    <?php if ($pitchTime > 0): ?>
      <div class="kpi-val"><?= number_format($audienciaPitch) ?></div>
      <div class="kpi-lbl">Audiência do Pitch</div>
      <div class="kpi-sub">chegaram em <?= gmdate($pitchTime >= 3600 ? 'G:i:s' : 'i:s', $pitchTime) ?></div>
    <?php else: ?>
      <div class="kpi-val" style="font-size:1.2rem;color:var(--muted);">—</div>
      <div class="kpi-lbl">Audiência do Pitch</div>
      <div class="kpi-sub">defina o Pitch acima</div>
    <?php endif; ?>
  </div>
</div>

<!-- ── Tabs ── -->
<div class="wt-tabs">
  <div class="wt-tab active" onclick="switchTab('retencao')">
    <i class="fas fa-chart-area"></i> Retenção Geral
  </div>
  <div class="wt-tab" onclick="switchTab('anuncios')">
    <i class="fas fa-bullhorn"></i> Anúncios
    <?php if ($adsStats) : ?>
      <span style="background:var(--accent-soft);border:1px solid var(--accent-border);color:var(--accent);font-size:.62rem;padding:.08rem .4rem;border-radius:99px;font-weight:700;"><?= count($adsStats) ?></span>
    <?php endif; ?>
  </div>
  <div class="wt-tab" onclick="switchTab('aovivo')" id="tab-aovivo-btn">
    <span class="wt-live-dot" style="margin-right:.1rem;"></span> Ao Vivo
  </div>
</div>

<!-- ── Tab: Retenção Geral ── -->
<div class="tab-panel active" id="tab-retencao">

  <!-- Views por dia -->
  <div class="chart-card">
    <div class="chart-header">
      <span class="chart-title"><i class="fas fa-chart-area" style="color:var(--accent);margin-right:.4rem;opacity:.7;"></i>Visualizações por dia</span>
      <span class="chart-legend"><span class="chart-legend-dot"></span> views</span>
    </div>

    <?php if ($viewsPorDia) : ?>
    <div id="chart-tooltip" class="chart-tooltip"></div>
    <div class="chart-outer">
      <div class="chart-y-axis" id="chart-y-axis"></div>
      <div class="chart-svg-wrap" id="chart-svg-wrap">
        <svg id="chart-svg" preserveAspectRatio="none">
          <defs>
            <linearGradient id="area-grad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%"   stop-color="#f97316" stop-opacity=".25"/>
              <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
            </linearGradient>
          </defs>
          <g id="chart-grid"></g>
          <path id="chart-area-fill" fill="url(#area-grad)"/>
          <path id="chart-line" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <g id="chart-dots"></g>
          <rect id="chart-hover-zone" fill="transparent" style="cursor:crosshair;"/>
        </svg>
      </div>
    </div>
    <div class="chart-x-axis" id="chart-x-axis"></div>
    <?php else : ?>
    <div style="height:180px;display:flex;align-items:center;justify-content:center;color:var(--muted);flex-direction:column;gap:.5rem;">
      <i class="fas fa-chart-area" style="font-size:2rem;opacity:.15;"></i>
      <span style="font-size:.84rem;">Ainda não há dados suficientes.</span>
    </div>
    <?php endif; ?>
  </div>

  <!-- Curva de retenção -->
  <div class="chart-card">
    <div class="chart-header">
      <span class="chart-title"><i class="fas fa-wave-square" style="color:#22c55e;margin-right:.4rem;opacity:.8;"></i>Curva de Retenção</span>
      <span class="chart-legend"><span class="chart-legend-dot green"></span> % assistindo</span>
    </div>

    <?php if ($retencao) : ?>
    <div id="ret-tooltip" class="chart-tooltip green"></div>
    <div class="chart-outer">
      <div class="chart-y-axis" id="ret-y-axis"></div>
      <div class="chart-svg-wrap" id="ret-svg-wrap">
        <svg id="ret-svg" preserveAspectRatio="none">
          <defs>
            <linearGradient id="ret-grad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%"   stop-color="#22c55e" stop-opacity=".28"/>
              <stop offset="100%" stop-color="#22c55e" stop-opacity="0"/>
            </linearGradient>
          </defs>
          <g id="ret-grid"></g>
          <path id="ret-area-fill" fill="url(#ret-grad)"/>
          <path id="ret-line" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <g id="ret-dots"></g>
          <rect id="ret-hover-zone" fill="transparent" style="cursor:crosshair;"/>
        </svg>
      </div>
    </div>
    <div class="chart-x-axis" id="ret-x-axis"></div>
    <?php else : ?>
    <div style="height:180px;display:flex;align-items:center;justify-content:center;color:var(--muted);flex-direction:column;gap:.5rem;">
      <i class="fas fa-wave-square" style="font-size:2rem;opacity:.15;"></i>
      <span style="font-size:.84rem;">Ainda não há dados de retenção.</span>
      <span style="font-size:.75rem;opacity:.7;">Os dados aparecem após o vídeo ser assistido.</span>
    </div>
    <?php endif; ?>
  </div>

</div>

<!-- ── Tab: Anúncios ── -->
<div class="tab-panel" id="tab-anuncios">
  <?php if ($adsStats) : ?>
  <div class="wt-card">
    <div class="wt-card-title">
      <i class="fas fa-bullhorn" style="margin-right:.45rem;color:var(--accent);opacity:.7;"></i>Performance dos CTAs
    </div>
    <p style="font-size:.76rem;color:var(--muted);margin-bottom:1rem;">
      Conversão = cliques ÷ visualizações totais do player
    </p>
    <div style="overflow-x:auto;">
      <table class="wt-table" style="min-width:600px;">
        <thead>
          <tr>
            <th>Anúncio</th>
            <th>Exibe em</th>
            <th>Impressões</th>
            <th>Cliques</th>
            <th>CTR</th>
            <th>Conversão</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($adsStats as $row) : ?>
          <tr>
            <td>
              <strong style="display:block;font-size:.87rem;"><?= htmlspecialchars($row["ad"]->nome, ENT_QUOTES) ?></strong>
              <a href="<?= htmlspecialchars($row["ad"]->url_destino, ENT_QUOTES) ?>" target="_blank" rel="noopener"
                 style="font-size:.7rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">ver link ↗</a>
            </td>
            <td style="color:var(--muted);"><?= (int)$row["ad"]->exibir_em ?>s</td>
            <td><?= number_format($row["impressoes"]) ?></td>
            <td><?= number_format($row["cliques"]) ?></td>
            <td>
              <?php $ctr = $row["ctr"]; ?>
              <span class="conv-pill <?= $ctr >= 5 ? 'hot' : ($ctr >= 2 ? 'warm' : 'cold') ?>">
                <?= $ctr ?>%
              </span>
            </td>
            <td>
              <?php $conv = $row["conversao"]; ?>
              <span class="conv-pill <?= $conv >= 2 ? 'hot' : ($conv >= 0.5 ? 'warm' : 'cold') ?>">
                <?= $conv ?>%
              </span>
              <?php if ($views > 0) : ?>
              <span style="font-size:.68rem;color:var(--muted);display:block;margin-top:.15rem;">
                <?= $row["cliques"] ?> / <?= $views ?> views
              </span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else : ?>
  <div class="wt-card empty-ads">
    <i class="fas fa-bullhorn"></i>
    <p style="margin-bottom:1.2rem;font-size:.88rem;">Nenhum CTA configurado para este vídeo.</p>
    <a href="<?= url("videos/editar/{$video->id}") ?>" class="btn-wt btn-wt-sm">
      <i class="fas fa-plus"></i> Adicionar CTA
    </a>
  </div>
  <?php endif; ?>
</div>

<!-- ── Tab: Ao Vivo ── -->
<div class="tab-panel" id="tab-aovivo">
  <div class="chart-card">
    <div class="chart-header" style="margin-bottom:.8rem;">
      <span class="chart-title" style="display:flex;align-items:center;gap:.5rem;">
        <span class="wt-live-dot"></span> Viewers Ativos Agora
      </span>
      <span id="aovivo-updated" class="aovivo-updated"></span>
    </div>

    <!-- Hero count -->
    <div class="aovivo-hero" id="aovivo-hero">
      <div>
        <div class="aovivo-count" id="aovivo-total">—</div>
        <div class="aovivo-label" id="aovivo-sublabel">assistindo agora</div>
      </div>
    </div>

    <!-- Grid de detalhes -->
    <div class="aovivo-grid" id="aovivo-grid" style="display:none;">
      <!-- Dispositivos -->
      <div class="aovivo-card">
        <div class="aovivo-card-title"><i class="fas fa-mobile-screen-button" style="margin-right:.4rem;color:var(--accent);opacity:.7;"></i>Dispositivos</div>
        <div id="aovivo-devices">
          <div class="aovivo-bar-row">
            <span class="aovivo-bar-label">Mobile</span>
            <div class="aovivo-bar-track"><div class="aovivo-bar-fill" id="bar-mobile" style="width:0%"></div></div>
            <span class="aovivo-bar-val" id="val-mobile">0</span>
          </div>
          <div class="aovivo-bar-row">
            <span class="aovivo-bar-label">Desktop</span>
            <div class="aovivo-bar-track"><div class="aovivo-bar-fill" id="bar-desktop" style="width:0%"></div></div>
            <span class="aovivo-bar-val" id="val-desktop">0</span>
          </div>
          <div class="aovivo-bar-row">
            <span class="aovivo-bar-label">Tablet</span>
            <div class="aovivo-bar-track"><div class="aovivo-bar-fill" id="bar-tablet" style="width:0%"></div></div>
            <span class="aovivo-bar-val" id="val-tablet">0</span>
          </div>
        </div>
      </div>

      <!-- Top Localizações -->
      <div class="aovivo-card">
        <div class="aovivo-card-title"><i class="fas fa-location-dot" style="margin-right:.4rem;color:var(--accent);opacity:.7;"></i>Top Localizações</div>
        <div id="aovivo-cidades">
          <div style="color:var(--muted);font-size:.78rem;">Aguardando dados de geo...</div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div class="aovivo-empty" id="aovivo-empty" style="display:none;">
      <i class="fas fa-satellite-dish"></i>
      <p style="font-size:.88rem;margin:0;">Nenhum viewer ativo no momento</p>
      <span style="font-size:.76rem;opacity:.7;">Atualiza automaticamente a cada 30s</span>
    </div>
  </div>
</div>

<?php $v->start("scripts") ?>
<script>
// ── Tab switch ────────────────────────────────────────────
function switchTab(name) {
  document.querySelectorAll('.wt-tab').forEach(function(t) { t.classList.remove('active'); });
  document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.remove('active'); });
  var tab = document.querySelector('[onclick="switchTab(\'' + name + '\')"]');
  var panel = document.getElementById('tab-' + name);
  if (tab) tab.classList.add('active');
  if (panel) panel.classList.add('active');
}

// ── Time formatter ────────────────────────────────────────
function fmtSeg(s) {
  s = Math.floor(s || 0);
  var h = Math.floor(s / 3600);
  var m = Math.floor((s % 3600) / 60);
  var ss = s % 60;
  if (h > 0) return h + ':' + String(m).padStart(2,'0') + ':' + String(ss).padStart(2,'0');
  return m + ':' + String(ss).padStart(2,'0');
}

// ── Views por dia chart ───────────────────────────────────
(function() {
  var raw = <?= json_encode(array_map(fn($r) => ['dia' => $r->dia, 'total' => (int)$r->total], $viewsPorDia ?: [])) ?>;
  if (!raw.length) return;

  // Fill 30-day gaps
  var map = {};
  raw.forEach(function(r) { map[r.dia] = r.total; });
  var days = [];
  for (var i = 29; i >= 0; i--) {
    var d = new Date();
    d.setHours(0,0,0,0);
    d.setDate(d.getDate() - i);
    var key = d.toISOString().slice(0,10);
    days.push({ dia: key, total: map[key] || 0 });
  }

  var maxVal = Math.max.apply(null, days.map(function(d) { return d.total; }));
  maxVal = maxVal || 1;

  var svg    = document.getElementById('chart-svg');
  var wrap   = document.getElementById('chart-svg-wrap');
  var yAxis  = document.getElementById('chart-y-axis');
  var xAxis  = document.getElementById('chart-x-axis');
  var tooltip= document.getElementById('chart-tooltip');

  var ticks = [0, 0.25, 0.5, 0.75, 1];
  yAxis.innerHTML = ticks.slice().reverse().map(function(t) {
    return '<div class="chart-y-tick">' + Math.round(t * maxVal) + '</div>';
  }).join('');

  xAxis.innerHTML = '';
  [0, 7, 14, 21, 29].forEach(function(idx) {
    var d = new Date(days[idx].dia + 'T12:00:00');
    var lbl = (d.getDate()) + '/' + (d.getMonth() + 1);
    xAxis.innerHTML += '<span class="chart-x-tick">' + lbl + '</span>';
  });

  function smoothPath(points) {
    if (points.length < 2) return '';
    var d = 'M ' + points[0][0] + ',' + points[0][1];
    var t = 0.35;
    for (var i = 0; i < points.length - 1; i++) {
      var p0 = points[i > 0 ? i - 1 : 0];
      var p1 = points[i];
      var p2 = points[i + 1];
      var p3 = points[i + 2] || p2;
      var cp1x = p1[0] + (p2[0] - p0[0]) * t;
      var cp1y = p1[1] + (p2[1] - p0[1]) * t;
      var cp2x = p2[0] - (p3[0] - p1[0]) * t;
      var cp2y = p2[1] - (p3[1] - p1[1]) * t;
      d += ' C ' + cp1x + ',' + cp1y + ' ' + cp2x + ',' + cp2y + ' ' + p2[0] + ',' + p2[1];
    }
    return d;
  }

  function render() {
    var W = wrap.clientWidth;
    var H = 180;
    var padL = 4, padR = 4, padT = 12, padB = 22;
    var plotW = W - padL - padR;
    var plotH = H - padT - padB;

    svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
    svg.style.height = H + 'px';

    var grid = document.getElementById('chart-grid');
    grid.innerHTML = '';
    ticks.slice(1).forEach(function(t) {
      var y = padT + (1 - t) * plotH;
      var line = document.createElementNS('http://www.w3.org/2000/svg','line');
      line.setAttribute('x1', padL); line.setAttribute('x2', W - padR);
      line.setAttribute('y1', y); line.setAttribute('y2', y);
      line.setAttribute('stroke', 'rgba(0,0,0,.05)');
      line.setAttribute('stroke-width', '1');
      grid.appendChild(line);
    });

    var pts = days.map(function(d, i) {
      var x = padL + (i / (days.length - 1)) * plotW;
      var y = padT + (1 - d.total / maxVal) * plotH;
      return [x, y, d];
    });

    var linePath = smoothPath(pts);
    var baseY = padT + plotH;
    var areaPath = linePath + ' L ' + pts[pts.length-1][0] + ',' + baseY + ' L ' + pts[0][0] + ',' + baseY + ' Z';
    document.getElementById('chart-area-fill').setAttribute('d', areaPath);

    var line = document.getElementById('chart-line');
    line.setAttribute('d', linePath);
    var len = line.getTotalLength ? line.getTotalLength() : 2000;
    line.style.strokeDasharray = len;
    line.style.strokeDashoffset = len;
    line.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(.22,1,.36,1)';
    requestAnimationFrame(function() {
      requestAnimationFrame(function() { line.style.strokeDashoffset = 0; });
    });

    var dotsG = document.getElementById('chart-dots');
    dotsG.innerHTML = '';
    pts.forEach(function(p) {
      var c = document.createElementNS('http://www.w3.org/2000/svg','circle');
      c.setAttribute('cx', p[0]); c.setAttribute('cy', p[1]);
      c.setAttribute('r', '4');
      c.setAttribute('fill', '#f97316');
      c.setAttribute('stroke', '#f2f3f8');
      c.setAttribute('stroke-width', '2');
      c.style.opacity = '0';
      c.style.transition = 'opacity .15s';
      dotsG.appendChild(c);
    });

    var hz = document.getElementById('chart-hover-zone');
    hz.setAttribute('x', padL); hz.setAttribute('y', padT);
    hz.setAttribute('width', plotW); hz.setAttribute('height', plotH);

    var activeDot = null;
    hz.addEventListener('mousemove', function(e) {
      var rect = svg.getBoundingClientRect();
      var mx = e.clientX - rect.left - padL;
      var idx = Math.round(mx / plotW * (days.length - 1));
      idx = Math.max(0, Math.min(days.length - 1, idx));
      var p = pts[idx];
      var d = days[idx];

      if (activeDot) activeDot.style.opacity = '0';
      activeDot = dotsG.children[idx];
      if (activeDot) activeDot.style.opacity = '1';

      var dd = new Date(d.dia + 'T12:00:00');
      var lbl = dd.getDate() + '/' + (dd.getMonth()+1) + '/' + dd.getFullYear();
      tooltip.innerHTML = '<strong>' + d.total + '</strong> view' + (d.total !== 1 ? 's' : '') + ' &middot; ' + lbl;

      var tx = p[0] + rect.left - wrap.getBoundingClientRect().left + 10;
      var ty = p[1] - 40;
      tooltip.style.left = tx + 'px';
      tooltip.style.top  = ty + 'px';
      tooltip.classList.add('show');
    });
    hz.addEventListener('mouseleave', function() {
      tooltip.classList.remove('show');
      if (activeDot) { activeDot.style.opacity = '0'; activeDot = null; }
    });
  }

  render();
  var ro = new ResizeObserver(render);
  ro.observe(wrap);
})();

// ── Curva de retenção chart ───────────────────────────────
(function() {
  var retData = <?= json_encode($retencao ?: []) ?>;
  var videoDur = <?= (int)$videoDur ?>;
  if (!retData.length) return;

  var svg   = document.getElementById('ret-svg');
  var wrap  = document.getElementById('ret-svg-wrap');
  var yAxis = document.getElementById('ret-y-axis');
  var xAxis = document.getElementById('ret-x-axis');
  var tooltip = document.getElementById('ret-tooltip');
  if (!svg || !wrap) return;

  // Y axis: 0-100% in 25% steps
  var ticks = [0, 0.25, 0.5, 0.75, 1];
  yAxis.innerHTML = ticks.slice().reverse().map(function(t) {
    return '<div class="chart-y-tick">' + Math.round(t * 100) + '%</div>';
  }).join('');

  // X axis: 5 labels evenly across duration
  xAxis.innerHTML = '';
  var xIdxs = [0, Math.floor(retData.length * 0.25), Math.floor(retData.length * 0.5), Math.floor(retData.length * 0.75), retData.length - 1];
  xIdxs.forEach(function(idx) {
    var s = retData[Math.min(idx, retData.length - 1)].s;
    xAxis.innerHTML += '<span class="chart-x-tick">' + fmtSeg(s) + '</span>';
  });

  function smoothPath(points) {
    if (points.length < 2) return '';
    var d = 'M ' + points[0][0] + ',' + points[0][1];
    var t = 0.3;
    for (var i = 0; i < points.length - 1; i++) {
      var p0 = points[i > 0 ? i - 1 : 0];
      var p1 = points[i];
      var p2 = points[i + 1];
      var p3 = points[i + 2] || p2;
      var cp1x = p1[0] + (p2[0] - p0[0]) * t;
      var cp1y = p1[1] + (p2[1] - p0[1]) * t;
      var cp2x = p2[0] - (p3[0] - p1[0]) * t;
      var cp2y = p2[1] - (p3[1] - p1[1]) * t;
      d += ' C ' + cp1x + ',' + cp1y + ' ' + cp2x + ',' + cp2y + ' ' + p2[0] + ',' + p2[1];
    }
    return d;
  }

  function render() {
    var W = wrap.clientWidth;
    var H = 180;
    var padL = 4, padR = 4, padT = 12, padB = 22;
    var plotW = W - padL - padR;
    var plotH = H - padT - padB;

    svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
    svg.style.height = H + 'px';

    // Grid lines
    var grid = document.getElementById('ret-grid');
    grid.innerHTML = '';
    ticks.slice(1).forEach(function(t) {
      var y = padT + (1 - t) * plotH;
      var line = document.createElementNS('http://www.w3.org/2000/svg','line');
      line.setAttribute('x1', padL); line.setAttribute('x2', W - padR);
      line.setAttribute('y1', y); line.setAttribute('y2', y);
      line.setAttribute('stroke', 'rgba(0,0,0,.05)');
      line.setAttribute('stroke-width', '1');
      grid.appendChild(line);
    });

    // Points: x = second/duration, y = pct/100
    var n = retData.length;
    var pts = retData.map(function(d, i) {
      var x = padL + (n > 1 ? (i / (n - 1)) : 0) * plotW;
      var y = padT + (1 - Math.min(d.pct, 100) / 100) * plotH;
      return [x, y, d];
    });

    var linePath = smoothPath(pts);
    var baseY = padT + plotH;
    var areaPath = linePath + ' L ' + pts[pts.length-1][0] + ',' + baseY + ' L ' + pts[0][0] + ',' + baseY + ' Z';
    document.getElementById('ret-area-fill').setAttribute('d', areaPath);

    var line = document.getElementById('ret-line');
    line.setAttribute('d', linePath);
    var len = line.getTotalLength ? line.getTotalLength() : 2000;
    line.style.strokeDasharray = len;
    line.style.strokeDashoffset = len;
    line.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(.22,1,.36,1)';
    requestAnimationFrame(function() {
      requestAnimationFrame(function() { line.style.strokeDashoffset = 0; });
    });

    // Dots
    var dotsG = document.getElementById('ret-dots');
    dotsG.innerHTML = '';
    pts.forEach(function(p) {
      var c = document.createElementNS('http://www.w3.org/2000/svg','circle');
      c.setAttribute('cx', p[0]); c.setAttribute('cy', p[1]);
      c.setAttribute('r', '4');
      c.setAttribute('fill', '#22c55e');
      c.setAttribute('stroke', '#f2f3f8');
      c.setAttribute('stroke-width', '2');
      c.style.opacity = '0';
      c.style.transition = 'opacity .15s';
      dotsG.appendChild(c);
    });

    // Hover
    var hz = document.getElementById('ret-hover-zone');
    hz.setAttribute('x', padL); hz.setAttribute('y', padT);
    hz.setAttribute('width', plotW); hz.setAttribute('height', plotH);

    var activeDot = null;
    hz.addEventListener('mousemove', function(e) {
      var rect = svg.getBoundingClientRect();
      var mx = e.clientX - rect.left - padL;
      var idx = Math.round(mx / plotW * (n - 1));
      idx = Math.max(0, Math.min(n - 1, idx));
      var p = pts[idx];
      var d = retData[idx];

      if (activeDot) activeDot.style.opacity = '0';
      activeDot = dotsG.children[idx];
      if (activeDot) activeDot.style.opacity = '1';

      tooltip.innerHTML = '<strong>' + d.pct + '%</strong> assistindo &middot; ' + fmtSeg(d.s);

      var tx = p[0] + rect.left - wrap.getBoundingClientRect().left + 10;
      var ty = p[1] - 40;
      tooltip.style.left = tx + 'px';
      tooltip.style.top  = ty + 'px';
      tooltip.classList.add('show');
    });
    hz.addEventListener('mouseleave', function() {
      tooltip.classList.remove('show');
      if (activeDot) { activeDot.style.opacity = '0'; activeDot = null; }
    });
  }

  render();
  var ro = new ResizeObserver(render);
  ro.observe(wrap);
})();

// ── Pitch editor ──────────────────────────────────────────
(function() {
  var toggle = document.getElementById('pitch-toggle');
  var form   = document.getElementById('pitch-form');
  var input  = document.getElementById('pitch-input');
  var save   = document.getElementById('pitch-save');
  var cancel = document.getElementById('pitch-cancel');
  if (!toggle) return;

  toggle.addEventListener('click', function() {
    form.classList.add('show');
    toggle.style.display = 'none';
    input.focus();
  });
  cancel.addEventListener('click', function() {
    form.classList.remove('show');
    toggle.style.display = 'inline-block';
  });
  save.addEventListener('click', function() {
    var val = input.value.trim();
    // Parse mm:ss or hh:mm:ss
    var parts = val.split(':').map(Number);
    var secs = 0;
    if (parts.length === 2) secs = parts[0] * 60 + parts[1];
    else if (parts.length === 3) secs = parts[0] * 3600 + parts[1] * 60 + parts[2];
    if (!secs || isNaN(secs) || secs < 0) { input.style.borderColor = 'red'; return; }
    input.style.borderColor = '';
    save.disabled = true;
    save.textContent = '...';
    var body = new URLSearchParams({ pitch_time: secs, id: <?= (int)$video->id ?> });
    fetch(<?= json_encode(url("videos/salvar")) ?>, { method: 'POST', body })
      .then(function(r) { return r.json(); })
      .then(function() { location.reload(); })
      .catch(function() { save.disabled = false; save.textContent = 'Salvar'; });
  });
  // Allow Enter key in input
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') save.click();
  });
})();

// ── Ao Vivo ───────────────────────────────────────────────
(function() {
  var videoId   = <?= (int)$video->id ?>;
  var endpoint  = <?= json_encode(url("metricas/ao-vivo/{id}")) ?>.replace('{id}', videoId);
  var timer     = null;
  var lastFetch = null;
  var isActive  = false;

  function updateUpdatedLabel() {
    var el = document.getElementById('aovivo-updated');
    if (!el || lastFetch === null) return;
    var sec = Math.round((Date.now() - lastFetch) / 1000);
    el.textContent = 'atualizado há ' + sec + 's';
  }

  function renderAoVivo(data) {
    lastFetch = Date.now();
    var total     = data.total || 0;
    var devices   = data.devices || {};
    var cidades   = data.cidades || {};

    document.getElementById('aovivo-total').textContent = total;
    document.getElementById('aovivo-sublabel').textContent = total === 1 ? 'assistindo agora' : 'assistindo agora';

    var grid  = document.getElementById('aovivo-grid');
    var empty = document.getElementById('aovivo-empty');

    if (total === 0) {
      grid.style.display  = 'none';
      empty.style.display = 'block';
    } else {
      grid.style.display  = 'grid';
      empty.style.display = 'none';

      // Devices
      var maxDev = Math.max(devices.Mobile || 0, devices.Desktop || 0, devices.Tablet || 0, 1);
      document.getElementById('bar-mobile').style.width  = Math.round((devices.Mobile  || 0) / maxDev * 100) + '%';
      document.getElementById('bar-desktop').style.width = Math.round((devices.Desktop || 0) / maxDev * 100) + '%';
      document.getElementById('bar-tablet').style.width  = Math.round((devices.Tablet  || 0) / maxDev * 100) + '%';
      document.getElementById('val-mobile').textContent  = devices.Mobile  || 0;
      document.getElementById('val-desktop').textContent = devices.Desktop || 0;
      document.getElementById('val-tablet').textContent  = devices.Tablet  || 0;

      // Cidades
      var cidKeys = Object.keys(cidades);
      var maxCid  = cidKeys.length ? Math.max.apply(null, cidKeys.map(function(k) { return cidades[k]; })) : 1;
      var cidHtml = '';
      if (cidKeys.length) {
        cidKeys.forEach(function(cidade) {
          var cnt = cidades[cidade];
          var pct = Math.round(cnt / maxCid * 100);
          cidHtml += '<div class="aovivo-location-row">' +
            '<span class="aovivo-loc-label" title="' + cidade + '">' + cidade + '</span>' +
            '<div class="aovivo-bar-track" style="max-width:60px;"><div class="aovivo-bar-fill" style="width:' + pct + '%;background:#22c55e;"></div></div>' +
            '<span class="aovivo-loc-val">' + cnt + '</span>' +
            '</div>';
        });
      } else {
        cidHtml = '<div style="color:var(--muted);font-size:.78rem;">Aguardando dados de geo...</div>';
      }
      document.getElementById('aovivo-cidades').innerHTML = cidHtml;
    }

    updateUpdatedLabel();
  }

  function fetchAoVivo() {
    fetch(endpoint, { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(d) { if (d.ok) renderAoVivo(d); })
      .catch(function() {});
  }

  function startPolling() {
    if (isActive) return;
    isActive = true;
    fetchAoVivo();
    timer = setInterval(fetchAoVivo, 30000);
    // Update "atualizado há Xs" every second
    setInterval(updateUpdatedLabel, 1000);
  }

  function stopPolling() {
    isActive = false;
    if (timer) { clearInterval(timer); timer = null; }
  }

  // Hook into switchTab
  var _origSwitch = window.switchTab;
  window.switchTab = function(name) {
    _origSwitch(name);
    if (name === 'aovivo') {
      startPolling();
    } else {
      stopPolling();
    }
  };
})();

// ── KPI Selector ─────────────────────────────────────────
(function() {
  var videoId  = <?= (int)$video->id ?>;
  var storageKey = 'wt_kpi_pref_' + videoId;
  var btn      = document.getElementById('kpi-selector-btn');
  var popover  = document.getElementById('kpi-popover');
  var closeBtn = document.getElementById('kpi-popover-close');
  if (!btn || !popover) return;

  // Load saved prefs (array of disabled kpi names)
  function loadPrefs() {
    try { return JSON.parse(localStorage.getItem(storageKey) || '[]'); } catch(e) { return []; }
  }
  function savePrefs(disabled) {
    localStorage.setItem(storageKey, JSON.stringify(disabled));
  }
  function applyPrefs(disabled) {
    document.querySelectorAll('[data-kpi]').forEach(function(card) {
      card.style.display = disabled.indexOf(card.dataset.kpi) !== -1 ? 'none' : '';
    });
    // Sync checkboxes
    popover.querySelectorAll('[data-kpi-target]').forEach(function(cb) {
      cb.checked = disabled.indexOf(cb.dataset.kpiTarget) === -1;
    });
  }

  // Apply on load
  applyPrefs(loadPrefs());

  // Toggle popover
  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    popover.classList.toggle('open');
  });
  closeBtn.addEventListener('click', function() {
    popover.classList.remove('open');
  });
  document.addEventListener('click', function(e) {
    if (!document.getElementById('kpi-selector-wrap').contains(e.target)) {
      popover.classList.remove('open');
    }
  });

  // Checkbox changes
  popover.querySelectorAll('[data-kpi-target]').forEach(function(cb) {
    cb.addEventListener('change', function() {
      var disabled = loadPrefs();
      var key = cb.dataset.kpiTarget;
      if (cb.checked) {
        disabled = disabled.filter(function(k) { return k !== key; });
      } else {
        if (disabled.indexOf(key) === -1) disabled.push(key);
      }
      savePrefs(disabled);
      applyPrefs(disabled);
    });
  });
})();

function zerarMetricas() {
  if (!confirm('Zerar TODAS as métricas deste vídeo?\n\nEsta ação apaga views, plays, retenção e dados geográficos. Não pode ser desfeita.')) return;

  fetch(<?= json_encode(url("metricas/zerar")) ?>, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'video_id=<?= (int)$video->id ?>'
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    if (res.ok) {
      // Limpa o dedup do localStorage para este vídeo
      var token = <?= json_encode($video->embed_token) ?>;
      localStorage.removeItem('wt_view_' + token);
      localStorage.removeItem('wt_play_' + token);
      alert('Métricas zeradas com sucesso!');
      location.reload();
    } else {
      alert('Erro: ' + (res.msg || 'Não foi possível zerar as métricas.'));
    }
  })
  .catch(function() { alert('Erro de comunicação.'); });
}
</script>
<?php $v->end() ?>

<?php $v->layout("_theme") ?>

<?php $v->start("styles") ?>
<style>
/* ── KPI cards ───────────────────────────────────────────── */
.kpi-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: .9rem;
  margin-bottom: 1.2rem;
}
@media (max-width: 900px) { .kpi-row { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 480px) { .kpi-row { grid-template-columns: 1fr; } }

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

/* ── Chart ───────────────────────────────────────────────── */
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

.chart-area {
  position: relative;
  width: 100%;
  height: 180px;
}
.chart-area svg {
  width: 100%; height: 100%;
  overflow: visible;
}
.chart-area .y-label {
  position: absolute; left: 0; top: 0; bottom: 0;
  display: flex; flex-direction: column;
  justify-content: space-between;
  pointer-events: none;
}
.y-lbl {
  font-size: .63rem; color: var(--muted);
  line-height: 1; padding-right: .5rem;
  text-align: right;
}

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
#chart-tooltip {
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
#chart-tooltip.show { opacity: 1; transform: translateY(0); }
#chart-tooltip strong { color: var(--accent); }

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
</style>
<?php $v->end() ?>

<?php
$views     = (int)($totais->views       ?? 0);
$plays     = (int)($totais->plays       ?? 0);
$completes = (int)($totais->completes   ?? 0);
$sessoes   = (int)($totais->sessoes_unicas ?? 0);
$avgPct    = (float)($progStats->avg_pct  ?? 0);
$avgMin    = (float)($progStats->avg_min  ?? 0);
$playRate  = $views > 0  ? round($plays  / $views * 100, 1) : 0;
$compRate  = $plays > 0  ? round($completes / $plays * 100, 1) : 0;
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
</div>

<!-- ── KPI Cards ── -->
<div class="kpi-row">
  <div class="kpi-card">
    <div class="kpi-icon"><i class="fas fa-eye"></i></div>
    <div class="kpi-val"><?= number_format($views) ?></div>
    <div class="kpi-lbl">Visualizações</div>
    <div class="kpi-sub"><?= $sessoes ?> sessões únicas</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon"><i class="fas fa-play"></i></div>
    <div class="kpi-val"><?= $playRate ?>%</div>
    <div class="kpi-lbl">Taxa de play</div>
    <div class="kpi-sub"><?= number_format($plays) ?> plays totais</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon"><i class="fas fa-flag-checkered"></i></div>
    <div class="kpi-val"><?= $compRate ?>%</div>
    <div class="kpi-lbl">Completaram</div>
    <div class="kpi-sub"><?= number_format($completes) ?> de <?= number_format($plays) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
    <div class="kpi-val"><?= $avgPct ?>%</div>
    <div class="kpi-lbl">Assistido médio</div>
    <div class="kpi-sub"><?= $avgMin ?> min em média</div>
  </div>
</div>

<!-- ── Tabs ── -->
<div class="wt-tabs">
  <div class="wt-tab active" onclick="switchTab('visao')">
    <i class="fas fa-chart-area"></i> Visão Geral
  </div>
  <div class="wt-tab" onclick="switchTab('anuncios')">
    <i class="fas fa-bullhorn"></i> Anúncios
    <?php if ($adsStats) : ?>
      <span style="background:var(--accent-soft);border:1px solid var(--accent-border);color:var(--accent);font-size:.62rem;padding:.08rem .4rem;border-radius:99px;font-weight:700;"><?= count($adsStats) ?></span>
    <?php endif; ?>
  </div>
</div>

<!-- ── Tab: Visão Geral ── -->
<div class="tab-panel active" id="tab-visao">
  <div class="chart-card">
    <div class="chart-header">
      <span class="chart-title"><i class="fas fa-chart-area" style="color:var(--accent);margin-right:.4rem;opacity:.7;"></i>Visualizações por dia</span>
      <span class="chart-legend"><span class="chart-legend-dot"></span> views</span>
    </div>

    <?php if ($viewsPorDia) : ?>
    <div id="chart-tooltip"></div>
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

// ── Chart ─────────────────────────────────────────────────
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

  // Y axis labels (nice ticks)
  var ticks = [0, 0.25, 0.5, 0.75, 1];
  yAxis.innerHTML = ticks.slice().reverse().map(function(t) {
    return '<div class="chart-y-tick">' + Math.round(t * maxVal) + '</div>';
  }).join('');

  // X axis labels (every ~6 days)
  xAxis.innerHTML = '';
  [0, 7, 14, 21, 29].forEach(function(idx) {
    var d = new Date(days[idx].dia + 'T12:00:00');
    var lbl = (d.getDate()) + '/' + (d.getMonth() + 1);
    xAxis.innerHTML += '<span class="chart-x-tick">' + lbl + '</span>';
  });

  function render() {
    var W = wrap.clientWidth;
    var H = 180;
    var padL = 4, padR = 4, padT = 12, padB = 22;
    var plotW = W - padL - padR;
    var plotH = H - padT - padB;

    svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
    svg.style.height = H + 'px';

    // Grid lines
    var grid = document.getElementById('chart-grid');
    grid.innerHTML = '';
    ticks.slice(1).forEach(function(t) {
      var y = padT + (1 - t) * plotH;
      var line = document.createElementNS('http://www.w3.org/2000/svg','line');
      line.setAttribute('x1', padL); line.setAttribute('x2', W - padR);
      line.setAttribute('y1', y); line.setAttribute('y2', y);
      line.setAttribute('stroke', 'rgba(255,255,255,.05)');
      line.setAttribute('stroke-width', '1');
      grid.appendChild(line);
    });

    // Compute points
    var pts = days.map(function(d, i) {
      var x = padL + (i / (days.length - 1)) * plotW;
      var y = padT + (1 - d.total / maxVal) * plotH;
      return [x, y, d];
    });

    // Smooth bezier path helper
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

    var linePath = smoothPath(pts);
    var baseY = padT + plotH;

    // Area fill
    var areaPath = linePath
      + ' L ' + pts[pts.length - 1][0] + ',' + baseY
      + ' L ' + pts[0][0] + ',' + baseY + ' Z';
    document.getElementById('chart-area-fill').setAttribute('d', areaPath);

    // Line with draw-in animation
    var line = document.getElementById('chart-line');
    line.setAttribute('d', linePath);
    var len = line.getTotalLength ? line.getTotalLength() : 2000;
    line.style.strokeDasharray = len;
    line.style.strokeDashoffset = len;
    line.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(.22,1,.36,1)';
    requestAnimationFrame(function() {
      requestAnimationFrame(function() { line.style.strokeDashoffset = 0; });
    });

    // Invisible dots (shown on hover)
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

    // Hover zone
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
</script>
<?php $v->end() ?>

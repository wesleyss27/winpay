<?php $v->layout("_theme") ?>

<?php
$hora = (int)date("G");
$saudacao = $hora < 12 ? "Bom dia" : ($hora < 18 ? "Boa tarde" : "Boa noite");
$primeiroNome = trim(explode(" ", trim($usuario))[0] ?? "");

$fmtDelta = function (?float $pct, string $sufixo = "%") {
    if ($pct === null) return "";
    $cls  = $pct >= 0 ? "up" : "down";
    $seta = $pct >= 0 ? "fa-arrow-trend-up" : "fa-arrow-trend-down";
    $txt  = ($pct > 0 ? "+" : "") . str_replace(".", ",", (string)$pct) . $sufixo;
    return '<span class="kpi-delta ' . $cls . '"><i class="fas ' . $seta . '"></i>' . $txt . '</span>';
};

// Taxa de conclusão por dia (para o sparkline do 4º card)
$taxaDia = [];
foreach ($serie["play"] as $i => $p) {
    $taxaDia[] = $p > 0 ? round($serie["complete"][$i] / $p * 100, 1) : 0;
}

$maxPlays = !empty($topVideos) ? max(array_map(fn($r) => $r->plays, $topVideos)) : 1;
$maxPlays = max($maxPlays, 1);
$capaUrl  = fn($vid) => $vid->capa ? url("uploads/capas/{$vid->capa}") : null;
?>

<?php $v->start("page_head") ?>
  <h1><?= $saudacao ?><?= $primeiroNome !== "" ? ", " . htmlspecialchars($primeiroNome, ENT_QUOTES) : "" ?></h1>
  <p>Veja o desempenho dos seus vídeos nos últimos <?= $dias ?> dias.</p>
<?php $v->end() ?>

<?php $v->start("actions") ?>
  <label class="dash-period">
    <i class="far fa-calendar"></i>
    <select id="periodo" aria-label="Período">
      <?php foreach ($periodos as $p) : ?>
        <option value="<?= $p ?>" <?= $p === $dias ? "selected" : "" ?>>Últimos <?= $p ?> dias</option>
      <?php endforeach; ?>
    </select>
    <i class="fas fa-chevron-down caret"></i>
  </label>
  <a href="<?= url("videos") ?>" class="btn-wt btn-wt-ghost"><i class="fas fa-film"></i> Meus vídeos</a>
  <a href="<?= url("videos#novo") ?>" class="btn-wt"><i class="fas fa-cloud-arrow-up"></i> Upload</a>
<?php $v->end() ?>

<?php $v->start("styles") ?>
<style>
.dash-period {
  position: relative; display: inline-flex; align-items: center; gap: .55rem;
  background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm);
  padding: 0 .9rem; height: 40px; color: var(--muted);
  box-shadow: 0 1px 2px rgba(20,24,60,.04);
}
.dash-period select {
  appearance: none; -webkit-appearance: none; background: none; border: none; outline: none;
  font-family: inherit; font-size: .84rem; font-weight: 600; color: var(--text);
  padding-right: 1.2rem; cursor: pointer;
}
.dash-period .caret { position: absolute; right: .9rem; font-size: .6rem; pointer-events: none; }
.dash-period:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(91,77,240,.12); }

.dash-row { display: grid; gap: 1.1rem; margin-bottom: 1.1rem; }
.dash-row.r2 { grid-template-columns: minmax(0, 1.9fr) minmax(0, 1fr); }
.dash-row.r3 { grid-template-columns: minmax(0, 1.9fr) minmax(0, 1fr); }
@media (max-width: 1100px) { .dash-row.r2, .dash-row.r3 { grid-template-columns: 1fr; } }

/* ── Gráfico ─────────────────────────────────────────────── */
#chart { position: relative; height: 280px; }
#chart svg { display: block; width: 100%; height: 100%; overflow: visible; }
.chart-grid line { stroke: #eceef6; stroke-width: 1; }
.chart-axis text { font-size: 11px; fill: #8b8fae; font-family: inherit; }
.chart-tip {
  position: absolute; pointer-events: none; z-index: 2;
  background: #171938; color: #fff; border-radius: 9px; padding: .45rem .7rem;
  font-size: .74rem; line-height: 1.35; white-space: nowrap;
  transform: translate(-50%, calc(-100% - 12px));
  opacity: 0; transition: opacity .12s;
}
.chart-tip b { display: block; font-size: .8rem; }
.chart-tip span { color: rgba(255,255,255,.65); }

/* ── Top vídeos ──────────────────────────────────────────── */
.top-list { display: flex; flex-direction: column; }
.top-item { display: grid; grid-template-columns: 18px 64px 1fr 92px; gap: .8rem; align-items: center; padding: .6rem 0; }
.top-item + .top-item { border-top: 1px solid var(--border); }
.top-rank { font-size: .85rem; font-weight: 800; color: var(--muted); text-align: center; }
.top-item:nth-child(1) .top-rank { color: var(--accent); }
.top-name { font-size: .84rem; font-weight: 700; text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.top-name:hover { color: var(--accent); }
.top-sub { font-size: .74rem; color: var(--muted); }
.top-rate { text-align: right; font-size: .78rem; font-weight: 700; }

/* ── Tabela recentes ─────────────────────────────────────── */
.rec-head, .rec-row { display: grid; grid-template-columns: minmax(0, 1fr) 120px 80px 130px; gap: 1rem; align-items: center; }
.rec-head { padding: 0 0 .6rem; font-size: .74rem; font-weight: 700; color: var(--muted); border-bottom: 1px solid var(--border); }
.rec-row { padding: .65rem 0; border-bottom: 1px solid var(--border); }
.rec-row:last-child { border-bottom: none; }
.rec-video { display: flex; align-items: center; gap: .8rem; min-width: 0; }
.rec-video .thumb { width: 60px; flex-shrink: 0; }
.rec-date { font-size: .78rem; color: var(--muted); }
.rec-num  { font-weight: 700; }
.rec-rate { font-size: .78rem; font-weight: 700; }
@media (max-width: 640px) {
  .rec-head { display: none; }
  .rec-row { grid-template-columns: minmax(0, 1fr) auto; }
  .rec-date, .rec-rate { display: none; }
}

/* ── Ações rápidas ───────────────────────────────────────── */
.qa-list { display: flex; flex-direction: column; gap: .7rem; }
.qa {
  display: flex; align-items: center; gap: .9rem; padding: .85rem 1rem;
  border: 1px solid var(--border); border-radius: 12px; text-decoration: none;
  transition: border-color .15s, background .15s;
}
.qa:hover { border-color: var(--accent-border); background: var(--accent-soft); }
.qa-ic { width: 38px; height: 38px; border-radius: 10px; background: var(--accent-soft); color: var(--accent);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.qa b { display: block; font-size: .85rem; }
.qa span { font-size: .74rem; color: var(--muted); }
.qa > .fa-chevron-right { margin-left: auto; font-size: .7rem; color: var(--muted); }

.dash-empty { padding: 2rem 1rem; text-align: center; color: var(--muted); font-size: .84rem; }
.dash-empty i { display: block; font-size: 1.8rem; opacity: .25; margin-bottom: .6rem; }
.dash-empty .btn-wt { margin-top: 1rem; }
</style>
<?php $v->end() ?>

<!-- KPIs -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon purple"><i class="fas fa-video"></i></div>
    <div class="kpi-lbl">Vídeos</div>
    <div class="kpi-val"><?= number_format($totalVideos, 0, ",", ".") ?></div>
    <div class="kpi-foot">
      <div class="kpi-cmp">
        <?php if ($videosNovos > 0) : ?><span class="kpi-delta up"><i class="fas fa-plus"></i><?= $videosNovos ?></span><?php endif; ?>
        no período
      </div>
      <?= wt_sparkline($acumulado, "#5b4df0") ?>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon blue"><i class="fas fa-play"></i></div>
    <div class="kpi-lbl">Plays</div>
    <div class="kpi-val"><?= number_format($atual["play"], 0, ",", ".") ?></div>
    <div class="kpi-foot">
      <div class="kpi-cmp"><?= $fmtDelta(wt_variacao($atual["play"], $anterior["play"])) ?>vs. período anterior</div>
      <?= wt_sparkline($serie["play"], "#3b7cf6") ?>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon green"><i class="fas fa-eye"></i></div>
    <div class="kpi-lbl">Visualizações</div>
    <div class="kpi-val"><?= number_format($atual["view"], 0, ",", ".") ?></div>
    <div class="kpi-foot">
      <div class="kpi-cmp"><?= $fmtDelta(wt_variacao($atual["view"], $anterior["view"])) ?>vs. período anterior</div>
      <?= wt_sparkline($serie["view"], "#12a765") ?>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon rose"><i class="fas fa-flag-checkered"></i></div>
    <div class="kpi-lbl">Taxa de conclusão</div>
    <div class="kpi-val"><?= str_replace(".", ",", (string)$taxaAtual) ?>%</div>
    <div class="kpi-foot">
      <div class="kpi-cmp">
        <?= $taxaAnterior !== null ? $fmtDelta(round($taxaAtual - $taxaAnterior, 1), " pp") : "" ?>vs. período anterior
      </div>
      <?= wt_sparkline($taxaDia, "#e5484d") ?>
    </div>
  </div>
</div>

<!-- Gráfico + Top vídeos -->
<div class="dash-row r2">
  <section class="panel">
    <div class="panel-head">
      <div class="panel-title">
        <i class="fas fa-chart-simple"></i>
        <div>
          <h2>Performance dos últimos <?= $dias ?> dias</h2>
          <p>Plays, visualizações e conclusões por dia.</p>
        </div>
      </div>
      <select class="metric-select" id="metric" aria-label="Métrica do gráfico">
        <option value="play">Plays</option>
        <option value="view">Visualizações</option>
        <option value="complete">Conclusões</option>
      </select>
    </div>
    <div id="chart"></div>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-title">
        <i class="fas fa-fire"></i>
        <div><h2>Top vídeos</h2></div>
      </div>
      <a class="panel-link" href="<?= url("videos") ?>">Ver todos</a>
    </div>
    <?php if (!empty($topVideos)) : ?>
    <div class="top-list">
      <?php foreach ($topVideos as $i => $r) : $capa = $capaUrl($r->video); ?>
      <div class="top-item">
        <div class="top-rank"><?= $i + 1 ?></div>
        <div class="thumb">
          <?php if ($capa) : ?><img src="<?= htmlspecialchars($capa, ENT_QUOTES) ?>" alt="" loading="lazy"><?php else : ?><i class="fas fa-play"></i><?php endif; ?>
        </div>
        <div style="min-width:0;">
          <a class="top-name" href="<?= url("metricas/{$r->video->id}") ?>"><?= htmlspecialchars($r->video->titulo, ENT_QUOTES) ?></a>
          <div class="top-sub"><?= number_format($r->plays, 0, ",", ".") ?> plays</div>
        </div>
        <div>
          <div class="top-rate"><?= $r->taxa ?>% <span style="font-weight:500;color:var(--muted);">concl.</span></div>
          <div class="bar"><i style="width:<?= round($r->plays / $maxPlays * 100) ?>%"></i></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else : ?>
    <div class="dash-empty"><i class="fas fa-chart-simple"></i>Nenhum play neste período.</div>
    <?php endif; ?>
  </section>
</div>

<!-- Recentes + Ações -->
<div class="dash-row r3">
  <section class="panel">
    <div class="panel-head">
      <div class="panel-title">
        <i class="fas fa-video"></i>
        <div>
          <h2>Últimos vídeos</h2>
          <p>Enviados recentemente, com plays do período.</p>
        </div>
      </div>
      <a class="panel-link" href="<?= url("videos") ?>">Ver todos</a>
    </div>
    <?php if (!empty($recentes)) : ?>
    <div class="rec-head"><span>Vídeo</span><span>Enviado em</span><span>Plays</span><span>Conclusão</span></div>
    <?php foreach ($recentes as $r) : $vid = $r->video; $capa = $capaUrl($vid); ?>
    <div class="rec-row">
      <div class="rec-video">
        <div class="thumb">
          <?php if ($capa) : ?><img src="<?= htmlspecialchars($capa, ENT_QUOTES) ?>" alt="" loading="lazy"><?php else : ?><i class="fas fa-play"></i><?php endif; ?>
        </div>
        <a class="top-name" href="<?= url("videos/editar/{$vid->id}") ?>"><?= htmlspecialchars($vid->titulo, ENT_QUOTES) ?></a>
      </div>
      <div class="rec-date"><?= formataDataHora($vid->created_at) ?></div>
      <div class="rec-num"><?= number_format($r->plays, 0, ",", ".") ?></div>
      <div class="rec-rate"><?= $r->taxa ?>%<div class="bar ok"><i style="width:<?= $r->taxa ?>%"></i></div></div>
    </div>
    <?php endforeach; ?>
    <?php else : ?>
    <div class="dash-empty">
      <i class="fas fa-film"></i>Você ainda não enviou nenhum vídeo.
      <br><a href="<?= url("videos#novo") ?>" class="btn-wt"><i class="fas fa-plus"></i> Enviar primeiro vídeo</a>
    </div>
    <?php endif; ?>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-title"><i class="fas fa-bolt"></i><div><h2>Ações rápidas</h2></div></div>
    </div>
    <div class="qa-list">
      <a class="qa" href="<?= url("videos#novo") ?>">
        <span class="qa-ic"><i class="fas fa-cloud-arrow-up"></i></span>
        <div><b>Enviar vídeo</b><span>MP4, WebM, MOV ou MKV</span></div>
        <i class="fas fa-chevron-right"></i>
      </a>
      <a class="qa" href="<?= url("videos") ?>">
        <span class="qa-ic"><i class="fas fa-folder-open"></i></span>
        <div><b>Biblioteca</b><span>Gerencie vídeos e pastas</span></div>
        <i class="fas fa-chevron-right"></i>
      </a>
      <?php if (!empty($topVideos)) : ?>
      <a class="qa" href="<?= url("metricas/{$topVideos[0]->video->id}") ?>">
        <span class="qa-ic"><i class="fas fa-chart-column"></i></span>
        <div><b>Métricas do top 1</b><span><?= htmlspecialchars(mb_strimwidth($topVideos[0]->video->titulo, 0, 28, "…"), ENT_QUOTES) ?></span></div>
        <i class="fas fa-chevron-right"></i>
      </a>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php $v->start("scripts") ?>
<script>
(function() {
  var DATA   = <?= json_encode(["labels" => $labels, "serie" => $serie], JSON_UNESCAPED_UNICODE) ?>;
  var NOMES  = { play: ["play", "plays"], view: ["visualização", "visualizações"], complete: ["conclusão", "conclusões"] };
  var MESES  = ["jan","fev","mar","abr","mai","jun","jul","ago","set","out","nov","dez"];
  var host   = document.getElementById("chart");
  var select = document.getElementById("metric");
  var ACCENT = "#5b4df0";

  document.getElementById("periodo").addEventListener("change", function() {
    location.href = location.pathname + "?dias=" + this.value;
  });

  function fmtDia(iso, longo) {
    var p = iso.split("-");
    var d = parseInt(p[2], 10), m = MESES[parseInt(p[1], 10) - 1];
    return longo ? d + " de " + m + " de " + p[0] : (d < 10 ? "0" + d : d) + " " + m;
  }
  function nice(max) {
    if (max <= 4) return 4;
    var pow = Math.pow(10, Math.floor(Math.log10(max)));
    var f = max / pow;
    var step = f <= 1 ? 1 : f <= 2 ? 2 : f <= 5 ? 5 : 10;
    return Math.ceil(max / (step * pow / 4)) * (step * pow / 4);
  }
  // Curva suave (Catmull-Rom → Bézier)
  function curva(pts) {
    var d = "M" + pts[0][0] + "," + pts[0][1];
    for (var i = 0; i < pts.length - 1; i++) {
      var p0 = pts[i - 1] || pts[i], p1 = pts[i], p2 = pts[i + 1], p3 = pts[i + 2] || p2;
      var c1x = p1[0] + (p2[0] - p0[0]) / 6, c1y = p1[1] + (p2[1] - p0[1]) / 6;
      var c2x = p2[0] - (p3[0] - p1[0]) / 6, c2y = p2[1] - (p3[1] - p1[1]) / 6;
      d += " C" + c1x + "," + c1y + " " + c2x + "," + c2y + " " + p2[0] + "," + p2[1];
    }
    return d;
  }

  function desenhar() {
    var tipo = select.value, vals = DATA.serie[tipo], n = vals.length;
    var W = host.clientWidth, H = host.clientHeight;
    var m = { t: 16, r: 16, b: 28, l: 44 };
    var iw = W - m.l - m.r, ih = H - m.t - m.b;
    var ymax = nice(Math.max.apply(null, vals.concat([1])));
    var X = function(i) { return m.l + (n === 1 ? iw / 2 : i / (n - 1) * iw); };
    var Y = function(v) { return m.t + ih - v / ymax * ih; };
    var pts = vals.map(function(v, i) { return [X(i), Y(v)]; });

    var s = '<svg viewBox="0 0 ' + W + ' ' + H + '" role="img" aria-label="Gráfico de ' + NOMES[tipo][1] + ' por dia">';
    s += '<defs><linearGradient id="ga" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + ACCENT + '" stop-opacity=".2"/><stop offset="1" stop-color="' + ACCENT + '" stop-opacity="0"/></linearGradient></defs>';
    s += '<g class="chart-grid chart-axis">';
    for (var k = 0; k <= 4; k++) {
      var yv = ymax / 4 * k, yy = Y(yv);
      s += '<line x1="' + m.l + '" x2="' + (W - m.r) + '" y1="' + yy + '" y2="' + yy + '"/>';
      s += '<text x="' + (m.l - 10) + '" y="' + (yy + 4) + '" text-anchor="end">' + Math.round(yv).toLocaleString("pt-BR") + '</text>';
    }
    var passo = Math.ceil(n / 8);
    for (var i = 0; i < n; i += 1) {
      if (i % passo !== 0 && i !== n - 1) continue;
      if (i !== n - 1 && n - 1 - i < passo * .6) continue;
      s += '<text x="' + X(i) + '" y="' + (H - 6) + '" text-anchor="middle">' + fmtDia(DATA.labels[i]) + '</text>';
    }
    s += '</g>';
    if (n > 1) {
      var linha = curva(pts);
      s += '<path d="' + linha + ' L' + X(n - 1) + ',' + (m.t + ih) + ' L' + X(0) + ',' + (m.t + ih) + ' Z" fill="url(#ga)"/>';
      s += '<path d="' + linha + '" fill="none" stroke="' + ACCENT + '" stroke-width="2.4" stroke-linecap="round"/>';
    }
    s += '<line id="c-guia" y1="' + m.t + '" y2="' + (m.t + ih) + '" stroke="' + ACCENT + '" stroke-opacity=".25" stroke-dasharray="3 3" style="display:none"/>';
    s += '<circle id="c-dot" r="5" fill="#fff" stroke="' + ACCENT + '" stroke-width="2.5" style="display:none"/>';
    s += '<rect x="' + m.l + '" y="' + m.t + '" width="' + iw + '" height="' + ih + '" fill="transparent" id="c-hit"/>';
    s += '</svg><div class="chart-tip" id="c-tip"></div>';
    host.innerHTML = s;

    var hit = host.querySelector("#c-hit"), dot = host.querySelector("#c-dot"),
        guia = host.querySelector("#c-guia"), tip = host.querySelector("#c-tip");
    function mostrar(ev) {
      var r = hit.getBoundingClientRect();
      var px = (ev.touches ? ev.touches[0].clientX : ev.clientX) - r.left;
      var i = n === 1 ? 0 : Math.max(0, Math.min(n - 1, Math.round(px / iw * (n - 1))));
      dot.setAttribute("cx", pts[i][0]); dot.setAttribute("cy", pts[i][1]);
      guia.setAttribute("x1", pts[i][0]); guia.setAttribute("x2", pts[i][0]);
      dot.style.display = guia.style.display = "";
      var q = vals[i];
      tip.innerHTML = "<b>" + q.toLocaleString("pt-BR") + " " + NOMES[tipo][q === 1 ? 0 : 1] + "</b><span>" + fmtDia(DATA.labels[i], true) + "</span>";
      tip.style.left = Math.max(60, Math.min(W - 60, pts[i][0])) + "px";
      tip.style.top  = pts[i][1] + "px";
      tip.style.opacity = 1;
    }
    function esconder() { dot.style.display = guia.style.display = "none"; tip.style.opacity = 0; }
    hit.addEventListener("mousemove", mostrar);
    hit.addEventListener("touchstart", mostrar, { passive: true });
    hit.addEventListener("touchmove", mostrar, { passive: true });
    hit.addEventListener("mouseleave", esconder);
  }

  select.addEventListener("change", desenhar);
  var t; window.addEventListener("resize", function() { clearTimeout(t); t = setTimeout(desenhar, 120); });
  desenhar();
})();
</script>
<?php $v->end() ?>

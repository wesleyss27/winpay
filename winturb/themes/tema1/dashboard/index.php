<?php $v->layout("_theme") ?>

<?php $v->start("styles") ?>
<style>
/* ── KPI grid ────────────────────────────────────────────── */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.3rem;
}
@media (max-width: 900px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px)  { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.2rem 1.3rem;
  display: flex; align-items: flex-start; gap: .9rem;
  box-shadow: var(--shadow);
  transition: box-shadow .2s, transform .15s;
}
.kpi-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }

.kpi-icon {
  width: 40px; height: 40px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: .9rem; flex-shrink: 0;
}
.kpi-icon-purple { background: rgba(108,92,231,.1); color: var(--accent); border: 1px solid rgba(108,92,231,.18); }
.kpi-icon-blue   { background: rgba(59,130,246,.1);  color: #3b82f6; border: 1px solid rgba(59,130,246,.18); }
.kpi-icon-green  { background: rgba(16,185,129,.1);  color: #10b981; border: 1px solid rgba(16,185,129,.18); }
.kpi-icon-rose   { background: rgba(244,63,94,.1);   color: #f43f5e; border: 1px solid rgba(244,63,94,.18); }

.kpi-body { flex: 1; min-width: 0; }
.kpi-val {
  font-family: 'Syne', sans-serif;
  font-size: 1.95rem; font-weight: 800; line-height: 1;
  color: var(--text); letter-spacing: -.02em;
}
.kpi-lbl {
  font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .1em;
  color: var(--muted); margin-top: .32rem;
}

/* ── Bottom grid ─────────────────────────────────────────── */
.dash-grid {
  display: grid;
  grid-template-columns: 1fr 290px;
  gap: 1rem; align-items: start;
}
@media (max-width: 960px) { .dash-grid { grid-template-columns: 1fr; } }

/* ── Section card ────────────────────────────────────────── */
.dash-section {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
}
.dash-section-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: .85rem 1.2rem;
  border-bottom: 1px solid var(--border);
  background: var(--bg);
}
.dash-section-title {
  font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .12em;
  color: var(--muted);
  display: flex; align-items: center; gap: .45rem;
}
.dash-section-title i { color: var(--accent); }

/* ── Recent rows ─────────────────────────────────────────── */
.recent-row {
  display: grid; grid-template-columns: 1fr auto;
  align-items: center; gap: 1rem;
  padding: .72rem 1.2rem;
  border-bottom: 1px solid var(--border);
  transition: background .12s;
}
.recent-row:last-child { border-bottom: none; }
.recent-row:hover { background: var(--bg); }
.recent-title {
  font-size: .86rem; font-weight: 500; color: var(--text);
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  text-decoration: none; display: block; transition: color .12s;
}
.recent-title:hover { color: var(--accent); }
.recent-meta { font-size: .7rem; color: var(--muted); margin-top: .1rem; }
.recent-badge {
  font-size: .67rem; font-weight: 600;
  font-family: 'JetBrains Mono', monospace;
  color: var(--muted); white-space: nowrap;
}

/* ── Top videos ──────────────────────────────────────────── */
.top-item {
  display: flex; align-items: center; gap: .72rem;
  padding: .72rem 1.2rem;
  border-bottom: 1px solid var(--border);
  transition: background .12s;
}
.top-item:last-child { border-bottom: none; }
.top-item:hover { background: var(--bg); }
.top-rank {
  font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 800;
  color: var(--border); width: 20px; text-align: center; flex-shrink: 0;
}
.top-rank.r1 { color: var(--accent); }
.top-rank.r2 { color: rgba(108,92,231,.45); }
.top-rank.r3 { color: rgba(108,92,231,.25); }
.top-info { flex: 1; min-width: 0; }
.top-name {
  font-size: .83rem; font-weight: 500; color: var(--text);
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  text-decoration: none; display: block; transition: color .12s;
}
.top-name:hover { color: var(--accent); }
.top-plays { font-size: .7rem; color: var(--muted); margin-top: .08rem; }
.top-bar-wrap {
  width: 52px; height: 3px;
  background: var(--bg3); border-radius: 99px; overflow: hidden; flex-shrink: 0;
}
.top-bar-fill { height: 100%; background: var(--accent); border-radius: 99px; }

/* ── Quick actions ───────────────────────────────────────── */
.quick-actions {
  display: grid; grid-template-columns: 1fr 1fr; gap: .5rem;
  padding: .9rem 1.2rem;
}
.qa-btn {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; gap: .4rem; padding: .85rem .5rem;
  border-radius: 9px; border: 1px solid var(--border);
  background: var(--bg); color: var(--muted);
  text-decoration: none; font-size: .7rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .07em;
  transition: background .15s, border-color .15s, color .15s, box-shadow .15s;
}
.qa-btn i { font-size: .95rem; }
.qa-btn:hover {
  background: var(--accent-soft);
  border-color: var(--accent-border);
  color: var(--accent);
  box-shadow: 0 2px 8px rgba(108,92,231,.1);
}

/* ── Empty ───────────────────────────────────────────────── */
.dash-empty {
  padding: 2.5rem 1.5rem; text-align: center; color: var(--muted);
}
.dash-empty i { font-size: 2rem; opacity: .2; display: block; margin-bottom: .7rem; }
.dash-empty p { font-size: .83rem; margin-bottom: 1.1rem; }
</style>
<?php $v->end() ?>

<?php
$maxPlays = !empty($topVideos) ? max(array_map(fn($v) => (int)$v->plays, $topVideos)) : 1;
$maxPlays = max($maxPlays, 1);
?>

<!-- KPI Cards -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon kpi-icon-purple"><i class="fas fa-film"></i></div>
    <div class="kpi-body">
      <div class="kpi-val"><?= number_format((int)$totalVideos) ?></div>
      <div class="kpi-lbl">Vídeos</div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon kpi-icon-blue"><i class="fas fa-play"></i></div>
    <div class="kpi-body">
      <div class="kpi-val"><?= number_format((int)($stats['plays'] ?? 0)) ?></div>
      <div class="kpi-lbl">Plays totais</div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon kpi-icon-green"><i class="fas fa-eye"></i></div>
    <div class="kpi-body">
      <div class="kpi-val"><?= number_format((int)($stats['views'] ?? 0)) ?></div>
      <div class="kpi-lbl">Visualizações</div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon kpi-icon-rose"><i class="fas fa-flag-checkered"></i></div>
    <div class="kpi-body">
      <div class="kpi-val"><?= number_format((int)($stats['completes'] ?? 0)) ?></div>
      <div class="kpi-lbl">Completados</div>
    </div>
  </div>
</div>

<!-- Main grid -->
<div class="dash-grid">

  <!-- Recentes -->
  <div class="dash-section">
    <div class="dash-section-head">
      <div class="dash-section-title"><i class="fas fa-clock"></i> Recentes</div>
      <a href="<?= url("videos") ?>" class="btn-wt btn-wt-sm btn-wt-ghost">Ver todos</a>
    </div>
    <?php if ($videos) : ?>
      <?php foreach ($videos as $vid) : ?>
      <div class="recent-row">
        <div style="min-width:0;">
          <a href="<?= url("videos/editar/{$vid->id}") ?>" class="recent-title">
            <?= htmlspecialchars($vid->titulo, ENT_QUOTES) ?>
          </a>
          <div class="recent-meta"><?= formataDataHora($vid->created_at) ?></div>
        </div>
        <div class="recent-badge"><?= htmlspecialchars(substr($vid->embed_token, 0, 8), ENT_QUOTES) ?>…</div>
      </div>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="dash-empty">
        <i class="fas fa-film"></i>
        <p>Nenhum vídeo ainda.</p>
        <a href="<?= url("videos") ?>" class="btn-wt"><i class="fas fa-plus"></i> Enviar primeiro vídeo</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar direita -->
  <div style="display:flex;flex-direction:column;gap:1rem;">

    <!-- Top vídeos -->
    <div class="dash-section">
      <div class="dash-section-head">
        <div class="dash-section-title"><i class="fas fa-fire"></i> Top vídeos</div>
      </div>
      <?php if (!empty($topVideos)) : ?>
        <?php foreach ($topVideos as $i => $tv) :
          $pct = round(($tv->plays / $maxPlays) * 100);
        ?>
        <div class="top-item">
          <div class="top-rank r<?= $i + 1 ?>"><?= $i + 1 ?></div>
          <div class="top-info">
            <a href="<?= url("metricas/{$tv->id}") ?>" class="top-name">
              <?= htmlspecialchars($tv->titulo, ENT_QUOTES) ?>
            </a>
            <div class="top-plays"><?= number_format((int)$tv->plays) ?> plays</div>
          </div>
          <div class="top-bar-wrap">
            <div class="top-bar-fill" style="width:<?= $pct ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else : ?>
        <div class="dash-empty">
          <i class="fas fa-chart-bar"></i>
          <p>Sem dados ainda.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Ações rápidas -->
    <div class="dash-section">
      <div class="dash-section-head">
        <div class="dash-section-title"><i class="fas fa-bolt"></i> Ações rápidas</div>
      </div>
      <div class="quick-actions">
        <a href="<?= url("videos") ?>" class="qa-btn">
          <i class="fas fa-film"></i> Biblioteca
        </a>
        <a href="<?= url("videos") ?>" class="qa-btn">
          <i class="fas fa-cloud-upload-alt"></i> Upload
        </a>
      </div>
    </div>

  </div>
</div>

<?php $v->layout("_theme") ?>

<?php
// ── Dados derivados para a view ─────────────────────────────────────────────
$countPorPasta = [];
foreach ($videos as $vid) {
    if ($vid->pasta_id) {
        $countPorPasta[(int)$vid->pasta_id] = ($countPorPasta[(int)$vid->pasta_id] ?? 0) + 1;
    }
}

$totalPlays  = array_sum($playsMap);
$publicados  = count(array_filter($videos, fn($x) => (int)$x->status === 1));
$retMedia    = $retMap ? round(array_sum($retMap) / count($retMap)) : null;

$statusDe = function ($vid) {
    if ((int)$vid->status !== 1) return ["Inativo", "warn"];
    $h = $vid->hls_status ?? "none";
    if (in_array($h, ["pending", "processing"], true)) return ["Processando", "info"];
    if ($h === "failed") return ["Falha no HLS", "warn"];
    return ["Publicado", "ok"];
};
?>

<?php $v->start("page_head") ?>
  <h1>Meus vídeos</h1>
  <p>Gerencie, organize e analise o desempenho dos seus vídeos em um só lugar.</p>
<?php $v->end() ?>

<?php $v->start("actions") ?>
  <label class="wt-search">
    <i class="fas fa-magnifying-glass"></i>
    <input type="search" id="search-input" placeholder="Buscar vídeos…" aria-label="Buscar vídeos">
  </label>
  <button class="btn-wt" id="btn-novo-video" onclick="toggleUpload()">
    <i class="fas fa-plus"></i> Novo vídeo
  </button>
  <button class="btn-wt btn-wt-ghost" onclick="abrirNovaPasta()">
    <i class="fas fa-folder-plus"></i> Nova pasta
  </button>
<?php $v->end() ?>

<?php $v->start("styles") ?>
<style>
/* ── Upload panel ─────────────────────────────────────── */
#upload-panel {
  display: none;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.2rem;
  margin-bottom: 1.1rem;
  box-shadow: var(--shadow);
  animation: wt-in .22s cubic-bezier(.22,1,.36,1);
}
#drop-zone {
  border: 1.5px dashed var(--accent-border);
  border-radius: 12px;
  padding: 2.2rem 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: border-color .18s, background .18s;
  background: var(--accent-soft);
}
#drop-zone:hover, #drop-zone.over { border-color: var(--accent); background: rgba(91,77,240,.14); }
#drop-zone .drop-icon {
  font-size: 1.8rem; color: var(--accent);
  display: block; margin-bottom: .6rem;
  transition: transform .2s;
}
#drop-zone:hover .drop-icon { transform: translateY(-3px); }
#upload-progress { display: none; margin-top: .9rem; }
#upload-bar-track { height: 6px; background: var(--bg3); border-radius: 99px; overflow: hidden; }
#upload-bar-fill { height: 100%; background: var(--grad); width: 0%; transition: width .2s; border-radius: 99px; }

/* ── Tabs + filtros ─────────────────────────────────────── */
.lib-bar {
  display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
  padding: 0 1.3rem; border-bottom: 1px solid var(--border);
}
.lib-tabs { display: flex; gap: .4rem; max-width: 100%; overflow-x: auto; }
.lib-tab {
  display: flex; align-items: center; gap: .5rem;
  padding: 1rem .8rem;
  font-size: .86rem; font-weight: 600; color: var(--muted);
  cursor: pointer; background: none; border: none; font-family: inherit; white-space: nowrap;
  border-bottom: 2px solid transparent; margin-bottom: -1px;
  transition: color .15s, border-color .15s;
}
.lib-tab:hover { color: var(--text); }
.lib-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
.tab-badge {
  background: var(--accent-soft); color: var(--accent);
  font-size: .68rem; font-weight: 700; padding: .08rem .45rem; border-radius: 99px;
}
.lib-filters { display: flex; align-items: center; gap: .6rem; padding: .6rem 0; }
.lib-filters .metric-select { min-height: 38px; }

/* ── Tabela ─────────────────────────────────────────────── */
.lib-panel {
  background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
  box-shadow: var(--shadow); overflow: hidden;
}
.table-scroll { overflow-x: auto; }
.table-inner { min-width: 1036px; }
.video-table-head, .video-row {
  display: grid;
  grid-template-columns: minmax(260px, 2.6fr) 120px 96px 68px 118px 96px 124px 176px;
  gap: 1rem; align-items: center; padding: 0 1.3rem;
}
.video-table-head { padding-top: .85rem; padding-bottom: .7rem; border-bottom: 1px solid var(--border); }
.video-table-head span { font-size: .74rem; font-weight: 700; color: var(--muted); }
.video-row { padding-top: .8rem; padding-bottom: .8rem; border-bottom: 1px solid var(--border); transition: background .12s; }
.video-row:last-child { border-bottom: none; }
.video-row:hover { background: #fafbfe; }

.video-cell { display: flex; align-items: center; gap: .9rem; min-width: 0; }
.video-thumb {
  position: relative; width: 84px; aspect-ratio: 16/10; flex-shrink: 0;
  border-radius: 9px; overflow: hidden;
  background: linear-gradient(135deg, #2a2660, #14122f);
  display: flex; align-items: center; justify-content: center;
  color: rgba(255,255,255,.35);
}
.video-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.video-dur {
  position: absolute; right: 4px; bottom: 4px;
  background: rgba(10,8,30,.78); color: #fff; border-radius: 5px;
  font-size: .64rem; font-weight: 700; padding: .1rem .35rem; letter-spacing: .01em;
}
.video-name a {
  font-size: .9rem; font-weight: 700; color: var(--text); text-decoration: none;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;
}
.video-name a:hover { color: var(--accent); }
.video-desc {
  font-size: .76rem; color: var(--muted); margin-top: .12rem;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.cell-muted { font-size: .8rem; color: var(--muted); }
.cell-num { font-size: .92rem; font-weight: 800; letter-spacing: -.01em; }
.video-pasta-tag {
  display: inline-flex; align-items: center; gap: .45rem; font-size: .8rem; color: var(--text);
  cursor: pointer; min-width: 0;
}
.video-pasta-tag i { color: var(--accent); }
.video-pasta-tag:hover { color: var(--accent); }
.metric { font-size: .8rem; font-weight: 700; }
.metric .bar { width: 88px; }

.row-actions { display: flex; align-items: center; gap: .35rem; justify-content: flex-end; }
.row-btn {
  width: 32px; height: 32px; border-radius: 9px;
  border: 1px solid var(--border); background: #fff;
  color: var(--muted); font-size: .8rem;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; text-decoration: none; position: relative;
  transition: background .12s, color .12s, border-color .12s;
}
.row-btn:hover { background: var(--accent-soft); color: var(--accent); border-color: var(--accent-border); }
.row-btn.danger:hover { background: var(--danger-soft); color: var(--danger); border-color: rgba(229,72,77,.3); }

.video-empty { text-align: center; padding: 4rem 2rem; color: var(--muted); }
.video-empty i { font-size: 2.4rem; opacity: .18; display: block; margin-bottom: 1rem; }
.video-empty .btn-wt { margin-top: 1rem; }
.no-results { display: none; }

.lib-foot { padding: .9rem 1.3rem; border-top: 1px solid var(--border); font-size: .8rem; color: var(--muted); }

/* ── Biblioteca (pastas) ─────────────────────────────────── */
.biblioteca-wrap { padding: 1.4rem 1.3rem 1.6rem; }
.pasta-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 1rem; margin-top: 1rem; transition: opacity .15s; }
.pasta-card {
  background: #fff; border: 1px solid var(--border); border-radius: 14px;
  padding: 1.1rem; cursor: pointer; position: relative;
  display: flex; flex-direction: column; gap: .55rem;
  transition: border-color .15s, box-shadow .15s;
}
.pasta-card:hover { border-color: var(--accent-border); box-shadow: var(--shadow-md); }
.pasta-card.active-pasta { border-color: var(--accent); background: var(--accent-soft); }
.pasta-icon {
  width: 40px; height: 40px; border-radius: 11px;
  background: var(--accent-soft); color: var(--accent);
  display: flex; align-items: center; justify-content: center; font-size: 1rem;
}
.pasta-name { font-size: .9rem; font-weight: 700; }
.pasta-count { font-size: .76rem; color: var(--muted); }
.pasta-del {
  position: absolute; top: .6rem; right: .6rem;
  width: 26px; height: 26px; border-radius: 7px; background: none; border: none;
  color: var(--muted); font-size: .76rem; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity .15s, color .15s, background .15s;
}
.pasta-card:hover .pasta-del, .pasta-del:focus-visible { opacity: 1; }
.pasta-del:hover { color: var(--danger); background: var(--danger-soft); }

.pasta-videos-wrap { margin-top: 1.5rem; display: none; }
.pasta-videos-wrap.show { display: block; }
.pasta-videos-header { display: flex; align-items: center; gap: .6rem; margin-bottom: .8rem; font-size: .84rem; color: var(--muted); }
.pasta-videos-header strong { color: var(--text); }
.pasta-back { cursor: pointer; padding: .2rem .4rem; border-radius: 6px; }
.pasta-back:hover { color: var(--accent); background: var(--accent-soft); }

/* Dropdown mover para pasta */
.move-dropdown {
  position: absolute;
  background: #fff; border: 1px solid var(--border); border-radius: 12px;
  min-width: 200px; z-index: 9000; padding: .4rem; display: none;
  box-shadow: 0 12px 32px -8px rgba(20,24,60,.22), 0 2px 6px rgba(20,24,60,.06);
  animation: dropdown-in .15s cubic-bezier(.22,1,.36,1);
}
@keyframes dropdown-in { from { opacity: 0; transform: translateY(-6px) scale(.97); } to { opacity: 1; transform: none; } }
.move-dropdown.open { display: block; }
.move-drop-title { font-size: .72rem; font-weight: 700; color: var(--muted); padding: .4rem .7rem .3rem; }
.move-opt {
  display: flex; align-items: center; gap: .6rem; padding: .55rem .7rem; border-radius: 8px;
  font-size: .84rem; font-weight: 600; cursor: pointer; transition: background .1s, color .1s;
}
.move-opt:hover { background: var(--accent-soft); color: var(--accent); }
.move-opt i { width: 15px; text-align: center; font-size: .78rem; color: var(--accent); flex-shrink: 0; }

@media (max-width: 768px) {
  .lib-bar { padding: 0 .8rem; }
  .lib-filters { width: 100%; padding-top: 0; }
  .lib-filters .metric-select { flex: 1; }
}
</style>
<?php $v->end() ?>

<!-- ── KPIs ── -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon purple"><i class="fas fa-video"></i></div>
    <div class="kpi-lbl">Total de vídeos</div>
    <div class="kpi-val"><?= number_format(count($videos), 0, ",", ".") ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon blue"><i class="fas fa-play"></i></div>
    <div class="kpi-lbl">Plays totais</div>
    <div class="kpi-val"><?= number_format($totalPlays, 0, ",", ".") ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon green"><i class="fas fa-eye"></i></div>
    <div class="kpi-lbl">Retenção média</div>
    <div class="kpi-val"><?= $retMedia !== null ? $retMedia . "%" : "—" ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon rose"><i class="fas fa-circle-check"></i></div>
    <div class="kpi-lbl">Vídeos publicados</div>
    <div class="kpi-val"><?= number_format($publicados, 0, ",", ".") ?></div>
  </div>
</div>

<!-- ── Upload panel ── -->
<div id="upload-panel">
  <div id="drop-zone" onclick="document.getElementById('file-input').click()">
    <i class="fas fa-cloud-arrow-up drop-icon"></i>
    <p style="font-size:.9rem;font-weight:700;margin-bottom:.2rem;">Clique ou arraste um vídeo aqui</p>
    <p style="font-size:.78rem;color:var(--muted);">MP4, WebM, MOV ou MKV &mdash; até 2 GB</p>
  </div>
  <input type="file" id="file-input" accept=".mp4,.webm,.ogg,.mov,.mkv" style="display:none;">
  <div id="upload-progress">
    <div style="display:flex;justify-content:space-between;font-size:.78rem;color:var(--muted);margin-bottom:.4rem;">
      <span id="upload-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:72%;"></span>
      <span id="upload-pct" style="color:var(--accent);font-weight:700;">0%</span>
    </div>
    <div id="upload-bar-track"><div id="upload-bar-fill"></div></div>
  </div>
</div>

<!-- ── Biblioteca ── -->
<div class="lib-panel">
  <div class="lib-bar">
    <div class="lib-tabs" role="tablist">
      <button class="lib-tab active" role="tab" onclick="switchTab('recentes')" id="tab-btn-recentes">
        <i class="fas fa-film"></i> Recentes
        <?php if ($videos) : ?><span class="tab-badge"><?= count($videos) ?></span><?php endif; ?>
      </button>
      <button class="lib-tab" role="tab" onclick="switchTab('top')" id="tab-btn-top">
        <i class="fas fa-fire"></i> Top vídeos
      </button>
      <button class="lib-tab" role="tab" onclick="switchTab('biblioteca')" id="tab-btn-biblioteca">
        <i class="fas fa-folder"></i> Pastas
        <?php if ($pastas) : ?><span class="tab-badge"><?= count($pastas) ?></span><?php endif; ?>
      </button>
    </div>
    <?php if ($videos && $pastas) : ?>
    <div class="lib-filters" id="lib-filters">
      <select class="metric-select" id="filtro-pasta" aria-label="Filtrar por pasta">
        <option value="">Todas as pastas</option>
        <option value="0">Sem pasta</option>
        <?php foreach ($pastas as $p) : ?>
          <option value="<?= (int)$p->id ?>"><?= htmlspecialchars($p->nome, ENT_QUOTES) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
  </div>

  <!-- Tab: Recentes + Top vídeos (mesma tabela, ordenação diferente) -->
  <div id="panel-recentes">
    <?php if ($videos) : ?>
    <div class="table-scroll">
      <div class="table-inner">
        <div class="video-table-head">
          <span>Vídeo</span>
          <span>Pasta</span>
          <span>Data</span>
          <span>Plays</span>
          <span>Retenção</span>
          <span>Conversão</span>
          <span>Status</span>
          <span style="text-align:right;">Ações</span>
        </div>
        <div id="video-list">
          <?php foreach ($videos as $vid) :
            $plays    = $playsMap[$vid->id] ?? 0;
            $cliques  = $cliquesMap[$vid->id] ?? 0;
            $conv     = $plays > 0 ? round($cliques / $plays * 100, 1) : null;
            $ret      = $retMap[$vid->id] ?? null;
            $dur      = $durMap[$vid->id] ?? 0;
            $thumbUrl = $vid->capa ? url("uploads/capas/{$vid->capa}") : null;
            $pasta    = $vid->pasta_id ? ($pastaMap[(int)$vid->pasta_id] ?? null) : null;
            [$stTxt, $stCls] = $statusDe($vid);
          ?>
          <div class="video-row" id="row-<?= $vid->id ?>"
               data-titulo="<?= htmlspecialchars(mb_strtolower($vid->titulo), ENT_QUOTES) ?>"
               data-plays="<?= $plays ?>"
               data-pasta="<?= (int)($vid->pasta_id ?? 0) ?>"
               data-created="<?= $vid->created_at ?>">
            <div class="video-cell">
              <div class="video-thumb">
                <?php if ($thumbUrl) : ?>
                  <img src="<?= htmlspecialchars($thumbUrl, ENT_QUOTES) ?>" alt="" loading="lazy">
                <?php else : ?>
                  <i class="fas fa-play"></i>
                <?php endif; ?>
                <?php if ($dur > 0) : ?><span class="video-dur"><?= wt_duracao($dur) ?></span><?php endif; ?>
              </div>
              <div style="min-width:0;">
                <div class="video-name">
                  <a href="<?= url("videos/editar/{$vid->id}") ?>"><?= htmlspecialchars($vid->titulo, ENT_QUOTES) ?></a>
                </div>
                <?php if (!empty($vid->descricao)) : ?>
                  <div class="video-desc"><?= htmlspecialchars($vid->descricao, ENT_QUOTES) ?></div>
                <?php endif; ?>
              </div>
            </div>
            <div>
              <?php if ($pasta) : ?>
                <span class="video-pasta-tag" onclick="switchTab('biblioteca');focusPasta(<?= (int)$vid->pasta_id ?>)">
                  <i class="fas fa-folder"></i><?= htmlspecialchars($pasta, ENT_QUOTES) ?>
                </span>
              <?php else : ?><span class="cell-muted">—</span><?php endif; ?>
            </div>
            <div class="cell-muted"><?= date("d/m/Y", strtotime($vid->created_at)) ?></div>
            <div class="cell-num"><?= number_format($plays, 0, ",", ".") ?></div>
            <div class="metric">
              <?php if ($ret !== null) : ?><?= $ret ?>%<div class="bar"><i style="width:<?= min(100, $ret) ?>%"></i></div>
              <?php else : ?><span class="cell-muted">—</span><?php endif; ?>
            </div>
            <div class="metric">
              <?php if ($conv !== null) : ?><?= str_replace(".", ",", (string)$conv) ?>%<div class="bar ok"><i style="width:<?= min(100, $conv) ?>%"></i></div>
              <?php else : ?><span class="cell-muted">—</span><?php endif; ?>
            </div>
            <div><span class="wt-badge <?= $stCls ?>"><span class="dot"></span><?= $stTxt ?></span></div>
            <div class="row-actions">
              <a href="<?= url("metricas/{$vid->id}") ?>" class="row-btn" title="Métricas" aria-label="Métricas">
                <i class="fas fa-chart-column"></i>
              </a>
              <button class="row-btn" title="Copiar embed" aria-label="Copiar embed"
                onclick="copiarEmbed('<?= htmlspecialchars(url("player/{$vid->embed_token}"), ENT_QUOTES) ?>',<?= (int)$vid->largura ?>,<?= (int)$vid->altura ?>)">
                <i class="fas fa-code"></i>
              </button>
              <div style="position:relative;">
                <button class="row-btn" title="Mover para pasta" aria-label="Mover para pasta"
                  onclick="toggleMoveDropdown(this, <?= $vid->id ?>)">
                  <i class="fas fa-folder-open"></i>
                </button>
                <div class="move-dropdown" id="move-<?= $vid->id ?>">
                  <div class="move-drop-title">Mover para pasta</div>
                  <div class="move-opt" onclick="moverVideo(<?= $vid->id ?>, null)">
                    <i class="fas fa-xmark"></i> Sem pasta
                  </div>
                  <?php foreach ($pastas as $p) : ?>
                  <div class="move-opt" onclick="moverVideo(<?= $vid->id ?>, <?= $p->id ?>)">
                    <i class="fas fa-folder"></i> <?= htmlspecialchars($p->nome, ENT_QUOTES) ?>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <a href="<?= url("videos/editar/{$vid->id}") ?>" class="row-btn" title="Editar" aria-label="Editar">
                <i class="fas fa-pen"></i>
              </a>
              <button class="row-btn danger" title="Excluir" aria-label="Excluir" onclick="excluir(<?= $vid->id ?>)">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="video-empty no-results" id="no-results">
          <i class="fas fa-magnifying-glass"></i>
          <p>Nenhum vídeo encontrado com esses filtros.</p>
        </div>
      </div>
    </div>
    <div class="lib-foot" id="lib-foot"><?= count($videos) ?> vídeo<?= count($videos) !== 1 ? "s" : "" ?></div>
    <?php else : ?>
    <div class="video-empty">
      <i class="fas fa-film"></i>
      <p>Você ainda não enviou nenhum vídeo.</p>
      <button class="btn-wt" onclick="toggleUpload()">
        <i class="fas fa-plus"></i> Enviar primeiro vídeo
      </button>
    </div>
    <?php endif; ?>
  </div>

  <!-- Tab: Pastas -->
  <div id="panel-biblioteca" class="biblioteca-wrap" style="display:none;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
      <div class="wt-card-title" style="margin:0;">Suas pastas</div>
      <button class="btn-wt btn-wt-sm btn-wt-ghost" onclick="abrirNovaPasta()">
        <i class="fas fa-plus"></i> Nova pasta
      </button>
    </div>

    <?php if ($pastas) : ?>
    <div class="pasta-grid" id="pasta-grid">
      <?php foreach ($pastas as $p) :
        $cnt = $countPorPasta[(int)$p->id] ?? 0;
      ?>
      <div class="pasta-card" id="pasta-card-<?= $p->id ?>" onclick="abrirPasta(<?= $p->id ?>, '<?= htmlspecialchars(addslashes($p->nome), ENT_QUOTES) ?>')">
        <button class="pasta-del" onclick="event.stopPropagation();excluirPasta(<?= $p->id ?>)" title="Excluir pasta" aria-label="Excluir pasta">
          <i class="fas fa-xmark"></i>
        </button>
        <div class="pasta-icon"><i class="fas fa-folder"></i></div>
        <div class="pasta-name"><?= htmlspecialchars($p->nome, ENT_QUOTES) ?></div>
        <div class="pasta-count"><?= $cnt ?> vídeo<?= $cnt !== 1 ? "s" : "" ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else : ?>
    <div class="video-empty" style="padding:2.5rem 1rem;">
      <i class="fas fa-folder-open"></i>
      <p>Nenhuma pasta criada ainda.</p>
      <button class="btn-wt btn-wt-sm" onclick="abrirNovaPasta()">
        <i class="fas fa-plus"></i> Criar pasta
      </button>
    </div>
    <?php endif; ?>

    <!-- Vídeos dentro da pasta selecionada -->
    <div class="pasta-videos-wrap" id="pasta-videos-wrap">
      <div class="pasta-videos-header">
        <span class="pasta-back" onclick="fecharPasta()"><i class="fas fa-chevron-left"></i></span>
        <i class="fas fa-folder" style="color:var(--accent);"></i>
        <strong id="pasta-nome-label"></strong>
      </div>
      <div class="lib-panel" style="box-shadow:none;">
        <div class="table-scroll"><div class="table-inner">
          <div id="pasta-video-list"></div>
        </div></div>
      </div>
    </div>
  </div>
</div>

<?php $v->start("scripts") ?>
<script>
const uploadUrl  = <?= json_encode(url("videos/upload")) ?>;
const excluirUrl = <?= json_encode(url("videos/excluir")) ?>;
const criarUrl   = <?= json_encode(url("pastas/criar")) ?>;
const excluirPastaUrl = <?= json_encode(url("pastas/excluir")) ?>;
const moverUrl   = <?= json_encode(url("pastas/mover")) ?>;
const editarBase = <?= json_encode(url("videos/editar/")) ?>;

var embedBase    = <?= json_encode(url("player/")) ?>;
var metricasBase = <?= json_encode(url("metricas/")) ?>;

// ── Upload ─────────────────────────────────────────────────
function toggleUpload(forcar) {
  var p = document.getElementById('upload-panel');
  var open = forcar === true ? false : p.style.display === 'block';
  p.style.display = open ? 'none' : 'block';
  var btn = document.getElementById('btn-novo-video');
  btn.innerHTML = open ? '<i class="fas fa-plus"></i> Novo vídeo' : '<i class="fas fa-times"></i> Fechar';
}

var dropZone = document.getElementById("drop-zone");
var fileInput= document.getElementById("file-input");
var progWrap = document.getElementById("upload-progress");
var barFill  = document.getElementById("upload-bar-fill");
var pctTxt   = document.getElementById("upload-pct");
var nameTxt  = document.getElementById("upload-name");

if (location.hash === '#novo') toggleUpload(true);

dropZone.addEventListener("dragover",  function(e) { e.preventDefault(); dropZone.classList.add("over"); });
dropZone.addEventListener("dragleave", function()  { dropZone.classList.remove("over"); });
dropZone.addEventListener("drop",      function(e) { e.preventDefault(); dropZone.classList.remove("over"); if (e.dataTransfer.files[0]) enviar(e.dataTransfer.files[0]); });
fileInput.addEventListener("change",   function()  { if (fileInput.files[0]) enviar(fileInput.files[0]); });

function enviar(file) {
  nameTxt.textContent = file.name;
  progWrap.style.display = "block";
  barFill.style.width = "0%";
  pctTxt.textContent  = "0%";
  var fd = new FormData();
  fd.append("arquivo", file);
  fd.append("titulo",  file.name.replace(/\.[^.]+$/, ""));
  var xhr = new XMLHttpRequest();
  xhr.open("POST", uploadUrl);
  xhr.upload.addEventListener("progress", function(e) {
    if (e.lengthComputable) { var p = Math.round(e.loaded / e.total * 100); barFill.style.width = p + "%"; pctTxt.textContent = p + "%"; }
  });
  xhr.onload = function() {
    try {
      var r = JSON.parse(xhr.responseText);
      if (r.ok) { wtToast("Vídeo enviado!", "ok"); setTimeout(function() { location.href = editarBase + r.id; }, 900); }
      else { wtToast(r.msg || "Erro no upload.", "err"); progWrap.style.display = "none"; }
    } catch(e) { wtToast("Resposta inválida.", "err"); }
  };
  xhr.onerror = function() { wtToast("Erro na conexão.", "err"); };
  xhr.send(fd);
}

// ── Tabs ────────────────────────────────────────────────────
var currentTab = 'recentes';
function switchTab(tab) {
  currentTab = tab;
  ['recentes','biblioteca','top'].forEach(function(t) {
    var b = document.getElementById('tab-btn-' + t);
    b.classList.toggle('active', t === tab);
    b.setAttribute('aria-selected', t === tab);
  });
  var lista = tab === 'recentes' || tab === 'top';
  document.getElementById('panel-recentes').style.display   = lista ? '' : 'none';
  document.getElementById('panel-biblioteca').style.display = lista ? 'none' : '';
  var filtros = document.getElementById('lib-filters');
  if (filtros) filtros.style.display = lista ? '' : 'none';

  if (lista) {
    var rows = Array.from(document.querySelectorAll('#video-list .video-row'));
    if (tab === 'top') {
      rows.sort(function(a, b) { return parseInt(b.dataset.plays||0) - parseInt(a.dataset.plays||0); });
    } else {
      rows.sort(function(a, b) { return a.dataset.created < b.dataset.created ? 1 : -1; });
    }
    var list = document.getElementById('video-list');
    if (list) rows.forEach(function(r) { list.appendChild(r); });
  }
}

// ── Busca + filtro de pasta ─────────────────────────────────
function aplicarFiltros() {
  var q = document.getElementById('search-input').value.toLowerCase().trim();
  var sel = document.getElementById('filtro-pasta');
  var pasta = sel ? sel.value : '';
  var visiveis = 0;
  document.querySelectorAll('#video-list .video-row').forEach(function(row) {
    var ok = (!q || row.dataset.titulo.includes(q)) && (pasta === '' || row.dataset.pasta === pasta);
    row.style.display = ok ? '' : 'none';
    if (ok) visiveis++;
  });
  var nr = document.getElementById('no-results');
  if (nr) nr.style.display = visiveis === 0 ? 'block' : 'none';
  var foot = document.getElementById('lib-foot');
  if (foot) foot.textContent = visiveis + (visiveis === 1 ? ' vídeo' : ' vídeos');
}
document.getElementById('search-input').addEventListener('input', aplicarFiltros);
var filtroPasta = document.getElementById('filtro-pasta');
if (filtroPasta) filtroPasta.addEventListener('change', aplicarFiltros);

// ── Excluir vídeo ────────────────────────────────────────────
function excluir(id) {
  if (!confirm("Excluir este vídeo permanentemente?")) return;
  fetch(excluirUrl, {
    method: "POST", headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "id=" + id,
  }).then(function(r) { return r.json(); }).then(function(r) {
    if (r.ok) { document.getElementById("row-" + id)?.remove(); aplicarFiltros(); wtToast("Vídeo excluído.", "ok"); }
    else wtToast("Erro ao excluir.", "err");
  });
}

// ── Copiar embed ─────────────────────────────────────────────
function copiarEmbed(embedUrl, w, h) {
  var code = '<iframe src="' + embedUrl + '" width="' + w + '" height="' + h + '" frameborder="0" allowfullscreen allow="autoplay; fullscreen"></iframe>';
  navigator.clipboard.writeText(code).then(function() { wtToast("Embed copiado!", "ok"); });
}

// ── Move dropdown (movido para o body para nunca ser cortado) ──
function toggleMoveDropdown(btn, id) {
  var dd = document.getElementById('move-' + id);
  var wasOpen = dd.classList.contains('open');
  // Fecha todos
  document.querySelectorAll('.move-dropdown.open').forEach(function(d) { d.classList.remove('open'); });
  if (wasOpen) return;

  // Posiciona em coordenadas de página a partir do botão
  var rect = btn.getBoundingClientRect();
  dd.style.top  = (rect.bottom + 6 + window.scrollY) + 'px';
  dd.style.right = (document.documentElement.clientWidth - rect.right) + 'px';
  dd.style.left  = 'auto';
  document.body.appendChild(dd); // move para body para evitar qualquer clipping
  dd.classList.add('open');
}
document.addEventListener('click', function(e) {
  if (!e.target.closest('.row-btn') && !e.target.closest('.move-dropdown')) {
    document.querySelectorAll('.move-dropdown.open').forEach(function(d) { d.classList.remove('open'); });
  }
});

function moverVideo(videoId, pastaId) {
  document.querySelectorAll('.move-dropdown.open').forEach(function(d) { d.classList.remove('open'); });
  var fd = new FormData();
  fd.append('video_id', videoId);
  if (pastaId) fd.append('pasta_id', pastaId);
  fetch(moverUrl, { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(r) {
      if (r.ok) { wtToast("Vídeo movido!", "ok"); setTimeout(function() { location.reload(); }, 700); }
      else wtToast("Erro ao mover.", "err");
    });
}

// ── Pasta CRUD ────────────────────────────────────────────────
function abrirNovaPasta() {
  var nome = prompt("Nome da pasta:");
  if (!nome || !nome.trim()) return;
  var fd = new FormData();
  fd.append('nome', nome.trim());
  fetch(criarUrl, { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(r) {
      if (r.ok) { wtToast("Pasta criada!", "ok"); setTimeout(function() { location.reload(); }, 600); }
      else wtToast(r.msg || "Erro ao criar pasta.", "err");
    });
}

function excluirPasta(id) {
  if (!confirm("Excluir esta pasta? Os vídeos não serão apagados.")) return;
  var fd = new FormData();
  fd.append('id', id);
  fetch(excluirPastaUrl, { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(r) {
      if (r.ok) { wtToast("Pasta removida!", "ok"); setTimeout(function() { location.reload(); }, 600); }
      else wtToast("Erro ao excluir.", "err");
    });
}

// ── Abrir pasta (mostra vídeos filtrados) ────────────────────
var currentPastaId = null;
function abrirPasta(id, nome) {
  currentPastaId = id;
  document.querySelectorAll('.pasta-card').forEach(function(c) { c.classList.remove('active-pasta'); });
  var card = document.getElementById('pasta-card-' + id);
  if (card) card.classList.add('active-pasta');

  document.getElementById('pasta-nome-label').textContent = nome;
  var list = document.getElementById('pasta-video-list');
  list.innerHTML = '';
  var cab = document.querySelector('.video-table-head');
  if (cab) list.appendChild(cab.cloneNode(true));
  var rows = document.querySelectorAll('#video-list .video-row');
  var achou = 0;
  rows.forEach(function(row) {
    if (parseInt(row.dataset.pasta, 10) !== id) return;
    var c = row.cloneNode(true);
    c.removeAttribute('id');
    c.style.display = '';
    // ações que dependem do estado da lista principal ficam só lá
    var mover = c.querySelector('.move-dropdown');
    if (mover) mover.parentElement.remove();
    var del = c.querySelector('.row-btn.danger');
    if (del) del.remove();
    list.appendChild(c);
    achou++;
  });
  if (!achou) {
    list.innerHTML = '<div class="video-empty" style="padding:2rem;"><i class="fas fa-film"></i><p>Nenhum vídeo nesta pasta.</p></div>';
  }

  document.getElementById('pasta-videos-wrap').classList.add('show');
  document.getElementById('pasta-grid').style.opacity = '.45';
}

function fecharPasta() {
  currentPastaId = null;
  document.querySelectorAll('.pasta-card').forEach(function(c) { c.classList.remove('active-pasta'); });
  document.getElementById('pasta-videos-wrap').classList.remove('show');
  document.getElementById('pasta-grid').style.opacity = '1';
}

function focusPasta(id) {
  var card = document.getElementById('pasta-card-' + id);
  if (card) { card.scrollIntoView({ behavior: 'smooth', block: 'center' }); card.click(); }
}

</script>
<?php $v->end() ?>

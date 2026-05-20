<?php $v->layout("_theme") ?>

<?php $v->start("styles") ?>
<style>
/* ── Upload panel ─────────────────────────────────────── */
#upload-panel {
  display: none;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.3rem;
  margin-bottom: 1.1rem;
  box-shadow: var(--shadow);
  animation: wt-in .22s cubic-bezier(.22,1,.36,1);
}
#drop-zone {
  border: 2px dashed rgba(108,92,231,.22);
  border-radius: 10px;
  padding: 2rem 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: border-color .18s, background .18s;
  background: rgba(108,92,231,.025);
}
#drop-zone:hover, #drop-zone.over {
  border-color: rgba(108,92,231,.5);
  background: rgba(108,92,231,.06);
}
#drop-zone .drop-icon {
  font-size: 1.7rem; color: var(--accent); opacity: .65;
  display: block; margin-bottom: .55rem;
  transition: transform .2s, opacity .2s;
}
#drop-zone:hover .drop-icon { opacity: 1; transform: translateY(-3px); }
#upload-progress { display: none; margin-top: .85rem; }
#upload-bar-track {
  height: 3px; background: var(--bg3);
  border-radius: 99px; overflow: hidden;
}
#upload-bar-fill {
  height: 100%;
  background: var(--accent);
  width: 0%; transition: width .2s;
}

/* ── Header ────────────────────────────────────────────── */
.lib-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.1rem; flex-wrap: wrap; gap: .7rem;
}
.lib-search {
  display: flex; align-items: center; gap: .4rem;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 9px;
  padding: .42rem .85rem;
  flex: 1; max-width: 260px;
  box-shadow: var(--shadow);
  transition: border-color .15s, box-shadow .15s;
}
.lib-search:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,92,231,.1); }
.lib-search i { color: var(--muted); font-size: .82rem; }
.lib-search input {
  background: none; border: none; outline: none;
  color: var(--text); font-family: 'Outfit', sans-serif;
  font-size: .85rem; width: 100%;
}
.lib-search input::placeholder { color: var(--muted); }

/* ── Tabs ───────────────────────────────────────────────── */
.lib-tabs {
  display: flex; gap: 0;
  border-bottom: 1px solid var(--border);
}
.lib-tab {
  display: flex; align-items: center; gap: .38rem;
  padding: .65rem 1.05rem;
  font-size: .83rem; font-weight: 600;
  color: var(--muted);
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
  transition: color .15s, border-color .15s;
  user-select: none;
}
.lib-tab:hover { color: var(--text); }
.lib-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
.tab-badge {
  background: var(--accent-soft);
  border: 1px solid var(--accent-border);
  color: var(--accent);
  font-size: .62rem; font-weight: 700;
  padding: .06rem .38rem; border-radius: 99px;
}

/* ── Video table ────────────────────────────────────────── */
.video-table-wrap {
  background: #fff;
  border: 1px solid var(--border);
  border-top: none;
  border-radius: 0 0 var(--radius) var(--radius);
  overflow: visible;
  box-shadow: var(--shadow);
}
.video-table-head {
  display: grid;
  grid-template-columns: 24px 52px 1fr 120px 140px;
  gap: .7rem; align-items: center;
  padding: .6rem 1.1rem;
  border-bottom: 1px solid var(--border);
  background: var(--bg);
}
.video-table-head span {
  font-size: .66rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .1em;
  color: var(--muted);
}
.video-row {
  display: grid;
  grid-template-columns: 24px 52px 1fr 120px 140px;
  gap: .7rem; align-items: center;
  padding: .7rem 1.1rem;
  border-bottom: 1px solid var(--border);
  transition: background .12s;
}
.video-row:last-child { border-bottom: none; }
.video-row:hover { background: var(--bg); }

/* Thumbnail */
.video-thumb {
  width: 52px; height: 38px;
  border-radius: 6px; overflow: hidden;
  background: var(--bg3);
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.video-thumb img { width: 100%; height: 100%; object-fit: cover; }
.video-thumb i { font-size: .72rem; color: var(--muted); opacity: .35; }

/* Name */
.video-name a {
  font-size: .87rem; font-weight: 500; color: var(--text);
  text-decoration: none; transition: color .12s;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  display: block;
}
.video-name a:hover { color: var(--accent); }
.video-pasta-tag {
  display: inline-flex; align-items: center; gap: .3rem;
  font-size: .68rem; color: var(--muted);
  margin-top: .1rem; cursor: pointer;
  transition: color .12s;
}
.video-pasta-tag:hover { color: var(--accent); }

/* Plays */
.plays-val {
  font-family: 'Syne', sans-serif;
  font-size: .92rem; font-weight: 700; color: var(--text);
}
.plays-sub { font-size: .65rem; color: var(--muted); }

/* Actions */
.row-actions {
  display: flex; align-items: center; gap: .28rem;
  justify-content: flex-end;
}
.row-btn {
  width: 29px; height: 29px; border-radius: 7px;
  border: 1px solid var(--border);
  background: #fff;
  color: var(--muted); font-size: .76rem;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: background .12s, color .12s, border-color .12s, box-shadow .12s;
  text-decoration: none; position: relative;
  box-shadow: 0 1px 2px rgba(0,0,0,.05);
}
.row-btn:hover { background: var(--accent-soft); color: var(--accent); border-color: var(--accent-border); }
.row-btn.danger:hover { background: #fee2e2; color: #dc2626; border-color: rgba(239,68,68,.3); }

/* Checkbox */
.row-check {
  appearance: none; -webkit-appearance: none;
  width: 15px; height: 15px;
  border: 1.5px solid var(--border);
  border-radius: 4px; background: transparent;
  cursor: pointer; transition: border-color .15s, background .15s;
  flex-shrink: 0;
}
.row-check:checked { background: var(--accent); border-color: var(--accent); }

/* Empty */
.video-empty {
  text-align: center; padding: 4rem 2rem; color: var(--muted);
}
.video-empty i { font-size: 2.5rem; opacity: .1; display: block; margin-bottom: 1rem; }

/* ── Biblioteca ──────────────────────────────────────────── */
.biblioteca-wrap {
  background: #fff;
  border: 1px solid var(--border);
  border-top: none;
  border-radius: 0 0 var(--radius) var(--radius);
  padding: 1.4rem;
  box-shadow: var(--shadow);
}
.pasta-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
  gap: .9rem;
  margin-top: 1rem;
}
.pasta-card {
  background: var(--bg3);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 1.1rem 1rem;
  cursor: pointer;
  transition: border-color .15s, background .15s, transform .1s;
  display: flex; flex-direction: column; gap: .5rem;
  position: relative;
}
.pasta-card:hover { border-color: var(--accent-border); background: var(--accent-soft); transform: translateY(-1px); box-shadow: var(--shadow-md); }
.pasta-card.active-pasta { border-color: var(--accent); background: var(--accent-soft); }
.pasta-icon {
  width: 36px; height: 36px;
  border-radius: 9px;
  background: var(--accent-soft);
  border: 1px solid var(--accent-border);
  display: flex; align-items: center; justify-content: center;
  color: var(--accent); font-size: .9rem;
}
.pasta-name { font-size: .87rem; font-weight: 600; color: var(--text); }
.pasta-count { font-size: .72rem; color: var(--muted); }
.pasta-del {
  position: absolute; top: .5rem; right: .5rem;
  width: 22px; height: 22px; border-radius: 5px;
  background: none; border: none;
  color: var(--muted); font-size: .72rem; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity .15s, color .15s;
}
.pasta-card:hover .pasta-del { opacity: 1; }
.pasta-del:hover { color: #fca5a5; }

.pasta-videos-wrap {
  margin-top: 1.2rem; display: none;
}
.pasta-videos-wrap.show { display: block; }
.pasta-videos-header {
  display: flex; align-items: center; gap: .6rem;
  margin-bottom: .8rem;
  font-size: .82rem; color: var(--muted);
}
.pasta-videos-header strong { color: var(--text); }
.pasta-back { cursor: pointer; }
.pasta-back:hover { color: var(--accent); }

/* Dropdown mover para pasta */
.move-dropdown {
  position: fixed;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 11px;
  min-width: 190px;
  z-index: 9000;
  padding: .4rem;
  display: none;
  box-shadow: 0 4px 24px rgba(0,0,0,.12), 0 1px 4px rgba(0,0,0,.06);
  animation: dropdown-in .15s cubic-bezier(.22,1,.36,1);
}
@keyframes dropdown-in {
  from { opacity: 0; transform: translateY(-6px) scale(.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.move-dropdown.open { display: block; }
.move-drop-title {
  font-size: .62rem; font-weight: 700; letter-spacing: .11em;
  text-transform: uppercase; color: var(--muted);
  padding: .35rem .7rem .2rem;
}
.move-opt {
  display: flex; align-items: center; gap: .55rem;
  padding: .5rem .7rem;
  border-radius: 7px;
  font-size: .83rem; font-weight: 500; color: var(--text);
  cursor: pointer; transition: background .1s, color .1s;
}
.move-opt:hover { background: var(--accent-soft); color: var(--accent); }
.move-opt i { width: 15px; text-align: center; font-size: .78rem; color: var(--accent); opacity: .75; flex-shrink: 0; }
</style>
<?php $v->end() ?>

<?php
$videosTop = $videos;
if ($videosTop) {
    usort($videosTop, fn($a, $b) => ($playsMap[$b->id] ?? 0) - ($playsMap[$a->id] ?? 0));
}
// Contagem de vídeos por pasta
$countPorPasta = [];
foreach ($videos as $vid) {
    if ($vid->pasta_id) {
        $countPorPasta[(int)$vid->pasta_id] = ($countPorPasta[(int)$vid->pasta_id] ?? 0) + 1;
    }
}
$semPastaCount = count(array_filter($videos, fn($v) => !$v->pasta_id));
?>

<!-- ── Header ── -->
<div class="lib-header">
  <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
    <button class="btn-wt btn-wt-sm" id="btn-novo-video" onclick="toggleUpload()">
      <i class="fas fa-plus"></i> Novo vídeo
    </button>
    <button class="btn-wt btn-wt-sm btn-wt-ghost" onclick="abrirNovaPasta()">
      <i class="fas fa-folder-plus"></i> Nova pasta
    </button>
    <span style="font-size:.77rem;color:var(--muted);">
      <?= count($videos) ?> vídeo<?= count($videos) !== 1 ? 's' : '' ?>
    </span>
  </div>
  <div class="lib-search">
    <i class="fas fa-magnifying-glass"></i>
    <input type="text" id="search-input" placeholder="Buscar…">
  </div>
</div>

<!-- ── Upload panel ── -->
<div id="upload-panel">
  <div id="drop-zone" onclick="document.getElementById('file-input').click()">
    <i class="fas fa-cloud-arrow-up drop-icon"></i>
    <p style="font-size:.87rem;font-weight:500;color:var(--text);margin-bottom:.2rem;">Clique ou arraste um vídeo aqui</p>
    <p style="font-size:.75rem;color:var(--muted);">MP4, WebM, MOV, MKV &mdash; máx. 2 GB</p>
  </div>
  <input type="file" id="file-input" accept=".mp4,.webm,.ogg,.mov,.mkv" style="display:none;">
  <div id="upload-progress">
    <div style="display:flex;justify-content:space-between;font-size:.74rem;color:var(--muted);margin-bottom:.32rem;">
      <span id="upload-name" style="font-family:'JetBrains Mono',monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:72%;"></span>
      <span id="upload-pct" style="color:var(--accent);font-weight:700;">0%</span>
    </div>
    <div id="upload-bar-track"><div id="upload-bar-fill"></div></div>
  </div>
</div>

<!-- ── Tabs ── -->
<div class="lib-tabs">
  <div class="lib-tab active" onclick="switchTab('recentes')" id="tab-btn-recentes">
    <i class="fas fa-film"></i> Meus vídeos
    <?php if ($videos) : ?><span class="tab-badge"><?= count($videos) ?></span><?php endif; ?>
  </div>
  <div class="lib-tab" onclick="switchTab('biblioteca')" id="tab-btn-biblioteca">
    <i class="fas fa-folder"></i> Biblioteca
    <?php if ($pastas) : ?><span class="tab-badge"><?= count($pastas) ?></span><?php endif; ?>
  </div>
  <div class="lib-tab" onclick="switchTab('top')" id="tab-btn-top">
    <i class="fas fa-fire"></i> Top vídeos
  </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- Tab: Meus vídeos + Top vídeos (mesma tabela, ordenação diferente) -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="panel-recentes" class="video-table-wrap">
  <?php if ($videos) : ?>
  <div class="video-table-head">
    <span></span><span></span>
    <span>Nome</span>
    <span>Plays (30d)</span>
    <span style="text-align:right;">Ações</span>
  </div>
  <div id="video-list">
    <?php foreach ($videos as $vid) :
      $plays    = $playsMap[$vid->id] ?? 0;
      $thumbUrl = $vid->capa ? url("uploads/capas/{$vid->capa}") : null;
      $pasta    = $vid->pasta_id ? ($pastaMap[(int)$vid->pasta_id] ?? null) : null;
    ?>
    <div class="video-row" id="row-<?= $vid->id ?>"
         data-titulo="<?= htmlspecialchars(strtolower($vid->titulo), ENT_QUOTES) ?>"
         data-plays="<?= $plays ?>"
         data-pasta="<?= (int)($vid->pasta_id ?? 0) ?>"
         data-created="<?= $vid->created_at ?>">
      <input type="checkbox" class="row-check">
      <div class="video-thumb">
        <?php if ($thumbUrl) : ?>
          <img src="<?= htmlspecialchars($thumbUrl, ENT_QUOTES) ?>" alt="">
        <?php else : ?>
          <i class="fas fa-film"></i>
        <?php endif; ?>
      </div>
      <div style="min-width:0;">
        <div class="video-name">
          <a href="<?= url("videos/editar/{$vid->id}") ?>">
            <?= htmlspecialchars($vid->titulo, ENT_QUOTES) ?>
          </a>
        </div>
        <?php if ($pasta) : ?>
          <span class="video-pasta-tag" onclick="switchTab('biblioteca');focusPasta(<?= (int)$vid->pasta_id ?>)">
            <i class="fas fa-folder" style="font-size:.6rem;color:var(--accent);opacity:.7;"></i>
            <?= htmlspecialchars($pasta, ENT_QUOTES) ?>
          </span>
        <?php endif; ?>
      </div>
      <div>
        <div class="plays-val"><?= number_format($plays) ?></div>
        <div class="plays-sub">plays</div>
      </div>
      <div class="row-actions">
        <a href="<?= url("metricas/{$vid->id}") ?>" class="row-btn" title="Métricas">
          <i class="fas fa-chart-bar"></i>
        </a>
        <button class="row-btn" title="Copiar embed"
          onclick="copiarEmbed('<?= htmlspecialchars(url("player/{$vid->embed_token}"), ENT_QUOTES) ?>',<?= (int)$vid->largura ?>,<?= (int)$vid->altura ?>)">
          <i class="fas fa-code"></i>
        </button>
        <div style="position:relative;">
          <button class="row-btn" title="Mover para pasta"
            onclick="toggleMoveDropdown(this, <?= $vid->id ?>)">
            <i class="fas fa-folder-open"></i>
          </button>
          <div class="move-dropdown" id="move-<?= $vid->id ?>">
            <div class="move-drop-title">Mover para pasta</div>
            <div class="move-opt" onclick="moverVideo(<?= $vid->id ?>, null)">
              <i class="fas fa-x"></i> Sem pasta
            </div>
            <?php foreach ($pastas as $p) : ?>
            <div class="move-opt" onclick="moverVideo(<?= $vid->id ?>, <?= $p->id ?>)">
              <i class="fas fa-folder"></i> <?= htmlspecialchars($p->nome, ENT_QUOTES) ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <a href="<?= url("videos/editar/{$vid->id}") ?>" class="row-btn" title="Editar">
          <i class="fas fa-pen"></i>
        </a>
        <button class="row-btn danger" title="Excluir" onclick="excluir(<?= $vid->id ?>)">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <div class="video-empty">
    <i class="fas fa-film"></i>
    <p>Nenhum vídeo ainda. Envie o primeiro!</p>
    <button class="btn-wt" onclick="toggleUpload()">
      <i class="fas fa-plus"></i> Enviar vídeo
    </button>
  </div>
  <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- Tab: Biblioteca -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="panel-biblioteca" class="video-table-wrap" style="display:none;padding:1.4rem;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
    <div class="wt-card-title" style="margin:0;">
      <i class="fas fa-folder" style="margin-right:.4rem;color:var(--accent);opacity:.7;"></i>Pastas
    </div>
    <button class="btn-wt btn-wt-sm btn-wt-ghost" onclick="abrirNovaPasta()">
      <i class="fas fa-plus"></i> Nova pasta
    </button>
  </div>

  <?php if ($pastas) : ?>
  <div class="pasta-grid" id="pasta-grid">
    <?php foreach ($pastas as $p) :
      $cnt = $countPorPasta[(int)$p->id] ?? 0;
    ?>
    <div class="pasta-card" id="pasta-card-<?= $p->id ?>" onclick="abrirPasta(<?= $p->id ?>, '<?= htmlspecialchars($p->nome, ENT_QUOTES) ?>')">
      <button class="pasta-del" onclick="event.stopPropagation();excluirPasta(<?= $p->id ?>)" title="Excluir pasta">
        <i class="fas fa-times"></i>
      </button>
      <div class="pasta-icon"><i class="fas fa-folder"></i></div>
      <div class="pasta-name"><?= htmlspecialchars($p->nome, ENT_QUOTES) ?></div>
      <div class="pasta-count"><?= $cnt ?> vídeo<?= $cnt !== 1 ? 's' : '' ?></div>
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
      <i class="fas fa-folder" style="color:var(--accent);opacity:.7;"></i>
      <strong id="pasta-nome-label"></strong>
    </div>
    <div class="video-table-wrap" style="border-radius:var(--radius);">
      <div class="video-table-head">
        <span></span><span></span>
        <span>Nome</span>
        <span>Plays (30d)</span>
        <span style="text-align:right;">Ações</span>
      </div>
      <div id="pasta-video-list"></div>
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

// Todos os vídeos como JSON para renderização na biblioteca
var TODOS_VIDEOS = <?= json_encode(array_map(function($vid) use ($playsMap, $pastaMap) {
  return [
    'id'          => (int)$vid->id,
    'titulo'      => $vid->titulo,
    'pasta_id'    => $vid->pasta_id ? (int)$vid->pasta_id : null,
    'pasta_nome'  => $vid->pasta_id ? ($pastaMap[(int)$vid->pasta_id] ?? '') : '',
    'plays'       => $playsMap[$vid->id] ?? 0,
    'capa'        => $vid->capa,
    'largura'     => (int)$vid->largura,
    'altura'      => (int)$vid->altura,
    'embedToken'  => $vid->embed_token,
    'created_at'  => $vid->created_at,
  ];
}, $videos), JSON_UNESCAPED_UNICODE) ?>;

var embedBase    = <?= json_encode(url("player/")) ?>;
var metricasBase = <?= json_encode(url("metricas/")) ?>;

// ── Upload ─────────────────────────────────────────────────
function toggleUpload() {
  var p = document.getElementById('upload-panel');
  var open = p.style.display === 'block';
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
    document.getElementById('tab-btn-' + t).classList.remove('active');
  });
  document.getElementById('tab-btn-' + tab).classList.add('active');
  document.getElementById('panel-recentes').style.display  = 'none';
  document.getElementById('panel-biblioteca').style.display= 'none';

  if (tab === 'recentes' || tab === 'top') {
    document.getElementById('panel-recentes').style.display = '';
    var rows = Array.from(document.querySelectorAll('.video-row'));
    if (tab === 'top') {
      rows.sort(function(a, b) { return parseInt(b.dataset.plays||0) - parseInt(a.dataset.plays||0); });
    } else {
      rows.sort(function(a, b) { return a.dataset.created < b.dataset.created ? 1 : -1; });
    }
    var list = document.getElementById('video-list');
    rows.forEach(function(r) { list.appendChild(r); });
  } else {
    document.getElementById('panel-biblioteca').style.display = '';
  }
}

// ── Search ──────────────────────────────────────────────────
document.getElementById('search-input').addEventListener('input', function() {
  var q = this.value.toLowerCase().trim();
  document.querySelectorAll('.video-row').forEach(function(row) {
    row.style.display = (!q || row.dataset.titulo.includes(q)) ? '' : 'none';
  });
});

// ── Excluir vídeo ────────────────────────────────────────────
function excluir(id) {
  if (!confirm("Excluir este vídeo permanentemente?")) return;
  fetch(excluirUrl, {
    method: "POST", headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "id=" + id,
  }).then(function(r) { return r.json(); }).then(function(r) {
    if (r.ok) { document.getElementById("row-" + id)?.remove(); wtToast("Vídeo excluído.", "ok"); }
    else wtToast("Erro ao excluir.", "err");
  });
}

// ── Copiar embed ─────────────────────────────────────────────
function copiarEmbed(embedUrl, w, h) {
  var code = '<iframe src="' + embedUrl + '" width="' + w + '" height="' + h + '" frameborder="0" allowfullscreen allow="autoplay; fullscreen"></iframe>';
  navigator.clipboard.writeText(code).then(function() { wtToast("Embed copiado!", "ok"); });
}

// ── Move dropdown (position:fixed para nunca ser cortado) ─────
function toggleMoveDropdown(btn, id) {
  var dd = document.getElementById('move-' + id);
  var wasOpen = dd.classList.contains('open');
  // Fecha todos
  document.querySelectorAll('.move-dropdown.open').forEach(function(d) { d.classList.remove('open'); });
  if (wasOpen) return;

  // Posiciona via getBoundingClientRect (fixed viewport coords)
  var rect = btn.getBoundingClientRect();
  dd.style.top  = (rect.bottom + 6 + window.scrollY) + 'px';
  dd.style.right = (window.innerWidth - rect.right) + 'px';
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
  var vids = TODOS_VIDEOS.filter(function(v) { return v.pasta_id === id; });

  if (!vids.length) {
    list.innerHTML = '<div class="video-empty" style="padding:2rem;"><i class="fas fa-film"></i><p>Nenhum vídeo nesta pasta.</p></div>';
  } else {
    list.innerHTML = vids.map(function(v) {
      return '<div class="video-row">' +
        '<input type="checkbox" class="row-check">' +
        '<div class="video-thumb"><i class="fas fa-film"></i></div>' +
        '<div style="min-width:0;">' +
          '<div class="video-name"><a href="' + editarBase + v.id + '">' + escHtml(v.titulo) + '</a></div>' +
        '</div>' +
        '<div><div class="plays-val">' + v.plays.toLocaleString() + '</div><div class="plays-sub">plays</div></div>' +
        '<div class="row-actions">' +
          '<a href="' + metricasBase + v.id + '" class="row-btn" title="Métricas"><i class="fas fa-chart-bar"></i></a>' +
          '<a href="' + editarBase + v.id + '" class="row-btn" title="Editar"><i class="fas fa-pen"></i></a>' +
        '</div>' +
      '</div>';
    }).join('');
  }

  document.getElementById('pasta-videos-wrap').classList.add('show');
  document.getElementById('pasta-grid').style.opacity = '.4';
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

function escHtml(str) {
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
<?php $v->end() ?>

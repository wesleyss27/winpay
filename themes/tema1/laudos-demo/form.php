<?php $v->layout("_theme", ["title" => "Editor de Laudo — Concentre PF"]); ?>

<style>
#pdf-viewer-wrap {
  background: #525659;
  border-radius: 4px;
  padding: 20px;
  text-align: center;
  min-height: 200px;
  overflow-y: auto;
  max-height: 78vh;
}
#pdf-drop-area {
  border: 3px dashed #aaa;
  border-radius: 8px;
  padding: 50px 20px;
  color: #ccc;
  cursor: pointer;
  transition: border-color .2s, color .2s;
}
#pdf-drop-area:hover, #pdf-drop-area.drag-over { border-color: #4287f5; color: #fff; }
#pdf-drop-area input[type=file] { display:none; }

.page-wrapper { position:relative; display:inline-block; margin-bottom:20px; box-shadow:0 4px 16px rgba(0,0,0,.6); }
.page-canvas  { display:block; }
.text-layer   { position:absolute; top:0; left:0; overflow:hidden; pointer-events:none; }

/* spans editáveis */
.pt {
  position:absolute; white-space:pre; font-family:Arial,sans-serif;
  cursor:text; pointer-events:all; line-height:1; padding:1px 3px;
  border-radius:2px; background:transparent; color:transparent;
  border:none; outline:none; box-sizing:content-box;
}
.pt:hover  { background:rgba(66,135,245,.18); }
.pt:focus  { background:rgba(255,255,255,.97)!important; color:#111!important; outline:2px solid #4287f5!important; z-index:200!important; }
.pt.modified { background:rgba(255,230,80,.55); color:#111; }

/* mini-toolbar flutuante */
#mini-tb {
  display:none; position:fixed; z-index:9999;
  background:#2c2c2c; border-radius:6px;
  padding:3px 6px; box-shadow:0 2px 10px rgba(0,0,0,.6);
  align-items:center; gap:3px; user-select:none;
  white-space:nowrap;
}
#mini-tb .tb-sep { width:1px; height:18px; background:#555; display:inline-block; margin:0 2px; }
.tb-btn {
  background:none; border:none; color:#ccc; cursor:pointer;
  padding:3px 7px; border-radius:4px; font-size:13px; line-height:1;
}
.tb-btn:hover   { background:#444; color:#fff; }
.tb-btn.active  { background:#4287f5; color:#fff; }
#tb-move {
  cursor:move; padding:4px 6px; color:#aaa; font-size:17px; line-height:1;
}
#tb-move:hover { color:#fff; }
</style>

<!-- Mini-toolbar (posição fixa, aparece perto do span ativo) -->
<div id="mini-tb">
  <span id="tb-move" title="Arraste para mover">⠿</span>
  <span class="tb-sep"></span>
  <button class="tb-btn" id="tb-left"   title="Alinhar à esquerda"><i class="fas fa-align-left"></i></button>
  <button class="tb-btn" id="tb-center" title="Centralizar"><i class="fas fa-align-center"></i></button>
  <button class="tb-btn" id="tb-right"  title="Alinhar à direita"><i class="fas fa-align-right"></i></button>
  <span class="tb-sep"></span>
  <button class="tb-btn" id="tb-reset-span" title="Desfazer edição"><i class="fas fa-undo"></i></button>
</div>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1><i class="fas fa-file-pdf mr-2"></i>Editor de Laudo — Concentre PF</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url("") ?>">Início</a></li>
            <li class="breadcrumb-item active">Laudo Demo</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <!-- Barra de ação -->
      <div id="action-bar" class="card mb-3" style="display:none;">
        <div class="card-body py-2 d-flex align-items-center justify-content-between flex-wrap">
          <div class="text-muted" style="font-size:13px;">
            <i class="fas fa-info-circle mr-1 text-primary"></i>
            Clique em qualquer texto para editar.
            Texto em <span style="background:rgba(255,230,80,.75);padding:1px 5px;border-radius:2px;">amarelo</span> = modificado.
            Use a toolbar <b>⠿</b> para mover e alinhar.
          </div>
          <div class="d-flex" style="gap:8px;">
            <button id="btn-reset" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-undo mr-1"></i> Resetar tudo
            </button>
            <button id="btn-baixar" class="btn btn-success btn-sm">
              <i class="fas fa-download mr-1"></i> Baixar PDF
            </button>
          </div>
        </div>
        <div id="progress-wrap" style="display:none;padding:4px 16px 8px;">
          <div id="progress-msg" class="text-muted mb-1" style="font-size:12px;"></div>
          <div class="progress" style="height:6px;">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
              style="width:0%;transition:width .3s;"></div>
          </div>
        </div>
      </div>

      <!-- Visualizador -->
      <div id="pdf-viewer-wrap">
        <div id="pdf-drop-area" onclick="document.getElementById('pdf-file-input').click()">
          <input type="file" id="pdf-file-input" accept=".pdf">
          <i class="fas fa-file-pdf fa-3x mb-3"></i>
          <div style="font-size:18px;font-weight:600;">Clique ou arraste o PDF aqui</div>
          <div style="font-size:13px;margin-top:6px;">Selecione o arquivo Concentre PF original</div>
        </div>
        <div id="pdf-pages"></div>
      </div>

    </div>
  </section>
</div>

<?php $v->start("scripts"); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc =
  'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// ── Estado global ────────────────────────────────────────────────
let gPdfDoc      = null;
let gSpans       = [];
let activeSpan   = null;    // span atualmente focado
let tbBlurTimer  = null;    // timer para esconder toolbar no blur
let drag         = null;    // estado do drag

// ── Mini-toolbar ─────────────────────────────────────────────────
const tb = document.getElementById('mini-tb');

// Impede que clicar na toolbar tire o foco do span
tb.addEventListener('mousedown', e => e.preventDefault());

// Botões de alinhamento
['left','center','right'].forEach(a => {
  document.getElementById('tb-' + a).addEventListener('click', () => {
    if (!activeSpan) return;
    activeSpan.dataset.align = a;
    marcarModificado(activeSpan);
    atualizarBotoesAlign(a);
  });
});

// Botão de undo do span
document.getElementById('tb-reset-span').addEventListener('click', () => {
  if (!activeSpan) return;
  activeSpan.textContent    = activeSpan.dataset.original;
  activeSpan.dataset.align  = 'left';
  activeSpan.dataset.dragDx = '0';
  activeSpan.dataset.dragDy = '0';
  activeSpan.style.left     = activeSpan.dataset.origLeft + 'px';
  activeSpan.style.top      = activeSpan.dataset.origTop  + 'px';
  activeSpan.classList.remove('modified');
  activeSpan.style.background = '';
  activeSpan.style.color      = '';
  atualizarBotoesAlign('left');
});

function atualizarBotoesAlign(a) {
  ['left','center','right'].forEach(x =>
    document.getElementById('tb-' + x).classList.toggle('active', x === a)
  );
}

function mostrarToolbar(span) {
  clearTimeout(tbBlurTimer);
  activeSpan = span;
  const r = span.getBoundingClientRect();
  tb.style.display = 'flex';
  tb.style.left = Math.max(4, r.left) + 'px';
  tb.style.top  = (r.top - 36) + 'px';
  atualizarBotoesAlign(span.dataset.align || 'left');
}

function esconderToolbar() {
  tbBlurTimer = setTimeout(() => {
    tb.style.display = 'none';
    activeSpan = null;
  }, 150);
}

// ── Drag (handle ⠿) ─────────────────────────────────────────────
const tbMove = document.getElementById('tb-move');

tbMove.addEventListener('mousedown', e => {
  if (!activeSpan) return;
  e.preventDefault();
  drag = {
    span:   activeSpan,
    startX: e.clientX,
    startY: e.clientY,
    origLeft: parseFloat(activeSpan.dataset.origLeft),
    origTop:  parseFloat(activeSpan.dataset.origTop),
    prevDx:   parseFloat(activeSpan.dataset.dragDx || 0),
    prevDy:   parseFloat(activeSpan.dataset.dragDy || 0),
  };
});

document.addEventListener('mousemove', e => {
  if (!drag) return;
  const dx = drag.prevDx + (e.clientX - drag.startX);
  const dy = drag.prevDy + (e.clientY - drag.startY);
  drag.span.style.left = (drag.origLeft + dx) + 'px';
  drag.span.style.top  = (drag.origTop  + dy) + 'px';
  drag.span.dataset.dragDx = dx;
  drag.span.dataset.dragDy = dy;

  // Mover toolbar junto
  const r = drag.span.getBoundingClientRect();
  tb.style.left = Math.max(4, r.left) + 'px';
  tb.style.top  = (r.top - 36) + 'px';
  marcarModificado(drag.span);
});

document.addEventListener('mouseup', () => { drag = null; });

// ── Upload / Drop ────────────────────────────────────────────────
const dropArea = document.getElementById('pdf-drop-area');
const fileInput = document.getElementById('pdf-file-input');

dropArea.addEventListener('dragover',  e => { e.preventDefault(); dropArea.classList.add('drag-over'); });
dropArea.addEventListener('dragleave', () => dropArea.classList.remove('drag-over'));
dropArea.addEventListener('drop', e => {
  e.preventDefault();
  dropArea.classList.remove('drag-over');
  if (e.dataTransfer.files[0]) carregarPdf(e.dataTransfer.files[0]);
});
fileInput.addEventListener('change', () => {
  if (fileInput.files[0]) carregarPdf(fileInput.files[0]);
});

// ── Carregar e renderizar PDF ────────────────────────────────────
async function carregarPdf(file) {
  try {
    const bytes = await file.arrayBuffer();
    gPdfDoc = await pdfjsLib.getDocument({ data: new Uint8Array(bytes) }).promise;
    gSpans  = [];

    const pagesEl = document.getElementById('pdf-pages');
    pagesEl.innerHTML = '';
    dropArea.style.display = 'none';
    document.getElementById('action-bar').style.display = 'block';

    const viewerW = document.getElementById('pdf-viewer-wrap').clientWidth - 40;

    for (let pn = 1; pn <= gPdfDoc.numPages; pn++) {
      const page     = await gPdfDoc.getPage(pn);
      const naturalW = page.getViewport({ scale: 1 }).width;
      const ds       = Math.min(viewerW / naturalW, 1.6);  // display scale
      const viewport = page.getViewport({ scale: ds });

      const wrapper = document.createElement('div');
      wrapper.className = 'page-wrapper';

      const canvas = document.createElement('canvas');
      canvas.className = 'page-canvas';
      canvas.width  = viewport.width;
      canvas.height = viewport.height;
      wrapper.appendChild(canvas);
      await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

      const layer = document.createElement('div');
      layer.className = 'text-layer';
      layer.style.width  = viewport.width  + 'px';
      layer.style.height = viewport.height + 'px';
      wrapper.appendChild(layer);

      // Criar spans editáveis a partir dos grupos de texto
      const textContent = await page.getTextContent();
      const grupos = agruparItems(textContent.items);

      for (const g of grupos) {
        if (!g.text.trim()) continue;
        const [cx, cy] = viewport.convertToViewportPoint(g.pdfX, g.pdfY);
        const fs = g.fontSize * ds;
        if (fs < 2) continue;

        const left = cx - 3;
        const top  = cy - fs * 0.88;

        const span = document.createElement('span');
        span.className       = 'pt';
        span.contentEditable = 'true';
        span.textContent     = g.text;

        span.dataset.original     = g.text;
        span.dataset.page         = pn;
        span.dataset.pdfX         = g.pdfX;
        span.dataset.pdfY         = g.pdfY;
        span.dataset.pdfW         = g.pdfW;
        span.dataset.pdfFs        = g.fontSize;
        span.dataset.displayScale = ds;
        span.dataset.align        = 'left';
        span.dataset.dragDx       = '0';
        span.dataset.dragDy       = '0';
        span.dataset.origLeft     = left;
        span.dataset.origTop      = top;

        span.style.left     = left + 'px';
        span.style.top      = top  + 'px';
        span.style.fontSize = fs   + 'px';
        span.style.minWidth = (g.pdfW * ds || fs) + 'px';

        span.addEventListener('focus',   () => mostrarToolbar(span));
        span.addEventListener('blur',    () => {
          marcarModificado(span);
          esconderToolbar();
        });
        span.addEventListener('keydown', e => {
          if (e.key === 'Enter')  { e.preventDefault(); span.blur(); }
          if (e.key === 'Escape') {
            span.textContent = span.dataset.original;
            span.classList.remove('modified');
            span.style.background = '';
            span.style.color      = '';
            span.blur();
          }
        });

        layer.appendChild(span);
        gSpans.push(span);
      }

      pagesEl.appendChild(wrapper);
    }
  } catch (err) {
    console.error(err);
    alert('Erro ao carregar PDF: ' + err.message);
  }
}

function marcarModificado(span) {
  const textChanged = span.textContent.trim() !== span.dataset.original.trim();
  const hasDrag     = parseFloat(span.dataset.dragDx || 0) !== 0 ||
                      parseFloat(span.dataset.dragDy || 0) !== 0;
  const changed = textChanged || hasDrag || span.dataset.align !== 'left';
  span.classList.toggle('modified', changed);
  if (!span.matches(':focus')) {
    span.style.color      = changed ? '#111'                   : 'transparent';
    span.style.background = changed ? 'rgba(255,230,80,.55)'  : 'transparent';
  }
}

// ── Agrupar items de texto em linhas lógicas ─────────────────────
function agruparItems(items) {
  const LINE_TOL = 3;
  const GAP_MULT = 2;

  const linhas = [];
  for (const it of items) {
    if (!it.str) continue;
    const y = it.transform[5];
    let l = linhas.find(x => Math.abs(x.y - y) <= LINE_TOL);
    if (!l) { l = { y, items: [] }; linhas.push(l); }
    l.items.push(it);
  }
  linhas.sort((a, b) => b.y - a.y);

  const grupos = [];
  for (const linha of linhas) {
    linha.items.sort((a, b) => a.transform[4] - b.transform[4]);
    let grp = null;
    for (const it of linha.items) {
      const x  = it.transform[4];
      const w  = it.width || 0;
      const fs = Math.abs(it.transform[3]);
      if (!grp) {
        grp = { pdfX: x, pdfY: linha.y, pdfW: w, fontSize: fs, text: it.str, maxX: x + w };
      } else {
        const gap    = x - grp.maxX;
        const maxGap = Math.max(fs * GAP_MULT, 8);
        if (gap <= maxGap) {
          grp.text    += it.str;
          grp.maxX     = Math.max(grp.maxX, x + w);
          grp.pdfW     = grp.maxX - grp.pdfX;
          grp.fontSize = Math.max(grp.fontSize, fs);
        } else {
          grupos.push(grp);
          grp = { pdfX: x, pdfY: linha.y, pdfW: w, fontSize: fs, text: it.str, maxX: x + w };
        }
      }
    }
    if (grp) grupos.push(grp);
  }
  return grupos;
}

// ── Resetar tudo ─────────────────────────────────────────────────
document.getElementById('btn-reset').addEventListener('click', () => {
  for (const s of gSpans) {
    s.textContent       = s.dataset.original;
    s.dataset.align     = 'left';
    s.dataset.dragDx    = '0';
    s.dataset.dragDy    = '0';
    s.style.left        = s.dataset.origLeft + 'px';
    s.style.top         = s.dataset.origTop  + 'px';
    s.style.color       = 'transparent';
    s.style.background  = 'transparent';
    s.classList.remove('modified');
  }
});

// ── Gerar e baixar PDF ───────────────────────────────────────────
document.getElementById('btn-baixar').addEventListener('click', async () => {
  if (!gPdfDoc) return;
  const btn = document.getElementById('btn-baixar');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Gerando...';
  mostrarProgress(true);
  setProgress(0, 'Iniciando...');

  try {
    const SCALE  = 2.5;
    const A4W = 595.28, A4H = 841.89;
    const { PDFDocument } = PDFLib;
    const newDoc = await PDFDocument.create();

    // Agrupar modificações por página
    const mods = {};
    for (const s of gSpans) {
      const textChanged = s.textContent.trim() !== s.dataset.original.trim();
      const hasDrag = parseFloat(s.dataset.dragDx || 0) !== 0 || parseFloat(s.dataset.dragDy || 0) !== 0;
      if (!textChanged && !hasDrag && s.dataset.align === 'left') continue;
      const pn = s.dataset.page;
      if (!mods[pn]) mods[pn] = [];
      mods[pn].push({
        pdfX:  parseFloat(s.dataset.pdfX),
        pdfY:  parseFloat(s.dataset.pdfY),
        pdfW:  parseFloat(s.dataset.pdfW),
        pdfFs: parseFloat(s.dataset.pdfFs),
        ds:    parseFloat(s.dataset.displayScale),
        align: s.dataset.align || 'left',
        dragDx: parseFloat(s.dataset.dragDx || 0),
        dragDy: parseFloat(s.dataset.dragDy || 0),
        original: s.dataset.original,
        novo:  s.textContent,
      });
    }

    for (let pn = 1; pn <= gPdfDoc.numPages; pn++) {
      setProgress(5 + (pn - 1) / gPdfDoc.numPages * 85,
        `Renderizando página ${pn}/${gPdfDoc.numPages}...`);

      const page     = await gPdfDoc.getPage(pn);
      const viewport = page.getViewport({ scale: SCALE });
      const canvas   = document.createElement('canvas');
      canvas.width   = viewport.width;
      canvas.height  = viewport.height;
      const ctx = canvas.getContext('2d');
      await page.render({ canvasContext: ctx, viewport }).promise;

      for (const mod of (mods[pn] || [])) {
        // Converter coordenadas PDF → canvas de export
        const [origCx, origCy] = viewport.convertToViewportPoint(mod.pdfX, mod.pdfY);

        // Escalar drag offset de pixels de display → pixels de export
        const scaleFactor = SCALE / mod.ds;
        const finalCx = origCx + mod.dragDx * scaleFactor;
        const finalCy = origCy + mod.dragDy * scaleFactor;

        const fs   = mod.pdfFs * SCALE;
        const boxW = mod.pdfW  * SCALE;

        // Cobrir original (posição original, sem drag)
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(origCx - 4, origCy - fs * 1.12, boxW + 14, fs * 1.45);

        // Preparar fonte para medir texto
        const isCabecalho = mod.pdfY > 680;
        const cor = isCabecalho ? '#1a3f6f' : '#222222';
        ctx.font         = `bold ${fs}px Arial, sans-serif`;
        ctx.textBaseline = 'alphabetic';
        ctx.fillStyle    = cor;

        // Calcular x conforme alinhamento
        const novoW = ctx.measureText(mod.novo).width;
        let textX = finalCx;
        if (mod.align === 'center') textX = finalCx + (boxW - novoW) / 2;
        if (mod.align === 'right')  textX = finalCx + boxW - novoW;

        ctx.fillText(mod.novo, textX, finalCy);
      }

      // Marca d'água

      const jpg = urlParaBytes(canvas.toDataURL('image/jpeg', 0.95));
      const img = await newDoc.embedJpg(jpg);
      const pag = newDoc.addPage([A4W, A4H]);
      pag.drawImage(img, { x: 0, y: 0, width: A4W, height: A4H });
    }

    setProgress(98, 'Salvando PDF...');
    baixar(await newDoc.save(), 'laudo-concentre-pf-DEMO.pdf');
    setProgress(100, 'PDF gerado com sucesso!');
    setTimeout(() => mostrarProgress(false), 2000);

  } catch (e) {
    console.error(e);
    alert('Erro: ' + e.message);
    mostrarProgress(false);
  } finally {
    btn.disabled = false;
    btn.innerHTML = "<i class='fas fa-download mr-1'></i> Baixar PDF";
  }
});

// ── Utilitários ──────────────────────────────────────────────────
function adicionarMarcaDagua(ctx, w, h) {
  ctx.save();
  ctx.globalAlpha = 0.085;
  ctx.fillStyle   = '#be0000';
  ctx.translate(w / 2, h / 2);
  ctx.rotate(-42 * Math.PI / 180);
  ctx.font = `bold ${Math.round(w * 0.115)}px Arial Black, Arial, sans-serif`;
  ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
  ctx.fillText('DEMONSTRAÇÃO', 0, 0);
  ctx.restore();
}

function urlParaBytes(dataUrl) {
  const bin = atob(dataUrl.split(',')[1]);
  const arr = new Uint8Array(bin.length);
  for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
  return arr;
}

function baixar(bytes, nome) {
  const a = Object.assign(document.createElement('a'), {
    href: URL.createObjectURL(new Blob([bytes], { type: 'application/pdf' })),
    download: nome,
  });
  a.click();
  setTimeout(() => URL.revokeObjectURL(a.href), 2000);
}

function setProgress(pct, msg) {
  document.getElementById('progress-bar').style.width = pct + '%';
  if (msg) document.getElementById('progress-msg').textContent = msg;
}

function mostrarProgress(show) {
  document.getElementById('progress-wrap').style.display = show ? '' : 'none';
}
</script>
<?php $v->end(); ?>

<?php $v->layout("_theme") ?>

<?php $v->start("styles") ?>
<style>
/* ── Embed code box ────────────────────────────────────── */
.embed-box {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 1rem;
  font-family: 'JetBrains Mono', monospace;
  font-size: .78rem;
  color: var(--accent);
  word-break: break-all;
  line-height: 1.7;
  cursor: pointer;
  transition: border-color .2s, background .2s, box-shadow .2s;
  user-select: all;
}
.embed-box:hover {
  border-color: var(--accent-border);
  background: var(--accent-soft);
  box-shadow: 0 0 0 3px rgba(108,92,231,.07);
}

/* ── Ad item ───────────────────────────────────────────── */
.ad-item {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 1rem;
  display: grid;
  grid-template-columns: 1fr auto;
  gap: .6rem;
  align-items: center;
  transition: border-color .15s, box-shadow .15s;
}
.ad-item:hover { border-color: var(--accent-border); box-shadow: var(--shadow); }

.ad-badge {
  display: inline-flex; align-items: center; gap: .35rem;
  padding: .22rem .6rem;
  border-radius: 99px;
  background: var(--accent-soft);
  border: 1px solid var(--accent-border);
  font-size: .7rem;
  font-weight: 700;
  color: var(--accent);
}

/* ── Player preview ────────────────────────────────────── */
.preview-thumb {
  width: 100%;
  background: #000;
  border-radius: 9px;
  overflow: hidden;
  border: 1px solid var(--border);
}
.preview-thumb iframe { width: 100%; height: 100%; display: block; border: none; }

/* ── Toggle switch ─────────────────────────────────────── */
.wt-toggle {
  display: flex; align-items: center;
  gap: .65rem;
  cursor: pointer;
  user-select: none;
}
.wt-toggle input[type="checkbox"] { display: none; }
.wt-toggle-track {
  width: 34px; height: 19px;
  border-radius: 99px;
  background: rgba(0,0,0,.12);
  border: 1px solid rgba(0,0,0,.1);
  position: relative;
  transition: background .2s, border-color .2s;
  flex-shrink: 0;
}
.wt-toggle-track::after {
  content: '';
  position: absolute;
  top: 2px; left: 2px;
  width: 13px; height: 13px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,.2);
  transition: transform .2s cubic-bezier(.34,1.56,.64,1);
}
.wt-toggle input:checked ~ .wt-toggle-track {
  background: var(--accent);
  border-color: var(--accent);
}
.wt-toggle input:checked ~ .wt-toggle-track::after {
  transform: translateX(15px);
}
.wt-toggle-label {
  font-size: .84rem;
  color: var(--muted);
  transition: color .15s;
}
.wt-toggle input:checked ~ .wt-toggle-label,
.wt-toggle:hover .wt-toggle-label { color: var(--text); }

/* ── Color picker row ──────────────────────────────────── */
.color-row {
  display: flex; gap: .5rem; align-items: center;
}
.color-row input[type="color"] {
  width: 38px; height: 36px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: none;
  cursor: pointer;
  padding: 2px;
  flex-shrink: 0;
}

/* ── Embed modal ───────────────────────────────────────── */
#modal-embed {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.72);
  backdrop-filter: blur(4px);
  z-index: 200;
  align-items: center; justify-content: center;
  padding: 1rem;
  display: none;
}
#modal-embed .modal-box { max-width: 520px; }
.embed-rec-badge {
  display: inline-flex; align-items: center;
  padding: .18rem .65rem;
  background: var(--accent-soft); border: 1px solid var(--accent-border);
  border-radius: 99px; font-size: .67rem; font-weight: 700;
  color: var(--accent); letter-spacing: .04em; text-transform: uppercase;
}
.embed-tab-pill {
  display: inline-flex; align-items: center;
  padding: .22rem .75rem;
  border-radius: 8px; font-size: .74rem; font-weight: 600;
  border: 1.5px solid var(--accent-border);
  color: var(--accent); background: var(--accent-soft);
}
.embed-code-block {
  background: #f7f8fc;
  border: 1px solid var(--border);
  border-radius: 10px; padding: .9rem .85rem .9rem .9rem;
  font-family: 'JetBrains Mono', monospace;
  font-size: .74rem; color: var(--accent);
  line-height: 1.65; word-break: break-all;
  user-select: all; cursor: text;
  min-height: 64px;
}
.embed-divider {
  border: none; border-top: 1px solid var(--border); margin: .1rem 0;
}
.embed-resp-row {
  display: flex; justify-content: space-between; align-items: center;
  gap: 1rem; padding: .5rem 0;
}

/* ── Modal ─────────────────────────────────────────────── */
#modal-ad {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.72);
  backdrop-filter: blur(4px);
  z-index: 200;
  align-items: center; justify-content: center;
  padding: 1rem;
  display: none;
}
.modal-box {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 14px;
  width: 100%; max-width: 420px;
  padding: 1.6rem;
  display: flex; flex-direction: column; gap: 1rem;
  box-shadow: 0 24px 64px rgba(0,0,0,.15);
  animation: modal-pop .25s cubic-bezier(.22,1,.36,1);
}
@keyframes modal-pop {
  from { opacity: 0; transform: scale(.95) translateY(8px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-title {
  display: flex; justify-content: space-between; align-items: center;
}
.modal-title h3 { font-size: .95rem; font-weight: 600; }
.modal-close {
  background: none; border: none;
  color: var(--muted); cursor: pointer;
  font-size: 1rem; padding: .2rem;
  transition: color .15s;
}
.modal-close:hover { color: var(--text); }
</style>
<?php $v->end() ?>

<!-- Page header -->
<div style="display:flex;gap:.6rem;align-items:center;margin-bottom:1.6rem;flex-wrap:wrap;">
  <a href="<?= url("videos") ?>" class="btn-wt btn-wt-ghost btn-wt-sm">
    <i class="fas fa-arrow-left"></i> Voltar
  </a>
  <h2 style="font-size:1rem;font-weight:600;flex:1;margin:0;">
    <?= htmlspecialchars($video->titulo, ENT_QUOTES) ?>
  </h2>
  <a href="<?= url("metricas/{$video->id}") ?>" class="btn-wt btn-wt-ghost btn-wt-sm">
    <i class="fas fa-chart-bar"></i> Métricas
  </a>
</div>

<div class="wt-grid-2" style="gap:1.4rem;align-items:start;">

  <!-- ── Coluna esquerda ── -->
  <div style="display:flex;flex-direction:column;gap:1.2rem;">

    <!-- Detalhes -->
    <div class="wt-card">
      <div class="wt-card-title"><i class="fas fa-pen" style="margin-right:.45rem;color:var(--accent);opacity:.7;"></i>Detalhes do vídeo</div>

      <div class="wt-field">
        <label class="wt-label">Título</label>
        <input class="wt-input" id="titulo" type="text" value="<?= htmlspecialchars($video->titulo, ENT_QUOTES) ?>">
      </div>

      <div class="wt-field">
        <label class="wt-label">Descrição</label>
        <textarea class="wt-textarea" id="descricao"><?= htmlspecialchars($video->descricao ?? "", ENT_QUOTES) ?></textarea>
      </div>

      <!-- Tamanho preset -->
      <div style="margin-bottom:.7rem;">
        <label class="wt-label" style="margin-bottom:.5rem;">Tamanho do embed</label>
        <div style="display:flex;flex-wrap:wrap;gap:.35rem;">
          <button type="button" class="wt-preset-btn" onclick="setSize(360,640)">Celular</button>
          <button type="button" class="wt-preset-btn" onclick="setSize(540,960)">Celular G</button>
          <button type="button" class="wt-preset-btn" onclick="setSize(768,432)">Tablet</button>
          <button type="button" class="wt-preset-btn" onclick="setSize(854,480)">PC</button>
          <button type="button" class="wt-preset-btn" onclick="setSize(1280,720)">HD</button>
          <button type="button" class="wt-preset-btn" onclick="setSize(1920,1080)">Full HD</button>
        </div>
      </div>

      <div class="wt-grid-2" style="gap:.8rem;margin-bottom:1.1rem;">
        <div>
          <label class="wt-label">Largura (px)</label>
          <input class="wt-input" id="largura" type="number" value="<?= (int)$video->largura ?>">
        </div>
        <div>
          <label class="wt-label">Altura (px)</label>
          <input class="wt-input" id="altura" type="number" value="<?= (int)$video->altura ?>">
        </div>
      </div>

      <hr class="wt-divider">

      <!-- Toggles -->
      <div style="display:flex;flex-direction:column;gap:.75rem;margin-bottom:1.1rem;">
        <label class="wt-toggle">
          <input type="checkbox" id="autoplay" <?= $video->autoplay ? "checked" : "" ?>>
          <span class="wt-toggle-track"></span>
          <div>
            <span class="wt-toggle-label">Autoplay mudo ao carregar</span>
            <div style="font-size:.73rem;color:var(--muted);margin-top:.1rem;">Inicia silencioso — clique desbloqueia o áudio</div>
          </div>
        </label>
        <label class="wt-toggle" style="padding:.6rem .7rem;background:var(--bg);border-radius:9px;border:1px solid var(--border);">
          <input type="checkbox" id="force_play" <?= $video->force_play ? "checked" : "" ?>>
          <span class="wt-toggle-track"></span>
          <div>
            <span class="wt-toggle-label">Reprodução forçada</span>
            <div style="font-size:.73rem;color:var(--muted);margin-top:.1rem;">Sem controles, sem pausa — volume máximo após clicar para ouvir</div>
          </div>
        </label>
        <label class="wt-toggle">
          <input type="checkbox" id="anti_skip" <?= $video->anti_skip ? "checked" : "" ?>>
          <span class="wt-toggle-track"></span>
          <span class="wt-toggle-label">Anti-skip — impede avançar o vídeo</span>
        </label>
        <label class="wt-toggle">
          <input type="checkbox" id="allow_speed" <?= $video->allow_speed ? "checked" : "" ?>>
          <span class="wt-toggle-track"></span>
          <span class="wt-toggle-label">Controle de velocidade</span>
        </label>
        <label class="wt-toggle">
          <input type="checkbox" id="hide_seekbar" <?= $video->hide_seekbar ? "checked" : "" ?>>
          <span class="wt-toggle-track"></span>
          <span class="wt-toggle-label">Ocultar barra de reprodução</span>
        </label>
        <label class="wt-toggle">
          <input type="checkbox" id="fake_seekbar" <?= $video->fake_seekbar ? "checked" : "" ?>>
          <span class="wt-toggle-track"></span>
          <div>
            <span class="wt-toggle-label">Barra fake</span>
            <div style="font-size:.73rem;color:var(--muted);margin-top:.1rem;">Barra visual que avança com o vídeo — usuário não consegue clicar para avançar. Ativa "Ocultar barra" automaticamente.</div>
          </div>
        </label>
      </div>

      <hr class="wt-divider">

      <!-- Cores -->
      <div class="wt-grid-2" style="gap:.8rem;margin-bottom:1.1rem;">
        <div>
          <label class="wt-label">Cor de destaque</label>
          <div class="color-row">
            <input type="color" id="cor_acento" value="<?= htmlspecialchars($video->cor_acento ?? '#f97316', ENT_QUOTES) ?>">
            <input class="wt-input" type="text" id="cor_acento_txt"
              value="<?= htmlspecialchars($video->cor_acento ?? '#f97316', ENT_QUOTES) ?>"
              style="font-family:'JetBrains Mono',monospace;font-size:.8rem;"
              maxlength="7" placeholder="#f97316">
          </div>
        </div>
        <div>
          <label class="wt-label">Cor de fundo</label>
          <div class="color-row">
            <input type="color" id="cor_fundo" value="<?= htmlspecialchars($video->cor_fundo ?? '#000000', ENT_QUOTES) ?>">
            <input class="wt-input" type="text" id="cor_fundo_txt"
              value="<?= htmlspecialchars($video->cor_fundo ?? '#000000', ENT_QUOTES) ?>"
              style="font-family:'JetBrains Mono',monospace;font-size:.8rem;"
              maxlength="7" placeholder="#000000">
          </div>
        </div>
      </div>

      <!-- Cor do botão play + fundo do card "Clique para ouvir" -->
      <div class="wt-grid-2" style="gap:.8rem;margin-bottom:1.1rem;">
        <div>
          <label class="wt-label">Cor do botão Play <small style="font-weight:400;color:var(--muted);">(vazio = cor de destaque)</small></label>
          <div class="color-row">
            <input type="color" id="cor_play_btn" value="<?= htmlspecialchars($video->cor_play_btn ?? ($video->cor_acento ?? '#f97316'), ENT_QUOTES) ?>">
            <input class="wt-input" type="text" id="cor_play_btn_txt"
              value="<?= htmlspecialchars($video->cor_play_btn ?? '', ENT_QUOTES) ?>"
              style="font-family:'JetBrains Mono',monospace;font-size:.8rem;"
              maxlength="7" placeholder="igual à cor de destaque">
          </div>
        </div>
        <div>
          <label class="wt-label">Fundo do card "Clique para ouvir" <small style="font-weight:400;color:var(--muted);">(vazio = preto)</small></label>
          <div class="color-row">
            <input type="color" id="cor_unmute_bg" value="<?= htmlspecialchars($video->cor_unmute_bg ?? '#000000', ENT_QUOTES) ?>">
            <input class="wt-input" type="text" id="cor_unmute_bg_txt"
              value="<?= htmlspecialchars($video->cor_unmute_bg ?? '', ENT_QUOTES) ?>"
              style="font-family:'JetBrains Mono',monospace;font-size:.8rem;"
              maxlength="7" placeholder="#000000">
          </div>
        </div>
      </div>

      <!-- Textos do card unmute -->
      <div class="wt-grid-2" style="gap:.8rem;margin-bottom:1.1rem;">
        <div>
          <label class="wt-label">Título do card <small style="font-weight:400;color:var(--muted);">(vazio = "Clique para ouvir")</small></label>
          <input class="wt-input" type="text" id="unmute_label" maxlength="100"
            placeholder="Clique para ouvir"
            value="<?= htmlspecialchars($video->unmute_label ?? '', ENT_QUOTES) ?>">
        </div>
        <div>
          <label class="wt-label">Subtítulo do card <small style="font-weight:400;color:var(--muted);">(vazio = "Vídeo com som desativado")</small></label>
          <input class="wt-input" type="text" id="unmute_sub" maxlength="150"
            placeholder="Vídeo com som desativado"
            value="<?= htmlspecialchars($video->unmute_sub ?? '', ENT_QUOTES) ?>">
        </div>
      </div>

      <div style="margin-bottom:1.1rem;">
        <label class="wt-label">Cor do texto do card "Clique para ouvir" <small style="font-weight:400;color:var(--muted);">(vazio = branco)</small></label>
        <div style="display:flex;gap:.5rem;align-items:center;">
          <button type="button" onclick="setUnmuteTexto('#ffffff')"
            id="btn-texto-branco"
            style="padding:.4rem 1rem;border-radius:8px;border:2px solid #fff;background:#fff;color:#111;font-size:.8rem;font-weight:700;cursor:pointer;">
            Branco
          </button>
          <button type="button" onclick="setUnmuteTexto('#000000')"
            id="btn-texto-preto"
            style="padding:.4rem 1rem;border-radius:8px;border:2px solid #333;background:#000;color:#fff;font-size:.8rem;font-weight:700;cursor:pointer;">
            Preto
          </button>
          <div class="color-row" style="flex:1;">
            <input type="color" id="cor_unmute_texto" value="<?= htmlspecialchars($video->cor_unmute_texto ?? '#ffffff', ENT_QUOTES) ?>">
            <input class="wt-input" type="text" id="cor_unmute_texto_txt"
              value="<?= htmlspecialchars($video->cor_unmute_texto ?? '', ENT_QUOTES) ?>"
              style="font-family:'JetBrains Mono',monospace;font-size:.8rem;"
              maxlength="7" placeholder="#ffffff">
          </div>
        </div>
      </div>

      <!-- Capa -->
      <div class="wt-field">
        <label class="wt-label">Capa / thumbnail</label>
        <input class="wt-input" id="capa" type="file" accept="image/*" style="padding:.4rem .8rem;">
      </div>

      <button class="btn-wt" onclick="salvarVideo()" style="width:100%;justify-content:center;">
        <i class="fas fa-check"></i> Salvar alterações
      </button>
    </div>

    <!-- Anúncios / CTAs -->
    <div class="wt-card" id="secao-anuncios">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <div class="wt-card-title" style="margin-bottom:0;">
          <i class="fas fa-bullhorn" style="margin-right:.45rem;color:var(--accent);opacity:.7;"></i>Anúncios / CTAs
        </div>
        <button class="btn-wt btn-wt-sm" onclick="abrirModalAd()">
          <i class="fas fa-plus"></i> Novo
        </button>
      </div>

      <div id="lista-anuncios" style="display:flex;flex-direction:column;gap:.6rem;">
        <?php if ($anuncios) : ?>
          <?php foreach ($anuncios as $ad) : ?>
          <div class="ad-item" id="ad-<?= $ad->id ?>">
            <div>
              <div style="display:flex;gap:.35rem;flex-wrap:wrap;margin-bottom:.35rem;">
                <span class="ad-badge"><i class="fas fa-clock"></i> <?= (int)$ad->exibir_em ?>s</span>
                <span class="ad-badge">
                  <i class="fas <?= ($ad->modo ?? 'pausa') === 'banner' ? 'fa-align-justify' : 'fa-pause' ?>"></i>
                  <?= ($ad->modo ?? 'pausa') === 'banner' ? 'Banner' : 'Pausa' ?>
                </span>
              </div>
              <strong style="display:block;font-size:.88rem;">
                <?= htmlspecialchars($ad->nome, ENT_QUOTES) ?>
              </strong>
              <?php if ($ad->valor) : ?>
                <span style="font-size:.8rem;color:var(--accent);font-weight:600;">
                  R$ <?= number_format((float)$ad->valor, 2, ",", ".") ?>
                </span>
              <?php endif; ?>
              <a href="<?= htmlspecialchars($ad->url_destino, ENT_QUOTES) ?>" target="_blank" rel="noopener"
                 style="display:block;font-size:.72rem;color:var(--muted);margin-top:.2rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;font-family:'JetBrains Mono',monospace;">
                <?= htmlspecialchars($ad->url_destino, ENT_QUOTES) ?>
              </a>
            </div>
            <button onclick="excluirAd(<?= $ad->id ?>)" class="btn-wt btn-wt-sm btn-wt-danger">
              <i class="fas fa-trash"></i>
            </button>
          </div>
          <?php endforeach; ?>
        <?php else : ?>
          <p id="sem-anuncios" style="color:var(--muted);font-size:.84rem;text-align:center;padding:1.5rem 0;">
            Nenhum CTA. Adicione um para exibir durante o vídeo.
          </p>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /coluna esquerda -->

  <!-- ── Coluna direita ── -->
  <div style="display:flex;flex-direction:column;gap:1.2rem;position:sticky;top:5rem;">

    <!-- Preview iframe -->
    <div class="wt-card">
      <div class="wt-card-title"><i class="fas fa-play-circle" style="margin-right:.45rem;color:var(--accent);opacity:.7;"></i>Preview</div>
      <div class="preview-thumb">
        <iframe id="preview-iframe"
          src="<?= htmlspecialchars($embedUrl, ENT_QUOTES) ?>"
          allowfullscreen
          allow="autoplay; fullscreen">
        </iframe>
      </div>
      <button class="btn-wt btn-wt-ghost btn-wt-sm" style="width:100%;justify-content:center;margin-top:.5rem;" onclick="recarregarPreview()">
        <i class="fas fa-rotate-right"></i> Atualizar preview
      </button>
    </div>

    <!-- Embed button card -->
    <div class="wt-card" style="gap:.85rem;">
      <div class="wt-card-title">
        <i class="fas fa-code" style="margin-right:.45rem;color:var(--accent);opacity:.7;"></i>Embed
      </div>
      <p style="font-size:.78rem;color:var(--muted);">
        Incorpore o player em qualquer site ou funil de vendas.
      </p>
      <button class="btn-wt" style="width:100%;justify-content:center;" onclick="abrirModalEmbed()">
        <i class="fas fa-code"></i> Copiar código de embed
      </button>
      <p style="font-size:.72rem;color:var(--muted);display:flex;align-items:center;gap:.3rem;">
        <i class="fas fa-shield-halved"></i> Funciona em qualquer site, sem login.
      </p>
    </div>

    <!-- URL direta -->
    <div class="wt-card">
      <div class="wt-card-title"><i class="fas fa-link" style="margin-right:.45rem;color:var(--accent);opacity:.7;"></i>URL do player</div>
      <div style="display:flex;gap:.5rem;align-items:center;">
        <input class="wt-input" type="text" id="embed-url-input"
               value="<?= htmlspecialchars($embedUrl, ENT_QUOTES) ?>" readonly
               style="flex:1;font-family:'JetBrains Mono',monospace;font-size:.76rem;">
        <button class="btn-wt btn-wt-sm" onclick="copiarUrl()"><i class="fas fa-copy"></i></button>
      </div>
    </div>

  </div><!-- /coluna direita -->

</div>

<!-- ── Modal embed ── -->
<div id="modal-embed">
  <div class="modal-box">
    <div class="modal-title">
      <h3><i class="fas fa-code" style="color:var(--accent);margin-right:.45rem;"></i>Copiar embed</h3>
      <button class="modal-close" onclick="fecharModalEmbed()"><i class="fas fa-times"></i></button>
    </div>

    <p style="font-size:.8rem;color:var(--muted);">
      Incorpore o vídeo onde quiser!
      <a href="<?= htmlspecialchars($embedUrl, ENT_QUOTES) ?>" target="_blank" rel="noopener"
         style="color:var(--accent);text-decoration:none;font-weight:600;">Abrir player ↗</a>
    </p>

    <hr class="embed-divider">

    <!-- Responsivo toggle -->
    <div class="embed-resp-row">
      <div>
        <div style="font-size:.83rem;font-weight:600;color:var(--text);">Vídeo Responsivo</div>
        <div style="font-size:.73rem;color:var(--muted);margin-top:.1rem;">Use 100% da largura do contêiner</div>
      </div>
      <label class="wt-toggle" style="cursor:pointer;">
        <input type="checkbox" id="embed-responsivo" onchange="atualizarCodigoEmbed()">
        <span class="wt-toggle-track"></span>
      </label>
    </div>

    <hr class="embed-divider">

    <!-- Código -->
    <div style="display:flex;flex-direction:column;gap:.7rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.4rem;">
        <span style="font-size:.82rem;font-weight:600;color:var(--text);">Código de Embed</span>
        <div style="display:flex;gap:.4rem;align-items:center;">
          <span class="embed-rec-badge">Recomendado</span>
          <span class="embed-tab-pill"><i class="fas fa-code" style="margin-right:.3rem;font-size:.65rem;"></i>iFrame</span>
        </div>
      </div>

      <div class="embed-code-block" id="embed-modal-code"></div>

      <button class="btn-wt" style="width:100%;justify-content:center;" onclick="copiarEmbedModal()">
        <i class="fas fa-copy"></i> Copiar código
      </button>
    </div>

    <button class="btn-wt btn-wt-ghost" onclick="fecharModalEmbed()" style="width:100%;justify-content:center;margin-top:.2rem;">
      Fechar
    </button>
  </div>
</div>

<!-- ── Modal novo anúncio ── -->
<div id="modal-ad">
  <div class="modal-box">
    <div class="modal-title">
      <h3><i class="fas fa-bullhorn" style="color:var(--accent);margin-right:.5rem;"></i>Novo anúncio</h3>
      <button class="modal-close" onclick="fecharModalAd()"><i class="fas fa-times"></i></button>
    </div>

    <div class="wt-grid-2" style="gap:.8rem;">
      <div class="wt-field" style="margin:0;">
        <label class="wt-label">Nome do anúncio *</label>
        <input class="wt-input" id="ad-nome" type="text" placeholder="Ex: Oferta especial">
      </div>
      <div class="wt-field" style="margin:0;">
        <label class="wt-label">Exibir em (segundos) *</label>
        <input class="wt-input" id="ad-segundo" type="number" min="0" placeholder="30">
      </div>
    </div>
    <div class="wt-field">
      <label class="wt-label">Modo de exibição *</label>
      <select class="wt-input" id="ad-modo" onchange="toggleModoFields()">
        <option value="pausa">Pausar vídeo — overlay centralizado com detalhes</option>
        <option value="banner">Banner simples — vídeo continua rodando</option>
      </select>
    </div>
    <div class="wt-field">
      <label class="wt-label">URL de destino *</label>
      <input class="wt-input" id="ad-url" type="url" placeholder="https://...">
    </div>
    <div id="ad-campos-pausa">
      <div class="wt-grid-2" style="gap:.8rem;margin-bottom:.8rem;">
        <div>
          <label class="wt-label">Valor (opcional)</label>
          <input class="wt-input" id="ad-valor" type="text" placeholder="R$ 97,00">
        </div>
        <div>
          <label class="wt-label">Imagem (opcional)</label>
          <input class="wt-input" id="ad-imagem" type="file" accept="image/*" style="padding:.4rem .8rem;">
        </div>
      </div>
      <div class="wt-field" style="margin:0;">
        <label class="wt-label">Descrição (opcional)</label>
        <input class="wt-input" id="ad-desc" type="text" placeholder="Descrição curta">
      </div>
    </div>

    <div style="display:flex;gap:.6rem;justify-content:flex-end;">
      <button class="btn-wt btn-wt-ghost" onclick="fecharModalAd()">Cancelar</button>
      <button class="btn-wt" onclick="salvarAd()"><i class="fas fa-check"></i> Salvar</button>
    </div>
  </div>
</div>

<?php $v->start("scripts") ?>
<script>
const VIDEO_ID    = <?= (int)$video->id ?>;
const SAVE_URL    = <?= json_encode(url("videos/salvar")) ?>;
const AD_SAVE_URL = <?= json_encode(url("anuncios/salvar")) ?>;
const AD_DEL_URL  = <?= json_encode(url("anuncios/excluir")) ?>;

function setSize(w, h) {
  document.getElementById("largura").value = w;
  document.getElementById("altura").value  = h;
  atualizarAspectPreview();
}

function atualizarAspectPreview() {
  const w = parseInt(document.getElementById("largura").value) || <?= (int)$video->largura ?>;
  const h = parseInt(document.getElementById("altura").value)  || <?= (int)$video->altura ?>;
  document.querySelector(".preview-thumb").style.aspectRatio = w + " / " + h;
}

// Atualiza ao digitar nos campos manualmente
document.getElementById("largura").addEventListener("input", atualizarAspectPreview);
document.getElementById("altura").addEventListener("input",  atualizarAspectPreview);

// Aplica o aspect ratio inicial
atualizarAspectPreview();

function salvarVideo() {
  const fd = new FormData();
  fd.append("id",        VIDEO_ID);
  fd.append("titulo",    document.getElementById("titulo").value);
  fd.append("descricao", document.getElementById("descricao").value);
  fd.append("largura",   document.getElementById("largura").value);
  fd.append("altura",    document.getElementById("altura").value);
  if (document.getElementById("autoplay").checked)     fd.append("autoplay", "1");
  if (document.getElementById("force_play").checked)  fd.append("force_play", "1");
  if (document.getElementById("anti_skip").checked)    fd.append("anti_skip", "1");
  if (document.getElementById("allow_speed").checked)  fd.append("allow_speed", "1");
  if (document.getElementById("hide_seekbar").checked) fd.append("hide_seekbar", "1");
  if (document.getElementById("fake_seekbar").checked) fd.append("fake_seekbar", "1");
  fd.append("cor_acento",   document.getElementById("cor_acento").value);
  fd.append("cor_fundo",    document.getElementById("cor_fundo").value);
  const playBtnTxt  = document.getElementById("cor_play_btn_txt").value.trim();
  if (/^#[0-9a-fA-F]{6}$/.test(playBtnTxt)) fd.append("cor_play_btn", playBtnTxt);
  const unmuteBgTxt  = document.getElementById("cor_unmute_bg_txt").value.trim();
  if (/^#[0-9a-fA-F]{6}$/.test(unmuteBgTxt)) fd.append("cor_unmute_bg", unmuteBgTxt);
  const unmuteTextoTxt = document.getElementById("cor_unmute_texto_txt").value.trim();
  if (/^#[0-9a-fA-F]{6}$/.test(unmuteTextoTxt)) fd.append("cor_unmute_texto", unmuteTextoTxt);
  const unmuteLabelVal = document.getElementById("unmute_label").value.trim();
  if (unmuteLabelVal) fd.append("unmute_label", unmuteLabelVal);
  const unmuteSubVal = document.getElementById("unmute_sub").value.trim();
  if (unmuteSubVal) fd.append("unmute_sub", unmuteSubVal);
  const capa = document.getElementById("capa").files[0];
  if (capa) fd.append("capa", capa);

  fetch(SAVE_URL, { method: "POST", body: fd })
    .then(r => r.json())
    .then(r => {
      if (r.ok) { wtToast("Salvo com sucesso!", "ok"); atualizarEmbedCode(); recarregarPreview(); }
      else wtToast("Erro ao salvar.", "err");
    });
}

function atualizarEmbedCode() {
  atualizarCodigoEmbed();
}

function recarregarPreview() {
  const f = document.getElementById('preview-iframe');
  f.src = f.src;
}

function copiarEmbed() {
  copiarEmbedModal();
}

function copiarUrl() {
  navigator.clipboard.writeText(document.getElementById("embed-url-input").value)
    .then(() => wtToast("URL copiada!", "ok"));
}

// ── Embed modal ────────────────────────────────────────────────────────────
const modalEmbed = document.getElementById("modal-embed");

function abrirModalEmbed() {
  document.getElementById("embed-responsivo").checked = false;
  atualizarCodigoEmbed();
  modalEmbed.style.display = "flex";
}

function fecharModalEmbed() { modalEmbed.style.display = "none"; }

function atualizarCodigoEmbed() {
  const responsivo = document.getElementById("embed-responsivo")?.checked;
  const w   = document.getElementById("largura").value || <?= (int)$video->largura ?>;
  const h   = document.getElementById("altura").value  || <?= (int)$video->altura ?>;
  const src = document.getElementById("embed-url-input").value;
  let code;
  if (responsivo) {
    const pct = (Math.round((h / w) * 10000) / 100) + '%';
    code = `<div style="position:relative;padding-bottom:${pct};height:0;overflow:hidden;">`
         + `<iframe src="${src}" style="position:absolute;inset:0;width:100%;height:100%;" frameborder="0" allowfullscreen allow="autoplay; fullscreen"></iframe></div>`;
  } else {
    code = `<iframe src="${src}" width="${w}" height="${h}" frameborder="0" allowfullscreen allow="autoplay; fullscreen"></iframe>`;
  }
  const el = document.getElementById("embed-modal-code");
  if (el) el.textContent = code;
}

function copiarEmbedModal() {
  const code = document.getElementById("embed-modal-code")?.textContent || "";
  navigator.clipboard.writeText(code).then(() => wtToast("Código copiado!", "ok"));
}

modalEmbed.addEventListener("click", function(e) {
  if (e.target === modalEmbed) fecharModalEmbed();
});

const modal = document.getElementById("modal-ad");

function abrirModalAd() {
  ["ad-nome","ad-url","ad-desc","ad-valor","ad-segundo"].forEach(id => document.getElementById(id).value = "");
  document.getElementById("ad-imagem").value = "";
  document.getElementById("ad-modo").value = "pausa";
  toggleModoFields();
  modal.style.display = "flex";
}

function toggleModoFields() {
  const modo = document.getElementById("ad-modo").value;
  document.getElementById("ad-campos-pausa").style.display = modo === "pausa" ? "" : "none";
}

function fecharModalAd() { modal.style.display = "none"; }

modal.addEventListener("click", e => { if (e.target === modal) fecharModalAd(); });

function salvarAd() {
  const nome = document.getElementById("ad-nome").value.trim();
  const url  = document.getElementById("ad-url").value.trim();
  const seg  = document.getElementById("ad-segundo").value.trim();

  if (!nome || !url || seg === "") {
    wtToast("Preencha nome, URL e segundo.", "err");
    return;
  }

  const fd = new FormData();
  const modo = document.getElementById("ad-modo").value;
  fd.append("video_id",    VIDEO_ID);
  fd.append("nome",        nome);
  fd.append("url_destino", url);
  fd.append("exibir_em",   seg);
  fd.append("modo",        modo);
  fd.append("descricao",   document.getElementById("ad-desc").value);
  fd.append("valor",       document.getElementById("ad-valor").value.replace("R$","").trim());
  const img = document.getElementById("ad-imagem").files[0];
  if (img) fd.append("imagem", img);

  fetch(AD_SAVE_URL, { method: "POST", body: fd })
    .then(r => r.json())
    .then(r => {
      if (r.ok) {
        wtToast("Anúncio salvo!", "ok");
        fecharModalAd();
        document.getElementById("sem-anuncios")?.remove();
        const item = document.createElement("div");
        item.className = "ad-item";
        item.id = "ad-" + r.id;
        const modoLabel = modo === 'banner' ? 'Banner' : 'Pausa';
        const modoIcon  = modo === 'banner' ? 'fa-align-justify' : 'fa-pause';
        item.innerHTML = `
          <div>
            <div style="display:flex;gap:.35rem;flex-wrap:wrap;margin-bottom:.35rem;">
              <span class="ad-badge"><i class="fas fa-clock"></i> ${seg}s</span>
              <span class="ad-badge"><i class="fas ${modoIcon}"></i> ${modoLabel}</span>
            </div>
            <strong style="display:block;font-size:.88rem;">${nome}</strong>
            <a href="${url}" target="_blank" rel="noopener"
               style="display:block;font-size:.72rem;color:var(--muted);margin-top:.2rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;font-family:'JetBrains Mono',monospace;">${url}</a>
          </div>
          <button onclick="excluirAd(${r.id})" class="btn-wt btn-wt-sm btn-wt-danger"><i class="fas fa-trash"></i></button>`;
        document.getElementById("lista-anuncios").appendChild(item);
      } else {
        wtToast(r.msg || "Erro ao salvar.", "err");
      }
    });
}

function excluirAd(id) {
  if (!confirm("Remover este anúncio?")) return;
  fetch(AD_DEL_URL, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "id=" + id,
  }).then(r => r.json()).then(r => {
    if (r.ok) { document.getElementById("ad-" + id)?.remove(); wtToast("Removido.", "ok"); }
  });
}

// fake_seekbar ↔ hide_seekbar: ativar fake força hide; desativar hide desativa fake
document.getElementById("fake_seekbar").addEventListener("change", function () {
  if (this.checked) document.getElementById("hide_seekbar").checked = true;
});
document.getElementById("hide_seekbar").addEventListener("change", function () {
  if (!this.checked) document.getElementById("fake_seekbar").checked = false;
});

function setUnmuteTexto(hex) {
  document.getElementById("cor_unmute_texto").value     = hex;
  document.getElementById("cor_unmute_texto_txt").value = hex;
}

// Sync color picker ↔ text input
["acento","fundo","play_btn","unmute_bg","unmute_texto"].forEach(key => {
  const picker = document.getElementById("cor_" + key);
  const txt    = document.getElementById("cor_" + key + "_txt");
  if (!picker || !txt) return;
  picker.addEventListener("input", () => { txt.value = picker.value; });
  txt.addEventListener("input", () => {
    if (/^#[0-9a-fA-F]{6}$/.test(txt.value)) picker.value = txt.value;
  });
});
</script>
<?php $v->end() ?>

<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper win-page">
    <section class="content">
        <div class="container-fluid">
            <header class="win-page-header">
                <div class="win-page-title">
                    <small>Gestão de conteúdo</small>
                    <h1>Biblioteca de Vídeos</h1>
                </div>
            </header>

            <!-- UPLOAD -->
            <section class="win-section">
                <div class="win-section-header">
                    <h2>Enviar novo vídeo</h2>
                    <span class="win-section-subtitle">Formatos aceitos: MP4, WebM, OGG, MOV, MKV · Máximo: 800 MB</span>
                </div>

                <div class="win-form">
                    <div class="win-form-grid">
                        <div class="form-group">
                            <label for="titulo-video">Título do vídeo</label>
                            <input type="text" class="form-control" id="titulo-video" placeholder="Ex.: Aula 1 – Introdução">
                        </div>
                        <div class="form-group">
                            <label>Arquivo</label>
                            <div class="attachment">
                                <label class="filelabel" for="arquivo-video" data-default="Escolher vídeo">
                                    <i class="fas fa-film"></i>
                                    <span id="label-arquivo">Escolher vídeo</span>
                                </label>
                                <input type="file" id="arquivo-video" accept=".mp4,.webm,.ogg,.mov,.mkv" style="display:none;">
                            </div>
                        </div>
                    </div>

                    <!-- barra de progresso do upload -->
                    <div id="upload-progress-wrap" style="display:none; margin-top:1rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.3rem;">
                            <small id="upload-status-txt" style="color:var(--wh-text-soft);">Enviando…</small>
                            <small id="upload-pct-txt" style="color:var(--wh-text-soft);">0%</small>
                        </div>
                        <div style="height:6px; background:rgba(255,255,255,0.08); border-radius:99px; overflow:hidden;">
                            <div id="upload-bar" style="height:100%; width:0%; background:var(--wh-accent); transition:width 0.2s;"></div>
                        </div>
                    </div>

                    <div class="win-form-actions" style="margin-top:1rem;">
                        <button type="button" id="btn-upload" class="btn-win">
                            Enviar vídeo
                            <i class="fas fa-upload"></i>
                        </button>
                    </div>
                </div>
            </section>

            <!-- LISTA -->
            <section class="win-section">
                <div class="win-section-header">
                    <h2>Meus vídeos</h2>
                </div>

                <div id="lista-videos">
                    <?php if ($videos) : ?>
                        <?php foreach ($videos as $vid) :
                            $mb = round($vid->tamanho / 1048576, 1);
                            $dur = '';
                            if ($vid->duracao) {
                                $m = floor($vid->duracao / 60);
                                $s = $vid->duracao % 60;
                                $dur = "{$m}:{$s}";
                            }
                        ?>
                            <div class="win-card video-row" id="vrow-<?= $vid->id ?>" style="display:flex;align-items:center;gap:1rem;padding:1rem 1.2rem;margin-bottom:0.75rem;">
                                <div style="width:52px;height:52px;background:rgba(244,208,63,0.08);border-radius:var(--wh-radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-film" style="color:rgba(244,208,63,0.5);font-size:1.4rem;"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <strong style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        <?= htmlspecialchars($vid->titulo, ENT_QUOTES, 'UTF-8') ?>
                                    </strong>
                                    <span style="font-size:0.78rem;color:var(--wh-text-soft);">
                                        <?= $mb ?> MB<?= $dur ? " · {$dur}" : '' ?>
                                        · ID <?= $vid->id ?>
                                    </span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-light" onclick="excluirVideo(<?= $vid->id ?>)" title="Excluir">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="win-empty-state" id="msg-vazio">
                            Nenhum vídeo enviado ainda.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </section>
</div>

<?= $v->start("scripts") ?>
<script>
const ROUTE_UPLOAD  = "<?= $router->route('videos.upload') ?>";
const ROUTE_EXCLUIR = "<?= $router->route('videos.excluir') ?>";

$('#arquivo-video').on('change', function () {
    const nome = this.files[0] ? this.files[0].name : 'Escolher vídeo';
    $('#label-arquivo').text(nome);
});

$('#btn-upload').on('click', function () {
    const arquivo = $('#arquivo-video')[0].files[0];
    const titulo  = $('#titulo-video').val().trim();

    if (!arquivo) { alert('Selecione um vídeo.'); return; }
    if (!titulo)  { alert('Informe o título.'); return; }

    const MAX_MB  = 800;
    const MAX_BYTES = MAX_MB * 1024 * 1024;
    if (arquivo.size > MAX_BYTES) {
        const sizeMb = (arquivo.size / 1048576).toFixed(1);
        alert(`Arquivo muito grande: ${sizeMb} MB.\nO limite é ${MAX_MB} MB. Comprima o vídeo antes de enviar.`);
        return;
    }

    const form = new FormData();
    form.append('arquivo', arquivo);
    form.append('titulo',  titulo);

    $('#upload-progress-wrap').show();
    $('#btn-upload').prop('disabled', true);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', ROUTE_UPLOAD);

    xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
            const pct = Math.round((e.loaded / e.total) * 100);
            $('#upload-bar').css('width', pct + '%');
            $('#upload-pct-txt').text(pct + '%');
        }
    };

    xhr.onload = function () {
        let res;
        try { res = JSON.parse(xhr.responseText); } catch (e) { res = {ok: false, msg: 'Resposta inválida.'}; }

        if (res.ok) {
            $('#upload-status-txt').text('Enviado com sucesso!');
            // adiciona na lista
            $('#msg-vazio').remove();
            $('#lista-videos').prepend(`
                <div class="win-card video-row" id="vrow-${res.id}" style="display:flex;align-items:center;gap:1rem;padding:1rem 1.2rem;margin-bottom:0.75rem;">
                    <div style="width:52px;height:52px;background:rgba(244,208,63,0.08);border-radius:var(--wh-radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-film" style="color:rgba(244,208,63,0.5);font-size:1.4rem;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <strong style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${res.titulo}</strong>
                        <span style="font-size:0.78rem;color:var(--wh-text-soft);">ID ${res.id}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="excluirVideo(${res.id})" title="Excluir">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </div>
            `);
            $('#titulo-video').val('');
            $('#arquivo-video').val('');
            $('#label-arquivo').text('Escolher vídeo');
        } else {
            alert(res.msg || 'Erro no upload.');
            $('#upload-status-txt').text('Erro.');
        }

        setTimeout(() => {
            $('#upload-progress-wrap').hide();
            $('#upload-bar').css('width', '0%');
            $('#upload-pct-txt').text('0%');
            $('#upload-status-txt').text('Enviando…');
        }, 2500);

        $('#btn-upload').prop('disabled', false);
    };

    xhr.onerror = function () {
        alert('Falha na conexão.');
        $('#btn-upload').prop('disabled', false);
    };

    xhr.send(form);
});

function excluirVideo(id) {
    if (!confirm('Excluir este vídeo? Ele será removido de todas as aulas que o utilizarem.')) return;
    $.post(ROUTE_EXCLUIR, { id: id }, function (r) {
        if (r.ok) {
            $('#vrow-' + id).fadeOut(300, function () { $(this).remove(); });
        } else {
            alert(r.msg || 'Erro ao excluir.');
        }
    }, 'json');
}
</script>
<?= $v->end() ?>

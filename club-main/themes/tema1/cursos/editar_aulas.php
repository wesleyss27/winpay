<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}

$aula = $aulass[0] ?? null;
$imagemAtual = $aula && $aula->imagem ? url("uploads/cursos/" . $aula->imagem) : "";
$anexo1 = $aula->arquivo_anexo1 ?? '';
$anexo2 = $aula->arquivo_anexo2 ?? '';
$anexo3 = $aula->arquivo_anexo3 ?? '';
?>

<div class="content-wrapper win-page">
    <section class="content">
        <div class="container-fluid">
            <header class="win-page-header">
                <div class="win-page-title">
                    <small>Gestão de conteúdo</small>
                    <h1>Editar aula</h1>
                </div>
                <div class="win-page-actions">
                    <a href="<?= url("cursos/detalhe/" . $curso->id) ?>" class="btn-win btn-win-ghost">
                        <i class="fas fa-arrow-left"></i>
                        Voltar para o curso
                    </a>
                </div>
            </header>

            <?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

            <section class="win-section">
                <form action="<?= $router->route("appcursos.cadastroeditaraulas"); ?>" id="form" method="post" enctype="multipart/form-data" class="win-form">
                    <div class="win-form-grid">
                        <div class="form-group">
                            <label for="aulas_nome">Nome da aula</label>
                            <input type="text" class="form-control" id="aulas_nome" name="aulas_nome" placeholder="Ex.: Aula 1" required value="<?= $aula->nome ?? '' ?>">
                        </div>

                        <?php if ($modulos) : ?>
                            <div class="form-group">
                                <label for="modulo_sel">Módulo</label>
                                <select id="modulo_sel" class="form-control" name="modulo_sel" required>
                                    <option value="" disabled>Selecione...</option>
                                    <?php foreach ($modulos as $modulo) : ?>
                                        <option value="<?= $modulo->id; ?>" <?= ($aula && $aula->modulo == $modulo->id) ? 'selected' : '' ?>><?= $modulo->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="link_video">Link do vídeo</label>
                            <input type="url" class="form-control" id="link_video" name="link_video" value="<?= $aula->link_video ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="script_player">Script do player (opcional)</label>
                            <textarea class="form-control" id="script_player" name="script_player" rows="5"><?= $aula && $aula->script_player ? base64_decode($aula->script_player) : '' ?></textarea>
                        </div>
                    </div>

                    <div class="win-divider"></div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label for="imageInput">Imagem da aula</label>
                            <div class="attachment">
                                <label class="filelabel image-filelabel" for="imageInput" data-default="<?= $aula->imagem ?? 'Escolher imagem' ?>">
                                    <i class="fa fa-image"></i>
                                    <span class="title1"><?= $aula->imagem ?? 'Escolher imagem' ?></span>
                                </label>
                                <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                                <img src="<?= $imagemAtual; ?>" alt="Pré-visualização" class="image-preview" style="<?= $imagemAtual ? '' : 'display:none;' ?> border-radius: var(--wh-radius-sm); width: 120px; height: 120px; object-fit: cover;">
                                <span class="change-image" style="<?= $imagemAtual ? '' : 'display:none;' ?> font-size: 0.8rem; color: var(--wh-text-soft); cursor: pointer;">Remover imagem</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="aulas_descricao">Conteúdo da aula</label>
                            <textarea class="form-control" id="aulas_descricao" name="aulas_descricao" rows="6"><?= $aula->descricao ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="win-divider"></div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="attachment">
                            <label class="filelabel1" for="FileInput1" data-default="<?= $anexo1 ?: 'Adicionar anexo 1' ?>">
                                <i class="fa fa-paperclip" style="<?= $anexo1 ? 'color: var(--wh-accent);' : '' ?>"></i>
                                <span class="title1" style="<?= $anexo1 ? 'color: var(--wh-accent);' : '' ?>"><?= $anexo1 ?: 'Adicionar anexo 1' ?></span>
                            </label>
                            <input class="FileUpload1" id="FileInput1" name="arquivo_anexo1" type="file">
                        </div>

                        <div class="attachment">
                            <label class="filelabel2" for="FileInput2" data-default="<?= $anexo2 ?: 'Adicionar anexo 2' ?>">
                                <i class="fa fa-paperclip" style="<?= $anexo2 ? 'color: var(--wh-accent);' : '' ?>"></i>
                                <span class="title2" style="<?= $anexo2 ? 'color: var(--wh-accent);' : '' ?>"><?= $anexo2 ?: 'Adicionar anexo 2' ?></span>
                            </label>
                            <input class="FileUpload2" id="FileInput2" name="arquivo_anexo2" type="file">
                        </div>

                        <div class="attachment">
                            <label class="filelabel3" for="FileInput3" data-default="<?= $anexo3 ?: 'Adicionar anexo 3' ?>">
                                <i class="fa fa-paperclip" style="<?= $anexo3 ? 'color: var(--wh-accent);' : '' ?>"></i>
                                <span class="title3" style="<?= $anexo3 ? 'color: var(--wh-accent);' : '' ?>"><?= $anexo3 ?: 'Adicionar anexo 3' ?></span>
                            </label>
                            <input class="FileUpload3" id="FileInput3" name="arquivo_anexo3" type="file">
                        </div>

                        <div class="attachment_links">
                            <label for="links">Links externos</label>
                            <input class="form-control" id="links" name="links" type="text" value="<?= $aula->links ?? '' ?>" placeholder="https://..." />
                        </div>
                    </div>

                    <?php if (!empty($aulass)) : ?>
                        <input type="hidden" name="aulas_id" value="<?= $aula->id; ?>">
                        <input type="hidden" name="anexo1_img1" value="<?= $anexo1; ?>">
                        <input type="hidden" name="anexo2_img2" value="<?= $anexo2; ?>">
                        <input type="hidden" name="anexo3_img3" value="<?= $anexo3; ?>">
                    <?php endif; ?>

                    <input type="hidden" name="course_id" value="<?= $curso->id; ?>">
                    <input type="hidden" name="course_img" value="<?= $curso->imagem; ?>">

                    <div class="win-form-actions">
                        <a href="<?= url("cursos/detalhe/" . $curso->id) ?>" class="btn-win btn-win-ghost">Cancelar</a>
                        <button type="submit" name="next-step" class="btn-win">
                            Salvar alterações
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</div>

<?= $v->start("scripts") ?>
<script>
    $(function() {
        function updateFileLabel($input, $label, fallbackText) {
            const fileName = $input.val().split('\\').pop();
            const ext = fileName.split('.').pop().toLowerCase();
            const $icon = $label.find('i');
            const defaultText = fallbackText || $label.data('default') || 'Selecionar arquivo';

            const iconMap = {
                image: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                pdf: ['pdf'],
                word: ['doc', 'docx'],
                video: ['mp4', 'mov', 'wmv', 'avi', 'mkv'],
                archive: ['zip', 'rar', '7z']
            };

            let iconClass = 'fa-file';
            let color = 'var(--wh-text)';

            if (iconMap.image.includes(ext)) {
                iconClass = 'fa-file-image';
                color = '#3dd68e';
            } else if (iconMap.pdf.includes(ext)) {
                iconClass = 'fa-file-pdf';
                color = '#ff6b81';
            } else if (iconMap.word.includes(ext)) {
                iconClass = 'fa-file-word';
                color = '#2c7be5';
            } else if (iconMap.video.includes(ext)) {
                iconClass = 'fa-file-video';
                color = '#f4d03f';
            } else if (iconMap.archive.includes(ext)) {
                iconClass = 'fa-file-archive';
                color = '#a770ef';
            }

            $icon.attr('class', `fa ${iconClass}`).css('color', color);

            if (fileName) {
                const displayName = fileName.length > 18 ? `${fileName.slice(0, 8)}…${ext}` : fileName;
                $label.find('.title1, .title2, .title3, .title').text(displayName).css('color', color);
                $label.css('border-color', color);
            } else {
                $label.find('.title1, .title2, .title3, .title').text(defaultText).css('color', defaultText !== 'Selecionar arquivo' ? 'var(--wh-accent)' : '');
                $label.css('border-color', defaultText !== 'Selecionar arquivo' ? 'var(--wh-accent)' : '');
                $icon.css('color', defaultText !== 'Selecionar arquivo' ? 'var(--wh-accent)' : '');
            }
        }

        $('#FileInput1').on('change', function() {
            updateFileLabel($(this), $('label[for="FileInput1"]'));
        });

        $('#FileInput2').on('change', function() {
            updateFileLabel($(this), $('label[for="FileInput2"]'));
        });

        $('#FileInput3').on('change', function() {
            updateFileLabel($(this), $('label[for="FileInput3"]'));
        });

        $('#imageInput').on('change', function() {
            const input = this;
            const $label = $('.image-filelabel');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('.image-preview').attr('src', e.target.result).show();
                    $('.change-image').show();
                };
                reader.readAsDataURL(input.files[0]);
                $label.find('.title1').text(input.files[0].name);
                $label.css('border-color', 'var(--wh-accent)');
                $label.find('i').css('color', 'var(--wh-accent)');
            }
        });

        $('.change-image').on('click', function() {
            $('#imageInput').val('');
            $('.image-preview').attr('src', '').hide();
            $(this).hide();
            const $label = $('.image-filelabel');
            const defaultText = $label.data('default') || 'Escolher imagem';
            $label.find('.title1').text(defaultText);
            $label.css('border-color', '');
            $label.find('i').css('color', '');
        });
    });
</script>
<?= $v->end() ?>

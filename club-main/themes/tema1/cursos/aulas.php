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
                    <small>Construção do curso</small>
                    <h1><?= $title ?></h1>
                </div>
                <div class="win-page-actions">
                    <a href="<?= url("cursos/modulo/" . $course_id) ?>" class="btn-win btn-win-ghost">
                        <i class="fas fa-arrow-left"></i>
                        Voltar para módulos
                    </a>
                </div>
            </header>

            <section class="win-section">
                <?php
                $v->insert("cursos/partials/stepper", [
                    'steps' => [
                        'Cadastro de curso',
                        'Cadastro de módulo',
                        'Cadastro de aula'
                    ],
                    'active' => 2
                ]);
                ?>

                <form action="<?= $router->route("appcursos.cadastroaulas"); ?>" method="post" enctype="multipart/form-data" class="win-form">
                    <div class="win-form-grid">
                        <div class="form-group">
                            <label for="aulas_nome">Nome da aula</label>
                            <input type="text" class="form-control" id="aulas_nome" name="aulas_nome" placeholder="Ex.: Boas-vindas" required>
                        </div>

                        <?php if ($modulos) : ?>
                            <div class="form-group">
                                <label for="modulo_sel">Módulo</label>
                                <select id="modulo_sel" class="form-control" name="modulo_sel" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <?php foreach ($modulos as $modulo) : ?>
                                        <option value="<?= $modulo->id; ?>"><?= $modulo->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="dias_liberacao">Liberar após (dias)</label>
                            <input type="number" class="form-control" id="dias_liberacao" name="dias_liberacao" min="0" placeholder="Ex.: 7 — deixe vazio para liberar imediatamente">
                            <small class="win-form-hint" style="display:block;margin-top:0.35rem;color:var(--wh-text-soft);">Número de dias após o cadastro do aluno para liberar esta aula. Deixe vazio para liberar imediatamente.</small>
                        </div>

                        <div class="form-group">
                            <label for="link_video">Link do vídeo</label>
                            <input type="url" class="form-control" id="link_video" name="link_video" placeholder="https://...">
                            <small class="text-muted">Cole a URL do player principal ou deixe vazio se for usar script incorporado.</small>
                        </div>

                        <div class="form-group">
                            <label for="script_player">Script do player (opcional)</label>
                            <textarea class="form-control" id="script_player" name="script_player" rows="5" placeholder="Cole o script ou código de incorporação"></textarea>
                        </div>
                    </div>

                    <div class="win-divider"></div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label for="imageInput">Imagem da aula</label>
                            <small class="win-form-hint" style="display:block;margin-top:0.35rem;color:var(--wh-text-soft);">Recomendado: 720x1080px (proporção 2:3).</small>
                            <div class="attachment">
                                <label class="filelabel image-filelabel" for="imageInput" data-default="Escolher imagem">
                                    <i class="fa fa-image"></i>
                                    <span class="title1">Escolher imagem</span>
                                </label>
                                <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                                <img src="" alt="" class="image-preview" style="display:none; border-radius: var(--wh-radius-sm); width: 120px; height: 120px; object-fit: cover;">
                                <span class="change-image" style="display:none; font-size: 0.8rem; color: var(--wh-text-soft); cursor: pointer;">Escolher outra imagem</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="aulas_descricao">Conteúdo da aula</label>
                            <textarea class="form-control" id="aulas_descricao" name="aulas_descricao" rows="6" placeholder="Resumo, objetivos ou materiais complementares"></textarea>
                        </div>
                    </div>

                    <div class="win-divider"></div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="attachment">
                            <label class="filelabel1" for="FileInput1" data-default="Adicionar anexo 1">
                                <i class="fa fa-paperclip"></i>
                                <span class="title1">Adicionar anexo 1</span>
                            </label>
                            <input class="FileUpload1" id="FileInput1" name="arquivo_anexo1" type="file">
                        </div>

                        <div class="attachment">
                            <label class="filelabel2" for="FileInput2" data-default="Adicionar anexo 2">
                                <i class="fa fa-paperclip"></i>
                                <span class="title2">Adicionar anexo 2</span>
                            </label>
                            <input class="FileUpload2" id="FileInput2" name="arquivo_anexo2" type="file">
                        </div>

                        <div class="attachment">
                            <label class="filelabel3" for="FileInput3" data-default="Adicionar anexo 3">
                                <i class="fa fa-paperclip"></i>
                                <span class="title3">Adicionar anexo 3</span>
                            </label>
                            <input class="FileUpload3" id="FileInput3" name="arquivo_anexo3" type="file">
                        </div>

                        <div class="attachment_links">
                            <label for="links">Links externos</label>
                            <input class="form-control" id="links" name="links" type="text" placeholder="https://...">
                        </div>
                    </div>

                    <?php if (!empty($cursoss)) : ?>
                        <input type="hidden" name="course_id" value="<?= $cursoss[0]->id; ?>">
                        <input type="hidden" name="course_img" value="<?= $cursoss[0]->imagem; ?>">
                        <input type="hidden" name="anexo1_img1" value="<?= $aulass[0]->arquivo_anexo1 ?? ''; ?>">
                        <input type="hidden" name="anexo2_img2" value="<?= $aulass[0]->arquivo_anexo2 ?? ''; ?>">
                        <input type="hidden" name="anexo3_img3" value="<?= $aulass[0]->arquivo_anexo3 ?? ''; ?>">
                    <?php endif; ?>

                    <input type="hidden" name="course_id" value="<?= $course_id; ?>">

                    <div class="win-form-actions">
                        <a href="<?= url("cursos/modulo/" . $course_id) ?>" class="btn-win btn-win-ghost">
                            Cancelar
                        </a>
                        <button type="submit" name="submit-form" class="btn-win submit_form_btn">
                            Salvar aula
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
                $label.find('.title1, .title2, .title3, .title').text(defaultText).css('color', '');
                $label.css('border-color', '');
                $icon.css('color', '');
            }
        }

        $('#FileInput1').on('change', function() {
            updateFileLabel($(this), $('label[for="FileInput1"]'), 'Adicionar anexo 1');
        });

        $('#FileInput2').on('change', function() {
            updateFileLabel($(this), $('label[for="FileInput2"]'), 'Adicionar anexo 2');
        });

        $('#FileInput3').on('change', function() {
            updateFileLabel($(this), $('label[for="FileInput3"]'), 'Adicionar anexo 3');
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

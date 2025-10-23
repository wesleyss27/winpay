<?php
$v->layout("_theme");

require __DIR__ . "/categorias.php";

$imagemAtual = $curso->imagem ? url("uploads/cursos/" . $curso->imagem) : "";
?>

<div class="content-wrapper win-page">
    <section class="content">
        <div class="container-fluid">
            <header class="win-page-header">
                <div class="win-page-title">
                    <small>Gestão de conteúdo</small>
                    <h1>Editar curso</h1>
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
                <form action="<?= $router->route("appcursos.editarcadastro"); ?>" id="form" method="post" enctype="multipart/form-data" class="win-form">
                    <div class="win-form-grid">
                        <div class="form-group">
                            <label for="nome">Nome do curso *</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required value="<?= $curso->nome; ?>">
                        </div>

                        <?php if ($produtos) : ?>
                            <div class="form-group">
                                <label for="produto">Produto associado *</label>
                                <select id="produto" class="form-control" name="produto" required>
                                    <?php foreach ($produtos as $produto) : ?>
                                        <option value="<?= $produto->id; ?>" <?= ($curso->produto == $produto->id) ? 'selected' : '' ?>><?= $produto->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="assunto">Categoria *</label>
                            <select id="assunto" class="form-control" name="assunto" required>
                                <option value="" disabled>Selecione...</option>
                                <?php foreach ($categorias as $id => $categoria) : ?>
                                    <option value="<?= $id; ?>" <?= ($curso->assunto == (int)$id) ? 'selected' : '' ?>><?= $categoria; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="formato">Formato *</label>
                            <select id="formato" class="form-control" name="formato" required>
                                <option value="" disabled>Selecione...</option>
                                <?php foreach ($formato as $id => $label) : ?>
                                    <option value="<?= $id; ?>" <?= ($curso->formato == (int)$id) ? 'selected' : '' ?>><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="liberacao">Liberação dos módulos</label>
                            <select name="liberacao" id="liberacao" class="form-control">
                                <option value="">Selecionar...</option>
                                <option value="1" <?= ($curso->liberacao == 1) ? 'selected' : '' ?>>Liberar imediatamente</option>
                                <option value="3" <?= ($curso->liberacao == 3) ? 'selected' : '' ?>>Segundo módulo depois de 7 dias</option>
                            </select>
                        </div>
                    </div>

                    <div class="win-divider"></div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label for="imageInput">Imagem do curso</label>
                            <div class="attachment">
                                <label class="filelabel image-filelabel" for="imageInput" data-default="<?= $curso->imagem ?: 'Escolher imagem'; ?>">
                                    <i class="fa fa-image" style="<?= $curso->imagem ? 'color: var(--wh-accent);' : '' ?>"></i>
                                    <span class="title1" style="<?= $curso->imagem ? 'color: var(--wh-accent);' : '' ?>"><?= $curso->imagem ?: 'Escolher imagem'; ?></span>
                                </label>
                                <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                                <img src="<?= $imagemAtual; ?>" alt="Pré-visualização" class="image-preview" style="<?= $imagemAtual ? '' : 'display:none;' ?> border-radius: var(--wh-radius-sm); width: 140px; height: 140px; object-fit: cover;">
                                <span class="change-image" style="<?= $imagemAtual ? '' : 'display:none;' ?> font-size: 0.8rem; color: var(--wh-text-soft); cursor: pointer;">Remover imagem</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição do curso</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="8" required><?= $curso->descricao; ?></textarea>
                        </div>
                    </div>

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

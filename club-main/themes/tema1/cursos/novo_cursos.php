<?php
$v->layout("_theme");
require __DIR__ . "/categorias.php";

$cursoAtual = $cursoss[0] ?? null;
$imagemAtual = $cursoAtual && $cursoAtual->imagem ? url("uploads/cursos/" . $cursoAtual->imagem) : "";

if ($_SESSION["tipo"] == 6) {
	header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper win-page">
	<section class="content">
		<div class="container-fluid">
			<header class="win-page-header">
				<div class="win-page-title">
					<small>Cadastro</small>
					<h1><?= $title ?></h1>
				</div>
				<div class="win-page-actions">
					<a href="<?= url("cursos") ?>" class="btn-win btn-win-ghost">
						<i class="fas fa-arrow-left"></i>
						Voltar
					</a>
				</div>
			</header>

			<section class="win-section">
        <form action="<?= $router->route("appcursos.cadastro"); ?>" id="form" method="post" enctype="multipart/form-data" class="win-form">
            <?php
            $v->insert("cursos/partials/stepper", [
                'steps' => [
                    'Cadastro de curso',
                    'Cadastro do módulo',
                    'Cadastro de aula'
                ],
                'active' => 0
            ]);
            ?>

            <div class="win-form-grid">
                <div class="form-group">
                    <label for="nome">Nome do curso *</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do curso" required value="<?= $cursoAtual->nome ?? ''; ?>">
                </div>

                <?php if ($produtos) : ?>
                    <div class="form-group">
                        <label for="produto">Produto *</label>
                        <select id="produto" class="form-control" name="produto" required>
                            <option value="" disabled <?= $cursoAtual ? '' : 'selected'; ?>>Selecione...</option>
                            <?php foreach ($produtos as $produto) : ?>
                                <option value="<?= $produto->id; ?>" <?= ($cursoAtual && $cursoAtual->produto == $produto->id) ? 'selected' : ''; ?>>
                                    <?= $produto->nome; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="assunto">Assunto *</label>
                    <select id="assunto" class="form-control" name="assunto" required>
                        <option value="" disabled <?= $cursoAtual ? '' : 'selected'; ?>>Selecione...</option>
                        <?php foreach ($categorias as $id => $categoria) : ?>
                            <option value="<?= $id; ?>" <?= ($cursoAtual && (int)$cursoAtual->assunto === (int)$id) ? 'selected' : ''; ?>>
                                <?= $categoria; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="formato">Formato *</label>
                    <select id="formato" class="form-control" name="formato" required>
                        <option value="" disabled <?= $cursoAtual ? '' : 'selected'; ?>>Selecione...</option>
                        <?php foreach ($formato as $id => $label) : ?>
                            <option value="<?= $id; ?>" <?= ($cursoAtual && (int)$cursoAtual->formato === (int)$id) ? 'selected' : ''; ?>>
                                <?= $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="liberacao">Liberação dos módulos</label>
                    <select name="liberacao" id="liberacao" class="form-control">
                        <option value="" disabled <?= $cursoAtual && $cursoAtual->liberacao ? '' : 'selected'; ?>>Selecionar...</option>
                        <option value="1" <?= ($cursoAtual && $cursoAtual->liberacao == 1) ? 'selected' : ''; ?>>Liberar imediatamente</option>
                        <option value="3" <?= ($cursoAtual && $cursoAtual->liberacao == 3) ? 'selected' : ''; ?>>Segundo módulo depois de 7 dias</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição do curso</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="6" placeholder="Explique o conteúdo e os objetivos" required><?= $cursoAtual->descricao ?? ''; ?></textarea>
                </div>
            </div>

            <div class="win-divider"></div>

            <div class="win-form-grid win-form-grid-2">
                <div class="form-group">
                    <label for="imageInput">Imagem do curso</label>
                    <div class="attachment">
                        <label class="filelabel image-filelabel" for="imageInput" data-default="<?= $cursoAtual->imagem ?: 'Escolher imagem'; ?>">
                            <i class="fa fa-image" style="<?= $cursoAtual && $cursoAtual->imagem ? 'color: var(--wh-accent);' : ''; ?>"></i>
                            <span class="title1" style="<?= $cursoAtual && $cursoAtual->imagem ? 'color: var(--wh-accent);' : ''; ?>"><?= $cursoAtual->imagem ?: 'Escolher imagem'; ?></span>
                        </label>
                        <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                        <img src="<?= $imagemAtual; ?>" alt="Pré-visualização" class="image-preview" style="<?= $imagemAtual ? '' : 'display:none;'; ?> border-radius: var(--wh-radius-sm); width: 140px; height: 140px; object-fit: cover;">
                        <span class="change-image" style="<?= $imagemAtual ? '' : 'display:none;'; ?> font-size: 0.8rem; color: var(--wh-text-soft); cursor: pointer;">Remover imagem</span>
                    </div>
                </div>
            </div>

            <?php if ($cursoAtual) : ?>
                <input type="hidden" name="course_id" value="<?= $cursoAtual->id; ?>">
                <input type="hidden" name="course_img" value="<?= $cursoAtual->imagem; ?>">
            <?php endif; ?>

            <div class="win-form-actions">
                <button type="submit" name="next-step" class="btn-win">
                    Continuar
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
			</section>
		</div>
	</section>

	<a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
		<i class="fas fa-chevron-up"></i>
	</a>

</div>
<?= $v->start("scripts") ?>
<script>
	$(function() {
		const $imageInput = $('#imageInput');
		const $label = $('.image-filelabel');
		const $preview = $('.image-preview');
		const $change = $('.change-image');

		$imageInput.on('change', function() {
			const [file] = this.files;
			if (file) {
				const reader = new FileReader();
				reader.onload = function(e) {
					$preview.attr('src', e.target.result).show();
					$change.show();
				};
				reader.readAsDataURL(file);
				$label.find('.title1').text(file.name);
				$label.css('border-color', 'var(--wh-accent)');
				$label.find('i').css('color', 'var(--wh-accent)');
			}
		});

		$change.on('click', function() {
			$imageInput.val('');
			$preview.attr('src', '').hide();
			$(this).hide();
			const defaultText = $label.data('default') || 'Escolher imagem';
			$label.find('.title1').text(defaultText);
			$label.css('border-color', '');
			$label.find('i').css('color', '');
		});
	});
</script>
<?= $v->end() ?>

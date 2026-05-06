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
					<h1>Editar módulo</h1>
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
				<form action="<?= $router->route("appcursos.cadastreditoromodulo"); ?>" id="form" method="post" enctype="multipart/form-data" class="win-form">
					<div class="win-form-grid">
						<div class="form-group">
							<label for="module_nome">Nome do módulo</label>
							<input type="text" class="form-control" id="module_nome" name="module_nome" placeholder="Ex.: Módulo 1" required value="<?= @$moduloss[0]->nome; ?>">
						</div>

						<div class="form-group">
							<label for="module_descricao">Descrição do módulo</label>
							<textarea class="form-control" id="module_descricao" name="module_descricao" rows="6" required><?= @$moduloss[0]->descricao; ?></textarea>
						</div>

						<div class="form-group">
							<label for="modImgInput">Imagem do módulo</label>
							<small class="win-form-hint" style="display:block;margin-top:.35rem;color:var(--wh-text-soft);">Ideal: <strong>900×1600px</strong> (proporção 9:16, portrait) — exibida no card vertical da página de módulos.</small>
							<?php $modImgAtual = !empty($moduloss[0]->imagem) ? url("uploads/cursos/" . $moduloss[0]->imagem) : ""; ?>
							<div class="attachment">
								<label class="filelabel mod-img-label" for="modImgInput" data-default="<?= $moduloss[0]->imagem ?? 'Escolher imagem' ?>">
									<i class="fa fa-image" style="<?= $modImgAtual ? 'color:var(--wh-accent);' : '' ?>"></i>
									<span class="mod-img-label-text" style="<?= $modImgAtual ? 'color:var(--wh-accent);' : '' ?>"><?= $moduloss[0]->imagem ?? 'Escolher imagem' ?></span>
								</label>
								<input type="file" accept="image/*" id="modImgInput" class="FileUpload1" name="modulo_imagem">
								<img src="<?= $modImgAtual ?>" alt="" class="mod-img-preview" style="<?= $modImgAtual ? '' : 'display:none;' ?>border-radius:8px;width:100px;height:150px;object-fit:cover;margin-top:.5rem;">
								<span class="change-mod-img" style="<?= $modImgAtual ? '' : 'display:none;' ?>font-size:.8rem;color:var(--wh-text-soft);cursor:pointer;">Remover imagem</span>
							</div>
							<input type="hidden" name="modulo_imagem_atual" value="<?= htmlspecialchars($moduloss[0]->imagem ?? '', ENT_QUOTES, 'UTF-8') ?>">
						</div>
					</div>

					<?php if (!empty($moduloss)) : ?>
						<input type="hidden" name="module_id" value="<?= @$moduloss[0]->id; ?>">
					<?php endif; ?>
					<input type="hidden" name="course_id" value="<?= @$course_id; ?>">

					<div class="win-form-actions">
						<a href="<?= url("cursos/detalhe/" . $curso->id) ?>" class="btn-win btn-win-ghost">
							Cancelar
						</a>
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
$(function(){
    $('#modImgInput').on('change', function(){
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e){
                $('.mod-img-preview').attr('src', e.target.result).show();
                $('.change-mod-img').show();
            };
            reader.readAsDataURL(this.files[0]);
            $('.mod-img-label .mod-img-label-text').text(this.files[0].name).css('color','var(--wh-accent)');
            $('.mod-img-label i').css('color','var(--wh-accent)');
        }
    });
    $('.change-mod-img').on('click', function(){
        $('#modImgInput').val('');
        $('input[name=modulo_imagem_atual]').val('');
        $('.mod-img-preview').attr('src','').hide();
        $(this).hide();
        $('.mod-img-label .mod-img-label-text').text('Escolher imagem').css('color','');
        $('.mod-img-label i').css('color','');
    });
});
</script>
<?= $v->end() ?>

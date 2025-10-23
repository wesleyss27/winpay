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

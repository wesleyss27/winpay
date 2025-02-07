<?php
$v->layout("_theme");
if ($_SESSION["tipo"] == 6) {
	header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->

	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 style="font-family:inter;padding-left: 15px;">Editar Módulo</h1>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section>

	<!-- Main content -->
	<section class="content">
		<?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

		<div class="card card-body">
			<form action="<?= $router->route("appcursos.cadastreditoromodulo"); ?>" id="form" method="post" enctype="multipart/form-data">

				<div class="form-container_box" style="padding-top: 0;">

					<fieldset>

						<div class="form-container">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-12 form-group">
									<div class="edit_name">
										<label for="formGroupExampleInput">Nome do Módulo</label>
										<input type="text" class="form-control" id="formGroupExampleInput" placeholder="Módulo 1" name="module_nome" required value="<?= @$moduloss[0]->nome; ?>">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12 col-md-12 col-12 form-group">
									<div class="edit_textarea">
										<label for="formGroupExampleInput">Descrição do módulo</label>
										<textarea class="form-control" id="exampleFormControlTextarea1" rows="6" placeholder="Módulo 1" name="module_descricao" required><?= @$moduloss[0]->descricao;  ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<?php
						if (!empty(@$moduloss)) {
						?>
							<input type="hidden" name="module_id" value="<?= @$moduloss[0]->id; ?>">
						<?php
						}
						?>
						<div class="form_next-prev px-3">
							<input type="hidden" name="course_id" value="<?= @$course_id; ?>">
							<input type="submit" name="next-step" class="nxt_step" value="Salvar" />
						</div>
					</fieldset>

				</div>
			</form>
		</div>

	</section>

</div>
<style>
	section.content {
		padding-top: 0px !important;
	}

	.content-header {
		padding-bottom: 0px;
	}
</style>
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
					<h1 style="font-family:inter;padding-left: 15px;"><?= $title ?></h1>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section>

	<!-- Main content -->
	<section class="content">
		<?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

		<style>
			#form {
				text-align: left !important;
			}
		</style>
		<?php if (@$_GET["msg"]) : ?>
			<div class="alert alert-info">
				<?= $_GET['msg'] ?>
			</div>
		<?php endif ?>

		<?php
		$fundo = @$certificado->fundo ? url("uploads/cursos/$certificado->fundo") : url("themes/imgs/background-certificado.jpg");
		$fonte_titulo = @$certificado->fonte_titulo ? $certificado->fonte_titulo : 'REM';
		$cor_titulo = @$certificado->cor_titulo ? $certificado->cor_titulo : '#1D465F';
		$fonte_subtitulo = @$certificado->fonte_subtitulo ? $certificado->fonte_subtitulo : 'Great Vibes';
		$cor_subtitulo = @$certificado->cor_subtitulo ? $certificado->cor_subtitulo : '#000';
		$fonte_destaque = @$certificado->fonte_destaque ? $certificado->fonte_destaque : 'Poppins';
		$cor_destaque = @$certificado->cor_destaque ? $certificado->cor_destaque : '#000';
		$fonte_descricao = @$certificado->fonte_descricao ? $certificado->fonte_descricao : 'Poppins';
		$cor_descricao = @$certificado->cor_descricao ? $certificado->cor_descricao : '#000';
		?>

		<div class="card card-body">
			<form action="<?= $router->route("certificados.salvar"); ?>" id="form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="curso" value="<?= $curso->id ?>">
				<input type="hidden" name="id" value="<?= @$certificado->id ?>">

				<div class="row">
					<div class="col-md-3">
						<label for="">Título</label>
						<select name="fonte_titulo" id="" class="form-control">
							<option value="Anton" <?= @$fonte_titulo == "Anton" ? 'selected' : '' ?>>Anton</option>
							<option value="Arial" <?= @$fonte_titulo == "Arial" ? 'selected' : '' ?>>Arial</option>
							<option value="Great Vibes" <?= @$fonte_titulo == "Great Vibes" ? 'selected' : '' ?>>Great Vibes</option>
							<option value="Poppins" <?= @$fonte_titulo == "Poppins" ? 'selected' : '' ?>>Poppins</option>
							<option value="REM" <?= @$fonte_titulo == "REM" ? 'selected' : '' ?>>REM</option>
							<option value="Times New Roman" <?= @$fonte_titulo == "Times New Roman" ? 'selected' : '' ?>>Times New Roman</option>
						</select>
					</div>

					<div class="col-md-3">
						<label for="">Cor do título</label><br>
						<input type="color" name="cor_titulo" id="" value="<?= @$cor_titulo ?>">
					</div>

					<div class="col-md-3">
						<label for="">Subtítulo</label>
						<select name="fonte_subtitulo" id="" class="form-control">
							<option value="Anton" <?= @$fonte_subtitulo == "Anton" ? 'selected' : '' ?>>Anton</option>
							<option value="Arial" <?= @$fonte_subtitulo == "Arial" ? 'selected' : '' ?>>Arial</option>
							<option value="Great Vibes" <?= @$fonte_subtitulo == "Great Vibes" ? 'selected' : '' ?>>Great Vibes</option>
							<option value="Poppins" <?= @$fonte_subtitulo == "Poppins" ? 'selected' : '' ?>>Poppins</option>
							<option value="REM" <?= @$fonte_subtitulo == "REM" ? 'selected' : '' ?>>REM</option>
							<option value="Times New Roman" <?= @$fonte_subtitulo == "Times New Roman" ? 'selected' : '' ?>>Times New Roman</option>
						</select>
					</div>

					<div class="col-md-3">
						<label for="">Cor do subtítulo</label><br>
						<input type="color" name="cor_subtitulo" id="" value="<?= @$cor_subtitulo ?>">
					</div>

				</div>
				<div class="row">
					<div class="col-md-3">
						<label for="">Destaque</label>
						<select name="fonte_destaque" id="" class="form-control">
							<option value="Anton" <?= @$fonte_destaque == "Anton" ? 'selected' : '' ?>>Anton</option>
							<option value="Arial" <?= @$fonte_destaque == "Arial" ? 'selected' : '' ?>>Arial</option>
							<option value="Great Vibes" <?= @$fonte_destaque == "Great Vibes" ? 'selected' : '' ?>>Great Vibes</option>
							<option value="Poppins" <?= @$fonte_destaque == "Poppins" ? 'selected' : '' ?>>Poppins</option>
							<option value="REM" <?= @$fonte_destaque == "REM" ? 'selected' : '' ?>>REM</option>
							<option value="Times New Roman" <?= @$fonte_destaque == "Times New Roman" ? 'selected' : '' ?>>Times New Roman</option>
						</select>
					</div>

					<div class="col-md-3">
						<label for="">Cor de destaque</label><br>
						<input type="color" name="cor_destaque" id="" value="<?= @$cor_destaque ?>">
					</div>

					<div class="col-md-3">
						<label for="">Descrição</label>
						<select name="fonte_descricao" id="" class="form-control">
							<option value="Anton" <?= @$fonte_descricao == "Anton" ? 'selected' : '' ?>>Anton</option>
							<option value="Arial" <?= @$fonte_descricao == "Arial" ? 'selected' : '' ?>>Arial</option>
							<option value="Great Vibes" <?= @$fonte_descricao == "Great Vibes" ? 'selected' : '' ?>>Great Vibes</option>
							<option value="Poppins" <?= @$fonte_descricao == "Poppins" ? 'selected' : '' ?>>Poppins</option>
							<option value="REM" <?= @$fonte_descricao == "REM" ? 'selected' : '' ?>>REM</option>
							<option value="Times New Roman" <?= @$fonte_descricao == "Times New Roman" ? 'selected' : '' ?>>Times New Roman</option>
						</select>
					</div>

					<div class="col-md-3">
						<label for="">Cor da descrição</label><br>
						<input type="color" name="cor_descricao" id="" value="<?= @$cor_descricao ?>">
					</div>
				</div>

				<br>
				<div class="row">
					<div class="col-md-3">
						<label for="">Fundo</label>
						<input type="file" name="fundo" id="" class="form-control">

						<input type="checkbox" name="fundo_padrao" id="" value="1"> Fundo Padrão
					</div>
					<div class="col-md-3">
						<label style="color:transparent">|</label><br>
						<?php if ($certificado) : ?>
							<button type="submit" class="btn btn-primary">Salvar</button>
						<?php else : ?>
							<button type="submit" class="btn btn-primary">Habilitar Certificado</button>
						<?php endif ?>
					</div>
				</div>
			</form>

			<br><br>

			<style>
				@import url("https://fonts.googleapis.com/css2?family=Anton&family=Great+Vibes&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=REM:wght@900&display=swap");

				.corpo {
					padding-top: 100px;
					height: 707px;
					width: 1000px;
					border: 1px solid #666;
					text-align: center;
					background: url(<?= $fundo ?>) no-repeat;
					background-size: cover;
					color: #000;
					/* display: flex;
                align-items: center; */
				}

				.titulo {
					text-align: center;
					font-size: 80px;
					font-family: <?= $fonte_titulo ?>;
					/* text-shadow: 2px 2px 2px rgb(247, 141, 2); */
					color: <?= $cor_titulo ?>;
				}

				.subtitulo {
					text-align: center;
					font-family: <?= $fonte_subtitulo ?>;
					font-size: 40px;
					margin-top: -40px;
					letter-spacing: 4px;
					color: <?= $cor_subtitulo ?>;
				}

				.descricao {
					font-size: 30px;
					font-family: <?= $fonte_descricao ?>;
					color: <?= $cor_descricao ?>;
				}

				.destaque {
					font-size: 40px;
					font-weight: 600;
					font-family: <?= $fonte_destaque ?>;
					color: <?= $cor_destaque ?>;
				}
			</style>

			<div class="corpo">
				<h1 class="titulo">CERTIFICADO</h1>
				<h2 class="subtitulo">de conclusão</h2>

				<br>
				<p class="descricao">
					Certificamos que <br>
					<span class="destaque"><?= $curso->dataProdutor->nome ?></span>
					<br><br>concluiu o curso <br>
					<span class="destaque"><?= $curso->nome ?></span>
				</p>

				<p class="descricao"><?= date('d/m/Y') ?></p>
			</div>
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
<?php
$v->layout("_theme");

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

	<!-- Content Header (Page header) -->
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 style="font-family:inter;padding-left: 15px;">Editar Curso</h1>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section>

	<!-- Main content -->
	<section class="content">

		<?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

		<div class="card card-body">
			<form action="<?= $router->route("appcursos.editarcadastro"); ?>" id="form" method="post" enctype="multipart/form-data" style="font-family:inter; text-transform:uppercase; font-size:11px">
				<div class="form-container_box">
					<fieldset>
						<strong>O registro começa aqui, vá passo a passo</strong>
						<input type="button" name="next-step" class="next-step nxt_step" value="Next Step" />
					</fieldset>

					<fieldset>
						<div class="form-container">
							<div class="row">
								<!-----left---->
								<div class="col-lg-8 col-md-8 col-12 left-area">
									<div class="row">
										<div class="col-lg-12 col-md-12 col-12 form-group">
											<div class="form-group">
												<label for="formGroupExampleInput">Nome do curso*</label>
												<input type="text" class="form-control" id="formGroupExampleInput" name="nome" placeholder="name" required value="<?= $curso->nome; ?>">
											</div>
										</div>
									</div>

									<?php if ($produtos) : ?>
										<div class="row">
											<div class="col-lg-12 col-md-12 col-12 form-group">
												<div class="inner-selct ">
													<label for="inputState">Produtos</label>
													<select id="inputState" class="form-control" name="produto" required>
														<?php foreach ($produtos as $produto) : ?>
															<option value="<?php echo $produto->id; ?>" <?= ($curso->produto == $produto->id) ? "selected" : "" ?>><?php echo $produto->nome; ?></option>
														<?php endforeach;  ?>
													</select>
												</div>
											</div>
										</div>
									<?php endif;  ?>

									<div class="row">
										<div class="col-lg-6 form-group">
											<div class="inner-selct ">
												<label for="inputState">Categoria*</label>
												<select id="inputState" class="form-control" name="assunto" required>
													<option disabled selected>-- Selecione --</option>
													<option value='1' <?= (@$curso->assunto == 1) ? 'selected' : '' ?>>Administração e Negócios</option>
													<option value='2' <?= (@$curso->assunto == 2) ? 'selected' : '' ?>>Animais de Estimação</option>
													<option value='3' <?= (@$curso->assunto == 3) ? 'selected' : '' ?>>Arquitetura e Engenharia</option>
													<option value='4' <?= (@$curso->assunto == 4) ? 'selected' : '' ?>>Artes e Música</option>
													<option value='5' <?= (@$curso->assunto == 5) ? 'selected' : '' ?>>Auto-ajuda e Desenvolvimento Pessoal</option>
													<option value='6' <?= (@$curso->assunto == 6) ? 'selected' : '' ?>>Automóveis</option>
													<option value='7' <?= (@$curso->assunto == 7) ? 'selected' : '' ?>>Blogs e Redes Sociais</option>
													<option value='8' <?= (@$curso->assunto == 8) ? 'selected' : '' ?>>Casa e Jardinagem</option>
													<option value='9' <?= (@$curso->assunto == 9) ? 'selected' : '' ?>>Culinária, Gastronomia, Receitas</option>
													<option value='10' <?= (@$curso->assunto == 10) ? 'selected' : '' ?>>Design e Templates PSD, PPT ou HTML</option>
													<option value='11' <?= (@$curso->assunto == 11) ? 'selected' : '' ?>>Edição de Áudio, Vídeo ou Imagens</option>
													<option value='12' <?= (@$curso->assunto == 12) ? 'selected' : '' ?>>Educacional, Cursos Técnicos e Profissionalizantes</option>
													<option value='13' <?= (@$curso->assunto == 13) ? 'selected' : '' ?>>Eletrodomésticos</option>
													<option value='46'>Eletrônicos</option>
													<option value='14' <?= (@$curso->assunto == 14) ? 'selected' : '' ?>>Eletroportáteis</option>
													<option value='15' <?= (@$curso->assunto == 15) ? 'selected' : '' ?>>Entretenimento, Lazer e Diversão</option>
													<option value='16' <?= (@$curso->assunto == 16) ? 'selected' : '' ?>>Esportes e Fitness</option>
													<option value='17' <?= (@$curso->assunto == 17) ? 'selected' : '' ?>>Filmes e Cinema</option>
													<option value='18' <?= (@$curso->assunto == 18) ? 'selected' : '' ?>>Geral</option>
													<option value='19' <?= (@$curso->assunto == 19) ? 'selected' : '' ?>>Histórias em Quadrinhos</option>
													<option value='20' <?= (@$curso->assunto == 20) ? 'selected' : '' ?>>Idiomas</option>
													<option value='21' <?= (@$curso->assunto == 21) ? 'selected' : '' ?>>Informática</option>
													<option value='22' <?= (@$curso->assunto == 22) ? 'selected' : '' ?>>Internet Marketing</option>
													<option value='23' <?= (@$curso->assunto == 23) ? 'selected' : '' ?>>Investimentos e Finanças</option>
													<option value='24' <?= (@$curso->assunto == 24) ? 'selected' : '' ?>>Jogos de Cartas, Poker, Loterias</option>
													<option value='25' <?= (@$curso->assunto == 25) ? 'selected' : '' ?>>Jogos de Computador, Jogos Online</option>
													<option value='26' <?= (@$curso->assunto == 26) ? 'selected' : '' ?>>Jurídico</option>
													<option value='27' <?= (@$curso->assunto == 27) ? 'selected' : '' ?>>Literatura e Poesia</option>
													<option value='28' <?= (@$curso->assunto == 28) ? 'selected' : '' ?>>Marketing de Rede</option>
													<option value='29' <?= (@$curso->assunto == 29) ? 'selected' : '' ?>>Marketing e Comunicação</option>
													<option value='30' <?= (@$curso->assunto == 30) ? 'selected' : '' ?>>Meio Ambiente</option>
													<option value='31' <?= (@$curso->assunto == 31) ? 'selected' : '' ?>>Moda e vestuário</option>
													<option value='32' <?= (@$curso->assunto == 32) ? 'selected' : '' ?>>Moveis e decoração</option>
													<option value='33' <?= (@$curso->assunto == 33) ? 'selected' : '' ?>>Música, Bandas e Shows</option>
													<option value='34' <?= (@$curso->assunto == 34) ? 'selected' : '' ?>>Paquera, Sedução e Relacionamentos</option>
													<option value='35' <?= (@$curso->assunto == 35) ? 'selected' : '' ?>>Perfumaria</option>
													<option value='36' <?= (@$curso->assunto == 36) ? 'selected' : '' ?>>Pessoas com deficiência</option>
													<option value='37' <?= (@$curso->assunto == 37) ? 'selected' : '' ?>>Plugins, Widgets e Extensões</option>
													<option value='38' <?= (@$curso->assunto == 38) ? 'selected' : '' ?>>Produtividade e Organização Pessoal</option>
													<option value='39' <?= (@$curso->assunto == 39) ? 'selected' : '' ?>>Produtos infantis</option>
													<option value='40' <?= (@$curso->assunto == 40) ? 'selected' : '' ?>>Relatórios, Artigos e Pesquisas</option>
													<option value='41' <?= (@$curso->assunto == 41) ? 'selected' : '' ?>>Religião e Crenças</option>
													<option value='42' <?= (@$curso->assunto == 42) ? 'selected' : '' ?>>Romances, Dramas, Estórias e Contos</option>
													<option value='43' <?= (@$curso->assunto == 43) ? 'selected' : '' ?>>RPG e Jogos de Mesa</option>
													<option value='44' <?= (@$curso->assunto == 44) ? 'selected' : '' ?>>Saúde, Bem-estar e Beleza</option>
													<option value='45' <?= (@$curso->assunto == 45) ? 'selected' : '' ?>>Scripts</option>
													<option value='46' <?= (@$curso->assunto == 46) ? 'selected' : '' ?>>Segurança do Trabalho</option>
													<option value='47' <?= (@$curso->assunto == 47) ? 'selected' : '' ?>>Sexologia e Sexualidade</option>
													<option value='48' <?= (@$curso->assunto == 48) ? 'selected' : '' ?>>Snippets (Trechos de Vídeo)</option>
													<option value='49' <?= (@$curso->assunto == 49) ? 'selected' : '' ?>>Trader</option>
													<option value='50' <?= (@$curso->assunto == 50) ? 'selected' : '' ?>>Turismo</option>
												</select>
											</div>
										</div>

										<div class="col-lg-6 form-group">
											<div class="inner-selct ">
												<label for="inputState">Formato*</label>
												<select id="inputState3" class="form-control" name="formato" required>
													<options>-- Selecione --</option>
														<option value='11' <?= (@$curso->formato == 11) ? 'selected' : '' ?>>Site Membros (Cursos, Video Aulas)/ Serviço de assinatura</option>
												</select>
											</div>
										</div>
									</div>

								</div>
								<!------left main col ends----->

								<div class="col-lg-4 col-md-4 col-12 right-area" id="right-col_form">
									<label for="exampleFormControlTextarea1">Imagem do curso</label>

									<div class="image-col">
										<div class="input-group mb-3">
											<div class="custom-file">
												<?php if (!empty($_GET['course_id'])) :  ?>
													<?php if ($curso->imagem != "") : ?>
														<input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
														<label for="imageInput" class="filelabel" style="display:none;"><i class="far fa-image"></i> Escolha a imagem</label>
														<img src="<?= url("uploads/cursos/" . $curso->imagem); ?>" class="image-preview" width="170px" height="170px">
														<span class="change-image">Escolha uma imagem diferente</span>
													<?php else : ?>
														<input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
														<label for="imageInput" class="filelabel"><i class="far fa-image"></i> Escolha a imagem</label>
														<img src="" class="image-preview">
														<span class="change-image">Escolha uma imagem diferente</span>
												<?php endif;
												endif; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!------form containe div ends--->

						<?php if (!empty($cursoss)) : ?>
							<input type="hidden" name="course_id" value="<?php echo $curso->id; ?>">
							<input type="hidden" name="course_img" value="<?php echo $curso->imagem; ?>">
						<?php endif; ?>

						<div class="form-container">
							<div class="row">
								<div class="col-md-4 form-group">
									<label for="exampleFormControlTextarea1">Liberação dos Módulos</label>
									<select name="liberacao" id="liberacao" class="form-control">
										<option value="">Selecionar...</option>
										<option value="1" <?= (@$curso->liberacao == 1) ? 'selected' : '' ?>>Liberar Imediatamente</option>
										<!-- <option value="2" <?= (@$curso->liberacao == 2) ? 'selected' : '' ?>>A cada conclusão de módulo</option> -->
										<option value="3" <?= (@$curso->liberacao == 3) ? 'selected' : '' ?>>Segundo módulo depois de 7 dias</option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-lg-12 text-areas">
									<label for="exampleFormControlTextarea1">Descrição do curso</label>
									<textarea class="form-control" id="exampleFormControlTextarea1" rows="8" name="descricao" required><?= $curso->descricao; ?></textarea>
								</div>
							</div>
						</div>

						<div class="form_next-prev px-3">
							<input type="submit" name="next-step" class="nxt_step" value="Salvar" />
						</div>
					</fieldset>

				</div>
			</form>
		</div>
	</section>

	<br>

</div>
<?php

if (!empty($_GET['course_id'])) {
?>
	<style>
		.progress-bar {
			width: 40% !important;
		}

		fieldset:nth-child(2) {
			display: block !important;
		}

		fieldset:nth-child(1) {
			display: none;
		}
	</style>

<?php

}
?>
<style>
	section.content {
		padding-top: 0px !important;
	}

	.content-header {
		padding-bottom: 0px;
	}
</style>
<?= $v->start("scripts") ?>
<script>
	$(document).ready(function() {


		$('#imageInput').on('change', function() {
			$input = $(this);
			if ($input.val().length > 0) {
				fileReader = new FileReader();
				fileReader.onload = function(data) {
					$('.image-preview').attr('src', data.target.result);
				}
				fileReader.readAsDataURL($input.prop('files')[0]);
				$('.filelabel').css('display', 'none');
				$('.image-preview').css('display', 'block');
				$('.change-image').css('display', 'block');
			}
		});

		$('.change-image').on('click', function() {
			$control = $(this);
			$('#imageInput').val('');
			$preview = $('.image-preview');
			$preview.attr('src', '');
			$preview.css('display', 'none');
			$control.css('display', 'none');
			$('.filelabel').css('display', 'block');
		});
	});
</script>
<?= $v->end() ?>
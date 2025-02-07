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
					<h1 style="font-family:inter"><?= $title ?></h1>
				</div>
			</div>
			<hr>
		</div><!-- /.container-fluid -->
	</section>

	<!-- Main content -->
	<section class="conten" style="padding:0 20px">
		<form action="<?= $router->route("appcursos.cadastro"); ?>" id="form" method="post" enctype="multipart/form-data" style="font-family:inter; text-transform:uppercase; font-size:11px">
			<ul id="progressbar">
				<li class="active" id="step1"><strong>Cadastro de curso</strong></li>
				<li id="step2"><strong>Cadastro do módulo</strong></li>
				<li id="step3"><strong>Cadastro de Aula</strong></li>
			</ul>

			<div class="form-container_box">

				<fieldset <?php if (!empty($_GET['course_id'])) { ?>style="display:block" ; <?php } ?>>
					<div class="form-container">
						<div class="row">
							<!-----left---->
							<div class="col-lg-8 col-md-8 col-12 left-area">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-12 form-group">
										<div class="name-grp">
											<label for="formGroupExampleInput">Nome do curso*</label>
											<input type="text" class="form-control" id="formGroupExampleInput" name="nome" placeholder="nome" required>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12 col-md-12 col-12 form-group">
										<div class="inner-selct ">
											<label for="inputState">Produtos *</label>
											<select id="inputState" class="form-control" name="produto" required>
												<?php if ($produtos) : ?>
													<?php foreach ($produtos as $produto) : ?>
														<option value="<?php echo $produto->id; ?>" <?php if (!empty($cursoss[0]->produto)) {
																										if ($cursoss[0]->produto == $produto->id) {
																											echo "selected";
																										}
																									} ?>><?php echo $produto->nome; ?></option>
													<?php endforeach;  ?>
												<?php endif;  ?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-6  form-group">
										<div class="inner-selct ">
											<label for="inputState">Assunto*</label>
											<select id="inputState" class="form-control" name="assunto" required>
												<!-- <option <?php if (!empty($cursoss[0]->assunto)) {
																	if ($cursoss[0]->assunto == 2) {
																		echo "selected";
																	}
																} ?>>2</option>
												<option <?php if (!empty($cursoss[0]->assunto)) {
															if ($cursoss[0]->assunto == 3) {
																echo "selected";
															}
														} ?>>3</option>
												<option <?php if (!empty($cursoss[0]->assunto)) {
															if ($cursoss[0]->assunto == 4) {
																echo "selected";
															}
														} ?>>4</option>
												<option <?php if (!empty($cursoss[0]->assunto)) {
															if ($cursoss[0]->assunto == 5) {
																echo "selected";
															}
														} ?>>5</option> -->

												<option disabled selected>-- Selecione --</option>
												<option value='1' <?= (@$cursoss[0]->assunto == 1) ? 'selected' : '' ?>>Administração e Negócios</option>
												<option value='2' <?= (@$cursoss[0]->assunto == 2) ? 'selected' : '' ?>>Animais de Estimação</option>
												<option value='3' <?= (@$cursoss[0]->assunto == 3) ? 'selected' : '' ?>>Arquitetura e Engenharia</option>
												<option value='4' <?= (@$cursoss[0]->assunto == 4) ? 'selected' : '' ?>>Artes e Música</option>
												<option value='5' <?= (@$cursoss[0]->assunto == 5) ? 'selected' : '' ?>>Auto-ajuda e Desenvolvimento Pessoal</option>
												<option value='6' <?= (@$cursoss[0]->assunto == 6) ? 'selected' : '' ?>>Automóveis</option>
												<option value='7' <?= (@$cursoss[0]->assunto == 7) ? 'selected' : '' ?>>Blogs e Redes Sociais</option>
												<option value='8' <?= (@$cursoss[0]->assunto == 8) ? 'selected' : '' ?>>Casa e Jardinagem</option>
												<option value='9' <?= (@$cursoss[0]->assunto == 9) ? 'selected' : '' ?>>Culinária, Gastronomia, Receitas</option>
												<option value='10' <?= (@$cursoss[0]->assunto == 10) ? 'selected' : '' ?>>Design e Templates PSD, PPT ou HTML</option>
												<option value='11' <?= (@$cursoss[0]->assunto == 11) ? 'selected' : '' ?>>Edição de Áudio, Vídeo ou Imagens</option>
												<option value='12' <?= (@$cursoss[0]->assunto == 12) ? 'selected' : '' ?>>Educacional, Cursos Técnicos e Profissionalizantes</option>
												<option value='13' <?= (@$cursoss[0]->assunto == 13) ? 'selected' : '' ?>>Eletrodomésticos</option>
												<option value='46'>Eletrônicos</option>
												<option value='14' <?= (@$cursoss[0]->assunto == 14) ? 'selected' : '' ?>>Eletroportáteis</option>
												<option value='15' <?= (@$cursoss[0]->assunto == 15) ? 'selected' : '' ?>>Entretenimento, Lazer e Diversão</option>
												<option value='16' <?= (@$cursoss[0]->assunto == 16) ? 'selected' : '' ?>>Esportes e Fitness</option>
												<option value='17' <?= (@$cursoss[0]->assunto == 17) ? 'selected' : '' ?>>Filmes e Cinema</option>
												<option value='18' <?= (@$cursoss[0]->assunto == 18) ? 'selected' : '' ?>>Geral</option>
												<option value='19' <?= (@$cursoss[0]->assunto == 19) ? 'selected' : '' ?>>Histórias em Quadrinhos</option>
												<option value='20' <?= (@$cursoss[0]->assunto == 20) ? 'selected' : '' ?>>Idiomas</option>
												<option value='21' <?= (@$cursoss[0]->assunto == 21) ? 'selected' : '' ?>>Informática</option>
												<option value='22' <?= (@$cursoss[0]->assunto == 22) ? 'selected' : '' ?>>Internet Marketing</option>
												<option value='23' <?= (@$cursoss[0]->assunto == 23) ? 'selected' : '' ?>>Investimentos e Finanças</option>
												<option value='24' <?= (@$cursoss[0]->assunto == 24) ? 'selected' : '' ?>>Jogos de Cartas, Poker, Loterias</option>
												<option value='25' <?= (@$cursoss[0]->assunto == 25) ? 'selected' : '' ?>>Jogos de Computador, Jogos Online</option>
												<option value='26' <?= (@$cursoss[0]->assunto == 26) ? 'selected' : '' ?>>Jurídico</option>
												<option value='27' <?= (@$cursoss[0]->assunto == 27) ? 'selected' : '' ?>>Literatura e Poesia</option>
												<option value='28' <?= (@$cursoss[0]->assunto == 28) ? 'selected' : '' ?>>Marketing de Rede</option>
												<option value='29' <?= (@$cursoss[0]->assunto == 29) ? 'selected' : '' ?>>Marketing e Comunicação</option>
												<option value='30' <?= (@$cursoss[0]->assunto == 30) ? 'selected' : '' ?>>Meio Ambiente</option>
												<option value='31' <?= (@$cursoss[0]->assunto == 31) ? 'selected' : '' ?>>Moda e vestuário</option>
												<option value='32' <?= (@$cursoss[0]->assunto == 32) ? 'selected' : '' ?>>Moveis e decoração</option>
												<option value='33' <?= (@$cursoss[0]->assunto == 33) ? 'selected' : '' ?>>Música, Bandas e Shows</option>
												<option value='34' <?= (@$cursoss[0]->assunto == 34) ? 'selected' : '' ?>>Paquera, Sedução e Relacionamentos</option>
												<option value='35' <?= (@$cursoss[0]->assunto == 35) ? 'selected' : '' ?>>Perfumaria</option>
												<option value='36' <?= (@$cursoss[0]->assunto == 36) ? 'selected' : '' ?>>Pessoas com deficiência</option>
												<option value='37' <?= (@$cursoss[0]->assunto == 37) ? 'selected' : '' ?>>Plugins, Widgets e Extensões</option>
												<option value='38' <?= (@$cursoss[0]->assunto == 38) ? 'selected' : '' ?>>Produtividade e Organização Pessoal</option>
												<option value='39' <?= (@$cursoss[0]->assunto == 39) ? 'selected' : '' ?>>Produtos infantis</option>
												<option value='40' <?= (@$cursoss[0]->assunto == 40) ? 'selected' : '' ?>>Relatórios, Artigos e Pesquisas</option>
												<option value='41' <?= (@$cursoss[0]->assunto == 41) ? 'selected' : '' ?>>Religião e Crenças</option>
												<option value='42' <?= (@$cursoss[0]->assunto == 42) ? 'selected' : '' ?>>Romances, Dramas, Estórias e Contos</option>
												<option value='43' <?= (@$cursoss[0]->assunto == 43) ? 'selected' : '' ?>>RPG e Jogos de Mesa</option>
												<option value='44' <?= (@$cursoss[0]->assunto == 44) ? 'selected' : '' ?>>Saúde, Bem-estar e Beleza</option>
												<option value='45' <?= (@$cursoss[0]->assunto == 45) ? 'selected' : '' ?>>Scripts</option>
												<option value='46' <?= (@$cursoss[0]->assunto == 46) ? 'selected' : '' ?>>Segurança do Trabalho</option>
												<option value='47' <?= (@$cursoss[0]->assunto == 47) ? 'selected' : '' ?>>Sexologia e Sexualidade</option>
												<option value='48' <?= (@$cursoss[0]->assunto == 48) ? 'selected' : '' ?>>Snippets (Trechos de Vídeo)</option>
												<option value='49' <?= (@$cursoss[0]->assunto == 49) ? 'selected' : '' ?>>Trader</option>
												<option value='50' <?= (@$cursoss[0]->assunto == 50) ? 'selected' : '' ?>>Turismo</option>
											</select>
										</div>
									</div>

									<div class="col-lg-6 form-group">
										<div class="inner-selct ">
											<label for="inputState">Formato*</label>
											<select id="inputState3" class="form-control" name="formato" required>
												<!-- <option <?php if (!empty($cursoss[0]->formato)) {
																	if ($cursoss[0]->formato == 2) {
																		echo "selected";
																	}
																} ?>>2</option>
												<option <?php if (!empty($cursoss[0]->formato)) {
															if ($cursoss[0]->formato == 3) {
																echo "selected";
															}
														} ?>>3</option>
												<option <?php if (!empty($cursoss[0]->formato)) {
															if ($cursoss[0]->formato == 4) {
																echo "selected";
															}
														} ?>>4</option>
												<option <?php if (!empty($cursoss[0]->formato)) {
															if ($cursoss[0]->formato == 5) {
																echo "selected";
															}
														} ?>>5</option> -->

												<option disabled>-- Selecione --</option>
												<option value='11' <?= (@$cursoss[0]->formato == 11) ? 'selected' : '' ?>>Site Membros (Cursos, Video Aulas)/ Serviço de assinatura</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6 form-group">
										<label for="exampleFormControlTextarea1">Liberação dos Módulos</label>
										<select name="liberacao" id="liberacao" class="form-control">
											<option value="">Selecionar...</option>
											<option value="1">Liberar Imediatamente</option>
											<!-- <option value="2">A cada conclusão de módulo</option> -->
											<option value="3">Segundo módulo depois de 7 dias</option>
										</select>
									</div>
								</div>

								<div class="row">
									<div class="form-group col-lg-12 col-sm-12 col-12 col-md-12 text-areas">
										<label for="exampleFormControlTextarea1">Descrição do curso</label>
										<textarea class="form-control" id="exampleFormControlTextarea1" rows="8" name="descricao" required><?php if (!empty($cursoss[0]->descricao)) {
																																				echo $cursoss[0]->descricao;
																																			} ?></textarea>
									</div>
									<!--<span class="text-right text-format" id="textarea_feedback">0/6000</span>-->
								</div>
							</div>
							<!------left main col ends----->

							<div class="col-lg-4 col-md-4 col-12 right-area" id="right-col_form">
								<label for="exampleFormControlTextarea1">Imagem do curso</label>
								<div class="image-col">
									<div class="input-group mb-3">
										<div class="custom-file">
											<?php if (!empty($_GET['course_id'])) {  ?>
												<?php if ($cursoss[0]->imagem != "") { ?>
													<input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
													<label for="imageInput" class="filelabel" style="display:none;"><i class="far fa-image"></i> Choose image</label>
													<img src="<?= url("uploads/cursos/" . $cursoss[0]->imagem); ?>" class="image-preview">
													<span class="change-image">Escolha uma imagem Diferente</span>
												<?php } else {

												?>
													<input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
													<label for="imageInput" class="filelabel"><i class="far fa-image"></i> Escolha uma imagem</label>
													<img src="" class="image-preview">
													<span class="change-image">Escolha uma imagem Diferente</span>

												<?php }
											} else { ?>

												<input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
												<label for="imageInput" class="filelabel"><i class="far fa-image"></i> Escolha uma imagem</label>
												<img src="" class="image-preview">
												<span class="change-image">Escolha uma imagem Diferente</span>

											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!------form containe div ends--->
					<?php
					if (!empty($cursoss)) {
					?>
						<input type="hidden" name="course_id" value="<?php echo $cursoss[0]->id; ?>">
						<input type="hidden" name="course_img" value="<?php echo $cursoss[0]->imagem; ?>">

					<?php
					}
					?>

					<div class="form_next-prev px-3">
						<input type="submit" name="next-step" class="nxt_step" value="Continuar" />
						<!-- <input type="button" name="previous-step" class="previous-step" value="Back" /> -->
					</div>
				</fieldset>
			</div>
		</form>




	</section>

</div>
<?php if (!empty($_GET['course_id'])) : ?>
	<style>
		.progress-bar {
			width: 40%;
		}

		.progress {
			height: 3px !important;
		}

		.progress-bar {
			background-color: #1a2d71 !important;
		}

		fieldset:nth-child(1) {
			display: none;
		}
	</style>

<?php endif; ?>

<?= $v->start("scripts") ?>
<script>
	$(document).ready(function() {
		var currentGfgStep, nextGfgStep, previousGfgStep;
		var opacity;
		var current = 1;
		var steps = 4;

		setProgressBar(current);

		$(".next-step").click(function() {
			$('#step2').addClass("active");
			$('fieldset:nth-child(2)').css("display", "block");
			$('fieldset:nth-child(1)').css("display", "none");
			setProgressBar(++current);
		});

		$(".previous-step").click(function() {
			$('#step2').removeClass("active");
			$('fieldset:nth-child(1)').css("display", "block");
			$('fieldset:nth-child(2)').css("display", "none");
			setProgressBar(--current);
		});

		function setProgressBar(currentStep) {

			var percent = parseFloat(100 / steps) * current;

			percent = percent.toFixed();


			$(".progress-bar")
				.css("width", percent + "%")
		}

		$(".submit").click(function() {
			return false;
		})


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
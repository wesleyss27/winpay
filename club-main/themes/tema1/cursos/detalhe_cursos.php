<?php
$v->layout("_theme");

$liberacao = array(
	1 => "imediatamente",
	2 => "a cada conclusão de módulo",
	3 => "segundo módulo depois de 7 dias"
);
?>

<!-- Content Wrapper. Contains page content -->

<style>
	.input-nome {
		border-bottom: 2px dashed #ddd;
		border-top: none;
		border-left: none;
		border-right: none;
		width: 90%;
	}
</style>

<div class="content-wrapper">

	<!-- Content Header (Page header) -->
	<section class="content" id="menu-content">
		<br>
		<!-- .main-outer -->
		<div class="row">
			<!-- <div class="col-md-1">
				<?php
				if (!empty($cursos->imagem)) {
					$img = url("uploads/cursos/" . $cursos->imagem);
				} else {
					$img = url("uploads/cursos/default.png");
				}
				?>

				<img src="<?= $img; ?>" alt="" style="border-radius: 10px;">
			</div> -->

			<div class="col-md-8">
				<h1><?= $title ?></h1>
			</div>

			<div class="top-head-right col-md-4" style="text-align: right">
				<a class="button-left" href="<?= url("minhas-compras/viewcursos/" . $cursos->id) ?>" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i>VISUALIZAR COMO ALUNO</a>
				<!-- <div class="edit_action">
					<a href="javascript:void();" class="course_action"><i class="fas fa-ellipsis-v"></i></a>
					<div class="action" style="display:none;">
						<span><a href="../editarcursos?course_id=<?php echo $cursos->id; ?>">Editar</a></span>
						<span><a href="#" onclick="excluir_cursos(<?= $cursos->id; ?>)">Excluir</a></span>
					</div>
				</div> -->
			</div>
		</div>
		<!-- fim .main-outer -->

		<!-- <br>
		<div class="row">
			<div class="col-md-6">
				<a class="button-left" href="<?= url("minhas-compras/viewcursos/" . $cursos->id) ?>" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i>VISUALIZAR COMO ALUNO</a>
			</div>
			<div class="col-md-6" style="text-align: right;">
				<a class="button-right" href="../editarmodulo/<?= $cursos->id; ?>"><i class="fa fa-plus" aria-hidden="true"></i>NOVO MODULO</a>
			</div>
		</div>
		<br> -->

		<!-- <div class="alert bg-primary">
			<p><?= $cursos->descricao; ?></p>
		</div> -->
		<!-- <p>Liberação <?= $liberacao[$cursos->liberacao]; ?></p> -->

		<?php $v->insert("cursos/menu", ['curso' => $cursos]); ?>

		<?php
		if ($modulos) :
			$i = 1;
			foreach ($modulos as $modulos) :
		?>
				<div class="card">
					<div class="card-header">
						<a href="../editarmodulo/<?= $cursos->id; ?>?module_id=<?= $modulos->id; ?>"><?= $modulos->nome; ?></a>

						<div class="card-tools text-right">
							<div class="acao_editar">
								<a href="javascript:void();" class="module_action"><i class="fas fa-ellipsis-v"></i></a>
								<div class="acao" style="display:none;">
									<span><a href="../editarmodulo/<?php echo $cursos->id; ?>?module_id=<?= $modulos->id; ?>">Editar</a></span><br>
									<span><a href="#" onclick="excluir_module(<?= $modulos->id; ?>)">Excluir</a></span>
								</div>
							</div>

							<input type="hidden" id="valor<?= $modulos->id ?>" value="0">
							<button type="button" id="btn<?= $modulos->id ?>" onclick="mostrarAulas(<?= $modulos->id ?>)" class="btn btn-tool">
								<i class="fas fa-plus"></i>
							</button>
						</div>
					</div>

					<div class="card-body" id="aulas<?= $modulos->id ?>" style="display: none;">
						<?php
						$count = [];
						if ($aulas) :
							$o = 1;
							foreach ($aulas as $aula) :
								// $aula->ordem = $o;
								// $aula->save();
								// $o++;
								if ($aula->modulo == $modulos->id) :
									$count[] = $modulos->id;
						?>
									<div class="sortable">
										<div class="row alternadas" data-id="<?= $aula->id; ?>">
											<div class="col-md-10">
												<i class="fas fa-arrows-alt"></i>
												<!-- <span class="play_icon mx-2"> <i class="far fa-play-circle icon_color"></i></span> -->
												<input type="text" name="aula_nome" id="aula_nome<?= $aula->id; ?>" onblur="atualizaNome(<?= $aula->id; ?>)" class="input-nome" value="<?= $aula->nome; ?>">
												<!-- <a href="../editaraulas/<?= $cursos->id; ?>?module_id=<?= $modulos->id; ?>&aulas_id=<?= $aula->id; ?>"><?= $aula->nome; ?></a> -->
											</div>

											<div class="col-md-2 text-right">
												<a href="<?= url("minhas-compras/viewaulas/$aula->id"); ?>&aulas_id=<?= $aula->id; ?>" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-external-link-alt"></i></a>
												<a href="../editaraulas/<?= $cursos->id; ?>?module_id=<?= $modulos->id; ?>&aulas_id=<?= $aula->id; ?>" class="btn btn-info btn-sm"><i class="far fa-edit"></i></a>
												<a href="#" onclick="excluir_aula(<?= $aula->id; ?>)" class="btn btn-info btn-sm"><i class="far fa-trash-alt"></i></a>
											</div>
										</div>
									</div>
						<?php
								endif;
							endforeach;
						endif;
						?>
					</div>

					<div class="card-footer justify-content-between">
						<div class="row">
							<div class="col-md-6">
								<p>
									<?php
									$c1 = count($count);
									echo $c1 > 1 ? "$c1 Aulas" : "$c1 Aula";
									?>
								</p>
							</div>

							<div class="col-md-6 text-right">
								<a class="btn btn-success" href="../editaraulas/<?php echo $cursos->id; ?>?module_id=<?= $modulos->id; ?>"><i class="fa fa-plus" aria-hidden="true"></i>ADICIONAR AULA</a>
							</div>
						</div>
					</div>
				</div>
		<?php
			endforeach;
		endif;
		?>
	</section>
	<!-- END SECTION -->

</div>
<?= $v->start("scripts") ?>
<script>
	$(".sortable").sortable({
		revert: true,
		update: function(event, ui) {
			// Função executada após a ordenação dos itens
			var order = [];
			$(".sortable .alternadas").each(function() {
				order.push($(this).data("id"));
			});

			// Enviar a ordem para o servidor via AJAX
			$.ajax({
				type: "POST",
				url: "<?= $router->route("cursos.ordemAulas") ?>", // Substitua pelo URL do seu script PHP
				data: {
					order: order
				},
				success: function(response) {
					// Lidar com a resposta do servidor, se necessário
					console.log(response);
				},
				error: function(xhr, status, error) {
					// Lidar com erros, se ocorrerem
				}
			});
		}
	})
	$(".alternadas").draggable({
		handle: "i",
		connectToSortable: ".sortable",
		revert: "invalid",
		axis: "y"
	});

	function atualizaNome(id) {
		var nome = $("#aula_nome" + id).val();

		$.ajax({
			type: "POST",
			url: "<?= $router->route("aulas.atualizaNome") ?>", // Substitua pelo URL do seu script PHP
			data: {
				id: id,
				nome: nome
			},
			success: function(response) {
				// Lidar com a resposta do servidor, se necessário
				console.log(response);
			},
			error: function(xhr, status, error) {
				// Lidar com erros, se ocorrerem
			}
		});
	}

	function mostrarAulas(id) {
		$("#aulas" + id).toggle();

		var valor = $("#valor" + id).val();

		if (valor == 0) {
			$("#btn" + id).html("<i class='fas fa-minus'></i>");
			$("#valor" + id).attr("value", 1);
		} else {
			$("#btn" + id).html("<i class='fas fa-plus'></i>");
			$("#valor" + id).attr("value", 0);
		}
	}

	var acc = document.getElementsByClassName("accordion");
	var i;

	for (i = 0; i < acc.length; i++) {
		acc[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var panel = this.nextElementSibling;
			if (panel.style.display === "block") {
				panel.style.display = "none";
			} else {
				panel.style.display = "block";
			}
		});
	}
	$(document).ready(function() {
		$(".course_action").click(function() {
			jQuery(this).next('.action').toggle();

		});

		$(".module_action").click(function() {
			jQuery(this).next('.acao').toggle();

		});

		$(".aulas_action").click(function() {
			jQuery(this).next('.acao').toggle();

		});
	});

	function excluir_aula(id) {

		//console.log(id);
		if (confirm("Você realmente deseja excluir esta aulas?")) {

			$.ajax({

				url: "<?= $router->route("appcursos.urlExcluirAula") ?>",

				data: {
					id: id
				},



				type: "POST",

				beforeSend: function() {

					console.log("atualizando");

				},

				error: function() {

					console.log("O servidor não conseguiu processar o pedido");

				},

				success: function(callback) {

					console.log(callback);

					if (callback == "1") {

						location.reload();

					} else {

						console.log("fracasso");

					}

				}

			});

		}

	}


	function excluir_module(id) {

		//console.log(id);
		if (confirm("A exclusão deste módulo também pode afetar outras aulas")) {

			$.ajax({

				url: "<?= $router->route("appcursos.urlExcluirModule") ?>",

				data: {
					id: id
				},



				type: "POST",

				beforeSend: function() {

					console.log("atualizando");

				},

				error: function() {

					console.log("O servidor não conseguiu processar o pedido");

				},

				success: function(callback) {

					console.log(callback);

					if (callback == "1") {

						location.reload();

					} else {

						console.log("fracasso");

					}

				}

			});

		}

	}


	function excluir_cursos(id) {

		//console.log(id);
		if (confirm("Tem certeza de que deseja excluir este curso? Pode afetar módulos e aulas")) {

			$.ajax({

				url: "<?= $router->route("appcursos.urlExcluirCursos") ?>",

				data: {
					id: id
				},



				type: "POST",

				beforeSend: function() {

					console.log("atualizando");

				},

				error: function() {

					console.log("O servidor não conseguiu processar o pedido");

				},

				success: function(callback) {

					console.log(callback);

					if (callback == "1") {

						window.location.href = "<?= $router->route("appcursos.home") ?>";

					} else {

						console.log("fracasso");

					}

				}

			});

		}

	}
</script>

<?= $v->end() ?>
<?php
$v->layout("_theme");

$liberacao = [
	1 => "Imediatamente",
	2 => "A cada conclusão de módulo",
	3 => "Segundo módulo depois de 7 dias"
];
?>

<div class="content-wrapper win-page">
	<section class="content">
		<div class="container-fluid">
			<header class="win-page-header">
				<div class="win-page-title">
					<small>Gestão de conteúdo</small>
					<h1><?= $title ?></h1>
				</div>
				<div class="win-page-actions">
					<a class="btn-win btn-win-ghost" href="<?= url("minhas-compras/viewcursos/" . $cursos->id) ?>" target="_blank">
						<i class="fa fa-eye"></i>
						Ver como aluno
					</a>
				</div>
			</header>

			<?php $v->insert("cursos/menu", ['curso' => $cursos]); ?>

			<section class="win-section">
				<div class="win-stats-grid">
					<div class="win-stat-card">
						<span>Módulos</span>
						<strong><?= is_countable($modulos) ? count($modulos) : 0; ?></strong>
					</div>
					<div class="win-stat-card">
						<span>Aulas</span>
						<strong><?= is_countable($aulas) ? count($aulas) : 0; ?></strong>
					</div>
					<div class="win-stat-card">
						<span>Liberação</span>
						<strong><?= $liberacao[$cursos->liberacao] ?? "Configuração padrão"; ?></strong>
					</div>
				</div>
			</section>

			<section class="win-section win-scroll-shadow">
				<?php if ($modulos) : ?>
					<div class="win-module-list">
						<?php foreach ($modulos as $modulo) : ?>
							<?php
							$moduleId = $modulo->id;
							$moduleAulas = [];
							if ($aulas) {
								foreach ($aulas as $aulaItem) {
									if ($aulaItem->modulo == $moduleId) {
										$moduleAulas[] = $aulaItem;
									}
								}
							}
							$totalAulasModulo = count($moduleAulas);
							?>
							<article class="win-card win-module-card">
								<div class="d-flex justify-content-between align-items-center win-flex-gap-sm">
									<a href="../editarmodulo/<?= $cursos->id; ?>?module_id=<?= $moduleId; ?>" class="win-module-title">
										<?= $modulo->nome; ?>
									</a>
									<div class="d-flex align-items-center win-flex-gap-sm">
										<div class="acao_editar">
											<a href="javascript:void(0);" class="module_action"><i class="fas fa-ellipsis-v"></i></a>
											<div class="acao" style="display:none;">
												<span><a href="../editarmodulo/<?= $cursos->id; ?>?module_id=<?= $moduleId; ?>">Editar</a></span><br>
												<span><a href="#" onclick="excluir_module(<?= $moduleId; ?>)">Excluir</a></span>
											</div>
										</div>
										<input type="hidden" id="valor<?= $moduleId; ?>" value="0">
										<button type="button" id="btn<?= $moduleId; ?>" onclick="mostrarAulas(<?= $moduleId; ?>)" class="btn btn-sm btn-outline-light">
											<i class="fas fa-plus"></i>
										</button>
									</div>
								</div>

								<div class="win-divider"></div>

								<div class="sortable win-module-lessons" id="aulas<?= $moduleId; ?>" style="display: none;">
									<?php if ($moduleAulas) : ?>
										<?php foreach ($moduleAulas as $aulaItem) : ?>
											<div class="alternadas" data-id="<?= $aulaItem->id; ?>">
												<div class="d-flex align-items-center win-flex-gap-sm flex-grow-1">
													<i class="fas fa-grip-vertical text-muted"></i>
													<input type="text" name="aula_nome" id="aula_nome<?= $aulaItem->id; ?>" onblur="atualizaNome(<?= $aulaItem->id; ?>)" class="win-inline-input" value="<?= $aulaItem->nome; ?>">
												</div>
												<div class="win-list-actions">
													<a href="<?= url("minhas-compras/viewaulas/$aulaItem->id"); ?>&aulas_id=<?= $aulaItem->id; ?>" class="btn btn-sm btn-outline-light" target="_blank"><i class="fas fa-external-link-alt"></i></a>
													<a href="../editaraulas/<?= $cursos->id; ?>?module_id=<?= $moduleId; ?>&aulas_id=<?= $aulaItem->id; ?>" class="btn btn-sm btn-outline-light"><i class="far fa-edit"></i></a>
													<a href="#" onclick="excluir_aula(<?= $aulaItem->id; ?>)" class="btn btn-sm btn-outline-light"><i class="far fa-trash-alt"></i></a>
												</div>
											</div>
										<?php endforeach; ?>
									<?php else : ?>
										<div class="win-empty-state">
											Este módulo ainda não possui aulas cadastradas.
										</div>
									<?php endif; ?>
								</div>

								<div class="win-card-footer">
									<span class="win-pill"><?= $totalAulasModulo === 1 ? "1 aula" : $totalAulasModulo . " aulas"; ?></span>
									<div class="win-card-actions">
										<a class="btn-win" href="../editaraulas/<?= $cursos->id; ?>?module_id=<?= $moduleId; ?>">
											<i class="fa fa-plus" aria-hidden="true"></i>
											Adicionar aula
										</a>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<div class="win-empty-state">
						Nenhum módulo foi criado ainda. Comece adicionando um novo módulo para organizar o conteúdo do curso.
					</div>
				<?php endif; ?>
			</section>
		</div>
	</section>
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

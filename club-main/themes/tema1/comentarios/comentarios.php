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

		<div class="card">
			<div class="card-header">
				<div class="row">
					Total de comentários: <?= @$count ?>
				</div>
			</div>

			<?php if ($comentarios) : ?>
				<div class="card-body p-0">
					<table class="table table-striped">
						<thead>
							<tr>
								<th scope="col">Aluno</th>
								<th scope="col">Comentário</th>
								<th scope="col">Aula</th>
								<th scope="col">Status</th>
								<th scope="col">Data</th>
								<th scope="col"></th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($comentarios as $comentario) : ?>
								<tr>
									<td><?= $comentario->dataAluno()->nome ?></td>
									<td><?= $comentario->comentario ?></td>
									<td>
										<?= $comentario->dataModulo()->nome ?> 
										<small><i class="fas fa-arrow-right"></i></small> 
										<?= $comentario->dataAula()->nome ?></td>
									<td><?= $comentario->status ?></td>
									<td><?= formataDataHora($comentario->created_at) ?></td>
									<td>
										<div class="acao_editar">
											<a href="javascript:void();" class="course_action"><i class="fas fa-ellipsis-v"></i></a>
											<div class="acao" style="display:none;">
												<?php if ($comentario->status == 1) : ?>
													<span><a href="#" onclick="bloquear_aluno(<?= @$comentario->id; ?>)">Bloquear acesso</a></span><br>
												<?php else : ?>
													<span><a href="#" onclick="desbloquear_aluno(<?= @$comentario->id; ?>)">Desbloquear acesso</a></span><br>
												<?php endif ?>
												<span><a href="#" onclick="excluir_aluno(<?= @$comentario->id; ?>)">Excluir aluno da turma</a></span>
											</div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<?php
				//EXIBE A NAVEGAÇÃO DO PAGINADOR
				$uri = explode("?", $_SERVER["REQUEST_URI"], 2);
				$link = URL_BASE . "/cursos/comentarios/$curso->id/";

				//VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
				$paginador->paginas($total, $pagina, $link, @$uri[1]);

				?>
			<?php else : ?>
				<div class="card-body">
					<h4>Nenhum aluno encontrado!</h4>
				</div>

			<?php endif; ?>
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
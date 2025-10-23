<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
	header("Location: " . url("vendas"));
}

$fontesDisponiveis = [
	"Anton" => "Anton",
	"Arial" => "Arial",
	"Great Vibes" => "Great Vibes",
	"Poppins" => "Poppins",
	"REM" => "REM",
	"Times New Roman" => "Times New Roman"
];

$fundo = @$certificado->fundo ? url("uploads/cursos/$certificado->fundo") : url("themes/imgs/background-certificado.jpg");
$fonte_titulo = @$certificado->fonte_titulo ?: 'REM';
$cor_titulo = @$certificado->cor_titulo ?: '#1D465F';
$fonte_subtitulo = @$certificado->fonte_subtitulo ?: 'Great Vibes';
$cor_subtitulo = @$certificado->cor_subtitulo ?: '#000000';
$fonte_destaque = @$certificado->fonte_destaque ?: 'Poppins';
$cor_destaque = @$certificado->cor_destaque ?: '#000000';
$fonte_descricao = @$certificado->fonte_descricao ?: 'Poppins';
$cor_descricao = @$certificado->cor_descricao ?: '#000000';
?>

<div class="content-wrapper win-page">
	<section class="content">
		<div class="container-fluid">
			<header class="win-page-header">
				<div class="win-page-title">
					<small>Certificados · <?= $curso->nome; ?></small>
					<h1><?= $title ?></h1>
				</div>
				<div class="win-page-actions">
					<a href="<?= url("certificados/ver/$curso->id") ?>" target="_blank" class="btn-win btn-win-ghost">
						<i class="fas fa-external-link-alt"></i>
						Visualizar certificado
					</a>
				</div>
			</header>

			<?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

			<?php if (@$_GET["msg"]) : ?>
				<section class="win-section">
					<div class="win-badge">
						<i class="fas fa-info-circle"></i>
						<?= $_GET['msg']; ?>
					</div>
				</section>
			<?php endif; ?>

			<section class="win-section">
				<div class="win-section-header">
					<h2>Configuração visual</h2>
					<span class="win-section-subtitle">Defina fontes, cores e o fundo que serão utilizados no certificado de conclusão.</span>
				</div>

				<form action="<?= $router->route("certificados.salvar"); ?>" id="form" method="post" enctype="multipart/form-data" class="win-form">
					<input type="hidden" name="curso" value="<?= $curso->id; ?>">
					<input type="hidden" name="id" value="<?= @$certificado->id; ?>">

					<div class="win-form-grid win-form-grid-2">
						<div class="form-group">
							<label for="fonte_titulo">Fonte do título</label>
							<select name="fonte_titulo" id="fonte_titulo" class="form-control">
								<?php foreach ($fontesDisponiveis as $fonteValor => $fonteLabel) : ?>
									<option value="<?= $fonteValor; ?>" <?= $fonte_titulo === $fonteValor ? 'selected' : ''; ?>><?= $fonteLabel; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="cor_titulo">Cor do título</label><br>
							<input type="color" name="cor_titulo" id="cor_titulo" value="<?= $cor_titulo; ?>">
						</div>

						<div class="form-group">
							<label for="fonte_subtitulo">Fonte do subtítulo</label>
							<select name="fonte_subtitulo" id="fonte_subtitulo" class="form-control">
								<?php foreach ($fontesDisponiveis as $fonteValor => $fonteLabel) : ?>
									<option value="<?= $fonteValor; ?>" <?= $fonte_subtitulo === $fonteValor ? 'selected' : ''; ?>><?= $fonteLabel; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="cor_subtitulo">Cor do subtítulo</label><br>
							<input type="color" name="cor_subtitulo" id="cor_subtitulo" value="<?= $cor_subtitulo; ?>">
						</div>

						<div class="form-group">
							<label for="fonte_destaque">Fonte do destaque</label>
							<select name="fonte_destaque" id="fonte_destaque" class="form-control">
								<?php foreach ($fontesDisponiveis as $fonteValor => $fonteLabel) : ?>
									<option value="<?= $fonteValor; ?>" <?= $fonte_destaque === $fonteValor ? 'selected' : ''; ?>><?= $fonteLabel; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="cor_destaque">Cor do destaque</label><br>
							<input type="color" name="cor_destaque" id="cor_destaque" value="<?= $cor_destaque; ?>">
						</div>

						<div class="form-group">
							<label for="fonte_descricao">Fonte da descrição</label>
							<select name="fonte_descricao" id="fonte_descricao" class="form-control">
								<?php foreach ($fontesDisponiveis as $fonteValor => $fonteLabel) : ?>
									<option value="<?= $fonteValor; ?>" <?= $fonte_descricao === $fonteValor ? 'selected' : ''; ?>><?= $fonteLabel; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="cor_descricao">Cor da descrição</label><br>
							<input type="color" name="cor_descricao" id="cor_descricao" value="<?= $cor_descricao; ?>">
						</div>
					</div>

					<div class="win-divider"></div>

					<div class="win-form-grid">
						<div class="form-group">
							<label for="fundo">Imagem de fundo</label>
							<div class="attachment">
								<label class="filelabel" for="fundo">
									<i class="fa fa-image"></i>
									<span class="title">Carregar nova imagem</span>
								</label>
								<input type="file" name="fundo" id="fundo" class="form-control">
							</div>
							<div class="form-check mt-2">
								<input class="form-check-input" type="checkbox" name="fundo_padrao" id="fundo_padrao" value="1">
								<label class="form-check-label" for="fundo_padrao">
									Usar fundo padrão da plataforma
								</label>
							</div>
						</div>
					</div>

					<div class="win-form-actions">
						<button type="submit" class="btn-win">
							<i class="fas fa-save"></i>
							Salvar configurações
						</button>
					</div>
				</form>
			</section>

			<section class="win-section">
				<div class="win-section-header">
					<h2>Pré-visualização</h2>
					<span class="win-section-subtitle">Veja como o certificado será exibido para os alunos.</span>
				</div>

				<style>
					@import url("https://fonts.googleapis.com/css2?family=Anton&family=Great+Vibes&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=REM:wght@900&display=swap");

					.preview-certificado {
						position: relative;
						border-radius: var(--wh-radius-lg);
						overflow: hidden;
						box-shadow: var(--wh-shadow-soft);
						border: 1px solid rgba(244, 208, 63, 0.16);
						background: linear-gradient(135deg, rgba(15, 17, 24, 0.85), rgba(5, 5, 8, 0.9));
					}

					.preview-certificado .canvas {
						padding: 110px 80px 100px;
						min-height: 560px;
						display: flex;
						flex-direction: column;
						align-items: center;
						justify-content: center;
						text-align: center;
						background: url(<?= $fundo ?>) center/cover no-repeat;
						color: #000;
					}

					.preview-certificado .titulo {
						font-size: 4.6rem;
						margin-bottom: 0.2em;
						font-family: <?= json_encode($fonte_titulo) ?>;
						color: <?= $cor_titulo ?>;
						text-transform: uppercase;
						letter-spacing: 0.16em;
					}

					.preview-certificado .subtitulo {
						font-family: <?= json_encode($fonte_subtitulo) ?>;
						font-size: 2.4rem;
						margin-top: -1.6rem;
						color: <?= $cor_subtitulo ?>;
						letter-spacing: 0.32em;
						text-transform: uppercase;
					}

					.preview-certificado .descricao {
						margin-top: 2.2rem;
						font-size: 1.6rem;
						line-height: 1.6;
						max-width: 720px;
						color: <?= $cor_descricao ?>;
						font-family: <?= json_encode($fonte_descricao) ?>;
					}

					.preview-certificado .destaque {
						display: block;
						font-size: 2.3rem;
						font-weight: 600;
						color: <?= $cor_destaque ?>;
						font-family: <?= json_encode($fonte_destaque) ?>;
						margin: 0.6rem 0;
					}

					.preview-certificado .assinaturas {
						display: flex;
						gap: 3rem;
						margin-top: 2.8rem;
						justify-content: center;
					}

					.preview-certificado .assinatura {
						min-width: 220px;
						border-top: 1px solid rgba(0, 0, 0, 0.35);
						padding-top: 0.8rem;
						font-size: 0.95rem;
						text-transform: uppercase;
						letter-spacing: 0.14em;
					}
				</style>

				<div class="preview-certificado">
					<div class="canvas">
						<h1 class="titulo">Certificado</h1>
						<h2 class="subtitulo">de conclusão</h2>

						<p class="descricao">
							Certificamos que
							<span class="destaque"><?= $curso->dataProdutor()->nome ?? 'Nome do aluno'; ?></span>
							concluiu o curso
							<span class="destaque"><?= $curso->nome; ?></span>
							em <?= date('d/m/Y'); ?>
						</p>

						<div class="assinaturas">
							<div class="assinatura">Coordenação</div>
							<div class="assinatura">Instrutor responsável</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</section>
</div>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Certificado</title>
</head>

<body>
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
	<style>
		@import url("https://fonts.googleapis.com/css2?family=Anton&family=Great+Vibes&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=REM:wght@900&display=swap");

		.corpo {
			padding-top: 100px;
			height: 870px;
			width: 1370px;
			border: 1px solid #666;
			text-align: center;
			background: url(<?= $fundo ?>) no-repeat;
			background-size: 1370px 970px;
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
			margin-top: -80px;
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
</body>

</html>
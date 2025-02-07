<?php

// Carregar o Composer

use Source\Models\Usuarios\Usuario;

require 'vendor/autoload.php';

// QUERY para recuperar os registros do banco de dados
$query_usuarios = "SELECT id, nome, email FROM usuarios";

// Prepara a QUERY
$result_usuarios = (new Usuario)->find()->fetch(true);

// Informacoes para o PDF
// $dados = "<!DOCTYPE html>";
// $dados .= "<html lang='pt-br'>";
// $dados .= "<head>";
// $dados .= "<meta charset='UTF-8'>";
// $dados .= "<link rel='stylesheet' href='http://localhost/celke/css/custom.css'";
// $dados .= "<title>Celke - Gerar PDF</title>";
// $dados .= "</head>";
// $dados .= "<body>";
// $dados .= "<h1>Listar os Usuário</h1>";

// // Ler os registros retornado do BD
// foreach ($result_usuarios as $usuario) {
//     //var_dump($row_usuario);
//     $dados .= "ID: $usuario->id <br>";
//     $dados .= "Nome: $usuario->nome <br>";
//     $dados .= "E-mail: <span style='color: green'>$usuario->email</span> <br>";
//     $dados .= "<hr>";
// }

// $dados .= "<img src='http://localhost/celke/imagens/celke.jpg'><br>";
// $dados .= "O PHP proin iaculis, libero et dictum fringilla, ex metus scelerisque mauris, sit amet lobortis enim justo quis arcu. Proin eget pharetra ipsum, eget auctor purus.";
// $dados .= "</body>";

// echo $dados;

$dados = '
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Certificado</title>

        <style>
            html, body {
                background: url("' . url('themes/imgs/background-certificado.jpg') . '") no-repeat;
                background-size: cover;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            .corpo {
                /*height: 707px;
                width: 1000px;*/
                /*border: 1px solid #666;*/
                text-align: center;
                /* display: flex;
                align-items: center; */
            }

            .titulo {
                text-align: center;
                font-size: 80px;
                font-family: "REM";
                text-shadow: 2px 2px 2px rgb(247, 141, 2);
                color: #1D465F;
            }

            .subtitulo {
                text-align: center;
                font-family: "Great Vibes";
                font-size: 40px;
                margin-top: -80px;
                letter-spacing: 4px;
            }

            .descricao {
                font-size: 30px;
                font-family: Poppins;
            }

            .destaque {
                font-size: 40px;
                font-weight: 600;
            }
        </style>
    </head>

    <body>
        <div class="corpo">
            <h1 class="titulo">CERTIFICADO</h1>
            <h2 class="subtitulo">de conclusão</h2>

            <br>
            <p class="descricao">
                Certificamos que <br>
                <span class="destaque">Pedro Henrique da Silva</span>
                <br><br>concluiu o curso de <br>
                <span class="destaque">"Marqueting Digital"</span>
            </p>

            <p class="descricao">21/08/2023</p>
        </div>
    </body>

</html>
';

// Referenciar o namespace Dompdf
use Dompdf\Dompdf;

// Instanciar e usar a classe dompdf
$dompdf = new Dompdf(['enable_remote' => true]);

// Instanciar o metodo loadHtml e enviar o conteudo do PDF
$dompdf->loadHtml($dados);

// Configurar o tamanho e a orientacao do papel
// landscape - Imprimir no formato paisagem
$dompdf->setPaper('A4', 'landscape');
// portrait - Imprimir no formato retrato
// $dompdf->setPaper('A4', 'portrait');

// Renderizar o HTML como PDF
$dompdf->render();

// Gerar o PDF
$dompdf->stream();

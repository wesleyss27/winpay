<?php


//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Cursos;

session_start();

use League\Plates\Engine;
use Source\Models\Cursos\Cursos;
use Source\App\Sessao\AppSessao;
use Source\Helpers\File;
use Source\Helpers\Paginador;
use Source\Models\Aulas\Aulas;
use Source\Models\AulasConcluidas\AulaConcluida;
use Source\Models\Certificados\Certificado;
use Source\Models\Comentarios\Comentario;
use TCPDF;

class AppCertificados
{
    private $view;
    private $pastaCursos;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->pastaCursos = PASTA . "cursos/";

        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    public function certificado($data)
    {
        $curso = (new Cursos)->findById($data["curso"]);
        $certificado = (new Certificado)->find("curso = $curso->id")->fetch();

        echo $this->view->render("cursos/certificado", [
            "title" => "Certificado",
            "curso" => $curso,
            "certificado" => $certificado,
        ]);
    }

    public function salvarCertificado($data)
    {
        if (@$data["id"]) {
            $certificado = (new Certificado)->findById($data["id"]);
        }
        if (!@$certificado) {
            $certificado = (new Certificado);
        }

        $certificado->curso = @$data["curso"];
        $certificado->fonte_titulo = @$data["fonte_titulo"];
        $certificado->cor_titulo = @$data["cor_titulo"];
        $certificado->fonte_subtitulo = @$data["fonte_subtitulo"];
        $certificado->cor_subtitulo = @$data["cor_subtitulo"];
        $certificado->fonte_destaque = @$data["fonte_destaque"];
        $certificado->cor_destaque = @$data["cor_destaque"];
        $certificado->fonte_descricao = @$data["fonte_descricao"];
        $certificado->cor_descricao = @$data["cor_descricao"];

        $certificado->usuario = @$_SESSION["id"];

        $files = new File;
        if (@$_FILES["fundo"]["name"]) {
            $foto_atual = $certificado->fundo;
            $certificado->fundo = $files->upload($_FILES["fundo"], PASTA . "cursos/");
            if (@$foto_atual) {
                @unlink(PASTA . "cursos/" . @$foto_atual);
            }
        }

        if (@$data["fundo_padrao"] == 1) {
            $foto_atual = $certificado->fundo;
            if (@$foto_atual) {
                @unlink(PASTA . "cursos/" . @$foto_atual);
            }
            $certificado->fundo = null;
        }

        if ($certificado->save()) {
            header("Location: " . url("certificados/$certificado->curso?msg=Salvo com sucesso"));
        } else {
            header("Location: " . url("certificados/$certificado->curso?msg=Erro ao salvar. {$certificado->fail()->getMessage()}"));
        }
    }

    public function verCertificado($data)
    {
        $curso = (new Cursos)->findById($data["curso"]);
        $certificado = (new Certificado)->find("curso = $curso->id")->fetch();

        $aulasTotal = (new Aulas)->find("curso = $curso->id")->count();
        $aulasConcluidas = (new AulaConcluida)->find("aluno = " . $_SESSION["id"] . " AND curso = $curso->id")->count();

        if ($aulasConcluidas == $aulasTotal) {

            echo $this->view->render("cursos/ver_certificado", [
                "title" => "Certificado",
                "curso" => $curso,
                "certificado" => $certificado,
            ]);
        }
    }

    public function baixarCertificado($data)
    {
        // $arquivo = "vendas.pdf";

        // header("Cache-Control: no-cache, must-revalidate");
        // header('Pragma: no-cache');
        // header('Content-Type: application/pdf');
        // header("Content-Disposition: attachment; filename=\"{$arquivo}\"");

        // echo "Teste<br>";

        $pdf = new TCPDF();  // Crie uma instância do TCPDF

        // Defina a orientação da página como paisagem
        $pdf->SetPageOrientation('L'); // 'L' para paisagem, 'P' para retrato

        // Defina o título do documento
        $pdf->SetTitle('Meu Primeiro PDF com PHP');

        // Adicione uma página
        $pdf->AddPage();

        // Defina a fonte e o tamanho
        $pdf->SetFont('helvetica', '', 12);

        // Adicione texto ao PDF
        $pdf->Cell(0, 10, 'Meu Primeiro PDF com PHP', 0, 1, 'C');

        // Saída do PDF para o navegador ou arquivo
        $pdf->Output('meu_primeiro_pdf.pdf', 'I');
    }

    public function comentarios($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 20;

        /*  $filtro = $this->filtros();
        if($filtro){
            $filtros = $filtro . " AND ";
        } else {
            $filtros = "";
        } */

        $cursoId = $data['curso'];

        $comentariosCount = (new Comentario())->find("curso = $cursoId")->count();
        $paginas = ceil($comentariosCount / $offset);
        $comentarios = (new Comentario)->find("curso = $cursoId")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        $curso = (new Cursos)->findById($cursoId);

        echo $this->view->render("comentarios/comentarios", [
            "title" => "Comentários",
            "curso" => @$curso,
            "comentarios" => @$comentarios,
            "count" => @$comentariosCount,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1
        ]);
    }
}

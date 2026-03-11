<?php

namespace Source\App\Home;

session_start();

use League\Plates\Engine;
use Source\Models\Produtos\Produto;
use Source\Models\Turmas\Turma;
use Source\Models\Turmaaluno\Turmaaluno;
use Source\Models\Cursos\Cursos;
use Source\Models\Modulos\Modulos;
use Source\Models\Aulas\Aulas;
use Source\App\Sessao\AppSessao;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Emails;
use Source\Models\AulasConcluidas\AulaConcluida;
use Source\Models\Sessao\Sessao;

class AppHome
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

    public function home($data)
    {
        // Se for aluno (tipo 6), direciona para a área correta
        if (!empty($_SESSION["tipo"]) && (int) $_SESSION["tipo"] === 6) {
            header("Location: " . url("minhas-compras"));
            exit;
        }

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $usuario = $_SESSION["id"];

        $curso = (new Cursos())->find()->fetch();
        $alunosCount = (new Turmaaluno())->find("usuario = $usuario")->count();

        $alunosTotal = (new Turmaaluno())->find("usuario = $usuario")->count();
        $sessao = new Sessao;
        $aulaCl = (new AulaConcluida);
        $aulaObj = (new Aulas);

        $totalAulas = $aulaObj->find("usuario = $usuario")->count();

        // CONTA O TOTAL DE AULAS POR CURSO
        $totalAulasCursos = $aulaObj->find("usuario = $usuario GROUP BY curso", null, "curso, COUNT(id) as soma")->fetch(true);

        // CONTA O TOTAL DE AULAS CONCLUÍDAS POR CURSO
        $total = $aulaCl->find("produtor = $usuario GROUP BY aluno", null, "curso, COUNT(id) as soma")->fetch(true);

        // Inicializa um array para armazenar o número de alunos que concluíram cada curso
        $alunosConcluidosPorCurso = [];
        $soma = 0;

        // Percorre os resultados da consulta de aulas concluídas
        if ($total) {
            foreach ($total as $aulaConcluida) {
                $curso = $aulaConcluida->curso;
                $totalConcluidas = $aulaConcluida->soma;
                $soma += $aulaConcluida->soma;

                // Encontra o total de aulas para o curso atual
                $totalAulasCurso = 0;
                foreach ($totalAulasCursos as $aula) {
                    if ($aula->curso == $curso) {
                        $totalAulasCurso = $aula->soma;
                        break;
                    }
                }

                // Calcula o número de alunos que concluíram o curso com base na proporção de aulas concluídas
                $alunosConcluidos = ($totalConcluidas / $totalAulasCurso) * $totalAulasCurso;

                // Arredonda o resultado para obter um número inteiro de alunos
                $alunosConcluidos = round($alunosConcluidos);

                // Armazena o resultado no array
                $alunosConcluidosPorCurso[$curso] = $alunosConcluidos;
            }
        }

        // Agora você tem o número aproximado de alunos que concluíram cada curso com base na proporção de aulas concluídas
        // print_r($alunosConcluidosPorCurso);
        $concluiu = 0;
        if ($alunosConcluidosPorCurso) {
            foreach ($alunosConcluidosPorCurso as $concluidos) {
                $concluiu++;
            }
        }

        if ($alunosTotal > 0) {
            $conclusao = round(@$concluiu / @$alunosTotal * 100);
            $media = round(($soma / ($totalAulas * $alunosTotal)) * 100);
        } else {
            $conclusao = 0;
            $media = 0;
        }

        $cursoMaisVendido = (new Turmaaluno)->find("usuario = $usuario GROUP BY curso ORDER BY quantidade_alunos DESC", null, "curso, COUNT(aluno) AS quantidade_alunos")->fetch();

        echo $this->view->render("home/home", [
            "title" => "Home",
            "sessao" => $sessao,
            "count" => $alunosCount,
            "curso" => $curso,
            "paginador" => @$paginador,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "aulaCl" => $aulaCl,
            "aulaObj" => $aulaObj,
            "media" => $media,
            "totalAulas" => $totalAulas,
            "conclusao" => $conclusao,
            "cursoMaisVendido" => $cursoMaisVendido,
        ]);
    }
}

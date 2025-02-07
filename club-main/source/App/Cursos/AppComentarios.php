<?php


//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Cursos;

session_start();

use League\Plates\Engine;
use Source\Models\Produtos\Produto;
use Source\Models\Cursos\Cursos;
use Source\Models\Modulos\Modulos;
use Source\Models\Aulas\Aulas;
//~ use Source\Models\Produtos\Plano;
use Source\App\Sessao\AppSessao;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Models\Comentarios\Comentario;
use Source\Models\Turmaaluno\Turmaaluno;

class AppComentarios
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

<?php


//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Cursos;

session_start();

use League\Plates\Engine;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Produtos\Produto;
use Source\Models\Cursos\Cursos;
use Source\Models\Modulos\Modulos;
use Source\Models\Aulas\Aulas;
//~ use Source\Models\Produtos\Plano;
use Source\App\Sessao\AppSessao;
use Source\Models\Checkout\Checkout;
use Source\Models\Campanhas\Campanha;
use Source\Models\Pixel\Pixel;
use Source\Models\URL\Url;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Emails;
use Source\Models\Turmaaluno\Turmaaluno;

class AppCursos
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

    public function cursos($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        /*  $filtro = $this->filtros();
        if($filtro){
            $filtros = $filtro . " AND ";
        } else {
            $filtros = "";
        } */

        $curso = new Cursos();

        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {
            $idProdutor = @$_SESSION["id"];
            $cursosCount = (new Cursos())->find("usuario = :idc", "idc={$idProdutor}")->count();
            $paginas = ceil($cursosCount / $offset);
            $cursos = $curso->find("usuario = :idc", "idc={$idProdutor}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        } else {
            $idProdutor = @$_SESSION["id"];
            $cursosCount = (new Cursos())->find("usuario = :idc", "idc={$idProdutor}")->count();
            $paginas = ceil($cursosCount / $offset);
            $cursos = $curso->find("usuario = :idc", "idc={$idProdutor}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        }

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();
        $turmaAluno = new Turmaaluno();
        $aula =  new Aulas();

        echo $this->view->render("cursos/cursos", [
            "title" => "Cursos",
            "cursos" => @$cursos,
            "turmaAluno" => @$turmaAluno,
            "aula" => @$aula,
            "usuarioDados" => @$usuarioDados,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1
        ]);
    }

    public function cursosCadastrados($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        /*  $filtro = $this->filtros();
        if($filtro){
            $filtros = $filtro . " AND ";
        } else {
            $filtros = "";
        } */

        $curso = new Cursos();

        $idProdutor = @$_SESSION["id"];
        $cursosCount = (new Cursos())->find()->count();
        $paginas = ceil($cursosCount / $offset);
        $cursos = $curso->find()->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();
        $turmaAluno = new Turmaaluno();
        $aula =  new Aulas();

        echo $this->view->render("cursos/cursos-cadastrados", [
            "title" => "Cursos",
            "cursos" => @$cursos,
            "turmaAluno" => @$turmaAluno,
            "aula" => @$aula,
            "usuarioDados" => @$usuarioDados,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1
        ]);
    }

    public function detalhes($data)
    {
        $cursos = new Cursos();
        $modulos =  new Modulos();
        $aulas =  new Aulas();
        $cursos = (new Cursos())->findById($data["codigo"]);
        $moduloss = $modulos->find("curso = :idc", "idc={$data["codigo"]}")->order("id ASC")->fetch(true);
        $aulass = $aulas->find("curso = :idc", "idc={$data["codigo"]}")->order("ordem ASC")->fetch(true);
        $usuario = (new UsuarioDados())->find("usuario = $cursos->usuario")->fetch();

        echo $this->view->render("cursos/detalhe_cursos", [
            "title" => @$cursos->nome,
            "cursos" => $cursos,
            "modulos" => $moduloss,
            "aulas" => $aulass,
            "usuario" => $usuario,
            "msg" => ""
        ]);
    }

    public function statusCurso($data)
    {
        $curso = (new Cursos())->findById($data["id"]);
        $curso->status = @$data["status-curso"];

        if ($curso->save()) {
            echo "Atualizado com sucesso";
        } else {
            echo "Erro ao atualizar";
        }
    }

    // CADASTRO CURSOS
    public function novo($data)
    {
        $idProdutor = @$_SESSION["id"];
        $produto = new Produto();
        $produtos = $produto->find("usuario = :idc AND excluir = 0", "idc={$idProdutor}")->order("id DESC")->fetch(true);

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        if (!empty($_GET['course_id'])) {
            $curso = new Cursos();
            $cursos = $curso->find("id = :idc", "idc={$_GET['course_id']}")->order("id DESC")->fetch(true);
        } else {
            $cursos = '';
        }

        echo $this->view->render("cursos/novo_cursos", [
            "produtos" => @$produtos,
            "cursoss" => @$cursos,
            "title" => "Novo Coursos",
            "usuarioDados" => '',
        ]);
    }

    public function cadastro($data)
    {
        $files = new File();

        if (!empty($data['course_id'])) {
            $cursos = (new Cursos())->findById($data["course_id"]);
        } else {
            $cursos = new Cursos();
        }

        $id = $cursos->find()->limit(1)->order("id DESC")->fetch(true);
        $cursos->usuario = $_SESSION["id"];

        if ($data["nome"] == "" || $data["nome"] == " ") {
            $msg = "Preencha todos os campos";
            $classCss = "bg-warning";
            header("Location: " . url("cursos/novo?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }

        $cursos->produto = $data["produto"];
        $cursos->nome = $data["nome"];
        $cursos->assunto = $data["assunto"];
        $cursos->formato = $data["formato"];
        $cursos->descricao = $data["descricao"];
        $cursos->liberacao = $data["liberacao"];
        $cursos->status = 0;

        if (!empty($data['course_id'])) {
            if (empty(@$_FILES["imagem"]["name"])) {
                $cursos->imagem = $data["course_img"];
            } else {
                $cursos->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
            }
        } else {
            $cursos->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
        }

        if ($cursos->save()) {
            $msg = "Salvo com sucesso";
            // $msg .= $cursos->fail()->getMessage();
            $classCss = "bg-success";

            header("Location: " . url("cursos/modulo/" . $cursos->id));
        } else {
            $msg = $cursos->fail()->getMessage();
            $classCss = "bg-danger";

            header("Location: " . url("cursos/novo" . "?msg=" . $msg . "&classCss=" . $classCss));
            //~ $msg .= $cursos->fail()->getMessage();
            //~ $classCss = "bg-danger";
        }
    }
    // FIM CADASTRO CURSOS

    // EDITAR CURSOS
    public function editarcursos($data)
    {
        $idProdutor = @$_SESSION["id"];
        $produtosCount = (new Produto())->find("usuario = :idc", "idc={$idProdutor}")->count();
        $produto = new Produto();
        $produtos = $produto->find("usuario = :idc", "idc={$idProdutor}")->order("id DESC")->fetch(true);

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        if (!empty($_GET['course_id'])) {
            $curso = (new Cursos())->find("id = :idc", "idc={$_GET['course_id']}")->order("id DESC")->fetch();
        } else {
            $curso = '';
        }

        echo $this->view->render("cursos/editar_cursos", [
            "produtos" => @$produtos,
            "curso" => @$curso,
            "title" => "Editar Curso",
            "usuarioDados" => '',
        ]);
    }

    public function editarcadastro($data)
    {
        $files = new File();

        if (!empty($data['course_id'])) {
            $cursos = (new Cursos())->findById($data["course_id"]);
        } else {
            $cursos = new Cursos();
        }

        $id = $cursos->find()->limit(1)->order("id DESC")->fetch(true);
        $cursos->usuario = $_SESSION["id"];

        if ($data["nome"] == "" || $data["nome"] == " ") {
            $msg = "Preencha todos os campos";
            $classCss = "bg-warning";
            header("Location: " . url("cursos/editarcursos?course_id" . $data['course_id'] . "&msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }

        $cursos->produto = $data["produto"];
        $cursos->nome = $data["nome"];
        $cursos->assunto = $data["assunto"];
        $cursos->formato = $data["formato"];
        $cursos->descricao = $data["descricao"];
        $cursos->liberacao = $data["liberacao"];
        $cursos->status = 0;

        if (!empty($data['course_id'])) {
            if (empty(@$_FILES["imagem"]["name"])) {
                $cursos->imagem = $data["course_img"];
            } else {
                $cursos->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
            }
        } else {
            $cursos->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
        }

        //echo $data["liberacao"];

        if ($cursos->save()) {
            $msg = "Salvo com sucesso";
            $classCss = "bg-success";
            header("Location: " . url("cursos/detalhe/" . $cursos->id));
        } else {
            $msg = $cursos->fail()->getMessage();
            $classCss = "bg-danger";
            header("Location: " . url("cursos/editarcursos?course_id?" . $data['course_id'] . "&msg=" . $msg . "&classCss=" . $classCss));
        }
    }
    // FIM EDITAR CURSOS

    // MÓDULOS
    public function modulo($data)
    {
        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        if (!empty($_GET['module_id'])) {
            $modulos = new Modulos();
            $moduloss = $modulos->find("id = :idc", "idc={$_GET['module_id']}")->order("id DESC")->fetch(true);
        } else {
            $moduloss = '';
        }
        $curso = (new Cursos)->findById($data['courseid']);

        echo $this->view->render("cursos/modulo", [
            "title" => "Novo Módulo",
            "curso" => $curso,
            "moduloss" => @$moduloss,
            "course_id" => $data['courseid'],
            "usuarioDados" => '',
        ]);
    }

    public function cadastromodulo($data)
    {
        $files = new File();
        $modulos =  new Modulos();

        if (!empty($data['module_id'])) {
            $modulos = (new Modulos())->findById($data["module_id"]);
        } else {
            $modulos =  new Modulos();
        }

        if ($data["module_nome"] == "" || $data["module_nome"] == " ") {
            $msg = "Preencha todos os campos";
            $classCss = "bg-warning";
            header("Location: " . url("cursos/modulo" . $data['course_id'] . "?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }

        $modulos->usuario = $_SESSION["id"];
        $modulos->nome =  $data["module_nome"];
        $modulos->descricao = $data["module_descricao"];
        $modulos->curso = $data['course_id'];


        if ($_SESSION["tipo"] == 5) {
            $usuario = (new Usuario())->findById($_SESSION["id"]);
            $usuario->tipo_usuario = 4;
            $usuario->save();
            $_SESSION["tipo"] = 4;
        }


        if ($modulos->save()) {
            $msg = "data  saved sucessfully";
            $classCss = "bg-success";

            header("Location: " . url("cursos/aulas/" . $data['course_id']));
        } else {
            $msg = $modulos->fail()->getMessage();
            $classCss = "bg-danger";

            header("Location: " . url("cursos/modulo" . $data['course_id']));
        }
    }

    public function editarmodulo($data)
    {
        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        if (!empty($_GET['module_id'])) {
            $modulos = new Modulos();
            $moduloss = $modulos->find("id = :idc", "idc={$_GET['module_id']}")->order("id DESC")->fetch(true);
        } else {
            $moduloss = '';
        }

        $curso = (new Cursos)->findById($data['courseid']);

        echo $this->view->render("cursos/editar_modulo", [
            "moduloss" => @$moduloss,
            "curso" => $curso,
            "title" => "Novo Modulo",
            "course_id" => $data['courseid'],
            "usuarioDados" => '',
        ]);
    }

    public function cadastreditoromodulo($data)
    {
        

        $files = new File();
        $modulos =  new Modulos();

        if (!empty($data['module_id'])) {
            $modulos = (new Modulos())->findById($data["module_id"]);
        } else {
            $modulos =  new Modulos();
        }

        if ($data["module_nome"] == "" || $data["module_nome"] == " ") {
            $msg = "Preencha todos os campos";
            $classCss = "bg-warning";
            header("Location: " . url("cursos/modulo" . $data['course_id'] . "?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }

        $modulos->usuario = $_SESSION["id"];
        $modulos->nome =  $data["module_nome"];
        $modulos->descricao = $data["module_descricao"];
        $modulos->curso = $data['course_id'];


        if ($_SESSION["tipo"] == 5) {
            $usuario = (new Usuario())->findById($_SESSION["id"]);
            $usuario->tipo_usuario = 4;
            $usuario->save();
            $_SESSION["tipo"] = 4;
        }


        if ($modulos->save()) {
            $msg = "data  saved sucessfully";
            $classCss = "bg-success";

            header("Location: " . url("cursos/detalhe/" . $data["course_id"]));
        } else {
            $msg = $modulos->fail()->getMessage();
            $classCss = "bg-danger";

            header("Location: " . url("cursos/editarmodulo/" . $data['course_id']));
        }
    }
    // FIM MÓDULOS

    public function aulas($data)
    {
        $courseid = $data['courseid'];
        if (!empty($_GET['module_id'])) {
            $modulos = new Modulos();
            $modulos = $modulos->find("id = :idc", "idc={$_GET['module_id']}")->order("id DESC")->fetch(true);
        } else {
            $modulos = new Modulos();
            $modulos = $modulos->find("curso = :idc", "idc={$courseid}")->order("id DESC")->fetch(true);
        }
        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        $curso = (new Cursos)->findById($data['courseid']);
        
        echo $this->view->render("cursos/aulas", [
            "title" => "Nova Aula",
            "course_id" => $data['courseid'],
            "curso" => $curso,
            "modulos" => $modulos,
            "usuarioDados" => '',
        ]);
    }

    public function ordemAulas($data)
    {
        // var_dump($data);

        if(isset($data['order'])){
            $o = 1;
            foreach($data['order'] as $order){
                $aula = (new Aulas)->findById($order);

                if($aula){
                    $aula->ordem = $o;
                    $aula->save();
                    $o++;
                }
            }
        }
    }

    public function atualizaNomeAula($data)
    {
        $aula = (new Aulas)->findById($data['id']);
        $aula->nome = $data['nome'];
        $aula->save();
    }

    public function cadastroaulas($data)
    {
        if (@$data["script_player"]) {
            $script_player = base64_encode(@$data["script_player"]);
        } else {
            $script_player = null;
        }

        $files = new File();
        $aulas =  new Aulas();

        //~ $cursos->rascunho = @$data['rascunho'] != "" ? $data['rascunho'] : 0;
        //$produto->st4rt = $data["st4rt"];

        if ($data["aulas_nome"] == "" || $data["aulas_nome"] == " ") {
            $msg = "Preencha todos os campos";
            $classCss = "bg-warning";
            header("Location: " . url("cursos/aulas" . $data['course_id'] . "?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }

        $aulas->nome = $data["aulas_nome"];
        $aulas->usuario = $_SESSION["id"];
        $aulas->modulo = $data["modulo_sel"];
        $aulas->curso = $data["course_id"];
        $aulas->link_video = @$data["link_video"];
        $aulas->script_player = $script_player;
        $aulas->descricao = $data["aulas_descricao"];

        if (!empty($data['course_id'])) {
            if (empty(@$_FILES["imagem"]["name"])) {
                $aulas->imagem = $data["course_img"];
            } else {
                $aulas->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
            }
        } else {
            $aulas->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
        }

        if (!empty($data['aulas_id'])) {
            if (empty(@$_FILES["arquivo_anexo1"]["name"])) {
                $aulas->imagem = $data["anexo1_img1"];
            } else {
                $aulas->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo1"], $this->pastaCursos);
            }

            if (empty(@$_FILES["arquivo_anexo2"]["name"])) {
                $aulas->imagem = $data["anexo2_img2"];
            } else {
                $aulas->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo2"], $this->pastaCursos);
            }

            if (empty(@$_FILES["arquivo_anexo2"]["name"])) {
                $aulas->imagem = $data["anexo3_img3"];
            } else {
                $aulas->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo3"], $this->pastaCursos);
            }
        } else {
            $aulas->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo1"], $this->pastaCursos);
            $aulas->arquivo_anexo2 = $files->upload($_FILES["arquivo_anexo2"], $this->pastaCursos);
            $aulas->arquivo_anexo3 = $files->upload($_FILES["arquivo_anexo3"], $this->pastaCursos);
        }

        $aulas->link_externo = $data["links"];

        $cursos = (new Cursos())->findById($data["course_id"]);

        $cursos->status = 1;
        $cursos_update = $cursos->save();


        if ($_SESSION["tipo"] == 5) {
            $usuario = (new Usuario())->findById($_SESSION["id"]);
            $usuario->tipo_usuario = 4;
            $usuario->save();
            $_SESSION["tipo"] = 4;
        }

        if ($aulas->save()) {
            header("Location: " . url("cursos/detalhe/" . $data["course_id"]));
        } else {
            $msg = $cursos->fail()->getMessage();
            $classCss = "bg-danger";

            header("Location: " . url("cursos/aulas" . $data['course_id'] . "?msg=" . $msg . "&classCss=" . $classCss));
        }
    }

    public function editaraulas($data)
    {
        $courseid = $data['courseid'];
        if (!empty($_GET['module_id'])) {
            $modulos = new Modulos();
            $modulos = $modulos->find("id = :idc", "idc={$_GET['module_id']}")->order("id DESC")->fetch(true);
        } else {
            $modulos = new Modulos();
            $modulos = $modulos->find("curso = :idc", "idc={$courseid}")->order("id DESC")->fetch(true);
        }

        if (!empty($_GET['aulas_id'])) {
            $aulas = new Aulas();
            $aulass = $aulas->find("id = :idc", "idc={$_GET['aulas_id']}")->order("id DESC")->fetch(true);
        } else {
            $aulass = '';
        }

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        $curso = (new Cursos)->findById($data['courseid']);

        echo $this->view->render("cursos/editar_aulas", [
            "title" => "Novo Coursos",
            "course_id" => $data['courseid'],
            "curso" => $curso,
            "modulos" => $modulos,
            "aulass" => $aulass,
            "usuarioDados" => '',
        ]);
    }

    public function cadastroeditaraulas($data)
    {
        // var_dump(base64_encode($data["script_player"]));
        if (@$data["script_player"]) {
            $script_player = base64_encode(@$data["script_player"]);
        } else {
            $script_player = null;
        }
        // exit;

        $files = new File();

        if (!empty($data['aulas_id'])) {
            $aula = (new Aulas())->findById($data["aulas_id"]);
        } else {
            $aula =  new Aulas();
        }

        // var_dump($data);
        // exit;

        $aula->nome = $data["aulas_nome"];
        $aula->usuario = $_SESSION["id"];
        $aula->modulo = $data["modulo_sel"];
        $aula->curso = $data["course_id"];
        $aula->link_video = @$data["link_video"];
        $aula->script_player = $script_player;
        $aula->descricao = $data["aulas_descricao"];

        if (!empty($data['course_id'])) {
            if (empty(@$_FILES["imagem"]["name"])) {
                $aula->imagem = @$aula->dataCurso()->imagem;
            } else {
                $aula->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
            }
        } else {
            $aula->imagem = $files->upload($_FILES["imagem"], $this->pastaCursos);
        }

        if (!empty($data['aulas_id'])) {
            if (empty(@$_FILES["arquivo_anexo1"]["name"])) {
                $aula->arquivo_anexo1 = $data["anexo1_img1"];
            } else {
                $aula->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo1"], $this->pastaCursos);
            }
        } else {
            $aula->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo1"], $this->pastaCursos);
        }

        if (!empty($data['aulas_id'])) {
            if (empty(@$_FILES["arquivo_anexo2"]["name"])) {
                $aula->arquivo_anexo2 = $data["anexo2_img2"];
            } else {
                $aula->arquivo_anexo2 = $files->upload($_FILES["arquivo_anexo2"], $this->pastaCursos);
            }
        } else {
            $aula->arquivo_anexo2 = $files->upload($_FILES["arquivo_anexo2"], $this->pastaCursos);
        }

        if (!empty($data['aulas_id'])) {
            if (empty(@$_FILES["arquivo_anexo3"]["name"])) {
                $aula->arquivo_anexo3 = $data["anexo3_img3"];
            } else {
                $aula->arquivo_anexo3 = $files->upload($_FILES["arquivo_anexo3"], $this->pastaCursos);
            }
        } else {
            $aula->arquivo_anexo3 = $files->upload($_FILES["arquivo_anexo3"], $this->pastaCursos);
        }

        $aula->link_externo = @$data["links"];

        if ($aula->save()) {
            // echo $script_player;
            // echo $cursos->fail()->getMessage();
            header("Location: " . url("cursos/detalhe/" . $data["course_id"]));
        } else {
            $msg = $aula->fail()->getMessage();
            $classCss = "bg-danger";

            header("Location: " . url("cursos/editaraulas" . $data['course_id'] . "?msg=" . $msg . "&classCss=" . $classCss));
        }
    }

    public function urlExcluirAula($data)
    {
        

        $url = (new Aulas())->findById($data["id"]);
        if ($url->destroy()) {
            echo "1";
        } else {
            echo $url->fail()->getMessage();
        }
    }

    public function urlExcluirModule($data)
    {
        

        $aulas = (new Aulas())->find("modulo = " . $data["id"])->fetch(true);
        if ($aulas) {
            foreach ($aulas as $aula) {
                $aula->destroy();
            }
        }
        $url = (new Modulos())->findById($data["id"]);

        $url->destroy();

        if ($url->destroy()) {
            echo "1";
        } else {
            echo $url->fail()->getMessage();
        }
    }

    public function urlExcluirCursos($data)
    {
        

        $aulas = (new Aulas())->find("curso = " . $data["id"])->fetch(true);

        if ($aulas) {
            foreach ($aulas as $aula) {
                $aula->destroy();
            }
        }

        $modules = (new Modulos())->find("curso = " . $data["id"])->fetch(true);
        if ($modules) {
            foreach ($modules as $module) {

                $module->destroy();
            }
        }

        $course = (new Cursos())->findById($data["id"]);
        if ($course->destroy()) {
            echo "1";
        } else {
            echo $course->fail()->getMessage();
        }
    }
}

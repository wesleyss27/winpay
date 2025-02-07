<?php

namespace Source\App\Cursos;

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

class AppTurmas
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

    public function turmas($data)
    {

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 15;

        /*  $filtro = $this->filtros();
        if($filtro){
            $filtros = $filtro . " AND ";
        } else {
            $filtros = "";
        } */

        $idProdutor = @$_SESSION["id"];
        $cursosCount = (new Turma())->find("usuario = :idc", "idc={$idProdutor}")->count();
        $paginas = ceil($cursosCount / $offset);
        $turmas = (new Turma())->find("usuario = :idc", "idc={$idProdutor}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        $cursos = (new Cursos())->find("usuario = $idProdutor")->fetch(true);

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();
        $turmaAluno = new Turmaaluno();

        echo $this->view->render("turmas/turmas", [
            "title" => "Turmas",
            "turmas" => @$turmas,
            "turmaAluno" => @$turmaAluno,
            "cursos" => @$cursos,
            "usuarioDados" => @$usuarioDados,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function cadastrarTurma($data)
    {
        $turma = (new Turma())->findById($data["id"]);
    }

    public function modulo($data)
    {
        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        if (!empty($_GET['module_id'])) {
            $modulos = new Modulos();
            $moduloss = $modulos->find("id = :idc", "idc={$_GET['module_id']}")->order("id DESC")->fetch(true);
        } else {
            $moduloss = '';
        }

        echo $this->view->render("cursos/modulo", [
            "moduloss" => @$moduloss,
            "title" => "Novo Coursos",
            "course_id" => $data['courseid'],
            "usuarioDados" => '',
        ]);
    }

    public function cadastroAlunoTurma()
    {
        echo "<pre>";
        if (@$_GET["inicio"])
            $inicio = @$_GET["inicio"];
        else
            $inicio = date("Y-m-d");

        if (@$_GET["fim"])
            $fim = @$_GET["fim"];
        else
            $fim = date("Y-m-d");

        echo "inicio=" . @$inicio . "&fim=" . @$fim . "<br><br>";

        $vendas = (new Venda())->find("status_transacao = 4 AND aluno = 0 AND data_finalizada BETWEEN '$inicio 00:00:00' AND '$fim 23:59:59'")->fetch(true);

        if ($vendas) {
            foreach ($vendas as $venda) {
                echo "$venda->id <br>";
                if (@$venda->dataProduto()->tipo_produto == 11) {

                    $curso = (new Cursos())->find("produto = $venda->produto")->fetch();

                    if ($curso && $venda->id_comprador != "") {
                        echo "venda: $venda->id - usuario: $venda->id_comprador - produto: $venda->produto - curso: $curso->id <br>";
                        $turma = (new Turmaaluno())->find("aluno = $venda->id_comprador AND curso = $curso->id")->fetch();

                        if (!$turma) {
                            $turma = new Turmaaluno;

                            $turma->usuario = $curso->usuario;
                            $turma->aluno = $venda->id_comprador;
                            $turma->curso = $curso->id;

                            $data["nome"] = $venda->dataEndereco()->nome;
                            $data["email"] = $venda->dataEndereco()->email;
                            $data["nome_curso"] = $curso->nome;

                            echo "$turma->curso <br>";

                            if ($turma->save()) {
                                echo "Salvo com sucesso<br>";
                                $ven = (new Venda())->findById($venda->id);
                                $ven->aluno = 1;
                                $ven->save();

                                $this->email($data);
                            } else {
                                echo $turma->fail()->getMessage();
                            }
                        }
                    } else {
                        echo "Nenhum comprador encontrado<br>";
                    }
                } else {
                    echo "Nenhum curso encontrado<br>";
                }
            }
        }
    }

    public function alunosTurma($data)
    {
        $usuario = $_SESSION["id"];
        $cursos = (new Cursos())->find("usuario = {$usuario}")->order("nome ASC")->fetch(true);

        echo $this->view->render("turmas/turmas", [
            "title" => "Turmas 2",
            "cursos" => $cursos,
        ]);
    }

    public function alunos($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $usuario = $_SESSION["id"];
        $curso1 = $data["curso"];

        $filtros = $this->filtros();

        if (@$filtros)
            $filtros .= " AND ";

        $curso = (new Cursos())->findById($curso1);
        $alunosCount = (new Turmaaluno())->find($filtros . "usuario = $usuario AND curso = $curso1")->count();
        $paginas = ceil($alunosCount / $offset);
        $alunos = (new Turmaaluno())->find($filtros . "usuario = $usuario AND curso = $curso1")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        $alunosTotal = (new Turmaaluno())->find($filtros . "usuario = $usuario AND curso = $curso1")->count();
        $sessao = new Sessao;
        $aulaCl = (new AulaConcluida);
        $aulaObj = (new Aulas);

        $totalAulas = $aulaObj->find("curso = $curso->id")->count();
        $total = $aulaCl->find("curso = $curso->id GROUP BY aluno", null, "COUNT(id) as soma")->fetch(true);
        $soma = 0;
        if ($total) {
            $concluiu = 0;
            foreach ($total as $total) {
                $soma += $total->soma;

                if ($total->soma == $totalAulas) {
                    $concluiu++;
                }
            }
        }
        
        if ($alunosTotal > 0) {
            $conclusao = round(@$concluiu / @$alunosTotal * 100);
            $media = round($soma / $alunosTotal);
        } else {
            $conclusao = 0;
            $media = 0;
        }

        // var_dump($alunos);

        echo $this->view->render("turmas/alunos", [
            "title" => "Alunos",
            "alunos" => $alunos,
            "sessao" => $sessao,
            "count" => $alunosCount,
            "curso" => $curso,
            "paginador" => @$paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "aulaCl" => $aulaCl,
            "aulaObj" => $aulaObj,
            "media" => $media,
            "totalAulas" => $totalAulas,
            "conclusao" => $conclusao,
        ]);
    }

    public function filtros()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];
        $inicio_finalizada = @$_GET["inicio_finalizada"];
        $fim_finalizada = @$_GET["fim_finalizada"];

        $aluno = @$_GET["aluno"];
        $sql = array();

        if ($inicio != "" || $fim != "") {
            if ($inicio == "")
                $inicio = date("Y-m-d");

            if ($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "(created_at BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";
        }

        if ($inicio_finalizada != "" || $fim_finalizada != "") {
            if ($inicio_finalizada == "")
                $inicio_finalizada = date("Y-m-d");

            if ($fim_finalizada == "")
                $fim_finalizada = date("Y-m-d");

            $sql[] = "(data_finalizada BETWEEN '{$inicio_finalizada} 00:00:00' AND '{$fim_finalizada} 23:59:59')";
        }

        if ($aluno != "") {
            $sql[] = "aluno IN ( SELECT usuario.id FROM usuario WHERE nome LIKE '%$aluno%' OR email LIKE '%$aluno%')";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function email($data)
    {
        $data["assunto"] = "Bem vindo ao Club RedRocket";
        $data["mensagem"] = "<h2>Bem vindo ao Club RedRocket!</h2>";
        $data["mensagem"] .= "Olá " . @$data["nome"] . ", tudo bem?
        Você foi adicionado ao curso " . @$data["nome_curso"] . ".<br><br>
        Seu usuário cadastrado é:<br>
        <b>" . @$data["email"] . "</b><br><br><br>
        <a class='btn' href='#'>Acessar curso</a><br><br><br>
        Bons estudos!
        ";
        // $cadastroUsers = (new Emails())->cadastroUsuario($data);
        $enviaEmail = (new Emails())->enviaSendGrid($data);
        // echo $enviaEmail;
    }


    public function cadastroAlunoManual($data)
    {
        $usuario = (new Usuario())->find("email = '" . @$data["email"] . "'")->fetch();
        $curso = (new Cursos())->findById($data["curso"]);
        $data["nome_curso"] = $curso->nome;

        if ($usuario) {
            // var_dump($usuario);
            $turma = (new Turmaaluno())->find("curso = " . @$data["curso"] . " AND aluno = $usuario->id")->fetch();

            if ($turma) {
                echo "Usuário Já cadastrado";
            } else {
                $turma = new Turmaaluno();
                $turma->curso = @$data["curso"];
                $turma->aluno = @$usuario->id;
                $turma->status = 1;
                $turma->manual = 1;
                $turma->usuario = @$_SESSION["id"];

                if ($turma->save()) {
                    echo "Aluno cadastrado na turma com sucesso";
                    $this->email($data);
                } else {
                    echo $turma->fail()->getMessage();
                }
            }
        } else {
            $usuario = $this->cadastroUsuarios($data);
            $turma = new Turmaaluno();
            $turma->curso = @$data["curso"];
            $turma->aluno = @$usuario;
            $turma->status = 1;
            $turma->manual = 1;
            $turma->usuario = @$_SESSION["id"];

            if ($turma->save()) {
                echo "Aluno cadastrado na turma";
                $this->email($data);
            } else {
                echo $turma->fail()->getMessage();
            }
        }
    }

    public function bloquearAlunoTurma($data)
    {
        // var_dump($data);
        // exit;

        $aluno = (new Turmaaluno())->findById(@$data["id"]);

        if ($aluno) {
            $aluno->status = 2;
            if ($aluno->save()) {
                echo "Aluno bloqueado com sucesso";
            } else {
                echo "Erro ao bloquear aluno";
            }
        } else {
            echo "Aluno não encontrado";
        }
    }

    public function desbloquearAlunoTurma($data)
    {
        // var_dump($data);
        // exit;

        $aluno = (new Turmaaluno())->findById(@$data["id"]);

        if ($aluno) {
            $aluno->status = 1;
            if ($aluno->save()) {
                echo "Aluno desbloqueado com sucesso";
            } else {
                echo "Erro ao desbloquear aluno";
            }
        } else {
            echo "Aluno não encontrado";
        }
    }

    public function excluirAlunoTurma($data)
    {
        $aluno = (new Turmaaluno())->findById(@$data["id"]);

        if ($aluno->destroy()) {
            echo "Aluno excluído com sucesso";
        } else {
            echo "Erro ao excluir aluno";
        }
    }

    public function cadastroUsuarios($data)
    {


        function limpaStr($valor)
        {
            $valor = trim($valor);
            $valor = str_replace(".", "", $valor);
            $valor = str_replace(",", "", $valor);
            $valor = str_replace("-", "", $valor);
            $valor = str_replace("/", "", $valor);
            $valor = str_replace("(", "", $valor);
            $valor = str_replace(")", "", $valor);
            $valor = str_replace(" ", "", $valor);
            return strtolower($valor);
        }

        $email = @$data["email"];
        $usuario = (new Usuario());
        $usuario->nome = @$data["nome"];

        $nome = explode(" ", @$data["nome"], 2);
        $usuario->nome_abreviado = @$nome[0];
        $usuario->email = strtolower(@$email);
        $usuario->senha = password_hash(@$data['senha'],  PASSWORD_DEFAULT, ["cost" => 10]);
        $usuario->status = 1;
        $usuario->ativo = 1;
        $usuario->tipo_usuario = 5;
        $usuario->taxa_saque = 7.5;
        $usuario->taxa_redrocket = 7.5;
        $usuario->taxa_assinatura = 7.5;
        $usuario->tempo_saque = 30;
        $usuario->tempo_pix = 30;
        $usuario->tempo_boleto = 30;
        $usuario->grupo = 1;
        $usuario->tema = "tema1";
        $usuario->chave_unica = md5(time() . $usuario->nome);
        $usuario->cliente_st4rt = 0;

        $usuarioDados = new UsuarioDados();
        $usuarioDados->nome = $usuario->nome;
        $usuarioDados->email = $usuario->email;
        $usuarioDados->telefone = @$data["telefone"];
        $usuarioDados->tipo_doc = @$data["tipo_doc"];
        $usuarioDados->cpf_cnpj = @$data["cpf"];

        $email = $usuario->find("email = :email", "email={$data['email']}")->fetch(true);

        $usuario->save();
        $usuarioDados->usuario = $usuario->id;
        $usuarioDados->save();
        $usuario->usuario_dados = $usuarioDados->id;
        $usuario->save();
        $msg = "Cadastrado Com Sucesso!";
        echo "Cadastrado Com Sucesso!";
        $classCss = "bg-success";

        $data["id"] = $usuario->id;

        $data["msg"] = $msg;
        $data["classCss"] = $classCss;

        return $usuario->id;
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

        if ($_SESSION["tipo"] == 5) {
            $usuario = (new Usuario())->findById($_SESSION["id"]);
            $usuario->tipo_usuario = 4;
            $usuario->save();
            $_SESSION["tipo"] = 4;
        }

        if ($cursos->save()) {
            $msg = "data  saved sucessfully";
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


    public function cadastroaulas($data)
    {


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
        $aulas->link_video = $data["link_video"];
        $aulas->descricao = $data["aulas_descricao"];


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


    public function detalhes($data)
    {
        $cursos = new Cursos();
        $modulos =  new Modulos();
        $aulas =  new Aulas();
        $cursos = (new Cursos())->findById($data["codigo"]);
        $moduloss = $modulos->find("curso = :idc", "idc={$data["codigo"]}")->order("id ASC")->fetch(true);
        $aulass = $aulas->find("curso = :idc", "idc={$data["codigo"]}")->order("id ASC")->fetch(true);
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

    public function editarcursos($data)
    {

        $idProdutor = @$_SESSION["id"];
        $produtosCount = (new Produto())->find("usuario = :idc", "idc={$idProdutor}")->count();
        $produto = new Produto();
        $produtos = $produto->find("usuario = :idc", "idc={$idProdutor}")->order("id DESC")->fetch(true);

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        if (!empty($_GET['course_id'])) {
            $cursos = new Cursos();
            $cursoss = $cursos->find("id = :idc", "idc={$_GET['course_id']}")->order("id DESC")->fetch(true);
        } else {
            $cursoss = '';
        }


        echo $this->view->render("cursos/editar_cursos", [
            "produtos" => @$produtos,
            "cursoss" => @$cursoss,
            "title" => "Novo Coursos",
            "usuarioDados" => '',
        ]);
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

        echo $this->view->render("cursos/editar_modulo", [
            "moduloss" => @$moduloss,
            "title" => "Novo Modulo",
            "course_id" => $data['courseid'],
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



        if ($_SESSION["tipo"] == 5) {
            $usuario = (new Usuario())->findById($_SESSION["id"]);
            $usuario->tipo_usuario = 4;
            $usuario->save();
            $_SESSION["tipo"] = 4;
        }


        if ($cursos->save()) {
            $msg = "data  saved sucessfully";
            $classCss = "bg-success";

            header("Location: " . url("cursos/detalhe/" . $cursos->id));
        } else {

            $msg = $cursos->fail()->getMessage();
            $classCss = "bg-danger";

            header("Location: " . url("cursos/editarcursos?course_id?" . $data['course_id'] . "&msg=" . $msg . "&classCss=" . $classCss));
            //~ $msg .= $cursos->fail()->getMessage();
            //~ $classCss = "bg-danger";
        }
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

        echo $this->view->render("cursos/editar_aulas", [
            "title" => "Novo Coursos",
            "course_id" => $data['courseid'],
            "modulos" => $modulos,
            "aulass" => $aulass,
            "usuarioDados" => '',
        ]);
    }

    public function cadastroeditaraulas($data)
    {


        $files = new File();

        /* echo '<pre>';
		print_r($_FILES);
		echo '</pre>';
		exit; */
        /*echo '<pre>';
		print_r($_POST);
		echo '</pre>'; */


        if (!empty($data['aulas_id'])) {
            $aulas = (new Aulas())->findById($data["aulas_id"]);
        } else {
            $aulas =  new Aulas();
        }



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
        $aulas->link_video = $data["link_video"];
        $aulas->descricao = $data["aulas_descricao"];

        if (!empty($data['aulas_id'])) {
            if (empty(@$_FILES["arquivo_anexo1"]["name"])) {
                $aulas->arquivo_anexo1 = $data["anexo1_img1"];
            } else {
                $aulas->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo1"], $this->pastaCursos);
            }
        } else {
            $aulas->arquivo_anexo1 = $files->upload($_FILES["arquivo_anexo1"], $this->pastaCursos);
        }


        if (!empty($data['aulas_id'])) {
            if (empty(@$_FILES["arquivo_anexo2"]["name"])) {

                $aulas->arquivo_anexo2 = $data["anexo2_img2"];
            } else {

                $aulas->arquivo_anexo2 = $files->upload($_FILES["arquivo_anexo2"], $this->pastaCursos);
            }
        } else {
            $aulas->arquivo_anexo2 = $files->upload($_FILES["arquivo_anexo2"], $this->pastaCursos);
        }


        if (!empty($data['aulas_id'])) {
            if (empty(@$_FILES["arquivo_anexo3"]["name"])) {
                $aulas->arquivo_anexo3 = $data["anexo3_img3"];
            } else {
                $aulas->arquivo_anexo3 = $files->upload($_FILES["arquivo_anexo3"], $this->pastaCursos);
            }
        } else {
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

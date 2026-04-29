<?php
//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Minhascompras;

session_start();

use League\Plates\Engine;
use Source\Models\Turmaaluno\Turmaaluno;
use Source\Models\Cursos\Cursos;
use Source\Models\Comentarios\Comentario;
use Source\Models\Modulos\Modulos;
use Source\Models\Aulas\Aulas;
use Source\App\Sessao\AppSessao;
use Source\Models\Usuarios\UsuarioDados;
use Source\Helpers\Paginador;
use Source\Models\AulasConcluidas\AulaConcluida;
use Source\Models\Avaliacao\Avaliacao;
use Source\Models\Usuarios\Usuario;
use Source\Models\Ofertas\Oferta;
use CoffeeCode\DataLayer\Connect;

class AppMinhascompras
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

    public function minhascompras($data)
    {

        $aluno = @$_SESSION["id"];
        $offset = 15;
        $paginador = new Paginador();
        $pagina = @$data["page"];

        $turmaaluno = (new Turmaaluno())->find("aluno = :idc", "idc={$aluno}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);

        // $turmass=[];
        // if($turmaalunoo){
        //     foreach($turmaalunoo as $turmaalun)
        //     {
        //         $turma = new Turma();
        //         $turmass[] = $turma->find("id = :idc", "idc={$turmaalun->turma}")->order("id DESC")->fetch(true);
        //     }
        // }

        // $cursoss=[];
        // if ($turmass) {
        //     foreach($turmass as $truma_key=>$turmass_val){
        //         $cursos = new Cursos();
        //         $curso
        //         ss[] = $cursos->find("id = :idc", "idc={$turmass_val[0]->curso}")->order("id DESC")->fetch(true);
        //     } 
        // }

        // $cursosCount = count($cursoss);
        // $paginas = ceil($cursosCount / $offset);

        echo $this->view->render("minhascompras/minhascompras", [
            "title" => "Minhascompras",
            "turmaaluno" => @$turmaaluno,
            "turmas" => @$turmass,
            "cursoss" => @$cursoss,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function viewcursos($data)
    {
        $cursos = new Cursos();
        $modulos =  new Modulos();
        $aulas =  new Aulas();
        $cursos = (new Cursos())->findById($data["codigo"]);
        $moduloss = $modulos->find("curso = :idc", "idc={$data["codigo"]}")->order("id ASC")->fetch(true);
        $modulosCount = $modulos->find("curso = :idc", "idc={$data["codigo"]}")->order("id ASC")->count();
        $aulass = $aulas->find("curso = :idc", "idc={$data["codigo"]}")->order("ordem ASC, id ASC")->fetch(true);
        $usuario = (new UsuarioDados())->find("usuario = $cursos->usuario")->fetch();
        $turma = (new Turmaaluno())->find("curso = {$data["codigo"]} AND aluno = " . $_SESSION["id"])->fetch();
        $aulaCl = (new AulaConcluida);
        $aulaObj = (new Aulas);

        echo $this->view->render("minhascompras/view_cursos", [
            "title" => @$cursos->nome,
            "cursos" => $cursos,
            "modulos" => $moduloss,
            "modulosCount" => $modulosCount,
            "aulas" => $aulass,
            "usuario" => $usuario,
            "turma" => $turma,
            "aulaCl" => $aulaCl,
            "aulaObj" => $aulaObj,
        ]);
    }

    public function viewaulas($data)
    {
        $aula = (new Aulas())->findById($data["codigo"]);

        // Verifica liberação por dias após cadastro do aluno
        if ($aula && $aula->dias_liberacao !== null && $aula->dias_liberacao !== '') {
            $turma = (new Turmaaluno())->find("curso = {$aula->curso} AND aluno = " . $_SESSION["id"])->fetch();
            if ($turma) {
                $diasDesde = floor((time() - strtotime($turma->created_at)) / 86400);
                if ($diasDesde < (int)$aula->dias_liberacao) {
                    header("Location: " . url("minhas-compras/viewcursos/" . $aula->curso));
                    exit;
                }
            }
        }

        $modulo = (new Modulos())->find("id = :idc", "idc={$aula->modulo}")->fetch();
        $all_aulas = (new Aulas())->find("modulo = :idc", "idc={$aula->modulo}")->order("ordem ASC, id ASC")->fetch(true);

        // var_dump($aulas);
        $curso = (new Cursos())->findById($aula->curso);

        // Ofertas ativas para o curso
        $ofertaExibir  = null;
        $ofertasPlayer = [];
        $todasOfertas  = (new Oferta())
            ->find("curso = :c AND status = 1", "c={$aula->curso}")
            ->fetch(true);

        if ($todasOfertas) {
            $pdo    = Connect::getInstance();
            $sidebar = [];

            foreach ($todasOfertas as $o) {
                $stmtCo = $pdo->prepare("SELECT codigo FROM checkout WHERE id = :id LIMIT 1");
                $stmtCo->execute([':id' => $o->checkout_id]);
                $codigo = $stmtCo->fetchColumn();
                if (!$codigo) continue;

                $o->checkout_url = URL_BASE_WINPAY . "/checkout/" . $codigo;

                // Overlay no player: vinculada a esta aula com tempo configurado
                if ($o->aula_id == $aula->id && $o->exibir_em !== null) {
                    $ofertasPlayer[] = [
                        "exibir_em"   => (int) $o->exibir_em,
                        "nome"        => $o->nome,
                        "descricao"   => $o->descricao,
                        "valor"       => $o->valor,
                        "imagem"      => $o->imagem,
                        "checkout_url"=> $o->checkout_url,
                    ];
                } else {
                    $sidebar[] = $o;
                }
            }

            // Sidebar: oferta aleatória entre as sem overlay
            if ($sidebar) {
                $ofertaExibir = $sidebar[array_rand($sidebar)];
            }
        }

        $comentarios = (new Comentario())->find("resposta IS NULL AND aula = " . $data["codigo"])->fetch(true);
        $resposta = new Comentario();
        $aulaConcluida = (new AulaConcluida())->find("aula = $aula->id AND aluno = " . $_SESSION["id"])->fetch();
        $usuario = (new Usuario)->findById($_SESSION["id"]);
        $avaliacao = (new Avaliacao)->find("aula = $aula->id AND aluno = " . $_SESSION["id"])->fetch();

        echo $this->view->render("minhascompras/view_aulas", [
            "title" => @$aula->nome,
            "id" => @$data["codigo"],
            "curso" => $curso,
            "modulo" => $modulo,
            "aula" => $aula,
            "all_aulas" => $all_aulas,
            "comentarios" => $comentarios,
            "resposta" => $resposta,
            "aulaConcluida"  => $aulaConcluida,
            "usuario"        => $usuario,
            "avaliacao"      => $avaliacao,
            "ofertaExibir"   => $ofertaExibir,
            "ofertasPlayer"  => $ofertasPlayer,
        ]);
    }

    public function comentar($data)
    {
        $comentar = new Comentario();
        $comentar->produtor     = @$data["produtor"];
        $comentar->aluno        = @$_SESSION["id"];
        $comentar->modulo       = @$data["modulo"];
        $comentar->curso        = @$data["curso"];
        $comentar->aula         = @$data["aula"];
        $comentar->comentario   = @$data["comentario"];
        $comentar->resposta     = @$data["resposta"];

        if ($comentar->save()) {
            echo "Comentário adicionado";
        } else {
            echo "Erro ao adicionar comentário";
        }
    }

    public function aulaConcluida($data)
    {
        $aula = new AulaConcluida();
        $aula->produtor     = @$data["produtor"];
        $aula->aluno        = @$_SESSION["id"];
        $aula->modulo       = @$data["modulo"];
        $aula->curso        = @$data["curso"];
        $aula->aula         = @$data["aula"];

        if ($aula->save()) {
            echo "Atualizado com sucesso";
        } else {
            echo "Erro ao atualizar";
            // echo $aula->fail()->getMessage();
        }
    }

    public function avaliacao($data)
    {
        if (@$data["avaliacao_id"]) {
            $avaliacao = (new Avaliacao)->findById($data["avaliacao_id"]);
            $avaliacao->avaliacao = @$data["avaliacao"];
        } else {
            $avaliacao = new Avaliacao();
            $avaliacao->produtor     = @$data["produtor"];
            $avaliacao->aluno        = @$_SESSION["id"];
            $avaliacao->modulo       = @$data["modulo"];
            $avaliacao->curso        = @$data["curso"];
            $avaliacao->aula         = @$data["aula"];
            $avaliacao->avaliacao    = @$data["avaliacao"];
        }

        if ($avaliacao->save()) {
            echo "Atualizado com sucesso";
        } else {
            echo "Erro ao atualizar";
            // echo $aula->fail()->getMessage();
        }
    }
}

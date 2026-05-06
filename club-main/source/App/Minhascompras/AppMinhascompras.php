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
        $aluno     = (int) $_SESSION["id"];
        $offset    = 15;
        $paginador = new Paginador();
        $pagina    = @$data["page"];

        $turmaaluno = (new Turmaaluno())
            ->find("aluno = :idc", "idc={$aluno}")
            ->limit($offset)
            ->offset($paginador->paginador($pagina, $offset))
            ->order("id DESC")
            ->fetch(true);

        // Progresso e "Continuar assistindo" por curso
        $progressoCursos     = [];
        $continuarAssistindo = [];

        if ($turmaaluno) {
            $pdo     = Connect::getInstance();
            $aulaObj = new Aulas();
            $aulaCl  = new AulaConcluida();

            foreach ($turmaaluno as $entrada) {
                $curso = $entrada->dataCurso();
                if (!$curso) continue;

                // % progresso
                $total = (int) $aulaObj->find("curso = {$curso->id}")->count();
                $concluidas = 0;
                if ($total > 0) {
                    $regs = $aulaCl->find("aluno = {$aluno} AND curso = {$curso->id}")->fetch(true);
                    $concluidas = $regs ? count($regs) : 0;
                }
                $progressoCursos[$curso->id] = $total > 0 ? round(($concluidas / $total) * 100) : 0;

                // última aula assistida via video_progresso (não concluída ainda)
                $stmt = $pdo->prepare("
                    SELECT vp.aula_id, vp.current_time, vp.percent_watched, vp.last_watched_at,
                           a.nome AS aula_nome, a.imagem AS aula_imagem
                    FROM video_progresso vp
                    JOIN aulas a ON a.id = vp.aula_id
                    WHERE vp.usuario = :u AND a.curso = :c AND vp.percent_watched < 95
                    ORDER BY vp.last_watched_at DESC
                    LIMIT 1
                ");
                $stmt->execute([':u' => $aluno, ':c' => $curso->id]);
                $ultima = $stmt->fetch(\PDO::FETCH_OBJ);
                if ($ultima) {
                    $ultima->curso_id     = $curso->id;
                    $ultima->curso_nome   = $curso->nome;
                    $ultima->curso_imagem = $curso->imagem;
                    $ultima->curso_banner = $curso->banner ?? null;
                    $continuarAssistindo[] = $ultima;
                }
            }

            usort($continuarAssistindo, fn($a, $b) => strtotime($b->last_watched_at) - strtotime($a->last_watched_at));
        }

        // Banners para o hero slideshow (cursos com banner cadastrado)
        $heroBanners = [];
        if ($turmaaluno) {
            foreach ($turmaaluno as $entrada) {
                $curso = $entrada->dataCurso();
                if (!$curso || empty($curso->banner)) continue;
                $heroBanners[] = [
                    'banner'           => $curso->banner,
                    'imagem'           => $curso->imagem,
                    'nome'             => $curso->nome,
                    'boas_vindas'      => $curso->boas_vindas ?? null,
                    'curso_id'         => $curso->id,
                    'mostrar_titulo'   => (int)($curso->mostrar_titulo_aulas ?? 1),
                ];
            }
        }

        echo $this->view->render("minhascompras/minhascompras", [
            "title"               => "Minhas compras",
            "turmaaluno"          => $turmaaluno,
            "paginador"           => $paginador,
            "total"               => $paginas ?? 0,
            "pagina"              => $data["page"] ?? 1,
            "progressoCursos"     => $progressoCursos,
            "continuarAssistindo" => $continuarAssistindo,
            "heroBanners"         => $heroBanners,
        ]);
    }

    public function viewmodulos($data)
    {
        $aluno  = (int) $_SESSION["id"];
        $cursoId = (int) $data["codigo"];

        $curso  = (new Cursos())->findById($cursoId);
        $turma  = (new Turmaaluno())->find("curso = {$cursoId} AND aluno = {$aluno}")->fetch();

        if (!$turma) {
            header("Location: " . url("minhas-compras"));
            exit;
        }

        $diasDesde = $turma ? (int) floor((time() - strtotime($turma->created_at)) / 86400) : 0;

        // Módulos com progresso
        $modulosRaw = (new Modulos())->find("curso = :c", "c={$cursoId}")->order("id ASC")->fetch(true);

        $aulaCl  = new AulaConcluida();
        $aulaObj = new Aulas();
        $pdo     = Connect::getInstance();

        $concluidas = [];
        $regs = $aulaCl->find("aluno = {$aluno} AND curso = {$cursoId}")->fetch(true);
        if ($regs) foreach ($regs as $r) $concluidas[] = (int) $r->aula;

        $modulosData = [];
        if ($modulosRaw) {
            foreach ($modulosRaw as $mod) {
                $liberado = (
                    $curso->liberacao == 1 ||
                    ($curso->liberacao == 3 && $diasDesde >= 7) ||
                    $mod === $modulosRaw[0]
                );

                $aulasModulo = $aulaObj->find("modulo = {$mod->id}")->order("ordem ASC, id ASC")->fetch(true);
                $total       = $aulasModulo ? count($aulasModulo) : 0;
                $done        = 0;
                $primeiraAula = null;

                if ($aulasModulo) {
                    foreach ($aulasModulo as $a) {
                        if (in_array((int)$a->id, $concluidas)) $done++;
                        if (!$primeiraAula) {
                            $aulLibre = ($a->dias_liberacao === null || $a->dias_liberacao === '') || $diasDesde >= (int)$a->dias_liberacao;
                            if ($aulLibre) $primeiraAula = $a;
                        }
                    }
                }

                $progresso = $total > 0 ? round(($done / $total) * 100) : 0;

                $modulosData[] = [
                    'mod'          => $mod,
                    'liberado'     => $liberado,
                    'total'        => $total,
                    'concluidas'   => $done,
                    'progresso'    => $progresso,
                    'primeiraAula' => $primeiraAula,
                ];
            }
        }

        // Aulas em progresso (parcialmente assistidas < 95%)
        $emProgresso = [];
        $stmt = $pdo->prepare("
            SELECT vp.aula_id, vp.current_time, vp.percent_watched,
                   a.nome AS aula_nome, a.imagem AS aula_imagem, a.modulo AS aula_modulo,
                   m.nome AS modulo_nome
            FROM video_progresso vp
            JOIN aulas a ON a.id = vp.aula_id
            JOIN modulos m ON m.id = a.modulo
            WHERE vp.usuario = :u AND a.curso = :c AND vp.percent_watched < 95
            ORDER BY vp.last_watched_at DESC
            LIMIT 10
        ");
        $stmt->execute([':u' => $aluno, ':c' => $cursoId]);
        $emProgresso = $stmt->fetchAll(\PDO::FETCH_OBJ);

        echo $this->view->render("minhascompras/view_modulos", [
            "title"        => $curso->tema_titulo_exibicao ?? $curso->nome,
            "curso"        => $curso,
            "turma"        => $turma,
            "modulosData"  => $modulosData,
            "emProgresso"  => $emProgresso,
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

<?php

namespace Source\App\Cursos;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\File;
use Source\Models\Cursos\Aulas;
use Source\Models\Cursos\Cursos;
use Source\Models\Ofertas\Oferta;
use CoffeeCode\DataLayer\Connect;

class AppOfertas
{
    private $view;
    private $sessao;
    private $tema;
    private $pastaCursos;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
        $this->pastaCursos = realpath(__DIR__ . "/../../..") . "/uploads/cursos/";
        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    /** Lista as ofertas cadastradas para o curso */
    public function ofertas($data)
    {
        $curso = (new Cursos())->findById($data["courseid"]);

        $ofertas = (new Oferta())
            ->find("curso = :c", "c={$data["courseid"]}")
            ->order("id DESC")
            ->fetch(true);

        // Enriquece com dados do checkout/produto/plano via PDO
        $pdo = Connect::getInstance();
        if ($ofertas) {
            foreach ($ofertas as $o) {
                if ($o->checkout_id) {
                    $stmt = $pdo->prepare("
                        SELECT c.codigo, c.descricao, p.id AS produto_id, p.nome AS produto_nome, pl.id AS plano_id, pl.nome AS plano_nome
                        FROM checkout c
                        LEFT JOIN produto p  ON p.id  = c.produto
                        LEFT JOIN planos  pl ON pl.id = c.plano
                        WHERE c.id = :id LIMIT 1
                    ");
                    $stmt->execute([':id' => $o->checkout_id]);
                    $o->checkout_info = $stmt->fetch(\PDO::FETCH_OBJ);
                }
            }
        }

        // Produtos do produtor para o formulário
        $produtos = $pdo->prepare("SELECT id, nome FROM produto WHERE usuario = :u AND status = 2 ORDER BY nome ASC");
        $produtos->execute([':u' => $_SESSION["id"]]);
        $produtos = $produtos->fetchAll(\PDO::FETCH_OBJ);

        $aulas = (new Aulas())
            ->find("curso = :c", "c={$data["courseid"]}")
            ->order("ordem ASC, id ASC")
            ->fetch(true);

        echo $this->view->render("cursos/ofertas", [
            "title"    => "Ofertas do curso",
            "curso"    => $curso,
            "ofertas"  => $ofertas,
            "produtos" => $produtos,
            "aulas"    => $aulas ?? [],
        ]);
    }

    /** AJAX: retorna planos de um produto */
    public function planosPorProduto($data)
    {
        header("Content-Type: application/json");
        $produtoId = (int) ($_POST["produto_id"] ?? $data["produto_id"] ?? 0);
        if (!$produtoId) { echo json_encode([]); return; }

        $stmt = Connect::getInstance()->prepare(
            "SELECT id, nome, preco FROM planos WHERE produto = :p ORDER BY nome ASC"
        );
        $stmt->execute([':p' => $produtoId]);
        echo json_encode($stmt->fetchAll(\PDO::FETCH_OBJ));
    }

    /** AJAX: retorna checkouts de um plano */
    public function checkoutsPorPlano($data)
    {
        header("Content-Type: application/json");
        $planoId = (int) ($_POST["plano_id"] ?? $data["plano_id"] ?? 0);
        if (!$planoId) { echo json_encode([]); return; }

        $stmt = Connect::getInstance()->prepare(
            "SELECT id, codigo, descricao FROM checkout WHERE plano = :p ORDER BY id DESC"
        );
        $stmt->execute([':p' => $planoId]);
        echo json_encode($stmt->fetchAll(\PDO::FETCH_OBJ));
    }

    /** Salva nova oferta */
    public function cadastro($data)
    {
        $files  = new File();
        $oferta = new Oferta();

        $oferta->usuario     = $_SESSION["id"];
        $oferta->curso       = $data["curso_id"];
        $oferta->checkout_id = (int) $data["checkout_id"];
        $oferta->nome        = $data["nome"];
        $oferta->descricao   = $data["descricao"] ?? null;
        $oferta->status      = 1;
        $oferta->valor       = ($data["valor"] !== "" && $data["valor"] !== null)
            ? (float) str_replace(",", ".", $data["valor"])
            : null;
        $oferta->aula_id     = !empty($data["aula_id"]) ? (int) $data["aula_id"] : null;
        $oferta->exibir_em   = $this->parseTempo($data["exibir_em"] ?? "");

        if (!empty($_FILES["imagem"]["name"])) {
            $upload = $files->upload($_FILES["imagem"], $this->pastaCursos);
            if ($upload) $oferta->imagem = $upload;
        }

        if ($oferta->save()) {
            header("Location: " . url("ofertas/" . $data["curso_id"]));
        } else {
            header("Location: " . url("ofertas/" . $data["curso_id"] . "?msg=Erro ao salvar oferta"));
        }
    }

    /** Edita oferta existente */
    public function editar($data)
    {
        $files  = new File();
        $oferta = (new Oferta())->findById($data["oferta_id"]);

        if (!$oferta || $oferta->usuario != $_SESSION["id"]) {
            header("Location: " . url("ofertas/" . $data["curso_id"]));
            exit;
        }

        $oferta->checkout_id = (int) $data["checkout_id"];
        $oferta->nome        = $data["nome"];
        $oferta->descricao   = $data["descricao"] ?? null;
        $oferta->valor       = ($data["valor"] !== "" && $data["valor"] !== null)
            ? (float) str_replace(",", ".", $data["valor"])
            : null;
        $oferta->aula_id     = !empty($data["aula_id"]) ? (int) $data["aula_id"] : null;
        $oferta->exibir_em   = $this->parseTempo($data["exibir_em"] ?? "");

        if (!empty($_FILES["imagem"]["name"])) {
            $upload = $files->upload($_FILES["imagem"], $this->pastaCursos);
            if ($upload) $oferta->imagem = $upload;
        } else {
            $oferta->imagem = $data["imagem_atual"] ?? $oferta->imagem;
        }

        if ($oferta->save()) {
            header("Location: " . url("ofertas/" . $data["curso_id"]));
        } else {
            header("Location: " . url("ofertas/" . $data["curso_id"] . "?msg=Erro ao editar oferta"));
        }
    }

    /** Ativa / desativa oferta */
    public function toggleStatus($data)
    {
        $oferta = (new Oferta())->findById($data["id"]);

        if (!$oferta || $oferta->usuario != $_SESSION["id"]) { echo "0"; return; }

        $oferta->status = $oferta->status == 1 ? 0 : 1;
        echo $oferta->save() ? $oferta->status : "0";
    }

    /** Converte MM:SS ou segundos brutos para int de segundos (null se vazio) */
    private function parseTempo(string $val): ?int
    {
        $val = trim($val);
        if ($val === "" || $val === null) return null;
        if (strpos($val, ":") !== false) {
            [$m, $s] = explode(":", $val, 2);
            return (int)$m * 60 + (int)$s;
        }
        return (int)$val > 0 ? (int)$val : null;
    }

    /** Exclui oferta */
    public function excluir($data)
    {
        $oferta = (new Oferta())->findById($data["id"]);

        if (!$oferta || $oferta->usuario != $_SESSION["id"]) { echo "0"; return; }

        echo $oferta->destroy() ? "1" : "0";
    }
}

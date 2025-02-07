<?php

//CLASSE QUE CONTROLA AS ROTAS DOS USUÁRIOS

namespace Source\App\Hotjar;

use League\Plates\Engine;
use Source\Models\Hotjar\Hotjar;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Permissao\Section;

class AppHotjar
{
    private $view;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->section = new Section();
    }

    public function hotjar($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 20;

        $hotjarCount = (new Hotjar())->find()->count();
        $paginas = ceil($hotjarCount / $offset);

        $produtos = (new Produto())->find("usuario = " . $this->section->getId())->fetch(true);
        $hotjar = (new Hotjar())->find("usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        echo $this->view->render("hotjar/hotjar", [
            "title" => "Integração com Hotjar",
            "hotjar" => $hotjar,
            "produtos" => $produtos,
            "total" => $paginas,
            "paginador" => $paginador,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function cadastrarHotjar($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $hotjar = new Hotjar();
        $hotjar->usuario = $this->section->getId();

        $hotjar->produto = @$data["produto_hotjar"];
        $hotjar->tracking_id = $data["tracking_id"];

        if ($hotjar->save()) {
            echo "Cadastrado com sucesso";
        } else {
            echo $hotjar->fail()->getMessage();
        }
    }

    public function editarHotjar($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $hotjar = (new Hotjar())->findById($data["id"]);

        $hotjar->produto = @$data["produto_hotjar"];
        $hotjar->tracking_id = $data["tracking_id"];

        if ($hotjar->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $hotjar->fail()->getMessage();
        }
    }

    public function excluirHotjar($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $hotjar = (new Hotjar())->findById($data["id"]);
        if ($hotjar->destroy()) {
            echo "Excluído com sucesso";
        } else {
            echo $hotjar->fail()->getMessage();
        }
    }
}

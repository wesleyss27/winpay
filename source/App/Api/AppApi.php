<?php

//CLASSE QUE CONTROLA AS ROTAS DOS AFILIADOS

namespace Source\App\Api;

session_start();

use League\Plates\Engine;

use Source\Models\Api\Api;
use Source\App\Sessao\AppSessao;

use Source\Helpers\Paginador;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;

class AppApi
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
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $this->section = new Section();
    }

    public function api($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("api");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'api'")->fetch();

        $usuario = $this->section->getId();
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 10;

        $apiCount = (new Api())->find("usuario = $usuario")->count();
        $paginas = ceil($apiCount / $offset);
        $api = (new Api())->find("usuario = $usuario")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        // var_dump($api);
        echo $this->view->render("api/api", [
            "title" => "Api",
            "section" => @$section,
            "permissao" => @$permissao,
            "api" => $api,
            "count" => $apiCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function cadastrarApi($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $resultado_bytes = random_bytes(6);
        $codigo = strtoupper(bin2hex($resultado_bytes));

        $api = new Api();
        $api->descricao = @$data["descricao"] ? $data["descricao"] : "api";
        $api->usuario = $this->section->getId();
        $api->api_key = $codigo;

        if ($api->save()) {
            echo "Cadastrado Com Sucesso";
        } else {
            echo $api->fail()->getMessage();
        }
    }

    public function excluirApi($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $api = (new Api())->findById($data["id"]);
        if ($api->destroy()) {
            echo "Excluído com sucesso";
        } else {
            echo $api->fail()->getMessage();
        }
    }
}

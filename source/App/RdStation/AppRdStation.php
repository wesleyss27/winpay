<?php

namespace Source\App\RdStation;

use League\Plates\Engine;
use Source\Models\RdStation\RdStation;
use Source\App\Sessao\AppSessao;
use Source\Models\Permissao\Section;

session_start();

class AppRdStation
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

    public function cadastrar($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $rdstation = (new RdStation())->find("usuario = " . $this->section->getId())->fetch();

        if (!@$rdstation) {
            $rdstation = new RdStation();

            $rdstation->usuario            = $this->section->getId();
        }

        $rdstation->client_id              = $data["rdstation_id"];
        $rdstation->client_secret              = $data["rdstation_secret"];
        $rdstation->code              = $data["rdstation_code"];

        if ($rdstation->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $rdstation->fail()->getMessage();
        }
    }

    public function excluirRdStation($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $rdstation = (new RdStation())->findById($data["id"]);
        if ($rdstation->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo "fail" . $rdstation->fail()->getMessage();
        }
    }
}

<?php

namespace Source\App\ActiveCampaign;

use League\Plates\Engine;
use Source\Models\ActiveCampaign\ActiveCampaign;
use Source\App\Sessao\AppSessao;
use Source\Models\Permissao\Section;

session_start();

class AppActiveCampaign
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
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $activecampaign = (new ActiveCampaign())->find("usuario = " . $this->section->getId())->fetch();

        if (!@$activecampaign) {
            $activecampaign = new ActiveCampaign();

            $activecampaign->usuario            = $this->section->getId();
        }


        $activecampaign->token              = $data["activecampaign_token"];
        $activecampaign->link              = $data["activecampaign_link"];

        if ($activecampaign->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $activecampaign->fail()->getMessage();
        }
    }

    public function excluirActiveCampaign($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $activecampaign = (new ActiveCampaign())->findById($data["id"]);
        if ($activecampaign->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo "fail" . $activecampaign->fail()->getMessage();
        }
    }
}

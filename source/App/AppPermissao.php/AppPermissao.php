<?php

namespace Source\App\Permissao;

use League\Plates\Engine;
use Source\Models\Permissao\Pagina;
use Source\Models\Permissao\Permissao;
use Source\Models\Permissao\PermissaoModulo;
use Source\App\Sessao\AppSessao;

session_start();

class AppActiveCampaign
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    public function paginas($data)
    {
        $paginas = (new Pagina())->find()->fetch(true);

        echo $this->view->render("permissao/paginas", [
            "title" => "Páginas",
            "paginas" => $paginas
        ]);
    }

    public function excluirActiveCampaign($data)
    {
        $activecampaign = (new ActiveCampaign())->findById($data["id"]);
        if ($activecampaign->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo "fail" . $activecampaign->fail()->getMessage();
        }
    }
}

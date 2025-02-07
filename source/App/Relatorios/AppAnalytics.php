<?php

namespace Source\App\Relatorios;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Produtos\Produto;
use Source\Models\RedrocketAnalytics\RedrocketAnalytics;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Saldo;
use Source\Models\Comissao\Banco;
use Source\Models\Vendas\Venda;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Enderecos\Endereco;
use Source\Models\Produtos\Plano;

session_start();

class AppAnalytics
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

    public function analytics($data)
    {
        $analytics = (new RedrocketAnalytics())->find("url IS NOT NULL GROUP BY url", null, "*, count(url) as cont")->fetch(true);
        $analyticsA = (new RedrocketAnalytics())->find("url IS NOT NULL GROUP BY url_anterior", null, "*, count(url_anterior) as cont")->fetch(true);
        $an = (new RedrocketAnalytics())->find("url_anterior IS NULL")->count();

        echo $this->view->render("relatorios/analytics", [
            "title" => "Analytics",
            "analytics" => $analytics,
            "analyticsA" => $analyticsA,
            "an" => $an,
        ]);
    }
}

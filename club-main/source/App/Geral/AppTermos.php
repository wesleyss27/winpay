<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\Geral;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Saldo;
use Source\Models\Vendas\Venda;

session_start();

class AppTermos
{
    private $view;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    public function termosUso($data)
    {
        echo $this->view->render("termos/termos-uso", [
            "title" => "Termos de Uso"
        ]);
    }

    public function termosCompra($data)
    {
        echo $this->view->render("termos/termos-compra", [
            "title" => "Termos de Compra"
        ]);
    }

    public function politicaPrivacidade($data)
    {
        echo $this->view->render("termos/politica-privacidade", [
            "title" => "Politicas de Privacidade"
        ]);
    }

    public function ajudaUsuarios($data)
    {
        echo $this->view->render("termos/ajuda-usuarios", [
            "title" => "Ajuda Usuários"
        ]);
    }

    public function politicaDisputa($data)
    {
        echo $this->view->render("termos/politica-disputa", [
            "title" => "POLÍTICA DE RESOLUÇÃO DE DISPUTA"
        ]);
    }
}
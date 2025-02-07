<?php

namespace Source\App\Checkout1;

use League\Plates\Engine;

class Termos
{
    private $view;
    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../pagamentos", "php");
        $this->view->addData(["router" => $router]);
    }
    
    public function termos($data):void
    {
        echo $this->view->render("termos", [
            "title" => "Termos de Uso | " . SITE
        ]);
    }

    public function privacidade($data):void
    {
        echo $this->view->render("privacidade", [
            "title" => "Política de Privacidade | " . SITE
        ]);
    }
}
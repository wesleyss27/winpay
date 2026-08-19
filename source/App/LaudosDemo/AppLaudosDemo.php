<?php

namespace Source\App\LaudosDemo;

use Source\App\Sessao\AppSessao;
use League\Plates\Engine;

class AppLaudosDemo
{
    private Engine $view;

    public function __construct($router)
    {
        @session_start();
        $tema = @$_SESSION["tema"] ?: "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/{$tema}", "php");
        $this->view->addData(["router" => $router]);
        (new AppSessao())->verificaSeLogado();

        $emailsPermitidos = ["contatofabiogestor@gmail.com"];

        $tipo = (int)($_SESSION["tipo"] ?? 0);
        $email = strtolower(trim($_SESSION["email"] ?? ""));
        if (!in_array($tipo, [1, 2]) && !in_array($email, $emailsPermitidos)) {
            header("Location: " . URL_BASE);
            exit();
        }
    }

    public function form(array $data): void
    {
        echo $this->view->render("laudos-demo/form");
    }

}

<?php

namespace Source\App\Login;

session_start();

use League\Plates\Engine;
use CoffeeCode\DataLayer\Connect;

class AppLogin
{
    private $view;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema1", "php");
        $this->view->addData(["router" => $router]);
    }

    public function index($data)
    {
        if (!empty($_SESSION["winturb_id"])) {
            header("Location: " . url("dashboard"));
            exit;
        }
        echo $this->view->render("login/login", ["title" => "Login — " . WINTURB_NAME, "erro" => $_SESSION["winturb_erro"] ?? null]);
        unset($_SESSION["winturb_erro"]);
    }

    public function entrar($data)
    {
        $login = trim($data["login"] ?? "");
        $senha = trim($data["senha"] ?? "");

        if (!$login || !$senha) {
            $_SESSION["winturb_erro"] = "Preencha login e senha.";
            header("Location: " . url("login"));
            exit;
        }

        try {
            $pdo  = Connect::getInstance();
            $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuario WHERE (email = :l OR login = :l) LIMIT 1");
            $stmt->execute([':l' => $login]);
            $user = $stmt->fetch(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            $_SESSION["winturb_erro"] = "Erro ao conectar. Tente novamente.";
            header("Location: " . url("login"));
            exit;
        }

        if (!$user || !password_verify($senha, $user->senha)) {
            $_SESSION["winturb_erro"] = "Login ou senha inválidos.";
            header("Location: " . url("login"));
            exit;
        }

        $_SESSION["winturb_id"]   = $user->id;
        $_SESSION["winturb_nome"] = $user->nome;
        header("Location: " . url("dashboard"));
        exit;
    }

    public function sair($data)
    {
        unset($_SESSION["winturb_id"], $_SESSION["winturb_nome"]);
        header("Location: " . url("login"));
        exit;
    }
}

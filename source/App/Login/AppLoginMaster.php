<?php

//CLASSE QUE CONTROLA AS ROTAS DOS CLIENTES

namespace Source\App\Login;

use League\Plates\Engine;
use Source\Models\Clientes\Cliente;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Sessao\Sessao;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Emails;
use Source\Models\Configuracoes\Taxa;

class AppLoginMaster
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    public function login($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();
        
        echo $this->view->render("login-master/login-master", [
            "title" => "Login Master",
            "msg" => "",
            "classCss" => ""
        ]);
    }

    public function logar($data)
    {
        session_start();
        
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $usuario = (new Usuario());

        $mail = $data["email"];

        $acesso = $usuario->find("(email = :email OR login = :email)", "email={$mail}")->fetch();

        if ($acesso) {

            if($acesso->ativo != 1){
                echo $this->view->render("login-master/login-master", [
                    "title" => "Login Erro",
                    "msg" => "Sua conta foi desativada <br> Entre em contato",
                    "classCss" => "bg-danger"
                ]);
                exit;
            }
            if($acesso->status != 1){
                echo $this->view->render("login-master/login-master", [
                    "title" => "Login Erro",
                    "msg" => "Sua conta ainda não foi ativada <br> Verifique seu email",
                    "classCss" => "bg-danger"
                ]);
                exit;
            }
            
            $_SESSION["tipo"] = $acesso->tipo_usuario;
            $_SESSION["nome"] = $acesso->nome;
            $_SESSION["nome_abreviado"] = $acesso->nome_abreviado;
            $_SESSION["email"] = $acesso->email;
            $_SESSION["id"] = $acesso->id;
            $_SESSION["idU"] = $acesso->usuario_dados;
            $_SESSION["tema"] = $acesso->tema;
            $_SESSION["foto"] = $acesso->foto;
            $_SESSION["verificado"] = $acesso->verificado;
            $_SESSION["testador"] = $acesso->testador;

            $sessao = new Sessao();
            $sessao->usuario = $acesso->id;
            $sessao->ip = $_SERVER['REMOTE_ADDR'];
            
            $sessao->expiration_date = date("Y-m-d H:i:s", strtotime("+30 minutes"));

            $sessao->sessao = password_hash($acesso->email . $sessao->expiration_date, PASSWORD_DEFAULT);

            if($sessao->save()){
                $_SESSION["sessao"] = $sessao->sessao;
                $_SESSION["sessao_id"] = $sessao->id;
                $_SESSION["expiration_date"] = $sessao->expiration_date;
                
                if($_SESSION["tipo"] == 6)
                    header("Location: " . url("vendas"));
                else
                    header("Location: ".URL_BASE . @$data["redirect"]);
            }
            else {
                echo $sessao->fail()->getMessage();
            }
            
        }
        else {
            echo $this->view->render("login-master/login-master", [
                "title" => "Login Erro",
                "msg" => "Email ou senha incorretos!",
                "classCss" => "bg-danger"
            ]);
        }
        
    }
}

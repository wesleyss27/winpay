<?php

//CLASSE QUE CONTROLA AS ROTAS DOS CLIENTES

namespace Source\App\Login;

use League\Plates\Engine;
use Source\Models\Clientes\Cliente;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Sessao\Sessao;
use Source\Helpers\Emails;
use Source\Models\Configuracoes\Taxa;

class AppLogin
{
    private $view;
    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../", "php");
        $this->view->addData(["router" => $router]);
    }

    public function login($data)
    {
        session_start();
        $sessao = (new Sessao())->find("sessao = :sessao", "sessao=".@$_SESSION["sessao"])->fetch();
        if(@$_SESSION["id"] && $sessao){
            header("Location: " . url());
        }
        echo $this->view->render("login3", [
            "title" => "Login RedRocket",
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
        $pass = $data["senha"];

        $acesso = $usuario->find("(email = :email OR login = :email)", "email={$mail}")->fetch();

        if ($acesso && password_verify($pass, $acesso->senha)) {

            if($acesso->ativo != 1){
                echo $this->view->render("login3", [
                    "title" => "Login Erro",
                    "msg" => "Sua conta foi desativada <br> Entre em contato",
                    "classCss" => "bg-danger"
                ]);
                exit;
            }
            if($acesso->status != 1){
                echo $this->view->render("login3", [
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

            $sessao = new Sessao();
            $sessao->usuario = $acesso->id;
            $sessao->ip = $_SERVER['REMOTE_ADDR'];
            
            $sessao->expiration_date = date("Y-m-d H:i:s", strtotime("+6 hours"));

            $sessao->sessao = password_hash($acesso->email . $sessao->expiration_date, PASSWORD_DEFAULT);

            if($sessao->save()){
                $_SESSION["sessao"] = $sessao->sessao;
                $_SESSION["sessao_id"] = $sessao->id;
                $_SESSION["expiration_date"] = $sessao->expiration_date;
                
                if($_SESSION["tipo"] == 6){
                    header("Location: " . url("minhas-compras"));
                } else {
                    header("Location: " . url() . @$data["redirect"]);
                }
            }
            else {
                echo $sessao->fail()->getMessage();
            }
            
        }
        else {
            echo $this->view->render("login3", [
                "title" => "Login Erro",
                "msg" => "Email ou senha incorretos!",
                "classCss" => "bg-danger"
            ]);
        }
        
    }

    public function cadastro($data)
    {
        echo $this->view->render("cadastro", [
            "title" => "Cadastro RedRocket",
            "msg" => "",
            "classCss" => ""
        ]);
    }

    public function cadastroUsuarios($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $usuario = (new Usuario());
        $taxa = (new Taxa())->findById(1);
        $usuario->nome = $data["nome"];

        $nome = explode(" ", $data["nome"], 2);
        $usuario->nome_abreviado = $nome[0];
        $usuario->login = $data["login"];
        $usuario->email = $data["email"];
        $usuario->senha = password_hash($data['senha'],  PASSWORD_DEFAULT, ["cost" => 10]);
        $usuario->status = 0;
        $usuario->ativo = 1;
        $usuario->tipo_usuario = $data["tipo_usuario"];
        $usuario->taxa_saque = $taxa->saque;
        $usuario->taxa_redrocket = $taxa->taxa_redrocket;
        $usuario->tempo_saque = $taxa->dias_iniciante;
        $usuario->grupo = 1;
        $usuario->tema = "tema1";
        $usuario->chave_unica = md5(time().$usuario->nome);
        $usuario->cliente_st4rt = 0;

        $usuarioDados = new UsuarioDados();
        $usuarioDados->nome = $usuario->nome;
        $usuarioDados->email = $usuario->email;
        $usuarioDados->telefone = $data["telefone"];
        $usuarioDados->tipo_doc = $data["tipo_doc"];
        $usuarioDados->cpf_cnpj = $data["cpf"];
        
        $email = $usuario->find("email = :email", "email={$data['email']}")->fetch(true);
        $login = $usuario->find("login = :login", "login={$data['login']}")->fetch(true);
        $cpf = $usuarioDados->find("cpf_cnpj = :cpf", "cpf={$data['cpf']}")->fetch(true);

        //var_dump($data);
        if($email) {
            $msg = "Email já cadastrado";
            echo "Email já cadastrado \n";
            $classCss = "bg-danger";
        }
        else if($login) {
            $msg = "Login já cadastrado";
            echo "Login já cadastrado \n";
            $classCss = "bg-danger";
        }
        else if($cpf) {
            echo $data["cpf"];
            $msg = "CPF/CNPJ já cadastrado";
            echo "CPF/CNPJ já cadastrado \n";
            $classCss = "bg-danger";
        }
        else {
            $usuario->save();
            $usuarioDados->usuario = $usuario->id;
            $usuarioDados->save();
            $msg = "Cadastrado Com Sucesso!";
            echo "Cadastrado Com Sucesso!";
            $classCss = "bg-success";

            if(@$_COOKIE["userPr"]){
                parse_str(@$_COOKIE["userPr"], $arr);
                //echo $arr["id"];
                $premium = new UsuarioPremium();
                $premium->usuario = $arr["id"];
                $premium->indicado = $usuario->id;
                $premium->expiracao = date("Y-m-d", strtotime("+1 year"));
                $premium->save();
            }

            $data["id"] = $usuario->id;

            $cadastroUsers = (new Emails())->cadastroUsuario($data);
            $enviaEmail = (new Emails())->enviaPhp($cadastroUsers);

            $data["email"] = "suporte@winpay.digital";
            $data["assunto"] = "Novo usuário cadastrado";
            $data["mensagem"] = "Novo usuário cadastrado <br>" . 
                                "Nome: $usuario->nome <br>";
            $enviaEmail = (new Emails())->enviaPhp($data);
        }

        $data["msg"] = $msg;
        $data["classCss"] = $classCss;

        //echo $msg;

        //echo json_encode($data);
    }

    public function ativarUsuario($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $uID = $data["id"];

        $acesso = (new Usuario())->findById($uID);

        if($acesso) {
            $acesso->status = 1;
            $acesso->ativo = 1;
            $acesso->save();

            echo $this->view->render("login", [
                "title" => "Login RedRocket",
                "msg" => "Conta Ativada",
                "classCss" => "bg-success"
            ]);
        }
        else {
            echo $this->view->render("login", [
                "title" => "Login Erro",
                "msg" => "Login não encontrado",
                "classCss" => "bg-danger"
            ]);
        }
    }
}

/*
$src = $data["src"];
        @$srcPerm = $data["perm"];
        $srcModel = array(
            "tabela" => [
                "cliente" => "Cliente",
                "usuario" => "Usuario"
            ],
            "permissao" => [
                "administrador" => "administrador",
                "atendente" => "atendente",
                "cobrança" => "cobrança",
                "cliente" => "cliente"
            ]
        );

        //var_dump($srcModel);
        $tabela = @$srcModel["tabela"][$src];
        //echo @$srcModel["permissao"][$srcPerm];*/

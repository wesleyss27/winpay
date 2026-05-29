<?php

//CLASSE QUE CONTROLA AS ROTAS DOS USUÁRIOS

namespace Source\App\Usuarios;

@session_start();

use League\Plates\Engine;
use Source\App\Asaas\AppAsaas;
use Source\App\Asaas\AsaasClientes;
use Source\App\Asaas\AsaasContas;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\RecuperarSenha;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Aws;
use Source\Helpers\Emails;
use Source\App\Login\AppLogin;
use Source\Models\Enderecos\Endereco;

class AppUsuarios
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

    public function usuarios($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 15;

        $sessionTipo = (int)$_SESSION["tipo"];
        $sessionId   = (int)$_SESSION["id"];

        if ($sessionTipo === 4) {
            // Produtor: vê apenas seus próprios atendentes
            $where = "tipo_usuario = 7 AND atendente_produtor = {$sessionId}";
            $produtores = [];
        } else {
            // Admin/Master: vê todos os funcionários
            $where = "tipo_usuario = 1 OR tipo_usuario = 2 OR tipo_usuario = 3 OR tipo_usuario = 7";
            $produtores = (new Usuario())->find("tipo_usuario = 4 AND ativo = 1")->order("nome ASC")->fetch(true) ?? [];
        }

        $usuariosCount = (new Usuario())->find($where)->count();
        $paginas = ceil($usuariosCount / $offset);
        $usuarios = (new Usuario())->find($where)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id ASC")->fetch(true);

        echo $this->view->render("usuarios/usuarios", [
            "title"      => $sessionTipo === 4 ? "Meus Atendentes" : "Gerenciar Usuários",
            "usuarios"   => $usuarios,
            "produtores" => $produtores,
            "is_produtor" => $sessionTipo === 4,
            "paginador"  => $paginador,
            "total"      => $paginas,
            "pagina"     => @$data["page"] ? $data["page"] : 1,
            "msg"        => @$data["msg"],
            "classCss"   => @$data["classCss"]
        ]);
    }

    public function cadastroFuncionarios($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $sessionTipo = (int)$_SESSION["tipo"];
        $sessionId   = (int)$_SESSION["id"];

        // Produtor só pode criar atendentes (tipo=7) vinculados a si mesmo
        if ($sessionTipo === 4) {
            $data["tipo_usuario"]       = 7;
            $data["atendente_produtor"] = $sessionId;
        }

        if ($data["email"] == "" || $data["senha"] == "" || @$data["tipo_usuario"] == "") {
            echo "Preecha todos os dados necessários";
            exit;
        }

        $usuario = (new Usuario());
        $usuario->nome = @$data["nome"];

        $nome = explode(" ", @$data["nome"], 2);
        $usuario->nome_abreviado = @$nome[0];
        $usuario->login = @$data["login"];
        $usuario->email = $data["email"];
        $usuario->senha = password_hash($data['senha'],  PASSWORD_DEFAULT, ["cost" => 10]);
        $usuario->status = 1;
        $usuario->ativo = 1;
        $usuario->tipo_usuario = $data["tipo_usuario"];

        if ((int)$data["tipo_usuario"] === 7 && !empty($data["atendente_produtor"])) {
            $usuario->atendente_produtor = (int)$data["atendente_produtor"];
        }

        $usuarioDados = new UsuarioDados();
        $usuarioDados->nome = $usuario->nome;
        $usuarioDados->email = $usuario->email;

        $email = $usuario->find("email = :email", "email={$data['email']}")->fetch(true);
        $login = $usuario->find("login = :login", "login={$data['login']}")->fetch(true);

        //var_dump($data);
        if ($email) {
            echo "Email já cadastrado";
        } else if ($login) {
            echo "Login já cadastrado";
        } else {
            $usuario->save();
            $usuarioDados->usuario = $usuario->id;
            $usuarioDados->save();
            echo "Cadastrado Com Sucesso!";
        }
    }

    public function editar($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $sessionTipo = (int)$_SESSION["tipo"];
        $sessionId   = (int)$_SESSION["id"];

        $usuario = (new Usuario())->findById($data["id"]);

        // Produtor só pode editar atendentes que lhe pertencem
        if ($sessionTipo === 4) {
            if ((int)$usuario->atendente_produtor !== $sessionId) {
                echo "Sem permissão para editar este usuário.";
                exit;
            }
            // Mantém tipo=7 e vínculo com o produtor, independente do que veio no form
            $data["tipo_usuario"]       = 7;
            $data["atendente_produtor"] = $sessionId;
        }

        $usuario->nome = @$data["nome"];
        $nome = explode(" ", @$data["nome"], 2);
        $usuario->nome_abreviado = @$nome[0];
        $usuario->login = @$data["login"];
        $usuario->email = $data["email"];
        $usuario->status = 1;
        $usuario->ativo = 1;
        $usuario->tipo_usuario = $data["tipo_usuario"];

        if ((int)$data["tipo_usuario"] === 7) {
            $usuario->atendente_produtor = !empty($data["atendente_produtor"]) ? (int)$data["atendente_produtor"] : null;
        }

        $user = (new UsuarioDados())->find("usuario = " . $data["id"])->fetch();
        //var_dump($user);
        if ($user) {
            $usuario->usuario_dados         = $user->id;
            $user->nome                     = $data["nome"];
            $user->email                    = $data["email"];
            $user->save();
        }

        if ($data["senha"] == "") {
            $usuario->senha = $data['senha_atual'];
        } else {
            $usuario->senha = password_hash($data['senha'],  PASSWORD_DEFAULT, ["cost" => 10]);
        }

        if ($usuario->save()) {
            echo "Atualizado Com Sucesso!";
        } else {
            echo "Erro Ao Atualizar!";
            echo $usuario->fail()->getMessage();
        }
    }

    public function aprovar($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if ($data["idAprovar"]) {
            $usuario = (new Usuario())->findById($data["idAprovar"]);
            $usuario->verificado = @$data["verificado"];
            $usuario->observacao = @$data["observacao"];

            if ($usuario->save()) {
                echo "Atualizado Com Sucesso!";
                $data["email"]  =  $usuario->email;

                if ($usuario->verificado == 1) {
                    $data["assunto"]  =  "Perfil verificado com sucesso";

                    $data["mensagem"] = "Olá $usuario->nome, tudo bem? <br>Seu perfil acaba de ser verificado e aprovado!<br>";
                } elseif ($usuario->verificado == 2) {
                    $data["assunto"]  =  "Existe uma pendência com a verificação do seu perfil!";

                    $data["mensagem"] = "Olá $usuario->nome, tudo bem? <br>Existe uma pendencia com a verificação do seu perfil! 
                                <br><br>$usuario->observacao<br><br>";
                }

                $mail = new Emails();
                $email = $mail->enviaSendGrid($data);
            } else {
                echo "Erro Ao Atualizar!";
                echo $usuario->fail()->getMessage();
            }
        }
    }

    public function perfilUsuario($data)
    {
        if ($data["id"] != $_SESSION["id"]) {
            header("Location: " . url("usuarios/perfil/" . $_SESSION["id"]));
        }
        $usuario = (new Usuario())->findById($data["id"]);
        $dados = (new UsuarioDados())->find("usuario = " . $data["id"])->fetch();
        $asaasClientes = (new AppAsaas(null))->verificarCliente($_SESSION["id"]);

        echo $this->view->render("usuarios/minha-conta", [
            "title" => "Perfil de " . $usuario->nome,
            "usuario" => $usuario,
            "dados" => $dados,
            "asaasClientes" => $asaasClientes,
        ]);
    }

    public function perfilUsuarioD($data)
    {
        if ($data["id"] != $_SESSION["id"]) {
            header("Location: " . url("usuarios/perfil/" . $_SESSION["id"]));
        }
        $usuario = (new Usuario())->findById($data["id"]);
        $dados = (new UsuarioDados())->find("usuario = " . $data["id"] . " OR id = $usuario->usuario_dados")->fetch();
        $asaasClientes = (new AppAsaas(null))->verificarCliente($_SESSION["id"]);

        echo $this->view->render("usuarios/perfil", [
            "title" => "Perfil de " . $usuario->nome,
            "usuario" => $usuario,
            "dados" => $dados,
            "asaasClientes" => $asaasClientes,
        ]);
    }

    public function editarUsuarioPerfil($data)
    {
        $usuario = (new UsuarioDados())->find("usuario = " . $data["id"])->fetch();
        $usuario->nome                  = $data["nome"];
        $usuario->email                 = $data["email"];
        $usuario->tipo_doc              = @$data["tipo_doc"];
        $usuario->cpf_cnpj              = $data["cpf"];
        $usuario->razao_social          = $data["razao_social"];
        $usuario->nome_fantasia         = $data["nome_fantasia"];
        $usuario->tipo_empresa          = @$data["tipo_empresa"];
        $usuario->telefone              = $data["telefone"];
        $usuario->cep                   = $data["cep"];
        $usuario->rua                   = $data["rua"];
        $usuario->numero                = $data["numero"];
        $usuario->complemento           = @$data["complemento"];
        $usuario->bairro                = $data["bairro"];
        $usuario->cidade                = $data["cidade"];
        $usuario->estado                = $data["estado"];
        $usuario->pais                  = $data["pais"];

        $usuario->cep_entrega           = @$data["cep_entrega"];
        $usuario->rua_entrega           = @$data["rua_entrega"];
        $usuario->numero_entrega        = @$data["numero_entrega"];
        $usuario->complemento_entrega   = @$data["complemento_entrega"];
        $usuario->bairro_entrega        = @$data["bairro_entrega"];
        $usuario->cidade_entrega        = @$data["cidade_entrega"];
        $usuario->estado_entrega        = @$data["estado_entrega"];
        $usuario->pais_entrega          = @$data["pais_entrega"];

        $user = (new Usuario())->findById($data["id"]);
        $user->usuario_dados            = $usuario->id;
        $user->nome                     = $data["nome"];
        $user->nome_abreviado           = $data["nome_abreviado"];
        $user->email                    = $data["email"];
        $user->login                    = $data["login"];

        if ($data["senha"] == "") {
            $user->senha = $data["senha_atual"];
        } else {
            $user->senha = password_hash($data['senha'],  PASSWORD_DEFAULT, ["cost" => 10]);
        }

        // var_dump($data);
        // $appAsas = new AppAsaas(null);
        // $asaasContas = new AsaasContas();
        // $asaasClientes = new AsaasClientes();

        // $verificarCliente = $appAsas->verificarCliente($user->id);
        // if ($verificarCliente) {
        //     #
        // } else {

        //     $listarConta = $asaasContas->listarContas(@$data["email"], @$data["cpf"]);

        //     if (!@$listarConta->id) {
        //         $novaConta = $asaasContas->novaConta($data);
        //         $novoCliente = $asaasClientes->novoCliente($data);
        //         // var_dump($novaConta);

        //         if (@$novaConta->id) {
        //             $data["id_user"]        = $user->id;
        //             $data["client_id"]      = @$novoCliente->id;
        //             $data["wallet_id"]      = $novaConta->walletId;
        //             $data["api_key"]        = $novaConta->apiKey;
        //             $data["account_id"]     = $novaConta->id;
        //             $data["agency"]         = $novaConta->accountNumber->agency;
        //             $data["account"]        = $novaConta->accountNumber->account;
        //             $data["account_digit"]  = $novaConta->accountNumber->accountDigit;
        //             $asaas = $appAsas->cadastrarUsuarioAsaas($data);

        //             // var_dump($asaas);
        //         }

        //         if(@$novaConta->errors || @$novoCliente->errors){
        //             $erro = 1;
        //             if(@$novaConta->errors){
        //                 foreach($novaConta->errors as $errors){
        //                     echo "$errors->description \n";
        //                 }
        //             }

        //             if(@$novoCliente->errors){
        //                 foreach($novoCliente->errors as $errors){
        //                     echo "$errors->description \n";
        //                 }
        //             }
        //         }
        //     }
        // }

        if ($usuario->save()) {
            echo "Atualizado Com Sucesso!";
            $user->save();
        } else {
            echo $usuario->fail()->getMessage();
        }
    }

    public function alterarTaxas($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $usuario = (new Usuario())->findById($_SESSION["id"]);

        switch (@$data["taxas"]) {
            case 3:
                $usuario->taxa_redrocket = 7.49;
                $usuario->taxa_assinatura = 7.49;
                $usuario->tempo_saque = 3;

                break;
            case 15:
                $usuario->taxa_redrocket = 5.49;
                $usuario->taxa_assinatura = 5.49;
                $usuario->tempo_saque = 15;
                break;
            case 30:
                $usuario->taxa_redrocket = 3.49;
                $usuario->taxa_assinatura = 3.49;
                $usuario->tempo_saque = 30;
                break;
            default:
                $usuario->taxa_redrocket = 3.49;
                $usuario->taxa_assinatura = 3.49;
                $usuario->tempo_saque = 30;
                break;
        };

        $usuario->tempo_boleto = 3;
        $usuario->tempo_pix = 1;

        if ($usuario->save()) {
            $msg = "Atualizado Com Sucesso!";
            $classCss = "bg-success";
        } else {
            $msg = "Erro ao trocar taxas";
            $classCss = "bg-danger";
        }

        header("Location: " . url("usuarios/perfil/" . $usuario->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function tipoUsuario($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $user = (new Usuario())->findById($_SESSION["id"]);
        $user->tipo_usuario = 5;

        if ($user->save()) {
            echo "Atualizado Com Sucesso!";
            $_SESSION["tipo"] = 5;
        } else {
            echo $user->fail()->getMessage();
        }
    }

    public function fotoPerfil($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $files = new File();
        $usuario = (new Usuario())->findById($data["idU"]);

        $size = $_FILES["foto"]["size"] / 1024 / 1024;
        if ($size > 2) {
            $msg = "Imagem excede o tamanho máximo!";
            $classCss = "bg-warning";
        } else {
            $usuario->foto = $files->upload($_FILES["foto"], PASTA . "perfil/");
            if (@$data["foto_atual"]) @unlink(PASTA . "perfil/" . @$data["foto_atual"]);

            if ($usuario->foto) {
                if ($usuario->save()) {
                    $msg = "Imagem alterada com sucesso!";
                    $classCss = "bg-success";
                    $_SESSION["foto"] = $usuario->foto;
                } else {
                    $msg = $usuario->fail()->getMessage();
                    $classCss = "bg-danger";
                }
            }
        }
        header("Location: " . url("usuarios/perfil/" . $usuario->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function documentoPerfil($data)
    {
        $files = new File();
        $usuario = (new Usuario())->findById($data["idU"]);

        $sizeDoc1 = $_FILES["documento1"]["size"] / 8192 / 8192;
        $sizeDoc2 = $_FILES["documento2"]["size"] / 8192 / 8192;

        if ($sizeDoc1 > 2 || $sizeDoc2 > 2) {
            $msg = "Imagem excede o tamanho máximo!";
            $classCss = "bg-warning";
        } else {
            $resultDoc1 = $files->upload($_FILES["documento1"], PASTA . "documentos/", 10);
            $resultDoc2 = $files->upload($_FILES["documento2"], PASTA . "documentos/", 10);

            if ($resultDoc1 && $resultDoc2) {
                $usuario->documento1 = $resultDoc1;
                $usuario->documento2 = $resultDoc2;

                if ($usuario->save()) {
                    $msg = "Documentos salvos com sucesso";
                    $classCss = "bg-success";

                    $data["nome"] = "Suporte";
                    $data["assunto"]  =  "Verificação de Perfil de usuário!";
                    $data["email"]  =  "suporte.oficial@redrocketpay.com";
                    $data["mensagem"] = "O usuário $usuario->nome acabou de enviar seus documentos para verificação do perfil 
                                <br><br>Nome: $usuario->nome
                                <br>Email: $usuario->email
                                <br><br>";

                    $mail = new Emails();

                    if (AMBIENTE == "produção")
                        $email = $mail->enviaSendGrid($data);
                } else {
                    $msg = "Erro ao salvar documentos<br>";
                    $msg .= $usuario->fail()->getMessage();
                    $classCss = "bg-danger";
                }
            } else {
                $msg = "Erro ao fazer upload";
                $classCss = "bg-danger";
            }
        }
        header("Location: " . url("usuarios/perfil/" . $usuario->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function verificaEmail($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $usuario = (new Usuario());
        $email = $usuario->find("email = :email", "email={$data['email']}")->fetch();

        if (!$email) {
            echo "Não existe usuário com esse email";
        } else {
            echo $email->id;
        }
    }

    public function esqueceuSenha($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        echo json_encode($data);
        $data["email"] = $data["email"];
        $usuario = (new Usuario())->find("email = :email", "email={$data['email']}")->fetch();
        echo json_encode($data);

        if (!$usuario) {
            echo "Não existe usuário com esse email";
        } else {

            function codigo($bytes)
            {
                $resultado_bytes = random_bytes($bytes);
                $codigo = strtoupper(bin2hex($resultado_bytes));
                return $codigo;
            }

            $recuperar = new RecuperarSenha();
            $recuperar->usuario = $usuario->id;
            $recuperar->codigo = codigo(2);
            $recuperar->codigo_recuperacao = codigo(4);
            $recuperar->save();

            $data["nome"] = $usuario->nome;
            $data["assunto"]  =  "Esqueceu a senha!";
            $data["mensagem"] = "Código de recuperação: $recuperar->codigo_recuperacao 
                                 <br><br>Clique no link abaixo para redefinir sua senha e depois digite o código de recuperação!
                                 <br><br><br><a href='" . url("usuarios/redefinir-senha/$usuario->id/$recuperar->codigo") . "' class='btn'>Redefinir senha</a><br><br><br>";

            $mail = new Emails();
            $email = $mail->enviaSendGrid($data);
            echo $email;
        }
    }

    public function redefinirSenha($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $recuperar = (new RecuperarSenha())->find("codigo = '" . $data["codigo"] . "'")->fetch();

        if (!$recuperar) {
            $msg = "Código inválido ou já verificado!";
            $classCss = "bg-danger";
            header("Location: " . url("login?msg=$msg&classCss=$classCss"));
        }
        $usuario = (new Usuario())->findById($data["id"]);

        echo $this->view->render("usuarios/redefinir", [
            "title" => "Redefinir Senha de " . $usuario->nome,
            "data" => $data

        ]);
    }

    public function atualizaSenha($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $recuperar = (new RecuperarSenha())->find("codigo = '" . $data["codigo"] . "' AND codigo_recuperacao = '" . $data["codigo_recuperacao"] . "'")->fetch();
        /*var_dump($data);
        var_dump($recuperar);*/
        if (!$recuperar) {
            $msg = "Código inválido ou já verificado!";
            $classCss = "bg-danger";
            header("Location: " . url("login?msg=$msg&classCss=$classCss"));
        } else {
            $usuario = (new Usuario())->findById($data["id"]);
            $usuario->senha = password_hash($data['senha'],  PASSWORD_DEFAULT, ["cost" => 10]);

            if ($usuario->save()) {
                $msg = "Senha alterada com sucesso!";
                $classCss = "bg-success";

                $recuper = (new RecuperarSenha())->findById($recuperar->id);
                $recuperar->destroy();

                $data["email"] = $usuario->email;

                echo $msg;
                (new AppLogin(null))->logar($data);
            } else {
                $msg = $usuario->fail()->getMessage();
                $classCss = "bg-danger";
            }
            echo $msg;
            //header("Location: " . url("login?msg=$msg&classCss=$classCss"));
        }
    }

    public function verificaLogin($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $usuario = (new Usuario());
        $login = $usuario->find("login = :login", "login={$data['login']}")->fetch(true);

        if (!$login) {
            echo "Não existe usuário com esse login";
        } else {
            foreach ($login as $login)
                echo $login->id;
        }
    }

    public function verificaCpf($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if ($data["cpf"] == "") exit;

        $usuario = (new UsuarioDados());
        $cpf = $usuario->find("cpf_cnpj = :cpf OR nome LIKE '%" . $data["cpf"] . "%'", "cpf={$data['cpf']}")->fetch(true);

        if (!$cpf) {
            echo "Não existe usuário com esse CPF/CNPJ";
        } else {
            foreach ($cpf as $cpf)
                echo (int) $cpf->id;
        }
    }

    public function buscaComprador($data)
    {
        $usuario = (new UsuarioDados())->find("cpf_cnpj = '" . $data["info"] . "' OR email = '" . $data["info"] . "'")->fetch();

        if ($usuario) {
            $dados["nome"] = $usuario->nome;
            $dados["email"] = $usuario->email;
            $dados["cpf"] = $usuario->cpf_cnpj;
            $dados["cep"] = $usuario->cep_entrega;
            $dados["telefone"] = $usuario->telefone;
            $dados["numero"] = $usuario->numero_entrega;

            echo json_encode($dados);
        } else {
            $dados["msg"] = "Nenhum usuario encontrado";
            echo json_encode($dados);
        }
    }

    public function cadastroUsuarioDados($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $usuario = (new UsuarioDados());

        $usuario->nome                  = $data["nome"];
        $usuario->email                 = $data["email"];
        $usuario->tipo_doc              = @$data["tipo_doc"];
        $usuario->cpf_cnpj              = $data["cpf"];
        $usuario->telefone              = $data["ddd"] . $data["telefone"];
        $usuario->cep                   = $data["cep"];
        $usuario->rua                   = $data["rua"];
        $usuario->numero                = $data["numero"];
        $usuario->complemento           = @$data["complemento"];
        $usuario->bairro                = $data["bairro"];
        $usuario->cidade                = $data["cidade"];
        $usuario->estado                = $data["estado"];
        $usuario->pais                  = @$data["pais"];
        $usuario->cep_entrega           = $data["cep"];
        $usuario->rua_entrega           = $data["rua"];
        $usuario->numero_entrega        = $data["numero"];
        $usuario->complemento_entrega   = @$data["complemento"];
        $usuario->bairro_entrega        = $data["bairro"];
        $usuario->cidade_entrega        = $data["cidade"];
        $usuario->estado_entrega        = $data["estado"];
        $usuario->pais_entrega          = @$data["pais"];
        $usuario->customer_id           = @$data["customer_id"];
        $usuario->data_nascimento       = @$data["nasc"];

        $user = new Usuario();
        $user->nome = $data["nome"];
        $nome = explode(" ", $data["nome"], 2);
        $user->nome_abreviado = $nome[0];
        $user->email = $data["email"];
        $user->senha = password_hash("123456",  PASSWORD_DEFAULT, ["cost" => 10]);
        $user->status = 1;
        $user->ativo = 1;
        $user->tipo_usuario = 6;
        $user->taxa_saque = 7.5;
        $user->tempo_saque = 30;
        $user->grupo = 1;

        if ($usuario->save()) {
            $resp["id"] = $usuario->id;
            $resp["msg"] = "Salvo com sucesso";
            $user->usuario_dados = $usuario->id;
            $user->save();

            $usuario1 = (new UsuarioDados())->findById($usuario->id);
            $usuario1->usuario = $user->id;
            $usuario1->save();

            $resp["id_comprador"] = $user->id;
            $data["id"] = $user->id;
            // $cadastroUsers = (new Emails())->cadastroUsuarioCompra($data);
            // $enviaEmail = (new Emails())->enviaSendGrid($cadastroUsers);
        } else {
            $resp["msg"] = $usuario->fail()->getMessage();
        }

        $arquivo = fopen(DIRETORIO . "logs/cadastro-usuario-compra.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "*******************************************************\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        $texto .= ' "protocolo: "' . @$data["protocolo"] . '",' . " \n";
        $texto .= ' "nome": "' . $data["nome"] . '",' . "\n";
        $texto .= ' "email": "' . $data["email"] . '",' . "\n";
        $texto .= ' "telefone": "' . $data["telefone"] . '",' . "\n";
        $texto .= ' "cpf": "' . $data["cpf"] . '",' . "\n ";
        $texto .= $resp["msg"] . "\n\n";
        $texto .= "******************************************************\n";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);

        return $resp;
    }

    public function editarUsuarioDados($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $usuario = (new UsuarioDados())->findById($data["id"]);

        $usuario->nome                  = $data["nome"];
        $usuario->email                 = $data["email"];
        $usuario->tipo_doc              = @$data["tipo_doc"];
        $usuario->cpf_cnpj              = $data["cpf"];
        $usuario->telefone              = $data["ddd"] . $data["telefone"];
        $usuario->cep_entrega           = $data["cep"];
        $usuario->rua_entrega           = $data["rua"];
        $usuario->numero_entrega        = $data["numero"];
        $usuario->complemento_entrega   = @$data["complemento"];
        $usuario->bairro_entrega        = $data["bairro"];
        $usuario->cidade_entrega        = $data["cidade"];
        $usuario->estado_entrega        = $data["estado"];
        $usuario->pais_entrega          = $data["pais"];
        $usuario->pais_entrega          = @$data["pais"];
        $usuario->customer_id           = @$data["customer_id"];
        $usuario->data_nascimento       = @$data["nasc"];

        if ($usuario->save()) {
            $msg  = $usuario->id;
        } else {
            $msg = $usuario->fail()->getMessage();
        }

        return $msg;
    }

    public function cadastroEndereco($data)
    {
        $endereco = new Endereco();

        $endereco->nome                  = $data["nome"];
        $endereco->email                 = $data["email"];
        $endereco->cpf_cnpj              = $data["cpf"];
        $endereco->telefone              = $data["ddd"] . $data["telefone"];
        $endereco->cep                   = @$data["cep"];
        $endereco->rua                   = @$data["rua"];
        $endereco->numero                = @$data["numero"];
        $endereco->complemento           = @$data["complemento"];
        $endereco->bairro                = @$data["bairro"];
        $endereco->cidade                = @$data["cidade"];
        $endereco->estado                = @$data["estado"];
        $endereco->pais                  = $data["pais"];
        $endereco->venda                 = @$data["venda"];
        $endereco->usuario               = @$data["usuario"] ? @$data["usuario"] : NULL;
        $endereco->customer_id           = @$data["customer_id"];

        if ($endereco->save()) {
            $msg = $endereco->id;
        } else {
            $msg = $endereco->fail()->getMessage();
        }

        if ($endereco->venda) {
        }
        return $msg;
    }
}

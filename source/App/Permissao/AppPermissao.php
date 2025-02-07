<?php

namespace Source\App\Permissao;

session_start();

use League\Plates\Engine;
use Source\Models\Permissao\Pagina;
use Source\Models\Permissao\Permissao;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Usuarios\Usuario;
use Source\App\Sessao\AppSessao;
use Source\Models\Permissao\Section;
use Source\Models\Sessao\Sessao;

class AppPermissao
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
        $menus = (new Pagina())->find("tipo = 'menu'")->fetch(true);

        echo $this->view->render("permissao/paginas", [
            "title" => "Páginas",
            "paginas" => $paginas,
            "menus" => $menus,
        ]);
    }

    public function salvarPagina($data)
    {
        // var_dump($data);
        // exit;
        if (@$data["id"])
            $pagina = (new Pagina())->findById($data["id"]);
        else
            $pagina = new Pagina();

        $pagina->pagina                     = @$data["pagina"];
        $pagina->modulo                     = @$data["modulo"];
        $pagina->tipo                       = @$data["tipo"];
        $pagina->menu                       = @$data["menu"] ? $data["menu"] : null;
        $pagina->admin                      = @$data["admin"] ? 1 : 0;
        $pagina->ver                        = @$data["ver"] ? 1 : 0;
        $pagina->adicionar                  = @$data["adicionar"] ? 1 : 0;
        $pagina->editar                     = @$data["editar"] ? 1 : 0;
        $pagina->excluir                    = @$data["excluir"] ? 1 : 0;
        $pagina->usuario_ultima_alteracao   = $_SESSION["id"];

        if ($pagina->save()) {
            echo "Página salva com sucesso!";
        } else {
            echo "Erro ao salvar";
        }
    }

    public function excluirPagina($data)
    {
        $pagina = (new Pagina())->findById($data["id"]);

        if ($pagina->destroy()) {
            echo "Página excluída com sucesso";
        } else {
            echo "Erro ao excluir";
        }
    }

    public function usuariosPermissao($data)
    {
        $usuariosPermissao = (new Permissao())->find()->fetch(true);

        echo $this->view->render("permissao/usuarios_permissao", [
            "title" => "Gerenciar Permissões de Usuários",
            "usuariosPermissao" => $usuariosPermissao,
        ]);
    }

    public function salvarUsuario($data)
    {
        if (@$data["id"])
            $permissao = (new Permissao())->findById($data["id"]);
        else
            $permissao = new Permissao();

        $permissao->usuario_origem             = $_SESSION["id"];
        $permissao->usuario_permitido          = @$data["usuario_permitido"];
        $permissao->ativo                      = 1;

        if ($permissao->save()) {
            header("Location: " . url("permissao/") . $permissao->id);
        } else {
            echo "Erro ao salvar";
        }
    }

    public function permissao($data)
    {
        $paginas = (new Pagina())->find()->fetch(true);
        $permissao = (new Permissao())->findById($data["id"]);
        $permissaoModulo = new PermissaoModulo;

        echo $this->view->render("permissao/permissoes", [
            "title" => "Permissões",
            "paginas" => $paginas,
            "permissao" => $permissao,
            "permissaoModulo" => $permissaoModulo,
        ]);
    }

    public function salvarPermissao($data)
    {
        // var_dump($data["dados"][0]["id"]);
        // var_dump($data);
        // exit;

        $i = 0;
        foreach ($data["dados"] as $data) {
            // echo $data["modulo"];

            if (@$data["id"])
                $permissao = (new PermissaoModulo())->findById($data["id"]);
            else
                $permissao = new PermissaoModulo();

            $permissao->permissao                  = @$data["permissao"];
            $permissao->usuario_origem             = $_SESSION["id"];
            $permissao->usuario_permitido          = @$data["usuario_permitido"];
            $permissao->pagina                     = @$data["pagina"];
            $permissao->modulo                     = @$data["modulo"];
            $permissao->ver                        = @$data["ver"] ? 1 : 0;
            $permissao->adicionar                  = @$data["adicionar"] ? 1 : 0;
            $permissao->editar                     = @$data["editar"] ? 1 : 0;
            $permissao->excluir                    = @$data["excluir"] ? 1 : 0;

            $permissao->save();
            $i++;
        }

        echo "Sucesso";
    }

    public function usuarioPermissao($data)
    {
        $permissao = new Permissao();

        $verifica = $permissao->find("usuario = '{$_SESSION["id"]}' AND permitido = '{$data["permitido"]}'")->fecth();
        if ($verifica) {
            echo "Email já adicionado";
            exit;
        }

        $permissao->usuario = $_SESSION["id"]; // ID DE USUÁRIO QUE ESTÁ DANDO AS PERMISSÕES
        $permissao->permitido = $data["permitido"]; // ID DE USUÁRIO QUE IRÁ RECEBER AS PERMISSÕES
        $permissao->ativo = 1;

        if ($permissao->save()) {
            echo "Email adicionado com sucesso!";
        } else {
            echo "Erro ao adicionar";
        }
    }

    public function logarSubusuario($data)
    {
        $permissao = (new Permissao())->findById($data["permissao"]);

        if ($permissao) {

            $section = new Section();
            var_dump($section->getId());
            // var_dump($permissao);
            // var_dump($permissao->dataUsuarioOrigem());

            if ($permissao->dataUsuarioOrigem()->ativo == 1) {
                $_SESSION["subusuario"]["tipo"] = $permissao->dataUsuarioOrigem()->tipo_usuario;
                $_SESSION["subusuario"]["nome"] = $permissao->dataUsuarioOrigem()->nome;
                $_SESSION["subusuario"]["nome_abreviado"] = $permissao->dataUsuarioOrigem()->nome_abreviado;
                $_SESSION["subusuario"]["email"] = $permissao->dataUsuarioOrigem()->email;
                $_SESSION["subusuario"]["id"] = $permissao->dataUsuarioOrigem()->id;
                $_SESSION["subusuario"]["idU"] = $permissao->dataUsuarioOrigem()->usuario_dados;
                $_SESSION["subusuario"]["tema"] = $permissao->dataUsuarioOrigem()->tema;
                $_SESSION["subusuario"]["foto"] = $permissao->dataUsuarioOrigem()->foto;
                $_SESSION["subusuario"]["verificado"] = $permissao->dataUsuarioOrigem()->verificado;
                $_SESSION["subusuario"]["testador"] = $permissao->dataUsuarioOrigem()->testador;

                // var_dump($_SESSION);

                if ($_SESSION["tipo"] == 6)
                    header("Location: " . url("vendas"));
                else
                    header("Location: " . URL_BASE . @$data["redirect"]);
            }
        }
    }
}

<?php

//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Produtos;

session_start();

use League\Plates\Engine;
use Source\Models\Produtos\Produto;
use Source\App\Sessao\AppSessao;
use Source\Models\Notificacoes\Notificacao;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Emails;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;

class AppNotificacoes
{
    private $view;
    private $pastaProduto;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->pastaProduto = PASTA . "produtos/";

        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    /**
     * SEÇÃO NOTIFICAÇÕES
     */
    public function notificacoesCheckout($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("notificacao_checkout");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'notificacao_checkout'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $produto = (new Produto())->findById($data["codigo"]);
        $notificacoesCount = (new Notificacao())->find("produto = '{$data["codigo"]}'")->count();
        $paginas = ceil($notificacoesCount / $offset);
        $notificacoes = (new Notificacao())->find("produto = '{$data["codigo"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch();

        echo $this->view->render("produtos/notificacoes", [
            "title" => "Notificacao $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "notificacoes" => $notificacoes,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function notificacaoCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $resultado_bytes = random_bytes(4);
        $codigo = strtoupper(bin2hex($resultado_bytes));

        if (@$data["id"])
            $notificacoes = (new Notificacao())->findById($data["id"]);

        if (!@$notificacoes)
            $notificacoes = new Notificacao();

        $notificacoes->produto = @$data["produto"];
        $notificacoes->frases = implode(",", @$data["frases"]);
        $notificacoes->valor1 = @$data["valor1"];
        $notificacoes->valor2 = @$data["valor2"];
        $notificacoes->valor3 = @$data["valor3"];
        $notificacoes->valor4 = @$data["valor4"];
        $notificacoes->valor5 = @$data["valor5"];
        $notificacoes->ativo = @$data["ativo"];
        $notificacoes->usuario = $_SESSION["id"];

        if ($notificacoes->save()) {
            echo "Notificação salva com sucesso";
        } else {
            echo $notificacoes->fail()->getMessage();
        }
    }

    public function notificacaoEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $notificacoes = (new Notificacao())->findById($data["id"]);

        $notificacoes->produto = $data["produto"];
        $notificacoes->frases = implode(",", @$data["frases"]);
        $notificacoes->valor1 = $data["valor1"];
        $notificacoes->valor2 = $data["valor2"];
        $notificacoes->valor3 = $data["valor3"];
        $notificacoes->valor4 = $data["valor4"];
        $notificacoes->valor5 = $data["valor5"];
        $notificacoes->ativo = @$data["ativo"];
        $notificacoes->usuario = $_SESSION["id"];


        if ($notificacoes->save()) {
            echo "Notificação atualizado com sucesso";
        } else {
            echo $notificacoes->fail()->getMessage();
        }
    }

    public function notificacaoExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $notificacoes = (new Notificacao())->findById($data["id"]);
        if ($notificacoes->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $notificacoes->fail()->getMessage();
        }
    }
    /**
     * FIM SEÇÃO NOTIFICACOES
     */
}

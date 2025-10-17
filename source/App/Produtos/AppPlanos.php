<?php

//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Produtos;

// session_start();

use League\Plates\Engine;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\App\Sessao\AppSessao;
use Source\Models\Checkout\Checkout;
use Source\Models\Upsell\Upsell;
use Source\Models\Campanhas\Campanha;
use Source\Models\Pixel\Pixel;
use Source\Models\URL\Url;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Emails;

class AppProdutos
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

    public function filtros()
    {
        $nome = @$_GET["nome"];
        $categoria = @$_GET["categoria"];
        $status = @$_GET["status"];
        $comissao = @$_GET["comissao"];
        $usuario = @$_GET["usuario"];
        $sql = array();

        if($nome != "") {
            $sql[] = "nome LIKE '%{$nome}%'";
        }

        if($categoria != "") {
            $sql[] = "categoria = {$categoria}";
        }

        if($status != "") {
            $sql[] = "status = {$status}";
        }

        if($comissao != "") {
            $sql[] = "tipo_atribuicao = {$comissao}";
        }

        if($usuario != "") {
            $sql[] = "usuario = {$usuario}";
        }

        $result = count($sql);
        
        for($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }
        
        return implode(" ", $sql);
        
    }

    public function planos($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $produto = (new Produto())->findById($data["codigo"]);
        $planosCount = (new Plano())->find("produto = '{$data["codigo"]}'")->count();
        $paginas = ceil($planosCount / $offset);
        $planos = (new Plano())->find("produto = '{$data["codigo"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        echo(var_dump($planos));
        exit;
        echo $this->view->render("produtos/planos", [
            "title" => "Planos",
            "produto" => $produto,
            "planos" => $planos,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => ""
        ]);
    }

    public function planoCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if (@$data["preco"] < 5) {
            echo "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
            exit;
        }

        $codigo = mt_rand(10000, 999999);

        $plano = new Plano();

        $plano->codigo = $codigo;
        $plano->nome = $data["nome"];
        $plano->produto = $data["produto"];
        $plano->quantidade = @$data["quantidade"];
        $plano->usuario = $_SESSION["id"];
        $plano->preco = $data["preco"];
        $plano->descricao = $data["descricao"];
        $plano->privado = @$data["privado"] == "" ? 0 : 1;
        $plano->status = $data["status"];
        $plano->ativo = @$data["ativo"] == "" ? 0 : 1;
        $plano->televendas = @$data["televendas"];

        if($plano->save()){
            echo "Salvo com sucesso";
        }
        else {
            echo $plano->fail()->getMessage();
        }

    }

    public function planoEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if (@$data["preco"] < 5) {
            echo "Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00";
            exit;
        }

        $plano = (new Plano())->findById($data["id"]);

        $plano->nome = $data["nome"];
        $plano->quantidade = @$data["quantidade"];
        $plano->preco = $data["preco"];
        $plano->descricao = $data["descricao"];
        $plano->privado = @$data["privado"] == "" ? 0 : 1;
        $plano->status = $data["status"];
        $plano->ativo = @$data["ativo"] == "" ? 0 : 1;
        $plano->televendas = @$data["televendas"];

        if($plano->save()){
            echo "Atualizado com sucesso";
        }
        else {
            echo $plano->fail()->getMessage();
        }
    }

    public function planoExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $checkout = (new Plano())->findById($data["id"]);
        if($checkout->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo $checkout->fail()->getMessage();
        }
    }

}
<?php

//CLASSE QUE CONTROLA AS ROTAS DOS USUÁRIOS

namespace Source\App\Pixel;

session_start();

use League\Plates\Engine;
use Source\Models\Pixel\Pixel;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;

class AppPixel
{
    private $view;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->sessao = (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
    }

    public function pixel($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("pixel");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'pixel'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 15;

        $pixelCount = (new Pixel())->find("usuario = {$this->section->getId()}")->count();
        $paginas = ceil($pixelCount / $offset);
        $pixel = (new Pixel())->find("usuario = {$this->section->getId()}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id ASC")->fetch(true);

        echo $this->view->render("pixel/pixel", [
            "title" => "Gerenciar Pixels",
            "section" => @$section,
            "permissao" => @$permissao,
            "pixel" => $pixel,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function cadastroPixel($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $codigo = mt_rand(10000, 999999);

        $pixel = (new Pixel());
        $pixel->codigo = @$codigo;
        $pixel->nome = @$data["nome"];
        $pixel->usuario = $this->section->getId();

        if (@$data["observacao"])
            $pixel->observacao = @$data["observacao"];

        $pixel->boleto = @$data["boleto"];
        $pixel->status = @$data['status'];
        $pixel->executar_checkout = @$data['executar_checkout'];

        if (@$data["valor"])
            $pixel->valor = @$data['valor'];

        $pixel->plataforma = @$data["plataforma"];
        $pixel->tipo_integracao = @$data["tipo_integracao"];
        $pixel->token_acesso = @$data["token_acesso"];
        $pixel->dominio_url = @$data["dominio_url"];
        $pixel->id_pixel = @$data["id_pixel"];
        $pixel->tag_conversao = @$data["tag_conversao"];
        $pixel->evento_taboola = @$data["evento_taboola"];
        $pixel->purchase_taboola = @$data["purchase_taboola"];

        if ($pixel->save()) {
            echo "Cadastrado Com Sucesso!";
        } else {
            echo $pixel->fail()->getMessage();
        }
    }

    public function editarPixel($data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $pixel = (new Pixel())->findById($data["id"]);
        $pixel->nome = @$data["nome"];

        if (@$data["observacao"])
            $pixel->observacao = @$data["observacao"];

        $pixel->boleto = @$data["boleto"];
        $pixel->status = @$data['status'];
        $pixel->executar_checkout = @$data['executar_checkout'];

        if (@$data["valor"])
            $pixel->valor = @$data['valor'];

        $pixel->plataforma = @$data["plataforma"];
        $pixel->tipo_integracao = @$data["tipo_integracao"];
        $pixel->token_acesso = @$data["token_acesso"];
        $pixel->dominio_url = @$data["dominio_url"];
        $pixel->id_pixel = @$data["id_pixel"];
        $pixel->tag_conversao = @$data["tag_conversao"];
        $pixel->evento_taboola = @$data["evento_taboola"];
        $pixel->purchase_taboola = @$data["purchase_taboola"];

        if ($pixel->save()) {
            echo "Atualizado Com Sucesso!";
        } else {
            echo $pixel->fail()->getMessage();
        }
    }

    public function excluirPixel($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $pixel = (new Pixel())->findById($data["id"]);
        if ($pixel->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $pixel->fail()->getMessage();
        }
    }
}

<?php

//CLASSE QUE CONTROLA AS ROTAS DOS USUÁRIOS

namespace Source\App\Denuncias;

session_start();

use League\Plates\Engine;
use Source\Models\Denuncia\Denuncia;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;

class AppDenuncia
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

    public function denuncias($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        $denunciaCount = (new Denuncia())->find()->count();
        $paginas = ceil($denunciaCount / $offset);
        $denuncias = (new Denuncia())->find()->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        
        echo $this->view->render("denuncias/denuncias", [
            "title" => "Gerenciar Denúncias",
            "denuncias" => $denuncias,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function cadastroDenuncia($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $denuncia = (new Denuncia());
        $denuncia->produto = $data["produto"];
        $denuncia->usuario = $data["usuario"];
        $denuncia->assunto = $data["assunto"];
        $denuncia->denuncia = $data["denuncia"];
        
        if($denuncia->save()){
            echo "Enviada!";
        } else {
            echo $denuncia->fail()->getMessage();
        }
    }

    public function editarDenuncia($data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $pixel = (new Pixel())->findById($data["id"]);
        $pixel->nome = $data["nome"];
        $pixel->usuario = $_SESSION["id"];

        if(@$data["observacao"])
        $pixel->observacao = $data["observacao"];

        $pixel->boleto = $data["boleto"];
        $pixel->status = $data['status'];
        $pixel->executar_checkout = $data['executar_checkout'];
        
        if(@$data["valor"])
        $pixel->valor = $data['valor'];
        
        $pixel->plataforma = $data["plataforma"];
        $pixel->id_pixel = $data["id_pixel"];

        if($pixel->save()){
            echo "Atualizado Com Sucesso!";
        } 
        else {
            echo $usuario->fail()->getMessage();
        }
    }

    public function excluirDenuncia($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $pixel = (new Pixel())->findById($data["id"]);
        if($pixel->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo $pixel->fail()->getMessage();
        }
    }
}
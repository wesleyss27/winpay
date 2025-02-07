<?php

namespace Source\App\Mex;

use League\Plates\Engine;
use Source\Models\Mex\Mex;
use Source\App\Sessao\AppSessao;
use Source\Models\Permissao\Section;

session_start();

class AppMex
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

    public function cadastrar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $mex = (new Mex())->find("usuario = " . $this->section->getId())->fetch();

        if (!@$mex) {
            $mex = new Mex();

            $mex->usuario            = $this->section->getId();
        }


        $mex->token              = $data["mex_token"];
        $mex->msg_cartao_pago      = $data["mex_cartao_aprovado"];
        $mex->msg_cartao_analise   = $data["mex_cartao_analise"];
        $mex->msg_cartao_cancelado = $data["mex_cartao_cancelado"];
        $mex->msg_boleto_pago      = $data["mex_boleto_pago"];
        $mex->msg_boleto_gerado    = $data["mex_boleto_gerado"];
        $mex->msg_boleto_cancelado = $data["mex_boleto_cancelado"];
        $mex->msg_pix_gerado       = $data["mex_pix_gerado"];
        $mex->msg_pix_pago         = $data["mex_pix_pago"];
        $mex->msg_pix_cancelado    = $data["mex_pix_cancelado"];
        $mex->msg_carrinho         = $data["mex_abandono_carrinho"];

        //$mex->save();

        if ($mex->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $mex->fail()->getMessage();
        }
    }

    public function excluirMex($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $mex = (new Mex())->findById($data["id"]);
        if ($mex->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo "fail" . $mex->fail()->getMessage();
        }
    }
}

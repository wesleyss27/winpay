<?php

namespace Source\App\Asaas;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Asaas\Asaas;
use Source\Models\Permissao\Section;

@session_start();

class AppAsaas
{
    private $view;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        // $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        // $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        // $this->view->addData(["router" => $router]);

        // $this->sessao = (new AppSessao())->verificaSeLogado();
        // $this->section = new Section();
    }

    public function cadastrarUsuarioAsaas($data)
    {
        $userAsaas = $this->verificarCliente($data["id_user"]);

        if (!$userAsaas) {
            $asaas = new Asaas();
            $asaas->id_user         = @$data["id_user"];
            $asaas->client_id       = @$data["client_id"];
            $asaas->wallet_id       = @$data["wallet_id"];
            $asaas->account_id      = @$data["account_id"];
            $asaas->api_key         = @$data["api_key"];
            $asaas->agency          = @$data["agency"];
            $asaas->account         = @$data["account"];
            $asaas->account_digit   = @$data["account_digit"];

            $asaas->save();
        } else {
            return $userAsaas->client_id;
        }
    }

    public function verificarCliente($id_cliente)
    {
        $userAsaas = (new Asaas())->find("id_user = $id_cliente")->fetch();

        if ($userAsaas) {
            return $userAsaas;
        } else {
            return false;
        }
    }

    public function setClientId($data)
    {
        $userAsaas = (new Asaas())->findById($data["id"]);

        if ($userAsaas) {
            $userAsaas->client_id = @$data["client_id"];

            if ($userAsaas->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setWalletId($data)
    {
        $userAsaas = (new Asaas())->findById($data["id"]);

        if ($userAsaas) {
            $userAsaas->wallet_id = @$data["wallet_id"];

            if ($userAsaas->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setAccountId($data)
    {
        $userAsaas = (new Asaas())->findById($data["id"]);

        if ($userAsaas) {
            $userAsaas->account_id = @$data["account_id"];

            if ($userAsaas->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

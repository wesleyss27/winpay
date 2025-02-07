<?php

namespace Source\App\Leadlovers;

use CoffeeCode\Router\Router;
use League\Plates\Engine;
use Source\Models\ChatGuru\ChatGuru;
use Source\Models\Vendas\Venda;
use Source\Models\Postback\Postback;
use Source\Models\Leadlovers\Leadlovers;
use Source\App\Sessao\AppSessao;
use Source\Models\Permissao\Section;

session_start();

class AppLeadlovers
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

        //$this->sessao = (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
    }

    public function cadastro($data)
    {
        // session_start();
        $lead = (new Leadlovers());
        // var_dump(@$data);
        // exit;

        $lead->usuario          = $this->section->getId();
        $lead->descricao        = @$data["descricao"];
        $lead->token            = @$data["token"];
        $lead->produto          = @$data["produto"];
        $lead->tipo_integracao  = @$data["tipo_integracao"];
        $lead->acao             = @$data["acao"] ? $data["acao"] : 1;
        $lead->eventos          = implode(",", @$data["eventos"]);
        if (@$data["tipo_integracao"] == "plataforma") {
            $lead->maquina          = @$data["cod_maquina"];
            $lead->maquina_nome     = @$data["maquina"];
            $lead->funil            = @$data["cod_funil"];
            $lead->funil_nome       = @$data["funil"];
            $lead->nivel            = @$data["cod_nivel"];
            $lead->nivel_nome       = @$data["nivel"];
        }
        // var_dump($lead);
        // exit;
        if (@$lead->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $lead->fail()->getMessage();
        }
    }

    public function editar($data)
    {
        $lead = (new Leadlovers())->findById(@$data["id"]);

        if (@$lead) {
            $lead->descricao        = @$data["descricao"];
            $lead->token            = @$data["token"];
            $lead->produto          = @$data["produto"];
            $lead->tipo_integracao  = @$data["tipo_integracao"];
            $lead->acao             = @$data["acao"] ? $data["acao"] : 1;
            $lead->eventos          = implode(",", @$data["eventos"]);
            if (@$data["tipo_integracao"] == "plataforma") {
                $lead->maquina          = @$data["cod_maquina"];
                $lead->maquina_nome     = @$data["maquina"];
                $lead->funil            = @$data["cod_funil"];
                $lead->funil_nome       = @$data["funil"];
                $lead->nivel            = @$data["cod_nivel"];
                $lead->nivel_nome       = @$data["nivel"];
            }
            // var_dump($lead);
            // exit;
            if (@$lead->save()) {
                echo "Salvo com sucesso";
                // echo $lead->fail()->getMessage();
            } else {
                echo $lead->fail()->getMessage();
            }
        }
    }

    public function excluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $lead = (new Leadlovers())->findById(@$data["id"]);
        if ($lead->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $lead->fail()->getMessage();
        }
    }

    public function verificaCadastro($data)
    {
        // session_start();
        // $this->sessao = (new AppSessao())->verificaSeLogado();
        // var_dump(@$data);
        // exit;
        $url = 'http://llapi.leadlovers.com/webapi/user?token=' . @$data["token"];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'Erro ao verificar conta #:' . $err;
        } else {
            echo $response;
        }
    }

    public function carregaMaquinas($data)
    {
        $url = 'http://llapi.leadlovers.com/webapi/Machines?token=' . @$data["token"];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            $resposta = json_decode($response);
            echo json_encode($resposta->Items);
        }
    }

    public function carregaFunil($data)
    {
        // var_dump($data);
        $url = 'http://llapi.leadlovers.com/webapi/EmailSequences?token=' . @$data["token"] . '&machineCode=' . @$data["maquina"];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            $resposta = json_decode($response);
            echo json_encode($resposta->Items);
        }
    }

    public function carregaNivel($data)
    {
        // var_dump($data);
        $url = 'http://llapi.leadlovers.com/webapi/Levels?token=' . @$data["token"] . '&machineCode=' . @$data["maquina"] . '&sequenceCode=' . @$data["funil"];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            $resposta = json_decode($response);
            // echo $response;
            echo json_encode($resposta->Items);
        }
    }

    public function enviaLead($data)
    {
        $url = 'http://llapi.leadlovers.com/webapi/Lead?token=' . @$data["token"];
        $email = @$data["email"];
        $nome = @$data["nome"];
        $tel = @$data["tel"];
        $maquina = @$data["maquina"];
        $email_code = @$data["funil"];
        $nivel = @$data["nivel"];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'Email=' . @$email . '&Name=' . @$nome . '&MachineCode=' . @$maquina . '&EmailSequenceCode=' . @$email_code . '&SequenceLevelCode=' . @$nivel . '&Phone=' . @$tel,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),

        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            echo $response;
        }
    }
}

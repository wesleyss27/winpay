<?php

//CLASSE QUE CONTROLA AS ROTAS DOS COMPRADORES

namespace Source\App\Compradores;

session_start();

use League\Plates\Engine;

use Source\Models\Compradores\Comprador;
use Source\Helpers\Paginador;

class AppCompradores
{
    private $view;
    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema2", "php");
        $this->view->addData(["router" => $router]);
    }

    public function compradores($data):void
    {
        $filtros = $this->filtros();
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        $compradoresCount = (new Comprador())->find($filtros)->count();
        $paginas = ceil($compradoresCount / $offset);
        $compradores = (new Comprador())->find($filtros)->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        echo $this->view->render("compradores", [
            "title" => "Compradores | " . SITE,
            "compradores" => $compradores,
            "count" => $compradoresCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function filtros()
    {
        $comprador = @$_GET["nome"];

        $nome = explode(" ", $comprador, 2);

        $sql = array();

        if($comprador != "" && @$nome[1] != "") {
            $sql[] = "(nome LIKE '{$nome[0]}' AND sobrenome LIKE '".@$nome[1]."%') OR cpf = '{$comprador}' OR cep = '{$comprador}' OR cidade = '{$comprador}'";
        }
        elseif($comprador != "" && @$nome[1] == "") {
            $sql[] = "nome LIKE '{$comprador}' OR cpf = '{$comprador}' OR cep = '{$comprador}' OR cidade = '{$comprador}'";
        }

        $result = count($sql);
        
        for($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }
        
        return implode(" ", $sql);
        
    }

    public function limpaStr($valor)
    {
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        $valor = str_replace("(", "", $valor);
        $valor = str_replace(")", "", $valor);
        $valor = str_replace(" ", "", $valor);
        return $valor;
    }

    public function cadastroCompradores($data):void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $comprador = (new Comprador());
        $comprador->nome = $data["nome"];
        $comprador->sobrenome = $data["sobrenome"];
        $comprador->email = $data["email"];
        $comprador->telefone = $data["telefone"];
        $comprador->cpf = $data["cpf"];
        $comprador->rua = $data["rua"];
        $comprador->numero = $data["numero"];
        $comprador->bairro = $data["bairro"];
        $comprador->cidade = $data["cidade"];
        $comprador->estado = $data["uf"];
        $cep = $this->limpaStr($data["cep"]);
        $comprador->cep = $cep;

        $comprador->add("ativo", $data["complemento"], $data["pais"]);
        
        $cpf = $comprador->find("cpf = :cpf", "cpf = {$data['cpf']}")->fetch(true);

        if($cpf) {
            $msg = "CPF Já Cadastrado";
            $classCss = "bg-danger";
        }
        else {
            if($comprador->save()){
                $msg = "Cadastrado Com Sucesso!";
                $classCss = "bg-success";
            } 
            else {
                $msg = "Erro Ao Cadastrar!"."<br>";
                $msg .= $comprador->fail()->getMessage();
                $classCss = "bg-danger";
            }
        }

        /*$compradores = (new Comprador())->find()->fetch(true);

        echo $this->view->render("compradores", [
            "title" => "Compradores | " . SITE,
            "compradores" => $compradores,
            "msg" => $msg,
            "classCss" => $classCss
        ]);*/

        $data["msg"] = $msg;
        $data["classCss"] = $classCss;

        $this->compradores($data);
    }

    public function editarComprador($data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $comprador = (new Comprador())->findById($data["id"]);
        $comprador->nome = $data["nome"];
        $comprador->sobrenome = $data["sobrenome"];
        $comprador->email = $data["email"];
        $comprador->telefone = $data["telefone"];
        
        $comprador->cpf = $data["cpf"];
        $comprador->rua = $data["rua"];
        $comprador->numero = $data["numero"];
        $comprador->bairro = $data["bairro"];
        $comprador->cidade = $data["cidade"];
        $comprador->estado = $data["uf"];
        $cep = $this->limpaStr($data["cep"]);
        $comprador->cep = $cep;

        $comprador->add("ativo", $data["complemento"], $data["pais"]);

            if($comprador->save()){
                $msg = "Atualizado Com Sucesso!";
                $classCss = "bg-success";
            } 
            else {
                $msg = "Erro Ao Atualizar!"."<br>";
                $msg .= $comprador->fail()->getMessage();
                $classCss = "bg-danger";
            }

        /*$compradores = (new Comprador())->find()->fetch(true);

        echo $this->view->render("compradores", [
            "title" => "Compradores | " . SITE,
            "compradores" => $compradores,
            "msg" => $msg,
            "classCss" => $classCss
        ]);*/

        $data["page"] = $data["pagina"];
        $data["msg"] = $msg;
        $data["classCss"] = $classCss;

        $this->compradores($data);
    }
}
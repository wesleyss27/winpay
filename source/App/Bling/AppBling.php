<?php

//CLASSE QUE CONTROLA AS ROTAS DOS USUÁRIOS

namespace Source\App\Bling;

use League\Plates\Engine;
use Source\Models\Bling\Bling;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Permissao\Section;

class AppBling
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

        $this->section = new Section();
    }

    public function bling($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 20;

        $blingCount = (new Bling())->find()->count();
        $paginas = ceil($blingCount / $offset);

        $produtos = (new Produto())->find("usuario = " . $this->section->getId())->fetch(true);
        $bling = (new Bling())->find("usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        echo $this->view->render("bling/bling", [
            "title" => "Integração com Bling",
            "bling" => $bling,
            "produtos" => $produtos,
            "total" => $paginas,
            "paginador" => $paginador,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function cadastrarBling($data)
    {
        session_start();
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $bling = new Bling();
        $bling->usuario = $this->section->getId();

        if(@$data["produtobling"])
        $bling->produto = @$data["produtobling"];
        $bling->api_key = $data["api_key"];

        if($bling->save()){
            echo "Cadastrado com sucesso";
        } else {
            echo $bling->fail()->getMessage();
        }
    }

    public function editarBling($data)
    {
        session_start();
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $bling = (new Bling())->findById($data["id"]);
        
        if(@$data["produtobling"])
        $bling->produto = @$data["produtobling"];
        $bling->api_key = $data["api_key"];

        if($bling->save()){
            echo "Salvo com sucesso";
        } else {
            echo $bling->fail()->getMessage();
        }
    }

    public function excluirBling($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $bling = (new Bling())->findById($data["id"]);
        if($bling->destroy()){
            echo "Excluído com sucesso";
        } else {
            echo $bling->fail()->getMessage();
        }
    }

    public function criarPedido($data)
    {
        $produto = $data["produto"] ? $data["produto"] : "";
        $usuario = $data["usuario"];

        // echo $produto, $usuario;

        $bling = (new Bling())->find("usuario = $usuario")->fetch(true);
        $blingCount = (new Bling())->find("usuario = $usuario")->count();
        $venda = (new Venda())->findById($data["venda"]);

        include_once "xml-pedido.php";

        $url = 'https://bling.com.br/Api/v2/pedido/json/';

        $texto  = "Qtd integrações: $blingCount<br>";
        $texto .= "Produto: " . @$data["produto"] . "<br>";
        $texto .= "Usuário: " . @$data["usuario"] . "<br>";
        $texto .= "Venda: " . @$data["venda"] . "<br>";

        if ($venda && $venda->bling == 1) {
            return "Venda já enviada";
            exit;
        }
        // var_dump($bling, $usuario, $produto);
        if($bling && $venda->status_transacao == 4){
            foreach ($bling as $bling) {
                // echo "$bling->produto - $produto";
                if(($bling->produto && $bling->produto == $produto) || !$bling->produto){
                    $xml = pedidoXml($venda);
                    $posts = array (
                        "apikey" => "$bling->api_key",
                        "xml" => rawurlencode($xml)
                    );
                    // echo "xml " . $xml . "<br>";

                    $retorno = $this->executeSendOrder($url, $posts);
                    // echo $retorno;

                    $texto .= $retorno;

                    //$retorno = json_encode($retorno);
                    $retorno = json_decode($retorno);
                    //var_dump($retorno->retorno->erros);
                    // echo $venda->id . "<br>";
                    
                    if(@$retorno->retorno->pedidos){
                        $venda->bling = 1;
                        if($venda->save()){
                            echo "Enviada com sucesso";
                        } else {
                            echo $venda->fail()->getMessage();
                        }
                    }

                    if(@$retorno->retorno->erros){
                        foreach ($retorno->retorno->erros as $retorno) {
                            if(@$retorno->erro->cod == 30){
                                $venda->bling = 1;
                                if($venda->save()){
                                    echo "Enviada com sucesso";
                                } else {
                                    echo $venda->fail()->getMessage();
                                }
                            }
                        }
                    }
                }
            }
        }

        $arquivo = fopen('bling.txt','a+');
        fwrite($arquivo, $texto . "\n");
        fclose($arquivo);
    }

    public function executeSendOrder($url, $data){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_POST, count($data));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }
}
<?php

//CLASSE QUE CONTROLA AS ROTAS DOS USUÁRIOS

namespace Source\App\Whatsapp;

use League\Plates\Engine;
use Source\Models\Vendas\Venda;
use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Postback\Postback;
use Source\Models\Whatsapp\NotificacoesInteligentes;
use Source\Models\Usuarios\Usuario;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;

class AppNotificacoesInteligentes
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    public function notificacoes($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 10;

        $notificacoesCount = (new NotificacoesInteligentes())->find("usuario = " . $_SESSION["id"])->count();
        $paginas = ceil($notificacoesCount / $offset);
        $notificacoes = (new NotificacoesInteligentes())->find("usuario = " . $_SESSION["id"])->fetch(true);

        echo $this->view->render("whatsapp/notificacoes-inteligentes", [
            "title" => "Notificações Inteligentes",
            "notificacoes" => $notificacoes,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);

        /**
         * $notificacao = new AppNotificacoesInteligentes(null);

         * $dados = $notificacao->carrinhoAbandonado(null);
         * echo $dados;
         * echo $notificacao->enviaDados($dados, null);
         */
    }

    public function cadastrarNotificacoesInteligentes($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $notificacoes = new NotificacoesInteligentes();
        $notificacoes->usuario = $_SESSION["id"];
        $notificacoes->token = $data["token"];
        $notificacoes->integracao_id = $data["integracao_id"];

        if($notificacoes->save()){
            echo "Salvo com sucesso";
        } else {
            echo $notificacoes->fail()->getMessage();
        }
    }

    public function editarNotificacoesInteligentes($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();
        
        $notificacoes = (new NotificacoesInteligentes())->findById($data["id"]);
        $notificacoes->token = $data["token"];
        $notificacoes->integracao_id = $data["integracao_id"];

        if($notificacoes->save()){
            echo "Salvo com sucesso";
        } else {
            echo $notificacoes->fail()->getMessage();
        }
    }

    public function excluirNotificacao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $notificacoes = (new NotificacoesInteligentes())->findById($data["id"]);
        if($notificacoes->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo $notificacoes->fail()->getMessage();
        }
    }

    public function carrinhoAbandonado($data)
    {
        $dados = array(
            "checkout" => array(
                "url" => @$data["url_checkout"]
            ),

            "customer" => array(
                "first_name" => "Pedro",
                "last_name" => "Henrique da Silva",
                "phone" => "+5533999102490",
                "email" => "pedro@octa.life"
            ),
            "order" => array(
                "products" => array(
                    array(
                        "name" => "Barra de Chocolate",
                        "quantity" => rand(),
                        "value" => 10
                    )
                )
            )
        );

        return json_encode($dados);
    }

    public function pedidos($data)
    {
        $nome = explode(" ", @$data["nome"], 2);
        $dados = array(
            "customer" => array(
                "first_name" => @$nome[0],
                "last_name" => @$nome[1],
                "phone" => "+55" . @$data["ddd"] . @$data["telefone"],
                "email" => @$data["email"]
            ),
            "order" => array(
                "id" => @$data["transacao"],
                "products_total" => @$data["valor_bruto"],
                "total" => @$data["valor_bruto"],
                "discount" => @$data["valor_desconto"],
                "status" => "Pedido Aprovado",
                "products" => array(
                    array(
                        "name" => @$data["nomekit"],
                        "quantity" => 1,
                        "value" => @$data["valor_produto"]
                    )
                ),
                "delivery" => array(
                    "fee" => 0,
                    "description" => "Frete Pedido",
                    "code" => "INDISPONIVEL" . rand(10, 99)
                )
            )
        );

        if(@$data["link_boleto"]){
            $dados["order"]["billet_url"] = @$data["link_boleto"];
            $dados["order"]["billet_barcode"] = "123456789 123456789 123456789 1234567" . rand(10, 99);
            $dados["order"]["billet_value"] = @$data["valor_bruto"];
            $dados["order"]["billet_due_date"] = date("Y-m-d");
        }

        return json_encode($dados);
    }

    public function enviaDados($dados, $api)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$api['url']}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dados,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$api['token']}",
                'Content-Type: application/json',
                'Cookie: __cfduid=d4f561bb46780269f8a2e7de31816e5381611150701'
            ),
        ));

        try {
            $response = curl_exec($curl);
        } catch (\Throwable $th) {
            $response = "Erro";
        }
        

        curl_close($curl);
        //echo $response;

    }

}

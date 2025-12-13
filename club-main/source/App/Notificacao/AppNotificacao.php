<?php

namespace Source\App\Notificacao;

session_start();

use League\Plates\Engine;
use Source\Models\Notificacao\Notificacao;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\Usuario;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Helpers\Emails;
use Pusher\Pusher;

class AppNotificacao
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
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 15;

        $notificacoesCount = (new Notificacao())->find("usuario = {$_SESSION["id"]}")->count();
        $paginas = ceil($notificacoesCount / $offset);
        $notificacoes = (new Notificacao())->find("usuario = {$_SESSION["id"]}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        echo $this->view->render("notificacoes", [
            "title" => "Notificações",
            "notificacoes" => $notificacoes,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function postbackNotificacao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        // var_dump($data);
        $dados["venda"] = $data["venda"];
        if(@$data["forma_pagamento"] == 1){
            $dados["texto"] = "Parabéns! Nova transação efetuada.";
        } else {
            $dados["texto"] = "Boleto gerado";
        }

        $produto = (new Produto())->findById($data["produto"]);
        $dados["texto"] .= " - " . strtoupper($produto->nome);
        $dados["link"] = url("vendas?venda=" . $data["transacao"]);
        
        $dados["usuario"] = $data["produtor"];
        $this->cadastroNotificao($dados);
        
        if (@$data["afiliado"]) {
            $dados["usuario"] = $data["afiliado"];
            $dados["texto"] .= " - Sua comissão se a transação for paga: R$ " . number_format($data["comissao_afiliado"], 2, ",", ".");
            $this->cadastroNotificao($dados);
        }

        $app_id = '1116646';
        $app_key = '3947f07a6edc6d29a706';
        $app_secret = '94cb689f7b2b2ff5e5cb';
        $app_cluster = 'us2';

        $pusher = new Pusher( $app_key, $app_secret, $app_id, array('cluster' => $app_cluster) );

        $data['message'] = $dados["texto"];
        $data['link'] = $dados["link"];
        $pusher->trigger('my-channel', 'my-event', $data);
    }

    public function postbackEmail($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $email = new Emails();
        $usuario = (new Usuario())->findById($dados["usuario"]);
        $dados["nome"] = $usuario->nome;
        $dados["email"] = $usuario->email;
        $dados["produto"] = $produto->nome;

        if(@$data["forma_pagamento"] == 1){
            $boleto = $email->boletoGerado($dados);
            $email  = $email->enviaPhp($boleto);
        } else {
            $cartao = $email->vendaRealizada($dados);
            $email  = $email->enviaPhp($cartao);
        }
        
        if (@$data["afiliado"]) {
            $dados["nome"] = $usuario->nome;
            $dados["email"] = $usuario->email;

            if(@$data["forma_pagamento"] == 1){
                $boleto = $email->boletoGerado($data);
                $email  = $email->enviaPhp($boleto);
            } else {
                $cartao = $email->vendaRealizada($data);
                $email  = $email->enviaPhp($cartao);
            }
        }

        var_dump($dados);
        var_dump($data);
    }

    public function cadastroNotificao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $notificacao = (new Notificacao());
        $notificacao->usuario = $data["usuario"];
        $notificacao->venda = $data["venda"];
        $notificacao->texto = $data["texto"];
        $notificacao->lida = 0;

        if(@$data["link"]){
            $notificacao->link = $data["link"];
        }

        if($notificacao->save()){
            echo "Cadastrado Com Sucesso!";
        } else {
            echo $notificacao->fail()->getMessage();
        }
    }

    public function editarNotificao($data): void
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

    public function excluirNotificao($data)
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
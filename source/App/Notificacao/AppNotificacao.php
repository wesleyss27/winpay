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
use Source\Models\Permissao\Section;

class AppNotificacao
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

    public function notificacoes($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 15;

        $notificacoesCount = (new Notificacao())->find("usuario = {$this->section->getId()}")->count();
        $paginas = ceil($notificacoesCount / $offset);
        $notificacoes = (new Notificacao())->find("usuario = {$this->section->getId()}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        echo $this->view->render("notificacoes", [
            "title" => "Notificações",
            "notificacoes" => $notificacoes,
            "count" => $notificacoesCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function postbackNotificacao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $app_id = '1116646';
        $app_key = '3947f07a6edc6d29a706';
        $app_secret = '94cb689f7b2b2ff5e5cb';
        $app_cluster = 'us2';

        $pusher = new Pusher($app_key, $app_secret, $app_id, array('cluster' => $app_cluster));

        $mensagem = array(
            1 => array(
                1 => "",
                2 => "",
                3 => "",
                4 => "Parabéns!!! Nova venda de cartão realizada!",
                5 => "",
                6 => ""
            ),
            2 => array(
                1 => "Novo Boleto Gerado!",
                2 => "",
                3 => "",
                4 => "Parabéns!!! Nova venda de boleto realizada!",
                5 => "",
                6 => ""
            ),
            3 => array(
                1 => "Pix Gerado!",
                2 => "",
                3 => "",
                4 => "Parabéns!!! Nova venda de pix realizada!",
                5 => "",
                6 => ""
            ),
        );

        $dados["venda"] = $data["venda"];
        $dados["titulo"] = @$mensagem[@$data["forma_pagamento"]][@$data["status_transacao"]];

        $produto = (new Produto())->findById($data["produto"]);
        $texto = $dados["titulo"] . " " . $produto->nome . "! ";
        $dados["link"] = url("vendas?venda=" . $data["transacao"]);

        $dados["usuario"] = $data["produtor"];
        if (@$data["comissao_produtor"])
            $dados["texto"] = $texto . " - Sua comissão: R$ " . number_format($data["comissao_produtor"], 2, ",", ".");

        $this->cadastroNotificao($dados);
        $data['message'] = $dados["texto"];
        $data['link'] = $dados["link"];
        $pusher->trigger('my-channel', 'my-event', $data);

        if (@$data["afiliado"]) {
            $dados["usuario"] = $data["afiliado"];
            $dados["texto"] = $texto . " | Sua comissão: R$ " . number_format($data["comissao_afiliado"], 2, ",", ".");
            $this->cadastroNotificao($dados);
            $data['message'] = $dados["texto"];
            $pusher->trigger('my-channel', 'my-event', $data);
        }

        if (@$data["coprodutores"]) {
            $usuarios = explode("/", $data["coprodutores"]);
            $comissao = explode("/", @$data["comissao_coprodutores"]);
            $i = 0;

            foreach ($comissao as $comissao) {
                if ($comissao > 0) {
                    @$dados["comissao"][$i] = $comissao;
                    $i++;
                }
            }

            $i = 0;
            foreach ($usuarios as $usuario) {
                if ($usuario > 0) {
                    $dados["coprodutor"] = $usuario;
                    @$dados["usuario"] = $usuario;
                    @$dados["texto"] = $texto . " || Sua comissão: R$ " . @number_format(@$dados["comissao"][$i], 2, ",", ".");
                    $this->cadastroNotificao($dados);
                    // $data['message'] = $dados["texto"];
                    // $pusher->trigger('my-channel', 'my-event', $data);
                    $i++;
                }
            }
        }
    }

    public function postbackEmail($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $email = new Emails();
        $usuario = (new Usuario())->findById($dados["usuario"]);
        $dados["nome"] = $usuario->nome;
        $dados["email"] = $usuario->email;
        $dados["produto"] = $produto->nome;

        if (@$data["forma_pagamento"] == 1) {
            $boleto = $email->boletoGerado($dados);
            $email  = $email->enviaSendGrid($boleto);
        } else {
            $cartao = $email->vendaRealizada($dados);
            $email  = $email->enviaSendGrid($cartao);
        }

        if (@$data["afiliado"]) {
            $dados["nome"] = $usuario->nome;
            $dados["email"] = $usuario->email;

            if (@$data["forma_pagamento"] == 1) {
                $boleto = $email->boletoGerado($data);
                $email  = $email->enviaSendGrid($boleto);
            } else {
                $cartao = $email->vendaRealizada($data);
                $email  = $email->enviaSendGrid($cartao);
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
        $notificacao->titulo = $data["titulo"];
        $notificacao->texto = $data["texto"];
        $notificacao->lida = 0;

        if (@$data["link"]) {
            $notificacao->link = $data["link"];
        }

        if ($notificacao->save()) {
            echo "Cadastrado Com Sucesso!";
        } else {
            echo $notificacao->fail()->getMessage();
        }
    }

    public function editarNotificao($data): void
    {
        $pixel = (new Pixel())->findById($data["id"]);
        $pixel->nome = $data["nome"];
        $pixel->usuario = $this->section->getId();

        if (@$data["observacao"])
            $pixel->observacao = $data["observacao"];

        $pixel->boleto = $data["boleto"];
        $pixel->status = $data['status'];
        $pixel->executar_checkout = $data['executar_checkout'];

        if (@$data["valor"])
            $pixel->valor = $data['valor'];

        $pixel->plataforma = $data["plataforma"];
        $pixel->id_pixel = $data["id_pixel"];

        if ($pixel->save()) {
            echo "Atualizado Com Sucesso!";
        } else {
            echo $usuario->fail()->getMessage();
        }
    }

    public function excluirNotificao($data)
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

<?php

namespace Source\App\ChatGuru;

use CoffeeCode\Router\Router;
use League\Plates\Engine;
use Source\Models\ChatGuru\ChatGuru;
use Source\Models\Vendas\Venda;
use Source\App\Sessao\AppSessao;
use Source\App\Processos\AppProcessos;
use Source\Models\Permissao\Section;

// session_start();

class AppChatGuru
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

    public function cadastrarChatGuru($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();
        // var_dump(@$data);
        // exit;
        $chat = new chatGuru();

        $chat->usuario              = $this->section->getId();
        $chat->endpoint             = trim($data["endpoint"]);
        $chat->key_api              = trim($data["key"]);
        $chat->account_id           = trim($data["id_conta"]);
        $chat->phone_id             = trim($data["id_aparelho"]);
        $chat->msg_cartao_pago      = $data["cartao_aprovado"];
        $chat->msg_cartao_analise   = $data["cartao_analise"];
        $chat->msg_cartao_cancelado = $data["cartao_cancelado"];
        $chat->msg_boleto_pago      = $data["boleto_pago"];
        $chat->msg_boleto_gerado    = $data["boleto_gerado"];
        $chat->msg_boleto_cancelado = $data["boleto_cancelado"];
        $chat->msg_pix_gerado       = $data["pix_gerado"];
        $chat->msg_pix_pago         = $data["pix_pago"];
        $chat->msg_pix_cancelado    = $data["pix_cancelado"];
        $chat->msg_carrinho         = $data["abandono_carrinho"];


        if ($chat->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $chat->fail()->getMessage();
        }
    }

    public function editarChatGuru($data)
    {

        $chat = (new ChatGuru())->findById($data["idchat"]);


        $chat->endpoint             = trim($data["endpoint"]);
        $chat->key_api              = trim($data["key"]);
        $chat->account_id           = trim($data["id_conta"]);
        $chat->phone_id             = trim($data["id_aparelho"]);
        $chat->msg_cartao_pago       = $data["cartao_aprovado"];
        $chat->msg_cartao_analise    = $data["cartao_analise"];
        $chat->msg_cartao_cancelado  = $data["cartao_cancelado"];
        $chat->msg_boleto_pago       = $data["boleto_pago"];
        $chat->msg_boleto_gerado     = $data["boleto_gerado"];
        $chat->msg_boleto_cancelado  = $data["boleto_cancelado"];
        $chat->msg_pix_gerado        = $data["pix_gerado"];
        $chat->msg_pix_pago          = $data["pix_pago"];
        $chat->msg_pix_cancelado     = $data["pix_cancelado"];
        $chat->msg_carrinho          = $data["abandono_carrinho"];


        if ($chat->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $chat->fail()->getMessage();
        }
    }

    public function excluirChatGuru($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $chat = (new ChatGuru())->findById($data["id"]);
        if ($chat->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $chat->fail()->getMessage();
        }
    }

    public function cadastrarChatAPI($data)
    {
        $url = @$data["url"];
        $dados["key"] = @$data["key"];
        $dados["account_id"] = @$data["account_id"];
        $dados["phone_id"] = @$data["phone_id"];
        $dados["chat_number"] = @$data["chat_number"];
        $dados["action"] = "chat_add"; //Parametro Fixo para cadastrar CHAT
        $dados["name"] = @$data["name"];
        $dados["text"] = @$data["text"];
        
        // var_dump(@$dados);
        // exit;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $dados
        ));

        $response = curl_exec($curl);
        // exit;
        curl_close($curl);
        $log["chat"] = $response;
        (new AppProcessos(null))->logs($log,"dados-chatguru");
        // echo ($response);
        $resposta = json_decode($response);

        $log["name"] = @$data["name"];
        $log["text"] = @$data["text"];
        $log["chat"] = $response;

        (new AppProcessos(null))->logs($log, "dados-chatguru");
        
        if (@$resposta->code == 201) {
            return 1;
        } else {
            return 0;
        }

        //     // Verifica se o chat foi cadastrado com sucesso

        //     $dados_chat["url"] = $url;
        //     $dados_chat["id_venda"] = $id_venda;
        //     $dados_chat["key"] = @$dados["key"];
        //     $dados_chat["account_id"] = @$dados["account_id"];
        //     $dados_chat["phone_id"] = @$dados["phone_id"];
        //     $dados_chat["chat_number"] = @$dados["chat_number"];
        //     $dados_chat["action"] = "chat_add_status";
        //     $dados_chat["chat_add_id"] = $resposta->chat_add_id;
        //     // $dados_chat["chat_add_id"] = "615c4d209eeadd4016ed4511";

        //     $curl = curl_init();

        //     $url = url("/chatguru/anotacao");

        //     curl_setopt_array($curl, array(
        //         CURLOPT_URL => "$url",
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => "",
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 0,
        //         CURLOPT_FOLLOWLOCATION => false,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_POST => true,
        //         CURLOPT_POSTFIELDS => $dados_chat
        //     ));
        //     $response = curl_exec($curl);
        //     // exit;
        //     curl_close($curl);
        //     // $this->verificaChat($url, $dados_chat, $id_venda);

        // }
    }


    // Verifica se o chat foi cadastro com sucesso
    // public function verificaChat($data)
    // {
    //     sleep(20);
    //     $venda = (new Venda())->findById($data["id_venda"]);
    //     $produto = $venda->dataProduto()->nome;
    //     if (@$venda->plano)
    //         $plano = $venda->dataPlano()->nome;

    //     $valor = $venda->valor_bruto;
    //     $url = $data["url"];
    //     // var_dump($data);
    //     // exit;
    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => "$url",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => false,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => $data
    //     ));

    //     $response = curl_exec($curl);
    //     // exit;
    //     curl_close($curl);
    //     // echo $response;
    //     // exit;
    //     $resposta = json_decode($response);


    //     if ($resposta->chat_add_status == "done") {
    //         // Verifica se o chat foi cadastrado com sucesso
    //         $dados_anotacao["key"] = @$data["key"];
    //         $dados_anotacao["account_id"] = @$data["account_id"];
    //         $dados_anotacao["phone_id"] = @$data["phone_id"];

    //         if (strlen(@$data["chat_number"]) == 13) {
    //             $tel_inicio = substr(@$data["chat_number"], 0, 4);
    //             $tel_fim = substr(@$data["chat_number"], 5, 13);
    //             $dados_anotacao["chat_number"] = $tel_inicio . $tel_fim;

    //         } else

    //             $dados_anotacao["chat_number"] = @$data["chat_number"];
    //         // $dados_anotacao["chat_add_id"] = $resposta->chat_add_id;
    //         $dados_anotacao["action"] = "note_add";
    //         $dados_anotacao["note_text"] = "-- RedRocket -- \n Essa mensagem não aparece para o cliente \n Venda: " . $id_venda . " \n" . "Valor: " . $valor . "\n" . "Produto: " . $produto;
    //         if (@$venda->plano)
    //             $dados_anotacao["note_text"] .= "\n" . "Plano: " . $plano;
    //         $this->addAnotacaoChat($url, $dados_anotacao);
    //     }
    // }


    // Adiciona anotação ao chat
    // public function addAnotacaoChat($url, $data)
    // {
    //     // var_dump($data);
    //     // exit;
    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => "$url",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => false,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => $data
    //     ));

    //     $response = curl_exec($curl);
    //     // exit;
    //     curl_close($curl);
    //     echo $response;
    // }
}

<?php

namespace Source\App\Checkout1;

session_start();

use League\Plates\Engine;
use Source\Helpers\Emails;

use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Vendas\Venda;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Helpers\Paginador;
use Source\App\Sessao\AppSessao;
use Source\App\Postback\AppPostback;
use Source\App\ChatGuru\AppChatGuru;
use Source\App\Whatsapp\AppNotificacoesInteligentes;
use Source\Models\ChatGuru\ChatGuru;
use Source\Models\Leadlovers\Leadlovers;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;
use Source\Models\Whatsapp\NotificacoesInteligentes;

class AppCarrinhoAbandonado
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

    public function abandonos($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("abandono_checkout");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'abandono_checkout'")->fetch();

        $filtros = $this->filtros();
        //var_dump($filtros);

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        if (@$this->section->getTipo() == 4) {
            if ($filtros)
                $filtros .= " AND ";

            $busca = $filtros . "(produtor = " . @$this->section->getId() . " OR afiliado = " . @$this->section->getId() . ")";
        } elseif (@$this->section->getTipo() == 5) {
            if ($filtros)
                $filtros .= " AND ";

            $busca = $filtros . "(afiliado = " . @$this->section->getId() . ")";
        } else {
            $busca = $filtros;
        }

        $_SESSION["busca"] = $busca;

        //echo "id u ".$_SESSION["idU"];var_dump($busca);

        $vendasCount = (new CarrinhoAbandonado())->find($busca)->count();
        $paginas = ceil($vendasCount / 30);
        $vendas = (new CarrinhoAbandonado())->find($busca)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        $produtos = new Produto();
        //var_dump($vendasCount);

        echo $this->view->render("vendas/abandonos", [
            "title" => "Abandono de checkout",
            "section" => @$section,
            "permissao" => @$permissao,
            "vendas" => $vendas,
            "produtos" => $produtos,
            "count" => $vendasCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"],
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function filtros()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];
        $produto = @$_GET["produto"];
        $comprador = @$_GET["comprador"];
        $sql = array();

        if ($inicio != "" || $fim != "") {
            if ($inicio == "")
                $inicio = date("Y-m-d");

            if ($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "created_at BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'";
        }

        if ($produto != "") {
            $sql[] = "produto = $produto";
        }

        if ($comprador != "") {
            $sql[] = "nome LIKE '%$comprador%' OR cpf_cnpj = '$comprador' OR email LIKE '%$comprador%'";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function cadastroCarrinho($data)
    {
        $carrinho = new CarrinhoAbandonado();

        $plano = @$data["plano"] ? " OR plano = " . @$data["plano"] : "";
        $produto = @$data["produto"];
        $cpf = @$data["cpf"];
        $telefone = trim(@$data["telefone"]);

        if (@$telefone == "") {
            $dados["msg"] = "Telefone Vazio";
            echo json_encode($dados);
            exit;
        }

        if (strlen(@$telefone) < 10 || strlen(@$telefone) > 12) {
            $dados["msg"] = "Telefone invalido";
            echo json_encode($dados);
            exit;
        }

        if (@$data["nome"] == "") {
            $dados["msg"] = "Nome vazio";
            echo json_encode($dados);
            exit;
        }

        $this->carrinhoChatGuru();

        // if (@$cpf == "") {
        //     $dados["msg"] = "CPF vazio";
        //     echo json_encode($dados);
        //     exit;
        // }

        // if (strlen(@$cpf) < 11) {
        //     $dados["msg"] = "CPF/CNPJ Invalido";
        //     echo json_encode($dados);
        //     exit;
        // }


        $produto2 = (new Produto());
        $id = $produto2->find()->limit(1)->order("id DESC")->fetch(true);

        if ($id) {
            foreach ($id as $id)
                $carrinho->codigo = $id->codigo + 1;
            // echo $carrinho->codigo;
        } else {
            $carrinho->codigo = "01010";
        }

        $busca = $carrinho->find("telefone = '$telefone' AND (produto = $produto $plano)")->fetch();

        if (@$busca) {
            $dados["msg"] = "Carrinho já cadastrado";
            $dados["msg"] .= " CPF: $cpf - PRODUTO: $produto - PLANO $plano";
            $dados["id"] = $busca->id;

            $carrinho = (new CarrinhoAbandonado())->findById($busca->id);
            $carrinho->nome             = @$data["nome"];
            $carrinho->email            = @$data["email"];
            $carrinho->telefone         = @$data["telefone"];
            $carrinho->cpf_cnpj         = @$data["cpf"];
            $carrinho->cep              = @$data["cep"];
            $carrinho->rua              = @$data["rua"];
            $carrinho->numero           = @$data["numero"];
            $carrinho->complemento      = @$data["complemento"];
            $carrinho->bairro           = @$data["bairro"];
            $carrinho->cidade           = @$data["cidade"];
            $carrinho->estado           = @$data["estado"];
            $carrinho->url_checkout     = @$data["url_checkout"];

            if ($carrinho->save()) {
                $dados["msg1"] =  "Dados atualizados";

                // CADASTRO DO CARRINHO NO TELEVENDAS
                $produto = (new Produto())->findById(@$data["produto"]);
                if (@$produto && @$produto->habilitar_televendas == 1) {
                    //  VERIFICA SE O PRODUTO ESTÁ HABILITADO PARA ENVIAR ABANDONO DE CARRINHO PARA O TELEVENDAS
                    $eventos = explode(",", @$produto->eventos_televendas);
                    foreach ($eventos as $evento) {
                        if ($evento == 10) {
                            $data["id_carrinho"] = $carrinho->id;
                            $this->enviarTelevendas($data);
                            break;
                        }
                    }
                }
                // FIM CADASTRO DO CARRINHO NO TELEVENDAS

                $this->leadLovers($carrinho->id);
            } else {
                $dados["msg"] =  $carrinho->fail()->getMessage();
            }
        } else {

            $carrinho->produtor         = @$data["produtor"];
            $carrinho->produto          = @$data["produto"];

            if (@$data["plano"])
                $carrinho->plano            = @$data["plano"];

            if (@$data["afiliado"])
                $carrinho->afiliado         = @$data["afiliado"];

            if (@$data["campanha"])
                $carrinho->campanha         = @$data["campanha"];

            $carrinho->nome             = @$data["nome"];
            $carrinho->email            = @$data["email"];
            $carrinho->telefone         = @$data["telefone"];
            $carrinho->cpf_cnpj         = @$data["cpf"];
            $carrinho->cep              = @$data["cep"];
            $carrinho->rua              = @$data["rua"];
            $carrinho->numero           = @$data["numero"];
            $carrinho->complemento      = @$data["complemento"];
            $carrinho->bairro           = @$data["bairro"];
            $carrinho->cidade           = @$data["cidade"];
            $carrinho->estado           = @$data["estado"];
            $carrinho->url_checkout     = @$data["url_checkout"];

            //var_dump($carrinho);

            function comprador($data)
            {
                $comprador  = "Nome: " . @$data["nome"] . "<br>";
                $comprador .= "Email: <a href='{$data["email"]}'>" . @$data["email"] . "</a><br>";
                $comprador .= "Telefone: " . @$data["ddd"] . @$data["telefone"] . "<br>";
                $comprador .= "Cidade: " . @$data["cidade"] . "<br>";
                $comprador .= "Estado: " . @$data["estado"] . "<br><br>";

                return $comprador;
            }

            if ($carrinho->save()) {
                // CADASTRO DO CARRINHO NO TELEVENDAS
                $produto = (new Produto())->findById($data["produto"]);
                if (@$produto && @$produto->habilitar_televendas == 1) {
                    $data["id_carrinho"] = $carrinho->id;
                    $this->enviarTelevendas($data);
                    // exit;
                }
                // FIM CADASTRO DO CARRINHO NO TELEVENDAS

                $this->leadLovers($carrinho->id);

                $dados["id"] = $carrinho->id;

                /*
                $data["id_produto"] = $data["produto"];
                $data["comprador"] = comprador($data);
                $data["nome"] = $carrinho->dataProdutor()->nome;
                $data["email"] = $carrinho->dataProdutor()->email;
                $data["produto"] = $carrinho->dataProduto()->nome;

                if (@$data["plano"])
                    $data["plano"] = $carrinho->dataPlano()->nome;
                */

                /*$boleto = (new Emails())->carrinhoAbandonado($data);
                $enviaEmail = (new Emails())->enviaSendGrid($boleto);

                $dados["email"] = $enviaEmail;*/

                $data["carrinho"] = $carrinho->id;
                $data["produto"] = $data["id_produto"];

                //$postback = (new AppPostback(null))->enviarPostbackCarrinho($data);
                //$dados["postback"] = @$postback;
        
                $notificacoesInteligentes = $this->notificacoesInteligentes($data);

            } else {
                $dados["msg"] =  $carrinho->fail()->getMessage();
            }
        }

        echo json_encode($dados);
    }

    /**
     * Notificação inteligente para enviar a notificação de carrinho abandonado.
     *
     */
    public function notificacoesInteligentes($data)
    {

        $notificacao = (new NotificacoesInteligentes())->find("usuario = " . $data['produtor'])->fetch();

        $data = array(
            "id_venda" => $data['id'],
            "nome" => $data["nome"] ?? null,
            "email" => $data['email'],
            "telefone" => $data['telefone'],
            "produto" => $data["nome_produto"],
            "valor_produto" => $data["valor_produto"] ?? null,
            "link_boleto" =>$data["link_boleto"] ?? null,
            "linha_digitavel" => $data["linha_digitavel"] ?? null,
            "data_vencimento" =>  $data["data_vencimento"] ?? null,
            "link_checkout" =>  $data["url_checkout"] ?? null,
            "afiliado" =>  $data["afiliado"] ?? null,
        );

        if ($notificacao) {
            $api["url"] = "https://api.notificacoesinteligentes.com/integrations/$notificacao->integracao_id/events/abandoned-cart";
            $api["token"] = $notificacao->token;

            $mensagem = new AppNotificacoesInteligentes(null);

            try {
                $pedido = $mensagem->pedidosCarrrinhoAbandonado($data);
                $envia = $mensagem->enviaDados($pedido, $api);
                // $pedido = "Enviando... " . $envia;
            } catch (Exception $e) {
                $envia = "Erro... " . $e;
            }

            // var_dump($envia);
            return $envia;
        }
    }
    public function excluirCarrinho($data)
    {
        $vendas = (new Venda())->find("carrinho_abandonado IS NOT NULL")->fetch(true);
        $contarVendas = (new Venda())->find("carrinho_abandonado IS NOT NULL")->count();
        $carrinho = new CarrinhoAbandonado();
        //var_dump($contarVendas);

        if ($vendas) {
            foreach ($vendas as $venda) {
                echo "<h4>Carrinho abandonado encontrado em venda</h4>";
                echo "<b>Venda: </b> $venda->id - $venda->transacao | <b>Carrinho:</b> $venda->carrinho_abandonado <br>";

                $abandono = $carrinho->findById($venda->carrinho_abandonado);
                echo "Nome: $abandono->nome $abandono->id <br>";

                if (@$abandono->id && $venda->carrinho_abandonado == @$abandono->id) {
                    if ($abandono->destroy()) {
                        $venda->carrinho_abandonado = NULL;
                        if ($venda->save()) {
                            echo "Venda atualizada<br>";
                        } else {
                            echo $venda->fail()->getMessage() . "<br>";
                        }
                    } else {
                        echo $abandono->fail()->getMessage();
                        echo "<br>Carrinho não excluido <br>";
                    }
                } else {
                    $venda->carrinho_abandonado = NULL;
                    if ($venda->save()) {
                        echo "Venda atualizada<br>";
                    } else {
                        echo $venda->fail()->getMessage() . "<br>";
                    }
                }
            }
        }
    }

    public function exportar($data)
    {
        //var_dump($_SESSION);
        $busca = $_SESSION["busca"];
        $vendas = (new CarrinhoAbandonado())->find($busca)->order("id DESC")->fetch(true);

        /** 
         * DADOS TABELA EXCEL
         * $analitico[9] = array("Boleto Outros", $upsell, $upsellValores, $p)
         */
        $forma_pagamento = array(
            1 => 'Cartão de Crédito',
            2 => 'Boleto',
            3 => 'Pix',
        );

        $valoresCoprodutor = @$_SESSION["valores_coproducao"];

        $i = 1;

        // var_dump($busca, $vendas);

        if ($vendas) {
            foreach ($vendas as $venda) {
                $analitico[] = array(
                    $venda->dataProduto()->codigo,
                    $venda->dataProduto()->nome,
                    formataDataHora($venda->created_at),
                    $venda->nome,
                    $venda->cpf_cnpj,
                    $venda->email,
                    $venda->telefone,
                    $venda->rua,
                    $venda->numero,
                    $venda->complemento,
                    $venda->bairro,
                    $venda->cidade,
                    $venda->estado,
                    $venda->cep,
                    $venda->plano ? $venda->dataPlano()->nome : "",
                    $venda->plano ? $venda->dataPlano()->quantidade : "",
                );

                // if($valoresCoprodutor){
                //     // $analitico[32 + $i] = 
                //     $valoresCoprodutor[$i][2];
                //     echo $venda->id . " - " . $analitico[32 + $i] . "<br>";
                // }

                $i++;
            }
        }
        // echo "<pre>";
        // var_dump($valoresCoprodutor);
        // echo "</pre>";

        $tabela2  = "<table border='1' class='table table-striped'>";

        $tabela2 .= "<thead>";
        $tabela2 .= "<tr>";
        $tabela2 .= "<th scope='col'>CODIGO PRODUTO</th>";
        $tabela2 .= "<th scope='col'>PRODUTO</th>";
        $tabela2 .= "<th scope='col'>Data do Carrinho</th>";
        $tabela2 .= "<th scope='col'>Nome comprador</th>";
        $tabela2 .= "<th scope='col'>CNPJ/CPF</th>";
        $tabela2 .= "<th scope='col'>E-mail comprador</th>";
        $tabela2 .= "<th scope='col'>Telefone</th>";
        $tabela2 .= "<th scope='col'>Endereco</th>";
        $tabela2 .= "<th scope='col'>Numero</th>";
        $tabela2 .= "<th scope='col'>Complemento</th>";
        $tabela2 .= "<th scope='col'>Bairro</th>";
        $tabela2 .= "<th scope='col'>Cidade</th>";
        $tabela2 .= "<th scope='col'>ESTADO</th>";
        $tabela2 .= "<th scope='col'>Cep</th>";
        $tabela2 .= "<th scope='col'>Plano</th>";
        $tabela2 .= "<th scope='col'>Itens Plano</th>";
        $tabela2 .= "</tr>";
        $tabela2 .= "</thead>";

        if (@$analitico) {
            foreach ($analitico as $dado) {
                $tabela2 .= "<tr>";
                $tabela2 .= "<td>" . @$dado[0] . "</td>";
                $tabela2 .= "<td>" . @$dado[1] . "</td>";
                $tabela2 .= "<td>" . @$dado[2] . "</td>";
                $tabela2 .= "<td>" . @$dado[3] . "</td>";
                $tabela2 .= "<td>" . @$dado[4] . "</td>";
                $tabela2 .= "<td>" . @$dado[5] . "</td>";
                $tabela2 .= "<td>" . @$dado[6] . "</td>";
                $tabela2 .= "<td>" . @$dado[7] . "</td>";
                $tabela2 .= "<td>" . @$dado[8] . "</td>";
                $tabela2 .= "<td>" . @$dado[9] . "</td>";
                $tabela2 .= "<td>" . @$dado[10] . "</td>";
                $tabela2 .= "<td>" . @$dado[11] . "</td>";
                $tabela2 .= "<td>" . @$dado[12] . "</td>";
                $tabela2 .= "<td>" . @$dado[13] . "</td>";
                $tabela2 .= "<td>" . @$dado[14] . "</td>";
                $tabela2 .= "<td>" . @$dado[15] . "</td>";
                $tabela2 .= "</tr>";

                // echo "<pre>";
                // var_dump(@$dado[33]);
                // echo "</pre>";
            }
        }

        $tabela2 .= "</table>";

        $arquivo = "carrinho-abandonado.xls";
        $_SESSION["tabela"]["abandonado"] = $tabela2;

        // echo $tabela2;
        header("Location: " . url("abandono/exportar-venda"));
    }

    public function exportarVendas($data)
    {
        $arquivo = "carrinho-abandonado.xls";

        header("Cache-Control: no-cache, must-revalidate");
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel');
        header("Content-Disposition: attachment; filename=\"{$arquivo}\"");

        echo @$_SESSION["tabela"]["abandonado"] . "<br>";
    }


    public function enviarTelevendas(array $data)
    {
        // var_dump($data);
        $carrinho = (new CarrinhoAbandonado())->findById($data["id_carrinho"]);
        $plano = (new Plano())->find("produto = {$carrinho->produto}")->fetch();

        if (@$carrinho) {

            if (@$plano->televendas == 1)
                $data["plano"] = @$carrinho->dataPlano()->codigo;

            if (@$data["afiliado"]) {
                $data["nome_afiliado"] = @$carrinho->dataAfiliado()->nome;
                $data["id_afiliado"] = @$carrinho->dataAfiliado()->id;
            }

            $data["nome"] = @$carrinho->nome;
            $data["email"] = @$carrinho->email;
            $data["telefone"] = @$carrinho->telefone;
            $data["cpf"] = @$carrinho->cpf_cnpj;
            $data["nome_produtor"] = @$carrinho->dataProdutorTelevendas()->nome;
            $data["cpf_produtor"] = @$carrinho->dataProdutorTelevendas()->cpf_cnpj;
            $data["email_produtor"] = @$carrinho->dataProdutorTelevendas()->email;
            $data["cep"] = @$carrinho->cep;
            $data["rua"] = @$carrinho->rua;
            $data["numero"] = @$carrinho->numero;
            $data["complemento"] = @$carrinho->complemento;
            $data["bairro"] = @$carrinho->bairro;
            $data["cidade"] = @$carrinho->cidade;
            $data["estado"] = @$carrinho->estado;
            $data["produto"] = @$carrinho->dataProduto()->codigo;

            if (@$data["campanha"])
                $data["campanha"] = @$carrinho->campanha;

            $data["url_checkout"] = @$carrinho->url_checkout;

            // var_dump($data);
            // exit;
            $curl = curl_init();
            $url = url_televendas("televendas/carrinho-abandonado");
            // $url_foto = url("/uploads/produtos/" .$produto->foto);
            // echo"lalala";
            // var_dump($data);
            // exit;
            curl_setopt_array($curl, array(
                CURLOPT_URL => "$url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => @$data
            ));

            $response = curl_exec($curl);
            // exit;
            curl_close($curl);
            echo $response;

            // return $response;
        }
    }

    // Envia mensagem do carrinho abandonado via chatGuru
    public function carrinhoChatGuru()
    {
        // echo "entrou chatguru";
        // exit;
        date_default_timezone_set("America/Recife");
        $carrinhos = (new CarrinhoAbandonado())->find("chatguru = 0 AND telefone is not null AND nome is not null AND produto is not null AND produtor is not null AND created_at BETWEEN '" . date("Y-m-d H:i:s", strtotime("-48 hours")) . "' AND '" . date("Y-m-d H:i:s", strtotime("-10 minutes")) . "'")->fetch(true);
        // $carrinhos = (new CarrinhoAbandonado())->find("chatguru = 0 AND created_at BETWEEN '" . date("Y-m-d", strtotime("+10 minutes")) . "' AND '" . date("Y-m-d", strtotime("-48 hours")) . "'")->fetch(true);

        if (@$carrinhos) {

            foreach ($carrinhos as $carrinho) {
                // echo "Encontrou carrinho - nome comprador: $carrinho->nome";
                $chatguru = (new ChatGuru())->find("usuario = $carrinho->produtor")->fetch();
                if ($chatguru) {
                    // echo "Usuario encontrado no chatguru - nome: $chatguru->usuario";
                    $dados["url"] = @$chatguru->endpoint;
                    $dados["key"] = @$chatguru->key_api;
                    $dados["account_id"] = @$chatguru->account_id;
                    $dados["phone_id"] = @$chatguru->phone_id;
                    $dados["chat_number"] = '55' . @$carrinho->telefone;
                    $dados["name"] = @$carrinho->nome;

                    $nome = explode(" ", $carrinho->nome);
                    if (@$nome[0] && @$nome[1])
                        $nome = $nome[0] . " " . $nome[1];
                    else
                        $nome = $nome[0];

                    $produto = $carrinho->dataProduto()->nome;

                    $procura = strripos(@$chatguru->msg_carrinho, "{nome}");
                    if ($procura === 0 || $procura === false)
                        $dados["text"] = @$chatguru->msg_carrinho;
                    else
                        $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_carrinho);

                    $procura_prod = strripos(@$chatguru->msg_carrinho, "{produto}");
                    if ($procura_prod != 0 || $procura_prod != false)
                        $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);

                    $chatguruAPI = new AppChatGuru(null);

                    // var_dump($dados);
                    // exit;
                    $retorno = $chatguruAPI->cadastrarChatAPI($dados);

                    if ($retorno == 1) {
                        $carrinho->chatguru = 1;
                        $carrinho->save();
                    }
                }
            }
        }
    }

    public function leadLovers($id)
    {
        $carrinho = (new CarrinhoAbandonado())->findById($id);

        if ($carrinho && @$carrinho->nome && @$carrinho->email && @$carrinho->telefone) {
            $leadlovers = (new Leadlovers())->find("usuario = $carrinho->produtor AND produto = $carrinho->produto")->fetch(true);

            if (@$leadlovers) {
                foreach (@$leadlovers as $leads) {
                    if ($leads->tipo_integracao == "plataforma") {
                        $this->leadLoversPlataforma($carrinho, $leads);
                    } elseif ($leads->tipo_integracao == "produto") {
                        $this->leadLoversProduto($carrinho, $leads);
                    }
                }
            } else {
                return "Não tem leadlovers";
            }
        }
    }

    // integração pela api, documentação leadlovers
    public static function leadLoversPlataforma($carrinho, $leads)
    {
        $telefone = $carrinho->telefone;
        $telefone = 55 . @$telefone;

        $eventosLead = explode(",", @$leads->eventos);
        $maquinaLead = explode("/", @$leads->maquina);
        $funilLead = explode("/", @$leads->funil);
        $nivelLead = explode("/", @$leads->nivel);

        foreach ($eventosLead as $evento) {
            if ($evento == 10) {

                $url = 'http://llapi.leadlovers.com/webapi/lead?token=' . @$leads->token;
                $email = @$carrinho->email;
                $nome = @$carrinho->nome;
                // $maquina = 1;
                $maquina = @$maquinaLead[0];
                $email_code = @$funilLead[0];
                $nivel = @$nivelLead[0];
                $curl = curl_init();
                // echo $url;
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => "Email=$email&Name=$nome&MachineCode=$maquina&EmailSequenceCode=$email_code&SequenceLevelCode=$nivel&Phone=$telefone",
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded'
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    return 'cURL Error #:' . $err;
                } else {
                    return $response;
                }
            }
        }
    }

    // integração com leadlovers pelas configurações nativas
    public static function leadLoversProduto($carrinho, $leadlovers)
    {
        $paymentWay = "Boleto";

        $nome = explode(" ", $carrinho->nome);
        // echo var_dump($nome[0]);
        if (@$nome[0] && @$nome[1]) {
            $first_name = $nome[0];
            $last_name = $nome[1];
        } else
            $first_name = $nome[0];

        $telefone = $carrinho->telefone;
        if (strlen($telefone) == 10) {
            $ddd = substr($telefone, 0, 2);
            $telefone = substr($telefone, 2, 8);
        } elseif (strlen($telefone) == 11) {
            $ddd = substr($telefone, 0, 2);
            $telefone = substr($telefone, 2, 9);
        } else {
            $ddd = substr($telefone, 0, 2);
            $telefone = substr($telefone, 2, strlen($telefone));
        }

        $statusPedido = "approved";

        $leadlovers = explode(",", @$leadlovers->eventos);
        foreach ($leadlovers as $lead) {
            if ($lead == 10) {
                $dados["json"] = '{
                        "paymentGateway": {  
                            "paymentGatewayId":"16",
                            "productId":"' . $carrinho->produto . '",  
                         },  
                         "transaction":{  
                            "transactionId": "' . $carrinho->id . '",
                            "recurrency":"1",	    
                            "recurrencyPeriod":"360",
                            "paymentWay":"' . $paymentWay . '",
                            "transactionStatus":"' . $statusPedido . '",
                            "transactionPrice": ' . $carrinho->dataPlano()->preco . ',
                            "transactionCurrency":"BRL",
                            "transactionDate":"' . $carrinho->updated_at . '"
                         },
                         "shopper":{  
                            "leadId":"' . $carrinho->id . '",
                            "firtsName":"' . $first_name . '",
                            "lastName":"' . $last_name . '",
                            "email":"' . $carrinho->email . '",      	  
                            "phones":[{"ddi":"55","ddd":"' . $ddd . '","number":"' . $telefone . '"}],
                            "addressStreet":"' . $carrinho->rua . '",
                            "addressNumber":"' . $carrinho->numero . '",
                            "addressDistrict":"' . $carrinho->bairro . '",
                            "addressCountry":"Brasil",
                            "addressState":"' . $carrinho->estado . '",
                            "addressCity":"' . $carrinho->cidade . '",
                            "addressZipCode":"' . $carrinho->cep . '"      
                         }
                     }';

                $url = 'https://app.leadlovers.com/payment/octa';
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => @$dados["json"],
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded'
                    ),
                ));

                // echo $dados["json"];

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                // echo "<pre>";
                // echo "<!-- EMAIL: " . $venda->dataComprador()->email . " | NOME: $nome | TELEFONE:$telefone | ID LEADLOVERS: $leads->id | DESCRIÇÃO: $leads->descricao | MAQUINA: $maquina | FUNIL: $email_code | NIVEL: $nivel";
                // echo $statusPedido;
                // echo $response;
                return $response;
            }
        }
    }
}

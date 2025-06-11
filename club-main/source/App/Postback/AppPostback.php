<?php

//CLASSE QUE CONTROLA AS ROTAS DOS USUÁRIOS

namespace Source\App\Postback;

use League\Plates\Engine;
use Source\Models\Vendas\Venda;
use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Produtos\Produto;
use Source\Models\Postback\Postback;
use Source\Models\Usuarios\Usuario;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;

class AppPostback
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

    public function postback($data)
    {
        session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 20;

        $postbacksCount = (new Postback())->find()->count();
        $paginas = ceil($postbacksCount / $offset);

        $produtos = (new Produto())->find("usuario = " . $_SESSION["id"])->fetch(true);
        $postbacks = (new Postback())->find("usuario = " . $_SESSION["id"])->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        $usuario = (new Usuario())->findById($_SESSION["id"]);

        echo $this->view->render("postback/postback", [
            "title" => "Postbacks",
            "produtos" => $produtos,
            "postbacks" => $postbacks,
            "usuario" => $usuario,
            "total" => $paginas,
            "paginador" => $paginador,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function cadastrarPostback($data)
    {
        session_start();
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $postback = new Postback();
        $postback->usuario = $_SESSION["id"];

        if(@$data["produto"])
        $postback->produto = @$data["produto"];
        $postback->url = $data["url"];
        $postback->eventos = implode(",", $data["evento"]);
        $postback->chave_unica = $data["chave_unica"];
        $postback->json = $data["json"];

        if($postback->save()){
            echo "Cadastrado com sucesso";
        } else {
            echo $postback->fail()->getMessage();
        }
    }

    public function editarPostback($data)
    {
        session_start();
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $postback = (new Postback())->findById($data["id"]);
        
        if(@$data["produto"])
        $postback->produto = @$data["produto"];
        $postback->url = @$data["url"];
        $postback->eventos = implode(",", $data["evento"]);
        $postback->chave_unica = $data["chave_unica"];
        $postback->json = $data["json"];

        if($postback->save()){
            echo "Cadastrado com sucesso";
        } else {
            echo $postback->fail()->getMessage();
        }
    }

    public function testarPostback($data)
    {
        $forma_pagamento = array(
            1 => "CARTÃO DE CRÉDITO",
            2 => "BOLETO",
            3 => "PIX"
        );

        $status_transacao = array(
            1 => "Aguardando pagamento",
            2 => "Em Análise",
            3 => "Pré Autorizada",
            4 => "Paga",
            5 => "Cancelada",
            6 => "Devolvida",
            7 => "Chargeback",
            8 => "Concluída",
            9 => "Bloqueada"
        );

        $chave = @$data["chave_unica"] ? $data["chave_unica"] : 'db890335eaf4a7cb8df604ec01fdaf17';
        $content = array(
            'chave_unica'               => $chave,

            "transacao"                 => "1",
            "nome_produto"              => "Produto Teste",
            "codigo_produto"            => "1",
            "valor_produto"             => "199.00",

            "nome_comprador"            => "Nome Teste",
            "telefone_comprador"        => "33999999999",
            "email_comprador"           => "teste@teste.com",
            "cpf_cnpj_comprador"        => "12345678901",
            "cep_comprador"             => "00000000",
            "rua_comprador"             => "Rua Teste",
            "numero_comprador"          => "00",
            "complemento_comprador"     => "",
            "bairro_comprador"          => "Bairro Teste",
            "cidade_comprador"          => "Cidade Teste",
            "estado_comprador"          => "MG",
            "pais_comprador"            => "BR",

            "forma_pagamento"           => @$forma_pagamento[1],
            "status_transacao"          => @$status_transacao[4],
            "valor_bruto"               => "199.00",
            "valor_frete"               => "0.00",
            "valor_desconto"            => "0.00",
            "valor_liquido"             => "199.00",
            "parcelas"                  => 1,
            "data_pedido"               => date("Y-m-d"),
            "data_finalizada"           => date("Y-m-d H:i:s"),
            "data_ultimo_evento"        => date("Y-m-d H:i:s"),
            "data_criacao"              => date("Y-m-d H:i:s"),
            "data_atualizacao"          => date("Y-m-d H:i:s"),
        );

        //var_dump($content, "<br><br>");

        /*$context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'=> "Content-type: application/x-www-form-urlencoded",
                'content' => $content,
            )
        ));
        
        //$result = file_get_contents('http://localhost/sistema/pedidos-hubsale', null, $context);
        $result = file_get_contents($data['url'], null, $context);*/

        if (@$data["json"]) {
            $content = json_encode($content);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "{$data['url']}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $content,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
        } else {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "{$data['url']}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $content,
                ));
            }

        $response = curl_exec($curl);

        curl_close($curl);
        //return $response;

        if($response){
            echo "Ok \n";
            echo $response;
        }
    }

    public function enviarPostback($data)
    {
        function postback($venda, $postback){
            $forma_pagamento = array(
                1 => "CARTÃO DE CRÉDITO",
                2 => "BOLETO",
                3 => "PIX"
            );

            $status_transacao = array(
                1 => "Aguardando pagamento",
                2 => "Em Análise",
                3 => "Pré Autorizada",
                4 => "Paga",
                5 => "Cancelada",
                6 => "Devolvida",
                7 => "Chargeback",
                8 => "Concluída",
                9 => "Bloqueada",
                10 => "Carrinho Abandonado",
            );

            $nome_comprador            = $venda->endereco ? @$venda->dataEndereco()->nome : @$venda->dataComprador()->nome;
            $telefone_comprador        = $venda->endereco ? @$venda->dataEndereco()->telefone : @$venda->dataComprador()->telefone;
            $email_comprador           = $venda->endereco ? @$venda->dataEndereco()->email : @$venda->dataComprador()->email;
            $cpf_cnpj_comprador        = $venda->endereco ? @$venda->dataEndereco()->cpf_cnpj : @$venda->dataComprador()->cpf_cnpj;
            $cep_comprador             = $venda->endereco ? @$venda->dataEndereco()->cep : @$venda->dataComprador()->cep_entrega;
            $rua_comprador             = $venda->endereco ? @$venda->dataEndereco()->rua : @$venda->dataComprador()->rua_entrega;
            $numero_comprador          = $venda->endereco ? @$venda->dataEndereco()->numero : @$venda->dataComprador()->numero_entrega;
            $complemento_comprador     = $venda->endereco ? @$venda->dataEndereco()->complemento : @$venda->dataComprador()->complemento_entrega;
            $bairro_comprador          = $venda->endereco ? @$venda->dataEndereco()->bairro : @$venda->dataComprador()->bairro_entrega;
            $cidade_comprador          = $venda->endereco ? @$venda->dataEndereco()->cidade : @$venda->dataComprador()->cidade_entrega;
            $estado_comprador          = $venda->endereco ? @$venda->dataEndereco()->estado : @$venda->dataComprador()->estado_entrega;
            $pais_comprador            = $venda->endereco ? @$venda->dataEndereco()->pais : @$venda->dataComprador()->pais_entrega;

            $content = array(
                'chave_unica'               => $postback->chave_unica,

                "transacao"                 => @$venda->transacao,
                "nome_produto"              => @$venda->dataProduto()->nome,
                "codigo_produto"            => @$venda->dataProduto()->codigo,
                "valor_produto"             => @$venda->dataProduto()->preco,
                "nome_plano"                => @$venda->dataPlano()->nome,
                "codigo_plano"              => @$venda->dataPlano()->codigo,
                "valor_plano"               => @$venda->dataPlano()->preco,
                "itens_plano"               => @$venda->dataPlano()->quantidade,

                "nome_comprador"            => @$nome_comprador,
                "telefone_comprador"        => @$telefone_comprador,
                "email_comprador"           => @$email_comprador,
                "cpf_cnpj_comprador"        => @$cpf_cnpj_comprador,
                "cep_comprador"             => @$cep_comprador,
                "rua_comprador"             => @$rua_comprador,
                "numero_comprador"          => @$numero_comprador,
                "complemento_comprador"     => @$complemento_comprador,
                "bairro_comprador"          => @$bairro_comprador,
                "cidade_comprador"          => @$cidade_comprador,
                "estado_comprador"          => @$estado_comprador,
                "pais_comprador"            => @$pais_comprador,

                "nome_produtor"             => @$venda->dataProdutor()->nome,
                "telefone_produtor"         => @$venda->dataProdutor()->telefone,
                "email_produtor"            => @$venda->dataProdutor()->email,
                "cpf_cnpj_produtor"         => @$venda->dataProdutor()->cpf_cnpj,

                "nome_afiliado"             => @$venda->dataAfiliado()->nome,
                "telefone_afiliado"         => @$venda->dataAfiliado()->telefone,
                "email_afiliado"            => @$venda->dataAfiliado()->email,
                "cpf_cnpj_afiliado"         => @$venda->dataAfiliado()->cpf_cnpj,

                "forma_pagamento"           => @$forma_pagamento[$venda->forma_pagamento],
                "status_transacao"          => @$status_transacao[$venda->status_transacao],
                "status_transacao_codigo"   => @$venda->status_transacao,
                "url_checkout"              => "https://" . @$venda->url_checkout,
                "link_boleto"               => @$venda->link_boleto,
                "linha_digitavel"           => @$venda->linha_digitavel,
                "valor_bruto"               => @$venda->valor_bruto - @$venda->valor_juros,
                "valor_frete"               => @$venda->valor_frete,
                "valor_desconto"            => @$venda->valor_desconto,
                "valor_liquido"             => @$venda->valor_liquido,
                "parcelas"                  => @$venda->parcelas,
                "data_vencimento_boleto"    => @$venda->data_vencimento,
                "data_pedido"               => @$venda->data_pedido,
                "data_finalizada"           => @$venda->data_finalizada,
                "data_ultimo_evento"        => @$venda->data_ultimo_evento,
                "data_criacao"              => @$venda->created_at,
                "data_atualizacao"          => @$venda->updated_at,
                
                "src"                       => @$venda->src,
                "utm_source"                => @$venda->utm_source,
                "utm_campaign"              => @$venda->utm_campaign,
                "utm_medium"                => @$venda->utm_medium,
                "utm_content"               => @$venda->utm_content,
            );

            //var_dump($content, "<br><br>");

            /*$context = stream_context_create(array(
                'http' => array(
                    'method'  => 'POST',
                    'header'=> "Content-type: application/x-www-form-urlencoded",
                    'content' => $content,
                )
            ));
            
            //$result = file_get_contents('http://localhost/sistema/pedidos-hubsale', null, $context);
            $result = file_get_contents($postback->url, null, $context);

            //var_dump($result);
            return $result;*/

            //var_dump($postback);
            if (@$postback->json) {
                $content = json_encode($content);
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "$postback->url",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $content,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
            } else {
    
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "$postback->url",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $content,
                    ));
                }

            $response = curl_exec($curl);

            curl_close($curl);
            //return $response;

            if($response){
                return "Ok \n $response";
            }
        }

        //return $data["produto"];
        $produtor = @$data['produtor'];
        $produto = @$data['produto'];
        $postback = (new Postback())->find("usuario = $produtor AND produto = $produto")->fetch(true);
        $postbackC = (new Postback())->find("usuario = $produtor AND produto = $produto")->count();
        $postback2 = (new Postback())->find("usuario = $produtor AND produto IS NULL")->fetch(true);
        $retorno = "";

        $status_transacao = array(
            1 => "Aguardando pagamento",
            2 => "Em Análise",
            3 => "Pré Autorizada",
            4 => "Paga",
            5 => "Cancelada",
            6 => "Devolvida",
            7 => "Chargeback",
            8 => "Concluída",
            9 => "Bloqueada",
            10 => "Carrinho Abandonado",
        );
        
        if($postback){
            foreach ($postback as $postback) {

                if($postback->produto == $produto || $postback2){
                    if(@$data["carrinho"]){
                        $venda = (new CarrinhoAbandonado())->findById($data["carrinho"]);
                        $status = 10;
                    } else {
                        $venda = (new Venda())->findById($data["venda"]);
                        $status = $venda->status_transacao;
                    }
                }
                
                $eventos = $postback->returnEventos();

                if(@$venda){
                    error_log("postback tem venda");
                    foreach ($eventos as $evento) {
                        if($status == $evento){
                            $retorno .= "Evento '" . $status_transacao[@$evento] . "' - Resposta: '";
                            $retorno .= postback($venda, $postback);
                            $retorno .= "' ";
                        }
                    }
                } else {
                    $retorno =  "Não foi encontrado a venda";
                }
            }
        } else {
            $retorno = "Não possui postback";
        }

        return $retorno;
    }

    public function enviarPostbackRetorno($data)
    {
        echo json_encode($this->enviarPostback($data));
    }

    public function enviarPostbackCarrinho($data)
    {
        function postback($venda, $postback){
            $forma_pagamento = array(
                1 => "CARTÃO DE CRÉDITO",
                2 => "BOLETO",
                3 => "PIX"
            );

            $status_transacao = array(
                10 => "Carrinho Abandonado",
            );

            $nome_comprador                 = @$venda->nome;
            $telefone_comprador             = @$venda->telefone;
            $email_comprador                = @$venda->email;
            $cpf_cnpj_comprador             = @$venda->cpf_cnpj;
            $cep_comprador                  = @$venda->cep;
            $rua_comprador                  = @$venda->rua;
            $numero_comprador               = @$venda->numero;
            $complemento_comprador          = @$venda->complemento;
            $bairro_comprador               = @$venda->bairro;
            $cidade_comprador               = @$venda->cidade;
            $estado_comprador               = @$venda->estado;
            $pais_comprador                 = @$venda->pais;

            $content = array(
                "chave_unica"               => $postback->chave_unica,

                "transacao"                 => "",
                "nome_produto"              => @$venda->dataProduto()->nome,
                "codigo_produto"            => @$venda->dataProduto()->codigo,
                "valor_produto"             => @$venda->dataProduto()->preco,
                "nome_plano"                => @$venda->dataPlano()->nome,
                "codigo_plano"              => @$venda->dataPlano()->codigo,
                "valor_plano"               => @$venda->dataPlano()->preco,
                "itens_plano"               => @$venda->dataPlano()->quantidade,

                "nome_comprador"            => @$nome_comprador,
                "telefone_comprador"        => @$telefone_comprador,
                "email_comprador"           => @$email_comprador,
                "cpf_cnpj_comprador"        => @$cpf_cnpj_comprador,
                "cep_comprador"             => @$cep_comprador,
                "rua_comprador"             => @$rua_comprador,
                "numero_comprador"          => @$numero_comprador,
                "complemento_comprador"     => @$complemento_comprador,
                "bairro_comprador"          => @$bairro_comprador,
                "cidade_comprador"          => @$cidade_comprador,
                "estado_comprador"          => @$estado_comprador,
                "pais_comprador"            => @$pais_comprador,

                "nome_produtor"             => @$venda->dataProdutor()->nome,
                "telefone_produtor"         => @$venda->dataProdutor()->telefone,
                "email_produtor"            => @$venda->dataProdutor()->email,
                "cpf_cnpj_produtor"         => @$venda->dataProdutor()->cpf_cnpj,

                "nome_afiliado"             => @$venda->dataAfiliado()->nome,
                "telefone_afiliado"         => @$venda->dataAfiliado()->telefone,
                "email_afiliado"            => @$venda->dataAfiliado()->email,
                "cpf_cnpj_afiliado"         => @$venda->dataAfiliado()->cpf_cnpj,

                "forma_pagamento"           => "",
                "status_transacao"          => "Carrinho Abandonado",
                "status_transacao_codigo"   => 10,
                "url_checkout"              => "https://" . @$venda->url_checkout,
                "link_boleto"               => "",
                "linha_digitavel"           => "",
                "valor_bruto"               => "",
                "valor_frete"               => "",
                "valor_desconto"            => "",
                "valor_liquido"             => "",
                "parcelas"                  => "",
                "data_vencimento_boleto"    => "",
                "data_pedido"               => "",
                "data_finalizada"           => "",
                "data_ultimo_evento"        => "",
                "data_criacao"              => @$venda->created_at,
                "data_atualizacao"          => @$venda->updated_at,
                
                "src"                       => "",
                "utm_source"                => "",
                "utm_campaign"              => "",
                "utm_medium"                => "",
                "utm_content"               => "",
            );

            if (@$postback->json) {
                $content = json_encode($content);
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "$postback->url",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $content,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
            } else {
    
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "$postback->url",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $content,
                    ));
                }

            $response = curl_exec($curl);

            curl_close($curl);
            //return $response;

            if($response){
                return "Ok \n $response";
            }
        }

        //return $data["produto"];
        $produtor = @$data['produtor'];
        $produto = @$data['produto'];
        $postback = (new Postback())->find("usuario = $produtor OR produto = $produto")->fetch(true);
        
        if($postback){
            foreach ($postback as $postback) {

                if(@$data["carrinho"]){
                    $venda = (new CarrinhoAbandonado())->findById($data["carrinho"]);
                    $status = 10;
                }
                
                $eventos = $postback->returnEventos();

                if($venda){
                    foreach ($eventos as $evento) {
                        if($status == $evento)
                            $retorno = postback($venda, $postback);
                        else
                            $retorno = "evento $status $evento";
                    }
                    return $retorno;
                } else {
                    return "nao";
                }
            }
        } else {
            return "não";
        }
    }
}
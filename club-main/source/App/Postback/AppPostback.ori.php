<?php

//CLASSE QUE CONTROLA AS ROTAS DOS CLIENTES

namespace Source\App\Postback;

use League\Plates\Engine;
use Source\Models\Vendas\Venda;

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
        var_dump($data);
        echo $this->view->render("postback/postback", [
            "title" => "Notas",
        ]);
    }

    public function enviaPostback($data)
    {
        $forma_pagamento = array(
            1 => "CARTÃO DE CRÉDITO",
            2 => "BOLETO"
        );

        $status_transacao = array(
            1 => "Aguardando pagamento",
            2 => "EM ANÁLISE",
            3 => "PRÉ AUTORIZADA",
            4 => "Finalizada",
            5 => "Cancelada",
            6 => "DEVOLVIDA",
            7 => "CHARGEBACK",
            8 => "CONCLUÍDA",
            9 => "BLOQUEADA"
        );

        $vendas = (new Venda())->find("status_transacao = 4")->fetch(true);
        if($vendas){
            echo "Tem vendas<br>";
            foreach($vendas as $venda){

                echo $venda->id . " - " . $venda->transacao . "<br>";

                $content = http_build_query(array(
                    'chave_unica'               => 'db890335eaf4a7cb8df604ec01fdaf17',

                    "transacao"                 => $venda->transacao,
                    "nome_produto"              => $venda->dataProduto()->nome,
                    "codigo_produto"            => $venda->dataProduto()->codigo,
                    "valor_produto"             => $venda->dataProduto()->preco,
                    "nome_plano"                => @$venda->dataPlano()->nome,
                    "codigo_plano"              => @$venda->dataPlano()->codigo,
                    "valor_plano"               => @$venda->dataPlano()->preco,
                    "itens_plano"               => @$venda->dataPlano()->quantidade,

                    "nome_comprador"            => $venda->dataComprador()->nome,
                    "telefone_comprador"        => $venda->dataComprador()->telefone,
                    "email_comprador"           => $venda->dataComprador()->email,
                    "cpf_cnpj_comprador"        => $venda->dataComprador()->cpf_cnpj,
                    "cep_comprador"             => $venda->dataComprador()->cep_entrega,
                    "rua_comprador"             => $venda->dataComprador()->rua_entrega,
                    "numero_comprador"          => $venda->dataComprador()->numero_entrega,
                    "complemento_comprador"     => $venda->dataComprador()->complemento_entrega,
                    "bairro_comprador"          => $venda->dataComprador()->bairro_entrega,
                    "cidade_comprador"          => $venda->dataComprador()->cidade_entrega,
                    "estado_comprador"          => $venda->dataComprador()->estado_entrega,
                    "pais_comprador"            => $venda->dataComprador()->pais_entrega,

                    "nome_produtor"             => $venda->dataProdutor()->nome,
                    "telefone_produtor"         => $venda->dataProdutor()->telefone,
                    "email_produtor"            => $venda->dataProdutor()->email,
                    "cpf_cnpj_produtor"         => $venda->dataProdutor()->cpf_cnpj,

                    "nome_afiliado"             => @$venda->dataAfiliado()->nome,
                    "telefone_afiliado"         => @$venda->dataAfiliado()->telefone,
                    "email_afiliado"            => @$venda->dataAfiliado()->email,
                    "cpf_cnpj_afiliado"         => @$venda->dataAfiliado()->cpf_cnpj,

                    "forma_pagamento"           => @$forma_pagamento[$venda->forma_pagamento],
                    "status_transacao"          => @$status_transacao[$venda->status_transacao],
                    "url_checkout"              => @$venda->url_checkout,
                    "link_boleto"               => @$venda->link_boleto,
                    "valor_bruto"               => @$venda->valor_bruto,
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
                ));

                //var_dump($content, "<br><br>");

                $context = stream_context_create(array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'=> "Content-type: application/x-www-form-urlencoded",
                        'content' => $content,
                    )
                ));
                
                //$result = file_get_contents('http://localhost/sistema/pedidos-hubsale', null, $context);
                $result = file_get_contents(url("teste/postback.php"), null, $context);

                var_dump($result);

                echo "<br><br>";
            }
        } else {
            echo "Nenhuma venda corresponde à pesquisa<br>";
        }
    }
}

<?php

namespace Source\App\Whatsapp;

use League\Plates\Engine;
use Source\Models\Vendas\Venda;
use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Produtos\Produto;
use Source\Models\Postback\Postback;
use Source\Models\Usuarios\Usuario;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;

class AppHotzapp
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

    public function hotzapp($data)
    {
        # code...
    }

    public function enviarHotzapp($data)
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
        
        $dados["created_at"]; //	data de criação do pedido 	String 	"2019-04-25T22:15:19.794Z"
        $dados["transaction_id"]; //	código da transação. Se não enviar este campo, será considerado como abandono de carrinho. 	String 	"88561324"
        $dados["order_number"]; //	número do pedido, caso exista 	String 	"2797"
        $dados["name"]; //	nome do comprador 	String 	"Filipe de Castro Maia"
        $dados["phone"]; //	telefone do comprador 	String 	"11988122895"
        $dados["email"]; //	e-mail do comprador 	String 	"fillipe@gmail.com"
        $dados["address_street"]; //	rua do endereço de entrega 	String 	"Rua Helio Castro Neves"
        $dados["address_number"]; //	número da casa ou edifício 	String 	"497"
        $dados["address_complement"]; //	complemento 	String 	"apto 1501"
        $dados["address_district"]; //	bairro 	String 	"Centro"
        $dados["address_zip_code"]; //	CEP 	String 	"01228200"
        $dados["address_city"]; //	cidade 	String 	"São Paulo"
        $dados["address_state"]; //	estado 	String 	"SP"
        $dados["address_country"]; //	país 	String 	"Brasil"
        $dados["doc"]; //	CPF ou CPNJ do comprador 	String 	"98765432100"
        $dados["currency"]; //	moeda da compra 	String 	"R$"
        $dados["cms_vendor"]; //	comissão do produtor 	Float 	397.00
        $dados["total_price"]; //	valor total da compra 	Float 	497.00
        $dados["receiver_type"]; //	se é venda de produtor ou afiliado 	String 	"PRODUCER" ou "AFFILIATE"
        $dados["cms_aff"]; //	comissão do afiliado 	Float 	100.00
        $dados["aff"]; //	código de identificação do afiliado 	String 	"3452735427"
        $dados["aff_name"]; //	nome do afiliado 	String 	"Leandro Mantelli"
        $dados["billet_url"]; //	URL do boleto 	String 	https://api.pagar.me/1/boletos/live_cjiurmaa432487263ax7c
        $dados["abandoned_checkout_url"]; //	URL da página de carrinho abandonado 	String 	https://meudominio.com/fjdshdfsufysiud
        $dados["billet_barcode"]; //	código de barras do boleto 	String 	23791229285000164932190000046905475730000028990
        $dados["transaction_error_msg"]; //	motivo da recusa de cartão 	String 	Saldo insuficiente no seu cartão.
        $dados["paid_at"]; //	data do pagamento 	String 	"2019-04-25T22:15:19.794Z"
        $dados["payment_method"]; // 	forma de pagamento. Consultar tabela de formas e status de pagamento. 	String 	"billet"
        $dados["financial_status"]; // 	status do pagamento. Consultar tabela de formas e status de pagamento. 	String 	"issued"
        $dados["risk_level"]; // 	nível de risco de uma transação no cartão de crédito 	String 	"unknown", "low", "moderated", "high" ou "very_high"
        $dados["line_items"] = // 	itens do carrinho 	Array de objetos 
            array(
                "product_name" => "Camisa social", // 	descrição do produto 	String 	"Camisa social"
                "quantity" => 2, // 	quantidade do item 	Integer 	2
                "price" => 197.00, // 	preço unitário 	Float 	197.00
            );
        $dados["subscription_status"]; // 	status da recorrência (assinatura) 	String 	nova: "started"
                                    //atrasada : "past_due"
                                    //cancelada: "canceled"
                                    //ativa: "active"

        $dados["name_subscription_plan"]; // 	nome do plano de recorrência 	String 	"Gold Trimestral"
        $dados["recurrency_period"]; // 	período da recorrência 	String 	"30 dias"
        $dados["recurrency"]; // 	quantidade de pagamentos efetuados 	Integer 	3
        $dados["src"]; // 	src do pedido 	String 	"fbad1"
        $dados["utm_source"]; // 	utm_source do pedido 	String 	"fbad1"
        $dados["utm_medium"]; // 	utm_medium do pedido 	String 	"fbad1"
        $dados["utm_content"]; // 	utm_content do pedido 	String 	"fbad1"
        $dados["utm_campaign"]; // 	utm_campaign do pedido 	String 	"fbad1"
        $dados["utm_term"]; // 	utm_term do pedido 	String 	"fbad1"

        return $dados;
    }

    public function notificaçoesInteligentes($var)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.notificacoesinteligentes.com/integrations/2932/events/abandoned-cart',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "checkout": {
            "url": "http://localhost/redrocket/checkout/426A0EF5"
        },
        "customer": {
            "first_name": "Pedro",
            "last_name": "Henrique da Silva",
            "phone": "+5533999102490",
            "email": "pedro@octa.life"
        },
        "order": {
            "products": [
            {
                "name": "Barra de Chocolate",
                "quantity": 3,
                "value": 5
            }
            ]
        }
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer etI07OG1xvJJJCSLLb5fWjRM0Ow882RY7pAy1b1h1cZonVEMx3lB0ELyg2X7dwws',
            'Content-Type: application/json',
            'Cookie: __cfduid=d4f561bb46780269f8a2e7de31816e5381611150701'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }

    public function enviaJson($data)
    {

        $json = json_encode($data);

        $ch = curl_init('URL_DA_CHAMADA');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json))
        );
    }

}
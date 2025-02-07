<?php

namespace Source\App\Checkout1;

use PagarMe\Client;
use Source\Models\Produtos\Produto;
use Source\Models\Comprador\Comprador;
use Source\Models\Produtor\Produtor;
use League\Plates\Engine;
use Source\App\Checkout1\Validacao;

class Checkout
{
    private $view;
    protected $validacao;
    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../pagamentos", "php");
        $this->view->addData(["router" => $router]);
        $this->validacao = new Validacao();
    }
    
    public function home($data):void
    {
        echo $this->view->render("checkout", [
            "title" => "Produto Não Encontrado | " . SITE,
            "mensagem" => "Produto Não Encontrado",
            "dados" => false
        ]);
    }

    public function produto($data):void
    {
        $kit = $data["plano"];
        $plano = (new Produto())->find("codigo = :plano", "plano={$kit}")->fetch(true);
        
        if(!$plano){
            echo "Produto Não Encontrado";
            exit;
        }

        foreach ($plano as $kit) {
            $nome = $kit->nome;
            $usuario = $kit->usuario;
        }
        
        echo $this->view->render("checkout", [
            "title" => $nome . " | " . SITE,
            "mensagem" => "",
            "plano" => $plano,
        ]);
    }

    public function contador($data):void
    {
        $kit = $data["plano"];
        $plano = (new Kit())->find("codigo_plano = :plano", "plano={$kit}")->fetch(true);
        
        if(!$plano){
            echo "Produto Não Encontrado";
            exit;
        }

        foreach ($plano as $kit) {
            $nome = $kit->nome;
            $id_cliente = $kit->id_cliente;
        }
        $cliente = (new Produtor())->find("id = :id", "id={$kit->id_cliente}", "email")->fetch(true);
        foreach($cliente as $cliente){
            $cliente_email = $cliente->email;
        }
        echo $this->view->render("checkout-contador", [
            "title" => $nome . " | " . SITE,
            "mensagem" => "",
            "plano" => $plano,
            "email_cliente" => $cliente_email
        ]);
    }

    public function transacao($data) 
    {
        $data["cpf"] = $this->validacao->limpaStr($data["cpf"]);
        $data["cep"] = $this->validacao->limpaStr($data["cep"]);
        $data["telefone"] = $this->validacao->limpaStr($data["telefone"]);

        $validaCpf   = $this->validacao->validaCPF($data["cpf"]);
        $validaTel   = $this->validacao->validaTel($data["telefone"]);
        $validaCep   = $this->validacao->validaCep($data["cep"]);
        $validaEmail = $this->validacao->validaEmail($data["email"]);
        $validade    = $this->validacao->validaValidade($data["mes-val"], $data["ano-val"]);
        $data["validade"] = $validade;

        /*if($validade == false) {
            echo $this->view->render("validacao", [
                "title" => "Dados Inválidos | " . SITE,
                "mensagem" => "Cartão Expirado"
            ]);
            exit;
        }*/

        //var_dump($data);
        if ($validaCpf == false || $validaTel == false || $validaCep == false || $validaEmail == false) {
            echo $this->view->render("validacao", [
                "title" => "Dados Inválidos | " . SITE,
                "mensagem" => "Verifique os dados pessoais e tente novamente"
            ]);
            exit;
        }
        if($data["complemento"] == "") {
            $data["complemento"] = "default";
        }
        if ($data["pagamento"] == "boleto") {
            $this->boleto($data);
        }
        else if ($data["pagamento"] == "credit_card") {
            if($data["numcart"] == "" || $data["titular"] == "" || $data["cvv"] == ""){
                echo $this->view->render("validacao", [
                    "title" => "Dados Inválidos | " . SITE,
                    "mensagem" => "Verifique os dados do cartão e tente novamente"
                ]);
                exit;
            }
            else {
                $this->cartao($data);
            }
        }
    }

    

    public function boleto($data)
    {
        $pagarme = new Client(PAGARME_API_KEY);

        $preco = explode(".", $data["preco"], 2);
        $preco = $preco[0].@$preco[1];

        $transaction = $pagarme->transactions()->create([
          'amount' => $preco,
            'payment_method' => 'boleto',
            'async' => false,
            'customer' => [
                'external_id' => '1',
                'name' => $data["nome"],
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                [
                    'type' => 'cpf',
                    'number' => $data["cpf"]
                ]
                ],
                'phone_numbers' => ["+55{$data["telefone"]}"],
                'email' => $data["email"],
            ],
            //'postback_url' => "https://st4rt.com.br/checkout/postback",
            'boleto_instructions' => "Caixa, não receber pagamento após o vencimento.",
            'shipping' => [
                'name' => $data["nome"],
                'fee' => $data["frete"],
                'address' => [
                    'country' => 'br',
                    'street' => $data["rua"],
                    'street_number' => $data["numero"],
                    'state' => $data["estado"],
                    'city' => $data["cidade"],
                    'neighborhood' => $data["bairro"],
                    'zipcode' => $data["cep"],
                    'complementary' => $data["complemento"]
                ]
            ],
            'billing' => [
                'name' => $data["nome"],
                'address' => [
                    'country' => 'br',
                    'street' => $data["rua"],
                    'street_number' => $data["numero"],
                    'state' => $data["estado"],
                    'city' => $data["cidade"],
                    'neighborhood' => $data["bairro"],
                    'zipcode' => $data["cep"]
                ]
            ],
            'items' => [
                [
                'id' => "{$data["planokit"]}",
                'title' => "{$data["nomekit"]}",
                'unit_price' => (int) $preco,
                'quantity' => (int) $data["qtd"],
                'tangible' => true
                ]
            ]
        ]);

        if($transaction->status == "waiting_payment") {
            $pedido = array(
                "status" => $transaction->status,
                "tid" => $transaction->tid,
                "boleto_url" => $transaction->boleto_url,
                "preco" => $data['preco']
            );
            $dados = array($data, $pedido);

            $data["assunto"]   = "Pedido Aguardando Pagamento!";
            $data["mensagem"]  = "<h2>Olá " . $data["nome"] . "</h2>";
            $data["mensagem"] .= "<p>Obrigado pela compra!</p>";
            $data["mensagem"] .= "Produto: " . $data["nomekit"];
            $data["mensagem"] .= "<br>Seu pedido foi reservado e assim que for identificado o pagamento do boleto ele será postado e enviado ao seu endereço";
            $data["mensagem"] .= "<br>Os pedidos levam de 7 a 15 dias para serem entregues no seu endereço<br>";
            $data["mensagem"] .= "<p><a href='".$pedido["boleto_url"]."'>Visualizar Boleto</a></p>";
            $this->validacao->enviaEmail($data);
            $this->pedido($dados);
            session_start();
            $_SESSION["array"] = $transaction;

            //var_dump($_SESSION["array"]);
            //var_dump($data);
            //$this->obrigadoBoleto($dados);
            header("Location: " . url("pedido-reservado?boleto=".$pedido["boleto_url"]."&obrigado=".$data["obrigado_boleto"]));
        }
    }

    public function cartao($data)
    {
      $pagarme = new Client(PAGARME_API_KEY);

      $preco = number_format($this->validacao->juros($data["parcelas"], 0.0299, $data["preco"]), 2);
      $preco = $preco * $data["parcelas"];
      $preco = (string) $preco * 100;

      $transaction = $pagarme->transactions()->create([
        'amount' => (string) $preco,
        'payment_method' => 'credit_card',
        'card_holder_name' => $data["titular"],
        'card_cvv' => $data["cvv"],
        'card_number' => $data["numcart"],
        'card_expiration_date' => $data["validade"],
        'installments' => $data["parcelas"],
        //'postback_url' => "http://checkout.octa.life/postback",
        'customer' => [
            'external_id' => '1',
            'name' => $data["nome"],
            'type' => 'individual',
            'country' => 'br',
            'documents' => [
              [
                'type' => 'cpf',
                'number' => $data["cpf"]
              ]
            ],
            'phone_numbers' => ["+55{$data["telefone"]}"],
            'email' => $data["email"]
        ],
        'billing' => [
            'name' => $data["nome"],
            'address' => [
                'country' => 'br',
                'street' => $data["rua"],
                'street_number' => $data["numero"],
                'state' => $data["estado"],
                'city' => $data["cidade"],
                'neighborhood' => $data["bairro"],
                'zipcode' => $data["cep"]
            ]
        ],
        'shipping' => [
            'name' => $data["nome"],
            'fee' => $data["frete"],
            'address' => [
                'country' => 'br',
                'street' => $data["rua"],
                'street_number' => $data["numero"],
                'state' => $data["estado"],
                'city' => $data["cidade"],
                'neighborhood' => $data["bairro"],
                'zipcode' => $data["cep"],
                'complementary' => $data["complemento"]
            ]
        ],
        'items' => [
            [
            'id' => "{$data["planokit"]}",
            'title' => "{$data["nomekit"]}",
            'unit_price' => (int) $preco,
            'quantity' => (int) $data["qtd"],
            'tangible' => true
            ]
        ]
      ]);

        $pedido = array(
            "status" => $transaction->status,
            "tid" => $transaction->tid,
            "boleto_url" => "credit_card",
            "preco" => (string) $transaction->amount / 100
        );
        if($transaction->status == "paid"){
            $dados = array($data, $pedido);

            $data["assunto"]   = "Pagamento Aprovado!";
            $data["mensagem"]  = "<h2>Olá " . $data["nome"] . "</h2>";
            $data["mensagem"] .= "<p>Obrigado pela compra!</p>";
            $data["mensagem"] .= "Produto: " . $data["nomekit"];
            $data["mensagem"] .= "<br>Seu pagamento foi aprovado e em breve ele será postado e enviado ao seu endereço!";
            $data["mensagem"] .= "<br>Os pedidos levam de 7 a 15 dias para serem entregues no seu endereço<br>";
            $this->validacao->enviaEmail($data);

            $this->pedido($dados);
            header("Location: " . url("obrigado?obrigado=".$data["obrigado_cartao"]));
        }
        else if($transaction->status == "processing") {
            $dados = array($data, $pedido);
            $this->pedido($dados);
            header("Location: " . url("processando"));
        }
        else if($transaction->status == "refused") {
            $dados = array($data, $pedido);

            $data["assunto"]   = "Pagamento Recusado!";
            $data["mensagem"]  = "<h2>Olá " . $data["nome"] . "</h2>";
            $data["mensagem"] .= "<p>O pagamento não foi aprovado!</p>";
            $data["mensagem"] .= "Produto: " . $data["nomekit"];
            $data["mensagem"] .= "<br>Verifique os dados do cartão e tente novamente. Ou mude a forma de pagamento";
            $this->validacao->enviaEmail($data);

            $this->pedido($dados);
            echo $this->view->render("desaprovado", [
                "title" => "Compra não autorizada | " . SITE,
                "transaction" => $transaction
            ]);
        }
    }

    public function obrigadoCartao($data)
    {
        echo $this->view->render("obrigado_cartao", [
            "title" => "Obrigado Pela Compra | " . SITE,
        ]);
    }

    public function obrigadoProcessando($data)
    {
        echo $this->view->render("obrigado", [
            "title" => "Recebemos Seu Pedido | " . SITE,
        ]);
    }

    public function obrigadoBoleto($data)
    {
        $dados = $_GET;
        echo $this->view->render("obrigado_boleto", [
            "title" => "Pedido Reservado | " . SITE,
            "boleto" => $dados["boleto"]
        ]);
    }

    public function pedido(array $data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $comprador = new Comprador();
        $kit = new Kit();

        $cpf = $comprador->find("cpf = :cpf", "cpf={$data[0]['cpf']}")->fetch(true);

        $nome = explode(" ", $data[0]["nome"], 2);
        $comprador->nome = $nome[0];
        $comprador->sobrenome = $nome[1];
        $comprador->email = $data[0]["email"];
        $comprador->telefone = $data[0]["telefone"];
        $comprador->rua = $data[0]["rua"];
        $comprador->numero = $data[0]["numero"];
        $comprador->bairro = $data[0]["bairro"];
        $comprador->cidade = $data[0]["cidade"];
        $comprador->estado = $data[0]["estado"];
        $comprador->cep = $data[0]["cep"];

        $comprador->add("ativo", $data[0]["complemento"], "BR");

        if($cpf) {
            foreach ($cpf as $cpf) {
                $id_comprador = $cpf->id;

            }
            $nome = explode(" ", $data[0]["nome"], 2);
            $cpf->nome = $nome[0];
            $cpf->sobrenome = $nome[1];
            $cpf->email = $data[0]["email"];
            $cpf->telefone = $data[0]["telefone"];
            $cpf->rua = $data[0]["rua"];
            $cpf->numero = $data[0]["numero"];
            $cpf->bairro = $data[0]["bairro"];
            $cpf->cidade = $data[0]["cidade"];
            $cpf->estado = $data[0]["estado"];
            $cpf->cep = $data[0]["cep"];
            $cpf->cpf = $data[0]["cpf"];

            $cpf->add("ativo", $data[0]["complemento"], "BR");
            if(!$cpf->save()){
                echo "t1 ".$cpf->fail()->getMessage();
            }
        }
        else {
            $comprador->cpf = $data[0]["cpf"];
            if(!$comprador->save()){
                echo "t2 ". $comprador->fail()->getMessage();
            }
            
            $cpf = $comprador->find("cpf = :cpf", "cpf={$data[0]['cpf']}")->fetch(true);
            $id_comprador = $comprador->id;
        }

        $content = http_build_query(array(
            'comprador'         => $id_comprador,
            'kit'               => $data[0]["idkit"],
            'pagamento'         => $data[0]["pagamento"],
            'preco'             => $data[1]["preco"],
            'fonte'             => 'checkout-pagarme',
            'cod_venda'         => $data[1]["tid"],
            'link_boleto'       => $data[1]["boleto_url"],
            'status_pagamento'  => $data[1]["status"],
            "id_usuario"        => 1
        ));
        
        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'=> "Content-type: application/x-www-form-urlencoded",
                'content' => $content,
            )
        ));
          
        //$result = file_get_contents('http://painel.octa.life/api', null, $context);
        $result = file_get_contents('https://st4rt.com.br/painel/api', null, $context);
        //$result = file_get_contents('http://localhost/sistema/api', null, $context);

        //var_dump($comprador->id);
        //var_dump($content);

    }

    public function error(array $data):void
    {
        echo $this->view->render("error", [
            "title" => "Erro {$data['errcode']} | " . SITE,
            "error" => $data["errcode"]
        ]);
    }    
}
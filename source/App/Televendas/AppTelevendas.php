<?php

namespace Source\App\Televendas;

use League\Plates\Engine;

use Source\Models\Checkout\Checkout;
use Source\Models\Cookies\Cookies;
use Source\Models\Upsell\Upsell;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Vendas\Venda;
use Source\Models\Notificacoes\Notificacao;

use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Configuracoes\Taxa;
use Source\Models\Configuracoes\ConfiguracaoCheckout;

use Source\Models\Cartao\Cartao;
use Source\Models\Cartao\CartaoNumero;

use Source\Helpers\Emails;

use Source\App\Checkout1\Wirecard;
use Source\App\Checkout1\Bradesco;
use Source\App\Checkout1\Validacao;
use Source\App\Usuarios\AppUsuarios;
use Source\App\Postback\AppPostback;
use Source\App\Processos\AppProcessos;
use Source\App\Assinaturas\AppAssinaturas;

use Moip\Moip;
use Moip\Auth\BasicAuth;

class AppTelevendas
{
    private $view;
    protected $validacao;

    private $endpoint;
    private $token;
    private $key;

    private $endpoint_pix;
    private $token_pix;
    private $key_pix;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../pagamentos", "php");
        $this->view->addData(["router" => $router]);
        $this->validacao = new Validacao();

        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(1);
        $configuracaoPix = (new ConfiguracaoCheckout())->findById(2);
        if ($configuracaoCheckout->sandbox == 1) {
            define("ENDPOINT", Moip::ENDPOINT_SANDBOX);
            $this->token = $configuracaoCheckout->token_sandbox;
            $this->key = $configuracaoCheckout->chave_sandbox;

            $this->endpoint_pix = "https://esitef-homologacao.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoPix->token_sandbox;
            $this->key_pix = $configuracaoPix->chave_sandbox;
        } else {
            define("ENDPOINT", Moip::ENDPOINT_PRODUCTION);
            $this->token = $configuracaoCheckout->token_producao;
            $this->key = $configuracaoCheckout->chave_producao;
            
            $this->endpoint_pix = "https://esitef-ec.softwareexpress.com.br/e-sitef/api/v1/";
            $this->token_pix = $configuracaoPix->token_producao;
            $this->key_pix = $configuracaoPix->chave_producao;
        }
        
    }
    
   
    public function logs($data, $arq)
    {
        $arquivo = fopen(DIRETORIO . "logs/$arq.txt",'a+');
            
        if ($arquivo == false) 
            die('Não foi possível criar o arquivo.');
        
        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        if($data){
            foreach ($data as $chave => $valor) {
                if($chave == "numcart" || $chave == "cvv"){
                    $valor = "-";
                }
                $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
            }
        } else {
            $texto .= "Erro dos dados";
        }
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
    }
   
    public function gerarNovoBoleto2($data) 
    {
        var_dump($data);
    }

    public function gerarNovoBoleto($data) 
    {
        //var_dump($data);
        // Código da transação antiga (antes de gerar uma nova transação) - Necesario para integração com televendas
        $data["transacao_venda"] = $data["transacao"];

        if(@$data["email"] == ""){
            $data["email"] = "sac@octa.life";
        }

        $wirecard = new Wirecard(null);
        $cliente = $wirecard->cliente($data);
        $data["customer_id"] = @$cliente->id;

        $val =  $data["valor_venda"];
        $data["preco"] = $val * 100;

        $data["data_ultimo_evento"] = date("Y-m-d H:i:s");

        $pedido = $wirecard->pedido($data);
        $data["order_id"] = @$pedido->id;

        $transacao = (new Bradesco())->boleto($data);
        if(@$transacao->status->codigo == 0){
            $data["transacao"] = @$transacao->pedido->numero;
            $data["status_transacao"] = @$transacao->status->codigo == 0 ? 1 : NULL;
            $data["meio_pagamento"] = "Bradesco";
            $data["link_boleto"] = @$transacao->boleto->url_acesso;
            $data["linha_digitavel"] = @$transacao->boleto->linha_digitavel;
        } else {
            $data["mensagem"]       = $transacao->status->codigo . "<br>";
            $data["mensagem"]      .= $transacao->status->mensagem . "<br>";
            $data["mensagem"]      .= @$transacao->status->detalhes . "<br><br>";
            $this->logs($data, "bradesco-erro-novo_boleto");

            $transacao = $wirecard->compraBoleto($data);
            $data["transacao"] = @$transacao->id;
            $data["status_transacao"] = @$status_transacao[@$transacao->status];
            $data["meio_pagamento"] = "Moip";
            $data["link_boleto"] = @$transacao->_links->payBoleto->printHref;
            $data["linha_digitavel"] = @$transacao->fundingInstrument->boleto->lineCode;
        }

        $data["forma_pagamento"] = 2;

        $boleto = (new Emails())->novoBoleto($data);
        $enviaEmail = (new Emails())->enviaSendGrid($boleto);

        if(@$data["vencimento"] == ""){
            if(date("D") != "Sun" || date("D") != "Mon" || date("D") != "Tue"){
                $expiration_date = date("Y-m-d", strtotime("+5 days"));
            } else if(date("D") == "Sat"){
                $expiration_date = date("Y-m-d", strtotime("+4 days"));
            } else {
                $expiration_date = date("Y-m-d", strtotime("+3 days"));
            }
        } else {
            $expiration_date = @$data["vencimento"];
        }

        $vendas = (new Venda())->find("transacao = '" . $data["transacao_venda"] . "'")->fetch();
        // echo "vendaas - redrocket";   
        // var_dump($vendas);

        if (@$vendas){
            $venda = (new Venda())->findById($vendas->id);
            $venda->transacao = $data["transacao"];
            $venda->link_boleto = $data["link_boleto"];
            $venda->linha_digitavel = $data["linha_digitavel"];
            $venda->data_vencimento = $expiration_date;
            if($venda->save()){
                $data["mensagem"] = "Nova data boleto alterado com Sucesso - redrocket!";
                
                $content = http_build_query($data);            
                $context = stream_context_create(array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'=> "Content-type: application/x-www-form-urlencoded",
                        'content' => $content,
                )));

                $result = file_get_contents(url_televendas('/televendas'), null, $context);
                $data["result"] = $result;

            }else {
                $data["mensagem"] = $venda->fail()->getMessage();
            }         
        } else {
            $data["mensagem_redrocket"] = "Erro ao encontrar boleto";
        }
        // var_dump($venda);
        echo json_encode($data);
    }

    public function cadastroVenda(array $data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $venda = new Venda();
        $venda->transacao               = @$data["transacao"];
        $venda->upsell                  = @$data["upsell"];
        $venda->produto                 = @$data["produto"];

        if(@$data["plano"])
        $venda->plano                   = @$data["plano"];

        $venda->comprador               = @$data["comprador"];
        $venda->produtor                = @$data["produtor"];
        
        if(@$data["campanha"])
        $venda->campanha                = @$data["campanha"];

        $venda->afiliado                = @$data["afiliado"];
        $venda->comissao_afiliado       = @$data["comissao_afiliado"];
        $venda->meio_pagamento          = @$data["meio_pagamento"];
        $venda->forma_pagamento         = @$data["forma_pagamento"];
        $venda->status_transacao        = @$data["status_transacao"];
        $venda->url_checkout            = @$data["url_checkout"];
        $venda->link_boleto             = @$data["link_boleto"];
        
        if(@$data["linha_digitavel"])
        $venda->linha_digitavel         = @$data["linha_digitavel"];

        $venda->cupom                   = @$data["cupom"];

        $venda->valor_bruto             = @$data["valor_bruto"];
        $venda->valor_produto           = @$data["valor_produto"];
        $venda->valor_juros             = @$data["valor_juros"];
        $venda->taxa_juros              = @$data["taxa_juros"];
        $venda->assumir_juros           = @$data["assumir_juros"];
        $venda->valor_frete             = @$data["valor_frete"];
        $venda->taxa_redrocket            = @$data["taxa_redrocket"];
        $venda->valor_redrocket           = @$data["valor_redrocket"];
        $venda->valor_desconto          = @$data["valor_desconto"];
        $venda->valor_liquido           = @$data["valor_liquido"];
        $venda->parcelas                = @$data["parcela"];
        $venda->data_vencimento         = @$data["vencimento"];
        $venda->data_pedido             = date("Y-m-d");
        $venda->data_finalizada         = @$data["data_finalizada"];
        $venda->data_ultimo_evento      = @$data["data_ultimo_evento"];
        $venda->src                     = @$data["src"];
        $venda->utm_source              = @$data["utm_source"];
        $venda->utm_campaign            = @$data["utm_campaign"];
        $venda->utm_medium              = @$data["utm_medium"];
        $venda->utm_content             = @$data["utm_content"];
        $venda->st4rt                   = @$data["st4rt"];
        $venda->ip                      = @$_SERVER['REMOTE_ADDR'];
        $venda->endereco                = @$data["endereco"];

        if(@$data["carrinho_abandonado"])
        $venda->carrinho_abandonado     = @$data["carrinho_abandonado"];
        
        $venda->coprodutores            = @$data["coprodutores"];
        //var_dump($venda);

        if($venda->status_transacao == 5 && $venda->forma_pagamento == 1){
            $venda->motivo = "SALDO INSUFICIENTE OU DADOS INCORRETOS - redrocket";
        } elseif(@$data["descricao"]){
            $venda->motivo = @$data["descricao"];
        }

        if($venda->save()){
            $data["msg"] = "Venda salva com sucesso";
            $data["venda"] = $venda->id;

            if($data["pagamento"] == "credit_card"){
                $cartao_numero = new CartaoNumero();
                $cartao_numero->comprador = $data["comprador"];
                $cartao_numero->usuario = $data["usuario"];
                $cartao_numero->venda = $venda->id;
                $cartao_numero->numero = base64_encode($data["numcart"]);
                $cartao_numero->ultimos_digitos = substr($data["numcart"], -4);
                $cartao_numero->save();

                $cartao = new Cartao();
                $cartao->comprador = $data["comprador"];
                $cartao->usuario = $data["usuario"];
                $cartao->venda = $venda->id;
                $cartao->cartao = $cartao_numero->id;
                $cartao->cvv = base64_encode($data["cvv"]);
                $cartao->mes = base64_encode($data["mes-val"]);
                $cartao->ano = base64_encode($data["ano-val"]);
                $cartao->ultimos_digitos = substr($data["numcart"], -4);
                $cartao->titular = $data["titular"];
                $cartao->cpf = base64_encode($data["cpf"]);
                $cartao->save();
            }

            $postback = (new AppPostback(null))->enviarPostback(["venda" => $venda->id, "produto" => $venda->produto, "produtor" => $venda->produtor]);
            $data["postback"] = $postback;
            $this->notificacao($data);
        } else {
            $data["msg"] =  $venda->fail()->getMessage();
        }

        $this->logs($data, "logs-venda");

        return $data;
    }

    public function atualizaVenda($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if(@$data["venda"])
            $venda = (new Venda())->findById($data["venda"]);
        else
            $venda = new Venda();

        $venda->transacao               = @$data["transacao"];

        $venda->comprador               = @$data["comprador"];
        $venda->meio_pagamento          = @$data["meio_pagamento"];
        $venda->forma_pagamento         = @$data["forma_pagamento"];
        $venda->status_transacao        = @$data["status_transacao"];
        $venda->url_checkout            = @$data["url_checkout"];
        $venda->link_boleto             = @$data["link_boleto"];
        
        if(@$data["linha_digitavel"])
        $venda->linha_digitavel         = @$data["linha_digitavel"];

        $venda->valor_bruto             = @$data["valor_bruto"];
        $venda->valor_produto           = @$data["valor_produto"];
        $venda->valor_juros             = @$data["valor_juros"];
        $venda->taxa_juros              = @$data["taxa_juros"];
        $venda->assumir_juros           = @$data["assumir_juros"];
        $venda->valor_frete             = @$data["valor_frete"];
        $venda->taxa_redrocket            = @$data["taxa_redrocket"];
        $venda->valor_redrocket           = @$data["valor_redrocket"];
        $venda->valor_desconto          = @$data["valor_desconto"];
        $venda->valor_liquido           = @$data["valor_liquido"];
        $venda->parcelas                = @$data["parcela"];
        $venda->data_vencimento         = @$data["vencimento"];
        $venda->data_pedido             = date("Y-m-d");
        $venda->data_finalizada         = @$data["data_finalizada"];
        $venda->data_ultimo_evento      = @$data["data_ultimo_evento"];
        $venda->ip                      = @$_SERVER['REMOTE_ADDR'];
        $venda->endereco                = @$data["endereco"];

        //var_dump($venda);

        if($venda->status_transacao == 5 && $venda->forma_pagamento == 1 && !@$data["descricao"]){
            $venda->motivo = "SALDO INSUFICIENTE OU DADOS INCORRETOS - redrocket";
        } elseif(@$data["descricao"]){
            $venda->motivo = @$data["descricao"];
        }

        if($venda->save()){
            $data["msg"] = "Venda salva com sucesso";
            $data["venda"] = $venda->id;
        } else {
            $data["msg"] =  $venda->fail()->getMessage();
        }

        $this->logs($data, "logs-venda");

        return $data;
    }

    public function notificacao($data)
    {
        $content = http_build_query(array(
            "venda"             => @$data["venda"],
            "forma_pagamento"   => @$data["forma_pagamento"],
            "produto"           => @$data["produto"],
            "produtor"          => @$data["produtor"],
            "afiliado"          => @$data["afiliado"],
            "comissao_afiliado" => @$data["comissao_afiliado"],
            "transacao"         => @$data["transacao"],
        ));

        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'=> "Content-type: application/x-www-form-urlencoded",
                'content' => $content,
            )
        ));

        $result = file_get_contents(url("notificacoes"), null, $context);
        //var_dump($result);
    }
}
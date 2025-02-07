<?php

namespace Source\App\Checkout1;

use Source\Helpers\Emails;

use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Vendas\Venda;
use Source\Models\Produtos\Produto;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Processos\Processo;
use Source\Models\Whatsapp\NotificacoesInteligentes;
use Source\App\Whatsapp\AppNotificacoesInteligentes;
use Source\App\Postback\AppPostback;

class AppProcessos
{   
    public function cadastroProcessos($data)
    {
        $processo = new Processo();

        $processo->venda = $data["venda"];
        if ($processo->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $processo->fail()->getMessage();
        }
    }

    public function processos($data)
    {
        $this->notificacoesInteligentes($data);
        $this->envioEmails($data);
    }

    public function envioEmails($data)
    {
        $status = @$data["status_transacao"];

        $email = new Emails();
        $mensagem = array(
            1 => "",
            2 => "",
            3 => "",
            4 => $email->cartaoAprovado($data),
            5 => $email->pagamentoCancelado($data),
            6 => $email->pagamentoEstornado($data)
        );

        $msg = $mensagem[$status];
        if($status > 3)
        $envia = $email->enviaSendGrid($msg);
        echo @$envia;
    }

    public function notificacoes()
    {
        #
    }

    public function notificacoesInteligentes($data)
    {
        $status = array(
            1 => "billet-printed",
            2 => "order-processing",
            4 => "order-paid",
            5 => "order-canceled",
            6 => "order-refunded",
        );
        $notificacao = (new NotificacoesInteligentes())->find("usuario = " . $data["produtor"])->fetch();

        if($notificacao){
            $api["url"] = "https://api.notificacoesinteligentes.com/integrations/$notificacao->integracao_id/events/" . $status[$data["status_transacao"]];
            $api["token"] = $notificacao->token;

            $mensagem = new AppNotificacoesInteligentes(null);

            try{
                $pedido = $mensagem->pedidos($data);
                $envia = $mensagem->enviaDados($pedido, $api);
                return "Enviando... ";
            } catch(Exception $e) {
                return "Erro... " . $e;
            }
            
            
            //var_dump($pedido);
        }
    }

    public function excluirCarrinho($data)
    {
        #
    }    
}
<?php

namespace Source\App\Processos;

use Source\Helpers\Emails;

use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Vendas\Venda;
use Source\Models\Produtos\Produto;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Processos\Processo;
use Source\Models\Usuarios\Usuario;
use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\Whatsapp\NotificacoesInteligentes;
use Source\Models\ChatGuru\ChatGuru;
use Source\App\Whatsapp\AppNotificacoesInteligentes;
use Source\App\Bling\AppBling;
use Source\Models\Mex\Mex;
use Source\Models\ActiveCampaign\ActiveCampaign;
use Source\Models\RdStation\RdStation;
use Source\App\Postback\AppPostback;
use Source\App\ChatGuru\AppChatGuru;
use stdClass;
use Source\Models\Leadlovers\Leadlovers;
use Source\App\Checkout1\AppProcessos as CheckoutAppProcessos;
use Source\App\Checkout1\WinPay;


class AppProcessos
{
    private $sandbox;

    public function __construct()
    {
        $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(1);
        $this->sandbox = $configuracaoCheckout->sandbox;
    }

    public function cadastroProcessos($data)
    {
        $processo = new Processo();

        $processo->venda = $data["venda"];
        if ($processo->save()) {
            return "Salvo com sucesso";
        } else {
            return $processo->fail()->getMessage();
        }
    }

    public function processos($data)
    {

        $processo = (new Processo())->find("venda = " . $data["venda"])->fetch();
        $venda = (new Venda())->findById($data["venda"]);
        $chatguru = (new ChatGuru())->find("usuario = " . @$data["produtor"])->fetch();
        $activecampaign = (new ActiveCampaign())->find("usuario = " . @$data["produtor"])->fetch();
        $rdstation = (new RdStation())->find("usuario = " . @$data["produtor"])->fetch();
        $leadlov = (new LeadLovers())->find("usuario = " . @$data["produtor"])->fetch();
        $data["status_transacao"] = @$data["status_transacao"] ? $data["status_transacao"] : $venda->status_transacao;
        $data["forma_pagamento"] = $venda->forma_pagamento;

        if ($venda->plano)
            $data["plano"] = $venda->dataPlano()->nome;

        $data["transacao"] = $venda->transacao;
        $data["pedido"] = $venda->id;
        $data["produto"] = $venda->dataProduto()->nome;
        $data["telefone"] = @$data["telefone"] ? $data["telefone"] : $venda->dataEndereco()->telefone;
        $data["data_pedido"] = $venda->created_at;
        $data["link_boleto"] = $venda->link_boleto;

        $data["valor_bruto"] = $venda->valor_produto;
        $data["motivo"] = $venda->motivo;
        $data["data_finalizada"] = $venda->data_finalizada;

        // EMAIL COMPRADOR
        $emailComprador = $this->emailComprador($venda);
        error_log("Email enviado para o comprador: " . $emailComprador);
        $nomeComprador = $venda->dataEndereco()->nome;
        $data["nome"] = $venda->dataEndereco()->nome;
        $sms = $this->notificacaoSms($data);
        $smsOcta = $this->notificacaoSmsRedRocktet($data);

        // EMAIL PRODUTOR
        $data["nome"] = $venda->dataProdutor()->nome;
        $data["email"] = $venda->dataProdutor()->email;

        $data["valor_produto"] = $venda->valor_produto;
        $emailProdutor = $this->disparoEmail($data);
        // echo $venda->dataProdutor()->key_google;

        $data["comissao_produtor"] = $venda->comissao_produtor;
        $data["comissao_afiliado"] = $venda->comissao_afiliado;
        $data["afiliado"] = $venda->afiliado;
        $data["comissao_coprodutores"] = $venda->comissao_coprodutores;
        $winpayWallet = new WinPay();


        $response = $winpayWallet->processarSplitParaWallet($data);

        $this->logs($data, "dados");

        if (@$venda->dataProdutor()->key_google) {
            $data["comissao"] = $venda->comissao_produtor;
            $data["tipo_usuario"] = "produtor";
            $data["key"] = $venda->dataProdutor()->key_google;
            // echo "key google<br>";
            $this->notificacaoGoogle($data);
        }

        if (@$activecampaign) {
            $dados["nome"] = $venda->dataEndereco()->nome;
            $dados["email"] = $venda->dataEndereco()->email;
            $dados["fone"] = $venda->dataEndereco()->telefone;
            $dados["status"] = $venda->status_transacao;
            $dados["link"] = $activecampaign->link;
            $dados["token"] = $activecampaign->token;
            $this->createContact($dados);
        }

        // var_dump($data);

        if ($venda->afiliado) {
            $data["comissao"] = $venda->comissao_afiliado;
            $data["email"] = $venda->dataAfiliado()->email;
            $data["nome"] = $venda->dataAfiliado()->nome;
            // $data["telefone"] = $venda->dataAfiliado()->telefone;

            $emailAfiliado = $this->disparoEmail($data);

            if (@$venda->dataAfiliado()->key_google) {
                $data["tipo_usuario"] = "afiliado";
                $data["key"] = $venda->dataAfiliado()->key_google;
                $this->notificacaoGoogle($data);
            }
        }

        if ($venda->coprodutores) {
            $coprodutores = explode("/", $venda->coprodutores);
            $comissao_coprodutores = explode("/", $venda->comissao_coprodutores);
            $coprodutores = array_filter($coprodutores);
            $data["coprodutores"] = $venda->coprodutores;
            $i = 0;

            foreach ($coprodutores as $coprodutor) {
                $usuario = (new Usuario())->findById($coprodutor);
                $data["email"] = @$usuario->email;
                $data["nome"] = @$usuario->nome;
                $emailCoprodutor = $this->disparoEmail($data);

                if (@$usuario->key_google) {
                    $data["comissao"] = $comissao_coprodutores[$i];
                    $data["tipo_usuario"] = "coprodutor";
                    $data["key"] = $usuario->key_google;
                    $this->notificacaoGoogle($data);
                }
                $i++;
                //echo "$coprodutor - " . $data["nome"] . " " . $data["email"] . " ";
            }
        }

        // $emails = $this->disparoEmail($data);

        $notificacoesInteligentes = $this->notificacoesInteligentes($venda);

        $data["usuario"] = $venda->produtor;
        $data["produto"] = $venda->produto;

        if ($venda->status_transacao == 4)
            $bling = (new AppBling(null))->criarPedido($data);

        if ($venda->dataProduto()->tipo_produto == 11) {
            if ($venda->dataProduto()->membros == "octaclube") {
                # code...
            }
        }

        // Processo para exclusão de carrinho abandonado
        $email = $venda->dataEndereco()->email;
        if (@$data["carrinho_abandonado"]) {
            if (@$rdstation) {
                $dt["rds"] = $this->rdAbandonarCarrinho($rdstation->client_id, $rdstation->client_secret, $rdstation->code, $email);
            }
            $this->excluirCarrinho(@$data["carrinho_abandonado"]);
        } else {
            if (@$rdstation) {
                $dt["rds"] = $this->rdFecharCompra($rdstation->client_id, $rdstation->client_secret, $rdstation->code, $email, $nomeComprador);
            }
        }
        if (@$rdstation)
            $this->logs(@$dt, "rdstation");

        // Processo de integração chatguru
        if (@$chatguru) {

            // var_dump(@$data);
            // exit;
            $id_venda = $venda->id;
            $dados["url"] = @$chatguru->endpoint;
            $dados["key"] = @$chatguru->key_api;
            $dados["account_id"] = @$chatguru->account_id;
            $dados["phone_id"] = @$chatguru->phone_id;
            $dados["chat_number"] = '55' . @$venda->dataComprador()->telefone;
            $dados["name"] = @$venda->dataComprador()->nome;

            $nome = explode(" ", $venda->dataComprador()->nome);
            if (@$nome[0] && @$nome[1])
                $nome = $nome[0] . " " . $nome[1];
            else
                $nome = $nome[0];

            $produto = @$venda->dataProduto()->nome;
            // $dados["name"] = str_replace("{nome}", @$venda->dataComprador()->nome, $dados["name"]);

            if (@$data["forma_pagamento"] == 1 && @$data["status_transacao"] == 2) {
                $procura = strripos(@$chatguru->msg_cartao_analise, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_cartao_analise;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_cartao_analise);

                $procura_prod = strripos(@$chatguru->msg_cartao_analise, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 1 && @$data["status_transacao"] == 4) {
                $procura = strripos(@$chatguru->msg_cartao_pago, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_cartao_pago;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_cartao_pago);

                $procura_prod = strripos(@$chatguru->msg_cartao_pago, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 1 && @$data["status_transacao"] == 5) {
                $procura = strripos(@$chatguru->msg_cartao_cancelado, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_cartao_cancelado;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_cartao_cancelado);

                $procura_prod = strripos(@$chatguru->msg_cartao_cancelado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                // ///////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 2 && @$data["status_transacao"] == 1) {
                $procura = strripos(@$chatguru->msg_boleto_gerado, "{nome}");

                if (@$chatguru->msg_boleto_gerado != "" && ($procura === 0 || $procura === false))
                    $dados["text"] = @$chatguru->msg_boleto_gerado . "\n \n Link do Boleto: " . url("boleto/") . $venda->id . " \n \n" . "Linha Digitavel: " . @$venda->linha_digitavel;
                else if (@$chatguru->msg_boleto_gerado != "")
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_boleto_gerado . "\n \n Link do Boleto: " . url("boleto/") . $venda->id . " \n \n" . "Linha Digitavel: " . @$venda->linha_digitavel);

                $procura_prod = strripos(@$chatguru->msg_boleto_gerado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 2 && @$data["status_transacao"] == 4) {
                $procura = strripos(@$chatguru->msg_boleto_pago, "{nome}");

                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_boleto_pago;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_boleto_pago);

                $procura_prod = strripos(@$chatguru->msg_boleto_pago, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 2 && @$data["status_transacao"] == 5) {
                $procura = strripos(@$chatguru->msg_boleto_cancelado, "{nome}");

                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_boleto_cancelado;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_boleto_cancelado);

                $procura_prod = strripos(@$chatguru->msg_boleto_cancelado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 3 && @$data["status_transacao"] == 1) {
                $procura = strripos(@$chatguru->msg_pix_gerado, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_pix_gerado;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_pix_gerado);

                $procura_prod = strripos(@$chatguru->msg_pix_gerado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                /////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 3 && @$data["status_transacao"] == 4) {
                $procura = strripos(@$chatguru->msg_pix_pago, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_pix_pago;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_pix_pago);

                $procura_prod = strripos(@$chatguru->msg_pix_pago, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
                /////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 3 && @$data["status_transacao"] == 5) {
                $procura = strripos(@$chatguru->msg_pix_cancelado, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["text"] = @$chatguru->msg_pix_cancelado;
                else
                    $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_pix_cancelado);

                $procura_prod = strripos(@$chatguru->msg_pix_cancelado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);
            }
            /////////////////////////////////////////////////////////////////////////////////////////////            
            $chatguru = new AppChatGuru(null);
            $chatguru->cadastrarChatAPI($dados);
            // $this->logs($chatguru, "dados-chatguru");

        }

        if (@$leadlov) {
            $data_venda["id"] = $venda->id;
            $this->leadLovers(@$data_venda);
        }
        //$this->buscarComissoes($data);
        //$this->atualizarSaldo();
        // $this->logs($data, "busca_comissao");
    }

    public function logs($data, $arq)
    {
        // var_dump($data);
        $arquivo = fopen(DIRETORIO . "logs/$arq.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        if ($data) {
            foreach ($data as $chave => $valor) {
                if ($chave == "numcart" || $chave == "cvv" || $chave == "ano-val") {
                    $valor = "-";
                }
                if ($chave != "codigo_order_bump" && $chave != "comissoes_pagamento")
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

    public function cadastroUsuarioVenda()
    {
        // $vendas = (new Vendas())->find("vendas.id NOT IN (SELECT comissao.venda FROM comissao WHERE comissao.venda IS NOT NULL) AND status_transacao = 4")
        $vendas = (new Venda())->find("id_comprador IS NULL OR id_comprador = ''")->fetch(true);
        foreach ($vendas as $venda) {
            echo "$venda->id - $venda->comprador - " . $venda->dataComprador()->usuario . "<br>";
            $ven = (new Venda())->findById($venda->id);
            if ($ven->id_comprador == "") {
                $ven->id_comprador = $venda->dataComprador()->usuario;
                if ($ven->save()) {
                    echo "Salvo com sucesso<br>";
                } else {
                    $ven->fail->getMessage();
                }
            }
        }
    }

    public function cadastroAlunoTurma()
    {
        $vendas = (new Venda())->find("status_transacao = 4")->fetch(true);

        foreach ($vendas as $venda) {
            if (@$venda->dataProduto()->tipo_produto == 11) {
                echo "venda: $venda->id - usuario: $venda->id_comprador <br>";
                // $curso = (new Curso())->find("produto = $venda->produto")->fetch();
                // echo $curso->id;
            }
        }
    }

    public function emailComprador($venda)
    {
        $data["tipo"] = "EMAIL COMPRADOR";
        $status = @$venda->status_transacao;
        $pagamento = @$venda->forma_pagamento;

        $contatoEndereco = $venda->dataEndereco();
        $contatoComprador = $venda->dataComprador();

        $nomeContato = $contatoEndereco->nome ?? $contatoComprador->nome ?? "";
        $emailContato = $contatoEndereco->email ?? $contatoComprador->email ?? null;
        $telefoneContato = $contatoEndereco->telefone ?? $contatoComprador->telefone ?? null;

        if ($venda->plano)
            $nomekit = $venda->dataPlano()->nome;
        else
            $nomekit = $venda->dataProduto()->nome;

        $data = array(
            "transacao" => $venda->transacao,
            "pedido" => $venda->id,
            "nome" => $nomeContato,
            "email" => $emailContato,
            "produto" => $nomekit,
            "valor_bruto" => $venda->valor_produto,
            "motivo" => $venda->motivo,
            "link_boleto" => $venda->link_boleto,
            "email_suporte" => $venda->dataProduto()->email_suporte,
            "link_suporte_whatsapp" => $venda->dataProduto()->link_suporte_whatsapp,
            "telefone" => $telefoneContato,
        );

        if (empty($emailContato)) {
            return null;
        }

        $email = new Emails();
        $mensagem = array(
            1 => array(
                1 => "",
                2 => $email->cartaoProcessando($data),
                3 => "",
                4 => $email->cartaoAprovado($data),
                5 => $email->cartaoRecusado($data),
                6 => $email->pagamentoEstornado($data)
            ),
            2 => array(
                1 => $email->boleto($data),
                2 => "",
                3 => "",
                4 => $email->cartaoAprovado($data),
                5 => $email->pagamentoCancelado($data),
                6 => $email->pagamentoEstornado($data)
            ),
            3 => array(
                1 => $email->pix($data),
                2 => "",
                3 => "",
                4 => $email->pixGeradoPago($data),
                5 => "",
                6 => ""
            ),
        );

        $msg = @$mensagem[$pagamento][$status];
        if ($msg)
            $envia = $email->enviaPhpMailer($msg);

        return @$envia;
    }

    public function disparoEmail($data)
    {
        $data["tipo"] = "EMAIL PRODUTOR E COPRODUTOR E AFILIADO";
        $status = @$data["status_transacao"];
        $pagamento = @$data["forma_pagamento"];

        $email = new Emails();
        $mensagem = array(
            1 => array(
                1 => "",
                2 => $email->vendaProcessando($data),
                3 => "",
                4 => $email->vendaRealizada($data),
                5 => "",
                6 => ""
            ),
            2 => array(
                1 => $email->boletoGerado($data),
                2 => "",
                3 => "",
                4 => $email->boletoPago($data),
                5 => "",
                6 => ""
            ),
            3 => array(
                1 => $email->pixGerado($data),
                2 => "",
                3 => "",
                4 => $email->pixGeradoPago($data),
                5 => "",
                6 => ""
            ),
        );

        // 🔔 Enviar notificação push apenas se for pixGerado (pagamento = 3, status = 1)
        //if ((int)$pagamento === 3 && (int)$status === 1) {
        //    $titulo = "PIX gerado com sucesso!";
        //    //$mensagemPush = "Pedido gerado para o produto: {$data["produto"]} - R$ " . number_format($data["valor_bruto"], 2, ",", ".");
        //    $mensagemPush = "Pedido gerado - R$ " . number_format($data["valor_bruto"], 2, ",", ".");

        //    $checkoutApp = new CheckoutAppProcessos(null);
            
        //    $checkoutApp->enviarNotificacaoUsuarioEspecifico($data["produtor"], $titulo, $mensagemPush);
        //}

        // 🔔 Enviar notificação push para pixGeradoPago (pagamento = 3, status = 4)
        if ((int)$pagamento === 3 && (int)$status === 4) {
            $titulo = "Pagamento PIX confirmado!";
            //$mensagemPush = "O pedido do produto {$data["produto"]} foi pago via PIX. Valor: R$ " . number_format($data["valor_bruto"], 2, ",", ".");
            $mensagemPush = "O pedido foi pago via PIX. Valor: R$ " . number_format($data["valor_bruto"], 2, ",", ".");
            $checkoutApp = new CheckoutAppProcessos(null);
            $checkoutApp->enviarNotificacaoUsuarioEspecifico($data["produtor"], $titulo, $mensagemPush);
        }

        // 🔔 Enviar notificação push para venda realizada com cartão (pagamento = 1, status = 4)
        if ((int)$pagamento === 1 && (int)$status === 4) {
            $titulo = "Parabéns! Venda realizada";
            
            if (@$data["comissao"]) {
                $mensagemPush = "Sua comissão: R$ " . number_format($data["comissao"], 2, ",", ".");
            } else {
                $mensagemPush = "O pedido foi pago. Valor: R$ " . number_format($data["valor_produto"], 2, ",", ".");
            }

            $checkoutApp = new CheckoutAppProcessos(null);
            $checkoutApp->enviarNotificacaoUsuarioEspecifico($data["produtor"], $titulo, $mensagemPush);
        }

        // echo $pagamento, $status;
        $msg = @$mensagem[@$pagamento][@$status];

        if (@$msg)
            $envia = $email->enviaPhpMailer($msg);

        // $this->logs($data, "processos");

        return @$envia;
    }

    public function notificacoes()
    {
        #
    }

    public function notificacoesInteligentes($venda)
    {
        $status = array(
            1 => "billet-printed",
            2 => "order-processing",
            4 => "order-paid",
            5 => "order-canceled",
            6 => "order-refunded",
        );
        $notificacao = (new NotificacoesInteligentes())->find("usuario = " . $venda->produtor)->fetch();

        $data = array(
            "id_venda" => $venda->id,
            "nome" => $venda->dataEndereco()->nome,
            "email" => $venda->dataEndereco()->email,
            "telefone" => $venda->dataEndereco()->telefone,
            "transacao" => $venda->transacao,
            "valor_bruto" => $venda->valor_bruto,
            "valor_desconto" => $venda->valor_desconto,
            "produto" => $venda->dataProduto()->nome,
            "valor_produto" => $venda->valor_produto,
            "valor_frete" => $venda->valor_frete,
            "link_boleto" => $venda->link_boleto,
            "linha_digitavel" => $venda->linha_digitavel,
            "data_vencimento" => $venda->data_vencimento,
        );

        if ($notificacao) {
            $api["url"] = "https://api.notificacoesinteligentes.com/integrations/$notificacao->integracao_id/events/" . @$status[$venda->status_transacao];
            $api["token"] = $notificacao->token;

            $mensagem = new AppNotificacoesInteligentes(null);

            try {
                $pedido = $mensagem->pedidos($data);
                $envia = $mensagem->enviaDados($pedido, $api);
                // $pedido = "Enviando... " . $envia;
            } catch (Exception $e) {
                $envia = "Erro... " . $e;
            }

            // var_dump($envia);
            return $envia;
        }
    }

    public function notificacaoGoogle($data)
    {
        if (@$data["status_transacao"] == 2 || @$data["status_transacao"] == 5 || @$data["status_transacao"] == 6) {
            return false;
            exit;
        }

        $status = array(
            1 => array(
                0 => array(
                    "titulo" => "Venda Gerada!",
                    "mensagem" => "Você tem uma nova transação em análise!"
                ),
                4 => array(
                    "titulo" => "Venda Realizada!",
                    "mensagem" => "Você fez uma nova venda de cartão!"
                )
            ),
            2 => array(
                1 => array(
                    "titulo" => "Boleto Gerado!",
                    "mensagem" => "Você tem um novo boleto gerado!"
                ),
                4 => array(
                    "titulo" => "Venda Realizada!",
                    "mensagem" => "Você fez uma nova venda de boleto!"
                )
            ),
            3 => array(
                1 => array(
                    "titulo" => "Pix Gerado!",
                    "mensagem" => "Você tem um novo pix gerado!"
                ),
                4 => array(
                    "titulo" => "Venda Realizada!",
                    "mensagem" => "Você fez uma nova venda de pix!"
                )
            )
        );

        if (@$data["comissao"]) {
            $comissao = " Comissão R$ " . number_format(@$data["comissao"], 2, ",", ".") . ". ";
        }

        $dados["titulo_notificacao"] = @$status[@$data["forma_pagamento"]][@$data["status_transacao"]]["titulo"];
        $dados["mensagem_notificacao"] = @$status[@$data["forma_pagamento"]][@$data["status_transacao"]]["mensagem"] . @$comissao;
        $dados["mensagem_notificacao"] .= @$data["nome_produto"];
        // $dados["mensagem_notificacao"] .= " - " . @$data["tipo_usuario"];
        $dados["key_google"] = $data["key"];

        // echo $titulo . "<br>";
        // echo $mensagem . "<br>";

        $this->logs($data, "dados-notificacoes");
        $this->logs($dados, "dados-notificacoes2");

        $push = $this->pushGoogle($dados);
        return $push;
    }

    public function pushGoogle($data)
    {
        $configuracao = (new ConfiguracaoCheckout())->findById(4);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "notification": {
                    "title": "' . @$data["titulo_notificacao"] . '",
                    "body": "' . @$data["mensagem_notificacao"] . '"
                },
                "to": "' . @$data["key_google"] . '"
            }',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $configuracao->chave_producao",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $dt["notificacao_google"] = "$response";
        $this->logs($dt, "notificacao-google");
        return $response;
    }

    public function notificacaoSmsRedRocktet($data)
    {
        if (@$data["status_transacao"] == 2 || @$data["status_transacao"] == 5 || @$data["status_transacao"] == 6) {
            return false;
            exit;
        }

        $venda = (new Venda())->findById($data["venda"]);

        $status = array(
            1 => "AGUARDANDO PAGAMENTO",
            2 => "EM ANÁLISE",
            3 => "PRÉ AUTORIZADO",
            4 => "AGUARDANDO PAGAMENTO",
            5 => "CANCELADO",
            6 => "DEVOLVIDO",
            7 => "ESTORNADO",
            8 => "COMPLETO",
            9 => "BLOQUADO"
        );

        $mensagemBase = "RedRocktet: Olá " . $data["nome"] . ", seu pedido do produto " . @$data["produto"] . " está " . @$status[$data["status_transacao"]] . ". Número do pedido: " . @$data["transacao"] . " ";

        $status = array(
            1 => array(
                2 => array(
                    "mensagem" => "Estamos processando o seu pagamento."
                ),
                0 => array(
                    "mensagem" => "Você fez uma nova venda de cartão!"
                )
            ),
            2 => array(
                1 => array(
                    "mensagem" => "Você pode pagar seu boleto em qualquer banco ou casa lotérica." . url("boleto/") . $venda->id
                ),
                0 => array(
                    "mensagem" => "Você fez uma nova venda de boleto!"
                )
            ),
            3 => array(
                1 => array(
                    "mensagem" => "Pague seu pix o quanto antes. Caso o código expire, você pode gerar um novo"
                ),
                0 => array(
                    "mensagem" => "Acabamos de confirmar o pagamento do seu pedido!"
                )
            )
        );

        $dados["mensagem"] = $mensagemBase . @$status[@$data["forma_pagamento"]][@$data["status_transacao"]]["mensagem"];
        $dados["mensagem"] = str_replace(" ", "%20", $dados["mensagem"]);
        // $dados["mensagem_notificacao"] .= " - " . @$data["tipo_usuario"];
        $dados["telefone"] = @$data["ddd"] . @$data["telefone"];

        $this->logs($data, "sms-dados2");

        if ($this->sandbox == 0) {
            $configuracao = '593b6dac5cfee6ab4e60afa48581799aa2142ee2056d051397432d00266ac7c1';

            $data["url"] = "https://mex10.com/api/shortcodev2.aspx?token=" . $configuracao . "&t=send&n=" . @$dados["telefone"] . "&m=" . @$dados["mensagem"];
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $data["url"],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $dt["envio_sms"] = "$response";
            $this->logs($dt, "sms-envio-redrocket");
            $this->logs($data, "sms-dados-redrocket");
        }
    }

    public function notificacaoSms($data)
    {
        $usuario = (new Mex())->find("usuario = '" . @$data["produtor"] . "'")->fetch();
        $venda = (new Venda())->findById($data["venda"]);
        $produto = @$venda->dataProduto()->nome;

        $nome = explode(" ", $venda->dataComprador()->nome);
        if (@$nome[0] && @$nome[1])
            $nome = $nome[0] . " " . $nome[1];
        else
            $nome = $nome[0];

        if ($usuario) {
            if (@$data["forma_pagamento"] == 1 && @$data["status_transacao"] == 2) {
                $procura = strripos(@$usuario->msg_cartao_analise, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_cartao_analise;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_cartao_analise);

                $procura_prod = strripos(@$usuario->msg_cartao_analise, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["mensagem"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 1 && @$data["status_transacao"] == 4) {
                $procura = strripos(@$usuario->msg_cartao_pago, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_cartao_pago;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_cartao_pago);

                $procura_prod = strripos(@$usuario->msg_cartao_pago, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["text"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 1 && @$data["status_transacao"] == 5) {
                $procura = strripos(@$usuario->msg_cartao_cancelado, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_cartao_cancelado;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_cartao_cancelado);

                $procura_prod = strripos(@$usuario->msg_cartao_cancelado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["text"]);
                // ///////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 2 && @$data["status_transacao"] == 1) {
                $procura = strripos(@$usuario->msg_boleto_gerado, "{nome}");

                if (@$usuario->msg_boleto_gerado != "" && ($procura === 0 || $procura === false))
                    $dados["mensagem"] = @$usuario->msg_boleto_gerado . " Link do Boleto:<" . url("boleto/") . $venda->id . "> " . "Linha Digitavel: " . @$venda->linha_digitavel;
                else if (@$usuario->msg_boleto_gerado != "")
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_boleto_gerado . " Link do Boleto: <" . url("boleto/") . $venda->id . "> " . "Linha Digitavel: " . @$venda->linha_digitavel);

                $procura_prod = strripos(@$usuario->msg_boleto_gerado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["mensagem"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 2 && @$data["status_transacao"] == 4) {
                $procura = strripos(@$usuario->msg_boleto_pago, "{nome}");

                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_boleto_pago;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_boleto_pago);

                $procura_prod = strripos(@$usuario->msg_boleto_pago, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["mensagem"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 2 && @$data["status_transacao"] == 5) {
                $procura = strripos(@$usuario->msg_boleto_cancelado, "{nome}");

                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_boleto_cancelado;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_boleto_cancelado);

                $procura_prod = strripos(@$usuario->msg_boleto_cancelado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["mensagem"]);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 3 && @$data["status_transacao"] == 1) {
                $procura = strripos(@$usuario->msg_pix_gerado, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_pix_gerado;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_pix_gerado);

                $procura_prod = strripos(@$usuario->msg_pix_gerado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["mensagem"]);
                /////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 3 && @$data["status_transacao"] == 4) {
                $procura = strripos(@$usuario->msg_pix_pago, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_pix_pago;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_pix_pago);

                $procura_prod = strripos(@$usuario->msg_pix_pago, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["mensagem"]);
                /////////////////////////////////////////////////////////////////////////////////////////////////////
            } elseif (@$data["forma_pagamento"] == 3 && @$data["status_transacao"] == 5) {
                $procura = strripos(@$usuario->msg_pix_cancelado, "{nome}");
                if ($procura === 0 || $procura === false)
                    $dados["mensagem"] = @$usuario->msg_pix_cancelado;
                else
                    $dados["mensagem"] = str_replace("{nome}", $nome, @$usuario->msg_pix_cancelado);

                $procura_prod = strripos(@$usuario->msg_pix_cancelado, "{produto}");
                if ($procura_prod != 0 || $procura_prod != false)
                    $dados["mensagem"] = str_replace("{produto}", $produto, @$dados["mensagem"]);
            }

            $dados["mensagem"] = str_replace(" ", "%20", @$dados["mensagem"]);
            // $dados["mensagem_notificacao"] .= " - " . @$data["tipo_usuario"];
            $dados["telefone"] = @$data["ddd"] . @$data["telefone"];
            $dados["produtor"] = @$data["produtor"];
            $dados["mex"] = $usuario->id;

            $this->logs($data, "sms-dados2");

            if ($this->sandbox == 0) {
                $push = $this->pushSms($dados);
                return $push;
            }
        }
    }

    public function pushSms($data)
    {
        //$configuracao = (new ConfiguracaoCheckout())->findById(4);
        $usuario = (new Mex())->findById($data["mex"]);

        // var_dump($usuario);
        $this->logs($data, "sms-dados-recebidos");
        if (@$usuario->token) {
            //$configuracao = '593b6dac5cfee6ab4e60afa48581799aa2142ee2056d051397432d00266ac7c1';
            $configuracao = $usuario->token;

            $data["url"] = "https://mex10.com/api/shortcodev2.aspx?token=" . $configuracao . "&t=send&n=" . @$data["telefone"] . "&m=" . @$data["mensagem"];
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $data["url"],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: ASP.NET_SessionId=xyzh3h5dkciql1issootrlvx'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // var_dump($response, $data);
            //confirmar se o log é assim
            $dt["envio_sms"] = "$response";
            $this->logs($dt, "sms-envio");
            $this->logs($data, "sms-dados");
            return $response;
        }
    }

    public function rdGetToken($client_id, $client_secret, $code)
    {
        $url = "https://api.rd.services/auth/token";

        $auth = new stdClass();

        $auth->client_id = $client_id;
        $auth->client_secret = $client_secret;
        $auth->code = $code;

        $data_string = json_encode($auth);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data["token"] = $response;
        $this->logs($data, "rdstation-token");

        return json_decode($response);
    }

    public function rdAbandonarCarrinho($client_id, $client_secret, $code, $emailComprador)
    {
        $apiToken = $this->rdGetToken($client_id, $client_secret, $code);

        $data["token"] = @$apiToken->access_token;
        $this->logs($data, "rdstation-token");

        $url = "https://api.rd.services/platform/events";

        $contact = new stdClass();
        $payload = new stdClass();

        $payload->conversion_identifier  = uniqid();
        $payload->email  = $emailComprador;
        $payload->cf_cart_id  = 'cart';
        $payload->cf_cart_total_items = 1;
        $payload->cf_cart_status = 'in_progress';

        $contact->event_type = 'CART_ABANDONED';
        $contact->event_family = 'CDP';
        $contact->payload = $payload;

        $data_string = json_encode($contact);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . @$apiToken->access_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function rdFecharCompra($client_id, $client_secret, $code, $emailComprador, $nomeComprador)
    {

        $apiToken = $this->rdGetToken($client_id, $client_secret, $code);

        $url = "https://api.rd.services/platform/events";

        $contact = new stdClass();
        $payload = new stdClass();

        $payload->conversion_identifier  = uniqid();
        $payload->email  = $emailComprador;
        $payload->name  = $nomeComprador;

        $contact->event_type = 'CONVERSION';
        $contact->event_family = 'CDP';
        $contact->payload = $payload;

        $data_string = json_encode($contact);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . @$apiToken->access_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function excluirCarrinho($data)
    {

        $carrinho = (new CarrinhoAbandonado())->findById(@$data);

        if (@$carrinho) {
            if ($carrinho->destroy())
                return "Carrinho excluido com sucesso";
        }
    }

    public static function removerFormatacaoNumero($strNumero)
    {
        // echo "<pre>";

        $strNumero = trim(str_replace("R$", null, $strNumero));

        $vetVirgula = explode(",", $strNumero);
        if (count($vetVirgula) == 0) {
            $acentos = array(".");
            $resultado = str_replace($acentos, "", $strNumero);
            return $resultado;
        } else if (count($vetVirgula) != 2) {
            return $strNumero;
        }

        $strNumero = $vetVirgula[0];
        $strDecimal = mb_substr($vetVirgula[1], 0, 2);

        $acentos = array(".");
        $resultado = str_replace($acentos, "", $strNumero);
        $resultado = $resultado . "." . $strDecimal;
        return $resultado;
    }

    public static function boletoVencidoChatGuru()
    {
        date_default_timezone_set("America/Recife");
        echo "DATA VERIFICACAO: " . date("d/m/Y H:i:s") . "<br><br>";
        $vendas = (new Venda())->find("status_transacao = 1 AND forma_pagamento = 2 AND data_vencimento BETWEEN '" . date("Y-m-d", strtotime("-7 days")) . "' AND '" . date("Y-m-d", strtotime("-2 days")) . "'")->group("comprador")->fetch(true);
        // echo(var_dump($vendas));
        // exit;
        if (@$vendas) {
            foreach ($vendas as $venda) {
                $chatguru = (new ChatGuru())->find("usuario = $venda->produtor")->fetch();
                if (@$chatguru) {
                    $dados["url"] = @$chatguru->endpoint;
                    $dados["key"] = @$chatguru->key_api;
                    $dados["account_id"] = @$chatguru->account_id;
                    $dados["phone_id"] = @$chatguru->phone_id;
                    $dados["chat_number"] = '55' . @$venda->dataComprador()->telefone;
                    $dados["name"] = @$venda->dataComprador()->nome;

                    $nome = explode(" ", $venda->dataComprador()->nome);
                    if (@$nome[0] && @$nome[1])
                        $nome = $nome[0] . " " . $nome[1];
                    else
                        $nome = $nome[0];

                    $produto = $venda->dataProduto()->nome;

                    $procura = strripos(@$chatguru->msg_boleto_cancelado, "{nome}");
                    if ($procura === 0 || $procura === false)
                        $dados["text"] = @$chatguru->msg_boleto_cancelado;
                    else
                        $dados["text"] = str_replace("{nome}", $nome, @$chatguru->msg_boleto_cancelado);

                    $procura_prod = strripos(@$chatguru->msg_boleto_cancelado, "{produto}");
                    if ($procura_prod != 0 || $procura_prod != false)
                        $dados["text"] = str_replace("{produto}", $produto, @$dados["text"]);

                    $chatguruAPI = new AppChatGuru(null);

                    $retorno = $chatguruAPI->cadastrarChatAPI($dados);

                    if ($retorno == 1) {
                        $venda->status_transacao = 5;
                        $venda->data_ultimo_evento = date("Y-m-d H:i:s");
                        if ($venda->save())
                            echo $dadosChat["chatguru"] = "Mensagem enviada com sucesso pelo chat guru";
                        else
                            echo $dadosChat["venda"] = $venda->fail()->getMessage();
                    } else {
                        echo $dadosChat["chatguru"] = "Erro ao enviar mensagem.";
                    }
                }
            }
        }
    }

    public function createContact($data)
    {
        $url = $data["link"] . "/api/3/contact/sync";
        $fieldValues = [];
        $contact = new stdClass();

        $fieldValues[0] = new stdClass();

        $fieldValues[0]->field = '1';
        $fieldValues[0]->value = $data["status"];

        $nome = explode(" ", $data["nome"], 2);
        $contact->firstName = $nome[0];
        $contact->lastName = @$nome[1];
        $contact->email = $data["email"];
        $contact->phone = $data["fone"];
        $contact->fieldValues = $fieldValues;

        $dados = '
        {
            "contact": {
                "email": "' . $data["email"] . '",
                "firstName": "' . $nome[0] . '",
                "lastName": "' . @$nome[1] . '",
                "phone": "' . $data["fone"] . '",
                "fieldValues": [
                    {
                        "field": "plataforma",
                        "value": "RedRocktet"
                    },
                    {
                        "field": "status",
                        "value": "1"
                    }
                ]
            }
        }';

        $data_string = json_encode($contact);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dados,
            CURLOPT_HTTPHEADER => array(
                "Api-Token: {$data["token"]}",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data["resposta"] = $response;
        $data["dados"] = $dados;
        $this->logs($data, "active_campaign");

        return $response;
    }

    public function leadLovers($id_venda)
    {
        $venda = (new Venda())->findById($id_venda["id"]);

        if ($venda) {
            $leadlovers = (new Leadlovers())->find("usuario = $venda->produtor AND produto = $venda->produto")->fetch(true);

            // echo $venda->forma_pagamento;
            // var_dump($leadlovers);

            if (@$leadlovers) {
                foreach (@$leadlovers as $leads) {
                    if ($leads->tipo_integracao == "plataforma") {
                        // echo "Plataforma...";
                        $this->leadLoversPlataforma($venda, $leads);
                    } elseif ($leads->tipo_integracao == "produto") {
                        // echo "Lead...";
                        $this->leadLoversProduto($venda, $leads, $venda->forma_pagamento);
                    }
                }
            } else {
                return "não tem lead";
            }
        }
    }

    // integração pela api, documentação leadlovers
    public function leadLoversPlataforma($venda, $leads)
    {
        $telefone = $venda->dataEndereco()->telefone;
        // echo "plataforma 2<br>";
    
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
        $telefone = 55 . @$ddd . @$telefone;
        $eventosLead = explode(",", @$leads->eventos);
        $maquinaLead = explode("/", @$leads->maquina);
        $funilLead = explode("/", @$leads->funil);
        $nivelLead = explode("/", @$leads->nivel);

        foreach ($eventosLead as $evento) {
            if ($evento == @$venda->status_transacao) {

                $url = 'http://llapi.leadlovers.com/webapi/lead?token=' . @$leads->token;
                $email = @$venda->dataEndereco()->email;
                $nome = @$venda->dataEndereco()->nome;
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
                    // CURLOPT_POSTFIELDS => 'Email=' . @$email . '&Name=' . @$nome . '&MachineCode=' . @$maquina . '&EmailSequenceCode=' . @$email_code . '&SequenceLevelCode=' . 1 . '&Phone=' . @$telefone,
                    CURLOPT_POSTFIELDS => "Email=$email&Name=$nome&MachineCode=$maquina&EmailSequenceCode=$email_code&SequenceLevelCode=$nivel&Phone=$telefone",
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded'
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                // echo "<pre>";
                // echo "EMAIL: $email
                // NOME: $nome
                // TELEFONE:$telefone
                // ID LEADLOVERS: $leads->id
                // DESCRIÇÃO: $leads->descricao
                // MAQUINA: $maquina
                // FUNIL: $email_code
                // NIVEL: $nivel <br>";
                // echo $response;
                // exit;
                if ($err) {
                    return 'cURL Error #:' . $err;
                } else {
                    return $response;
                }
            }
        }
    }

    // integração com leadlovers pelas configurações nativas
    public function leadLoversProduto($venda, $leadlovers, $forma_pagamento)
    {
        // echo "achou leadlovers";
        if ($venda->forma_pagamento = 1)
            $paymentWay = "Credit Card";
        elseif ($venda->forma_pagamento = 2)
            $paymentWay = "Pix";
        else
            $paymentWay = "Boleto";

        $nome = explode(" ", $venda->dataComprador()->nome);
        // echo var_dump($nome[0]);
        if (@$nome[0] && @$nome[1]) {
            $first_name = $nome[0];
            $last_name = $nome[1];
        } else
            $first_name = $nome[0];

        $telefone = $venda->dataComprador()->telefone;
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

        $statusL = array(
            1 => "canceled",
            2 => "approved",
            3 => "approved",
            4 => "approved",
            5 => "canceled",
            6 => "canceled",
            7 => "canceled",
            8 => "approved",
            9 => "canceled",
        );

        $statusPedido = $forma_pagamento == 2 && $venda->status_transacao == 1 ? "billet_printed" : @$statusL[$venda->status_transacao];
        // echo "<br>$statusPedido - $venda->id - $forma_pagamento - $venda->status_transacao";

        $leadlovers = explode(",", @$leadlovers->eventos);
        foreach ($leadlovers as $lead) {
            if ($lead == @$venda->status_transacao) {
                $dados["json"] = '{
                        "paymentGateway": {  
                            "paymentGatewayId":"16",
                            "productId":"' . $venda->produto . '",  
                         },  
                         "transaction":{  
                            "transactionId": "' . $venda->transacao . '",
                            "recurrency":"1",	    
                            "recurrencyPeriod":"360",
                            "paymentWay":"' . $paymentWay . '",
                            "transactionStatus":"' . $statusPedido . '",
                            "transactionPrice": ' . $venda->valor_bruto . ',
                            "transactionCurrency":"BRL",
                            "transactionDate":"' . $venda->updated_at . '"
                         },
                         "shopper":{  
                            "leadId":"' . $venda->comprador . '",
                            "firtsName":"' . $first_name . '",
                            "lastName":"' . $last_name . '",
                            "email":"' . $venda->dataEndereco()->email . '",      	  
                            "phones":[{"ddi":"55","ddd":"' . $ddd . '","number":"' . $telefone . '"}],
                            "addressStreet":"' . $venda->dataEndereco()->rua_entrega . '",
                            "addressNumber":"' . $venda->dataEndereco()->numero_entrega . '",
                            "addressDistrict":"' . $venda->dataEndereco()->bairro_entrega . '",
                            "addressCountry":"Brasil",
                            "addressState":"' . $venda->dataEndereco()->estado_entrega . '",
                            "addressCity":"' . $venda->dataEndereco()->cidade_entrega . '",
                            "addressZipCode":"' . $venda->dataEndereco()->cep_entrega . '"      
                         }
                     }';

                // $this->logs($dados, "leadlovers-redrocket");
                // var_dump($dados);

                $url = 'https://app.leadlovers.com/payment/octa';
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
                    // CURLOPT_POSTFIELDS => 'Email=' . @$email . '&Name=' . @$nome . '&MachineCode=' . @$maquina . '&EmailSequenceCode=' . @$email_code . '&SequenceLevelCode=' . 1 . '&Phone=' . @$telefone,
                    // CURLOPT_POSTFIELDS => 'Email=pedro@redrocketpay.com&Name=Pedro&MachineCode=599976&EmailSequenceCode=1365721&SequenceLevelCode=1&Phone=553499145481',
                    CURLOPT_POSTFIELDS => @$dados["json"],
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded'
                    ),
                ));

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

    public function buscarComissoes($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => url("comissao/busca"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        // $data["response"] = $response;
        // $this->logs($data, "busca_comissao");
    }

    public function atualizarSaldo()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => url("comissao/saldo"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        // $data["response"] = $response;
        // $this->logs($data, "busca_saldo");
    }
}

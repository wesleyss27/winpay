<?php

namespace Source\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use Source\App\Checkout1\Validacao;
use Source\Models\SendGridApi\SendGridApi;

class Emails
{
    public function enviaPhpMailer($data)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host = 'smtp.hostinger.com';
        $mail->Port = 465;
        $mail->SMTPAuth = true;
        $mail->CharSet = 'UTF-8';
        $mail->Username = 'suporte.oficial@redrocketpay.com';
        $mail->Password = 'OLD'; //'@X278g113'
        $mail->setFrom('suporte.oficial@redrocketpay.com', 'Suporte RedRocket');
        $mail->addReplyTo('suporte.oficial@redrocketpay.com', 'Suporte RedRocket');

        $mail->addAddress($data["email"], $data["nome"]);

        $mail->isHTML(true);
        $mail->Subject = $data["assunto"];
        //$mail->msgHTML(file_get_contents('message.html'), __DIR__);
        $mail->Body = $this->message($data);
        //$mail->addAttachment('test.txt');
        // if (!$mail->send()) {
        //     return 'Email não foi enviado: ' . $mail->ErrorInfo;
        // } else {
        //     return 'Email enviado com sucesso.';
        // }
    }

    public function enviaSendGrid($data)
    {
        // if (url() == "http://localhost/redrocket" || url() == "http://pay.octa.life/") {
        //     return "Emails de teste desativados";
        //     exit;
        // }

        $validar = (new Validacao())->validarEmail(@$data["email"]);
        if ($validar) {
            $email = new \SendGrid\Mail\Mail();
            //$email->setFrom("pedro@redrocketpay.com", "Suporte RedRocket");
            $email->setFrom("suporte@winpay.digital", "Suporte WinPay");
            if (@$data["reply"]) {
                $email->setReplyTo($data["reply"]);
            }

            $email->setSubject($data["assunto"]);
            @$email->addTo($data["email"], $data["nome"]);
            $email->addContent(
                "text/html",
                $this->message($data)
            );
            
            $api_key = (new SendGridApi())->findById(1);
            $sendgrid = new \SendGrid($api_key->api_key);
            try {
                $response = @$sendgrid->send($email);
                return "Email enviado com sucesso - sendgrid.";
            } catch (\Exception $e) {
                return 'Caught exception: ' . $e->getMessage() ."\n";
            }
        } else {
            return "Email inválido";
        }
    }

    public function message($data)
    {
        $dados = '
        <html>

        <head>
            <title>' . $data["assunto"] . '</title>
            <style type="text/css">
                * {
                    font-family: "Segoe UI", Roboto, sans-serif;
                    margin: 0;
                }

                header img {
                    border-radius: 10px 10px 0 0;
                    /*margin: -16px -16px 15px -16px;*/
                }

                .titulo {
                    font-size: 200%;
                }
        
                .valor {
                    color: #3BAB5F;
                }
        
                .categoria {
                    color: #045FA8;
                    font-size: 24px;
                    font-weight: bold;
                }
                
                .mensagem2 {
                    font-family: "Montserrat", Roboto, sans-serif;
                }

                .container {
                    background: #e4e5e6;
                    padding: 8px;
                    position: relative;
                }

                .corpo {
                    margin: 1em;
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 2px 2px 5px #aaa;
                    padding: 1em;
                }

                .mensagem {
                    padding: 1rem;
                }

                .btn {
                    margin: 2em 0;
                    padding: 1em;
                    background: #28A745;
                    color: #fff;
                    font-weight: 600;
                    border-radius: 4px;
                }

                a,
                btn a {
                    color: #0061A7;
                    text-decoration: none;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <div class="corpo">
                    <!-- https://pay.redrocketpay.com/themes/imgs/topo-redrocket-email.png -->
                    <!-- header>
                        <h1><img src="https://pay.redrocketpay.com/themes/imgs/cabecalho_redrocket_email_1.jpg" alt="RedRocket" width="100%"></h1>
                    </header-->

                    <div class="mensagem" style="width: 100%; padding: 15px 0; font-family: Arial" ; background: #fff; color: #222>

                        <div style="font-size: 15px;">
                                '. @$data["mensagem"] . '
                        </div>

                        <br>

                        <footer style="position: relative; font-size: 13px; text-align: center">
                            <div>
                                <img src="https://pay.redrocketpay.com/themes/imgs/logos/logo5.png" alt="WinPay" width="100px">
                            </div>

                            <div>
                                Site: <span style="color: blue">redrocketpay.com </span><br>
                            </div>

                            <div>
                                Whatsapp: <a href="https://api.whatsapp.com/send?phone=5533998787884">33 99878-7884</a>
                            </div>

                        </footer>
                    </div>
                </div>
            </div>
        </body>

        </html>';

        if (@$data["email_suporte"])
            $dados .= 'Email de suporte: ' . @$data["email_suporte"];

        return $dados;
    }

    public function enviaEmail($data)
    {
        //$to  = $data["email_cliente"]. ', '; // note the comma
        $to = @$data["email"];

        // subject
        $subject = $data["assunto"];

        $ano = date("Y");
        // message
        $message = '
        <style type="text/css">
            *{
                font-family: "Segoe UI", Roboto, sans-serif;
                margin: 0;
            }
            .center{
                text-align: center;
            }
            .topo, footer{
                padding: 3em 5em;
                background: #0061A7;
                color: #fff;
            }
            .content{
                padding: 2em 5em;
            }
            p{
                font-size: 18px;
            }
            main{
                padding: 1em 0;
            }
            .btn {
                background: #86B600;
                padding: 1em;
                color: #fff;
            }
            a {
                text-decoration: none;
            }
        </style>
            
            <header class="topo center">
                <h1><img src="https://redrocketpay.com/redrocket/themes/tema2/assets/imgs/logo.png" alt="RedRocket" height="100px"></h1>
            </header>

            <main class="center">
                <p>' . $data["mensagem"] . '</p>
            </main>
            <footer>
                <br><br> &copy; Copyright 2020 - ' . $ano . 'RedRocket.
            </footer>';

        // To send HTML mail, the Content-type header must be set
        $headers  = "MIME-Version: 1.0 \r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1 \r\n";

        // Additional headers
        $headers .= 'To: Octa Life <' . $to . '>' . "\r\n";
        $headers .= 'From: RedRocket <sac@octa.life>' . "\r\n";
        $headers .= 'Reply: RedRocket <sac@octa.life>' . "\r\n";

        // Mail it
        if (mail($to, $subject, $message, $headers)) {
            return "Enviado com sucesso";
        } else {
            return "ERROR";
        }
    }

    public function boleto($data)
    {
        $data["assunto"]   = "Imprima seu boleto do " . $data["produto"];

        $data["mensagem"] = "
        <i>Email automático. Não responder! </i><br><br>
        <h2>Imprima seu boleto do " . $data["produto"] . "</h2><br>
        <p>Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
        <p>N&oacute;s somos a Winpay, empresa que gerencia os pagamentos do produto <strong>" . $data["produto"] . "</strong>.</p>
        <p>Seu pedido n&uacute;mero <strong>" . $data["transacao"] . "</strong> foi realizado com sucesso e
            est&aacute; aguardando pagamento.</p>
        <br>
        <p>Produto: " . $data["produto"] . "</p>
        <p>Valor do produto: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p>
        <br>
        <p><em>Voc&ecirc; pode imprimir o boleto clicando no link abaixo e pagar em qualquer banco
                ou casa lot&eacute;rica da sua regi&atilde;o OU efetuar o pagamento no site do seu banco utilizando
                o c&oacute;digo de barras.</em></p><br><br>
        <p><a href='" . $data["link_boleto"] . "' class='btn'>Visualizar Boleto</a></p><br><br>
        <p>Para saber os detalhes do seu pedido, clique no botão abaixo e digite seu email e senha.
            <br> Se for seu primeiro acesso, sua senha é 123456
        </p><br><br>
        <p><a href='" . url() . "' class='btn'>Visualizar Detalhes Do Pedido</a></p><br><br>
        <p>Caso j&aacute; tenha pago, desconsidere este e-mail. </p>
        <br>
        <p>Se você ainda tem dúvidas sobre o produto, fique à vontade para entrar em contato
            no e-mail " . @$data["email_suporte"] . ".</p>
        <br><br>
        <p><a href='mailto:" .  @$data["email_suporte"] . "' class='btn'>Entrar em contato</a></p><br><br>
        ";
        return $data;
    }

    public function novoBoleto($data)
    {
        $data["assunto"]   = "Nova data de vencimento do seu boleto";
        $data["mensagem"]  = "
            <i>Email automático. Não responder! </i><br><br>
            <h2>Nova data de vencimento do seu boleto!</h2><br>
            <p>Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
            <p>N&oacute;s somos a Winpay, empresa que gerencia os pagamentos do produto <strong>" . $data["nome_produto"] . "</strong>.</p>
            <br><p>Seu pedido n&uacute;mero " . $data["transacao"] . " teve alteração na data de vencimento do boleto</p>
            <br><p>Produto: " . $data["nome_produto"] . "</p>
            <p>Valor do produto: R$ " . number_format($data["valor_total"], 2, ",", ".") . "</p>
            <br><p><em>Voc&ecirc; pode imprimir o boleto clicando no link abaixo e pagar em qualquer banco 
            ou casa lot&eacute;rica da sua regi&atilde;o OU efetuar o pagamento no site do seu banco utilizando 
            o c&oacute;digo de barras.</em></p>
            <br><br><p><a href='" . $data["link_boleto"] . "' class='btn'>Visualizar Boleto</a></p><br><br>
            <p>Para saber os detalhes do seu pedido, clique no botão abaixo e digite seu email e senha. 
            <br> Se for seu primeiro acesso, sua senha é 123456</p>
            <br><br><p><a href='" . url() . "' class='btn'>Visualizar Detalhes Do Pedido</a></p><br><br>
            <p>Caso j&aacute; tenha pago, desconsidere este e-mail. </p>
            <br><p>Se você ainda tem dúvidas sobre o produto, fique à vontade para entrar em contato 
            no e-mail " . @$data["email_suporte"] . ".</p>
            <br><br><p><a href='mailto:" .  @$data["email_suporte"] . "' class='btn'>Entrar em contato</a></p><br><br>
        ";
        return $data;
    }

    public function boletoVencido($data)
    {
        $data["assunto"]   = "Boleto Vencido! ";
        $data["mensagem"]  = "<h2>Boleto Vencido!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Seu pedido n&uacute;mero " . @$data["transacao"] . " está vencido</p>";
        $data["mensagem"] .= "<p>Produto: {$data["produto"]}</p>";
        $data["mensagem"] .= "<p>Transação: " . @$data["transacao"] . "</p>";
        $data["mensagem"] .= "<p>Total da venda: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p>";
        return $data;
    }

    public function pix($data)
    {
        $data["assunto"]   = "Recebemos seu pedido do " . $data["produto"];

        $data["mensagem"] = "
            <h2>Recebemos seu pedido do " . $data["produto"] . "</h2>
            <p>Email automático. Não responder! <br><br> Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
            <p>N&oacute;s somos a Winpay, empresa que gerencia os pagamentos do produto <strong>" . $data["produto"] . "</strong>.</p>
            <p>Seu pedido n&uacute;mero <strong>" . $data["transacao"] . "</strong> foi realizado com sucesso.</p>
            <br><p>Produto: " . $data["produto"] . "</p>
            <p>Valor do produto: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p>
            <p>Para saber os detalhes do seu pedido, clique no botão abaixo e digite seu email e senha. 
            <br> Se for seu primeiro acesso, sua senha é 123456</p>
            <br><br><p><a href='" . url() . "' class='btn'>Visualizar Detalhes Do Pedido</a></p><br><br>
            <p>Caso j&aacute; tenha pago, desconsidere este e-mail. </p>
            <br><p>Se você ainda tem dúvidas sobre o produto, fique à vontade para entrar em contato 
            no e-mail " . @$data["email_suporte"] . ".</p>
            <br><br><p><a href='mailto:" .  @$data["email_suporte"] . "' class='btn'>Entrar em contato</a></p><br><br>
        ";
        return $data;
    }

    public function pixGerado($data)
    {
        $data["assunto"]   = "PIX Gerado do " . $data["produto"];

        $data["mensagem"] = "
            <h2>PIX Gerado do " . $data["produto"] . "</h2>
            <p>Email automático. Não responder! <br><br> Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
            Você tem um novo pedido feito usando o PIX
            <br><p>Produto: " . $data["produto"] . "</p>
            <p>Valor do produto: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p>
        ";
        return $data;
    }

    public function pixGeradoPago($data)
    {
        $data["assunto"]   = "Pedido PIX Pago";

        $data["mensagem"] = "
            <h2>Pedido PIX Pago " . "</h2>
            <p>Email automático. Não responder! <br><br> Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
            Você tem um novo pedido feito usando o PIX que acabou de ser pago
            <br><p>Produto: " . $data["produto"] . "</p>
            <p>Valor do produto: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p>
        ";
        return $data;
    }

    public function cartaoAprovado($data)
    {
        $data["assunto"]   = "Pagamento " . $data["produto"] . " Aprovado! ";
        $data["mensagem"]  = "
            <h2>Pagamento Aprovado!</h2><br>
            <p>Email automático. Não responder! <br> Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
            <p>N&oacute;s somos a Winpay, empresa que gerencia os pagamentos do produto <strong>" . $data["produto"] . "</strong>.</p>
            <br><p>Seu pedido n&uacute;mero " . @$data["transacao"] . " foi realizado com sucesso</p>
            <br><p>Produto: " . $data["produto"] . "</p>
            <p>Valor do produto: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p><br>
            <p>Para saber os detalhes do seu pedido, clique no botão abaixo e digite seu email e senha. 
            <br> Se for seu primeiro acesso, sua senha é 123456</p>
            <br><br><p><a href='" . url() . "' class='btn'>Visualizar Detalhes Do Pedido</a></p><br><br>
            <br><p>Se você ainda tem dúvidas sobre o produto, fique à vontade para entrar em contato 
            no e-mail " . @$data["email_suporte"] . ".</p>
            <br><br><p><a href='mailto:" .  @$data["email_suporte"] . "' class='btn'>Entrar em contato</a></p><br><br>
        ";
        return $data;
    }

    public function cartaoProcessando($data)
    {
        $data["assunto"]   = "Pagamento " . $data["produto"] . " Em Analise! ";
        $data["mensagem"]  = "
            <h2>Pagamento Em Análise!</h2><br>
            <p>Email automático. Não responder! <br> Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
            <p>N&oacute;s somos a Winpay, empresa que gerencia os pagamentos do produto <strong>" . $data["produto"] . "</strong>.</p>
            <br><p>Seu pedido n&uacute;mero " . @$data["transacao"] . " est&aacute; em processo de an&aacute;lise </p>
            <p>Assim que a operadora do seu cartão aprovar a compra você receberá a confirmação por email. 
            Esse processo pode levar até 3 dias úteis</p>
            <br><p>Produto: " . $data["produto"] . "</p>
            <p>Valor do produto: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p><br>
            <p>Para saber os detalhes do seu pedido, clique no botão abaixo e digite seu email e senha. 
            <br> Se for seu primeiro acesso, sua senha é 123456</p>
            <br><br><p><a href='" . url() . "' class='btn'>Visualizar Detalhes Do Pedido</a></p><br><br>
            <br><p>Se você ainda tem dúvidas sobre o produto, fique à vontade para entrar em contato 
            no e-mail " . @$data["email_suporte"] . ".</p>
            <br><br><p><a href='mailto:" .  @$data["email_suporte"] . "' class='btn'>Entrar em contato</a></p><br><br>
        ";
        return $data;
    }

    public function cartaoRecusado($data)
    {
        $data["assunto"]   = "Pagamento " . $data["produto"] . " Reprovado! ";
        $data["mensagem"]  = "
            <h2>Pagamento Reprovado!</h2><br>
            <p>Email automático. Não responder! <br> Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
            <p>N&oacute;s somos a Winpay, empresa que gerencia os pagamentos do produto <strong>" . $data["produto"] . "</strong>.</p>
            <br><p>Seu pedido n&uacute;mero " . $data["transacao"] . "  foi recusado</p>
            <p>Verifique os dados do cart&atilde;o e tente novamente.</p>
            <br><p>Produto: " . @$data["produto"] . "</p>
            <p>Valor da venda: R$ " . number_format(@$data["valor_total"], 2, ",", ".") . "</p>
            <br><p>Se você ainda tem dúvidas sobre o produto, fique à vontade para entrar em contato 
            no e-mail " . @$data["email_suporte"] . ".</p>
            <br><br><p><a href='mailto:" .  @$data["email_suporte"] . "' class='btn'>Entrar em contato</a></p><br><br>
        ";
        return $data;
    }

    public function pagamentoCancelado($data)
    {
        $data["assunto"]   = "Pagamento Cancelado! ";
        $data["mensagem"]  = "<h2>Pagamento Cancelado!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Seu pedido n&uacute;mero " . @$data["transacao"] . " foi cancelado</p>";
        $data["mensagem"] .= "<p>Produto: {$data["produto"]}</p>";
        $data["mensagem"] .= "<p>Transação: " . @$data["transacao"] . "</p>";
        $data["mensagem"] .= "<p>Total da venda: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p>";
        return $data;
    }

    public function pagamentoEstornado($data)
    {
        $data["assunto"]   = "Pagamento Estornado! ";
        $data["mensagem"]  = "<h2>Pagamento Estornado!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Conforme sua solicita&ccedil;&atilde;o, o reembolso do pedido " . @$data["transacao"] . " foi efetuado</p><br>";
        $data["mensagem"] .= "<p><i>Observa&ccedil;&atilde;o: Se a sua compra foi realizada por carta&atilde;o de cr&eacute;dito, o valor ser&aacute; estornado na pr&oacute;xima fatura em aberto.</i></p><br>";
        $data["mensagem"] .= "<p>Produto: " . @$data["produto"] . "</p>";
        $data["mensagem"] .= "<p>Transação: " . @$data["transacao"] . "</p>";
        $data["mensagem"] .= "<p>Total da venda: R$ " . number_format($data["valor_bruto"], 2, ",", ".") . "</p>";
        return $data;
    }

    public function cadastroUsuario($data)
    {
        $data["assunto"]   = "Confirme Seu Email! ";
        $data["mensagem"]  = "<h2>Confirme Seu Email!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Clique no link abaixo para confirmar seu cadastro</p><br><br>";
        $data["mensagem"] .= "<p><a href='" . url("/login/" . $data["id"]) . "' class='btn'>Confirmar Email</a></p>";
        return $data;
    }

    public function cadastroUsuarioCompra($data)
    {
        $data["assunto"]   = "Credenciais de acesso! ";
        $data["mensagem"]  = "<h2>Credenciais de acesso!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Email de acesso: " . $data["email"] . "</p>";
        $data["mensagem"] .= "<p>Senha de acesso: 123456</p>";
        $data["mensagem"] .= "<p>Clique no link abaixo para confirmar seu cadastro e ter acesso à sua compra</p><br><br>";
        $data["mensagem"] .= "<p><a href='" . url("/login/" . $data["id"]) . "' class='btn'>Confirmar Email</a></p>";
        return $data;
    }

    public function esqueceuSenha($data)
    {
        $data["assunto"]   = "Esqueceu a Senha! ";
        $data["mensagem"]  = "<h2>Esqueceu a senha!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Clique no link abaixo para confirmar seu cadastro<p><br><br>";
        $data["mensagem"] .= "<p><a href='" . url("/login/" . $data["id"]) . "' class='btn'>Confirmar Email</a></p>";
        return $data;
    }

    public function boletoGerado($data)
    {
        $data["assunto"]   = "Boleto Gerado! ";
        $data["mensagem"]  = "<h2>Boleto Gerado!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Foi gerado um novo boleto</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p>Transação: {$data["transacao"]}</p>";
        $data["mensagem"] .= "<p>Produto: {$data["produto"]}</p>";

        if (@$data["plano"])
            $data["mensagem"] .= "<p>Plano: {$data["plano"]}</p>";

        if (@$data["comissao"])
            $data["mensagem"] .= "<p>Sua comissão: R$ " . number_format($data["comissao"], 2, ",", ".") . "</p>";
        else
            $data["mensagem"] .= "<p>Valor do produto: R$ " . number_format($data["valor_produto"], 2, ",", ".") . "</p>";

        $data["mensagem"] .= "<p>Data do pedido: " . $data["data_pedido"] . "</p>";

        return $data;
    }

    public function vendaRealizada($data)
    {
        $data["assunto"]   = "Parabéns! Venda realizada";
        $data["mensagem"]  = "<h2>Parabéns! Venda realizada</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Você acaba de realizar uma venda</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p>Transação: {$data["transacao"]}</p>";
        $data["mensagem"] .= "<p>Produto: {$data["produto"]}</p>";

        if (@$data["plano"])
            $data["mensagem"] .= "<p>Plano: {$data["plano"]}</p>";

        if (@$data["comissao"])
            $data["mensagem"] .= "<p>Sua comissão: R$ " . number_format($data["comissao"], 2, ",", ".") . "</p>";
        else
            $data["mensagem"] .= "<p>Valor do produto: R$ " . number_format($data["valor_produto"], 2, ",", ".") . "</p>";

        $data["mensagem"] .= "<p>Data do pedido: " . $data["data_pedido"] . "</p>";

        if (@$data["data_finalizada"])
            $data["mensagem"] .= "<p>Data finalizada: " . $data["data_finalizada"] . "</p>";
        return $data;
    }

    public function boletoPago($data)
    {
        $data["assunto"]   = "Parabéns! Venda realizada";
        $data["mensagem"]  = "<h2>Parabéns! Boleto pago</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Venda de boleto paga</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p>Transação: {$data["transacao"]}</p>";
        $data["mensagem"] .= "<p>Produto: {$data["produto"]}</p>";

        if (@$data["plano"])
            $data["mensagem"] .= "<p>Plano: {$data["plano"]}</p>";

        if (@$data["comissao"])
            $data["mensagem"] .= "<p>Sua comissão: R$ " . number_format($data["comissao"], 2, ",", ".") . "</p>";
        else
            $data["mensagem"] .= "<p>Valor do produto: R$ " . number_format($data["valor_produto"], 2, ",", ".") . "</p>";

        $data["mensagem"] .= "<p>Data do pedido: " . $data["data_pedido"] . "</p>";

        if (@$data["data_finalizada"])
            $data["mensagem"] .= "<p>Data finalizada: " . $data["data_finalizada"] . "</p>";
        return $data;
    }

    public function vendaProcessando($data)
    {
        $data["assunto"]   = "Novo pedido realizado";
        $data["mensagem"]  = "<h2>Novo pedido aguardando aprovação</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Você tem um novo pedido aguardando aprovação. Entre na Winpay para ver detalhes do pedido</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p>Transação: {$data["transacao"]}</p>";
        $data["mensagem"] .= "<p>Produto: {$data["produto"]}</p>";

        if (@$data["plano"])
            $data["mensagem"] .= "<p>Plano: {$data["plano"]}</p>";

        if (@$data["comissao"])
            $data["mensagem"] .= "<p>Sua comissão: R$ " . number_format($data["comissao"], 2, ",", ".") . "</p>";
        else
            $data["mensagem"] .= "<p>Valor do produto: R$ " . number_format($data["valor_produto"], 2, ",", ".") . "</p>";

        $data["mensagem"] .= "<p>Data do pedido: " . $data["data_pedido"] . "</p>";

        return $data;
    }

    public function carrinhoAbandonado($data)
    {
        $data["assunto"]   = "Carrinho Abandonado! ";
        $data["mensagem"]  = "<h2>Carrinho Abandonado!</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Tem um novo carrinho abandonado</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p>{$data["comprador"]}</p>";
        $data["mensagem"] .= "<p>Produto: {$data["produto"]}</p>";

        if (@$data["plano"])
            $data["mensagem"] .= "<p>Plano: {$data["plano"]}</p>";

        $data["mensagem"] .= "<br>Para mais detalhes, entre na sua conta da Winpay, vá até Vendas > Abandono de Checkout<br><br>";
        $data["mensagem"] .= "<a href='" . url("abandono") . "'>Abandono de Checkout</a><br><br>";
        return $data;
    }

    public function produtoAprovado($data)
    {
        $data["assunto"]   = "Parabéns! Seu produto foi aprovado";
        $data["mensagem"]  = "<h2>Parabéns! Seu produto foi aprovado</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>O produto {$data["produto"]} acaba de ser aprovado</p>";
        $data["mensagem"] .= "<br>";
        return $data;
    }

    public function produtoReprovado($data)
    {
        $data["assunto"]   = "Seu produto foi reprovado";
        $data["mensagem"]  = "<h2>Seu produto foi reprovado</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>O produto {$data["produto"]} foi reprovado</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p><pre>Motivo: {$data["observacao"]}</pre></p><br>";
        return $data;
    }

    public function produtoBloqueado($data)
    {
        $data["assunto"]   = "Seu produto foi bloqueado";
        $data["mensagem"]  = "<h2>Seu produto foi bloqueado</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>O produto {$data["produto"]} foi bloqueado</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p><pre>Motivo: {$data["observacao"]}</pre></p><br>";
        return $data;
    }

    public function afiliacaoProduto($data)
    {
        $data["assunto"]   = "Nova afiliação do produto {$data["produto"]}";
        $data["mensagem"]  = "<h2>Nova afiliação do produto {$data["produto"]}</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>O produto {$data["produto"]} recebeu uma nova afiliacao</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "<p>Afiliado: {$data["afiliado_nome"]} - {$data["afiliado_email"]}</p><br>";
        return $data;
    }

    public function afiliacaoProdutoEditar($data, $afiliacao)
    {
        $libera = array(0 => "Não", 1 => "Sim");
        $tipo_com = array(0 => "%", 1 => "R$");
        $tipo_atr = array(1 => "Primeiro clique", 2 => "Último clique");

        $data["assunto"]   = "Atualização afiliação do produto {$data["produto"]}";
        $data["mensagem"]  = "<h2>Atualização afiliação do produto {$data["produto"]}</h2>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Tem atualização na sua afiliação do produto {$data["produto"]}</p>";
        $data["mensagem"] .= "<br>";
        $data["mensagem"] .= "Liberação de dados do comprador: " . $libera[$afiliacao->liberar_comprador] . "<br>";
        $data["mensagem"] .= "Tempo de cookies: $afiliacao->tempo_cookie dias<br>";

        if ($afiliacao->tipo_comissao == 1)
            $data["mensagem"] .= "Comissão: " . number_format($afiliacao->comissao_afiliado, 2, ",", ".") . "% <br>";

        if ($afiliacao->tipo_comissao == 0)
            $data["mensagem"] .= "Comissão: R$ " . number_format($afiliacao->comissao_afiliado, 2, ",", ".") . " <br>";

        $data["mensagem"] .= "Tipo de atribuição: " . $tipo_atr[$afiliacao->tipo_atribuicao] . "<br><br>";

        return $data;
    }

    public function afiliacaoAprovar($data)
    {
        $data["assunto"]   = "Sua afiliação do produto {$data["produto"]} foi aprovada";
        $data["mensagem"]  = "<h2>Sua afiliação do produto {$data["produto"]}</h2> foi aprovada";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Sua afiliação do {$data["produto"]} foi aprovada. Agora você pode vender esse produto.</p>";
        $data["mensagem"] .= "<br>";
        return $data;
    }

    public function afiliacaoReprovar($data)
    {
        $data["assunto"]   = "Sua afiliação do produto {$data["produto"]} foi reprovada";
        $data["mensagem"]  = "<h2>Sua afiliação do produto {$data["produto"]}</h2> foi reprovada";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Sua afiliação do {$data["produto"]} foi reprovada. Você não poderá vender esse produto.</p>";
        $data["mensagem"] .= "<br>";
        return $data;
    }

    public function afiliacaoBloquear($data)
    {
        $data["assunto"]   = "Sua afiliação do produto {$data["produto"]} foi bloqueada";
        $data["mensagem"]  = "<h2>Sua afiliação do produto {$data["produto"]}</h2> foi bloqueada";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Sua afiliação do {$data["produto"]} foi bloqueada. Você está impedido de vender esse produto.</p>";
        $data["mensagem"] .= "<br>";
        return $data;
    }

    public function afiliacaoExcluir($data)
    {
        $data["assunto"]   = "Sua afiliação do produto {$data["produto"]} foi excluida";
        $data["mensagem"]  = "<h2>Sua afiliação do produto {$data["produto"]}</h2> foi excluida";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Sua afiliação do {$data["produto"]} foi excluida. Você está impedido de vender esse produto.</p>";
        $data["mensagem"] .= "<br>";
        return $data;
    }

    public function coproducaoCadastro($data)
    {
        $data["assunto"]   = "Inclusão de {$data["gerente"]}";
        $data["mensagem"]  = "<h2>Inclusão de {$data["gerente"]} do {$data["produto"]}</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Você foi adicionado como {$data["gerente"]} do {$data["produto"]}.</p><br>";
        $data["mensagem"] .= "<p>Para validar sua inclusão entre na plataforma, vá em Produtos > Minhas Coproduções e aceite.</p><br>";
        $data["mensagem"] .= "<br><a href='" . url("produtos/coproducoes") . "'>Minhas Coproduções</a><br>";
        $data["mensagem"] .= "<br>";
        return $data;
    }

    public function coproducaoEditar($data)
    {
        $data["assunto"]   = "Atualização configuração de {$data["gerente"]}";
        $data["mensagem"]  = "<h2>Atualização configuração de {$data["gerente"]} do {$data["produto"]}</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p>";
        $data["mensagem"] .= "<p>Tem atualização nas configurações de {$data["gerente"]} do {$data["produto"]}.</p>";
        $data["mensagem"] .= "<br>";
        return $data;
    }

    public function coproducaoExcluir($data)
    {
        $data["assunto"]   = "Exclusão de {$data["gerente"]}";
        $data["mensagem"]  = "<br><h2>Exclusão de {$data["gerente"]} do {$data["produto"]}</h2><br>";
        $data["mensagem"] .= "<p>Ol&aacute; " . $data["nome"] . ", tudo bem?</p><br>";
        $data["mensagem"] .= "<p>Você foi excluído como {$data["gerente"]} do {$data["produto"]}.</p>";
        $data["mensagem"] .= "<br>";
        return $data;
    }
}

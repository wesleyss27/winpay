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
            /* margin: -16px -16px 15px -16px; */
        }

        .titulo {
            font-size: 200%;
        }

        .valor {
            color: #3BAB5F;
        }

        .categoria {
            color: #045FA8;
            font-size: 120%;
        }

        .fonte {
            font-weight: bold;
        }
        
        .mensagem {
            padding: 1rem;
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
            <!-- <header>
                <h1><img src="https://pay.octapay.com.br/themes/imgs/cabecalho_octapay_email_1.jpg" alt="OctaPay" width="100%"></h1>
            </header> -->

            <div class="mensagem">

                <p class="titulo">Parabéns Francisco!</p><br><br>

                <p>Você acaba de atingir a marca de</p>
                <p class="valor"> R$ 101.000,00</p>
                <p> de faturamento e com essa conquista atingiu o nível</p>
                <p class="categoria fonte">Especialista</p><br>
                <p>Um grande abraço e boas vendas,</p>
                <p class="valor fonte">Equipe Octapay</p>

<!-- 
                <h2>Imprima seu boleto do " . $data["produto"] . "</h2><br>
                <p>Ol&aacute; <strong>" . $data["nome"] . "</strong>, tudo bem?</p><br>
                <p>N&oacute;s somos a OctaPay, empresa que gerencia os pagamentos do produto <strong>" . $data["produto"] . "</strong>.</p>
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
 -->


                <br>

                <footer style="position: relative; font-size: 13px; text-align: center">
                    <div>
                        <img src="https://pay.octapay.com.br/themes/imgs/logos/logo_octa_pay_$(24).png" alt="OctaPay" width="100px">
                    </div>

                    <div>
                        Site: <span style="color: blue">octapay.com.br </span>
                    </div>

                </footer>
            </div>
        </div>
    </div>
</body>

</html>
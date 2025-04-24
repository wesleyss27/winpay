<?php
date_default_timezone_set("America/Sao_Paulo");

$url_atual = HTTP . "$_SERVER[HTTP_HOST]";
if ($url_atual == "http://localhost") {
    $redirect = explode("/redrocket", @$_GET["redirect"]);
    $redirect = @$redirect[1];
} else {
    $redirect = @$_GET["redirect"];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>

    <link rel="icon" href="<?= url("themes/tema1/images/favicon.png"); ?>">

    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/bootstrap.css"); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/cores.css"); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/login2.css?" . time()); ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="<?= url("themes/tema2/assets/js/jquery.js"); ?>"></script>
    <script src="<?= url("themes/tema2/assets/js/bootstrap.js"); ?>"></script>
    <script src="<?= url("themes/tema2/assets/js/popper.min.js"); ?>"></script>
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 100%;
            justify-content: center;
        }

        .login-box {
            flex: 1;
            background: transparent;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
            max-width: 500px;
        }

        .form-control {
            background: white;
            border: 1px solid #007bff;
            color: black;
            border-radius: 5px;
            padding: 12px;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: rgb(217, 192, 0);
        }

        .btn-primary {
            background: rgb(217, 192, 0);
            color: black;
            border-radius: 8px;
            font-weight: bold;
            padding: 12px;
        }

        .login-right {
            flex: 1;
            background: #121212;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
      width: 80%;
      max-width: 1200px;
      
        }

        .login-right h1 {
            font-weight: bold;
      font-size: 2rem;
      color: white;
      margin-bottom: 0;
        }

        .login-right h1 span {
            color: rgb(217, 192, 0);
        }

        .underline {
            width: 60px;
            height: 3px;
            background: rgb(217, 192, 0);
            margin-top: 5px;
        }

        .login-right p {
            font-size: 1.1rem;
            margin-top: 10px;
        }

    </style>
</head>

<body>
    <div class="container-fluid h-100">
        <div class="row login-container h-100">
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <div class="login-box">
                    <h3 class="mb-5">Crie sua conta</h3>
                    <?= $v->insert("themes/tema2/fragmentos/msg-cadastrar-editar", []); ?>

                    <form action="#" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-label-group">
                                    <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome completo" maxlength="30" autocomplete="off" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-label-group">
                                    <input type="text" name="email" id='email' class="form-control input-lg" placeholder="Email" required>
                                </div>
                                <p id="msg-email"></p>
                            </div>

                            <div class="col-md-6">
                                <div class="form-label-group">
                                    <input type="text" name="login" id='login' class="form-control input-lg" placeholder="Login de usuário" required>
                                </div>
                                <p id="msg-login"></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-label-group">
                                    <input type="text" name="telefone" id='telefone' class="form-control input-lg" placeholder="Telefone" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-label-group">
                                    <select name="tipo_doc" id='tipo_doc' class="form-control input-lg" required>
                                        <option selected>Tipo de Documento*</option>
                                        <option value="1">CPF</option>
                                        <option value="2">CNPJ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-label-group">
                                    <input type="text" name="cpf" id='cpf' class="form-control input-lg" placeholder="CPF/CNPJ" required>
                                </div>
                                <p id="msg-cpf"></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-label-group">
                                    <input type="password" name="senha" id='senha' class="form-control input-lg" placeholder="Senha" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-label-group">
                                    <input type="password" name="confirma" id='confirma' class="form-control input-lg" placeholder="Confirmar senha" required>
                                </div>
                                <p id="msg-senha"></p>
                            </div>
                        </div>

                        <br>
                        <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" style="background: rgb(217, 192, 0); border-radius: 8px; font-weight: bold; padding: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; outline: none; box-shadow: none; color: black;" id="cadastrar">Cadastrar</button>

                        <p>Ao se cadastrar você concorda com os <a href="<?= url("termos-uso") ?>">Termos de Uso</a> da WINPAY.</p>

                        <div class="login-card-footer-text">
                            <a href="<?= url("login") ?>" class="login-card-footer-text2"> Entrar</a> •
                            <?php
                            $assunto = "Preciso de Ajuda";
                            $para = "suporte.oficial@redrocket.com.br";
                            $mensagem = "Olá, estou com problema na página " . $_SERVER["REQUEST_URI"] . ". Estou com o seguinte problema:";
                            ?>
                            <a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>">Não conseguiu cadastrar?</a>
                        </div>

                        <input type="hidden" name="dropshipping" value="0">
                    </form>
                </div>
            </div>

            <div class="login-right" style="align-items: flex-start; text-align: left;">
                <h1><b>WIN</b><span>PAY</span></h1>
                <div class="underline"></div>
                <p>"O sucesso é a soma de pequenos esforços repetidos dia após dia."</p>
                <span style="color: rgb(217, 192, 0);">Robert Collier</span>
            </div>
        </div>
    </div>

    <script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js") ?>"></script>
    <script>
        // Validação de email
        $("#email").focusout(function() {
            var email = $("#email").val();
            validarEmail(email);
        });

        $("#email").on("paste", function() {
            var email = $("#email").val();
            validarEmail(email);
        });

        function validarEmail(email) {
            console.log("<?= $router->route("validacao.email") ?>");

            if (email) {
                $.ajax({
                    url: "<?= $router->route("validacao.email") ?>",
                    data: {
                        email: email
                    },
                    type: "POST",
                    success: function(callback) {
                        console.log(callback);
                        if (callback == "Email inválido") {
                            $("#msg-email").css("color", "red").html("Email inválido! Favor corrigí-lo");
                            $("#email").focus();
                        } else {
                            $("#msg-email").html("");
                        }
                    }
                });
            } else {
                $("#msg-email").html("");
            }
        }

        // Validação de telefone
        $("#telefone").focusout(function() {
            var telefone = $("#telefone").val();
            validarTelefone(telefone);
        });

        $("#telefone").on("paste", function() {
            var telefone = $("#telefone").val();
            validarTelefone(telefone);
        });

        function validarTelefone(telefone) {
            const numeroLimpo = telefone.replace(/\D/g, '');
            if (/^\d+$/.test(numeroLimpo)) {
                $("#telefone").val(numeroLimpo);
                return true;
            } else {
                return false;
            }
        }

        // Validação de CPF
        $("#cpf").blur(function() {
            var cpf = $(this).val();
            $.ajax({
                url: "<?= $router->route("appusuarios.cpf") ?>",
                data: {
                    cpf: cpf
                },

                type: "POST",
                success: function(callback) {
                    if (callback == "Não existe usuário com esse CPF/CNPJ" || callback == "") {
                        $("#msg-cpf").html("");
                        $("#cadastrar").attr("disabled", false);
                    } else {
                        $("#msg-cpf").html("CPF/CNPJ já cadastrado");
                        $("#cadastrar").attr("disabled", true);
                    }
                }
            });
        });

        // Validação de senhas
        $("#confirma").blur(function() {
            if ($(this).val() != $("#senha").val()) {
                $("#msg-senha").html("As senhas são diferentes");
                $("#cadastrar").attr("disabled", true);
            } else {
                $("#msg-senha").html("");
                $("#cadastrar").attr("disabled", false);
            }
        });

        // Envio de dados
        $("#cadastrar").click(function(e) {
            e.preventDefault();
            var form = $("form");

            $("input").each(function() {
                var element = $(this);
                if (element.val() == "") {
                    isValid = false;
                } else {
                    isValid = true;
                }
            });

            if (isValid == false) {
                alert("Todos os campos devem ser preenchidos.");
                return false;
            } else {
                $.ajax({
                    url: "<?= $router->route("appusuarios.cadastrar") ?>",
                    data: form.serialize(),
                    type: "POST",
                    success: function(callback) {
                        if (callback == "Cadastrado Com Sucesso!") {
                            alert(callback);
                            location.href = "<?= $router->route("login.login") ?>?msg=Cadastrado com Sucesso. Não esqueça de verificar sua caixa de email e ativar sua conta&classCss=bg-success";
                        } else {
                            alert(callback);
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>

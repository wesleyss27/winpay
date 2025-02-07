<?php
date_default_timezone_set("America/Sao_Paulo");
//echo date("Y-m-d H:i:s", strtotime("+30 min"));

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
</head>

<body class="body bg-white d-flex justify-content-center align-items-center">
  <div class="container-fluid h-100">
      <div class="row login-container h-100">
          <div class="col-md-6 vh-100 d-flex justify-content-center align-items-center" style="background: #fafcff">
              <!-- Conteúdo de login (lado direito) -->
              <div class="d-flex flex-column justify-content-center align-items-center w-100">
                <img class="logo mb-2" style="width: 180px; height: auto;" src="<?= url("themes/imgs/logos/logo.png"); ?>" alt="Logo-redrocket" width="240px">
                  
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
                    <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" id="cadastrar">Cadastrar</button>

                    <p>Ao se cadastrar você concorda com os <a href="<?= url("termos-uso") ?>">Termos de Uso</a> da REDROCKET.</p>

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

          <div class="col-md-6 d-flex justify-content-center align-items-center">
              <!-- Imagem de background (lado esquerdo) -->
              <img class="w-75 h-auto" src="<?= url("themes/imgs/login.jpg"); ?>" alt="">
          </div>
      </div>
  </div>
                        

    <script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js") ?>"></script>
    <script>
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
                            // alert("Email inválido! Favor corrigí-lo ou deixe em branco");
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

        $("#telefone").focusout(function() {
            var telefone = $("#telefone").val();
            validarTelefone(telefone);
        });

        $("#telefone").on("paste", function() {
            var telefone = $("#telefone").val();
            validarTelefone(telefone);
        });

        function validarTelefone(telefone) {
            // Remover caracteres especiais: + - ( ) , . -
            const numeroLimpo = telefone.replace(/\D/g, '');
            console.log(telefone, numeroLimpo)

            // Verificar se o número contém apenas dígitos
            if (/^\d+$/.test(numeroLimpo)) {
                // Outras verificações podem ser adicionadas aqui, se necessário
                $("#telefone").val(numeroLimpo);
                console.log('limpo')
                return true;
            } else {
                return false;
            }
        }

        var url = "<?= $router->route("appusuarios.cadastro"); ?>";

        $("#email").blur(function() {
            var email = $(this).val();
            $.ajax({
                url: "<?= $router->route("appusuarios.email") ?>",
                data: {
                    email: email
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log("callback ", callback);
                    if (callback == "Não existe usuário com esse email") {
                        $("#cadastrar").attr("disabled", false);
                        $("#add-email").css("background", "#007bff");
                        $("#msg-email").html("");
                    } else {
                        $("#add-email").css("background", "#ed1c24");
                        $("#msg-email").html("Email já cadastrado");
                        $("#cadastrar").attr("disabled", true);
                    }
                }
            });
        });
        $("#login").blur(function() {
            var login = $(this).val();
            $.ajax({
                url: "<?= $router->route("appusuarios.login") ?>",
                data: {
                    login: login
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log("callback ", callback);
                    if (callback == "Não existe usuário com esse login") {
                        $("#add-login").css("background", "#007bff");
                        $("#msg-login").html("");
                        $("#cadastrar").attr("disabled", false);
                    } else {
                        $("#add-login").css("background", "#ed1c24");
                        $("#msg-login").html("Login não disponível");
                        $("#cadastrar").attr("disabled", true);
                    }
                }
            });
        });
        $("#cpf").blur(function() {
            var cpf = $(this).val();
            $.ajax({
                url: "<?= $router->route("appusuarios.cpf") ?>",
                data: {
                    cpf: cpf
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log("callback ", callback);
                    if (callback == "Não existe usuário com esse CPF/CNPJ" || callback == "") {
                        $("#add-cpf").css("background", "#007bff");
                        $("#msg-cpf").html("");
                        $("#cadastrar").attr("disabled", false);
                    } else {
                        $("#add-cpf").css("background", "#ed1c24");
                        $("#msg-cpf").html("CPF/CNPJ já cadastrado");
                        $("#cadastrar").attr("disabled", true);
                    }
                }
            });
        });
        $("#confirma").blur(function() {
            if ($(this).val() != $("#senha").val()) {
                $("#add-confirma").css("background", "#ed1c24");
                $("#msg-senha").html("As senhas são diferentes");
                $("#cadastrar").attr("disabled", true);
            } else {
                $("#add-confirma").css("background", "#007bff");
                $("#msg-senha").html("");
                console.log("Senhas iguais");
                $("#cadastrar").attr("disabled", false);
            }
        });

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
                    //datatype: "json",
                    beforeSend: function() {
                        console.log("atualizando");
                    },
                    error: function(err) {
                        console.log("O servidor não conseguiu processar o pedido", err);
                    },
                    success: function(callback) {
                        console.log("callback ", callback);
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
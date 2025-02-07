<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro OctaPay</title>

    <link rel="stylesheet" href="<?= url("themes/tema1/assets/css/bootstrap.css"); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema1/assets/css/login.css?".time()); ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!--[if IE]><link rel="shortcut icon" href="<?= URL_BASE . "/themes/tema1/assets/imgs/OCTA.png"; ?>"><![endif]-->
    <link rel="icon" href="<?= URL_BASE . "/themes/tema1/assets/imgs/OCTA.png"; ?>">
</head>
<body class="body">
    <div class="container-fluid">
    <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="corpo mx-auto">
             
                <h1><img class="logo" src="<?= url("themes/tema1/images/octapay.png"); ?>" width="250" alt="OctaPay"></h1>
                <br>
                <h5 style="text-align:center; margin-bottom:10px" >Seja Bem Vindo, crie sua conta na OctaPay.</h5> 

                
                <?= $v->insert("themes/tema1/fragmentos/msg-cadastrar-editar", []); ?>
                
                <form action="#" method="post">
                    <input type="hidden" name="tipo_usuario" value="5">
                    
                    <div class="form-label-group">
                        <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome completo" required>
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


                    <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" id="cadastrar">Cadastrar</button>

                    <p>Ao se cadastrar você concorda com os <a href="<?= url("termos-de-uso") ?>">Termos de Uso</a> da Octapay.</p>

                    <!--div class="col-md-6"><a href="#">Esqueci a Senha </a></div>
                    <div class="col-md-6"><a href="#"> Solicitar Cadastro</a></div-->
                    
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        var url = "<?= $router->route("appusuarios.cadastro"); ?>";
        $("#email").blur(function(){
            var email = $(this).val();
            $.ajax({
                url: "<?=$router->route("appusuarios.email")?>",
                data: { email: email},

                type: "POST",
                beforeSend: function () {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function (callback) {
                    console.log("callback ", callback);
                    if(callback == "Não existe usuário com esse email"){
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
        $("#login").blur(function(){
            var login = $(this).val();
            $.ajax({
                url: "<?=$router->route("appusuarios.login")?>",
                data: {login: login},

                type: "POST",
                beforeSend: function () {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function (callback) {
                    console.log("callback ", callback);
                    if(callback == "Não existe usuário com esse login"){
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
        $("#cpf").blur(function(){
            var cpf = $(this).val();
            $.ajax({
                url: "<?=$router->route("appusuarios.cpf")?>",
                data: {cpf: cpf},

                type: "POST",
                beforeSend: function () {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function (callback) {
                    console.log("callback ", callback);
                    if(callback == "Não existe usuário com esse CPF/CNPJ" || callback == ""){
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
        $("#confirma").blur(function(){
            if($(this).val() != $("#senha").val()){
                $("#add-confirma").css("background", "#ed1c24");
                $("#msg-senha").html("As senhas são diferentes");
                $("#cadastrar").attr("disabled", true);
            }else{
                $("#add-confirma").css("background", "#007bff");
                $("#msg-senha").html("");
                console.log("Senhas iguais");
                $("#cadastrar").attr("disabled", false);
            }
        });

        $("#cadastrar").click(function(e){
            e.preventDefault();
            var form = $("form");

            $("input").each(function(){
                var element = $(this);
                if (element.val() == "") { isValid = false; } else { isValid = true; }
            });

            if(isValid == false){
                alert("Todos os campos devem ser preenchidos.");
                return false;
            } else {

                $.ajax({
                    url: "<?=$router->route("appusuarios.cadastrar")?>",
                    data: form.serialize(),
                    type: "POST",
                    //datatype: "json",
                    beforeSend: function () {
                        console.log("atualizando");
                    },
                    error: function(err) {
                        console.log("O servidor não conseguiu processar o pedido", err);
                    },
                    success: function (callback) {
                        console.log("callback ", callback);
                        alert(callback);
                        if(callback == "Cadastrado Com Sucesso!"){
                            location.href = "<?=$router->route("login.login")?>?msg=Verifique sua caixa de email e ative sua conta antes de logar&classCss=bg-danger";
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
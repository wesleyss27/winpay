<?php
date_default_timezone_set("America/Sao_Paulo");
//echo date("Y-m-d H:i:s", strtotime("+30 min"));

$url_atual = HTTP . "$_SERVER[HTTP_HOST]";
if ($url_atual == "http://localhost") {
  $redirect = explode("/club-redrocket", @$_GET["redirect"]);
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

  <link rel="icon" href="<?= url("themes/imgs/logo-icon.png"); ?>">

  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/adminlte.min.css?" . time()); ?>">
  <!-- <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/cores.css"); ?>"> -->
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/login2.css?" . time()); ?>">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js?" . time()); ?>"></script>
  <script src="<?= url("themes/tema1/plugins/bootstrap/js/bootstrap.min.js?" . time()); ?>"></script>
  <script src="<?= url("themes/tema1/plugins/popper/popper.min.js?" . time()); ?>"></script>


</head>

<body class="body">
  <div class="container-fluid">
    <div class="row no-gutter">
      <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
      <div class="col-md-8 col-lg-6">
        <div class="login d-flex align-items-center py-5">
          <div class="container">
            <div class="row">
              <div class="col-md-9 col-lg-8 mx-auto">

                <img class="logo" src="<?= url("themes/imgs/logo5.png"); ?>" alt="Logo-redrocket" width=250 height=70>
                <br>
                <br>
                <h3 class="login-heading mb-4">Acesse sua conta</h3>
                <p>Valor de redirect: <?= @$redirect ?></p>

                <?= $v->insert("themes/tema1/fragmentos/msg-cadastrar-editar", []); ?>

                <form action="<?= $router->route("login.logar"); ?>" method="post">
                  <input type="hidden" name="redirect" value="<?= @$redirect ?>">

                  <div class="form-label-group">
                    <input type="text" id='email' name="email" class="form-control input-lg" placeholder="Email ou login" required>
                  </div>

                  <div class="form-label-group">
                    <input type="password" name="senha" id='senha' class="form-control input-lg" placeholder="Senha" required>
                  </div>

                  <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Entrar</button>

                  <a href="#!" data-toggle="modal" data-target="#myModal" class="forgot-password-link">Esqueceu sua senha?</a>

                  <!--div class="col-md-6"><a href="#">Esqueci a Senha </a></div>
                    <div class="col-md-6"><a href="#"> Solicitar Cadastro</a></div-->
                  <!-- <div class="login-card-footer-text"><p>Ainda não possui conta?<a href="<?= url("cadastro") ?>" class="login-card-footer-text2" >  Cadastre-se</a></div> -->
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>

  <!-- The Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Esqueceu a senha</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <form action="#" method="post">
          <!-- Modal body -->
          <div class="modal-body">
            Informe o email cadastrado:
            <input type="text" name="email2" id="email2" class="form-control">
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" id="salvar">Enviar</button>
          </div>
        </form>

      </div>
    </div>
  </div>
  <!-- Modal -->

  <script>
    $("#salvar").click(function(e) {
      e.preventDefault();
      var email = $("#email2").val();
      console.log(email);
      salvar("<?= $router->route("appusuarios.esqueceuSenha") ?>");
    });

    function salvar(url) {
      var form = $("form");
      $.ajax({

        url: url,
        data: form.serialize(),
        type: "POST",
        success: function(callback) {

          if (callback == "Email enviado com sucesso.") {
            alert("Verifique o email informado para redefinir sua senha");
            location.reload();
          } else {
            alert(callback);
          }
        }

      });
    };
  </script>
</body>

</html>
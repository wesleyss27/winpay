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
  <link rel="icon" href="<?= url("themes/imgs/logos/logo-icon.png"); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/cores.css"); ?>">
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/login2.css?" . time()); ?>">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="<?= url("themes/tema1/assets/js/jquery.js"); ?>"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body class="body bg-white d-flex justify-content-center align-items-center">
  <div class="container-fluid h-100">
      <div class="row login-container h-100">
            <div class="col-md-6 d-none d-md-flex justify-content-center align-items-center">
              <!-- Imagem de background (lado esquerdo) -->
              <img class="w-100 h-100" src="<?= url("themes/imgs/login.png"); ?>" alt="" style="object-fit: cover;">
            </div>

          <div class="col-md-6 vh-100 d-flex justify-content-center align-items-center" style="background: #fafcff">
              <!-- Conteúdo de login (lado direito) -->
              <div class="d-flex flex-column justify-content-center align-items-center w-100">
                <img class="logo mb-2" style="width: 180px; height: auto;" src="<?= url("themes/imgs/logos/logo.png"); ?>" alt="Logo-redrocket" width="240px">
                  
                <h3 class="mb-5">Acesse sua conta</h3>

                <?= $v->insert("themes/tema2/fragmentos/msg-cadastrar-editar", []); ?>

                <form action="<?= $router->route("login.logar"); ?>" method="post" class="w-50">
                  <input type="hidden" name="redirect" value="<?= @$redirect ?>">

                  <div class="form-label-group">
                    <small><label>Email ou login</label></small>
                    <input type="text" id='email' name="email" class="form-control input-lg" placeholder="Email ou login" required>
                  </div>

                  <div class="form-label-group">
                    <small><label>Senha</label></small>
                    <input type="password" name="senha" id='senha' class="form-control input-lg" placeholder="Senha" required>
                  </div>

                    <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit" style="border-radius: 50px;">Entrar</button>

                  <div class="text-center mt-4">
                    <a href="#" data-toggle="modal" data-target="#myModal" class="">Esqueceu sua senha?</a> •
                    <a href="<?= url("cadastro") ?>" class=""> Cadastre-se</a><br>

                    <?php
                    $assunto = "Preciso de Ajuda";
                    $para = "suporte.oficial@redrocket.com.br";
                    $mensagem = "Olá, estou com problema na página " . $_SERVER["REQUEST_URI"] . ". Estou com o seguinte problema:";
                    ?>
                    <a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>">Não conseguiu entrar?</a>
                  </div>
                </form>
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
              <div class="modal-body" style="background: #f4f6f9">
                <div class="card card-body">
                  Informe o email cadastrado:
                  <input type="text" name="email2" id="email2" class="form-control">
                </div>
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
  </div>

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
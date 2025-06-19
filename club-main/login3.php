<?php
date_default_timezone_set("America/Sao_Paulo");

$url_atual = HTTP . "$_SERVER[HTTP_HOST]";
if ($url_atual == "http://localhost") {
  $redirect = explode("/club-redrocket", @$_GET["redirect"]);
  $redirect = @$redirect[1];
} else {
  $redirect = @$_GET["redirect"];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - WinPay</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

  <style>
    body {
      background-color: #121212;
      color: white;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
      padding: 40px 30px;
      background: #1e1e1e;
      border-radius: 16px;
      box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .login-container h3 {
      margin-bottom: 20px;
      text-align: center;
    }

    .login-container .logo {
      width: 80px;
      margin: 0 auto 15px;
      display: block;
    }

    .form-group label {
      font-weight: 500;
    }

    .form-control {
      background-color: #2a2a2a;
      border: 1px solid #444;
      color: white;
      border-radius: 8px;
    }

    .form-control::placeholder {
      color: #aaa;
    }

    .input-group-text {
      background-color: #2a2a2a;
      border: 1px solid #444;
      color: #FFD700;
      border-radius: 8px 0 0 8px;
    }

    .btn-primary {
      width: 100%;
      background-color: #FFD700;
      color: black;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      padding: 10px;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #e6c200;
    }

    .login-links {
      text-align: center;
      margin-top: 15px;
    }

    .login-links a {
      color: #FFD700;
      text-decoration: none;
    }

    .modal-content {
      background-color: #1e1e1e;
      color: white;
    }

    .modal-header,
    .modal-footer {
      border-color: #444;
    }
  </style>
</head>
<body>

<div class="login-container">
  <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Logo" class="logo">
  <h3>Acesse sua conta</h3>

  <?= $v->insert("themes/tema1/fragmentos/msg-cadastrar-editar", []); ?>

  <form action="<?= $router->route("login.logar"); ?>" method="post">
    <input type="hidden" name="redirect" value="<?= @$redirect ?>">

    <div class="form-group">
      <label for="email">Email ou login</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-user"></i></span>
        </div>
        <input type="text" name="email" class="form-control" placeholder="Digite seu email ou login" required>
      </div>
    </div>

    <div class="form-group">
      <label for="senha">Senha</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-lock"></i></span>
        </div>
        <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Entrar</button>

    <div class="login-links">
      <a href="#!" data-toggle="modal" data-target="#myModal">Esqueceu sua senha?</a> •
      <a href="<?= url('cadastro'); ?>">Cadastre-se</a>
    </div>
  </form>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="#" method="post">
        <div class="modal-header">
          <h5 class="modal-title">Esqueceu a senha</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <p>Informe o email cadastrado:</p>
          <input type="email" name="email2" id="email2" class="form-control" placeholder="Seu email" required>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="salvar">Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $("#salvar").click(function(e) {
    e.preventDefault();
    var email = $("#email2").val();
    salvar("<?= $router->route("appusuarios.esqueceuSenha") ?>");
  });

  function salvar(url) {
    var form = $("form");
    $.ajax({
      url: url,
      data: form.serialize(),
      type: "POST",
      success: function(response) {
        if (response === "Email enviado com sucesso.") {
          alert("Verifique o email informado para redefinir sua senha.");
          location.reload();
        } else {
          alert(response);
        }
      }
    });
  }
</script>

</body>
</html>

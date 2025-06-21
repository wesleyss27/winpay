<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - WinPay</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

  <style>
    body {
      background-color: black;
      color: white;
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .background-elements {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      pointer-events: none;
      background: radial-gradient(circle at top left, rgba(217, 192, 0, 0.2), transparent 50%),
        radial-gradient(circle at bottom right, rgba(0, 0, 0, 0.15), transparent 50%);
    }

    .login-container {
      display: flex;
      width: 80%;
      max-width: 1200px;
    }

    .login-left {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 2rem;
    }

    .login-left h1 {
      font-weight: bold;
      font-size: 2rem;
      color: white;
      margin-bottom: 0;
    }

    .login-left h1 span {
      color: rgb(217, 192, 0);
    }

    .underline {
      width: 60px;
      height: 3px;
      background: rgb(217, 192, 0);
      margin-top: 5px;
    }

    .login-left p {
      font-size: 1.1rem;
      margin-top: 10px;
    }

    @media (max-width: 768px) {
      .login-left {
        display: none;
      }

      .login-box {
        width: 100%;
      }
    }

    .login-box {
      flex: 1;
      background: transparent;
      padding: 3rem;
      border-radius: 10px;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
      position: relative;
      z-index: 2;
      height: 700px;
    }

    .form-control {
      background: white;
      border: none;
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

    .label-icon {
      color: white;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .label-icon .material-symbols-outlined {
      color: rgb(217, 192, 0);
    }

    .input-group {
      margin-bottom: 25px;
    }
  </style>
</head>

<body>
  <div class="background-elements"></div>
  <div class="login-container">
    <div class="login-left">
      <h1><b>WIN</b><span>PAY</span></h1>
      <div class="underline"></div>
      <p>"O sucesso é a soma de pequenos esforços repetidos dia após dia."</p>
      <span style="color: rgb(217, 192, 0);">Robert Collier</span>
    </div>

    <div class="login-box">
      <h3 class="text-center mb-4">Acesse sua conta</h3>
      <form action="<?= $router->route('login.logar'); ?>" method="post">
        <div class="label-icon">
          <span class="material-symbols-outlined">mail</span> Email ou login
        </div>
        <div class="input-group">
          <input type="email" name="email" class="form-control" placeholder="Seu email" required>
        </div>
        <div class="label-icon">
          <span class="material-symbols-outlined">lock</span> Senha
        </div>
        <div class="input-group">
          <input type="password" name="senha" class="form-control" placeholder="Sua senha" required>
        </div>
        <br>
        <button type="submit" class="btn btn-primary btn-block"
          style="background: rgb(217, 192, 0); border-radius: 8px; font-weight: bold; padding: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; outline: none; box-shadow: none; color: black;">
          ENTRAR <span class="material-symbols-outlined">arrow_forward</span>
        </button>

        <br><br><br><br><br><br>
        <div class="text-center mt-3 login-links" style="color: white;">
          <a href="#" data-toggle="modal" data-target="#myModal" style="color: white;">Esqueceu sua senha?</a> •
          <a href="<?= url('cadastro'); ?>" style="color: white;">Cadastre-se</a>
        </div>
        <div class="text-center mt-2" style="color: white;">
          <a
            href="mailto:suporte.oficial@redrocket.com.br?subject=Preciso de Ajuda&body=Ol%C3%A1,%20estou%20com%20problema%20na%20p%C3%A1gina%20<?= $_SERVER["REQUEST_URI"] ?>.%20Estou%20com%20o%20seguinte%20problema:"
            style="color: white;">Não conseguiu entrar?</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal "Esqueceu sua senha?" -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="background: #1e293b; color: white;">
        <div class="modal-header" style="border-bottom: 1px solid #7e69aa;">
          <h4 class="modal-title">Esqueceu a senha?</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Digite seu email para receber um link de redefinição de senha.</p>
          <input type="email" id="email2" class="form-control" placeholder="Seu email">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" id="salvar">Enviar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function salvar(url) {
      var email = $("#email2").val().trim();

      if (email === "") {
        alert("Por favor, preencha o email.");
        return;
      }

      $.ajax({
        url: url,
        method: "POST",
        data: { email: email },
        success: function (response) {
          console.log(response);
          alert("Se o email estiver cadastrado, você receberá um link para redefinir sua senha.");
          $('#myModal').modal('hide');
        },
        error: function (error) {
          console.error(error);
          alert("Ocorreu um erro ao enviar. Tente novamente.");
        }
      });
    }

    $("#salvar").click(function (e) {
      e.preventDefault();
      salvar("<?= $router->route('appusuarios.esqueceuSenha') ?>");
    });
  </script>
</body>

</html>

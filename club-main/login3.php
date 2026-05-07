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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Material+Symbols+Outlined&display=swap" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

  <style>
    :root {
      --color-bg: #050507;
      --color-card: rgba(12, 13, 20, 0.82);
      --color-border: rgba(255, 255, 255, 0.08);
      --color-text: #f9fafb;
      --color-muted: #9ca3af;
      --color-accent: #e2e8f0;
      --color-accent-soft: rgba(226, 232, 240, 0.12);
      --shadow-lg: 0 24px 48px rgba(0, 0, 0, 0.6);
    }

    * {
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      background:
        radial-gradient(circle at 12% 18%, rgba(226, 232, 240, 0.07), transparent 55%),
        radial-gradient(circle at 86% 24%, rgba(226, 232, 240, 0.05), transparent 60%),
        var(--color-bg);
      position: relative;
      color: var(--color-text);
      padding: 2.5rem 1.5rem;
    }

    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: url('themes/tema1/assets/pattern.svg') repeat;
      opacity: 0.08;
      pointer-events: none;
    }

    .auth-shell {
      position: relative;
      width: min(480px, 92vw);
      padding: clamp(2.4rem, 4vw, 3.1rem);
      border-radius: 26px;
      border: 1px solid var(--color-border);
      background: rgba(5, 5, 8, 0.72);
      backdrop-filter: blur(22px);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
      animation: fadeIn 0.8s ease;
    }

    .auth-shell::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: radial-gradient(circle at 80% 0%, rgba(226, 232, 240, 0.06), transparent 60%);
      opacity: 0.65;
      pointer-events: none;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .auth-header {
      text-align: center;
      margin-bottom: clamp(2rem, 4vw, 2.4rem);
      position: relative;
      z-index: 1;
    }

    .auth-logo {
      display: inline-flex;
      align-items: center;
      gap: .7rem;
      margin-bottom: 1rem;
    }

    .auth-logo-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      overflow: hidden;
      flex-shrink: 0;
      box-shadow: 0 8px 18px rgba(0,0,0,.35);
    }

    .auth-logo-icon img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .auth-logo-text {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
      text-align: left;
    }

    .auth-logo-name {
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: .2em;
      color: #f9fafb;
    }

    .auth-logo-sub {
      font-size: .68rem;
      font-weight: 600;
      letter-spacing: .32em;
      text-transform: uppercase;
      color: rgba(226, 232, 240, .55);
    }

    .auth-header h1 {
      font-size: clamp(1.7rem, 3vw, 1.95rem);
      font-weight: 600;
      margin-bottom: 0.35rem;
      letter-spacing: 0.02em;
    }

    .auth-header p {
      color: var(--color-muted);
      font-size: 0.95rem;
    }

    .auth-form {
      display: grid;
      gap: 1.6rem;
      position: relative;
      z-index: 1;
    }

    .form-field {
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }

    .field-label {
      font-weight: 600;
      font-size: 0.92rem;
      color: var(--color-text);
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-wrapper .material-symbols-outlined {
      position: absolute;
      left: 1rem;
      color: var(--color-accent);
      font-size: 1.35rem;
      opacity: 0.85;
    }

    .form-control {
      width: 100%;
      padding: 0.95rem 1.1rem 0.95rem 2.7rem;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(12, 13, 20, 0.85);
      color: var(--color-text);
      transition: border 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .form-control::placeholder {
      color: var(--color-muted);
    }

    .form-control:focus {
      border-color: rgba(226, 232, 240, 0.45);
      box-shadow: 0 0 0 4px rgba(226, 232, 240, 0.12);
      background: rgba(12, 13, 20, 0.95);
      color: var(--color-text);
    }

    .toggle-password {
      position: absolute;
      right: 0.75rem;
      border: none;
      background: transparent;
      color: var(--color-muted);
      display: grid;
      place-items: center;
      width: 40px;
      height: 40px;
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.2s ease, color 0.2s ease;
    }

    .toggle-password:hover {
      background: rgba(226, 232, 240, 0.1);
      color: var(--color-accent);
    }

    .btn-auth {
      width: 100%;
      border: none;
      border-radius: 16px;
      padding: 0.95rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      background: linear-gradient(135deg, #e2e8f0 0%, #c8d4e0 100%);
      color: #0a0b12;
      box-shadow: 0 8px 24px rgba(226, 232, 240, 0.18);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-auth:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 32px rgba(226, 232, 240, 0.22);
      color: #0a0b12;
    }

    .auth-links {
      margin-top: 1.6rem;
      text-align: center;
      font-size: 0.9rem;
      color: var(--color-muted);
    }

    .auth-links a {
      color: var(--color-accent);
      font-weight: 500;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .auth-links a:hover {
      color: #b8c4d0;
    }

    .modal-content {
      border-radius: 20px;
      border: 1px solid rgba(226, 232, 240, 0.18);
      background: rgba(10, 12, 18, 0.95);
      color: var(--color-text);
      box-shadow: var(--shadow-lg);
    }

    .modal-header,
    .modal-footer {
      border-color: rgba(255, 255, 255, 0.08);
    }

    .modal .form-control {
      background: rgba(13, 15, 22, 0.85);
      border-radius: 12px;
    }

    .btn-secondary,
    .btn-primary {
      border-radius: 12px;
      padding: 0.75rem 1.4rem;
      font-weight: 600;
      border: none;
    }

    .btn-secondary {
      background: rgba(148, 163, 184, 0.12);
      color: var(--color-text);
    }

    .btn-primary {
      background: var(--color-accent);
      color: #0a0b12;
    }

    .btn-secondary:hover {
      background: rgba(148, 163, 184, 0.18);
      color: var(--color-text);
    }

    .btn-primary:hover {
      background: #c8d4e0;
      color: #0a0b12;
    }
  </style>
</head>

<body>

  <div class="auth-shell">
    <header class="auth-header">
      <div class="auth-logo">
        <div class="auth-logo-icon">
          <img src="<?= url('themes/imgs/logo-win.png') ?>" alt="Win Hub">
        </div>
        <div class="auth-logo-text">
          <span class="auth-logo-name">WIN HUB</span>
          <span class="auth-logo-sub">Learning</span>
        </div>
      </div>
      <h1>Acesse sua conta</h1>
      <p>Entre com seus dados para continuar gerindo seus resultados.</p>
    </header>

    <?= $v->insert("themes/tema1/fragmentos/msg-cadastrar-editar", []); ?>

    <form action="<?= $router->route("login.logar"); ?>" method="post" class="auth-form">
      <input type="hidden" name="redirect" value="<?= @$redirect ?>">

      <div class="form-field">
        <label class="field-label" for="email">Email ou login</label>
        <div class="input-wrapper">
          <span class="material-symbols-outlined">mail</span>
          <input type="text" id="email" name="email" class="form-control" placeholder="nome@empresa.com" autocomplete="username" required>
        </div>
      </div>

      <div class="form-field">
        <label class="field-label" for="senha">Senha</label>
        <div class="input-wrapper">
          <span class="material-symbols-outlined">lock</span>
          <input type="password" id="senha" name="senha" class="form-control" placeholder="Digite sua senha" autocomplete="current-password" required>
          <button type="button" class="toggle-password" aria-label="Mostrar senha">
            <span class="material-symbols-outlined">visibility</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-auth">
        Entrar
        <span class="material-symbols-outlined">arrow_forward</span>
      </button>

      <div class="auth-links">
        <a href="#!" data-toggle="modal" data-target="#myModal">Esqueceu sua senha?</a>
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
    (function () {
      const toggleButton = document.querySelector('.toggle-password');
      const passwordInput = document.getElementById('senha');

      if (toggleButton && passwordInput) {
        toggleButton.addEventListener('click', function () {
          const isHidden = passwordInput.type === 'password';
          passwordInput.type = isHidden ? 'text' : 'password';
          this.querySelector('.material-symbols-outlined').textContent = isHidden ? 'visibility_off' : 'visibility';
          this.setAttribute('aria-label', isHidden ? 'Ocultar senha' : 'Mostrar senha');
        });
      }
    }());

    $("#salvar").click(function (e) {
      e.preventDefault();
      salvar("<?= $router->route("appusuarios.esqueceuSenha") ?>");
    });

    function salvar(url) {
      var form = $("form");
      $.ajax({
        url: url,
        data: form.serialize(),
        type: "POST",
        success: function (response) {
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

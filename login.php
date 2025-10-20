<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - WinPay</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&family=Poppins:wght@400;500;600;700&display=swap" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

  <style>
    :root {
      --color-bg: #050507;
      --color-card: rgba(12, 13, 20, 0.85);
      --color-border: rgba(255, 255, 255, 0.08);
      --color-text: #f9fafb;
      --color-muted: #9ca3af;
      --color-accent: #f4d03f;
      --color-accent-soft: rgba(244, 208, 63, 0.16);
      --shadow-xl: 0 28px 48px rgba(0, 0, 0, 0.55);
    }

    body {
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at 20% -10%, rgba(244, 208, 63, 0.18), rgba(5, 5, 7, 0) 55%),
        radial-gradient(circle at 80% 20%, rgba(84, 58, 183, 0.14), transparent 60%),
        var(--color-bg);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      color: var(--color-text);
    }

    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: url('themes/tema1/assets/pattern.svg') repeat;
      opacity: 0.08;
      pointer-events: none;
    }

    .background-elements {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      pointer-events: none;
      background:
        radial-gradient(circle at 15% 25%, rgba(244, 208, 63, 0.18), transparent 58%),
        radial-gradient(circle at 80% 80%, rgba(244, 208, 63, 0.1), transparent 60%);
    }

    .auth-shell {
      position: relative;
      display: grid;
      grid-template-columns: minmax(280px, 1fr) minmax(340px, 440px);
      gap: 2.5rem;
      width: min(1120px, 92vw);
      padding: 2.5rem 3vw;
      border-radius: 28px;
      background: rgba(5, 5, 8, 0.65);
      border: 1px solid rgba(255, 255, 255, 0.06);
      box-shadow: var(--shadow-xl);
      backdrop-filter: blur(24px);
      z-index: 1;
    }

    .auth-highlight {
      display: flex;
      flex-direction: column;
      gap: 2rem;
      padding: 1rem 0 1rem 1.5rem;
    }

    .brand {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .brand h1 {
      font-weight: 700;
      font-size: clamp(2.4rem, 4vw, 3rem);
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--color-text);
    }

    .brand h1 span {
      color: var(--color-accent);
    }

    .brand-line {
      width: 68px;
      height: 4px;
      border-radius: 999px;
      background: linear-gradient(90deg, rgba(244, 208, 63, 0.4), rgba(244, 208, 63, 1));
    }

    .brand-quote {
      font-weight: bold;
      font-size: 1.6rem;
      line-height: 1.4;
      max-width: 420px;
    }

    .brand-cite {
      color: rgba(244, 208, 63, 0.8);
      letter-spacing: 0.04em;
      text-transform: uppercase;
      font-size: 0.75rem;
    }

    .feature-list {
      display: grid;
      gap: 1rem;
    }

    .feature-item {
      display: flex;
      gap: 1rem;
      align-items: flex-start;
      padding: 1.1rem 1rem;
      border-radius: 18px;
      border: 1px solid rgba(255, 255, 255, 0.06);
      background: rgba(10, 11, 18, 0.72);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    .feature-icon {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: rgba(244, 208, 63, 0.12);
      color: var(--color-accent);
      font-size: 1.3rem;
    }

    .feature-item span {
      display: block;
      color: var(--color-muted);
      font-size: 0.85rem;
    }

    .auth-card {
      background: var(--color-card);
      border-radius: 24px;
      border: 1px solid var(--color-border);
      padding: clamp(2.2rem, 4vw, 3rem);
      box-shadow: var(--shadow-xl);
      display: flex;
      flex-direction: column;
      gap: 2rem;
      position: relative;
      overflow: hidden;
    }

    .auth-card::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: radial-gradient(circle at right top, rgba(244, 208, 63, 0.18), transparent 65%);
      opacity: 0.6;
      pointer-events: none;
    }

    .auth-card header {
      position: relative;
      z-index: 1;
    }

    .auth-title {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .auth-title h2 {
      font-size: 1.8rem;
      font-weight: 600;
    }

    .auth-title span {
      color: var(--color-muted);
      font-size: 0.95rem;
    }

    .auth-form {
      display: grid;
      gap: 1.5rem;
      position: relative;
      z-index: 1;
    }

    .form-field {
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }

    .field-label {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .field-label .material-symbols-outlined {
      font-size: 1.45rem;
      color: var(--color-accent);
      background: rgba(244, 208, 63, 0.14);
      border-radius: 12px;
      padding: 0.35rem;
    }

    .form-control {
      background: rgba(13, 15, 22, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.08);
      color: var(--color-text);
      border-radius: 14px;
      padding: 0.95rem 1.1rem;
      font-size: 0.95rem;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
      border-color: rgba(244, 208, 63, 0.5);
      box-shadow: 0 0 0 4px rgba(244, 208, 63, 0.12);
      background: rgba(13, 15, 22, 0.95);
      color: var(--color-text);
    }

    .password-group {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 0.85rem;
      transform: translateY(-50%);
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
      background: rgba(244, 208, 63, 0.1);
      color: var(--color-accent);
    }

    .btn-auth {
      width: 100%;
      padding: 0.95rem;
      border-radius: 16px;
      font-weight: 600;
      letter-spacing: 0.04em;
      border: none;
      background: linear-gradient(90deg, rgba(244, 208, 63, 0.9), rgba(244, 208, 63, 0.7));
      color: #050507;
      box-shadow: 0 14px 32px rgba(244, 208, 63, 0.25);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.65rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-auth:hover {
      transform: translateY(-2px);
      box-shadow: 0 18px 38px rgba(244, 208, 63, 0.32);
      color: #050507;
    }

    .auth-links {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      font-size: 0.9rem;
      align-items: center;
      text-align: center;
      color: var(--color-muted);
    }

    .auth-links a {
      color: var(--color-text);
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .auth-links a:hover {
      color: var(--color-accent);
    }

    .modal-content {
      border-radius: 20px;
      border: 1px solid rgba(244, 208, 63, 0.18);
      background: rgba(10, 12, 18, 0.95);
      color: var(--color-text);
      box-shadow: var(--shadow-xl);
    }

    .modal-header {
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .modal-footer {
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .modal .form-control {
      background: rgba(13, 15, 22, 0.85);
    }

    @media (max-width: 1024px) {
      .auth-shell {
        grid-template-columns: 1fr;
        padding: clamp(1.6rem, 4vw, 2.2rem);
        gap: 2rem;
      }

      .auth-highlight {
        display: none;
      }
    }

    @media (max-width: 600px) {
      body {
        padding: 2rem 0;
      }

      .auth-shell {
        width: min(96vw, 420px);
        background: rgba(5, 5, 8, 0.78);
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        padding: clamp(1.8rem, 5vw, 2.4rem);
      }

      .auth-card {
        border-radius: 18px;
        padding: clamp(1.75rem, 5vw, 2.3rem);
      }
    }
  </style>
</head>

<body>
  <div class="background-elements"></div>
  <div class="auth-shell">
    <aside class="auth-highlight">
      <div class="brand">
        <h1><b>WIN</b><span>PAY</span></h1>
        <div class="brand-line"></div>
        <p class="brand-quote">"O sucesso é a soma de pequenos esforços repetidos dia após dia."</p>
        <span class="brand-cite">Robert Collier</span>
      </div>
      <div class="feature-list">
        <div class="feature-item">
          <div class="feature-icon material-symbols-outlined">verified</div>
          <div>
            <strong>Segurança em primeiro lugar</strong>
            <span>Suas transações e dados contam com camadas adicionais de proteção.</span>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon material-symbols-outlined">query_stats</div>
          <div>
            <strong>Dashboard em tempo real</strong>
            <span>Acompanhe vendas e comissões com métricas claras e atualizadas.</span>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon material-symbols-outlined">support_agent</div>
          <div>
            <strong>Suporte dedicado</strong>
            <span>Equipe disponível para ajudar você a escalar seus resultados.</span>
          </div>
        </div>
      </div>
    </aside>

    <main class="auth-card">
      <header class="auth-title">
        <h2>Acesse sua conta</h2>
        <span>Entre com seus dados para continuar</span>
      </header>

      <form action="<?= $router->route('login.logar'); ?>" method="post" class="auth-form">
        <div class="form-field">
          <label class="field-label" for="email">
            <span class="material-symbols-outlined">mail</span>
            Email ou login
          </label>
          <input type="email" id="email" name="email" class="form-control" placeholder="nome@empresa.com" autocomplete="username" required>
        </div>

        <div class="form-field password-group">
          <label class="field-label" for="senha">
            <span class="material-symbols-outlined">lock</span>
            Senha
          </label>
          <input type="password" id="senha" name="senha" class="form-control" placeholder="Digite sua senha" autocomplete="current-password" required>
          <button type="button" class="toggle-password" aria-label="Mostrar senha">
            <span class="material-symbols-outlined">visibility</span>
          </button>
        </div>

        <button type="submit" class="btn-auth">
          Entrar
          <span class="material-symbols-outlined">arrow_forward</span>
        </button>
      </form>

      <div class="auth-links">
        <div>
          <a href="#" data-toggle="modal" data-target="#myModal">Esqueceu sua senha?</a>
          <span>•</span>
          <a href="<?= url('cadastro'); ?>">Cadastre-se</a>
        </div>
        <a href="mailto:suporte.oficial@redrocket.com.br?subject=Preciso de Ajuda&body=Ol%C3%A1,%20estou%20com%20problema%20na%20p%C3%A1gina%20<?= $_SERVER["REQUEST_URI"] ?>.%20Estou%20com%20o%20seguinte%20problema:">Não conseguiu entrar? Fale com o suporte.</a>
      </div>
    </main>
  </div>

  <!-- Modal "Esqueceu sua senha?" -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
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

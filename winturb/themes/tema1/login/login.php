<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar — WinTurb</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:     #f5f6fb;
      --accent: #5b4df0;
      --accent2:#4a3de0;
      --grad:   linear-gradient(135deg, #5b4df0 0%, #6f5cf6 55%, #7c6bff 100%);
      --text:   #15173b;
      --muted:  #737797;
      --border: #e8eaf3;
      --nav-bg: #0e0c27;
    }

    html, body {
      height: 100%;
      background: var(--bg);
      color: var(--text);
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      -webkit-font-smoothing: antialiased;
    }

    .login-wrap {
      min-height: 100vh;
      display: flex;
    }

    /* ══════════════════════════════════
       LEFT — dark sidebar (VTurb style)
    ══════════════════════════════════ */
    .login-left {
      width: 44%;
      background: linear-gradient(180deg, #0e0c27 0%, #130f36 100%);
      display: flex; flex-direction: column;
      justify-content: space-between;
      padding: 3rem 3.2rem;
      position: relative;
      overflow: hidden;
    }

    /* Subtle radial glow */
    .login-left::before {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(91,77,240,.3) 0%, transparent 65%);
      top: -100px; right: -160px;
      border-radius: 50%; pointer-events: none;
      animation: glow-drift 9s ease-in-out infinite alternate;
    }
    .login-left::after {
      content: '';
      position: absolute;
      width: 300px; height: 300px;
      background: radial-gradient(circle, rgba(91,77,240,.16) 0%, transparent 65%);
      bottom: -60px; left: -80px;
      border-radius: 50%; pointer-events: none;
      animation: glow-drift 13s ease-in-out infinite alternate-reverse;
    }
    @keyframes glow-drift {
      0%   { transform: translate(0,0); }
      100% { transform: translate(24px, 18px); }
    }

    /* Grid lines overlay */
    .login-grid {
      position: absolute; inset: 0; pointer-events: none;
      background-image:
        linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
      background-size: 44px 44px;
      mask-image: radial-gradient(ellipse at 65% 25%, rgba(0,0,0,.5) 0%, transparent 60%);
    }

    .login-left-top,
    .login-left-mid,
    .login-left-foot { position: relative; z-index: 1; }

    .login-logo {
      font-size: 2rem; font-weight: 800;
      letter-spacing: -.04em; color: #fff;
      margin-bottom: .5rem;
    }
    .login-logo em {
      font-style: normal;
      background: linear-gradient(90deg, #8f84ff, #5aa7ff);
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .login-tagline {
      font-size: .92rem; color: rgba(255,255,255,.55); line-height: 1.6;
    }

    /* Analytics card */
    .metrics-card {
      margin-top: 2.6rem;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 18px;
      padding: 1.3rem 1.4rem;
      backdrop-filter: blur(8px);
      display: flex; flex-direction: column; gap: 1.1rem;
    }

    .mc-row { display: flex; align-items: center; justify-content: space-between; gap: .8rem; }
    .mc-label {
      font-size: .76rem; font-weight: 600; color: rgba(255,255,255,.5);
    }
    .mc-val {
      font-size: 1.7rem; font-weight: 800; letter-spacing: -.03em;
      line-height: 1; color: #fff;
    }
    .mc-icon {
      width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: .88rem;
    }
    .mc-icon-v { background: rgba(124,107,255,.22); color: #b8afff; }
    .mc-icon-g { background: rgba(52,211,153,.15); color: #34d399; }

    .mc-bars { display: flex; align-items: flex-end; gap: 3px; height: 30px; }
    .mc-bar { width: 7px; border-radius: 3px 3px 0 0; background: rgba(124,107,255,.28); animation: bar-grow .5s cubic-bezier(.22,1,.36,1) both; }
    .mc-bar.hi { background: var(--accent); }
    @keyframes bar-grow { from { transform: scaleY(0); transform-origin: bottom; } to { transform: scaleY(1); transform-origin: bottom; } }

    /* Feature pills */
    .pill-row { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: 1.8rem; }
    .pill {
      display: inline-flex; align-items: center; gap: .38rem;
      padding: .3rem .75rem;
      border-radius: 99px;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.1);
      font-size: .74rem; font-weight: 500; color: rgba(255,255,255,.65);
    }
    .pill i { font-size: .65rem; color: #9d92ff; }

    .login-left-foot { font-size: .72rem; color: rgba(255,255,255,.32); }

    /* ══════════════════════════════════
       RIGHT — light form area
    ══════════════════════════════════ */
    .login-right {
      flex: 1;
      display: flex; align-items: center; justify-content: center;
      padding: 2.5rem 2rem;
      background: var(--bg);
    }

    .login-form-wrap {
      width: 100%; max-width: 380px;
      animation: form-in .48s cubic-bezier(.22,1,.36,1) both;
    }
    @media (prefers-reduced-motion: reduce) { .login-form-wrap, .mc-bar { animation: none; } }
    @keyframes form-in {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .login-form-logo {
      font-size: 1.6rem; font-weight: 800;
      letter-spacing: -.04em; color: var(--text);
      margin-bottom: 1.8rem; display: none;
    }
    .login-form-logo em { font-style: normal; color: var(--accent); }

    .login-form-title {
      font-size: 1.85rem; font-weight: 800;
      letter-spacing: -.035em; margin-bottom: .4rem; color: var(--text);
    }
    .login-form-sub {
      font-size: .9rem; color: var(--muted); margin-bottom: 2rem; line-height: 1.5;
    }

    .field { margin-bottom: 1.1rem; }
    .field label {
      display: block; font-size: .8rem; font-weight: 700;
      color: var(--text); margin-bottom: .45rem;
    }
    .field-inner { position: relative; }
    .field-inner i {
      position: absolute; left: .9rem; top: 50%;
      transform: translateY(-50%);
      color: var(--muted); font-size: .78rem; pointer-events: none;
    }
    .field input {
      width: 100%;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 11px;
      color: var(--text);
      padding: .8rem .9rem .8rem 2.5rem;
      font-size: .92rem; font-family: inherit;
      outline: none;
      box-shadow: 0 1px 2px rgba(20,24,60,.04);
      transition: border-color .15s, box-shadow .15s;
    }
    .field input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(91,77,240,.14);
    }
    .field input::placeholder { color: #b3b6cc; }

    .btn-login {
      width: 100%; margin-top: .5rem;
      background: var(--grad);
      color: #fff; border: none; border-radius: 11px;
      padding: .85rem; font-size: .95rem; font-weight: 700;
      font-family: inherit; cursor: pointer;
      box-shadow: 0 8px 20px -8px rgba(91,77,240,.75), inset 0 1px 0 rgba(255,255,255,.18);
      transition: filter .15s, box-shadow .2s, transform .1s;
    }
    .btn-login:hover { filter: brightness(1.07); }
    .btn-login:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
    .btn-login:active { transform: scale(.98); }

    .login-error {
      background: rgba(229,72,77,.08); border: 1px solid rgba(229,72,77,.22);
      border-radius: 11px; padding: .75rem .95rem;
      font-size: .85rem; font-weight: 500; color: #d13438;
      margin-bottom: 1.3rem;
      display: flex; align-items: center; gap: .5rem;
    }

    @media (max-width: 768px) {
      .login-left { display: none; }
      .login-form-logo { display: block; }
      .login-right { background: var(--bg); padding: 2.5rem 1.5rem; }
    }
  </style>
</head>
<body>
<div class="login-wrap">

  <!-- Left: dark brand panel -->
  <div class="login-left">
    <div class="login-grid"></div>

    <div class="login-left-top">
      <div class="login-logo">Win<em>Turb</em></div>
      <p class="login-tagline">Player inteligente, métricas reais,<br>conversões que aparecem.</p>

      <div class="metrics-card">
        <div class="mc-row">
          <div>
            <div class="mc-label">Plays hoje</div>
            <div class="mc-val">2.847</div>
          </div>
          <div class="mc-icon mc-icon-v"><i class="fas fa-play"></i></div>
        </div>
        <div class="mc-row" style="align-items:flex-end;">
          <div>
            <div class="mc-label" style="margin-bottom:.4rem;">Últimos 7 dias</div>
            <div class="mc-bars">
              <div class="mc-bar" style="height:35%;animation-delay:.05s"></div>
              <div class="mc-bar" style="height:55%;animation-delay:.1s"></div>
              <div class="mc-bar" style="height:42%;animation-delay:.15s"></div>
              <div class="mc-bar" style="height:70%;animation-delay:.2s"></div>
              <div class="mc-bar" style="height:58%;animation-delay:.25s"></div>
              <div class="mc-bar hi" style="height:88%;animation-delay:.3s"></div>
              <div class="mc-bar hi" style="height:100%;animation-delay:.35s"></div>
            </div>
          </div>
          <div style="text-align:right;">
            <div class="mc-label">Completados</div>
            <div style="font-size:1.15rem;font-weight:800;letter-spacing:-.02em;color:#34d399;">64%</div>
          </div>
        </div>
      </div>

      <div class="pill-row">
        <span class="pill"><i class="fas fa-shield-halved"></i> Anti-skip</span>
        <span class="pill"><i class="fas fa-bullhorn"></i> CTAs no vídeo</span>
        <span class="pill"><i class="fas fa-chart-line"></i> Analytics</span>
        <span class="pill"><i class="fas fa-code"></i> Embed</span>
      </div>
    </div>

    <div class="login-left-foot">WinTurb &copy; <?= date("Y") ?></div>
  </div>

  <!-- Right: light form -->
  <div class="login-right">
    <div class="login-form-wrap">

      <div class="login-form-logo">Win<em>Turb</em></div>

      <h1 class="login-form-title">Boas-vindas</h1>
      <p class="login-form-sub">Entre na sua conta para continuar.</p>

      <?php if ($erro ?? null) : ?>
        <div class="login-error">
          <i class="fas fa-exclamation-circle"></i>
          <?= htmlspecialchars($erro, ENT_QUOTES) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= url("login") ?>">
        <div class="field">
          <label>E-mail ou login</label>
          <div class="field-inner">
            <i class="fas fa-user"></i>
            <input type="text" name="login" autocomplete="username"
                   placeholder="seu@email.com" required>
          </div>
        </div>
        <div class="field">
          <label>Senha</label>
          <div class="field-inner">
            <i class="fas fa-lock"></i>
            <input type="password" name="senha" autocomplete="current-password"
                   placeholder="••••••••" required>
          </div>
        </div>
        <button type="submit" class="btn-login">
          Entrar <i class="fas fa-arrow-right" style="margin-left:.35rem;font-size:.8rem;"></i>
        </button>
      </form>
    </div>
  </div>

</div>
</body>
</html>

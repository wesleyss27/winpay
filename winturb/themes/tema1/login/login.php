<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar — WinTurb</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:     #f2f3f8;
      --accent: #6c5ce7;
      --accent2:#5a4bd6;
      --text:   #1e1c3a;
      --muted:  rgba(30,28,58,.42);
      --border: rgba(0,0,0,.09);
      --nav-bg: #13112b;
    }

    html, body {
      height: 100%;
      background: var(--bg);
      color: var(--text);
      font-family: 'Outfit', sans-serif;
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
      background: var(--nav-bg);
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
      background: radial-gradient(circle, rgba(108,92,231,.18) 0%, transparent 65%);
      top: -100px; right: -160px;
      border-radius: 50%; pointer-events: none;
      animation: glow-drift 9s ease-in-out infinite alternate;
    }
    .login-left::after {
      content: '';
      position: absolute;
      width: 300px; height: 300px;
      background: radial-gradient(circle, rgba(108,92,231,.1) 0%, transparent 65%);
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
      font-family: 'Syne', sans-serif;
      font-size: 1.8rem; font-weight: 800;
      letter-spacing: -.03em; color: #fff;
      margin-bottom: .35rem;
    }
    .login-logo em { font-style: normal; color: var(--accent); }
    .login-logo-dot {
      display: inline-block; width: 5px; height: 5px;
      background: var(--accent); border-radius: 50%;
      margin-left: 2px; vertical-align: middle;
      position: relative; top: -2px;
      animation: blink 2.6s ease-in-out infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
    .login-tagline {
      font-size: .88rem; color: rgba(255,255,255,.45); line-height: 1.55;
    }

    /* Analytics card */
    .metrics-card {
      margin-top: 2.6rem;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 14px;
      padding: 1.25rem 1.3rem;
      backdrop-filter: blur(8px);
      display: flex; flex-direction: column; gap: 1.1rem;
      animation: card-float 5.5s ease-in-out infinite;
    }
    @keyframes card-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-5px)} }

    .mc-row { display: flex; align-items: center; justify-content: space-between; gap: .8rem; }
    .mc-label {
      font-size: .64rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: .1em; color: rgba(255,255,255,.4);
    }
    .mc-val {
      font-family: 'Syne', sans-serif; font-size: 1.55rem; font-weight: 800;
      line-height: 1; color: #fff;
    }
    .mc-icon {
      width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: .88rem;
    }
    .mc-icon-v { background: rgba(108,92,231,.25); border: 1px solid rgba(108,92,231,.4); color: #a78bfa; }
    .mc-icon-g { background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.3);  color: #34d399; }

    .mc-bars { display: flex; align-items: flex-end; gap: 3px; height: 30px; }
    .mc-bar { width: 7px; border-radius: 3px 3px 0 0; background: rgba(108,92,231,.25); animation: bar-grow .5s cubic-bezier(.22,1,.36,1) both; }
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
      font-size: .7rem; color: rgba(255,255,255,.55);
    }
    .pill i { font-size: .62rem; color: var(--accent); }

    .login-left-foot { font-size: .68rem; color: rgba(255,255,255,.2); }

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
      width: 100%; max-width: 360px;
      animation: form-in .48s cubic-bezier(.22,1,.36,1) both;
    }
    @keyframes form-in {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .login-form-logo {
      font-family: 'Syne', sans-serif;
      font-size: 1.5rem; font-weight: 800;
      letter-spacing: -.02em; color: var(--text);
      margin-bottom: 1.8rem; display: none;
    }
    .login-form-logo em { font-style: normal; color: var(--accent); }

    .login-form-title {
      font-family: 'Syne', sans-serif;
      font-size: 1.6rem; font-weight: 800;
      letter-spacing: -.03em; margin-bottom: .35rem; color: var(--text);
    }
    .login-form-sub {
      font-size: .84rem; color: var(--muted); margin-bottom: 1.9rem; line-height: 1.5;
    }

    .field { margin-bottom: 1.1rem; }
    .field label {
      display: block; font-size: .68rem; font-weight: 700;
      letter-spacing: .1em; text-transform: uppercase;
      color: var(--muted); margin-bottom: .4rem;
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
      border: 1px solid rgba(0,0,0,.1);
      border-radius: 10px;
      color: var(--text);
      padding: .7rem .9rem .7rem 2.35rem;
      font-size: .9rem; font-family: 'Outfit', sans-serif;
      outline: none;
      box-shadow: 0 1px 3px rgba(0,0,0,.06);
      transition: border-color .15s, box-shadow .15s;
    }
    .field input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(108,92,231,.12);
    }
    .field input::placeholder { color: rgba(30,28,58,.2); }

    .btn-login {
      width: 100%; margin-top: .5rem;
      background: var(--accent);
      color: #fff; border: none; border-radius: 10px;
      padding: .78rem; font-size: .92rem; font-weight: 600;
      font-family: 'Outfit', sans-serif; cursor: pointer;
      transition: background .15s, box-shadow .2s, transform .1s;
    }
    .btn-login:hover { background: var(--accent2); box-shadow: 0 6px 20px rgba(108,92,231,.32); }
    .btn-login:active { transform: scale(.98); }

    .login-error {
      background: #fff1f2; border: 1px solid #fecdd3;
      border-radius: 10px; padding: .7rem .9rem;
      font-size: .83rem; color: #e11d48;
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
      <div class="login-logo">Win<em>Turb</em><span class="login-logo-dot"></span></div>
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
            <div style="font-size:1.1rem;font-family:'Syne',sans-serif;font-weight:800;color:#34d399;">64%</div>
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

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'Central WinPay' ?></title>

  <!-- Preloads do player -->
  <script>!function(i,n){i._plt=i._plt||(n&&n.timeOrigin?n.timeOrigin+n.now():Date.now())}(window,performance);</script>
  <link rel="preload" href="https://scripts.converteai.net/371d759e-bebd-4f17-a05b-aaf1d312f5cb/players/6851e910bf4e89bbabab9af3/v4/player.js" as="script">
  <link rel="preload" href="https://scripts.converteai.net/lib/js/smartplayer-wc/v4/smartplayer.js" as="script">
  <link rel="preload" href="https://cdn.converteai.net/371d759e-bebd-4f17-a05b-aaf1d312f5cb/6851e8f8949bb54701ac22cc/main.m3u8" as="fetch">
  <link rel="dns-prefetch" href="https://cdn.converteai.net">
  <link rel="dns-prefetch" href="https://scripts.converteai.net">
  <link rel="dns-prefetch" href="https://images.converteai.net">
  <link rel="dns-prefetch" href="https://api.vturb.com.br">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/bootstrap/css/bootstrap.min.css") ?>">
  <link rel="icon" href="<?= url("themes/imgs/logos/logo-icon.png") ?>">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: #0d0d0d;
      font-family: 'Inter', 'Segoe UI', sans-serif;
      color: #f0f0f0;
      min-height: 100vh;
    }

    .central-body {
      max-width: 860px;
      margin: 0 auto;
      padding: 40px 20px 60px;
    }

    .central-title {
      text-align: center;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: #fff;
      letter-spacing: 0.02em;
    }

    .central-subtitle {
      text-align: center;
      font-size: 0.95rem;
      color: #888;
      margin-bottom: 32px;
    }

    .video-wrapper {
      position: relative;
      width: 100%;
      padding-bottom: 56.25%;
      background: #000;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(0,0,0,0.6);
    }

    .video-wrapper smartplayer,
    .video-wrapper #smartplayer {
      position: absolute;
      top: 0; left: 0;
      width: 100% !important;
      height: 100% !important;
    }

    .btn-prints {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      max-width: 380px;
      margin: 36px auto 0;
      padding: 16px 32px;
      font-size: 1.05rem;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
      border: none;
      border-radius: 10px;
      text-decoration: none;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(79,70,229,0.4);
      transition: transform 0.15s, box-shadow 0.15s;
    }

    .btn-prints:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 28px rgba(79,70,229,0.55);
      color: #fff;
      text-decoration: none;
    }

    .btn-prints i {
      font-size: 1.2rem;
    }

  </style>
</head>
<body>

  <?= $v->section("content") ?>

  <!-- Bootstrap JS -->
  <script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js") ?>"></script>
  <script src="<?= url("themes/tema1/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>

  <?= $v->section("scripts") ?>
</body>
</html>

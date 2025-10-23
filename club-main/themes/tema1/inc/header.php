<!DOCTYPE html>
<html lang="pt-br">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css?" . time()) ?>">
  <script src="https://kit.fontawesome.com/b3da9d1491.js" crossorigin="anonymous"></script>

  <!-- Theme style -->
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/adminlte.min.css") ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/overlayScrollbars/css/OverlayScrollbars.min.css") ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/daterangepicker/daterangepicker.css") ?>">
 
  <link href="<?= url("themes/tema1/dist/css/font-inter.css") ?>" rel="stylesheet"><link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
  <link href="<?= url("themes/tema1/dist/css/custom/aplication-fb.css") ?>" rel="stylesheet">  
   
  <link rel="icon" href="<?= url("themes/imgs/logo-icon.png?" . time()) ?>">

  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/geral.css?" . time()) ?>">
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/cores.css?" . time()) ?>">
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/modern-dashboard.css?" . time()) ?>">
  <style>
    body {
      background: linear-gradient(180deg, #040406 0%, #0f1118 100%);
      color: #f9fafb;
    }

    .wrapper {
      background: transparent;
    }

    .content-wrapper {
      background: transparent !important;
      padding: clamp(1.4rem, 2vw, 2rem);
      min-height: calc(100vh - 57px);
      position: relative;
    }

    .content-wrapper::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 25% 15%, rgba(244, 208, 63, 0.12), transparent 55%);
      pointer-events: none;
      z-index: -1;
    }

    .content,
    .content > .container-fluid {
      background: transparent !important;
    }

      .main-header.navbar {
      background: linear-gradient(90deg, rgba(5, 5, 8, 0.96) 0%, rgba(9, 12, 18, 0.96) 60%, rgba(15, 18, 28, 0.96) 100%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      color: #f9fafb;
      backdrop-filter: blur(16px);
    }

    .main-header .nav-link,
    .main-header .navbar-nav > li > a {
      color: #f9fafb !important;
      transition: color 0.2s ease;
    }

    .main-header .nav-link:hover {
      color: #f4d03f !important;
    }

        .main-footer {
      background: linear-gradient(90deg, rgba(5, 5, 8, 0.96) 0%, rgba(9, 12, 18, 0.96) 60%, rgba(15, 18, 28, 0.96) 100%) !important;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
      color: #f9fafb !important;
    }
  </style>
  <?= $v->section("links") ?>

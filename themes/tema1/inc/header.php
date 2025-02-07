<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, follow">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Google Font: Source Sans Pro -->
  <!--link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"-->
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/fontawesome-free/css/all.min.css") ?>">
  <!-- Ionicons -->
  <!--link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"-->
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css") ?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/icheck-bootstrap/icheck-bootstrap.min.css") ?>">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/jqvmap/jqvmap.min.css") ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/adminlte.min.css") ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/overlayScrollbars/css/OverlayScrollbars.min.css") ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/daterangepicker/daterangepicker.css") ?>">
  <!-- summernote -->
  <link rel="stylesheet" href="<?= url("themes/tema1/plugins/summernote/summernote-bs4.min.css") ?>">

  <link href="<?= url("themes/tema1/dist/css/font-inter.css") ?>" rel="stylesheet">
  <link href="<?= url("themes/tema1/dist/css/font-lobster.css") ?>" rel="stylesheet">
  <link rel="icon" href="<?= url("themes/imgs/logos/logo-icon.png") ?>">

  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/geral.css?" . time()) ?>">
  <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/cores.css?" . time()) ?>">
  <?= $v->section("links") ?>

  <?php if (AMBIENTE == "produção") : ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-HLZ80Q2Z7K"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());

      gtag('config', 'G-HLZ80Q2Z7K');
    </script> -->
  <?php endif; ?>
  
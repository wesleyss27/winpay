<?php include_once "inc/header.php"; ?>
<title><?= $title ?></title>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<?php include_once "inc/navbar_topo.php"; ?>
<?php include_once "inc/sidebar_menu.php"; ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1><?= $title ?></h1>
      <form method="POST" style="margin-top:20px;">
        <label>Token do Dispositivo:</label>
        <textarea name="token" class="form-control" rows="3"><?= htmlspecialchars($token ?? '') ?></textarea>

        <label>Título:</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($titleMsg ?? '') ?>">

        <label>Mensagem:</label>
        <input type="text" name="body" class="form-control" value="<?= htmlspecialchars($body ?? '') ?>">

        <button type="submit" class="btn btn-primary mt-2">Enviar Notificação</button>

        <?php if (!empty($result)): ?>
          <div class="alert alert-success mt-2"><?= $result ?></div>
        <?php elseif (!empty($error)): ?>
          <div class="alert alert-danger mt-2"><?= $error ?></div>
        <?php endif; ?>
      </form>
    </div>
  </section>
</div>

<?php include_once "inc/footer.php"; ?>

<div class="not">
  <?php

  use Source\Models\Notificacao\Notificacao;

  $ultimas = (new Notificacao())->find("usuario = " . $_SESSION["id"])->limit(5)->order("id DESC")->fetch(true);
  $conta = (new Notificacao())->find("lida = 0 and usuario = " . $_SESSION["id"])->count();
  ?>

  <li class="nav-item dropdown notificacao">
    <a class="nav-link" data-toggle="dropdown" href="#">
      <i class="far fa-bell"></i>
      <span class="badge bg-vermelho-gradient navbar-badge"><?= $conta ?></span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

      <?php if ($ultimas) : ?>
        <span class="dropdown-item dropdown-header"><?= $conta ?> Notificações</span>
        <div class="dropdown-divider"></div>

        <?php foreach ($ultimas as $notificacao) : ?>
          <a href="<?= $notificacao->link ?>" class="dropdown-item">
            <p>
              <img src="<?= url("themes/imgs/logos/logo5.png?" . time()) ?>" alt="" width="70px">
              <span style="color: #023770"><b><?= $notificacao->titulo ? $notificacao->titulo : substr($notificacao->texto, 0, 20) . "..." ?></b></span><br>
              <?= $notificacao->texto ?>
            </p>
          </a>
          <div class="dropdown-divider"></div>
        <?php endforeach; ?>

        <a href="<?= url("notificacoes") ?>" class="dropdown-item dropdown-footer">Ver todas</a>

      <?php else : ?>
        <span class="dropdown-item dropdown-header">Nenhuma notificação</span>
      <?php endif; ?>
    </div>
  </li>
</div>

<div class="notifications card">
  <div class="card-body alert bg-white alert-dismissible fade show" role="alert">
    <a href="" id="link_notificacao" style="text-decoration: none">
      <img src="<?= url("themes/imgs/logos/logo5.png?" . time()) ?>" alt="" width="70px"><br>
      <span id="msg"></span>
    </a>

    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
</div>

<style>
  .notifications {
    position: absolute;
    z-index: 1000;
    right: 10px;
    top: 40px;
    width: 50%;
    display: none
  }
</style>

<script>
  //$(".alert").alert('close')
</script>
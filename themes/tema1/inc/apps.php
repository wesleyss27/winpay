<div class="not">

  <li class="nav-item dropdown notificacao">
    <a class="nav-link" data-toggle="dropdown" href="#">
      <i class="fas fa-th"></i>
      <span class="badge bg-vermelho-gradient navbar-badge">Novo</span>
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <a href="<?= url_club() ?>" class="dropdown-item" target="_blank">
        <p>
          <img src="<?= url("themes/imgs/logos/logo-icon.png") ?>" alt="" width="20px">
          <span>Club WINPAY</span>
        </p>
      </a>
      <!--
      <div class="dropdown-divider"></div>

      <a href="<?= url("ecommerce") ?>" class="dropdown-item">
        <p>
          <img src="<?= url("themes/imgs/logos/logo-icon.png") ?>" alt="" width="20px">
          <span>Ecommerce</span>
          <span class="badge bg-vermelho-gradient">Novo</span>
        </p>
      </a>
    </div>
-->

  </li>
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

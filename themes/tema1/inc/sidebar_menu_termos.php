<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-image: linear-gradient(to bottom, #0b0024, #b73234, #b73234)">
  <?php if (@$_SESSION["tipo"] != 6) : ?>
    <a href="<?= url() ?>" class="brand-link" style="border:0">
      <div align="center"><img src="<?= url("themes/imgs/logos/logo4.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div>
    </a>
  <?php else: ?>
    <div align="center"><img src="<?= url("themes/imgs/logos/logo4.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div>
  <?php endif ?>
  <div class="sidebar">

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="font-family:inter">
        <li class="nav-item">
          <a href="<?= url() ?>" class="nav-link text-white">
            <i class="nav-icon fas fa-th"></i>
            <p>Início </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('ajuda-usuarios') ?>" class="nav-link text-white">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Termos Usuários</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('termos-uso') ?>" class="nav-link text-white">
            <i class="nav-icon fas fa-edit"></i>
            <p>Termos de Uso</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('termos-compra') ?>" class="nav-link text-white">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>Termos de Compra</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('politica-privacidade') ?>" class="nav-link text-white">
            <i class="nav-icon fas fa-user-lock"></i>
            <p>Política de Privacidade</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('politica-disputa') ?>" class="nav-link text-white">
            <i class="nav-icon fas fa-people-arrows"></i>
            <p>Resolução de Disputa</p>
          </a>
        </li>

      </ul>
    </nav>

  </div>
</aside>
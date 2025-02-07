  <nav class="main-header navbar navbar-expand bg-white">

    <ul class="navbar-nav">
      <li class="nav-item">
        <a id="push-menu" class="nav-link desktop" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" id="icone-menu-bar"></i></a><!-- bars -->
      </li>
    </ul>

    <!--<form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Buscar..." aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>-->

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a href="<?= url("usuarios/perfil/" . $_SESSION["id"]) ?>" class="dropdown-item dropdown-footer">
          <?php if (@$_SESSION["foto"]) : ?>
            <img src="<?= url_octa("uploads/perfil/{$_SESSION["foto"]}"); ?>" alt="" width="30px" style="border-radius: 50%">
          <?php else : ?>
            <i class="far fa-user"></i>
          <?php endif; ?>
          <?= $_SESSION["nome_abreviado"] ?>
          <!-- <span class="badge badge-danger navbar-badge">3</span>-->
        </a>
      </li>

      <!-- Notifications Dropdown Menu -->
      <!-- < ?php $v->insert("inc/notificacao") ?> -->

      <!-- <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li> -->

      <li class="nav-item">
        <a class="nav-link" href="?action=sair" role="button" title="Sair">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>

      <!--<li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>-->
    </ul>
  </nav>
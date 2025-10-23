
  <style>
    .winpay-navbar {
      background: linear-gradient(90deg, rgba(5, 5, 8, 0.96) 0%, rgba(9, 12, 18, 0.96) 60%, rgba(15, 18, 28, 0.96) 100%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(16px);
      padding: 0.75rem 1rem;
    }

    .winpay-navbar .nav-link {
      color: #f9fafb;
      transition: color 0.2s ease, transform 0.2s ease;
    }

    .winpay-navbar .nav-link:hover {
      color: #f4d03f;
    }

    .winpay-toggle-icon {
      display: inline-flex;
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: rgba(244, 208, 63, 0.1);
      align-items: center;
      justify-content: center;
      box-shadow: inset 0 0 0 1px rgba(244, 208, 63, 0.12);
    }

    .winpay-profile-link {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      padding: 0.35rem 0.65rem;
      border-radius: 14px;
      background: rgba(244, 208, 63, 0.08);
      color: #f9fafb !important;
      transition: background 0.2s ease;
    }

    .winpay-profile-link:hover {
      background: rgba(244, 208, 63, 0.14);
      color: #f4d03f !important;
    }

    .winpay-avatar {
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
      object-fit: cover;
    }

    .winpay-avatar-placeholder {
      width: 32px;
      height: 32px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: rgba(244, 208, 63, 0.18);
      color: rgba(244, 208, 63, 0.9);
      font-size: 1.1rem;
    }

    .winpay-profile-name {
      font-weight: 500;
      letter-spacing: 0.02em;
    }

    .winpay-logout {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.35rem 0.75rem;
      border-radius: 14px;
      background: rgba(244, 208, 63, 0.08);
      color: #f9fafb !important;
    }

    .winpay-logout:hover {
      background: rgba(244, 208, 63, 0.14);
      color: #f4d03f !important;
    }
  </style>

  <nav class="main-header navbar navbar-expand navbar-dark winpay-navbar">

    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link menu-toggle" data-widget="pushmenu" href="#" role="button" aria-label="Alternar menu lateral">
          <span class="winpay-toggle-icon"><i class="fas fa-chevron-left menu-toggle-icon"></i></span>
        </a>
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
        <a href="<?= url("usuarios/perfil/" . $_SESSION["id"]) ?>" class="dropdown-item dropdown-footer winpay-profile-link">
          <?php if (@$_SESSION["foto"]) : ?>
            <img src="<?= url_octa("uploads/perfil/{$_SESSION["foto"]}"); ?>" alt="" width="32" height="32" class="winpay-avatar">
          <?php else : ?>
            <span class="winpay-avatar-placeholder"><i class="far fa-user"></i></span>
          <?php endif; ?>
          <span class="winpay-profile-name"><?= $_SESSION["nome_abreviado"] ?></span>
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
        <a class="nav-link winpay-logout" href="?action=sair" role="button" title="Sair">
          <span>Sair</span>
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

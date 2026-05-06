
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
      color: #e2e8f0;
    }

    .winpay-toggle-icon {
      display: inline-flex;
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: rgba(226, 232, 240, 0.1);
      align-items: center;
      justify-content: center;
      box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.12);
    }

    .winpay-profile-link {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      padding: 0.35rem 0.65rem;
      border-radius: 14px;
      background: rgba(226, 232, 240, 0.08);
      color: #f9fafb !important;
      transition: background 0.2s ease;
    }

    .winpay-profile-link:hover {
      background: rgba(226, 232, 240, 0.14);
      color: #e2e8f0 !important;
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
      background: rgba(226, 232, 240, 0.18);
      color: rgba(226, 232, 240, 0.9);
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
      background: rgba(226, 232, 240, 0.08);
      color: #f9fafb !important;
    }

    .winpay-logout:hover {
      background: rgba(226, 232, 240, 0.14);
      color: #e2e8f0 !important;
    }

    .winpay-search {
      position: relative;
      display: none;
    }

    @media (min-width: 768px) {
      .winpay-search {
        display: block;
      }
    }

    .winpay-search input {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      color: #f9fafb;
      padding: 0.35rem 0.9rem 0.35rem 2.2rem;
      font-size: 0.82rem;
      width: 200px;
      outline: none;
      transition: border-color 0.2s ease, background 0.2s ease, width 0.3s ease;
    }

    .winpay-search input::placeholder {
      color: rgba(148, 163, 184, 0.4);
    }

    .winpay-search input:focus {
      border-color: rgba(226, 232, 240, 0.35);
      background: rgba(255, 255, 255, 0.08);
      width: 260px;
    }

    .winpay-search-icon {
      position: absolute;
      left: 0.65rem;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(148, 163, 184, 0.4);
      font-size: 0.75rem;
      pointer-events: none;
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

    <div class="winpay-search ml-3">
      <i class="fas fa-search winpay-search-icon"></i>
      <input type="search" placeholder="Buscar..." aria-label="Buscar" autocomplete="off" id="winpay-search-input">
    </div>

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

<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-image: linear-gradient(to bottom, #0b0024, #03207c, #0b0024);">  -->
<style>
  :root {
    --sidebar-bg: #050507;
    --sidebar-border: rgba(255, 255, 255, 0.06);
    --sidebar-text: #f9fafb;
    --sidebar-muted: #9ca3af;
    --sidebar-accent: #f4d03f;
    --sidebar-accent-soft: rgba(244, 208, 63, 0.16);
    --sidebar-width: 250px;
    --sidebar-width-collapsed: 5.2rem;
  }

  #aside {
    background: linear-gradient(180deg, rgba(15, 17, 24, 0.95) 0%, rgba(5, 5, 8, 0.98) 55%, rgba(5, 5, 8, 1) 100%) !important;
    border-right: 1px solid var(--sidebar-border);
    font-family: 'Inter', sans-serif;
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    overflow: hidden;
    z-index: 1060;
    transition: width 0.3s ease, margin-left 0.3s ease;
  }

  #aside::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 20% 15%, rgba(244, 208, 63, 0.25), transparent 60%);
    opacity: 0.7;
    pointer-events: none;
  }

  #aside .brand-logo-sidebar {
    position: relative;
    z-index: 1;
    padding: 2rem 1.8rem 1.4rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  }

  .brand-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.65rem;
  }

  .brand-badge-icon {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
  }

  .brand-badge-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .brand-badge-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
  }

  .brand-badge-name {
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 0.2em;
    color: var(--sidebar-text);
  }

  .brand-badge-subtitle {
    font-size: 0.7rem;
    letter-spacing: 0.34em;
    text-transform: uppercase;
    color: rgba(244, 208, 63, 0.75);
  }

  #aside .sidebar {
    padding: 1.6rem 1.2rem;
    position: relative;
    z-index: 1;
    height: calc(100vh - 132px);
    overflow-y: auto;
  }

  #aside .nav {
    gap: 0.5rem;
  }

  #aside .nav-item {
    margin-bottom: 0.3rem;
  }

  #aside .nav-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 0.9rem 1.05rem;
    border-radius: 14px;
    color: var(--sidebar-muted) !important;
    font-weight: 500;
    font-size: 0.92rem;
    transition: 0.2s ease;
    background: transparent;
    border: 1px solid transparent;
  }

  #aside .nav-link::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: linear-gradient(90deg, rgba(244, 208, 63, 0.22), transparent);
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
  }

  #aside .nav-link .nav-icon {
    font-size: 1.1rem;
    color: inherit;
    min-width: 1.1rem;
    text-align: center;
  }

  #aside .nav-link:hover {
    color: var(--sidebar-text) !important;
    border-color: rgba(244, 208, 63, 0.18);
    background: rgba(12, 13, 20, 0.72);
  }

  #aside .nav-link:hover::before {
    opacity: 0.45;
  }

  #aside .nav-link.active {
    color: #050507 !important;
    background: linear-gradient(95deg, rgba(244, 208, 63, 0.95), rgba(244, 208, 63, 0.78)) !important;
    border: none;
    box-shadow: 0 14px 28px rgba(244, 208, 63, 0.22);
  }

  #aside .nav-link.active .nav-icon {
    color: inherit !important;
  }

  #aside .nav-link.active::before {
    display: none;
  }

  #aside .nav-link .badge {
    margin-left: auto;
    background: rgba(244, 208, 63, 0.15);
    color: var(--sidebar-accent);
    border-radius: 999px;
    padding: 0.1rem 0.5rem;
    font-size: 0.7rem;
  }

  #aside .nav-section {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    color: rgba(244, 208, 63, 0.6);
    text-transform: uppercase;
    margin: 1.6rem 0 0.8rem 0.75rem;
    transition: opacity 0.2s ease;
  }

  #aside .sidebar::-webkit-scrollbar {
    width: 6px;
  }

  #aside .sidebar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.24);
    border-radius: 3px;
  }

  .layout-fixed .content-wrapper,
  .layout-fixed .main-header,
  .layout-fixed .main-footer {
    margin-left: var(--sidebar-width);
    transition: margin-left 0.3s ease;
  }

  body.sidebar-collapse #aside {
    width: var(--sidebar-width-collapsed);
    overflow: visible;
    margin-left: 0;
  }

  body.sidebar-collapse #aside .brand-logo-sidebar {
    padding: 1.2rem 0;
    text-align: center;
  }

  body.sidebar-collapse #aside .brand-badge {
    justify-content: center;
  }

  body.sidebar-collapse #aside .brand-badge-text {
    display: none;
  }

  body.sidebar-collapse #aside .brand-title {
    font-size: 0;
  }

  body.sidebar-collapse #aside .brand-title::after {
    content: "WH";
    font-size: 1.1rem;
    letter-spacing: 0.2em;
    color: var(--sidebar-accent);
  }

  body.sidebar-collapse #aside .nav-link {
    justify-content: center;
    width: var(--sidebar-width-collapsed);
    position: relative;
    padding: 0.9rem 0.5rem;
  }

  body.sidebar-collapse #aside .menu-label,
  body.sidebar-collapse #aside .nav-section,
  body.sidebar-collapse #aside .nav-link .badge {
    display: none;
  }

  body.sidebar-collapse #aside .nav-link::after {
    content: attr(data-label);
    position: absolute;
    left: calc(var(--sidebar-width-collapsed) + 12px);
    top: 50%;
    transform: translateY(-50%);
    background: rgba(12, 13, 20, 0.92);
    border: 1px solid rgba(244, 208, 63, 0.18);
    border-radius: 12px;
    padding: 0.45rem 0.8rem;
    font-size: 0.85rem;
    color: #f9fafb;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease, transform 0.2s ease;
    white-space: nowrap;
  }

  body.sidebar-collapse #aside .nav-link:hover::after {
    opacity: 1;
    transform: translateY(-50%) translateX(4px);
  }
  body.sidebar-collapse #aside .menu-label {
    display: none !important;
  }


  body.sidebar-collapse .content-wrapper,
  body.sidebar-collapse .main-header,
  body.sidebar-collapse .main-footer {
    margin-left: var(--sidebar-width-collapsed);
  }

@media (max-width: 991.98px) {
    #aside {
      margin-left: calc(var(--sidebar-width) * -1);
      width: var(--sidebar-width);
    }

    body.sidebar-open #aside {
      margin-left: 0;
    }

    body.sidebar-open {
      overflow: hidden;
    }

    body.sidebar-open .content-wrapper,
    body.sidebar-open .main-header,
    body.sidebar-open .main-footer {
      margin-left: 0;
    }

    body.sidebar-collapse #aside {
      margin-left: calc(var(--sidebar-width) * -1);
      width: var(--sidebar-width);
    }

    body.sidebar-collapse .content-wrapper,
    body.sidebar-collapse .main-header,
    body.sidebar-collapse .main-footer {
      margin-left: 0;
    }
  }
</style>
<aside id="aside" class="main-sidebar sidebar-dark-primary elevation-4">
  <?php if ($_SESSION["tipo"] != 6) : ?>
    <div class="brand-logo-sidebar">
      <div class="brand-badge">
        <div class="brand-badge-icon">
          <img src="<?= url("themes/imgs/logo-win.png") ?>" alt="Win Hub logo">
        </div>
        <div class="brand-badge-text">
          <span class="brand-badge-name">WIN HUB</span>
          <span class="brand-badge-subtitle">LEARNING</span>
        </div>
      </div>
    </div>
  <?php elseif ($_SESSION["tipo"] == 6) : ?>
    <div class="brand-logo-sidebar">
      <div class="brand-badge">
        <div class="brand-badge-icon">
          <img src="<?= url("themes/imgs/logo-win.png") ?>" alt="Win Hub logo">
        </div>
        <div class="brand-badge-text">
          <span class="brand-badge-name">WIN HUB</span>
          <span class="brand-badge-subtitle">LEARNING</span>
        </div>
      </div>
    </div>
    <!-- <div align="center"><img src="<?= url("themes/imgs/logo5.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div> -->
  <?php endif ?>
  <div class="sidebar">

    <br>

    <nav class="mt-2">
      <div class="nav-section">Navegação</div>
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <?php if ($_SESSION["tipo"] != 6) : ?>
          <li class="nav-item" title="Veja os produtos cadastrados por você">
            <a href="<?= url('cursos') ?>" class="nav-link txt-azul-dark">
              <i class="fa-solid fa-table nav-icon"></i>
              <span class="menu-label" data-label="Cursos">Cursos</span>
            </a>
          </li>

          <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
            <li class="nav-item" title="Veja os produtos cadastrados por você">
              <a href="<?= url('cursos/cursos-cadastrados') ?>" class="nav-link txt-azul-dark">
                <i class="fa-solid fa-table nav-icon"></i>
                <span class="menu-label" data-label="Cursos Cadastrados">Cursos Cadastrados</span>
              </a>
            </li>
          <?php endif ?>

          <li class="nav-item" title="Cadastre um novo produto">
            <a href="<?= url('cursos/novo') ?>" class="nav-link txt-azul-dark">
              <i class="fas fa-book-medical nav-icon"></i>
              <span class="menu-label" data-label="Criar Curso">Criar Curso</span>
            </a>
          </li>

          <li class="nav-item" title="Cadastre um novo produto">
            <a href="<?= url('turmas') ?>" class="nav-link txt-azul-dark">
              <i class="fa-solid fa-users nav-icon"></i>
              <span class="menu-label" data-label="Turmas">Turmas</span>
            </a>
          </li>

          <li class="nav-item" title="Biblioteca de vídeos hospedados">
            <a href="<?= url('videos') ?>" class="nav-link txt-azul-dark">
              <i class="fas fa-film nav-icon"></i>
              <span class="menu-label" data-label="Meus Vídeos">Meus Vídeos</span>
            </a>
          </li>

          <li class="nav-item" title="Veja suas compras">
            <a href="<?= url('minhas-compras/') ?>" class="nav-link txt-azul-dark">
              <i class="fa-solid fa-basket-shopping nav-icon"></i>
              <span class="menu-label" data-label="Minhas Compras">Minhas Compras</span>
            </a>
          </li>

        <?php endif ?>

        <?php if ($_SESSION["tipo"] == 6) : ?>
          <li class="nav-item" title="Veja suas compras">
            <a href="<?= url('minhas-compras/') ?>" class="nav-link txt-azul-dark">
              <i class="nav-icon fas fa-check"></i>
              <p> Minhas Compras </p>
            </a>
          </li>

          <li class="nav-item" title="Veja e edite seus dados pessoais">
            <a href="<?= url('usuarios/perfil/' . $_SESSION["id"]) ?>" class="nav-link txt-azul-dark">
              <i class="nav-icon fas fa-user"></i>
              <p> Meus dados </p>
            </a>
          </li>
        <?php endif ?>
      </ul>
    </nav>

  </div>
</aside>

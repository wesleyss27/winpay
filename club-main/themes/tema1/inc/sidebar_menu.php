<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-image: linear-gradient(to bottom, #0b0024, #03207c, #0b0024);">  -->
<style>
  #aside .nav-link.active {
    background: #000 !important;
  }

  .card {
    border: 1px solid #000;
  }

  /* Sidebar principal com fundo preto */
  #aside {
    background-color: #000 !important;
    color: #fff;
  }

  /* Estilo do logo */
  #aside .brand-link {
    border: none;
    padding: 1rem;
    text-align: center;
  }

  /* Lista de itens */
  #aside .nav-link {
    color: #ccc !important;
    font-weight: 500;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    font-size: 15px;
    transition: 0.2s ease;
  }

  /* Ícones */
  #aside .nav-link .nav-icon {
    margin-right: 10px;
    font-size: 16px;
    color: #ccc !important;
  }

  /* Hover */
  #aside .nav-link:hover {
    background-color: #111 !important;
    color: #FFD700 !important;
  }

  #aside .nav-link:hover .nav-icon {
    color: #FFD700 !important;
  }

  /* Ativo */
  #aside .nav-link.active {
    background-color: #FFD700 !important;
    color: #000 !important;
    font-weight: bold;
    border-radius: 4px;
  }

  #aside .nav-link.active .nav-icon {
    color: #000 !important;
  }

  /* Espaçamento entre itens */
  #aside .nav-item {
    margin-bottom: 4px;
  }

  /* Remover bordas extras */
  .sidebar {
    border: none;
  }

  /* Scroll suave */
  .sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar::-webkit-scrollbar-thumb {
    background: #333;
    border-radius: 3px;
  }

  .brand-logo-sidebar {
  text-align: left;
  padding: 20px 20px 10px;
  font-family: 'Inter', sans-serif;
}

.brand-title {
  color: #FFD700;
  font-size: 22px;
  font-weight: bold;
  letter-spacing: 1px;
  display: block;
}

.brand-title .highlight {
  color: #FFD700; /* mesmo tom do restante, para manter tudo amarelo */
}

/* Itens do menu */
#aside .nav-link {
  color: #ccc !important;
  font-weight: 500;
  padding: 12px 20px;
  font-size: 14px;
  transition: 0.2s ease;
}

#aside .nav-link .nav-icon {
  font-size: 16px;
  margin-right: 10px;
  color: #ccc !important;
}

#aside .nav-link:hover {
  background-color: #111 !important;
  color: #FFD700 !important;
}

#aside .nav-link:hover .nav-icon {
  color: #FFD700 !important;
}

#aside .nav-link.active {
  background-color: #FFD700 !important;
  color: #000 !important;
  font-weight: bold;
}

#aside .nav-link.active .nav-icon {
  color: #000 !important;
}


</style>
<aside id="aside" class="main-sidebar sidebar-dark-primary elevation-4 bg-black">
  <?php if ($_SESSION["tipo"] != 6) : ?>
    <div class="brand-logo-sidebar">
      <span class="brand-title">WIN <span class="highlight">HUB</span></span>
    </div>

  <?php elseif ($_SESSION["tipo"] == 6) : ?>
    <div class="brand-logo-sidebar">
      <span class="brand-title">WIN <span class="highlight">HUB</span></span>
    </div>
    <!-- <div align="center"><img src="<?= url("themes/imgs/logo5.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div> -->
  <?php endif ?>
  <div class="sidebar">

    <br>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="font-family:inter">

        <?php if ($_SESSION["tipo"] != 6) : ?>
          <li class="nav-item" title="Veja os produtos cadastrados por você">
            <a href="<?= url('cursos') ?>" class="nav-link txt-azul-dark">
              <i class="fa-solid fa-table nav-icon"></i>
              <p>Cursos</p>
            </a>
          </li>

          <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
            <li class="nav-item" title="Veja os produtos cadastrados por você">
              <a href="<?= url('cursos/cursos-cadastrados') ?>" class="nav-link txt-azul-dark">
                <i class="fa-solid fa-table nav-icon"></i>
                <p>Cursos Cadastrados</p>
              </a>
            </li>
          <?php endif ?>

          <li class="nav-item" title="Cadastre um novo produto">
            <a href="<?= url('cursos/novo') ?>" class="nav-link txt-azul-dark">
              <i class="fas fa-book-medical nav-icon"></i>
              <p>Criar Curso</p>
            </a>
          </li>

          <li class="nav-item" title="Cadastre um novo produto">
            <a href="<?= url('turmas') ?>" class="nav-link txt-azul-dark">
              <i class="fa-solid fa-users nav-icon"></i>
              <p>Turmas</p>
            </a>
          </li>

          <li class="nav-item" title="Veja suas compras">
            <a href="<?= url('minhas-compras/') ?>" class="nav-link txt-azul-dark">
              <i class="fa-solid fa-basket-shopping nav-icon"></i>
              <p> Minhas Compras </p>
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
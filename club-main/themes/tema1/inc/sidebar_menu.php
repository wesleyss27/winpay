<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-image: linear-gradient(to bottom, #0b0024, #03207c, #0b0024);">  -->
<style>
  #aside .nav-link.active {
    background: #ddd !important;
  }

  .card {
    border: 1px solid #ddd;
  }
</style>
<aside id="aside" class="main-sidebar sidebar-dark-primary elevation-4 bg-white">
  <?php if ($_SESSION["tipo"] != 6) : ?>
    <a href="<?= url() ?>" class="brand-link" style="border:0">
      <div align="center"><img id="logo-menu" src="<?= url("themes/imgs/logo5.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div>
    </a>
  <?php elseif ($_SESSION["tipo"] == 6) : ?>
    <div align="center"><img src="<?= url("themes/imgs/logo5.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div>
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
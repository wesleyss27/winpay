  <?php

  use Source\Models\Permissao\Permissao;
  use Source\Models\Permissao\Section;

  $section = new Section();
  $contas = (new Permissao())->find("usuario_permitido = " . @$_SESSION["id"])->fetch(true);
  ?>

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
      <li class="nav-item">
        <a id="push-menu" class="nav-link desktop" data-widget="pushmenu" href="#" role="button"><i class="fas fa-chevron-left" id="icone-menu-bar"></i></a><!-- bars -->
        <a class="nav-link mobile" id="push-menu" data-widget="pushmenu" href="#" role="button"><i class="fas fa-chevron-left" id="icone-menu-bar"></i></a><!-- bars -->
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
      <!-- <li class="nav-item m-none">
        <span class="nav-link">
          <?= @$_SESSION["verificado"] == 1 ? "<i class='fas fa-thumbs-up text-success'></i> verificado" : "<i class='fas fa-clock text-warning'></i> aguardando análise" ?>
        </span>
      </li> -->

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a href="#" class="dropdown-item dropdown-footer" data-toggle="dropdown">
          <?php if (@$section->getFoto()) : ?>
            <img src="<?= url("uploads/perfil/{$section->getFoto()}"); ?>" alt="" width="30px" style="border-radius: 50%">
          <?php else : ?>
            <i class="far fa-user"></i>
          <?php endif; ?>
          <?= $section->getNomeAbreviado() ?>
          <!-- <span class="badge badge-danger navbar-badge">3</span>-->
        </a>

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="<?= url("usuarios/perfil/" . $_SESSION["id"]) ?>" class="dropdown-item">
            <span style="color: #023770">Minha Conta</span>
          </a>
          <div class="dropdown-divider"></div>

          <a href="<?= url("permissao/usuarios") ?>" class="dropdown-item">
            <span style="color: #023770">Permissões</span>
          </a>
          <div class="dropdown-divider"></div>

          <?php if (@$_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
            <a href="<?= url("permissao/paginas") ?>" class="dropdown-item">
              <span style="color: #023770">Páginas de Permissões</span>
            </a>
            <div class="dropdown-divider"></div>
          <?php endif; ?>

          <?php
          if ($contas && !@$_SESSION["subusuario"]) :
            foreach ($contas as $conta) :
          ?>
              <a href="<?= url("permissao/logar/$conta->id") ?>" class="dropdown-item">
                <div class="row">
                  <div class="col-md-2">
                    <img src="<?= url("uploads/perfil/{$conta->dataUsuarioOrigem()->foto}"); ?>" alt="" width="30px" style="border-radius: 50%">
                  </div>
                  <div class="col-md-10">
                    <span style="color: #023770"><?= $conta->dataUsuarioOrigem()->nome ?></span><br>
                    <small>Visualizar Conta</small>
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          <?php elseif (@$_SESSION["subusuario"]) : ?>
            <a href="?action=sair_sub" class="dropdown-item">
              <span style="color: #023770">Gerenciar Conta Principal</span>
            </a>
            <div class="dropdown-divider"></div>
          <?php endif; ?>
          <!-- <div class="dropdown-divider"></div> -->
        </div>
      </li>

      <!-- Notifications Dropdown Menu -->
      <?php $v->insert("inc/notificacao") ?>


      <?php $v->insert("inc/apps") ?>

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
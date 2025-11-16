<?php

use Source\Models\Permissao\Permissao;
use Source\Models\Permissao\Section;

$section = new Section();
$contas = (new Permissao())->find("usuario_permitido = " . @$_SESSION["id"])->fetch(true);
?>

<style>
  .navbar-modern {
    background: #060606;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    padding: 0 24px;
    min-height: 64px;
  }

  .navbar-modern .nav-link {
    color: #f4f4f4;
    transition: color .2s ease;
  }

  .navbar-modern .nav-link:hover {
    color: #ffdd00;
  }

  .navbar-modern .toggle-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.02);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background .2s ease, border .2s ease;
  }

  .navbar-modern .toggle-btn:hover {
    border-color: rgba(255, 221, 0, 0.4);
    background: rgba(255, 221, 0, 0.08);
  }

  .navbar-modern .nav-pill {
    border-radius: 999px;
    border: none;
    padding: 8px 14px;
    background: rgba(255, 255, 255, 0.03);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #fafafa;
    transition: background .2s ease;
  }

  .navbar-modern .nav-pill:hover {
    background: rgba(255, 221, 0, 0.08);
  }

  .navbar-modern .avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    object-fit: cover;
    border: none;
  }

  .navbar-modern .profile-info {
    display: flex;
    flex-direction: column;
    font-size: 11px;
    line-height: 1.2;
    text-align: left;
  }

  .navbar-modern .profile-info strong {
    font-size: 13px;
    text-transform: none;
  }

  .navbar-modern .dropdown-menu {
    background: #101010;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    min-width: 250px;
    padding: 10px 0;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
  }

  .navbar-modern .dropdown-item {
    color: #dcdcdc;
    font-weight: 500;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background .2s ease, padding-left .2s ease;
  }

  .navbar-modern .dropdown-item:hover {
    background: rgba(255, 221, 0, 0.07);
    padding-left: 26px;
    color: #fff;
  }

  .navbar-modern .dropdown-header {
    text-transform: uppercase;
    font-size: 11px;
    color: #848484;
    padding: 6px 20px;
    letter-spacing: 0.08em;
  }

  .navbar-modern .divider {
    margin: 8px 0;
    height: 1px;
    background: rgba(255, 255, 255, 0.08);
  }

  .navbar-modern .btn-exit {
    border-radius: 999px;
    border: none;
    padding: 8px 16px;
    color: #f4f4f4;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.03);
    transition: background .2s ease, color .2s ease;
  }

  .navbar-modern .btn-exit:hover {
    color: #ffdd00;
    background: rgba(255, 221, 0, 0.08);
  }

  .navbar-modern .navbar-nav {
    align-items: center;
    gap: 12px;
  }

  .navbar-modern .navbar-nav .nav-item {
    display: flex;
    align-items: center;
  }

  .navbar-modern .mobile {
    display: none;
  }

  @media (max-width: 991.98px) {
    .navbar-modern .desktop {
      display: none;
    }

    .navbar-modern .mobile {
      display: inline-flex;
    }
  }
</style>

<nav class="main-header navbar navbar-expand navbar-modern">

  <ul class="navbar-nav">
    <li class="nav-item">
      <a id="push-menu" class="nav-link desktop toggle-btn" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-chevron-left" id="icone-menu-bar"></i>
      </a>
      <a class="nav-link mobile toggle-btn" id="push-menu" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-chevron-left" id="icone-menu-bar"></i>
      </a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a href="#" class="dropdown-item dropdown-footer nav-pill" data-toggle="dropdown">
        <?php if (@$section->getFoto()) : ?>
          <img src="<?= url("uploads/perfil/{$section->getFoto()}"); ?>" alt="Foto de perfil" class="avatar">
        <?php else : ?>
          <div class="avatar" style="display:flex;align-items:center;justify-content:center;background:#1f1f1f;">
            <i class="far fa-user"></i>
          </div>
        <?php endif; ?>
        <div class="profile-info">
          <span>Conectado como</span>
          <strong><?= $section->getNomeAbreviado() ?></strong>
        </div>
        <i class="fas fa-chevron-down" style="font-size:12px;"></i>
      </a>

      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <div class="dropdown-header">Minha conta</div>
        <a href="<?= url("usuarios/perfil/" . $_SESSION["id"]) ?>" class="dropdown-item">
          <i class="far fa-user-circle text-warning"></i>
          Minha Conta
        </a>

        <a href="<?= url("permissao/usuarios") ?>" class="dropdown-item">
          <i class="fas fa-shield-alt text-warning"></i>
          Permissões
        </a>

        <?php if (@$_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
          <a href="<?= url("permissao/paginas") ?>" class="dropdown-item">
            <i class="fas fa-sitemap text-warning"></i>
            Páginas de Permissões
          </a>
          <div class="divider"></div>
        <?php endif; ?>

        <?php if ($contas && !@$_SESSION["subusuario"]) : ?>
          <div class="dropdown-header">Alternar conta</div>
          <?php foreach ($contas as $conta) : ?>
            <a href="<?= url("permissao/logar/$conta->id") ?>" class="dropdown-item">
              <img src="<?= url("uploads/perfil/{$conta->dataUsuarioOrigem()->foto}"); ?>" alt="" class="avatar" style="width:30px;height:30px;">
              <div>
                <strong><?= $conta->dataUsuarioOrigem()->nome ?></strong><br>
                <small style="color:#8b8b8b;">Visualizar conta</small>
              </div>
            </a>
          <?php endforeach; ?>
        <?php elseif (@$_SESSION["subusuario"]) : ?>
          <div class="dropdown-header">Subconta</div>
          <a href="?action=sair_sub" class="dropdown-item">
            <i class="fas fa-random text-warning"></i>
            Gerenciar Conta Principal
          </a>
        <?php endif; ?>
      </div>
    </li>

    <?php $v->insert("inc/notificacao") ?>

    <?php $v->insert("inc/apps") ?>

    <li class="nav-item d-flex align-items-center">
      <a class="btn-exit" href="?action=sair" role="button" title="Sair">
        <i class="fas fa-sign-out-alt"></i> Sair
      </a>
    </li>
  </ul>
</nav>

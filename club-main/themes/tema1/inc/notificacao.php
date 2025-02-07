<div class="not">
    <?php 
        use Source\Models\Notificacao\Notificacao;

        $ultimas = (new Notificacao())->find("usuario = " . $_SESSION["id"])->limit(5)->order("id DESC")->fetch(true);
        $conta = (new Notificacao())->find("lida = 0 and usuario = " . $_SESSION["id"])->count();
    ?>

    <li class="nav-item dropdown notificacao">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge"><?= $conta ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <?php if($ultimas): ?>
            <span class="dropdown-item dropdown-header"><?= $conta ?> Notificações</span>
            <div class="dropdown-divider"></div>

            <?php foreach($ultimas as $notificacao): ?>
              <a href="<?= $notificacao->link ?>" class="dropdown-item">
                <p><i class="fas fa-dollar-sign mr-2" style="color: green"></i> <?= $notificacao->texto ?></p>
              </a>
              <div class="dropdown-divider"></div>
            <?php endforeach; ?>
            
            <a href="<?= url("notificacoes") ?>" class="dropdown-item dropdown-footer">Ver todas</a>

          <?php else: ?>
            <span class="dropdown-item dropdown-header">Nenhuma notificação</span>
          <?php endif; ?>
        </div>
      </li>
</div>

<div class="notifications">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <a href="" id="link_notificacao"><span id="msg"></span></a>
        
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
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
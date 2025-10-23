<?php $v->layout("_theme"); ?>

<div class="content-wrapper win-page">
  <section class="content">
    <div class="container-fluid">
      <header class="win-page-header">
        <div class="win-page-title">
          <small>Área de cursos</small>
          <h1><?= $title ?></h1>
        </div>
        <?php if (@$_SESSION["tipo"] != 6) : ?>
          <div class="win-page-actions">
            <a href="<?= url("cursos/novo") ?>" class="btn-win">
              <i class="fas fa-plus"></i>
              Novo curso
            </a>
          </div>
        <?php endif; ?>
      </header>

      <?php if ($cursos) : ?>
        <section class="win-section">
          <div class="win-section-header">
            <h2>Seus cursos</h2>
            <span class="win-section-subtitle">
              <?= is_countable($cursos) ? count($cursos) : 0 ?> resultados nesta página
            </span>
          </div>

          <div class="win-grid win-grid-2">
            <?php foreach ($cursos as $curso) : ?>
              <?php
              $alunosCount = $turmaAluno->find("usuario = $curso->usuario AND curso = $curso->id")->count();
              $aulasCount = $aula->find("curso = $curso->id")->count();
              $cover = $curso->imagem ? url("uploads/cursos/" . $curso->imagem) : url("uploads/cursos/default.png");
              ?>
              <article class="win-card">
                <figure>
                  <img src="<?= $cover; ?>" alt="<?= $curso->nome; ?>">
                </figure>

                <div class="win-card-body">
                  <div class="win-card-meta">
                    <span class="win-meta-chip">
                      <i class="fas fa-layer-group"></i>
                      ID <?= $curso->id; ?>
                    </span>
                  </div>

                  <h3><?= mb_strimwidth($curso->nome, 0, 60, "..."); ?></h3>

                  <div class="win-card-meta">
                    <span><i class="fas fa-user-friends"></i> <?= $alunosCount; ?> alunos</span>
                    <span><i class="fas fa-video"></i> <?= $aulasCount; ?> aulas</span>
                  </div>
                </div>

                <div class="win-card-footer">
                  <a class="btn-win" href="<?= url("cursos/detalhe/" . $curso->id); ?>">
                    <i class="fas fa-arrow-right"></i>
                    Gerenciar curso
                  </a>
                  <a class="btn-win btn-win-ghost" href="<?= url("minhas-compras/viewcursos/" . $curso->id) ?>" target="_blank">
                    <i class="fas fa-eye"></i>
                    Ver como aluno
                  </a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="win-divider"></div>

          <div class="d-flex justify-content-center">
            <?php
            $link = URL_BASE . "/cursos/p/";
            $paginador->paginas($total, $pagina, $link);
            ?>
          </div>
        </section>
      <?php else : ?>
        <section class="win-section">
          <div class="win-empty-state">
            Nenhum curso encontrado no momento. Que tal criar o primeiro?
          </div>
        </section>
      <?php endif; ?>
    </div>
  </section>

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>

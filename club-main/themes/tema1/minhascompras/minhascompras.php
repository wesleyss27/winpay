<?php
$v->layout("_theme");

$listaCursos = [];
if ($turmaaluno) {
    foreach ($turmaaluno as $entrada) {
        $listaCursos[] = $entrada;
    }
}
$destacado = $listaCursos[0] ?? null;

$imagemCurso = function ($curso) {
    if (!$curso) {
        return url("uploads/cursos/default.png");
    }
    $cover = $curso->imagem ?? null;
    return $cover ? url("uploads/cursos/" . $cover) : url("uploads/cursos/default.png");
};

$descricaoCurso = function ($curso) {
    if (!$curso || empty($curso->descricao)) {
        return "Pronto para começar uma nova jornada de aprendizado?";
    }
    return mb_strimwidth(strip_tags($curso->descricao), 0, 180, "…");
};
?>

<div class="content-wrapper win-page">
  <section class="content">
    <div class="container-fluid">

      <?php if ($destacado) :
        $cursoHero = $destacado->dataCurso();
        $imagemHero = $imagemCurso($cursoHero);
      ?>
        <section class="win-hero" style="background: linear-gradient(135deg, rgba(11, 15, 28, 0.92), rgba(5, 5, 8, 0.9)), url('<?= $imagemHero; ?>') center/cover no-repeat;">
          <h2><?= $cursoHero->nome; ?></h2>
          <p><?= $descricaoCurso($cursoHero); ?></p>
          <div class="win-page-actions">
            <a href="<?= url("minhas-compras/viewcursos/" . $cursoHero->id); ?>" class="btn-win">
              <i class="fas fa-play"></i>
              Continuar curso
            </a>
          </div>
        </section>
      <?php endif; ?>

      <section class="win-section">
        <div class="win-section-header">
          <h2>Seus cursos</h2>
          <span class="win-section-subtitle">Acesse rapidamente o conteúdo que você já adquiriu.</span>
        </div>

        <?php if ($listaCursos) : ?>
          <div class="win-slider">
            <?php foreach ($listaCursos as $item) :
              $curso = $item->dataCurso();
              $imagem = $imagemCurso($curso);
              $descricao = $descricaoCurso($curso);
            ?>
              <article class="win-slider-card">
                <figure>
                  <img src="<?= $imagem; ?>" alt="<?= $curso->nome; ?>">
                </figure>
                <figcaption>
                  <h3><?= mb_strimwidth($curso->nome, 0, 60, "…"); ?></h3>
                  <p class="win-card-text"><?= $descricao; ?></p>
                  <a href="<?= url("minhas-compras/viewcursos/" . $curso->id); ?>" class="btn-win">
                    <i class="fas fa-arrow-right"></i>
                    Acessar curso
                  </a>
                </figcaption>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="d-flex justify-content-center mt-4">
            <?php
            $link = URL_BASE . "/produtos/p/";
            $paginador->paginas($total, $pagina, $link);
            ?>
          </div>
        <?php else : ?>
          <div class="win-empty-state">
            Você ainda não possui cursos disponíveis. Explore o catálogo e adquira seu primeiro treinamento!
          </div>
        <?php endif; ?>
      </section>
    </div>
  </section>

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>

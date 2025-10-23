<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper win-page">
  <section class="content">
    <div class="container-fluid">
      <header class="win-page-header">
        <div class="win-page-title">
          <small>Construção do curso</small>
          <h1><?= $title ?></h1>
        </div>
        <div class="win-page-actions">
          <a href="<?= url("cursos/novo?course_id=" . $course_id); ?>" class="btn-win btn-win-ghost">
            <i class="fas fa-arrow-left"></i>
            Voltar
          </a>
        </div>
      </header>

      <section class="win-section">
        <?php
        $v->insert("cursos/partials/stepper", [
          'steps' => [
            'Cadastro de curso',
            'Cadastro de módulo',
            'Cadastro de aula'
          ],
          'active' => 1
        ]);
        ?>

        <form action="<?= $router->route("appcursos.cadastromodulo"); ?>" method="post" enctype="multipart/form-data" class="win-form">
          <div class="win-form-grid">
            <div class="form-group">
              <label for="module_nome">Nome do módulo</label>
              <input type="text" class="form-control" id="module_nome" name="module_nome" placeholder="Ex.: Introdução" required>
            </div>

            <div class="form-group">
              <label for="module_descricao">Descrição do módulo</label>
              <textarea class="form-control" id="module_descricao" name="module_descricao" rows="5" placeholder="Descreva o objetivo do módulo" required></textarea>
            </div>
          </div>

          <input type="hidden" name="course_id" value="<?= $course_id; ?>">

          <div class="win-form-actions">
            <a href="<?= url("cursos/novo?course_id=" . $course_id); ?>" class="btn-win btn-win-ghost">
              Cancelar
            </a>
            <button type="submit" name="next-step" value="1" class="btn-win">
              Avançar para aulas
              <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </form>
      </section>
    </div>
  </section>
</div>

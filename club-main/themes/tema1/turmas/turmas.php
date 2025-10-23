<?php
$v->layout("_theme");

$imagemCurso = function ($curso) {
    $cover = $curso->imagem ?? null;
    return $cover ? url("uploads/cursos/" . $cover) : url("uploads/cursos/default.png");
};

$contarAlunos = function ($curso, $turmaAluno) {
    return $turmaAluno->find("usuario = $curso->usuario AND curso = $curso->id")->count();
};
?>

<div class="content-wrapper win-page">
    <section class="content">
        <div class="container-fluid">
            <header class="win-page-header">
                <div class="win-page-title">
                    <small>Gestão de turmas</small>
                    <h1><?= $title ?></h1>
                </div>
                <div class="win-page-actions">
                    <a href="<?= url("cursos/novo"); ?>" class="btn-win">
                        <i class="fas fa-plus"></i>
                        Criar novo curso
                    </a>
                </div>
            </header>

            <section class="win-section">
                <div class="win-section-header">
                    <h2>Turmas ativas</h2>
                    <span class="win-section-subtitle">Selecione uma turma para acompanhar alunos e desempenho.</span>
                </div>

                <?php if ($cursos) : ?>
                    <div class="win-grid win-grid-2">
                        <?php foreach ($cursos as $curso) :
                            $alunosCount = $contarAlunos($curso, $turmaAluno);
                            $cover = $imagemCurso($curso);
                        ?>
                            <article class="win-card">
                                <figure>
                                    <img src="<?= $cover; ?>" alt="<?= $curso->nome; ?>">
                                </figure>
                                <div class="win-card-body">
                                    <div class="win-card-meta">
                                        <span class="win-meta-chip">
                                            <i class="fas fa-users"></i>
                                            <?= $alunosCount; ?> alunos
                                        </span>
                                    </div>
                                    <h3><?= mb_strimwidth($curso->nome, 0, 60, "…"); ?></h3>
                                    <p class="win-card-text">
                                        <?= $curso->descricao ? mb_strimwidth(strip_tags($curso->descricao), 0, 120, "…") : "Acompanhe o progresso da turma em tempo real."; ?>
                                    </p>
                                </div>
                                <div class="win-card-footer">
                                    <a href="<?= url("turmas/alunos/" . $curso->id); ?>" class="btn-win">
                                        <i class="fas fa-arrow-right"></i>
                                        Ver alunos
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="win-empty-state">
                        Nenhuma turma encontrada. Crie um curso e convide alunos para começar!
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </section>

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>

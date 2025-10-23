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
                    <small>Engajamento</small>
                    <h1><?= $title ?></h1>
                </div>
                <div class="win-page-actions">
                    <span class="win-badge">
                        <i class="far fa-comments"></i>
                        <?= @$count ?? 0 ?> comentários
                    </span>
                </div>
            </header>

            <?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

            <section class="win-section">
                <div class="win-section-header">
                    <h2>Comentários recebidos</h2>
                    <span class="win-section-subtitle">Controle respostas, status e moderação</span>
                </div>

                <?php if ($comentarios) : ?>
                    <div class="win-table-wrapper">
                        <table class="table win-table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Comentário</th>
                                    <th>Aula</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comentarios as $comentario) : ?>
                                    <tr>
                                        <td><?= $comentario->dataAluno()->nome ?></td>
                                        <td><?= $comentario->comentario ?></td>
                                        <td>
                                            <?= $comentario->dataModulo()->nome ?>
                                            <small class="d-block text-muted"><i class="fas fa-arrow-right"></i> <?= $comentario->dataAula()->nome ?></small>
                                        </td>
                                        <td>
                                            <span class="win-status <?= $comentario->status == 1 ? 'win-status-success' : 'win-status-warning' ?>">
                                                <?= $comentario->status == 1 ? 'Visível' : 'Oculto' ?>
                                            </span>
                                        </td>
                                        <td><?= formataDataHora($comentario->created_at) ?></td>
                                        <td>
                                            <div class="acao_editar">
                                                <a href="javascript:void(0);" class="course_action"><i class="fas fa-ellipsis-v"></i></a>
                                                <div class="acao" style="display:none;">
                                                    <?php if ($comentario->status == 1) : ?>
                                                        <span><a href="#" onclick="bloquear_aluno(<?= @$comentario->id; ?>)">Ocultar comentário</a></span><br>
                                                    <?php else : ?>
                                                        <span><a href="#" onclick="desbloquear_aluno(<?= @$comentario->id; ?>)">Reativar comentário</a></span><br>
                                                    <?php endif; ?>
                                                    <span><a href="#" onclick="excluir_aluno(<?= @$comentario->id; ?>)">Remover definitivamente</a></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <?php
                        $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
                        $link = URL_BASE . "/cursos/comentarios/$curso->id/";
                        $paginador->paginas($total, $pagina, $link, @$uri[1]);
                        ?>
                    </div>
                <?php else : ?>
                    <div class="win-empty-state">
                        Nenhum comentário por aqui ainda. Assim que seus alunos começarem a interagir, você verá todos nesta página.
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </section>
</div>

<?= $v->start("scripts") ?>
<script>
    $(function() {
        $(document).on('click', '.course_action', function() {
            $(this).next('.acao').toggle();
        });
    });
</script>
<?= $v->end() ?>

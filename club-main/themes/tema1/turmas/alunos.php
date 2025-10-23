<?php
$v->layout("_theme");

$statusConfig = [
    1 => ['label' => 'Ativado', 'class' => 'win-status-success'],
    2 => ['label' => 'Bloqueado', 'class' => 'win-status-warning'],
];

$totalAlunos = (int) @$count;
$totalAulasCurso = (int) @$totalAulas;
$mediaAproveitamento = $totalAulasCurso > 0 ? round((@$media / $totalAulasCurso) * 100) : 0;
$mediaConclusao = (int) @$conclusao;
?>

<div class="content-wrapper win-page">
    <section class="content">
        <div class="container-fluid">
            <header class="win-page-header">
                <div class="win-page-title">
                    <small>Turmas · <?= $curso->nome; ?></small>
                    <h1><?= $title ?></h1>
                </div>
                <div class="win-page-actions">
                    <button class="btn-win" data-toggle="modal" data-target="#modal-lg" id="cadastro">
                        <i class="fas fa-user-plus"></i>
                        Cadastrar aluno
                    </button>
                </div>
            </header>

            <?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

            <section class="win-section">
                <div class="win-section-header">
                    <h2>Busca rápida</h2>
                    <span class="win-section-subtitle">Filtre pelo nome ou e-mail para localizar um aluno</span>
                </div>
                <form action="#" class="win-form">
                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label for="aluno">Nome ou e-mail do aluno</label>
                            <input type="text" name="aluno" id="aluno" class="form-control" placeholder="Ex.: Maria Silva ou maria@email.com">
                        </div>
                        <div class="form-group d-flex align-items-end">
                            <button class="btn-win" type="submit">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="win-section">
                <div class="win-section-header">
                    <h2>Indicadores da turma</h2>
                    <span class="win-section-subtitle">Resumo do engajamento e progresso dos alunos</span>
                </div>

                <div class="win-stats-grid">
                    <div class="win-stat-card">
                        <span>Total de alunos</span>
                        <strong><?= $totalAlunos; ?></strong>
                    </div>
                    <div class="win-stat-card">
                        <span>Média de aproveitamento</span>
                        <strong><?= $mediaAproveitamento; ?>%</strong>
                    </div>
                    <div class="win-stat-card">
                        <span>Média de conclusão</span>
                        <strong><?= $mediaConclusao; ?>%</strong>
                    </div>
                </div>
            </section>

            <section class="win-section">
                <div class="win-section-header">
                    <h2>Alunos matriculados</h2>
                    <span class="win-section-subtitle">Gerencie acessos, acompanhe progresso e intervenha quando necessário</span>
                </div>

                <?php if ($alunos) : ?>
                    <div class="win-table-wrapper">
                        <table class="table win-table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>E-mail</th>
                                    <th>Status</th>
                                    <th>Desde</th>
                                    <th>Último acesso</th>
                                    <th>Andamento</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alunos as $aluno) :
                                    $session = $sessao->find("usuario = $aluno->aluno")->order("created_at DESC")->fetch();
                                    $aulasTotal = (int) $aulaObj->find("curso = $aluno->curso")->count();
                                    $aulasConcluidas = (int) $aulaCl->find("aluno = $aluno->aluno AND curso = $aluno->curso")->count();
                                    $percentual = $aulasTotal > 0 ? round(($aulasConcluidas / $aulasTotal) * 100) : 0;
                                    $statusInfo = $statusConfig[$aluno->status] ?? ['label' => 'Indefinido', 'class' => 'win-status-warning'];
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?= $aluno->dataAluno()->nome; ?></strong><br>
                                            <small class="text-muted">ID interno: <?= $aluno->aluno; ?></small>
                                        </td>
                                        <td><?= $aluno->dataAluno()->email; ?></td>
                                        <td>
                                            <span class="win-status <?= $statusInfo['class']; ?>">
                                                <?= $statusInfo['label']; ?>
                                            </span>
                                        </td>
                                        <td><?= $aluno->aluno ? $aluno->dataAluno()->created_at : '-'; ?></td>
                                        <td><?= @$session->created_at ? formataDataHora(@$session->created_at) : '-'; ?></td>
                                        <td>
                                            <div class="win-progress">
                                                <div class="win-progress-bar" style="width: <?= $percentual; ?>%;"></div>
                                            </div>
                                            <small class="text-muted"><?= $aulasConcluidas; ?>/<?= $aulasTotal; ?> aulas concluídas</small>
                                        </td>
                                        <td>
                                            <div class="acao_editar">
                                                <a href="javascript:void(0);" class="course_action"><i class="fas fa-ellipsis-v"></i></a>
                                                <div class="acao" style="display:none;">
                                                    <?php if ($aluno->status == 1) : ?>
                                                        <span><a href="#" onclick="bloquear_aluno(<?= @$aluno->id; ?>)">Bloquear acesso</a></span><br>
                                                    <?php else : ?>
                                                        <span><a href="#" onclick="desbloquear_aluno(<?= @$aluno->id; ?>)">Desbloquear acesso</a></span><br>
                                                    <?php endif; ?>
                                                    <span><a href="#" onclick="excluir_aluno(<?= @$aluno->id; ?>)">Excluir aluno da turma</a></span>
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
                        $link = URL_BASE . "/turmas/alunos/$curso->id/p/";
                        $paginador->paginas($total, $pagina, $link, @$uri[1]);
                        ?>
                    </div>
                <?php else : ?>
                    <div class="win-empty-state">
                        Ainda não há alunos matriculados nesta turma. Utilize o botão “Cadastrar aluno” ou compartilhe o link de inscrição.
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </section>

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>

<div class="modal fade win-modal" id="modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-info-circle"></i> Cadastrar aluno</h5>
                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="<?= $router->route("cadastro.aluno") ?>" method="post" class="win-form" name="plano" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="curso" value="<?= $curso->id ?>">

                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label for="nome">Nome *</label>
                            <input type="text" name="nome" id="nome" class="form-control" placeholder="Nome completo" required>
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="aluno@email.com" required>
                        </div>

                        <div class="form-group">
                            <label for="senha">Senha (opcional)</label>
                            <input type="password" name="senha" id="senha" class="form-control" placeholder="Defina uma senha inicial">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-win btn-win-ghost" data-dismiss="modal" id="cancelar">Cancelar</button>
                    <button class="btn-win" id="salvar">Salvar aluno</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $v->start("scripts") ?>
<script>
    $(function() {
        $(document).on('click', '.course_action', function() {
            $(this).next('.acao').toggle();
        });

        $("#salvar").on('click', function(e) {
            e.preventDefault();
            salvarEditar();
        });
    });

    function salvarEditar() {
        const form = $("form[name='plano']");
        $.ajax({
            url: "<?= $router->route("cadastro.aluno"); ?>",
            data: form.serialize(),
            type: "POST",
            beforeSend: function() {
                console.log("Enviando cadastro de aluno...");
            },
            error: function() {
                alert("O servidor não conseguiu processar o pedido.");
            },
            success: function(callback) {
                alert(callback);
            location.reload();
            }
        });
    }

    function bloquear_aluno(id) {
        if (confirm("Tem certeza que deseja bloquear esse aluno?")) {
            $.post("<?= $router->route("bloquear.aluno") ?>", { id }, handleAlunoResponse);
        }
    }

    function desbloquear_aluno(id) {
        if (confirm("Tem certeza que deseja desbloquear esse aluno?")) {
            $.post("<?= $router->route("desbloquear.aluno") ?>", { id }, handleAlunoResponse);
        }
    }

    function excluir_aluno(id) {
        if (confirm("Tem certeza que deseja excluir esse aluno?")) {
            $.post("<?= $router->route("excluir.aluno") ?>", { id }, handleAlunoResponse);
        }
    }

    function handleAlunoResponse(callback) {
        alert(callback);
        location.reload();
    }
</script>
<?= $v->end() ?>

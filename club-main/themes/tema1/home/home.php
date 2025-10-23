<?php
$v->layout("_theme");
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row titulo-pagina">
                <div class="col-md-12">
                    <h1 style="font-family:inter"><?= $title ?></h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="card card-body winpay-overview-card">
            <?php
            $cursoNome = '-';
            $cursoQuantidade = '-';
            if (!empty($cursoMaisVendido) && @$cursoMaisVendido->curso) {
                $cursoNome = @$cursoMaisVendido->dataCurso()->nome;
                $cursoQuantidade = number_format((int) @$cursoMaisVendido->quantidade_alunos, 0, ',', '.');
            }
            ?>

            <style>
                .winpay-overview-card {
                    border-radius: 22px;
                    padding: clamp(1.8rem, 3vw, 2.6rem);
                    background: rgba(5, 5, 8, 0.82);
                    border: 1px solid rgba(255, 255, 255, 0.06);
                    box-shadow: 0 24px 48px rgba(0, 0, 0, 0.45);
                    color: #f9fafb;
                    position: relative;
                    overflow: hidden;
                }

                .winpay-overview-card::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    border-radius: inherit;
                    background: radial-gradient(circle at 85% 10%, rgba(244, 208, 63, 0.18), transparent 60%);
                    opacity: 0.7;
                    pointer-events: none;
                }

                .winpay-overview-card header {
                    position: relative;
                    z-index: 1;
                    margin-bottom: 2.4rem;
                }

                .winpay-overview-card header h2 {
                    font-size: clamp(1.4rem, 2.5vw, 1.8rem);
                    font-weight: 600;
                    margin-bottom: 0.35rem;
                    letter-spacing: 0.04em;
                }

                .winpay-overview-card header span {
                    color: #9ca3af;
                    font-size: 0.95rem;
                }

                .winpay-metrics-grid {
                    position: relative;
                    z-index: 1;
                    display: grid;
                    gap: 1.6rem;
                    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                    margin-bottom: 2.2rem;
                }

                .metric-card {
                    background: rgba(12, 13, 20, 0.85);
                    border-radius: 20px;
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    padding: 1.4rem 1.5rem;
                    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
                    display: flex;
                    flex-direction: column;
                    gap: 0.75rem;
                    transition: transform 0.2s ease, border 0.2s ease;
                }

                .metric-card:hover {
                    transform: translateY(-4px);
                    border-color: rgba(244, 208, 63, 0.25);
                }

                .metric-label {
                    font-size: 0.85rem;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    color: rgba(244, 208, 63, 0.75);
                    font-weight: 600;
                }

                .metric-value {
                    font-size: clamp(2rem, 4vw, 2.5rem);
                    font-weight: 600;
                    letter-spacing: -0.02em;
                }

                .metric-subtext {
                    color: #9ca3af;
                    font-size: 0.9rem;
                }

                .winpay-course-card {
                    position: relative;
                    z-index: 1;
                    display: grid;
                    gap: 1.8rem;
                    padding: 1.8rem;
                    border-radius: 22px;
                    border: 1px solid rgba(255, 255, 255, 0.06);
                    background: rgba(12, 13, 20, 0.72);
                    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
                }

                .winpay-course-card h3 {
                    font-size: 1.2rem;
                    font-weight: 600;
                    margin-bottom: 0.4rem;
                    letter-spacing: 0.04em;
                    text-transform: uppercase;
                    color: rgba(244, 208, 63, 0.9);
                }

                .course-highlight {
                    display: flex;
                    justify-content: space-between;
                    gap: 1.5rem;
                    flex-wrap: wrap;
                    align-items: center;
                }

                .course-name {
                    font-size: clamp(1.2rem, 2.2vw, 1.4rem);
                    font-weight: 600;
                }

                .course-data {
                    display: flex;
                    gap: 1.5rem;
                    flex-wrap: wrap;
                }

                .course-pill {
                    background: rgba(244, 208, 63, 0.12);
                    border: 1px solid rgba(244, 208, 63, 0.2);
                    border-radius: 999px;
                    padding: 0.45rem 0.9rem;
                    font-size: 0.85rem;
                    color: rgba(244, 208, 63, 0.9);
                    font-weight: 500;
                }

                @media (max-width: 768px) {
                    .winpay-overview-card {
                        padding: 1.8rem;
                    }
                }
            </style>

            <header>
                <h2>Resumo da plataforma</h2>
                <span>Acompanhe de perto os indicadores principais do seu clube.</span>
            </header>

            <div class="winpay-metrics-grid">
                <div class="metric-card">
                    <span class="metric-label">Total de alunos</span>
                    <span class="metric-value"><?= number_format((int) @$count, 0, ',', '.') ?></span>
                    <span class="metric-subtext">Alunos ativos no período atual.</span>
                </div>

                <div class="metric-card">
                    <span class="metric-label">Média de aproveitamento</span>
                    <span class="metric-value"><?= number_format((float) @$media, 2, ',', '.') ?>%</span>
                    <span class="metric-subtext">Percentual médio de conclusão dos módulos.</span>
                </div>

                <div class="metric-card">
                    <span class="metric-label">Média de conclusão</span>
                    <span class="metric-value"><?= number_format((float) @$conclusao, 2, ',', '.') ?>%</span>
                    <span class="metric-subtext">Taxa geral de finalização do conteúdo.</span>
                </div>
            </div>

            <div class="winpay-course-card">
                <h3>Curso mais vendido</h3>
                <div class="course-highlight">
                    <div class="course-name"><?= $cursoNome ?></div>
                    <div class="course-data">
                        <span class="course-pill"><?= $cursoQuantidade ?> alunos</span>
                        <span class="course-pill">Desempenho em alta</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>
<!-- /.content-wrapper -->

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Planos</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form action="<?= $router->route("cadastro.aluno") ?>" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px" name="plano" enctype="multipart/form-data">
                <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="curso" value="<?= $curso->id ?>">

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Nome: *</label>
                                <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome">
                            </div>

                            <div class="col-md-4">
                                <label>Email: *</label>
                                <input type="text" name="email" id='email' class="form-control input-lg" placeholder="Preço *" required>
                            </div>

                            <div class="col-md-4">
                                <label>Senha: </label>
                                <input type="password" name="senha" id='senha' class="form-control input-lg" placeholder="Quantidade *">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                    <button class="btn btn-primary" id="salvar">Salvar</button>
                </div>
        </div>

        </form>

    </div>

</div>

<!-- end cadastrar -->
<?= $v->start("scripts") ?>
<script>
    $(document).ready(function() {
        $(".course_action").click(function() {
            jQuery(this).next('.acao').toggle();

        });

        $(".module_action").click(function() {
            jQuery(this).next('.acao').toggle();

        });

        $(".aulas_action").click(function() {
            jQuery(this).next('.acao').toggle();

        });
    });

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar();
    });

    function salvarEditar() {
        var form = $("form");
        var url = "<?= $router->route("cadastro.aluno"); ?>";

        console.log(url);
        console.log(form.serialize());
        $.ajax({

            url: url,
            data: form.serialize(),
            type: "POST",
            //datatype: "json",
            beforeSend: function() {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log("callback ", callback);
                alert(callback);
                location.reload();
            }

        });
    }

    function bloquear_aluno(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja bloquear esse aluno?")) {
            $.ajax({
                url: "<?= $router->route("bloquear.aluno") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Aluno bloqueado com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }

    function desbloquear_aluno(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja desbloquear esse aluno?")) {
            $.ajax({
                url: "<?= $router->route("desbloquear.aluno") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Aluno desbloqueado com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }

    function excluir_aluno(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse aluno?")) {
            $.ajax({
                url: "<?= $router->route("excluir.aluno") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Aluno excluído com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
</script>
<?= $v->end() ?>

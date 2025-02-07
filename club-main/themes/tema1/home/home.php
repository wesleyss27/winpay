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

        <div class="card card-body">

            <style>
                .dest1 {
                    font-size: 30px;
                    font-weight: 600;
                }
            </style>

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-body center">
                        Total de alunos:
                        <p class="dest1"><?= @$count ?></p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-body center">
                        Média de aproveitamento:
                        <p class="dest1"><?= @$media ?>%</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-body center">
                        Média de conclusão:
                        <p class="dest1"><?= $conclusao ?>%</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-body">
                        <div class="center">
                            Curso Mais Vendido:
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <b><?= @$cursoMaisVendido->curso ? @$cursoMaisVendido->dataCurso()->nome : '-' ?></b>
                            </div>
                            <div class="col-md-2">
                                <?= @$cursoMaisVendido->quantidade_alunos ?>
                            </div>
                        </div>

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
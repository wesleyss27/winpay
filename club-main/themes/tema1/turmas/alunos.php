<?php
$v->layout("_theme");

$status = array(
    1 => "Ativado",
    2 => "Bloqueado",
);
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
            <hr>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

        <div class="card card-body">
            <form action="#">
                <div class="row">
                    <div class="col-md-6">
                        <label>Nome ou email do aluno:</label>
                        <input type="text" name="aluno" id="aluno" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label style="color: #fff">|</label>
                        <button class="btn btn-outline-primary form-control">Buscar</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card card-body">

            <div class="">
                <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
            </div>
            <br>

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
                        <p class="dest1"><?= round(@$media / @$totalAulas * 100) ?>%</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-body center">
                        Média de conclusão: 
                        <p class="dest1"><?= $conclusao ?>%</p>
                    </div>
                </div>
            </div>


            <?php if ($alunos) : ?>
                <div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Email</th>
                                <th scope="col">Status</th>
                                <th scope="col">Desde</th>
                                <th scope="col">Último acesso</th>
                                <th scope="col" class="col-md-3">Andamento</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($alunos as $aluno) : ?>
                                <tr>
                                    <td><?= $aluno->aluno . ' ' . $aluno->dataAluno()->nome ?></td>
                                    <td><?= $aluno->dataAluno()->email ?></td>
                                    <td><?= $status[$aluno->status] ?></td>
                                    <td><?= $aluno->aluno ? $aluno->dataAluno()->created_at : '-' ?></td>
                                    <?php
                                    $session = $sessao->find("usuario = $aluno->aluno")->order("created_at DESC")->fetch();
                                    ?>
                                    <td><?= @$session->created_at ? formataDataHora(@$session->created_at) : '-' ?></td>
                                    <td>
                                        <?php
                                        $aulasTotal = $aulaObj->find("curso = $aluno->curso")->count();
                                        $aulasConcluidas = $aulaCl->find("aluno = $aluno->aluno AND curso = $aluno->curso")->count();

                                        $porcent = round($aulasConcluidas / $aulasTotal * 100);
                                        ?>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcent ?>%;" aria-valuenow="<?= $porcent ?>" aria-valuemin="0" aria-valuemax="100"><?= "$aulasConcluidas/$aulasTotal" ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="acao_editar">
                                            <a href="javascript:void();" class="course_action"><i class="fas fa-ellipsis-v"></i></a>
                                            <div class="acao" style="display:none;">
                                                <?php if ($aluno->status == 1) : ?>
                                                    <span><a href="#" onclick="bloquear_aluno(<?= @$aluno->id; ?>)">Bloquear acesso</a></span><br>
                                                <?php else : ?>
                                                    <span><a href="#" onclick="desbloquear_aluno(<?= @$aluno->id; ?>)">Desbloquear acesso</a></span><br>
                                                <?php endif ?>
                                                <span><a href="#" onclick="excluir_aluno(<?= @$aluno->id; ?>)">Excluir aluno da turma</a></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
                $link = URL_BASE . "/turmas/alunos/$curso->id/p/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link, @$uri[1]);

                ?>
            <?php else : ?>
                <div class="card-body">
                    <h4>Nenhum aluno encontrado!</h4>
                </div>

            <?php endif; ?>
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
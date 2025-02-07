<?php
$v->layout("_theme");

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item"><a href="./">Ferramentas</a></li>
                        <li class="breadcrumb-item active">Api</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                    <a class="btn btn-outline-primary" data-toggle="modal" data-target="#cadastrar" style="font-family:inter" id="cadastro">Gerar API <i class="fa fa-chevron-right"></i> </a>
                <?php endif ?>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">

                <?php if (@$api) : $i = 1; ?>
                    <script>
                        var dados = [];
                    </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">API KEY</th>
                                <th scope="col">Ação</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($api as $api) : ?>
                                <tr>
                                    <td><?= $api->id ?></td>
                                    <td><?= $api->descricao ?></td>
                                    <td><?= $api->api_key ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluir(<?= $api->id; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id: "<?= $api->id ?>",
                                        descricao: "<?= $api->descricao ?>",
                                        api_key: "<?= $api->api_key ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php
                    //EXIBE A NAVEGAÇÃO DO PAGINADOR
                    $link = URL_BASE . "/api/";

                    //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                    $paginador->paginas($total, $pagina, $link);

                else : ?>
                    Nenhuma api encontrada
                <?php endif; ?>
            </div>
            <!-- FIM CARD BODY -->

        </div>
    </section>
</div>



<!-- Modal -->
<div id="cadastrar" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Cadastrar Chave API</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body" style="background: #f4f6f9">

                    <div class="card card-body">
                        <input type="hidden" name="id" id="id">
                        <b>Descrição</b>
                        <input type="text" name="descricao" id="descricao" class="form-control">
                    </div>

                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->


<?php $v->start("scripts") ?>
<script>
    $("#cadastro").click((e) => {
        cancelar();
    });

    $("#salvar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("api.cadastrar") ?>");
    });

    $("#editar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("api.editar") ?>");
    });

    function salvarEditar(url) {

        var form = $("form");
        console.log(form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#id").attr({
            "value": ""
        });
        $("#api_key").attr("value", "");
        $("#descricao").attr("value", "");
    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa API?")) {
            $.ajax({
                url: "<?= $router->route("api.excluir") ?>",
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
                    if (callback == "Excluído com sucesso") {
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
<?php $v->end() ?>
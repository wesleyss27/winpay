<?php
$v->layout("_theme");

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
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
                <a class="btn btn-outline-primary" data-toggle="modal" data-target="#cadastrar" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </a>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">

                <?php if (@$paginas) : $i = 1; ?>
                    <script>
                        var dados = [];
                    </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Página</th>
                                <th scope="col">Módulo</th>
                                <th scope="col">Permissões</th>
                                <th scope="col">Ação</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($paginas as $pagina) : ?>
                                <tr>
                                    <td><?= $pagina->id ?></td>
                                    <td><?= $pagina->pagina ?></td>
                                    <td><?= $pagina->modulo ?></td>
                                    <td>
                                        <?= $pagina->admin == 1 ? "admin, " : "" ?>
                                        <?= $pagina->ver == 1 ? "ver, " : "" ?>
                                        <?= $pagina->adicionar == 1 ? "adicionar, " : "" ?>
                                        <?= $pagina->editar == 1 ? "editar, " : "" ?>
                                        <?= $pagina->excluir == 1 ? "excluir" : "" ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#cadastrar" onclick="editar(<?= $i ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#excluir-modal"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id: "<?= $pagina->id ?>",
                                        pagina: "<?= $pagina->pagina ?>",
                                        modulo: "<?= $pagina->modulo ?>",
                                        tipo: "<?= $pagina->tipo ?>",
                                        menu: "<?= $pagina->menu ?>",
                                        admin: "<?= $pagina->admin ?>",
                                        ver: "<?= $pagina->ver ?>",
                                        adicionar: "<?= $pagina->adicionar ?>",
                                        editar: "<?= $pagina->editar ?>",
                                        excluir: "<?= $pagina->excluir ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    Nenhuma página encontrada
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
                <h4 class="modal-title">Cadastrar Página</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#" name="form-cadastro">
                <!-- Modal body -->
                <div class="modal-body">

                    <input type="hidden" name="id" id="id">
                    <label>Nome (da página ou menu)</label>
                    <input type="text" name="pagina" id="pagina" class="form-control"><br>

                    <label>Módulo</label>
                    <input type="text" name="modulo" id="modulo" class="form-control">

                    <label>Tipo</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="pagina">Página</option>
                        <option value="menu">Menu</option>
                    </select>

                    <label>Menu</label>
                    <select name="menu" id="menu" class="form-control">
                        <option value="">Selecionar</option>
                        <?php if ($menus) : ?>
                            <?php foreach ($menus as $menu) : ?>
                                <option value="<?= $menu->id ?>"><?= $menu->pagina ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                    </select>

                    <hr>
                    <p>Permissões da página</p>

                    <input type="checkbox" name="admin" id="admin" value="1" class="">
                    <label for="admin">Admin</label><br>

                    <input type="checkbox" name="ver" id="ver" value="1" class="">
                    <label for="ver">Ver</label><br>

                    <input type="checkbox" name="adicionar" id="adicionar" value="1" class="">
                    <label for="adicionar">Adicionar</label><br>

                    <input type="checkbox" name="editar" id="editar-ck" value="1" class="">
                    <label for="editar-ck">Editar</label><br>

                    <input type="checkbox" name="excluir" id="excluir" value="1" class="">
                    <label for="excluir">Excluir</label><br>

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

<!-- MODAL EXCLUIR -->
<div id="excluir-modal" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza que deseja excluir?</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <input type="hidden" name="id" id="id-excluir">

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Não</button>
                    <button type="button" class="btn btn-success" id="btn-excluir" onclick="excluir(<?= $pagina->id ?>)">Sim</button>
                </div>
                <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- FIM MODAL EXCLUIR -->


<?php $v->start("scripts") ?>
<script>
    $("#cadastro").click((e) => {
        cancelar();
    });

    $("#salvar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("paginas.salvar") ?>");
    });

    $("#editar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("paginas.salvar") ?>");
    });

    function salvarEditar(url) {

        var form = $("form[name=form-cadastro]");
        console.log(form.serialize());

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                console.log(callback);

                alert(callback);
                if (callback == "Página salva com sucesso!") {
                    location.reload();
                }
            }
        })
    }

    function editar(i) {
        cancelar();

        var id = dados[i]["id"];
        var pagina = dados[i]["pagina"];
        var modulo = dados[i]["modulo"];
        var tipo = dados[i]["tipo"];
        var menu = dados[i]["menu"];
        var admin = dados[i]["admin"];
        var ver = dados[i]["ver"];
        var adicionar = dados[i]["adicionar"];
        var editar = dados[i]["editar"];
        var excluir = dados[i]["excluir"];

        console.log(id, tipo, menu);

        $("#id").prop("value", id);
        $("#pagina").prop("value", pagina);
        $("#modulo").prop("value", modulo);

        if (tipo)
            $("#tipo option[value=" + tipo + "]").prop("selected", true);

        if (menu)
            $("#menu option[value=" + menu + "]").prop("selected", true);

        if (admin == 1)
            $("#admin").prop("checked", true);

        if (ver == 1)
            $("#ver").prop("checked", true);

        if (adicionar == 1)
            $("#adicionar").prop("checked", true);

        if (editar == 1)
            $("#editar-ck").prop("checked", true);

        if (excluir == 1)
            $("#excluir").prop("checked", true);
    }

    function cancelar() {
        console.log("canc");
        $("#id").prop("value", "");
        $("#pagina").prop("value", "");
        $("#modulo").prop("value", "");
        $("#tipo option").prop("selected", false);
        $("#menu option").prop("selected", false);

        console.log($("#id").val(), $("#pagina").val(), $("#modulo").val());

        $("#admin").prop("checked", false);
        $("#ver").prop("checked", false);
        $("#adicionar").prop("checked", false);
        $("#editar-ck").prop("checked", false);
        $("#excluir").prop("checked", false);
    }

    function excluir(id) {
        console.log(id);
        $.ajax({
            url: "<?= $router->route("paginas.excluir") ?>",
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
</script>
<?php $v->end() ?>
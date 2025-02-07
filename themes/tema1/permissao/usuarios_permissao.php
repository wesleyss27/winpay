<?php
$v->layout("_theme");

$status = array(
    0 => "Inativo",
    1 => "Ativo"
);
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
                <form action="<?= $router->route("permissao.salvarUsuario") ?>" method="post">
                    <div class="row">
                        <div class="col-md-8">
                            <label>Adicionar email de usuário</label>
                            <input type="email" name="email" id="email" class="form-control" onblur="verificaEmail()" placeholder="Adicionar Email">
                            <span id="res-email"></span><br>

                            <input type="hidden" name="usuario_permitido" id="usuario">
                        </div>

                        <div class="col-md-3">
                            <label style="color: #fff">|</label><br>
                            <button type="submit" class="btn btn-outline-primary" style="font-family:inter" id="salvar">Adicionar <i class="fa fa-chevron-right"></i> </button>
                        </div>

                        <div class="col-md-1 card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">

                <?php if (@$usuariosPermissao) : $i = 1; ?>
                    <script>
                        var dados = [];
                    </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Usuário</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data da inclusão</th>
                                <th scope="col">Ação</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($usuariosPermissao as $usuarios) : ?>
                                <tr>
                                    <?php 
                                        $foto = $usuarios->dataUsuarioPermitido()->foto ? $usuarios->dataUsuarioPermitido()->foto : "user.png";
                                    ?>

                                    <td><img src="<?= url("uploads/perfil/" . $foto) ?>" width="50px" height="50px"></td>
                                    <td>
                                        <?= $usuarios->dataUsuarioPermitido()->nome ?><br>
                                        <?= $usuarios->dataUsuarioPermitido()->email ?>
                                    </td>
                                    <td><?= $status[$usuarios->ativo] ?></td>
                                    <td>
                                        <?= formataDataHora($usuarios->created_at) ?>
                                    </td>
                                    <td>
                                        <a href="<?= url("permissao/") . $usuarios->id ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#excluir-modal"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id: "<?= $usuarios->id ?>",
                                        pagina: "<?= $usuarios->pagina ?>",
                                        modulo: "<?= $usuarios->modulo ?>",
                                        admin: "<?= $usuarios->admin ?>",
                                        ver: "<?= $usuarios->ver ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    Nenhum usuário encontrado
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
                    <label>Página</label>
                    <input type="text" name="pagina" id="pagina" class="form-control"><br>

                    <label>Módulo</label>
                    <input type="text" name="modulo" id="modulo" class="form-control">

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
                    <button type="button" class="btn btn-primary" id="salvar1">Salvar</button>
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
    function verificaEmail() {
        var email = $("#email").val();
        console.log("email ", email);

        $.ajax({
            url: "<?= $router->route("appusuarios.email") ?>",
            data: {
                email: email
            },

            type: "POST",
            beforeSend: function() {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log("callback ", callback);
                co_produtor = callback;
                if (callback == "Não existe usuário com esse email") {
                    $("#res-email").html(callback);
                    $("#salvar").attr("disabled", true);
                } else if(callback == <?= $_SESSION["id"] ?>) {
                    $("#res-email").html("Você não pode adicionar o próprio email");
                    $("#salvar").attr("disabled", true);
                } else {
                    $("#res-email").html(callback);
                    $("#salvar").attr("disabled", false);
                    $("#usuario").attr("value", callback);
                }
            }
        });
    }

    $("#cadastro").click((e) => {
        cancelar();
    });

    $("#salva").click((e) => {
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
        var admin = dados[i]["admin"];
        var ver = dados[i]["ver"];
        var adicionar = dados[i]["adicionar"];
        var editar = dados[i]["editar"];
        var excluir = dados[i]["excluir"];

        console.log(id);

        $("#id").attr("value", id);
        $("#pagina").attr("value", pagina);
        $("#modulo").attr("value", modulo);

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
        $("#id").attr("value", "");
        $("#pagina").attr("value", "");
        $("#modulo").attr("value", "");

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
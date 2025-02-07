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

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">

                <?php
                if (@$paginas) :
                    $i = 0;
                    function menus($pagina, $i, $pm, $permissao, $menu = true)
                    {
                        // echo "($pagina->pagina - $i) - ";
                ?>
                        <tr class="<?= $menu ? "bg-success" : "" ?>">

                            <td>
                                <?= $pagina->pagina ?>
                                <input type='hidden' name='dados[<?= $i ?>][id]' value='<?= $pm ? $pm->id : '' ?>'>
                                <input type='hidden' name='dados[<?= $i ?>][permissao]' value='<?= $permissao->id ?>'>
                                <input type='hidden' name='dados[<?= $i ?>][usuario_permitido]' value='<?= $permissao->usuario_permitido ?>'>
                                <input type='hidden' name='dados[<?= $i ?>][pagina]' value='<?= $pagina->id ?>'>
                                <input type='hidden' name='dados[<?= $i ?>][modulo]' value='<?= $pagina->modulo ?>'>
                            </td>
                            <td>
                                <?php if ($pagina->ver == 1) : ?>
                                    <input type='checkbox' name='dados[<?= $i ?>][ver]' id='ver<?= $i ?>' value='1' <?= $pm && @$pm->ver == 1 ? 'checked' : '' ?>>
                                    <label for='ver<?= $i ?>'>Acessar</label>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($pagina->adicionar == 1) : ?>
                                    <input type='checkbox' name='dados[<?= $i ?>][adicionar]' id='adicionar<?= $i ?>' value='1' <?= $pm && @$pm->adicionar == 1 ? 'checked' : '' ?>>
                                    <label for='adicionar<?= $i ?>'>Adicionar / Solicitar</label>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($pagina->editar == 1) : ?>
                                    <input type='checkbox' name='dados[<?= $i ?>][editar]' id='editar-ck<?= $i ?>' value='1' <?= $pm && @$pm->editar == 1 ? 'checked' : '' ?>>
                                    <label for='editar-ck<?= $i ?>'>Editar</label>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($pagina->excluir == 1) : ?>
                                    <input type='checkbox' name='dados[<?= $i ?>][excluir]' id='excluir<?= $i ?>' value='1' <?= $pm && @$pm->excluir == 1 ? 'checked' : '' ?>>
                                    <label for='excluir<?= $i ?>'>Excluir</label>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <script>
                        var dados = [];
                    </script>
                    <form action="" method="post">
                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">Página</th>
                                    <th scope="col" colspan="4">Permissões</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                // $menuPagina = array();
                                $i = 0;
                                foreach ($paginas as $pagina) :

                                    $pm = $permissaoModulo->find("pagina = $pagina->id AND permissao = $permissao->id")->fetch();
                                    if ($pagina->tipo == "pagina" && $pagina->menu == "") {
                                        $menuPagina[$pagina->id][0] = $pagina->id;
                                        $menuPagina[$pagina->id][1] = $pagina->pagina;
                                    } elseif ($pagina->tipo == "menu") {
                                        $menuPagina[$pagina->id][0] = $pagina->id;
                                        $menuPagina[$pagina->id][1] = $pagina->pagina;
                                    } elseif ($pagina->tipo == "pagina" && $pagina->menu) {
                                        $menuPagina[$pagina->menu][2][] = $pagina->pagina;
                                        // echo "<pre>";
                                        // var_dump($menuPagina["menu"]);
                                        // echo "</pre>";
                                    }

                                    if (!$pagina->menu) :
                                        menus($pagina, $i, $pm, $permissao);
                                    else :
                                        menus($pagina, $i, $pm, $permissao, false);

                                    endif;
                                    $i++;
                                endforeach
                                ?>
                            </tbody>
                        </table>

                        <button class="btn btn-success" id="salvar">Salvar</button>
                    </form>
                <?php else : ?>
                    Nenhuma página encontrada
                <?php endif; ?>
            </div>

            <!-- <pre>< ?php var_dump($menuPagina) ?></pre> -->
            <!-- FIM CARD BODY -->

        </div>
    </section>
</div>

<?php $v->start("scripts") ?>
<script>
    $("#cadastro").click((e) => {
        cancelar();
    });

    $("#salvar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("permissao.salvar") ?>");
    });

    $("#editar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("paginas.salvar") ?>");
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
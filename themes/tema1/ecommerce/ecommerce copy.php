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

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item"><a href="./">Produtos</a></li>
                        <li class="breadcrumb-item active">Ecommerce</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">

        <!-- CARD BODY -->
        <form action="<?= $router->route("ecommerce.cadastrar") ?>" method="POST" enctype="multipart/form-data">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Habilitar Ecommerce</label>
                        <select name="habilitar" id="habilitar" class="form-control">
                            <option value="0" <?= @$ecommerce->habilitar == 0 ? "selected" : "" ?>>Não</option>
                            <option value="1" <?= @$ecommerce->habilitar == 1 ? "selected" : "" ?>>Sim</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="configuracoes">
                <hr>
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Nome da Loja</label>
                            <input type="text" name="nome_loja" id="nome_loja" class="form-control" value="<?= @$ecommerce->nome_loja ?>">
                            <p id="url_loja"></p>
                        </div>

                        <div class="col-md-4">
                            <label>Logomarca da loja</label>
                            <input type="file" id="logo" name="logo" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Banner da loja</label>
                            <input type="file" id="banner" name="banner" class="form-control">
                        </div>
                    </div>
                </div>

                <hr>
                <?php if (@$ecommerce->destaques) {
                    $destaque = explode(",", $ecommerce->destaques);
                    // var_dump($destaque);

                    foreach($destaque as $key => $value){
                        $dest[$value] = $value;
                    }
                }
                ?>
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Produtos em destaque</label>
                            <select name="destaques[]" id="destaques" class="form-control" multiple="multiple">
                                <?php if ($produtos) : $i = 0; ?>
                                    <?php foreach ($produtos as $produto) : ?>
                                        <option value="<?= $produto->id ?>" <?= @$dest[$produto->id] == $produto->id ? "selected" : "" ?>><?= $produto->nome ?></option>
                                    <?php $i++;
                                    endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="desktop">Para marcar mais de um, segure a tecla "ctrl"</small>
                        </div>

                        <style>
                            input[type="color"] {
                                visibility: hidden;
                            }

                            .input-color {
                                border-radius: 50%;
                                width: 20px;
                                height: 20px;
                                box-shadow: 2px 2px 2px #ddd;
                                padding: 2px;
                                border: 1px solid #bbb;
                            }

                            .input-color:hover {
                                cursor: pointer;
                            }

                            .box-labels {
                                /* display: flex;
                                align-items: center; */
                                border: 1px solid #ddd;
                                border-radius: 10px;
                                padding: 5px 10px;
                                margin: 10px 0;
                                box-shadow: 2px 2px 2px #ddd;
                            }
                        </style>
                        <div class="col-md-4">
                            <div class="row box-labels">
                                <label class="col-10">Cor dos títulos e menu</label>
                                <div class="col-2">
                                    <label class="input-color" id="lbl-cor-titulo" style="background: <?= @$ecommerce->cor_titulo ?>">
                                        <input type="color" name="cor_titulo" id="cor_titulo" class="box-color" value="<?= @$ecommerce->cor_titulo ?>">
                                    </label>
                                </div><br>
                            </div>

                            <div class="row box-labels">
                                <label class="col-10">Cor do texto</label>
                                <div class="col-2">
                                    <label class="input-color" id="lbl-cor-titulo" style="background: <?= @$ecommerce->cor_texto ?>">
                                        <input type="color" name="cor_texto" id="cor_texto" class="box-color" value="<?= @$ecommerce->cor_texto ?>"><br>
                                    </label>
                                </div><br>
                            </div>

                            <div class="row box-labels">
                                <label class="col-10">Cor das tarjas</label>
                                <div class="col-2">
                                    <label class="input-color" id="lbl-cor-titulo" style="background: <?= @$ecommerce->cor_tarja ?>">
                                        <input type="color" name="cor_tarja" id="cor_tarja" class="box-color" value="<?= @$ecommerce->cor_tarja ?>"><br>
                                    </label>
                                </div><br>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Informações sobre a loja</label>
                            <textarea name="informacoes_loja" id="informacoes_loja" cols="10" rows="5" class="form-control"><?= @$ecommerce->informacoes_loja ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label>Dúvidas frequentes</label>
                            <textarea name="duvidas" id="duvidas" cols="10" rows="5" class="form-control"><?= @$ecommerce->duvidas ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="card card-body">
                    <div class="row">
                        <button class="btn btn-success" id="editar">Salvar</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- FIM CARD BODY -->

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
                <h4 class="modal-title">Cadastrar Integração Com Bling</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body">

                    <input type="hidden" name="id" id="id">
                    <b>Descrição</b>
                    <input type="text" name="descricao" id="descricao" class="form-control">

                </div>
                <!-- Fim Modal body -->

                <!-- Modal footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
                    <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
                    <button type="button" class="btn btn-warning" id="editar">Editar</button>
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
    $("#cor_titulo").change(() => {
        $("#lbl-cor-titulo").css("background", $("#cor_titulo").val());
    });

    $("#cadastro").click((e) => {
        cancelar();
    });

    $("#salva").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("api.cadastrar") ?>");
    });

    $("#edita").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("ecommerce.cadastrar") ?>");
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
                // if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
                //     location.reload();
                // }
            }
        })
    }

    function cancelar() {
        $(".modal-title").html("Gerar Api");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#id").attr({
            "value": ""
        });
        $("#api_key").attr("value", "");
        $("#descricao").attr("value", "");
    }

    // $("#cor_titulo").change(() => {
    //     alert($("#cor_titulo").prop("value", "#ffff00"));
    // });
</script>
<?php $v->end() ?>
<?php
$v->layout("_theme");

$json = array(
    0 => "Não",
    1 => "Sim"
);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">

        <!-- < ?= $section->getTipoSecaoLayout() ?> -->

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Postback</li>
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
                    <a class="btn btn-outline-primary" data-toggle="modal" data-target="#cadastrar" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </a>
                <?php endif ?>
                
                &nbsp; Chave única: <?= $usuario->chave_unica ?>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">

                Documentação do postback <a href="https://octapay.com.br/documentacao-postback-octapay/" target="_blank"><i class="fas fa-external-link-alt"></i></a>

                <?php if (@$postbacks) : $i = 1; ?>
                    <script>
                        var dados = [];
                    </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">Proudto</th>
                                <th scope="col">URL</th>
                                <th scope="col">JSON</th>
                                <th scope="col">Eventos</th>
                                <th scope="col">Ação</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($postbacks as $postback) : ?>
                                <tr>
                                    <td><?= $postback->id ?></td>
                                    <td><?= $postback->produto ? $postback->dataProduto()->nome : "Todos" ?></td>
                                    <td><?= $postback->url ?></td>
                                    <td><?= $json[$postback->json] ?></td>
                                    <td><?= $postback->dataEventos() ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-sm" data-target="#cadastrar" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <?php endif ?>

                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-sm" onclick="excluir(<?= $postback->id; ?>)"><i class="fas fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id: "<?= $postback->id ?>",
                                        produto: "<?= $postback->produto ?>",
                                        url: "<?= $postback->url ?>",
                                        eventos: "<?= $postback->eventos ?>",
                                        status: "<?= $postback->status ?>",
                                        json: "<?= $postback->json ?>",
                                    }
                                </script>
                            <?php $i++;
                            endforeach ?>
                        </tbody>
                    </table>
                <?php
                    //EXIBE A NAVEGAÇÃO DO PAGINADOR
                    $link = URL_BASE . "/postbacks/";

                    //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                    $paginador->paginas($total, $pagina, $link);

                else : ?>
                    Nenhum postback cadastrado
                <?php endif; ?>
            </div>
            <!-- FIM CARD BODY -->

        </div>
    </section>
</div>



<!-- Modal -->
<div id="cadastrar" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Cadastrar Postback</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
                <!-- Modal body -->
                <div class="modal-body">

                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="descricao" id="descricao" value="postback">
                    <input type="text" name="chave_unica" id="chave_unica" class="form-control" value="<?= $usuario->chave_unica ?>" readonly>

                    <div class="row">
                        <div class="col-md-6">
                            <b>Produto</b>
                            <select name="produto" id="produto" class="form-control">
                                <option value="">Selecionar</option>
                                <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                                        <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <b>URL</b>
                            <input type="text" name="url" id="url" class="form-control">
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-4">
                            <b>Enviar no formato JSON?</b>
                            <select name="json" id="json" class="form-control">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                            <small>Na dúvida, deixe como SIM</small>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <b>Eventos</b>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="checkbox" name="evento[0]" value="1" id="evento1" checked> Aguardando Pagamento <br>
                                    <input type="checkbox" name="evento[1]" value="2" id="evento2" checked> Em Análise <br>
                                    <input type="checkbox" name="evento[2]" value="3" id="evento3" checked> Pré-autorizada <br>
                                </div>

                                <div class="col-md-3">
                                    <input type="checkbox" name="evento[3]" value="4" id="evento4" checked> Paga <br>
                                    <input type="checkbox" name="evento[4]" value="5" id="evento5" checked> Cancelada <br>
                                    <input type="checkbox" name="evento[5]" value="6" id="evento6" checked> Devolvida <br>
                                </div>

                                <div class="col-md-5">
                                    <input type="checkbox" name="evento[6]" value="7" id="evento7" checked> Chargeback <br>
                                    <input type="checkbox" name="evento[7]" value="8" id="evento8" checked> Concluída <br>
                                    <input type="checkbox" name="evento[8]" value="9" id="evento9" checked> Bloqueada <br>
                                    <input type="checkbox" name="evento[9]" value="10" id="evento10" checked> Carrinho Abandonado (produtor) <br>
                                </div>

                            </div>
                        </div>

                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-primary form-control" id="testar">Testar</button>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="resultado" cols="30" rows="10" class="form-control" readonly></textarea>
                        </div>
                    </div>
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
    $("#cadastro").click((e) => {
        cancelar();
    });

    $("#salvar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("postback.cadastrar") ?>");
    });

    $("#editar").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("postback.editar") ?>");
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
                if (callback == "Salvo com sucesso") {
                    location.reload();
                }
            }
        })
    }

    $("#testar").click((e) => {
        e.preventDefault();
        console.log("clique");
        testar();
        console.log("clique");
    });

    function testar() {

        var url = $("#url").val();
        var json = $("#json").val();
        var chave_unica = $("#chave_unica").val();
        console.log(url, json, chave_unica);
        console.log("testando");

        $.ajax({
            url: "<?= $router->route("postback.testar") ?>",
            type: "POST",
            data: {
                url: url,
                json: json,
                chave_unica: chave_unica
            },
            success: (callback) => {
                console.log(callback);
                $("#resultado").css("display", "block");
                $("#resultado").prop("value", callback);
            },
            error: (err) => {
                console.log(err);
                $("#resultado").css("display", "block");
                $("#resultado").prop("value", err);
                alert("Houve um erro ao realizar o teste. Verifique se a url está correta!");
            }
        });

    }

    function editar(i) {
        var id = dados[i]["id"];
        var produto = dados[i]["produto"];
        var url = dados[i]["url"];
        var eventos = dados[i]["eventos"];
        var json = dados[i]["json"];

        console.log(id, produto, url, eventos);

        $(".modal-title").html("Editar Postback");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#resultado").css("display", "none");
        $("#id").attr({
            "value": id
        });
        if (produto)
            $("#produto option[value=" + produto + "]").prop("selected", true);
        $("#json option[value=" + json + "]").prop("selected", true);
        $("#url").attr("value", url);

        var evento = eventos.split(",");

        for (i = 1; i < 11; i++) {
            $("#evento" + i).prop("checked", false);
        }

        evento.forEach((indice, valor) => {
            $("#evento" + indice).prop("checked", true);
        });
    }

    function cancelar() {
        $(".modal-title").html("Editar Postback");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#id").attr({
            "value": ""
        });
        $("#produto option").prop("selected", false);
        $("#json option").prop("selected", false);
        $("#url").attr("value", "");

        for (i = 1; i < 11; i++) {
            $("#evento" + i).prop("checked", true);
        }
    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse postback?")) {
            $.ajax({
                url: "<?= $router->route("postback.excluir") ?>",
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
                    if (callback == "Excluido com sucesso") {
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
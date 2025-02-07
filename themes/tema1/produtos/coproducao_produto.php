<?php
$v->layout("_theme");

if ($produto->usuario != $section->getId()) {
    header("Location: " . url("produtos/detalhe/") . $produto->id);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 style="font-family:inter"><?= $title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?= url("produtos") ?>">Produtos</a></li>
                        <li class="breadcrumb-item active">Coprodução</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
        <div class="callout menu-edicao">
            <?php if ($section->permissaoModuloBool("meus_produtos")) : ?>
                <a href="<?= url("produtos/detalhe/") . $produto->id; ?>">Detalhes</a>
            <?php endif ?>
            <?php if ($produto->usuario == $section->getId()) :
                $v->insert("produtos/menu-produto", ["produto" => $produto]);
            endif; ?>
            <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->

            <?php if ($section->permissaoModuloBool("campanha_produto")) : ?>
                <a href="<?= url("produtos/campanhas/") . $produto->id; ?>">Campanhas</a>
            <?php endif ?>
        </div>

        <?php if ($produto->status == 3 || $produto->status == 4) : ?>
            <section class="card">
                <div class="card-body">
                    <span class="badge bg-danger">Produto REPROVADO/BLOQUEADO</span>
                </div>
            </section>
        <?php endif; ?>
        <!-- Default box -->
        <div class="card card-body bg-azul-octa">
            O coprodutor/gerente só poderá ver as vendas e receber as comissões depois que aceitar a coprodução <br>
            <small>As vendas e comissões que tiver no período em que ele não aceitar não serão contabilizadas</small>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="produto" id="produto" value="<?= $produto->id ?>">
                        <input type="hidden" name="id" id="id">

                        <div class="row">

                            <div class="col-md-4">
                                <label>E-mail:</label>
                                <input type="email" name="email" id="email" class="form-control" onblur="verificaEmail()">
                                <label id="res-email" class="text-danger"></label><br>
                            </div>


                            <div class="col-md-4">
                                <label>Tipo:</label>
                                <select name="gerente" id="gerente" class="form-control" onchange="mostrar()">
                                    <option value="0">Coprodutor</option>
                                    <!-- <option value="1">Gerente</option> -->
                                </select>
                            </div>

                            <div id="coprodutor" class="col-md-4">
                                <label>Comissão:</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="tipo_comissao1" id="tipo_comissao1" class="form-control">
                                            <option value="0">%</option>
                                            <option value="1">R$</option>
                                        </select>
                                    </div>

                                    <div class="col-md-9">
                                        <input type="text" name="comissao1" id="comissao1" class="form-control" placeholder="0,00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="gerencia">
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Comissão SEM afiliado:</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select name="tipo_comissao2" id="tipo_comissao2" class="form-control">
                                                <option value="0">%</option>
                                                <option value="1">R$</option>
                                            </select>
                                        </div>

                                        <div class="col-md-9">
                                            <input type="text" name="comissao2" id="comissao2" class="form-control" placeholder="0,00">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label>Comissão COM afiliado:</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select name="tipo_comissao_afiliados" id="tipo_comissao_afiliados" class="form-control">
                                                <option value="0">%</option>
                                                <option value="1">R$</option>
                                            </select>
                                        </div>

                                        <div class="col-md-9">
                                            <input type="text" name="comissao_afiliados" id="comissao_afiliados" class="form-control" placeholder="0,00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Data Limite:</label>
                                <input type="date" name="data_limite" id="data_limite" class="form-control">
                                Deixe vazio para eterno
                            </div>
                        </div>
                    </div>
                    <div class="row center">
                        <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                            <button disabled class="btn btn-success" id="salvar">Salvar</button>
                        <?php else : ?>
                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                <button class="btn btn-success" id="salvar">Salvar</button>
                            <?php endif ?>
                        <?php endif; ?>

                        <button class="btn btn-warning" id="editar">Editar</button> &nbsp;
                        <button class="btn btn-danger" id="cancelar">Cancelar</button>
                        <br><br>
                    </div>
                </form>


            </div>

        </div>

        <div class="card">

            <div class="card-body p-0" style="overflow-x: auto">
                <?php if ($coproducao) : ?>
                    <script>
                        var dados = [];
                    </script>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Coprodutor</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Aceito</th>
                                <th scope="col" style="text-align: center">Comissão</th>
                                <th scope="col" style="text-align: center">Desde</th>
                                <th scope="col" style="text-align: center">Data Limite</th>
                                <th scope="col">Editar/Excluir</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $tipo = array(
                                0 => "Coprodutor",
                                1 => "Gerente"
                            );
                            $aceito = array(
                                0 => "Não",
                                1 => "Sim"
                            );
                            $tipoComissao = array(
                                0 => "%",
                                1 => "R$"
                            );

                            function formataComissao($tipo, $valor)
                            {
                                if ($tipo == 0) {
                                    return number_format($valor, 2, ",", ".") . "%";
                                } else {
                                    return "R$ " . number_format($valor, 2, ",", ".");
                                }
                            }

                            $i = 0;

                            foreach ($coproducao as $coproducao) :

                            ?>
                                <tr class="cli" id="afiliacao<?= $coproducao->co_produtor; ?>">
                                    <td>
                                        <?= $coproducao->dataCoProdutor()->nome; ?><br>
                                        <?= $coproducao->dataCoProdutor()->email ?>
                                    </td>
                                    <td><?= $tipo[$coproducao->gerente]; ?></td>
                                    <td><?= $aceito[$coproducao->aceito]; ?></td>

                                    <?php if ($coproducao->gerente == 1) : ?>
                                        <td align="center">
                                            COM afililiado: <?= formataComissao($coproducao->tipo_comissao_afiliados, $coproducao->comissao_afiliados) ?><br>
                                            SEM afililiado: <?= formataComissao($coproducao->tipo_comissao, $coproducao->comissao) ?>
                                        </td>
                                    <?php else : ?>
                                        <td align="center"><?= formataComissao($coproducao->tipo_comissao, $coproducao->comissao) ?></td>
                                    <?php endif; ?>
                                    <td align="center"><?= formataDataHora($coproducao->created_at); ?></td>
                                    <td align="center"><?php if ($coproducao->data_limite == "0000-00-00") echo " - ";
                                                        else echo formataData($coproducao->data_limite); ?></td>
                                    <td>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                            <button class="btn btn-warning btn-editar" onclick="editar(<?= $i; ?>)"><i class="fa fa-edit"></i></button>
                                        <?php endif ?>

                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $coproducao->id; ?>)"><i class="fa fa-times"></i></button>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id: "<?= $coproducao->id ?>",
                                        email: "<?= $coproducao->dataCoProdutor()->email ?>",
                                        gerente: "<?= $coproducao->gerente ?>",
                                        tipo_comissao: "<?= $coproducao->tipo_comissao ?>",
                                        comissao: "<?= $coproducao->comissao ?>",
                                        tipo_comissao_afiliados: "<?= $coproducao->tipo_comissao_afiliados ?>",
                                        comissao_afiliados: "<?= $coproducao->comissao_afiliados ?>",
                                        data_limite: "<?= $coproducao->data_limite ?>",
                                    }
                                </script>

                            <?php $i++;
                            endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <h4>Você ainda não tem coproduções para esse produto</h4>
                <?php endif; ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </section>

</div>


</section>

</div>

<!-- INICIO BOTÃO AJUDA E SUAS FUNCIONALIDADES -->
<button class="btn btn-primary btn-ajuda" id="btn-ajuda">
    <i class="far fa-question-circle"></i> <span class="desktop">Ajuda</span>
</button>

<?php
$assunto = "Preciso de Ajuda";
$para = "suporte.oficial@redrocketpay.com";
$mensagem = "Email: {$_SESSION["email"]} %0D" .
    "ID: {$_SESSION["id"]} %0D" .
    "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
    "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda. Estou com o seguinte problema: ";

$mensagem_whats = "Email: {$_SESSION["email"]} %0D" .
    "ID: {$_SESSION["id"]} %0D" .
    "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
    "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda.";
?>

<div class="ajuda">
    <div class="ajuda-titulo">
        Assistente
        <button type="button" class="close" id="ajuda-fechar"> &times;</button>
    </div>

    <div class="ajuda-corpo">
        <div class="mensagens">Olá <?= $_SESSION["nome_abreviado"] ?>, tudo bem? Como posso te ajudar?</div>
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/coproducao/" target="_blank">Dúvidas sobre Coprodução</a></div>
        <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
        <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
    </div>
</div>
<!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

<?= $v->start("scripts") ?>
<script>
    // INICIO BOTÃO DE AJUDA
    $("#btn-ajuda").click(() => {
        $(".ajuda").toggle("slow");
    });

    $("#ajuda-fechar").click(() => {
        $(".ajuda").hide("slow");
    });
    // FIM BOTÃO DE AJUDA

    $("#comissao1").blur(function(e) {
        var valor = $("#comissao1").val();
        $("#comissao1").val(valor.replace(",", "."));
    });

    $("#comissao2").blur(function(e) {
        var valor = $("#comissao2").val();
        $("#comissao2").val(valor.replace(",", "."));
    });

    $("#comissao_afiliados").blur(function(e) {
        var valor = $("#comissao_afiliados").val();
        $("#comissao_afiliados").val(valor.replace(",", "."));
    });

    var gerente = $("#gerente").val();
    var co_produtor;
    if (gerente == 1) {
        $("#coprodutor").css("display", "none");
        $("#gerencia").css("display", "block");
    } else {
        $("#coprodutor").css("display", "block");
        $("#gerencia").css("display", "none");
    }

    function mostrar() {
        var gerente = $("#gerente").val();
        if (gerente == 0) {
            $("#coprodutor").css("display", "block");
            $("#gerencia").css("display", "none");
            gerente = 0;
            console.log("gerente1 ", gerente);
        } else {
            $("#coprodutor").css("display", "none");
            $("#gerencia").css("display", "block");
            gerente = 1;
            console.log("gerente1 ", gerente);
        }
    }

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
                } else {
                    $("#res-email").css("display", "none");
                    $("#salvar").attr("disabled", false);
                }
            }
        });
    }

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });

    $("#editar").click(function(e) {
        e.preventDefault();
        salvarEditar(0);
    });

    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });

    function salvarEditar(salvar) {

        if (salvar == 1) {
            url = "<?= $router->route("produtos.coproducaoCadastro") ?>";
        } else {
            url = "<?= $router->route("produtos.coproducaoEditar") ?>";
        }
        console.log(url);

        var gerente = $("#gerente").val();
        var id = $("#id").val();
        console.log("url ", url, "id ", id, "gerente ", gerente);

        var produto = $("#produto").val();
        var email = $("#email").val();
        var data_limite = $("#data_limite").val();

        if (gerente == 1) {
            var tipo_comissao2 = $("#tipo_comissao2").val();
            var comissao2 = $("#comissao2").val();
            var tipo_comissao_afiliados = $("#tipo_comissao_afiliados").val();
            var comissao_afiliados = $("#comissao_afiliados").val();


            if (tipo_comissao2 == "" || comissao2 == "" || tipo_comissao_afiliados == "" || comissao_afiliados == "" || email == "") {
                alert("Preencha todos os campos");
            } else {
                console.log("comissao ", comissao2, comissao_afiliados);
                $.ajax({
                    url: url,
                    data: {
                        id: id,
                        produto: produto,
                        tipo_comissao: tipo_comissao2,
                        comissao: comissao2,
                        tipo_comissao_afiliados: tipo_comissao_afiliados,
                        comissao_afiliados: comissao_afiliados,
                        co_produtor: co_produtor,
                        gerente: 1,
                        aceito: 0,
                        todos_afiliados: 1,
                        data_limite: data_limite
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
                        if (callback == "Salvo com sucesso") {
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
        } else {

            var tipo_comissao1 = $("#tipo_comissao1").val();
            var comissao1 = $("#comissao1").val();

            console.log("comissao ", comissao1, co_produtor);
            if (tipo_comissao1 == "" || comissao1 == "" || email == "") {
                alert("Preencha todos os campos");
            } else {

                $.ajax({
                    url: url,
                    data: {
                        id: id,
                        produto: produto,
                        tipo_comissao: tipo_comissao1,
                        comissao: comissao1,
                        co_produtor: co_produtor,
                        gerente: 0,
                        aceito: 0,
                        todos_afiliados: 1,
                        data_limite: data_limite
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
                        if (callback == "Salvo com sucesso") {
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
    };

    function excluir(id) {
        var produto = $("#produto").val();
        console.log(id);

        if (confirm("Tem certeza que deseja excluir esse coprodutor/gerente?")) {
            $.ajax({
                url: "<?= $router->route("produtos.coproducaoExcluir") ?>",
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
                        // console.log(callback);
                        // console.log("sucesso");
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

    $("#editar").css("display", "none");
    $("#cancelar").css("display", "none");

    function editar(i) {
        var id = dados[i]["id"];
        var email = dados[i]["email"];
        var tipo = dados[i]["gerente"];
        var tipo_comissao = dados[i]["tipo_comissao"];
        var comissao = dados[i]["comissao"];
        var tipo_comissao_afiliados = dados[i]["tipo_comissao_afiliados"];
        var comissao_afiliados = dados[i]["comissao_afiliados"];
        var data_limite = dados[i]["data_limite"];

        console.log(id, email, tipo, tipo_comissao, comissao, tipo_comissao_afiliados, comissao_afiliados, data_limite);

        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $(".btn-editar, .btn-excluir").attr("disabled", "true");
        $("#id").attr({
            "value": id
        });
        $("#email").attr({
            "value": email,
            "readonly": true,
            "disabled": true
        });
        $("#gerente option[value=" + tipo + "]").attr({
            "selected": true
        });

        console.log("gerente ", gerente);
        mostrar();
        verificaEmail();
        console.log("gerente ", gerente);

        if (tipo == 1) {
            $("#tipo_comissao2 option[value=" + tipo_comissao + "]").attr({
                "selected": true
            });
            $("#comissao2").attr({
                "value": comissao
            });

            $("#tipo_comissao_afiliados option[value=" + tipo_comissao_afiliados + "]").attr({
                "selected": true
            });
            $("#comissao_afiliados").attr({
                "value": comissao_afiliados
            });
        } else {
            $("#tipo_comissao1 option[value=" + tipo_comissao + "]").attr({
                "selected": true
            });
            $("#comissao1").attr({
                "value": comissao
            });
        }

        $("#data_limite").attr({
            "value": data_limite
        });

    }

    function cancelar() {
        var tipo = $("#gerente").val();
        console.log("tipo ", tipo);
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "none");
        $(".btn-editar, .btn-excluir").attr("disabled", false);
        $("#id").attr({
            "value": ""
        });
        $("#email").attr({
            "value": "",
            "readonly": false,
            "disabled": false
        });
        $("#gerente option").attr({
            "selected": false
        });

        $("#tipo_comissao1").attr({
            "selected": true
        });
        $("#comissao1").attr({
            "value": ""
        });

        mostrar();

        $("#tipo_comissao2 option").attr({
            "selected": true
        });
        $("#comissao2").attr({
            "value": ""
        });

        $("#tipo_comissao_afiliados option").attr({
            "selected": true
        });
        $("#comissao_afiliados").attr({
            "value": ""
        });

        $("#data_limite").attr({
            "value": ""
        });

    }
</script>
<?= $v->end() ?>
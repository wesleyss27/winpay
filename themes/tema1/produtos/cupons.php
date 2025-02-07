<?php

$v->layout("_theme");

if ($produto->usuario != $section->getId()) {
    header("Location: " . url("produtos/detalhe/") . $produto->id);
}

$i = 1;

$status = array(
    1 => "EM ANÁLISE",
    2 => "APROVADO",
    3 => "REPROVADO",
    4 => "BLOQUEADO"
);

function meio_pagamento($pagamento)
{
    $pag = explode(",", $pagamento);

    $meio_pagamento = array(
        10 => "Todos",
        1  => "Cartão",
        2  => "Boleto",
        3  => "PIX"
    );

    foreach ($pag as $pagamento) {
        echo @$meio_pagamento[$pagamento] . "<br>";
    }
}

$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);
if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<style>
    #idSelect2 {
        color: #000;
        width: 100%;
    }

    .select2-selection__rendered li {
        color: #000;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 style="font-family:inter">Cupom de Desconto</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>

                        <li class="breadcrumb-item active">Cupom De Desconto</li>
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

        <!-- Default box -->
        <div class="card">
            <div class="card-header">

                <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                    <button disabled class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <span class="badge bg-danger">Produto REPROVADO/BLOQUEADO</span>
                <?php else : ?>
                    <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <?php endif ?>
                    O Cupom será ativado no checkout
                <?php endif; ?>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>
            <div class="card-bods">
                <div class="p-0">
                    <?php if ($cupons) : ?>
                        <script>
                            var dados = [];
                        </script>
                        <div style="overflow-x: auto;">

                            <table class="table table-striped projects" style="font-size:13px">
                                <thead>
                                    <tr style="font-family:inter">
                                        <th scope="col">ID</th>
                                        <th scope="col">Cupom</th>
                                        <!-- <th scope="col" style="text-align: center">Mais de uma compra</th> -->
                                        <th scope="col" style="text-align: center">Habilitado Planos</th>
                                        <th scope="col" style="text-align: center">Planos</th>
                                        <th scope="col" style="text-align: center">Valor Desconto</th>
                                        <th scope="col" style="text-align: center">Meio Pagamento Aceito</th>
                                        <th scope="col" style="text-align: center">Ativo</th>
                                        <th scope="col">Duplicar</th>
                                        <th scope="col">Editar/Excluir</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $porcentagem = array(
                                        0 => "%",
                                        1 => "R$"
                                    );

                                    function formataDesconto($porcentagem, $valor_porcentagem)
                                    {
                                        if ($porcentagem == 0) {
                                            return number_format($valor_porcentagem, 2, ",", ".") . "%";
                                        } else {
                                            return "R$ " . number_format($valor_porcentagem, 2, ",", ".");
                                        }
                                    }

                                    $i = 0;

                                    ?>
                                    <tr style="font-family:inter">
                                        <?php foreach ($cupons as $cupom) : ?>
                                            <th scope="row"><?= $cupom->id; ?></td>
                                            <th><?= $cupom->cupom; ?></td>
                                                <!-- <td align="center"><?= $ativo[$cupom->multicompra]; ?></td> -->
                                            <td align="center"><?= $ativo[$cupom->definir_plano]; ?></td>
                                            <td align="center">
                                                <?php
                                                if ($cupom->planos) :
                                                    $kits = explode(",", $cupom->planos);
                                                    foreach ($kits as $kit) :
                                                ?>
                                                        <?= $plano->findById($kit)->nome; ?><br>
                                                <?php endforeach;
                                                endif; ?>
                                                </th>
                                            <td align="center"><?= formataDesconto($cupom->porcentagem, $cupom->valor_porcentagem) ?></td>
                                            <td align="center"><?= meio_pagamento($cupom->meio_pagamento) ?></td>
                                            <td align="center"><?= $ativo[$cupom->ativo] ?></td>
                                            <td><button class="btn btn-link" onclick="duplicar(<?= $cupom->id; ?>)"><i class="far fa-copy"></i></button> </td>
                                            <?php if ($cupom->usuario == $section->getId()) : ?>
                                                <td>
                                                    <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                        <button class="btn btn-warning btn-sm" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                                    <?php endif ?>

                                                    <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                        <button class="btn btn-danger btn-sm" onclick="excluir(<?= $cupom->id; ?>)"><i class="fas fa-times"></i></button>
                                                    <?php endif ?>
                                                </td>
                                            <?php endif; ?>
                                    </tr>

                                    <form action="#" method="post" name="linha<?= $cupom->id; ?>">

                                        <input type="hidden" name="status" value="<?= $cupom->status ?>">
                                        <input type="hidden" name="produto" value="<?= $produto->id ?>">
                                        <input type="hidden" name="cupom" value="<?= $cupom->cupom ?> - copy">
                                        <!-- <input type="hidden" name="multicompra" value="<?= $cupom->multicompra ?>"> -->
                                        <input type="hidden" name="definir_plano" value="<?= $cupom->definir_plano ?>">

                                        <?php $plan = explode(",", $cupom->planos);
                                            $j = 0;
                                            foreach ($plan as $plan1) : ?>
                                            <input type="hidden" name="planos[<?= $j ?>]" value="<?= $plan1 ?>">
                                        <?php $j++;
                                            endforeach; ?>

                                        <input type="hidden" name="valor_porcentagem" value="<?= $cupom->valor_porcentagem ?>">
                                        <input type="hidden" name="porcentagem" value="<?= $cupom->porcentagem ?>">

                                        <?php $pagamentos = explode(",", $cupom->meio_pagamento);
                                            $j = 0;
                                            foreach ($pagamentos as $pagamento) : ?>
                                            <input type="hidden" name="meio_pagamento[<?= $j ?>]" value="<?= $pagamento ?>">
                                        <?php $j++;
                                            endforeach; ?>
                                        <input type="hidden" name="ativo" value="<?= $plano->ativo ?>">

                                    </form>

                                    <script>
                                        dados[<?= $i ?>] = {
                                            id: "<?= $cupom->id ?>",
                                            cupom: "<?= $cupom->cupom ?>",
                                            multicompra: "<?= $cupom->multicompra ?>",
                                            definir_plano: "<?= $cupom->definir_plano ?>",
                                            planos: "<?= $cupom->planos ?>",
                                            porcentagem: "<?= $cupom->porcentagem ?>",
                                            valor_porcentagem: "<?= $cupom->valor_porcentagem ?>",
                                            meio_pagamento: "<?= $cupom->meio_pagamento ?>",
                                            ativo: "<?= $cupom->ativo ?>",
                                        }
                                    </script>

                                <?php $i++;
                                        endforeach; ?>

                                </tbody>

                            </table>

                        <?php
                        //EXIBE A NAVEGAÇÃO DO PAGINADOR
                        $link = URL_BASE . "/cupons/";

                        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                        $paginador->paginas($total, $pagina, $link);

                    else :
                        ?>
                        </div>
                        <h4>Cadastre um cupom de desconto</h4>
                    <?php
                    endif;
                    ?>

                    <!-- cadastar -->
                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h3 class="card-title"><i class="fa fa-info-circle"></i>Cadastrar Cupom</h3>
                                    <button type="button" class="close" data-dismiss="modal"> &times;</button>
                                </div>

                                <form action="#" name="cadastro" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                                    <div class="modal-body" style="background: #f4f6f9">

                                        <div class="card card-body">
                                            <input type="hidden" name="id" id="id">
                                            <input type="hidden" name="produto" value="<?= $produto->id ?>">

                                            <span><b>Preencha os campos abaixo caso queira oferecer desconto no checkout</b></span>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Cupom:</label>
                                                    <input type="text" name="cupom" id='cupom' class="form-control input-lg">
                                                </div>

                                                <div class="col-md-4">
                                                    <label>Ativar:</label>
                                                    <select name="ativo" id="ativo" class="form-control input-lg">
                                                        <option value="0">Não</option>
                                                        <option value="1">Sim</option>
                                                    </select>
                                                </div>

                                                <!-- <div class="col-md-10">
                                                    <label>Clientes podem utilizar cupom mais de uma vez?</label>
                                                </div> -->

                                                <!-- <div class="row">
                                                    <div class="col-md-10">
                                                        <select name="multicompra" id="multicompra" class="form-control input-lg">
                                                            <option value="0">Não</option>
                                                            <option value="1">Sim</option>
                                                        </select>
                                                    </div>
                                                </div> -->

                                                <div class="col-md-4">
                                                    <label>Definir o(s) plano(s)?</label>
                                                    <select name="definir_plano" id="definir_plano" class="form-control input-lg">
                                                        <option value="0">Não</option>
                                                        <option value="1">Sim</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <span class="mostrar_plano">
                                                        <br>

                                                        <label for="idSelect2">
                                                            <b>Escolha o(s) Plano(s):</b>
                                                        </label>

                                                        <select class="select2" name="planos[]" id="planos" multiple="" tabindex="-1" style="display: none;">
                                                            <?php if ($planos) : $i = 1;
                                                                foreach ($planos as $planos) : $planos->privado == 1 ? $privado = "**" : $privado = ""; ?>
                                                                    <option value="<?= $planos->id ?>" id="opcao<?= $i ?>"><?= "$planos->codigo $planos->nome $privado" ?></option>
                                                            <?php $i++;
                                                                endforeach;
                                                            endif; ?>
                                                        </select>
                                                        Deixe em branco para manter os atuais
                                                    </span>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Tipo Desconto</label>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <select name="porcentagem" id="porcentagem" class="form-control">
                                                                <option value="0">%</option>
                                                                <option value="1">R$</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-8">
                                                            <input type="text" name="valor_porcentagem" id="valor_porcentagem" class="form-control" placeholder="0,00">
                                                        </div>
                                                    </div>
                                                    <small>Ex.: o produto custa R$ 100,00 e será aplicado um desconto de 30% e passará a custar R$ 70,00</small>
                                                </div>

                                                <br>

                                                <div class="col-md-4">
                                                    <label>Escolha o pagamento aceito</label>

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="10" name="meio_pagamento[10]" id="meio_pagamento10" checked>
                                                        <label class="form-check-label" for="meio_pagamento1">
                                                            Todos
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="1" name="meio_pagamento[1]" id="meio_pagamento1">
                                                        <label class="form-check-label" for="meio_pagamento2">
                                                            Cartão
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="2" name="meio_pagamento[2]" id="meio_pagamento2">
                                                        <label class="form-check-label" for="meio_pagamento3">
                                                            Boleto
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="3" name="meio_pagamento[3]" id="meio_pagamento3">
                                                        <label class="form-check-label" for="meio_pagamento4">
                                                            PIX
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                                        <button class="btn btn-primary" id="salvar">Salvar</button>
                                        <button class="btn btn-warning" id="editar">Editar</button>
                                    </div>
                                </form>

                            </div>

                        </div>

                    </div>
                    <!-- end cadastrar -->

                </div>

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
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/cupom-de-desconto/" target="_blank">Dúvidas sobre a criação de Cupons de Desconto</a></div>
        <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
        <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
    </div>
</div>
<!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

<?= $v->start("scripts") ?>


<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>

<script>
    // INICIO BOTÃO DE AJUDA
    $("#btn-ajuda").click(() => {
        $(".ajuda").toggle("slow");
    });

    $("#ajuda-fechar").click(() => {
        $(".ajuda").hide("slow");
    });
    // FIM BOTÃO DE AJUDA

    $(".select2").select2({
        width: '100%',
        theme: "classic"
    });

    $("#preco").blur(function(e) {
        var valor = $("#preco").val();
        $("#preco").val(valor.replace(",", "."));
    });

    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Planos");
        $("#salvar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#editar").css("display", "none");
        cancelar();
        console.log($("#valor_desconto").val());
    });

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


    var plano = 0;
    $(".mostrar_plano").css("display", "none");

    $("#definir_plano").change((e) => {
        console.log("plano", plano);
        if (plano == 0) {
            $(".mostrar_plano").css("display", "inline");
            plano = 1;
            console.log("mostrar", plano);
        } else {
            $(".mostrar_plano").css("display", "none");
            plano = 0;
            console.log("mostrar", plano);
        }
    });

    function mostrarPlano(plano) {

    }

    function salvarEditar(salvar) {
        if (salvar == 1) {
            url = "<?= $router->route("produtos.cupomCadastro"); ?>";
        } else {
            url = "<?= $router->route("produtos.cupomEditar") ?>";
        }

        var checkout = $("#checkout").val(),
            headline = $("#headline").val();

        var form = $("form[name=cadastro]");
        console.log(form.serialize());

        if (checkout == "" || headline == "") {

            alert("Preecha todos os dados necessários");

        } else {

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
    }

    function editar(i) {
        var id = dados[i]["id"];
        var cupom = dados[i]["cupom"];
        var multicompra = dados[i]["multicompra"];
        var definir_plano = dados[i]["definir_plano"];
        var planos = dados[i]["planos"];
        var porcentagem = dados[i]["porcentagem"];
        var valor_porcentagem = dados[i]["valor_porcentagem"];
        var meio_pagamento = dados[i]["meio_pagamento"];
        var ativo = dados[i]["ativo"];

        console.log(id, cupom, definir_plano, planos, porcentagem, valor_porcentagem, meio_pagamento, ativo);

        $(".modal-title").html("Editar Cupons");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": id
        });
        $("#cupom").attr({
            "value": cupom
        });
        // $("#multicompra option[value=" + multicompra + "]").attr({"selected": true});
        $("#definir_plano option[value=" + definir_plano + "]").attr({
            "selected": true
        });
        $("#ativo option[value=" + ativo + "]").attr({
            "selected": true
        });
        $("#porcentagem option[value=" + porcentagem + "]").attr({
            "selected": true
        });
        $("#valor_porcentagem").attr({
            "value": valor_porcentagem
        });

        var meios_pagamento = meio_pagamento.split(",");

        for (i = 0; i < 11; i++) {
            $("#meio_pagamento" + i).prop("checked", false);
        }

        meios_pagamento.forEach((indice, valor) => {
            $("#meio_pagamento" + indice).prop("checked", true);
            console.log("indice", indice);
        });

        if (planos) {
            var plan = planos.split(",");
            console.log(plan.length);

            for (var i = 0; i < plan.length; i++) {
                console.log(plan);
                $("#planos option[value=" + plan[i] + "]").prop({
                    "selected": true
                });
            }
        }

        if (definir_plano == 1) {
            $(".mostrar_plano").css("display", "inline");
            plano = 1;
        }

        if (porcentagem == 1) {
            $("#porcentagem option[value=" + porcentagem + "]").attr({
                "selected": true
            });
            $("#valor_porcentagem").attr({
                "value": valor_porcentagem
            });

        } else {
            $("#porcentagem option[value=" + porcentagem + "]").attr({
                "selected": true
            });
            $("#valor_porcentagem").attr({
                "value": valor_porcentagem
            });
        }
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": ""
        });
        $("#cupom").attr({
            "value": ""
        });
        // $("#multicompra option").attr({"selected": false});
        $("#definir_plano option").attr({
            "selected": false
        });
        $("#planos").attr({
            "value": ""
        });
        $("#ativo option").attr({
            "selected": false
        });
        $("#meio_pagamento option").attr({
            "selected": false
        });

        mostrar();

        $("#porcentagem option").attr({
            "selected": false
        });
        $("#valor_porcentagem").attr({
            "value": ""
        });

    }

    function duplicar(id) {
        var form = $("form[name=linha" + id + "]");
        console.log(form.serialize());

        $.ajax({

            url: "<?= $router->route("produtos.cupomCadastro"); ?>",
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

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse cupom?")) {
            $.ajax({
                url: "<?= $router->route("produtos.cupomExcluir") ?>",
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
<?= $v->end() ?>

<!-- TAB CUPONS -->

<!-- FIM TAB CUPONS -->
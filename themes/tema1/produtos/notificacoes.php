<?php

$v->layout("_theme");
$i = 1;

$status = array(
    1 => "EM ANÁLISE",
    2 => "APROVADO",
    3 => "REPROVADO",
    4 => "BLOQUEADO"
);
$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);

if ($produto->usuario != $section->getId()) {
    header("Location: " . url("produtos/detalhe/") . $produto->id);
}

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 style="font-family:inter">Notificações</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>

                        <li class="breadcrumb-item active">Notificações Checkout</li>
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
            <div class="card-body">
                <div class="p-0">
                    <form action="#" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">

                        <input type="hidden" name="id" id="id" value="<?= @$notificacoes->id ?>">
                        <input type="hidden" name="produto" value="<?= $produto->id ?>">

                        <!-- TAB NOTIFICAÇÕES -->
                        HABILITE AS NOTIFICAÇÕES CASO QUEIRA MOSTRAR SEUS CLIENTES QUE PESSOAS ESTÃO COMPRANDO SEU PRODUTO.
                        <br><br>
                        <div class="row">
                            <div class="col-md-2">

                                <b>Habilitar Notificações</small></b>
                                <select name="ativo" id="usar_notificacoes" class="form-control">
                                    <option value="0" <?= (@$notificacoes->ativo == 0) ? 'selected' : '' ?>>Não</option>
                                    <option value="1" <?= (@$notificacoes->ativo == 1) ? 'selected' : '' ?>>Sim</option>
                                </select><br><br>
                            </div>

                            <!-- .col -->
                            <div class="form-check col-md-5 mostrar_notificacoes">
                                <?php
                                $frases = explode(",", @$notificacoes->frases);
                                if ($frases) {
                                    foreach ($frases as $frases) {
                                        $f[$frases] = $frases;
                                    }
                                }
                                ?>
                                <b>Alertas </b>
                                <br><br>

                                <!-- FRASE 1 -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-check-input" name="frases[0]" type="checkbox" value="1" <?= (@$f[1] == 1) ? 'checked' : '' ?> id="frase1">
                                        <label class="form-check-label" for="inlineCheck1">
                                            XX - Pessoas estão comprando <td><?= $produto->nome ?></td> nesse momento.
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="valor1" id='valor1' value="<?= @$notificacoes->valor1 ?>" class="form-control">
                                        <small>Valor Mínimo</small>
                                    </div>
                                </div>
                                <!-- FIM FRASE 1 -->

                                <!-- FRASE 2 -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-check-input" name="frases[1]" type="checkbox" value="2" <?= (@$f[2] == 2) ? 'checked' : '' ?> id="frase2">
                                        <label class="form-check-label" for="inlineCheck2">
                                            XX - Pessoas compraram <td><?= $produto->nome ?></td> essa semana.
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="valor2" id='valor2' value="<?= @$notificacoes->valor2 ?>" class="form-control">
                                        <small>Valor Mínimo</small>
                                    </div>
                                </div>
                                <!-- FIM FRASE 2 -->

                                <!-- FRASE 3 -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-check-input" name="frases[2]" type="checkbox" value="3" <?= (@$f[3] == 3) ? 'checked' : '' ?> id="frase3">
                                        <label class="form-check-label" for="inlineCheck3">
                                            XX - Pessoas compraram <td><?= $produto->nome ?></td> nos últimos 30 minutos.
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="valor3" id='valor3' value="<?= @$notificacoes->valor3 ?>" class="form-control">
                                        <small>Valor Mínimo</small>
                                    </div>
                                </div>
                                <!-- FIM FRASE 3 -->

                                <!-- FRASE 4 -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-check-input" name="frases[3]" type="checkbox" value="4" <?= (@$f[4] == 4) ? 'checked' : '' ?> id="frase4">
                                        <label class="form-check-label" for="inlineCheck4">
                                            XX - Pessoas compraram <td><?= $produto->nome ?></td> hoje.
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="valor4" id='valor4' value="<?= @$notificacoes->valor4 ?>" class="form-control">
                                        <small>Valor Mínimo</small>
                                    </div>
                                </div>
                                <!-- FIM FRASE 4 -->
                            </div>
                            <!-- fim .col -->

                            <!-- .col -->
                            <div class="form-check col-md-5 mostrar_notificacoes">
                                <!-- FRASE 5 -->
                                <br><br>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-check-input" name="frases[4]" type="checkbox" value="5" <?= (@$f[5] == 5) ? 'checked' : '' ?> id="frase5">
                                        <label class="form-check-label" for="inlineCheck5">
                                            XX - Pessoas compraram <td><?= $produto->nome ?></td> na última hora.
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="valor5" id='valor5' value="<?= @$notificacoes->valor5 ?>" class="form-control">
                                        <small>Valor Mínimo</small>
                                    </div>
                                </div>
                                <!-- FIM FRASE 5 -->

                                <!-- FRASE 6 -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-check-input" name="frases[5]" type="checkbox" value="6" <?= (@$f[6] == 6) ? 'checked' : '' ?> id="frase6">
                                        <label class="form-check-label" for="inlineCheck6">
                                            "CLIENTE" comprou <td><?= $produto->nome ?></td>.
                                        </label>
                                    </div>
                                </div>
                                <!-- FIM FRASE 6 -->

                                <!-- FRASE 7 -->
                                <br><br>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-check-input" name="frases[6]" type="checkbox" value="7" <?= (@$f[7] == 7) ? 'checked' : '' ?> id="frase7">
                                        <label class="form-check-label" for="inlineCheck7">
                                            "CLIENTE" acabou de comprar <td><?= $produto->nome ?></td>.
                                        </label>
                                    </div>
                                </div>
                                <!-- FIM FRASE 7 -->
                                <br>
                            </div>
                            <!-- fim .col -->
                        </div>
                        <!-- fim .row -->
                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                            <?php if (@!$notificacoes) : ?>
                                <button class="btn btn-primary" id="salvar">Salvar</button>
                            <?php elseif (@$notificacoes) : ?>
                                <button class="btn btn-warning" id="editar">Salvar</button>
                            <?php else : ?>
                                <button class="btn btn-warning" id="editar">Salvar</button>
                            <?php endif; ?>
                        <?php endif ?>
                </div>
                <!-- FIM TAB NOTIFICAÇÕES -->
                </form>
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
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/notificacoes-no-checkout/" target="_blank">Dúvidas sobre Notificações no Checkout</a></div>
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
        console.log($("#notificacoes").val());
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

    var notificacao = <?= @$notificacoes->ativo ? $notificacoes->ativo : 0 ?>;

    if (notificacao == 0) {
        $(".mostrar_notificacoes").css("display", "none");
    } else {
        $(".mostrar_notificacoes").css("display", "inline");
    }

    $("#usar_notificacoes").change((e) => {
        if (notificacao == 0) {
            $(".mostrar_notificacoes").css("display", "inline");
            notificacao = 1;
        } else {
            $(".mostrar_notificacoes").css("display", "none");
            notificacao = 0;
        }
    });

    function salvarEditar(salvar) {
        if (salvar == 1) {
            url = "<?= $router->route("produtos.notificacaoCadastro"); ?>";
        } else {
            url = "<?= $router->route("produtos.notificacaoEditar") ?>";
        }

        var form = $("form");

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

    function editar(i) {
        var id = dados[i]["id"];
        var notificacoes = dados[i]["frases"];
        var notificacoes = dados[i]["valor1"];
        var notificacoes = dados[i]["valor2"];
        var notificacoes = dados[i]["valor3"];
        var notificacoes = dados[i]["valor4"];
        var notificacoes = dados[i]["valor5"];
        var ativo = dados[i]["ativo"];

        $("#usar_notificacoes").attr({
            "value=": usar_notificacoes
        });
        $("#frases").attr({
            "value": frases
        });
        $("#valor1").attr({
            "value": valor1
        });
        $("#valor1").attr({
            "value": valor2
        });
        $("#valor1").attr({
            "value": valor3
        });
        $("#valor1").attr({
            "value": valor4
        });
        $("#valor1").attr({
            "value": valor5
        });


        console.log(id, notificacao, porcentagem, ativo);

        $(".modal-title").html("Editar Notificacoes");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": id
        });
        $("#notificacao").attr({
            "value": notificacao
        });
        $("#ativo option[value=" + ativo + "]").attr({
            "selected": true
        });
        $("#porcentagem").attr({
            "value": porcentagem
        });
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": ""
        });
        $("#notificacao").attr({
            "value": ""
        });
        $("#ativo option").attr({
            "selected": false
        });
        $("#porcentagem").attr({
            "value": ""
        });
    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir a notificação?")) {
            $.ajax({
                url: "<?= $router->route("produtos.notificacaoExcluir") ?>",
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
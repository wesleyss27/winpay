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
$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);
if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
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
                    <h4 style="font-family:inter"><?= $title ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>

                        <li class="breadcrumb-item active">Upsell</li>
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
                    <div class="row">
                        <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                            </div>
                        <?php endif ?>

                        <div class="col-md-10">
                            O Upsell será ativado na página de obrigado <br> <small>Upsell para PIX ainda em fase de teste</small>
                        </div>
                    </div>

                <?php endif; ?>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>
            <div class="card-bod">
                <div class="p-0">
                    <?php if ($upsell) : ?>
                        <script>
                            var dados = [];
                        </script>
                        <div style="overflow-x: auto;">

                            <table class="table table-striped projects" style="font-size:13px">
                                <thead>
                                    <tr style="font-family:inter">
                                        <th scope="col">ID</th>
                                        <th scope="col">Código</th>
                                        <th scope="col">Headline</th>
                                        <th scope="col">Checkout</th>
                                        <th scope="col">Plano</th>
                                        <th scope="col">Ativo</th>
                                        <th scope="col">Boleto</th>
                                        <th scope="col">Ação</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr style="font-family:inter">
                                        <?php foreach ($upsell as $upsell) : ?>
                                            <th scope="row"><?= $upsell->id; ?></th>
                                            <th><?= $upsell->codigo; ?></th>
                                            <td>
                                                <?= $upsell->headline; ?><br>
                                                <?= $upsell->subheadline; ?>
                                            </td>
                                            <td>
                                                <?php if ($upsell->checkout) : ?>
                                                    <?= @$upsell->dataCheckout()->descricao; ?><br>
                                                    <a href="<?= url("checkout/" . @$upsell->dataCheckout()->codigo) ?>" target="_blank"><?= url("checkout/" . @$upsell->dataCheckout()->codigo) ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $upsell->plano ? $upsell->dataPlano()->nome : "-"; ?>
                                            </td>
                                            <td><?= $ativo[$upsell->ativar] ?></td>
                                            <td><?= $ativo[$upsell->boleto] ?></td>
                                            <?php if ($upsell->usuario == $section->getId()) : ?>
                                                <td>
                                                    <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                        <button class="btn btn-warning btn-sm" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                                    <?php endif ?>

                                                    <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                        <button class="btn btn-danger btn-sm" onclick="excluir(<?= $upsell->id; ?>)"><i class="fas fa-times"></i></button>
                                                    <?php endif ?>
                                                </td>
                                            <?php endif; ?>
                                    </tr>

                                    <script>
                                        dados[<?= $i ?>] = {
                                            id: "<?= $upsell->id ?>",
                                            checkout: "<?= $upsell->checkout ?>",
                                            plano: "<?= $upsell->plano ?>",
                                            ativar: "<?= $upsell->ativar ?>",
                                            boleto: "<?= $upsell->boleto ?>",
                                            headline: "<?= $upsell->headline ?>",
                                            subheadline: "<?= $upsell->subheadline ?>",
                                        }
                                    </script>

                                <?php $i++;
                                        endforeach; ?>

                                </tbody>
                            </table>

                        <?php
                        //EXIBE A NAVEGAÇÃO DO PAGINADOR
                        $link = URL_BASE . "/upsell/";

                        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                        $paginador->paginas($total, $pagina, $link);

                    else :
                        ?>
                        </div>
                        <h4>Clique em Cadastrar para ter um upsell</h4>
                    <?php
                    endif;
                    ?>

                    <!-- cadastar -->
                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Upsell</h3>
                                    <button type="button" class="close" data-dismiss="modal"> &times;</button>
                                </div>

                                <form action="#" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                                    <div class="modal-body" style="background: #f4f6f9">

                                        <div class="card card-body">
                                            <input type="hidden" name="id" id="id">
                                            <input type="hidden" name="produto" value="<?= $produto->id ?>">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Ativar *</label>
                                                    <select name="ativar" id="ativar" class="form-control input-lg">
                                                        <option value="1">Sim</option>
                                                        <option value="0">Não</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Executar no Checkout</label>
                                                    <select name="checkout" id="checkout" class="form-control input-lg">
                                                        <option value="">Selecione um Checkout</option>
                                                        <?php
                                                        if (@$checkouts) :
                                                            foreach ($checkouts as $checkout) :
                                                        ?>
                                                                <option value="<?= $checkout->id ?>"><?= $checkout->descricao ?></option>
                                                        <?php
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Oferecer Plano</label>
                                                    <select name="plano" id="plano" class="form-control input-lg">
                                                        <option value="">Selecione um Plano</option>
                                                        <?php
                                                        if (@$planos) :
                                                            foreach ($planos as $plano) :
                                                        ?>
                                                                <?php if ($plano->dataProduto()->excluir != 1) : ?>
                                                                    <option value="<?= $plano->id ?>"><?= "{$plano->dataProduto()->nome} / $plano->nome" ?></option>
                                                        <?php
                                                                endif;
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Executar no boleto</label>
                                                    <select name="boleto" id="boleto" class="form-control input-lg">
                                                        <option value="1">Sim</option>
                                                        <option value="0">Não</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Headline *</label>
                                                    <input type="text" name="headline" id='headline' class="form-control input-lg" placeholder="Headline">
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Sub-Headline *</label>
                                                    <input type="text" name="subheadline" id='subheadline' class="form-control input-lg" placeholder="Headline">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/upsell/" target="_blank">Dúvidas sobre a criação de Upsell</a></div>
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

    function salvarEditar(salvar) {
        if (salvar == 1) {
            url = "<?= $router->route("produtos.upsellCadastro"); ?>";
        } else {
            url = "<?= $router->route("produtos.upsellEditar") ?>";
        }

        var headline = $("#headline").val();

        var form = $("form");

        if (headline == "") {
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
        var checkout = dados[i]["checkout"];
        var ativar = dados[i]["ativar"];
        var boleto = dados[i]["boleto"];
        var headline = dados[i]["headline"];
        var subheadline = dados[i]["subheadline"];
        var plano = dados[i]["plano"];

        console.log(id);

        $(".modal-title").html("Editar Upsell");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": id
        });

        if (checkout)
            $("#checkout option[value=" + checkout + "]").attr({
                "selected": true
            });

        $("#ativar option[value=" + ativar + "]").attr({
            "selected": true
        });
        $("#boleto option[value=" + boleto + "]").attr({
            "selected": true
        });
        $("#plano option[value=" + plano + "]").attr({
            "selected": true
        });
        $("#headline").attr({
            "value": headline
        });
        $("#subheadline").attr({
            "value": subheadline
        });
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": ""
        });
        $("#checkout option").attr({
            "selected": false
        });
        $("#ativar option").attr({
            "selected": false
        });
        $("#boleto option").attr({
            "selected": false
        });
        $("#plano option").attr({
            "selected": false
        });
        $("#subheadline").attr({
            "value": ""
        });
    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse upsell?")) {
            $.ajax({
                url: "<?= $router->route("produtos.upsellExcluir") ?>",
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
<?php

$v->layout("_theme");

$status = array(
    0 => "Rascunho",
    1 => "Em Análise",
    2 => "Aprovado",
    3 => "Reprovado",
    4 => "Bloqueado"
);

$bg = array(
    0 => "bg-info",
    1 => "bg-info",
    2 => "bg-success",
    3 => "bg-danger",
    4 => "bg-danger"
);

$tipo_doc = array(
    1 => "CPF",
    2 => "CNPJ"
);

$simOuNao = array(
    "" => "NÃO",
    0 => "NÃO",
    1 => "SIM",
);

$buscaAfiliacao = $afiliacao->find("afiliado = '{$_SESSION["id"]}' AND produto = '" . @$produto->id . "'")->fetch();
$buscaAfiliacaoApro = $afiliacao->find("status = 2 AND afiliado = '{$_SESSION["id"]}' AND produto = '" . @$produto->id . "'")->fetch();
$BuscaCheckout = $checkout->find("produto = '" . @$produto->id . "'")->fetch(true);
//var_dump($buscaAfiliacaoApro);

include "tipo.php";

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<div class="ajax_load">
    <div class="ajax_load_box">
        <div class="ajax_load_box_circle"></div>
        <div class="ajax_load_box_title">Aguarde, carregando!</div>
    </div>
</div>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <?php if ($produto) : ?>

        <!-- Main content -->
        <section class="content">

            <!-- menu produtores -->
            <br>
            <!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
            <?php if ($buscaAfiliacaoApro || $produto->usuario == $_SESSION["id"]) : ?>
                <div class="callout menu-edicao">
                    <a href="<?= url("produtos/detalhe/") . $produto->id; ?>">Detalhes</a>
                    <?php if ($produto->usuario == $_SESSION["id"]) :
                        $v->insert("produtos/menu-produto", ["produto" => $produto]);
                    endif; ?>
                    <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
                    <a href="<?= url("produtos/campanhas/") . $produto->id; ?>">Campanhas</a>
                </div>
            <?php endif; ?>

            <br>
            <!-- end menu produtores -->

            <!-- CONVITE -->
            <?php if (@$_GET["convite"]) : ?>
                <div class="bg-info alert">
                    <h5>Parabéns!</h5>

                    Você recebeu um convite de afiliação <br>
                    Clique em <b> Promover Esse Produto </b> e comece a faturar <br>
                </div>
            <?php endif; ?>
            <!-- FIM CONVITE -->

            <!-- SE PRODUTOR OCULTAR OPÇÕES DE PROMOVER -->
            <!--?php if($produto->usuario != $_SESSION["id"] && ($_SESSION["tipo"] == 4 || $_SESSION["tipo"] == 5)): ?-->
            <div class="row">
                <div class="col-md-7"></div>
                <div class="col-md-5">
                    <?php if ($produto->usuario != $_SESSION["id"]) : ?>
                        <?php
                        if ($produto->abrir_afiliacao == 1) :
                        ?>
                            <?php if ($buscaAfiliacao == null) : ?>
                                <buttom class="btn btn-primary" id="promover">Promover Esse Produto</buttom> &nbsp;

                            <?php elseif ($buscaAfiliacaoApro) : ?>
                                <button disabled class="btn btn-success"> Afiliação Aprovada</button> &nbsp;
                            <?php else : ?>
                                <button disabled class="btn <?= @$bg[@$buscaAfiliacao->status] ?>"> Afiliação <?= @$status[@$buscaAfiliacao->status] ?></button> &nbsp;
                            <?php endif; ?>

                            <button class="btn btn-danger" id="denunciar" data-target="#modal-lg" data-toggle="modal">Denunciar Esse Produto</button>
                        <?php endif; ?>
                        <br><br>
                    <?php endif; ?>
                </div>
            </div>
            <!-- FIM SE PRODUTOR OCULTAR OPÇÕES DE PROMOVER -->

            <!-- Default box -->
            <div class="card card-solid">
                <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

                <div class="card-body" style="font-family:inter">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <h3 class="d-inline-block d-sm-none"><?= $produto->nome; ?></h3>
                            <div class="col-12">

                                <div align="center" class="img-detalhe">
                                    <img src="<?= url("uploads/produtos/" . $produto->foto) ?>" alt="Imagem do Produto" style="border-radius: 5px">
                                    <br><br>
                                </div>

                                <div class="mt-4">
                                    <?php if (($_SESSION["tipo"] != 4 && $_SESSION["tipo"] != 5 && $_SESSION["tipo"] != 6) && $produto->status != 4) : ?>

                                        <form action="<?= $router->route("produtos.editarStatus") ?>" method="post">

                                            <?php if (
                                                ($produto->usuario == $_SESSION["id"] ||
                                                    $_SESSION["tipo"] == 1 ||
                                                    $_SESSION["tipo"] == 2 ||
                                                    $_SESSION["tipo"] == 3) &&
                                                $produto->observacao != ""

                                            ) : ?>
                                                <b>Observação:</b>
                                                <pre><?= $produto->observacao ?></pre>
                                            <?php endif; ?>

                                            <div class="card">
                                                <div class="card-body">


                                                    <b>Adicionar observação: </b><small>(opcional)</small>
                                                    <textarea name="observacao" id="observacao" class="form-control" cols="30" rows="4"></textarea><br>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="hidden" name="id" value="<?= $produto->id; ?>">
                                                            <label>Status </label>

                                                            <select name="status" id="status" class="form-control">
                                                                <option value="">Status</option>
                                                                <option value="2" <?= $produto->status == 2 ? "selected" : "" ?>>APROVADO</option>
                                                                <option value="3" <?= $produto->status == 3 ? "selected" : "" ?>>REPROVADO</option>
                                                                <option value="4" <?= $produto->status == 4 ? "selected" : "" ?>>BLOQUEADO</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>Integrar com Logística St4rt? </label>

                                                            <select name="st4rt" id="st4rt" class="form-control">
                                                                <option value="0" <?= $produto->st4rt == 0 ? "selected" : "" ?>>Não</option>
                                                                <option value="1" <?= $produto->st4rt == 1 ? "selected" : "" ?>>Sim</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <br>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button class="btn btn-success form-control" id="atualizar">Atualizar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                    <?php endif; ?>
                                    <br><br>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <?php if ($produto->usuario == $_SESSION["id"] || ($_SESSION["tipo"] != 4 && $_SESSION["tipo"] != 5)) : ?>
                                <div class="row">
                                    <div class="col-md-6 <?= $bg[$produto->status] ?>" style="border-radius: 5px"><?= $status[$produto->status] ?></div>
                                    <div class="col-md-6"><button class="btn btn-danger btn-sm">Excluir</button></div>
                                </div>
                            <?php endif; ?>
                            <h3 class="my-3"><?= $produto->nome; ?>

                                <?php if (round($produto->rockets) == 0) : ?>
                                    <img src="<?= url("themes/tema1/images/foguete2.png"); ?>" alt="" title="Rockets <?= $produto->rockets; ?>">
                                    <?php
                                else :
                                    $round = round($produto->rockets);
                                    for ($i = 1; $i <= $round; $i++) :
                                    ?>
                                        <img src="<?= url("themes/tema1/images/foguete1.png"); ?>" alt="" title="Rockets <?= $produto->rockets; ?>">
                                <?php
                                    endfor;
                                endif;
                                ?>
                                <small class="badge bg-warning" style="font-size:14px">código: <?= $produto->codigo; ?></small>
                            </h3>

                            <?php if (@!$planos) : ?>
                                <div class="bg-success py-2 px-3 mt-4" style="border-radius: 5px">
                                    <h2 class="mb-0">
                                        <small>Preço:</small> <b>R$ <?= number_format($produto->preco, 2, ",", "."); ?></b>
                                    </h2>
                                </div>
                                <br />
                            <?php endif; ?>

                            <b>Suporte:</b> <?= $produto->email_suporte; ?>

                            <?php if ($urls) : ?>
                                <br><b>Página de vendas:</b>
                                <?php
                                foreach ($urls as $url) :
                                    if ($url->pv == 1) :
                                ?>
                                        <a href="<?= $url->url ?>" target="_blank"><?= $url->url ?></a>
                            <?php
                                    endif;
                                endforeach;
                            endif;
                            ?> <br>

                            <?php if ($produto->tipo_produto == 11) : ?>
                                <b>Link de acesso membros: </b> <a href="<?= @$produto->url_membros ?>" target="_blank"><?= @$produto->url_membros ?></a><br>
                            <?php endif; ?>
                            <br>

                            <b>Categoria:</b> <?= $categoria[$produto->categoria]; ?> <br>
                            <b>Tipo de produto:</b> <?= $tipoProduto[$produto->tipo_produto]; ?> <br><br>

                            <b>Comissão afiliado:</b>
                            <?php
                            if ($buscaAfiliacao) {
                                if ($buscaAfiliacao->tipo_comissao == 0)
                                    echo number_format($buscaAfiliacao->comissao_afiliado, 2, ",", ".") . "%";
                                else
                                    echo "R$ " . number_format($buscaAfiliacao->comissao_afiliado, 2, ",", ".");
                            } else {
                                if ($produto->tipo_comissao == 0)
                                    echo number_format($produto->comissao_afiliado, 2, ",", ".") . "%";
                                else
                                    echo "R$ " . number_format($produto->comissao_afiliado, 2, ",", ".");
                            }
                            ?><br>
                            <b>Tempo de cookie:</b> <?= $produto->tempo_cookie; ?> dias <br><br>

                            <b>Descrição:</b> <br>
                            <?= $produto->descricao; ?><br><br>

                            <?php if (@$planos) : ?>

                                <div class="bg-info alert">
                                    <b>Planos:</b>
                                </div>
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Plano</th>
                                            <th>Preço</th>
                                            <?php if ($produto->usuario == $_SESSION["id"] || ($_SESSION["tipo"] != 4 && $_SESSION["tipo"] != 5)) : ?>
                                                <th>Código</th>
                                            <?php endif; ?>
                                            <th>Comissão</th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($planos as $plano) : ?>
                                        <tr>
                                            <td><?= $plano->nome ?></td>
                                            <td>R$ <?= number_format($plano->preco, 2, ",", ".") ?></td>
                                            <?php if ($produto->usuario == $_SESSION["id"] || ($_SESSION["tipo"] != 4 && $_SESSION["tipo"] != 5)) : ?>
                                                <td><?= $plano->codigo ?></td>
                                            <?php endif; ?>
                                            <td>até R$
                                                <?php
                                                if ($buscaAfiliacao) {
                                                    if ($produto->tipo_comissao == 0)
                                                        $comissao = $plano->preco * ($buscaAfiliacao->comissao_afiliado / 100);
                                                    else
                                                        $comissao = $buscaAfiliacao->comissao_afiliado;
                                                } else {
                                                    if ($produto->tipo_comissao == 0)
                                                        $comissao = $plano->preco * ($produto->comissao_afiliado / 100);
                                                    else
                                                        $comissao = $produto->comissao_afiliado;
                                                }
                                                echo number_format($comissao, 2, ",", ".");
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endif; ?>

                            <br />
                            <b>Descrição Afiliação:</b> <br>
                            <textarea cols="30" rows="6" class="form-control" readonly><?= $produto->descricao_afiliados; ?></textarea>

                            <br>
                            <b>Termos de Afiliação:</b> <br>
                            <textarea cols="30" rows="6" class="form-control" readonly><?= $produto->termos; ?></textarea>

                            <br />

                        </div>

                    </div>

                    <?php if (@$buscaAfiliacao->status == 2 || $_SESSION["id"] == $produto->usuario || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                        <div class="row mt-4">
                            <nav class="w-100">
                                <div class="nav nav-tabs" id="product-tab" role="tablist" style="font-family:inter">
                                    <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : $active = "active"; ?>
                                        <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">PRODUTOR</a>
                                        <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">URL's</a>
                                    <?php else : ?>
                                        <a class="nav-item nav-link active" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">URL's</a>
                                    <?php endif ?>

                                    <a class="nav-item nav-link" id="product-rating-tab" data-toggle="tab" href="#product-rating" role="tab" aria-controls="product-rating" aria-selected="false">MÍDIAS</a>
                                </div>
                            </nav>

                            <div class="tab-content p-3" id="nav-tabContent" style="font-family:inter">
                                <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : $active = ""; ?>
                                    <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">
                                        <br />

                                        <b>Nome:</b> <?= $produto->dataProdutor()->nome; ?> - <b><?= $tipo_doc[$usuario->tipo_doc]; ?>:</b> <?= $usuario->cpf_cnpj; ?><br>
                                        <b>Email:</b> <?= $usuario->email; ?> - <b>Telefone:</b> <?= $usuario->telefone; ?><br>
                                        <b>Endereço:</b> <?= $usuario->rua; ?>, <?= $usuario->numero; ?>, <?= $usuario->bairro; ?><br>
                                        <?= $usuario->cidade; ?> - <?= $usuario->estado; ?>

                                        <br><br>

                                        <b>Cliente St4rt: </b><?= $simOuNao[$produto->dataProdutor()->cliente_st4rt]; ?><br>

                                        <b>Tipo de produto: </b> <?= $tipoProduto[$produto->tipo_produto]; ?><br>

                                        <?php if ($produto->tipo_produto == 8) : ?>
                                            <b>Link de rastreio: </b> <a href="<?= @$produto->link_rastreio ?>" target="_blank"><?= @$produto->link_rastreio ?></a><br>
                                        <?php endif; ?>

                                        <?php if ($produto->tipo_produto == 11) : ?>
                                            <b>Link de acesso membros: </b> <a href="<?= @$produto->url_membros ?>" target="_blank"><?= @$produto->url_membros ?></a><br>
                                        <?php endif; ?>

                                        <br>

                                        <b>Documento técnico:</b>
                                        <a href="<?= url("uploads/documentos/") . $produto->documentos ?>" target="_blank"><?= $produto->documentos ?></a><br>

                                        <br>
                                        <b>Visualização no marketplace: </b> <?= @$simOuNao[$produto->visualizacao] ?>

                                        <br><br>
                                        <b>Página de obrigado boleto: </b> <a href="<?= @$produto->obrigado_boleto ?>" target="_blank"><?= @$produto->obrigado_boleto ?></a><br>
                                        <b>Página de obrigado cartão: </b> <a href="<?= @$produto->obrigado_cartao ?>" target="_blank"><?= @$produto->obrigado_cartao ?></a><br>
                                        <b>Página de obrigado análise: </b> <a href="<?= @$produto->obrigado_analise ?>" target="_blank"><?= @$produto->obrigado_analise ?></a><br>

                                    </div>
                                <?php else : $active = "active"; ?>
                                <?php endif ?>

                                <div class="tab-pane fade show <?= $active; ?>" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">
                                    <h4>Links Cadastrados</h4>
                                    <?php
                                    if ($urls) :
                                        foreach ($urls as $url) :
                                            if ($url->privada == 0) :
                                                if ($buscaAfiliacao) :
                                    ?>
                                                    <?= $url->descricao ?>:<br>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="<?= url("a/$produto->id/" . $buscaAfiliacao->codigo . "/?pg=" . $url->id . "&tp=5") ?>">
                                                        <div class="input-group-append">
                                                            <a href="<?= url("a/$produto->id/" . $buscaAfiliacao->codigo . "/?pg=" . $url->id . "&tp=5") ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                                        </div>
                                                    </div>
                                                    <br>
                                                <?php
                                                else :
                                                ?>
                                                    <?= $url->descricao ?>:<br>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="<?= $url->url ?>">
                                                        <div class="input-group-append">
                                                            <a href="<?= $url->url ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                                        </div>
                                                    </div>
                                                    <br>
                                    <?php
                                                endif;
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>

                                    <h4>URL's de Checkout</h4>
                                    <?php
                                    if ($BuscaCheckout) :
                                        foreach ($BuscaCheckout as $checkout) :
                                            if ($checkout->privado == 0) :
                                                if ($buscaAfiliacao) :
                                    ?>
                                                    <?= $checkout->descricao ?><br>

                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="<?= url("a/$produto->id/" . $buscaAfiliacao->codigo . "/?ch=" . $checkout->codigo . "&tp=5") ?>">
                                                        <div class="input-group-append">
                                                            <a href="<?= url("a/$produto->id/" . $buscaAfiliacao->codigo . "/?ch=" . $checkout->codigo . "&tp=5") ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                                        </div>
                                                    </div>
                                                    <br>

                                                <?php else : ?>
                                                    <?= $checkout->descricao ?><br>

                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="<?= url("checkout/" . $checkout->codigo) ?>">
                                                        <div class="input-group-append">
                                                            <a href="<?= url("checkout/" . $checkout->codigo) ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                                        </div>
                                                    </div>
                                                    <br>
                                    <?php
                                                endif;
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </div>

                                <div class="tab-pane fade" id="product-rating" role="tabpanel" aria-labelledby="product-rating-tab">
                                    <?php if ($produto->url_midias) : ?>
                                        Para ver as mídias dess produto clique no link abaixo: <br>
                                        <a href="<?= $produto->url_midias ?>" target="_blank"><?= $produto->url_midias ?></a>
                                    <?php endif; ?>

                                    <?php if ($produto->usuario == $_SESSION["id"]) : ?>
                                        <br><i class="fas fa-question-circle"></i> <small>Para editar o link de mídias, vá a seção editar no menu superior</small>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php
    else :
?>
    <h4>Produto Não encontrado</h4>
<?php
    endif;
?>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-info-circle"></i> Denunciar Produto</h3>
                    </div>

                    <div class="card-body">

                        <form action="" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                            <input type="hidden" name="usuario" id="usuario" value="<?= $_SESSION["id"] ?>">
                            <input type="hidden" name="produto" id="produto" value="<?= $produto->id ?>">
                            Assunto:
                            <select name="assunto" id="assunto" class="form-control" required>
                                <option value="">Selecionar</option>
                                <option value="1">Produto enganoso</option>
                                <option value="2">Outro</option>
                            </select>

                            <br>
                            Denúncia:
                            <textarea name="denuncia" id="denuncia" class="form-control"></textarea>

                            <div class="modal-footer justify-content-between" style="background:#f1f1f1">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar1">Cancelar</button>
                                <button class="btn btn-primary" id="salvar1">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- end cadastrar -->

<?= $v->start("scripts") ?>
<script>
    function load(action, tempo = 1000) {
        var load_div = $(".ajax_load");
        if (action === "open") {
            load_div.fadeIn().css("display", "flex");
            setTimeout(() => {
                load_div.fadeOut();
            }, tempo);
        } else {
            load_div.fadeOut();
        }
    }

    $("#promover").click((e) => {
        var produto = "<?= @$produto->id ?>";
        var afiliado = "<?= @$_SESSION["id"] ?>";
        var liberar_comprador = "<?= @$produto->liberar_comprador ?>";
        var tempo_cookie = "<?= @$produto->tempo_cookie ?>";
        var tipo_comissao = "<?= @$produto->tipo_comissao ?>";
        var comissao_afiliado = "<?= @$produto->comissao_afiliado ?>";
        var tipo_atribuicao = "<?= @$produto->tipo_atribuicao ?>";
        var afiliados_aprovacao = "<?= @$produto->afiliados_aprovacao ?>";
        var produto_nome = "<?= @$produto->nome ?>";

        $.ajax({
            url: "<?= $router->route("appafiliados.afiliacao") ?>",
            data: {
                produto: produto,
                afiliado: afiliado,
                liberar_comprador: liberar_comprador,
                tempo_cookie: tempo_cookie,
                tipo_comissao: tipo_comissao,
                comissao_afiliado: comissao_afiliado,
                tipo_atribuicao: tipo_atribuicao,
                afiliados_aprovacao: afiliados_aprovacao,
                produto_nome: produto_nome
            },

            type: "POST",
            beforeSend: function() {
                console.log("atualizando");
                load("open", 3000);
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log(callback)
                load("close");
                alert(callback);
                location.reload();
            }
        })
    });

    $("#salvar1").click((e) => {
        e.preventDefault();

        if ($("#assunto").val() == "" || $("#denuncia").val() == "") {
            alert("Preencha todos os campos");
        } else {
            var form = $("form");
            $.ajax({
                url: "<?= $router->route("denuncias.cadastrar") ?>",
                data: form.serialize(),
                type: "POST",
                beforeSend: function() {
                    console.log("atualizando")
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback)
                    alert(callback);
                    location.reload();
                }
            })
        }
    })

    var click = 1;
    $("#observacao").click(() => {
        if (click == 1)
            $("#observacao").html("Tentativas: \nOperador: \nMotivo: \nÚltimo motivo: ");

        click++;
    })
</script>
<?= $v->end(); ?>
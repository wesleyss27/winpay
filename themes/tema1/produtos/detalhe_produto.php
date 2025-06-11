<?php

$v->layout("_theme");

$status = array(
    0 => "Rascunho",
    1 => "Em Análise",
    2 => "Aprovado",
    3 => "Reprovado",
    4 => "Bloqueado",
    5 => "Excluido",
    6 => "Inativado",
);

$bg = array(
    0 => "bg-info",
    1 => "bg-info",
    2 => "bg-success",
    3 => "bg-danger",
    4 => "bg-danger",
    5 => "bg-danger",
    6 => "bg-warning",
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

include __DIR__ . "/tipo.php";

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
    <?php if ($produto) : ?>

        <!-- MAIN CONTENT -->
        <section class="content">
            <br>
            <?= $section->getTipoSecaoLayout() ?>

            <!-- MENU PRODUTORES -->

            <!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
            <?php if (($buscaAfiliacaoApro || $produto->usuario == $section->getId()) && $produto->excluir == 0) : ?>
                <br>
                <div class="callout menu-edicao">
                    <a href="<?= url("produtos/detalhe/") . $produto->id; ?>">Detalhes</a>
                    <?php
                    if ($produto->usuario == $section->getId()) :
                        $v->insert("produtos/menu-produto", ["produto" => $produto]);
                    endif;
                    ?>
                    <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->

                    <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("campanha_produto")) : ?>
                        <a href="<?= url("produtos/campanhas/") . $produto->id; ?>">Campanhas</a>
                    <?php endif ?>
                </div>
            <?php endif; ?>

            <!-- FIM MENU PRODUTORES -->

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
            <div class="row">
                <div class="col-md-7">
                    <?php if ($produto->excluir == 1) : ?>
                        <div class="alert alert-sm bg-danger">Esse produto não se encontra mais a venda</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-5">
                    <?php if ($produto->usuario != $section->getId() && $produto->excluir == 0 && $produto->status == 2) : ?>
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

            <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

            <!-- INFORMAÇÕES DO PRODUTO -->
            <div class="card card-solid">
                <div class="row">
                    <!-- COLUNA IMAGEM E OBSERVAÇÃO -->
                    <div class="col-md-5 card-body">
                        <?php if ($produto->usuario == $section->getId() || ($_SESSION["tipo"] != 4 && $_SESSION["tipo"] != 5)) : ?>
                            <div class="row card-body">
                                <div class="col-md-6 col-12 <?= $bg[$produto->status] ?>" style="border-radius: 5px; padding: 5px; text-align: center"><?= $status[$produto->status] ?></div>
                                <?php if ($produto->status != 6 && $produto->status == 2) : ?>
                                    <div class="col-md-3 col-6"><button class="btn btn-warning btn-sm form-control" data-target="#modal-inativar" data-toggle="modal">Inativar</button></div>
                                <?php elseif ($produto->status == 6) : ?>
                                    <div class="col-md-3 col-6"><button class="btn btn-success btn-sm form-control" id="btn-ativar">Ativar</button></div>
                                <?php endif ?>

                                <?php if ($produto->excluir != 1 && (!@$_SESSION["subusuario"] || $permissao->excluir == 1)) : ?>
                                    <div class="col-md-3 col-6"><button class="btn btn-danger btn-sm form-control" data-target="#modal-excluir" data-toggle="modal">Excluir</button></div>
                                <?php endif ?>
                            </div>
                        <?php endif; ?>

                        <h3 class="d-inline-block d-sm-none"><?= $produto->nome; ?></h3>

                        <div align="center" class="img-detalhe">
                            <?php if ($produto->foto != "" && $produto->shopify == 0) : ?>
                                <img src="<?= url("uploads/produtos/" . $produto->foto); ?>" alt="<?= $produto->id; ?>"><br>
                            <?php elseif ($produto->foto != "" && $produto->shopify == 1) : ?>
                                <img src="<?= $produto->foto; ?>" alt="<?= $produto->id; ?>"><br>
                            <?php else : ?>
                                <img src="<?= url("uploads/produtos/default.jpg"); ?>" alt="<?= $produto->id; ?>"><br>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- FIM COLUNA IMAGEM E OBSERVAÇÃO -->

                    <!-- COLUNA INFORMAÇÕES -->
                    <div class="col-md-7 card-body">
                        <!-- NOME DO PRODUTO, CÓDIGO -->
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
                            <small class="badge bg-vermelho-gradient" style="font-size:14px">código: <?= $produto->codigo; ?></small>
                        </h3>
                        <!-- FIM NOME PRODUTO, CÓDIGO -->

                        <!-- NOME PRODUTOR -->
                        <p><i><b>Produtor:</b> <?= $produto->dataProdutor()->nome; ?></i></p>

                        <!-- PREÇO -->
                        <?php if (@!$planos) : ?>
                            <div class="bg-success py-2 px-3 mt-4" style="border-radius: 5px">
                                <h2 class="mb-0">
                                    <small>Preço:</small> <b>R$ <?= number_format($produto->preco, 2, ",", "."); ?></b>
                                </h2>
                            </div>
                            <br />
                        <?php endif; ?>
                        <!-- FIM PREÇO -->

                        <?= mb_strimwidth($produto->descricao, 0, 500, "..."); ?><br><br><!-- DESCRIÇÃO DO PRODUTO -->

                        <!-- INFORMAÇÕES AFILIADOS -->
                        <div class="row">

                            <!-- COMISSÃO -->
                            <div class="col-md-6">
                                <span>Sua Comissão:</span><br>

                                <h4>
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
                                    ?>
                                </h4>

                            </div>
                            <!-- FIM COMISSÃO -->

                            <!-- COOKIES -->
                            <div class="col-md-6">
                                <span>Tempo de cookie:</span><br>
                                <h4><?= $produto->tempo_cookie; ?> dias</h4>
                            </div>
                            <!-- FIM COOKIES -->

                        </div>
                        <!-- FIM INFORMAÇÕES AFILIADOS -->

                        <br>
                        <!-- INFORMAÇÕES PÁGINA DE VENDAS -->
                        <div class="row">

                            <!-- PÁGINA DE VENDAS -->
                            <div class="col-md-6">
                                <b><span>Página de vendas:</span></b><br>
                                <?php if ($urls) : ?>
                                    <?php
                                    foreach ($urls as $url) :
                                        if ($url->pv == 1) :
                                    ?>
                                            <a href="<?= $url->url ?>" target="_blank"><?= $url->url ?></a>
                                <?php
                                        endif;
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            <!-- FIM PÁGINA DE VENDAS -->

                            <!-- SUPORTE -->
                            <div class="col-md-6">
                                <b><span>Suporte:</span></b><br>
                                <a href="mailto:<?= $produto->email_suporte; ?>"><?= $produto->email_suporte; ?></a>
                            </div>
                            <!-- FIM SUPORTE -->
                        </div>
                        <!-- FIM INFORMAÇÕES PÁGINA DE VENDAS -->

                        <br>
                        <!-- INFORMAÇÕES TIPO, CATEGORIA -->
                        <div class="row">

                            <!-- TIPO DE PRODUTO -->
                            <div class="col-md-6">
                                <b><span>Tipo de produto:</span></b><br>
                                <?= $tipoProduto[$produto->tipo_produto]; ?>
                            </div>
                            <!-- FIM TIPO DE PRODUTO -->

                            <!-- CATEGORIA -->
                            <div class="col-md-6">
                                <b><span>Categoria:</span></b><br>
                                <?= $categoria[$produto->categoria]; ?>
                            </div>
                            <!-- FIM CATEGORIA -->

                        </div>
                        <!-- FIM INFORMAÇÕES TIPO, CATEGORIA -->

                        <br>
                        <!-- LINK ÁREA DE MEMBROS -->
                        <?php if ($produto->tipo_produto == 11) : ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <b><span>Link de acesso membros:</span></b><br>
                                    <a href="<?= @$produto->url_membros ?>" target="_blank"><?= @$produto->url_membros ?></a><br>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- LINK ÁREA DE MEMBROS -->

                    </div>

                </div>
                <!-- FIM COLUNA INFORMAÇÕES -->

            </div>
            <!-- FIM INFORMAÇÕES DO PRODUTO -->

            <br>
            <!-- OPÇÕES USUÁRIO MASTER  -->
            <?php if (
                ($produto->usuario == $_SESSION["id"] ||
                    $_SESSION["tipo"] == 1 ||
                    $_SESSION["tipo"] == 2 ||
                    $_SESSION["tipo"] == 3) &&
                $produto->observacao != ""

            ) : ?>
                <div class="card">
                    <div class="row card-body">
                        <!-- OBSERVAÇÃO -->
                        <div class="col-md-6">
                            <b>Observação:</b>
                            <pre style="font-family: inter"><?= $produto->observacao ?></pre>
                        </div>
                        <!-- FIM OBSERVAÇÃO -->
                    </div>
                </div>
            <?php endif; ?>
            <!-- FIM OPÇÕES USUARIO MASTER -->

            <!-- TERMOS AFILIADOS -->
            <?php if ($produto->abrir_afiliacao) : ?>
                <div class="card">
                    <div class="row card-body">

                        <!-- AFILIADOS -->
                        <div class="col-md-6">
                            <h3>Descrição Afiliação:</h3>
                            <textarea cols="30" rows="10" class="form-control bg-white" readonly><?= $produto->descricao_afiliados; ?></textarea>
                        </div>
                        <!-- FIM AFILIADOS -->

                        <!-- INFORMAÇÕES PRODUTOR -->
                        <div class="col-md-6">
                            <h3>Termos de Afiliação:</h3>
                            <textarea cols="30" rows="10" class="form-control bg-white" readonly><?= $produto->termos; ?></textarea>
                        </div>
                        <!-- FIM INFORMAÇÕES PRODUTOR -->
                    </div>
                </div>
            <?php endif; ?>
            <!-- FIM TERMOS AFILIADOS -->

            <!-- TABULAÇÃO -->
            <?php if ($produto->excluir == 0 && (@$buscaAfiliacao->status == 2 || $_SESSION["id"] == $produto->usuario || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)) : ?>
                <div class="card card-body">
                    <div class="row">
                        <nav class="w-100">
                            <div class="nav nav-tabs" id="product-tab" role="tablist" style="font-family:inter">
                                <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : $active = "active"; ?>
                                    <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">PRODUTOR</a>
                                    <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">URL's</a>
                                <?php else : ?>
                                    <a class="nav-item nav-link active" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">URL's</a>
                                <?php endif ?>

                                <a class="nav-item nav-link" id="product-rating-tab" data-toggle="tab" href="#product-rating" role="tab" aria-controls="product-rating" aria-selected="false">MATERIAL DE DIVULGAÇÃO</a>
                            </div>
                        </nav>

                        <div class="tab-content p-3" id="nav-tabContent" style="font-family:inter">
                            <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : $active = ""; ?>
                                <!-- TAB PRODUTOR -->
                                <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">

                                    <div class="row">
                                        <!-- DADOS PESSOAIS -->
                                        <div class="col-md-6">
                                            <div class="text-white">
                                                <h4>Dados Pessoais</h4>
                                                <table class="table text-white">
                                                    <tr>
                                                        <th>Nome</th>
                                                        <td><?= $produto->dataProdutor()->nome; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <th><?= $tipo_doc[$usuario->tipo_doc]; ?></th>
                                                        <td><?= $usuario->cpf_cnpj; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <th>Email</th>
                                                        <td><?= $usuario->email; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <th>Telefone</th>
                                                        <td>
                                                            <a style="color:#00ff00" href="<?= "https://api.whatsapp.com/send?phone=55$usuario->telefone"; ?>" target="_blank">
                                                                <i class='fab fa-whatsapp' style='color: green'></i>
                                                                <?= $usuario->telefone ?>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th>Endereço</th>
                                                        <td>
                                                            <?= $usuario->rua; ?>, <?= $usuario->numero; ?>, <?= $usuario->bairro; ?><br>
                                                            <?= $usuario->cidade; ?> - <?= $usuario->estado; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div>
                                                <?php if ($produto->observacao != "") : ?>
                                                    <b>Observação:</b>
                                                    <pre style="font-family: inter"><?= $produto->observacao ?></pre>
                                                <?php endif; ?>

                                                <div class="bg-light alert">
                                                    <form action="<?= $router->route("produtos.editarStatus") ?>" method="post">
                                                        <b>Adicionar observação: </b><small>(opcional)</small>
                                                        <textarea name="observacao" id="observacao" class="form-control" cols="30" rows="4"></textarea><br>

                                                        <br>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input type="hidden" name="id" value="<?= $produto->id; ?>">
                                                                <label>Status </label>

                                                                <select name="status" id="status" class="form-control">
                                                                    <option value="">Status</option>
                                                                    <?php if ($produto->excluir != 1) : ?>
                                                                        <option value="2" <?= $produto->status == 2 ? "selected" : "" ?>>APROVADO</option>
                                                                        <option value="3" <?= $produto->status == 3 ? "selected" : "" ?>>REPROVADO</option>
                                                                        <option value="4" <?= $produto->status == 4 ? "selected" : "" ?>>BLOQUEADO</option>
                                                                    <?php endif ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label>Integrar com Logística St4rt? </label>

                                                                <select name="st4rt" id="st4rt" class="form-control">
                                                                    <?php if ($produto->excluir != 1) : ?>
                                                                        <option value="0" <?= $produto->st4rt == 0 ? "selected" : "" ?>>Não</option>
                                                                        <option value="1" <?= $produto->st4rt == 1 ? "selected" : "" ?>>Sim</option>
                                                                    <?php endif ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <br>

                                                        <?php if ($produto->excluir != 1) : ?>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <button class="btn bg-vermelho-gradient form-control" id="atualizar">Atualizar</button>
                                                                </div>
                                                            </div>
                                                        <?php endif ?>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- FIM DADOS PESSOAIS -->

                                        <!-- DADOS PRODUTO -->
                                        <div class="col-md-6">
                                            <h4>Informações do produto</h4>
                                            <table class="table">
                                                <tr>
                                                    <th>Cliente St4rt</th>
                                                    <td><?= $simOuNao[$produto->dataProdutor()->cliente_st4rt]; ?></td>
                                                </tr>

                                                <tr>
                                                    <th>Tipo de produto</th>
                                                    <td><?= $tipoProduto[$produto->tipo_produto]; ?></td>
                                                </tr>

                                                <?php if ($produto->tipo_produto == 8) : ?>
                                                    <tr>
                                                        <th>Link de rastreio</th>
                                                        <td><a href="<?= @$produto->link_rastreio ?>" target="_blank"><?= @$produto->link_rastreio ?></a></td>
                                                    </tr>
                                                <?php endif; ?>

                                                <?php if ($produto->tipo_produto == 11) : ?>
                                                    <tr>
                                                        <th>Link de acesso membros</th>
                                                        <td><a href="<?= @$produto->url_membros ?>" target="_blank"><?= @$produto->url_membros ?></a></td>
                                                    </tr>
                                                <?php endif; ?>

                                                <tr>
                                                    <th>Documento técnico</th>
                                                    <td><a href="<?= url("uploads/documentos/") . $produto->documentos ?>" target="_blank"><?= $produto->documentos ?></a></td>
                                                </tr>

                                                <tr>
                                                    <th>Visualização no marketplace</th>
                                                    <td><?= @$simOuNao[$produto->visualizacao] ?></td>
                                                </tr>

                                                <tr>
                                                    <th>Página de obrigado boleto</th>
                                                    <td><a href="<?= @$produto->obrigado_boleto ?>" target="_blank"><?= @$produto->obrigado_boleto ?></a></td>
                                                </tr>

                                                <tr>
                                                    <th>Página de obrigado cartão</th>
                                                    <td><a href="<?= @$produto->obrigado_cartao ?>" target="_blank"><?= @$produto->obrigado_cartao ?></a></td>
                                                </tr>

                                                <tr>
                                                    <th>Página de obrigado análise</th>
                                                    <td><a href="<?= @$produto->obrigado_analise ?>" target="_blank"><?= @$produto->obrigado_analise ?></a></td>
                                                </tr>

                                                <tr>
                                                    <th>Documento técnico</th>
                                                    <td><a href="<?= url("uploads/documentos/") . $produto->documentos ?>" target="_blank"><?= $produto->documentos ?></a></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <!-- FIM DADOS PRODUTOS -->
                                    </div>

                                </div>
                            <?php else : $active = "active"; ?>
                            <?php endif ?>
                            <!-- FIM TAB PRODUTOR -->

                            <!-- TAB URL's -->
                            <div class="tab-pane fade show <?= $active; ?>" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">

                                <div class="row d-link">
                                    <div class="col-md-6">
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

                                    </div>


                                    <div class="col-md-6">
                                        <h4>URL's de Checkout</h4>
                                        <?php
                                        if ($BuscaCheckout) :
                                            foreach ($BuscaCheckout as $checkout) :
                                                if ($checkout->privado == 0 && $checkout->adquirente == "redrocket" && $checkout->adquirente_pix == "redrocket" && $checkout->adquirente_boleto == "redrocket") :
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
                                </div>

                            </div>
                            <!-- FIM TAB URL's -->

                            <!-- TAB MÍDIAS -->
                            <div class="tab-pane fade" id="product-rating" role="tabpanel" aria-labelledby="product-rating-tab">
                                <?php if ($produto->url_midias) : ?>
                                    Para ver as mídias dess produto clique no link abaixo: <br>
                                    <a href="<?= $produto->url_midias ?>" target="_blank"><?= $produto->url_midias ?></a>
                                <?php endif; ?>

                                <?php if ($produto->usuario == $_SESSION["id"]) : ?>
                                    <br><i class="fas fa-question-circle"></i> <small>Para editar o link de mídias, vá a seção editar no menu superior</small>
                                <?php endif; ?>
                            </div>
                            <!-- FIM TAB MÍDIAS -->

                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- FIM TABULAÇÃO -->
        </section>
        <!-- FIM MAIN CONTENT -->
    <?php else : ?>
        <h4>Produto Não encontrado</h4>
    <?php endif; ?>
</div>
<!-- FIM Content Wrapper -->

<!-- DENUNCIA PRODUTO -->
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
<!-- FIM DENUNCIA PRODUTO -->

<!-- EXCLUIR PRODUTO -->
<div class="modal fade" id="modal-excluir">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-info-circle"></i>Tem certeza que deseja excluir esse produto? </h4>
            </div>

            <div class="modal-body">
                Todos afiliados serão notificados e esta operação não poderá ser revertida.
            </div>

            <div class="modal-footer justify-content-between" style="background:#f1f1f1">
                <button type="button" class="btn btn-success" data-dismiss="modal" id="cancelar1">Cancelar</button>
                <button class="btn btn-danger" id="btn-excluir">Excluir</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM EXCLUIR PRODUTO -->

<!-- EXCLUIR PRODUTO -->
<div class="modal fade" id="modal-inativar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-info-circle"></i>Tem certeza que deseja inativar esse produto? </h4>
            </div>

            <div class="modal-body">
                Todos afiliados serão notificados e o checkout ficará indisponível para visualização.
            </div>

            <div class="modal-footer justify-content-between" style="background:#f1f1f1">
                <button type="button" class="btn btn-success" data-dismiss="modal" id="cancelar1">Cancelar</button>
                <button class="btn btn-warning" id="btn-inativar">Inativar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM EXCLUIR PRODUTO -->

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
        var produtor = "<?= @$produto->usuario ?>";
        var afiliado = "<?= @$_SESSION["id"] ?>";
        var liberar_comprador = "<?= @$produto->libera_comprador ?>";
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
                produtor: produtor,
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

    $("#btn-ativar").click((e) => {
        e.preventDefault();
        load("open");
        var dados = {
            "produto": "<?= $produto->id ?>"
        };
        enviaDados("<?= $router->route("produtos.ativar") ?>", dados, 1);
    });

    $("#btn-inativar").click((e) => {
        e.preventDefault();
        load("open");
        var dados = {
            "produto": "<?= $produto->id ?>"
        };
        enviaDados("<?= $router->route("produtos.inativar") ?>", dados, 1);
    });

    $("#btn-excluir").click((e) => {
        e.preventDefault();
        load("open", 5000);
        var dados = {
            "produto": "<?= $produto->id ?>"
        };
        enviaDados("<?= $router->route("produtos.excluir") ?>", dados);
    });

    function enviaDados(url, dados, redirecionar = 0) {
        $.ajax({
            url: url, //"",
            data: dados,
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
                if (redirecionar == 0)
                    location.href = "<?= url("produtos") ?>";
                else
                    location.reload();
            }
        })
    }
</script>
<?= $v->end(); ?>
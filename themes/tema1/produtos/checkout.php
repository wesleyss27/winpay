<?php
$v->layout("_theme");
if ($produto->usuario != $section->getId()) {
    header("Location: " . url("produtos/detalhe/") . $produto->id);
}

$ativo = array(
    1 => "SIM",
    0 => "NÃO",
    "" => "NÃO"
);

$modelo_frete = array(
    0 => "Sem Frete",
    1 => "Frete Fixo",
    2 => "Frete: Correios",
    3 => "Frete: Jadlog",
    4 => "Frete: Por Região"
);

$order_bump = array(
    1 => "SIM",
    0 => "NÃO"
);

$modelo_checkout = array(
    1 => "Modelo Padrão",
    2 => "Modelo Padrão",
    3 => "Modelo Padrão",
    4 => "Modelo 2",
);
?>

<style>
    .checkout-table-scroll {
        overflow-x: auto;
        padding: 12px 15px 5px;
        background: rgba(5, 5, 5, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        margin: 0 12px 25px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
    }

    .checkout-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
        font-family: "Inter", sans-serif;
        color: #f5f5f5;
    }

    .checkout-table thead th {
        border: none;
        background: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.18rem;
        padding: 14px 16px;
    }

    .checkout-table tbody tr {
        background: rgba(7, 7, 8, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
    }

    .checkout-table tbody td {
        border: none;
        padding: 18px 20px;
        background: transparent;
        vertical-align: middle;
        font-size: 0.92rem;
    }

    .checkout-table tbody td:first-child {
        border-top-left-radius: 18px;
        border-bottom-left-radius: 18px;
    }

    .checkout-table tbody td:last-child {
        border-top-right-radius: 18px;
        border-bottom-right-radius: 18px;
    }

    .checkout-table__code-id {
        text-transform: uppercase;
        letter-spacing: 0.3rem;
        font-size: 0.68rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .checkout-table__code-tag {
        display: inline-block;
        margin: 6px 0;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(255, 221, 0, 0.15);
        color: #ffdd00;
        font-weight: 600;
        letter-spacing: 0.12rem;
        font-size: 0.8rem;
    }

    .checkout-table__link {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.78rem;
        word-break: break-all;
    }

    .checkout-table__link:hover {
        color: #ffdd00;
        text-decoration: none;
    }

    .checkout-table__name {
        font-weight: 600;
        font-size: 1rem;
    }

    .checkout-table__product small {
        display: block;
        margin-top: 6px;
        color: rgba(255, 255, 255, 0.55);
        font-size: 0.78rem;
        letter-spacing: 0.08rem;
        text-transform: uppercase;
    }

    .price-chip {
        display: inline-flex;
        align-items: baseline;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 221, 0, 0.18);
        color: #ffdd00;
        font-size: 0.95rem;
        font-weight: 600;
        letter-spacing: 0.05rem;
    }

    .checkout-table__price small {
        display: block;
        margin-top: 6px;
        color: rgba(255, 255, 255, 0.55);
        font-size: 0.75rem;
    }

    .privacy-pill {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.15rem;
        font-size: 0.7rem;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .checkout-action-stack {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .checkout-table__actions {
        text-align: center;
    }

    .checkout-table__actions .btn-link {
        color: #ffdd00;
    }

    .checkout-action-stack .btn {
        min-width: 110px;
        border-radius: 999px;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.12rem;
    }

    @media (max-width: 768px) {
        .checkout-table thead {
            display: none;
        }

        .checkout-table tbody,
        .checkout-table tr,
        .checkout-table td {
            display: block;
            width: 100%;
        }

        .checkout-table tbody tr {
            margin-bottom: 18px;
        }

        .checkout-table tbody td {
            padding: 10px 14px;
        }

        .checkout-table__price,
        .checkout-table__privacy {
            text-align: left;
        }
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
                    <h4 style="font-family:inter"><?= $title ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Checkout</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">

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

        <section class="callout">
            * É necessário cadastrar um checkout para vender o produto e que este produto esteja aprovado.
        </section>

        <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

        <form action="#">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Nome ou código</label>
                        <input type="text" name="nome" id="nome" class="form-control">
                    </div>

                    <div class="col-md-1">
                        <label style="color: #fff">|</label>
                        <button class="btn btn-primary form-control">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Default box -->
        <div class="card">
            <!-- CARD-HEADER -->
            <div class="card-header">
                <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                    <button disabled class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <span class="badge bg-danger">Produto REPROVADO/BLOQUEADO</span>
                <?php else : ?>
                    <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <?php endif ?>
                <?php endif; ?>

                &nbsp; Total de registros: <?= $count1 ?>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- FIM CARD-HEADER -->

            <?php if ($checkout) : ?>
                <div class="card-body p-0">
                    <script>
                        var dados = [];
                    </script>

                    <div class="checkout-table-scroll">
                        <table class="table checkout-table">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Produto / Plano</th>
                                    <th scope="col">Valor</th>
                                    <th scope="col">Privado</th>
                                    <!-- <th scope="col">Order Bump</th> -->
                                    <th scope="col">Duplicar</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($checkout as $checkout) :
                                    $valorProduto = $checkout->plano ? $checkout->dataPlano()->preco : $checkout->dataProduto()->preco;
                                    $origemValor = $checkout->plano ? "Plano associado" : "Produto base";
                                ?>
                                    <tr>
                                        <td class="checkout-table__code">
                                            <div class="checkout-table__code-id">#<?= $checkout->id ?></div>
                                            <span class="checkout-table__code-tag"><?= $checkout->codigo ?></span><br>
                                            <a href="<?= url("checkout/" . $checkout->codigo) ?>" target="_blank" class="checkout-table__link"><?= url("checkout/" . $checkout->codigo) ?></a>
                                        </td>
                                        <td class="checkout-table__name"><?= $checkout->descricao ?></td>
                                        <td class="checkout-table__product">
                                            <?= $checkout->dataProduto()->nome; ?>
                                            <?php if ($checkout->plano) : ?>
                                                <small>Plano: <?= $checkout->dataPlano()->nome; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="checkout-table__price">
                                            <span class="price-chip">R$ <?= number_format($valorProduto ?? 0, 2, ",", ".") ?></span>
                                            <small><?= $origemValor; ?></small>
                                        </td>
                                        <td class="checkout-table__privacy">
                                            <span class="privacy-pill"><?= $ativo[@$checkout->privado] ?></span>
                                        </td>
                                        <!-- <td>< ?= $order_bump[@$checkout->usar_order_bump] ?></td> -->
                                        <form action="#" method="post" name="linha<?= $checkout->id; ?>" action="<?= $router->route("produtos.checkoutCadastro") ?>">
                                            <input type="hidden" name="descricao" value="<?= $checkout->descricao ?> - copy">
                                            <input type="hidden" name="produto" value="<?= $produto->id ?>">
                                            <input type="hidden" name="plano" value="<?= $checkout->plano ?>">
                                            <input type="hidden" name="privado" value="<?= $checkout->privado ?>">
                                            <input type="hidden" name="modelo_checkout" value="<?= $checkout->modelo_checkout ?>">
                                            <input type="hidden" name="mostrar_preco" value="<?= $checkout->mostrar_preco ?>">
                                            <input type="hidden" name="endereco_obrigatorio" value="<?= $checkout->endereco_obrigatorio ?>">
                                            <input type="hidden" name="adquirente" value="<?= $checkout->adquirente ?>">
                                            <input type="hidden" name="adquirente_pix" value="<?= $checkout->adquirente_pix ?>">
                                            <input type="hidden" name="adquirente_boleto" value="<?= $checkout->adquirente_boleto ?>">
                                            <input type="hidden" name="televendas" value="<?= $checkout->televendas ?>">
                                            <input type="hidden" name="banner_atual" value="<?= $checkout->banner ?>">
                                            <input type="hidden" name="banner_lateral" value="<?= $checkout->banner_lateral ?>">
                                            <input type="hidden" name="aviso_atual" value="<?= $checkout->aviso ?>">
                                            <input type="hidden" name="video_prova_social" value="<?= htmlspecialchars($checkout->video_prova_social ?? '') ?>">
                                            <input type="hidden" name="video_posicao" value="<?= $checkout->video_posicao ?? 'botao' ?>">
                                            <input type="hidden" name="usar_contador" value="<?= $checkout->usar_contador ?>">
                                            <input type="hidden" name="texto_contador" value="<?= $checkout->texto_contador ?>">
                                            <input type="hidden" name="acao_contador" value="<?= $checkout->acao_contador ?>">
                                            <input type="hidden" name="aumento" value="<?= $checkout->aumento ?? 10 ?>">
                                            <input type="hidden" name="cor_contador" value="<?= $checkout->cor_contador ?>">
                                            <input type="hidden" name="cor_texto" value="<?= $checkout->cor_texto ?>">
                                            <input type="hidden" name="vencimento_boletos" value="<?= $checkout->vencimento_boletos ?>">
                                            <input type="hidden" name="usar_frete" value="<?= $checkout->usar_frete ?>">
                                            <input type="hidden" name="valor_frete" value="<?= $checkout->valor_frete ?>">
                                            <input type="hidden" name="prazo_entrega" value='<?= $checkout->prazo_entrega ?>'>
                                            <input type="hidden" name="frete_gratis" value="<?= $checkout->frete_gratis ?>">
                                            <input type="hidden" name="prazo_entrega_gratis" value="<?= $checkout->prazo_entrega_gratis ?>">
                                            <input type="hidden" name="pixel_facebook" value="<?= $checkout->pixel_facebook ?>">
                                            <input type="hidden" name="pixel_facebook_api" value="<?= $checkout->pixel_facebook_api ?>">
                                            <input type="hidden" name="pixel_google_analitics" value="<?= $checkout->pixel_google_analitics ?>">
                                            <input type="hidden" name="pixel_google_ads" value="<?= $checkout->pixel_google_ads ?>">
                                            <input type="hidden" name="pixel_google_tag_manager" value="<?= $checkout->pixel_google_tag_manager ?>">
                                            <input type="hidden" name="pixel_taboola" value="<?= $checkout->pixel_taboola ?>">
                                            <input type="hidden" name="pixel_tiktok" value="<?= $checkout->pixel_tiktok ?>">
                                            <input type="hidden" name="pixel_kwai" value="<?= $checkout->pixel_kwai ?>">
                                            <input type="hidden" name="usar_order_bump" value="<?= $checkout->usar_order_bump ?>">
                                            <input type="hidden" name="titulo_informativo" value="<?= $checkout->titulo_informativo ?>">
                                            <input type="hidden" name="planos_order_bump" value="<?= $checkout->planos_order_bump ?>">
                                            <input type="hidden" name="id_frete" value="<?= $checkout->id_frete ?>">
                                            <?php if ($checkout->id_frete) : ?>
                                                <input type="hidden" name="usar_frete" value="<?= $checkout->dataFrete()->tipo_frete ?>">
                                                <input type="hidden" name="valor_frete" value="<?= $checkout->dataFrete()->frete_padrao ?>">
                                                <input type="hidden" name="prazo_entrega" value="<?= $checkout->dataFrete()->prazo_entrega ?>">
                                                <input type="hidden" name="frete_gratis" value="<?= $checkout->dataFrete()->frete_gratis ?>">
                                                <input type="hidden" name="prazo_entrega_gratis" value="<?= $checkout->dataFrete()->prazo_entrega_gratis ?>">
                                                <!-- CORREIOS -->
                                                <input type="hidden" name="tipo_entrega_correios" value="<?= $checkout->dataFrete()->correios_entrega ?>">
                                                <input type="hidden" name="formato_sedex" value="<?= $checkout->dataFrete()->correios_formato ?>">
                                                <input type="hidden" name="cep_sedex" value="<?= $checkout->dataFrete()->cep_origem ?>">
                                                <input type="hidden" name="valor_padrao_sedex" value="<?= $checkout->dataFrete()->frete_padrao ?>">
                                                <input type="hidden" name="prazo_entrega_sedex" value="<?= $checkout->dataFrete()->prazo_entrega ?>">
                                                <input type="hidden" name="largura_sedex" value="<?= $checkout->dataFrete()->largura_mercadoria ?>">
                                                <input type="hidden" name="comprimento_sedex" value="<?= $checkout->dataFrete()->comprimento_mercadoria ?>">
                                                <input type="hidden" name="peso_sedex" value="<?= $checkout->dataFrete()->peso_mercadoria ?>">
                                                <input type="hidden" name="altura_sedex" value="<?= $checkout->dataFrete()->altura_mercadoria ?>">
                                                <!-- CORREIOS -->
                                                <!-- JADLOG -->
                                                <input type="hidden" name="cep_jadlog" value="<?= $checkout->dataFrete()->cep_origem ?>">
                                                <input type="hidden" name="peso_jadlog" value="<?= $checkout->dataFrete()->peso_mercadoria ?>">
                                                <input type="hidden" name="altura_jadlog" value="<?= $checkout->dataFrete()->altura_mercadoria ?>">
                                                <input type="hidden" name="largura_jadlog" value="<?= $checkout->dataFrete()->altura_mercadoria ?>">
                                                <input type="hidden" name="comprimento_jadlog" value="<?= $checkout->dataFrete()->altura_mercadoria ?>">
                                                <input type="hidden" name="valor_padrao_jadlog" value="<?= $checkout->dataFrete()->frete_padrao ?>">
                                                <input type="hidden" name="prazo_entrega_jadlog" value="<?= $checkout->dataFrete()->prazo_entrega ?>">
                                                <!-- FIM JADLOG -->
                                                <!--  FRETE REGIAO-->
                                                <input type="hidden" name="regiao_norte" value="<?= $checkout->dataFrete()->regiao_norte ?>">
                                                <input type="hidden" name="prazo_entrega_regiao_norte" value="<?= $checkout->dataFrete()->prazo_reg_norte ?>">
                                                <input type="hidden" name="regiao_nordeste" value="<?= $checkout->dataFrete()->regiao_nordeste ?>">
                                                <input type="hidden" name="prazo_entrega_regiao_nordeste" value="<?= $checkout->dataFrete()->prazo_reg_nordeste ?>">
                                                <input type="hidden" name="regiao_centro_oeste" value="<?= $checkout->dataFrete()->regiao_centro ?>">
                                                <input type="hidden" name="prazo_entrega_regiao_centro" value="<?= $checkout->dataFrete()->prazo_reg_centro ?>">
                                                <input type="hidden" name="regiao_sudeste" value="<?= $checkout->dataFrete()->regiao_sudeste ?>">
                                                <input type="hidden" name="prazo_entrega_regiao_sudeste" value="<?= $checkout->dataFrete()->prazo_reg_sudeste ?>">
                                                <input type="hidden" name="regiao_sul" value="<?= $checkout->dataFrete()->regiao_sul ?>">
                                                <input type="hidden" name="prazo_entrega_regiao_sul" value="<?= $checkout->dataFrete()->prazo_reg_sul ?>">
                                                <!-- FIM FRETE REGIAO -->
                                            <?php endif; ?>
                                            <?php $pagamentos = explode(",", $checkout->meio_pagamento);
                                            $j = 0;
                                            foreach ($pagamentos as $pagamento) : ?>
                                                <input type="hidden" name="meio_pagamento[<?= $j ?>]" value="<?= $pagamento ?>">
                                            <?php $j++;
                                            endforeach; ?>
                                            <!-- 
                                        <?php $pagamentos = explode(",", $checkout->meio_pagamento);
                                        $j = 0;
                                        foreach ($pagamentos as $pagamento) : ?>
                                            <?php $planosOrder = explode(",", $checkout->planos_order_bump);
                                            $j = 0;
                                            foreach ($planosOrder as $orderBump) : ?>
                                                <input type="hidden" name="meio_pagamento[<?= $j ?>]" value="<?= $pagamento ?>">
                                                <input type="hidden" name="planos_order_bump[<?= $j ?>]" value="<?= $orderBump ?>">
                                        <?php $j++;
                                            endforeach;
                                        endforeach; ?> -->
                                            <td class="checkout-table__actions">
                                                <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                                                    <div class="checkout-action-stack">
                                                        <button class="btn btn-link" onclick="duplicar(<?= $checkout->id; ?>)"><i class="far fa-copy"></i></button>
                                                    </div>
                                                <?php endif ?>
                                            </td>

                                        </form>

                                        <td class="checkout-table__actions">
                                            <div class="checkout-action-stack">
                                                <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                    <button class="btn btn-warning btn-editar" data-toggle="modal" data-target="#modal-lg" onclick="editar(<?= $i; ?>)"><i class="fa fa-edit"></i></button>
                                                <?php endif ?>

                                                <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                    <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $checkout->id; ?>)"><i class="fa fa-times"></i></button>
                                                <?php endif ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Modelo: <?= @$modelo_checkout[$checkout->modelo_checkout] ?></td>
                                        <td>Usar Contador: <?= @$ativo[$checkout->usar_contador] ?></td>
                                        <td>Vencimento Boleto: <?= $checkout->vencimento_boletos ?> dias</td>
                                        <td>
                                            <?php if ($checkout->id_frete == "") : ?>
                                                Usar frete fixo: <?= @$ativo[$checkout->usar_frete] ?><br>
                                                Usar frete grátis: <?= @$ativo[$checkout->frete_gratis] ?><br>

                                            <?php elseif ($checkout->id_frete != "") : ?>
                                                <?php if ($checkout->dataFrete()->tipo_frete == 1) : ?>
                                                    Usar frete fixo: <?= @$checkout->dataFrete()->frete_padrao > 0 ? "SIM" : "NÃO" ?><br>
                                                    Usar frete grátis: <?= @$checkout->dataFrete()->frete_gratis == 1 ? "SIM" : "NÃO" ?><br>
                                                <?php else : ?>
                                                    <?= @$modelo_frete[$checkout->dataFrete()->tipo_frete] ?><br>
                                            <?php endif;
                                            endif; ?>
                                        </td>

                                        <td>Order Bump: <?= @$ativo[$checkout->usar_order_bump] ?></td>

                                    </tr>

                                    <script>
                                        dados[<?= $i ?>] = {
                                            id: "<?= $checkout->id ?>",
                                            descricao: "<?= $checkout->descricao ?>",
                                            plano: "<?= $checkout->plano ?>",
                                            pixel_facebook: "<?= $checkout->pixel_facebook ?>",
                                            pixel_facebook_api: "<?= $checkout->pixel_facebook_api ?>",
                                            pixel_google_analitics: "<?= $checkout->pixel_google_analitics ?>",
                                            pixel_google_ads: "<?= $checkout->pixel_google_ads ?>",
                                            pixel_google_tag_manager: "<?= $checkout->pixel_google_tag_manager ?>",
                                            pixel_taboola: "<?= $checkout->pixel_taboola ?>",
                                            pixel_tiktok: "<?= $checkout->pixel_tiktok ?>",
                                            pixel_kwai: "<?= $checkout->pixel_kwai ?>",
                                            privado: "<?= $checkout->privado ?>",
                                            modelo_checkout: "<?= $checkout->modelo_checkout ?>",
                                            adquirente: "<?= $checkout->adquirente ?>",
                                            adquirente_boleto: "<?= $checkout->adquirente_boleto ?>",
                                            adquirente_pix: "<?= $checkout->adquirente_pix ?>",
                                            mostrar_preco: "<?= $checkout->mostrar_preco ?>",
                                            endereco_obrigatorio: "<?= $checkout->endereco_obrigatorio ?>",
                                            televendas: "<?= $checkout->televendas ?>",
                                            banner: "<?= $checkout->banner ?>",
                                            banner_lateral: "<?= $checkout->banner_lateral ?>",
                                            aviso: "<?= $checkout->aviso ?>",
                                            video_prova_social: "<?= base64_encode($checkout->video_prova_social ?? '') ?>",
                                            video_posicao: "<?= $checkout->video_posicao ?? 'botao' ?>",
                                            cupom: "<?= $checkout->cupom ?>",
                                            valor_cupom: "<?= $checkout->valor_cupom ?>",
                                            so_cartao: "<?= $checkout->so_cartao ?>",
                                            usar_contador: "<?= $checkout->usar_contador ?>",
                                            texto_contador: "<?= $checkout->texto_contador ?>",
                                            acao_contador: "<?= $checkout->acao_contador ?>",
                                            aumento: "<?= $checkout->aumento ?? 10 ?>",
                                            cor_contador: "<?= $checkout->cor_contador ?>",
                                            cor_texto: "<?= $checkout->cor_texto ?>",
                                            vencimento_boletos: "<?= $checkout->vencimento_boletos ?>",
                                            usar_order_bump: "<?= $checkout->usar_order_bump ?>",
                                            titulo_informativo: "<?= $checkout->titulo_informativo ?>",
                                            planos_order_bump: "<?= $checkout->planos_order_bump ?>",
                                            meio_pagamento: "<?= $checkout->meio_pagamento ?>",
                                            id_frete: "<?= $checkout->id_frete ?>",
                                            usar_frete: "<?= $checkout->usar_frete ?>",
                                            valor_frete: "<?= $checkout->valor_frete ?>",
                                            prazo_entrega: "<?= $checkout->prazo_entrega ?>",
                                            frete_gratis: "<?= $checkout->frete_gratis ?>",
                                            prazo_entrega_gratis: "<?= $checkout->prazo_entrega_gratis ?>",

                                            cep: "<?= @$checkout->dataFrete()->cep_origem ?>",
                                            altura: "<?= @$checkout->dataFrete()->altura_mercadoria ?>",
                                            largura: "<?= @$checkout->dataFrete()->largura_mercadoria ?>",
                                            comprimento: "<?= @$checkout->dataFrete()->comprimento_mercadoria ?>",
                                            peso: "<?= @$checkout->dataFrete()->peso_mercadoria ?>",
                                            tipo_entrega: "<?= @$checkout->dataFrete()->correios_entrega ?>",
                                            tipo_frete: "<?= @$checkout->dataFrete()->tipo_frete ?>",
                                            tipo_formato: "<?= @$checkout->dataFrete()->correios_formato; ?>",
                                            frete: "<?= @$checkout->dataFrete()->frete_padrao ?>",
                                            frete_gratis_novo: "<?= @$checkout->dataFrete()->frete_gratis ?>",
                                            prazo_entrega_novo: "<?= @$checkout->dataFrete()->prazo_entrega ?>",
                                            prazo_entrega_gratis_novo: "<?= @$checkout->dataFrete()->prazo_entrega_gratis ?>",
                                            frete_reg_norte: "<?= @$checkout->dataFrete()->regiao_norte ?>",
                                            prazo_reg_norte: "<?= @$checkout->dataFrete()->prazo_reg_norte ?>",

                                            frete_reg_nordeste: "<?= @$checkout->dataFrete()->regiao_nordeste ?>",
                                            prazo_reg_nordeste: "<?= @$checkout->dataFrete()->prazo_reg_nordeste ?>",

                                            frete_reg_centro: "<?= @$checkout->dataFrete()->regiao_centro ?>",
                                            prazo_reg_centro: "<?= @$checkout->dataFrete()->prazo_reg_centro ?>",

                                            frete_reg_sudeste: "<?= @$checkout->dataFrete()->regiao_sudeste ?>",
                                            prazo_reg_sudeste: "<?= @$checkout->dataFrete()->prazo_reg_sudeste ?>",

                                            frete_reg_sul: "<?= @$checkout->dataFrete()->regiao_sul ?>",
                                            prazo_reg_sul: "<?= @$checkout->dataFrete()->prazo_reg_sul ?>",

                                        }
                                    </script>

                                <?php $i++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else : ?>
                <div class="card-body">
                    <p><b>Você ainda não tem nenhum checkout para esse produto</b></p>
                </div>
            <?php endif; ?>
        </div>
        <!-- FIM Default box -->
    </section>
    <!-- FIM Main content -->
</div>
<!-- FIM Content Wrapper. Contains page content -->

<!-- MODAL CADASTRO -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background:rgb(2, 2, 2)">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Checkout</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form action="<?= $router->route("produtos.checkoutCadastro") ?>" method="POST" enctype="multipart/form-data">
                <!-- MODAL BODY-->
                <div class="modal-body" style="background: #f4f6f9">
                    <div class="form-group">
                        <input type="hidden" name="id" id="id">

                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nome: </label>
                                    <i class="fas fa-question-circle" title="Essa descrição serve para informar o afiliado sobre o que é o link"></i>
                                    <input type="text" class="form-control" id="descricao" name="descricao" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Produto: </label>
                                    <select name="produto" id="produto" class="form-control">
                                        <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                                    </select>
                                </div>
                            </div>

                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Plano: </label> <i class="fas fa-question-circle" title="Se não selecionado, será aplicado as configurações de preço do produto"></i>
                                    <select name="plano" id="plano" class="form-control">
                                        <option value="">Plano...</option>
                                        <?php if ($plano) : foreach ($plano as $plano) : $plano->privado == 1 ? $privado = "**" : $privado = ""; ?>

                                                <option value="<?= $plano->id ?>"><?= "$plano->codigo $plano->nome $privado" ?></option>
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                    <small>** plano privado </small>
                                </div>

                                <div class="col-md-6">
                                    <label>Checkout privado? </label>
                                    <i class="fas fa-question-circle" title="Se sim, somente você pode ver esse link e enviar para os clientes"></i>

                                    <select name="privado" id="privado" class="form-control">
                                        <option value="0">Não</option>
                                        <option value="1">Sim</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Modelo do checkout:</label>
                                    <select name="modelo_checkout" id="modelo_checkout" class="form-control">
                                        <option value="3" selected>Modelo Padrão</option>
                                        <option value="4">Modelo 2</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <img src="<?= url("themes/tema1/images/modelo3.png") ?>" alt="" id="imagem_checkout" width="100%" style="border-radius: 10%;">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Banner Topo: 870 X 272 pixels</label>
                                    <i class="fas fa-question-circle" title="Se não for escolhido o banner uma imagem padrão ficará no lugar"></i>

                                    <input type="file" class="form-control" id="banner" name="banner">
                                    <input type="hidden" class="form-control" id="banner_atual" name="banner_atual">
                                    <input type="hidden" class="form-control" id="ban_atual" name="ban_atual">
                                    <small>Caso a imagem possua .(ponto) no nome, favor remover os pontos</small><br>
                                    <small>imagem de banner principal que ficará no topo da tela</small>

                                    <button id="excluir-banner" class="close text-danger" title="Remover imagem atual">&times;</button>
                                    <img src="" alt="" id="imagem_banner_atual" width="100%">
                                </div>

                                <div class="col-md-6">
                                    <label>Banner Topo 2: 870 X 107 pixels</label>
                                    <input type="file" class="form-control" id="aviso" name="aviso">
                                    <input type="hidden" class="form-control" id="aviso_atual" name="aviso_atual">
                                    <input type="hidden" class="form-control" id="avi_atual" name="avi_atual">
                                    <small>Caso a imagem possua .(ponto) no nome, favor remover os pontos</small><br>
                                    <small>imagem que ficará abaixo do banner principal</small>

                                    <button id="excluir-aviso" class="close text-danger" title="Remover imagem atual">&times;</button>
                                    <img src="" alt="" id="imagem_aviso_atual" width="100%">
                                </div>
                            </div>

                            <div id="lateral">
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Banner Lateral: 380 X 600 pixels</label>
                                        <i class="fas fa-question-circle" title="Se não for escolhido o banner uma imagem padrão ficará no lugar"></i>
                                        <input type="file" class="form-control" id="baner_lateral" name="baner_lateral">
                                        <input type="hidden" class="form-control" id="banner_lateral" name="banner_lateral">
                                        <input type="hidden" class="form-control" id="ban_lateral" name="ban_lateral">
                                        <small>Caso a imagem possua .(ponto) no nome, favor remover os pontos</small><br>
                                        <small>imagem de banner lateral que ficará no lado direito da tela</small>

                                        <button id="excluir-banner-lateral" class="close text-danger" title="Remover imagem atual">&times;</button>
                                        <img src="" alt="" id="imagem_banner_lateral" width="100%">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <label><i class="fas fa-play-circle"></i> Vídeo de Prova Social (VTurb)</label>
                                    <small class="d-block mb-1">Cole aqui o código embed gerado pelo VTurb</small>
                                    <textarea class="form-control" id="video_prova_social" name="video_prova_social" rows="5" placeholder="<vturb-smartplayer ...></vturb-smartplayer> <script ...></script>"></textarea>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Posição do vídeo:</label>
                                    <select class="form-control" id="video_posicao" name="video_posicao">
                                        <option value="botao">Abaixo do botão de compra</option>
                                        <option value="banner">Como banner no topo</option>
                                        <option value="ambos">Ambas as posições</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="card card-body">
                            <!-- <div class="row">
                                <div class="col-md-6">
                                    <label>Ativar checkout para televendas: <span class="badge bg-azul-dark">Em breve</span></label>
                                    <select name="televendas" id="televendas" class="form-control">
                                        <option value="0">Não</option>
                                        <option value="1">Sim</option>
                                    </select>
                                </div>
                            </div>
                            <br> -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Escolha o pagamento aceito:</label>
                                    <div class="row" style="text-align:center; margin-top:7px">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="10" name="meio_pagamento[10]" id="meio_pagamento10" checked>
                                                <label class="form-check-label" for="meio_pagamento1">
                                                    Todos
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" name="meio_pagamento[1]" id="meio_pagamento1">
                                                <label class="form-check-label" for="meio_pagamento2">
                                                    Cartão
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="2" name="meio_pagamento[2]" id="meio_pagamento2">
                                                <label class="form-check-label" for="meio_pagamento3">
                                                    Boleto
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="3" name="meio_pagamento[3]" id="meio_pagamento3">
                                                <label class="form-check-label" for="meio_pagamento4">
                                                    PIX
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label>Ativar preço do produto no checkout</label>
                                    <div class="row" style="text-align:center; margin-top:4px;">
                                        <div class="col-md-6">
                                            <select name="mostrar_preco" id="mostrar_preco" class="form-control">
                                                <option value="0">Não</option>
                                                <option value="1">Sim</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <img src="<?= url("themes/imgs/preco_produto.PNG?" . time()) ?>" style="padding-left: 10px" width="100%" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <b>Endereço Obrigatório:</b><br>
                                    <select name="endereco_obrigatorio" id="endereco_obrigatorio" class="form-control">
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <!-- TABULAÇÃO -->
                        <div class="card card-body">
                            <div class="nav nav-tabs" id="product-tab" role="tablist" style="font-family:inter">
                                <a class="nav-item nav-link active" id="contador-tab" data-toggle="tab" href="#contador" role="tab" aria-controls="contador" aria-selected="false">Contador</a>
                                <a class="nav-item nav-link" id="boletos-tab" data-toggle="tab" href="#boletos" role="tab" aria-controls="boletos" aria-selected="false">Boletos</a>
                                <a class="nav-item nav-link" id="frete-tab" data-toggle="tab" href="#frete" role="tab" aria-controls="frete" aria-selected="false">Frete</a>
                                <a class="nav-item nav-link" id="pixel-tab" data-toggle="tab" href="#pixel" role="tab" aria-controls="pixel" aria-selected="false">Pixels</a>
                                <a class="nav-item nav-link" id="orderbump-tab" data-toggle="tab" href="#order_bump" role="tab" aria-controls="order_bump" aria-selected="false">Order Bump <span class="badge bg-warning">Novo</span></a>
                                <!-- <a class="nav-item nav-link" id="notificacoes-tab" data-toggle="tab" href="#notificacoes" role="tab" aria-controls="notificacoes" aria-selected="false">Notificações</a> -->
                            </div>

                            <div class="tab-content p-3" id="nav-tabContent" style="font-family:inter">

                                <!-- TAB CONTADOR -->
                                <div class="tab-pane fade active show" id="contador" role="tabpanel" aria-labelledby="contador-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Usar contador:</b>
                                            <select name="usar_contador" id="usar_contador" class="form-control input-lg">
                                                <option value="0">Não</option>
                                                <option value="1">Sim</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <b>Texto:</b> <small>max.: 50 caracteres</small>
                                            <input type="text" name="texto_contador" id='texto_contador' class="form-control input-lg" value="OFERTA POR TEMPO LIMITADO">
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Ação após expiração</b>
                                            <select name="acao_contador" id="acao_contador" class="form-control input-lg">
                                                <option value="0">Não Fazer Nada</option>
                                                <option value="1">Aumentar Preço</option>
                                            </select>
                                            <small>Caso seja selecionado "Aumentar Preço", ao finalizar o contador (10 min.) será acrescido um valor que você estipular</small>
                                        </div>

                                        <div class="col-md-6">
                                            <b>Aumento:</b>

                                            <div class="input-group mb-3">
                                                <input type="text" name="aumento" id='aumento' class="form-control input-lg" placeholder="10" value="10">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row box-labels">
                                                <label class="col-10">Cor do contador</label>
                                                <div class="col-2">
                                                    <label class="input-color" id="lbl-cor-contador" style="background: <?= @$checkout->cor_contador ?>">
                                                        <input type="color" name="cor_contador" id="cor_contador" class="box-color" value="<?= @$checkout->cor_contador ?>">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="row box-labels">
                                                <label class="col-10">Cor do texto</label>
                                                <div class="col-2">
                                                    <label class="input-color" id="lbl-cor-titulo" style="background: <?= @$checkout->cor_texto ?>">
                                                        <input type="color" name="cor_texto" id="cor_texto" class="box-color" value="<?= @$checkout->cor_texto ?>"><br>
                                                    </label>
                                                </div><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM TAB CONTADOR -->

                                <!-- TAB BOLETOS -->
                                <div class="tab-pane fade" id="boletos" role="tabpanel" aria-labelledby="boletos-tab">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <b>Vencimento Boletos: <small>em dias corridos</small></b>
                                            <input type="text" name="vencimento_boletos" id='vencimento_boletos' class="form-control input-lg">
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM TAB BOLETOS -->

                                <!-- TAB FRETE -->
                                <div class="tab-pane fade" id="frete" role="tabpanel" aria-labelledby="frete-tab">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <b>Usar cálculo de frete?</small></b>
                                            <select name="usar_frete" id="usar_frete" class="form-control">
                                                <option value="0">Não</option>
                                                <option value="1">Sim - Frete Fixo</option>
                                                <option value="2">Sim - via Correios</option>
                                                <option value="3">Sim - via JadLog</option>
                                                <option value="4">Sim - por Região</option>
                                            </select>
                                        </div>

                                        <!-- FRETE FIXO -->

                                        <div class="col-md-4 mostrar_fixo">
                                            <span>
                                                <b>Frete Fixo: </b>
                                                <input type="text" name="valor_frete" id="valor_frete" class="form-control input-lg" placeholder="R$ 0,00">

                                                <br>
                                                <b>Prazo de entrega: </b>
                                                <input type="text" name="prazo_entrega" id="prazo_entrega" class="form-control input-lg" placeholder="Em Dia(s)">

                                            </span>
                                        </div>

                                        <div class="col-md-4 mostrar_fixo">
                                            <span>
                                                <b>Frete grátis: </b>
                                                <select name="frete_gratis" id="frete_gratis" class="form-control">
                                                    <option value="0">Não</option>
                                                    <option value="1">Sim</option>
                                                </select>

                                                <br>
                                                <b>Prazo de entrega grátis: </b>
                                                <input type="text" name="prazo_entrega_gratis" id='prazo_entrega_gratis' class="form-control input-lg" placeholder="Em Dia(s)">

                                            </span>
                                        </div>
                                        <!-- FIM FRETE FIXO -->

                                        <!-- SEDEX -->
                                        <div class="col-md-4 frete_sedex">
                                            <span><b>Tipo de Entrega</b></span>
                                            <select name="tipo_entrega_correios" id="tipo_entrega_correios" class="form-control">
                                                <option value="0">TODOS</option>
                                                <option value="1">SEDEX</option>
                                                <option value="2">PAC</option>
                                            </select>
                                        </div>


                                        <div class="col-md-4 frete_sedex">
                                            <span>
                                                <b>Formato: * </b>
                                                <select name="formato_sedex" id="formato_sedex" class="form-control">
                                                    <option value="0">Caixa/Pacote</option>
                                                    <option value="1">Envelope</option>
                                                </select>
                                            </span>
                                        </div>
                                        <!-- SEDEX -->

                                        <!-- JADLOG -->
                                        <div class="col-md-4 frete_jadlog">
                                            <span> <b>CEP de Origem: *</b>
                                                <input type="text" name="cep_jadlog" maxlength="9" onblur="validaCep(this.value,'#cep_jadlog');" id="cep_jadlog" class="form-control input-lg" placeholder="Apenas números">
                                            </span>
                                        </div>

                                        <div class="col-md-4 frete_jadlog">
                                            <span> <b>Peso(Kg): *</b>
                                                <input type="text" name="peso_jadlog" id="peso_jadlog" class="form-control input-lg" placeholder="0,000">
                                            </span>
                                        </div>
                                        <!-- JADLOG -->

                                        <!-- FRETE REGIAO -->
                                        <div class="col-md-4 frete_regiao">
                                            <span>
                                                <b>Região Norte: </b>
                                                <i class="fas fa-question-circle" title="Pará - Amapá - Amazonas - Roraima - Acre"></i>

                                                <input type="text" name="regiao_norte" id="regiao_norte" class="form-control input-lg" placeholder="R$ 0,00">
                                            </span><br>
                                        </div>

                                        <div class="col-md-4 frete_regiao">
                                            <span> <b>Prazo de entrega:</b>
                                                <input type="text" name="prazo_entrega_regiao_norte" id="prazo_entrega_regiao_norte" class="form-control input-lg" placeholder="Em Dia(s)">
                                            </span>
                                        </div>

                                        <!-- FIM FRETE REGIAO -->

                                    </div>

                                    <!-- SEDEX -->
                                    <div class="frete_sedex">
                                        <br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span> <b>CEP de Origem: *</b>
                                                    <input type="text" maxlength="9" onblur="validaCep(this.value,'#cep_sedex');" name="cep_sedex" id="cep_sedex" class="form-control input-lg" placeholder="Apenas números">
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span>
                                                    <b>Valor padrão: </b>
                                                    <input type="text" name="valor_padrao_sedex" id="valor_padrao_sedex" class="form-control input-lg" placeholder="0,00">
                                                </span>
                                            </div>

                                            <div class="col-md-4">
                                                <span> <b>Prazo de entrega padrão: *</b>
                                                    <input type="text" name="prazo_entrega_sedex" onblur="validaDias(this.value,'#prazo_entrega_sedex')" id="prazo_entrega_sedex" class="form-control input-lg" placeholder="Em Dia(s)">
                                                </span>
                                            </div>

                                        </div>

                                        <div class="">
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4 peso_alt_larg">
                                                    <img src="<?= url("themes/imgs/caixanormal2.jpg") ?>" style="position: absolute;margin-left: -10%;" width="100%" alt="">

                                                </div>

                                                <div class="col-md-4 img_envelope">
                                                    <img src="<?= url("themes/imgs/envelope.jpg") ?>" style="position: absolute;margin-left: -10%;" width="100%" alt="">
                                                </div>
                                                <div class="col-md-4">
                                                    <span> <b>Largura(cm): *</b>
                                                        <input type="text" name="largura_sedex" onblur="validaLarg(this.value)" id="largura_sedex" class="form-control input-lg" placeholder="0,00">
                                                    </span>
                                                </div>

                                                <div class="col-md-4">
                                                    <span> <b>Comprimento(cm): *</b>
                                                        <input type="text" name="comprimento_sedex" onblur="validaComp(this.value)" id="comprimento_sedex" class="form-control input-lg" placeholder="0,00">
                                                    </span>
                                                </div>
                                            </div>
                                            <br>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <span> <b>Peso(Kg): *</b>
                                                    <input type="text" name="peso_sedex" id="peso_sedex" class="form-control input-lg" placeholder="0,000">
                                                </span>
                                            </div>
                                            <div class="col-md-4 peso_alt_larg">
                                                <span>
                                                    <b>Altura(cm): * </b>
                                                    <input type="text" name="altura_sedex" onblur="validaAlt(this.value)" id="altura_sedex" class="form-control input-lg" placeholder="0,00">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <p>
                                                <br>
                                                <small><b>Observação: </b> <b>Valor Padrão</b> e <b>Prazo de entrega padrão</b> só irá aparecer caso o sistema não consiga calcular o frete com os <b>Correios.</b></small>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- FIM SEDEX -->

                                    <!-- JADLOG -->
                                    <div class="frete_jadlog">
                                        <br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span>
                                                    <b>Altura(cm): * </b>
                                                    <input type="text" name="altura_jadlog" id="altura_jadlog" class="form-control input-lg" placeholder="0,00">
                                                </span><br>
                                            </div>

                                            <div class="col-md-4">
                                                <span> <b>Largura(cm): *</b>
                                                    <input type="text" name="largura_jadlog" id="largura_jadlog" class="form-control input-lg" placeholder="0,00">
                                                </span>
                                            </div>

                                            <div class="col-md-4">
                                                <span> <b>Comprimento(cm): *</b>
                                                    <input type="text" name="comprimento_jadlog" id="comprimento_jadlog" class="form-control input-lg" placeholder="0,00">
                                                </span>
                                            </div>
                                        </div>

                                        <br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <img src="<?= url("themes/imgs/caixanormal2.jpg") ?>" style="position: absolute;margin-left: -10%;margin-top: -14%;" width="95%" alt="">
                                            </div>

                                            <div class="col-md-4">
                                                <span>
                                                    <b>Valor padrão: </b>
                                                    <input type="text" name="valor_padrao_jadlog" id="valor_padrao_jadlog" class="form-control input-lg" placeholder="R$ 0,00">
                                                </span>
                                            </div>

                                            <div class="col-md-4">
                                                <span> <b>Prazo de entrega padrão: *</b>
                                                    <input type="text" name="prazo_entrega_jadlog" onblur="validaDias(this.value,'#prazo_entrega_jadlog')" id="prazo_entrega_jadlog" class="form-control input-lg" placeholder="Em Dia(s)">
                                                </span>
                                            </div>
                                        </div>

                                        <br>
                                        <br>
                                        <div class="row">
                                            <p>
                                                <br>
                                                <small><b>Observação: </b> <b>Valor Padrão</b> e <b>Prazo de entrega padrão</b> só irá aparecer caso o sistema não consiga calcular o frete com a <b>JadLog.</b></small>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- FIM JADLOG -->


                                    <!-- FRETE POR REGIÃO -->
                                    <div class="frete_regiao">

                                        <div class="row">

                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <span> <b>Região Nordeste:</b>
                                                    <i class="fas fa-question-circle" title="Bahia - Sergipe - Pernambuco - Alagoas - Paraíba - Rio Grande do Norte - Ceará - Piauí - Maranhão"></i>

                                                    <input type="text" name="regiao_nordeste" id="regiao_nordeste" class="form-control input-lg" placeholder="R$ 0,00">
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span> <b>Prazo de entrega:</b>
                                                    <input type="text" name="prazo_entrega_regiao_nordeste" id="prazo_entrega_regiao_nordeste" class="form-control input-lg" placeholder="Em Dia(s)">
                                                </span>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <!-- <img src="<?= url("themes/imgs/regioesbrasil.PNG") ?>" style="position: absolute;margin-left: -13%; margin-top: -4%;background: whitesmoke;border-radius: 18%;box-shadow: 2px 2px 5px #aaa;" width="110%" alt=""> -->
                                                <img src="<?= url("themes/imgs/regioesbrasil.png") ?>" style="position: absolute;margin-left: -15%;margin-top: -30%;" width="110%" alt="">
                                            </div>
                                            <div class="col-md-4">
                                                <span> <b>Região Centro-Oeste:</b>
                                                    <i class="fas fa-question-circle" title="Distrito Federal - Goiás - Tocantins - Mato Grosso - Rondônia - Mato Grosso do Sul"></i>
                                                    <input type="text" name="regiao_centro_oeste" id="regiao_centro_oeste" class="form-control input-lg" placeholder="R$ 0,00">
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span> <b>Prazo de entrega:</b>
                                                    <input type="text" name="prazo_entrega_regiao_centro" id="prazo_entrega_regiao_centro" class="form-control input-lg" placeholder="Em Dia(s)">
                                                </span>
                                            </div>
                                        </div>

                                        <br>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <span> <b>Região Sudeste:</b>
                                                    <i class="fas fa-question-circle" title="São Paulo - Rio de Janeiro - Espírito Santo - Minas Gerais"></i>
                                                    <input type="text" name="regiao_sudeste" id="regiao_sudeste" class="form-control input-lg" placeholder="R$ 0,00">
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span> <b>Prazo de entrega:</b>
                                                    <input type="text" name="prazo_entrega_regiao_sudeste" id="prazo_entrega_regiao_sudeste" class="form-control input-lg" placeholder="Em Dia(s)">
                                                </span>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">

                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <span>
                                                    <b>Região Sul:</b>
                                                    <i class="fas fa-question-circle" title="Paraná - Santa Catarina - Rio Grande do Sul"></i>
                                                    <input type="text" name="regiao_sul" id="regiao_sul" class="form-control input-lg" placeholder="R$ 0,00">
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span> <b>Prazo de entrega:</b>
                                                    <input type="text" name="prazo_entrega_regiao_sul" id="prazo_entrega_regiao_sul" class="form-control input-lg" placeholder="Em Dia(s)">
                                                </span>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <p>
                                                <small><b>Observação: </b> Caso queria colocar <b>Frete Grátis</b> em alguma região, basta colocar <b>0</b> no valor da região.</small>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- FIM FRETE POR REGIÃO -->

                                </div>
                                <!-- FIM TAB FRETE -->

                                <!-- TAB PIXEL -->
                                <div class="tab-pane fade" id="pixel" role="tabpanel" aria-labelledby="pixel-tab">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/Facebook.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px">
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_facebook" id="pixel_facebook" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel1) : ?>
                                                        <?php if ($pixel1->plataforma == "Facebook" && $pixel1->tipo_integracao != "api") : ?>
                                                            <option value="<?= $pixel1->id ?>"><?= $pixel1->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/Facebook.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px"><small>Api de conversão</small>
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_facebook_api" id="pixel_facebook_api" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel1) : ?>
                                                        <?php if ($pixel1->plataforma == "Facebook" && $pixel1->tipo_integracao == "api") : ?>
                                                            <option value="<?= $pixel1->id ?>"><?= $pixel1->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/Taboola.png") ?>" width='100px' style="margin-bottom: 5px">
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_taboola" id="pixel_taboola" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel3) : ?>
                                                        <?php if ($pixel3->plataforma == "Taboola") : ?>
                                                            <option value="<?= $pixel3->id ?>"><?= $pixel3->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/GoogleAnalitics.png") ?>" width='70px' style="margin-bottom: 5px">
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_google_analitics" id="pixel_google_analitics" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel2) : ?>
                                                        <?php if ($pixel2->plataforma == "GoogleAnalitics") : ?>
                                                            <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/GoogleAs.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px">
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_google_ads" id="pixel_google_ads" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel2) : ?>
                                                        <?php if ($pixel2->plataforma == "GoogleAds") : ?>
                                                            <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/GoogleTagManager.png") ?>" width='85px' style="border-radius: 20px; margin-bottom: 5px">
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_google_tag_manager" id="pixel_google_tag_manager" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel2) : ?>
                                                        <?php if ($pixel2->plataforma == "GoogleTagManager") : ?>
                                                            <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/TikTok2.png") ?>" width='85px' style="border-radius: 20px; margin-bottom: 5px">
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_tiktok" id="pixel_tiktok" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel2) : ?>
                                                        <?php if ($pixel2->plataforma == "TikTok") : ?>
                                                            <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                   
                                        <br>
                                        <div class="col-md-4">
                                            <img src="<?= url("themes/imgs/Kwai.png") ?>" width='85px' style="border-radius: 20px; margin-bottom: 5px">
                                            <?php if ($pixel) : ?>
                                                <select name="pixel_kwai" id="pixel_kwai" class="form-control">
                                                    <option value="">Selecionar</option>
                                                    <?php foreach ($pixel as $pixel2) : ?>
                                                        <?php if ($pixel2->plataforma == "Kwai") : ?>
                                                            <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                        
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <br />
                                            <label for="checkboxSuccess1"> Não tem Pixel? <a href="<?= url("pixel") ?>" target="_blank">Cadastrar Pixel</a></label>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM TAB PIXEL -->

                                <!-- TAB ORDER BUMP -->
                                <div class="tab-pane fade" id="order_bump" role="tabpanel" aria-labelledby="orderbump-tab">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <b>Usar Order Bump:</b>
                                            <select name="usar_order_bump" id="usar_order_bump" class="form-control input-lg">
                                                <option value="0">Não</option>
                                                <option value="1">Sim</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <span class="mostrar_order_bump">
                                                <label>Texto Informativo</label>
                                                <input type="text" name="titulo_informativo" id='titulo_informativo' class="form-control input-lg" value="Descrição do produto ofertado">
                                                <small>max.: 50 caracteres</small>
                                            </span>
                                        </div>
                                    </div>



                                    <div class="row">
                                        <div class="col-md-8">
                                            <span class="mostrar_order_bump">
                                                <br>

                                                <label for="idSelect2">
                                                    <b>Escolha o(s) Plano(s):</b>
                                                </label>
                                                <br>

                                                <small>Plano 1:</small>
                                                <select class="form-control" name="planos_order_bump0" id="planos_order_bump0" tabindex="-1">
                                                    <option value="">Selecionar</option>
                                                    <?php if ($planosOrderBump) : $i = 1;
                                                        foreach ($planosOrderBump as $planos) : $planos->privado == 1 ? $privado = "**" : $privado = ""; ?>
                                                            <?php if ($planos->dataProduto()->excluir != 1) : ?>
                                                                <option value="<?= $planos->id ?>" id="opcao<?= $i ?>"><?= "{$planos->dataProduto()->nome} / $planos->nome $privado" ?></option>
                                                    <?php $i++;
                                                            endif;
                                                        endforeach;
                                                    endif; ?>
                                                </select>

                                                <small>Plano 2 (opcional):</small>
                                                <select class="form-control" name="planos_order_bump1" id="planos_order_bump1" tabindex="-1">
                                                    <option value="">Selecionar</option>
                                                    <?php if ($planosOrderBump) : $i = 1;
                                                        foreach ($planosOrderBump as $planos) : $planos->privado == 1 ? $privado = "**" : $privado = ""; ?>
                                                            <?php if ($planos->dataProduto()->excluir != 1) : ?>
                                                                <option value="<?= $planos->id ?>" id="opcao<?= $i ?>"><?= "{$planos->dataProduto()->nome} / $planos->nome $privado" ?></option>
                                                    <?php $i++;
                                                            endif;
                                                        endforeach;
                                                    endif; ?>
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM TAB ORDER BUMP -->

                            </div>
                        </div>
                        <!-- FIM TABULAÇÃO -->

                    </div>
                </div>
                <!-- FIM MODAL BODY-->

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                    <button class="btn btn-primary" id="salvar">Salvar</button>
                    <button class="btn btn-warning btn-editar" id="editar">Editar</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- FIM MODAL CADASTRO -->
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
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/cadastrar-editar-um-checkout/" target="_blank">Dúvidas sobre o Checkout</a></div>
        <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
        <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
    </div>
</div>
<!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

<?= $v->start("scripts") ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="<?= url("themes/tema1/dist/js/mascara-dinheiro.js") ?>"></script>
<script src="<?= url("themes/tema1/dist/js/mascara-peso.js") ?>"></script>

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

    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Checkout");
        $("#salvar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#editar").css("display", "none");
        $(".frete_sedex").css("display", "none");
        $(".frete_jadlog").css("display", "none");
        $(".frete_regiao").css("display", "none");
        $(".mostrar_fixo").css("display", "none");
        $(".img_envelope").css("display", "none");

        cancelar();
    });

    $("#valor_cupom").blur(function(e) {
        var valor = $("#valor_cupom").val();
        $("#valor_cupom").val(valor.replace(",", "."));
    });

    $("#aumento").blur(function(e) {
        var valor = $("#aumento").val();
        $("#aumento").val(valor.replace(",", "."));
    });

    $("#meio_pagamento1,#meio_pagamento2,#meio_pagamento3").click(() => {
        $("#meio_pagamento10").prop("checked", false);
    });

    $("#meio_pagamento10").click(() => {
        $("#meio_pagamento1").prop("checked", false);
        $("#meio_pagamento2").prop("checked", false);
        $("#meio_pagamento3").prop("checked", false);
    });


    // INICIO SESSÃO FRETES
    var frete = 0;
    $(".mostrar_fixo").css("display", "none");
    $(".img_envelope").css("display", "none");


    function tipoEntrega(formato) {
        /* formato = 0 (Caixa/Pacote)
         *  formato == 1 (Envelope)
         */
        if (formato == 1) {
            $(".peso_alt_larg").css("display", "none");
            $(".img_envelope").css("display", "inline");

        } else {
            $(".peso_alt_larg").css("display", "inline");
            $(".img_envelope").css("display", "none");

        }
    }

    function tipoFrete(frete) {
        /*  frete == 0 (Sem Frente)
         *  frete == 1 (Frete Fixo)
         *  frete == 2 (Frete Correios)
         *  frete == 3 (Frete Jadlog)
         *  frete == 4 (Frete por Região)
         */
        if (frete == 1) {
            $(".frete_sedex").css("display", "none");
            $(".frete_regiao").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".mostrar_fixo").css("display", "inline");
            frete = 1;
        } else if (frete == 2) {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_regiao").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".frete_sedex").css("display", "inline");
            frete = 2;
        } else if (frete == 3) {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_sedex").css("display", "none");
            $(".frete_regiao").css("display", "none");
            $(".frete_jadlog").css("display", "inline");
        } else if (frete == 4) {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_sedex").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".frete_regiao").css("display", "inline");
        } else {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".frete_sedex").css("display", "none");
            $(".frete_regiao").css("display", "none");
        }
    }

    $("#usar_frete").change((e) => {
        var frete = $("#usar_frete").val();
        tipoFrete(frete);
    });


    $("#formato_sedex").change((e) => {
        var formato = $("#formato_sedex").val();
        tipoEntrega(formato);
    });

    function validaLarg(larg) {
        larg = parseFloat(larg);
        if (larg < 10) {
            alert("A largura não pode ser menor que 10 cm.");
            // $("#largura_sedex").focus();
        }
    }

    function validaComp(comp) {
        // console.log(larg);
        comp = parseFloat(comp);
        if (comp < 15) {
            alert("O comprimento não pode ser menor que 15 cm.");

        }
    }

    function validaAlt(alt) {
        // console.log(larg);
        alt = parseFloat(alt);
        if (alt < 1) {
            alert("A altura não pode ser menor que 1 cm.");

        }
    }

    // INICIO SESSAO VALIDAÇÕES DE CEP E PRAZOS DE ENTREGA DOS FRETES
    function validaCep(valor_cep, cep) {
        var valor_cep = valor_cep.replace(/\D/g, '');
        var validacep = /^[0-9]{8}$/;
        if (validacep.test(valor_cep)) {
            $(cep).val(valor_cep);
        } else {
            $(cep).val('');
            alert("CEP inválido.");
        }
    }

    function validaDias(valor_dias, tipo_frete) {
        var valor_dias = valor_dias.replace(/\D/g, '');
        $(tipo_frete).val(valor_dias);
    }
    // FIM SESSAO VALIDAÇÕES

    // INICIO SESSÃO MASCARAS CORREIOS

    $("#valor_frete").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#valor_padrao_sedex").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#largura_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#comprimento_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#altura_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#peso_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 3,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });


    // FIM SESSÃO MASCARAS CORREIOS

    // INICIO SESSÃO MASCARAS JADLOG
    $("#valor_padrao_jadlog").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#largura_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#comprimento_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#altura_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#peso_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 3,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });
    // FIM SESSÃO MASCARAS JADLOG

    // INICIO SESSÃO MASCARAS FRETE POR REGIAO
    $("#regiao_norte").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_nordeste").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_centro_oeste").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_sudeste").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_sul").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });
    // FIM SESSÃO MASCARAS CORREIOS

    // FIM SESSÃO FRETES

    // INICIO SESSÃO ORDER BUMP
    var order_bump = 0;
    $(".mostrar_order_bump").css("display", "none");

    $("#usar_order_bump").change((e) => {
        if (order_bump == 0) {
            $(".mostrar_order_bump").css("display", "inline");
            order_bump = 1;
        } else {
            $(".mostrar_order_bump").css("display", "none");
            order_bump = 0;
        }
    });
    // FIM SESSÃO ORDER BUMP


    // INICIO SESSÃO BANNER
    $("#excluir-banner").click((e) => {
        e.preventDefault();
        $("#banner_atual").attr("value", "");
        $("#imagem_banner_atual").hide("slow");
        $("#excluir-banner").hide("slow");
    });

    $("#excluir-aviso").click((e) => {
        e.preventDefault();
        $("#aviso_atual").attr("value", "");
        $("#imagem_aviso_atual").hide("slow");
        $("#excluir-aviso").hide("slow");
    });

    $("#excluir-banner-lateral").click((e) => {
        e.preventDefault();
        $("#banner_lateral").attr("value", "");
        $("#imagem_banner_lateral").hide("slow");
        $("#excluir-banner-lateral").hide("slow");
    });

    $("#excluir-banner").hide();
    $("#excluir-aviso").hide();
    $("#excluir-banner-lateral").hide();

    // FIM SESSÃO BANNER

    function modelo() {
        var modelo = $("#modelo_checkout").val();

        var model = {
            1: "modelo3",
            2: "modelo3",
            3: "modelo3",
            4: "modelo4",
        }

        $("#imagem_checkout").prop("src", "<?= url("themes/tema1/images/") ?>" + model[modelo] + ".png");
    }

    $("#lateral").hide();

    function lateral() {
        var modelo = $("#modelo_checkout").val();

        if (modelo == 4) {
            $("#lateral").show();
        } else {
            $("#lateral").hide();
        }
        console.log("@", modelo);
    }

    $("#modelo_checkout").change(() => {
        modelo();
        lateral();
    });

    function editar(i) {
        var id = dados[i]["id"];
        var descricao = dados[i]["descricao"];
        var plano = dados[i]["plano"];
        var pixel_facebook = dados[i]["pixel_facebook"];
        var pixel_facebook_api = dados[i]["pixel_facebook_api"];
        var pixel_google_analitics = dados[i]["pixel_google_analitics"];
        var pixel_google_ads = dados[i]["pixel_google_ads"];
        var pixel_google_tag_manager = dados[i]["pixel_google_tag_manager"];
        var pixel_taboola = dados[i]["pixel_taboola"];
        var pixel_tiktok = dados[i]["pixel_tiktok"];
        var pixel_kwai = dados[i]["pixel_kwai"];
        var privado = dados[i]["privado"];
        var modelo_checkout = dados[i]["modelo_checkout"];
        var adquirente = dados[i]["adquirente"];
        var adquirente_boleto = dados[i]["adquirente_boleto"];
        var adquirente_pix = dados[i]["adquirente_pix"];
        var mostrar_preco = dados[i]["mostrar_preco"];
        var endereco_obrigatorio = dados[i]["endereco_obrigatorio"];
        var banner = dados[i]["banner"];
        var banner_lateral = dados[i]["banner_lateral"];
        var aviso = dados[i]["aviso"];
        var cupom = dados[i]["cupom"];
        var valor_cupom = dados[i]["valor_cupom"];
        var so_cartao = dados[i]["so_cartao"];
        var usar_contador = dados[i]["usar_contador"];
        var texto_contador = dados[i]["texto_contador"];
        var acao_contador = dados[i]["acao_contador"];
        var aumento = dados[i]["aumento"];
        var cor_contador = dados[i]["cor_contador"];
        var cor_texto = dados[i]["cor_texto"];
        var vencimento_boletos = dados[i]["vencimento_boletos"];
        var usar_order_bump = dados[i]["usar_order_bump"];
        var titulo_informativo = dados[i]["titulo_informativo"];
        var planos_order_bump = dados[i]["planos_order_bump"];
        var meio_pagamento = dados[i]["meio_pagamento"];
        var televendas = dados[i]["televendas"];
        var id_frete = dados[i]["id_frete"];
        var usar_frete = dados[i]["usar_frete"];
        var valor_frete = dados[i]["valor_frete"];
        var prazo_entrega = dados[i]["prazo_entrega"];
        var frete_gratis = dados[i]["frete_gratis"];
        var prazo_entrega_gratis = dados[i]["prazo_entrega_gratis"];
        // Nova Tabela de Fretes
        var cep = dados[i]["cep"];
        var altura = dados[i]["altura"];
        var largura = dados[i]["largura"];
        var comprimento = dados[i]["comprimento"];
        var peso = dados[i]["peso"];
        var tipo_entrega = dados[i]["tipo_entrega"];
        var tipo_frete = dados[i]["tipo_frete"];
        var tipo_formato = dados[i]["tipo_formato"];
        var frete = dados[i]["frete"];
        var frete_gratis_novo = dados[i]["frete_gratis_novo"];
        var prazo_entrega_novo = dados[i]["prazo_entrega_novo"];
        var prazo_entrega_gratis_novo = dados[i]["prazo_entrega_gratis_novo"];

        var frete_reg_norte = dados[i]["frete_reg_norte"];
        var prazo_reg_norte = dados[i]["prazo_reg_norte"];

        var frete_reg_nordeste = dados[i]["frete_reg_nordeste"];
        var prazo_reg_nordeste = dados[i]["prazo_reg_nordeste"];

        var frete_reg_centro = dados[i]["frete_reg_centro"];
        var prazo_reg_centro = dados[i]["prazo_reg_centro"];

        var frete_reg_sudeste = dados[i]["frete_reg_sudeste"];
        var prazo_reg_sudeste = dados[i]["prazo_reg_sudeste"];

        var frete_reg_sul = dados[i]["frete_reg_sul"];
        var prazo_reg_sul = dados[i]["prazo_reg_sul"];

        // console.log(id, descricao, banner, pixel_google_analitics, pixel_google_ads);
        // console.log($("#descricao").val(), "order_bump", usar_order_bump);
        // $("#descricao").click(()=>{alert()})

        // console.log($("form").attr("action"));
        $("form").attr("action", "<?= $router->route("produtos.checkoutEditar") ?>");
        // console.log($("form").attr("action"));

        $(".modal-title").html("Editar Checkout");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");

        // $(".btn-editar, .btn-excluir").attr("disabled", "true");
        $("#id").attr("value", id);
        $("#descricao").attr("value", descricao);
        $("#banner_atual").attr("value", banner);
        $("#modelo_checkout option[value=" + modelo_checkout + "]").prop("selected", true);
        $("#adquirente option[value=" + adquirente + "]").prop("selected", true);
        $("#adquirente_boleto option[value=" + adquirente_boleto + "]").prop("selected", true);
        $("#adquirente_pix option[value=" + adquirente_pix + "]").prop("selected", true);
        // console.log("modelo", modelo_checkout);
        modelo();

        $("#mostrar_preco option[value=" + mostrar_preco + "]").prop("selected", true);
        $("#endereco_obrigatorio option[value=" + endereco_obrigatorio + "]").prop("selected", true);
        // $("#televendas option[value=" + televendas + "]").prop("selected", true);

        var video_prova_social = dados[i]["video_prova_social"];
        var video_posicao = dados[i]["video_posicao"];

        var img_banner = "<?= url("uploads/checkout/") ?>" + banner;
        var img_banner_lateral = "<?= url("uploads/checkout/") ?>" + banner_lateral;
        var img_aviso = "<?= url("uploads/checkout/") ?>" + aviso;

        $("#video_prova_social").val(video_prova_social ? atob(video_prova_social) : "");
        $("#video_posicao option[value='" + video_posicao + "']").prop("selected", true);

        $("#banner_atual").attr("value", banner);

        $("#ban_atual").attr("value", banner);
        if (banner) {
            $("#excluir-banner").show();
            $("#imagem_banner_atual").attr("src", img_banner);
        }

        $("#banner_lateral").attr("value", banner_lateral);

        lateral();

        $("#ban_lateral").attr("value", banner_lateral);

        if (banner_lateral) {
            $("#excluir-banner-lateral").show();
            $("#imagem_banner_lateral").attr("src", img_banner_lateral);
        }

        $("#aviso_atual").attr("value", aviso);

        $("#avi_atual").attr("value", aviso);

        if (aviso) {
            $("#excluir-aviso").show();
            $("#imagem_aviso_atual").attr("src", img_aviso);
        }

        var meios_pagamento = meio_pagamento.split(",");

        for (i = 0; i < 11; i++) {
            $("#meio_pagamento" + i).prop("checked", false);
        }

        planos_order_bump = planos_order_bump.split(",");
        // console.log(planos_order_bump);

        for (i = 0; i < 2; i++) {
            // console.log(i);
            if (planos_order_bump[i])
                $("#planos_order_bump" + i + " option[value=" + planos_order_bump[i] + "]").prop({
                    "selected": true
                });
            // console.log($("#planos_order_bump" + i));
            // console.log("asas", planos_order_bump[i]);
        }

        meios_pagamento.forEach((indice, valor) => {
            $("#meio_pagamento" + indice).prop("checked", true);
            // console.log("indice", indice);
        });

        if (plano)
            $("#plano option[value=" + plano + "]").prop("selected", true);

        if (pixel_facebook)
            $("#pixel_facebook option[value=" + pixel_facebook + "]").prop("selected", true);

        if (pixel_facebook_api)
            $("#pixel_facebook_api option[value=" + pixel_facebook_api + "]").prop("selected", true);

        if (pixel_google_analitics)
            $("#pixel_google_analitics option[value=" + pixel_google_analitics + "]").prop("selected", true);

        if (pixel_google_ads)
            $("#pixel_google_ads option[value=" + pixel_google_ads + "]").prop("selected", true);

        if (pixel_google_tag_manager)
            $("#pixel_google_tag_manager option[value=" + pixel_google_tag_manager + "]").prop("selected", true);

        if (pixel_taboola)
            $("#pixel_taboola option[value=" + pixel_taboola + "]").prop("selected", true);

        if (pixel_tiktok)
            $("#pixel_tiktok option[value=" + pixel_tiktok + "]").prop("selected", true);

        if (pixel_kwai)
            $("#pixel_kwai option[value=" + pixel_kwai + "]").prop("selected", true);

        $("#privado option[value=" + privado + "]").prop("selected", true);

        $("#cupom").attr("value", cupom);
        $("#valor_cupom").attr("value", valor_cupom);
        $("#so_cartao option[value='" + so_cartao + "']").prop("selected", true);

        $("#texto_contador").attr("value", texto_contador);
        $("#usar_contador option[value='" + usar_contador + "']").prop("selected", true);
        $("#acao_contador option[value='" + acao_contador + "']").prop("selected", true);
        $("#aumento").attr("value", aumento);
        $("#cor_contador").attr("value", cor_contador);
        $("#cor_texto").attr("value", cor_texto);

        $("#lbl-cor-contador").css("background", cor_contador);
        $("#lbl-cor-texto").css("background", cor_texto);

        $("#valor_frete").attr("value", valor_frete);
        $("#prazo_entrega").attr("value", prazo_entrega);
        $("#frete_gratis option[value='" + frete_gratis + "']").prop("selected", true);
        $("#prazo_entrega_gratis").attr("value", prazo_entrega_gratis);

        // SESSÃO TIPO DE FRETE
        // id_frete > 0 indica que o frete do checkout está na nova tabela de Fretes
        // Caso contrario irá buscar valores de frete na própria tabela de checkout (Modelo antigo)
        if (id_frete > 0) {
            // var tipo_frete = < ?= @$checkout->dataFrete()->tipo_frete; ?>;
            tipoFrete(tipo_frete);
            // TIPO FRETE FIXO
            if (tipo_frete == 1) {

                $("#valor_frete").attr("value", frete);
                $("#prazo_entrega").attr("value", prazo_entrega_novo);
                $("#frete_gratis option[value='" + frete_gratis_novo + "']").prop("selected", true);
                $("#prazo_entrega_gratis").attr("value", prazo_entrega_gratis_novo);

                // TIPO FRETE CORREIOS E JADLOG
            } else if (tipo_frete == 2 || tipo_frete == 3) {
                if (tipo_frete == 2) {
                    // TIPO ENTREGA CORREIOS (0 = TODOS / 1 = SEDEX / 2 = PAC)
                    // var tipo_entrega = < ?= @$checkout->dataFrete()->correios_entrega; ?>;
                    $("#tipo_entrega_correios option[value='" + tipo_entrega + "']").prop("selected", true);

                    // FORMATO CAIXA/PACOTE OU ENVELOPE (CORREIOS)
                    // var tipo_formato = < ?= @$checkout->dataFrete()->correios_formato; ?>;
                    $("#formato_sedex option[value='" + tipo_formato + "']").prop("selected", true);
                    tipoEntrega(tipo_formato);
                }

                $("#cep_jadlog").val(cep);
                $("#valor_padrao_jadlog").val(frete);
                $("#prazo_entrega_jadlog").val(prazo_entrega_novo);
                $("#altura_jadlog").val(altura);
                $("#largura_jadlog").val(largura);
                $("#comprimento_jadlog").val(comprimento);
                $("#peso_jadlog").val(peso);
                $("#cep_sedex").val(cep);
                $("#valor_padrao_sedex").val(frete);
                $("#prazo_entrega_sedex").val(prazo_entrega_novo);
                $("#altura_sedex").val(altura);
                $("#largura_sedex").val(largura);
                $("#comprimento_sedex").val(comprimento);
                $("#peso_sedex").val(peso);
                // TIPO FRETE POR REGIAO
            } else {
                $("#regiao_norte").attr("value", frete_reg_norte);
                $("#regiao_nordeste").attr("value", frete_reg_nordeste);
                $("#regiao_centro_oeste").attr("value", frete_reg_centro);
                $("#regiao_sudeste").attr("value", frete_reg_sudeste);
                $("#regiao_sul").attr("value", frete_reg_sul);
                $("#prazo_entrega_regiao_norte").attr("value", prazo_reg_norte);
                $("#prazo_entrega_regiao_nordeste").attr("value", prazo_reg_nordeste);
                $("#prazo_entrega_regiao_centro").attr("value", prazo_reg_centro);
                $("#prazo_entrega_regiao_sudeste").attr("value", prazo_reg_sudeste);
                $("#prazo_entrega_regiao_sul").attr("value", prazo_reg_sul);
            }
        } else if (id_frete == 0 && usar_frete == 1) {
            tipo_frete = 1;
            tipoFrete(tipo_frete);

        } else {
            tipo_frete = 0;
            tipoFrete(tipo_frete);
        }

        $("#usar_frete option[value='" + tipo_frete + "']").prop("selected", true);
        // FIM SESSÃO TIPO DE FRETE

        $("#usar_order_bump option[value='" + usar_order_bump + "']").prop("selected", true);
        $("#titulo_informativo").attr("value", titulo_informativo);
        $("#planos_order_bump option[value='" + planos_order_bump + "']").prop("selected", true);

        order_bump = $("#usar_order_bump").val();

        if ($("#usar_order_bump").val() == 1) {
            $(".mostrar_order_bump").css("display", "inline");
            // console.log("mostrar");
        } else {
            $(".mostrar_order_bump").css("display", "none");
            // console.log("esconder");
        }

        $("#banner_atual").attr("value", banner);
        $("#aviso_atual").attr("value", aviso);
        $("#vencimento_boletos").attr("value", vencimento_boletos);
    }

    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });

    function cancelar() {
        $("form").attr("action", "<?= $router->route("produtos.checkoutCadastro") ?>");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $(".btn-editar, .btn-excluir").attr("disabled", false);
        $("#id").attr("value", "");
        $("#descricao").attr("value", "");
        $("#modelo_checkout option").prop("selected", false);
        $("#mostrar_preco option").prop("selected", false);
        $("#plano option").prop("selected", false);
        $("#pixel_facebook option").prop("selected", false);
        $("#pixel_facebook_api option").prop("selected", false);
        $("#pixel_google option").prop("selected", false);
        $("#pixel_taboola option").prop("selected", false);
        $("#pixel_tiktok option").prop("selected", false);
        $("#pixel_kwai option").prop("selected", false);
        $("#privado option").prop("selected", false);
        $("#banner_atual").attr("value", "");
        $("#aviso_atual").attr("value", "");
        $("#cupom").attr("value", "");
        $("#valor_cupom").attr("value", "");
        $("#so_cartao").prop("selected", false);
        $("#vencimento_boletos").attr("value", "");
        $("#usar_order_bum option").prop("selected", false);
        $("#titulo_informativo").attr("value", "");
        $("#planos_order_bump option").prop("selected", false);
        $("#meio_pagamento10").prop("checked", true);

        $("#usar_frete option").prop("selected", false);
        $("#valor_frete").attr("value", "");
        $("#prazo_entrega").attr("value", "");
        $("#frete_gratis option").prop("selected", false);
        $("#prazo_entrega_gratis").attr("value", "");
        $("#video_prova_social").val("");
        $("#video_posicao option[value='botao']").prop("selected", true);
    }

    function duplicar(id) {
        $("form[name=linha" + id + "]").attr("action", "<?= $router->route("produtos.checkoutCadastro") ?>");
    }

    function excluir(id) {

        if (confirm("Tem certeza que deseja excluir esse checkout?")) {
            $.ajax({
                url: "<?= $router->route("produtos.checkoutExcluir") ?>",
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

    $("#cor_contador").change(() => {
        $("#lbl-cor-contador").css("background", $("#cor_contador").val());
    });

    $("#cor_texto").change(() => {
        $("#lbl-cor-texto").css("background", $("#cor_texto").val());
    });
</script>
<?= $v->end() ?>

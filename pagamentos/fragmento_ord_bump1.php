<hr><br>
<?php $planos_order_bump = explode(",", $checkout->planos_order_bump);
$i = 1;
// var_dump($planos_order_bump);
foreach ($planos_order_bump as $cod_plano) :
    $order_bump = $planos->findById($cod_plano);

    if ($order_bump) :
        $temPrecoOriginal = @$order_bump->preco_original && $order_bump->preco_original > $order_bump->preco;
?>
        <label class="box-order-bump comprar-order-bump<?= $i ?>" for="comprar-order-bump<?= $i ?>">
            <span class="ordbump-badge"><i class="fas fa-tag"></i> Oferta especial</span>

            <div class="ordbump-linha">
                <div class="ordbump-img-wrap">
                    <?php $foto = $order_bump->foto ? $order_bump->foto : @$order_bump->dataProduto()->foto ?>
                    <img class="img-produto-order-bump" src="<?= url("uploads/produtos/$foto") ?>" alt="">
                </div>

                <div class="ordbump-content">
                    <div class="check-order-bump texto-destaque">
                        <span class="ordbump-headline">Sim! Quero adicionar <?= $order_bump->quantidade ? $order_bump->quantidade : 1 ?> unidade(s) de</span>
                        <b class="ordbump-produto"><?= $order_bump->dataProduto()->nome ?></b>
                    </div>
                    <?= $order_bump->descricao ? '<div class="ordbump-desc">' . $order_bump->descricao . '</div>' : "" ?>
                </div>

                <div class="ordbump-precobox">
                    <?php if ($temPrecoOriginal) : ?>
                        <span class="ordbump-preco-de">R$ <?= number_format($order_bump->preco_original, 2, ",", ".") ?></span>
                    <?php endif; ?>
                    <span class="ordbump-preco">R$ <?= number_format($order_bump->preco, 2, ",", ".") ?></span>
                </div>

                <div class="ordbump-check">
                    <input type="checkbox" class="dias" name="codigo_order_bump[<?= $cod_plano ?>]" id="comprar-order-bump<?= $i ?>" onclick="order(this.id, this.checked)" onload="order(this.id, this.checked)" value="<?= $order_bump->preco ?>">
                    <span class="ordbump-checkmark"></span>
                </div>
            </div>

            <div class="ordbump-rodape">
                <i class="fas fa-shield-alt"></i> Oferta única e exclusiva &bull; Não perca!
            </div>
        </label>
<?php
        $i++;
    endif;
endforeach;
?>

<style>
    .box-order-bump {
        position: relative;
        display: block;
        background: var(--card, #1E1E1E);
        border: 1px solid var(--accent, #00C853);
        border-radius: 16px;
        padding: 18px 16px 0;
        margin: 16px 0;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 0 0 1px rgba(0, 200, 83, .15), 0 0 18px rgba(0, 200, 83, .25);
        transition: box-shadow .2s ease, border-color .2s ease;
    }

    .box-order-bump:hover,
    .box-order-bump.selecionado {
        box-shadow: 0 0 0 1px rgba(0, 200, 83, .3), 0 0 26px rgba(0, 200, 83, .4);
    }

    .ordbump-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--accent, #00C853);
        color: var(--accent, #00C853);
        font-weight: 700;
        font-size: 11px;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 20px;
        margin-bottom: 14px;
    }

    .ordbump-linha {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .ordbump-img-wrap {
        flex-shrink: 0;
    }

    .img-produto-order-bump {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .25);
    }

    .ordbump-content {
        flex: 1 1 180px;
        min-width: 0;
    }

    .check-order-bump.texto-destaque {
        color: var(--text, #f5f5f5);
        font-size: 13.5px;
        line-height: 1.4;
        font-weight: 400;
        padding: 0;
        width: auto;
    }

    .ordbump-headline {
        display: block;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .ordbump-produto {
        display: block;
        color: var(--accent, #00C853);
        font-size: 15px;
        font-weight: 700;
    }

    .ordbump-desc {
        color: var(--muted, #aaa);
        font-size: 12px;
        margin-top: 4px;
    }

    .ordbump-precobox {
        flex-shrink: 0;
        text-align: right;
        margin-left: auto;
    }

    .ordbump-preco-de {
        display: block;
        color: var(--muted, #888);
        font-size: 12px;
        text-decoration: line-through;
    }

    .ordbump-preco {
        display: block;
        color: var(--accent-2, #00E676);
        font-weight: 700;
        font-size: 16px;
        white-space: nowrap;
    }

    .ordbump-check {
        flex-shrink: 0;
        position: relative;
        width: 26px;
        height: 26px;
    }

    .ordbump-check input {
        position: absolute;
        inset: 0;
        opacity: 0;
        margin: 0;
        width: 26px;
        height: 26px;
        cursor: pointer;
    }

    .ordbump-checkmark {
        position: absolute;
        top: 0;
        left: 0;
        width: 26px;
        height: 26px;
        border: 2px solid var(--accent, #00C853);
        border-radius: 8px;
        background: transparent;
        transition: background-color .15s ease, transform .15s ease;
    }

    .ordbump-check input:checked~.ordbump-checkmark {
        background: var(--accent, #00C853);
        transform: scale(1.05);
    }

    .ordbump-checkmark::after {
        content: "";
        position: absolute;
        display: none;
        left: 8px;
        top: 4px;
        width: 6px;
        height: 11px;
        border: solid #06210f;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .ordbump-check input:checked~.ordbump-checkmark::after {
        display: block;
    }

    .ordbump-rodape {
        margin-top: 16px;
        padding: 10px 16px;
        border-top: 1px solid rgba(0, 200, 83, .25);
        background: rgba(0, 200, 83, .08);
        color: var(--accent, #00C853);
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        border-radius: 0 0 15px 15px;
    }

    @media (max-width: 500px) {
        .ordbump-precobox {
            margin-left: 0;
            text-align: left;
            flex-basis: 100%;
            order: 3;
        }

        .ordbump-check {
            order: 4;
        }
    }
</style>

<script>
    function order(id, checked) {
        $("." + id).toggleClass("selecionado", checked);
    }

    // Atualiza preços com order_bump
    totalprod = <?= number_format(@$preco, 2, ".", "") ?>;
    tt = 0;
    id = "";
    tab = "";
    tab2 = "";
    
    form = "";
    id_tab_pagamento = 0;
    cupom_desconto = 0;

    // Verifica qual TAB Clicou para atualizar order_bump
    // TAB CARTÃO(1)/ BOLETO(2)/ PIX(3)
    $("#nav-home-tab").click(() => {
        id_tab_pagamento = 0
    })

    $("#nav-pix-tab").click(() => {
        id_tab_pagamento = 2
    })

    $("#nav-profile-tab").click(() => {
        id_tab_pagamento = 3
    })
    // FIM Verificação TAB


    $(".dias").change(function() {
        atualiza_preco_orderbump(totalprod, id_tab_pagamento);
    });

    // var parc = parcelamento(); 

    function atualiza_preco_orderbump(totalprod, id) {

        // Se tiver cupom
        if (cupom_desconto == true) {
            $("#valor_desconto").attr("value", valor);
            cupom_desconto = parseFloat(valor);
        } else {
            cupom_desconto = 0;
        }

        // Caso tenha Order Bump 
        if (id == 0) {
            parcelamento();
        }

        console.log("id", id, "totalprod", totalprod, "cupom_desconto", cupom_desconto);

        if ($("#resultado_cartao").is(":visible") && (id === 0 || id === 1)) {
            console.log("resultado_cartao")
            tab = "#resultado_cartao";
            form = "#form_cartao ";
        } else if ($("#resultado_pix").is(":visible") && (id === 2 || id === 1 || id === 0)) {
            console.log("resultado_pix")
            tab = "#resultado_pix";
            tab2 = "#txt_valor_pix";
            form = "#form_pix ";
        } else if ($("#resultado_boleto").is(":visible")) {
            console.log("resultado_boleto")
            tab = "#resultado_boleto";
            tab2 = "#txt_valor_boleto";
            form = "#form_boleto ";
        } else {
            console.warn("Nenhuma forma de pagamento visível no DOM para o id:", id);
            return;
        }






        // Pega valor total dos order bumps selecionados

        total_order_bump = $('input.dias:checked').get().reduce(function (tot, el) {
        return tot + Number(el.value);
    }, 0);

        // Pegar valor do frete selecionado
        // Caso Checkout seja Modelo novo (modelo 4) - Aba de frete não
        modelo_checkout = <?= $checkout->modelo_checkout ?>;
        if (modelo_checkout == 4) {
            form = "form ";
        }

        var frete = $(form + 'input[name=frete]:checked').get().reduce(function(frete, ep) {
            return frete + Number(ep.value);
        }, 0);
        // Soma valores do order + produto + frete
        frete = parseFloat(frete);
        totalprod = parseFloat(totalprod);
        total_order_bump = parseFloat(total_order_bump);
        console.log("total_order_bump", total_order_bump, "totalprod", totalprod, "frete", frete);
        cupom_desconto = parseFloat(cupom_desconto);
        tt = (total_order_bump + totalprod + frete - cupom_desconto).toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });
        
        // Atualiza Total
        $(tab).html(tt);

        $(tab2).html(tt);
        $("#resultado_valor").html(tt);

        console.log("total", tt, id);
    }
    // Fim Alteração de preço order bump
</script>
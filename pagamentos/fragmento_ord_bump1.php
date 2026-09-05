<hr><br>
<div class="ordbump-card">
    <div class="ordbump-header">
        <span class="ordbump-badge">Oferta especial</span>
        <span class="txt_titulo_order_bump"><?= $checkout->titulo_informativo ?></span>
    </div>

    <div class="ordbump-list">
<?php $planos_order_bump = explode(",", $checkout->planos_order_bump);
$i = 1;
// var_dump($planos_order_bump);
foreach ($planos_order_bump as $cod_plano) :
    $order_bump = $planos->findById($cod_plano);

    if ($order_bump) :
?>
        <label class="box-order-bump comprar-order-bump<?= $i ?>" for="comprar-order-bump<?= $i ?>">
            <div class="ordbump-img-wrap">
                <?php $foto = $order_bump->foto ? $order_bump->foto : @$order_bump->dataProduto()->foto ?>
                <img class="img-produto-order-bump" src="<?= url("uploads/produtos/$foto") ?>" alt="">
            </div>

            <div class="ordbump-content">
                <div class="check-order-bump texto-destaque">
                    Adicionar <?= $order_bump->quantidade ? $order_bump->quantidade : 1 ?> unidade(s) de <b><?= $order_bump->dataProduto()->nome ?></b> por apenas <span class="ordbump-preco">R$ <?= number_format($order_bump->preco, 2, ",", ".") ?></span>
                </div>
                <?= $order_bump->descricao ? '<div class="ordbump-desc">' . $order_bump->descricao . '</div>' : "" ?>
            </div>

            <div class="ordbump-check">
                <input type="checkbox" class="dias" name="codigo_order_bump[<?= $cod_plano ?>]" id="comprar-order-bump<?= $i ?>" onclick="order(this.id, this.checked)" onload="order(this.id, this.checked)" value="<?= $order_bump->preco ?>">
                <span class="ordbump-checkmark"></span>
            </div>
        </label>
<?php
        $i++;
    endif;
endforeach;
?>
    </div>
</div>

<style>
    .ordbump-card {
        background: var(--card, #1E1E1E);
        border: 1px solid var(--stroke, #333);
        border-radius: 14px;
        padding: 16px 0 4px;
        margin: 10px 0;
        overflow: hidden;
    }

    .ordbump-header {
        text-align: center;
        margin-bottom: 12px;
        padding: 0 16px;
    }

    .ordbump-badge {
        display: inline-block;
        background: var(--accent, #00C853);
        color: #06210f;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 3px 12px;
        border-radius: 20px;
        margin-bottom: 6px;
    }

    .txt_titulo_order_bump {
        display: block;
        color: var(--text, #f5f5f5);
        font-size: 1rem;
        font-weight: 700;
    }

    .ordbump-list {
        display: flex;
        flex-direction: column;
    }

    .box-order-bump {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        cursor: pointer;
        border-top: 1px solid var(--stroke, #333);
        transition: background-color .15s ease;
    }

    .box-order-bump:hover {
        background-color: rgba(128, 128, 128, .08);
    }

    .box-order-bump.selecionado {
        background-color: rgba(0, 200, 83, .1);
    }

    .ordbump-img-wrap {
        flex-shrink: 0;
    }

    .img-produto-order-bump {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        object-fit: cover;
    }

    .ordbump-content {
        flex: 1;
        min-width: 0;
    }

    .check-order-bump.texto-destaque {
        color: var(--text, #f5f5f5);
        font-size: 14px;
        line-height: 1.4;
        font-weight: 500;
    }

    .ordbump-preco {
        color: var(--accent-2, #00E676);
        font-weight: 700;
        white-space: nowrap;
    }

    .ordbump-desc {
        color: var(--muted, #aaa);
        font-size: 12px;
        margin-top: 4px;
    }

    .ordbump-check {
        flex-shrink: 0;
        position: relative;
        width: 22px;
        height: 22px;
    }

    .ordbump-check input {
        position: absolute;
        inset: 0;
        opacity: 0;
        margin: 0;
        width: 22px;
        height: 22px;
        cursor: pointer;
    }

    .ordbump-checkmark {
        position: absolute;
        top: 0;
        left: 0;
        width: 22px;
        height: 22px;
        border: 2px solid var(--muted, #555);
        border-radius: 6px;
        background: var(--card, #2A2A2A);
        transition: background-color .15s ease, border-color .15s ease;
    }

    .ordbump-check input:checked~.ordbump-checkmark {
        background: var(--accent, #00C853);
        border-color: var(--accent, #00C853);
    }

    .ordbump-checkmark::after {
        content: "";
        position: absolute;
        display: none;
        left: 7px;
        top: 3px;
        width: 5px;
        height: 10px;
        border: solid #06210f;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .ordbump-check input:checked~.ordbump-checkmark::after {
        display: block;
    }

    @media (max-width: 500px) {
        .box-order-bump {
            padding: 12px;
            gap: 10px;
        }

        .img-produto-order-bump {
            width: 46px;
            height: 46px;
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
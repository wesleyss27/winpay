<hr><br>
<?php
// Verifica se existe ao menos 1 plano válido antes de desenhar o card
$planos_order_bump = explode(",", $checkout->planos_order_bump);
$order_bumps_validos = [];
foreach ($planos_order_bump as $cod_plano) {
    $ob = $planos->findById($cod_plano);
    if ($ob) {
        $order_bumps_validos[$cod_plano] = $ob;
    }
}
?>
<?php if ($order_bumps_validos) : ?>
    <div class="box-order-bump">
        <span class="ordbump-badge"><i class="fas fa-tag"></i> Oferta especial</span>

        <div class="ordbump-list">
<?php $i = 1;
foreach ($order_bumps_validos as $cod_plano => $order_bump) :
    $temPrecoOriginal = @$order_bump->preco_original && $order_bump->preco_original > $order_bump->preco;
    $textoInformativo = @$order_bump->texto_informativo
        ? $order_bump->texto_informativo
        : "Sim! Quero adicionar " . ($order_bump->quantidade ? $order_bump->quantidade : 1) . " unidade(s) de";
?>
            <label class="ordbump-linha comprar-order-bump<?= $i ?>" for="comprar-order-bump<?= $i ?>">
                <div class="ordbump-img-wrap">
                    <?php $foto = $order_bump->foto ? $order_bump->foto : @$order_bump->dataProduto()->foto ?>
                    <img class="img-produto-order-bump" src="<?= url("uploads/produtos/$foto") ?>" alt="">
                </div>

                <div class="ordbump-content">
                    <div class="check-order-bump texto-destaque">
                        <span class="ordbump-headline"><?= $textoInformativo ?></span>
                        <b class="ordbump-produto"><?= $order_bump->dataProduto()->nome ?></b>
                    </div>
                    <?= $order_bump->descricao ? '<div class="ordbump-desc">' . $order_bump->descricao . '</div>' : "" ?>
                </div>

                <div class="ordbump-acao">
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
            </label>
<?php
    $i++;
endforeach;
?>
        </div>

        <div class="ordbump-rodape">
            <i class="fas fa-shield-alt"></i> Oferta única e exclusiva &bull; Não perca!
        </div>
    </div>
<?php endif; ?>

<style>
    .box-order-bump {
        position: relative;
        display: block;
        background: var(--card, #1E1E1E);
        border: 1px solid var(--stroke, #2c2c2c);
        border-radius: 16px;
        padding: 18px 16px 0;
        margin: 16px 0;
        font-family: inherit;
        box-shadow: 0 0 32px -8px rgba(0, 200, 83, .35), 0 8px 20px rgba(0, 0, 0, .3);
        overflow: hidden;
    }

    .ordbump-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--accent, #00C853);
        color: #06210f;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 20px;
        margin-bottom: 4px;
    }

    .ordbump-list {
        display: flex;
        flex-direction: column;
    }

    .ordbump-linha {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 14px 0;
        margin: 0 -16px;
        padding-left: 16px;
        padding-right: 16px;
        cursor: pointer;
        transition: background-color .15s ease;
    }

    .ordbump-linha+.ordbump-linha {
        border-top: 1px solid var(--stroke, #2c2c2c);
    }

    .ordbump-linha:hover,
    .ordbump-linha.selecionado {
        background-color: rgba(0, 200, 83, .08);
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

    .ordbump-acao {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        margin-left: auto;
    }

    .ordbump-precobox {
        flex-shrink: 0;
        text-align: right;
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
        border: 2px solid var(--muted, #555);
        border-radius: 8px;
        background: rgba(128, 128, 128, .08);
        transition: background-color .15s ease, border-color .15s ease, transform .15s ease;
    }

    .ordbump-check input:checked~.ordbump-checkmark {
        background: var(--accent, #00C853);
        border-color: var(--accent, #00C853);
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
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .ordbump-check input:checked~.ordbump-checkmark::after {
        display: block;
    }

    .ordbump-rodape {
        margin: 0 -16px;
        padding: 10px 16px;
        border-top: 1px solid var(--stroke, #2c2c2c);
        background: rgba(128, 128, 128, .05);
        color: var(--muted, #999);
        font-size: 11.5px;
        font-weight: 500;
        text-align: center;
    }

    .ordbump-rodape i {
        color: var(--accent, #00C853);
    }

    @media (max-width: 500px) {
        .ordbump-acao {
            margin-left: 0;
            flex-basis: 100%;
            justify-content: space-between;
        }

        .ordbump-precobox {
            text-align: left;
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
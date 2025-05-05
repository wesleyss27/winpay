<hr><br>
<span class="txt_titulo_order_bump">
    <b><?= $checkout->titulo_informativo ?></b>
</span>

<?php $planos_order_bump = explode(",", $checkout->planos_order_bump);
$i = 1;
// var_dump($planos_order_bump);
foreach ($planos_order_bump as $cod_plano) :
    $order_bump = $planos->findById($cod_plano);

    if ($order_bump) :
?>
        <div class="box-order-bump comprar-order-bump<?= $i ?>">
            <div class="row">
                <div class="col-md-3 col-6">
                    <label for="comprar-oder-bump">
                        <!-- <input type="hidden" class="dias" name="planos_order_bump[<?= $i ?>]" id="comprar-order-bump<?= $i ?>" value="<?= $cod_plano ?>"> &nbsp; -->

                        <?php $foto = $order_bump->foto ? $order_bump->foto : @$order_bump->dataProduto()->foto ?>
                        <img class="img-produto-order-bump" src="<?= url("uploads/produtos/$foto") ?>" alt="" width="100%">
                    </label>
                </div>

                <div class="col-md-9 col-6" style="display:flex; align-items: center">
                    <div>
                        <div class="check-order-bump texto-destaque">
                            <input type="checkbox" class="dias" name="codigo_order_bump[<?= $cod_plano ?>]" id="comprar-order-bump<?= $i ?>" onclick="order(this.id, this.checked)" onload="order(this.id, this.checked)" value="<?= $order_bump->preco ?>"> &nbsp;
                            Adicionar <?= $order_bump->quantidade ? $order_bump->quantidade : 1 ?> unidade(s) de <b><?= $order_bump->dataProduto()->nome ?></b> por apenas R$ <?= number_format($order_bump->preco, 2, ",", ".") ?></b>
                        </div>
                        <?= $order_bump->descricao ? $order_bump->descricao : "" ?>
                    </div>

                </div>
            </div>
        </div>
<?php
        $i++;
    endif;
endforeach;
?>

<script>
    function order(id, checked) {
        if (checked) {
            $("." + id).css("border", "2px dashed green");
        } else {
            $("." + id).css("border", "2px dashed red");
        }
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

        if ($("#resultado_cartao").length > 0) {
            tab = "#resultado_cartao";
            form = "#form_cartao ";
        } else if ($("#resultado_pix").length > 0) {
            tab = "#resultado_pix";
            tab2 = "#txt_valor_pix";
            form = "#form_pix ";
        } else if ($("#resultado_boleto").length > 0) {
            tab = "#resultado_boleto";
            tab2 = "#txt_valor_boleto";
            form = "#form_boleto ";
        } else {
            console.warn("Nenhuma forma de pagamento disponível.");
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
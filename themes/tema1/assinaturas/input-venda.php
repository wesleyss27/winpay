<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 style="font-family: inter;"><?= $title ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Cadastrar Venda</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="dashboard-container">
        <form action="/processar-venda" method="post" class="form-modern">

            <h4>Dados Pessoais</h4>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Nome Completo *</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Email (opcional)</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Telefone *</label>
                    <input type="tel" name="telefone" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>CPF/CNPJ *</label>
                    <input type="text" name="cpf" class="form-control" required>
                </div>
            </div>

            <h4>Endereço</h4>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>CEP</label>
                    <input type="text" name="cep" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Rua</label>
                    <input type="text" name="rua" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Número</label>
                    <input type="text" name="numero" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Complemento</label>
                    <input type="text" name="complemento" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Bairro</label>
                    <input type="text" name="bairro" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Cidade</label>
                    <input type="text" name="cidade" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Estado</label>
                    <input type="text" name="estado" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h4>Produto</h4>
                    <div class="form-group">
                        <label>Selecione o Produto *</label>
                        <select name="produto" id="produto-select" class="form-control" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <h4>Gerar Cobrança?</h4>
                    <div class="form-group">
                        <input type="checkbox" id="gerar_cobranca" name="gerar_cobranca" checked>
                        <label for="gerar_cobranca">Sim, gerar cobrança imediatamente</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4>Plano</h4>
                    <div class="form-group">
                        <label>Selecione o Plano *</label>
                        <select name="plano" id="plano-select" class="form-control" required>
                            <option value="">Selecione o Produto primeiro...</option>
                        </select>
                    </div>

                    <div id="campo_proxima_cobranca">
                        <h4>Próxima Cobrança</h4>
                        <div class="form-group">
                            <label>Data da próxima cobrança *</label>
                            <input type="date" name="proxima_cobranca_input" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <h4>Forma de Pagamento</h4>
            <div class="form-group">
                <label>Escolha uma forma de pagamento *</label>
                <select name="pagamento" id="forma_pagamento" class="form-control" onchange="mostrarPagamento()" required>
                    <option value="">Selecione...</option>
                    <option value="credit_card">Cartão de Crédito</option>
                    <option value="boleto">Boleto</option>
                    <option value="pix">Pix</option>
                </select>
            </div>


            <div id="cartao_fields" class="pagamento-extra" style="display:none;">
                <h5>Dados do Cartão</h5>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Número do Cartão</label>
                        <input type="text" name="numcart" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nome do Titular</label>
                        <input type="text" name="titular" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>MM</label>
                        <input type="text" name="mes_val" class="form-control" placeholder="MM">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>AAAA</label>
                        <input type="text" name="ano_val" class="form-control" placeholder="AAAA">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>CVC</label>
                        <input type="text" name="cvv" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Parcelas</label>
                        <input type="number" name="parcela" class="form-control" min="1" value="1">
                    </div>
                </div>
            </div>

            <div id="boleto_fields" class="pagamento-extra" style="display:none;">
                <h5>Informações do Boleto</h5>
                <p>O boleto será gerado após o cadastro.</p>
            </div>

            <div id="pix_fields" class="pagamento-extra" style="display:none;">
                <h5>Informações do Pix</h5>
                <p>O QR Code será gerado após o cadastro.</p>
            </div>

            <input type="hidden" name="adquirente_pix" id="adquirente_pix">
            <input type="hidden" name="preco" id="preco">
            <input type="hidden" name="taxa_redrocket" id="taxa_redrocket">
            <input type="hidden" name="valor_desconto" value="0">
            <input type="hidden" name="meio_pagamento_desconto" value="<?= @$meio_pagamento_desconto; ?>">
            <input type="hidden" name="produtor" id="produtor" value="">
            <input type="hidden" name="comissao_produtor" id="comissao_produtor" value="<?= @$comissao_produtor; ?>">
            <input type="hidden" name="email_suporte" value="<?= @$email_suporte; ?>">
            <input type="hidden" name="valor_redrocket" id="valor_redrocket">
            <input type="hidden" name="valor_liquido" id="valor_liquido">
            <input type="hidden" name="coprodutores" id="coprodutores">
            <input type="hidden" name="comissao_coprodutor" id="comissao_coprodutor">
            <input type="hidden" name="soma_coprodutor" id="soma_coprodutor">
            <input type="hidden" name="taxa_transacao" id="taxa_transacao">





            <button type="submit" class="btn btn-primary btn-lg mt-3">Cadastrar Venda</button>
        </form>

        <!-- Tela Pix -->
        <div id="pix-page" style="display:none; text-align: center; padding: 30px;">
            <h4 style="color:#FFD700;">Pagamento via Pix</h4>
            <div class="form-group">
                <label style="color: #fff;">Código Pix:</label>
                <input type="text" id="inp-copiar" class="form-control mb-3" readonly>
                <button class="btn btn-warning btn-lg" onclick="copiarPix()" style="font-weight: 500;">Copiar Código Pix</button>
            </div>
            <button class="btn btn-secondary btn-lg mt-3" id="btn-voltar">Voltar</button>
        </div>

    </div>
</div>

<?= $v->start("scripts") ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

<script>
    $(document).ready(function () {
        $("#gerar_cobranca").on("change", function () {
            if ($(this).is(":checked")) {
                $("#campo_proxima_cobranca").hide();
            } else {
                $("#campo_proxima_cobranca").show();
            }
        }).trigger("change"); // chama na inicialização
    });
</script>


<script>
    const planosPorProduto = <?= json_encode($planosPorProduto) ?>;
    const checkoutPorProduto = <?= json_encode($checkoutPorProduto) ?>;
    const taxaPorProduto = <?= json_encode($taxaPorProduto) ?>;
    const taxaTransacaoPorProduto = <?= json_encode($taxaTransacaoPorProduto) ?>;
    const valorRedrocketPorProduto = <?= json_encode($valorRedrocketPorProduto) ?>;
    const valorLiquidoPorProduto = <?= json_encode($valorLiquidoPorProduto) ?>;
    const comissaoProdutorPorProduto = <?= json_encode($comissaoProdutorPorProduto) ?>;
    const coprodutoresPorProduto = <?= json_encode($coprodutoresPorProduto) ?>;
    const comissaoCoprodutorPorProduto = <?= json_encode($comissaoCoprodutorPorProduto) ?>;
    const somaCoprodutorPorProduto = <?= json_encode($somaCoprodutorPorProduto) ?>;
    


    $("#produto-select").on("change", function() {
    const produtoId = $(this).val();

    $("#taxa_redrocket").val(taxaPorProduto[produtoId]);
    $("#taxa_transacao").val(taxaTransacaoPorProduto[produtoId] ?? 0);  // ✅ Aqui!

    $("#valor_redrocket").val(valorRedrocketPorProduto[produtoId]);
    $("#valor_liquido").val(valorLiquidoPorProduto[produtoId]);

    $("#comissao_produtor")
        .val(comissaoProdutorPorProduto[produtoId])
        .attr('value', comissaoProdutorPorProduto[produtoId])
        .trigger('change');

    $("#coprodutores").val(coprodutoresPorProduto[produtoId]);
    $("#comissao_coprodutor").val(comissaoCoprodutorPorProduto[produtoId]);
    $("#soma_coprodutor").val(somaCoprodutorPorProduto[produtoId]);

    const checkout = checkoutPorProduto[produtoId];
    $("#taxa_redrocket").val(taxaPorProduto[produtoId] ?? 0);



    const planoSelect = $("#plano-select").empty();
    if (produtoId && planosPorProduto[produtoId]) {
        planoSelect.append('<option value="">Selecione...</option>');
        planosPorProduto[produtoId].forEach(function(plano) {
            planoSelect.append(
                $('<option>', {
                    value: plano.id,
                    text: plano.nome + " - R$ " + plano.preco,
                    'data-usuario': plano.usuario,
                    'data-preco': plano.preco
                })
            );
        });
    } else {
        planoSelect.append('<option value="">Nenhum plano disponível</option>');
    }

    $("#produtor").val('').trigger('change');
});


    $("#plano-select").on("change", function() {
        const selectedOption = $(this).find('option:selected');
        const produtor = selectedOption.data('usuario') || '';
        const preco = selectedOption.data('preco') || '';

        $("#produtor").val(produtor);
        $("#preco").val(preco);
    });
</script>



<script>
    function mostrarPagamento() {
        let forma = $("#forma_pagamento").val();
        $("#cartao_fields").toggle(forma === "credit_card");
        $("#boleto_fields").toggle(forma === "boleto");
        $("#pix_fields").toggle(forma === "pix");
    }

    function copiarPix() {
        const input = document.getElementById("inp-copiar");
        if (input && input.value) {
            navigator.clipboard.writeText(input.value).then(() => {
                alert("Código Pix copiado!");
            });
        } else {
            alert("Código Pix ainda não carregado.");
        }
    }


    $(function () {
        $(".form-modern").on("submit", function (e) {
            e.preventDefault();
            const produtoId = $("#produto-select").val();
            console.log("Garantindo comissao_produtor no submit...");
            $("#comissao_produtor")
            .val(comissaoProdutorPorProduto[produtoId] ?? 0)
            .attr('value', comissaoProdutorPorProduto[produtoId] ?? 0)
            .trigger('change');
            let forma = $("#forma_pagamento").val();

            if (forma === "pix") {
                enviarPix();
            } else if (forma === "credit_card") {
                alert("Será gerada uma tentativa de cobrança no cartão do cliente.");
                this.submit();
            } else {
                this.submit();
            }
        });

        function enviarPix() {
            const produtoId = $("#produto-select").val();
            $("#comissao_produtor")
            .val(comissaoProdutorPorProduto[produtoId] ?? 0)
            .attr('value', comissaoProdutorPorProduto[produtoId] ?? 0)
            .trigger('change');
            let nome = $("input[name=nome]").val();
            let cpf = $("input[name=cpf]").val().replace(/\D/g, ''); // ✅ aqui já limpa
            $("input[name=cpf]").val(cpf); // ✅ atualiza o input para garantir que vai limpo
            let telefone = $("input[name=telefone]").val();

            $(".form-control").removeClass('input-error');

            if (!nome || !cpf || !telefone) {
                if (!nome) $("input[name=nome]").addClass('input-error');
                if (!cpf) $("input[name=cpf]").addClass('input-error');
                if (!telefone) $("input[name=telefone]").addClass('input-error');
                return;
            }

            console.log($(".form-modern").serialize());

            $.ajax({
                url: "<?= $router->route("checkout.transacaoPixAss") ?>",
                data: $(".form-modern").serialize(),
                type: "POST",
                beforeSend: () => console.log("Enviando..."),
                error: err => {
                    console.error("Erro:", err);
                    alert("Erro ao enviar solicitação. Tente novamente.");
                },
                success: function (res) {
                    try {
                        let arr;
                        if (typeof res === 'string') {
                            arr = res.match(/\{.*?\}(?=\{|\s*$)/gs);
                        }

                        if (arr && arr.length > 0) {
                            const mercadopago = JSON.parse(arr[0]);
                            const sistema = arr[1] ? JSON.parse(arr[1]) : null;

                            console.log("MercadoPago:", mercadopago);
                            console.log("Sistema:", sistema);

                            if ($("#gerar_cobranca").is(":checked")) {
                                const qrCode = mercadopago.point_of_interaction.transaction_data.qr_code;

                                $(".form-modern").hide();
                                $("#pix-page").show();
                                window.scrollTo({ top: 0, behavior: "smooth" });
                                $("#inp-copiar").val(qrCode);

                                $("#btn-voltar").off("click").on("click", function (e) {
                                    e.preventDefault();
                                    window.location.href = "<?= url('/assinaturas') ?>";
                                });
                            } else {
                                alert("Assinatura criada com sucesso (sem gerar cobrança).");
                                window.location.href = "<?= url('/assinaturas') ?>";
                            }
                        } else {
                            throw new Error("Resposta inesperada do servidor");
                        }
                    } catch (error) {
                        console.error("Erro parse JSON:", error, res);
                        alert("Erro inesperado na resposta do servidor.");
                    }
                }




            });
        }

        function mostrarTelaPix(qrCode, vendaId) {
            $(".form-modern").hide();
            $("#pix-page").show();
            $("#qrcode").empty();
            new QRCode(document.getElementById("qrcode"), { text: qrCode, width: 220, height: 220 });
            $("#inp-copiar").val(qrCode);

            $("#btn-copy").off().on("click", () => {
                navigator.clipboard.writeText(qrCode).then(() => alert("Código Pix copiado!"));
            });

            $("#btn-pix-confirmar").off().on("click", function (e) {
                e.preventDefault();
                window.location.href = "<?= url("checkout/finalizar-compra/") ?>" + vendaId;
            });
        }
    });
</script>
<?= $v->end() ?>

<style>
    .form-modern h4, .form-modern h5 { margin-top: 20px; color: #FFD700; font-weight: bold; }
    .form-modern label { margin-top: 10px; color: #fff; font-family: 'Inter', sans-serif; }
    .form-modern .form-control { background: #1E1E1E; color: #fff; border: 1px solid #333; border-radius: 8px; padding: 10px; }
    .form-modern .form-control:focus { border-color: #FFD700; outline: none; box-shadow: 0 0 5px rgba(255, 215, 0, 0.5); }
    .pagamento-extra { background: #1E1E1E; padding: 15px; border-radius: 8px; margin-top: 15px; }
    .btn-primary { background-color: #FFD700; color: #121212; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; }
    .btn-primary:hover { background-color: #e6c200; }
    .input-error { border: 2px solid red; }
</style>

<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<?= $v->start("links"); ?>
<style>
    .manual-sale-page {
        background: #050505;
        color: #f5f5f5;
        min-height: 100vh;
        padding: 0.5rem clamp(1rem, 2vw + 0.5rem, 2.75rem) 3rem;
    }

    .manual-sale-page::before {
        background: #050505 !important;
    }

    .manual-sale-page .content-header {
        padding: 0;
        margin-bottom: 2rem;
    }

    .manual-sale-page .content-header .container-fluid {
        padding: 0;
    }

    .manual-sale-page .dashboard-container {
        background: rgba(17, 17, 17, 0.94);
        border: 1px solid rgba(217, 192, 0, 0.18);
        border-radius: 20px;
        padding: 2.25rem;
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.35);
    }

    .manual-sale-page h4,
    .manual-sale-page h5 {
        color: #ffd700;
        font-weight: 600;
    }

    .manual-sale-page label {
        font-weight: 500;
        color: rgba(255, 255, 255, 0.78);
    }

    .manual-sale-page input.form-control,
    .manual-sale-page select.form-control {
        background: #1b1b1b;
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #f5f5f5;
    }

    .manual-sale-page input.form-control:focus,
    .manual-sale-page select.form-control:focus {
        background: #202020;
        border-color: rgba(255, 215, 0, 0.48);
        box-shadow: none;
    }

    .manual-sale-page .btn-primary {
        background: #ffd700;
        border-color: #ffd700;
        color: #111;
        font-weight: 600;
    }

    .manual-sale-page .btn-primary:hover {
        background: #ffe34d;
        border-color: #ffe34d;
        color: #111;
    }

    body.sidebar-collapse .manual-sale-page {
        margin-left: 0;
    }

    @media (min-width: 992px) {
        body:not(.sidebar-collapse) .manual-sale-page {
            margin-left: 256px;
        }

        .manual-sale-page {
            padding: 0.75rem 3rem 3.5rem;
        }
    }

    @media (max-width: 991.98px) {
        .manual-sale-page {
            padding: 4.5rem 1.5rem 2.5rem;
        }

        .manual-sale-page .dashboard-container {
            padding: 1.75rem;
        }
    }
</style>
<?= $v->end(); ?>

<div class="content-wrapper manual-sale-page">
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
        <form action="<?= url("assinaturas/venda-manual") ?>" method="post" class="form-modern" enctype="multipart/form-data">
            <input type="hidden" name="items" id="items-field">

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

            <h4>Itens da Venda</h4>
            <div class="row align-items-end">
                <div class="col-md-4 form-group">
                    <label>Produto *</label>
                    <select id="produto-select" class="form-control">
                        <option value="">Selecione...</option>
                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>Plano *</label>
                    <select id="plano-select" class="form-control">
                        <option value="">Selecione o produto primeiro...</option>
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label>Quantidade *</label>
                    <input type="number" id="quantidade" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-2 form-group">
                    <button type="button" class="btn btn-success btn-block" id="adicionar-item">Adicionar</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover" id="itens-tabela">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Plano</th>
                            <th>Recorrência</th>
                            <th class="text-center">Qtd.</th>
                            <th class="text-right">Valor Unit.</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center text-muted" id="itens-vazios">
                            <td colspan="7">Nenhum item adicionado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-md-4 form-group">
                    <label>Parcelas *</label>
                    <select name="parcelas" id="parcelas" class="form-control">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?>x</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>Primeira cobrança *</label>
                    <input type="date" name="primeira_cobranca" id="primeira_cobranca" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label>Resumo</label>
                    <div class="form-control bg-dark text-warning" id="total-geral">R$ 0,00</div>
                    <small id="total-parcelado" class="text-muted d-block mt-1">Valor por parcela: R$ 0,00</small>
                </div>
            </div>

            <h4>Gerar Cobrança?</h4>
            <div class="form-group">
                <input type="checkbox" id="gerar_cobranca" name="gerar_cobranca" checked>
                <label for="gerar_cobranca">Sim, gerar cobrança imediatamente</label>
            </div>

            <h4 class="mt-4">Anexos</h4>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Foto do Cliente</label>
                    <input type="file" name="foto_cliente" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4 form-group">
                    <label>Contrato (PDF ou imagem)</label>
                    <input type="file" name="contrato" class="form-control" accept="application/pdf,image/*">
                </div>
                <div class="col-md-4 form-group">
                    <label>Fotos dos Produtos Entregues</label>
                    <input type="file" name="fotos_produto[]" class="form-control" accept="image/*" multiple>
                    <small class="text-muted d-block mt-1">Envie imagens dos itens entregues ao cliente.</small>
                </div>
            </div>

            <div class="form-group">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="3" placeholder="Informações adicionais sobre a venda"></textarea>
            </div>

            <div id="manual-sale-feedback" class="alert d-none"></div>

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


            <button type="submit" class="btn btn-primary btn-lg mt-3">Cadastrar Venda</button>
        </form>

        <!-- Tela Pix -->
        <div id="pix-page" style="display:none; text-align: center; padding: 30px;">
            <h4 style="color:#FFD700;">Pagamento via Pix</h4>
            <div id="pix-feedback" class="alert alert-info d-none"></div>
            <div id="pix-qrcode" class="d-flex justify-content-center mb-3"></div>
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
    const planosPorProduto = <?= json_encode($planosPorProduto) ?>;
    const produtosInfo = <?= json_encode($produtosInfo) ?>;

    const itensVenda = [];
    const roundCurrency = value => Math.round((Number(value) + Number.EPSILON) * 100) / 100;

    const produtoSelect = document.getElementById("produto-select");
    const planoSelect = document.getElementById("plano-select");
    const quantidadeInput = document.getElementById("quantidade");
    const itensTabelaBody = document.querySelector("#itens-tabela tbody");
    const linhaItensVazios = document.getElementById("itens-vazios");
    const parcelasSelect = document.getElementById("parcelas");
    const totalGeralEl = document.getElementById("total-geral");
    const totalParceladoEl = document.getElementById("total-parcelado");
    const feedbackBox = document.getElementById("manual-sale-feedback");
    const formManual = document.querySelector(".form-modern");
    const itemsField = document.getElementById("items-field");
    const submitButton = formManual.querySelector("button[type='submit']");
    const parcelaCartaoInput = document.querySelector("#cartao_fields input[name='parcela']");

    document.getElementById("adicionar-item").addEventListener("click", adicionarItem);
    produtoSelect.addEventListener("change", atualizarPlanos);
    parcelasSelect.addEventListener("change", atualizarResumo);
    itensTabelaBody.addEventListener("click", removerItemHandler);
    formManual.addEventListener("submit", enviarFormulario);

    atualizarResumo();
    mostrarPagamento();

    function atualizarPlanos() {
        const produtoId = produtoSelect.value;
        planoSelect.innerHTML = '<option value="">Selecione o produto primeiro...</option>';

        if (!produtoId || !planosPorProduto[produtoId] || !planosPorProduto[produtoId].length) {
            return;
        }

        planosPorProduto[produtoId].forEach(plano => {
            const option = document.createElement("option");
            option.value = plano.id;
            option.textContent = `${plano.nome} - ${formatCurrency(Number(plano.preco))}`;
            planoSelect.appendChild(option);
        });
    }

    function adicionarItem() {
        const produtoId = produtoSelect.value;
        const planoId = planoSelect.value;
        const quantidade = Number(quantidadeInput.value || 1);

        if (!produtoId) {
            return exibirMensagem("Selecione um produto para adicionar à venda.", "danger");
        }

        if (!planoId) {
            return exibirMensagem("Selecione um plano válido para o produto.", "danger");
        }

        if (!Number.isFinite(quantidade) || quantidade <= 0) {
            return exibirMensagem("Informe uma quantidade válida.", "danger");
        }

        const produto = encontrarProduto(produtoId);
        const plano = encontrarPlano(produtoId, planoId);

        if (!produto || !plano) {
            return exibirMensagem("Não foi possível localizar o produto ou plano selecionado.", "danger");
        }

        if (Number(produto.controla_estoque) === 1 && quantidade > Number(produto.estoque_atual)) {
            return exibirMensagem(`Estoque insuficiente para o produto ${produto.nome}.`, "danger");
        }

        const precoUnitario = roundCurrency(Number(plano.preco) || 0);
        const subtotal = roundCurrency(precoUnitario * quantidade);

        itensVenda.push({
            produto_id: produto.id,
            produto_nome: produto.nome,
            plano_id: plano.id,
            plano_nome: plano.nome,
            periodo: plano.periodo,
            quantidade,
            preco_unitario: precoUnitario,
            subtotal,
            maximo_recorrencia_plano: plano.maximo_recorrencia,
            coprodutores: plano.coprodutores,
            comissao_coprodutores: plano.comissao_coprodutores
        });

        quantidadeInput.value = 1;
        planoSelect.innerHTML = '<option value="">Selecione o produto primeiro...</option>';

        renderizarItens();
        atualizarResumo();
        exibirMensagem("Item adicionado à venda.", "success");
    }

    function removerItemHandler(event) {
        if (!event.target.classList.contains("remover-item")) {
            return;
        }

        const index = Number(event.target.getAttribute("data-index"));
        if (!Number.isInteger(index)) {
            return;
        }

        itensVenda.splice(index, 1);
        renderizarItens();
        atualizarResumo();
        exibirMensagem("Item removido da venda.", "info");
    }

    function renderizarItens() {
        itensTabelaBody.innerHTML = "";

        if (!itensVenda.length) {
            linhaItensVazios.classList.remove("d-none");
            return;
        }

        linhaItensVazios.classList.add("d-none");

        itensVenda.forEach((item, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${item.produto_nome}</td>
                <td>${item.plano_nome}</td>
                <td>${item.periodo || '-'}</td>
                <td class="text-center">${item.quantidade}</td>
                <td class="text-right">${formatCurrency(item.preco_unitario)}</td>
                <td class="text-right">${formatCurrency(item.subtotal)}</td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remover-item" data-index="${index}">Remover</button></td>
            `;
            itensTabelaBody.appendChild(tr);
        });
    }

    function atualizarResumo() {
        const total = roundCurrency(itensVenda.reduce((soma, item) => soma + item.subtotal, 0));
        const parcelas = Number(parcelasSelect.value || 1);
        const valorParcela = parcelas > 0 ? roundCurrency(total / parcelas) : 0;

        totalGeralEl.textContent = formatCurrency(total);
        totalParceladoEl.textContent = `Valor por parcela: ${formatCurrency(valorParcela)}`;

        if (parcelaCartaoInput) {
            parcelaCartaoInput.value = parcelas;
        }
    }

    function enviarFormulario(event) {
        event.preventDefault();

        if (!itensVenda.length) {
            return exibirMensagem("Adicione ao menos um item antes de finalizar a venda.", "danger");
        }

        itemsField.value = JSON.stringify(itensVenda);

        const formData = new FormData(formManual);

        submitButton.disabled = true;
        submitButton.textContent = "Processando...";

        fetch(formManual.action, {
            method: "POST",
            body: formData
        })
        .then(async response => {
            const text = await response.text();
            try {
                const pos = text.lastIndexOf('}{');
                const jsonSegment = pos >= 0 ? text.slice(pos + 1) : text;
                return JSON.parse(jsonSegment);
            } catch (err) {
                exibirMensagem("Erro ao interpretar a resposta do servidor.", "danger");
                console.error("Resposta bruta:", text);
                throw err;
            }
        })
        .then(data => {
            if (data.success) {
                const resumo = data.manual_sale_id
                    ? `${data.message || "Venda manual registrada com sucesso."}<br><small>ID da venda: #${data.manual_sale_id} · Total: ${formatCurrency(data.valor_total)} · ${data.parcelas}x de ${formatCurrency(data.valor_parcela)}</small>`
                    : (data.message || "Venda manual registrada com sucesso.");
                if (data.gateway && data.gateway.type === "pix") {
                    exibirPix(data.gateway, resumo);
                    return;
                }
                resetarFormulario();
                exibirMensagem(resumo, "success", true);
            } else {
                throw new Error(data.message || "Não foi possível registrar a venda manual.");
            }
        })
        .catch(error => {
            exibirMensagem(error.message, "danger");
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = "Cadastrar Venda";
        });
    }

    function resetarFormulario() {
        formManual.reset();
        itensVenda.length = 0;
        document.getElementById("primeira_cobranca").value = new Date().toISOString().split("T")[0];
        itemsField.value = "";
        renderizarItens();
        atualizarResumo();
        mostrarPagamento();
        if (feedbackBox) {
            feedbackBox.classList.add("d-none");
            feedbackBox.innerHTML = "";
        }
    }

    function exibirMensagem(texto, tipo, usarHtml = false) {
        if (!feedbackBox) {
            return;
        }

        feedbackBox.className = `alert alert-${tipo}`;
        if (usarHtml) {
            feedbackBox.innerHTML = texto;
        } else {
            feedbackBox.textContent = texto;
        }
        feedbackBox.classList.remove("d-none");
        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function encontrarProduto(produtoId) {
        return produtosInfo.find(produto => String(produto.id) === String(produtoId));
    }

    function encontrarPlano(produtoId, planoId) {
        const planos = planosPorProduto[produtoId] || [];
        return planos.find(plano => String(plano.id) === String(planoId));
    }

    function formatCurrency(valor) {
        return Number(valor).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
    }

    function mostrarPagamento() {
        const forma = document.getElementById("forma_pagamento").value;
        document.getElementById("cartao_fields").style.display = forma === "credit_card" ? "block" : "none";
        document.getElementById("boleto_fields").style.display = forma === "boleto" ? "block" : "none";
        document.getElementById("pix_fields").style.display = forma === "pix" ? "block" : "none";
    }

    function copiarPix() {
        const input = document.getElementById("inp-copiar");
        if (input && input.value) {
            navigator.clipboard.writeText(input.value).then(() => alert("Código Pix copiado!"));
        } else {
            alert("Código Pix ainda não carregado.");
        }
    }

    function exibirPix(gateway, mensagemHtml) {
        const form = document.querySelector(".form-modern");
        const pixPage = document.getElementById("pix-page");
        const qrContainer = document.getElementById("pix-qrcode");
        const qrInput = document.getElementById("inp-copiar");
        const voltar = document.getElementById("btn-voltar");
        const pixFeedback = document.getElementById("pix-feedback");

        const qrValue = gateway?.qr_code
            || gateway?.raw?.qrcode
            || gateway?.raw?.qrCode
            || "";

        if (form) form.classList.add("d-none");
        if (pixPage) pixPage.style.display = "block";

        if (qrContainer) {
            qrContainer.innerHTML = "";
            if (qrValue) {
                new QRCode(qrContainer, { text: qrValue, width: 220, height: 220 });
            } else {
                qrContainer.innerHTML = "<p class='text-warning'>QR Code indisponível.</p>";
            }
        }

        if (qrInput) {
            qrInput.value = qrValue;
        }

        if (pixFeedback) {
            pixFeedback.classList.remove("d-none");
            pixFeedback.innerHTML = mensagemHtml || "";
        }

        if (voltar) {
            voltar.onclick = function (e) {
                e.preventDefault();
                if (pixPage) pixPage.style.display = "none";
                if (form) form.classList.remove("d-none");
                resetarFormulario();
                if (pixFeedback) {
                    pixFeedback.classList.add("d-none");
                    pixFeedback.innerHTML = "";
                }
            };
        }
    }

    window.mostrarPagamento = mostrarPagamento;
    window.copiarPix = copiarPix;
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

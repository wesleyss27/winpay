<?php
if ($_SESSION["tipo"] != 1) {
    header("Location: " . url());
}
?>


<?php $v->layout("_theme"); ?>


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
                        <li class="breadcrumb-item active">Notificação</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="dashboard-container">
        <form action="<?= url('/processos/notificar-todos') ?>" method="post" class="form-modern">
            <h4>Notificar Todos</h4>

            <div class="form-group">
                <label>Título *</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Mensagem *</label>
                <textarea name="mensagem" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Imagem (URL - opcional)</label>
                <input type="url" name="imagem" class="form-control" placeholder="https://exemplo.com/imagem.jpg">
            </div>

            <div class="form-group">
                <label>Link (URL ao clicar na notificação - opcional)</label>
                <input type="url" name="url" class="form-control" placeholder="https://seusite.com/pagina">
            </div>
            <button type="submit" class="btn btn-primary btn-lg mt-3">Enviar Notificação</button>

            <hr style="border-top: 1px solid #FFD700; margin: 40px 0;">
            <div class="form-modern">
                <h4>Envio Manual para Webhook</h4>

                <div class="form-group">
                    <label>Nome do Comprador *</label>
                    <input type="text" id="nome_comprador" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>CPF/CNPJ do Comprador *</label>
                    <input type="text" id="cpf_cnpj_comprador" class="form-control" required>
                </div>

                <button type="button" class="btn btn-primary btn-lg mt-3" onclick="enviarWebhook()">Enviar para Webhook</button>

                <p id="respostaWebhook" style="margin-top: 20px; color: #fff; font-family: 'Inter', sans-serif;"></p>
            </div>

        </form>

    </div>
</div>

<style>
    .form-modern h4 { margin-top: 20px; color: #FFD700; font-weight: bold; }
    .form-modern label { margin-top: 10px; color: #fff; font-family: 'Inter', sans-serif; }
    .form-modern .form-control { background: #1E1E1E; color: #fff; border: 1px solid #333; border-radius: 8px; padding: 10px; }
    .form-modern .form-control:focus { border-color: #FFD700; outline: none; box-shadow: 0 0 5px rgba(255, 215, 0, 0.5); }
    .btn-primary { background-color: #FFD700; color: #121212; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; }
    .btn-primary:hover { background-color: #e6c200; }
</style>

<script>
function enviarWebhook() {
    const nome = document.getElementById("nome_comprador").value.trim();
    const cpf = document.getElementById("cpf_cnpj_comprador").value.trim().padStart(11, '0');
    const resposta = document.getElementById("respostaWebhook");

    if (!nome || !cpf) {
        resposta.textContent = "Por favor, preencha todos os campos obrigatórios.";
        return;
    }

    const payload = {
        chave_unica: "",
        transacao: "0001",
        nome_produto: "Input manual Winpay",
        codigo_produto: "0001",
        valor_produto: "37.00",
        nome_plano: "Input manual Winpay",
        codigo_plano: "405838",
        valor_plano: "37.00",
        itens_plano: "1",
        plano_adicional1: null,
        plano_adicional2: null,
        nome_comprador: nome,
        telefone_comprador: "",
        email_comprador: "",
        cpf_cnpj_comprador: cpf,
        cep_comprador: null,
        rua_comprador: null,
        numero_comprador: null,
        complemento_comprador: null,
        bairro_comprador: null,
        cidade_comprador: null,
        estado_comprador: null,
        pais_comprador: "BR",
        nome_produtor: "Winpay",
        telefone_produtor: null,
        email_produtor: "suporte@winpay.com",
        cpf_cnpj_produtor: null,
        nome_afiliado: null,
        telefone_afiliado: null,
        email_afiliado: null,
        cpf_cnpj_afiliado: null,
        forma_pagamento: "MANUAL",
        status_transacao: "Paga",
        status_transacao_codigo: "4",
        url_checkout: "",
        link_boleto: null,
        linha_digitavel: null,
        valor_bruto: 37,
        valor_frete: null,
        valor_desconto: "0.00",
        valor_liquido: "33.23",
        parcelas: "1",
        data_vencimento_boleto: null,
        data_pedido: new Date().toISOString(),
        data_finalizada: new Date().toISOString(),
        data_ultimo_evento: new Date().toISOString(),
        data_criacao: new Date().toISOString(),
        data_atualizacao: new Date().toISOString(),
        src: "manual",
        utm_source: "manual",
        utm_campaign: "manual",
        utm_medium: "manual",
        utm_content: "manual"
    };

    resposta.textContent = "Enviando...";

    fetch("https://bjjsoaadpfowefidvjin.supabase.co/functions/v1/webhook-pagamento", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const text = await response.text();
        resposta.innerHTML = `<strong>Status:</strong> ${response.status}<br><strong>Resposta:</strong> <pre style="color: #fff;">${text}</pre>`;
    })
    .catch(error => {
        resposta.innerHTML = `<span style="color:red">Erro ao enviar webhook: ${error.message}</span>`;
    });
}
</script>



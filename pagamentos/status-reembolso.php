<?php
/**
 * Página pública para acompanhamento de status de reembolso.
 * Consulta por CPF (e opcionalmente ID da solicitação).
 */
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status do Reembolso | Winpay</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f4b400;
            --primary-100: #fff7db;
            --gray-25: #f9fafb;
            --gray-50: #f4f5f7;
            --gray-100: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #1f2937;
            --green: #10b981;
            --red: #ef4444;
            --blue: #3b82f6;
            --shadow: 0 12px 30px rgba(0,0,0,0.06);
            --radius: 14px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: radial-gradient(circle at 30% 20%, rgba(244,180,0,0.12), transparent 22%), var(--gray-25);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--gray-700);
        }
        .page { min-height: 100vh; display: flex; flex-direction: column; }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: #fff;
            box-shadow: 0 6px 20px rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 5;
        }
        .logo { display: flex; align-items: center; gap: 10px; font-weight: 700; color: var(--primary); letter-spacing: -0.02em; }
        .logo img { height: 38px; width: auto; object-fit: contain; }
        .header-title { font-weight: 700; font-size: 1.08rem; color: var(--gray-700); flex: 1; text-align: center; }
        main { flex: 1; display: flex; justify-content: center; padding: 22px 16px 48px; }
        .container { width: 100%; max-width: 900px; display: flex; flex-direction: column; gap: 16px; }
        .card {
            background: #fff;
            border-radius: var(--radius);
            padding: 18px 18px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-100);
        }
        h1 { margin: 0 0 8px; font-size: 1.6rem; }
        h2 { margin: 0; font-size: 1.2rem; }
        p { margin: 0; color: var(--gray-500); }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: var(--gray-700); }
        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }
        @media (min-width: 640px) {
            .grid.two { grid-template-columns: repeat(2, 1fr); }
        }
        .field {
            position: relative;
        }
        .field input {
            width: 100%;
            border: 2px solid var(--gray-100);
            border-radius: 12px;
            padding: 14px 14px 14px 48px;
            font-size: 1rem;
            background: var(--gray-50);
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }
        .field input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(244,180,0,0.18);
        }
        .field .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 18px;
        }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 6px; }
        .btn {
            border: none;
            border-radius: 10px;
            padding: 14px 16px;
            background: linear-gradient(135deg, #f7c948, #f4b400);
            color: #1a1a1a;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.12s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            box-shadow: 0 12px 28px rgba(244,180,0,0.35);
            min-width: 180px;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; box-shadow: none; }
        .hint { font-size: 0.95rem; color: var(--gray-500); }
        .error { color: var(--red); font-weight: 600; margin-top: 8px; display: none; }

        .status-list { display: flex; flex-direction: column; gap: 12px; margin-top: 12px; }
        .status-card {
            border: 1px solid var(--gray-100);
            border-radius: 12px;
            padding: 14px;
            background: var(--gray-50);
        }
        .status-head { display: flex; justify-content: space-between; gap: 10px; align-items: flex-start; }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.95rem;
            color: #1f2937;
            background: var(--primary-100);
        }
        .chip.processing { background: rgba(59,130,246,0.16); color: var(--blue); }
        .chip.approved { background: rgba(16,185,129,0.16); color: var(--green); }
        .chip.denied { background: rgba(239,68,68,0.16); color: var(--red); }
        .chip.done { background: rgba(16,185,129,0.18); color: var(--green); }
        .chip.pending { background: rgba(244,180,0,0.18); color: #b45309; }

        .status-progress {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .step {
            position: relative;
            text-align: center;
            font-weight: 600;
            color: var(--gray-500);
        }
        .step .dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid var(--gray-300);
            background: #fff;
            margin: 0 auto 6px;
            position: relative;
        }
        .step::after {
            content: \"\";
            position: absolute;
            top: 7px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--gray-100);
            z-index: 0;
        }
        .step:last-child::after { display: none; }
        .step.done { color: var(--gray-700); }
        .step.done .dot { background: var(--primary); border-color: var(--primary); box-shadow: 0 0 0 4px rgba(244,180,0,0.18); }
        .step.current { color: var(--blue); }
        .step.current .dot { background: var(--blue); border-color: var(--blue); box-shadow: 0 0 0 4px rgba(59,130,246,0.15); }
        .step.disabled .dot { background: var(--gray-100); border-color: var(--gray-100); }
        .fail-box {
            margin-top: 12px;
            padding: 12px;
            border-radius: 10px;
            background: rgba(239,68,68,0.08);
            color: var(--red);
            border: 1px solid rgba(239,68,68,0.18);
            font-weight: 600;
        }
        .items { margin: 12px 0 0; padding: 0; list-style: none; display: grid; gap: 6px; }
        .items li { background: #fff; border: 1px solid var(--gray-100); border-radius: 8px; padding: 10px 12px; display: flex; justify-content: space-between; color: var(--gray-700); }
        .meta { display: flex; gap: 12px; flex-wrap: wrap; color: var(--gray-500); font-size: 0.95rem; margin-top: 8px; }
        .motivo { margin-top: 10px; color: var(--gray-700); background: #fff; border: 1px dashed var(--gray-100); padding: 10px 12px; border-radius: 8px; }
        .empty { text-align: center; background: #fffbe6; border: 1px dashed var(--primary); color: #b45309; }
    </style>
</head>
<body>
<div class="page">
    <header>
        <div class="logo">
            <img src="/themes/imgs/logos/logo-icon.png" alt="Winpay">
            <span>Winpay</span>
        </div>
        <div class="header-title">Acompanhar reembolso</div>
        <div style="width:40px;height:40px;"></div>
    </header>

    <main>
        <div class="container">
            <section class="card">
                <h1>Atualização da solicitação</h1>
                <p class="hint">Informe o CPF usado na compra para ver o status em tempo real.</p>
                <div class="grid" style="margin-top:12px;">
                    <div>
                        <label for="cpf">CPF</label>
                        <div class="field">
                            <span class="icon">🪪</span>
                            <input type="text" id="cpf" placeholder="000.000.000-00" inputmode="numeric" maxlength="14" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn" id="btn-buscar">Buscar status</button>
                    <span class="hint" id="cpf-label"></span>
                </div>
                <div class="error" id="form-error">Erro</div>
            </section>

            <section class="card empty" id="empty-state" style="display:none;">
                Nenhuma solicitação de reembolso encontrada para o CPF informado.
            </section>

            <section class="card" id="result-card" style="display:none;">
                <div class="status-list" id="status-list"></div>
            </section>
        </div>
    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const statusMap = {
        0: { label: "Pendente", class: "pending", desc: "Recebemos sua solicitação e estamos validando." },
        3: { label: "Processando", class: "processing", desc: "Estamos analisando com o produtor." },
        1: { label: "Aprovado", class: "approved", desc: "Reembolso aprovado, seguindo para conclusão." },
        4: { label: "Concluído", class: "done", desc: "Pagamento de reembolso finalizado." },
        2: { label: "Reprovado", class: "denied", desc: "Solicitação não aprovada. Verifique o motivo." }
    };

    const cpfInput = document.getElementById("cpf");
    const btnBuscar = document.getElementById("btn-buscar");
    const formError = document.getElementById("form-error");
    const emptyState = document.getElementById("empty-state");
    const resultCard = document.getElementById("result-card");
    const statusList = document.getElementById("status-list");
    const cpfLabel = document.getElementById("cpf-label");

    // Máscara simples de CPF
    cpfInput.addEventListener("input", (e) => {
        let value = e.target.value.replace(/\D/g, "").slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        e.target.value = value;
        formError.style.display = "none";
    });

    const currency = (v) => (v || 0).toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const escapeHtml = (str = "") => str.replace(/[&<>\"']/g, (c) => ({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[c]));
    const formatDate = (str) => str ? new Date(str.replace(/ /g, "T")).toLocaleString("pt-BR") : "-";

    function renderStatusCard(item) {
        const info = statusMap[item.status] || statusMap[0];

        const steps = [
            { code: 0, label: "Recebido" },
            { code: 3, label: "Processando" },
            { code: 1, label: "Aprovado" },
            { code: 4, label: "Concluído" }
        ];

        let progressHtml = "";
        if (item.status === 2) {
            progressHtml = `<div class=\"fail-box\">Solicitação reprovada. ${escapeHtml(info.desc)}</div>`;
        } else {
            const currentIndex = steps.findIndex(s => s.code === item.status);
            progressHtml = `<div class=\"status-progress\">${steps.map((s, idx) => {
                const cls = currentIndex === -1 ? "disabled" : idx < currentIndex ? "done" : (idx === currentIndex ? "current" : "");
                return `<div class=\"step ${cls}\"><div class=\"dot\"></div><div>${s.label}</div></div>`;
            }).join("")}</div>`;
        }

        const itensHtml = (item.itens || []).map(i => `
            <li>
                <span>${escapeHtml(i.nome || "Produto")}</span>
                <strong>R$ ${currency(i.valor)}</strong>
            </li>
        `).join("");

        const motivoHtml = item.motivo ? `<div class=\"motivo\"><strong>Motivo informado pelo cliente:</strong><br>${escapeHtml(item.motivo)}</div>` : "";
        const motivoRecusaHtml = item.motivo_recusa ? `<div class=\"motivo\" style=\"border-color:rgba(239,68,68,0.35); color:var(--red);\"><strong>Motivo da reprovação:</strong><br>${escapeHtml(item.motivo_recusa)}</div>` : "";
        const contestacaoHtml = item.contestacao ? `<div class=\"motivo\" style=\"border-color:rgba(59,130,246,0.35); color:var(--blue);\"><strong>Contestação enviada:</strong><br>${escapeHtml(item.contestacao)}${item.contestado_em ? `<br><small>Enviado em ${formatDate(item.contestado_em)}</small>` : ""}</div>` : "";

        return `
            <div class=\"status-card\">
                <div class=\"status-head\">
                    <div>
                        <div style=\"font-weight:700;\">Solicitação #${item.id}</div>
                        <div class=\"meta\">
                            <span>Criado: ${formatDate(item.criado_em)}</span>
                        </div>
                    </div>
                    <span class=\"chip ${info.class}\">${info.label}</span>
                </div>
                ${progressHtml}
                <ul class=\"items\">${itensHtml}</ul>
                <div class=\"meta\">
                    <span>Valor total: <strong>R$ ${currency(item.valor_total)}</strong></span>
                </div>
                <p class=\"hint\" style=\"margin-top:6px;\">${escapeHtml(info.desc)}</p>
                ${motivoHtml}
                ${motivoRecusaHtml}
                ${contestacaoHtml}
                ${item.status === 2 ? `<button class=\"btn\" data-contestar=\"${item.id}\">Contestar decisão</button>` : ""}
            </div>
        `;
    }

    function buscar() {
        const cpf = cpfInput.value.trim();

        if (!cpf || cpf.replace(/\D/g, "").length !== 11) {
            formError.textContent = "Informe um CPF válido.";
            formError.style.display = "block";
            return;
        }

        formError.style.display = "none";
        btnBuscar.disabled = true;
        btnBuscar.textContent = "Buscando...";
        emptyState.style.display = "none";
        resultCard.style.display = "none";
        statusList.innerHTML = "";
        cpfLabel.textContent = "";

        const qs = new URLSearchParams({ cpf });

        fetch(`/reembolsos/status-publico?${qs.toString()}`)
            .then(async (r) => {
                let body;
                try {
                    body = await r.json();
                } catch (e) {
                    const text = await r.text();
                    body = { erro: text || "Não foi possível interpretar a resposta." };
                }
                return { ok: r.ok, body };
            })
            .then(({ ok, body }) => {
                btnBuscar.disabled = false;
                btnBuscar.textContent = "Buscar status";

                if (!ok || body.erro) {
                    emptyState.style.display = "block";
                    emptyState.textContent = body.erro || "Não encontramos solicitações para este CPF.";
                    return;
                }

                const lista = body.reembolsos || [];
                if (!lista.length) {
                    emptyState.style.display = "block";
                    return;
                }

                cpfLabel.textContent = `Resultados para CPF ${cpf}`;
                statusList.innerHTML = lista.map(renderStatusCard).join("\n");
                resultCard.style.display = "block";
            })
            .catch(() => {
                btnBuscar.disabled = false;
                btnBuscar.textContent = "Buscar status";
                emptyState.style.display = "block";
                emptyState.textContent = "Não foi possível consultar agora. Tente novamente em instantes.";
                console.error("Falha ao consultar status de reembolso.");
            });
    }

    btnBuscar.addEventListener("click", buscar);
    cpfInput.addEventListener("keyup", (e) => { if (e.key === "Enter") buscar(); });

    // Delegação para botões de contestação
    document.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-contestar]");
        if (!btn) return;
        const id = btn.getAttribute("data-contestar");
        const cpf = cpfInput.value.trim();
        const texto = prompt("Descreva sua contestação para o produtor:");
        if (!texto) return;
        btn.disabled = true;
        fetch("/reembolsos/contestar", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${encodeURIComponent(id)}&cpf=${encodeURIComponent(cpf)}&contestacao=${encodeURIComponent(texto)}`
        })
        .then(r => r.json())
        .then(resp => {
            if (resp && resp.ok) {
                alert("Contestação enviada. Voltamos o status para pendente.");
                buscar();
            } else {
                alert(resp?.erro || "Não foi possível enviar a contestação.");
            }
        })
        .catch(() => alert("Erro ao enviar contestação."))
        .finally(() => { btn.disabled = false; });
    });
});
</script>
</body>
</html>

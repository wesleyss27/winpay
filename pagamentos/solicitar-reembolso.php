<?php
/**
 * Página de Solicitação de Reembolso - Winpay
 * Front-end estático (UI/UX) em 3 etapas, mobile-first.
 * Integrações reais de busca/validação de CPF e envio devem ser conectadas ao backend.
 */
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Reembolso | Winpay</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-omW1Nsp5Ian+FJxuxMxDPZWS9Vyuk3F7S3w7Dnk3a1JpNggCBvs1+qsSVqSdz+CA0nVddOZXS6URttuPAo9H6w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary: #f4b400;          /* amarelo/gold do painel atual */
            --primary-100: #fff7db;
            --primary-200: #ffe9a3;
            --gray-50: #f7f7f8;
            --gray-100: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-500: #6B7280;
            --gray-700: #1f2937;
            --white: #ffffff;
            --green: #10B981;
            --shadow: 0 12px 30px rgba(0,0,0,0.08);
            --radius: 12px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--gray-50);
            color: var(--gray-700);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: var(--white);
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.02em;
        }
        .logo img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }
        .header-title {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--gray-700);
            text-align: center;
            flex: 1;
        }
        .help-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--gray-100);
            background: var(--white);
            color: var(--primary);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .help-btn:hover { transform: translateY(-1px); box-shadow: var(--shadow); }

        main {
            flex: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 20px 16px 40px;
        }
        .container {
            width: 100%;
            max-width: 720px;
        }
        .stepper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 12px 0 18px;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: var(--gray-500);
            font-weight: 600;
            font-size: 0.9rem;
        }
        .step .circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            border: 2px solid var(--gray-300);
            background: var(--white);
            transition: all 0.2s ease;
            font-weight: 700;
        }
        .step.active { color: var(--primary); }
        .step.active .circle {
            border-color: var(--primary);
            background: var(--primary-100);
        }
        .step.done .circle {
            border-color: var(--primary);
            background: var(--primary);
            color: var(--white);
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 18px 16px;
            box-shadow: var(--shadow);
        }
        .card + .card { margin-top: 16px; }
        h2 {
            margin: 0 0 6px;
            font-size: 1.2rem;
            color: var(--gray-700);
        }
        .subtitle {
            margin: 0 0 16px;
            color: var(--gray-500);
            font-size: 0.95rem;
        }
        label { display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; }
        .field {
            position: relative;
            display: flex;
            align-items: center;
        }
        .field input, .field textarea {
            width: 100%;
            border: 2px solid var(--gray-100);
            border-radius: 10px;
            padding: 14px 14px 14px 42px;
            font-size: 1rem;
            background: var(--gray-50);
            transition: border-color 0.2s ease, background 0.2s ease;
        }
        .field textarea {
            min-height: 120px;
            resize: vertical;
            padding-left: 14px;
        }
        .field input:focus, .field textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(244,180,0,0.18);
        }
        .field .icon {
            position: absolute;
            left: 12px;
            color: var(--gray-500);
        }
        .error {
            color: #ef4444;
            font-size: 0.9rem;
            margin-top: 8px;
        }

        .products {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .product {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--gray-100);
            border-radius: 12px;
            background: var(--gray-50);
            align-items: center;
            transition: border-color 0.2s ease, background 0.2s ease;
        }
        .product.selected {
            border-color: var(--primary);
            background: var(--primary-100);
        }
        .product img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 10px;
            background: var(--white);
        }
        .product h4 {
            margin: 0 0 4px;
            font-size: 1rem;
            color: var(--gray-700);
        }
        .product .meta {
            font-size: 0.9rem;
            color: var(--gray-500);
        }
        .product .price {
            font-weight: 700;
            color: var(--gray-700);
        }
        .checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid var(--gray-300);
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: var(--white);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .checkbox.active {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }
        .summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gray-50);
            padding: 12px 14px;
            border-radius: 10px;
            margin-top: 10px;
            font-weight: 600;
            color: var(--gray-700);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: var(--white);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            box-shadow: 0 10px 25px rgba(244,180,0,0.30);
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:disabled { opacity: 0.55; cursor: not-allowed; box-shadow: none; }
        .btn.secondary {
            background: transparent;
            color: var(--gray-700);
            border: 2px solid var(--gray-200);
            box-shadow: none;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
        }
        .policy {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 12px;
            font-size: 0.95rem;
            color: var(--gray-600);
        }
        .policy input {
            margin-top: 3px;
            accent-color: var(--primary);
        }
        .badge-total {
            background: var(--primary-100);
            color: var(--primary);
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 700;
        }
        .success {
            text-align: center;
            padding: 24px 16px;
        }
        .success .icon {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: rgba(16,185,129,0.12);
            color: var(--green);
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            font-size: 30px;
        }
        footer {
            margin-top: 28px;
            text-align: center;
            color: var(--gray-500);
            font-size: 0.95rem;
        }
        footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .tooltip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--gray-100);
            color: var(--gray-600);
            font-size: 12px;
            cursor: default;
            margin-left: 6px;
        }
        /* Desktop */
        @media (min-width: 780px) {
            header { padding: 18px 32px; }
            .card { padding: 22px 20px; }
            .actions { flex-direction: row; justify-content: flex-end; }
            .actions .btn { width: auto; min-width: 180px; }
            .actions .btn.secondary { min-width: 150px; }
        }
    </style>
</head>
<body>
<div class="page">
    <header>
        <div class="logo">
            <img src="/themes/imgs/logos/logo-icon.png" alt="Winpay">
            <span>Winpay</span>
        </div>
        <div class="header-title">Solicitar Reembolso</div>
        <button class="help-btn" title="Precisa de ajuda?">
            <i class="fa-solid fa-circle-question"></i>
        </button>
    </header>

    <main>
        <div class="container">
            <div class="stepper" id="stepper">
                <div class="step active" data-step="1">
                    <div class="circle">1</div>
                    CPF
                </div>
                <div class="step" data-step="2">
                    <div class="circle">2</div>
                    Produtos
                </div>
                <div class="step" data-step="3">
                    <div class="circle">3</div>
                    Reembolso
                </div>
            </div>

            <!-- Etapa 1 -->
            <section class="card" id="step-1">
                <h2>Digite seu CPF</h2>
                <p class="subtitle">Informe o CPF utilizado na compra para localizar seus pedidos.</p>
                <label for="cpf">CPF <span class="tooltip" title="Usamos o CPF para encontrar suas compras.">i</span></label>
                <div class="field">
                    <span class="icon"><i class="fa-regular fa-id-card"></i></span>
                    <input type="text" id="cpf" placeholder="000.000.000-00" inputmode="numeric" maxlength="14" autocomplete="off">
                </div>
                <div class="error" id="cpf-error" style="display:none;">CPF inválido ou não encontrado.</div>
                <div class="actions" style="margin-top:18px;">
                    <button class="btn" id="btn-cpf">Continuar</button>
                </div>
            </section>

            <!-- Etapa 2 -->
            <section class="card" id="step-2" style="display:none;">
                <h2>Seus Produtos</h2>
                <p class="subtitle">Selecione os itens que deseja solicitar reembolso.</p>
                <div class="products" id="product-list"></div>
                <div class="summary" id="product-summary" style="display:none;">
                    <span><span id="selected-count">0</span> produtos selecionados</span>
                    <span class="badge-total">Total: R$ <span id="selected-total">0,00</span></span>
                </div>
                <div class="actions">
                    <button class="btn secondary" id="btn-back-1">Voltar</button>
                    <button class="btn" id="btn-products" disabled>Continuar para Reembolso</button>
                </div>
            </section>

            <!-- Etapa 3 -->
            <section class="card" id="step-3" style="display:none;">
                <h2>Solicitar Reembolso</h2>
                <p class="subtitle">Confirme os dados e envie a solicitação ao produtor.</p>

                <div class="card" style="padding:14px; background: var(--gray-50); box-shadow:none; border:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <strong>Produtos selecionados</strong>
                        <span class="badge-total" id="summary-total">R$ 0,00</span>
                    </div>
                    <ul id="summary-products" style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px;">
                    </ul>
                </div>

                <div style="margin-top:16px;">
                    <label for="motivo">Motivo da solicitação (opcional)</label>
                    <div class="field">
                        <textarea id="motivo" placeholder="Conte ao produtor o motivo do reembolso..."></textarea>
                    </div>
                </div>

                <div class="policy">
                    <input type="checkbox" id="policy">
                    <label for="policy" style="font-weight:500;">
                        Declaro que estou ciente das políticas de reembolso da Winpay e do produtor.
                        <a href="#" target="_blank">Ver termos</a>
                    </label>
                </div>

                <div class="actions">
                    <button class="btn secondary" id="btn-back-2">Voltar</button>
                    <button class="btn" id="btn-submit" disabled>Enviar Solicitação</button>
                </div>
            </section>

            <!-- Sucesso -->
            <section class="card" id="step-success" style="display:none;">
                <div class="success">
                    <div class="icon"><i class="fa-solid fa-check"></i></div>
                    <h2>Seu reembolso foi solicitado com sucesso e está sendo analisado junto com o vendedor.</h2>
                    <p class="subtitle" style="margin-top:8px;">Em até 5 dias úteis traremos atualização do seu pedido de reembolso.</p>
                    <div class="actions" style="justify-content:center;">
                        <button class="btn secondary" onclick="location.href='/'">Voltar ao Início</button>
                    </div>
                </div>
            </section>

            <footer>
                <a href="#">Termos de Uso</a> · <a href="#">Política de Privacidade</a>
            </footer>
        </div>
    </main>
</div>

<script>
    // Estado
    const products = [];
    const selected = new Set();
    let currentStep = 1;

    // Helpers
    const currency = (v) => v.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const stepEls = document.querySelectorAll(".step");
    const stepSections = {
        1: document.getElementById("step-1"),
        2: document.getElementById("step-2"),
        3: document.getElementById("step-3"),
        success: document.getElementById("step-success"),
    };

    const cpfInput = document.getElementById("cpf");
    const cpfError = document.getElementById("cpf-error");
    const btnCpf = document.getElementById("btn-cpf");
    const btnProducts = document.getElementById("btn-products");
    const btnSubmit = document.getElementById("btn-submit");
    const productList = document.getElementById("product-list");
    const selectedCount = document.getElementById("selected-count");
    const selectedTotal = document.getElementById("selected-total");
    const productSummary = document.getElementById("product-summary");
    const summaryTotal = document.getElementById("summary-total");
    const summaryProducts = document.getElementById("summary-products");

    // Mask CPF
    cpfInput.addEventListener("input", (e) => {
        let value = e.target.value.replace(/\D/g, "").slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d)/, "$1.$2")
                     .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        e.target.value = value;
        cpfError.style.display = "none";
    });

    function setStep(step) {
        currentStep = step;
        stepSections[1].style.display = step === 1 ? "block" : "none";
        stepSections[2].style.display = step === 2 ? "block" : "none";
        stepSections[3].style.display = step === 3 ? "block" : "none";
        stepSections.success.style.display = step === "success" ? "block" : "none";

        stepEls.forEach(el => {
            const s = Number(el.dataset.step);
            el.classList.toggle("active", s === step);
            el.classList.toggle("done", s < step);
        });
    }

    // Simples validação de CPF (tamanho/máscara; ajuste para regra real se necessário)
    function cpfValido(cpf) {
        const only = cpf.replace(/\D/g, "");
        if (only.length !== 11) return false;
        // rejeita CPFs com todos dígitos iguais
        if (/^(\d)\1{10}$/.test(only)) return false;
        let soma = 0, resto;
        for (let i = 1; i <= 9; i++) soma += parseInt(only.substring(i-1, i)) * (11 - i);
        resto = (soma * 10) % 11;
        if ((resto === 10) || (resto === 11)) resto = 0;
        if (resto !== parseInt(only.substring(9, 10))) return false;
        soma = 0;
        for (let i = 1; i <= 10; i++) soma += parseInt(only.substring(i-1, i)) * (12 - i);
        resto = (soma * 10) % 11;
        if ((resto === 10) || (resto === 11)) resto = 0;
        if (resto !== parseInt(only.substring(10, 11))) return false;
        return true;
    }

    btnCpf.addEventListener("click", () => {
        btnCpf.disabled = true;
        btnCpf.textContent = "Validando...";

        const cpf = cpfInput.value;
        if (!cpfValido(cpf)) {
            cpfError.style.display = "block";
            btnCpf.disabled = false;
            btnCpf.textContent = "Continuar";
            return;
        }

        fetch(`/reembolsos/produtos?cpf=${encodeURIComponent(cpf)}`)
            .then(r => r.json())
            .then(resp => {
                if (!resp || resp.erro || !resp.produtos || !resp.produtos.length) {
                    cpfError.style.display = "block";
                    cpfError.textContent = resp?.erro || "Nenhuma compra encontrada para este CPF.";
                    btnCpf.disabled = false;
                    btnCpf.textContent = "Continuar";
                    return;
                }
                preencherProdutos(resp.produtos);
                setStep(2);
                btnCpf.disabled = false;
                btnCpf.textContent = "Continuar";
            })
            .catch(() => {
                cpfError.style.display = "block";
                cpfError.textContent = "Erro ao consultar compras. Tente novamente.";
                btnCpf.disabled = false;
                btnCpf.textContent = "Continuar";
            });
    });

    document.getElementById("btn-back-1").onclick = () => setStep(1);
    document.getElementById("btn-back-2").onclick = () => setStep(2);

    function preencherProdutos(produtosApi) {
        productList.innerHTML = "";
        const list = produtosApi || products;
        products.splice(0, products.length, ...list); // sobrescreve mock com dados reais
        list.forEach(prod => {
            const el = document.createElement("div");
            el.className = "product";
            el.innerHTML = `
                <img src="${prod.img}" alt="${prod.name}">
                <div>
                    <h4>${prod.name}</h4>
                    <div class="meta">Compra em ${prod.date}</div>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="price">R$ ${currency(prod.price)}</div>
                    <div class="checkbox" data-id="${prod.id}">
                        <i class="fa-solid fa-check" style="display:none;"></i>
                    </div>
                </div>
            `;
            const check = el.querySelector(".checkbox");
            check.addEventListener("click", () => toggleProduct(prod.id, el, check));
            productList.appendChild(el);
        });
    }

    function toggleProduct(id, card, checkbox) {
        if (selected.has(id)) {
            selected.delete(id);
            card.classList.remove("selected");
            checkbox.classList.remove("active");
            checkbox.querySelector("i").style.display = "none";
        } else {
            selected.add(id);
            card.classList.add("selected");
            checkbox.classList.add("active");
            checkbox.querySelector("i").style.display = "block";
        }
        updateSummary();
    }

    function updateSummary() {
        const arr = products.filter(p => selected.has(p.id));
        const total = arr.reduce((sum, p) => sum + p.price, 0);
        selectedCount.textContent = arr.length;
        selectedTotal.textContent = currency(total);
        summaryTotal.textContent = "R$ " + currency(total);

        summaryProducts.innerHTML = arr.map(p => `<li>${p.name} - R$ ${currency(p.price)}</li>`).join("");

        productSummary.style.display = arr.length ? "flex" : "none";
        btnProducts.disabled = arr.length === 0;
        btnSubmit.disabled = arr.length === 0 || !document.getElementById("policy").checked;
    }

    btnProducts.addEventListener("click", () => {
        setStep(3);
    });

    document.getElementById("policy").addEventListener("change", (e) => {
        const hasProducts = selected.size > 0;
        btnSubmit.disabled = !(hasProducts && e.target.checked);
    });

    btnSubmit.addEventListener("click", () => {
        btnSubmit.disabled = true;
        btnSubmit.textContent = "Enviando...";

        const payload = {
            cpf: cpfInput.value,
            motivo: document.getElementById("motivo").value,
            produtos: Array.from(selected).map(id => {
                const p = products.find(x => x.id === id);
                return {
                    venda_id: p.venda_id,
                    produto_id: p.produto_id,
                    produtor_id: p.produtor_id,
                    afiliado_id: p.afiliado_id,
                    valor: p.price,
                    nome: p.name
                };
            })
        };

        fetch("/reembolsos/solicitar", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(resp => {
            btnSubmit.textContent = "Enviar Solicitação";
            if (resp && resp.ok) {
                setStep("success");
            } else {
                alert(resp.erro || "Falha ao enviar");
                btnSubmit.disabled = false;
            }
        })
        .catch(() => {
            alert("Erro ao enviar solicitação");
            btnSubmit.textContent = "Enviar Solicitação";
            btnSubmit.disabled = false;
        });
    });
</script>
</body>
</html>

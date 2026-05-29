<?php $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<style>
.atend-page { background:#000; color:#f5f5f5; min-height:100vh; font-family:'Inter',sans-serif; padding-bottom:4rem; }
.atend-header { padding:2.5rem 1.5rem 1rem; }
.atend-title { font-size:2rem; font-weight:700; margin:0; }
.atend-subtitle { color:rgba(255,255,255,.6); margin:.4rem 0 0; }

.atend-card {
  background:rgba(17,17,20,.85);
  border:1px solid rgba(217,192,0,.18);
  border-radius:18px;
  padding:1.75rem;
  margin-bottom:1.5rem;
}
.atend-card h5 { font-size:.8rem; text-transform:uppercase; letter-spacing:.08rem; color:rgba(255,255,255,.5); margin:0 0 1.2rem; }

/* Produto cards */
.produto-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1rem; }
.produto-card {
  background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.1);
  border-radius:12px;
  padding:1rem;
  cursor:pointer;
  transition:all .2s;
  position:relative;
}
.produto-card:hover { border-color:rgba(217,192,0,.4); background:rgba(217,192,0,.05); }
.produto-card.selecionado { border-color:rgb(217,192,0); background:rgba(217,192,0,.12); }
.produto-card .check-mark {
  position:absolute; top:.6rem; right:.6rem;
  width:20px; height:20px;
  border-radius:50%; border:2px solid rgba(255,255,255,.3);
  display:flex; align-items:center; justify-content:center;
  font-size:.75rem; color:#111;
  transition:all .2s;
}
.produto-card.selecionado .check-mark { background:rgb(217,192,0); border-color:rgb(217,192,0); }
.produto-nome { font-weight:600; font-size:.95rem; margin-bottom:.3rem; padding-right:1.5rem; }
.produto-plano { font-size:.78rem; color:rgba(255,255,255,.55); }
.produto-preco { font-size:1.1rem; font-weight:700; color:#f4d03f; margin-top:.6rem; }

/* Busca */
.busca-prod { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#f5f5f5; border-radius:10px; padding:.6rem 1rem; width:100%; margin-bottom:1rem; font-size:.95rem; }
.busca-prod::placeholder { color:rgba(255,255,255,.35); }

/* Resumo da cesta */
.cesta-resumo { display:flex; flex-direction:column; gap:.5rem; }
.cesta-item { display:flex; justify-content:space-between; font-size:.9rem; padding:.35rem 0; border-bottom:1px solid rgba(255,255,255,.06); }
.cesta-item:last-child { border-bottom:none; }
.cesta-total { display:flex; justify-content:space-between; font-weight:700; font-size:1.05rem; margin-top:.75rem; padding-top:.75rem; border-top:1px solid rgba(217,192,0,.3); }
.cesta-vazio { color:rgba(255,255,255,.35); font-size:.9rem; text-align:center; padding:1rem 0; }

/* Valor final editável */
.valor-final-wrap { position:relative; }
.valor-final-wrap span { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:rgba(255,255,255,.5); font-size:1rem; }
.valor-final-input {
  width:100%; background:rgba(255,255,255,.06); border:1px solid rgba(217,192,0,.4);
  color:#f4d03f; border-radius:12px; padding:.8rem 1rem .8rem 2.2rem;
  font-size:1.4rem; font-weight:700;
}
.valor-final-input:focus { outline:none; border-color:rgb(217,192,0); }

/* Dados do cliente */
.form-label-at { font-size:.72rem; text-transform:uppercase; letter-spacing:.08rem; color:rgba(255,255,255,.5); display:block; margin-bottom:.35rem; }
.form-input-at { width:100%; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#f5f5f5; border-radius:10px; padding:.65rem .9rem; font-size:.95rem; }
.form-input-at:focus { outline:none; border-color:rgba(217,192,0,.5); }
.form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }

/* Botão */
.btn-gerar { width:100%; padding:1rem; background:linear-gradient(135deg,rgb(217,192,0),#f4d03f); color:#111; font-weight:700; font-size:1rem; border:none; border-radius:12px; cursor:pointer; transition:opacity .2s; }
.btn-gerar:hover { opacity:.9; }
.btn-gerar:disabled { opacity:.5; cursor:not-allowed; }

/* Modal resultado */
.modal-resultado { display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); z-index:9999; align-items:center; justify-content:center; }
.modal-resultado.aberto { display:flex; }
.modal-box { background:#111; border:1px solid rgba(217,192,0,.3); border-radius:18px; padding:2rem; max-width:480px; width:90%; text-align:center; }
.modal-box h3 { font-size:1.3rem; font-weight:700; margin-bottom:1.5rem; }
.link-pagamento { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.15); border-radius:10px; padding:.8rem 1rem; font-size:.85rem; word-break:break-all; color:#f4d03f; margin-bottom:1rem; }
.btn-copiar { background:rgba(217,192,0,.15); border:1px solid rgba(217,192,0,.4); color:#f4d03f; border-radius:8px; padding:.5rem 1.2rem; font-size:.85rem; cursor:pointer; }
.btn-fechar-modal { margin-top:1rem; background:transparent; border:1px solid rgba(255,255,255,.2); color:rgba(255,255,255,.7); border-radius:8px; padding:.5rem 1.2rem; font-size:.85rem; cursor:pointer; }
</style>
<?= $v->end(); ?>

<div class="atend-page content-wrapper">
  <div class="container-fluid">
    <div class="atend-header">
      <h1 class="atend-title">Nova Venda Personalizada</h1>
      <p class="atend-subtitle">Monte a cesta de produtos, ajuste o valor e gere o link de pagamento PIX para o cliente.</p>
    </div>

    <?php if (!empty($erro)) : ?>
      <div style="background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.35); color:#f87171; border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem;">
        ⚠️ <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif ?>

    <div class="row">
      <!-- Coluna esquerda: produtos -->
      <div class="col-md-7">
        <div class="atend-card">
          <h5>Selecione os produtos</h5>
          <input type="text" class="busca-prod" id="buscaProd" placeholder="Buscar produto..." autocomplete="off">
          <div class="produto-grid" id="produtoGrid">
            <?php if (empty($produtos_com_planos)) : ?>
              <p style="color:rgba(255,255,255,.4); font-size:.9rem; grid-column:1/-1;">
                Nenhum produto ativo encontrado para este produtor.
              </p>
            <?php endif ?>
            <?php foreach ($produtos_com_planos as $row) : ?>
              <?php $produto = $row["produto"]; ?>
              <?php foreach ($row["planos"] as $plano) : ?>
                <div class="produto-card"
                     data-plano-id="<?= $plano->id ?>"
                     data-produto="<?= htmlspecialchars($produto->nome) ?>"
                     data-plano="<?= htmlspecialchars($plano->nome) ?>"
                     data-preco="<?= $plano->preco ?>"
                     onclick="toggleProduto(this)">
                  <div class="check-mark">✓</div>
                  <div class="produto-nome"><?= htmlspecialchars($produto->nome) ?></div>
                  <div class="produto-plano"><?= htmlspecialchars($plano->nome) ?></div>
                  <div class="produto-preco">R$ <?= number_format($plano->preco, 2, ",", ".") ?></div>
                </div>
              <?php endforeach ?>
            <?php endforeach ?>
          </div>
        </div>

        <!-- Dados do cliente -->
        <div class="atend-card">
          <h5>Dados do cliente</h5>
          <div class="form-grid-2" style="margin-bottom:1rem;">
            <div>
              <label class="form-label-at">Nome completo *</label>
              <input type="text" class="form-input-at" id="clienteNome" placeholder="Ex: João da Silva">
            </div>
            <div>
              <label class="form-label-at">E-mail *</label>
              <input type="email" class="form-input-at" id="clienteEmail" placeholder="email@exemplo.com">
            </div>
          </div>
          <div class="form-grid-2">
            <div>
              <label class="form-label-at">CPF / CNPJ *</label>
              <input type="text" class="form-input-at" id="clienteCpf" placeholder="000.000.000-00" maxlength="18">
            </div>
            <div>
              <label class="form-label-at">Telefone / WhatsApp *</label>
              <input type="text" class="form-input-at" id="clienteTelefone" placeholder="(11) 9 0000-0000">
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna direita: resumo + valor -->
      <div class="col-md-5">
        <div class="atend-card" style="position:sticky; top:80px;">
          <h5>Resumo da cesta</h5>
          <div class="cesta-resumo" id="cestaResumo">
            <div class="cesta-vazio" id="cestaVazio">Nenhum produto selecionado</div>
          </div>
          <div class="cesta-total" id="cestaTotalRow" style="display:none;">
            <span>Soma original</span>
            <span id="cestaSomaOriginal">R$ 0,00</span>
          </div>

          <div style="margin-top:1.5rem; margin-bottom:.5rem;">
            <label class="form-label-at">Valor final cobrado do cliente</label>
            <div class="valor-final-wrap">
              <span>R$</span>
              <input type="number" class="valor-final-input" id="valorFinal" step="0.01" min="0.01" placeholder="0,00">
            </div>
            <div style="font-size:.78rem; color:rgba(255,255,255,.4); margin-top:.4rem;">
              Você pode alterar o valor livremente. A taxa da plataforma é cobrada uma única vez sobre este total.
            </div>
          </div>

          <button class="btn-gerar" id="btnGerar" disabled onclick="gerarVenda()">
            Gerar Link de Pagamento PIX
          </button>

          <div id="msgErro" style="color:#f87171; font-size:.85rem; margin-top:.75rem; display:none;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal resultado -->
<div class="modal-resultado" id="modalResultado">
  <div class="modal-box">
    <h3>✅ Link gerado com sucesso!</h3>
    <div style="font-size:.85rem; color:rgba(255,255,255,.6); margin-bottom:1rem;">Envie este link para o cliente efetuar o pagamento via PIX:</div>
    <div class="link-pagamento" id="linkResultado"></div>
    <button class="btn-copiar" onclick="copiarLink()">Copiar Link</button>
    <br>
    <a id="abrirLink" href="#" target="_blank" style="color:#f4d03f; font-size:.85rem; display:inline-block; margin-top:.75rem;">Abrir página de pagamento →</a>
    <br>
    <button class="btn-fechar-modal" onclick="fecharModal()">Fechar</button>
  </div>
</div>

<?= $v->start("scripts"); ?>
<script>
  let selecionados = {}; // planoId -> {produto, plano, preco}

  function toggleProduto(el) {
    const id    = el.dataset.planoId;
    const preco = parseFloat(el.dataset.preco);

    if (selecionados[id]) {
      delete selecionados[id];
      el.classList.remove("selecionado");
    } else {
      selecionados[id] = {
        produto: el.dataset.produto,
        plano:   el.dataset.plano,
        preco:   preco,
      };
      el.classList.add("selecionado");
    }
    atualizarResumo();
  }

  function atualizarResumo() {
    const resumo  = document.getElementById("cestaResumo");
    const vazio   = document.getElementById("cestaVazio");
    const totalRow = document.getElementById("cestaTotalRow");
    const somaEl  = document.getElementById("cestaSomaOriginal");
    const btnGerar = document.getElementById("btnGerar");
    const valorFinal = document.getElementById("valorFinal");

    const ids = Object.keys(selecionados);

    if (ids.length === 0) {
      vazio.style.display = "";
      totalRow.style.display = "none";
      btnGerar.disabled = true;
      return;
    }

    vazio.style.display = "none";
    totalRow.style.display = "flex";

    // Remove itens antigos (não o vazio)
    resumo.querySelectorAll(".cesta-item").forEach(el => el.remove());

    let soma = 0;
    ids.forEach(id => {
      const s = selecionados[id];
      soma += s.preco;
      const div = document.createElement("div");
      div.className = "cesta-item";
      div.innerHTML = `<span>${s.produto} <small style="color:rgba(255,255,255,.45);">${s.plano}</small></span>
                       <span style="color:#f4d03f;">R$ ${s.preco.toFixed(2).replace(".",",")}</span>`;
      resumo.appendChild(div);
    });

    somaEl.textContent = "R$ " + soma.toFixed(2).replace(".", ",");

    // Preenche valorFinal com a soma se estiver vazio ou igual a soma anterior
    if (!valorFinal.dataset.editado || valorFinal.dataset.editado === "0") {
      valorFinal.value = soma.toFixed(2);
    }

    btnGerar.disabled = valorFinal.value <= 0 || ids.length === 0;
  }

  document.getElementById("valorFinal").addEventListener("input", function() {
    this.dataset.editado = "1";
    document.getElementById("btnGerar").disabled = parseFloat(this.value) <= 0 || Object.keys(selecionados).length === 0;
  });

  // Busca de produtos
  document.getElementById("buscaProd").addEventListener("input", function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll(".produto-card").forEach(el => {
      const txt = (el.dataset.produto + " " + el.dataset.plano).toLowerCase();
      el.style.display = txt.includes(q) ? "" : "none";
    });
  });

  function gerarVenda() {
    const ids   = Object.keys(selecionados);
    const valor = parseFloat(document.getElementById("valorFinal").value);
    const nome  = document.getElementById("clienteNome").value.trim();
    const email = document.getElementById("clienteEmail").value.trim();
    const cpf   = document.getElementById("clienteCpf").value.trim();
    const tel   = document.getElementById("clienteTelefone").value.trim();

    if (ids.length === 0 || valor <= 0 || !nome || !email || !cpf || !tel) {
      mostrarErro("Preencha todos os campos e selecione ao menos um produto.");
      return;
    }

    const btn = document.getElementById("btnGerar");
    btn.disabled = true;
    btn.textContent = "Gerando PIX...";
    document.getElementById("msgErro").style.display = "none";

    const fd = new FormData();
    ids.forEach(id => fd.append("planos[]", id));
    fd.append("valor_final", valor.toFixed(2));
    fd.append("nome",      nome);
    fd.append("email",     email);
    fd.append("cpf",       cpf);
    fd.append("telefone",  tel);

    fetch("<?= url("atendente/criar-venda") ?>", { method:"POST", body: fd })
      .then(r => r.json())
      .then(res => {
        btn.disabled = false;
        btn.textContent = "Gerar Link de Pagamento PIX";
        if (res.erro) {
          mostrarErro(res.msg);
        } else {
          abrirModal(res.link_pagamento);
        }
      })
      .catch(() => {
        btn.disabled = false;
        btn.textContent = "Gerar Link de Pagamento PIX";
        mostrarErro("Erro de comunicação. Tente novamente.");
      });
  }

  function abrirModal(link) {
    document.getElementById("linkResultado").textContent = link;
    document.getElementById("abrirLink").href = link;
    document.getElementById("modalResultado").classList.add("aberto");
  }

  function fecharModal() {
    document.getElementById("modalResultado").classList.remove("aberto");
  }

  function copiarLink() {
    const txt = document.getElementById("linkResultado").textContent;
    navigator.clipboard.writeText(txt).then(() => alert("Link copiado!"));
  }

  function mostrarErro(msg) {
    const el = document.getElementById("msgErro");
    el.textContent = msg;
    el.style.display = "";
  }
</script>
<?= $v->end(); ?>

<?php
$status = [
    0 => "Pendente",
    1 => "Aprovado",
    2 => "Recusado",
    3 => "Processando",
    4 => "Concluído"
];
$canChange = in_array(@$_SESSION["tipo"], [1, 2, 4, 5]);
?>
<?php $v->layout("_theme"); ?>
<?php $v->start("styles"); ?>
<style>
  /* Visual mais clean para a tabela de reembolsos */
  .reb-table {
    border-collapse: separate;
    border-spacing: 0 6px;
  }
  .reb-table thead th {
    background: #fff9e6;
    border: none;
    color: #5f4b00;
    font-weight: 700;
  }
  .reb-table tbody tr {
    background: #ffffff;
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    transition: transform 0.12s ease, box-shadow 0.12s ease;
  }
  .reb-table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
    background: #fffdf5;
  }
  .reb-table td, .reb-table th {
    border: none !important;
    vertical-align: middle;
  }
  .reb-chip {
    padding: 4px 10px;
    border-radius: 999px;
    font-weight: 600;
    display: inline-block;
  }
  .reb-chip.pending { background:#fff3d4; color:#b57700; }
  .reb-chip.success { background:#e6f7ef; color:#0f8b4c; }
  .reb-chip.neutral { background:#eef2f7; color:#445; }
</style>
<?php $v->end(); ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><?= $title ?></h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Solicitações de Reembolso / Chargeback</h3>
      </div>

      <?php if ($isAdmin): ?>
      <div class="card-body">
        <form class="form-inline mb-2" method="get" action="<?= url('reembolsos') ?>">
          <div class="form-group mr-2">
            <label class="mr-1">Produtor</label>
            <input type="number" name="produtor" class="form-control form-control-sm" value="<?= @$query['produtor'] ?>">
          </div>
          <div class="form-group mr-2">
            <label class="mr-1">Afiliado</label>
            <input type="number" name="afiliado" class="form-control form-control-sm" value="<?= @$query['afiliado'] ?>">
          </div>
          <div class="form-group mr-2">
            <label class="mr-1">Comprador</label>
            <input type="number" name="comprador" class="form-control form-control-sm" value="<?= @$query['comprador'] ?>">
          </div>
          <div class="form-group mr-2">
            <label class="mr-1">Produto</label>
            <input type="number" name="produto" class="form-control form-control-sm" value="<?= @$query['produto'] ?>">
          </div>
          <div class="form-group mr-2">
            <label class="mr-1">Status</label>
            <select name="status" class="form-control form-control-sm">
              <option value="">Todos</option>
              <?php foreach ($status as $k => $txt): ?>
                <option value="<?= $k ?>" <?= (isset($query['status']) && $query['status'] !== '' && $query['status'] == $k) ? 'selected' : '' ?>><?= $txt ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="btn btn-sm btn-primary mr-2" type="submit">Filtrar</button>
          <a class="btn btn-sm btn-secondary mr-2" href="<?= url('reembolsos') ?>">Limpar</a>
          <a class="btn btn-sm btn-success" href="<?= url('reembolsos/export?' . http_build_query($query)) ?>">Exportar XLS</a>
        </form>
      </div>
      <?php endif; ?>

      <div class="card-body table-responsive p-0">
        <table class="table reb-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Valor</th>
              <th>Status</th>
              <th>Produtor</th>
              <th>Afiliado</th>
              <th>Comprador</th>
              <th>Criado em</th>
              <th>Ver</th>
              <?php if ($canChange): ?><th>Ações</th><?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php if ($reembolsos) : foreach ($reembolsos as $r) : ?>
              <tr data-id="<?= $r->id ?>">
                <td>#<?= $r->id ?></td>
                <td>R$ <?= number_format($r->valor_total, 2, ",", ".") ?></td>
                <td>
                  <?php
                    $chipClass = $r->status == 0 ? 'pending' : ($r->status == 4 ? 'success' : 'neutral');
                  ?>
                  <span class="reb-chip <?= $chipClass ?> status-pill"><?= $status[$r->status] ?? 'N/D' ?></span>
                </td>
                <td><?= $r->produtor_nome ?? $r->produtor_id ?></td>
                <td><?= $r->afiliado_nome ?? $r->afiliado_id ?></td>
                <td><?= $r->comprador_nome ?? $r->comprador_id ?></td>
                <td><?= date("d/m/Y H:i", strtotime($r->criado_em)) ?></td>
                <td><button class="btn btn-xs btn-info btn-detalhes">Ver</button></td>
                <?php if ($canChange): ?>
                  <td style="min-width:180px;">
                    <select class="form-control form-control-sm status-select" style="width:130px; display:inline-block;">
                      <?php foreach ($status as $k => $txt): ?>
                        <?php if ($k == 4) continue; // concluído só via botão ?>
                        <option value="<?= $k ?>" <?= $k == $r->status ? 'selected' : '' ?>><?= $txt ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn-xs btn-primary btn-status-save">Salvar</button>
                    <button class="btn btn-xs btn-danger btn-estornar" <?= $r->status == 4 ? 'disabled' : '' ?>>Solicitar reembolso</button>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; else : ?>
              <tr><td colspan="7">Nenhuma solicitação encontrada.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <?php if ($paginas > 1): ?>
    <div class="card">
      <div class="card-body">
        <?php
          $uri = $_GET;
          unset($uri['page']);
          $uri = http_build_query($uri);
          $paginador->paginas($paginas, $pagina, url('reembolsos?page='), $uri);
        ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalhes do Reembolso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="detalhes-conteudo">Carregando...</div>
      </div>
    </div>
  </div>
</div>

<?php $v->start("scripts"); ?>
<script>
document.querySelectorAll(".btn-status-save").forEach(btn => {
  btn.addEventListener("click", () => {
    const tr = btn.closest("tr");
    const id = tr.dataset.id;
    const select = tr.querySelector(".status-select");
    const val = select.value;
    btn.disabled = true;
    btn.textContent = "Salvando...";
    fetch("<?= url('reembolsos/status') ?>", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(val)}`
    })
    .then(r => r.json())
    .then(resp => {
      if (resp && resp.ok) {
        const map = {
          0: "Pendente",
          1: "Aprovado",
          2: "Recusado",
          3: "Processando",
          4: "Concluído"
        };
        const pill = tr.querySelector(".status-pill");
        pill.textContent = map[val] || "N/D";
        pill.className = "badge status-pill " + (val == 0 ? "badge-warning" : (val == 4 ? "badge-success" : "badge-secondary"));
      } else {
        alert(resp?.erro || "Erro ao salvar status");
      }
    })
    .catch(() => alert("Erro ao salvar status"))
    .finally(() => {
      btn.disabled = false;
      btn.textContent = "Salvar";
    });
  });
});

// Detalhes
document.querySelectorAll(".btn-detalhes").forEach(btn => {
  btn.addEventListener("click", () => {
    const tr = btn.closest("tr");
    const id = tr.dataset.id;
    const box = document.getElementById("detalhes-conteudo");
    box.innerHTML = "Carregando...";
    $("#modalDetalhes").modal("show");
    fetch(`<?= url('reembolsos/detalhe') ?>?id=${id}`)
      .then(r => r.text())
      .then(html => box.innerHTML = html)
      .catch(() => box.innerHTML = "Erro ao carregar detalhes");
  });
});

// Estornar PagarMe
document.querySelectorAll(".btn-estornar").forEach(btn => {
  btn.addEventListener("click", () => {
    const tr = btn.closest("tr");
    const id = tr.dataset.id;
    if (!confirm("Confirmar solicitação de reembolso/estorno?")) return;
    btn.disabled = true;
    btn.textContent = "Enviando...";
    fetch("<?= url('reembolsos/estornar') ?>", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${encodeURIComponent(id)}`
    })
    .then(r => r.json())
    .then(resp => {
      if (resp && resp.ok) {
        // status concluído
        const pill = tr.querySelector(".status-pill");
        pill.textContent = "Concluído";
        pill.className = "reb-chip success status-pill";
        tr.querySelector(".btn-estornar").disabled = true;
        const select = tr.querySelector(".status-select");
        if (select) select.disabled = true;
        const saveBtn = tr.querySelector(".btn-status-save");
        if (saveBtn) saveBtn.disabled = true;
      } else {
        alert(resp?.erro || "Erro ao estornar");
        btn.disabled = false;
        btn.textContent = "Solicitar reembolso";
      }
    })
    .catch(() => {
      alert("Erro ao estornar");
      btn.disabled = false;
      btn.textContent = "Solicitar reembolso";
    });
  });
});
</script>
<?php $v->end(); ?>

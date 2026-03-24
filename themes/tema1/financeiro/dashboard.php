<?php
$v->layout("_theme");

$statusTransfer = [
    "pending_transfer" => ["label" => "Pendente",    "class" => "warning"],
    "transferred"      => ["label" => "Transferido", "class" => "success"],
    "failed"           => ["label" => "Falhou",      "class" => "danger"],
    "processing"       => ["label" => "Processando", "class" => "info"],
    "canceled"         => ["label" => "Cancelado",   "class" => "secondary"],
    "created"          => ["label" => "Criado",      "class" => "info"],
];

$statusSaque = [
    1 => ["label" => "Pendente",   "class" => "warning"],
    2 => ["label" => "Aprovado",   "class" => "success"],
    3 => ["label" => "Reprovado",  "class" => "danger"],
    4 => ["label" => "Bloqueado",  "class" => "secondary"],
];

function fmt($v) { return "R$ " . number_format((float)$v, 2, ",", "."); }
function fmtDelta($v) {
    $cls = $v >= 0 ? "success" : "danger";
    $icon = $v >= 0 ? "arrow_upward" : "arrow_downward";
    return "<span class='text-$cls'><span class='material-symbols-rounded' style='font-size:14px;vertical-align:middle'>$icon</span> " . fmt($v) . "</span>";
}
?>

<style>
.fin-card {
    background: linear-gradient(145deg, #1e1e1e, #161616);
    border: 1px solid rgba(255,221,0,.2);
    border-radius: 16px;
    padding: 22px 24px;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.fin-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.5); }
.fin-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--card-color, #FFDD00);
}
.fin-card .card-icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: rgba(255,255,255,.07);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
    color: var(--card-color, #FFDD00);
    font-size: 26px;
}
.fin-card .card-label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
.fin-card .card-value { font-size: 26px; font-weight: 700; color: #fff; }
.fin-card .card-sub   { font-size: 12px; color: #666; margin-top: 6px; }

.fin-section { margin-bottom: 32px; }
.fin-section-title {
    font-size: 14px; font-weight: 600; text-transform: uppercase;
    letter-spacing: 1px; color: #FFDD00; margin-bottom: 16px;
    padding-bottom: 8px; border-bottom: 1px solid rgba(255,221,0,.15);
}

.fin-table th { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: .6px; border-bottom: 1px solid #2a2a2a !important; }
.fin-table td { border-color: #1e1e1e !important; font-size: 14px; color: #ccc; vertical-align: middle; }
.fin-table tr:hover td { background: rgba(255,221,0,.04); }

.filter-bar { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; }
.filter-bar label { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: .6px; display: block; margin-bottom: 4px; }
.filter-bar select, .filter-bar input {
    background: #0f0f0f; border: 1px solid #2a2a2a; color: #fff;
    border-radius: 8px; padding: 8px 12px; font-size: 13px;
}
.filter-bar select:focus, .filter-bar input:focus { outline: none; border-color: #FFDD00; }
.filter-bar select option { background: #111; }

.chart-wrap {
    background: #1a1a1a; border: 1px solid #222; border-radius: 16px; padding: 20px;
}

.badge-status { font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 500; }

.progress-compare { height: 8px; border-radius: 4px; background: #2a2a2a; overflow: hidden; margin-top: 6px; }
.progress-compare .bar { height: 100%; border-radius: 4px; background: #FFDD00; transition: width .6s ease; }
</style>

<div class="content-wrapper">
    <br>
    <section class="content">
        <div class="container-fluid">

            <!-- Cabeçalho -->
            <div class="d-flex align-items-center justify-content-between mb-4" style="flex-wrap:wrap;gap:12px">
                <div>
                    <h4 style="color:#FFDD00;font-weight:700;margin:0">
                        <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:6px">account_balance</span>
                        Dashboard Financeiro
                    </h4>
                    <span style="color:#666;font-size:13px">Consolidado de comissões e saques</span>
                </div>
                <div style="color:#555;font-size:12px">Atualizado em <?= date("d/m/Y H:i") ?></div>
            </div>

            <!-- Filtros -->
            <div class="filter-bar">
                <form method="GET" class="d-flex align-items-end" style="gap:16px;flex-wrap:wrap">
                    <div>
                        <label>De</label>
                        <input type="date" name="data_ini" value="<?= htmlspecialchars($data_ini) ?>">
                    </div>
                    <div>
                        <label>Até</label>
                        <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
                    </div>
                    <div style="padding-bottom:2px">
                        <button type="submit" style="background:#FFDD00;color:#111;border:none;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer">
                            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;margin-right:4px">filter_alt</span>Filtrar
                        </button>
                    </div>
                    <div style="padding-bottom:2px">
                        <a href="<?= url('financeiro') ?>" style="background:#1e1e1e;color:#888;border:1px solid #2a2a2a;border-radius:8px;padding:9px 16px;font-size:13px;text-decoration:none;display:inline-block">
                            Limpar
                        </a>
                    </div>
                    <div style="color:#555;font-size:12px;padding-bottom:6px;align-self:flex-end">
                        <?= date("d/m/Y", strtotime($data_ini)) ?> &rarr; <?= date("d/m/Y", strtotime($data_fim)) ?>
                    </div>
                </form>
            </div>

            <!-- Cards KPI -->
            <div class="fin-section">
                <div class="fin-section-title">Período selecionado</div>
                <div class="row" style="gap:0">

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="fin-card" style="--card-color:#FFDD00">
                            <div class="card-icon"><span class="material-symbols-rounded">trending_up</span></div>
                            <div class="card-label">Comissões Ganhas</div>
                            <div class="card-value"><?= fmt($comissoesPeriodo) ?></div>
                            <div class="card-sub">no período filtrado</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="fin-card" style="--card-color:#4ade80">
                            <div class="card-icon"><span class="material-symbols-rounded">payments</span></div>
                            <div class="card-label">Saques Realizados</div>
                            <div class="card-value"><?= fmt($saquesPeriodo) ?></div>
                            <div class="card-sub">transferido para conta</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <?php $delta = $comissoesPeriodo - $saquesPeriodo; ?>
                        <div class="fin-card" style="--card-color:<?= $delta >= 0 ? '#60a5fa' : '#f87171' ?>">
                            <div class="card-icon"><span class="material-symbols-rounded">balance</span></div>
                            <div class="card-label">Diferença (Ganho − Saque)</div>
                            <div class="card-value" style="color:<?= $delta >= 0 ? '#60a5fa' : '#f87171' ?>"><?= fmt($delta) ?></div>
                            <div class="card-sub"><?= $delta >= 0 ? "a receber ainda" : "sacou mais que ganhou" ?></div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="fin-card" style="--card-color:#a78bfa">
                            <div class="card-icon"><span class="material-symbols-rounded">fast_forward</span></div>
                            <div class="card-label">Antecipações</div>
                            <div class="card-value"><?= fmt($antecipacoes) ?></div>
                            <div class="card-sub">antecipado no período</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Saldo wallet + Histórico geral -->
            <div class="fin-section">
                <div class="fin-section-title">Saldo Wallet & Acumulado</div>
                <div class="row">

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="fin-card" style="--card-color:#34d399">
                            <div class="card-icon"><span class="material-symbols-rounded">account_balance_wallet</span></div>
                            <div class="card-label">Saldo Disponível</div>
                            <div class="card-value"><?= fmt($saldoDisponivel) ?></div>
                            <div class="card-sub">pronto para sacar</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="fin-card" style="--card-color:#fbbf24">
                            <div class="card-icon"><span class="material-symbols-rounded">hourglass_top</span></div>
                            <div class="card-label">Aguardando Liberação</div>
                            <div class="card-value"><?= fmt($saldoAguardando) ?></div>
                            <div class="card-sub">a liberar em breve</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="fin-card" style="--card-color:#FFDD00">
                            <div class="card-icon"><span class="material-symbols-rounded">emoji_events</span></div>
                            <div class="card-label">Total Comissões (histórico)</div>
                            <div class="card-value"><?= fmt($comissoesTotal) ?></div>
                            <div class="card-sub">desde o início</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="fin-card" style="--card-color:#4ade80">
                            <div class="card-icon"><span class="material-symbols-rounded">swap_horiz</span></div>
                            <div class="card-label">Total Saques (histórico)</div>
                            <div class="card-value"><?= fmt($saquesTotal) ?></div>
                            <div class="card-sub">tudo que já sacou</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Gráfico 12 meses -->
            <div class="fin-section">
                <div class="fin-section-title">Evolução — últimos 12 meses</div>
                <div class="chart-wrap">
                    <canvas id="chartFinanceiro" height="100"></canvas>
                </div>
            </div>

            <!-- Comparativo mensal no ano -->
            <div class="fin-section">
                <div class="fin-section-title">Comparativo mensal — <?= date("d/m/Y", strtotime($data_ini)) ?> até <?= date("d/m/Y", strtotime($data_fim)) ?></div>
                <?php if (empty($comparativo)): ?>
                    <div style="color:#555;text-align:center;padding:30px">Nenhum movimento neste ano.</div>
                <?php else: ?>
                <div style="overflow-x:auto">
                    <table class="table fin-table" style="background:transparent">
                        <thead>
                            <tr>
                                <th>Mês</th>
                                <th>Comissões Ganhas</th>
                                <th>Saques Realizados</th>
                                <th>Diferença</th>
                                <th>% Sacado</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($comparativo as $row):
                            $pct = $row["ganho"] > 0 ? min(100, round($row["saque"] / $row["ganho"] * 100, 1)) : 0;
                        ?>
                            <tr>
                                <td style="color:#fff;font-weight:500"><?= ucfirst($row["mes"]) ?></td>
                                <td style="color:#FFDD00"><?= fmt($row["ganho"]) ?></td>
                                <td style="color:#4ade80"><?= fmt($row["saque"]) ?></td>
                                <td><?= fmtDelta($row["delta"]) ?></td>
                                <td>
                                    <span style="color:#888;font-size:12px"><?= $pct ?>%</span>
                                    <div class="progress-compare"><div class="bar" style="width:<?= $pct ?>%"></div></div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                        <tfoot>
                            <tr style="border-top:2px solid #2a2a2a">
                                <td style="color:#888">Total</td>
                                <td style="color:#FFDD00;font-weight:700"><?= fmt(array_sum(array_column($comparativo, "ganho"))) ?></td>
                                <td style="color:#4ade80;font-weight:700"><?= fmt(array_sum(array_column($comparativo, "saque"))) ?></td>
                                <td><?= fmtDelta(array_sum(array_column($comparativo, "delta"))) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif ?>
            </div>

            <!-- Últimos saques locais -->
            <?php if (!empty($saquesList)): ?>
            <div class="fin-section">
                <div class="fin-section-title">Saques no período</div>
                <div style="overflow-x:auto">
                    <table class="table fin-table" style="background:transparent">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($saquesList as $s): ?>
                            <tr>
                                <td><?= date("d/m/Y", strtotime($s->data_pedido)) ?></td>
                                <td><?= htmlspecialchars($s->descricao ?: "Saque") ?></td>
                                <td style="color:#4ade80"><?= fmt(abs($s->valor)) ?></td>
                                <td>
                                    <?php $st = $statusSaque[$s->status] ?? ["label"=>"—","class"=>"secondary"]; ?>
                                    <span class="badge badge-<?= $st["class"] ?> badge-status"><?= $st["label"] ?></span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif ?>

            <!-- Saques para conta bancária (transfers) -->
            <?php if (!empty($transferencias)): ?>
            <div class="fin-section">
                <div class="fin-section-title">Saques para conta bancária — Pagar.me</div>
                <div style="overflow-x:auto">
                    <table class="table fin-table" style="background:transparent">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Prev. Crédito</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($transferencias as $t):
                            $valor_abs = abs((float)($t->amount ?? 0)) / 100;
                            $data_op   = $t->created_at ?? null;
                            $tipo_op   = strtoupper($t->type ?? "transfer");
                            $status_op = $t->status ?? null;
                            $prev_cred = $t->funding_estimated_date ?? null;
                            $st_cfg    = $statusTransfer[$status_op] ?? ["label" => ucfirst((string)$status_op ?: "—"), "class" => "secondary"];
                        ?>
                            <tr>
                                <td><?= $data_op ? date("d/m/Y H:i", strtotime($data_op)) : "—" ?></td>
                                <td style="color:#4ade80"><?= fmt($valor_abs) ?></td>
                                <td style="color:#888;font-size:12px"><?= htmlspecialchars($tipo_op) ?></td>
                                <td><span class="badge badge-<?= $st_cfg["class"] ?> badge-status"><?= $st_cfg["label"] ?></span></td>
                                <td style="color:#888;font-size:12px"><?= $prev_cred ? date("d/m/Y", strtotime($prev_cred)) : "—" ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif ?>

            <!-- Liquidações / Depósitos creditados (settlements) -->
            <?php if (!empty($settlements)): ?>
            <div class="fin-section">
                <div class="fin-section-title">Histórico de Depósitos no período — Pagar.me</div>
                <div style="overflow-x:auto">
                    <table class="table fin-table" style="background:transparent">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Produto</th>
                                <th>Bandeira</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $totalSettlements = 0;
                        foreach ($settlements as $s):
                            $valor_s = abs((float)($s->amount ?? 0)) / 100;
                            $totalSettlements += $valor_s;
                            $data_s  = $s->payment_date ?? null;
                            $prod_s  = ucfirst(str_replace('_', ' ', $s->product ?? '—'));
                            $brand_s = strtoupper($s->card_brand ?? '—');
                            $st_s    = $s->status ?? '—';
                            $st_cls  = $st_s === 'success' ? 'success' : 'secondary';
                        ?>
                            <tr>
                                <td><?= $data_s ? date("d/m/Y", strtotime($data_s)) : "—" ?></td>
                                <td style="color:#FFDD00"><?= fmt($valor_s) ?></td>
                                <td style="color:#888;font-size:12px"><?= htmlspecialchars($prod_s) ?></td>
                                <td style="color:#888;font-size:12px"><?= htmlspecialchars($brand_s) ?></td>
                                <td><span class="badge badge-<?= $st_cls ?> badge-status"><?= htmlspecialchars($st_s) ?></span></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                        <tfoot>
                            <tr style="border-top:2px solid #2a2a2a">
                                <td style="color:#888">Total</td>
                                <td style="color:#FFDD00;font-weight:700"><?= fmt($totalSettlements) ?></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endif ?>

        </div>
    </section>
</div>

<?php $v->start("scripts"); ?>
<script src="<?= url("themes/tema1/plugins/chart.js/Chart.bundle.min.js") ?>"></script>
<script>
(function () {
    const labels  = <?= json_encode($grafico["meses"]) ?>;
    const ganhos  = <?= json_encode($grafico["ganhos"]) ?>;
    const saques  = <?= json_encode($grafico["saques"]) ?>;

    const ctx = document.getElementById("chartFinanceiro").getContext("2d");
    new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Comissões Ganhas",
                    data: ganhos,
                    backgroundColor: "rgba(255,221,0,.75)",
                    borderColor: "#FFDD00",
                    borderWidth: 1,
                    borderRadius: 6,
                },
                {
                    label: "Saques Realizados",
                    data: saques,
                    backgroundColor: "rgba(74,222,128,.65)",
                    borderColor: "#4ade80",
                    borderWidth: 1,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: "#aaa", font: { size: 12 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => " R$ " + ctx.parsed.y.toLocaleString("pt-BR", { minimumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                x: { ticks: { color: "#666" }, grid: { color: "rgba(255,255,255,.04)" } },
                y: {
                    ticks: {
                        color: "#666",
                        callback: v => "R$ " + v.toLocaleString("pt-BR", { minimumFractionDigits: 2 })
                    },
                    grid: { color: "rgba(255,255,255,.06)" }
                }
            }
        }
    });
})();
</script>
<?php $v->end(); ?>

<?php
  $v->layout("_theme");
  if($_SESSION["tipo"] == 6){
    header("Location: " . url("vendas"));
  }
?>

<div class="content-wrapper win-page">
  <section class="content">
    <div class="container-fluid">

      <header class="win-page-header">
        <div class="win-page-title">
          <small>Visão geral</small>
          <h1>Dashboard</h1>
        </div>
        <div class="win-page-actions">
          <a href="<?= url("cursos") ?>" class="btn-win btn-win-ghost">
            <i class="fas fa-book-open"></i>
            Meus cursos
          </a>
          <a href="<?= url("comissao") ?>" class="btn-win">
            <i class="fas fa-wallet"></i>
            Saldos
          </a>
        </div>
      </header>

      <!-- Stats row 1: revenue -->
      <div class="win-stats-grid" style="margin-bottom:1.6rem;">
        <div class="win-stat-card">
          <span><i class="fas fa-sun" style="margin-right:.4rem;color:var(--wh-accent)"></i>Hoje</span>
          <strong><?= $hoje ? "R$ " . number_format($hoje, 2, ",", ".") : "R$ 0,00" ?></strong>
        </div>
        <div class="win-stat-card">
          <span><i class="fas fa-calendar-day" style="margin-right:.4rem;color:var(--wh-accent)"></i>Ontem</span>
          <strong><?= $ontem ? "R$ " . number_format($ontem, 2, ",", ".") : "R$ 0,00" ?></strong>
        </div>
        <div class="win-stat-card">
          <span><i class="fas fa-calendar-alt" style="margin-right:.4rem;color:var(--wh-accent)"></i>Este mês</span>
          <strong><?= $mes ? "R$ " . number_format($mes, 2, ",", ".") : "R$ 0,00" ?></strong>
        </div>
      </div>

      <!-- Stats row 2: balances -->
      <div class="win-stats-grid" style="margin-bottom:2rem;">
        <div class="win-stat-card">
          <span><i class="fas fa-check-circle" style="margin-right:.4rem;color:#4ade80"></i>Disponível</span>
          <strong style="color:#4ade80"><?= @$saldo->valor > 0 ? "R$ " . number_format($saldo->valor, 2, ",", ".") : "R$ 0,00" ?></strong>
          <a href="<?= url("comissao") ?>" class="win-stat-link">Solicitar saque <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="win-stat-card">
          <span><i class="fas fa-clock" style="margin-right:.4rem;color:#fb923c"></i>Pendente</span>
          <strong style="color:#fb923c"><?= $pendente ? "R$ " . number_format($pendente, 2, ",", ".") : "R$ 0,00" ?></strong>
          <a href="<?= url("antecipacao") ?>" class="win-stat-link">Antecipar <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="win-stat-card">
          <span><i class="fas fa-coins" style="margin-right:.4rem;color:var(--wh-accent)"></i>Total acumulado</span>
          <strong><?= "R$ " . number_format(@$saldo->valor + @$pendente, 2, ",", ".") ?></strong>
          <a href="<?= url("comissao") ?>" class="win-stat-link">Ver saldos <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>

      <!-- Chart section -->
      <section class="win-section">
        <div class="win-section-header">
          <h2>Comissões diárias</h2>
          <div style="display:flex;gap:1rem;align-items:center;font-size:.78rem;color:var(--wh-text-soft)">
            <span><span style="display:inline-block;width:10px;height:10px;background:#4ade80;border-radius:50%;margin-right:.3rem"></span>Mês atual</span>
            <span><span style="display:inline-block;width:10px;height:10px;background:rgba(76,144,203,.8);border-radius:50%;margin-right:.3rem"></span>Mês anterior</span>
          </div>
        </div>
        <div style="position:relative;height:200px">
          <canvas id="areaChart"></canvas>
        </div>
      </section>

      <!-- Sales summary -->
      <section class="win-section">
        <div class="win-section-header" style="margin-bottom:1rem">
          <h2>Resumo de vendas</h2>
          <span class="win-section-subtitle">Mês atual</span>
        </div>
        <div class="win-stats-grid" style="--wh-grid-min:160px">
          <div class="win-stat-card">
            <span>Vendas do mês</span>
            <strong><?= (int)$vendasAtual ?></strong>
          </div>
          <div class="win-stat-card">
            <span>Total faturado</span>
            <strong>
              <?php
                $soma = ($_SESSION["tipo"] != 5) ? ($vendasAtualSoma->soma ?? 0) : $mes;
                echo "R$ " . number_format($soma, 2, ",", ".");
              ?>
            </strong>
          </div>
          <div class="win-stat-card">
            <span><?= ($_SESSION["tipo"] != 5) ? "Ticket médio" : "Comissão média" ?></span>
            <strong>
              <?= $vendasAtual ? "R$ " . number_format($soma / $vendasAtual, 2, ",", ".") : "R$ 0,00" ?>
            </strong>
          </div>
        </div>

        <?php if($_SESSION["tipo"] == 1): ?>
          <div class="win-divider"></div>
          <div class="win-section-header" style="margin-bottom:1rem">
            <h2 style="font-size:1rem">Geral da plataforma</h2>
            <span class="win-section-subtitle">Vendas pagas</span>
          </div>
          <div class="win-stats-grid">
            <div class="win-stat-card">
              <span>Vendas totais</span>
              <strong><?= (int)$vendasTotal ?></strong>
            </div>
            <div class="win-stat-card">
              <span>Volume total</span>
              <?php $soma = $vendasTotalSoma->soma ?? 0; ?>
              <strong>R$ <?= number_format($soma, 2, ",", ".") ?></strong>
            </div>
            <div class="win-stat-card">
              <span>Ticket médio</span>
              <strong><?= $vendasTotal ? "R$ " . number_format($soma / $vendasTotal, 2, ",", ".") : "R$ 0,00" ?></strong>
            </div>
          </div>
        <?php endif; ?>
      </section>

    </div>
  </section>
</div>

<?= $v->start("scripts"); ?>
<script>
  (function() {
    <?php
      $dados = [];
      for ($i = 1; $i <= 31; $i++) $dados[] = $i;

      $valorDia = $diaComissao = $valorDiaAnterior = $diaComissaoAnterior = $dias = [];
      for ($j = 1; $j <= 31; $j++) $dias[$j] = $j;

      if ($comissaoAtual) {
        foreach ($comissaoAtual as $c) {
          $d = date("d", strtotime($c->dia));
          $valorDia[$d] = $c->valor;
          $diaComissao[$d] = $d;
        }
      }
      foreach ($dias as $d1) {
        $d1k = $d1 < 10 ? "0$d1" : (string)$d1;
        $comAtual[$d1k] = @$diaComissao[$d1k] ? $valorDia[$d1k] : 0;
      }

      if ($comissaoAnterior) {
        foreach ($comissaoAnterior as $c) {
          $d = date("d", strtotime($c->dia));
          $valorDiaAnterior[$d] = $c->valor;
          $diaComissaoAnterior[$d] = $d;
        }
      }
      foreach ($dias as $d2) {
        $d2k = $d2 < 10 ? "0$d2" : (string)$d2;
        $comAnterior[$d2k] = @$diaComissaoAnterior[$d2k] ? $valorDiaAnterior[$d2k] : 0;
      }
    ?>

    var labels = [<?= implode(',', $dados) ?>];
    var atual   = [<?= implode(',', $comAtual ?? array_fill(0, 31, 0)) ?>];
    var anterior = [<?= implode(',', $comAnterior ?? array_fill(0, 31, 0)) ?>];

    var ctx = document.getElementById('areaChart');
    if (!ctx) return;
    new Chart(ctx.getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Atual',
            data: atual,
            backgroundColor: 'rgba(74,222,128,.15)',
            borderColor: 'rgba(74,222,128,.7)',
            borderWidth: 2,
            pointRadius: 2,
            pointBorderWidth: 2,
            pointBorderColor: 'rgba(74,222,128,.9)',
            lineTension: 0.35,
            fill: true,
          },
          {
            label: 'Anterior',
            data: anterior,
            backgroundColor: 'rgba(76,144,203,.1)',
            borderColor: 'rgba(76,144,203,.55)',
            borderWidth: 2,
            pointRadius: 2,
            lineTension: 0.35,
            fill: true,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 800, easing: 'easeInOutQuart' },
        legend: { display: false },
        scales: {
          xAxes: [{ gridLines: { color: 'rgba(255,255,255,.04)', zeroLineColor: 'rgba(255,255,255,.04)' }, ticks: { fontColor: 'rgba(148,163,184,.6)', fontSize: 11 } }],
          yAxes: [{ gridLines: { color: 'rgba(255,255,255,.04)', zeroLineColor: 'rgba(255,255,255,.04)' }, ticks: { fontColor: 'rgba(148,163,184,.6)', fontSize: 11, callback: function(v){ return 'R$ ' + v; } } }]
        }
      }
    });
  })();
</script>
<?= $v->end(); ?>

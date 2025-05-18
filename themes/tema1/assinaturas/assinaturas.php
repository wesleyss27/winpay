<?php

$v->layout("_theme");

$i = 1;

$status_assinatura = array(
    1 => "ATIVA",
    2 => "CANCELADA",
    3 => "SUSPENSA"
);
$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);

$forma_pagamento = array(
    1 => "CARTÃO DE CRÉDITO",
    2 => "BOLETO",
    3 => "PIX"
);

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}

function diffDate($d1, $d2, $type = '', $sep = '-')
{
    $d1 = explode($sep, $d1);
    $d2 = explode($sep, $d2);
    // var_dump($d2);

    // MULTIPLICAR 60 SEGUNDOS POR 60 MINUTOS POR 24 HORAS
    // 60*60*24

    switch ($type) {
        case 'Mensal':
            $X = (60 * 60 * 24 * 30);
            break;
        case 'Semanal':
            $X = (60 * 60 * 24 * 7);
            break;
        case 'Bimestral':
            $X = (60 * 60 * 24 * 60);
            break;
        case 'Trimestral':
            $X = (60 * 60 * 24 * 90);
            break;
        case 'Semestral':
            $X = (60 * 60 * 24 * 182);
            break;
        case 'Anual':
            $X = (60 * 60 * 24 * 365);
            break;
        case 'Trienal':
            $X = (60 * 60 * 24 * 1095);
            break;
        default:
            $X = 1;
            break;
    }
    $dt = @mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) - mktime(0, 0, 0, $d1[1], $d1[2], $d1[0]);
    return floor($dt / $X);
}
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 style="font-family:inter"><?= $title ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>

                        <li class="breadcrumb-item active">Assinaturas</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">


    <?php
        use Carbon\Carbon;

        $mesAtual = date("Y-m");
        $mesAnterior = date("Y-m", strtotime("-1 month"));

        function extrairMes($data) {
            return date("Y-m", strtotime($data));
        }

        // Vendas e Faturamento mês atual e anterior
        $vendasMesAtual = array_filter($assinaturas, fn($a) => extrairMes($a->created_at) === $mesAtual);
        $vendasMesAnterior = array_filter($assinaturas, fn($a) => extrairMes($a->created_at) === $mesAnterior);

        $totalVendasAtual = count($vendasMesAtual);
        $totalVendasAnterior = count($vendasMesAnterior);
        $varVendas = $totalVendasAnterior > 0 ? (($totalVendasAtual - $totalVendasAnterior) / $totalVendasAnterior) * 100 : 0;

        $faturadoAtual = array_sum(array_map(fn($a) => $a->valor_plano, $vendasMesAtual));
        $faturadoAnterior = array_sum(array_map(fn($a) => $a->valor_plano, $vendasMesAnterior));
        $varFaturado = $faturadoAnterior > 0 ? (($faturadoAtual - $faturadoAnterior) / $faturadoAnterior) * 100 : 0;

        $previsaoProxMes = $faturadoAtual * 1.08;
        $varPrevisao = $faturadoAnterior > 0 ? (($previsaoProxMes - $faturadoAnterior) / $faturadoAnterior) * 100 : 0;

        // Vendas por plano por mês
        $porPlano = [];
        foreach ($assinaturas as $a) {
            $mes = extrairMes($a->created_at);
            $plano = $a->dataPlano()->nome ?? 'Desconhecido';
            if (!isset($porPlano[$mes])) $porPlano[$mes] = [];
            if (!isset($porPlano[$mes][$plano])) $porPlano[$mes][$plano] = 0;
            $porPlano[$mes][$plano] += 1;
        }

        // Faturamento por mês
        $porMesFaturamento = [];
        foreach ($assinaturas as $a) {
            $mes = extrairMes($a->created_at);
            if (!isset($porMesFaturamento[$mes])) $porMesFaturamento[$mes] = 0;
            $porMesFaturamento[$mes] += $a->valor_plano;
        }
        ksort($porMesFaturamento);
        $mesesEvolucao = array_map(fn($m) => date("M/Y", strtotime($m . "-01")), array_keys($porMesFaturamento));
        $valoresEvolucao = array_values($porMesFaturamento);

        // Previsão próxima
        $mediaCrescimento = 0;
        for ($i = 1; $i < count($valoresEvolucao); $i++) {
            if ($valoresEvolucao[$i - 1] > 0) {
                $mediaCrescimento += ($valoresEvolucao[$i] - $valoresEvolucao[$i - 1]) / $valoresEvolucao[$i - 1];
            }
        }
        $mediaCrescimento = $mediaCrescimento / max(1, count($valoresEvolucao) - 1);
        $previsaoValor = round(end($valoresEvolucao) * (1 + $mediaCrescimento));
        $mesesEvolucao[] = date("M/Y", strtotime("+1 month"));
        $valoresEvolucao[] = $previsaoValor;
        ?>
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
        <style>
            .dashboard-container {
                background: #121212;
                color: #fff;
                font-family: 'Inter', sans-serif;
                padding: 20px;
                border-radius: 12px;
                margin-bottom: 30px;
            }

            .kpi-card {
                background: #1E1E1E;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                max-width: 500px; /* NOVO: define largura máxima */
                width: 100%;
            }

            .kpi-card i {
                font-size: 30px;
                color: #FFD700;
                margin-right: 15px;
            }

            .kpi-value {
                font-size: 20px;
                font-weight: bold;
                margin-top: 5px;
            }

            .chart-container {
                background: #1E1E1E;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                flex: 1 1 48%; /* ocupa 48% da largura com flex grow e shrink */
                max-width: 48%;
                min-width: 320px;

            }

            .ranking-table {
                width: 100%;
                color: white;
                font-size: 14px;
            }

            .ranking-table th, .ranking-table td {
                padding: 8px;
            }

            .bar {
                height: 8px;
                background-color: yellow;
                border-radius: 5px;
            }

            .label-highlight {
                font-size: 14px;
                font-weight: bold;
                color: #E6E600;
            }

            .row {
                gap: 15px; /* apenas se quiser espaçamento horizontal entre colunas */
            }

            .full-width-chart {
                width: 100% !important;
                max-width: 100% !important;
                flex: 1 1 100% !important;
            }

            .responsive-chart {
                flex: 1 1 48%;
                min-width: 300px;
                margin-bottom: 20px;
            }

            @media (max-width: 768px) {
                .responsive-chart {
                    flex: 1 1 100%;
                    max-width: 100%;
                }
            }

        </style>

        <div class="dashboard-container">
            <div class="row text-left">
                <div class="col-md-4 kpi-card">
                    <i class="fas fa-shopping-cart"></i>
                    <div>
                        <div>Total de Vendas no Mês</div>
                        <div class="kpi-value"><?= $totalVendasAtual ?> assinaturas</div>
                        <div class="text-success">↑ <?= number_format($varVendas, 1) ?>% vs mês anterior</div>
                    </div>
                </div>
                <div class="col-md-4 kpi-card">
                    <i class="fas fa-dollar-sign"></i>
                    <div>
                        <div>Valor Faturado</div>
                        <div class="kpi-value">R$ <?= number_format($faturadoAtual, 2, ',', '.') ?></div>
                        <div class="text-success">↑ <?= number_format($varFaturado, 1) ?>% vs mês anterior</div>
                    </div>
                </div>
                <div class="col-md-4 kpi-card">
                    <i class="fas fa-chart-line"></i>
                    <div>
                        <div>Previsão Próximo Mês</div>
                        <div class="kpi-value">R$ <?= number_format($previsaoProxMes, 2, ',', '.') ?></div>
                        <div class="text-success">↑ <?= number_format($varPrevisao, 1) ?>% vs mês anterior</div>
                    </div>
                </div>
            </div>

            <div class="chart-container full-width-chart">
            <h5 class="label-highlight">Volume de Vendas por Plano</h5>
            <canvas id="graficoVendasPlano" height="100"></canvas>
        </div>


            <div class="d-flex flex-wrap gap-3 justify-content-between">

            <div class="chart-container responsive-chart">

                    <h5 class="label-highlight">Ranking de Planos</h5>
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Receita</th>
                                <th>Vendas</th>
                                <th>Participação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ranking = [];
                            $totalReceita = 0;
                            foreach ($assinaturas as $a) {
                                $nome = $a->dataPlano()->nome ?? 'Desconhecido';
                                $ranking[$nome]['receita'] = ($ranking[$nome]['receita'] ?? 0) + $a->valor_plano;
                                $ranking[$nome]['vendas'] = ($ranking[$nome]['vendas'] ?? 0) + 1;
                                $totalReceita += $a->valor_plano;
                            }
                            arsort($ranking);
                            foreach ($ranking as $nome => $info) {
                                $percent = round(($info['receita'] / $totalReceita) * 100, 1);
                                echo "<tr>
                                        <td>$nome</td>
                                        <td>R$ " . number_format($info['receita'], 2, ',', '.') . "</td>
                                        <td>{$info['vendas']}</td>
                                        <td><div class='bar' style='width: {$percent}%'></div><small> {$percent}%</small></td>
                                    </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="chart-container responsive-chart">


                    <h5 class="label-highlight">Evolução do Faturamento</h5>
                    <canvas id="graficoEvolucaoFaturamento" height="120"></canvas>
                </div>
            </div>
        </div>

        <form action="#" style="font-family:inter; font-size:10px">
            <div class="callout callout-primary">
                <div class="row">
                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter">Data de cadastro:</h6>
                        <input type="date" name="inicio" id="inicio" class="form-control" style="cursor:pointer;" value="<?= @$_GET["inicio"]; ?>">
                    </div>

                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter; color:#fff">|</h6>
                        <input type="date" name="fim" id="fim" class="form-control" style="cursor:pointer;" value="<?= @$_GET["fim"]; ?>">
                    </div>

                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter">Proxima Cobrança:</h6>
                        <input type="date" name="inicio_proxima" id="inicio_proxima" class="form-control" style="cursor:pointer;" value="<?= @$_GET["inicio_proxima"]; ?>">
                    </div>

                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter; color:#fff">|</h6>
                        <input type="date" name="fim_proxima" id="fim_proxima" class="form-control" style="cursor:pointer;" value="<?= @$_GET["fim_proxima"]; ?>">
                    </div>

                    <div class="col-12 col-md-4">
                        <h6 style="font-family:inter">Comprador:</h6>
                        <input type="text" name="comprador" id="cpf" class="form-control" placeholder="(nome ou CPF/CNPJ ou email)" value="<?= @$_GET["comprador"]; ?>">
                    </div>
                </div>
            </div>

            <div class="callout callout-primary">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <h6 style="font-family:inter">Código do pedido de origem:</h6>
                        <input type="text" name="venda" class="form-control" placeholder="ex.: 239" value="<?= @$_GET["venda"]; ?>">
                    </div>

                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter">Forma de Pagamento:</h6>

                        <select name="pagamento" id="pagamento" class="form-control">
                            <option value="">Pagamento...</option>
                            <option value="1" <?php if (@$_GET["pagamento"] == 1) echo "selected";
                                                echo "" ?>>Cartão de crédito</option>
                            <option value="2" <?php if (@$_GET["pagamento"] == 2) echo "selected";
                                                echo "" ?>>Boleto</option>
                            <option value="3" <?php if (@$_GET["pagamento"] == 3) echo "selected";
                                                echo "" ?>>PIX</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter">Status da assinatura:</h6>
                        <select name="status" id="status" class="form-control">
                            <option value="">Status...</option>
                            <?php foreach ($status_assinatura as $cod => $status) : ?>
                                <option value="<?= $cod ?>" <?php if (@$_GET["status"] == $cod) echo "selected";
                                                            echo "" ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter">Produto:</h6>

                        <select name="produto" id="produto" class="form-control">
                            <option value="">Produto...</option>
                            <?php foreach ($produtos as $produto) : ?>
                                <option value="<?= $produto->id ?>" <?= (@$_GET["produto"] == $produto->id) ? "selected" : "" ?>><?= $produto->nome ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <h6 style="font-family:inter; color:#fff">|</h6>
                        <button type="submit" name="" class="form-control form-control btn-primary"> <i class="fa fa-check"></i> Filtrar</button>
                    </div>
                </div>
            </div>
        </form>

        <style>
            .mobile-n {
                display: block;
            }

            .desktop-n {
                display: none;
            }

            .hr-mobile {
                border-bottom: 1px solid #eee;
                margin: 10px 0;
            }

            @media(max-width: 500px) {
                .mobile-n {
                    display: none;
                }

                .desktop-n {
                    display: block;
                }
            }
        </style>

        <?php if ($assinaturas) : ?>
            <!-- <div class="card"> -->
            <!-- <div class="card-header"> -->
            <div class="row">
                <div class="col-md-4 center">
                    <div class="card card-body">
                        Total de registros: <br>
                        <?= $count ?>
                    </div>
                </div>
                <div class="col-md-4 center">
                    <div class="card card-body">
                        Valor das assinaturas: <br>
                        R$ <?= number_format($assinaturasValor->valor, 2, ",", ".") ?>
                    </div>
                </div>

                <div class="col-md-4 center">
                    <div class="card card-body">
                        Valor médio: <br>
                        R$ <?= number_format($assinaturasValor->valor / $count, 2, ",", ".") ?>
                    </div>
                </div>
            </div>
            <!-- </div> -->

            <a class="btn btn-primary btn-sm" href="<?= url("assinaturas/extrato") ?>">Outras infomações</a>

            <!-- <div class="card-body" style="background: #f4f6f9"> -->
            <script>
                var dados = [];
            </script>
            <div style="overflow-x: auto;">

                <div class="mobile-n card card-body bg-azul-gradient" style="font-size:13px; font-family:inter">
                    <div class="row">
                        <!-- <div class="col">ID</div> -->
                        <div class="col">ID</div>
                        <div class="col">Venda Origem</div>
                        <div class="col">Produto</div>

                        <div class="col">Comprador</div>
                        <div class="col">Periodicidade</div>

                        <div class="col">Fim</div>
                        <div class="col">Valor Assinatura</div>
                        <div class="col">Status</div>
                        <div class="col">Editar/Excluir</div>
                    </div>
                </div>

                <?php foreach ($assinaturas as $assinatura) : ?>

                    <div class="card card-body efeito-slow">
                        <div class="row" style="font-family:inter; font-size:12px; margin: 0 15px">
                            <!-- <div class="col"><b><?= $plano->id; ?></b></div> -->
                            <div class="col">
                                <b>#<?= $assinatura->id; ?></b>
                            </div>
                            <div class="col">
                                <?= $assinatura->venda; ?><br>
                                <?= $assinatura->dataVenda()->transacao; ?>
                            </div>
                            <div class="col">
                                <a href="<?= url("produtos/detalhe/$assinatura->produto") ?>">
                                    <?= $assinatura->dataProduto()->nome; ?><br>
                                    <?= $assinatura->dataPlano()->nome; ?>
                                </a>
                            </div>

                            <!-- <div class="desktop-n hr-mobile col-md-12"></div> -->

                            <div class="col">
                                <?= @$assinatura->dataComprador()->nome; ?>
                            </div>

                            <div class="col"><?= $assinatura->recorrencia; ?></div>

                            <div class="col mobile-n">
                                <?= $assinatura->data_limite != "" ? formataData($assinatura->data_limite) : "Eterno" ?>
                            </div>
                            <div class="col">R$ <?= number_format($assinatura->valor_plano, 2, ",", ".") ?></div>
                            <div class="col"><?= @$status_assinatura[$assinatura->status] ?></div>
                            <?php if ($assinatura->produtor == $section->getId()) : ?>
                                <div class="col">
                                    <button class="btn btn-warning btn-sm" data-target="#modal-lg<?= $assinatura->id; ?>" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                </div>
                            <?php else : ?>
                                <div class="col"></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <script>
                        dados[<?= $i ?>] = {
                            id: "<?= $assinatura->id ?>",
                            checkout: "<?= $assinatura->checkout ?>",
                            plano: "<?= $assinatura->plano ?>",
                            ativar: "<?= $assinatura->ativar ?>",
                            boleto: "<?= $assinatura->boleto ?>",
                            headline: "<?= $assinatura->headline ?>",
                            subheadline: "<?= $assinatura->subheadline ?>",
                        }
                    </script>

                    <?php include "modal-assinaturas.php"; ?>
                <?php $i++;
                endforeach; ?>
            </div>
            <!-- </div> -->
            <!-- </div> -->

            <br>
        <?php
            //EXIBE A NAVEGAÇÃO DO PAGINADOR
            $link = URL_BASE . "/assinaturas/";

            //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
            $paginador->paginas($total, $pagina, $link);

        else :
        ?>
            <div class="card card-body">
                <p>Nenhuma assinatura encontrada</p>
            </div>
        <?php endif; ?>


    </section>

</div>

<?= $v->start("scripts") ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx1 = document.getElementById("graficoVendasPlano").getContext("2d");
        new Chart(ctx1, {
            type: "bar",
            data: {
                labels: <?= json_encode(array_keys($porPlano[$mesAtual] ?? [])) ?>,
                datasets: [
                    {
                        label: "Mês Atual",
                        data: <?= json_encode(array_values($porPlano[$mesAtual] ?? [])) ?>,
                        backgroundColor: "#FFFF66"
                    },
                    {
                        label: "Mês Anterior",
                        data: <?= json_encode(array_values($porPlano[$mesAnterior] ?? [])) ?>,
                        backgroundColor: "#999999"
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const ctx2 = document.getElementById("graficoEvolucaoFaturamento").getContext("2d");
        new Chart(ctx2, {
            type: "line",
            data: {
                labels: <?= json_encode($mesesEvolucao) ?>,
                datasets: [{
                    label: "Faturamento",
                    data: <?= json_encode($valoresEvolucao) ?>,
                    borderColor: "#FFD700",
                    backgroundColor: "rgba(255, 215, 0, 0.2)",
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: "#FFD700"
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>


<script>
    $("#preco").blur(function(e) {
        var valor = $("#preco").val();
        $("#preco").val(valor.replace(",", "."));
    });

    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Planos");
        $("#salvar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#editar").css("display", "none");
        cancelar();
        console.log($("#valor_desconto").val());
    });

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });

    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });

    function salvarEditar(salvar) {
        if (salvar == 1) {
            url = "<?= $router->route("produtos.upsellCadastro"); ?>";
        } else {
            url = "<?= $router->route("produtos.upsellEditar") ?>";
        }

        var headline = $("#headline").val();

        var form = $("form");

        if (headline == "") {
            alert("Preecha todos os dados necessários");
        } else {

            $.ajax({

                url: url,
                data: form.serialize(),
                type: "POST",
                //datatype: "json",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log("callback ", callback);
                    alert(callback);
                    location.reload();
                }

            });
        }
    }

    function editar(id, status) {
        $.ajax({
            url: "<?= $router->route("assinaturas.editar") ?>",
            data: {
                id: id,
                status: status
            },
            type: "POST",
            //datatype: "json",
            beforeSend: function() {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log("callback ", callback);
                alert(callback);
                location.reload();
            }
        });
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": ""
        });
        $("#checkout option").attr({
            "selected": false
        });
        $("#ativar option").attr({
            "selected": false
        });
        $("#boleto option").attr({
            "selected": false
        });
        $("#plano option").attr({
            "selected": false
        });
        $("#subheadline").attr({
            "value": ""
        });
    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse upsell?")) {
            $.ajax({
                url: "<?= $router->route("produtos.upsellExcluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
</script>
<?= $v->end() ?>
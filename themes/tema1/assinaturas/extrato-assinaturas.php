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

            <div class="card">
                <div class="card-header">
                    <a href="<?= url("themes/tema1/assinaturas/exportar.php") ?>" class="badge badge-success col-2" target="_blank">Exportar</a>

                    <div class="card-tools">
                        <button type="button" id="btn-collapse" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body" style="overflow-x: auto;" id="extrato">

                    <table class="table" style="font-size:13px; font-family:inter">
                        <thead>
                            <tr>
                                <!-- <div class="col">ID</div> -->
                                <th scope="col">ID</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Comprador</th>
                                <th scope="col">Status</th>
                                <th scope="col">Valor Assinatura</th>
                                <th scope="col">Parcelas Pagas</th>
                                <th scope="col">Total pago</th>
                                <th scope="col">Parcelas restantes</th>
                                <th scope="col">Total restantes</th>
                                <th scope="col">Total assinatura</th>
                                <th scope="col">Parcelas Antecipadas</th>
                                <th scope="col">Valor Antecipado</th>
                                <th scope="col">Periodicidade</th>
                                <th scope="col">Fim</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $celulas = "";
                            foreach ($assinaturas as $assinatura) :
                                $venda = $vendaObj->find("comprador = $assinatura->comprador AND produto = $assinatura->produto AND status_transacao = 4")->count();

                                $periodo = array(
                                    "Semanal"       => [7, "days"],
                                    "Mensal"        => [1, "month"],
                                    "Bimestral"     => [2, "months"],
                                    "Trimestral"    => [3, "months"],
                                    "Semestral"     => [6, "months"],
                                    "Anual"         => [1, "years"],
                                    "Trienal"       => [3, "years"]
                                );

                                if ($assinatura->data_limite)
                                    $falta = 1 + diffDate($assinatura->proxima_cobranca, $assinatura->data_limite, "$assinatura->recorrencia");
                                else
                                    $falta = 1;

                                $total_pago = $assinatura->valor_plano * $venda;
                                @$total_pago1 += $assinatura->valor_plano * $venda;
                                $total_restante = $assinatura->valor_plano * $falta;

                                if ($assinatura->status == 1)
                                    @$total_restante1 += $assinatura->valor_plano * $falta;

                                $total_assinatura = $total_pago + $total_restante;
                                @$total_assinatura1 += $total_pago + $total_restante;
                                @$total_antecipado1 += $assinatura->valor_antecipado - ($assinatura->valor_antecipado * 0.0549);
                            ?>

                                <tr>
                                    <td><b>#<?= $assinatura->id; ?></b></td>
                                    <td>
                                        <a href="<?= url("produtos/detalhe/$assinatura->produto") ?>">
                                            <?= $assinatura->dataProduto()->nome; ?><br>
                                            <?= $assinatura->dataPlano()->nome; ?>
                                        </a>
                                    </td>
                                    <td><?= @$assinatura->dataComprador()->nome; ?></td>
                                    <td><?= @$status_assinatura[$assinatura->status] ?>
                                    <td>R$ <?= number_format($assinatura->valor_plano, 2, ",", ".") ?>
                                    <td><?= $venda; ?></td>
                                    <td>R$ <?= number_format($total_pago, 2, ",", ".") ?></td>
                                    <td><?= $falta; ?></td>
                                    <td>R$ <?= number_format($total_restante, 2, ",", ".") ?></td>
                                    <td>R$ <?= number_format($total_assinatura, 2, ",", ".") ?></td>
                                    <td><?= $assinatura->parcelas_antecipadas; ?></td>
                                    <td><?= $assinatura->valor_antecipado; ?></td>
                                    <td><?= $assinatura->recorrencia; ?></td>
                                    <td><?= $assinatura->data_limite != "" ? formataData($assinatura->data_limite) : "Eterno" ?></td>
                                </tr>

                                <?php
                                $celulas .= "
                                <tr>
                                    <td><b># $assinatura->id </b></td>
                                    <td>
                                        {$assinatura->dataProduto()->nome} - 
                                        {$assinatura->dataPlano()->nome} 
                                    </td>
                                    <td> {$assinatura->dataComprador()->nome}</td>
                                    <td> {$status_assinatura[$assinatura->status]}</td>
                                    <td>R$ " . number_format($assinatura->valor_plano, 2, ",", ".") . "</td>
                                    <td>$venda</td>
                                    <td>R$ " . number_format($total_pago, 2, ",", ".") . "</td>
                                    <td>$falta</td>
                                    <td>R$ " . number_format($total_restante, 2, ",", ".") . "</td>
                                    <td>R$ " . number_format($total_assinatura, 2, ",", ".") . "</td>
                                    <td> $assinatura->parcelas_antecipadas </td>
                                    <td> $assinatura->valor_antecipado </td>
                                    <td> $assinatura->recorrencia </td>
                                    <td> " . ($assinatura->data_limite != "" ? formataData($assinatura->data_limite) : "Eterno") . "</td>
                                </tr>";
                                ?>
                            <?php $i++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-body" style="overflow-x: auto;" id="totais">
                <table class='table' style='font-size:13px; font-family:inter'>
                    <thead>
                        <tr>
                            <th scope='col'>Total pago</th>
                            <th scope='col'>Total restante</th>
                            <th scope='col'>Total assinaturas</th>
                            <th scope='col'>Total antecipado</th>
                            <th scope='col'>Débitos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>R$ <?= number_format($total_pago1, 2, ",", ".") ?></td>
                            <td>R$ <?= number_format($total_restante1, 2, ",", ".") ?></td>
                            <td>R$ <?= number_format($total_assinatura1, 2, ",", ".") ?></td>
                            <td>R$ <?= number_format($total_antecipado1, 2, ",", ".") ?></td>
                            <td>R$ <?= number_format($devendo->devendo, 2, ",", ".") ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- </div> -->

            <br>
        <?php else : ?>
            <div class="card card-body">
                <p>Nenhuma assinatura encontrada</p>
            </div>
        <?php endif; ?>

        <?php
        $tabela_totais = "
        <table class='table' style='font-size:13px; font-family:inter'>
        <thead>
            <tr>
                <th scope='col'>Total pago</th>
                <th scope='col'>Total restante</th>
                <th scope='col'>Total assinaturas</th>
                <th scope='col'>Total antecipado</th>
                <th scope='col'>Débitos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>R$ " . number_format($total_pago1, 2, ",", ".") . "</td>
                <td>R$ " . number_format($total_restante1, 2, ",", ".") . "</td>
                <td>R$ " . number_format($total_assinatura1, 2, ",", ".") . "</td>
                <td>R$ " . number_format($total_antecipado1, 2, ",", ".") . "</td>
                <td>R$ " . number_format($devendo->devendo, 2, ",", ".") . "</td>
            </tr>
        </tbody>
        </table>
        ";

        $tabela = "<table class='table' style='font-size:13px; font-family:inter'>
        <thead>
            <tr>
                <th scope='col'>ID</th>
                <th scope='col'>Produto</th>
                <th scope='col'>Comprador</th>
                <th scope='col'>Status</th>
                <th scope='col'>Valor Assinatura</th>
                <th scope='col'>Parcelas Pagas</th>
                <th scope='col'>Total pago</th>
                <th scope='col'>Parcelas restantes</th>
                <th scope='col'>Total restantes</th>
                <th scope='col'>Total assinatura</th>
                <th scope='col'>Parcelas Antecipadas</th>
                <th scope='col'>Valor Antecipado</th>
                <th scope='col'>Periodicidade</th>
                <th scope='col'>Fim</th>
            </tr>
        </thead>
        <tbody>$celulas</tbody>
        </table>
        ";

        $_SESSION["extrato_assinaturas"] = $tabela_totais . "<br><br>" . $tabela;
        ?>
    </section>

</div>

<?= $v->start("scripts") ?>
<script>
    // $("#btn-collapse").click(() => {
    //     $("#extrato").toggle();
    // })
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
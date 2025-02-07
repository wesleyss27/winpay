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
<?php 
    $v->layout("_theme");

    //if(strtotime(date("H:i")) >= '17:00') echo date("Y-m-d", strtotime("+3 days")); else echo date("Y-m-d", strtotime("+2 days"));

    if(@$saldo == "")
        $saldo = 0;

    $saqueMin = 50;
    $saqueMax = 300;
    $limiteSaque = $saqueMax - (-$saqueMes);
?>

    <?php if($saldo >= $saqueMin): ?>

        <?php if($usuario->tipo_doc == 1 && $limiteSaque >= $saqueMin && $limiteSaque < $saqueMax): ?>
            <?php $v->start("saque"); ?>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" id="cadastro">Solicitar Saque</button>
            <?php $v->end(); ?>
        <?php elseif($usuario->tipo_doc == 2): ?>
            <?php $v->start("saque"); ?>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" id="cadastro">Solicitar Saque</button>
            <?php $v->end(); ?>
        <?php endif; ?>
    <?php endif; ?>

<div class="">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <div class="tab">
        <div class="tab_content">
            <h4>Saldos</h4>
        </div>
    </div>

    <div class="principal bg-white">
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Hoje</th>
                    <th scope="col">Ontem</th>
                    <th scope="col">Esse Mês</th>
                    <th scope="col">Disponível</th>
                    <th scope="col">Pendente</th>
                    <th scope="col">Sacado no Mês</th>
                    <th scope="col">Total</th>
                </tr>
            </thead>

            <tbody>
                <tr class="plano">
                    <td><?= @$hoje ? "R$ " . number_format($hoje, 2, ",", "."): "R$ 0,00"; ?></td>
                    <td><?= @$ontem ? "R$ " . number_format($ontem, 2, ",", "."): "R$ 0,00"; ?></td>
                    <td><?= @$mes ? "R$ " . number_format($mes, 2, ",", "."): "R$ 0,00"; ?></td>
                    <td><?= @$saldo ? "R$ " . number_format($saldo, 2, ",", "."): "R$ 0,00"; ?></td>
                    <td><?= @$pendente ? "R$ " . number_format($pendente, 2, ",", "."): "R$ 0,00"; ?></td>
                    <td><?= @$saqueMes ? "R$ " . number_format($saqueMes, 2, ",", "."): "R$ 0,00"; ?></td>
                    <td><?= @$saldo ? "R$ " . number_format($saldo + $pendente, 2, ",", "."): "R$ 0,00"; ?></td>
                    
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="principal bg-white">
        <?php if(@$comissao): ?>
            <div style="overflow: auto">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <?php if($_SESSION["tipo"] == 1): ?>
                        <th scope="col">#</th>
                        <?php endif; ?>

                        <th scope="col">Valor</th>
                        <th scope="col">Descrição</th>

                        <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                        <th scope="col">Comissionado</th>
                        <?php endif; ?>

                        <th scope="col">Produto</th>
                        <th scope="col">Venda</th>
                        <th scope="col">Data</th>
                        <th scope="col">Disponível Em:</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($comissao as $comissao): 
                    /** CALCULA COMISSÃO MENOS A TAXA DE TRANSAÇÃO */
                    //$total = $comissao->valor - ($comissao->valor * $comissao->taxa / 100);
                ?>
                    <tr class="plano" id="plano<?= $comissao->id; ?>">
                        <?php if($_SESSION["tipo"] == 1): ?>
                        <th scope="row"><?= $comissao->id; ?></th>
                        <?php endif; ?>
                        <td>R$ <?= number_format($comissao->valor, 2, ",", "."); ?></td>
                        <td><?= $comissao->descricao; ?></td>

                        <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                        <td><?= @$comissao->dataUsuario()->nome . "<br>" . @$comissao->dataUsuario()->email; ?></td>
                        <?php endif; ?>

                        <td><?= (@$comissao->produto ? $comissao->dataProduto()->nome : " - ") ?></td>
                        <td><?= (@$comissao->venda ? $comissao->dataVenda()->transacao : "<a href='".url("saques")."'>saque</a>") ?></td>
                        <td><?= formataData(@$comissao->data_pedido); ?></td>
                        <td><?= formataData(@$comissao->tempo_saque); ?></td>
                    </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/comissao/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($paginas, $pagina, $link);
        
        else:
        ?>
        <h4>Você ainda não possui comissão!</h4>
        <?php
        endif;
        ?>
        </div>
    </div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Solicitar Saque</h4>
        </div>
        <form class="form_ajax cadastro" action="#" method="post">
        <div class="modal-body">
            <input type="hidden" name="usuario" value="<?= $_SESSION['id'] ?>">
            <input type="hidden" name="taxa">
            <input type="hidden" name="tipo" value="2">
            <input type="hidden" name="disponivel" value="0">

            <input type="hidden" name="tipo_doc" value="<?= $usuario->tipo_doc ?>">
            <input type="hidden" name="saqueMin" value="<?= $saqueMin ?>">
            <input type="hidden" name="saqueMax" value="<?= $saqueMax ?>">
            <input type="hidden" name="saqueMes" value="<?= $saqueMes ?>">

            <label>Valor disponível:</label> <?= @$saldo ? "R$ " . number_format($saldo, 2, ",", "."): "R$ 0,00"; ?><br>

            <input type="hidden" name="saldo" value="<?= @$saldo ?>">

            <label>Valor:</label>
            <input type="text" name="valor" id="valor" placeholder="Valor Sacado" class="form-control">

            <label>Conta Bancária</label>
            <select name="conta" id="conta" class="form-control">
                <?php foreach($bancos as $banco): ?>
                <option value="<?= $banco->id?>"><?= "$banco->banco - $banco->agencia $banco->conta - $banco->titular" ?></option>
                <?php endforeach; ?>
            </select>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="salvar">Solicitar</button>
        </div>

        </form>
    </div>

  </div>
</div>

<script>
    $("#valor").blur(function(e) {
        var valor = $("#valor").val();
        $("#valor").val(valor.replace(",", "."));

        var tipo_doc = <?= $usuario->tipo_doc ?>;
        var saqueMin = <?= @$saqueMin; ?>;
        var saqueMax = <?= @$saqueMax; ?>;
        var saqueMes = <?= @$saqueMes; ?>;

        if($("#valor").val() > <?= @$saldo; ?>){
            alert("Valor maior que o disponível!");
            $("#valor").val("<?= @$saldo; ?>");
        } else if($("#valor").val() < saqueMin) {
            alert("Valor menor que o saque mínimo");
            $("#valor").val(saqueMin);
        }

        var limiteSaque = saqueMax - (-saqueMes);
        console.log("soma", limiteSaque);
        if(tipo_doc == 1 && (limiteSaque < saqueMin || limiteSaque > saqueMax)) {
            alert("Você atingiu o limite mensal de saque");
            $("#salvar").attr("disabled", true);
        }
    });

    $("#salvar").click(function(e) {
        e.preventDefault();
        if($("#valor").val() != ""){
            salvarEditar(1);
        }
    });

    function salvarEditar(salvar) {
        if(salvar == 1) {
            url = "<?= $router->route("appcomissao.sacar"); ?>";
        } else {
            url = "<?=$router->route("appcomissao.saque")?>";
        }

        var form = $("form");
        $.ajax({

            url: url,
            data: form.serialize(),
            type: "POST",
            //datatype: "json",
            beforeSend: function () {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log("callback ", callback);
                alert(callback);
                location.reload();
            }

        });
    }
</script>
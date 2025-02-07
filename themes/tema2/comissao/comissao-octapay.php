<?php 
    $v->layout("_theme");

    //if(strtotime(date("H:i")) >= '17:00') echo date("Y-m-d", strtotime("+3 days")); else echo date("Y-m-d", strtotime("+2 days"));

    if(@$saldo == "")
        $saldo = 0;

    if($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2){
        header("Location: " . URL_BASE);
    }
?>

<div class="">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <div class="tab">
        <div class="tab_content">
            <h4>Saldo OctaPay</h4>
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
                    <td><?= @$saldo ? "R$ " . number_format($saldo + $pendente, 2, ",", "."): "R$ 0,00"; ?></td>
                    
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="principal bg-white">
        <?php if(@$comissao): ?>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <?php if($_SESSION["tipo"] == 1): ?>
                        <th scope="col">#</th>
                        <?php endif; ?>
                        <th scope="col">Valor</th>
                        <th scope="col">Saldo</th>
                        <th scope="col">Comissionado</th>
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
                        <td>R$ <?= number_format($comissao->saldo, 2, ",", "."); ?></td>
                        <td><?= @$comissao->dataUsuario()->nome . "<br>" . @$comissao->dataUsuario()->email; ?></td>
                        <td><?= (@$comissao->produto ? $comissao->dataProduto()->nome : " - ") ?></td>
                        <td><?= (@$comissao->venda ? $comissao->dataVenda()->transacao : " - ") ?></td>
                        <td><?= formataData(@$comissao->data_pedido); ?></td>
                        <td><?= formataData(@$comissao->tempo_saque); ?></td>
                    </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/comissao/octapay/";

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



<script>

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
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
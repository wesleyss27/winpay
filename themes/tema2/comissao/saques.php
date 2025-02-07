<?php 
    $v->layout("_theme");

    //if(strtotime(date("H:i")) >= '17:00') echo date("Y-m-d", strtotime("+3 days")); else echo date("Y-m-d", strtotime("+2 days"));

    if(@$saldo == "")
        $saldo = 0;

    $status = array(
        1 => "Pendente",
        2 => "Aprovada",
        3 => "Reprovada",
        4 => "Bloqueada"
    )
?>

    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <div class="tab">
        <div class="tab_content">
            <h4>Saques</h4>
        </div>
    </div>
    
    <div class="principal bg-white">
        <?php if(@$comissao): ?>
            <script>var dados = [];</script>
            <div style="overflow: auto">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <?php if($_SESSION["tipo"] == 1): ?>
                        <th scope="col">#</th>
                        <?php endif; ?>
                        <th scope="col">Valor</th>
                        <th scope="col">Comissionado</th>
                        <th scope="col">Conta</th>
                        <th scope="col">Status</th>
                        <th scope="col">Código</th>
                        <th scope="col">Data</th>
                        <th scope="col">Disponível Em:</th>
                        <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2): ?>
                        <th scope="col"></th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($comissao as $comissao): 
                    /** CALCULA COMISSÃO MENOS A TAXA DE TRANSAÇÃO */
                    $total = $comissao->valor - ($comissao->valor * $comissao->taxa / 100);
                ?>
                    <tr class="plano" id="plano<?= $comissao->id; ?>">
                        <?php if($_SESSION["tipo"] == 1): ?>
                        <th scope="row"><?= $comissao->id; ?></th>
                        <?php endif; ?>
                        <td>R$ <?= number_format($total, 2, ",", "."); ?></td>
                        <td><?= @$comissao->dataUsuario()->nome . "<br>" . @$comissao->dataUsuario()->email; ?></td>
                        <td>
                            <?= "{$comissao->dataConta()->banco} {$comissao->dataConta()->agencia} 
                                {$comissao->dataConta()->conta}"; 
                            ?>
                        </td>
                        <td><?= $status[$comissao->status]; ?></td>
                        <td><?= (@$comissao->codigo ? $comissao->codigo : " - ") ?></td>
                        <td><?= formataData(@$comissao->data_pedido); ?></td>
                        <td><?= formataData(@$comissao->tempo_saque); ?></td>
                        <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2): ?>
                        <td><button class="btn btn-warning" data-toggle="modal" data-target="#myModal" onclick="editar(<?= $comissao->id ?>)">S</button></td>
                        <?php endif; ?>
                    </tr>

                    <script>
                        dados[<?= $comissao->id; ?>] = {
                            id : "<?= $comissao->id ?>",
                            titular : "<?= @$comissao->dataConta()->titular ?>",
                            email : "<?= @$comissao->dataUsuario()->email ?>",
                            cpf : "<?= @$comissao->dataConta()->cpf ?>",
                            banco : "<?= @$comissao->dataConta()->banco ?>",
                            agencia : "<?= @$comissao->dataConta()->agencia ?>",
                            conta: "<?= @$comissao->dataConta()->conta ?>",
                            tipo: "<?= @$comissao->dataConta()->tipo ?>",
                            status: "<?= $comissao->status ?>",
                            codigo: "<?= $comissao->codigo ?>",
                            observacao: "<?= $comissao->observacao ?>",
                        }
                        console.log(dados);
                    </script>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/saques/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($paginas, $pagina, $link);
        
        else:
        ?>
        <h4>Nenhuma solicitação de saque encontrada!</h4>
        <?php
        endif;
        ?>
    </div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Solicitação de saque</h4>
        </div>
        <form class="form_ajax cadastro" action="#" method="post">
        <div class="modal-body">
            <input type="hidden" name="id" id="id">
            
            <p><b>Titular</b> <span id="titular"></span> - <b>CPF/CNPJ</b> <span id="cpf"> <span id="email"></span></span></p>
            <p><b>Banco</b> <span id="banco"></span> - <b>Agência</b> <span id="agencia"></span> - <b>Conta</b> <span id="conta"></span> - <b>Tipo</b> <span id="tipo"></span></p>
            <p><b>Chave Pix</b> <span id="chave_pix"></span></p>

            <label>Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="1">Pendente</option>
                <option value="2">Aprovada</option>
                <option value="3">Reprovada</option>
                <option value="4">Bloqueada</option>
            </select>

            <label>Código de Transferência</label>
            <input type="text" name="codigo" id="codigo" class="form-control">

            <label>Observação</label>
            <textarea name="observacao" id="observacao" class="form-control"></textarea>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="salvar">Solicitar</button>
        </div>

        </form>
    </div>

  </div>
</div>

<script>
    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar("<?= $router->route("appcomissao.statusSaque"); ?>");
    });

    function salvarEditar(url) {
        var form = $("form");
        $.ajax({

            url: url,
            data: form.serialize(),
            type: "POST",
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

    function editar(i) {
        var id = dados[i]["id"];
        var titular = dados[i]["titular"];
        var email = dados[i]["email"];
        var cpf = dados[i]["cpf"];
        var banco = dados[i]["banco"];
        var agencia = dados[i]["agencia"];
        var conta = dados[i]["conta"];
        var tipo = dados[i]["tipo"];
        var chave_pix = dados[i]["chave_pix"];
        var status = dados[i]["status"];
        var codigo = dados[i]["codigo"];
        var observacao = dados[i]["observacao"];

        console.log(i, titular, banco);

        $("#id").attr({"value": id});
        $("#codigo").attr({"value": codigo});
        $("#observacao").prop({"value": observacao});
        $("#status option[value=" + status + "]").prop({"selected": true});
        $("#titular").html(titular);
        $("#email").html(email);
        $("#cpf").html(cpf);
        $("#banco").html(banco);
        $("#agencia").html(agencia);
        $("#conta").html(conta);
        $("#tipo").html(tipo);
        $("#chave_pix").html(chave_pix);
    }
</script>
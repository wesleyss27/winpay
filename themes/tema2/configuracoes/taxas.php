<?php 
    $v->layout("_theme"); 

    if($_SESSION["tipo"] != 1){
        header("Location: " . URL_BASE);
    }

    $ativo = array(
        0 => "NÃO",
        1 => "SIM"
    );
?>

<div class="principal bg-white">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <div class="row">
        <div class="col-md-12">
        <?php if($taxa): ?>
            <div style="overflow: auto;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Parcelamento</th>
                        <th scope="col">Taxa Saque</th>
                        <th scope="col">Taxa Iniciante</th>
                        <th scope="col">Taxa Bronze</th>
                        <th scope="col">Taxa Prata</th>
                        <th scope="col">Taxa Ouro</th>
                        <th scope="col">Última Alteração</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>

                <tbody>
                    <tr id="usuario<?= $taxa->id; ?>">
                        <td><?= $taxa->parcelamento; ?>%</td>
                        <td><?= $taxa->saque; ?>%</td>
                        <td><?= $taxa->taxa_iniciante ; ?>%</td>
                        <td><?= $taxa->taxa_bronze ; ?>%</td>
                        <td><?= $taxa->taxa_prata ; ?>%</td>
                        <td><?= $taxa->taxa_ouro; ?>%</td>
                        <td><?= $taxa->dataUsuario()->nome; ?></td>
                        <td><button class="btn btn-warning" data-target="#myModal" onclick="editar(1)" data-toggle="modal">Editar</a></button></td>
                    </tr>
                </tbody>
            </table>
            </div>

        <?php        
        else:
        ?>
        <h4>Não existem produtores cadastrados ou ativos!</h4>
        <?php
        endif;
        ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Configurações de Taxas</span></h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <input type="hidden" name="id" id="id">
      <div class="modal-body">

        <div class="row">
            <div class="col-md-3">
                <label>Taxa de parcelamento %</label>
                <input type="text" name="parcelamento" id='parcelamento' class="form-control input-lg">
            </div>

            <div class="col-md-3">
                <label>Taxa de saque %</label>
                <input type="text" name="saque" id='saque' class="form-control input-lg">
            </div>

            <div class="col-md-3">
                <label>Taxa OctaPay</label>
                <input type="text" name="taxa_octapay" id='taxa_octapay' class="form-control input-lg">
            </div>

            <div class="col-md-3">
                <label>Valor transação R$</label>
                <input type="text" name="valor_transacao" id='valor_transacao' class="form-control input-lg">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-4">
                <label>Taxa do grupo iniciante %</label>
                <input type="text" name="taxa_iniciante" id='taxa_iniciante' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Vendas do grupo iniciante</label>
                <input type="text" name="vendas_iniciante" id='vendas_iniciante' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Dias para saque iniciante</label>
                <input type="text" name="dias_iniciante" id='dias_iniciante' class="form-control input-lg">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-4">
                <label>Taxa do grupo bronze %</label>
                <input type="text" name="taxa_bronze" id='taxa_bronze' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Vendas do grupo bronze</label>
                <input type="text" name="vendas_bronze" id='vendas_bronze' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Dias para saque bronze</label>
                <input type="text" name="dias_bronze" id='dias_bronze' class="form-control input-lg">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-4">
                <label>Taxa do grupo prata %</label>
                <input type="text" name="taxa_prata" id='taxa_prata' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Vendas do grupo prata</label>
                <input type="text" name="vendas_prata" id='vendas_prata' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Dias para saque prata</label>
                <input type="text" name="dias_prata" id='dias_prata' class="form-control input-lg">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-4">
                <label>Taxa do grupo ouro %</label>
                <input type="text" name="taxa_ouro" id='taxa_ouro' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Vendas do grupo ouro</label>
                <input type="text" name="vendas_ouro" id='vendas_ouro' class="form-control input-lg">
            </div>

            <div class="col-md-4">
                <label>Dias para saque prata</label>
                <input type="text" name="dias_ouro" id='dias_ouro' class="form-control input-lg">
            </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
        <button class="btn btn-primary" id="salvar">Salvar</button>
      </div>

      </form>
    </div>

  </div>
</div>

<script>

    $("input").blur(function(e) {
        var valor = $(this).val();
        console.log(valor);
        $(this).val(valor.replace(",", "."));
    });

    function editar(i) {

        var id = "<?= $taxa->id ?>";
        var parcelamento = "<?= $taxa->parcelamento ?>";
        var saque = "<?= $taxa->saque ?>";
        var taxa_octapay = "<?= $taxa->taxa_octapay ?>";
        var valor_transacao = "<?= $taxa->valor_transacao ?>";
        var taxa_iniciante = "<?= $taxa->taxa_iniciante ?>";
        var vendas_iniciante = "<?= $taxa->vendas_iniciante ?>";
        var dias_iniciante = "<?= $taxa->dias_iniciante ?>";
        var taxa_bronze = "<?= $taxa->taxa_bronze ?>";
        var vendas_bronze = "<?= $taxa->vendas_bronze ?>";
        var dias_bronze = "<?= $taxa->dias_bronze ?>";
        var taxa_prata = "<?= $taxa->taxa_prata ?>";
        var vendas_prata = "<?= $taxa->vendas_prata ?>";
        var dias_prata = "<?= $taxa->dias_prata ?>";
        var taxa_ouro = "<?= $taxa->taxa_ouro ?>";
        var vendas_ouro = "<?= $taxa->vendas_ouro ?>";
        var dias_ouro = "<?= $taxa->dias_ouro ?>";

        $("#id").attr("value", id);
        $("#parcelamento").attr("value", parcelamento);
        $("#saque").attr("value", saque);
        $("#taxa_octapay").attr("value", taxa_octapay);
        $("#valor_transacao").attr("value", valor_transacao);
        $("#taxa_iniciante").attr("value", taxa_iniciante);
        $("#vendas_iniciante").attr("value", vendas_iniciante);
        $("#dias_iniciante").attr("value", dias_iniciante);
        $("#taxa_bronze").attr("value", taxa_bronze);
        $("#vendas_bronze").attr("value", vendas_bronze);
        $("#dias_bronze").attr("value", dias_bronze);
        $("#taxa_prata").attr("value", taxa_prata);
        $("#vendas_prata").attr("value", vendas_prata);
        $("#dias_prata").attr("value", dias_prata);
        $("#taxa_ouro").attr("value", taxa_ouro);
        $("#vendas_ouro").attr("value", vendas_ouro);
        $("#dias_ouro").attr("value", dias_ouro);

    }

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });
    
    function salvarEditar(salvar) {
        if(salvar == 1) {
            url = "<?= $router->route("taxa.editar"); ?>";
        } else {
            url = "<?=$router->route("taxa.editar")?>";
        }

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
                if(callback == "Atualizado Com Sucesso!"){
                    $('form').each (function(){
                        this.reset();
                    });
                    location.reload();
                }
            }

        });
    }

    function ativo(id, ativo) {
        $.ajax({
            url: "<?= $router->route("taxa.inativar"); ?>",
            data: {id: id, ativo: ativo},
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
                if(callback == "Atualizado com sucesso"){
                    location.reload();
                }
            }

        });
    }
</script>
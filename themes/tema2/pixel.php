<?php

$v->layout("_theme");
$i = 1;

$status = array(
    1 => "EM ANÁLISE" ,
    2 => "APROVADO",
    3 => "REPROVADO",
    4 => "BLOQUEADO"
);
$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);
?>

<div class="principal">
<?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
    
    <div class="row">
        <div class="col-md-12">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" id="cadastro">Cadastrar</button>
        </div>
    </div>

    <div class="row">

        <div class="col-md-10">
        <?php if($pixel): ?>
            <script> var dados = []; </script>
        <div style="overflow: auto;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Pixel</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Plataforma</th>
                        <th scope="col">Ativo</th>
                        <th scope="col">Executar no Checkout</th>
                        <th scope="col">Executar no Boleto</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($pixel as $pixel): ?>
                    <tr class="plano" id="plano<?= $pixel->id; ?>">
                        <th scope="row"><?= $pixel->id_pixel; ?></th>
                        <td><?= $pixel->nome; ?></td>
                        <td>R$ <?= number_format($pixel->valor, 2, ",", "."); ?></td>
                        <td><?= $pixel->plataforma; ?></td>
                        <td><?= $ativo[$pixel->status] ?></td>
                        <td><?= $ativo[$pixel->executar_checkout] ?></td>
                        <td><?= $ativo[$pixel->boleto] ?></td>
                        <?php if($pixel->usuario == $_SESSION["id"]): ?>
                        <td>
                            <button class="btn btn-warning edit" data-target="#myModal" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="material-icons" style="font-size: 15px">create</i></button>
                            <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $pixel->id; ?>)"><i class="material-icons" style="font-size: 15px">close</i></button>
                        </td>
                        <?php endif; ?>
                    </tr>

                    <script>
                        dados[<?= $i ?>] = {
                            id : "<?= $pixel->id ?>",
                            nome : "<?= $pixel->nome ?>",
                            observacao : "<?= $pixel->observacao ?>",
                            valor : "<?= $pixel->valor ?>",
                            boleto : "<?= $pixel->boleto ?>",
                            status : "<?= $pixel->status ?>",
                            executar_checkout : "<?= $pixel->executar_checkout ?>",
                            plataforma : "<?= $pixel->plataforma ?>",
                            id_pixel : "<?= $pixel->id_pixel ?>",
                        }
                    </script>

                <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/pixel/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);
        
        else:
        ?>
        <h4>Nenhum pixel encontrado!</h4>
        <?php
        endif;
        ?>
        </div>
    </div>
</div>

<!-- CHAMA O MODAL -->
<!--?= $v->insert("fragmentos/modal-usuarios-cadastrar", []); ?-->

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cadastrar Pixel</h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <div class="modal-body">
        <input type="hidden" name="id" id="id">
        
        <label>Nome do Pixel *</label>
        <input type="text" name="nome" id='nome' class="form-control" placeholder="Nome"><br>

        <label>Observação</label>
        <textarea  name="observacao" id='observacao' class="form-control input-lg"></textarea><br>

        Executar no Boleto
        <select name="boleto" id="boleto" class="form-control">
            <option value="0">Não</option>
            <option value="1">Sim</option>
        </select><br>

        Executar no Checkout
        <select name="executar_checkout" id="executar_checkout" class="form-control">
            <option value="0">Não</option>
            <option value="1">Sim</option>
        </select><br>

        Ativo
        <select name="status" id="status" class="form-control">
            <option value="0">Não</option>
            <option value="1" selected>Sim</option>
        </select><br>

        Valor que será enviado
        <input type="text" name="valor" id="valor" class="form-control"><br>

        Plataforma *
        <select name="plataforma" id="plataforma" class="form-control">
            <option value="Facebook">Facebook</option>
        </select><br>

        Id do Pixel *
        <input type="text" name="id_pixel" id="id_pixel" class="form-control"><br>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
        <button class="btn btn-primary" id="salvar">Salvar</button>
        <button class="btn btn-warning" id="editar">Editar</button>
      </div>

      </form>
    </div>

  </div>
</div>

<script>

    $("#valor").blur(function(e) {
        var valor = $("#valor").val();
        $("#valor").val(valor.replace(",", "."));
    });

    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Planos");
        $("#salvar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#editar").css("display", "none");
        cancelar();
    });

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });

    $("#editar").click(function(e) {
        e.preventDefault();
        salvarEditar(0);
    });
    
    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });
    
    function salvarEditar(salvar) {
        if(salvar == 1) {
            url = "<?= $router->route("pixel.cadastro"); ?>";
        } else {
            url = "<?=$router->route("pixel.editar")?>";
        }

        var nome = $("#nome").val(),
            id_pixel = $("#id_pixel").val();

        var form = $("form");

        if(nome == "" || id_pixel == ""){

            alert("Preecha todos os dados necessários");

        } else {

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
                    console.log(form.serialize());
                    alert(callback);
                    location.reload();
                }

            });
        }
    }

    function editar(i) {
        var id = dados[i]["id"];
        var nome = dados[i]["nome"];
        var observacao = dados[i]["observacao"];
        var valor = dados[i]["valor"];
        var boleto = dados[i]["boleto"];
        var status = dados[i]["status"];
        var executar_checkout = dados[i]["executar_checkout"];
        var plataforma = dados[i]["plataforma"];
        var id_pixel = dados[i]["id_pixel"];


        console.log(id, nome, observacao, valor, boleto, status, executar_checkout, plataforma, id_pixel);

        $(".modal-title").html("Editar Usuário " + nome);
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({"value": id});
        $("#nome").attr({"value": nome});
        $("#observacao").attr({"value": observacao});
        $("#valor").attr({"value": valor});
        $("#id_pixel").prop({"value": id_pixel});
        $("#boleto option[value=" + boleto + "]").attr({"selected": true});
        $("#status option[value=" + status + "]").attr({"selected": true});
        $("#executar_checkout option[value=" + executar_checkout + "]").attr({"selected": true});
        $("#id_pixel option[value=" + id_pixel + "]").attr({"selected": true});
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "none");
        $("#id").attr({"value": ""});
        $("#preco").attr({"value": ""});
        $("#nome").attr({"value": ""});
        $("#quantidade").attr({"value": ""});
        $("#descricao").attr({"value": ""});
        $("#privado").prop({"checked": false});
        $("#ativo").prop({"checked": false});
    }

    function excluir(id) {
        console.log(id);
        if(confirm("Tem certeza que deseja excluir esse plano?")){
            $.ajax({
                url: "<?=$router->route("pixel.excluir")?>",
                data: { id: id},

                type: "POST",
                beforeSend: function () {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function (callback) {
                    console.log(callback);
                    if(callback == "Excluido com sucesso"){
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
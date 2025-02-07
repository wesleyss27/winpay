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

<!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
<div class="menu-edicao principal bg-white">
    <?php if($produto->usuario == $_SESSION["id"]):
        $v->insert("produtos/menu-produto", ["produto" => $produto]);
    endif; ?>
    <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
    <a href="<?= url("produtos/campanhas/").$produto->id; ?>">Campanhas</a>
</div>

<div class="">
<?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
    
    <div class="row">
        <div class="col-md-12">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" id="cadastro">Cadastrar</button>
        <p>* não é necessário cadastrar planos para vender o produto, basta cadastrar um checkout.</p>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
        <?php if($planos): ?>
            <script> var dados = []; </script>
        <div style="overflow: auto;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Código</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Produto</th>
                        <th scope="col">Preço</th>
                        <th scope="col">Un.</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($planos as $plano): ?>
                    <tr class="plano" id="plano<?= $plano->id; ?>">
                        <th scope="row"><?= $plano->id; ?></th>
                        <th><?= $plano->codigo; ?></th>
                        <td><?= $plano->nome; ?></td>
                        <td><?= $plano->dataProduto()->nome; ?></td>
                        <td>R$ <?= number_format($plano->preco, 2, ",", "."); ?></td>
                        <td><?= $plano->quantidade; ?></td>
                        <td>
                            Ativo: <?= $ativo[$plano->ativo] ?><br>
                            Privado: <?= $ativo[$plano->privado] ?>
                        </td>
                        <?php if($plano->usuario == $_SESSION["id"]): ?>
                        <td>
                            <button class="btn btn-warning edit" data-target="#myModal" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="material-icons" style="font-size: 15px">create</i></button>
                            <!--button class="btn btn-danger btn-excluir" onclick="excluir(<?= $plano->id; ?>)"><i class="material-icons" style="font-size: 15px">close</i></button-->
                        </td>
                        <?php endif; ?>
                    </tr>

                    <script>
                        dados[<?= $i ?>] = {
                            id : "<?= $plano->id ?>",
                            nome : "<?= $plano->nome ?>",
                            preco : "<?= $plano->preco ?>",
                            quantidade : "<?= $plano->quantidade ?>",
                            descricao : "<?= $plano->descricao ?>",
                            privado : "<?= $plano->privado ?>",
                            ativo : "<?= $plano->ativo ?>",
                        }
                    </script>

                <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/planos/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);
        
        else:
        ?>
        <h4>Não existem planos cadastrados!</h4>
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
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cadastrar Planos</h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <div class="modal-body">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="status" value="<?= $produto->status ?>">
        <input type="hidden" name="produto" value="<?= $produto->id ?>">
        
        <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome completo">

        <br>

        <div class="row">
            <div class="col-md-6">
            Preço *
            <input type="text" name="preco" id='preco' class="form-control input-lg" placeholder="Preço *" required>
            </div>

            <div class="col-md-6">
            Quantidade *
            <div class="input-group">
                <span class="input-group-addon" id="add-login"><i class="material-icons">person</i></span>
                <input type="text" name="quantidade" id='quantidade' class="form-control input-lg" placeholder="Quantidade *">
            </div>
            <small>Quantidade de itens que serão enviados nesse plano</small>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-12">
            Descrição:
            <textarea name="descricao" id="descricao" cols="30" rows="5" class="form-control"></textarea>

            <label>
                <input type="checkbox" name="privado" id='privado'>
                Plano privado
            </label>

            <label>
                <input type="checkbox" name="ativo" id='ativo'>
                Ativar Plano
            </label>
            </div>
        </div>
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
            url = "<?= $router->route("produtos.planos"); ?>";
        } else {
            url = "<?=$router->route("produtos.planosEditar")?>";
        }

        var email = $("#email").val(),
            senha = $("#senha").val(),
            tipo_usuario = $("#tipo_usuario").val(),
            nome = $("#nome").val(),
            login = $("#login").val();

        var form = $("form");

        if(email == "" || (senha == "" && salvar == 1 ) || tipo_usuario == ""){

            alert("Preecha todos os dados necessários");

        } else {

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
    }

    function editar(i) {
        var id = dados[i]["id"];
        var nome = dados[i]["nome"];
        var preco = dados[i]["preco"];
        var quantidade = dados[i]["quantidade"];
        var descricao = dados[i]["descricao"];
        var privado = (dados[i]["privado"] == 1) ? true : false;
        var ativo = (dados[i]["ativo"] == 1) ? true : false;

        console.log(id, preco, nome, quantidade, descricao, privado);

        $(".modal-title").html("Editar Usuário " + nome);
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({"value": id});
        $("#preco").attr({"value": preco});
        $("#nome").attr({"value": nome});
        $("#quantidade").attr({"value": quantidade});
        
        $("#descricao").prop({"value": descricao});
        $("#privado").prop({"checked": privado});
        $("#ativo").prop({"checked": ativo});
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
                url: "<?=$router->route("produtos.planosExcluir")?>",
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
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
        </div>
    </div>

    <div class="row">

        <div class="principal">
        <?php if($urls): ?>
            <script> var dados = []; </script>
        <div style="overflow: auto;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Descrição</th>
                        <th scope="col">Url</th>
                        <th scope="col">Privada</th>
                        <th scope="col"></th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($urls as $url): ?>
                    <tr class="plano" id="plano<?= $url->id; ?>">
                        <th><?= $url->descricao; ?></th>
                        <td><?= $url->url; ?></td>
                        <td><?= $ativo[$url->privada]; ?></td>
                        <?php if($url->usuario == $_SESSION["id"]): ?>
                        <td>
                            <button class="btn btn-warning edit" data-target="#myModal" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="material-icons" style="font-size: 15px">create</i></button>
                            <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $url->id; ?>)"><i class="material-icons" style="font-size: 15px">close</i></button>
                        </td>
                        <?php endif; ?>
                    </tr>

                    <script>
                        dados[<?= $i ?>] = {
                            id : "<?= $url->id ?>",
                            url : "<?= $url->url ?>",
                            descricao : "<?= $url->descricao ?>",
                            privada : "<?= $url->privada ?>",
                        }
                    </script>

                <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/produtos/urls/".$url->produto."/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);
        
        else:
        ?>
        <h4>Nenhuma url encontrada!</h4>
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
        <h4 class="modal-title">Cadastrar Urls</h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <div class="modal-body">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="usuario" value="<?= $_SESSION["id"] ?>">
        <input type="hidden" name="produto" value="<?= $produto->id ?>">
        
        Descrição *
        <input type="text" name="descricao" id='descricao' class="form-control input-lg" placeholder="Descrição">
        <br>

        URL *
        <input type="text" name="url" id='url' class="form-control input-lg" placeholder="URL">
        <br>

        <label>
            <input type="checkbox" name="privada" id='privada'>
            Url privada
        </label>

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

    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Urls");
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
            destino = "<?= $router->route("produtos.url"); ?>";
        } else {
            destino = "<?=$router->route("produtos.urlEditar")?>";
        }

        var descricao = $("#descricao").val(),
            url = $("#url").val();

        var form = $("form");

        if(descricao == "" || url == ""){

            alert("Preecha todos os dados necessários");

        } else {

            $.ajax({

                url: destino,
                data: form.serialize(),
                type: "POST",
                beforeSend: function () {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function (callback) {
                    console.log(destino);
                    console.log("callback ", callback);
                    alert(callback);
                    location.reload();
                }

            });
        }
    }

    function editar(i) {
        var id = dados[i]["id"];
        var url = dados[i]["url"];
        var descricao = dados[i]["descricao"];
        var privada = (dados[i]["privada"] == 1) ? true : false;

        $(".modal-title").html("Editar Url");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({"value": id});
        $("#url").attr({"value": url});
        $("#descricao").attr({"value": descricao});
        $("#privada").prop({"checked": privada});
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "none");
        $("#id").attr({"value": ""});
        $("#url").attr({"value": ""});
        $("#descricao").attr({"value": ""});
        $("#privada").prop({"checked": false});
    }

    function excluir(id) {
        console.log(id);
        if(confirm("Tem certeza que deseja excluir esse plano?")){
            $.ajax({
                url: "<?=$router->route("produtos.urlExcluir")?>",
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
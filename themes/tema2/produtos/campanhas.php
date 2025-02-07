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

$buscaAfiliacao = $afiliacao->find("afiliado = '{$_SESSION["id"]}' AND produto = '".@$produto->id."'")->fetch();
$BuscaCheckout = $checkout->find("produto = '".@$produto->id."'")->fetch(true);
?>

<!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
<div class="menu-edicao principal bg-white">
    <?php if($produto->usuario == $_SESSION["id"]):
        $v->insert("produtos/menu-produto", ["produto" => $produto]);
    endif; ?>
    <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
    <a href="<?= url("produtos/campanhas/").$produto->id; ?>">Campanhas</a>
</div>

<div class="principal">
<?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
    
    <div class="row">
        <div class="col-md-12">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" id="cadastro">Cadastrar</button>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
        <?php if($campanhas): ?>
            <script> var dados = []; </script>
        <div style="overflow: auto;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Url de Destino</th>
                        <th scope="col">Pixel</th>
                        <th scope="col">Vendas</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($campanhas as $campanha): ?>
                    <tr class="campanha" id="campanha<?= $campanha->id; ?>">
                        <th scope="row"><?= $campanha->codigo; ?></th>
                        <td><?= $campanha->nome; ?></td>
                        <td><?= url("a/").$campanha->url_destino."&cp=".$campanha->codigo; ?></td>

                        <?php if($campanha->pixel): ?>
                        <td><?= @$campanha->dataPixel()->nome . " - " . @$campanha->dataPixel()->id_pixel; ?></td>
                        <?php else: ?>
                        <td align="center">-</td>
                        <?php endif; ?>

                        <td><?= $campanha->countVendas(); ?></td>
                        <?php if($campanha->usuario == $_SESSION["id"]): ?>
                        <td>
                            <button class="btn btn-warning edit" data-target="#myModal" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="material-icons" style="font-size: 15px">create</i></button>
                            <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $campanha->id; ?>)"><i class="material-icons" style="font-size: 15px">close</i></button>
                        </td>
                        <?php endif; ?>
                    </tr>

                    <script>
                        dados[<?= $i ?>] = {
                            id : "<?= $campanha->id ?>",
                            nome : "<?= $campanha->nome ?>",
                            url_destino : "<?= $campanha->url_destino ?>",
                            pixel : "<?= $campanha->pixel ?>"
                        }
                    </script>

                <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/produtos/campanhas/".$produto->id."/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);
        
        else:
        ?>
        <h4>Você ainda não criou nenhuma campanha</h4>
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
        <h4 class="modal-title">Cadastrar campanhas</h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <div class="modal-body">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="produto" value="<?= $produto->id ?>">
        
        <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome">

        <br>

        Página de destino *
        <select name="url_destino" id="url_destino" class="form-control input-lg" required>
            <?php if(@$buscaAfiliacao != ""): ?>
            <option value="<?= $buscaAfiliacao->codigo . "?ch=" . 0 . "&tp=5" ?>">Página de Venda - <?= $produto->pagina_venda ?></option>
            <?php else: ?>
            <option value="0?tp=4&ch=0">Página de Venda - <?= $produto->pagina_venda ?></option>
            <?php endif; ?>

            <?php 
                if($BuscaCheckout):
                foreach($BuscaCheckout as $checkout): 
                if($buscaAfiliacao):
            ?>
                <option value="<?= $buscaAfiliacao->codigo . "?ch=" . $checkout->codigo . "&tp=5" ?>"><?= $checkout->descricao ?> - <?= url("a/" . $buscaAfiliacao->codigo . "?ch=" . $checkout->codigo . "&tp=5") ?></option>

            <?php else: ?>
                <option value="<?= $checkout->codigo . "?tp=4&ch=$checkout->codigo" ?>"><?= $checkout->descricao ?> - <?= url("checkout/" . $checkout->codigo . "?tp=4") ?></option>
            <?php 
                endif;
                endforeach; 
                endif; 
            ?>
        </select>

        <br>
        Pixel
        <select name="pixel" id="pixel" class="form-control input-lg">
            <option disabled selected>Selecionar...</option>
            <?php if($pixel): foreach($pixel as $pixel):?>
            <option value="<?= $pixel->id ?>"><?= $pixel->nome . " - " . $pixel->id_pixel ?></option>
            <?php endforeach; endif; ?>
        </select>

        <br>
        Não tem pixel? <a href="<?= url("pixel") ?>">Cadastrar Pixel</a>
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

        $(".modal-title").html("Cadastrar campanhas");
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
            url = "<?= $router->route("produtos.campanha"); ?>";
        } else {
            url = "<?=$router->route("produtos.campanhaEditar")?>";
        }

        var nome = $("#nome").val();

        var form = $("form");

        if(nome == ""){

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
        var url_destino = dados[i]["url_destino"];
        var pixel = dados[i]["pixel"];


        console.log(id, nome, url_destino, pixel);

        $(".modal-title").html("Editar Usuário " + nome);
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({"value": id});
        $("#nome").attr({"value": nome});
        $("#url_destino option[value='" + url_destino + "']").attr({"selected": true});
        $("#pixel option[value=" + pixel + "]").attr({"selected": true});
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "none");
        $("#id").attr({"value": ""});
        $("#nome").attr({"value": ""});
        $("#url_destino option").attr({"selected": false});
        $("#pixel option").attr({"selected": false});
    }

    function excluir(id) {
        console.log(id);
        if(confirm("Tem certeza que deseja excluir esse campanha?")){
            $.ajax({
                url: "<?=$router->route("produtos.campanhaExcluir")?>",
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
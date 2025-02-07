<?php 
    $v->layout("_theme"); 
    if($produto->usuario != $_SESSION["id"]){
        header("Location: " . url("produtos/detalhe/").$produto->id);
    }

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
<p>* É necessário cadastrar um checkout para vender o produto e que este produto esteja aprovado.</p>
<form action="<?=$router->route("produtos.checkout")?>" method="POST" enctype="multipart/form-data">
<div class="form-group">
    <input type="hidden" name="id" id="id">

    <label>Descrição: </label>
    <input type="text" class="form-control" id="descricao" name="descricao">

    <label>Produto: </label>
    <select name="produto" id="produto" class="form-control">
        <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
    </select>

    <label>Plano: </label>
    <select name="plano" id="plano" class="form-control">
        <?php if($plano): foreach($plano as $plano): ?>
        <option value="<?= $plano->id ?>"><?= "$plano->codigo $plano->nome" ?></option>
        <?php endforeach; endif; ?>
    </select>

    <label>Banner Topo: </label>
    <input type="file" class="form-control" id="banner" name="banner">
    <input type="hidden" class="form-control" id="banner_atual" name="banner_atual">
    <small>imagem de banner principal que ficará no topo da tela</small><br>

    <label>Banner Topo 2: </label>
    <input type="file" class="form-control" id="aviso" name="aviso">
    <input type="hidden" class="form-control" id="aviso_atual" name="aviso_atual">
    <small>imagem que ficará abaixo do banner principal</small><br>

    <br>
    Preencha os campos abaixo caso queria oferecer desconto no checkout
    <div class="row">
        <div class="col-md-4">
        Cupom
        <input type="text" name="cupom" id='cupom' class="form-control input-lg">
        </div>

        <div class="col-md-4">
        Valor de Desconto
        <input type="text" name="valor_cupom" id='valor_cupom' class="form-control input-lg">
        </div>

        <div class="col-md-4">
        Somente cartão?
        <select name="so_cartao" id="so_cartao" class="form-control input-lg">
            <option value="0">Não</option>
            <option value="1">Sim</option>
        </select>
        </div>
    </div>

</div>

    <button class="btn btn-success" id="salvar">Salvar</button>
    <button class="btn btn-warning" id="editar">Editar</button>
    <button class="btn btn-danger" id="cancelar">Cancelar</button>
</form>
</div>

<div class="col-md-12">

    <?php if($checkout): ?>
        <script>
                var dados = [];
            </script>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Descrição</th>
                <th scope="col">Produto</th>
                <th scope="col">Plano</th>
                <th scope="col">Cupom</th>
                <th scope="col" style="text-align: center">Desde</th>
                <th scope="col">#</th>
            </tr>
        </thead>

        <tbody>
            <?php 
                $i = 0;

                foreach($checkout as $checkout): 
                    
            ?>
            <tr>
                <td>
                    <?= $checkout->codigo ?><br>
                    <a href="<?= url("checkout/" . $checkout->codigo) ?>" target="_blank"><?= url("checkout/" . $checkout->codigo) ?></a>
                </td>
                <td><?= $checkout->descricao ?></td>
                <td><?php 
                    if($checkout->dataProduto()){
                        foreach($checkout->dataProduto() as $produto) {
                            echo $produto->nome;
                        }
                    }
                ?></td>
                <td><?php 
                    if($checkout->dataPlano()){
                        foreach($checkout->dataPlano() as $plano) {
                            echo $plano->nome;
                        }
                    }
                ?></td>
                <td>
                    <?php if($checkout->cupom): ?>
                        <small>Cupom: </small> <?= $checkout->cupom ?><br>
                        <small>Só Cartão:</small> <?= $ativo[@$checkout->so_cartao] ?><br>
                        R$ <?= number_format($checkout->valor_cupom, 2, ",", ".") ?><br>
                    <?php endif ?>
                </td>
                <td align="center"><?= formataDataHora($checkout->created_at); ?></td>
                <td>
                    <button class="btn btn-warning btn-editar" onclick="editar(<?= $i; ?>)">!</button>
                    <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $checkout->id; ?>)">X</button>
                </td>
            </tr>

            <script>
                dados[<?= $i ?>] = {
                    id : "<?= $checkout->id ?>",
                    descricao : "<?= $checkout->descricao ?>",
                    plano : "<?= $checkout->plano ?>",
                    banner : "<?= $checkout->banner ?>",
                    aviso : "<?= $checkout->aviso ?>",
                    cupom : "<?= $checkout->cupom ?>",
                    valor_cupom : "<?= $checkout->valor_cupom ?>",
                    so_cartao : "<?= $checkout->so_cartao ?>",
                }
            </script>

            <?php $i++; endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <h4>Você ainda não tem nenhum checkout para esse produto</h4>
    <?php endif; ?>
</div>
</div>

<script>
    $("#valor_cupom").blur(function(e) {
        var valor = $("#valor_cupom").val();
        $("#valor_cupom").val(valor.replace(",", "."));
    });

    function excluir(id) {

        if(confirm("Tem certeza que deseja excluir esse coprodutor/gerente?")){
            $.ajax({
                url: "<?=$router->route("produtos.checkoutExcluir")?>",
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

    $("#editar").css("display", "none");
    $("#cancelar").css("display", "none");

    function editar(i) {
        var id = dados[i]["id"];
        var descricao = dados[i]["descricao"];
        var plano = dados[i]["plano"];
        var banner = dados[i]["banner"];
        var aviso = dados[i]["aviso"];
        var cupom = dados[i]["cupom"];
        var valor_cupom = dados[i]["valor_cupom"];
        var so_cartao = dados[i]["so_cartao"];

        console.log(id, descricao, plano, banner, aviso);
        console.log($("form").attr("action"));
        $("form").attr("action", "<?=$router->route("produtos.checkoutEditar")?>");
        console.log($("form").attr("action"));

        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $(".btn-editar, .btn-excluir").attr("disabled", "true");
        $("#id").attr({"value": id});
        $("#descricao").attr({"value": descricao});
        
        if(plano)
        $("#plano option[value=" + plano + "]").prop({"selected": true});
        
        $("#cupom").attr({"value": cupom});
        $("#valor_cupom").attr({"value": valor_cupom});
        $("#so_cartao option[value='" + so_cartao + "']").prop({"selected": true});

        $("#banner_atual").attr({"value": banner});
        $("#aviso_atual").attr({"value": aviso});
    }

    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });

    function cancelar() {
        $("form").attr("action", "<?=$router->route("produtos.checkout")?>");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "none");
        $(".btn-editar, .btn-excluir").attr("disabled", false);
        $("#id").attr({"value": ""});
        $("#descricao").attr({"value": ""});
        $("#plano option").prop({"selected": false});
        $("#banner_atual").attr({"value": ""});
        $("#aviso_atual").attr({"value": ""});
        $("#cupom").attr({"value": ""});
        $("#valor_cupom").attr({"value": ""});
        $("#so_cartao").prop({"selected": false});
    }
</script>
<?php 
    $v->layout("_theme"); 

    if($produto->usuario != $_SESSION["id"]){
        header("Location: " . url("produtos/detalhe/").$produto->id);
    }
?>

<div class="menu-edicao principal bg-white">
    <?php if($produto->usuario == $_SESSION["id"]):
        $v->insert("produtos/menu-produto", ["produto" => $produto]);
    endif; ?>
    <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
    <a href="<?= url("produtos/campanhas/").$produto->id; ?>">Campanhas</a>
</div>

<div class="row">

<div class="col-md-12">
<form action="" method="POST">
<div class="form-group">
<input type="hidden" name="produto" id="produto" value="<?= $produto->id ?>">
<input type="hidden" name="id" id="id">

    <div class="row">
        <label class="col-md-2">E-mail:</label>
        <div class="col-md-10">
            <input type="email" name="email" id="email" class="form-control" onblur="verificaEmail()">
            <label id="res-email" class="text-danger"></label><br>
        </div>
    </div>

    <br>

    <div class="row">
        <label class="col-md-2">Tipo:</label>
        <div class="col-md-10">
            <select name="gerente" id="gerente" class="form-control" onchange="mostrar()">
                <option value="0">Coprodutor</option>
                <option value="1">Gerente</option>
            </select>
        </div>
    </div>
    <br>

<div id="coprodutor">
    <div class="row">
        <label class="col-md-2">Comissão:</label>
        <div class="col-md-1">
            <select name="tipo_comissao1" id="tipo_comissao1" class="form-control">
                <option value="0">%</option>
                <option value="1">R$</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="text" name="comissao1" id="comissao1" class="form-control" placeholder="0,00">
        </div>
    </div>
    <br>
</div>

<div id="gerencia">
    <div class="row">
        <label class="col-md-2">Comissão SEM afiliado:</label>
        <div class="col-md-1">
            <select name="tipo_comissao2" id="tipo_comissao2" class="form-control">
                <option value="0">%</option>
                <option value="1">R$</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="text" name="comissao2" id="comissao2" class="form-control" placeholder="0,00">
        </div>
    </div>
    <br>

    <div class="row">
        <label class="col-md-2">Comissão COM afiliado:</label>
        <div class="col-md-1">
            <select name="tipo_comissao_afiliados" id="tipo_comissao_afiliados" class="form-control">
                <option value="0">%</option>
                <option value="1">R$</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="text" name="comissao_afiliados" id="comissao_afiliados" class="form-control" placeholder="0,00">
        </div>
    </div>
    <br>
</div>

    <div class="row">
        <label class="col-md-2">Data Limite:</label>
        <div class="col-md-3">
            <input type="date" name="data_limite" id="data_limite" class="form-control" placeholder="0,00">
        </div>
    </div>
    <br>
</div>
    <div class="row center">
        <button class="btn btn-success" id="salvar">Salvar</button>
        <button class="btn btn-warning" id="editar">Editar</button>
        <button class="btn btn-danger" id="cancelar">Cancelar</button>
        <br><br>
    </div>
</form>

    <?php if($coproducao): ?>
        <script>
                var dados = [];
            </script>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Coprodutor</th>
                <th scope="col">Tipo</th>
                <th scope="col">Aceito</th>
                <th scope="col" style="text-align: center">Comissão</th>
                <th scope="col" style="text-align: center">Desde</th>
                <th scope="col" style="text-align: center">Data Limite</th>
                <th scope="col">#</th>
            </tr>
        </thead>

        <tbody>
            <?php 
                $tipo = array(
                    0 => "Coprodutor",
                    1 => "Gerente"
                );
                $aceito = array(
                    0 => "Não",
                    1 => "Sim"
                );
                $tipoComissao = array(
                    0 => "%",
                    1 => "R$"
                );

                function formataComissao($tipo, $valor) {
                    if($tipo == 0) {
                        return number_format($valor, 2, ",", ".") . "%";
                    } else {
                        return "R$ " . number_format($valor, 2, ",", ".");
                    }
                }

                $i = 0;

                foreach($coproducao as $coproducao): 
                    
            ?>
            <tr class="cli" id="afiliacao<?= $coproducao->co_produtor; ?>">
                <td>
                    <?= $coproducao->dataCoProdutor()->nome; ?><br>
                    <?= $coproducao->dataCoProdutor()->email ?>
                </td>
                <td><?= $tipo[$coproducao->gerente]; ?></td>
                <td><?= $aceito[$coproducao->aceito]; ?></td>

                <?php if($coproducao->gerente == 1): ?>
                <td align="center">
                    COM afililiado: <?= formataComissao($coproducao->tipo_comissao_afiliados, $coproducao->comissao_afiliados) ?><br>
                    SEM afililiado: <?= formataComissao($coproducao->tipo_comissao, $coproducao->comissao) ?>
                </td>
                <?php else: ?>
                <td align="center"><?= formataComissao($coproducao->tipo_comissao, $coproducao->comissao) ?></td>
                <?php endif; ?>
                <td align="center"><?= formataDataHora($coproducao->created_at); ?></td>
                <td align="center"><?php if($coproducao->data_limite == "0000-00-00") echo " - "; else echo formataData($coproducao->data_limite); ?></td>
                <td>
                    <button class="btn btn-warning btn-editar" onclick="editar(<?= $i; ?>)">!</button>
                    <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $coproducao->id; ?>)">X</button>
                </td>
            </tr>

            <script>
                dados[<?= $i ?>] = {
                    id : "<?= $coproducao->id ?>",
                    email : "<?= $coproducao->dataCoProdutor()->email ?>",
                    gerente : "<?= $coproducao->gerente ?>",
                    tipo_comissao : "<?= $coproducao->tipo_comissao ?>",
                    comissao : "<?= $coproducao->comissao ?>",
                    tipo_comissao_afiliados : "<?= $coproducao->tipo_comissao_afiliados ?>",
                    comissao_afiliados : "<?= $coproducao->comissao_afiliados ?>",
                    data_limite : "<?= $coproducao->data_limite ?>",
                }
            </script>

            <?php $i++; endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <h4>Você ainda não tem coproduções para esse produto</h4>
    <?php endif; ?>
</div>
</div>

<script>
    $("#comissao1").blur(function(e) {
        var valor = $("#comissao1").val();
        $("#comissao1").val(valor.replace(",", "."));
    });

    $("#comissao2").blur(function(e) {
        var valor = $("#comissao2").val();
        $("#comissao2").val(valor.replace(",", "."));
    });

    $("#comissao_afiliados").blur(function(e) {
        var valor = $("#comissao_afiliados").val();
        $("#comissao_afiliados").val(valor.replace(",", "."));
    });

    var gerente = $("#gerente").val();
    var co_produtor;
    if(gerente == 1){
        $("#coprodutor").css("display", "none");
        $("#gerencia").css("display", "block");
    }
    else {
        $("#coprodutor").css("display", "block");
        $("#gerencia").css("display", "none");
    }

    function mostrar(){
        var gerente = $("#gerente").val();
        if(gerente == 0){
            $("#coprodutor").css("display", "block");
            $("#gerencia").css("display", "none");
            gerente = 0;
            console.log("gerente1 " , gerente);
        }
        else {
            $("#coprodutor").css("display", "none");
            $("#gerencia").css("display", "block");
            gerente = 1;
            console.log("gerente1 " , gerente);
        }
    }

    function verificaEmail() {
        var email = $("#email").val();
        console.log("email ", email);

        $.ajax({
            url: "<?=$router->route("appusuarios.email")?>",
            data: { email: email},

            type: "POST",
            beforeSend: function () {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log("callback ", callback);
                co_produtor = callback;
                if(callback == "Não existe usuário com esse email"){
                    $("#res-email").html(callback);
                    $("#salvar").attr("disabled", true);
                } else {
                    $("#res-email").css("display", "none");
                    $("#salvar").attr("disabled", false);
                }
            }
        });
    }

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

    function salvarEditar(salvar){

        if(salvar == 1) {
            url = "<?=$router->route("produtos.coproducao")?>";
        } else {
            url = "<?=$router->route("produtos.coproducaoEditar")?>";
        }
        
        var gerente = $("#gerente").val();
        var id = $("#id").val();
        console.log("url ", url, id, gerente);

        var produto = $("#produto").val();
        var email = $("#email").val();
        var data_limite = $("#data_limite").val();

        if(gerente == 1) {
            var tipo_comissao2 = $("#tipo_comissao2").val();
            var comissao2 = $("#comissao2").val();
            var tipo_comissao_afiliados = $("#tipo_comissao_afiliados").val();
            var comissao_afiliados = $("#comissao_afiliados").val();
            

            if(tipo_comissao2 == "" || comissao2 == "" || tipo_comissao_afiliados == "" || comissao_afiliados == "" || email == "") {
                alert("Preencha todos os campos");
            } else {
                console.log("comissao ", comissao2, comissao_afiliados);
                $.ajax({
                    url: url,
                    data: { id: id, produto: produto, tipo_comissao: tipo_comissao2, comissao: comissao2, tipo_comissao_afiliados: tipo_comissao_afiliados, 
                            comissao_afiliados: comissao_afiliados, co_produtor: co_produtor, gerente: 1, aceito: 0, todos_afiliados: 1, 
                            data_limite: data_limite},

                    type: "POST",
                    beforeSend: function () {
                        console.log("atualizando");
                    },
                    error: function() {
                        console.log("O servidor não conseguiu processar o pedido");
                    },
                    success: function (callback) {
                        console.log(callback);
                        if(callback == "Salvo com sucesso"){
                            console.log("sucesso");
                            alert(callback);
                            location.href = "<?=$router->route("produtos.coproducao")?>/" + produto;
                        } else {
                            console.log("fracasso");
                            alert(callback);
                        }
                    }
                });
            }
        } else {

            var tipo_comissao1 = $("#tipo_comissao1").val();
            var comissao1 = $("#comissao1").val();

            console.log("comissao ", comissao1, co_produtor);
            if(tipo_comissao1 == "" || comissao1 == "" || email == "") {
                alert("Preencha todos os campos");
            } else {

                $.ajax({
                    url: url,
                    data: { id: id, produto: produto, tipo_comissao: tipo_comissao1, comissao: comissao1, co_produtor: co_produtor,
                            gerente: 0, aceito: 0, todos_afiliados: 1, data_limite: data_limite},

                    type: "POST",
                    beforeSend: function () {
                        console.log("atualizando");
                    },
                    error: function() {
                        console.log("O servidor não conseguiu processar o pedido");
                    },
                    success: function (callback) {
                        console.log(callback);
                        if(callback == "Salvo com sucesso"){
                            console.log("sucesso");
                            alert(callback);
                            location.href = "<?=$router->route("produtos.coproducao")?>/" + produto;
                        } else {
                            console.log("fracasso");
                            alert(callback);
                        }
                    }
                });
            }
        }
    };

    function excluir(id) {
        var produto = $("#produto").val();
        console.log(id);

        if(confirm("Tem certeza que deseja excluir esse coprodutor/gerente?")){
            $.ajax({
                url: "<?=$router->route("produtos.coproducaoExcluir")?>",
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
        var email = dados[i]["email"];
        var tipo = dados[i]["gerente"];
        var tipo_comissao = dados[i]["tipo_comissao"];
        var comissao = dados[i]["comissao"];
        var tipo_comissao_afiliados = dados[i]["tipo_comissao_afiliados"];
        var comissao_afiliados = dados[i]["comissao_afiliados"];
        var data_limite = dados[i]["data_limite"];

        console.log(id, email, tipo, tipo_comissao, comissao, tipo_comissao_afiliados, comissao_afiliados, data_limite);

        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $(".btn-editar, .btn-excluir").attr("disabled", "true");
        $("#id").attr({"value": id});
        $("#email").attr({"value": email, "readonly": true, "disabled": true});
        $("#gerente option[value=" + tipo + "]").attr({"selected": true});

        console.log("gerente " , gerente);
        mostrar();
        verificaEmail();
        console.log("gerente " , gerente);

        if(tipo == 1){
            $("#tipo_comissao2 option[value=" + tipo_comissao + "]").attr({"selected": true});
            $("#comissao2").attr({"value": comissao});

            $("#tipo_comissao_afiliados option[value=" + tipo_comissao_afiliados + "]").attr({"selected": true});
            $("#comissao_afiliados").attr({"value": comissao_afiliados});
        } else {
            $("#tipo_comissao1 option[value=" + tipo_comissao + "]").attr({"selected": true});
            $("#comissao1").attr({"value": comissao});
        }

        $("#data_limite").attr({"value": data_limite});

    }

    function cancelar() {
        var tipo = $("#gerente").val();
        console.log("tipo ",tipo);
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "none");
        $(".btn-editar, .btn-excluir").attr("disabled", false);
        $("#id").attr({"value": ""});
        $("#email").attr({"value": "", "readonly": false, "disabled": false});
        $("#gerente option").attr({"selected": false});

        $("#tipo_comissao1").attr({"selected": true});
        $("#comissao1").attr({"value": ""});

        mostrar();

        $("#tipo_comissao2 option").attr({"selected": true});
        $("#comissao2").attr({"value": ""});

        $("#tipo_comissao_afiliados option").attr({"selected": true});
        $("#comissao_afiliados").attr({"value": ""});

        $("#data_limite").attr({"value": ""});

    }
</script>
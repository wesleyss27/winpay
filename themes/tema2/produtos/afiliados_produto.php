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

<div class="">

<form action="" method="POST">
<input type="hidden" name="id" id="id" value="<?= $produto->id ?>">
    <div class="row form-group">
        <label class="col-md-2">Abrir para afiliados?</label>
        <div class="col-md-10">
            <select name="abrir_afiliacao" id="abrir_afiliacao" class="form-control" onchange="mostrar_afiliacao()">
                <option value="0" <?=($produto->abrir_afiliacao == 0)?'selected':''?>>Não</option>
                <option value="1" <?=($produto->abrir_afiliacao == 1)?'selected':''?>>Sim</option>
            </select>
        </div>
    </div>
<div id="mostrar_afiliacao" class="form-group">
    <div class="row">
        <label class="col-md-2">Tipo de aprovação:</label>
        <div class="col-md-10">
            <select name="afiliados_aprovacao" id="afiliados_aprovacao" class="form-control">
                <option value="1" <?=($produto->afiliados_aprovacao == 1)?'selected':''?>>Todos podem promover meu produto</option>
                <option value="0" <?=($produto->afiliados_aprovacao == 0)?'selected':''?>>Somente afiliados aprovados por mim</option>
            </select>
        </div>
    </div>
    <br>

    <div class="row">
        <label class="col-md-2">Visualização no MarketPlace:</label>
        <div class="col-md-10">
            <select name="visualizacao" id="visualizacao" class="form-control">
                <option value="1" <?=($produto->visualizacao == 1)?'selected':''?>>Sim, Qualquer um pode ver meu produto</option>
                <option value="0" <?=($produto->visualizacao == 0)?'selected':''?>>Não, Somente a quem eu enviar convite</option>
            </select>
        </div>
    </div>
    <br>

    <div class="row">
        <label class="col-md-2">Dados do comprador:</label>
        <div class="col-md-10">
            <select name="libera_comprador" id="libera_comprador" class="form-control">
                <option value="0" <?=($produto->libera_comprador == 0)?'selected':''?>>Não</option>
                <option value="1" <?=($produto->libera_comprador == 1)?'selected':''?>>Sim</option>
            </select>
        </div>
    </div>
    <br>

    <div class="row">
        <label class="col-md-2">Tempo de cookie:</label>
        <div class="col-md-10">
            <input type="text" name="tempo_cookie" id="cookie" value="<?= $produto->tempo_cookie; ?>" class="form-control">
        </div>
    </div>
    <br>

    <div class="row">
        <label class="col-md-2">Comissão:</label>
        <div class="col-md-1">
            <select name="tipo_comissao" id="tipo_comissao" class="form-control">
                <option value="0" <?=($produto->tipo_comissao == 0)?'selected':''?>>%</option>
                <option value="1" <?=($produto->tipo_comissao == 1)?'selected':''?>>R$</option>
            </select>
        </div>

        <div class="col-md-2">
        <input type="text" name="comissao_afiliado" id="comissao" value="<?= $produto->comissao_afiliado; ?>" class="form-control">
        </div>
    </div>
    <br>

    <div class="row">
        <label class="col-md-2">Tipo de atribuição:</label>
        <div class="col-md-10">
            <select name="tipo_atribuicao" id="tipo_atribuicao" class="form-control">
                <option value="1" <?=($produto->tipo_atribuicao == 1)?'selected':''?>>Primeiro Clique</option>
                <option value="2" <?=($produto->tipo_atribuicao == 2)?'selected':''?>>Último Clique</option>
            </select>
        </div>
    </div>
    <br>
    <br>
</div>

    <div class="row center">
        <button class="btn btn-success" id="salvar">Salvar</button>
        <br><br>
    </div>
</form>

    <?php if($afiliacao): ?>

    <script> var dados = []; </script>
    <div style="overflow: auto">

    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Afiliado</th>
                <th scope="col">Status</th>
                <th scope="col">Comissão</th>
                <th scope="col">Desde</th>
                <th scope="col">Dados Comprador</th>
                <th scope="col">#</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            $i = 1;

            $ativo = array(
                0 => "Não",
                1 => "Sim"
            );
            $statusAfiliacao = array(
                1 => "PENDENTE",
                2 => "APROVADO",
                3 => "REPROVADO",
                4 => "BLOQUEADO"
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
                foreach($afiliacao as $afiliacao): 
            ?>
            <tr class="cli" id="afiliacao<?= $afiliacao->id; ?>">
                <td><?php 
                    if($afiliacao->dataAfiliado()){
                        foreach($afiliacao->dataAfiliado() as $afiliado) {
                            echo $afiliado->nome . " <br> " . $afiliado->email;
                        }
                    }
                ?></td>
                <td><?= $statusAfiliacao[$afiliacao->status]; ?></td>
                <td id="comissao"><?= formataComissao($afiliacao->tipo_comissao, $afiliacao->comissao_afiliado); ?></td>
                <td><?= formataData($afiliacao->created_at); ?></td>
                <td><?= $ativo[$afiliacao->liberar_comprador]; ?></td>
                <td>
                    <button class="btn btn-warning btn-editar" data-target="#myModal" data-toggle="modal" onclick="editar(<?= $i; ?>)"><i class="material-icons" style="font-size: 15px">create</i></button>
                    <button class="btn btn-danger btn-excluir" onclick="excluir(<?= $afiliacao->id; ?>)"><i class="material-icons" style="font-size: 15px">close</i></button>
                </td>
            </tr>

            <script>
                dados[<?= $i ?>] = {
                    id : "<?= $afiliacao->id ?>",
                    email : "<?= $afiliado->email ?>",
                    nome : "<?= $afiliado->nome ?>",
                    status : "<?= $afiliacao->status ?>",
                    senha : "<?= $afiliacao->senha ?>",
                    liberar_comprador : "<?= $afiliacao->liberar_comprador ?>",
                    tempo_cookie: "<?= $afiliacao->tempo_cookie ?>",
                    tipo_comissao: "<?= $afiliacao->tipo_comissao ?>",
                    comissao_afiliado: "<?= $afiliacao->comissao_afiliado ?>",
                    tipo_atribuicao: "<?= $afiliacao->tipo_atribuicao ?>"
                }
            </script>
            <?php $i++; endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
    <h4>Você ainda não tem afiliados para esse produto</h4>
    <?php endif; ?>
</div>


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar Afiliação</h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <input type="hidden" name="afiliacao_id" id="afiliacao_id">
      <div class="modal-body">

      <div>
        <strong>Afiliado: </strong><span id="afiliado"></span><br>
        <strong>Email: </strong><span id="email"></span><br>
      </div>
        
        <label>Status do Afiliado</label>
        <div class="input-group">
            <span class="input-group-addon"><i class="material-icons">person</i></span>
            <select name="status" id="status1" class="form-control input-lg" required="required">
                <option disabled selected>Status</option>
                <option value="1">Pendente</option>
                <option value="2">Aprovar</option>
                <option value="3">Reprovar</option>
                <option value="4">Bloquear</option>
            </select>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
            <label>Dados do Comprador</label>
            <div class="input-group">
                <span class="input-group-addon" id="add-email"><i class="material-icons">email</i></span>
                <select name="liberar_comprador" id="liberar_comprador1" class="form-control input-lg" required="required">
                    <option disabled selected>Dados Comprador</option>
                    <option value="1">Sim</option>
                    <option value="0">Não</option>
                </select>
            </div>
            </div>

            <div class="col-md-6">
            <label>Tempo de cookie</label>
            <div class="input-group">
                <span class="input-group-addon" id="add-login"><i class="material-icons">person</i></span>
                <input type="text" name="tempo_cookie" id='tempo_cookie1' class="form-control input-lg" placeholder="Tempo de cookie">
            </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
            <label> Comissão </label>
            <div class="input-group">
            <span class="input-group-addon" id="add-login">
                <select name="tipo_comissao" id="tipo_comissao1" class="">
                    <option value="0">%</option>
                    <option value="1">R$</option>
                </select>
            </span>

                <input type="text" name="comissao_afiliado" id="comissao_afiliado1"  class="form-control input-lg">
            </div>
            </div>

            <div class="col-md-6">
            <label> Tipo de Atribuição de Comissao </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="material-icons">lock</i></span>
                <select name="tipo_atribuicao" id="tipo_atribuicao1" class="form-control input-lg" required="required">
                    <option disabled selected>Atribuição de Comissao</option>
                    <option value="1">Primeiro Clique</option>
                    <option value="2">Último Clique</option>
                </select>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar1">Cancelar</button>
        <button class="btn btn-primary" id="salvar1">Salvar</button>
      </div>

      </form>
    </div>

  </div>
</div>


<script>
    $("#comissao").blur(function(e) {
        var valor = $("#comissao").val();
        $("#comissao").val(valor.replace(",", "."));
    });
    
    $("#comissao_afiliado1").blur(function(e) {
        var valor = $("#comissao_afiliado1").val();
        $("#comissao_afiliado1").val(valor.replace(",", "."));
    });

    var afiliacao = $("#abrir_afiliacao").val();
    if(afiliacao == 0)
        $("#mostrar_afiliacao").css("display", "none");
    else
        $("#mostrar_afiliacao").css("display", "block");

    function mostrar_afiliacao(){
        var mos = $("#abrir_afiliacao").val();
        if(mos == 1)
            $("#mostrar_afiliacao").css("display", "block");
        else
            $("#mostrar_afiliacao").css("display", "none");
        
        afiliacao = 1;
    }

    $("#salvar").click(function(e){
        e.preventDefault();

        console.log(afiliacao);
        if(afiliacao == 1) {
            var cookie = $("#cookie").val();
            var comissao = $("#comissao").val();

            if(cookie == "" || comissao == "") {
                alert("Preencha todos os campos");
            } else {

                var id = $("#id").val();// ID DO PRODUTO
                var abrir_afiliacao = $("#abrir_afiliacao").val();
                var afiliados_aprovacao = $("#afiliados_aprovacao").val();
                var visualizacao = $("#visualizacao").val();
                var libera_comprador = $("#libera_comprador").val();
                var tempo_cookie = $("#cookie").val();
                var tipo_comissao = $("#tipo_comissao").val();
                var comissao_afiliado = $("#comissao").val();
                var tipo_atribuicao = $("#tipo_atribuicao").val();

                console.log(id, abrir_afiliacao, afiliados_aprovacao);

                $.ajax({
                    url: "<?=$router->route("produtos.afiliados")?>",
                    data: { id: id, abrir_afiliacao: abrir_afiliacao, afiliados_aprovacao: afiliados_aprovacao, visualizacao: visualizacao, 
                            libera_comprador: libera_comprador, tempo_cookie: tempo_cookie, tipo_comissao: tipo_comissao, comissao_afiliado: comissao_afiliado, 
                            tipo_atribuicao: tipo_atribuicao},

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
                            location.href = "<?= url("produtos/afiliados")?>/" + id;
                        } else {
                            console.log("fracasso");
                            alert(callback);
                        }
                    }
                });
            }
        } else {
            var id = $("#id").val();
            var abrir_afiliacao = $("#abrir_afiliacao").val();

            $.ajax({
                url: "<?=$router->route("produtos.afiliados")?>",
                data: { id: id, abrir_afiliacao: abrir_afiliacao },

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
                        location.href = "<?=$router->route("produtos.afiliados")?>";
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    });

    function editar(i) {

        var id = dados[i]["id"];
        var email = dados[i]["email"];
        var nome = dados[i]["nome"];
        var status = dados[i]["status"];
        var liberar_comprador = dados[i]["liberar_comprador"];
        var tempo_cookie = dados[i]["tempo_cookie"];
        var tipo_comissao = dados[i]["tipo_comissao"];
        var comissao_afiliado = dados[i]["comissao_afiliado"];
        var tipo_atribuicao = dados[i]["tipo_atribuicao"];

        console.log(id, email, nome, status, liberar_comprador, 
        tempo_cookie, tipo_comissao, comissao_afiliado, tipo_atribuicao); 
        
        console.log($("#status").val());
        
        $(".modal-title").html("Editar Usuário " + nome);
        $("#afiliacao_id").attr({"value": id});
        $("#email").html(email);
        $("#afiliado").html(nome);
        $("#status1 option[value=" + status + "]").attr("selected", "true");
        $("#liberar_comprador1 option[value=" + liberar_comprador + "]").attr("selected", "true");
        $("#tempo_cookie1").attr({"value": tempo_cookie});
        $("#tipo_comissao1 option[value=" + tipo_comissao + "]").attr("selected", "true");
        $("#comissao_afiliado1").attr({"value": comissao_afiliado});
        $("#tipo_atribuicao1 option[value=" + tipo_atribuicao + "]").attr("selected", "true");

    }

    function cancelar() {
        $("#afiliacao_id").attr({"value": ""});
        $("#email").html();
        $("#afiliado").html();
        $("#status1").attr("selected", "true");
        $("#liberar_comprador1").attr("selected", "true");
        $("#tempo_cookie1").attr({"value": ""});
        $("#tipo_comissao1").attr("selected", "true");
        $("#comissao_afiliado1").attr({"value": ""});
        $("#tipo_atribuicao1").attr("selected", "true");
    }

    $("#salvar1").click(function(e) {
        e.preventDefault();
        salvar();
    });

    function salvar() {

        var form = $("form");

        $.ajax({

            url: "<?= $router->route("appafiliados.editar") ?>",
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
                $('form').each (function(){
                    this.reset();
                });
                $("#myModal").modal("hide");
                location.reload();
            }

        });
    }

    function excluir(id) {
        console.log(id);
        if(confirm("Tem certeza que deseja excluir essa afiliação?")){
            $.ajax({
                url: "<?=$router->route("afiliacao.excluir")?>",
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
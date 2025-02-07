<?php 
    $v->layout("_theme"); 

    $status = array(
        1 => "Pendente",
        2 => "Aprovada",
        3 => "Reprovada",
        4 => "Bloqueada"
    )
?>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<div class="principal  bg-white">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" id="cadastro">Cadastrar Conta</button>
</div>

<div class="principal  bg-white">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
    
    <div class="row">

        <div class="col-md-12">
        <?php if(@$bancos): ?>
            <script> var dados = []; </script>
            <div style="overflow: auto;">
            
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Status</th>
                        <th scope="col">Titular</th>
                        <th scope="col">CPF/CNPJ</th>
                        <th scope="col">Banco</th>
                        <th scope="col">Agência</th>
                        <th scope="col">Conta</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Usuário</th>
                        <th scope="col"></th>
                    </tr>
                </thead>

                <tbody>
                <?php $i = 1; foreach($bancos as $banco): ?>
                    <tr class="plano" id="plano<?= $banco->id; ?>">
                        <?php if($_SESSION["tipo"] == 1): ?>
                        <th scope="row"><?= $banco->id; ?></th>
                        <?php endif; ?>
                        <td><?= $status[$banco->status]; ?></td>
                        <td><?= $banco->titular; ?></td>
                        <td><?= $banco->cpf; ?></td>
                        <td><?= $banco->banco; ?></td>
                        <td><?= $banco->agencia; ?></td>
                        <td><?= $banco->conta; ?></td>
                        <td><?= $banco->tipo; ?></td>
                        <td><?= $banco->dataUsuario()->nome; ?></td>
                        <td>
                        <?php if($_SESSION["tipo"] == 1): ?>
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#status" onclick="edtStatus(<?= $banco->id; ?>)">S</button>
                        <?php endif; ?>
                        <?php if($_SESSION["id"] == $banco->usuario): ?>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" onclick="editar(<?= $banco->id; ?>)"><i class="material-icons" style="font-size: 15px">create</i></button>
                            <button type="button" class="btn btn-danger" onclick="excluir(<?= $banco->id; ?>)"><i class="material-icons" style="font-size: 15px">close</i></button>
                        <?php endif; ?>
                        </td>
                    </tr>

                    <script>
                        dados[<?= $banco->id ?>] = {
                            id : "<?= $banco->id ?>",
                            titular : "<?= $banco->titular ?>",
                            cpf : "<?= $banco->cpf ?>",
                            banco : "<?= $banco->banco ?>",
                            agencia : "<?= $banco->agencia ?>",
                            conta: "<?= $banco->conta ?>",
                            tipo: "<?= $banco->tipo ?>",
                            chave_pix: "<?= $banco->chave_pix ?>",
                            status: "<?= $banco->status ?>",
                        }
                    </script>

                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
            </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/banco/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($paginas, $pagina, $link);
        
        else:
        ?>
        <h4>Você ainda não possiu conta bancária!</h4>
        <p>Você precisa cadastrar uma conta para realizar saques</p>
        <?php
        endif;
        ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Cadastrar Conta Bancária</h4>
        </div>
        <form class="form_ajax cadastro" action="#" method="post" id="form_cadastro">
        <div class="modal-body">
            <input type="hidden" name="id" id="id">

            <label>Titular:</label>
            <input type="text" name="titular" id="titular" class="form-control" required>

            <label>CPF do Titular:</label>
            <input type="text" name="cpf" id="cpf" class="form-control" required>

            <?php include "lista-bancos.php" ?>
            <label>Banco:</label> <small>colocar código do banco</small><br>
            <select name="banco" id="banco" class="form-control meuselect" required>
                <?php foreach($lista_bancos as $cod => $lista): ?>
                <option value="<?= $cod ?>"><?= $cod . " - " . $lista ?></option>
                <?php endforeach; ?>
            </select>

            <br>
            <label>Agência:</label>
            <input type="text" name="agencia" id="agencia" class="form-control" required>

            <label>Conta:</label>
            <input type="text" name="conta" id="conta" class="form-control" required>

            <label>Chave Pix:</label>
            <input type="text" name="chave_pix" id="chave_pix" class="form-control" required>

            <label>Tipo:</label>
            <select name="tipo" id="tipo" class="form-control" required>
                <option value="poupanca">Poupança</option>
                <option value="corrente">Corrente</option>
            </select>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
            <button class="btn btn-primary" id="salvar">Cadastrar</button>
            <button class="btn btn-warning" id="atualizar">Salvar</button>
        </div>

        </form>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="status" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Status Conta Bancária</h4>
        </div>
        <form class="form_ajax cadastro" action="#" method="post">
        <div class="modal-body">
            <input type="hidden" name="idStatus" id="idStatus">

            <label>Status:</label>
            <select name="status" id="mudaStatus" class="form-control">
                <option value="1">Pendente</option>
                <option value="2">Aprovada</option>
                <option value="3">Reprovada</option>
                <option value="4">Bloqueada</option>
            </select>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
            <button class="btn btn-warning" id="atlStatus">Salvar</button>
        </div>

        </form>
    </div>

  </div>
</div>

<script>
    $("#cadastro").click(function() {
        $('form').each (function(){
            this.reset();
        });

        $("#salvar").css("display", "inline");
        $("#atualizar").css("display", "none");
    });

    $("#salvar").click(function(e) {
        e.preventDefault();
        var cont = 0;
        $("#form_cadastro input").each(function(){
            if($(this).val() == "") {
                $(this).css({"border" : "1px solid #F00", "padding": "2px"});
                cont++;
                console.log("Cont: ", cont);
            }
        });
        console.log(cont);
        if(cont == 1) {
            salvar("<?= $router->route('appcomissao.bancoCadastro')?>");
        }
    });

    $("#atualizar").click(function(e) {
        e.preventDefault();
        salvar("<?= $router->route('appcomissao.bancoEditar')?>");
    });

    $("#atlStatus").click(function(e) {
        e.preventDefault();
        salvar("<?= $router->route('appcomissao.bancoStatus')?>");
    });

    function salvar(url) {
        var form = $("form");
        $.ajax({

            url: url,
            data: form.serialize(),
            type: "POST",
            //datatype: "json",
            success: function (callback) {
                alert(callback);
                if(callback == "Salvo com sucesso" || callback == "Atualizado com sucesso"){
                    location.reload();
                }
            }

        });
    };

    function editar(i) {
        var id = dados[i]["id"];
        var titular = dados[i]["titular"];
        var cpf = dados[i]["cpf"];
        var banco = dados[i]["banco"];
        var agencia = dados[i]["agencia"];
        var conta = dados[i]["conta"];
        var tipo = dados[i]["tipo"];
        var chave_pix = dados[i]["chave_pix"];

        console.log(i, titular, banco);

        $("#salvar").css("display", "none");
        $("#atualizar").css("display", "inline");

        $("#id").attr({"value": id});
        $("#titular").attr({"value": titular});
        $("#cpf").attr({"value": cpf});
        $("#banco option[value=" + banco + "]").prop({"selected": true});
        $("#agencia").attr({"value": agencia});
        $("#conta").attr({"value": conta});
        $("#chave_pix").attr({"value": chave_pix});
        $("#tipo option[value=" + tipo + "]").prop({"selected": true});
    }

    function edtStatus(i) {
        var status = dados[i]["status"];

        $("#idStatus").attr({"value": i});
        $("#mudaStatus option[value=" + status + "]").prop({"selected": true});
    }

    function excluir(id) {
        console.log(id);
        if(confirm("Tem certeza que deseja excluir esse campanha?")){
            $.ajax({
                url: "<?=$router->route("appcomissao.bancoExcluir")?>",
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

    $(document).ready(function() {
        $(".meuselect").select2();
    });

</script>
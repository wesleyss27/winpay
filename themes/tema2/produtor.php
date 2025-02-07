<?php 
    $v->layout("_theme"); 

    if($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2){
        header("Location: " . URL_BASE);
    }

    $ativo = array(
        0 => "NÃO",
        1 => "SIM"
    );
    $grupo = array(
        1 => "Iniciante",
        2 => "Bronze",
        3 => "Prata",
        4 => "Ouro"
    );
?>

<div class="principal bg-white">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
    
    <div class="row">
        <?php if(@$acesso == 4 || @$acesso == 5): ?>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary col-md-12" data-toggle="modal" data-target="#myModal" id="cadastro">Cadastrar</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-md-12">
        <?php if($produtor): ?>
            <script> var dados = []; </script>
            <div style="overflow: auto;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Tempo de Saque</th>
                        <th scope="col">Taxa de Saque</th>
                        <th scope="col">Desde</th>
                        <th scope="col">Grupo</th>
                        <th scope="col">Ativo</th>
                        <th scope="col">Vendas</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                    $i = 1; 
                    foreach($produtor as $produtor): 
                    $venda = $vendas->find("produtor = $produtor->id")->count();
                ?>
                    <tr id="usuario<?= $produtor->id; ?>">
                        <th scope="row"><?= $produtor->id; ?></th>
                        <td><?= $produtor->nome . "<br>" . $produtor->email; ?></td>
                        <td align="center"><?= $produtor->tempo_saque; ?></td>
                        <td align="center"><?= $produtor->taxa_saque; ?>%</td>
                        <td><?= formataData($produtor->created_at); ?></td>
                        <td align="center"><?= $grupo[$produtor->grupo]; ?></td>
                        <td align="center"><?= $ativo[$produtor->ativo]; ?></td>
                        <td align="center"><?= $venda; ?></td>
                        <td>
                            <button class="btn btn-warning" data-target="#myModal" onclick="editar(<?= $i; ?>)" data-toggle="modal">Editar</a></button>
                            <?php if($produtor->ativo == 1): ?>
                                <button class="btn btn-danger" onclick="ativo(<?= $produtor->id; ?>, 0)">Inativar</a></button>
                            <?php else: ?>
                                <button class="btn btn-success" onclick="ativo(<?= $produtor->id; ?>, 1)">Ativar</a></button>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php $dados = $usuarioDados->find("usuario = $produtor->id")->fetch(); ?>
                    <script>
                        dados[<?= $i ?>] = {
                            id : "<?= $produtor->id ?>",
                            email : "<?= $produtor->email ?>",
                            nome : "<?= $produtor->nome ?>",
                            tempo_saque : "<?= $produtor->tempo_saque ?>",
                            taxa_saque : "<?= $produtor->taxa_saque ?>",
                            observacao : "<?= $produtor->observacao ?>",
                            ativo : "<?= $produtor->ativo ?>",
                            grupo : "<?= $produtor->grupo ?>",

                            tipo_doc: "<?= $dados->tipo_doc ?>",
                            cpf: "<?= $dados->cpf_cnpj ?>",
                            
                            email: "<?= $dados->email ?>",
                            telefone: "<?= $dados->telefone ?>",

                            cidade: "<?= $dados->cidade ?>",
                            estado: "<?= $dados->estado ?>",
                        }
                    </script>
                <?php $i++; endforeach; ?>
                </tbody>
            </table>
            </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/produtor/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppProdutor
        $paginador->paginas($total, $pagina, $link);
        
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
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Opções para <span id="nome_produtor"></span></h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <input type="hidden" name="id" id="id">
      <div class="modal-body">
        
        <div class="row">
            <div class="col-md-6">
                <b>Tipo Documento: </b> <span id="tipo_doc"></span><br>
                <b>CPF/CNPJ: </b> <span id="cpf"></span><br>
                <b>Email: </b> <span id="email"></span><br>
            </div>

            <div class="col-md-6">
                <b>Telefone: </b> <span id="telefone"></span><br>
                <b>Cidade: </b> <span id="cidade"></span> - <span id="estado"></span><br>
            </div>
        </div>
        
        <br>

        <div class="row">
            <div class="col-md-6">
            Tempo de Saque:
            <div class="input-group">
                <span class="input-group-addon" id="add-email"><i class="material-icons">email</i></span>
                <input type="text" name="tempo_saque" id='tempo_saque' class="form-control input-lg" placeholder="Tempo de saque">
            </div>
            <p id="msg-email"></p>
            </div>

            <div class="col-md-6">
            Taxa de Saque:
            <div class="input-group">
                <span class="input-group-addon" id="add-login"><i class="material-icons">person</i></span>
                <input type="text" name="taxa_saque" id='taxa_saque' class="form-control input-lg" placeholder="Taxa de saque">
            </div>
            <p id="msg-login"></p>
            </div>
        </div>

        Observação:
        <div class="input-group">
            <span class="input-group-addon"><i class="material-icons">person</i></span>
            <textarea name="observacao" id='observacao' class="form-control input-lg" placeholder="Observação"></textarea>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
            Status:
            <div class="input-group">
                <span class="input-group-addon"><i class="material-icons">lock</i></span>
                <select name="ativo" id="ativo" class="form-control input-lg" required="required">
                    <option disabled selected>Ativo *</option>
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>
            </div>

            <div class="col-md-6">
            Grupo:
            <div class="input-group">
                <span class="input-group-addon"><i class="material-icons">lock</i></span>
                <select name="grupo" id="grupo" class="form-control input-lg" required="required">
                    <option disabled selected>Grupo *</option>
                    <option value="1">Iniciante</option>
                    <option value="2">Bronze</option>
                    <option value="3">Prata</option>
                    <option value="4">Outro</option>
                </select>
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
    $("#taxa_saque").blur(function(e) {
        var valor = $("#taxa_saque").val();
        $("#taxa_saque").val(valor.replace(",", "."));
    });

    function editar(i) {
        $('form').each (function(){
            this.reset();
        });
        var id = dados[i]["id"];
        var email = dados[i]["email"];
        var nome = dados[i]["nome"];
        var tempo_saque = dados[i]["tempo_saque"];
        var taxa_saque = dados[i]["taxa_saque"];
        var observacao = dados[i]["observacao"];
        var ativo = dados[i]["ativo"];
        var grupo = dados[i]["grupo"];

        console.log(id, email, nome, tempo_saque, taxa_saque, observacao, ativo);


        $("#nome_produtor").html(nome);
        $("#id").attr({"value": id});
        $("#email").attr({"value": email});
        $("#nome").attr({"value": nome});
        $("#tempo_saque").attr({"value": tempo_saque});
        $("#taxa_saque").attr({"value": taxa_saque});
        $("#observacao").attr({"value": observacao});
        $("#ativo option[value=" + ativo + "]").attr({"selected": true});
        $("#grupo option[value=" + grupo + "]").attr({"selected": true});

        $("#tipo_doc").html(dados[i]["tipo_doc"]);
        $("#cpf").html(dados[i]["cpf"]);
        $("#email").html(dados[i]["email"]);
        $("#telefone").html(dados[i]["telefone"]);
        $("#cidade").html(dados[i]["cidade"]);
        $("#estado").html(dados[i]["estado"]);
    }

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });
    
    function salvarEditar(salvar) {
        if(salvar == 1) {
            url = "<?= $router->route("produtor.editar"); ?>";
        } else {
            url = "<?=$router->route("appusuarios.editar")?>";
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
            url: "<?= $router->route("produtor.inativar"); ?>",
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
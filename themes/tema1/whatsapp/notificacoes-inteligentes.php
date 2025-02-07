<?php $v->layout("_theme"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
                </div>
                
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Postback</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">

        <div class="card card-body">
            Somente o produtor pode cadastrar uma integração! <br>

            <a href="https://manual.notificacoesinteligentes.com/integrations/">Crie uma integração do tipo API</a><br>
            <a href="https://manual.notificacoesinteligentes.com/integrations/api/">Configure essa integração</a><br>
            <a href="https://manual.notificacoesinteligentes.com/instances/instances/#como-configurar-uma-integracao-em-uma-instancia">Configure uma instância</a><br>
            <br>
        </div>

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <a class="btn btn-outline-primary" data-toggle="modal" data-target="#cadastrar" style="font-family:inter" id="cadastro">
                    Notificações Inteligentes
                    <i class="fa fa-chevron-right"></i> 
                </a>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                    </button>
        
                </div>
            </div>

            <!-- CARD BODY -->
            <div class="card-body p-0" style="overflow-x: auto">
                
                <?php if(@$notificacoes): $i = 1; ?>
                <script> var dados = []; </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                        <tr style="font-family:inter">
                            <th scope="col">ID</th>
                            <th scope="col">Token</th>
                            <th scope="col">Integração ID</th>
                            <th scope="col">Ação</th>
                        </tr>
                        </thead>

                        <tbody>
                            <?php foreach($notificacoes as $notificacao): ?>
                                <tr>
                                    <th><?= $notificacao->id ?></th>
                                    <td><?= $notificacao->token ?></td>
                                    <td><?= $notificacao->integracao_id ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-target="#cadastrar" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm" onclick="excluir(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id : "<?= $notificacao->id ?>",
                                        token : "<?= $notificacao->token ?>",
                                        integracao_id : "<?= $notificacao->integracao_id ?>",
                                    }
                                </script>
                            <?php $i++; endforeach ?>
                        </tbody>
                    </table>
                <?php 
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $link = URL_BASE."/whatsapp/notificacoes-inteligentes/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link);

                else: ?>
                    Nenhuma integração encontrada
                <?php endif; ?>
            </div>
            <!-- FIM CARD BODY -->
    
        </div>
    </section>
</div>



<!-- Modal -->
<div id="cadastrar" class="modal fade" role="dialog">
  	
    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Notificações Inteligentes</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->
              	
            <form action="#">
            <!-- Modal body -->
            <div class="modal-body">

                <input type="hidden" name="id" id="id">

                <div class="row">
                    <div class="col-md-6">
                        <b>Token:</b>
                        <input type="text" name="token" id="token" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <b>Integração ID:</b>
                        <input type="text" name="integracao_id" id="integracao_id" class="form-control">
                    </div>
                </div>
            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
              <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
              <button type="button" class="btn btn-warning" id="editar">Editar</button>
            </div>
            <!-- Fim Modal footer -->
            </form>

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->
 

 <?php $v->start("scripts") ?>
    <script>

        $("#cadastro").click((e) => {
            cancelar();
        });

        $("#salvar").click((e) => {
            e.preventDefault();
            salvarEditar("<?= $router->route("whatsapp.cadastrarNI") ?>");
        });

        $("#editar").click((e) => {
            e.preventDefault();
            salvarEditar("<?= $router->route("whatsapp.editarNI") ?>");
        });

        function salvarEditar(url) {

            var form = $("form");
            console.log(form.serialize());

            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                success: (callback) => {
                    console.log(callback);
                    
                    alert(callback);
                    if(callback == "Salvo com sucesso"){
                        location.reload();
                    }
                }
            })
        }

        function editar(i) {
            var id = dados[i]["id"];
            var token = dados[i]["token"];
            var integracao_id = dados[i]["integracao_id"];

            console.log(id);

            $("#salvar").css("display", "none");
            $("#editar").css("display", "inline");
            $("#id").attr({"value": id});
            $("#token").attr("value", token);
            $("#integracao_id").attr("value", integracao_id);
        }

        function cancelar() {
            $("#salvar").css("display", "inline");
            $("#editar").css("display", "none");
            $("#id").attr({"value": ""});
            $("#token").attr("value", "");
            $("#integracao_id").attr("value", "");
        }

        function excluir(id) {
            console.log(id);
            if(confirm("Tem certeza que deseja excluir essa integração?")){
                $.ajax({
                    url: "<?=$router->route("whatsapp.excluirNI")?>",
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
<?php $v->end() ?>
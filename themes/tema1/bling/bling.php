<?php 
    $v->layout("_theme"); 
    
    $json = array(
        0 => "Não",
        1 => "Sim"
    );
?>

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
                        <li class="breadcrumb-item"><a href="./">Integração</a></li>
                        <li class="breadcrumb-item active">Bling</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <a class="btn btn-outline-primary" data-toggle="modal" data-target="#cadastrar" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </a>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                    </button>
        
                </div>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">
                
                <?php if(@$bling): $i = 1; ?>
                <script> var dados = []; </script>
                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                        <tr style="font-family:inter">
                            <th scope="col">ID</th>
                            <th scope="col">Proudto</th>
                            <th scope="col">API KEY</th>
                            <th scope="col">Ação</th>
                        </tr>
                        </thead>

                        <tbody>
                            <?php foreach($bling as $bling): ?>
                                <tr>
                                    <td><?= $bling->id ?></td>
                                    <td><?= $bling->produto ? $bling->dataProduto()->nome : "Todos" ?></td>
                                    <td><?= $bling->api_key ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-target="#cadastrar" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm" onclick="excluir(<?= $bling->id; ?>)" data-toggle="modal"><i class="fas fa-times"></i></button>

                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id : "<?= $bling->id ?>",
                                        produto : "<?= $bling->produto ?>",
                                        api_key : "<?= $bling->api_key ?>",
                                    }
                                </script>
                            <?php $i++; endforeach ?>
                        </tbody>
                    </table>
                <?php 
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $link = URL_BASE."/bling/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link);

                else: ?>
                    Nenhuma integração com Bling encontrada
                <?php endif; ?>
            </div>
            <!-- FIM CARD BODY -->
    
        </div>
    </section>
</div>



<!-- Modal -->
<div id="cadastrar" class="modal fade" role="dialog">
  	
    <!-- Modal dialog -->
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Cadastrar Integração Com Bling</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->
              	
            <form action="#">
            <!-- Modal body -->
            <div class="modal-body">

                <input type="hidden" name="id" id="id">
                <b>API KEY</b>
                <input type="text" name="api_key" id="api_key" class="form-control">
                
                <br>

                <b>Produto</b>
                <select name="produto" id="produto" class="form-control">
                    <option value="">Todos</option>
                    <?php if($produtos): foreach($produtos as $produto): ?>
                        <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                    <?php endforeach; endif; ?>
                </select>
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
            salvarEditar("<?= $router->route("bling.cadastrar") ?>");
        });

        $("#editar").click((e) => {
            e.preventDefault();
            salvarEditar("<?= $router->route("bling.editar") ?>");
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
                    if(callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso"){
                        location.reload();
                    }
                }
            })
        }

        $("#testar").click((e) => {
            e.preventDefault();
            console.log("clique");
            testar();
            console.log("clique");
        });

        function editar(i) {
            var id = dados[i]["id"];
            var produto = dados[i]["produto"];
            var api_key = dados[i]["api_key"];

            $(".modal-title").html("Editar Integração Com Bling");
            $("#salvar").css("display", "none");
            $("#editar").css("display", "inline");
            $("#resultado").css("display", "none");
            $("#id").attr({"value": id});
            
            if(produto)
            $("#produto option[value=" + produto + "]").prop("selected", true);
            
            $("#api_key").attr("value", api_key);
        }

        function cancelar() {
            $(".modal-title").html("Editar Integração Com Bling");
            $("#salvar").css("display", "inline");
            $("#editar").css("display", "none");
            $("#resultado").css("display", "none");
            $("#id").attr({"value": ""});
            $("#produto option").prop("selected", false);
            $("#api_key").attr("value", "");
        }

        function excluir(id) {
            console.log(id);
            if(confirm("Tem certeza que deseja excluir esse plano?")){
                $.ajax({
                    url: "<?=$router->route("bling.excluir")?>",
                    data: { id: id },

                    type: "POST",
                    beforeSend: function () {
                        console.log("atualizando");
                    },
                    error: function() {
                        console.log("O servidor não conseguiu processar o pedido");
                    },
                    success: function (callback) {
                        console.log(callback);
                        if(callback == "Excluído com sucesso"){
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
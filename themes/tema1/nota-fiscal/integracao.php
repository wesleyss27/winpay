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
                        <li class="breadcrumb-item active">Notas</li>
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
                <a class="btn btn-outline-primary" data-toggle="modal" data-target="#integracaoNotazz" style="font-family:inter" id="cadastro">Integração Notazz <i class="fa fa-chevron-right"></i> </a>

                &nbsp; &nbsp;
                <a href="https://octapay.com.br/como-integrar-com-o-notazz/" target="_blank">Como integrar com o Notazz?</a><br>
                Url de postback Notazz: <b><?= url("notas/postback") ?></b>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                    </button>
        
                </div>
            </div>

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">
                <!--div class="alert bg-warning">Em Fase de Testes</div-->
                <?php if(@$notas): $i = 1; ?>

                    <script> var dados = []; </script>

                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">ID</th>
                                <th scope="col">API</th>
                                <th scope="col">Dias após a venda</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Data Cadastro</th>
                                <th scope="col">Ação</th>
                            </tr>
                        </thead>

                        <tbody>
                        <tr style="font-family:inter">
                            <?php foreach($notas as $nota): ?>
                            <th scope="row"><?= $nota->id; ?></th>
                            <th><?= mb_strimwidth(@$nota->api_notazz, 0, 30, "...") ; ?></th>
                            <td><?= $nota->data_nota == 0 ? "Manualmente" : $nota->data_nota . " dia(s)"; ?></td>
                            <td><?= $nota->produto ? $nota->dataProduto()->nome : "-" ?></td>
                            <td><?= formataDataHora($nota->created_at); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-target="#integracaonotazz" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm" onclick="excluir(<?= $nota->id; ?>)"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                        
                        <script>
                            dados[<?= $i ?>] = {
                                id : "<?= $nota->id ?>",
                                api_notazz : "<?= $nota->api_notazz ?>",
                                data_nota : "<?= $nota->data_nota ?>",
                                produto : "<?= $nota->produto ?>",
                                codigo_cnae : "<?= $nota->codigo_cnae ?>",
                                codigo_lc116 : "<?= $nota->codigo_lc116 ?>",
                                enviar_email_cliente : "<?= $nota->enviar_email_cliente ?? 1 ?>",
                            }
                        </script>

                        <?php $i++; endforeach; ?>
                        
                    </tbody>
                </table>

                <?php 
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $link = URL_BASE."/integracao/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link);

                else: ?>
                    Nenhuma integração cadastrada
                <?php endif; ?>
            </div>
            <!-- FIM CARD BODY -->
    
        </div>
    </section>
</div>



<!-- Modal -->
<div id="integracaonotazz" class="modal fade" role="dialog">
  	
    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <h4 class="modal-title">Integração com Notazz</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <form action="#">
            <!-- Modal body -->
            <div class="modal-body">
                
                <input type="hidden" id="id" name="id">
                <div class="row">
                    <div class="col-md-6">
                        <b>API Notazz</b> (<small>obrigatório</small>)
                        <input type="text" name="api_notazz" id="api_notazz" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <b>Quando gerar a nota?</b> (<small>obrigatorio</small>)
                        <select name="data_nota" id="data_nota" class="form-control">
                            <option value="0">Manualmente</option>
                            <!--option value="1">No momento da venda</option>
                            <option value="10">10 dias após a venda</option-->
                        </select>
                    </div>
                </div>

                <br>

                
                <div class="row">
                    <div class="col-md-6">
                        <b>Produto</b> (<small>opcional</small>)
                        <select name="produto" id="produto" class="form-control">
                            <option value="">Selecionar</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <b>Código CNAE do produto</b> (<small>opcional</small>)
                        <input type="text" name="codigo_cnae" id="codigo_cnae" class="form-control">
                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-6">
                        <b>Código LC116</b> (<small>opcional</small>)
                        <input type="text" name="codigo_lc116" id="codigo_lc116" class="form-control">
                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-12">
                        <b>Enviar nota fiscal para o cliente por e-mail?</b>
                        <select name="enviar_email_cliente" id="enviar_email_cliente" class="form-control">
                            <option value="1">Sim — enviar por e-mail ao cliente</option>
                            <option value="0">Não — apenas emitir a nota</option>
                        </select>
                    </div>
                </div>

                <small>
                    Campos opcionais: <br><br>

                    Escolha se desejar gerar uma nota para um produto específico; <br>
                    Digite o código de atividade econômica do produto selecionado; <br>
                    Digite o código da área de serviço na prefeitura. <br><br>

                    obs.: caso não seja digitado o código CNAE e LC116, será configurado de acordo com o cadastrado no Notazz <br>
                    obs.: você pode pegar o código CNAE e LC116 nas configurações do seu produto no Notazz <br>
                </small>
            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                <button class="btn btn-primary" id="salvar">Salvar</button>
                <button class="btn btn-warning" id="editar">Editar</button>
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
            salvarEditar("<?= $router->route("notazz.cadastrar") ?>");
        });

        $("#editar").click((e) => {
            e.preventDefault();
            salvarEditar("<?= $router->route("notazz.editar") ?>");
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
            var api_notazz = dados[i]["api_notazz"];
            var data_nota = dados[i]["data_nota"];
            var produto = dados[i]["produto"];
            var codigo_cnae = dados[i]["codigo_cnae"];
            var codigo_lc116 = dados[i]["codigo_lc116"];
            var enviar_email_cliente = dados[i]["enviar_email_cliente"];

            $(".modal-title").html("Editar API");
            $("#salvar").css("display", "none");
            $("#editar").css("display", "inline");
            $("#id").attr({"value": id});
            $("#api_notazz").attr("value", api_notazz);
            $("#data_nota option[value=" + data_nota + "]").prop("selected", true);
            $("#produto option[value=" + produto + "]").prop("selected", true);
            $("#codigo_cnae").attr("value", codigo_cnae);
            $("#codigo_lc116").attr("value", codigo_lc116);
            $("#enviar_email_cliente option[value=" + enviar_email_cliente + "]").prop("selected", true);
        }

        function cancelar() {
            $(".modal-title").html("Integração Notazz");
            $("#salvar").css("display", "inline");
            $("#editar").css("display", "none");
            $("#resultado").css("display", "none");
            $("#id").attr({"value": ""});
            $("#api_notazz").attr("value", "");
            $("#data nota option").prop("selected", false);
            $("#produto option").prop("selected", false);
            $("#codigo_cnae").attr("value", "");
            $("#codigo_lc116").attr("value", "");
        }

        function excluir(id) {
            console.log(id);
            if(confirm("Tem certeza que deseja excluir essa api?")){
                $.ajax({
                    url: "<?=$router->route("notazz.excluir")?>",
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
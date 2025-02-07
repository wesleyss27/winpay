<?php

$v->layout("_theme");
$i = 1;

$status = array(
    1 => "EM ANÁLISE",
    2 => "APROVADO",
    3 => "REPROVADO",
    4 => "BLOQUEADO"
);
$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);

if ($produto->usuario != $section->getId()) {
    header("Location: " . url("produtos/detalhe/") . $produto->id);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 style="font-family:inter"><?= $title ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>

                        <li class="breadcrumb-item active">URLS</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
        <div class="callout menu-edicao">
            <?php if ($section->permissaoModuloBool("meus_produtos")) : ?>
                <a href="<?= url("produtos/detalhe/") . $produto->id; ?>">Detalhes</a>
            <?php endif ?>
            <?php if ($produto->usuario == $section->getId()) :
                $v->insert("produtos/menu-produto", ["produto" => $produto]);
            endif; ?>
            <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->

            <?php if ($section->permissaoModuloBool("campanha_produto")) : ?>
                <a href="<?= url("produtos/campanhas/") . $produto->id; ?>">Campanhas</a>
            <?php endif ?>
        </div>

        <!-- Default box -->
        <div class="card">
            <div class="card-header">

                <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                    <button disabled class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <span class="badge bg-danger">Produto REPROVADO/BLOQUEADO</span>
                <?php else : ?>
                    <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <?php endif ?>
                <?php endif; ?>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>
            <div class="card-body p-0">
                <?php if ($urls) : ?>
                    <script>
                        var dados = [];
                    </script>
                    <div style="overflow-x: auto;">

                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr style="font-family:inter">
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Url</th>
                                    <th scope="col">Privada</th>
                                    <th scope="col">Editar/Excluir</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($urls as $url) : ?>
                                    <tr style="font-family:inter">
                                        <th><?= $url->descricao; ?></th>
                                        <td><a href="<?= $url->url; ?>"><?= $url->url; ?></a></td>
                                        <td><?= $ativo[$url->privada]; ?></td>
                                        <?php if ($url->usuario ==  $section->getId()) : ?>
                                            <td>
                                                <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                    <button class="btn btn-warning btn-sm" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                                <?php endif ?>
                                                
                                                <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                    <button class="btn btn-danger btn-excluir btn-sm" onclick="excluir(<?= $url->id; ?>)"><i class="fas fa-times"></i></button>
                                                <?php endif ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>

                                    <script>
                                        dados[<?= $i ?>] = {
                                            id: "<?= $url->id ?>",
                                            url: "<?= $url->url ?>",
                                            descricao: "<?= $url->descricao ?>",
                                            privada: "<?= $url->privada ?>",
                                            pv: "<?= $url->pv ?>",
                                        }
                                    </script>

                                <?php $i++;
                                endforeach; ?>
                            </tbody>
                        </table>

                    <?php
                    //EXIBE A NAVEGAÇÃO DO PAGINADOR
                    $link = URL_BASE . "/produtos/urls/" . $url->produto . "/";

                    //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                    $paginador->paginas($total, $pagina, $link);

                else :
                    ?>
                        <h4>Nenhuma url encontrada!</h4>
                    <?php
                endif;
                    ?>

                    <!-- cadastar -->
                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar URLS</h3>
                                    <button type="button" class="close" data-dismiss="modal"> &times;</button>
                                </div>

                                <form action="#" method="post" style="font-family:inter; font-size:12px">
                                    <div class="modal-body" style="background: #f4f6f9">

                                        <div class="card card-body">
                                            <input type="hidden" name="id" id="id">
                                            <input type="hidden" name="usuario" value="<?= $_SESSION["id"] ?>">
                                            <input type="hidden" name="produto" value="<?= $produto->id ?>">

                                            <div class="row">
                                                <div class="col-12">
                                                    <label>Descrição*</label>
                                                    <input type="text" name="descricao" id='descricao' class="form-control input-lg" placeholder="Descrição">
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-12">
                                                    <label style="margin-top:10px">URL*</label>
                                                    <input type="text" name="url" id='url' class="form-control input-lg" placeholder="URL">
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-6">
                                                    <br />
                                                    <label>
                                                        <input type="checkbox" name="privada" id='privada'>
                                                        Url privada
                                                    </label>

                                                    <label>
                                                        <input type="checkbox" name="pv" id='pv'>
                                                        Página de vendas
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                                        <button class="btn btn-primary" id="salvar">Salvar</button>
                                        <button class="btn btn-warning" id="editar">Editar</button>
                                    </div>
                                </form>

                            </div>

                        </div>

                    </div>
                    <!-- end cadastrar -->


                    </div>

            </div>


    </section>

</div>

<!-- INICIO BOTÃO AJUDA E SUAS FUNCIONALIDADES -->
<button class="btn btn-primary btn-ajuda" id="btn-ajuda">
    <i class="far fa-question-circle"></i> <span class="desktop">Ajuda</span>
</button>

<?php
$assunto = "Preciso de Ajuda";
$para = "suporte.oficial@redrocketpay.com";
$mensagem = "Email: {$_SESSION["email"]} %0D" .
    "ID: {$_SESSION["id"]} %0D" .
    "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
    "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda. Estou com o seguinte problema: ";

$mensagem_whats = "Email: {$_SESSION["email"]} %0D" .
    "ID: {$_SESSION["id"]} %0D" .
    "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
    "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda.";
?>

<div class="ajuda">
    <div class="ajuda-titulo">
        Assistente
        <button type="button" class="close" id="ajuda-fechar"> &times;</button>
    </div>

    <div class="ajuda-corpo">
        <div class="mensagens">Olá <?= $_SESSION["nome_abreviado"] ?>, tudo bem? Como posso te ajudar?</div>
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/url//" target="_blank">Dúvidas sobre o Cadastro de URL's</a></div>
        <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
        <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
    </div>
</div>
<!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

<?= $v->start("scripts") ?>
<script>
    // INICIO BOTÃO DE AJUDA
    $("#btn-ajuda").click(() => {
        $(".ajuda").toggle("slow");
    });

    $("#ajuda-fechar").click(() => {
        $(".ajuda").hide("slow");
    });
    // FIM BOTÃO DE AJUDA

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
        if (salvar == 1) {
            destino = "<?= $router->route("produtos.urlCadastro"); ?>";
        } else {
            destino = "<?= $router->route("produtos.urlEditar") ?>";
        }

        var descricao = $("#descricao").val(),
            url = $("#url").val();

        var form = $("form");

        if (descricao == "" || url == "") {

            alert("Preecha todos os dados necessários");

        } else {

            $.ajax({

                url: destino,
                data: form.serialize(),
                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
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
        var pv = (dados[i]["pv"] == 1) ? true : false;

        $(".modal-title").html("Editar Url");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr({
            "value": id
        });
        $("#url").attr({
            "value": url
        });
        $("#descricao").attr({
            "value": descricao
        });
        $("#privada").prop({
            "checked": privada
        });
        $("#pv").prop({
            "checked": pv
        });
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#id").attr({
            "value": ""
        });
        $("#url").attr({
            "value": ""
        });
        $("#descricao").attr({
            "value": ""
        });
        $("#privada").prop({
            "checked": false
        });
    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa url?")) {
            $.ajax({
                url: "<?= $router->route("produtos.urlExcluir") ?>",
                data: {
                    id: id
                },

                type: "POST",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback);
                    if (callback == "Excluido com sucesso") {
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
<?= $v->end() ?>
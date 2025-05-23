<?php
$v->layout("_theme");

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

                        <li class="breadcrumb-item active">Afiliação</li>
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
        <section class="card">
            <div class="card-body">
                Configuração de afiliação somente para transações pela WinPay, não incluso para drop.

                <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                    <span class="badge bg-danger">Produto REPROVADO/BLOQUEADO</span>
                <?php endif; ?>
            </div>
        </section>

        <!--table class="table table-striped projects" style="font-size:13px"-->

        <form action="" method="POST">
            <input type="hidden" name="id" id="id" value="<?= $produto->id ?>">
            <div class="card card-body">
                <div class="row form-group">
                    <div class="col-md-4">
                        <label>Abrir para afiliados?</label>
                        <select name="abrir_afiliacao" id="abrir_afiliacao" class="form-control" onchange="mostrar_afiliacao()">
                            <option value="0" <?= ($produto->abrir_afiliacao == 0) ? 'selected' : '' ?>>Não</option>
                            <option value="1" <?= ($produto->abrir_afiliacao == 1) ? 'selected' : '' ?>>Sim</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="box-salvar1">
                        <label style="color: #fff">|</label><br>
                        <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                            <button disabled class="btn btn-success" id="btn-salvar1">Salvar</button>
                        <?php else : ?>
                            <button class="btn btn-success" id="btn-salvar1">Salvar</button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div id="mostrar_afiliacao" class="form-group">
                <div class="card card-body">

                    <div class="row">

                        <div class="col-md-4">
                            <label>Tipo de aprovação: *</label>
                            <select name="afiliados_aprovacao" id="afiliados_aprovacao" class="form-control">
                                <option value="1" <?= ($produto->afiliados_aprovacao == 1) ? 'selected' : '' ?>>Todos podem promover meu produto</option>
                                <option value="0" <?= ($produto->afiliados_aprovacao == 0) ? 'selected' : '' ?>>Somente afiliados aprovados por mim</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>URL de convite de afiliado:</label>
                            <div class="input-group">
                                <input type="text" value="<?= url("produtos/detalhe/$produto->id?convite=true") ?>" class="form-control">
                                <div class="input-group-append">
                                    <a href="<?= url("produtos/detalhe/$produto->id?convite=true") ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label>Visualização no MarketPlace: *</label>
                            <select name="visualizacao" id="visualizacao" class="form-control">
                                <option value="1" <?= ($produto->visualizacao == 1) ? 'selected' : '' ?>>Sim, Qualquer um pode ver meu produto</option>
                                <option value="0" <?= ($produto->visualizacao == 0) ? 'selected' : '' ?>>Não, Somente a quem eu enviar convite</option>
                            </select>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Dados do comprador: *</label>
                            <select name="libera_comprador" id="libera_comprador" class="form-control">
                                <option value="0" <?= ($produto->libera_comprador == 0) ? 'selected' : '' ?>>Não</option>
                                <option value="1" <?= ($produto->libera_comprador == 1) ? 'selected' : '' ?>>Sim</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Tempo de cookie: *</label>
                            <input type="text" name="tempo_cookie" id="cookie" value="<?= $produto->tempo_cookie; ?>" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Comissão: *</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="tipo_comissao" id="tipo_comissao" class="form-control">
                                        <option value="0" <?= ($produto->tipo_comissao == 0) ? 'selected' : '' ?>>%</option>
                                        <option value="1" <?= ($produto->tipo_comissao == 1) ? 'selected' : '' ?>>R$</option>
                                    </select>
                                </div>

                                <div class="col-md-8">
                                    <input type="text" name="comissao_afiliado" id="comissao" value="<?= $produto->comissao_afiliado; ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Tipo de atribuição: *</label>
                            <select name="tipo_atribuicao" id="tipo_atribuicao" class="form-control">
                                <option value="1" <?= ($produto->tipo_atribuicao == 1) ? 'selected' : '' ?>>Primeiro Clique</option>
                                <option value="2" <?= ($produto->tipo_atribuicao == 2) ? 'selected' : '' ?>>Último Clique</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Incluir Também</label>
                            <select name="usuarios" id="usuarios" class="form-control input-lg">
                                <option value="novos">Novos afiliados</option>
                                <option value="antigos">Todos os afiliados</option>
                            </select>
                            <small>Informe se deseja que as configurações sejam aplicadas somente a novos afiliados ou aos antigos também</small>

                        </div>
                    </div>

                </div>

                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label style="margin-top:10px">Descrição para o afiliado*</label>
                            <textarea name="descricao_afiliados" rows="5" class="form-control form-control" id="descricao_afiliados" required style="resize:none"><?= $produto->descricao_afiliados ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label style="margin-top:10px">Termos de afiliação do produto*</label>
                            <textarea name="termos" rows="5" class="form-control form-control" id="termos" placeholder="Termo de Afiliação" required style="resize:none"><?= $produto->termos ?></textarea>
                        </div>
                    </div>

                    <hr>
                    <div class="row" id="box-salvar2">
                        <div class="col-md-12">
                            <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                                <button disabled class="btn btn-success" id="btn-salvar2">Salvar</button>
                            <?php else : ?>
                                <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                    <button class="btn btn-success" id="btn-salvar2">Salvar</button>
                                <?php endif ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <br>

        <!-- Default box -->
        <div class="card">
            <div class="card-body p-0">
                <?php if ($afiliacao) : ?>

                    <script>
                        var dados = [];
                    </script>
                    <div style="overflow-x: auto">

                        <table class="table table-striped projects" style="font-size:13px">
                            <thead>
                                <tr>
                                    <th scope="col">Afiliado</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Comissão</th>
                                    <th scope="col">Desde</th>
                                    <th scope="col">Dados Comprador</th>
                                    <th scope="col">Vendas</th>
                                    <th scope="col">Editar/Excluir</th>
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

                                function formataComissao($tipo, $valor)
                                {
                                    if ($tipo == 0) {
                                        return number_format($valor, 2, ",", ".") . "%";
                                    } else {
                                        return "R$ " . number_format($valor, 2, ",", ".");
                                    }
                                }
                                foreach ($afiliacao as $afiliacao) :
                                    $venda = $vendas->find("afiliado = $afiliacao->afiliado AND produto = $produto->id AND status_transacao = 4")->count();
                                    $dados = $usuarioDados->find("usuario = $afiliacao->afiliado")->fetch();
                                ?>
                                    <tr class="cli" id="afiliacao<?= $afiliacao->afiliado; ?>">
                                        <td><?= $afiliacao->dataAfiliado()->nome . " <br> " . $afiliacao->dataAfiliado()->email . " <br> " . @$dados->telefone; ?></td>
                                        <td><?= $statusAfiliacao[$afiliacao->status]; ?></td>
                                        <td id="comissao"><?= formataComissao($afiliacao->tipo_comissao, $afiliacao->comissao_afiliado); ?></td>
                                        <td><?= formataData($afiliacao->created_at); ?></td>
                                        <td><?= @$ativo[$afiliacao->liberar_comprador]; ?></td>
                                        <td><a href="<?= url("vendas?afiliado=$afiliacao->afiliado&status=4&produto=$produto->id") ?>" target="_blank"><?= $venda; ?></a></td>
                                        <td>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#modal-lg" data-toggle="modal" onclick="editar(<?= $i; ?>)"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluir(<?= $afiliacao->id; ?>)"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </td>
                                    </tr>

                                    <script>
                                        dados[<?= $i ?>] = {
                                            id: "<?= $afiliacao->id ?>",
                                            email: "<?= $afiliacao->dataAfiliado()->email ?>",
                                            nome: "<?= $afiliacao->dataAfiliado()->nome ?>",
                                            status: "<?= $afiliacao->status ?>",
                                            status_afiliacao: "<?= $statusAfiliacao[$afiliacao->status] ?>",
                                            senha: "<?= $afiliacao->senha ?>",
                                            liberar_comprador: "<?= $afiliacao->liberar_comprador ?>",
                                            tempo_cookie: "<?= $afiliacao->tempo_cookie ?>",
                                            tipo_comissao: "<?= $afiliacao->tipo_comissao ?>",
                                            comissao_afiliado: "<?= $afiliacao->comissao_afiliado ?>",
                                            tipo_atribuicao: "<?= $afiliacao->tipo_atribuicao ?>",
                                            termos: "<?= $afiliacao->termos ?>",
                                            descricao_afiliados: "<?= $afiliacao->descricao_afiliados ?>",
                                        }
                                    </script>
                                <?php $i++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <h4>Você ainda não tem afiliados para esse produto</h4>
                <?php endif; ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </section>

</div>


</section>

</div>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Afiliação</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form action="" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                <div class="modal-body" style="background: #f4f6f9">
                    <div class="card card-body">
                        <input type="hidden" name="afiliacao_id" id="afiliacao_id">

                        <strong>Afiliado: </strong><span id="afiliado"></span><br>
                        <strong>Email: </strong><span id="email"></span><br>

                        <label id="st1">Status do Afiliado </label>
                        <span id="st"></span>
                    </div>

                    <div class="card card-body">
                        <div id="esconde">
                            <label>Status do afiliado</label>
                            <select name="status" id="status1" class="form-control input-lg" required="required">
                                <option disabled selected>Status</option>
                                <option value="1">Pendente</option>
                                <option value="2">Aprovado</option>
                                <option value="3">Reprovado ou Inativado</option>
                                <option value="4">Bloqueado</option>
                            </select>

                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Dados do Comprador</label>
                                    <select name="liberar_comprador" id="liberar_comprador1" class="form-control input-lg" required="required">
                                        <option disabled selected>Dados Comprador</option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Tempo de cookie</label>
                                    <input type="text" name="tempo_cookie" id='tempo_cookie1' class="form-control input-lg" placeholder="Tempo de cookie">
                                </div>
                            </div>

                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label> Comissão </label>
                                    <div class="input-group">
                                        <select name="tipo_comissao" id="tipo_comissao1 form-control" class="">
                                            <option value="0">%</option>
                                            <option value="1">R$</option>
                                        </select>

                                        <input type="text" name="comissao_afiliado" id="comissao_afiliado1" class="form-control input-lg">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label> Tipo de Atribuição de Comissao </label>
                                    <select name="tipo_atribuicao" id="tipo_atribuicao1" class="form-control input-lg" required="required">
                                        <option disabled selected>Atribuição de Comissao</option>
                                        <option value="1">Primeiro Clique</option>
                                        <option value="2">Último Clique</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar1">Cancelar</button>
                    <button class="btn btn-primary" id="salvar1">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end cadastrar -->

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
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/editar-produto/" target="_blank">Dúvidas sobre a Afiliação</a></div>
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

    $("#comissao").blur(function(e) {
        var valor = $("#comissao").val();
        $("#comissao").val(valor.replace(",", "."));
    });

    $("#comissao_afiliado1").blur(function(e) {
        var valor = $("#comissao_afiliado1").val();
        $("#comissao_afiliado1").val(valor.replace(",", "."));
    });

    var afiliacao = $("#abrir_afiliacao").val();
    if (afiliacao == 0) {
        $("#mostrar_afiliacao").css("display", "none");
        $("#box-salvar1").css("display", "block");
    } else {
        $("#mostrar_afiliacao").css("display", "block");
        $("#box-salvar1").css("display", "none");
    }

    function mostrar_afiliacao() {
        var mos = $("#abrir_afiliacao").val();
        if (mos == 1) {
            $("#mostrar_afiliacao").css("display", "block");
            $("#box-salvar1").css("display", "none");
        } else {
            $("#mostrar_afiliacao").css("display", "none");
            $("#box-salvar1").css("display", "block");
        }

        afiliacao = 1;
    }

    $("#btn-salvar1, #btn-salvar2").click(function(e) {
        e.preventDefault();

        console.log(afiliacao);
        if (afiliacao == 1) {
            var cookie = $("#cookie").val();
            var comissao = $("#comissao").val();

            if (cookie == "" || comissao == "") {
                alert("Preencha todos os campos");
            } else {

                var id = $("#id").val(); // ID DO PRODUTO
                var abrir_afiliacao = $("#abrir_afiliacao").val();
                var afiliados_aprovacao = $("#afiliados_aprovacao").val();
                var visualizacao = $("#visualizacao").val();
                var libera_comprador = $("#libera_comprador").val();
                var tempo_cookie = $("#cookie").val();
                var tipo_comissao = $("#tipo_comissao").val();
                var comissao_afiliado = $("#comissao").val();
                var tipo_atribuicao = $("#tipo_atribuicao").val();
                var usuarios = $("#usuarios").val();
                var termos = $("#termos").val();
                var descricao_afiliados = $("#descricao_afiliados").val();

                var form = $("form");

                console.log(id, abrir_afiliacao, afiliados_aprovacao, descricao_afiliados);

                $.ajax({
                    url: "<?= $router->route("produtos.afiliados1") ?>",
                    data: {
                        id: id,
                        abrir_afiliacao: abrir_afiliacao,
                        afiliados_aprovacao: afiliados_aprovacao,
                        visualizacao: visualizacao,
                        libera_comprador: libera_comprador,
                        tempo_cookie: tempo_cookie,
                        tipo_comissao: tipo_comissao,
                        comissao_afiliado: comissao_afiliado,
                        tipo_atribuicao: tipo_atribuicao,
                        usuarios: usuarios,
                        termos: termos,
                        descricao_afiliados: descricao_afiliados
                    },
                    //data: form.serialize(),
                    type: "POST",
                    beforeSend: function() {
                        console.log("atualizando");
                    },
                    error: function() {
                        console.log("O servidor não conseguiu processar o pedido");
                    },
                    success: function(callback) {
                        console.log(callback);
                        if (callback == "Salvo com sucesso") {
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
        } else {
            var id = $("#id").val();
            var abrir_afiliacao = $("#abrir_afiliacao").val();

            $.ajax({
                url: "<?= $router->route("produtos.afiliados") ?>",
                data: {
                    id: id,
                    abrir_afiliacao: abrir_afiliacao
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
                    if (callback == "Salvo com sucesso") {
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
    });

    function editar(i) {
        cancelar();

        var id = dados[i]["id"];
        var email = dados[i]["email"];
        var nome = dados[i]["nome"];
        var status = dados[i]["status"];
        var status_afiliacao = dados[i]["status_afiliacao"];
        var liberar_comprador = dados[i]["liberar_comprador"];
        var tempo_cookie = dados[i]["tempo_cookie"];
        var tipo_comissao = dados[i]["tipo_comissao"];
        var comissao_afiliado = dados[i]["comissao_afiliado"];
        var tipo_atribuicao = dados[i]["tipo_atribuicao"];

        console.log(id, email, nome, status, liberar_comprador,
            tempo_cookie, tipo_comissao, comissao_afiliado, tipo_atribuicao);

        console.log($("#status").val());

        $(".modal-title").html("Editar Usuário " + nome);
        $("#afiliacao_id").attr({
            "value": id
        });
        $("#email").html(email);
        $("#afiliado").html(nome);

        $("#st").html(status_afiliacao);

        if (status == 4) {
            $("#esconde").hide();
            $("#salvar1").hide();
        } else {
            $("#status1 option[value=" + status + "]").attr("selected", "true");
            $("#liberar_comprador1 option[value=" + liberar_comprador + "]").attr("selected", "true");
            $("#tempo_cookie1").attr({
                "value": tempo_cookie
            });
            $("#tipo_comissao1 option[value=" + tipo_comissao + "]").attr("selected", "true");
            $("#comissao_afiliado1").attr({
                "value": comissao_afiliado
            });
            $("#tipo_atribuicao1 option[value=" + tipo_atribuicao + "]").attr("selected", "true");
        }

    }

    function cancelar() {
        $("#esconde").show();
        $("#salvar1").show();
        $("#st").html("");

        $("#afiliacao_id").attr({
            "value": ""
        });
        $("#email").html();
        $("#afiliado").html();
        $("#status1").attr("selected", "true");
        $("#liberar_comprador1").attr("selected", "true");
        $("#tempo_cookie1").attr({
            "value": ""
        });
        $("#tipo_comissao1").attr("selected", "true");
        $("#comissao_afiliado1").attr({
            "value": ""
        });
        $("#tipo_atribuicao1").attr("selected", "true");
    }

    $("#salvar1").click(function(e) {
        e.preventDefault();

        if ($("#status1").val() == 4 && confirm("Tem certeza que deseja bloquear esse afiliado? Essa ação não poderá ser revertida")) {
            salvar();
        } else {
            salvar();
        }
    });

    function salvar() {

        var form = $("form");

        $.ajax({

            url: "<?= $router->route("appafiliados.editar") ?>",
            data: form.serialize(),
            type: "POST",
            dataType: "json",
            beforeSend: function() {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {

                console.log(callback);

                jsonString = JSON.stringify(callback, null, 2);
                jsonString = jsonString.replace(/^"|"$/g, '');

                alert(jsonString);

                $('form').each(function() {
                    this.reset();
                });
                $("#myModal").modal("hide");
                location.reload();
            }

        });
    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir essa afiliação?")) {
            $.ajax({
                url: "<?= $router->route("afiliacao.excluir") ?>",
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
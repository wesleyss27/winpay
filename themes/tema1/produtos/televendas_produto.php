<?php $v->layout("_theme"); ?>

<?php
if ($produto->usuario != $_SESSION["id"]) {
    header("Location: " . url("produtos/televendas/") . $produto->id);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Cadastrar Produto</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style="padding:20px">

        <!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
        <div class="callout menu-edicao">
            <a href="<?= url("produtos/detalhe/") . $produto->id; ?>">Detalhes</a>
            <?php if ($produto->usuario == $_SESSION["id"]) :
                $v->insert("produtos/menu-produto", ["produto" => $produto]);
            endif; ?>
            <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
            <a href="<?= url("produtos/campanhas/") . $produto->id; ?>">Campanhas</a>
        </div>

        <?php if ($produto->status != 0 && $produto->status != 1) : ?>
            <form action="<?= $router->route("televendas.cadastro") ?>" method="post" enctype="multipart/form-data" style="font-family:inter; text-transform:uppercase; font-size:11px">

                <!-- Passando código do produto de forma oculta -->
                <input type="hidden" name="produto_id" value="<?= $produto->id ?>">

                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-5">
                                <b>Habilitar produto para o Televendas?</b>
                                <br><br>
                                <select name="ativo" id="usar_notificacoes" class="form-control">
                                    <option value="0" <?= (@$produto->habilitar_televendas == 0) ? 'selected' : '' ?>>Não</option>
                                    <option value="1" <?= (@$produto->habilitar_televendas == 1) ? 'selected' : '' ?>>Sim</option>
                                </select>

                                <br>

                                * Caso opte por habilitar o televendas, você deve criar os checkouts e planos que serão sincronizados com o televendas <br><br>
                                * Será cobrado uma taxa de 20% por venda paga além da taxa de transação.
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mostrar_notificacoes">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-6">
                                <b>Selecione os eventos para enviar ao Televendas:</b>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="checkbox" name="evento[0]" value="1" id="evento1" checked> <big>Aguardando Pagamento</big> <br>
                                        <input type="checkbox" name="evento[1]" value="2" id="evento2" checked> <big>Em Análise</big> <br>
                                        <input type="checkbox" name="evento[2]" value="3" id="evento3" checked> <big>Pré-autorizada</big> <br>
                                        <input type="checkbox" name="evento[9]" value="10" id="evento10" checked> <big>Carrinho Abandonado (produtor)</big> <br><br><br><br>
                                    </div>

                                    <div class="col-md-4">
                                        <input type="checkbox" name="evento[3]" value="4" id="evento4" checked> <big>Paga</big> <br>
                                        <input type="checkbox" name="evento[4]" value="5" id="evento5" checked> <big>Cancelada</big> <br>
                                        <input type="checkbox" name="evento[5]" value="6" id="evento6" checked> <big>Devolvida</big> <br>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="checkbox" name="evento[6]" value="7" id="evento7" checked> <big>Chargeback</big> <br>
                                        <input type="checkbox" name="evento[7]" value="8" id="evento8" checked> <big>Concluída</big> <br>
                                        <input type="checkbox" name="evento[8]" value="9" id="evento9" checked> <big>Bloqueada</big> <br>
                                    </div>

                                    <div class="row">
                                        <span><b>Comissão Afiliado:</b></span>
                                        <div class="col-md-2">
                                            <select name="tipo_comissao1" id="tipo_comissao1" class="form-control">
                                                <option value="0" <?= ($produto->tipo_comissao_afiliado == 0) ? 'selected' : ''  ?>>%</option>
                                                <option value="1" <?= ($produto->tipo_comissao_afiliado == 1) ? 'selected' : ''  ?>>R$</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <input type="text" name="comissao1" id="comissao1" class="form-control" value="<?= ($produto->comissao_afiliado_televendas != "") ? $produto->comissao_afiliado_televendas : ''  ?>" placeholder="0,00">
                                            <br>
                                        </div>
                                        <span>*Atenção! Se não quiser que o afiliado ganhe comissão digite 0</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">
                            <?php if (@$produto->descricao_televendas) : ?>
                                <label>Descrição detalhada para a equipe de Televendas</label>
                                <textarea rows="6" class="form-control" id="descricao" name="descricao" required><?= $produto->descricao_televendas ?></textarea>
                            <?php else : ?>
                                <label>Descrição detalhada para a equipe de Televendas</label>
                                <textarea rows="6" class="form-control" id="descricao" name="descricao"></textarea>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Fim Card Descrição -->

                    <div class="card">
                        <div class="cardy-body">
                            <!-- DOCUMENTOS -->
                            <div class="col-md-5" style="align-items:center">
                                <label style="margin-top:25px">Documentos técnicos do produto</label> <small>Tamanho máximo: 10 mb</small><br>
                                Arquivos suportados: <span style="text-transform:lowercase;"> png, jpg, pdf, zip, rar </span>

                                <!-- Verifica se existe documento-->
                                <?php if (@$produto->documentos_televendas) : ?>
                                    <input type="file" name="documentos" class="form-control"><br>
                                    <input type="hidden" name="doc_atual" value="<?= $produto->documentos_televendas ?>">
                                <?php else : ?>
                                    <input type="file" name="documentos" class="form-control"><br>
                                <?php endif; ?>

                                <label class="form-label">Documento atual:</label><br>
                                <?php if (@$produto->documentos_televendas) : ?>
                                    <a href="<?= url("uploads/documentos/") . $produto->documentos_televendas ?>" target="_blank"><?= $produto->documentos_televendas ?></a>
                                <?php endif; ?>
                                <!-- Fim verificação de documento -->

                            </div>
                            <!-- FIM DOCUMENTOS -->
                        </div>
                    </div>
                </div>

                <hr>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-primary" id="salvar"><i class="fas fa-check"></i> Atualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php else : ?>
            <div class="card card-body">
                Poderá habilitar após o produto ser aprovado
            </div>
        <?php endif; ?>
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
    <div class="mensagens"><a href="https://octapay.com.br/tutoriais/televendas/" target="_blank">Dúvidas sobre o Televendas</a></div>
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
    // Eventos selecionados pelo usuario que estão gravados no banco
    eventos_televendas = '<?= $produto->eventos_televendas ?>';
    eventos_televendas = eventos_televendas.split(",");

    eventos_televendas.forEach((indice, valor) => {
        $("#evento" + indice).prop("checked", true);
        console.log("eventos_televendas");
    });

    var televendas = $("#usar_notificacoes").val();

    function televendas() {
        var televendas = $("#usar_notificacoes").val();
        if (televendas == 1) {
            $(".mostrar_notificacoes").css("display", "inline");
        } else {
            $(".mostrar_notificacoes").css("display", "none");
        }
    }

    $("#comissao1").blur(function(e) {
        var valor = $("#comissao1").val();
        $("#comissao1").val(valor.replace(",", "."));
    });

    // $produto = $("#usar_notificacoes").val();
    $("#usar_notificacoes").change((e) => {
        var televendas = $("#usar_notificacoes").val();
        console.log(televendas);
        if (televendas == 1) {
            $(".mostrar_notificacoes").css("display", "inline");
            // televendas = 0;
        } else {
            $(".mostrar_notificacoes").css("display", "none");
            // televendas = 1;
        }
    });

    if (televendas == 0) {
        $(".mostrar_notificacoes").css("display", "none");

    } else {
        $(".mostrar_notificacoes").css("display", "inline");

    }

    // $("#salvar").click((e)=> {
    //     e.preventDefault();
    //     salvarEditar(1);
    // });

    //     function salvarEditar(salvar) {
    //         if (salvar == 1) {
    //             url = "<?= $router->route("televendas.cadastro"); ?>";
    //         } else {
    //             url = "<?= $router->route("televendas.editar"); ?>";
    //         }

    //         var form = $("form");
    //         console.log(url);

    //         $.ajax({

    //             url: url,
    //             data: form.serialize(),
    //             type: "POST",
    //             datatype: "json",
    //             beforeSend: function() {
    //                 console.log("atualizando");
    //             },
    //             error: function() {
    //                 console.log("O servidor não conseguiu processar o pedido");
    //             },
    //             success: function(callback) {
    //                 //callback = JSON.parse(callback);
    //                 console.log("callback ", callback);

    //                 if (callback == "Atualizado Com Sucesso!") {
    //                     // location.reload();
    //                     // location.href = "<?= URL_BASE . '/produtos/detalhe/' ?>" + callback.id;
    //                 }
    //             }

    //         });
    //     }
</script>
<?= $v->end() ?>
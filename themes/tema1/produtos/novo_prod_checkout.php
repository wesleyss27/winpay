<?php
$v->layout("_theme");
if ($produto->usuario != $section->getId()) {
    header("Location: " . url("produtos/detalhe/") . $produto->id);
}

$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);

$order_bump = array(
    1 => "SIM",
    0 => "NÃO"
);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-10">
                    <h4 style="font-family:inter"><?= $title ?></h4>
                </div>
                <div class="col-sm-2">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Checkout</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <ul id="progressbar">
                        <li class="active" id="step1"><strong>Cadastrar Produto</strong></li>
                        <li class="active" id="step2"><strong>Cadastrar Plano</strong></li>
                        <li class="active" id="step3"><strong>Cadastrar Checkout</strong></li>
                    </ul>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <style>
        /* Modernização local - mantém a paleta WinPay (amarelo #FFDD00) */
        :root {
            --winpay-primary: #FFDD00;
            --winpay-primary-700: #D4B800;
            --winpay-bg-elevated: #0b0b0b;
            --winpay-bg-surface: #101010;
            --winpay-border: rgba(255, 255, 255, 0.08);
            --winpay-text: #f2f2f2;
            --winpay-text-dim: #bdbdbd;
            --winpay-focus: rgba(255, 221, 0, 0.35);
            --winpay-shadow: 0 10px 30px rgba(0,0,0,0.35);
        }

        /* Breadcrumb (existente) deixa mais elegante sem mudar markup */
        .breadcrumb {
            background: transparent;
            margin: 0;
            padding: 0;
        }

        /* Cards */
        .card {
            background: linear-gradient(135deg, var(--winpay-bg-surface) 0%, #060606 100%);
            border: 1px solid var(--winpay-border);
            border-radius: 14px;
            box-shadow: var(--winpay-shadow);
            overflow: hidden;
        }
        .card > .card-body { padding: 18px; }

        /* Inputs */
        .form-control {
            background: #111;
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--winpay-text);
            border-radius: 10px;
            height: 43px;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .form-control::placeholder { color: #8c8c8c; }
        .form-control:focus {
            background: #141414;
            color: #fff;
            border-color: rgba(255, 221, 0, 0.45);
            box-shadow: 0 0 0 3px var(--winpay-focus);
        }

        label { color: var(--winpay-text); font-weight: 500; }
        small { color: var(--winpay-text-dim); }

        /* Callouts */
        .callout.callout-primary {
            background: linear-gradient(145deg, rgba(255, 221, 0, 0.08), rgba(255, 221, 0, 0.02));
            border-left: 4px solid var(--winpay-primary);
            border-radius: 12px;
            border: 1px solid rgba(255, 221, 0, 0.2);
            color: var(--winpay-text);
        }

        /* Botões */
        .btn.btn-primary {
            background: linear-gradient(135deg, var(--winpay-primary) 0%, #ffe866 100%);
            color: #1a1a1a;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .btn.btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(255, 221, 0, 0.25);
            filter: brightness(1.02);
        }
        .btn.btn-outline-primary {
            border: 1px solid var(--winpay-primary);
            color: var(--winpay-primary);
            border-radius: 10px;
            background: transparent;
        }
        .btn.btn-outline-primary:hover {
            background: rgba(255, 221, 0, 0.1);
            color: var(--winpay-primary);
            box-shadow: 0 6px 18px rgba(255,221,0,0.18);
        }

        /* Progressbar existente (#progressbar) */
        #progressbar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            counter-reset: step;
            padding: 0;
            margin: 10px 0 0;
            list-style: none;
        }
        #progressbar li {
            position: relative;
            text-align: center;
            color: var(--winpay-text-dim);
            font-weight: 600;
            background: linear-gradient(145deg, #0f0f0f, #0a0a0a);
            border: 1px solid var(--winpay-border);
            padding: 12px 8px;
            border-radius: 12px;
            transition: all .25s ease;
        }
        #progressbar li::before {
            counter-increment: step;
            content: counter(step);
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 8px;
            background: rgba(255, 221, 0, 0.15);
            color: var(--winpay-primary-700);
            border: 1px solid rgba(255,221,0,0.25);
            box-shadow: inset 0 0 10px rgba(255,221,0,0.08);
        }
        #progressbar li.active {
            color: var(--winpay-primary);
            border-color: rgba(255,221,0,0.35);
            box-shadow: 0 8px 26px rgba(255,221,0,0.12);
        }
        #progressbar li.active::before {
            background: var(--winpay-primary);
            color: #1a1a1a;
            border-color: var(--winpay-primary);
        }

        /* Checkbox rascunho */
        .rascunho.alert {
            background: linear-gradient(145deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 12px;
            color: var(--winpay-text);
        }

        /* Seções de upload */
        input[type="file"].form-control { background: #111; }

        /* Ajuda (botão flutuante) */
        .btn-ajuda {
            border-radius: 999px;
            background: linear-gradient(135deg, var(--winpay-primary) 0%, #ffe866 100%);
            color: #151515;
            border: none;
            box-shadow: 0 10px 24px rgba(255,221,0,0.28);
            font-weight: 700;
        }
        .ajuda {
            background: linear-gradient(135deg, #0f0f0f, #0a0a0a);
            border: 1px solid var(--winpay-border);
            border-radius: 14px;
            color: var(--winpay-text);
            box-shadow: var(--winpay-shadow);
        }
        .ajuda-titulo { color: var(--winpay-primary); }
        .mensagens a { color: var(--winpay-primary); }
    </style>

    <!-- Main content -->
    <section class="content">
        <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
        <!-- Default box -->


        <form action="<?= $router->route("produtos.checkoutCadastro") ?>" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <input type="hidden" name="id" id="id">

                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nome: </label>
                            <i class="fas fa-question-circle" title="Essa descrição serve para informar o afiliado sobre o que é o link"></i>
                            <input type="text" class="form-control" id="descricao" name="descricao" required>
                        </div>

                        <div class="col-md-6">
                            <label>Produto: </label>
                            <select name="produto" id="produto" class="form-control">
                                <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                            </select>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Plano: </label> <i class="fas fa-question-circle" title="Se não selecionado, será aplicado as configurações de preço do produto"></i>
                            <select name="plano" id="plano" class="form-control">
                                <option value="">Plano...</option>
                                <?php if ($plano) : foreach ($plano as $plano) : $plano->privado == 1 ? $privado = "**" : $privado = ""; ?>

                                        <option value="<?= $plano->id ?>"><?= "$plano->codigo $plano->nome $privado" ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                            <small>** plano privado </small>
                        </div>

                        <div class="col-md-6">
                            <label>Checkout privado? </label>
                            <i class="fas fa-question-circle" title="Se sim, somente você pode ver esse link e enviar para os clientes"></i>

                            <select name="privado" id="privado" class="form-control">
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Modelo do checkout:</label>
                            <select name="modelo_checkout" id="modelo_checkout" class="form-control">
                                <option value="3" selected>Modelo Padrão</option>
                                <option value="4">Modelo 2</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <img src="<?= url("themes/tema1/images/modelo3.png") ?>" alt="" id="imagem_checkout" width="100%" style="border-radius: 10%;">
                        </div>
                    </div>
                </div>

                <hr>
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Banner Topo: 870 X 272 pixels</label>
                            <i class="fas fa-question-circle" title="Se não for escolhido o banner uma imagem padrão ficará no lugar"></i>

                            <input type="file" class="form-control" id="banner" name="banner">
                            <input type="hidden" class="form-control" id="banner_atual" name="banner_atual">
                            <input type="hidden" class="form-control" id="ban_atual" name="ban_atual">
                            <small>Caso a imagem possua .(ponto) no nome, favor remover os pontos</small><br>
                            <small>imagem de banner principal que ficará no topo da tela</small>

                            <button id="excluir-banner" class="close text-danger" title="Remover imagem atual">&times;</button>
                            <img src="" alt="" id="imagem_banner_atual" width="100%">
                        </div>

                        <div class="col-md-6">
                            <label>Banner Topo 2: 870 X 107 pixels</label>
                            <input type="file" class="form-control" id="aviso" name="aviso">
                            <input type="hidden" class="form-control" id="aviso_atual" name="aviso_atual">
                            <input type="hidden" class="form-control" id="avi_atual" name="avi_atual">
                            <small>Caso a imagem possua .(ponto) no nome, favor remover os pontos</small><br>
                            <small>imagem que ficará abaixo do banner principal</small>

                            <button id="excluir-aviso" class="close text-danger" title="Remover imagem atual">&times;</button>
                            <img src="" alt="" id="imagem_aviso_atual" width="100%">
                        </div>
                    </div>

                    <div id="lateral">
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Banner Lateral: 380 X 600 pixels</label>
                                <i class="fas fa-question-circle" title="Se não for escolhido o banner uma imagem padrão ficará no lugar"></i>
                                <input type="file" class="form-control" id="baner_lateral" name="baner_lateral">
                                <input type="hidden" class="form-control" id="banner_lateral" name="banner_lateral">
                                <input type="hidden" class="form-control" id="ban_lateral" name="ban_lateral">
                                <small>Caso a imagem possua .(ponto) no nome, favor remover os pontos</small><br>
                                <small>imagem de banner lateral que ficará no lado direito da tela</small>

                                <!-- <button id="excluir-banner-lateral" class="close text-danger" title="Remover imagem atual">&times;</button> -->
                                <img src="" alt="" id="imagem_banner_lateral" width="100%">
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="card card-body">
                    <!-- <div class="row">
                                <div class="col-md-6">
                                    <label>Ativar checkout para televendas: <span class="badge bg-azul-dark">Em breve</span></label>
                                    <select name="televendas" id="televendas" class="form-control">
                                        <option value="0">Não</option>
                                        <option value="1">Sim</option>
                                    </select>
                                </div>
                            </div>
                            <br> -->
                    <div class="row">
                        <div class="col-md-6">
                            <label>Escolha o pagamento aceito:</label>
                            <div class="row" style="text-align:center; margin-top:7px">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="10" name="meio_pagamento[10]" id="meio_pagamento10" checked>
                                        <label class="form-check-label" for="meio_pagamento1">
                                            Todos
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="meio_pagamento[1]" id="meio_pagamento1">
                                        <label class="form-check-label" for="meio_pagamento2">
                                            Cartão
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="2" name="meio_pagamento[2]" id="meio_pagamento2">
                                        <label class="form-check-label" for="meio_pagamento3">
                                            Boleto
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="3" name="meio_pagamento[3]" id="meio_pagamento3">
                                        <label class="form-check-label" for="meio_pagamento4">
                                            PIX
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Ativar preço do produto no checkout</label>
                            <div class="row" style="text-align:center; margin-top:4px;">
                                <div class="col-md-6">
                                    <select name="mostrar_preco" id="mostrar_preco" class="form-control">
                                        <option value="0">Não</option>
                                        <option value="1">Sim</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <img src="<?= url("themes/imgs/preco_produto.PNG?" . time()) ?>" style="padding-left: 10px" width="100%" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <b>Endereço Obrigatório:</b><br>
                            <select name="endereco_obrigatorio" id="endereco_obrigatorio" class="form-control">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card card-body">
                    <div class="rol">
                        <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                            <button type="submit" class="btn btn-primary" style="float:right" id="next-step">Finalizar Cadastro <i class="fas fa-check"></i></button>
                        <?php endif ?>
                        
                        &nbsp;
                        <button class="btn btn-finalizar-tarde" style="float:left" id="finish-later">Finalizar mais tarde <i class="far fa-clock"></i></button>
                    </div>
                </div>

        </form>

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
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/cadastro-de-checkout-para-um-novo-produto/" target="_blank">Dúvidas sobre o cadastro de checkout</a></div>
        <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
        <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
    </div>
</div>
<!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->


<?= $v->start("scripts") ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="<?= url("themes/tema1/dist/js/mascara-dinheiro.js") ?>"></script>
<script src="<?= url("themes/tema1/dist/js/mascara-peso.js") ?>"></script>

<script>
    $("#btn-ajuda").click(() => {
        $(".ajuda").toggle("slow");
    });

    $("#ajuda-fechar").click(() => {
        $(".ajuda").hide("slow");
    });

    $(".select2").select2({
        width: '100%',
        theme: "classic"
    });

    $("#finish-later").click(() => {
        // alert("clicou");
        location.href = "<?= url("produtos/detalhe/" . $produto->id) ?>";
        // location.reload();
    });

    $("#valor_cupom").blur(function(e) {
        var valor = $("#valor_cupom").val();
        $("#valor_cupom").val(valor.replace(",", "."));
    });

    $("#aumento").blur(function(e) {
        var valor = $("#aumento").val();
        $("#aumento").val(valor.replace(",", "."));
    });

    $("#meio_pagamento1,#meio_pagamento2,#meio_pagamento3").click(() => {
        $("#meio_pagamento10").prop("checked", false);
    });

    $("#meio_pagamento10").click(() => {
        $("#meio_pagamento1").prop("checked", false);
        $("#meio_pagamento2").prop("checked", false);
        $("#meio_pagamento3").prop("checked", false);
    });

    // INICIO SESSÃO FRETES
    $(".frete_sedex").css("display", "none");
    $(".frete_jadlog").css("display", "none");
    $(".frete_regiao").css("display", "none");
    $(".mostrar_fixo").css("display", "none");
    $(".img_envelope").css("display", "none");

    var frete = 0;
    $(".mostrar_fixo").css("display", "none");
    $(".img_envelope").css("display", "none");


    function tipoEntrega(formato) {
        /* formato = 0 (Caixa/Pacote)
         *  formato == 1 (Envelope)
         */
        if (formato == 1) {
            $(".peso_alt_larg").css("display", "none");
            $(".img_envelope").css("display", "inline");

        } else {
            $(".peso_alt_larg").css("display", "inline");
            $(".img_envelope").css("display", "none");

        }
    }

    function tipoFrete(frete) {
        /*  frete == 0 (Sem Frente)
         *  frete == 1 (Frete Fixo)
         *  frete == 2 (Frete Correios)
         *  frete == 3 (Frete Jadlog)
         *  frete == 4 (Frete por Região)
         */
        if (frete == 1) {
            $(".frete_sedex").css("display", "none");
            $(".frete_regiao").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".mostrar_fixo").css("display", "inline");
            frete = 1;
        } else if (frete == 2) {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_regiao").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".frete_sedex").css("display", "inline");
            frete = 2;
        } else if (frete == 3) {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_sedex").css("display", "none");
            $(".frete_regiao").css("display", "none");
            $(".frete_jadlog").css("display", "inline");
        } else if (frete == 4) {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_sedex").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".frete_regiao").css("display", "inline");
        } else {
            $(".mostrar_fixo").css("display", "none");
            $(".frete_jadlog").css("display", "none");
            $(".frete_sedex").css("display", "none");
            $(".frete_regiao").css("display", "none");
        }
    }

    $("#usar_frete").change((e) => {
        var frete = $("#usar_frete").val();
        tipoFrete(frete);
    });


    $("#formato_sedex").change((e) => {
        var formato = $("#formato_sedex").val();
        tipoEntrega(formato);
    });

    function validaLarg(larg) {
        larg = parseFloat(larg);
        if (larg < 10) {
            alert("A largura não pode ser menor que 10 cm.");
            // $("#largura_sedex").focus();
        }
    }

    function validaComp(comp) {
        // console.log(larg);
        comp = parseFloat(comp);
        if (comp < 15) {
            alert("O comprimento não pode ser menor que 15 cm.");

        }
    }

    function validaAlt(alt) {
        // console.log(larg);
        alt = parseFloat(alt);
        if (alt < 1) {
            alert("A altura não pode ser menor que 1 cm.");

        }
    }

    // INICIO SESSAO VALIDAÇÕES DE CEP E PRAZOS DE ENTREGA DOS FRETES
    function validaCep(valor_cep, cep) {
        var valor_cep = valor_cep.replace(/\D/g, '');
        var validacep = /^[0-9]{8}$/;
        if (validacep.test(valor_cep)) {
            $(cep).val(valor_cep);
        } else {
            $(cep).val('');
            alert("CEP inválido.");
        }
    }

    function validaDias(valor_dias, tipo_frete) {
        var valor_dias = valor_dias.replace(/\D/g, '');
        $(tipo_frete).val(valor_dias);
    }
    // FIM SESSAO VALIDAÇÕES

    // INICIO SESSÃO MASCARAS CORREIOS

    $("#valor_frete").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#valor_padrao_sedex").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#largura_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#comprimento_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#altura_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#peso_sedex").maskWeight({
        integerDigits: 3,
        decimalDigits: 3,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });


    // FIM SESSÃO MASCARAS CORREIOS

    // INICIO SESSÃO MASCARAS JADLOG
    $("#valor_padrao_jadlog").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#largura_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#comprimento_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#altura_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 2,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });

    $("#peso_jadlog").maskWeight({
        integerDigits: 3,
        decimalDigits: 3,
        decimalMark: ',',
        //initVal default: generated
        //roundingZeros default: true
    });
    // FIM SESSÃO MASCARAS JADLOG

    // INICIO SESSÃO MASCARAS FRETE POR REGIAO
    $("#regiao_norte").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_nordeste").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_centro_oeste").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_sudeste").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });

    $("#regiao_sul").maskMoney({
        prefix: "R$ ",
        decimal: ",",
        thousands: "."
    });
    // FIM SESSÃO MASCARAS CORREIOS

    // FIM SESSÃO FRETES

    var order_bump = 0;
    $(".mostrar_order_bump").css("display", "none");

    $("#usar_order_bump").change((e) => {
        if (order_bump == 0) {
            $(".mostrar_order_bump").css("display", "inline");
            order_bump = 1;
        } else {
            $(".mostrar_order_bump").css("display", "none");
            order_bump = 0;
        }
    });

    $("#excluir-banner").hide();
    $("#excluir-aviso").hide();
    $("#excluir-banner-lateral").hide();
    $("#lateral").hide();

    $("#excluir-banner").click((e) => {
        e.preventDefault();
        $("#banner_atual").attr("value", "");
        $("#imagem_banner_atual").hide("slow");
        $("#excluir-banner").hide("slow");
    });

    $("#excluir-aviso").click((e) => {
        e.preventDefault();
        $("#aviso_atual").attr("value", "");
        $("#imagem_aviso_atual").hide("slow");
        $("#excluir-aviso").hide("slow");
    });



    if (banner_lateral) {
        $("#excluir-banner-lateral").show();
        $("#imagem_banner_lateral").attr("src", "");
    }

    function modelo() {
        var modelo = $("#modelo_checkout").val();

        var model = {
            1: "modelo1",
            2: "modelo2",
            3: "modelo3",
            4: "modelo4",
        }

        $("#imagem_checkout").prop("src", "<?= url("themes/tema1/images/") ?>" + model[modelo] + ".png");

        if (modelo == 4) {
            $("#lateral").show();
        } else {
            $("#lateral").hide();
        }
    }

    $("#modelo_checkout").change(() => {
        modelo();
    });
</script>
<?= $v->end() ?>
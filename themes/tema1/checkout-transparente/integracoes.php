<?php

$v->layout("_theme");
$i = 1;

$status = array(
    1 => "bg-warning",
    2 => "bg-success",
    3 => "bg-danger",
    4 => "bg-danger"
);
$ativo = array(
    1 => "SIM",
    0 => "NÃO"
);

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
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
                    <h4 style="font-family:inter"><b><?= $title ?></b></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Checkout Transparente</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <?php if (!@$usuarioDados) : ?>
        <section class="content">
            <div class="card">
                <div class="card-body">
                    <p>
                    <h2>Você ainda não tem permissão para utilizar Dropshipping!</h2><br> Caso queira transacionar via modelo Dropshipping, entre em contato com o suporte via E-mail ou Whatsapp.<br>
                    <b>E-mail:</b><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank"> suporte.oficial@redrocketpay.com</a><br>
                    <b>Whatsapp:</b><a href="https://api.whatsapp.com/send?phone=5533998787884&text=<?= $mensagem_whats ?>" target="_blank"> (33) 99878-7884</a></p>
                </div>
            </div>
        </section>
    <?php else : ?>
        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                        <?php if ($section->getVerificado() == 1) : ?>
                            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-cartao" style="font-family:inter" id="cadastro-cartao">Cadastrar Cartão</button>
                        <?php else : ?>
                            Antes de cadastrar um cartão você precisa verificar sua conta (<?= print_r($_SESSION) ?>)
                        <?php endif; ?>
                    <?php endif ?>
                </div>

                <div class="card-body">
                    <div class="row">
                        <?php if ($cartoes) : ?>
                            <?php foreach ($cartoes as $cartao) : ?>
                                <div class="col-md-4">
                                    <div class="card card-body <?= $status[$cartao->status] ?>">

                                        <?php if ($contarCartao > 1 || $cartao->status == 3) : ?>
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <div style="margin: -20px -15px 0 0px;"><button data-toggle="modal" data-target="#modal-excluir" class="btn-close text-white" title="Excluir Cartão" onclick="excluir(<?= $cartao->id ?>, 'cartao')">&times;</button></div>
                                            <?php endif ?>
                                        <?php endif; ?>

                                        <div class="row <?= $status[$cartao->status] ?>">
                                            <div class="col-md-2 col-2">
                                                <br>
                                                <img src="<?= url("themes/tema1/images/chip.png"); ?>" alt="" width="100%" id="mastercard">
                                            </div>
                                            <div class="col-md-7 col-7">
                                                **** **** **** <?= $cartao->ultimos_digitos; ?><br>
                                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                <?= base64_decode($cartao->mes) . "/" . base64_decode($cartao->ano); ?><br>
                                                <?= strtoupper($cartao->titular); ?>
                                            </div>
                                            <div class="col-md-3 col-3">
                                                <img src="<?= url("themes/imgs/bandeiras/$cartao->bandeira.png"); ?>" alt="" width="100%" id="mastercard">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            Você não possui nenhum cartão cadastrado
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Após cadastrar suas credencias em algum dos serviços abaixo você poderá escolher no checkout a forma de transacionar. <br>
                    Necessário cadastrar as credenciais de produção e as taxas aplicadas no parcelamento. <br>
                    As configurações de afiliado e coprodução não se aplicam a esse meio de transacionar
                </div>
                <div class="card-body" style="background: #f4f6f9">
                    <div class="row">
                        <?php if ($adquirentes && $cartoes) : $i = 0; ?>
                            <script>
                                var dados = [];
                            </script>

                            <?php foreach ($adquirentes as $adquirente) :
                                $pagarme = $adquirente->adquirente == "pagarme" ? @$pagarme + 1 : @$pagarme + 0;
                                $wirecard = $adquirente->adquirente == "wirecard" ? @$wirecard + 1 : @$wirecard + 0;
                                $cielo = $adquirente->adquirente == "cielo" ? @$cielo + 1 : @$cielo + 0;
                                $pagseguro = $adquirente->adquirente == "pagseguro" ? @$pagseguro + 1 : @$pagseguro + 0;
                                $mercadopago = $adquirente->adquirente == "mercadopago" ? @$mercadopago + 1 : @$mercadopago + 0;
                            ?>
                                <script>
                                    dados["<?= $adquirente->adquirente ?>"] = {
                                        id_adquirente: "<?= $adquirente->id ?>",
                                        status: "<?= $adquirente->status ?>",
                                        adquirente: "<?= $adquirente->adquirente ?>",
                                        nome_fatura: "<?= $adquirente->nome_fatura ?>",
                                        boleto_cielo: "<?= $adquirente->boleto_cielo ?>",
                                        pix_redrocket: "<?= $adquirente->pix_redrocket ?>",
                                        chave: "<?= $adquirente->chave ?>",
                                        token: "<?= $adquirente->token ?>",
                                        taxa1: "<?= $adquirente->taxa1 ?>",
                                        taxa2: "<?= $adquirente->taxa2 ?>",
                                        taxa3: "<?= $adquirente->taxa3 ?>",
                                        taxa4: "<?= $adquirente->taxa4 ?>",
                                        taxa5: "<?= $adquirente->taxa5 ?>",
                                        taxa6: "<?= $adquirente->taxa6 ?>",
                                        taxa7: "<?= $adquirente->taxa7 ?>",
                                        taxa8: "<?= $adquirente->taxa8 ?>",
                                        taxa9: "<?= $adquirente->taxa9 ?>",
                                        taxa10: "<?= $adquirente->taxa10 ?>",
                                        taxa11: "<?= $adquirente->taxa11 ?>",
                                        taxa12: "<?= $adquirente->taxa12 ?>"
                                    }
                                </script>
                        <?php $i++;
                            endforeach;
                        else :
                            $pagarme = 0;
                            $wirecard = 0;
                            $cielo = 0;
                            $pagseguro = 0;
                            $mercadopago = 0;
                        endif; ?>
                    </div>

                    <div class="row">
                        <?php if ($contarCartao > 0) : ?>
                            <div class="col-md-3">

                                <div class="card card-body">
                                    <?php if (@$pagarme > 0) : ?>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <div id="btn-close-pagarme" style="margin: -20px -15px 0 0px;"><button class="close text-danger" title="Excluir integração" data-toggle="modal" data-target="#modal-excluir" onclick="excluir('pagarme', 'adquirente')">&times;</button></div>
                                        <?php endif ?>
                                    <?php endif; ?>
                                    <div style="float: right; width: 20px; margin: -15px 0">
                                        <span class="badge bg-azul-octa">
                                            <?= @$pagarme ?>
                                        </span>
                                    </div>
                                    <button class="btn btn-link" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="btn_pagarme" onclick="adquirente('pagarme', <?= $pagarme ?>)">
                                        <img src="<?= url("themes/imgs/pagamentos/pagarme.png") ?>" alt="" width="100%">
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-body">
                                    <?php if (@$wirecard > 0) : ?>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <div id="btn-close-wirecard" style="margin: -20px -15px 0 0px;"><button class="close text-danger" title="Excluir integração" data-toggle="modal" data-target="#modal-excluir" onclick="excluir('wirecard', 'adquirente')">&times;</button></div>
                                        <?php endif ?>
                                    <?php endif; ?>
                                    <div style="float: right; width: 20px; margin: -15px 0">
                                        <span class="badge bg-azul-octa">
                                            <?= @$wirecard ?>
                                        </span>
                                    </div>
                                    <button class="btn btn-link" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="btn_wirecard" onclick="adquirente('wirecard', <?= $wirecard ?>)">
                                        <img src="<?= url("themes/imgs/pagamentos/wirecard.png") ?>" alt="" width="100%">
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-body">
                                    <?php if (@$cielo > 0) : ?>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <div id="btn-close-cielo" style="margin: -20px -15px 0 0px;"><button class="close text-danger" title="Excluir integração" data-toggle="modal" data-target="#modal-excluir" onclick="excluir('cielo', 'adquirente')">&times;</button></div>
                                        <?php endif ?>
                                    <?php endif; ?>
                                    <div style="float: right; width: 20px; margin: -15px 0">
                                        <span class="badge bg-azul-octa">
                                            <?= @$cielo ?>
                                        </span>
                                    </div>
                                    <button class="btn btn-link" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="btn_cielo" onclick="adquirente('cielo', <?= $cielo ?>)">
                                        <img src="<?= url("themes/imgs/pagamentos/cielo.png") ?>" alt="" width="100%">
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-body">
                                    <?php if (@$pagseguro > 0) : ?>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <div id="btn-close-pagseguro" style="margin: -20px -15px 0 0px;"><button class="close text-danger" title="Excluir integração" data-toggle="modal" data-target="#modal-excluir" onclick="excluir('pagseguro', 'adquirente')">&times;</button></div>
                                        <?php endif ?>
                                    <?php endif; ?>
                                    <div style="float: right; width: 20px; margin: -15px 0">
                                        <span class="badge bg-azul-octa">
                                            <?= @$pagseguro ?>
                                        </span>
                                    </div>
                                    <button class="btn btn-link" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="btn_pagseguro" onclick="adquirente('pagseguro', <?= $pagseguro ?>)">
                                        <img src="<?= url("themes/imgs/pagamentos/pagseguro.png") ?>" alt="" width="100%">
                                    </button>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="card card-body">
                                Você precisar cadastrar um meio de pagamento para configurar um adquirente.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <?php if ($contarCartao > 0) : ?>
                            <div class="col-md-3">
                                <div class="card card-body">
                                    <?php if (@$mercadopago > 0) : ?>
                                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                            <div id="btn-close-mercadopago" style="margin: -20px -15px 0 0px;"><button class="close text-danger" title="Excluir integração" data-toggle="modal" data-target="#modal-excluir" onclick="excluir('mercadopago', 'adquirente')">&times;</button></div>
                                        <?php endif ?>
                                    <?php endif; ?>
                                    <div style="float: right; width: 20px; margin: -15px 0">
                                        <span class="badge bg-azul-octa">
                                            <?= @$mercadopago ?>
                                        </span>
                                    </div>
                                    <button class="btn btn-link" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="btn_mercadopago" onclick="adquirente('mercadopago', <?= $mercadopago ?>)">
                                        <img src="<?= url("themes/imgs/pagamentos/mercadopago.png") ?>" alt="" width="100%">
                                    </button>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="card card-body">
                                Você precisar cadastrar um meio de pagamento para configurar um adquirente.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </section>
    <?php endif; ?>

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
            <div class="mensagens"><a href="https://octapay.com.br/tutoriais/como-usar-checkout-transparente-na-octapay/" target="_blank">Dúvidas sobre o Checkout Transparente</a></div>
            <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
            <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998787884&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
        </div>
    </div>
    <!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

</div>

<!-- CARTÃO DE CRÉDITO -->
<div class="modal fade" id="modal-cartao">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Cartão de Crédito</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form name="form_cartao" action="#" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="id" id="id">

                    <div class="card card-body bg-danger">
                        Preencha corretamente os dados abaixo. <br>
                        Dados necessários para que possamos fazer a cobrança da taxa de transação. <br>
                        Confira seus dados, caso tenha algum dado incorreto, corrija-o!
                    </div>
                    <div class="card card-body">

                        <h5>Dados de Cobrança</h5>

                        <div class="row">
                            <div class="col-md-4">
                                Nome Completo:
                                <input type="text" name="nome" id='nome' class="form-control input-lg" value="<?= $usuarioDados->nome ?>">
                            </div>

                            <div class="col-md-4">
                                Email:
                                <input type="text" name="email" id='email' class="form-control input-lg" value="<?= $usuarioDados->email ?>">
                            </div>

                            <div class="col-md-4">
                                Telefone/Celular:
                                <input type="text" name="telefone" id='telefone' class="form-control input-lg" value="<?= $usuarioDados->telefone ?>"><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                Tipo de Documento
                                <select name="tipo_doc" id="tipo_doc" class="form-control input-lg">
                                    <option value="1">CPF</option>
                                    <option value="2">CNPJ</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                CPF/CNPJ:
                                <input type="text" name="cpf" id='cpf' class="form-control input-lg" value="<?= $usuarioDados->cpf_cnpj ?>">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h5>Endereço de Cobrança</h5>
                        <div class="row">
                            <div class="col-md-4">
                                CEP:
                                <input type="text" name="cep" id='cep' class="form-control input-lg" value="<?= $usuarioDados->cep ?>">
                            </div>

                            <div class="col-md-6">
                                Rua:
                                <input type="text" name="rua" id='rua' class="form-control input-lg" value="<?= $usuarioDados->rua ?>">
                            </div>

                            <div class="col-md-2">
                                Número:
                                <input type="text" name="numero" id='numero' class="form-control input-lg" value="<?= $usuarioDados->numero ?>"><br>
                            </div>
                        </div>

                        <br>

                        <div class="row">
                            <div class="col-md-6">
                                Complemento:
                                <input type="text" name="complemento" id='complemento' class="form-control input-lg" value="<?= $usuarioDados->complemento ?>">
                            </div>

                            <div class="col-md-6">
                                Bairro:
                                <input type="text" name="bairro" id='bairro' class="form-control input-lg" value="<?= $usuarioDados->bairro ?>"><br>
                            </div>
                        </div>

                        <br>

                        <div class="row">
                            <div class="col-md-6">
                                Cidade:
                                <input type="text" name="cidade" id='cidade' class="form-control input-lg" value="<?= $usuarioDados->cidade ?>">
                            </div>

                            <div class="col-md-3">
                                Estado:
                                <!--input type="text" name="estado" id='estado' class="form-control input-lg"-->
                                <?= $v->insert("usuarios/uf", ["estado" => ""]); ?>
                            </div>

                            <div class="col-md-3">
                                País:
                                <input type="text" name="pais" id='pais' class="form-control input-lg" value="<?= $usuarioDados->pais ?>">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                        <h5>Dados do cartão</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Número do Cartão:</label>
                                <input type="text" name="numcart" id='numero_cartao' class="form-control input-lg">
                            </div>

                            <div class="col-md-6">
                                <label id="lbl-token">Titular:</label>
                                <input type="text" name="titular" id='titular' class="form-control input-lg"><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label>Validade:</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" name="mes-val" id="mes" class="form-control input-lg" placeholder="Mês">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="ano-val" id="ano" class="form-control input-lg" placeholder="Ano">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label id="lbl-token">CVV:</label>
                                <input type="text" name="cvv" id='cvv' class="form-control input-lg"><br>
                            </div>

                            <div class="col-md-3">
                                <label id="lbl-token">Bandeira:</label>
                                <input type="text" name="bandeira" id='bandeira' class="form-control input-lg"><br>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar_cartao">Cancelar</button>
                    <button class="btn btn-primary" id="salvar_cartao">Salvar</button>
                    <button class="btn btn-warning" id="editar_cartao">Editar</button>
                </div>
            </form>

        </div>

    </div>

</div>
<!-- FIM CARTÃO DE CRÉDITO -->

<!-- ADQUIRENTES -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i></h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form name="form_adquirente" action="#" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                <div class="modal-body" style="background: #f4f6f9">
                    <div class="card card-body">
                        <div class="row">
                            <input type="hidden" name="id_adquirente" id="id_adquirente">
                            <input type="hidden" name="adquirente" id="adquirente">

                            <div class="col-md-6" id="div-chave">
                                <label id="lbl-firstKey"></label>
                                <input type="text" name="chave" id='chave' class="form-control input-lg">
                            </div>

                            <div class="col-md-6">
                                <!-- <label id="lbl-criptografia">Chave de Criptografia:</label>
                                <label id="lbl-merchantid">MerchantId:</label>
                                <label id="lbl-token">Token:</label> -->
                                <label id="lbl-secondKey"></label>
                                <input type="text" name="token" id='token' class="form-control input-lg">
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label id="lbl-nome-fatura">Nome que aparecerá na fatura do cartão:</label>
                                <input type="text" name="nome_fatura" id='nome_fatura' maxlength="13" class="form-control input-lg"><br><br>
                            </div>

                            <div class="col-md-6" id="mostra-boleto" style="display:none;">
                                <label id="lbl-boleto">Selecionar tipo de boleto cielo:</label>
                                <select name="boleto_cielo" id="boleto_cielo" class="form-control" style="margin-top: -8px;">
                                    <option value="Bradesco2">Bradesco</option>
                                    <option value="BancoDoBrasil2">Banco do Brasil</option>
                                </select>
                            </div>
                            <!-- </div> -->
                        </div>

                        <div class="row" id="aviso-pixRredrocket">
                            <br><br>
                            <span><b>Atenção! Essa Adquirente não possui suporte via PIX. Como alternativa, você pode transacionar via PIX com a REDROCKET na configuração do Checkout.<br> Consulte a taxas aqui: <a href="<?= url("/usuarios/perfil/" . $_SESSION["id"]); ?>"> Consultar Taxas</a></b></span>
                        </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                        <label>Taxas de parcelamento:</label>
                        <div class="row">
                            <!-- <div class="col-md-3">
                                <label>1x: (%)</label>
                                <input type="text" name="taxa1" id='taxa1' class="form-control input-lg" placeholder="3.9">
                            </div> -->

                            <div class="col-md-3">
                                <label>2x: (%)</label>
                                <input type="text" name="taxa2" id='taxa2' class="form-control input-lg" placeholder="4.9">
                            </div>

                            <div class="col-md-3">
                                <label>3x: (%)</label>
                                <input type="text" name="taxa3" id='taxa3' class="form-control input-lg" placeholder="5.9">
                            </div>

                            <div class="col-md-3">
                                <label>4x: (%)</label>
                                <input type="text" name="taxa4" id='taxa4' class="form-control input-lg" placeholder="6.9">
                            </div>

                            <div class="col-md-3">
                                <label>5x: (%)</label>
                                <input type="text" name="taxa5" id='taxa5' class="form-control input-lg" placeholder="7.9">
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-3">
                                <label>6x: (%)</label>
                                <input type="text" name="taxa6" id='taxa6' class="form-control input-lg" placeholder="8.9">
                            </div>

                            <div class="col-md-3">
                                <label>7x: (%)</label>
                                <input type="text" name="taxa7" id='taxa7' class="form-control input-lg" placeholder="9.9">
                            </div>

                            <div class="col-md-3">
                                <label>8x: (%)</label>
                                <input type="text" name="taxa8" id='taxa8' class="form-control input-lg" placeholder="10.9">
                            </div>

                            <div class="col-md-3">
                                <label>9x: (%)</label>
                                <input type="text" name="taxa9" id='taxa9' class="form-control input-lg" placeholder="11.9">
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <label>10x: (%)</label>
                                <input type="text" name="taxa10" id='taxa10' class="form-control input-lg" placeholder="12.9">
                            </div>

                            <div class="col-md-3">
                                <label>11x: (%)</label>
                                <input type="text" name="taxa11" id='taxa11' class="form-control input-lg" placeholder="13.9">
                            </div>

                            <div class="col-md-3">
                                <label>12x: (%)</label>
                                <input type="text" name="taxa12" id='taxa12' class="form-control input-lg" placeholder="14.9">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                    <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                        <button class="btn btn-primary" id="salvar_adquirente">Salvar</button>
                    <?php endif ?>

                    <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                        <button class="btn btn-warning" id="editar_adquirente">Editar</button>
                    <?php endif ?>
                </div>
            </form>

        </div>

    </div>

</div>
<!-- FIM ADQUIRENTES -->

<!-- EXCLUIR -->
<div class="modal fade" id="modal-excluir">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Excluir</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <div class="modal-body" style="background: #f4f6f9">
                <input type="hidden" name="id_obj" id="id_obj">
                <input type="hidden" name="url_excluir" id="url_excluir">

                <div class="card card-body">
                    <p id="msg_excluir"></p>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-success" data-dismiss="modal" id="cancelar">Cancelar</button>
                <button class="btn btn-danger" id="excluir_obj">Excluir</button>
            </div>

        </div>

    </div>

</div>
<!-- FIM EXCLUIR -->

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

    $("#cadastro-cartao").click(() => {
        $("#salvar_cartao").css("display", "inline");
        $("#editar_cartao").css("display", "none");
    });

    $("#numero_cartao").blur(() => {
        bandeira = tgdeveloper.getCardFlag($("#numero_cartao").val());
        $("#bandeira").attr("value", bandeira);
    })

    // FUNÇÃO PARA PEGAR BANDEIRA DO CARTÃO
    var tgdeveloper = {

        /**
         * getCardFlag
         * Return card flag by number
         *
         * @param cardnumber
         */
        getCardFlag: function(cardnumber) {
            var cardnumber = cardnumber.replace(/[^0-9]+/g, '');

            var cards = {
                visa: /^4[0-9]{12}(?:[0-9]{3})/,
                mastercard: /^5[1-5][0-9]{14}/,
                diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}/,
                amex: /^3[47][0-9]{13}/,
                discover: /^6(?:011|5[0-9]{2})[0-9]{12}/,
                hipercard: /^(606282\d{10}(\d{3})?)|(3841\d{15})/,
                elo: /^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})/,
                jcb: /^(?:2131|1800|35\d{3})\d{11}/,
                aura: /^(5078\d{2})(\d{2})(\d{11})$/
            };

            for (var flag in cards) {
                if (cards[flag].test(cardnumber)) {
                    return flag;
                }
            }

            return false;
        }

    }

    function adquirente(adquirente, count) {

        if (adquirente == "mercadopago") {
            var lbl_firstKey = "PUBLIC KEY";
            var lbl_secondKey = "ACCESS TOKEN";

            $("#mostra-boleto").hide();

        } else if (adquirente == "wirecard") {
            var lbl_secondKey = "Token";
            var lbl_firstKey = "Chave";

            $("#mostra-boleto").hide();

        } else if (adquirente == "pagseguro") {
            var lbl_secondKey = "Token";
            var lbl_firstKey = "";

            $("#mostra-boleto").hide();

        } else if (adquirente == "cielo") {
            var lbl_secondKey = "MerchantId";
            var lbl_firstKey = "MerchantKey";

            $("#mostra-boleto").show();

        } else if (adquirente == "pagarme") {
            var lbl_secondKey = "Chave Secreta";
            var lbl_firstKey = "Chave Pública";

            $("#mostra-boleto").hide();

        }

        if (adquirente == "pagseguro")
            $("#div-chave").css("display", "none");
        else
            $("#div-chave").css("display", "inline");

        // console.log(count);
        if (count > 0) {
            var id_adquirente = dados[adquirente]["id_adquirente"];
            var status = dados[adquirente]["status"];
            var chave = dados[adquirente]["chave"];
            var token = dados[adquirente]["token"];
            var nome_fatura = dados[adquirente]["nome_fatura"];
            var boleto_cielo = dados[adquirente]["boleto_cielo"];
            var pix_redrocket = dados[adquirente]["pix_redrocket"]
            var taxa1 = dados[adquirente]["taxa1"];
            var taxa2 = dados[adquirente]["taxa2"];
            var taxa3 = dados[adquirente]["taxa3"];
            var taxa4 = dados[adquirente]["taxa4"];
            var taxa5 = dados[adquirente]["taxa5"];
            var taxa6 = dados[adquirente]["taxa6"];
            var taxa7 = dados[adquirente]["taxa7"];
            var taxa8 = dados[adquirente]["taxa8"];
            var taxa9 = dados[adquirente]["taxa9"];
            var taxa10 = dados[adquirente]["taxa10"];
            var taxa11 = dados[adquirente]["taxa11"];
            var taxa12 = dados[adquirente]["taxa12"];

            // console.log(token);

            $(".card-title").html("<i class='fa fa-info-circle'></i> Editar " + adquirente);
            $("#lbl-firstKey").html(lbl_firstKey);
            $("#lbl-secondKey").html(lbl_secondKey);
            $("#salvar_adquirente").css("display", "none");
            $("#editar_adquirente").css("display", "inline");
            $("#cancelar").css("display", "inline");

            $("#id_adquirente").prop("value", id_adquirente);
            $("#adquirente").prop("value", adquirente);

            // console.log("ADQUIRENTE:", adquirente);

            if (adquirente == "mercadopago") {
                $("#lbl-nome-fatura").css("display", "none");
                $("#nome_fatura").css("display", "none");
            } else {
                $("#lbl-nome-fatura").css("display", "inline");
                $("#nome_fatura").css("display", "inline");
            }

            // AVISOS ADQUIRENTES
            if (adquirente == "pagarme") {
                console.log("entrou pagseguro");
                $("#aviso-pixRedrocket").html("<br><b>Atenção! Para correta utilização da PagarMe, é necessario que sua chave API seja 'v5'.<br> Além disso, é necessário configurar os Webhook's de notificações.</b><br><b>Clique aqui para seguir tutorial: <a href='https://octapay.com.br/tutoriais/tutorial-webhook-pagarme/'> Configurar Webhook's</a></b>");
                $("#aviso-pixRedrocket").css("display", "inline");
            } else if (adquirente == "cielo" || adquirente == "wirecard") {
                $("#lbl-pixRedrocket").css("display", "inline");
                $("#pix_redrocket").css("display", "inline");
                $("#aviso-pixRedrocket").html("<br><b>Atenção! Essa Adquirente não possui suporte via PIX. Como alternativa, você pode transacionar via PIX com a REDROCKET na configuração do Checkout.<br> Consulte a taxas aqui: <a href='<?= url("/usuarios/perfil/" . $_SESSION["id"]); ?> '> Consultar Taxas</a></b>");
                $("#aviso-pixRedrocket").css("display", "inline");
            } else {
                $("#lbl-pixRedrocket").css("display", "none");
                $("#pix_redrocket").css("display", "none");
                $("#aviso-pixRedrocket").css("display", "none");
            }
            // FIM AVISOS ADQUIRENTES

            if (adquirente == "cielo") {
                $("#boleto_cielo option[value=" + boleto_cielo + "]").prop("selected", true);
            }

            $("#nome_fatura").prop("value", nome_fatura);

            $("#pix_redrocket").prop("value", pix_redrocket);
            $("#chave").prop("value", chave);
            $("#token").prop("value", token);
            $("#taxa1").prop("value", taxa1);
            $("#taxa2").prop("value", taxa2);
            $("#taxa3").prop("value", taxa3);
            $("#taxa4").prop("value", taxa4);
            $("#taxa5").prop("value", taxa5);
            $("#taxa6").prop("value", taxa6);
            $("#taxa7").prop("value", taxa7);
            $("#taxa8").prop("value", taxa8);
            $("#taxa9").prop("value", taxa9);
            $("#taxa10").prop("value", taxa10);
            $("#taxa11").prop("value", taxa11);
            $("#taxa12").prop("value", taxa12);
        } else {
            // console.log(count);
            if (adquirente == "mercadopago") {
                $("#lbl-nome-fatura").css("display", "none");
                $("#nome_fatura").css("display", "none");
            } else {
                $("#lbl-nome-fatura").css("display", "inline");
                $("#nome_fatura").css("display", "inline");
            }

            // AVISOS ADQUIRENTES
            if (adquirente == "pagarme") {
                console.log("entrou pagseguro");
                $("#aviso-pixRredrocket").html("<br><b>Atenção! Para correta utilização da PagarMe, é necessario que sua chave API seja 'v5'.<br> Além disso, é necessário configurar os Webhook's de notificações.</b><br><b>Clique aqui para seguir tutorial: <a href='https://octapay.com.br/tutoriais/tutorial-webhook-pagarme/'> Configurar Webhook's</a></b>");
                $("#aviso-pixRredrocket").css("display", "inline");
            } else if (adquirente == "cielo" || adquirente == "wirecard") {
                $("#lbl-pixRredrocket").css("display", "inline");
                $("#pix_redrocket").css("display", "inline");
                $("#aviso-pixRredrocket").html("<br><b>Atenção! Essa Adquirente não possui suporte via PIX. Como alternativa, você pode transacionar via PIX com a REDROCKET na configuração do Checkout.<br> Consulte a taxas aqui: <a href='<?= url("/usuarios/perfil/" . $_SESSION["id"]); ?> '> Consultar Taxas</a></b>");
                $("#aviso-pixRredrocket").css("display", "inline");
            } else {
                $("#lbl-pixRredrocket").css("display", "none");
                $("#pix_redrocket").css("display", "none");
                $("#aviso-pixRredrocket").css("display", "none");
            }
            // FIM AVISOS ADQUIRENTES

            $("#lbl-firstKey").html(lbl_firstKey);
            $("#lbl-secondKey").html(lbl_secondKey);
            $(".card-title").html("<i class='fa fa-info-circle'></i> Cadastrar " + adquirente);
            $("#salvar_adquirente").css("display", "inline");
            $("#editar_adquirente").css("display", "none");
            $("#cancelar").css("display", "inline");

            $("#id_adquirente").prop("value", "");
            $("#adquirente").prop("value", adquirente);
            $("#chave").prop("value", "");
            $("#token").prop("value", "");
            if (adquirente == "Mercado Pago") {
                $("#lbl-nome-fatura").css("display", "none");
                $("#nome_fatura").css("display", "none");
            }
            $("#nome_fatura").prop("value", "");
            $("#taxa1").prop("value", "");
            $("#taxa2").prop("value", "");
            $("#taxa3").prop("value", "");
            $("#taxa4").prop("value", "");
            $("#taxa5").prop("value", "");
            $("#taxa6").prop("value", "");
            $("#taxa7").prop("value", "");
            $("#taxa8").prop("value", "");
            $("#taxa9").prop("value", "");
            $("#taxa10").prop("value", "");
            $("#taxa11").prop("value", "");
            $("#taxa12").prop("value", "");
            // console.log($("#taxa12").val());
        }
    }

    $("#salvar_cartao").click(function(e) {
        e.preventDefault();
        var form = $("form[name=form_cartao]");
        salvarEditar("<?= $router->route("checkout.cartao"); ?>", form);
    });

    $("#editar_cartao").click(function(e) {
        e.preventDefault();
        salvarEditar("<?= $router->route("produtos.campanhaEditar") ?>");
    });

    $("#salvar_adquirente, #editar_adquirente").click(function(e) {
        e.preventDefault();
        var form = $("form[name=form_adquirente]");
        salvarEditar("<?= $router->route("checkout.adquirente"); ?>", form);
    });

    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });

    function salvarEditar(url, form) {

        $.ajax({

            url: url,
            data: form.serialize(),
            type: "POST",
            //datatype: "json",
            beforeSend: function() {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log("callback ", callback);
                alert(callback);
                location.reload();
            }

        });
    }

    function excluir(id, obj) {
        console.log(id, obj);

        if (obj == "cartao") {
            $("#msg_excluir").html("Tem certeza que deseja excluir esse cartão?");
            $("#id_obj").prop("value", id);
            $("#url_excluir").prop("value", "<?= $router->route("checkout.cartaoExcluir") ?>");
        } else if (obj == "adquirente") {
            $("#msg_excluir").html("Tem certeza que deseja excluir essa integração?");
            $("#id_obj").prop("value", id);
            $("#url_excluir").prop("value", "<?= $router->route("checkout.adquirenteExcluir") ?>");
        }
    }

    $("#excluir_obj").click((e) => {
        var id = $("#id_obj").val();
        var url = $("#url_excluir").val();
        excluirObjeto(id, url);
    })

    function excluirObjeto(id, url) {

        $.ajax({
            url: url,
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
                // console.log(callback);
                if (callback == "Excluído com sucesso") {
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
</script>

<?= $v->end() ?>
<?php
$v->layout("_theme");

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
                        <li class="breadcrumb-item active">Ecommerce</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">

        <!-- CARD BODY -->
        <form action="<?= $router->route("ecommerce.cadastrar") ?>" method="POST" enctype="multipart/form-data">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Habilitar Ecommerce</label>
                        <select name="habilitar" id="habilitar" class="form-control">
                            <option value="0" <?= @$ecommerce->habilitar == 0 ? "selected" : "" ?>>Não</option>
                            <option value="1" <?= @$ecommerce->habilitar == 1 ? "selected" : "" ?>>Sim</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="configuracoes">
                <hr>
                <style>
                    input[type="color"] {
                        visibility: hidden;
                    }

                    .input-color {
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        box-shadow: 2px 2px 2px #ddd;
                        padding: 2px;
                        border: 1px solid #bbb;
                    }

                    .input-color:hover {
                        cursor: pointer;
                    }

                    .box-labels {
                        /* display: flex;
                        align-items: center; */
                        border: 1px solid #ddd;
                        border-radius: 10px;
                        padding: 5px 10px;
                        margin: 10px 0;
                        box-shadow: 2px 2px 2px #ddd;
                    }

                    .inp-form {
                        border: none;
                        border-bottom: 1px solid #aaa;
                        border-radius: 5px;
                        background: transparent;
                        padding: 7px;
                    }

                    .inp-titulo {
                        font-weight: 600;
                        font-size: 22px;
                    }

                    .titulo-loja {
                        color: <?= @$ecommerce->cor_titulo ? @$ecommerce->cor_titulo : "#666" ?>
                    }

                    .texto-tarja {
                        color: <?= @$ecommerce->cor_destaque ? @$ecommerce->cor_destaque : "#fff" ?>
                    }

                    .bg-tarja {
                        background: <?= @$ecommerce->cor_tarja ? @$ecommerce->cor_tarja : "#888" ?>;
                    }

                    .tarja {
                        width: max-content;
                        padding: 5px 10px;
                        font-size: 20px;
                        font-weight: 600;
                        background: <?= @$ecommerce->cor_tarja ? @$ecommerce->cor_tarja : "#888" ?>;
                    }

                    .border-tarja {
                        border-bottom: 2px solid <?= @$ecommerce->cor_tarja ? @$ecommerce->cor_tarja : "#888" ?>;
                        margin-bottom: 10px;
                    }

                    .texto-loja {
                        color: <?= @$ecommerce->cor_texto ? @$ecommerce->cor_texto : "#666" ?>
                    }

                    .campo-logo {
                        width: 80px;
                    }

                    .change-image {
                        font-size: 20px;
                        font-weight: 600;
                        text-align: right;
                        float: right;
                        color: red;
                        /* margin-bottom: 0px; */
                        cursor: pointer;
                    }

                    .image-preview {
                        width: 60px;
                        height: 60px;
                        border-radius: 50%;
                    }

                    #imageInput,
                    #banner-input {
                        /* visibility: hidden; */
                        display: none;
                    }

                    .filelabel:hover {
                        cursor: pointer;
                    }

                    .banner-preview {
                        width: 100%;
                        height: 300px;
                        border-radius: 0;
                    }

                    .imagem-produto {
                        border-radius: 5px;
                        margin: 5px auto;
                        width: 70%;
                        display: block;
                    }

                    .txt-control {
                        border: none;
                        border-bottom: 1px solid #ddd;
                        border-radius: 5px;
                        min-height: 50px;
                    }

                    .btn-destaque {
                        font-weight: bold;
                        padding: 10px;
                        border-radius: 25px;
                        border: 3px solid rgba(255, 255, 255, .5);
                        background: <?= @$ecommerce->cor_botao ? @$ecommerce->cor_botao : "#888" ?>;
                        color: <?= @$ecommerce->cor_texto_botao ? @$ecommerce->cor_texto_botao : "#fff" ?>;
                    }
                </style>

                <div class="card card-body">
                    <p>Há uma cobrança para poder usar o nosso ecommerce. Essa cobrança é de <?= $taxa->taxa_ecommerce ?>% por venda paga. </p>
                    <!-- <div class="row">
                        <div class="col-md-4">
                            <div class="card card-body">
                                <div class="center">
                                    Taxa por transação <br>
                                    <h4><?= $taxa->taxa_ecommerce ?>% </h4>
                                </div>
                                <hr>
                                <span><i class="fas fa-check-circle txt-verde-octa3"></i> Todas as funcionalidades</span> <br>
                                <span><i class="fas fa-check-circle txt-verde-octa3"></i> Taxa aplicada por venda paga</span> <br>
                                <hr>

                                <button type="button" class="btn btn-success" onclick="taxaEcommerce('taxa', <?= $_SESSION['id'] ?>, <?= $taxa->taxa_ecommerce ?>)">Aceitar</button>
                            </div>
                        </div> -->

                    <!-- <div class="col-md-4">
                            <div class="card card-body">
                                <div class="center">
                                    Taxa fixa <br>
                                    <h4>R$ <?= number_format($taxa->valor_ecommerce, 2, ",", ".") ?></h4>
                                    <hr>
                                </div>

                                <span><i class="fas fa-check-circle txt-verde-octa3"></i> Todas as funcionalidades</span> <br>
                                <span><i class="fas fa-check-circle txt-verde-octa3"></i> Taxa fixa por mês</span> <br>
                                <span><i class="fas fa-check-circle txt-verde-octa3"></i> Taxa cobrada independente de vendas pagas</span> <br>
                                <hr>

                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-cartao">Escolher</button>
                            </div>
                        </div> -->
                    <!-- </div> -->
                </div>

                <div class="card card-body">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="row box-labels">
                                <label class="col-10">Cor das tarjas e do menu</label>
                                <div class="col-2">
                                    <label class="input-color bg-tarja" id="lbl-cor-tarja">
                                        <input type="color" name="cor_tarja" id="cor_tarja" class="box-color" value="<?= @$ecommerce->cor_tarja ? @$ecommerce->cor_tarja : "#888888" ?>"><br>
                                    </label>
                                </div><br>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row box-labels">
                                <label class="col-10">Cor do texto das tarjas</label>
                                <div class="col-2">
                                    <label class="input-color texto-tarja" id="lbl-cor-destaque">
                                        <input type="color" name="cor_destaque" id="cor_destaque" class="box-color" value="<?= @$ecommerce->cor_destaque ? @$ecommerce->cor_destaque : "#ffffff" ?>"><br>
                                    </label>
                                </div><br>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row box-labels">
                                <label class="col-10">Cor do texto</label>
                                <div class="col-2">
                                    <label class="input-color" id="lbl-cor-texto" style="background: <?= @$ecommerce->cor_texto ? @$ecommerce->cor_texto : "#666" ?>">
                                        <input type="color" name="cor_texto" id="cor_texto" class="box-color" value="<?= @$ecommerce->cor_texto ? @$ecommerce->cor_texto : "#666666" ?>"><br>
                                    </label>
                                </div><br>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row box-labels">
                                <label class="col-10">Texto destaque</label>
                                <div class="col-2">
                                    <label class="input-color" id="lbl-cor-titulo" style="background: <?= @$ecommerce->cor_titulo ? @$ecommerce->cor_titulo : "#666" ?>">
                                        <input type="color" name="cor_titulo" id="cor_titulo" class="box-color" value="<?= @$ecommerce->cor_titulo ? @$ecommerce->cor_titulo : "#666666" ?>">
                                    </label>
                                </div><br>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row box-labels">
                                <label class="col-10">Cor dos botões</label>
                                <div class="col-2">
                                    <label class="input-color" id="lbl-cor-botao" style="background: <?= @$ecommerce->cor_botao ? @$ecommerce->cor_botao : "#888" ?>">
                                        <input type="color" name="cor_botao" id="cor_botao" class="box-color" value="<?= @$ecommerce->cor_botao ? @$ecommerce->cor_botao : "#888888" ?>">
                                    </label>
                                </div><br>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row box-labels">
                                <label class="col-10">Cor do texto dos botões</label>
                                <div class="col-2">
                                    <label class="input-color" id="lbl-cor-texto-botao" style="background: <?= @$ecommerce->cor_texto_botao ? @$ecommerce->cor_texto_botao : "#fff" ?>">
                                        <input type="color" name="cor_texto_botao" id="cor_texto_botao" class="box-color" value="<?= @$ecommerce->cor_texto_botao ? @$ecommerce->cor_texto_botao : "#ffffff" ?>">
                                    </label>
                                </div><br>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-body">
                    <label>Selecionar Pixels:</label><br>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?= url("themes/imgs/Facebook.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px">
                            <?php if (@$pixel) : ?>
                                <select name="pixel_facebook" id="pixel_facebook" class="form-control">
                                    <option value="">Selecionar</option>
                                    <?php foreach ($pixel as $pixel1) : ?>
                                        <?php if (@$pixel1->plataforma == "Facebook" && $pixel1->tipo_integracao != "api") : ?>
                                            <option value="<?= @$pixel1->id ?>" <?= @$ecommerce->pixel_facebook == @$pixel1->id ? "selected" : "" ?>><?= @$pixel1->nome ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <img src="<?= url("themes/imgs/Facebook.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px"><small>Api de conversão</small>
                            <?php if ($pixel) : ?>
                                <select name="pixel_facebook_api" id="pixel_facebook_api" class="form-control">
                                    <option value="">Selecionar</option>
                                    <?php foreach ($pixel as $pixel1) : ?>
                                        <?php if ($pixel1->plataforma == "Facebook" && $pixel1->tipo_integracao == "api") : ?>
                                            <option value="<?= $pixel1->id ?>" <?= @$ecommerce->pixel_facebook_api == $pixel1->id ? "selected" : "" ?>><?= $pixel1->nome ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <img src="<?= url("themes/imgs/Taboola.png") ?>" width='100px' style="margin-bottom: 5px">
                            <?php if ($pixel) : ?>
                                <select name="pixel_taboola" id="pixel_taboola" class="form-control">
                                    <option value="">Selecionar</option>
                                    <?php foreach ($pixel as $pixel3) : ?>
                                        <?php if ($pixel3->plataforma == "Taboola") : ?>
                                            <option value="<?= $pixel3->id ?>" <?= @$ecommerce->pixel_taboola == $pixel3->id ? "selected" : "" ?>><?= $pixel3->nome ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?= url("themes/imgs/GoogleAnalitics.png") ?>" width='70px' style="margin-bottom: 5px">
                            <?php if ($pixel) : ?>
                                <select name="pixel_google_analytics" id="pixel_google_analytics" class="form-control">
                                    <option value="">Selecionar</option>
                                    <?php foreach ($pixel as $pixel2) : ?>
                                        <?php if ($pixel2->plataforma == "GoogleAnalytics") : ?>
                                            <option value="<?= $pixel2->id ?>" <?= @$ecommerce->pixel_google_analytics == $pixel2->id ? "selected" : "" ?>><?= $pixel2->nome ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <img src="<?= url("themes/imgs/GoogleAds.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px">
                            <?php if ($pixel) : ?>
                                <select name="pixel_google_ads" id="pixel_google_ads" class="form-control">
                                    <option value="">Selecionar</option>
                                    <?php foreach ($pixel as $pixel2) : ?>
                                        <?php if ($pixel2->plataforma == "GoogleAds") : ?>
                                            <option value="<?= $pixel2->id ?>" <?= @$ecommerce->pixel_google_ads == $pixel2->id ? "selected" : "" ?>><?= $pixel2->nome ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <img src="<?= url("themes/imgs/GoogleTagManager.png") ?>" width='85px' style="border-radius: 20px; margin-bottom: 5px">
                            <?php if ($pixel) : ?>
                                <select name="pixel_google_tag_manager" id="pixel_google_tag_manager" class="form-control">
                                    <option value="">Selecionar</option>
                                    <?php foreach ($pixel as $pixel2) : ?>
                                        <?php if ($pixel2->plataforma == "GoogleTagManager") : ?>
                                            <option value="<?= $pixel2->id ?>" <?= @$ecommerce->pixel_google_tag_manager == $pixel2->id ? "selected" : "" ?>><?= $pixel2->nome ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?= url("themes/imgs/TikTok2.png") ?>" width='85px' style="border-radius: 20px; margin-bottom: 5px">
                            <?php if ($pixel) : ?>
                                <select name="pixel_tiktok" id="pixel_tiktok" class="form-control" style="margin-top: 2%;">
                                    <option value="">Selecionar</option>
                                    <?php foreach ($pixel as $pixel2) : ?>
                                        <?php if ($pixel2->plataforma == "TikTok") : ?>
                                            <option value="<?= $pixel2->id ?>" <?= @$ecommerce->pixel_tiktok == $pixel2->id ? "selected" : "" ?>><?= $pixel2->nome ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card card-body">
                    <div class="row bg-tarja" style="padding: 5px;">
                        <div class="col-md-1">
                            <!-- <label>Logomarca</label> -->
                            <!-- <input type="file" id="logo" name="logo" class="form-control"> -->

                            <div class="campo-logo">

                                <?php if (@$ecommerce->logo != "") : ?>
                                    <span class="change-image" id="change-logo">&times;</span>
                                    <img src="<?= url("uploads/ecommerce/$ecommerce->logo") ?>" class="image-preview">

                                    <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="logo">
                                    <input type="hidden" name="logo-atual" id="logo-atual" value="<?= $ecommerce->logo ?>">
                                    <label for="imageInput" class="filelabel" id="lbl-logo" style="display: none;"><i class="far fa-image"></i> Escolha uma logo</label>
                                <?php else : ?>
                                    <span class="change-image" id="change-logo" style="display: none;">&times;</span>
                                    <img src="" class="image-preview" style="display: none;">

                                    <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="logo">
                                    <label for="imageInput" class="filelabel" id="lbl-logo"><i class="far fa-image"></i> Escolha uma logo</label>
                                <?php endif ?>
                            </div>

                        </div>

                        <div class="col-md-4" style="display:flex; align-items: center">
                            <!-- <label>Nome da Loja</label> -->
                            <input type="text" name="nome_loja" id="nome_loja" class="inp-form inp-titulo texto-tarja" placeholder="Digite o nome da loja" value="<?= @$ecommerce->nome_loja ?>">
                        </div>

                        <div class="col-md-6" style="display:flex; align-items: center">

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text input-group-sm"><small><?= url("loja/") ?></small></div>
                                </div>
                                <input type="text" name="url_loja" id="url_loja" class="form-control titulo-loja input-group-sm" placeholder="URL da loja" value="<?= @$ecommerce->url_loja ?>">
                                <div class="input-group-append">
                                    <a href="<?= url("loja/" . @$ecommerce->url_loja) ?>" class="btn btn-primary btn-sm" target="_blank"><i class="fas fa-external-link-alt"></i></a>
                                </div>
                                <span id="aviso_loja"></span>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <!-- <label>Banner da loja</label>
                            <input type="file" id="banner" name="banner" class="form-control"> -->

                            <div class="campo-banner">
                                <?php if (@$ecommerce->banner != "") : ?>
                                    <span class="change-image" id="change-banner">&times;</span>
                                    <img src="<?= url("uploads/ecommerce/$ecommerce->banner") ?>" class="banner-preview">

                                    <input type="file" accept="image/*" id="banner-input" class="FileUpload1" name="banner">
                                    <input type="hidden" name="banner-atual" id="banner-atual" value="<?= $ecommerce->banner ?>">
                                    <label for="banner-input" class="filelabel" id="lbl-banner" style="display: none;"><i class="far fa-image"></i> Escolha um banner</label>
                                <?php else : ?>
                                    <span class="change-image" id="change-banner" style="display: none;">&times;</span>
                                    <img src="" class="banner-preview" style="display: none;">

                                    <input type="file" accept="image/*" id="banner-input" class="FileUpload1" name="banner">
                                    <label for="banner-input" class="filelabel" id="lbl-banner"><i class="far fa-image"></i> Escolha um banner</label>
                                <?php endif ?>
                            </div>


                        </div>
                    </div>
                </div>

                <hr>
                <?php if (@$ecommerce->destaques) {
                    $destaque = explode(",", $ecommerce->destaques);
                    // var_dump($destaque);

                    foreach ($destaque as $key => $value) {
                        $dest[$value] = $value;
                    }
                }
                ?>
                <div class="card card-body">

                    <p>
                        <span class="texto-tarja tarja">Destaques</span>
                        <span class="border-tarja"></span>
                    </p>

                    <p class="texto-loja">Escolha quais produtos ficarão em destaque no Ecommerce</p>

                    <div class="row">

                        <!-- <select name="destaques[]" id="destaques" class="form-control" multiple="multiple"> -->
                        <?php if ($produtos) : $i = 0; ?>
                            <?php foreach ($produtos as $produto) : ?>
                                <div class="col-md-3">
                                    <div class="card card-body" style="text-align: center;">
                                        <input type="checkbox" value="<?= $produto->id ?>" <?= @$dest[$produto->id] == $produto->id ? "checked" : "" ?> name="destaques[]">
                                        <img src="<?= url("uploads/produtos/$produto->foto") ?>" alt="" class="imagem-produto">
                                        <span class="texto-loja"><?= $produto->nome ?></span><br>
                                        <b class="titulo-loja"> R$ <?= $produto->preco ?></b>

                                        <button class="btn-destaque">Comprar agora</button>
                                    </div>
                                </div>
                            <?php $i++;
                            endforeach; ?>
                        <?php endif; ?>

                        <!-- </select> -->

                    </div>
                </div>

                <hr>
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Email da loja:*</label>
                            <input type="email" name="email_loja" id="email_loja" class="form-control" value="<?= @$ecommerce->email_loja ?>">
                        </div>

                        <div class="col-md-6">
                            <label>Telefone ou Whatsapp da loja:*</label>
                            <input type="tel" name="telefone_loja" id="telefone_loja" class="form-control" value="<?= @$ecommerce->telefone_loja ?>">
                        </div>
                    </div>
                    * se não preenchido, será mostrado o da conta

                    <hr>
                    <span class="texto-tarja tarja">Endereço da loja</span>
                    <span class="border-tarja"></span>

                    <div class="row">
                        <div class="col-md-3">
                            <label>CEP</label>
                            <input type="tel" name="cep_loja" id="cep_loja" class="form-control" value="<?= @$ecommerce->cep_loja ?>">
                        </div>

                        <div class="col-md-6">
                            <label>Endereço</label>
                            <input type="text" name="rua_loja" id="rua_loja" class="form-control" value="<?= @$ecommerce->rua_loja ?>">
                        </div>

                        <div class="col-md-3">
                            <label>Número</label>
                            <input type="text" name="numero_loja" id="numero_loja" class="form-control" value="<?= @$ecommerce->numero_loja ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Complemento</label>
                            <input type="tel" name="complemento_loja" id="complemento_loja" class="form-control" value="<?= @$ecommerce->complemento_loja ?>">
                        </div>

                        <div class="col-md-6">
                            <label>Bairro</label>
                            <input type="text" name="bairro_loja" id="bairro_loja" class="form-control" value="<?= @$ecommerce->bairro_loja ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Cidade</label>
                            <input type="tel" name="cidade_loja" id="cidade_loja" class="form-control" value="<?= @$ecommerce->cidade_loja ?>">
                        </div>

                        <div class="col-md-6">
                            <label>Estado</label>
                            <input type="text" name="estado_loja" id="estado_loja" class="form-control" value="<?= @$ecommerce->estado_loja ?>">
                        </div>
                    </div>

                    <hr>
                    <span class="texto-tarja tarja">Sobre a loja</span>
                    <span class="border-tarja"></span>

                    <textarea name="informacoes_loja" id="informacoes_loja" cols="10" class="txt-control texto-loja"><?= @$ecommerce->informacoes_loja ?></textarea>
                    <hr>

                    <span class="texto-tarja tarja">Dúvidas frequentes</span>
                    <span class="border-tarja"></span>
                    <textarea name="duvidas" id="duvidas" cols="10" class="txt-control texto-loja"><?= @$ecommerce->duvidas ?></textarea>

                </div>

                <hr>
                <div class="card card-body">
                    <div class="row">
                        <button class="btn btn-success" id="editar">Salvar</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- FIM CARD BODY -->

    </section>
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
                    <button class="btn btn-primary" id="salvar_cartao" onclick="taxaEcommerce('assinatura', <?= $_SESSION['id'] ?>, <?= $taxa->valor_ecommerce ?>)">Salvar</button>
                    <button class="btn btn-warning" id="editar_cartao">Editar</button>
                </div>
            </form>

        </div>

    </div>

</div>
<!-- FIM CARTÃO DE CRÉDITO -->

<?php $v->start("scripts") ?>
<script>
    function taxaEcommerce(tipo, id, taxa) {
        $.ajax({
            url: "<?= $router->route("ecommerce.cobranca") ?>",
            data: {
                assinatura: tipo,
                id: id,
                taxa: taxa
            },
            type: "POST",
            success: (callback) => {
                alert(callback);
            }
        });
    }

    function habilitar() {
        if ($("#habilitar").val() == 0) {
            $("#configuracoes").hide();
        } else {
            $("#configuracoes").show();
            $(".imagem-produto").show();
        }
    }

    habilitar();
    $("#habilitar").change(() => {
        habilitar();
    });

    $("#cor_titulo").change(() => {
        $("#lbl-cor-titulo").css("background", $("#cor_titulo").val());
    });

    $("#cor_tarja").change(() => {
        $("#lbl-cor-tarja").css("background", $("#cor_tarja").val());
    });

    $("#cor_destaque").change(() => {
        $("#lbl-cor-destaque").css("background", $("#cor_destaque").val());
    });

    $("#cor_texto").change(() => {
        $("#lbl-cor-texto").css("background", $("#cor_texto").val());
    });

    $("#cor_botao").change(() => {
        $("#lbl-cor-botao").css("background", $("#cor_botao").val());
    });

    $("#cor_texto_botao").change(() => {
        $("#lbl-cor-texto-botao").css("background", $("#cor_texto_botao").val());
    });

    // DEFINIR URL DA LOJA
    var nomeLoja = $("#nome_loja").val();
    var url_loja = $("#url_loja").val();

    function urlLoja(nome) {
        var low = nome.toLowerCase();
        var sp = low.split(" ");
        var url = sp.join("-");

        $("#url_loja").prop("value", url);
    }

    $("#nome_loja").blur(() => {
        console.log("url", url_loja);
        if (url_loja == "") {
            urlLoja(nomeLoja);
        }
    });

    // FIM DEFINIR URL DA LOJA

    var larguraProduto = $('.imagem-produto').width(160);
    $('.imagem-produto').height(160);

    var informacoes_loja = document.getElementById("informacoes_loja");
    var duvidas = document.getElementById("duvidas");

    function informacoes(id) {
        var res = id.value.match(/\n/g) ? id.value.match(/\n/g).length : 0;
        var altura = 38 * res;
        $("#" + id.id).height(altura);
    }
    informacoes(informacoes_loja);
    informacoes(duvidas);

    informacoes_loja.addEventListener("input", informacoes(informacoes_loja));
    duvidas.addEventListener("input", informacoes(duvidas));


    $('#imageInput').on('change', function() {
        $input = $(this);
        if ($input.val().length > 0) {
            fileReader = new FileReader();
            fileReader.onload = function(data) {
                $('.image-preview').attr('src', data.target.result);
            }
            fileReader.readAsDataURL($input.prop('files')[0]);
            $('#lbl-logo').css('display', 'none');
            $('.image-preview').css('display', 'block');
            $('#change-logo').css('display', 'block');
        }
    });

    $('#change-logo').on('click', function() {
        $control = $(this);
        $('#imageInput').val('');
        $preview = $('.image-preview');
        $preview.attr('src', '');
        $preview.css('display', 'none');
        $control.css('display', 'none');
        $("#logo-atual").attr("value", "");
        $('#lbl-logo').css('display', 'block');
    });

    $('#banner-input').on('change', function() {
        $input = $(this);
        if ($input.val().length > 0) {
            fileReader = new FileReader();
            fileReader.onload = function(data) {
                $('.banner-preview').attr('src', data.target.result);
            }
            fileReader.readAsDataURL($input.prop('files')[0]);
            $('#lbl-banner').css('display', 'none');
            $('.banner-preview').css('display', 'block');
            $('#change-banner').css('display', 'block');
        }
    });

    $('#change-banner').on('click', function() {
        $control = $(this);
        $('#banner-input').val('');
        $preview = $('.banner-preview');
        $preview.attr('src', '');
        $preview.css('display', 'none');
        $control.css('display', 'none');
        $("#banner-atual").attr("value", "");
        $('#lbl-banner').css('display', 'block');
    });

    $("#cadastro").click((e) => {
        cancelar();
    });

    $("#salva").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("api.cadastrar") ?>");
    });

    $("#edita").click((e) => {
        e.preventDefault();
        salvarEditar("<?= $router->route("ecommerce.cadastrar") ?>");
    });

    function buscaUrlLoja(url, loja) {

        $.ajax({
            url: "<?= $router->route("ecommerce.url") ?>",
            type: "POST",
            data: {
                url: url,
                loja: loja
            },
            success: (callback) => {
                console.log(callback);
                if (callback == "true") {
                    $("#url_loja").attr("value", $("#url_loja").val() + "-2");
                    $("#aviso_loja").html("Url já cadastrada");
                }
            }
        })
    }

    $("#url_loja").blur(() => {
        buscaUrlLoja($("#url_loja").val(), "<?= @$ecommerce->id ?>");
    })

    function cancelar() {
        $(".modal-title").html("Gerar Api");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#resultado").css("display", "none");
        $("#id").attr({
            "value": ""
        });
        $("#api_key").attr("value", "");
        $("#descricao").attr("value", "");
    }

    // $("#cor_titulo").change(() => {
    //     alert($("#cor_titulo").prop("value", "#ffff00"));
    // });

    $("#cep_loja").focusout(function() {
        $.ajax({
            url: 'https://api.pagar.me/1/zipcodes/' + $(this).val(),

            dataType: 'json',
            success: function(resposta) {
                $("#rua_loja").val(resposta.street);
                $("#bairro_loja").val(resposta.neighborhood);
                $("#cidade_loja").val(resposta.city);
                $("#estado_loja").val(resposta.state);
            }
        });
    });
</script>
<?php $v->end() ?>
<?php $v->layout("ecommerce/_tema"); ?>

<?php if ($loja->banner) : ?>
    <div class="secao-banner">
        <img class="banner-loja" src="<?= url("uploads/ecommerce/$loja->banner"); ?>" alt="">
    </div>
<?php endif ?>

<!-- RESULTADO PESQUISA -->
<div id="resultado_pesquisa"></div>
<!-- FIM RESULTADO PESQUISA -->

<!-- DESTAQUES -->
<div id="resultados_loja">
    <br>
    <?php if ($loja->destaques && $isCategoria == false) : ?>
        <div class="container secao-destaques">
            <div class="row">
                <div class="col-md-12">
                    <div class="border-tarja">
                        <span class="texto-tarja tarja">Destaques</span>
                    </div>
                </div>
            </div>

            <br>
            <div class="row">
                <?php
                $destaques = explode(",", $loja->destaques);

                function tirarAcentos($string)
                {
                    return preg_replace(array('/[@\.\;\""]+/', "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a a e e i i o o u u n n"), $string);
                }

                foreach ($destaques as $destaque) :
                    $produto = $produtoObj->findById($destaque);
                    $nome1 = strtolower($produto->nome);
                    $nome2 = explode(" ", $nome1);
                    $nome = implode("-", $nome2);
                    $nome = tirarAcentos($nome);
                ?>
                    <div class="col-md-3 col-6">
                        <div class="card card-body card-produto">
                            <a class="texto-loja link-produto" href="<?= url("loja/$loja->url_loja/$produto->id/$nome"); ?>">
                                <img class="imagem-produto" src="<?= url("uploads/produtos/$produto->foto"); ?>" alt="">

                                <div class="descricao-produto">
                                    <span class="texto-loja"><?= mb_strimwidth($produto->nome, 0, 17, "...") ?><br></span>
                                    <span><small class="texto-loja">por: </small> <b class="texto-1">R$<?= number_format($produto->preco, 2, ",", ".") ?></b></span><br>
                                </div>

                                <button class="btn-destaque">Comprar agora</button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <br>
    <?php endif ?>

    <?php if (@$produtos) : ?>
        <div class="container secao-produtos">
            <div class="row">
                <div class="col-md-12">
                    <div class="border-tarja">
                        <span class="texto-tarja tarja">Produtos</span>
                    </div>
                </div>
            </div>

            <br>
            <div class="row">
                <?php
                foreach ($produtos as $produto) :
                ?>
                    <div class="col-md-3 col-6">
                        <div class="card card-body card-produto">
                            <a class="texto-loja link-produto" href="<?= url("loja/$loja->url_loja/$produto->id/$produto->nome"); ?>">
                                <img class="imagem-produto" src="<?= url("uploads/produtos/$produto->foto"); ?>" alt="">

                                <div class="descricao-produto">
                                    <span class="texto-loja"><?= mb_strimwidth($produto->nome, 0, 15, "...") ?><br></span>
                                    <span><small class="texto-loja">por: </small> <b class="texto-1">R$<?= number_format($produto->preco, 2, ",", ".") ?></b></span><br>
                                </div>

                                <button class="btn-destaque">Comprar agora</button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif ?>

    <?php if ($loja->informacoes_loja) : ?>
        <br>
        <div class="container secao-sobre" id="sobre">
            <div class="row">
                <div class="col-md-12">
                    <div class="border-tarja">
                        <span class="texto-tarja tarja">Sobre a loja</span>
                    </div>

                    <p class="textos texto-loja"><?= nl2br($loja->informacoes_loja) ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>

    <?php if ($loja->duvidas) : ?>
        <br>
        <div class="container secao-duvidas">
            <div class="row">
                <div class="col-md-12">
                    <div class="border-tarja">
                        <span class="texto-tarja tarja">Dúvidas</span>
                    </div>

                    <p class="textos texto-loja"><?= nl2br($loja->duvidas) ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php $v->start("scripts") ?>
<script>
    var larguraProduto = $('.imagem-produto').width();
    $('.imagem-produto').height(larguraProduto);
    console.log(larguraProduto);

    var pesquisa = $(".secao-pesquisa").position();
    console.log(pesquisa.top);
    $(".menu-mobile").click(() => {
        $(".menu-list").toggle();
        // $(".pesquisa").toggle();
    });

    $(".btn-busca").click((e) => {
        e.preventDefault();

        var form = $("form");
        console.log(form);

        $.ajax({
            url: "<?= $router->route("ecommerce.busca") ?>",
            type: "POST",
            data: form.serialize(),
            success: (callback) => {
                if (callback != "false") {
                    console.log(callback);
                    $("#resultado_pesquisa").html(callback);
                    $("#resultado_pesquisa").show();
                    $("#resultados_loja").hide();
                } else {
                    $("#resultado_pesquisa").hide();
                    $("#resultados_loja").show();
                }
            }
        })
    });
</script>
<?php $v->end() ?>
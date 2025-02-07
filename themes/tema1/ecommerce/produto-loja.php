<?php
// include __DIR__ . "/../produtos/tipo.php";
?>

<?php $v->layout("ecommerce/_tema"); ?>

<!-- <div class="secao-banner">
        <img class="banner-loja" src="<?= url("uploads/ecommerce/$loja->banner"); ?>" alt="">
    </div> -->

<!-- RESULTADO PESQUISA -->
<div id="resultado_pesquisa"></div>
<!-- FIM RESULTADO PESQUISA -->

<!-- DESTAQUES -->
<div id="resultados_loja">
    <div class="container">
        <div class="row" style="margin: 20px 0;">
            <div class="col-md-5">
                <img src="<?= url("uploads/produtos/$produto->foto"); ?>" alt="teste" width="100%">
            </div>

            <div class="col-md-7">
                <h1 class="texto-loja"><?= $produto->nome ?></h1>
                <span class="texto-loja"><?= $produto->descricao ?></span>

                <?php if (@$planos) : ?>
                    <br><br>
                    <?php foreach ($planos as $plano) : ?>
                        <?php
                        $checkout = $checkoutObj->find("plano = $plano->id")->fetch();
                        if ($checkout) :
                        ?>
                            <div class="bg-white" style="border-radius: 5px; margin-bottom: 5px; box-shadow: 2px 2px 3px #ddd">
                                <div class="row">
                                    <div class="col-md-2 col-4">
                                        <?php if (@$plano->foto) : ?>
                                            <img class="imagem-plano" src="<?= url("uploads/produtos/$plano->foto"); ?>" alt="">
                                        <?php else : ?>
                                            <img class="imagem-plano" src="<?= url("uploads/produtos/$produto->foto"); ?>" alt="">
                                        <?php endif ?>
                                    </div>

                                    <div class="col-md-8 col-8">
                                        <div class="descricao-produto">
                                            <span class="texto-loja"><?= $plano->nome ?><br></span>
                                            <span><small class="texto-loja">por: </small> <b class="texto-1">R$<?= number_format($produto->preco, 2, ",", ".") ?></b></span><br>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <br>
                                        <a href="<?= url("checkout/$checkout->codigo?ec=true") ?>" class="btn-destaque col-12">Escolher</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    <?php endforeach; ?>

                <?php endif ?>
            </div>
        </div>

        <?php if ($produto->caracteristicas) : ?>
        <br>
        <div class="container secao-destaques">
            <div class="row">
                <div class="col-md-12">
                    <div class="border-tarja">
                        <span class="texto-tarja tarja">Caracteristicas</span>
                    </div>

                    <p class="textos texto-loja"><?= nl2br($produto->caracteristicas) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($loja->destaques) : ?>
            <br>
            <div class="container secao-destaques">
                <div class="row">
                    <div class="col-md-12">
                        <div class="border-tarja">
                            <span class="texto-tarja tarja">Você também pode gostar</span>
                        </div>
                    </div>
                </div>

                <br>
                <div class="row">
                    <?php
                    $destaques = explode(",", $loja->destaques);

                    $i = 1;
                    foreach ($destaques as $destaque) :
                        if ($destaque && $destaque != $produto->id && $i <= 4) :
                            $produtoDestaque = $produtoObj->findById($destaque);
                    ?>
                            <div class="col-md-3 col-6">
                                <div class="card card-body card-produto">
                                    <a class="texto-loja link-produto" href="<?= url("loja/$loja->url_loja/$produtoDestaque->id/$produtoDestaque->nome"); ?>">
                                        <img class="imagem-produto" src="<?= url("uploads/produtos/$produtoDestaque->foto"); ?>" alt="">

                                        <div class="descricao-produto">
                                            <span><?= mb_strimwidth($produtoDestaque->nome, 0, 17, "...") ?><br></span>
                                            <span><small class="texto-loja mobile-none">por: </small> <b class="texto-1">R$<?= number_format($produtoDestaque->preco, 2, ",", ".") ?></b></span><br>
                                        </div>

                                        <button class="btn-destaque">Comprar agora</button>
                                    </a>
                                </div>
                            </div>
                    <?php
                        endif;
                        $i++;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
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
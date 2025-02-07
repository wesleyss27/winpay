<style>
    body {
        background: #f2f4f6;
    }

    ul {
        margin: 0 !important;
        padding: 0 !important;
    }

    .bg-tarja {
        background: <?= @$loja->cor_tarja ? @$loja->cor_tarja : "#888" ?>;
    }
    /* MENU */
    .menu-header {
        padding: 10px;
        background: <?= @$loja->cor_tarja ? @$loja->cor_tarja : "#888" ?>;
        display: flex;
        align-items: center;
        position: relative;
    }

    .titulo-loja {
        font-size: 30px;
    }

    .menu-mobile {
        display: none;
    }

    .menu-list {
        text-align: right;
    }

    .menu-list .item,
    .submenu-item {
        padding: 7px;
        text-decoration: none;
        display: inline-block;
    }

    .item:hover {
        background: rgba(255, 255, 255, .1);
    }

    .item-active:hover .submenu {
        display: block;
    }

    .submenu {
        display: none;
        color: <?= @$loja->cor_texto ? @$loja->cor_texto : "#fff" ?>;
        background: #fff;
        text-align: left;
        position: absolute;
        min-width: 200px;
        padding: 10px;
        z-index: 100;
        right: 10px;
        top: 40px;
    }

    .submenu .submenu-item {
        display: block;
    }

    .submenu .submenu-item:hover {
        background: rgba(0, 0, 0, .1);
    }

    /* FIM MENU */

    .logo-loja {
        width: 60px;
        height: 60px;
        border-radius: 50%;
    }

    .logo-loja-footer {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .texto-1 {
        color: <?= $loja->cor_titulo ? $loja->cor_titulo : "#666" ?>;
    }

    .banner-loja {
        width: 100%;
        height: 300px;
    }

    .texto-loja {
        color: <?= @$loja->cor_texto ? @$loja->cor_texto : "#fff" ?>;
    }

    /* TARJAS */
    .tarja {
        /* width: max-content; */
        padding: 5px 10px;
        font-size: 20px;
        font-weight: 600;
        background: <?= @$loja->cor_tarja ? @$loja->cor_tarja : "#888" ?>;
    }

    .texto-tarja {
        color: <?= @$loja->cor_destaque ? @$loja->cor_destaque : "#fff" ?>
    }

    .border-tarja {
        padding: 4px 0;
        width: 100%;
        border-bottom: 3px solid <?= @$loja->cor_tarja ? @$loja->cor_tarja : "#888" ?>;
        margin-bottom: 10px;
    }

    /* FIM TARJAS */

    /* PRODUTOS */
    .imagem-produto {
        width: 70%;
        margin: 0 auto;
    }

    .imagem-plano {
        width: 100%;
        margin: 0 auto;
    }

    .card-produto {
        /* border: none; */
        box-shadow: 2px 2px 4px #ddd;
        text-align: center;
        margin-bottom: 30px;
    }

    .card-produto:hover {
        transform: scale(1.1);
    }

    .link-produto:hover {
        text-decoration: none;
        color: <?= @$loja->cor_texto ? @$loja->cor_texto : "#fff" ?>
    }

    .descricao-produto {
        margin: 10px 0;
    }

    /* FIM PRODUTOS */

    .textos {
        font-family: "Segoe UI";
        font-size: 17px;
        padding: 20px;
        word-wrap: break-word;
        display: inline-block;
    }

    /* PESQUISA */
    .pesquisa {
        border: 2px solid <?= @$loja->cor_destaque ? @$loja->cor_destaque : "#fff" ?>;
        background: none;
    }

    .inp-pesquisa {
        background: none;
        border: none;
        width: 90%;
        color: <?= @$loja->cor_destaque ? @$loja->cor_destaque : "#fff" ?>;
    }

    .input:focus {
        border: 2px solid #fff !important;
    }

    .btn-busca {
        width: 10%;
        background: <?= @$loja->cor_destaque ? @$loja->cor_destaque : "#fff" ?>;
        border: none;
        padding: 5px;
    }

    .btn-destaque {
        font-weight: bold;
        width: 100%;
        padding: 7px;
        border-radius: 7px;
        border: 1px solid rgba(255, 255, 255, .5);
        background: <?= @$loja->cor_botao ? @$loja->cor_botao : "#fff" ?>;
        color: <?= @$loja->cor_texto_botao ? @$loja->cor_texto_botao : "#fff" ?>;
    }

    .btn-destaque:hover {
        opacity: .8;
        color: <?= @$loja->cor_texto_botao ? @$loja->cor_texto_botao : "#fff" ?>;
        text-decoration: none;
    }

    .secao-footer {
        padding: 15px 0;
    }
    /* FIM PESQUISA */

    @media(max-width: 500px) {
        .mobile-none {
            display: none;
        }

        /* MENU */
        .menu-mobile {
            display: block;
        }

        .menu-list {
            text-align: left;
            width: 100%;
            display: none;
            background: <?= @$loja->cor_tarja ? @$loja->cor_tarja : "#fff" ?>;
        }

        .menu-list .item,
        .submenu-item {
            display: block;
            background: rgba(255, 255, 255, .1);
            border: 2px solid <?= @$loja->cor_tarja ? @$loja->cor_tarja : "#fff" ?>;
            padding: 5px 10px;
            margin: 0;
        }

        .menu-list .item {
            border-left: 2px solid #fff;
        }

        .submenu {
            display: none;
            color: #fff;
            width: 100%;
            background: transparent;
            text-align: left;
            position: relative;
            /* margin: 0 0 0 10px; */
            top: auto;
            right: 0;
            padding: 0px;
        }

        /* FIM MENU */

        .titulo-loja {
            font-size: 20px;
        }

        .texto-1 {
            font-size: 17px;
        }

        .btn-destaque {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: -1px;
        }

        .banner-loja {
            width: 100%;
            height: 100%;
        }

        .logo-loja {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .pesquisa {
            margin: 10px 0 0 0;
        }

        .secao-pesquisa {
            /* background: #259A90; */
            margin: 5px 0;
            border-top: 1px solid rgba(0, 0, 0, .1);
        }

        /* PRODUTOS */
        .descricao-produto {
            font-size: small;
        }

        .imagem-produto {
            width: 100%;
            margin: 0 auto;
        }

        .imagem-plano {
            width: 100%;
            margin: 0 auto;
        }

        .card-produto {
            padding: 7px;
        }

        /* FIM PRODUTOS */
    }
</style>
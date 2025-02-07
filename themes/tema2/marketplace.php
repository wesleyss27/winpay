<?php
//session_start();
//$acesso = access($_SESSION["permissao"]);
include "produtos/tipo.php";

?>

<?php $v->layout("_theme"); ?>
<link rel="stylesheet" href="<?= url("themes/tema2/assets/css/icons.css"); ?>"/>

<form action="#">
    <div class="row principal bg-white">
        <div class="col-md-3">
            <input type="text" name="nome" class="form-control" placeholder="Nome do produto" value="<?= @$_GET["venda"]; ?>">
        </div>

        <div class="col-md-3">
            <select name="categoria" id="categoria" class="form-control">
                <option disabled selected>Categoria...</option>
                <?php $i = 1; foreach($categoria as $categorias): ?>
                <option value="<?= $i ?>"><?= $categorias ?></option>
                <?php $i++; endforeach ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="comissao" id="comissao" class="form-control">
                <option disabled selected>Comissionamento...</option>
                <option value="1">Primeiro clique</option>
                <option value="2">Último clique</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="tipo" id="tipo" class="form-control">
                <option value="">Tipo do produto...</option>
                <option value="8">Físico</option>
                <option value="1">Digital</option>
            </select>
        </div>
    </div> 

    <div class="row principal bg-white">
        <div class="col-md-3">
            <a href="?recente=true" class="btn btn-primary form-control">Mais Recentes</a>
        </div>

        <!--div class="col-md-3">
            <a href="?vendido=true" class="btn btn-primary form-control">Mais Vendidos</a>
        </div-->

        <div class="col-md-1">
            <button type="submit" class="btn btn-success">Buscar</button>
        </div> 
    </div> 
</form>

<div class="principal bg-white">
<div class="row">
        <?php if($produtos): ?>
            <?php foreach($produtos as $produto): ?>
                <div class="col-md-3">
                    <div class="col-md-12">

                    <a href="<?= url("produtos/detalhe/".$produto->id)?>">
                    <div class="row produto-marketplace">
                        <figure style="margin: 0 auto; text-align: center">
                            <?php if($produto->foto != ""): ?>
                            <img src="<?= url("uploads/produtos/".$produto->foto); ?>" alt="<?= $produto->nome; ?>" width="200px" height="200px"><br>
                            <?php else: ?>
                            <img src="<?= url("uploads/produtos/default.jpg"); ?>" alt="<?= $produto->nome; ?>" width="200px" height="200px"><br>
                            <?php endif; ?>
                        </figure>
                        <div class="descricao">
                            <strong class="center"><i class="material-icons">grade</i><?= $produto->rockets ?></strong>
                            <h4 class="center"><?= mb_strimwidth($produto->nome, 0, 20, "..."); ?></h4>
                            
                            <?= mb_strimwidth($categoria[$produto->categoria], 0, 20, "..."); ?><br>

                            Ganhe até 
                            <?php 
                                if($produto->tipo_comissao == 0){
                                    $comissao = $produto->preco * ($produto->comissao_afiliado / 100);
                                    echo "R$" . number_format($comissao, 2, ",", ".");
                                } else if($produto->tipo_comissao == 1){
                                    echo "R$" . number_format($produto->comissao_afiliado, 2, ",", ".");
                                }
                            ?><br>

                            <br>
                            <button class="btn btn-success">Promover esse produto</button>
                        </div>
                    </div>
                    </a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $link = URL_BASE."/produtos/p/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link);
                
                else:
                ?>
            <h4>Nenhum produto encontrado!</h4>
            <?php
            endif;
            ?>
        </div>
    </div>
</div>


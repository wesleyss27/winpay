<?php
include "tipo.php";
?>

<?php $v->layout("_theme"); ?>

<div class="row principal bg-white">
    <div class="col-md-12 filtros">
        <form action="#">
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

            <div class="col-md-1">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>  
        </form>
    </div>
</div>

<div class="principal bg-white">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <div class="row">
        <?php if($produtos): ?>
            <?php foreach($produtos as $produto): ?>
                <div class="col-md-3">
                    <div class="col-md-12">

                    <a href="<?= url("produtos/detalhe/".$produto->dataProduto()->id)?>">
                    <div class="row produto-marketplace">
                        <figure style="margin: 0 auto; text-align: center">
                            <?php if($produto->dataProduto()->foto != ""): ?>
                            <img src="<?= url("uploads/produtos/".$produto->dataProduto()->foto); ?>" alt="<?= $produto->dataProduto()->id; ?>" width="200px" height="200px"><br>
                            <?php else: ?>
                            <img src="<?= url("uploads/produtos/default.png"); ?>" alt="<?= $produto->dataProduto()->id; ?>" width="200px" height="200px"><br>
                            <?php endif; ?>
                        </figure>
                        <div class="descricao">
                            <h4 class="center"><?= mb_strimwidth($produto->dataProduto()->nome, 0, 20, "..."); ?></h4>
                            
                            <?= $categoria[$produto->dataProduto()->categoria]; ?><br>

                            Ganhe até 
                            <?php 
                                if($produto->tipo_comissao == 0){
                                    $comissao = $produto->preco * ($produto->comissao_afiliado / 100);
                                    echo "R$" . number_format($comissao, 2, ",", ".");
                                } else if($produto->tipo_comissao == 1){
                                    echo "R$" . number_format($produto->comissao_afiliado, 2, ",", ".");
                                }
                            ?><br>
                        </div>
                    </div>
                    </a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $link = URL_BASE."/produtos/promovidos/p/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link);
                
                else:
                ?>
            <h4>Não existem produtos cadastrados!</h4>
            <?php
            endif;
            ?>
        </div>
    </div>
    

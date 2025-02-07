<?php 
    $v->layout("_theme"); 

    $status = array(
        0 => "Rascunho",
        1 => "Em Análise",
        2 => "Aprovado",
        3 => "Reprovado",
        4 => "Bloqueado"
    );
    
    $bg = array(
        0 => "bg-info",
        1 => "bg-info",
        2 => "bg-success",
        3 => "bg-danger",
        4 => "bg-danger"
    );

    include "tipo.php";
?>

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
                <select name="status" id="status" class="form-control">
                    <option disabled selected>Status...</option>
                    <option value="0">Rascunho</option>
                    <option value="1">Em análise</option>
                    <option value="2">Aprovado</option>
                    <option value="3">Reprovado</option>
                    <option value="4">Bloqueado</option>
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

    <?php
        //$_SESSION["id"] . " " .$usuarioDados->id . $usuarioDados->nome . $usuarioDados->tipo_doc . "<br>";
    ?>

    <?php if($_SESSION["tipo"] != 2 && $_SESSION["tipo"] != 3 && $usuarioDados->tipo_doc == 2): ?>
    <a href="<?= $router->route('produtos.novo') ?>">Novo</a>
    <?php elseif($_SESSION["tipo"] == 5): ?>
    Para cadastrar um produto, você precisa ter um CNPJ cadastrado
    <?php endif; ?>

    <div class="row">
        <?php if($produtos): ?>
            <?php foreach($produtos as $produto): ?>
                <div class="col-md-3">
                    <div class="col-md-12">

                    <a href="<?= url("produtos/detalhe/".$produto->id)?>">
                    <div class="row produto-marketplace">

                    <div class="col-md-12 <?= $bg[$produto->status] ?>"><?php echo $status[$produto->status]; if($produto->rascunho == 1) echo " - Rascunho"; ?></div>

                        <figure style="margin: 0 auto; text-align: center">
                            <?php if($produto->foto != ""): ?>
                            <img src="<?= url("uploads/produtos/".$produto->foto); ?>" alt="<?= $produto->id; ?>" width="200px" height="200px"><br>
                            <?php else: ?>
                            <img src="<?= url("uploads/produtos/default.jpg"); ?>" alt="<?= $produto->id; ?>" width="200px" height="200px"><br>
                            <?php endif; ?>
                        </figure>
                        <div class="descricao">
                            <h4 class="center"><?= mb_strimwidth($produto->nome, 0, 20, "..."); ?></h4>
                            
                            <?= $categoria[$produto->categoria]; ?><br>
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
                <div class="principal"><h4>Nenhum produto encontrado!</h4></div>
            <?php
            endif;
            ?>
        </div>
    </div>
    
</div>

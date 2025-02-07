<?php

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

$tipo_doc = array(
    1 => "CPF",
    2 => "CNPJ"
);

$buscaAfiliacao = $afiliacao->find("afiliado = '{$_SESSION["id"]}' AND produto = '".@$produto->id."'")->fetch();
$buscaAfiliacaoApro = $afiliacao->find("status = 2 AND afiliado = '{$_SESSION["id"]}' AND produto = '".@$produto->id."'")->fetch();
$BuscaCheckout = $checkout->find("produto = '".@$produto->id."'")->fetch(true);
//var_dump($buscaAfiliacao);

include "tipo.php";
?>

<?php $v->layout("_theme"); ?>

<div class="">

    <?php if($produto): ?>

        <!-- SE PRODUTOR OCULTAR OPÇÕES DE PROMOVER -->
        <!--?php if($produto->usuario != $_SESSION["id"] && ($_SESSION["tipo"] == 4 || $_SESSION["tipo"] == 5)): ?-->
        <?php if($produto->usuario != $_SESSION["id"]): ?>
        <?php 
            $v->start("promover"); 
            if($produto->abrir_afiliacao == 1):
        ?>
        <?php if($buscaAfiliacao == null): ?>
            <buttom class="btn btn-primary" id="promover">Promover Esse Produto</buttom>
        <?php endif; ?>
        <?php if($buscaAfiliacao): ?>
            <span class="bg-success"> Afiliação Aprovada</span>
        <?php endif; ?>
            <button class="btn btn-danger" id="denunciar">Denunciar Esse Produto</button>
            <?php endif; $v->end(); ?>
        <?php endif; ?>
        <!-- FIM SE PRODUTOR OCULTAR OPÇÕES DE PROMOVER -->

        <!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
        <div class="menu-edicao principal bg-white">
            <?php if($produto->usuario == $_SESSION["id"]):
                $v->insert("produtos/menu-produto", ["produto" => $produto]);
            endif; ?>
            <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
            <a href="<?= url("produtos/campanhas/").$produto->id; ?>">Campanhas</a>
        </div>

        <div class="row principal bg-white"><!-- PRINCIPAL -->
        <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
        <div class="col-md-4">
            <img src="<?= url("uploads/produtos/".$produto->foto) ?>" alt="Imagem do Produto" width="200px" height="200px"><br><br>            

            <?php if(($_SESSION["tipo"] != 4 && $_SESSION["tipo"] != 5 && $_SESSION["tipo"] != 6) && $produto->status != 4): ?>

            <form action="<?=$router->route("produtos.editarStatus")?>" method="post">
            <textarea name="observacao" id="observacao" class="form-control" cols="30" rows="4" placeholder="Observação">

            </textarea><br>

                <div class="col-md-4">
                    <input type="hidden" name="id" value="<?= $produto->id; ?>">
                    <select name="status" id="status" class="form-control" required>
                        <option disabled selected>Status</option>
                        <option value="2">APROVADO</option>
                        <option value="3">REPROVADO</option>
                        <option value="4">BLOQUEADO</option>
                    </select>
                </div>
                <button class="btn btn-success" id="atualizar">Atualizar</button>
            </form>

            <?php endif;?>
            <br><br>

            <?php if($produto->usuario == $_SESSION["id"]): ?>
            <b>Observação:</b>
            <textarea class="form-control" cols="30" rows="4" readonly><?= $produto->observacao ?></textarea>
            <?php endif;?>
        </div>


        <div class="col-md-8">

            <?php if($produto->usuario == $_SESSION["id"] || ($_SESSION["tipo"] != 4 && $_SESSION["tipo"] != 5)): ?>
                <div class="col-md-12 <?= $bg[$produto->status] ?>"><?= $status[$produto->status] ?></div>
            <?php endif;?>

            <h3><?= $produto->nome; ?></h3>
            <b>Código:</b> <?= $produto->codigo; ?> <br>
            <b>Suporte:</b> <?= $produto->email_suporte; ?> <br>
            <b>Página de vendas:</b> <?= $produto->pagina_venda; ?> <br><br>

            <b>Categoria:</b> <?= $categoria[$produto->categoria]; ?> <br>
            <b>Tipo de produto:</b> <?= $tipoProduto[$produto->tipo_produto]; ?> <br><br>

            <b>Preço:</b> R$ <?= $produto->preco; ?> <br>
            <b>Comissão afiliado</b>
            <?php 
                if($produto->tipo_comissão == 0)
                    echo $produto->comissao_afiliado . "%";
                else
                    echo "R$ " . number_format($produto->comissao_afiliado, 2, ",", ".");
            ?><br>
            <b>Tempo de cookie:</b> <?= $produto->tempo_cookie; ?> dias <br><br>

            <b>Descrição:</b><br> <pre><?= $produto->descricao; ?></pre> <br><br>
            <?php if(@$planos): ?>
            <b>Planos:</b><br>
            
            
            <table class="table">
                <tr>
                    <th>Plano</th>
                    <th>Preço</th>
                    <th>Comissão</th>
                </tr>
                <?php foreach($planos as $plano): ?>
                <tr>
                    <td><?= $plano->nome ?></td>
                    <td>R$ <?= number_format($plano->preco, 2, ",", ".") ?></td>
                    <td>até 
                        <?php 
                            if($produto->tipo_comissão == 0)
                                $comissao = $plano->preco * ($produto->comissao_afiliado / 100);
                            else
                                $comissao = $produto->comissao_afiliado;
                            echo number_format($comissao, 2, ",", ".");
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>


            <b>Termos de Afiliação:</b><br> <pre><?= $produto->termos; ?></pre> <br><br>

            <?php if($buscaAfiliacao || $_SESSION["id"] == $produto->usuario || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                <ul class="nav nav-tabs">
                    <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                    <li><a data-toggle="tab" href="#produtor">Produtor</a></li>
                    <?php endif ?>
                    <li class="active"><a data-toggle="tab" href="#urls">Urls</a></li>
                    <li><a data-toggle="tab" href="#menu2">Mídias</a></li>
                    <li><a data-toggle="tab" href="#menu3">Menu 3</a></li>
                </ul>

            <div class="tab-content">
                <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                <div id="produtor" class="tab-pane fade">
                <br>
                <b>Nome:</b> <?= $produtor->nome; ?> - <b><?= $tipo_doc[$usuario->tipo_doc]; ?>:</b> <?= $usuario->cpf_cnpj; ?><br>
                <b>Email:</b> <?= $usuario->email; ?> - <b>Telefone:</b> <?= $usuario->telefone; ?><br>
                <b>Endereço:</b> <?= $usuario->rua; ?>, <?= $usuario->numero; ?>, <?= $usuario->bairro; ?><br>
                <?= $usuario->cidade; ?> - <?= $usuario->estado; ?>
                
                </div>
                <?php endif ?>

                <div id="urls" class="tab-pane fade in active">
                    <br>
                    <!--?php if(@$buscaAfiliacao != ""): ?>
                    Página de Venda<br>
                    <a href="<?= url("a/" . $buscaAfiliacao->codigo . "?tp=5&ch=0") ?>" target="_blank"><?= url("a/" . $buscaAfiliacao->codigo . "?tp=5&ch=0") ?></a><br>
                    <-?php else: ?>
                        Página de Venda: 
                        <a href="<?= $produto->pagina_venda ?>" target="_blank"><?= $produto->pagina_venda ?></a><br>
                    <-?php endif; ?-->

                    <?php
                    if($urls):
                            foreach($urls as $url):
                            if($buscaAfiliacao):
                    ?>
                            <?= $url->descricao ?>:<br> <a href="<?= url("a/" . $buscaAfiliacao->codigo . "?pg=" . $url->id . "&tp=5") ?>" target="_blank"><?= url("a/" . $buscaAfiliacao->codigo . "?pg=" . $url->id . "&tp=5") ?></a><br><br>
                    <?php
                            else:
                    ?>
                            <?= $url->descricao ?>: <br> <a href="<?= $url->url ?>" target="_blank"><?= $url->url ?></a><br><br>
                    <?php
                            endif;
                            endforeach;
                        endif;
                    ?>

                    <?php 
                        if($BuscaCheckout):
                        foreach($BuscaCheckout as $checkout): 
                        if($buscaAfiliacao):
                    ?>
                        <?= $checkout->descricao ?><br>
                        <a href="<?= url("a/" . $buscaAfiliacao->codigo . "?ch=" . $checkout->codigo . "&tp=5") ?>" target="_blank"><?= url("a/" . $buscaAfiliacao->codigo . "?ch=" . $checkout->codigo . "&tp=5") ?></a><br>

                    <?php else: ?>
                        <?= $checkout->descricao ?><br>
                        <a href="<?= url("checkout/" . $checkout->codigo) ?>" target="_blank"><?= url("checkout/" . $checkout->codigo) ?></a><br>
                    <?php 
                        endif;
                        endforeach; 
                        endif;
                    ?>
                </div>

                <div id="menu2" class="tab-pane fade">
                <h3>Mídias</h3>
                Link de mídias: <a href="<?= $produto->url_midias ?>"><?= $produto->url_midias ?></a>
                </div>
                <div id="menu3" class="tab-pane fade">
                <h3>Menu 3</h3>
                <p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                </div>
            </div>
            </div>
            <?php endif; ?>
        </div>
        </div><!-- FIM PRINCIPAL -->

    <?php
        else:
    ?>
        <h4>Produto Não encontrado</h4>
    <?php
        endif;
    ?>
    
</div>

<script>
    $("#promover").click((e) => {
        var produto = "<?= $produto->id ?>";
        var afiliado = "<?= $_SESSION["id"] ?>";
        var liberar_comprador = "<?= $produto->liberar_comprador ?>";
        var tempo_cookie = "<?= $produto->tempo_cookie ?>";
        var tipo_comissao = "<?= $produto->tipo_comissao ?>";
        var comissao_afiliado = "<?= $produto->comissao_afiliado ?>";
        var tipo_atribuicao = "<?= $produto->tipo_atribuicao ?>";
        var afiliados_aprovacao = "<?= $produto->afiliados_aprovacao ?>";
        var produto_nome = "<?= $produto->nome ?>";

        $.ajax({
            url: "<?=$router->route("appafiliados.afiliacao")?>",
            data: { produto: produto, afiliado: afiliado, liberar_comprador: liberar_comprador, tempo_cookie: tempo_cookie,
                    tipo_comissao: tipo_comissao, comissao_afiliado: comissao_afiliado, tipo_atribuicao: tipo_atribuicao,
                    afiliados_aprovacao: afiliados_aprovacao, produto_nome: produto_nome},

            type: "POST",
            beforeSend: function () {
                console.log("atualizando")
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log(callback)
                alert(callback);
                location.reload();
            }
        })
    });

    function cookie(){}

    var click = 1;
    $("#observacao").click(() => {
        if(click == 1)
        $("#observacao").html("Tentativas: \nOperador: \nMotivo: \nÚltimo motivo: ");

        click++;
    })

    
</script>

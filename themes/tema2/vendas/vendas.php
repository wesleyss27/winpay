<?php
//session_start();
//$acesso = access($_SESSION["permissao"]);
$status_transacao = array(
    1  => "AGUARDANDO PAGAMENTO",
    2  => "EM ANÁLISE",
    3  => "PRÉ AUTORIZADA",
    4  => "PAGA",
    5  => "CANCELADA",
    6  => "DEVOLVIDA",
    7  => "CHARGEBACK",
    8  => "CONCLUÍDA",
    9  => "BLOQUEADA",
    10 => "ANTIFRAUDE",
);

if($_SESSION["tipo"] == 4){
    $produto = $produtos->find("status != 0 AND usuario = " . $_SESSION["id"])->fetch(true);
    $produtor = $afiliacao->find("status != 0 AND afiliado = " . $_SESSION["id"])->fetch(true);
} elseif ($_SESSION["tipo"] == 5){
    $produto = $afiliacao->find("status != 0 AND afiliado = " . $_SESSION["id"])->fetch(true);
} else {
    $produto = $produtos->find("status != 0")->fetch(true);
}

?>

<?php 
    $v->layout("_theme");
?>

<?php function dadosComprador($venda){ 
    echo "
    <h3>Dados do Comprador</h3>
    <b>{$venda->dataComprador()->nome} - CPF: </b> {$venda->dataComprador()->cpf_cnpj}<br>
    <b>Tel: </b> <a href='https://web.whatsapp.com/send?phone=55{$venda->dataComprador()->telefone}' title='Entrar em contato pelo whatsapp' target='_blank'>{$venda->dataComprador()->telefone}</a> - 
    <b>email: </b><a href='mailto:{$venda->dataComprador()->email}'>{$venda->dataComprador()->email}</a><br>

    Rua {$venda->dataComprador()->rua_entrega}, nº {$venda->dataComprador()->numero_entrega} - {$venda->dataComprador()->complemento_entrega}<br>
    {$venda->dataComprador()->bairro_entrega}, {$venda->dataComprador()->cidade_entrega}, {$venda->dataComprador()->estado_entrega}<br>
    CEP: {$venda->dataComprador()->cep_entrega}";
} 
?>

<style>
    .pedido {
        font-size: 13px;
        font-family: "Arial";
    }
</style>
    
<form action="<?= $router->route("vendas.vendas"); ?>">
    <div class="row principal bg-white">
        <div class="col-md-6">
            <b>Período</b>
            <div class="row">
                <div class="col-50">
                    <input type="date" name="inicio" id="inicio" class="form-control" value="<?= @$_GET["inicio"]; ?>">
                </div>

                <div class="col-50">
                    <input type="date" name="fim" id="fim" class="form-control" value="<?= @$_GET["fim"]; ?>">
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <b>Código da venda ou pedido</b>
            <input type="text" name="venda" class="form-control" placeholder="Código da venda ou pedido" value="<?= @$_GET["venda"]; ?>">
        </div>
        <div class="col-md-3">
            <b>Status Transação</b>
            <select name="status" id="status" class="form-control">
                <option value="">Status...</option>
                <?php foreach($status_transacao as $cod => $status): ?>
                <option value="<?= $cod ?>" <?php if(@$_GET["status"] == $cod) echo "selected"; echo "" ?>><?= $status ?></option>
                <?php endforeach; ?>
            </select>
        </div> 
    </div> 

    <div class="row principal bg-white">
        <div class="col-md-3">
            <b>Produto</b>
            <select name="produto" id="produto" class="form-control">
                <option value="">Produto...</option>
                <?php foreach($produto as $produto): ?>
                <option value="<?= $produto->id ?>" <?php if(@$_GET["produto"] == $produto->id) echo "selected"; echo "" ?>><?= $produto->nome ?></option>
                <?php endforeach; ?>
                <?php if($produtor): foreach($produtor as $produtor): ?>
                <option value="<?= $produtor->dataProduto()->id ?>" <?php if(@$_GET["produto"] == $produtor->dataProduto()->id) echo "selected"; echo "" ?>><?= $produtor->dataProduto()->nome ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <div class="col-md-3">
            <b>Comprador</b>
            <input type="text" name="comprador" id="cpf" class="form-control" placeholder="(nome ou CPF/CNPJ ou email)" value="<?= @$_GET["comprador"]; ?>">
            <!--input type="hidden" name="comprador" id="comprador"-->
        </div>

        <div class="col-md-3">
            <b>Fomra de Pagamento</b>
            <select name="pagamento" id="pagamento" class="form-control">
                <option value="">Pagamento...</option>
                <option value="1" <?php if(@$_GET["pagamento"] == 1) echo "selected"; echo "" ?>>Cartão de crédito</option>
                <option value="2" <?php if(@$_GET["pagamento"] == 2) echo "selected"; echo "" ?>>Boleto</option>
            </select>
        </div>

        <div class="col-md-1">
            <br>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div> 
    </div> 
</form>

<div class="">
    <div class="row">
        <?php
            if($vendas): 
        ?>
        <div class="row" style="margin: 10px 0">
            <b>Total de registros: </b> <?= $count; ?>
        </div>

            <table class="table table-striped" id="retorno">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Venda</th>
                        <th scope="col">Comprador</th>
                        <th scope="col">Produtor</th>
                        <th scope="col">Produto</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Status</th>
                        <th scope="col">Data Transação</th>
                        <th scope="col"></th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                    
                    $forma_pagamento = array(
                        1 => "CARTÃO DE CRÉDITO",
                        2 => "BOLETO"
                    );

                    $i = 1;
                    foreach($vendas as $venda): 
                        $afiliado = $afiliacao->find("produto = $venda->produto AND afiliado = " . $_SESSION["id"])->fetch();
                ?>

                    <tr class="pedido" id="pedido<?= $venda->id; ?>" style="font-size: 12px">
                        <td><?= $venda->transacao; ?></td>

                        <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                            <td><?= $venda->dataComprador()->nome ?></td>
                        <?php elseif(($_SESSION["tipo"] == 4 || $_SESSION["tipo"] == 5) && $afiliado->liberar_comprador == 1): ?>
                            <td><?= $venda->dataComprador()->nome ?></td>
                        <?php else: ?>
                            <td>Dados ocultado pelo produtor</td>
                        <?php endif; ?>

                        <td><?= $venda->dataProdutor()->nome ?></td>
                        <td><?= $venda->dataProduto()->nome ?></td>
                        <td>R$ <?= number_format($venda->valor_bruto, 2, ",", "."); ?></td>
                        <td><?= $status_transacao[$venda->status_transacao]; ?></td>
                        <td><?= formataDataHora($venda->created_at); ?></td>
                        <td><button class="btn btn-info edit" data-id="<?= $venda->id; ?>" data-target="#myModal<?= $venda->id; ?>" data-toggle="modal">
                            <i class="material-icons" style="font-size: 15px">add_circle_outline</i>
                        </button>
                    </tr>

                    <?= $v->insert("vendas/modal-info", [
                        "venda" => $venda, 
                        "pagina" => $pagina, 
                        "status_transacao" => $status_transacao, 
                        "forma_pagamento" => $forma_pagamento, 
                        "i" => $i, 
                        "afiliado" => $afiliado,
                        //"dadosComprador" => dadosComprador($venda)
                    ]); ?>
                <?php $i++; endforeach; ?>
                    
                </tbody>
            </table>
            
            <?php
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
                $link = URL_BASE."/vendas/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link, @$uri[1]);

                $url = explode("?", $_SERVER["REQUEST_URI"]);
                if(@$url[1] != ""){
                    $conc = "&";
                }
                else{
                    $conc = "";
                }
            ?>

            <?php
                else:
            ?>
            <h4>Não existem pedidos cadastrados!</h4>
            <?php
            endif;
            ?>
    </div>
</div>

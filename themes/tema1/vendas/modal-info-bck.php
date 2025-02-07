	<!-- modal detalhes de vendas -->
	<div class="container">
<div id="modalx-slideUp<?= $venda->id; ?>" class="body-modalx">
	<h4 class="text"><br><span class="txt-destaque1">Detalhes do Pedido</span><hr></h4>
		<h6>#<?= $venda->transacao; ?> &nbsp; &nbsp; &nbsp; <small><?= $status_transacao[$venda->status_transacao]; ?></small>
        </h6>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
	<div class="row">
		<div class="col-md-6">
			<div class="titulo">Pagamento</div>
			<div>
				<p class="box-howUse">
                        <b>Data do Pedido:</b> <?= " ".formataDataHora($venda->created_at); ?> <br>
                        <b>Data Finalizada:</b> <?= " ".formataDataHora($venda->data_finalizada); ?><br>
                        <b>Status do Pagamento:</b> <?= " {$status_transacao[$venda->status_transacao]}"; ?><br>
                        <b>Total da venda:</b> R$ <?= number_format(@$venda->valor_bruto, 2, ",", "."); ?><br>
						<b>Valor de desconto:</b> R$ <?= number_format(@$venda->valor_desconto, 2, ",", "."); ?><br>
						<?php if($_SESSION["tipo"] != 6): ?>
                        <?php $liquido = (@$venda->valor_redrocket - 1) ?>
                        <b>Taxa redrocket:</b>  R$ <?= number_format(@$venda->valor_redrocket, 2, ",", "."); ?> (R$ <?= number_format(@$liquido, 2, ",", "."); ?> + R$ 1,00)<br>
                        <b>Valor liquido:</b> R$ <?= number_format(@$venda->valor_liquido, 2, ",", "."); ?><br>
                        <b>Parcelas:</b> <?= @$venda->parcelas; ?> <br>
						<?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
						<b>Meio de Pagamento:</b> <?= $venda->meio_pagamento; ?> <br>
                        <b>Forma de Pagamento:</b><?= $forma_pagamento[$venda->forma_pagamento]; ?><br>
						<b>Campanha:</b> <?= @$venda->dataCampanha()->nome; ?><br>
						<?php endif; ?>
						<?php if($venda->afiliado): ?>
						<b>Afiliado:</b> <?= @$venda->dataAfiliado()->nome; ?><br>
						<?php endif; ?>
						</p>
					</div>
				</div>
				<div class=".col-md-4">
			   <img src="<?= url("uploads/produtos/". $venda->dataProduto()->foto) ?> "height="350px"  width="350px" alt="">
		   </div>
	   </div>
	   <div class="titulo">Dados do Comprador</div>
		   <p class="box-howUse">
			   <!--
			   <b>Pedro Henrique da Silva - CPF: 12719252654</b><br>
			   <b>Tel:</b> (33)9 99102490 <b>Email:</b> pedro@octa.life<br>
			   <br>Rua Rua São Bartolomeu, nº 330
			   <br>Vila Mariana, Governador Valadares, MG
			   <br>CEP: 35012140<br> -->
			   <?php 
                            if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3 || $_SESSION["tipo"] == 6): 
                                dadosComprador($venda);
                            elseif(($_SESSION["tipo"] == 4 || $_SESSION["tipo"] == 5) && $afiliado->liberar_comprador == 1):
                                dadosComprador($venda);
                            else:
                                echo "<br><br>Dados do comprador ocultado pelo produtor";
                            endif; 
                        ?>
			   
			   <div class="col-md-4-ml">
				   <div class="titulo">Informações do Produto</div>
				   <p class=box-howUse><b>Produto:</b> <?= $venda->dataProduto()->codigo . " - " . $venda->dataProduto()->nome; ?><br>
					<?php if($venda->plano != ""): ?>
                        <b>Plano: </b> &nbsp; <?= $venda->dataPlano()->codigo . " - " . $venda->dataPlano()->nome; ?><br>
                        <b>Preço: </b> &nbsp; R$ <?= number_format($venda->dataPlano()->preco, 2, ",", "."); ?><br>
                        <b>Itens por plano: </b> &nbsp; <?= $venda->dataPlano()->quantidade; ?><br>
                        <?php endif; ?>
				   </div>

	<?php if($_SESSION["tipo"] != 6): ?>
		<div class="col-md-6-ml">
		   <div class="titulo">Rastreio</div>
			   <p class="box-howUse">  
			   <b>Link do Boleto:</b><br><a href="<?= "{$venda->link_boleto}"; ?>" target="_blank"><?= " {$venda->link_boleto}"; ?></a><br>
			   <b>Url Checkout:</b><br><a href="https://<?= "{$venda->url_checkout}"; ?>" target="_blank"><?= " {$venda->url_checkout}"; ?></a><br>
			   <b>SRC:</b><br><?= " {$venda->src}"; ?><br>
			   <b>UTM_SOURCE:</b><br><?= " {$venda->utm_source}"; ?><br>
			   <b>UTM_CAMPAIGN:</b><?= " {$venda->utm_campaign}"; ?><br>
			   <b>UTM_MEDIUM:</b><br><?= " {$venda->utm_medium}"; ?><br>
			   <b>UTM_MEDIUM:</b><br><?= " {$venda->utm_content}"; ?><br>
		   </div>
		   <?php endif; ?>

		   <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
		   <h3>Taxas e Comissões</h3>
		   <div class="col-md-6-ml">
			   <div class="titulo">Taxas e Comissões</div>
				   <p class="box-howUse">  
					   <b>Produtor:</b> <?= " {$venda->dataProdutor()->email}"; ?><br>
					   <b>Total da venda:</b> R$ <?= number_format(@$venda->valor_bruto, 2, ",", "."); ?><br>
					   <b>Valor do produto:</b> R$ <?= number_format(@$venda->valor_produto, 2, ",", "."); ?><br>
					   <b>Juros do cartão:</b> R$ <?= number_format(@$venda->valor_juros, 2, ",", "."); ?><br>
					   <b>Valor do frete: </b> R$ <?= number_format(@$venda->valor_frete, 2, ",", "."); ?><br>
					   <b>Valor de desconto:</b> R$ <?= number_format(@$venda->valor_desconto, 2, ",", "."); ?><br>
					   <?php $liquido = (@$venda->valor_redrocket - 1) ?>
					   <b>Taxa redrocket:  R$ <?= number_format(@$venda->valor_redrocket, 2, ",", "."); ?> (R$ <?= number_format(@$liquido, 2, ",", "."); ?> + R$ 1,00)<br>
					   <b>Valor liquido:</b>R$ <?= number_format(@$venda->valor_liquido, 2, ",", "."); ?><br>
					   <b>Parcelas:</b> <?= @$venda->parcelas; ?><br>
					   <?php if($venda->afiliado): ?>
                        <b>Afiliado: </b> &nbsp; <?= @$venda->dataAfiliado()->nome; ?><br>
                        <b>Comissão Do Afiliado: </b> R$ <?= number_format(@$venda->comissao_afiliado, 2, ",", "."); ?><br>
                        <?php endif; ?>
				  </div>
                  <?php endif; ?>



<div>
    <button class="modalx-slideUp_close btn-close-modalx pull-right">Fechar&nbsp;<i class="fa fa-times-circle"></i>
	</button>
</div>
    <br>
    <br>
    <br>
</div>
<script>
    $(document).ready(function () {
    
        $('#modalx-slideUp').popup({
            pagecontainer: '.container',
            transition: 'all 0.5s',
            background: true,
            color: '#000',
            opacity: '0.5',
        });
    
    });
	</script>
	<footer>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12 footer">
					</div>
				</div>
			</div>
		</div>
	</footer>
  
    <script src="dist/js/modal.js"></script>
<?php endif; ?>
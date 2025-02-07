<!-- Modal -->

<div id="myModal<?= $venda->id; ?>" class="modal fade" role="dialog">
  	
    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
              <h4>
              <span class="txt-destaque1">
                Detalhes do Pedido
                </span>
                <small>
                #<?= $venda->id; ?> #<?= $venda->transacao; ?> &nbsp; &nbsp; &nbsp; <small><?= $status_transacao[$venda->status_transacao]; ?></small>
                </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Fim Modal header-->
              	
            <!-- Modal body -->
            <div class="modal-body">

            <div class="container">
                  
            <div class="row">
                <div class="col-md-7">
                    <div class="titulo">Pagamento</div>
                    <div>
				    <p class="box-howUse">

                        <?php 
                            if(@$comissoes){
                                $comissao = @$comissoes->find("venda = $venda->id")->fetch();
                            }
                        ?>

                        <b>Data do Pedido: </b><?= " ".formataDataHora($venda->created_at); ?><br>
                        <?php if($venda->data_finalizada): ?>
                        <b>Data Finalizada: </b> <?= " ".formataDataHora($venda->data_finalizada); ?><br><br>
                        <?php endif; ?>

                        <b>Status do Pagamento: </b><?= " {$status_transacao[$venda->status_transacao]}"; ?><br>
                        <b>Total da venda: </b> R$ <?= number_format(@$venda->valor_bruto - $venda->valor_juros, 2, ",", "."); ?><br>
                        <b>Valor de desconto: </b> R$ <?= number_format(@$venda->valor_desconto, 2, ",", "."); ?><br>

                        <?php if($_SESSION["tipo"] != 6): ?>
                        <?php $liquido = (@$venda->valor_redrocket - 1) ?>
                        <b>Taxa RedRocket: </b> R$ <?= number_format(@$venda->valor_redrocket, 2, ",", "."); ?> (R$ <?= number_format(@$liquido, 2, ",", "."); ?> + R$ 1,00)<br>
                        <b>Valor liquido: </b> R$ <?= number_format(@$venda->valor_liquido, 2, ",", "."); ?><br>
                        <?php endif; ?>
                        
                        <b>Parcelas: </b> <?= @$venda->parcelas; ?>x<br><br>

                        <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                        <b>Meio de Pagamento: </b> <?= $venda->meio_pagamento; ?><br>
                        <b>Integrado St4rt: </b> <?= $venda->st4rt; ?><br>
                        <b>Postback St4rt: </b> <?= $venda->postback_st4rt; ?><br>
                        <b>Bling: </b> <?= $venda->bling; ?><br>
                        <?php endif; ?>

                        <b>Forma de Pagamento: </b> <?= $forma_pagamento[$venda->forma_pagamento]; ?><br>

                        <?php if($venda->campanha): ?>
                            <br>
                        <b>Campanha: </b> <?= @$venda->dataCampanha()->nome; ?><br>
                        <?php endif; ?>

                        <?php if($venda->afiliado): ?>
                        <b>Afiliado: </b> <?= @$venda->dataAfiliado()->nome; ?><br>
                        <?php endif; ?>

                        <?php if($venda->forma_pagamento == 2 && ($venda->status_transacao != 4 && $venda->status_transacao != 8)): ?>
                        
                            <br>

                            <b>Data vencimento: </b><?= formataData(@$venda->data_vencimento); ?><br><br>
                            <a href="<?= "{$venda->link_boleto}"; ?>" target="_blank" class="btn btn-success">Visualizar Boleto</a><br>
      
                            <div class="row">
                                <div class="col-md-6">
                                    Nova data boleto:
                                    <input type="date" class="form-control" id="data_venc<?= $i ?>" onblur="vencimento(<?= $i ?>)">
                                </div>

                                <div class="col-md-6">
                                <br>
                                    <button class="btn btn-success" id="novo-boleto<?= $i ?>" onclick="editar(<?= $i ?>)">Gerar Novo boleto</button>
                                </div>
                            </div>
                        <?php endif ?>

                        <?php if(($_SESSION["id"] == $venda->produtor || $_SESSION["tipo"] == 1) && ($venda->status_transacao == 4 || $venda->status_transacao == 8)): ?>
                        <br>

                        <button class="btn btn-success" id="nota-fiscal<?= $i ?>" onclick="notaFiscal(<?= $i ?>)">Gerar Nota Fiscal</button><br>
                        <small>Necessário integração com notazz</small><br>
                        <small>Integração precisar estar na conta do produtor</small>
                        
                        <?php endif; ?>
                    </p>
                    </div>

                </div>
                <div class="col-md-5">
                    <div class="titulo text-white">|</div>
                    <img src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto) ?>" height="300px"  width="300px" alt="">
                </div>

            </div>

            <br>

            <br>
            <div class="row">
                <!-- REENVIO POSTBACK -->
                <div class="col-md-4">
                    <button class="btn btn-success" id="postback<?= $venda->id ?>" onclick="postback(<?= $venda->id ?>, <?= $venda->produtor ?>, <?= $venda->produto ?>)">Reenviar Postbacks</button>
                    <span id="resposta_postback"></span>
                </div>

                <div class="col-md-4">
                <?php if($venda->status_transacao == 4 && $venda->bling == 0): ?>
                    <button class="btn btn-success" id="bling<?= $venda->id ?>" onclick="bling(<?= $venda->id ?>, <?= $venda->produtor ?>, <?= $venda->produto ?>)">Reenviar Bling</button>
                    <span id="resposta_postback"></span>
                <?php endif; ?>
                </div>
            </div>
            <br>

            <?php if(($_SESSION["id"] == $venda->produtor || $_SESSION["tipo"] == 1) && ($venda->status_transacao == 5)): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="titulo">Motivo Cancelamento</div>

                    <p class="box-howUse">
                        <?= $venda->motivo ?>
                    </p>
                </div>

                <div class="col-md-6">
                    <div class="titulo">Editar Motivo Cancelamento</div>

                    <p class="box-howUse">
                        <input type="text" class="form-control" name="motivo" id="motivo<?= $venda->id ?>" value="<?= $venda->motivo ?>">
                        <br>

                        <button class="btn btn-success" id="btn-motivo<?= $venda->id ?>" onclick="motivo(<?= $venda->id ?>)">Alterar</button>
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="titulo">Dados do Comprador</div>
                    <p class="box-howUse">
                        <?php 
                            if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3 || $_SESSION["tipo"] == 6): 
                                $venda->endereco ? dadosEndereco($venda) : dadosComprador($venda);
                            elseif($_SESSION["tipo"] == 4 || ($_SESSION["tipo"] == 5 && @$afiliado->liberar_comprador == 1)):
                                dadosComprador($venda);
                            elseif($_SESSION["tipo"] == 5 && @$_GET["minhas"] == "true"):
                                $venda->endereco ? dadosEndereco($venda) : dadosComprador($venda);
                            else:
                                echo "<br><br>Dados do comprador ocultado pelo produtor";
                            endif; 

                            if ($_SESSION["tipo"] == 1):
                                echo "<br> Endereço: $venda->endereco, Usuário: " . @$venda->dataEndereco()->usuario . ", Comprador: $venda->comprador";
                            endif;
                        ?>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="titulo">Informações do Produto</div>
                    <p class=box-howUse>
                    <b>Produto: </b> &nbsp; <a href="<?= url("produtos/detalhe/$venda->produto") ?>"><?= $venda->dataProduto()->codigo . " - " . $venda->dataProduto()->nome; ?></a><br>
                    

                    <?php if($venda->plano != ""): ?>
                    <b>Plano: </b> &nbsp; <?= $venda->dataPlano()->codigo . " - " . $venda->dataPlano()->nome; ?><br>
                    <b>Preço: </b> &nbsp; R$ <?= number_format($venda->dataPlano()->preco, 2, ",", "."); ?><br>
                    <b>Itens por plano: </b> &nbsp; <?= $venda->dataPlano()->quantidade; ?><br>
                    <?php endif; ?>
                    </p>
                </div>
            </div>

                <div class="row">
                    <div class="col-md-12">
                    <div class="titulo">Dados Extras</div>
                    <p class="box-howUse">
                        
                        <b>Link do Boleto: </b><a href="<?= "{$venda->link_boleto}"; ?>" target="_blank"><?= " {$venda->link_boleto}"; ?></a><br>
                        
                        <?php if(@$venda->linha_digitavel): ?>
                        <b>Linha digitável: </b><?= " {$venda->linha_digitavel}"; ?><br>
                        <?php endif; ?>
                        
                        <b>Url Checkout: </b><a href="<?= HTTP . "{$venda->url_checkout}"; ?>" target="_blank"><?= HTTP . "{$venda->url_checkout}"; ?></a><br>

                        <?php if($_SESSION["tipo"] != 6 && @$_GET["minhas"] != "true"): ?>
                        <b>SRC: </b><?= " {$venda->src}"; ?><br>
                        <b>UTM_SOURCE: </b><?= " {$venda->utm_source}"; ?><br>
                        <b>UTM_CAMPAIGN: </b><?= " {$venda->utm_campaign}"; ?><br>
                        <b>UTM_MEDIUM: </b><?= " {$venda->utm_medium}"; ?><br>
                        <b>UTM_CONTENT: </b><?= " {$venda->utm_content}"; ?><br>
                        <?php endif; ?>
                    </p>
                    </div>
                </div>

                <br>

            <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
            <div class="row">
                <div class="col-md-12">
			     <div class="titulo">Taxas e Comissões</div>
				    <p class="box-howUse">  
                        <b>Produtor: </b><?= " {$venda->dataProdutor()->email}"; ?><br>
                        <b>Total da venda: </b> R$ <?= number_format(@$venda->valor_bruto, 2, ",", "."); ?><br>
                        <b>Valor do produto: </b> R$ <?= number_format(@$venda->valor_produto, 2, ",", "."); ?><br>
                        <b>Juros do cartão: </b> R$ <?= number_format(@$venda->valor_juros, 2, ",", "."); ?><br>
                        <b>Valor do frete: </b> R$ <?= number_format(@$venda->valor_frete, 2, ",", "."); ?><br>
                        <b>Valor de desconto: </b> R$ <?= number_format(@$venda->valor_desconto, 2, ",", "."); ?><br>

                        <?php $liquido = (@$venda->valor_redrocket - 1) ?>
                        <b>Taxa RedRocket: </b> R$ <?= number_format(@$venda->valor_redrocket, 2, ",", "."); ?> (R$ <?= number_format(@$liquido, 2, ",", "."); ?> + R$ 1,00)<br>
                        <b>Valor liquido: </b> R$ <?= number_format(@$venda->valor_liquido, 2, ",", "."); ?><br>

                        <b>Parcelas: </b> <?= @$venda->parcelas; ?>X<br>
                        <b>Valor parcelas: </b> R$ <?= number_format(@$venda->valor_bruto / @$venda->parcelas, 2, ",", "."); ?><br>

                        <?php if($venda->afiliado): ?>
                        <b>Afiliado: </b> &nbsp; <?= @$venda->dataAfiliado()->nome; ?><br>
                        <b>Comissão Do Afiliado: </b> R$ <?= number_format(@$venda->comissao_afiliado, 2, ",", "."); ?><br>
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>


            </div>
            <!--Fim Modal body-->

            <!-- Modal footer -->
            <div class="modal-footer">
            <button data-dismiss="modal" class="modalx-slideUp_close btn-close-modalx pull-right">Fechar&nbsp;<i class="fa fa-times-circle"></i></button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->
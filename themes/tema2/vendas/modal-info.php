

<!-- Modal -->
<div id="myModal<?= $venda->id; ?>" class="modal fade" role="dialog">
  	
    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">
                Detalhes do Pedido
                #<?= $venda->transacao; ?> &nbsp; <?= $status_transacao[$venda->status_transacao]; ?>
                </h4>
            </div>
            <!-- Fim Modal header-->
              	
            <!-- Modal body -->
            <div class="modal-body">

            <div class="container">

              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#comprador<?= $i ?>">Home</a></li>
                <li><a data-toggle="tab" href="#geral<?= $i ?>">Geral</a></li>
                <li><a data-toggle="tab" href="#taxa<?= $i ?>">Taxas e Comissões</a></li>
                <!--li><a data-toggle="tab" href="#menu3">Menu 3</a></li-->
              </ul>

              <div class="tab-content">
                
                <!-- TAB COMPRADORES -->
                <div id="comprador<?= $i ?>" class="tab-pane fade in active">
                  
                  <div class="row">
                    <div class="col-md-6">
                        <h3>Pagamento</h3>
                        <b>Data do Pedido: </b><?= " ".formataDataHora($venda->created_at); ?><br>
                        <b>Data Finalizada: </b><?= " ".formataDataHora($venda->data_finalizada); ?><br>
                        <b>Status do Pagamento: </b><?= " {$status_transacao[$venda->status_transacao]}"; ?><br>
                        <b>Valor bruto: </b> R$ <?= number_format(@$venda->valor_bruto, 2, ",", "."); ?><br>
                        <b>Parcelas: </b> <?= @$venda->parcelas; ?><br>

                        <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): ?>
                        <b>Meio de Pagamento: </b> <?= $venda->meio_pagamento; ?><br>
                        <?php endif; ?>
                        <b>Forma de Pagamento: </b> <?= $forma_pagamento[$venda->forma_pagamento]; ?><br>
                        <?php if($venda->campanha): ?>
                        <b>Campanha: </b> <?= @$venda->dataCampanha()->nome; ?><br>
                        <?php endif; ?>

                        <?php if($venda->afiliado): ?>
                        <b>Afiliado: </b> <?= @$venda->dataAfiliado()->nome; ?><br>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <img src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto) ?>" height="200px"  width="200px" alt="">
                    </div>
                  </div>

                  <div class="row">
                  
                      <div class="col-md-6">
                          <?php 
                              if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3): 
                                  dadosComprador($venda);
                              elseif(($_SESSION["tipo"] == 4 || $_SESSION["tipo"] == 5) && $afiliado->liberar_comprador == 1):
                                  dadosComprador($venda);
                              else:
                                  echo "<br><br>Dados do comprador ocultado pelo produtor";
                              endif; 
                          ?>
                          
                      </div>

                      <div class="col-md-6">
                          <h3>Informações do Produto</h3>
                          <b>Produto: </b> &nbsp; <?= $venda->dataProduto()->codigo . " - " . $venda->dataProduto()->nome; ?><br>
                            

                          <?php if($venda->plano != ""): ?>
                          <b>Plano: </b> &nbsp; <?= $venda->dataPlano()->codigo . " - " . $venda->dataPlano()->nome; ?><br>
                          <b>Preço: </b> &nbsp; R$ <?= number_format($venda->dataPlano()->preco, 2, ",", "."); ?><br>
                          <b>Itens por plano: </b> &nbsp; <?= $venda->dataPlano()->quantidade; ?><br>
                          <?php endif; ?>
                      </div>
                  </div>

                </div>
                <!-- FIM TAB COMPRADORES -->

                <!-- TAB GERAL -->
                <div id="geral<?= $i ?>" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Rastreio</h3>
                            <b>Link do Boleto: </b><a href="<?= "{$venda->link_boleto}"; ?>" target="_blank"><?= " {$venda->link_boleto}"; ?></a><br>
                            <b>Url Checkout: </b><a href="https://<?= "{$venda->url_checkout}"; ?>" target="_blank"><?= " {$venda->url_checkout}"; ?></a><br>
                            <b>SRC: </b><?= " {$venda->src}"; ?><br>
                            <b>UTM_SOURCE: </b><?= " {$venda->utm_source}"; ?><br>
                            <b>UTM_CAMPAIGN: </b><?= " {$venda->utm_campaign}"; ?><br>
                            <b>UTM_MEDIUM: </b><?= " {$venda->utm_medium}"; ?><br>
                            <b>UTM_CONTENT: </b><?= " {$venda->utm_content}"; ?><br>
                        </div>

                        <div class="col-md-6">
                            <img src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto) ?>" height="200px"  width="200px" alt="">
                        </div>
                    </div>
                </div>
                <!-- FIM TAB GERAL -->

                <!-- TAB TAXAS E COMISSÕES -->
                <div id="taxa<?= $i ?>" class="tab-pane fade">
                    <h3>Taxas e Comissões</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <b>Produtor: </b><?= " {$venda->dataProdutor()->email}"; ?><br>
                            <b>Total da venda: </b> R$ <?= number_format(@$venda->valor_bruto, 2, ",", "."); ?><br>
                            <b>Valor do produto: </b> R$ <?= number_format(@$venda->valor_produto, 2, ",", "."); ?><br>
                            <b>Juros do cartão: </b> R$ <?= number_format(@$venda->valor_juros, 2, ",", "."); ?><br>
                            <b>Valor do frete: </b> R$ <?= number_format(@$venda->valor_frete, 2, ",", "."); ?><br>
                            <b>Valor de desconto: </b> R$ <?= number_format(@$venda->valor_desconto, 2, ",", "."); ?><br>

                            <?php $liquido = (@$venda->valor_octapay - 1) ?>
                            <b>Taxa OctaPay: </b> R$ <?= number_format(@$venda->valor_octapay, 2, ",", "."); ?> (R$ <?= number_format(@$liquido, 2, ",", "."); ?> + R$ 1,00)<br>
                            <b>Valor liquido: </b> R$ <?= number_format(@$venda->valor_liquido, 2, ",", "."); ?><br>

                            <b>Parcelas: </b> <?= @$venda->parcelas; ?><br>

                            <?php if($venda->afiliado): ?>
                            <b>Afiliado: </b> &nbsp; <?= @$venda->dataAfiliado()->nome; ?><br>
                            <b>Comissão Do Afiliado: </b> R$ <?= number_format(@$venda->comissao_afiliado, 2, ",", "."); ?><br>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <img src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto) ?>" height="200px"  width="200px" alt="">
                        </div>
                    </div>
                </div>
                <!-- FIM TAB TAXAS E COMISSÕES -->

                <!-- TAB EXTRA ->
                <div id="menu3" class="tab-pane fade">
                  <h3>Menu 3</h3>
                  <p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                </div>
                <!- FIM TAB EXTRA -->


              </div>
            </div>

            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">FECHAAAAAAR</button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->
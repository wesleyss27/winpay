<!-- Modal -->
<style>
/* Modal WINPAY - Style Moderno */

.modal-body table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: #1a1a1a;
  color: #f1f1f1;
  font-family: 'Inter', sans-serif;
  border: 1px solid rgba(255, 221, 0, 0.1);
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.5);
}

.modal-body thead {
  background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
}

.modal-body thead th {
  padding: 14px 16px;
  text-align: left;
  color: #FFDD00;
  font-weight: 600;
  border-bottom: 1px solid rgba(255, 221, 0, 0.1);
  font-size: 14px;
}

.modal-body tbody tr {
  transition: background 0.3s ease;
}

.modal-body tbody tr:hover {
  background: rgba(255, 221, 0, 0.05);
}

.modal-body tbody td {
  padding: 12px 16px;
  border-bottom: 1px solid rgba(255, 221, 0, 0.05);
  font-size: 13px;
}

.modal-body tbody tr:last-child td {
  border-bottom: none;
}

.modal-body table td,
.modal-body table th {
  white-space: nowrap;
}


.modal-fade {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  height: 100% !important;
  z-index: 1050 !important;
  background: rgba(0, 0, 0, 0.85) !important;
  backdrop-filter: blur(10px) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  animation: fadeIn 0.3s ease-out !important;
}

.modal-dialog {
  width: 100% !important;
  max-width: 900px !important;
  margin: 20px !important;
  position: relative !important;
  animation: slideUp 0.3s ease-out !important;
}

.modal-content {
  background: linear-gradient(145deg, #1a1a1a, #111111) !important;
  border: 1px solid rgba(255, 221, 0, 0.3) !important;
  border-radius: 20px !important;
  box-shadow: 
    0 25px 50px rgba(0, 0, 0, 0.9),
    0 0 0 1px rgba(255, 221, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
  overflow: hidden !important;
  position: relative !important;
}

.modal-content::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, #FFDD00, transparent);
  animation: shimmer 2s infinite;
}

.modal-header {
  background: linear-gradient(135deg, #2a2a2a, #1a1a1a) !important;
  padding: 24px 32px !important;
  border-bottom: 1px solid rgba(255, 221, 0, 0.2) !important;
  position: relative !important;
}

.modal-header h4 {
  color: #FFDD00 !important;
  font-size: 20px !important;
  font-weight: 600 !important;
  margin: 0 !important;
  font-family: 'Inter', sans-serif !important;
}

.modal-header small {
  color: #aaa !important;
  font-size: 14px !important;
}

.close {
  background: none !important;
  border: none !important;
  color: #FFDD00 !important;
  font-size: 24px !important;
  cursor: pointer !important;
  position: absolute !important;
  right: 20px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 36px !important;
  height: 36px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  border-radius: 50% !important;
  transition: all 0.3s ease !important;
}

.close:hover {
  background: rgba(255, 221, 0, 0.1) !important;
  transform: translateY(-50%) scale(1.1) !important;
}

.modal-body {
  background: #111111 !important;
  padding: 32px !important;
  max-height: 70vh !important;
  overflow-y: auto !important;
}

.modal-body::-webkit-scrollbar {
  width: 8px;
}

.modal-body::-webkit-scrollbar-track {
  background: #1a1a1a;
  border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #FFDD00, #D4B800);
  border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #FFE83D, #FFDD00);
}

/* Cards dentro do modal */
.card.card-body {
  background: linear-gradient(145deg, #2a2a2a, #1a1a1a) !important;
  border: 1px solid rgba(255, 221, 0, 0.1) !important;
  border-radius: 15px !important;
  padding: 24px !important;
  margin-bottom: 20px !important;
  box-shadow: 
    0 8px 32px rgba(0, 0, 0, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
  position: relative !important;
  overflow: hidden !important;
}

.card.card-body::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255, 221, 0, 0.3), transparent);
}

.titulo {
  color: #FFDD00 !important;
  font-size: 18px !important;
  font-weight: 600 !important;
  margin-bottom: 20px !important;
  font-family: 'Inter', sans-serif !important;
  border-bottom: 2px solid rgba(255, 221, 0, 0.2) !important;
  padding-bottom: 8px !important;
}

.box-info .row.borda1 {
  padding: 12px 0 !important;
  border-bottom: 1px solid rgba(255, 221, 0, 0.1) !important;
  transition: all 0.3s ease !important;
}

.box-info .row.borda1:hover {
  background: rgba(255, 221, 0, 0.05) !important;
  border-radius: 8px !important;
  margin: 0 -12px !important;
  padding: 12px !important;
}

.box-info .row.borda1:last-child {
  border-bottom: none !important;
}

/* Modal footer */
.modal-footer {
  background: linear-gradient(135deg, #2a2a2a, #1a1a1a) !important;
  padding: 20px 32px !important;
  border-top: 1px solid rgba(255, 221, 0, 0.2) !important;
  display: flex !important;
  justify-content: flex-end !important;
  gap: 12px !important;
}

/* Animações */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    opacity: 0; 
    transform: translateY(30px) scale(0.95); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0) scale(1); 
  }
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Responsivo */
@media (max-width: 768px) {
  .modal-dialog {
    margin: 10px !important;
    max-width: calc(100vw - 20px) !important;
  }

  .modal-body {
    padding: 20px !important;
    max-height: 60vh !important;
  }

  .modal-header, .modal-footer {
    padding: 16px 20px !important;
  }
}
/* Garante que o modal abre centralizado na tela */
.modal.show .modal-dialog {
  margin: 0 auto !important;
  align-self: center !important;
}

</style>
<div id="myModal<?= $venda->id; ?>" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header <?= @$status_bg[$venda->status_transacao]; ?>">
                <h4>
                    Detalhes do Pedido &nbsp;
                    <small>
                        #<?= $venda->id; ?> #<?= $venda->transacao; ?>

                        <?php

                        use Source\Models\Produtos\Plano;

                        if ($venda->upsell) : ?>
                            <span class="badge bg-azul-dark">Upsell</span>
                        <?php endif; ?>

                        &nbsp; <small><?= @$status_transacao[$venda->status_transacao]; ?></small>
                    </small>

                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <!-- Modal body -->
            <div class="modal-body" style="background: #f4f6f9">

                <!-- CONTAINER -->
                <div class="container">

                    <div class="card card-body">
                        <div class="row">

                            <div class="col-md-8">
                                <div class="titulo">Informações do Pagamento</div>

                                <div class="box-info">
                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Data do Pedido: </b></div>
                                        <div class="col-md-7"><?= formataDataHora($venda->created_at); ?></div>
                                    </div>

                                    <?php if ($venda->data_finalizada) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Data Finalizada: </b></div>
                                            <div class="col-md-7"><?= " " . formataDataHora($venda->data_finalizada); ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($venda->status_transacao == 5 || $venda->status_transacao == 6 || $venda->status_transacao == 7) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b><?= @$status_transacao[$venda->status_transacao] ?>: </b></div>
                                            <div class="col-md-7"><?= " " . formataDataHora($venda->data_ultimo_evento); ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if( $venda->data->motivo && $venda->status_transacao != 4 ) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Situação: </b></div>
                                            <div class="col-md-7"> <?= $venda->data->motivo ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Status: </b></div>
                                        <div class="col-md-7"><?= @$status_transacao[$venda->status_transacao] ?></div>
                                    </div>

                                    <?php if ($section->getId() == 6 || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Total da venda: </b></div>
                                            <div class="col-md-7">R$ <?= number_format(@$venda->valor_bruto, 2, ",", "."); ?></div>
                                        </div>
                                    <?php else : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Valor do Produto: </b></div>
                                            <div class="col-md-7">R$ <?= number_format(@$venda->valor_produto, 2, ",", "."); ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Valor de desconto: </b></div>
                                        <div class="col-md-7">R$ <?= number_format(@$venda->valor_desconto, 2, ",", "."); ?></div>
                                    </div>

                                    <?php if (@$venda->cupom) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Cupom: </b></div>
                                            <div class="col-md-7"><?= $venda->cupom; ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Valor de frete: </b></div>
                                        <div class="col-md-7">R$ <?= number_format(@$venda->valor_frete, 2, ",", "."); ?></div>
                                    </div>

                                    <?php if ($venda->tipo_frete != "") : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Serviço de frete: </b></div>
                                            <div class="col-md-7"><?= $venda->tipo_frete ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Parcelas: </b></div>
                                        <div class="col-md-7"><?= @$venda->parcelas; ?>x</div>
                                    </div>

                                    <div class="row borda1">
                                        <?php
                                        $dados = $usuarioDados->find("usuario = $venda->produtor")->fetch();
                                        $vendedor = @$dados && @$dados->nome_fantasia ? @$dados->nome_fantasia : @$venda->dataProdutor()->nome;
                                        ?>
                                        <div class="col-md-5"><b>Vendedor: </b></div>
                                        <div class="col-md-7"><?= @$vendedor; ?></div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Email do Vendedor: </b></div>
                                        <div class="col-md-7"><a href="mailto:<?= @$venda->dataProduto()->email_suporte; ?>"><?= @$venda->dataProduto()->email_suporte; ?></a></div>
                                    </div>

                                    <?php if ($venda->atendente) :
                                        $atendente = (new Source\Models\Usuarios\Usuario())->findById($venda->atendente);
                                    ?>
                                    <div class="row borda1" style="background:rgba(168,85,247,.07);">
                                        <div class="col-md-5"><b>Atendente: </b></div>
                                        <div class="col-md-7">
                                            <span style="background:rgba(168,85,247,.18);color:#c084fc;border:1px solid rgba(168,85,247,.4);padding:1px 7px;border-radius:999px;font-size:11px;font-weight:700;margin-right:6px;">Venda Personalizada</span>
                                            <?= htmlspecialchars(@$atendente->nome ?? '—') ?>
                                            <?php if (@$atendente->email) : ?>
                                                <div style="font-size:11px;color:#888;margin-top:2px;"><?= htmlspecialchars($atendente->email) ?></div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                    <?php endif ?>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Forma de Pagamento: </b></div>
                                        <div class="col-md-7"><?= $forma_pagamento[$venda->forma_pagamento]; ?></div>
                                    </div>

                                    <?php if ($venda->forma_pagamento == 2 && ($venda->status_transacao != 4 && $venda->status_transacao != 8)) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Data vencimento: </b></div>
                                            <div class="col-md-7"><?= formataData(@$venda->data_vencimento); ?></div>
                                        </div>
                                        <br>
                                        <a href="<?= "{$venda->link_boleto}"; ?>" target="_blank" class="btn btn-success">Visualizar Boleto</a><br>
                                    <?php endif ?>

                                    <br>

                                    <?php if ($section->getId() != 6) : ?>
                                        <?php if ($venda->upsell) : ?>
                                            <div class="row borda1 bg-azul-dark">
                                                <div class="col-md-5"><b>Upsell: </b></div>
                                                <div class="col-md-7"><?= @$venda->upsell; ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($venda->campanha) : ?>
                                            <div class="row borda1">
                                                <div class="col-md-5"><b>Campanha: </b></div>
                                                <div class="col-md-7"><?= $venda->campanha . " - " . @$venda->dataCampanha()->nome; ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($venda->afiliado) : ?>
                                            <div class="row borda1">
                                                <div class="col-md-5"><b>Afiliado: </b></div>
                                                <div class="col-md-7"><?= @$venda->dataAfiliado()->nome; ?></div>
                                            </div>

                                            <div class="row borda1">
                                                <div class="col-md-5"><b>Comissão afiliado: </b></div>
                                                <div class="col-md-7">R$ <?= number_format(@$venda->comissao_afiliado, 2, ",", "."); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Meio de Pagamento: </b></div>
                                            <div class="col-md-7"><?= $venda->meio_pagamento; ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Integrado St4rt: </b></div>
                                            <div class="col-md-7"><?= @$sim[$venda->st4rt]; ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Postback St4rt: </b></div>
                                            <div class="col-md-7"><?= @$sim[$venda->postback_st4rt]; ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Bling: </b></div>
                                            <div class="col-md-7"><?= @$sim[$venda->bling]; ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-5"><b>IP: </b></div>
                                            <div class="col-md-7"><?= $venda->ip; ?></div>
                                        </div>

                                        <?php if ($venda->cookie) : ?>
                                            <div class="row borda1">
                                                <div class="col-md-5"><b>Cookie: </b></div>
                                                <div class="col-md-7"><?= $venda->cookie; ?></div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <br>
                                <img class="img-produto-venda" src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto) ?>" alt="">
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="titulo">Dados do Comprador</div>

                                <?php
                                // echo $section->getTipo();
                                if ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3 || $section->getTipo() == 6) :
                                    $venda->endereco ? dadosEndereco($venda) : dadosComprador($venda);
                                elseif ($pag_coprodutor == true || $section->getTipo() == 4 || ($section->getTipo() == 5 && @$af1->liberar_comprador == 1)) :
                                    $venda->endereco ? dadosEndereco($venda) : dadosComprador($venda);
                                elseif ($section->getTipo() == 5 && @$_GET["minhas"] == "true") :
                                    $venda->endereco ? dadosEndereco($venda) : dadosComprador($venda);
                                else :
                                    echo "<br><br>Dados do comprador ocultado pelo produtor";
                                endif;

                                if ($_SESSION["tipo"] == 1) :
                                    echo "<br> Endereço: $venda->endereco, Usuário: " . @$venda->dataEndereco()->usuario . ", Comprador: $venda->comprador";
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="titulo">Informações do Produto</div>

                                <?php if ((int)$venda->tipo_venda === 1) : ?>
                                    <?php
                                      $vpItensModal = (new Source\Models\Atendente\VendaPersonalizadaItem())->porVenda($venda->id);
                                    ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Tipo: </b></div>
                                        <div class="col-md-9"><span style="background:rgba(168,85,247,.18);color:#c084fc;border:1px solid rgba(168,85,247,.4);padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;">Venda Personalizada</span></div>
                                    </div>
                                    <?php if ($vpItensModal) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-3"><b>Produtos: </b></div>
                                            <div class="col-md-9">
                                                <?php foreach ($vpItensModal as $vpI) : ?>
                                                    <div style="margin-bottom:4px;">
                                                        <b><?= htmlspecialchars($vpI->nome_produto) ?></b>
                                                        <?php if ($vpI->nome_plano) : ?>
                                                            <small style="color:#888;"> — <?= htmlspecialchars($vpI->nome_plano) ?></small>
                                                        <?php endif ?>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="row borda1">
                                            <div class="col-md-3"><b>Produto: </b></div>
                                            <div class="col-md-9"><?= $venda->dataProduto()->nome ?></div>
                                        </div>
                                    <?php endif ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Total: </b></div>
                                        <div class="col-md-9">R$ <?= number_format($venda->valor_bruto, 2, ",", ".") ?></div>
                                    </div>

                                <?php elseif ($section->getId() == 6) : ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Produto: </b></div>
                                        <div class="col-md-9"><?= $venda->dataProduto()->nome; ?></div>
                                    </div>
                                <?php else : ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Produto: </b></div>
                                        <div class="col-md-9"><a href="<?= url("produtos/detalhe/$venda->produto") ?>"><?= $venda->dataProduto()->codigo . " - " . $venda->dataProduto()->nome; ?></a></div>
                                    </div>
                                <?php endif; ?>

                                <?php if ((int)$venda->tipo_venda !== 1 && $venda->plano != "") : ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Plano: </b></div>
                                        <div class="col-md-9"><?= $venda->dataPlano()->codigo . " - " . $venda->dataPlano()->nome; ?></div>
                                    </div>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Preço: </b></div>
                                        <div class="col-md-9">R$ <?= number_format($venda->valor_produto, 2, ",", "."); ?></div>
                                    </div>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Itens por plano: </b></div>
                                        <div class="col-md-9"><?= $venda->dataPlano()->quantidade ? $venda->dataPlano()->quantidade : 1; ?></div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($venda->dataProduto()->tipo_produto == 8) : ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>RASTREAR PEDIDO: </b></div>
                                        <div class="col-md-9"><a href="<?= $venda->dataProduto()->link_rastreio; ?>" target="_blank"><?= $venda->dataProduto()->link_rastreio; ?></a></div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Código de Rastreio: </b></div>
                                        <div class="col-md-9"><?= $venda->codigo_rastreio; ?></div>
                                    </div>

                                    <?php if ($venda->produtor == $section->getId()) : ?>
                                        <label>Inserir código de rastreio:</label>
                                        <div class="row">
                                            <input type="text" name="codigo_rastreio" id="codigo_rastreio<?= $venda->id ?>" value="<?= $venda->codigo_rastreio ?>" class="form-control col-md-4"> &nbsp;
                                            <button class="btn btn-success col-md-2" onclick="inserir(<?= $venda->id ?>)">Inserir</button>
                                        </div>
                                    <?php endif ?>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <?php

                    if (@$venda->planos_order_bump && @$venda->preco_order_bump) :

                        $cod_order_bump = explode(",", $venda->planos_order_bump);
                        $valores_order_bump = explode(",", $venda->preco_order_bump);
                        $i = 0;
                        // var_dump($cod_order_bump);
                    ?>
                        <br>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="titulo">Produtos Adicionais</div>

                                    <?php
                                    $soma_order_bump = 0;
                                    foreach ($cod_order_bump as $cod_plano) :
                                        $valor_order_bump = explode("|", $valores_order_bump[$i]);

                                        $cod_plano = intval($cod_plano);
                                        $planos = (new Plano())->findById($cod_plano);
                                        $soma_order_bump += $valor_order_bump[1];
                                    ?>
                                        <!-- <div class="row borda1">
                                            <div class="col-md-12"><?= "<b>$planos->nome </b> - R$ " . @number_format($valor_order_bump[1], 2, ",", ".") . " - " . $planos->quantidade . " itens" ?></div>
                                        </div> -->

                                        <div class="row borda1">
                                            <div class="col-md-3"><b>Plano: </b></div>
                                            <div class="col-md-9"><?= $planos->nome; ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-3"><b>Preço: </b></div>
                                            <div class="col-md-9">R$ <?= @number_format($valor_order_bump[1], 2, ",", ".") ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-3"><b>Itens por plano: </b></div>
                                            <div class="col-md-9"><?= $planos->quantidade ? $planos->quantidade : 1 ?></div>
                                        </div>
                                        <br>

                                    <?php $i++;
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (
                        $venda->produtor == $section->getId() ||
                        ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)
                    ) : ?>

                        <?php if (@!$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                            <br>
                            <div class="card card-body">
                                <div class="titulo">Ferramentas</div>
                                <div class="row">
                                    <?php if ($venda->forma_pagamento == 2 && ($venda->status_transacao != 4 && $venda->status_transacao != 8)) : ?>
                                        <div class="col-md-6">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    Nova data boleto:
                                                    <input type="date" class="form-control" id="data_venc<?= $index ?>" onblur="vencimento(<?= $index ?>)">
                                                </div>

                                                <div class="col-md-6">
                                                    <br>
                                                    <button class="btn btn-success" id="novo-boleto<?= $index ?>" onclick="editar(<?= $index ?>, '<?= $venda->adquirente ?>')">Gerar Novo boleto</button>
                                                </div>
                                                Caso não dê certo, tente novamente mais tarde.
                                            </div>

                                        </div>
                                    <?php endif ?>

                                    <div class="col-md-6">

                                        <button class="btn btn-success" id="postback<?= $venda->id ?>" onclick="postback(<?= $venda->id ?>, <?= $venda->produtor ?>, <?= $venda->produto ?>)">Reenviar Postbacks</button>
                                        <span id="resposta_postback"></span>

                                        <!--?php if($venda->status_transacao == 4 && $venda->bling == 0): ?-->
                                        <button class="btn btn-success" id="bling<?= $venda->id ?>" onclick="bling(<?= $venda->id ?>, <?= $venda->produtor ?>, <?= $venda->produto ?>)">Reenviar Bling</button>
                                        <span id="resposta_postback"></span>
                                        <!--?php endif; ?-->

                                        <!--?php if$section->getId() == $venda->produtor || $_SESSION["tipo"] == 1) && ($venda->status_transacao == 4 || $venda->status_transacao == 8)): ?-->
                                        <br><br>

                                        <button class="btn btn-success" id="nota-fiscal<?= $index ?>" onclick="notaFiscal(<?= $index ?>)">Gerar Nota Fiscal</button><br>
                                        <?php if (!empty($notasFiscaisMap[$venda->id])): ?>
                                            <small class="text-success"><i class="fas fa-check-circle"></i> Nota emitida em <?= formataDataHora($notasFiscaisMap[$venda->id]->created_at) ?></small><br>
                                        <?php endif; ?>
                                        <small>Necessário integração com notazz</small><br>
                                        <small>Integração precisar estar na conta do produtor</small>

                                        <!--?php endif; ?-->

                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (($section->getId() == $venda->produtor || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) && ($venda->status_transacao == 5)) : ?>

                        <br>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="titulo">Motivo Cancelamento</div>

                                    <?= $venda->motivo ?>
                                </div>

                                <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                    <div class="col-md-6">
                                        <div class="titulo">Editar Motivo Cancelamento</div>

                                        <input type="text" class="form-control" name="motivo" id="motivo<?= $venda->id ?>" value="<?= $venda->motivo ?>">
                                        <br>

                                        <button class="btn btn-success" id="btn-motivo<?= $venda->id ?>" onclick="motivo(<?= $venda->id ?>)">Alterar</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <br>

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="titulo">Dados Extras</div>

                                <?php if (@$venda->link_boleto) : ?>
                                    <b>Link do Boleto: </b><a href="<?= url("boleto/$venda->id"); ?>" target="_blank"><?= url("boleto/$venda->id"); ?></a><br>
                                <?php endif; ?>

                                <?php if (@$venda->linha_digitavel) : ?>
                                    <b>Linha digitável: </b><?= " {$venda->linha_digitavel}"; ?><br>
                                <?php endif; ?>

                                <b>Url de compra: </b><a href="<?= HTTP . "{$venda->url_checkout}"; ?>" target="_blank"><?= HTTP . "{$venda->url_checkout}"; ?></a><br>

                                <?php if (@$venda->codigo_checkout && $venda->planos_order_bump == "" && $venda->adquirente == "redrocket") : ?>
                                    <br>
                                    <b>Url para recuperação de venda: <a href="<?= url("checkout/$venda->codigo_checkout/$venda->id"); ?>" target="_blank"><?= url("checkout/$venda->codigo_checkout/$venda->id"); ?></a></b><br>
                                    <small>Usar esse link em caso de venda cancelada ou para manter comissões</small>
                                <?php endif; ?>

                                <hr>
                                <?php if ($section->getId() != 6 && @$_GET["minhas"] != "true") : ?>
                                    <b>SRC: </b><?= " {$venda->src}"; ?><br>
                                    <b>UTM_SOURCE: </b><?= " {$venda->utm_source}"; ?><br>
                                    <b>UTM_CAMPAIGN: </b><?= " {$venda->utm_campaign}"; ?><br>
                                    <b>UTM_MEDIUM: </b><?= " {$venda->utm_medium}"; ?><br>
                                    <b>UTM_CONTENT: </b><?= " {$venda->utm_content}"; ?><br>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $chargeback = date("Y-m-d", strtotime("+30 days", strtotime($venda->data_pedido)));
                    $chargeback180 = date("Y-m-d", strtotime("+1800 days", strtotime($venda->data_pedido)));
                    // echo "$chargeback $venda->produtor ";
                    ?>
                    <?php if (
                        (($venda->status_transacao == 4 || $venda->status_transacao == 9) &&
                            ($venda->produtor == $section->getId() || $_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3)) &&
                        strtotime(date("Y-m-d")) < strtotime($chargeback180)
                    ) : ?>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="titulo">Cancelar Venda</div>
                                    <button type="button" class="btn btn-warning" id="btn_estorno<?= $index; ?>">Cancelar Venda</button>

                                    <div class="box-howUs" id="div_estorno<?= $index; ?>">
                                        <?php if ($venda->forma_pagamento == 1) : ?>
                                            <p>O estorno será feito no cartão utilizado na compra</p>

                                            <b>Motivo do cancelamento:</b>
                                            <input type="text" name="motivo" id="motivo<?= $venda->id; ?>" class="form-control">min.: 50 caracteres<br>

                                            <?php if ($section->getId() == $venda->produtor && strtotime(date("Y-m-d")) <= strtotime($chargeback) && $venda->status_transacao != 9) : ?>
                                                <button class="btn btn-danger" onclick="estornar('<?= $venda->id ?>', '<?= $venda->transacao ?>', '<?= $venda->forma_pagamento ?>', '<?= $venda->status_transacao ?>', 'estorno', null, '<?= @$venda->adquirente ?>')">Estornar</button>
                                            <?php elseif ($section->getId() == $venda->produtor && strtotime(date("Y-m-d")) >= strtotime($chargeback) && $venda->status_transacao != 9) : ?>
                                                <button class="btn btn-danger" onclick="estornar('<?= $venda->id ?>', '<?= $venda->transacao ?>', '<?= $venda->forma_pagamento ?>', '<?= $venda->status_transacao ?>', 'solicitar', null, '<?= @$venda->adquirente ?>')">Solicitar Estorno</button>
                                            <?php elseif ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                                <button class="btn btn-danger" onclick="estornar('<?= $venda->id ?>', '<?= $venda->transacao ?>', '<?= $venda->forma_pagamento ?>', '<?= $venda->status_transacao ?>', 'estorno', null, '<?= @$venda->adquirente ?>')">Chargeback</button>
                                            <?php endif; ?>

                                        <?php elseif ($venda->forma_pagamento == 2) : ?>
                                            <small>Preencha os dados bancários do comprador para realizar o estorno. O estorno será feito para o titular da compra</small>
                                            <?php $customer = $venda->endereco ? $venda->dataEndereco()->customer_id : $venda->dataComprador()->customer_id ?>

                                            <b>Motivo do cancelamento:</b> <small>(Obrigatório)</small>
                                            <input type="text" name="motivo" id="motivo<?= $venda->id; ?>" class="form-control">min.: 50 caracteres<br>
                                            <hr>

                                            <?php include "lista-bancos.php" ?>
                                            <form action="#" name="estorno-boleto<?= $venda->id ?>">
                                                <label>Banco:</label><br>
                                                <select name="banco" id="banco<?= $venda->id; ?>" class="form-control">
                                                    <?php foreach ($lista_bancos as $cod => $lista) : ?>
                                                        <option value="<?= $cod . " - " . $lista ?>"><?= $cod . " - " . $lista ?> </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Agência:</label>
                                                        <input type="text" name="agencia" id="agencia<?= $venda->id; ?>" class="form-control"><br>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label>Conta com DV:</label>
                                                        <input type="text" name="conta" id="conta<?= $venda->id; ?>" class="form-control"><br>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Tipo de conta:</label>
                                                        <select name="tipo" id="tipo<?= $venda->id; ?>" class="form-control">
                                                            <option value="poupanca">Poupança</option>
                                                            <option value="corrente">Corrente</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label>Chave Pix:</label> <small>Opcional</small>
                                                        <input type="text" name="chave_pix" id="chave_pix<?= $venda->id; ?>" class="form-control"><br>
                                                    </div>
                                                </div>
                                            </form>

                                            <script>
                                                // var form = $("form[name=estorno-boleto]");
                                            </script>
                                            <button class="btn btn-danger" onclick="estornar('<?= $venda->id ?>', '<?= $venda->transacao ?>', '<?= $venda->forma_pagamento ?>', '<?= $venda->status_transacao ?>', 'estorno', '<?= $customer ?>', 'sim')">Estornar</button>

                                        <?php elseif ($venda->forma_pagamento == 3) : ?>
                                            <b>Motivo do cancelamento:</b>
                                            <input type="text" name="motivo" id="motivo<?= $venda->id; ?>" class="form-control">min.: 50 caracteres<br>

                                            <?php if ($section->getId() == $venda->produtor && strtotime(date("Y-m-d")) <= strtotime($chargeback)) : ?>
                                                <button class="btn btn-danger" onclick="estornarPix('<?= $venda->id ?>', '<?= $venda->transacao ?>', '<?= $venda->forma_pagamento ?>', 'estorno', '<?= @$venda->adquirente ?>')">Estornar</button>
                                            <?php elseif ($section->getId() == $venda->produtor && strtotime(date("Y-m-d")) >= strtotime($chargeback)) : ?>
                                                <button class="btn btn-danger" onclick="estornarPix('<?= $venda->id ?>', '<?= $venda->transacao ?>', '<?= $venda->forma_pagamento ?>', 'solicitar', '<?= @$venda->adquirente ?>')">Solicitar Estorno</button>
                                            <?php elseif ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                                <button class="btn btn-danger" onclick="estornarPix('<?= $venda->id ?>', '<?= $venda->transacao ?>', '<?= $venda->forma_pagamento ?>', 'estorno', '<?= @$venda->adquirente ?>')">Chargeback</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- FIM CANCELAR VENDA -->

                                <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                    <div class="col-md-6">
                                        <div class="titulo">Alerta de Chargeback</div>

                                        <?php if ($venda->status_transacao != 9) : ?>
                                            <button class="btn btn-danger" onclick="alertaChargeback('<?= $venda->id ?>')">Alerta e bloqueio</button>
                                        <?php elseif ($venda->status_transacao == 9) : ?>
                                            <button class="btn btn-outline-success" onclick="desbloquear('<?= $venda->id ?>')">Desbloquear venda</button>
                                        <?php endif; ?>

                                        <hr>

                                        <?php if ($venda->status_transacao == 9 && $venda->forma_pagamento == 2) : ?>
                                            <button class="btn btn-outline-primary" onclick="estornoCompleto(<?= $venda->id ?>)">Estorno Completo</button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($venda->observacao != "") : ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="titulo">Estorno</div>
                                <div class="row borda1">
                                    <div class="col-md-5"><b>Motivo Estorno: </b></div>
                                    <div class="col-md-7"><?= $venda->observacao ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <br>

                    <?php if ($section->getTipo() != 6 && $section->getId() != $venda->afiliado && $venda->adquirente == "redrocket") : ?>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="titulo">Taxas e Comissões</div>
                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Produtor: </b></div>
                                        <div class="col-md-7"><?= $venda->dataProdutor()->nome; ?> <br> <?= $venda->dataProdutor()->email; ?></div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Taxa REDROCKET: </b></div>
                                        <?php $liquido = (@$venda->valor_redrocket - 1) ?>
                                        <div class="col-md-7">R$ <?= number_format(@$venda->valor_redrocket, 2, ",", "."); ?> <?= @$venda->adquirente == "redrocket" ? "(R$ " . number_format(@$liquido, 2, ",", ".") . " + R$ 1,00)" : "" ?></div>
                                    </div>

                                    <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3 || $section->getId() == $venda->produtor) : ?>
                                        <div class="row borda1 <?= @$simCor[$venda->assumir_juros] ?>">
                                            <div class="col-md-5"><b>Assumir Juros: </b></div>
                                            <div class="col-md-7"><?= @$sim[$venda->assumir_juros]; ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Total da venda: </b></div>
                                            <div class="col-md-7">R$ <?= number_format(@$venda->valor_bruto, 2, ",", "."); ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Juros do cartão: </b></div>
                                            <div class="col-md-7">R$ <?= number_format(@$venda->valor_juros, 2, ",", "."); ?></div>
                                        </div>

                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Valor liquido: </b></div>
                                            <div class="col-md-7">R$ <?= number_format(@$venda->valor_liquido, 2, ",", "."); ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Valor do produto: </b></div>
                                        <div class="col-md-7">R$ <?= number_format(@$venda->valor_produto, 2, ",", "."); ?></div>
                                    </div>

                                    <?php if (@$soma_order_bump) : ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Soma dos produtos adicionais: </b></div>
                                            <div class="col-md-7">R$ <?= number_format(@$soma_order_bump, 2, ",", "."); ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Valor do frete: </b></div>
                                        <div class="col-md-7">R$ <?= number_format(@$venda->valor_frete, 2, ",", "."); ?></div>
                                    </div>


                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Cupom de desconto: </b></div>
                                        <div class="col-md-7">- R$ <?= number_format(@$venda->valor_desconto, 2, ",", "."); ?> - <?= $venda->cupom ?></div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Parcelas: </b></div>
                                        <div class="col-md-7"><?= @$venda->parcelas; ?>X</div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Valor parcelas: </b></div>
                                        <div class="col-md-7">R$ <?= number_format(@$venda->valor_bruto / @$venda->parcelas, 2, ",", "."); ?></div>
                                    </div>

                                    <?php
                                    $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                    $url = explode("?", $url_atual, 2);
                                    if ($url[0] == url("vendas/coprodutor")) :
                                    ?>
                                        <div class="row borda1">
                                            <div class="col-md-5"><b>Comissão Coprodutor: </b></div>
                                            <div class="col-md-7">R$ <?= number_format(@$valorComissao, 2, ",", "."); ?></div>
                                        </div>
                                    <?php else : ?>

                                        <?php
                                        if (@$comissoes && $venda->status_transacao == 4) {
                                            $comissao = @$comissoes->find("venda = $venda->id")->fetch(true);
                                            if ($comissao) {
                                                foreach ($comissao as $comissao) {
                                                    if ($comissao->usuario != 1) {
                                                        echo "<div class='row borda1'>";
                                                        echo "<div class='col-md-5'><b>$comissao->descricao:</b></div>";
                                                        echo "<div class='col-md-7'>(" . $comissao->dataUsuario()->nome . ") - R$ " . number_format($comissao->valor, 2, ",", ".") . "</div>";
                                                        echo "</div>";
                                                    }
                                                }
                                            }
                                        } else {
                                            if ($venda->afiliado) {
                                                echo "<div class='row borda1'>";
                                                echo "<div class='col-md-5'><b>Comissão Afiliado:</b></div>";
                                                echo "<div class='col-md-7'>(" . $venda->dataAfiliado()->nome . ") - R$ " . number_format($venda->comissao_afiliado, 2, ",", ".") . "</div>";
                                                echo "</div>";
                                            }

                                            if (@$venda->coprodutores) {
                                                $coprodutores = explode("/", $venda->coprodutores);
                                                $comissao_coprodutores = explode("/", $venda->comissao_coprodutores);

                                                $j = 1;
                                                $a = 0;
                                                foreach ($coprodutores as $coprodutor1) {
                                                    if ($coprodutor1) {
                                                        // echo "$coprodutor1 {$comissao_coprodutores[$a]}<br>";
                                                        $com1 = (float) $comissao_coprodutores[$a];
                                                        $nomeUser = $usuario->findById($coprodutor1);

                                                        echo "<div class='row borda1'>";
                                                        echo "<div class='col-md-5'><b>Comissão coprodutor:</b></div>";
                                                        echo "<div class='col-md-7'>(" . $nomeUser->nome . ") - R$ " . @number_format($com1, 2, ",", ".") . "</div>";
                                                        echo "</div>";

                                                        @$comissoesCoprodutores += $com1;
                                                        $valoresCoprodutor[] = array(@$j, $venda->id, number_format($com1, 2, ",", "."));
                                                        $j++;
                                                    }
                                                    $a++;
                                                }
                                            }

                                            echo "<div class='row borda1'>";
                                            echo "<div class='col-md-5'><b>Comissão Produtor:</b></div>";
                                            echo "<div class='col-md-7'><b>R$ " . number_format($venda->valor_liquido - @$comissoesCoprodutores - @$venda->comissao_afiliado, 2, ",", ".") . "</b></div>";
                                            echo "</div>";
                                            $comissoesCoprodutores = 0;
                                        }

                                        $_SESSION["valores_coproducao"] = @$valoresCoprodutor;
                                        ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>



                </div>
                <!-- FIM CONTAINER -->
            </div>
            <!--Fim Modal body-->

            <!-- Modal footer -->
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-danger">Fechar&nbsp;<i class="fa fa-times-circle"></i></button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->
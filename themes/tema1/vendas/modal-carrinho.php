<!-- Modal -->
<div id="myModal<?= $venda->id; ?>" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <!-- Fim Modal header-->

            <!-- Modal body -->
            <div class="modal-body" style="background: #f4f6f9">

                <div class="container">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-body">
                                <h3>Pagamento</h3>

                                <div class="row borda1">
                                    <div class="col-md-5"><b>Data do carrinho: </b></div>
                                    <div class="col-md-7"><?= " " . formataDataHora($venda->created_at); ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-5"><b>Produto: </b></div>
                                    <div class="col-md-7"><?= $venda->dataProduto()->codigo . " - " . $venda->dataProduto()->nome; ?></div>
                                </div>

                                <?php if ($venda->plano != "") : ?>
                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Plano: </b></div>
                                        <div class="col-md-7"><?= $venda->dataPlano()->codigo . " - " . $venda->dataPlano()->nome; ?></div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Preço: </b></div>
                                        <div class="col-md-7">R$ <?= number_format($venda->dataPlano()->preco, 2, ",", "."); ?></div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Itens por plano: </b></div>
                                        <div class="col-md-7"><?= $venda->dataPlano()->quantidade; ?></div>
                                    </div>

                                    <div class="row borda1">
                                        <div class="col-md-5"><b>Link do checkout: </b></div>
                                        <div class="col-md-7"><?= "https://" . $venda->url_checkout; ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <?php if ($venda->plano) : ?>
                                <?php if ($venda->dataPlano()->foto) : ?>
                                    <img src="<?= url("uploads/produtos/" . $venda->dataPlano()->foto) ?>" height="200px" width="200px" alt="">
                                <?php else : ?>
                                    <img src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto) ?>" height="200px" width="200px" alt="">
                                <?php endif; ?>
                            <?php else : ?>
                                <img src="<?= url("uploads/produtos/" . $venda->dataProduto()->foto) ?>" height="200px" width="200px" alt="">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-body">
                                <!-- < ?= dadosComprador($venda); ?> -->
                                <h3>Dados do Comprador</h3>

                                <p><a class="btn btn-success" href="https://api.whatsapp.com/send?phone=55<?= $venda->telefone ?>&text=Olá <?= $venda->nome ?>, tudo bem? Percebi que você ainda não finalizou sua compra, posso ajudar de alguma forma?" target="_blank">Recuperar Carrinho</a></p>
                                <br><br>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Nome: </b></div>
                                    <div class="col-md-9"><?= $venda->nome ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>CPF: </b></div>
                                    <div class="col-md-9"><?= $venda->cpf_cnpj ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Tel: </b></div>
                                    <div class="col-md-9"><a href='https://api.whatsapp.com/send?phone=55<?= $venda->telefone ?>&text=Olá <?= $venda->nome ?>, tudo bem? Percebi que você ainda não finalizou sua compra, posso ajudar de alguma forma?' title='Entrar em contato pelo whatsapp' target='_blank'><?= $venda->telefone ?></a></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Email: </b></div>
                                    <div class="col-md-9"><a href='mailto:<?= $venda->email ?>'><?= $venda->email ?></a></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Endereço: </b></div>
                                    <div class="col-md-9"><?= $venda->rua ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Número </b></div>
                                    <div class="col-md-9"><?= $venda->numero ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Complemento: </b></div>
                                    <div class="col-md-9"><?= $venda->complemento ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Bairro: </b></div>
                                    <div class="col-md-9"><?= $venda->bairro ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Cidade: </b></div>
                                    <div class="col-md-9"><?= $venda->cidade ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Estado: </b></div>
                                    <div class="col-md-9"><?= $venda->estado ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>CEP: </b></div>
                                    <div class="col-md-9"><?= $venda->cep ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Fim Modal body -->

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
            <!-- Fim Modal footer -->

        </div>
        <!-- Fim Modal content-->

    </div>
    <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->
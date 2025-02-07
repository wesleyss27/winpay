<!-- cadastrar -->
<div class="modal fade" id="modal-lg<?= $assinatura->id; ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Gerenciar Assinatura <?= $assinatura->id ?></h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row linha1">
                            <div class="col-md-5"><b>Data da Assinatura: </b></div>
                            <div class="col-md-7"><?= formataDataHora($assinatura->created_at); ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-5"><b>Próxima Cobrança: </b></div>
                            <div class="col-md-7"><?= formataData($assinatura->proxima_cobranca); ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-5"><b>Fim da Assinatura: </b></div>
                            <div class="col-md-7"><?= $assinatura->data_limite != "" ? formataData($assinatura->data_limite) : "Eterno" ?></div>
                        </div>

                        <div class="row linha1">
                            <?php
                            $periodo = array(
                                "Semanal"       => [7, "days"],
                                "Mensal"        => [1, "month"],
                                "Bimestral"     => [2, "months"],
                                "Trimestral"    => [3, "months"],
                                "Semestral"     => [6, "months"],
                                "Anual"         => [1, "years"],
                                "Trienal"       => [3, "years"]
                            );

                            if ($assinatura->data_limite)
                                $falta = 1 + diffDate($assinatura->proxima_cobranca, $assinatura->data_limite, "$assinatura->recorrencia");
                            ?>
                            <div class="col-md-5"><b>Falta: </b></div>
                            <div class="col-md-7"><?= $assinatura->data_limite != "" ? $falta : "Eterno" ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-5"><b>Status: </b></div>
                            <div class="col-md-7"><?= @$status_assinatura[$assinatura->status]; ?></div>
                        </div>

                        <br>
                        <div class="row linha1">
                            <div class="col-md-5"><b>Valor da assinatura: </b></div>
                            <div class="col-md-7">R$ <?= number_format($assinatura->valor_plano, 2, ",", "."); ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-5"><b>Produto: </b></div>
                            <div class="col-md-7"><?= $assinatura->dataProduto()->nome; ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-5"><b>Plano: </b></div>
                            <div class="col-md-7"><?= $assinatura->dataPlano()->nome; ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-5"><b>Periodicidade: </b></div>
                            <div class="col-md-7"><?= $assinatura->recorrencia; ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-5"><b>Forma de pagamento: </b></div>
                            <div class="col-md-7"><?= @$forma_pagamento[$assinatura->pagamento]; ?></div>
                        </div>

                        <?php if ($assinatura->parcelas_antecipadas) : ?>
                            <div class="row linha1">
                                <div class="col-md-5"><b>Parcelas antecipadas: </b></div>
                                <div class="col-md-7"><?= $assinatura->parcelas_antecipadas; ?></div>
                            </div>

                            <div class="row linha1">
                                <div class="col-md-5"><b>Data antecipação: </b></div>
                                <div class="col-md-7"><?= formataData($assinatura->data_antecipacao); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($assinatura->afiliado) : ?>
                            <div class="row linha1">
                                <div class="col-md-5"><b>Afiliado: </b></div>
                                <div class="col-md-7"><?= $assinatura->afiliado; ?></div>
                            </div>

                            <div class="row linha1">
                                <div class="col-md-5"><b>Comissão do afiliado: </b></div>
                                <div class="col-md-7">R$ <?= number_format($assinatura->comissao_afiliado, 2, ",", "."); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <img src="<?= url("uploads/produtos/" . $assinatura->dataProduto()->foto) ?>" height="200px" width="200px" alt="">
                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-12">
                        <h4>Assinante:</h4>

                        <div class="row linha1">
                            <div class="col-md-4"><b>Nome: </b></div>
                            <div class="col-md-8"><?= $assinatura->dataComprador()->nome ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-4"><b>Email: </b></div>
                            <div class="col-md-8"><?= @$assinatura->dataComprador()->email ?></div>
                        </div>

                        <div class="row linha1">
                            <div class="col-md-4"><b>Telefone: </b></div>
                            <div class="col-md-8"><?= @$assinatura->dataComprador()->telefone ?></div>
                        </div>


                    </div>
                </div>

                <br>

                <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                    <?php if ($assinatura->status != 2) ?>
                    <button class="btn btn-danger" type="button" onclick="editar(<?= $assinatura->id ?>, 2)">Cancelar assinatura</button>
                    <?php if ($assinatura->status == 1 && $assinatura->status != 2) : ?>
                        <button class="btn btn-warning" type="button" onclick="editar(<?= $assinatura->id ?>, 3)">Suspender assinatura</button>
                    <?php elseif ($assinatura->status == 3 && $assinatura->status != 2) : ?>
                        <button class="btn btn-success" type="button" onclick="editar(<?= $assinatura->id ?>, 1)">Ativar assinatura</button>
                    <?php endif; ?>
                <?php endif ?>
            </div>
        </div>
    </div>

</div>
<!-- end cadastrar -->
<!-- Modal -->

<div id="myModal<?= $cobranca->id; ?>" class="modal fade" role="dialog">

    <!-- Modal dialog -->
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">

            <!-- Modal header-->
            <div class="modal-header <?= @$status_bg[$cobranca->status_cobranca]; ?>">
                <h4>
                    Detalhes da Cobrança &nbsp;
                    <small>
                        #<?= $cobranca->id; ?> #<?= $cobranca->transacao; ?>

                        &nbsp; <small><?= @$status_transacao[$cobranca->status_transacao]; ?></small>
                    </small>

                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff">&times;</button>
            </div>
            <!-- Fim Modal header-->

            <!-- Modal body -->
            <div class="modal-body" style="background: #f4f6f9">
                <div class="card card-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="titulo">Informações da Cobrança</div>

                            <div class="box-info">
                                <div class="row borda1">
                                    <div class="col-md-3"><b>Data da Cobrança: </b></div>
                                    <div class="col-md-9"><?= formataDataHora($cobranca->created_at); ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Data Finalizada: </b></div>
                                    <div class="col-md-9"><?= " " . formataDataHora($cobranca->data_finalizada); ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Situação: </b></div>
                                    <div class="col-md-9"><?= @$status_transacao[$cobranca->status_cobranca] ?></div>
                                </div>

                                <div class="row borda1">
                                    <div class="col-md-3"><b>Valor Cobrado: </b></div>
                                    <div class="col-md-9">R$ <?= number_format(@$cobranca->valor_cobrado, 2, ",", ".") ?></div>
                                </div>

                                <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) : ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Meio Pagamento: </b></div>
                                        <div class="col-md-9"><?= $cobranca->meio_pagamento ?></div>
                                    </div>
                                <?php endif ?>

                                <?php if ($cobranca->tentativas > 0) : ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Tentativas: </b></div>
                                        <div class="col-md-9"><?= $cobranca->tentativas ?></div>
                                    </div>
                                <?php endif ?>

                                <?php if ($cobranca->nova_cobranca > 0) : ?>
                                    <div class="row borda1">
                                        <div class="col-md-3"><b>Nova Cobrança: </b></div>
                                        <div class="col-md-9">SIM</div>
                                    </div>
                                <?php endif ?>

                                <div class="row">
                                    <div class="col-md-3"><b>Produtor: </b></div>
                                    <div class="col-md-9">
                                        <?= $cobranca->dataProdutor()->nome ?><br>
                                        <?= $cobranca->dataProdutor()->email ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                    <?php if ($cobranca->status_cobranca == 5 || $cobranca->status_cobranca == "") : ?>
                        <div class="card card-body">
                            <div class="titulo">Retentativa de cobrança</div>

                            Gostaria de tentar fazer essa cobrança novamente?
                            <div class="row">
                                <button class="btn btn-success" onclick="retentar(<?= $cobranca->id ?>)">Tentar Novamente</button>
                            </div>

                        </div>
                    <?php endif ?>
                <?php endif ?>
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
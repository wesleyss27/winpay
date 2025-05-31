<!-- cadastrar -->
<div class="modal fade" id="modal-lg<?= $assinatura->id; ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content form-modern" style="background-color: #121212; color: #fff; border-radius: 10px;">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Gerenciar Assinatura <?= $assinatura->id ?></h3>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <?php
                        $periodo = [
                            "Semanal" => [7, "days"],
                            "Mensal" => [1, "month"],
                            "Bimestral" => [2, "months"],
                            "Trimestral" => [3, "months"],
                            "Semestral" => [6, "months"],
                            "Anual" => [1, "years"],
                            "Trienal" => [3, "years"]
                        ];
                        if ($assinatura->data_limite)
                            $falta = 1 + diffDate($assinatura->proxima_cobranca, $assinatura->data_limite, "$assinatura->recorrencia");
                        ?>

                        <?php
                        $infos = [
                            "Data da Assinatura" => formataDataHora($assinatura->created_at),
                            "Próxima Cobrança" => formataData($assinatura->proxima_cobranca),
                            "Fim da Assinatura" => $assinatura->data_limite ? formataData($assinatura->data_limite) : "Eterno",
                            "Falta" => $assinatura->data_limite ? $falta : "Eterno",
                            "Status" => @$status_assinatura[$assinatura->status],
                            "Valor da assinatura" => "R$ " . number_format($assinatura->valor_plano, 2, ",", "."),
                            "Produto" => $assinatura->dataProduto()->nome,
                            "Plano" => $assinatura->dataPlano()->nome,
                            "Periodicidade" => $assinatura->recorrencia,
                            "Forma de pagamento" => @$forma_pagamento[$assinatura->pagamento]
                        ];

                        foreach ($infos as $label => $valor): ?>
                            <div class="row linha1 mb-2">
                                <div class="col-md-5"><b><?= $label ?>:</b></div>
                                <div class="col-md-7"><?= $valor ?></div>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($assinatura->parcelas_antecipadas): ?>
                            <div class="row linha1 mb-2">
                                <div class="col-md-5"><b>Parcelas antecipadas:</b></div>
                                <div class="col-md-7"><?= $assinatura->parcelas_antecipadas ?></div>
                            </div>
                            <div class="row linha1 mb-2">
                                <div class="col-md-5"><b>Data antecipação:</b></div>
                                <div class="col-md-7"><?= formataData($assinatura->data_antecipacao) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($assinatura->afiliado): ?>
                            <div class="row linha1 mb-2">
                                <div class="col-md-5"><b>Afiliado:</b></div>
                                <div class="col-md-7"><?= $assinatura->afiliado ?></div>
                            </div>
                            <div class="row linha1 mb-2">
                                <div class="col-md-5"><b>Comissão do afiliado:</b></div>
                                <div class="col-md-7">R$ <?= number_format($assinatura->comissao_afiliado, 2, ",", ".") ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4 text-center">
                        <img src="<?= url("uploads/produtos/" . $assinatura->dataProduto()->foto) ?>" height="200" width="200" class="img-fluid rounded" alt="">
                    </div>
                </div>

                <hr class="bg-secondary">

                <h4>Assinante:</h4>
                <div class="row linha1 mb-2">
                    <div class="col-md-4"><b>Nome:</b></div>
                    <div class="col-md-8"><?= $assinatura->dataComprador()->nome ?></div>
                </div>
                <div class="row linha1 mb-2">
                    <div class="col-md-4"><b>Email:</b></div>
                    <div class="col-md-8"><?= @$assinatura->dataComprador()->email ?></div>
                </div>
                <div class="row linha1 mb-3">
                    <div class="col-md-4"><b>Telefone:</b></div>
                    <div class="col-md-8"><?= @$assinatura->dataComprador()->telefone ?></div>
                </div>

                <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1): ?>
                    <?php if ($assinatura->status != 2): ?>
                        <button class="btn btn-danger mr-2" onclick="editar(<?= $assinatura->id ?>, 2)">Cancelar assinatura</button>
                    <?php endif; ?>
                    <?php if ($assinatura->status == 1): ?>
                        <button class="btn btn-warning mr-2" onclick="editar(<?= $assinatura->id ?>, 3)">Suspender assinatura</button>
                    <?php elseif ($assinatura->status == 3): ?>
                        <button class="btn btn-success" onclick="editar(<?= $assinatura->id ?>, 1)">Ativar assinatura</button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Estilo visual moderno -->
<style>
    .form-modern h3, .form-modern h4 { color: #FFD700; font-family: 'Inter', sans-serif; }
    .form-modern .modal-header { background: #1E1E1E; border-bottom: 1px solid #333; }
    .form-modern .modal-body { background: #121212; padding: 20px; }
    .form-modern .linha1 div { color: #fff; font-family: 'Inter', sans-serif; }
    .form-modern .btn-danger { background-color: #c0392b; border: none; font-weight: bold; }
    .form-modern .btn-warning { background-color: #f39c12; border: none; font-weight: bold; }
    .form-modern .btn-success { background-color: #27ae60; border: none; font-weight: bold; }
    .form-modern .btn:hover { opacity: 0.9; }
    .form-modern img { border: 2px solid #FFD700; border-radius: 10px; }
</style>

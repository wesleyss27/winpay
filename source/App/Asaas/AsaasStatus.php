<?php

namespace Source\App\Asaas;

use Source\App\Postback\AppPostback;
use Source\Models\Asaas;
use Source\Models\Vendas\Venda;

class AsaasStatus
{
    public function dadosVendas($venda = null)
    {
        $status = array(
            "RECEIVED" => 4,
            "PENDING" => 1,
            "AWAITING_RISK_ANALYSIS" => 2,
            "RECEIVED_IN_CASH" => 4,
            "CONFIRMED" => 4,
            "OVERDUE" => 5,
            "REFUNDED" => 6,
            "REFUND_REQUESTED" => 9,
            "CHARGEBACK_REQUESTED" => 9,
            "CHARGEBACK_DISPUTE" => 9,
            "AWAITING_CHARGEBACK_REVERSAL" => 7,
            "DUNNING_REQUESTED" => 6,
            "DUNNING_RECEIVED" => 4
        );

        if ($venda) {
            $venda = (new Venda())->findById($venda);

            $asaas = (new AsaasCobrancas())->dadosCobranca($venda->transacao);

            if ($venda->status_transacao != $status[$asaas->status]) {
                $venda->status_transacao = $status[$asaas->status];
                if ($venda->status_transacao == 5) {
                    $venda->motivo = $asaas->description;
                }

                if ($venda->save()) {
                    echo "STATUS DE TRANSAÇÃO ATUALIZADO <br>";
                } else {
                    echo $venda->fail()->getMessage();
                }
            }
        } else {
            $semana = date("Y-m-d", strtotime("-7 days", strtotime(date("Y-m-d"))));
            $hoje = date("Y-m-d");
            $vendas = (new Venda())->find("meio_pagamento = 'Asaas' AND data_pedido BETWEEN '$semana' AND '$hoje'")->fetch(true);

            if ($vendas) {
                foreach ($vendas as $venda) {
                    echo "<br>---------------------------------------------------<br>";
                    echo "<b>venda: $venda->id</b><br>";

                    $asaas = (new AsaasCobrancas())->dadosCobranca($venda->transacao);

                    if (@$asaas->status) {
                        if ($venda->status_transacao != @$status[@$asaas->status]) {
                            $venda->status_transacao = $status[$asaas->status];
                            $venda->data_finalizada = date("Y-m-d H:i:s");

                            if ($venda->status_transacao == 5) {
                                $venda->motivo = $asaas->description;
                            }

                            if ($venda->save()) {
                                echo "STATUS DE TRANSAÇÃO ATUALIZADO <br>";

                                $data["venda"] = $venda->id;
                                $data["produto"] = $venda->produto;
                                $data["produtor"] = $venda->produtor;

                                (new AppPostback(null))->enviaPostback($data);
                            } else {
                                echo $venda->fail()->getMessage();
                            }
                        }
                    }
                }
            }
        }
    }
}

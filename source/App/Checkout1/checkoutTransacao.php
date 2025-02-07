<?php

$adquerentes = "1,7";
$adq = explode(",", $adquerentes);

// $transacao = $pagarme->transaction($data);

// $arr["transacao"] = @$transacao->tid;
// $arr["object"] = @$transacao->object;
// $arr["status"] = @$transacao->status;
// $this->logs($arr, "pagarme");

// if(@$transacao->object){
//     $data["transacao"] = @$transacao->tid;
//     $data["status_transacao"] = @$status_transacaoP[@$transacao->status];
//     $data["meio_pagamento"] = "PagarMe";

//     if($data["status_transacao"] == 4)
//     $data["data_finalizada"] = date("Y-m-d H:i:s");
// } 
// if(!@$transacao->object || @$transacao->status == "refused"){
//     $transacao = $wirecard->compraCartao($data);
//     $data["transacao"] = @$transacao->id;
//     $data["status_transacao"] = @$status_transacao[@$transacao->status];
//     $data["meio_pagamento"] = "Moip";
// }

if (@$data["endereco_obrigatorio"] == 0) {
    $transacao = $cielo->compraCartaoSimples($data);
    // var_dump($transacao);
    $data["transacao"] = @$transacao->Payment->Tid;
    $data["nit_pix"] = @$transacao->Payment->PaymentId;
    $data["status_transacao"] = @$status_transacaoC[@$transacao->Payment->Status];
    $data["meio_pagamento"] = "Cielo";

    if ($data["status_transacao"] == 4)
        $data["data_finalizada"] = date("Y-m-d H:i:s");

    if ($data["status_transacao"] == 5) {
        $data["descricao"] = $transacao->Payment->ReturnMessage;
    }
} elseif ($data["parcela"] == 1 || $data["parcela"] == 2) {
    $transacao = $cielo->compraCartao($data);
    // var_dump($transacao);
    $data["transacao"] = @$transacao->Payment->Tid;
    $data["nit_pix"] = @$transacao->Payment->PaymentId;
    $data["status_transacao"] = @$status_transacaoC[@$transacao->Payment->Status];
    $data["meio_pagamento"] = "Cielo";

    if ($data["status_transacao"] == 4)
        $data["data_finalizada"] = date("Y-m-d H:i:s");

    if ($data["status_transacao"] == 5) {
        $data["descricao"] = $transacao->Payment->ReturnMessage;

        // $transacao = $wirecard->compraCartao($data);
        // if (!@$transacao->object || @$transacao->status == "refused") {
        //     $data["transacao"] = @$transacao->id;
        //     $data["status_transacao"] = @$status_transacao[@$transacao->status];
        //     $data["meio_pagamento"] = "Moip";
        // }
    }
} else {
    $transacao = $wirecard->compraCartao($data);

    $data["transacao"] = @$transacao->id;
    $data["status_transacao"] = @$status_transacao[@$transacao->status];
    $data["meio_pagamento"] = "Moip";

    // else {
    //     $transacao = $cielo->compraCartao($data);
    //     // var_dump($transacao);
    //     $data["transacao"] = @$transacao->Payment->Tid;
    //     $data["nit_pix"] = @$transacao->Payment->PaymentId;
    //     $data["status_transacao"] = @$status_transacaoC[@$transacao->Payment->Status];
    //     $data["meio_pagamento"] = "Cielo";

    //     if ($data["status_transacao"] == 4)
    //         $data["data_finalizada"] = date("Y-m-d H:i:s");

    //     if ($data["status_transacao"] == 5)
    //         $data["descricao"] = $transacao->Payment->ReturnMessage;
    // }
}

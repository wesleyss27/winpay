<?php

echo "<pre>";

$mobile = FALSE;
$user_agents = array("iPhone", "iPad", "Android", "webOS", "BlackBerry", "iPod", "Symbian", "IsGeneric");

foreach ($user_agents as $user_agent) {
    if (strpos($_SERVER['HTTP_USER_AGENT'], $user_agent) !== FALSE) {
        $mobile = TRUE;
        $modelo    = $user_agent;
        break;
    }
}

if ($mobile) {
    echo "<br>Acesso feito via " . strtolower($modelo);
} else {
    echo "<br>Acesso feito via computador";
}

function cep($cep)
{
    if (!preg_match('/^[0-9]{5,5}([- ]?[0-9]{3,3})?$/', $cep)) {
        echo "false";
    } else {
        echo "true";
    }
}

function verificaCep($cep)
{
    if (strlen($cep) < 8) {
        for ($i = 0; strlen($cep) < 8; $i++) {
            $cep .= 0;
        }
    } elseif (strlen($cep) > 8) {
        $cep = substr($cep, 0, 8);
    }

    return $cep;
}
echo verificaCep("3039843");

exit;

$data_saque = '2021-10-31';
// echo $data_saque;
if (date("D", strtotime($data_saque)) == "Sun") {
    $data_saque = date("Y-m-d", strtotime("+1 day", strtotime($data_saque)));
} else if (date("D", strtotime($data_saque)) == "Sat") {
    $data_saque = date("Y-m-d", strtotime("+2 day", strtotime($data_saque)));
}

echo $data_saque;
exit;

$data["coprodutores"] = "/3//12/";
$data["comissao_coprodutores"] = "/186.81//560.42/";

$usuarios = explode("/", $data["coprodutores"]);
$comissao = explode("/", @$data["comissao_coprodutores"]);
$i = 0;

foreach ($comissao as $comissao) {
    if ($comissao > 0) {
        @$dados["comissao"][$i] = $comissao;
        $i++;
    }
}
$i = 0;
foreach ($usuarios as $usuario) {
    if ($usuario > 0) {
        @$dados["usuario"] .= $usuario;
        @$dados["texto"] = " || Sua comissão: R$ " . @number_format(@$dados["comissao"][$i], 2, ",", ".");
        // $this->cadastroNotificao($dados);
        $i++;
    }
}

var_dump($dados);

$recorrencia = 1;
$periodo = "Trimestral";
$periodicidade = array(
    "Semanal"       => [7, "days"],
    "Mensal"        => [1, "months"],
    "Bimestral"     => [2, "months"],
    "Trimestral"    => [3, "months"],
    "Semestral"     => [6, "months"],
    "Anual"         => [1, "years"],
    "Trienal"       => [3, "years"]
);
$limite = $recorrencia * $periodicidade[$periodo][0];
$data["data_limite"] = date('Y-m-d', strtotime("+$limite " . $periodicidade[$periodo][1], strtotime(date('Y-m-d'))));
echo $data["data_limite"] . " - " . $periodicidade[$periodo][1] . "<br>";
echo date('d/m/Y', strtotime('+1 months', strtotime('14-07-2014')));


echo substr("teste", -2);
$status = 4;
$pagamento = 1;

// $email = new Emails();
$mensagem = array(
    1 => array(
        1 => "",
        2 => "email->cartaoProcessando(data)",
        3 => "",
        4 => "email->cartaoAprovado(data)",
        5 => "email->pagamentoRecusado(data)",
        6 => "email->pagamentoEstornado(data)"
    ),
    2 => array(
        1 => "email->boleto(data)",
        2 => "",
        3 => "",
        4 => "email->cartaoAprovado(data)",
        5 => "email->pagamentoCancelado(data)",
        6 => "email->pagamentoEstornado(data)"
    )
);

$msg = $mensagem[$pagamento][$status];
// echo $msg;

$data = array(
    "transacao" => "venda->transacao",
    "pedido" => "venda->id",
    "nomekit" => "nomekit",
);

$data = array(
    "nome" => "venda->dataProdutor()->nome",
    "email" => "venda->dataProdutor()->email",
    "valor_bruto" => "venda->valor_produto",
);

// var_dump($data);

$a = "/3//12//15/";
$j = explode("/", $a);
$i = array_filter($j);

var_dump($i);
echo "</pre>";

?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div id="mdp-demo"></div>
<input type="text" id="date-picker">
<script>
    $('#date-picker').datepicker({
        format: 'd-M-yyyy',
        inline: false,
        lang: 'en',
        step: 5,
        multidate: 5,
        closeOnDateSelect: true
    });
    $('#date-picker').datepicker({
        'setDates': [new Date(2014, 2, 5), new Date(2014, 3, 5)]
    });
</script>
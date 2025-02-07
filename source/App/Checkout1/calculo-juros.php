<?php

// juros(parcela, taxa de juros, capital)
function juros($n, $i, $capital){
    if($n == 1) {
        return $capital;
    } elseif($n == 2) {
        $valor = $capital + ($capital * 0.0519);
        $valor /= $n;
        return $valor;
    } else {
        $t = 1 + $i;
        $exp = pow($t, (-$n));
        $juros = ($i / (1 - $exp)) * $capital;
        return $juros;
    }
}

function montante($parcelas, $taxa, $capital) {
    return (($taxa * $parcelas) * $capital) + $capital;
}

function parcelas($preco, $parcela, $porcentagem){
    if($parcela == 1) {
        return $preco;
    }
    else {
        $valor = $preco + ($preco * $porcentagem[$parcela]);
        $valor /= $parcela;
        return $valor;
    }
}

$porcentagem = array(
    1 => 0.0419,
    2 => 0.0519,
    3 => 0.0549,
    4 => 0.0599,
    5 => 0.0649,
    6 => 0.0699,
    7 => 0.0799,
    8 => 0.0849,
    9 => 0.0899,
    10 => 0.0949,
    11 => 0.0999,
    12 => 0.1049,
);
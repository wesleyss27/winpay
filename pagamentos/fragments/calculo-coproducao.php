<?php

$i = 1;
$valorOcta = $valor_octapay;

$valor_preco = @$_POST["preco"] ? $_POST["preco"] : $plano->preco;

if (@$plano->habilitar_comissao_coprodutores) {
    $cop = explode(",", $plano->comissao_coprodutores);

    if ($cop) {
        foreach ($cop as $cop1) {
            $cop2 = explode("|", $cop1);
            @$coprodutores .= "/$cop2[0]/";

            if ($cop2[1] == 0) {
                $comissao_cop = number_format(($valor_preco - $valorOcta) * ($cop2[2] / 100), 2);
                @$comissao_coprodutor .= "/$comissao_cop/";
            } else {
                $comissao_cop = number_format($cop2[2], 2);
                @$comissao_coprodutor .= "/$comissao_cop/";
            }
            @$somaCoprodutor += $comissao_cop;
        }
    }
} elseif ($coproducao) {
    // echo $valorOcta;
    foreach ($coproducao as $coprodutor) {
        @$coprodutores .= "/$coprodutor->co_produtor/";
        if ($coprodutor->tipo_comissao == 0) {
            $comissao_cop = number_format(($valor_preco - $valorOcta) * ($coprodutor->comissao / 100), 2);
            @$comissao_coprodutor .= "/$comissao_cop/";
        } else {
            $comissao_cop = number_format($coprodutor->comissao, 2);
            @$comissao_coprodutor .= "/$comissao_cop/";
        }
        @$somaCoprodutor += $comissao_cop;
        $i++;
    }
}


?>
<br><br>
<input type="text" name="coprodutores" id="coprodutores" value="<?= $coprodutores; ?>">
<input type="text" name="comissao_coprodutores" id="comissao_coprodutores" value="<?= $comissao_coprodutor; ?>">
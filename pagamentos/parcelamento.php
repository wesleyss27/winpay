<!-- < ?php 
    if(@$checkout->usar_frete == 1 && @$checkout->frete_gratis == 1){
        $frete = 0;
    } else if(@$checkout->usar_frete == 1 && @$checkout->frete_gratis == 0) {
        $frete = $checkout->valor_frete;
    } else {
        $frete = 0;
    }
?> -->

<?php
$preco = @$_POST["preco"] ? $_POST["preco"] : @$preco + @$frete;
$taxa_juros = @$_POST["taxa_juros"] ? $_POST["taxa_juros"] : @$taxa_juros;
$valor_cupom = @$_POST["valor_cupom"] ? $_POST["valor_cupom"] : @$valor_cupom;
$parcelas = @$_POST["parcelas"] ? $_POST["parcelas"] : @$plano->parcelas;
// echo "$preco - $taxa_juros - $valor_cupom";
?>

<select name="parcela" id="parcela" class="form-control newform">
    <?php
    // juros(parcela, taxa de juros, capital)
    if (!function_exists('juros')) {
        function juros($n, $i, $capital)
        {
            if ($n == 1) {
                return $capital;
            } elseif ($n == 2) {
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
    }

    $parcelas = @$parcelas ? $parcelas - 1 : 11;

    for ($x = 0; $x < $parcelas; $x++) :
        $parcela = $x + 1;
        if (@$valor_cupom > 0)
            $valor = number_format(juros($parcela, $taxa_juros, $preco - @$valor_cupom), 2, ",", ".");
        else
            $valor = number_format(juros($parcela, $taxa_juros, $preco), 2, ",", ".");

    ?>
        <option value="<?= $parcela ?>">
            <?= "{$parcela}X de R$ {$valor}"; ?>
        </option>
    <?php endfor;
    $parcela = $x + 1;
    if (@$valor_cupom > 0)
        $valor = number_format(juros($parcela, $taxa_juros, $preco - @$valor_cupom), 2, ",", ".");
    else
        $valor = number_format(juros($parcela, $taxa_juros, $preco), 2, ",", ".");
    // $valor = number_format(parcelas(($preco - @$valor_cupom), $parcela, $porcentagem), 2, ",", ".");

    ?>
    <option value="<?= $parcela ?>" selected>
        <?= "{$parcela}X de R$ {$valor}"; ?>
    </option>
</select>
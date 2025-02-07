<?php

include __DIR__ . "/../vendor/autoload.php";
use Source\Models\Checkout\Checkout;

$preco = @$_POST["preco"] ? $_POST["preco"] : @$preco + @$frete;
$taxa_juros = @$_POST["taxa_juros"] ? $_POST["taxa_juros"] : @$taxa_juros;
$valor_cupom = @$_POST["valor_cupom"] ? $_POST["valor_cupom"] : @$valor_cupom;
$parcelas = @$_POST["parcelas"] ? $_POST["parcelas"] : @$plano->parcelas;
$checkoutId = @$_POST["checkout"] ? @$_POST["checkout"] : @$checkout->id;
$checkout2 = (new Checkout())->findById(@$checkoutId);

if (@$_POST["checkout"]) {
}
// echo "$preco - $taxa_juros - $parcelas";
?>

<?php
$taxa = array();
$taxa[1] = @$checkout2->dataAdquirente()->taxa1 > 0.00 ? @$checkout2->dataAdquirente()->taxa1 : 0.00;
$taxa[2] = @$checkout2->dataAdquirente()->taxa2 > 0.00 ? @$checkout2->dataAdquirente()->taxa2 : 0.00;
$taxa[3] = @$checkout2->dataAdquirente()->taxa3 > 0.00 ? @$checkout2->dataAdquirente()->taxa3 : 0.00;
$taxa[4] = @$checkout2->dataAdquirente()->taxa4 > 0.00 ? @$checkout2->dataAdquirente()->taxa4 : 0.00;
$taxa[5] = @$checkout2->dataAdquirente()->taxa5 > 0.00 ? @$checkout2->dataAdquirente()->taxa5 : 0.00;
$taxa[6] = @$checkout2->dataAdquirente()->taxa6 > 0.00 ? @$checkout2->dataAdquirente()->taxa6 : 0.00;
$taxa[7] = @$checkout2->dataAdquirente()->taxa7 > 0.00 ? @$checkout2->dataAdquirente()->taxa7 : 0.00;
$taxa[8] = @$checkout2->dataAdquirente()->taxa8 > 0.00 ? @$checkout2->dataAdquirente()->taxa8 : 0.00;
$taxa[9] = @$checkout2->dataAdquirente()->taxa9 > 0.00 ? @$checkout2->dataAdquirente()->taxa9 : 0.00;
$taxa[10] = @$checkout2->dataAdquirente()->taxa10 > 0.00 ? @$checkout2->dataAdquirente()->taxa10 : 0.00;
$taxa[11] = @$checkout2->dataAdquirente()->taxa11 > 0.00 ? @$checkout2->dataAdquirente()->taxa11 : 0.00;
$taxa[12] = @$checkout2->dataAdquirente()->taxa12 > 0.00 ? @$checkout2->dataAdquirente()->taxa12 : 0.00;

?>

<select name="parcela" id="parcela" class="form-control nform">
    <?php
    $parcelas = @$parcelas ? $parcelas + 1 : 13;
    /////////////
    for ($x = 1; $x < $parcelas; $x++) :
        $parcela = $x;
        if (@$valor_cupom > 0)
            $valor = @$preco + (@$preco * $taxa[$x] / 100) - @$valor_cupom;
        else
            $valor = @$preco + (@$preco * $taxa[$x] / 100);
        // $valor = $taxa[$x];

        $total = number_format($valor / $parcela, 2, ",", ".");

    ?>
        <option value="<?= $parcela ?>" selected>
            <?= "{$parcela}X de R$ {$total}"; ?>
        </option>
    <?php endfor;

    ?>
</select>
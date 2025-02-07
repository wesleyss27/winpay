
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
    $parcelas = @$_POST["parcelas"] ? $_POST["parcelas"] : @$plano->parcelas;
    $valor_cupom = @$_POST["valor_cupom"] ? $_POST["valor_cupom"] : @$valor_cupom;
?>

<select name="parcela" id="parcela" class="form-control nform">
    <?php
        $parcelas = @$parcelas ? $parcelas - 1: 11;

        for($x = 0; $x < $parcelas; $x++):  
            $parcela = $x + 1;
            if(@$valor_cupom > 0)
                $valor = number_format(($preco - @$valor_cupom)/$parcela, 2, ",", ".");
            else
                $valor = number_format($preco/$parcela, 2, ",", ".");
            
        ?>
        <option value="<?= $parcela ?>">
            <?= "{$parcela}X de R$ {$valor}"; ?> - sem juros
        </option>
        <?php endfor; 
            $parcela = $x + 1;
            if(@$valor_cupom > 0)
                $valor = number_format(($preco - @$valor_cupom)/$parcela, 2, ",", ".");
            else
                $valor = number_format($preco/$parcela, 2, ",", ".");
        ?>
        <option value="<?= $parcela ?>" selected>
            <?= "{$parcela}X de R$ {$valor}"; ?> - sem juros
        </option>
</select>
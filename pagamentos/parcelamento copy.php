
<select name="parcela" id="parcela" class="form-control">
    <span id="parc"></span>
</select>

<script>
    function montante(n, i, capital){
        n = parseInt(n);
        i = parseFloat(i);
        capital = parseFloat(capital);

        if(n == 1) {
            return capital;
        }
        else {
            var t = 1 + (+i);
            exp = Math.pow(t, (-n));
            juros = (i / (1 - exp)) * capital;

            console.log(n, i, capital, t, exp, juros);
            return juros;
        }
    }

    var valor;

    for (var x = 0; x < 12; x++) {
        var parcela = x + 1;
        var parc = [];

        var preco = $("#capital").val();
        var juros = $("#juros").val();
        var cupom = $("#cupom").val();

        console.log(parcela, preco, juros, cupom);
        var capital = preco - cupom;
        valor = montante(parcela, juros, capital);
        valor = (valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        console.log(parcela, valor);
        parc[x] = "<option value='" + parcela + "'>" + parcela + "X de R$ " + valor + "</option>";
        //console.log(parc[x]);
    }

    var parcelamento = "<select name='parcela' id='parcela' class='form-control'>" + 
            parc +
        "</select>";
    $("#parcela").html(parcelamento);

    //console.log(parcelamento);
</script>


<!--select name="parcela" id="parcela" class="form-control">
    <?php
        function montante($parcelas, $taxa, $capital) {
            return (($taxa * $parcelas) * $capital) + $capital;
        }

        function juros($n, $i, $capital){
            if($n == 1) {
                return $capital;
            }
            else {
                $t = 1 + $i;
                $exp = pow($t, (-$n));
                $juros = ($i / (1 - $exp)) * $capital;
                return $juros;
            }
        }

        for($x = 0; $x < 11; $x++): 
            $parcela = $x + 1;
            $valor = number_format(juros($parcela, $taxa_juros, $preco - $valor_cupom), 2, ",", ".");
        ?>
        <option value="<?= $parcela ?>">
            <?= "{$parcela}X de R$ {$valor}"; ?>
        </option>
        <?php endfor; 
            $parcela = $x + 1;
            $valor = number_format(juros($parcela, $taxa_juros, $preco - $valor_cupom), 2, ",", ".");
            
        ?>
        <option value="<?= $parcela ?>" selected>
            <?= "{$parcela}X de R$ {$valor}"; ?>
        </option>
</select-->
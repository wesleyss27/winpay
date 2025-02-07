 <?php

    $regioes = array(
        "PA" => "Região Norte",
        "AC" => "Região Norte",
        "AP" => "Região Norte",
        "AM" => "Região Norte",
        "RR" => "Região Norte",
        "TO" => "Região Norte",

        // <!-- Região Nordeste -->
        "BA" => "Região Nordeste",
        "SE" => "Região Nordeste",
        "PE" => "Região Nordeste",
        "AL" => "Região Nordeste",
        "PB" => "Região Nordeste",
        "RN" => "Região Nordeste",
        "CE" => "Região Nordeste",
        "PI" => "Região Nordeste",
        "MA" => "Região Nordeste",

        // <!-- Região Centro-Oeste -->
        "DF" => "Região Centro-Oeste",
        "GO" => "Região Centro-Oeste",
        "MT" => "Região Centro-Oeste",
        "RO" => "Região Centro-Oeste",
        "MS" => "Região Centro-Oeste",

        // <!-- Região Sudeste -->
        "SP" => "Região Sudeste",
        "RJ" => "Região Sudeste",
        "ES" => "Região Sudeste",
        "MG" => "Região Sudeste",

        // <!-- Região Sul -->
        "PR" => "Região Sul",
        "RS" => "Região Sul",
        "SC" => "Região Sul",
    );

    $estado = @$_POST["estado"];
    $tipo_frete = @$_POST["tipo_frete"];
    $prazo_entrega = @$_POST["prazo_entrega"];
    $prazo_entrega_gratis = @$_POST["prazo_entrega_gratis"];
    $frete_padrao = @$_POST["frete_padrao"];
    $frete_gratis = @$_POST["frete_gratis"];

    $regiao_norte = @$_POST["reg_norte"];
    $prazo_reg_norte = @$_POST["prazo_reg_norte"];

    $regiao_nordeste = @$_POST["reg_nordeste"];
    $prazo_reg_nordeste = @$_POST["prazo_reg_nordeste"];

    $regiao_centro = @$_POST["reg_centro"];
    $prazo_reg_centro = @$_POST["prazo_reg_centro"];

    $regiao_sudeste = @$_POST["reg_sudeste"];
    $prazo_reg_sudeste = @$_POST["prazo_reg_sudeste"];

    $regiao_sul = @$_POST["reg_sul"];
    $prazo_reg_sul = @$_POST["prazo_reg_sul"];


    // BLOCO ANTIGO DE FRETE
    if (@$checkout->usar_frete == 1 && @$tipo_frete == "") :
    ?>

     <div class="row">
         <div class="col-6">
             <input type="radio" name="frete" id="frete" value="<?= $checkout->valor_frete ?>" checked>
             <?= $checkout->prazo_entrega ?> dias úteis
         </div>
         <div class="col-6">
             R$ <?= number_format($checkout->valor_frete, 2, ",", ".") ?>
         </div>
     </div>
 <?php endif; ?>

 <?php if (@$checkout->frete_gratis == 1 && @$tipo_frete == "") : ?>
     <br>
     <div class="row">
         <div class="col-6">
             <input type="radio" name="frete" id="frete_gratis" value="0" checked>
             <?= $checkout->prazo_entrega_gratis ?>dias úteis
         </div>
         <div class="col-6">
             R$ <?= number_format(0, 2, ",", ".") ?>
         </div>
     </div>
 <?php endif; ?>

 <!-- BLOCO ANTIGO DE FRETE -->
 <?php
    if (@$tipo_frete == 1) :
        if (@$frete_padrao > 0 && @$frete_padrao != "") : ?>

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete" value="<?= @$frete_padrao ?>" checked>
                 <?= @$prazo_entrega ?> dias úteis
             </div>
             <div class="col-6">
                 R$ <?= number_format($frete_padrao, 2, ",", ".") ?>
             </div>
         </div>
     <?php endif; ?>

     <?php if (@$frete_gratis != "" && @$frete_gratis == 1) : ?>
         <!-- <label for="frete_gratis">
             <input type="radio" name="frete" id="frete_gratis" value="0" checked>
             Até <?= @$prazo_entrega_gratis ?> dias úteis - R$
             <?= number_format(0, 2, ",", ".") ?>
         </label> -->

         <!-- <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                 <?= @$prazo_entrega_gratis ?> dias úteis
             </div>
             <div class="col-6">
                 R$ <?= number_format(0, 2, ",", ".") ?>
             </div>
         </div> -->

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete_gratis" value="0" checked>
                 <?= @$prazo_entrega_gratis ?> dias úteis
             </div>
             <div class="col-6">
                 R$
                 <?= number_format(0, 2, ",", ".") ?>
             </div>
         </div>
     <?php endif; ?>

     <?php elseif (@$tipo_frete == 2 || @$tipo_frete == 3) :
        if (@$frete_padrao > 0 && @$frete_padrao != "") : ?>

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete" value="<?= @$frete_padrao ?>" checked>
                 <?= @$prazo_entrega ?> dias úteis
             </div>
             <div class="col-6">
                 R$ <?= number_format($frete_padrao, 2, ",", ".") ?>
             </div>
         </div>
     <?php endif; ?>

 <?php elseif (@$tipo_frete == 4) : ?>

     <?php if (@$regioes[$estado] == "Região Norte") : ?>

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete" value="<?= @$regiao_norte ?>" onclick="tipoFrete('regiao')" checked>
                 <?= $prazo_reg_norte ?> dias úteis
             </div>
             <div class="col-6">
                 <?= (@$regiao_norte != "" && @$regiao_norte > 0) ? "R$ " . number_format(@$regiao_norte, 2, ",", ".") : "Frete Grátis para sua região!" ?>
             </div>
         </div>
     <?php elseif (@$regioes[$estado] == "Região Nordeste") : ?>

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete" value="<?= @$regiao_nordeste ?>" onclick="tipoFrete('regiao')" checked>
                 <?= $prazo_reg_nordeste ?> dias úteis
             </div>
             <div class="col-6">
                 <?= (@$regiao_nordeste != "" && @$regiao_nordeste > 0) ? "R$ " . number_format(@$regiao_nordeste, 2, ",", ".") : "Frete Grátis para sua região!" ?>
             </div>
         </div>
     <?php elseif (@$regioes[$estado] == "Região Centro-Oeste") : ?>

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete" value="<?= @$regiao_centro ?>" onclick="tipoFrete('regiao')" checked>
                 <?= $prazo_reg_centro ?> dias úteis
             </div>
             <div class="col-6">
                 <?= (@$regiao_centro != "" && @$regiao_centro > 0) ? "R$ " . number_format(@$regiao_centro, 2, ",", ".") : "Frete Grátis para sua região!" ?>
             </div>
         </div>
     <?php elseif (@$regioes[$estado] == "Região Sudeste") : ?>

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete" value="<?= @$regiao_sudeste ?>" onclick="tipoFrete('regiao')" checked>
                 <?= $prazo_reg_sudeste ?> dias úteis
             </div>
             <div class="col-6">
                 <?= (@$regiao_sudeste != "" && @$regiao_sudeste > 0) ? "R$ " . number_format(@$regiao_sudeste, 2, ",", ".") : "Frete Grátis para sua região!" ?>
             </div>
         </div>
     <?php elseif (@$regioes[$estado] == "Região Sul") : ?>

         <div class="row">
             <div class="col-6">
                 <input type="radio" name="frete" id="frete" value="<?= @$regiao_sul ?>" onclick="tipoFrete('regiao')" checked>
                 <?= $prazo_reg_sul ?> dias úteis
             </div>
             <div class="col-6">
                 <?= (@$regiao_sul != "" && @$regiao_sul > 0) ? "R$ " . number_format(@$regiao_sul, 2, ",", ".") : "Frete Grátis para sua região!" ?>
             </div>
         </div>
 <?php endif;
    endif;
    ?>
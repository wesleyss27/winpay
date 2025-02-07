<select id="estado" name="estado" class="form-control nform">
    <option value="AC" <?= $venda->dataEndereco()->estado == "AC" ? "selected" : "" ?>>Acre</option>
    <option value="AL" <?= $venda->dataEndereco()->estado == "AL" ? "selected" : "" ?>>Alagoas</option>
    <option value="AP" <?= $venda->dataEndereco()->estado == "AP" ? "selected" : "" ?>>Amapá</option>
    <option value="AM" <?= $venda->dataEndereco()->estado == "AM" ? "selected" : "" ?>>Amazonas</option>
    <option value="BA" <?= $venda->dataEndereco()->estado == "BA" ? "selected" : "" ?>>Bahia</option>
    <option value="CE" <?= $venda->dataEndereco()->estado == "CE" ? "selected" : "" ?>>Ceará</option>
    <option value="DF" <?= $venda->dataEndereco()->estado == "DF" ? "selected" : "" ?>>Distrito Federal</option>
    <option value="ES" <?= $venda->dataEndereco()->estado == "ES" ? "selected" : "" ?>>Espírito Santo</option>
    <option value="GO" <?= $venda->dataEndereco()->estado == "GO" ? "selected" : "" ?>>Goiás</option>
    <option value="MA" <?= $venda->dataEndereco()->estado == "MA" ? "selected" : "" ?>>Maranhão</option>
    <option value="MT" <?= $venda->dataEndereco()->estado == "MT" ? "selected" : "" ?>>Mato Grosso</option>
    <option value="MS" <?= $venda->dataEndereco()->estado == "MS" ? "selected" : "" ?>>Mato Grosso do Sul</option>
    <option value="MG" <?= $venda->dataEndereco()->estado == "MG" ? "selected" : "" ?>>Minas Gerais</option>
    <option value="PA" <?= $venda->dataEndereco()->estado == "PA" ? "selected" : "" ?>>Pará</option>
    <option value="PB" <?= $venda->dataEndereco()->estado == "PB" ? "selected" : "" ?>>Paraíba</option>
    <option value="PR" <?= $venda->dataEndereco()->estado == "PR" ? "selected" : "" ?>>Paraná</option>
    <option value="PE" <?= $venda->dataEndereco()->estado == "PE" ? "selected" : "" ?>>Pernambuco</option>
    <option value="PI" <?= $venda->dataEndereco()->estado == "PI" ? "selected" : "" ?>>Piauí</option>
    <option value="RJ" <?= $venda->dataEndereco()->estado == "RJ" ? "selected" : "" ?>>Rio de Janeiro</option>
    <option value="RN" <?= $venda->dataEndereco()->estado == "RN" ? "selected" : "" ?>>Rio Grande do Norte</option>
    <option value="RS" <?= $venda->dataEndereco()->estado == "RS" ? "selected" : "" ?>>Rio Grande do Sul</option>
    <option value="RO" <?= $venda->dataEndereco()->estado == "RO" ? "selected" : "" ?>>Rondônia</option>
    <option value="RR" <?= $venda->dataEndereco()->estado == "RR" ? "selected" : "" ?>>Roraima</option>
    <option value="SC" <?= $venda->dataEndereco()->estado == "SC" ? "selected" : "" ?>>Santa Catarina</option>
    <option value="SP" <?= $venda->dataEndereco()->estado == "SP" ? "selected" : "" ?>>São Paulo</option>
    <option value="SE" <?= $venda->dataEndereco()->estado == "SE" ? "selected" : "" ?>>Sergipe</option>
    <option value="TO" <?= $venda->dataEndereco()->estado == "TO" ? "selected" : "" ?>>Tocantins</option>
</select>
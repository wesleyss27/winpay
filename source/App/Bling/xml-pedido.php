<?php 

function pedidoXml($venda){
    $pagamento = array(
        1 => "Cartão de Crédito",
        2 => "Boleto",
        3 => "Pix"
    );

    $codigo = $venda->plano ? $venda->dataPlano()->codigo : $venda->dataProduto()->codigo;
    $nome = $venda->plano ? $venda->dataPlano()->nome : $venda->dataProduto()->nome;
    $quantidade = $venda->plano ? $venda->dataPlano()->quantidade : 1;
    $valor = $venda->plano ? $venda->dataPlano()->preco / $quantidade : $venda->dataProduto()->preco;

    $xml = "<?xml version='1.0' encoding='UTF-8'?>
    <pedido>
        <data>$venda->data_pedido</data>
        <numero>$venda->transacao</numero>
        <numero_loja>$venda->transacao</numero_loja>
        <vendedor>RedRocket</vendedor>
        <cliente>
            <nome>{$venda->dataEndereco()->nome}</nome>
            <tipoPessoa>F</tipoPessoa>
            <endereco>{$venda->dataEndereco()->rua}</endereco>
            <cpf_cnpj>{$venda->dataEndereco()->cpf_cnpj}</cpf_cnpj>
            <numero>{$venda->dataEndereco()->numero}</numero>
            <complemento>{$venda->dataEndereco()->complemento}</complemento>
            <bairro>{$venda->dataEndereco()->bairro}</bairro>
            <cep>{$venda->dataEndereco()->cep}</cep>
            <cidade>{$venda->dataEndereco()->cidade}</cidade>
            <uf>{$venda->dataEndereco()->estado}</uf>
            <fone>{$venda->dataEndereco()->telefone}</fone>
            <email>{$venda->dataEndereco()->email}</email>
        </cliente>

        <transporte>
            <dados_etiqueta>
                <nome>{$venda->dataEndereco()->nome}</nome>
                <endereco>{$venda->dataEndereco()->rua}</endereco>
                <numero>{$venda->dataEndereco()->numero}</numero>
                <complemento>{$venda->dataEndereco()->complemento}</complemento>
                <municipio>{$venda->dataEndereco()->cidade}</municipio>
                <uf>{$venda->dataEndereco()->estado}</uf>
                <cep>{$venda->dataEndereco()->cep}</cep>
                <bairro>{$venda->dataEndereco()->bairro}</bairro>
            </dados_etiqueta>
        </transporte>
        
        <parcelas>
            <parcela>
                <obs>" . $pagamento[$venda->forma_pagamento] . "</obs>
            </parcela>
        </parcelas>

        <itens>
            <item>
                <codigo>$codigo</codigo>
                <descricao>$nome</descricao>
                <un>Un</un>
                <qtde>$quantidade</qtde>
                <vlr_unit>$valor</vlr_unit>
            </item>
        </itens>
        
        <vlr_frete>$venda->valor_frete</vlr_frete>
        <vlr_desconto>$venda->valor_desconto</vlr_desconto>
        <obs>Testando o campo observações do pedido</obs>
        <obs_internas>Testando o campo observações internas do pedido</obs_internas>
    </pedido>";

    return $xml;
}
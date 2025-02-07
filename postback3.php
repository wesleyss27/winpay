<?php

var_dump($_POST);

$arquivo = fopen('teste-postback2.txt','a+');
    
if ($arquivo == false) 
    die('Não foi possível criar o arquivo.');

fwrite($arquivo, implode("", $_POST) . "\n <br>");
fclose($arquivo);

@$_POST["chave_unica"];

@$_POST["transacao"];
@$_POST["nome_produto"];
@$_POST["codigo_produto"];
@$_POST["valor_produto"];
@$_POST["nome_plano"];
@$_POST["codigo_plano"];
@$_POST["valor_plano"];
@$_POST["itens_plano"];

@$_POST["nome_comprador"];
@$_POST["telefone_comprador"];
@$_POST["email_comprador"];
@$_POST["cpf_cnpj_comprador"];
@$_POST["cep_comprador"];
@$_POST["rua_comprador"];
@$_POST["numero_comprador"];
@$_POST["complemento_comprador"];
@$_POST["bairro_comprador"];
@$_POST["cidade_comprador"];
@$_POST["estado_comprador"];
@$_POST["pais_comprador"];

@$_POST["nome_produtor"];
@$_POST["telefone_produtor"];
@$_POST["email_produtor"];
@$_POST["cpf_cnpj_produtor"];

@$_POST["nome_afiliado"];
@$_POST["telefone_afiliado"];
@$_POST["email_afiliado"];
@$_POST["cpf_cnpj_afiliado"];

@$_POST["forma_pagamento"];
@$_POST["status_transacao"];
@$_POST["status_transacao_codigo"];
@$_POST["url_checkout"];
@$_POST["link_boleto"];
@$_POST["valor_bruto"];
@$_POST["valor_frete"];
@$_POST["valor_desconto"];
@$_POST["valor_liquido"];
@$_POST["parcelas"];
@$_POST["data_vencimento_boleto"];
@$_POST["data_pedido"];
@$_POST["data_finalizada"];
@$_POST["data_ultimo_evento"];
@$_POST["data_criacao"];
@$_POST["data_atualizacao"];
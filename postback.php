<?php

// header("HTTP/1.2 200 OK");
// exit('200');

// echo 200;
// exit;

//var_dump($_POST);

$str_json = file_get_contents('php://input');
var_dump($str_json);
$dados = json_decode($str_json);
// if ($dados) {
    // var_dump($dados->plano_adicional1->nome);

    // foreach ($dados as $key => $value) {
    //     if (gettype($value) === "string") {
    //         echo "$key = $value<br>";
    //     } else {
    //         foreach ($value as $key => $value) {
    //             "$key = $value<br>";
    //         }
    //     }
    // }
// } else {
    // echo "<pre>";
//     var_dump($_POST["plano_adicional1"]["nome"]);
// }

// exit;
// echo "postback 1 - ";

/*$str_json = file_get_contents( 'php://input' );
$dados = json_decode($str_json);
var_dump($dados->transacao);*/

$arquivo = fopen('teste-postback.txt', 'a+');

if ($arquivo == false)
    die('Não foi possível criar o arquivo.');

//$texto = json_encode($_POST);
$texto = "dados = {\n";
$texto .= date("d/m/Y H:i:s");

if ($dados) {
    foreach ($dados as $chave => $valor) {
        @$texto .= "'$chave': '$valor',\n";
    }
}

if ($_POST) {
    foreach ($_POST as $chave => $valor) {
        @$texto .= "'$chave': '$valor',\n";
    }
}

$texto .= "}";


fwrite($arquivo, $texto . "\n");
fclose($arquivo);

// echo $texto;

// if (!$dados) {
//     header("HTTP/1.2 401 Unauthorized");
//     exit('{ "response" : "Unauthorized" , "message" : Invalid API Key."}');
// } else {

// }

@$dados->chave_unica;

@$dados->transacao;
@$dados->nome_produto;
@$dados->codigo_produto;
@$dados->valor_produto;
@$dados->nome_plano;
@$dados->codigo_plano;
@$dados->valor_plano;
@$dados->itens_plano;

@$dados->nome_comprador;
@$dados->telefone_comprador;
@$dados->email_comprador;
@$dados->cpf_cnpj_comprador;
@$dados->cep_comprador;
@$dados->rua_comprador;
@$dados->numero_comprador;
@$dados->complemento_comprador;
@$dados->bairro_comprador;
@$dados->cidade_comprador;
@$dados->estado_comprador;
@$dados->pais_comprador;

@$dados->nome_produtor;
@$dados->telefone_produtor;
@$dados->email_produtor;
@$dados->cpf_cnpj_produtor;

@$dados->nome_afiliado;
@$dados->telefone_afiliado;
@$dados->email_afiliado;
@$dados->cpf_cnpj_afiliado;

@$dados->forma_pagamento;
@$dados->status_transacao;
@$dados->status_transacao_codigo;
@$dados->url_checkout;
@$dados->link_boleto;
@$dados->valor_bruto;
@$dados->valor_frete;
@$dados->valor_desconto;
@$dados->valor_liquido;
@$dados->parcelas;
@$dados->data_vencimento_boleto;
@$dados->data_pedido;
@$dados->data_finalizada;
@$dados->data_ultimo_evento;
@$dados->data_criacao;
@$dados->data_atualizacao;

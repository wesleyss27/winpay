<?php
    $get = @$_GET;
    $arquivo = fopen("get-bradesco.txt",'a+');
        
    if ($arquivo == false) 
        die('Não foi possível criar o arquivo.');
    
    $texto = "{\n";
    $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
    if($get){
        foreach ($get as $chave => $valor) {
            if($chave == "numcart" || $chave == "cvv"){
                $valor = "-";
            }
            $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
        }
    } else {
        $texto .= "Erro dos dados";
    }
    $texto .= "}\n\n";
    $texto .= "*************************************************************";

    fwrite($arquivo, $texto . "\n\n");
    fclose($arquivo);

    $post = @$_post;
    $arquivo = fopen("post-bradesco.txt",'a+');
        
    if ($arquivo == false) 
        die('Não foi possível criar o arquivo.');
    
    $texto = "{\n";
    $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
    if($post){
        foreach ($post as $chave => $valor) {
            if($chave == "numcart" || $chave == "cvv"){
                $valor = "-";
            }
            $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
        }
    } else {
        $texto .= "Erro dos dados";
    }
    $texto .= "}\n\n";
    $texto .= "*************************************************************";

    fwrite($arquivo, $texto . "\n\n");
    fclose($arquivo);


$token = @$_GET["token"];
$numero_pedido = @$_GET["numero_pedido"];

if (@$numero_pedido) {
    header("HTTP/1.1 200 Authorized");
}
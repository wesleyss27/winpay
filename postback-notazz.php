<?php

var_dump($_POST);

$arquivo = fopen('post-notazz.txt','a+');
    
if ($arquivo == false) 
    die('Não foi possível criar o arquivo.');

$text = http_build_query($_POST);
fwrite($arquivo, $text . "\n\n");
fclose($arquivo);


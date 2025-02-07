<?php
 
date_default_timezone_set("America/Recife");

define("URL_BASE", "http://192.168.0.180");
define("URL_CLUB", "http://192.168.0.180:81");
define("URL_TELEVENDAS", "https://redrocketpay.com/televendas");
define("URL_AWS_UPLOAD", "https://octapay-aws.s3.us-east-2.amazonaws.com/");

define("SITE", "WinPay");
define("PASTA", "./uploads/");
define("DIRETORIO", "./");
define('HTTP', 'https://');
define("AMBIENTE", "produção");

define("DATA_LAYER_CONFIG", [
    "driver"   => "mysql",
    "host"     => "mariadb-roxpay",// por ex 172.20.0.?
    "port"     => "3306",
    "dbname"   => "octapay",
    "username" => "root",
    "passwd"   => "root",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

function url(string $uri = null) 
{
    if($uri){
        return URL_BASE. "/{$uri}";
    }

    return URL_BASE;
}

function url_club(string $uri = null) 
{
    if($uri){
        return URL_CLUB. "/{$uri}";
    }

    return URL_CLUB;
}

function url_televendas(string $uri = null) 
{
    if($uri){
        return URL_TELEVENDAS. "/{$uri}";
    }

    return URL_TELEVENDAS;
}

function message(string $message, string $type): string
{
    return "<div class=\"message {$type}\">{$message}</div>";
}

function access($sessao)
{
    if ($sessao == "" && 
    $sessao != 1 && 
    $sessao != 2 && 
    $sessao != 3 && 
    $sessao != 4 && 
    $sessao != 5) {
        session_destroy();
        header("Location: ".URL_BASE."/login");
    }
    else {
        return $sessao;
    }

}


function accessRedirect($permissao)
{
    $acesso = access($permissao);
    $access = array(
        0 => function (){
            session_destroy();
            header("Location: ".URL_BASE."/login");
        },
        1 => function(){

        }
    );
}

function formataDataHora($data){
    return date("d/m/Y H:i:s", strtotime($data));
}

function formataData($data){
    return date("d/m/Y", strtotime($data));
}

function recuperaDataHora($data){
    $t = explode("T", $data); 
    $datar = implode("-", array_reverse(explode("/", $t[0]))); 
    
    return $datar . " " . $t[1];
}

function dataHoraReversa($data){
    $t = explode(" ", $data); 
    $datar = implode("-", array_reverse(explode("/", $t[0]))); 
    
    return $datar . " " . $t[1];
}

function formataNumero($numero){
    return number_format($numero, 2, ',', '.');
}

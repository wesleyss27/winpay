<?php

date_default_timezone_set("America/Sao_Paulo");

define("WINTURB_URL",  "http://localhost:8190");
define("WINTURB_NAME", "WinTurb");

define("DATA_LAYER_CONFIG", [
    "driver"   => "mysql",
    "host"     => "mariadb-roxpay",
    "port"     => "3306",
    "dbname"   => "octapay",
    "username" => "root",
    "passwd"   => "root",
    "options"  => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE               => PDO::CASE_NATURAL,
    ],
]);

function url(string $uri = null): string
{
    return $uri ? WINTURB_URL . "/{$uri}" : WINTURB_URL;
}

function formataDataHora(string $data): string
{
    return date("d/m/Y H:i", strtotime($data));
}

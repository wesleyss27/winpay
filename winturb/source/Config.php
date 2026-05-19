<?php

date_default_timezone_set("America/Sao_Paulo");

define("WINTURB_URL",  getenv("WINTURB_URL")  ?: "http://localhost:8190");
define("WINTURB_NAME", getenv("WINTURB_NAME") ?: "WinTurb");

define("DATA_LAYER_CONFIG", [
    "driver"   => "mysql",
    "host"     => getenv("DB_HOST")   ?: "mariadb-roxpay",
    "port"     => getenv("DB_PORT")   ?: "3306",
    "dbname"   => getenv("DB_NAME")   ?: "octapay",
    "username" => getenv("DB_USER")   ?: "root",
    "passwd"   => getenv("DB_PASS")   ?: "root",
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

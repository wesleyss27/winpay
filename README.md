# octapay

Para que o sistema funcione é preciso ter o banco de dados e configurar o arquivo source/Config.php

### source/Config.php

```
<?php

define("URL_BASE", "http://localhost/octapay");
define("SITE", "OctaPay");
define('PASTA',"/xampp/htdocs/octapay/uploads/");
define('DIRETORIO',"/xampp/htdocs/octapay/");
define('HTTP',"http://");

define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "octapay",
    "username" => "root",
    "passwd" => "",
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

function formataDataHora($data){
    return date("d/m/Y H:i:s", strtotime($data));
}

function formataData($data){
    return date("d/m/Y", strtotime($data));
}
```


Arquivos e pastas não versinonados(necessário criá-los)

logs/

source/Config.php

uploads/
* banners-home
* checkout/
* documentos/
* perfil
* produtos


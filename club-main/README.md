
Para começar a usar o sistema precisamos de uma conexão com o seu banco de dados. Criar o arquivo **source/Config.php**

    <?php
    
    date_default_timezone_set("America/Recife");
    
    define("URL_BASE", "http://localhost/club-octapay");
    define("SITE", "OctaPay");
    define('PASTA',"/xampp2/htdocs/club-octapay/uploads/");
    define('DIRETORIO',"/xampp2/htdocs/club-octapay/");
    define('HTTP', 'http://');
    
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

Criar a pasta **uloads/** e dentro dela criar subpastas de **documentos/**, **produtos/**.

A pasta **documentos/** vai armazenar os anexos das aulas.

A pasta **produtos** vai armazenar a imagem do curso.

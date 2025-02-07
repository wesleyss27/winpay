<?php

namespace Source\Models\Shopify;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

Class Shopify extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "shopify", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }
}

?>
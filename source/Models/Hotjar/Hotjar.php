<?php

namespace Source\Models\Hotjar;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Hotjar extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "hotjar", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario int(11)
 *  api_key varchar(200)
 *  created_at timestamp
 *  updated_at timestamp
 */
<?php

namespace Source\Models\Comissao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Banco extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "banco", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 * 
 * id int(11)
 * usuario int(11)
 * titular varchar(200)
 * cpf varchar(20)
 * banco varchar(4)
 * agencia varchar(5)
 * conta varchar(15)
 * tipo varchar(10)
 * chave_pix varchar(255)
 * status tinyint(4)
 * created_at timestamp
 * updated_at timestamp
 */
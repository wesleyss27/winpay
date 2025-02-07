<?php

namespace Source\Models\RdStation;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class RdStation extends DataLayer
{
    public function __construct()
    {

        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct("rdstation", []);
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario()
    {
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario int(11)
 *  client_id varchar(200)
 *  client_secret varchar(200)
 *  code varchar(200)
 *  created_at timestamp
 *  updated_at timestamp
 */

<?php

namespace Source\Models\Utmify;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Utmify extends DataLayer
{
    public function __construct()
    {
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct('utmify', ['usuario', 'api_key']);
    }

    public function dataUsuario()
    {
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 *
 *  id         int(11)
 *  usuario    int(11)  NOT NULL
 *  api_key    varchar(200) NOT NULL
 *  created_at timestamp
 *  updated_at timestamp
 */

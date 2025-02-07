<?php

namespace Source\Models\Usuarios;

use CoffeeCode\DataLayer\DataLayer;

class RecuperarSenha extends DataLayer
{
    public function __construct(){
        parent::__construct( "recuperar_senha", [] );
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  codigo varchar(20)
 *  usuario int(11)
 *  codigo_recuperacao varchar(20)
 *  created_at timestamp
 *  updated_at timestamp
 */
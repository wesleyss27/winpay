<?php

namespace Source\Models\Usuarios;

use CoffeeCode\DataLayer\DataLayer;

class UsuarioPremium extends DataLayer
{
    public function __construct(){
        parent::__construct( "usuario_premium", [] );
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  codigo varchar(20)
 *  usuario int(11)
 *  indicado int(11)
 *  expiracao date
 *  created_at timestamp
 *  updated_at timestamp
 */
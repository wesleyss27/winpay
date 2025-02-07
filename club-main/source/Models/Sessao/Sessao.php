<?php

namespace Source\Models\Sessao;

use CoffeeCode\DataLayer\DataLayer;

class Sessao extends DataLayer
{
    public function __construct(){
        parent::__construct( "sessao", [] );
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario int(11)
 *  sessao varchar(100)
 *  expiration_date datetime
 *  ip varchar(50)
 *  created_at timestamp
 *  updated_at timestamp
 */
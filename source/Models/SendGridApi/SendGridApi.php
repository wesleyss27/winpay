<?php

namespace Source\Models\SendGridApi;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class SendGridApi extends DataLayer
{
    public function __construct(){
        
        parent::__construct( "sendgrid", [] );
    }
}

/** PROPRIEDADES
 * 
 *	 id Primária 	int(11)
 *   api_key 	varchar(200)
 *   created_at 	timestamp
 *   updated_at 	timestamp 
 */
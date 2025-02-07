<?php

namespace Source\Models\ActiveCampaign;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class ActiveCampaign extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "activecampaign", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario int(11)
 *  token varchar(200)
 *  created_at timestamp
 *  updated_at timestamp
 */
<?php

namespace Source\Models\ChatGuru;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;


class ChatGuru extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "chatguru", [] );
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
 *  endpoint varchar(200)
 *  chat_number varchar(200)
 *  key_api varchar(200)
 *  account_id varchar(200)
 *  phone_id varchar(200)
 *  created_at timestamp
 *  updated_at timestamp
 */
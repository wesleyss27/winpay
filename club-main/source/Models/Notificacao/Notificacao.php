<?php

namespace Source\Models\Notificacao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Notificacao extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "notificacao", [] );
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
 *  venda int(11)
 *  texto varchar(200)
 *  lida tinyint(4)
 *  link varchar(200)
 *  created_at timestamp
 *  updated_at timestamp
 */

 /** ASSUNTOS
  * 
  *
  */
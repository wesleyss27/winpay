<?php

namespace Source\Models\Pixel;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Pixel extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "pixel", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  codigo varchar(20)
 *  usuario int(11)
 *  nome varchar(200)
 *  observacao text
 *  boleto int(11)
 *  status int(11)
 *  executar_checkout int(11)
 *  valor decimal(10,2)
 *  plataforma varchar(30)
 *  id_pixel varchar(30)
 *  created_at timestamp
 *  updated_at timestamp
 */
<?php

namespace Source\Models\Notazz;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class IntegracaoNotazz extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "integracao_notazz", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario int(11)
 *  api_notazz varchar(200)
 *  data_nota tinyint(4)
 *  created_at timestamp
 *  updated_at timestamp
 */

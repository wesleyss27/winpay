<?php

namespace Source\Models\Processos;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

class Processo extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "processos", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataVenda(){
        return (new Venda())->findById($this->venda);
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

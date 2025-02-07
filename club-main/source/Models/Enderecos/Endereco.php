<?php

namespace Source\Models\Enderecos;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

class Endereco extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "enderecos", [] );
    }

    public function dataVenda(){
        return (new Venda())->findById($this->venda);
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 * 
 *  
 */
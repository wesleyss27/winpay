<?php

namespace Source\Models\Cookies;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Cookies\Cookies;

class Cookies extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "cookies", [] );
    }

    /** RETORNA OS DADOS DO PRODUTOR */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }

    /** RETORNA OS DADOS DO PLANO */
    public function dataPlano(){
        return (new Plano())->findById($this->plano);
    }

    /** RETORNA OS DADOS DO PLANO */
    public function dataCheckout(){
        return (new Checkout())->findById($this->checkout);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  codig` varchar(20)
 *  usuario int(11)
 *  produto int(11)
 *  plano int(11)
 *  checkout int(11)
 *  created_at timestamp
 *  updated_at timestamp
 */
<?php

namespace Source\Models\Cookies;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;
// use Source\Models\Produtos\Plano;
use Source\Models\Checkout\Checkout;
// use Source\Models\Cookies\Cookies;

class cookiesTelevendas extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "cookies_televendas", [] );
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
    // public function dataPlano(){
    //     return (new Plano())->findById($this->plano);
    // }

    /** RETORNA OS DADOS DO PLANO */
    public function dataCheckout(){
        return (new Checkout())->findById($this->checkout);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  ip varchar(30)
 *  produto varchar(20)
 *  atendente int(11)
 *  comissao decimal(10,2)
  */
<?php

namespace Source\Models\Cupons;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Checkout\Checkout;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;

class Cupom extends DataLayer
{
    public function __construct(){
        
        parent::__construct( "cupons", [] );
    }

    public function dataCheckout(){
        return (new Checkout())->find("id = :uid", "uid={$this->checkout}")->fetch();
    }

    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->find("id = :uid", "uid={$this->produto}")->fetch();
    }

    public function dataPlano(){
        return (new Plano())->find("id = :uid", "uid={$this->plano}")->fetch();
    }
}

/** PROPRIEDADES
 * 
 *	id int(11)
 *  cupom varchar(20)
 *  porcentagem int(11)
 *  usuario int(11)
 *  produto int(11)
 *  created_at timestamp
 *  updated_at timestamp
 */
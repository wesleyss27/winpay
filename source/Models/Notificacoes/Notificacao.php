<?php

namespace Source\Models\Notificacoes;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Checkout\Checkout;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;

class Notificacao extends DataLayer
{
    public function __construct(){
        
        parent::__construct( "notificacao_checkout", [] );
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
 *  frase1 int(11)
 *  frase2 int(11)
 *  frase3 int(11)
 *  frase4 int(11)
 *  frase5 int(11)
 *  frase6 int(11)
 *  frase7 int(11)
 *  ativo int(11)
 */
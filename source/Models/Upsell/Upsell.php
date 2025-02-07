<?php

namespace Source\Models\Upsell;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Checkout\Checkout;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;

class Upsell extends DataLayer
{
    public function __construct(){
        
        parent::__construct( "upsell", [] );
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

    public function dataProdutoPlano($produto){
        return (new Produto())->find("id = :uid", "uid={$produto}")->fetch();
    }

    public function dataPlano(){
        return (new Plano())->find("id = :uid", "uid={$this->plano}")->fetch();
    }
}

/** PROPRIEDADES
 * 
 *	id int(11)
 *  codigo varchar(20)
 *  produto int(11)
 *  afiliado int(11)
 *  status int(11)
 *  ativo int(11)
 *  liberar_comprador
 *  tempo_cookie int(11)
 *  tipo_comissao int(11)
 *  comissao_afiliado decimal(10,2)
 *  tipo_atribuicao tinyint(1)
 *  created_at timestamp
 *  updated_at timestamp
 */
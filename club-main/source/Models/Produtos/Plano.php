<?php

namespace Source\Models\Produtos;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Plano extends DataLayer
{
    public function __construct(){
        parent::__construct( "planos", [] );
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->find("id = :uid", "uid={$this->produto}")->fetch();
    }

    /** RETORNA OS DADOS DO CLIENTE */
    public function dataUsuario(){
        return (new Usuario())->find("id = :uid", "uid={$this->usuario}")->fetch(true);
    }
}

/** PROPIEDADES
 * 
 *  id int(11)
 *  codigo varchar(20)
 *  nome varchar(255)
 *  produto int(11)
 *  usuario int(11)
 *  quantidade int(11)
 *  preco decimal(10,2)
 *  status int(11)
 *  ativo tinyint(4)
 *  privado tinyint(4)
 *  descricao text
 *  created_at timestamp
 *  updated_at timestamp
 */
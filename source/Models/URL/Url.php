<?php

namespace Source\Models\URL;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Url extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "urls", [] );
    }

    /** RETORNA OS DADOS DO PRODUTOR */
    public function dataProdutor(){
        return (new Usuario())->find("id = :uid", "uid={$this->usuario}")->fetch(true);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->find("id = :uid", "uid={$this->produto}")->fetch(true);
    }
}

/** PROPRIEDADES
 * 
 * id int(11)
 * `descricao varchar(200)
 * `url varchar(250)
 * `usuario int(11)
 * `produto int(11)
 * `privada tinyint(4)
 * `created_at timestamp
 * `updated_at timestamp
 */
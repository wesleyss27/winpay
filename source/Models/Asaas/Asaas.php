<?php

namespace Source\Models\Asaas;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Asaas extends DataLayer
{
    public function __construct(){
        parent::__construct( "asaas", [] );
    }

    public function dataAfiliado(){
        return (new Usuario())->find("id = :uid", "uid={$this->afiliado}")->fetch();
    }

    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }
}
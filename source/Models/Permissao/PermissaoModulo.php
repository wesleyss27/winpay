<?php

namespace Source\Models\Permissao;

use CoffeeCode\DataLayer\DataLayer;

class PermissaoModulo extends DataLayer
{
    public function __construct(){
        parent::__construct( "permissao_modulo", [] );
    }
}
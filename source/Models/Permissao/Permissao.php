<?php

namespace Source\Models\Permissao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Permissao extends DataLayer
{
    public function __construct(){
        parent::__construct( "permissao", [] );
    }

    public function dataUsuarioPermitido()
    {
        return (new Usuario())->findById($this->usuario_permitido);
    }

    public function dataUsuarioOrigem()
    {
        return (new Usuario())->findById($this->usuario_origem);
    }
}
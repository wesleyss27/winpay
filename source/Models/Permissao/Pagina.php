<?php

namespace Source\Models\Permissao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Pagina extends DataLayer
{
    public function __construct()
    {
        parent::__construct("paginas", []);
    }

    public function dataMenu()
    {
        if ($this->menu)
            return (new Pagina())->findById($this->menu);
    }
}

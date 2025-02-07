<?php

namespace Source\Models\Assinaturas;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Vendas\Venda;

class AssinaturaCadastro extends DataLayer
{
    public function __construct()
    {

        parent::__construct("assinaturas_cadastro", []);
    }

    public function dataVenda()
    {
        if ($this->venda)
            return (new Venda())->findById($this->venda);
    }
}

/** PROPRIEDADES
 * 
 *	id int(11)
 *  
 */

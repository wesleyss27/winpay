<?php

namespace Source\Models\Drop;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Cobranca extends DataLayer
{
    public function __construct()
    {

        parent::__construct("cobrancas_drop", []);
    }

    public function dataProdutor()
    {
        if ($this->produtor)
            return (new Usuario())->findById($this->produtor);
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

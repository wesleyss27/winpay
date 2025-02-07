<?php

namespace Source\Models\Cartao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Cartao extends DataLayer
{
    public function __construct(){
        
        parent::__construct( "cartao", [] );
    }

    public function dataAfiliado(){
        return (new Usuario())->find("id = :uid", "uid={$this->afiliado}")->fetch();
    }

    public function dataProduto(){
        return (new Produto())->findById($this->produto);
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
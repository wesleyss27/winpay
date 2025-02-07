<?php

namespace Source\Models\CoProdutor;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class CoProdutor extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "co_produtor", [] );
    }

    /**
     * id
     * produto
     * co_produtor
     * tipo_comissao
     * comissao
     * tipo_comissao_afiliados
     * comissao_afiliados
     * gerente
     * aceito
     * todos_afiliados
     * data_limite
     * created_at
     * updated_at
     */
    public function dataCoProdutor(){
        return (new Usuario())->findById($this->co_produtor);
    }

    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }
}
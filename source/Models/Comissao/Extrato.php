<?php

namespace Source\Models\Comissao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;

class Extrato extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "comissao", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }

    /** RETORNA A QUANTIDADE DE VENDAS */
    public function dataVenda(){
        return (new Venda())->findById($this->venda);
    }
}
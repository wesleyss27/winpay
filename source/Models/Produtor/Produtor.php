<?php

namespace Source\Models\Produtor;

use CoffeeCode\DataLayer\DataLayer;

class Produtor extends DataLayer
{
    public function __construct(){
        
        parent::__construct( "produtor", [] );
    }

    public function add(string $status, string $complemento, string $pais): Cliente
    {
        $this->status = $status;
        $this->complemento = $complemento;
        $this->pais = $pais;

        return $this;
    }

    public function addStatus(string $status): Cliente
    {
        $this->status = $status;

        return $this;
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Cliente())->find("id_cliente = :uid", "uid={$this->id_cliente}")->fetch(true);
    }
}

/** PROPRIEDADES
 * 
 * 
 */
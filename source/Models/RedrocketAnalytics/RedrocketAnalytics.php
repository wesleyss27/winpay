<?php

namespace Source\Models\RedrocketAnalytics;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\Usuario;

class RedrocketAnalytics extends DataLayer
{
    public function __construct()
    {

        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct("redrocket_analytics", []);
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataProdutor()
    {
        if ($this->produtor)
            return (new Usuario())->findById($this->produtor);
    }

    public function dataProduto()
    {
        if ($this->produto)
            return (new Produto())->findById($this->produto);
    }

    public function dataPlano()
    {
        if ($this->plano)
            return (new Produto())->findById($this->plano);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario int(11)
 *  token varchar(200)
 *  created_at timestamp
 *  updated_at timestamp
 */

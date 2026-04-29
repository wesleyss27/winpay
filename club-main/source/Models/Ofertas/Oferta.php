<?php

namespace Source\Models\Ofertas;

use CoffeeCode\DataLayer\DataLayer;

class Oferta extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ofertas", []);
    }
}

/** PROPRIEDADES
 *
 *  id           int(11)
 *  usuario      int(11)
 *  curso        int(11)
 *  checkout_id  int(11)
 *  nome         varchar(100)
 *  descricao    text
 *  imagem       varchar(300)
 *  valor        decimal(10,2)
 *  status       int(11)
 *  created_at   timestamp
 *  updated_at   timestamp
 */

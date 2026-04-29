<?php

namespace Source\Models\Videos;

use CoffeeCode\DataLayer\DataLayer;

class Video extends DataLayer
{
    public function __construct()
    {
        parent::__construct("videos", ["usuario", "titulo", "arquivo"]);
    }
}

/** PROPRIEDADES
 *
 *  id           int(11)
 *  usuario      int(11)
 *  titulo       varchar(200)
 *  arquivo      varchar(300)
 *  tamanho      bigint(20)
 *  duracao      int(11)       — segundos
 *  status       tinyint(1)    — 1=ativo, 0=inativo
 *  created_at   timestamp
 *  updated_at   timestamp
 */

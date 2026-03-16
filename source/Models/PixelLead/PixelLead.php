<?php

namespace Source\Models\PixelLead;

use CoffeeCode\DataLayer\DataLayer;

class PixelLead extends DataLayer
{
    public function __construct()
    {
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct('pixel_leads', ['ip']);
    }

    /**
     * Busca o registro mais recente associado ao IP informado,
     * criado nas últimas 2 horas.
     */
    public function findByIp(string $ip): ?self
    {
        return $this->find(
            "ip = :ip AND created_at >= DATE_SUB(NOW(), INTERVAL 2 HOUR)",
            "ip={$ip}"
        )->order("created_at DESC")->limit(1)->fetch();
    }
}

/** PROPRIEDADES
 *
 *  id         int(11)
 *  fbclid     varchar(255)
 *  fbc        varchar(255)
 *  ip         varchar(45)  NOT NULL
 *  ua         text
 *  created_at timestamp
 *  updated_at timestamp
 */

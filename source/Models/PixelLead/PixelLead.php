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
     * Usa PDO direto para evitar o rowCount() do DataLayer que não é
     * confiável em SELECT no MariaDB.
     */
    public function findByIp(string $ip): ?self
    {
        try {
            $pdo  = \CoffeeCode\DataLayer\Connect::getInstance();
            $stmt = $pdo->prepare(
                "SELECT * FROM pixel_leads WHERE ip = :ip ORDER BY created_at DESC LIMIT 1"
            );
            $stmt->execute([':ip' => $ip]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            foreach ($row as $key => $value) {
                $this->$key = $value;
            }
            return $this;
        } catch (\PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }
}

/** PROPRIEDADES
 *
 *  id           int(11)
 *  fbclid       varchar(255)
 *  fbc          varchar(255)
 *  ip           varchar(45)  NOT NULL
 *  ua           text
 *  utm_source   varchar(200)
 *  utm_campaign varchar(200)
 *  utm_medium   varchar(200)
 *  utm_content  varchar(200)
 *  utm_term     varchar(200)
 *  src          varchar(200)
 *  sck          varchar(200)
 *  round_id     varchar(100)
 *  round_number varchar(100)
 *  created_at   timestamp
 *  updated_at   timestamp
 */

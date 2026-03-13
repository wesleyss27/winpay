<?php

namespace Source\Models\Reembolsos;

use CoffeeCode\DataLayer\DataLayer;

class Reembolso extends DataLayer
{
    public function __construct()
    {
        // timestamps=false: o DataLayer não injeta created_at/updated_at.
        // A tabela usa criado_em/atualizado_em gerenciados pelo MySQL (DEFAULT/ON UPDATE).
        parent::__construct("reembolsos", [], "id", false);
    }
}

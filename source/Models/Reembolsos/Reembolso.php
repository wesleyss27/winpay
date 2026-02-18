<?php

namespace Source\Models\Reembolsos;

use CoffeeCode\DataLayer\DataLayer;

class Reembolso extends DataLayer
{
    public function __construct()
    {
        // Tabela, campos obrigatórios, chave primária, timestamps
        parent::__construct("reembolsos", [], "id", false);
    }
}

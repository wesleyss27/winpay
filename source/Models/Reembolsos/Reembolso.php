<?php

namespace Source\Models\Reembolsos;

use CoffeeCode\DataLayer\DataLayer;

class Reembolso extends DataLayer
{
    /**
     * Campos de timestamp customizados na tabela.
     */
    protected $created = "criado_em";
    protected $updated = "atualizado_em";

    public function __construct()
    {
        // Tabela, campos obrigatórios, chave primária, timestamps ligados
        parent::__construct("reembolsos", [], "id", true);
    }
}

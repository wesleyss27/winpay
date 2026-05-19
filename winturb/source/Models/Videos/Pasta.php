<?php

namespace Source\Models\Videos;

use CoffeeCode\DataLayer\DataLayer;

class Pasta extends DataLayer
{
    public function __construct()
    {
        parent::__construct("winturb_pastas", ["usuario", "nome"], "id", false);
    }

    public function byUsuario(int $userId): array
    {
        return $this->find("usuario = :u", "u={$userId}")
            ->order("nome ASC")
            ->fetch(true) ?: [];
    }
}

<?php

namespace Source\Models\Atendente;

use CoffeeCode\DataLayer\DataLayer;

class VendaPersonalizadaItem extends DataLayer
{
    public function __construct()
    {
        // timestamps=false: tabela só tem created_at, não updated_at
        parent::__construct("venda_personalizada_itens", ["venda", "produto"], "id", false);
    }

    public function porVenda(int $vendaId): array
    {
        $id = intval($vendaId);
        return $this->find("venda = {$id}")->fetch(true) ?? [];
    }
}

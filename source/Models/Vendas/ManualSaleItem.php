<?php

namespace Source\Models\Vendas;

use CoffeeCode\DataLayer\DataLayer;

class ManualSaleItem extends DataLayer
{
    public function __construct()
    {
        parent::__construct(
            "manual_sale_items",
            ["manual_sale_id", "produto_id", "plano_id", "quantidade", "preco_unitario", "subtotal"],
            "id",
            true
        );
    }
}

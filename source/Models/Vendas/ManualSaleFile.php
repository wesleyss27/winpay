<?php

namespace Source\Models\Vendas;

use CoffeeCode\DataLayer\DataLayer;

class ManualSaleFile extends DataLayer
{
    public function __construct()
    {
        parent::__construct(
            "manual_sale_files",
            ["manual_sale_id", "tipo", "caminho"],
            "id",
            true
        );
    }
}

<?php

namespace Source\Models\Vendas;

use CoffeeCode\DataLayer\DataLayer;

class ManualSale extends DataLayer
{
    public function __construct()
    {
        parent::__construct(
            "manual_sales",
            ["produtor_id", "comprador_id", "parcelas", "forma_pagamento", "valor_total"],
            "id",
            true
        );
    }
}

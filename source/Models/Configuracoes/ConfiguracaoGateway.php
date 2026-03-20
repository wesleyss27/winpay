<?php

namespace Source\Models\Configuracoes;

use CoffeeCode\DataLayer\DataLayer;

class ConfiguracaoGateway extends DataLayer
{
    public function __construct()
    {
        parent::__construct("configuracao_gateway", []);
    }
}

/** PROPRIEDADES
 *
 * id                        int(11)
 * modo                      enum('balanceado','dedicado')
 * gateway_principal         enum('pagarme','stripe')   -- usado no modo balanceado
 * gateway_dedicado          enum('pagarme','stripe')   -- usado no modo dedicado
 * usuario_ultima_alteracao  int(11)
 * created_at                timestamp
 * updated_at                timestamp
 */

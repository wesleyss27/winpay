<?php

namespace Source\Models\Configuracoes;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Taxa extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "taxas", [] );
    }

    /** RETORNA OS DADOS DO USUÁRIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario_ultima_alteracao);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  parcelamento decimal(6,4)
 *  saque decimal(5,2)
 *  taxa_redrocket decimal(5,2)
 *  valor_transacao decimal(5,2)
 *  taxa_iniciante decimal(5,2)
 *  vendas_iniciante int(11)
 *  dias_iniciante int(11)
 *  taxa_bronze decimal(5,2)
 *  vendas_bronze int(11)
 *  dias_bronze int(11)
 *  taxa_prata decimal(5,2)
 *  vendas_prata int(11)
 *  dias_prata int(11)
 *  taxa_ouro decimal(5,2)
 *  vendas_ouro int(11)
 *  dias_ouro int(11)
 *  usuario_ultima_alteracao int(11)
 *  created_at timestamp
 *  updated_at timestamp
 */
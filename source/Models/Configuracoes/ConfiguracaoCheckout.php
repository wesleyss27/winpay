<?php

namespace Source\Models\Configuracoes;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class ConfiguracaoCheckout extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "configuracao_checkout", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario_ultima_alteracao);
    }

}

/** PROPRIEDADES
 * 
 * id int(11)
 * chave_producao varchar(200)
 * token_producao varchar(200)
 * chave_sandbox varchar(200)
 * token_sandbox varchar(200)
 * sandbox tinyint(4)
 * usuario_ultima_alteracao int(11)
 * created_at timestamp
 * updated_at timestamp
 */
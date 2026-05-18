<?php

namespace Source\Models\Comissao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Banco extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "banco", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 *
 * id int(11)
 * usuario int(11)
 * titular varchar(200)
 * cpf varchar(20)
 * banco varchar(4)
 * agencia varchar(5)
 * digito_agencia varchar(2)
 * conta varchar(15)
 * digito_conta varchar(2)
 * tipo varchar(10)
 * chave_pix varchar(255)
 * status tinyint(4)
 * pagarme_conta varchar(50)
 * winpay_conta varchar(50)
 * stripe_account_id varchar(50)
 * stripe_onboarding_url varchar(500)
 * principal tinyint(1)
 * renda_mensal int(11)        -- centavos, PF, para PagarMe monthly_income
 * ocupacao varchar(100)       -- PF, para PagarMe professional_occupation
 * receita_anual bigint(20)    -- centavos, PJ, para PagarMe annual_revenue
 * observacao text
 * imagem_conta varchar(255)
 * created_at timestamp
 * updated_at timestamp
 */
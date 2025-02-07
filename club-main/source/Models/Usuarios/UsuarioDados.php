<?php

namespace Source\Models\Usuarios;

use CoffeeCode\DataLayer\DataLayer;

class UsuarioDados extends DataLayer
{
    public function __construct(){
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "usuario_dados", []);
    }

    public function verificaCpf($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if($data["cpf"] == "") exit;

        $usuario = (new UsuarioDados());
        $cpf = $this->find("cpf_cnpj = :cpf", "cpf={$data['cpf']}")->fetch();

        if(!$cpf){
            return false;
        } else {
            return $cpf->id;
        }
    }

    /** RETORNA OS DADOS DO PRODUTOR */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }
}

/** PROPRIEDADES
 * 
 * id int(11)
 * usuario int(11)
 * nome varchar(255)
 * email varchar(255)
 * tipo_doc tinyint(4)
 * cpf_cnpj varchar(20)
 * telefone varchar(20)
 * cep varchar(15)
 * rua varchar(50)
 * numero int(11)
 * complemento varchar(100)
 * bairro varchar(50)
 * cidade varchar(50)
 * estado varchar(50)
 * pais varchar(50)
 * cep_entrega varchar(15)
 * rua_entrega varchar(50)
 * numero_entrega int(11)
 * complemento_entrega varchar(100)
 * bairro_entrega varchar(50)
 * cidade_entrega varchar(50)
 * estado_entrega varchar(50)
 * pais_entrega int(50)
 * created_at timestamp
 * updated_at timestamp
 * customer_id varchar(50)
 */
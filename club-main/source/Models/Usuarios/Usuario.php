<?php

namespace Source\Models\Usuarios;

use CoffeeCode\DataLayer\DataLayer;

class Usuario extends DataLayer
{
    public function __construct(){
        parent::__construct( "usuario", [] );
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario_dados int(11)
 *  nome varchar(200)
 *  nome_abreviado varchar(200)
 *  email varchar(200)
 *  login varchar(50)
 *  senha varchar(100)
 *  tipo_usuario int(1)
 *  ativo int(1)
 *  status int(1)
 *  taxa_saque decimal(10,2)
 *  tempo_saque varchar(2)
 *  grupo tinyint(4)
 *  foto varchar(64)
 *  user_google varchar(100)
 *  facebook varchar(300)
 *  pid_facebook varchar(140)
 *  pid_google varchar(140)
 *  sexo varchar(2)
 *  celular varchar(15)
 *  observacao text
 *  tema varchar(30)
 *  created_at timestamp
 *  updated_at timestamp
 */
<?php

namespace Source\Models\Whatsapp;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class NotificacoesInteligentes extends DataLayer
{
    public function __construct(){
        parent::__construct( "notificacoes_inteligentes", []);
    }

    /** RETORNA OS DADOS DO COMPRADOR */
    public function dataUsuario(){
        return (new Usuario())->find("id = :uid", "uid={$this->usuario}")->fetch();
    }

}

/** PROPRIEDADES
 * 
 *  
 */
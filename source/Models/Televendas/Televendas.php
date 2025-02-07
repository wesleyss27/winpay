<?php

namespace Source\Models\Televendas;

use CoffeeCode\DataLayer\DataLayer;

class Televendas extends DataLayer
{
    public function __construct(){
        parent::__construct( "televendas", [] );
    }
}

// PROPRIEDADES
// id 	int(11)			
// produto	int(11)		
// usuario	int(11)			
// habilitar_televendas	tinyint(1)		
// descricao	varchar(50)	
// documentos	varchar(300)	
// created_at	timestamp
// updated_at	timestamp		
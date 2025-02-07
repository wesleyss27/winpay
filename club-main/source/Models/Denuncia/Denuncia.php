<?php

namespace Source\Models\Denuncia;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Denuncia extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "denuncias_produto", [] );
    }

    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    public function dataUsuarioAlteracao(){
        return (new Usuario())->findById($this->usuario_ultima_alteracao);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  produto int(11)
 *  usuario int(11)
 *  assunto tinyint(4)
 *  denuncia text
 *  status tinyint(4)
 *  usuario_ultima_alteracao int(11)
 *  created_at timestamp
 *  updated_at timestamp
 */

 /** ASSUNTOS
  * 
  *
  */
<?php

namespace Source\Models\Postback;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Postback extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "postback", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }

    public function dataEventos()
    {
        $eventos = explode(",", $this->eventos);

        $arr = array(
            1 => "Aguardando pagamento",
            2 => "Em Análise",
            3 => "Pré Autorizada",
            4 => "Paga",
            5 => "Cancelada",
            6 => "Devolvida",
            7 => "Chargeback",
            8 => "Concluída",
            9 => "Bloqueada",
            10 => "Carrinho Abandonado",
        );

        foreach ($eventos as $evento) {
            echo $arr[$evento] . "<br>";
        }
    }

    public function returnEventos()
    {
        $eventos = explode(",", $this->eventos);

        return $eventos;
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  codigo varchar(20)
 *  usuario int(11)
 *  nome varchar(200)
 *  observacao text
 *  boleto int(11)
 *  status int(11)
 *  executar_checkout int(11)
 *  valor decimal(10,2)
 *  plataforma varchar(30)
 *  id_pixel varchar(30)
 *  created_at timestamp
 *  updated_at timestamp
 */
<?php

namespace Source\Models\Configuracoes;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;
use Source\Models\Comissao\Banco;

class Antecipacao extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "antecipacao", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario_ultima_alteracao);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }

    /** RETORNA A QUANTIDADE DE VENDAS */
    public function dataVenda(){
        return (new Venda())->findById($this->venda);
    }

    /** RETORNA DADOS DA CONTA BANCÁRIA */
    public function dataConta(){
        return (new Banco())->findById($this->conta);
    }
}

/** PROPRIEDADES
 * 
 * id int(11)
 * dias_antecipar int(11)
 * periodo_antecipacao int(11)
 * porcentagem decimal(5,2)
 * taxa_antecipacao decimal(5,2)
 * usuario int(11)
 * usuario_ultima_alteracao int(11)
 * created_at timestamp
 * updated_at timestamp
 */
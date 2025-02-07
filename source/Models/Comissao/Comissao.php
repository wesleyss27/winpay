<?php

namespace Source\Models\Comissao;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;
use Source\Models\Comissao\Banco;

class Comissao extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "comissao", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
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
        if ($this->conta)
            return (new Banco())->findById($this->conta);
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  usuario int(11)
 *  venda nt(11)
 *  produto` int(11)
 *  descricao varchar(100)
 *  valor decimal(10,2)
 *  saldo decimal(10,2)
 *  taxa decimal(10,2)
 *  tempo_saque date
 *  disponivel tinyint(4)
 *  tipo int(11)
 *  conta int(11)
 *  status int(11)
 *  codigo varchar(30)
 *  observacao text
 *  data_pedido date
 *  usuario_ultima_alteracao
 *  created_at timestamp
 *  updated_at timestamp
 */
<?php

namespace Source\Models\Frete;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;

class Frete extends DataLayer
{
    public function __construct()
    {

        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct("frete", []);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto()
    {
        if ($this->produto) {
            return @(new Produto())->find("id = :uid", "uid={$this->produto}")->fetch();
        }
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario()
    {
        return (new Usuario())->findById($this->usuario);
    }

}
/** PROPRIEDADES
 * 
 *  id int(11)
 *  produtor int(11)
 *   produto int(11)
 *  checkout int(11)
 *  tipo_frete tinyint(4)
 *  frete_padrao decimal(5,2)
 *  frete_gratis tinyint(4)
 *  prazo_entrega tinyint(4)
 *  prazo_entrega_gratis tinyint(4)
 *  correios_entrega tinyint(4)
 *  correios_formato tinyint(4)
 *  cep_origem varchar(9)
 *  altura_mercadora decimal(6,2)
 *  largura_mercadora decimal(6,2)
 *  comprimento_mercadora decimal(6,2)
 *  peso_mercadora decimal(6,3)
 *  regiao_norte decimal(5,2)
 *  regiao_nordeste decimal(5,2)
 *  regiao_centro decimal(5,2)
 *  regiao_sudeste decimal(5,2)
 *  regiao_sul decimal(5,2)
 *  created_at timestamp
 *  updated_at timestamp
 */

<?php

namespace Source\Models\Assinaturas;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Vendas\Venda;

class Assinatura extends DataLayer
{
    public function __construct()
    {

        parent::__construct("assinaturas", []);
    }

    public function dataVenda()
    {
        if ($this->venda)
            return (new Venda())->findById($this->venda);
    }

    public function dataPlano()
    {
        if ($this->plano)
            return (new Plano())->findById($this->plano);
    }

    public function dataProduto()
    {
        return (new Produto())->findById($this->produto);
    }

    public function dataProdutor()
    {
        return (new Usuario())->findById($this->produtor);
    }

    public function dataComprador()
    {
        return (new UsuarioDados())->findById($this->comprador);
    }

    public function dataEndereco()
    {
        return (new Usuario())->findById($this->endereco);
    }

    public function dataAfiliado()
    {
        return (new Usuario())->findById($this->afiliado);
    }
}

/** PROPRIEDADES
 * 
 *	id int(11)
 *  
 */

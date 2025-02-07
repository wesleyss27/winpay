<?php

namespace Source\Models\CarrinhoAbandonado;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Campanhas\Campanha;

class CarrinhoAbandonado extends DataLayer
{
    public function __construct()
    {
        parent::__construct("carrinho_abandonado", []);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto()
    {
        return (new Produto())->find("id = :uid", "uid={$this->produto}")->fetch();
    }

    /** RETORNA OS DADOS DO PLANO */
    public function dataPlano()
    {
        return (new Plano())->find("id = :uid", "uid={$this->plano}")->fetch();
    }

    /** RETORNA OS DADOS DO PRODUTOR */
    public function dataProdutor()
    {
        return (new Usuario())->findById($this->produtor);
    }

    /** RETORNA OS DADOS DO COMPRADOR */
    public function dataComprador()
    {
        return (new UsuarioDados())->find("id = :uid", "uid={$this->comprador}")->fetch();
    }

    /** RETORNA OS DADOS DO AFILIADO */
    public function dataAfiliado()
    {
        return (new Usuario())->find("id = :uid", "uid={$this->afiliado}")->fetch();
    }

    /** RETORNA OS DADOS DO AFILIADO */
    public function dataCampanha()
    {
        return (new Campanha())->find("id = :uid", "uid={$this->campanha}")->fetch();
    }

    /** RETORNA OS DADOS DO PRODUTOR (USADO PARA ENVIAR DADOS PARA O TELEVENDAS) */
    public function dataProdutorTelevendas()
    {
        return (new UsuarioDados())->find("usuario = :uid", "uid={$this->produtor}")->fetch();
    }

}

/** PROPRIEDADES
 * 
 * id int(11)
 * codigo varchar(20)
 * nome varchar(200)
 * email varchar(100)
 * telefone varchar(20)
 * cpf_cnpj varchar(20)
 * cep varchar(9)
 * rua varchar(100)
 * numero varchar(5)
 * complemento varchar(200)
 * bairro varchar(200)
 * cidade varchar(200)
 * estado varchar(2)
 * produto int(11)
 * plano int(11)
 * produtor int(11)
 * afiliado int(11)
 * campanha int(11)
 * url_checkout varchar(200)
 * created_at timestamp
 * updated_at timestamp
 * status tinyint(4)
 */

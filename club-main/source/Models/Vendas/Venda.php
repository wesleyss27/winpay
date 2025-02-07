<?php

namespace Source\Models\Vendas;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Campanhas\Campanha;
use Source\Models\Enderecos\Endereco;

class Venda extends DataLayer
{
    public function __construct(){
        parent::__construct( "vendas", []);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->find("id = :uid", "uid={$this->produto}")->fetch();
    }

    /** RETORNA OS DADOS DO PLANO */
    public function dataPlano(){
        return (new Plano())->find("id = :uid", "uid={$this->plano}")->fetch();
    }

    /** RETORNA OS DADOS DO PRODUTOR */
    public function dataProdutor(){
        return (new Usuario())->findById($this->produtor);
    }

    /** RETORNA OS DADOS DO COMPRADOR */
    public function dataComprador(){
        return (new UsuarioDados())->find("id = :uid", "uid={$this->comprador}")->fetch();
    }

    /** RETORNA OS DADOS DO COMPRADOR */
    public function dataEndereco(){
        return (new Endereco())->find("id = :uid", "uid={$this->endereco}")->fetch();
    }

    /** RETORNA OS DADOS DO AFILIADO */
    public function dataAfiliado(){
        return (new Usuario())->find("id = :uid", "uid={$this->afiliado}")->fetch();
    }

    /** RETORNA OS DADOS DO AFILIADO */
    public function dataCampanha(){
        return (new Campanha())->find("id = :uid", "uid={$this->campanha}")->fetch();
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  venda int(11)
 *  transacao varchar(30)
 *  produto int(255)
 *  plano int(11)
 *  comprador int(11)
 *  produtor int(11)
 *  campanha int(11)
 *  afiliado int(11)
 *  comissao_afiliado decimal(10,2)
 *  meio_pagamento varchar(20)
 *  forma_pagamento int(11)
 *  status_transacao int(11)
 *  url_checkout varchar(300)
 *  link_boleto varchar(100)
 *  valor_bruto decimal(10,2)
 *  valor_produto decimal(10,2)
 *  valor_juros decimal(10,2)
 *  valor_frete decimal(10,2)
 *  taxa_juros decimal(10,4)
 *  valor_taxa decimal(10,2)
 *  taxa_octapay decimal(10,2)
 *  valor_octapay decimal(10,2)
 *  valor_desconto decimal(10,2)
 *  valor_liquido decimal(10,2)
 *  parcelas int(11)
 *  data_finalizada datetime
 *  data_ultimo_evento datetime
 *  created_at timestamp
 *  updated_at timestamp
 *  src varchar(200) DEFAULT
 *  utm_source varchar(200)
 *  utm_campaign varchar(200)
 *  utm_medium varchar(200)
 *  utm_content varchar(200)
 *  st4rt int(11)
 *  postback_st4rt int(11)
 *  carrinho_abandonado int(11)
 */
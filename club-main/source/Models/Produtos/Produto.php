<?php

namespace Source\Models\Produtos;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;

class Produto extends DataLayer
{
    public function __construct(){
        parent::__construct( "produto", []);

        /**
         * PROPRIEDADES DA TABELA PRODUTO
         * 
         *   id` int(11) NOT NULL,
         *   codigo` varchar(20) DEFAULT NULL,
         *   rascunho` int(11) DEFAULT 0,
         *   nome` varchar(50) DEFAULT NULL,
         *   usuario` int(11) DEFAULT NULL,
         *   descricao` text DEFAULT NULL,
         *   categoria` int(11) DEFAULT NULL,
         *   subcategoria` int(11) DEFAULT NULL,
         *   tipo_produto` int(11) DEFAULT NULL,
         *   tipo_cobranca` int(11) DEFAULT NULL,
         *   tipo_precificacao` int(11) DEFAULT NULL,
         *   rockets` decimal(2,1) DEFAULT 0.0,
         *   preco` decimal(10,2) DEFAULT NULL,
         *   preco_alteravel` decimal(10,2) DEFAULT NULL,
         *   termos` text DEFAULT NULL,
         *   aceite_termos` int(11) DEFAULT NULL,
         *   email_suporte` varchar(200) DEFAULT NULL,
         *   status` int(11) DEFAULT 1,
         *   frete_fixo` decimal(10,2) DEFAULT NULL,
         *   pagina_venda` varchar(200) DEFAULT NULL,
         *   url_midias` varchar(300) DEFAULT NULL,
         *   foto` varchar(300) DEFAULT NULL,
         *   taxa_juros` decimal(10,2) DEFAULT NULL,
         *   parcelas` int(11) DEFAULT NULL,
         *   obrigado_boleto` varchar(300) DEFAULT NULL,
         *   obrigado_cartao` varchar(300) DEFAULT NULL,
         *   obrigado_analise` varchar(300) DEFAULT NULL,
         *   garantia` int(11) DEFAULT NULL,
         *   disponivel` int(11) DEFAULT NULL,
         *   quantidade_max` int(11) DEFAULT NULL,
         *   documentos` varchar(300) DEFAULT NULL,
         *   observacao` text DEFAULT NULL,
         *   abrir_afiliacao` int(11) DEFAULT NULL,
         *   afiliados_aprovacao` int(11) DEFAULT NULL,
         *   visualizacao` int(11) DEFAULT NULL,
         *   libera_comprador` int(11) DEFAULT NULL,
         *   tempo_cookie` int(11) DEFAULT NULL,
         *   tipo_comissao` int(11) DEFAULT NULL,
         *   comissao_afiliado` decimal(10,2) DEFAULT NULL,
         *   tipo_atribuicao` int(11) DEFAULT NULL,
         *   created_at` timestamp NULL DEFAULT NULL,
         *   updated_at` timestamp NULL DEFAULT NULL,
         *   st4rt` int(11) DEFAULT 0
         */

         /**
          * STATUS DO PRODUTO
          * 1 - PENDENTE
          * 2 - APROVADO
          * 3 - REPROVADO
          * 4 - BLOQUEADO
          */
    }

    /** RETORNA OS DADOS DO PRODUTOR */
    public function dataProdutor(){
        return (new Usuario())->find("id = :uid", "uid={$this->usuario}")->fetch();
    }
}
<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\Geral;

use DateTime;
use League\Plates\Engine;
use Source\App\Asaas\AppAsaas;
use Source\App\Checkout1\PagarMe;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\App\Checkout1\WinPay;
use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Saldo;
use Source\Models\Permissao\Permissao;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;
use Source\Models\Sessao\Sessao;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;
use Source\Models\Checkout\Checkout;
use Source\Helpers\Emails;

session_start();

class AppWeb
{
    private $view;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->sessao = (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
    }

    public function painel($data)
    {
        $section = new Section();

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'inicio'")->fetch();

        $user = $this->section->getTipo() == 1 ? "" : " AND usuario = " . $this->section->getId();
        $usuario = $this->section->getId();

        $asaasClientes = (new AppAsaas(null))->verificarCliente($_SESSION["id"]);
        $usuarioPagarme = (new Usuario)->findById($this->section->getId());

        $saldoPagarme = (new PagarMe)->saldo($usuarioPagarme->pagarme_conta);

        $mesAtual = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
        $mesAnterior = "'" . date("Y-m-01", strtotime("-1 month")) . "' AND '" . date("Y-m-31", strtotime("-1 month")) . "'";

        $comissoes = new Comissao();
        $comissaoAtual = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido BETWEEN $mesAtual GROUP BY tempo_saque", null, "DISTINCT data_pedido as dia, sum(valor) as valor")->fetch(true);
        $comissaoAnterior = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido BETWEEN $mesAnterior GROUP BY tempo_saque", null, "DISTINCT data_pedido as dia, sum(valor) as valor")->fetch(true);

        $vendasAtual = (new Venda())->find("(afiliado = $usuario OR produtor = $usuario ) AND status_transacao = 4 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasCoprodutor = (new Venda())->find("coprodutores Like '%/$usuario/%' AND status_transacao = 4 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasAtualSoma = (new Venda())->find("(produtor = $usuario) AND status_transacao = 4 AND created_at BETWEEN $mesAtual", null, "SUM(comissao_produtor) as soma")->fetch();

        $vendasTotal = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasTotalSoma = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor_bruto) as soma")->fetch();

        $hoje = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d") . "'", null, "SUM(valor) as valor")->fetch();
        $ontem = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d", strtotime("-1 day")) . "'", null, "SUM(valor) as valor")->fetch();
        $mes = $comissoes->find("usuario = $usuario AND tipo = 1 AND data_pedido BETWEEN $mesAtual", null, "SUM(valor)")->fetch();

        $mesAnteriorInicio = date("Y-m-01", strtotime("-1 month"));
        $mesAnteriorFim = date("Y-m-t", strtotime("-1 month"));

        $vendasAnteriorSomaProdAfi = (new Venda())->find(
            "(produtor = :usuario OR afiliado = :usuario)
            AND status_transacao = 4
            AND created_at BETWEEN :inicio AND :fim",
            "usuario={$usuario}&inicio={$mesAnteriorInicio}&fim={$mesAnteriorFim}",
            "DAY(created_at) AS dia, 
            SUM(CASE 
                    WHEN produtor = {$usuario} THEN comissao_produtor
                    WHEN afiliado = {$usuario} THEN comissao_afiliado
                    ELSE 0
                END) AS soma"
        )->group("dia")->order("dia ASC")->fetch(true);

        $mesAtualInicio = date("Y-m-01");
        $mesAtualFim = date("Y-m-d H:i:s");

        $vendasAtualSomaProdAfi = (new Venda())->find(
            "(produtor = :usuario OR afiliado = :usuario)
            AND status_transacao = 4
            AND created_at BETWEEN :inicio AND :fim",
            "usuario={$usuario}&inicio={$mesAtualInicio}&fim={$mesAtualFim}",
            "DAY(created_at) AS dia, 
            SUM(CASE 
                    WHEN produtor = {$usuario} THEN comissao_produtor
                    WHEN afiliado = {$usuario} THEN comissao_afiliado
                    ELSE 0
                END) AS soma"
        )->group("dia")->order("dia ASC")->fetch(true);


    
        foreach ($mes->data() as $mes) null;

        echo $this->view->render("home_users", [
            "title" => "WinPay",
            "section" => @$section,
            "permissao" => @$permissao,
            "vendasAnteriorSomaProdAfi" => @$vendasAnteriorSomaProdAfi,
            "vendasAtualSomaProdAfi" => @$vendasAtualSomaProdAfi,
            "comissaoAtual" => $comissaoAtual,
            "comissaoAnterior" => $comissaoAnterior,
            "vendasAtual" => $vendasAtual,
            "vendasCoprodutor" => $vendasCoprodutor,
            "vendasAtualSoma" => $vendasAtualSoma,
            "vendasTotal" => $vendasTotal,
            "vendasTotalSoma" => $vendasTotalSoma,
            "asaasClientes" => $asaasClientes,
            "usuarioPagarme" => $usuarioPagarme,
            "saldoPagarme" => $saldoPagarme,
            "hoje" => @$hoje->valor,
            "ontem" => @$ontem->valor,
            "mes" => @$mes,
        ]);
    }

    public function painelDados($data)
    {
        // $section = (new Section())->permissaoModulo("inicio");
        // $section = new Section();

        // if (@$_SESSION["subusuario"])
        //     $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'inicio'")->fetch();
        // var_dump($permissao);

        $comissoes = new Comissao();

        $user = $this->section->getTipo() == 1 ? "" : " AND usuario = " . $this->section->getId();
        $usuario = $this->section->getId();
        $usuario_tipo = $this->section->getTipo();

        $datas = array(
            'hoje' => date('Y-m-d') . ' 00:00:00',
            'ontem' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'semana' => date('Y-m-d H:i:s', strtotime('-7 days')),
            'mes' => date('Y-m-d H:i:s', strtotime('-30 days')),
        );

        $filtros = "";

        if ( ( @$_GET['busca'] && @$_GET['busca'] != 'personalizado' ) && @$_GET['busca'] !== "ontem"){
            $filtros        = "created_at BETWEEN '" . $datas[$_GET["busca"]] . "' AND '" . date('Y-m-d H:i:s') . "' AND ";
            $filtros_master = "created_at BETWEEN '" . $datas[$_GET["busca"]] . "' AND '" . date('Y-m-d H:i:s'). "'";
        } elseif(  ( @$_GET['busca'] && @$_GET['busca'] != 'personalizado' ) && @$_GET['busca'] == "ontem" ) {
            $yesterday = new DateTime('yesterday');
            $start = new DateTime($yesterday->format('Y-m-d') . ' 00:00:00');
            $end = new DateTime($yesterday->format('Y-m-d') . ' 23:59:59');
            $filtros        = "created_at BETWEEN '" . $start->format('Y-m-d H:i:s') . "' AND '" . $end->format('Y-m-d H:i:s') . "' AND ";
            $filtros_master = "created_at BETWEEN '" . $start->format('Y-m-d H:i:s') . "' AND '" . $end->format('Y-m-d H:i:s'). "'";
        }elseif(@$_GET['busca'] == 'personalizado') {
            $inicio = $_GET['inicio'] . ' 00:00:00';
            $fim = $_GET['fim'] . ' 23:59:59';

            $filtros = "created_at BETWEEN '{$inicio}' AND '{$fim}' AND ";
            $filtros_master = "created_at BETWEEN '{$inicio}' AND '{$fim}'";

        }

        $filtro_produtor =  $usuario_tipo != 1 ? $filtros . "produtor = $usuario" :  $filtros_master;
        $filtro_afiliado =  $usuario_tipo != 1 ? $filtros . "afiliado = $usuario" :  $filtros_master;
        

        // TOTAL VENDAS
        $vendaProd = (new Venda)
            ->find(
                $filtro_produtor,
                null,
                "SUM(comissao_produtor) as soma_produtor, COUNT(*) as total",
            )
            ->fetch();

        $vendaAf = (new Venda)
            ->find(
                $filtro_afiliado,
                null,
                "SUM(comissao_afiliado) as soma_afiliado, COUNT(*) as total",
            )
            ->fetch();
        // var_dump($vendaProd);
        $valorTotal = $vendaProd->soma_produtor + $vendaAf->soma_afiliado;

        
        $totalVendas = $vendaProd->total + $vendaAf->total;

        if ($totalVendas)
            $ticketTotal = $valorTotal / $totalVendas;
        else
            $ticketTotal = 0;

        // TOTAL PAGO
        $pagoProd = (new Venda)
            ->find(
                $filtro_produtor . " AND status_transacao = 4",
                null,
                "SUM(comissao_produtor) as soma_produtor, COUNT(*) as total",
            )
            ->fetch();
        $pagoAf = (new Venda)
            ->find(
                $filtro_afiliado . " AND status_transacao = 4",
                null,
                "SUM(comissao_afiliado) as soma_afiliado, COUNT(*) as total",
            )
            ->fetch();
        // var_dump($vendaProd);
        $valorPago = $pagoProd->soma_produtor + $pagoAf->soma_afiliado;
        $pagoVendas = $pagoProd->total + $pagoAf->total;

        if ($pagoVendas)
            $ticketPago = $valorPago / $pagoVendas;
        else
            $ticketPago = 0;

        // TOTAL CANCELADO
        $canceladoProd = (new Venda)
            ->find(
                $filtro_produtor . " AND status_transacao = 5",
                null,
                "SUM(comissao_produtor) as soma_produtor, COUNT(*) as total",
            )
            ->fetch();
        $canceladoAf = (new Venda)
            ->find(
                $filtro_afiliado . " AND status_transacao = 5",
                null,
                "SUM(comissao_afiliado) as soma_afiliado, COUNT(*) as total"
            )
            ->fetch();
        // var_dump($vendaProd);
        $valorCancelado = $canceladoProd->soma_produtor + $canceladoAf->soma_afiliado;
        $canceladoVendas = $canceladoProd->total + $canceladoAf->total;

        if ($canceladoVendas)
            $ticketCancelado = $valorCancelado / $canceladoVendas;
        else
            $ticketCancelado = 0;

        // TOTAL PENDENTE
        $pendenteProd = (new Venda)
            ->find(
                $filtro_produtor . " AND status_transacao = 1",
                null,
                "SUM(comissao_produtor) as soma_produtor, COUNT(*) as total",
            )
            ->fetch();
        $pendenteAf = (new Venda)
            ->find(
                $filtro_afiliado . "AND status_transacao = 1",
                null,
                "SUM(comissao_afiliado) as soma_afiliado, COUNT(*) as total"
            )
            ->fetch();
        // var_dump($vendaProd);
        $valorPendente = $pendenteProd->soma_produtor + $pendenteAf->soma_afiliado;
        $pendenteVendas = $pendenteProd->total + $pendenteAf->total;

        if ($pendenteVendas)
            $ticketPendente = $valorPendente / $pendenteVendas;
        else
            $ticketPendente = 0;

        // CARRINHO ABANDONADO
        $abandonadoProd = (new CarrinhoAbandonado)
            ->find(
                $filtro_produtor,
                null,
                "COUNT(*) as total"
            )
            ->fetch();
        $abandonadoAf = (new Venda)
            ->find(
                $filtro_afiliado,
                null,
                "COUNT(*) as total"
            )
            ->fetch();
        // var_dump($vendaProd);
        // $valorPendente = $pendenteProd->soma_produtor + $pendenteAf->soma_afiliado;
        $abandonadoVendas = $abandonadoProd->total + $abandonadoAf->total;

        // if ($abandonadoVendas)
        //     $ticketPendente = $valorPendente / $pendenteVendas;
        // else
        //     $ticketPendente = 0;

        // TOTAL ESTORNOS
        $estornoProd = (new Venda)
            ->find(
                $filtro_produtor . "AND status_transacao = 6",
                null,
                "SUM(comissao_produtor) as soma_produtor, COUNT(*) as total"
            )
            ->fetch();
        $estornoAf = (new Venda)
            ->find(
                $filtro_afiliado . " AND status_transacao = 6",
                null,
                "SUM(comissao_afiliado) as soma_afiliado, COUNT(*) as total"
            )
            ->fetch();
        // var_dump($vendaProd);
        $valorEstorno = $estornoProd->soma_produtor + $estornoAf->soma_afiliado;
        $estornoVendas = $estornoProd->total + $estornoAf->total;

        if ($estornoVendas)
            $ticketEstorno = $valorEstorno / $estornoVendas;
        else
            $ticketEstorno = 0;

            // CHARGEBACKS
            $chargebackProd = (new Venda)
            ->find(
                $filtro_produtor . " AND status_transacao = 7",
                null,
                "SUM(comissao_produtor) as soma_produtor, COUNT(*) as total"
            )
            ->fetch();
        $chargebackAf = (new Venda)
            ->find(
                $filtro_afiliado . " AND status_transacao = 7",
                null,
                "SUM(comissao_afiliado) as soma_afiliado, COUNT(*) as total"
            )
            ->fetch();
        // var_dump($vendaProd);
        $valorChargeback = $chargebackProd->soma_produtor + $chargebackAf->soma_afiliado;
        $chargebackVendas = $chargebackProd->total + $chargebackAf->total;

        if ($chargebackVendas)
            $ticketChargeback = $valorChargeback / $chargebackVendas;
        else
            $ticketChargeback = 0;

        $vendas = (new Venda);
        $dados_filtro_prod_afiliado =  $usuario_tipo != 1 ? "(produtor = $usuario OR afiliado = $usuario) " :  "";
        $dados_filtro_prod =  $usuario_tipo != 1 ? "produtor = $usuario AND " :  " AND ";
        $dados_filtro_afiliado =  $usuario_tipo != 1 ? "afiliado = $usuario AND " :  " AND ";

        $filtros = $usuario_tipo != 1 ? $filtros : $filtros_master;
        $dados["totalVendas"] = $vendas->find( $filtros . $dados_filtro_prod_afiliado)->count();

        $valorVendas1 = $vendas->find($filtro_produtor, null, "SUM(comissao_produtor) as valor")->fetch();
        $valorVendas2 = $vendas->find($filtro_afiliado, null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorVendas"] = @$valorVendas1->valor + @$valorVendas2->valor;


        ####
        $whereCartao = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 1"]);
        $dados["cartoes"] = $vendas->find($whereCartao)->count();

        $valorCartao1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 1", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorCartao2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 1", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorCartao"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $whereCartoesPagos = $this->montarWhere([
            $filtros,
            $dados_filtro_prod_afiliado,
            "forma_pagamento = 1",
            "(status_transacao = 4 OR status_transacao = 8)"
        ]);
        $dados["cartoesPagos"] = $vendas->find($whereCartoesPagos)->count();
        
        $valorCartao1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorCartao2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorCartaoPago"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $whereCartoesCancelados = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 1", "status_transacao = 5"]);
        $dados["cartoesCancelados"] = $vendas->find($whereCartoesCancelados)->count();
        $valorCartao1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorCartao2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorCartaoCancelado"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $whereCartoesOutros = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 1", "(status_transacao = 1 OR status_transacao = 2 OR status_transacao = 3 OR status_transacao = 9)"]);
        $dados["cartoesOutros"] = $vendas->find($whereCartoesOutros)->count();
        $whereCartoesReembolsos = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 1", "(status_transacao = 6 OR status_transacao = 7)"]);
        $dados["cartoesReembolsos"] = $vendas->find($whereCartoesReembolsos)->count();

        ####
        $whereBoletos = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 2"]);
        $dados["boletos"] = $vendas->find($whereBoletos)->count();

        $valorCartao1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 2", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorCartao2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 2", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorBoleto"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $whereBoletosPagos = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 2", "(status_transacao = 4 OR status_transacao = 8)"]);
        $dados["boletosPagos"] = $vendas->find($whereBoletosPagos)->count();
        $valorCartao1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorCartao2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorBoletoPago"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $whereBoletosCancelados = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 2", "status_transacao = 5"]);
        $dados["boletosCancelados"] = $vendas->find($whereBoletosCancelados)->count();
        $valorCartao1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorCartao2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorBoletoCancelado"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $whereBoletosOutros = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 2", "(status_transacao = 1 OR status_transacao = 2 OR status_transacao = 3 OR status_transacao = 9)"]);
        $dados["boletosOutros"] = $vendas->find($whereBoletosOutros)->count();

        $whereBoletosReembolsos = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 2", "(status_transacao = 6 OR status_transacao = 7)"]);
        $dados["boletosReembolsos"] = $vendas->find($whereBoletosReembolsos)->count();

        ####
        $wherePix = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 3"]);
        $dados["pix"] = $vendas->find($wherePix)->count();
        $valorPix1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 3", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorPix2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 3", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorPix"] = @$valorPix1->valor + @$valorPix2->valor;

        $wherePixPagos = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 3", "(status_transacao = 4 OR status_transacao = 8)"]);
        $dados["pixPagos"] = $vendas->find($wherePixPagos)->count();
        $valorPix1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorPix2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorPixPago"] = @$valorPix1->valor + @$valorPix2->valor;

        $wherePixCancelados = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 3", "status_transacao = 5"]);
        $dados["pixCancelados"] = $vendas->find($wherePixCancelados)->count();
        $valorPix1 = $vendas->find($filtros . $dados_filtro_prod . "forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(comissao_produtor) as valor")->fetch();
        $valorPix2 = $vendas->find($filtros . $dados_filtro_afiliado . "forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
        $dados["valorPixCancelado"] = @$valorPix1->valor + @$valorPix2->valor;

        $wherePixOutros = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 3", "(status_transacao = 1 OR status_transacao = 2 OR status_transacao = 3 OR status_transacao = 9)"]);
        $dados["pixOutros"] = $vendas->find($wherePixOutros)->count();

        $wherePixReembolsos = $this->montarWhere([$filtros, $dados_filtro_prod_afiliado, "forma_pagamento = 3", "(status_transacao = 6 OR status_transacao = 7)"]);
        $dados["pixReembolsos"] = $vendas->find($wherePixReembolsos)->count();


        // *********************************
        $mesAtual = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
        $mesAnterior = "'" . date("Y-m-01", strtotime("-1 month")) . "' AND '" . date("Y-m-31", strtotime("-1 month")) . "'";

        $hoje = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d") . "'", null, "SUM(valor) as valor")->fetch();
        $ontem = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d", strtotime("-1 day")) . "'", null, "SUM(valor) as valor")->fetch();
        $mes = $comissoes->find("usuario = $usuario AND tipo = 1 AND data_pedido BETWEEN $mesAtual", null, "SUM(valor)")->fetch();

        $hojeDrop = (new Venda())->find("adquirente != 'redrocket' AND status_transacao = 4 AND produtor = $usuario AND data_pedido = '" . date("Y-m-d") . "'", null, "SUM(valor_bruto) as valor")->fetch();
        $ontemDrop = (new Venda())->find("adquirente != 'redrocket' AND status_transacao = 4 AND produtor = $usuario AND data_pedido = '" . date("Y-m-d", strtotime("-1 day")) . "'", null, "SUM(valor_bruto) as valor")->fetch();
        $mesDrop = (new Venda())->find("adquirente != 'redrocket' AND status_transacao = 4 AND produtor = $usuario AND data_pedido BETWEEN $mesAtual", null, "SUM(valor_bruto) as valor")->fetch();

        $pendente = $comissoes->find("(antecipado = 1 OR antecipado = 0) and disponivel = 0 and tipo = 1 and usuario = $usuario", null, "SUM(valor)")->fetch();
        $comissao = $comissoes->find("AND tipo = 1 and usuario = " . $this->section->getId())->limit(7)->fetch(true);
        $antecipado = (new Comissao())->find("usuario = $usuario AND disponivel = 0", null, "SUM(antecipado)")->fetch();
        $bloqueado = (new Comissao())->find("descricao like '%bloqueio%' AND usuario = $usuario", null, "SUM(valor) as valor")->fetch();
        $saqueMes = (new Comissao())->find("tipo = 2 and status = 2 AND usuario = $usuario AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        $saldo = (new Saldo())->find( "usuario = " . $this->section->getId())->fetch();
        $indicacao = (new UsuarioPremium())->find("usuario = $usuario")->count();

        $min30 = date("Y-m-d H:i:s", strtotime("-30 minutes"));
        $usuariosOnline = (new Sessao())->find("created_at BETWEEN '$min30' AND '" . date("Y-m-d H:i:s") . "'", null, "DISTINCT(usuario)")->count();

        $vendasAtual = (new Venda())->find("(afiliado = $usuario OR produtor = $usuario ) AND status_transacao = 4 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasCoprodutor = (new Venda())->find("coprodutores Like '%/$usuario/%' AND status_transacao = 4 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasAtualSoma = (new Venda())->find("(produtor = $usuario) AND status_transacao = 4 AND created_at BETWEEN $mesAtual", null, "SUM(comissao_produtor) as soma")->fetch();

        $vendasTotal = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasTotalSoma = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor_bruto) as soma")->fetch();

        $comissaoAtual = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido BETWEEN $mesAtual GROUP BY tempo_saque", null, "DISTINCT data_pedido as dia, sum(valor) as valor")->fetch(true);
        $comissaoAnterior = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido BETWEEN $mesAnterior GROUP BY tempo_saque", null, "DISTINCT data_pedido as dia, sum(valor) as valor")->fetch(true);

        $asaasClientes = (new AppAsaas(null))->verificarCliente($_SESSION["id"]);
        $usuarioPagarme = (new Usuario)->findById($this->section->getId());

        $saldoPagarme = (new PagarMe)->saldo($usuarioPagarme->pagarme_conta);
        // $saldoPagarme = (new Winpay)->saldo($usuarioPagarme->pagarme_conta);

        foreach ($mes->data() as $mes) null;
        foreach ($pendente->data() as $pendente) null;
        foreach ($antecipado->data() as $antecipado) null;

        echo $this->view->render("home_users_dados", [
            "title" => "RedRocket",
            // "section" => @$section,
            // "permissao" => @$permissao,
            "valorTotal" => @$valorTotal,
            "totalVendas" => @$totalVendas,
            "ticketTotal" => @$ticketTotal,

            "valorPago" => @$valorPago,
            "pagoVendas" => @$pagoVendas,
            "ticketPago" => @$ticketPago,

            "valorCancelado" => @$valorCancelado,
            "canceladoVendas" => @$canceladoVendas,
            "ticketCancelado" => @$ticketCancelado,

            "valorPendente" => @$valorPendente,
            "pendenteVendas" => @$pendenteVendas,
            "ticketPendente" => @$ticketPendente,

            "abandonadoVendas" => @$abandonadoVendas,

            "valorEstorno" => @$valorEstorno,
            "estornoVendas" => @$estornoVendas,
            "ticketEstorno" => @$ticketEstorno,

            "valorChargeback" => @$valorChargeback,
            "chargebackVendas" => @$chargebackVendas,
            "ticketChargeback" => @$ticketChargeback,

            "dados" => $dados,

            "hoje" => @$hoje->valor,
            "ontem" => @$ontem->valor,
            "mes" => @$mes,
            "hojeDrop" => @$hojeDrop->valor,
            "ontemDrop" => @$ontemDrop->valor,
            "mesDrop" => @$mesDrop->valor,
            "pendente" => @$pendente,
            "antecipado" => @$antecipado,
            "comissao" => @$comissao,
            "saldo" => @$saldo,
            "bloqueado" => @$bloqueado->valor,
            "saqueMes" => @$saqueMes->valor,
            "indicacao" => $indicacao,
            "usuariosOnline" => $usuariosOnline,
            "comissaoAtual" => $comissaoAtual,
            "comissaoAnterior" => $comissaoAnterior,
            "vendasAtual" => $vendasAtual,
            "vendasCoprodutor" => $vendasCoprodutor,
            "vendasAtualSoma" => $vendasAtualSoma,
            "vendasTotal" => $vendasTotal,
            "vendasTotalSoma" => $vendasTotalSoma,
            "asaasClientes" => $asaasClientes,
            "usuarioPagarme" => $usuarioPagarme,
            "saldoPagarme" => $saldoPagarme
        ]);
    }

    private function montarWhere(array $condicoes = []): string
    {
        $filtrosLimpos = [];

        foreach ($condicoes as $condicao) {
            if (!empty(trim($condicao))) {
                $filtrosLimpos[] = trim($condicao, " AND");
            }
        }

        return implode(" AND ", $filtrosLimpos);
    }


    public function linksUteis($data)
    {
        echo $this->view->render("links-uteis", [
            "title" => "Links Úteis"
        ]);
    }

    public function marketplace($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("marketplace");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'marketplace'")->fetch();

        $paginador = new Paginador();
        $filtros = $this->filtros();
        if ($filtros) {
            $filtros .= " AND ";
        }

        $recente = @$_GET["recente"];
        if ($recente)
            $order = "id";
        else
            $order = "rockets";

        $pagina = @$data["page"];
        $offset = 16;

        /*if(@$_GET["vendidos"]){
            $produto = new Venda();
            $produtosCount = (new Venda())->find("produto IN (SELECT id FROM ")->count();
            $paginas = ceil($produtosCount / $offset);
            $produtos = $produto->find($filtros . "status = 2 AND visualizacao = 1")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order($order . " DESC")->fetch(true);
            /**
             * SELECT COUNT(*), produto FROM `vendas` WHERE produto IN (SELECT produto.id FROM produto) GROUP BY produto;

             * SELECT COUNT(*), produto.id, codigo, nome FROM `produto` WHERE produto.id IN (SELECT vendas.produto FROM vendas order by COUNT(*)) GROUP BY produto.id
             *
        } else {*/
        $produto = new Produto();
        $produtosCount = (new Produto())->find("status = 2 AND visualizacao = 1")->count();
        $paginas = ceil($produtosCount / $offset);
        $produtos = $produto->find($filtros . "status = 2 AND visualizacao = 1")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order($order . " DESC")->fetch(true);


        echo $this->view->render("marketplace", [
            "title" => "Marketplace WinPay",
            "section" => @$section,
            "permissao" => @$permissao,
            "produtos" => $produtos,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function filtros()
    {
        $nome = @$_GET["nome"];
        $categoria = @$_GET["categoria"];
        $comissao = @$_GET["comissao"];
        $tipo = @$_GET["tipo"];
        $formato = @$_GET["formato"];
        $sql = array();

        if ($nome != "") {
            $sql[] = "nome LIKE '%{$nome}%'";
        }

        if ($categoria != "") {
            $sql[] = "categoria = {$categoria}";
        }

        if ($comissao != "") {
            $sql[] = "tipo_atribuicao = {$comissao}";
        }

        if ($tipo != "") {
            $sql[] = "tipo_produto = {$tipo}";
        }

        if ($formato != "") {
            if ($formato == 8)
                $sql[] = "tipo_produto = {$formato}";
            else
                $sql[] = "tipo_produto != 8";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function relatorios($data)
    {
        echo $this->view->render("relatorios", [
            "title" => "Relatorios de Vendas"
        ]);
    }

    public function cursos($data)
    {
        echo $this->view->render("cursos", [
            "title" => "Cursos Teste"
        ]);
    }

    public function ferramentaEmail($data)
    {
        $usuarioTipo = $this->section->getTipo();
        $usuarioId = $this->section->getId();

        if (!in_array($usuarioTipo, [1, 4])) {
            header("Location: " . url());
            exit;
        }

        $statusOpcoes = [
            "paga" => [
                "label" => "Pagas",
                "codigos" => [4, 8],
            ],
            "pendente" => [
                "label" => "Pendentes",
                "codigos" => [1, 2, 3],
            ],
            "cancelada" => [
                "label" => "Canceladas",
                "codigos" => [5, 6, 7],
            ],
            "todas" => [
                "label" => "Todas",
                "codigos" => [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
        ];

        $filtroProduto = "excluir = 0 AND status != 6 AND status != 0";
        if ($usuarioTipo !== 1) {
            $filtroProduto .= " AND usuario = {$usuarioId}";
        }

        $produtos = (new Produto())
            ->find($filtroProduto)
            ->order("nome ASC")
            ->fetch(true);

        $parseManualEmails = function (?string $raw) {
            $destinos = [];
            if (!$raw) {
                return $destinos;
            }

            $lista = preg_split('/[\r\n,;]+/', $raw);

            foreach ($lista as $item) {
                $email = strtolower(trim($item));
                if (!$email) {
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $nome = ucfirst(explode("@", $email)[0]);
                $destinos[$email] = [
                    "nome" => $nome ?: "Contato",
                    "email" => $email,
                    "origem" => "manual",
                ];
            }

            return $destinos;
        };

        $feedback = null;
        $resultadoEnvio = null;
        $old = [
            "produto_id" => null,
            "status_filtro" => "todas",
            "assunto" => "",
            "mensagem" => "",
            "destinatarios_extra" => "",
            "checkout_codigo" => "",
        ];

        $checkouts = [];
        $checkoutFiltroBase = $usuarioTipo !== 1 ? "usuario = :usuario" : "1=1";
        $checkoutParams = $usuarioTipo !== 1 ? "usuario={$usuarioId}" : null;
        $listaCheckout = (new Checkout())->find($checkoutFiltroBase, $checkoutParams)->order("produto ASC")->fetch(true);
        if ($listaCheckout) {
            foreach ($listaCheckout as $checkoutItem) {
                $checkouts[] = [
                    "codigo" => $checkoutItem->codigo,
                    "descricao" => $checkoutItem->descricao,
                    "produto_id" => (int) $checkoutItem->produto,
                ];
            }
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $produtoId = filter_input(INPUT_POST, "produto_id", FILTER_SANITIZE_NUMBER_INT);
            $statusFiltro = $_POST["status_filtro"] ?? "paga";
            $assunto = trim($_POST["assunto"] ?? "");
            $mensagem = $_POST["mensagem"] ?? "";
            $destinatariosExtra = trim($_POST["destinatarios_extra"] ?? "");
            $checkoutCodigo = trim($_POST["checkout_codigo"] ?? "");
            if (!$produtoId) {
                $statusFiltro = "todas"; // ignora filtro quando sem produto
            }

            $old = [
                "produto_id" => $produtoId,
                "status_filtro" => $statusFiltro,
                "assunto" => $assunto,
                "mensagem" => $mensagem,
                "destinatarios_extra" => $destinatariosExtra,
                "checkout_codigo" => $checkoutCodigo,
            ];

            $destinosManuais = $parseManualEmails($destinatariosExtra);

            $temFiltroVendas = $produtoId || $checkoutCodigo;

            $checkoutProdutoId = null;
            if ($checkoutCodigo) {
                $filtroBase = "codigo = :checkout";
                $paramsBase = "checkout={$checkoutCodigo}";

                if ($usuarioTipo !== 1) {
                    $filtroBase .= " AND usuario = :usuario";
                    $paramsBase .= "&usuario={$usuarioId}";
                }

                $checkoutObj = (new Checkout())->find($filtroBase, $paramsBase)->fetch();
                if ($checkoutObj && $checkoutObj->produto) {
                    $checkoutProdutoId = (int) $checkoutObj->produto;
                }
            }

            if ((!$temFiltroVendas && empty($destinosManuais)) || !$assunto || !$mensagem) {
                $feedback = [
                    "tipo" => "danger",
                    "mensagem" => "Informe produto ou checkout, ou adicione emails manualmente, e preencha assunto e mensagem para disparar.",
                ];
            } elseif ($produtoId && !isset($statusOpcoes[$statusFiltro])) {
                $feedback = [
                    "tipo" => "danger",
                    "mensagem" => "Selecione uma opção de status válida.",
                ];
            } else {
                $destinatarios = [];

                if ($temFiltroVendas) {
                    $statusCodes = implode(", ", array_map("intval", $statusOpcoes[$statusFiltro]["codigos"]));
                    $filtroVendas = "status_transacao IN ({$statusCodes})";
                    $paramsArr = [];

                    if ($produtoId) {
                        $filtroVendas .= " AND produto = :produto";
                        $paramsArr[] = "produto={$produtoId}";
                    } elseif ($checkoutProdutoId) {
                        $filtroVendas .= " AND produto = :produto";
                        $paramsArr[] = "produto={$checkoutProdutoId}";
                    }

                    if ($checkoutCodigo) {
                        $filtroVendas .= " AND url_checkout LIKE :checkout";
                        $paramsArr[] = "checkout=%{$checkoutCodigo}%";
                    }

                    if ($usuarioTipo !== 1) {
                        $filtroVendas .= " AND produtor = :produtor";
                        $paramsArr[] = "produtor={$usuarioId}";
                    }

                    $params = implode("&", $paramsArr);
                    $vendas = (new Venda())->find($filtroVendas, $params)->fetch(true);

                    if ($vendas) {
                        foreach ($vendas as $venda) {
                            $comprador = $venda->dataComprador();

                            if (!$comprador || !@$comprador->email) {
                                continue;
                            }

                            $email = strtolower(trim($comprador->email));

                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                continue;
                            }

                            $destinatarios[$email] = [
                                "nome" => $comprador->nome ?? "Cliente",
                                "email" => $email,
                                "origem" => "filtro",
                            ];
                        }
                    }
                }

                if ($destinosManuais) {
                    foreach ($destinosManuais as $email => $dados) {
                        $destinatarios[$email] = $dados;
                    }
                }

                if (empty($destinatarios)) {
                    $feedback = [
                        "tipo" => "warning",
                        "mensagem" => "Nenhum destinatário encontrado para os filtros aplicados e/ou emails manuais informados.",
                    ];
                } else {
                    $envio = new Emails();
                    $sucesso = 0;
                    $falha = 0;
                    $logs = [];
                    $manualCount = $destinosManuais ? count($destinosManuais) : 0;
                    $filtroCount = count($destinatarios) - $manualCount;

                    foreach ($destinatarios as $destinatario) {
                        $payload = [
                            "assunto" => $assunto,
                            "mensagem" => $mensagem,
                            "nome" => $destinatario["nome"],
                            "email" => $destinatario["email"],
                        ];

                        $retorno = $envio->enviaPhpMailer($payload);
                        $ok = stripos($retorno, "sucesso") !== false;

                        if ($ok) {
                            $sucesso++;
                        } else {
                            $falha++;
                        }

                        $logs[] = [
                            "email" => $destinatario["email"],
                            "nome" => $destinatario["nome"],
                            "retorno" => $retorno,
                        ];
                    }

                    $resultadoEnvio = [
                        "total" => count($destinatarios),
                        "sucesso" => $sucesso,
                        "falha" => $falha,
                        "status" => $produtoId ? $statusOpcoes[$statusFiltro]["label"] : "Manual",
                        "manual" => $manualCount,
                        "automatico" => $filtroCount,
                        "logs" => $logs,
                    ];

                    $feedback = [
                        "tipo" => $falha > 0 ? "warning" : "success",
                        "mensagem" => "Disparo concluído. {$sucesso} enviado(s), {$falha} falhou(aram).",
                    ];
                }
            }

            $_SESSION["ferramenta_email_feedback"] = $feedback;
            $_SESSION["ferramenta_email_resultado"] = $resultadoEnvio;

            header("Location: " . url("emails"));
            exit;
        }

        if (isset($_SESSION["ferramenta_email_feedback"])) {
            $feedback = $_SESSION["ferramenta_email_feedback"];
            unset($_SESSION["ferramenta_email_feedback"]);
        }

        if (isset($_SESSION["ferramenta_email_resultado"])) {
            $resultadoEnvio = $_SESSION["ferramenta_email_resultado"];
            unset($_SESSION["ferramenta_email_resultado"]);
        }

        echo $this->view->render("ferramentas/ferramenta-emails", [
            "title" => "Disparo de Emails",
            "produtos" => $produtos,
            "checkouts" => $checkouts,
            "statusOpcoes" => $statusOpcoes,
            "feedback" => $feedback,
            "resultadoEnvio" => $resultadoEnvio,
            "old" => $old,
        ]);
    }

    public function error(array $data): void
    {
        echo $this->view->render("error", [
            "title" => "Erro {$data['errcode']}",
            "error" => $data["errcode"]
        ]);
    }
}

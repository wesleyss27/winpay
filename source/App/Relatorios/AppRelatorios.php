<?php

namespace Source\App\Relatorios;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Saldo;
use Source\Models\Comissao\Banco;
use Source\Models\Vendas\Venda;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Enderecos\Endereco;
use Source\Models\Produtos\Plano;
use Source\Models\Permissao\Section;
use Source\Models\Permissao\PermissaoModulo;

session_start();

class AppRelatorios
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

    public function relatorios($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("analitico_vendas");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'analitico_vendas'")->fetch();

        $filtros = $this->filtros();
        $somaVendas = (new Venda())->find($filtros . "produtor = " . @$this->section->getId(), null, "SUM(valor_liquido) as soma")->fetch();
        $vendasCount = (new Venda())->find($filtros)->count();
        $afiliacao = (new Afiliacao())->find("produto = " . @$_POST["produto"])->fetch(true);
        $usuarioDados = (new UsuarioDados());
        $vendas = (new Venda());
        $planos = (new Plano());

        if (@$_POST["inicio"] && @$_POST["fim"]) {
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";
            $periodo2 = "'" . @$_POST["inicio"] . " 00:00:00' AND '" . @$_POST["fim"] . " 23:59:59'";
        } else {
            $periodo = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
            $periodo2 = "'" . date("Y-m-01 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
        }

        if ($filtros) {
            $somaVendas = (new Venda())->find($filtros . " AND afiliado IS NOT NULL", null, "SUM(comissao_afiliado) as soma")->fetch();
            $vendasCount = (new Venda())->find($filtros . " AND afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find($filtros . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($filtros . " AND forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find($filtros . " AND forma_pagamento = 3")->count();
            $vendasPeriodo = (new Venda())->find($filtros . " AND status_transacao = 4")->fetch(true);
            $planoMaior = (new Venda())->find($filtros . " AND status_transacao = 4 GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
        } else {
            $somaVendas = (new Venda())->find("afiliado IS NOT NULL", null, "SUM(comissao_afiliado) as soma")->fetch();
            $vendasCount = (new Venda())->find("afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find("forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find("forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find("forma_pagamento = 3")->count();
            $vendasPeriodo = (new Venda())->find("status_transacao = 4")->fetch(true);
            $planoMaior = (new Venda())->find("status_transacao = 4 GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
        }

        if ($filtros) {

            $this->sessao = (new AppSessao())->verificaSeLogado();

            $filtros = $this->filtros();

            $usuario = $this->section->getId();
            $produto = @$_POST["produto"];
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";

            if ($this->section->getTipo() == 5) {
                $produtor = "afiliado = $usuario";
                $soma = "SUM(comissao_afiliado) as valor";
            } else {
                $produtor = "(produtor = $usuario OR afiliado = $usuario)";
                $soma = "SUM(valor_liquido) as valor";
            }

            if (@$_POST["produto"] && @$_POST["inicio"] && @$_POST["fim"]) {
                $consulta = "
                $produtor AND 
                produto = $produto AND 
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido";
                $vendasDia = $vendas->find(
                    $consulta,
                    null,
                    "DISTINCT data_pedido as dia, 
                    $soma
                "
                )->fetch(true);
            }

            // var_dump($consulta);
            $_SESSION["busca"] = $filtros;

            if ($filtros)
                $filtros .= " AND ";

            $dados["totalVendas"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario)")->count();

            $valorVendas1 = $vendas->find($filtros . "produtor = $usuario", null, "SUM(valor_liquido) as valor")->fetch();
            $valorVendas2 = $vendas->find($filtros . "afiliado = $usuario", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorVendas"] = @$valorVendas1->valor + @$valorVendas2->valor;

            ####
            $dados["cartoes"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1")->count();
            $valorCartao1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 1", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 1", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorCartao"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["cartoesPagos"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorCartaoPago"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["cartoesCancelados"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorCartaoCancelado"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["cartoesOutros"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["cartoesReembolsos"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["boletos"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2")->count();
            $valorCartao1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 2", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 2", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorBoleto"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["boletosPagos"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorBoletoPago"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["boletosCancelados"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorBoletoCancelado"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["boletosOutros"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["boletosReembolsos"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["reembolsos"] = $vendas->find($filtros . "status_transacao = 6")->count();
            $valorReembolso1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 6", null, "SUM(valor_produto) as valor")->fetch();
            $valorReembolso2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 6", null, "SUM(comissão_afiliado) as valor")->fetch();
            $dados["valorReembolso"] = @$valorReembolso1->valor + @$valorReembolso2->valor;

            $dados["chargeback"] = $vendas->find($filtros . "status_transacao = 7")->count();
            $valorChargeback1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 7", null, "SUM(valor_produto) as valor")->fetch();
            $valorChargeback2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 7", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorChargeback"] = @$valorChargeback1->valor + @$valorChargeback2->valor;

            $dados["upsell"] = $vendas->find($filtros . "upsell IS NOT NULL")->count();
            $valorUpsell1 = $vendas->find($filtros . "produtor = $usuario AND upsell IS NOT NULL", null, "SUM(valor_produto) as valor")->fetch();
            $valorUpsell2 = $vendas->find($filtros . "afiliado = $usuario AND upsell IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorUpsell"] = @$valorUpsell1->valor + @$valorUpsell2->valor;

            $dados["upsellPago"] = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL")->count();
            $valorUpsellPago1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(valor_produto) as valor")->fetch();
            $valorUpsellPago2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorUpsellPago"] = @$valorUpsellPago1->valor + @$valorUpsellPago2->valor;

            $dados["upsellCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND (produtor = $usuario OR afiliado = $usuario) AND upsell IS NOT NULL")->count();
            $valorUpsellCancelado1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 5 AND upsell IS NOT NULL", null, "SUM(valor_liquido) as valor")->fetch();
            $valorUpsellCancelado2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 5 AND upsell IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorUpsellCancelado"] = @$valorUpsellCancelado1->valor + @$valorUpsellCancelado2->valor;

            $dados["orderBump"] = $vendas->find($filtros . "planos_order_bump IS NOT NULL")->count();
            $valorOrderBump1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(valor_produto) as valor")->fetch();
            $valorOrderBump2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorOrderBump"] = @$valorOrderBump1->valor + @$valorOrderBump2->valor;

            $dados["orderBumpPago"] = $vendas->find($filtros . "status_transacao = 4 AND planos_order_bump IS NOT NULL")->count();
            $valorOrderBumpPago1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(valor_produto) as valor")->fetch();
            $valorOrderBumpPago2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorOrderBumpPago"] = @$valorOrderBumpPago1->valor + @$valorOrderBumpPago2->valor;

            $dados["orderBumpCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND (produtor = $usuario OR afiliado = $usuario) AND planos_order_bump IS NOT NULL")->count();
            $valororderBumpCancelado1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 5 AND planos_order_bump IS NOT NULL", null, "SUM(valor_liquido) as valor")->fetch();
            $valororderBumpCancelado2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 5 AND planos_order_bump IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valororderBumpCancelado"] = @$valororderBumpCancelado1->valor + @$valororderBumpCancelado2->valor;

            $dados["televendas"] = $vendas->find($filtros . "atendente_televendas IS NOT NULL")->count();
            $valorTelevendas1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(valor_produto) as valor")->fetch();
            $valorTelevendas2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorTelevendas"] = @$valorTelevendas1->valor + @$valorTelevendas2->valor;

            $dados["televendasPago"] = $vendas->find($filtros . "status_transacao = 4 AND atendente_televendas IS NOT NULL")->count();
            $valorTelevendasPago1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(valor_produto) as valor")->fetch();
            $valorTelevendasPago2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorTelevendasPago"] = @$valorTelevendasPago1->valor + @$valorTelevendasPago2->valor;

            $dados["televendasCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND (produtor = $usuario OR afiliado = $usuario) AND atendente_televendas IS NOT NULL")->count();
            $valorTelevendasCancelado1 = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 5 AND atendente_televendas IS NOT NULL", null, "SUM(valor_liquido) as valor")->fetch();
            $valorTelevendasCancelado2 = $vendas->find($filtros . "afiliado = $usuario AND status_transacao = 5 AND atendente_televendas IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorTelevendasCancelado"] = @$valorTelevendasCancelado1->valor + @$valorTelevendasCancelado2->valor;

            //$todasVendasProdutor = $vendas->find($filtros . "produtor = $usuario")->fetch(true);
            //$todasVendasAfiliado = $vendas->find($filtros . "afiliado = $usuario")->fetch(true);

            ####
            $dados["pix"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 3")->count();
            $valorPix1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 3", null, "SUM(valor_liquido) as valor")->fetch();
            $valorPix2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 3", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorPix"] = @$valorPix1->valor + @$valorPix2->valor;

            $dados["pixPagos"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorPix1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorPix2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorPixPago"] = @$valorPix1->valor + @$valorPix2->valor;

            $dados["pixCancelados"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 3 AND (status_transacao = 5)")->count();
            $valorPix1 = $vendas->find($filtros . "produtor = $usuario AND forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorPix2 = $vendas->find($filtros . "afiliado = $usuario AND forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorPixCancelado"] = @$valorPix1->valor + @$valorPix2->valor;

            $dados["pixOutros"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 3 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["pixReembolsos"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 3 AND (status_transacao = 6 || status_transacao = 7)")->count();

            $dados["vendasDiretas"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND status_transacao = 4 AND afiliado IS NULL")->count();
            $dados["vendasAfiliados"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario) AND status_transacao = 4 AND afiliado IS NOT NULL")->count();

            $valorVendasDiretas = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 4 AND afiliado IS NULL", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasDiretas"] = @$valorVendasDiretas->valor;
            $valorVendasAfiliados = $vendas->find($filtros . "produtor = $usuario AND status_transacao = 4 AND afiliado IS NOT NULL", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasAfiliados"] = @$valorVendasAfiliados->valor;
        }

        $produtos = new Produto();
        $produtoAfiliacao = new Afiliacao();

        echo $this->view->render("relatorios/relatorios", [
            "title" => "Analíticos de Vendas",
            "section" => @$section,
            "permissao" => @$permissao,
            "vendasD" => @$vendasDia,
            "dados" => @$dados,
            "produtos" => $produtos,
            "count" => $vendasCount,
            "afiliacao" => $afiliacao,
            "somaVendas" => @$somaVendas,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
            "vendasPix" => $vendasPix,
            "usuarioDados" => $usuarioDados,
            "vendas" => $vendas,
            "vendasPeriodo" => $vendasPeriodo,
            "valorVendasAfiliados" => @$valorVendasAfiliados,
            "planoMaior" => $planoMaior,
            "planos" => $planos,
            "estados" => @$estados,
            "busca" => @$filtros,
            "produtoAfiliacao" => @$produtoAfiliacao,
        ]);
    }

    public function filtros()
    {
        $produto = @$_POST["produto"];
        $produtor = @$_POST["produtor"];
        $inicio = @$_POST["inicio"];
        $fim = @$_POST["fim"];
        $sql = array();

        if ($produto != "") {
            $sql[] = "produto = '{$produto}'";
        }

        if ($produtor != "") {
            $sql[] = "produtor = '{$produtor}'";
        }

        if ($inicio != "" || $fim != "") {
            if ($inicio == "")
                $inicio = date("Y-m-d");

            if ($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "(data_pedido BETWEEN '{$inicio}' AND '{$fim}' OR data_finalizada BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND ";
        }

        return implode(" ", $sql);
    }

    public function buscaRelatorios($data)
    {
        $vendas = new Venda();
        $usuario = $this->section->getId();
        $produto = $data["produto"];
        $periodo = "'" . $data["inicio"] . "' AND '" . $data["fim"] . "'";

        $vendasDia = $vendas->find(
            "
            (produtor = $usuario OR afiliado = $usuario) AND 
            produto = $produto and 
            data_pedido BETWEEN $periodo 
            GROUP BY data_pedido",
            null,
            "DISTINCT data_pedido as dia, 
            SUM(valor_bruto) as valor
        "
        )->fetch(true);

        $i = 1;
        foreach ($vendasDia as $venda) {
            //var_dump($venda->dia, $venda->valor);
            $dados["grafico"][$i] = array($venda->dia, $venda->valor);
            $i++;
        }

        echo json_encode($dados);
    }
    public function exportar($data)
    {
        //var_dump($_SESSION);
        if (@$_GET["tabela"] && $_GET["tabela"] == "vendas") {
            $arquivo = "vendas.xls";
            echo $_SESSION["tabelaProduto"] . "<br><br>" . $_SESSION["tabelaDias"];
        }

        if (@$_GET["tabela"] && $_GET["tabela"] == "analitico") {
            $arquivo = "analitico.xls";
            echo $_SESSION["tabelaProduto"] . "<br><br>" . $_SESSION["tabelaAnalitico"];
        }

        header("Cache-Control: no-cache, must-revalidate");
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel');
        header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
    }

    public function painelRedRocket($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $filtros = $this->filtros();

        $vendas = new Venda();
        // $somaVendas = (new Venda())->find($filtros . "produtor = " . @$this->section->getId(), null, "SUM(valor_redrocket) as soma")->fetch();
        $usuarioDados = (new UsuarioDados());
        $planos = (new Plano());
        $usuario = $this->section->getId();
        $produto = @$_POST["produto"];
        $produtor = @$_POST["produtor"];

        if (@$_POST["inicio"] && @$_POST["fim"]) {
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";
            $periodo2 = "'" . @$_POST["inicio"] . " 00:00:00' AND '" . @$_POST["fim"] . " 23:59:59'";
        } else {
            $periodo = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
            $periodo2 = "'" . date("Y-m-01 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
        }

        $antecipacao = (new Comissao())->find("usuario = 1 AND descricao LIKE '%Taxa antecipação%U$produtor%' AND data_pedido BETWEEN $periodo", null, "SUM(valor) as valor")->fetch();

        if ($filtros) {
            // $somaVendas = (new Venda())->find($filtros . " AND afiliado IS NOT NULL", null, "SUM(valor_redrocket) as soma")->fetch();
            $vendasCount = (new Venda())->find($filtros . " AND afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find($filtros . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($filtros . " AND forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find($filtros . " AND forma_pagamento = 3")->count();
            $vendasPeriodo = (new Venda())->find($filtros . " AND status_transacao = 4")->fetch(true);
            $planoMaior = (new Venda())->find($filtros . " AND status_transacao = 4 GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
            $vendasCount = (new Venda())->find($filtros)->count();
            $afiliacao = (new Venda())->find($filtros . " AND status_transacao = 4 AND afiliado IS NOT NULL GROUP BY afiliado ORDER BY soma DESC", NULL, "*, COUNT(*) AS total, SUM(valor_bruto) AS soma")->fetch(true);
            $cliente = (new Venda())->find($filtros . " AND status_transacao = 4 AND produtor IS NOT NULL GROUP BY produtor ORDER BY soma DESC ", NULL, "*, COUNT(*) AS total, SUM(valor_redrocket) AS soma")->fetch(true);
        } else {
            $inicio = date("Y-m-01");
            $fim = date("Y-m-d");
            $periodoMes = "(data_pedido BETWEEN '{$inicio}' AND '{$fim}' OR data_finalizada BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";

            // $somaVendas = (new Venda())->find($periodoMes . " AND afiliado IS NOT NULL", null, "SUM(valor_redrocket) as soma")->fetch();
            $vendasCount = (new Venda())->find($periodoMes . " AND afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find($periodoMes . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($periodoMes . " AND forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find($periodoMes . " AND forma_pagamento = 3")->count();
            $vendasPeriodo = (new Venda())->find($periodoMes . " AND status_transacao = 4")->fetch(true);
            $planoMaior = (new Venda())->find($periodoMes . " AND status_transacao = 4 GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
            $vendasCount = (new Venda())->find($filtros)->count();
            $afiliacao = (new Venda())->find("status_transacao = 4 AND afiliado IS NOT NULL GROUP BY afiliado ORDER BY soma DESC", NULL, "*, COUNT(*) AS total, SUM(valor_bruto) AS soma")->fetch(true);
            $cliente = (new Venda())->find("status_transacao = 4 AND produtor IS NOT NULL GROUP BY produtor ORDER BY soma DESC ", NULL, "*, COUNT(*) AS total, SUM(valor_redrocket) AS soma")->fetch(true);
        }

        if ((@$_POST["produto"]) && @$_POST["inicio"] && @$_POST["fim"]) {
            $vendasDia = $vendas->find(
                "
                produto = $produto and 
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        } elseif (@$_POST["produtor"] && @$_POST["inicio"] && @$_POST["fim"]) {
            $vendasDia = $vendas->find(
                "
                produtor = $produtor and 
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        } elseif (@$_POST["inicio"] && @$_POST["fim"]) {
            $vendasDia = $vendas->find(
                "
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        } else {
            $vendasDia = $vendas->find(
                "
                data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        }

        $dados["totalVendas"] = $vendas->find($filtros)->count();

        $valorVendas1 = $vendas->find($filtros, null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorVendas"] = @$valorVendas1->valor;

        if ($filtros)
            $filtros .= " AND ";
        else {
            $filtros = "(data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' OR data_finalizada BETWEEN '" . date("Y-m-01") . " 00:00:00' AND '" . date("Y-m-d") . " 23:59:59') AND ";
            $filtro = "(data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' OR data_finalizada BETWEEN '" . date("Y-m-01") . " 00:00:00' AND '" . date("Y-m-d") . " 23:59:59')";

            $dados["totalVendas"] = $vendas->find($filtro)->count();
            $valorVendas1 = $vendas->find($filtro, null, "SUM(valor_redrocket) as valor")->fetch();
            $dados["valorVendas"] = @$valorVendas1->valor;
        }

        ####
        $dados["cartoes"] = $vendas->find($filtros . "forma_pagamento = 1")->count();
        $valorCartao1 = $vendas->find($filtros . "forma_pagamento = 1", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorCartao"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $dados["cartoesPagos"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $valorCartaoPago1 = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorCartaoPago"] = @$valorCartaoPago1->valor;

        $dados["cartoesCancelados"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 5)")->count();
        $valorCartao1 = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorCartaoCancelado"] = @$valorCartao1->valor;

        $dados["cartoesOutros"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
        $dados["cartoesReembolsos"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 6 || status_transacao = 7)")->count();

        ####
        $dados["boletos"] = $vendas->find($filtros . "forma_pagamento = 2")->count();
        $valorBoleto1 = $vendas->find($filtros . "forma_pagamento = 2", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorBoleto"] = @$valorBoleto1->valor;

        $dados["boletosPagos"] = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $valorCartao1 = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorBoletoPago"] = @$valorCartao1->valor;

        $dados["boletosCancelados"] = $vendas->find($filtros . "forma_pagamento = 2 AND status_transacao = 5")->count();
        $valorBoleto1 = $vendas->find($filtros . "forma_pagamento = 2 AND status_transacao = 5", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorBoletoCancelado"] = @$valorBoleto1->valor;

        $dados["boletosOutros"] = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
        $dados["boletosReembolsos"] = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 6 || status_transacao = 7)")->count();

        ####
        $dados["reembolsos"] = $vendas->find($filtros . "status_transacao = 6")->count();
        $valorReembolso1 = $vendas->find($filtros . "status_transacao = 6", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorReembolso"] = @$valorReembolso1->valor;

        $dados["chargeback"] = $vendas->find($filtros . "status_transacao = 7")->count();
        $valorChargeback = $vendas->find($filtros . "status_transacao = 7", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorChargeback"] = @$valorChargeback->valor;

        $dados["upsell"] = $vendas->find($filtros . "upsell IS NOT NULL")->count();
        $valorUpsell = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorUpsell"] = @$valorUpsell->valor;

        $dados["upsellPago"] = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL")->count();
        $valorUpsellPago = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorUpsellPago"] = @$valorUpsellPago->valor;

        $dados["upsellCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND upsell IS NOT NULL")->count();
        $valorUpsellCancelado = $vendas->find($filtros . "status_transacao = 5 AND upsell IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorUpsellCancelado"] = @$valorUpsellCancelado->valor;

        $dados["orderBump"] = $vendas->find($filtros . "planos_order_bump IS NOT NULL")->count();
        $valorOrderBump = $vendas->find($filtros . "status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorOrderBump"] = @$valorOrderBump->valor;

        $dados["orderBumpPago"] = $vendas->find($filtros . "status_transacao = 4 AND planos_order_bump IS NOT NULL")->count();
        $valorOrderBumpPago = $vendas->find($filtros . "status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorOrderBumpPago"] = @$valorOrderBumpPago->valor;

        $dados["orderBumpCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND planos_order_bump IS NOT NULL")->count();
        $valororderBumpCancelado = $vendas->find($filtros . "status_transacao = 5 AND planos_order_bump IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valororderBumpCancelado"] = @$valororderBumpCancelado->valor;

        $dados["televendas"] = $vendas->find($filtros . "atendente_televendas IS NOT NULL")->count();
        $valorTelevendas = $vendas->find($filtros . "status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorTelevendas"] = @$valorTelevendas->valor;

        $dados["televendasPago"] = $vendas->find($filtros . "status_transacao = 4 AND atendente_televendas IS NOT NULL")->count();
        $valorTelevendasPago = $vendas->find($filtros . "status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorTelevendasPago"] = @$valorTelevendasPago->valor;

        $dados["televendasCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND atendente_televendas IS NOT NULL")->count();
        $valorTelevendasCancelado = $vendas->find($filtros . "status_transacao = 5 AND atendente_televendas IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorTelevendasCancelado"] = @$valorTelevendasCancelado->valor;

        ####
        $dados["pix"] = $vendas->find($filtros . "forma_pagamento = 3")->count();
        $valorPix1 = $vendas->find($filtros . "forma_pagamento = 3", null, "SUM(valor_liquido) as valor")->fetch();
        $dados["valorPix"] = @$valorPix1->valor;

        $dados["pixPagos"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $valorPix1 = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorPixPago"] = @$valorPix1->valor;

        $dados["pixCancelados"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 5)")->count();
        $valorPix1 = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorPixCancelado"] = @$valorPix1->valor;

        $dados["pixOutros"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
        $dados["pixReembolsos"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 6 || status_transacao = 7)")->count();

        ####
        $dados["vendasAfiliados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $dados["vendasDiretas"] = $vendas->find($filtros . "afiliado IS NULL AND (status_transacao = 4 OR status_transacao = 8)")->count();

        $valorVendasDiretas = $vendas->find($filtros . "afiliado is null AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorVendasDiretas"] = @$valorVendasDiretas->valor;
        $valorVendasAfiliados = $vendas->find($filtros . "afiliado is not null AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorVendasAfiliados"] = @$valorVendasAfiliados->valor;

        function jurosMoip($valor, $parcela)
        {
            $porcentagem = array(
                1 => 0.0419,
                2 => 0.0519,
                3 => 0.0549,
                4 => 0.0599,
                5 => 0.0649,
                6 => 0.0699,
                7 => 0.0799,
                8 => 0.0849,
                9 => 0.0899,
                10 => 0.0949,
                11 => 0.0999,
                12 => 0.1049,
            );

            return number_format((($valor * $porcentagem[$parcela]) + 0.4), 2);
        }

        function taxasCielo($valor, $parcela)
        {
            $taxaCartao = array(
                1 => 0.037,
                2 => 0.05,
                3 => 0.062,
                4 => 0.077,
                5 => 0.089,
                6 => 0.101,
                7 => 0.113,
                8 => 0.125,
                9 => 0.137,
                10 => 0.149,
                11 => 0.161,
                12 => 0.173,
            );


            return number_format((($valor * $taxaCartao[$parcela]) + 0.4), 2);
        }

        $paginador = new Paginador();
        $pagina = @$data["page"] ? $data["page"] : 1;
        $offset = 30;

        if (@$_GET["meus"]) {
            $busca = "usuario = " . $this->section->getId();
        }

        $comissaoCount = (new Comissao())->find(@$busca)->count();
        $paginas = ceil($comissaoCount / $offset);
        $comissao = (new Comissao())->find(@$busca)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);

        $usuario = $this->section->getId();

        if ($_POST) {
            $jurosCartao = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_juros) as valor")->fetch();
            $liquido = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_liquido) as valor")->fetch();
            $vendas1 = (new Venda())->find($filtros . "status_transacao = 4 AND forma_pagamento = 1 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'")->fetch(true);
            $mes = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_bruto) as valor")->fetch();
            $valorRedRocket = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_redrocket) as valor")->fetch();
            $saqueMes = (new Comissao())->find("usuario != 1 AND tipo = 2 AND status != 3 AND updated_at BETWEEN '" . date($_POST["inicio"]) . "' AND '"  . date($_POST["fim"]) . "'", null, "SUM(valor) as valor")->fetch();
            $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND updated_at BETWEEN '" . date($_POST["inicio"]) . "' AND '"  . date($_POST["fim"]) . "'", null, "SUM(valor) as valor")->fetch();

            // $saqueMes = (new Comissao())->find("usuario != 1 AND tipo = 2 AND status != 3 AND created_at BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
            // $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND created_at BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        } else {
            $jurosCartao = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_juros) as valor")->fetch();
            $liquido = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_liquido) as valor")->fetch();
            $vendas1 = (new Venda())->find("status_transacao = 4 AND forma_pagamento = 1 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'")->fetch(true);
            $mes = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_bruto) as valor")->fetch();
            $valorRedRocket = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_redrocket) as valor")->fetch();
            $saqueMes = (new Comissao())->find("tipo = 2 AND status != 3 AND updated_at BETWEEN '" . date("Y-m-01") . "' AND '"  . date("Y-m-d") . "'", null, "SUM(valor) as valor")->fetch();
            $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND updated_at BETWEEN '" . date("Y-m-01") . "' AND '"  . date("Y-m-d") . "'", null, "SUM(valor) as valor")->fetch();

            // $saqueMes = (new Comissao())->find("tipo = 2 AND status != 3 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
            // $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        }

        if (@$vendas1) {
            // $j = 1;
            $valorJuros = 0;
            foreach ($vendas1 as $venda) {
                if ($venda->meio_pagamento == "Moip") {
                    @$valorJuros += jurosMoip($venda->valor_bruto, $venda->parcelas);
                } elseif ($venda->meio_pagamento == "Cielo") {
                    @$valorJuros += taxasCielo($venda->valor_bruto, $venda->parcelas);
                }
                // var_dump($j, $venda->transacao, juros($venda->valor_bruto, $venda->parcelas));
                // $j++;
            }
        }
        $countBoletosMoip = (new Venda())->find($filtros . "status_transacao = 4 AND forma_pagamento = 2 AND meio_pagamento = 'Moip' AND data_finalizada BETWEEN $periodo2")->count();
        $countBoletosBradesco = (new Venda())->find($filtros . "status_transacao = 4 AND forma_pagamento = 2 AND meio_pagamento = 'Bradesco' AND data_finalizada BETWEEN $periodo2")->count();
        $countPix = (new Venda())->find($filtros . "status_transacao = 4 AND forma_pagamento = 3 AND meio_pagamento = 'Pix' AND data_finalizada BETWEEN $periodo2")->count();
        $countPixBradesco = (new Venda())->find($filtros . "status_transacao = 4 AND forma_pagamento = 3 AND meio_pagamento = 'Pix' AND data_finalizada BETWEEN $periodo2")->count();
        $wirecard = @$valorJuros + (2.79 * $countBoletosMoip) + (1.75 * $countBoletosBradesco) + (1.65 * $countPixBradesco) + (0.37 * $countPix);

        $produtos = new Produto();
        $pendente = (new Comissao())->find("usuario = 1 AND disponivel = 0", null, "SUM(valor) as valor")->fetch();
        $saldo = (new Saldo())->find("usuario = 1")->fetch();
        $produtores = (new Usuario())->find("tipo_usuario = 4 OR tipo_usuario = 1 OR tipo_usuario = 2")->order("nome ASC")->fetch(true);
        $produtoAfiliacao = new Afiliacao();

        echo $this->view->render("relatorios/painel", [
            "title" => "Painel RedRocket",
            "wirecard" => $wirecard,
            "mes" => @$mes->valor,
            "redrocket" => @$valorRedRocket->valor,
            "juros" => @$jurosCartao->valor,
            "liquido" => @$liquido,
            "saldo" => @$saldo->valor,
            "vendasDia" => @$vendasDia,
            "dados" => @$dados,
            "produtos" => $produtos,
            "produtores" => $produtores,
            "saldo" => @$saldo->valor,
            "pendente" => $pendente->valor,
            "saqueMes" => @$saqueMes->valor,
            "saqueOcta" => @$saqueOcta->valor,
            "count" => $vendasCount,
            "afiliacao" => $afiliacao,
            // "somaVendas" => @$somaVendas,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
            "vendasPix" => $vendasPix,
            "usuarioDados" => $usuarioDados,
            "vendas1" => $vendas1,
            "vendas" => $vendas,
            "vendasPeriodo" => $vendasPeriodo,
            "valorVendasAfiliados" => @$valorVendasAfiliados,
            "planoMaior" => $planoMaior,
            "planos" => $planos,
            "estados" => @$estados,
            "busca" => @$filtros,
            "produtoAfiliacao" => @$produtoAfiliacao,
            "cliente" => $cliente,
            "antecipacao" => $antecipacao,
        ]);
    }

    public function painelClientes($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $filtros = $this->filtros();

        $vendas = new Venda();
        $vendasCount = (new Venda())->find($filtros)->count();
        $usuarioDados = (new UsuarioDados());
        $planos = (new Plano());
        $usuario = $this->section->getId();
        $produto = @$_POST["produto"];
        $produtor = @$_POST["produtor"];

        if (@$_POST["inicio"] && @$_POST["fim"]) {
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";
            $periodo2 = "'" . @$_POST["inicio"] . " 00:00:00' AND '" . @$_POST["fim"] . " 23:59:59'";
        } else {
            $periodo = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
            $periodo2 = "'" . date("Y-m-01 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
        }

        if ($filtros) {
            $somaVendas = (new Venda())->find($filtros . " AND afiliado IS NOT NULL", null, "SUM(valor_bruto) as soma")->fetch();
            $vendasCount = (new Venda())->find($filtros . " AND afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find($filtros . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($filtros . " AND forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find($filtros . " AND forma_pagamento = 3")->count();
            $vendasPeriodo = (new Venda())->find($filtros . " AND status_transacao = 4")->fetch(true);
            $planoMaior = (new Venda())->find($filtros . " AND status_transacao = 4 GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
            $somaVendas = (new Venda())->find($filtros . " AND produtor = " . @$this->section->getId(), null, "SUM(valor_bruto) as soma")->fetch();
            $afiliacao = (new Venda())->find($filtros . " AND status_transacao = 4 AND afiliado IS NOT NULL GROUP BY afiliado ORDER BY soma DESC", NULL, "*, COUNT(*) AS total, SUM(valor_bruto) AS soma")->fetch(true);
            $cliente = (new Venda())->find($filtros . " AND status_transacao = 4 AND produtor IS NOT NULL GROUP BY produtor ORDER BY soma DESC ", NULL, "*, COUNT(*) AS total, SUM(valor_bruto) AS soma")->fetch(true);
        } else {
            $inicio = date("Y-m-01");
            $fim = date("Y-m-d");
            $periodoMes = "(data_pedido BETWEEN '{$inicio}' AND '{$fim}' OR data_finalizada BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";

            $somaVendas = (new Venda())->find($periodoMes . " AND afiliado IS NOT NULL", null, "SUM(valor_bruto) as soma")->fetch();
            $vendasCount = (new Venda())->find($periodoMes . " AND afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find($periodoMes . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($periodoMes . " AND forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find($periodoMes . " AND forma_pagamento = 3")->count();
            $vendasPeriodo = (new Venda())->find($periodoMes . " AND status_transacao = 4")->fetch(true);
            $planoMaior = (new Venda())->find($periodoMes . " AND status_transacao = 4 GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
            $somaVendas = (new Venda())->find("produtor = " . @$this->section->getId(), null, "SUM(valor_bruto) as soma")->fetch();
            $afiliacao = (new Venda())->find("status_transacao = 4 AND afiliado IS NOT NULL GROUP BY afiliado ORDER BY soma DESC", NULL, "*, COUNT(*) AS total, SUM(valor_bruto) AS soma")->fetch(true);
            $cliente = (new Venda())->find("status_transacao = 4 AND produtor IS NOT NULL GROUP BY produtor ORDER BY soma DESC ", NULL, "*, COUNT(*) AS total, SUM(valor_bruto) AS soma")->fetch(true);
        }

        if ((@$_POST["produto"]) && @$_POST["inicio"] && @$_POST["fim"]) {
            $vendasDia = $vendas->find(
                "
                produto = $produto and 
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        } elseif (@$_POST["produtor"] && @$_POST["inicio"] && @$_POST["fim"]) {
            $vendasDia = $vendas->find(
                "
                produtor = $produtor and 
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        } elseif (@$_POST["inicio"] && @$_POST["fim"]) {
            $vendasDia = $vendas->find(
                "
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        } else {
            $vendasDia = $vendas->find(
                "
                data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' 
                GROUP BY data_pedido",
                null,
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            "
            )->fetch(true);
        }

        $dados["totalVendas"] = $vendas->find($filtros)->count();

        $valorVendas1 = $vendas->find($filtros, null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorVendas"] = @$valorVendas1->valor;

        if ($filtros)
            $filtros .= " AND ";
        else {
            $filtros = "(data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' OR data_finalizada BETWEEN '" . date("Y-m-01") . " 00:00:00' AND '" . date("Y-m-d") . " 23:59:59') AND ";
            $filtro = "(data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' OR data_finalizada BETWEEN '" . date("Y-m-01") . " 00:00:00' AND '" . date("Y-m-d") . " 23:59:59')";

            $dados["totalVendas"] = $vendas->find($filtro)->count();
            $valorVendas1 = $vendas->find($filtro, null, "SUM(valor_bruto) as valor")->fetch();
            $dados["valorVendas"] = @$valorVendas1->valor;
        }

        ####
        $dados["cartoes"] = $vendas->find($filtros . "forma_pagamento = 1")->count();
        $valorCartao1 = $vendas->find($filtros . "forma_pagamento = 1", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorCartao"] = @$valorCartao1->valor + @$valorCartao2->valor;

        $dados["cartoesPagos"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $valorCartaoPago1 = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorCartaoPago"] = @$valorCartaoPago1->valor;

        $dados["cartoesCancelados"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 5)")->count();
        $valorCartao1 = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorCartaoCancelado"] = @$valorCartao1->valor;

        $dados["cartoesOutros"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
        $dados["cartoesReembolsos"] = $vendas->find($filtros . "forma_pagamento = 1 AND (status_transacao = 6 || status_transacao = 7)")->count();

        ####
        $dados["boletos"] = $vendas->find($filtros . "forma_pagamento = 2")->count();
        $valorBoleto1 = $vendas->find($filtros . "forma_pagamento = 2", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorBoleto"] = @$valorBoleto1->valor;

        $dados["boletosPagos"] = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $valorCartao1 = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorBoletoPago"] = @$valorCartao1->valor;

        $dados["boletosCancelados"] = $vendas->find($filtros . "forma_pagamento = 2 AND status_transacao = 5")->count();
        $valorBoleto1 = $vendas->find($filtros . "forma_pagamento = 2 AND status_transacao = 5", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorBoletoCancelado"] = @$valorBoleto1->valor;

        $dados["boletosOutros"] = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
        $dados["boletosReembolsos"] = $vendas->find($filtros . "forma_pagamento = 2 AND (status_transacao = 6 || status_transacao = 7)")->count();

        ####
        $dados["reembolsos"] = $vendas->find($filtros . "status_transacao = 6")->count();
        $valorReembolso1 = $vendas->find($filtros . "status_transacao = 6", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorReembolso"] = @$valorReembolso1->valor;

        $dados["chargeback"] = $vendas->find($filtros . "status_transacao = 7")->count();
        $valorChargeback = $vendas->find($filtros . "status_transacao = 7", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorChargeback"] = @$valorChargeback->valor;

        $dados["upsell"] = $vendas->find($filtros . "upsell IS NOT NULL")->count();
        $valorUpsell = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorUpsell"] = @$valorUpsell->valor;

        $dados["upsellPago"] = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL")->count();
        $valorUpsellPago = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorUpsellPago"] = @$valorUpsellPago->valor;

        $dados["upsellCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND upsell IS NOT NULL")->count();
        $valorUpsellCancelado = $vendas->find($filtros . "status_transacao = 5 AND upsell IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorUpsellCancelado"] = @$valorUpsellCancelado->valor;

        $dados["orderBump"] = $vendas->find($filtros . "planos_order_bump IS NOT NULL")->count();
        $valorOrderBump = $vendas->find($filtros . "status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorOrderBump"] = @$valorOrderBump->valor;

        $dados["orderBumpPago"] = $vendas->find($filtros . "status_transacao = 4 AND planos_order_bump IS NOT NULL")->count();
        $valorOrderBumpPago = $vendas->find($filtros . "status_transacao = 4 AND planos_order_bump IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorOrderBumpPago"] = @$valorOrderBumpPago->valor;

        $dados["orderBumpCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND planos_order_bump IS NOT NULL")->count();
        $valororderBumpCancelado = $vendas->find($filtros . "status_transacao = 5 AND planos_order_bump IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valororderBumpCancelado"] = @$valororderBumpCancelado->valor;

        $dados["televendas"] = $vendas->find($filtros . "atendente_televendas IS NOT NULL")->count();
        $valorTelevendas = $vendas->find($filtros . "status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorTelevendas"] = @$valorTelevendas->valor;

        $dados["televendasPago"] = $vendas->find($filtros . "status_transacao = 4 AND atendente_televendas IS NOT NULL")->count();
        $valorTelevendasPago = $vendas->find($filtros . "status_transacao = 4 AND atendente_televendas IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorTelevendasPago"] = @$valorTelevendasPago->valor;

        $dados["televendasCancelados"] = $vendas->find($filtros . "status_transacao = 5 AND atendente_televendas IS NOT NULL")->count();
        $valorTelevendasCancelado = $vendas->find($filtros . "status_transacao = 5 AND atendente_televendas IS NOT NULL", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorTelevendasCancelado"] = @$valorTelevendasCancelado->valor;

        ####
        $dados["pix"] = $vendas->find($filtros . "forma_pagamento = 3")->count();
        $valorPix1 = $vendas->find($filtros . "forma_pagamento = 3", null, "SUM(valor_liquido) as valor")->fetch();
        $dados["valorPix"] = @$valorPix1->valor;

        $dados["pixPagos"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $valorPix1 = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorPixPago"] = @$valorPix1->valor;

        $dados["pixCancelados"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 5)")->count();
        $valorPix1 = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorPixCancelado"] = @$valorPix1->valor;

        $dados["pixOutros"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
        $dados["pixReembolsos"] = $vendas->find($filtros . "forma_pagamento = 3 AND (status_transacao = 6 || status_transacao = 7)")->count();

        ####
        $dados["vendasAfiliados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND (status_transacao = 4 OR status_transacao = 8)")->count();
        $dados["vendasDiretas"] = $vendas->find($filtros . "afiliado IS NULL AND (status_transacao = 4 OR status_transacao = 8)")->count();

        $valorVendasDiretas = $vendas->find($filtros . "afiliado is null AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorVendasDiretas"] = @$valorVendasDiretas->valor;
        $valorVendasAfiliados = $vendas->find($filtros . "afiliado is not null AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_bruto) as valor")->fetch();
        $dados["valorVendasAfiliados"] = @$valorVendasAfiliados->valor;

        function juros($valor, $parcela)
        {
            $porcentagem = array(
                1 => 0.0419,
                2 => 0.0519,
                3 => 0.0549,
                4 => 0.0599,
                5 => 0.0649,
                6 => 0.0699,
                7 => 0.0799,
                8 => 0.0849,
                9 => 0.0899,
                10 => 0.0949,
                11 => 0.0999,
                12 => 0.1049,
            );

            return number_format((($valor * $porcentagem[$parcela]) + 0.4), 2);
        }

        $paginador = new Paginador();
        $pagina = @$data["page"] ? $data["page"] : 1;
        $offset = 30;

        if (@$_GET["meus"]) {
            $busca = "usuario = " . $this->section->getId();
        }

        $comissaoCount = (new Comissao())->find(@$busca)->count();
        $paginas = ceil($comissaoCount / $offset);
        $comissao = (new Comissao())->find(@$busca)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);

        $usuario = $this->section->getId();

        if ($_POST) {
            $jurosCartao = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_juros) as valor")->fetch();
            $liquido = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_liquido) as valor")->fetch();
            $vendas1 = (new Venda())->find($filtros . "status_transacao = 4 AND forma_pagamento = 1 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'")->fetch(true);
            $mes = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_bruto) as valor")->fetch();
            $valorRedRocket = (new Venda())->find($filtros . "status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_redrocket) as valor")->fetch();
            $saqueMes = (new Comissao())->find("usuario != 1 AND tipo = 2 AND status != 3 AND updated_at BETWEEN '" . date($_POST["inicio"]) . "' AND '"  . date($_POST["fim"]) . "'", null, "SUM(valor) as valor")->fetch();
            $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND updated_at BETWEEN '" . date($_POST["inicio"]) . "' AND '"  . date($_POST["fim"]) . "'", null, "SUM(valor) as valor")->fetch();

            // $saqueMes = (new Comissao())->find("usuario != 1 AND tipo = 2 AND status != 3 AND created_at BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
            // $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND created_at BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        } else {
            $jurosCartao = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_juros) as valor")->fetch();
            $liquido = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_liquido) as valor")->fetch();
            $vendas1 = (new Venda())->find("status_transacao = 4 AND forma_pagamento = 1 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'")->fetch(true);
            $mes = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_bruto) as valor")->fetch();
            $valorRedRocket = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_redrocket) as valor")->fetch();
            $saqueMes = (new Comissao())->find("tipo = 2 AND status != 3 AND updated_at BETWEEN '" . date("Y-m-01") . "' AND '"  . date("Y-m-d") . "'", null, "SUM(valor) as valor")->fetch();
            $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND updated_at BETWEEN '" . date("Y-m-01") . "' AND '"  . date("Y-m-d") . "'", null, "SUM(valor) as valor")->fetch();

            // $saqueMes = (new Comissao())->find("tipo = 2 AND status != 3 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
            // $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        }

        if (@$vendas1) {
            // $j = 1;
            foreach ($vendas1 as $venda) {
                @$valorJuros += juros($venda->valor_bruto, $venda->parcelas);
                // var_dump($j, $venda->transacao, juros($venda->valor_bruto, $venda->parcelas));
                // $j++;
            }
        }
        $countBoletos = (new Venda())->find($filtros . "status_transacao = 4 AND forma_pagamento = 2 AND data_finalizada BETWEEN $periodo2")->count();
        $wirecard = @$valorJuros + (2.79 * $countBoletos);

        $produtos = new Produto();
        $pendente = (new Comissao())->find("usuario = 1 AND disponivel = 0", null, "SUM(valor) as valor")->fetch();
        $saldo = (new Saldo())->find("usuario = 1")->fetch();
        $produtores = (new Usuario())->find("tipo_usuario = 4 OR tipo_usuario = 1 OR tipo_usuario = 2")->fetch(true);
        $produtoAfiliacao = new Afiliacao();

        echo $this->view->render("relatorios/painel_clientes", [
            "title" => "Painel Clientes",
            "hoje" => @$hoje->valor,
            "wirecard" => $wirecard,
            "mes" => @$mes->valor,
            "redrocket" => @$valorRedRocket->valor,
            "juros" => @$jurosCartao->valor,
            "liquido" => @$liquido,
            "saldo" => @$saldo->valor,
            "vendasDia" => @$vendasDia,
            "dados" => @$dados,
            "produtos" => $produtos,
            "produtores" => $produtores,
            "saldo" => @$saldo->valor,
            "pendente" => $pendente->valor,
            "saqueMes" => @$saqueMes->valor,
            "saqueOcta" => @$saqueOcta->valor,
            "count" => $vendasCount,
            "afiliacao" => $afiliacao,
            "somaVendas" => @$somaVendas,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
            "vendasPix" => $vendasPix,
            "usuarioDados" => $usuarioDados,
            "vendas1" => $vendas1,
            "vendas" => $vendas,
            "vendasPeriodo" => $vendasPeriodo,
            "valorVendasAfiliados" => @$valorVendasAfiliados,
            "planoMaior" => $planoMaior,
            "planos" => $planos,
            "estados" => @$estados,
            "busca" => @$filtros,
            "produtoAfiliacao" => @$produtoAfiliacao,
            "cliente" => $cliente,

        ]);
    }

    public function painelNF($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        if (@$_POST["inicio"] && @$_POST["fim"]) {
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";
            $periodo2 = "'" . @$_POST["inicio"] . " 00:00:00' AND '" . @$_POST["fim"] . " 23:59:59'";
        } else {
            $periodo = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
            $periodo2 = "'" . date("Y-m-01 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
        }

        $vendas = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN $periodo2 GROUP BY produtor", null, "COUNT(*) as total, SUM(vendas.valor_redrocket) as taxa, AVG(taxa_redrocket) as taxa_media, produtor")->fetch(true);
        $usuarioDados = new UsuarioDados();
        $comissao = new Comissao();

        $antecipacao = $comissao->find("usuario = 1 AND descricao LIKE '%Taxa antecipação%' AND data_pedido BETWEEN $periodo")->fetch(true);

        // var_dump($vendas);
        // SELECT SUM(vendas.taxa_redrocket), usuario.nome FROM `vendas`, usuario WHERE produtor = usuario.id GROUP BY produtor;
        // SELECT SUM(vendas.taxa_redrocket), usuario.nome FROM `vendas`, usuario WHERE produtor = usuario.id AND data_finalizada BETWEEN "2021-08-01" AND "2021-08-31" GROUP BY produtor 
        echo $this->view->render("relatorios/painel-nf", [
            "title" => "Painel RedRocket NF",
            "vendas" => @$vendas,
            "usuarioDados" => @$usuarioDados,
            "comissao" => @$comissao,
            "antecipacao" => @$antecipacao,
            "periodo" => @$periodo,
            "periodo2" => @$periodo2,
        ]);
    }
}

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
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;
use Source\Models\Produtos\Plano;

session_start();

class AppRelatoriosAfiliados
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

    public function vendasAfiliados($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("vendas_afiliados");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'vendas_afiliados'")->fetch();

        $filtros = $this->filtros();
        $somaVendas = (new Venda())->find($filtros . "afiliado = " . @$this->section->getId(), null, "SUM(comissao_afiliado) as soma")->fetch();
        $vendasCount = (new Venda())->find($filtros)->count();
        if (@$_POST["produto"])
            $afiliacao = (new Afiliacao())->find("produto = " . $_POST["produto"])->fetch(true);
        $usuarioDados = (new UsuarioDados());
        $vendas = (new Venda());

        if ($filtros) {
            $somaVendas = (new Venda())->find($filtros . " AND afiliado IS NOT NULL", null, "SUM(valor_liquido) as soma")->fetch();
            $vendasCount = (new Venda())->find($filtros . " AND afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find($filtros . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($filtros . " AND forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find($filtros . " AND forma_pagamento = 3")->count();
        } else {
            $somaVendas = (new Venda())->find("afiliado IS NOT NULL", null, "SUM(valor_liquido) as soma")->fetch();
            $vendasCount = (new Venda())->find("afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find("forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find("forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find("forma_pagamento = 3")->count();
        }

        if ($filtros) {
            $vendas = new Venda();
            $usuario = $this->section->getId();
            $produto = @$_POST["produto"];
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";

            if ($this->section->getTipo() == 4) {
                $produtor = "afiliado = $usuario";
                $soma = "SUM(comissao_afiliado) as valor";
            } else {
                $produtor = "(produtor = $usuario OR afiliado = $usuario)";
                $soma = "SUM(valor_liquido) as valor";
            }

            if (@$_POST["produto"] && @$_POST["inicio"] && @$_POST["fim"]) {
                $vendasDia = $vendas->find(
                    "
                    $produtor AND 
                    produto = $produto and 
                    data_pedido BETWEEN $periodo 
                    GROUP BY data_pedido",
                    null,
                    "DISTINCT data_pedido as dia, 
                    $soma
                "
                )->fetch(true);
            }


            $_SESSION["busca"] = $filtros;

            if ($filtros)
                $filtros .= " AND ";

            $dados["totalVendas"] = $vendas->find($filtros . "afiliado IS NOT NULL")->count();
            $valorVendas1 = $vendas->find($filtros . "afiliado IS NOT NULL AND status_transacao = 4", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasPagas"] = @$valorVendas1->valor;

            ####
            $dados["cartoes"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorCartao"] = @$valorCartao1->valor;

            $dados["cartoesPagos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorCartaoPago"] = @$valorCartao1->valor;

            $dados["cartoesCancelados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorCartaoCancelado"] = @$valorCartao1->valor;

            $dados["cartoesOutros"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["cartoesReembolsos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["boletos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorBoleto"] = @$valorCartao1->valor;

            $dados["boletosPagos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorBoletoPago"] = @$valorCartao1->valor;

            $dados["boletosCancelados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorBoletoCancelado"] = @$valorCartao1->valor;

            $dados["boletosOutros"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["boletosReembolsos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["reembolsos"] = $vendas->find($filtros . "status_transacao = 6")->count();
            $valorReembolso1 = $vendas->find($filtros . "status_transacao = 6", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorReembolso"] = @$valorReembolso1->valor;

            $dados["chargeback"] = $vendas->find($filtros . "status_transacao = 7")->count();
            $valorChargeback1 = $vendas->find($filtros . "status_transacao = 7", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorChargeback"] = @$valorChargeback->valor;

            $dados["upsell"] = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL")->count();
            $valorUpsell1 = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorUpsell"] = @$valorUpsell->valor;

            //$todasVendasProdutor = $vendas->find($filtros . "produtor = $usuario")->fetch(true);
            //$todasVendasAfiliado = $vendas->find($filtros . "afiliado = $usuario")->fetch(true);

            ####
            $dados["pix"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3")->count();
            $valorPix1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorPix"] = @$valorPix1->valor;

            $dados["pixPagos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorPix1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorPixPago"] = @$valorPix1->valor;

            $dados["pixCancelados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 5)")->count();
            $valorPix1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorPixCancelado"] = @$valorPix1->valor;

            $dados["pixOutros"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["pixReembolsos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 6 || status_transacao = 7)")->count();

            $dados["vendasAfiliados"] = $vendas->find($filtros . "afiliado IS NOT NULL")->count();
            $dados["vendasDiretas"] = $vendas->find($filtros . "afiliado IS NULL")->count();

            $valorVendasDiretas = $vendas->find($filtros . "afiliado IS NOT NULL AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasDiretas"] = @$valorVendasDiretas->valor;
            $valorVendasAfiliados = $vendas->find($filtros . "afiliado IS NOT NULL AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasAfiliados"] = @$valorVendasAfiliados->valor;
        }

        $produtos = new Produto();

        echo $this->view->render("relatorios/relatorios_afiliados", [
            "title" => "Vendas Afiliados",
            "section" => @$section,
            "permissao" => @$permissao,
            "vendasD" => @$vendasDia,
            "dados" => @$dados,
            "produtos" => $produtos,
            "count" => $vendasCount,
            "afiliacao" => @$afiliacao,
            "somaVendas" => @$somaVendas,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
            "vendasPix" => $vendasPix,
            "usuarioDados" => $usuarioDados,
            "vendas" => $vendas,
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

            $sql[] = "(data_finalizada BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";
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

    public function performance($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("performance_afiliados");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'performance_afiliados'")->fetch();

        $filtros = $this->filtros();
        $somaVendas = (new Venda())->find($filtros . "afiliado = " . @$this->section->getId(), null, "SUM(comissao_afiliado) as soma")->fetch();
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
            $vendasPeriodo = (new Venda())->find($filtros . " AND status_transacao = 4 AND afiliado IS NOT NULL")->fetch(true);
            $planoMaior = (new Venda())->find($filtros . " AND status_transacao = 4 AND afiliado IS NOT NULL GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
        } else {
            $somaVendas = (new Venda())->find("afiliado IS NOT NULL", null, "SUM(comissao_afiliado) as soma")->fetch();
            $vendasCount = (new Venda())->find("afiliado IS NOT NULL")->count();
            $vendasBoleto = (new Venda())->find("forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find("forma_pagamento = 1")->count();
            $vendasPix = (new Venda())->find("forma_pagamento = 3")->count();
            $vendasPeriodo = (new Venda())->find("status_transacao = 4 AND afiliado IS NOT NULL")->fetch(true);
            $planoMaior = (new Venda())->find("status_transacao = 4 AND afiliado IS NOT NULL GROUP BY plano", NULL, "COUNT(plano) as total, plano")->order("total DESC")->fetch(true);
        }

        if ($filtros) {

            $this->sessao = (new AppSessao())->verificaSeLogado();

            $filtros = $this->filtros();

            $vendas = new Venda();
            $usuario = $this->section->getId();
            $produto = @$_POST["produto"];

            if (@$_POST["inicio"] && @$_POST["fim"]) {
                $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";
                $periodo2 = "'" . @$_POST["inicio"] . " 00:00:00' AND '" . @$_POST["fim"] . " 23:59:59'";
            } else {
                $periodo = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
                $periodo2 = "'" . date("Y-m-01 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
            }

            if ((@$_POST["produto"]) && @$_POST["inicio"] && @$_POST["fim"]) {
                $vendasDia = $vendas->find(
                    "
                    produto = $produto and 
                    data_pedido BETWEEN $periodo AND afiliado IS NOT NULL AND status_transacao=4
                    GROUP BY data_pedido",
                    null,
                    "DISTINCT data_pedido as dia, 
                    SUM(valor_redrocket) as valor
                "
                )->fetch(true);
            } elseif (@$_POST["produto"]) {
                $vendasDia = $vendas->find(
                    "
                    produto = $produto AND afiliado IS NOT NULL AND status_transacao=4
                    GROUP BY data_pedido",
                    null,
                    "DISTINCT data_pedido as dia, 
                    SUM(valor_liquido) as valor
                "
                )->fetch(true);
            } elseif (@$_POST["inicio"] && @$_POST["fim"]) {
                $vendasDia = $vendas->find(
                    "
                    data_pedido BETWEEN $periodo AND afiliado IS NOT NULL AND status_transacao=4
                    GROUP BY data_pedido",
                    null,
                    "DISTINCT data_pedido as dia, 
                    SUM(valor_redrocket) as valor
                "
                )->fetch(true);
            } else {
                $vendasDia = $vendas->find(
                    "
                    data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' AND afiliado IS NOT NULL AND status_transacao=4
                    GROUP BY data_pedido",
                    null,
                    "DISTINCT data_pedido as dia, 
                    SUM(valor_redrocket) as valor
                "
                )->fetch(true);
            }


            // var_dump($vendasDia);

            $_SESSION["busca"] = $filtros;

            if ($filtros)
                $filtros .= " AND ";

            // $dados["estados"] = $vendas->find($filtros . "afiliado IS NOT NULL")->count();
            // $valorEstados = $vendas->find($filtros . "afiliado IS NOT NULL AND status_transacao = 4", null, "SUM(valor_liquido) as valor")->fetch();
            // $dados["valorEstados"] = @$valorEstados->valor;


            ####    

            $dados["totalVendas"] = $vendas->find($filtros . "afiliado IS NOT NULL")->count();
            $valorVendas1 = $vendas->find($filtros . "afiliado IS NOT NULL AND status_transacao = 4", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasPagas"] = @$valorVendas1->valor;

            ####
            $dados["cartoes"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorCartao"] = @$valorCartao1->valor;


            $dados["cartoesPagos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorCartaoPago"] = @$valorCartao1->valor;

            $dados["cartoesCancelados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorCartaoCancelado"] = @$valorCartao1->valor;

            $dados["cartoesOutros"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["cartoesReembolsos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 1 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["boletos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorBoleto"] = @$valorCartao1->valor;

            $dados["boletosPagos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorBoletoPago"] = @$valorCartao1->valor;

            $dados["boletosCancelados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorBoletoCancelado"] = @$valorCartao1->valor;

            $dados["boletosOutros"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["boletosReembolsos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 2 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["reembolsos"] = $vendas->find($filtros . "status_transacao = 6")->count();
            $valorReembolso1 = $vendas->find($filtros . "status_transacao = 6", null, "SUM(valor_redrocket) as valor")->fetch();
            $dados["valorReembolso"] = @$valorReembolso1->valor;

            $dados["chargeback"] = $vendas->find($filtros . "status_transacao = 7")->count();
            $valorChargeback1 = $vendas->find($filtros . "status_transacao = 7", null, "SUM(valor_redrocket) as valor")->fetch();
            $dados["valorChargeback"] = @$valorChargeback->valor;

            $dados["upsell"] = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL")->count();
            $valorUpsell1 = $vendas->find($filtros . "status_transacao = 4 AND upsell IS NOT NULL", null, "SUM(valor_redrocket) as valor")->fetch();
            $dados["valorUpsell"] = @$valorUpsell->valor;

            //$todasVendasProdutor = $vendas->find($filtros . "produtor = $usuario")->fetch(true);
            //$todasVendasAfiliado = $vendas->find($filtros . "afiliado = $usuario")->fetch(true);

            ####
            $dados["pix"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3")->count();
            $valorPix1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorPix"] = @$valorPix1->valor;

            $dados["pixPagos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorPix1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorPixPago"] = @$valorPix1->valor;

            $dados["pixCancelados"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 5)")->count();
            $valorPix1 = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorPixCancelado"] = @$valorPix1->valor;

            $dados["pixOutros"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["pixReembolsos"] = $vendas->find($filtros . "afiliado IS NOT NULL AND forma_pagamento = 3 AND (status_transacao = 6 || status_transacao = 7)")->count();

            $dados["vendasAfiliados"] = $vendas->find($filtros . "afiliado IS NOT NULL")->count();
            $dados["vendasDiretas"] = $vendas->find($filtros . "afiliado IS NULL")->count();

            $valorVendasDiretas = $vendas->find($filtros . "afiliado IS NOT NULL AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasDiretas"] = @$valorVendasDiretas->valor;
            $valorVendasAfiliados = $vendas->find($filtros . "afiliado IS NOT NULL AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $dados["valorVendasAfiliados"] = @$valorVendasAfiliados->valor;
        }

        $produtos = new Produto();

        echo $this->view->render("relatorios/performance_afiliados", [
            "title" => "Performance Afiliados",
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
        ]);
    }
}

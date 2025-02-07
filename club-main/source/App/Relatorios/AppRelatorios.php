<?php

namespace Source\App\Relatorios;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Saldo;
use Source\Models\Comissao\Banco;
use Source\Models\Vendas\Venda;
use Source\Models\Afiliados\Afiliacao;

session_start();

class AppRelatorios
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    public function relatorios($data)
    {
        $filtros = $this->filtros();

        if($filtros){
            $vendas = new Venda();
            $usuario = $_SESSION["id"];
            $produto = @$_POST["produto"];
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";

            if($_SESSION["tipo"] == 4){
                $produtor = "afiliado = $usuario";
                $soma = "SUM(comissao_afiliado) as valor";
            } else {
                $produtor = "(produtor = $usuario OR afiliado = $usuario)";
                $soma = "SUM(valor_liquido) as valor";
            }

            if(@$_POST["produto"] && @$_POST["inicio"] && @$_POST["fim"]){
                $vendasDia = $vendas->find("
                    $produtor AND 
                    produto = $produto and 
                    data_pedido BETWEEN $periodo 
                    GROUP BY data_pedido", 
                    null, 
                    "DISTINCT data_pedido as dia, 
                    $soma
                ")->fetch(true);
            }

            if($filtros)
                $filtros .= " AND ";

            $dados["totalVendas"] = $vendas->find($filtros . "(produtor = $usuario OR afiliado = $usuario)")->count();

            $valorVendas1 = $vendas->find($filtros . "produtor = $usuario", null, "SUM(valor_liquido) as valor")->fetch();
            $valorVendas2 = $vendas->find($filtros . "afiliado = $usuario", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorVendas"] = @$valorVendas1->valor + @$valorVendas2->valor;

            ####
            $dados["cartoes"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1")->count();
            $valorCartao1 = $vendas->find($filtros. "produtor = $usuario AND forma_pagamento = 1", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros. "afiliado = $usuario AND forma_pagamento = 1", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorCartao"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["cartoesPagos"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros. "produtor = $usuario AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros. "afiliado = $usuario AND forma_pagamento = 1 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorCartaoPago"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["cartoesCancelados"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros. "produtor = $usuario AND forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros. "afiliado = $usuario AND forma_pagamento = 1 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorCartaoCancelado"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["cartoesOutros"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["cartoesReembolsos"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 1 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["boletos"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2")->count();
            $valorCartao1 = $vendas->find($filtros. "produtor = $usuario AND forma_pagamento = 2", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros. "afiliado = $usuario AND forma_pagamento = 2", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorBoleto"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["boletosPagos"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)")->count();
            $valorCartao1 = $vendas->find($filtros. "produtor = $usuario AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros. "afiliado = $usuario AND forma_pagamento = 2 AND (status_transacao = 4 OR status_transacao = 8)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorBoletoPago"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["boletosCancelados"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 5)")->count();
            $valorCartao1 = $vendas->find($filtros. "produtor = $usuario AND forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorCartao2 = $vendas->find($filtros. "afiliado = $usuario AND forma_pagamento = 2 AND (status_transacao = 5)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorBoletoCancelado"] = @$valorCartao1->valor + @$valorCartao2->valor;

            $dados["boletosOutros"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 1 || status_transacao = 2 || status_transacao = 3 || status_transacao = 9)")->count();
            $dados["boletosReembolsos"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND forma_pagamento = 2 AND (status_transacao = 6 || status_transacao = 7)")->count();

            ####
            $dados["reembolsos"] = $vendas->find($filtros. "(produtor = $usuario OR afiliado = $usuario) AND (status_transacao = 6 OR status_transacao = 7)")->count();
            $valorReembolso1 = $vendas->find($filtros. "produtor = $usuario AND (status_transacao = 6 OR status_transacao = 7)", null, "SUM(valor_liquido) as valor")->fetch();
            $valorReembolso2 = $vendas->find($filtros. "afiliado = $usuario AND (status_transacao = 6 OR status_transacao = 7)", null, "SUM(comissao_afiliado) as valor")->fetch();
            $dados["valorReembolso"] = @$valorReembolso1->valor + @$valorReembolso2->valor;

            //$todasVendasProdutor = $vendas->find($filtros . "produtor = $usuario")->fetch(true);
            //$todasVendasAfiliado = $vendas->find($filtros . "afiliado = $usuario")->fetch(true);

        }

        $produtos = new Produto();
        $afiliacao = new Afiliacao();

        echo $this->view->render("relatorios/relatorios", [
            "title" => "Relatórios de Vendas",
            "vendas" => @$vendasDia,
            "dados" => @$dados,
            "produtos" => $produtos,
            "afiliacao" =>$afiliacao,
        ]);
    }

    public function filtros()
    {
        $produto = @$_POST["produto"];
        $inicio = @$_POST["inicio"];
        $fim = @$_POST["fim"];
        $sql = array();

        if($produto != "") {
            $sql[] = "produto = '{$produto}'";
        }

        if($inicio != "" || $fim !=""){
            if($inicio == "")
                $inicio = date("Y-m-d");

            if($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "(data_pedido BETWEEN '{$inicio}' AND '{$fim}' OR data_finalizada BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";
        }

        $result = count($sql);
        
        for($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND ";
        }
        
        return implode(" ", $sql);
    }

    public function buscaRelatorios($data)
    {
        $vendas = new Venda();
        $usuario = $_SESSION["id"];
        $produto = $data["produto"];
        $periodo = "'" . $data["inicio"] . "' AND '" . $data["fim"] . "'";

        $vendasDia = $vendas->find("
            (produtor = $usuario OR afiliado = $usuario) AND 
            produto = $produto and 
            data_pedido BETWEEN $periodo 
            GROUP BY data_pedido", 
            null, 
            "DISTINCT data_pedido as dia, 
            SUM(valor_bruto) as valor
        ")->fetch(true);

        $i = 1;
        foreach($vendasDia as $venda){
            //var_dump($venda->dia, $venda->valor);
            $dados["grafico"][$i] = array($venda->dia, $venda->valor);
            $i++;
        }

        echo json_encode($dados);
        
    }
    public function exportar($data)
    {
        //var_dump($_SESSION);
        if(@$_GET["tabela"] && $_GET["tabela"] == "vendas"){
            $arquivo = "vendas.xls";
            echo $_SESSION["tabelaProduto"] . "<br><br>" . $_SESSION["tabelaDias"];
        }

        if(@$_GET["tabela"] && $_GET["tabela"] == "analitico"){
            $arquivo = "analitico.xls";
            echo $_SESSION["tabelaProduto"] . "<br><br>" . $_SESSION["tabelaAnalitico"];
        }

        header("Cache-Control: no-cache, must-revalidate");
        header ('Pragma: no-cache');
        header('Content-Type: application/x-msexcel');
        header ("Content-Disposition: attachment; filename=\"{$arquivo}\"");

    }

    public function painelRedrocket($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $filtros = $this->filtros();
        
        $vendas = new Venda();
        $usuario = $_SESSION["id"];
        $produto = @$_POST["produto"];

        if(@$_POST["inicio"] && @$_POST["fim"]){
            $periodo = "'" . @$_POST["inicio"] . "' AND '" . @$_POST["fim"] . "'";
            $periodo2 = "'" . @$_POST["inicio"] . " 00:00:00' AND '" . @$_POST["fim"] . " 23:59:59'";
        }
        else {
            $periodo = "'" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "'";
            $periodo2 = "'" . date("Y-m-01 00:00:00") . "' AND '" . date("Y-m-d 23:59:59") . "'";
        }

        if(@$_POST["produto"] && @$_POST["inicio"] && @$_POST["fim"]){
            $vendasDia = $vendas->find("
                produto = $produto and 
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido", 
                null, 
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            ")->fetch(true);
        } elseif(@$_POST["inicio"] && @$_POST["fim"]) {
            $vendasDia = $vendas->find("
                data_pedido BETWEEN $periodo 
                GROUP BY data_pedido", 
                null, 
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            ")->fetch(true);
        }else {
            $vendasDia = $vendas->find("
                data_pedido BETWEEN '" . date("Y-m-01") . "' AND '" . date("Y-m-d") . "' 
                GROUP BY data_pedido", 
                null, 
                "DISTINCT data_pedido as dia, 
                SUM(valor_redrocket) as valor
            ")->fetch(true);
        }

        $dados["totalVendas"] = $vendas->find($filtros)->count();

        $valorVendas1 = $vendas->find($filtros, null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorVendas"] = @$valorVendas1->valor;

        if($filtros)
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
        $dados["reembolsos"] = $vendas->find($filtros . "status_transacao = 6 OR status_transacao = 7")->count();
        $valorReembolso1 = $vendas->find($filtros . "status_transacao = 6 OR status_transacao = 7", null, "SUM(valor_redrocket) as valor")->fetch();
        $dados["valorReembolso"] = @$valorReembolso1->valor;

        function juros($valor, $parcela){
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

        if(@$_GET["meus"]){
            $busca = "usuario = " . $_SESSION["id"];
        }

        $comissaoCount = (new Comissao())->find(@$busca)->count();
        $paginas = ceil($comissaoCount / $offset);
        $comissao = (new Comissao())->find(@$busca)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        
        $usuario = $_SESSION["id"];
        
        if($_POST){
            $jurosCartao = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_juros) as valor")->fetch();
            $liquido = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_liquido) as valor")->fetch();
            $vendas = (new Venda())->find("status_transacao = 4 AND forma_pagamento = 1 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'")->fetch(true);
            $mes = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_bruto) as valor")->fetch();
            $valorRedrocket = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " 23:59:59") . "'", null, "SUM(valor_redrocket) as valor")->fetch();
            $saqueMes = (new Comissao())->find("tipo = 2 AND created_at BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
            $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND created_at BETWEEN '" . date($_POST["inicio"] . " 00:00:00") . "' AND '"  . date($_POST["fim"] . " H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        } else {
            $jurosCartao = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_juros) as valor")->fetch();
            $liquido = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_liquido) as valor")->fetch();
            $vendas = (new Venda())->find("status_transacao = 4 AND forma_pagamento = 1 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'")->fetch(true);
            $mes = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_bruto) as valor")->fetch();
            $valorRedrocket = (new Venda())->find("status_transacao = 4 AND data_finalizada BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d 23:59:59") . "'", null, "SUM(valor_redrocket) as valor")->fetch();
            $saqueMes = (new Comissao())->find("tipo = 2 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
            $saqueOcta = (new Comissao())->find("tipo = 2 AND usuario = 1 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        }

        if(@$vendas){
            // $j = 1;
            foreach ($vendas as $venda) {
                @$valorJuros += juros($venda->valor_bruto, $venda->parcelas);
                // var_dump($j, $venda->transacao, juros($venda->valor_bruto, $venda->parcelas));
                // $j++;
            }
        }
        $countBoletos = (new Venda())->find("status_transacao = 4 AND forma_pagamento = 2 AND data_finalizada BETWEEN $periodo2")->count();
        $wirecard = @$valorJuros + (2.79 * $countBoletos);
        
        $produtos = new Produto();
        $pendente = (new Comissao())->find("usuario = 1 AND disponivel = 0", null, "SUM(valor) as valor")->fetch();
        $saldo = (new Saldo())->find("usuario = 1")->fetch();

        echo $this->view->render("relatorios/painel", [
            "title" => "Painel RedRocket",
            "hoje" => @$hoje->valor,
            "wirecard" => $wirecard,
            "mes" => @$mes->valor,
            "redrocket" => @$valorRedrocket->valor,
            "juros" => @$jurosCartao->valor,
            "liquido" => @$liquido,
            "saldo" => @$saldo->valor,
            "vendas" => @$vendasDia,
            "dados" => @$dados,
            "produtos" => $produtos,
            "saldo" => @$saldo->valor,
            "pendente" => $pendente->valor,
            "saqueMes" => $saqueMes->valor,
            "saqueOcta" => $saqueOcta->valor,
        ]);
    }
    
}

<?php

namespace Source\App\Vendas;

session_start();

use League\Plates\Engine;
use Source\Models\Vendas\Venda;
use Source\Models\Produtos\Produto;
use Source\Helpers\Paginador;
use Source\App\Sessao\AppSessao;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Comissao\Comissao;

class AppVendas
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

    public function vendas($data)
    {
        $filtros = $this->filtros();
        //var_dump($filtros);

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        if(@$_SESSION["tipo"] == 4){
            if($filtros)
                $filtros .= " AND ";

            $busca = $filtros . "(produtor = " . @$_SESSION["id"] . " OR afiliado = " . @$_SESSION["id"] . ")";

            $soma1 = (new Venda())->find($filtros . "produtor = " . @$_SESSION["id"], null, "SUM(valor_liquido) as soma")->fetch();
            $soma2 = (new Venda())->find($filtros . "afiliado = " . @$_SESSION["id"], null, "SUM(comissao_afiliado) as soma")->fetch();
            
            @$somaVendas->soma = @$soma1->soma + @$soma2->soma;
        }
        else if(@$_SESSION["tipo"] == 5){
            if($filtros)
                $filtros .= " AND ";

            $busca = $filtros . "afiliado = " . @$_SESSION["id"];
            $somaVendas = (new Venda())->find($filtros . "afiliado = " . @$_SESSION["id"], null, "SUM(comissao_afiliado) as soma")->fetch();
        }
        else if(@$_SESSION["tipo"] == 6){
            if($filtros)
                $filtros .= " AND ";

            if(@$_SESSION["idU"])
            $busca = $filtros . "comprador = " . @$_SESSION["idU"];
            else 
            $busca = $filtros . "comprador = 0";

            @$somaVendas->soma = "-";
        }
        else {
            $busca = $filtros;
            $somaVendas = (new Venda())->find($filtros, null, "SUM(valor_liquido) as soma")->fetch();
        }

        if(@$_GET["minhas"] == "true"){
            if($filtros)
                $filtros .= " AND ";
            $busca = $filtros . "comprador = '" . @$_SESSION["idU"] . "'";
            $somaVendas = (new Venda())->find($busca, null, "SUM(valor_liquido) as soma")->fetch();
        }
        

        //echo "id u ".$_SESSION["idU"];var_dump($busca);

        $vendasCount = (new Venda())->find($busca)->count();

        if($busca){
            $vendasBoleto = (new Venda())->find($busca . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($busca . " AND forma_pagamento = 1")->count();
        } else {
            $vendasBoleto = (new Venda())->find("forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find("forma_pagamento = 1")->count();
        }

        $paginas = ceil($vendasCount / 30);
        $vendas = (new Venda())->find($busca)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("data_ultimo_evento DESC")->fetch(true);
        $produtos = new Produto();
        $afiliacao = new Afiliacao();
        $comissoes = new Comissao();
        //var_dump($vendasCount);

        echo $this->view->render("vendas/vendas", [
            "title" => "Vendas",
            "vendas" => $vendas,
            "produtos" => $produtos,
            "afiliacao" =>$afiliacao,
            "comissoes" => @$comissoes,
            "count" => $vendasCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "somaVendas" => $somaVendas,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
        ]);
    }

    public function vendasCoprodutor($data)
    {
        $filtros = $this->filtros();
        //var_dump($filtros);

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        if ($filtros) {
            $filtros .= " AND ";
        }

        $usuario = $_SESSION["id"];
        $busca = $filtros . "coprodutores LIKE '%/$usuario/%'";

        $vendasCount = (new Venda())->find($busca)->count();

        if($busca){
            $vendasBoleto = (new Venda())->find($busca . " AND forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find($busca . " AND forma_pagamento = 1")->count();
        } else {
            $vendasBoleto = (new Venda())->find("forma_pagamento = 2")->count();
            $vendasCartao = (new Venda())->find("forma_pagamento = 1")->count();
        }

        $paginas = ceil($vendasCount / 30);
        $vendas = (new Venda())->find($busca)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("data_ultimo_evento DESC")->fetch(true);
        //$somaVendas = (new Venda())->find($busca, null, "SUM(valor_liquido) as soma")->fetch();
        
        $vendasConta = (new Venda())->find($busca)->order("data_ultimo_evento DESC")->fetch(true);
        $coprodutor = new CoProdutor();
        foreach ($vendasConta as $valor) {
            $coproducao = (new CoProdutor())->find("co_produtor = $usuario and produto = $valor->produto")->fetch();
            
            if (@$coproducao->gerente == 0) {
                if (@$coproducao->tipo_comissao == 0) {
                    @$somaVendas->soma += $valor->valor_liquido * ($coproducao->comissao / 100);
                    //echo "$valor->valor_liquido  @$coproducao->comissao| " .@$somaVendas->soma . " - ";
                } else {
                    @$somaVendas->soma += $coproducao->comissao;
                }
            } else {
                if ($valor->afiliado) {
                    if ($coproducao->tipo_comissao_afiliados == 0) {
                        @$somaVendas->soma += $valor->valor_liquido * ($coproducao->comissao_afiliados / 100);
                    } else {
                        @$somaVendas->soma += $coproducao->comissao_afiliados;
                    }
                } else {
                    if ($coproducao->tipo_comissao == 0) {
                        @$somaVendas->soma += $valor->valor_liquido * ($coproducao->comissao / 100);
                    } else {
                        @$somaVendas->soma += $coproducao->comissao;
                    }
                }
            }
        }
        
        //var_dump($vendasCount);

        $produtos = new Produto();
        $afiliacao = new Afiliacao();

        echo $this->view->render("vendas/vendas", [
            "title" => "Suas vendas de CoProdutor",
            "vendas" => $vendas,
            "produtos" => @$produtos,
            "afiliacao" => @$afiliacao,
            "count" => $vendasCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "somaVendas" => @$somaVendas,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
            "coprodutor" => $coprodutor,
        ]);
    }

    public function filtros()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];
        $venda = @$_GET["venda"];
        $status = @$_GET["status"];
        $produto = @$_GET["produto"];
        $comprador = @$_GET["comprador"];
        $pagamento = @$_GET["pagamento"];
        $sql = array();

        if($inicio != "" || $fim !=""){
            if($inicio == "")
                $inicio = date("Y-m-d");

            if($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "(created_at BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59' OR data_finalizada BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";
        }

        if($venda != "") {
            $sql[] = "transacao LIKE '%$venda%' OR id = '$venda'";
        }

        if($status != "") {
            $sql[] = "status_transacao = $status";
        }

        if($produto != "") {
            $sql[] = "produto = $produto";
        }

        if($comprador != "") {
            $sql[] = "comprador IN ( SELECT usuario_dados.id FROM usuario_dados WHERE nome LIKE '%$comprador%' OR cpf_cnpj = '$comprador' OR email LIKE '%$comprador%')";
        }

        if($pagamento != "") {
            $sql[] = "forma_pagamento = $pagamento";
        }

        $result = count($sql);
        
        for($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }
        
        return implode(" ", $sql);
        
    }

    public function motivoCancelamento($data)
    {
        $venda = (new Venda())->findById($data["id"]);
        $venda->motivo = @$data["motivo"];

        if ($venda->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $venda->fail()->getMessage();
        }
    }
}
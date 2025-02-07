<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\Geral;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Saldo;
use Source\Models\Vendas\Venda;

session_start();

class AppWeb
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

    public function painel($data)
    {
        $comissoes = new Comissao();

        $user = $_SESSION["tipo"] == 1 ? "" : " AND usuario = " . $_SESSION["id"];
        $usuario = $_SESSION["id"];

        $mesAtual = "'" .date("Y-m-01") . "' AND '" . date("Y-m-31") . "'";
        $mesAnterior = "'" .date("Y-m-01", strtotime("-1 month")) . "' AND '" . date("Y-m-31", strtotime("-1 month")) . "'";

        $hoje = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d") . "'", null, "SUM(valor) as valor")->fetch();
        $ontem = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d", strtotime("-1 day")) . "'", null, "SUM(valor) as valor")->fetch();
        $mes = $comissoes->find("usuario = $usuario AND data_pedido BETWEEN $mesAtual", null, "SUM(valor)")->fetch();
        $pendente = $comissoes->find("(antecipado = 1 OR antecipado = 0) and disponivel = 0 and tipo = 1 and usuario = $usuario", null, "SUM(valor)")->fetch();
        $comissao = $comissoes->find("AND tipo = 1 and usuario = " . $_SESSION["id"])->limit(7)->fetch(true);
        $antecipado = (new Comissao())->find("usuario = $usuario AND disponivel = 0", null, "SUM(antecipado)")->fetch();
        $saldo = (new Saldo())->find("usuario = " . $_SESSION["id"])->fetch();
        $indicacao = (new UsuarioPremium())->find("usuario = $usuario")->count();

        $vendasAtual = (new Venda())->find("(afiliado = $usuario OR produtor = $usuario) AND status_transacao = 4 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasAtualSoma = (new Venda())->find("(produtor = $usuario) AND status_transacao = 4 AND created_at BETWEEN $mesAtual", null, "SUM(valor_liquido) as soma")->fetch();

        $vendasTotal = (new Venda())->find("status_transacao = 4 AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();
        $vendasTotalSoma = (new Venda())->find("status_transacao = 4 AND created_at BETWEEN $mesAtual", null, "SUM(valor_bruto) as soma")->fetch();

        $comissaoAtual = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido BETWEEN $mesAtual GROUP BY tempo_saque", null, "DISTINCT data_pedido as dia, sum(valor) as valor")->fetch(true);
        $comissaoAnterior = $comissoes->find("tipo = 1 AND usuario = $usuario AND data_pedido BETWEEN $mesAnterior GROUP BY tempo_saque", null, "DISTINCT data_pedido as dia, sum(valor) as valor")->fetch(true);

        foreach($mes->data() as $mes) null;
        foreach($pendente->data() as $pendente) null;
        foreach($antecipado->data() as $antecipado) null;

        echo $this->view->render("home_users", [
            "title" => "RedRocket",
            "hoje" => @$hoje->valor,
            "ontem" => @$ontem->valor,
            "mes" => @$mes,
            "pendente" => @$pendente,
            "antecipado" => @$antecipado,
            "comissao" => @$comissao,
            "saldo" => @$saldo,
            "indicacao" => $indicacao,
            "comissaoAtual" => $comissaoAtual,
            "comissaoAnterior" => $comissaoAnterior,
            "vendasAtual" => $vendasAtual,
            "vendasAtualSoma" => $vendasAtualSoma,
            "vendasTotal" => $vendasTotal,
            "vendasTotalSoma" => $vendasTotalSoma,
        ]);
    }

    public function linksUteis($data)
    {
        echo $this->view->render("links-uteis", [
            "title" => "Links Úteis"
        ]);
    }

    public function marketplace($data)
    {
        $paginador = new Paginador();
        $filtros = $this->filtros();
        if($filtros){
            $filtros .= " AND ";
        }

        $recente = @$_GET["recente"];
        if($recente)
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
            "title" => "Marketplace RedRocket",
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
        $sql = array();

        if($nome != "") {
            $sql[] = "nome LIKE '%{$nome}%'";
        }

        if($categoria != "") {
            $sql[] = "categoria = {$categoria}";
        }

        if($comissao != "") {
            $sql[] = "tipo_atribuicao = {$comissao}";
        }

        if($tipo != "") {
            if($tipo == 8)
                $sql[] = "tipo_produto = {$tipo}";
            else
                $sql[] = "tipo_produto != {$tipo}";
        }

        $result = count($sql);
        
        for($i = 0; $i < $result - 1; $i++) {
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

    public function error(array $data):void
    {
        echo $this->view->render("error", [
            "title" => "Erro {$data['errcode']}",
            "error" => $data["errcode"]
        ]);
    }
}
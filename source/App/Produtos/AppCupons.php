<?php

//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Produtos;

session_start();

use League\Plates\Engine;
use Source\Models\Produtos\Produto;
use Source\App\Sessao\AppSessao;
use Source\Models\Cupons\Cupom;
use Source\Models\Produtos\Plano;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Emails;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;

class AppCupons
{
    private $view;
    private $pastaProduto;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->pastaProduto = PASTA . "produtos/";

        $this->section = new Section();
    }

    /**
     * SEÇÃO CUPONS
     */
    public function cupons($data)
    {
        
        (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("cupom_desconto");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'cupom_desconto'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $produto = (new Produto())->findById($data["codigo"]);
        $cuponsCount = (new Cupom())->find("produto = '{$data["codigo"]}'")->count();
        $paginas = ceil($cuponsCount / $offset);
        $cupons = (new Cupom())->find("produto = '{$data["codigo"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        $planos = (new Plano())->find("produto = '{$data["codigo"]}'")->fetch(true);
        $plano = (new Plano());

        echo $this->view->render("produtos/cupons", [
            "title" => "Cupom $produto->nome",
            "section" => @$section,
            "permissao" => @$permissao,
            "produto" => $produto,
            "planos" => $planos,
            "plano" => $plano,
            "cupons" => $cupons,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function cupomCadastro($data)
    {
        session_start();
        (new AppSessao())->verificaSeLogado();

        $resultado_bytes = random_bytes(4);
        $codigo = strtoupper(bin2hex($resultado_bytes));

        $cupons = new Cupom();

        $cupons->produto = @$data["produto"];
        $cupons->cupom = @$data["cupom"];
        // $cupons->multicompra = @$data["multicompra"];
        $cupons->definir_plano = @$data["definir_plano"];
        if (@$data["planos"])
            $cupons->planos = implode(",", @$data["planos"]);
        $cupons->porcentagem = @$data["porcentagem"];
        $cupons->valor_porcentagem = @$data["valor_porcentagem"];

        if (@$data["meio_pagamento"])
            $cupons->meio_pagamento = @implode(",", @$data["meio_pagamento"]);
        else
            $cupons->meio_pagamento = 10;

        $cupons->ativo = @$data["ativo"];
        $cupons->usuario = $_SESSION["id"];

        if ($cupons->save()) {
            echo "Cupom cadastrado com sucesso";
        } else {
            echo $cupons->fail()->getMessage();
        }
    }

    public function cupomEditar($data)
    {
        session_start();
        (new AppSessao())->verificaSeLogado();

        $cupons = (new Cupom())->findById($data["id"]);

        $cupons->produto = @$data["produto"];
        $cupons->cupom = @$data["cupom"];
        // $cupons->multicompra = @$data["multicompra"];
        $cupons->definir_plano = @$data["definir_plano"];

        if (@$data["planos"])
            $cupons->planos = implode(",", @$data["planos"]);
        if ($cupons->definir_plano == 0)
            $cupons->planos = NULL;
        // var_dump($data["planos"]);

        $cupons->porcentagem = @$data["porcentagem"];
        $cupons->valor_porcentagem = @$data["valor_porcentagem"];

        if (@$data["meio_pagamento"])
            $cupons->meio_pagamento = implode(",", @$data["meio_pagamento"]);
        else
            $cupons->meio_pagamento = 10;

        $cupons->ativo = @$data["ativo"];

        if ($cupons->save()) {
            echo "Cupom atualizado com sucesso";
        } else {
            echo $cupons->fail()->getMessage();
        }
    }

    public function cupomExcluir($data)
    {
        session_start();
        (new AppSessao())->verificaSeLogado();

        $cupons = (new Cupom())->findById($data["id"]);
        if ($cupons->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $cupons->fail()->getMessage();
        }
    }
    /**
     * FIM SEÇÃO CUPONS
     */

    public function cupomBuscar($data)
    {
        $cupom = (new Cupom())->find("ativo = 1 AND cupom = '" . $data['cupom'] . "' AND produto = " . $data['produto'])->fetch();

        $forma_pagamento = array(
            10 => " todos os meios de pagamento.",
            1  => " cartão de crédito,",
            2  => " boleto,",
            3  => " pix"
        );
        if ($cupom) {

            $dados["tipo"] = $cupom->porcentagem;
            $dados["valor"] = $cupom->valor_porcentagem;
            $dados["multicompra"] = $cupom->multicompra;
            $dados["meio_pagamento"] = $cupom->meio_pagamento;
            $meio_pagamento = explode(",", $cupom->meio_pagamento);
            $dados["resp"] = true;

            if ($cupom->multicompra == 0 && @$dados["compra"] == 1);

            if ($cupom->definir_plano == 0) {
                $dados["msg"] = "<span style='color: green'>Seu cupom será aplicado para";
                $i = 0;
                foreach ($meio_pagamento as $pagamento) {
                    $dados["msg"] .= $forma_pagamento[$meio_pagamento[$i]];
                    $i++;
                }
                $dados["msg"] .= "</span>";
            } else {
                $planos = explode(",", $cupom->planos);
                foreach ($planos as $plano) {
                    if ($data['plano'] == $plano) {
                        $cupomPlano = true;
                        break;
                    } else {
                        $cupomPlano = false;
                    }
                }

                if ($cupomPlano) {
                    $dados["msg"] = "<span style='color: green'>Cupom será aplicado ao plano </span>";
                } else {
                    $dados["msg"] = "<span style='color: red'>Cupom não se aplica para esse plano </span>";
                    $dados["resp"] = false;
                }
            }
        } else {
            $dados["msg"] = "<span style='color: red'>Cupom não encontrado! Verifique se digitou corretamente.</span>";
            $dados["resp"] = false;
        }

        echo json_encode($dados);
    }
}

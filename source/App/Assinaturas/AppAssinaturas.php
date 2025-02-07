<?php

//CLASSE QUE CONTROLA AS ROTAS DOS AFILIADOS

namespace Source\App\Assinaturas;

@session_start();

use League\Plates\Engine;

use Source\Models\Assinaturas\Assinatura;
use Source\Models\Assinaturas\AssinaturaCadastro;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Vendas\Venda;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\App\Sessao\AppSessao;
use Source\App\Assinaturas\AppPagamentoAssinaturas;
use Source\App\Postback\AppPostback;

use Source\Models\Configuracoes\Taxa;

use Source\Models\Cartao\Cartao;
use Source\Models\Cartao\CartaoNumero;

use Source\Helpers\Paginador;
use Source\Helpers\Emails;
use Source\Models\Comissao\Comissao;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;

class AppAssinaturas
{
    private $view;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->section = new Section();
    }

    public function assinaturas($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("assinaturas");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'assinaturas'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        $filtros = $this->filtros();

        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {
            $produtos = (new Produto())->find("tipo_cobranca = 2")->fetch(true);

            $assinaturasCount = (new Assinatura())->find($filtros)->count();
            $paginas = ceil($assinaturasCount / $offset);
            $assinaturas = (new Assinatura())->find($filtros)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
            $assinaturasValor = (new Assinatura())->find($filtros, null, "SUM(valor_plano) as valor")->fetch();
        } else {
            if ($filtros)
                $filtros .= " AND ";

            $produtos = (new Produto())->find("usuario = {$this->section->getId()} AND tipo_cobranca = 2")->fetch(true);

            $assinaturasCount = (new Assinatura())->find($filtros . "produtor = '{$this->section->getId()}'")->count();
            $paginas = ceil($assinaturasCount / $offset);
            $assinaturas = (new Assinatura())->find($filtros . "produtor = '{$this->section->getId()}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
            $assinaturasValor = (new Assinatura())->find($filtros . "produtor = '{$this->section->getId()}'", null, "SUM(valor_plano) as valor")->fetch();
        }

        echo $this->view->render("assinaturas/assinaturas", [
            "title" => "Assinaturas",
            "section" => @$section,
            "permissao" => @$permissao,
            "produtos" => $produtos,
            "assinaturas" => $assinaturas,
            "assinaturasValor" => $assinaturasValor,
            "count" => $assinaturasCount,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function filtros()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];
        $inicio_proxima = @$_GET["inicio_proxima"];
        $fim_proxima = @$_GET["fim_proxima"];
        $venda = @$_GET["venda"];
        $status = @$_GET["status"];
        $produto = @$_GET["produto"];
        $plano = @$_GET["plano"];
        $comprador = @$_GET["comprador"];
        $pagamento = @$_GET["pagamento"];
        $afiliado = @$_GET["afiliado"];
        $sql = array();

        if ($inicio != "" || $fim != "") {
            if ($inicio == "")
                $inicio = date("Y-m-d");

            if ($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "(created_at BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";
        }

        if ($inicio_proxima != "" || $fim_proxima != "") {
            if ($inicio_proxima == "")
                $inicio_proxima = date("Y-m-d");

            if ($fim_proxima == "")
                $fim_proxima = date("Y-m-d");

            $sql[] = "(proxima_cobranca BETWEEN '{$inicio_proxima} 00:00:00' AND '{$fim_proxima} 23:59:59')";
        }

        if ($venda != "") {
            $sql[] = "venda = '$venda'";
        }

        if ($status != "") {
            $sql[] = "status = $status";
        }

        if ($produto != "") {
            $sql[] = "produto = $produto";
        }

        if ($plano != "") {
            $sql[] = "plano = $plano";
        }

        if ($comprador != "") {
            $sql[] = "comprador IN ( SELECT usuario_dados.id FROM usuario_dados WHERE nome LIKE '%$comprador%' OR cpf_cnpj = '$comprador' OR email LIKE '%$comprador%')";
        }

        if ($pagamento != "") {
            $sql[] = "pagamento = $pagamento";
        }

        if ($afiliado != "") {
            $sql[] = "afiliado = $afiliado";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function extratoAssinaturas($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("assinaturas");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'assinaturas'")->fetch();

        $filtros = $this->filtros();

        $order = @$_GET["order"] ? @$_GET["order"] : "id";

        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {
            $produtos = (new Produto())->find("tipo_cobranca = 2")->fetch(true);

            $assinaturasCount = (new Assinatura())->find($filtros)->count();
            $assinaturas = (new Assinatura())->find($filtros)->order("id DESC")->order("$order DESC")->fetch(true);
            $assinaturasValor = (new Assinatura())->find($filtros, null, "SUM(valor_plano) as valor")->fetch();
        } else {
            if ($filtros)
                $filtros .= " AND ";

            $produtos = (new Produto())->find("usuario = {$_SESSION['id']} AND tipo_cobranca = 2")->fetch(true);

            $assinaturasCount = (new Assinatura())->find($filtros . "produtor = '{$this->section->getId()}'")->count();
            $assinaturas = (new Assinatura())->find($filtros . "produtor = '{$this->section->getId()}'")->order("id DESC")->order("$order DESC")->fetch(true);
            $assinaturasValor = (new Assinatura())->find($filtros . "produtor = '{$this->section->getId()}'", null, "SUM(valor_plano) as valor")->fetch();
        }

        $devendo = (new Comissao())->find("descricao LIKE '%Antecipação parcelas assinatura%' and disponivel = 0 AND usuario = " . $this->section->getId(), null, "SUM(valor) as devendo")->fetch();

        $vendaObj = new Venda();

        echo $this->view->render("assinaturas/extrato-assinaturas", [
            "title" => "Assinaturas",
            "section" => @$section,
            "permissao" => @$permissao,
            "produtos" => $produtos,
            "assinaturas" => $assinaturas,
            "assinaturasValor" => $assinaturasValor,
            "vendaObj" => $vendaObj,
            "devendo" => $devendo,
            "count" => $assinaturasCount,
        ]);
    }

    public function cadastroAssinaturas($data)
    {
        // @session_start();
        // $this->sessao = (new AppSessao())->verificaSeLogado();

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $assinatura = new Assinatura();

        $assinatura->venda                  = @$data["venda"];
        $assinatura->produto                = @$data["produto"];
        $assinatura->plano                  = @$data["plano"];
        $assinatura->produtor               = @$data["produtor"];
        $assinatura->comprador              = @$data["comprador"];
        $assinatura->endereco               = @$data["endereco"];
        $assinatura->recorrencia            = @$data["recorrencia"];
        $assinatura->proxima_cobranca       = @$data["proxima_cobranca"];
        $assinatura->data_limite            = @$data["data_limite"];
        $assinatura->valor_plano            = @$data["valor_plano"];
        $assinatura->status                 = 1;
        $assinatura->pagamento              = @$data["forma_pagamento"];
        $assinatura->coprodutores           = @$data["coprodutores"];
        $assinatura->comissao_coprodutor    = @$data["comissao_coprodutores"];
        $assinatura->afiliado               = @$data["afiliado"];
        $assinatura->comissao_afiliado      = @$data["comissao_afiliado_assinatura"];
        $assinatura->aceitar_afiliado       = @$data["aceitar_afiliado"];

        if ($assinatura->save()) {
            $venda = (new Venda())->findById($data["venda"]);
            $venda->assinatura = $assinatura->id;
            $venda->save();
            return "Salvo com sucesso";
        } else {
            return $assinatura->fail()->getMessage();
        }
    }

    public function verificarAssinaturas($data)
    {
        echo "<pre>";
        $assinaturas = (new AssinaturaCadastro())->find("status = 1")->fetch(true);
        if ($assinaturas) {
            foreach ($assinaturas as $assinatura) {
                $venda = (new Venda())->findById($assinatura->venda);

                if ($venda->dataProduto()->tipo_cobranca == 2 && $venda->status_transacao == 4) {
                    $plano = (new Plano())->findById($venda->plano);

                    $periodicidade = array(
                        "Semanal"       => [7, "days"],
                        "Mensal"        => [1, "months"],
                        "Bimestral"     => [2, "months"],
                        "Trimestral"    => [3, "months"],
                        "Semestral"     => [6, "months"],
                        "Anual"         => [1, "years"],
                        "Trienal"       => [3, "years"]
                    );

                    $data["recorrencia"] = $plano->periodo;
                    $limite = ($plano->maximo_recorrencia - 1) * @$periodicidade[$plano->periodo][0];

                    if ($plano->maximo_recorrencia > 0)
                        $data["data_limite"] = date('Y-m-d', strtotime("+$limite " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));

                    $proxima = @$periodicidade[$plano->periodo][0];
                    $data["proxima_cobranca"] = date('Y-m-d', strtotime("+$proxima " . @$periodicidade[$plano->periodo][1], strtotime(date('Y-m-d'))));
                    $data["valor_plano"] = $plano->preco;
                    $data["venda"] = $venda->id;
                    $data["produto"] = $venda->produto;
                    $data["plano"] = $venda->plano;
                    $data["produtor"] = $venda->produtor;
                    $data["endereco"] = $venda->endereco;
                    $data["comprador"] = $venda->comprador;
                    $data["forma_pagamento"] = $venda->forma_pagamento;
                    $data["coprodutores"] = $venda->coprodutores;
                    $data["comissao_coprodutores"] = $venda->comissao_coprodutores;
                    $data["afiliado"] = $venda->afiliado;
                    $data["comissao_afiliado"] = $venda->comissao_afiliado;
                    $data["assinatura"] = $this->cadastroAssinaturas($data);

                    if (@$data["assinatura"] == "Salvo com sucesso") {
                        $assinatura->status = 2;
                        $assinatura->save();
                    }
                    var_dump($data);
                }
            }
        } else {
            echo "Assinaturas já cadastradas";
        }
    }

    public function editarAssinatura($data)
    {
        @session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $assinatura = (new Assinatura())->findById($data["id"]);
        $assinatura->status = $data["status"];

        if ($data["status"] == 1)
            $assinatura->tentativas = 0;

        if ($assinatura->save()) {
            echo "Salvo com sucesso";
        } else {
            echo "Houve um erro na sua solicitação";
        }
    }

    public function pagamentosAssinaturas($data)
    {
        $hoje = date("Y-m-d");
        $assinaturas = (new Assinatura())->find("proxima_cobranca <= '$hoje' AND (data_limite > '$hoje' OR data_limite IS NULL) AND status = 1")->fetch(true);

        $pagamento = array(
            1 => "credit_card",
            2 => "boleto",
            3 => "pix"
        );

        echo "<pre>";

        $pagamentoAssinaturas = new AppPagamentoAssinaturas(null);
        $postback = new AppPostback(null);
        if ($assinaturas) {
            foreach ($assinaturas as $assinatura) {

                $produtor = (new Usuario())->findById($assinatura->produtor);
                $taxa = (new Taxa())->findById(1);
                $produto = (new Produto())->findById($assinatura->produto);

                // DADOS DO COMPRADOR
                $data["comprador"] = $assinatura->comprador;
                $data["nome"] = $assinatura->dataComprador()->nome;
                $data["email"] = $assinatura->dataComprador()->email;
                $data["tipo_doc"] = $assinatura->dataComprador()->tipo_doc;
                $data["cpf"] = $assinatura->dataComprador()->cpf_cnpj;
                $data["telefone"] = $assinatura->dataComprador()->telefone;
                $data["nasc"] = $assinatura->dataComprador()->data_nascimento;
                $data["cep"] = $assinatura->dataComprador()->cep;
                $data["rua"] = $assinatura->dataComprador()->rua;
                $data["numero"] = $assinatura->dataComprador()->numero;
                $data["complemento"] = $assinatura->dataComprador()->complemento;
                $data["bairro"] = $assinatura->dataComprador()->bairro;
                $data["cidade"] = $assinatura->dataComprador()->cidade;
                $data["estado"] = $assinatura->dataComprador()->estado;

                // DADOS PRODUTO
                $data["preco"] = $assinatura->valor_plano;
                $data["taxa_redrocket"] = $produtor->taxa_assinatura;
                $data["taxa_juros"] = $taxa->parcelamento;
                $data["valor_redrocket"] = number_format($assinatura->valor_plano * ($produtor->taxa_redrocket / 100), 2);
                $data["valor_liquido"] = number_format($assinatura->valor_plano - $data["valor_redrocket"], 2);
                $data["valor_produto"] = $assinatura->valor_plano;
                $data["parcela"] = 1;
                $data["nomekit"] = $assinatura->dataPlano()->nome;
                $data["nome_produto"] = $assinatura->dataProduto()->nome;
                $data["produto"] = $assinatura->produto;
                $data["plano"] = $assinatura->plano;
                $data["produtor"] = $assinatura->produtor;
                $data["url_checkout"] = null;
                $data["coprodutores"] = $assinatura->coprodutores;
                $data["comissao_coprodutores"] = $assinatura->comisssao_coprodutor;
                $data["afiliado"] = $assinatura->afiliado;
                $data["comissao_afiliado"] = $assinatura->comissao_afiliado;
                $data["st4rt"] = $assinatura->dataProduto()->st4rt;
                $data["assumir_juros"] = $assinatura->dataPlano()->assumir_juros;
                $data["pagamento"] = $pagamento[$assinatura->pagamento];
                $data["assinatura"] = $assinatura->id;

                if ($assinatura->pagamento == 1) {
                    $cartao_numero = (new CartaoNumero())->find("comprador = $assinatura->comprador AND venda = $assinatura->venda")->fetch();
                    if ($cartao_numero) {
                        $cartao = (new Cartao())->findById($cartao_numero->id);
                        $data["mes-val"] = base64_decode($cartao->mes);
                        $data["ano-val"] = base64_decode($cartao->ano);
                        $data["numcart"] = base64_decode($cartao_numero->numero);
                        $data["cvv"] = base64_decode($cartao->cvv);
                        $data["titular"] = $cartao->titular;
                    }
                }
                // var_dump($data);
                echo "**********************************************************<br>";
                echo "****     <b>Assinatura: $assinatura->id </b><br>";
                echo "**********************************************************<br>";

                $venda = $pagamentoAssinaturas->transacao($data);

                echo "***   <b>Dados venda<b><br>";
                var_dump($venda);

                echo "<br> ***   <b>JSON venda<b><br>";
                $venda = json_decode($venda);
                var_dump($venda);

                $postback1 = $postback->enviarPostback(["venda" => $venda->venda, "produto" => $venda->produto, "produtor" => $venda->produtor]);

                $ass = (new Assinatura())->findById($assinatura->id);

                $periodicidade = array(
                    "Semanal"       => [7, "days"],
                    "Mensal"        => [1, "month"],
                    "Bimestral"     => [2, "months"],
                    "Trimestral"    => [3, "months"],
                    "Semestral"     => [6, "months"],
                    "Anual"         => [1, "years"],
                    "Trienal"       => [3, "years"]
                );
                $proxima = $periodicidade[$assinatura->recorrencia][0];

                if ($venda->status_transacao == 5) {
                    $proxima_cobranca = date('Y-m-d', strtotime("+1 day", strtotime(date('Y-m-d'))));
                    echo "$proxima_cobranca - pagamento cancelado<br>";
                    $ass->proxima_cobranca = $proxima_cobranca;
                    $ass->tentativas = $assinatura->tentativas + 1;
                    if ($ass->tentativas >= 2)
                        $ass->status = 3;
                } else {
                    echo "$proxima - 2<br>";
                    $proxima_data = date('Y-m-d', strtotime("+$proxima {$periodicidade[$assinatura->recorrencia][1]}", strtotime(date($assinatura->proxima_cobranca))));
                    echo "$proxima_data - 2<br>";
                    $ass->proxima_cobranca = $proxima_data;
                    $ass->tentativas = 0;
                }

                if ($assinatura->data_limite != "" && $venda->status_transacao == 5 && $ass->tentativas > 3) {
                    $ass->data_limite = date('Y-m-d', strtotime("+$proxima {$periodicidade[$assinatura->recorrencia][1]}", strtotime(date($assinatura->data_limite))));
                    echo "data limite $ass->data_limite";
                }

                $ass->save();

                echo "<br> ***   <b>Dados assinatura<b><br>";
                var_dump($ass->data());
            }
        } else {
            echo "Nenhuma cobrança para hoje";
        }
    }
}

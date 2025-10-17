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
use Source\Models\Checkout\Checkout;
use Source\Models\CoProdutor\CoProdutor;
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
use Source\Models\Vendas\ManualSale;
use Source\Models\Vendas\ManualSaleItem;
use Source\Models\Vendas\ManualSaleFile;
use Source\Helpers\File;
use CoffeeCode\DataLayer\Connect;
use Source\App\Checkout1\BsPay;
use Source\App\Checkout1\Stripe;
use Exception;

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
        $assinatura->manual_sale_item_id    = @$data["manual_sale_item_id"];

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
                if ($assinatura->pagamento == 1) {
                    $venda = $pagamentoAssinaturas->transacao($data);
                } else {
                    $venda = $pagamentoAssinaturas->transacaoPix($data);
                }

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
                echo "$proxima - 1<br>";

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

    public function renderInputVenda($data)
{
    $this->sessao = (new AppSessao())->verificaSeLogado();

    $usuarioId = $_SESSION["id"];
    $produtos = (new Produto())->find("usuario = :uid", "uid={$usuarioId}")->order("nome ASC")->fetch(true);

    $planosPorProduto = [];
    $checkoutPorProduto = [];
    $taxaPorProduto = [];
    $valorRedrocketPorProduto = [];
    $valorLiquidoPorProduto = [];
    $comissaoProdutorPorProduto = [];
    $coprodutoresPorProduto = [];
    $comissaoCoprodutorPorProduto = [];
    $somaCoprodutorPorProduto = [];
    $taxaTransacaoPorProduto = [];
    $produtosInfo = [];

    foreach ($produtos as $produto) {
        $planos = (new Plano())->find("produto = :pid AND ativo = 1 AND privado = 0", "pid={$produto->id}")->fetch(true);

        $planosPorProduto[$produto->id] = array_map(function($plano) {
            return [
                'id' => $plano->id,
                'nome' => $plano->nome,
                'usuario' => $plano->usuario,
                'preco' => $plano->preco,
                'periodo' => $plano->periodo,
                'maximo_recorrencia' => $plano->maximo_recorrencia,
                'coprodutores' => $plano->coprodutores ?? null,
                'comissao_coprodutores' => $plano->comissao_coprodutores ?? null
            ];
        }, $planos ?: []);

        $checkout = (new Checkout())->find("produto = :pid", "pid={$produto->id}")->fetch();
        $checkoutPorProduto[$produto->id] = $checkout;

        $usuario = (new Usuario())->findById($usuarioId);
        $taxa = (new Taxa())->findById(1);

        if ($usuario && $usuario->taxa_redrocket) {
            $taxa_transacao = $produto->tipo_cobranca == 1 ? $usuario->taxa_redrocket : $usuario->taxa_assinatura;
        } else {
            $taxa_transacao = $produto->tipo_cobranca == 1 ? $taxa->taxa_redrocket : $taxa->taxa_assinatura;
        }
        if (@$_GET["ec"] && $usuario->assinatura_ecommerce == "taxa") {
            $taxa_transacao += $usuario->taxa_ecommerce;
        }
    
        $taxaTransacaoPorProduto[$produto->id] = $taxa_transacao;
        $taxa_redrocket = $taxa ? $taxa->taxa_redrocket : 0;
        $meio_pagamento_desconto = @$taxa->meio_pagamento_desconto ?? 0;

        $taxaPorProduto[$produto->id] = $taxa_redrocket;

        if (!empty($planos)) {
            $plano = $planos[0]; // assume primeiro plano como padrão
            $preco = $plano->preco;

            $valor_redrocket = number_format(($preco * ($taxa_redrocket / 100)) + 1, 2, ".", "");
            $valor_liquido = $preco - $valor_redrocket;

            // === INÍCIO: lógica coprodutores ===
            $usuario = (new Usuario())->findById($usuarioId);
            $valorOcta = number_format(($plano->preco * ($usuario->taxa_redrocket / 100)) + 1, 2);

            $coprodutores = "";
            $comissao_coprodutor = "";
            $somaCoprodutor = 0;

            $coproducao = (new CoProdutor())->find("produto = {$produto->id} AND aceito = 1")->fetch(true);

            if (@$plano->habilitar_comissao_coprodutores) {
                $cop = explode(",", $plano->comissao_coprodutores);
                if ($cop) {
                    foreach ($cop as $cop1) {
                        $cop2 = explode("|", $cop1);
                        $coprodutores .= "/$cop2[0]/";
                        if (@$cop2[1] == 0) {
                            $comissao_cop = number_format(($plano->preco - $valorOcta) * (@$cop2[2] / 100), 2);
                        } else {
                            $comissao_cop = number_format($cop2[2], 2);
                        }
                        $comissao_coprodutor .= "/$comissao_cop/";
                        $somaCoprodutor += $comissao_cop;
                    }
                }
            } elseif ($coproducao) {
                foreach ($coproducao as $coprodutor) {
                    $coprodutores .= "/{$coprodutor->co_produtor}/";
                    if ($coprodutor->tipo_comissao == 0) {
                        $comissao_cop = number_format(($plano->preco - $valorOcta) * ($coprodutor->comissao / 100), 2);
                    } else {
                        $comissao_cop = number_format($coprodutor->comissao, 2);
                    }
                    $comissao_coprodutor .= "/$comissao_cop/";
                    $somaCoprodutor += $comissao_cop;
                }
            }
            // === FIM: lógica coprodutores ===

            $comissao_afiliado = 0; // Se quiser calcular também.

            $resto = $valor_liquido - $somaCoprodutor - $comissao_afiliado;
            $comissao_produtor = max($resto, 0);

            $valorRedrocketPorProduto[$produto->id] = $valor_redrocket;
            $valorLiquidoPorProduto[$produto->id] = $valor_liquido;
            $comissaoProdutorPorProduto[$produto->id] = $comissao_produtor;

            $coprodutoresPorProduto[$produto->id] = $coprodutores;
            $comissaoCoprodutorPorProduto[$produto->id] = $comissao_coprodutor;
            $somaCoprodutorPorProduto[$produto->id] = $somaCoprodutor;
        } else {
            $valorRedrocketPorProduto[$produto->id] = 0;
            $valorLiquidoPorProduto[$produto->id] = 0;
            $comissaoProdutorPorProduto[$produto->id] = 0;
            $coprodutoresPorProduto[$produto->id] = "";
            $comissaoCoprodutorPorProduto[$produto->id] = "";
            $somaCoprodutorPorProduto[$produto->id] = 0;
        }

        $produtosInfo[] = [
            "id" => $produto->id,
            "nome" => $produto->nome,
            "controla_estoque" => (int)$produto->controla_estoque,
            "estoque_atual" => (int)$produto->estoque_atual
        ];
    }

    echo $this->view->render("assinaturas/input-venda", [
        "title" => "Criar Venda Manual",
        "produtos" => $produtos,
        "planosPorProduto" => $planosPorProduto,
        "checkoutPorProduto" => $checkoutPorProduto,
        "taxaPorProduto" => $taxaPorProduto,
        "taxaTransacaoPorProduto" => $taxaTransacaoPorProduto,
        "valorRedrocketPorProduto" => $valorRedrocketPorProduto,
        "valorLiquidoPorProduto" => $valorLiquidoPorProduto,
        "meio_pagamento_desconto" => $meio_pagamento_desconto,
        "comissaoProdutorPorProduto" => $comissaoProdutorPorProduto,
        "coprodutoresPorProduto" => $coprodutoresPorProduto,
        "comissaoCoprodutorPorProduto" => $comissaoCoprodutorPorProduto,
        "somaCoprodutorPorProduto" => $somaCoprodutorPorProduto,
        "produtosInfo" => $produtosInfo
    ]);
}

    public function criarVendaManual($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        header("Content-Type: application/json; charset=utf-8");

        $pdo = Connect::getInstance();
        $uploadedFiles = [];

        try {
            $payload = $_POST ?? [];

            $itemsPayload = $payload["items"] ?? "";
            $items = json_decode($itemsPayload, true);
            if (!is_array($items) || empty($items)) {
                throw new Exception("Adicione pelo menos um produto à venda manual.");
            }

            $parcelas = (int)($payload["parcelas"] ?? 1);
            if ($parcelas < 1 || $parcelas > 5) {
                throw new Exception("O número de parcelas deve estar entre 1 e 5.");
            }

            $formaPagamentoKey = $payload["pagamento"] ?? ($payload["forma_pagamento"] ?? "");
            $formaPagamento = $this->normalizaFormaPagamento($formaPagamentoKey);
            if (!$formaPagamento) {
                throw new Exception("Forma de pagamento inválida.");
            }

            $primeiraCobranca = $payload["primeira_cobranca"] ?? date("Y-m-d");
            if (!$this->validaData($primeiraCobranca)) {
                throw new Exception("Data da primeira cobrança inválida.");
            }

            $cliente = $this->resolveComprador([
                "nome" => $payload["nome"] ?? "",
                "email" => $payload["email"] ?? "",
                "telefone" => $payload["telefone"] ?? "",
                "cpf" => $payload["cpf"] ?? "",
                "cep" => $payload["cep"] ?? "",
                "rua" => $payload["rua"] ?? "",
                "numero" => $payload["numero"] ?? "",
                "complemento" => $payload["complemento"] ?? "",
                "bairro" => $payload["bairro"] ?? "",
                "cidade" => $payload["cidade"] ?? "",
                "estado" => $payload["estado"] ?? ""
            ]);

            if (!$cliente) {
                throw new Exception("Não foi possível cadastrar ou localizar o comprador.");
            }

            $produtorId = $this->section->getId();
            $primeiraCobrancaDate = new \DateTime($primeiraCobranca);

            $valorTotal = 0;
            $preparedItems = [];

            foreach ($items as $idx => $item) {
                $produtoId = (int)($item["produto_id"] ?? 0);
                $planoId = (int)($item["plano_id"] ?? 0);
                $quantidade = (int)($item["quantidade"] ?? 1);

                if ($produtoId <= 0 || $planoId <= 0) {
                    throw new Exception("Produto ou plano inválido na linha " . ($idx + 1) . ".");
                }

                if ($quantidade <= 0) {
                    throw new Exception("Quantidade inválida para o produto na linha " . ($idx + 1) . ".");
                }

                $produto = (new Produto())->findById($produtoId);
                $plano = (new Plano())->findById($planoId);

                if (!$produto || !$plano) {
                    throw new Exception("Produto ou plano não encontrado na linha " . ($idx + 1) . ".");
                }

                if ((int)$produto->usuario !== (int)$produtorId) {
                    throw new Exception("Você não possui permissão para utilizar o produto " . $produto->nome . ".");
                }

                if ((int)$plano->produto !== (int)$produto->id) {
                    throw new Exception("Plano selecionado não pertence ao produto informado na linha " . ($idx + 1) . ".");
                }

                if ((int)$produto->controla_estoque === 1 && (int)$produto->estoque_atual < $quantidade) {
                    throw new Exception("Estoque insuficiente para o produto {$produto->nome}.");
                }

                $precoUnitario = round((float)$plano->preco, 2);
                $subtotal = round($precoUnitario * $quantidade, 2);
                if ($subtotal <= 0) {
                    throw new Exception("O subtotal do produto {$produto->nome} deve ser maior que zero.");
                }

                $valorTotal += $subtotal;

                $preparedItems[] = [
                    "produto" => $produto,
                    "plano" => $plano,
                    "quantidade" => $quantidade,
                    "preco_unitario" => $precoUnitario,
                    "subtotal" => $subtotal,
                    "periodo" => $plano->periodo,
                    "coprodutores" => $plano->coprodutores ?? null,
                    "comissao_coprodutores" => $plano->comissao_coprodutores ?? null
                ];
            }

            $valorTotal = round($valorTotal, 2);

            if ($valorTotal <= 0) {
                throw new Exception("O valor total da venda manual precisa ser maior que zero.");
            }

            $pdo->beginTransaction();

            $manualSale = new ManualSale();
            $manualSale->produtor_id = $produtorId;
            $manualSale->comprador_id = $cliente->id;
            $manualSale->parcelas = $parcelas;
            $manualSale->forma_pagamento = $formaPagamento["label"];
            $manualSale->valor_total = $valorTotal;
            $manualSale->observacoes = $payload["observacoes"] ?? null;
            $manualSale->status = 1;

            if (!$manualSale->save()) {
                throw new Exception("Não foi possível registrar a venda manual.");
            }

            $valorParcela = round($valorTotal / $parcelas, 2);
            $valorParcelaReal = 0.0;
            $assinaturasGeradas = [];
            $vendasGeradas = [];
            $valorParcelaAcumulado = 0.0;
            $assinaturaReferencia = null;
            $totalItens = count($preparedItems);

            foreach ($preparedItems as $indice => $item) {
                /** @var Produto $produto */
                $produto = $item["produto"];
                /** @var Plano $plano */
                $plano = $item["plano"];

                $itemSubtotal = $item["subtotal"];
                $valorParcelaItem = round($itemSubtotal / $parcelas, 2);
                if ($indice === $totalItens - 1) {
                    $valorParcelaItem = round($valorParcela - $valorParcelaAcumulado, 2);
                }
                $ciclosPlanejados = $parcelas;
                $valorParcelaReal += $valorParcelaItem;
                $valorParcelaAcumulado += $valorParcelaItem;

                $manualItem = new ManualSaleItem();
                $manualItem->manual_sale_id = $manualSale->id;
                $manualItem->produto_id = $produto->id;
                $manualItem->plano_id = $plano->id;
                $manualItem->quantidade = $item["quantidade"];
                $manualItem->preco_unitario = $item["preco_unitario"];
                $manualItem->subtotal = $itemSubtotal;
                $manualItem->ciclo_recorrencia = $item["periodo"];

                if (!$manualItem->save()) {
                    throw new Exception("Não foi possível registrar o item da venda manual.");
                }

                if ((int)$produto->controla_estoque === 1) {
                    $produto->estoque_atual = (int)$produto->estoque_atual - $item["quantidade"];
                    if ($produto->estoque_atual < 0) {
                        throw new Exception("Estoque insuficiente para o produto {$produto->nome}.");
                    }
                    if (!$produto->save()) {
                        throw new Exception("Não foi possível atualizar o estoque do produto {$produto->nome}.");
                    }
                }

                $venda = new Venda();
                $venda->transacao = null;
                $venda->produto = $produto->id;
                $venda->plano = $plano->id;
                $venda->produtor = $produtorId;
                $venda->comprador = $cliente->id;
                $venda->valor_bruto = $valorParcelaItem;
                $venda->valor_produto = $valorParcelaItem;
                $venda->valor_liquido = $valorParcelaItem;
                $venda->valor_juros = 0;
                $venda->valor_frete = 0;
                $venda->taxa_juros = 0;
                $venda->valor_taxa = 0;
                $venda->valor_redrocket = 0;
                $venda->taxa_redrocket = 0;
                $venda->valor_desconto = 0;
                $venda->parcelas = $parcelas;
                $venda->meio_pagamento = $formaPagamento["label"];
                $venda->forma_pagamento = $formaPagamento["codigo"];
                $venda->status_transacao = 1;
                $venda->data_finalizada = null;
                $venda->manual_sale_item_id = $manualItem->id;
                $venda->comissao_produtor = $valorParcelaItem;
                $venda->comissao_afiliado = 0;
                $venda->coprodutores = $item["coprodutores"];
                $venda->comissao_coprodutores = $item["comissao_coprodutores"];

                if (!$venda->save()) {
                    $fail = $venda->fail();
                    $detalhe = $fail ? $fail->getMessage() : "motivo não informado";
                    throw new Exception("Não foi possível registrar a venda associada ao produto {$produto->nome}. Detalhes: {$detalhe}");
                }

                if ($assinaturaReferencia === null) {
                    $assinaturaReferencia = [
                        "manual_sale_item_id" => $manualItem->id,
                        "produto" => $produto,
                        "plano" => $plano,
                        "periodo" => $item["periodo"],
                        "coprodutores" => $item["coprodutores"],
                        "comissao_coprodutores" => $item["comissao_coprodutores"],
                        "parcelas" => $parcelas
                    ];
                }
                $vendasGeradas[] = $venda->id;
            }

            if ($assinaturaReferencia !== null && !empty($vendasGeradas)) {
                $ciclosAssinatura = $assinaturaReferencia["parcelas"] ?? $parcelas;
                $dataLimiteAssinatura = $this->calculaDataLimite(
                    $primeiraCobrancaDate,
                    $assinaturaReferencia["periodo"],
                    $ciclosAssinatura
                );

                $assinaturaStatus = $this->cadastroAssinaturas([
                    "venda" => $vendasGeradas[0],
                    "produto" => $assinaturaReferencia["produto"]->id,
                    "plano" => $assinaturaReferencia["plano"]->id,
                    "produtor" => $produtorId,
                    "comprador" => $cliente->id,
                    "recorrencia" => $assinaturaReferencia["periodo"],
                    "proxima_cobranca" => $primeiraCobrancaDate->format("Y-m-d"),
                    "data_limite" => $dataLimiteAssinatura,
                    "valor_plano" => round($valorParcelaReal, 2),
                    "forma_pagamento" => $formaPagamento["codigo"],
                    "coprodutores" => $assinaturaReferencia["coprodutores"],
                    "comissao_coprodutores" => $assinaturaReferencia["comissao_coprodutores"],
                    "manual_sale_item_id" => $assinaturaReferencia["manual_sale_item_id"]
                ]);

                if ($assinaturaStatus !== "Salvo com sucesso") {
                    throw new Exception("Falha ao criar assinatura da venda manual: {$assinaturaStatus}");
                }

                $vendaPrimaria = (new Venda())->findById($vendasGeradas[0]);
                if ($vendaPrimaria && $vendaPrimaria->assinatura) {
                    $assinaturasGeradas[] = $vendaPrimaria->assinatura;
                }
            }

            $valorParcelaReal = round($valorParcelaReal, 2);
            $gatewayInfo = null;
            if (!empty($payload["gerar_cobranca"])) {
                $gatewayInfo = $this->processarCobrancaInicial(
                    $payload,
                    $cliente,
                    $formaPagamento,
                    $valorParcelaReal,
                    $parcelas,
                    $manualSale,
                    $vendasGeradas
                );
            }

            $uploadedFiles = $this->armazenarArquivosVendaManual($manualSale->id);

            $pdo->commit();

            echo json_encode([
                "success" => true,
                "manual_sale_id" => $manualSale->id,
                "valor_total" => $valorTotal,
                "parcelas" => $parcelas,
                "valor_parcela" => $valorParcelaReal,
                "assinaturas" => $assinaturasGeradas,
                "gateway" => $gatewayInfo,
                "message" => "Venda manual registrada com sucesso."
            ]);
        } catch (Exception $exception) {
            if (isset($pdo) && $pdo instanceof \PDO && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $path) {
                    if (is_file($path)) {
                        @unlink($path);
                    }
                }
            }

            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => $exception->getMessage()
            ]);
        }
    }

    private function normalizaFormaPagamento(string $tipo): ?array
    {
        $map = [
            "credit_card" => ["codigo" => 1, "label" => "credit_card"],
            "boleto" => ["codigo" => 2, "label" => "boleto"],
            "pix" => ["codigo" => 3, "label" => "pix"]
        ];

        return $map[$tipo] ?? null;
    }

    private function validaData(string $data): bool
    {
        $d = \DateTime::createFromFormat("Y-m-d", $data);
        return $d && $d->format("Y-m-d") === $data;
    }

    private function resolveComprador(array $dados): ?UsuarioDados
    {
        $cpfCnpj = preg_replace("/\\D+/", "", $dados["cpf"] ?? "");
        $nome = trim($dados["nome"] ?? "");
        $telefone = trim($dados["telefone"] ?? "");

        if (empty($nome) || empty($telefone) || empty($cpfCnpj)) {
            return null;
        }

        $usuarioDados = null;

        if ($cpfCnpj) {
            $usuarioDados = (new UsuarioDados())->find("cpf_cnpj = :doc", "doc={$cpfCnpj}")->fetch();
        }

        if (!$usuarioDados && !empty($dados["email"])) {
            $usuarioDados = (new UsuarioDados())->find("email = :email", "email={$dados['email']}")->fetch();
        }

        if (!$usuarioDados) {
            $usuarioDados = new UsuarioDados();
        }

        $usuarioDados->nome = $nome;
        $usuarioDados->email = $dados["email"] ?? null;
        $usuarioDados->telefone = $telefone;
        $usuarioDados->cpf_cnpj = $cpfCnpj;
        $usuarioDados->tipo_doc = strlen($cpfCnpj) > 11 ? 2 : 1;
        $usuarioDados->cep = $dados["cep"] ?? null;
        $usuarioDados->rua = $dados["rua"] ?? null;
        $usuarioDados->numero = $dados["numero"] ?? null;
        $usuarioDados->complemento = $dados["complemento"] ?? null;
        $usuarioDados->bairro = $dados["bairro"] ?? null;
        $usuarioDados->cidade = $dados["cidade"] ?? null;
        $usuarioDados->estado = $dados["estado"] ?? null;
        $usuarioDados->usuario = $this->section->getId();

        if (!$usuarioDados->save()) {
            $fail = $usuarioDados->fail();
            throw new Exception($fail ? $fail->getMessage() : "Não foi possível salvar os dados do comprador.");
        }

        return $usuarioDados;
    }

    private function calculaDataLimite(\DateTime $inicio, ?string $periodo, ?int $maximoRecorrencia): ?string
    {
        if (!$periodo || !$maximoRecorrencia || $maximoRecorrencia < 1) {
            return null;
        }

        $intervalo = $this->intervaloPorPeriodo($periodo);
        if (!$intervalo) {
            return null;
        }

        $limite = clone $inicio;
        for ($i = 1; $i < $maximoRecorrencia; $i++) {
            $limite->add($intervalo);
        }

        return $limite->format("Y-m-d");
    }

    private function intervaloPorPeriodo(?string $periodo): ?\DateInterval
    {
        $periodos = [
            "Semanal" => "P7D",
            "Mensal" => "P1M",
            "Bimestral" => "P2M",
            "Trimestral" => "P3M",
            "Semestral" => "P6M",
            "Anual" => "P1Y",
            "Trienal" => "P3Y"
        ];

        $codigo = $periodos[$periodo] ?? null;
        return $codigo ? new \DateInterval($codigo) : null;
    }

    private function processarCobrancaInicial(
        array $payload,
        UsuarioDados $cliente,
        array $formaPagamento,
        float $valorPrimeiraParcela,
        int $parcelas,
        ManualSale $manualSale,
        array $vendasGeradas
    ): ?array {
        if ($valorPrimeiraParcela <= 0) {
            return null;
        }

        $telefone = preg_replace("/\\D+/", "", $payload["telefone"] ?? "");
        $email = $cliente->email ?? ($payload["email"] ?? null);
        if (!$email) {
            $email = "sem-email-{$manualSale->id}@winpay.local";
        }
        $cpf = preg_replace("/\\D+/", "", $cliente->cpf_cnpj ?? ($payload["cpf"] ?? ""));
        if (!$cpf) {
            throw new Exception("CPF do cliente é obrigatório para gerar cobrança.");
        }

        $valorCentavos = (int)round($valorPrimeiraParcela * 100);

        if ($formaPagamento["label"] === "pix") {
            $bsPay = new BsPay();
            $pixPayload = [
                "id" => $manualSale->id,
                "nome" => $cliente->nome,
                "email" => $email,
                "cpf" => $cpf,
                "telefone" => $telefone,
                "preco" => $valorCentavos,
                "nome_produto" => "Venda manual #" . $manualSale->id,
                "descricao" => "Primeira parcela venda manual #" . $manualSale->id,
            ];

            $resposta = $bsPay->compraPix($pixPayload);
            if (!$resposta) {
                throw new Exception("Não foi possível gerar a cobrança Pix inicial.");
            }

            $qrCode = $resposta->qrcode ?? ($resposta->qrCode ?? null);
            $transactionId = $resposta->transactionId ?? null;

            foreach ($vendasGeradas as $vendaId) {
                $venda = (new Venda())->findById($vendaId);
                if (!$venda) {
                    continue;
                }
                $venda->transacao = $transactionId;
                $venda->nit_pix = $transactionId;
                $venda->qr_code = $qrCode;
                $venda->status_transacao = 1; // pendente
                $venda->meio_pagamento = "Pix";
                $venda->forma_pagamento = 3;
                $venda->data_finalizada = null;
                $venda->save();
            }

            $manualSale->status = 1;
            $manualSale->save();

            return [
                "type" => "pix",
                "transaction_id" => $transactionId,
                "qr_code" => $qrCode,
                "raw" => json_decode(json_encode($resposta), true)
            ];
        }

        if ($formaPagamento["label"] === "credit_card") {
            $numeroCartao = preg_replace("/\\D+/", "", $payload["numcart"] ?? "");
            $mesVal = $payload["mes_val"] ?? "";
            $anoVal = $payload["ano_val"] ?? "";
            $cvv = $payload["cvv"] ?? "";

            if (!$numeroCartao || !$mesVal || !$anoVal || !$cvv) {
                throw new Exception("Preencha os dados do cartão para gerar a cobrança.");
            }

            $stripe = new Stripe();
            $cartaoPayload = [
                "preco" => $valorCentavos,
                "nome" => $cliente->nome,
                "email" => $email,
                "cpf" => $cpf,
                "telefone" => $telefone,
                "parcela" => $parcelas,
                "numcart" => $numeroCartao,
                "mes_val" => $mesVal,
                "ano_val" => $anoVal,
                "cvv" => $cvv,
                "descricao" => "Primeira parcela venda manual #" . $manualSale->id
            ];

            $resposta = $stripe->compraCartao($cartaoPayload);
            if (isset($resposta->error)) {
                $mensagem = $resposta->error->message ?? "Erro ao processar cartão";
                throw new Exception($mensagem);
            }

            $status = strtolower($resposta->status ?? "");
            $statusMap = [
                "succeeded" => 4,
                "requires_payment_method" => 5,
                "requires_action" => 1,
                "processing" => 1,
                "pending" => 1,
                "failed" => 5,
                "canceled" => 5
            ];

            $statusVenda = $statusMap[$status] ?? 1;
            $transactionId = $resposta->id ?? null;

            foreach ($vendasGeradas as $vendaId) {
                $venda = (new Venda())->findById($vendaId);
                if (!$venda) {
                    continue;
                }
                $venda->transacao = $transactionId;
                $venda->status_transacao = $statusVenda;
                $venda->meio_pagamento = "Stripe";
                $venda->forma_pagamento = 1;
                $venda->data_finalizada = ($statusVenda === 4) ? date("Y-m-d H:i:s") : null;
                if ($statusVenda === 5) {
                    $venda->motivo = "O pagamento foi rejeitado pela operadora.";
                }
                $venda->save();
            }

            $manualSale->status = $statusVenda;
            $manualSale->save();

            return [
                "type" => "credit_card",
                "transaction_id" => $transactionId,
                "status" => $status,
                "raw" => json_decode(json_encode($resposta), true)
            ];
        }

        return null;
    }

    private function armazenarArquivosVendaManual(int $manualSaleId): array
    {
        $salvos = [];
        $mapeamento = [
            "foto_cliente" => "cliente_foto",
            "contrato" => "contrato",
            "fotos_produto" => "produto_entregue"
        ];

        $baseDir = rtrim(PASTA, "/") . "/vendas-manuais/" . $manualSaleId . "/";
        if (!is_dir($baseDir) && !mkdir($baseDir, 0775, true)) {
            throw new Exception("Não foi possível criar o diretório para os anexos.");
        }

        $uploader = new File();

        foreach ($mapeamento as $campo => $tipo) {
            if (empty($_FILES[$campo])) {
                continue;
            }

            $entrada = $_FILES[$campo];
            $arquivos = [];

            if (is_array($entrada["name"])) {
                foreach ($entrada["name"] as $index => $nome) {
                    if (empty($nome)) {
                        continue;
                    }
                    $arquivos[] = [
                        "name" => $entrada["name"][$index],
                        "type" => $entrada["type"][$index],
                        "tmp_name" => $entrada["tmp_name"][$index],
                        "error" => $entrada["error"][$index],
                        "size" => $entrada["size"][$index]
                    ];
                }
            } else {
                $arquivos[] = $entrada;
            }

            foreach ($arquivos as $arquivo) {
                if (empty($arquivo["name"])) {
                    continue;
                }
                $novoNome = $uploader->upload($arquivo, $baseDir, 10);
                if (!$novoNome) {
                    throw new Exception("Falha ao enviar o arquivo {$arquivo['name']}.");
                }

                $relativo = "vendas-manuais/" . $manualSaleId . "/" . $novoNome;
                $registro = new ManualSaleFile();
                $registro->manual_sale_id = $manualSaleId;
                $registro->tipo = $tipo;
                $registro->caminho = $relativo;
                $registro->nome_original = $arquivo["name"];
                $registro->tamanho = $arquivo["size"];

                if (!$registro->save()) {
                    throw new Exception("Não foi possível registrar o arquivo {$arquivo['name']}.");
                }

                $salvos[] = $baseDir . $novoNome;
            }
        }

        return $salvos;
    }


}

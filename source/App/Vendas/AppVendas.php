<?php

namespace Source\App\Vendas;

session_start();

use League\Plates\Engine;
use Source\Models\Vendas\Venda;
use Source\Models\Produtos\Produto;
use Source\Models\Campanhas\Campanha;
use Source\Helpers\Paginador;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Emails;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Comissao\Comissao;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;
use Source\Models\Produtos\Plano;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;

class AppVendas
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

    public function boletos($data)
    {
        $venda = (new Venda())->findById($data["venda"]);

        if ($venda && $venda->link_boleto) {
            header("Location: " . $venda->link_boleto);
        } else {
            echo "Venda ou boleto não encontrado";
        }
    }

    public function vendas($data)
    {
        $filtros = $this->filtros();
        //var_dump($filtros);
        $pag_coprodutor = false;

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        $order = @$_GET["order"] ? $_GET["order"] : "id";

        if (@$this->section->getTipo() == 4) {
            if ($filtros)
                $filtros .= " AND ";

            $busca = $filtros . "(produtor = " . @$this->section->getId() . " OR afiliado = " . @$this->section->getId() . ")";

            $soma1 = (new Venda())->find($filtros . "produtor = " . @$this->section->getId(), null, "SUM(valor_bruto) as soma")->fetch();
            $soma2 = (new Venda())->find($filtros . "afiliado = " . @$this->section->getId(), null, "SUM(comissao_afiliado) as soma")->fetch();

            @$somaVendas = @$soma1->soma + @$soma2->soma;
            $somaProdutor = (new Venda())->find($filtros . "produtor = " . $this->section->getId(), null, "SUM(comissao_produtor) as soma_produtor")->fetch();
        } else if (@$this->section->getTipo() == 5) {
            if ($filtros)
                $filtros .= " AND ";

            $busca = $filtros . "afiliado = " . @$this->section->getId();
            $somas = (new Venda())->find($filtros . "afiliado = " . @$this->section->getId(), null, "SUM(comissao_afiliado) as soma")->fetch();
            $somaVendas = $somas->soma;
        } else if (@$this->section->getTipo() == 6) {
            if ($filtros)
                $filtros .= " AND ";

            if (@$this->section->getIdU())
                $busca = $filtros . "comprador = " . @$this->section->getIdU();
            else
                $busca = $filtros . "comprador = 0";

            @$somaVendas = "-";
        } else {
            $busca = $filtros;
            $somas = (new Venda())->find($filtros, null, "SUM(valor_bruto) as soma")->fetch();
            $somaVendas = $somas->soma;
            $filtro = $filtros ? $filtros . " AND " : "";
            $somaProdutor = (new Venda())->find($filtro . "produtor = " . $this->section->getId(), null, "SUM(comissao_produtor) as soma_produtor")->fetch();
        }

        $section = new Section();
        if (@$_GET["minhas"] == "true") {
            (new Section())->permissaoModulo("minhas_compras");

            if (@$_SESSION["subusuario"])
                $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $this->section->getId() . " AND modulo = 'minhas_compras'")->fetch();

            if ($filtros)
                $filtros .= " AND ";
            $busca = $filtros . "comprador = '" . @$this->section->getIdU() . "'";
            $somas = (new Venda())->find($busca, null, "SUM(valor_bruto) as soma")->fetch();
            $somaVendas = $somas->soma;
        } else {

            (new Section())->permissaoModulo("vendas");

            if (@$_SESSION["subusuario"])
                $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'vendas'")->fetch();
        }

        //echo "id u ".$_SESSION["idU"];var_dump($busca);

        $statsColumns = "COUNT(*) as total, SUM(forma_pagamento = 2) as boleto, SUM(forma_pagamento = 1) as cartao, SUM(forma_pagamento = 3) as pix";
        $stats = (new Venda())->find($busca ?: null, null, $statsColumns)->fetch();
        $vendasCount = $stats ? (int)$stats->total : 0;
        $vendasBoleto = $stats ? (int)$stats->boleto : 0;
        $vendasCartao = $stats ? (int)$stats->cartao : 0;
        $vendasPix    = $stats ? (int)$stats->pix : 0;

        $_SESSION["busca"] = $busca;
        $_SESSION["coprodutor"] = $pag_coprodutor;

        $paginas = ceil($vendasCount / 30);
        $vendas = (new Venda())->find($busca ?: null)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("$order DESC")->fetch(true);
        $produtos = new Produto();
        $afiliacao = new Afiliacao();
        $afiliados = (new Afiliacao())->find("produtor = {$this->section->getId()} GROUP BY afiliado")->fetch(true);
        $campanhas = (new Campanha())->find("usuario = {$this->section->getId()}")->fetch(true);
        $comissoes = new Comissao();
        $coproducoes = new CoProdutor();

        $planos = (new Plano())->find("usuario = {$this->section->getId()}")->fetch(true);
        //var_dump($vendasCount);
        $user = (new Usuario())->findById($this->section->getId());
        $nivel_usuario = $user->grupo;

        $usuario = new Usuario();
        $usuarioDados = new UsuarioDados();

        echo $this->view->render("vendas/vendas", [
            "title" => "Vendas",
            "section" => @$section,
            "permissao" => @$permissao,
            "vendas" => $vendas,
            "produtos" => $produtos,
            "afiliacao" => $afiliacao,
            "comissoes" => @$comissoes,
            "coproducoes" => @$coproducoes,
            "count" => $vendasCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "somaVendas" => @$somaVendas,
            "somaProdutor" => @$somaProdutor,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
            "vendasPix" => $vendasPix,
            "pag_coprodutor" => $pag_coprodutor,
            "planos" => $planos,
            "afiliados" => $afiliados,
            "campanhas" => $campanhas,
            "nivelUsuario" => $nivel_usuario,
            "usuario" => $usuario,
            "usuarioDados" => $usuarioDados,
        ]);
    }

    public function vendasCoprodutor($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("vendas_coprodutor");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $this->section->getId() . " AND modulo = 'vendas_coprodutor'")->fetch();

        $filtros = $this->filtros();

        $pag_coprodutor = true;

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;

        if ($filtros) {
            $filtros .= " AND ";
        }

        $usuario = $this->section->getId();
        $busca = $filtros . "coprodutores LIKE '%/$usuario/%'";

        $statsColumns = "COUNT(*) as total, SUM(forma_pagamento = 2) as boleto, SUM(forma_pagamento = 1) as cartao, SUM(forma_pagamento = 3) as pix";
        $stats = (new Venda())->find($busca ?: null, null, $statsColumns)->fetch();
        $vendasCount = $stats ? (int)$stats->total : 0;
        $vendasBoleto = $stats ? (int)$stats->boleto : 0;
        $vendasCartao = $stats ? (int)$stats->cartao : 0;
        $vendasPix    = $stats ? (int)$stats->pix : 0;

        $paginas = ceil($vendasCount / 30);
        $vendas = (new Venda())->find($busca ?: null)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("data_ultimo_evento DESC")->fetch(true);

        $coprodutor = new CoProdutor();
        $comissoes = new Comissao();
        $somaVendas = 0;

        //var_dump($vendasCount);

        $_SESSION["busca"] = $busca;
        $_SESSION["coprodutor"] = $pag_coprodutor;

        $produtos = new Produto();
        $afiliacao = new Afiliacao();
        $usuarioDados = new UsuarioDados();

        echo $this->view->render("vendas/vendas", [
            "title" => "Suas vendas de CoProdutor",
            "section" => @$section,
            "permissao" => @$permissao,
            "vendas" => $vendas,
            "produtos" => @$produtos,
            "afiliacao" => @$afiliacao,
            "comissoes" => @$comissoes,
            "count" => $vendasCount,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "somaVendas" => @$somaVendas,
            "vendasBoleto" => $vendasBoleto,
            "vendasCartao" => $vendasCartao,
            "vendasPix" => $vendasPix,
            "coprodutor" => $coprodutor,
            "pag_coprodutor" =>  $pag_coprodutor,
            "usuarioDados" => $usuarioDados,
        ]);
    }

    public function filtros()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];
        $inicio_finalizada = @$_GET["inicio_finalizada"];
        $fim_finalizada = @$_GET["fim_finalizada"];
        $venda = @$_GET["venda"];
        $status = @$_GET["status"];
        $produto = @$_GET["produto"];
        $comprador = @$_GET["comprador"];
        $pagamento = @$_GET["pagamento"];
        $campanha = @$_GET["campanha"];
        $afiliado = @$_GET["afiliado"];
        $televendas = @$_GET["televendas"];
        $cupom = @$_GET["cupom"];
        $frete = @$_GET["frete"];
        $assumir_juros = @$_GET["assumir_juros"];
        $upsell = @$_GET["upsell"];
        $vencimento_boleto = @$_GET["vencimento_boleto"];
        $parcelas = @$_GET["parcelas"];
        $order_bump = @$_GET["order_bump"];
        $src = @$_GET["src"];
        $sql = array();

        if ($inicio != "" || $fim != "") {
            if ($inicio == "")
                $inicio = date("Y-m-d");

            if ($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "(created_at BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59')";
        }

        if ($inicio_finalizada != "" || $fim_finalizada != "") {
            if ($inicio_finalizada == "")
                $inicio_finalizada = date("Y-m-d");

            if ($fim_finalizada == "")
                $fim_finalizada = date("Y-m-d");

            $sql[] = "(data_finalizada BETWEEN '{$inicio_finalizada} 00:00:00' AND '{$fim_finalizada} 23:59:59')";
        }

        if ($venda != "") {
            $sql[] = "transacao LIKE '%$venda%' OR id = '$venda'";
        }

        if ($status != "") {
            $sql[] = "status_transacao = $status";
        }

        if ($produto != "") {
            $sql[] = "produto = $produto";
        }

        if ($comprador != "") {
            $sql[] = "comprador IN ( SELECT usuario_dados.id FROM usuario_dados WHERE nome LIKE '%$comprador%' OR cpf_cnpj = '$comprador' OR email LIKE '%$comprador%')";
        }

        if ($pagamento != "") {
            $sql[] = "forma_pagamento = $pagamento";
        }

        if ($campanha != "") {
            $sql[] = "campanha = $campanha";
        }

        if ($afiliado != "") {
            $sql[] = "afiliado = $afiliado";
        }

        if ($televendas != "") {
            $sql[] = "atendente_televendas $televendas";
        }

        if ($cupom != "") {
            $sql[] = "cupom = '$cupom'";
        }

        if ($frete != "") {
            $sql[] = "tipo_frete = '$frete'";
        }

        if ($assumir_juros != "") {
            $sql[] = "assumir_juros $assumir_juros";
        }

        if ($upsell != "") {
            $sql[] = "upsell $upsell";
        }

        if ($vencimento_boleto != "") {
            $sql[] = "data_vencimento = '$vencimento_boleto'";
        }

        if ($parcelas != "") {
            $sql[] = "parcelas = $parcelas";
        }

        if ($order_bump != "") {
            $sql[] = "planos_order_bump $order_bump";
        }

        if ($src != "") {
            $sql[] = "src = '$src' OR utm_source = '$src' OR utm_campaign = '$src' OR utm_medium = '$src' OR utm_content = '$src'";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function motivoCancelamento($data)
    {
        header('Content-type: text/html; charset=UTF-8');

        $venda = (new Venda())->findById($data["id"]);
        $venda->motivo = @$data["motivo"];

        if ($venda->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $venda->fail()->getMessage();
        }
    }

    public function exportar($data)
    {
        //var_dump($_SESSION);
        $busca = $_SESSION["busca"];
        $vendas = (new Venda())->find($busca)->order("data_ultimo_evento DESC")->fetch(true);

        /** 
         * DADOS TABELA EXCEL
         * $analitico[9] = array("Boleto Outros", $upsell, $upsellValores, $p)
         */
        $forma_pagamento = array(
            1 => 'Cartão de Crédito',
            2 => 'Boleto',
            3 => 'Pix',
        );

        $status_transacao = array(
            1  => "AGUARDANDO PAGAMENTO",
            2  => "EM ANÁLISE",
            3  => "PRÉ AUTORIZADA",
            4  => "PAGA",
            5  => "CANCELADA",
            6  => "DEVOLVIDA",
            7  => "CHARGEBACK",
            8  => "CONCLUÍDA",
            9  => "BLOQUEADA",
            10 => "ANTIFRAUDE",
        );

        $valoresCoprodutor = @$_SESSION["valores_coproducao"];

        $i = 1;

        // var_dump($busca, $vendas);

        if ($vendas) {
            foreach ($vendas as $venda) {
                if ($this->section->getId() != $venda->produtor) {
                    $venda->valor_produto = 0;
                    $venda->valor_redrocket = 0;
                }
                $analitico[] = array(
                    $venda->transacao,
                    $venda->dataProduto()->codigo,
                    $venda->dataProduto()->nome,
                    formataDataHora($venda->created_at),
                    $venda->data_finalizada ? formataDataHora($venda->data_finalizada) : "",
                    $venda->endereco ? @$venda->dataEndereco()->nome : @$venda->dataComprador()->nome,
                    $venda->endereco ? @$venda->dataEndereco()->cpf_cnpj : @$venda->dataComprador()->cpf_cnpj,
                    $venda->endereco ? @$venda->dataEndereco()->email : @$venda->dataComprador()->email,
                    $venda->endereco ? @$venda->dataEndereco()->telefone : @$venda->dataComprador()->telefone,
                    $venda->endereco ? @$venda->dataEndereco()->rua : @$venda->dataComprador()->rua,
                    $venda->endereco ? @$venda->dataEndereco()->numero : @$venda->dataComprador()->numero,
                    $venda->endereco ? @$venda->dataEndereco()->complemento : @$venda->dataComprador()->complemento,
                    $venda->endereco ? @$venda->dataEndereco()->bairro : @$venda->dataComprador()->bairro,
                    $venda->endereco ? @$venda->dataEndereco()->cidade : @$venda->dataComprador()->cidade,
                    $venda->endereco ? @$venda->dataEndereco()->estado : @$venda->dataComprador()->estado,
                    $venda->endereco ? @$venda->dataEndereco()->cep : @$venda->dataComprador()->cep,
                    $venda->plano ? $venda->dataPlano()->nome : "",
                    $venda->plano ? $venda->dataPlano()->quantidade : "",
                    $venda->valor_desconto,
                    $venda->valor_frete,
                    $venda->valor_produto,
                    $venda->valor_redrocket,
                    $venda->comissao_afiliado,
                    // $venda->coprodutores,
                    @$forma_pagamento[$venda->forma_pagamento],
                    $venda->parcelas,
                    @$status_transacao[$venda->status_transacao],
                    $venda->motivo,
                    $venda->src,
                    $venda->utm_source,
                    $venda->utm_medium,
                    $venda->utm_content,
                    $venda->utm_campaign,
                );

                // if($valoresCoprodutor){
                //     // $analitico[32 + $i] = 
                //     $valoresCoprodutor[$i][2];
                //     echo $venda->id . " - " . $analitico[32 + $i] . "<br>";
                // }

                $i++;
            }
        }
        // echo "<pre>";
        // var_dump($valoresCoprodutor);
        // echo "</pre>";

        $tabela2  = "<table border='1' class='table table-striped'>";

        $tabela2 .= "<thead>";
        $tabela2 .= "<tr>";
        $tabela2 .= "<th scope='col'>CODIGO VENDA</th>";
        $tabela2 .= "<th scope='col'>CODIGO PRODUTO</th>";
        $tabela2 .= "<th scope='col'>PRODUTO</th>";
        $tabela2 .= "<th scope='col'>Data do pedido</th>";
        $tabela2 .= "<th scope='col'>DATA FINALIZADA</th>";
        $tabela2 .= "<th scope='col'>Nome comprador</th>";
        $tabela2 .= "<th scope='col'>CNPJ/CPF</th>";
        $tabela2 .= "<th scope='col'>E-mail comprador</th>";
        $tabela2 .= "<th scope='col'>Telefone</th>";
        $tabela2 .= "<th scope='col'>Endereço</th>";
        $tabela2 .= "<th scope='col'>Número</th>";
        $tabela2 .= "<th scope='col'>Complemento</th>";
        $tabela2 .= "<th scope='col'>Bairro</th>";
        $tabela2 .= "<th scope='col'>Cidade</th>";
        $tabela2 .= "<th scope='col'>ESTADO</th>";
        $tabela2 .= "<th scope='col'>Cep</th>";
        $tabela2 .= "<th scope='col'>Plano</th>";
        $tabela2 .= "<th scope='col'>Itens Plano</th>";
        $tabela2 .= "<th scope='col'>Cupom</th>";
        $tabela2 .= "<th scope='col'>Frete</th>";
        $tabela2 .= "<th scope='col'>Valor Produto</th>";
        $tabela2 .= "<th scope='col'>Taxa REDROCKET (R$)</th>";
        $tabela2 .= "<th scope='col'>Afiliados</th>";
        // $tabela2 .= "<th scope='col'>Co-produtor</th>";
        $tabela2 .= "<th scope='col'>FORMA PAGAMENTO</th>";
        $tabela2 .= "<th scope='col'>Parcelas</th>";
        $tabela2 .= "<th scope='col'>Status</th>";
        $tabela2 .= "<th scope='col'>Motivo de Cancelamento</th>";
        $tabela2 .= "<th scope='col'>src</th>";
        $tabela2 .= "<th scope='col'>utm_source</th>";
        $tabela2 .= "<th scope='col'>utm_medium</th>";
        $tabela2 .= "<th scope='col'>utm_content</th>";
        $tabela2 .= "<th scope='col'>utm_campaign</th>";
        $tabela2 .= "</tr>";
        $tabela2 .= "</thead>";

        if (@$analitico) {
            foreach ($analitico as $dado) {
                $tabela2 .= "<tr>";
                $tabela2 .= "<td>" . @$dado[0] . "</td>";
                $tabela2 .= "<td>" . @$dado[1] . "</td>";
                $tabela2 .= "<td>" . @$dado[2] . "</td>";
                $tabela2 .= "<td>" . @$dado[3] . "</td>";
                $tabela2 .= "<td>" . @$dado[4] . "</td>";
                $tabela2 .= "<td>" . @$dado[5] . "</td>";
                $tabela2 .= "<td>" . @$dado[6] . "</td>";
                $tabela2 .= "<td>" . @$dado[7] . "</td>";
                $tabela2 .= "<td>" . @$dado[8] . "</td>";
                $tabela2 .= "<td>" . @$dado[9] . "</td>";
                $tabela2 .= "<td>" . @$dado[10] . "</td>";
                $tabela2 .= "<td>" . @$dado[11] . "</td>";
                $tabela2 .= "<td>" . @$dado[12] . "</td>";
                $tabela2 .= "<td>" . @$dado[13] . "</td>";
                $tabela2 .= "<td>" . @$dado[14] . "</td>";
                $tabela2 .= "<td>" . @$dado[15] . "</td>";
                $tabela2 .= "<td>" . @$dado[16] . "</td>";
                $tabela2 .= "<td>" . @$dado[17] . "</td>";
                $tabela2 .= "<td>" . @$dado[18] . "</td>";
                $tabela2 .= "<td>" . @$dado[19] . "</td>";
                $tabela2 .= "<td>" . @$dado[20] . "</td>";
                $tabela2 .= "<td>" . @$dado[21] . "</td>";
                $tabela2 .= "<td>" . @$dado[22] . "</td>";
                $tabela2 .= "<td>" . @$dado[23] . "</td>";
                $tabela2 .= "<td>" . @$dado[24] . "</td>";
                $tabela2 .= "<td>" . @$dado[25] . "</td>";
                $tabela2 .= "<td>" . @$dado[26] . "</td>";
                $tabela2 .= "<td>" . @$dado[27] . "</td>";
                $tabela2 .= "<td>" . @$dado[28] . "</td>";
                $tabela2 .= "<td>" . @$dado[29] . "</td>";
                $tabela2 .= "<td>" . @$dado[30] . "</td>";
                $tabela2 .= "<td>" . @$dado[31] . "</td>";
                $tabela2 .= "<td>" . @$dado[32] . "</td>";
                // $tabela2 .= "<td>" . @$dado[33] . "</td>";
                $tabela2 .= "</tr>";

                // echo "<pre>";
                // var_dump(@$dado[33]);
                // echo "</pre>";
            }
        }

        $tabela2 .= "</table>";

        $arquivo = "vendas.xls";
        $_SESSION["tabela"]["vendas"] = $tabela2;

        // echo $tabela2;
        header("Location: " . url("vendas/exportar-vendas"));
    }

    public function exportarVendas($data)
    {
        $arquivo = "vendas.xls";

        header("Cache-Control: no-cache, must-revalidate");
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel');
        header("Content-Disposition: attachment; filename=\"{$arquivo}\"");

        echo @$_SESSION["tabela"]["vendas"] . "<br>";
    }

    public function rastreio($data)
    {
        $venda = (new Venda())->findById($data["venda"]);

        $venda->codigo_rastreio = $data["codigo_rastreio"];

        if ($venda->save()) {
            echo "Código inserido com sucesso!";

            $dados["email"] = $venda->dataEndereco()->email;
            $dados["assunto"] = "Código de Rastreio";
            $dados["mensagem"] = "Olá {$venda->dataEndereco()->nome}, tudo bem?<br>" .
                "Passando para informar que seu pedido teve uma atualição e está disponível o código de rastreio.<br><br>" .
                "Código de rastreio: {$data["codigo_rastreio"]} <br>" .
                "Link de rastreio: {$venda->dataProduto()->link_rastreio}";
            (new Emails())->enviaSendGrid($dados);
        } else {
            echo "Erro ao inserir código.";
        }
    }
}

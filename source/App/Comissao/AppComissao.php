<?php

//CLASSE QUE CONTROLA AS ROTAS DOS AFILIADOS

namespace Source\App\Comissao;

session_start();

use League\Plates\Engine;
use Source\App\Checkout1\PagarMe;
use Source\App\Checkout1\Stripe;
use Source\App\Checkout1\WinPay;
use Source\Models\Configuracoes\ConfiguracaoGateway;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Banco;
use Source\Models\Comissao\Saldo;
use Source\Models\Vendas\Venda;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Configuracoes\Antecipacao;
use Source\Models\Assinaturas\Assinatura;
use Source\Models\Configuracoes\Taxa;
use Source\Helpers\Paginador;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Emails;
use Source\Helpers\File;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;

class AppComissao
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
        $this->section = new Section();
    }

    public function comissao($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("saques_extratos");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'saques_extratos'")->fetch();

        $filtros = $this->filtroComissao();

        if ($filtros) {
            $filtro = $filtros . " AND ";
        }

        $paginador = new Paginador();
        $pagina = @$data["page"] ? $data["page"] : 1;
        $offset = 30;

        if (@$_GET["meus"]) {
            $busca = @$filtro . "usuario = " . $this->section->getId();
        } else {
            $busca = $filtros;
        }

        $tempo_saque = @$_SESSION["tempo_saque"] ? $_SESSION["tempo_saque"] : 30;

        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) {
            $comissaoCount = (new Comissao())->find(@$busca)->count();
            $paginas = ceil($comissaoCount / $offset);
            $comissao = (new Comissao())->find(@$busca)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        } else {
            $comissaoCount = (new Comissao())->find(@$filtro . "usuario = " . $this->section->getId())->count();
            $paginas = ceil($comissaoCount / $offset);
            $comissao = (new Comissao())->find(@$filtro . "usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        }

        $usuario = $this->section->getId();
        $hoje = (new Comissao())->find("descricao NOT LIKE 'Desbloqueio%' AND tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d") . "'", null, "SUM(valor)")->fetch();
        $ontem = (new Comissao())->find("descricao NOT LIKE 'Desbloqueio%' AND tipo = 1 AND usuario = $usuario AND data_pedido = '" . date("Y-m-d", strtotime("-1 day")) . "'", null, "SUM(valor)")->fetch();
        $mes = (new Comissao())->find("descricao NOT LIKE 'Desbloqueio%' AND tipo = 1 AND usuario = $usuario AND data_pedido BETWEEN '" . date("Y-m-01") . "' AND '"  . date("Y-m-d") . "'", null, "SUM(valor)")->fetch();
        $pendente = (new Comissao())->find("(antecipado = 1 OR antecipado = 0) and disponivel = 0 and tipo = 1 and usuario = $usuario", null, "SUM(valor)")->fetch();
        $bloqueado = (new Comissao())->find("descricao like '%bloqueio%' AND usuario = $usuario AND valor", null, "SUM(valor) as valor")->fetch();
        $antecipado = (new Comissao())->find("usuario = $usuario AND disponivel = 0", null, "SUM(antecipado)")->fetch();
        $saldo = (new Saldo())->find("usuario = " . $this->section->getId())->fetch();

        $saldoTotal = (new Comissao())->find("tipo = 1 AND usuario = $usuario AND valor > 0", null, "SUM(valor) as valor")->fetch();
        $saldoTotalVendas = (new Venda())->find("produtor = $usuario AND (status_transacao = 4 OR status_transacao = 6 OR status_transacao = 7)", null, "SUM(valor_liquido) as valor")->fetch();

        $saqueTotal = (new Comissao())->find("tipo = 2 AND usuario = $usuario", null, "SUM(valor) as valor")->fetch();
        $saqueTotalAntecipacao = (new Comissao())->find("tipo = 3 AND usuario = $usuario AND valor < 0", null, "SUM(valor) as valor")->fetch();
        $saqueTotalAntecipacao2 = (new Comissao())->find("tipo = 3 AND usuario = $usuario AND valor > 0", null, "SUM(valor) as valor")->fetch();
        echo "<!-- $saldoTotal->valor, $saldoTotalVendas->valor, $saqueTotal->valor, $saqueTotalAntecipacao->valor, $saqueTotalAntecipacao2->valor -->";

        $usuarioDados = (new UsuarioDados())->find("usuario = $usuario")->fetch();
        $user = (new Usuario())->findById($usuario);
        $bancos = (new Banco())->find("status = 2 AND usuario = " . $this->section->getId())->fetch(true);
        $saqueMes = (new Comissao())->find("tipo = 2 and status = 2 AND usuario = $usuario AND created_at BETWEEN '" . date("Y-m-01 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'", null, "SUM(valor) as valor")->fetch();
        $taxas = (new Taxa())->findById(1);

        foreach ($hoje->data() as $hoje) null;
        foreach ($ontem->data() as $ontem) null;
        foreach ($mes->data() as $mes) null;
        foreach ($pendente->data() as $pendente) null;
        foreach ($antecipado->data() as $antecipado) null;



        $primeiroSaque = (new Comissao())->find("usuario = $usuario AND tipo = 2")->count();

        $pagarmeId    = $user->pagarme_conta;
        $saldoPagarme = $pagarmeId ? (new PagarMe())->saldo($pagarmeId) : (object)[];

        echo $this->view->render("comissao/comissao", [
            "title" => "Saldos e Extratos",
            "section" => @$section,
            "permissao" => @$permissao,
            "comissao" => $comissao,
            "primeiroSaque" => $primeiroSaque,
            "bancos" => $bancos,
            "hoje" => @$hoje,
            "ontem" => @$ontem,
            "mes" => @$mes,
            "pendente" => ! empty( $saldoPagarme->waiting_funds_amount  ) ? $saldoPagarme->waiting_funds_amount / 100 : 0,
            "antecipado" => @$antecipado,
            "comissao" => @$comissao,
            "saldo" =>  $saldo->valor,
            "usuario" => @$usuarioDados,
            "user" => @$user,
            "saqueMes" => @$saqueMes,
            "bloqueado" => @$bloqueado->valor,
            "taxas" => @$taxas,
            "count" => $comissaoCount,
            "paginador" => @$paginador,
            "paginas" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "saldoPagarme" => $saldoPagarme,
        ]);
    }

    public function saquePagarme()
    {

        $withdraw_value = $_POST['valor_a_sacar'];
        $withdraw_value = str_replace(array('.', ','), '', $withdraw_value);

        if ( ! $withdraw_value > 0 ) {
            echo "É necessário informar um valor para ser sacado";
            return;
        }

        $usuario   = (new Usuario())->findById($this->section->getId());
        $pagarmeId = $usuario->pagarme_conta;

        if (!$pagarmeId) {
            echo "Conta PagarMe não encontrada para este usuário.";
            return;
        }

        // Valida contra o saldo disponível real da PagarMe
        $saldoApi        = (new PagarMe())->saldo($pagarmeId);
        $available_cents = intval($saldoApi->available_amount ?? 0);
        $available_brl   = $available_cents / 100;

        $MIN_SAQUE = 10000; // R$ 100,00 em centavos

        if ($withdraw_value > $available_cents) {
            echo "Valor superior ao disponível na PagarMe (R$ " . number_format($available_brl, 2, ',', '.') . ").";
            return;
        }

        if ($withdraw_value < $MIN_SAQUE) {
            echo "Valor mínimo para saque é R$ 100,00.";
            return;
        }

        $valor =  sprintf("%.2f", $withdraw_value / 100); 

        $saque = new Comissao();
        $saque->usuario = $usuario->id;
        $saque->usuario_ultima_alteracao = $this->section->getId();
        $saque->valor = -$valor;
        $saque->descricao = "Solicitação de saque pendente";
        $saque->tipo = 2;
        $saque->status = 1;
        $data_saque = strtotime(date("H:i")) >= '17:00' ? date("Y-m-d", strtotime("+3 days")) :  date("Y-m-d", strtotime("+2 days"));

        if (date("D", strtotime($data_saque)) == "Sun") {
            $data_saque = date("Y-m-d", strtotime("+1 day", strtotime($data_saque)));
        } else if (date("D", strtotime($data_saque)) == "Sat") {
            $data_saque = date("Y-m-d", strtotime("+2 day", strtotime($data_saque)));
        }

        $saque->tempo_saque = $data_saque;
        $saque->taxa  = 0;
        $saque->data_pedido = date("Y-m-d");
        if($saque->save()){
            echo "Saque solicitado com sucesso.";
        } else {
            echo "Erro " . $saque->fail()->getMessage();
        }
    
    
    }

    public function filtroComissao()
    {
        $inicio =       @$_GET["inicio"];
        $fim =          @$_GET["fim"];
        $usuario =      @$_GET["usuario"];
        $sql = array();

        if ($inicio != "" || $fim != "") {
            if ($inicio == "")
                $inicio = date("Y-m-d");

            if ($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "(data_pedido BETWEEN '{$inicio}' AND '{$fim}')";
        }

        if ($usuario != "") {
            $sql[] = "usuario IN ( SELECT usuario.id FROM usuario WHERE nome LIKE '%$usuario%' OR login = '$usuario' OR email LIKE '%$usuario%')";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function comissaoRedrocket($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $paginador = new Paginador();
        $pagina = @$data["page"] ? $data["page"] : 1;
        $offset = 30;

        $tempo_saque = @$_SESSION["tempo_saque"] ? $_SESSION["tempo_saque"] : 30;

        $comissaoCount = (new Comissao())->find("usuario = 1")->count();
        $paginas = ceil($comissaoCount / $offset);
        $comissao = (new Comissao())->find("usuario = 1")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);

        $hoje = (new Comissao())->find("descricao NOT LIKE 'Desbloqueio%' AND usuario = 1 AND tempo_saque = '" . date("Y-m-d") . "'", null, "SUM(valor)")->fetch();
        $ontem = (new Comissao())->find("descricao NOT LIKE 'Desbloqueio%' AND usuario = 1 AND tempo_saque = '" . date("Y-m-d", strtotime("-1 day")) . "'", null, "SUM(valor)")->fetch();
        $mes = (new Comissao())->find("descricao NOT LIKE 'Desbloqueio%' AND usuario = 1 AND created_at BETWEEN '" . date("Y-m-01") . "' AND '"  . date("Y-m-d") . "'", null, "SUM(valor)")->fetch();
        $pendente = (new Comissao())->find("usuario = 1 AND disponivel = 0", null, "SUM(valor)")->fetch();
        $saldo = (new Saldo())->find("usuario = 1")->fetch();
        $bloqueado = (new Comissao())->find("descricao like '%bloqueio%' AND usuario = 1", null, "SUM(valor) as valor")->fetch();

        foreach ($hoje->data() as $hoje) null;
        foreach ($ontem->data() as $ontem) null;
        foreach ($mes->data() as $mes) null;
        foreach ($pendente->data() as $pendente) null;

        echo $this->view->render("comissao/comissao-redrocket", [
            "title" => "Comissão Redrocket",
            "comissao" => $comissao,
            "hoje" => @$hoje,
            "ontem" => @$ontem,
            "mes" => @$mes,
            "pendente" => @$pendente,
            "comissao" => @$comissao,
            "saldo" => @$saldo->valor,
            "bloqueado" => @$bloqueado->valor,
            "count" => $comissaoCount,
            "paginador" => @$paginador,
            "paginas" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function taxasMoip($data)
    {
        $formaPagamento = $data["forma_pagamento"];
        $valorTotal = $data["valor_bruto"];
        $parcelas = $data["parcelas"];

        $taxaBoleto = 2.79;
        $taxaCartao = array(
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
        $custoTransacao = 0.4;

        if ($formaPagamento == 2)
            $valorTaxa = $taxaBoleto;
        else
            $valorTaxa = ($valorTotal * $taxaCartao[$parcelas]) + $custoTransacao;

        return number_format($valorTaxa, 2);
    }

    public function taxasPagarMe($data)
    {
        $formaPagamento = $data["forma_pagamento"];
        $valorTotal = $data["valor_bruto"];
        $parcelas = $data["parcelas"];

        $taxaBoleto = 2.79;
        $taxaCartao = array(
            1 => 0.0385,
            2 => 0.0468,
            3 => 0.0551,
            4 => 0.0634,
            5 => 0.0717,
            6 => 0.0800,
            7 => 0.0883,
            8 => 0.0966,
            9 => 0.1049,
            10 => 0.1132,
            11 => 0.1215,
            12 => 0.1299,
        );
        $custoTransacao = 0.9;

        if ($formaPagamento == 2)
            $valorTaxa = $taxaBoleto;
        else
            $valorTaxa = ($valorTotal * $taxaCartao[$parcelas]) + $custoTransacao;

        return number_format($valorTaxa, 2);
    }

    /** FAZ UMA VERIFICAÇÃO NAS VENDAS PARA VER SE EXISTE COMISSÕES NÃO LANÇADAS */
    public function verificaComissao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $vendas = (new Venda())->find("vendas.id NOT IN (SELECT comissao.venda FROM comissao WHERE comissao.venda IS NOT NULL) AND status_transacao = 4")->order("id ASC")->fetch(true);

        $i = 1;
        // var_dump($vendas);
        if ($vendas) {
            foreach ($vendas as $venda) {
                echo "<pre>";
                echo "################################################################################################### <br>";
                echo "###      <b style='font-size: 20px'>VERIFICAR COMISSÃO DA VENDA $venda->id - $venda->transacao</b>               ### <br>";
                echo "################################################################################################### <br>";


                echo "###<br>";
                echo "### count: <b>$i</b> <br>" .
                    "### produto: <b>$venda->produto</b> <br>" .
                    "### valor bruto: <b>R$ $venda->valor_bruto</b><br>" .
                    "### valor do produto: <b>R$ $venda->valor_produto</b><br>" .
                    "### valor líquido: <b>R$ $venda->valor_liquido</b><br>" .
                    "### Valor Redrocket: <b>R$ $venda->valor_redrocket</b><br>";
                "### Valor Frete: <b>R$ $venda->valor_frete</b><br>";

                echo "###<br>";
                echo "################################################################################################### <br>";

                /** ESTRUTURA BÁSICA CADASTRO DE COMISSÕES */
                $data["venda"] = $venda->id;
                $data["produto"] = $venda->produto;
                $data["data_pedido"] = $venda->data_finalizada;
                $data["tipo"] = 1;

                // if($venda->meio_pagamento == "Pix")
                //     $tempo_saque =  date("Y-m-d", strtotime("+2 days", strtotime($venda->data_finalizada)));
                // elseif ($venda->meio_pagamento == "Bradesco")
                //     $tempo_saque =  date("Y-m-d", strtotime("+3 days", strtotime($venda->data_finalizada)));

                echo "tempo de saque: " . @$tempo_saque;
                /** 
                 * USUARIO PREMIUM 
                 */

                echo "<br>";
                echo "*************************************************************************************************** <br>";
                echo "***                       VERIFICAR USUÁRIOS PREMIUNS                                           *** <br>";
                echo "*************************************************************************************************** <br>";

                $comissaoPremium = number_format($venda->valor_bruto * (1 / 100), 2);
                echo "***<br>";
                echo "*** COMISSÃO PREMIUM: <b>R$ $comissaoPremium</b> <br>";

                $usuarioPremium = new UsuarioPremium();

                /** AFILIADO PREMIUM */
                $afiliadoPremium = $venda->afiliado;
                $hoje = date("Y-m-d");
                $userPremium = $usuarioPremium->find("indicado = $venda->afiliado AND expiracao >= '$hoje'")->fetch();
                if ($userPremium) {
                    $indicandoAfiliadoPremium = $usuarioPremium->usuario;
                    $indicando[0] = $indicandoAfiliadoPremium;
                    echo "*** Afiliado premium <b>$indicandoAfiliadoPremium</b> <br>";
                }
                /** FIM AFILIADO PREMIUM */

                /** PRODUTOR PREMIUM */
                $produtorPremium = $venda->produtor;
                $userPremium = $usuarioPremium->find("indicado = $venda->produtor  AND expiracao >= '$hoje'")->fetch();
                if ($userPremium) {
                    $indicandoProdutorPremium = $userPremium->usuario;
                    $indicando[1] = $indicandoProdutorPremium;
                    echo "*** Produtor premium <b>$indicandoProdutorPremium</b> <br>";
                }
                /** FIM PRODUTOR PREMIUM */

                if (@$indicandoProdutorPremium || @$indicandoAfiliadoPremium) {
                    $venda->valor_liquido -= $comissaoPremium;
                }
                echo "*** Valor liquido <b>R$ $venda->valor_liquido</b> <br>";
                //var_dump($indicando);

                if (@$indicandoAfiliadoPremium) {
                    $afiliado = 1;
                } else {
                    $afiliado = 0;
                }

                if (@$indicandoProdutorPremium) {
                    $produtor = 1;
                } else {
                    $produtor = 0;
                }

                $somaIndicandos = @$afiliado + @$produtor;

                if ($somaIndicandos == 1) {
                    $data["usuario"] = @$indicando[0] ? $indicando[0] : $indicando[1];
                    $data["valor"] = $comissaoPremium;
                    $data["descricao"] = "Indicação Premium";
                    $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+30 days"));
                    $this->cadastroComissao($data);
                } else if ($somaIndicandos == 2) {
                    $data["valor"] = $comissaoPremium / 2;
                    $data["descricao"] = "Indicação Premium";
                    $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+30 days"));

                    $data["usuario"] = $indicando[0];
                    $this->cadastroComissao($data);

                    $data["usuario"] = $indicando[1];
                    $this->cadastroComissao($data);
                }

                echo "*** Produtor $produtor - Afiliado $afiliado <br>";

                echo "***<br>";
                echo "*************************************************************************************************** <br>";
                echo "</pre>";

                /** 
                 * FIM USUARIO PREMIUM 
                 */

                /** 
                 * TAXA REDROCKET 
                 */

                echo "<br><pre>";
                echo "*************************************************************************************************** <br>";
                echo "***                                      TAXA REDROCKET                                           *** <br>";
                echo "*************************************************************************************************** <br>";

                echo "***<br>";
                echo "*** count: <b>$i</b> <br>" .
                    "*** venda: <b>$venda->id</b> <br>" .
                    "*** valor redrocket: <b>R$ $venda->valor_redrocket</b><br>";

                $data["usuario"] = 1;
                $data["valor"] = $venda->valor_redrocket;
                $data["descricao"] = "Taxa Redrocket";
                $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+15 days", strtotime($venda->data_finalizada)));
                // date("Y-m-d", strtotime("+15 days"));
                $data["taxa_saque"] = 0;
                $this->cadastroComissao($data);

                if ($venda->valor_juros > 0) {
                    echo "<br>*** JUROS CARTÃO <br>";
                    $data["usuario"] = 1;
                    $data["valor"] = $venda->valor_juros;
                    $data["descricao"] = "Juros Cartão";
                    $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+15 days", strtotime($venda->data_finalizada)));
                    $data["taxa_saque"] = 0;

                    echo "*** juros do cartão: <b>R$ $venda->valor_juros</b><br>";
                    $this->cadastroComissao($data);
                }

                echo "***<br>";
                echo "*************************************************************************************************** <br>";
                echo "</pre>";

                /** 
                 * FIM TAXA REDROCKET
                 */

                /** 
                 * TAXAS OPERADORAS
                 */

                $data["forma_pagamento"] = $venda->forma_pagamento;
                $data["valor_bruto"] = $venda->valor_bruto;
                $data["parcelas"] = $venda->parcelas;

                $operadoras = array(
                    "Moip" => $this->taxasMoip($data),
                    "Bradesco" => 0,
                    "Pix" => 0,
                    "PagarMe" => $this->taxasPagarMe($data)
                );

                $valorOperadora = @$operadoras[$venda->meio_pagamento];

                echo "<br><pre>";
                echo "*************************************************************************************************** <br>";
                echo "***                                     TAXAS OPERADORAS                                        *** <br>";
                echo "*************************************************************************************************** <br>";

                echo "***<br>";
                echo "*** count: <b>$i</b> <br>" .
                    "*** venda: <b>$venda->id</b> <br>" .
                    "*** operadora: <b>$venda->meio_pagamento</b> <br>" .
                    "*** valor operadora: <b>R$ $valorOperadora</b><br>";

                $data["usuario"] = 1;
                $data["valor"] = -$valorOperadora;
                $data["descricao"] = "Taxa $venda->meio_pagamento";
                $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+15 days", strtotime($venda->data_finalizada)));
                $data["taxa_saque"] = 0;
                $this->cadastroComissao($data);

                echo "***<br>";
                echo "*************************************************************************************************** <br>";
                echo "</pre>";

                /** 
                 * FIM TAXAS OPERADORAS
                 */

                /** 
                 * COMISSÃO AFILIADO
                 */

                if ($venda->afiliado) {
                    echo "<br><pre>";
                    echo "*************************************************************************************************** <br>";
                    echo "***                                      TEM AFILIADO                                           *** <br>";
                    echo "*************************************************************************************************** <br>";

                    echo "***<br>";
                    echo "*** count: $i<br>" .
                        "*** venda: $venda->id<br>" .
                        "*** id afiliado: $venda->afiliado<br>" .
                        "*** Comissão afiliado: R$ $venda->comissao_afiliado<br>";

                    //$taxa_redrocket_saque = number_format($venda->comissao_afiliado * ($venda->dataAfiliado()->taxa_saque / 100), 2);
                    //$comissaoAfiliado = $venda->comissao_afiliado - $taxa_redrocket_saque;
                    /*echo "<br>*** Taxa redrocket saque: <b>R$ $taxa_redrocket_saque </b> - 
                <b>Comissao afiliado:</b> total <b>R$ $venda->comissao_afiliado</b> - liquido <b>R$ $comissaoAfiliado</b> ***<br>";*/

                    $data["usuario"] = $venda->afiliado;
                    $data["valor"] = $venda->comissao_afiliado;
                    $data["descricao"] = "Comissão afiliado";
                    $data["taxa_saque"] = 0; //$venda->dataAfiliado()->taxa_saque;

                    if ($venda->meio_pagamento == "Pix")
                        $tempo_saque =  date("Y-m-d", strtotime("+" . $venda->dataAfiliado()->tempo_pix . " days", strtotime($venda->data_finalizada)));
                    elseif ($venda->meio_pagamento == "Bradesco")
                        $tempo_saque =  date("Y-m-d", strtotime("+" . $venda->dataAfiliado()->tempo_boleto . " days", strtotime($venda->data_finalizada)));

                    $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+" . $venda->dataAfiliado()->tempo_saque . " days", strtotime($venda->data_finalizada)));

                    echo "*** Tempo de Saque: " . $venda->dataAfiliado()->tempo_saque . " dias <br>";
                    echo "*** data finalizada: $venda->data_finalizada <br>" .
                        "*** tempo saque " . $data["tempo_saque"] . "<br>";

                    $this->cadastroComissao($data);

                    echo "***<br>";
                    echo "*************************************************************************************************** <br>";
                    echo "</pre>";
                }

                /** 
                 * FIM COMISSÃO AFILIADO
                 */

                $coprodutores = (new CoProdutor())->find("aceito = 1 AND produto = $venda->produto AND data_limite <= '" . date("Y-m-d") . "'")->fetch(true);

                if ($coprodutores) { // TEM COPRODUTORES
                    $comissaoCoProdutor = array();
                    $somaComissao = 0;
                    $j = 1;
                    foreach ($coprodutores as $coprodutor) {

                        echo "<br><pre>";
                        echo "*************************************************************************************************** <br>";
                        echo "***                                 VERIFICAÇÃO COPRODUTORES                                    *** <br>";
                        echo "*************************************************************************************************** <br>";

                        echo "***<br>";
                        echo "*** Período: <b>$coprodutor->created_at (" . strtotime($coprodutor->created_at) . ") $coprodutor->data_limite</b> <br>";
                        echo "*** Data venda: <b>$venda->created_at (" . strtotime($venda->created_at) . ")</b> <br>";

                        if (strtotime($venda->created_at) >= strtotime($coprodutor->created_at)) {
                            echo "<br><br>***   <b>ÍNDICE: $j</b>   ***<br><br>";
                            echo "***   SUPERIOR    ***<br><br>";

                            /** 
                             * => SE COPRODUTOR 
                             */

                            if ($coprodutor->gerente == 0) {

                                /**
                                 * => SE COPRODUTOR
                                 * => TIPO COMISSAO == 0 (%)
                                 */

                                echo "* => SE COPRODUTOR <br>";

                                if ($coprodutor->tipo_comissao == 0) {
                                    $comissaoCoProdutor[$j] = ($venda->valor_liquido) * $coprodutor->comissao / 100;
                                    echo "* => SE COPRODUTOR * => TIPO COMISSAO == 0 (%) $coprodutor->comissao% - <b>R$ $comissaoCoProdutor[$j]</b><br>";
                                }

                                /**
                                 * => SE COPRODUTOR
                                 * => TIPO COMISSAO == 1 (R$)
                                 */

                                else {
                                    $comissaoCoProdutor[$j] = $coprodutor->comissao;
                                    echo "* => SE COPRODUTOR * => TIPO COMISSAO == 1 (R$) <b>R$ $comissaoCoProdutor[$j]</b> <br>";
                                }

                                /** 
                                 * => SE GERENTE
                                 */
                            } else {

                                /** 
                                 * => SE GERENTE
                                 * => A VENDA TEM AFILIADO
                                 */
                                echo "* => SE GERENTE <br>";

                                if ($venda->afiliado) {

                                    /**
                                     * => SE GERENTE
                                     * => A VENDA TEM AFILIADOS
                                     * => TIPO COMISSAO == 0 (%)
                                     */

                                    echo "* => SE GERENTE * => A VENDA TEM AFILIADO <br>";

                                    if ($coprodutor->tipo_comissao_afiliado == 0) {
                                        $comissaoCoProdutor[$j] = $venda->valor_liquido * $coprodutor->comissao_afiliados / 100;
                                        echo "* => SE GERENTE * => A VENDA TEM AFILIADOS * => TIPO COMISSAO == 0 (%) $coprodutor->comissao_afiliados% - <b>R$ $comissaoCoProdutor[$j]</b> <br>";
                                    }

                                    /**
                                     * => SE GERENTE
                                     * => A VENDA TEM AFILIADOS
                                     * => TIPO COMISSAO == 1 (R$)
                                     */

                                    else {
                                        $comissaoCoProdutor[$j] = $coprodutor->comissao_afiliados;
                                        echo "* => SE GERENTE * => A VENDA TEM AFILIADOS * => TIPO COMISSAO == 1 (R$) <b> R$ $comissaoCoProdutor[$j]</b> <br>";
                                    }
                                }

                                /** 
                                 * => SE GERENTE
                                 * => A VENDA NÃO TEM AFILIADO
                                 */

                                else {

                                    /**
                                     * => SE GERENTE
                                     * => A VENDA NÃO TEM AFILIADOS
                                     * => TIPO COMISSAO == 0 (%)
                                     */

                                    echo "* => SE GERENTE * => A VENDA NÃO TEM AFILIADOS  <br>";

                                    if ($coprodutor->tipo_comissao == 0) {
                                        $comissaoCoProdutor[$j] = $venda->valor_liquido * $coprodutor->comissao / 100;
                                        $somaComissao += $comissaoCoProdutor[$j];
                                        echo "* => SE GERENTE * => A VENDA NÃO TEM AFILIADOS * => TIPO COMISSAO == 0 (%) $coprodutor->comissao% - <b>R$ $comissaoCoProdutor[$j]</b> <br>";
                                    }

                                    /**
                                     * => SE GERENTE
                                     * => A VENDA NÃO TEM AFILIADOS
                                     * => TIPO COMISSAO == 1 (R$)
                                     */

                                    else {
                                        $comissaoCoProdutor[$j] = $coprodutor->comissao;
                                        echo "* => SE GERENTE * => A VENDA NÃO TEM AFILIADOS * => TIPO COMISSAO == 1 (R$) <b>R$ $coprodutor->comissao</b> <br>";
                                    }
                                } // FIM SE GERENTE => A VENDA NÃO TEM AFILIADO
                            } // FIM SE GERENTE

                            echo "<br><br>*** Comissão coprodutor: {$comissaoCoProdutor[$j]} <br><br>";
                            $comissaoCoProdutor[$j] = number_format($comissaoCoProdutor[$j], 2);

                            /*$taxa_redrocket_saque = number_format($comissaoCoProdutor[$j] * ($coprodutor->dataCoProdutor()->taxa_saque / 100), 2);
                        $comissaoCop = $comissaoCoProdutor[$j] - $taxa_redrocket_saque;
                        echo "<br>*** Taxa redrocket saque: <b>R$ $taxa_redrocket_saque </b> - 
                        <b>Comissao coprodutor:</b> total <b>R$ {$comissaoCoProdutor[$j]}</b> - liquido <b>R$ $comissaoCop</b> ***<br>";*/

                            $somaComissao += $comissaoCoProdutor[$j];
                            $data["usuario"] = $coprodutor->co_produtor;
                            $data["valor"] = $comissaoCoProdutor[$j];
                            $data["descricao"] = "Comissão coprodução";
                            $data["taxa_saque"] = 0; //$coprodutor->dataCoProdutor()->taxa_saque;

                            if ($venda->meio_pagamento == "Pix")
                                $tempo_saque =  date("Y-m-d", strtotime("+" . $coprodutor->dataCoProdutor()->tempo_pix . " days", strtotime($venda->data_finalizada)));
                            elseif ($venda->meio_pagamento == "Bradesco")
                                $tempo_saque =  date("Y-m-d", strtotime("+" . $coprodutor->dataCoProdutor()->tempo_boleto . " days", strtotime($venda->data_finalizada)));

                            $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+" . $coprodutor->dataCoProdutor()->tempo_saque . " days", strtotime($venda->data_finalizada)));

                            echo "*** Tempo saque: " . $coprodutor->dataCoProdutor()->tempo_saque . " dias <br>";
                            echo "*** data finalizada: $venda->data_finalizada<br>" .
                                "*** tempo saque: " . $data["tempo_saque"] . "<br>";
                            echo "*** Comissão coprodutor: <b>R$ $comissaoCoProdutor[$j]</b> <br>";

                            $this->cadastroComissao($data);

                            $j++;
                        }

                        echo "***<br>";
                        echo "*************************************************************************************************** <br>";
                        echo "</pre>";
                    } // FIM FOREACH

                } // FIM TEM COPRODUTORES

                /** 
                 * FIM COMISSÃO AFILIADO
                 */

                echo "<br><pre>";
                echo "*************************************************************************************************** <br>";
                echo "***                                      COMISSÃO PRODUTOR                                      *** <br>";
                echo "*************************************************************************************************** <br>";

                echo "***<br>";

                echo "*** count: $i<br>";
                echo "*** SOMA COMISSÃO COPRODUTORES: <b>R$ " . @$somaComissao . "</b> <br>";

                //echo "Comissão dos coprodutores R$ " . @$com . "<br>";
                $resto = $venda->valor_liquido - @$venda->comissao_afiliado - @$somaComissao;
                echo "*** Valor liquido: $venda->valor_liquido<br>" .
                    "*** Comissão afiliado: " . @$venda->comissao_afiliado . "<br>" .
                    "*** Soma comissão: " . @$somaComissao . " ***<br>";

                echo "***<br>";
                echo "*** Resto: $venda->valor_liquido - " . @$venda->comissao_afiliado . " - " . @$somaComissao . " = <b>R$ $resto </b><br><br>";

                /*$taxa_redrocket_saque = number_format($resto * ($venda->dataProdutor()->taxa_saque / 100), 2);
            $comissaoProd = $resto - $taxa_redrocket_saque;
            echo "<br>*** Taxa redrocket saque: <b>R$ $taxa_redrocket_saque </b> - 
            <b>Comissao produtor:</b> total <b>R$ $resto</b> - liquido <b>R$ $comissaoProd</b> ***<br>";*/

                echo "***<br>";
                echo "*** <b>COMISSÃO PRODUTOR </b><br>";
                $data["usuario"] = $venda->dataProduto()->usuario;
                $data["valor"] = $resto + $venda->valor_frete;
                $data["descricao"] = "Comissão Produtor";

                if ($venda->meio_pagamento == "Pix")
                    $tempo_saque =  date("Y-m-d", strtotime("+" . $venda->dataProdutor()->tempo_pix . " days", strtotime($venda->data_finalizada)));
                elseif ($venda->meio_pagamento == "Bradesco")
                    $tempo_saque =  date("Y-m-d", strtotime("+" . $venda->dataProdutor()->tempo_boleto . " days", strtotime($venda->data_finalizada)));

                $data["taxa_saque"] = 0; //$venda->dataProdutor()->taxa_saque;
                $data["tempo_saque"] = @$tempo_saque ? $tempo_saque : date("Y-m-d", strtotime("+" . $venda->dataProdutor()->tempo_saque . " days", strtotime($venda->data_finalizada)));

                echo "<br>*** tempo saque: " . $venda->dataProdutor()->tempo_saque . " dias <br>";
                echo "*** data finalizada $venda->data_finalizada<br>" .
                    "*** tempo saque: " . $data["tempo_saque"] . "<br>";

                $this->cadastroComissao($data);
                echo "<br>";

                echo "*** <b>resto: R$ {$data['valor']}</b>";
                $somaComissao = 0;
                $comissaoAfiliado = 0;
                //$comissaoCoProdutor[$j] = 0;

                echo "***<br>";
                echo "*************************************************************************************************** <br>";
                echo "</pre>";

                $i++;

                echo "<br>" .
                    "############################################# FIM #################################################" .
                    "<br><br>";

                $ven = (new Venda())->findById($venda->id);
                $ven->comissionada = 1;
                if ($ven->save()) {
                    echo "Venda atualizada <br>";
                } else {
                    echo $ven->fail()->getMessage();
                }
            } // FIM FOREACH VENDAS
        } else {
            echo "Não tem vendas sem comissão";
        }
    }

    public function cadastrarEstorno($data)
    {
        $venda = $data["venda"];
        $comissoes = (new Comissao())->find("venda = '$venda'")->fetch(true);
        echo "Venda $venda<br>";

        var_dump($comissoes);
        if ($comissoes) {
            foreach ($comissoes as $comissao) {
                if (stripos($comissao->descricao, "tx Taxa") === false && $comissao->descricao != "Estorno") {
                    $data["usuario"] = $comissao->usuario;
                    $data["venda"] = $comissao->venda;
                    $data["valor"] = -$comissao->valor;
                    $data["descricao"] = "Estorno";
                    $data["taxa_saque"] = 0;
                    $data["tipo"] = 1;
                    $data["tempo_saque"] = date("Y-m-d");
                    $data["data_pedido"] = $comissao->data_pedido;
                    $data["produto"] = $comissao->produto;

                    $this->cadastroComissao($data);
                    $this->verificarSaldo($data);
                }
            }
        }
    }

    public function atualizarEstorno($data)
    {
        $vendas = (new Venda())->find("status_transacao = 6 OR status_transacao = 7")->fetch(true);

        foreach ($vendas as $venda) {
            $comissoes = (new Comissao())->find("venda = '$venda->id'")->fetch(true);
            // echo "Venda $venda<br>";

            // var_dump($comissoes);
            if ($comissoes) {
                foreach ($comissoes as $comissao) {
                    if ($comissao->descricao != "Taxa Wirecard") {
                        $data["usuario"] = $comissao->usuario;
                        $data["venda"] = $comissao->venda;
                        $data["valor"] = -$comissao->valor;
                        $data["descricao"] = "Estorno";
                        $data["taxa_saque"] = 0;
                        $data["tempo_saque"] = date("Y-m-d");
                        $data["data_pedido"] = $comissao->data_pedido;
                        $data["produto"] = $comissao->produto;

                        $this->cadastroComissao($data);
                        $this->verificarSaldo($data);
                    }
                }
            }
        }
    }

    public function cadastroComissao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $comissao = new Comissao();

        $comissao->usuario      = $data["usuario"];
        $comissao->venda        = $data["venda"];
        $comissao->produto      = $data["produto"];
        $comissao->descricao    = @$data["descricao"];
        $comissao->valor        = $data["valor"];

        if (@$data["saldo"])
            $comissao->saldo        = $data["saldo"];

        $comissao->tempo_saque  = @$data["tempo_saque"]; // DATA PARA FICAR DISPONÍVEL
        $comissao->taxa  = @$data["taxa_saque"];
        $comissao->disponivel   = 0; // 0 (NÃO) - 1 (SIM) - 2 (SACADO - INSERIDO NA TABELA SALDO)
        $comissao->tipo         = @$data["tipo"]; // 1 (ENTRADA) - 2 (SAIDA)
        $comissao->data_pedido  = $data["data_pedido"];

        echo "***<br>";
        echo "*** <b>CADASTRO COMISSÃO</b><br>" .
            "*** usuario $comissao->usuario - id venda $comissao->venda" .
            "- produto $comissao->produto - comissao <b>R$ $comissao->valor</b> - descrição: $comissao->descricao<br>";

        if ($comissao->save()) {
            echo "<br>*** Salvo com sucesso <br>";
        } else {
            echo "<br>*** " . $comissao->fail()->getMessage() . "<br>";
        }
    }

    public function verificarSaldo($data)
{
    $amanha = date("Y-m-d", strtotime("+1 day"));

    // A) Seu filtro original (interseção)
    $qElig = (new Comissao())->find("tempo_saque < :amanha AND disponivel = :disp", "amanha={$amanha}&disp=0");

    // B) Só disponivel=0 (pendentes, qualquer data)
    $qPend = (new Comissao())->find("disponivel = :disp", "disp=0");

    // C) Só data (passados), qualquer disponivel
    $qPass = (new Comissao())->find("tempo_saque < :amanha", "amanha={$amanha}");

    // D) Pendurados no futuro: disponivel=0 E tempo_saque >= amanhã
    $qFutu = (new Comissao())->find("tempo_saque >= :amanha AND disponivel = :disp", "amanha={$amanha}&disp=0");

    // E) Já liberados/alterados: tempo_saque < amanhã E disponivel != 0
    $qJaLib = (new Comissao())->find("tempo_saque < :amanha AND disponivel != :disp", "amanha={$amanha}&disp=0");

    echo "<pre>";
    echo "Elegíveis (disp=0 & data<amanha): " . ($qElig->count() ?? 0) . "\n";
    echo "Pendentes (disp=0):              " . ($qPend->count() ?? 0) . "\n";
    echo "Passados (data<amanha):           " . ($qPass->count() ?? 0) . "\n";
    echo "Futuros pendentes (disp=0 & data>=amanha): " . ($qFutu->count() ?? 0) . "\n";
    echo "Passados já tratados (data<amanha & disp!=0): " . ($qJaLib->count() ?? 0) . "\n";

    // Amostras para entender:
    $sampleFutu = $qFutu->fetch(true) ?? [];
    if (!empty($sampleFutu)) {
        echo "Amostra FUTUROS PENDENTES (até 5):\n";
        $i=0; foreach ($sampleFutu as $c) { echo "  id={$c->id} | tempo_saque={$c->tempo_saque} | disp={$c->disponivel}\n"; if(++$i>=5) break; }
    }

    $samplePass = $qPass->fetch(true) ?? [];
    if (!empty($samplePass)) {
        echo "Amostra PASSADOS (até 5):\n";
        $i=0; foreach ($samplePass as $c) { echo "  id={$c->id} | tempo_saque={$c->tempo_saque} | disp={$c->disponivel}\n"; if(++$i>=5) break; }
    }
    echo "</pre>";

    // Se não há elegíveis, encerra (evita warning no foreach)
    $comissoes = $qElig->fetch(true) ?? [];
    if (empty($comissoes)) {
        echo "Nenhuma comissão elegível hoje.\n";
        return;
    }

    // --- processamento normal abaixo ---
    foreach ($comissoes as $comissao) {
        $valor      = (float)$comissao->valor;
        $antecipado = (float)$comissao->antecipado;
        $total      = round($valor - $antecipado, 2);

        echo "***************************************************<br>";
        echo "USUÁRIO: " . $comissao->dataUsuario()->nome . " - " . $comissao->dataUsuario()->taxa_saque . "<br>";
        echo "VALOR: " . number_format($valor, 2, ",", ".") . "<br>";
        echo "TOTAL: " . number_format($total, 2, ",", ".") . "<br><br>";

        $comm = (new Comissao())->findById($comissao->id);
        $comm->disponivel = 2;
        $comm->save();

        $dados["usuario"] = $comissao->usuario;
        $dados["valor"]   = $total;
        $this->cadastroSaldo($dados);
    }
}

    


    public function cadastroSaldo($data)
    {
        $usuario = (new Saldo())->find("usuario = " . $data["usuario"])->fetch();
        // echo "SALDO ATUAL: " . @$usuario->valor."<br>";

        if ($usuario) {
            $saldo = (new Saldo())->findById($usuario->id);
            $saldo->valor = number_format($usuario->valor + $data["valor"], 2, ".", "");
            // $valor = $usuario->valor + $data["valor"];
            // echo "NOVO SALDO: " . $saldo->valor . "<br>";

            if ($saldo->save()) {
                echo "Saldo atualizado";
            } else {
                echo $saldo->fail()->getMessage();
            }
        } else {
            $saldo = new Saldo();
            $saldo->usuario = $data["usuario"];
            $saldo->valor = $data["valor"];
            //echo $saldo->usuario . " - " . $saldo->valor . "<br>";
            if ($saldo->save()) {
                echo "Saldo inserido";
            } else {
                echo $saldo->fail()->getMessage();
            }
        }
        // echo "<br><br>";
    }

    public function verificaSaldo($data)
    {
        $usuario = (new Saldo())->find("usuario = " . $data["usuario"])->fetch();
        echo $usuario->valor . " " . $data["valor"];
        if ($usuario != "" && $data["valor"] <= $usuario->valor) {
            return true;
        } else {
            return false;
        }
    }

    public function antecipacao($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("antecipacao");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'antecipacao'")->fetch();

        $_SESSION["comissoes"] = array(0);
        $id = $this->section->getId();
        $tipo = $_SESSION["tipo"];
        $usuario = (new Usuario())->findById($id);
        $vendas = new Venda();

        $antecipacao = (new Antecipacao())->findById(1);
        $saldo = (new Saldo())->find("usuario = $id")->fetch();
        $dias_antecipar = $antecipacao->dias_antecipar;
        $periodo = $antecipacao->periodo_antecipacao;

        //echo "$dias_antecipar dias - $periodo dias<br>";
        $desde = date("Y-m-d", strtotime("+" . $dias_antecipar . " days"));
        $ate = date("Y-m-d", strtotime("+" . ($dias_antecipar + $periodo) . " days"));

        $pendente = (new Comissao())->find("(antecipado = 1 OR antecipado = 0) and disponivel = 0 and tipo = 1 and usuario = $id", null, "SUM(valor) as soma")->fetch();
        //$comissao = (new Comissao())->find("tipo = 1 AND disponivel = 0 and usuario = $id AND tempo_saque BETWEEN '$desde' AND '$ate'")->fetch(true);

        /** 
         * VERIFICAR PERFIL DE USUÁRIO 
         */

        $volume = $vendas->find("produtor = $id OR afiliado = $id")->fetch(true);
        $ved = array();
        $vendas = array();
        $cont = 1;
        $liberado = 0;

        // VERIFICA SE EXISTE VENDA
        if ($volume) {
            $diferença = strtotime(date("Y-m-d")) - strtotime($usuario->created_at);
            @$dados["tempoPlataforma"] = floor($diferença / (60 * 60 * 24 * 30));

            // VERIFICA O TEMPO DE PLATAFORMA
            if (@$dados["tempoPlataforma"] >= 2) {
                foreach ($volume as $volume) {
                    $ved[$cont] = $volume->created_at;
                    $diff = strtotime($volume->created_at) - strtotime($cont == 1 ? $volume->created_at : @$ved[$cont - 1]);
                    $dado[$cont]["diff"] = floor($diff / (60 * 60 * 24));
                    @$dados["soma"] += $dado[$cont]["diff"];
                    $cont++;

                    $vendas[$cont]["status_transacao"] = $volume->status_transacao;
                }

                // CALCULA O TOTAL DE VENDAS PAGAS + DEVOLVIDAS + CHARGEBACKS + CONCLUÍDAS
                foreach ($vendas as $vend) {
                    if ($vend["status_transacao"] == 4 || $vend["status_transacao"] == 6 || $vend["status_transacao"] == 7 || $vend["status_transacao"] == 8) {
                        @$dados["totalPDCC"] += 1;
                    }
                }

                // CALCULA O TOTAL DE VENDAS DEVOLVIDAS + CHARGEBACKS
                foreach ($vendas as $vend1) {
                    if ($vend1["status_transacao"] == 6 || $vend1["status_transacao"] == 7) {
                        @$dados["totalDC"] += 1;
                    }
                }

                $dados["volumeVendas"] = count($ved);
                @$dados["taxaReembolso"] = $dados["totalDC"] / $dados["totalPDCC"];
                if (@$dados["taxaReembolso"] > 40) {
                    $liberado = 0;
                } else {

                    $comissao = (new Comissao())->find("antecipado = 0 and disponivel = 0 and tipo = 1 and usuario = $id AND tempo_saque BETWEEN '$desde' AND '$ate'")->fetch(true);
                    $comissaoLib = (new Comissao())->find("antecipado = 0 and disponivel = 0 and tipo = 1 and usuario = $id AND tempo_saque BETWEEN '$desde' AND '$ate'", null, "SUM(valor) as valor")->fetch();
                    $valor = array();
                    $valor[0] = 0;
                    $i = 1;
                    if ($comissao) {
                        $lib = $comissaoLib->valor * 0.5;
                        //echo "liberado $comissaoLib->valor - $lib | ";
                        foreach ($comissao as $comissao) {
                            $valor[$i] = $comissao->valor;
                            //echo "\n\nsoma anterior " .@$soma . " \n\n<br>";
                            @$soma += $valor[$i];
                            //echo "COMISSÃO R$ " . $valor[$i] . " (soma $soma) \n<br>";

                            if ($soma > $lib) {
                                $soma -= $valor[$i];
                                $_SESSION["comissoes"][$i - 1] = array($comissao->id, $comissao->valor, 0);
                            } else {
                                $_SESSION["comissoes"][$i - 1] = array($comissao->id, $comissao->valor, 1);
                                //echo "comissão id " . $comissao->id . " \n\n<br>";
                            }
                            $i++;
                        }
                        $liberado = $soma;
                        //echo "SOMA $soma <br>";
                    }
                }

                @$dados["frequenciaVendas"] = $dados["soma"] / $dados["volumeVendas"];
            }
            /** FIM TEMPO DE PLATAFORMA */
        } else {
            $liberado = 0;
        }
        /** FIM VENDA */

        /** 
         * FIM VERIFICAR PERFIL DE USUÁRIO 
         */
        $taxa = number_format($liberado * ($antecipacao->taxa_antecipacao / 100), 2);
        $_SESSION["liberado"] = $liberado;
        $_SESSION["pendente"] = $pendente->soma;
        $_SESSION["taxa"]     = $taxa;

        /** ASSINATURAS */
        $assinaturas = (new Assinatura())->find("status = 1 AND (proxima_antecipacao IS NULL OR proxima_antecipacao <= '" . date("Y-m-d") . "') AND data_limite IS NOT NULL AND produtor = " . $this->section->getId())->fetch(true);
        $assinaturasAntecipadas = (new Assinatura())->find("parcelas_antecipadas IS NOT NULL AND produtor = " . $this->section->getId(), null, "SUM(valor_antecipado) as antecipado")->fetch();
        $assinaturasRestante = (new Assinatura())->find("parcelas_antecipadas IS NOT NULL AND produtor = " . $this->section->getId())->fetch(true);
        $devendo = (new Comissao())->find("descricao LIKE '%Antecipação parcelas assinatura%' and disponivel = 0 AND usuario = " . $this->section->getId(), null, "SUM(valor) as devendo")->fetch();
        $vendas = new Venda();
        $coprodutor = new CoProdutor();
        /** FIM ASSINATURAS */

        echo $this->view->render("comissao/antecipacao", [
            "title" => "Antecipação",
            "section" => @$section,
            "permissao" => @$permissao,
            "pendente" => $pendente,
            "liberado" => $liberado,
            "comissao" => @$comissao,
            "antecipacao" => $antecipacao,
            "saldo" => $saldo,
            "dados" => @$dados,
            "assinaturas" => @$assinaturas,
            "assinaturasRestante" => @$assinaturasRestante,
            "assinaturasAntecipadas" => @$assinaturasAntecipadas,
            "devendo" => @$devendo,
            "vendas" => @$vendas,
            "coprodutor" => @$coprodutor,
        ]);
    }

    public function antecipar($data)
    {
        $id = $this->section->getId();
        $usuario = (new Usuario())->findById($id);
        $antecipacao = (new Antecipacao())->findById(1);
        $saldo = (new Saldo())->find("usuario = $id")->fetch();

        $i = 1;
        foreach ($_SESSION["comissoes"] as $comissoes) {
            //var_dump($comissoes[0]);
            $com = (new Comissao())->findById($comissoes[0]);

            /**
             * 0 = não antecipado
             * 1 = participou de algum processo de antecipação
             * 2 = antecipado
             */
            if (@$comissoes[2] == 1)
                $com->antecipado = 2;
            else
                $com->antecipado = 1;
            if ($com->save()) {
                @$count += 1;
                //echo "Salvo com sucesso $count<br>";
            } else {
                $dados["email"] = "dev.ti@redrocket.com.br";
                $dados["nome"] = "RedRocket";
                $dados["assunto"] = "Erro antecipação etapa 1";
                $dados["mensagem"]  = "Comissão " . $comissoes[0] . "<br>";
                $dados["mensagem"] .= "Usuário $id<br>";
                $dados["mensagem"]  = "Erro " . $com->fail()->getMessage() . "<br>";
                $enviaEmail = (new Emails())->enviaPHPMAILER($dados);
                echo "Erro " . $com->fail()->getMessage() . "<br>";
                @$count += 0;
            }
            //echo $count . " $i<br>";

            $i++;
        }
        //echo $count . " $i<br>";

        if (@$count != ($i - 1)) {
            echo "Ocorreu um erro na solicitação. Entre em contato com o suporte e informe o usuário $id <br>";
        } else {
            echo "Processando solicitação...";

            $taxa = $_SESSION["taxa"];
            $liquido = $_SESSION["liberado"] - $taxa;

            $comissao1 = new Comissao();
            $comissao1->usuario = $id;
            $comissao1->descricao = "Antecipação";
            $comissao1->valor = $liquido;
            $comissao1->tempo_saque = date("Y-m-d");
            $comissao1->data_pedido = date("Y-m-d");
            $comissao1->disponivel = 2;
            $comissao1->tipo = 3;
            $comissao1->save();

            $saldo->valor += $liquido;
            $saldo->save();

            $comissaoRedrocket = new Comissao();
            $comissaoRedrocket->usuario = 1;
            $comissaoRedrocket->descricao = "Taxa antecipação U$id";
            $comissao1->usuario = $id;
            $comissaoRedrocket->valor = $taxa;
            $comissaoRedrocket->tempo_saque = date("Y-m-d");
            $comissaoRedrocket->data_pedido = date("Y-m-d");
            $comissaoRedrocket->disponivel = 0;
            $comissaoRedrocket->tipo = 1;
            $comissaoRedrocket->save();
        }
    }

    public function anteciparAssinatura($data)
    {
        // var_dump($data);

        $periodicidade = array(
            "Semanal"       => [7, "days"],
            "Mensal"        => [1, "months"],
            "Bimestral"     => [2, "months"],
            "Trimestral"    => [3, "months"],
            "Semestral"     => [6, "months"],
            "Anual"         => [1, "years"],
            "Trienal"       => [3, "years"]
        );

        if (@$data["assinatura"]) {
            $i = 0;
            $erro = 0;
            $erro2 = 0;
            foreach ($data["assinatura"] as $ass) {
                $assinatura = (new Assinatura())->findById($ass);
                $assinatura->parcelas_antecipadas += $data["parcelas_antecipadas"][$i];
                $assinatura->valor_antecipado += $data["valor_antecipado"][$i];
                $assinatura->data_antecipacao = date("Y-m-d");
                $assinatura->proxima_antecipacao = date('Y-m-d', strtotime("+" . $assinatura->parcelas_antecipadas + 1 . @$periodicidade[$assinatura->recorrencia][1], strtotime($assinatura->proxima_cobranca)));
                // echo "<br> $assinatura->proxima_antecipacao - $assinatura->valor_antecipado - $assinatura->id<br>";
                if ($assinatura->save()) {
                    $ok = 1;
                } else {
                    $erro += 1;

                    $arquivo = fopen(DIRETORIO . "logs/antecipacao.txt", 'a+');

                    if ($arquivo == false)
                        die('Não foi possível criar o arquivo.');

                    $texto = "Usuario: {$this->section->getId()} | {$_SESSION["nome"]}\n";
                    $texto .= "Assinatura: {$assinatura->id}\n";
                    $texto .= "Mensagem: {$assinatura->fail()->getMessage()}\n";
                    $texto .= "*********************************************************\n";
                    fwrite($arquivo, $texto . "\n");
                    fclose($arquivo);
                }

                // echo "$assinatura->recorrencia - ";
                for ($j = 0; $j < $assinatura->parcelas_antecipadas; $j++) {

                    $dt = date('Y-m-d', strtotime("+" . $j + 1 . @$periodicidade[$assinatura->recorrencia][1], strtotime($assinatura->proxima_cobranca)));

                    // echo "<br> $dt <br>";

                    $comissao = new Comissao();
                    $comissao->usuario = $assinatura->produtor;
                    $comissao->produto = $assinatura->produto;
                    $comissao->descricao = "Antecipação parcelas assinatura";
                    $comissao->valor = number_format(- ($data["valor_antecipado"][$i] / $data["parcelas_antecipadas"][$i]), 2, ".", "");
                    $comissao->tempo_saque = @$dt;
                    $comissao->taxa = 5.49;
                    $comissao->tipo = 3;
                    $comissao->disponivel = 0;
                    $comissao->data_pedido = date("Y-m-d");
                    if ($comissao->save()) {
                        $ok = 1;
                    } else {
                        $erro2 += 1;

                        $arquivo = fopen(DIRETORIO . "logs/antecipacao-comissao.txt", 'a+');

                        if ($arquivo == false)
                            die('Não foi possível criar o arquivo.');

                        $texto = "Usuario: {$this->section->getId()} | {$_SESSION["nome"]}\n";
                        $texto .= "Assinatura: {$assinatura->id}\n";
                        $texto .= "Mensagem: {$comissao->fail()->getMessage()}\n";
                        $texto .= "*********************************************************\n";
                        fwrite($arquivo, $texto . "\n");
                        fclose($arquivo);
                    }
                    // echo $comissao->valor;
                }
                $i++;
                // echo "<br>";
            }
            if ($erro == 0 && $erro2 == 0) {
                echo "Valores atualizadas com sucesso...<br>";
            } else {
                echo "Entre em contato com suporte.oficial@redrocket.com.br e informe o ocorrido.";
                exit;
            }
        }

        $id = $this->section->getId();
        $saldo = (new Saldo())->find("usuario = $id")->fetch();
        $comissao1 = new Comissao();
        $comissao1->usuario = $id;
        $comissao1->descricao = "Antecipação assinatura";
        $comissao1->valor = $data["total_antecipado"];
        $comissao1->tempo_saque = date("Y-m-d");
        $comissao1->data_pedido = date("Y-m-d");
        $comissao1->disponivel = 2;
        $comissao1->tipo = 3;

        if ($comissao1->save()) {
            $st["com"] = 1;
        } else {
            $st["com"] = 0;
        }

        $saldo->valor += $data["total_antecipado"];
        if ($saldo->save()) {
            $st["sal"] = 1;
        } else {
            $st["sal"] = 0;
        }

        $comissaoRedrocket = new Comissao();
        $comissaoRedrocket->usuario = 1;
        $comissaoRedrocket->descricao = "Taxa antecipação assinatura U$id";
        $comissaoRedrocket->antecipado = $id;
        $comissaoRedrocket->valor = $data["taxa_antecipacao"];
        $comissaoRedrocket->tempo_saque = date("Y-m-d");
        $comissaoRedrocket->data_pedido = date("Y-m-d");
        $comissaoRedrocket->disponivel = 0;
        $comissaoRedrocket->tipo = 1;
        if ($comissaoRedrocket->save()) {
            $st["octa"] = 1;
        } else {
            $st["octa"] = 0;
        }

        if ($st["com"] == 1 && $st["sal"] == 1 && $st["octa"]) {
            echo "Antecipação realizada com sucesso";
        } else {
            echo "Ocorreu um erro ao solicitar antecipação<br>";
            echo "Status  da solicitação " . $st["com"] . $st["sal"] . $st["octa"];
        }
    }

    public function saque($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("saques_solicitados");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'saques_solicitados'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"] ? $data["page"] : 1;
        $offset = 30;

        $filtros = $this->filtros();

        if ($filtros)
            $filtros .= " AND ";

        if (@$_SESSION["tipo"] == 1 || @$_SESSION["tipo"] == 2) {
            $comissaoCount = (new Comissao())->find($filtros . "tipo = 2")->count();
            $paginas = ceil($comissaoCount / $offset);
            $comissao = (new Comissao())->find($filtros . "tipo = 2")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        } else {
            $comissaoCount = (new Comissao())->find($filtros . "tipo = 2 and usuario = " . $this->section->getId())->count();
            $paginas = ceil($comissaoCount / $offset);
            $comissao = (new Comissao())->find($filtros . "tipo = 2 and usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        }

        echo $this->view->render("comissao/saques", [
            "title" => "Saques Solicitados",
            "section" => @$section,
            "permissao" => @$permissao,
            "comissao" => $comissao,
            "paginador" => $paginador,
            "paginas" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function filtros()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];
        $inicio_finalizada = @$_GET["inicio_finalizada"];
        $fim_finalizada = @$_GET["fim_finalizada"];
        $status = @$_GET["status_saque"];
        $nome = @$_GET["nome"];
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

        if ($status != "") {
            $sql[] = "status = $status";
        }

        if ($nome != "") {
            $sql[] = "usuario IN ( SELECT usuario.id FROM usuario WHERE nome LIKE '%$nome%' OR email LIKE '%$nome%')";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function solicitarSaque2($data)
    {
        // var_dump($data);
        $data["valor"] = str_replace(".", "", $data["valor"]);
        $data["valor"] = str_replace(",", "", $data["valor"]);

        echo number_format($data["valor"], 2, ".", "");
    }

    public function solicitarSaque($data)
    {
        if (!@$data["conta"]) {
            echo "Informe a conta bancária";
            exit;
        }

        $taxa = (new Taxa())->findById(1);
        $data["valor"] = str_replace(".", "", $data["valor"]);
        $data["valor"] = str_replace(",", ".", $data["valor"]);

        if (@$data["valor"] >= $taxa->saque_minimo) :
            $limiteSaque = @$data["saqueMax"] - (-@$data["saqueMes"]);
            if ($data["tipo_doc"] == 1 && $limiteSaque < @$data["saqueMin"] && @$limiteSaque > @$data["saqueMax"]) :
                echo "Você atingiu o limite mensal de saque";
                exit;
            endif;
        endif;

        $saque = new Comissao();

        if (@$data["valor"] > @$data["saldo"]) {
            $data["valor"] = @$data["saldo"];
        }

        $saqueDia = (new Comissao())->find("tipo = 2 and status = 1 AND usuario = " . @$data["usuario"] . " AND created_at BETWEEN '" . date("Y-m-d 00:00:00") . "' AND '"  . date("Y-m-d H:i:s") . "'")->count();

        if (@$saqueDia >= 1 && $data["valor"] < 700) {
            $comissao = new Comissao();

            $comissao->usuario      = 1;
            $comissao->descricao    = "Segundo saque";
            $comissao->valor        = 3.8;

            $comissao->tempo_saque  = date("Y-m-d");
            $comissao->taxa  = 0;
            $comissao->disponivel   = 0;
            $comissao->tipo         = 1;
            $comissao->data_pedido  = date("Y-m-d");

            $comissao->save();

            $retorno["taxa_redrocket"] = $comissao->valor;
        }

        $saque->usuario = @$data["usuario"];
        $saque->usuario_ultima_alteracao = $this->section->getId();
        $saque->valor = -$data["valor"] + @$comissao->valor;
        $saque->descricao = "Solicitação de saque";
        $saque->saldo = @$data["saldo"];
        $saque->tipo = 2;
        $saque->conta = @$data["conta"];
        $saque->status = 1;

        $data_saque = strtotime(date("H:i")) >= '17:00' ? date("Y-m-d", strtotime("+3 days")) :  date("Y-m-d", strtotime("+2 days"));

        if (date("D", strtotime($data_saque)) == "Sun") {
            $data_saque = date("Y-m-d", strtotime("+1 day", strtotime($data_saque)));
        } else if (date("D", strtotime($data_saque)) == "Sat") {
            $data_saque = date("Y-m-d", strtotime("+2 day", strtotime($data_saque)));
        }

        $saque->tempo_saque = $data_saque;
        $saque->taxa  = 0;
        $saque->data_pedido = date("Y-m-d");

        $dados["usuario"] = $saque->usuario;
        $dados["valor"] = $saque->valor;
        $this->cadastroSaldo($dados);

        if ($saque->save()) {
            echo "Salvo com sucesso";

            $data["email"] = "suporte.oficial@redrocket.com.br";
            $data["assunto"] = "Solicitação de Saque";
            $data["mensagem"] = "Nova Solicitação de saque <br>" .
                "ID da solicitação: $saque->id <br>" .
                "Valor: R$" . number_format($saque->valor, 2, ",", ".") . " <br>Usuário: {$saque->dataUsuario()->nome}";

            if (AMBIENTE == "produção")
                $enviaEmail = (new Emails())->enviaSendGrid($data);
        } else {
            echo $saque->fail()->getMessage();
        }
    }

    public function saqueRedrocket($data)
    {

        $saque = new Comissao();

        if (@$data["valor"] > @$data["saldo"]) {
            $data["valor"] = @$data["saldo"];
        }

        $saque->usuario = 1;
        $saque->usuario_ultima_alteracao = $this->section->getId();
        $saque->valor = -$data["valor"];
        $saque->descricao = @$data["descricao"];
        $saque->saldo = @$data["saldo"];
        $saque->tipo = 2;
        $saque->status = 1;
        $saque->tempo_saque = date("Y-m-d H:i:s");
        $saque->taxa  = 0;
        $saque->data_pedido = date("Y-m-d");

        $dados["usuario"] = $saque->usuario;
        $dados["valor"] = $saque->valor;
        $this->cadastroSaldo($dados);

        if ($saque->save()) {
            echo "Salvo com sucesso";

            $data["email"] = "master@redrocket.com.br";
            $data["assunto"] = "Solicitação de Saque";
            $data["mensagem"] = "Nova Solicitação de saque <br>" .
                "ID da solicitação: $saque->id <br>" .
                "Valor: R$" . number_format($saque->valor, 2, ",", ".") . " <br>Usuário: {$saque->dataUsuario()->nome}";
            $enviaEmail = (new Emails())->enviaSendGrid($data);
        } else {
            echo $saque->fail()->getMessage();
        }
    }

    public function statusSaque($data)
    {
        $saque = (new Comissao())->findById($data["id"]);

        $saque->status = $data["status"];
        $saque->codigo = $data["codigo"];
        $saque->observacao = $data["observacao"];
        $saque->descricao = "Solicitação aprovada";
        
        $status = array(
            1 => "Pendente",
            2 => "Aprovado",
            3 => "Reprovado",
            4 => "Bloqueado"
        );

        $value = str_replace(array('.', ',', '-'), '', $saque->valor);


        $user = (new Usuario())->findById($saque->usuario);

        if ( ! $user ) {
            echo "Usuário não encontrado";
            return;
        }

        if ($saque->status == 2) {

            $walletId = $user->winpay_conta ?: $user->pagarme_conta;

            if (!$walletId) {
                echo "Conta de wallet não encontrada para este usuário.";
                return;
            }

            $resultadoSaque = (new WinPay)->saque($walletId, $value);

            if (!$resultadoSaque) {
                echo "Erro ao solicitar saque, tente novamente mais tarde.";
                return;
            }

            if ((isset($resultadoSaque->success) && !$resultadoSaque->success) || isset($resultadoSaque->error)) {
                echo "Erro ao solicitar saque, entre em contato com o suporte RedRocket";
                return;
            }

        }

        $data["email"] = $saque->dataUsuario()->email;
        $data["assunto"] = "Saque " . $status[$saque->status];
        $data["mensagem"] = "Sua solicitação de saque foi " . $status[$saque->status] . " <br> Entre na RedRocket para ver mais detalhes";
        $enviaEmail = (new Emails())->enviaSendGrid($data);

        if ($saque->status == 3) {
            $comissao = new Comissao();
            $comissao->usuario = $saque->usuario;
            $comissao->descricao = "Devolução solicitação de saque";
            $comissao->valor = $saque->valor * -1;
            $comissao->tempo_saque = date("Y-m-d");
            $comissao->data_pedido = date("Y-m-d");
            $comissao->tipo = 1;
            $comissao->disponivel = 2;
            $comissao->save();

            $saldo = (new Saldo())->find("usuario = $saque->usuario")->fetch();
            $saldo->valor += $saque->valor * -1;
            $saldo->save();
        }

        if ($saque->save()) {
            echo "Atualizado com sucesso";
        } else {
            echo $saque->fail()->getMessage();
        }
    }

    public function banco($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $section = new Section();
        (new Section())->permissaoModulo("conta_bancaria");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'conta_bancaria'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"] ? $data["page"] : 1;
        $offset = 30;

        $filtros = $this->filtroBanco();

        if ($filtros) {
            $filtro = $filtros . " AND ";
        }

        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) {
            $bancosCount = (new Banco())->find(@$filtros)->count();
            $paginas = ceil($bancosCount / $offset);
            $bancos = (new Banco())->find(@$filtros)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        } else {
            $bancosCount = (new Banco())->find(@$filtro . "usuario = " . $this->section->getId())->count();
            $paginas = ceil($bancosCount / $offset);
            $bancos = (new Banco())->find(@$filtro . "usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
        }
        $usuario = new UsuarioDados();

        //var_dump($bancos);
        echo $this->view->render("comissao/banco", [
            "title" => "Contas Bancárias",
            "section" => @$section,
            "permissao" => @$permissao,
            "bancos" => $bancos,
            "paginador" => @$paginador,
            "paginas" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "usuarioDados" => @$usuario,
        ]);
    }

    public function filtroBanco()
    {
        $conta =       @$_GET["conta"];
        $usuario =      @$_GET["usuario"];
        $sql = array();

        if ($conta != "") {
            $sql[] = "conta = '$conta' OR cpf = '$conta'";
        }

        if ($usuario != "") {
            $sql[] = "usuario IN ( SELECT usuario.id FROM usuario WHERE nome LIKE '%$usuario%' OR login = '$usuario' OR email LIKE '%$usuario%')";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function cadastroBanco2($data)
    {
        $banco = new Banco();

        $banco->usuario = $this->section->getId();
        $banco->titular = @$data["titular"];
        $banco->cpf = @$data["cpf"];
        $banco->banco = @$data["banco"];
        $banco->agencia = @$data["agencia"];
        $banco->conta = @$data["conta"];
        $banco->tipo = @$data["tipo"];
        $banco->chave_pix = @$data["chave_pix"];
        $banco->status = 1;

        $files = new File();
        if (@$_FILES["imagem"]["name"]) {
            $banco->imagem_conta = @$files->upload($_FILES["imagem"], PASTA . "banco/", 5);
        }

        exit;
        if ($banco->save()) {
            header("Location: " . url("banco?msg=Cadastrado com sucesso&classCss=bg-success"));
        } else {
            $msg = $banco->fail()->getMessage();
            header("Location: " . url("banco?msg=$msg&classCss=bg-danger"));
        }
    }

    public function cadastroBanco($data)
    {
        $banco = new Banco();

        $banco->usuario = $this->section->getId();
        $banco->titular = @$data["titular"];
        $banco->cpf = @$data["cpf"];
        $banco->banco = @$data["banco"];
        $banco->agencia = @$data["agencia"];
        $banco->digito_agencia = @$data["digito_agencia"];
        $banco->conta = @$data["conta"];
        $banco->digito_conta = @$data["digito_conta"];
        $banco->tipo = @$data["tipo"];
        $banco->chave_pix = @$data["chave_pix"];
        $banco->renda_mensal = !empty($data["renda_mensal"]) ? (int)round((float)str_replace(',', '.', $data["renda_mensal"]) * 100) : null;
        $banco->ocupacao = @$data["ocupacao"];
        $banco->receita_anual = !empty($data["receita_anual"]) ? (int)round((float)str_replace(',', '.', $data["receita_anual"]) * 100) : null;
        $banco->status = 1;

        $bancos = $banco->find("principal == 1 && usuario = " . $this->section->getId())->fetch();
        if (!$bancos) {
            $banco->principal = $data["principal"];
        }

        $files = new File();
        if (@$_FILES["imagem"]["name"]) {
            $banco->imagem_conta = @$files->upload($_FILES["imagem"], PASTA . "banco/", 5);
        }

        /*if($banco->cpf != @$data["cpf_usuario"])
            $banco->status = 1;
        else
            $banco->status = 3;*/

        //var_dump($data);
        if ($banco->imagem_conta == "") {
            header("Location: " . url("banco?msg=Necessário enviar a imagem da conta. Máximo 5 mb. Somente imagem do tipo .jpg, .jpeg, .png ou documento PDF&classCss=bg-danger"));
            exit;
        }

        $data["nome"] = $data["titular"];
        $data["email"] = $_SESSION["email"];
        $data["id"] = $this->section->getId();

        // Enriquece $data com campos do perfil do usuário (endereço, nascimento, dados PJ)
        $ud = (new UsuarioDados())->find("usuario = " . $this->section->getId())->fetch();
        if ($ud) {
            $data["data_nascimento"]  = $data["data_nascimento"]  ?: $ud->data_nascimento;
            $data["renda_mensal"]     = $banco->renda_mensal;
            $data["receita_anual"]    = $banco->receita_anual;
            $data["telefone"]         = @$data["telefone_pj"] ?: $ud->telefone;
            $data["cep"]              = $ud->cep;
            $data["rua"]              = $ud->rua;
            $data["numero"]           = $ud->numero;
            $data["complemento"]      = $ud->complemento;
            $data["bairro"]           = $ud->bairro;
            $data["cidade"]           = $ud->cidade;
            $data["estado"]           = $ud->estado;
            // Prefere o valor digitado no formulário; só usa usuario_dados como fallback se não for um documento (CNPJ/CPF)
            $razaoForm     = trim((string)@$data["razao_social_pj"]);
            $fantaForm     = trim((string)@$data["nome_fantasia_pj"]);
            $razaoDb       = preg_match('/^\d+$/', trim((string)$ud->razao_social)) ? '' : $ud->razao_social;
            $fantaDb       = preg_match('/^\d+$/', trim((string)$ud->nome_fantasia)) ? '' : $ud->nome_fantasia;
            $data["razao_social"]  = $razaoForm  ?: ($razaoDb  ?: $data["nome"]);
            $data["nome_fantasia"] = $fantaForm  ?: ($fantaDb  ?: $data["nome"]);
        }

        $pagarMe = (new PagarMe)->criarRecebedor($data);

        error_log(json_encode($pagarMe, true));

        if (!isset($pagarMe->id)) {
            $msgErro = isset($pagarMe->message) ? $pagarMe->message : "Erro ao criar recebedor na PagarMe";
            header("Location: " . url("banco?msg=" . urlencode($msgErro) . "&classCss=bg-danger"));
            exit;
        }

        // Cria conta Stripe Express se o gateway Stripe estiver ativo (dedicado ou balanceado)
        $stripeAccountId    = null;
        $stripeOnboardingUrl = null;
        try {
            $cfgGw = (new ConfiguracaoGateway())->findById(1);
            $stripeAtivo = $cfgGw && (
                $cfgGw->gateway_dedicado  === 'stripe' ||
                $cfgGw->gateway_principal === 'stripe' ||
                $cfgGw->modo              === 'balanceado'
            );

            if ($stripeAtivo) {
                $stripe = new Stripe();
                $contaStripe = $stripe->criarContaConectada([
                    "nome"  => $data["titular"],
                    "email" => $_SESSION["email"],
                    "cpf"   => $data["cpf"] ?? $data["documento"] ?? "",
                    "tipo"  => "express",
                ]);

                if (!empty($contaStripe->id)) {
                    $stripeAccountId = $contaStripe->id;

                    $linkStripe = $stripe->linkOnboarding(
                        $stripeAccountId,
                        url("banco?msg=Link+Stripe+expirado,+solicite+novo"),
                        url("banco?msg=Cadastro+Stripe+concluído")
                    );
                    $stripeOnboardingUrl = $linkStripe->url ?? null;

                    error_log("[Stripe] Conta criada: {$stripeAccountId} onboarding={$stripeOnboardingUrl}");
                } else {
                    error_log("[Stripe] Falha ao criar conta: " . json_encode($contaStripe));
                }
            }
        } catch (\Throwable $e) {
            // Não bloqueia o cadastro PagarMe se a Stripe falhar
            error_log("[Stripe] Exceção ao criar conta: " . $e->getMessage());
        }

        if ($banco->save()) {
            $winPay  = (new WinPay)->criarWallet($data);

            $arquivo = fopen(DIRETORIO . "logs/dados-recebedor.txt", 'a+');

            if ($arquivo == false)
                die('Não foi possível criar o arquivo.');

            $texto = date("d/m/Y H:i:s");
            $texto .= "Usuario: {$data["nome"]} | {$_SESSION["email"]}\n";
            $texto .= "Recebedor PagarMe: {$pagarMe->id}\n";
            if ($stripeAccountId) {
                $texto .= "Conta Stripe: {$stripeAccountId}\n";
            }
            if (@$winPay->id) {
                $texto .= "Recebedor WinPay: {$winPay->id}\n";
            }
            $texto .= "*********************************************************\n";
            fwrite($arquivo, $texto . "\n");
            fclose($arquivo);

            $banco->pagarme_conta = $pagarMe->id;
            if ($stripeAccountId) {
                $banco->stripe_account_id    = $stripeAccountId;
                $banco->stripe_onboarding_url = $stripeOnboardingUrl;
            }
            if (isset($winPay->id)) {
                $banco->winpay_conta = $winPay->id;
            }

            $banco->save();

            $usuario = (new Usuario)->findById($this->section->getId());

            $usuario->pagarme_conta = $pagarMe->id;
            if ($stripeAccountId) {
                $usuario->stripe_account_id = $stripeAccountId;
            }
            if (isset($winPay->id)) {
                $usuario->winpay_conta = $winPay->id;
            }

            $usuario->save();

            $data["email"] = "ws.silva19@gmail.com";
            $data["assunto"] = "Conta bancária cadastrada";
            $data["mensagem"] = "Uma nova conta bancária foi cadastrada <br>" .
                "ID da conta: $banco->id <br>Titular: $banco->titular" .
                "Banco: $banco->banco <br>Conta: $banco->agencia $banco->conta";

            if (AMBIENTE == "produção")
                $enviaEmail = (new Emails())->enviaSendGrid($data);
            header("Location: " . url("banco?msg=Cadastrado com sucesso&classCss=bg-success"));
        } else {
            $msg = $banco->fail()->getMessage();
            header("Location: " . url("banco?msg=$msg&classCss=bg-danger"));
        }
    }

    public function editarBanco($data)
    {
        $banco = (new Banco())->findById($data["id"]);

        $banco->titular = @$data["titular"];
        $banco->cpf = @$data["cpf"];
        $banco->banco = @$data["banco"];
        $banco->agencia = @$data["agencia"];
        $banco->digito_agencia = @$data["digito_agencia"];
        $banco->conta = @$data["conta"];
        $banco->digito_conta = @$data["digito_conta"];
        $banco->tipo = @$data["tipo"];
        $banco->chave_pix = @$data["chave_pix"];
        if (!empty($data["renda_mensal"])) {
            $banco->renda_mensal = (int)round((float)str_replace(',', '.', $data["renda_mensal"]) * 100);
        }
        if (!empty($data["ocupacao"])) {
            $banco->ocupacao = $data["ocupacao"];
        }
        if (!empty($data["receita_anual"])) {
            $banco->receita_anual = (int)round((float)str_replace(',', '.', $data["receita_anual"]) * 100);
        }
        $banco->status = 1;

        $bancos = $banco->find("id != $banco->id && principal == 1 && usuario = " . $this->section->getId())->fetch();
        if (!$bancos) {
            $banco->principal = $data["principal"];
        }

        $files = new File();
        if (@$_FILES["imagem"]["name"]) {
            $banco->imagem_conta = @$files->upload($_FILES["imagem"], PASTA . "banco/", 5);
            if (@$data["imagem_conta"]) @unlink(PASTA . "banco/" . @$data["imagem_conta"]);
        } else {
            $banco->imagem_conta = $data["imagem_conta"];
        }

        if ($banco->imagem_conta == "") {
            header("Location: " . url("banco?msg=Necessário enviar a imagem da conta. Máximo 5 mb. Somente imagem do tipo .jpg, .jpeg, .png ou documento PDF&classCss=bg-danger"));
            exit;
        }

        if ($banco->save()) {
            $data["nome"] = $data["titular"];
            $data["email"] = $_SESSION["email"];
            $data["id"] = $this->section->getId();

            $usuario = null;
            $salvarBancoNovamente = false;
            $salvarUsuario = false;

            if (!$banco->pagarme_conta) {
                // Enriquece $data com campos do perfil do usuário
                $ud = (new UsuarioDados())->find("usuario = " . $this->section->getId())->fetch();
                if ($ud) {
                    $data["data_nascimento"] = @$data["data_nascimento"] ?: $ud->data_nascimento;
                    $data["renda_mensal"]    = $banco->renda_mensal;
                    $data["receita_anual"]   = $banco->receita_anual;
                    $data["telefone"]        = @$data["telefone_pj"] ?: $ud->telefone;
                    $data["cep"]             = $ud->cep;
                    $data["rua"]             = $ud->rua;
                    $data["numero"]          = $ud->numero;
                    $data["complemento"]     = $ud->complemento;
                    $data["bairro"]          = $ud->bairro;
                    $data["cidade"]          = $ud->cidade;
                    $data["estado"]          = $ud->estado;
                    $razaoForm    = trim((string)@$data["razao_social_pj"]);
                    $fantaForm    = trim((string)@$data["nome_fantasia_pj"]);
                    $razaoDb      = preg_match('/^\d+$/', trim((string)$ud->razao_social)) ? '' : $ud->razao_social;
                    $fantaDb      = preg_match('/^\d+$/', trim((string)$ud->nome_fantasia)) ? '' : $ud->nome_fantasia;
                    $data["razao_social"]  = $razaoForm ?: ($razaoDb  ?: $data["nome"]);
                    $data["nome_fantasia"] = $fantaForm ?: ($fantaDb  ?: $data["nome"]);
                }
                $pagarMe = (new PagarMe)->criarRecebedor($data);

                $arquivo = fopen(DIRETORIO . "logs/dados-recebedor.txt", 'a+');

                if ($arquivo == false)
                    die('Não foi possível criar o arquivo.');

                $texto = date("d/m/Y H:i:s");
                $texto .= "\nUsuario: {$data["nome"]} | {$_SESSION["email"]}\n";
                $texto .= "Recebedor PagarMe: " . (@$pagarMe->id ?: 'sem retorno') . "\n";
                $texto .= "*********************************************************\n";
                fwrite($arquivo, $texto . "\n");
                fclose($arquivo);

                if (isset($pagarMe->id)) {
                    $banco->pagarme_conta = $pagarMe->id;
                    $salvarBancoNovamente = true;

                    $usuario = $usuario ?: (new Usuario)->findById($this->section->getId());
                    $usuario->pagarme_conta = $pagarMe->id;
                    $salvarUsuario = true;
                }
            }

            if (!$banco->winpay_conta) {
                $winPay = (new WinPay)->criarWallet($data);

                $arquivo = fopen(DIRETORIO . "logs/dados-recebedor.txt", 'a+');

                if ($arquivo == false)
                    die('Não foi possível criar o arquivo.');

                $texto = date("d/m/Y H:i:s");
                $texto .= "\nUsuario: {$data["nome"]} | {$_SESSION["email"]}\n";
                $texto .= "Recebedor WinPay: " . (@$winPay->id ?: 'sem retorno') . "\n";
                $texto .= "*********************************************************\n";
                fwrite($arquivo, $texto . "\n");
                fclose($arquivo);

                if (isset($winPay->id)) {
                    $banco->winpay_conta = $winPay->id;
                    $salvarBancoNovamente = true;

                    $usuario = $usuario ?: (new Usuario)->findById($this->section->getId());
                    $usuario->winpay_conta = $winPay->id;
                    $salvarUsuario = true;
                }
            }

            if ($salvarBancoNovamente) {
                $banco->save();
            }

            if ($salvarUsuario && $usuario) {
                $usuario->save();
            }

            echo "Atualizado com sucesso";
            header("Location: " . url("banco?msg=Atualizado com sucesso&classCss=bg-success"));
        } else {
            $msg = $banco->fail()->getMessage();
            header("Location: " . url("banco?msg=$msg&classCss=bg-danger"));
        }
    }

    public function editarStatusBanco($data)
    {
        $status = array(
            1 => "Pendente",
            2 => "Aprovado",
            3 => "Reprovado",
            4 => "Bloqueado"
        );

        $banco = (new Banco())->findById($data["idStatus"]);
        $banco->status = @$data["status"];
        $banco->observacao = @$data["observacao"];

        $data["email"] = $banco->dataUsuario()->email;
        $data["assunto"] = "Conta bancária " . $status[$banco->status];
        $data["mensagem"] = "Sua conta bancária está " . $status[$banco->status] . " <br> Entre na RedRocket para ver mais detalhes";
        $enviaEmail = (new Emails())->enviaSendGrid($data);

        if ($banco->save()) {
            echo "Atualizado com sucesso";
        } else {
            echo $banco->fail()->getMessage();
        }
    }

    public function excluirBanco($data)
    {
        $banco = (new Banco())->findById($data["id"]);

        if ($banco->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $banco->fail()->getMessage();
        }
    }
}

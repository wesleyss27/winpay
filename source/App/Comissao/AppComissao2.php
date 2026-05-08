<?php

//CLASSE QUE CONTROLA AS ROTAS DOS AFILIADOS

namespace Source\App\Comissao;

session_start();

use League\Plates\Engine;

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

class AppComissao2
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    public function logs($data, $arq)
    {
        $arquivo = fopen(DIRETORIO . "logs/$arq.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        if ($data) {
            foreach ($data as $chave => $valor) {
                if ($chave == "numcart" || $chave == "cvv") {
                    $valor = "-";
                }
                $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
            }
        } else {
            $texto .= "Erro dos dados\n";
        }
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
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

    public function taxasAsaas($data)
    {
        $formaPagamento = $data["forma_pagamento"];
        $valorTotal = $data["valor_bruto"];
        $parcelas = $data["parcelas"];

        $hoje = date("Y-m-d");

        if (strtotime($hoje) >= strtotime("2022-12-10")) {

            $taxaBoleto = 1.99;
            $taxaCartao = array(
                1 => 0.0249,
                2 => 0.0249,
                3 => 0.0249,
                4 => 0.0249,
                5 => 0.0249,
                6 => 0.0249,
                7 => 0.0249,
                8 => 0.0249,
                9 => 0.0249,
                10 => 0.0249,
                11 => 0.0249,
                12 => 0.0249,
            );
        } else {

            $taxaBoleto = 0.99;
            $taxaCartao = array(
                1 => 0.0199,
                2 => 0.0199,
                3 => 0.0199,
                4 => 0.0199,
                5 => 0.0199,
                6 => 0.0199,
                7 => 0.0199,
                8 => 0.0199,
                9 => 0.0199,
                10 => 0.0199,
                11 => 0.0199,
                12 => 0.0199,
            );
        }
        $custoTransacao = 0.49;

        if ($formaPagamento == 2)
            $valorTaxa = $taxaBoleto;
        elseif ($formaPagamento == 1)
            $valorTaxa = ($valorTotal * $taxaCartao[$parcelas]) + $custoTransacao;
        elseif ($formaPagamento == 3)
            $valorTaxa = $taxaBoleto;

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

    public function taxasCielo($data)
    {
        $formaPagamento = $data["forma_pagamento"];
        $valorTotal = $data["valor_bruto"];
        $parcelas = $data["parcelas"];

        $taxaBoleto = 2.79;
        $taxaCartao = array(
            1 => 0.0389,
            2 => 0.0639,
            3 => 0.0639,
            4 => 0.0639,
            5 => 0.0639,
            6 => 0.0639,
            7 => 0.0639,
            8 => 0.0639,
            9 => 0.0639,
            10 => 0.0639,
            11 => 0.0639,
            12 => 0.0639,
        );
        $custoTransacao = 0;

        if ($formaPagamento == 2)
            $valorTaxa = $taxaBoleto;
        elseif ($formaPagamento == 1)
            $valorTaxa = ($valorTotal * $taxaCartao[$parcelas]) + $custoTransacao;
        elseif ($formaPagamento == 3)
            $valorTaxa = 0.99;

        return number_format($valorTaxa, 2);
    }

    public function tempoSaque($forma_pagamento, $user, $data)
    {
        $usuario = (new Usuario)->findById($user);

        if ($forma_pagamento == 1)
            $tempo_saque =  date("Y-m-d", strtotime("+" . $usuario->tempo_saque . " days", strtotime($data)));
        elseif ($forma_pagamento == 2)
            $tempo_saque =  date("Y-m-d", strtotime("+" . $usuario->tempo_boleto . " days", strtotime($data)));
        elseif ($forma_pagamento == 3)
            $tempo_saque =  date("Y-m-d", strtotime("+" . $usuario->tempo_pix . " days", strtotime($data)));

        return $tempo_saque;
    }

    // VERIFICAR COMISSÕES
    public function buscaComissoes($data)
    {
        // var_dump($data);

        echo "<pre>";
        echo date("d/m/Y H:i:s") . "<br>";
        // Gateways com split nativo não geram comissão local (já distribuído pelo próprio gateway)
        $semSplitNativo = "AND (gateway_usado IS NULL OR gateway_usado NOT IN ('pagarme'))";

        if (@$data["venda"]) {
            $vendas = (new Venda())->find("id = {$data["venda"]} AND comissionada = 0 AND status_transacao = 4 AND adquirente = 'redrocket' $semSplitNativo")->fetch(true);
            echo "v1<br>";
        } else {
            echo "v2<br>";
            $vendas = (new Venda())->find("vendas.id NOT IN (SELECT comissao.venda FROM comissao WHERE comissao.venda IS NOT NULL) AND status_transacao = 4 AND adquirente = 'redrocket' $semSplitNativo")->order("id ASC")->fetch(true);
        }

        if ($vendas) {
            // var_dump($vendas);

            $i = 0;
            foreach ($vendas as $venda) {
                echo "################################################################################################### <br>";
                echo "###      <b style='font-size: 20px'>VERIFICAR COMISSÃO DA VENDA $venda->id - $venda->transacao</b>               ### <br>";
                echo "################################################################################################### <br>";

                $data["meio_pagamento"] = $venda->meio_pagamento;

                echo "###<br>";
                echo "### count: <b>$i</b> <br>" .
                    "### produto: <b>$venda->produto - {$venda->dataProduto()->nome}</b> <br>" .
                    "###<br>" .
                    "### valor Bruto: <b>R$ $venda->valor_bruto</b><br>" .
                    "### Valor Frete: <b>R$ $venda->valor_frete</b><br>" .
                    "### Valor Juros: <b>R$ $venda->valor_juros</b><br>" .
                    "### valor do produto: <b>R$ $venda->valor_produto</b><br>" .
                    "### Valor RedRocekt: <b>R$ $venda->valor_redrocket</b><br>" .
                    "### Valor Desconto: <b>R$ $venda->valor_desconto</b><br>" .
                    "### valor Líquido: <b>R$ $venda->valor_liquido</b><br>" .
                    "### valor Order Bump: <b> $venda->preco_order_bump</b><br>" .

                    "###<br>";
                echo "################################################################################################### <br>";

                /** ESTRUTURA BÁSICA CADASTRO DE COMISSÕES */
                $data["venda"] = $venda->id;
                $data["produto"] = $venda->produto;
                $data["data_pedido"] = $venda->data_finalizada;
                $data["tipo"] = 1;

                /** 
                 * USUARIO PREMIUM 
                 */

                echo "<br>";
                echo "*************************************************************************************************** <br>";
                echo "***                       VERIFICAR USUÁRIOS PREMIUNS                                           *** <br>";
                echo "*************************************************************************************************** <br>";

                $comissaoPremium = number_format($venda->valor_liquido * (1 / 100), 2);
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
                    $data["tempo_saque"] = $this->tempoSaque($venda->forma_pagamento, $data["usuario"], $venda->data_finalizada);
                    $this->cadastroComissao($data);
                } else if ($somaIndicandos == 2) {
                    $data["usuario"] = $indicando[0];
                    $data["valor"] = $comissaoPremium / 2;
                    $data["descricao"] = "Indicação Premium";
                    $data["tempo_saque"] = $this->tempoSaque($venda->forma_pagamento, $data["usuario"], $venda->data_finalizada);

                    $this->cadastroComissao($data);

                    $data["usuario"] = $indicando[1];
                    $this->cadastroComissao($data);
                }

                echo "*** Produtor $produtor - Afiliado $afiliado <br>";

                echo "***<br>";
                echo "*************************************************************************************************** <br>";

                /** 
                 * FIM USUARIO PREMIUM 
                 */

                /** 
                 * TAXA REDROCKET 
                 */

                echo "<br><pre>";
                echo "*************************************************************************************************** <br>";
                echo "***                                      TAXA REDROCKET                                         *** <br>";
                echo "*************************************************************************************************** <br>";

                echo "***<br>";
                echo "*** count: <b>$i</b> <br>" .
                    "*** venda: <b>$venda->id</b> <br>" .
                    "*** valor líquido: <b>$venda->valor_liquido</b> <br>" .
                    "*** valor redrocket: <b>R$ $venda->valor_redrocket</b><br>";

                $red = (new Usuario)->findById(1);
                if ($venda->forma_pagamento == 1)
                    $tempo_red =  date("Y-m-d", strtotime("+" . $red->tempo_saque . " days", strtotime($venda->data_finalizada)));
                elseif ($venda->forma_pagamento == 2)
                    $tempo_red =  date("Y-m-d", strtotime("+" . $red->tempo_boleto . " days", strtotime($venda->data_finalizada)));
                elseif ($venda->forma_pagamento == 3)
                    $tempo_red =  date("Y-m-d", strtotime("+" . $red->tempo_pix . " days", strtotime($venda->data_finalizada)));

                $data["usuario"] = 1;
                $data["valor"] = $venda->valor_redrocket;
                $data["descricao"] = "Taxa RedRocket";
                $data["tempo_saque"] = @$tempo_red ? $tempo_red : date("Y-m-d", strtotime("+15 days", strtotime($venda->data_finalizada)));
                // date("Y-m-d", strtotime("+15 days"));
                $data["taxa_saque"] = 0;
                $this->cadastroComissao($data);

                if ($venda->valor_juros > 0) {
                    echo "<br>*** JUROS CARTÃO <br>";
                    $data["usuario"] = 1;
                    $data["valor"] = $venda->valor_juros;
                    $data["descricao"] = "Juros Cartão";
                    $data["tempo_saque"] = @$tempo_red ? $tempo_red : date("Y-m-d", strtotime("+15 days", strtotime($venda->data_finalizada)));
                    $data["taxa_saque"] = 0;

                    echo "*** juros do cartão: <b>R$ $venda->valor_juros</b><br>";
                    $this->cadastroComissao($data);
                }

                echo "***<br>";
                echo "*************************************************************************************************** <br>";

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
                    "Asaas" => $this->taxasAsaas($data),
                    "Bradesco" => 1.75,
                    "BradescoPix" => 1.65,
                    "Pix" => 0.99,
                    "PagarMe" => $this->taxasPagarMe($data),
                    "Cielo" => $this->taxasCielo($data),
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
                $data["tempo_saque"] = @$tempo_red ? $tempo_red : date("Y-m-d", strtotime("+15 days", strtotime($venda->data_finalizada)));
                $data["taxa_saque"] = 0;
                // $this->cadastroComissao($data);

                echo "***<br>";
                echo "*************************************************************************************************** <br>";

                /** 
                 * FIM TAXAS OPERADORAS
                 */

                // ENVOLVIDOS NA VENDA
                echo "<br>";
                echo "*************************************************************************************************** <br>";
                echo "***                                      ENVOLVIDOS NA VENDA                                    *** <br>";
                echo "*************************************************************************************************** <br>";

                echo "***<br>";
                echo "*** Valor liquido: <b>$venda->valor_liquido</b> <br>" .
                    "*** Produtor: <b>$venda->produtor</b> <br>" .
                    "*** Afiliado: <b>$venda->afiliado</b> - R$ $venda->comissao_afiliado <br>" .
                    "*** Coprodutores: <b> $venda->coprodutores</b> - R$ $venda->comissao_coprodutores<br>";
                echo "***<br>";

                // COMISSÃO AFILIADOS
                if ($venda->afiliado) {
                    echo "*************************************************************************************************** <br>";
                    echo "***                                            AFILIADO                                         *** <br>";
                    echo "*************************************************************************************************** <br>";

                    $data["usuario"] = $venda->afiliado;
                    $data["descricao"] = "Comissão afiliado";

                    if ($venda->forma_pagamento == 3)
                        $tempo_afiliado =  date("Y-m-d", strtotime("+" . $venda->dataAfiliado()->tempo_pix . " days", strtotime($venda->data_finalizada)));
                    elseif ($venda->forma_pagamento == 2)
                        $tempo_afiliado =  date("Y-m-d", strtotime("+" . $venda->dataAfiliado()->tempo_boleto . " days", strtotime($venda->data_finalizada)));

                    $data["tempo_saque"] = @$tempo_afiliado ? $tempo_afiliado : date("Y-m-d", strtotime("+" . $venda->dataAfiliado()->tempo_saque . " days", strtotime($venda->data_finalizada)));

                    if ($venda->comissao_afiliado > $venda->valor_liquido) {
                        $data["valor"] = $venda->valor_liquido;
                    } else {
                        $data["valor"] = $venda->comissao_afiliado;
                    }

                    $this->cadastroComissao($data);
                    echo "***<br>";
                    echo "*************************************************************************************************** <br>";
                }
                // FIM COMISSÃO AFILIADOS

                $coprodutores = explode("/", $venda->coprodutores);
                $comissao_coprodutores = explode("/", $venda->comissao_coprodutores);
                // var_dump($coprodutores);

                $soma_coprodutor = 0;
                if ($coprodutores) {
                    echo "<br>";
                    echo "*************************************************************************************************** <br>";
                    echo "***                                            COPRODUTORES                                     *** <br>";
                    echo "*************************************************************************************************** <br>";

                    $ic1 = 0; // indice coprodutor
                    $count_coprodutores = 0;
                    $cop = array();
                    foreach ($coprodutores as $coprodutor) {
                        if ($coprodutor > 0) {
                            $cop[$count_coprodutores]["coprodutor"] = $coprodutor;
                            $cop[$count_coprodutores]["comissao"] = $comissao_coprodutores[$ic1];
                            // echo "$coprodutor {$comissao_coprodutores[$ic1]}";
                            $soma_coprodutor += $comissao_coprodutores[$ic1];
                            $count_coprodutores++;
                        }
                        $ic1++;
                    }

                    $valores_totais = $venda->comissao_afiliado + $soma_coprodutor;
                    $valor_restante = ($venda->valor_liquido - $venda->comissao_afiliado);
                    echo "***<br>";
                    echo "*** soma coprodutores: $soma_coprodutor <br>";
                    echo "*** valores totais: $valores_totais<br>";
                    echo "*** valores liquido: $venda->valor_liquido<br>";
                    echo "*** valor restante: " . $valor_restante . "<br>";

                    // var_dump($cop);

                    $data["descricao"] = "Comissão coprodução";

                    if ($valores_totais > $venda->valor_liquido) {
                        // $soma_coprodutor = $valor_restante / $count_coprodutores;
                        // $soma_coprodutor = number_format($soma_coprodutor, 2, ".", "");
                        // echo "*** novos valores: $soma_coprodutor $count_coprodutores<br>";

                        foreach ($cop as $cop1) {
                            $usuario = (new Usuario())->findById($cop1["coprodutor"]);
                            $data["usuario"] = $cop1["coprodutor"];

                            if ($venda->forma_pagamento == 3)
                                $tempo_cop1 =  date("Y-m-d", strtotime("+" . $usuario->tempo_pix . " days", strtotime($venda->data_finalizada)));
                            elseif ($venda->forma_pagamento == 2)
                                $tempo_cop1 =  date("Y-m-d", strtotime("+" . $usuario->tempo_boleto . " days", strtotime($venda->data_finalizada)));

                            $data["tempo_saque"] = @$tempo_cop1 ? $tempo_cop1 : date("Y-m-d", strtotime("+" . $usuario->tempo_saque . " days", strtotime($venda->data_finalizada)));

                            echo "<br>*** {$cop1["comissao"]} $soma_coprodutor<br>";
                            $valor_comissao = ($cop1["comissao"] * 100) / $soma_coprodutor;

                            echo "*** $valor_comissao<br>";
                            $data["valor"] = $valor_restante * ($valor_comissao / 100);
                            $data["valor"] = number_format($data["valor"], 2, ".", "");
                            $this->cadastroComissao($data);
                        }
                        $valor_produtor = 0;
                        // var_dump($cop2);
                    } else {
                        foreach ($cop as $cop3) {
                            $usuario = (new Usuario())->findById($cop3["coprodutor"]);
                            $data["usuario"] = $cop3["coprodutor"];

                            if ($venda->forma_pagamento == 3)
                                $tempo_cop2 =  date("Y-m-d", strtotime("+" . $usuario->tempo_pix . " days", strtotime($venda->data_finalizada)));
                            elseif ($venda->forma_pagamento == 2)
                                $tempo_cop2 =  date("Y-m-d", strtotime("+" . $usuario->tempo_boleto . " days", strtotime($venda->data_finalizada)));

                            $data["tempo_saque"] = @$tempo_cop2 ? $tempo_cop2 : date("Y-m-d", strtotime("+" . $usuario->tempo_saque . " days", strtotime($venda->data_finalizada)));
                            $data["valor"] = $cop3["comissao"];
                            $this->cadastroComissao($data);
                        }

                        $valor_produtor = $valor_restante - $soma_coprodutor;
                    }
                }

                echo "<br>";
                echo "*************************************************************************************************** <br>";
                echo "***                                              PRODUTOR                                       *** <br>";
                echo "*************************************************************************************************** <br>";

                $data["usuario"] = $venda->produtor;
                $data["descricao"] = "Comissão Produtor";

                if ($venda->forma_pagamento == 1)
                    $tempo_prod =  date("Y-m-d", strtotime("+" . $venda->dataProdutor()->tempo_saque . " days", strtotime($venda->data_finalizada)));
                elseif ($venda->forma_pagamento == 2)
                    $tempo_prod =  date("Y-m-d", strtotime("+" . $venda->dataProdutor()->tempo_boleto . " days", strtotime($venda->data_finalizada)));
                elseif ($venda->forma_pagamento == 3)
                    $tempo_prod =  date("Y-m-d", strtotime("+" . $venda->dataProdutor()->tempo_pix . " days", strtotime($venda->data_finalizada)));

                $data["tempo_saque"] = @$tempo_prod ? $tempo_prod : date("Y-m-d", strtotime("+" . $venda->dataProdutor()->tempo_saque . " days", strtotime($venda->data_finalizada)));
                echo "*** tempo $venda->forma_pagamento " . $tempo_prod . "<br>";

                if (@$valor_produtor) {
                    $data["valor"] = $valor_produtor + @$venda->valor_frete;
                } else {
                    $data["valor"] = $valor_restante + @$venda->valor_frete;
                }

                $this->cadastroComissao($data);

                echo "***<br>";
                echo "*************************************************************************************************** <br>";
                // FIM ENVOLVIDOS NA VENDA
                echo "<br>";
                echo "<br>";

                $ven = (new Venda())->findById($venda->id);
                $ven->comissionada = 1;
                if ($ven->save()) {
                    echo "Venda atualizada <br>";
                } else {
                    echo $ven->fail()->getMessage();
                }

                $this->logs($data, "comissao");
            } // FIM FOREACH VENDAS
        } else {
            echo "Não tem vendas sem comissão";
        }
        echo "</pre>";
    }
    // FIM VERIFICAR COMISSÕES

    public function cadastrarEstorno($data)
    {
        $venda = $data["venda"];
        $comissoes = (new Comissao())->find("venda = '$venda'")->fetch(true);
        echo "Venda $venda<br>";

        var_dump($comissoes);
        if ($comissoes) {
            foreach ($comissoes as $comissao) {
                if ($comissao->descricao != "Taxa Wirecard" && $comissao->descricao != "Estorno") {
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
        // $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
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

        if (@$data["meio_pagamento"] == "PagarMe") {
            $comissao->disponivel   = 2;
        } else {
            $comissao->disponivel   = 0; // 0 (NÃO) - 1 (SIM) - 2 (SACADO - INSERIDO NA TABELA SALDO)
        }
        $comissao->tipo         = @$data["tipo"]; // 1 (ENTRADA) - 2 (SAIDA)
        $comissao->data_pedido  = $data["data_pedido"];

        echo "***<br>";
        echo "*** <b>CADASTRO COMISSÃO</b><br>" .
            "*** usuario $comissao->usuario - id venda $comissao->venda" .
            "- produto $comissao->produto - comissao <b>R$ $comissao->valor</b> - descrição: $comissao->descricao <br>" .
            "*** data_pedido $comissao->data_pedido - tempo saque <b>R$ $comissao->tempo_saque</b> <br>";

        if ($comissao->save()) {
            echo "<br>*** Salvo com sucesso <br>";
        } else {
            echo "<br>*** " . $comissao->fail()->getMessage() . "<br>";
        }
    }

    public function verificarSaldo($data)
    {
        $comissoes = (new Comissao())->find("tempo_saque <= '" . date("Y-m-d") . "' AND disponivel = 0")->fetch(true);

        foreach ($comissoes as $comissao) {
            $total = number_format($comissao->valor - $comissao->antecipado, 2, ".", "");

            echo "***************************************************<br>";
            echo "USUÁRIO: " . $comissao->dataUsuario()->nome . " - " . $comissao->dataUsuario()->taxa_saque . "<br>";
            echo "VALOR: " . $comissao->valor . "<br>";
            echo "TOTAL: " . number_format($total, 2, ".", ",") . "<br><br>";

            $comm = (new Comissao())->findById($comissao->id);
            $comm->disponivel = 2;
            $comm->save();

            $dados["usuario"] = $comissao->usuario;
            $dados["valor"] = $total;
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
}

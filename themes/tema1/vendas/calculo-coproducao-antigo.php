<?php
    if (@$comissoes && $venda->status_transacao == 4) {
        $comissao = @$comissoes->find("venda = $venda->id")->fetch(true);
        if ($comissao) {
            foreach ($comissao as $comissao) {
                if ($comissao->usuario != 1) {
                    echo "<div class='row borda1'>";
                    echo "<div class='col-md-5'><b>$comissao->descricao:</b></div>";
                    echo "<div class='col-md-7'>(" . $comissao->dataUsuario()->nome . ") - R$ " . number_format($comissao->valor, 2, ",", ".") . "</div>";
                    echo "</div>";
                }
            }
        }
    } else {
        if ($venda->afiliado) {
            echo "<div class='row borda1'>";
            echo "<div class='col-md-5'><b>Comissão Afiliado:</b></div>";
            echo "<div class='col-md-7'>(" . $venda->dataAfiliado()->nome . ") - R$ " . number_format($venda->comissao_afiliado, 2, ",", ".") . "</div>";
            echo "</div>";
        }

        if (@$coproducoes) {
            $coprodutores = $coproducoes->find("aceito = 1 AND produto = $venda->produto AND data_limite <= '" . date("Y-m-d") . "'")->fetch(true);
            if ($coprodutores) {
                $j = 1;
                foreach ($coprodutores as $coprodutor) {
                    if (strtotime($venda->created_at) >= strtotime($coprodutor->created_at)) {
                        // SE COPRODUTOR
                        if ($coprodutor->gerente == 0) {
                            if ($coprodutor->tipo_comissao == 0) {
                                $comissaoCoProdutor = ($venda->valor_liquido) * $coprodutor->comissao / 100;
                                @$comissoesCoprodutores += $comissaoCoProdutor;
                                echo "<div class='row borda1'>";
                                echo "<div class='col-md-5'><b>Comissão Coprodutor:</b></div>";
                                echo "<div class='col-md-7'>(" . $coprodutor->dataCoProdutor()->nome . ") - R$ " . number_format($comissaoCoProdutor, 2, ",", ".") . "</div>";
                                echo "</div>";
                                $valoresCoprodutor[] = array($j, $venda->id, number_format($comissaoCoProdutor, 2, ",", "."));
                            } else {
                                $comissaoCoProdutor = $coprodutor->comissao;
                                @$comissoesCoprodutores += $comissaoCoProdutor;
                                echo "<div class='row borda1'>";
                                echo "<div class='col-md-5'><b>Comissão Coprodutor:</b></div>";
                                echo "<div class='col-md-7'>(" . $coprodutor->dataCoProdutor()->nome . ") - R$ " . number_format($comissaoCoProdutor, 2, ",", ".") . "</div>";
                                echo "</div>";
                                $valoresCoprodutor[] = array($j, $venda->id, number_format($comissaoCoProdutor, 2, ",", "."));
                            }
                        }
                    }
                    // SE GERENTE
                    else {
                        if ($venda->afiliado) {
                            if ($coprodutor->tipo_comissao_afiliado == 0) {
                                $comissaoCoProdutor = $venda->valor_liquido * $coprodutor->comissao_afiliados / 100;
                                @$comissoesCoprodutores += $comissaoCoProdutor;
                                echo "<div class='row borda1'>";
                                echo "<div class='col-md-5'><b>Comissão Gerente:</b></div>";
                                echo "<div class='col-md-7'>(" . $coprodutor->dataCoProdutor()->nome . ") - R$ " . number_format($comissaoCoProdutor, 2, ",", ".") . "</div>";
                                echo "</div>";
                                $valoresCoprodutor[] = array($j, $venda->id, number_format($comissaoCoProdutor, 2, ",", "."));
                            } else {
                                $comissaoCoProdutor = $coprodutor->comissao_afiliados;
                                @$comissoesCoprodutores += $comissaoCoProdutor;
                                echo "<div class='row borda1'>";
                                echo "<div class='col-md-5'><b>Comissão Gerente:</b></div>";
                                echo "<div class='col-md-7'>(" . $coprodutor->dataCoProdutor()->nome . ") - R$ " . number_format($comissaoCoProdutor, 2, ",", ".") . "</div>";
                                echo "</div>";
                                $valoresCoprodutor[] = array($j, $venda->id, number_format($comissaoCoProdutor, 2, ",", "."));
                            }
                        } else {
                            if ($coprodutor->tipo_comissao == 0) {
                                $comissaoCoProdutor = $venda->valor_liquido * $coprodutor->comissao / 100;
                                @$somaComissao += $comissaoCoProdutor;
                                @$comissoesCoprodutores += $somaComissao;
                                echo "<div class='row borda1'>";
                                echo "<div class='col-md-5'><b>Comissão Gerente:</b></div>";
                                echo "<div class='col-md-7'>(" . $coprodutor->dataCoProdutor()->nome . ") - R$ " . number_format($somaComissao, 2, ",", ".") . "</div>";
                                echo "</div>";
                                $valoresCoprodutor[] = array($j, $venda->id, number_format($somaComissao, 2, ",", "."));
                            } else {
                                $comissaoCoProdutor = $coprodutor->comissao;
                                @$comissoesCoprodutores += $comissaoCoProdutor;
                                echo "<div class='row borda1'>";
                                echo "<div class='col-md-5'><b>Comissão Gerente:</b></div>";
                                echo "<div class='col-md-7'>(" . $coprodutor->dataCoProdutor()->nome . ") - R$ " . number_format($comissaoCoProdutor, 2, ",", ".") . "</div>";
                                echo "</div>";
                                $valoresCoprodutor[] = array($j, $venda->id, number_format($comissaoCoProdutor, 2, ",", "."));
                            }
                        }
                    }

                    $j++;
                }
            }
        }

        echo "<div class='row borda1'>";
        echo "<div class='col-md-5'><b>Comissão Produtor:</b></div>";
        echo "<div class='col-md-7'><b>R$ " . number_format($venda->valor_liquido - @$comissoesCoprodutores - $venda->comissao_afiliado, 2, ",", ".") . "</b></div>";
        echo "</div>";
        $comissoesCoprodutores = 0;
    }

    $_SESSION["valores_coproducao"] = @$valoresCoprodutor;

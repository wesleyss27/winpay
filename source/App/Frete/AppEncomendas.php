<?php

namespace Source\App\Frete;

use Source\App\Frete\AppSigep2;
use Source\App\Frete\AppJadlog;


class AppEncomendas
{
    public function calcularFrete($data)
    {

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);


        $sigep = new AppSigep2();
        $jadlog = new AppJadlog();

        // echo $data["id"] . "\n";
        $data["servico"] = "04510";
        $erro = $sigep->calculaFrete($data);

        if (@$erro->cServico->Erro == "0") {
            $dados["erro"] = 0;
            // Tipo_Frete == 2 (Correios) // Tipo_Entrega == 0 (Todos) / 1 (Sedex) / 2 (PAC)
            if (@$data["tipo_frete"] == 2 && @$data["tipo_entrega"] == 0) {
                $data["servico"] = "04510";

                $pac = $sigep->calculaFrete($data)->cServico->Valor;
                $pac_prazo = $sigep->calculaFrete($data)->cServico->PrazoEntrega;

                // $pac = $sigep->calculaFrete($data)->cServico->Valor;
                $pac = (float) str_replace(",", ".", $pac);
                $pac_prazo = (int) ($pac_prazo);


                $data["servico"] = "04014";
                $sedex = $sigep->calculaFrete($data)->cServico->Valor;
                $sedex_prazo = $sigep->calculaFrete($data)->cServico->PrazoEntrega;

                $sedex_prazo = (int) ($sedex_prazo);
                $sedex = (float) str_replace(",", ".", $sedex);


                $dados["pac"] = $pac;
                $dados["pac_prazo"] = $pac_prazo;
                $dados["sedex"] = $sedex;
                $dados["sedex_prazo"] = $sedex_prazo;
            } else if (@$data["tipo_frete"] == 2 && @$data["tipo_entrega"] == 1) //Entrega Sedex
            {
                $data["servico"] = "04014";
                $sedex = $sigep->calculaFrete($data)->cServico->Valor;
                $sedex = (float) str_replace(",", ".", $sedex);

                $sedex_prazo = $sigep->calculaFrete($data)->cServico->PrazoEntrega;
                $sedex_prazo = (int) ($sedex_prazo);


                $dados["sedex"] = $sedex;
                $dados["sedex_prazo"] = $sedex_prazo;
            } else if (@$data["tipo_frete"] == 2 && @$data["tipo_entrega"] == 2) //Entrega PAC
            {
                $data["servico"] = "04510";
                $pac = $sigep->calculaFrete($data)->cServico->Valor;
                $pac = (float) str_replace(",", ".", $pac);

                $pac_prazo = $sigep->calculaFrete($data)->cServico->PrazoEntrega;
                $pac_prazo = (int) ($pac_prazo);
                $dados["pac"] = $pac;
                $dados["pac_prazo"] = $pac_prazo;
            }

            if (@$data["tipo_frete"] == 3) {

                $freteJadlog = $jadlog->calculaFrete($data);

                $freteJad = $jadlog->executa($freteJadlog);

                $jad = json_decode($freteJad);
                // echo json_encode($jad);
                // exit;
                foreach ($jad->frete as $freteJ) {
                    $dados["jadlog"] = $freteJ->vltotal;
                    $dados["jadlog_prazo"] = $freteJ->prazo;
                    // echo "Jadlog:  $freteJ->vltotal\n";              
                }
            }
        } else {
            $dados["erro"] = (int) ($erro->cServico->Erro);
        }
        echo json_encode($dados);
    }
}

<?php

namespace Source\App\Frete;

use Source\Models\Frete\Frete;
use Source\App\Processos\AppProcessos;
use Source\Models\Checkout\Checkout;
use Source\Models\Permissao\Section;

class AppFrete
{
    private $section;

    public function __construct()
    {
        $this->section = new Section();
    }

    public function cadastroFrete(array $data)
    {
        /*@$data["usar_frete"] == 0 (Sem Frente)
         *@$data["usar_frete"] == 1 (Frete Fixo)
         *@$data["usar_frete"] == 2 (Frete Correios)
         *@$data["usar_frete"] == 3 (Frete Jadlog)
         *@$data["usar_frete"] == 4 (Frete por Região)
        */
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        //    var_dump(@$data);
        if (@$data["usar_frete"] != "") {
            // echo("nao encontrou frete");
            $frete = new Frete();
            $frete->produtor = $this->section->getId();
            $frete->produto = @$data["produto"];
            $frete->checkout = @$data["id_checkout"];

            if (@$data["usar_frete"] == 1) {
                $frete->tipo_frete = 1;

                if (@$data["frete_gratis"] == 1) {
                    $frete->frete_gratis = 1;
                    $frete->prazo_entrega_gratis = @$data["prazo_entrega_gratis"];
                } else {
                    $frete->frete_gratis = 0;
                }

                $frete->prazo_entrega = @$data["prazo_entrega"];
                $frete->frete_padrao = (new AppProcessos(null))->removerFormatacaoNumero(@$data["valor_frete"]);
            } else if (@$data["usar_frete"] == 2) {
                $frete->tipo_frete = 2;
                $frete->prazo_entrega = @$data["prazo_entrega_sedex"];
                $frete->correios_entrega = @$data["tipo_entrega_correios"];
                $frete->cep_origem = @$data["cep_sedex"];
                $frete->frete_padrao = (new AppProcessos(null))->removerFormatacaoNumero(@$data["valor_padrao_sedex"]);
                $frete->correios_formato = @$data["formato_sedex"];
                    $frete->altura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["altura_sedex"]);
                    $frete->largura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["largura_sedex"]);
                    $frete->comprimento_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["comprimento_sedex"]);
                    $frete->peso_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["peso_sedex"]);
            
            } else if (@$data["usar_frete"] == 3) {
                $frete->tipo_frete = 3;
                $frete->prazo_entrega = @$data["prazo_entrega_jadlog"];
                $frete->cep_origem = @$data["cep_jadlog"];
                $frete->frete_padrao = (new AppProcessos(null))->removerFormatacaoNumero(@$data["valor_padrao_jadlog"]);
                $frete->altura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["altura_jadlog"]);
                $frete->largura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["largura_jadlog"]);
                $frete->comprimento_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["comprimento_jadlog"]);
                $frete->peso_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["peso_jadlog"]);
            } else if (@$data["usar_frete"] == 4) {
                $frete->tipo_frete = 4;
                $frete->prazo_reg_norte = @$data["prazo_entrega_regiao_norte"];
                $frete->prazo_reg_nordeste = @$data["prazo_entrega_regiao_nordeste"];
                $frete->prazo_reg_centro = @$data["prazo_entrega_regiao_centro"];
                $frete->prazo_reg_sudeste = @$data["prazo_entrega_regiao_sudeste"];
                $frete->prazo_reg_sul = @$data["prazo_entrega_regiao_sul"];
                $frete->regiao_norte = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_norte"]);
                $frete->regiao_nordeste = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_nordeste"]);
                $frete->regiao_centro = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_centro_oeste"]);
                $frete->regiao_sudeste = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_sudeste"]);
                $frete->regiao_sul = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_sul"]);
            } else {
                $frete->tipo_frete = 0;
            }

            // echo "<pre>";
            // var_dump($frete);
            // exit;
            if (@$frete->save()) {
                // var_dump("FRETE", $frete);
                $checkout = (new Checkout())->findById(@$frete->checkout);

                if (@$checkout)
                    $checkout->id_frete = @$frete->id;

                if (@$checkout->save()) {

                    // var_dump("CHECKOUT FRETE:", $checkout);
                    return 0;
                } else
                    return 1;
            } else {
                return 1;
            }
        }
    }

    public function editarFrete(array $data)
    {

        /*@$data["usar_frete"] == 0 (Sem Frente)
         *@$data["usar_frete"] == 1 (Frete Fixo)
         *@$data["usar_frete"] == 2 (Frete Correios)
         *@$data["usar_frete"] == 3 (Frete Jadlog)
         *@$data["usar_frete"] == 4 (Frete por Região)
        */

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $frete = (new frete())->findById(@$data["id_frete"]);
        if (@$frete) {
            $frete->produtor = $this->section->getId();
            $frete->produto = @$data["produto"];
            $frete->checkout = @$data["id_checkout"];

            if (@$data["usar_frete"] == 1) {
                $frete->tipo_frete = 1;

                if (@$data["frete_gratis"] == 1) {
                    $frete->frete_gratis = 1;
                    $frete->prazo_entrega_gratis = @$data["prazo_entrega_gratis"];
                } else {
                    $frete->frete_gratis = 0;
                }

                $frete->prazo_entrega = @$data["prazo_entrega"];
                $frete->frete_padrao = (new AppProcessos(null))->removerFormatacaoNumero(@$data["valor_frete"]);
            } else if (@$data["usar_frete"] == 2) {
                $frete->tipo_frete = 2;
                $frete->prazo_entrega = @$data["prazo_entrega_sedex"];
                $frete->correios_entrega = @$data["tipo_entrega_correios"];
                $frete->cep_origem = @$data["cep_sedex"];
                $frete->frete_padrao = (new AppProcessos(null))->removerFormatacaoNumero(@$data["valor_padrao_sedex"]);
                $frete->correios_formato = @$data["formato_sedex"];
                $frete->altura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["altura_sedex"]);
                $frete->largura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["largura_sedex"]);
                $frete->comprimento_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["comprimento_sedex"]);
                $frete->peso_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["peso_sedex"]);
            } else if (@$data["usar_frete"] == 3) {
                $frete->tipo_frete = 3;
                $frete->prazo_entrega = @$data["prazo_entrega_jadlog"];
                $frete->cep_origem = @$data["cep_jadlog"];
                $frete->frete_padrao = (new AppProcessos(null))->removerFormatacaoNumero(@$data["valor_padrao_jadlog"]);
                $frete->altura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["altura_jadlog"]);
                $frete->largura_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["largura_jadlog"]);
                $frete->comprimento_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["comprimento_jadlog"]);
                $frete->peso_mercadoria = (new AppProcessos(null))->removerFormatacaoNumero(@$data["peso_jadlog"]);
            } else if (@$data["usar_frete"] == 4) {
                $frete->tipo_frete = 4;
                $frete->prazo_reg_norte = @$data["prazo_entrega_regiao_norte"];
                $frete->prazo_reg_nordeste = @$data["prazo_entrega_regiao_nordeste"];
                $frete->prazo_reg_centro = @$data["prazo_entrega_regiao_centro"];
                $frete->prazo_reg_sudeste = @$data["prazo_entrega_regiao_sudeste"];
                $frete->prazo_reg_sul = @$data["prazo_entrega_regiao_sul"];

                $frete->regiao_norte = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_norte"]);
                $frete->regiao_nordeste = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_nordeste"]);
                $frete->regiao_centro = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_centro_oeste"]);
                $frete->regiao_sudeste = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_sudeste"]);
                $frete->regiao_sul = (new AppProcessos(null))->removerFormatacaoNumero(@$data["regiao_sul"]);
            } else {
                $frete->tipo_frete = 0;
            }

            // echo "<pre>";
            // var_dump($frete);
            // exit;
            if (@$frete->save())
                return 0;
            else
                return 1;
        } else {
            $frete->fail()->getMessage();
        }
    }

    public function excluirFrete($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $frete = (new Frete())->findById($data["id_frete"]);

        if ($frete->destroy()) {
            // echo "Excluido com sucesso";
        } else {
            echo "Erro ao excluir <br>";
            echo $frete->fail()->getMessage();
        }
    }
}

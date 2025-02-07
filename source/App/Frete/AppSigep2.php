<?php

namespace Source\App\Frete;

class AppSigep2
{
    // private
    //     $usuario = "sigep",
    //     $senha = "n5f9t8",
    //     $codAdmin = "17000190",
    //     $contrato = "9992157880",
    //     $servico = "161277",
    //     $cartao = "0067599079",
    //     $cnpj = "34028316000103";

    public function calculaFrete($data)
    {
        $cep_origem = @$data["cep_origem"];     // Seu CEP , ou CEP da Loja
        $cep_destino = @$data["cep_destino"];
        $peso          = @$data["peso_mercadoria"];
        $largura       = @$data["largura_mercadoria"];
        $comprimento   = @$data["comprimento_mercadoria"];
        $valor         = @$data["preco_produto"];
        $pac = '04510';
        $sedex = '04014';

        $servico = $data["servico"];

        if (@$data["formato"] == 0) {
            $formato = 1; // Formato Caixa ou Pacote
            $altura = @$data["altura_mercadoria"];
        } else {
            $formato = 3;        // Formato Envelope
            $altura = 0;
            $peso = $peso >= 1 ? 1 : $peso;
            $largura = $largura <= 10 ? 11 : $largura;
            $comprimento = $comprimento <= 15 ? 16 : $largura;
        }
  

        $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?";
        $url .= "nCdEmpresa=" . '';
        $url .= "&sDsSenha=" . '';
        $url .= "&sCepOrigem=" . $cep_origem;
        $url .= "&sCepDestino=" . $cep_destino;
        $url .= "&nVlPeso=" . $peso;
        $url .= "&nVlLargura=" . $largura;
        $url .= "&nVlAltura=" . $altura;
        $url .= "&nCdFormato=" . $formato;
        $url .= "&nVlComprimento=" . $comprimento;
        $url .= "&sCdMaoPropria=n";
        $url .= "&nVlValorDeclarado=0";
        $url .= "&sCdAvisoRecebimento=n";
        $url .= "&nCdServico=" . $servico;
        $url .= "&nVlDiametro=0";
        $url .= "&StrRetorno=xml";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        $result = simplexml_load_string($result);

        return $result;
    }
}

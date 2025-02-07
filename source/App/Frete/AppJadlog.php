<?php

namespace Source\App\Frete;

class AppJadlog
{
    // private 
    //     $usuario = "35494472",
    //     $senha = "7ww2eb",
    //     $codAdmin = "20230400",
    //     $contrato = "9912497714",
    //     $servico = "162026",
    //     $cartao = "0075651491",
    //     $cnpj = "35494472000122";

    public function calculaFrete($data)
    {
        $campos = array(
            "frete" => [array(
                "cepori" => @$data["cep_origem"],
                "cepdes" => @$data["cep_destino"],
                "frap" => "N",
                "peso" => $data["peso_mercadoria"],
                // "cnpj" => "35494472000122",
                // "conta" => "0249212",
                // "contrato" => "123",
                "modalidade" => "3",
                "tpentrega" => "D",
                "tpseguro" => "N",
                "vldeclarado" => $data["preco_produto"],
                "vlcoleta" => null
            )]
        );

        $campos["url"] = "www.jadlog.com.br/embarcador/api/frete/valor";
        return json_encode($campos);
    }

    public function executa($campos)
    {
        $curl = curl_init();
        $url = json_decode($campos);
        //var_dump($url);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url->url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $campos,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOjEwMTM2NCwiZHQiOiIyMDIxMDEyOCJ9.j-AjYSA3j10bGCqtKqsIHyhOLHkaei3HhoEnYuxycB4',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
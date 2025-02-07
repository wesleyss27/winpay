<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://homolog.meiosdepagamentobradesco.com.br/apipix/transacao',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 120,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "merchant_id": "200001828",
    "meio_pagamento": "1200",
    "pedido": {
        "numero": "928",
        "valor": 1000,
        "descricao": "Teste no valor de R$ 1,00",
        "expiracao": 3600,
        "formato": 3
    },
    "comprador": {
        "nome": "Pessoa de Teste",
        "documento": "12719252654",
        "endereco": {
            "cep": "35012140",
            "logradouro": "Rua São Bartolomeu",
            "numero": "277",
            "complemento": "",
            "bairro": "Minas Gerais",
            "cidade": "Governador Valadares",
            "uf": "MG"
        }
    }
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic MjAwMDAxODI4Oi04U2czTFY4OERyTjg3UkJyOHVzZ1pLTm1MQ2NaVGFubG9FMHNYN0pRU00=',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

<?php

namespace Source\App\Facebook;

class AppFacebook
{
    public function executar($url, $metodo, $dados)
    {
	$curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $metodo,
            CURLOPT_POSTFIELDS => $dados,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //echo $response;

        $data["dados"] = $dados;
        $data["url"] = $url;
        $data["response"] = $response;

        $this->logs($data, "facebook-pixel");
    }

    public function pixel($data)
    {

        $url = "https://graph.facebook.com/v18.0/{$data['pixel']}/events?access_token={$data['token']}";
        $json = '{
            "data": [
                {
                    "event_name": "' . @$data["evento"] . '",
                    "event_time": ' . time() . ',
                    "action_source": "website",
                    "event_source_url": "' . @$data["site_url"] . '",
                    "user_data": {
                        "em": [
                            "309a0a5c3e211326ae75ca18196d301a9bdbd1a882a4d2569511033da23f0abd"
                          ],
                        "ph": [
                            "254aa248acb47dd654ca3ea53f48c2c26d641d23d7e2e93a1ec56258df7674c4",
                            "6f4fcb9deaeadc8f9746ae76d97ce1239e98b404efe5da3ee0b7149740f89ad6"
                        ],
                        "client_ip_address": "' . @$data["ip"] . '",
                        "client_user_agent": "' . @$data["user_agent"] . '",
                        "fbc": "fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890",
                        "fbp": "fb.1.1558571054389.1098115397"
                    }';

        if (@$data["valor"]) {
            $json .= ',"custom_data": {
                        "currency": "BRL",
                        "value": "' . @$data["valor"] . '"
                    }';
        }
        $json .= '
                }
            ]
        }';

        return $this->executar($url, "POST", $json);
    }

    public function logs($data, $arq)
    {
        // var_dump($data);
        $diretorio = "/xampp2/htdocs/redrocket/";
        $arquivo = fopen(DIRETORIO . "logs/$arq.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        if ($data) {
            foreach ($data as $chave => $valor) {
                if ($chave == "numcart" || $chave == "cvv" || $chave == "ano-val") {
                    $valor = "-";
                }
                if ($chave != "codigo_order_bump")
                    $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
            }
        } else {
            $texto .= "Erro dos dados";
        }
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
    }

    // "em": [
    //     "' . @$data["email_hash"] . '"
    // ],
    // "ph": [
    //     "' . @$data["telefone_hash"] . '"
    // ],
    // "fn": [
    //     "' . @$data["nome_hash"] . '"
    // ],
    // "st": [
    //     "' . @$data["estado_hash"] . '"
    // ],
    // "zp": [
    //     "' . @$data["cep_hash"] . '"
    // ],
}

// include "../../../../vendor/autoload.php";

// $data["evento"] = "PageView";
// $data["site_url"] = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// $data["ip"] = $_SERVER["REMOTE_ADDR"];
// $data["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
// $data["pixel"] = "952025535749682";
// $data["token"] = "EAANMiW59o9YBABXxrkd2Fnfy0xUk0G4HfJp1DWjSD2ArgvDV8jbW1vZB8Bdj3AFz2jJZBpgK8wc0BXrulL93hyVvqrDx85aE6QjyobZB6UU1yoSBQU8dW723YPVmTRs9bmA8MRrIXJX56L2IzqAMBzuAMfQkVfHeT19xDmpx93oHLZBwrQ71";

// $facebook = (new AppFacebook())->pixel($data);

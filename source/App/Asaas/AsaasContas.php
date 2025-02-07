<?php

namespace Source\App\Asaas;

class AsaasContas
{
    private $endpoint;
    private $api_key;

    public function __construct()
    {
        $this->endpoint = 'https://sandbox.asaas.com';
        $this->api_key = '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwNDA4Mjc6OiRhYWNoXzAyZGNjOGNlLTkwOTAtNGIzMC1iNzc4LTVlNmIzNDhhYWY1Mw==';
    }

    public function novaConta($data)
    {
        if(@$data["tipo_doc"] == 2) {
            $tipo_empresa = @$data["tipo_empresa"] ? @$data["tipo_empresa"] : "MEI";
        } else {
            $tipo_empresa = "null";
        }
        $dados["endpoint"] = $this->endpoint . "/api/v3/accounts";
        $dados["method"] = "POST";
        $dados["json"] = "{
            'name': '" . @$data["nome"] . "',
            'email': '" . @$data["email"] . "',
            'cpfCnpj': '" . @$data["cpf"] . "',
            'companyType': $tipo_empresa,
            'birthDate': null,
            'mobilePhone': '" . @$data["telefone"] . "',
            'address': '" . @$data["rua"] . "',
            'addressNumber': '" . @$data["numero"] . "',
            'complement': '" . @$data["complemento"] . "',
            'province': '" . @$data["bairro"] . "',
            'postalCode': '" . @$data["cep"] . "'
        }";

        return $this->executar($dados["endpoint"], $dados["method"], $dados["json"]);
    }

    public function listarContas($email = null, $cpf_cnpj = null)
    {
        $dados["endpoint"]      = $this->endpoint . "/api/v3/accounts?cpfCnpj=$cpf_cnpj&email=$email";
        $dados["unica_conta"]   = "?id=";
        $dados["method"]        = "GET";

        return $this->executar($dados["endpoint"], $dados["method"]);
    }

    public function dadosCliente($id_cliente)
    {
        $dados["endpoint"] = $this->endpoint . "/api/v3/customers/$id_cliente";
        $dados["method"] = "GET";

        return $dados;
    }

    public function executar($url, $method = "GET", $json = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "$method",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "access_token: $this->api_key",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
}

// $asaas = new AsaasClientes();
// $novoCliente = $asaas->novoCliente(null);
// $novoCliente = $asaas->listarClientes();
// $novoCliente = $asaas->dadosCliente("cus_000004981616");
// $novoCliente = $asaas->atualizarCliente(["id_cliente" => "cus_000004981616"]);

// $executar = $asaas->executar($novoCliente['endpoint'], $novoCliente['method'], $novoCliente['json']);
// $executar = $asaas->executar($novoCliente['endpoint'], $novoCliente['method']);
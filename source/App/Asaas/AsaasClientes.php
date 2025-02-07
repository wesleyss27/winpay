<?php

namespace Source\App\Asaas;

class AsaasClientes
{
    private $endpoint;
    private $api_key;

    public function __construct()
    {
        $this->endpoint = 'https://sandbox.asaas.com';
        $this->api_key = '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwNDA4Mjc6OiRhYWNoXzAyZGNjOGNlLTkwOTAtNGIzMC1iNzc4LTVlNmIzNDhhYWY1Mw==';
    }

    public function novoCliente($data)
    {
        $id = uniqid();

        if(@$data["endereco_obrigatorio"] == 0){
            $data["cep"] = "35010070";
            $data["rua"] = "Avenida Brasil";
            $data["numero"] = "3021";
            $data["complemento"] = "";
            $data["bairro"] = "Centro";
            $data["cidade"] = "Governador Valadares";
            $data["estado"] = "MG";
            $data["cep"] = "35010050";
        }

        $dados["endpoint"] = $this->endpoint . "/api/v3/customers";
        $dados["method"] = "POST";
        $dados["json"] = "{
            'name': '" . @$data["nome"] . "',
            'email': '" . @$data["email"] ."',
            'mobilePhone': '" . @$data["ddd"] . @$data["telefone"] . "',
            'cpfCnpj': '" . @$data["cpf"] . "',
            'postalCode': '" . @$data["cep"] . "',
            'address': '" . @$data["rua"] . "',
            'addressNumber': '" . @$data["numero"] . "',
            'complement': '" . @$data["complemento"] . "',
            'province': '" . @$data["bairro"] . "',
            'externalReference': '$id',
            'notificationDisabled': true,
        }";

        $executar = $this->executar($dados['endpoint'], $dados['method'], $dados['json']);
        return $executar;
    }

    public function listarClientes()
    {
        $dados["endpoint"] = $this->endpoint . "/api/v3/customers";
        $dados["method"] = "GET";

        return $this->executar($dados['endpoint'], $dados['method']);
    }

    public function dadosCliente($id_cliente)
    {
        $dados["endpoint"] = $this->endpoint . "/api/v3/customers/$id_cliente";
        $dados["method"] = "GET";

        return $this->executar($dados['endpoint'], $dados['method'], $dados['json']);
    }

    public function atualizarCliente($data)
    {
        $dados["endpoint"] = $this->endpoint . "/api/v3/customers/" . $data["id_cliente"];
        $dados["method"] = "POST";
        $dados["json"] = "{
            'name': 'Marcelo Almeida',
            'email': 'marcelo.almeida@gmail.com',
            'phone': '4738010919',
            'mobilePhone': '4799376637',
            'cpfCnpj': '24971563792',
            'postalCode': '01310-000',
            'address': 'Av. Paulista',
            'addressNumber': '150',
            'complement': 'Sala 201',
            'province': 'Centro',
            'externalReference': '12987382',
            'notificationDisabled': false,
            'additionalEmails': 'marcelo.almeida2@gmail.com,marcelo.almeida3@gmail.com',
            'municipalInscription': '46683695908',
            'stateInscription': '646681195275',
            'observations': 'ótimo pagador, nenhum problema até o momento'
        }";

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

$asaas = new AsaasClientes();
// $novoCliente = $asaas->novoCliente(null);
// $novoCliente = $asaas->listarClientes();
// $novoCliente = $asaas->dadosCliente("cus_000004981616");
// $novoCliente = $asaas->atualizarCliente(["id_cliente" => "cus_000004981616"]);

// $executar = $asaas->executar($novoCliente['endpoint'], $novoCliente['method'], $novoCliente['json']);
// $executar = $asaas->executar($novoCliente['endpoint'], $novoCliente['method']);
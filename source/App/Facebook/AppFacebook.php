<?php

namespace Source\App\Facebook;

/**
 * Envia eventos para a Conversions API.
 * Mantém a assinatura existente, mas agora aceita event_id, fbp/fbc,
 * custom_data e user_data reais (hash SHA256).
 */
class AppFacebook
{
    private function notEmpty($value): bool
    {
        return !($value === null || $value === '' || $value === []);
    }

    private function onlyDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $value);
        return $digits !== '' ? $digits : null;
    }

    private function hashValue(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        return hash('sha256', trim(mb_strtolower($value)));
    }

    private function buildUserData(array $data): array
    {
        $userData = $data['user_data'] ?? [];

        // Sinais do navegador
        $userData['client_ip_address']  = $userData['client_ip_address']  ?? ($data['ip'] ?? null);
        $userData['client_user_agent']  = $userData['client_user_agent']  ?? ($data['user_agent'] ?? null);
        $userData['fbc']                = $userData['fbc']                ?? ($data['fbc'] ?? null);
        $userData['fbp']                = $userData['fbp']                ?? ($data['fbp'] ?? null);
        $userData['external_id']        = $userData['external_id']        ?? ($data['external_id'] ?? null);

        // Dados pessoais – hash obrigatória
        if (!empty($data['email'])) {
            $userData['em'][] = $this->hashValue($data['email']);
        }
        if (!empty($data['telefone'])) {
            $phoneDigits = $this->onlyDigits($data['telefone']);
            if ($phoneDigits) {
                $userData['ph'][] = $this->hashValue($phoneDigits);
            }
        }
        if (!empty($data['first_name'])) {
            $userData['fn'][] = $this->hashValue($data['first_name']);
        }
        if (!empty($data['last_name'])) {
            $userData['ln'][] = $this->hashValue($data['last_name']);
        }
        if (!empty($data['city'])) {
            $userData['ct'] = $this->hashValue($data['city']);
        }
        if (!empty($data['state'])) {
            $userData['st'] = $this->hashValue($data['state']);
        }
        if (!empty($data['zip'])) {
            $zipDigits = $this->onlyDigits($data['zip']);
            if ($zipDigits) {
                $userData['zp'] = $this->hashValue($zipDigits);
            }
        }
        if (!empty($data['country'])) {
            $userData['country'] = $this->hashValue($data['country']);
        }

        return array_filter($userData, [$this, 'notEmpty']);
    }

    private function buildCustomData(array $data): ?array
    {
        if (!empty($data['custom_data'])) {
            return array_filter($data['custom_data'], [$this, 'notEmpty']);
        }

        // retrocompatibilidade: se só existir "valor", monta currency/value
        if (isset($data['valor'])) {
            return [
                'currency' => 'BRL',
                'value'    => (float)$data['valor'],
            ];
        }

        return null;
    }

    public function executar($url, $metodo, $dados)
    {
        $curl = curl_init();

        if (is_array($dados)) {
            $dados = json_encode($dados);
        }

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

        $data["dados"] = $dados;
        $data["url"] = $url;
        $data["response"] = $response;

        $this->logs($data, "facebook-pixel");
    }

    public function pixel($data)
    {
        $pixelId = $data['pixel'] ?? null;
        $token   = $data['token'] ?? null;

        if (!$pixelId || !$token) {
            return null;
        }

        $event = [
            'event_name'       => $data['evento'] ?? 'InitiateCheckout',
            'event_time'       => $data['event_time'] ?? time(),
            'event_id'         => $data['event_id'] ?? null, // usado para deduplicar com o pixel do navegador
            'event_source_url' => $data['site_url'] ?? null,
            'action_source'    => $data['action_source'] ?? 'website',
            'user_data'        => $this->buildUserData($data),
        ];

        $custom = $this->buildCustomData($data);
        if ($custom) {
            $event['custom_data'] = $custom;
        }

        $event = array_filter($event, [$this, 'notEmpty']);

        $payload = [
            'data' => [$event]
        ];

        if (!empty($data['test_event_code'])) {
            $payload['test_event_code'] = $data['test_event_code'];
        }

        $url = "https://graph.facebook.com/v18.0/{$pixelId}/events?access_token={$token}";

        return $this->executar($url, "POST", $payload);
    }

    public function logs($data, $arq)
    {
        $arquivo = fopen(DIRETORIO . "logs/$arq.txt", 'a+');

        if ($arquivo == false) {
            die('Não foi possível criar o arquivo.');
        }

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        if ($data) {
            foreach ($data as $chave => $valor) {
                if ($chave == "numcart" || $chave == "cvv" || $chave == "ano-val") {
                    $valor = "-";
                }
                if ($chave != "codigo_order_bump") {
                    $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
                }
            }
        } else {
            $texto .= "Erro dos dados";
        }
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
    }
}

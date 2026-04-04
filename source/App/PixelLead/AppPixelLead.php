<?php

namespace Source\App\PixelLead;

use Source\Models\PixelLead\PixelLead;

/**
 * Recebe o webhook do roundbr com o fbclid capturado na landing page,
 * salva na tabela pixel_leads indexada por IP para posterior recuperação
 * no checkout (InitiateCheckout) e na finalização da compra (Purchase).
 *
 * Payload esperado (JSON, POST):
 *   { "fbclid": "AbCd...", "ip": "1.2.3.4", "ua": "Mozilla...", "ts": 1710000000000 }
 *
 * Autenticação: header  Authorization: Bearer <ROUNDBR_WEBHOOK_SECRET>
 */
class AppPixelLead
{
    public function __construct($router) {}

    public function store($data): void
    {
        header('Content-Type: application/json');

        // Autenticação simples por chave compartilhada (campo "secret" no body)
        if (defined('ROUNDBR_WEBHOOK_SECRET') && ROUNDBR_WEBHOOK_SECRET !== '') {
            $rawBody = file_get_contents('php://input');
            $bodyCheck = json_decode($rawBody, true);
            if (($bodyCheck['secret'] ?? '') !== ROUNDBR_WEBHOOK_SECRET) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }
            $body = $bodyCheck;
        } else {
            $body = json_decode(file_get_contents('php://input'), true);
        }

        if (!$body) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON body']);
            return;
        }

        $fbclid       = trim($body['fbclid']       ?? '');
        $ip           = trim($body['ip']           ?? '');
        $ua           = trim($body['ua']           ?? '');
        $ts           = $body['ts']                ?? null;
        $utm_source   = trim($body['utm_source']   ?? '');
        $utm_campaign = trim($body['utm_campaign'] ?? '');
        $utm_medium   = trim($body['utm_medium']   ?? '');
        $utm_content  = trim($body['utm_content']  ?? '');
        $utm_term     = trim($body['utm_term']     ?? '');
        $src          = trim($body['src']          ?? '');
        $sck          = trim($body['sck']          ?? '');
        $round_id     = trim($body['round_id']     ?? '');
        $round_number = trim($body['round_number'] ?? '');

        if (!$ip) {
            http_response_code(400);
            echo json_encode(['error' => 'ip is required']);
            return;
        }

        // Reconstrói fbc no formato padrão da Meta: fb.1.<timestamp_ms>.<fbclid>
        $fbc = null;
        if ($fbclid) {
            $tsMs = $ts ? (int)$ts : (time() * 1000);
            $fbc  = "fb.1.{$tsMs}.{$fbclid}";
        }

        $lead               = new PixelLead();
        $lead->fbclid       = $fbclid       ?: null;
        $lead->fbc          = $fbc;
        $lead->ip           = $ip;
        $lead->ua           = $ua           ?: null;
        $lead->utm_source   = $utm_source   ?: null;
        $lead->utm_campaign = $utm_campaign ?: null;
        $lead->utm_medium   = $utm_medium   ?: null;
        $lead->utm_content  = $utm_content  ?: null;
        $lead->utm_term     = $utm_term     ?: null;
        $lead->src          = $src          ?: null;
        $lead->sck          = $sck          ?: null;
        $lead->round_id     = $round_id     ?: null;
        $lead->round_number = $round_number ?: null;
        $lead->save();

        if ($lead->fail()) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $lead->fail()->getMessage()]);
            return;
        }

        self::log([
            'evento'       => 'pixel_lead_salvo',
            'id'           => $lead->id,
            'ip'           => $ip,
            'fbclid'       => $fbclid ?: null,
            'utm_source'   => $utm_source   ?: null,
            'utm_campaign' => $utm_campaign ?: null,
            'utm_medium'   => $utm_medium   ?: null,
            'src'          => $src          ?: null,
            'sck'          => $sck          ?: null,
            'round_id'     => $round_id     ?: null,
        ]);

        http_response_code(201);
        echo json_encode(['ok' => true, 'id' => $lead->id]);
    }

    private static function log(array $data): void
    {
        $linha = date('Y-m-d H:i:s') . ' ' . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        $arquivo = fopen(DIRETORIO . "logs/pixel-lead.txt", 'a+');
        if ($arquivo) {
            fwrite($arquivo, $linha);
            fclose($arquivo);
        }
    }
}

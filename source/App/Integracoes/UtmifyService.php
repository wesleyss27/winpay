<?php

namespace Source\App\Integracoes;

use Source\Models\Utmify\Utmify;
use Source\Models\PixelLead\PixelLead;
use Source\Models\Vendas\Venda;
use Source\Models\Reembolsos\Reembolso;

/**
 * Envia pedidos para a API da Utmify.
 * Chamado após cada venda salva no checkout.
 *
 * Prioridade de UTMs:
 *   1. Valores já na venda (vieram da URL do checkout)
 *   2. Fallback por IP na tabela pixel_leads (comprador veio pelo WhatsApp)
 */
class UtmifyService
{
    private const ENDPOINT = 'https://api.utmify.com.br/api-credentials/orders';
    private const PLATFORM = 'Winpay';

    /** Mapa status_transacao (int) → status Utmify */
    private const STATUS_MAP = [
        1 => 'waiting_payment', // aguardando
        2 => 'waiting_payment', // em análise
        3 => 'waiting_payment', // pré-autorizado
        4 => 'paid',
        5 => 'refused',
        6 => 'refunded',
        7 => 'chargedback',
    ];

    /** Mapa forma_pagamento (int) → paymentMethod Utmify */
    private const PAYMENT_MAP = [
        1 => 'credit_card',
        2 => 'boleto',
        3 => 'pix',
    ];

    /**
     * Ponto de entrada: busca integração do produtor e envia.
     * Retorna null se o produtor não tem Utmify configurado.
     */
    public static function enviar(Venda $venda): ?array
    {
        $config = (new Utmify())->find("usuario = :u", "u={$venda->produtor}")->fetch();
        if (!$config || !$config->api_key) {
            self::log(['evento' => 'sem_config', 'produtor' => $venda->produtor, 'venda_id' => $venda->id]);
            return null;
        }

        $payload = self::buildPayload($venda);
        return self::post($config->api_key, $payload);
    }

    // -----------------------------------------------------------------------

    private static function buildPayload(Venda $venda): array
    {
        $comprador = $venda->dataComprador();
        $produto   = $venda->dataProduto();
        $plano     = $venda->plano ? $venda->dataPlano() : null;

        // IP real do cliente (X-Forwarded-For tem prioridade sobre REMOTE_ADDR do Docker)
        $clientIp = isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            ? trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0])
            : ($venda->ip ?: null);

        // orderId: usa transacao se preenchida, senão usa o id da venda
        $orderId = !empty($venda->transacao) ? (string) $venda->transacao : (string) $venda->id;

        $status        = self::STATUS_MAP[(int)$venda->status_transacao] ?? 'waiting_payment';
        $paymentMethod = self::PAYMENT_MAP[(int)$venda->forma_pagamento] ?? 'pix';

        // Datas em UTC (banco está em BRT −3)
        $createdAt    = self::toUtc($venda->created_at);
        $approvedDate = ($status === 'paid' && $venda->data_finalizada)
            ? self::toUtc($venda->data_finalizada)
            : null;

        $refundedAt = null;
        if ($status === 'refunded') {
            $reembolso = (new Reembolso())->find(
                "venda_id = :v AND status = 4",
                "v={$venda->id}"
            )->order("criado_em DESC")->limit(1)->fetch();
            if ($reembolso) {
                $refundedAt = self::toUtc($reembolso->criado_em);
            }
        }

        $tracking = self::resolveTracking($venda);

        $totalCents    = (int) round($venda->valor_bruto   * 100);
        $taxaCents     = (int) round($venda->valor_taxa    * 100);
        $liquidoCents  = (int) round($venda->valor_liquido * 100);
        if ($liquidoCents <= 0) {
            $liquidoCents = $totalCents;
        }

        return [
            'orderId'       => $orderId,
            'platform'      => self::PLATFORM,
            'paymentMethod' => $paymentMethod,
            'status'        => $status,
            'createdAt'     => $createdAt,
            'approvedDate'  => $approvedDate,
            'refundedAt'    => $refundedAt,
            'customer'      => [
                'name'     => $comprador->nome     ?? '',
                'email'    => $comprador->email    ?? '',
                'phone'    => preg_replace('/\D/', '', $comprador->telefone ?? '') ?: null,
                'document' => preg_replace('/\D/', '', $comprador->cpf_cnpj ?? '') ?: null,
                'country'  => 'BR',
                'ip'       => $clientIp,
            ],
            'products' => [[
                'id'        => (string) $venda->produto,
                'name'      => $produto->nome ?? 'Produto',
                'planId'    => $plano ? (string) $venda->plano : null,
                'planName'  => $plano ? ($plano->nome ?? null) : null,
                'quantity'  => 1,
                'priceInCents' => $totalCents,
            ]],
            'trackingParameters' => $tracking,
            'commission' => [
                'totalPriceInCents'      => $totalCents,
                'gatewayFeeInCents'      => $taxaCents,
                'userCommissionInCents'  => $liquidoCents,
            ],
        ];
    }

    /**
     * Resolve UTMs com prioridade:
     *   1. Campos já salvos na venda (vieram da URL no momento do checkout)
     *   2. Lookup em pixel_leads pelo IP do comprador (veio pelo WhatsApp)
     */
    private static function resolveTracking(Venda $venda): array
    {
        $tracking = [
            'src'          => $venda->src          ?: null,
            'sck'          => null,
            'utm_source'   => $venda->utm_source   ?: null,
            'utm_campaign' => $venda->utm_campaign ?: null,
            'utm_medium'   => $venda->utm_medium   ?: null,
            'utm_content'  => $venda->utm_content  ?: null,
            'utm_term'     => null,
        ];

        $semUtms = !$tracking['utm_source'] && !$tracking['utm_campaign'] && !$tracking['src'];

        if (!$semUtms) {
            self::log([
                'venda'        => $venda->transacao,
                'ip'           => $venda->ip,
                'match'        => 'url',
                'utm_source'   => $tracking['utm_source'],
                'utm_campaign' => $tracking['utm_campaign'],
            ]);
            return $tracking;
        }

        // Usa o IP do cliente salvo na venda (capturado no checkout, não o IP do webhook)
        $clientIp = $venda->ip ?: null;

        // Sem UTMs na URL — tenta match por IP em pixel_leads
        if ($clientIp) {
            $lead = (new PixelLead())->findByIp($clientIp);
            if ($lead) {
                $tracking['src']          = $lead->src          ?: $tracking['src'];
                $tracking['sck']          = $lead->sck          ?: null;
                $tracking['utm_source']   = $lead->utm_source   ?: null;
                $tracking['utm_campaign'] = $lead->utm_campaign ?: null;
                $tracking['utm_medium']   = $lead->utm_medium   ?: null;
                $tracking['utm_content']  = $lead->utm_content  ?: null;
                $tracking['utm_term']     = $lead->utm_term     ?: null;

                self::log([
                    'venda'         => $venda->transacao,
                    'ip'            => $clientIp,
                    'match'         => 'ip_ok',
                    'pixel_lead_id' => $lead->id,
                    'utm_source'    => $tracking['utm_source'],
                    'utm_campaign'  => $tracking['utm_campaign'],
                ]);
            } else {
                self::log([
                    'venda' => $venda->transacao,
                    'ip'    => $clientIp,
                    'match' => 'ip_nao_encontrado',
                ]);
            }
        } else {
            self::log([
                'venda' => $venda->transacao,
                'ip'    => null,
                'match' => 'sem_ip',
            ]);
        }

        return $tracking;
    }

    private static function log(array $data): void
    {
        $linha = date('Y-m-d H:i:s') . ' ' . json_encode($data) . PHP_EOL;
        $arquivo = fopen(DIRETORIO . "logs/utmify.txt", 'a+');
        if ($arquivo) {
            fwrite($arquivo, $linha);
            fclose($arquivo);
        }
    }

    private static function post(string $apiKey, array $payload): array
    {
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $ch = curl_init(self::ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-token: ' . $apiKey,
            ],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $erro = curl_error($ch);
        curl_close($ch);

        self::log([
            'evento'      => 'request',
            'http_code'   => $code,
            'curl_error'  => $erro ?: null,
            'payload'     => $payload,
            'response'    => $body,
        ]);

        return ['http_code' => $code, 'body' => $body];
    }

    /** Converte datetime BRT (America/Sao_Paulo −3) para UTC */
    private static function toUtc(?string $datetime): ?string
    {
        if (!$datetime) return null;
        try {
            $dt = new \DateTime($datetime, new \DateTimeZone('America/Sao_Paulo'));
            $dt->setTimezone(new \DateTimeZone('UTC'));
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $datetime;
        }
    }
}

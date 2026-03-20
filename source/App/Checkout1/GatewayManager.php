<?php

namespace Source\App\Checkout1;

use Source\Models\Configuracoes\ConfiguracaoGateway;
use Source\App\Checkout1\EfiBank;

/**
 * GatewayManager – Roteador inteligente de gateways de pagamento
 *
 * Modos:
 *   dedicado       → usa apenas o gateway configurado (comportamento legado)
 *   balanceado     → tenta o gateway principal; se falhar, tenta automaticamente o secundário
 *   personalizado  → admin escolhe um gateway específico por tipo de cobrança (pix, cartão, boleto)
 *
 * Gateways suportados: PagarMe, Stripe
 *
 * Resposta normalizada (objeto retornado pelos métodos públicos):
 *   transacao_id     string       ID da transação no gateway
 *   status_transacao int          Status interno (1=aguardando, 2=análise, 3=pré-auth, 4=paga, 5=cancelada)
 *   meio_pagamento   string       "PagarMe" | "Stripe"
 *   gateway_usado    string       "pagarme" | "stripe"
 *   qr_code          string|null  String Pix copia-e-cola (PIX)
 *   qr_code_image    string|null  URL PNG do QR code (Stripe PIX)
 *   link_boleto      string|null  URL do boleto
 *   linha_digitavel  string|null  Linha digitável do boleto
 *   erro             bool         true se o pagamento falhou em todos os gateways
 *   mensagem_erro    string|null  Mensagem de erro para exibir ao usuário
 *   raw              object       Resposta original do gateway utilizado
 */
class GatewayManager
{
    private string $modo;
    private string $gatewayPrincipal;
    private string $gatewayDedicado;
    private string $gatewayPix;
    private string $gatewayCartao;
    private string $gatewayBoleto;

    // Mapeamento status PagarMe v5 → status interno
    private const STATUS_PAGARME = [
        'pending'   => 1,
        'paid'      => 4,
        'canceled'  => 5,
        'failed'    => 5,
        'processing'=> 2,
        'authorized'=> 3,
    ];

    // Mapeamento status Stripe → status interno
    private const STATUS_STRIPE = [
        'requires_action'         => 1,
        'requires_confirmation'   => 1,
        'processing'              => 2,
        'requires_capture'        => 3,
        'succeeded'               => 4,
        'requires_payment_method' => 5,
        'canceled'                => 5,
    ];

    public function __construct()
    {
        $config = (new ConfiguracaoGateway())->findById(1);

        if ($config) {
            $this->modo             = (string)($config->modo              ?? 'dedicado');
            $this->gatewayPrincipal = (string)($config->gateway_principal ?? 'pagarme');
            $this->gatewayDedicado  = (string)($config->gateway_dedicado  ?? 'pagarme');
            $this->gatewayPix       = (string)($config->gateway_pix       ?? 'pagarme');
            $this->gatewayCartao    = (string)($config->gateway_cartao    ?? 'pagarme');
            $this->gatewayBoleto    = (string)($config->gateway_boleto    ?? 'pagarme');
        } else {
            // fallback seguro se a tabela ainda não foi migrada
            $this->modo             = 'dedicado';
            $this->gatewayPrincipal = 'pagarme';
            $this->gatewayDedicado  = 'pagarme';
            $this->gatewayPix       = 'pagarme';
            $this->gatewayCartao    = 'pagarme';
            $this->gatewayBoleto    = 'pagarme';
        }
    }

    // -------------------------------------------------------------------------
    // API pública
    // -------------------------------------------------------------------------

    public function compraPix(array $data): object
    {
        $temFallback = $this->modo === 'balanceado';

        foreach ($this->gatewaysOrdenados('pix') as $gw) {
            try {
                $raw    = $this->tentarPix($gw, $data);
                $result = $this->normalizarPix($gw, $raw);

                if (!$result->erro) {
                    return $result;
                }

                error_log("[GatewayManager][PIX] Falha em '{$gw}': {$result->mensagem_erro}." .
                    ($temFallback ? " Tentando próximo..." : ""));

            } catch (\Throwable $e) {
                error_log("[GatewayManager][PIX] Exceção em '{$gw}': " . $e->getMessage());
                if (!$temFallback) {
                    return $this->respostaErro($gw, $e->getMessage());
                }
            }
        }

        return $this->respostaErro($this->gatewayAtivo('pix'), "Falha ao gerar Pix em todos os gateways disponíveis.");
    }

    public function compraCartao(array $data): object
    {
        $temFallback = $this->modo === 'balanceado';

        foreach ($this->gatewaysOrdenados('cartao') as $gw) {
            try {
                $raw    = $this->tentarCartao($gw, $data);
                $result = $this->normalizarCartao($gw, $raw);

                if (!$result->erro) {
                    return $result;
                }

                error_log("[GatewayManager][Cartão] Falha em '{$gw}': {$result->mensagem_erro}." .
                    ($temFallback ? " Tentando próximo..." : ""));

            } catch (\Throwable $e) {
                error_log("[GatewayManager][Cartão] Exceção em '{$gw}': " . $e->getMessage());
                if (!$temFallback) {
                    return $this->respostaErro($gw, $e->getMessage());
                }
            }
        }

        return $this->respostaErro($this->gatewayAtivo('cartao'), "Falha ao processar cartão em todos os gateways disponíveis.");
    }

    public function compraBoleto(array $data): object
    {
        $temFallback = $this->modo === 'balanceado';

        foreach ($this->gatewaysOrdenados('boleto') as $gw) {
            try {
                $raw    = $this->tentarBoleto($gw, $data);
                $result = $this->normalizarBoleto($gw, $raw);

                if (!$result->erro) {
                    return $result;
                }

                error_log("[GatewayManager][Boleto] Falha em '{$gw}': {$result->mensagem_erro}." .
                    ($temFallback ? " Tentando próximo..." : ""));

            } catch (\Throwable $e) {
                error_log("[GatewayManager][Boleto] Exceção em '{$gw}': " . $e->getMessage());
                if (!$temFallback) {
                    return $this->respostaErro($gw, $e->getMessage());
                }
            }
        }

        return $this->respostaErro($this->gatewayAtivo('boleto'), "Falha ao gerar boleto em todos os gateways disponíveis.");
    }

    // -------------------------------------------------------------------------
    // Roteamento
    // -------------------------------------------------------------------------

    /**
     * Retorna os gateways na ordem de tentativa para um tipo de cobrança.
     * @param string $tipo  'pix' | 'cartao' | 'boleto'
     */
    private function gatewaysOrdenados(string $tipo = 'cartao'): array
    {
        if ($this->modo === 'dedicado') {
            return [$this->gatewayDedicado];
        }

        if ($this->modo === 'personalizado') {
            // Gateway fixo por tipo — sem fallback
            return [$this->gatewayParaTipo($tipo)];
        }

        // balanceado: principal primeiro, depois os outros dois em sequência
        $todos      = ['pagarme', 'stripe', 'efibank'];
        $ordenados  = array_merge(
            [$this->gatewayPrincipal],
            array_values(array_filter($todos, fn($g) => $g !== $this->gatewayPrincipal))
        );
        return $ordenados;
    }

    private function gatewayParaTipo(string $tipo): string
    {
        switch ($tipo) {
            case 'pix':    return $this->gatewayPix;
            case 'cartao': return $this->gatewayCartao;
            case 'boleto': return $this->gatewayBoleto;
            default:       return $this->gatewayDedicado;
        }
    }

    private function gatewayAtivo(string $tipo = 'cartao'): string
    {
        if ($this->modo === 'personalizado') {
            return $this->gatewayParaTipo($tipo);
        }
        return $this->modo === 'dedicado' ? $this->gatewayDedicado : $this->gatewayPrincipal;
    }

    // -------------------------------------------------------------------------
    // Chamadas para cada gateway
    // -------------------------------------------------------------------------

    private function tentarPix(string $gw, array $data): object
    {
        switch ($gw) {
            case 'pagarme': return (new PagarMe)->compraPix($data);
            case 'stripe':  return (new Stripe)->compraPix($data);
            case 'efibank': return (new EfiBank)->compraPix($data);
            default: throw new \Exception("Gateway '{$gw}' não suportado para Pix.");
        }
    }

    private function tentarCartao(string $gw, array $data): object
    {
        switch ($gw) {
            case 'pagarme': return (new PagarMe)->compraCartao($data);
            case 'stripe':  return (new Stripe)->compraCartao($data);
            case 'efibank': return (new EfiBank)->compraCartao($data);
            default: throw new \Exception("Gateway '{$gw}' não suportado para cartão.");
        }
    }

    private function tentarBoleto(string $gw, array $data): object
    {
        if ($gw === 'stripe') {
            throw new \Exception("Stripe não suporta boleto bancário.");
        }
        switch ($gw) {
            case 'pagarme': return (new PagarMe)->compraBoleto($data);
            case 'efibank': return (new EfiBank)->compraBoleto($data);
            default: throw new \Exception("Gateway '{$gw}' não suportado para boleto.");
        }
    }

    // -------------------------------------------------------------------------
    // Normalização de respostas
    // -------------------------------------------------------------------------

    private function normalizarPix(string $gw, object $raw): object
    {
        if ($gw === 'pagarme') {
            $qrCode = $raw->charges[0]->last_transaction->qr_code ?? null;
            $erro   = !isset($raw->id) || !empty($raw->errors) || empty($qrCode);
            $msg    = $erro ? ($this->extractErrorPagarMe($raw) ?? "Falha ao gerar Pix PagarMe") : null;

            return (object)[
                'transacao_id'    => $raw->id ?? null,
                'status_transacao' => 1, // PIX sempre começa aguardando
                'meio_pagamento'  => 'PagarMe',
                'gateway_usado'   => 'pagarme',
                'qr_code'         => $qrCode,
                'qr_code_image'   => null,
                'link_boleto'     => null,
                'linha_digitavel' => null,
                'erro'            => $erro,
                'mensagem_erro'   => $msg,
                'raw'             => $raw,
            ];
        }

        // EfiBank Pix
        if ($gw === 'efibank') {
            $qrCode = $raw->pixCopiaECola ?? null;
            $qrImage = $raw->qrCodeImagem  ?? null;
            $txid    = $raw->txid          ?? null;
            $erro    = empty($qrCode) || (isset($raw->nome) && str_contains((string)($raw->nome ?? ''), 'error'));
            $msg     = $erro ? ($raw->mensagem ?? "Falha ao gerar Pix EfiBank") : null;

            return (object)[
                'transacao_id'    => $txid,
                'status_transacao' => 1,
                'meio_pagamento'  => 'EfiBank',
                'gateway_usado'   => 'efibank',
                'qr_code'         => $qrCode,
                'qr_code_image'   => $qrImage,
                'link_boleto'     => null,
                'linha_digitavel' => null,
                'erro'            => $erro,
                'mensagem_erro'   => $msg,
                'raw'             => $raw,
            ];
        }

        // Stripe
        $pixAction = $raw->next_action->pix_display_qr_code ?? null;
        $qrCode    = $pixAction->data          ?? null;
        $qrImage   = $pixAction->image_url_png ?? null;
        $erro      = isset($raw->error) || !isset($raw->id) || empty($qrCode);
        $msg       = $erro ? ($raw->error->message ?? "Falha ao gerar Pix Stripe") : null;

        return (object)[
            'transacao_id'    => $raw->id ?? null,
            'status_transacao' => 1,
            'meio_pagamento'  => 'Stripe',
            'gateway_usado'   => 'stripe',
            'qr_code'         => $qrCode,
            'qr_code_image'   => $qrImage,
            'link_boleto'     => null,
            'linha_digitavel' => null,
            'erro'            => $erro,
            'mensagem_erro'   => $msg,
            'raw'             => $raw,
        ];
    }

    private function normalizarCartao(string $gw, object $raw): object
    {
        if ($gw === 'pagarme') {
            $status = self::STATUS_PAGARME[$raw->status ?? ''] ?? 5;
            $erro   = !isset($raw->id) || !empty($raw->errors) || $status === 5;
            $msg    = $erro ? ($this->extractErrorPagarMe($raw) ?? "Pagamento recusado") : null;

            return (object)[
                'transacao_id'    => $raw->id ?? null,
                'status_transacao' => $status,
                'meio_pagamento'  => 'PagarMe',
                'gateway_usado'   => 'pagarme',
                'qr_code'         => null,
                'qr_code_image'   => null,
                'link_boleto'     => null,
                'linha_digitavel' => null,
                'erro'            => $erro,
                'mensagem_erro'   => $msg,
                'raw'             => $raw,
            ];
        }

        // EfiBank cartão
        if ($gw === 'efibank') {
            // EfiBank retorna data.charge_id e data.status
            $efStatus = strtolower((string)($raw->data->status ?? ''));
            $statusMap = [
                'new'         => 1,
                'waiting'     => 1,
                'identified'  => 2,
                'paid'        => 4,
                'unpaid'      => 5,
                'refunded'    => 6,
                'contested'   => 7,
                'canceled'    => 5,
            ];
            $status  = $statusMap[$efStatus] ?? 5;
            $chargeId = (string)($raw->data->charge_id ?? '');
            $erro    = empty($chargeId) || $status === 5;
            $msg     = $erro ? ($raw->data->message ?? "Pagamento recusado") : null;

            return (object)[
                'transacao_id'    => $chargeId,
                'status_transacao' => $status,
                'meio_pagamento'  => 'EfiBank',
                'gateway_usado'   => 'efibank',
                'qr_code'         => null,
                'qr_code_image'   => null,
                'link_boleto'     => null,
                'linha_digitavel' => null,
                'erro'            => $erro,
                'mensagem_erro'   => $msg,
                'raw'             => $raw,
            ];
        }

        // Stripe
        $stripeStatus = $raw->status ?? '';
        $status       = self::STATUS_STRIPE[$stripeStatus] ?? 5;
        $erro         = isset($raw->error) || !isset($raw->id) || $status === 5;
        $msg          = $erro ? ($raw->error->message ?? "Pagamento recusado") : null;

        return (object)[
            'transacao_id'    => $raw->id ?? null,
            'status_transacao' => $status,
            'meio_pagamento'  => 'Stripe',
            'gateway_usado'   => 'stripe',
            'qr_code'         => null,
            'qr_code_image'   => null,
            'link_boleto'     => null,
            'linha_digitavel' => null,
            'erro'            => $erro,
            'mensagem_erro'   => $msg,
            'raw'             => $raw,
        ];
    }

    private function normalizarBoleto(string $gw, object $raw): object
    {
        // EfiBank boleto
        if ($gw === 'efibank') {
            $link     = $raw->data->link    ?? null;
            $barcode  = $raw->data->barcode ?? null;
            $chargeId = (string)($raw->data->charge_id ?? '');
            $erro     = empty($chargeId);
            $msg      = $erro ? ($raw->data->message ?? "Falha ao gerar boleto EfiBank") : null;

            return (object)[
                'transacao_id'    => $chargeId,
                'status_transacao' => $erro ? 5 : 1,
                'meio_pagamento'  => 'EfiBank',
                'gateway_usado'   => 'efibank',
                'qr_code'         => null,
                'qr_code_image'   => null,
                'link_boleto'     => $link,
                'linha_digitavel' => $barcode,
                'erro'            => $erro,
                'mensagem_erro'   => $msg,
                'raw'             => $raw,
            ];
        }

        $status = self::STATUS_PAGARME[$raw->status ?? ''] ?? 1;
        $erro   = !isset($raw->id) || !empty($raw->errors);

        // PagarMe v5 boleto
        $linkBoleto     = $raw->charges[0]->last_transaction->pdf  ?? null;
        $linhaDigitavel = $raw->charges[0]->last_transaction->line ?? null;

        $msg = $erro ? ($this->extractErrorPagarMe($raw) ?? "Falha ao gerar boleto") : null;

        return (object)[
            'transacao_id'    => $raw->id ?? null,
            'status_transacao' => $erro ? 5 : $status,
            'meio_pagamento'  => 'PagarMe',
            'gateway_usado'   => 'pagarme',
            'qr_code'         => null,
            'qr_code_image'   => null,
            'link_boleto'     => $linkBoleto,
            'linha_digitavel' => $linhaDigitavel,
            'erro'            => $erro,
            'mensagem_erro'   => $msg,
            'raw'             => $raw,
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function extractErrorPagarMe(object $raw): ?string
    {
        if (!empty($raw->errors)) {
            $msgs = array_map(fn($e) => $e->message ?? '', (array)$raw->errors);
            return implode('; ', array_filter($msgs));
        }
        if (isset($raw->message)) {
            return (string)$raw->message;
        }
        return null;
    }

    private function respostaErro(string $gw, string $msg): object
    {
        return (object)[
            'transacao_id'    => null,
            'status_transacao' => 5,
            'meio_pagamento'  => $gw === 'pagarme' ? 'PagarMe' : 'Stripe',
            'gateway_usado'   => $gw,
            'qr_code'         => null,
            'qr_code_image'   => null,
            'link_boleto'     => null,
            'linha_digitavel' => null,
            'erro'            => true,
            'mensagem_erro'   => $msg,
            'raw'             => (object)[],
        ];
    }
}

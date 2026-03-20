<?php

namespace Source\App\Configuracoes;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Configuracoes\ConfiguracaoGateway;
use Source\Models\Usuarios\Usuario;
use Source\Models\Comissao\Banco;
use Source\App\Checkout1\Stripe;

class AppConfiguracaoGateway
{
    private $view;
    private $sessao;
    private string $tema;

    public function __construct($router)
    {
        $this->tema   = @$_SESSION["tema"] ?: "tema1";
        $this->view   = Engine::create(__DIR__ . "/../../../themes/{$this->tema}", "php");
        $this->view->addData(["router" => $router]);
        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    // -------------------------------------------------------------------------
    // Página principal de configuração
    // -------------------------------------------------------------------------

    public function gateway($data)
    {
        if ($_SESSION["tipo"] != 1) {
            header("Location: " . url(""));
            exit;
        }

        $config = (new ConfiguracaoGateway())->findById(1);

        echo $this->view->render("configuracoes/gateway", [
            "title"  => "Configuração de Gateway",
            "config" => $config,
        ]);
    }

    // -------------------------------------------------------------------------
    // Salvar configuração
    // -------------------------------------------------------------------------

    public function salvar($data)
    {
        if ($_SESSION["tipo"] != 1) {
            header("Location: " . url(""));
            exit;
        }

        $config = (new ConfiguracaoGateway())->findById(1);
        if (!$config) {
            $config = new ConfiguracaoGateway();
        }

        $gws   = ["pagarme", "stripe", "efibank"];
        $modos = ["balanceado", "dedicado", "personalizado"];

        $modo             = in_array($data["modo"] ?? "", $modos) ? $data["modo"] : "dedicado";
        $gatewayPrincipal = in_array($data["gateway_principal"] ?? "", $gws) ? $data["gateway_principal"] : "pagarme";
        $gatewayDedicado  = in_array($data["gateway_dedicado"]  ?? "", $gws) ? $data["gateway_dedicado"]  : "pagarme";
        $gatewayPix       = in_array($data["gateway_pix"]       ?? "", $gws) ? $data["gateway_pix"]       : "pagarme";
        $gatewayCartao    = in_array($data["gateway_cartao"]    ?? "", $gws) ? $data["gateway_cartao"]    : "pagarme";
        $gatewayBoleto    = in_array($data["gateway_boleto"]    ?? "", $gws) ? $data["gateway_boleto"]    : "pagarme";

        $config->modo                       = $modo;
        $config->gateway_principal          = $gatewayPrincipal;
        $config->gateway_dedicado           = $gatewayDedicado;
        $config->gateway_pix                = $gatewayPix;
        $config->gateway_cartao             = $gatewayCartao;
        $config->gateway_boleto             = $gatewayBoleto;
        $config->usuario_ultima_alteracao   = $_SESSION["id"];

        if ($config->save()) {
            error_log("[Gateway] Config salva: modo={$modo} principal={$gatewayPrincipal} dedicado={$gatewayDedicado}");
            header("Location: " . url("gateway?msg=Configuração salva com sucesso&classCss=bg-success"));
        } else {
            $msg = $config->fail() ? $config->fail()->getMessage() : "Erro ao salvar";
            header("Location: " . url("gateway?msg=" . urlencode($msg) . "&classCss=bg-danger"));
        }
        exit;
    }

    // -------------------------------------------------------------------------
    // Migração de usuários existentes para Stripe
    // -------------------------------------------------------------------------

    /**
     * Lista usuários que já possuem conta PagarMe mas ainda não têm Stripe.
     * Permite criar a conta Stripe para cada um individualmente.
     */
    public function migracaoStripe($data)
    {
        if ($_SESSION["tipo"] != 1) {
            header("Location: " . url(""));
            exit;
        }

        // Busca bancos com pagarme_conta preenchido mas sem stripe_account_id
        $bancos = (new Banco())->find(
            "pagarme_conta IS NOT NULL AND pagarme_conta != '' AND (stripe_account_id IS NULL OR stripe_account_id = '')"
        )->fetch(true);

        echo $this->view->render("configuracoes/gateway-migracao", [
            "title"  => "Migração Stripe",
            "bancos" => $bancos ?? [],
        ]);
    }

    /**
     * Cria conta Stripe Express para um usuário existente e salva o ID + link de onboarding.
     */
    public function criarContaStripe($data)
    {
        if ($_SESSION["tipo"] != 1) {
            echo json_encode(["erro" => true, "msg" => "Sem permissão"]);
            return;
        }

        header("Content-Type: application/json");

        $bancoId = (int)($data["banco_id"] ?? 0);
        if (!$bancoId) {
            echo json_encode(["erro" => true, "msg" => "banco_id inválido"]);
            return;
        }

        $banco = (new Banco())->findById($bancoId);
        if (!$banco) {
            echo json_encode(["erro" => true, "msg" => "Banco não encontrado"]);
            return;
        }

        $usuario = (new Usuario())->findById($banco->usuario);
        if (!$usuario) {
            echo json_encode(["erro" => true, "msg" => "Usuário não encontrado"]);
            return;
        }

        try {
            $stripe = new Stripe();

            // Cria conta Express
            $dadosConta = [
                "nome"      => $banco->titular,
                "email"     => $usuario->email,
                "cpf"       => $banco->documento ?? $usuario->cpf ?? "",
                "tipo"      => "express",
            ];

            $conta = $stripe->criarContaConectada($dadosConta);

            if (empty($conta->id)) {
                $msg = $conta->error->message ?? "Erro ao criar conta na Stripe";
                echo json_encode(["erro" => true, "msg" => $msg]);
                return;
            }

            $stripeAccountId = $conta->id;

            // Gera link de onboarding
            $link = $stripe->linkOnboarding(
                $stripeAccountId,
                url("gateway/migracao-stripe?msg=Link+expirado,+solicite+novo"),
                url("gateway/migracao-stripe?msg=Cadastro+Stripe+concluído")
            );

            $onboardingUrl = $link->url ?? null;

            // Salva no banco e no usuario
            $banco->stripe_account_id   = $stripeAccountId;
            $banco->stripe_onboarding_url = $onboardingUrl;
            $banco->save();

            $usuario->stripe_account_id = $stripeAccountId;
            $usuario->save();

            error_log("[Stripe Migração] banco_id={$bancoId} acct={$stripeAccountId}");

            echo json_encode([
                "erro"           => false,
                "account_id"     => $stripeAccountId,
                "onboarding_url" => $onboardingUrl,
            ]);

        } catch (\Throwable $e) {
            error_log("[Stripe Migração] Exceção: " . $e->getMessage());
            echo json_encode(["erro" => true, "msg" => $e->getMessage()]);
        }
    }

    /**
     * Regera o link de onboarding para um banco já migrado.
     */
    public function reenviarOnboarding($data)
    {
        if ($_SESSION["tipo"] != 1) {
            echo json_encode(["erro" => true, "msg" => "Sem permissão"]);
            return;
        }

        header("Content-Type: application/json");

        $bancoId = (int)($data["banco_id"] ?? 0);
        $banco   = (new Banco())->findById($bancoId);

        if (!$banco || empty($banco->stripe_account_id)) {
            echo json_encode(["erro" => true, "msg" => "Conta Stripe não encontrada para este banco"]);
            return;
        }

        try {
            $stripe = new Stripe();
            $link   = $stripe->linkOnboarding(
                $banco->stripe_account_id,
                url("gateway/migracao-stripe?msg=Link+expirado,+solicite+novo"),
                url("gateway/migracao-stripe?msg=Cadastro+Stripe+concluído")
            );

            $onboardingUrl = $link->url ?? null;

            $banco->stripe_onboarding_url = $onboardingUrl;
            $banco->save();

            echo json_encode(["erro" => false, "onboarding_url" => $onboardingUrl]);

        } catch (\Throwable $e) {
            echo json_encode(["erro" => true, "msg" => $e->getMessage()]);
        }
    }
}

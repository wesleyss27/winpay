<?php

namespace Source\App\MobileApi;

use Source\Models\Sessao\Sessao;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\RecuperarSenha;
use Source\Models\Vendas\Venda;
use Source\App\MobileApi\PushMobile;
use Source\Helpers\Emails;

class AppMobileApi
{
    private function cors(): void
    {
        header("Content-Type: application/json; charset=utf-8");
    }

    private function json($data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    private function getAuthUser(): ?object
    {
        $token = null;

        // Apache pode entregar via getallheaders(), nginx via $_SERVER
        $headers = getallheaders();
        foreach ($headers as $k => $v) {
            if (strtolower($k) === "authorization") {
                $token = trim(str_replace("Bearer", "", $v));
                break;
            }
        }

        if (!$token && !empty($_SERVER["HTTP_AUTHORIZATION"])) {
            $token = trim(str_replace("Bearer", "", $_SERVER["HTTP_AUTHORIZATION"]));
        }

        if (!$token) {
            return null;
        }

        $sessao = (new Sessao())
            ->find("sessao = :s AND expiration_date > NOW()", "s={$token}")
            ->fetch();

        if (!$sessao) {
            return null;
        }

        return (new Usuario())->findById((int) $sessao->usuario);
    }

    /** OPTIONS /mobile/* – preflight CORS */
    public function options($data): void
    {
        $this->cors();
        $this->json(["ok" => true]);
    }

    /** POST /mobile/login */
    public function login($data): void
    {
        $this->cors();

        $input    = json_decode(file_get_contents("php://input"), true) ?? [];
        $email    = trim($input["email"] ?? "");
        $password = $input["password"] ?? "";

        if (!$email || !$password) {
            $this->json(["error" => "Email e senha são obrigatórios."], 400);
        }

        $acesso = (new Usuario())
            ->find("email = :e OR login = :e", "e={$email}")
            ->fetch();

        if (!$acesso || !password_verify($password, $acesso->senha)) {
            $this->json(["error" => "Credenciais inválidas."], 401);
        }

        // Token com validade longa para mobile (30 dias)
        $expiration = date("Y-m-d H:i:s", strtotime("+30 days"));
        $tokenHash  = password_hash($acesso->email . $expiration . uniqid("", true), PASSWORD_DEFAULT);

        $sessao                  = new Sessao();
        $sessao->usuario         = $acesso->id;
        $sessao->ip              = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
        $sessao->expiration_date = $expiration;
        $sessao->sessao          = $tokenHash;

        if (!$sessao->save()) {
            $this->json(["error" => "Erro interno ao criar sessão."], 500);
        }

        $this->json([
            "token" => $tokenHash,
            "user"  => [
                "id"    => (int) $acesso->id,
                "name"  => $acesso->nome,
                "email" => $acesso->email,
                "type"  => (int) $acesso->tipo_usuario,
            ],
        ]);
    }

    /** GET /mobile/dashboard */
    public function dashboard($data): void
    {
        $this->cors();

        $usuario = $this->getAuthUser();
        if (!$usuario) {
            $this->json(["error" => "Não autorizado."], 401);
        }

        $uid  = (int) $usuario->id;
        $tipo = (int) $usuario->tipo_usuario;

        // Filtro por produtor/afiliado (admin vê tudo)
        $filtroUsuario = $tipo !== 1 ? "(produtor = {$uid} OR afiliado = {$uid})" : "1=1";

        $venda = new Venda();

        // ── Hoje ────────────────────────────────────────────────────────
        $hojeInicio = date("Y-m-d") . " 00:00:00";
        $hojeFim    = date("Y-m-d") . " 23:59:59";

        $filtroHoje = "created_at BETWEEN '{$hojeInicio}' AND '{$hojeFim}' AND {$filtroUsuario}";

        $vendasHoje   = $venda->find("{$filtroHoje} AND status_transacao = 4")->count();
        $valorHojeObj = $venda->find("{$filtroHoje} AND status_transacao = 4", null, "SUM(valor_liquido) as soma")->fetch();
        $valorHoje    = (float) ($valorHojeObj->soma ?? 0);

        // ── Ontem (para calcular variação) ──────────────────────────────
        $ontemInicio = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
        $ontemFim    = date("Y-m-d", strtotime("-1 day")) . " 23:59:59";
        $filtroOntem = "created_at BETWEEN '{$ontemInicio}' AND '{$ontemFim}' AND {$filtroUsuario}";

        $valorOntemObj = $venda->find("{$filtroOntem} AND status_transacao = 4", null, "SUM(valor_liquido) as soma")->fetch();
        $valorOntem    = (float) ($valorOntemObj->soma ?? 0);

        $variacaoDia = $valorOntem > 0
            ? round((($valorHoje - $valorOntem) / $valorOntem) * 100, 1)
            : 0.0;

        // ── Mês atual ───────────────────────────────────────────────────
        $mesInicio = date("Y-m-01") . " 00:00:00";
        $mesFim    = date("Y-m-d") . " 23:59:59";
        $filtroMes = "created_at BETWEEN '{$mesInicio}' AND '{$mesFim}' AND {$filtroUsuario}";

        $vendasMes   = $venda->find("{$filtroMes} AND status_transacao = 4")->count();
        $valorMesObj = $venda->find("{$filtroMes} AND status_transacao = 4", null, "SUM(valor_liquido) as soma")->fetch();
        $valorMes    = (float) ($valorMesObj->soma ?? 0);
        $ticketMedio = $vendasMes > 0 ? round($valorMes / $vendasMes, 2) : 0.0;

        // ── Chargeback rate (últimos 30 dias) ────────────────────────────
        $filtro30Dias = "created_at >= '" . date("Y-m-d H:i:s", strtotime("-30 days")) . "' AND {$filtroUsuario}";
        $total30      = $venda->find($filtro30Dias)->count();
        $cb30         = $venda->find("{$filtro30Dias} AND status_transacao = 7")->count();
        $cbRate       = $total30 > 0 ? round(($cb30 / $total30) * 100, 2) : 0.0;

        // ── Gráfico semanal (últimos 7 dias) ────────────────────────────
        $labelsPt = ["Sun" => "Dom", "Mon" => "Seg", "Tue" => "Ter", "Wed" => "Qua", "Thu" => "Qui", "Fri" => "Sex", "Sat" => "Sáb"];
        $grafico  = [];

        for ($i = 6; $i >= 0; $i--) {
            $diaInicio  = date("Y-m-d", strtotime("-{$i} days")) . " 00:00:00";
            $diaFim     = date("Y-m-d", strtotime("-{$i} days")) . " 23:59:59";
            $filtroDia  = "created_at BETWEEN '{$diaInicio}' AND '{$diaFim}' AND {$filtroUsuario} AND status_transacao = 4";
            $valorDiaObj = $venda->find($filtroDia, null, "SUM(valor_liquido) as soma")->fetch();
            $labelEn     = date("D", strtotime("-{$i} days"));

            $grafico[] = [
                "label" => $labelsPt[$labelEn] ?? $labelEn,
                "value" => (float) ($valorDiaObj->soma ?? 0),
            ];
        }

        $this->json([
            "metrics" => [
                "hoje" => [
                    "valor"    => $valorHoje,
                    "vendas"   => $vendasHoje,
                    "variacao" => $variacaoDia,
                ],
                "mes" => [
                    "valor"        => $valorMes,
                    "vendas"       => $vendasMes,
                    "ticket_medio" => $ticketMedio,
                ],
                "chargeback_rate" => $cbRate,
            ],
            "grafico" => $grafico,
        ]);
    }

    /** GET /mobile/vendas */
    public function vendas($data): void
    {
        $this->cors();

        $usuario = $this->getAuthUser();
        if (!$usuario) {
            $this->json(["error" => "Não autorizado."], 401);
        }

        $uid  = (int) $usuario->id;
        $tipo = (int) $usuario->tipo_usuario;

        $filtroUsuario = $tipo !== 1 ? "(produtor = {$uid} OR afiliado = {$uid})" : "1=1";

        $vendas = (new Venda())
            ->find($filtroUsuario)
            ->order("created_at DESC")
            ->limit(20)
            ->fetch(true);

        $formas   = [1 => "Cartão", 2 => "Boleto", 3 => "Pix"];
        $statuses = [
            1 => "aguardando", 2 => "aguardando", 3 => "aguardando",
            4 => "pago",       5 => "cancelado",  6 => "cancelado",
            7 => "chargeback", 8 => "pago",       9 => "aguardando",
        ];

        $result = [];

        if ($vendas) {
            foreach ($vendas as $v) {
                $comprador = $v->dataComprador();
                $result[]  = [
                    "id"      => (int) $v->id,
                    "buyer"   => $comprador->nome ?? "Cliente",
                    "channel" => $formas[(int) $v->forma_pagamento] ?? "—",
                    "amount"  => (float) $v->valor_liquido,
                    "status"  => $statuses[(int) $v->status_transacao] ?? "aguardando",
                    "time"    => date("H:i",    strtotime($v->created_at)),
                    "date"    => date("d/m/Y",  strtotime($v->created_at)),
                ];
            }
        }

        $this->json(["vendas" => $result]);
    }

    /** POST /mobile/registrar-token */
    public function registrarToken($data): void
    {
        $this->cors();

        $usuario = $this->getAuthUser();
        if (!$usuario) {
            $this->json(["error" => "Não autorizado."], 401);
        }

        $input = json_decode(file_get_contents("php://input"), true) ?? [];
        $token = trim($input["expo_token"] ?? "");

        if (!$token) {
            $this->json(["error" => "expo_token é obrigatório."], 400);
        }

        PushMobile::registrarToken((int) $usuario->id, $token);
        $this->json(["ok" => true]);
    }

    /** GET /mobile/preferencias */
    public function getPreferencias($data): void
    {
        $this->cors();

        $usuario = $this->getAuthUser();
        if (!$usuario) {
            $this->json(["error" => "Não autorizado."], 401);
        }

        $prefs = PushMobile::buscarPreferencias((int) $usuario->id);
        $this->json(["preferencias" => $prefs]);
    }

    /** POST /mobile/preferencias */
    public function savePreferencias($data): void
    {
        $this->cors();

        $usuario = $this->getAuthUser();
        if (!$usuario) {
            $this->json(["error" => "Não autorizado."], 401);
        }

        $input = json_decode(file_get_contents("php://input"), true) ?? [];
        $prefs = $input["preferencias"] ?? $input;

        PushMobile::salvarPreferencias((int) $usuario->id, $prefs);
        $this->json(["ok" => true]);
    }

    /** GET /mobile/resumo-diario  (chamado por cron às 23:55) */
    public function resumoDiario($data): void
    {
        $this->cors();

        // Endpoint protegido por chave interna
        $headers = getallheaders();
        $chave   = "";
        foreach ($headers as $k => $v) {
            if (strtolower($k) === "x-cron-key") { $chave = $v; break; }
        }

        if ($chave !== (defined("CRON_SECRET") ? CRON_SECRET : "winpay-cron-2025")) {
            $this->json(["error" => "Não autorizado."], 401);
        }

        $usuario = (int) ($data["usuario"] ?? 0);
        $uid     = $usuario_tipo = 1; // admin vê tudo
        $venda   = new Venda();

        $inicio = date("Y-m-d") . " 00:00:00";
        $fim    = date("Y-m-d") . " 23:59:59";

        $totalObj = $venda->find("created_at BETWEEN '{$inicio}' AND '{$fim}' AND status_transacao = 4", null, "COUNT(*) as qt, SUM(valor_liquido) as soma")->fetch();
        $qt       = (int)   ($totalObj->qt   ?? 0);
        $soma     = (float) ($totalObj->soma ?? 0);

        $titulo = "Resumo do dia 📊";
        $corpo  = "{$qt} venda" . ($qt !== 1 ? "s" : "") . " · R$ " . number_format($soma, 2, ",", ".");

        PushMobile::enviarParaTodos($titulo, $corpo, "recap");
        $this->json(["ok" => true, "vendas" => $qt, "total" => $soma]);
    }

    /**
     * POST /mobile/esqueci-senha
     * Body: { "email": "..." }
     * Gera código e envia e-mail com o código de 4 dígitos.
     */
    public function esqueciSenha($data): void
    {
        $this->cors();

        $input = json_decode(file_get_contents("php://input"), true) ?? [];
        $email = trim(strtolower($input["email"] ?? ""));

        if (!$email) {
            $this->json(["error" => "Email é obrigatório."], 400);
        }

        $usuario = (new Usuario())->find("email = :e", "e={$email}")->fetch();

        // Responde OK mesmo se não encontrou (evita enumeração)
        if (!$usuario) {
            $this->json(["ok" => true]);
        }

        $recuperar                    = new RecuperarSenha();
        $recuperar->usuario           = $usuario->id;
        $recuperar->codigo            = strtoupper(bin2hex(random_bytes(2)));
        $recuperar->codigo_recuperacao = strtoupper(bin2hex(random_bytes(4)));
        $recuperar->save();

        $mail          = new Emails();
        $mailData      = [
            "email"    => $usuario->email,
            "nome"     => $usuario->nome,
            "assunto"  => "Recuperação de senha — Winpay",
            "mensagem" => "Use o código abaixo no app Winpay para redefinir sua senha:<br><br>"
                        . "<h2 style='letter-spacing:6px'>{$recuperar->codigo_recuperacao}</h2><br>"
                        . "O código expira em 30 minutos. Se não foi você, ignore este e-mail.",
        ];
        $mail->enviaSendGrid($mailData);

        // Retorna o `codigo` (link interno) para o app prosseguir para a tela de redefinição
        $this->json(["ok" => true, "codigo" => $recuperar->codigo, "usuario_id" => (int) $usuario->id]);
    }

    /**
     * GET /mobile/versao
     * Retorna a versão mínima exigida do app.
     * Altere MIN_VERSION para forçar atualização em versões antigas.
     */
    public function versao($data): void
    {
        $this->cors();

        define('MIN_VERSION', '1.0.0');          // ← altere aqui para forçar update
        define('STORE_URL',   'https://play.google.com/store/apps/details?id=com.winpaymobile');

        $this->json([
            "min_version" => MIN_VERSION,
            "store_url"   => STORE_URL,
        ]);
    }

    /**
     * POST /mobile/redefinir-senha
     * Body: { "usuario_id": 1, "codigo": "ABCD", "codigo_recuperacao": "XXXXXYYY", "nova_senha": "..." }
     */
    public function redefinirSenha($data): void
    {
        $this->cors();

        $input              = json_decode(file_get_contents("php://input"), true) ?? [];
        $usuarioId          = (int)   ($input["usuario_id"]          ?? 0);
        $codigo             = trim(strtoupper($input["codigo"]             ?? ""));
        $codigoRecuperacao  = trim(strtoupper($input["codigo_recuperacao"] ?? ""));
        $novaSenha          = $input["nova_senha"] ?? "";

        if (!$usuarioId || !$codigo || !$codigoRecuperacao || strlen($novaSenha) < 6) {
            $this->json(["error" => "Dados inválidos. A senha deve ter no mínimo 6 caracteres."], 400);
        }

        $recuperar = (new RecuperarSenha())
            ->find("codigo = :c AND codigo_recuperacao = :cr", "c={$codigo}&cr={$codigoRecuperacao}")
            ->fetch();

        if (!$recuperar) {
            $this->json(["error" => "Código inválido ou já utilizado."], 400);
        }

        $usuario = (new Usuario())->findById($usuarioId);
        if (!$usuario) {
            $this->json(["error" => "Usuário não encontrado."], 404);
        }

        $usuario->senha = password_hash($novaSenha, PASSWORD_DEFAULT, ["cost" => 10]);
        if (!$usuario->save()) {
            $this->json(["error" => "Erro ao salvar nova senha."], 500);
        }

        // Invalida o token de recuperação
        $recuperar->destroy();

        $this->json(["ok" => true]);
    }
}

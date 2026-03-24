<?php

namespace Source\App\Financeiro;

session_start();

use League\Plates\Engine;
use Source\App\Checkout1\PagarMe;
use Source\App\Sessao\AppSessao;
use Source\Models\Comissao\Comissao;
use Source\Models\Usuarios\Usuario;
use Source\Models\Permissao\Section;

class AppFinanceiro
{
    private $view;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema    = @$_SESSION["tema"] ?: "tema1";
        $this->view    = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
        $this->section = new Section();
    }

    public function dashboard($data)
    {
        (new AppSessao())->verificaSeLogado();

        $usuario = $this->section->getId();

        // recipient_id da Pagar.me do usuário logado
        $user         = (new Usuario())->findById($usuario);
        $recipientId  = @$user->pagarme_conta;

        // --- Filtros de intervalo ---
        $data_ini = @$_GET["data_ini"] ?: date("Y-m-01");
        $data_fim = @$_GET["data_fim"] ?: date("Y-m-d");
        $data_ini = date("Y-m-d", strtotime($data_ini));
        $data_fim = date("Y-m-d", strtotime($data_fim));
        if ($data_fim < $data_ini) $data_fim = $data_ini;

        // --- Totais no período (banco local) ---
        $comissoesPeriodo = $this->somaComissao($usuario, 1, $data_ini, $data_fim);
        $saquesPeriodo    = abs($this->somaComissao($usuario, 2, $data_ini, $data_fim));
        $antecipacoes     = abs($this->somaComissao($usuario, 3, $data_ini, $data_fim, " AND valor < 0"));

        // --- Totais históricos ---
        $comissoesTotal = $this->somaComissaoTotal($usuario, 1);
        $saquesTotal    = abs($this->somaComissaoTotal($usuario, 2));

        // --- Saldo na Pagar.me ---
        $saldoDisponivel = 0;
        $saldoAguardando = 0;
        if ($recipientId) {
            $saldoPagarme    = (new PagarMe())->saldo($recipientId);
            $saldoDisponivel = isset($saldoPagarme->available_amount)     ? $saldoPagarme->available_amount     / 100 : 0;
            $saldoAguardando = isset($saldoPagarme->waiting_funds_amount) ? $saldoPagarme->waiting_funds_amount / 100 : 0;
        }

        // --- Histórico Pagar.me ---
        $transferencias = $this->buscarTransferencias($recipientId, $data_ini, $data_fim);
        $settlements    = $this->buscarSettlements($recipientId, $data_ini, $data_fim);

        // --- Gráfico: últimos 12 meses ---
        $grafico = $this->dadosGrafico($usuario);

        // --- Comparativo mensal no intervalo ---
        $comparativo = $this->comparativoPeriodo($usuario, $data_ini, $data_fim);

        // --- Lista saques locais no período ---
        $saquesList = (new Comissao())
            ->find("tipo = 2 AND usuario = $usuario AND DATE(created_at) BETWEEN '$data_ini' AND '$data_fim'")
            ->order("id DESC")
            ->fetch(true);

        echo $this->view->render("financeiro/dashboard", [
            "title"            => "Dashboard Financeiro",
            "data_ini"         => $data_ini,
            "data_fim"         => $data_fim,
            "comissoesPeriodo" => $comissoesPeriodo,
            "saquesPeriodo"    => $saquesPeriodo,
            "antecipacoes"     => $antecipacoes,
            "comissoesTotal"   => $comissoesTotal,
            "saquesTotal"      => $saquesTotal,
            "saldoDisponivel"  => $saldoDisponivel,
            "saldoAguardando"  => $saldoAguardando,
            "transferencias"   => $transferencias,
            "settlements"      => $settlements,
            "grafico"          => $grafico,
            "comparativo"      => $comparativo,
            "saquesList"       => $saquesList,
        ]);
    }

    // -----------------------------------------------------------------------

    private function somaComissao($usuario, $tipo, $ini, $fim, $extra = "")
    {
        $r = (new Comissao())
            ->find("tipo = $tipo AND usuario = $usuario AND DATE(created_at) BETWEEN '$ini' AND '$fim'" . $extra, null, "SUM(valor) as valor")
            ->fetch();
        foreach ($r->data() as $r) null;
        return (float) ($r->valor ?? 0);
    }

    private function somaComissaoTotal($usuario, $tipo, $extra = "")
    {
        $r = (new Comissao())
            ->find("tipo = $tipo AND usuario = $usuario" . $extra, null, "SUM(valor) as valor")
            ->fetch();
        foreach ($r->data() as $r) null;
        return (float) ($r->valor ?? 0);
    }

    private function dadosGrafico($usuario)
    {
        $meses  = [];
        $ganhos = [];
        $saques = [];

        for ($i = 11; $i >= 0; $i--) {
            $ts  = strtotime("-$i months");
            $ini = date("Y-m-01", $ts);
            $fim = date("Y-m-t",  $ts);

            $meses[]  = mb_strtoupper(strftime("%b/%y", $ts));
            $ganhos[] = round($this->somaComissao($usuario, 1, $ini, $fim), 2);
            $saques[] = round(abs($this->somaComissao($usuario, 2, $ini, $fim)), 2);
        }

        return compact("meses", "ganhos", "saques");
    }

    private function comparativoPeriodo($usuario, $data_ini, $data_fim)
    {
        $rows   = [];
        $cursor = strtotime(date("Y-m-01", strtotime($data_ini)));
        $limite = strtotime(date("Y-m-01", strtotime($data_fim)));

        while ($cursor <= $limite) {
            $ini_mes  = date("Y-m-01", $cursor);
            $fim_mes  = date("Y-m-t",  $cursor);
            $ini_real = $ini_mes < $data_ini ? $data_ini : $ini_mes;
            $fim_real = $fim_mes > $data_fim ? $data_fim : $fim_mes;

            $ganho = round($this->somaComissao($usuario, 1, $ini_real, $fim_real), 2);
            $saque = round(abs($this->somaComissao($usuario, 2, $ini_real, $fim_real)), 2);

            if ($ganho > 0 || $saque > 0) {
                $rows[] = [
                    "mes"   => strftime("%B/%Y", $cursor),
                    "ganho" => $ganho,
                    "saque" => $saque,
                    "delta" => round($ganho - $saque, 2),
                ];
            }

            $cursor = strtotime("+1 month", $cursor);
        }

        return array_reverse($rows);
    }

    private function buscarTransferencias($recipientId, $data_ini, $data_fim)
    {
        if (!$recipientId) return [];

        $pagarme = new PagarMe();

        $urls = [
            "https://api.pagar.me/core/v5/transfers?recipient_id=$recipientId&count=50",
            "https://api.pagar.me/core/v5/transfers?count=5",  // sem filtro, só pra ver se token tem acesso
        ];

        foreach ($urls as $url) {
            $resp = $pagarme->executa($url, "GET");
            echo "<!-- TRANSFER_DEBUG url=$url resp=" . substr(json_encode($resp), 0, 500) . " -->";
        }

        return [];
    }

    private function buscarSettlements($recipientId, $data_ini, $data_fim)
    {
        if (!$recipientId) return [];

        $url  = "https://api.pagar.me/core/v5/settlements"
              . "?recipient_id=$recipientId"
              . "&payment_date_start=" . urlencode($data_ini)
              . "&payment_date_end="   . urlencode($data_fim)
              . "&size=100";

        $resp = (new PagarMe())->executa($url, "GET");

        if (isset($resp->settlements) && is_array($resp->settlements)) {
            return $resp->settlements;
        }

        return [];
    }
}

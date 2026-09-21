<?php

namespace Source\App\Dashboard;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Videos\Video;

class AppDashboard
{
    private $view;

    const PERIODOS = [7, 14, 30];

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema1", "php");
        $this->view->addData(["router" => $router]);
        (new AppSessao())->verificaSeLogado();
    }

    public function index($data)
    {
        $userId = AppSessao::usuarioId();

        $dias = (int)($_GET["dias"] ?? 7);
        if (!in_array($dias, self::PERIODOS, true)) $dias = 7;

        $videos = (new Video())
            ->find("usuario = :u", "u={$userId}")
            ->order("id DESC")
            ->fetch(true) ?: [];
        $totalVideos = count($videos);

        // Eixo de datas do período atual (do mais antigo ao mais recente)
        $labels = [];
        $serie  = ["view" => [], "play" => [], "complete" => []];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $d = date("Y-m-d", strtotime("-{$i} days"));
            $labels[] = $d;
            foreach ($serie as $t => $_) $serie[$t][$d] = 0;
        }

        $anterior   = ["view" => 0, "play" => 0, "complete" => 0];
        $porVideo   = [];
        $videosNovos = 0;
        $videosAntes = 0;

        $pdo = \CoffeeCode\DataLayer\Connect::getInstance();
        if ($pdo && $totalVideos > 0) {
            $inicioAtual    = date("Y-m-d", strtotime("-" . ($dias - 1) . " days"));
            $inicioAnterior = date("Y-m-d", strtotime("-" . (($dias * 2) - 1) . " days"));

            // Eventos por dia e tipo, período atual + anterior
            $stmt = $pdo->prepare(
                "SELECT DATE(e.created_at) AS dia, e.tipo, COUNT(*) AS total
                 FROM winturb_eventos e
                 JOIN winturb_videos v ON v.id = e.video_id
                 WHERE v.usuario = :u
                   AND e.tipo IN ('view','play','complete')
                   AND e.created_at >= :ini
                 GROUP BY dia, e.tipo"
            );
            $stmt->execute([":u" => $userId, ":ini" => $inicioAnterior . " 00:00:00"]);
            foreach ($stmt->fetchAll(\PDO::FETCH_OBJ) as $row) {
                if ($row->dia >= $inicioAtual) {
                    $serie[$row->tipo][$row->dia] = (int)$row->total;
                } else {
                    $anterior[$row->tipo] += (int)$row->total;
                }
            }

            // Plays e conclusões por vídeo no período atual
            $stmt = $pdo->prepare(
                "SELECT e.video_id, SUM(e.tipo = 'play') AS plays, SUM(e.tipo = 'complete') AS completes
                 FROM winturb_eventos e
                 JOIN winturb_videos v ON v.id = e.video_id
                 WHERE v.usuario = :u
                   AND e.tipo IN ('play','complete')
                   AND e.created_at >= :ini
                 GROUP BY e.video_id"
            );
            $stmt->execute([":u" => $userId, ":ini" => $inicioAtual . " 00:00:00"]);
            foreach ($stmt->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $porVideo[(int)$row->video_id] = [
                    "plays"     => (int)$row->plays,
                    "completes" => (int)$row->completes,
                ];
            }
        }

        // Vídeos criados no período (para o sparkline e a variação de "Vídeos")
        $cortePeriodo = strtotime($labels[0] . " 00:00:00");
        $criadosPorDia = array_fill_keys($labels, 0);
        foreach ($videos as $vid) {
            $ts = strtotime($vid->created_at);
            if ($ts >= $cortePeriodo) {
                $videosNovos++;
                $d = date("Y-m-d", $ts);
                if (isset($criadosPorDia[$d])) $criadosPorDia[$d]++;
            }
        }
        $videosAntes = $totalVideos - $videosNovos;
        $acumulado = [];
        $corrente  = $videosAntes;
        foreach ($labels as $d) {
            $corrente += $criadosPorDia[$d];
            $acumulado[] = $corrente;
        }

        // Totais do período
        $atual = [
            "view"     => array_sum($serie["view"]),
            "play"     => array_sum($serie["play"]),
            "complete" => array_sum($serie["complete"]),
        ];
        $taxaAtual    = $atual["play"] > 0 ? round($atual["complete"] / $atual["play"] * 100, 1) : 0.0;
        $taxaAnterior = $anterior["play"] > 0 ? round($anterior["complete"] / $anterior["play"] * 100, 1) : null;

        // Ranking (top 5 por plays no período)
        $ranking = [];
        foreach ($videos as $vid) {
            $m = $porVideo[(int)$vid->id] ?? ["plays" => 0, "completes" => 0];
            $ranking[] = (object)[
                "video"     => $vid,
                "plays"     => $m["plays"],
                "completes" => $m["completes"],
                "taxa"      => $m["plays"] > 0 ? round($m["completes"] / $m["plays"] * 100) : 0,
            ];
        }
        usort($ranking, fn($a, $b) => $b->plays <=> $a->plays);
        $topVideos = array_slice(array_filter($ranking, fn($r) => $r->plays > 0), 0, 5);

        $recentes = [];
        foreach (array_slice($videos, 0, 5) as $vid) {
            $m = $porVideo[(int)$vid->id] ?? ["plays" => 0, "completes" => 0];
            $recentes[] = (object)[
                "video" => $vid,
                "plays" => $m["plays"],
                "taxa"  => $m["plays"] > 0 ? round($m["completes"] / $m["plays"] * 100) : 0,
            ];
        }

        echo $this->view->render("dashboard/index", [
            "title"        => "Dashboard — " . WINTURB_NAME,
            "usuario"      => AppSessao::usuarioNome(),
            "dias"         => $dias,
            "periodos"     => self::PERIODOS,
            "totalVideos"  => $totalVideos,
            "videosNovos"  => $videosNovos,
            "acumulado"    => $acumulado,
            "labels"       => $labels,
            "serie"        => [
                "view"     => array_values($serie["view"]),
                "play"     => array_values($serie["play"]),
                "complete" => array_values($serie["complete"]),
            ],
            "atual"        => $atual,
            "anterior"     => $anterior,
            "taxaAtual"    => $taxaAtual,
            "taxaAnterior" => $taxaAnterior,
            "topVideos"    => $topVideos,
            "recentes"     => $recentes,
        ]);
    }
}

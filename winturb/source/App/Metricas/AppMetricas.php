<?php

namespace Source\App\Metricas;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Videos\Video;
use Source\Models\Anuncios\Anuncio;
use CoffeeCode\DataLayer\Connect;

class AppMetricas
{
    private $view;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema1", "php");
        $this->view->addData(["router" => $router]);
        (new AppSessao())->verificaSeLogado();
    }

    public function index($data)
    {
        $userId = AppSessao::usuarioId();
        $video  = (new Video())->findById($data["id"] ?? 0);

        if (!$video || (int)$video->usuario !== $userId) {
            header("Location: " . url("videos"));
            exit;
        }

        $pdo     = Connect::getInstance();
        $videoId = (int)$video->id;

        // ── Totais gerais ──────────────────────────────────────────────────
        $stmt = $pdo->prepare("
            SELECT
                SUM(tipo = 'view')           AS views,
                SUM(tipo = 'play')           AS plays,
                SUM(tipo = 'complete')       AS completes,
                COUNT(DISTINCT session_key)  AS sessoes_unicas
            FROM winturb_eventos
            WHERE video_id = :v
        ");
        $stmt->execute([':v' => $videoId]);
        $totais = $stmt->fetch(\PDO::FETCH_OBJ);

        // ── Média de progresso assistido ───────────────────────────────────
        $stmt = $pdo->prepare("
            SELECT
                ROUND(AVG(percent_watched), 1) AS avg_pct,
                ROUND(AVG(total_watch_time) / 60, 1) AS avg_min,
                SUM(completed) AS concluidos
            FROM winturb_progresso
            WHERE video_id = :v
        ");
        $stmt->execute([':v' => $videoId]);
        $progStats = $stmt->fetch(\PDO::FETCH_OBJ);

        // ── Views por dia (últimos 30 dias) ───────────────────────────────
        $stmt = $pdo->prepare("
            SELECT DATE(created_at) AS dia, COUNT(*) AS total
            FROM winturb_eventos
            WHERE video_id = :v AND tipo = 'view'
              AND created_at >= NOW() - INTERVAL 30 DAY
            GROUP BY dia
            ORDER BY dia ASC
        ");
        $stmt->execute([':v' => $videoId]);
        $viewsPorDia = $stmt->fetchAll(\PDO::FETCH_OBJ);

        // ── Performance dos anúncios ───────────────────────────────────────
        $anuncios = (new Anuncio())->find("video_id = :v", "v={$videoId}")->order("exibir_em ASC")->fetch(true);
        $adsStats = [];

        if ($anuncios) {
            foreach ($anuncios as $ad) {
                $stmt = $pdo->prepare("
                    SELECT
                        SUM(tipo = 'ad_impression') AS impressoes,
                        SUM(tipo = 'ad_click')      AS cliques
                    FROM winturb_eventos
                    WHERE video_id = :v AND ad_id = :a
                ");
                $stmt->execute([':v' => $videoId, ':a' => (int)$ad->id]);
                $row = $stmt->fetch(\PDO::FETCH_OBJ);
                $impressoes = (int)($row->impressoes ?? 0);
                $cliques    = (int)($row->cliques    ?? 0);
                $ctr        = $impressoes > 0 ? round($cliques / $impressoes * 100, 1) : 0;

                $adsStats[] = [
                    "ad"         => $ad,
                    "impressoes" => $impressoes,
                    "cliques"    => $cliques,
                    "ctr"        => $ctr,
                    "conversao"  => $totais->views > 0 ? round($cliques / $totais->views * 100, 1) : 0,
                ];
            }
        }

        echo $this->view->render("metricas/index", [
            "title"       => "Métricas — " . htmlspecialchars($video->titulo, ENT_QUOTES),
            "video"       => $video,
            "totais"      => $totais,
            "progStats"   => $progStats,
            "viewsPorDia" => $viewsPorDia,
            "adsStats"    => $adsStats,
            "usuario"     => AppSessao::usuarioNome(),
            "embedUrl"    => url("player/{$video->embed_token}"),
        ]);
    }
}

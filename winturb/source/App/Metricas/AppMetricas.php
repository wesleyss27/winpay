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

        // ── Totais únicos (por session_key) ──────────────────────────────────
        $stmt = $pdo->prepare("
            SELECT
                COUNT(DISTINCT CASE WHEN tipo = 'view'     THEN session_key END) AS views_unicas,
                COUNT(DISTINCT CASE WHEN tipo = 'play'     THEN session_key END) AS plays_unicos,
                SUM(tipo = 'ad_click')                                           AS cliques_botao
            FROM winturb_eventos
            WHERE video_id = :v
        ");
        $stmt->execute([':v' => $videoId]);
        $totaisUnicos = $stmt->fetch(\PDO::FETCH_OBJ);

        // ── Duração do vídeo ──────────────────────────────────────────────────
        $stmt = $pdo->prepare("
            SELECT GREATEST(COALESCE(MAX(duration),0), COALESCE((SELECT MAX(duracao) FROM winturb_retencao WHERE video_id = :v2),0)) AS dur
            FROM winturb_progresso WHERE video_id = :v
        ");
        $stmt->execute([':v' => $videoId, ':v2' => $videoId]);
        $durRow   = $stmt->fetch(\PDO::FETCH_OBJ);
        $videoDur = (int)($durRow->dur ?? 0);

        // ── Curva de retenção ─────────────────────────────────────────────────
        $stmt = $pdo->prepare("SELECT max_segundo FROM winturb_retencao WHERE video_id = :v");
        $stmt->execute([':v' => $videoId]);
        $maxSeconds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $playsUnicos = (int)($totaisUnicos->plays_unicos ?? 0);
        $retencao = [];
        if ($playsUnicos > 0 && $videoDur > 0) {
            $interval = max(10, (int)($videoDur / 60));
            for ($s = 0; $s <= $videoDur; $s += $interval) {
                $cnt = count(array_filter($maxSeconds, fn($ms) => (int)$ms >= $s));
                $retencao[] = ['s' => $s, 'pct' => round($cnt / $playsUnicos * 100, 1)];
            }
        }

        // ── Retenção ao Pitch ─────────────────────────────────────────────────
        $pitchTime     = (int)($video->pitch_time ?? 0);
        $retencaoPitch = 0;
        $audienciaPitch = 0;
        if ($pitchTime > 0 && $playsUnicos > 0 && count($maxSeconds) > 0) {
            $audienciaPitch = count(array_filter($maxSeconds, fn($ms) => (int)$ms >= $pitchTime));
            $retencaoPitch  = round($audienciaPitch / $playsUnicos * 100, 1);
        }

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
            "title"          => "Métricas — " . htmlspecialchars($video->titulo, ENT_QUOTES),
            "video"          => $video,
            "totais"         => $totais,
            "totaisUnicos"   => $totaisUnicos,
            "progStats"      => $progStats,
            "viewsPorDia"    => $viewsPorDia,
            "adsStats"       => $adsStats,
            "retencao"       => $retencao,
            "videoDur"       => $videoDur,
            "pitchTime"      => $pitchTime,
            "retencaoPitch"  => $retencaoPitch,
            "audienciaPitch" => $audienciaPitch,
            "usuario"        => AppSessao::usuarioNome(),
            "embedUrl"       => url("player/{$video->embed_token}"),
        ]);
    }

    public function aoVivo($data)
    {
        header("Content-Type: application/json");
        header("Cache-Control: no-store");

        $userId  = AppSessao::usuarioId();
        $video   = (new \Source\Models\Videos\Video())->findById($data["id"] ?? 0);
        if (!$video || (int)$video->usuario !== $userId) {
            echo json_encode(["ok" => false]);
            return;
        }

        $pdo     = \CoffeeCode\DataLayer\Connect::getInstance();
        $videoId = (int)$video->id;

        // Sessões ativas (heartbeat nos últimos 45s)
        $stmt = $pdo->prepare("
            SELECT ip, device, pais, cidade
            FROM winturb_retencao
            WHERE video_id = :v
              AND atualizado_em >= NOW() - INTERVAL 45 SECOND
        ");
        $stmt->execute([':v' => $videoId]);
        $sessoes = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $total   = count($sessoes);
        $devices = ['Mobile' => 0, 'Desktop' => 0, 'Tablet' => 0];
        $cidades = [];

        foreach ($sessoes as $s) {
            $dev = $s->device ?: 'Desktop';
            $devices[$dev] = ($devices[$dev] ?? 0) + 1;
            if ($s->cidade) {
                $key = trim($s->cidade);
                $cidades[$key] = ($cidades[$key] ?? 0) + 1;
            }
        }

        arsort($cidades);
        $topCidades = array_slice($cidades, 0, 5, true);

        echo json_encode([
            "ok"      => true,
            "total"   => $total,
            "devices" => $devices,
            "cidades" => $topCidades,
        ]);
    }
}

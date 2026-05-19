<?php

namespace Source\App\Dashboard;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Videos\Video;

class AppDashboard
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

        $totalVideos = (new Video())
            ->find("usuario = :u", "u={$userId}")
            ->count();

        $videos = (new Video())
            ->find("usuario = :u", "u={$userId}")
            ->order("id DESC")
            ->limit(5)
            ->fetch(true);

        // Métricas globais dos vídeos do usuário
        $stats = ["plays" => 0, "views" => 0, "completes" => 0];
        $playsMap = [];
        $topVideos = [];

        $pdo = \CoffeeCode\DataLayer\Connect::getInstance();
        if ($pdo && $totalVideos > 0) {
            $stmt = $pdo->prepare(
                "SELECT e.tipo, COUNT(*) AS total
                 FROM winturb_eventos e
                 JOIN winturb_videos v ON v.id = e.video_id
                 WHERE v.usuario = :u AND e.tipo IN ('play','view','complete')
                 GROUP BY e.tipo"
            );
            $stmt->execute([':u' => $userId]);
            foreach ($stmt->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $stats[$row->tipo . 's'] = (int)$row->total;
            }

            // Top 3 vídeos por plays
            $stmt2 = $pdo->prepare(
                "SELECT v.id, v.titulo, v.embed_token, COUNT(e.id) AS plays
                 FROM winturb_videos v
                 LEFT JOIN winturb_eventos e ON e.video_id = v.id AND e.tipo = 'play'
                 WHERE v.usuario = :u
                 GROUP BY v.id, v.titulo, v.embed_token
                 ORDER BY plays DESC
                 LIMIT 3"
            );
            $stmt2->execute([':u' => $userId]);
            $topVideos = $stmt2->fetchAll(\PDO::FETCH_OBJ);
        }

        echo $this->view->render("dashboard/index", [
            "title"       => "Dashboard — " . WINTURB_NAME,
            "totalVideos" => $totalVideos,
            "videos"      => $videos ?: [],
            "stats"       => $stats,
            "topVideos"   => $topVideos,
            "usuario"     => AppSessao::usuarioNome(),
        ]);
    }
}

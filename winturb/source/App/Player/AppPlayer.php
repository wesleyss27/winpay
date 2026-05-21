<?php

namespace Source\App\Player;

use League\Plates\Engine;
use Source\Models\Videos\Video;
use Source\Models\Anuncios\Anuncio;
use CoffeeCode\DataLayer\Connect;

class AppPlayer
{
    private $view;
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
        $this->view   = Engine::create(__DIR__ . "/../../../themes/tema1", "php");
        $this->view->addData(["router" => $router]);
    }

    public function embed($data)
    {
        header_remove("X-Frame-Options");
        header("Content-Security-Policy: frame-ancestors *");

        $token = $data["token"] ?? "";
        $video = (new Video())->findByToken($token);

        if (!$video) {
            http_response_code(404);
            echo "<!doctype html><html><body style='background:#000;color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;'>Vídeo não encontrado.</body></html>";
            exit;
        }

        $anuncios     = (new Anuncio())->porVideo((int)$video->id);
        $streamUrl    = url("player/stream/{$video->embed_token}");
        $progressoUrl = url("videos/progresso/{$video->embed_token}");
        $salvarUrl    = url("videos/progresso");
        $eventoUrl    = url("player/evento");

        $hlsFile = __DIR__ . "/../../../uploads/hls/{$video->embed_token}/master.m3u8";
        $hlsUrl  = ($video->hls_status === 'ready' && is_file($hlsFile))
            ? url("uploads/hls/{$video->embed_token}/master.m3u8")
            : null;

        echo $this->view->render("player/embed", [
            "video"        => $video,
            "anuncios"     => $anuncios,
            "streamUrl"    => $streamUrl,
            "progressoUrl" => $progressoUrl,
            "salvarUrl"    => $salvarUrl,
            "eventoUrl"    => $eventoUrl,
            "hlsUrl"       => $hlsUrl,
        ]);
    }

    /** POST /player/evento — registra um evento de analytics (fire-and-forget) */
    public function evento($data)
    {
        header("Content-Type: application/json");

        $tipos  = ['view', 'play', 'ad_impression', 'ad_click', 'complete'];
        $tipo   = $data["tipo"]     ?? "";
        $token  = $data["token"]    ?? "";
        $adId   = !empty($data["ad_id"]) ? (int)$data["ad_id"] : null;

        if (!in_array($tipo, $tipos) || !$token) {
            echo json_encode(["ok" => false]);
            return;
        }

        $video = (new Video())->findByToken($token);
        if (!$video) {
            echo json_encode(["ok" => false]);
            return;
        }

        $sessionKey = $this->sessionKey();
        $ip = $_SERVER["HTTP_CF_CONNECTING_IP"]
            ?? $_SERVER["HTTP_X_FORWARDED_FOR"]
            ?? $_SERVER["REMOTE_ADDR"]
            ?? null;

        try {
            $pdo  = Connect::getInstance();

            // Dedup: view do mesmo session_key nas últimas 24h não é contada duas vezes
            if ($tipo === 'view') {
                $check = $pdo->prepare("
                    SELECT id FROM winturb_eventos
                    WHERE video_id = :v AND session_key = :s AND tipo = 'view'
                      AND criado_em > NOW() - INTERVAL 1 DAY
                    LIMIT 1
                ");
                $check->execute([':v' => (int)$video->id, ':s' => $sessionKey]);
                if ($check->fetch()) {
                    echo json_encode(["ok" => true]);
                    return;
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO winturb_eventos (video_id, ad_id, tipo, session_key, ip)
                VALUES (:v, :a, :t, :s, :ip)
            ");
            $stmt->execute([
                ':v'  => (int)$video->id,
                ':a'  => $adId,
                ':t'  => $tipo,
                ':s'  => $sessionKey,
                ':ip' => $ip ? substr($ip, 0, 45) : null,
            ]);
            echo json_encode(["ok" => true]);
        } catch (\PDOException $e) {
            echo json_encode(["ok" => false]);
        }
    }

    /** Byte-range stream — público, sem autenticação */
    public function stream($data)
    {
        $video = (new Video())->findByToken($data["token"] ?? "");

        if (!$video) {
            http_response_code(404);
            exit("Vídeo não encontrado.");
        }

        $arquivo = __DIR__ . "/../../../uploads/videos/" . $video->arquivo;

        if (!is_file($arquivo)) {
            http_response_code(404);
            exit("Arquivo não encontrado.");
        }

        $ext     = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
        $mimeMap = [
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'ogg'  => 'video/ogg',
            'mov'  => 'video/quicktime',
            'mkv'  => 'video/x-matroska',
        ];
        $mime    = $mimeMap[$ext] ?? 'application/octet-stream';
        $tamanho = filesize($arquivo);
        $inicio  = 0;
        $fim     = $tamanho - 1;

        header("Accept-Ranges: bytes");
        header("Content-Type: $mime");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Access-Control-Allow-Origin: *");

        if (isset($_SERVER['HTTP_RANGE'])) {
            preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $m);
            $inicio = (int)$m[1];
            $fim    = !empty($m[2]) ? (int)$m[2] : $tamanho - 1;
            $fim    = min($fim, $tamanho - 1);
            http_response_code(206);
            header("Content-Range: bytes $inicio-$fim/$tamanho");
        }

        $length = $fim - $inicio + 1;
        header("Content-Length: $length");

        $fp = fopen($arquivo, 'rb');
        fseek($fp, $inicio);
        $restante = $length;
        while ($restante > 0 && !feof($fp)) {
            $chunk = min(65536, $restante);
            echo fread($fp, $chunk);
            $restante -= $chunk;
            flush();
        }
        fclose($fp);
        exit;
    }

    /** GET /player/hls-status/{token} — status da transcodificação HLS */
    public function hlsStatus($data)
    {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");

        $video = (new Video())->findByToken($data["token"] ?? "");
        if (!$video) {
            echo json_encode(["status" => "not_found"]);
            return;
        }

        $hlsFile = __DIR__ . "/../../../uploads/hls/{$video->embed_token}/master.m3u8";
        $ready   = $video->hls_status === 'ready' && is_file($hlsFile);

        echo json_encode([
            "status"  => $video->hls_status ?: 'none',
            "hls_url" => $ready ? url("uploads/hls/{$video->embed_token}/master.m3u8") : null,
        ]);
    }

    private function sessionKey(): string
    {
        if (session_status() === PHP_SESSION_NONE) @session_start();
        if (empty($_SESSION["wt_guest_key"])) {
            $_SESSION["wt_guest_key"] = bin2hex(random_bytes(16));
        }
        return $_SESSION["wt_guest_key"];
    }
}

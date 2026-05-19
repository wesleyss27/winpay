<?php

namespace Source\App\Videos;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Videos\Video;
use Source\Models\Videos\VideoProgresso;
use Source\Models\Videos\Pasta;
use Source\Models\Anuncios\Anuncio;

class AppVideos
{
    private $view;
    const MAX_SIZE_BYTES = 524288000; // 500 MB
    const ALLOWED_EXT   = ['mp4', 'webm', 'ogg', 'mov', 'mkv'];

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema1", "php");
        $this->view->addData(["router" => $router]);
        (new AppSessao())->verificaSeLogado();
    }

    public function index($data)
    {
        $userId = AppSessao::usuarioId();
        $videos = (new Video())
            ->find("usuario = :u", "u={$userId}")
            ->order("id DESC")
            ->fetch(true);

        $playsMap = [];
        $pdo      = \CoffeeCode\DataLayer\Connect::getInstance();

        if ($videos && $pdo) {
            $ids          = array_map(fn($v) => (int)$v->id, $videos);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $stmt = $pdo->prepare(
                "SELECT video_id, COUNT(*) AS total
                 FROM winturb_eventos
                 WHERE tipo = 'play' AND video_id IN ($placeholders)
                 GROUP BY video_id"
            );
            $stmt->execute($ids);
            foreach ($stmt->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $playsMap[(int)$row->video_id] = (int)$row->total;
            }
        }

        // Pastas do usuário
        $pastas   = (new Pasta())->byUsuario($userId);
        $pastaMap = [];
        foreach ($pastas as $p) {
            $pastaMap[(int)$p->id] = $p->nome;
        }

        echo $this->view->render("videos/index", [
            "title"    => "Meus Vídeos — " . WINTURB_NAME,
            "videos"   => $videos ?: [],
            "playsMap" => $playsMap,
            "pastas"   => $pastas,
            "pastaMap" => $pastaMap,
            "usuario"  => AppSessao::usuarioNome(),
        ]);
    }

    public function upload($data)
    {
        header("Content-Type: application/json");

        if (empty($_FILES["arquivo"]["name"])) {
            echo json_encode(["ok" => false, "msg" => "Nenhum arquivo enviado."]);
            return;
        }

        $file = $_FILES["arquivo"];
        $ext  = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, self::ALLOWED_EXT)) {
            echo json_encode(["ok" => false, "msg" => "Formato inválido. Permitido: " . implode(", ", self::ALLOWED_EXT)]);
            return;
        }

        if ($file["size"] > self::MAX_SIZE_BYTES) {
            echo json_encode(["ok" => false, "msg" => "Arquivo excede 500 MB."]);
            return;
        }

        if ($file["error"] !== UPLOAD_ERR_OK) {
            echo json_encode(["ok" => false, "msg" => "Erro no upload (código {$file['error']})."]);
            return;
        }

        $userId   = AppSessao::usuarioId();
        $baseDir  = __DIR__ . "/../../../uploads/videos/{$userId}/";
        if (!is_dir($baseDir)) mkdir($baseDir, 0775, true);

        $novoNome = md5(uniqid("", true)) . "." . $ext;
        $destino  = $baseDir . $novoNome;

        if (!move_uploaded_file($file["tmp_name"], $destino)) {
            echo json_encode(["ok" => false, "msg" => "Falha ao mover o arquivo."]);
            return;
        }

        $titulo = trim($data["titulo"] ?? pathinfo($file["name"], PATHINFO_FILENAME));

        $video              = new Video();
        $video->usuario     = $userId;
        $video->titulo      = $titulo ?: "Vídeo sem título";
        $video->arquivo     = "{$userId}/{$novoNome}";
        $video->tamanho     = $file["size"];
        $video->embed_token = Video::gerarToken();
        $video->status      = 1;

        if ($video->save()) {
            echo json_encode(["ok" => true, "id" => $video->id, "titulo" => $video->titulo]);
        } else {
            @unlink($destino);
            $msg = $video->fail() ? $video->fail()->getMessage() : "Erro ao salvar.";
            echo json_encode(["ok" => false, "msg" => $msg]);
        }
    }

    public function editar($data)
    {
        $userId = AppSessao::usuarioId();
        $video  = (new Video())->findById($data["id"] ?? 0);

        if (!$video || (int)$video->usuario !== $userId) {
            header("Location: " . url("videos"));
            exit;
        }

        $anuncios = (new Anuncio())->find("video_id = :v", "v={$video->id}")->order("exibir_em ASC")->fetch(true);

        echo $this->view->render("videos/editar", [
            "title"    => "Editar Vídeo — " . WINTURB_NAME,
            "video"    => $video,
            "anuncios" => $anuncios ?: [],
            "usuario"  => AppSessao::usuarioNome(),
            "embedUrl" => url("player/{$video->embed_token}"),
        ]);
    }

    public function salvar($data)
    {
        header("Content-Type: application/json");

        $userId = AppSessao::usuarioId();
        $video  = (new Video())->findById($data["id"] ?? 0);

        if (!$video || (int)$video->usuario !== $userId) {
            echo json_encode(["ok" => false, "msg" => "Não encontrado."]);
            return;
        }

        $video->titulo      = trim($data["titulo"] ?? $video->titulo);
        $video->descricao   = trim($data["descricao"] ?? "");
        $video->largura     = (int)($data["largura"] ?? 854);
        $video->altura      = (int)($data["altura"] ?? 480);
        $video->anti_skip    = isset($data["anti_skip"])    ? 1 : 0;
        $video->allow_speed  = isset($data["allow_speed"])  ? 1 : 0;
        $video->hide_seekbar = isset($data["hide_seekbar"]) ? 1 : 0;
        $video->autoplay     = isset($data["autoplay"])     ? 1 : 0;
        $video->force_play   = isset($data["force_play"])   ? 1 : 0;
        $video->cor_acento   = preg_match('/^#[0-9a-fA-F]{6}$/', $data["cor_acento"]  ?? "") ? $data["cor_acento"]  : "#7c6af7";
        $video->cor_fundo    = preg_match('/^#[0-9a-fA-F]{6}$/', $data["cor_fundo"]   ?? "") ? $data["cor_fundo"]   : "#000000";

        // Thumbnail (capa)
        if (!empty($_FILES["capa"]["name"]) && $_FILES["capa"]["error"] === UPLOAD_ERR_OK) {
            $capaExt  = strtolower(pathinfo($_FILES["capa"]["name"], PATHINFO_EXTENSION));
            $capaDir  = __DIR__ . "/../../../uploads/capas/";
            if (!is_dir($capaDir)) mkdir($capaDir, 0775, true);
            $capaNome = md5(uniqid("", true)) . "." . $capaExt;
            if (move_uploaded_file($_FILES["capa"]["tmp_name"], $capaDir . $capaNome)) {
                if ($video->capa && is_file($capaDir . $video->capa)) @unlink($capaDir . $video->capa);
                $video->capa = $capaNome;
            }
        }

        echo json_encode(["ok" => $video->save()]);
    }

    public function excluir($data)
    {
        header("Content-Type: application/json");

        $userId = AppSessao::usuarioId();
        $video  = (new Video())->findById($data["id"] ?? 0);

        if (!$video || (int)$video->usuario !== $userId) {
            echo json_encode(["ok" => false]);
            return;
        }

        $arquivo = __DIR__ . "/../../../uploads/videos/" . $video->arquivo;
        if (is_file($arquivo)) @unlink($arquivo);

        echo json_encode(["ok" => $video->destroy()]);
    }

    /** Stream byte-range — acesso por embed_token (sem auth) */
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

    /** AJAX: salva progresso */
    public function salvarProgresso($data)
    {
        header("Content-Type: application/json");

        $token       = $data["token"]        ?? "";
        $currentTime = (float)($data["current_time"] ?? 0);
        $duration    = (float)($data["duration"]     ?? 0);
        $watchSecs   = (int)($data["watch_seconds"]  ?? 0);

        $video = (new Video())->findByToken($token);
        if (!$video) { echo json_encode(["ok" => false]); return; }

        $userId     = isset($_SESSION["winturb_id"]) ? (int)$_SESSION["winturb_id"] : null;
        $sessionKey = $userId ? null : $this->sessionKey();

        $prog = new VideoProgresso();
        $ok   = $prog->upsert((int)$video->id, $userId, $sessionKey, $currentTime, $duration, $watchSecs);

        $percent = $duration > 0 ? round(($currentTime / $duration) * 100, 1) : 0;
        echo json_encode(["ok" => $ok, "percent" => $percent]);
    }

    /** AJAX: retorna progresso */
    public function getProgresso($data)
    {
        header("Content-Type: application/json");

        $video = (new Video())->findByToken($data["token"] ?? "");
        if (!$video) { echo json_encode(["current_time" => 0, "percent" => 0]); return; }

        $userId     = isset($_SESSION["winturb_id"]) ? (int)$_SESSION["winturb_id"] : null;
        $sessionKey = $userId ? null : $this->sessionKey();

        $p = (new VideoProgresso())->findByKey((int)$video->id, $userId, $sessionKey);

        echo json_encode([
            "current_time" => $p ? (float)$p->current_time    : 0,
            "percent"      => $p ? (float)$p->percent_watched  : 0,
            "completed"    => $p ? (bool)$p->completed          : false,
        ]);
    }

    private function sessionKey(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION["wt_guest_key"])) {
            $_SESSION["wt_guest_key"] = bin2hex(random_bytes(16));
        }
        return $_SESSION["wt_guest_key"];
    }
}

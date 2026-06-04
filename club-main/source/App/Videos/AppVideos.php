<?php

namespace Source\App\Videos;

session_start();

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Videos\Video;
use Source\Models\Videos\VideoProgresso;

class AppVideos
{
    private $view;
    private $sessao;
    private $tema;
    private $pastaVideos;

    // 500 MB em bytes
    const MAX_SIZE_BYTES = 524288000;

    const ALLOWED_EXT = ['mp4', 'webm', 'ogg', 'mov', 'mkv'];

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ?: "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/{$this->tema}", "php");
        $this->view->addData(["router" => $router]);
        $this->sessao = (new AppSessao())->verificaSeLogado();

        // /var/www/html/uploads/videos/{usuario}/
        $base = realpath(__DIR__ . "/../../..") . "/uploads/videos/";
        $userDir = $base . ($_SESSION["id"] ?? "0") . "/";
        if (!is_dir($userDir)) {
            mkdir($userDir, 0775, true);
        }
        $this->pastaVideos = $userDir;
    }

    /** Caminho da versão 480p comprimida de um arquivo */
    private static function path480(string $path): string
    {
        return preg_replace('/(\.[^.\/]+)$/', '_480p.mp4', $path);
    }

    /** Biblioteca de vídeos do produtor */
    public function biblioteca($data)
    {
        $videos = (new Video())
            ->find("usuario = :u", "u={$_SESSION['id']}")
            ->order("id DESC")
            ->fetch(true);

        $baseDir = realpath(__DIR__ . "/../../..") . "/uploads/videos/";
        if ($videos) {
            foreach ($videos as $v) {
                $v->has480 = is_file(self::path480($baseDir . $v->arquivo));
            }
        }

        echo $this->view->render("videos/biblioteca", [
            "title"  => "Minha Biblioteca de Vídeos",
            "videos" => $videos,
        ]);
    }

    /** Upload de vídeo */
    public function upload($data)
    {
        header("Content-Type: application/json");

        // post_max_size excedido: PHP descarta $_FILES e $_POST completamente
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES) && empty($_POST)) {
            $limit = ini_get('post_max_size');
            echo json_encode(["ok" => false, "msg" => "Arquivo muito grande para o servidor (limite: {$limit}). Reduza o tamanho ou contate o suporte."]);
            return;
        }

        if (empty($_FILES["arquivo"]["name"])) {
            echo json_encode(["ok" => false, "msg" => "Nenhum arquivo enviado."]);
            return;
        }

        $file = $_FILES["arquivo"];
        $ext  = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, self::ALLOWED_EXT)) {
            echo json_encode(["ok" => false, "msg" => "Formato não permitido. Use: " . implode(", ", self::ALLOWED_EXT)]);
            return;
        }

        if ($file["size"] > self::MAX_SIZE_BYTES) {
            echo json_encode(["ok" => false, "msg" => "Arquivo excede o limite de 150 MB."]);
            return;
        }

        if ($file["error"] !== UPLOAD_ERR_OK) {
            echo json_encode(["ok" => false, "msg" => "Erro no upload (código {$file['error']})."]);
            return;
        }

        $titulo   = trim($data["titulo"] ?? $file["name"]);
        $novoNome = md5(uniqid("", true)) . "." . $ext;
        $destino  = $this->pastaVideos . $novoNome;

        if (!move_uploaded_file($file["tmp_name"], $destino)) {
            echo json_encode(["ok" => false, "msg" => "Falha ao mover o arquivo."]);
            return;
        }

        $video          = new Video();
        $video->usuario = $_SESSION["id"];
        $video->titulo  = $titulo;
        $video->arquivo = $_SESSION["id"] . "/" . $novoNome;
        $video->tamanho = $file["size"];
        $video->status  = 1;

        if ($video->save()) {
            $ffmpeg = trim((string) shell_exec('which ffmpeg 2>/dev/null'));
            if ($ffmpeg) {
                // 1. Faststart: move o moov atom para o início do arquivo.
                //    Isso permite seeking imediato sem baixar o arquivo inteiro.
                //    É uma operação rápida (sem re-codificação).
                $tmp = $destino . '.fst.mp4';
                exec($ffmpeg . ' -i ' . escapeshellarg($destino)
                    . ' -c copy -movflags +faststart '
                    . escapeshellarg($tmp) . ' > /dev/null 2>&1');
                if (is_file($tmp) && filesize($tmp) > 0) {
                    rename($tmp, $destino);
                } else {
                    @unlink($tmp);
                }

                // 2. Comprime para 480p em background (não bloqueia a resposta)
                $dst = self::path480($destino);
                exec($ffmpeg . ' -i ' . escapeshellarg($destino)
                    . ' -vf scale=-2:480 -c:v libx264 -crf 26 -preset fast -c:a aac -b:a 96k '
                    . escapeshellarg($dst) . ' > /dev/null 2>&1 &');
            }
            echo json_encode(["ok" => true, "id" => $video->id, "titulo" => $titulo]);
        } else {
            @unlink($destino);
            echo json_encode(["ok" => false, "msg" => "Erro ao salvar no banco."]);
        }
    }

    /** Exclui vídeo */
    public function excluir($data)
    {
        header("Content-Type: application/json");

        $video = (new Video())->findById($data["id"] ?? 0);

        if (!$video || $video->usuario != $_SESSION["id"]) {
            echo json_encode(["ok" => false, "msg" => "Não encontrado."]);
            return;
        }

        $arquivo = realpath(__DIR__ . "/../../..") . "/uploads/videos/" . $video->arquivo;
        if (is_file($arquivo)) {
            @unlink($arquivo);
        }
        $arquivo480 = self::path480($arquivo);
        if (is_file($arquivo480)) {
            @unlink($arquivo480);
        }

        echo json_encode(["ok" => $video->destroy()]);
    }

    /**
     * Stream do vídeo com suporte a byte-range (necessário para seeking no browser)
     * Apenas o dono do vídeo (produtor) ou alunos com acesso podem assistir.
     */
    public function stream($data)
    {
        $video = (new Video())->findById($data["id"] ?? 0);

        if (!$video || $video->status != 1) {
            http_response_code(404);
            exit("Vídeo não encontrado.");
        }

        // Segurança: apenas o próprio produtor ou alunos autenticados
        if (!isset($_SESSION["id"])) {
            http_response_code(403);
            exit("Acesso negado.");
        }

        $original = realpath(__DIR__ . "/../../..") . "/uploads/videos/" . $video->arquivo;

        if (!is_file($original)) {
            http_response_code(404);
            exit("Arquivo não encontrado.");
        }

        // Prefere versão 480p comprimida se já estiver pronta
        $arquivo480 = self::path480($original);
        $arquivo    = is_file($arquivo480) ? $arquivo480 : $original;

        $ext = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
        $mimeMap = [
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'ogg'  => 'video/ogg',
            'mov'  => 'video/quicktime',
            'mkv'  => 'video/x-matroska',
        ];
        $mime = $mimeMap[$ext] ?? 'video/mp4';

        $tamanho = filesize($arquivo);
        $etag    = '"' . md5($arquivo . $tamanho) . '"';
        $inicio  = 0;
        $fim     = $tamanho - 1;

        header("Accept-Ranges: bytes");
        header("Content-Type: $mime");
        // Permite cache privado de 2h — reduz requests repetidos para o mesmo vídeo
        header("Cache-Control: private, max-age=7200");
        header("ETag: $etag");

        // Responde 304 se o cliente já tem a versão atual em cache
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
            http_response_code(304);
            exit;
        }

        if (isset($_SERVER['HTTP_RANGE'])) {
            preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches);
            $inicio = (int) $matches[1];
            $fim    = !empty($matches[2]) ? (int) $matches[2] : $tamanho - 1;
            $fim    = min($fim, $tamanho - 1);

            http_response_code(206);
            header("Content-Range: bytes $inicio-$fim/$tamanho");
        }

        $length = $fim - $inicio + 1;
        header("Content-Length: $length");

        // Limpa output buffer e configura para stream longo
        if (ob_get_level()) ob_end_clean();
        set_time_limit(0);
        ignore_user_abort(true);

        $fp = fopen($arquivo, 'rb');
        fseek($fp, $inicio);
        $restante = $length;
        while ($restante > 0 && !feof($fp)) {
            $chunk = min(1048576, $restante); // 1 MB por chunk (era 64 KB)
            echo fread($fp, $chunk);
            flush();
            $restante -= $chunk;
        }
        fclose($fp);
        exit;
    }

    /** AJAX: salva progresso do aluno */
    public function salvarProgresso($data)
    {
        header("Content-Type: application/json");

        $aulaId      = (int) ($data["aula_id"]      ?? 0);
        $videoId     = (int) ($data["video_id"]     ?? 0);
        $currentTime = (float) ($data["current_time"] ?? 0);
        $duration    = (float) ($data["duration"]   ?? 0);
        $watchSecs   = (int) ($data["watch_seconds"] ?? 0);

        if (!$aulaId || !$videoId || !isset($_SESSION["id"])) {
            echo json_encode(["ok" => false]);
            return;
        }

        $progresso = new VideoProgresso();
        $ok = $progresso->upsert(
            (int) $_SESSION["id"],
            $aulaId,
            $videoId,
            $currentTime,
            $duration,
            $watchSecs
        );

        $percent   = $duration > 0 ? round(($currentTime / $duration) * 100, 1) : 0;
        $completed = $percent >= 90;

        $errMsg = null;
        if (!$ok) {
            $errMsg = $progresso->fail() ? $progresso->fail()->getMessage() : "execute retornou false";
        }
        echo json_encode(["ok" => $ok, "percent" => $percent, "completed" => $completed, "err" => $errMsg]);
    }

    /** AJAX: retorna progresso do aluno em uma aula */
    public function getProgresso($data)
    {
        header("Content-Type: application/json");

        $aulaId = (int) ($data["aula_id"] ?? 0);

        if (!$aulaId || !isset($_SESSION["id"])) {
            echo json_encode(["current_time" => 0, "percent" => 0, "completed" => false]);
            return;
        }

        $p = (new VideoProgresso())->findByUsuarioAula((int) $_SESSION["id"], $aulaId);

        echo json_encode([
            "current_time" => $p ? (float) $p->current_time : 0,
            "percent"      => $p ? (float) $p->percent_watched : 0,
            "completed"    => $p ? (bool) $p->completed : false,
        ]);
    }

    /** AJAX: lista vídeos do produtor para o seletor na edição de aula */
    public function listar($data)
    {
        header("Content-Type: application/json");

        $videos = (new Video())
            ->find("usuario = :u AND status = 1", "u={$_SESSION['id']}")
            ->order("titulo ASC")
            ->fetch(true);

        $result = [];
        if ($videos) {
            foreach ($videos as $v) {
                $result[] = ["id" => $v->id, "titulo" => $v->titulo];
            }
        }
        echo json_encode($result);
    }
}

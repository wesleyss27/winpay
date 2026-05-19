<?php

namespace Source\App\Pastas;

session_start();

use Source\App\Sessao\AppSessao;
use Source\Models\Videos\Pasta;
use Source\Models\Videos\Video;

class AppPastas
{
    public function __construct($router) {}

    /** POST /pastas/criar — cria pasta, retorna JSON */
    public function criar($data)
    {
        header("Content-Type: application/json");
        (new AppSessao())->verificaSeLogado();

        $nome = trim($data["nome"] ?? "");
        if (!$nome) {
            echo json_encode(["ok" => false, "msg" => "Nome obrigatório."]);
            return;
        }

        $userId = AppSessao::usuarioId();
        $pasta  = new Pasta();
        $pasta->usuario = $userId;
        $pasta->nome    = substr($nome, 0, 100);

        if ($pasta->save()) {
            echo json_encode(["ok" => true, "id" => $pasta->id, "nome" => $pasta->nome]);
        } else {
            $msg = $pasta->fail() ? $pasta->fail()->getMessage() : "Erro ao salvar.";
            echo json_encode(["ok" => false, "msg" => $msg]);
        }
    }

    /** POST /pastas/excluir — remove pasta e desvincula vídeos */
    public function excluir($data)
    {
        header("Content-Type: application/json");
        (new AppSessao())->verificaSeLogado();

        $userId = AppSessao::usuarioId();
        $pasta  = (new Pasta())->findById($data["id"] ?? 0);

        if (!$pasta || (int)$pasta->usuario !== $userId) {
            echo json_encode(["ok" => false]);
            return;
        }

        // Desvincular vídeos desta pasta
        $pdo = \CoffeeCode\DataLayer\Connect::getInstance();
        $stmt = $pdo->prepare("UPDATE winturb_videos SET pasta_id = NULL WHERE pasta_id = :p AND usuario = :u");
        $stmt->execute([':p' => (int)$pasta->id, ':u' => $userId]);

        echo json_encode(["ok" => $pasta->destroy()]);
    }

    /** POST /pastas/mover — move vídeo para pasta (ou tira da pasta) */
    public function mover($data)
    {
        header("Content-Type: application/json");
        (new AppSessao())->verificaSeLogado();

        $userId  = AppSessao::usuarioId();
        $video   = (new Video())->findById($data["video_id"] ?? 0);
        $pastaId = !empty($data["pasta_id"]) ? (int)$data["pasta_id"] : null;

        if (!$video || (int)$video->usuario !== $userId) {
            echo json_encode(["ok" => false]);
            return;
        }

        if ($pastaId) {
            $pasta = (new Pasta())->findById($pastaId);
            if (!$pasta || (int)$pasta->usuario !== $userId) {
                echo json_encode(["ok" => false, "msg" => "Pasta inválida."]);
                return;
            }
        }

        $video->pasta_id = $pastaId;
        echo json_encode(["ok" => $video->save()]);
    }
}

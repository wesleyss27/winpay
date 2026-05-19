<?php

namespace Source\App\Anuncios;

session_start();

use Source\App\Sessao\AppSessao;
use Source\Models\Anuncios\Anuncio;
use Source\Models\Videos\Video;

class AppAnuncios
{
    public function __construct($router) {}

    public function salvar($data)
    {
        header("Content-Type: application/json");
        (new AppSessao())->verificaSeLogado();

        $userId  = AppSessao::usuarioId();
        $videoId = (int)($data["video_id"] ?? 0);

        // Confirma que o vídeo pertence ao usuário
        $video = (new Video())->findById($videoId);
        if (!$video || (int)$video->usuario !== $userId) {
            echo json_encode(["ok" => false, "msg" => "Não autorizado."]);
            return;
        }

        $id = (int)($data["id"] ?? 0);
        $ad = $id ? (new Anuncio())->findById($id) : new Anuncio();

        if (!$ad) $ad = new Anuncio();

        $ad->video_id    = $videoId;
        $ad->nome        = trim($data["nome"] ?? "");
        $ad->descricao   = trim($data["descricao"] ?? "");
        $ad->valor       = !empty($data["valor"]) ? (float)str_replace(",", ".", $data["valor"]) : null;
        $ad->url_destino = trim($data["url_destino"] ?? "");
        $ad->exibir_em   = (int)($data["exibir_em"] ?? 0);
        $ad->modo        = in_array($data["modo"] ?? "", ["pausa", "banner"]) ? $data["modo"] : "pausa";
        $ad->status      = 1;

        // Imagem do anúncio
        if (!empty($_FILES["imagem"]["name"]) && $_FILES["imagem"]["error"] === UPLOAD_ERR_OK) {
            $ext    = strtolower(pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION));
            $dir    = __DIR__ . "/../../../uploads/anuncios/";
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $nome   = md5(uniqid("", true)) . "." . $ext;
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $dir . $nome)) {
                $ad->imagem = $nome;
            }
        }

        $ok = $ad->save();
        echo json_encode(["ok" => $ok, "id" => $ad->id, "msg" => $ok ? null : ($ad->fail() ? $ad->fail()->getMessage() : "Erro.")]);
    }

    public function excluir($data)
    {
        header("Content-Type: application/json");
        (new AppSessao())->verificaSeLogado();

        $userId = AppSessao::usuarioId();
        $ad     = (new Anuncio())->findById($data["id"] ?? 0);

        if (!$ad) { echo json_encode(["ok" => false]); return; }

        $video = (new Video())->findById($ad->video_id);
        if (!$video || (int)$video->usuario !== $userId) {
            echo json_encode(["ok" => false, "msg" => "Não autorizado."]);
            return;
        }

        echo json_encode(["ok" => $ad->destroy()]);
    }
}

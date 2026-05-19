<?php

namespace Source\Models\Anuncios;

use CoffeeCode\DataLayer\DataLayer;

class Anuncio extends DataLayer
{
    public function __construct()
    {
        parent::__construct("winturb_anuncios", ["video_id", "nome", "url_destino", "exibir_em"], "id", false);
    }

    public function porVideo(int $videoId): array
    {
        $rows = $this->find("video_id = :v AND status = 1", "v={$videoId}")
            ->order("exibir_em ASC")
            ->fetch(true);
        return $rows ?: [];
    }
}

/**
 * id          int
 * video_id    int
 * nome        varchar(255)
 * descricao   text
 * imagem      varchar(255)
 * valor       decimal(10,2)
 * url_destino varchar(500)
 * exibir_em   int            -- segundos no vídeo
 * status      tinyint
 * created_at  timestamp
 */

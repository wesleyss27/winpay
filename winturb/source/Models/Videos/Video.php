<?php

namespace Source\Models\Videos;

use CoffeeCode\DataLayer\DataLayer;

class Video extends DataLayer
{
    public function __construct()
    {
        parent::__construct("winturb_videos", ["usuario", "titulo", "embed_token"], "id", false);
    }

    public function findByToken(string $token): ?self
    {
        return $this->find("embed_token = :t AND status = 1", "t={$token}")->fetch() ?: null;
    }

    public static function gerarToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}

/**
 * id            int
 * usuario       int
 * titulo        varchar(255)
 * descricao     text
 * arquivo       varchar(255)   -- usuario_id/hash.ext
 * tamanho       bigint
 * capa          varchar(255)
 * embed_token   varchar(64)    UNIQUE
 * largura       int            default 854
 * altura        int            default 480
 * anti_skip     tinyint        default 1
 * allow_speed   tinyint        default 1
 * status        tinyint        default 1
 * pitch_time    int            default 0   -- tempo em segundos do pitch
 * created_at    timestamp
 * updated_at    timestamp
 */

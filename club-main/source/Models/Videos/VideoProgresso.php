<?php

namespace Source\Models\Videos;

use CoffeeCode\DataLayer\DataLayer;
use CoffeeCode\DataLayer\Connect;

class VideoProgresso extends DataLayer
{
    public function __construct()
    {
        parent::__construct("video_progresso", ["usuario", "aula_id", "video_id"], "id", false);
    }

    /** Busca o progresso de um aluno em uma aula específica */
    public function findByUsuarioAula(int $usuario, int $aulaId): ?self
    {
        try {
            $pdo  = Connect::getInstance();
            $stmt = $pdo->prepare(
                "SELECT * FROM `video_progresso` WHERE `usuario` = :u AND `aula_id` = :a LIMIT 1"
            );
            $stmt->execute([':u' => $usuario, ':a' => $aulaId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$row) return null;
            foreach ($row as $key => $value) {
                $this->$key = $value;
            }
            return $this;
        } catch (\PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    /** Upsert: salva ou atualiza progresso */
    public function upsert(int $usuario, int $aulaId, int $videoId, float $currentTime, float $duration, int $watchSeconds): bool
    {
        try {
            $pdo  = Connect::getInstance();
            $percent = $duration > 0 ? round(($currentTime / $duration) * 100, 2) : 0;
            $completed = $percent >= 90 ? 1 : 0;

            // VALUES(col) referencia o valor do INSERT no ON DUPLICATE KEY UPDATE,
            // evitando o problema de named params duplicados no PDO.
            $stmt = $pdo->prepare("
                INSERT INTO `video_progresso`
                    (`usuario`, `aula_id`, `video_id`, `current_time`, `duration`, `percent_watched`, `completed`, `total_watch_time`, `last_watched_at`)
                VALUES
                    (:u, :a, :v, :ct, :dur, :pct, :comp, :tw, NOW())
                ON DUPLICATE KEY UPDATE
                    `current_time`     = VALUES(`current_time`),
                    `duration`         = VALUES(`duration`),
                    `percent_watched`  = VALUES(`percent_watched`),
                    `completed`        = GREATEST(`completed`, VALUES(`completed`)),
                    `total_watch_time` = `total_watch_time` + VALUES(`total_watch_time`),
                    `last_watched_at`  = NOW()
            ");
            return $stmt->execute([
                ':u'    => $usuario,
                ':a'    => $aulaId,
                ':v'    => $videoId,
                ':ct'   => $currentTime,
                ':dur'  => $duration,
                ':pct'  => $percent,
                ':comp' => $completed,
                ':tw'   => $watchSeconds,
            ]);
        } catch (\PDOException $e) {
            $this->fail = $e;
            return false;
        }
    }
}

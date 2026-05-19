<?php

namespace Source\Models\Videos;

use CoffeeCode\DataLayer\DataLayer;
use CoffeeCode\DataLayer\Connect;

class VideoProgresso extends DataLayer
{
    public function __construct()
    {
        parent::__construct("winturb_progresso", ["video_id"], "id", false);
    }

    public function findByKey(int $videoId, ?int $usuario, ?string $sessionKey): ?self
    {
        try {
            $pdo = Connect::getInstance();
            if ($usuario) {
                $stmt = $pdo->prepare("SELECT * FROM winturb_progresso WHERE video_id = :v AND usuario = :u LIMIT 1");
                $stmt->execute([':v' => $videoId, ':u' => $usuario]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM winturb_progresso WHERE video_id = :v AND session_key = :s LIMIT 1");
                $stmt->execute([':v' => $videoId, ':s' => $sessionKey]);
            }
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$row) return null;
            foreach ($row as $k => $val) $this->$k = $val;
            return $this;
        } catch (\PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    public function upsert(int $videoId, ?int $usuario, ?string $sessionKey, float $currentTime, float $duration, int $watchSecs): bool
    {
        try {
            $pdo     = Connect::getInstance();
            $percent = $duration > 0 ? round(($currentTime / $duration) * 100, 2) : 0;
            $completed = $percent >= 90 ? 1 : 0;

            if ($usuario) {
                $stmt = $pdo->prepare("
                    INSERT INTO winturb_progresso (video_id, usuario, current_time, duration, percent_watched, completed, total_watch_time)
                    VALUES (:v, :u, :ct, :dur, :pct, :comp, :tw)
                    ON DUPLICATE KEY UPDATE
                        current_time      = VALUES(current_time),
                        duration          = VALUES(duration),
                        percent_watched   = VALUES(percent_watched),
                        completed         = GREATEST(completed, VALUES(completed)),
                        total_watch_time  = total_watch_time + VALUES(total_watch_time),
                        last_watched_at   = NOW()
                ");
                return $stmt->execute([':v' => $videoId, ':u' => $usuario, ':ct' => $currentTime, ':dur' => $duration, ':pct' => $percent, ':comp' => $completed, ':tw' => $watchSecs]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO winturb_progresso (video_id, session_key, current_time, duration, percent_watched, completed, total_watch_time)
                    VALUES (:v, :s, :ct, :dur, :pct, :comp, :tw)
                    ON DUPLICATE KEY UPDATE
                        current_time      = VALUES(current_time),
                        duration          = VALUES(duration),
                        percent_watched   = VALUES(percent_watched),
                        completed         = GREATEST(completed, VALUES(completed)),
                        total_watch_time  = total_watch_time + VALUES(total_watch_time),
                        last_watched_at   = NOW()
                ");
                return $stmt->execute([':v' => $videoId, ':s' => $sessionKey, ':ct' => $currentTime, ':dur' => $duration, ':pct' => $percent, ':comp' => $completed, ':tw' => $watchSecs]);
            }
        } catch (\PDOException $e) {
            $this->fail = $e;
            return false;
        }
    }
}

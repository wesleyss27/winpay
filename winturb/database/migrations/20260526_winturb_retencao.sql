-- Retenção: máximo segundo assistido por sessão
CREATE TABLE IF NOT EXISTS `winturb_retencao` (
  `video_id`      int         NOT NULL,
  `session_key`   varchar(64) NOT NULL,
  `max_segundo`   smallint    UNSIGNED NOT NULL DEFAULT 0,
  `duracao`       smallint    UNSIGNED NOT NULL DEFAULT 0,
  `atualizado_em` datetime    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`video_id`, `session_key`),
  KEY `idx_vid_seg` (`video_id`, `max_segundo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tempo do pitch (segundo onde ocorre a oferta principal)
ALTER TABLE `winturb_videos`
  ADD COLUMN IF NOT EXISTS `pitch_time` INT DEFAULT NULL AFTER `fake_seekbar`

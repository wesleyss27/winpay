-- WinTurb — métricas de player
-- Cada linha = um evento rastreado no embed player

CREATE TABLE IF NOT EXISTS `winturb_eventos` (
  `id`          BIGINT(20)  NOT NULL AUTO_INCREMENT,
  `video_id`    INT(11)     NOT NULL,
  `ad_id`       INT(11)     DEFAULT NULL,
  `tipo`        ENUM(
                  'view',          -- player carregado (iframe abriu)
                  'play',          -- usuário clicou play (1ª vez por sessão)
                  'ad_impression', -- anúncio exibido
                  'ad_click',      -- usuário clicou no CTA do anúncio
                  'complete'       -- assistiu >= 90% do vídeo
                ) NOT NULL,
  `session_key` VARCHAR(64) DEFAULT NULL,
  `ip`          VARCHAR(45) DEFAULT NULL,
  `created_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_video_tipo`   (`video_id`, `tipo`),
  KEY `idx_video_ad`     (`video_id`, `ad_id`),
  KEY `idx_created_at`   (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

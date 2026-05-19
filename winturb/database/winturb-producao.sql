-- =============================================================
--  WinTurb — Schema completo para produção
--  Gerado em: 2026-05-19
--  Usar: importar uma única vez no banco de produção
-- =============================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- -------------------------------------------------------------
--  winturb_videos
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `winturb_videos` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `usuario`      INT(11)      NOT NULL,
  `pasta_id`     INT(11)      DEFAULT NULL,
  `titulo`       VARCHAR(255) NOT NULL,
  `descricao`    TEXT         DEFAULT NULL,
  `arquivo`      VARCHAR(255) DEFAULT NULL,
  `tamanho`      BIGINT(20)   DEFAULT NULL,
  `capa`         VARCHAR(255) DEFAULT NULL,
  `embed_token`  VARCHAR(64)  NOT NULL,
  `largura`      INT(11)      NOT NULL DEFAULT 854,
  `altura`       INT(11)      NOT NULL DEFAULT 480,
  `anti_skip`    TINYINT(1)   NOT NULL DEFAULT 1,
  `allow_speed`  TINYINT(1)   NOT NULL DEFAULT 1,
  `hide_seekbar` TINYINT(1)   NOT NULL DEFAULT 0,
  `autoplay`     TINYINT(1)   NOT NULL DEFAULT 0,
  `force_play`   TINYINT(1)   NOT NULL DEFAULT 0,
  `cor_acento`   VARCHAR(20)  NOT NULL DEFAULT '#7c6af7',
  `cor_fundo`    VARCHAR(20)  NOT NULL DEFAULT '#000000',
  `status`       TINYINT(4)   NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `embed_token` (`embed_token`),
  KEY `idx_usuario`  (`usuario`),
  KEY `idx_pasta_id` (`pasta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  winturb_anuncios
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `winturb_anuncios` (
  `id`          INT(11)                NOT NULL AUTO_INCREMENT,
  `video_id`    INT(11)                NOT NULL,
  `nome`        VARCHAR(255)           NOT NULL,
  `descricao`   TEXT                   DEFAULT NULL,
  `imagem`      VARCHAR(255)           DEFAULT NULL,
  `valor`       DECIMAL(10,2)          DEFAULT NULL,
  `url_destino` VARCHAR(500)           NOT NULL,
  `exibir_em`   INT(11)                NOT NULL DEFAULT 0,
  `modo`        ENUM('pausa','banner') NOT NULL DEFAULT 'pausa',
  `status`      TINYINT(4)             NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP              NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_video_id` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  winturb_progresso
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `winturb_progresso` (
  `id`               INT(11)     NOT NULL AUTO_INCREMENT,
  `usuario`          INT(11)     DEFAULT NULL,
  `session_key`      VARCHAR(64) DEFAULT NULL,
  `video_id`         INT(11)     NOT NULL,
  `current_time`     FLOAT       NOT NULL DEFAULT 0,
  `duration`         FLOAT       NOT NULL DEFAULT 0,
  `percent_watched`  FLOAT       NOT NULL DEFAULT 0,
  `completed`        TINYINT(1)  NOT NULL DEFAULT 0,
  `total_watch_time` INT(11)     NOT NULL DEFAULT 0,
  `last_watched_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_video` (`usuario`, `video_id`),
  UNIQUE KEY `uk_session_video` (`session_key`, `video_id`),
  KEY `idx_video_id` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  winturb_eventos
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `winturb_eventos` (
  `id`          BIGINT(20)  NOT NULL AUTO_INCREMENT,
  `video_id`    INT(11)     NOT NULL,
  `ad_id`       INT(11)     DEFAULT NULL,
  `tipo`        ENUM('view','play','ad_impression','ad_click','complete') NOT NULL,
  `session_key` VARCHAR(64) DEFAULT NULL,
  `ip`          VARCHAR(45) DEFAULT NULL,
  `created_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_video_tipo` (`video_id`, `tipo`),
  KEY `idx_video_ad`   (`video_id`, `ad_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  winturb_pastas
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `winturb_pastas` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `usuario`    INT(11)      NOT NULL,
  `nome`       VARCHAR(100) NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pasta_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;

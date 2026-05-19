-- WinTurb — tabelas base
-- videos, anuncios, progresso

CREATE TABLE IF NOT EXISTS `winturb_videos` (
  `id`            INT(11)       NOT NULL AUTO_INCREMENT,
  `usuario`       INT(11)       NOT NULL,
  `titulo`        VARCHAR(255)  NOT NULL,
  `descricao`     TEXT          DEFAULT NULL,
  `arquivo`       VARCHAR(255)  DEFAULT NULL,
  `tamanho`       BIGINT(20)    DEFAULT NULL,
  `capa`          VARCHAR(255)  DEFAULT NULL,
  `embed_token`   VARCHAR(64)   NOT NULL,
  `largura`       INT(11)       NOT NULL DEFAULT 854,
  `altura`        INT(11)       NOT NULL DEFAULT 480,
  `anti_skip`     TINYINT(1)    NOT NULL DEFAULT 1,
  `allow_speed`   TINYINT(1)    NOT NULL DEFAULT 1,
  `status`        TINYINT(4)    NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `embed_token` (`embed_token`),
  KEY `idx_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `winturb_anuncios` (
  `id`          INT(11)         NOT NULL AUTO_INCREMENT,
  `video_id`    INT(11)         NOT NULL,
  `nome`        VARCHAR(255)    NOT NULL,
  `descricao`   TEXT            DEFAULT NULL,
  `imagem`      VARCHAR(255)    DEFAULT NULL,
  `valor`       DECIMAL(10,2)   DEFAULT NULL,
  `url_destino` VARCHAR(500)    NOT NULL,
  `exibir_em`   INT(11)         NOT NULL DEFAULT 0,
  `status`      TINYINT(4)      NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_video_id` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  UNIQUE KEY `uk_usuario_video`  (`usuario`, `video_id`),
  UNIQUE KEY `uk_session_video`  (`session_key`, `video_id`),
  KEY `idx_video_id` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

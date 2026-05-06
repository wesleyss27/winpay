CREATE TABLE `video_progresso` (
  `id`              int(11)        NOT NULL AUTO_INCREMENT,
  `usuario`         int(11)        NOT NULL,
  `aula_id`         int(11)        NOT NULL,
  `video_id`        int(11)        NOT NULL,
  `current_time`    decimal(10,3)  DEFAULT 0,
  `duration`        decimal(10,3)  DEFAULT 0,
  `percent_watched` decimal(5,2)   DEFAULT 0,
  `completed`       tinyint(1)     DEFAULT 0,
  `total_watch_time` int(11)       DEFAULT 0 COMMENT 'segundos totais assistidos',
  `last_watched_at` timestamp      NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_aula` (`usuario`, `aula_id`),
  KEY `idx_video` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

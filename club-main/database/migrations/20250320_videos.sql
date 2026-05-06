CREATE TABLE `videos` (
  `id`          int(11)        NOT NULL AUTO_INCREMENT,
  `usuario`     int(11)        NOT NULL,
  `titulo`      varchar(200)   NOT NULL,
  `arquivo`     varchar(300)   NOT NULL,
  `tamanho`     bigint(20)     DEFAULT 0,
  `duracao`     int(11)        DEFAULT 0 COMMENT 'duração em segundos',
  `status`      tinyint(1)     DEFAULT 1 COMMENT '1=ativo, 0=inativo',
  `created_at`  timestamp      NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  timestamp      NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

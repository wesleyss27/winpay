-- Sistema de pastas/biblioteca para organizar vídeos
CREATE TABLE IF NOT EXISTS `winturb_pastas` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `usuario`    int(11)      NOT NULL,
  `nome`       varchar(100) NOT NULL,
  `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pasta_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coluna de referência de pasta nos vídeos
ALTER TABLE `winturb_videos`
  ADD COLUMN IF NOT EXISTS `pasta_id` int(11) DEFAULT NULL AFTER `usuario`;

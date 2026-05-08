CREATE TABLE IF NOT EXISTS `pixel_leads` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `fbclid`       VARCHAR(255) DEFAULT NULL,
  `fbc`          VARCHAR(255) DEFAULT NULL,
  `ip`           VARCHAR(45)  NOT NULL,
  `ua`           TEXT         DEFAULT NULL,
  `utm_source`   VARCHAR(200) DEFAULT NULL,
  `utm_campaign` VARCHAR(200) DEFAULT NULL,
  `utm_medium`   VARCHAR(200) DEFAULT NULL,
  `utm_content`  VARCHAR(200) DEFAULT NULL,
  `utm_term`     VARCHAR(200) DEFAULT NULL,
  `src`          VARCHAR(200) DEFAULT NULL,
  `sck`          VARCHAR(200) DEFAULT NULL,
  `round_id`     VARCHAR(100) DEFAULT NULL,
  `round_number` VARCHAR(100) DEFAULT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migration: adiciona colunas se a tabela já existir
ALTER TABLE `pixel_leads`
  ADD COLUMN IF NOT EXISTS `utm_source`   VARCHAR(200) DEFAULT NULL AFTER `ua`,
  ADD COLUMN IF NOT EXISTS `utm_medium`   VARCHAR(200) DEFAULT NULL AFTER `utm_source`,
  ADD COLUMN IF NOT EXISTS `utm_campaign` VARCHAR(200) DEFAULT NULL AFTER `utm_medium`,
  ADD COLUMN IF NOT EXISTS `utm_content`  VARCHAR(200) DEFAULT NULL AFTER `utm_campaign`,
  ADD COLUMN IF NOT EXISTS `utm_term`     VARCHAR(200) DEFAULT NULL AFTER `utm_content`,
  ADD COLUMN IF NOT EXISTS `src`          VARCHAR(200) DEFAULT NULL AFTER `utm_term`,
  ADD COLUMN IF NOT EXISTS `sck`          VARCHAR(200) DEFAULT NULL AFTER `src`,
  ADD COLUMN IF NOT EXISTS `round_id`     VARCHAR(100) DEFAULT NULL AFTER `sck`,
  ADD COLUMN IF NOT EXISTS `round_number` VARCHAR(100) DEFAULT NULL AFTER `round_id`;

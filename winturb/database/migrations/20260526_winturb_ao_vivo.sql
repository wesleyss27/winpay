-- Feature "Ao Vivo": adiciona campos de geo/device na tabela de retenção
ALTER TABLE `winturb_retencao`
  ADD COLUMN IF NOT EXISTS `ip`          varchar(45)  DEFAULT NULL AFTER `duracao`,
  ADD COLUMN IF NOT EXISTS `user_agent`  varchar(300) DEFAULT NULL AFTER `ip`,
  ADD COLUMN IF NOT EXISTS `device`      varchar(20)  DEFAULT NULL AFTER `user_agent`,
  ADD COLUMN IF NOT EXISTS `pais`        varchar(100) DEFAULT NULL AFTER `device`,
  ADD COLUMN IF NOT EXISTS `cidade`      varchar(100) DEFAULT NULL AFTER `pais`,
  ADD COLUMN IF NOT EXISTS `geo_ok`      tinyint(1)   DEFAULT 0   AFTER `cidade`;

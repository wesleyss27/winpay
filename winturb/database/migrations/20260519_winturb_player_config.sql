-- Personalização visual do player e controle da barra de reprodução
ALTER TABLE `winturb_videos`
  ADD COLUMN `hide_seekbar` TINYINT(1)   NOT NULL DEFAULT 0  AFTER `allow_speed`,
  ADD COLUMN `cor_acento`   VARCHAR(20)  NOT NULL DEFAULT '#7c6af7' AFTER `hide_seekbar`,
  ADD COLUMN `cor_fundo`    VARCHAR(20)  NOT NULL DEFAULT '#000000' AFTER `cor_acento`;

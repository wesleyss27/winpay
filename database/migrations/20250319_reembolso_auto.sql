ALTER TABLE `reembolsos`
  ADD COLUMN `auto_reembolsado`   TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Reembolso processado automaticamente via gateway',
  ADD COLUMN `auto_reembolso_erro` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Tentativa de reembolso automático falhou; manter pendente para revisão manual';

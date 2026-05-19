-- Coluna autoplay na tabela de vídeos
ALTER TABLE `winturb_videos`
  ADD COLUMN IF NOT EXISTS `autoplay` tinyint(1) NOT NULL DEFAULT 0 AFTER `hide_seekbar`;

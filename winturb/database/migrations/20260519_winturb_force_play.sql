-- Reprodução forçada: volume máximo, sem controles, sem pausa
ALTER TABLE `winturb_videos`
  ADD COLUMN IF NOT EXISTS `force_play` tinyint(1) NOT NULL DEFAULT 0 AFTER `autoplay`;

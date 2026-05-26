-- Barra fake: progresso visual não interativo (oculta a barra real automaticamente)
ALTER TABLE `winturb_videos`
  ADD COLUMN `fake_seekbar` TINYINT(1) NOT NULL DEFAULT 0 AFTER `hide_seekbar`;

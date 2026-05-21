ALTER TABLE `winturb_videos`
  ADD COLUMN IF NOT EXISTS `hls_status` VARCHAR(20) NOT NULL DEFAULT 'none' AFTER `force_play`;

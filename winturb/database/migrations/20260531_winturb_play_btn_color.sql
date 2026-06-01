-- Migration: cor personalizada do botão play
-- Data: 2026-05-31

ALTER TABLE winturb_videos
  ADD COLUMN IF NOT EXISTS cor_play_btn VARCHAR(7) DEFAULT NULL COMMENT 'Cor do botão play (hex). NULL = usa cor_acento';

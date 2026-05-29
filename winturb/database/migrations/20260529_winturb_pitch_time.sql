-- Migration: campo pitch_time na tabela winturb_videos
-- Data: 2026-05-29

ALTER TABLE winturb_videos
  ADD COLUMN IF NOT EXISTS pitch_time INT NOT NULL DEFAULT 0 COMMENT 'Tempo em segundos do pitch (0 = não definido)';

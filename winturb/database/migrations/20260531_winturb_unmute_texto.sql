-- Migration: cor do texto do card "Clique para ouvir"
-- Data: 2026-05-31

ALTER TABLE winturb_videos
  ADD COLUMN IF NOT EXISTS cor_unmute_texto VARCHAR(7) DEFAULT NULL COMMENT 'Cor do texto do card unmute (hex). NULL = #ffffff';

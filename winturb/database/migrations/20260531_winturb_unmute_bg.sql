-- Migration: cor de fundo do card "Clique para ouvir"
-- Data: 2026-05-31

ALTER TABLE winturb_videos
  ADD COLUMN IF NOT EXISTS cor_unmute_bg VARCHAR(7) DEFAULT NULL COMMENT 'Cor de fundo do card unmute (hex). NULL = #000000';

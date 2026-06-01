-- Migration: textos customizados do card "Clique para ouvir"
-- Data: 2026-05-31

ALTER TABLE winturb_videos
  ADD COLUMN IF NOT EXISTS unmute_label VARCHAR(100) DEFAULT NULL COMMENT 'Título do card unmute. NULL = "Clique para ouvir"',
  ADD COLUMN IF NOT EXISTS unmute_sub   VARCHAR(150) DEFAULT NULL COMMENT 'Subtítulo do card unmute. NULL = "Vídeo com som desativado"';

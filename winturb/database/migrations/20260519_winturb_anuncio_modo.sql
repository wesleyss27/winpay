-- Modo de exibição do anúncio: 'pausa' (overlay + pausa) | 'banner' (banner simples, vídeo continua)
ALTER TABLE `winturb_anuncios`
  ADD COLUMN IF NOT EXISTS `modo` ENUM('pausa','banner') NOT NULL DEFAULT 'pausa' AFTER `exibir_em`;

-- Vincula oferta a uma aula específica e define o segundo do vídeo para exibir o overlay
-- aula_id  = NULL → exibida na sidebar (comportamento anterior)
-- exibir_em = segundos no vídeo; NULL → sem overlay no player

ALTER TABLE `ofertas`
  ADD COLUMN `aula_id`   int(11) DEFAULT NULL COMMENT 'aula onde o overlay aparece (NULL = sidebar)',
  ADD COLUMN `exibir_em` int(11) DEFAULT NULL COMMENT 'segundo do vídeo para disparar o overlay';

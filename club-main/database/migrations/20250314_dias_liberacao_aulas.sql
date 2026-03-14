-- Adiciona coluna dias_liberacao na tabela aulas
-- NULL = sem restrição (liberado imediatamente)
-- 0    = liberado imediatamente
-- N    = libera N dias após o cadastro do aluno no curso

ALTER TABLE aulas ADD COLUMN dias_liberacao INT DEFAULT NULL;

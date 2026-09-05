ALTER TABLE planos
ADD COLUMN texto_informativo VARCHAR(255) NULL DEFAULT NULL AFTER preco_original;

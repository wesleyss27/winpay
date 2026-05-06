-- Posição do objeto no banner (CSS object-position)
ALTER TABLE cursos
    ADD COLUMN IF NOT EXISTS banner_posicao VARCHAR(30) DEFAULT 'center center'
        COMMENT 'CSS object-position do banner: ex. center center, top right, 30% center';

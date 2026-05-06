-- Campos de tema visual da área de membros por curso
ALTER TABLE cursos
    ADD COLUMN IF NOT EXISTS tema_cor_primaria  VARCHAR(20)  DEFAULT '#f4d03f'  COMMENT 'Cor de destaque (hex)',
    ADD COLUMN IF NOT EXISTS tema_cor_fundo     VARCHAR(20)  DEFAULT '#0f1118'  COMMENT 'Cor de fundo (hex)',
    ADD COLUMN IF NOT EXISTS tema_cor_texto     VARCHAR(20)  DEFAULT '#ffffff'  COMMENT 'Cor do texto principal (hex)',
    ADD COLUMN IF NOT EXISTS tema_logo          VARCHAR(300) DEFAULT NULL       COMMENT 'Logotipo/imagem da marca do curso',
    ADD COLUMN IF NOT EXISTS tema_exibir_titulo TINYINT(1)   DEFAULT 1          COMMENT '1 = exibir nome do curso no cabeçalho',
    ADD COLUMN IF NOT EXISTS tema_titulo_exibicao VARCHAR(255) DEFAULT NULL     COMMENT 'Título alternativo para exibir (null = usa nome do curso)';

-- Imagem de capa para módulos
ALTER TABLE modulos
    ADD COLUMN IF NOT EXISTS imagem VARCHAR(300) DEFAULT NULL COMMENT 'Imagem de capa do módulo';

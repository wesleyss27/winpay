-- ============================================================
-- ÁREA DE MEMBROS — MIGRATION COMPLETA
-- Consolida todas as alterações de personalização da área de
-- membros por curso (banner, tema, módulos).
-- Seguro para rodar múltiplas vezes (IF NOT EXISTS).
-- ============================================================

-- ── TABELA: cursos ──────────────────────────────────────────

-- Banner largo no topo da página do curso (ex: 1280×400)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS banner VARCHAR(300) NULL DEFAULT NULL
    COMMENT 'Imagem de banner wide para o topo da área de membros';

-- Título exibido sobre o banner
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS banner_titulo VARCHAR(255) NULL DEFAULT NULL
    COMMENT 'Título customizado exibido sobre o banner';

-- Posição do objeto no banner (CSS object-position)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS banner_posicao VARCHAR(30) NOT NULL DEFAULT 'center center'
    COMMENT 'CSS object-position do banner: ex. center center, top right, 30% center';

-- Mensagem de boas-vindas exibida no hero
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS boas_vindas TEXT NULL DEFAULT NULL
    COMMENT 'Mensagem de boas-vindas exibida ao aluno';

-- Controla se o título das aulas aparece nos cards (1 = sim, 0 = não)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS mostrar_titulo_aulas TINYINT(1) NOT NULL DEFAULT 1
    COMMENT '1 = exibir título nos cards de aula/módulo';

-- Cor de destaque do tema (hex)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS tema_cor_primaria VARCHAR(20) NOT NULL DEFAULT '#f4d03f'
    COMMENT 'Cor de destaque do tema (hex)';

-- Cor de fundo do tema (hex)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS tema_cor_fundo VARCHAR(20) NOT NULL DEFAULT '#0f1118'
    COMMENT 'Cor de fundo do tema (hex)';

-- Cor do texto principal (hex)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS tema_cor_texto VARCHAR(20) NOT NULL DEFAULT '#ffffff'
    COMMENT 'Cor do texto principal (hex)';

-- Logotipo/imagem da marca do curso
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS tema_logo VARCHAR(300) NULL DEFAULT NULL
    COMMENT 'Logotipo da marca do curso exibido na sidebar';

-- Exibir nome do curso no cabeçalho
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS tema_exibir_titulo TINYINT(1) NOT NULL DEFAULT 1
    COMMENT '1 = exibir nome do curso no cabeçalho da área de membros';

-- Título alternativo para exibir no cabeçalho (null = usa nome do curso)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS tema_titulo_exibicao VARCHAR(255) NULL DEFAULT NULL
    COMMENT 'Título alternativo para exibir (null = usa nome do curso)';

-- ── TABELA: modulos ─────────────────────────────────────────

-- Imagem de capa do módulo (portrait 600×900 recomendado)
ALTER TABLE modulos
  ADD COLUMN IF NOT EXISTS imagem VARCHAR(300) NULL DEFAULT NULL
    COMMENT 'Imagem de capa do módulo (portrait 600×900 recomendado)';

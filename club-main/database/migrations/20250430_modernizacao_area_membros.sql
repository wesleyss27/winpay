-- ──────────────────────────────────────────────────────────────
-- MODERNIZAÇÃO DA ÁREA DE MEMBROS
-- Rodar apenas uma vez. Usa IF NOT EXISTS / IF EXISTS para
-- ser seguro caso alguma coluna já exista.
-- ──────────────────────────────────────────────────────────────

-- 1. Banner largo para o topo da página do curso (ex: 1280×400)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS banner VARCHAR(300) NULL DEFAULT NULL;

-- 2. Título customizado exibido sobre o banner (ex: "Área de Membros Exclusiva")
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS banner_titulo VARCHAR(255) NULL DEFAULT NULL AFTER banner;

-- 3. Mensagem de boas-vindas exibida no hero da área de membros
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS boas_vindas TEXT NULL DEFAULT NULL AFTER banner_titulo;

-- 4. Flag que controla se o título das aulas aparece nos cards (1 = sim, 0 = não)
ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS mostrar_titulo_aulas TINYINT(1) NOT NULL DEFAULT 1 AFTER boas_vindas;

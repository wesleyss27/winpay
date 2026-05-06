ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS mostrar_titulo_aulas TINYINT(1) NOT NULL DEFAULT 1 AFTER boas_vindas;

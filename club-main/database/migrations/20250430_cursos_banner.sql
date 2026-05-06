-- Personalização da área de membros pelo produtor
-- banner      = imagem larga (ex: 1280x400) para o topo da página do curso
-- boas_vindas = mensagem exibida no hero da área de membros

ALTER TABLE `cursos`
  ADD COLUMN `banner`      varchar(300) DEFAULT NULL COMMENT 'banner wide para o topo da área de membros',
  ADD COLUMN `boas_vindas` text         DEFAULT NULL COMMENT 'mensagem de boas-vindas para o aluno';

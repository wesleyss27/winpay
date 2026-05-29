-- Migration: Módulo Atendente + Venda Personalizada
-- Data: 2026-05-28

-- 1. Novos campos na tabela vendas
ALTER TABLE vendas
  ADD COLUMN IF NOT EXISTS tipo_venda TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=normal 1=personalizada',
  ADD COLUMN IF NOT EXISTS link_publico VARCHAR(64) DEFAULT NULL COMMENT 'UUID público para página de pagamento',
  ADD COLUMN IF NOT EXISTS atendente INT(11) DEFAULT NULL COMMENT 'FK usuario.id do atendente que criou';

CREATE INDEX IF NOT EXISTS idx_vendas_link_publico ON vendas(link_publico);
CREATE INDEX IF NOT EXISTS idx_vendas_atendente    ON vendas(atendente);

-- 1b. Associação atendente → produtor (campo no próprio usuario)
ALTER TABLE usuario
  ADD COLUMN IF NOT EXISTS atendente_produtor INT(11) DEFAULT NULL COMMENT 'Para tipo_usuario=7: FK usuario.id do produtor associado';

CREATE INDEX IF NOT EXISTS idx_usuario_atendente_produtor ON usuario(atendente_produtor);

-- 2. Tabela de itens da venda personalizada
CREATE TABLE IF NOT EXISTS venda_personalizada_itens (
  id                  INT(11)        NOT NULL AUTO_INCREMENT,
  venda               INT(11)        NOT NULL,
  produto             INT(11)        NOT NULL,
  plano               INT(11)        DEFAULT NULL,
  nome_produto        VARCHAR(255)   DEFAULT NULL,
  nome_plano          VARCHAR(255)   DEFAULT NULL,
  valor_original      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  valor_proporcional  DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  created_at          TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_vpi_venda (venda),
  CONSTRAINT fk_vpi_venda FOREIGN KEY (venda) REFERENCES vendas (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Se a tabela já existia sem updated_at, adiciona a coluna
ALTER TABLE venda_personalizada_itens
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

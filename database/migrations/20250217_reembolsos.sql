-- Tabela de solicitações de reembolso (Winpay)
CREATE TABLE IF NOT EXISTS reembolsos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  venda_id BIGINT UNSIGNED NULL,
  produto_id BIGINT UNSIGNED NULL,
  comprador_id BIGINT UNSIGNED NULL,
  produtor_id BIGINT UNSIGNED NULL,
  afiliado_id BIGINT UNSIGNED NULL,
  valor_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  motivo TEXT NULL,
  motivo_cliente TEXT NULL,
  motivo_recusa  TEXT NULL,
  contestacao TEXT NULL,
  contestado_em TIMESTAMP NULL DEFAULT NULL,
  itens JSON NULL,
  status TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=pending,1=aprovado,2=recusado,3=processando,4=concluido',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_venda (venda_id),
  INDEX idx_produtor (produtor_id),
  INDEX idx_afiliado (afiliado_id),
  INDEX idx_comprador (comprador_id),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

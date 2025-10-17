-- Manual sales feature: header, items, attachments and links with vendas/assinaturas

START TRANSACTION;

CREATE TABLE IF NOT EXISTS manual_sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produtor_id INT NOT NULL,
    comprador_id INT NOT NULL,
    parcelas TINYINT UNSIGNED NOT NULL DEFAULT 1,
    forma_pagamento VARCHAR(20) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    observacoes TEXT NULL,
    status SMALLINT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_manual_sales_produtor (produtor_id),
    INDEX idx_manual_sales_comprador (comprador_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS manual_sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    manual_sale_id BIGINT UNSIGNED NOT NULL,
    produto_id INT NOT NULL,
    plano_id INT NOT NULL,
    quantidade INT UNSIGNED NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ciclo_recorrencia VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_manual_sale_items_sale FOREIGN KEY (manual_sale_id) REFERENCES manual_sales(id) ON DELETE CASCADE,
    INDEX idx_manual_sale_items_sale (manual_sale_id),
    INDEX idx_manual_sale_items_produto (produto_id),
    INDEX idx_manual_sale_items_plano (plano_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS manual_sale_files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    manual_sale_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(40) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    nome_original VARCHAR(255) NULL,
    tamanho INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_manual_sale_files_sale FOREIGN KEY (manual_sale_id) REFERENCES manual_sales(id) ON DELETE CASCADE,
    INDEX idx_manual_sale_files_sale (manual_sale_id),
    INDEX idx_manual_sale_files_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE produto
    ADD COLUMN controla_estoque TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN estoque_atual INT NOT NULL DEFAULT 0;

ALTER TABLE vendas
    ADD COLUMN manual_sale_item_id BIGINT UNSIGNED NULL AFTER carrinho_abandonado,
    ADD INDEX idx_vendas_manual_sale_item (manual_sale_item_id),
    ADD CONSTRAINT fk_vendas_manual_sale_item FOREIGN KEY (manual_sale_item_id) REFERENCES manual_sale_items(id) ON DELETE SET NULL;

ALTER TABLE assinaturas
    ADD COLUMN manual_sale_item_id BIGINT UNSIGNED NULL AFTER aceitar_afiliado,
    ADD INDEX idx_assinaturas_manual_sale_item (manual_sale_item_id),
    ADD CONSTRAINT fk_assinaturas_manual_sale_item FOREIGN KEY (manual_sale_item_id) REFERENCES manual_sale_items(id) ON DELETE SET NULL;

COMMIT;

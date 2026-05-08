-- Adiciona coluna gateway_usado na tabela vendas
-- Registra qual gateway processou cada transação: 'pagarme' | 'stripe' | 'efibank'
-- Permite estorno automático sem depender de prefixo do ID da transação

ALTER TABLE `vendas`
    ADD COLUMN IF NOT EXISTS `gateway_usado` VARCHAR(20) DEFAULT NULL AFTER `meio_pagamento`;

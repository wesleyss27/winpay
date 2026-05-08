-- Adiciona modo 'personalizado' e colunas de gateway por tipo de cobrança

ALTER TABLE `configuracao_gateway`
    MODIFY COLUMN `modo` ENUM('balanceado','dedicado','personalizado') NOT NULL DEFAULT 'dedicado',
    ADD COLUMN IF NOT EXISTS `gateway_pix`     ENUM('pagarme','stripe') NOT NULL DEFAULT 'pagarme' AFTER `gateway_dedicado`,
    ADD COLUMN IF NOT EXISTS `gateway_cartao`  ENUM('pagarme','stripe') NOT NULL DEFAULT 'pagarme' AFTER `gateway_pix`,
    ADD COLUMN IF NOT EXISTS `gateway_boleto`  ENUM('pagarme','stripe') NOT NULL DEFAULT 'pagarme' AFTER `gateway_cartao`;

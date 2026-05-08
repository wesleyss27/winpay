-- ============================================================
-- Configuração do Gateway de Pagamento
-- Modos: 'balanceado' (fallback automático) | 'dedicado' (gateway único)
-- ============================================================

CREATE TABLE IF NOT EXISTS `configuracao_gateway` (
    `id`                        INT(11)         NOT NULL AUTO_INCREMENT,
    `modo`                      ENUM('balanceado','dedicado') NOT NULL DEFAULT 'dedicado',
    `gateway_principal`         ENUM('pagarme','stripe') NOT NULL DEFAULT 'pagarme',
    `gateway_dedicado`          ENUM('pagarme','stripe') NOT NULL DEFAULT 'pagarme',
    `usuario_ultima_alteracao`  INT(11)         DEFAULT NULL,
    `created_at`                TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`                TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registro inicial: modo dedicado PagarMe (sem quebrar comportamento atual)
INSERT INTO `configuracao_gateway` (`modo`, `gateway_principal`, `gateway_dedicado`)
VALUES ('dedicado', 'pagarme', 'pagarme');

-- ============================================================
-- Adiciona stripe_account_id nas tabelas de banco e usuários
-- (necessário para conectar vendedores à conta Stripe)
-- ============================================================

ALTER TABLE `banco`
    ADD COLUMN IF NOT EXISTS `stripe_account_id` VARCHAR(100) DEFAULT NULL AFTER `pagarme_conta`,
    ADD COLUMN IF NOT EXISTS `stripe_onboarding_url` VARCHAR(500) DEFAULT NULL AFTER `stripe_account_id`;

ALTER TABLE `usuario`
    ADD COLUMN IF NOT EXISTS `stripe_account_id` VARCHAR(100) DEFAULT NULL AFTER `pagarme_conta`;

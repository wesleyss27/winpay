-- ============================================================
-- Adiciona 'sharkhub' como opção em todos os campos ENUM de gateway
-- SharkHub: gateway sem split nativo — comissões via carteira WinPay
-- ============================================================

ALTER TABLE `configuracao_gateway`
    MODIFY COLUMN `gateway_principal` ENUM('pagarme','stripe','efibank','sharkhub') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_dedicado`  ENUM('pagarme','stripe','efibank','sharkhub') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_pix`       ENUM('pagarme','stripe','efibank','sharkhub') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_cartao`    ENUM('pagarme','stripe','efibank','sharkhub') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_boleto`    ENUM('pagarme','stripe','efibank','sharkhub') NOT NULL DEFAULT 'pagarme';

-- Registro de credenciais SharkHub (id=11)
-- chave_producao : API key de pagamentos (Bearer token)
-- token_producao : API key de saque (Bearer token — rotas /v1/transfer)
-- chave_sandbox  : API key de pagamentos sandbox
-- token_sandbox  : API key de saque sandbox
-- sandbox        : 0=produção | 1=sandbox
INSERT IGNORE INTO `configuracao_checkout` (`id`, `sandbox`) VALUES (11, 1);

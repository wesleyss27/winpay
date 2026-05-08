-- Adiciona campo efibank_payee_code nas tabelas de banco e usuário
-- Necessário para split de pagamento na EfiBank (Efí Pay)
-- O payee_code é o identificador da conta Efí do recebedor

ALTER TABLE `banco`
    ADD COLUMN IF NOT EXISTS `efibank_payee_code` VARCHAR(100) DEFAULT NULL AFTER `stripe_account_id`;

ALTER TABLE `usuario`
    ADD COLUMN IF NOT EXISTS `efibank_payee_code` VARCHAR(100) DEFAULT NULL AFTER `stripe_account_id`;

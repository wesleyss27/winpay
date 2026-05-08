-- Adiciona 'efibank' como opção em todos os campos ENUM de gateway

ALTER TABLE `configuracao_gateway`
    MODIFY COLUMN `gateway_principal` ENUM('pagarme','stripe','efibank') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_dedicado`  ENUM('pagarme','stripe','efibank') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_pix`       ENUM('pagarme','stripe','efibank') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_cartao`    ENUM('pagarme','stripe','efibank') NOT NULL DEFAULT 'pagarme',
    MODIFY COLUMN `gateway_boleto`    ENUM('pagarme','stripe','efibank') NOT NULL DEFAULT 'pagarme';

-- Registro de credenciais EfiBank (id=10)
-- chave_producao: "client_id|chave_pix"  (ex.: "Client_Id_xxx|seu@email.com")
-- token_producao: client_secret
INSERT IGNORE INTO `configuracao_checkout` (`id`, `sandbox`) VALUES (10, 1);

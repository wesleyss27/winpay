-- Configuração do App Meta (admin)
CREATE TABLE IF NOT EXISTS `meta_app_config` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `app_id`      VARCHAR(100) NOT NULL,
  `app_secret`  VARCHAR(200) NOT NULL,
  `versao_api`  VARCHAR(20)  DEFAULT 'v22.0',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Conexões OAuth dos produtores com Meta
CREATE TABLE IF NOT EXISTS `meta_conexoes` (
  `id`               INT(11)      NOT NULL AUTO_INCREMENT,
  `usuario`          INT(11)      NOT NULL,
  `access_token`     TEXT         NOT NULL,
  `token_expires_at` DATETIME     NULL,
  `meta_user_id`     VARCHAR(100) NOT NULL,
  `meta_user_name`   VARCHAR(200) NULL,
  `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contas de anúncio por usuário
CREATE TABLE IF NOT EXISTS `meta_contas_anuncio` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `usuario`      INT(11)      NOT NULL,
  `account_id`   VARCHAR(100) NOT NULL,
  `account_name` VARCHAR(200) NULL,
  `currency`     VARCHAR(10)  DEFAULT 'BRL',
  `ativa`        TINYINT(1)   DEFAULT 1,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_account` (`usuario`, `account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cache de insights do Meta (gasto por campanha/anúncio)
CREATE TABLE IF NOT EXISTS `meta_insights_cache` (
  `id`            INT(11)        NOT NULL AUTO_INCREMENT,
  `usuario`       INT(11)        NOT NULL,
  `account_id`    VARCHAR(100)   NOT NULL,
  `campaign_id`   VARCHAR(100)   NULL,
  `campaign_name` VARCHAR(500)   NULL,
  `adset_id`      VARCHAR(100)   NULL,
  `adset_name`    VARCHAR(500)   NULL,
  `ad_id`         VARCHAR(100)   NULL,
  `ad_name`       VARCHAR(500)   NULL,
  `spend`         DECIMAL(12,2)  DEFAULT 0.00,
  `impressions`   INT(11)        DEFAULT 0,
  `clicks`        INT(11)        DEFAULT 0,
  `date_start`    DATE           NOT NULL,
  `date_stop`     DATE           NOT NULL,
  `synced_at`     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_insight` (`usuario`, `ad_id`, `date_start`, `date_stop`),
  KEY `idx_usuario_campaign` (`usuario`, `campaign_id`),
  KEY `idx_usuario_dates` (`usuario`, `date_start`, `date_stop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

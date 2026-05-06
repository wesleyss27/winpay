-- Tabela de ofertas para exibição aleatória aos alunos durante as aulas
-- checkout_id = ID do checkout winpay (já contém produto + plano)
-- status      = 1 ativo, 0 inativo

CREATE TABLE `ofertas` (
  `id`           int(11)        NOT NULL AUTO_INCREMENT,
  `usuario`      int(11)        DEFAULT NULL,
  `curso`        int(11)        DEFAULT NULL,
  `checkout_id`  int(11)        DEFAULT NULL,
  `nome`         varchar(100)   DEFAULT NULL,
  `descricao`    text           DEFAULT NULL,
  `imagem`       varchar(300)   DEFAULT NULL,
  `valor`        decimal(10,2)  DEFAULT NULL,
  `status`       int(11)        DEFAULT 1,
  `created_at`   timestamp      NULL DEFAULT NULL,
  `updated_at`   timestamp      NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

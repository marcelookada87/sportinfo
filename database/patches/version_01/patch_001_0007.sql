-- Patch 001_0007
-- Cria tabela configuracoes_financeiras para gerenciar multa e juros
-- Permite configurar multa e juros por valor fixo ou porcentagem

CREATE TABLE IF NOT EXISTS `configuracoes_financeiras` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `chave` VARCHAR(100) NOT NULL,
    `valor` TEXT NOT NULL,
    `tipo` ENUM('string', 'integer', 'decimal', 'boolean') NOT NULL DEFAULT 'string',
    `descricao` TEXT DEFAULT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dt_atualizacao` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_chave` (`chave`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insere configurações padrão
INSERT INTO `configuracoes_financeiras` (`chave`, `valor`, `tipo`, `descricao`, `ativo`) VALUES
('multa_tipo', 'porcentagem', 'string', 'Tipo de cálculo de multa (fixo ou porcentagem)', 1),
('multa_valor', '2.00', 'decimal', 'Valor da multa (fixo em R$ ou porcentagem)', 1),
('juros_tipo', 'porcentagem', 'string', 'Tipo de cálculo de juros (fixo ou porcentagem)', 1),
('juros_valor', '0.33', 'decimal', 'Valor dos juros (fixo em R$ ou porcentagem ao mês)', 1),
('dias_carencia', '0', 'integer', 'Dias de carência antes de aplicar multa e juros', 1)
ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`);

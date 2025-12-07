-- Patch 001_0003
-- Cria tabela de relacionamento aluno_modalidades (N:N)
-- Permite associar múltiplas modalidades a um aluno

CREATE TABLE IF NOT EXISTS `aluno_modalidades` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `aluno_id` INT UNSIGNED NOT NULL,
    `modalidade_id` INT UNSIGNED NOT NULL,
    `preferencia` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Modalidade preferida do aluno',
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_aluno_modalidade` (`aluno_id`, `modalidade_id`),
    KEY `idx_aluno` (`aluno_id`),
    KEY `idx_modalidade` (`modalidade_id`),
    KEY `idx_preferencia` (`preferencia`),
    CONSTRAINT `fk_aluno_modalidades_aluno` FOREIGN KEY (`aluno_id`) 
        REFERENCES `alunos` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_aluno_modalidades_modalidade` FOREIGN KEY (`modalidade_id`) 
        REFERENCES `modalidades` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


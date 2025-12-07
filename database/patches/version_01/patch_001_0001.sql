-- ================================================================
-- PATCH 001_0001
-- Cria tabela de controle de versão de patches
-- Data: 2025-12-06
-- ================================================================

CREATE TABLE IF NOT EXISTS `db_patches` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `version` VARCHAR(20) NOT NULL,
    `description` TEXT NOT NULL,
    `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `execution_time` DECIMAL(10,4) DEFAULT NULL COMMENT 'Tempo de execução em segundos',
    `status` ENUM('success', 'failed') NOT NULL DEFAULT 'success',
    `error_message` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_version` (`version`),
    KEY `idx_applied_at` (`applied_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


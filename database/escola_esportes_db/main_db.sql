-- ================================================================
-- SISTEMA DE ESCOLA DE ESPORTES
-- Estrutura completa do banco de dados
-- MySQL 8.0+ com engine InnoDB
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS `escola_esportes_db` 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `escola_esportes_db`;

-- ================================================================
-- TABELA: usuarios
-- Usuários do sistema (Admin, Financeiro, Professor, Atendente)
-- ================================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `senha_hash` VARCHAR(255) NOT NULL,
    `perfil` ENUM('Admin', 'Financeiro', 'Professor', 'Atendente') NOT NULL DEFAULT 'Atendente',
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dt_atualizacao` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `idx_perfil` (`perfil`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: responsaveis
-- Responsáveis pelos alunos
-- ================================================================
CREATE TABLE IF NOT EXISTS `responsaveis` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `cpf` VARCHAR(14) NOT NULL,
    `contato` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dt_atualizacao` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cpf` (`cpf`),
    KEY `idx_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: alunos
-- Cadastro de alunos
-- ================================================================
CREATE TABLE IF NOT EXISTS `alunos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `nome_pai` VARCHAR(255) DEFAULT NULL,
    `telefone_pai` VARCHAR(20) DEFAULT NULL,
    `email_pai` VARCHAR(255) DEFAULT NULL,
    `telegram_pai` VARCHAR(100) DEFAULT NULL,
    `nome_mae` VARCHAR(255) DEFAULT NULL,
    `telefone_mae` VARCHAR(20) DEFAULT NULL,
    `email_mae` VARCHAR(255) DEFAULT NULL,
    `telegram_mae` VARCHAR(100) DEFAULT NULL,
    `cpf` VARCHAR(14) DEFAULT NULL,
    `rg` VARCHAR(20) DEFAULT NULL,
    `cpf_responsavel` VARCHAR(14) DEFAULT NULL,
    `responsavel_id` INT UNSIGNED DEFAULT NULL,
    `dt_nascimento` DATE NOT NULL,
    `sexo` ENUM('M', 'F', 'Outro') NOT NULL,
    `tipo_sanguineo` VARCHAR(5) DEFAULT NULL COMMENT 'A+, A-, B+, AB+, AB-, O+, O-',
    `alergias` TEXT DEFAULT NULL,
    `observacoes_medicas` TEXT DEFAULT NULL,
    `contato` VARCHAR(20) DEFAULT NULL,
    `contato_emergencia` VARCHAR(20) DEFAULT NULL,
    `nome_contato_emergencia` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `endereco` TEXT DEFAULT NULL,
    `status` ENUM('Ativo', 'Inativo', 'Suspenso', 'Cancelado') NOT NULL DEFAULT 'Ativo',
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dt_atualizacao` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cpf` (`cpf`),
    KEY `idx_rg` (`rg`),
    KEY `idx_status` (`status`),
    KEY `idx_responsavel` (`responsavel_id`),
    KEY `idx_nome` (`nome`),
    KEY `idx_tipo_sanguineo` (`tipo_sanguineo`),
    KEY `idx_email_pai` (`email_pai`),
    KEY `idx_email_mae` (`email_mae`),
    CONSTRAINT `fk_alunos_responsavel` FOREIGN KEY (`responsavel_id`) 
        REFERENCES `responsaveis` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: professores
-- Cadastro de professores
-- ================================================================
CREATE TABLE IF NOT EXISTS `professores` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `cpf` VARCHAR(14) NOT NULL,
    `registro_cref` VARCHAR(50) DEFAULT NULL,
    `contato` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `especialidade` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('Ativo', 'Inativo') NOT NULL DEFAULT 'Ativo',
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dt_atualizacao` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cpf` (`cpf`),
    KEY `idx_status` (`status`),
    KEY `idx_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: modalidades
-- Modalidades esportivas (Natação, Lutas, Futebol, etc.)
-- ================================================================
CREATE TABLE IF NOT EXISTS `modalidades` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `categoria_etaria` VARCHAR(100) DEFAULT NULL,
    `descricao` TEXT DEFAULT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_nome` (`nome`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: planos
-- Planos de mensalidade (mensal, trimestral, anual)
-- ================================================================
CREATE TABLE IF NOT EXISTS `planos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `periodicidade` ENUM('mensal', 'trimestral', 'anual') NOT NULL DEFAULT 'mensal',
    `valor_base` DECIMAL(10,2) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_periodicidade` (`periodicidade`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: turmas
-- Turmas e horários das aulas
-- ================================================================
CREATE TABLE IF NOT EXISTS `turmas` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `modalidade_id` INT UNSIGNED NOT NULL,
    `professor_id` INT UNSIGNED NOT NULL,
    `nome` VARCHAR(255) NOT NULL,
    `nivel` VARCHAR(100) DEFAULT NULL,
    `capacidade` INT UNSIGNED NOT NULL DEFAULT 20,
    `local` VARCHAR(255) DEFAULT NULL,
    `dias_da_semana` JSON NOT NULL COMMENT 'Array de dias: ["Segunda", "Quarta", "Sexta"]',
    `hora_inicio` TIME NOT NULL,
    `hora_fim` TIME NOT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_modalidade` (`modalidade_id`),
    KEY `idx_professor` (`professor_id`),
    KEY `idx_ativo` (`ativo`),
    CONSTRAINT `fk_turmas_modalidade` FOREIGN KEY (`modalidade_id`) 
        REFERENCES `modalidades` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_turmas_professor` FOREIGN KEY (`professor_id`) 
        REFERENCES `professores` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: matriculas
-- Matrículas de alunos em turmas
-- ================================================================
CREATE TABLE IF NOT EXISTS `matriculas` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `aluno_id` INT UNSIGNED NOT NULL,
    `turma_id` INT UNSIGNED NOT NULL,
    `plano_id` INT UNSIGNED NOT NULL,
    `dt_inicio` DATE NOT NULL,
    `dt_fim` DATE DEFAULT NULL,
    `status` ENUM('Ativa', 'Suspensa', 'Cancelada', 'Finalizada') NOT NULL DEFAULT 'Ativa',
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dt_atualizacao` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_aluno` (`aluno_id`),
    KEY `idx_turma` (`turma_id`),
    KEY `idx_plano` (`plano_id`),
    KEY `idx_status` (`status`),
    KEY `idx_dt_inicio` (`dt_inicio`),
    CONSTRAINT `fk_matriculas_aluno` FOREIGN KEY (`aluno_id`) 
        REFERENCES `alunos` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_matriculas_turma` FOREIGN KEY (`turma_id`) 
        REFERENCES `turmas` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_matriculas_plano` FOREIGN KEY (`plano_id`) 
        REFERENCES `planos` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: mensalidades
-- Mensalidades geradas para cada matrícula
-- ================================================================
CREATE TABLE IF NOT EXISTS `mensalidades` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `matricula_id` INT UNSIGNED NOT NULL,
    `competencia` VARCHAR(7) NOT NULL COMMENT 'Formato: YYYY-MM',
    `valor` DECIMAL(10,2) NOT NULL,
    `desconto` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `multa` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `juros` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `dt_vencimento` DATE NOT NULL,
    `status` ENUM('Aberto', 'Pago', 'Atrasado', 'Cancelado') NOT NULL DEFAULT 'Aberto',
    `dt_geracao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dt_atualizacao` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_matricula` (`matricula_id`),
    KEY `idx_competencia` (`competencia`),
    KEY `idx_status` (`status`),
    KEY `idx_dt_vencimento` (`dt_vencimento`),
    UNIQUE KEY `uk_matricula_competencia` (`matricula_id`, `competencia`),
    CONSTRAINT `fk_mensalidades_matricula` FOREIGN KEY (`matricula_id`) 
        REFERENCES `matriculas` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: pagamentos
-- Pagamentos realizados para mensalidades
-- ================================================================
CREATE TABLE IF NOT EXISTS `pagamentos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `mensalidade_id` INT UNSIGNED NOT NULL,
    `forma` ENUM('PIX', 'Cartão', 'Dinheiro', 'Boleto') NOT NULL,
    `valor_pago` DECIMAL(10,2) NOT NULL,
    `dt_pagamento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `transacao_ref` VARCHAR(255) DEFAULT NULL COMMENT 'Referência da transação (ex: código PIX)',
    `conciliado` TINYINT(1) NOT NULL DEFAULT 0,
    `observacoes` TEXT DEFAULT NULL,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_mensalidade` (`mensalidade_id`),
    KEY `idx_forma` (`forma`),
    KEY `idx_dt_pagamento` (`dt_pagamento`),
    KEY `idx_conciliado` (`conciliado`),
    KEY `idx_transacao_ref` (`transacao_ref`),
    CONSTRAINT `fk_pagamentos_mensalidade` FOREIGN KEY (`mensalidade_id`) 
        REFERENCES `mensalidades` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: presencas
-- Registro de presenças dos alunos
-- ================================================================
CREATE TABLE IF NOT EXISTS `presencas` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `matricula_id` INT UNSIGNED NOT NULL,
    `data` DATE NOT NULL,
    `status` ENUM('Presente', 'Falta', 'Reposição') NOT NULL DEFAULT 'Presente',
    `observacoes` TEXT DEFAULT NULL,
    `dt_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_matricula` (`matricula_id`),
    KEY `idx_data` (`data`),
    KEY `idx_status` (`status`),
    UNIQUE KEY `uk_matricula_data` (`matricula_id`, `data`),
    CONSTRAINT `fk_presencas_matricula` FOREIGN KEY (`matricula_id`) 
        REFERENCES `matriculas` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: avaliacoes
-- Avaliações dos alunos
-- ================================================================
CREATE TABLE IF NOT EXISTS `avaliacoes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `aluno_id` INT UNSIGNED NOT NULL,
    `modalidade_id` INT UNSIGNED NOT NULL,
    `data` DATE NOT NULL,
    `notas_json` JSON DEFAULT NULL COMMENT 'Estrutura JSON com as notas dos critérios',
    `observacoes` TEXT DEFAULT NULL,
    `dt_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_aluno` (`aluno_id`),
    KEY `idx_modalidade` (`modalidade_id`),
    KEY `idx_data` (`data`),
    CONSTRAINT `fk_avaliacoes_aluno` FOREIGN KEY (`aluno_id`) 
        REFERENCES `alunos` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_avaliacoes_modalidade` FOREIGN KEY (`modalidade_id`) 
        REFERENCES `modalidades` (`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: caixa_movimentos
-- Movimentações do caixa
-- ================================================================
CREATE TABLE IF NOT EXISTS `caixa_movimentos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo` ENUM('Receita', 'Despesa') NOT NULL,
    `origem` VARCHAR(100) NOT NULL COMMENT 'mensalidade, ajuste, etc.',
    `valor` DECIMAL(10,2) NOT NULL,
    `dt_movimento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `observacao` TEXT DEFAULT NULL,
    `usuario_id` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_tipo` (`tipo`),
    KEY `idx_origem` (`origem`),
    KEY `idx_dt_movimento` (`dt_movimento`),
    KEY `idx_usuario` (`usuario_id`),
    CONSTRAINT `fk_caixa_usuario` FOREIGN KEY (`usuario_id`) 
        REFERENCES `usuarios` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABELA: audit_logs
-- Log de auditoria do sistema
-- ================================================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED DEFAULT NULL,
    `entidade` VARCHAR(100) NOT NULL COMMENT 'Nome da tabela/entidade',
    `entidade_id` INT UNSIGNED DEFAULT NULL,
    `acao` ENUM('CREATE', 'UPDATE', 'DELETE', 'VIEW') NOT NULL,
    `antes_json` JSON DEFAULT NULL COMMENT 'Estado anterior do registro',
    `depois_json` JSON DEFAULT NULL COMMENT 'Estado posterior do registro',
    `dt_evento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_usuario` (`usuario_id`),
    KEY `idx_entidade` (`entidade`, `entidade_id`),
    KEY `idx_acao` (`acao`),
    KEY `idx_dt_evento` (`dt_evento`),
    CONSTRAINT `fk_audit_usuario` FOREIGN KEY (`usuario_id`) 
        REFERENCES `usuarios` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- INSERÇÃO DE DADOS INICIAIS
-- ================================================================

-- Usuário administrador padrão (senha: admin123)
-- Senha hash usando password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO `usuarios` (`nome`, `email`, `senha_hash`, `perfil`, `ativo`) VALUES
('Administrador', 'admin@escolaesportes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 1);

-- Modalidades de exemplo
INSERT INTO `modalidades` (`nome`, `categoria_etaria`, `descricao`, `ativo`) VALUES
('Natação', 'Todas as idades', 'Aulas de natação para todas as idades', 1),
('Futebol', 'Infantil e Juvenil', 'Escolinha de futebol', 1),
('Lutas', 'Juvenil e Adulto', 'Aulas de artes marciais', 1);

-- Planos de exemplo
INSERT INTO `planos` (`nome`, `periodicidade`, `valor_base`, `descricao`, `ativo`) VALUES
('Plano Mensal', 'mensal', 150.00, 'Mensalidade mensal', 1),
('Plano Trimestral', 'trimestral', 400.00, 'Plano com desconto para 3 meses', 1),
('Plano Anual', 'anual', 1500.00, 'Plano com maior desconto para 12 meses', 1);


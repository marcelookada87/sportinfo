-- Patch 001_0004
-- Adiciona campos de contato dos pais (telefone, email, telegram)

ALTER TABLE alunos
ADD COLUMN telefone_pai VARCHAR(20) DEFAULT NULL AFTER nome_pai,
ADD COLUMN email_pai VARCHAR(255) DEFAULT NULL AFTER telefone_pai,
ADD COLUMN telegram_pai VARCHAR(100) DEFAULT NULL AFTER email_pai,
ADD COLUMN telefone_mae VARCHAR(20) DEFAULT NULL AFTER nome_mae,
ADD COLUMN email_mae VARCHAR(255) DEFAULT NULL AFTER telefone_mae,
ADD COLUMN telegram_mae VARCHAR(100) DEFAULT NULL AFTER email_mae,
ADD INDEX idx_email_pai (email_pai),
ADD INDEX idx_email_mae (email_mae);


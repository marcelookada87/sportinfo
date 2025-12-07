-- Patch 001_0002
-- Adiciona campos adicionais na tabela alunos

ALTER TABLE alunos
ADD COLUMN nome_pai VARCHAR(255) DEFAULT NULL AFTER nome,
ADD COLUMN nome_mae VARCHAR(255) DEFAULT NULL AFTER nome_pai,
ADD COLUMN rg VARCHAR(20) DEFAULT NULL AFTER cpf,
ADD COLUMN tipo_sanguineo VARCHAR(5) DEFAULT NULL COMMENT 'A+, A-, B+, AB+, AB-, O+, O-' AFTER sexo,
ADD COLUMN alergias TEXT DEFAULT NULL AFTER tipo_sanguineo,
ADD COLUMN observacoes_medicas TEXT DEFAULT NULL AFTER alergias,
ADD COLUMN contato_emergencia VARCHAR(20) DEFAULT NULL AFTER contato,
ADD COLUMN nome_contato_emergencia VARCHAR(255) DEFAULT NULL AFTER contato_emergencia,
ADD INDEX idx_rg (rg),
ADD INDEX idx_tipo_sanguineo (tipo_sanguineo);


-- Patch 001_0005
-- Expande tabela professores com campos profissionais detalhados

ALTER TABLE professores
ADD COLUMN rg VARCHAR(20) DEFAULT NULL AFTER cpf,
ADD COLUMN dt_nascimento DATE DEFAULT NULL AFTER rg,
ADD COLUMN sexo ENUM('M', 'F', 'Outro') DEFAULT NULL AFTER dt_nascimento,
ADD COLUMN endereco TEXT DEFAULT NULL AFTER email,
ADD COLUMN formacao_academica TEXT DEFAULT NULL COMMENT 'Formação acadêmica do professor' AFTER endereco,
ADD COLUMN certificacoes TEXT DEFAULT NULL COMMENT 'Certificações e qualificações' AFTER formacao_academica,
ADD COLUMN experiencia_profissional TEXT DEFAULT NULL COMMENT 'Experiência profissional' AFTER certificacoes,
ADD COLUMN valor_hora DECIMAL(10,2) DEFAULT NULL COMMENT 'Valor por hora de aula' AFTER experiencia_profissional,
ADD COLUMN banco_nome VARCHAR(255) DEFAULT NULL COMMENT 'Nome do banco' AFTER valor_hora,
ADD COLUMN banco_agencia VARCHAR(20) DEFAULT NULL COMMENT 'Agência bancária' AFTER banco_nome,
ADD COLUMN banco_conta VARCHAR(50) DEFAULT NULL COMMENT 'Conta bancária' AFTER banco_agencia,
ADD COLUMN banco_tipo_conta ENUM('Corrente', 'Poupança') DEFAULT NULL COMMENT 'Tipo de conta' AFTER banco_conta,
ADD COLUMN banco_pix VARCHAR(255) DEFAULT NULL COMMENT 'Chave PIX' AFTER banco_tipo_conta,
ADD COLUMN contato_emergencia VARCHAR(20) DEFAULT NULL COMMENT 'Contato de emergência' AFTER banco_pix,
ADD COLUMN nome_contato_emergencia VARCHAR(255) DEFAULT NULL COMMENT 'Nome do contato de emergência' AFTER contato_emergencia,
ADD COLUMN observacoes TEXT DEFAULT NULL COMMENT 'Observações gerais' AFTER nome_contato_emergencia,
ADD INDEX idx_rg (rg),
ADD INDEX idx_dt_nascimento (dt_nascimento),
ADD INDEX idx_sexo (sexo);

-- Cria tabela para relacionamento N:N entre professores e modalidades
CREATE TABLE IF NOT EXISTS professor_modalidades (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    professor_id INT UNSIGNED NOT NULL,
    modalidade_id INT UNSIGNED NOT NULL,
    dt_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_professor_modalidade (professor_id, modalidade_id),
    KEY idx_professor (professor_id),
    KEY idx_modalidade (modalidade_id),
    CONSTRAINT fk_professor_modalidades_professor FOREIGN KEY (professor_id) 
        REFERENCES professores (id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_professor_modalidades_modalidade FOREIGN KEY (modalidade_id) 
        REFERENCES modalidades (id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


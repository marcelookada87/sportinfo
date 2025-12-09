-- Patch 001_0006
-- Adiciona campo quantidade_meses na tabela planos
-- Permite definir duração do plano em meses

ALTER TABLE `planos` 
ADD COLUMN `quantidade_meses` INT UNSIGNED NOT NULL DEFAULT 1 
COMMENT 'Quantidade de meses de duração do plano' 
AFTER `periodicidade`;

-- Atualiza planos existentes baseado na periodicidade
UPDATE `planos` SET `quantidade_meses` = 1 WHERE `periodicidade` = 'mensal';
UPDATE `planos` SET `quantidade_meses` = 3 WHERE `periodicidade` = 'trimestral';
UPDATE `planos` SET `quantidade_meses` = 12 WHERE `periodicidade` = 'anual';


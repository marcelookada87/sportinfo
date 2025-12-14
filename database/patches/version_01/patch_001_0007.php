<?php

declare(strict_types=1);

/**
 * Patch 001_0007
 * Cria tabela configuracoes_financeiras para gerenciar multa e juros
 * 
 * Data: 2025-01-27
 * Descrição: Cria tabela para armazenar configurações de multa e juros
 * que podem ser calculados por valor fixo ou porcentagem.
 */

return [
    'version' => '001_0007',
    'description' => 'Cria tabela configuracoes_financeiras para multa e juros',
    'date' => '2025-01-27',
    'sql_file' => __DIR__ . '/patch_001_0007.sql',
    'execute' => function(PDO $pdo): bool {
        // Verifica se a tabela já existe
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'configuracoes_financeiras'");
            if ($stmt->rowCount() > 0) {
                // Tabela já existe, patch já foi aplicado
                return true;
            }
        } catch (PDOException $e) {
            // Continua para criar a tabela
        }

        // Lê e executa o arquivo SQL
        $sqlFile = __DIR__ . '/patch_001_0007.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("Arquivo SQL não encontrado: {$sqlFile}");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Remove comentários e divide em comandos
        $lines = explode("\n", $sql);
        $cleanSql = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            // Remove linhas de comentário
            if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
                continue;
            }
            $cleanSql .= $line . "\n";
        }
        
        // Divide por ponto e vírgula
        $statements = explode(';', $cleanSql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Se a tabela já existe, não é erro crítico
                    if (strpos($e->getMessage(), 'Duplicate table') === false &&
                        strpos($e->getMessage(), 'already exists') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        // Insere configurações padrão
        try {
            $stmt = $pdo->prepare("
                INSERT INTO configuracoes_financeiras (chave, valor, tipo, descricao, ativo) 
                VALUES 
                    ('multa_tipo', 'porcentagem', 'string', 'Tipo de cálculo de multa (fixo ou porcentagem)', 1),
                    ('multa_valor', '2.00', 'decimal', 'Valor da multa (fixo em R$ ou porcentagem)', 1),
                    ('juros_tipo', 'porcentagem', 'string', 'Tipo de cálculo de juros (fixo ou porcentagem)', 1),
                    ('juros_valor', '0.33', 'decimal', 'Valor dos juros (fixo em R$ ou porcentagem ao mês)', 1),
                    ('dias_carencia', '0', 'integer', 'Dias de carência antes de aplicar multa e juros', 1)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)
            ");
            $stmt->execute();
        } catch (PDOException $e) {
            // Ignora se já existir
        }
        
        return true;
    }
];

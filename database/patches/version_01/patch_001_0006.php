<?php

declare(strict_types=1);

/**
 * Patch 001_0006
 * Adiciona campo quantidade_meses na tabela planos
 * 
 * Data: 2025-12-06
 * Descrição: Adiciona campo quantidade_meses na tabela planos para definir
 * a duração do plano em meses, permitindo cálculo automático da data de término
 * nas matrículas.
 */

return [
    'version' => '001_0006',
    'description' => 'Adiciona campo quantidade_meses na tabela planos',
    'date' => '2025-12-06',
    'sql_file' => __DIR__ . '/patch_001_0006.sql',
    'execute' => function(PDO $pdo): bool {
        // Verifica se a coluna já existe
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM planos LIKE 'quantidade_meses'");
            if ($stmt->rowCount() > 0) {
                // Coluna já existe, patch já foi aplicado
                return true;
            }
        } catch (PDOException $e) {
            // Tabela pode não existir ainda, continua
        }

        // Lê e executa o arquivo SQL
        $sqlFile = __DIR__ . '/patch_001_0006.sql';
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
                    // Se a coluna já existe, não é erro crítico
                    if (strpos($e->getMessage(), 'Duplicate column name') === false &&
                        strpos($e->getMessage(), 'already exists') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        return true;
    }
];


<?php

declare(strict_types=1);

/**
 * Patch 001_0002
 * Adiciona campos adicionais na tabela alunos
 * 
 * Data: 2025-12-06
 * Descrição: Adiciona campos para informações completas do aluno (pai, mãe, RG, tipo sanguíneo, alergias, etc.)
 */

return [
    'version' => '001_0002',
    'description' => 'Adiciona campos adicionais na tabela alunos (pai, mãe, RG, tipo sanguíneo, alergias, etc.)',
    'date' => '2025-12-06',
    'sql_file' => __DIR__ . '/patch_001_0002.sql',
    'execute' => function(PDO $pdo): bool {
        // Lê e executa o arquivo SQL
        $sqlFile = __DIR__ . '/patch_001_0002.sql';
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
                    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        return true;
    }
];


<?php

declare(strict_types=1);

/**
 * Patch 001_0003
 * Cria tabela de relacionamento aluno_modalidades (N:N)
 * 
 * Data: 2025-12-06
 * Descrição: Cria tabela para relacionar alunos com modalidades,
 * permitindo que um aluno tenha múltiplas modalidades associadas.
 */

return [
    'version' => '001_0003',
    'description' => 'Cria tabela aluno_modalidades para relacionamento N:N entre alunos e modalidades',
    'date' => '2025-12-06',
    'sql_file' => __DIR__ . '/patch_001_0003.sql',
    'execute' => function(PDO $pdo): bool {
        // Lê e executa o arquivo SQL
        $sqlFile = __DIR__ . '/patch_001_0003.sql';
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
                    if (strpos($e->getMessage(), 'already exists') === false && 
                        strpos($e->getMessage(), 'Duplicate') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        return true;
    }
];


<?php

declare(strict_types=1);

/**
 * Patch 001_0001
 * Cria tabela de controle de versão de patches
 * 
 * Data: 2025-12-06
 * Descrição: Cria tabela db_patches para controlar patches aplicados
 */

return [
    'version' => '001_0001',
    'description' => 'Cria tabela de controle de versão de patches',
    'date' => '2025-12-06',
    'sql_file' => __DIR__ . '/patch_001_0001.sql',
    'execute' => function(PDO $pdo): bool {
        // Lê e executa o arquivo SQL
        $sqlFile = __DIR__ . '/patch_001_0001.sql';
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
                    // Se a tabela já existe, não é erro
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        // Verifica se a tabela foi criada
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'db_patches'");
            if ($stmt->rowCount() === 0) {
                throw new Exception("Tabela db_patches não foi criada");
            }
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar criação da tabela: " . $e->getMessage());
        }
        
        return true;
    }
];


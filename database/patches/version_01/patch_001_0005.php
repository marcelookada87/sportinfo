<?php

declare(strict_types=1);

/**
 * Patch 001_0005
 * Expande tabela professores com campos profissionais detalhados
 * 
 * Data: 2025-12-06
 * Descrição: Adiciona campos profissionais completos para professores:
 * RG, data nascimento, sexo, endereço, formação, certificações,
 * experiência, especialidades, dados bancários, contato emergência, etc.
 */

return [
    'version' => '001_0005',
    'description' => 'Expande tabela professores com campos profissionais detalhados',
    'date' => '2025-12-06',
    'sql_file' => __DIR__ . '/patch_001_0005.sql',
    'execute' => function(PDO $pdo): bool {
        // Lê e executa o arquivo SQL
        $sqlFile = __DIR__ . '/patch_001_0005.sql';
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


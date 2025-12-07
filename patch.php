<?php

declare(strict_types=1);

/**
 * Sistema de Aplicação de Patches do Banco de Dados
 * Acesse: http://localhost/mensalidade/patch.php
 */

// Carrega configurações
require_once __DIR__ . '/config/config.php';

// Carrega autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/app/autoload.php';
}

use App\Core\Model;

// Configurações
$patchesDir = ROOT_PATH . '/database/patches';
$versionDirs = ['version_01']; // Adicione novas versões aqui

// Função para obter conexão PDO
function getConnection(): PDO
{
    static $connection = null;
    
    if ($connection === null) {
        $config = require ROOT_PATH . '/config/database.php';
        
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            die('Erro ao conectar com o banco de dados: ' . $e->getMessage());
        }
    }

    return $connection;
}

// Função para obter patches aplicados
function getAppliedPatches(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT version FROM db_patches WHERE status = 'success'");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        // Tabela ainda não existe, retorna array vazio
        return [];
    }
}

// Função para descobrir patches disponíveis
function discoverPatches(string $patchesDir, array $versionDirs): array
{
    $patches = [];
    
    foreach ($versionDirs as $versionDir) {
        $versionPath = $patchesDir . '/' . $versionDir;
        
        if (!is_dir($versionPath)) {
            continue;
        }
        
        $files = glob($versionPath . '/patch_*.php');
        
        foreach ($files as $file) {
            $patch = require $file;
            if (is_array($patch) && isset($patch['version'])) {
                $patches[] = [
                    'file' => $file,
                    'version' => $patch['version'],
                    'description' => $patch['description'] ?? '',
                    'date' => $patch['date'] ?? '',
                    'execute' => $patch['execute'] ?? null
                ];
            }
        }
    }
    
    // Ordena por versão
    usort($patches, fn($a, $b) => strcmp($a['version'], $b['version']));
    
    return $patches;
}

// Processa aplicação de patches
$message = '';
$messageType = '';
$appliedPatches = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_patches'])) {
    try {
        $pdo = getConnection();
        $applied = getAppliedPatches($pdo);
        $available = discoverPatches($patchesDir, $versionDirs);
        
        $toApply = array_filter($available, fn($p) => !in_array($p['version'], $applied));
        
        if (empty($toApply)) {
            $message = 'Nenhum patch pendente para aplicar.';
            $messageType = 'info';
        } else {
            // Inicia transação
            $transactionStarted = false;
            if (!$pdo->inTransaction()) {
                $pdo->beginTransaction();
                $transactionStarted = true;
            }
            
            try {
                foreach ($toApply as $patch) {
                    $startTime = microtime(true);
                    
                    try {
                        if ($patch['execute'] && is_callable($patch['execute'])) {
                            $result = $patch['execute']($pdo);
                            
                            if (!$result) {
                                throw new Exception("Falha ao executar patch {$patch['version']}");
                            }
                        } else {
                            // Executa SQL diretamente se não houver função execute
                            $sqlFile = dirname($patch['file']) . '/patch_' . $patch['version'] . '.sql';
                            if (file_exists($sqlFile)) {
                                $sql = file_get_contents($sqlFile);
                                $statements = array_filter(
                                    array_map('trim', explode(';', $sql)),
                                    fn($stmt) => !empty($stmt) && !preg_match('/^--/', $stmt) && !preg_match('/^\/\*/', $stmt)
                                );
                                
                                foreach ($statements as $statement) {
                                    $statement = trim($statement);
                                    if (!empty($statement)) {
                                        $pdo->exec($statement);
                                    }
                                }
                            } else {
                                throw new Exception("Arquivo SQL não encontrado para patch {$patch['version']}");
                            }
                        }
                        
                        $appliedPatches[] = $patch['version'];
                    } catch (Exception $e) {
                        throw new Exception("Erro ao executar patch {$patch['version']}: " . $e->getMessage());
                    }
                }
                
                // Faz commit apenas se iniciou a transação aqui
                if ($transactionStarted && $pdo->inTransaction()) {
                    $pdo->commit();
                }
                
                // Aguarda um pouco para garantir que a transação foi commitada
                usleep(100000); // 0.1 segundo
                
                // Registra patches aplicados (após commit, para garantir que a tabela existe)
                foreach ($appliedPatches as $version) {
                    $patchInfo = array_filter($toApply, fn($p) => $p['version'] === $version);
                    if (!empty($patchInfo)) {
                        $patchInfo = reset($patchInfo);
                        
                        // Tenta várias vezes registrar o patch (para o primeiro patch que cria a tabela)
                        $maxTries = 3;
                        $registered = false;
                        
                        for ($i = 0; $i < $maxTries; $i++) {
                            try {
                                $stmt = $pdo->prepare("
                                    INSERT INTO db_patches (version, description, applied_at) 
                                    VALUES (:version, :description, NOW())
                                    ON DUPLICATE KEY UPDATE applied_at = NOW()
                                ");
                                $stmt->execute([
                                    'version' => $version,
                                    'description' => $patchInfo['description']
                                ]);
                                $registered = true;
                                break;
                            } catch (PDOException $e) {
                                if ($i < $maxTries - 1) {
                                    usleep(200000); // Aguarda 0.2 segundos antes de tentar novamente
                                } else {
                                    // Se ainda não conseguir, tenta criar a tabela manualmente
                                    try {
                                        $pdo->exec("
                                            CREATE TABLE IF NOT EXISTS `db_patches` (
                                                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                                `version` VARCHAR(20) NOT NULL,
                                                `description` TEXT NOT NULL,
                                                `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                                `execution_time` DECIMAL(10,4) DEFAULT NULL,
                                                `status` ENUM('success', 'failed') NOT NULL DEFAULT 'success',
                                                `error_message` TEXT DEFAULT NULL,
                                                PRIMARY KEY (`id`),
                                                UNIQUE KEY `uk_version` (`version`),
                                                KEY `idx_applied_at` (`applied_at`)
                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                                        ");
                                        
                                        // Tenta registrar novamente
                                        $stmt = $pdo->prepare("
                                            INSERT INTO db_patches (version, description, applied_at) 
                                            VALUES (:version, :description, NOW())
                                        ");
                                        $stmt->execute([
                                            'version' => $version,
                                            'description' => $patchInfo['description']
                                        ]);
                                        $registered = true;
                                    } catch (PDOException $e2) {
                                        // Se ainda falhar, continua sem registrar
                                    }
                                }
                            }
                        }
                    }
                }
                
                $message = count($appliedPatches) . ' patch(es) aplicado(s) com sucesso!';
                $messageType = 'success';
            } catch (Exception $e) {
                // Só faz rollback se iniciou a transação aqui e ainda está ativa
                if (isset($transactionStarted) && $transactionStarted && $pdo->inTransaction()) {
                    try {
                        $pdo->rollBack();
                    } catch (PDOException $rollbackError) {
                        // Ignora erro de rollback
                    }
                }
                throw $e;
            }
        }
    } catch (Exception $e) {
        $message = 'Erro ao aplicar patches: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Obtém informações para exibição
try {
    $pdo = getConnection();
    $applied = getAppliedPatches($pdo);
    $available = discoverPatches($patchesDir, $versionDirs);
    
    $pending = array_filter($available, fn($p) => !in_array($p['version'], $applied));
    $appliedList = array_filter($available, fn($p) => in_array($p['version'], $applied));
} catch (Exception $e) {
    $available = [];
    $pending = [];
    $appliedList = [];
    $message = 'Erro ao conectar com o banco: ' . $e->getMessage();
    $messageType = 'error';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Patches - Banco de Dados</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/forms.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        .patch-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 2rem;
        }
        .patch-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .patch-header h1 {
            color: #2563eb;
            margin-bottom: 0.5rem;
        }
        .patch-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-box {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
            border-left: 4px solid #2563eb;
        }
        .stat-box.success {
            border-left-color: #10b981;
        }
        .stat-box.warning {
            border-left-color: #f59e0b;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        .patch-section {
            margin-bottom: 2rem;
        }
        .patch-section h2 {
            color: #1f2937;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        .patch-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .patch-item {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #2563eb;
        }
        .patch-item.applied {
            border-left-color: #10b981;
            opacity: 0.7;
        }
        .patch-item.pending {
            border-left-color: #f59e0b;
        }
        .patch-info {
            flex: 1;
        }
        .patch-version {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        .patch-description {
            color: #6b7280;
            font-size: 0.875rem;
        }
        .patch-date {
            color: #9ca3af;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        .patch-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .patch-badge.applied {
            background: #d1fae5;
            color: #065f46;
        }
        .patch-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }
        .btn-apply {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-apply:hover {
            transform: translateY(-2px);
        }
        .btn-apply:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .alert.info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #2563eb;
        }
    </style>
</head>
<body>
    <div class="patch-container">
        <div class="patch-header">
            <h1>🔧 Sistema de Patches do Banco de Dados</h1>
            <p>Gerencie e aplique atualizações do banco de dados</p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $messageType ?>">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="patch-stats">
            <div class="stat-box">
                <div class="stat-value"><?= count($available) ?></div>
                <div class="stat-label">Total de Patches</div>
            </div>
            <div class="stat-box success">
                <div class="stat-value"><?= count($appliedList) ?></div>
                <div class="stat-label">Patches Aplicados</div>
            </div>
            <div class="stat-box warning">
                <div class="stat-value"><?= count($pending) ?></div>
                <div class="stat-label">Patches Pendentes</div>
            </div>
        </div>

        <?php if (!empty($pending)): ?>
            <div class="patch-section">
                <h2>📋 Patches Pendentes</h2>
                <ul class="patch-list">
                    <?php foreach ($pending as $patch): ?>
                        <li class="patch-item pending">
                            <div class="patch-info">
                                <div class="patch-version">Patch <?= htmlspecialchars($patch['version'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="patch-description"><?= htmlspecialchars($patch['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if ($patch['date']): ?>
                                    <div class="patch-date">Data: <?= htmlspecialchars($patch['date'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <span class="patch-badge pending">Pendente</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <form method="POST" onsubmit="return confirm('Deseja realmente aplicar os patches pendentes?');">
                <button type="submit" name="apply_patches" class="btn-apply">
                    ✅ Aplicar Todos os Patches Pendentes
                </button>
            </form>
        <?php else: ?>
            <div class="alert info">
                ✅ Todos os patches foram aplicados! O banco de dados está atualizado.
            </div>
        <?php endif; ?>

        <?php if (!empty($appliedList)): ?>
            <div class="patch-section">
                <h2>✅ Patches Aplicados</h2>
                <ul class="patch-list">
                    <?php foreach ($appliedList as $patch): ?>
                        <li class="patch-item applied">
                            <div class="patch-info">
                                <div class="patch-version">Patch <?= htmlspecialchars($patch['version'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="patch-description"><?= htmlspecialchars($patch['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if ($patch['date']): ?>
                                    <div class="patch-date">Data: <?= htmlspecialchars($patch['date'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <span class="patch-badge applied">Aplicado</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>


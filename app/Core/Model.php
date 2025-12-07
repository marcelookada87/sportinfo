<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe base Model
 * Gerencia conexão com banco de dados e operações CRUD básicas
 */
abstract class Model
{
    protected static ?PDO $connection = null;
    protected string $table;
    protected string $primaryKey = 'id';

    /**
     * Obtém conexão PDO (Singleton)
     */
    protected static function getConnection(): PDO
    {
        if (self::$connection === null) {
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
                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                throw new \RuntimeException('Erro ao conectar com o banco de dados');
            }
        }

        return self::$connection;
    }

    /**
     * Inicia transação
     */
    protected function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Confirma transação
     */
    protected function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Reverte transação
     */
    protected function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Busca por ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Busca todos os registros
     */
    public function all(array $conditions = [], string $orderBy = '', int $limit = 0): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Insere novo registro
     */
    public function create(array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($field) => ":{$field}", $fields);
        
        $sql = sprintf(
            "INSERT INTO {$this->table} (%s) VALUES (%s)",
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($data);
        
        return (int) self::getConnection()->lastInsertId();
    }

    /**
     * Atualiza registro
     */
    public function update(int $id, array $data): bool
    {
        $fields = array_map(fn($field) => "{$field} = :{$field}", array_keys($data));
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        
        $data['id'] = $id;
        $stmt = self::getConnection()->prepare($sql);
        
        return $stmt->execute($data);
    }

    /**
     * Deleta registro
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = self::getConnection()->prepare($sql);
        
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Executa query customizada
     */
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        
        return $stmt;
    }
}


<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Usuário
 */
class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected string $primaryKey = 'id';

    /**
     * Busca usuário por email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND ativo = 1 LIMIT 1";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Verifica credenciais
     */
    public function verifyCredentials(string $email, string $password): ?array
    {
        $usuario = $this->findByEmail($email);
        
        if (!$usuario) {
            return null;
        }
        
        if (!password_verify($password, $usuario['senha_hash'])) {
            return null;
        }
        
        return $usuario;
    }

    /**
     * Atualiza última atividade
     */
    public function updateLastActivity(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET dt_atualizacao = NOW() WHERE id = :id";
        $stmt = self::getConnection()->prepare($sql);
        
        return $stmt->execute(['id' => $id]);
    }
}


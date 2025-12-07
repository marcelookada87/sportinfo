<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Modalidade
 */
class Modalidade extends Model
{
    protected string $table = 'modalidades';
    protected string $primaryKey = 'id';

    /**
     * Busca todas as modalidades ativas
     */
    public function findAllAtivas(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca modalidades de um aluno
     */
    public function findByAluno(int $alunoId): array
    {
        $sql = "SELECT m.*, am.preferencia 
                FROM {$this->table} m
                INNER JOIN aluno_modalidades am ON m.id = am.modalidade_id
                WHERE am.aluno_id = :aluno_id
                ORDER BY am.preferencia DESC, m.nome ASC";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['aluno_id' => $alunoId]);
        
        return $stmt->fetchAll() ?: [];
    }
}


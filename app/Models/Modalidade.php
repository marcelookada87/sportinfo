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
     * Busca modalidades com filtros
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (nome LIKE :search_nome OR categoria_etaria LIKE :search_categoria OR descricao LIKE :search_descricao)";
            $params['search_nome'] = $searchTerm;
            $params['search_categoria'] = $searchTerm;
            $params['search_descricao'] = $searchTerm;
        }

        // Filtro de status (ativo/inativo)
        if (isset($filters['ativo']) && $filters['ativo'] !== '' && trim($filters['ativo']) !== '') {
            $sql .= " AND ativo = :ativo";
            $params['ativo'] = (int)trim($filters['ativo']);
        }

        $sql .= " ORDER BY nome ASC";
        
        $limit = isset($filters['limit']) && $filters['limit'] > 0 ? (int)$filters['limit'] : 20;
        $sql .= " LIMIT {$limit}";
        
        $offset = isset($filters['offset']) && $filters['offset'] >= 0 ? (int)$filters['offset'] : 0;
        if ($offset > 0) {
            $sql .= " OFFSET {$offset}";
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll() ?: [];
        } catch (\PDOException $e) {
            error_log("Erro ao buscar modalidades: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de modalidades com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (nome LIKE :search_nome OR categoria_etaria LIKE :search_categoria OR descricao LIKE :search_descricao)";
            $params['search_nome'] = $searchTerm;
            $params['search_categoria'] = $searchTerm;
            $params['search_descricao'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['ativo']) && $filters['ativo'] !== '' && trim($filters['ativo']) !== '') {
            $sql .= " AND ativo = :ativo";
            $params['ativo'] = (int)trim($filters['ativo']);
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Erro ao contar modalidades: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
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

    /**
     * Busca modalidades de um professor
     */
    public function findByProfessor(int $professorId): array
    {
        $sql = "SELECT m.* 
                FROM {$this->table} m
                INNER JOIN professor_modalidades pm ON m.id = pm.modalidade_id
                WHERE pm.professor_id = :professor_id
                ORDER BY m.nome ASC";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['professor_id' => $professorId]);
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Verifica se modalidade está sendo usada em turmas ativas
     */
    public function isUsedInTurmas(int $modalidadeId): bool
    {
        $sql = "SELECT COUNT(*) as total FROM turmas WHERE modalidade_id = :modalidade_id AND ativo = 1";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['modalidade_id' => $modalidadeId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0) > 0;
    }

    /**
     * Conta quantos alunos estão matriculados nesta modalidade
     */
    public function countAlunos(int $modalidadeId): int
    {
        $sql = "SELECT COUNT(DISTINCT m.aluno_id) as total 
                FROM matriculas m
                INNER JOIN turmas t ON m.turma_id = t.id
                WHERE t.modalidade_id = :modalidade_id 
                AND m.status = 'Ativa'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['modalidade_id' => $modalidadeId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Conta quantas turmas ativas existem para esta modalidade
     */
    public function countTurmas(int $modalidadeId): int
    {
        $sql = "SELECT COUNT(*) as total FROM turmas WHERE modalidade_id = :modalidade_id AND ativo = 1";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['modalidade_id' => $modalidadeId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }
}


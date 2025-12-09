<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Professor
 */
class Professor extends Model
{
    protected string $table = 'professores';
    protected string $primaryKey = 'id';

    /**
     * Busca professores com filtros
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (nome LIKE :search_nome OR cpf LIKE :search_cpf OR email LIKE :search_email OR registro_cref LIKE :search_cref)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_email'] = $searchTerm;
            $params['search_cref'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND status = :status";
            $params['status'] = trim($filters['status']);
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
            error_log("Erro ao buscar professores: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de professores com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (nome LIKE :search_nome OR cpf LIKE :search_cpf OR email LIKE :search_email OR registro_cref LIKE :search_cref)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_email'] = $searchTerm;
            $params['search_cref'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND status = :status";
            $params['status'] = trim($filters['status']);
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Erro ao contar professores: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Busca por CPF
     */
    public function findByCpf(string $cpf): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE cpf = :cpf LIMIT 1";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['cpf' => $cpf]);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Calcula idade do professor
     */
    public function calcularIdade(?string $dtNascimento): ?int
    {
        if (empty($dtNascimento)) {
            return null;
        }
        
        $nascimento = new \DateTime($dtNascimento);
        $hoje = new \DateTime();
        $idade = $hoje->diff($nascimento);
        
        return (int)$idade->y;
    }

    /**
     * Adiciona modalidades ao professor
     */
    public function adicionarModalidades(int $professorId, array $modalidadeIds): bool
    {
        // Remove modalidades existentes
        $this->removerModalidades($professorId);

        if (empty($modalidadeIds)) {
            return true;
        }

        $sql = "INSERT INTO professor_modalidades (professor_id, modalidade_id) VALUES ";
        $values = [];
        $params = [];

        foreach ($modalidadeIds as $index => $modalidadeId) {
            $values[] = "(:professor_id_{$index}, :modalidade_id_{$index})";
            $params["professor_id_{$index}"] = $professorId;
            $params["modalidade_id_{$index}"] = (int)$modalidadeId;
        }

        $sql .= implode(', ', $values);

        $stmt = self::getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Remove todas as modalidades do professor
     */
    public function removerModalidades(int $professorId): bool
    {
        $sql = "DELETE FROM professor_modalidades WHERE professor_id = :professor_id";
        $stmt = self::getConnection()->prepare($sql);
        return $stmt->execute(['professor_id' => $professorId]);
    }

    /**
     * Busca IDs das modalidades do professor
     */
    public function getModalidadeIds(int $professorId): array
    {
        $sql = "SELECT modalidade_id FROM professor_modalidades WHERE professor_id = :professor_id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['professor_id' => $professorId]);
        
        $result = $stmt->fetchAll();
        return array_column($result, 'modalidade_id');
    }
}


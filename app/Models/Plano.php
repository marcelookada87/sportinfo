<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Plano
 */
class Plano extends Model
{
    protected string $table = 'planos';
    protected string $primaryKey = 'id';

    /**
     * Busca todos os planos ativos
     */
    public function findAllAtivos(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca planos com filtros
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (nome LIKE :search_nome OR descricao LIKE :search_descricao)";
            $params['search_nome'] = $searchTerm;
            $params['search_descricao'] = $searchTerm;
        }

        // Filtro de periodicidade
        if (isset($filters['periodicidade']) && $filters['periodicidade'] !== '' && trim($filters['periodicidade']) !== '') {
            $sql .= " AND periodicidade = :periodicidade";
            $params['periodicidade'] = trim($filters['periodicidade']);
        }

        // Filtro de status (ativo/inativo)
        if (isset($filters['ativo']) && $filters['ativo'] !== '' && trim($filters['ativo']) !== '') {
            $sql .= " AND ativo = :ativo";
            $params['ativo'] = (int)trim($filters['ativo']);
        }

        $sql .= " ORDER BY periodicidade ASC, nome ASC";
        
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
            error_log("Erro ao buscar planos: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de planos com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (nome LIKE :search_nome OR descricao LIKE :search_descricao)";
            $params['search_nome'] = $searchTerm;
            $params['search_descricao'] = $searchTerm;
        }

        // Filtro de periodicidade
        if (isset($filters['periodicidade']) && $filters['periodicidade'] !== '' && trim($filters['periodicidade']) !== '') {
            $sql .= " AND periodicidade = :periodicidade";
            $params['periodicidade'] = trim($filters['periodicidade']);
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
            error_log("Erro ao contar planos: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Verifica se plano está sendo usado em matrículas ativas
     */
    public function isUsedInMatriculas(int $planoId): bool
    {
        $sql = "SELECT COUNT(*) as total FROM matriculas WHERE plano_id = :plano_id AND status = 'Ativa'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['plano_id' => $planoId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0) > 0;
    }

    /**
     * Conta quantas matrículas ativas usam este plano
     */
    public function countMatriculasAtivas(int $planoId): int
    {
        $sql = "SELECT COUNT(*) as total FROM matriculas WHERE plano_id = :plano_id AND status = 'Ativa'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['plano_id' => $planoId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Conta total de matrículas (ativas e inativas) usando este plano
     */
    public function countTotalMatriculas(int $planoId): int
    {
        $sql = "SELECT COUNT(*) as total FROM matriculas WHERE plano_id = :plano_id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['plano_id' => $planoId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Calcula valor médio mensal do plano
     */
    public function getValorMensal(int $planoId): ?float
    {
        $plano = $this->find($planoId);
        if (!$plano) {
            return null;
        }

        $valorBase = (float)$plano['valor_base'];
        $periodicidade = $plano['periodicidade'];

        switch ($periodicidade) {
            case 'mensal':
                return $valorBase;
            case 'trimestral':
                return $valorBase / 3;
            case 'anual':
                return $valorBase / 12;
            default:
                return $valorBase;
        }
    }
}


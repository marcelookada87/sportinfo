<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Turma
 */
class Turma extends Model
{
    protected string $table = 'turmas';
    protected string $primaryKey = 'id';

    /**
     * Busca todas as turmas ativas
     */
    public function findAllAtivas(): array
    {
        $sql = "SELECT t.*, md.nome as modalidade_nome, pr.nome as professor_nome
                FROM {$this->table} t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE t.ativo = 1
                ORDER BY md.nome, t.nome ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca turmas com filtros e relacionamentos
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT t.*, md.nome as modalidade_nome, md.id as modalidade_id,
                       pr.nome as professor_nome, pr.id as professor_id
                FROM {$this->table} t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE 1=1";
        
        $params = [];

        // Filtro de busca (nome da turma, modalidade ou professor)
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (t.nome LIKE :search_turma OR md.nome LIKE :search_modalidade OR pr.nome LIKE :search_professor)";
            $params['search_turma'] = $searchTerm;
            $params['search_modalidade'] = $searchTerm;
            $params['search_professor'] = $searchTerm;
        }

        // Filtro de modalidade
        if (isset($filters['modalidade_id']) && $filters['modalidade_id'] > 0) {
            $sql .= " AND t.modalidade_id = :modalidade_id";
            $params['modalidade_id'] = (int)$filters['modalidade_id'];
        }

        // Filtro de professor
        if (isset($filters['professor_id']) && $filters['professor_id'] > 0) {
            $sql .= " AND t.professor_id = :professor_id";
            $params['professor_id'] = (int)$filters['professor_id'];
        }

        // Filtro de status (ativo/inativo)
        if (isset($filters['ativo']) && $filters['ativo'] !== '' && trim($filters['ativo']) !== '') {
            $sql .= " AND t.ativo = :ativo";
            $params['ativo'] = (int)trim($filters['ativo']);
        }

        // Filtro de nível
        if (isset($filters['nivel']) && $filters['nivel'] !== '' && trim($filters['nivel']) !== '') {
            $sql .= " AND t.nivel = :nivel";
            $params['nivel'] = trim($filters['nivel']);
        }

        $sql .= " ORDER BY md.nome ASC, t.nome ASC";
        
        $limit = isset($filters['limit']) && $filters['limit'] > 0 ? (int)$filters['limit'] : 20;
        $sql .= " LIMIT {$limit}";
        
        $offset = isset($filters['offset']) && $filters['offset'] >= 0 ? (int)$filters['offset'] : 0;
        if ($offset > 0) {
            $sql .= " OFFSET {$offset}";
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll() ?: [];
            
            // Processa dias da semana (JSON)
            foreach ($result as &$turma) {
                if (!empty($turma['dias_da_semana'])) {
                    $dias = json_decode($turma['dias_da_semana'], true);
                    $turma['dias_array'] = is_array($dias) ? $dias : [];
                } else {
                    $turma['dias_array'] = [];
                }
            }
            unset($turma);
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Erro ao buscar turmas: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de turmas com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE 1=1";
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (t.nome LIKE :search_turma OR md.nome LIKE :search_modalidade OR pr.nome LIKE :search_professor)";
            $params['search_turma'] = $searchTerm;
            $params['search_modalidade'] = $searchTerm;
            $params['search_professor'] = $searchTerm;
        }

        // Filtro de modalidade
        if (isset($filters['modalidade_id']) && $filters['modalidade_id'] > 0) {
            $sql .= " AND t.modalidade_id = :modalidade_id";
            $params['modalidade_id'] = (int)$filters['modalidade_id'];
        }

        // Filtro de professor
        if (isset($filters['professor_id']) && $filters['professor_id'] > 0) {
            $sql .= " AND t.professor_id = :professor_id";
            $params['professor_id'] = (int)$filters['professor_id'];
        }

        // Filtro de status
        if (isset($filters['ativo']) && $filters['ativo'] !== '' && trim($filters['ativo']) !== '') {
            $sql .= " AND t.ativo = :ativo";
            $params['ativo'] = (int)trim($filters['ativo']);
        }

        // Filtro de nível
        if (isset($filters['nivel']) && $filters['nivel'] !== '' && trim($filters['nivel']) !== '') {
            $sql .= " AND t.nivel = :nivel";
            $params['nivel'] = trim($filters['nivel']);
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Erro ao contar turmas: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Busca turma por ID com relacionamentos
     */
    public function findWithRelations(int $id): ?array
    {
        $sql = "SELECT t.*, md.nome as modalidade_nome, md.id as modalidade_id, md.descricao as modalidade_descricao,
                       pr.nome as professor_nome, pr.id as professor_id
                FROM {$this->table} t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE t.id = :id";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result) {
            // Processa dias da semana (JSON)
            if (!empty($result['dias_da_semana'])) {
                $dias = json_decode($result['dias_da_semana'], true);
                $result['dias_array'] = is_array($dias) ? $dias : [];
            } else {
                $result['dias_array'] = [];
            }
        }
        
        return $result ?: null;
    }

    /**
     * Verifica se turma está sendo usada em matrículas ativas
     */
    public function isUsedInMatriculas(int $turmaId): bool
    {
        $sql = "SELECT COUNT(*) as total FROM matriculas WHERE turma_id = :turma_id AND status = 'Ativa'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['turma_id' => $turmaId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0) > 0;
    }

    /**
     * Conta quantas matrículas ativas tem esta turma
     */
    public function countMatriculasAtivas(int $turmaId): int
    {
        $sql = "SELECT COUNT(*) as total FROM matriculas WHERE turma_id = :turma_id AND status = 'Ativa'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['turma_id' => $turmaId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Conta total de matrículas (ativas e inativas) desta turma
     */
    public function countTotalMatriculas(int $turmaId): int
    {
        $sql = "SELECT COUNT(*) as total FROM matriculas WHERE turma_id = :turma_id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['turma_id' => $turmaId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Verifica se turma está cheia (capacidade atingida)
     */
    public function isCheia(int $turmaId): bool
    {
        $turma = $this->find($turmaId);
        if (!$turma) {
            return false;
        }

        $matriculasAtivas = $this->countMatriculasAtivas($turmaId);
        return $matriculasAtivas >= (int)$turma['capacidade'];
    }

    /**
     * Retorna vagas disponíveis
     */
    public function getVagasDisponiveis(int $turmaId): int
    {
        $turma = $this->find($turmaId);
        if (!$turma) {
            return 0;
        }

        $matriculasAtivas = $this->countMatriculasAtivas($turmaId);
        $capacidade = (int)$turma['capacidade'];
        $vagas = $capacidade - $matriculasAtivas;
        
        return max(0, $vagas);
    }

    /**
     * Busca turmas por modalidade
     */
    public function findByModalidade(int $modalidadeId): array
    {
        $sql = "SELECT t.*, pr.nome as professor_nome
                FROM {$this->table} t
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE t.modalidade_id = :modalidade_id AND t.ativo = 1
                ORDER BY t.nome ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['modalidade_id' => $modalidadeId]);
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca turmas por professor
     */
    public function findByProfessor(int $professorId): array
    {
        $sql = "SELECT t.*, md.nome as modalidade_nome
                FROM {$this->table} t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                WHERE t.professor_id = :professor_id AND t.ativo = 1
                ORDER BY md.nome, t.nome ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['professor_id' => $professorId]);
        
        return $stmt->fetchAll() ?: [];
    }
}


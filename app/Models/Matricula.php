<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Matrícula
 */
class Matricula extends Model
{
    protected string $table = 'matriculas';
    protected string $primaryKey = 'id';

    /**
     * Busca todas as matrículas ativas
     */
    public function findAllAtivas(): array
    {
        $sql = "SELECT m.*, a.nome as aluno_nome, t.nome as turma_nome, 
                       md.nome as modalidade_nome, p.nome as plano_nome
                FROM {$this->table} m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN turmas t ON m.turma_id = t.id
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN planos p ON m.plano_id = p.id
                WHERE m.status = 'Ativa'
                ORDER BY m.dt_inicio DESC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca matrículas com filtros e relacionamentos
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT m.*, a.nome as aluno_nome, a.cpf as aluno_cpf,
                       t.nome as turma_nome, t.capacidade as turma_capacidade,
                       md.nome as modalidade_nome, md.id as modalidade_id,
                       p.nome as plano_nome, p.periodicidade as plano_periodicidade, p.valor_base as plano_valor_base,
                       pr.nome as professor_nome
                FROM {$this->table} m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN turmas t ON m.turma_id = t.id
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN planos p ON m.plano_id = p.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE 1=1";
        
        $params = [];

        // Filtro de busca (nome do aluno ou turma)
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_aluno OR t.nome LIKE :search_turma OR md.nome LIKE :search_modalidade)";
            $params['search_aluno'] = $searchTerm;
            $params['search_turma'] = $searchTerm;
            $params['search_modalidade'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND m.status = :status";
            $params['status'] = trim($filters['status']);
        }

        // Filtro de aluno
        if (isset($filters['aluno_id']) && $filters['aluno_id'] > 0) {
            $sql .= " AND m.aluno_id = :aluno_id";
            $params['aluno_id'] = (int)$filters['aluno_id'];
        }

        // Filtro de turma
        if (isset($filters['turma_id']) && $filters['turma_id'] > 0) {
            $sql .= " AND m.turma_id = :turma_id";
            $params['turma_id'] = (int)$filters['turma_id'];
        }

        // Filtro de modalidade
        if (isset($filters['modalidade_id']) && $filters['modalidade_id'] > 0) {
            $sql .= " AND md.id = :modalidade_id";
            $params['modalidade_id'] = (int)$filters['modalidade_id'];
        }

        // Filtro de plano
        if (isset($filters['plano_id']) && $filters['plano_id'] > 0) {
            $sql .= " AND m.plano_id = :plano_id";
            $params['plano_id'] = (int)$filters['plano_id'];
        }

        $sql .= " ORDER BY m.dt_inicio DESC, m.dt_cadastro DESC";
        
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
            error_log("Erro ao buscar matrículas: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de matrículas com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN turmas t ON m.turma_id = t.id
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                WHERE 1=1";
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_aluno OR t.nome LIKE :search_turma OR md.nome LIKE :search_modalidade)";
            $params['search_aluno'] = $searchTerm;
            $params['search_turma'] = $searchTerm;
            $params['search_modalidade'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND m.status = :status";
            $params['status'] = trim($filters['status']);
        }

        // Filtro de aluno
        if (isset($filters['aluno_id']) && $filters['aluno_id'] > 0) {
            $sql .= " AND m.aluno_id = :aluno_id";
            $params['aluno_id'] = (int)$filters['aluno_id'];
        }

        // Filtro de turma
        if (isset($filters['turma_id']) && $filters['turma_id'] > 0) {
            $sql .= " AND m.turma_id = :turma_id";
            $params['turma_id'] = (int)$filters['turma_id'];
        }

        // Filtro de modalidade
        if (isset($filters['modalidade_id']) && $filters['modalidade_id'] > 0) {
            $sql .= " AND md.id = :modalidade_id";
            $params['modalidade_id'] = (int)$filters['modalidade_id'];
        }

        // Filtro de plano
        if (isset($filters['plano_id']) && $filters['plano_id'] > 0) {
            $sql .= " AND m.plano_id = :plano_id";
            $params['plano_id'] = (int)$filters['plano_id'];
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Erro ao contar matrículas: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Busca matrícula por ID com relacionamentos
     */
    public function findWithRelations(int $id): ?array
    {
        $sql = "SELECT m.*, a.nome as aluno_nome, a.cpf as aluno_cpf, a.dt_nascimento as aluno_dt_nascimento,
                       t.nome as turma_nome, t.capacidade as turma_capacidade, t.local as turma_local,
                       t.dias_da_semana as turma_dias, t.hora_inicio as turma_hora_inicio, t.hora_fim as turma_hora_fim,
                       md.nome as modalidade_nome, md.id as modalidade_id,
                       p.nome as plano_nome, p.periodicidade as plano_periodicidade, p.valor_base as plano_valor_base,
                       pr.nome as professor_nome, pr.id as professor_id
                FROM {$this->table} m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN turmas t ON m.turma_id = t.id
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN planos p ON m.plano_id = p.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE m.id = :id";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Verifica se aluno já está matriculado na turma
     */
    public function alunoJaMatriculado(int $alunoId, int $turmaId, ?int $excludeMatriculaId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE aluno_id = :aluno_id AND turma_id = :turma_id AND status = 'Ativa'";
        
        $params = [
            'aluno_id' => $alunoId,
            'turma_id' => $turmaId
        ];

        if ($excludeMatriculaId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeMatriculaId;
        }

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0) > 0;
    }

    /**
     * Conta mensalidades da matrícula
     */
    public function countMensalidades(int $matriculaId): int
    {
        $sql = "SELECT COUNT(*) as total FROM mensalidades WHERE matricula_id = :matricula_id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['matricula_id' => $matriculaId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Conta mensalidades em aberto da matrícula
     */
    public function countMensalidadesAbertas(int $matriculaId): int
    {
        $sql = "SELECT COUNT(*) as total FROM mensalidades 
                WHERE matricula_id = :matricula_id AND status IN ('Aberto', 'Atrasado')";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['matricula_id' => $matriculaId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Busca matrículas por aluno
     */
    public function findByAluno(int $alunoId): array
    {
        $sql = "SELECT m.*, t.nome as turma_nome, md.nome as modalidade_nome, p.nome as plano_nome
                FROM {$this->table} m
                INNER JOIN turmas t ON m.turma_id = t.id
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN planos p ON m.plano_id = p.id
                WHERE m.aluno_id = :aluno_id
                ORDER BY m.dt_inicio DESC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['aluno_id' => $alunoId]);
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca matrículas por turma
     */
    public function findByTurma(int $turmaId): array
    {
        $sql = "SELECT m.*, a.nome as aluno_nome, p.nome as plano_nome
                FROM {$this->table} m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN planos p ON m.plano_id = p.id
                WHERE m.turma_id = :turma_id
                ORDER BY a.nome ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['turma_id' => $turmaId]);
        
        return $stmt->fetchAll() ?: [];
    }
}


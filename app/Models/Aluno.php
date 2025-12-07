<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Aluno
 */
class Aluno extends Model
{
    protected string $table = 'alunos';
    protected string $primaryKey = 'id';

    /**
     * Busca alunos com filtros
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT a.*, r.nome as responsavel_nome, r.contato as responsavel_contato 
                FROM {$this->table} a 
                LEFT JOIN responsaveis r ON a.responsavel_id = r.id 
                WHERE 1=1";
        
        $params = [];

        // Filtro de busca - usando parâmetros únicos para cada uso
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_nome OR a.cpf LIKE :search_cpf OR a.email LIKE :search_email)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_email'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND a.status = :status";
            $params['status'] = trim($filters['status']);
        }

        $sql .= " ORDER BY a.nome ASC";
        
        // LIMIT e OFFSET não podem ser parâmetros nomeados no PDO, devem ser valores diretos
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
            error_log("Erro ao buscar alunos: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de alunos com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} a WHERE 1=1";
        $params = [];

        // Filtro de busca - usando parâmetros únicos para cada uso
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_nome OR a.cpf LIKE :search_cpf OR a.email LIKE :search_email)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_email'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND a.status = :status";
            $params['status'] = trim($filters['status']);
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Erro ao contar alunos: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Busca aluno com dados do responsável
     */
    public function findWithResponsavel(int $id): ?array
    {
        $sql = "SELECT a.*, r.nome as responsavel_nome, r.cpf as responsavel_cpf, 
                       r.contato as responsavel_contato, r.email as responsavel_email
                FROM {$this->table} a 
                LEFT JOIN responsaveis r ON a.responsavel_id = r.id 
                WHERE a.id = :id 
                LIMIT 1";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch() ?: null;
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
     * Calcula idade do aluno
     */
    public function calcularIdade(string $dtNascimento): int
    {
        $nascimento = new \DateTime($dtNascimento);
        $hoje = new \DateTime();
        $idade = $hoje->diff($nascimento);
        
        return (int)$idade->y;
    }

    /**
     * Adiciona modalidades ao aluno
     */
    public function adicionarModalidades(int $alunoId, array $modalidadeIds, ?int $preferenciaId = null): bool
    {
        // Remove modalidades existentes
        $this->removerModalidades($alunoId);

        if (empty($modalidadeIds)) {
            return true;
        }

        $sql = "INSERT INTO aluno_modalidades (aluno_id, modalidade_id, preferencia) VALUES ";
        $values = [];
        $params = [];

        foreach ($modalidadeIds as $index => $modalidadeId) {
            $values[] = "(:aluno_id_{$index}, :modalidade_id_{$index}, :preferencia_{$index})";
            $params["aluno_id_{$index}"] = $alunoId;
            $params["modalidade_id_{$index}"] = (int)$modalidadeId;
            $params["preferencia_{$index}"] = ($preferenciaId && $preferenciaId == $modalidadeId) ? 1 : 0;
        }

        $sql .= implode(', ', $values);

        $stmt = self::getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Remove todas as modalidades do aluno
     */
    public function removerModalidades(int $alunoId): bool
    {
        $sql = "DELETE FROM aluno_modalidades WHERE aluno_id = :aluno_id";
        $stmt = self::getConnection()->prepare($sql);
        return $stmt->execute(['aluno_id' => $alunoId]);
    }

    /**
     * Busca IDs das modalidades do aluno
     */
    public function getModalidadeIds(int $alunoId): array
    {
        $sql = "SELECT modalidade_id FROM aluno_modalidades WHERE aluno_id = :aluno_id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['aluno_id' => $alunoId]);
        
        $result = $stmt->fetchAll();
        return array_column($result, 'modalidade_id');
    }

    /**
     * Busca modalidade preferida do aluno
     */
    public function getModalidadePreferida(int $alunoId): ?int
    {
        $sql = "SELECT modalidade_id FROM aluno_modalidades 
                WHERE aluno_id = :aluno_id AND preferencia = 1 
                LIMIT 1";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['aluno_id' => $alunoId]);
        
        $result = $stmt->fetch();
        return $result ? (int)$result['modalidade_id'] : null;
    }
}


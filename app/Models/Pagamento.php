<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Pagamento
 */
class Pagamento extends Model
{
    protected string $table = 'pagamentos';
    protected string $primaryKey = 'id';

    /**
     * Busca pagamentos com filtros e joins
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT 
                    p.*,
                    m.id as mensalidade_id,
                    m.competencia,
                    m.valor as mensalidade_valor,
                    m.status as mensalidade_status,
                    mat.id as matricula_id,
                    a.id as aluno_id,
                    a.nome as aluno_nome,
                    a.cpf as aluno_cpf
                FROM {$this->table} p
                INNER JOIN mensalidades m ON p.mensalidade_id = m.id
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                WHERE 1=1";
        
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_nome OR a.cpf LIKE :search_cpf OR p.transacao_ref LIKE :search_ref)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_ref'] = $searchTerm;
        }

        // Filtro de forma de pagamento
        if (isset($filters['forma']) && $filters['forma'] !== '' && trim($filters['forma']) !== '') {
            $sql .= " AND p.forma = :forma";
            $params['forma'] = trim($filters['forma']);
        }

        // Filtro de conciliado
        if (isset($filters['conciliado']) && $filters['conciliado'] !== '') {
            $sql .= " AND p.conciliado = :conciliado";
            $params['conciliado'] = (int)trim($filters['conciliado']);
        }

        // Filtro de data de pagamento (início)
        if (isset($filters['dt_pagamento_inicio']) && $filters['dt_pagamento_inicio'] !== '') {
            $sql .= " AND DATE(p.dt_pagamento) >= :dt_pagamento_inicio";
            $params['dt_pagamento_inicio'] = trim($filters['dt_pagamento_inicio']);
        }

        // Filtro de data de pagamento (fim)
        if (isset($filters['dt_pagamento_fim']) && $filters['dt_pagamento_fim'] !== '') {
            $sql .= " AND DATE(p.dt_pagamento) <= :dt_pagamento_fim";
            $params['dt_pagamento_fim'] = trim($filters['dt_pagamento_fim']);
        }

        // Filtro de aluno
        if (isset($filters['aluno_id']) && $filters['aluno_id'] !== '' && trim($filters['aluno_id']) !== '') {
            $sql .= " AND a.id = :aluno_id";
            $params['aluno_id'] = (int)trim($filters['aluno_id']);
        }

        $sql .= " ORDER BY p.dt_pagamento DESC, p.id DESC";
        
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
            error_log("Erro ao buscar pagamentos: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de pagamentos com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} p
                INNER JOIN mensalidades m ON p.mensalidade_id = m.id
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                WHERE 1=1";
        $params = [];

        // Mesmos filtros do findAllWithFilters
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_nome OR a.cpf LIKE :search_cpf OR p.transacao_ref LIKE :search_ref)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_ref'] = $searchTerm;
        }

        if (isset($filters['forma']) && $filters['forma'] !== '' && trim($filters['forma']) !== '') {
            $sql .= " AND p.forma = :forma";
            $params['forma'] = trim($filters['forma']);
        }

        if (isset($filters['conciliado']) && $filters['conciliado'] !== '') {
            $sql .= " AND p.conciliado = :conciliado";
            $params['conciliado'] = (int)trim($filters['conciliado']);
        }

        if (isset($filters['dt_pagamento_inicio']) && $filters['dt_pagamento_inicio'] !== '') {
            $sql .= " AND DATE(p.dt_pagamento) >= :dt_pagamento_inicio";
            $params['dt_pagamento_inicio'] = trim($filters['dt_pagamento_inicio']);
        }

        if (isset($filters['dt_pagamento_fim']) && $filters['dt_pagamento_fim'] !== '') {
            $sql .= " AND DATE(p.dt_pagamento) <= :dt_pagamento_fim";
            $params['dt_pagamento_fim'] = trim($filters['dt_pagamento_fim']);
        }

        if (isset($filters['aluno_id']) && $filters['aluno_id'] !== '' && trim($filters['aluno_id']) !== '') {
            $sql .= " AND a.id = :aluno_id";
            $params['aluno_id'] = (int)trim($filters['aluno_id']);
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Erro ao contar pagamentos: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca pagamento com detalhes completos
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT 
                    p.*,
                    m.id as mensalidade_id,
                    m.competencia,
                    m.valor as mensalidade_valor,
                    m.desconto,
                    m.multa,
                    m.juros,
                    m.status as mensalidade_status,
                    m.dt_vencimento,
                    mat.id as matricula_id,
                    a.id as aluno_id,
                    a.nome as aluno_nome,
                    a.cpf as aluno_cpf,
                    a.contato as aluno_contato,
                    a.email as aluno_email
                FROM {$this->table} p
                INNER JOIN mensalidades m ON p.mensalidade_id = m.id
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                WHERE p.id = :id
                LIMIT 1";
        
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log("Erro ao buscar pagamento: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca pagamentos por mensalidade
     */
    public function findByMensalidade(int $mensalidadeId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE mensalidade_id = :mensalidade_id ORDER BY dt_pagamento DESC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['mensalidade_id' => $mensalidadeId]);
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Calcula total pago de uma mensalidade
     */
    public function getTotalPago(int $mensalidadeId): float
    {
        $sql = "SELECT SUM(valor_pago) as total FROM {$this->table} WHERE mensalidade_id = :mensalidade_id";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['mensalidade_id' => $mensalidadeId]);
        $result = $stmt->fetch();
        
        return (float)($result['total'] ?? 0);
    }

    /**
     * Atualiza status de conciliação
     */
    public function updateConciliado(int $id, bool $conciliado): bool
    {
        return $this->update($id, ['conciliado' => $conciliado ? 1 : 0]);
    }

    /**
     * Obtém estatísticas de pagamentos
     */
    public function getEstatisticas(array $filters = []): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(valor_pago) as valor_total,
                    SUM(CASE WHEN forma = 'PIX' THEN valor_pago ELSE 0 END) as valor_pix,
                    SUM(CASE WHEN forma = 'Cartão' THEN valor_pago ELSE 0 END) as valor_cartao,
                    SUM(CASE WHEN forma = 'Dinheiro' THEN valor_pago ELSE 0 END) as valor_dinheiro,
                    SUM(CASE WHEN forma = 'Boleto' THEN valor_pago ELSE 0 END) as valor_boleto,
                    SUM(CASE WHEN conciliado = 1 THEN valor_pago ELSE 0 END) as valor_conciliado
                FROM {$this->table} p
                INNER JOIN mensalidades m ON p.mensalidade_id = m.id
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                WHERE 1=1";
        
        $params = [];

        if (isset($filters['forma']) && $filters['forma'] !== '' && trim($filters['forma']) !== '') {
            $sql .= " AND p.forma = :forma";
            $params['forma'] = trim($filters['forma']);
        }

        if (isset($filters['dt_pagamento_inicio']) && $filters['dt_pagamento_inicio'] !== '') {
            $sql .= " AND DATE(p.dt_pagamento) >= :dt_pagamento_inicio";
            $params['dt_pagamento_inicio'] = trim($filters['dt_pagamento_inicio']);
        }

        if (isset($filters['dt_pagamento_fim']) && $filters['dt_pagamento_fim'] !== '') {
            $sql .= " AND DATE(p.dt_pagamento) <= :dt_pagamento_fim";
            $params['dt_pagamento_fim'] = trim($filters['dt_pagamento_fim']);
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return [
                'total' => (int)($result['total'] ?? 0),
                'valor_total' => (float)($result['valor_total'] ?? 0),
                'valor_pix' => (float)($result['valor_pix'] ?? 0),
                'valor_cartao' => (float)($result['valor_cartao'] ?? 0),
                'valor_dinheiro' => (float)($result['valor_dinheiro'] ?? 0),
                'valor_boleto' => (float)($result['valor_boleto'] ?? 0),
                'valor_conciliado' => (float)($result['valor_conciliado'] ?? 0)
            ];
        } catch (\PDOException $e) {
            error_log("Erro ao buscar estatísticas de pagamentos: " . $e->getMessage());
            return [
                'total' => 0,
                'valor_total' => 0.0,
                'valor_pix' => 0.0,
                'valor_cartao' => 0.0,
                'valor_dinheiro' => 0.0,
                'valor_boleto' => 0.0,
                'valor_conciliado' => 0.0
            ];
        }
    }
}


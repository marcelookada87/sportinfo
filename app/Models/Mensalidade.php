<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Mensalidade
 */
class Mensalidade extends Model
{
    protected string $table = 'mensalidades';
    protected string $primaryKey = 'id';

    /**
     * Busca mensalidades com filtros e joins
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT 
                    m.*,
                    mat.id as matricula_id,
                    mat.dt_inicio,
                    mat.dt_fim,
                    mat.status as matricula_status,
                    a.id as aluno_id,
                    a.nome as aluno_nome,
                    a.cpf as aluno_cpf,
                    pl.nome as plano_nome,
                    pl.valor_base as plano_valor_base,
                    t.nome as turma_nome,
                    t.id as turma_id,
                    md.nome as modalidade_nome
                FROM {$this->table} m
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                INNER JOIN planos pl ON mat.plano_id = pl.id
                INNER JOIN turmas t ON mat.turma_id = t.id
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                WHERE 1=1";
        
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_nome OR a.cpf LIKE :search_cpf OR m.competencia LIKE :search_competencia)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_competencia'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND m.status = :status";
            $params['status'] = trim($filters['status']);
        }

        // Filtro de competência
        if (isset($filters['competencia']) && $filters['competencia'] !== '' && trim($filters['competencia']) !== '') {
            $sql .= " AND m.competencia = :competencia";
            $params['competencia'] = trim($filters['competencia']);
        }

        // Filtro de data de vencimento (início)
        if (isset($filters['dt_vencimento_inicio']) && $filters['dt_vencimento_inicio'] !== '') {
            $sql .= " AND m.dt_vencimento >= :dt_vencimento_inicio";
            $params['dt_vencimento_inicio'] = trim($filters['dt_vencimento_inicio']);
        }

        // Filtro de data de vencimento (fim)
        if (isset($filters['dt_vencimento_fim']) && $filters['dt_vencimento_fim'] !== '') {
            $sql .= " AND m.dt_vencimento <= :dt_vencimento_fim";
            $params['dt_vencimento_fim'] = trim($filters['dt_vencimento_fim']);
        }

        // Filtro de aluno
        if (isset($filters['aluno_id']) && $filters['aluno_id'] !== '' && trim($filters['aluno_id']) !== '') {
            $sql .= " AND a.id = :aluno_id";
            $params['aluno_id'] = (int)trim($filters['aluno_id']);
        }

        $sql .= " ORDER BY m.dt_vencimento DESC, m.competencia DESC";
        
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
            error_log("Erro ao buscar mensalidades: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Conta total de mensalidades com filtros
     */
    public function countWithFilters(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} m
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                WHERE 1=1";
        $params = [];

        // Filtro de busca
        if (isset($filters['search']) && $filters['search'] !== '' && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.nome LIKE :search_nome OR a.cpf LIKE :search_cpf OR m.competencia LIKE :search_competencia)";
            $params['search_nome'] = $searchTerm;
            $params['search_cpf'] = $searchTerm;
            $params['search_competencia'] = $searchTerm;
        }

        // Filtro de status
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND m.status = :status";
            $params['status'] = trim($filters['status']);
        }

        // Filtro de competência
        if (isset($filters['competencia']) && $filters['competencia'] !== '' && trim($filters['competencia']) !== '') {
            $sql .= " AND m.competencia = :competencia";
            $params['competencia'] = trim($filters['competencia']);
        }

        // Filtro de data de vencimento (início)
        if (isset($filters['dt_vencimento_inicio']) && $filters['dt_vencimento_inicio'] !== '') {
            $sql .= " AND m.dt_vencimento >= :dt_vencimento_inicio";
            $params['dt_vencimento_inicio'] = trim($filters['dt_vencimento_inicio']);
        }

        // Filtro de data de vencimento (fim)
        if (isset($filters['dt_vencimento_fim']) && $filters['dt_vencimento_fim'] !== '') {
            $sql .= " AND m.dt_vencimento <= :dt_vencimento_fim";
            $params['dt_vencimento_fim'] = trim($filters['dt_vencimento_fim']);
        }

        // Filtro de aluno
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
            error_log("Erro ao contar mensalidades: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            throw $e;
        }
    }

    /**
     * Busca mensalidade com detalhes completos
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT 
                    m.*,
                    mat.id as matricula_id,
                    mat.dt_inicio,
                    mat.dt_fim,
                    mat.status as matricula_status,
                    a.id as aluno_id,
                    a.nome as aluno_nome,
                    a.cpf as aluno_cpf,
                    a.contato as aluno_contato,
                    a.email as aluno_email,
                    pl.id as plano_id,
                    pl.nome as plano_nome,
                    pl.valor_base as plano_valor_base,
                    pl.periodicidade as plano_periodicidade
                FROM {$this->table} m
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                INNER JOIN planos pl ON mat.plano_id = pl.id
                WHERE m.id = :id
                LIMIT 1";
        
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log("Erro ao buscar mensalidade: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calcula valor total da mensalidade (valor + multa + juros - desconto)
     */
    public function calcularValorTotal(int $mensalidadeId): float
    {
        $mensalidade = $this->find($mensalidadeId);
        if (!$mensalidade) {
            return 0.0;
        }

        $valor = (float)$mensalidade['valor'];
        $desconto = (float)$mensalidade['desconto'];
        $multa = (float)$mensalidade['multa'];
        $juros = (float)$mensalidade['juros'];

        return $valor + $multa + $juros - $desconto;
    }

    /**
     * Verifica se mensalidade está paga
     */
    public function isPaga(int $mensalidadeId): bool
    {
        $mensalidade = $this->find($mensalidadeId);
        return $mensalidade && $mensalidade['status'] === 'Pago';
    }

    /**
     * Verifica se mensalidade está atrasada
     */
    public function isAtrasada(int $mensalidadeId): bool
    {
        $mensalidade = $this->find($mensalidadeId);
        if (!$mensalidade) {
            return false;
        }

        $dtVencimento = new \DateTime($mensalidade['dt_vencimento']);
        $hoje = new \DateTime();
        
        return $mensalidade['status'] !== 'Pago' 
            && $mensalidade['status'] !== 'Cancelado'
            && $dtVencimento < $hoje;
    }

    /**
     * Busca mensalidades por matrícula
     */
    public function findByMatricula(int $matriculaId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE matricula_id = :matricula_id ORDER BY competencia DESC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['matricula_id' => $matriculaId]);
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca mensalidades por competência
     */
    public function findByCompetencia(string $competencia): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE competencia = :competencia ORDER BY dt_vencimento ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['competencia' => $competencia]);
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Atualiza status da mensalidade
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Calcula e atualiza multa e juros automaticamente para uma mensalidade
     */
    public function calcularEAtualizarMultaEJuros(int $mensalidadeId): bool
    {
        $mensalidade = $this->find($mensalidadeId);
        if (!$mensalidade) {
            return false;
        }

        // Se já está paga ou cancelada, não atualiza
        if (in_array($mensalidade['status'], ['Pago', 'Cancelado'])) {
            return false;
        }

        $configModel = new \App\Models\ConfiguracaoFinanceira();
        $calculo = $configModel->calcularMultaEJuros(
            (float)$mensalidade['valor'],
            $mensalidade['dt_vencimento']
        );

        // Atualiza multa e juros
        $data = [
            'multa' => $calculo['multa'],
            'juros' => $calculo['juros']
        ];

        // Se está vencida e ainda está como "Aberto", muda para "Atrasado"
        if ($calculo['dias_atraso'] > 0 && $mensalidade['status'] === 'Aberto') {
            $data['status'] = 'Atrasado';
        }

        return $this->update($mensalidadeId, $data);
    }

    /**
     * Calcula e atualiza multa e juros para todas as mensalidades vencidas
     */
    public function atualizarMultaEJurosVencidas(): int
    {
        $hoje = date('Y-m-d');
        
        // Busca mensalidades vencidas que não estão pagas ou canceladas
        $sql = "SELECT id, valor, dt_vencimento, status 
                FROM {$this->table} 
                WHERE dt_vencimento < :hoje 
                AND status NOT IN ('Pago', 'Cancelado')";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['hoje' => $hoje]);
        $mensalidades = $stmt->fetchAll() ?: [];

        $configModel = new \App\Models\ConfiguracaoFinanceira();
        $atualizadas = 0;

        foreach ($mensalidades as $mensalidade) {
            $calculo = $configModel->calcularMultaEJuros(
                (float)$mensalidade['valor'],
                $mensalidade['dt_vencimento']
            );

            $data = [
                'multa' => $calculo['multa'],
                'juros' => $calculo['juros']
            ];

            // Se está vencida e ainda está como "Aberto", muda para "Atrasado"
            if ($calculo['dias_atraso'] > 0 && $mensalidade['status'] === 'Aberto') {
                $data['status'] = 'Atrasado';
            }

            if ($this->update((int)$mensalidade['id'], $data)) {
                $atualizadas++;
            }
        }

        return $atualizadas;
    }

    /**
     * Obtém estatísticas financeiras
     */
    public function getEstatisticas(array $filters = []): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Pago' THEN 1 ELSE 0 END) as total_pagas,
                    SUM(CASE WHEN status = 'Aberto' THEN 1 ELSE 0 END) as total_abertas,
                    SUM(CASE WHEN status = 'Atrasado' THEN 1 ELSE 0 END) as total_atrasadas,
                    SUM(CASE WHEN status = 'Pago' THEN (valor + multa + juros - desconto) ELSE 0 END) as valor_recebido,
                    SUM(CASE WHEN status IN ('Aberto', 'Atrasado') THEN (valor + multa + juros - desconto) ELSE 0 END) as valor_pendente
                FROM {$this->table} m
                INNER JOIN matriculas mat ON m.matricula_id = mat.id
                INNER JOIN alunos a ON mat.aluno_id = a.id
                WHERE 1=1";
        
        $params = [];

        // Aplica mesmos filtros
        if (isset($filters['status']) && $filters['status'] !== '' && trim($filters['status']) !== '') {
            $sql .= " AND m.status = :status";
            $params['status'] = trim($filters['status']);
        }

        if (isset($filters['competencia']) && $filters['competencia'] !== '' && trim($filters['competencia']) !== '') {
            $sql .= " AND m.competencia = :competencia";
            $params['competencia'] = trim($filters['competencia']);
        }

        if (isset($filters['dt_vencimento_inicio']) && $filters['dt_vencimento_inicio'] !== '') {
            $sql .= " AND m.dt_vencimento >= :dt_vencimento_inicio";
            $params['dt_vencimento_inicio'] = trim($filters['dt_vencimento_inicio']);
        }

        if (isset($filters['dt_vencimento_fim']) && $filters['dt_vencimento_fim'] !== '') {
            $sql .= " AND m.dt_vencimento <= :dt_vencimento_fim";
            $params['dt_vencimento_fim'] = trim($filters['dt_vencimento_fim']);
        }

        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return [
                'total' => (int)($result['total'] ?? 0),
                'total_pagas' => (int)($result['total_pagas'] ?? 0),
                'total_abertas' => (int)($result['total_abertas'] ?? 0),
                'total_atrasadas' => (int)($result['total_atrasadas'] ?? 0),
                'valor_recebido' => (float)($result['valor_recebido'] ?? 0),
                'valor_pendente' => (float)($result['valor_pendente'] ?? 0)
            ];
        } catch (\PDOException $e) {
            error_log("Erro ao buscar estatísticas: " . $e->getMessage());
            return [
                'total' => 0,
                'total_pagas' => 0,
                'total_abertas' => 0,
                'total_atrasadas' => 0,
                'valor_recebido' => 0.0,
                'valor_pendente' => 0.0
            ];
        }
    }
}


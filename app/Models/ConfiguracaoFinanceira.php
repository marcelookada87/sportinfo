<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Model de Configuração Financeira
 */
class ConfiguracaoFinanceira extends Model
{
    protected string $table = 'configuracoes_financeiras';
    protected string $primaryKey = 'id';

    /**
     * Busca configuração por chave
     */
    public function findByChave(string $chave): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE chave = :chave AND ativo = 1 LIMIT 1";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['chave' => $chave]);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Obtém valor de uma configuração
     */
    public function getValor(string $chave, mixed $default = null): mixed
    {
        $config = $this->findByChave($chave);
        
        if (!$config) {
            return $default;
        }

        $valor = $config['valor'];
        
        // Converte conforme o tipo
        switch ($config['tipo']) {
            case 'integer':
                return (int)$valor;
            case 'decimal':
                return (float)$valor;
            case 'boolean':
                return (bool)$valor;
            default:
                return $valor;
        }
    }

    /**
     * Define valor de uma configuração
     */
    public function setValor(string $chave, mixed $valor, string $tipo = 'string', string $descricao = ''): bool
    {
        $config = $this->findByChave($chave);
        
        $data = [
            'chave' => $chave,
            'valor' => (string)$valor,
            'tipo' => $tipo,
            'descricao' => $descricao,
            'ativo' => 1
        ];

        if ($config) {
            // Atualiza
            return $this->update((int)$config['id'], $data);
        } else {
            // Cria nova
            return (bool)$this->create($data);
        }
    }

    /**
     * Busca todas as configurações ativas
     */
    public function findAllAtivas(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1 ORDER BY chave ASC";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Obtém todas as configurações financeiras como array associativo
     */
    public function getAllAsArray(): array
    {
        $configs = $this->findAllAtivas();
        $result = [];
        
        foreach ($configs as $config) {
            $chave = $config['chave'];
            $valor = $config['valor'];
            
            // Converte conforme o tipo
            switch ($config['tipo']) {
                case 'integer':
                    $result[$chave] = (int)$valor;
                    break;
                case 'decimal':
                    $result[$chave] = (float)$valor;
                    break;
                case 'boolean':
                    $result[$chave] = (bool)$valor;
                    break;
                default:
                    $result[$chave] = $valor;
            }
        }
        
        return $result;
    }

    /**
     * Calcula multa baseado nas configurações
     */
    public function calcularMulta(float $valorMensalidade): float
    {
        $tipo = $this->getValor('multa_tipo', 'porcentagem');
        $valorConfig = $this->getValor('multa_valor', 2.0);
        
        if ($tipo === 'fixo') {
            return $valorConfig;
        } else {
            // Porcentagem
            return ($valorMensalidade * $valorConfig) / 100;
        }
    }

    /**
     * Calcula juros baseado nas configurações e dias de atraso
     */
    public function calcularJuros(float $valorMensalidade, int $diasAtraso): float
    {
        $tipo = $this->getValor('juros_tipo', 'porcentagem');
        $valorConfig = $this->getValor('juros_valor', 0.33);
        $diasCarencia = $this->getValor('dias_carencia', 0);
        
        // Aplica carência
        $diasAplicaveis = max(0, $diasAtraso - $diasCarencia);
        
        if ($diasAplicaveis <= 0) {
            return 0.0;
        }
        
        if ($tipo === 'fixo') {
            // Juros fixo por dia
            return $valorConfig * $diasAplicaveis;
        } else {
            // Porcentagem ao mês (proporcional aos dias)
            $jurosMensal = ($valorMensalidade * $valorConfig) / 100;
            // Calcula proporcional aos dias (considera 30 dias = 1 mês)
            return ($jurosMensal * $diasAplicaveis) / 30;
        }
    }

    /**
     * Calcula multa e juros para uma mensalidade vencida
     */
    public function calcularMultaEJuros(float $valorMensalidade, string $dtVencimento): array
    {
        $hoje = new \DateTime();
        $hoje->setTime(0, 0, 0); // Zera hora para comparar apenas datas
        $vencimento = new \DateTime($dtVencimento);
        $vencimento->setTime(0, 0, 0); // Zera hora para comparar apenas datas
        
        // Se não está vencida, retorna zero
        if ($hoje <= $vencimento) {
            return [
                'multa' => 0.0,
                'juros' => 0.0,
                'dias_atraso' => 0
            ];
        }
        
        // Calcula diferença em dias (sempre positivo quando vencida)
        $diasAtraso = (int)$hoje->diff($vencimento)->days;
        
        $multa = $this->calcularMulta($valorMensalidade);
        $juros = $this->calcularJuros($valorMensalidade, $diasAtraso);
        
        return [
            'multa' => round($multa, 2),
            'juros' => round($juros, 2),
            'dias_atraso' => $diasAtraso
        ];
    }
}

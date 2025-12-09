<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Model;
use App\Models\Usuario;
use App\Models\Aluno;
use App\Models\Professor;

/**
 * Controller do Dashboard
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        // Verifica se está autenticado
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->find((int)$_SESSION['usuario_id']);

        if (!$usuario) {
            session_destroy();
            $this->redirect('/login');
            return;
        }

        // Dados para o dashboard
        $stats = $this->getDashboardStats();

        $content = $this->view->render('dashboard/index', [
            'usuario' => $usuario,
            'stats' => $stats
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Dashboard - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    /**
     * Obtém estatísticas do dashboard
     */
    protected function getDashboardStats(): array
    {
        $pdo = Model::getConnection();
        
        try {
            // Total de alunos
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM alunos");
            $totalAlunos = (int)($stmt->fetch()['total'] ?? 0);
            
            // Total de turmas ativas
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM turmas WHERE ativo = 1");
            $totalTurmas = (int)($stmt->fetch()['total'] ?? 0);
            
            // Total de professores ativos
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM professores WHERE status = 'Ativo'");
            $totalProfessores = (int)($stmt->fetch()['total'] ?? 0);
            
            // Mensalidades abertas (status = 'Aberto')
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM mensalidades WHERE status = 'Aberto'");
            $mensalidadesAbertas = (int)($stmt->fetch()['total'] ?? 0);
            
            // Receita do mês atual (mensalidades pagas no mês)
            $mesAtual = date('Y-m');
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(p.valor_pago), 0) as total 
                FROM pagamentos p
                INNER JOIN mensalidades m ON p.mensalidade_id = m.id
                WHERE DATE_FORMAT(p.dt_pagamento, '%Y-%m') = :mes_atual
            ");
            $stmt->execute(['mes_atual' => $mesAtual]);
            $receitaMes = (float)($stmt->fetch()['total'] ?? 0);
            
            // Inadimplência (mensalidades atrasadas)
            $hoje = date('Y-m-d');
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(m.valor - m.desconto + m.multa + m.juros), 0) as total 
                FROM mensalidades m
                WHERE m.status = 'Atrasado' 
                OR (m.status = 'Aberto' AND m.dt_vencimento < :hoje)
            ");
            $stmt->execute(['hoje' => $hoje]);
            $inadimplencia = (float)($stmt->fetch()['total'] ?? 0);
            
            return [
                'total_alunos' => $totalAlunos,
                'total_turmas' => $totalTurmas,
                'total_professores' => $totalProfessores,
                'mensalidades_abertas' => $mensalidadesAbertas,
                'receita_mes' => $receitaMes,
                'inadimplencia' => $inadimplencia
            ];
        } catch (\PDOException $e) {
            error_log("Erro ao buscar estatísticas do dashboard: " . $e->getMessage());
            // Retorna valores padrão em caso de erro
            return [
                'total_alunos' => 0,
                'total_turmas' => 0,
                'total_professores' => 0,
                'mensalidades_abertas' => 0,
                'receita_mes' => 0.00,
                'inadimplencia' => 0.00
            ];
        }
    }
}


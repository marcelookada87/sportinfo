<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

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
        // Placeholder - será implementado com queries reais
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


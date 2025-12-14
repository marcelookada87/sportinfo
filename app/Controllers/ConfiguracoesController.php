<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ConfiguracaoFinanceira;
use App\Models\Usuario;

/**
 * Controller de Configurações
 */
class ConfiguracoesController extends Controller
{
    /**
     * Lista e edita configurações financeiras
     */
    public function index(): void
    {
        // Verifica autenticação
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

        // Verifica se é Admin ou Financeiro
        if (!in_array($usuario['perfil'], ['Admin', 'Financeiro'])) {
            $this->redirect('/dashboard');
            return;
        }

        $configModel = new ConfiguracaoFinanceira();
        
        // Busca todas as configurações
        $configs = $configModel->findAllAtivas();
        
        // Organiza em array associativo para facilitar
        $configArray = [];
        foreach ($configs as $config) {
            $configArray[$config['chave']] = $config;
        }

        // Se for POST, atualiza configurações
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($csrfToken)) {
                $_SESSION['error'] = 'Token de segurança inválido.';
                $this->redirect('/configuracoes');
                return;
            }
            
            $multaTipo = $_POST['multa_tipo'] ?? 'porcentagem';
            $multaValor = (float)($_POST['multa_valor'] ?? 0);
            $jurosTipo = $_POST['juros_tipo'] ?? 'porcentagem';
            $jurosValor = (float)($_POST['juros_valor'] ?? 0);
            $diasCarencia = (int)($_POST['dias_carencia'] ?? 0);

            // Validações
            if ($multaValor < 0) {
                $_SESSION['error'] = 'Valor da multa não pode ser negativo.';
                $this->redirect('/configuracoes');
                return;
            }

            if ($jurosValor < 0) {
                $_SESSION['error'] = 'Valor dos juros não pode ser negativo.';
                $this->redirect('/configuracoes');
                return;
            }

            if ($diasCarencia < 0) {
                $_SESSION['error'] = 'Dias de carência não pode ser negativo.';
                $this->redirect('/configuracoes');
                return;
            }

            // Atualiza configurações
            $configModel->setValor('multa_tipo', $multaTipo, 'string', 'Tipo de cálculo de multa (fixo ou porcentagem)');
            $configModel->setValor('multa_valor', $multaValor, 'decimal', 'Valor da multa (fixo em R$ ou porcentagem)');
            $configModel->setValor('juros_tipo', $jurosTipo, 'string', 'Tipo de cálculo de juros (fixo ou porcentagem)');
            $configModel->setValor('juros_valor', $jurosValor, 'decimal', 'Valor dos juros (fixo em R$ ou porcentagem ao mês)');
            $configModel->setValor('dias_carencia', $diasCarencia, 'integer', 'Dias de carência antes de aplicar multa e juros');

            $_SESSION['success'] = 'Configurações atualizadas com sucesso!';
            $this->redirect('/configuracoes');
            return;
        }

        // Busca valores atuais
        $multaTipo = $configModel->getValor('multa_tipo', 'porcentagem');
        $multaValor = $configModel->getValor('multa_valor', 2.0);
        $jurosTipo = $configModel->getValor('juros_tipo', 'porcentagem');
        $jurosValor = $configModel->getValor('juros_valor', 0.33);
        $diasCarencia = $configModel->getValor('dias_carencia', 0);

        $content = $this->view->render('configuracoes/index', [
            'usuario' => $usuario,
            'multa_tipo' => $multaTipo,
            'multa_valor' => $multaValor,
            'juros_tipo' => $jurosTipo,
            'juros_valor' => $jurosValor,
            'dias_carencia' => $diasCarencia,
            'csrf_token' => $this->generateCsrfToken()
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Configurações Financeiras - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }
}

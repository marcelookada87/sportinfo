<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

/**
 * Controller de autenticação
 */
class AuthController extends Controller
{
    public function login(): void
    {
        // Se já estiver autenticado, redireciona para dashboard
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('/dashboard');
            return;
        }

        $content = $this->view->render('auth/login', [
            'csrf_token' => $this->generateCsrfToken(),
            'error' => $_SESSION['login_error'] ?? null
        ]);
        
        // Remove erro da sessão após exibir
        unset($_SESSION['login_error']);
        
        echo $this->view->renderWithLayout('layout-auth', [
            'title' => 'Login - Sistema de Escola de Esportes',
            'content' => $content
        ]);
    }

    public function authenticate(): void
    {
        $this->validateMethod('POST');
        
        $data = $this->getPostData();
        
        // Valida CSRF
        if (empty($data['csrf_token']) || !$this->validateCsrfToken($data['csrf_token'])) {
            $_SESSION['login_error'] = 'Token de segurança inválido';
            $this->redirect('/login');
            return;
        }
        
        // Valida campos
        if (empty($data['email']) || empty($data['senha'])) {
            $_SESSION['login_error'] = 'Email e senha são obrigatórios';
            $this->redirect('/login');
            return;
        }
        
        // Autentica usuário
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->verifyCredentials($data['email'], $data['senha']);
        
        if (!$usuario) {
            $_SESSION['login_error'] = 'Email ou senha inválidos';
            $this->redirect('/login');
            return;
        }
        
        // Cria sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];
        $_SESSION['login_time'] = time();
        
        // Atualiza última atividade
        $usuarioModel->updateLastActivity($usuario['id']);
        
        // Redireciona para dashboard
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}


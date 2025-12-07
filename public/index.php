<?php

declare(strict_types=1);

/**
 * Front Controller
 * Ponto de entrada único da aplicação
 */

// Carrega configurações
require_once dirname(__DIR__) . '/config/config.php';

// Carrega autoloader do Composer (se existir)
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
} else {
    // Carrega autoloader simples se Composer não estiver instalado
    require_once dirname(__DIR__) . '/app/autoload.php';
}

use App\Core\Router;

// Cria router e define rotas
$router = new Router();

// Rotas públicas
$router->get('/login', 'AuthController', 'login');
$router->post('/login', 'AuthController', 'authenticate');
$router->get('/logout', 'AuthController', 'logout');

// Rotas protegidas
$router->get('/', 'DashboardController', 'index');
$router->get('/dashboard', 'DashboardController', 'index');

// Rotas de Alunos
$router->get('/alunos', 'AlunosController', 'index');
$router->get('/alunos/create', 'AlunosController', 'create');
$router->post('/alunos', 'AlunosController', 'store');
$router->get('/alunos/{id}', 'AlunosController', 'show');
$router->get('/alunos/{id}/edit', 'AlunosController', 'edit');
$router->post('/alunos/{id}', 'AlunosController', 'update');
$router->post('/alunos/{id}/delete', 'AlunosController', 'delete');

// Resolve rota
$router->resolve();


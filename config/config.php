<?php

declare(strict_types=1);

/**
 * Configurações gerais do sistema
 */

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Ambiente (development, production)
define('ENVIRONMENT', 'development');

// Base URL do sistema
define('BASE_URL', 'http://localhost/mensalidade');
define('ASSETS_URL', BASE_URL . '/public/assets');

// Caminhos do sistema
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOG_PATH', STORAGE_PATH . '/logs');

// Configurações de erro
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', LOG_PATH . '/php_errors.log');
}

// Configurações de sessão
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '0'); // Ativar em produção com HTTPS
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Strict');

// Configurações de segurança
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 7200); // 2 horas


<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Classe base Controller
 */
abstract class Controller
{
    protected View $view;
    protected array $middleware = [];

    public function __construct()
    {
        $this->view = new View();
        
        // Inicia sessão se ainda não foi iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Redireciona para uma URL
     */
    protected function redirect(string $url, int $code = 302): void
    {
        // Se a URL já começa com http/https, usa como está
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            header("Location: {$url}", true, $code);
            exit;
        }
        
        // Obtém BASE_URL (garante que está definida)
        if (!defined('BASE_URL')) {
            // Tenta carregar config se não estiver carregado
            if (file_exists(ROOT_PATH . '/config/config.php')) {
                require_once ROOT_PATH . '/config/config.php';
            }
        }
        
        $baseUrl = defined('BASE_URL') ? BASE_URL : 'http://localhost/mensalidade';
        
        // Remove barra final do baseUrl se existir
        $baseUrl = rtrim($baseUrl, '/');
        
        // Se começa com /, adiciona BASE_URL
        if (strpos($url, '/') === 0) {
            $finalUrl = $baseUrl . $url;
        } else {
            // URL relativa, adiciona BASE_URL com /
            $finalUrl = $baseUrl . '/' . $url;
        }
        
        // Garante que é uma URL absoluta
        header("Location: {$finalUrl}", true, $code);
        exit;
    }

    /**
     * Retorna resposta JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Retorna resposta de erro
     */
    protected function error(string $message, int $statusCode = 400): void
    {
        $this->json(['error' => $message], $statusCode);
    }

    /**
     * Retorna resposta de sucesso
     */
    protected function success(array $data = [], string $message = ''): void
    {
        $response = ['success' => true];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        if (!empty($data)) {
            $response['data'] = $data;
        }
        
        $this->json($response);
    }

    /**
     * Verifica se é requisição AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Obtém método HTTP da requisição
     */
    protected function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Valida método HTTP esperado
     */
    protected function validateMethod(string $expected): void
    {
        if ($this->getMethod() !== $expected) {
            $this->error('Método não permitido', 405);
        }
    }

    /**
     * Obtém dados da requisição POST
     */
    protected function getPostData(): array
    {
        return $_POST ?? [];
    }

    /**
     * Obtém dados JSON da requisição
     */
    protected function getJsonData(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        return is_array($data) ? $data : [];
    }

    /**
     * Gera token CSRF
     */
    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Valida token CSRF
     */
    protected function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION[CSRF_TOKEN_NAME]) 
            && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
}


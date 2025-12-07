<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Classe Router
 * Gerencia rotas e direcionamento de requisições
 */
class Router
{
    protected array $routes = [];
    protected array $params = [];

    /**
     * Adiciona rota GET
     */
    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    /**
     * Adiciona rota POST
     */
    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    /**
     * Adiciona rota para qualquer método
     */
    public function any(string $path, string $controller, string $action): void
    {
        $this->addRoute('*', $path, $controller, $action);
    }

    /**
     * Adiciona rota
     */
    protected function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * Resolve a rota atual
     */
    public function resolve(): void
    {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach ($this->routes as $route) {
            // Verifica método
            if ($route['method'] !== '*' && $route['method'] !== $method) {
                continue;
            }

            // Converte padrão de rota para regex
            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                // Remove primeira entrada (match completo)
                array_shift($matches);
                
                // Parâmetros nomeados
                $this->params = $this->extractParams($route['path'], $matches);
                
                $this->dispatch($route['controller'], $route['action']);
                return;
            }
        }

        // Rota não encontrada
        $this->notFound();
    }

    /**
     * Converte padrão de rota para regex
     */
    protected function convertToRegex(string $path): string
    {
        // Primeiro substitui {param} por placeholders temporários
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '___PARAM___', $path);
        
        // Escapa caracteres especiais, mas mantém as barras
        $pattern = preg_quote($pattern, '#');
        // Remove escape das barras (elas são seguras no nosso caso)
        $pattern = str_replace('\\/', '/', $pattern);
        
        // Substitui placeholders por regex de captura
        $pattern = str_replace('___PARAM___', '([^/]+)', $pattern);
        
        return '#^' . $pattern . '$#';
    }

    /**
     * Extrai parâmetros nomeados
     */
    protected function extractParams(string $path, array $matches): array
    {
        $params = [];
        
        if (preg_match_all('/\\{([a-zA-Z0-9_]+)\\}/', $path, $paramNames)) {
            foreach ($paramNames[1] as $index => $name) {
                if (isset($matches[$index])) {
                    $params[$name] = $matches[$index];
                }
            }
        }
        
        return $params;
    }

    /**
     * Obtém URI da requisição
     */
    protected function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove base path se existir
        $basePath = '/mensalidade';
        
        // Se a URI começa com o base path, remove
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Remove /public se existir na URI
        if (strpos($uri, '/public') === 0) {
            $uri = substr($uri, 7);
        }
        
        // Garante que começa com /
        if ($uri === '' || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        return rtrim($uri, '/') ?: '/';
    }

    /**
     * Despacha para controller e action
     */
    protected function dispatch(string $controller, string $action): void
    {
        $controllerClass = "App\\Controllers\\{$controller}";
        
        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }
        
        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $action)) {
            $this->notFound();
            return;
        }
        
        // Passa parâmetros para o método
        // Se houver parâmetros, converte array associativo para valores ordenados
        if (!empty($this->params)) {
            try {
                $reflection = new \ReflectionMethod($controllerInstance, $action);
                $parameters = $reflection->getParameters();
                $args = [];
                
                foreach ($parameters as $param) {
                    $paramName = $param->getName();
                    if (isset($this->params[$paramName])) {
                        $args[] = $this->params[$paramName];
                    } elseif ($param->isOptional()) {
                        $args[] = $param->getDefaultValue();
                    } else {
                        // Parâmetro obrigatório não encontrado
                        $this->notFound();
                        return;
                    }
                }
                
                call_user_func_array([$controllerInstance, $action], $args);
            } catch (\ReflectionException $e) {
                // Se não conseguir fazer reflection, tenta passar os parâmetros diretamente
                call_user_func_array([$controllerInstance, $action], array_values($this->params));
            }
        } else {
            call_user_func([$controllerInstance, $action]);
        }
    }

    /**
     * Retorna parâmetros da rota
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Retorna 404
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        echo "404 - Página não encontrada<br>";
        echo "URI: " . htmlspecialchars($uri) . "<br>";
        echo "Method: " . htmlspecialchars($method) . "<br>";
        echo "Rotas registradas: " . count($this->routes) . "<br>";
        exit;
    }
}


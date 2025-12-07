<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Classe View
 * Gerencia renderização de templates
 */
class View
{
    protected string $viewsPath;
    protected array $data = [];

    public function __construct()
    {
        $this->viewsPath = APP_PATH . '/Views';
    }

    /**
     * Define dados para a view
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Define múltiplos dados
     */
    public function with(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Renderiza view
     */
    public function render(string $template, array $data = []): string
    {
        $data = array_merge($this->data, $data);
        
        // Extrai variáveis para o escopo da view
        extract($data, EXTR_SKIP);
        
        $templatePath = $this->viewsPath . '/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("View não encontrada: {$template}");
        }
        
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * Renderiza view com layout (header + footer)
     */
    public function renderWithLayout(string $layout, array $data = []): string
    {
        $layoutPath = $this->viewsPath . '/' . $layout . '.php';
        
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout não encontrado: {$layout}");
        }
        
        extract(array_merge($this->data, $data), EXTR_SKIP);
        
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }
}


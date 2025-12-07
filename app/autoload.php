<?php

/**
 * Autoloader simples baseado em PSR-4
 * Funciona sem Composer
 */

spl_autoload_register(function ($class) {
    // Remove o namespace base
    $prefix = 'App\\';
    
    // Verifica se a classe usa o namespace App
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Obtém o nome relativo da classe
    $relativeClass = substr($class, $len);
    
    // Define o caminho base (app/) - funciona mesmo sem APP_PATH definido
    $baseDir = __DIR__ . '/';
    
    // Converte namespace para caminho de arquivo
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // Se o arquivo existe, carrega
    if (file_exists($file)) {
        require $file;
    }
});


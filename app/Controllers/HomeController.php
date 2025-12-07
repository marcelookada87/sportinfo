<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

/**
 * Controller da página inicial
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $content = $this->view->render('home/index', []);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Bem-vindo ao Sistema de Escola de Esportes',
            'content' => $content
        ]);
    }
}


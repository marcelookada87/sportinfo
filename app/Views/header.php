<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($title ?? 'Sistema de Escola de Esportes', ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- CSS Local -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/forms.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/tables.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/modals.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/tooltips.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/sidebar.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/alunos.css">
    <?php if (strpos($_SERVER['REQUEST_URI'] ?? '', '/alunos') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', '/professores') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', '/modalidades') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', '/planos') !== false): ?>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/datatables/css/dataTables.css">
    <?php endif; ?>
</head>
<body>
    <!-- Skip link para navegação por teclado (WCAG 2.4.1) -->
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
    
    <?php 
    // Helper para verificar página ativa
    $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
    $currentPath = parse_url($currentUri, PHP_URL_PATH);
    $basePath = '/mensalidade';
    if (strpos($currentPath, $basePath) === 0) {
        $currentPath = substr($currentPath, strlen($basePath));
    }
    $currentPath = rtrim($currentPath, '/') ?: '/';
    
    function isActive($path, $currentPath) {
        if ($path === '/' && $currentPath === '/') return true;
        if ($path !== '/' && strpos($currentPath, $path) === 0) return true;
        return false;
    }
    ?>
    
    <?php if (!empty($usuario)): ?>
        <div class="sidebar-container">
            <!-- Sidebar -->
            <aside class="sidebar" role="navigation" aria-label="Menu lateral">
                <div class="sidebar-header">
                    <h1 class="sidebar-logo">
                        <span class="sidebar-logo-icon"></span>
                        <span>Escola Esportes</span>
                    </h1>
                    <button class="sidebar-toggle" aria-label="Toggle menu" onclick="toggleSidebar()">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
                
                <nav class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/dashboard" class="sidebar-menu-link <?= isActive('/dashboard', $currentPath) || $currentPath === '/' ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-dashboard"></span>
                            <span class="sidebar-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/alunos" class="sidebar-menu-link <?= isActive('/alunos', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-users"></span>
                            <span class="sidebar-menu-text">Alunos</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/professores" class="sidebar-menu-link <?= isActive('/professores', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-teacher"></span>
                            <span class="sidebar-menu-text">Professores</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/turmas" class="sidebar-menu-link <?= isActive('/turmas', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-class"></span>
                            <span class="sidebar-menu-text">Turmas</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/modalidades" class="sidebar-menu-link <?= isActive('/modalidades', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-sport"></span>
                            <span class="sidebar-menu-text">Modalidades</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/planos" class="sidebar-menu-link <?= isActive('/planos', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-plan"></span>
                            <span class="sidebar-menu-text">Planos</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/matriculas" class="sidebar-menu-link <?= isActive('/matriculas', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-enrollment"></span>
                            <span class="sidebar-menu-text">Matrículas</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/financeiro" class="sidebar-menu-link <?= isActive('/financeiro', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-money"></span>
                            <span class="sidebar-menu-text">Financeiro</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="<?= BASE_URL ?>/relatorios" class="sidebar-menu-link <?= isActive('/relatorios', $currentPath) ? 'active' : '' ?>">
                            <span class="sidebar-menu-icon icon-chart"></span>
                            <span class="sidebar-menu-text">Relatórios</span>
                        </a>
                    </li>
                </nav>
            </aside>
            
            <!-- Overlay para mobile -->
            <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
            
            <!-- Main Content -->
            <div class="main-content-wrapper">
                <header class="main-content-header" role="banner">
                    <h2 class="main-content-title"><?= htmlspecialchars($title ?? 'Sistema de Escola de Esportes', ENT_QUOTES, 'UTF-8') ?></h2>
                    
                    <!-- User Menu -->
                    <div class="top-user-menu">
                        <div class="top-user-info" onclick="toggleUserMenu()">
                            <div class="top-user-avatar">
                                <?= strtoupper(substr($usuario['nome'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="top-user-details">
                                <div class="top-user-name"><?= htmlspecialchars($usuario['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="top-user-role"><?= htmlspecialchars($usuario['perfil'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <span class="top-user-arrow">▼</span>
                        </div>
                        <div class="top-user-dropdown" id="userDropdown">
                            <a href="<?= BASE_URL ?>/perfil" class="top-user-link">
                                <span class="top-user-icon icon-settings"></span>
                                <span>Perfil</span>
                            </a>
                            <a href="<?= BASE_URL ?>/logout" class="top-user-link">
                                <span class="top-user-icon icon-logout"></span>
                                <span>Sair</span>
                            </a>
                        </div>
                    </div>
                </header>
                
                <main id="main-content" class="main-content-body" role="main">
    <?php else: ?>
        <main id="main-content" role="main">
    <?php endif; ?>


<?php
/**
 * Layout para páginas de autenticação (login)
 * Layout simplificado sem header/footer completo
 */
if (!isset($title)) {
    $title = 'Login - Sistema de Escola de Esportes';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- CSS Local -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/forms.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/auth.css">
</head>
<body class="auth-page">
    <?php if (isset($content)): ?>
        <?= $content ?>
    <?php endif; ?>
    
    <!-- JavaScript Local -->
    <script src="<?= ASSETS_URL ?>/js/utils.js"></script>
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
</body>
</html>


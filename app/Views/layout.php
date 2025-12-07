<?php
/**
 * Layout principal do sistema
 * Estrutura: header.php -> conteúdo -> footer.php -> scripts
 */
if (!isset($title)) {
    $title = 'Sistema de Escola de Esportes';
}

// Inclui header
include __DIR__ . '/header.php';
?>

<?php if (isset($content)): ?>
    <?= $content ?>
<?php endif; ?>

<?php
// Inclui footer (que contém os scripts JS)
include __DIR__ . '/footer.php';
?>


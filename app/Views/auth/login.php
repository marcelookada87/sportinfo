<div class="login-container">
    <div class="login-header">
        <h1>Sistema de Escola de Esportes</h1>
        <p>Faça login para acessar o sistema</p>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="login-error" role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/login" class="login-form" data-validate role="form" aria-label="Formulário de login">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
        
        <div class="form-group">
            <label for="email">E-mail</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                required 
                autocomplete="email"
                aria-required="true"
                placeholder="seu@email.com"
                autofocus
            >
        </div>
        
        <div class="form-group">
            <label for="senha">Senha</label>
            <input 
                type="password" 
                id="senha" 
                name="senha" 
                required 
                autocomplete="current-password"
                aria-required="true"
                placeholder="••••••••"
            >
        </div>
        
        <button type="submit" class="btn-login">Entrar</button>
    </form>

    <div class="login-footer">
        <p>&copy; <?= date('Y') ?> Sistema de Escola de Esportes</p>
    </div>
</div>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Cadastrar Nova Modalidade</h1>
        <p class="page-subtitle">Preencha os dados da modalidade</p>
    </div>
    <a href="<?= BASE_URL ?>/modalidades" class="btn btn-secondary">Voltar</a>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>
    <div class="alert alert-error">
        <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dados da Modalidade</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/modalidades" data-validate>
            <?php
            // Gera token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="nome" class="form-label">
                    Nome da Modalidade <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    class="form-control" 
                    required
                    value="<?= htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Natação, Futebol, Lutas, etc."
                >
                <small class="form-text">Nome da modalidade esportiva</small>
            </div>

            <div class="form-group">
                <label for="categoria_etaria" class="form-label">Categoria Etária</label>
                <input 
                    type="text" 
                    id="categoria_etaria" 
                    name="categoria_etaria" 
                    class="form-control"
                    value="<?= htmlspecialchars($_POST['categoria_etaria'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Infantil (5-10 anos), Juvenil (11-17 anos), Adulto (18+), Todas as idades"
                >
                <small class="form-text">Faixa etária recomendada para esta modalidade</small>
            </div>

            <div class="form-group form-group-full">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea 
                    id="descricao" 
                    name="descricao" 
                    class="form-control"
                    rows="5"
                    placeholder="Descreva a modalidade, suas características, benefícios, requisitos, etc."
                ><?= htmlspecialchars($_POST['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <small class="form-text">Informações detalhadas sobre a modalidade</small>
            </div>

            <div class="form-group">
                <label for="ativo" class="form-label">Status</label>
                <select id="ativo" name="ativo" class="form-control">
                    <option value="1" <?= (!isset($_POST['ativo']) || $_POST['ativo'] == '1') ? 'selected' : '' ?>>Ativa</option>
                    <option value="0" <?= (isset($_POST['ativo']) && $_POST['ativo'] == '0') ? 'selected' : '' ?>>Inativa</option>
                </select>
                <small class="form-text">Modalidades inativas não aparecerão nas opções de cadastro</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar Modalidade</button>
                <a href="<?= BASE_URL ?>/modalidades" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>


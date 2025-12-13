<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Editar Mensalidade</h1>
        <p class="page-subtitle">Atualize os dados da mensalidade</p>
    </div>
    <a href="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>" class="btn btn-secondary">Voltar</a>
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
        <h3 class="card-title">Dados da Mensalidade</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>" data-validate>
            <?php
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="matricula_id" value="<?= $mensalidade['matricula_id'] ?>">
            
            <div class="form-group">
                <label class="form-label">Aluno</label>
                <input 
                    type="text" 
                    class="form-control" 
                    value="<?= htmlspecialchars($mensalidade['aluno_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                    disabled
                >
                <small class="form-text">Matrícula não pode ser alterada</small>
            </div>

            <div class="form-group">
                <label for="competencia" class="form-label">
                    Competência <span class="required">*</span>
                </label>
                <input 
                    type="month" 
                    id="competencia" 
                    name="competencia" 
                    class="form-control" 
                    required
                    value="<?= htmlspecialchars($mensalidade['competencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Mês e ano de referência (formato: YYYY-MM)</small>
            </div>

            <div class="form-group">
                <label for="valor" class="form-label">
                    Valor (R$) <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    id="valor" 
                    name="valor" 
                    class="form-control" 
                    required
                    step="0.01"
                    min="0.01"
                    value="<?= htmlspecialchars($mensalidade['valor'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0.00"
                >
                <small class="form-text">Valor base da mensalidade</small>
            </div>

            <div class="form-group">
                <label for="desconto" class="form-label">Desconto (R$)</label>
                <input 
                    type="number" 
                    id="desconto" 
                    name="desconto" 
                    class="form-control" 
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars($mensalidade['desconto'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0.00"
                >
                <small class="form-text">Valor de desconto a ser aplicado</small>
            </div>

            <div class="form-group">
                <label for="multa" class="form-label">Multa (R$)</label>
                <input 
                    type="number" 
                    id="multa" 
                    name="multa" 
                    class="form-control" 
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars($mensalidade['multa'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0.00"
                >
                <small class="form-text">Valor de multa (se houver)</small>
            </div>

            <div class="form-group">
                <label for="juros" class="form-label">Juros (R$)</label>
                <input 
                    type="number" 
                    id="juros" 
                    name="juros" 
                    class="form-control" 
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars($mensalidade['juros'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0.00"
                >
                <small class="form-text">Valor de juros (se houver)</small>
            </div>

            <div class="form-group">
                <label for="dt_vencimento" class="form-label">
                    Data de Vencimento <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    id="dt_vencimento" 
                    name="dt_vencimento" 
                    class="form-control" 
                    required
                    value="<?= htmlspecialchars($mensalidade['dt_vencimento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Data limite para pagamento</small>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="Aberto" <?= ($mensalidade['status'] ?? '') === 'Aberto' ? 'selected' : '' ?>>Aberto</option>
                    <option value="Pago" <?= ($mensalidade['status'] ?? '') === 'Pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="Atrasado" <?= ($mensalidade['status'] ?? '') === 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                    <option value="Cancelado" <?= ($mensalidade['status'] ?? '') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
                <small class="form-text">Status da mensalidade</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar Mensalidade</button>
                <a href="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>


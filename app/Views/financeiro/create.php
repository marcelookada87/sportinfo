<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Cadastrar Nova Mensalidade</h1>
        <p class="page-subtitle">Preencha os dados da mensalidade</p>
    </div>
    <a href="<?= BASE_URL ?>/financeiro" class="btn btn-secondary">Voltar</a>
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
        <form method="POST" action="<?= BASE_URL ?>/financeiro" data-validate>
            <?php
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="matricula_id" class="form-label">
                    Matrícula <span class="required">*</span>
                </label>
                <select id="matricula_id" name="matricula_id" class="form-control" required>
                    <option value="">Selecione a matrícula...</option>
                    <?php foreach ($matriculas as $matricula): ?>
                        <option 
                            value="<?= $matricula['id'] ?>" 
                            data-valor-base="<?= htmlspecialchars($matricula['plano_valor_base'], ENT_QUOTES, 'UTF-8') ?>"
                            <?= (isset($_POST['matricula_id']) && $_POST['matricula_id'] == $matricula['id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($matricula['aluno_nome'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($matricula['aluno_cpf'])): ?>
                                - CPF: <?= htmlspecialchars($matricula['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                            - <?= htmlspecialchars($matricula['plano_nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Selecione a matrícula do aluno</small>
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
                    value="<?= htmlspecialchars($_POST['competencia'] ?? date('Y-m'), ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($_POST['valor'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($_POST['desconto'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($_POST['multa'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($_POST['juros'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($_POST['dt_vencimento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Data limite para pagamento</small>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="Aberto" <?= (!isset($_POST['status']) || $_POST['status'] == 'Aberto') ? 'selected' : '' ?>>Aberto</option>
                    <option value="Pago" <?= (isset($_POST['status']) && $_POST['status'] == 'Pago') ? 'selected' : '' ?>>Pago</option>
                    <option value="Atrasado" <?= (isset($_POST['status']) && $_POST['status'] == 'Atrasado') ? 'selected' : '' ?>>Atrasado</option>
                    <option value="Cancelado" <?= (isset($_POST['status']) && $_POST['status'] == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                </select>
                <small class="form-text">Status inicial da mensalidade</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar Mensalidade</button>
                <a href="<?= BASE_URL ?>/financeiro" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const matriculaSelect = document.getElementById('matricula_id');
    const valorInput = document.getElementById('valor');
    
    matriculaSelect.addEventListener('change', function() {
        const selectedOption = matriculaSelect.options[matriculaSelect.selectedIndex];
        const valorBase = parseFloat(selectedOption.getAttribute('data-valor-base')) || 0;
        
        if (valorBase > 0 && !valorInput.value) {
            valorInput.value = valorBase.toFixed(2);
        }
    });
});
</script>


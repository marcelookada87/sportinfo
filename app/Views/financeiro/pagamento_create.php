<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Registrar Pagamento</h1>
        <p class="page-subtitle">Registre o pagamento da mensalidade</p>
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

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Informações da Mensalidade</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <small style="color: var(--text-secondary);">Aluno</small>
                <div><strong><?= htmlspecialchars($mensalidade['aluno_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></div>
            </div>
            <div>
                <small style="color: var(--text-secondary);">Competência</small>
                <div><span class="badge badge-info"><?= htmlspecialchars($mensalidade['competencia'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></div>
            </div>
            <div>
                <small style="color: var(--text-secondary);">Valor Total</small>
                <div><strong style="font-size: 1.2rem; color: var(--primary-color);">R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong></div>
            </div>
            <div>
                <small style="color: var(--text-secondary);">Total Pago</small>
                <div><strong style="color: var(--success-color);">R$ <?= number_format($totalPago, 2, ',', '.') ?></strong></div>
            </div>
            <div>
                <small style="color: var(--text-secondary);">Valor Restante</small>
                <div><strong style="color: var(--<?= $valorRestante > 0 ? 'error' : 'success' ?>-color);">R$ <?= number_format($valorRestante, 2, ',', '.') ?></strong></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dados do Pagamento</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/financeiro/pagamento" data-validate>
            <?php
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="mensalidade_id" value="<?= $mensalidade['id'] ?>">
            
            <div class="form-group">
                <label for="forma" class="form-label">
                    Forma de Pagamento <span class="required">*</span>
                </label>
                <select id="forma" name="forma" class="form-control" required>
                    <option value="">Selecione...</option>
                    <option value="PIX" <?= (isset($_POST['forma']) && $_POST['forma'] === 'PIX') ? 'selected' : '' ?>>PIX</option>
                    <option value="Cartão" <?= (isset($_POST['forma']) && $_POST['forma'] === 'Cartão') ? 'selected' : '' ?>>Cartão</option>
                    <option value="Dinheiro" <?= (isset($_POST['forma']) && $_POST['forma'] === 'Dinheiro') ? 'selected' : '' ?>>Dinheiro</option>
                    <option value="Boleto" <?= (isset($_POST['forma']) && $_POST['forma'] === 'Boleto') ? 'selected' : '' ?>>Boleto</option>
                </select>
                <small class="form-text">Forma de pagamento utilizada</small>
            </div>

            <div class="form-group">
                <label for="valor_pago" class="form-label">
                    Valor Pago (R$) <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    id="valor_pago" 
                    name="valor_pago" 
                    class="form-control" 
                    required
                    step="0.01"
                    min="0.01"
                    max="<?= $valorRestante ?>"
                    value="<?= htmlspecialchars($_POST['valor_pago'] ?? ($valorRestante > 0 ? number_format($valorRestante, 2, '.', '') : ''), ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0.00"
                >
                <small class="form-text">Valor máximo: R$ <?= number_format($valorRestante, 2, ',', '.') ?></small>
            </div>

            <div class="form-group">
                <label for="dt_pagamento" class="form-label">Data do Pagamento</label>
                <input 
                    type="datetime-local" 
                    id="dt_pagamento" 
                    name="dt_pagamento" 
                    class="form-control" 
                    value="<?= htmlspecialchars($_POST['dt_pagamento'] ?? date('Y-m-d\TH:i'), ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Data e hora do pagamento (deixe em branco para usar data/hora atual)</small>
            </div>

            <div class="form-group">
                <label for="transacao_ref" class="form-label">Referência da Transação</label>
                <input 
                    type="text" 
                    id="transacao_ref" 
                    name="transacao_ref" 
                    class="form-control" 
                    value="<?= htmlspecialchars($_POST['transacao_ref'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Código PIX, número do boleto, etc."
                >
                <small class="form-text">Código ou referência da transação (opcional)</small>
            </div>

            <div class="form-group">
                <label for="conciliado" class="form-label">Status de Conciliação</label>
                <select id="conciliado" name="conciliado" class="form-control">
                    <option value="0" <?= (!isset($_POST['conciliado']) || $_POST['conciliado'] == '0') ? 'selected' : '' ?>>Não Conciliado</option>
                    <option value="1" <?= (isset($_POST['conciliado']) && $_POST['conciliado'] == '1') ? 'selected' : '' ?>>Conciliado</option>
                </select>
                <small class="form-text">Indica se o pagamento já foi conciliado</small>
            </div>

            <div class="form-group form-group-full">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea 
                    id="observacoes" 
                    name="observacoes" 
                    class="form-control"
                    rows="3"
                    placeholder="Observações adicionais sobre o pagamento"
                ><?= htmlspecialchars($_POST['observacoes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <small class="form-text">Informações adicionais sobre o pagamento</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar Pagamento</button>
                <a href="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const valorPagoInput = document.getElementById('valor_pago');
    const valorRestante = <?= $valorRestante ?>;
    
    valorPagoInput.addEventListener('input', function() {
        const valor = parseFloat(this.value) || 0;
        if (valor > valorRestante) {
            this.setCustomValidity('Valor não pode ser maior que o valor restante (R$ <?= number_format($valorRestante, 2, ',', '.') ?>)');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>


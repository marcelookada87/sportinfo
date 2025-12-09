<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Cadastrar Novo Plano</h1>
        <p class="page-subtitle">Preencha os dados do plano de mensalidade</p>
    </div>
    <a href="<?= BASE_URL ?>/planos" class="btn btn-secondary">Voltar</a>
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
        <h3 class="card-title">Dados do Plano</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/planos" data-validate>
            <?php
            // Gera token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="nome" class="form-label">
                    Nome do Plano <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    class="form-control" 
                    required
                    value="<?= htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Plano Mensal, Plano Trimestral, Plano Anual"
                >
                <small class="form-text">Nome identificador do plano</small>
            </div>

            <div class="form-group">
                <label for="periodicidade" class="form-label">
                    Periodicidade <span class="required">*</span>
                </label>
                <select id="periodicidade" name="periodicidade" class="form-control" required>
                    <option value="">Selecione...</option>
                    <option value="mensal" <?= ($_POST['periodicidade'] ?? '') === 'mensal' ? 'selected' : '' ?>>Mensal</option>
                    <option value="trimestral" <?= ($_POST['periodicidade'] ?? '') === 'trimestral' ? 'selected' : '' ?>>Trimestral (3 meses)</option>
                    <option value="anual" <?= ($_POST['periodicidade'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual (12 meses)</option>
                </select>
                <small class="form-text">Frequência de pagamento do plano</small>
            </div>

            <div class="form-group">
                <label for="valor_base" class="form-label">
                    Valor Base (R$) <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    id="valor_base" 
                    name="valor_base" 
                    class="form-control" 
                    required
                    step="0.01"
                    min="0.01"
                    value="<?= htmlspecialchars($_POST['valor_base'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0.00"
                >
                <small class="form-text">
                    Valor total do plano. O sistema calculará automaticamente o valor mensal:
                    <span id="valorMensalPreview" style="color: var(--success-color); font-weight: 600; margin-left: 0.5rem;"></span>
                </small>
            </div>

            <div class="form-group form-group-full">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea 
                    id="descricao" 
                    name="descricao" 
                    class="form-control"
                    rows="4"
                    placeholder="Descreva o plano, benefícios, condições, etc."
                ><?= htmlspecialchars($_POST['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <small class="form-text">Informações detalhadas sobre o plano</small>
            </div>

            <div class="form-group">
                <label for="ativo" class="form-label">Status</label>
                <select id="ativo" name="ativo" class="form-control">
                    <option value="1" <?= (!isset($_POST['ativo']) || $_POST['ativo'] == '1') ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= (isset($_POST['ativo']) && $_POST['ativo'] == '0') ? 'selected' : '' ?>>Inativo</option>
                </select>
                <small class="form-text">Planos inativos não aparecerão nas opções de matrícula</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar Plano</button>
                <a href="<?= BASE_URL ?>/planos" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const periodicidadeSelect = document.getElementById('periodicidade');
    const valorBaseInput = document.getElementById('valor_base');
    const valorMensalPreview = document.getElementById('valorMensalPreview');
    
    function calcularValorMensal() {
        const periodicidade = periodicidadeSelect.value;
        const valorBase = parseFloat(valorBaseInput.value) || 0;
        
        if (valorBase > 0 && periodicidade) {
            let valorMensal = 0;
            switch(periodicidade) {
                case 'mensal':
                    valorMensal = valorBase;
                    break;
                case 'trimestral':
                    valorMensal = valorBase / 3;
                    break;
                case 'anual':
                    valorMensal = valorBase / 12;
                    break;
            }
            
            if (valorMensal > 0) {
                valorMensalPreview.textContent = 'R$ ' + valorMensal.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' /mês';
            } else {
                valorMensalPreview.textContent = '';
            }
        } else {
            valorMensalPreview.textContent = '';
        }
    }
    
    periodicidadeSelect.addEventListener('change', calcularValorMensal);
    valorBaseInput.addEventListener('input', calcularValorMensal);
})();
</script>


<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Configurações Financeiras</h1>
        <p class="page-subtitle">Configure multa e juros para mensalidades vencidas</p>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Configurações de Multa e Juros</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/configuracoes" data-validate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            
            <!-- Seção de Multa -->
            <div class="form-section">
                <h4 class="form-section-title">Multa</h4>
                <p class="form-section-description">
                    Configure como a multa será calculada quando a mensalidade estiver vencida.
                </p>
                
                <div class="form-group">
                    <label for="multa_tipo" class="form-label">
                        Tipo de Cálculo <span class="required">*</span>
                    </label>
                    <select id="multa_tipo" name="multa_tipo" class="form-control" required>
                        <option value="fixo" <?= $multa_tipo === 'fixo' ? 'selected' : '' ?>>Valor Fixo (R$)</option>
                        <option value="porcentagem" <?= $multa_tipo === 'porcentagem' ? 'selected' : '' ?>>Porcentagem (%)</option>
                    </select>
                    <small class="form-text">
                        <strong>Valor Fixo:</strong> Aplicará um valor fixo em reais para todas as mensalidades vencidas.<br>
                        <strong>Porcentagem:</strong> Aplicará uma porcentagem sobre o valor da mensalidade.
                    </small>
                </div>

                <div class="form-group">
                    <label for="multa_valor" class="form-label">
                        Valor da Multa <span class="required">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="multa_valor" 
                        name="multa_valor" 
                        class="form-control" 
                        required
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars(number_format($multa_valor, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="0.00"
                    >
                    <small class="form-text" id="multa_help">
                        <span id="multa_help_fixo" style="display: <?= $multa_tipo === 'fixo' ? 'inline' : 'none' ?>;">
                            Valor fixo em reais (R$) a ser aplicado em cada mensalidade vencida.
                        </span>
                        <span id="multa_help_porcentagem" style="display: <?= $multa_tipo === 'porcentagem' ? 'inline' : 'none' ?>;">
                            Porcentagem (%) sobre o valor da mensalidade. Exemplo: 2.00 = 2% do valor da mensalidade.
                        </span>
                    </small>
                </div>
            </div>

            <!-- Seção de Juros -->
            <div class="form-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <h4 class="form-section-title">Juros</h4>
                <p class="form-section-description">
                    Configure como os juros serão calculados quando a mensalidade estiver vencida.
                </p>
                
                <div class="form-group">
                    <label for="juros_tipo" class="form-label">
                        Tipo de Cálculo <span class="required">*</span>
                    </label>
                    <select id="juros_tipo" name="juros_tipo" class="form-control" required>
                        <option value="fixo" <?= $juros_tipo === 'fixo' ? 'selected' : '' ?>>Valor Fixo por Dia (R$)</option>
                        <option value="porcentagem" <?= $juros_tipo === 'porcentagem' ? 'selected' : '' ?>>Porcentagem ao Mês (%)</option>
                    </select>
                    <small class="form-text">
                        <strong>Valor Fixo:</strong> Aplicará um valor fixo em reais por cada dia de atraso.<br>
                        <strong>Porcentagem:</strong> Aplicará uma porcentagem ao mês, calculada proporcionalmente aos dias de atraso.
                    </small>
                </div>

                <div class="form-group">
                    <label for="juros_valor" class="form-label">
                        Valor dos Juros <span class="required">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="juros_valor" 
                        name="juros_valor" 
                        class="form-control" 
                        required
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars(number_format($juros_valor, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="0.00"
                    >
                    <small class="form-text" id="juros_help">
                        <span id="juros_help_fixo" style="display: <?= $juros_tipo === 'fixo' ? 'inline' : 'none' ?>;">
                            Valor fixo em reais (R$) por cada dia de atraso. Exemplo: 0.50 = R$ 0,50 por dia.
                        </span>
                        <span id="juros_help_porcentagem" style="display: <?= $juros_tipo === 'porcentagem' ? 'inline' : 'none' ?>;">
                            Porcentagem ao mês (%) sobre o valor da mensalidade, calculada proporcionalmente aos dias. Exemplo: 0.33 = 0,33% ao mês (aproximadamente 1% ao mês).
                        </span>
                    </small>
                </div>
            </div>

            <!-- Seção de Carência -->
            <div class="form-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <h4 class="form-section-title">Carência</h4>
                <p class="form-section-description">
                    Configure quantos dias de carência antes de aplicar multa e juros.
                </p>
                
                <div class="form-group">
                    <label for="dias_carencia" class="form-label">
                        Dias de Carência <span class="required">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="dias_carencia" 
                        name="dias_carencia" 
                        class="form-control" 
                        required
                        min="0"
                        value="<?= htmlspecialchars($dias_carencia, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="0"
                    >
                    <small class="form-text">
                        Número de dias após o vencimento antes de aplicar multa e juros. 
                        Exemplo: Se configurado como 5, a multa e juros só serão aplicados após 5 dias do vencimento.
                    </small>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">
                    Salvar Configurações
                </button>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const multaTipo = document.getElementById('multa_tipo');
    const jurosTipo = document.getElementById('juros_tipo');
    
    // Atualiza ajuda da multa
    function updateMultaHelp() {
        const tipo = multaTipo.value;
        const helpFixo = document.getElementById('multa_help_fixo');
        const helpPorcentagem = document.getElementById('multa_help_porcentagem');
        
        if (tipo === 'fixo') {
            helpFixo.style.display = 'inline';
            helpPorcentagem.style.display = 'none';
        } else {
            helpFixo.style.display = 'none';
            helpPorcentagem.style.display = 'inline';
        }
    }
    
    // Atualiza ajuda dos juros
    function updateJurosHelp() {
        const tipo = jurosTipo.value;
        const helpFixo = document.getElementById('juros_help_fixo');
        const helpPorcentagem = document.getElementById('juros_help_porcentagem');
        
        if (tipo === 'fixo') {
            helpFixo.style.display = 'inline';
            helpPorcentagem.style.display = 'none';
        } else {
            helpFixo.style.display = 'none';
            helpPorcentagem.style.display = 'inline';
        }
    }
    
    multaTipo.addEventListener('change', updateMultaHelp);
    jurosTipo.addEventListener('change', updateJurosHelp);
    
    // Inicializa
    updateMultaHelp();
    updateJurosHelp();
});
</script>

<style>
.form-section {
    margin-bottom: 2rem;
}

.form-section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary, #1f2937);
    margin-bottom: 0.5rem;
}

.form-section-description {
    color: var(--text-secondary, #6b7280);
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}
</style>

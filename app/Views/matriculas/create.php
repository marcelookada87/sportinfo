<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Cadastrar Nova Matrícula</h1>
        <p class="page-subtitle">Matricule um aluno em uma ou múltiplas turmas</p>
    </div>
    <a href="<?= BASE_URL ?>/matriculas" class="btn btn-secondary">Voltar</a>
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

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 56px;
    height: 28px;
    cursor: pointer;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    border-radius: 28px;
    transition: 0.3s;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.toggle-slider:before {
    content: '';
    position: absolute;
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.toggle-switch input:checked + .toggle-slider {
    background-color: var(--primary-color, #4CAF50);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(28px);
}

.toggle-switch:hover .toggle-slider {
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.15), 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.toggle-switch input:checked + .toggle-slider:hover {
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.15), 0 0 0 3px rgba(76, 175, 80, 0.2);
}

.toggle-multiple-classes {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, rgba(76, 175, 80, 0.02) 100%);
    border-radius: 0.5rem;
    border: 1px solid rgba(76, 175, 80, 0.1);
    transition: all 0.3s ease;
}

.toggle-multiple-classes:hover {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(76, 175, 80, 0.04) 100%);
    border-color: rgba(76, 175, 80, 0.2);
}

.toggle-multiple-classes.active {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.12) 0%, rgba(76, 175, 80, 0.06) 100%);
    border-color: rgba(76, 175, 80, 0.3);
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .toggle-multiple-classes {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3 class="card-title" style="margin: 0;">Dados da Matrícula</h3>
        <div class="toggle-multiple-classes" id="toggleContainer">
            <label for="modoMultiplo" class="toggle-switch">
                <input type="checkbox" id="modoMultiplo" onchange="toggleModoMatricula()">
                <span class="toggle-slider"></span>
            </label>
            <div style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1;">
                <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-primary);">
                    Matricular em múltiplas turmas
                </span>
                <small style="color: var(--text-secondary); font-size: 0.8rem;">
                    <span id="toggleHint">Selecione uma turma por vez</span>
                </small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/matriculas" id="formMatricula" data-validate>
            <?php
            // Gera token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="aluno_id" class="form-label">
                    Aluno <span class="required">*</span>
                </label>
                <select id="aluno_id" name="aluno_id" class="form-control" required>
                    <option value="">Selecione o aluno...</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>" <?= (isset($_POST['aluno_id']) && $_POST['aluno_id'] == $aluno['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($aluno['cpf'])): ?>
                                - CPF: <?= htmlspecialchars($aluno['cpf'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Selecione o aluno que será matriculado</small>
            </div>

            <!-- Seleção Única (padrão) -->
            <div id="selecaoUnica" class="form-group" style="transition: opacity 0.3s ease, transform 0.3s ease;">
                <label for="turma_id" class="form-label">
                    Turma <span class="required">*</span>
                </label>
                <select id="turma_id" name="turma_id" class="form-control">
                    <option value="">Selecione a turma...</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?= $turma['id'] ?>" <?= (isset($_POST['turma_id']) && $_POST['turma_id'] == $turma['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($turma['modalidade_nome'], ENT_QUOTES, 'UTF-8') ?> - 
                            <?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($turma['professor_nome'])): ?>
                                (Prof: <?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Selecione a turma em que o aluno será matriculado</small>
            </div>

            <!-- Seleção Múltipla -->
            <div id="selecaoMultipla" class="form-group" style="display: none; transition: opacity 0.3s ease, transform 0.3s ease;">
                <label class="form-label">
                    Turmas <span class="required">*</span>
                </label>
                <div style="max-height: 300px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 0.25rem; padding: 1rem; background-color: var(--bg-primary);">
                    <?php 
                    // Agrupa turmas por modalidade
                    $turmasPorModalidade = [];
                    foreach ($turmas as $turma) {
                        $modalidade = $turma['modalidade_nome'];
                        if (!isset($turmasPorModalidade[$modalidade])) {
                            $turmasPorModalidade[$modalidade] = [];
                        }
                        $turmasPorModalidade[$modalidade][] = $turma;
                    }
                    ?>
                    <?php foreach ($turmasPorModalidade as $modalidade => $turmasModalidade): ?>
                        <div style="margin-bottom: 1.5rem;">
                            <strong style="display: block; margin-bottom: 0.75rem; color: var(--primary-color); font-size: 1rem;">
                                <?= htmlspecialchars($modalidade, ENT_QUOTES, 'UTF-8') ?>
                            </strong>
                            <?php foreach ($turmasModalidade as $turma): ?>
                                <?php
                                $diasArray = !empty($turma['dias_array']) ? $turma['dias_array'] : 
                                            (!empty($turma['dias_da_semana']) ? json_decode($turma['dias_da_semana'], true) : []);
                                $diasTexto = is_array($diasArray) && !empty($diasArray) ? implode(', ', $diasArray) : '';
                                ?>
                                <label style="display: flex; align-items: start; gap: 0.75rem; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.25rem; margin-bottom: 0.5rem; cursor: pointer; transition: background-color 0.2s;"
                                       onmouseover="this.style.backgroundColor='var(--bg-secondary)'" 
                                       onmouseout="this.style.backgroundColor='transparent'">
                                    <input 
                                        type="checkbox" 
                                        name="turmas[]" 
                                        value="<?= $turma['id'] ?>"
                                        data-modalidade-id="<?= $turma['modalidade_id'] ?? '' ?>"
                                        data-modalidade-nome="<?= htmlspecialchars($modalidade, ENT_QUOTES, 'UTF-8') ?>"
                                        class="turma-checkbox"
                                        style="margin-top: 0.25rem; width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;"
                                        onchange="validarModalidade(this)"
                                    >
                                    <div style="flex: 1;">
                                        <div style="font-weight: 500; margin-bottom: 0.25rem;">
                                            <?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                            <?php if (!empty($turma['professor_nome'])): ?>
                                                <span>Prof: <?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php if ($diasTexto || $turma['hora_inicio']): ?> • <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if ($diasTexto): ?>
                                                <span><?= htmlspecialchars($diasTexto, ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php if ($turma['hora_inicio']): ?> • <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if ($turma['hora_inicio']): ?>
                                                <span><?= date('H:i', strtotime($turma['hora_inicio'])) ?> - <?= date('H:i', strtotime($turma['hora_fim'])) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <small class="form-text">Selecione uma ou mais turmas em que o aluno será matriculado</small>
                <div id="contadorTurmas" style="margin-top: 0.5rem; color: var(--primary-color); font-weight: 500; display: none;">
                    <span id="totalSelecionadas">0</span> turma(s) selecionada(s)
                </div>
            </div>

            <div class="form-group">
                <label for="plano_id" class="form-label">
                    Plano <span class="required">*</span>
                </label>
                <select id="plano_id" name="plano_id" class="form-control" required>
                    <option value="">Selecione o plano...</option>
                    <?php foreach ($planos as $plano): ?>
                        <option 
                            value="<?= $plano['id'] ?>" 
                            data-quantidade-meses="<?= htmlspecialchars($plano['quantidade_meses'] ?? 1, ENT_QUOTES, 'UTF-8') ?>"
                            <?= (isset($_POST['plano_id']) && $_POST['plano_id'] == $plano['id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($plano['nome'], ENT_QUOTES, 'UTF-8') ?>
                            <?php
                            $periodicidadeLabels = [
                                'mensal' => 'Mensal',
                                'trimestral' => 'Trimestral',
                                'anual' => 'Anual'
                            ];
                            $periodicidadeLabel = $periodicidadeLabels[$plano['periodicidade']] ?? ucfirst($plano['periodicidade']);
                            ?>
                            (<?= $periodicidadeLabel ?>) - 
                            R$ <?= number_format((float)$plano['valor_base'], 2, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Selecione o plano de pagamento</small>
            </div>

            <div class="form-group">
                <label for="dt_inicio" class="form-label">
                    Data de Início <span class="required">*</span>
                </label>
                <input 
                    type="date" 
                    id="dt_inicio" 
                    name="dt_inicio" 
                    class="form-control" 
                    required
                    value="<?= htmlspecialchars($_POST['dt_inicio'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Data em que a matrícula começa a valer</small>
            </div>

            <div class="form-group">
                <label for="dt_fim" class="form-label">Data de Término</label>
                <input 
                    type="date" 
                    id="dt_fim" 
                    name="dt_fim" 
                    class="form-control"
                    value="<?= htmlspecialchars($_POST['dt_fim'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Deixe em branco se a matrícula não tem data de término definida</small>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="Ativa" <?= (!isset($_POST['status']) || $_POST['status'] == 'Ativa') ? 'selected' : '' ?>>Ativa</option>
                    <option value="Suspensa" <?= (isset($_POST['status']) && $_POST['status'] == 'Suspensa') ? 'selected' : '' ?>>Suspensa</option>
                    <option value="Cancelada" <?= (isset($_POST['status']) && $_POST['status'] == 'Cancelada') ? 'selected' : '' ?>>Cancelada</option>
                    <option value="Finalizada" <?= (isset($_POST['status']) && $_POST['status'] == 'Finalizada') ? 'selected' : '' ?>>Finalizada</option>
                </select>
                <small class="form-text">Status inicial da matrícula</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="btnSubmit">Cadastrar Matrícula</button>
                <a href="<?= BASE_URL ?>/matriculas" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModoMatricula() {
    const modoMultiplo = document.getElementById('modoMultiplo').checked;
    const selecaoUnica = document.getElementById('selecaoUnica');
    const selecaoMultipla = document.getElementById('selecaoMultipla');
    const form = document.getElementById('formMatricula');
    const btnSubmit = document.getElementById('btnSubmit');
    const turmaSelect = document.getElementById('turma_id');
    const toggleContainer = document.getElementById('toggleContainer');
    const toggleHint = document.getElementById('toggleHint');
    
    if (modoMultiplo) {
        selecaoUnica.style.display = 'none';
        selecaoMultipla.style.display = 'block';
        turmaSelect.removeAttribute('required');
        form.action = '<?= BASE_URL ?>/matriculas/multiple';
        btnSubmit.textContent = 'Cadastrar Matrículas';
        toggleContainer.classList.add('active');
        toggleHint.textContent = 'Selecione quantas turmas desejar';
        
        // Animação suave
        selecaoMultipla.style.opacity = '0';
        selecaoMultipla.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            selecaoMultipla.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            selecaoMultipla.style.opacity = '1';
            selecaoMultipla.style.transform = 'translateY(0)';
        }, 10);
    } else {
        selecaoUnica.style.display = 'block';
        selecaoMultipla.style.display = 'none';
        turmaSelect.setAttribute('required', 'required');
        form.action = '<?= BASE_URL ?>/matriculas';
        btnSubmit.textContent = 'Cadastrar Matrícula';
        toggleContainer.classList.remove('active');
        toggleHint.textContent = 'Selecione uma turma por vez';
        
        // Limpa checkboxes
        document.querySelectorAll('.turma-checkbox').forEach(cb => cb.checked = false);
        atualizarContador();
        
        // Animação suave
        selecaoUnica.style.opacity = '0';
        selecaoUnica.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            selecaoUnica.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            selecaoUnica.style.opacity = '1';
            selecaoUnica.style.transform = 'translateY(0)';
        }, 10);
    }
}

function atualizarContador() {
    const checkboxes = document.querySelectorAll('.turma-checkbox:checked');
    const total = checkboxes.length;
    const contador = document.getElementById('contadorTurmas');
    const totalSelecionadas = document.getElementById('totalSelecionadas');
    
    if (total > 0) {
        contador.style.display = 'block';
        totalSelecionadas.textContent = total;
    } else {
        contador.style.display = 'none';
    }
}

// Calcula data de término automaticamente
function calcularDataTermino() {
    const planoSelect = document.getElementById('plano_id');
    const dtInicioInput = document.getElementById('dt_inicio');
    const dtFimInput = document.getElementById('dt_fim');
    
    const planoId = planoSelect.value;
    const dtInicio = dtInicioInput.value;
    
    if (planoId && dtInicio) {
        const selectedOption = planoSelect.options[planoSelect.selectedIndex];
        const quantidadeMeses = parseInt(selectedOption.getAttribute('data-quantidade-meses')) || 1;
        
        if (quantidadeMeses > 0) {
            // Cria uma data a partir da data de início
            const dataInicio = new Date(dtInicio);
            
            // Adiciona a quantidade de meses
            const dataTermino = new Date(dataInicio);
            dataTermino.setMonth(dataTermino.getMonth() + quantidadeMeses);
            
            // Subtrai 1 dia para que a data de término seja o último dia do período
            dataTermino.setDate(dataTermino.getDate() - 1);
            
            // Formata para YYYY-MM-DD
            const ano = dataTermino.getFullYear();
            const mes = String(dataTermino.getMonth() + 1).padStart(2, '0');
            const dia = String(dataTermino.getDate()).padStart(2, '0');
            const dataTerminoFormatada = `${ano}-${mes}-${dia}`;
            
            // Preenche o campo de data de término
            dtFimInput.value = dataTerminoFormatada;
        }
    }
}

// Valida que todas as turmas selecionadas sejam da mesma modalidade
let modalidadeSelecionada = null;
let modalidadeNomeSelecionada = null;

function validarModalidade(checkbox) {
    const modalidadeId = checkbox.getAttribute('data-modalidade-id');
    const modalidadeNome = checkbox.getAttribute('data-modalidade-nome');
    
    if (checkbox.checked) {
        // Se é a primeira seleção, define a modalidade
        if (modalidadeSelecionada === null) {
            modalidadeSelecionada = modalidadeId;
            modalidadeNomeSelecionada = modalidadeNome;
            atualizarContador();
            return;
        }
        
        // Se já tem outra modalidade selecionada, desmarca e avisa
        if (modalidadeSelecionada !== modalidadeId) {
            checkbox.checked = false;
            alert('Não é possível selecionar turmas de modalidades diferentes na mesma matrícula.\n\nModalidade já selecionada: ' + modalidadeNomeSelecionada + '\nTentou selecionar: ' + modalidadeNome + '\n\nSelecione apenas turmas da mesma modalidade.');
            return;
        }
    } else {
        // Se desmarcou, verifica se ainda tem alguma selecionada
        const outrasSelecionadas = document.querySelectorAll('.turma-checkbox:checked');
        if (outrasSelecionadas.length === 0) {
            modalidadeSelecionada = null;
            modalidadeNomeSelecionada = null;
        }
    }
    
    atualizarContador();
}

// Adiciona listeners aos checkboxes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.turma-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            validarModalidade(this);
        });
    });
    
    // Adiciona listeners para calcular data de término
    const planoSelect = document.getElementById('plano_id');
    const dtInicioInput = document.getElementById('dt_inicio');
    
    planoSelect.addEventListener('change', calcularDataTermino);
    dtInicioInput.addEventListener('change', calcularDataTermino);
    
    // Calcula ao carregar se já houver valores
    if (planoSelect.value && dtInicioInput.value) {
        calcularDataTermino();
    }
    
    // Validação do formulário
    const form = document.getElementById('formMatricula');
    form.addEventListener('submit', function(e) {
        const modoMultiplo = document.getElementById('modoMultiplo').checked;
        
        if (modoMultiplo) {
            const checkboxes = document.querySelectorAll('.turma-checkbox:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Selecione pelo menos uma turma para matricular.');
                return false;
            }
        }
    });
});
</script>


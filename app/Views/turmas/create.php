<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Cadastrar Nova Turma</h1>
        <p class="page-subtitle">Preencha os dados da turma</p>
    </div>
    <a href="<?= BASE_URL ?>/turmas" class="btn btn-secondary">Voltar</a>
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
        <h3 class="card-title">Dados da Turma</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/turmas" data-validate>
            <?php
            // Gera token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="nome" class="form-label">
                    Nome da Turma <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    class="form-control" 
                    required
                    value="<?= htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Turma de Natação Infantil - Manhã"
                >
                <small class="form-text">Nome identificador da turma</small>
            </div>

            <div class="form-group">
                <label for="modalidade_id" class="form-label">
                    Modalidade <span class="required">*</span>
                </label>
                <select id="modalidade_id" name="modalidade_id" class="form-control" required>
                    <option value="">Selecione a modalidade...</option>
                    <?php foreach ($modalidades as $modalidade): ?>
                        <option value="<?= $modalidade['id'] ?>" <?= (isset($_POST['modalidade_id']) && $_POST['modalidade_id'] == $modalidade['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Modalidade esportiva da turma</small>
            </div>

            <div class="form-group">
                <label for="professor_id" class="form-label">
                    Professor <span class="required">*</span>
                </label>
                <select id="professor_id" name="professor_id" class="form-control" required>
                    <option value="">Selecione o professor...</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?= $professor['id'] ?>" <?= (isset($_POST['professor_id']) && $_POST['professor_id'] == $professor['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($professor['cpf'])): ?>
                                - CPF: <?= htmlspecialchars($professor['cpf'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Professor responsável pela turma</small>
            </div>

            <div class="form-group">
                <label for="nivel" class="form-label">Nível</label>
                <input 
                    type="text" 
                    id="nivel" 
                    name="nivel" 
                    class="form-control"
                    value="<?= htmlspecialchars($_POST['nivel'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Iniciante, Intermediário, Avançado"
                >
                <small class="form-text">Nível da turma (opcional)</small>
            </div>

            <div class="form-group">
                <label for="capacidade" class="form-label">
                    Capacidade <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    id="capacidade" 
                    name="capacidade" 
                    class="form-control" 
                    required
                    min="1"
                    value="<?= htmlspecialchars($_POST['capacidade'] ?? '20', ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Número máximo de alunos na turma</small>
            </div>

            <div class="form-group">
                <label for="local" class="form-label">Local</label>
                <input 
                    type="text" 
                    id="local" 
                    name="local" 
                    class="form-control"
                    value="<?= htmlspecialchars($_POST['local'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Piscina 1, Quadra A, Sala 3"
                >
                <small class="form-text">Local onde as aulas acontecem</small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Dias da Semana <span class="required">*</span>
                </label>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.5rem;">
                    <?php
                    $diasSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
                    $diasSelecionados = isset($_POST['dias_da_semana']) && is_array($_POST['dias_da_semana']) ? $_POST['dias_da_semana'] : [];
                    foreach ($diasSemana as $dia):
                    ?>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 0.25rem; transition: background-color 0.2s;" 
                               onmouseover="this.style.backgroundColor='var(--bg-secondary)'" 
                               onmouseout="this.style.backgroundColor='transparent'">
                            <input 
                                type="checkbox" 
                                name="dias_da_semana[]" 
                                value="<?= $dia ?>"
                                <?= in_array($dia, $diasSelecionados) ? 'checked' : '' ?>
                                style="cursor: pointer;"
                            >
                            <span><?= $dia ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <small class="form-text">Selecione os dias da semana em que a turma se reúne (pelo menos um dia)</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="hora_inicio" class="form-label">
                        Hora de Início <span class="required">*</span>
                    </label>
                    <input 
                        type="time" 
                        id="hora_inicio" 
                        name="hora_inicio" 
                        class="form-control" 
                        required
                        value="<?= htmlspecialchars($_POST['hora_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <small class="form-text">Horário de início das aulas</small>
                </div>

                <div class="form-group">
                    <label for="hora_fim" class="form-label">
                        Hora de Término <span class="required">*</span>
                    </label>
                    <input 
                        type="time" 
                        id="hora_fim" 
                        name="hora_fim" 
                        class="form-control" 
                        required
                        value="<?= htmlspecialchars($_POST['hora_fim'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <small class="form-text">Horário de término das aulas</small>
                </div>
            </div>

            <div class="form-group">
                <label for="ativo" class="form-label">Status</label>
                <select id="ativo" name="ativo" class="form-control">
                    <option value="1" <?= (!isset($_POST['ativo']) || $_POST['ativo'] == '1') ? 'selected' : '' ?>>Ativa</option>
                    <option value="0" <?= (isset($_POST['ativo']) && $_POST['ativo'] == '0') ? 'selected' : '' ?>>Inativa</option>
                </select>
                <small class="form-text">Turmas inativas não aparecerão nas opções de matrícula</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar Turma</button>
                <a href="<?= BASE_URL ?>/turmas" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Validação de horários
document.addEventListener('DOMContentLoaded', function() {
    const horaInicio = document.getElementById('hora_inicio');
    const horaFim = document.getElementById('hora_fim');
    const form = document.querySelector('form[data-validate]');
    
    function validateHorarios() {
        if (horaInicio.value && horaFim.value) {
            if (horaInicio.value >= horaFim.value) {
                horaFim.setCustomValidity('Hora de término deve ser posterior à hora de início');
            } else {
                horaFim.setCustomValidity('');
            }
        }
    }
    
    horaInicio.addEventListener('change', validateHorarios);
    horaFim.addEventListener('change', validateHorarios);
    
    // Validação de checkboxes de dias (remove required individual, valida no submit)
    const checkboxes = document.querySelectorAll('input[name="dias_da_semana[]"]');
    
    // Remove required de cada checkbox individual
    checkboxes.forEach(cb => {
        cb.removeAttribute('required');
    });
    
    form.addEventListener('submit', function(e) {
        const checked = Array.from(checkboxes).some(cb => cb.checked);
        if (!checked) {
            e.preventDefault();
            alert('Selecione pelo menos um dia da semana');
            return false;
        }
    });
});
</script>


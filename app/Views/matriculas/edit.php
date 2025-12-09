<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Editar Matrícula</h1>
        <p class="page-subtitle">Atualize os dados da matrícula</p>
    </div>
    <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>" class="btn btn-secondary">Voltar</a>
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
        <h3 class="card-title">Dados da Matrícula</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>" data-validate>
            <?php
            // Gera token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="_method" value="PUT">
            
            <div class="form-group">
                <label for="aluno_id" class="form-label">
                    Aluno <span class="required">*</span>
                </label>
                <select id="aluno_id" name="aluno_id" class="form-control" required>
                    <option value="">Selecione o aluno...</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>" <?= $matricula['aluno_id'] == $aluno['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($aluno['cpf'])): ?>
                                - CPF: <?= htmlspecialchars($aluno['cpf'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text">Selecione o aluno que será matriculado</small>
            </div>

            <div class="form-group">
                <label for="turma_id" class="form-label">
                    Turma <span class="required">*</span>
                </label>
                <select id="turma_id" name="turma_id" class="form-control" required>
                    <option value="">Selecione a turma...</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?= $turma['id'] ?>" <?= $matricula['turma_id'] == $turma['id'] ? 'selected' : '' ?>>
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

            <div class="form-group">
                <label for="plano_id" class="form-label">
                    Plano <span class="required">*</span>
                </label>
                <select id="plano_id" name="plano_id" class="form-control" required>
                    <option value="">Selecione o plano...</option>
                    <?php foreach ($planos as $plano): ?>
                        <option value="<?= $plano['id'] ?>" <?= $matricula['plano_id'] == $plano['id'] ? 'selected' : '' ?>>
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
                    value="<?= htmlspecialchars($matricula['dt_inicio'], ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($matricula['dt_fim'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="form-text">Deixe em branco se a matrícula não tem data de término definida</small>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="Ativa" <?= $matricula['status'] === 'Ativa' ? 'selected' : '' ?>>Ativa</option>
                    <option value="Suspensa" <?= $matricula['status'] === 'Suspensa' ? 'selected' : '' ?>>Suspensa</option>
                    <option value="Cancelada" <?= $matricula['status'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    <option value="Finalizada" <?= $matricula['status'] === 'Finalizada' ? 'selected' : '' ?>>Finalizada</option>
                </select>
                <small class="form-text">Status da matrícula</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>


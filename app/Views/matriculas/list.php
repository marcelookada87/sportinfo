<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Matrículas</h1>
        <p class="page-subtitle">Gerencie as matrículas de alunos em turmas</p>
    </div>
    <a href="<?= BASE_URL ?>/matriculas/create" class="btn btn-primary">
        <span style="margin-right: 0.5rem;">+</span>
        Nova Matrícula
    </a>
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
        <h3 class="card-title">Filtros de Busca</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/matriculas" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por aluno, turma ou modalidade..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
            </div>
            <div class="filter-group">
                <select name="status" id="statusFilter" class="form-control filter-select">
                    <option value="">Todos os status</option>
                    <option value="Ativa" <?= ($filters['status'] ?? '') === 'Ativa' ? 'selected' : '' ?>>Ativa</option>
                    <option value="Suspensa" <?= ($filters['status'] ?? '') === 'Suspensa' ? 'selected' : '' ?>>Suspensa</option>
                    <option value="Cancelada" <?= ($filters['status'] ?? '') === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    <option value="Finalizada" <?= ($filters['status'] ?? '') === 'Finalizada' ? 'selected' : '' ?>>Finalizada</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="aluno_id" id="alunoFilter" class="form-control filter-select">
                    <option value="">Todos os alunos</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>" <?= ($filters['aluno_id'] ?? 0) == $aluno['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <select name="modalidade_id" id="modalidadeFilter" class="form-control filter-select">
                    <option value="">Todas as modalidades</option>
                    <?php foreach ($modalidades as $modalidade): ?>
                        <option value="<?= $modalidade['id'] ?>" <?= ($filters['modalidade_id'] ?? 0) == $modalidade['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">
                    <span class="btn-icon">🔍</span>
                    Buscar
                </button>
            </div>
            <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['aluno_id']) || !empty($filters['modalidade_id'])): ?>
            <div class="filter-group">
                <a href="<?= BASE_URL ?>/matriculas" class="btn btn-secondary">
                    <span class="btn-icon">✕</span>
                    Limpar
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Matrículas</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($matriculas)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhuma matrícula encontrada.</p>
                <a href="<?= BASE_URL ?>/matriculas/create" class="btn btn-primary">Cadastrar Primeira Matrícula</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="matriculasTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Turma</th>
                            <th>Modalidade</th>
                            <th>Plano</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matriculas as $matricula): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($matricula['aluno_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if (!empty($matricula['aluno_cpf'])): ?>
                                        <br><small style="color: var(--text-secondary);">CPF: <?= htmlspecialchars($matricula['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($matricula['turma_nome'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($matricula['professor_nome'])): ?>
                                        <br><small style="color: var(--text-secondary);">Prof: <?= htmlspecialchars($matricula['professor_nome'], ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-secondary"><?= htmlspecialchars($matricula['modalidade_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($matricula['plano_nome'], ENT_QUOTES, 'UTF-8') ?>
                                    <br><small style="color: var(--text-secondary);">
                                        <?php
                                        $periodicidadeLabels = [
                                            'mensal' => 'Mensal',
                                            'trimestral' => 'Trimestral',
                                            'anual' => 'Anual'
                                        ];
                                        $periodicidadeLabel = $periodicidadeLabels[$matricula['plano_periodicidade']] ?? ucfirst($matricula['plano_periodicidade']);
                                        ?>
                                        <?= $periodicidadeLabel ?>
                                    </small>
                                </td>
                                <td><?= date('d/m/Y', strtotime($matricula['dt_inicio'])) ?></td>
                                <td>
                                    <?= $matricula['dt_fim'] ? date('d/m/Y', strtotime($matricula['dt_fim'])) : '<span style="color: var(--text-secondary);">-</span>' ?>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'Ativa' => 'badge-success',
                                        'Suspensa' => 'badge-warning',
                                        'Cancelada' => 'badge-error',
                                        'Finalizada' => 'badge-secondary'
                                    ];
                                    $statusColor = $statusColors[$matricula['status']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?= $statusColor ?>"><?= htmlspecialchars($matricula['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
                                            Editar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>


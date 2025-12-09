<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Turmas</h1>
        <p class="page-subtitle">Gerencie as turmas e horários das aulas</p>
    </div>
    <a href="<?= BASE_URL ?>/turmas/create" class="btn btn-primary">
        <span style="margin-right: 0.5rem;">+</span>
        Nova Turma
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
        <form method="GET" action="<?= BASE_URL ?>/turmas" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por nome, modalidade ou professor..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
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
                <select name="professor_id" id="professorFilter" class="form-control filter-select">
                    <option value="">Todos os professores</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?= $professor['id'] ?>" <?= ($filters['professor_id'] ?? 0) == $professor['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <select name="ativo" id="ativoFilter" class="form-control filter-select">
                    <option value="">Todas</option>
                    <option value="1" <?= ($filters['ativo'] ?? '') === '1' ? 'selected' : '' ?>>Ativas</option>
                    <option value="0" <?= ($filters['ativo'] ?? '') === '0' ? 'selected' : '' ?>>Inativas</option>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">
                    <span class="btn-icon">🔍</span>
                    Buscar
                </button>
            </div>
            <?php if (!empty($filters['search']) || !empty($filters['modalidade_id']) || !empty($filters['professor_id']) || !empty($filters['ativo'])): ?>
            <div class="filter-group">
                <a href="<?= BASE_URL ?>/turmas" class="btn btn-secondary">
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
        <h3 class="card-title">Lista de Turmas</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($turmas)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhuma turma encontrada.</p>
                <a href="<?= BASE_URL ?>/turmas/create" class="btn btn-primary">Cadastrar Primeira Turma</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="turmasTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Modalidade</th>
                            <th>Professor</th>
                            <th>Nível</th>
                            <th>Dias</th>
                            <th>Horário</th>
                            <th>Local</th>
                            <th>Vagas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($turmas as $turma): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-secondary"><?= htmlspecialchars($turma['modalidade_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td><?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if (!empty($turma['nivel'])): ?>
                                        <span class="badge badge-info"><?= htmlspecialchars($turma['nivel'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($turma['dias_array']) && is_array($turma['dias_array'])) {
                                        echo implode(', ', $turma['dias_array']);
                                    } else {
                                        echo '<span style="color: var(--text-secondary);">-</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?= date('H:i', strtotime($turma['hora_inicio'])) ?> - 
                                    <?= date('H:i', strtotime($turma['hora_fim'])) ?>
                                </td>
                                <td>
                                    <?= !empty($turma['local']) ? htmlspecialchars($turma['local'], ENT_QUOTES, 'UTF-8') : '<span style="color: var(--text-secondary);">-</span>' ?>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span class="badge <?= $turma['is_cheia'] ? 'badge-error' : 'badge-success' ?>">
                                            <?= number_format($turma['total_matriculas_ativas'], 0, ',', '.') ?> / <?= number_format($turma['capacidade'], 0, ',', '.') ?>
                                        </span>
                                        <?php if ($turma['vagas_disponiveis'] > 0): ?>
                                            <small style="color: var(--success-color);">
                                                (<?= number_format($turma['vagas_disponiveis'], 0, ',', '.') ?> vagas)
                                            </small>
                                        <?php else: ?>
                                            <small style="color: var(--error-color);">(Cheia)</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($turma['ativo']): ?>
                                        <span class="badge badge-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inativa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/turmas/<?= $turma['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/turmas/<?= $turma['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
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


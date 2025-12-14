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
            <div class="matriculas-agrupadas">
                <?php foreach ($matriculasAgrupadas as $grupo): ?>
                    <?php 
                    $totalMatriculas = count($grupo['matriculas']);
                    $alunoId = $grupo['aluno_id'];
                    $collapseId = 'aluno_' . $alunoId;
                    ?>
                    <div class="matricula-grupo">
                        <div class="matricula-grupo-header" onclick="toggleMatriculaGrupo('<?= $collapseId ?>')">
                            <div class="matricula-grupo-info">
                                <div class="matricula-grupo-aluno">
                                    <strong><?= htmlspecialchars($grupo['aluno_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if (!empty($grupo['aluno_cpf'])): ?>
                                        <small style="color: var(--text-secondary); margin-left: 0.5rem;">CPF: <?= htmlspecialchars($grupo['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="matricula-grupo-badge">
                                    <span class="badge badge-info"><?= $totalMatriculas ?> <?= $totalMatriculas === 1 ? 'matrícula' : 'matrículas' ?></span>
                                </div>
                            </div>
                            <div class="matricula-grupo-toggle">
                                <span class="toggle-icon" id="icon_<?= $collapseId ?>">▼</span>
                            </div>
                        </div>
                        <div class="matricula-grupo-content" id="<?= $collapseId ?>" style="display: none;">
                            <div class="matriculas-lista">
                                <?php foreach ($grupo['matriculas'] as $matricula): ?>
                                    <div class="matricula-item">
                                        <div class="matricula-item-header">
                                            <div class="matricula-item-info">
                                                <div class="matricula-item-turma">
                                                    <strong><?= htmlspecialchars($matricula['turma_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if (!empty($matricula['professor_nome'])): ?>
                                                        <small style="color: var(--text-secondary); margin-left: 0.5rem;">Prof: <?= htmlspecialchars($matricula['professor_nome'], ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php endif; ?>
                                                </div>
                                                <div class="matricula-item-badges">
                                    <span class="badge badge-secondary"><?= htmlspecialchars($matricula['modalidade_nome'], ENT_QUOTES, 'UTF-8') ?></span>
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
                                                </div>
                                            </div>
                                            <div class="matricula-item-actions">
                                        <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
                                            Editar
                                        </a>
                                                <form method="POST" action="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>/delete" style="display: inline-block; margin: 0; padding: 0;" onsubmit="return confirm('Tem certeza que deseja remover esta matrícula? Esta ação não pode ser desfeita.');">
                                                    <?php
                                                    if (empty($_SESSION['csrf_token'])) {
                                                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                                    }
                                                    ?>
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Remover matrícula">
                                                        Remover
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="matricula-item-details">
                                            <div class="matricula-detail-item">
                                                <span class="detail-label">Plano:</span>
                                                <span class="detail-value">
                                                    <?= htmlspecialchars($matricula['plano_nome'], ENT_QUOTES, 'UTF-8') ?>
                                                    <?php
                                                    $periodicidadeLabels = [
                                                        'mensal' => 'Mensal',
                                                        'trimestral' => 'Trimestral',
                                                        'anual' => 'Anual'
                                                    ];
                                                    $periodicidadeLabel = $periodicidadeLabels[$matricula['plano_periodicidade']] ?? ucfirst($matricula['plano_periodicidade']);
                                                    ?>
                                                    <small style="color: var(--text-secondary);">(<?= $periodicidadeLabel ?>)</small>
                                                </span>
                                            </div>
                                            <?php if (!empty($matricula['turma_dias'])): ?>
                                            <div class="matricula-detail-item">
                                                <span class="detail-label">Dias:</span>
                                                <span class="detail-value">
                                                    <?php
                                                    $dias = json_decode($matricula['turma_dias'], true);
                                                    if (is_array($dias) && !empty($dias)) {
                                                        // Mapeia abreviações para nomes completos
                                                        $diasMap = [
                                                            'Segunda' => 'Seg',
                                                            'Terça' => 'Ter',
                                                            'Quarta' => 'Qua',
                                                            'Quinta' => 'Qui',
                                                            'Sexta' => 'Sex',
                                                            'Sábado' => 'Sáb',
                                                            'Domingo' => 'Dom'
                                                        ];
                                                        $diasFormatados = array_map(function($dia) use ($diasMap) {
                                                            return $diasMap[$dia] ?? $dia;
                                                        }, $dias);
                                                        echo htmlspecialchars(implode(', ', $diasFormatados), ENT_QUOTES, 'UTF-8');
                                                    } else {
                                                        echo '<span style="color: var(--text-secondary);">-</span>';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($matricula['turma_hora_inicio']) && !empty($matricula['turma_hora_fim'])): ?>
                                            <div class="matricula-detail-item">
                                                <span class="detail-label">Horário:</span>
                                                <span class="detail-value">
                                                    <strong><?= date('H:i', strtotime($matricula['turma_hora_inicio'])) ?></strong>
                                                    <span style="color: var(--text-secondary);"> às </span>
                                                    <strong><?= date('H:i', strtotime($matricula['turma_hora_fim'])) ?></strong>
                                                </span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($matricula['turma_local'])): ?>
                                            <div class="matricula-detail-item">
                                                <span class="detail-label">Local:</span>
                                                <span class="detail-value"><?= htmlspecialchars($matricula['turma_local'], ENT_QUOTES, 'UTF-8') ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="matricula-detail-item">
                                                <span class="detail-label">Início:</span>
                                                <span class="detail-value"><?= date('d/m/Y', strtotime($matricula['dt_inicio'])) ?></span>
                                            </div>
                                            <div class="matricula-detail-item">
                                                <span class="detail-label">Fim:</span>
                                                <span class="detail-value">
                                                    <?= $matricula['dt_fim'] ? date('d/m/Y', strtotime($matricula['dt_fim'])) : '<span style="color: var(--text-secondary);">-</span>' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                                    </div>
                        <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.matriculas-agrupadas {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.matricula-grupo {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: box-shadow 0.2s ease;
}

.matricula-grupo:hover {
    box-shadow: var(--shadow-md);
}

.matricula-grupo-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    cursor: pointer;
    user-select: none;
    transition: background 0.2s ease;
}

.matricula-grupo-header:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.matricula-grupo-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.matricula-grupo-aluno {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.matricula-grupo-aluno strong {
    font-size: 1.1rem;
    color: var(--text-primary);
}

.matricula-grupo-badge {
    margin-left: auto;
}

.matricula-grupo-toggle {
    margin-left: 1rem;
}

.toggle-icon {
    display: inline-block;
    transition: transform 0.3s ease;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.matricula-grupo-content {
    padding: 0;
    background: var(--bg-secondary);
}

.matriculas-lista {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 0.75rem;
}

.matricula-item {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 1rem;
    transition: all 0.2s ease;
}

.matricula-item:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.matricula-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    gap: 1rem;
}

.matricula-item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.matricula-item-turma {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.matricula-item-turma strong {
    font-size: 1rem;
    color: var(--text-primary);
}

.matricula-item-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.matricula-item-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.matricula-item-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border-color);
}

.matricula-item-details .detail-value strong {
    color: var(--primary-color);
    font-weight: 600;
}

.matricula-detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 0.875rem;
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .matricula-item-header {
        flex-direction: column;
    }
    
    .matricula-item-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .matricula-item-details {
        grid-template-columns: 1fr;
    }
    
    .matricula-grupo-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .matricula-grupo-badge {
        margin-left: 0;
    }
}
</style>

<script>
function toggleMatriculaGrupo(collapseId) {
    const content = document.getElementById(collapseId);
    const icon = document.getElementById('icon_' + collapseId);
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const grupos = document.querySelectorAll('.matricula-grupo');
    grupos.forEach(function(grupo) {
        const badge = grupo.querySelector('.matricula-grupo-badge .badge');
        if (badge && badge.textContent.includes('1 matrícula')) {
            const collapseId = grupo.querySelector('.matricula-grupo-content').id;
            toggleMatriculaGrupo(collapseId);
        }
    });
});
</script>


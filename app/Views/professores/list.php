<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Professores</h1>
        <p class="page-subtitle">Gerencie o cadastro de professores</p>
    </div>
    <a href="<?= BASE_URL ?>/professores/create" class="btn btn-primary">
        <span style="margin-right: 0.5rem;">+</span>
        Novo Professor
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
        <form method="GET" action="<?= BASE_URL ?>/professores" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por nome, CPF, email ou CREF..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
            </div>
            <div class="filter-group">
                <select name="status" id="statusFilter" class="form-control filter-select">
                    <option value="">Todos os status</option>
                    <option value="Ativo" <?= ($filters['status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Inativo" <?= ($filters['status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">
                    <span class="btn-icon">🔍</span>
                    Buscar
                </button>
            </div>
            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
            <div class="filter-group">
                <a href="<?= BASE_URL ?>/professores" class="btn btn-secondary">
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
        <h3 class="card-title">Lista de Professores</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($professores)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhum professor encontrado.</p>
                <a href="<?= BASE_URL ?>/professores/create" class="btn btn-primary">Cadastrar Primeiro Professor</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="professoresTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>CREF</th>
                            <th>Contato</th>
                            <th>Email</th>
                            <th>Especialidade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($professores as $professor): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </td>
                                <td><?= !empty($professor['cpf']) ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $professor['cpf']) : '-' ?></td>
                                <td><?= !empty($professor['registro_cref']) ? htmlspecialchars($professor['registro_cref'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= !empty($professor['contato']) ? htmlspecialchars($professor['contato'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= !empty($professor['email']) ? htmlspecialchars($professor['email'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= !empty($professor['especialidade']) ? htmlspecialchars($professor['especialidade'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td>
                                    <?php
                                    $statusClass = match($professor['status']) {
                                        'Ativo' => 'success',
                                        'Inativo' => 'secondary',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($professor['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/professores/<?= $professor['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/professores/<?= $professor['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
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


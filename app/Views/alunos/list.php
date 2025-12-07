<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Alunos</h1>
        <p class="page-subtitle">Gerencie o cadastro de alunos</p>
    </div>
    <a href="<?= BASE_URL ?>/alunos/create" class="btn btn-primary">
        <span style="margin-right: 0.5rem;">+</span>
        Novo Aluno
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
        <form method="GET" action="<?= BASE_URL ?>/alunos" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por nome, CPF ou email..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
            </div>
            <div class="filter-group">
                <select name="status" id="statusFilter" class="form-control filter-select">
                    <option value="">Todos os status</option>
                    <option value="Ativo" <?= ($filters['status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Inativo" <?= ($filters['status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                    <option value="Suspenso" <?= ($filters['status'] ?? '') === 'Suspenso' ? 'selected' : '' ?>>Suspenso</option>
                    <option value="Cancelado" <?= ($filters['status'] ?? '') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
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
                <a href="<?= BASE_URL ?>/alunos" class="btn btn-secondary">
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
        <h3 class="card-title">Lista de Alunos</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($alunos)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhum aluno encontrado.</p>
                <a href="<?= BASE_URL ?>/alunos/create" class="btn btn-primary">Cadastrar Primeiro Aluno</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="alunosTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>RG</th>
                            <th>Data Nascimento</th>
                            <th>Idade</th>
                            <th>Contato</th>
                            <th>Pais</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alunos as $aluno): ?>
                            <?php
                            $nascimento = new DateTime($aluno['dt_nascimento']);
                            $hoje = new DateTime();
                            $idade = $hoje->diff($nascimento)->y;
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if (!empty($aluno['email'])): ?>
                                        <br><small style="color: var(--text-secondary);"><?= htmlspecialchars($aluno['email'], ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($aluno['cpf']) ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $aluno['cpf']) : '-' ?></td>
                                <td><?= !empty($aluno['rg']) ? htmlspecialchars($aluno['rg'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= date('d/m/Y', strtotime($aluno['dt_nascimento'])) ?></td>
                                <td><?= $idade ?> anos</td>
                                <td><?= !empty($aluno['contato']) ? htmlspecialchars($aluno['contato'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td>
                                    <?php if (!empty($aluno['nome_pai']) || !empty($aluno['nome_mae'])): ?>
                                        <div style="font-size: 0.875rem; line-height: 1.5;">
                                            <?php if (!empty($aluno['nome_pai'])): ?>
                                                <div style="margin-bottom: 0.25rem;">
                                                    <strong>Pai:</strong> <?= htmlspecialchars($aluno['nome_pai'], ENT_QUOTES, 'UTF-8') ?>
                                                    <?php if (!empty($aluno['telefone_pai'])): ?>
                                                        <br><small style="color: var(--text-secondary);">📞 <?= htmlspecialchars($aluno['telefone_pai'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($aluno['email_pai'])): ?>
                                                        <br><small style="color: var(--text-secondary);">✉ <?= htmlspecialchars($aluno['email_pai'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($aluno['telegram_pai'])): ?>
                                                        <br><small style="color: var(--text-secondary);">💬 <?= htmlspecialchars($aluno['telegram_pai'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($aluno['nome_mae'])): ?>
                                                <div>
                                                    <strong>Mãe:</strong> <?= htmlspecialchars($aluno['nome_mae'], ENT_QUOTES, 'UTF-8') ?>
                                                    <?php if (!empty($aluno['telefone_mae'])): ?>
                                                        <br><small style="color: var(--text-secondary);">📞 <?= htmlspecialchars($aluno['telefone_mae'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($aluno['email_mae'])): ?>
                                                        <br><small style="color: var(--text-secondary);">✉ <?= htmlspecialchars($aluno['email_mae'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($aluno['telegram_mae'])): ?>
                                                        <br><small style="color: var(--text-secondary);">💬 <?= htmlspecialchars($aluno['telegram_mae'], ENT_QUOTES, 'UTF-8') ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($aluno['status']) {
                                        'Ativo' => 'success',
                                        'Inativo' => 'secondary',
                                        'Suspenso' => 'warning',
                                        'Cancelado' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($aluno['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/alunos/<?= $aluno['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/alunos/<?= $aluno['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
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


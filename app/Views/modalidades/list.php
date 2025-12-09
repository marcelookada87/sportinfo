<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Modalidades</h1>
        <p class="page-subtitle">Gerencie as modalidades esportivas</p>
    </div>
    <a href="<?= BASE_URL ?>/modalidades/create" class="btn btn-primary">
        <span style="margin-right: 0.5rem;">+</span>
        Nova Modalidade
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
        <form method="GET" action="<?= BASE_URL ?>/modalidades" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por nome, categoria ou descrição..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
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
            <?php if (!empty($filters['search']) || !empty($filters['ativo'])): ?>
            <div class="filter-group">
                <a href="<?= BASE_URL ?>/modalidades" class="btn btn-secondary">
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
        <h3 class="card-title">Lista de Modalidades</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($modalidades)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhuma modalidade encontrada.</p>
                <a href="<?= BASE_URL ?>/modalidades/create" class="btn btn-primary">Cadastrar Primeira Modalidade</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="modalidadesTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Categoria Etária</th>
                            <th>Descrição</th>
                            <th>Alunos</th>
                            <th>Turmas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modalidades as $modalidade): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($modalidade['categoria_etaria'])): ?>
                                        <span class="badge badge-secondary"><?= htmlspecialchars($modalidade['categoria_etaria'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($modalidade['descricao'])): ?>
                                        <?php
                                        $descricao = htmlspecialchars($modalidade['descricao'], ENT_QUOTES, 'UTF-8');
                                        echo strlen($descricao) > 50 ? substr($descricao, 0, 50) . '...' : $descricao;
                                        ?>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= number_format($modalidade['total_alunos'] ?? 0, 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= number_format($modalidade['total_turmas'] ?? 0, 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <?php if ($modalidade['ativo']): ?>
                                        <span class="badge badge-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inativa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/modalidades/<?= $modalidade['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/modalidades/<?= $modalidade['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
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


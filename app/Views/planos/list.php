<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Planos</h1>
        <p class="page-subtitle">Gerencie os planos de mensalidade</p>
    </div>
    <a href="<?= BASE_URL ?>/planos/create" class="btn btn-primary">
        <span style="margin-right: 0.5rem;">+</span>
        Novo Plano
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
        <form method="GET" action="<?= BASE_URL ?>/planos" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por nome ou descrição..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
            </div>
            <div class="filter-group">
                <select name="periodicidade" id="periodicidadeFilter" class="form-control filter-select">
                    <option value="">Todas as periodicidades</option>
                    <option value="mensal" <?= ($filters['periodicidade'] ?? '') === 'mensal' ? 'selected' : '' ?>>Mensal</option>
                    <option value="trimestral" <?= ($filters['periodicidade'] ?? '') === 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                    <option value="anual" <?= ($filters['periodicidade'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="ativo" id="ativoFilter" class="form-control filter-select">
                    <option value="">Todos</option>
                    <option value="1" <?= ($filters['ativo'] ?? '') === '1' ? 'selected' : '' ?>>Ativos</option>
                    <option value="0" <?= ($filters['ativo'] ?? '') === '0' ? 'selected' : '' ?>>Inativos</option>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">
                    <span class="btn-icon">🔍</span>
                    Buscar
                </button>
            </div>
            <?php if (!empty($filters['search']) || !empty($filters['periodicidade']) || !empty($filters['ativo'])): ?>
            <div class="filter-group">
                <a href="<?= BASE_URL ?>/planos" class="btn btn-secondary">
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
        <h3 class="card-title">Lista de Planos</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($planos)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhum plano encontrado.</p>
                <a href="<?= BASE_URL ?>/planos/create" class="btn btn-primary">Cadastrar Primeiro Plano</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="planosTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Periodicidade</th>
                            <th>Valor Base</th>
                            <th>Valor Mensal</th>
                            <th>Matrículas Ativas</th>
                            <th>Total Matrículas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($planos as $plano): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($plano['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if (!empty($plano['descricao'])): ?>
                                        <br><small style="color: var(--text-secondary);">
                                            <?php
                                            $descricao = htmlspecialchars($plano['descricao'], ENT_QUOTES, 'UTF-8');
                                            echo strlen($descricao) > 50 ? substr($descricao, 0, 50) . '...' : $descricao;
                                            ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $periodicidadeLabels = [
                                        'mensal' => 'Mensal',
                                        'trimestral' => 'Trimestral',
                                        'anual' => 'Anual'
                                    ];
                                    $periodicidadeLabel = $periodicidadeLabels[$plano['periodicidade']] ?? ucfirst($plano['periodicidade']);
                                    ?>
                                    <span class="badge badge-info"><?= $periodicidadeLabel ?></span>
                                </td>
                                <td>
                                    <strong>R$ <?= number_format((float)$plano['valor_base'], 2, ',', '.') ?></strong>
                                </td>
                                <td>
                                    <span style="color: var(--success-color); font-weight: 600;">
                                        R$ <?= number_format($plano['valor_mensal'] ?? 0, 2, ',', '.') ?>
                                    </span>
                                    <br><small style="color: var(--text-secondary);">/mês</small>
                                </td>
                                <td>
                                    <span class="badge badge-success"><?= number_format($plano['total_matriculas_ativas'] ?? 0, 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= number_format($plano['total_matriculas'] ?? 0, 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <?php if ($plano['ativo']): ?>
                                        <span class="badge badge-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/planos/<?= $plano['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/planos/<?= $plano['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
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


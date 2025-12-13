<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Financeiro - Mensalidades</h1>
        <p class="page-subtitle">Gerencie mensalidades e pagamentos</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="<?= BASE_URL ?>/financeiro/pagamentos" class="btn btn-secondary">
            Pagamentos
        </a>
        <a href="<?= BASE_URL ?>/financeiro/create" class="btn btn-primary">
            <span style="margin-right: 0.5rem;">+</span>
            Nova Mensalidade
        </a>
    </div>
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

<!-- Estatísticas -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem;">
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Total de Mensalidades</div>
            <div style="font-size: 1.25rem; font-weight: 700;"><?= number_format($estatisticas['total'] ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Pagas</div>
            <div style="font-size: 1.25rem; font-weight: 700;"><?= number_format($estatisticas['total_pagas'] ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Abertas</div>
            <div style="font-size: 1.25rem; font-weight: 700;"><?= number_format($estatisticas['total_abertas'] ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Atrasadas</div>
            <div style="font-size: 1.25rem; font-weight: 700;"><?= number_format($estatisticas['total_atrasadas'] ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Valor Recebido</div>
            <div style="font-size: 1.1rem; font-weight: 700;">R$ <?= number_format($estatisticas['valor_recebido'] ?? 0, 2, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 0.25rem;">Valor Pendente</div>
            <div style="font-size: 1.1rem; font-weight: 700;">R$ <?= number_format($estatisticas['valor_pendente'] ?? 0, 2, ',', '.') ?></div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Filtros de Busca</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/financeiro" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por aluno, CPF ou competência..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
            </div>
            <div class="filter-group">
                <select name="status" id="statusFilter" class="form-control filter-select">
                    <option value="">Todos os status</option>
                    <option value="Aberto" <?= ($filters['status'] ?? '') === 'Aberto' ? 'selected' : '' ?>>Aberto</option>
                    <option value="Pago" <?= ($filters['status'] ?? '') === 'Pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="Atrasado" <?= ($filters['status'] ?? '') === 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                    <option value="Cancelado" <?= ($filters['status'] ?? '') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="filter-group">
                <input 
                    type="month" 
                    name="competencia" 
                    id="competenciaFilter"
                    value="<?= htmlspecialchars($filters['competencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                    placeholder="Competência"
                >
            </div>
            <div class="filter-group">
                <input 
                    type="date" 
                    name="dt_vencimento_inicio" 
                    id="dtVencimentoInicio"
                    value="<?= htmlspecialchars($filters['dt_vencimento_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                    placeholder="Vencimento de"
                >
            </div>
            <div class="filter-group">
                <input 
                    type="date" 
                    name="dt_vencimento_fim" 
                    id="dtVencimentoFim"
                    value="<?= htmlspecialchars($filters['dt_vencimento_fim'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                    placeholder="Vencimento até"
                >
            </div>
            <div class="filter-group">
                <select name="aluno_id" id="alunoFilter" class="form-control filter-select">
                    <option value="">Todos os alunos</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>" <?= ($filters['aluno_id'] ?? '') == $aluno['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?>
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
            <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['competencia']) || !empty($filters['dt_vencimento_inicio']) || !empty($filters['dt_vencimento_fim']) || !empty($filters['aluno_id'])): ?>
            <div class="filter-group">
                <a href="<?= BASE_URL ?>/financeiro" class="btn btn-secondary">
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
        <h3 class="card-title">Lista de Mensalidades</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($mensalidades)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhuma mensalidade encontrada.</p>
                <a href="<?= BASE_URL ?>/financeiro/create" class="btn btn-primary">Cadastrar Primeira Mensalidade</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="mensalidadesTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Competência</th>
                            <th>Valor</th>
                            <th>Desconto</th>
                            <th>Multa/Juros</th>
                            <th>Valor Total</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensalidades as $mensalidade): ?>
                            <tr style="<?= ($mensalidade['is_atrasada'] ?? false) ? 'background-color: #fff3cd;' : '' ?>">
                                <td>
                                    <strong><?= htmlspecialchars($mensalidade['aluno_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if (!empty($mensalidade['aluno_cpf'])): ?>
                                        <br><small style="color: var(--text-secondary);">
                                            CPF: <?= htmlspecialchars($mensalidade['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= htmlspecialchars($mensalidade['competencia'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <strong>R$ <?= number_format((float)($mensalidade['valor'] ?? 0), 2, ',', '.') ?></strong>
                                </td>
                                <td>
                                    <?php if ((float)($mensalidade['desconto'] ?? 0) > 0): ?>
                                        <span style="color: var(--success-color);">
                                            - R$ <?= number_format((float)$mensalidade['desconto'], 2, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $multaJuros = (float)($mensalidade['multa'] ?? 0) + (float)($mensalidade['juros'] ?? 0);
                                    if ($multaJuros > 0): 
                                    ?>
                                        <span style="color: var(--error-color);">
                                            + R$ <?= number_format($multaJuros, 2, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="font-size: 1.1rem; color: var(--primary-color);">
                                        R$ <?= number_format($mensalidade['valor_total'] ?? 0, 2, ',', '.') ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php
                                    $dtVencimento = new \DateTime($mensalidade['dt_vencimento']);
                                    $hoje = new \DateTime();
                                    $diasAtraso = $hoje->diff($dtVencimento)->days;
                                    
                                    if ($dtVencimento < $hoje && $mensalidade['status'] !== 'Pago') {
                                        echo '<span style="color: var(--error-color); font-weight: 600;">';
                                        echo $dtVencimento->format('d/m/Y');
                                        echo ' <small>(' . $diasAtraso . ' dias atrasado)</small>';
                                        echo '</span>';
                                    } else {
                                        echo $dtVencimento->format('d/m/Y');
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $mensalidade['status'] ?? 'Aberto';
                                    $statusLabels = [
                                        'Aberto' => ['label' => 'Aberto', 'class' => 'badge-warning'],
                                        'Pago' => ['label' => 'Pago', 'class' => 'badge-success'],
                                        'Atrasado' => ['label' => 'Atrasado', 'class' => 'badge-danger'],
                                        'Cancelado' => ['label' => 'Cancelado', 'class' => 'badge-secondary']
                                    ];
                                    $statusInfo = $statusLabels[$status] ?? ['label' => $status, 'class' => 'badge-secondary'];
                                    ?>
                                    <span class="badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <?php if ($status !== 'Pago' && $status !== 'Cancelado'): ?>
                                            <a href="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>/edit" class="btn btn-sm btn-primary" title="Editar">
                                                Editar
                                            </a>
                                            <a href="<?= BASE_URL ?>/financeiro/pagamento/<?= $mensalidade['id'] ?>/create" class="btn btn-sm btn-success" title="Registrar pagamento">
                                                Pagar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 0.5rem;">
                    <?php if ($currentPage > 0): ?>
                        <a href="<?= BASE_URL ?>/financeiro?page=<?= $currentPage - 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="btn btn-secondary">Anterior</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 0; $i < $totalPages; $i++): ?>
                        <?php if ($i === $currentPage): ?>
                            <span class="btn btn-primary" style="cursor: default;"><?= $i + 1 ?></span>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/financeiro?page=<?= $i ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="btn btn-secondary"><?= $i + 1 ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages - 1): ?>
                        <a href="<?= BASE_URL ?>/financeiro?page=<?= $currentPage + 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="btn btn-secondary">Próxima</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>


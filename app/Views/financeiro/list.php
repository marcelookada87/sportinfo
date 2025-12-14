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
            <?php if (!empty($mensalidadesAgrupadas)): ?>
                <!-- Visualização Agrupada/Consolidada -->
                <div class="mensalidades-agrupadas">
                    <?php foreach ($mensalidadesAgrupadas as $chave => $grupo): ?>
                        <?php 
                        $totalMensalidades = count($grupo['mensalidades']);
                        $collapseId = 'mensalidade_' . $chave;
                        ?>
                        <div class="mensalidade-grupo">
                            <div class="mensalidade-grupo-header">
                                <div class="mensalidade-grupo-info">
                                    <div class="mensalidade-grupo-aluno">
                                        <strong><?= htmlspecialchars($grupo['aluno_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <?php if (!empty($grupo['aluno_cpf'])): ?>
                                            <small style="color: var(--text-secondary); margin-left: 0.5rem;">CPF: <?= htmlspecialchars($grupo['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?></small>
                                        <?php endif; ?>
                                        <?php
                                        // Converte competência de YYYY-MM para MM/YYYY
                                        $competenciaFormatada = '';
                                        if (!empty($grupo['competencia'])) {
                                            $parts = explode('-', $grupo['competencia']);
                                            if (count($parts) === 2) {
                                                $competenciaFormatada = $parts[1] . '/' . $parts[0];
                                            } else {
                                                $competenciaFormatada = $grupo['competencia'];
                                            }
                                        }
                                        ?>
                                        <span class="badge badge-info" style="margin-left: 0.75rem;"><?= htmlspecialchars($competenciaFormatada, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ($totalMensalidades > 1): ?>
                                            <span class="badge badge-secondary" style="margin-left: 0.5rem;"><?= $totalMensalidades ?> turmas</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mensalidade-grupo-valores">
                                        <div class="valor-total-consolidado">
                                            <?php if (!empty($grupo['dt_vencimento'])): ?>
                                                <small style="color: var(--text-secondary); margin-right: 1rem;">
                                                    Vencimento: <?= date('d/m/Y', strtotime($grupo['dt_vencimento'])) ?>
                                                    <?php if ($grupo['is_atrasada'] ?? false): ?>
                                                        <span style="color: var(--error-color); font-weight: 600;">(Atrasado)</span>
                                                    <?php endif; ?>
                                                </small>
                                            <?php endif; ?>
                                            <span class="valor-label">Total:</span>
                                            <strong style="font-size: 1.2rem; color: var(--primary-color);">
                                                R$ <?= number_format($grupo['valor_total'], 2, ',', '.') ?>
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="mensalidade-grupo-actions">
                                    <?php if ($totalMensalidades > 1): ?>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleMensalidadeGrupo('<?= $collapseId ?>')" title="Ver detalhes das turmas">
                                            <span id="icon_<?= $collapseId ?>">▼</span>
                                        </button>
                                    <?php endif; ?>
                                    <?php
                                    $status = $grupo['status'];
                                    $statusLabels = [
                                        'Aberto' => ['label' => 'Aberto', 'class' => 'badge-warning'],
                                        'Pago' => ['label' => 'Pago', 'class' => 'badge-success'],
                                        'Parcial' => ['label' => 'Parcial', 'class' => 'badge-info'],
                                        'Atrasado' => ['label' => 'Atrasado', 'class' => 'badge-danger'],
                                        'Cancelado' => ['label' => 'Cancelado', 'class' => 'badge-secondary']
                                    ];
                                    $statusInfo = $statusLabels[$status] ?? ['label' => $status, 'class' => 'badge-secondary'];
                                    ?>
                                    <span class="badge <?= $statusInfo['class'] ?>" style="margin: 0 0.5rem;"><?= $statusInfo['label'] ?></span>
                                    <a href="<?= BASE_URL ?>/financeiro/<?= $grupo['primeira_mensalidade_id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                        Ver
                                    </a>
                                    <?php if ($status !== 'Pago' && $status !== 'Cancelado'): ?>
                                        <a href="<?= BASE_URL ?>/financeiro/pagamento/<?= $grupo['primeira_mensalidade_id'] ?>/create" class="btn btn-sm btn-success" title="Registrar pagamento">
                                            Pagar
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" action="<?= BASE_URL ?>/financeiro/<?= $grupo['primeira_mensalidade_id'] ?>/delete" style="display: inline-block; margin: 0; padding: 0;" onsubmit="return confirm('Tem certeza que deseja remover esta mensalidade? Esta ação não pode ser desfeita.');">
                                        <?php
                                        if (empty($_SESSION['csrf_token'])) {
                                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                        }
                                        ?>
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remover mensalidade">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php if ($totalMensalidades > 1): ?>
                                <div class="mensalidade-grupo-content" id="<?= $collapseId ?>" style="display: none;">
                                    <div class="mensalidades-lista-detalhes">
                                        <?php foreach ($grupo['mensalidades'] as $mensalidade): ?>
                                            <?php
                                            $status = $mensalidade['status'] ?? 'Aberto';
                                            ?>
                                            <div class="mensalidade-item-detalhe">
                                                <div class="mensalidade-detalhe-info">
                                                    <div>
                                                        <strong><?= htmlspecialchars($mensalidade['turma_nome'] ?? 'Turma', ENT_QUOTES, 'UTF-8') ?></strong>
                                                        <small style="color: var(--text-secondary); margin-left: 0.5rem;">
                                                            <?= htmlspecialchars($mensalidade['modalidade_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                                        </small>
                                                    </div>
                                                    <div class="mensalidade-detalhe-valor">
                                                        R$ <?= number_format($mensalidade['valor_total'], 2, ',', '.') ?>
                                                    </div>
                                                </div>
                                                <div class="mensalidade-detalhe-actions">
                                                    <?php
                                                    $statusColors = [
                                                        'Aberto' => 'badge-warning',
                                                        'Pago' => 'badge-success',
                                                        'Atrasado' => 'badge-danger',
                                                        'Cancelado' => 'badge-secondary'
                                                    ];
                                                    $statusColor = $statusColors[$status] ?? 'badge-secondary';
                                                    ?>
                                                    <span class="badge <?= $statusColor ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span>
                                                    <a href="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>" class="btn btn-xs btn-secondary">Ver</a>
                                                    <form method="POST" action="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>/delete" style="display: inline-block; margin: 0; padding: 0;" onsubmit="return confirm('Tem certeza que deseja remover esta mensalidade? Esta ação não pode ser desfeita.');">
                                                        <?php
                                                        if (empty($_SESSION['csrf_token'])) {
                                                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                                        }
                                                        ?>
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <button type="submit" class="btn btn-xs btn-danger" title="Remover mensalidade">
                                                            Remover
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Fallback para tabela tradicional se não houver agrupamento -->
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
                                    <?php
                                    // Converte competência de YYYY-MM para MM/YYYY
                                    $competenciaFormatada = '';
                                    if (!empty($mensalidade['competencia'])) {
                                        $parts = explode('-', $mensalidade['competencia']);
                                        if (count($parts) === 2) {
                                            $competenciaFormatada = $parts[1] . '/' . $parts[0];
                                        } else {
                                            $competenciaFormatada = $mensalidade['competencia'];
                                        }
                                    }
                                    ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($competenciaFormatada, ENT_QUOTES, 'UTF-8') ?></span>
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
                                        <form method="POST" action="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>/delete" style="display: inline-block; margin: 0; padding: 0;" onsubmit="return confirm('Tem certeza que deseja remover esta mensalidade? Esta ação não pode ser desfeita.');">
                                            <?php
                                            if (empty($_SESSION['csrf_token'])) {
                                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                            }
                                            ?>
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Remover mensalidade">
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

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

<style>
.mensalidades-agrupadas {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.mensalidade-grupo {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: box-shadow 0.2s ease;
}

.mensalidade-grupo:hover {
    box-shadow: var(--shadow-md);
}

.mensalidade-grupo-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    gap: 1rem;
}

.mensalidade-grupo-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.mensalidade-grupo-aluno {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.mensalidade-grupo-aluno strong {
    font-size: 1.1rem;
    color: var(--text-primary);
}

.mensalidade-grupo-valores {
    display: flex;
    align-items: center;
}

.valor-total-consolidado {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.valor-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.mensalidade-grupo-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.mensalidade-grupo-content {
    padding: 0.75rem;
    background: var(--bg-secondary);
}

.mensalidades-lista-detalhes {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.mensalidade-item-detalhe {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.mensalidade-detalhe-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.mensalidade-detalhe-valor {
    font-weight: 600;
    color: var(--primary-color);
    font-size: 1rem;
}

.mensalidade-detalhe-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .mensalidade-grupo-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .mensalidade-grupo-info {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    
    .mensalidade-grupo-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .mensalidade-item-detalhe {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .mensalidade-detalhe-info {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    
    .mensalidade-detalhe-actions {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>

<script>
function toggleMensalidadeGrupo(collapseId) {
    const content = document.getElementById(collapseId);
    const icon = document.getElementById('icon_' + collapseId);
    
    if (content && icon) {
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }
}
</script>


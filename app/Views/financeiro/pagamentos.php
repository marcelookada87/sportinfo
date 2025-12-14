<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Financeiro - Pagamentos</h1>
        <p class="page-subtitle">Gerencie pagamentos registrados</p>
    </div>
    <a href="<?= BASE_URL ?>/financeiro" class="btn btn-secondary">Mensalidades</a>
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

<!-- Estatísticas -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem;">
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Total de Pagamentos</div>
            <div style="font-size: 1.25rem; font-weight: 700;"><?= number_format($estatisticas['total'] ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Valor Total</div>
            <div style="font-size: 1.1rem; font-weight: 700;">R$ <?= number_format($estatisticas['valor_total'] ?? 0, 2, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">PIX</div>
            <div style="font-size: 1.1rem; font-weight: 700;">R$ <?= number_format($estatisticas['valor_pix'] ?? 0, 2, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Cartão</div>
            <div style="font-size: 1.1rem; font-weight: 700;">R$ <?= number_format($estatisticas['valor_cartao'] ?? 0, 2, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.25rem;">Dinheiro</div>
            <div style="font-size: 1.1rem; font-weight: 700;">R$ <?= number_format($estatisticas['valor_dinheiro'] ?? 0, 2, ',', '.') ?></div>
        </div>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
        <div class="card-body" style="padding: 0.75rem;">
            <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 0.25rem;">Conciliado</div>
            <div style="font-size: 1.1rem; font-weight: 700;">R$ <?= number_format($estatisticas['valor_conciliado'] ?? 0, 2, ',', '.') ?></div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Filtros de Busca</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/financeiro/pagamentos" class="search-form" id="filterForm">
            <div class="filter-group">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Buscar por aluno, CPF ou referência..." 
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                >
            </div>
            <div class="filter-group">
                <select name="forma" id="formaFilter" class="form-control filter-select">
                    <option value="">Todas as formas</option>
                    <option value="PIX" <?= ($filters['forma'] ?? '') === 'PIX' ? 'selected' : '' ?>>PIX</option>
                    <option value="Cartão" <?= ($filters['forma'] ?? '') === 'Cartão' ? 'selected' : '' ?>>Cartão</option>
                    <option value="Dinheiro" <?= ($filters['forma'] ?? '') === 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                    <option value="Boleto" <?= ($filters['forma'] ?? '') === 'Boleto' ? 'selected' : '' ?>>Boleto</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="conciliado" id="conciliadoFilter" class="form-control filter-select">
                    <option value="">Todos</option>
                    <option value="1" <?= ($filters['conciliado'] ?? '') === '1' ? 'selected' : '' ?>>Conciliados</option>
                    <option value="0" <?= ($filters['conciliado'] ?? '') === '0' ? 'selected' : '' ?>>Não Conciliados</option>
                </select>
            </div>
            <div class="filter-group">
                <input 
                    type="date" 
                    name="dt_pagamento_inicio" 
                    id="dtPagamentoInicio"
                    value="<?= htmlspecialchars($filters['dt_pagamento_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                    placeholder="Data de"
                >
            </div>
            <div class="filter-group">
                <input 
                    type="date" 
                    name="dt_pagamento_fim" 
                    id="dtPagamentoFim"
                    value="<?= htmlspecialchars($filters['dt_pagamento_fim'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control filter-input"
                    placeholder="Data até"
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
            <?php if (!empty($filters['search']) || !empty($filters['forma']) || !empty($filters['conciliado']) || !empty($filters['dt_pagamento_inicio']) || !empty($filters['dt_pagamento_fim']) || !empty($filters['aluno_id'])): ?>
            <div class="filter-group">
                <a href="<?= BASE_URL ?>/financeiro/pagamentos" class="btn btn-secondary">
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
        <h3 class="card-title">Lista de Pagamentos</h3>
    </div>
    
    <div class="card-body">
        <?php if (empty($pagamentos)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <p style="font-size: 1.125rem; margin-bottom: 0.5rem;">Nenhum pagamento encontrado.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="pagamentosTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Aluno</th>
                            <th>Competência</th>
                            <th>Forma</th>
                            <th>Valor</th>
                            <th>Referência</th>
                            <th>Conciliado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagamentos as $pagamento): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($pagamento['dt_pagamento'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($pagamento['aluno_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if (!empty($pagamento['aluno_cpf'])): ?>
                                        <br><small style="color: var(--text-secondary);">
                                            CPF: <?= htmlspecialchars($pagamento['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    // Converte competência de YYYY-MM para MM/YYYY
                                    $competenciaFormatada = '';
                                    if (!empty($pagamento['competencia'])) {
                                        $parts = explode('-', $pagamento['competencia']);
                                        if (count($parts) === 2) {
                                            $competenciaFormatada = $parts[1] . '/' . $parts[0];
                                        } else {
                                            $competenciaFormatada = $pagamento['competencia'];
                                        }
                                    }
                                    ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($competenciaFormatada, ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= htmlspecialchars($pagamento['forma'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <strong style="color: var(--success-color);">
                                        R$ <?= number_format((float)$pagamento['valor_pago'], 2, ',', '.') ?>
                                    </strong>
                                </td>
                                <td>
                                    <?= !empty($pagamento['transacao_ref']) ? htmlspecialchars($pagamento['transacao_ref'], ENT_QUOTES, 'UTF-8') : '-' ?>
                                </td>
                                <td>
                                    <?php if ($pagamento['conciliado']): ?>
                                        <span class="badge badge-success">Sim</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Não</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>/financeiro/pagamento/<?= $pagamento['id'] ?>" class="btn btn-sm btn-secondary" title="Ver detalhes">
                                            Ver
                                        </a>
                                        <a href="<?= BASE_URL ?>/financeiro/<?= $pagamento['mensalidade_id'] ?>" class="btn btn-sm btn-primary" title="Ver mensalidade">
                                            Mensalidade
                                        </a>
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
                        <a href="<?= BASE_URL ?>/financeiro/pagamentos?page=<?= $currentPage - 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="btn btn-secondary">Anterior</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 0; $i < $totalPages; $i++): ?>
                        <?php if ($i === $currentPage): ?>
                            <span class="btn btn-primary" style="cursor: default;"><?= $i + 1 ?></span>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/financeiro/pagamentos?page=<?= $i ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="btn btn-secondary"><?= $i + 1 ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages - 1): ?>
                        <a href="<?= BASE_URL ?>/financeiro/pagamentos?page=<?= $currentPage + 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>" class="btn btn-secondary">Próxima</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>


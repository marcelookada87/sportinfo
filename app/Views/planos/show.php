<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes do Plano</h1>
        <p class="page-subtitle">Informações completas do plano</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/planos/<?= $plano['id'] ?>/edit" class="btn btn-primary">Editar</a>
        <a href="<?= BASE_URL ?>/planos" class="btn btn-secondary">Voltar</a>
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

<div class="details-grid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações do Plano</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Nome</dt>
                    <dd><strong><?= htmlspecialchars($plano['nome'], ENT_QUOTES, 'UTF-8') ?></strong></dd>
                </div>
                
                <div class="details-item">
                    <dt>Periodicidade</dt>
                    <dd>
                        <?php
                        $periodicidadeLabels = [
                            'mensal' => 'Mensal',
                            'trimestral' => 'Trimestral (3 meses)',
                            'anual' => 'Anual (12 meses)'
                        ];
                        $periodicidadeLabel = $periodicidadeLabels[$plano['periodicidade']] ?? ucfirst($plano['periodicidade']);
                        ?>
                        <span class="badge badge-info"><?= $periodicidadeLabel ?></span>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Valor Base</dt>
                    <dd>
                        <strong style="font-size: 1.2rem; color: var(--primary-color);">
                            R$ <?= number_format((float)$plano['valor_base'], 2, ',', '.') ?>
                        </strong>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Valor Mensal</dt>
                    <dd>
                        <strong style="font-size: 1.2rem; color: var(--success-color);">
                            R$ <?= number_format($valorMensal ?? 0, 2, ',', '.') ?>
                        </strong>
                        <small style="display: block; color: var(--text-secondary); margin-top: 0.25rem;">
                            Valor médio por mês
                        </small>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Status</dt>
                    <dd>
                        <?php if ($plano['ativo']): ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inativo</span>
                        <?php endif; ?>
                    </dd>
                </div>
                
                <?php if (!empty($plano['descricao'])): ?>
                <div class="details-item">
                    <dt>Descrição</dt>
                    <dd><?= nl2br(htmlspecialchars($plano['descricao'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Estatísticas</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Matrículas Ativas</dt>
                    <dd>
                        <span class="badge badge-success" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalMatriculasAtivas, 0, ',', '.') ?>
                        </span>
                        <small style="display: block; color: var(--text-secondary); margin-top: 0.25rem;">
                            Alunos atualmente usando este plano
                        </small>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Total de Matrículas</dt>
                    <dd>
                        <span class="badge badge-info" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalMatriculas, 0, ',', '.') ?>
                        </span>
                        <small style="display: block; color: var(--text-secondary); margin-top: 0.25rem;">
                            Total histórico (ativas + finalizadas + canceladas)
                        </small>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <?php if (!empty($matriculas)): ?>
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h3 class="card-title">Matrículas Ativas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Turma</th>
                            <th>Modalidade</th>
                            <th>Data Início</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matriculas as $matricula): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($matricula['aluno_nome'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($matricula['turma_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge badge-secondary"><?= htmlspecialchars($matricula['modalidade_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($matricula['dt_inicio'])) ?></td>
                                <td>
                                    <span class="badge badge-success"><?= htmlspecialchars($matricula['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações do Sistema</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Data de Cadastro</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($plano['dt_cadastro'])) ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>


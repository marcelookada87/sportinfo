<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes da Matrícula</h1>
        <p class="page-subtitle">Informações completas da matrícula</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/matriculas/<?= $matricula['id'] ?>/edit" class="btn btn-primary">Editar</a>
        <a href="<?= BASE_URL ?>/matriculas" class="btn btn-secondary">Voltar</a>
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
            <h3 class="card-title">Informações do Aluno</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Nome</dt>
                    <dd>
                        <strong><?= htmlspecialchars($matricula['aluno_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <a href="<?= BASE_URL ?>/alunos/<?= $matricula['aluno_id'] ?>" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">Ver Aluno</a>
                    </dd>
                </div>
                
                <?php if (!empty($matricula['aluno_cpf'])): ?>
                <div class="details-item">
                    <dt>CPF</dt>
                    <dd><?= htmlspecialchars($matricula['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($matricula['aluno_dt_nascimento'])): ?>
                <div class="details-item">
                    <dt>Data de Nascimento</dt>
                    <dd><?= date('d/m/Y', strtotime($matricula['aluno_dt_nascimento'])) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações da Turma</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Modalidade</dt>
                    <dd>
                        <span class="badge badge-secondary"><?= htmlspecialchars($matricula['modalidade_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                        <a href="<?= BASE_URL ?>/modalidades/<?= $matricula['modalidade_id'] ?>" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">Ver Modalidade</a>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Turma</dt>
                    <dd>
                        <strong><?= htmlspecialchars($matricula['turma_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Professor</dt>
                    <dd>
                        <?= htmlspecialchars($matricula['professor_nome'], ENT_QUOTES, 'UTF-8') ?>
                        <a href="<?= BASE_URL ?>/professores/<?= $matricula['professor_id'] ?>" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">Ver Professor</a>
                    </dd>
                </div>
                
                <?php if (!empty($matricula['turma_local'])): ?>
                <div class="details-item">
                    <dt>Local</dt>
                    <dd><?= htmlspecialchars($matricula['turma_local'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($matricula['turma_dias'])): ?>
                <div class="details-item">
                    <dt>Dias da Semana</dt>
                    <dd>
                        <?php
                        $dias = json_decode($matricula['turma_dias'], true);
                        if (is_array($dias)) {
                            echo implode(', ', $dias);
                        } else {
                            echo htmlspecialchars($matricula['turma_dias'], ENT_QUOTES, 'UTF-8');
                        }
                        ?>
                    </dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($matricula['turma_hora_inicio']) && !empty($matricula['turma_hora_fim'])): ?>
                <div class="details-item">
                    <dt>Horário</dt>
                    <dd>
                        <?= date('H:i', strtotime($matricula['turma_hora_inicio'])) ?> 
                        às 
                        <?= date('H:i', strtotime($matricula['turma_hora_fim'])) ?>
                    </dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações do Plano</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Plano</dt>
                    <dd>
                        <strong><?= htmlspecialchars($matricula['plano_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Periodicidade</dt>
                    <dd>
                        <?php
                        $periodicidadeLabels = [
                            'mensal' => 'Mensal',
                            'trimestral' => 'Trimestral',
                            'anual' => 'Anual'
                        ];
                        $periodicidadeLabel = $periodicidadeLabels[$matricula['plano_periodicidade']] ?? ucfirst($matricula['plano_periodicidade']);
                        ?>
                        <span class="badge badge-info"><?= $periodicidadeLabel ?></span>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Valor Base</dt>
                    <dd>
                        <strong style="font-size: 1.2rem; color: var(--primary-color);">
                            R$ <?= number_format((float)$matricula['plano_valor_base'], 2, ',', '.') ?>
                        </strong>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Status e Datas</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Status</dt>
                    <dd>
                        <?php
                        $statusColors = [
                            'Ativa' => 'badge-success',
                            'Suspensa' => 'badge-warning',
                            'Cancelada' => 'badge-error',
                            'Finalizada' => 'badge-secondary'
                        ];
                        $statusColor = $statusColors[$matricula['status']] ?? 'badge-secondary';
                        ?>
                        <span class="badge <?= $statusColor ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?= htmlspecialchars($matricula['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Data de Início</dt>
                    <dd><?= date('d/m/Y', strtotime($matricula['dt_inicio'])) ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>Data de Término</dt>
                    <dd>
                        <?= $matricula['dt_fim'] ? date('d/m/Y', strtotime($matricula['dt_fim'])) : '<span style="color: var(--text-secondary);">Não definida</span>' ?>
                    </dd>
                </div>
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
                    <dt>Total de Mensalidades</dt>
                    <dd>
                        <span class="badge badge-info" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalMensalidades, 0, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Mensalidades em Aberto</dt>
                    <dd>
                        <span class="badge badge-warning" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalMensalidadesAbertas, 0, ',', '.') ?>
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <?php if (!empty($mensalidades)): ?>
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h3 class="card-title">Mensalidades</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Competência</th>
                            <th>Valor</th>
                            <th>Desconto</th>
                            <th>Multa</th>
                            <th>Juros</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensalidades as $mensalidade): ?>
                            <tr>
                                <td><?= htmlspecialchars($mensalidade['competencia'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>R$ <?= number_format((float)$mensalidade['valor'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float)$mensalidade['desconto'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float)$mensalidade['multa'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float)$mensalidade['juros'], 2, ',', '.') ?></td>
                                <td><?= date('d/m/Y', strtotime($mensalidade['dt_vencimento'])) ?></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'Aberto' => 'badge-warning',
                                        'Pago' => 'badge-success',
                                        'Atrasado' => 'badge-error',
                                        'Cancelado' => 'badge-secondary'
                                    ];
                                    $statusColor = $statusColors[$mensalidade['status']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?= $statusColor ?>"><?= htmlspecialchars($mensalidade['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/financeiro/mensalidades/<?= $mensalidade['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($presencas)): ?>
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h3 class="card-title">Presenças Recentes</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($presencas as $presenca): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($presenca['data'])) ?></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'Presente' => 'badge-success',
                                        'Falta' => 'badge-error',
                                        'Reposição' => 'badge-info'
                                    ];
                                    $statusColor = $statusColors[$presenca['status']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?= $statusColor ?>"><?= htmlspecialchars($presenca['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td><?= htmlspecialchars($presenca['observacoes'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>


<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes da Modalidade</h1>
        <p class="page-subtitle">Informações completas da modalidade</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/modalidades/<?= $modalidade['id'] ?>/edit" class="btn btn-primary">Editar</a>
        <a href="<?= BASE_URL ?>/modalidades" class="btn btn-secondary">Voltar</a>
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
            <h3 class="card-title">Informações Gerais</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Nome</dt>
                    <dd><strong><?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?></strong></dd>
                </div>
                
                <?php if (!empty($modalidade['categoria_etaria'])): ?>
                <div class="details-item">
                    <dt>Categoria Etária</dt>
                    <dd>
                        <span class="badge badge-secondary"><?= htmlspecialchars($modalidade['categoria_etaria'], ENT_QUOTES, 'UTF-8') ?></span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <div class="details-item">
                    <dt>Status</dt>
                    <dd>
                        <?php if ($modalidade['ativo']): ?>
                            <span class="badge badge-success">Ativa</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inativa</span>
                        <?php endif; ?>
                    </dd>
                </div>
                
                <?php if (!empty($modalidade['descricao'])): ?>
                <div class="details-item">
                    <dt>Descrição</dt>
                    <dd><?= nl2br(htmlspecialchars($modalidade['descricao'], ENT_QUOTES, 'UTF-8')) ?></dd>
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
                    <dt>Total de Alunos</dt>
                    <dd>
                        <span class="badge badge-info" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalAlunos, 0, ',', '.') ?>
                        </span>
                        <small style="display: block; color: var(--text-secondary); margin-top: 0.25rem;">
                            Alunos matriculados em turmas desta modalidade
                        </small>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Total de Turmas</dt>
                    <dd>
                        <span class="badge badge-info" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalTurmas, 0, ',', '.') ?>
                        </span>
                        <small style="display: block; color: var(--text-secondary); margin-top: 0.25rem;">
                            Turmas ativas desta modalidade
                        </small>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <?php if (!empty($turmas)): ?>
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h3 class="card-title">Turmas Ativas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome da Turma</th>
                            <th>Professor</th>
                            <th>Nível</th>
                            <th>Dias da Semana</th>
                            <th>Horário</th>
                            <th>Local</th>
                            <th>Capacidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($turmas as $turma): ?>
                            <?php
                            $diasSemana = json_decode($turma['dias_da_semana'] ?? '[]', true);
                            $diasFormatados = is_array($diasSemana) ? implode(', ', $diasSemana) : '-';
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= !empty($turma['nivel']) ? htmlspecialchars($turma['nivel'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= $diasFormatados ?></td>
                                <td>
                                    <?= date('H:i', strtotime($turma['hora_inicio'])) ?> - 
                                    <?= date('H:i', strtotime($turma['hora_fim'])) ?>
                                </td>
                                <td><?= !empty($turma['local']) ? htmlspecialchars($turma['local'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= $turma['capacidade'] ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/turmas/<?= $turma['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
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
                    <dd><?= date('d/m/Y H:i', strtotime($modalidade['dt_cadastro'])) ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>


<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes do Aluno</h1>
        <p class="page-subtitle">Informações completas do cadastro</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/alunos/<?= $aluno['id'] ?>/edit" class="btn btn-primary">Editar</a>
        <a href="<?= BASE_URL ?>/alunos" class="btn btn-secondary">Voltar</a>
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
            <h3 class="card-title">Informações Pessoais</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Nome Completo</dt>
                    <dd><?= htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>CPF</dt>
                    <dd><?= !empty($aluno['cpf']) ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $aluno['cpf']) : '-' ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>RG</dt>
                    <dd><?= !empty($aluno['rg']) ? htmlspecialchars($aluno['rg'], ENT_QUOTES, 'UTF-8') : '-' ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>Data de Nascimento</dt>
                    <dd><?= date('d/m/Y', strtotime($aluno['dt_nascimento'])) ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>Idade</dt>
                    <dd><?= $idade ?> anos</dd>
                </div>
                
                <div class="details-item">
                    <dt>Sexo</dt>
                    <dd>
                        <?php
                        $sexoLabels = [
                            'M' => 'Masculino',
                            'F' => 'Feminino',
                            'Outro' => 'Outro'
                        ];
                        echo $sexoLabels[$aluno['sexo']] ?? $aluno['sexo'];
                        ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Tipo Sanguíneo</dt>
                    <dd><?= !empty($aluno['tipo_sanguineo']) ? htmlspecialchars($aluno['tipo_sanguineo'], ENT_QUOTES, 'UTF-8') : '-' ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>Status</dt>
                    <dd>
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
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Contato</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Telefone/WhatsApp</dt>
                    <dd><?= !empty($aluno['contato']) ? htmlspecialchars($aluno['contato'], ENT_QUOTES, 'UTF-8') : '-' ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>Contato de Emergência</dt>
                    <dd>
                        <?php if (!empty($aluno['contato_emergencia'])): ?>
                            <?= htmlspecialchars($aluno['contato_emergencia'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($aluno['nome_contato_emergencia'])): ?>
                                <br><small style="color: var(--text-secondary);">(<?= htmlspecialchars($aluno['nome_contato_emergencia'], ENT_QUOTES, 'UTF-8') ?>)</small>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>E-mail</dt>
                    <dd>
                        <?php if (!empty($aluno['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($aluno['email'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($aluno['email'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Endereço</dt>
                    <dd><?= !empty($aluno['endereco']) ? nl2br(htmlspecialchars($aluno['endereco'], ENT_QUOTES, 'UTF-8')) : '-' ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações dos Pais</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Nome do Pai</dt>
                    <dd><?= !empty($aluno['nome_pai']) ? htmlspecialchars($aluno['nome_pai'], ENT_QUOTES, 'UTF-8') : '-' ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>Telefone do Pai</dt>
                    <dd>
                        <?php if (!empty($aluno['telefone_pai'])): ?>
                            <a href="tel:<?= htmlspecialchars($aluno['telefone_pai'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($aluno['telefone_pai'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>E-mail do Pai</dt>
                    <dd>
                        <?php if (!empty($aluno['email_pai'])): ?>
                            <a href="mailto:<?= htmlspecialchars($aluno['email_pai'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($aluno['email_pai'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Telegram do Pai</dt>
                    <dd>
                        <?php if (!empty($aluno['telegram_pai'])): ?>
                            <a href="https://t.me/<?= ltrim(htmlspecialchars($aluno['telegram_pai'], ENT_QUOTES, 'UTF-8'), '@') ?>" target="_blank" rel="noopener noreferrer">
                                <?= htmlspecialchars($aluno['telegram_pai'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Nome da Mãe</dt>
                    <dd><?= !empty($aluno['nome_mae']) ? htmlspecialchars($aluno['nome_mae'], ENT_QUOTES, 'UTF-8') : '-' ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>Telefone da Mãe</dt>
                    <dd>
                        <?php if (!empty($aluno['telefone_mae'])): ?>
                            <a href="tel:<?= htmlspecialchars($aluno['telefone_mae'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($aluno['telefone_mae'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>E-mail da Mãe</dt>
                    <dd>
                        <?php if (!empty($aluno['email_mae'])): ?>
                            <a href="mailto:<?= htmlspecialchars($aluno['email_mae'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($aluno['email_mae'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Telegram da Mãe</dt>
                    <dd>
                        <?php if (!empty($aluno['telegram_mae'])): ?>
                            <a href="https://t.me/<?= ltrim(htmlspecialchars($aluno['telegram_mae'], ENT_QUOTES, 'UTF-8'), '@') ?>" target="_blank" rel="noopener noreferrer">
                                <?= htmlspecialchars($aluno['telegram_mae'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <?php if (!empty($aluno['responsavel_nome'])): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Responsável Legal</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Nome</dt>
                    <dd><?= htmlspecialchars($aluno['responsavel_nome'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                
                <?php if (!empty($aluno['responsavel_cpf'])): ?>
                <div class="details-item">
                    <dt>CPF</dt>
                    <dd><?= preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $aluno['responsavel_cpf']) ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($aluno['responsavel_contato'])): ?>
                <div class="details-item">
                    <dt>Contato</dt>
                    <dd><?= htmlspecialchars($aluno['responsavel_contato'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($aluno['responsavel_email'])): ?>
                <div class="details-item">
                    <dt>E-mail</dt>
                    <dd>
                        <a href="mailto:<?= htmlspecialchars($aluno['responsavel_email'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($aluno['responsavel_email'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modalidades</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($modalidades)): ?>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                    <?php foreach ($modalidades as $modalidade): ?>
                        <span class="badge badge-secondary" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                            <?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-secondary); margin: 0;">Nenhuma modalidade cadastrada para este aluno.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($aluno['alergias']) || !empty($aluno['observacoes_medicas'])): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações Médicas</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <?php if (!empty($aluno['alergias'])): ?>
                <div class="details-item">
                    <dt>Alergias</dt>
                    <dd><?= nl2br(htmlspecialchars($aluno['alergias'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($aluno['observacoes_medicas'])): ?>
                <div class="details-item">
                    <dt>Observações Médicas</dt>
                    <dd><?= nl2br(htmlspecialchars($aluno['observacoes_medicas'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
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
                    <dd><?= date('d/m/Y H:i', strtotime($aluno['dt_cadastro'])) ?></dd>
                </div>
                
                <?php if (!empty($aluno['dt_atualizacao'])): ?>
                <div class="details-item">
                    <dt>Última Atualização</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($aluno['dt_atualizacao'])) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</div>


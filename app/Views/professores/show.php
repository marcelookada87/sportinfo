<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes do Professor</h1>
        <p class="page-subtitle">Informações completas do cadastro</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/professores/<?= $professor['id'] ?>/edit" class="btn btn-primary">Editar</a>
        <a href="<?= BASE_URL ?>/professores" class="btn btn-secondary">Voltar</a>
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
                    <dd><?= htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                
                <div class="details-item">
                    <dt>CPF</dt>
                    <dd><?= !empty($professor['cpf']) ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $professor['cpf']) : '-' ?></dd>
                </div>
                
                <?php if (!empty($professor['rg'])): ?>
                <div class="details-item">
                    <dt>RG</dt>
                    <dd><?= htmlspecialchars($professor['rg'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['dt_nascimento'])): ?>
                <div class="details-item">
                    <dt>Data de Nascimento</dt>
                    <dd><?= date('d/m/Y', strtotime($professor['dt_nascimento'])) ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($idade)): ?>
                <div class="details-item">
                    <dt>Idade</dt>
                    <dd><?= $idade ?> anos</dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['sexo'])): ?>
                <div class="details-item">
                    <dt>Sexo</dt>
                    <dd>
                        <?php
                        $sexoLabels = [
                            'M' => 'Masculino',
                            'F' => 'Feminino',
                            'Outro' => 'Outro'
                        ];
                        echo $sexoLabels[$professor['sexo']] ?? $professor['sexo'];
                        ?>
                    </dd>
                </div>
                <?php endif; ?>
                
                <div class="details-item">
                    <dt>Status</dt>
                    <dd>
                        <?php
                        $statusClass = match($professor['status']) {
                            'Ativo' => 'success',
                            'Inativo' => 'secondary',
                            default => 'secondary'
                        };
                        ?>
                        <span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($professor['status'], ENT_QUOTES, 'UTF-8') ?></span>
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
                    <dd>
                        <?php if (!empty($professor['contato'])): ?>
                            <a href="tel:<?= htmlspecialchars($professor['contato'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($professor['contato'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>E-mail</dt>
                    <dd>
                        <?php if (!empty($professor['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($professor['email'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($professor['email'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                
                <?php if (!empty($professor['endereco'])): ?>
                <div class="details-item">
                    <dt>Endereço</dt>
                    <dd><?= nl2br(htmlspecialchars($professor['endereco'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Dados Profissionais</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <?php if (!empty($professor['registro_cref'])): ?>
                <div class="details-item">
                    <dt>Registro CREF</dt>
                    <dd><?= htmlspecialchars($professor['registro_cref'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['especialidade'])): ?>
                <div class="details-item">
                    <dt>Especialidade</dt>
                    <dd><?= htmlspecialchars($professor['especialidade'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['valor_hora'])): ?>
                <div class="details-item">
                    <dt>Valor por Hora</dt>
                    <dd>R$ <?= number_format((float)$professor['valor_hora'], 2, ',', '.') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['formacao_academica'])): ?>
                <div class="details-item">
                    <dt>Formação Acadêmica</dt>
                    <dd><?= nl2br(htmlspecialchars($professor['formacao_academica'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['certificacoes'])): ?>
                <div class="details-item">
                    <dt>Certificações</dt>
                    <dd><?= nl2br(htmlspecialchars($professor['certificacoes'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['experiencia_profissional'])): ?>
                <div class="details-item">
                    <dt>Experiência Profissional</dt>
                    <dd><?= nl2br(htmlspecialchars($professor['experiencia_profissional'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

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
                <p style="color: var(--text-secondary); margin: 0;">Nenhuma modalidade cadastrada para este professor.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($professor['banco_nome']) || !empty($professor['banco_agencia']) || !empty($professor['banco_conta']) || !empty($professor['banco_pix'])): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Dados Bancários</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <?php if (!empty($professor['banco_nome'])): ?>
                <div class="details-item">
                    <dt>Banco</dt>
                    <dd><?= htmlspecialchars($professor['banco_nome'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['banco_agencia'])): ?>
                <div class="details-item">
                    <dt>Agência</dt>
                    <dd><?= htmlspecialchars($professor['banco_agencia'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['banco_conta'])): ?>
                <div class="details-item">
                    <dt>Conta</dt>
                    <dd>
                        <?= htmlspecialchars($professor['banco_conta'], ENT_QUOTES, 'UTF-8') ?>
                        <?php if (!empty($professor['banco_tipo_conta'])): ?>
                            <span style="color: var(--text-secondary);">(<?= htmlspecialchars($professor['banco_tipo_conta'], ENT_QUOTES, 'UTF-8') ?>)</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['banco_pix'])): ?>
                <div class="details-item">
                    <dt>Chave PIX</dt>
                    <dd><?= htmlspecialchars($professor['banco_pix'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($professor['contato_emergencia']) || !empty($professor['nome_contato_emergencia'])): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Contato de Emergência</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <?php if (!empty($professor['nome_contato_emergencia'])): ?>
                <div class="details-item">
                    <dt>Nome</dt>
                    <dd><?= htmlspecialchars($professor['nome_contato_emergencia'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($professor['contato_emergencia'])): ?>
                <div class="details-item">
                    <dt>Telefone</dt>
                    <dd>
                        <a href="tel:<?= htmlspecialchars($professor['contato_emergencia'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($professor['contato_emergencia'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($professor['observacoes'])): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Observações</h3>
        </div>
        <div class="card-body">
            <p style="margin: 0; white-space: pre-wrap;"><?= htmlspecialchars($professor['observacoes'], ENT_QUOTES, 'UTF-8') ?></p>
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
                    <dd><?= date('d/m/Y H:i', strtotime($professor['dt_cadastro'])) ?></dd>
                </div>
                
                <?php if (!empty($professor['dt_atualizacao'])): ?>
                <div class="details-item">
                    <dt>Última Atualização</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($professor['dt_atualizacao'])) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</div>


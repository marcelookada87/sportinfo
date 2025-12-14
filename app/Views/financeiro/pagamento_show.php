<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes do Pagamento</h1>
        <p class="page-subtitle">Informações completas do pagamento</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/financeiro/<?= $pagamento['mensalidade_id'] ?>" class="btn btn-primary">Ver Mensalidade</a>
        <a href="<?= BASE_URL ?>/financeiro/pagamentos" class="btn btn-secondary">Voltar</a>
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
            <h3 class="card-title">Informações do Pagamento</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Aluno</dt>
                    <dd>
                        <strong><?= htmlspecialchars($pagamento['aluno_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                        <?php if (!empty($pagamento['aluno_cpf'])): ?>
                            <br><small style="color: var(--text-secondary);">
                                CPF: <?= htmlspecialchars($pagamento['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?>
                            </small>
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Competência</dt>
                    <dd>
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
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Valor Pago</dt>
                    <dd>
                        <strong style="font-size: 1.5rem; color: var(--success-color);">
                            R$ <?= number_format((float)$pagamento['valor_pago'], 2, ',', '.') ?>
                        </strong>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Forma de Pagamento</dt>
                    <dd>
                        <span class="badge badge-info"><?= htmlspecialchars($pagamento['forma'], ENT_QUOTES, 'UTF-8') ?></span>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Data do Pagamento</dt>
                    <dd>
                        <?= date('d/m/Y H:i:s', strtotime($pagamento['dt_pagamento'])) ?>
                    </dd>
                </div>
                
                <?php if (!empty($pagamento['transacao_ref'])): ?>
                <div class="details-item">
                    <dt>Referência da Transação</dt>
                    <dd><?= htmlspecialchars($pagamento['transacao_ref'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                
                <div class="details-item">
                    <dt>Status de Conciliação</dt>
                    <dd>
                        <?php if ($pagamento['conciliado']): ?>
                            <span class="badge badge-success">Conciliado</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Não Conciliado</span>
                        <?php endif; ?>
                    </dd>
                </div>
                
                <?php if (!empty($pagamento['observacoes'])): ?>
                <div class="details-item">
                    <dt>Observações</dt>
                    <dd><?= nl2br(htmlspecialchars($pagamento['observacoes'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações da Mensalidade</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Valor da Mensalidade</dt>
                    <dd>
                        <strong>R$ <?= number_format((float)$pagamento['mensalidade_valor'], 2, ',', '.') ?></strong>
                    </dd>
                </div>
                
                <?php if ((float)($pagamento['desconto'] ?? 0) > 0): ?>
                <div class="details-item">
                    <dt>Desconto</dt>
                    <dd>
                        <span style="color: var(--success-color);">
                            - R$ <?= number_format((float)$pagamento['desconto'], 2, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <?php 
                $multaJuros = (float)($pagamento['multa'] ?? 0) + (float)($pagamento['juros'] ?? 0);
                if ($multaJuros > 0): 
                ?>
                <div class="details-item">
                    <dt>Multa e Juros</dt>
                    <dd>
                        <span style="color: var(--error-color);">
                            + R$ <?= number_format($multaJuros, 2, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <div class="details-item">
                    <dt>Data de Vencimento</dt>
                    <dd>
                        <?= date('d/m/Y', strtotime($pagamento['dt_vencimento'])) ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Status da Mensalidade</dt>
                    <dd>
                        <?php
                        $status = $pagamento['mensalidade_status'] ?? 'Aberto';
                        $statusLabels = [
                            'Aberto' => ['label' => 'Aberto', 'class' => 'badge-warning'],
                            'Pago' => ['label' => 'Pago', 'class' => 'badge-success'],
                            'Atrasado' => ['label' => 'Atrasado', 'class' => 'badge-danger'],
                            'Cancelado' => ['label' => 'Cancelado', 'class' => 'badge-secondary']
                        ];
                        $statusInfo = $statusLabels[$status] ?? ['label' => $status, 'class' => 'badge-secondary'];
                        ?>
                        <span class="badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>


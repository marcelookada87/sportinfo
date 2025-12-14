<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes da Mensalidade</h1>
        <p class="page-subtitle">Informações completas da mensalidade</p>
    </div>
    <div class="page-header-actions">
        <?php if ($mensalidade['status'] !== 'Pago' && $mensalidade['status'] !== 'Cancelado'): ?>
            <a href="<?= BASE_URL ?>/financeiro/pagamento/<?= $mensalidade['id'] ?>/create" class="btn btn-success">Registrar Pagamento</a>
            <a href="<?= BASE_URL ?>/financeiro/<?= $mensalidade['id'] ?>/edit" class="btn btn-primary">Editar</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/financeiro" class="btn btn-secondary">Voltar</a>
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
            <h3 class="card-title">Informações da Mensalidade</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Aluno</dt>
                    <dd>
                        <strong><?= htmlspecialchars($mensalidade['aluno_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                        <?php if (!empty($mensalidade['aluno_cpf'])): ?>
                            <br><small style="color: var(--text-secondary);">
                                CPF: <?= htmlspecialchars($mensalidade['aluno_cpf'], ENT_QUOTES, 'UTF-8') ?>
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
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Valor Base</dt>
                    <dd>
                        <strong style="font-size: 1.2rem; color: var(--primary-color);">
                            R$ <?= number_format((float)$mensalidade['valor'], 2, ',', '.') ?>
                        </strong>
                    </dd>
                </div>
                
                <?php if ((float)($mensalidade['desconto'] ?? 0) > 0): ?>
                <div class="details-item">
                    <dt>Desconto</dt>
                    <dd>
                        <span style="color: var(--success-color); font-weight: 600;">
                            - R$ <?= number_format((float)$mensalidade['desconto'], 2, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <?php 
                $multa = (float)($mensalidade['multa'] ?? 0);
                $juros = (float)($mensalidade['juros'] ?? 0);
                $multaJuros = $multa + $juros;
                if ($multaJuros > 0): 
                ?>
                <div class="details-item" style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 0.5rem;">
                    <dt style="font-weight: 600; color: var(--error-color);">Encargos por Atraso</dt>
                    <dd></dd>
                </div>
                
                <?php if ($multa > 0): ?>
                <div class="details-item">
                    <dt>
                        Multa
                        <?php if ($isAtrasada && $diasAtraso > 0): ?>
                            <small style="color: var(--text-secondary); font-weight: normal; display: block; margin-top: 0.25rem;">
                                (aplicada após vencimento)
                            </small>
                        <?php endif; ?>
                    </dt>
                    <dd>
                        <span style="color: var(--error-color); font-weight: 600;">
                            + R$ <?= number_format($multa, 2, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <?php if ($juros > 0): ?>
                <div class="details-item">
                    <dt>
                        Juros
                        <?php if ($isAtrasada && $diasAtraso > 0): ?>
                            <small style="color: var(--text-secondary); font-weight: normal; display: block; margin-top: 0.25rem;">
                                (<?= $diasAtraso ?> dia<?= $diasAtraso > 1 ? 's' : '' ?> de atraso)
                            </small>
                        <?php endif; ?>
                    </dt>
                    <dd>
                        <span style="color: var(--error-color); font-weight: 600;">
                            + R$ <?= number_format($juros, 2, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <?php if ($multa > 0 || $juros > 0): ?>
                <div class="details-item" style="border-top: 1px solid var(--border-color); padding-top: 0.5rem; margin-top: 0.5rem;">
                    <dt>Total de Encargos</dt>
                    <dd>
                        <span style="color: var(--error-color); font-weight: 600;">
                            + R$ <?= number_format($multaJuros, 2, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <div class="details-item" style="border-top: 2px solid var(--primary-color); padding-top: 1rem; margin-top: 1rem;">
                    <dt style="font-size: 1.1rem; font-weight: 700;">Valor Total a Pagar</dt>
                    <dd>
                        <strong style="font-size: 1.8rem; color: var(--primary-color);">
                            R$ <?= number_format($valorTotal, 2, ',', '.') ?>
                        </strong>
                    </dd>
                </div>
                
                <?php if ($multaJuros > 0): ?>
                <div class="details-item" style="margin-top: 1rem; padding: 1rem; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 0.25rem;">
                    <dt style="font-weight: 600; margin-bottom: 0.5rem;">Composição do Valor Total:</dt>
                    <dd style="font-size: 0.9rem; line-height: 1.6;">
                        <div style="margin-bottom: 0.25rem;">
                            <strong>Valor Base:</strong> R$ <?= number_format((float)$mensalidade['valor'], 2, ',', '.') ?>
                        </div>
                        <?php if ((float)($mensalidade['desconto'] ?? 0) > 0): ?>
                        <div style="margin-bottom: 0.25rem; color: var(--success-color);">
                            <strong>Desconto:</strong> - R$ <?= number_format((float)$mensalidade['desconto'], 2, ',', '.') ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($multa > 0): ?>
                        <div style="margin-bottom: 0.25rem; color: var(--error-color);">
                            <strong>Multa:</strong> + R$ <?= number_format($multa, 2, ',', '.') ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($juros > 0): ?>
                        <div style="margin-bottom: 0.25rem; color: var(--error-color);">
                            <strong>Juros (<?= $diasAtraso ?? 0 ?> dia<?= ($diasAtraso ?? 0) > 1 ? 's' : '' ?>):</strong> + R$ <?= number_format($juros, 2, ',', '.') ?>
                        </div>
                        <?php endif; ?>
                        <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid #ddd; font-weight: 600;">
                            <strong>Total:</strong> R$ <?= number_format($valorTotal, 2, ',', '.') ?>
                        </div>
                    </dd>
                </div>
                <?php endif; ?>
                
                <div class="details-item">
                    <dt>Data de Vencimento</dt>
                    <dd>
                        <?php
                        $dtVencimento = new \DateTime($mensalidade['dt_vencimento']);
                        $hoje = new \DateTime();
                        
                        if ($isAtrasada && isset($diasAtraso) && $diasAtraso > 0) {
                            echo '<span style="color: var(--error-color); font-weight: 600;">';
                            echo $dtVencimento->format('d/m/Y');
                            echo ' <small>(' . $diasAtraso . ' dia' . ($diasAtraso > 1 ? 's' : '') . ' atrasado)</small>';
                            echo '</span>';
                        } else {
                            echo $dtVencimento->format('d/m/Y');
                        }
                        ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Status</dt>
                    <dd>
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
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Plano</dt>
                    <dd><?= htmlspecialchars($mensalidade['plano_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pagamentos</h3>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 1rem; padding: 1rem; background: var(--bg-secondary); border-radius: 0.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Valor Base:</span>
                    <strong>R$ <?= number_format((float)$mensalidade['valor'], 2, ',', '.') ?></strong>
                </div>
                
                <?php if ((float)($mensalidade['desconto'] ?? 0) > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Desconto:</span>
                    <strong style="color: var(--success-color);">- R$ <?= number_format((float)$mensalidade['desconto'], 2, ',', '.') ?></strong>
                </div>
                <?php endif; ?>
                
                <?php 
                $multa = (float)($mensalidade['multa'] ?? 0);
                $juros = (float)($mensalidade['juros'] ?? 0);
                if ($multa > 0 || $juros > 0): 
                ?>
                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                    <div style="font-weight: 600; color: var(--error-color); margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Encargos por Atraso:
                    </div>
                    
                    <?php if ($multa > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; padding-left: 0.5rem;">
                        <span style="font-size: 0.9rem;">Multa:</span>
                        <strong style="color: var(--error-color); font-size: 0.9rem;">+ R$ <?= number_format($multa, 2, ',', '.') ?></strong>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($juros > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; padding-left: 0.5rem;">
                        <span style="font-size: 0.9rem;">
                            Juros<?= isset($diasAtraso) && $diasAtraso > 0 ? ' (' . $diasAtraso . ' dia' . ($diasAtraso > 1 ? 's' : '') . ')' : '' ?>:
                        </span>
                        <strong style="color: var(--error-color); font-size: 0.9rem;">+ R$ <?= number_format($juros, 2, ',', '.') ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between; border-top: 2px solid var(--primary-color); padding-top: 0.75rem; margin-top: 0.75rem;">
                    <span style="font-weight: 600; font-size: 1.05rem;">Valor Total:</span>
                    <strong style="font-size: 1.1rem; color: var(--primary-color);">R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                    <span>Total Pago:</span>
                    <strong style="color: var(--success-color);">R$ <?= number_format($totalPago, 2, ',', '.') ?></strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.75rem;">
                    <span style="font-weight: 600;">Restante:</span>
                    <strong style="color: var(--<?= ($valorTotal - $totalPago) > 0 ? 'error' : 'success' ?>-color); font-size: 1.05rem;">
                        R$ <?= number_format(max(0, $valorTotal - $totalPago), 2, ',', '.') ?>
                    </strong>
                </div>
            </div>

            <?php if (empty($pagamentos)): ?>
                <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                    Nenhum pagamento registrado ainda.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
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
                                        <span class="badge badge-info"><?= htmlspecialchars($pagamento['forma'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td>
                                        <strong>R$ <?= number_format((float)$pagamento['valor_pago'], 2, ',', '.') ?></strong>
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
                                        <a href="<?= BASE_URL ?>/financeiro/pagamento/<?= $pagamento['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


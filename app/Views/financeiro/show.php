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
                $multaJuros = (float)($mensalidade['multa'] ?? 0) + (float)($mensalidade['juros'] ?? 0);
                if ($multaJuros > 0): 
                ?>
                <div class="details-item">
                    <dt>Multa e Juros</dt>
                    <dd>
                        <span style="color: var(--error-color); font-weight: 600;">
                            + R$ <?= number_format($multaJuros, 2, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <div class="details-item">
                    <dt>Valor Total</dt>
                    <dd>
                        <strong style="font-size: 1.5rem; color: var(--primary-color);">
                            R$ <?= number_format($valorTotal, 2, ',', '.') ?>
                        </strong>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Data de Vencimento</dt>
                    <dd>
                        <?php
                        $dtVencimento = new \DateTime($mensalidade['dt_vencimento']);
                        $hoje = new \DateTime();
                        
                        if ($isAtrasada) {
                            $diasAtraso = $hoje->diff($dtVencimento)->days;
                            echo '<span style="color: var(--error-color); font-weight: 600;">';
                            echo $dtVencimento->format('d/m/Y');
                            echo ' <small>(' . $diasAtraso . ' dias atrasado)</small>';
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
                    <span>Valor Total:</span>
                    <strong>R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Total Pago:</span>
                    <strong style="color: var(--success-color);">R$ <?= number_format($totalPago, 2, ',', '.') ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.5rem; margin-top: 0.5rem;">
                    <span>Restante:</span>
                    <strong style="color: var(--<?= ($valorTotal - $totalPago) > 0 ? 'error' : 'success' ?>-color);">
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


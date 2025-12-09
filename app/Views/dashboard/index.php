<div class="dashboard" style="padding: 0;">
    <div class="dashboard-welcome">
        <h2>Bem-vindo, <?= htmlspecialchars($usuario['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8') ?>!</h2>
        <p>Gerencie sua escola de esportes de forma eficiente</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-card-header">
                <span class="stat-card-title">Total de Alunos</span>
                <div class="stat-card-icon">👥</div>
            </div>
            <div class="stat-card-value"><?= number_format($stats['total_alunos'] ?? 0, 0, ',', '.') ?></div>
            <div class="stat-card-footer">Alunos cadastrados</div>
        </div>

        <div class="stat-card success">
            <div class="stat-card-header">
                <span class="stat-card-title">Total de Turmas</span>
                <div class="stat-card-icon">🏋️</div>
            </div>
            <div class="stat-card-value"><?= number_format($stats['total_turmas'] ?? 0, 0, ',', '.') ?></div>
            <div class="stat-card-footer">Turmas ativas</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-card-header">
                <span class="stat-card-title">Professores</span>
                <div class="stat-card-icon">👨‍🏫</div>
            </div>
            <div class="stat-card-value"><?= number_format($stats['total_professores'] ?? 0, 0, ',', '.') ?></div>
            <div class="stat-card-footer">Professores ativos</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-card-header">
                <span class="stat-card-title">Mensalidades Abertas</span>
                <div class="stat-card-icon">💰</div>
            </div>
            <div class="stat-card-value"><?= number_format($stats['mensalidades_abertas'] ?? 0, 0, ',', '.') ?></div>
            <div class="stat-card-footer">Aguardando pagamento</div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Ações Rápidas</h3>
            </div>
            <ul class="dashboard-list">
                <li class="dashboard-list-item">
                    <div class="dashboard-list-item-info">
                        <div class="dashboard-list-item-title">Cadastrar Novo Aluno</div>
                        <div class="dashboard-list-item-subtitle">Adicione um novo aluno ao sistema</div>
                    </div>
                    <a href="<?= BASE_URL ?>/alunos/create" class="dashboard-card-action">Acessar →</a>
                </li>
                <li class="dashboard-list-item">
                    <div class="dashboard-list-item-info">
                        <div class="dashboard-list-item-title">Criar Nova Turma</div>
                        <div class="dashboard-list-item-subtitle">Organize uma nova turma de aulas</div>
                    </div>
                    <a href="<?= BASE_URL ?>/turmas/create" class="dashboard-card-action">Acessar →</a>
                </li>
                <li class="dashboard-list-item">
                    <div class="dashboard-list-item-info">
                        <div class="dashboard-list-item-title">Registrar Pagamento</div>
                        <div class="dashboard-list-item-subtitle">Registre um novo pagamento</div>
                    </div>
                    <a href="<?= BASE_URL ?>/financeiro/pagamentos/create" class="dashboard-card-action">Acessar →</a>
                </li>
                <li class="dashboard-list-item">
                    <div class="dashboard-list-item-info">
                        <div class="dashboard-list-item-title">Gerar Relatório</div>
                        <div class="dashboard-list-item-subtitle">Visualize relatórios do sistema</div>
                    </div>
                    <a href="<?= BASE_URL ?>/relatorios" class="dashboard-card-action">Acessar →</a>
                </li>
            </ul>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Resumo Financeiro</h3>
                <a href="<?= BASE_URL ?>/financeiro" class="dashboard-card-action">Ver mais</a>
            </div>
            <ul class="dashboard-list">
                <li class="dashboard-list-item">
                    <div class="dashboard-list-item-info">
                        <div class="dashboard-list-item-title">Receita do Mês</div>
                        <div class="dashboard-list-item-subtitle">
                            <?php
                            $meses = [
                                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                            ];
                            echo $meses[(int)date('n')] . ' ' . date('Y');
                            ?>
                        </div>
                    </div>
                    <span class="dashboard-list-item-badge success">
                        R$ <?= number_format($stats['receita_mes'] ?? 0, 2, ',', '.') ?>
                    </span>
                </li>
                <li class="dashboard-list-item">
                    <div class="dashboard-list-item-info">
                        <div class="dashboard-list-item-title">Inadimplência</div>
                        <div class="dashboard-list-item-subtitle">Valor em atraso</div>
                    </div>
                    <span class="dashboard-list-item-badge danger">
                        R$ <?= number_format($stats['inadimplencia'] ?? 0, 2, ',', '.') ?>
                    </span>
                </li>
                <li class="dashboard-list-item">
                    <div class="dashboard-list-item-info">
                        <div class="dashboard-list-item-title">Mensalidades Abertas</div>
                        <div class="dashboard-list-item-subtitle">Aguardando pagamento</div>
                    </div>
                    <span class="dashboard-list-item-badge warning">
                        <?= number_format($stats['mensalidades_abertas'] ?? 0, 0, ',', '.') ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</div>


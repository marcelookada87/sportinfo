<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Cadastrar Novo Aluno</h1>
        <p class="page-subtitle">Preencha os dados do aluno</p>
    </div>
    <a href="<?= BASE_URL ?>/alunos" class="btn btn-secondary">Voltar</a>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>
    <div class="alert alert-error">
        <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dados do Aluno</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/alunos" data-validate>
            <?php
            // Gera token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            
                <div class="form-group">
                    <label for="nome" class="form-label">
                        Nome Completo <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        class="form-control" 
                        required
                        value="<?= htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Digite o nome completo"
                    >
                </div>

                <div class="form-group">
                    <label for="cpf" class="form-label">CPF</label>
                    <input 
                        type="text" 
                        id="cpf" 
                        name="cpf" 
                        class="form-control"
                        maxlength="14"
                        value="<?= htmlspecialchars($_POST['cpf'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="000.000.000-00"
                    >
                </div>

                <div class="form-group">
                    <label for="rg" class="form-label">RG</label>
                    <input 
                        type="text" 
                        id="rg" 
                        name="rg" 
                        class="form-control"
                        maxlength="20"
                        value="<?= htmlspecialchars($_POST['rg'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="00.000.000-0"
                    >
                </div>

                <div class="form-group">
                    <label for="dt_nascimento" class="form-label">
                        Data de Nascimento <span class="required">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="dt_nascimento" 
                        name="dt_nascimento" 
                        class="form-control" 
                        required
                        max="<?= date('Y-m-d') ?>"
                        value="<?= htmlspecialchars($_POST['dt_nascimento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="sexo" class="form-label">
                        Sexo <span class="required">*</span>
                    </label>
                    <select id="sexo" name="sexo" class="form-control" required>
                        <option value="">Selecione...</option>
                        <option value="M" <?= ($_POST['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= ($_POST['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= ($_POST['sexo'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                    <select id="tipo_sanguineo" name="tipo_sanguineo" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="A+" <?= ($_POST['tipo_sanguineo'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= ($_POST['tipo_sanguineo'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= ($_POST['tipo_sanguineo'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= ($_POST['tipo_sanguineo'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                        <option value="AB+" <?= ($_POST['tipo_sanguineo'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= ($_POST['tipo_sanguineo'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                        <option value="O+" <?= ($_POST['tipo_sanguineo'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= ($_POST['tipo_sanguineo'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contato" class="form-label">Telefone/WhatsApp</label>
                    <input 
                        type="text" 
                        id="contato" 
                        name="contato" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['contato'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="(00) 00000-0000"
                    >
                </div>

                <div class="form-group">
                    <label for="contato_emergencia" class="form-label">Contato de Emergência</label>
                    <input 
                        type="text" 
                        id="contato_emergencia" 
                        name="contato_emergencia" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['contato_emergencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="(00) 00000-0000"
                    >
                </div>

                <div class="form-group">
                    <label for="nome_contato_emergencia" class="form-label">Nome do Contato de Emergência</label>
                    <input 
                        type="text" 
                        id="nome_contato_emergencia" 
                        name="nome_contato_emergencia" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['nome_contato_emergencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Nome completo"
                    >
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="email@exemplo.com"
                    >
                </div>

                <div class="form-group">
                    <label for="nome_pai" class="form-label">Nome do Pai</label>
                    <input 
                        type="text" 
                        id="nome_pai" 
                        name="nome_pai" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['nome_pai'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Nome completo do pai"
                    >
                </div>

                <div class="form-group">
                    <label for="telefone_pai" class="form-label">Telefone do Pai</label>
                    <input 
                        type="text" 
                        id="telefone_pai" 
                        name="telefone_pai" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['telefone_pai'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="(00) 00000-0000"
                    >
                </div>

                <div class="form-group">
                    <label for="email_pai" class="form-label">E-mail do Pai</label>
                    <input 
                        type="email" 
                        id="email_pai" 
                        name="email_pai" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['email_pai'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="email@exemplo.com"
                    >
                </div>

                <div class="form-group">
                    <label for="telegram_pai" class="form-label">Telegram do Pai</label>
                    <input 
                        type="text" 
                        id="telegram_pai" 
                        name="telegram_pai" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['telegram_pai'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="@usuario_telegram"
                    >
                </div>

                <div class="form-group">
                    <label for="nome_mae" class="form-label">Nome da Mãe</label>
                    <input 
                        type="text" 
                        id="nome_mae" 
                        name="nome_mae" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['nome_mae'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Nome completo da mãe"
                    >
                </div>

                <div class="form-group">
                    <label for="telefone_mae" class="form-label">Telefone da Mãe</label>
                    <input 
                        type="text" 
                        id="telefone_mae" 
                        name="telefone_mae" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['telefone_mae'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="(00) 00000-0000"
                    >
                </div>

                <div class="form-group">
                    <label for="email_mae" class="form-label">E-mail da Mãe</label>
                    <input 
                        type="email" 
                        id="email_mae" 
                        name="email_mae" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['email_mae'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="email@exemplo.com"
                    >
                </div>

                <div class="form-group">
                    <label for="telegram_mae" class="form-label">Telegram da Mãe</label>
                    <input 
                        type="text" 
                        id="telegram_mae" 
                        name="telegram_mae" 
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['telegram_mae'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="@usuario_telegram"
                    >
                </div>

                <div class="form-group form-group-full">
                    <label for="endereco" class="form-label">Endereço</label>
                    <textarea 
                        id="endereco" 
                        name="endereco" 
                        class="form-control"
                        rows="3"
                        placeholder="Rua, número, bairro, cidade..."
                    ><?= htmlspecialchars($_POST['endereco'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group form-group-full">
                    <label for="alergias" class="form-label">Alergias</label>
                    <textarea 
                        id="alergias" 
                        name="alergias" 
                        class="form-control"
                        rows="2"
                        placeholder="Liste as alergias conhecidas (ex: penicilina, amendoim, etc.)"
                    ><?= htmlspecialchars($_POST['alergias'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group form-group-full">
                    <label for="observacoes_medicas" class="form-label">Observações Médicas</label>
                    <textarea 
                        id="observacoes_medicas" 
                        name="observacoes_medicas" 
                        class="form-control"
                        rows="3"
                        placeholder="Informações médicas importantes, medicações, condições especiais, etc."
                    ><?= htmlspecialchars($_POST['observacoes_medicas'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Ativo" <?= ($_POST['status'] ?? 'Ativo') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                        <option value="Inativo" <?= ($_POST['status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                        <option value="Suspenso" <?= ($_POST['status'] ?? '') === 'Suspenso' ? 'selected' : '' ?>>Suspenso</option>
                        <option value="Cancelado" <?= ($_POST['status'] ?? '') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Modalidades <span class="required">*</span></label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 0.5rem;">
                        <?php if (!empty($modalidades)): ?>
                            <?php foreach ($modalidades as $modalidade): ?>
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: all 0.2s;">
                                    <input 
                                        type="checkbox" 
                                        name="modalidades[]" 
                                        value="<?= $modalidade['id'] ?>"
                                        style="width: 18px; height: 18px; cursor: pointer;"
                                        <?= (isset($_POST['modalidades']) && in_array($modalidade['id'], $_POST['modalidades'])) ? 'checked' : '' ?>
                                    >
                                    <span><?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: var(--text-secondary);">Nenhuma modalidade cadastrada. <a href="#">Cadastrar modalidade</a></p>
                        <?php endif; ?>
                    </div>
                </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cadastrar Aluno</button>
                <a href="<?= BASE_URL ?>/alunos" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>



<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Editar Professor</h1>
        <p class="page-subtitle">Atualize os dados do professor</p>
    </div>
    <a href="<?= BASE_URL ?>/professores/<?= $professor['id'] ?>" class="btn btn-secondary">Voltar</a>
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
        <h3 class="card-title">Dados Pessoais</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/professores/<?= $professor['id'] ?>" data-validate>
            <?php
            // Gera token CSRF
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="_method" value="PUT">
            
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
                    value="<?= htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Digite o nome completo"
                >
            </div>

            <div class="form-group">
                <label for="cpf" class="form-label">
                    CPF <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="cpf" 
                    name="cpf" 
                    class="form-control"
                    maxlength="14"
                    required
                    value="<?= !empty($professor['cpf']) ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $professor['cpf']) : '' ?>"
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
                    value="<?= htmlspecialchars($professor['rg'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="00.000.000-0"
                >
            </div>

            <div class="form-group">
                <label for="dt_nascimento" class="form-label">Data de Nascimento</label>
                <input 
                    type="date" 
                    id="dt_nascimento" 
                    name="dt_nascimento" 
                    class="form-control" 
                    max="<?= date('Y-m-d') ?>"
                    value="<?= htmlspecialchars($professor['dt_nascimento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="form-group">
                <label for="sexo" class="form-label">Sexo</label>
                <select id="sexo" name="sexo" class="form-control">
                    <option value="">Selecione...</option>
                    <option value="M" <?= ($professor['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= ($professor['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>Feminino</option>
                    <option value="Outro" <?= ($professor['sexo'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="contato" class="form-label">
                    Telefone/WhatsApp <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="contato" 
                    name="contato" 
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($professor['contato'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="(00) 00000-0000"
                >
            </div>

            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="email@exemplo.com"
                >
            </div>

            <div class="form-group form-group-full">
                <label for="endereco" class="form-label">Endereço</label>
                <textarea 
                    id="endereco" 
                    name="endereco" 
                    class="form-control"
                    rows="3"
                    placeholder="Rua, número, bairro, cidade, CEP..."
                ><?= htmlspecialchars($professor['endereco'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dados Profissionais</h3>
    </div>
    <div class="card-body">
            <div class="form-group">
                <label for="registro_cref" class="form-label">Registro CREF</label>
                <input 
                    type="text" 
                    id="registro_cref" 
                    name="registro_cref" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['registro_cref'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Número do registro CREF"
                >
            </div>

            <div class="form-group form-group-full">
                <label for="formacao_academica" class="form-label">Formação Acadêmica</label>
                <textarea 
                    id="formacao_academica" 
                    name="formacao_academica" 
                    class="form-control"
                    rows="4"
                    placeholder="Ex: Graduação em Educação Física - Universidade XYZ (2010-2014)"
                ><?= htmlspecialchars($professor['formacao_academica'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group form-group-full">
                <label for="certificacoes" class="form-label">Certificações e Qualificações</label>
                <textarea 
                    id="certificacoes" 
                    name="certificacoes" 
                    class="form-control"
                    rows="4"
                    placeholder="Ex: Certificação em Natação Infantil - Confederação Brasileira (2018)"
                ><?= htmlspecialchars($professor['certificacoes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group form-group-full">
                <label for="experiencia_profissional" class="form-label">Experiência Profissional</label>
                <textarea 
                    id="experiencia_profissional" 
                    name="experiencia_profissional" 
                    class="form-control"
                    rows="4"
                    placeholder="Ex: Professor de Natação - Academia XYZ (2015-2020)"
                ><?= htmlspecialchars($professor['experiencia_profissional'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label for="especialidade" class="form-label">Especialidade</label>
                <input 
                    type="text" 
                    id="especialidade" 
                    name="especialidade" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['especialidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Natação, Futebol, Lutas, etc."
                >
            </div>

            <div class="form-group">
                <label for="valor_hora" class="form-label">Valor por Hora (R$)</label>
                <input 
                    type="number" 
                    id="valor_hora" 
                    name="valor_hora" 
                    class="form-control"
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars($professor['valor_hora'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0.00"
                >
            </div>

            <div class="form-group form-group-full">
                <label class="form-label">Modalidades</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 0.5rem;">
                    <?php if (!empty($modalidades)): ?>
                        <?php foreach ($modalidades as $modalidade): ?>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: all 0.2s;">
                                <input 
                                    type="checkbox" 
                                    name="modalidades[]" 
                                    value="<?= $modalidade['id'] ?>"
                                    style="width: 18px; height: 18px; cursor: pointer;"
                                    <?= in_array($modalidade['id'], $modalidadesProfessor ?? []) ? 'checked' : '' ?>
                                >
                                <span><?= htmlspecialchars($modalidade['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: var(--text-secondary);">Nenhuma modalidade cadastrada.</p>
                    <?php endif; ?>
                </div>
            </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dados Bancários</h3>
    </div>
    <div class="card-body">
            <div class="form-group">
                <label for="banco_nome" class="form-label">Nome do Banco</label>
                <input 
                    type="text" 
                    id="banco_nome" 
                    name="banco_nome" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['banco_nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Ex: Banco do Brasil, Itaú, etc."
                >
            </div>

            <div class="form-group">
                <label for="banco_agencia" class="form-label">Agência</label>
                <input 
                    type="text" 
                    id="banco_agencia" 
                    name="banco_agencia" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['banco_agencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="0000"
                >
            </div>

            <div class="form-group">
                <label for="banco_conta" class="form-label">Conta</label>
                <input 
                    type="text" 
                    id="banco_conta" 
                    name="banco_conta" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['banco_conta'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="00000-0"
                >
            </div>

            <div class="form-group">
                <label for="banco_tipo_conta" class="form-label">Tipo de Conta</label>
                <select id="banco_tipo_conta" name="banco_tipo_conta" class="form-control">
                    <option value="">Selecione...</option>
                    <option value="Corrente" <?= ($professor['banco_tipo_conta'] ?? '') === 'Corrente' ? 'selected' : '' ?>>Corrente</option>
                    <option value="Poupança" <?= ($professor['banco_tipo_conta'] ?? '') === 'Poupança' ? 'selected' : '' ?>>Poupança</option>
                </select>
            </div>

            <div class="form-group">
                <label for="banco_pix" class="form-label">Chave PIX</label>
                <input 
                    type="text" 
                    id="banco_pix" 
                    name="banco_pix" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['banco_pix'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="CPF, e-mail, telefone ou chave aleatória"
                >
            </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Contato de Emergência</h3>
    </div>
    <div class="card-body">
            <div class="form-group">
                <label for="nome_contato_emergencia" class="form-label">Nome do Contato</label>
                <input 
                    type="text" 
                    id="nome_contato_emergencia" 
                    name="nome_contato_emergencia" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['nome_contato_emergencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Nome completo"
                >
            </div>

            <div class="form-group">
                <label for="contato_emergencia" class="form-label">Telefone de Emergência</label>
                <input 
                    type="text" 
                    id="contato_emergencia" 
                    name="contato_emergencia" 
                    class="form-control"
                    value="<?= htmlspecialchars($professor['contato_emergencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="(00) 00000-0000"
                >
            </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Outras Informações</h3>
    </div>
    <div class="card-body">
            <div class="form-group form-group-full">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea 
                    id="observacoes" 
                    name="observacoes" 
                    class="form-control"
                    rows="3"
                    placeholder="Observações gerais sobre o professor..."
                ><?= htmlspecialchars($professor['observacoes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="Ativo" <?= ($professor['status'] ?? 'Ativo') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Inativo" <?= ($professor['status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/professores/<?= $professor['id'] ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>


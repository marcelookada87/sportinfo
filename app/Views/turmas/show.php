<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Detalhes da Turma</h1>
        <p class="page-subtitle">Informações completas da turma</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= BASE_URL ?>/turmas/<?= $turma['id'] ?>/edit" class="btn btn-primary">Editar</a>
        <a href="<?= BASE_URL ?>/turmas" class="btn btn-secondary">Voltar</a>
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
            <h3 class="card-title">Informações da Turma</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Nome</dt>
                    <dd><strong><?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></strong></dd>
                </div>
                
                <div class="details-item">
                    <dt>Modalidade</dt>
                    <dd>
                        <span class="badge badge-secondary"><?= htmlspecialchars($turma['modalidade_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                        <a href="<?= BASE_URL ?>/modalidades/<?= $turma['modalidade_id'] ?>" class="btn btn-sm btn-primary" style="margin-left: 0.5rem; color: white;">Ver Modalidade</a>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Professor</dt>
                    <dd>
                        <?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?>
                        <a href="<?= BASE_URL ?>/professores/<?= $turma['professor_id'] ?>" class="btn btn-sm btn-primary" style="margin-left: 0.5rem; color: white;">Ver Professor</a>
                    </dd>
                </div>
                
                <?php if (!empty($turma['nivel'])): ?>
                <div class="details-item">
                    <dt>Nível</dt>
                    <dd>
                        <span class="badge badge-info"><?= htmlspecialchars($turma['nivel'], ENT_QUOTES, 'UTF-8') ?></span>
                    </dd>
                </div>
                <?php endif; ?>
                
                <div class="details-item">
                    <dt>Status</dt>
                    <dd>
                        <?php if ($turma['ativo']): ?>
                            <span class="badge badge-success">Ativa</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inativa</span>
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Horários e Local</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Dias da Semana</dt>
                    <dd>
                        <?php
                        if (!empty($turma['dias_array']) && is_array($turma['dias_array'])) {
                            echo implode(', ', $turma['dias_array']);
                        } else {
                            echo '<span style="color: var(--text-secondary);">Não definido</span>';
                        }
                        ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Horário</dt>
                    <dd>
                        <strong>
                            <?= date('H:i', strtotime($turma['hora_inicio'])) ?> 
                            às 
                            <?= date('H:i', strtotime($turma['hora_fim'])) ?>
                        </strong>
                    </dd>
                </div>
                
                <?php if (!empty($turma['local'])): ?>
                <div class="details-item">
                    <dt>Local</dt>
                    <dd><?= htmlspecialchars($turma['local'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Capacidade e Vagas</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Capacidade Total</dt>
                    <dd>
                        <strong style="font-size: 1.2rem; color: var(--primary-color);">
                            <?= number_format($turma['capacidade'], 0, ',', '.') ?> alunos
                        </strong>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Matrículas Ativas</dt>
                    <dd>
                        <span class="badge badge-success" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalMatriculasAtivas, 0, ',', '.') ?>
                        </span>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Vagas Disponíveis</dt>
                    <dd>
                        <?php if ($isCheia): ?>
                            <span class="badge badge-error" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                                Turma Cheia
                            </span>
                        <?php else: ?>
                            <span class="badge badge-success" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                                <?= number_format($vagasDisponiveis, 0, ',', '.') ?> vagas
                            </span>
                        <?php endif; ?>
                    </dd>
                </div>
                
                <div class="details-item">
                    <dt>Total de Matrículas</dt>
                    <dd>
                        <span class="badge badge-info" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                            <?= number_format($totalMatriculas, 0, ',', '.') ?>
                        </span>
                        <small style="display: block; color: var(--text-secondary); margin-top: 0.25rem;">
                            Total histórico (ativas + finalizadas + canceladas)
                        </small>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Alunos Matriculados</h3>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto; padding: 1rem;">
            <?php if (!empty($matriculas)): ?>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($matriculas as $matricula): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;" 
                            onmouseover="this.style.backgroundColor='var(--bg-secondary)'" 
                            onmouseout="this.style.backgroundColor='transparent'">
                            <span style="font-weight: 500; color: var(--text-primary);">
                                <?= htmlspecialchars($matricula['aluno_nome'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <button 
                                type="button" 
                                class="btn btn-sm btn-primary" 
                                onclick="abrirModalHorarios(<?= $matricula['aluno_id'] ?>, '<?= htmlspecialchars($matricula['aluno_nome'], ENT_QUOTES, 'UTF-8') ?>')"
                                style="color: white;"
                            >
                                Ver Horários
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    <p>Nenhum aluno matriculado nesta turma.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações do Sistema</h3>
        </div>
        <div class="card-body">
            <dl class="details-list">
                <div class="details-item">
                    <dt>Data de Cadastro</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($turma['dt_cadastro'])) ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>

<!-- Modal de Horários do Aluno -->
<div id="modalHorarios" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 class="modal-title">Horários do Aluno</h3>
            <button type="button" class="modal-close" onclick="fecharModalHorarios()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="loadingHorarios" style="text-align: center; padding: 2rem;">
                <p>Carregando horários...</p>
            </div>
            <div id="conteudoHorarios" style="display: none;">
                <div id="nomeAluno" style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary-color);"></div>
                <div id="listaHorarios"></div>
            </div>
            <div id="semHorarios" style="display: none; text-align: center; padding: 2rem; color: var(--text-secondary);">
                <p>Nenhum horário encontrado.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="fecharModalHorarios()">Fechar</button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    margin: auto;
    border-radius: 0.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary);
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
}

.modal-close:hover {
    background-color: var(--bg-secondary);
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.horario-item {
    padding: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    background-color: var(--bg-primary);
}

.horario-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.75rem;
}

.horario-turma {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--primary-color);
}

.horario-modalidade {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background-color: var(--bg-secondary);
    border-radius: 1rem;
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

.horario-detalhes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    margin-top: 0.75rem;
}

.horario-detalhe-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.horario-detalhe-label {
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.horario-detalhe-valor {
    font-size: 0.95rem;
    color: var(--text-primary);
}
</style>

<script>
function abrirModalHorarios(alunoId, alunoNome) {
    const modal = document.getElementById('modalHorarios');
    const loading = document.getElementById('loadingHorarios');
    const conteudo = document.getElementById('conteudoHorarios');
    const semHorarios = document.getElementById('semHorarios');
    const nomeAluno = document.getElementById('nomeAluno');
    const listaHorarios = document.getElementById('listaHorarios');
    
    // Mostra modal e loading
    modal.style.display = 'flex';
    loading.style.display = 'block';
    conteudo.style.display = 'none';
    semHorarios.style.display = 'none';
    nomeAluno.textContent = alunoNome;
    listaHorarios.innerHTML = '';
    
    // Busca horários via AJAX
    fetch(`<?= BASE_URL ?>/turmas/aluno/${alunoId}/horarios`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            loading.style.display = 'none';
            
            if (data.error) {
                semHorarios.style.display = 'block';
                document.querySelector('#semHorarios p').textContent = data.error;
                return;
            }
            
            if (data.horarios && data.horarios.length > 0) {
                let html = '';
                
                data.horarios.forEach(horario => {
                    const dias = horario.dias_array && horario.dias_array.length > 0 
                        ? horario.dias_array.join(', ') 
                        : 'Não definido';
                    
                    html += `
                        <div class="horario-item">
                            <div class="horario-item-header">
                                <div>
                                    <div class="horario-turma">${horario.turma_nome}</div>
                                    <span class="horario-modalidade">${horario.modalidade_nome}</span>
                                </div>
                            </div>
                            <div class="horario-detalhes">
                                <div class="horario-detalhe-item">
                                    <span class="horario-detalhe-label">Dias da Semana</span>
                                    <span class="horario-detalhe-valor">${dias}</span>
                                </div>
                                <div class="horario-detalhe-item">
                                    <span class="horario-detalhe-label">Horário</span>
                                    <span class="horario-detalhe-valor">${horario.hora_inicio.substring(0, 5)} - ${horario.hora_fim.substring(0, 5)}</span>
                                </div>
                                ${horario.turma_local ? `
                                <div class="horario-detalhe-item">
                                    <span class="horario-detalhe-label">Local</span>
                                    <span class="horario-detalhe-valor">${horario.turma_local}</span>
                                </div>
                                ` : ''}
                                <div class="horario-detalhe-item">
                                    <span class="horario-detalhe-label">Professor</span>
                                    <span class="horario-detalhe-valor">${horario.professor_nome}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                listaHorarios.innerHTML = html;
                conteudo.style.display = 'block';
            } else {
                semHorarios.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar horários:', error);
            loading.style.display = 'none';
            semHorarios.style.display = 'block';
            document.querySelector('#semHorarios p').textContent = 'Erro ao carregar horários.';
        });
}

function fecharModalHorarios() {
    document.getElementById('modalHorarios').style.display = 'none';
}

// Fecha modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('modalHorarios');
    if (event.target == modal) {
        fecharModalHorarios();
    }
}
</script>

